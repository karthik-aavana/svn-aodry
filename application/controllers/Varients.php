<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Varients extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    function index() {
        $varients_module_id = $this->config->item('varients_module');
        $data['varients_module_id'] = $varients_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($varients_module_id, $modules, $privilege);
        $data['varients'] = $this->general_model->getRecords('*', 'varients', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'added_date',
                1 => 'varient_key',
                2 => 'varients_value',
                3 => 'added_user',
                4 => 'action',);
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->varients_list_field();
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
                    $varients_id = $this->encryption_url->encode($post->varients_id);
                    $nestedData['added_date'] = $post->added_date;
                    $nestedData['varient_key'] = $post->varient_key;
                    $nestedData['varients_value'] = $post->varients_value;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if(in_array($varients_module_id, $data['active_edit'])){
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_varient_value_modal"><a data-id="' . $post->varients_value_id . '"  data-toggle="tooltip" data-placement="bottom" title="Edit Varient Value" class="edit_varient_value btn btn-app"><i class="fa fa-pencil"></i></a></span>';

                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_varient_key_modal"><a data-id="' . $post->varients_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit Varient Key" class="edit_varient_key btn btn-app"><i class="fa fa-key"></i></a></span>';
                    }
                    $varient_id = $this->general_model->getRecords('*', 'varients_value', array(
                        'varients_id' => $post->varients_id,
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));

                    $varient_used = $this->general_model->getRecords('*', 'product_varients_value', array(
                        'varients_id' => $post->varients_id,
                        'delete_status' => 0)
                        );
                    /*$varient_used = $varient_used->result();*/
                    if(in_array($varients_module_id, $data['active_delete'])){
                        if ($varient_id) {
                            if(empty($varient_used))
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"> <a class="btn btn-app delete_button" data-id="' . $post->varients_value_id . '" data-path="varients/delete_varients_value" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                            // $cols .= '<a data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        } else {
                            $cols .= '<a data-toggle="modal" data-target="#delete_modal" data-id="' . $post->varients_id . '" data-path="varients/delete_varients_key" title="Delete Varient Key" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    }
                    $cols .= '</div></div>';
                    $disabled = '';
                    if(!in_array($varients_module_id, $data['active_delete']) && !in_array($varients_module_id, $data['active_edit'])){
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
            $this->load->view('varients/list', $data);
        }
    }

    function add() {
        $varients_module_id = $this->config->item('varients_module');
        $data['varients_module_id'] = $varients_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($varients_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['varients'] = $this->general_model->getRecords('*', 'varients', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));

        $this->load->view('varients/add', $data);
    }

    function add_varient_key_modal() {
        $varients_module_id = $this->config->item('varients_module');
        $data['module_id'] = $varients_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($varients_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $varient_data = array(
            "varient_key" => trim($this->input->post('varient_name')),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));

        $id = $this->general_model->insertData('varients', $varient_data);
        $log_data = array(
            'user_id' => $this->session->userdata('SESS_USER_ID'),
            'table_id' => $id,
            'table_name' => 'varients',
            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            'message' => 'Varients Inserted');

        $this->general_model->insertData('log', $log_data);

        $data['data'] = $this->general_model->getRecords('*', 'varients', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        $data['id'] = $id;
        echo json_encode($data);
    }

    function add_varients() {
        $varients_module_id = $this->config->item('varients_module');
        $data['module_id'] = $varients_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($varients_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post('varient_key'))) {

            $varient_key = $this->input->post('varient_key');
            $varient_value = $this->input->post('varient_value');
            $varient_value_data = explode(",", $varient_value[0]);

            $i = 0;
            foreach ($varient_value_data as $key => $value) {

                $varient_data = array(
                    'varients_id' => $varient_key,
                    'varients_value' => $varient_value_data[$i],
                    'added_date' => date('Y-m-d'),
                    'added_user_id' => $this->session->userdata('SESS_USER_ID'),
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'));

                $id = $this->general_model->insertData('varients_value', $varient_data);
                $i++;
            }
            $successMsg = 'Variant Added Successfully';
            $this->session->set_flashdata('varients_success',$successMsg);
            $log_data = array(
                    'user_id' => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id' => $id,
                    'table_name' => 'varients_value',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id" => $this->session->userdata("SESS_BRANCH_ID"),
                    'message' => 'Variants value Inserted');
                $this->general_model->insertData('log', $log_data);

            redirect('varients');
        } else {
            $errorMsg = 'Variant Add Unsuccessful';
            $this->session->set_flashdata('varients_error',$errorMsg);
            $this->load->view('varients/add', $data);
        }
    }

    function add_varients_ajax() {
        $varients_module_id = $this->config->item('varients_module');
        $data['module_id'] = $varients_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($varients_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $result = array();
        if (!empty($this->input->post('varient_key'))) {

            $varient_key = $this->input->post('varient_key');
            $varient_value = $this->input->post('varient_value');
            $varient_value_data = explode(",", $varient_value[0]);

            $i = 0;
            foreach ($varient_value_data as $key => $value) {

                $varient_data = array(
                    'varients_id' => $varient_key,
                    'varients_value' => $varient_value_data[$i],
                    'added_date' => date('Y-m-d'),
                    'added_user_id' => $this->session->userdata('SESS_USER_ID'),
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'));
                 
                $LeatherCraft_id = $this->config->item('LeatherCraft');
                if($LeatherCraft_id == $this->session->userdata('SESS_BRANCH_ID')){
                     $data  = $this->general_model->getRecords('count(*) num', 'varients_value', array('branch_id' => $this->session->userdata('SESS_BRANCH_ID'), 'delete_status' => 0, 'varients_id'  => $varient_key));
                    $code = $data[0]->num;
                    $varient_data['variant_value_code'] = (int) $code + 1;
                }


                $id = $this->general_model->insertData('varients_value', $varient_data);
                $i++;
            }
            $result['flag'] = true;
            $result['msg'] = 'Variant Added Successfully';
            /*$successMsg = 'Varients Added Successfully';
            $this->session->set_flashdata('varients_success',$successMsg);*/
            $log_data = array(
                    'user_id' => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id' => $id,
                    'table_name' => 'varients_value',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id" => $this->session->userdata("SESS_BRANCH_ID"),
                    'message' => 'Variants value Inserted');
                $this->general_model->insertData('log', $log_data);
        } else {
            $result['flag'] = false;
            $result['msg'] = 'Variant Add Unsuccessful';
            /*$errorMsg = 'Varients Add Unsuccessful';
            $this->session->set_flashdata('varients_error',$errorMsg);
            $this->load->view('varients/add', $data);*/
        }
        echo json_encode($result);
    }

    function get_varient_value_modal($id) {
        $data = $this->general_model->getRecords('*', 'varients_value', array(
            'varients_value_id' => $id,
            'delete_status' => 0));
        echo json_encode($data);
    }

    function get_varient_key_modal($id) {
        $data = $this->general_model->getRecords('*', 'varients', array(
            'varients_id' => $id,
            'delete_status' => 0));
        echo json_encode($data);
    }

    function get_varient_value() {
        $varient_id = $this->input->post('varient_id');

        $data = $this->general_model->getRecords('*', 'varients_value', array(
            'varients_id' => $varient_id,
            'delete_status' => 0,
            'branch_id' => $this->session->userdata("SESS_BRANCH_ID")));
        echo json_encode($data);
    }

    function edit_varient_value_modal() {
        $varients_module = $this->config->item('varients_module');
        $data['module_id'] = $varients_module;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($varients_module, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('id');

        $varients_value_data = array(
            "varients_value" => $this->input->post('varient_value'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
            if(@$this->input->post('varient_code')){
                $varients_value_data['variant_value_code'] = $this->input->post('varient_code');
            }

        if ($this->general_model->updateData('varients_value', $varients_value_data, array(
                    'varients_value_id' => $id))) {
            $result['flag'] = true;
            $result['msg'] = 'Variant Value Updated Successfully';
            /*$successMsg = 'Varients Value Updated Successfully';
            $this->session->set_flashdata('varients_success',$successMsg);*/
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'varients_value',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Varient value Updated');
            $this->general_model->insertData('log', $log_data);
        } else{
            $result['flag'] = false;
            $result['msg'] = 'Variant Value Update Unsuccessful';
            /*$errorMsg = 'Varients Value Update Unsuccessful';
            $this->session->set_flashdata('varients_error',$errorMsg);*/
        }
        echo json_encode($result);
    }

    function edit_varient_key_modal() {
        $varients_module = $this->config->item('varients_module');
        $data['module_id'] = $varients_module;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($varients_module, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('id');

        $varients_key_data = array(
            "varient_key" => $this->input->post('varient_key'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
        $result = array();
        if ($this->general_model->updateData('varients', $varients_key_data, array(
                    'varients_id' => $id))) {
            $result['flag'] = true;
            $result['msg'] = 'Variant Key Updated Successfully';
            /*$successMsg = 'Varients Key Updated Successfully';
            $this->session->set_flashdata('varients_success',$successMsg);*/
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'varients',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Varient Key Updated');
            $this->general_model->insertData('log', $log_data);
        }else{
            $result['flag'] = false;
            $result['msg'] = 'Variant Key Update Unsuccessful';
            /*$errorMsg = 'Varients Key Update Unsuccessful';
            $this->session->set_flashdata('varients_error',$errorMsg);*/
        }
        echo json_encode($result);
    }

    function delete_varients_value() {
        $varients_module = $this->config->item('varients_module');
        $data['module_id'] = $varients_module;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($varients_module, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        if ($this->general_model->updateData('varients_value', array(
                    'delete_status' => 1), array(
                    'varients_value_id' => $id))) {
            $successMsg = 'Variant Value Deleted Successfully';
            $this->session->set_flashdata('varients_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'varients_value',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Varient value Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('varients', 'refresh');
        } else {
            $errorMsg = 'Variant Value Delete Unsuccessful';
            $this->session->set_flashdata('varients_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Cannot be Deleted.');
            redirect("varients", 'refresh');
        }
    }

    function delete_varients_key() {
        $varients_module = $this->config->item('varients_module');
        $data['module_id'] = $varients_module;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($varients_module, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');


        if ($this->general_model->updateData('varients', array(
                    'delete_status' => 1), array(
                    'varients_id' => $id))) {
            $successMsg = 'Variant Key Deleted Successfully';
            $this->session->set_flashdata('varients_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'varients',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Varient key Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('varients', 'refresh');
        } else {
            $errorMsg = 'Variant Key Delete Unsuccessful';
            $this->session->set_flashdata('varients_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Cannot be Deleted.');
            redirect("varients", 'refresh');
        }
    }

    public function codeValidation(){
        $variant_code = trim($this->input->post('variant_code'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT varients_value_id FROM varients_value WHERE variant_value_code = '".$variant_code."' AND varients_value_id != '{$id}' ")->num_rows();

        echo  json_encode(array('rows' => $rows ));
    }
}
