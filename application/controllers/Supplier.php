<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }

    public function index() {
        $supplier_module_id = $this->config->item('supplier_module');
        $data['supplier_module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "supplier_id";
        $table_name = "supplier";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['country'] = $this->country_call();

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
                1 => 'supplier_code',
                2 => 'supplier_name',
                3 => 'country');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->supplier_list_field();
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
            } $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $supplier_id = $this->encryption_url->encode($post->supplier_id);
                    $nestedData['supplier_code'] = $post->supplier_code;
                    $nestedData['supplier_name'] = $post->supplier_name;
                    // $nestedData['contact_person'] = $post->contact_person_name;
                    // $nestedData['phone']         = $post->supplier_mobile;
                    //$nestedData['email']         = $post->supplier_email;
                    $nestedData['city']          = $post->city_name;
                    $nestedData['state']         = $post->state_name;
                    $nestedData['country'] = $post->country_name;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;

                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    if (in_array($data['supplier_module_id'], $data['active_edit'])) {
                        $cols .= '<span><a href="' . base_url('supplier/edit/') . $supplier_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }
                    $payment_voucher = $this->general_model->getRecords('*', 'payment_voucher', array(
                        'party_id' => $post->supplier_id,
                        'party_type' => 'supplier',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $purchase_order = $this->general_model->getRecords('*', 'purchase_order', array(
                        'purchase_order_party_id' => $post->supplier_id,
                        'purchase_order_party_type' => 'supplier',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $purchase = $this->general_model->getRecords('*', 'purchase', array(
                        'purchase_party_id' => $post->supplier_id,
                        'purchase_party_type' => 'supplier',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $purchase_return = $this->general_model->getRecords('*', 'purchase_return', array(
                        'purchase_return_party_id' => $post->supplier_id,
                        'purchase_return_party_type' => 'supplier',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $purchase_credit_note = $this->general_model->getRecords('*', 'purchase_credit_note', array(
                        'purchase_credit_note_party_id' => $post->supplier_id,
                        'purchase_credit_note_party_type' => 'supplier',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    $purchase_debit_note = $this->general_model->getRecords('*', 'purchase_debit_note', array(
                        'purchase_debit_note_party_id' => $post->supplier_id,
                        'purchase_debit_note_party_type' => 'supplier',
                        'delete_status' => 0,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
                    
                    if (in_array($data['supplier_module_id'], $data['active_delete'])) {
                        if ($payment_voucher || $purchase_order || $purchase || $purchase_return || $purchase_credit_note || $purchase_debit_note) {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal"><a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete_button btn btn-xs btn-app"><i class="fa fa-trash"></i></a></span>';
                        } else {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"><a href="javascript:void(0);" class="btn btn-app delete_button" data-id="' . $supplier_id . '" data-path="supplier/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i></a></span>';
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
            $this->load->view('supplier/list', $data);
        }
    }
    public function add() {
        $data = $this->get_default_country_state();
        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "supplier_id";
        $table_name = "supplier";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('supplier/add', $data);
    }

    public function add_bulk_upload_supplier()
    {
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/supplierCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();

        if (!$this->upload->do_upload('bulk_supplier')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_supplier',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        }else {
            $data = $this->get_default_country_state();
            $customer_module_id = $this->config->item('supplier_module');
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

                    if(!empty($allDataInSheet)){
                        if(strtolower($allDataInSheet[1]['A']) == 'supplier type' && strtolower($allDataInSheet[1]['B']) == 'company/firm name' && strtolower($allDataInSheet[1]['C']) == 'gst number' && strtolower($allDataInSheet[1]['D']) == 'country' && strtolower($allDataInSheet[1]['E']) == 'state' && strtolower($allDataInSheet[1]['F']) == 'city' && strtolower($allDataInSheet[1]['G']) == 'address' && strtolower($allDataInSheet[1]['H']) == 'pin code' && strtolower($allDataInSheet[1]['I']) == 'pan number' && strtolower($allDataInSheet[1]['J']) == 'tan number' && strtolower($allDataInSheet[1]['K']) == 'contact person name' && strtolower($allDataInSheet[1]['L']) == 'contact number' && strtolower($allDataInSheet[1]['M']) == 'email' && strtolower($allDataInSheet[1]['N']) == 'payment days' && strtolower($allDataInSheet[1]['O']) == 'department'){

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
                            $primary_id = "supplier_id";
                            $table_name = "supplier";
                            $date_field_name = "added_date";
                            $current_date = date('Y-m-d');

                            $purchase_ledger = $this->config->item('purchase_ledger');
                            $default_supplier_id = $purchase_ledger['SUPPLIER'];
                            $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($default_supplier_id);

                            foreach($allDataInSheet as $row){
                                $supplier_type = strtolower(trim($row['A']));
                                $supplier_name = strtolower(trim($row['B']));
                                $gst_number = trim($row['C']);
                                $country = strtolower(trim($row['D']));
                                $states = strtolower(trim($row['E']));
                                $city = strtolower(trim($row['F']));
                                $address = trim($row['G']);
                                $email = trim($row['M']);
                                $payment_due = trim($row['N']);
                                $pin_code = trim($row['H']);
                                $pan_number = trim($row['I']);
                                $tan_number = trim($row['J']);
                                $department = trim($row['O']);
                                $customer_country_id = '';
                                $customer_state_id = '';
                                $customer_city_id = '';
                                $supplier_ledger_id = '';
                                $is_add = true;
                                $error = '';
                                $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                if($supplier_type != '' && !empty($supplier_type)){
                                    if($supplier_type == 'company' || $supplier_type == 'firm' || $supplier_type == 'private limited company' || $supplier_type == 'proprietorship' || $supplier_type == 'partnership' || $supplier_type == 'one person company' || $supplier_type == 'limited liability partnership'){
                                        if($supplier_type == 'firm'){
                                            $supplier_type = 'individual';
                                        }
                                        if($supplier_name != '' && !empty($supplier_name)){
                                            $supplier_check = $this->Bulk_SupplierValidation($supplier_name);
                                            if($supplier_check["rows"] <= 0){
                                                $supplier_ary = array(
                                                                'ledger_name' => trim($row['B']),
                                                                'second_grp' => '',
                                                                'primary_grp' => 'Sundry Creditors',
                                                                'main_grp' => 'Current Liabilities',
                                                                'default_ledger_id' => 0,
                                                                'default_value' => trim($row['B']),
                                                                'amount' => 0
                                                            );
                                                if(!empty($supplier_ledger_name)){
                                                    $supplier_ledger = $supplier_ledger_name->ledger_name;
                                                    /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                                                    $supplier_ledger = str_ireplace('{{X}}',trim($row['B']), $supplier_ledger);
                                                    $supplier_ary['ledger_name'] = $supplier_ledger;
                                                    $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                                                    $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                                                    $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                                                    $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                                                }
                                                $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                                                /*$supplier_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                    'ledger_name' => trim($row['B']),
                                                    'subgrp_2' => 'Sundry Creditors',
                                                    'subgrp_1' => '',
                                                    'main_grp' => 'Current Liabilities',
                                                    'amount' =>  0
                                                ));*/
                                                if($gst_number != '' && $is_add == true){
                                                    if(!preg_match("/^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/", $gst_number)){
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
                                                                                $customer_city_id = $city_bulk[$city];
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
                                                                                        if(!preg_match('/^[0-9]+$/', $pin_code)){
                                                                                            $is_add = false;
                                                                                            $error = "Invalid Pin Code";
                                                                                        }
                                                                                    }
                                                                                    if($payment_due != '' && $is_add == true){
                                                                                        if ($payment_due < 0 || $payment_due > 365 ) {
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
                                        $error = "Supplier Name Should Not Empty";
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Wrong Supplier Type";
                                    }
                                }else{
                                    $is_add = false;
                                    $error = "Supplier Type Should Not Empty";
                                }
                                if($is_add){
                                    $headers = array(
                                        "supplier_name" => trim($row['B']),  
                                        "supplier_code" => $invoice_number,
                                        "supplier_type" => $supplier_type,
                                        "supplier_address" => $address,
                                        "supplier_country_id" => $customer_country_id,
                                        "supplier_state_id" => $customer_state_id,
                                        "supplier_city_id" => $customer_city_id,
                                        "supplier_gstin_number" => $gst_number,
                                        "ledger_id" => $supplier_ledger_id,
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        "supplier_contact_person" => trim($row['K']),
                                        "updated_date" => "",
                                        "updated_user_id" => "",
                                        "supplier_pan_number" => $pan_number,
                                        "supplier_tan_number" => $tan_number,
                                        "supplier_mobile" => trim($row['L']),
                                        "supplier_postal_code"=> $pin_code,
                                        "supplier_email" => $email,
                                        "payment_days" => $payment_due
                                    );
                                    if($id = $this->general_model->insertData($table_name, $headers)){
                                        $supplier_name = $row['B'];
                                        $this->createTransOption_Supplier($supplier_name, $id);
                                        $txt_shipping_code = $invoice_number . "-1";

                                        $shipping_address_data = array(
                                        "shipping_address" => $address,
                                        "primary_address" => 'yes',
                                        "shipping_party_id" => $id,
                                        "shipping_party_type" => 'supplier',
                                        "contact_person" => trim($row['K']),
                                        "department" => trim($row['O']),
                                        "email" => $email,
                                        "shipping_gstin" => $gst_number,
                                        "contact_number" => trim($row['L']),
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        "country_id" => $customer_country_id,
                                        "state_id" => $customer_state_id,
                                        "city_id" => $customer_city_id,
                                        "shipping_code" => $txt_shipping_code,
                                        "address_pin_code" => $pin_code,
                                        "updated_date" => "",
                                        "updated_user_id" => ""
                                    );
                                    $table = "shipping_address";
                                    $this->general_model->insertData($table, $shipping_address_data);
                                    }
                                }else {
                                    $error_array[] = $error_log;
                                }
                                if(!$is_add && !empty($row)){
                                    array_unshift($row,$error);
                                    array_push($errors_email, array_values($row));
                                }
                                if(!empty($error_array)){
                                    $errorMsg = implode('<br>', $error_array);
                                    $this->session->set_flashdata('bulk_error_supplier',$errorMsg);
                                    /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array));  */  
                                }else{
                                    $successMsg = 'Supplier imported successfully.';
                                    $this->session->set_flashdata('bulk_success_customer',$successMsg);/*
                                    $this->session->set_userdata('bulk_success', $successMsg); */ 
                                }
                            } 
                            $table = "log";
                                $log_data = array(
                                                'user_id' => $this->session->userdata('SESS_USER_ID'),
                                                'table_id' => 0,
                                                'table_name' => 'supplier',
                                                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                                'message' => 'Bulk Supplier Inserted. File_Name->'.$Updata['uploadData']['file_name']);
                                            $this->general_model->insertData($table, $log_data);

                                $shipping_log_data = array(
                                                'user_id' => $this->session->userdata('SESS_USER_ID'),
                                                'table_id' => 0,
                                                'table_name' => 'shipping_address',
                                                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                                'message' => 'Bulk Shipping Address Inserted. File_Name->'.$Updata['uploadData']['file_name']);
                                            $this->general_model->insertData($table, $shipping_log_data);
                        }else{
                            $this->session->set_flashdata('bulk_error_supplier',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }
                    }else{
                        $this->session->set_flashdata('bulk_error_supplier',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_supplier',"Error on file upload, please try again.");
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
           $resp = $this->send_csv_mail($errors_email,'Supplier Bulk Import Error Logs, <br><br> PFA,',"Supplier bulk upload error logs in <{$import_xls_file}>",$to);
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
            $this->session->set_flashdata('bulk_error_customer',"Error email has been sent to registered email ID");
             /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
        }
        redirect("supplier", 'refresh');
    }
    function send_csv_mail ($csvData, $body, $subject,$to) {

        /*$to = 'harish.sr@aavana.in';*/
        $path = 'uploads/SupplierErrors/error.csv';
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
            $this->session->set_flashdata('email_send_supplier', 'success');
        }
        
        return $resp;
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

       $string = 's.*,sh.department';
       $table = 'supplier s';
       $join = ['shipping_address sh' => 's.supplier_id=sh.shipping_party_id#left'];
       $where = array(
            's.supplier_id' => $id,
            's.delete_status' => 0,
            'sh.shipping_party_type' => 'supplier',
            'sh.primary_address' => 'yes'
          );
       $data['data'] = $this->general_model->getJoinRecords($string, $table, $where, $join, $order = "");
        /*echo '<pre>';
        print_r($this->db->last_query());
        exit();*/
        $string = 'con.*';
        $table = 'contact_person con';
        $where = array(
            'con.party_id' => $id,
            'con.party_type' => 'supplier',
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
            'st.country_id' => $data['data'][0]->supplier_country_id);
        $data['state'] = $this->general_model->getRecords($string, $table, $where);
        $string = 'ct.*';
        $table = 'cities ct';
        $where = array(
            'ct.state_id' => $data['data'][0]->supplier_state_id);
        $data['city'] = $this->general_model->getRecords($string, $table, $where);

        /*  $string                       = 'st.*';
          $table                        = 'states st';
          $where                        = array(
          'st.country_id' => $data['contact_person'][0]->contact_person_country_id );
          $data['contact_person_state'] = $this->general_model->getRecords($string, $table, $where);
          $string                       = 'ct.*';
          $table                        = 'cities ct';
          $where                        = array(
          'ct.state_id' => $data['contact_person'][0]->contact_person_state_id );
          $data['contact_person_city']  = $this->general_model->getRecords($string, $table, $where); */

        $data['additional_info'] = $additional_info = $this->general_model->getRecords('*', 'supplier_additional_info', array(
            'supplier_id' => $id));
        $data['additional_info_count'] = count($additional_info);
        $this->load->view('supplier/edit', $data);
    }

    public function edit_supplier() {

        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('supplier_id_edit');
        $ledger_id = $this->input->post('ledger_id_edit');
        $country = $this->input->post('cmb_country_edit');
        $state = $this->input->post('cmb_state_edit');

        $supplier_name = trim($this->input->post('vendor_name_edit'));
        $this->general_model->updateData('ledgers', array(
            'ledger_title' => $supplier_name), array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'ledger_id' => $ledger_id));
        $supplier_data = array(
            "supplier_name" => $this->input->post('vendor_name_edit'),
            "supplier_code" => $this->input->post('supplier_code_edit'),
            "supplier_type" => $this->input->post('vendor_type_edit'),
            "supplier_address" => $this->input->post('address_edit'),
            "supplier_country_id" => $this->input->post('cmb_country_edit'),
            "supplier_state_id" => $this->input->post('cmb_state_edit'),
            "supplier_city_id" => $this->input->post('cmb_city_edit'),
            "supplier_gstin_number" => $this->input->post('gst_number_edit'),
            "supplier_contact_person" => $this->input->post('txt_contact_person_edit'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "supplier_postal_code" => $this->input->post('txt_pin_code'),
            "supplier_pan_number" => $this->input->post('txt_pan_number'),
            "supplier_tan_number" => $this->input->post('txt_tan_number'),
            "supplier_mobile" => $this->input->post('txt_contact_number'),
            "supplier_email" => $this->input->post('txt_email'),
            "payment_days" => $this->input->post('payment_days')
            );

        if($this->input->post('dl_no')){
            $supplier_data['drug_licence_no'] = $this->input->post('dl_no');
        }
        
        if($this->input->post('dl_no')){
            $supplier_data['HSI_number'] = $this->input->post('hsi_no');
        }

        $table = "supplier";
        $where = array("supplier_id" => $id);
        if ($this->general_model->updateData($table, $supplier_data, $where)) {
            $this->updateTransOption_Supplier($supplier_name, $id);
            /* Update ledger name */
            $successMsg = 'Supplier Updated Successfully';
            $this->session->set_flashdata('supplier_success',$successMsg);
            $purchase_ledger = $this->config->item('purchase_ledger');
            $default_supplier_id = $purchase_ledger['SUPPLIER'];
            $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($default_supplier_id);
            if(!empty($supplier_ledger_name)){
                $supplier_ledger = $supplier_ledger_name->ledger_name;
                $supplier_name = str_ireplace('{{X}}',$supplier_name, $supplier_ledger);
            }
            $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$supplier_name}' WHERE ledger_id='{$ledger_id}'");

            $this->general_model->deleteData('supplier_additional_info', array('supplier_id' => $id));
            
            $shipping_address_data = array(
                    "shipping_address" => $this->input->post('address_edit'),          
                    "shipping_party_type" => 'supplier',
                    "contact_person" => $this->input->post('txt_contact_person_edit'),
                    "department" => $this->input->post('department'),
                    "email" => $this->input->post('txt_email'),
                    "shipping_gstin" => $this->input->post('gst_number_edit'),
                    "country_id" => $this->input->post('cmb_country_edit'),
                    "state_id" => $this->input->post('cmb_state_edit'),
                    "city_id" => $this->input->post('cmb_city_edit'),
                    "updated_date" => date('Y-m-d'),
                    'contact_number' => $this->input->post('txt_contact_number'),
                    "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                    "address_pin_code" => $this->input->post('txt_pin_code'),
                    "email" => $this->input->post('txt_email')
                );
           /* echo '<pre>';
            print_r($shipping_address_data);
            exit;*/
            $table = "shipping_address";
            $where = array("shipping_party_id" => $id, "primary_address" => 'yes');
            $this->general_model->updateData($table, $shipping_address_data, $where);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'supplier',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Supplier Updated');
            $this->general_model->insertData($table, $log_data);

            if (isset($_POST['custom_field'])) {
                $data = array();
                $custom_field = $_POST['custom_field'];
                $custom_lablel = $_POST['custom_lablel'];
                $length = count($_POST['custom_field']);

                for ($i = 0; $i < $length; $i++) {
                    $data[$i]['value'] = $custom_field[$i];
                    $data[$i]['column_name'] = $custom_lablel[$i];
                    $data[$i]['supplier_id'] = $id;
                    $data[$i]['added_user_id'] = $this->session->userdata('SESS_USER_ID');
                    $data[$i]['added_date'] = date('Y-m-d');
                }

                $this->db->insert_batch('supplier_additional_info', $data);
            }
        } else {
            $errorMsg = 'Supplier Update Unsuccessful';
            $this->session->set_flashdata('supplier_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Supplier can not be Updated.');
            redirect("supplier", 'refresh');
        }
        redirect("supplier", 'refresh');
    }

    public function add_supplier() {

        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $country = $this->input->post('cmb_country');
        $state = $this->input->post('cmb_state');

        $title = trim($this->input->post('vendor_name'));
        $subgroup = "Supplier";
        $purchase_ledger = $this->config->item('purchase_ledger');
        $default_supplier_id = $purchase_ledger['SUPPLIER'];
        $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($default_supplier_id);
            
        $supplier_ary = array(
                        'ledger_name' => $title,
                        'second_grp' => '',
                        'primary_grp' => 'Sundry Creditors',
                        'main_grp' => 'Current Liabilities',
                        'default_ledger_id' => 0,
                        'default_value' => $title,
                        'amount' => 0
                    );
        if(!empty($supplier_ledger_name)){
            $supplier_ledger = $supplier_ledger_name->ledger_name;
            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
            $supplier_ledger = str_ireplace('{{X}}',$title, $supplier_ledger);
            $supplier_ary['ledger_name'] = $supplier_ledger;
            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
        }
        $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
        /*$supplier_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                    'ledger_name' => $title,
                                                    'subgrp_2' => 'Sundry Creditors',
                                                    'subgrp_1' => '',
                                                    'main_grp' => 'Current Liabilities',
                                                    'amount' =>  0
                                    ));*/
        //if ($ledger_id = $this->ledger_model->addLedger($title, $subgroup)) {
            $supplier_data = array(
                    "supplier_name"                  => $this->input->post('vendor_name'),  
                    "supplier_code"                  => $this->input->post('supplier_code'),
                    "supplier_type"                  => $this->input->post('vendor_type'),
                    "supplier_address"               => $this->input->post('address'),
                    "supplier_country_id"            => $this->input->post('cmb_country'),
                    "supplier_state_id"              => $this->input->post('cmb_state'),
                    "supplier_city_id"               => $this->input->post('cmb_city'),
                    "supplier_gstin_number"          => $this->input->post('gst_number'),
                    "ledger_id"                      => $supplier_ledger_id,
                    "added_date"                     => date('Y-m-d'),
                    "added_user_id"                  => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"                      => $this->session->userdata('SESS_BRANCH_ID'),
                    "supplier_contact_person"        => $this->input->post('txt_contact_person'),
                    "updated_date"                   => "",
                    "updated_user_id"                => "",
                    "supplier_pan_number"            => $this->input->post('txt_pan_number'),
                    "supplier_tan_number"            => $this->input->post('txt_tan_number'),
                    "supplier_mobile"             => $this->input->post('txt_contact_number'),
                    "supplier_postal_code"           => $this->input->post('txt_pin_code'),
                    "supplier_email"                 => $this->input->post('email_address'),
                    "payment_days"                   => $this->input->post('payment_days')
                );

            if($this->input->post('dl_no')){
                $supplier_data['drug_licence_no'] = $this->input->post('dl_no');
            }

            if($this->input->post('dl_no')){
                $supplier_data['HSI_number'] = $this->input->post('hsi_no');
            }

            $table  = "supplier";
            if ($id  = $this->general_model->insertData($table, $supplier_data)){
                //$reference_number = $this->input->post('reference_number');
                $this->createTransOption_Supplier($title, $id);
                $successMsg = 'Supplier Added Successfully';
                $this->session->set_flashdata('supplier_success',$successMsg);
                $supplier_name = $this->input->post('supplier_code');
                $txt_shipping_code = $supplier_name."-1";

            /*$shipping_address_data = array(
                "shipping_address" => $this->input->post('address'),
                "primary_address" => 'yes',
                "shipping_party_id" => $id,
                "shipping_party_type" => 'supplier',
                "contact_person" => $this->input->post('txt_contact_person'),
                "department" => '',
                "email" => $this->input->post('txt_email'),
                "shipping_gstin" => $this->input->post('gst_number'),
                "contact_number" => '',
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "supplier_contact_person" => $this->input->post('txt_contact_person'),
                "updated_date" => "",
                "updated_user_id" => "");*/
           /* $table = "supplier";
            if ($id = $this->general_model->insertData($table, $supplier_data)) {*/
                /*$reference_number = $this->input->post('reference_number');
                $txt_shipping_code = $reference_number . "-1";*/

                $shipping_address_data = array(
                    "shipping_address" => $this->input->post('address'),
                    "primary_address" => 'yes',
                    "shipping_party_id" => $id,
                    "shipping_party_type" => 'supplier',
                    "contact_person" => $this->input->post('txt_contact_person'),
                    "department" => $this->input->post('department'),
                    "email" => $this->input->post('txt_email'),
                    "shipping_gstin" => $this->input->post('gst_number'),
                    "contact_number" => $this->input->post('txt_contact_number'),
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                    "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                    "country_id" => $this->input->post('cmb_country'),
                    "state_id" => $this->input->post('cmb_state'),
                    "city_id" => $this->input->post('cmb_city'),
                    "shipping_code" => $txt_shipping_code,
                    "updated_date" => "",
                    "updated_user_id" => "",
                    "address_pin_code" => $this->input->post('txt_pin_code'),
                    "email" => $this->input->post('email_address')
                );
              /*  echo '<pre>';
                print_r($shipping_address_data);
                exit();*/

                $table = "shipping_address";
                $this->general_model->insertData($table, $shipping_address_data);
                $table = "log";
                $log_data = array(
                    'user_id' => $this->session->userdata('SESS_USER_ID'),
                    'table_id' => $id,
                    'table_name' => 'supplier',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'message' => 'Supplier Inserted');
                $this->general_model->insertData($table, $log_data);
                if (isset($_POST['custom_field'])) {
                    $data = array();
                    $custom_field = $_POST['custom_field'];
                    $custom_lablel = $_POST['custom_lablel'];
                    $length = count($_POST['custom_field']);

                    for ($i = 0; $i < $length; $i++) {
                        $data[$i]['value'] = $custom_field[$i];
                        $data[$i]['column_name'] = $custom_lablel[$i];
                        $data[$i]['supplier_id'] = $id;
                        $data[$i]['added_user_id'] = $this->session->userdata('SESS_USER_ID');
                        $data[$i]['added_date'] = date('Y-m-d');
                    }

                    $this->db->insert_batch('supplier_additional_info', $data);
                }
            } else {
                $errorMsg = 'Supplier Add Unsuccessful';
                $this->session->set_flashdata('supplier_error',$errorMsg);
                $this->session->set_flashdata('fail', 'Supplier can not be Inserted.');
                redirect("supplier", 'refresh');
            }
            //  echo json_encode($id);
       // }
    // }
        // echo json_encode($id);
        redirect("supplier", 'refresh');
    }

    public function add_supplier_ajax() {
        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);

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

        $supplier_code = strtoupper(trim($this->input->post('supplier_code')));

        $supplier_exist = $this->general_model->getRecords('count(*) num', 'supplier', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'supplier_code' => $supplier_code,
        ));

        if ($supplier_exist[0]->num > 0) {
            $primary_id = "supplier_id";
            $table_name = "supplier";
            $date_field_name = "added_date";
            $current_date = date('Y-m-d');
            $access_settings = $section_modules['access_settings'];
            $supplier_code = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

            $supplier_code = strtoupper(trim($supplier_code));
        }

        $supplier_name = strtoupper(trim($this->input->post('supplier_name')));
        /*$supplier_exist = $this->general_model->getRecords('count(*) num', 'ledgers', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'ledger_title' => $supplier_name,
        ));

        if ($supplier_exist[0]->num > 0) {

            $supplier_name = $supplier_name . " - " . rand(10000, 99999);
        }*/


        $title = strtoupper(trim($supplier_name));

        $subgroup = "Supplier";

        $purchase_ledger = $this->config->item('purchase_ledger');
        $default_supplier_id = $purchase_ledger['SUPPLIER'];
        $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($default_supplier_id);
            
        $supplier_ary = array(
                        'ledger_name' => $title,
                        'second_grp' => '',
                        'primary_grp' => 'Sundry Creditors',
                        'main_grp' => 'Current Liabilities',
                        'default_ledger_id' => 0,
                        'default_value' => $title,
                        'amount' => 0
                    );
        if(!empty($supplier_ledger_name)){
            $supplier_ledger = $supplier_ledger_name->ledger_name;
            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
            $supplier_ledger = str_ireplace('{{X}}',$title, $supplier_ledger);
            $supplier_ary['ledger_name'] = $supplier_ledger;
            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
        }
        $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);

        if ($supplier_ledger_id) {
            $result = array(
                "supplier_name" => $supplier_name,
                "supplier_code" => $supplier_code,
                "supplier_type" => $this->input->post('supplier_type'),
                "supplier_address" => $this->input->post('supplier_address'),
                "supplier_country_id" => $this->input->post('country'),
                "supplier_state_id" => $this->input->post('state'),
                "supplier_city_id" => $this->input->post('city'),
                "supplier_mobile"                => $this->input->post('mobile'),
                //"supplier_telephone"             => $this->input->post('telephone'),
                "supplier_email"                 => $this->input->post('email'),
                "supplier_postal_code"           => $this->input->post('postal_code'),
                // "supplier_website"               => $this->input->post('website'),
                "supplier_gstin_number" => $this->input->post('supplier_gstin_number'),
                "supplier_pan_number"            => $this->input->post('panno'),
                "supplier_tan_number"            => $this->input->post('tanno'),
                "supplier_state_code" => $this->input->post('state_code'),
                "supplier_contact_person" => $this->input->post('supplier_contact_person'),
                // "supplier_contact_person_id"     => $id,
                "payment_days" => $this->input->post('payment_days'),
                "ledger_id" => $supplier_ledger_id,
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => "",
                "updated_user_id" => "");

            if($this->input->post('dl_no')){
                $result['drug_licence_no'] = $this->input->post('dl_no');
            }
            
            if($this->input->post('dl_no')){
                $result['HSI_number'] = $this->input->post('hsi_no');
            }

            $table = "supplier";
            if ($id = $this->general_model->insertData($table, $result)) {
                $this->createTransOption_Supplier($supplier_name, $id);
                $reference_number = $this->input->post('reference_number');
                $txt_shipping_code = $supplier_code . "-1";
                $shipping_address_data = array(
                    "shipping_address" => $this->input->post('supplier_address'),
                    "primary_address" => 'yes',
                    "shipping_party_id" => $id,
                    "shipping_party_type" => 'supplier',
                    "contact_person" => $this->input->post('supplier_contact_person'),
                    "department" => $this->input->post('department'),
                    "email" => $this->input->post('email'),
                    "shipping_gstin" => $this->input->post('supplier_gstin_number'),
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                    "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                    "country_id" => $this->input->post('country'),
                    "state_id" => $this->input->post('state'),
                    "city_id" => $this->input->post('city'),
                    "contact_number" => $this->input->post('mobile'),
                    "shipping_code" => $txt_shipping_code,
                    "address_pin_code" => $this->input->post('postal_code'),
                    "updated_date" => "",
                    "updated_user_id" => ""
                );
               /* echo '<pre>';
                print_r( $shipping_address_data );
                exit();*/
                $table = "shipping_address";
                $this->general_model->insertData($table, $shipping_address_data);
                $log_data = array(
                    'user_id' => $this->session->userdata('SESS_USER_ID'),
                    'table_id' => $id,
                    'table_name' => 'supplier',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'message' => 'Supplier Inserted(Modal)');
                $table = "log";
                $this->general_model->insertData($table, $log_data);
                $string = "*";
                $table = "supplier";
                $where = array(
                    "delete_status" => 0,
                    "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
                $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");

                $data['id'] = $id;
                $ledger_string = "l.*";
                $ledger_where = array(
                    "l.delete_status" => 0,
                    "s.delete_status" => 0,
                    "s.branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                    "l.branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
                $table = "ledgers l";
                $join = [
                    'supplier s' => 's.ledger_id = l.ledger_id'];
                $data['ledgers_data'] = $this->general_model->getJoinRecords($ledger_string, $table, $ledger_where, $join);
                $data['ledger_id'] = $supplier_ledger_id;
                echo json_encode($data);
            }
        }
    }

    public function delete() {
        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $string = "ledger_id";
        $table = "supplier";
        $where = array(
            "supplier_id" => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        // $contact_person_id               = $data['data'][0]->supplier_contact_person_id;
        $this->general_model->updateData('ledgers', array(
            'delete_status' => 1), array(
            'ledger_id' => $data['data'][0]->ledger_id));
        $table = "supplier";
        $data = array(
            "delete_status" => 1);
        $where = array(
            "supplier_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
            $successMsg = 'Supplier Deleted Successfully';
            $this->session->set_flashdata('supplier_success',$successMsg);
            $table = "contact_person";
            $data = array(
                "delete_status" => 1);
            $where = array(
                "party_id" => $id,
                "party_type" => 'supplier'
            );
            $this->general_model->updateData($table, $data, $where);
            $table = "shipping_address";
            $data = array(
            "delete_status" => 1);
            $where = array("shipping_party_id" => $id);
            $this->general_model->updateData($table, $data, $where);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'supplier',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Supplier Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('supplier');
        } else {
            $errorMsg = 'Supplier Delete Unsuccessful';
            $this->session->set_flashdata('supplier_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Supplier can not be Deleted.');
            redirect("supplier", 'refresh');
        }
    }

    public function get_check_supplier() {
        $supplier_code = strtoupper(trim($this->input->post('supplier_code')));
        $supplier_id = $this->input->post('supplier_id');
        $data = $this->general_model->getRecords('count(*) num', 'supplier', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'supplier_code' => $supplier_code,
            'supplier_id!=' => $supplier_id));
        echo json_encode($data);
    }

    public function edit_modal($id) {
        $id = $this->encryption_url->decode($id);
        $supplier_module_id = $this->config->item('supplier_module');
        $data['module_id'] = $supplier_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($supplier_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 's.*';
        $table = 'supplier s';
        // $join['contact_person c'] = 'c.contact_person_id=s.supplier_contact_person_id';
        $where = array(
            's.supplier_id' => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");

        $string = 'c.*';
        $table = 'countries c';
        $where = array(
            'c.delete_status' => 0);
        $data['country'] = $this->general_model->getRecords($string, $table, $where);
        $string = 'st.*';
        $table = 'states st';
        $where = array(
            'st.country_id' => $data['data'][0]->supplier_country_id);
        $data['state'] = $this->general_model->getRecords($string, $table, $where);
        $string = 'ct.*';
        $table = 'cities ct';
        $where = array(
            'ct.state_id' => $data['data'][0]->supplier_state_id);
        $data['city'] = $this->general_model->getRecords($string, $table, $where);
        echo json_encode($data);
        //$this->load->view('supplier/edit', $data);
    }

    public function SupplierValidation(){
        $customer_name = trim($this->input->post('cust_name'));
        $id = $this->input->post('id');
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        
        $rows = $this->db->query("SELECT customer_id FROM customer WHERE customer_name = '".$customer_name."' AND branch_id = '".$branch_id."'")->num_rows();

        $rows1 = $this->db->query("SELECT supplier_id FROM supplier WHERE supplier_name = '".$customer_name."' AND supplier_id != '{$id}' AND branch_id = '".$branch_id."'")->num_rows();

        echo  json_encode(array('rows' => $rows + $rows1));
    }
    public function Bulk_SupplierValidation($customer_name, $id = 0){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $rows = $this->db->query("SELECT customer_id FROM customer WHERE customer_name = '".$customer_name."' AND branch_id = '".$branch_id."'")->num_rows();

        $rows1 = $this->db->query("SELECT supplier_id FROM supplier WHERE supplier_name = '".$customer_name."' AND supplier_id != '{$id}' AND branch_id = '".$branch_id."'")->num_rows();
        
        return array('rows' => $rows + $rows1);
    }


    public function createTransOption_Supplier($suplier_name, $suplier_id){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $user_id = $this->session->userdata('SESS_USER_ID');
        $date = date('Y-m-d');
        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type','suppliers');
        $this->db->where('branch_id',$branch_id);
        $sup = $this->db->get();
        $result_option = $sup->result();
        $option_array = array();
         $i = 1;
        if($suplier_name!= ''){
            foreach ($result_option as $key1 => $value1) { 
                $supplier_option = $value1->customise_option;
                $parent_id = $value1->id;

                $supplier_option = str_ireplace('{{X}}',$suplier_name, $supplier_option);
                $option_array[$i]['purpose_option'] = $supplier_option;
                $option_array[$i]['parent_id'] =  $parent_id;
                $option_array[$i]['payee_id'] = $suplier_id;
                $option_array[$i]['branch_id'] = $branch_id;
                $option_array[$i]['added_user_id'] = $user_id;
                $option_array[$i]['added_date'] = $date;
                $i = $i + 1;
            }  
        } 
        if(!empty($option_array)){
            $table = "tbl_transaction_purpose_option";
            $this->db->insert_batch($table, $option_array);
        }
    }


    public function updateTransOption_Supplier($suplier_name, $suplier_id){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $user_id = $this->session->userdata('SESS_USER_ID');

        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type','suppliers');
        $this->db->where('branch_id',$branch_id);
        $sup = $this->db->get();
        $result_option = $sup->result();
        $option_array = array();    
        $date = date('Y-m-d');
             
        foreach ($result_option as $key1 => $value1) { 
            $deposit_option = $value1->customise_option;
            $parent_id = $value1->id;

            $deposit_option = str_ireplace('{{X}}',$suplier_name, $deposit_option);
            $option_array['purpose_option'] = $deposit_option;
            $option_array['parent_id'] =  $parent_id;
            $option_array['payee_id'] = $suplier_id;
            $option_array['branch_id'] = $branch_id;
            $option_array['added_user_id'] = $user_id;
            $option_array['added_date'] = $date;
            $where = array("payee_id" => $suplier_id,"parent_id"=>$parent_id);
             $table = "tbl_transaction_purpose_option";
            $this->general_model->updateData($table, $option_array, $where);
        }

    }
    public function exportSupplierReportExcel(){
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $table_columns = array("Vendor Code", "Supplier Type", "Company/Firm Name", "GST Number", "Contact Person Name", "Country", "State", "City", "Address", "PIN Code","PAN Number","TAN Number", "Contact Number", "Email", "Payment Days");

        $column = 0;

        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }
        $list_data = $this->common->supplier_list_field();
        $posts = $this->general_model->getPageJoinRecords($list_data);
        // echo '<pre>';
        // print_r($posts);
        // exit();
        $excel_row = 2;
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $value->supplier_code);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value->supplier_type);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value->supplier_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $value->supplier_gstin_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,$value->supplier_contact_person);
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $value->country_name);  
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $value->state_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $value->city_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $value->supplier_address);
                $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $value->supplier_postal_code);
                $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $value->supplier_pan_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $value->supplier_tan_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $value->supplier_mobile);
                $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $value->supplier_email);
                 $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $value->payment_days);
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "Supplier Data{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }
    public function get_check_supplier_code() {
        $supplier_code = strtoupper(trim($this->input->post('supplier_code')));
        $supplier_id = $this->input->post('supplier_id');
        $data = $this->general_model->getRecords('count(*) num', 'supplier', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'supplier_code' => $supplier_code));
        echo json_encode($data);
    }

}
