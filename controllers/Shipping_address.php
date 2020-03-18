<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_Address extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(['general_model', 'ledger_model']);
        $this->modules = $this->get_modules();
    }

    public function index() {
        $customer_module_id = $this->config->item('customer_module');
        $data['customer_module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);
        $data['country'] = $this->country_call();
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'action',
                1 => 'customer_name',
                2 => 'contact_person',
                3 => 'department',
                4 => 'email',
                5 => 'contact_number',
                6 => 'shipping_code',
                7 => 'address'
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->shipping_address_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;


            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $shipping_address_id = $this->encryption_url->encode($post->shipping_address_id);
                    /*if ($post->shipping_party_type == 'supplier') {
                        $supplier = $this->supplier_data($post->shipping_party_id);

                        $party_name = ($supplier[0]->supplier_name) ? $supplier[0]->supplier_name : '';
                    } else if ($post->shipping_party_type == 'customer') {
                        $customer = $this->customer_data($post->shipping_party_id);
                        if (!empty($customer)) {
                            $party_name = $customer[0]->customer_name;
                        } else {
                            $customer = '';
                        }
                    } else {
                        $party_name = '';
                    }*/

                    $nestedData['customer_name'] = $post->party_name;
                    $nestedData['contact_person'] = $post->contact_person ? $post->contact_person : '-';
                    $nestedData['department'] = $post->department;
                    $nestedData['email'] = $post->email;
                    $nestedData['contact_number'] = $post->contact_number;
                    $nestedData['shipping_code'] = $post->shipping_code;
                    $nestedData['address'] = $post->shipping_address;

                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';

                    if (in_array($data['customer_module_id'], $data['active_edit'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_shipping"><a data-id="' . $shipping_address_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_shipping btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['customer_module_id'], $data['active_delete'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a data-id="' . $shipping_address_id . '" data-path="shipping_address/delete" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete_button btn btn-app"><i class="fa fa-trash"></i></a></span>';
                        ;
                    }

                    // $nestedData['action'] = $cols;
                    $cols .= '</div></div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';

                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('shipping_address/list', $data);
        }
    }

    public function add() {
        $data = $this->get_default_country_state();
        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $this->load->view('shipping_address/add', $data);
    }

    public function edit($id) {

        $id = $this->encryption_url->decode($id);

        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'con.*';
        $table = 'contact_person con';
        $where = array(
            'con.contact_person_id' => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        $string = 'c.*';
        $table = 'countries c';
        $where = array(
            'c.delete_status' => 0);
        $data['country'] = $this->general_model->getRecords($string, $table, $where);
        $string = 'st.*';
        $table = 'states st';
        $where = array(
            'st.country_id' => $data['data'][0]->contact_person_country_id);
        $data['state'] = $this->general_model->getRecords($string, $table, $where);
        $string = 'ct.*';
        $table = 'cities ct';
        $where = array(
            'ct.state_id' => $data['data'][0]->contact_person_state_id);
        $data['city'] = $this->general_model->getRecords($string, $table, $where);

        $data['customer'] = $this->customer_call();
        $data['supplier'] = $this->supplier_call();

        $this->load->view('shipping_address/edit', $data);
    }

    public function edit_shipping_address() {

        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $shipping_address_id = $this->input->post('shipping_address_id_edit');
        $shipping_address_data = array(
                "shipping_address" => $this->input->post('party_address_edit'),
                "primary_address" => $this->input->post('primary_address_edit'),
                "shipping_party_id" => $this->input->post('company_name_edit'),
                "shipping_party_type" => $this->input->post('company_type_edit'),
                "contact_person" => $this->input->post('contact_person_name_edit'),
                "department" => $this->input->post('department_edit'),
                "email" => $this->input->post('txt_email_edit'),
                "shipping_gstin" => $this->input->post('gst_number_edit'),
                "contact_number" => $this->input->post('contact_number_edit'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => date('Y-m-d'),
                "country_id" => $this->input->post('cmb_country_edit1'),
                "state_id" => $this->input->post('cmb_state_edit'),
                "city_id" => $this->input->post('cmb_city_edit'),
                "shipping_code" => $this->input->post('txt_shipping_code_edit'),
                "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                "address_pin_code" => $this->input->post('edit_pin_code')
        );
        $table = "shipping_address";
        if ($this->general_model->updateData($table, $shipping_address_data, array('shipping_address_id' => $shipping_address_id))) {

            /*$ecommerce = 1;
            if($ecommerce){
                $shipping_address_data['customer_id'] = $shipping_address_data['shipping_party_id'];
                $this->customerhook->UpdateCustomerAddress($shipping_address_data);
            }*/

            $successMsg = 'Shipping Address Updated Successfully';
            $this->session->set_flashdata('shipping_address_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $shipping_address_id,
                'table_name' => 'shipping_address',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Shipping Address Updated');
            $this->general_model->insertData($table, $log_data);
        }else{
            $errorMsg = 'Shipping Address Update Unsuccessful';
            $this->session->set_flashdata('shipping_address_error',$errorMsg);
            echo json_encode($shipping_address_id);
        }
        echo json_encode($shipping_address_id);
    }

    public function add_shipping_address() {
        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $shipping_address_data = array(
            "shipping_address" => $this->input->post('party_address'),
            "primary_address" => 'no',
            "shipping_party_id" => $this->input->post('company_name'),
            "shipping_party_type" => $this->input->post('company_type'),
            "contact_person" => $this->input->post('contact_person_name'),
            "department" => $this->input->post('department'),
            "email" => $this->input->post('txt_email'),
            "shipping_gstin" => $this->input->post('gst_number'),
            "contact_number" => $this->input->post('contact_number'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "country_id" => $this->input->post('cmb_country'),
            "state_id" => $this->input->post('cmb_state'),
            "city_id" => $this->input->post('cmb_city'),
            "shipping_code" => $this->input->post('txt_shipping_code'),
            "address_pin_code" => $this->input->post('pin_code'),
            "updated_date" => "",
            "updated_user_id" => ""
        );

        $table = "shipping_address";
        if ($id = $this->general_model->insertData($table, $shipping_address_data)) {

            /*$ecommerce = 1;
            if($ecommerce){
                $shipping_address_data['customer_id'] = $shipping_address_data['shipping_party_id'];
                $this->customerhook->CreateCustomerAddress($shipping_address_data);
            }*/

            $successMsg = 'Shipping Address Updated Successfully';
            $this->session->set_flashdata('shipping_address_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'shipping_address',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Shipping Address Inserted');
            $this->general_model->insertData($table, $log_data);
            $successMsg = 'Shipping Address Added Successfully';
            $this->session->set_flashdata('shipping_address_success',$successMsg);
        } else{
            $errorMsg = 'Shipping Address Add Unsuccessful';
            $this->session->set_flashdata('shipping_address_error',$errorMsg);
            echo json_decode($id);
        }
        echo json_decode($id);
    }

    public function delete() {

        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "shipping_address";
        $data = array(
            "delete_status" => 1);
        $where = array("shipping_address_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
            $successMsg = 'Shipping Address Deleted Successfully';
            $this->session->set_flashdata('shipping_address_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'shipping_address',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Shipping Address Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('shipping_address');
        } else {
            $errorMsg = 'Shipping Address Delete Unsuccessful';
            $this->session->set_flashdata('shipping_address_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Shipping Address cannot be Deleted.');
            redirect("shipping_address", 'refresh');
        }
    }

    public function get_party_name() {

        $party_type = $this->input->post('party_type');
        $output = '<option value="">Select Company</option>';
        if ($party_type == 'customer') {
            $customer = $this->customer_call();
            foreach ($customer as $row) {
                $output .= "<option value='" . $row->customer_id . "'>" . $row->customer_name . "</option>";
            }
        } elseif ($party_type == 'supplier') {
            $supplier = $this->supplier_call();
            foreach ($supplier as $row) {
                $output .= "<option value='" . $row->supplier_id . "'>" . $row->supplier_name . "</option>";
            }
        }

        echo json_encode($output);
    }

    public function edit_modal($id) {
        $id = $this->encryption_url->decode($id);
        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 's.*';
        $table = 'shipping_address s';
        // $join['contact_person c'] = 'c.contact_person_id=s.supplier_contact_person_id';
        $where = array('s.shipping_address_id' => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");

        $party_type = $data['data'][0]->shipping_party_type;
        $country_id = $data['data'][0]->country_id;
        $state_id = $data['data'][0]->state_id;
        $output = '<option value="">Select Company</option>';
        if ($party_type == 'customer') {
            $customer = $this->customer_call();
            foreach ($customer as $row) {
                $output .= "<option value='" . $row->customer_id . "'>" . $row->customer_name . "</option>";
            }
        } elseif ($party_type == 'supplier') {
            $supplier = $this->supplier_call();
            foreach ($supplier as $row) {
                $output .= "<option value='" . $row->supplier_id . "'>" . $row->supplier_name . "</option>";
            }
        }
        $data['party_list'] = $output;

        $output_state = '<option value="">Select State</option>';
        $string_st = 'st.*';
        $table_st = 'states st';
        $where_st = array('st.country_id' => $country_id);
        $state = $this->general_model->getRecords($string_st, $table_st, $where_st);

        foreach ($state as $row1) {
            $output_state .= "<option value='" . $row1->state_id . "'>" . $row1->state_name . "</option>";
        }
        $data['state_list'] = $output_state;
        $output_city = '<option value="">Select City</option>';

        $string_ct = 'ct.*';
        $table_ct = 'cities ct';
        $where_ct = array('ct.state_id' => $state_id);
        $city = $this->general_model->getRecords($string_ct, $table_ct, $where_ct);
        if (!empty($city)) {
            foreach ($city as $row2) {
                $output_city .= "<option value='" . $row2->city_id . "'>" . $row2->city_name . "</option>";
            }
        } else {
            $output_city .= "<option value='0'>Others</option>";
        }
        $data['city_list'] = $output_city;


        echo json_encode($data);
    }

    public function get_party_shipping_code() {
        $party_id = $this->input->post('party_id');
        $party_type = $this->input->post('party_type');
        if ($party_type == 'customer') {
            $string = 'customer_code  as party_code';
            $table = 'customer';
            $where = array('customer_id' => $party_id);
            $customer = $this->general_model->getRecords($string, $table, $where, $order = "");

            $string = 'count(*) as cnt';
            $table = 'shipping_address';
            $where = array('shipping_party_id' => $party_id, 'shipping_party_type' => $party_type);
            $count_shipping = $this->general_model->getRecords($string, $table, $where, $order = "");
        } else {
            $string = 'supplier_code as party_code';
            $table = 'supplier';
            $where = array('supplier_id' => $party_id);
            $customer = $this->general_model->getRecords($string, $table, $where, $order = "");

            $string = 'count(*) as cnt';
            $table = 'shipping_address';
            $where = array('shipping_party_id' => $party_id, 'shipping_party_type' => $party_type);
            $count_shipping = $this->general_model->getRecords($string, $table, $where, $order = "");
        }
        $count = $count_shipping[0]->cnt;
        $count = $count + 1;
        $shipping_code = $customer[0]->party_code . '-' . $count;
        $data = array('shipping_code' => $shipping_code);
        echo json_encode($data);
    }

    public function get_party_shipping_code_edit() {
        $party_id = $this->input->post('party_id');
        $party_type = $this->input->post('party_type');
        $id = $this->input->post('id');
        if ($party_type == 'customer') {
            $string = 'customer_code  as party_code';
            $table = 'customer';
            $where = array('customer_id' => $party_id);
            $customer = $this->general_model->getRecords($string, $table, $where, $order = "");

            $string = 'count(*) as cnt';
            $table = 'shipping_address';
            $where = array('shipping_party_id' => $party_id, 'shipping_party_type' => $party_type,
                'shipping_address_id !=' => $id);
            $count_shipping = $this->general_model->getRecords($string, $table, $where, $order = "");
        } else {
            $string = 'supplier_code as party_code';
            $table = 'supplier';
            $where = array('supplier_id' => $party_id);
            $customer = $this->general_model->getRecords($string, $table, $where, $order = "");

            $string = 'count(*) as cnt';
            $table = 'shipping_address';
            $where = array('shipping_party_id' => $party_id, 'shipping_party_type' => $party_type,
                'shipping_address_id !=' => $id);
            $count_shipping = $this->general_model->getRecords($string, $table, $where, $order = "");
        }
        $count = $count_shipping[0]->cnt;
        $count = $count + 1;
        $shipping_code = $customer[0]->party_code . '-' . $count;
        $data = array('shipping_code' => $shipping_code);
        echo json_encode($data);
    }

}
