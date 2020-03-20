<?php
ini_set( 'display_errors', 0 );
require APPPATH . 'libraries/REST_Controller.php';
class ReceiptVoucherApi extends REST_Controller {
    
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
            'ion_auth',
            'form_validation'));

        $this->load->model([
            'general_model' ,
            'product_model' ,
            'service_model' ,
            'Voucher_model' ,
            'ledger_model' ]);
        
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
        try {
            $resp = $this->common_api->GetBranchDetails($post_req);
            $method = $post_req['Method'];
            if(@$resp['modules']){
                $this->modules = $resp['modules'];
                if(!empty($this->modules['modules'])){
                    $data = array();
                    $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
                    $module_id                 = $receipt_voucher_module_id;
                    $modules                   = $this->modules;
                    $privilege                 = "add_privilege";
                    $section_modules           = $this->common_api->get_section_modules($receipt_voucher_module_id, $modules, $privilege);
                    /* presents all the needed */
                    $data = array_merge($data, $section_modules);
                    $data['module_id']         = $receipt_voucher_module_id;
                    $data['receipt_voucher_module_id'] = $receipt_voucher_module_id;
                    $data['bank_account_module_id'] = $this->config->item('bank_account_module');
                    $data['accounts_module_id']     = $this->config->item('accounts_module');
                    $data['notes_module_id']        = $this->config->item('notes_module');
                    $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
                    $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
                    $access_settings = $section_modules['access_settings'];
                    $voucher_data = $post_req['data'];

                    if ($access_settings[0]->invoice_creation == "automatic"){
                            $primary_id      = "receipt_id";
                            $table_name      = $this->config->item('receipt_voucher_table');
                            $date_field_name = "voucher_date";
                            $current_date    = date('Y-m-d');
                            $voucher_number  = $this->common_api->generate_invoice_number_api($this,$access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                    } else{
                        $voucher_number = $voucher_data['sales_invoice_number'];
                    }
                    
                    $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array('customer_id' => $voucher_data['aodry_customer_id']));
                    $customer_name = $customer[0]->customer_name;
                    $customer_ledger_id = $customer[0]->ledger_id;

                    $receipt_data = array(
                        "voucher_date"            => date('Y-m-d'),
                        "voucher_number"          => $voucher_number,
                        "party_id"                => $voucher_data['aodry_customer_id'],
                        "party_type"              => 'customer',
                        "reference_id"            => $voucher_data['sales_invoice_id'],
                        "reference_type"          => 'Sales',
                        "reference_number"        => $voucher_data['sales_invoice_number'],
                        "from_account"            => $voucher_data['payment_mode'],
                        "to_account"              => 'customer-' . $customer_name,
                        "imploded_receipt_amount" => $voucher_data['total_amount'],
                        "invoice_balance_amount"  => $voucher_data['total_amount'],
                        "invoice_paid_amount"     => $voucher_data['total_amount'],
                        "invoice_total"           => $voucher_data['total_amount'],
                        "receipt_amount"          => $voucher_data['total_amount'],
                        "payment_mode"            => $voucher_data['payment_mode'],
                        "payment_via"             => $voucher_data['payment_mode'],
                        "reff_number"             => '',
                        "financial_year_id"       => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        "bank_name"               => '',
                        "cheque_number"           => '',
                        "cheque_date"             => '',
                        "description"             => '',
                        "added_date"              => date('Y-m-d'),
                        "added_user_id"           => $this->session->userdata('SESS_USER_ID'),
                        "branch_id"               => $this->session->userdata('SESS_BRANCH_ID'),
                        "currency_id"             => '',
                        "updated_date"            => "",
                        "updated_user_id"         => "",
                        "note1"                  => (@$voucher_data['Note1'] ? trim($voucher_data['Note1']) : ''),
                        "note2"                  => (@$voucher_data['Note2'] ? trim($voucher_data['Note2']) : '')
                    );

                    /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
                        $receipt_data['converted_receipt_amount']          = $receipt_grand_total;
                        $receipt_data['imploded_converted_receipt_amount'] = $receipt_grand_total;
                    }else {
                        $receipt_data['converted_receipt_amount']          = 0;
                        $receipt_data['imploded_converted_receipt_amount'] = 0;
                    }*/

                    if ($voucher_data['payment_mode'] == "cash"){
                        $receipt_data['voucher_status'] = "0";
                    } else{
                        $receipt_data['voucher_status'] = "1";
                    }

                    $data_main = array_map('trim', $receipt_data);

                    $receipt_voucher_table = $this->config->item('receipt_voucher_table');
                    if ($receipt_id = $this->general_model->insertData($receipt_voucher_table, $data_main)) {
                        $reference_data = array();                
                                $reference_data[] = array('receipt_id' => $receipt_id,
                                                        'reference_id' => $voucher_data['sales_invoice_id'],
                                                        'receipt_amount' => $voucher_data['total_amount'],
                                                        'Invoice_total_received' => 0,
                                                        'Invoice_pending' => 0,
                                                        'exchange_gain_loss' => 0,
                                                        'exchange_gain_loss_type' => 0,
                                                        'discount' => 0,
                                                        'other_charges' => 0,
                                                        'round_off' => 0,
                                                        'round_off_icon' => 0,
                                                        'receipt_total_paid' => 0
                                                    );
                           
                        $this->db->insert_batch('receipt_invoice_reference',$reference_data);
                        $log_data = array(
                            'user_id'           => $this->session->userdata('SESS_USER_ID'),
                            'table_id'          => $receipt_id,
                            'table_name'        => $receipt_voucher_table,
                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                            'message'           => 'Receipt Voucher Inserted');
                        $data_main['receipt_id'] = $receipt_id;
                        $data_main['sub_receipt_total'] = $voucher_data['total_amount'];
                        $log_table               = $this->config->item('log_table');
                        $this->general_model->insertData($log_table, $log_data);
                        if (in_array($data['accounts_module_id'], $data['active_add'])){

                            if (in_array($data['accounts_sub_module_id'], $data['access_sub_modules'])){
                                $this->VoucherEntry($data_main,$reference_data,$customer_name , "add",$customer_ledger_id);
                            }
                        }    
                        $resp['status'] = 200;
                        $resp['message'] = 'Receipt Voucher addded successfully!';
                    }
                }else{
                    $resp['status'] = 404;
                    $resp['message'] = 'User access denied!';
                } 
            }
        }
        catch(Exception $e){

        }

        // everything else results in a 404 Not Found
        if ($resp['status'] == 200) {
            $this->response($resp, REST_Controller::HTTP_OK);
        }else{
            $this->response($resp, REST_Controller::HTTP_NOT_FOUND);
        }
        exit();
    }

    public function VoucherEntry($data_main,$reference_data,$customer_name,$operation,$customer_ledger_id){
        $vouchers = array();

        $receipt_id = $data_main['receipt_id'];
        $receipt_ledger = $this->config->item('receipt_ledger');
        $exchang_gain = $exchang_loss = $discount = $other_charges = $round_off_plus = $round_off_minus = 0;
        
        foreach ($reference_data as $key => $value) {
            if(@$value['exchange_gain_loss_type']){
                if($value['exchange_gain_loss_type'] == 'plus'){
                    /*bcadd($exchang_gain, $value['exchange_gain_loss'],2);*/
                    $exchang_gain += (@$value['exchange_gain_loss'] ? $value['exchange_gain_loss'] : 0);
                }else{
                    /*bcadd($exchang_loss, $value['exchange_gain_loss'],2);*/
                    $exchang_loss += (@$value['exchange_gain_loss'] ? $value['exchange_gain_loss'] : 0);
                }
            }
            $discount += (@$value['discount'] ? $value['discount'] : 0);
           /* bcadd($discount, $value['discount'],2);*/
            $other_charges += (@$value['other_charges'] ? $value['other_charges'] : 0);
            /*bcadd($other_charges, $value['other_charges'],2);*/
            if(@$value['round_off_icon']){
                if($value['round_off_icon'] == 'plus'){
                    $round_off_plus += (@$value['round_off'] ? $value['round_off'] : 0);
                    /*bcadd($round_off_plus, $value['round_off'],2);*/
                }else{
                    $round_off_minus += (@$value['round_off'] ? $value['round_off'] : 0);
                    /*bcadd($round_off_minus, $value['round_off'],2);*/
                }
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

        $payment_ledger = trim($data_main['payment_mode']);
        $payment_ledger = strtolower($payment_ledger);

        if ($payment_ledger != "cash" && $payment_ledger != "bank" && $payment_ledger != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $payment_ledger);
            $payment_ledger          = $bank_acc_payment_mode[0];
            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_ledger));
            $bank_id = $ledger_bank_acc[0]->ledger_id; 
            /*$from_acc    = $bank_acc_payment_mode[1];*/
        }else{
            if($payment_ledger == 'bank' ) $payment_ledger = 'Bank A/C';
            $default_payment_id = $receipt_ledger['Other_Payment'];
            if ($payment_ledger == "cash"){
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

            /*$bank_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => $payment_ledger,
                                                'subgrp_2' => (strtolower($payment_ledger) == 'cash' ? 'Cash & Cash Equivalent' : ''),
                                                'subgrp_1' => '',
                                                'main_grp' => 'Current Assets',
                                                'amount' =>  0
                                            ));*/
        }

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
                
                $old_voucher_items = $this->general_model->getRecords('*', 'accounts_receipt_voucher', array('receipt_voucher_id' => $receipt_id,'delete_status'      => 0));
                /*echo "<pre>";
                print_r($old_voucher_items);
                print_r($vouchers);
                 exit();*/
                $old_sales_ledger_ids = $this->getValues($old_voucher_items,'ledger_id');
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

                /*$this->db->where('receipt_voucher_id',$data_main['receipt_id']);
                $this->db->delete('accounts_receipt_voucher');*/
                /* Add New ledgers */
                /*$this->db->insert_batch('accounts_receipt_voucher',$vouchers);*/
                /*if ($accounts_receipt){
                    $this->general_model->updateData('accounts_receipt_voucher', $data1, array(
                        'accounts_receipt_id' => $accounts_receipt[0]->accounts_receipt_id));
                    $this->general_model->updateData('accounts_receipt_voucher', $data2, array(
                        'accounts_receipt_id' => $accounts_receipt[1]->accounts_receipt_id));
                }*/
            }
        }
    }

}