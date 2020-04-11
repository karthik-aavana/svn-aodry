<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_methods extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model(['general_model']);
        $this->load->library('SSP');
	}

	function index(){
		$data = array();
		$payments = $this->db->query("SELECT * FROM payment_methods");
		$data['payments'] = $payments->result();
		$this->load->view("super_admin/billing/payment_method",$data);
	}

	function billing_list(){
		$data = array();
		$payments = $this->db->query("SELECT * FROM payment_methods");
		$data['payments'] = $payments->result();
		$this->load->view("super_admin/billing/billing_list",$data);
	}

	function getBillingInfo(){
		$columns = array(
                0 => 'firm_company_code',
                1  => 'firm_name',
                2  => 'package',
                3  => 'payment_status',
                4  => 'activation_date',
                5  => 'end_date',
                6  => 'amount',
                6  => 'package_status',
                7  => 'bill_id');
		

        $limit               = $this->input->post('length');
        $start               = $this->input->post('start');
        $order               = $columns[$this->input->post('order')[0]['column']];
        $dir                 = $this->input->post('order')[0]['dir'];
        $list_data           = $this->common->billing_info_list($order, $dir);
        $list_data['search'] = 'all';
        $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
        $totalFiltered       = $totalData;

        if (empty($this->input->post('search')['value'])){
            $list_data['limit']  = $limit;
            $list_data['start']  = $start;
            $list_data['search'] = 'all';
            $posts               = $this->general_model->getPageJoinRecords($list_data);

        }else{
            $search              = $this->input->post('search')['value'];
            $list_data['limit']  = $limit;
            $list_data['start']  = $start;
            $list_data['search'] = $search;
            $posts               = $this->general_model->getPageJoinRecords($list_data);
            $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
        }

        $send_data = array();
        
        if (!empty($posts)){
            foreach ($posts as $post){
                $bill_id                 = $this->encryption_url->encode($post->bill_id);
                $nestedData['firm_company_code'] = $post->firm_company_code;
                $nestedData['firm_name'] = $post->firm_name;
                $nestedData['payment_method'] = $post->payment_method;
                $nestedData['activation_date'] = date('d-m-Y', strtotime($post->activation_date));
                $nestedData['end_date'] = date('d-m-Y', strtotime($post->end_date));
                $nestedData['amount'] = number_format($post->amount,2);
                $payment_status = '<span class="label label-danger">Pending</span>';
                if($post->payment_status == 1) $payment_status = '<span class="label  label-success">Paid</span>';
                if($post->payment_status == 2) $payment_status = '<span class="label label-warning">Partially Paid</span>';
                if($post->payment_status == 3) $payment_status = '<span class="label label-default">Failed</span>';
    
                $nestedData['payment_status'] = $payment_status;
                $checked = '';
                if($post->package_status == 1) $checked = 'checked';
                $nestedData['package_status'] =  '<label class="switch"><input type="checkbox" '.$checked.' class="checkbox disable_in" name="acc_status" data-id="'.$post->bill_id.'" onclick="return updateBillStatus($(this))"><span class="slider round"></span></label>'; //$post->package_status;
                $nestedData['action'] = "<input type='hidden' value='".$post->Id."' name='package_id'><input type='hidden' value='".$post->firm_id."' name='firm_id'><input type='hidden' value='".number_format($post->amount,2)."' name='amount'><input type='hidden' value='".date('d-m-Y', strtotime($post->activation_date))."' name='activation_date'><input type='hidden' value='".date('d-m-Y', strtotime($post->end_date))."' name='end_date'><input type='hidden' value='".$post->payment_status."' name='payment_status'><a href='javascript:void(0);' class='edit_bill' data-id='".$post->bill_id."'><i class='fa fa-pencil'></i></a>";
                
                $send_data[]          = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $send_data);
        echo json_encode($json_data);
	}

	function updateBillInfo(){
		$json = array();
		$activation_date = date('Y-m-d H:i:s',strtotime($this->input->post('activation_date')));
		$end_date = date('Y-m-d H:i:s',strtotime($this->input->post('end_date')));
    	$firm_id = $this->input->post('firm_id');
    	$bill_id = $this->input->post('id');
    	$this->data['flag'] = true;
    	$validate = array(
    					'bill_id' => $bill_id,
    					'activation_date' => $activation_date,
    					'end_date' => $end_date,
    					'firm_id' => $firm_id,
    					'package' => $this->input->post('package')
    				);

    	$this->data = $this->validatePackage($validate);

    	
    	if($this->data['flag']){
			$update_data = array('package' => $this->input->post('package'),
							'activation_date' => $activation_date,
							'end_date' => $end_date,
							'is_updated' => '1',
							'amount' => $this->input->post('amount'),
							'payment_status' => $this->input->post('payment_status'),
		 				);
			if($update_data['package'] == 1){
				$update_data['amount'] = 0;
				$update_data['payment_status'] = '1';
			}

			$billing_info = $this->db->escape_str($update_data);
        	$this->db->where('bill_id',$bill_id);
		 
		 	if($this->db->update('tbl_billing_info', $billing_info)){
		 		$this->data['msg'] = "Billing information updated successfully!";
		 		$this->data['flag'] = true;
		 		$log_data              = array(
	                'user_id'           => $firm_id ,
	                'table_id'          => $bill_id,
	                'table_name'        => 'tbl_billing_info' ,
	                'financial_year_id' => 0 ,
	                'branch_id'         => 0,
	                'message'           => 'Billing Information updated successfully' 
	            );
	            
	            $log_table             = $this->config->item('log_table');
	            $this->general_model->insertData($log_table , $log_data);

		 	}else{
		 		$this->data['msg'] = "something went wrong!";
		 		$this->data['flag'] = false;
		 	}
		}
	 	echo json_encode($this->data);
	
	}

	public function updateBillStatus(){
		$this->data['flag'] = true;
		$sts = $this->input->post('status');
		$bill_id = $this->input->post('bill_id');
		$package = $this->input->post('package');
		/*if($sts == '1' && $package != 1){
			$this->db->select('b.bill_id');
			$this->db->from('tbl_billing_info b');
			$this->db->where('b.bill_id',$bill_id);
			$this->db->where('payment_status','1');
			$this->db->join('tbl_invoice v','b.bill_id=v.bill_id');
			$qry = $this->db->get();
			if($qry->num_rows() < 1){
				$this->data['flag'] = false;
				$this->data['msg'] = 'update payment status and upload invoice first!';
			}
		}*/
		if($this->data['flag']){
			$this->db->set('package_status',$sts);
			$this->db->where('bill_id',$bill_id);
			$this->db->update('tbl_billing_info');
			$this->data['flag'] = true;
			$this->data['msg'] = 'status updated successfully!';
			/*if($this->session->userdata('userId') != $firm_id){*/
				
				$log_data              = array(
	                'user_id'           => 1 ,
	                'table_id'          => $bill_id,
	                'table_name'        => 'tbl_billing_info' ,
	                'financial_year_id' => 0 ,
	                'branch_id'         => 0,
	                'message'           => 'Your package status updated successfully' 
	            );
	            
	            $log_table             = $this->config->item('log_table');
	            $this->general_model->insertData($log_table , $log_data);
        		
        	/*}*/
		}
		echo json_encode($this->data);
	}

	function validatePackage($d){
    	$package = $d['package'];
    	$activation_date = $d['activation_date'];
    	$end_date = $d['end_date'];
    	$firm_id = $d['firm_id'];
    	$bill_id = $d['bill_id'];
    	
		$this->data['flag'] = true;
		if(strtotime($end_date) <= strtotime($activation_date)){
    		$this->data['flag'] = false;
    		$this->data['msg'] = 'Enter valid date!';
    	}

		if($this->data['flag'] && strtolower($package) == 1){
			$datediff = strtotime($end_date) - strtotime($activation_date);
			$diff = round($datediff / (60 * 60 * 24));
			
			/*if($diff > 30){
				$this->data['flag'] = false;
				$this->data['msg'] = "Trial should not greater than 30 days!";
			}else{*/
				$this->db->select('bill_id');
	        	$this->db->where('package ',1);
	        	$this->db->where('firm_id = ',$firm_id);
	        	if($bill_id != '0')$this->db->where('bill_id != ',$bill_id);
	        	$qry = $this->db->get('tbl_billing_info');
	        	if($qry->num_rows() > 0){
	        		$this->data['flag'] = false;
	        		$this->data['msg'] = "Trail already exist for this company!";
	        	}
			/*}*/
		}
		if($this->data['flag']){
	        $qry = $this->db->query("SELECT bill_id FROM tbl_billing_info WHERE ('{$end_date}' BETWEEN activation_date AND end_date) AND firm_id='{$firm_id}' AND bill_id != '{$bill_id}'");

	        if($qry->num_rows() > 0){
	        	$this->data['msg'] = "Plan already running for this duration!";
				$this->data['flag'] = false;
	        }else{
	        	$qry = $this->db->query("SELECT bill_id FROM tbl_billing_info WHERE ('{$activation_date}' BETWEEN activation_date AND end_date) AND firm_id='{$firm_id}' AND bill_id != '{$bill_id}'");
	        	if($qry->num_rows() > 0){
		        	$this->data['msg'] = "Plan already running for this duration!";
					$this->data['flag'] = false;
				}
	        }
	    }

	    if($bill_id != '0' && $this->data['flag'] && $package == 'Trial'){

	    	$this->db->select('bill_id');
	    	$this->db->where('bill_id',$bill_id);
	    	$this->db->where('package !=',1);
	    	$t = $this->db->get('tbl_billing_info');
	    	if($t->num_rows() > 0){

	    		$this->data['msg'] = "You can't add trial for this company!";
				$this->data['flag'] = false;
	    	}
	    }

	    if($this->data['flag']){
	    	/* Update free package date if overlap with others */
    		$this->db->select('bill_id');
	        $this->db->where('end_date > ',$activation_date);
	        $this->db->where('package ',1);
	        $this->db->where('firm_id = ',$firm_id);
	        if($bill_id != '0')$this->db->where('bill_id != ',$bill_id);
	        $qry = $this->db->get('tbl_billing_info');
	        if($qry->num_rows() > 0){
	        	$this->db->set('end_date',$activation_date);
		        $this->db->where('end_date > ',$activation_date);
		        $this->db->where('package ',1);
		        $this->db->where('firm_id = ',$firm_id);
		        if($bill_id != '0')$this->db->where('bill_id != ',$bill_id);
		        $this->db->update('tbl_billing_info');
	        }
	        /* end */
	    }

	    return $this->data;
    }

    public function edit($id){
    	$data = array();
    	$package_detail = $this->db->query("SELECT * FROM payment_methods WHERE Id = '{$id}'");
		$payments = $this->db->query("SELECT * FROM tbl_package_modules WHERE package_id = '{$id}' ");
		$data['package_detail'] = $package_detail->row();
		$data['selected_modules'] = $payments->result();
		/*echo "<pre>";
		print_r($data);*/
		$data['modules'] = $this->db->select('m.*')->from('modules m')->where('m.delete_status', 0)->get()->result();

		$this->load->view("super_admin/billing/edit_package",$data);
    }

    function package_update(){
    	$date = date('Y-m-d');
    	$package_id = $this->input->post('package_id');
    	$valid_days = $this->input->post('valid_days');
    	$amounts = $this->input->post('amounts');
    	$modules = $this->input->post('module_id');
    	$this->db->query("UPDATE payment_methods SET valid_days='{$valid_days}',amounts='{$amounts}' WHERE Id='{$package_id}' ");
    	$this->db->query("DELETE FROM tbl_package_modules WHERE package_id='{$package_id}'");
    	if(!empty($modules)){
    		$insert_module = array();
    		foreach ($modules as $key => $value) {
    			$insert_module[] = array('package_id'=>$package_id,'module_id'=>$value,'added_date'=>$date,'updated_date'=>$date);
    		}
    		$this->db->insert_batch('tbl_package_modules',$insert_module);
    	}
    	redirect("superadmin/payment_methods", 'refresh');
    }
}