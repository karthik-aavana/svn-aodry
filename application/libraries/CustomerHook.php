<?php
class CustomerHook {
	public function __construct()
    {
        $this->ci = &get_instance();
    }

	public function CreateCustomer($data){
		$cust_data = array();
		$cust_data['customer_id'] = $data['customer_id'];
		$cust_data['email'] = $data['customer_email'];
		$cust_data['first_name'] = $data['customer_name'];
		$cust_data['username'] = $data['customer_name'];
		$cust_data['billing'] = array(
								'first_name' => $data['customer_name'],
								'company'   => $data['customer_name'],
								'address_1' => $data['customer_address'],
								'city' 		=> $this->getCityName($data['customer_city_id']),
								'state'	=> $this->getStateName($data['customer_state_id']),
								'country' => $this->getCountryName($data['customer_country_id']),
								'email'	=> $data['customer_email'],
								'phone' => $data['customer_mobile'],
								'postcode' => $data['customer_postal_code']
							);
		$cust_data['shipping'] = $cust_data['billing'];

        $user_id = $this->ci->session->userdata('SESS_USER_ID');
        $branch_id = $this->ci->session->userdata('SESS_BRANCH_ID');
        //ecom url 
        $ecom_url = $this->ci->general_model->getRecords('ecom_url', 'branch', array(
                    'branch_id'     => $branch_id,
                    'ecommerce'  => '1'));
        $url = $ecom_url[0]->ecom_url;

        //user detail
        $branch_detail = $this->ci->general_model->getRecords('*', 'users', array(
                    'id'     => $user_id));

        /*$branch_detail = $this->ci->db->query("SELECT email, password, branch_code FROM `users` WHERE id = {$user_id}");
        $branch_detail = $branch_detail->row();*/

        //login code
        $login_code = $this->ci->general_model->getRecords('token', 'ecom_branch_setting', array(
                    'branch_id'     => $branch_id));

        $data = array(
            'Method' => 'CreateCustomer',
            'branch' => array(
                'User' => $branch_detail[0]->email,
                'Password' => base64_encode('123456'),
                'Code' => $branch_detail[0]->branch_code,
                'LoginCode' => $login_code[0]->token
            ),
            'data' => $cust_data
        );
		/*$data = array(
                'Method' => 'CreateCustomer',
                'branch' => array(
                    'User' => 'credittest12@gmail.com',
                    'Password' => base64_encode('123456'),
                    'Code' => 'CODE054',
                    'LoginCode' => ''
                ),
                'data' => $cust_data
            );*/

		$url = $url.'/CreateCustomer';
		$result = $this->ci->common->postCurlData($url,$cust_data);
        $result = json_decode($result,true);
        $look_up_ary = array();
        if(!empty($result)  && @$result['status']){
            if($result['status'] == 200){
                $resp = $result['data'];
                $look_up_ary = array(
                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                    'aodry_customer_id' => $data['customer_id'],
                                    'woo_customer_id' => $resp['customer_id'],
                                    'email' => $data['email'],
                                    'added_date' => date('Y-m-d'),
                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                );
               	
            }else{
            	$look_up_ary = array(
                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                    'aodry_customer_id' => $data['customer_id'],
                                    'email' => $data['email'],
                                    'status' => $result['status'],
                                    'response' => $result['message'],
                                    'added_date' => date('Y-m-d'),
                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                );
            }
            $this->db->insert('ecom_customer_sync', $look_up_ary);

            $logs = array('action_name' => 'CreateCustomer','action_id'=> $data['customer_id'],'status' => $resp['status'],'response' => $resp['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));

            $this->ci->db->insert('ecom_sync_logs',$logs);
        }
	}

	public function UpdateCustomer($data){
		$cust_data = array();
		$cust_data['customer_id'] = $data['customer_id'];
		$cust_data['email'] = $data['customer_email'];
		$cust_data['first_name'] = $data['customer_name'];
		$cust_data['username'] = $data['customer_name'];
		$cust_data['billing'] = array(
								'first_name' => $data['customer_name'],
								'company'   => $data['customer_name'],
								'address_1' => $data['customer_address'],
								'city' 		=> $this->getCityName($data['customer_city_id']),
								'state'	=> $this->getStateName($data['customer_state_id']),
								'country' => $this->getCountryName($data['customer_country_id']),
								'email'	=> $data['customer_email'],
								'phone' => $data['customer_mobile'],
								'postcode' => $data['customer_postal_code']
							);
		$cust_data['shipping'] = $cust_data['billing'];

		$data = array(
                'Method' => 'UpdateCustomer',
                'branch' => array(
                    'User' => 'credittest12@gmail.com',
                    'Password' => base64_encode('123456'),
                    'Code' => 'CODE054',
                    'LoginCode' => ''
                ),
                'data' => $cust_data
            );

		$url = 'http://192.168.1.85/fashnett/wp-json/api/v1/UpdateCustomer';
		$result = $this->ci->common->postCurlData($url,$cust_data);
        $result = json_decode($result,true);
        $look_up_ary = array();
        if(!empty($result) && @$result['status']){
            if($result['status'] == 200){
                $resp = $result['data'];
                $update_data = array(
                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                    'aodry_customer_id' => $data['customer_id'],
                                    'woo_customer_id' => $resp['customer_id'],
                                    'email' => $data['email'],
                                    'added_date' => date('Y-m-d'),
                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                );
               	
            }else{
            	$update_data = array(
                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                    'aodry_customer_id' => $data['customer_id'],
                                    'email' => $data['email'],
                                    'status' => $result['status'],
                                    'response' => $result['message'],
                                    'added_date' => date('Y-m-d'),
                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                );
            }
            $this->general_model->updateData('ecom_customer_sync', $update_data, array('aodry_customer_id' => $data['customer_id']));
            
            $logs = array('action_name' => 'UpdateCustomer','action_id'=> $data['customer_id'],'status' => $resp['status'],'response' => $resp['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));

            $this->ci->db->insert('ecom_sync_logs',$logs);
        }
	}

	public function UpdateCustomerAddress($data){
		
	}

	public function getCityName($city_id){
		$this->ci->db->select('city_name');
		$this->ci->db->from('cities');
		$this->ci->db->where('city_id',$city_id);
		$city_resp = $this->ci->db->get();
		return $city_resp->row()->city_name;
	}

	public function getStateName($state_id){
		$this->ci->db->select('state_short_code');
		$this->ci->db->from('states');
		$this->ci->db->where('state_id',$state_id);
		$state_resp = $this->ci->db->get();
		return $state_resp->row()->state_short_code;
	}

	public function getCountryName($country_id){
		$this->ci->db->select('country_shortname');
		$this->ci->db->from('countries');
		$this->ci->db->where('country_id',$country_id);
		$state_resp = $this->ci->db->get();
		return $state_resp->row()->country_shortname;
	}
}