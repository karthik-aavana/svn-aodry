<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subcategory extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('product_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['subcategory_module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'action',
                1 => 'subcategory_code',
                2 => 'subcategory_name',
                3 => 'category_name');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->subcategory_list_field();
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
                    $sub_category_id = $this->encryption_url->encode($post->sub_category_id);
                    $nestedData['subcategory_code'] = $post->sub_category_code;
                    $nestedData['subcategory_name'] = $post->sub_category_name;
                    $nestedData['category_name'] = $post->category_name;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($data['subcategory_module_id'], $data['active_edit'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_subcategory_modal"><a data-id="' . $sub_category_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_subcategory btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['subcategory_module_id'], $data['active_delete'])) {
                        $product_id = $this->general_model->getRecords('*', 'products', array(
                            'product_subcategory_id' => $post->sub_category_id,
                            'delete_status' => 0,
                            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                        $service_id = $this->general_model->getRecords('*', 'services', array(
                            'service_subcategory_id' => $post->sub_category_id,
                            'delete_status' => 0,
                            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                        if ($product_id || $service_id) {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal"><a data-toggle="tooltip" data-placement="bottom" title="Delete" class="btn btn-app btn-danger"><i class="fa fa-trash"></i></a></span>';
                        } else {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" > <a data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" class="btn btn-app delete_button" data-id="' . $sub_category_id . '" data-path="subcategory/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
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
            $this->load->view('subcategory/list', $data);
        }
    }

    public function get_category() {
        $category_module_id = $this->config->item('category_module');
        $data['module_id'] = $category_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($category_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $category_data = $this->general_model->getRecords('*', 'category', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        echo json_encode($category_data);
    }

    public function add_subcategory_ajax() {
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data = $this->general_model->getRecords('count(*) as num_sub_category', 'sub_category', array(
            'delete_status' => 0,
            'sub_category_name' => $this->input->post('subcategory_name'),
            'category_id' => $this->input->post('category_id_model'),
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        if ($data[0]->num_sub_category == 0) {
            $subcategory_code = $this->product_model->getMaxSubcategoryId();
            $sub_category_data = array(
                "category_id" => $this->input->post('category_id_model'),
                "sub_category_code" => $subcategory_code,
                "sub_category_name" => $this->input->post('subcategory_name'),
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
            $id = $this->general_model->insertData('sub_category', $sub_category_data);
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'sub_category',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Subcategory Inserted');
            $this->general_model->insertData('log', $log_data);
            $data['data'] = $this->general_model->getRecords('*', 'sub_category', array(
                'category_id' => $sub_category_data['category_id']));
            $data['subcategory_id'] = $id;
            echo json_encode($data);
        } else {
            $result = 'duplicate';
            echo json_encode($result);
        }
    }
    public function add() {
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
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
        }
        $data['data'] = $this->general_model->getRecords('*', 'category', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        $this->load->view('subcategory/add', $data);
    }
    public function add_subcategory_modal() {
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $this->form_validation->set_rules('subcategory_name', 'Subcategory Name', 'trim|required');
        $this->form_validation->set_rules('category_id_model', 'Category', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $category_id_model = trim($this->input->post('category_id_model'));
            $subcategory_name = trim($this->input->post('subcategory_name'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            /* category duplicate check */
            $data = $this->general_model->getRecords('count(*) as num_sub_category', 'sub_category', array(
                'delete_status' => 0,
                'sub_category_name' => $subcategory_name,
                'category_id' => $category_id_model,
                'branch_id' => $session_branch_id));
            $result = array();
            if ($data[0]->num_sub_category == 0) {
                $subcategory_code = $this->product_model->getMaxSubcategoryId();
                $sub_category_data = array(
                    "category_id" => $category_id_model,
                    "sub_category_code" => $subcategory_code,
                    "sub_category_name" => $subcategory_name,
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $session_user_id,
                    "branch_id" => $session_branch_id);
                if ($id = $this->general_model->insertData('sub_category', $sub_category_data)) {
                    $result['flag'] = true;
                    $result['msg'] = 'SubCategory Added Successfully';
                    /*$successMsg = 'SubCategory Added Successfully';
                    $this->session->set_flashdata('subcategory_success',$successMsg);*/
                    $log_data = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'table_id' => $id,
                        'table_name' => 'sub_category',
                        'financial_year_id' => $session_finacial_year_id,
                        "branch_id" => $session_branch_id,
                        'message' => 'Subcategory Inserted');
                    $this->general_model->insertData('log', $log_data);
                } else {
                    $result['flag'] = false;
                    $result['msg'] = 'SubCategory Add Unsuccessful';
                    /*$errorMsg = 'SubCategory Add Unsuccessful';
                    $this->session->set_flashdata('subcategory_error',$errorMsg);*/
                    $this->session->set_flashdata('fail', 'Subcategory can not be Inserted.');
                }
            } else {
                $result['flag'] = false;
                $result['msg'] = 'duplicate';
                $this->session->set_flashdata('fail', 'Subcategory is already exit.');
            }
        }
        echo json_encode($result);
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

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
        } $data['category'] = $this->general_model->getRecords('*', 'category', array(
            'delete_status' => 0));
        $data['data'] = $this->general_model->getRecords('*', 'sub_category', array(
            'sub_category_id' => $id,
            'delete_status' => 0));
        $this->load->view('subcategory/edit', $data);
    }

    public function edit_subcategory_modal() {
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('id');
        $this->form_validation->set_rules('subcategory_name', 'Subcategory Name', 'trim|required');
        $this->form_validation->set_rules('category_id_model', 'Category', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->edit($id);
        } else {
            $data = $this->general_model->getRecords('count(*) as num_sub_category', 'sub_category', array(
                'delete_status' => 0,
                'sub_category_name' => $this->input->post('subcategory_name'),
                'category_id' => $this->input->post('category_id_model'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'sub_category_id!=' => $id));
            $result = array();
            if ($data[0]->num_sub_category == 0) {
                $sub_category_data = array(
                    "category_id" => $this->input->post('category_id_model'),
                    "sub_category_name" => $this->input->post('subcategory_name'),
                    "updated_date" => date('Y-m-d'),
                    "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                    "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
                if ($this->general_model->updateData('sub_category', $sub_category_data, array(
                            'sub_category_id' => $id))) {
                    $result['flag'] = true;
                    $result['msg'] = 'SubCategory Updated Successfully';
                    /*$successMsg = 'SubCategory Updated Successfully';
                    $this->session->set_flashdata('subcategory_success',$successMsg);*/
                    $log_data = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'table_id' => $id,
                        'table_name' => 'sub_category',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                        'message' => 'Subcategory Updated');
                    $this->general_model->insertData('log', $log_data);
                } else {
                    $result['flag'] = false;
                    $result['msg'] = 'SubCategory Update Unsuccessful';
                    /*$errorMsg = 'SubCategory Update Unsuccessful';
                    $this->session->set_flashdata('subcategory_error',$errorMsg);*/
                    $this->session->set_flashdata('fail', 'Subcategory can not be Updated.');
                }
            } else {
                $result['flag'] = false;
                $result['msg'] = 'duplicate';
            }
        }
        echo json_encode($result);
    }

    public function delete() {
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('sub_category', array(
                    'delete_status' => 1), array(
                    'sub_category_id' => $id))) {
            $successMsg = 'SubCategory Deleted Successfully';
            $this->session->set_flashdata('subcategory_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'table_id' => $id,
                'table_name' => 'sub_category',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Subcategory Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('subcategory', 'refresh');
        } else {
            $errorMsg = 'SubCategory Delete Unsuccessful';
            $this->session->set_flashdata('subcategory_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Subcategory can not be Deleted.');
            redirect("subcategory", 'refresh');
        }
    }

    public function get_subcategory_modal($id) {
        $id = $this->encryption_url->decode($id);
        $subcategory_module_id = $this->config->item('subcategory_module');
        $data['module_id'] = $subcategory_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($subcategory_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data = $this->general_model->getRecords('*', 'sub_category', array(
            'sub_category_id' => $id,
            'delete_status' => 0));
        echo json_encode($data);
    }

}
