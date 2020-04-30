<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends MY_Controller
{
	function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index()
    {
    	$groups_module_id                   = $this->config->item('groups_module');
        $data['groups_module_id']           = $groups_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($groups_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'action',
                    1 => 'group_name',
                    2 => 'description');	
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->user_group_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                	$group_id                    = $this->encryption_url->encode($post->id);
                    $nestedData['group_name']         = $post->name;
                    $nestedData['description'] = $post->description;
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
				   	$cols.= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_user_group_modal"><a data-id="' . $group_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_groups btn btn-app"><i class="fa fa-pencil"></i></a></span>';
				   	$cols.= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $group_id . '" data-path="groups/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';                  
				   	$cols .= '</div></div>';
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';	
                   $send_data[]               = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }else{
        	$this->load->view('user_groups/list', $data);
        }
        
    }

    public function add_user_group()
    {
        $groups_module_id                   = $this->config->item('groups_module');
        $data['groups_module_id']           = $groups_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($groups_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $group_name = trim($this->input->post('group_name'));
        $data=array_merge($data,$section_modules);
        $data  = $this->general_model->getRecords('count(*) as group_count', 'groups', array(
            'delete_status' => 0,
            'name' => $group_name,
            'branch_id' => $this->session->userdata("SESS_BRANCH_ID")));
        $result = array();
        if($data[0]->group_count == 0){
            $group_data                        = array(
                    "name"        => trim($this->input->post('group_name')),
                    "description"       => trim($this->input->post('description')),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    "added_date"      => date('Y-m-d'),
                    "added_user_id"   => $this->session->userdata("SESS_USER_ID"));
            if($id = $this->general_model->insertData("groups", $group_data)){
            	$log_data                        = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'groups',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'User Group Inserted' );
            	$result['flag'] = true;
                $result['msg'] = 'User Group Added Successfully';
            }else{
	        	$result['flag'] = false;
	            $result['msg'] = 'User Group Add UnSuccessfully';
        	}    
        }else{
        	$result['resl'] = 'duplicate';
        }
        echo json_encode($result);
    }
    public function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $groups_module_id                   = $this->config->item('groups_module');
        $data['groups_module_id']           = $groups_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($groups_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data = $this->general_model->getRecords("*", "groups", [
                "delete_status" => 0,
                "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                "id"            => $id ]);
        echo json_encode($data);
    }
    public function update_user_group()
    {
        $groups_module_id                   = $this->config->item('groups_module');
        $data['groups_module_id']           = $groups_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($groups_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $id = trim($this->input->post('group_id'));
        $group_name = trim($this->input->post('group_name'));
        $data=array_merge($data,$section_modules);
        $data  = $this->general_model->getRecords('count(*) as group_count', 'groups', array(
            'delete_status' => 0,
            'name' => $group_name,
        	'id!='  => $id));
        $result = array();
        if($data[0]->group_count == 0){
            $group_data                        = array(
                    "name"        => trim($this->input->post('group_name')),
                    "description"       => trim($this->input->post('description')),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    "updated_date"      => date('Y-m-d'),
                    "updated_user_id"   => $this->session->userdata("SESS_USER_ID"));
            if($id = $this->general_model->updateData('groups', $group_data, array(
                        'id' => $id ))){
            	$log_data                        = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'groups',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'User Group Updated' );
            	$result['flag'] = true;
                $result['msg'] = 'User Group Updated Successfully';
            }else{
	        	$result['flag'] = false;
	            $result['msg'] = 'User Group Update Unsuccessfully';
        	}    
        }else{
        	$result['resl'] = 'duplicate';
        }
        echo json_encode($result);
    }
    public function delete()
    {
    	$groups_module_id                   = $this->config->item('groups_module');
        $data['groups_module_id']           = $groups_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($groups_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($id1 = $this->general_model->updateData('groups', ["delete_status" => 1 ], array('id' => $id ))) {
            $successMsg = 'User Group Deleted Successfully';
            $this->session->set_flashdata('group_user_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id1,
                    'table_name'        => 'groups',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'User Group Deleted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
            redirect("groups", 'refresh');
        }
        else{
            $errorMsg = 'User Group Delete Unsuccessful';
            $this->session->set_flashdata('group_user_error',$errorMsg);
            redirect("groups", 'refresh');
        }
    }
}