<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subdepartment extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('product_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
        $subdepartment_module_id = $this->config->item('sub_department_module');
        $data['subdepartment_module_id'] = $subdepartment_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($subdepartment_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "sub_department_id";
        $table_name = "sub_department";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'action',
                1 => 'sub_department_code',
                2 => 'sub_department_name',
                3 => 'department_name');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->subdepartment_list_field();
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
                $sub_department_id = $this->encryption_url->encode($post->sub_department_id);
                    $nestedData['sub_department_code'] = $post->sub_department_code;
                    $nestedData['sub_department_name'] = $post->sub_department_name;
                    $nestedData['department_name'] = $post->department_name;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($data['subdepartment_module_id'], $data['active_edit'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_subdepartment_modal"><a data-id="' . $sub_department_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_subdepartment btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['subdepartment_module_id'], $data['active_delete'])) {
                      /*  $product_id = $this->general_model->getRecords('*', 'products', array(
                            'product_subcategory_id' => $post->sub_category_id,
                            'delete_status' => 0,
                            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                        $service_id = $this->general_model->getRecords('*', 'services', array(
                            'service_subcategory_id' => $post->sub_category_id,
                            'delete_status' => 0,
                            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                        if ($product_id || $service_id) {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal"><a data-toggle="tooltip" data-placement="bottom" title="Delete" class="btn btn-app btn-danger"><i class="fa fa-trash"></i></a></span>';
                        } else {*/
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" > <a data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" class="btn btn-app delete_button" data-id="' . $sub_department_id . '" data-path="subdepartment/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                       // }
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
            $this->load->view('subdepartment/list', $data);
        }
    }

    public function get_department() {
        $department_module_id = $this->config->item('department_module');
        $data['module_id'] = $department_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($department_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $category_data = $this->general_model->getRecords('*', 'department', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        echo json_encode($category_data);
    }


    public function add_subdepartment_modal() {
        $subdepartment_module_id = $this->config->item('sub_department_module');
        $data['module_id'] = $subdepartment_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($subdepartment_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $this->form_validation->set_rules('subdepartment_name', 'Subdepartment Name', 'trim|required');
        $this->form_validation->set_rules('department_id', 'Department', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $department_id = trim($this->input->post('department_id'));
            $subdepartment_name = trim($this->input->post('subdepartment_name'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            $subdepartment_code = trim($this->input->post('subdepartment_code'));
            /* Subdepartment duplicate check */
            $data = $this->general_model->getRecords('count(*) as num_subdepartment', 'sub_department', array(
                'delete_status' => 0,
                'sub_department_name' => $subdepartment_name,
                'department_id' => $department_id,
                'branch_id' => $session_branch_id));
            $result = array();
            if ($data[0]->num_subdepartment == 0) {
                $sub_department_data = array(
                    "department_id" => $department_id,
                    "sub_department_code" => $subdepartment_code,
                    "sub_department_name" => $subdepartment_name,
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $session_user_id,
                    "branch_id" => $session_branch_id);
                if ($id = $this->general_model->insertData('sub_department', $sub_department_data)) {
                    $result['flag'] = true;
                    $result['msg'] = 'Sub Department Added Successfully';
                    $log_data = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'table_id' => $id,
                        'table_name' => 'sub_department',
                        'financial_year_id' => $session_finacial_year_id,
                        "branch_id" => $session_branch_id,
                        'message' => 'Sub Department Inserted');
                    $this->general_model->insertData('log', $log_data);
                } else {
                    $result['flag'] = false;
                    $result['msg'] = 'Sub Department Add Unsuccessful';
                    $this->session->set_flashdata('fail', 'Sub Department can not be Inserted.');
                }
            }else {
                $result['flag'] = false;
                $result['msg'] = 'duplicate';
                $this->session->set_flashdata('fail', 'Sub Department is already exit.');
            }
        }
        echo json_encode($result);
    }

    public function edit_subdepartment_modal() {
        $subdepartment_module_id = $this->config->item('sub_department_module');
        $data['module_id'] = $subdepartment_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($subdepartment_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('id');
        $this->form_validation->set_rules('subdepartment_name', 'Subdepartment Name', 'trim|required');
        $this->form_validation->set_rules('department_id', 'Department', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->edit($id);
        } else {
            $department_id = trim($this->input->post('department_id'));
            $subdepartment_name = trim($this->input->post('subdepartment_name'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            $subdepartment_code = trim($this->input->post('subdepartment_code'));
            /* Subdepartment duplicate check */
            $data = $this->general_model->getRecords('count(*) as num_subdepartment', 'sub_department', array(
                'delete_status' => 0,
                'sub_department_name' => $subdepartment_name,
                'department_id' => $department_id,
                'sub_department_id !=' => $id,
                'branch_id' => $session_branch_id));
            $result = array();
            if ($data[0]->num_subdepartment == 0) {
                $sub_department_data = array(
                    "department_id" => $department_id,
                    "sub_department_code" => $subdepartment_code,
                    "sub_department_name" => $subdepartment_name,
                    "updated_date" => date('Y-m-d'),
                    "updated_user_id" => $session_user_id,
                    "branch_id" => $session_branch_id);
                if ($this->general_model->updateData('sub_department', $sub_department_data, array('sub_department_id' => $id))) {
                    $result['flag'] = true;
                    $result['msg'] = 'Sub Department Updated Successfully';
                    $log_data = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'table_id' => $id,
                        'table_name' => 'sub_department',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                        'message' => 'Sub Department Updated');
                    $this->general_model->insertData('log', $log_data);
                } else {
                    $result['flag'] = false;
                    $result['msg'] = 'Sub Department Update Unsuccessful';
                    $this->session->set_flashdata('fail', 'Sub Department can not be Updated.');
                }
            } else {
                $result['flag'] = false;
                $result['msg'] = 'duplicate';
            }
        }
        echo json_encode($result);
    }

    public function delete() {
        $subdepartment_module_id = $this->config->item('sub_department_module');
        $data['module_id'] = $subdepartment_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($subdepartment_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('sub_department', array(
                    'delete_status' => 1), array(
                    'sub_department_id' => $id))) {
            $successMsg = 'Sub Department Deleted Successfully';
            $this->session->set_flashdata('subdepartment_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'sub_department',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Sub Department Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('subdepartment', 'refresh');
        } else {
            $errorMsg = 'Sub Department Delete Unsuccessful';
            $this->session->set_flashdata('subdepartment_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Sub Department can not be Deleted.');
            redirect("subdepartment", 'refresh');
        }
    }

    public function get_subdepartment_modal($id) {
        $id = $this->encryption_url->decode($id);
        $subdepartment_module_id = $this->config->item('sub_department_module');
        $data['module_id'] = $subdepartment_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($subdepartment_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data = $this->general_model->getRecords('*', 'sub_department', array(
            'sub_department_id' => $id,
            'delete_status' => 0));
        echo json_encode($data);
    }

    public function add_subdepartment_ajax() {
        $subdepartment_module_id = $this->config->item('sub_department_module');
        $data['module_id'] = $subdepartment_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($subdepartment_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $access_settings = $data['access_settings'];
        $primary_id = "sub_department_id";
        $table_name = "sub_department";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $subdepartment_code = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $department_id = trim($this->input->post('department_id'));
        $subdepartment_name = trim($this->input->post('subdepartment_name'));
        $session_user_id = $this->session->userdata('SESS_USER_ID');
        $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            
            /* Subdepartment duplicate check */
            $data = $this->general_model->getRecords('count(*) as num_subdepartment', 'sub_department', array(
                'delete_status' => 0,
                'sub_department_name' => $subdepartment_name,
                'department_id' => $department_id,
                'branch_id' => $session_branch_id));
            $result = array();
            if ($data[0]->num_subdepartment == 0) {
            $sub_department_data = array(
                    "department_id" => $department_id,
                    "sub_department_code" => $subdepartment_code,
                    "sub_department_name" => $subdepartment_name,
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $session_user_id,
                    "branch_id" => $session_branch_id);
                $id = $this->general_model->insertData('sub_department', $sub_department_data);
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'sub_department',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Sub Department Inserted');
                $this->general_model->insertData('log', $log_data);
                $data['data'] = $this->general_model->getRecords('*', 'sub_department', array(
                    'department_id' => $department_id));
                $data['subdepartment_id'] = $id;
            echo json_encode($data);
        } else {
            $result = 'duplicate';
            echo json_encode($result);
        }
    }

}
