<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('product_model');
        $this->modules = $this->get_modules();
        $this->load->helper(array(
                'form',
                'url' ));
        $this->load->library('form_validation');
    }

    public function index(){
        $category_module_id           = $this->config->item('category_module');
        $data['category_module_id']   = $category_module_id;
        $modules                      = $this->modules;
        $privilege                    = "view_privilege";
        $data['privilege']            = $privilege;
        $section_modules              = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                0 => 'category_code',
                1 => 'category_name',
                2 => 'category_type',
                3 => 'action',
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->category_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;

              if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
            if (empty($this->input->post('search')['value']))
            {
                // $list_data['limit']  = $limit;
                // $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                // $list_data['limit']  = $limit;
                // $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $category_id                 = $this->encryption_url->encode($post->category_id);
                   $nestedData['category_code'] = $post->category_code;
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['category_type'] = ucwords($post->category_type);

                   $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
							
                    if (in_array($data['category_module_id'], $data['active_edit']))
                    {	
                        $cols .= '<span data-toggle="modal" data-target="#edit_category_modal" data-backdrop="static" data-keyboard="false"><a data-id="' . $category_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_category btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['category_module_id'], $data['active_delete']))
                    {
                        $sub_category_id = $this->general_model->getRecords('*', 'sub_category', array(
                                'category_id'   => $post->category_id,
                                'delete_status' => 0,
                                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                        if ($sub_category_id)
                        {	
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal" data-path="sales/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                        else
                        {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $category_id . '" data-path="category/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                    }

					$cols .= '</div></div>';
					$disabled = '';
                    if(!in_array($data['category_module_id'], $data['active_delete']) && !in_array($data['category_module_id'], $data['active_edit'])){
                        $disabled = 'disabled';
                    }
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal"'.$disabled.'>';
                    $send_data[]          = $nestedData;
                }
            }
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('category/list', $data);
        }
    }

    public function add()
    {
        $category_module_id       = $this->config->item('category_module');
        $data['module_id']        = $category_module_id;
        $modules                  = $this->modules;
        $privilege                = "add_privilege";
        $data['privilege']        = "add_privilege";
        $section_modules          = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view('category/add', $data);
    }

    public function add_category_ajax()
    {
        $category_module_id   = $this->config->item('category_module');
        $data['module_id']    = $category_module_id;
        $modules              = $this->modules;
        $privilege            = "add_privilege";
        $data['privilege']    = "add_privilege";
        $section_modules      = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data  = $this->general_model->getRecords('count(*) as num_category', 'category', array(
                'delete_status' => 0,
                'category_name' => trim($this->input->post('category_name')),
                'category_type' => trim($this->input->post('category_type')),
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        if($data[0]->num_category == 0) {

            $category_code                   = $this->product_model->getMaxCategoryId();
            $category_data                   = array(
                    "category_code" => $category_code,
                    "category_name" => trim($this->input->post('category_name')),
                    "category_type" => trim($this->input->post('category_type')),
                    "added_date"    => date('Y-m-d'),
                    "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') );
            $id                              = $this->general_model->insertData('category', $category_data);
            $type                            = trim($this->input->post('category_type'));
            $log_data                        = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'category',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Category Inserted(Subcategory)' );
            $this->general_model->insertData('log', $log_data);
            $data['data']               = $this->general_model->getRecords('*', 'category', array(
                    'category_type' => $type ));
            $data['id']                = $id;
            echo json_encode($data);
        }else{
            $result = 'duplicate' ; 
            echo json_encode($result);
        }
    }

    public function get_category_ajax()
    {
        /*$category_module_id  = $this->config->item('category_module');
        $data['module_id']   = $category_module_id;
        $modules             = $this->modules;
        $privilege           = "view_privilege";
        $data['privilege']   = "view_privilege";
        $section_modules     = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        presents all the needed 
        $data=array_merge($data,$section_modules);*/

        $id              = $this->input->post('new_category');
        $type            = $this->input->post('new_type');
        $data['data']    = $this->general_model->getRecords('*', 'category', array(
                'category_type' => $type,
                'delete_status' => 0 ));
        $data['id']                      = $id;
        echo json_encode($data);
    }

    public function get_category_modal($id)
    {
        $id                   = $this->encryption_url->decode($id);
        $category_module_id   = $this->config->item('category_module');
        $data['module_id']    = $category_module_id;
        $modules              = $this->modules;
        $privilege            = "edit_privilege";
        $data['privilege']    = "edit_privilege";
        $section_modules      = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data = $this->general_model->getRecords('*', 'category', array(
                'category_id'   => $id,
                'delete_status' => 0 ));
        echo json_encode($data);
    }

    public function add_category_modal()
    {
        $category_module_id   = $this->config->item('category_module');
        $data['module_id']    = $category_module_id;
        $modules              = $this->modules;
        $privilege            = "add_privilege";
        $data['privilege']    = "add_privilege";
        $section_modules      = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* form validation check */
        $this->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $category_name = trim($this->input->post('category_name'));
            $category_type = trim($this->input->post('category_type'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* category duplicate check */
            $data  = $this->general_model->getRecords('count(*) as num_category,category_type', 'category', array(
                'delete_status' => 0,
                'category_name' => $category_name,
                //'category_type' => $category_type,
                'branch_id'     => $session_branch_id ));
           /* $data1  = $this->general_model->getRecords('count(*) as category_type', 'category', array(
                'delete_status' => 0,
                'category_name' => $category_name,
                //'category_type' => $category_type,
                'branch_id'     => $session_branch_id ));*/
            $result = array();
            if($data[0]->num_category == 0) {
                $category_code = $this->product_model->getMaxCategoryId();
                $category_data = array(
                        "category_code" => $category_code,
                        "category_name" => $category_name,
                        "category_type" => $category_type,
                        "added_date"    => date('Y-m-d'),
                        "added_user_id" => $session_user_id,
                        "branch_id"     => $session_branch_id );
                if ($id  = $this->general_model->insertData('category', $category_data)) {
                    $result['flag'] = true;
                    $result['msg'] = 'Category Added Successfully';
                    /*$successMsg = 'Category Added Successfully';
                    $this->session->set_flashdata('category_success',$successMsg);*/
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'category',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Category Inserted' );
                    $this->general_model->insertData('log', $log_data);
                }
                else {
                    $result['flag'] = false;
                    $result['msg'] = 'Category Add Unsuccessful';
                    /*$errorMsg = 'Category Add Unsuccessful';
                    $this->session->set_flashdata('category_error',$errorMsg);*/
                    $this->session->set_flashdata('fail', 'Category can not be Inserted.');
                }
            }else{
                $result['flag'] = false;
                $result['duplicate']='duplicate';
                $this->session->set_flashdata('fail', 'Category is already exit.');
            }
        }
        echo json_encode($result);
    }

    public function edit($id)
    {
        $id                  = $this->encryption_url->decode($id);
        $category_module_id  = $this->config->item('category_module');
        $data['module_id']   = $category_module_id;
        $modules             = $this->modules;
        $privilege           = "edit_privilege";
        $data['privilege']   = "edit_privilege";
        $section_modules     = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['data'] = $this->general_model->getRecords('*', 'category', array(
                'category_id'   => $id,
                'delete_status' => 0 ));
        $this->load->view('category/edit', $data);
    }

    public function edit_category_modal()
    {
        $category_module_id  = $this->config->item('category_module');
        $data['module_id']   = $category_module_id;
        $modules             = $this->modules;
        $privilege           = "edit_privilege";
        $data['privilege']   = "edit_privilege";
        $section_modules     = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id  = $this->input->post('id');
        $this->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->edit($id);
        }else {
             $data  = $this->general_model->getRecords('count(*) as num_category,category_type', 'category', array(
                'delete_status' => 0,
                'category_name' => trim($this->input->post('category_name')),
                // 'category_type' => trim($this->input->post('category_type')),
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'category_id!=' => $id  ));
            if($data[0]->num_category == 0) {
                $category_data = array(
                        "category_name"   => trim($this->input->post('category_name')),
                        "category_type"   => trim($this->input->post('category_type')),
                        "updated_date"    => date('Y-m-d'),
                        "updated_user_id" => $this->session->userdata('SESS_USER_ID') );
                if ($this->general_model->updateData('category', $category_data, array(
                                'category_id' => $id ))) {
                    $result['flag'] = true;
                    $result['msg'] = 'Category Updated successfully';
                    /*$successMsg = 'Category Updated successfully';
                    $this->session->set_flashdata('category_success',$successMsg);*/
                    $log_data = array(
                            'user_id'           => $this->session->userdata('SESS_USER_ID'),
                            'table_id'          => $id,
                            'table_name'        => 'category',
                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                            'message'           => 'Category Updated' );
                    $this->general_model->insertData('log', $log_data);
                }else{
                    $result['flag'] = false;
                    $result['msg'] = 'Category Update Unsuccessful';
                    /*$errorMsg = 'Category Update Unsuccessful';
                    $this->session->set_flashdata('category_error',$errorMsg);*/
                    $this->session->set_flashdata('fail', 'Category can not be Updated.');
                }
            }else{
                $result['flag'] = false;
                $result['duplicate']='duplicate'; 
            }
        } 
        echo json_encode($result);
    }

    public function delete()
    {
        $category_module_id  = $this->config->item('category_module');
        $data['module_id']   = $category_module_id;
        $modules             = $this->modules;
        $privilege           = "delete_privilege";
        $data['privilege']   = "delete_privilege";
        $section_modules     = $this->get_section_modules($category_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id  = $this->input->post('delete_id');
        $id  = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('category', array(
                'delete_status' => 1 ), array(
                'category_id' => $id )))
        {
            $successMsg = 'Category Deleted successfully';
            $this->session->set_flashdata('category_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'category',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Category Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('category');
        }
        else
        {
            $errorMsg = 'Category Delete Unsuccessful';
            $this->session->set_flashdata('category_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Category can not be Deleted.');
            redirect("category", 'refresh');
        }
    }

}

