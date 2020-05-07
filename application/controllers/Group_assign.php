<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Group_assign extends MY_Controller
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
        $data['group_name'] = $this->general_model->getRecords("id,name", "groups", [
                "delete_status" => 0,
                "branch_id"         => $this->session->userdata("SESS_BRANCH_ID")
            	]);
        $this->load->view('group_assign/assign_user_groups', $data);
    }
    public function find_active_modules_group(){
    	$group_id = $this->input->post('group_id');
    	$groups_module_id                   = $this->config->item('groups_module');
        $data['groups_module_id']           = $groups_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($groups_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $list_data           = $this->common->assigned_module_list_field($group_id);
        $assigned_data       = $this->general_model->getPageJoinRecords($list_data);
        $list_data           = $this->common->active_module_list_field();
        $posts               = $this->general_model->getPageJoinRecords($list_data);
        /*$assigned_data = sizeof($assigned_data);*/
        $tr = '';
        if(empty($assigned_data)){     
	        if(!empty($posts)){
	        	foreach ($posts as $key => $value) {
                    $module_id = $value->module_id;
                    $module_name = $value->module_name;
                    $module_name_checkbox = "<input type='checkbox' class='reset-check' name='module' data-modulename='{$module_name}' data-id='id_{$module_id}' value='{$module_id}'>". '</br>';
                    if($value->is_report == 1){
                        $module_view_checkbox = "<input type='checkbox' class='reset-check' name='view' data-id='id_{$module_id}' value='{$module_id}'>". '</br>';
                        $module_add_checkbox = '';
                        $module_edit_checkbox = '';
                        $module_delete_checkbox = '';
                    }elseif($module_id == 37){
                        $module_name_checkbox = "<input type='checkbox' class='reset-check' name='module' data-modulename='{$module_name}' data-id='id_{$module_id}' value='{$module_id}' checked disabled>". '</br>';
                        $module_add_checkbox = "<input type='checkbox' class='reset-check' name='add' data-id='id_{$module_id}' value='{$module_id}' checked disabled>". '</br>';
                        $module_edit_checkbox = "<input type='checkbox' class='reset-check' name='edit' data-id='id_{$module_id}' value='{$module_id}' checked disabled>". '</br>';
                        $module_view_checkbox = "<input type='checkbox' class='reset-check' name='view' data-id='id_{$module_id}' value='{$module_id}' checked disabled>". '</br>';
                        $module_delete_checkbox = "<input type='checkbox' class='reset-check' name='delete' data-id='id_{$module_id}' value='{$module_id}' checked disabled>". '</br>';
                    }else{
                        $module_add_checkbox = "<input type='checkbox' class='reset-check' name='add' data-id='id_{$module_id}' value='{$module_id}'>". '</br>';
                        $module_edit_checkbox = "<input type='checkbox' class='reset-check' name='edit' data-id='id_{$module_id}' value='{$module_id}'>". '</br>';
                        $module_view_checkbox = "<input type='checkbox' class='reset-check' name='view' data-id='id_{$module_id}' value='{$module_id}'>". '</br>';
                        $module_delete_checkbox = "<input type='checkbox' class='reset-check' name='delete' data-id='id_{$module_id}' value='{$module_id}'>". '</br>';
                    }
                    $tr .=  '<tr class="main_tr" data-id="'.$module_id.'">';
                    $tr .= "<td>{$module_name_checkbox}</td>";
                    $tr .= "<td>{$module_name}</td>";
                    $tr .= "<td>{$module_view_checkbox}</td>";
                    $tr .= "<td>{$module_add_checkbox}</td>";
                    $tr .= "<td>{$module_edit_checkbox}</td>";
                    $tr .= "<td>{$module_delete_checkbox}</td></tr>";
			    }
	        }
        }
        else{
        	/*$assigned_module_id = array_column($assigned_data, 'module_id');*/
            foreach ($assigned_data as $key => $value) {
                $assigned_module_id[] = $value->module_id;
            }
        	$assigned_data_key = array();
        	foreach ($assigned_data as $value_assign) {
     			$assigned_data_key[$value_assign->module_id]['add'] = $value_assign->add_privilege;
     			$assigned_data_key[$value_assign->module_id]['edit'] = $value_assign->edit_privilege;
     			$assigned_data_key[$value_assign->module_id]['view'] = $value_assign->view_privilege;
     			$assigned_data_key[$value_assign->module_id]['delete'] = $value_assign->delete_privilege;
        	}

        	foreach ($posts as $key => $value_post) {

        		$module_id = $value_post->module_id;
        		$module_name = $value_post->module_name;
        		$modal_check = '';
        		$add_check = '';
        		$edit_check = '';
        		$delete_check = '';
        		$view_check = '';
                $static_privilege = '';
        		if(in_array($module_id, $assigned_module_id)){
        			$modal_check = 'checked';
	        		if($assigned_data_key[$module_id]['add'] == 1){
	        			$add_check = 'checked';
	        		}
	        		if($assigned_data_key[$module_id]['edit'] == 1){
	        			$edit_check = 'checked';
	        		}
	        		if($assigned_data_key[$module_id]['delete'] == 1){
	        			$delete_check = 'checked';
	        		}
	        		if($assigned_data_key[$module_id]['view'] == 1){
	        			$view_check = 'checked';
	        		}
        		}
                if($module_id == 37){
                    $static_privilege = 'disabled';
                    $modal_check = 'checked';
                    $add_check = 'checked';
                    $edit_check = 'checked';
                    $delete_check = 'checked';
                    $view_check = 'checked';
                }
                $module_name_checkbox = "<input type='checkbox' class='reset-check' name='module' data-modulename='{$module_name}' data-id='id_{$module_id}' value='{$module_id}' {$modal_check} {$static_privilege}>". '</br>';
                if($value_post->is_report == 1){
                    $module_add_checkbox = '';
                    $module_edit_checkbox = '';
                    $module_delete_checkbox = '';
                    $module_view_checkbox = "<input type='checkbox' class='reset-check' name='view' data-id='id_{$module_id}' value='{$module_id}' {$view_check}>". '</br>';
                }else{
                    $module_add_checkbox = "<input type='checkbox' class='reset-check' name='add' data-id='id_{$module_id}' value='{$module_id}' {$add_check} {$static_privilege}>". '</br>';
                    $module_edit_checkbox = "<input type='checkbox' class='reset-check' name='edit' data-id='id_{$module_id}' value='{$module_id}' {$edit_check} {$static_privilege}>". '</br>';
                    $module_view_checkbox = "<input type='checkbox' class='reset-check' name='view' data-id='id_{$module_id}' value='{$module_id}' {$view_check} {$static_privilege}>". '</br>';
                    $module_delete_checkbox = "<input type='checkbox' class='reset-check' name='delete' data-id='id_{$module_id}' value='{$module_id}' {$delete_check} {$static_privilege}>".'</br>';
                } 
				
        		$tr .=  '<tr class="main_tr" data-id="'.$module_id.'">';
        		$tr .= "<td>{$module_name_checkbox}</td>";
		        $tr .= "<td>{$module_name}</td>";
		        $tr .= "<td>{$module_view_checkbox}</td>";
		        $tr .= "<td>{$module_add_checkbox}</td>";
		        $tr .= "<td>{$module_edit_checkbox}</td>";
		        $tr .= "<td>{$module_delete_checkbox}</td></tr>";

        	}
        }
        echo json_encode($tr);
    }
        
    public function add_active_modules_group(){

    	$groups_module_id                   = $this->config->item('groups_module');
        $data['groups_module_id']           = $groups_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($groups_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        /*print_r($_POST);*/
        $posts = $_POST['module_data'];
        $group_id = $_POST['group_id'];
        $result = array();
        if(!empty($posts)){
        	$i = 0;
        	$this->db->where('group_id',$group_id);
            $update_id = $this->db->delete('group_accessibility');
        		$log_data                        = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $update_id,
                    'table_name'        => 'group_accessibility',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Assigned Modules Deleted Successfully');
            	$this->general_model->insertData('log', $log_data);
        	foreach ($posts as $key => $value) {
        		$group_data[$i]['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
			    $group_data[$i]['module_id'] = $value['module_id'];
			    $group_data[$i]['group_id'] = $group_id;
			    $group_data[$i]['add_privilege'] = $value['add'];
			    $group_data[$i]['edit_privilege'] = $value['edit'];
			    $group_data[$i]['delete_privilege'] = $value['delete'];
			    $group_data[$i]['view_privilege'] = $value['view'];
			    $group_data[$i]['delete_status'] = 0;
			    $group_data[$i]['added_user_id'] = $this->session->userdata("SESS_USER_ID");
			    $group_data[$i]['added_date'] = date('Y-m-d');
			    $i++;
        	}
        	if($id = $this->db->insert_batch('group_accessibility', $group_data)){
        		$result['flag'] = true;
        		$result['msg'] = 'Modules Assigned Successfully';
        		$log_data                        = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'group_accessibility',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Modules Assigned Successfully');
            	$this->general_model->insertData('log', $log_data);
        	}else{
        		$result['flag'] = false;
                $result['msg'] = 'Modules Assigned Unsuccessfully';
        	}    	
        }else{
        	$result['flag'] = false;
            $result['msg'] = 'Select Atleast One Module';
        }
        echo json_encode($result);
    }
}