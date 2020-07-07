<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Firm extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model(['general_model','Ion_auth_model']);
        $this->load->helper('image_upload_helper');
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->library('Mailer');
	}

	function index(){
		$data['firm_list'] = $this->general_model->getRecords("*","firm",["delete_status" =>0],['firm_id'=>'DESC']);
		// print_r($data['firm_list']);
		$this->load->view("super_admin/firm/list",$data);
	}

	public function add() {  

       	/* Country Details */
		$country_data=$this->common->country_field();    
		$data['country']= $this->general_model->getRecords($country_data['string'],$country_data['table'],$country_data['where']);
        $data['currency']= $this->currency_call();
		/* Country Details */

		$financial_data=$this->common->financial_year_field();    
		$data['financial_year']= $this->general_model->getRecords($financial_data['string'],$financial_data['table'],$financial_data['where']);

		/* Get Comapany CODE */
		$code_count = 1;
		$dt = $this->db->query('SELECT firm_id FROM firm ORDER BY firm_id DESC LIMIT 1');
		if(!empty($dt->result())){
			$resp = $dt->result();
			$code_count = $resp[0]->firm_id;
			$code_count = $code_count + 1;
		}
		if($code_count < 9){
			$code_count = '00'.$code_count;
		}elseif ($code_count < 99) {
			$code_count = '0'.$code_count;
		}
		$data['company_code'] = COMPANY_CODE.$code_count;

		/*$currency_data=$this->common->currency_field();    
		$data['currency']= $this->general_model->getRecords($currency_data['string'],$currency_data['table'],$currency_data['where']);*/
       
        $this->load->view("super_admin/firm/add",$data);

    }

	public function add_firm(){
        $fyp = $this->input->post('financial_year_password');
        $efyd =  $this->encryption->encrypt($fyp);
        // print_r($this->input->post());die;

        $id= $this->input->post('id');  

        if (isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"]!=""){
            $path_parts = pathinfo($_FILES["logo"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];

            // if (!is_dir('assets/branch_files/'.$this->session->userdata('SESS_BRANCH_ID'))) 
            // {
            //     mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            // }

            $url = "uploads/".$image_path;

            if (in_array($path_parts['extension'], array("jpg", "jpeg", "png"))) {
                if (is_uploaded_file($_FILES["logo"]["tmp_name"])) 
                {
                    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $url)) 
                    {
                        $image_name = $image_path;
                    }
                }
            }
        } else {
            $image_name = $this->input->post('hidden_logo_name');
        }

        if (isset($_FILES["import_export_code"]["name"]) && $_FILES["import_export_code"]["name"]!="")
        {
            $path_parts = pathinfo($_FILES["import_export_code"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];

            // if (!is_dir('assets/branch_files/'.$this->session->userdata('SESS_BRANCH_ID'))) 
            // {
            //     mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            // }

            $url = "uploads/".$image_path;

            if (in_array($path_parts['extension'], array("jpg", "jpeg", "png", "pdf", "doc", "docx", "xls", "xlsx"))) {
                if (is_uploaded_file($_FILES["import_export_code"]["tmp_name"])) 
                {
                    if (move_uploaded_file($_FILES["import_export_code"]["tmp_name"], $url)) 
                    {
                        $iec_name = $image_path;
                    }
                }
            }
        } else {
            $iec_name = $this->input->post('hidden_iec_name');
        }

        if (isset($_FILES["shop_establishment"]["name"]) && $_FILES["shop_establishment"]["name"]!="")
        {
            $path_parts = pathinfo($_FILES["shop_establishment"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];

            // if (!is_dir('assets/branch_files/'.$this->session->userdata('SESS_BRANCH_ID'))) 
            // {
            //     mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            // }

            $url = "uploads/".$image_path;

            if (in_array($path_parts['extension'], array("jpg", "jpeg", "png", "pdf", "doc", "docx", "xls", "xlsx"))) {
                if (is_uploaded_file($_FILES["shop_establishment"]["tmp_name"])) 
                {
                    if (move_uploaded_file($_FILES["shop_establishment"]["tmp_name"], $url)) 
                    {
                        $shop_name = $image_path;
                    }
                }
            }
        } else {
            $shop_name = $this->input->post('hidden_shop_name');
        }
     
        // $firm_id=$this->input->post('firm_id');
        // $branch_id=$this->input->post('branch_id');
        
        $firm_data = array("firm_name" => $this->input->post('name'),
                            "firm_short_name" => $this->input->post('short_name'),
                            "firm_registered_type" => $this->input->post('registered_type'),
                            "firm_logo" => $image_name,
                            "firm_company_code" => $this->input->post('company_code')
                        );
        if($firm_id=$this->general_model->insertData('firm',$firm_data)){
            // $log_data = array(
            //             'user_id' => $this->session->userdata('SESS_USER_ID'),
            //             'table_id' => $firm_id,
            //             'table_name' => 'firm',
            //             'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            //             'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            //             'message' => 'Firm Inserted'
            //         );                

            // $this->general_model->insertData('log',$log_data);
        	if(!empty($this->input->post('financial_year_id'))){
        		$finance_data = explode('-', $this->input->post('financial_year_id'));
        		$date_from = '01-04-'.$finance_data[0];
        		$date_to = '01-03-'.$finance_data[1];
        		$year_from_date = date('Y-m-d H:i:s',strtotime($date_from));
        		$date_to = date('Y-m-d H:i:s',strtotime($date_to));
        		$last_date = date('t',strtotime($date_to));
		        $year_to_date = $last_date.'-03-'.$finance_data[1];
		        $year_to_date = date('Y-m-d H:i:s',strtotime($year_to_date));

        	}
	        $branch_data = array(
	                            "firm_id" => $firm_id,
	                            "branch_name" => $this->input->post('name'),
	                            "branch_gstin_number" => $this->input->post('branch_gstin_number'),
	                            "branch_gst_registration_type" => $this->input->post('branch_gst_registration_type'),
	                            "branch_code" => $this->input->post('branch_code'),
	                            "branch_address" => $this->input->post('branch_address'),
	                            "branch_country_id" => $this->input->post('sa_country'),
	                            "branch_state_id" => $this->input->post('sa_state'),
	                            "branch_city_id" => $this->input->post('sa_city'),
	                            "branch_postal_code" => $this->input->post('branch_postal_code'),
	                            "branch_email_address" => $this->input->post('email'),
	                            "branch_mobile" => $this->input->post('mobile'),
	                            "branch_land_number" => $this->input->post('land_number'),
	                            "branch_pan_number" => $this->input->post('pan_number'),
	                            "branch_cin_number" => $this->input->post('cin_number'),
	                            "branch_roc" => $this->input->post('branch_roc'),
	                            "branch_esi" => $this->input->post('branch_esi'),
	                            "branch_pf" => $this->input->post('branch_pf'),
	                            "branch_tan_number" => $this->input->post('tan_number'),
	                            "branch_import_export_code" => $iec_name,
	                            "branch_shop_establishment" => $shop_name,
	                            "branch_others" => $this->input->post('others'),
	                            "added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
	                            "branch_default_currency" => $this->input->post('currency_id')
	                            );//"financial_year_id" => $this->input->post('financial_year_id')
	                            // print_r($branch_data);die;           


	        if($branch_id=$this->general_model->insertData('branch',$branch_data)){
                $group_data                        = array(
                    "name"        => 'admin',
                    "description"       => 'Admin have all the privileges',
                    "branch_id"         => $branch_id,
                    "added_date"      => date('Y-m-d'),
                    "added_user_id"   => '1');
                $group_id = $this->general_model->insertData("groups", $group_data);

	        	$warehouse_data = array(
					"warehouse_name" => $this->input->post("name"),
				 	"warehouse_address" => $this->input->post("branch_address"),
				 	"warehouse_country_id" => $this->input->post("sa_country"),
				 	"warehouse_state_id" => $this->input->post("sa_state"),
				 	"warehouse_city_id" => $this->input->post("sa_city"),
				 	"added_user_id" => '1',
				 	"added_date" => date("Y-m-d"),
				 	"branch_id" => $branch_id
				);
				$this->general_model->insertData('warehouse',$warehouse_data);

				$addAcc = array(
                            'branch_id' => $branch_id,
                            'from_date' => $year_from_date,
                            'to_date' => $year_to_date,
                            'is_current' => '1',
                            'created_ts' => date('Y-m-d H:i:s'),
                            'created_by' => '1'
                        );

				$default_year = array(
                            'branch_id' => $branch_id,
                            'is_default' => '1',
                            'created_ts' => date('Y-m-d H:i:s'),
                            'created_by' => '1'
                        );
				
                $this->general_model->insertData("tbl_financial_year", $default_year);
                $financial_year_id = $this->general_model->insertData("tbl_financial_year", $addAcc);
               
                $this->general_model->updateData('branch', array('financial_year_id'=>$financial_year_id), array('branch_id' => $branch_id ));
                $tax_split_percentage = 50;
                if($this->input->post('tax_split_percentage') != '') 
                    if($this->input->post('tax_split_percentage') <= 100 ) 
                    $tax_split_percentage = $this->input->post('tax_split_percentage');

	            $common_settings_data = array(
                                "tax_split_percentage" => $tax_split_percentage,
	                            "round_off_access" => $this->input->post('round_off_access'),
	                            "tax_split_equaly" => $this->input->post('tax_split_equaly'),
	                            "financial_year_password" => $efyd,
	                            'default_notification_date' => $this->input->post('default_notification_date'),
	                            "invoice_footer" => $this->input->post('invoice_footer'),
	                            "registered_type" => 'trial',
	                            "branch_id" => $branch_id
	                        );

	            $this->general_model->insertData('common_settings',$common_settings_data);

	            $this->default_ledger_group_entry($branch_id);
	            $this->default_discount_entry($branch_id);
                $this->default_transaction_purpose($branch_id);
	            $this->default_tax_entry($branch_id);
               
                $this->createOption_finance($financial_year_id,$branch_id);
	            $this->default_settings_entry($branch_id);
	            $this->default_active_sub_modules_entry($branch_id);

                $q = $this->db->select('m.*')->from('modules m')->where('m.delete_status', 0)->where('m.module_id IN (select module_id from active_modules where delete_status=0 and branch_id=' . $branch_id . ' )', NULL, FALSE)->get();
                $modules_added = $q->result();

                foreach($modules_added as $module ){
                    $module_id = $module->module_id;
                    $id_module_branch = $this->general_model->insertData('active_modules',['module_id' => $module_id,'branch_id'=> $branch_id]);
                    if($id_module_branch){
                        $sub_modules = $this->general_model->getRemainingSubModules($branch_id,$module_id);

                        foreach ($sub_modules as $sub_module) {
                            $sub_module_id = $sub_module->sub_module_id;
                            $active_sub_modules_data = array(
                                        'branch_id' => $branch_id,
                                        'module_id' => $module_id,
                                        'sub_module_id' => $sub_module_id
                                    );
                        $this->general_model->insertData("active_sub_modules",$active_sub_modules_data);
                        }
                        
                    }
                }
          

	            $modules = $this->general_model->getRemianingModules($branch_id);
	            foreach($modules as $module ){
	            	$module_id = $module->module_id;
					$id_module_branch = $this->general_model->insertData('active_modules',['module_id' => $module_id,'branch_id'=> $branch_id]);
					if($id_module_branch){
						$sub_modules = $this->general_model->getRemainingSubModules($branch_id,$module_id);

						foreach ($sub_modules as $sub_module) {
							$sub_module_id = $sub_module->sub_module_id;
							$active_sub_modules_data = array(
										'branch_id' => $branch_id,
										'module_id' => $module_id,
										'sub_module_id' => $sub_module_id
									);
						$this->general_model->insertData("active_sub_modules",$active_sub_modules_data);
						}
						
					}
				}
                $group_module = array_merge($modules_added, $modules);
                $data_item = array();
                foreach ($group_module as $key => $value) {
                    $data_item[$key]['branch_id'] = $branch_id;
                    $data_item[$key]['module_id'] = $value->module_id;
                    $data_item[$key]['group_id'] = $group_id;
                    if($value->is_report == 1){
                        $data_item[$key]['add_privilege'] = 0;
                        $data_item[$key]['edit_privilege'] = 0;
                        $data_item[$key]['delete_privilege'] = 0;
                        $data_item[$key]['view_privilege'] = 1;
                    }else{
                        $data_item[$key]['add_privilege'] = 1;
                        $data_item[$key]['edit_privilege'] = 1;
                        $data_item[$key]['delete_privilege'] = 1;
                        $data_item[$key]['view_privilege'] = 1;
                    }
                    $data_item[$key]['delete_status'] = 0;
                    $data_item[$key]['added_user_id'] = 1;
                    $data_item[$key]['added_date'] = date("Y-m-d");
                }
                foreach ($data_item as $value) {
                    $this->general_model->insertData("group_accessibility", $value);
                }
	            // $log_data = array(
	            //             'user_id' => $this->session->userdata('SESS_USER_ID'),
	            //             'table_id' => $branch_id,
	            //             'table_name' => 'branch',
	            //             'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
	            //             'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
	            //             'message' => 'Branch Updated'
	            //         );

	            // $this->session->set_userdata('SESS_FINANCIAL_YEAR_ID',$this->input->post('financial_year_id'));
	            // $this->session->set_userdata('SESS_DEFAULT_CURRENCY',$this->input->post('currency_id'));

	            // $this->general_model->insertData('log',$log_data);
	        }
        }

        if(null != $this->input->post('email')){
            $html =  $this->load->view('email_template/email_registration', '', TRUE);
            $html = str_ireplace('{{CUSTOMER}}', $this->input->post('name'), $html);
            $html = str_ireplace('{{USER_CODE}}', $this->input->post('branch_code'), $html);
            $html = str_ireplace('{{EMAIL}}', $this->input->post('email'), $html);
         
            $emailDataSet = array(                         
                                'subject' =>'Welcome to Aodry!!!',                    
                                'message' => $html,
                                'email'=>  $this->input->post('email'), 
                            );
              
            $is_send = $this->mailer->sendEmail($emailDataSet);
            /*$this->SendEmail($email);*/
        }

        $branch_id=$this->encryption_url->encode($branch_id);
	  	redirect("superadmin/modules/assign_modules_to_branch/".$branch_id,"refresh");
	}

    public function autoSignup(){
        /*$this->form_validation->set_rules('registered_type','', 'required');*/
        $this->form_validation->set_rules('name','', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('email','', 'trim|required|valid_email');
        /*$this->form_validation->set_rules('sa_country','', 'required');
        $this->form_validation->set_rules('sa_state','', 'required');
        $this->form_validation->set_rules('sa_city','', 'required');*/
        /*$this->form_validation->set_rules('branch_address','', 'required');*/
        $data = $this->input->post();
        if ($this->form_validation->run() === true) {
            $email = $this->input->post('email');
            $already_firm_email = $this->db->query("SELECT branch_id FROM branch WHERE branch_email_address='{$email}' AND delete_status=0 ");
            $already_user_email = $this->db->query("SELECT id FROM users WHERE email='{$email}' AND delete_status=0 ");

            if($already_firm_email->num_rows() < 1 && $already_user_email->num_rows() < 1){
                $remember = (bool) $this->input->post('remember');
                /* Get Comapany CODE */
                $code_count = 1;
                $dt = $this->db->query('SELECT firm_id FROM firm ORDER BY firm_id DESC LIMIT 1');
                if(!empty($dt->result())){
                    $resp = $dt->result();
                    $code_count = $resp[0]->firm_id;
                    $code_count = $code_count + 1;
                }
                if($code_count < 9){
                    $code_count = '00'.$code_count;
                }elseif ($code_count < 99) {
                    $code_count = '0'.$code_count;
                }

                $company_code = COMPANY_CODE.$code_count;
                $firm_data = array("firm_name" => $this->input->post('name'),
                                    "auto_register"=>1,
                                    "is_updated" => 0,
                                    "firm_short_name" => $this->input->post('short_name'),
                                    "firm_registered_type" => $this->input->post('registered_type'),
                                    "firm_logo" => '',
                                    "firm_company_code" => $company_code
                                );

                if($firm_id=$this->general_model->insertData('firm',$firm_data)){
                    /* Financial year */
                    $date_from = '01-04-'.date('Y');
                    $date_to = '01-03-'.date('Y', strtotime('+1 year'));
                    $year_from_date = date('Y-m-d H:i:s',strtotime($date_from));
                    $date_to = date('Y-m-d H:i:s',strtotime($date_to));
                    $last_date = date('t',strtotime($date_to));
                    $year_to_date = $last_date.'-03-'.date('Y', strtotime('+1 year'));
                    $year_to_date = date('Y-m-d H:i:s',strtotime($year_to_date));
                    /* End */
                    $branch_gst_registration_type = 'Unregistered';
                    if($this->input->post('branch_gstin_number')){
                        $branch_gst_registration_type = 'Registered';
                    }

                    $branch_data = array(
                                        "firm_id" => $firm_id,
                                        "branch_name" => $this->input->post('name'),
                                        /*"branch_gstin_number" => $this->input->post('branch_gstin_number'),*/
                                        /*"branch_gst_registration_type" => $branch_gst_registration_type,*/
                                        "branch_code" => $company_code,
                                        /*"branch_address" => $this->input->post('branch_address'),
                                        "branch_country_id" => $this->input->post('sa_country'),
                                        "branch_state_id" => $this->input->post('sa_state'),
                                        "branch_city_id" => $this->input->post('sa_city'),*/
                                        "branch_postal_code" => '',
                                        "branch_email_address" => $email,
                                        /*"branch_mobile" => $this->input->post('mobile'),*/
                                        "branch_land_number" => '',
                                        "branch_pan_number" => '',
                                        "branch_cin_number" => '',
                                        "branch_roc" => '',
                                        "branch_esi" => '',
                                        "branch_pf" => '',
                                        "branch_tan_number" => '',
                                        "branch_import_export_code" => '',
                                        "branch_shop_establishment" =>'',
                                        "branch_others" => '',
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" => 0,
                                        "branch_default_currency" => 75
                                    );

                    if($branch_id=$this->general_model->insertData('branch',$branch_data)){
                        $package_id=0;
                        $group_data                        = array(
                            "name"        => 'admin',
                            "description"       => 'Admin have all the privileges',
                            "branch_id"         => $branch_id,
                            "added_date"      => date('Y-m-d'),
                            "added_user_id"   => '1');
                        $group_id = $this->general_model->insertData("groups", $group_data);

                        if(@$this->input->post('payment')){
                            $payment = $this->input->post('payment');
                            if(strtolower($payment) == 'trial'){
                                $this->db->select("*");
                                $this->db->from("payment_methods");
                                $this->db->where('payment_method','Trial');
                                $re = $this->db->get();
                                $payment_qry = $re->row();
                                $days = (int)$payment_qry->valid_days;
                                $current_date = date('Y-m-d H:i:s');
                                $end_trial = date('Y-m-d H:i:s',strtotime($current_date.' + '.$days.' days'));
                                $package_id = $payment_qry->Id;
                                                   
                            }
                        }
                        /*$warehouse_data = array(
                            "warehouse_name" => $this->input->post("name"),
                            "warehouse_address" => $this->input->post("branch_address"),
                            "warehouse_country_id" => $this->input->post("sa_country"),
                            "warehouse_state_id" => $this->input->post("sa_state"),
                            "warehouse_city_id" => $this->input->post("sa_city"),
                            "added_user_id" => 0,
                            "added_date" => date("Y-m-d"),
                            "branch_id" => $branch_id
                        );
                        $this->general_model->insertData('warehouse',$warehouse_data);*/

                        $addAcc = array(
                                    'branch_id' => $branch_id,
                                    'from_date' => $year_from_date,
                                    'to_date' => $year_to_date,
                                    'is_current' => '1',
                                    'created_ts' => date('Y-m-d H:i:s'),
                                    'created_by' => '1'
                                );

                        $default_year = array(
                                    'branch_id' => $branch_id,
                                    'is_default' => '1',
                                    'created_ts' => date('Y-m-d H:i:s'),
                                    'created_by' => '1'
                                );
                        
                        $this->general_model->insertData("tbl_financial_year", $default_year);
                        $financial_year_id = $this->general_model->insertData("tbl_financial_year",$addAcc);
                       
                        $this->general_model->updateData('branch', array('financial_year_id'=>$financial_year_id), array('branch_id' => $branch_id ));
                        $tax_split_percentage = 50;
                        if($this->input->post('tax_split_percentage') != '') 
                            if($this->input->post('tax_split_percentage') <= 100 ) 
                            $tax_split_percentage = $this->input->post('tax_split_percentage');

                        $common_settings_data = array(
                                        "tax_split_percentage" => $tax_split_percentage,
                                        "round_off_access" => 'yes',
                                        "tax_split_equaly" => 'yes',
                                        "financial_year_password" => 'test',
                                        'default_notification_date' => 10,
                                        "invoice_footer" => '',
                                        "registered_type" => 'trial',
                                        "branch_id" => $branch_id
                                    );

                        $this->general_model->insertData('common_settings',$common_settings_data);

                        $this->default_ledger_group_entry($branch_id);
                        $this->default_discount_entry($branch_id);
                        $this->default_transaction_purpose($branch_id);
                        $this->default_tax_entry($branch_id);
                       
                        $this->createOption_finance($financial_year_id,$branch_id);
                        $this->default_settings_entry($branch_id);
                        $this->default_active_sub_modules_entry($branch_id);

                        $q = $this->db->select('m.*')->from('tbl_package_modules m')->where('m.package_id', $package_id)->get();

                        $modules = $q->result();
                        foreach($modules as $module ){
                            $module_id = $module->module_id;
                            $id_module_branch = $this->general_model->insertData('active_modules',['module_id' => $module_id,'branch_id'=> $branch_id]);
                            if($id_module_branch){
                                $sub_modules = $this->general_model->getRemainingSubModules($branch_id,$module_id);
                                $active_sub_modules_data = array();
                                foreach ($sub_modules as $sub_module) {
                                    $sub_module_id = $sub_module->sub_module_id;
                                    $active_sub_modules_data[] = array(
                                                'branch_id' => $branch_id,
                                                'module_id' => $module_id,
                                                'sub_module_id' => $sub_module_id
                                            );
                                    /*$this->general_model->insertData("active_sub_modules",$active_sub_modules_data);*/
                                }
                                $this->db->insert_batch("active_sub_modules", $active_sub_modules_data);
                            }
                        }
                        /* Add user */
                        $additional_data = array(
                            'first_name' => $this->input->post('name'),
                            'last_name' => '',
                            'company' => $this->input->post('name'),
                            'phone' => $this->input->post('mobile'),
                        );

                        $identity = $email = $this->input->post('email');
                        $password = '';
                        /*$group = 1; //Admin */
                        
                        $user_id = $this->ion_auth->register($branch_id,$identity,$password,$email,$additional_data);
                        
                        $this->general_model->insertData("users_groups",["user_id" => $user_id,"group_id" =>$group_id]);

                        $active_modules = array();
                        $modules=$this->sa_getOnly_modules($user_id,$branch_id);
                        /*foreach ($modules['modules'] as $key => $value){
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
                        }*/
                    
                        $data_item = array();
                        $data_item_group = array();
                        foreach ($modules['modules'] as $key => $value){
                            $data_item[$key]['branch_id']= $branch_id;
                            $data_item[$key]['user_id']=$user_id;
                            $data_item[$key]['module_id']=$value->module_id;
                            $data_item_group[$key]['branch_id'] = $branch_id;
                            $data_item_group[$key]['module_id'] = $value->module_id;
                            $data_item_group[$key]['group_id'] = $group_id;
                            if($value->is_report == 1){
                                $data_item_group[$key]['add_privilege'] = 0;
                                $data_item_group[$key]['edit_privilege'] = 0;
                                $data_item_group[$key]['delete_privilege'] = 0;
                                $data_item_group[$key]['view_privilege'] = 1;
                                $data_item[$key]['add_privilege']="no";
                                $data_item[$key]['edit_privilege']="no";
                                $data_item[$key]['delete_privilege']="no";
                                $data_item[$key]['view_privilege']="yes";
                            }else{
                                $data_item_group[$key]['add_privilege'] = 1;
                                $data_item_group[$key]['edit_privilege'] = 1;
                                $data_item_group[$key]['delete_privilege'] = 1;
                                $data_item_group[$key]['view_privilege'] = 1;
                                $data_item[$key]['add_privilege']="yes";
                                $data_item[$key]['edit_privilege']="yes";
                                $data_item[$key]['delete_privilege']="yes";
                                $data_item[$key]['view_privilege']="yes";
                            }
                            $data_item_group[$key]['delete_status'] = 0;
                            $data_item_group[$key]['added_user_id'] = 1;
                            $data_item_group[$key]['added_date'] = date("Y-m-d");
                        }

                        /*foreach ($data_item as $value){
                            $this->general_model->insertData(,$value);
                        }*/
                        $this->db->insert_batch("user_accessibility", $data_item);

                        $this->db->insert_batch("group_accessibility", $data_item_group);

                        $modules = $this->general_model->getActiveRemianingModules($user_id,$branch_id);
                        $access_array = array();
                        $access_array_group = array();

                        foreach($modules as $module ){
                            $module_id = $module->module_id;
                            $access_array[] = array("branch_id" => $branch_id,
                                                    "user_id" => $user_id,
                                                    "add_privilege" => 'yes',
                                                    "edit_privilege" => 'yes',   
                                                    "delete_privilege" => 'yes',    
                                                    "view_privilege" => 'yes',  
                                                    "module_id" => $module_id,
                                                );
                            $access_array_group[] = array("branch_id" => $branch_id,
                                                    "group_id" => $group_id,
                                                    "add_privilege" => 1,
                                                    "edit_privilege" => 1,   
                                                    "delete_privilege" => 1,    
                                                    "view_privilege" => 1,  
                                                    "module_id" => $module_id,
                                                    "added_user_id" => 1,
                                                    "added_date" => date("Y-m-d"),
                                                );
                            /*$id =  $this->general_model->insertData('user_accessibility',$access_array);*/
                        }
                        if(!empty($access_array)){
                            $this->db->insert_batch("user_accessibility", $access_array);
                        }
                        if(!empty($access_array_group)){
                           $this->db->insert_batch("group_accessibility", $access_array_group); 
                        }
                        if($package_id){
                            $payment = $this->input->post('payment');
                            if(strtolower($payment) == 'trial'){
                                $this->db->select("*");
                                $this->db->from("payment_methods");
                                $this->db->where('payment_method','Trial');
                                $re = $this->db->get();
                                $payment_qry = $re->row();
                                $days = (int)$payment_qry->valid_days;
                                $current_date = date('Y-m-d H:i:s');
                                $end_trial = date('Y-m-d H:i:s',strtotime($current_date.' + '.$days.' days'));
                                $package_id = $payment_qry->Id;
                                $payment = array(
                                    'firm_id' => $firm_id,
                                    'package' => $payment_qry->Id,
                                    'amount' => 0,
                                    'payment_status' => '1',
                                    'activation_date' => $current_date,
                                    'end_date' => $end_trial,
                                    'package_status' => '1',
                                    'is_updated' => '0',
                                    'added_date' => date('Y-m-d H:i:s'),
                                    'added_user_id' => $user_id,
                                );
                                $this->db->insert('tbl_billing_info',$payment);                   
                            }
                        }
                        /* Send */
                        $forgotten = $this->ion_auth->forgotten_password($identity, $company_code);
                        $forgot_code = $forgotten['forgotten_password_code'];

                        $html =  $this->load->view('email_template/password_setup', '', TRUE);
                        $message = "<a href = " . base_url('auth/verification/' . $forgot_code) . ">".base_url('auth/verification/' . $forgot_code)."</a>";
                        $html = str_replace('{{Password_Reset_link}}', $message, $html);
                        $html = str_replace('{{CUSTOMER}}', $this->input->post('name'), $html);
                        $html = str_replace('{{USER_CODE}}', $company_code, $html);
                        $html = str_replace('{{EMAIL}}', $identity, $html);
                        $emailDataSet = array(
                                            'subject' =>'Confidential Mail',                    
                                            'message' => $html,
                                            'email'=>  'chetna.b@aavana.in'//$identity, 
                                        );
                          
                        $is_send = $this->mailer->sendEmail($emailDataSet);
                        /* End user */
                        $log_data = array(
                                'user_id' => $user_id,
                                'table_id' => $firm_id,
                                'table_name' => 'firm',
                                'financial_year_id' => 0,
                                'branch_id' => $branch_id,
                                'message' => 'Auto Registration'
                            );
                        $this->general_model->insertData('log',$log_data);
                        echo $html;exit;
                        if($is_send){
                            $this->session->set_flashdata('message', 'Please check your email for verification and password setup!');
                        }else{
                            $this->session->set_flashdata('error_message', $this->ion_auth->errors());
                        }
                    }
                }else{
                    $this->session->set_flashdata('auto_data', $data);
                    $this->session->set_flashdata('error_message', 'Something went wrong!');
                }
            }else{
                $this->session->set_flashdata('auto_data', $data);
                $this->session->set_flashdata('error_message', 'Email ID already registered!');
            }
        }else{
            $this->session->set_flashdata('auto_data', $data);
            $this->session->set_flashdata('error_message', validation_errors());
        }
        redirect("auth/signup", 'refresh');
    }

    function SendEmail($data){
       
        $from_email = $this->general_model->getRecords('*', 'email_setup', array(
            'email_setup_id' => $data['from'],
            'delete_status' => 0,
            'added_user_id' => $this->session->userdata('SESS_USER_ID')));
       
        $to_email = explode(',',  $data['to']);
       
        $cc_email = explode(',',  $data['cc']);
        $message = str_replace(array(
            "\r\n",
            "\\r\\n"), "<br>",  $data['message']);
        
        require APPPATH . 'third_party/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "chetna.b@aavana.in";
        $mail->Password   = "ZXCVzxcv";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = '587';
        $mail->IsHTML(true);
        $mail->CharSet    = 'UTF-8';

       // $mail->IsHTML(true);
        $mail->setFrom('noreply@gmail.com',  'Aavana');
        $mail->addReplyTo('noreply@gmail.com', 'Aavana');
        $mail->addAddress($to_email[0]);

        $i = 0;
        foreach ($to_email as $value) {
            if ($i == 1) {
                $mail->addCC($value);
            }
            $i = 1;
        }
        /*foreach ($cc_email as $value) {
            $mail->addCC($value);
        }*/

        $mail->isHTML(true);
        $bodyContent = $message;
        $mail->Subject = $data['subject'];
        $mail->Body = $bodyContent;
        /*$mail->addAttachment($attachment_file);*/
       /* if (isset($_FILES["attachments"]["name"]) && $_FILES["attachments"]["name"] != "") {
            $file_tmp = $_FILES["attachments"]["tmp_name"];
            $file_name = $_FILES["attachments"]["name"];
            $mail->addAttachment($file_tmp, $file_name);
        }*/

        $resp = array();
        if (!$mail->send()) {
            $resp['flag'] = false; 
            $resp['msg'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            /*echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;*/
        } else {
            $resp['flag'] = true;
        }
        return $resp;
    }


	function default_ledger_group_entry($branch_id){
        
        $this->db->query("INSERT INTO branch_default_ledgers (default_ledger_id, main_group ,sub_group_1,sub_group_2,ledger_name,gst_payable,place_of_supply,module,branch_id) Select ledger_id, main_group ,sub_group_1,sub_group_2,ledger_name,gst_payable,place_of_supply,module,{$branch_id} from default_ledgers_cases ");
        
        $this->db->select('*');
        $qry = $this->db->get('tbl_default_group');
        $default_ry = $qry->result_array();
        $insert_dt = $insert_sub = $is_added = array();

        foreach ($default_ry as $key => $value) {
            $k = $value['main_group_id'].'_'.$value['primary_sub_group'];
            if(!array_key_exists($k , $is_added)){
                $insert_dt = array(
                                'branch_id' => $branch_id,
                                'sub_group_name_1' => $value['primary_sub_group'],
                                'sub_group_name_2' => $value['sec_sub_group'],
                                'main_grp_id' => $value['main_group_id'],
                                'group_status' => '1',
                                'is_editable' => '0',
                                'default_group_id' => $value['group_id'],
                                'created_ts' => date('Y-m-d H:i:s'),
                                'created_by' => '1',
                            );
                $this->db->insert('tbl_sub_group',$insert_dt);
                $ins_id = $this->db->insert_id();
                if($value['main_group_id'] == '1'){
                    $insert_sub = array(
                            /*'firm_id' => $firm_id,*/
                            'branch_id' => $branch_id,
                            'ledger_name' => 'Drawings',
                            'sub_group_id' => $ins_id,
                            'created_ts' => date('Y-m-d H:i:s'),
                            'created_by' => '1',
                        );
                }else{

                    $insert_sub = array(
                                /*'firm_id' => $firm_id,*/
                                'branch_id' => $branch_id,
                                'sub_group_id' => $ins_id,
                                'created_ts' => date('Y-m-d H:i:s'),
                                'created_by' => '1',
                            );
                }
                $this->db->insert('tbl_ledgers',$insert_sub);
                $is_added[$k] = $k;
            }

        }
		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Sundry Debtors','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Customer','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'CUSTOMER','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Customer','ledger_label'=>'Customer','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}
		
		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Sundry Creditors','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Supplier','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'SUPPLIER','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Supplier','ledger_label'=>'Supplier','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}
		
		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Cash-in-Hand','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Cash','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'CASH','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Cash','ledger_label'=>'Cash','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}
		
		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Sales Accounts','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Sales','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'SALES','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Sales','ledger_label'=>'Sales','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Indirect Expense','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Discount Given','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'DISCOUNT GIVEN','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Discount Given','ledger_label'=>'Discount Given','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}

			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Expense','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'EXPENSE','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Expense','ledger_label'=>'Expense','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'FREIGHT CHARGE GIVEN','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Freight Charge Given','ledger_label'=>'Freight Charge Given','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'INSURANCE CHARGE GIVEN','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Insurance Charge Given','ledger_label'=>'Insurance Charge Given','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'PACKING CHARGE GIVEN','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Packing Charge Given','ledger_label'=>'Packing Charge Given','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'INCIDENTAL CHARGE GIVEN','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Incidental Charge Given','ledger_label'=>'Incidental Charge Given','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'OTHER INCLUSIVE CHARGE GIVEN','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Other Inclusive Charge Given','ledger_label'=>'Other Inclusive Charge Given','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}

				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'OTHER EXCLUSIVE CHARGE GIVEN','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Other Exclusive Charge Given','ledger_label'=>'Other Exclusive Charge Given','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Duties & Taxes','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'GST','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'IGST','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'IGST','ledger_label'=>'IGST','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}

				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'CGST','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'CGST','ledger_label'=>'CGST','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}

				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'SGST','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'SGST','ledger_label'=>'SGST','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Fixed Assets','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Asset','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'ASSET','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Asset','ledger_label'=>'Asset','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Purchase Accounts','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Purchase','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'PURCHASE','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Purchase','ledger_label'=>'Purchase','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Indirect Income','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Discount Received','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'DISCOUNT Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Discount Received','ledger_label'=>'Discount Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}

			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Income','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'INCOME','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Income','ledger_label'=>'Income','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'FREIGHT CHARGE RECEIVED','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Freight Charge Received','ledger_label'=>'Freight Charge Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'INSURANCE CHARGE RECEIVED','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Insurance Charge Received','ledger_label'=>'Insurance Charge Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'PACKING CHARGE RECEIVED','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Packing Charge Received','ledger_label'=>'Packing Charge Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'INCIDENTAL CHARGE RECEIVED','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Incidental Charge Received','ledger_label'=>'Incidental Charge Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
				
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'OTHER INCLUSIVE CHARGE RECEIVED','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Other Inclusive Charge Received','ledger_label'=>'Other Inclusive Charge Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}

				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'OTHER EXCLUSIVE CHARGE RECEIVED','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Other Exclusive Charge Received','ledger_label'=>'Other Exclusive Charge Received','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}

			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Service','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'SERVICE','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Service','ledger_label'=>'Service','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}

			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Product','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'PRODUCT','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Product','ledger_label'=>'Product','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Bank Accounts','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Bank','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'BANK','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Bank','ledger_label'=>'Bank','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}

				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'OTHER PAYMENT MODE','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Other Payment Mode','ledger_label'=>'Other Payment Mode','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Current Liabilities','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'TDS','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'TDS','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'TDS','ledger_label'=>'TDS','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}
	}

	function default_discount_entry($branch_id)
	{
		/*$discount_data[]=array('discount_name'=>' Discount@0%',
								'discount_value'=>'0.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);*/
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'2.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'5.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'8.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'10.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'12.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'15.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'18.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'20.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'24.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$discount_data[]=array('discount_name'=>'Discount',
								'discount_value'=>'25.00',
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
							);
		$this->general_model->insertBatchData('discount',$discount_data);
	}

	function default_tax_entry($branch_id)
	{
		$this->db->select('*');
		$this->db->where('is_default','1');
		$this->db->where('delete_status',0);
        $qry = $this->db->get('tax_section');
        $default_ry = $qry->result_array();
        $tax_data = array();
        $tax_gst = array();

        foreach ($default_ry as $key => $value) {
           
                $tax_data[] = array(
                                'tax_name'=>$value['tax_name'],
                                'section_id' => $value['section_id'],
                                'tax_description' => $value['section_description'],
								'tax_value'=>$value['default_per'],
								"added_date" => date('Y-m-d'),
	                            "added_user_id" => '1',
								'branch_id'=>$branch_id
                            );
            
        }
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'0.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		/*$tax_gst[]=array('tax_name'=>'Exempted',
							'tax_value'=>'0.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);*/
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'5.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'9.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'12.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'18.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'20.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'24.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'28.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$tax_gst[]=array('tax_name'=>'GST',
							'tax_value'=>'30.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);
		$this->general_model->insertBatchData('tax',$tax_gst);
	}

	function default_settings_entry($branch_id){
		$settings_data[]=array('settings_invoice_first_prefix'=>'QT',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "pdf_settings" => '',
                            "module_id" => '1',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'SAL',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '2',
                            "pdf_settings" => json_encode(array (
                                                          'company_name' => 'yes',
                                                          'logo' => 'yes',
                                                          'address' => 'yes',
                                                          'country' => 'no',
                                                          'state' => 'yes',
                                                          'mobile' => 'yes',
                                                          'landline' => 'yes',
                                                          'email' => 'yes',
                                                          'gst' => 'yes',
                                                          'pan' => 'yes',
                                                          'iec' => 'yes',
                                                          'lut' => 'yes',
                                                          'cin' => 'yes',
                                                          'to_company' => 'yes',
                                                          'to_address' => 'yes',
                                                          'to_country' => 'yes',
                                                          'to_state' => 'yes',
                                                          'to_mobile' => 'yes',
                                                          'to_email' => 'yes',
                                                          'to_state_code' => 'yes',
                                                          'place_of_supply' => 'yes',
                                                          'billing_country' => 'yes',
                                                          'nature_of_supply' => 'yes',
                                                          'gst_payable' => 'yes',
                                                          'quantity' => 'yes',
                                                          'price' => 'yes',
                                                          'sub_total' => 'yes',
                                                          'taxable_value' => 'yes',
                                                          'cgst' => 'yes',
                                                          'sgst' => 'yes',
                                                          'show_from' => 'yes',
                                                          'bordered' => 'no',
                                                          'l_r' => 'yes',
                                                          'logo_align' => 'Center',
                                                          'heading_position' => 'center',
                                                          'theme' => 'custom',
                                                          'background' => 'white',
                                                          'igst' => 'yes',
                                                          'tds' => 'yes',
                                                          'display_affliate' => 'yes',
                                                        )),
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'CR',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '3',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'DR',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '4',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PO',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '5',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PUR',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '-',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '6',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PR',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '7',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'DC',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '8',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PCODE',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '-',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '11',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'SCODE',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '-',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '12',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'EXB',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '18',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'RV',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '19',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PV',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '20',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'EXV',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '21',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'AV',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '22',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'RFV',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '23',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PCR',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '27',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PDR',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '28',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'SV',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '31',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'PUV',
							'settings_invoice_last_prefix'=>'month_with_number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'monthly',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '32',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'BR',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '56',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'DAM',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '59',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'MIS',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '58',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
		$settings_data[]=array('settings_invoice_first_prefix'=>'FIX',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '57',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);

		$settings_data[]=array('settings_invoice_first_prefix'=>'BOE',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '60',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);

			$settings_data[]=array('settings_invoice_first_prefix'=>'CUST',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '10',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);

			$settings_data[]=array('settings_invoice_first_prefix'=>'SUP',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '09',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);
			$settings_data[]=array('settings_invoice_first_prefix'=>'JV',
							'settings_invoice_last_prefix'=>'number',
							"invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
							'invoice_readonly'=>'yes',
							"item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '48',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                			"tcs_visible" => 'yes',
                			"gst_visible" => 'yes',
							'branch_id'=>$branch_id
						);   

            $settings_data[]=array('settings_invoice_first_prefix'=>'BH',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '61',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );  

                 $settings_data[]=array('settings_invoice_first_prefix'=>'CH',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '62',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        ); 

                 $settings_data[]=array('settings_invoice_first_prefix'=>'GV',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '63',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );

                 $settings_data[]=array('settings_invoice_first_prefix'=>'SH',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '64',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );


                 $settings_data[]=array('settings_invoice_first_prefix'=>'DE',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '65',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );



                 $settings_data[]=array('settings_invoice_first_prefix'=>'FA',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '66',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );

                 $settings_data[]=array('settings_invoice_first_prefix'=>'INV',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '67',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );

                 $settings_data[]=array('settings_invoice_first_prefix'=>'LO',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '68',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );

                     $settings_data[]=array('settings_invoice_first_prefix'=>'CV',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'both',
                            "note_split" => 'yes',
                            "module_id" => '45',
                            "pdf_settings" => '',
                            "tds_visible" => 'yes',
                            "tcs_visible" => 'yes',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );
                    $settings_data[]=array('settings_invoice_first_prefix'=>'OUT',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'product',
                            "note_split" => 'yes',
                            "module_id" => '114',
                            "pdf_settings" => '',
                            "tds_visible" => 'no',
                            "tcs_visible" => 'no',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );
                    $settings_data[]=array('settings_invoice_first_prefix'=>'INL',
                            'settings_invoice_last_prefix'=>'number',
                            "invoice_seperation" => '/',
                            "invoice_type" => 'regular',
                            'invoice_creation'=>'automatic',
                            'invoice_readonly'=>'yes',
                            "item_access" => 'product',
                            "note_split" => 'yes',
                            "module_id" => '115',
                            "pdf_settings" => '',
                            "tds_visible" => 'no',
                            "tcs_visible" => 'no',
                            "gst_visible" => 'yes',
                            'branch_id'=>$branch_id
                        );
		$this->general_model->insertBatchData('settings',$settings_data);
	}

	function default_active_sub_modules_entry($branch_id)
	{
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '2',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '3',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '4',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '6',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '18',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '19',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '20',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '21',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '22',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '23',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '27',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '28',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '30',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '31',
							'branch_id'=>$branch_id
						);
		$active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '32',
							'branch_id'=>$branch_id
						);
        $active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '114',
                            'branch_id'=>$branch_id
                        );
        $active_sub_modules_data[]=array('sub_module_id'=>'7',
                            "module_id" => '115',
                            'branch_id'=>$branch_id
                        );
		$this->general_model->insertBatchData('active_sub_modules',$active_sub_modules_data);
	}

	function edit($id)
	{
		$id=$this->encryption_url->decode($id);

		$string="f.*,b.*,c.*,s.state_code";
		$table="firm f";
		$where=array("f.firm_id"=>$id,'f.delete_status'=>0);
		$join=array('branch b'=>'f.firm_id=b.firm_id','common_settings c'=>'c.branch_id=b.branch_id','states s'=>'s.state_id=b.branch_state_id');
		$data['data'] = $this->general_model->getJoinRecords($string,$table,$where,$join);
		// print_r($data['firm']);

       		/* Country Details */
		$country_data=$this->common->country_field();    
		$data['country']= $this->general_model->getRecords($country_data['string'],$country_data['table'],$country_data['where']);
		/* Country Details */

       		/* State Details */
		$state_data=$this->common->state_field($data['data'][0]->branch_country_id);    
		$data['state']= $this->general_model->getRecords($state_data['string'],$state_data['table'],$state_data['where']);
		/* State Details */

       		/* City Details */
		$city_data=$this->common->city_field($data['data'][0]->branch_state_id);    
		$data['city']= $this->general_model->getRecords($city_data['string'],$city_data['table'],$city_data['where']);
		/* City Details */

		$financial_data=$this->common->financial_year_field();    
		$data['financial_year']= $this->general_model->getRecords($financial_data['string'],$financial_data['table'],$financial_data['where']);

		/*$currency_data=$this->common->currency_field();    
		$data['currency']= $this->general_model->getRecords($currency_data['string'],$currency_data['table'],$currency_data['where']);*/
        $data['currency']= $this->currency_call();
		$this->load->view("super_admin/firm/edit",$data);
	}

	function edit_firm()
	{
		// 	 // print_r($this->input->post());die;
		// 	$img = $_FILES["logo"]["name"];
		// 	$id = $this->input->post("id");
		// 	if(empty($img)){
		// 	$firm_array = array(
		// 		"firm_name" => $this->input->post('firm_name'),
		// 		"firm_short_name" => $this->input->post('short_name'),
		// 		"firm_registered_type" => $this->input->post('f_r_type'),
		// 		"firm_company_code" => $this->input->post('firm_c_c')
		// 	);
		// 	if($this->general_model->updateData("firm",$firm_array,["firm_id"=>$id])){
		// 		redirect("superadmin/firm");
		// 	}

		// }
		// else{

		// 	echo $old_image = $this->input->post("old_logo");

		// 	unlink("./uploads/".$old_image);

		// 	$image = image_upload("logo",1000,1000,1000,786);
		// 	// echo $image;die;
		// 	$firm_array = array(
		// 		"firm_name" => $this->input->post('firm_name'),
		// 		"firm_short_name" => $this->input->post('short_name'),
		// 		"firm_registered_type" => $this->input->post('f_r_type'),
		// 		"firm_logo" => $image,
		// 		"firm_company_code" => $this->input->post('firm_c_c')
		// 	);
		// 	if($this->general_model->updateData("firm",$firm_array,["firm_id"=>$id])){
		// 		redirect("superadmin/firm");
		// 	}
		// }

        $fyp = $this->input->post('financial_year_password');
        $efyd =  $this->encryption->encrypt($fyp);
        // print_r($this->input->post());die;

        $id= $this->input->post('id');  

        if (isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"]!="")
        {
            $path_parts = pathinfo($_FILES["logo"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];

            // if (!is_dir('assets/branch_files/'.$this->session->userdata('SESS_BRANCH_ID'))) 
            // {
            //     mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            // }

            $url = "uploads/".$image_path;

            if (in_array($path_parts['extension'], array("jpg", "jpeg", "png"))) {
                if (is_uploaded_file($_FILES["logo"]["tmp_name"])) 
                {
                    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $url)) 
                    {
                        $image_name = $image_path;
                    }
                }
            }
        } else {
            $image_name = $this->input->post('hidden_logo_name');
        }

        if (isset($_FILES["import_export_code"]["name"]) && $_FILES["import_export_code"]["name"]!="")
        {
            $path_parts = pathinfo($_FILES["import_export_code"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];

            // if (!is_dir('assets/branch_files/'.$this->session->userdata('SESS_BRANCH_ID'))) 
            // {
            //     mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            // }

            $url = "uploads/".$image_path;

            if (in_array($path_parts['extension'], array("jpg", "jpeg", "png", "pdf", "doc", "docx", "xls", "xlsx"))) {
                if (is_uploaded_file($_FILES["import_export_code"]["tmp_name"])) 
                {
                    if (move_uploaded_file($_FILES["import_export_code"]["tmp_name"], $url)) 
                    {
                        $iec_name = $image_path;
                    }
                }
            }
        } else {
            $iec_name = $this->input->post('hidden_iec_name');
        }

        if (isset($_FILES["shop_establishment"]["name"]) && $_FILES["shop_establishment"]["name"]!="")
        {
            $path_parts = pathinfo($_FILES["shop_establishment"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];

            // if (!is_dir('assets/branch_files/'.$this->session->userdata('SESS_BRANCH_ID'))) 
            // {
            //     mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            // }

            $url = "uploads/".$image_path;

            if (in_array($path_parts['extension'], array("jpg", "jpeg", "png", "pdf", "doc", "docx", "xls", "xlsx"))) {
                if (is_uploaded_file($_FILES["shop_establishment"]["tmp_name"])) 
                {
                    if (move_uploaded_file($_FILES["shop_establishment"]["tmp_name"], $url)) 
                    {
                        $shop_name = $image_path;
                    }
                }
            }
        } else {
            $shop_name = $this->input->post('hidden_shop_name');
        }
     
        $firm_id=$this->input->post('firm_id');
        $branch_id=$this->input->post('branch_id');
        
        $firm_data = array("firm_name" => $this->input->post('name'),
                            "firm_short_name" => $this->input->post('short_name'),
                            "firm_registered_type" => $this->input->post('registered_type'),
                            "firm_logo" => $image_name,
                            "firm_company_code" => $this->input->post('company_code')
                        );
        if($this->general_model->updateData('firm',$firm_data,array('firm_id'=>$firm_id,'delete_status'=>0)))
        {
            // $log_data = array(
            //             'user_id' => $this->session->userdata('SESS_USER_ID'),
            //             'table_id' => $firm_id,
            //             'table_name' => 'firm',
            //             'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            //             'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            //             'message' => 'Firm Inserted'
            //         );                

            // $this->general_model->insertData('log',$log_data);

	        $branch_data = array(
	                            "firm_id" => $firm_id,
	                            "branch_name" => $this->input->post('name'),
	                            "branch_gstin_number" => $this->input->post('branch_gstin_number'),
	                            "branch_gst_registration_type" => $this->input->post('branch_gst_registration_type'),
	                            "branch_code" => $this->input->post('branch_code'),
	                            "branch_address" => $this->input->post('branch_address'),
	                            "branch_country_id" => $this->input->post('sa_country'),
	                            "branch_state_id" => $this->input->post('sa_state'),
	                            "branch_city_id" => $this->input->post('sa_city'),
	                            "branch_postal_code" => $this->input->post('branch_postal_code'),
	                            "branch_email_address" => $this->input->post('email'),
	                            "branch_mobile" => $this->input->post('mobile'),
	                            "branch_land_number" => $this->input->post('land_number'),
	                            "branch_pan_number" => $this->input->post('pan_number'),
	                            "branch_cin_number" => $this->input->post('cin_number'),
	                            "branch_roc" => $this->input->post('branch_roc'),
	                            "branch_esi" => $this->input->post('branch_esi'),
	                            "branch_pf" => $this->input->post('branch_pf'),
	                            "branch_tan_number" => $this->input->post('tan_number'),
	                            "branch_import_export_code" => $iec_name,
	                            "branch_shop_establishment" => $shop_name,
	                            "branch_others" => $this->input->post('others'),
	                            "updated_date" => date('Y-m-d'),
	                            "updated_user_id" => '1',
	                            "branch_default_currency" => $this->input->post('currency_id')

	                            /*"financial_year_id" => $this->input->post('financial_year_id')*/);
	                            // print_r($branch_data);die;

	        if($this->general_model->updateData('branch',$branch_data,array('branch_id'=>$branch_id,'delete_status'=>0)))
	        {

                $tax_split_percentage = 50;
                if($this->input->post('tax_split_percentage') != '') 
                    if($this->input->post('tax_split_percentage') <= 100 ) 
                    $tax_split_percentage = $this->input->post('tax_split_percentage');

	            $common_settings_data = array(
                                "tax_split_percentage" => $tax_split_percentage,
	                            "round_off_access" => $this->input->post('round_off_access'),
	                            "tax_split_equaly" => $this->input->post('tax_split_equaly'),
	                            "financial_year_password" => $efyd,
	                            'default_notification_date' => $this->input->post('default_notification_date'),
	                            "invoice_footer" => $this->input->post('invoice_footer'),
	                            "registered_type" => 'trial',
	                            "branch_id" => $branch_id
	                        );
	            $this->general_model->updateData('common_settings',$common_settings_data,array('branch_id'=>$branch_id,'delete_status'=>0));

	            // $this->default_ledger_group_entry($branch_id);
	            // $this->default_discount_entry($branch_id);
	            // $this->default_tax_entry($branch_id);
	            // $this->default_settings_entry($branch_id);
	            // $this->default_active_sub_modules_entry($branch_id);

	            // $log_data = array(
	            //             'user_id' => $this->session->userdata('SESS_USER_ID'),
	            //             'table_id' => $branch_id,
	            //             'table_name' => 'branch',
	            //             'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
	            //             'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
	            //             'message' => 'Branch Updated'
	            //         );

	            // $this->session->set_userdata('SESS_FINANCIAL_YEAR_ID',$this->input->post('financial_year_id'));
	            // $this->session->set_userdata('SESS_DEFAULT_CURRENCY',$this->input->post('currency_id'));

	            // $this->general_model->insertData('log',$log_data);
	        }
        }

		redirect("superadmin/firm");
	}

	public function delete()
	{
       	$id=$this->input->post('delete_id');
        $id=$this->encryption_url->decode($id);
      
		if($this->general_model->updateData('firm',array('delete_status'=>1),array('firm_id'=>$id)))
		{
			$branch=$this->general_model->getRecords('branch_id','branch',array('firm_id'=>$id,'delete_status'=>0));
			foreach ($branch as $key => $value)
			{
				$this->general_model->updateData('branch',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('common_settings',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('account_group',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('account_subgroup',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('ledgers',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('default_ledgers',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('tax',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('discount',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('settings',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
				$this->general_model->updateData('active_sub_modules',array('delete_status'=>1),array('branch_id'=>$value->branch_id));
			}
			redirect('superadmin/firm','refresh');
		}
		else
		{
			$this->session->set_flashdata('fail', 'Firm can not be Deleted.');
			redirect("superadmin/firm",'refresh');
		}
	}


    public function get_check_email() {
        $email = $this->input->post('email');
        $firm_id = $this->input->post('firm_id');
        $branch_id = $this->input->post('branch_id');       
       

        $data = $this->general_model->getRecords('count(*) num', 'branch', array(
            'branch_email_address' => $email,
            'firm_id !=' => $firm_id,
            'branch_id !=' => $branch_id));   
                 

        echo json_encode($data);
    }


    public function createOption_finance($finance_year_id,$branch_id){

        $user_id = $this->session->userdata('SESS_USER_ID');
        $this->db->select('*');
        $this->db->from('tbl_financial_year');
        $this->db->where('year_id',$finance_year_id);
        $res = $this->db->get();
        $result = $res->result();

        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type','financial year');
        $this->db->where('branch_id',$branch_id);
        $sup = $this->db->get();
        $result_option = $sup->result();
        

       $option_array = array();

    $i = 1;
        foreach ($result as $key => $value) {   
                $from_date = $value->from_date;
                $to_date = $value->to_date;
                $finance_year_id = $value->year_id;
                $finance_year = date('Y',strtotime($from_date)) .'-'.date('y',strtotime($to_date));
                $date = date('Y-m-d');
               $branch_id = $value->branch_id;
               
                if($from_date!= '' && $from_date!= '0000-00-00 00:00:00' && $to_date!= '' && $to_date!= '0000-00-00 00:00:00'){
                     
                
                    foreach ($result_option as $key1 => $value1) { 
                        $finance_year_option = $value1->customise_option;
                        $parent_id = $value1->id;

                        $finance_year_option = str_ireplace('{{X}}',$finance_year, $finance_year_option);
                        $option_array[$i]['purpose_option'] = $finance_year_option;
                        $option_array[$i]['parent_id'] =  $parent_id;
                        $option_array[$i]['payee_id'] = $finance_year_id;
                        $option_array[$i]['branch_id'] = $branch_id;
                        $option_array[$i]['added_user_id'] = 1;
                        $option_array[$i]['added_date'] = $date;

                        $i = $i + 1;
                    }  
                }    
        }
     
        if(!empty($option_array)){
            $table = "tbl_transaction_purpose_option";
            $this->db->insert_batch($table, $option_array);
        } 
    }


    function default_transaction_purpose($branch_id){
        $user_id = $this->session->userdata('SESS_USER_ID');
        $date = date('Y-m-d');
        $this->db->query("INSERT INTO tbl_transaction_purpose (transaction_purpose, transaction_category, customise_option, input_type, voucher_type,branch_id,status) Select transaction_purpose, transaction_category, customise_option, input_type, voucher_type,{$branch_id},status from tbl_default_transaction_purpose ");

        $this->db->select('*');
        $this->db->from('defalult_transaction_purpose_option');
        $sup = $this->db->get();
        $result_option = $sup->result();
        $i = 1;
        foreach ($result_option as $key1 => $value1) { 
            $option = $value1->purpose_option;

            $this->db->select('*');
            $this->db->from('tbl_transaction_purpose');
            $this->db->where('customise_option',$option);
            $this->db->where('branch_id',$branch_id);
            $trns = $this->db->get();
            $result_trans_option = $trns->result();            
            foreach ($result_trans_option as $key1 => $value1) { 
                $option = $value1->customise_option;
                $parent_id = $value1->id;                
                $option_array[$i]['purpose_option'] = $option;
                $option_array[$i]['parent_id'] =  $parent_id;
                $option_array[$i]['payee_id'] = 0;
                $option_array[$i]['branch_id'] = $branch_id;
                $option_array[$i]['added_user_id'] = 1;
                $option_array[$i]['added_date'] = $date;
                $i = $i + 1;
            }
        }

        if(!empty($option_array)){
            $table = "tbl_transaction_purpose_option";
            $this->db->insert_batch($table, $option_array);
        } 

    }
}