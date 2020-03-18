<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends MY_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('product_model');
        $this->modules = $this->get_modules();
        $this->load->helper(array('form','url' ));
        $this->load->library('form_validation');
    }

    public function index(){
        $department_module_id           = $this->config->item('department_module');
        $data['department_module_id']   = $department_module_id;
        $modules                      = $this->modules;
        $privilege                    = "view_privilege";
        $data['privilege']            = $privilege;
        $section_modules              = $this->get_section_modules($department_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "department_id";
        $table_name = "department";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        if (!empty($this->input->post())){
            $columns = array(
                0 => 'department_code',
                1 => 'department_name',
                2 => 'action',
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->department_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])){
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }else{
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } 

            $send_data = array();
            if (!empty($posts)){
                foreach ($posts as $post){
                    $department_id = $this->encryption_url->encode($post->department_id);
                    $nestedData['department_code'] = $post->department_code;
                    $nestedData['department_name'] = $post->department_name;

                   $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
							
                    if (in_array($data['department_module_id'], $data['active_edit'])){	
                        $cols .= '<span data-toggle="modal" data-target="#edit_department_modal" data-backdrop="static" data-keyboard="false"><a data-id="' . $department_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_category btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['department_module_id'], $data['active_delete'])){
                        $sub_department_id = $this->general_model->getRecords('*', 'sub_department', array(
                                'department_id'   => $post->department_id,
                                'delete_status' => 0,
                                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                        if ($sub_department_id){	
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal" data-path="sales/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }else{
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $department_id . '" data-path="department/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                    }

					$cols .= '</div></div>';
					
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]          = $nestedData;
                }
            }
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }else{
            $this->load->view('department/list', $data);
        }
    }

    public function add_department_modal(){
        $department_module_id   = $this->config->item('department_module');
        $data['module_id']    = $department_module_id;
        $modules              = $this->modules;
        $privilege            = "add_privilege";
        $data['privilege']    = "add_privilege";
        $section_modules      = $this->get_section_modules($department_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* form validation check */
        $this->form_validation->set_rules('department_name', 'Department Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $department_name = trim($this->input->post('department_name'));
            $department_code = trim($this->input->post('department_code'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* department duplicate check */
            $data  = $this->general_model->getRecords('count(*) as num_department', 'department', array(
                'delete_status' => 0,
                'department_name' => $department_name,
                'branch_id'     => $session_branch_id ));
            $result = array();
            if($data[0]->num_department == 0) {
                $department_data = array(
                        "department_code" => $department_code,
                        "department_name" => $department_name,
                        "added_date"    => date('Y-m-d'),
                        "added_user_id" => $session_user_id,
                        "branch_id"     => $session_branch_id );
                if ($id  = $this->general_model->insertData('department', $department_data)) {
                    $result['flag'] = true;
                    $result['msg'] = 'Department Added Successfully';
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'department',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Department Inserted' );
                    $this->general_model->insertData('log', $log_data);
                }
                else {
                    $result['flag'] = false;
                    $result['msg'] = 'Department Add Unsuccessful';
                    $this->session->set_flashdata('fail', 'Department can not be Inserted.');
                }
            }else{
                $result['flag'] = false;
                $result['duplicate']='duplicate';
                $this->session->set_flashdata('fail', 'Department is already exit.');
            }
        }
        echo json_encode($result);
    }

    public function get_department_modal($id){
        $id                   = $this->encryption_url->decode($id);
        $department_module_id   = $this->config->item('department_module');
        $data['module_id']    = $department_module_id;
        $modules              = $this->modules;
        $privilege            = "view_privilege";
        $data['privilege']    = "view_privilege";
        $section_modules      = $this->get_section_modules($department_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data = $this->general_model->getRecords('*', 'department', array(
                'department_id'   => $id,
                'delete_status' => 0 ));
        echo json_encode($data);
    }

    public function edit_department_modal(){
        $department_module_id  = $this->config->item('department_module');
        $data['module_id']   = $department_module_id;
        $modules             = $this->modules;
        $privilege           = "edit_privilege";
        $data['privilege']   = "edit_privilege";
        $section_modules     = $this->get_section_modules($department_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id  = $this->input->post('id');
        $this->form_validation->set_rules('department_name', 'Department Name', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->edit($id);
        }else {
            $department_name = trim($this->input->post('department_name'));
            $department_code = trim($this->input->post('department_code'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            /* department duplicate check */
            $data  = $this->general_model->getRecords('count(*) as num_department', 'department', array(
                'delete_status' => 0,
                'department_name' => $department_name,
                'branch_id'  => $session_branch_id,
                'department_id!=' => $id ));
            $result = array();
            if($data[0]->num_department == 0) {
                $department_data = array(
                        "department_code" => $department_code,
                        "department_name" => $department_name,
                        "updated_date"    => date('Y-m-d'),
                        "updated_user_id" => $session_user_id);
                
                if ($this->general_model->updateData('department', $department_data, array(
                                'department_id' => $id ))) {
                    $result['flag'] = true;
                    $result['msg'] = 'Department Updated successfully';
                    $log_data = array(
                            'user_id'           => $this->session->userdata('SESS_USER_ID'),
                            'table_id'          => $id,
                            'table_name'        => 'department',
                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                            'message'           => 'Department Updated' );
                    $this->general_model->insertData('log', $log_data);
                }else{
                    $result['flag'] = false;
                    $result['msg'] = 'Department Update Unsuccessful';
                    $this->session->set_flashdata('fail', 'Department can not be Updated.');
                }
            }else{
                $result['flag'] = false;
                $result['duplicate']='duplicate'; 
            }
        } 
        echo json_encode($result);
    }

    public function delete(){
        $department_module_id  = $this->config->item('department_module');
        $data['module_id']   = $department_module_id;
        $modules             = $this->modules;
        $privilege           = "delete_privilege";
        $data['privilege']   = "delete_privilege";
        $section_modules     = $this->get_section_modules($department_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id  = $this->input->post('delete_id');
        $id  = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('department', array(
                'delete_status' => 1 ), array(
                'department_id' => $id ))){
            $successMsg = 'Department Deleted successfully';
            $this->session->set_flashdata('department_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'department',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Department Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('department');
        }else{
            $errorMsg = 'Department Delete Unsuccessful';
            $this->session->set_flashdata('department_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Department can not be Deleted.');
            redirect("department", 'refresh');
        }
    }

    public function add_department_ajax(){
        $department_module_id   = $this->config->item('department_module');
        $data['module_id']    = $department_module_id;
        $modules              = $this->modules;
        $privilege            = "add_privilege";
        $data['privilege']    = "add_privilege";
        $section_modules      = $this->get_section_modules($department_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
          $access_settings = $data['access_settings'];
        $primary_id = "department_id";
        $table_name = "department";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $department_code = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        
            $department_name = trim($this->input->post('department_name'));
           
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* department duplicate check */
            $data  = $this->general_model->getRecords('count(*) as num_department', 'department', array(
                'delete_status' => 0,
                'department_name' => $department_name,
                'branch_id'     => $session_branch_id ));
            $result = array();
            if($data[0]->num_department == 0) {
        
                $department_data = array(
                            "department_code" => $department_code,
                            "department_name" => $department_name,
                            "added_date"    => date('Y-m-d'),
                            "added_user_id" => $session_user_id,
                            "branch_id"     => $session_branch_id );
                $id  = $this->general_model->insertData('department', $department_data);
        
                $log_data  = array('user_id' => $this->session->userdata('SESS_USER_ID'),
                            'table_id'          => $id,
                            'table_name'        => 'department',
                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                            'message'           => 'Department Inserted' );
                $this->general_model->insertData('log', $log_data);
                $data['data'] = $this->general_model->getRecords('*', 'department', array('delete_status' => 0,'branch_id'     => $session_branch_id ));
                    $data['id'] = $id;
            echo json_encode($data);
        }else{
            $result = 'duplicate' ; 
                    echo json_encode($result);
        }
    }

    public function get_department_ajax(){
        $category_module_id  = $this->config->item('department_module');
        $data['module_id']   = $category_module_id;
        $modules             = $this->modules;
        $privilege           = "view_privilege";
        $data['privilege']   = "view_privilege";
        $section_modules     = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $id  = $this->input->post('new_department');
        $data['data']    = $this->general_model->getRecords('*', 'department', array('delete_status' => 0, 'branch_id' => $session_branch_id));
        $data['id'] = $id;
        echo json_encode($data);
    }

}

