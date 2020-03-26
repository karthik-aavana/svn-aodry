<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
        /*$this->load->library('CustomerHook');*/
        //gg
    }

    public function index() {
        $customer_module_id = $this->config->item('customer_module');
        $data['customer_module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /*if($this->session->userdata('bulk_success')){
            if($this->session->flashdata('email_send') != 'success')
            $data['bulk_success'] = $this->session->userdata('bulk_success');
            $this->session->unset_userdata('bulk_success');
        }elseif ($this->session->userdata('bulk_error')) {
            $data['bulk_error'] = $this->session->userdata('bulk_error');
            $this->session->unset_userdata('bulk_error');
        }*/

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'action',
                1 => 'customer_code',
                2 => 'customer_name',
                3 => 'country',
                4 => 'state',
                5 => 'city'
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->customer_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            } 
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $customer_id = $this->encryption_url->encode($post->customer_id);
                    $nestedData['customer_code'] = $post->customer_code;
                    //$nestedData['reference_number'] = $post->reference_number;
                    $nestedData['customer_name'] = $post->customer_name;
                    // $nestedData['contact_person'] = $post->contact_person_name;
                    //$nestedData['phone']            = $post->customer_mobile;
                    //$nestedData['email']            = $post->customer_email;
                    $nestedData['country'] = $post->country_name;
                    $nestedData['state'] = $post->state_name;
                    if ($post->city_name == '') {
                        $city_name = 'Others';
                    } else {
                        $city_name = $post->city_name;
                    }
                    $nestedData['city'] = $city_name;
                    //$nestedData['added_user']       = $post->first_name . ' ' . $post->last_name;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['customer_module_id'], $data['active_edit'])) {
                        $cols .= '<span><a href="' . base_url('customer/edit/') . $customer_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    $advance_voucher = $this->general_model->getRecords('*', 'advance_voucher', array(
                        'party_id' => $post->customer_id,
                        'party_type' => 'customer',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $refund_voucher = $this->general_model->getRecords('*', 'refund_voucher', array(
                        'party_id' => $post->customer_id,
                        'party_type' => 'customer',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $receipt_voucher = $this->general_model->getRecords('*', 'receipt_voucher', array(
                        'party_id' => $post->customer_id,
                        'party_type' => 'customer',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $quotation = $this->general_model->getRecords('*', 'quotation', array(
                        'quotation_party_id' => $post->customer_id,
                        'quotation_party_type' => 'customer',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $sales = $this->general_model->getRecords('*', 'sales', array(
                        'sales_party_id' => $post->customer_id,
                        'sales_party_type' => 'customer',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $sales_credit_note = $this->general_model->getRecords('*', 'sales_credit_note', array(
                        'sales_credit_note_party_id' => $post->customer_id,
                        'sales_credit_note_party_type' => 'customer',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $sales_debit_note = $this->general_model->getRecords('*', 'sales_debit_note', array(
                        'sales_debit_note_party_id' => $post->customer_id,
                        'sales_debit_note_party_type' => 'customer',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));

                    if (in_array($data['customer_module_id'], $data['active_delete'])) {
                        if ($advance_voucher || $refund_voucher || $receipt_voucher || $quotation || $sales || $sales_credit_note || $sales_debit_note) {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal"><a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete_button btn btn-xs btn-app"><i class="fa fa-trash"></i></a></span>';
                        } else {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $customer_id . '" data-path="customer/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                    }
                    $cols .= '</div></div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('customer/list', $data);
        }
    }

    public function add() {
        $data = $this->get_default_country_state();
        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $access_settings = $data['access_settings'];
        $primary_id = "customer_id";
        $table_name = "customer";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $access_settings = $data['access_settings'];
        $primary_id = "customer_id";
        $table_name = "customer";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['reference_number'] = $this->generate_reference_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        //echo $data['invoice_number'].'<br>';
        // echo $data['reference_number'];
        //  exit;
        
        $this->load->view('customer/add', $data);
    }

    public function add_bulk_upload_customer()
    {
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/customerCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();

        if (!$this->upload->do_upload('bulk_customer')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_customer',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        }else {
            $data = $this->get_default_country_state();
            $customer_module_id = $this->config->item('customer_module');
            $data['module_id'] = $customer_module_id;
            $modules = $this->modules;
            $privilege = "add_privilege";
            $data['privilege'] = "add_privilege";
            $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

            /* presents all the needed */
            $data = array_merge($data, $section_modules);

            /* presents all the needed */
            $Updata = array('uploadData' => $this->upload->data());

            if (!empty($Updata['uploadData']['file_name'])) {
                $import_xls_file = $Updata['uploadData']['file_name'];
                $inputFileName = $path . $import_xls_file;
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);               
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    //print_r($allDataInSheet);
                    if(!empty($allDataInSheet)){
                        if(strtolower($allDataInSheet[1]['A']) == 'customer type' && strtolower($allDataInSheet[1]['B']) == 'company/firm name' && strtolower($allDataInSheet[1]['C']) == 'gst number' && strtolower($allDataInSheet[1]['D']) == 'country' && strtolower($allDataInSheet[1]['E']) == 'state' && strtolower($allDataInSheet[1]['F']) == 'city' && strtolower($allDataInSheet[1]['G']) == 'address' && strtolower($allDataInSheet[1]['H']) == 'pin code' && strtolower($allDataInSheet[1]['I']) == 'pan number' && strtolower($allDataInSheet[1]['J']) == 'contact person name' && strtolower($allDataInSheet[1]['K']) == 'contact number' && strtolower($allDataInSheet[1]['L']) == 'email' && strtolower($allDataInSheet[1]['M']) == 'due days' && strtolower($allDataInSheet[1]['N']) == 'tan number' && strtolower($allDataInSheet[1]['O']) == 'department'){
                            
                            $header_row = array_shift($allDataInSheet);

                            $country_bulk = $this->general_model->customer_bulk_country();
                            $country_bulk = array_column($country_bulk, 'country_id', 'country_name');
                            $states_bulk = $this->general_model->customer_bulk_state();
                            $states_bulk_country = array_column($states_bulk, 'country_id', 'state_name');
                            $states_bulk= array_column($states_bulk, 'state_id', 'state_name');
                            $city_bulk = $this->general_model->customer_bulk_city();
                            $city_bulk_state = array_column($city_bulk, 'state_id', 'city_name');
                            $city_bulk = array_column($city_bulk, 'city_id', 'city_name');
                            $access_settings = $data['access_settings'];
                            $primary_id = "customer_id";
                            $table_name = "customer";
                            $date_field_name = "added_date";
                            $current_date = date('Y-m-d');
                            $sales_ledger = $this->config->item('sales_ledger');
                            $default_customer_id = $sales_ledger['CUSTOMER'];
                            $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
                                
                            
                            foreach($allDataInSheet as $row){ 
                                $customer_type = strtolower(trim($row['A']));
                                $customer_name = strtolower(trim($row['B']));
                                $gst_number = trim($row['C']);
                                $country = strtolower(trim($row['D']));
                                $states = strtolower(trim($row['E']));
                                $city = strtolower(trim($row['F']));
                                $address = trim($row['G']);
                                $email = trim($row['L']);
                                $pin_code = trim($row['H']);
                                $due_days = trim($row['M']);
                                $pan_number = trim($row['I']);
                                $tan_number = trim($row['N']);
                                $department = trim($row['O']);
                                $customer_ledger_id = '';
                                $customer_country_id = '';
                                $customer_state_city_id = '';
                                $customer_state_id = '';
                                $customer_city_id = '';
                                $is_add = true;
                                $error = '';
                                $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                
                                $reference_number = $this->generate_reference_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                if($customer_type != '' && !empty($customer_type)){
                                    if($customer_type == 'company' || $customer_type == 'firm' || $customer_type == 'private limited company' || $customer_type == 'proprietorship' || $customer_type == 'partnership' || $customer_type == 'one person company' || $customer_type == 'limited liability partnership'){
                                        if($customer_type == 'firm'){
                                            $customer_type = 'individual';
                                        }
                                        if($customer_name != '' && !empty($customer_name)){
                                            $Customer_check = $this->Bulk_CustomerValidation($customer_name);
                                            if($Customer_check["rows"] <= 0){
                                                $customer_ary = array(
                                                                'ledger_name' => trim($row['B']),
                                                                'second_grp' => '',
                                                                'primary_grp' => 'Sundry Debtors',
                                                                'main_grp' => 'Current Assets',
                                                                'default_ledger_id' => 0,
                                                                'default_value' => trim($row['B']),
                                                                'amount' => 0
                                                            );
                                                if(!empty($customer_ledger_name)){
                                                    $customer_ledger = $customer_ledger_name->ledger_name;
                                                    /*$customer_ledger = str_ireplace('{{SECTION}}',$section_name , $customer_ledger);*/
                                                    $customer_ledger = str_ireplace('{{X}}',trim($row['B']), $customer_ledger);
                                                    $customer_ary['ledger_name'] = $customer_ledger;
                                                    $customer_ary['primary_grp'] = $customer_ledger_name->sub_group_1;
                                                    $customer_ary['second_grp'] = $customer_ledger_name->sub_group_2;
                                                    $customer_ary['main_grp'] = $customer_ledger_name->main_group;
                                                    $customer_ary['default_ledger_id'] = $customer_ledger_name->ledger_id;
                                                }
                                                $customer_ledger_id = $this->ledger_model->getGroupLedgerId($customer_ary);
                                                /*$customer_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                        'ledger_name' => trim($row['B']),
                                                        'subgrp_2' => 'Sundry Debtors',
                                                        'subgrp_1' => '',
                                                        'main_grp' => 'Current Assets',
                                                        'amount' =>  0
                                                ));*/
                                                if($gst_number != '' && $is_add == true){
                                                    if(preg_match("/^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/", $gst_number)){
                                                    } else{
                                                    $is_add = false;
                                                    $error = "Enter Valid GST Number";
                                                    }
                                                }
                                                if($country !='' && !empty($country) && $is_add == true){
                                                    if(isset($country_bulk[$country])){
                                                        $customer_country_id = $country_bulk[$country];
                                                        if($states !='' && !empty($states)){
                                                            if(isset($states_bulk_country[$states])){
                                                                $state_country_id = $states_bulk_country[$states];
                                                                if($customer_country_id == $state_country_id){
                                                                    $customer_state_id = $states_bulk[$states];
                                                                    if($city !='' && !empty($city)){
                                                                        if(isset($city_bulk_state[$city])){
                                                                            $customer_state_city_id =$city_bulk_state[$city];
                                                                            if($customer_state_id == $customer_state_city_id){
                                                                                $customer_state_city_id = $city_bulk[$city];
                                                                                if($address != '' && !empty($address)){
                                                                                    $customer_address = $address;
                                                                                    if($email != '' && $is_add == true){
                                                                                        if(!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email)){
                                                                                            $is_add = false;
                                                                                            $error = "Invalid Email";
                                                                                        }
                                                                                    }
                                                                                    if($pan_number != '' && $is_add == true){
                                                                                        if(!preg_match('/^[-a-zA-Z\s0-9 ]+$/', $pan_number)){
                                                                                            $is_add = false;
                                                                                            $error = "Invalid Pan Number";
                                                                                        }
                                                                                    }
                                                                                    if($tan_number != '' && $is_add == true){
                                                                                        if(!preg_match('/^[-a-zA-Z\s0-9 ]+$/', $tan_number)){
                                                                                            $is_add = false;
                                                                                            $error = "Invalid Tan Number";
                                                                                        }
                                                                                    }
                                                                                    if($pin_code != '' && $is_add == true){
                                                                                        if(!preg_match('/^[0-9]\+$/', $pin_code)){
                                                                                            $is_add = false;
                                                                                            $error = "Invalid Pin Code";;
                                                                                        }
                                                                                    }
                                                                                    if($due_days != '' && $is_add == true){
                                                                                        if ($due_days < 0 || $due_days > 365 ) {
                                                                                            $is_add = false;
                                                                                            $error = "Value must be less than or equal to 365";
                                                                                        }
                                                                                    }
                                                                                }else{
                                                                                    $is_add = false;
                                                                                    $error = "Address Should Not Empty";
                                                                                }
                                                                            }else{
                                                                                $is_add = false;
                                                                                $error = "City Name Is Not Present In entered States";  
                                                                            }
                                                                        }else {
                                                                            $is_add = false;
                                                                            $error = "City Name Is Not Exit"; 
                                                                        }
                                                                    }else{
                                                                       $is_add = false;
                                                                        $error = "City Name Should Not Empty"; 
                                                                    }
                                                                }else{
                                                                    $is_add = false;
                                                                    $error = "States Name Is Not Exit In entered Country";
                                                                }
                                                            }else{
                                                                $is_add = false;
                                                                $error = "Entered State Name Is Not Exit!";
                                                            }
                                                        }else{
                                                            $is_add = false;
                                                            $error = "States Name Should Not Empty";
                                                        }
                                                    }else{
                                                        $is_add = false;
                                                        $error = "Entered Country Name Is Not Exit!";
                                                    }
                                                }elseif($is_add == true){
                                                    $is_add = false;
                                                    $error = "Country Name Should Not Empty";
                                                }
                                            }else{
                                                $is_add = false;
                                                $error = "Name already used";
                                            }
                                        }else{
                                        $is_add = false;
                                        $error = "Customer Name Should Not Empty";
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Wrong Customer Type";
                                    }
                                }else{
                                    $is_add = false;
                                    $error = "Customer Type Should Not Empty";
                                }
                                if($is_add){
                                    $headers = array(
                                        'customer_name' => trim($row['B']),
                                        "customer_code" => $invoice_number,
                                        "reference_number" => $reference_number,
                                        "reference_type" => 'customer',
                                        "customer_type" => $customer_type,
                                        "customer_address" => $address,
                                        "customer_country_id" => $customer_country_id,
                                        "customer_state_id" => $customer_state_id,
                                        "customer_city_id" => $customer_city_id,
                                        "contact_person" => trim($row['J']),
                                        "customer_email" => trim($row['L']),
                                        "ledger_id" => $customer_ledger_id,
                                        "customer_gstin_number" => trim($row['C']),
                                        "customer_pan_number" => trim($row['I']),    
                                        "customer_mobile" => trim($row['K']),
                                        "customer_postal_code" => trim($row['H']),
                                        "due_days" => trim($row['M']),
                                        "tan_number" => trim($row['N']),
                                        "delete_status" => 0,
                                        "added_date" => date('Y-m-d'),
                                        "updated_date" => "",
                                        "updated_user_id" => "",
                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                    );
                                    if($id = $this->general_model->insertData($table_name, $headers)){
                                        $txt_shipping_code = $invoice_number . "-1";

                                        $shipping_address_data = array(
                                            "shipping_address" => $address,
                                            "primary_address" => 'yes',
                                            "shipping_party_id" => $id,
                                            "shipping_party_type" => 'customer',
                                            "contact_person" => trim($row['J']),
                                            /*"department" => trim($row['N']),*/
                                            "email" => trim($row['L']),
                                            "shipping_gstin" => trim($row['C']),
                                            "contact_number" => trim($row['K']),
                                            "department" => trim($row['O']),
                                            "added_date" => date('Y-m-d'),
                                            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                            "country_id" => $customer_country_id,
                                            "state_id" => $customer_state_id,
                                            "city_id" => $customer_city_id,
                                            "shipping_code" => $txt_shipping_code,
                                            "address_pin_code" => trim($row['H']),
                                            "updated_date" => "",
                                            "updated_user_id" => ""
                                        );
                                        $table = "shipping_address";
                                        $this->general_model->insertData($table, $shipping_address_data);
                                        /*$ecommerce = 1;
                                        if($ecommerce){
                                            $headers['customer_id'] = $id;
                                            $this->customerhook->CreateCustomer($headers);
                                        }*/
                                    }
                                }else {
                                        $error_array[] = $error;
                                }
                                /* $row['Error'] = $added_error;*/
                                if(!$is_add && !empty($row)){
                                    array_unshift($row,$error);
                                    array_push($errors_email, array_values($row));
                                }
                                if(!empty($error_array)){
                                    $errorMsg = implode('<br>', $error_array);
                                    $this->session->set_flashdata('bulk_error_customer',$errorMsg);
                                    /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)); */   
                                }else{
                                    $successMsg = 'Customer imported successfully.';
                                    $this->session->set_flashdata('bulk_success_customer',$successMsg);
                                    /*$this->session->set_userdata('bulk_success', $successMsg); */ 
                                }
                            }
                            $table = "log";
                                $log_data = array(
                                                'user_id' => $this->session->userdata('SESS_USER_ID'),
                                                'table_id' => 0,
                                                'table_name' => 'customer',
                                                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                                'message' => 'Bulk Customer Inserted. File_Name->'.$Updata['uploadData']['file_name']);
                                            $this->general_model->insertData($table, $log_data);

                                $log_data = array(
                                                'user_id' => $this->session->userdata('SESS_USER_ID'),
                                                'table_id' => 0,
                                                'table_name' => 'shipping_address',
                                                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                                'message' => 'Bulk Shipping Address Inserted. File_Name->'.$Updata['uploadData']['file_name']);
                                            $this->general_model->insertData($table, $log_data);
                        }else{
                            $this->session->set_flashdata('bulk_error_customer',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }
                    }else{
                        $this->session->set_flashdata('bulk_error_customer',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_customer',"Error on file upload, please try again.");
                    /*$this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');*/
                }
            }
        }
        if(!empty($errors_email)){
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = $this->session->userdata('SESS_EMAIL');
            /*$to = 'harish.sr@aavana.in';*/
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
           $resp = $this->send_csv_mail($errors_email,'Customer Bulk Import Error Logs, <br><br> PFA,',"Customer bulk upload error logs in <{$import_xls_file}>",$to);
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
            $this->session->set_flashdata('bulk_error_customer',"Error email has been sent to registered email ID");
             /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
        }
        redirect("customer", 'refresh');
    }
    public function add_bulk_upload_customer_leathercraft()
    {
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/customerCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();

        if (!$this->upload->do_upload('bulk_customer')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_customer',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        }else {
            $data = $this->get_default_country_state();
            $customer_module_id = $this->config->item('customer_module');
            $data['module_id'] = $customer_module_id;
            $modules = $this->modules;
            $privilege = "add_privilege";
            $data['privilege'] = "add_privilege";
            $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

            /* presents all the needed */
            $data = array_merge($data, $section_modules);

            /* presents all the needed */
            $Updata = array('uploadData' => $this->upload->data());

            if (!empty($Updata['uploadData']['file_name'])) {
                $import_xls_file = $Updata['uploadData']['file_name'];
                $inputFileName = $path . $import_xls_file;
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);               
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    //print_r($allDataInSheet);
                    if(!empty($allDataInSheet)){
                        if(strtolower($allDataInSheet[1]['A']) == 'customer type' && strtolower($allDataInSheet[1]['B']) == 'store name' && strtolower($allDataInSheet[1]['C']) == 'gst number' && strtolower($allDataInSheet[1]['D']) == 'country' && strtolower($allDataInSheet[1]['E']) == 'state' && strtolower($allDataInSheet[1]['F']) == 'city' && strtolower($allDataInSheet[1]['G']) == 'address' && strtolower($allDataInSheet[1]['H']) == 'pin code' && strtolower($allDataInSheet[1]['I']) == 'pan number' && strtolower($allDataInSheet[1]['J']) == 'contact person name' && strtolower($allDataInSheet[1]['K']) == 'contact number' && strtolower($allDataInSheet[1]['L']) == 'email' && strtolower($allDataInSheet[1]['M']) == 'due days' && strtolower($allDataInSheet[1]['N']) == 'tan number' && strtolower($allDataInSheet[1]['O']) == 'department' && strtolower($allDataInSheet[1]['P']) == 'store location'){
                            
                            $header_row = array_shift($allDataInSheet);

                            $country_bulk = $this->general_model->customer_bulk_country();
                            $country_bulk = array_column($country_bulk, 'country_id', 'country_name');
                            $states_bulk = $this->general_model->customer_bulk_state();
                            $states_bulk_country = array_column($states_bulk, 'country_id', 'state_name');
                            $states_bulk= array_column($states_bulk, 'state_id', 'state_name');
                            $city_bulk = $this->general_model->customer_bulk_city();
                            $city_bulk_state = array_column($city_bulk, 'state_id', 'city_name');
                            $city_bulk = array_column($city_bulk, 'city_id', 'city_name');
                            $access_settings = $data['access_settings'];
                            $primary_id = "customer_id";
                            $table_name = "customer";
                            $date_field_name = "added_date";
                            $current_date = date('Y-m-d');
                            $sales_ledger = $this->config->item('sales_ledger');
                            $default_customer_id = $sales_ledger['CUSTOMER'];
                            $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
                                
                            
                            foreach($allDataInSheet as $row){ 
                                $customer_type = strtolower(trim($row['A']));
                                $customer_name = strtolower(trim($row['B']));
                                $gst_number = trim($row['C']);
                                $country = strtolower(trim($row['D']));
                                $states = strtolower(trim($row['E']));
                                $city = strtolower(trim($row['F']));
                                $address = trim($row['G']);
                                $email = trim($row['L']);
                                $pin_code = trim($row['H']);
                                $due_days = trim($row['M']);
                                $pan_number = trim($row['I']);
                                $tan_number = trim($row['N']);
                                $department = trim($row['O']);
                                $store_location = trim($row['P']);
                                $customer_ledger_id = '';
                                $customer_country_id = '';
                                $customer_state_city_id = '';
                                $customer_state_id = '';
                                $customer_city_id = '';
                                $is_add = true;
                                $error = '';
                                $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                
                                $reference_number = $this->generate_reference_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                if($customer_type != '' && !empty($customer_type)){
                                    if($customer_type == 'company' || $customer_type == 'firm' || $customer_type == 'private limited company' || $customer_type == 'proprietorship' || $customer_type == 'partnership' || $customer_type == 'one person company' || $customer_type == 'limited liability partnership'){
                                        if($customer_type == 'firm'){
                                            $customer_type = 'individual';
                                        }
                                        if($customer_name != '' && !empty($customer_name)){
                                            $Customer_check = $this->Bulk_CustomerValidation($customer_name);
                                            if($Customer_check["rows"] <= 0){
                                                $customer_ary = array(
                                                                'ledger_name' => trim($row['B']),
                                                                'second_grp' => '',
                                                                'primary_grp' => 'Sundry Debtors',
                                                                'main_grp' => 'Current Assets',
                                                                'default_ledger_id' => 0,
                                                                'default_value' => trim($row['B']),
                                                                'amount' => 0
                                                            );
                                                if(!empty($customer_ledger_name)){
                                                    $customer_ledger = $customer_ledger_name->ledger_name;
                                                    /*$customer_ledger = str_ireplace('{{SECTION}}',$section_name , $customer_ledger);*/
                                                    $customer_ledger = str_ireplace('{{X}}',trim($row['B']), $customer_ledger);
                                                    $customer_ary['ledger_name'] = $customer_ledger;
                                                    $customer_ary['primary_grp'] = $customer_ledger_name->sub_group_1;
                                                    $customer_ary['second_grp'] = $customer_ledger_name->sub_group_2;
                                                    $customer_ary['main_grp'] = $customer_ledger_name->main_group;
                                                    $customer_ary['default_ledger_id'] = $customer_ledger_name->ledger_id;
                                                }
                                                $customer_ledger_id = $this->ledger_model->getGroupLedgerId($customer_ary);
                                                /*$customer_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                        'ledger_name' => trim($row['B']),
                                                        'subgrp_2' => 'Sundry Debtors',
                                                        'subgrp_1' => '',
                                                        'main_grp' => 'Current Assets',
                                                        'amount' =>  0
                                                ));*/
                                                if($gst_number != '' && $is_add == true){
                                                    if(preg_match("/^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/", $gst_number)){
                                                    } else{
                                                    $is_add = false;
                                                    $error = "Enter Valid GST Number";
                                                    }
                                                }
                                                if($country !='' && !empty($country) && $is_add == true){
                                                    if(isset($country_bulk[$country])){
                                                        $customer_country_id = $country_bulk[$country];
                                                        if($states !='' && !empty($states)){
                                                            if(isset($states_bulk_country[$states])){
                                                                $state_country_id = $states_bulk_country[$states];
                                                                if($customer_country_id == $state_country_id){
                                                                    $customer_state_id = $states_bulk[$states];
                                                                    if($city !='' && !empty($city)){
                                                                        if(isset($city_bulk_state[$city])){
                                                                            $customer_state_city_id =$city_bulk_state[$city];
                                                                            if($customer_state_id == $customer_state_city_id){
                                                                                $customer_state_city_id = $city_bulk[$city];
                                                                                if($address != '' && !empty($address)){
                                                                                    $customer_address = $address;
                                                                                    if($store_location != '' && !empty($store_location)){
                                                                                        if($email != '' && $is_add == true){
                                                                                            if(!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email)){
                                                                                                $is_add = false;
                                                                                                $error = "Invalid Email";
                                                                                            }
                                                                                        }
                                                                                        if($pan_number != '' && $is_add == true){
                                                                                            if(!preg_match('/^[-a-zA-Z\s0-9 ]+$/', $pan_number)){
                                                                                                $is_add = false;
                                                                                                $error = "Invalid Pan Number";
                                                                                            }
                                                                                        }
                                                                                        if($tan_number != '' && $is_add == true){
                                                                                            if(!preg_match('/^[-a-zA-Z\s0-9 ]+$/', $tan_number)){
                                                                                                $is_add = false;
                                                                                                $error = "Invalid Tan Number";
                                                                                            }
                                                                                        }
                                                                                        if($pin_code != '' && $is_add == true){
                                                                                            if(!preg_match('/^[0-9]\+$/', $pin_code)){
                                                                                                $is_add = false;
                                                                                                $error = "Invalid Pin Code";;
                                                                                            }
                                                                                        }
                                                                                        if($due_days != '' && $is_add == true){
                                                                                            if ($due_days < 0 || $due_days > 365 ) {
                                                                                                $is_add = false;
                                                                                                $error = "Value must be less than or equal to 365";
                                                                                            }
                                                                                        }
                                                                                    }else{
                                                                                      $is_add = false;
                                                                                        $error = "Store Location Should Not Empty";  
                                                                                    }
                                                                                }else{
                                                                                    $is_add = false;
                                                                                    $error = "Address Should Not Empty";
                                                                                }
                                                                            }else{
                                                                                $is_add = false;
                                                                                $error = "City Name Is Not Present In entered States";  
                                                                            }
                                                                        }else {
                                                                            $is_add = false;
                                                                            $error = "City Name Is Not Exit"; 
                                                                        }
                                                                    }else{
                                                                       $is_add = false;
                                                                        $error = "City Name Should Not Empty"; 
                                                                    }
                                                                }else{
                                                                    $is_add = false;
                                                                    $error = "States Name Is Not Exit In entered Country";
                                                                }
                                                            }else{
                                                                $is_add = false;
                                                                $error = "Entered State Name Is Not Exit!";
                                                            }
                                                        }else{
                                                            $is_add = false;
                                                            $error = "States Name Should Not Empty";
                                                        }
                                                    }else{
                                                        $is_add = false;
                                                        $error = "Entered Country Name Is Not Exit!";
                                                    }
                                                }elseif($is_add == true){
                                                    $is_add = false;
                                                    $error = "Country Name Should Not Empty";
                                                }
                                            }else{
                                                $is_add = false;
                                                $error = "Name already used";
                                            }
                                        }else{
                                        $is_add = false;
                                        $error = "Customer Name Should Not Empty";
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Wrong Customer Type";
                                    }
                                }else{
                                    $is_add = false;
                                    $error = "Customer Type Should Not Empty";
                                }
                                if($is_add){
                                    $headers = array(
                                        'customer_name' => trim($row['B']),
                                        "customer_code" => $invoice_number,
                                        "reference_number" => $reference_number,
                                        "reference_type" => 'customer',
                                        "customer_type" => $customer_type,
                                        "customer_address" => $address,
                                        "customer_country_id" => $customer_country_id,
                                        "customer_state_id" => $customer_state_id,
                                        "customer_city_id" => $customer_city_id,
                                        "contact_person" => trim($row['J']),
                                        "customer_email" => trim($row['L']),
                                        "ledger_id" => $customer_ledger_id,
                                        "customer_gstin_number" => trim($row['C']),
                                        "customer_pan_number" => trim($row['I']),    
                                        "customer_mobile" => trim($row['K']),
                                        "customer_postal_code" => trim($row['H']),
                                        "due_days" => trim($row['M']),
                                        "customer_tan_number" => trim($row['N']),
                                        "delete_status" => 0,
                                        "added_date" => date('Y-m-d'),
                                        "updated_date" => "",
                                        "updated_user_id" => "",
                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                    );
                                    if($id = $this->general_model->insertData($table_name, $headers)){
                                        $txt_shipping_code = $invoice_number . "-1";

                                        $shipping_address_data = array(
                                            "shipping_address" => $address,
                                            "primary_address" => 'yes',
                                            "shipping_party_id" => $id,
                                            "shipping_party_type" => 'customer',
                                            "contact_person" => trim($row['J']),
                                            /*"department" => trim($row['N']),*/
                                            "email" => trim($row['L']),
                                            "shipping_gstin" => trim($row['C']),
                                            "contact_number" => trim($row['K']),
                                            "department" => trim($row['O']),
                                            "store_location" => trim($row['P']),
                                            "added_date" => date('Y-m-d'),
                                            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                            "country_id" => $customer_country_id,
                                            "state_id" => $customer_state_id,
                                            "city_id" => $customer_city_id,
                                            "shipping_code" => $txt_shipping_code,
                                            "address_pin_code" => trim($row['H']),
                                            "updated_date" => "",
                                            "updated_user_id" => ""
                                        );
                                        $table = "shipping_address";
                                        $this->general_model->insertData($table, $shipping_address_data);
                                        /*$ecommerce = 1;
                                        if($ecommerce){
                                            $headers['customer_id'] = $id;
                                            $this->customerhook->CreateCustomer($headers);
                                        }*/
                                    }
                                }else {
                                        $error_array[] = $error;
                                }
                                /* $row['Error'] = $added_error;*/
                                if(!$is_add && !empty($row)){
                                    array_unshift($row,$error);
                                    array_push($errors_email, array_values($row));
                                }
                                if(!empty($error_array)){
                                    $errorMsg = implode('<br>', $error_array);
                                    $this->session->set_flashdata('bulk_error_customer',$errorMsg);
                                    /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)); */   
                                }else{
                                    $successMsg = 'Customer imported successfully.';
                                    $this->session->set_flashdata('bulk_success_customer',$successMsg);
                                    /*$this->session->set_userdata('bulk_success', $successMsg); */ 
                                }
                            }
                            $table = "log";
                                $log_data = array(
                                                'user_id' => $this->session->userdata('SESS_USER_ID'),
                                                'table_id' => 0,
                                                'table_name' => 'customer',
                                                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                                'message' => 'Bulk Customer Inserted. File_Name->'.$Updata['uploadData']['file_name']);
                                            $this->general_model->insertData($table, $log_data);

                                $log_data = array(
                                                'user_id' => $this->session->userdata('SESS_USER_ID'),
                                                'table_id' => 0,
                                                'table_name' => 'shipping_address',
                                                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                                'message' => 'Bulk Shipping Address Inserted. File_Name->'.$Updata['uploadData']['file_name']);
                                            $this->general_model->insertData($table, $log_data);
                        }else{
                            $this->session->set_flashdata('bulk_error_customer',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }
                    }else{
                        $this->session->set_flashdata('bulk_error_customer',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_customer',"Error on file upload, please try again.");
                    /*$this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');*/
                }
            }
        }
        if(!empty($errors_email)){
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = $this->session->userdata('SESS_EMAIL');
            /*$to = 'harish.sr@aavana.in';*/
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
           $resp = $this->send_csv_mail($errors_email,'Customer Bulk Import Error Logs, <br><br> PFA,',"Customer bulk upload error logs in <{$import_xls_file}>",$to);
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
            $this->session->set_flashdata('bulk_error_customer',"Error email has been sent to registered email ID");
             /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
        }
        redirect("customer", 'refresh');
    }
    function send_csv_mail ($csvData, $body, $subject,$to) {

        /*$to = 'chetna.b@aavana.in';*/
        $path = 'uploads/CustomerErrors/error.csv';
        $fp = fopen($path, 'w');//fopen('php://temp', 'w+');
        foreach ($csvData as $line) fputcsv($fp, array_values($line));
        rewind($fp);
        fclose($fp);
        $emailDataSet = array(                         
                            'subject' =>$subject,                    
                            'message' => $body,
                            'email'=>  $to,
                            'csv_string' => $path
                        );

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
        $mail->setFrom("noreply@aodry.com", $subject);
        $mail->addReplyTo("noreply@aodry.com", $subject);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $bodyContent = $body;
        $mail->Subject = $subject;
        $mail->Body = $bodyContent;
        $mail->addAttachment($path);
        $resp = 'Success';
        if (!$mail->send()) {
            $resp = 'Message could not be sent.';
            $resp =  'Mailer Error : ' . $mail->ErrorInfo;
        } else {
            $this->session->set_flashdata('email_send_customer', 'success');
        }
        
        return $resp;
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'cust.*,s.department,s.store_location';
        $table = 'customer cust';
        $join = ['shipping_address s' => 'cust.customer_id=s.shipping_party_id#left'];
        $where = array(
           "cust.customer_id" => $id,
            "cust.delete_status" => 0,
            's.shipping_party_type' => 'customer',
            's.primary_address' => 'yes'
        );
        $data['data'] = $this->general_model->getJoinRecords($string, $table, $where, $join, $order = "");
        /*echo '<pre>';
        print_r($this->db->last_query());
        exit();*/
        $string = 'con.*';
        $table = 'contact_person con';
        $where = array(
            'con.party_id' => $id,
            'con.party_type' => 'customer',
            'con.delete_status' => 0);
        $order = array(
            "con.contact_person_id" => "asc");
        $data['contact_person'] = $this->general_model->getRecords($string, $table, $where, $order);

        $string = 'c.*';
        $table = 'countries c';
        $where = array(
            'c.delete_status' => 0);
        $data['country'] = $this->general_model->getRecords($string, $table, $where);
        $string = 'st.*';
        $table = 'states st';
        $where = array(
            'st.country_id' => $data['data'][0]->customer_country_id);
        $data['state'] = $this->general_model->getRecords($string, $table, $where);
        $string = 'ct.*';
        $table = 'cities ct';
        $where = array(
            'ct.state_id' => $data['data'][0]->customer_state_id);
        $data['city'] = $this->general_model->getRecords($string, $table, $where);

        /*   $string                       = 'st.*';
          $table                        = 'states st';
          $where                        = array(
          'st.country_id' => $data['contact_person'][0]->contact_person_country_id );
          $data['contact_person_state'] = $this->general_model->getRecords($string, $table, $where);
          $string                       = 'ct.*';
          $table                        = 'cities ct';
          $where                        = array(
          'ct.state_id' => $data['contact_person'][0]->contact_person_state_id );
          $data['contact_person_city']  = $this->general_model->getRecords($string, $table, $where); */

        $data['additional_info'] = $additional_info = $this->general_model->getRecords('*', 'customer_additional_info', array(
            'customer_id' => $id));
        $data['additional_info_count'] = count($additional_info);

        $this->load->view('customer/edit', $data);
    }

    public function edit_customer() {

        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('customer_id');
        $ledger_id = $this->input->post('ledger_id');
        $country = $this->input->post('cmb_country');
        $state = $this->input->post('cmb_state');

        // $contact_person_name = $this->input->post('contact_person_name');
        // if (!isset($contact_person_name) || $contact_person_name == NULL || $contact_person_name == "")
        // {
        //     $contact_person_name = $this->input->post('customer_name');
        // }
        // $contact_person = array(
        //                "contact_person_name"          => $contact_person_name,
        //                "contact_person_department"    => $this->input->post('contact_person_department'),
        //                "contact_person_email" => $this->input->post('contact_person_email'),
        //                "contact_person_mobile"        => $this->input->post('contact_person_mobile'),
        //                "added_date"                   => $this->input->post('added_date'),
        //                "added_user_id"                => $this->input->post('added_user_id'),
        //                "branch_id"                    => $this->session->userdata('SESS_BRANCH_ID'),
        //                "updated_date"                 => date('Y-m-d'),
        //                "updated_user_id"              => $this->session->userdata('SESS_USER_ID'));
        // $table          = "contact_person";
        // $where          = array(
        //                "contact_person_id" => $contact_person_id);
        // $cp_id          = $this->general_model->updateData($table, $contact_person, $where);
        $customer_name = trim($this->input->post('customer_name'));
       /* $this->general_model->updateData('ledgers', array(
            'ledger_title' => $customer_name), array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'ledger_id' => $ledger_id));*/

        /*$customer_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                    'ledger_name' => ucwords(strtolower($customer_name)),
                                                    'subgrp_2' => 'Sundry Debtors',
                                                    'subgrp_1' => '',
                                                    'main_grp' => 'Current Assets',
                                                    'amount' =>  0
                                    ));*/
        $customer_data = array(
            "customer_name" => $customer_name,
            "customer_code" => $this->input->post('customer_code'),
            "reference_number" => $this->input->post('reference_number'),
            "reference_type" => 'customer',
            "customer_type" => $this->input->post('customer_type'),
            "customer_address" => $this->input->post('address'),
            "customer_country_id" => $this->input->post('cmb_country'),
            "customer_state_id" => $this->input->post('cmb_state'),
            "customer_city_id" => $this->input->post('cmb_city'),
            "contact_person" => $this->input->post('txt_contact_person'),
            "customer_gstin_number" => $this->input->post('gst_number'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "customer_pan_number" => $this->input->post('txt_pan_number'),
            "customer_tan_number" => $this->input->post('txt_tan_number'),
            "customer_postal_code" => $this->input->post('txt_pin_code'),
            "customer_mobile" => $this->input->post('txt_contact_number'),
            "customer_email"  => $this->input->post('txt_email'),
            "due_days" => $this->input->post('due_days')
        );

        if($this->input->post('dl_no')){
            $customer_data['drug_licence_no'] = $this->input->post('dl_no');
        }

        $table = "customer";
        $where = array("customer_id" => $id);
        
        if ($this->general_model->updateData($table, $customer_data, $where)) {

            /*$ecommerce = 1;
            if($ecommerce){
                $customer_data['customer_id'] = $id;
                $this->customerhook->UpdateCustomer($customer_data);
            }*/

            $sales_ledger = $this->config->item('sales_ledger');
            $default_customer_id = $sales_ledger['CUSTOMER'];
            $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
            if(!empty($customer_ledger_name)){
                $customer_ledger = $customer_ledger_name->ledger_name;
                $customer_name = str_ireplace('{{X}}',$customer_name, $customer_ledger);
            }
            /* Update ledger name */
            $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$customer_name}' WHERE ledger_id='{$ledger_id}'");

            $this->general_model->deleteData('customer_additional_info', array('customer_id' => $id));
            $shipping_address_data = array(
                    "shipping_address" => $this->input->post('address'),          
                    "shipping_party_type" => 'customer',
                    "contact_person" => $this->input->post('txt_contact_person'),
                    "email" => $this->input->post('txt_email'),
                    "department" => $this->input->post('department'),
                    "shipping_gstin" => $this->input->post('gst_number'),
                    "country_id" => $this->input->post('cmb_country'),
                    "state_id" => $this->input->post('cmb_state'),
                    "city_id" => $this->input->post('cmb_city'),
                    "contact_number" => $this->input->post('txt_contact_number'),
                    "updated_date" => date('Y-m-d'),
                    "email"  => $this->input->post('txt_email'),
                    "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                    "address_pin_code" => $this->input->post('txt_pin_code')
                );
            if($this->input->post('store_location')){
                $shipping_address_data['store_location'] = $this->input->post('store_location');
            }
           /* echo '<pre>';
            print_r($shipping_address_data);
            exit();*/
            $table = "shipping_address";
            $where = array("shipping_party_id" => $id, "primary_address" => 'yes');
            $this->general_model->updateData($table, $shipping_address_data, $where);
            $successMsg = 'Customer Updated Successfully';
            $this->session->set_flashdata('customer_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'customer',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'customer Updated');
            $this->general_model->insertData($table, $log_data);

            if (isset($_POST['custom_field'])) {
                $data = array();
                $custom_field = $_POST['custom_field'];
                $custom_lablel = $_POST['custom_lablel'];
                $length = count($_POST['custom_field']);

                for ($i = 0; $i < $length; $i++) {
                    $data[$i]['value'] = $custom_field[$i];
                    $data[$i]['column_name'] = $custom_lablel[$i];
                    $data[$i]['customer_id'] = $id;
                    $data[$i]['added_user_id'] = $this->session->userdata('SESS_USER_ID');
                    $data[$i]['added_date'] = date('Y-m-d');
                }

                $this->db->insert_batch('customer_additional_info', $data);
            }

            /* $contact_person_data = array(
              "contact_person_name"        => $this->input->post('contact_person_name'),
              "contact_person_code"        => $this->input->post('contact_person_code'),
              "contact_person_address"     => $this->input->post('contact_person_address'),
              "contact_person_country_id"  => $this->input->post('contact_person_country'),
              "contact_person_state_id"    => $this->input->post('contact_person_state'),
              "contact_person_city_id"     => $this->input->post('contact_person_city'),
              "contact_person_postal_code" => $this->input->post('contact_person_postal_code'),
              "contact_person_email"       => $this->input->post('contact_person_email'),
              "contact_person_mobile"      => $this->input->post('contact_person_mobile'),
              "contact_person_telephone"   => $this->input->post('contact_person_telephone'),
              "contact_person_website"     => $this->input->post('contact_person_website'),
              "contact_person_department"  => $this->input->post('contact_person_department'),
              "contact_person_designation" => $this->input->post('contact_person_designation'),
              "contact_person_industry"    => $this->input->post('contact_person_industry'),
              "party_id"                   => $id,
              "party_type"                 => 'customer',
              "updated_date"               => date('Y-m-d'),
              "updated_user_id"            => $this->session->userdata('SESS_USER_ID'),
              "branch_id"                  => $this->session->userdata('SESS_BRANCH_ID')
              );
              if ($this->general_model->updateData('contact_person', $contact_person_data, array(
              'contact_person_id' => $contact_person_id )))
              {
              $table    = "log";
              $log_data = array(
              'user_id'           => $this->session->userdata('SESS_USER_ID'),
              'table_id'          => $contact_person_id,
              'table_name'        => 'contact_person',
              'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
              'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
              'message'           => 'Contact Person(Customer) Updated' );
              $this->general_model->insertData($table, $log_data);
              } */

            redirect('customer', 'refresh');
        } else {
            $errorMsg = 'Customer Update Unsuccessful';
            $this->session->set_flashdata('customer_error',$errorMsg);
            $this->session->set_flashdata('fail', 'customer can not be Updated.');
            redirect("customer", 'refresh');
        }
    }

    public function add_customer() {
        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $country = $this->input->post('country');
        $state = $this->input->post('state');

        $title = strtoupper(trim($this->input->post('customer_name')));
        $subgroup = "Customer";
        $customer_name = trim($this->input->post('customer_name'));
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
        /*$customer_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                    'ledger_name' => $customer_name,
                                                    'subgrp_2' => 'Sundry Debtors',
                                                    'subgrp_1' => '',
                                                    'main_grp' => 'Current Assets',
                                                    'amount' =>  0
                                    ));*/

        /* if ($ledger_id = $this->ledger_model->addLedger($title, $subgroup)){ */
        $customer_data = array(
            "customer_name" => $customer_name,
            "customer_code" => $this->input->post('customer_code'),
            "reference_number" => $this->input->post('reference_number'),
            "reference_type" => 'customer',
            "customer_type" => $this->input->post('customer_type'),
            "customer_address" => $this->input->post('address'),
            "customer_country_id" => $this->input->post('cmb_country'),
            "customer_state_id" => $this->input->post('cmb_state'),
            "customer_city_id" => $this->input->post('cmb_city'),
            "contact_person" => $this->input->post('txt_contact_person'),
            "customer_email" => $this->input->post('email_address'),
            "customer_gstin_number" => $this->input->post('gst_number'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "updated_date" => "",
            "updated_user_id" => "",
            "ledger_id"  => $customer_ledger_id,
            "customer_pan_number" => $this->input->post('txt_pan_number'),
            "customer_tan_number" => $this->input->post('txt_tan_number'),
            "customer_mobile" => $this->input->post('txt_contact_number'),
            "customer_postal_code" => $this->input->post('txt_pin_code'),
            "due_days" => $this->input->post('due_days')); 

        if($this->input->post('dl_no')){
            $customer_data['drug_licence_no'] = $this->input->post('dl_no');
        }
        $table = "customer";
        if ($id = $this->general_model->insertData($table, $customer_data)) {
            //$reference_number = $this->input->post('reference_number');
            $customer_code = $this->input->post('customer_code');
            $txt_shipping_code = $customer_code . "-1";

            /*$ecommerce = 1;
            if($ecommerce){
                $customer_data['customer_id'] = $id;
                $this->customerhook->CreateCustomer($customer_data);
            }*/

            $shipping_address_data = array(
                "shipping_address" => $this->input->post('address'),
                "primary_address" => 'yes',
                "shipping_party_id" => $id,
                "shipping_party_type" => 'customer',
                "contact_person" => $this->input->post('txt_contact_person'),
                "department" => $this->input->post('department'),
                "email" => $this->input->post('email_address'),
                "shipping_gstin" => $this->input->post('gst_number'),
                "contact_number" => $this->input->post('txt_contact_number'),
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "country_id" => $this->input->post('cmb_country'),
                "state_id" => $this->input->post('cmb_state'),
                "city_id" => $this->input->post('cmb_city'),
                "shipping_code" => $txt_shipping_code,
                "address_pin_code" => $this->input->post('txt_pin_code'),
                "updated_date" => "",
                "updated_user_id" => ""
            );
            if($this->input->post('store_location')){
                $shipping_address_data['store_location'] = $this->input->post('store_location');
            }
           /* echo '<pre>';
            print_r($shipping_address_data);
            exit();*/
            $table = "shipping_address";
            $this->general_model->insertData($table, $shipping_address_data);
            $successMsg = 'Customer Added Successfully';
            $this->session->set_flashdata('customer_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'customer',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Customer Inserted');
            $this->general_model->insertData($table, $log_data);

            if (isset($_POST['custom_field'])) {
                $data = array();
                $custom_field = $_POST['custom_field'];
                $custom_lablel = $_POST['custom_lablel'];
                $length = count($_POST['custom_field']);

                for ($i = 0; $i < $length; $i++) {
                    $data[$i]['value'] = $custom_field[$i];
                    $data[$i]['column_name'] = $custom_lablel[$i];
                    $data[$i]['customer_id'] = $id;
                    $data[$i]['added_user_id'] = $this->session->userdata('SESS_USER_ID');
                    $data[$i]['added_date'] = date('Y-m-d');
                }

                $this->db->insert_batch('customer_additional_info', $data);
            }
        }else{
            $errorMsg = 'Customer Add Unsuccessful';
            $this->session->set_flashdata('customer_error',$errorMsg);
        }
        /* } */
        redirect("customer", 'refresh');
    }

    public function add_customer_ajax() {
        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* if ($this->input->post('gstregtype') != "Registered")
          {
          $gstin = "";
          }
          else
          {
          $gstin = $this->input->post('gstin');
          } */

        $customer_code = strtoupper(trim($this->input->post('customer_code')));

        $customer_exist = $this->general_model->getRecords('count(*) num', 'customer', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'customer_code' => $customer_code,
        ));


        if ($customer_exist[0]->num > 0) {
            $primary_id = "customer_id";
            $table_name = "customer";
            $date_field_name = "added_date";
            $current_date = date('Y-m-d');
            $access_settings = $section_modules['access_settings'];
            $customer_code = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

            $customer_code = strtoupper(trim($customer_code));
        }

        $customer_name = strtoupper(trim($this->input->post('customer_name')));
        $customer_exist = $this->general_model->getRecords('count(*) num', 'ledgers', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'ledger_title' => $customer_name,
        ));

        if ($customer_exist[0]->num > 0) {

            $customer_name = $customer_name . " - " . rand(10000, 99999);
        }


        $title = strtoupper(trim($customer_name));
        $subgroup = "Customer";
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

        if ($customer_ledger_id) {
            $customer_data = array(
                "customer_name" => $customer_name,
                "customer_code" => $customer_code,
                "reference_number" => $this->input->post('reference_number'),
                "reference_type" => $this->input->post('reference_type'),
                "customer_type" => $this->input->post('customer_type'),
                "customer_address" => $this->input->post('address'),
                "customer_country_id" => $this->input->post('country'),
                "customer_state_id" => $this->input->post('state'),
                "customer_city_id" => $this->input->post('city'),
                "customer_mobile"                => $this->input->post('mobile'),
                //"customer_telephone"             => $this->input->post('telephone'),
                "customer_email"                 => $this->input->post('email'),
                "customer_postal_code"           => $this->input->post('postal_code'),
                //"customer_website"               => $this->input->post('website'),
                "customer_gstin_number" => $this->input->post('gst_number'),
                "customer_pan_number"            => $this->input->post('panno'),
                "customer_tan_number"            => $this->input->post('tanno'),

                "customer_state_code" => $this->input->post('state_code'),
                "contact_person" => $this->input->post('contact_person_name'),
                "due_days" => $this->input->post('due'),
                //"customer_gst_registration_type" => $this->input->post('gstregtype'),
                // "customer_contact_person_id"     => $contact_person_id,
                "ledger_id" => $customer_ledger_id,
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => "",
                "updated_user_id" => "");
        }
        if($this->input->post('dl_no')){
            $customer_data['drug_licence_no'] = $this->input->post('dl_no');
        }
        $table = "customer";
        $customer_id = $this->general_model->insertData($table, $customer_data);
        $log_data = array(
            'user_id' => $this->session->userdata('SESS_USER_ID'),
            'table_id' => $customer_id,
            'table_name' => 'customer',
            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'message' => 'Customer Inserted(Modal)'
        );
        $table = "log";
        if ($id = $this->general_model->insertData($table, $log_data)) {
            $reference_number = $this->input->post('reference_number');
            $txt_shipping_code = $reference_number . "-1";

            $shipping_address_data = array(
                "shipping_address" => $this->input->post('address'),
                "primary_address" => 'yes',
                "shipping_party_id" => $customer_id,
                "shipping_party_type" => 'customer',
                "contact_person" => $this->input->post('txt_contact_person'),
                "email" => $this->input->post('email'),
                "shipping_gstin" => $this->input->post('gst_number'),
                "contact_number" => $this->input->post('mobile'),
                "department" => $this->input->post('department'),
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "country_id" => $this->input->post('country'),
                "state_id" => $this->input->post('state'),
                "city_id" => $this->input->post('city'),
                "contact_person" => $this->input->post('contact_person_name'),
                "shipping_code" => $txt_shipping_code,
                "updated_date" => "",
                "updated_user_id" => ""
            );
           /* echo '<pre>';
            print_r($shipping_address_data);
            exit();*/
            if($this->input->post('store_location')){
                $shipping_address_data['store_location'] = $this->input->post('store_location');
            }
            $table = "shipping_address";
            $this->general_model->insertData($table, $shipping_address_data);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'shipping_address',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Shipping Address Inserted');
            $this->general_model->insertData($table, $log_data);
            $string = "*";
            $table = "customer";
            $where = array(
                "delete_status" => 0,
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID')
            );
            $order = array(
                "customer_name" => "asc");
            $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");

            $data['id'] = $customer_id;

            $ledger_string = "l.*";
            $ledger_where = array(
                "c.delete_status" => 0,
                "c.branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "l.branch_id" => $this->session->userdata('SESS_BRANCH_ID')
            );
            $table = "tbl_ledgers l";
            $join = ['customer c' => 'c.ledger_id = l.ledger_id'];
            $data['ledgers_data'] = $this->general_model->getJoinRecords($ledger_string, $table, $ledger_where, $join);
            $data['ledger_id'] = $customer_ledger_id;
            echo json_encode($data);
        }
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $customer_module_id = $this->config->item('customer_module');
        $data['module_id'] = $customer_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($customer_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $string = "ledger_id";
        $table = "customer";
        $where = array(
            "customer_id" => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        // $contact_person_id               = $data['data'][0]->customer_contact_person_id;
        $this->general_model->updateData('ledgers', array(
            'delete_status' => 1), array(
            'ledger_id' => $data['data'][0]->ledger_id));
        $table = "customer";
        $data = array(
            "delete_status" => 1);
        $where = array(
            "customer_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
            $table = "contact_person";
            $data = array(
                "delete_status" => 1);
            $where = array(
                "party_id" => $id,
                "party_type" => 'customer'
            );
            $this->general_model->updateData($table, $data, $where);
            $table = "shipping_address";
            $data = array(
            "delete_status" => 1);
            $where = array("shipping_party_id" => $id);
            $this->general_model->updateData($table, $data, $where);
            $successMsg = 'Customer Deleted Successfully';
            $this->session->set_flashdata('customer_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'customer',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'customer Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('customer');
        } else {
            $errorMsg = 'Customer Delete Unsuccessful';
            $this->session->set_flashdata('customer_error',$errorMsg);
            $this->session->set_flashdata('fail', 'customer can not be Deleted.');
            redirect("customer", 'refresh');
        }
    }

    public function get_check_customer() {
        $customer_code = strtoupper(trim($this->input->post('customer_code')));
        $customer_id = $this->input->post('customer_id');
        $data = $this->general_model->getRecords('count(*) num', 'customer', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'customer_code' => $customer_code,
            'customer_id!=' => $customer_id));
        echo json_encode($data);
    }
    public function get_check_customer_code_add() {
        $customer_code = strtoupper(trim($this->input->post('customer_code')));
        $data = $this->general_model->getRecords('count(*) num', 'customer', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'customer_code' => $customer_code));
        echo json_encode($data);
    }

    public function CustomerValidation(){
        $customer_name = trim($this->input->post('cust_name'));
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT customer_id FROM customer WHERE customer_name like '".$customer_name."' AND branch_id = '".$branch_id."' AND customer_id != '{$id}' ")->num_rows();

        $rows1 = $this->db->query("SELECT supplier_id FROM supplier WHERE branch_id = '".$branch_id."' AND supplier_name like '".$customer_name."' ")->num_rows();

        echo  json_encode(array('rows' => $rows + $rows1));
    }

    public function Bulk_CustomerValidation($customer_name,$id = 0){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $rows = $this->db->query("SELECT customer_id FROM customer WHERE customer_name like '".$customer_name."' AND branch_id = '".$branch_id."' AND customer_id != '{$id}' ")->num_rows();

        $rows1 = $this->db->query("SELECT supplier_id FROM supplier WHERE branch_id = '".$branch_id."' AND supplier_name like '".$customer_name."' ")->num_rows();

        return array('rows' => $rows + $rows1);
    }
    public function exportCustomerReportExcel(){
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $table_columns = array("Customer Code", "Customer Type", "Company/Firm Name", "PIN Code", "Country", "State", "City", "Address", "GST Number", "PAN Number","Contact Person Name","Contact Number", "Email", "Due Days", "TAN Number");

        $column = 0;

        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }
        $list_data = $this->common->customer_list_field();
        $posts = $this->general_model->getPageJoinRecords($list_data);
        // echo '<pre>';
        // print_r($posts);
        // exit();
        $excel_row = 2;
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $value->customer_code);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value->customer_type);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value->customer_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $value->customer_postal_code);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $value->country_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row,$value->state_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $value->city_name);  
                $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $value->customer_address);
                $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $value->customer_gstin_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $value->customer_pan_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $value->customer_contact_person_id);
                $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $value->customer_mobile);
                $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $value->customer_email);
                $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $value->due_days);
                $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $value->customer_tan_number);
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "Customer Data{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }

}
