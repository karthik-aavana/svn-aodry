<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Posaccounting extends CI_Controller {
	public $branch_id = 0;
    public $user_id = 0;
    public $SESS_FINANCIAL_YEAR_TITLE = '';
    public $SESS_FINANCIAL_YEAR_ID = '';
    public $SESS_DEFAULT_CURRENCY_TEXT = '';
    public $SESS_DEFAULT_CURRENCY_CODE = '';
    public $SESS_DEFAULT_CURRENCY = '';
    public $SESS_DEFAULT_CURRENCY_SYMBOL = '';
    public $modules = array();
	function __construct(){
		parent::__construct();
		$this->pos_db = $this->load->database('pos', true);
		$this->ci = &get_instance();
        $this->load->library(array(
            'common_api',
            'ion_auth',
            'form_validation'));

        $this->load->model([
            'general_model' ,
            'Voucher_model' ,
            'ledger_model' ]);
	}

	public function salesAccounting(){
        $sales_qry = $this->pos_db->query('SELECT branch_id,user_id,count(id) as count FROM `aodry_pos_sales_masters` group BY branch_id ORDER BY id DESC');
		$sales_resp  = $sales_qry->result();
		if(!empty($sales_resp)){
			foreach ($sales_resp as $key => $branch_data) {
				$branch_id = $branch_data->branch_id;
               
				if($branch_data->count > 0){
					$b_data = $this->common_api->get_default_country_state($branch_data->branch_id);
					if($this->getModulesDetails($branch_data)){
                        $b_data['notes_module_id']           = $this->config->item('notes_module');
                        $b_data['product_module_id']         = $this->config->item('product_module');
                        $b_data['service_module_id']         = $this->config->item('service_module');
                        $b_data['customer_module_id']        = $this->config->item('customer_module');
                        $b_data['category_module_id']        = $this->config->item('category_module');
                        $b_data['subcategory_module_id']     = $this->config->item('subcategory_module');
                        $b_data['tax_module_id']             = $this->config->item('tax_module');
                        $b_data['discount_module_id']        = $this->config->item('discount_module');
                        $b_data['accounts_module_id']        = $this->config->item('accounts_module');
                        /* Sub Modules Present */
                        $b_data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
                        $b_data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
                        $b_data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
                        $b_data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
                        $b_data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
                        $privilege       = "add_privilege";
    					$sales_module_id = $this->config->item('sales_module');
                        $section_modules = $this->common_api->get_section_modules($sales_module_id , $this->modules , $privilege);
                        $data            = array_merge($b_data , $section_modules);

                        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
                        $receipt_section_modules = $this->common_api->get_section_modules($receipt_voucher_module_id , $this->modules , $privilege);

                        $receipt_data            = array_merge($b_data , $receipt_section_modules);

                        $customer_module_id = $this->config->item('customer_module');
                        $customer_section_modules = $this->common_api->get_section_modules($customer_module_id , $this->modules , $privilege);
                        $customer_data            = array_merge($b_data , $customer_section_modules);

                        $access_settings = $section_modules['access_settings'];
    					$data['sales_module_id']           = $sales_module_id;
                        $data['module_id']                 = $sales_module_id;
    					
    					$sales_records = $this->pos_db->query('SELECT s.*,c.name,c.email,c.address,c.pincode,c.mobile_number,p.payment_method,s.created_at as sales_date FROM `aodry_pos_sales_masters` s LEFT JOIN aodry_pos_customers c ON c.id=s.pos_customer_id LEFT JOIN aodry_pos_payment_types p ON p.id=s.payment_type_id WHERE s.branch_id='.$branch_id.' AND s.delete_status=0 AND s.edit_status=0 AND is_ledgers=0 ORDER BY s.id ASC');
    					$sales_records  = $sales_records->result_array();
                        
    					foreach ($sales_records as $k => $sales_data) {
    						$sales_item_qry = $this->pos_db->query('SELECT * FROM `aodry_pos_sales_transactions` WHERE pos_sales_master_id='.$sales_data['id'].' AND delete_status=0');
    						$js_data  = $sales_item_qry->result_array();
                            
                            $pos_customer = array(
                                'pos_customer_id' => $sales_data['pos_customer_id'],
                                'email' => $sales_data['email'],
                                'mobile_number' => $sales_data['mobile_number'],
                                'name' => $sales_data['name'],
                                'address' => $sales_data['address'],
                                'pincode' => $sales_data['pincode'],
                            );
                            $customer_ary = $this->getCustomerDetails($pos_customer,$branch_data,$customer_data);
                            $sales_data['customer_id'] = $customer_ary['customer_id'];
                            $sales_data['customer_ledger_id'] = $customer_ary['customer_ledger_id'];
    						$js_data1 = array();
                            $sales_tax_amount = 0;
    						foreach ($js_data as $keyy => $value) {
    							$item_data = $value;
    							$gst_amount     = $item_data['gst_amount'];
                                $sales_tax_amount +=  $item_data['gst_amount'];
    	                        $gst_percentage = $item_data['gst_percentage'];
    	                        if ($section_modules['access_settings'][0]->tax_type == "gst"){
    	                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
    	                            $cgst_amount_percentage = $tax_split_percentage;
    	                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
    	                            
    	                            $item_data['sales_item_cgst_amount'] = ($gst_amount * $cgst_amount_percentage) / 100;
    	                            $item_data['sales_item_sgst_amount'] = ($gst_amount * $sgst_amount_percentage) / 100;
    	                            
    	                            $item_data['sales_item_cgst_percentage'] = ($gst_percentage * $cgst_amount_percentage) / 100;
    	                            $item_data['sales_item_sgst_percentage'] = ($gst_percentage * $sgst_amount_percentage) / 100;
    	                        }
                                $item_data['sales_item_tax_cess_percentage'] = 0;
    	                        $data_item  = array_map('trim' , $item_data);
                            	$js_data1[] = $data_item;
    						}
    					    $sales_data['sales_tax_amount'] = $sales_tax_amount;
                            $data_main   = array_map('trim' , $sales_data);
    		                
                            $action = "add";
    						if (in_array($data['accounts_module_id'] , $section_modules['active_add']) && $data_main['total_amount_received'] > 0){
    		                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
    		                        $this->sales_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
    		                    }
    		                }
    		                $this->receipt_voucher($receipt_data,$data_main,$action,$data['branch']);
                            $this->pos_db->set('is_ledgers',1);
                            $this->pos_db->where('id',$sales_data['id']);
                            $this->pos_db->update('aodry_pos_sales_masters');
    					}
                    }
				}
			}
		}
		/*print_r($sales_resp);*/
		exit();
	}

    public function EditSalesAccounting(){
        $sales_qry = $this->pos_db->query('SELECT branch_id,user_id,count(id) as count FROM `aodry_pos_sales_masters` GROUP BY branch_id ORDER BY id DESC');
        $sales_resp  = $sales_qry->result();

        if(!empty($sales_resp)){
            foreach ($sales_resp as $key => $branch_data) {
                $branch_id = $branch_data->branch_id;

                if($branch_data->count > 0){
                    $sales_records = $this->pos_db->query('SELECT s.*,c.name,c.email,c.address,c.pincode,c.mobile_number,p.payment_method,s.created_at as sales_date FROM `aodry_pos_sales_masters` s LEFT JOIN aodry_pos_customers c ON c.id=s.pos_customer_id LEFT JOIN aodry_pos_payment_types p ON p.id=s.payment_type_id WHERE s.branch_id='.$branch_id.' AND s.delete_status=0 AND s.edit_status=1 AND s.is_edit_ledgers=0 AND is_ledgers=1 ORDER BY s.id ASC');
                    $sales_records  = $sales_records->result_array();
                    
                    if(!empty($sales_records)){
                        $b_data = $this->common_api->get_default_country_state($branch_data->branch_id);
                        if($this->getModulesDetails($branch_data)){
                            $b_data['notes_module_id']           = $this->config->item('notes_module');
                            $b_data['product_module_id']         = $this->config->item('product_module');
                            $b_data['service_module_id']         = $this->config->item('service_module');
                            $b_data['customer_module_id']        = $this->config->item('customer_module');
                            $b_data['category_module_id']        = $this->config->item('category_module');
                            $b_data['subcategory_module_id']     = $this->config->item('subcategory_module');
                            $b_data['tax_module_id']             = $this->config->item('tax_module');
                            $b_data['discount_module_id']        = $this->config->item('discount_module');
                            $b_data['accounts_module_id']        = $this->config->item('accounts_module');
                            /* Sub Modules Present */
                            $b_data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
                            $b_data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
                            $b_data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
                            $b_data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
                            $b_data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
                            $privilege       = "add_privilege";
                            $sales_module_id = $this->config->item('sales_module');
                            $section_modules = $this->common_api->get_section_modules($sales_module_id , $this->modules , $privilege);
                            $data            = array_merge($b_data , $section_modules);

                            $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
                            $receipt_section_modules = $this->common_api->get_section_modules($receipt_voucher_module_id , $this->modules , $privilege);

                            $receipt_data            = array_merge($b_data , $receipt_section_modules);

                            $customer_module_id = $this->config->item('customer_module');
                            $customer_section_modules = $this->common_api->get_section_modules($customer_module_id , $this->modules , $privilege);
                            $customer_data            = array_merge($b_data , $customer_section_modules);

                            $access_settings = $section_modules['access_settings'];
                            $data['sales_module_id']           = $sales_module_id;
                            $data['module_id']                 = $sales_module_id;
                            
                            foreach ($sales_records as $k => $sales_data) {
                                $sales_voucher_qry = $this->db->query("SELECT sales_voucher_id FROM `sales_voucher` WHERE reference_id='{$sales_data['id']}' AND reference_type='pos' AND branch_id='".$branch_id."'");
                                $sales_voucher  = $sales_voucher_qry->result_array();

                                $sales_voucher_id = (!empty($sales_voucher) ? $sales_voucher[0]['sales_voucher_id'] : 0);

                                $sales_item_qry = $this->pos_db->query('SELECT * FROM `aodry_pos_sales_transactions` WHERE pos_sales_master_id='.$sales_data['id'].' AND delete_status=0');
                                $js_data  = $sales_item_qry->result_array();
                                
                                $pos_customer = array(
                                    'pos_customer_id' => $sales_data['pos_customer_id'],
                                    'email' => $sales_data['email'],
                                    'mobile_number' => $sales_data['mobile_number'],
                                    'name' => $sales_data['name'],
                                    'address' => $sales_data['address'],
                                    'pincode' => $sales_data['pincode'],
                                );
                                $customer_ary = $this->getCustomerDetails($pos_customer,$branch_data,$customer_data);
                                $sales_data['customer_id'] = $customer_ary['customer_id'];
                                $sales_data['customer_ledger_id'] = $customer_ary['customer_ledger_id'];
                                $js_data1 = array();
                                $sales_tax_amount = 0;
                                foreach ($js_data as $keyy => $value) {
                                    $item_data = $value;
                                    $gst_amount     = $item_data['gst_amount'];
                                    $sales_tax_amount +=  $item_data['gst_amount'];
                                    $gst_percentage = $item_data['gst_percentage'];
                                    if ($section_modules['access_settings'][0]->tax_type == "gst"){
                                        $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                                        $cgst_amount_percentage = $tax_split_percentage;
                                        $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                                        
                                        $item_data['sales_item_cgst_amount'] = ($gst_amount * $cgst_amount_percentage) / 100;
                                        $item_data['sales_item_sgst_amount'] = ($gst_amount * $sgst_amount_percentage) / 100;
                                        
                                        $item_data['sales_item_cgst_percentage'] = ($gst_percentage * $cgst_amount_percentage) / 100;
                                        $item_data['sales_item_sgst_percentage'] = ($gst_percentage * $sgst_amount_percentage) / 100;
                                    }
                                    $item_data['sales_item_tax_cess_percentage'] = 0;
                                    $data_item  = array_map('trim' , $item_data);
                                    $js_data1[] = $data_item;
                                }
                                $sales_data['sales_tax_amount'] = $sales_tax_amount;
                                $data_main   = array_map('trim' , $sales_data);
                                
                                $action = "edit";
                                if (in_array($data['accounts_module_id'] , $section_modules['active_add']) && $data_main['total_amount_received'] > 0){
                                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
                                        $this->sales_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                                    }
                                }
                                $this->receipt_voucher($receipt_data,$data_main,$action,$data['branch']);
                                $this->pos_db->set('is_edit_ledgers',1);
                                $this->pos_db->where('id',$sales_data['id']);
                                $this->pos_db->update('aodry_pos_sales_masters');
                            }
                        }
                    }
                }
            }
        }
    }

    public function getCustomerDetails($customer_details,$branch_data, $customer_data){
        $this->db->select('customer_id,ledger_id');
        $this->db->from('customer');
        $this->db->where('pos_customer_id',$customer_details['pos_customer_id']);
        $this->db->where('branch_id',$branch_data->branch_id);
        $this->db->where('delete_status',0);
        $cust_qry = $this->db->get();
        $result = $cust_qry->result();
        if(!empty($result)){
            return array('customer_id'=>$result[0]->customer_id, 'customer_ledger_id' => $result[0]->ledger_id);
            
        }else {
            $this->db->select('customer_id,ledger_id');
            $this->db->from('customer');
            $this->db->where('(customer_email="'.$customer_details['email'].'" OR customer_mobile="'.$customer_details['mobile_number'].'")');
            $this->db->where('branch_id',$branch_data->branch_id);
            $this->db->where('delete_status',0);
            $cust_qry = $this->db->get();
            $result = $cust_qry->result();
            if(!empty($result)){
                $this->db->set('pos_customer_id',$customer_details['pos_customer_id']);
                $this->db->where('customer_id',$result[0]->customer_id);
                $this->db->update('customer');

                return array('customer_id'=>$result[0]->customer_id, 'customer_ledger_id' => $result[0]->ledger_id);
            }
        }

        if(empty($result)){
            $access_settings = $customer_data['access_settings'];
            $primary_id      = "customer_id";
            $table_name      = 'customer';
            $date_field_name = "added_date";
            $current_date = date('Y-m-d');
            /*$voucher_number  = $this->common_api->generate_invoice_number_api($this,$access_settings , $primary_id , $table_name , $date_field_name , $current_date);*/
            /*echo $this->SESS_FINANCIAL_YEAR_TITLE;
            exit;*/
            $invoice_number  = $this->common_api->generate_invoice_number_api($this,$access_settings, $primary_id, $table_name, $date_field_name, $current_date);

            $sales_ledger = $this->config->item('sales_ledger');
            $default_customer_id = $sales_ledger['CUSTOMER'];
            $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
            
            $customer_ary = array(
                            'ledger_name' => $customer_details['mobile_number'],
                            'second_grp' => '',
                            'primary_grp' => 'Sundry Debtors',
                            'main_grp' => 'Current Assets',
                            'default_ledger_id' => 0,
                            'default_value' => $customer_details['mobile_number'],
                            'amount' => 0
                        );
            if(!empty($customer_ledger_name)){
                $customer_ledger = $customer_ledger_name->ledger_name;
                /*$customer_ledger = str_ireplace('{{SECTION}}',$section_name , $customer_ledger);*/
                $customer_ledger = str_ireplace('{{X}}',$customer_details['mobile_number'], $customer_ledger);
                $customer_ary['ledger_name'] = $customer_ledger;
                $customer_ary['primary_grp'] = $customer_ledger_name->sub_group_1;
                $customer_ary['second_grp'] = $customer_ledger_name->sub_group_2;
                $customer_ary['main_grp'] = $customer_ledger_name->main_group;
                $customer_ary['default_ledger_id'] = $customer_ledger_name->ledger_id;
            }
            $customer_ledger_id = $this->ledger_model->getGroupLedgerId($customer_ary);
            
            /* Get invoice number */
            $reference_number = $this->common_api->generate_reference_number_api($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

            $customer_data = array(
                "customer_name" => $customer_details['name'],
                "customer_code" => $invoice_number,
                "reference_number" => $reference_number,
                "reference_type" => 'customer',
                "customer_email" => $customer_details['email'],
                "customer_address" => $customer_details['address'],
                "pos_customer_id" => $customer_details['pos_customer_id'],
                "ledger_id" => $customer_ledger_id, //$ledger_id,
                "added_date" => date('Y-m-d'),
                "added_user_id" => $branch_data->user_id,
                "branch_id" => $branch_data->branch_id,
                "updated_date" => "",
                "updated_user_id" => "",
                "customer_postal_code" => $customer_details['pincode']);

            $table = "customer";
            $customer_id = $this->general_model->insertData($table, $customer_data);
            return array('customer_id' => $customer_id , 'customer_ledger_id' => $customer_ledger_id);
        }
    }

    public function receipt_voucher($data,$data_r , $action , $branch){
        $access_settings = $data['access_settings'];
        
        $primary_id      = "receipt_id";
        $table_name      = $this->config->item('receipt_voucher_table');
        $date_field_name = "voucher_date";
        $current_date    = date('Y-m-d', strtotime($data_r['sales_date']));
        $voucher_number  = $this->common_api->generate_invoice_number_api($this,$access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $receipt_data = array(
            "voucher_date"            => date('Y-m-d',strtotime($data_r['sales_date'])),
            "voucher_number"          => $voucher_number,
            "party_id"                => $data_r['customer_id'],
            "party_type"              => 'customer',
            "reference_id"            => $data_r['id'],
            "reference_type"          => 'pos',
            "reference_number"        => $data_r['pos_ref_no'],
            "from_account"            => 'customer',
            "to_account"              => 'customer-' . $data_r['name'],
            "imploded_receipt_amount" => $data_r['total_amount_received'],
            "invoice_balance_amount"  => $data_r['total_amount_received'],
            "invoice_paid_amount"     => $data_r['total_amount_received'],
            "invoice_total"           => $data_r['total_amount_received'],
            "receipt_amount"          => $data_r['total_amount_received'],
            "payment_mode"            => $data_r['payment_method'],
            "payment_via"             => $data_r['payment_transaction_name'],
            "reff_number"             => $data_r['payment_transaction_ref_no'],
            "financial_year_id"       => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "bank_name"               => $data_r['payment_transaction_name'],
            "cheque_number"           => $data_r['payment_transaction_ref_no'],
            "cheque_date"             => $data_r['created_at'],
            "description"             => $data_r['remarks'],
            "branch_id"               => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id"             => $this->input->post('currency_id')
        );
        $receipt_data['converted_receipt_amount']          = $data_r['total_amount_received'];
        $receipt_data['imploded_converted_receipt_amount'] = $data_r['total_amount_received'];
        
        $receipt_data['voucher_status'] = "1";

        if($action == 'add'){
            $receipt_data['added_date'] = date('Y-m-d');
            $receipt_data['added_user_id'] = $this->session->userdata('SESS_USER_ID');
            $data_main = array_map('trim', $receipt_data);

            if($receipt_id = $this->general_model->insertData($table_name, $data_main)){
                $pending_amount = $data_r['total_pos_amount'] - $data_r['total_amount_received'];
                $reference_data = array('receipt_id' => $receipt_id,
                                    'reference_id' => $data_r['id'],
                                    'receipt_amount' => $data_r['total_pos_amount'],
                                    'Invoice_total_received' => ($data_r['total_amount_received']),
                                    'Invoice_pending' => $pending_amount,
                                    'exchange_gain_loss' => 0,
                                    'exchange_gain_loss_type' => '',
                                    'discount' => 0,
                                    'other_charges' => 0,
                                    'round_off' => 0,
                                    'round_off_icon' => '',
                                    'receipt_total_paid' => $data_r['total_amount_received']
                                );
                $this->db->insert('receipt_invoice_reference',$reference_data);
                $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $receipt_id,
                    'table_name'        => $table_name,
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Receipt Voucher Inserted');
                $data_main['receipt_id'] = $receipt_id;
                $data_main['sub_receipt_total'] = $data_r['total_pos_amount'];
                $log_table               = $this->config->item('log_table');
                $this->general_model->insertData($log_table, $log_data);
            }
        }
        if($action == 'edit'){
            $receipt_data['updated_date'] = date('Y-m-d');
            $receipt_data['updated_user_id'] = $this->session->userdata('SESS_USER_ID');
            $data_main = array_map('trim', $receipt_data);
            $receipt_voucher_data = $this->general_model->getRecords('receipt_id', $table_name,array('reference_id'  => $data_main['reference_id'],'reference_type' => 'pos','delete_status' => 0));
            $receipt_id         = $receipt_voucher_data[0]->receipt_id;
            $where = array( 'receipt_id' => $receipt_id);
            
            $sub_receipt_total = 0;

            if ($this->general_model->updateData($table_name, $data_main, $where)){
                $pending_amount = $data_r['total_pos_amount'] - $data_r['total_amount_received'];
                $reference_data = array('receipt_id' => $receipt_id,
                                    'reference_id' => $data_r['id'],
                                    'receipt_amount' => $data_r['total_pos_amount'],
                                    'Invoice_total_received' => ($data_r['total_amount_received']),
                                    'Invoice_pending' => $pending_amount,
                                    'exchange_gain_loss' => 0,
                                    'exchange_gain_loss_type' => '',
                                    'discount' => 0,
                                    'other_charges' => 0,
                                    'round_off' => 0,
                                    'round_off_icon' => '',
                                    'receipt_total_paid' => $data_r['total_amount_received']
                                );
                
                $this->db->set($reference_data);
                $this->db->where('receipt_id',$receipt_id);
                $this->db->update('receipt_invoice_reference');
                $sub_receipt_total += $data_r['total_amount_received'];
                
                $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $receipt_id,
                    'table_name'        => $table_name,
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Receipt Voucher Updated');
                $data_main['receipt_id'] = $receipt_id;
                $data_main['sub_receipt_total'] = $sub_receipt_total;
                $log_table               = $this->config->item('log_table');
                $this->general_model->insertData($log_table, $log_data);
            }
        }

        if (in_array($data['accounts_module_id'], $data['active_add'])){
            if (in_array($data['accounts_sub_module_id'], $data['access_sub_modules'])){
                $this->VoucherEntry($data_main,$reference_data,$data_r['mobile_number'] , $action,$data_r['customer_ledger_id']);
            }
        }
    }

	public function sales_vouchers($section_modules , $data_main , $js_data , $branch){
        $invoice_from = 'customer';
        $invoice_to   = 'sales';
        $ledgers      = array();
        $access_sub_modules    = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings       = $section_modules['access_settings'];
        $sales_ledger = $this->config->item('sales_ledger');

        $default_cgst_id = $sales_ledger['CGST@X'];
        $cgst_x = $this->ledger_model->getDefaultLedgerId($default_cgst_id);

        $default_sgst_id = $sales_ledger['SGST@X'];
        $sgst_x = $this->ledger_model->getDefaultLedgerId($default_sgst_id);

        $default_utgst_id = $sales_ledger['UTGST@X'];
        $utgst_x = $this->ledger_model->getDefaultLedgerId($default_utgst_id);

        $default_igst_id = $sales_ledger['IGST@X'];
        $igst_x = $this->ledger_model->getDefaultLedgerId($default_igst_id);

        /* Tax rate slab */
        $present = "";
        $igst_slab_minus = array();
        $cgst_slab_minus = array();
        $sgst_slab_minus = array();
        $cess_slab_minus = array();
        
        $igst_slab       = array();
        $cgst_slab       = array();
        $sgst_slab       = array();
        $cess_slab       = array();
        $igst_slab_items = array();
        $cgst_slab_items = array();
        $sgst_slab_items = array();
        $cess_slab_items = array();
        $new_ledger_ary = array();
        
        if ($data_main['sales_tax_amount'] > 0){
            $present = "gst";
            $present = "cgst";
            if ($present != "gst") {
                
                foreach ($js_data as $key => $value){
                    if ($present == "cgst"){
                        if ($value['sales_item_cgst_percentage'] > 0 || $value['sales_item_sgst_percentage'] > 0){
                           
                            $cgst_ary = array(
                                            'ledger_name' => 'Output CGST@'.$value['sales_item_cgst_percentage'].'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['sales_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_x)){
                                $cgst_ledger = $cgst_x->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['sales_item_cgst_percentage'] , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_x->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                            }
                            /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);*/
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                            /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'CGST@'.$value['sales_item_cgst_percentage'].'%',
                                                                        'subgrp_1' => 'CGST',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            
                            $gst_lbl = 'SGST';
                            /*$is_utgst = $this->general_model->checkIsUtgst($data_main['sales_billing_state_id']);
                            if($is_utgst == '1') $gst_lbl = 'UTGST';*/

                            /*$default_sgst_id = $sales_ledger[$gst_lbl.'@X'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);*/
                            
                            $sgst_ary = array(
                                            'ledger_name' => 'Output '.$gst_lbl.'@'.$value['sales_item_sgst_percentage'].'%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['sales_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($sgst_x)){
                                /*if($is_utgst == '1') {
                                    $sgst_ledger = $utgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['sales_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $utgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                }else{*/
                                    $sgst_ledger = $sgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['sales_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                /*}*/
                                
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);

                            if (in_array($cgst_tax_ledger , $cgst_slab)) {
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $value['sales_item_cgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);   
                            }else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['sales_item_cgst_amount'];
                            }
                            if (in_array($sgst_tax_ledger , $sgst_slab)){
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $value['sales_item_sgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $sgst_slab[]                       = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['sales_item_sgst_amount'];
                            }
                        }
                        if ($value['sales_item_tax_cess_percentage'] > 0){
                            $default_cess_id = $sales_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                           
                            $cess_ary = array(
                                            'ledger_name' => 'Output Compensation Cess @'.$value['sales_item_tax_cess_percentage'].'%',
                                            'second_grp' => 'Cess',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['sales_item_tax_cess_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cess_ledger_name)){
                                $cess_ledger = $cess_ledger_name->ledger_name;
                                $cess_ledger = str_ireplace('{{X}}',$value['sales_item_tax_cess_percentage'] , $cess_ledger);
                                $cess_ary['ledger_name'] = $cess_ledger;
                                $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                                $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                                $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                                $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                            }
                            $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);
                            
                            if (in_array($cess_tax_ledger , $cess_slab)){
                                $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger] , $value['sales_item_tax_cess_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $cess_slab[]                       = $cess_tax_ledger;
                                $cess_slab_items[$cess_tax_ledger] = $value['sales_item_tax_cess_amount'];
                            }
                        }
                    }
                }
            }
        }
        
        /*$sales_ledger_id            = $this->ledger_model->getDefaultLedger('Sales');*/
        $default_sales_id = $sales_ledger['SALES'];
        $sales_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sales_id);
            
        $sales_ary = array(
                        'ledger_name' => 'Sales',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Sales group',
                        'amount' => 0
                    );
        if(!empty($sales_ledger_name)){
            $sales_ledger_nm = $sales_ledger_name->ledger_name;
            $sales_ary['ledger_name'] = $sales_ledger_nm;
            $sales_ary['primary_grp'] = $sales_ledger_name->sub_group_1;
            $sales_ary['second_grp'] = $sales_ledger_name->sub_group_2;
            $sales_ary['main_grp'] = $sales_ledger_name->main_group;
            $sales_ary['default_ledger_id'] = $sales_ledger_name->ledger_id;
        }
        $sales_ledger_id = $this->ledger_model->getGroupLedgerId($sales_ary);
        if($sales_ledger_id == 0){
            
            /*$sales_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => 'Sales',
                                                                    'subgrp_2' => '',
                                                                    'subgrp_1' => '',
                                                                    'main_grp' => 'Sales group',
                                                                    'amount' => 0
                                                                ));*/
        }
        $ledgers['sales_ledger_id'] = $sales_ledger_id;

        /*$string             = 'ledger_id,customer_name,customer_mobile';
        $table              = 'customer';
        $where              = array('customer_id' => $data_main['sales_party_id']);
        $customer_data      = $this->general_model->getRecords($string , $table , $where , $order = "");
        $customer_name = $customer_data[0]->customer_name;*/
        $customer_ledger_id = $data_main['customer_ledger_id'];

        if(!$customer_ledger_id){
            $default_customer_id = $sales_ledger['CUSTOMER'];
            $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
                
            $customer_ary = array(
                            'ledger_name' => $data_main['mobile_number'],
                            'second_grp' => '',
                            'primary_grp' => 'Sundry Debtors',
                            'main_grp' => 'Current Assets',
                            'default_ledger_id' => 0,
                            'default_value' => $data_main['mobile_number'],
                            'amount' => 0
                        );
            if(!empty($customer_ledger_name)){
                $customer_ledger = $customer_ledger_name->ledger_name;
                $customer_ledger = str_ireplace('{{X}}',$data_main['mobile_number'], $customer_ledger);
                $customer_ary['ledger_name'] = $customer_ledger;
                $customer_ary['primary_grp'] = $customer_ledger_name->sub_group_1;
                $customer_ary['second_grp'] = $customer_ledger_name->sub_group_2;
                $customer_ary['main_grp'] = $customer_ledger_name->main_group;
                $customer_ary['default_ledger_id'] = $customer_ledger_name->ledger_id;
            }
            $customer_ledger_id = $this->ledger_model->getGroupLedgerId($customer_ary);
        }
        /*$customer_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => $customer_name,
                                            'subgrp_2' => 'Sundry Debtors',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Current Assets',
                                            'amount' =>  0
                                        ));*/
        /*$payment_ledger = $data_main['payment_method'];
        $receipt_ledger = $this->config->item('receipt_ledger');
        if(strtolower($payment_ledger) == 'bank' ) $payment_ledger = 'Bank A/C';
        $default_payment_id = $receipt_ledger['Other_Payment'];
        if (strtolower($data_main['payment_method']) == "cash"){
            $default_payment_id = $receipt_ledger['Cash_Payment'];
        }

        $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
                
        $default_payment_ary = array(
                        'ledger_name' => $payment_ledger,
                        'second_grp' => '',
                        'primary_grp' => 'Cash & Cash Equivalent',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => 0,
                        'amount' => 0
                    );
        if(!empty($default_payment_name)){
            $default_led_nm = $default_payment_name->ledger_name;
            $default_payment_ary['ledger_name'] = str_ireplace('{{PAYMENT_MODE}}',$payment_ledger, $default_led_nm);
            $default_payment_ary['primary_grp'] = $default_payment_name->sub_group_1;
            $default_payment_ary['second_grp'] = $default_payment_name->sub_group_2;
            $default_payment_ary['main_grp'] = $default_payment_name->main_group;
            $default_payment_ary['default_ledger_id'] = $default_payment_name->ledger_id;
        }
        $bank_id = $this->ledger_model->getGroupLedgerId($default_payment_ary);*/
        $ledgers['customer_ledger_id'] = $customer_ledger_id;
        $ledger_from                   = $customer_ledger_id;
        $ledgers['ledger_from'] = $data_main['from_account'] = $ledger_from;
        $ledgers['ledger_to']  = $data_main['to_account'] = $sales_ledger_id;
        $vouchers              = array();
        $vouchers_new          = array();
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        /*if ($data_main['sales_gst_payable'] != "yes"){*/
            $grand_total = $data_main['total_pos_amount'];
        /*} else {
            $total_tax_amount = ($data_main['sales_tax_amount']);
            $grand_total      = bcsub($data_main['sales_grand_total'] , $total_tax_amount,2);
        }*/
        /*if ($data_main['sales_type_of_supply'] == "export_with_payment"){
            $total_tax_amount = ($data_main['sales_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount'] + $data_main['sales_tax_cess_amount']);
            $grand_total      = bcsub($data_main['sales_grand_total'] , $total_tax_amount,2);
        }*/
        
        $vouchers_new[] = array(
                            "ledger_from"              => $customer_ledger_id,
                            "ledger_to"                => $ledgers['ledger_to'] ,
                            "sales_voucher_id"         => '' ,
                            "voucher_amount"           => $grand_total ,
                            "converted_voucher_amount" => 0,
                            "dr_amount"                => $grand_total ,
                            "cr_amount"                => '',
                            'ledger_id'                => $customer_ledger_id
                        );
        /*$this->db->set('customer_payable_amount',$grand_total);
        $this->db->where('sales_id',$data_main['sales_id']);
        $this->db->update('sales');*/
        $sub_total = $data_main['total_amount'];
        /* discount slab */
        $discount_sum = 0;
        if ($data_main['total_discount_amount'] > 0){
           
            foreach ($js_data as $key => $value){
                $discount_sum = bcadd($discount_sum , $value['discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                if(@$value['sales_item_scheme_discount_amount']){
                    $discount_sum = bcadd($discount_sum , $value['sales_item_scheme_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                }
            }
        
            $sub_total = bcsub($sub_total , $discount_sum,2);
        }
        $converted_voucher_amount = $sub_total;
        /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
        } else{
            $converted_voucher_amount = 0;
        }*/
        $vouchers_new[] = array(
                            "ledger_from"              => $ledgers['ledger_from'],
                            "ledger_to"                => $sales_ledger_id,
                            "sales_voucher_id"         => '' ,
                            "voucher_amount"           => $sub_total ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => '' ,
                            "cr_amount"                => $sub_total,
                            'ledger_id'                => $sales_ledger_id
                        );
        
        
        if (!empty($cgst_slab_items) || !empty($sgst_slab_items)) {
            foreach ($cgst_slab_items as $key => $value)
            {
                $converted_voucher_amount = $value;
                /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                }
                else
                {
                    $converted_voucher_amount = 0;
                }*/
                if (in_array($key , $cgst_slab_minus)){
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_from'];
                }else{
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_to'];
                }
                if ($value > 0){
                    $vouchers_new[] = array(
                            "ledger_from"              => $key,
                            "ledger_to"                => $ledger_to,
                            "sales_voucher_id"         => '' ,
                            "voucher_amount"           => $value ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $dr_amount ,
                            "cr_amount"                => $cr_amount,
                            'ledger_id'                => $key
                        );
                }
            }
           
            foreach ($sgst_slab_items as $key => $value)
            {
                $converted_voucher_amount = $value;
                /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                }
                else
                {
                    $converted_voucher_amount = 0;
                }*/
                if (in_array($key , $sgst_slab_minus)) {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_from'];
                }else{
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_to'];
                }
                $vouchers_new[] = array(
                            "ledger_from"              => $key,
                            "ledger_to"                => $ledger_to,
                            "sales_voucher_id"         => '' ,
                            "voucher_amount"           => $value ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $dr_amount ,
                            "cr_amount"                => $cr_amount,
                            'ledger_id'                => $key
                        );
            }
        }
       
        /* discount slab */
        $discount_sum = 0;
        if ($data_main['total_discount_amount'] > 0){
            /*$discount_ledger_id            = $this->ledger_model->getDefaultLedger('Discount');*/
            /*$default_discount_id = $sales_ledger['Discount'];
            $discount_ledger_name = $this->ledger_model->getDefaultLedgerId($default_discount_id);
                
            $discount_ary = array(
                            'ledger_name' => 'Trade Discount Allowed',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Expenses',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($discount_ledger_name)){
                $discount_ary['ledger_name'] = $discount_ledger_name->ledger_name;
                $discount_ary['primary_grp'] = $discount_ledger_name->sub_group_1;
                $discount_ary['second_grp'] = $discount_ledger_name->sub_group_2;
                $discount_ary['main_grp'] = $discount_ledger_name->main_group;
                $discount_ary['default_ledger_id'] = $discount_ledger_name->ledger_id;
            }
            $discount_ledger_id = $this->ledger_model->getGroupLedgerId($discount_ary);*/
            /*$discount_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                                    'ledger_name' => 'Trade Discount Allowed',
                                                    'subgrp_1' => '',
                                                    'subgrp_2' => '',
                                                    'main_grp' => 'Direct Expenses',
                                                    'amount' =>  0
                                                ));*/
            /*$ledgers['discount_ledger_id'] = $discount_ledger_id;
            foreach ($js_data as $key => $value){
                $discount_sum = bcadd($discount_sum , $value['discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                if(@$value['sales_item_scheme_discount_amount']){
                    $discount_sum = bcadd($discount_sum , $value['sales_item_scheme_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                }
            }
            
            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $converted_voucher_amount = $discount_sum;
            } else {
                $converted_voucher_amount = 0;
            }*/
            /*$vouchers_new[] = array(
                                "ledger_from"              => $discount_ledger_id,
                                "ledger_to"                => $sales_ledger_id,
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $discount_sum ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $discount_sum ,
                                "cr_amount"                => '',
                                'ledger_id'                => $discount_ledger_id
                            );*/
        }

        if (@$data_main['cash_discount']){
            if($data_main['cash_discount'] > 0){
	            $discount_ary = array(
	                            'ledger_name' => 'Cash Discount',
	                            'second_grp' => '',
	                            'primary_grp' => '',
	                            'main_grp' => 'Indirect Expenses',
	                            'default_ledger_id' => 0,
	                            'amount' => $data_main['cash_discount']
	                        );
	           
	            $cash_discount_ledger_id = $this->ledger_model->getGroupLedgerId($discount_ary);
	            $ledgers['cash_discount_ledger_id'] = $cash_discount_ledger_id;
	           
	            $converted_voucher_amount = $data_main['cash_discount'];
	            /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
	            } else {
	                $converted_voucher_amount = 0;
	            }*/
	            $vouchers_new[] = array(
	                                "ledger_from"              => $cash_discount_ledger_id,
	                                "ledger_to"                => $sales_ledger_id,
	                                "sales_voucher_id"         => '' ,
	                                "voucher_amount"           => $data_main['cash_discount'],
	                                "converted_voucher_amount" => $converted_voucher_amount ,
	                                "dr_amount"                => $data_main['cash_discount'],
	                                "cr_amount"                => '',
	                                'ledger_id'                => $cash_discount_ledger_id
	                            );
        	}
        }
        /* discount slab ends */
        /* Round off */
        if ($data_main['round_off'] > 0 || $data_main['round_off'] < 0){
            $round_off_amount = $data_main['round_off'];
            if ($round_off_amount > 0){
                $round_off_amount = $round_off_amount;
                $dr_amount        = $round_off_amount;
                $cr_amount        = '';
                $ledger_to = $ledgers['ledger_from'];
                $default_roundoff_id = $sales_ledger['RoundOff_Given'];
                $roundoff_ledger_name = $this->ledger_model->getDefaultLedgerId($default_roundoff_id);
                    
                $round_off_ary = array(
                                'ledger_name' => 'ROUND OFF Given',
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Indirect Expenses',
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );

                /*$round_off_ary = array(
                    'ledger_name' => 'ROUND OFF Given',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Expenses',
                    'amount' =>  0
                );*/
            }else {
                $round_off_amount = ($round_off_amount * -1);
                $dr_amount        = '';
                $cr_amount        = $round_off_amount;
                $ledger_to = $ledgers['ledger_to'];
                $default_roundoff_id = $sales_ledger['RoundOff_Received'];
                $roundoff_ledger_name = $this->ledger_model->getDefaultLedgerId($default_roundoff_id);
                $round_off_ary = array(
                                'ledger_name' => 'ROUND OFF Received',
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Indirect Incomes',
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );
                /*$round_off_ary = array(
                    'ledger_name' => 'ROUND OFF Received',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Incomes',
                    'amount' =>  0
                );*/
            }
            $converted_voucher_amount = $round_off_amount;
            /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
            } else {
                $converted_voucher_amount = 0;
            }*/
            
            if(!empty($roundoff_ledger_name)){
                $round_off_ary['ledger_name'] = $roundoff_ledger_name->ledger_name;
                $round_off_ary['primary_grp'] = $roundoff_ledger_name->sub_group_1;
                $round_off_ary['second_grp'] = $roundoff_ledger_name->sub_group_2;
                $round_off_ary['main_grp'] = $roundoff_ledger_name->main_group;
                $round_off_ary['default_ledger_id'] = $roundoff_ledger_name->ledger_id;
            }
            $round_off_ledger_id = $this->ledger_model->getGroupLedgerId($round_off_ary);
            $ledgers['round_off_ledger_id'] = $round_off_ledger_id;
            $vouchers_new[] = array(
                                "ledger_from"              => $round_off_ledger_id,
                                "ledger_to"                => $ledger_to,
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $round_off_amount ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $round_off_ledger_id
                            );
        }
        $vouchers = array();
        $voucher_keys = array();
        
        if(!empty($vouchers_new)){
            foreach ($vouchers_new as $key => $value) {
                $k = 'ledger_'.$value['ledger_id'];
                if(!array_key_exists($k, $vouchers)){
                    $vouchers[$k] = $value; 
                }else{
                    $vouchers[$k]['dr_amount'] += $value['dr_amount'];
                    $vouchers[$k]['cr_amount'] += $value['cr_amount'];
                    $vouchers[$k]['voucher_amount'] += $value['voucher_amount'];
                    $vouchers[$k]['converted_voucher_amount'] += $value['converted_voucher_amount'];
                }
            }
            $vouchers = array_values($vouchers); 
        }
        /* Round off */
        return array('from_account' => $ledgers['ledger_from'],'vouchers' => $vouchers,'to_account' => $ledgers['ledger_to']);
    }

	public function sales_voucher_entry($data_main , $js_data , $action , $branch){
        $sales_voucher_module_id = $this->config->item('sales_module');
        $modules                 = $this->modules;
        $privilege               = "view_privilege";
        $section_modules         = $this->common_api->get_section_modules($sales_voucher_module_id , $modules , $privilege);
        
        $access_sub_modules    = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings       = $section_modules['access_settings'];
        /* generated voucher number */
        $resp = $this->sales_vouchers($section_modules , $data_main , $js_data , $branch);
        $vouchers = $resp['vouchers'];
        $grand_total = $data_main['total_pos_amount'];
        
        $table           = 'sales_voucher';
        $reference_key   = 'sales_voucher_id';
        $reference_table = 'accounts_sales_voucher';
        if ($action == "add"){
            /* generated voucher number */
            $primary_id      = "sales_voucher_id";
            $table_name      = $this->config->item('sales_voucher_table');
            $date_field_name = "voucher_date";
            $current_date    = $data_main['sales_date'];
            $voucher_number  = $this->common_api->generate_invoice_number_api($this,$access_settings , $primary_id , $table_name , $date_field_name , $current_date);
            /*$voucher_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);*/
            $headers = array(
                "voucher_date"      => $data_main['sales_date'] ,
                "voucher_number"    => $voucher_number ,
                "party_id"          => $data_main['customer_id'] ,
                "party_type"        => 'customer' ,
                "reference_id"      => $data_main['id'] ,
                "reference_type"    => 'pos' ,
                "reference_number"  => $data_main['pos_ref_no'] ,
                "receipt_amount"    => $grand_total ,
                "from_account"      => 'customer' ,
                "to_account"        => 'sales' ,
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "description"       => '' ,
                "added_date"        => date('Y-m-d') ,
                "added_user_id"     => $this->session->userdata('SESS_USER_ID') ,
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                "currency_id"       => $this->session->userdata('SESS_DEFAULT_CURRENCY') ,
                "note1"             => $data_main['remarks'] ,
            );
            
            $headers['converted_receipt_amount'] = $grand_total;
            /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
            }else{
                $headers['converted_receipt_amount'] = 0;
            }*/
            $this->general_model->addVouchers($table , $reference_key , $reference_table , $headers , $vouchers);
            
        } else if ($action == "edit"){
            $headers = array(
                "voucher_date"      => $data_main['sales_date'] ,
                "party_id"          => $data_main['customer_id'] ,
                "party_type"        => 'customer' ,
                "reference_id"      => $data_main['id'] ,
                "reference_type"    => 'pos' ,
                "reference_number"  => $data_main['pos_ref_no'] ,
                "receipt_amount"    => $grand_total ,
                "from_account"      => $resp['from_account'] ,
                "to_account"        => $resp['to_account'] ,
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "description"       => '' ,
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                "currency_id"       => $this->session->userdata('SESS_DEFAULT_CURRENCY') ,
                "updated_date"      => date('Y-m-d') ,
                "updated_user_id"   => $this->session->userdata('SESS_USER_ID') ,
                "note1"             => $data_main['remarks']
            );
            $headers['converted_receipt_amount'] = $grand_total;
            /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
            } else {
                $headers['converted_receipt_amount'] = 0;
            }*/
            $sales_voucher_data = $this->general_model->getRecords('sales_voucher_id' , 'sales_voucher' , array(
                'reference_id'  => $data_main['id'],'reference_type' => 'pos','delete_status' => 0));
            if ($sales_voucher_data){
                $sales_voucher_id        = $sales_voucher_data[0]->sales_voucher_id;
                $this->general_model->updateData('sales_voucher', $headers,array('sales_voucher_id' => $sales_voucher_id ));
                $string = 'accounts_sales_id,delete_status,ledger_id,voucher_amount,dr_amount,cr_amount';
                $table = 'accounts_sales_voucher';
                $where = array('sales_voucher_id' => $sales_voucher_id);
                $old_sales_voucher_items = $this->general_model->getRecords($string , $table , $where , $order
                    = "");
                $old_sales_ledger_ids = $this->common_api->getValues($old_sales_voucher_items,'ledger_id');
                $not_deleted_ids = array();
                foreach ($vouchers as $key => $value) {
                    if (($led_key = array_search($value['ledger_id'], $old_sales_ledger_ids)) !== false) {
                        unset($old_sales_ledger_ids[$led_key]);
                        $accounts_sales_id = $old_sales_voucher_items[$led_key]->accounts_sales_id;
                        array_push($not_deleted_ids,$accounts_sales_id );
                        $value['sales_voucher_id'] = $sales_voucher_id;
                        $value['delete_status']    = 0;
                        $table                     = 'accounts_sales_voucher';
                        $where                     = array('accounts_sales_id' => $accounts_sales_id );
                        $this->general_model->updateBunchVoucher($value,$where,$headers['voucher_date']);
                        $this->general_model->updateData($table , $value , $where);
                    }else{
                        $value['sales_voucher_id'] = $sales_voucher_id;
                        $table                     = 'accounts_sales_voucher';
                        $this->general_model->insertData($table , $value);
                    }
                }
                if(!empty($old_sales_voucher_items)){
                    $revert_ary = array();
                    foreach ($old_sales_voucher_items as $key => $value) {
                        if(!in_array($value->accounts_sales_id, $not_deleted_ids)){
                            $revert_ary[] = $value;
                            $table      = 'accounts_sales_voucher';
                            $where      = array('accounts_sales_id' => $value->accounts_sales_id );
                            $sales_data = array('delete_status' => 1 );
                            $this->general_model->updateData($table , $sales_data , $where);
                        }
                    }
                    if(!empty($revert_ary)) $this->general_model->revertLedgerAmount($revert_ary,$headers['voucher_date']);
                }
            }
        }
    }

	function getModulesDetails($branch_id){
		$branch_data = $this->ci->db->select('users.id as user_id,branch.branch_id,branch.financial_year_id,concat(YEAR(tbl_financial_year.from_date),"-",YEAR(tbl_financial_year.to_date)) as financial_year_title,branch.branch_default_currency,currency.currency_symbol,currency.currency_code,currency.currency_text')->from('users')->join('branch', 'users.branch_id = branch.branch_id')->join('currency', 'currency.currency_id = branch.branch_default_currency')->join('tbl_financial_year', 'tbl_financial_year.year_id = branch.financial_year_id')->where('users.id', $branch_id->user_id)->where('username !=', 'superadmin')->get()->row();
        if(!empty($branch_data)){
       
            $this->user_id = $branch_data->user_id;
            
            $this->ci->session->set_userdata('SESS_BRANCH_ID',trim($branch_id->branch_id));
            $this->ci->session->set_userdata('SESS_USER_ID',trim($branch_data->user_id));
            $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_TITLE',trim($branch_data->financial_year_title));
            $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_ID',trim($branch_data->financial_year_id));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_TEXT',trim($branch_data->currency_text));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_CODE',trim($branch_data->currency_code));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY',trim($branch_data->branch_default_currency));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_SYMBOL',trim($branch_data->currency_symbol));

            $this->SESS_FINANCIAL_YEAR_TITLE = trim($branch_data->financial_year_title);
            $this->SESS_FINANCIAL_YEAR_ID = $branch_data->financial_year_id;
            $this->SESS_DEFAULT_CURRENCY_TEXT = $branch_data->currency_text;
            $this->SESS_DEFAULT_CURRENCY_CODE = $branch_data->currency_code;
            $this->SESS_DEFAULT_CURRENCY = $branch_data->branch_default_currency;
            $this->SESS_DEFAULT_CURRENCY_SYMBOL = $branch_data->currency_symbol;
            $this->modules = $this->common_api->get_modules($branch_data);
            return true;
        }else{
            return false;
        }
	}

    public function VoucherEntry($data_main,$reference_data,$customer_name,$operation,$customer_ledger_id){
        $vouchers = array();

        $receipt_id = $data_main['receipt_id'];
        $receipt_ledger = $this->config->item('receipt_ledger');
        $exchang_gain = $exchang_loss = $discount = $other_charges = $round_off_plus = $round_off_minus = 0;
        
        if(@$reference_data['exchange_gain_loss_type']){
            if($reference_data['exchange_gain_loss_type'] == 'plus'){
                $exchang_gain += (@$reference_data['exchange_gain_loss'] ? $reference_data['exchange_gain_loss'] : 0);
            }else{
                $exchang_loss += (@$reference_data['exchange_gain_loss'] ? $reference_data['exchange_gain_loss'] : 0);
            }
        }
        $discount += (@$reference_data['discount'] ? $reference_data['discount'] : 0);
       
        $other_charges += (@$reference_data['other_charges'] ? $reference_data['other_charges'] : 0);
        if(@$reference_data['round_off_icon']){
            if($reference_data['round_off_icon'] == 'plus'){
                $round_off_plus += (@$reference_data['round_off'] ? $reference_data['round_off'] : 0);
            }else{
                $round_off_minus += (@$reference_data['round_off'] ? $reference_data['round_off'] : 0);
            }
        }

        if(!$customer_ledger_id){
            $default_customer_id = $receipt_ledger['CUSTOMER'];
            $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
                
            $customer_ary = array(
                            'ledger_name' => $customer_name,
                            'second_grp' => '',
                            'primary_grp' => 'Sundry Debtors',
                            'main_grp' => 'Current Assets',
                            'default_ledger_id' => 0,
                            'default_value' => $customer_name,
                            'amount' => 0
                        );
            if(!empty($customer_ledger_name)){
                $customer_ledger = $customer_ledger_name->ledger_name;
                /*$customer_ledger = str_ireplace('{{SECTION}}',$section_name , $customer_ledger);*/
                $customer_ledger = str_ireplace('{{X}}',$customer_name, $customer_ledger);
                $customer_ary['ledger_name'] = $customer_ledger;
                $customer_ary['primary_grp'] = $customer_ledger_name->sub_group_1;
                $customer_ary['second_grp'] = $customer_ledger_name->sub_group_2;
                $customer_ary['main_grp'] = $customer_ledger_name->main_group;
                $customer_ary['default_ledger_id'] = $customer_ledger_name->ledger_id;
            }
            $customer_ledger_id = $this->ledger_model->getGroupLedgerId($customer_ary);
        }

        /*$customer_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => $customer_name,
                                            'subgrp_2' => 'Sundry Debtors',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Current Assets',
                                            'amount' =>  0
                                        ));*/
        $vouchers[] = array(
            'receipt_voucher_id' => $data_main['receipt_id'],
            'ledger_from'        => $customer_ledger_id,
            'ledger_to'          => $customer_ledger_id,
            'ledger_id'          => $customer_ledger_id,
            'voucher_amount'     => $data_main['receipt_amount'] + $discount + $other_charges + $exchang_loss - $exchang_gain + $round_off_minus - $round_off_plus,
            'dr_amount'          => 0,
            'cr_amount'          => $data_main['receipt_amount'] + $discount + $other_charges + $exchang_loss - $exchang_gain + $round_off_minus - $round_off_plus);

       
        $payment_ledger = $data_main['payment_mode'];
        $receipt_ledger = $this->config->item('receipt_ledger');
        if(strtolower($payment_ledger) == 'bank' ) $payment_ledger = 'Bank A/C';
        $default_payment_id = $receipt_ledger['Other_Payment'];
        if (strtolower($data_main['payment_mode']) == "cash"){
            $default_payment_id = $receipt_ledger['Cash_Payment'];
        }

        $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
                
        $default_payment_ary = array(
                        'ledger_name' => $payment_ledger,
                        'second_grp' => '',
                        'primary_grp' => 'Cash & Cash Equivalent',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => 0,
                        'amount' => 0
                    );
        if(!empty($default_payment_name)){
            $default_led_nm = $default_payment_name->ledger_name;
            $default_payment_ary['ledger_name'] = str_ireplace('{{PAYMENT_MODE}}',$payment_ledger, $default_led_nm);
            $default_payment_ary['primary_grp'] = $default_payment_name->sub_group_1;
            $default_payment_ary['second_grp'] = $default_payment_name->sub_group_2;
            $default_payment_ary['main_grp'] = $default_payment_name->main_group;
            $default_payment_ary['default_ledger_id'] = $default_payment_name->ledger_id;
        }
        $bank_id = $this->ledger_model->getGroupLedgerId($default_payment_ary);
        $vouchers[] = array(
            'receipt_voucher_id' => $data_main['receipt_id'],
            'ledger_from'        => $bank_id,
            'ledger_to'          => $bank_id,
            'ledger_id'          => $bank_id,
            'voucher_amount'     => $data_main['receipt_amount'],
            'dr_amount'          => $data_main['receipt_amount'],
            'cr_amount'          => 0);//$data_main['sub_receipt_total']
        
        if($exchang_loss > 0){
            $default_exc_id = $receipt_ledger['ExcessLoss'];
            $exc_ledger_name = $this->ledger_model->getDefaultLedgerId($default_exc_id);
           
            $exc_ary = array(
                            'ledger_name' => 'Exchange Loss',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Indirect Expenses',
                            'default_ledger_id' => 0,
                            'default_value' => '',
                            'amount' => 0
                        );
            if(!empty($exc_ledger_name)){
                $exc_ledger = $exc_ledger_name->ledger_name;
                $exc_ary['ledger_name'] = $exc_ledger;
                $exc_ary['primary_grp'] = $exc_ledger_name->sub_group_1;
                $exc_ary['second_grp'] = $exc_ledger_name->sub_group_2;
                $exc_ary['main_grp'] = $exc_ledger_name->main_group;
                $exc_ary['default_ledger_id'] = $exc_ledger_name->ledger_id;
            }
            $exchange_loss_id = $this->ledger_model->getGroupLedgerId($exc_ary);
            /*$exchange_loss_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => 'Exchange Loss',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'receipt_voucher_id' => $data_main['receipt_id'],
                'ledger_from'        => $exchange_loss_id,
                'ledger_to'          => $exchange_loss_id,
                'ledger_id'          => $exchange_loss_id,
                'voucher_amount'     => $exchang_loss,
                'dr_amount'          => $exchang_loss,
                'cr_amount'          => 0);
        }
        
        if($exchang_gain > 0){
            $default_exc_id = $receipt_ledger['ExcessGain'];
            $exc_ledger_name = $this->ledger_model->getDefaultLedgerId($default_exc_id);
           
            $exc_ary = array(
                            'ledger_name' => 'Exchange Gain',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Indirect Incomes',
                            'default_ledger_id' => 0,
                            'default_value' => '',
                            'amount' => 0
                        );
            if(!empty($exc_ledger_name)){
                $exc_ledger = $exc_ledger_name->ledger_name;
                $exc_ary['ledger_name'] = $exc_ledger;
                $exc_ary['primary_grp'] = $exc_ledger_name->sub_group_1;
                $exc_ary['second_grp'] = $exc_ledger_name->sub_group_2;
                $exc_ary['main_grp'] = $exc_ledger_name->main_group;
                $exc_ary['default_ledger_id'] = $exc_ledger_name->ledger_id;
            }
            $exchange_gain_id = $this->ledger_model->getGroupLedgerId($exc_ary);
            /*$exchange_gain_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => 'Exchange Gain',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Incomes',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'receipt_voucher_id' => $data_main['receipt_id'],
                'ledger_from'        => $exchange_gain_id,
                'ledger_to'          => $exchange_gain_id,
                'ledger_id'          => $exchange_gain_id,
                'voucher_amount'     => $exchang_gain,
                'dr_amount'          => 0,
                'cr_amount'          => $exchang_gain);
        }
        
        if($discount > 0){
            $default_dis_id = $receipt_ledger['Discount'];
            $dis_ledger_name = $this->ledger_model->getDefaultLedgerId($default_dis_id);
           
            $dis_ary = array(
                            'ledger_name' => 'Discount Allowed',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Indirect Expenses',
                            'default_ledger_id' => 0,
                            'default_value' => '',
                            'amount' => 0
                        );
            if(!empty($dis_ledger_name)){
                $dis_ledger = $dis_ledger_name->ledger_name;
                $dis_ary['ledger_name'] = $dis_ledger;
                $dis_ary['primary_grp'] = $dis_ledger_name->sub_group_1;
                $dis_ary['second_grp'] = $dis_ledger_name->sub_group_2;
                $dis_ary['main_grp'] = $dis_ledger_name->main_group;
                $dis_ary['default_ledger_id'] = $dis_ledger_name->ledger_id;
            }
            $discount_id = $this->ledger_model->getGroupLedgerId($dis_ary);
            /*$discount_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => 'Discount Allowed',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'receipt_voucher_id' => $data_main['receipt_id'],
                'ledger_from'        => $discount_id,
                'ledger_to'          => $discount_id,
                'ledger_id'          => $discount_id,
                'voucher_amount'     => $discount,
                'dr_amount'          => $discount,
                'cr_amount'          => 0);
        }
       
        if($other_charges > 0){
            $default_other_id = $receipt_ledger['Other_Charges'];
            $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
           
            $other_ary = array(
                            'ledger_name' => 'Other Charges',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Indirect Expenses',
                            'default_ledger_id' => 0,
                            'default_value' => '',
                            'amount' => 0
                        );
            if(!empty($other_ledger_name)){
                $other_ledger = $other_ledger_name->ledger_name;
                $other_ary['ledger_name'] = $other_ledger;
                $other_ary['primary_grp'] = $other_ledger_name->sub_group_1;
                $other_ary['second_grp'] = $other_ledger_name->sub_group_2;
                $other_ary['main_grp'] = $other_ledger_name->main_group;
                $other_ary['default_ledger_id'] = $other_ledger_name->ledger_id;
            }
            $other_charges_id = $this->ledger_model->getGroupLedgerId($other_ary);
            /*$other_charges_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => 'Other Charges',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'receipt_voucher_id' => $data_main['receipt_id'],
                'ledger_from'        => $other_charges_id,
                'ledger_to'          => $other_charges_id,
                'ledger_id'          => $other_charges_id,
                'voucher_amount'     => $other_charges,
                'dr_amount'          => $other_charges,
                'cr_amount'          => 0);
        }
       
        if($round_off_plus > 0){
            $default_roundoff_id = $receipt_ledger['RoundOff_Received'];
            $roundoff_ledger_name = $this->ledger_model->getDefaultLedgerId($default_roundoff_id);
                
            $round_off_ary = array(
                            'ledger_name' => 'ROUND OFF Received',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Indirect Incomes',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );

            if(!empty($roundoff_ledger_name)){
                $round_off_ary['ledger_name'] = $roundoff_ledger_name->ledger_name;
                $round_off_ary['primary_grp'] = $roundoff_ledger_name->sub_group_1;
                $round_off_ary['second_grp'] = $roundoff_ledger_name->sub_group_2;
                $round_off_ary['main_grp'] = $roundoff_ledger_name->main_group;
                $round_off_ary['default_ledger_id'] = $roundoff_ledger_name->ledger_id;
            }
            $round_off_plus_id = $this->ledger_model->getGroupLedgerId($round_off_ary);

            /*$round_off_plus_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => 'ROUND OFF',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Incomes',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'receipt_voucher_id' => $data_main['receipt_id'],
                'ledger_from'        => $round_off_plus_id,
                'ledger_to'          => $round_off_plus_id,
                'ledger_id'          => $round_off_plus_id,
                'voucher_amount'     => $round_off_plus,
                'dr_amount'          => 0,
                'cr_amount'          => $round_off_plus);
        }
        
        if($round_off_minus > 0){
            $default_roundoff_id = $receipt_ledger['RoundOff_Given'];
            $roundoff_ledger_name = $this->ledger_model->getDefaultLedgerId($default_roundoff_id);
                
            $round_off_ary = array(
                                'ledger_name' => 'ROUND OFF Given',
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Indirect Expenses',
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );

            if(!empty($roundoff_ledger_name)){
                $round_off_ary['ledger_name'] = $roundoff_ledger_name->ledger_name;
                $round_off_ary['primary_grp'] = $roundoff_ledger_name->sub_group_1;
                $round_off_ary['second_grp'] = $roundoff_ledger_name->sub_group_2;
                $round_off_ary['main_grp'] = $roundoff_ledger_name->main_group;
                $round_off_ary['default_ledger_id'] = $roundoff_ledger_name->ledger_id;
            }

            $round_off_minus_id = $this->ledger_model->getGroupLedgerId($round_off_ary);

            /*$round_off_minus_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => 'ROUND OFF Given',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'receipt_voucher_id' => $data_main['receipt_id'],
                'ledger_from'        => $round_off_minus_id,
                'ledger_to'          => $round_off_minus_id,
                'ledger_id'          => $round_off_minus_id,
                'voucher_amount'     => $round_off_minus,
                'dr_amount'          => $round_off_minus,
                'cr_amount'          => 0);
        }
       
        if(!empty($vouchers)){

            if ($operation == "add"){
                foreach ($vouchers as $key => $value){
                    $this->db->insert('accounts_receipt_voucher', $value); 
                    $update_voucher = array();
                    $update_voucher['ledger_id'] = $value['ledger_id'];
                    $update_voucher['voucher_amount'] = $value['voucher_amount'];
                    if($value['dr_amount'] > 0){
                        $update_voucher['amount_type'] = 'DR';
                    }else{
                        $update_voucher['amount_type'] = 'CR';
                    }
                    $update_voucher['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
                    $this->general_model->addBunchVoucher($update_voucher,$data_main['voucher_date']);
                } 
                /*$this->db->insert_batch('accounts_receipt_voucher',$vouchers);
                $this->general_model->addVouchers($table , $reference_key , $reference_table , $headers , $vouchers);*/
            
            }elseif ($operation == "edit"){
                
                $old_voucher_items = $this->general_model->getRecords('*', 'accounts_receipt_voucher', array('receipt_voucher_id' => $receipt_id,'delete_status' => 0));
                
                $old_sales_ledger_ids = array();
                if(!empty($old_voucher_items)){
                    foreach ($old_voucher_items as $k => $value) {
                        array_push($old_sales_ledger_ids, $value->ledger_id);
                    }
                }
                /*echo "<pre>";
                print_r($old_voucher_items);
                print_r($vouchers);
                 exit();*/
       
                $not_deleted_ids = array();
                foreach ($vouchers as $key => $value) {
                    if (($led_key = array_search($value['ledger_id'], $old_sales_ledger_ids)) !== false) {
                        unset($old_sales_ledger_ids[$led_key]);
                        $accounts_receipt_id = $old_voucher_items[$led_key]->accounts_receipt_id;
                        array_push($not_deleted_ids,$accounts_receipt_id );
                        $value['receipt_voucher_id'] = $receipt_id;
                        $value['delete_status']    = 0;
                        $where = array('receipt_voucher_id' => $accounts_receipt_id );
                        $post_data = array('data' => $value,
                                            'where' => $where,
                                            'voucher_date' => $data_main['voucher_date'],
                                            'table' => 'receipt_voucher',
                                            'sub_table' => 'accounts_receipt_voucher',
                                            'primary_id' => 'receipt_id',
                                            'sub_primary_id' => 'receipt_voucher_id'
                                        );
                        $this->general_model->updateBunchVoucherCommon($post_data);
                        $this->general_model->updateData('accounts_receipt_voucher' , $value , array('accounts_receipt_id' => $accounts_receipt_id ));
                    }else{
                        $value['receipt_voucher_id'] = $receipt_id;
                        $table                     = 'accounts_receipt_voucher';
                        $this->general_model->insertData($table , $value);
                    }
                }

                if(!empty($old_voucher_items)){
                    $revert_ary = array();
                    
                    foreach ($old_voucher_items as $key => $value) {
                        if(!in_array($value->accounts_receipt_id, $not_deleted_ids)){
                            $revert_ary[] = $value;
                            $table      = 'accounts_receipt_voucher';
                            $where      = array('accounts_receipt_id' => $value->accounts_receipt_id );
                            $sales_data = array('delete_status' => 1 );
                            $this->general_model->updateData($table , $sales_data , $where);
                        }
                    }
                    
                    if(!empty($revert_ary)) $this->general_model->revertLedgerAmount($revert_ary,$data_main['voucher_date']);
                }
                /* Delete old ledgers */
            }
        }
    }
}
?>