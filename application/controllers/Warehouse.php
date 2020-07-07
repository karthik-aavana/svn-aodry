<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }

    public function index() {
        $warehouse_module_id = $this->config->item('warehouse_module');
        $data['warehouse_module_id'] = $warehouse_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($warehouse_module_id, $modules, $privilege);
        $data['country'] = $this->country_call();
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /*if($this->session->userdata('bulk_success')){
            if($this->session->flashdata('email_send') != 'success')
            $data['bulk_success'] = $this->session->userdata('bulk_success');
            $this->session->unset_userdata('bulk_success');
        }elseif ($this->session->userdata('bulk_error')) {
            $data['bulk_error'] = $this->session->userdata('bulk_error');
            $this->session->unset_userdata('bulk_error');
        }*/

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'action',
                1 => 'Warehouse Name',
                3 => 'country',
                4 => 'state',
                5 => 'city' 
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->warehouse_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
            if (empty($this->input->post('search')['value'])) {
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            } 
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $warehouse_id = $this->encryption_url->encode($post->warehouse_id);
                    $nestedData['warehouse_name'] = $post->warehouse_name;
                    $nestedData['country'] = $post->country_name;
                    $nestedData['state'] = $post->state_name;
                    if ($post->city_name == '') {
                        $city_name = 'Others';
                    } else {
                        $city_name = $post->city_name;
                    }
                    $nestedData['city'] = $city_name;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['warehouse_module_id'], $data['active_edit'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_warehouse"><a data-id="' . $warehouse_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_warehouse btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }


                    if (in_array($data['warehouse_module_id'], $data['active_delete'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a data-id="' . $warehouse_id . '" data-path="warehouse/delete" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete_button btn btn-app"><i class="fa fa-trash"></i></a></span>';
                    }
                    $cols .= '</div></div>';
                    $disabled = '';
                    if(!in_array($data['warehouse_module_id'], $data['active_delete']) && !in_array($data['warehouse_module_id'], $data['active_edit'])){
                        $disabled = 'disabled';
                    }
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal"'.$disabled.'>';
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('warehouse/list', $data);
        }
    }
    public function add_warehouse() {
        $warehouse_module_id = $this->config->item('warehouse_module');
        $data['warehouse_module_id'] = $warehouse_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->check_privilege_section_modules($warehouse_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $warehouse_data = array(
            "warehouse_name" => $this->input->post('warehouse_name'),
            "warehouse_address" => $this->input->post('warehouse_address'),
            "warehouse_country_id" => $this->input->post('cmb_country'),
            "warehouse_state_id" => $this->input->post('cmb_state'),
            "warehouse_city_id" => $this->input->post('cmb_city'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "updated_date" => "",
            "updated_user_id" => "",
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "delete_status" => 0
        );
        $table = "warehouse";
        if ($id = $this->general_model->insertData($table, $warehouse_data)) {
            $successMsg = 'Warehouse Added Successfully';
            $this->session->set_flashdata('warehouse_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'warehouse',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Warehouse Inserted');
            $this->general_model->insertData($table, $log_data);
        } else{
            $errorMsg = 'Warehouse Add Unsuccessful';
            $this->session->set_flashdata('warehouse_error',$errorMsg);
            echo json_decode($id);
        }
        echo json_decode($id);
    }
    public function edit_modal($id) {
        $id = $this->encryption_url->decode($id);
        $warehouse_module_id = $this->config->item('warehouse_module');
        $data['shipping_address_module_id'] = $warehouse_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->check_privilege_section_modules($warehouse_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'w.*';
        $table = 'warehouse w';
        // $join['contact_person c'] = 'c.contact_person_id=s.supplier_contact_person_id';
        $where = array('w.warehouse_id' => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");

        $country_id = $data['data'][0]->warehouse_country_id;
        $state_id = $data['data'][0]->warehouse_state_id;
       // $output_state = '<option value="">Select State</option>';
         $output_state = '';
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
    public function edit_warehouse() {

        $warehouse_module_id = $this->config->item('warehouse_module');
        $data['warehouse_module_id'] = $warehouse_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->check_privilege_section_modules($warehouse_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $warehouse_id = $this->input->post('warehouse_id_edit');
        $warehouse_data = array(
                "warehouse_name" => $this->input->post('warehouse_name_edit'),
                "warehouse_address" => $this->input->post('warehouse_address_edit'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => date('Y-m-d'),
                "warehouse_country_id" => $this->input->post('cmb_country_edit1'),
                "warehouse_state_id" => $this->input->post('cmb_state_edit'),
                "warehouse_city_id" => $this->input->post('cmb_city_edit'),
                "updated_user_id" => $this->session->userdata('SESS_USER_ID')
        );
        $table = "warehouse";
        if ($this->general_model->updateData($table, $warehouse_data, array('warehouse_id' => $warehouse_id)))
        {
            $successMsg = 'Warehouse Updated Successfully';
            $this->session->set_flashdata('warehouse_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $warehouse_id,
                'table_name' => 'warehouse',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'warehouse Updated');
            $this->general_model->insertData($table, $log_data);
        }else{
            $errorMsg = 'Warehouse Update Unsuccessful';
            $this->session->set_flashdata('warehouse_error',$errorMsg);
            echo json_encode($warehouse_id);
        }
        echo json_encode($warehouse_id);
    }
    public function delete() {

        $warehouse_module_id = $this->config->item('warehouse_module');
        $data['warehouse_module_id'] = $warehouse_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->check_privilege_section_modules($warehouse_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "warehouse";
        $data = array(
            "delete_status" => 1);
        $where = array("warehouse_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
            $successMsg = 'Warehouse Deleted Successfully';
            $this->session->set_flashdata('warehouse_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'warehouse',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Warehouse Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('warehouse');
        } else {
            $errorMsg = 'Shipping Address Delete Unsuccessful';
            $this->session->set_flashdata('shipping_address_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Shipping Address cannot be Deleted.');
            redirect("warehouse", 'refresh');
        }
    }
    public function WarehouseNameValidation(){
        $warehouse_name = strtoupper(trim($this->input->post('warehouse_name')));
        $data = $this->general_model->getRecords('count(*) num', 'warehouse', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'warehouse_name' => $warehouse_name));
        echo json_encode($data);
    }
    public function WarehouseNameValidationEdit(){
        $warehouse_name = strtoupper(trim($this->input->post('warehouse_name_edit')));
        $warehouse_id = $this->input->post('warehouse_id_edit');
        $data = $this->general_model->getRecords('count(*) num', 'warehouse', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'warehouse_id !=' => $warehouse_id,
            'warehouse_name' => $warehouse_name));
        echo json_encode($data);
    }
}