<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modules extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model(['general_model','Ion_auth_model']);
        $this->load->helper('image_upload_helper');
	}

	function index(){

		$data["data"] = $this->general_model->getJoinRecords("users.*,branch.*,firm.*","users",['users.delete_status' => 0],["branch"=>"users.branch_id = branch.branch_id","firm" => "branch.firm_id = firm.firm_id"]);
		// print_r($data["data"]);die; 

		$this->load->view("super_admin/modules/list",$data);
		// echo count($data["data"]);
	}

	// function add($id){

	// 	 $id;
	// 	$branch_id = $this->uri->segment(5);

	// 	$string = "user_accessibility.*,modules.module_name";
	// 	$table ="user_accessibility";
	// 	$join = ["modules" => "modules.module_id = user_accessibility.module_id"];
	// 	$where = ["user_accessibility.user_id" => $id,"user_accessibility.branch_id" => $branch_id,"user_accessibility.delete_status" => 0];

	// 	$data['privilage'] = $this->general_model->getJoinRecords($string,$table,$where,$join);
	// 	// print_r($data['privilage']);
	// 	$module_where = ["active_modules.branch_id" => $branch_id ];
	// 	$moduel_join = ["active_modules" => "active_modules.module_id = modules.module_id"] ;
	// 	$data['modules'] = $this->general_model->getJoinRecords("modules.*","modules",$module_where,$moduel_join);
	// 	$this->load->view("super_admin/modules/add_new_previlages",$data);

	// }

	function add_privilege($id){
		$id = $this->encryption_url->decode($id);
		$branch_id = $this->uri->segment(5);
		$branch_id = $this->encryption_url->decode($branch_id);

		$string = "user_accessibility.*,modules.module_name";
		$table ="user_accessibility";
		$join = ["modules" => "modules.module_id = user_accessibility.module_id"];
		$where = ["user_accessibility.user_id" => $id,"user_accessibility.branch_id" => $branch_id,"user_accessibility.delete_status" => 0];

		$data['active_modules'] = $this->general_model->getJoinRecords($string,$table,$where,$join);
			// print_r($data['privilage']);die;
	
		$data['modules'] = $this->general_model->getActiveRemianingModules($id,$branch_id);

		// echo "<pre>";
		// print_r($data['modules']);
		// exit;

		$this->load->view("super_admin/modules/add_new_previlages",$data);
	}


	function assign_modules(){

		$data['modules'] = $this->general_model->getRecords("*","modules");
		$this->load->view("super_admin/modules/assign_modules",$data);
	}

	function add_modules(){	
		
		$val = $this->input->post();

		$edit = "";
		$view = "";
		$delete = "";

		foreach ($val as $key => $value) {
			echo "<pre>";
			print_r($value);
			echo "</pre>";
			$yes =null;
			if(is_array($value)){
				echo $value[0];	
				if($value[1]=="delete"){
					$delete = "yes";
					$data =array("view"=>"no","edit" => "no","delete" => "yes");
					print_r($data);
				}

				if ($value[1]=="edit" && isset($value[2])=="delete"	) {
					
					$data =array("view"=>"no","edit" => "yes","delete" => "yes");
					print_r($data);
				}
				if ($value[1]=="view" && isset($value[2])=="edit" && isset($value[3])=="delete") {
					
					$data =array("view"=>"yes","edit" => "yes","delete" => "yes");
					print_r($data);
				}

				if ($value[1]=="view" && isset($value[2])=="" && isset($value[3])=="") {
					
					$data =array("view"=>"yes","edit" => "no","delete" => "no");
					print_r($data);
				}

				if ($value[1]=="view" && isset($value[2])=="edit" && isset($value[3])=="") {
					
					$data =array("view"=>"yes","edit" => "yes","delete" => "no");
					print_r($data);
				}
			}
		}
	}


	function assign_modules_single(){

		$data['modules'] = $this->general_model->getRecords("*","modules");
		$this->load->view("super_admin/modules/assign_modules_single",$data);

	}

	function post_user_access(){
		
		// print_r($this->input->post());
		

		$access_array = array(

			"branch_id" => (int)$this->input->post('batch_id'),
			"user_id" => (int)$this->input->post('user_id'),
			"add_privilege" => $this->input->post('add'),
			"edit_privilege" => $this->input->post('edit'),	
			"delete_privilege" => $this->input->post('del'),	
			"view_privilege" => $this->input->post('view'),	
			"module_id" => $this->input->post('module_id'),
		);
		
		$id =  $this->general_model->insertData('user_accessibility',$access_array);
		 echo "<tr id='row".$id."'>";
		 echo "<td>".$this->input->post('module_name')."</td>";
		 echo "<td>".$this->input->post('add')."</td>";
		 echo "<td>".$this->input->post('edit')."</td>";
		 echo "<td>".$this->input->post('view')."</td>";
		 echo "<td>".$this->input->post('del')."</td>";
		 echo "<td><a data-toggle='modal' data-id= '".$id."' title ='delete' class = 'delete_record btn btn-xs btn-danger'><span class = 'glyphicon glyphicon-trash'></span></a>&nbsp </td>";
		 echo "</tr>";
	}

	function active_module_delete($id)
	{
		// echo $id;
		if($this->general_model->updateData("active_modules",["delete_status" => 1],["active_id"=>$id])){
			echo "success";
		}
	}

	function add_active_sub_module()
	{
		$branch_id=$this->input->post('branch_id');
		$module_id=$this->input->post('module_id');
		$sub_module_id=$this->input->post('sub_module_id');
		$active_sub_modules_data = array(
										'branch_id' => trim($branch_id),
										'module_id' => trim($module_id),
										'sub_module_id' => trim($sub_module_id)
									);
		if($this->general_model->insertData("active_sub_modules",$active_sub_modules_data))
		{
			if($branch_id && $module_id)
			{
				$data['success']='1';
				$data['sub_modules'] = $this->general_model->getRemainingSubModules($branch_id,$module_id);
				$string = "active_sub_modules.*,sub_modules.sub_module_name";
				$table ="active_sub_modules";
				$join = ["sub_modules" => "sub_modules.sub_module_id = active_sub_modules.sub_module_id"];
				$where = ["active_sub_modules.branch_id" => $branch_id,"active_sub_modules.module_id" => $module_id,"active_sub_modules.delete_status" => 0,"sub_modules.delete_status" => 0];
				$data['active_sub_modules'] = $this->general_model->getJoinRecords($string,$table,$where,$join);
			}		
			else
			{
				$data['success']='0';
			}
			echo json_encode($data);
		}
	}

	function active_sub_module_delete($id)
	{
		if($this->general_model->updateData("active_sub_modules",["delete_status" => 1],["active_sub_module_id"=>$id]))
		{
			$branch_id=$this->input->post('branch_id');
			$module_id=$this->input->post('module_id');
			if($branch_id && $module_id)
			{
				$data['success']='1';
				$data['sub_modules'] = $this->general_model->getRemainingSubModules($branch_id,$module_id);
				$string = "active_sub_modules.*,sub_modules.sub_module_name";
				$table ="active_sub_modules";
				$join = ["sub_modules" => "sub_modules.sub_module_id = active_sub_modules.sub_module_id"];
				$where = ["active_sub_modules.branch_id" => $branch_id,"active_sub_modules.module_id" => $module_id,"active_sub_modules.delete_status" => 0,"sub_modules.delete_status" => 0];
				$data['active_sub_modules'] = $this->general_model->getJoinRecords($string,$table,$where,$join);
			}		
			else
			{
				$data['success']='0';
			}
			echo json_encode($data);
		}
	}

	function privilege_delete($id){

		// echo $id;
		$result = array();
		if($this->general_model->updateData("user_accessibility",["delete_status" => 1],["accessibility_id"=>$id])){
			$successMsg = 'Module Deleted Successfully';
			$this->session->set_flashdata('module_delete_success',$successMsg);
			echo "sucess";
		}
	}

	function branch(){

		$string = "firm.*,branch.*";
		$from = "branch";
		$join = ["firm" => "firm.firm_id = branch.firm_id"];
		$where = ["firm.delete_status" =>0];

		$data['branch_list'] = $this->general_model->getJoinRecords($string,$from,$where,$join);
		$this->load->view("super_admin/modules/branch_list",$data);

	}

	function assign_modules_to_branch($id){
		$id = $this->encryption_url->decode($id);

		$string = "active_modules.*,modules.module_name";
		$table ="active_modules";
		$join = ["modules" => "modules.module_id = active_modules.module_id"];
		$where = ["active_modules.branch_id" => $id,"active_modules.delete_status" => 0,"modules.delete_status" => 0];
		$data['active_modules'] = $this->general_model->getJoinRecords($string,$table,$where,$join);
			// print_r($data['privilage']);die;
	

		$data['modules'] = $this->general_model->getRemianingModules($id);


		// $data['modules'] = $this->general_model->getRecords("*","modules");		
		$this->load->view("super_admin/modules/assign_branch_module",$data);			
	}

	function assign_submodules_to_branch($id)
	{
		$id = $this->encryption_url->decode($id);

		$data['modules'] = $this->general_model->getActiveModules($id);
		$this->load->view("super_admin/modules/assign_branch_submodule",$data);			
	}

	function get_sub_modules()
	{
		$branch_id=$this->input->post('branch_id');
		$module_id=$this->input->post('module_id');
		if($branch_id && $module_id)
		{
			$data['success']='1';
			$data['sub_modules'] = $this->general_model->getRemainingSubModules($branch_id,$module_id);
			$string = "active_sub_modules.*,sub_modules.sub_module_name";
			$table ="active_sub_modules";
			$join = ["sub_modules" => "sub_modules.sub_module_id = active_sub_modules.sub_module_id"];
			$where = ["active_sub_modules.branch_id" => $branch_id,"active_sub_modules.module_id" => $module_id,"active_sub_modules.delete_status" => 0,"sub_modules.delete_status" => 0];
			$data['active_sub_modules'] = $this->general_model->getJoinRecords($string,$table,$where,$join);
		}		
		else
		{
			$data['success']='0';
		}
		echo json_encode($data);
	}

	function post_branch_access(){

		 $modules = $this->input->post('module_id');
		 $batch_id = $this->input->post('batch_id');

		foreach($modules as $moduel ){
			$this->general_model->insertData('active_modules',['module_id' => $moduel,'branch_id'=> $batch_id]);
		}
		$join = ["modules" => "modules.module_id = active_modules.module_id"];
		$table_data =  $this->general_model->getJoinRecords("modules.*,active_modules.*","active_modules",['modules.delete_status' => 0,'active_modules.branch_id' =>$batch_id],$join);
			// print_r($table_data);die;
			echo "<table  id='index' class='table table-bordered table-striped table-hover table-responsive'>";
			echo "<thead>
                  <tr>
                    <th>Module</th>
                    <th>Action</th>
                  </tr>
                </thead>";

			 echo "<tbody>";
			foreach($table_data as $table){
			 echo "<tr id='row".$table->active_id."'>";
			 echo "<td>".$table->module_name."</td>";
			 echo "<td><a data-toggle='modal' data-id= '".$table->active_id."' title ='delete' class = 'delete_record btn btn-xs btn-danger'><span class = 'glyphicon glyphicon-trash'></span></a>&nbsp </td>";
			 echo "</tr>";

	}
			 echo "</tbody>";
			 echo "</table>";	

}

}	

