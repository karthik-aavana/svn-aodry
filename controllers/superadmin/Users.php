<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller 
{
	function __construct(){
		parent::__construct();
        $this->load->model(['general_model','Ion_auth_model']);
        $this->load->helper('image_upload_helper');
        $this->load->library(array('ion_auth', 'form_validation'));
	}

	function index(){

		$data["data"] = $this->general_model->getJoinRecords("users.*,branch.*,firm.*","users",['users.delete_status' => 0],["branch"=>"users.branch_id = branch.branch_id", "firm"=> "firm.firm_id = branch.firm_id"]);
		$this->load->view("super_admin/users/list",$data);

	}

	function add(){
		$data['groups'] = $this->general_model->getRecords("*","groups",["delete_status"=>0]);
		// print_r($data['group']);die;
		
		$string = "firm.*,branch.*";
		$from = "branch";
		$join = ["firm" => "firm.firm_id = branch.firm_id"];
		$where = ["firm.delete_status" =>0];
		$order = array();
		$group = array('branch.firm_id');
		$data['firm'] = $this->general_model->getJoinRecords($string,$from,$where,$join,$order,$group);
		$this->load->view("super_admin/users/add",$data);
	}

	function add_user(){
  		$email = strtolower($this->input->post('email'));
		$identity = $email;
		$password = $this->input->post('password');

		$additional_data = array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'company' => $this->input->post('company'),
			'phone' => $this->input->post('phone'),
		);	
		$group = $this->input->post("group");
		$branch_id=$this->input->post('branch_id');
		$user_id = $this->ion_auth->register($branch_id,$identity,$password,$email,$additional_data);
		
		$group = $this->input->post("group");

		$this->general_model->insertData("users_groups",["user_id" => $user_id,"group_id" =>$group]);

		$active_modules = array();

		$modules=$this->sa_get_modules($user_id,$branch_id);
		foreach ($modules['modules'] as $key => $value){
			if(!in_array($value, $active_modules) && $value != ""){
			 	$active_modules[]=$value->module_id;
			}
		}

		$modules_assigned_section=$this->config->item('modules_assigned_section');
		$add_modules_assigned_section=$this->config->item('add_modules_assigned_section');
		$edit_modules_assigned_section=$this->config->item('edit_modules_assigned_section');
		$delete_modules_assigned_section=$this->config->item('delete_modules_assigned_section');
		$view_modules_assigned_section=$this->config->item('view_modules_assigned_section');


    	if($group==$this->config->item('admin_group')){
    		$module_section=$modules_assigned_section['admin'];
    		$add_section=$add_modules_assigned_section['admin'];
    		$edit_section=$edit_modules_assigned_section['admin'];
    		$delete_section=$delete_modules_assigned_section['admin'];
    		$view_section=$view_modules_assigned_section['admin'];
    	}

    	if($group==$this->config->item('members_group')){
    		$module_section=$modules_assigned_section['members'];
    		$add_section=array();
    		$edit_section=array();
    		$delete_section=array();
    		$view_section=$view_modules_assigned_section['members'];
    	}

    	if($group==$this->config->item('purchaser_group')){
    		$module_section=$modules_assigned_section['purchaser'];
    		$add_section=$add_modules_assigned_section['purchaser'];
    		$edit_section=$edit_modules_assigned_section['purchaser'];
    		$delete_section=array();
    		$view_section=$view_modules_assigned_section['purchaser'];
    	}

    	if($group==$this->config->item('sales_person_group')){
    		$module_section=$modules_assigned_section['sales_person'];
    		$add_section=$add_modules_assigned_section['sales_person'];
    		$edit_section=$edit_modules_assigned_section['sales_person'];
    		$delete_section=array();
    		$view_section=$view_modules_assigned_section['sales_person'];
    	}

    	if($group==$this->config->item('manager_group')){
    		$module_section=$modules_assigned_section['manager'];
    		$add_section=$add_modules_assigned_section['manager'];
    		$edit_section=$edit_modules_assigned_section['manager'];
    		$delete_section=$delete_modules_assigned_section['manager'];
    		$view_section=$view_modules_assigned_section['manager'];
    	}

    	if($group==$this->config->item('accountant_group')){
    		$module_section=$modules_assigned_section['accountant'];
    		$add_section=$add_modules_assigned_section['accountant'];
    		$edit_section=$edit_modules_assigned_section['accountant'];
    		$delete_section=$delete_modules_assigned_section['accountant'];
    		$view_section=$view_modules_assigned_section['accountant'];
    	}
	
		$data_item = array();
       	foreach ($active_modules as $key => $value){
       		$data_item[$key]['branch_id']=$this->session->userdata('SESS_BRANCH_ID');
       		$data_item[$key]['user_id']=$user_id;
       		$data_item[$key]['module_id']=$value;
       		$data_item[$key]['add_privilege']="yes";
       		$data_item[$key]['edit_privilege']="yes";
       		$data_item[$key]['delete_privilege']="yes";
       		$data_item[$key]['view_privilege']="yes";

       		/*if(in_array($value, $add_section)){
       			$data_item[$key]['add_privilege']="yes";
       		}else{
       			$data_item[$key]['add_privilege']="no";
       		}

       		if(in_array($value, $edit_section)){
       			$data_item[$key]['edit_privilege']="yes";
       		}else{
       			$data_item[$key]['edit_privilege']="no";
       		}

       		if(in_array($value, $delete_section)){
       			$data_item[$key]['delete_privilege']="yes";
       		}else{
       			$data_item[$key]['delete_privilege']="no";
       		}

       		if(in_array($value, $add_section)){
       			$data_item[$key]['view_privilege']="yes";
       		}else{
       			$data_item[$key]['view_privilege']="no";
       		} */
       	}

        foreach ($data_item as $value){
          $this->general_model->insertData("user_accessibility",$value);
		}
		
		$modules = $this->general_model->getActiveRemianingModules($user_id,$branch_id);
		foreach($modules as $module ){
	            $module_id = $module->module_id;
				$access_array = array("branch_id" => $branch_id,
									  "user_id" => $user_id,
									   "add_privilege" => 'yes',
									   "edit_privilege" => 'yes',	
									    "delete_privilege" => 'yes',	
										"view_privilege" => 'yes',	
										"module_id" => $module_id,
									);

			$id =  $this->general_model->insertData('user_accessibility',$access_array);
		}
		redirect("superadmin/users");
	}

	function edit($id){
		$id = $this->encryption_url->decode($id);
		// $data['modules'] = $this->general_model->getJoinRecordss("*","modules");

		$data['users'] = $this->general_model->getRecords("*","users",['delete_status'=>0,"id" => $id]);
		$firm_id = $this->general_model->getRecords("firm_id","branch",['delete_status'=>0,"branch_id" => $data['users'][0]->branch_id]);
		$data['branch'] = $this->general_model->getRecords("*","branch",['delete_status'=>0,'branch_id'=>$data['users'][0]->branch_id]);
		$data['firm'] = $this->general_model->getRecords("*","firm",['delete_status'=>0,'firm_id'=>$firm_id[0]->firm_id]);
		
		
		// $string = "firm.*,branch.*";
		// $from = "branch";
		// $join = ["firm" => "firm.firm_id = branch.firm_id"];
		// $where = ["firm.delete_status" =>0];
		// $order = array();
		// $group = array('branch.firm_id');
		// $data['branch'] = $this->general_model->getJoinRecords($string,$from,$where,$join,$order,$group);

		$this->load->view("super_admin/users/edit",$data);
	}

	function edit_user()
	{
		$pwd = $this->input->post("password");

		$id = $this->input->post("branch_id");
		$branch = $this->general_model->getRecords("*","branch",['delete_status'=>0,'branch_id'=>$id]);
		$branch_code = $branch[0]->branch_code;

		$user_id = $this->input->post("id");

		$user_array = array(
			"branch_id" => $this->input->post("branch_id"),
			"branch_code" => $branch_code,
			"username" => $this->input->post("email"),
			"phone" => $this->input->post("phone"),
			"last_name" => $this->input->post("last_name"),
			"email" => $this->input->post("email"),
			"first_name" =>$this->input->post("first_name"),
			"ip_address" =>$_SERVER['REMOTE_ADDR'],
			"company" => $company_name
		);

		if($pwd!=null && $pwd!="")
		{
			$password = $this->Ion_auth_model->hash_password($pwd);
			$user_array['password'] = $password;
		}
		
		if($this->general_model->updateData("users",$user_array,["id" => $user_id])){
			redirect("superadmin/users");
		}
	}

	function delete()
	{
		// if($this->general_model->updateData("users",["delete_status" => 1],["id"=>$id])){
		// 	echo "sucess";
		// }
		
		$id=$this->input->post('delete_id');
        $id=$this->encryption_url->decode($id);
      
		if($this->general_model->updateData('users',array('delete_status'=>1),array('id'=>$id)))
		{
			redirect('superadmin/users','refresh');
		}
		else
		{
			$this->session->set_flashdata('fail', 'User can not be Deleted.');
			redirect("superadmin/users",'refresh');
		}
	}
}