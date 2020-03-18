<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Discount extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
        $discount_module_id = $this->config->item('discount_module');
        $data['discount_module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'action',
                1 => 'discount_name',
                2 => 'discount_value',
                3 => 'description'
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->discount_list_field();
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
                    $discount_id = $this->encryption_url->encode($post->discount_id);
                    $nestedData['discount_name'] = $post->discount_name . '@' . round($post->discount_value, 2) . '%';
                    $nestedData['discount_value'] = round($post->discount_value, 2) . '%';
                    $nestedData['description'] = $post->description;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_discount"><a data-id="' . $discount_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_discount btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $purchase_order_item = $this->general_model->getJoinRecords('pi.*', 'purchase_order_item pi', array(
                        'pi.purchase_order_item_discount_id' => $post->discount_id,
                        'pi.delete_status' => 0,
                        'p.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'purchase_order p' => 'p.purchase_order_id=pi.purchase_order_id'));
                    $purchase_item = $this->general_model->getJoinRecords('pi.*', 'purchase_item pi', array(
                        'pi.purchase_item_discount_id' => $post->discount_id,
                        'pi.delete_status' => 0,
                        'p.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'purchase p' => 'p.purchase_id=pi.purchase_id'));
                    $purchase_return_item = $this->general_model->getJoinRecords('pi.*', 'purchase_return_item pi', array(
                        'pi.purchase_return_item_discount_id' => $post->discount_id,
                        'pi.delete_status' => 0,
                        'p.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'purchase_return p' => 'p.purchase_return_id=pi.purchase_return_id'));
                    $purchase_credit_note_item = $this->general_model->getJoinRecords('pi.*', 'purchase_credit_note_item pi', array(
                        'pi.purchase_credit_note_item_discount_id' => $post->discount_id,
                        'pi.delete_status' => 0,
                        'p.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'purchase_credit_note p' => 'p.purchase_credit_note_id=pi.purchase_credit_note_id'));
                    $purchase_debit_note_item = $this->general_model->getJoinRecords('pi.*', 'purchase_debit_note_item pi', array(
                        'pi.purchase_debit_note_item_discount_id' => $post->discount_id,
                        'pi.delete_status' => 0,
                        'p.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'purchase_debit_note p' => 'p.purchase_debit_note_id=pi.purchase_debit_note_id'));
                    $quotation_item = $this->general_model->getJoinRecords('qi.*', 'quotation_item qi', array(
                        'qi.quotation_item_discount_id' => $post->discount_id,
                        'qi.delete_status' => 0,
                        'q.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'quotation q' => 'q.quotation_id=qi.quotation_id'));
                    $sales_item = $this->general_model->getJoinRecords('si.*', 'sales_item si', array(
                        'si.sales_item_discount_id' => $post->discount_id,
                        'si.delete_status' => 0,
                        's.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'sales s' => 's.sales_id=si.sales_id'));
                    $credit_note_item = $this->general_model->getJoinRecords('ci.*', 'sales_credit_note_item ci', array(
                        'ci.sales_credit_note_item_discount_id' => $post->discount_id,
                        'ci.delete_status' => 0,
                        'c.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'sales_credit_note c' => 'c.sales_credit_note_id=ci.sales_credit_note_id'));
                    $debit_note_item = $this->general_model->getJoinRecords('di.*', 'sales_debit_note_item di', array(
                        'di.sales_debit_note_item_discount_id' => $post->discount_id,
                        'di.delete_status' => 0,
                        'd.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'sales_debit_note d' => 'd.sales_debit_note_id=di.sales_debit_note_id'));
                    if ($purchase_order_item || $purchase_item || $purchase_return_item || $purchase_credit_note_item || $purchase_debit_note_item || $quotation_item || $sales_item || $credit_note_item || $debit_note_item) {
                        $cols .= '<span><a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-app btn-danger"><i class="fa fa-trash-o"></i></a></span>';
                    } else {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $discount_id . '" data-path="discount/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                    }

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
            $this->load->view('discount/list', $data);
        }
    }

    public function add() {
        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $this->load->view("discount/add", $data);
    }

    public function add_discount() {
        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $discount_data = array(
            "discount_name" => $this->input->post('discount_name'),
            "discount_value" => $this->input->post('discount_percentage'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
        if ($id = $this->general_model->insertData("discount", $discount_data)) {
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'discount',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Discount Inserted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
            redirect("discount", 'refresh');
        }
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $modules = $this->modules;
        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        } $join = [
            "users" => "users.id = added_user_id"];
        $data['data'] = $this->general_model->getJoinRecords("discount.*,users.username", "discount", [
            "discount.delete_status" => 0,
            'discount_id' => $id], $join);
        $this->load->view("discount/edit", $data);
    }

    public function edit_discount() {
        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post("id");
        $discount_data = array(
            "discount_name" => $this->input->post('discount_name'),
            "discount_value" => $this->input->post('discount_percentage'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
        if ($id1 = $this->general_model->updateData('discount', $discount_data, array(
                    'discount_id' => $id))) {
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id1,
                    'table_name'        => 'discount',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Discount Updated' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
            redirect("discount", 'refresh');
        }
    }

    public function add_discount_ajax() {
        $discount_data = array(
            "discount_name" => $this->input->post('discount_name'),
            "discount_value" => $this->input->post('discount_percentage'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "updated_date" => "",
            "updated_user_id" => "");
        if($id = $this->general_model->insertData("discount", $discount_data)){
            $log_data = array(
                'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                'table_id'          => $id,
                'table_name'        => 'discount',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                'message'           => 'Discount Inserted' );
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
        $value = sprintf("%.2f", $discount_data['discount_value']);
        $data['id'] = $id;
        $data['value'] = $value;
        echo json_encode($data);
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($id1 = $this->general_model->updateData('discount', [
                    "delete_status" => 1], array(
                    'discount_id' => $id))) {
            $successMsg = 'Discount Deleted Successfully';
            $this->session->set_flashdata('discount_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id1,
                    'table_name'        => 'discount',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Discount Deleted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
        }else{
            $errorMsg = 'Discount Delete Unsuccessful';
            $this->session->set_flashdata('discount_error',$errorMsg);
        }
         redirect("discount", 'refresh');
    }

    public function get_discount() {
        $discount_id = $this->input->post('discount_id');
        $discount_percentage = $this->input->post('discount_percentage');
        $data = $this->general_model->getRecords('count(*) as num_discount_percentage', 'discount', array(
            'delete_status' => 0,
            'discount_value' => $discount_percentage,
            'discount_id!=' => $discount_id,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        echo json_encode($data);
    }

    public function add_discount_modal() {

        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* form validation check */
        $this->form_validation->set_rules('discount_percentage', 'Discount Percentage', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $result = $this->form_validation->run();
        } else {
            $discount_name = trim($this->input->post('discount_name'));
            $discount_percentage = trim($this->input->post('discount_percentage'));
            $description = trim($this->input->post('description'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* Discount duplicate check */
            $data = $this->general_model->getRecords('count(*) as discount', 'discount', array(
                'delete_status' => 0,
                'discount_value' => $discount_percentage,
                'branch_id' => $session_branch_id));
            $resp = array();
            if ($data[0]->discount == 0) {
                $discount_data = array(
                    "discount_name" => $discount_name,
                    "discount_value" => $discount_percentage,
                    "description" => $description,
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $session_user_id,
                    "branch_id" => $session_branch_id);
                if ($id = $this->general_model->insertData("discount", $discount_data)) {
                    $resp['flag'] = true;
                    $resp['msg'] = 'Discount Added Successfully';
                    /*$successMsg = 'Discount Added Successfully';
                    $this->session->set_flashdata('discount_success',$successMsg);*/
                    $log_data = array(
                        'user_id' => $session_user_id,
                        'table_id' => $id,
                        'table_name' => 'discount',
                        'financial_year_id' => $session_finacial_year_id,
                        "branch_id" => $session_branch_id,
                        'message' => 'Discount Inserted');
                    $this->general_model->insertData('log', $log_data);
                } else {
                    $resp['flag'] = false;
                    $resp['msg'] = 'Discount Add Unsuccessful';
                    /*$errorMsg = 'Discount Add Unsuccessful';
                    $this->session->set_flashdata('discount_error',$errorMsg);*/
                    $this->session->set_flashdata('fail', 'Discount can not be Inserted.');
                }
            } else {
                $resp['flag'] = false;
                $resp['msg'] = 'duplicate';
                $this->session->set_flashdata('fail', 'Discount Percentage is already exit.');
            }
        }
        echo json_encode($resp);
    }
    public function add_discount_modal_ajax() {
        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* form validation check */
        $this->form_validation->set_rules('discount_percentage', 'Discount Percentage', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $result = $this->form_validation->run();
        } else {
            $discount_name = trim($this->input->post('discount_name'));
            $discount_percentage = trim($this->input->post('discount_percentage'));
            $description = trim($this->input->post('description'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* Discount duplicate check */
            $data = $this->general_model->getRecords('count(*) as discount', 'discount', array(
                'delete_status' => 0,
                'discount_value' => $discount_percentage,
                'branch_id' => $session_branch_id));
            $resp = array();
            if ($data[0]->discount == 0) {
                $discount_data = array(
                    "discount_name" => $discount_name,
                    "discount_value" => $discount_percentage,
                    "description" => $description,
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $session_user_id,
                    "branch_id" => $session_branch_id);
                if ($id = $this->general_model->insertData("discount", $discount_data)) {
                    $log_data = array(
                        'user_id' => $session_user_id,
                        'table_id' => $id,
                        'table_name' => 'discount',
                        'financial_year_id' => $session_finacial_year_id,
                        "branch_id" => $session_branch_id,
                        'message' => 'Discount Inserted');
                    $this->general_model->insertData('log', $log_data);
                }
                $resp['discount'] = $this->discount_call();
                $resp['id']                      = $id;
                $resp['tax_value']               = $discount_percentage;
            }else {
                $resp['msg'] = 'duplicate';    
            }
        }
        echo json_encode($resp);    
    }
    public function get_discount_modal($id) {
        $id = $this->encryption_url->decode($id);
        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data = $this->general_model->getRecords('*', 'discount', array(
            'discount_id' => $id,
            'delete_status' => 0));
        echo json_encode($data);
    }

    public function update_discount_modal() {
        $discount_module_id = $this->config->item('discount_module');
        $data['module_id'] = $discount_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($discount_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post("id");
        //    $discount_id         = $this->input->post('discount_id');
        $discount_percentage = $this->input->post('discount_percentage');
        $data = $this->general_model->getRecords('count(*) as num_discount_percentage', 'discount', array(
            'delete_status' => 0,
            'discount_value' => $discount_percentage,
            'discount_id!=' => $id,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        $resp = array();
        if ($data[0]->num_discount_percentage == 0) {
            $discount_data = array(
                "discount_name" => $this->input->post('discount_name'),
                "discount_value" => $this->input->post('discount_percentage'),
                "description" => $this->input->post('description'),
                "updated_date" => date('Y-m-d'),
                "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
            if ($id_i1 = $this->general_model->updateData('discount', $discount_data, array('discount_id' => $id))) {
                $resp['flag'] = true;
                $resp['msg'] = 'Discount Updated Successfully';
                /*$successMsg = 'Discount Updated Successfully';
                $this->session->set_flashdata('discount_success',$successMsg);*/
                $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id_i1,
                    'table_name'        => 'discount',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Discount Updated' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
            } else{
                $resp['flag'] = false;
                $resp['msg'] = 'Discount Update Unsuccessful';
                /*$errorMsg = 'Discount Update Unsuccessful';
                $this->session->set_flashdata('discount_error',$errorMsg);*/
            }
        } else {
            $resp['flag'] = false;
            $resp['msg'] = 'duplicate';
        }
        echo json_encode($resp);
    }
}