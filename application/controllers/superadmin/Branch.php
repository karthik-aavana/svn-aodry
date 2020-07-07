<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Branch extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model(['general_model','Ion_auth_model']);
        $this->load->helper('image_upload_helper');
	}

	function index(){

		$string = "firm.*,branch.*";
		$from = "branch";
		$join = ["firm" => "firm.firm_id = branch.firm_id"];
		$where = ["firm.delete_status" =>0];

		$data['branch_list'] = $this->general_model->getJoinRecords($string,$from,$where,$join);
		$this->load->view("super_admin/branch/list",$data);
	}

	function add(){

		$data['firm'] = $this->general_model->getRecords("*","firm",["delete_status" =>0]);
		$data['country'] = $this->general_model->getRecords("*","countries");
		// print_r($data['country']);die;
					$financial_data=$this->common->financial_year_field();    
		$data['financial_year']= $this->general_model->getRecords($financial_data['string'],$financial_data['table'],$financial_data['where']);

		$currency_data=$this->common->currency_field();    
		/*$data['currency']= $this->general_model->getRecords($currency_data['string'],$currency_data['table'],$currency_data['where']);*/
		$data['currency']= $this->currency_call();


		$this->load->view("super_admin/branch/add",$data);
	}

	function add_branch(){
// echo "<pre>";
// echo $this->session->userdata("SESS_SA_USER_ID");
// print_r($this->session->userdata('SESS_SA_USER_ID'));
// 		 print_r($this->input->post());die;

		$id = $this->input->post("firm_id");
		$data['firm'] = $this->general_model->getRecords("*","firm",["delete_status" =>0,"firm_id" => $id]);
		$firm_details = $data['firm'];
		// print_r($data['firm']);
		$firm_name = $firm_details[0]->firm_name;
		$firm_registered_type = $firm_details[0]->firm_registered_type;
		$firm_company_code = $firm_details[0]->firm_company_code;

		$branch_array= array(
			"branch_name" => $this->input->post("branch_name"),
		 	"company_name" => $firm_name,
		 	"firm_id" => $id,
		 	"branch_gstin_number" => $this->input->post("gstn_no"),
		 	"branch_gst_registration_type" => $this->input->post("gstn_type"),
		 	"branch_code" => $firm_company_code,
		 	"branch_address" => $this->input->post("firm-address"),
		 	"branch_country_id" => $this->input->post("country"),
		 	"branch_state_id" => $this->input->post("state"),
		 	"branch_city_id" => $this->input->post("city"),
		 	"branch_postal_code" => $this->input->post("postal_code"),
		 	"branch_email_address" => $this->input->post("email_id"),
		 	"branch_mobile" => $this->input->post("number"),
		 	"branch_land_number" => $this->input->post("land_line_no"),
		 	"branch_pan_number" => $this->input->post("pan_no"),
		 	"branch_cin_number" => $this->input->post("cin_no"),
		 	"branch_roc" => $this->input->post("roc_no"),
		 	"branch_esi" => $this->input->post("esi_no"),
		 	"branch_pf" => $this->input->post("branch_pf"),
		 	"branch_tan_number" => $this->input->post("tan_no"),
		 	"branch_import_export_code" => $this->input->post("branch_import_export_code"),
		 	"branch_shop_establishment" => $this->input->post("branch_shop_establishment"),
		 	"added_user_id" => $this->session->userdata("SESS_SA_USER_ID"),
		 	"added_date" => date("Y-m-d"),
		 	"branch_default_currency" => $this->input->post("currency_id"),
		 	"financial_year_id" => $this->input->post("financial_year_id")
		);
		// print_r($branch_array);die;
		$financial_year_id = $this->input->post("financial_year_id");
		if($branch_id=$this->general_model->insertData("branch",$branch_array))
		{
			$group_data                        = array(
                    "name"        => 'admin',
                    "description"       => 'Admin have all the privileges',
                    "branch_id"         => $branch_id,
                    "added_date"      => date('Y-m-d'),
                    "added_user_id"   => '1');
			$group_id = $this->general_model->insertData("groups", $group_data);
			$warehouse_data= array(
				"warehouse_name" => $this->input->post("branch_name"),
			 	"warehouse_address" => $this->input->post("firm-address"),
			 	"warehouse_country_id" => $this->input->post("country"),
			 	"warehouse_state_id" => $this->input->post("state"),
			 	"warehouse_city_id" => $this->input->post("city"),
			 	"added_user_id" => $this->session->userdata("SESS_SA_USER_ID"),
			 	"added_date" => date("Y-m-d"),
			 	"branch_id" => $branch_id
			);
			$this->general_model->insertData('warehouse',$warehouse_data);

			$common_settings_data = array(
	                            "round_off_access" =>"",
	                            "tax_split_equaly" => "",
	                            "financial_year_password" => " ",
	                            'default_notification_date' => $this->input->post('default_notification_date'),
	                            "invoice_footer" => "",
	                            "registered_type" => 'trial',
	                            "branch_id" => $branch_id
	                        );
	            $this->general_model->insertData('common_settings',$common_settings_data);

	            $this->default_ledger_group_entry($branch_id);
	            $this->default_discount_entry($branch_id);
	            $this->default_tax_entry($branch_id);
	            $this->default_settings_entry($branch_id);
	            $this->default_active_sub_modules_entry($branch_id);

	            $this->default_transaction_purpose($branch_id);

	            $this->createOption_finance_with_id($financial_year_id);

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

			redirect("superadmin/branch");
		}
	}

	function default_ledger_group_entry($branch_id)
	{
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
                                'created_by' => 1,
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
                            'created_by' => 1,
                        );
                }else{

                    $insert_sub = array(
                                /*'firm_id' => $firm_id,*/
                                'branch_id' => $branch_id,
                                'sub_group_id' => $ins_id,
                                'created_ts' => date('Y-m-d H:i:s'),
                                'created_by' => 1,
                            );
                }
                $this->db->insert('tbl_ledgers',$insert_sub);
            }
        }

		/*if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Sundry Debtors','branch_id'=>$branch_id)))
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

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Deposit','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'RD','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'RD','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'RD','ledger_label'=>'RD','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'FD','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'FD','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'FD','ledger_label'=>'FD','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Rent','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Rent','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Rent','ledger_label'=>'Rent','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Investment','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Investment','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Investment','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Investment','ledger_label'=>'Investment','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Capital','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Self','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Self','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Self','ledger_label'=>'Self','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Partners','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Partners','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Partners','ledger_label'=>'Partners','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Shareholder','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Shareholder','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Shareholder','ledger_label'=>'Shareholder','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}

		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Loan','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Organization','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Organization','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Organization','ledger_label'=>'Organization','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Person','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Person','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Person','ledger_label'=>'Person','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Loan Repaid To Lender','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Interest Paid To Loan','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Interest Paid To Loan','ledger_label'=>'Interest Paid To Loan','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Instalment or EMI Paid To Lender','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Interest Paid By Instalment or EMI','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Interest Paid By Instalment or EMI','ledger_label'=>'Interest Paid By Instalment or EMI','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Loan Repaid By Borrower','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Interest Received To Loan','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Interest Received To Loan','ledger_label'=>'Interest Received To Loan','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Instalment or EMI Repaid By Borrower','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Interest Received By Instalment or EMI','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Interest Received By Instalment or EMI','ledger_label'=>'Interest Received By Instalment or EMI','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}
		if($account_group_id=$this->general_model->insertData('account_group',array('account_group_title'=>'Suspense','branch_id'=>$branch_id)))
		{
			if($account_subgroup_id=$this->general_model->insertData('account_subgroup',array('subgroup_title'=>'Suspense','branch_id'=>$branch_id,'account_group_id'=>$account_group_id)))
			{
				if($ledger_id=$this->general_model->insertData('ledgers',array('ledger_title'=>'Others','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id)))
				{
					$this->general_model->insertData('default_ledgers',array('ledger_name'=>'Others','ledger_label'=>'Others','branch_id'=>$branch_id,'account_subgroup_id'=>$account_subgroup_id,'ledger_id'=>$ledger_id));
				}
			}
		}*/
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
		/*$tax_data[]=array('tax_name'=>'Slab@ 0%',
							'tax_value'=>'0.00',
							"added_date" => date('Y-m-d'),
                            "added_user_id" => '1',
							'branch_id'=>$branch_id
						);*/

		$this->db->select('*');
		$this->db->where('is_default','1');
		$this->db->where('delete_status',0);
        $qry = $this->db->get('tax_section');
        $default_ry = $qry->result_array();
        $tax_data = array();

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
        $tax_gst = array();
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
    public function get_branch($firm_id){
  

      // if(!empty($this->input->post('firm_id')) && $this->input->post('firm_id') !="")
      // {
      //   $firm_id=$this->input->post('firm_id');
      // }
      // else
      // {
      //   $firm_id="";
      // }

        /* Branch Details */
      
        $branch_data=$this->common->branch_field($firm_id);
  

        $data= $this->general_model->getJoinRecords($branch_data['string'],$branch_data['table'],$branch_data['where'],$branch_data['join'],$branch_data['order']);
        /* Branch Details */
 
        echo json_encode($data);
    }
	function edit($id)
	{
		// $data['firm'] = $this->general_model->getRecords("*","firm",["delete_status" =>0]);
		// $data["branch"] = $this->general_model->getRecords("branch.*","branch",["delete_status" => 0,"branch_id" => $id]);

		// $state_id = $data["branch"][0]->branch_state_id;

		// $data['country'] = $this->general_model->getRecords("*","countries");
		// $data['state'] = $this->general_model->getRecords("*","states");
		// // print_r($data['state']);die;
		// // print_r($data["branch"]);die;	
		// $this->load->view("super_admin/branch/edit",$data);

		$id=$this->encryption_url->decode($id);

		$string="f.*,b.*,c.*";
		$table="branch b";
		$where=array("b.branch_id"=>$id,'b.delete_status'=>0);
		$join=array('firm f'=>'f.firm_id=b.firm_id','common_settings c'=>'c.branch_id=b.branch_id');
		$data['data'] = $this->general_model->getJoinRecords($string,$table,$where,$join);


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

		$this->load->view("super_admin/branch/edit",$data);
	}

	function edit_branch()
	{
		// $branch_id = $this->input->post("branch_id");

		// $branch_array= array(
		// 	"branch_name" => $this->input->post("branch_name"),
		// 	"company_name" => $firm_name,
		// 	"branch_gstin_number" => $this->input->post("gstn_no"),
		// 	"branch_email_address" => $this->input->post("email_id"),
		// 	"firm_id" => $this->input->post("firm_id"),
		// 	"branch_gstin_number" => $this->input->post("branch_code"),
		// 	"branch_code" => $this->input->post("branch_code"),
		// 	"branch_address" => $this->input->post("firm-address"),
		// 	"branch_country_id" => $this->input->post("country"),
		// 	"branch_tan_number" => $this->input->post("tan_no"),
		// 	"branch_state_id" => $this->input->post("state"),
		// 	"branch_postal_code" => $this->input->post("postal_code"),
		// 	"branch_mobile" => $this->input->post("number"),
		// 	"branch_land_number" => $this->input->post("land_line_no"),
		// 	"branch_pan_number" => $this->input->post("pan_no"),
		// 	"branch_cin_number" => $this->input->post("cin_no"),
		// 	"branch_roc" => $this->input->post("roc_no"),
		// 	"branch_import_export_code" =>$this->input->post("branch_import_export_code"),
		// 	"branch_shop_establishment" => $this->input->post("branch_shop_establishment"),
		// 	"branch_esi" => $this->input->post("esi_no"),
		// 	'updated_date' =>date("Y-m-d"),
		// 	"branch_pf" => $this->input->post("branch_pf")
		// );

		// if($this->general_model->updateData("branch",$branch_array,["branch_id" => $branch_id])){
		// 	redirect("superadmin/branch");
		// }

        $fyp = $this->input->post('financial_year_password');
        $efyd =  $this->encryption->encrypt($fyp);

        $firm_id=$this->input->post('firm_id');
        $branch_id=$this->input->post('branch_id');
        
        $firm_data = array(
        					// "firm_name" => $this->input->post('name'),
                            "firm_short_name" => $this->input->post('short_name'),
                            "firm_registered_type" => $this->input->post('registered_type'),
                            "firm_logo" => $image_name,
                            "firm_company_code" => $this->input->post('company_code')
                        );
        if($this->general_model->updateData('firm',$firm_data,array('firm_id'=>$firm_id,'delete_status'=>0)))
        {
	        $branch_data = array(
	                            "firm_id" => $firm_id,
	                            "branch_name" => $this->input->post('name'),
	                            "branch_gstin_number" => $this->input->post('branch_gstin_number'),
	                            "branch_gst_registration_type" => $this->input->post('branch_gst_registration_type'),
	                            "branch_code" => $this->input->post('branch_code'),
	                            "branch_address" => $this->input->post('branch_address'),
	                            "branch_country_id" => $this->input->post('country'),
	                            "branch_state_id" => $this->input->post('state'),
	                            "branch_city_id" => $this->input->post('city'),
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
	                            "updated_user_id" => $this->session->userdata('SESS_SA_USER_ID'),
	                            "branch_default_currency" => $this->input->post('currency_id'),
	                            /*"financial_year_id" => $this->input->post('financial_year_id')*/
	                        );

	        if($this->general_model->updateData('branch',$branch_data,array('branch_id'=>$branch_id,'delete_status'=>0)))
	        {
	            $common_settings_data = array(
	                            "round_off_access" => $this->input->post('round_off_access'),
	                            "tax_split_equaly" => $this->input->post('tax_split_equaly'),
	                            "financial_year_password" => $efyd,
	                            'default_notification_date' => $this->input->post('default_notification_date'),
	                            "invoice_footer" => $this->input->post('invoice_footer'),
	                            "registered_type" => 'trial',
	                            "branch_id" => $branch_id
	                        );
	            $this->general_model->updateData('common_settings',$common_settings_data,array('branch_id'=>$branch_id,'delete_status'=>0));
	        }
        }

		redirect("superadmin/branch");
	}

	function reverse(){

			$a = '{"Title": "The Cuckoos Calling",
				"Author": "Robert Galbraith",
				"Detail": {
				"Publisher": "Little Brown"
				}}';

	$b = json_decode($a,true);
	print_r($b);
// Title : The Cuckoos Calling
// Author : Robert Galbraith
// Publisher : Little Brown

	foreach ($b as $key => $value) {
		echo $value;
	}

	}

	public function createOption_finance_with_id($financial_id){

        $user_id = $this->session->userdata('SESS_USER_ID');
        $this->db->select('*');
        $this->db->from('tbl_financial_year');
        $this->db->where('year_id',$financial_id);
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