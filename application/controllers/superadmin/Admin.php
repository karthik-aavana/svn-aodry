<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model(['general_model','Ion_auth_model','default_model']);
        $this->load->helper('image_upload_helper');
	}

	public function index(){
		$data['country'] = $this->general_model->getRecords("*","countries");
		$this->load->view("super_admin/add",$data);
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

		$this->load->view("super_admin/create_user",$data);

	}

	

	public function users_list(){

		$data["data"] = $this->general_model->getJoinRecords("users.*,branch.*","users",['users.delete_status' => 0],["branch"=>"users.branch_id = branch.branch_id"]);
		// print_r($data["data"] );die;
		$this->load->view("super_admin/list",$data);

	}

	public function edit_user($id){

		$data["id"] = $id;	
		$data['group'] = $this->general_model->getRecords("*","groups",["delete_status"=>0]);	
		$data["data"] = $this->general_model->getJoinRecords("users.*,branch.*","users",['users.delete_status' => 0,"users.id" => $id],["branch"=>"users.branch_id = branch.branch_id"]);

		// print_r($data['data']);die;

		$this->load->view("super_admin/edit_user",$data);

	}

	public function edit_users(){

		$pwd = $this->input->post("password");
		$grp = $this->input->post("groups");
		// $privilages = implode(",", $grp);
		$password = $this->Ion_auth_model->hash_password($pwd);

			$id = $this->input->post("id");
		$update = array(
				"username" => $this->input->post("user_name"),
				"email" => $this->input->post("email_id"),
				"first_name" => $this->input->post("user_name"),
				"last_name" => $this->input->post("last_name"),
				"phone" => $this->input->post("user_no"),
				"username" => $this->input->post("user_name"),
				"password" =>$password,
				// "previlages" => $privilages 
		);
			if($this->general_model->updateData('users',$update, array('id'=>$id))){
				redirect("superadmin/admin/users_list");
			}
	}

	public function list_branch(){

			$data["data"] = $this->general_model->getRecords("branch.*","branch",["delete_status"=>0]);
			print_r($data["data"]);

	}


	public function default_sql(){

			$data = $this->default_model->insertDefaultRecords();
			
			redirect("superadmin/auth/dashboard");
	}
		
}