<?php
ini_set( 'display_errors', 0 );
require APPPATH . 'libraries/REST_Controller.php';
class CustomerAPIs extends REST_Controller {
	/**
     * Get All Data from this method.
     *
     * @return Response
    */
    public $branch_id = 0;
    public $user_id = 0;
    public $SESS_FINANCIAL_YEAR_TITLE = '';
    public $SESS_FINANCIAL_YEAR_ID = '';
    public $SESS_DEFAULT_CURRENCY_TEXT = '';
    public $SESS_DEFAULT_CURRENCY_CODE = '';
    public $SESS_DEFAULT_CURRENCY = '';
    public $SESS_DEFAULT_CURRENCY_SYMBOL = '';
    public $modules = array();
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array(
            'common_api',
            'common',
            'session',
            'ion_auth',
            'form_validation'));

        $this->load->model([
            'general_model' ,
            'product_model' ,
            'service_model' ,
            'Voucher_model' ,
            'ledger_model' ]);

        $this->ci = &get_instance();
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
	public function index_get($id = 0)
	{
        if(!empty($id)){
            $data = $this->db->get_where("sales", ['sales_id' => $id])->row_array();
        }else{
            $data = $this->db->get("sales")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
	}
	/**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_post(){
        
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        $post_req = json_decode(file_get_contents("php://input"),true);
        $resp = array();
        $resp = $this->common_api->GetBranchDetails($post_req);
        $method = $post_req['Method'];
        if(@$resp['modules']){
            $this->modules = $resp['modules'];
            if(!empty($this->modules['modules'])){
            	$customer_module_id = $this->config->item('customer_module');
                $data['module_id'] = $customer_module_id;
                $modules           = $this->modules;
                $privilege         = "add_privilege";
                $data['privilege'] = "add_privilege";
                $section_modules   = $this->common_api->get_section_modules($customer_module_id, $modules, $privilege);
                $access_settings          = $section_modules['access_settings'];
                $primary_id               = "customer_id";
                $table_name               = "customer";
                $date_field_name          = "added_date";
                $current_date             = date('Y-m-d');
       			
                /* presents all the needed */
                $data = array_merge($data, $section_modules); 
                $branch_id = $this->ci->session->userdata('SESS_BRANCH_ID');
                $post = $post_req['data'];
                if(@$post['email']){
                    $address = '';
                    $countryId = $stateId = $cityId = 0;
                    $phone = $postcode = '';
                    if(@$post['billing']['address_1']){
                    	$billing = $post['billing'];
                    	$address = $billing['address_1'];
                    	$countryId = $this->getCountryId($billing['country']);
                    	$stateId = $this->getStateId($billing['state']);
                    	$cityId = $this->getCityId($billing['city']);
                    	$phone = $billing['phone'];
                    	$postcode = $billing['postcode'];
                    }elseif (@$post['shipping']['address_1']) {
                    	$shipping = $post['shipping'];
                    	$address = $shipping['address_1'];
                    	$countryId = $this->getCountryId($shipping['country']);
                    	$stateId = $this->getStateId($billing['state']);
                    	$cityId = $this->getCityId($billing['city']);
                    	$phone = $shipping['phone'];
                    	$postcode = $shipping['postcode'];
                    }
	                $email = $post['email'];

                    $this->db->select('*');
                    $this->db->where('customer_email',trim($email));
                    $this->db->from('customer');
                    $qry_res = $this->db->get();
                    $exist = $qry_res->result();
                    if(!empty($exist)){
                        $method = 'UpdateCustomer';
                    }else{
                        $method = 'CreateCustomer';
                    }

	                $customer_name = $post['first_name'].(@$post['last_name'] ? $post['last_name'] : '');

                    if($method == 'CreateCustomer'){
                        $state_country_qry = $this->db->query("SELECT branch_country_id, branch_state_id, branch_city_id FROM `branch` WHERE branch_id='{$this->ci->session->userdata('SESS_BRANCH_ID')}'");
                        $state_country_res = $state_country_qry->result();

                        $branch_country_id = $state_country_res[0]->branch_country_id;
                        $branch_state_id = $state_country_res[0]->branch_state_id;
                        $branch_city_id = $state_country_res[0]->branch_city_id;

                        $customer_code = $this->common_api->generate_invoice_number_api($this,$access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                        $reference_number = $this->common_api->generate_reference_number_api($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

    	                $customer_ledger_id = 0;
    	                $sales_ledger = $this->config->item('sales_ledger');
    	                $default_customer_id = $sales_ledger['CUSTOMER'];
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

    	                $customer_data = array(
    					            "customer_name" => $customer_name,
    					            "customer_code" => $customer_code,
    					            "reference_number" => $reference_number,
    					            "reference_type" => 'customer',
    					            "customer_type" => 'one person company',
    					            "customer_address" => $address,
    					            "customer_country_id" => $branch_country_id,
    					            "customer_state_id" => $branch_state_id,
    					            "customer_city_id" => $branch_city_id,
    					            "customer_email" => $email,
    					            "added_date" => date('Y-m-d'),
    					            "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
    					            "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'),
    					            "ledger_id"  => $customer_ledger_id,
    					            "customer_mobile" => $phone,
    					            "customer_postal_code" => $postcode,
    					            "due_days" => 0
    					        ); 

    	                if ($customer_id = $this->general_model->insertData($table_name, $customer_data)) {
    			            $txt_shipping_code = $customer_code . "-1";
    			            $shipping_address_data = array(
    			                "shipping_address" => $address,
    			                "primary_address" => 'yes',
    			                "shipping_party_id" => $customer_id,
    			                "shipping_party_type" => 'customer',
    			                "department" => '',
    			                "email" => $email,
    			                "contact_number" => $phone,
    			                "added_date" => date('Y-m-d'),
    			                "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
    			                "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'),
    			                "country_id" => $branch_country_id,
    			                "state_id" => $branch_state_id,
    			                "city_id" => $branch_city_id,
    			                "shipping_code" => $txt_shipping_code,
    			                "address_pin_code" => $postcode
    			            );

    			            $table = "shipping_address";
    			            $billing_id = $this->general_model->insertData($table, $shipping_address_data);
    			            $shipping_id = $billing_id;

    			            if($post['is_shipping_same'] != '1'){
    		                	$shipping = $post['shipping'];
    		                	$address = $shipping['address_1'];
    		                	$countryId = $this->getCountryId($shipping['country']);
    		                	$stateId = $this->getStateId($billing['state']);
    		                	$cityId = $this->getCityId($billing['city']);
    		                	$phone = $shipping['phone'];
    		                	$postcode = $shipping['postcode'];
    	                		$txt_shipping_code = $customer_code . "-2";
    				            $shipping_address_data = array(
    				                "shipping_address" => $address,
    				                "primary_address" => 'no',
    				                "shipping_party_id" => $customer_id,
    				                "shipping_party_type" => 'customer',
    				                "department" => '',
    				                "email" => $email,
    				                "contact_number" => $phone,
    				                "added_date" => date('Y-m-d'),
    				                "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
    				                "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'),
    				                "country_id" => $branch_country_id,
    				                "state_id" => $branch_state_id,
    				                "city_id" => $branch_city_id,
    				                "shipping_code" => $txt_shipping_code,
    				                "address_pin_code" => $postcode
    				            );

    				            $table = "shipping_address";
    				            $shipping_id = $this->general_model->insertData($table, $shipping_address_data);
    			            }
    			        }

    			        $look_up_ary = array(
    	                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
    	                                    'aodry_customer_id' => $customer_id,
    	                                    'woo_customer_id' => $post['customer_id'],
    	                                    'email' => $email,
    	                                    'added_date' => date('Y-m-d'),
    	                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
    	                                );
    			        $this->db->insert('ecom_customer_sync', $look_up_ary);
    			        $look_up_ary['shipping_id'] = $shipping_id;
    			        $look_up_ary['billing_id'] = $billing_id;
    			        
    			        $resp['status'] = 200;
    	                $resp['message'] = 'Customer created successfully!';
    	                $resp['data'] = $look_up_ary;

    	                $logs = array('action_name' => 'CreateCustomer','action_id'=> $customer_id,'status' => $resp['status'],'response' => $resp['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));
                		$this->ci->db->insert('ecom_sync_logs',$logs);


    	            }elseif ($method == 'UpdateCustomer') {
                        $resp = array();
                        $customer_id= $exist[0]->customer_id;
                        $customer_code = $exist[0]->customer_code;
                        $customer_data = array(
                                    "customer_name" => $customer_name,
                                    "customer_address" => $address,
                                    "customer_country_id" => $countryId,
                                    "customer_state_id" => $stateId,
                                    "customer_city_id" => $cityId,
                                    "updated_date" => date('Y-m-d'),
                                    "updated_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                                    "customer_postal_code" => $postcode,
                                    "customer_mobile" => $phone,
                                );
                        $table = "customer";
                        $where = array("customer_id" => $customer_id);
                        
                        if ($this->general_model->updateData($table, $customer_data, $where)) {
                            $sales_ledger = $this->config->item('sales_ledger');
                            $default_customer_id = $sales_ledger['CUSTOMER'];
                            $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
                            if(!empty($customer_ledger_name)){
                                $customer_ledger = $customer_ledger_name->ledger_name;
                                $customer_name = str_ireplace('{{X}}',$customer_name, $customer_ledger);
                            }

                            /* Update ledger name */
                            $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$customer_name}' WHERE ledger_id='{$ledger_id}'");

                            $qry = $this->db->query("SELECT * FROM shipping_address WHERE shipping_party_type='customer' AND shipping_party_id='{$customer_id}'");
                            $num_shipp_address= $qry->num_rows();

                            if($num_shipp_address > 0){
                                $addresses = $qry->result_array();
                                $is_bill_address = $is_ship_address = 0;
                                foreach ($addresses as $key => $value) {
                                    
                                    if(strtolower(str_replace(' ', '', $value['shipping_address'])) == strtolower(str_replace(' ', '', $billing_address['address1'])) && $value['address_pin_code'] == $billing_address['zipcode']){
                                        $is_bill_address = 1;
                                        $billing_id = $value['shipping_address_id'];
                                        if($post['is_shipping_same'] == '1'){
                                            $is_ship_address = 1;
                                            $shipping_id = $billing_id;
                                        }
                                    }

                                    if($post['is_shipping_same'] != '1'){
                                        $shipping = $post['shipping'];
                                        $shipping_address = $shipping['address_1'];
                                        $postcode = $shipping['postcode'];
                                        if(strtolower(str_replace(' ', '', $value['shipping_address'])) == strtolower(str_replace(' ', '', $shipping_address)) && $value['address_pin_code'] == $postcode){
                                            $is_ship_address = 1;
                                            $shipping_id = $value['shipping_address_id'];
                                        }
                                    }
                                }

                                if(!$is_bill_address){
                                    $billing = $post['billing'];
                                    $address = $billing['address_1'];
                                    $countryId = $this->getCountryId($billing['country']);
                                    $stateId = $this->getStateId($billing['state']);
                                    $cityId = $this->getCityId($billing['city']);
                                    $phone = $billing['phone'];
                                    $postcode = $billing['postcode'];
                                    $txt_shipping_code = $customer_code . "-2";
                                    $shipping_address_data = array(
                                        "shipping_address" => $address,
                                        "primary_address" => 'no',
                                        "shipping_party_id" => $customer_id,
                                        "shipping_party_type" => 'customer',
                                        "department" => '',
                                        "email" => $email,
                                        "contact_number" => $phone,
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                        "country_id" => $countryId,
                                        "state_id" => $stateId,
                                        "city_id" => $cityId,
                                        "shipping_code" => $txt_shipping_code,
                                        "address_pin_code" => $postcode
                                    );

                                    $table = "shipping_address";
                                    $billing_id = $this->general_model->insertData($table, $shipping_address_data);
                                    if($post['is_shipping_same'] == '1') $is_ship_address = 1;
                                }

                                if(!$is_ship_address){
                                    $shipping = $post['shipping'];
                                    $address = $shipping['address_1'];
                                    $countryId = $this->getCountryId($shipping['country']);
                                    $stateId = $this->getStateId($billing['state']);
                                    $cityId = $this->getCityId($billing['city']);
                                    $phone = $shipping['phone'];
                                    $postcode = $shipping['postcode'];
                                    $txt_shipping_code = $customer_code . "-2";
                                    $shipping_address_data = array(
                                        "shipping_address" => $address,
                                        "primary_address" => 'no',
                                        "shipping_party_id" => $customer_id,
                                        "shipping_party_type" => 'customer',
                                        "department" => '',
                                        "email" => $email,
                                        "contact_number" => $phone,
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                        "country_id" => $countryId,
                                        "state_id" => $stateId,
                                        "city_id" => $cityId,
                                        "shipping_code" => $txt_shipping_code,
                                        "address_pin_code" => $postcode
                                    );

                                    $table = "shipping_address";
                                    $shipping_id = $this->general_model->insertData($table, $shipping_address_data);
                                }
                            }

                            $look_up_ary = array(
                                        'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                        'aodry_customer_id' => $customer_id,
                                        'woo_customer_id' => $post['customer_id'],
                                        'email' => $email,
                                        "updated_date"           => date('Y-m-d'),
                                        "updated_user_id"        => $this->ci->session->userdata('SESS_USER_ID')
                                    );

                            $where = array('aodry_customer_id',$customer_id);
                            $this->general_model->updateData('ecom_customer_sync', $look_up_ary, $where);
                            $look_up_ary['shipping_id'] = $shipping_id;
                            $look_up_ary['billing_id'] = $billing_id;
                        }

                        $resp['status'] = 200;
                        $resp['message'] = 'Customer updated successfully!';
                        $resp['data'] = $look_up_ary;

                        $logs = array('action_name' => 'UpdateCustomer','action_id'=> $customer_id,'status' => $resp['status'],'response' => $resp['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));

                        $this->ci->db->insert('ecom_sync_logs',$logs);
                    }
                }else{
                    $resp['status'] = 404;
                    $resp['message'] = 'Invalid email ID.';
                }
            }else{
                $resp['status'] = 404;
                $resp['message'] = 'Invalid data.';
            }
        }

        if ($resp['status'] == 200) {
            $this->response($resp, REST_Controller::HTTP_OK);
        }else{
            $this->response($resp, REST_Controller::HTTP_NOT_FOUND);
        }
        exit();
    }

    function getCountryId($countryCode){
    	$this->ci->db->select('country_id');
		$this->ci->db->from('countries');
		$this->ci->db->where('LOWER(country_shortname)',strtolower(trim($countryCode)));
		$state_resp = $this->ci->db->get();
		$resp = $state_resp->result();
		$country_id = 0;
		if(!empty($resp)){
			$country_id = $resp[0]->country_id;
		}
		return $country_id;
    }

    function getStateId($stateCode){
    	$this->ci->db->select('state_id');
		$this->ci->db->from('states');
		$this->ci->db->where('LOWER(state_short_code)',strtolower(trim($stateCode)));
		$state_resp = $this->ci->db->get();
		$resp = $state_resp->result();
		$state_id = 0;
		if(!empty($resp)){
			$state_id = $resp[0]->state_id;
		}
		return $state_id;
    }

    function getCityId($city){
    	$this->ci->db->select('city_id');
		$this->ci->db->from('cities');
		$this->ci->db->where('LOWER(city_name)',strtolower(trim($city)));
		$city_resp = $this->ci->db->get();
		$resp = $city_resp->result();
		$city_id = 0;
		if(!empty($resp)){
			$city_id = $resp[0]->city_id;
		}
		return $city_id;
    }

    /*function GetBranchDetails($post){
    	$resp = array();
    	if(@$post['Method']){
            $method = $post['Method'];
            if(@$post['branch']){
                $branch = $post['branch'];
                if(@$branch['User'] && @$branch['Password'] && @$branch['Code']){
                    $branch_code = $branch['Code'];
                    if ($this->ion_auth->login($branch['Code'], $branch['User'], base64_decode($branch['Password']),'0')) {

                        $query = $this->db->select('email,first_name,last_name, id,branch_id,branch_code, password, active, last_login')->where([
                            'email' => $branch['User'],
                            'branch_code' => $branch['Code'] ])->where('username !=', 'superadmin')->limit(1)->order_by('id', 'desc')->get('users');
                        if($query->num_rows() > 0){

                            $branch_detail = $query->result();
                
                            $this->branch_id = $branch_detail[0]->branch_id;
                           
                            $data = $this->common_api->get_default_country_state($this->branch_id);
                            
                            $branch_data = $this->db->select('users.id as user_id,branch.branch_id,branch.financial_year_id,concat(YEAR(tbl_financial_year.from_date),"-",YEAR(tbl_financial_year.to_date)) as financial_year_title,branch.branch_default_currency,currency.currency_symbol,currency.currency_code,currency.currency_text')->from('users')->join('branch', 'users.branch_id = branch.branch_id')->join('currency', 'currency.currency_id = branch.branch_default_currency')->join('tbl_financial_year', 'tbl_financial_year.year_id = branch.financial_year_id')->where('users.id', $branch_detail[0]->id)->where('username !=', 'superadmin')->get()->row();
                                
                            $this->user_id = $branch_data->user_id;
                            $this->ci->session->set_userdata('SESS_BRANCH_ID',trim($branch_detail[0]->branch_id));
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

                        }else{
                            $resp['status'] = 404;
                            $resp['message'] = 'Invalid branch detail.';
                        }
                    }else{
                        $resp['status'] = 404;
                        $resp['message'] = 'Invalid branch detail.';
                    }
                }else{
                    $resp['status'] = 404;
                    $resp['message'] = 'User details required!';
                }
            }else{
                $resp['status'] = 404;
                $resp['message'] = 'Branch details required';
            }
        }else{
            $resp['status'] = 404;
            $resp['message'] = 'Method not defined!';
        }
        return $resp;
    }*/
}