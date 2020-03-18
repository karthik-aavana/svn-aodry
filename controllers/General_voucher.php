<?php

defined('BASEPATH') OR exit('No direct script access allowed');

Class General_voucher extends MY_Controller
{
    public $data;
    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'product_model',
                'service_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index(){
        $general_voucher_module_id       = $this->config->item('general_voucher_module');
        $data['module_id']               = $general_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['voucher_type'] = 'journal';
        $data['redirect_uri'] = 'general_voucher';

        if($this->session->userdata('bulk_success')){
            $data['bulk_success'] = $this->session->userdata('bulk_success');
            $this->session->unset_userdata('bulk_success');
        }elseif ($this->session->userdata('bulk_error')) {
            $data['bulk_error'] = $this->session->userdata('bulk_error');
            $this->session->unset_userdata('bulk_error');
        }

        if (!empty($this->input->post())){
            $columns             = array(
                    0 => 'gv.general_voucher_id',
                    1 => 'gv.voucher_date',
                    2 => 'gv.voucher_number',
                    3 => 'gv.reference_number',
                    4 => 'gv.receipt_amount' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order') [0]['column']];
            $dir                 = $this->input->post('order') [0]['dir'];
            $list_data           = $this->common->general_voucher_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search') ['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search') ['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } 

            $send_data = array();

            if (!empty($posts)) {
                foreach ($posts as $post)
                {
                    $general_voucher_id           = $this->encryption_url->encode($post->general_voucher_id);
                    $nestedData['action'] = '<input type="checkbox" value="'.$general_voucher_id.'" name="check_voucher" vtype="journal">';
                    $nestedData['voucher_date']   = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = '<a href="' . base_url('general_voucher/view_details/') . $general_voucher_id . '">' . $post->voucher_number . '</a>';

                    $nestedData['invoice_number'] = str_replace(",", ",<br/>", $post->reference_number);
                    $nestedData['grand_total']    = $post->currency_symbol . ' ' . $this->precise_amount(str_replace(",", ",<br/>", $post->receipt_amount),2);
                    $nestedData['from_account']   = $post->from_account;
                    $nestedData['to_account']     = $post->to_account;

                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view('general_voucher/list', $data);
        }
    }

    function view_details($id)
    {
        $general_voucher_id              = $this->encryption_url->decode($id);
        $general_voucher_module_id       = $this->config->item('general_voucher_module');

        $data['module_id']               = $general_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $voucher_details = $this->common->general_voucher_details($general_voucher_id);
        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);

        $this->load->view('general_voucher/view_details', $data);
    }

    public function AddNewVoucher(){
        $voucher_type = $this->input->post('voucher_type');
        $general_voucher_module_id       = $this->config->item('general_voucher_module');
        if($voucher_type == 'contra') $general_voucher_module_id = $this->config->item('contra_voucher_module');
        if($voucher_type == 'bank') $general_voucher_module_id = $this->config->item('bank_voucher_module');
        if($voucher_type == 'cash') $general_voucher_module_id = $this->config->item('cash_voucher_module');
        $data['module_id'] = $general_voucher_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data = array_merge($data,$section_modules);

        $update = array();
        $update_voucher = array();
        $flag = true;
        
        $ledgers = $this->input->post('ledgers');
        $invoice_date = date('Y-m-d');
        if(null != $this->input->post('invoice_date')){
            $invoice_date = date('Y-m-d',strtotime($this->input->post('invoice_date')));
            $valid = $this->ValidateVoucherDate($invoice_date);
            if($valid <= 0) $flag = false;
        }
        $voucher_id = 0;
        $mode = 'add';
        if('0' != $this->input->post('voucher_id')){
            $voucher_id =  $this->encryption_url->decode($this->input->post('voucher_id'));
            $mode = 'edit';
        }

        $prefix = $voucher_type;
        if($voucher_type == 'journal') $prefix = 'general';
       
        $sub_table = 'accounts_'.$prefix.'_voucher';
        $sub_primary_id = 'accounts_'.$prefix.'_id';
        $primary_id      = $prefix."_voucher_id";
        $table_name      = $prefix.'_voucher';
        $date_field_name = "voucher_date";
        $current_date    = date('Y-m-d');
        
        
        $headers = array(
            "voucher_date"      => $invoice_date ,
            "reference_id"      => '0' ,
            "reference_type"    => $voucher_type,
            "reference_number"  => $this->input->post('invoice_number'),
            "receipt_amount"    => /*$this->input->post('invoice_total')*/0,
            "from_account"      => '' ,
            "to_account"        => '' ,
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
            "description"       => '' ,
            "added_date"        => date('Y-m-d') ,
            "added_user_id"     => $this->session->userdata('SESS_USER_ID') ,
            "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
            "currency_id"       => $this->session->userdata('SESS_DEFAULT_CURRENCY'),
            "note1"             => $this->input->post('invoice_narration') ,
            "note2"             => ''
        );


        /*$is_exist = $this->db->query("SELECT voucher_id FROM tbl_sales_voucher WHERE firm_id='".$this->input->post('firm_id')."' AND company_id = '".$this->input->post('company_id')."' ");*/ //
        /*if($is_exist->num_rows()== 0){*/
            if($flag){
                /*$this->db->select('balance_id');
                $this->db->where('firm_id',$this->input->post('firm_id'));
                $this->db->where('acc_id',$this->input->post('company_id'));
                $this->db->where('balance_upto_date >=',$update['invoice_date']);
                $dt_qry = $this->db->get('tbl_default_balance_date');
                if($dt_qry->num_rows() == 0){*/
                    $old_ledger_ids = $not_deleted_ids = array();
                    
                    if($mode == 'edit'){
                        $id = $this->general_model->updateData($table_name, $headers,array($primary_id => $voucher_id ));
                        $insert_id = $voucher_id;
                        $string = '*';
                        $where = array($primary_id => $voucher_id);
                        $old_voucher_items = $this->general_model->getRecords($string , $sub_table , $where , $order = "");
                        $old_ledger_ids = $this->getValues($old_voucher_items,'ledger_id');
                        $log_data = array(
                                        'user_id' => $this->session->userdata('SESS_USER_ID'),
                                        'table_id' => $id,
                                        'table_name' => $table_name,
                                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                        'message' => 'Voucher Updated');
                                        $log_table = $this->config->item('log_table');
                                        $this->general_model->insertData($log_table , $log_data);
                    }else{
                       
                        $voucher_number  = $this->generate_invoice_number($data['access_settings'] , $primary_id , $table_name , $date_field_name , $current_date);
                        $headers['voucher_number'] = $voucher_number;
                        $this->db->insert($table_name,$headers);
                        $insert_id = $this->db->insert_id();
                        $log_data = array(
                                        'user_id' => $this->session->userdata('SESS_USER_ID'),
                                        'table_id' => $insert_id,
                                        'table_name' => $table_name,
                                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                        'message' => 'Voucher Inserted');
                                        $log_table = $this->config->item('log_table');
                                        $this->general_model->insertData($log_table , $log_data);
                    }
                 
                    if($insert_id){
                        foreach ($ledgers as $key => $value) {
                            $update_voucher = array();
                            $update_voucher[$prefix.'_voucher_id'] = $insert_id;
                            $update_voucher['ledger_id'] = $value['ledger_id'];
                            $update_voucher['voucher_amount'] = $value['voucher_amount'];
                            if($value['amount_type'] == 'CR'){
                                $update_voucher['dr_amount'] = 0;
                                $update_voucher['cr_amount'] = $update_voucher['voucher_amount'];
                            }else{
                                $update_voucher['cr_amount'] = 0;
                                $update_voucher['dr_amount'] = $update_voucher['voucher_amount'];
                            }

                            $update_voucher['delete_status']    = 0;
                            $update_voucher = $this->db->escape_str($update_voucher);
                            if (($led_key = array_search($value['ledger_id'], $old_ledger_ids)) !== false) {
                                unset($old_ledger_ids[$led_key]);
                                $accounts_voucher_id = $old_voucher_items[(int)$led_key]->$sub_primary_id;
                                array_push($not_deleted_ids,$accounts_voucher_id );
                                $update_voucher[$sub_primary_id] = $accounts_voucher_id;
                                $where  = array($sub_primary_id => $accounts_voucher_id );

                                $post_data = array('data' => $update_voucher,
                                                    'where' => $where,
                                                    'voucher_date' => $headers['voucher_date'],
                                                    'table' => $table_name,
                                                    'sub_table' => $sub_table,
                                                    'primary_id' => $primary_id,
                                                    'sub_primary_id' => $primary_id
                                                );
                                $this->general_model->updateBunchVoucherCommon($post_data);
                                $this->general_model->updateData($sub_table , $update_voucher , $where);
                            }else{
                                $this->db->insert($sub_table,$update_voucher);
                                $update_voucher['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
                                $update_voucher['amount_type'] = $value['amount_type'];
                                $this->general_model->addBunchVoucher($update_voucher,$invoice_date);
                            }
                        }

                        if(!empty($old_voucher_items)){
                            $revert_ary = array();
                            foreach ($old_voucher_items as $key => $value) {
                                if(!in_array($value->$sub_primary_id, $not_deleted_ids)){
                                    $revert_ary[] = $value;
                                    $where      = array($sub_primary_id => $value->$sub_primary_id );
                                    $vou_data = array('delete_status' => 1 );
                                    $this->general_model->updateData($sub_table , $vou_data , $where);
                                }
                            }
                            if(!empty($revert_ary)) $this->general_model->revertLedgerAmount($revert_ary,$headers['voucher_date']);
                        }
                        $this->data['flag'] = true;
                        $this->data['msg'] = 'Voucher added successfully!';
                        if($mode == 'edit') $this->data['msg'] = 'Voucher updated successfully!';
                        
                    }
                /*}else{
                    $this->data['flag'] = false;
                    $this->data['msg'] = "You have already added default opening balance for this date.";
                }*/
            }else{
                $this->data['flag'] = false;
                $this->data['msg'] = "Financial year is not created for this duration";
            }
        /*}else{
            $this->data['flag'] = false;
            $this->data['msg'] = "Can't add duplicate invoice/voucher number!";
        }*/
        echo json_encode($this->data);
        exit;
    }

    public function ImportVoucher(){
        $data =  $insData = array();
        $error_log = '';
        $this->voucher_type = $this->input->post('voucher_type');
        $redirect_url = $this->input->post('redirect_uri');

        $path = 'uploads/voucherCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
       // $config['allowed_types'] = 'xls|xlsx|csv';
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();
        
        if (!$this->upload->do_upload('bulk_voucher')) {
            /*$error = array('error' => );*/
            $this->session->set_userdata('bulk_error', $this->upload->display_errors());
        } else {
            $general_voucher_module_id       = $this->config->item('general_voucher_module');
            $data['module_id']               = $general_voucher_module_id;
            $modules                         = $this->modules;
            $privilege                       = "view_privilege";
            $data['privilege']               = $privilege;
            $general_modules                 = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
            $data = array_merge($data,$general_modules);
            $data['general_modules'] = $general_modules;
            $contra_voucher_module_id = $this->config->item('contra_voucher_module');
            $data['contra_modules']= $this->get_section_modules($contra_voucher_module_id, $modules, $privilege);

            $cash_voucher_module_id = $this->config->item('cash_voucher_module');
            $data['cash_modules'] = $this->get_section_modules($cash_voucher_module_id, $modules, $privilege);

            $bank_voucher_module_id = $this->config->item('bank_voucher_module');
            $data['bank_modules'] = $this->get_section_modules($bank_voucher_module_id, $modules, $privilege);
            
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
                        /*if($allDataInSheet[1]['A'] == 'voucher_number' && $allDataInSheet[1]['B'] == 'invoice_date' && $allDataInSheet[1]['C'] == 'invoice_no' && $allDataInSheet[1]['D'] == 'narration' && $allDataInSheet[1]['E'] == 'invoice_total'){*/
                        if(strtolower($allDataInSheet[1]['A']) == 'voucher type' && strtolower(str_replace(' ','', $allDataInSheet[1]['B'])) == 'invoice/voucherdate' && strtolower(str_replace(' ','', $allDataInSheet[1]['C'])) == 'invoice/vouchernumber' && strtolower($allDataInSheet[1]['D']) == 'narration' && strtolower(str_replace(' ','',$allDataInSheet[1]['E'])) == 'invoice/vouchertotal'){
                            
                            $header_row = array_shift($allDataInSheet);
                            $ledgers_exist = $this->ledger_model->GetLedgersName();
                            
                            if($ledgers_exist != ''){ 
                                $added = 0;
                                $current_date = date('Y-m-d');
                                $table_name = '';
                                foreach($allDataInSheet as $row){ 
                                    $ledgers_ary = $ledgers_add = array();
                                    $slice = array_slice($row,5);
                                    $is_added = false;
                                    $added_error = '';
                                    if(!empty($slice))$ledgers_ary = array_chunk($slice, 3);
                                    // Prepare data for DB insertion
                                    
                                    if(trim($row['A']) != '' && !empty($ledgers_ary)){
                                        $voucher_type = strtolower(trim($row['A']));
                                        
                                        if(in_array($voucher_type, $this->config->item('voucher_types'))){
                                            $cr_amnt = $dr_amnt = 0;
                                            $prefix = $voucher_type;
                                            if($voucher_type == 'journal') $prefix = 'general';
                                            $sub_table = 'accounts_'.$prefix.'_voucher';
                                            $sub_primary_id = 'accounts_'.$prefix.'_id';
                                            $primary_id      = $prefix."_voucher_id";
                                            $table_name      = $prefix.'_voucher';
                                            $date_field_name = "voucher_date";

                                            $voucher_number  = $this->generate_invoice_number($data[$prefix.'_modules']['access_settings'] , $primary_id , $table_name , $date_field_name , $current_date);

                                            $invoice_total = (float)str_replace(',', '', trim($row['E']));
                                            $invoice_total = 0;
                                            foreach ($ledgers_ary as $key => $value) {
                                                $ledger_id = $this->FindLedgerId($value[0], $ledgers_exist);
                                                
                                                if($value[0] != '' && $ledger_id != ''){
                                                    
                                                    if(strtolower($value[2]) == 'cr' && $value[1] != ''){
                                                        $cr_amnt += (float)str_replace(',', '', $value[1]);
                                                    }else{
                                                        $dr_amnt += (float)str_replace(',','',$value[1]);
                                                    }
                                                    $ledgers_add[] = array('ledger_id'=>$ledger_id,'voucher_amount'=> (float)str_replace(',','',$value[1]),'amount_type' => strtoupper($value[2]));
                                                }
                                            }
                                           
                                            /*$Invoice_date = $this->getInvoiceDateFormate($row['B'],$company_id,$firm_id);*/
                                            $Invoice_date = date('Y-m-d',strtotime($row['B']));
                                            
                                            /*$this->db->select('balance_id');
                                            $this->db->where('firm_id',$this->input->post('firm_id'));
                                            $this->db->where('acc_id',$this->input->post('company_id'));
                                            $this->db->where('balance_upto_date >=',$Invoice_date);
                                            $dt_qry = $this->db->get('tbl_default_balance_date');*/
                                            $valid_date = $this->ValidateVoucherDate($Invoice_date);

                                            $is_add = true;//$dt_qry->num_rows() > 0 || 
                                            if($row['C'] == '' || $valid_date <= 0 || (string)$cr_amnt !== (string)$dr_amnt || empty($ledgers_add)) $is_add = false;

                                            if($voucher_type == 'purchase' || $voucher_type == 'sales'){
                                                if((string)$invoice_total !== (string)$cr_amnt) $is_add = false;
                                            }
                                            
                                            if($is_add){
                                                $headers = array(
                                                    'voucher_number' => $voucher_number,
                                                    "voucher_date"      => $Invoice_date ,
                                                    "reference_id"      => '0' ,
                                                    "reference_type"    => $voucher_type,
                                                    "reference_number"  => trim($row['C']),
                                                    "receipt_amount"    => $invoice_total,
                                                    "from_account"      => '' ,
                                                    "to_account"        => '' ,
                                                    "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                                                    "description"       => '' ,
                                                    "added_date"        => date('Y-m-d') ,
                                                    "added_user_id"     => $this->session->userdata('SESS_USER_ID') ,
                                                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                                                    "currency_id"       => $this->session->userdata('SESS_DEFAULT_CURRENCY'),
                                                    "note1"             => trim($row['D']),
                                                    "note2"             => ''
                                                );
                                                
                                                $this->db->insert($table_name,$headers);
                                                $insert_id = $this->db->insert_id();
                                               
                                                if($insert_id){
                                                    foreach ($ledgers_add as $key => $value) {
                                                        $update_voucher = array();
                                                        $update_voucher[$prefix.'_voucher_id'] = $insert_id;
                                                        $update_voucher['ledger_id'] = $value['ledger_id'];
                                                        $update_voucher['voucher_amount'] = $value['voucher_amount'];
                                                        if($value['amount_type'] == 'CR'){
                                                            $update_voucher['dr_amount'] = 0;
                                                            $update_voucher['cr_amount'] = $update_voucher['voucher_amount'];
                                                        }else{
                                                            $update_voucher['cr_amount'] = 0;
                                                            $update_voucher['dr_amount'] = $update_voucher['voucher_amount'];
                                                        }

                                                        $update_voucher['delete_status']    = 0;
                                                        $update_voucher = $this->db->escape_str($update_voucher);
                                                        
                                                        $this->db->insert($sub_table,$update_voucher);
                                                        $update_voucher['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
                                                        $update_voucher['amount_type'] = $value['amount_type'];
                                                        $this->general_model->addBunchVoucher($update_voucher,$Invoice_date);
                                                        $added++;
                                                    }
                                                    $data['flag'] = true;
                                                    $data['msg'] = 'Voucher Added successfully!';
                                                    $is_added = true;
                                                }
                                            }else{
                                                $err= 'default error!';
                                                /*if($dt_qry->num_rows() > 0) $err=' default closing balance defined for this date!'; */
                                                /*if($row['D'] == '') $err = $row['C'].' narration required! <br>';*/
                                                /*if($voucher_type == 'purchase' || $voucher_type == 'sales'){
                                                    if((string)$invoice_total !== (string)$cr_amnt) 
                                                    $err = 'Invalid amount or ledger!';
                                                }*/
                                                if ((string)$cr_amnt !== (string)$dr_amnt) 
                                                $err = 'Invalid amount or ledger!';

                                                if (empty($ledgers_add)) $err = 'Ledgers not matching in existing records';
                                                
                                                if($row['C'] == '') $err = 'Invoice number required!';
                                                if($valid_date <= 0)  $err = 'Financial year not found for this date!';
                                                $added_error = $err;
                                                $error_log .= $row['C'].$err.'<br>';
                                            }
                                        }else{
                                            $error_log .= $row['C'].' Undefined voucher type! <br>';
                                            $added_error = ' Undefined voucher type!';
                                        }
                                    }
                                   /* $row['Error'] = $added_error;*/
                                    if(!$is_added && !empty($row)){
                                        array_unshift($row,$added_error);
                                        array_push($errors_email, array_values($row));
                                    }
                                    // Insert data
                                }
                                if($error_log != ''){
                                    $this->session->set_userdata('bulk_error', $error_log);
                                }else{
                                    $successMsg = 'Voucher imported successfully.';
                                    $this->session->set_userdata('bulk_success', $successMsg);
                                }
                                $log_data = array(
                                        'user_id' => $this->session->userdata('SESS_USER_ID'),
                                        'table_id' => 0,
                                        'table_name' => $table_name,
                                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                        'message' => 'Voucher Inserted');
                                        $log_table = $this->config->item('log_table');
                                        $this->general_model->insertData($log_table , $log_data);
                            }else{
                                $this->session->set_userdata('bulk_error', "Ledgers not found!");
                            }
                        }else{
                            $this->session->set_userdata('bulk_error', "File formate not correct!");
                        }
                    }else{
                        $this->session->set_userdata('bulk_error', 'Empty file!');
                    }
                    
                } catch (Exception $e) {
                    $this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');
                }
            }
        }
        
        if(!empty($errors_email)){
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = 'chetna.b@aavana.in';
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
            $resp = $this->send_csv_mail($errors_email,'Voucher Bulk Import Error Logs, <br><br> PFA,',"Voucher bulk upload error logs in <{$import_xls_file}>",$to);
            
            $this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');
        }
        
        redirect($redirect_url);
    }

    function send_csv_mail ($csvData, $body, $subject,$to) {

        /*$to = 'chetna.b@aavana.in';*/
        $path = 'uploads/VoucherErrors.csv';
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
        $mail->setFrom("chetna.b@aavana.in", $subject);
        $mail->addReplyTo("chetna.b@aavana.in", $subject);
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
            $this->session->set_flashdata('email_send', 'success');
        }
        
        return $resp;
    }

    public function getVoucherDetail(){
        $voucher_type = $this->input->post('voucher_type');
        $voucher_id =  $this->encryption_url->decode($this->input->post('voucher_id'));
        
        $prefix = $voucher_type;
        if($voucher_type == 'journal') $prefix = 'general';
        $sub_table = 'accounts_'.$prefix.'_voucher';

        $primary_id      = $prefix."_voucher_id";
        $table_name      = $prefix.'_voucher';
       
        $voucher_row = $this->db->select("v.*,sub.*")->from($table_name.' as v')->join($sub_table.' as sub ','v.'.$primary_id.'=sub.'.$primary_id)->where('branch_id', $this->session->userdata('SESS_BRANCH_ID'))->where('v.delete_status', 0)->where('sub.delete_status', 0)->where('v.'.$primary_id,$voucher_id)->get()->result();
        $vouchers = array();
        if(!empty($voucher_row)){
            foreach ($voucher_row as $key => $value) {
                $value->voucher_date = date('d-m-Y',strtotime($value->voucher_date));
                $value->receipt_amount = $this->precise_amount($value->receipt_amount,2);
                $value->voucher_amount = $this->precise_amount($value->voucher_amount,2);
                $vouchers[] = $value;
            }
        }

        echo json_encode($vouchers);
        exit;
    }

    public function deleteVoucher(){
        $voucher_type = $this->input->post('voucher_type');
        $voucher_id =  $this->encryption_url->decode($this->input->post('voucher_id'));
        
        $prefix = $voucher_type;
        if($voucher_type == 'journal') $prefix = 'general';
        $sub_table = 'accounts_'.$prefix.'_voucher';
        $sub_primary_id = 'accounts_'.$prefix.'_id';
        $primary_id      = $prefix."_voucher_id";
        $table_name      = $prefix.'_voucher';
        $where = array($primary_id =>$voucher_id);
        $data = array();
        if($id = $this->general_model->deleteCommonVoucher(array('table' => $table_name, 'where' => $where),array('table' => $sub_table, 'where' => $where))){
            $data['flag'] = true;
            $data['msg'] = 'Voucher Deleted Successfully';
            $log_data = array(
                    'user_id' => $this->session->userdata('SESS_USER_ID'),
                    'table_id' => $id,
                    'table_name' => $table_name,
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'message' => 'Voucher Deleted');
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
        }else{
            $data['flag'] = false;
            $data['msg'] = 'Voucher Delete Unsuccessful';
        }

        echo json_encode($data);
    }

    public function FindLedgerId($value='',$array) {

        foreach ($array as $key => $val) {

            if (strtolower(trim($val['ledger_name'])) == strtolower(trim($value))) {
               return $val['ledger_id'];
            }
        }
        return null;
    }

    public function add(){      
        $journal_voucher_module_id = $this->config->item('journal_voucher_module');
      
        $data['module_id'] = $journal_voucher_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);


         $data              = $this->get_default_country_state();
        /* presents all the needed */
        $data = array_merge($data,$section_modules);
        $data['customer'] = $this->customer_call();
        $data['transaction_purpose'] = $this->transaction_purpose_call();
        $data['privilege'] = $privilege;
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['bank_account']     = $this->bank_account_call_new();

        
        
        $this->load->view('general_voucher/add', $data);
    }


    public function edit($id){   
        $id  = $this->encryption_url->decode($id);   
        $journal_voucher_module_id = $this->config->item('journal_voucher_module');
      
        $data['module_id'] = $journal_voucher_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);


         $data              = $this->get_default_country_state();
        /* presents all the needed */
        $data = array_merge($data,$section_modules);
        $data['customer'] = $this->customer_call();
        $data['transaction_purpose'] = $this->transaction_purpose_call();
        $data['privilege'] = $privilege;
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['bank_account']     = $this->bank_account_call_new();

        $data['data']  = $this->general_model->getRecords('*', 'tbl_journal_voucher', array(
            'journal_voucher_id' => $id));
        $data['journal_voucher_id']       = $id;

        $transaction_id = $data['data'][0]->transaction_purpose_id;
        $voucher_row = $this->transaction_purpose_call_det($transaction_id);
        $data['input_type']  =  $voucher_row[0]->input_type;
        $data['voucher_type'] = $voucher_row[0]->voucher_type;
        $data['category'] = $voucher_row[0]->transaction_category;
        $data['purpose'] = $voucher_row[0]->transaction_purpose;
        $input_type = $voucher_row[0]->input_type;
        $string = "*";
        $table = "tbl_shareholder";
        $where = array(
            "delete_status" => 0,
            "sharholder_type" => $input_type,
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID')
        );
        $order = array("sharholder_name" => "asc");
        $data['sharholder'] =  $this->general_model->getRecords($string, $table, $where, $order = "");
        
        
        $this->load->view('general_voucher/edit', $data);
    }



    public function add_general_voucher(){    
        $journal_voucher_module_id         = $this->config->item('journal_voucher_module');
        $data['module_id']                 = $journal_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "add_privilege";
        $data['privilege']                 = "add_privilege";
        $section_modules                   = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);

       if ($this->input->post('payment_mode') == "other payment mode") {
            $payment_via = $this->input->post('payment_via');
            $reff_number = $this->input->post('ref_number');
        } else {
            $payment_via = "";
            $reff_number = "";
        } 
        $general_ledger = $this->config->item('general_ledger');
         $input_type = $this->input->post('input_type');
        if($input_type != 'interest fixed' && $input_type != 'interest recurring' && $input_type != 'interest other'  &&  $input_type != 'interest liability'){
        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode          = $bank_acc_payment_mode[0];
            $from_acc              = $bank_acc_payment_mode[1];
           
            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array('bank_account_id' => $payment_mode));
            $from_ledger_id =  $ledger_bank_acc[0]->ledger_id;
            $ledger_from = $ledger_bank_acc[0]->ledger_id;

        } else {
            $payment_mode     = $this->input->post('payment_mode');
            $from_acc         = $this->input->post('payment_mode');
            /*$ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));*/

            if ($from_acc != '') {
                $default_payment_id = $general_ledger['Other_Payment'];
                if (strtolower($payment_mode) == "cash"){
                    $default_payment_id = $general_ledger['Cash_Payment'];
                }
                $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
                $default_payment_ary = array(
                                'ledger_name' => $from_acc,
                                'second_grp' => '',
                                'primary_grp' => 'Cash & Cash Equivalent',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_payment_id,
                                'amount' => 0
                            );
                if(!empty($default_payment_name)){
                    $default_led_nm = $default_payment_name->ledger_name;
                    $default_payment_ary['ledger_name'] = str_ireplace('{{PAYMENT_MODE}}',$from_acc, $default_led_nm);
                    $default_payment_ary['primary_grp'] = $default_payment_name->sub_group_1;
                    $default_payment_ary['second_grp'] = $default_payment_name->sub_group_2;
                    $default_payment_ary['main_grp'] = $default_payment_name->main_group;
                    $default_payment_ary['default_ledger_id'] = $default_payment_name->ledger_id;
                }
                $from_ledger_id = $this->ledger_model->getGroupLedgerId($default_payment_ary);
            }
        }
    }else{
        $payment_mode     = 'none';
    }

        $cheque_date = $this->input->post('cheque_date');
        $transaction_purpose = $this->input->post('transaction_purpose');
        if (!$cheque_date) {
            $cheque_date = null;
        }else{
            $cheque_date = date('Y-m-d', strtotime($this->input->post('cheque_date')));
        }
        
        $primary_id      = "journal_voucher_id";
        $table_name      = 'tbl_journal_voucher';
        $date_field_name = "voucher_date";
        $current_date    =date('Y-m-d',strtotime($this->input->post('voucher_date')));

        $voucher_number  = $this->generate_invoice_number($data['access_settings'] , $primary_id , $table_name , $date_field_name , $current_date);        
         
        $voucher_type = $this->input->post('voucher_type');        
       
        
        $transaction_purpose_id = $this->input->post('trans_purpose');
        $voucher_amount = $this->input->post('receipt_amount');
        $interest_expense_amount = ($this->input->post('interest_amount'))?$this->input->post('interest_amount'):0;

        $others_amount_tax = ($this->input->post('others_amount'))?$this->input->post('others_amount'):0;
        $transaction_details = $this->get_transaction_purpose_det($transaction_purpose_id);
        $transaction_ledger = $transaction_details[0]->purpose_option;
        $transaction_category = $transaction_details[0]->transaction_category;
        $payee_id = $transaction_details[0]->payee_id;

        $sgst_amount = ($this->input->post('txt_sgst'))?$this->input->post('txt_sgst'):0;
        $cgst_amount = ($this->input->post('txt_cgst'))?$this->input->post('txt_cgst'):0;
        $utgst_amount = ($this->input->post('txt_utgst'))?$this->input->post('txt_utgst'):0;
        $igst_amount = ($this->input->post('txt_igst'))?$this->input->post('txt_igst'):0;
        $cess_amount = ($this->input->post('txt_cess'))?$this->input->post('txt_cess'):0;
        $tds_amount = ($this->input->post('txt_tds'))?$this->input->post('txt_tds'):0;


        if($transaction_purpose == 'Advances'){
            if($input_type == 'suppliers'){
                $table = 'supplier';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'supplier_id' => $payee_id,
                'delete_status' => 0);
                $suppliers = $this->general_model->getRecords('*', $table, $where); 
                $to_ledger = $supplier_name = $suppliers[0]->supplier_name;              
                $to_acc = 'supplier-' . $suppliers[0]->supplier_name;
                $to_ledger_id = $suppliers[0]->ledger_id;

                $supplier_ledger_id = $suppliers[0]->ledger_id;

                if(!$supplier_ledger_id){
                    $supplier_ledger_id = $general_ledger['SUPPLIER'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                    'ledger_name' => $supplier_name,
                                    'second_grp' => '',
                                    'primary_grp' => 'Sundry Creditors',
                                    'main_grp' => 'Current Assets',
                                    'default_ledger_id' => $supplier_ledger_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$supplier_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;

                if($transaction_category == 'Advance repaid by vendor'){
                    $default_other_id = $general_ledger['Other_Charges'];
                    $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Other Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);
                    
                }
            }elseif($input_type == 'financial year'){ 
                $table = 'tbl_financial_year';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'year_id' => $payee_id);


                $financial_year = $this->general_model->getRecords('*', $table, $where); 
                $from_date = $financial_year[0]->from_date;
                $to_date = $financial_year[0]->to_date;
                $finance_year = date('Y',strtotime($from_date)) .' - '.date('y',strtotime($to_date));
                $to_ledger = $transaction_ledger;
                $to_acc = 'finance year-' . $finance_year;

                if ($transaction_ledger != '') {
                    $ledger = str_replace(' ', '_', $transaction_category);
                    $finance_ledger_id = $general_ledger[$ledger];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($finance_ledger_id);
                        
                    $supplier_ary = array(
                                    'ledger_name' => $transaction_ledger,
                                    'second_grp' => '',
                                    'primary_grp' => 'Advances',
                                    'main_grp' => 'Current Assets',
                                    'default_ledger_id' => $finance_ledger_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$finance_year, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                if( $transaction_category == 'Advance Tax Refund by Govt'){

                    $default_intrest_id = $general_ledger['Interest_Income'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Interest Income',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Incomes',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }
            }
        }else if($transaction_purpose == 'Capital Invested'){
           if($input_type == 'shareholder'){
                if($transaction_category == 'Preference share issue to shareholders'){
                    $to_ledger = 'Preference Share Capital A/c';
                    $default_capital_id = $general_ledger['Preference_Share_Capital_AC'];
                    $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                     $capital_ary = array(
                                            'ledger_name' => 'Preference Share Capital A/c',
                                            'second_grp' => '',
                                            'primary_grp' => '',
                                            'main_grp' => 'Capital',
                                            'default_ledger_id' => $default_capital_id,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($capital_ary)){
                                $capital_ledger = $capital_ac_name->ledger_name;
                                $capital_ary['ledger_name'] = $capital_ledger;
                                $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                                $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                                $capital_ary['main_grp'] = $capital_ac_name->main_group;
                                $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                            }
                            $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                            $to_acc = $transaction_ledger;
                    
                    $default_intrest_id = $general_ledger['Security_Premium'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Security Premium',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }

                if($transaction_category == 'Equity shares issued to shareholder'){
                    $to_ledger = 'Equity Share Capital A/c';
                    $default_capital_id = $general_ledger['Equity_Share_Capital_AC'];          
                    $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                     $capital_ary = array(
                                            'ledger_name' => 'Equity Share Capital A/c',
                                            'second_grp' => '',
                                            'primary_grp' => '',
                                            'main_grp' => 'Capital',
                                            'default_ledger_id' => $default_capital_id,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($capital_ary)){
                                $capital_ledger = $capital_ac_name->ledger_name;
                                $capital_ary['ledger_name'] = $capital_ledger;
                                $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                                $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                                $capital_ary['main_grp'] = $capital_ac_name->main_group;
                                $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                            }
                            $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                            $to_acc = $transaction_ledger;

                    $default_intrest_id = $general_ledger['Security_Premium'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Security Premium',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }
           }elseif($input_type == 'partner'){
                $default_capital_id = $general_ledger['Partner'];  
                $payee_id = $this->input->post('cmb_partner');
                $table = 'tbl_shareholder';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'id' => $payee_id,
                'delete_status' => 0);
                $partner = $this->general_model->getRecords('*', $table, $where); 
                $to_ledger = $sharholder_name = $partner[0]->sharholder_name;              
                $to_acc = 'partner-' . $partner[0]->sharholder_name;
                $to_ledger_id = $partner[0]->partner_ledger_id;
                $partner_ledger_id = $partner[0]->partner_ledger_id;        
           
             if(!$partner_ledger_id){
                    $defult_ledger_id = $general_ledger['Partner'];
                    $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($partner_ledger_id);
                        
                    $supplier_ary = array(
                                    'ledger_name' => $sharholder_name,
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $defult_ledger_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                        if(!empty($partner_ledger_name)){
                            $supplier_ledger = $partner_ledger_name->ledger_name;
                           
                            $supplier_ledger = str_ireplace('{{X}}',$sharholder_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $partner_ledger_name;
                            $supplier_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $partner_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                        }
                    $partner_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $partner_ledger_id;

           }else{
             if($transaction_category == 'Capital withdrawn by Proprietor'){
                $to_ledger = 'Drawings A/C';
                $default_capital_id = $general_ledger['Drawing_AC'];          
                $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                $capital_ary = array(
                                    'ledger_name' => 'Drawings A/C',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_capital_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($capital_ary)){
                        $capital_ledger = $capital_ac_name->ledger_name;
                        $capital_ary['ledger_name'] = $capital_ledger;
                        $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                        $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                        $capital_ary['main_grp'] = $capital_ac_name->main_group;
                        $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                    $to_acc = $transaction_ledger;

             }else{
                $to_ledger = 'Capital A/c';
                $default_capital_id = $general_ledger['Capital_AC'];          
                $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                $capital_ary = array(
                                    'ledger_name' => 'Capital A/c',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_capital_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($capital_ary)){
                        $capital_ledger = $capital_ac_name->ledger_name;
                        $capital_ary['ledger_name'] = $capital_ledger;
                        $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                        $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                        $capital_ary['main_grp'] = $capital_ac_name->main_group;
                        $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                    $to_acc = $transaction_ledger;
             }
            
            }
        }elseif($transaction_purpose == 'Cash transactions'){
           if($voucher_type == 'CONTRA A/C'){
                $to_ledger = 'Cash A/c';
                $default_cash_ac_id = $general_ledger['Cash_AC'];          
                $cash_ac_name = $this->ledger_model->getDefaultLedgerId($default_cash_ac_id);
                 $cash_ac_ary = array(
                                        'ledger_name' => 'Cash A/c',
                                        'second_grp' => '',
                                        'primary_grp' => 'Cash',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_cash_ac_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($cash_ac_ary)){
                        $cash_ac_ledger = $cash_ac_name->ledger_name;
                        $cash_ac_ary['ledger_name'] = $cash_ac_ledger;
                        $cash_ac_ary['primary_grp'] = $cash_ac_name->sub_group_1;
                        $cash_ac_ary['second_grp'] = $cash_ac_name->sub_group_2;
                        $cash_ac_ary['main_grp'] = $cash_ac_name->main_group;
                        $cash_ac_ary['default_ledger_id'] = $cash_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($cash_ac_ary);
                    $to_acc = $transaction_ledger;

                if($transaction_category =='Cash deposited in bank'){
                    $default_other_id = $general_ledger['Bank_Charges'];
                    $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Bank Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);
                }
           }else{
                $to_ledger = 'Suspense A/c';
                $default_supense_ac_id = $general_ledger['Suspense_AC'];          
                $suspense_ac_name = $this->ledger_model->getDefaultLedgerId($default_supense_ac_id);
                 $suspense_ac_ary = array(
                                        'ledger_name' => 'Suspense A/c',
                                        'second_grp' => '',
                                        'primary_grp' => 'NA',
                                        'main_grp' => 'Suspense',
                                        'default_ledger_id' => $default_supense_ac_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($suspense_ac_ary)){
                        $suspense_ledger = $suspense_ac_name->ledger_name;
                        $suspense_ac_ary['ledger_name'] = $suspense_ledger;
                        $suspense_ac_ary['primary_grp'] = $suspense_ac_name->sub_group_1;
                        $suspense_ac_ary['second_grp'] = $suspense_ac_name->sub_group_2;
                        $suspense_ac_ary['main_grp'] = $suspense_ac_name->main_group;
                        $suspense_ac_ary['default_ledger_id'] = $suspense_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($suspense_ac_ary);
                    $to_acc = $transaction_ledger;
           } 
        }elseif($transaction_purpose == 'Deposits'){

                
            if($input_type == 'fixed'){
                $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;
                
                $to_ledger = $deposit_name = 'Fixed Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'deposit-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Fixed_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($input_type == 'recurring'){
                $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;

                $to_ledger = $deposit_name = 'Recurring Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'deposit-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Recurring_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($input_type == 'rent'){
                $to_ledger = 'Rent Deposit';
                $default_rent_dep_id = $general_ledger['Rent_Deposit'];          
                $rent_dep_name = $this->ledger_model->getDefaultLedgerId($default_rent_dep_id);
                 $rent_dep_ary = array(
                                        'ledger_name' => 'Rent Deposit',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_rent_dep_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($rent_dep_ary)){
                        $rent_dep_ledger = $rent_dep_name->ledger_name;
                        $rent_dep_ary['ledger_name'] = $rent_dep_ledger;
                        $rent_dep_ary['primary_grp'] = $rent_dep_name->sub_group_1;
                        $rent_dep_ary['second_grp'] = $rent_dep_name->sub_group_2;
                        $rent_dep_ary['main_grp'] = $rent_dep_name->main_group;
                        $rent_dep_ary['default_ledger_id'] = $rent_dep_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($rent_dep_ary);
                    $to_acc = $transaction_ledger;

            }elseif($input_type == 'electricity'){
                $to_ledger = 'Electricity Deposit';
                $default_electricity_id = $general_ledger['Electricity_Deposit'];          
                $electricity_name = $this->ledger_model->getDefaultLedgerId($default_electricity_id);
                 $electricity_ary = array(
                                        'ledger_name' => 'Electricity Deposit',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_electricity_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($electricity_ary)){
                        $suspense_ledger = $electricity_name->ledger_name;
                        $electricity_ary['ledger_name'] = $suspense_ledger;
                        $electricity_ary['primary_grp'] = $electricity_name->sub_group_1;
                        $electricity_ary['second_grp'] = $electricity_name->sub_group_2;
                        $electricity_ary['main_grp'] = $electricity_name->main_group;
                        $electricity_ary['default_ledger_id'] = $electricity_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($electricity_ary);
                    $to_acc = $transaction_ledger;
                
            }elseif($input_type == 'water'){
                $to_ledger = 'Water Deposit';
                $default_water_dep_id = $general_ledger['Water_Deposit'];          
                $water_dep_name = $this->ledger_model->getDefaultLedgerId($default_water_dep_id);
                 $water_dep_ary = array(
                                        'ledger_name' => 'Water Deposit',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_water_dep_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($water_dep_ary)){
                        $water_dep_ledger = $water_dep_name->ledger_name;
                        $water_dep_ary['ledger_name'] = $water_dep_ledger;
                        $water_dep_ary['primary_grp'] = $water_dep_name->sub_group_1;
                        $water_dep_ary['second_grp'] = $water_dep_name->sub_group_2;
                        $water_dep_ary['main_grp'] = $water_dep_name->main_group;
                        $water_dep_ary['default_ledger_id'] = $water_dep_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($water_dep_ary);
                    $to_acc = $transaction_ledger;
                
            }elseif($input_type == 'other'){
                $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;

                $to_ledger = $deposit_name = $deposits[0]->others_name;
                $to_acc = 'deposit-' . $deposits[0]->others_name;
                $to_ledger_id = $supplier_ledger_id =  $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Other_Deposits'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;
            }
            if($voucher_type == 'RECEIPTS'){
                
            if($input_type == 'fixed' || $input_type == 'recurring'){
                    $default_intrest_id = $general_ledger['Interest_Receivable'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Interest Receivable',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Other Income',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }else{
                $default_other_id = $general_ledger['Other_Charges'];
                $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Other Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);
                    
                }
            }

        }elseif($transaction_purpose == 'Fixed Assset'){

                $table = 'tbl_fixed_assets';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'fixed_assets_id' => $payee_id,
                'delete_status' => 0);
                $deposits = $this->general_model->getRecords('*', $table, $where);
                $supplier_ledger_id = $deposits[0]->ledger_id;
                $to_ledger = $deposit_name = $deposits[0]->name_of_assets_purchase;
                $to_acc = 'Fixed Assets -' . $deposits[0]->name_of_assets_purchase;
                $assets_type = $deposits[0]->particulars;
                
                 if(!$supplier_ledger_id){
                    $supplier_ledger_id = $general_ledger['Fixed_Assets'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => $assets_type,
                                'main_grp' => 'Fixed Assets',
                                'default_ledger_id' => $supplier_ledger_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            $sub_group = $supplier_ledger_name->sub_group_1;
                            $supplier_ledger = str_ireplace('{{X}}',$deposit_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = str_ireplace('{{X}}',$assets_type, $sub_group);
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;
                //$voucher_type == 'RECEIPTS'
                 if($voucher_type == 'PAYMENTS'){
                    $default_other_id = $general_ledger['Other_Charges'];
                    $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Other Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);

                    // CGST
                    $default_input_cgst_id = $general_ledger['Input_CGST'];
                    $input_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cgst_id);
                   
                    $input_cgst_ary = array(
                                    'ledger_name' => 'Input CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cgst_ledger_name)){
                        $input_cgst_ledger = $input_cgst_ledger_name->ledger_name;
                        $input_cgst_ary['ledger_name'] = $input_cgst_ledger;
                        $input_cgst_ary['primary_grp'] = $input_cgst_ledger_name->sub_group_1;
                        $input_cgst_ary['second_grp'] = $input_cgst_ledger_name->sub_group_2;
                        $input_cgst_ary['main_grp'] = $input_cgst_ledger_name->main_group;
                        $input_cgst_ary['default_ledger_id'] = $input_cgst_ledger_name->ledger_id;
                    }
                    $input_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_cgst_ary);

                     // SGST
                    $default_input_sgst_id = $general_ledger['Input_SGST'];
                    $input_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_sgst_id);
                   
                    $input_sgst_ary = array(
                                    'ledger_name' => 'Input SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_sgst_ledger_name)){
                        $input_sgst_ledger = $input_sgst_ledger_name->ledger_name;
                        $input_sgst_ary['ledger_name'] = $input_sgst_ledger;
                    $input_sgst_ary['primary_grp'] = $input_sgst_ledger_name->sub_group_1;
                        $input_sgst_ary['second_grp'] = $input_sgst_ledger_name->sub_group_2;
                        $input_sgst_ary['main_grp'] = $input_sgst_ledger_name->main_group;
                        $input_sgst_ary['default_ledger_id'] = $input_sgst_ledger_name->ledger_id;
                    }
                    $input_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_sgst_ary);

                     // UTGST
                    $default_input_utgst_id = $general_ledger['Input_UTGST'];
                    $input_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_utgst_id);
                   
                    $input_utgst_ary = array(
                                    'ledger_name' => 'Input UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_utgst_ledger_name)){
                        $input_utgst_ledger = $input_utgst_ledger_name->ledger_name;
                        $input_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $input_utgst_ary['primary_grp'] = $input_utgst_ledger_name->sub_group_1;
                        $input_utgst_ary['second_grp'] = $input_utgst_ledger_name->sub_group_2;
                        $input_utgst_ary['main_grp'] = $input_utgst_ledger_name->main_group;
                        $input_utgst_ary['default_ledger_id'] = $input_utgst_ledger_name->ledger_id;
                    }
                    $input_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_utgst_ary);

                     // IGST
                    $default_input_igst_id = $general_ledger['Input_IGST'];
                    $input_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_igst_id);
                   
                    $input_igst_ary = array(
                                    'ledger_name' => 'Input IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_igst_ledger_name)){
                        $input_igst_ledger = $input_igst_ledger_name->ledger_name;
                        $input_igst_ary['ledger_name'] = $input_igst_ledger;
                        $input_igst_ary['primary_grp'] = $input_igst_ledger_name->sub_group_1;
                        $input_igst_ary['second_grp'] = $input_igst_ledger_name->sub_group_2;
                        $input_igst_ary['main_grp'] = $input_igst_ledger_name->main_group;
                        $input_igst_ary['default_ledger_id'] = $input_igst_ledger_name->ledger_id;
                    }
                    $input_igst_ledger_id = $this->ledger_model->getGroupLedgerId($input_igst_ary);

                     // Cess
                    $default_input_cess_id = $general_ledger['Input_Cess'];
                    $input_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cess_id);
                   
                    $input_cess_ary = array(
                                    'ledger_name' => 'Input Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cess_ledger_name)){
                        $input_cess_ledger = $input_cess_ledger_name->ledger_name;
                        $input_cess_ary['ledger_name'] = $input_cess_ledger;
                        $input_cess_ary['primary_grp'] = $input_cess_ledger_name->sub_group_1;
                        $input_cess_ary['second_grp'] = $input_cess_ledger_name->sub_group_2;
                        $input_cess_ary['main_grp'] = $input_cess_ledger_name->main_group;
                        $input_cess_ary['default_ledger_id'] = $input_cess_ledger_name->ledger_id;
                    }
                    $input_cess_ledger_id = $this->ledger_model->getGroupLedgerId($input_cess_ary);
                    
                }else{

                    /*$default_intrest_id = $general_ledger['Other_Incomes'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Other Incomes',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Incomes',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);*/

                $default_loss_sale_id = $general_ledger['Loss_on_sale_investment'];
                $loss_sale_name = $this->ledger_model->getDefaultLedgerId($default_loss_sale_id);
                $loss_sale_ary = array(
                                        'ledger_name' => 'Loss on sale of investment',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Preference shares invested by Shareholders',
                                        'default_ledger_id' => $default_loss_sale_id,
                                        'default_value' => 0,
                                       'amount' => 0
                                        );
            if(!empty($loss_sale_ary)){
                $loss_sale_ledger = $loss_sale_name->ledger_name;
                $loss_sale_ary['ledger_name'] = $loss_sale_ledger;
                $loss_sale_ary['primary_grp'] = $loss_sale_name->sub_group_1;
                $loss_sale_ary['second_grp'] = $loss_sale_name->sub_group_2;
                $loss_sale_ary['main_grp'] = $loss_sale_name->main_group;
                $loss_sale_ary['default_ledger_id'] = $loss_sale_name->ledger_id;
            }
            $loss_on_sale_id = $this->ledger_model->getGroupLedgerId($loss_sale_ary);

            $default_profit_sale_id = $general_ledger['Profit_on_sale_investment'];
            $profit_sale_name = $this->ledger_model->getDefaultLedgerId($default_profit_sale_id);
       
            $profit_sale_ary = array(
                        'ledger_name' => 'Profit on sale of investment',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Indirect Incomes',
                        'default_ledger_id' => $default_profit_sale_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
            if(!empty($profit_sale_ary)){
                    $profit_sale_ledger = $profit_sale_name->ledger_name;
                    $profit_sale_ary['ledger_name'] = $profit_sale_ledger;
                    $profit_sale_ary['primary_grp'] = $profit_sale_name->sub_group_1;
                    $profit_sale_ary['second_grp'] = $profit_sale_name->sub_group_2;
                    $profit_sale_ary['main_grp'] = $profit_sale_name->main_group;
                    $profit_sale_ary['default_ledger_id'] = $profit_sale_name->ledger_id;
            }
                $profit_sale_ledger_id = $this->ledger_model->getGroupLedgerId($profit_sale_ary);

                    // CGST
                    $default_output_cgst_id = $general_ledger['Output_CGST'];
                    $output_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cgst_id);
                   
                    $output_cgst_ary = array(
                                    'ledger_name' => 'Output CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cgst_ledger_name)){
                        $output_cgst_ledger = $output_cgst_ledger_name->ledger_name;
                        $output_cgst_ary['ledger_name'] = $output_cgst_ledger;
                        $output_cgst_ary['primary_grp'] = $output_cgst_ledger_name->sub_group_1;
                        $output_cgst_ary['second_grp'] = $output_cgst_ledger_name->sub_group_2;
                        $output_cgst_ary['main_grp'] = $output_cgst_ledger_name->main_group;
                        $output_cgst_ary['default_ledger_id'] = $output_cgst_ledger_name->ledger_id;
                    }
                    $output_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_cgst_ary);

                     // SGST
                    $default_output_sgst_id = $general_ledger['Output_SGST'];
                    $output_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_sgst_id);
                   
                    $output_sgst_ary = array(
                                    'ledger_name' => 'Output SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_sgst_ledger_name)){
                        $output_sgst_ledger = $output_sgst_ledger_name->ledger_name;
                        $output_sgst_ary['ledger_name'] = $output_sgst_ledger;
                    $output_sgst_ary['primary_grp'] = $output_sgst_ledger_name->sub_group_1;
                        $output_sgst_ary['second_grp'] = $output_sgst_ledger_name->sub_group_2;
                        $output_sgst_ary['main_grp'] = $output_sgst_ledger_name->main_group;
                        $output_sgst_ary['default_ledger_id'] = $output_sgst_ledger_name->ledger_id;
                    }
                    $output_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_sgst_ary);

                     // UTGST
                    $default_output_utgst_id = $general_ledger['Output_UTGST'];
                    $output_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_utgst_id);
                   
                    $output_utgst_ary = array(
                                    'ledger_name' => 'Output UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_utgst_ledger_name)){
                        $input_utgst_ledger = $output_utgst_ledger_name->ledger_name;
                        $output_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $output_utgst_ary['primary_grp'] = $output_utgst_ledger_name->sub_group_1;
                        $output_utgst_ary['second_grp'] = $output_utgst_ledger_name->sub_group_2;
                        $output_utgst_ary['main_grp'] = $output_utgst_ledger_name->main_group;
                        $output_utgst_ary['default_ledger_id'] = $output_utgst_ledger_name->ledger_id;
                    }
                    $output_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_utgst_ary);

                     // IGST
                    $default_output_igst_id = $general_ledger['Output_IGST'];
                    $output_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_igst_id);
                   
                    $output_igst_ary = array(
                                    'ledger_name' => 'Output IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_igst_ledger_name)){
                        $output_igst_ledger = $output_igst_ledger_name->ledger_name;
                        $output_igst_ary['ledger_name'] = $output_igst_ledger;
                        $output_igst_ary['primary_grp'] = $output_igst_ledger_name->sub_group_1;
                        $output_igst_ary['second_grp'] = $output_igst_ledger_name->sub_group_2;
                        $output_igst_ary['main_grp'] = $output_igst_ledger_name->main_group;
                        $output_igst_ary['default_ledger_id'] = $output_igst_ledger_name->ledger_id;
                    }
                    $output_igst_ledger_id = $this->ledger_model->getGroupLedgerId($output_igst_ary);

                     // Cess
                    $default_output_cess_id = $general_ledger['Output_Cess'];
                    $output_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cess_id);
                   
                    $output_cess_ary = array(
                                    'ledger_name' => 'Output Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cess_ledger_name)){
                        $output_cess_ledger = $output_cess_ledger_name->ledger_name;
                        $output_cess_ary['ledger_name'] = $output_cess_ledger;
                        $output_cess_ary['primary_grp'] = $output_cess_ledger_name->sub_group_1;
                        $output_cess_ary['second_grp'] = $output_cess_ledger_name->sub_group_2;
                        $output_cess_ary['main_grp'] = $output_cess_ledger_name->main_group;
                        $output_cess_ary['default_ledger_id'] = $output_cess_ledger_name->ledger_id;
                    }
                    $output_cess_ledger_id = $this->ledger_model->getGroupLedgerId($output_cess_ary);
                }
        }elseif($transaction_purpose == 'Tax receivables'){
            if($transaction_category =='Tax received from Income Tax'){
                $to_ledger = 'Income tax refund';
                $default_income_tax_id = $general_ledger['Income_tax_refund'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Income tax refund',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $to_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);
                        $to_acc = $income_tax_ledger;


                    $default_intrest_it_id = $general_ledger['Interest_on_Income_tax'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                   
                    $intrest_it_ary = array(
                                    'ledger_name' => 'Interest on Income tax refund',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Income',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                        $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $intrest_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);

                    $default_others_income_id = $general_ledger['Others_income'];
                    $others_income_ledger_name = $this->ledger_model->getDefaultLedgerId($default_others_income_id);
                   
                    $others_income_ary = array(
                                    'ledger_name' => 'Others',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Incomes',
                                    'default_ledger_id' => $default_others_income_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($others_income_ledger_name)){
                        $others_income_ledger = $others_income_ledger_name->ledger_name;
                        $others_income_ary['ledger_name'] = $others_income_ledger;
                        $others_income_ary['primary_grp'] = $others_income_ledger_name->sub_group_1;
                        $others_income_ary['second_grp'] = $others_income_ledger_name->sub_group_2;
                        $others_income_ary['main_grp'] = $others_income_ledger_name->main_group;
                        $others_income_ary['default_ledger_id'] = $others_income_ledger_name->ledger_id;
                    }
                    $others_income_id = $this->ledger_model->getGroupLedgerId($others_income_ary);

            }else{
                $to_acc = 'GST';
                // CGST
                    $default_output_cgst_id = $general_ledger['Output_CGST'];
                    $output_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cgst_id);
                   
                    $output_cgst_ary = array(
                                    'ledger_name' => 'Output CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cgst_ledger_name)){
                        $output_cgst_ledger = $output_cgst_ledger_name->ledger_name;
                        $output_cgst_ary['ledger_name'] = $output_cgst_ledger;
                        $output_cgst_ary['primary_grp'] = $output_cgst_ledger_name->sub_group_1;
                        $output_cgst_ary['second_grp'] = $output_cgst_ledger_name->sub_group_2;
                        $output_cgst_ary['main_grp'] = $output_cgst_ledger_name->main_group;
                        $output_cgst_ary['default_ledger_id'] = $output_cgst_ledger_name->ledger_id;
                    }
                    $output_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_cgst_ary);

                     // SGST
                    $default_output_sgst_id = $general_ledger['Output_SGST'];
                    $output_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_sgst_id);
                   
                    $output_sgst_ary = array(
                                    'ledger_name' => 'Output SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_sgst_ledger_name)){
                        $output_sgst_ledger = $output_sgst_ledger_name->ledger_name;
                        $output_sgst_ary['ledger_name'] = $output_sgst_ledger;
                    $output_sgst_ary['primary_grp'] = $output_sgst_ledger_name->sub_group_1;
                        $output_sgst_ary['second_grp'] = $output_sgst_ledger_name->sub_group_2;
                        $output_sgst_ary['main_grp'] = $output_sgst_ledger_name->main_group;
                        $output_sgst_ary['default_ledger_id'] = $output_sgst_ledger_name->ledger_id;
                    }
                    $output_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_sgst_ary);

                     // UTGST
                    $default_output_utgst_id = $general_ledger['Output_UTGST'];
                    $output_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_utgst_id);
                   
                    $output_utgst_ary = array(
                                    'ledger_name' => 'Output UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_utgst_ledger_name)){
                        $input_utgst_ledger = $output_utgst_ledger_name->ledger_name;
                        $output_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $output_utgst_ary['primary_grp'] = $output_utgst_ledger_name->sub_group_1;
                        $output_utgst_ary['second_grp'] = $output_utgst_ledger_name->sub_group_2;
                        $output_utgst_ary['main_grp'] = $output_utgst_ledger_name->main_group;
                        $output_utgst_ary['default_ledger_id'] = $output_utgst_ledger_name->ledger_id;
                    }
                    $output_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_utgst_ary);

                     // IGST
                    $default_output_igst_id = $general_ledger['Output_IGST'];
                    $output_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_igst_id);
                   
                    $output_igst_ary = array(
                                    'ledger_name' => 'Output IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_igst_ledger_name)){
                        $output_igst_ledger = $output_igst_ledger_name->ledger_name;
                        $output_igst_ary['ledger_name'] = $output_igst_ledger;
                        $output_igst_ary['primary_grp'] = $output_igst_ledger_name->sub_group_1;
                        $output_igst_ary['second_grp'] = $output_igst_ledger_name->sub_group_2;
                        $output_igst_ary['main_grp'] = $output_igst_ledger_name->main_group;
                        $output_igst_ary['default_ledger_id'] = $output_igst_ledger_name->ledger_id;
                    }
                    $output_igst_ledger_id = $this->ledger_model->getGroupLedgerId($output_igst_ary);

                     // Cess
                    $default_output_cess_id = $general_ledger['Output_Cess'];
                    $output_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cess_id);
                   
                    $output_cess_ary = array(
                                    'ledger_name' => 'Output Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cess_ledger_name)){
                        $output_cess_ledger = $output_cess_ledger_name->ledger_name;
                        $output_cess_ary['ledger_name'] = $output_cess_ledger;
                        $output_cess_ary['primary_grp'] = $output_cess_ledger_name->sub_group_1;
                        $output_cess_ary['second_grp'] = $output_cess_ledger_name->sub_group_2;
                        $output_cess_ary['main_grp'] = $output_cess_ledger_name->main_group;
                        $output_cess_ary['default_ledger_id'] = $output_cess_ledger_name->ledger_id;
                    }
                    $output_cess_ledger_id = $this->ledger_model->getGroupLedgerId($output_cess_ary);
            }


              

        }elseif($transaction_purpose == 'Tax payable'){
             if($transaction_category =='Tax paid to INCOME TAX'){ 
                $to_ledger = 'Provision for Income tax';
                $default_income_tax_id = $general_ledger['Provision_Income_tax'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Provision for Income tax',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Duties and taxes',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $to_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);
                        $to_acc = $income_tax_ledger;

                    $default_intrest_it_id = $general_ledger['Interest'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                   
                    $intrest_it_ary = array(
                                    'ledger_name' => 'Interest',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                        $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $intrest_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);


                    $default_others_expense_id = $general_ledger['Others_expense'];
                    $others_expense_ledger_name = $this->ledger_model->getDefaultLedgerId($default_others_expense_id);
                   
                    $others_expense_ary = array(
                                    'ledger_name' => 'Others',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_others_expense_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($others_expense_ledger_name)){
                        $others_expense_ledger = $others_expense_ledger_name->ledger_name;
                        $others_expense_ary['ledger_name'] = $others_expense_ledger;
                        $others_expense_ary['primary_grp'] = $others_expense_ledger_name->sub_group_1;
                        $others_expense_ary['second_grp'] = $others_expense_ledger_name->sub_group_2;
                        $others_expense_ary['main_grp'] = $others_expense_ledger_name->main_group;
                        $others_expense_ary['default_ledger_id'] = $others_expense_ledger_name->ledger_id;
                    }
                    $others_expense_id = $this->ledger_model->getGroupLedgerId($others_expense_ary);

             }else{
                $to_acc = 'GST';
            // CGST
                    $default_input_cgst_id = $general_ledger['Input_CGST'];
                    $input_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cgst_id);
                   
                    $input_cgst_ary = array(
                                    'ledger_name' => 'Input CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cgst_ledger_name)){
                        $input_cgst_ledger = $input_cgst_ledger_name->ledger_name;
                        $input_cgst_ary['ledger_name'] = $input_cgst_ledger;
                        $input_cgst_ary['primary_grp'] = $input_cgst_ledger_name->sub_group_1;
                        $input_cgst_ary['second_grp'] = $input_cgst_ledger_name->sub_group_2;
                        $input_cgst_ary['main_grp'] = $input_cgst_ledger_name->main_group;
                        $input_cgst_ary['default_ledger_id'] = $input_cgst_ledger_name->ledger_id;
                    }
                    $input_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_cgst_ary);

                     // SGST
                    $default_input_sgst_id = $general_ledger['Input_SGST'];
                    $input_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_sgst_id);
                   
                    $input_sgst_ary = array(
                                    'ledger_name' => 'Input SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_sgst_ledger_name)){
                        $input_sgst_ledger = $input_sgst_ledger_name->ledger_name;
                        $input_sgst_ary['ledger_name'] = $input_sgst_ledger;
                    $input_sgst_ary['primary_grp'] = $input_sgst_ledger_name->sub_group_1;
                        $input_sgst_ary['second_grp'] = $input_sgst_ledger_name->sub_group_2;
                        $input_sgst_ary['main_grp'] = $input_sgst_ledger_name->main_group;
                        $input_sgst_ary['default_ledger_id'] = $input_sgst_ledger_name->ledger_id;
                    }
                    $input_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_sgst_ary);

                     // UTGST
                    $default_input_utgst_id = $general_ledger['Input_UTGST'];
                    $input_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_utgst_id);
                   
                    $input_utgst_ary = array(
                                    'ledger_name' => 'Input UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_utgst_ledger_name)){
                        $input_utgst_ledger = $input_utgst_ledger_name->ledger_name;
                        $input_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $input_utgst_ary['primary_grp'] = $input_utgst_ledger_name->sub_group_1;
                        $input_utgst_ary['second_grp'] = $input_utgst_ledger_name->sub_group_2;
                        $input_utgst_ary['main_grp'] = $input_utgst_ledger_name->main_group;
                        $input_utgst_ary['default_ledger_id'] = $input_utgst_ledger_name->ledger_id;
                    }
                    $input_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_utgst_ary);

                     // IGST
                    $default_input_igst_id = $general_ledger['Input_IGST'];
                    $input_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_igst_id);
                   
                    $input_igst_ary = array(
                                    'ledger_name' => 'Input IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_igst_ledger_name)){
                        $input_igst_ledger = $input_igst_ledger_name->ledger_name;
                        $input_igst_ary['ledger_name'] = $input_igst_ledger;
                        $input_igst_ary['primary_grp'] = $input_igst_ledger_name->sub_group_1;
                        $input_igst_ary['second_grp'] = $input_igst_ledger_name->sub_group_2;
                        $input_igst_ary['main_grp'] = $input_igst_ledger_name->main_group;
                        $input_igst_ary['default_ledger_id'] = $input_igst_ledger_name->ledger_id;
                    }
                    $input_igst_ledger_id = $this->ledger_model->getGroupLedgerId($input_igst_ary);

                     // Cess
                    $default_input_cess_id = $general_ledger['Input_Cess'];
                    $input_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cess_id);
                   
                    $input_cess_ary = array(
                                    'ledger_name' => 'Input Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cess_ledger_name)){
                        $input_cess_ledger = $input_cess_ledger_name->ledger_name;
                        $input_cess_ary['ledger_name'] = $input_cess_ledger;
                        $input_cess_ary['primary_grp'] = $input_cess_ledger_name->sub_group_1;
                        $input_cess_ary['second_grp'] = $input_cess_ledger_name->sub_group_2;
                        $input_cess_ary['main_grp'] = $input_cess_ledger_name->main_group;
                        $input_cess_ary['default_ledger_id'] = $input_cess_ledger_name->ledger_id;
                    }
                    $input_cess_ledger_id = $this->ledger_model->getGroupLedgerId($input_cess_ary);
                }
        }elseif($transaction_purpose == 'Interest'){
            if($transaction_category == 'Interest Income earned'){
                $from_ledger = 'Interest Receivable';
                $default_income_tax_id = $general_ledger['Interest_Receivable_other'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Interest Receivable',
                                        'second_grp' => '',
                                        'primary_grp' => 'Other Current Assets',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $from_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);
                        $from_acc = $income_tax_ledger;

                $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;
                $deposit_type = $deposits[0]->deposit_type;
            }else{
                if($input_type == 'interest liability'){
                    $default_intrest_it_id = $general_ledger['Interest'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                    $from_ledger = 'Interest';
                    $intrest_it_ary = array(
                                    'ledger_name' => 'Interest',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                        $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $from_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);
                    $from_acc = $from_ledger_id;

                    $default_tds_payable_id = $general_ledger['Tds_payable'];
                    $tds_payable_name = $this->ledger_model->getDefaultLedgerId($default_tds_payable_id);
                    
                    $tds_payable_ary = array(
                                    'ledger_name' => 'TDS Payable',
                                    'second_grp' => '',
                                    'primary_grp' => 'TDS Payable',
                                    'main_grp' => 'Current Liabilities',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($tds_payable_name)){
                        $tds_payable_ledger = $tds_payable_name->ledger_name;
                        $tds_payable_ary['ledger_name'] = $tds_payable_ledger;
                        $tds_payable_ary['primary_grp'] = $tds_payable_name->sub_group_1;
                        $tds_payable_ary['second_grp'] = $tds_payable_name->sub_group_2;
                        $tds_payable_ary['main_grp'] = $tds_payable_name->main_group;
                        $tds_payable_ary['default_ledger_id'] = $tds_payable_name->ledger_id;
                    }
                    $tds_payable_ledger_id = $this->ledger_model->getGroupLedgerId($tds_payable_ary);
                    
                }

                $to_ledger = 'Interest Payable';
                $default_income_tax_id = $general_ledger['Interest_Payable'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Interest Payable',
                                        'second_grp' => '',
                                        'primary_grp' => 'Other Liabilities',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $to_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);                        
                        $to_acc = 'interest-' . $income_tax_ledger;
                        $deposit_type = '';
                
            }
               
            if($deposit_type == 'fixed deposit'){
                
                $to_ledger = $deposit_name = 'Fixed Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'interest-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Fixed_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($deposit_type == 'recurring deposit'){

                $to_ledger = $deposit_name = 'Recurring Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'interest-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Recurring_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($deposit_type == 'others'){

                $to_ledger = $deposit_name = $deposits[0]->others_name;
                $to_acc = 'interest-' . $deposits[0]->others_name;
                $to_ledger_id = $supplier_ledger_id =  $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Other_Deposits'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $supplier_ledger_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;
            }

        }elseif($transaction_purpose == 'Investments'){            
           
                $table = 'tbl_investments';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'investments_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;
                $to_ledger = $deposit_name = $transaction_category.'@'.$deposits[0]->investments_type;
                $to_acc = 'Investments-' . $deposits[0]->investments_type;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $default_fixed_id = $general_ledger['Investments'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Investments',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->investments_type, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

             if($voucher_type == 'RECEIPTS'){   
                $default_loss_sale_id = $general_ledger['Loss_on_sale_investment'];
                $loss_sale_name = $this->ledger_model->getDefaultLedgerId($default_loss_sale_id);
                $loss_sale_ary = array(
                                        'ledger_name' => 'Loss on sale of investment',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Preference shares invested by Shareholders',
                                        'default_ledger_id' => $default_loss_sale_id,
                                        'default_value' => 0,
                                       'amount' => 0
                                        );
            if(!empty($loss_sale_ary)){
                $loss_sale_ledger = $loss_sale_name->ledger_name;
                $loss_sale_ary['ledger_name'] = $loss_sale_ledger;
                $loss_sale_ary['primary_grp'] = $loss_sale_name->sub_group_1;
                $loss_sale_ary['second_grp'] = $loss_sale_name->sub_group_2;
                $loss_sale_ary['main_grp'] = $loss_sale_name->main_group;
                $loss_sale_ary['default_ledger_id'] = $loss_sale_name->ledger_id;
            }
            $loss_on_sale_id = $this->ledger_model->getGroupLedgerId($loss_sale_ary);

            $default_profit_sale_id = $general_ledger['Profit_on_sale_investment'];
            $profit_sale_name = $this->ledger_model->getDefaultLedgerId($default_profit_sale_id);
       
            $profit_sale_ary = array(
                        'ledger_name' => 'Profit on sale of investment',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Indirect Incomes',
                        'default_ledger_id' => $default_profit_sale_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($profit_sale_ary)){
                    $profit_sale_ledger = $profit_sale_name->ledger_name;
                    $profit_sale_ary['ledger_name'] = $profit_sale_ledger;
                    $profit_sale_ary['primary_grp'] = $profit_sale_name->sub_group_1;
                    $profit_sale_ary['second_grp'] = $profit_sale_name->sub_group_2;
                    $profit_sale_ary['main_grp'] = $profit_sale_name->main_group;
                    $profit_sale_ary['default_ledger_id'] = $profit_sale_name->ledger_id;
                }
                $profit_sale_ledger_id = $this->ledger_model->getGroupLedgerId($profit_sale_ary);
           }

        }elseif($transaction_purpose == 'Loan Borrowed and repaid'){
           
                $default_fixed_id = $general_ledger['Director'];
                if( $input_type == 'director loan'){
                  //  $payee_id = $this->input->post('cmb_partner');
                    $table = 'tbl_shareholder';
                    $where = array(
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'id' => $payee_id,
                    'sharholder_type' => 'director',
                    'delete_status' => 0);
                    $partner = $this->general_model->getRecords('*', $table, $where); 
                    
                    $to_ledger = $sharholder_name = $partner[0]->sharholder_name;              
                    $to_acc = 'director-' . $partner[0]->sharholder_name;
                    $to_ledger_id = $partner[0]->partner_ledger_id;
                    $partner_ledger_id = $partner[0]->partner_ledger_id;    
                    
                }elseif( $input_type == 'others loan'){
                    //$payee_id = $this->input->post('cmb_partner');

                    $table = 'tbl_loans';
                    $where = array(
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'loan_id' => $payee_id,
                    'loan_type' => 'others',
                    'delete_status' => 0);
                    $partner = $this->general_model->getRecords('*', $table, $where); 
                    $to_ledger = $sharholder_name = $partner[0]->others_name;              
                    $to_acc = 'others-' . $partner[0]->others_name;
                    $to_ledger_id = $partner[0]->ledger_id;
                    $partner_ledger_id = $partner[0]->ledger_id;  
                    
                }else{
                  //  $payee_id = $this->input->post('cmb_partner');                    

                    $table = 'tbl_loans';
                    $where = array(
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'loan_id' => $payee_id,
                    'loan_type' => 'bank',
                    'delete_status' => 0);
                    $partner = $this->general_model->getRecords('*', $table, $where); 
                    $to_ledger = $sharholder_name = $partner[0]->loan_bank;              
                    $to_acc = 'bank-' . $partner[0]->loan_bank;
                    $to_ledger_id = $partner[0]->ledger_id;
                    $partner_ledger_id = $partner[0]->ledger_id;  
                }
                

                if(!$to_ledger_id){                    
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
                    $supplier_ary = array(
                        'ledger_name' => $sharholder_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Loans (Liability)',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$sharholder_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $to_ledger_id;
                $to_acc = $transaction_ledger;


               if($voucher_type == 'PAYMENT'){
                $default_intrest_it_id = $general_ledger['Interest'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                   
                    $intrest_it_ary = array(
                                    'ledger_name' => 'Interest',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                        $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $intrest_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);


                    $default_others_expense_id = $general_ledger['Others_expense'];
                    $others_expense_ledger_name = $this->ledger_model->getDefaultLedgerId($default_others_expense_id);
                   
                    $others_expense_ary = array(
                                    'ledger_name' => 'Others',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_others_expense_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($others_expense_ledger_name)){
                        $others_expense_ledger = $others_expense_ledger_name->ledger_name;
                        $others_expense_ary['ledger_name'] = $others_expense_ledger;
                        $others_expense_ary['primary_grp'] = $others_expense_ledger_name->sub_group_1;
                        $others_expense_ary['second_grp'] = $others_expense_ledger_name->sub_group_2;
                        $others_expense_ary['main_grp'] = $others_expense_ledger_name->main_group;
                        $others_expense_ary['default_ledger_id'] = $others_expense_ledger_name->ledger_id;
                    }
                    $others_expense_id = $this->ledger_model->getGroupLedgerId($others_expense_ary);
               }

                    
            

        } 
        
       $voucher_date = date('Y-m-d',strtotime($this->input->post('voucher_date')));
        $general_voucher_data = array(
                "transaction_purpose_id" => $this->input->post('trans_purpose'),
               "voucher_date" => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
                "voucher_number"  => $voucher_number,
                "option_id" => $payee_id,
                "transaction_mode" => $payment_mode,
                "voucher_type" => $this->input->post('voucher_type'),
                "amount" => $this->input->post('receipt_amount'),
                "input_type" => $this->input->post('input_type'),
                "narration" => $this->input->post('description'),
                "updated_date"  => date('Y-m-d'),
                "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'), 
                "from_account" => $from_acc,
                "to_account" => $to_acc,
                "cheque_date"         => $cheque_date,
                "cheque_number"       => $this->input->post('cheque_number'),
                "payment_via"         => $payment_via,
                "ref_number"          => $reff_number,
                "igst"       => $igst_amount,
                "cgst"       => $cgst_amount,
                "sgst"       => $sgst_amount,
                "utgst"       => $utgst_amount,
                "cess"       => $cess_amount,
                "tds"       => $tds_amount,
                "interest_expense_amount" => $interest_expense_amount,
                "others_amount" => $others_amount_tax,
                "partner_shareholder_id" => $this->input->post('cmb_partner'),
                "currency_id" => 0,
                "delete_status" => 0    
                );
       
        $data_main  = array_map('trim', $general_voucher_data);
        
        $general_voucher_table = 'tbl_journal_voucher';
        $general_voucher_id = $this->general_model->insertData($general_voucher_table, $data_main);

        if($input_type == 'suppliers' ){ 
            if($voucher_type == 'PAYMENT'){            
                $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else if($voucher_type == 'RECEIPTS'){
                if($interest_expense_amount > 0 && $transaction_category == 'Advance repaid by vendor'){
                $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                $bank_account_amount = $voucher_amount - $interest_expense_amount ;
                }else{
                    $bank_account_amount = $voucher_amount;
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            } 
        }elseif($input_type == 'financial year' ){ 

            if($voucher_type == 'PAYMENT'){            
                $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else if($voucher_type == 'RECEIPTS'){

               if($interest_expense_amount > 0 && $transaction_category == 'Advance Tax Refund by Govt'){
                $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$extra_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$extra_ledger_id]["cr_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                $bank_account_amount = $voucher_amount + $interest_expense_amount ;
                }else{
                    $bank_account_amount = $voucher_amount;
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
               
            }
        }elseif($input_type == 'proprietor'){
                if($voucher_type == 'PAYMENT'){ 
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  0;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }else{
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }

        }elseif($input_type == 'shareholder' ){
                    if($transaction_category == 'Preference share issue to shareholders' || $transaction_category == 'Equity shares issued to shareholder'){
                    if($interest_expense_amount > 0){
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $bank_account_amount = $voucher_amount + $interest_expense_amount ;
                    }else{
                        $bank_account_amount = $voucher_amount;
                    }
                }
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
        }elseif($input_type == 'partner' ){
            if($voucher_type == 'PAYMENT'){  
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else if($voucher_type == 'RECEIPTS'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }
        }elseif($transaction_purpose == 'Cash transactions'){
            if($voucher_type == 'CONTRA A/C'){
                if($transaction_category == 'Cash deposited in bank'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    if($interest_expense_amount > 0){
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $cash_amount = $interest_expense_amount + $voucher_amount;
                    }else{
                        $cash_amount = $voucher_amount;
                    }

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $cash_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $cash_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

                }elseif($transaction_category == 'Cash withdrawal from bank'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }
            }elseif($voucher_type == 'RECEIPTS'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            }elseif($voucher_type == 'PAYMENT'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }
        }elseif($transaction_purpose == 'Deposits'){
            if($voucher_type == 'RECEIPTS'){
                if($interest_expense_amount > 0 ){
                    if($input_type == 'recurring' || $input_type == 'fixed'){
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] =  $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $bank_account_amount = $voucher_amount + $interest_expense_amount ;
                        
                    }else{
                         $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = 0; 
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $bank_account_amount = $voucher_amount - $interest_expense_amount ;
                        
                    }
                }else{
                   $bank_account_amount = $voucher_amount;
                }
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            }elseif($voucher_type == 'PAYMENT'){ 
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    
            }
        }elseif($transaction_purpose == 'Fixed Assset'){

            if($voucher_type == 'RECEIPTS'){
                /*if($interest_expense_amount > 0 ){                    
                         $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = 0; 
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                }*/

                if($others_amount_tax > 0){
                        $ledger_entry[$loss_on_sale_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$loss_on_sale_id]["ledger_to"] = $loss_on_sale_id;
                        $ledger_entry[$loss_on_sale_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$loss_on_sale_id]["voucher_amount"] =  $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$loss_on_sale_id]["dr_amount"] = $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["cr_amount"] =  0;
                        $ledger_entry[$loss_on_sale_id]['ledger_id'] = $loss_on_sale_id;
                    }
                    if($interest_expense_amount > 0){
                        $ledger_entry[$profit_sale_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["ledger_to"] = $profit_sale_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$profit_sale_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]['ledger_id'] = $profit_sale_ledger_id;
                    }
                

                $bank_account_amount = $voucher_amount + $interest_expense_amount + $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount - $others_amount_tax;

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$output_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["ledger_to"] = $output_sgst_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["cr_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]['ledger_id'] = $output_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$output_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["ledger_to"] = $output_igst_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["cr_amount"] =  $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]['ledger_id'] = $output_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$output_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["ledger_to"] = $output_cgst_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["cr_amount"] =  $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]['ledger_id'] = $output_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$output_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["ledger_to"] = $output_utgst_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["cr_amount"] =  $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]['ledger_id'] = $output_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$output_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["ledger_to"] = $output_cess_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["cr_amount"] =  $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]['ledger_id'] = $output_cess_ledger_id;
                   
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            }elseif($voucher_type == 'PAYMENTS'){               

                if($interest_expense_amount > 0 ){                   
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id;
                   
                }
                

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$input_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["ledger_to"] = $input_sgst_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_sgst_ledger_id]["dr_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_sgst_ledger_id]['ledger_id'] = $input_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$input_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["ledger_to"] = $input_igst_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_igst_ledger_id]["dr_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_igst_ledger_id]['ledger_id'] = $input_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$input_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["ledger_to"] = $input_cgst_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cgst_ledger_id]["dr_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cgst_ledger_id]['ledger_id'] = $input_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$input_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["ledger_to"] = $input_utgst_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_utgst_ledger_id]["dr_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_utgst_ledger_id]['ledger_id'] = $input_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$input_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["ledger_to"] = $input_cess_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cess_ledger_id]["dr_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cess_ledger_id]['ledger_id'] = $input_cess_ledger_id;
                   
                }

                    $bank_account_amount = $voucher_amount + $interest_expense_amount + $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    
            }
        }elseif($transaction_purpose == 'Tax receivables'){
             if($transaction_category =='Tax received from Income Tax'){
                $bank_account_amount = $others_amount_tax + $interest_expense_amount + $voucher_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

                    if($interest_expense_amount > 0){
                        $ledger_entry[$intrest_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["ledger_to"] = $intrest_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$intrest_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]['ledger_id'] = $intrest_ledger_id;
                    }

                    if($others_amount_tax > 0){
                        $ledger_entry[$others_income_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$others_income_id]["ledger_to"] = $others_income_id;
                        $ledger_entry[$others_income_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$others_income_id]["voucher_amount"] = $others_amount_tax;
                        $ledger_entry[$others_income_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$others_income_id]["dr_amount"] = 0;
                        $ledger_entry[$others_income_id]["cr_amount"] = $others_amount_tax;
                        $ledger_entry[$others_income_id]['ledger_id'] = $others_income_id;
                    }


            }else{
            $bank_account_amount = $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount;

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$output_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["ledger_to"] = $output_sgst_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["cr_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]['ledger_id'] = $output_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$output_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["ledger_to"] = $output_igst_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["cr_amount"] =  $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]['ledger_id'] = $output_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$output_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["ledger_to"] = $output_cgst_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["cr_amount"] =  $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]['ledger_id'] = $output_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$output_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["ledger_to"] = $output_utgst_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["cr_amount"] =  $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]['ledger_id'] = $output_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$output_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["ledger_to"] = $output_cess_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["cr_amount"] =  $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]['ledger_id'] = $output_cess_ledger_id;
                   
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;
            }

        }elseif($transaction_purpose == 'Tax payable'){
            if($transaction_category =='Tax paid to INCOME TAX'){
                $bank_account_amount = $others_amount_tax + $interest_expense_amount + $voucher_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                if($interest_expense_amount > 0){
                   $ledger_entry[$intrest_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$intrest_ledger_id]["ledger_to"] = $intrest_ledger_id;
                    $ledger_entry[$intrest_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$intrest_ledger_id]["voucher_amount"] = $interest_expense_amount;
                    $ledger_entry[$intrest_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$intrest_ledger_id]["dr_amount"] = $interest_expense_amount;
                    $ledger_entry[$intrest_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$intrest_ledger_id]['ledger_id'] = $intrest_ledger_id; 
                }
                    if($others_amount_tax > 0){
                    $ledger_entry[$others_expense_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$others_expense_id]["ledger_to"] = $others_expense_id;
                    $ledger_entry[$others_expense_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$others_expense_id]["voucher_amount"] = $others_amount_tax;
                    $ledger_entry[$others_expense_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$others_expense_id]["dr_amount"] = $others_amount_tax;
                    $ledger_entry[$others_expense_id]["cr_amount"] = 0;
                    $ledger_entry[$others_expense_id]['ledger_id'] = $others_expense_id; 
                    }

            }else{

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$input_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["ledger_to"] = $input_sgst_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_sgst_ledger_id]["dr_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_sgst_ledger_id]['ledger_id'] = $input_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$input_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["ledger_to"] = $input_igst_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_igst_ledger_id]["dr_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_igst_ledger_id]['ledger_id'] = $input_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$input_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["ledger_to"] = $input_cgst_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cgst_ledger_id]["dr_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cgst_ledger_id]['ledger_id'] = $input_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$input_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["ledger_to"] = $input_utgst_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_utgst_ledger_id]["dr_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_utgst_ledger_id]['ledger_id'] = $input_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$input_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["ledger_to"] = $input_cess_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cess_ledger_id]["dr_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cess_ledger_id]['ledger_id'] = $input_cess_ledger_id;
                   
                }

                    $bank_account_amount =  $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;
                }
        }elseif($transaction_purpose == 'Interest'){  
               if($transaction_category == 'Interest Income earned'){             
                   $voucher_amount = $voucher_amount - $tds_amount; 
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $tds_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $tds_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }else{
                    if($input_type == 'interest liability'){
                        $interest_amount = $voucher_amount + $tds_amount; 
                        $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$from_ledger_id]["voucher_amount"] =  $interest_amount;
                        $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$from_ledger_id]["dr_amount"] = $interest_amount;
                        $ledger_entry[$from_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                        $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                        if($tds_amount > 0){
                            $ledger_entry[$tds_payable_ledger_id]["ledger_from"] = $from_ledger_id;
                            $ledger_entry[$tds_payable_ledger_id]["ledger_to"] = $tds_payable_ledger_id;
                            $ledger_entry[$tds_payable_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                            $ledger_entry[$tds_payable_ledger_id]["voucher_amount"] = $tds_amount;
                            $ledger_entry[$tds_payable_ledger_id]["converted_voucher_amount"] = 0;
                            $ledger_entry[$tds_payable_ledger_id]["dr_amount"] = 0;
                            $ledger_entry[$tds_payable_ledger_id]["cr_amount"] = $tds_amount;
                            $ledger_entry[$tds_payable_ledger_id]['ledger_id'] = $tds_payable_ledger_id;
                        }
                        
                    }else{
                        $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$from_ledger_id]["voucher_amount"] =  $voucher_amount;
                        $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                        $ledger_entry[$from_ledger_id]["cr_amount"] =  $voucher_amount;
                        $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                        $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                        $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    }
                    
                }



        }elseif($transaction_purpose == 'Investments'){
            if($voucher_type == 'PAYMENTS'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else{
                    $bank_account_amount =  $voucher_amount - $others_amount_tax + $interest_expense_amount;
                    // Amount + profit - loss
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    if($others_amount_tax > 0){
                        $ledger_entry[$loss_on_sale_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$loss_on_sale_id]["ledger_to"] = $loss_on_sale_id;
                        $ledger_entry[$loss_on_sale_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$loss_on_sale_id]["voucher_amount"] =  $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$loss_on_sale_id]["dr_amount"] = $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["cr_amount"] =  0;
                        $ledger_entry[$loss_on_sale_id]['ledger_id'] = $loss_on_sale_id;
                    }
                    if($interest_expense_amount > 0){
                        $ledger_entry[$profit_sale_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["ledger_to"] = $profit_sale_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$profit_sale_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]['ledger_id'] = $profit_sale_ledger_id;
                    }
            }
                    
        }elseif($transaction_purpose == 'Loan Borrowed and repaid'){
            if($voucher_type == 'RECEIPTS'){
                $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else{
               

                 $bank_account_amount = $others_amount_tax + $interest_expense_amount + $voucher_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    if($interest_expense_amount > 0){
                        $ledger_entry[$intrest_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["ledger_to"] = $intrest_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$intrest_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]["cr_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]['ledger_id'] = $intrest_ledger_id; 
                    }
                    if($others_amount_tax > 0){
                        $ledger_entry[$others_expense_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$others_expense_id]["ledger_to"] = $others_expense_id;
                        $ledger_entry[$others_expense_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$others_expense_id]["voucher_amount"] = $others_amount_tax;
                        $ledger_entry[$others_expense_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$others_expense_id]["dr_amount"] = $others_amount_tax;
                        $ledger_entry[$others_expense_id]["cr_amount"] = 0;
                        $ledger_entry[$others_expense_id]['ledger_id'] = $others_expense_id; 
                    }
            }
        }
        
         $this->db->insert_batch('accounts_journal_voucher', $ledger_entry);
         
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        foreach ($ledger_entry as $key => $value) {
            $data_bunch = array();
            $data_bunch['ledger_id'] = $value['ledger_id'];
            $data_bunch['voucher_amount'] = $value['voucher_amount'];

                if ($value['dr_amount'] > 0) {
                    $data_bunch['amount_type'] = 'DR';
                } else {
                    $data_bunch['amount_type'] = 'CR';
            }

            $data_bunch['branch_id'] = $branch_id;
           
            $this->general_model->addBunchVoucher($data_bunch, $voucher_date);
        }

       redirect("general_voucher/general_voucher_list", 'refresh');

    }


    public function edit_general_voucher(){    
        $journal_voucher_module_id         = $this->config->item('journal_voucher_module');
        $data['module_id']                 = $journal_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "edit_privilege";
        $data['privilege']                 = "edit_privilege";
        $section_modules                   = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);

       if ($this->input->post('payment_mode') == "other payment mode") {
            $payment_via = $this->input->post('payment_via');
            $reff_number = $this->input->post('ref_number');
        } else {
            $payment_via = "";
            $reff_number = "";
        } 
        $general_ledger = $this->config->item('general_ledger');
         $input_type = $this->input->post('input_type');
        if($input_type != 'interest fixed' && $input_type != 'interest recurring' && $input_type != 'interest other' &&  $input_type != 'interest liability'){
        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode          = $bank_acc_payment_mode[0];
            $from_acc              = $bank_acc_payment_mode[1];
           
            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array('bank_account_id' => $payment_mode));
            $from_ledger_id =  $ledger_bank_acc[0]->ledger_id;
            $ledger_from = $ledger_bank_acc[0]->ledger_id;

        } else {
            $payment_mode     = $this->input->post('payment_mode');
            $from_acc         = $this->input->post('payment_mode');
            /*$ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));*/

            if ($from_acc != '') {
                $default_payment_id = $general_ledger['Other_Payment'];
                if (strtolower($payment_mode) == "cash"){
                    $default_payment_id = $general_ledger['Cash_Payment'];
                }
                $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
                $default_payment_ary = array(
                                'ledger_name' => $from_acc,
                                'second_grp' => '',
                                'primary_grp' => 'Cash & Cash Equivalent',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_payment_id,
                                'amount' => 0
                            );
                if(!empty($default_payment_name)){
                    $default_led_nm = $default_payment_name->ledger_name;
                    $default_payment_ary['ledger_name'] = str_ireplace('{{PAYMENT_MODE}}',$from_acc, $default_led_nm);
                    $default_payment_ary['primary_grp'] = $default_payment_name->sub_group_1;
                    $default_payment_ary['second_grp'] = $default_payment_name->sub_group_2;
                    $default_payment_ary['main_grp'] = $default_payment_name->main_group;
                    $default_payment_ary['default_ledger_id'] = $default_payment_name->ledger_id;
                }
                $from_ledger_id = $this->ledger_model->getGroupLedgerId($default_payment_ary);
            }
        }
    }else{
        $payment_mode = 'none';
    }

        $cheque_date = $this->input->post('cheque_date');
        $transaction_purpose = $this->input->post('transaction_purpose');
        if (!$cheque_date) {
            $cheque_date = null;
        }else{
            $cheque_date = date('Y-m-d', strtotime($this->input->post('cheque_date')));
        }
        
        $primary_id      = "journal_voucher_id";
        $table_name      = 'tbl_journal_voucher';
        $date_field_name = "voucher_date";
        $current_date    =date('Y-m-d',strtotime($this->input->post('voucher_date')));

        $voucher_number  = $this->input->post('voucher_number');          
        $voucher_type = $this->input->post('voucher_type'); 
        
        $transaction_purpose_id = $this->input->post('trans_purpose');
        $voucher_amount = $this->input->post('receipt_amount');
        $interest_expense_amount = ($this->input->post('interest_amount'))?$this->input->post('interest_amount'):0;

       $others_amount_tax = ($this->input->post('others_amount'))?$this->input->post('others_amount'):0;
        $transaction_details = $this->get_transaction_purpose_det($transaction_purpose_id);
        $transaction_ledger = $transaction_details[0]->purpose_option;
        $transaction_category = $transaction_details[0]->transaction_category;
        $payee_id = $transaction_details[0]->payee_id;

        $sgst_amount = ($this->input->post('txt_sgst'))?$this->input->post('txt_sgst'):0;
        $cgst_amount = ($this->input->post('txt_cgst'))?$this->input->post('txt_cgst'):0;
        $utgst_amount = ($this->input->post('txt_utgst'))?$this->input->post('txt_utgst'):0;
        $igst_amount = ($this->input->post('txt_igst'))?$this->input->post('txt_igst'):0;
        $cess_amount = ($this->input->post('txt_cess'))?$this->input->post('txt_cess'):0;
        $tds_amount = ($this->input->post('txt_tds'))?$this->input->post('txt_tds'):0;


        if($transaction_purpose == 'Advances'){
            if($input_type == 'suppliers'){
                $table = 'supplier';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'supplier_id' => $payee_id,
                'delete_status' => 0);
                $suppliers = $this->general_model->getRecords('*', $table, $where); 
                $to_ledger = $supplier_name = $suppliers[0]->supplier_name;              
                $to_acc = 'supplier-' . $suppliers[0]->supplier_name;
                $to_ledger_id = $suppliers[0]->ledger_id;

                $supplier_ledger_id = $suppliers[0]->ledger_id;

                if(!$supplier_ledger_id){
                    $supplier_ledger_id = $general_ledger['SUPPLIER'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                    'ledger_name' => $supplier_name,
                                    'second_grp' => '',
                                    'primary_grp' => 'Sundry Creditors',
                                    'main_grp' => 'Current Assets',
                                    'default_ledger_id' => $supplier_ledger_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$supplier_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;

                if($transaction_category == 'Advance repaid by vendor'){
                    $default_other_id = $general_ledger['Other_Charges'];
                    $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Other Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);
                    
                }
            }elseif($input_type == 'financial year'){ 
                $table = 'tbl_financial_year';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'year_id' => $payee_id);


                $financial_year = $this->general_model->getRecords('*', $table, $where); 
                $from_date = $financial_year[0]->from_date;
                $to_date = $financial_year[0]->to_date;
                $finance_year = date('Y',strtotime($from_date)) .' - '.date('y',strtotime($to_date));
                $to_ledger = $transaction_ledger;
                $to_acc = 'finance year-' . $finance_year;

                if ($transaction_ledger != '') {
                    $ledger = str_replace(' ', '_', $transaction_category);
                    $finance_ledger_id = $general_ledger[$ledger];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($finance_ledger_id);
                        
                    $supplier_ary = array(
                                    'ledger_name' => $transaction_ledger,
                                    'second_grp' => '',
                                    'primary_grp' => 'Advances',
                                    'main_grp' => 'Current Assets',
                                    'default_ledger_id' => $finance_ledger_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$finance_year, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                if( $transaction_category == 'Advance Tax Refund by Govt'){

                    $default_intrest_id = $general_ledger['Interest_Income'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Interest Income',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Incomes',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }
            }
        }else if($transaction_purpose == 'Capital Invested'){
           if($input_type == 'shareholder'){
                if($transaction_category == 'Preference share issue to shareholders'){
                    $to_ledger = 'Preference Share Capital A/c';
                    $default_capital_id = $general_ledger['Preference_Share_Capital_AC'];
                    $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                     $capital_ary = array(
                                            'ledger_name' => 'Preference Share Capital A/c',
                                            'second_grp' => '',
                                            'primary_grp' => '',
                                            'main_grp' => 'Capital',
                                            'default_ledger_id' => $default_capital_id,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($capital_ary)){
                                $capital_ledger = $capital_ac_name->ledger_name;
                                $capital_ary['ledger_name'] = $capital_ledger;
                                $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                                $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                                $capital_ary['main_grp'] = $capital_ac_name->main_group;
                                $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                            }
                            $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                            $to_acc = $transaction_ledger;

                            
                    $default_intrest_id = $general_ledger['Security_Premium'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Security Premium',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }

                if($transaction_category == 'Equity shares issued to shareholder'){
                    $to_ledger = 'Equity Share Capital A/c';
                    $default_capital_id = $general_ledger['Equity_Share_Capital_AC'];          
                    $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                     $capital_ary = array(
                                            'ledger_name' => 'Equity Share Capital A/c',
                                            'second_grp' => '',
                                            'primary_grp' => '',
                                            'main_grp' => 'Capital',
                                            'default_ledger_id' => $default_capital_id,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($capital_ary)){
                                $capital_ledger = $capital_ac_name->ledger_name;
                                $capital_ary['ledger_name'] = $capital_ledger;
                                $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                                $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                                $capital_ary['main_grp'] = $capital_ac_name->main_group;
                                $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                            }
                            $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                            $to_acc = $transaction_ledger;

                    $default_intrest_id = $general_ledger['Security_Premium'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Security Premium',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }
           }elseif($input_type == 'partner'){
                $default_capital_id = $general_ledger['Partner'];  
                $payee_id = $this->input->post('cmb_partner');
                $table = 'tbl_shareholder';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'id' => $payee_id,
                'delete_status' => 0);
                $partner = $this->general_model->getRecords('*', $table, $where); 
                $to_ledger = $sharholder_name = $partner[0]->sharholder_name;              
                $to_acc = 'partner-' . $partner[0]->sharholder_name;
                $to_ledger_id = $partner[0]->partner_ledger_id;
                $partner_ledger_id = $partner[0]->partner_ledger_id;        
           
             if(!$partner_ledger_id){
                    $defult_ledger_id = $general_ledger['Partner'];
                    $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($partner_ledger_id);
                        
                    $supplier_ary = array(
                                    'ledger_name' => $sharholder_name,
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $defult_ledger_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                        if(!empty($partner_ledger_name)){
                            $supplier_ledger = $partner_ledger_name->ledger_name;
                           
                            $supplier_ledger = str_ireplace('{{X}}',$sharholder_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $partner_ledger_name;
                            $supplier_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $partner_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                        }
                    $partner_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $partner_ledger_id;

           }else{
            if($transaction_category == 'Capital withdrawn by Proprietor'){
                $to_ledger = 'Drawings A/C';
                $default_capital_id = $general_ledger['Drawing_AC'];          
                $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                $capital_ary = array(
                                    'ledger_name' => 'Drawings A/C',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_capital_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($capital_ary)){
                        $capital_ledger = $capital_ac_name->ledger_name;
                        $capital_ary['ledger_name'] = $capital_ledger;
                        $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                        $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                        $capital_ary['main_grp'] = $capital_ac_name->main_group;
                        $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                    $to_acc = $transaction_ledger;

             }else{
                $to_ledger = 'Capital A/c';
                $default_capital_id = $general_ledger['Capital_AC'];          
                $capital_ac_name = $this->ledger_model->getDefaultLedgerId($default_capital_id);
                $capital_ary = array(
                                    'ledger_name' => 'Capital A/c',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Capital',
                                    'default_ledger_id' => $default_capital_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($capital_ary)){
                        $capital_ledger = $capital_ac_name->ledger_name;
                        $capital_ary['ledger_name'] = $capital_ledger;
                        $capital_ary['primary_grp'] = $capital_ac_name->sub_group_1;
                        $capital_ary['second_grp'] = $capital_ac_name->sub_group_2;
                        $capital_ary['main_grp'] = $capital_ac_name->main_group;
                        $capital_ary['default_ledger_id'] = $capital_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($capital_ary);
                    $to_acc = $transaction_ledger;
             }
            
            }
        }elseif($transaction_purpose == 'Cash transactions'){
           if($voucher_type == 'CONTRA A/C'){
                $to_ledger = 'Cash A/c';
                $default_cash_ac_id = $general_ledger['Cash_AC'];          
                $cash_ac_name = $this->ledger_model->getDefaultLedgerId($default_cash_ac_id);
                 $cash_ac_ary = array(
                                        'ledger_name' => 'Cash A/c',
                                        'second_grp' => '',
                                        'primary_grp' => 'Cash',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_cash_ac_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($cash_ac_ary)){
                        $cash_ac_ledger = $cash_ac_name->ledger_name;
                        $cash_ac_ary['ledger_name'] = $cash_ac_ledger;
                        $cash_ac_ary['primary_grp'] = $cash_ac_name->sub_group_1;
                        $cash_ac_ary['second_grp'] = $cash_ac_name->sub_group_2;
                        $cash_ac_ary['main_grp'] = $cash_ac_name->main_group;
                        $cash_ac_ary['default_ledger_id'] = $cash_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($cash_ac_ary);
                    $to_acc = $transaction_ledger;
                    if($transaction_category =='Cash deposited in bank'){
                    $default_other_id = $general_ledger['Bank_Charges'];
                    $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Bank Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);
                }
           }else{
                $to_ledger = 'Suspense A/c';
                $default_supense_ac_id = $general_ledger['Suspense_AC'];          
                $suspense_ac_name = $this->ledger_model->getDefaultLedgerId($default_supense_ac_id);
                 $suspense_ac_ary = array(
                                        'ledger_name' => 'Suspense A/c',
                                        'second_grp' => '',
                                        'primary_grp' => 'NA',
                                        'main_grp' => 'Suspense',
                                        'default_ledger_id' => $default_supense_ac_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($suspense_ac_ary)){
                        $suspense_ledger = $suspense_ac_name->ledger_name;
                        $suspense_ac_ary['ledger_name'] = $suspense_ledger;
                        $suspense_ac_ary['primary_grp'] = $suspense_ac_name->sub_group_1;
                        $suspense_ac_ary['second_grp'] = $suspense_ac_name->sub_group_2;
                        $suspense_ac_ary['main_grp'] = $suspense_ac_name->main_group;
                        $suspense_ac_ary['default_ledger_id'] = $suspense_ac_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($suspense_ac_ary);
                    $to_acc = $transaction_ledger;
           } 
        }elseif($transaction_purpose == 'Deposits'){
                
            if($input_type == 'fixed'){
                $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;
                
                $to_ledger = $deposit_name = 'Fixed Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'deposit-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Fixed_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($input_type == 'recurring'){
                $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;

                $to_ledger = $deposit_name = 'Recurring Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'deposit-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Recurring_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($input_type == 'rent'){
                $to_ledger = 'Rent Deposit';
                $default_rent_dep_id = $general_ledger['Rent_Deposit'];          
                $rent_dep_name = $this->ledger_model->getDefaultLedgerId($default_rent_dep_id);
                 $rent_dep_ary = array(
                                        'ledger_name' => 'Rent Deposit',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_rent_dep_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($rent_dep_ary)){
                        $rent_dep_ledger = $rent_dep_name->ledger_name;
                        $rent_dep_ary['ledger_name'] = $rent_dep_ledger;
                        $rent_dep_ary['primary_grp'] = $rent_dep_name->sub_group_1;
                        $rent_dep_ary['second_grp'] = $rent_dep_name->sub_group_2;
                        $rent_dep_ary['main_grp'] = $rent_dep_name->main_group;
                        $rent_dep_ary['default_ledger_id'] = $rent_dep_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($rent_dep_ary);
                    $to_acc = $transaction_ledger;

            }elseif($input_type == 'electricity'){
                $to_ledger = 'Electricity Deposit';
                $default_electricity_id = $general_ledger['Electricity_Deposit'];          
                $electricity_name = $this->ledger_model->getDefaultLedgerId($default_electricity_id);
                 $electricity_ary = array(
                                        'ledger_name' => 'Electricity Deposit',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_electricity_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($electricity_ary)){
                        $suspense_ledger = $electricity_name->ledger_name;
                        $electricity_ary['ledger_name'] = $suspense_ledger;
                        $electricity_ary['primary_grp'] = $electricity_name->sub_group_1;
                        $electricity_ary['second_grp'] = $electricity_name->sub_group_2;
                        $electricity_ary['main_grp'] = $electricity_name->main_group;
                        $electricity_ary['default_ledger_id'] = $electricity_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($electricity_ary);
                    $to_acc = $transaction_ledger;
                
            }elseif($input_type == 'water'){
                $to_ledger = 'Water Deposit';
                $default_water_dep_id = $general_ledger['Water_Deposit'];          
                $water_dep_name = $this->ledger_model->getDefaultLedgerId($default_water_dep_id);
                 $water_dep_ary = array(
                                        'ledger_name' => 'Water Deposit',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_water_dep_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                    if(!empty($water_dep_ary)){
                        $water_dep_ledger = $water_dep_name->ledger_name;
                        $water_dep_ary['ledger_name'] = $water_dep_ledger;
                        $water_dep_ary['primary_grp'] = $water_dep_name->sub_group_1;
                        $water_dep_ary['second_grp'] = $water_dep_name->sub_group_2;
                        $water_dep_ary['main_grp'] = $water_dep_name->main_group;
                        $water_dep_ary['default_ledger_id'] = $water_dep_name->ledger_id;
                    }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($water_dep_ary);
                    $to_acc = $transaction_ledger;
                
            }elseif($input_type == 'other'){
                $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;

                $to_ledger = $deposit_name = $deposits[0]->others_name;
                $to_acc = 'deposit-' . $deposits[0]->others_name;
                $to_ledger_id = $supplier_ledger_id =  $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Other_Deposits'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;
            }
            if($voucher_type == 'RECEIPTS'){
            if($input_type == 'fixed' || $input_type == 'recurring'){
                    $default_intrest_id = $general_ledger['Interest_Receivable'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Interest Receivable',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Other Income',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);
                }else{
                $default_other_id = $general_ledger['Other_Charges'];
                $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Other Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);
                    
                }
            }

        }elseif($transaction_purpose == 'Fixed Assset'){

                $table = 'tbl_fixed_assets';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'fixed_assets_id' => $payee_id,
                'delete_status' => 0);
                $deposits = $this->general_model->getRecords('*', $table, $where);
                $supplier_ledger_id = $deposits[0]->ledger_id;
                $to_ledger = $deposit_name = $deposits[0]->name_of_assets_purchase;
                $to_acc = 'Fixed Assets -' . $deposits[0]->name_of_assets_purchase;
                $assets_type = $deposits[0]->particulars;
                
                 if(!$supplier_ledger_id){
                    $supplier_ledger_id = $general_ledger['Fixed_Assets'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => $assets_type,
                                'main_grp' => 'Fixed Assets',
                                'default_ledger_id' => $supplier_ledger_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            $sub_group = $supplier_ledger_name->sub_group_1;
                            $supplier_ledger = str_ireplace('{{X}}',$deposit_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = str_ireplace('{{X}}',$assets_type, $sub_group);
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;
                //$voucher_type == 'RECEIPTS'
                 if($voucher_type == 'PAYMENTS'){
                    $default_other_id = $general_ledger['Other_Charges'];
                    $other_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_id);
                   
                    $other_ary = array(
                                    'ledger_name' => 'Other Charges',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_other_id,
                                    'default_value' => 0,
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
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($other_ary);

                    // CGST
                    $default_input_cgst_id = $general_ledger['Input_CGST'];
                    $input_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cgst_id);
                   
                    $input_cgst_ary = array(
                                    'ledger_name' => 'Input CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cgst_ledger_name)){
                        $input_cgst_ledger = $input_cgst_ledger_name->ledger_name;
                        $input_cgst_ary['ledger_name'] = $input_cgst_ledger;
                        $input_cgst_ary['primary_grp'] = $input_cgst_ledger_name->sub_group_1;
                        $input_cgst_ary['second_grp'] = $input_cgst_ledger_name->sub_group_2;
                        $input_cgst_ary['main_grp'] = $input_cgst_ledger_name->main_group;
                        $input_cgst_ary['default_ledger_id'] = $input_cgst_ledger_name->ledger_id;
                    }
                    $input_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_cgst_ary);

                     // SGST
                    $default_input_sgst_id = $general_ledger['Input_SGST'];
                    $input_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_sgst_id);
                   
                    $input_sgst_ary = array(
                                    'ledger_name' => 'Input SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_sgst_ledger_name)){
                        $input_sgst_ledger = $input_sgst_ledger_name->ledger_name;
                        $input_sgst_ary['ledger_name'] = $input_sgst_ledger;
                    $input_sgst_ary['primary_grp'] = $input_sgst_ledger_name->sub_group_1;
                        $input_sgst_ary['second_grp'] = $input_sgst_ledger_name->sub_group_2;
                        $input_sgst_ary['main_grp'] = $input_sgst_ledger_name->main_group;
                        $input_sgst_ary['default_ledger_id'] = $input_sgst_ledger_name->ledger_id;
                    }
                    $input_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_sgst_ary);

                     // UTGST
                    $default_input_utgst_id = $general_ledger['Input_UTGST'];
                    $input_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_utgst_id);
                   
                    $input_utgst_ary = array(
                                    'ledger_name' => 'Input UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_utgst_ledger_name)){
                        $input_utgst_ledger = $input_utgst_ledger_name->ledger_name;
                        $input_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $input_utgst_ary['primary_grp'] = $input_utgst_ledger_name->sub_group_1;
                        $input_utgst_ary['second_grp'] = $input_utgst_ledger_name->sub_group_2;
                        $input_utgst_ary['main_grp'] = $input_utgst_ledger_name->main_group;
                        $input_utgst_ary['default_ledger_id'] = $input_utgst_ledger_name->ledger_id;
                    }
                    $input_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_utgst_ary);

                     // IGST
                    $default_input_igst_id = $general_ledger['Input_IGST'];
                    $input_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_igst_id);
                   
                    $input_igst_ary = array(
                                    'ledger_name' => 'Input IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_igst_ledger_name)){
                        $input_igst_ledger = $input_igst_ledger_name->ledger_name;
                        $input_igst_ary['ledger_name'] = $input_igst_ledger;
                        $input_igst_ary['primary_grp'] = $input_igst_ledger_name->sub_group_1;
                        $input_igst_ary['second_grp'] = $input_igst_ledger_name->sub_group_2;
                        $input_igst_ary['main_grp'] = $input_igst_ledger_name->main_group;
                        $input_igst_ary['default_ledger_id'] = $input_igst_ledger_name->ledger_id;
                    }
                    $input_igst_ledger_id = $this->ledger_model->getGroupLedgerId($input_igst_ary);

                     // Cess
                    $default_input_cess_id = $general_ledger['Input_Cess'];
                    $input_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cess_id);
                   
                    $input_cess_ary = array(
                                    'ledger_name' => 'Input Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cess_ledger_name)){
                        $input_cess_ledger = $input_cess_ledger_name->ledger_name;
                        $input_cess_ary['ledger_name'] = $input_cess_ledger;
                        $input_cess_ary['primary_grp'] = $input_cess_ledger_name->sub_group_1;
                        $input_cess_ary['second_grp'] = $input_cess_ledger_name->sub_group_2;
                        $input_cess_ary['main_grp'] = $input_cess_ledger_name->main_group;
                        $input_cess_ary['default_ledger_id'] = $input_cess_ledger_name->ledger_id;
                    }
                    $input_cess_ledger_id = $this->ledger_model->getGroupLedgerId($input_cess_ary);
                    
                }else{

                    /*$default_intrest_id = $general_ledger['Other_Incomes'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_id);
                   
                    $intrest_ary = array(
                                    'ledger_name' => 'Other Incomes',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Incomes',
                                    'default_ledger_id' => $default_intrest_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_ary['ledger_name'] = $intrest_ledger;
                        $intrest_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $extra_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_ary);*/

                $default_loss_sale_id = $general_ledger['Loss_on_sale_investment'];
                $loss_sale_name = $this->ledger_model->getDefaultLedgerId($default_loss_sale_id);
                $loss_sale_ary = array(
                                        'ledger_name' => 'Loss on sale of investment',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Preference shares invested by Shareholders',
                                        'default_ledger_id' => $default_loss_sale_id,
                                        'default_value' => 0,
                                       'amount' => 0
                                        );
            if(!empty($loss_sale_ary)){
                $loss_sale_ledger = $loss_sale_name->ledger_name;
                $loss_sale_ary['ledger_name'] = $loss_sale_ledger;
                $loss_sale_ary['primary_grp'] = $loss_sale_name->sub_group_1;
                $loss_sale_ary['second_grp'] = $loss_sale_name->sub_group_2;
                $loss_sale_ary['main_grp'] = $loss_sale_name->main_group;
                $loss_sale_ary['default_ledger_id'] = $loss_sale_name->ledger_id;
            }
            $loss_on_sale_id = $this->ledger_model->getGroupLedgerId($loss_sale_ary);

            $default_profit_sale_id = $general_ledger['Profit_on_sale_investment'];
            $profit_sale_name = $this->ledger_model->getDefaultLedgerId($default_profit_sale_id);
       
            $profit_sale_ary = array(
                        'ledger_name' => 'Profit on sale of investment',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Indirect Incomes',
                        'default_ledger_id' => $default_profit_sale_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
            if(!empty($profit_sale_ary)){
                    $profit_sale_ledger = $profit_sale_name->ledger_name;
                    $profit_sale_ary['ledger_name'] = $profit_sale_ledger;
                    $profit_sale_ary['primary_grp'] = $profit_sale_name->sub_group_1;
                    $profit_sale_ary['second_grp'] = $profit_sale_name->sub_group_2;
                    $profit_sale_ary['main_grp'] = $profit_sale_name->main_group;
                    $profit_sale_ary['default_ledger_id'] = $profit_sale_name->ledger_id;
            }
                $profit_sale_ledger_id = $this->ledger_model->getGroupLedgerId($profit_sale_ary);

                    // CGST
                    $default_output_cgst_id = $general_ledger['Output_CGST'];
                    $output_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cgst_id);
                   
                    $output_cgst_ary = array(
                                    'ledger_name' => 'Output CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cgst_ledger_name)){
                        $output_cgst_ledger = $output_cgst_ledger_name->ledger_name;
                        $output_cgst_ary['ledger_name'] = $output_cgst_ledger;
                        $output_cgst_ary['primary_grp'] = $output_cgst_ledger_name->sub_group_1;
                        $output_cgst_ary['second_grp'] = $output_cgst_ledger_name->sub_group_2;
                        $output_cgst_ary['main_grp'] = $output_cgst_ledger_name->main_group;
                        $output_cgst_ary['default_ledger_id'] = $output_cgst_ledger_name->ledger_id;
                    }
                    $output_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_cgst_ary);

                     // SGST
                    $default_output_sgst_id = $general_ledger['Output_SGST'];
                    $output_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_sgst_id);
                   
                    $output_sgst_ary = array(
                                    'ledger_name' => 'Output SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_sgst_ledger_name)){
                        $output_sgst_ledger = $output_sgst_ledger_name->ledger_name;
                        $output_sgst_ary['ledger_name'] = $output_sgst_ledger;
                    $output_sgst_ary['primary_grp'] = $output_sgst_ledger_name->sub_group_1;
                        $output_sgst_ary['second_grp'] = $output_sgst_ledger_name->sub_group_2;
                        $output_sgst_ary['main_grp'] = $output_sgst_ledger_name->main_group;
                        $output_sgst_ary['default_ledger_id'] = $output_sgst_ledger_name->ledger_id;
                    }
                    $output_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_sgst_ary);

                     // UTGST
                    $default_output_utgst_id = $general_ledger['Output_UTGST'];
                    $output_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_utgst_id);
                   
                    $output_utgst_ary = array(
                                    'ledger_name' => 'Output UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_utgst_ledger_name)){
                        $input_utgst_ledger = $output_utgst_ledger_name->ledger_name;
                        $output_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $output_utgst_ary['primary_grp'] = $output_utgst_ledger_name->sub_group_1;
                        $output_utgst_ary['second_grp'] = $output_utgst_ledger_name->sub_group_2;
                        $output_utgst_ary['main_grp'] = $output_utgst_ledger_name->main_group;
                        $output_utgst_ary['default_ledger_id'] = $output_utgst_ledger_name->ledger_id;
                    }
                    $output_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_utgst_ary);

                     // IGST
                    $default_output_igst_id = $general_ledger['Output_IGST'];
                    $output_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_igst_id);
                   
                    $output_igst_ary = array(
                                    'ledger_name' => 'Output IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_igst_ledger_name)){
                        $output_igst_ledger = $output_igst_ledger_name->ledger_name;
                        $output_igst_ary['ledger_name'] = $output_igst_ledger;
                        $output_igst_ary['primary_grp'] = $output_igst_ledger_name->sub_group_1;
                        $output_igst_ary['second_grp'] = $output_igst_ledger_name->sub_group_2;
                        $output_igst_ary['main_grp'] = $output_igst_ledger_name->main_group;
                        $output_igst_ary['default_ledger_id'] = $output_igst_ledger_name->ledger_id;
                    }
                    $output_igst_ledger_id = $this->ledger_model->getGroupLedgerId($output_igst_ary);

                     // Cess
                    $default_output_cess_id = $general_ledger['Output_Cess'];
                    $output_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cess_id);
                   
                    $output_cess_ary = array(
                                    'ledger_name' => 'Output Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cess_ledger_name)){
                        $output_cess_ledger = $output_cess_ledger_name->ledger_name;
                        $output_cess_ary['ledger_name'] = $output_cess_ledger;
                        $output_cess_ary['primary_grp'] = $output_cess_ledger_name->sub_group_1;
                        $output_cess_ary['second_grp'] = $output_cess_ledger_name->sub_group_2;
                        $output_cess_ary['main_grp'] = $output_cess_ledger_name->main_group;
                        $output_cess_ary['default_ledger_id'] = $output_cess_ledger_name->ledger_id;
                    }
                    $output_cess_ledger_id = $this->ledger_model->getGroupLedgerId($output_cess_ary);
                }
        }elseif($transaction_purpose == 'Tax receivables'){
            if($transaction_category =='Tax received from Income Tax'){
                $to_ledger = 'Income tax refund';
                $default_income_tax_id = $general_ledger['Income_tax_refund'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Income tax refund',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $to_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);
                        $to_acc = $income_tax_ledger;


                    $default_intrest_it_id = $general_ledger['Interest_on_Income_tax'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                   
                    $intrest_it_ary = array(
                                    'ledger_name' => 'Interest on Income tax refund',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Income',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                        $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $intrest_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);

                    $default_others_income_id = $general_ledger['Others_income'];
                    $others_income_ledger_name = $this->ledger_model->getDefaultLedgerId($default_others_income_id);
                   
                    $others_income_ary = array(
                                    'ledger_name' => 'Others',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Income',
                                    'default_ledger_id' => $default_others_income_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($others_income_ledger_name)){
                        $others_income_ledger = $others_income_ledger_name->ledger_name;
                        $others_income_ary['ledger_name'] = $others_income_ledger;
                        $others_income_ary['primary_grp'] = $others_income_ledger_name->sub_group_1;
                        $others_income_ary['second_grp'] = $others_income_ledger_name->sub_group_2;
                        $others_income_ary['main_grp'] = $others_income_ledger_name->main_group;
                        $others_income_ary['default_ledger_id'] = $others_income_ledger_name->ledger_id;
                    }
                    $others_income_id = $this->ledger_model->getGroupLedgerId($others_income_ary);

            }else{
                $to_acc = 'GST';
                // CGST
                    $default_output_cgst_id = $general_ledger['Output_CGST'];
                    $output_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cgst_id);
                   
                    $output_cgst_ary = array(
                                    'ledger_name' => 'Output CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cgst_ledger_name)){
                        $output_cgst_ledger = $output_cgst_ledger_name->ledger_name;
                        $output_cgst_ary['ledger_name'] = $output_cgst_ledger;
                        $output_cgst_ary['primary_grp'] = $output_cgst_ledger_name->sub_group_1;
                        $output_cgst_ary['second_grp'] = $output_cgst_ledger_name->sub_group_2;
                        $output_cgst_ary['main_grp'] = $output_cgst_ledger_name->main_group;
                        $output_cgst_ary['default_ledger_id'] = $output_cgst_ledger_name->ledger_id;
                    }
                    $output_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_cgst_ary);

                     // SGST
                    $default_output_sgst_id = $general_ledger['Output_SGST'];
                    $output_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_sgst_id);
                   
                    $output_sgst_ary = array(
                                    'ledger_name' => 'Output SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_sgst_ledger_name)){
                        $output_sgst_ledger = $output_sgst_ledger_name->ledger_name;
                        $output_sgst_ary['ledger_name'] = $output_sgst_ledger;
                    $output_sgst_ary['primary_grp'] = $output_sgst_ledger_name->sub_group_1;
                        $output_sgst_ary['second_grp'] = $output_sgst_ledger_name->sub_group_2;
                        $output_sgst_ary['main_grp'] = $output_sgst_ledger_name->main_group;
                        $output_sgst_ary['default_ledger_id'] = $output_sgst_ledger_name->ledger_id;
                    }
                    $output_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_sgst_ary);

                     // UTGST
                    $default_output_utgst_id = $general_ledger['Output_UTGST'];
                    $output_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_utgst_id);
                   
                    $output_utgst_ary = array(
                                    'ledger_name' => 'Output UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_utgst_ledger_name)){
                        $input_utgst_ledger = $output_utgst_ledger_name->ledger_name;
                        $output_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $output_utgst_ary['primary_grp'] = $output_utgst_ledger_name->sub_group_1;
                        $output_utgst_ary['second_grp'] = $output_utgst_ledger_name->sub_group_2;
                        $output_utgst_ary['main_grp'] = $output_utgst_ledger_name->main_group;
                        $output_utgst_ary['default_ledger_id'] = $output_utgst_ledger_name->ledger_id;
                    }
                    $output_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($output_utgst_ary);

                     // IGST
                    $default_output_igst_id = $general_ledger['Output_IGST'];
                    $output_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_igst_id);
                   
                    $output_igst_ary = array(
                                    'ledger_name' => 'Output IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_igst_ledger_name)){
                        $output_igst_ledger = $output_igst_ledger_name->ledger_name;
                        $output_igst_ary['ledger_name'] = $output_igst_ledger;
                        $output_igst_ary['primary_grp'] = $output_igst_ledger_name->sub_group_1;
                        $output_igst_ary['second_grp'] = $output_igst_ledger_name->sub_group_2;
                        $output_igst_ary['main_grp'] = $output_igst_ledger_name->main_group;
                        $output_igst_ary['default_ledger_id'] = $output_igst_ledger_name->ledger_id;
                    }
                    $output_igst_ledger_id = $this->ledger_model->getGroupLedgerId($output_igst_ary);

                     // Cess
                    $default_output_cess_id = $general_ledger['Output_Cess'];
                    $output_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_output_cess_id);
                   
                    $output_cess_ary = array(
                                    'ledger_name' => 'Output Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_output_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($output_cess_ledger_name)){
                        $output_cess_ledger = $output_cess_ledger_name->ledger_name;
                        $output_cess_ary['ledger_name'] = $output_cess_ledger;
                        $output_cess_ary['primary_grp'] = $output_cess_ledger_name->sub_group_1;
                        $output_cess_ary['second_grp'] = $output_cess_ledger_name->sub_group_2;
                        $output_cess_ary['main_grp'] = $output_cess_ledger_name->main_group;
                        $output_cess_ary['default_ledger_id'] = $output_cess_ledger_name->ledger_id;
                    }
                    $output_cess_ledger_id = $this->ledger_model->getGroupLedgerId($output_cess_ary);
            }


              

        }elseif($transaction_purpose == 'Tax payable'){
             if($transaction_category =='Tax paid to INCOME TAX'){ 
                $to_ledger = 'Provision for Income tax';
                $default_income_tax_id = $general_ledger['Provision_Income_tax'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Provision for Income tax',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Duties and taxes',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $to_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);
                        $to_acc = $income_tax_ledger;

                    $default_intrest_it_id = $general_ledger['Interest'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                   
                    $intrest_it_ary = array(
                                    'ledger_name' => 'Interest',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                        $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $intrest_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);


                    $default_others_expense_id = $general_ledger['Others_expense'];
                    $others_expense_ledger_name = $this->ledger_model->getDefaultLedgerId($default_others_expense_id);
                   
                    $others_expense_ary = array(
                                    'ledger_name' => 'Others',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_others_expense_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($others_expense_ledger_name)){
                        $others_expense_ledger = $others_expense_ledger_name->ledger_name;
                        $others_expense_ary['ledger_name'] = $others_expense_ledger;
                        $others_expense_ary['primary_grp'] = $others_expense_ledger_name->sub_group_1;
                        $others_expense_ary['second_grp'] = $others_expense_ledger_name->sub_group_2;
                        $others_expense_ary['main_grp'] = $others_expense_ledger_name->main_group;
                        $others_expense_ary['default_ledger_id'] = $others_expense_ledger_name->ledger_id;
                    }
                    $others_expense_id = $this->ledger_model->getGroupLedgerId($others_expense_ary);

             }else{
                $to_acc = 'GST';
            // CGST
                    $default_input_cgst_id = $general_ledger['Input_CGST'];
                    $input_cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cgst_id);
                   
                    $input_cgst_ary = array(
                                    'ledger_name' => 'Input CGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cgst_ledger_name)){
                        $input_cgst_ledger = $input_cgst_ledger_name->ledger_name;
                        $input_cgst_ary['ledger_name'] = $input_cgst_ledger;
                        $input_cgst_ary['primary_grp'] = $input_cgst_ledger_name->sub_group_1;
                        $input_cgst_ary['second_grp'] = $input_cgst_ledger_name->sub_group_2;
                        $input_cgst_ary['main_grp'] = $input_cgst_ledger_name->main_group;
                        $input_cgst_ary['default_ledger_id'] = $input_cgst_ledger_name->ledger_id;
                    }
                    $input_cgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_cgst_ary);

                     // SGST
                    $default_input_sgst_id = $general_ledger['Input_SGST'];
                    $input_sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_sgst_id);
                   
                    $input_sgst_ary = array(
                                    'ledger_name' => 'Input SGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_sgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_sgst_ledger_name)){
                        $input_sgst_ledger = $input_sgst_ledger_name->ledger_name;
                        $input_sgst_ary['ledger_name'] = $input_sgst_ledger;
                    $input_sgst_ary['primary_grp'] = $input_sgst_ledger_name->sub_group_1;
                        $input_sgst_ary['second_grp'] = $input_sgst_ledger_name->sub_group_2;
                        $input_sgst_ary['main_grp'] = $input_sgst_ledger_name->main_group;
                        $input_sgst_ary['default_ledger_id'] = $input_sgst_ledger_name->ledger_id;
                    }
                    $input_sgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_sgst_ary);

                     // UTGST
                    $default_input_utgst_id = $general_ledger['Input_UTGST'];
                    $input_utgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_utgst_id);
                   
                    $input_utgst_ary = array(
                                    'ledger_name' => 'Input UTGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_utgst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_utgst_ledger_name)){
                        $input_utgst_ledger = $input_utgst_ledger_name->ledger_name;
                        $input_utgst_ary['ledger_name'] = $input_utgst_ledger;
                        $input_utgst_ary['primary_grp'] = $input_utgst_ledger_name->sub_group_1;
                        $input_utgst_ary['second_grp'] = $input_utgst_ledger_name->sub_group_2;
                        $input_utgst_ary['main_grp'] = $input_utgst_ledger_name->main_group;
                        $input_utgst_ary['default_ledger_id'] = $input_utgst_ledger_name->ledger_id;
                    }
                    $input_utgst_ledger_id = $this->ledger_model->getGroupLedgerId($input_utgst_ary);

                     // IGST
                    $default_input_igst_id = $general_ledger['Input_IGST'];
                    $input_igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_igst_id);
                   
                    $input_igst_ary = array(
                                    'ledger_name' => 'Input IGST',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_igst_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_igst_ledger_name)){
                        $input_igst_ledger = $input_igst_ledger_name->ledger_name;
                        $input_igst_ary['ledger_name'] = $input_igst_ledger;
                        $input_igst_ary['primary_grp'] = $input_igst_ledger_name->sub_group_1;
                        $input_igst_ary['second_grp'] = $input_igst_ledger_name->sub_group_2;
                        $input_igst_ary['main_grp'] = $input_igst_ledger_name->main_group;
                        $input_igst_ary['default_ledger_id'] = $input_igst_ledger_name->ledger_id;
                    }
                    $input_igst_ledger_id = $this->ledger_model->getGroupLedgerId($input_igst_ary);

                     // Cess
                    $default_input_cess_id = $general_ledger['Input_Cess'];
                    $input_cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_input_cess_id);
                   
                    $input_cess_ary = array(
                                    'ledger_name' => 'Input Cess',
                                    'second_grp' => '',
                                    'primary_grp' => 'GST',
                                    'main_grp' => 'Duties and Taxes',
                                    'default_ledger_id' => $default_input_cess_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($input_cess_ledger_name)){
                        $input_cess_ledger = $input_cess_ledger_name->ledger_name;
                        $input_cess_ary['ledger_name'] = $input_cess_ledger;
                        $input_cess_ary['primary_grp'] = $input_cess_ledger_name->sub_group_1;
                        $input_cess_ary['second_grp'] = $input_cess_ledger_name->sub_group_2;
                        $input_cess_ary['main_grp'] = $input_cess_ledger_name->main_group;
                        $input_cess_ary['default_ledger_id'] = $input_cess_ledger_name->ledger_id;
                    }
                    $input_cess_ledger_id = $this->ledger_model->getGroupLedgerId($input_cess_ary);
                }
        }elseif($transaction_purpose == 'Interest'){
            if($transaction_category == 'Interest Income earned'){
                $from_ledger = 'Interest Receivable';
                $default_income_tax_id = $general_ledger['Interest_Receivable_other'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Interest Receivable',
                                        'second_grp' => '',
                                        'primary_grp' => 'Other Current Assets',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $from_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);
                        $from_acc = $income_tax_ledger;
                         $table = 'tbl_deposit';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'deposit_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;
                $deposit_type = $deposits[0]->deposit_type;
                
                }else{
                    if($input_type == 'interest liability'){
                        $default_intrest_it_id = $general_ledger['Interest'];
                        $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                        $from_ledger = 'Interest';
                        $intrest_it_ary = array(
                                        'ledger_name' => 'Interest',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Indirect Expenses',
                                        'default_ledger_id' => $default_intrest_it_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($intrest_ledger_name)){
                            $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                            $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                            $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                            $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                            $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                            $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                        }
                        $from_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);
                        $from_acc = $from_ledger_id;

                        $default_tds_payable_id = $general_ledger['Tds_payable'];
                        $tds_payable_name = $this->ledger_model->getDefaultLedgerId($default_tds_payable_id);
                        
                        $tds_payable_ary = array(
                                        'ledger_name' => 'TDS Payable',
                                        'second_grp' => '',
                                        'primary_grp' => 'TDS Payable',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => $default_intrest_it_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($tds_payable_name)){
                            $tds_payable_ledger = $tds_payable_name->ledger_name;
                            $tds_payable_ary['ledger_name'] = $tds_payable_ledger;
                            $tds_payable_ary['primary_grp'] = $tds_payable_name->sub_group_1;
                            $tds_payable_ary['second_grp'] = $tds_payable_name->sub_group_2;
                            $tds_payable_ary['main_grp'] = $tds_payable_name->main_group;
                            $tds_payable_ary['default_ledger_id'] = $tds_payable_name->ledger_id;
                        }
                        $tds_payable_ledger_id = $this->ledger_model->getGroupLedgerId($tds_payable_ary);
                    
                }

                $to_ledger = 'Interest Payable';
                $default_income_tax_id = $general_ledger['Interest_Payable'];
                $income_tax_name = $this->ledger_model->getDefaultLedgerId($default_income_tax_id);
                 $income_tax_ary = array(
                                        'ledger_name' => 'Interest Payable',
                                        'second_grp' => '',
                                        'primary_grp' => 'Other Liabilities',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => $default_income_tax_id,
                                        'default_value' => 0,
                                        'amount' => 0
                                    );
                        if(!empty($income_tax_name)){
                            $income_tax_ledger = $income_tax_name->ledger_name;
                            $income_tax_ary['ledger_name'] = $income_tax_ledger;
                            $income_tax_ary['primary_grp'] = $income_tax_name->sub_group_1;
                            $income_tax_ary['second_grp'] = $income_tax_name->sub_group_2;
                            $income_tax_ary['main_grp'] = $income_tax_name->main_group;
                            $income_tax_ary['default_ledger_id'] = $income_tax_name->ledger_id;
                        }
                        $to_ledger_id = $this->ledger_model->getGroupLedgerId($income_tax_ary);                        
                        $to_acc = 'interest-' . $income_tax_ledger;
                        $deposit_type = '';
            }
               
                if($deposit_type == 'fixed deposit'){
                
                $to_ledger = $deposit_name = 'Fixed Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'interest-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Fixed_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($deposit_type == 'recurring deposit'){

                $to_ledger = $deposit_name = 'Recurring Deposit@'.$deposits[0]->deposit_bank;
                $to_acc = 'interest-' . $deposits[0]->deposit_bank;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Recurring_Deposit'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => 'NA',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

            }elseif($deposit_type == 'others'){

                $to_ledger = $deposit_name = $deposits[0]->others_name;
                $to_acc = 'interest-' . $deposits[0]->others_name;
                $to_ledger_id = $supplier_ledger_id =  $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $supplier_ledger_id = $general_ledger['Other_Deposits'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => $supplier_ledger_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->deposit_bank, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;
            }

        }elseif($transaction_purpose == 'Investments'){            
           
                $table = 'tbl_investments';
                $where = array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'investments_id' => $payee_id,
                'delete_status' => 0);
                 $deposits = $this->general_model->getRecords('*', $table, $where);
                $deposit_ledger_id = $deposits[0]->ledger_id;
                $to_ledger = $deposit_name = $transaction_category.'@'.$deposits[0]->investments_type;
                $to_acc = 'Investments-' . $deposits[0]->investments_type;
                $to_ledger_id = $supplier_ledger_id = $deposits[0]->ledger_id;

                if(!$to_ledger_id){
                    $default_fixed_id = $general_ledger['Investments'];
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                        
                    $supplier_ary = array(
                                'ledger_name' => $deposit_name,
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Investments',
                                'default_ledger_id' => $default_fixed_id,
                                'default_value' => 0,
                                'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$deposits[0]->investments_type, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $supplier_ledger_id;
                $to_acc = $transaction_ledger;

             if($voucher_type == 'RECEIPTS'){   
                $default_loss_sale_id = $general_ledger['Loss_on_sale_investment'];
                $loss_sale_name = $this->ledger_model->getDefaultLedgerId($default_loss_sale_id);
                $loss_sale_ary = array(
                                        'ledger_name' => 'Loss on sale of investment',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Preference shares invested by Shareholders',
                                        'default_ledger_id' => $default_loss_sale_id,
                                        'default_value' => 0,
                                       'amount' => 0
                                        );
            if(!empty($loss_sale_ary)){
                $loss_sale_ledger = $loss_sale_name->ledger_name;
                $loss_sale_ary['ledger_name'] = $loss_sale_ledger;
                $loss_sale_ary['primary_grp'] = $loss_sale_name->sub_group_1;
                $loss_sale_ary['second_grp'] = $loss_sale_name->sub_group_2;
                $loss_sale_ary['main_grp'] = $loss_sale_name->main_group;
                $loss_sale_ary['default_ledger_id'] = $loss_sale_name->ledger_id;
            }
            $loss_on_sale_id = $this->ledger_model->getGroupLedgerId($loss_sale_ary);

            $default_profit_sale_id = $general_ledger['Profit_on_sale_investment'];
            $profit_sale_name = $this->ledger_model->getDefaultLedgerId($default_profit_sale_id);
       
            $profit_sale_ary = array(
                        'ledger_name' => 'Profit on sale of investment',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Indirect Incomes',
                        'default_ledger_id' => $default_profit_sale_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($profit_sale_ary)){
                    $profit_sale_ledger = $profit_sale_name->ledger_name;
                    $profit_sale_ary['ledger_name'] = $profit_sale_ledger;
                    $profit_sale_ary['primary_grp'] = $profit_sale_name->sub_group_1;
                    $profit_sale_ary['second_grp'] = $profit_sale_name->sub_group_2;
                    $profit_sale_ary['main_grp'] = $profit_sale_name->main_group;
                    $profit_sale_ary['default_ledger_id'] = $profit_sale_name->ledger_id;
                }
                $profit_sale_ledger_id = $this->ledger_model->getGroupLedgerId($profit_sale_ary);
           }

        }elseif($transaction_purpose == 'Loan Borrowed and repaid'){
           
                $default_fixed_id = $general_ledger['Director'];
                if( $input_type == 'director loan'){
                    //$payee_id = $this->input->post('cmb_partner');
                    $table = 'tbl_shareholder';
                    $where = array(
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'id' => $payee_id,
                    'sharholder_type' => 'director',
                    'delete_status' => 0);
                    $partner = $this->general_model->getRecords('*', $table, $where); 
                    $to_ledger = $sharholder_name = $partner[0]->sharholder_name;              
                    $to_acc = 'director-' . $partner[0]->sharholder_name;
                    $to_ledger_id = $partner[0]->partner_ledger_id;
                    $partner_ledger_id = $partner[0]->partner_ledger_id;    

                }elseif( $input_type == 'others loan'){
                   // $payee_id = $this->input->post('cmb_partner');
                     $table = 'tbl_loans';
                    $where = array(
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'loan_id' => $payee_id,
                    'loan_type' => 'others',
                    'delete_status' => 0);
                    $partner = $this->general_model->getRecords('*', $table, $where); 
                    $to_ledger = $sharholder_name = $partner[0]->others_name;              
                    $to_acc = 'others-' . $partner[0]->others_name;
                    $to_ledger_id = $partner[0]->ledger_id;
                    $partner_ledger_id = $partner[0]->ledger_id;  
                    
                }else{
                  //  $payee_id = $this->input->post('cmb_partner');
                   

                    $table = 'tbl_loans';
                    $where = array(
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'loan_id' => $payee_id,
                    'loan_type' => 'bank',
                    'delete_status' => 0);
                    $partner = $this->general_model->getRecords('*', $table, $where); 
                    $to_ledger = $sharholder_name = $partner[0]->loan_bank;              
                    $to_acc = 'bank-' . $partner[0]->loan_bank;
                    $to_ledger_id = $partner[0]->ledger_id;
                    $partner_ledger_id = $partner[0]->ledger_id;  
                }
                

                if(!$to_ledger_id){                    
                    $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
                    $supplier_ary = array(
                        'ledger_name' => $sharholder_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Loans (Liability)',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                            );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}',$sharholder_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                    $to_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                }
                $to_ledger_id = $to_ledger_id;
                $to_acc = $transaction_ledger;


               if($voucher_type == 'PAYMENT'){
                $default_intrest_it_id = $general_ledger['Interest'];
                    $intrest_ledger_name = $this->ledger_model->getDefaultLedgerId($default_intrest_it_id);
                   
                    $intrest_it_ary = array(
                                    'ledger_name' => 'Interest',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_intrest_it_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($intrest_ledger_name)){
                        $intrest_it_ledger = $intrest_ledger_name->ledger_name;
                        $intrest_it_ary['ledger_name'] = $intrest_it_ledger;
                        $intrest_it_ary['primary_grp'] = $intrest_ledger_name->sub_group_1;
                        $intrest_it_ary['second_grp'] = $intrest_ledger_name->sub_group_2;
                        $intrest_it_ary['main_grp'] = $intrest_ledger_name->main_group;
                        $intrest_it_ary['default_ledger_id'] = $intrest_ledger_name->ledger_id;
                    }
                    $intrest_ledger_id = $this->ledger_model->getGroupLedgerId($intrest_it_ary);


                    $default_others_expense_id = $general_ledger['Others_expense'];
                    $others_expense_ledger_name = $this->ledger_model->getDefaultLedgerId($default_others_expense_id);
                   
                    $others_expense_ary = array(
                                    'ledger_name' => 'Others',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Indirect Expenses',
                                    'default_ledger_id' => $default_others_expense_id,
                                    'default_value' => 0,
                                    'amount' => 0
                                );
                    if(!empty($others_expense_ledger_name)){
                        $others_expense_ledger = $others_expense_ledger_name->ledger_name;
                        $others_expense_ary['ledger_name'] = $others_expense_ledger;
                        $others_expense_ary['primary_grp'] = $others_expense_ledger_name->sub_group_1;
                        $others_expense_ary['second_grp'] = $others_expense_ledger_name->sub_group_2;
                        $others_expense_ary['main_grp'] = $others_expense_ledger_name->main_group;
                        $others_expense_ary['default_ledger_id'] = $others_expense_ledger_name->ledger_id;
                    }
                    $others_expense_id = $this->ledger_model->getGroupLedgerId($others_expense_ary);
               }

                    
            

        } 
        
       $voucher_date = date('Y-m-d',strtotime($this->input->post('voucher_date')));
        $general_voucher_data = array(
                "transaction_purpose_id" => $this->input->post('trans_purpose'),
               "voucher_date" => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
                "voucher_number"  => $voucher_number,
                "option_id" => $payee_id,
                "transaction_mode" => $payment_mode,
                "voucher_type" => $this->input->post('voucher_type'),
                "amount" => $this->input->post('receipt_amount'),
                "input_type" => $this->input->post('input_type'),
                "narration" => $this->input->post('description'),
                "added_date"  => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'), 
                "from_account" => $from_acc,
                "to_account" => $to_acc,
                "cheque_date"         => $cheque_date,
                "cheque_number"       => $this->input->post('cheque_number'),
                "payment_via"         => $payment_via,
                "ref_number"          => $reff_number,
                "igst"       => $igst_amount,
                "cgst"       => $cgst_amount,
                "sgst"       => $sgst_amount,
                "utgst"       => $utgst_amount,
                "cess"       => $cess_amount,
                "tds"       => $tds_amount,
                "interest_expense_amount" => $interest_expense_amount,
                "partner_shareholder_id" => $this->input->post('cmb_partner'),
                "currency_id" => 0,
                "delete_status" => 0    
                );
       
        $data_main  = array_map('trim', $general_voucher_data);
        $general_voucher_id = $this->input->post('general_voucher_id');
        $general_voucher_table = 'tbl_journal_voucher';
        $where = array('journal_voucher_id' => $general_voucher_id);
        $this->general_model->updateData($general_voucher_table, $data_main, $where);

        if($input_type == 'suppliers' ){ 
            if($voucher_type == 'PAYMENT'){            
                $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else if($voucher_type == 'RECEIPTS'){
                if($interest_expense_amount > 0 && $transaction_category == 'Advance repaid by vendor'){
                $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                $bank_account_amount = $voucher_amount - $interest_expense_amount ;
                }else{
                    $bank_account_amount = $voucher_amount;
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            } 
        }elseif($input_type == 'financial year' ){ 

            if($voucher_type == 'PAYMENT'){            
                $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else if($voucher_type == 'RECEIPTS'){

               if($interest_expense_amount > 0 && $transaction_category == 'Advance Tax Refund by Govt'){
                $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$extra_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$extra_ledger_id]["cr_amount"] = $interest_expense_amount;
                $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                $bank_account_amount = $voucher_amount + $interest_expense_amount ;
                }else{
                    $bank_account_amount = $voucher_amount;
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
               
            }
        }elseif($input_type == 'proprietor'){
                if($voucher_type == 'PAYMENT'){ 
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  0;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }else{
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }

        }elseif($input_type == 'shareholder' ){
                if($transaction_category == 'Preference share issue to shareholders' || $transaction_category == 'Equity shares issued to shareholder'){
                    if($interest_expense_amount > 0){
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $bank_account_amount = $voucher_amount + $interest_expense_amount ;
                    }else{
                        $bank_account_amount = $voucher_amount;
                    }
                }
                
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
        }elseif($input_type == 'partner' ){
            if($voucher_type == 'PAYMENT'){  
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else if($voucher_type == 'RECEIPTS'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }
        }elseif($transaction_purpose == 'Cash transactions'){
            if($voucher_type == 'CONTRA A/C'){
                if($transaction_category == 'Cash deposited in bank'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    if($interest_expense_amount > 0){
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $cash_amount = $interest_expense_amount + $voucher_amount;
                    }else{
                        $cash_amount = $voucher_amount;
                    }

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $cash_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $cash_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    
                }elseif($transaction_category == 'Cash withdrawal from bank'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }
            }elseif($voucher_type == 'RECEIPTS'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            }elseif($voucher_type == 'PAYMENT'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }
        }elseif($transaction_purpose == 'Deposits'){
            if($voucher_type == 'RECEIPTS'){
                if($interest_expense_amount > 0 ){
                    if($input_type == 'recurring' || $input_type == 'fixed'){
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] =  $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $bank_account_amount = $voucher_amount + $interest_expense_amount ;
                        
                    }else{
                         $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = 0; 
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                        $bank_account_amount = $voucher_amount - $interest_expense_amount ;
                        
                    }
                }else{
                   $bank_account_amount = $voucher_amount;
                }
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            }elseif($voucher_type == 'PAYMENT'){ 
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    
            }
        }elseif($transaction_purpose == 'Fixed Assset'){

            if($voucher_type == 'RECEIPTS'){
               /* if($interest_expense_amount > 0 ){                    
                         $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] = 0; 
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id; 
                }*/

                if($others_amount_tax > 0){
                        $ledger_entry[$loss_on_sale_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$loss_on_sale_id]["ledger_to"] = $loss_on_sale_id;
                        $ledger_entry[$loss_on_sale_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$loss_on_sale_id]["voucher_amount"] =  $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$loss_on_sale_id]["dr_amount"] = $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["cr_amount"] =  0;
                        $ledger_entry[$loss_on_sale_id]['ledger_id'] = $loss_on_sale_id;
                    }
                    if($interest_expense_amount > 0){
                        $ledger_entry[$profit_sale_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["ledger_to"] = $profit_sale_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$profit_sale_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]['ledger_id'] = $profit_sale_ledger_id;
                    }
                

                $bank_account_amount = $voucher_amount + $interest_expense_amount + $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount - $others_amount_tax;

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$output_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["ledger_to"] = $output_sgst_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["cr_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]['ledger_id'] = $output_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$output_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["ledger_to"] = $output_igst_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["cr_amount"] =  $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]['ledger_id'] = $output_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$output_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["ledger_to"] = $output_cgst_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["cr_amount"] =  $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]['ledger_id'] = $output_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$output_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["ledger_to"] = $output_utgst_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["cr_amount"] =  $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]['ledger_id'] = $output_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$output_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["ledger_to"] = $output_cess_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["cr_amount"] =  $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]['ledger_id'] = $output_cess_ledger_id;
                   
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

            }elseif($voucher_type == 'PAYMENTS'){               

                if($interest_expense_amount > 0 ){                   
                        $ledger_entry[$extra_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$extra_ledger_id]["ledger_to"] = $extra_ledger_id;
                        $ledger_entry[$extra_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$extra_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$extra_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$extra_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$extra_ledger_id]['ledger_id'] = $extra_ledger_id;
                   
                }
                

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$input_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["ledger_to"] = $input_sgst_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_sgst_ledger_id]["dr_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_sgst_ledger_id]['ledger_id'] = $input_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$input_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["ledger_to"] = $input_igst_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_igst_ledger_id]["dr_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_igst_ledger_id]['ledger_id'] = $input_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$input_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["ledger_to"] = $input_cgst_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cgst_ledger_id]["dr_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cgst_ledger_id]['ledger_id'] = $input_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$input_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["ledger_to"] = $input_utgst_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_utgst_ledger_id]["dr_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_utgst_ledger_id]['ledger_id'] = $input_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$input_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["ledger_to"] = $input_cess_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cess_ledger_id]["dr_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cess_ledger_id]['ledger_id'] = $input_cess_ledger_id;
                   
                }

                    $bank_account_amount = $voucher_amount + $interest_expense_amount + $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    
            }
        }elseif($transaction_purpose == 'Tax receivables'){
             if($transaction_category =='Tax received from Income Tax'){
                $bank_account_amount = $others_amount_tax + $interest_expense_amount + $voucher_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;

                    if($interest_expense_amount > 0){
                        $ledger_entry[$intrest_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["ledger_to"] = $intrest_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$intrest_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]['ledger_id'] = $intrest_ledger_id;

                    }

                    if($others_amount_tax > 0){
                        $ledger_entry[$others_income_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$others_income_id]["ledger_to"] = $others_income_id;
                        $ledger_entry[$others_income_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$others_income_id]["voucher_amount"] = $others_amount_tax;
                        $ledger_entry[$others_income_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$others_income_id]["dr_amount"] = 0;
                        $ledger_entry[$others_income_id]["cr_amount"] = $others_amount_tax;
                        $ledger_entry[$others_income_id]['ledger_id'] = $others_income_id;
                    }

            }else{
            $bank_account_amount = $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount;

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$output_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["ledger_to"] = $output_sgst_ledger_id;
                        $ledger_entry[$output_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_sgst_ledger_id]["cr_amount"] = $sgst_amount;
                        $ledger_entry[$output_sgst_ledger_id]['ledger_id'] = $output_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$output_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["ledger_to"] = $output_igst_ledger_id;
                        $ledger_entry[$output_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_igst_ledger_id]["cr_amount"] =  $igst_amount;
                        $ledger_entry[$output_igst_ledger_id]['ledger_id'] = $output_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$output_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["ledger_to"] = $output_cgst_ledger_id;
                        $ledger_entry[$output_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cgst_ledger_id]["cr_amount"] =  $cgst_amount;
                        $ledger_entry[$output_cgst_ledger_id]['ledger_id'] = $output_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$output_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["ledger_to"] = $output_utgst_ledger_id;
                        $ledger_entry[$output_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_utgst_ledger_id]["cr_amount"] =  $utgst_amount;
                        $ledger_entry[$output_utgst_ledger_id]['ledger_id'] = $output_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$output_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["ledger_to"] = $output_cess_ledger_id;
                        $ledger_entry[$output_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$output_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$output_cess_ledger_id]["cr_amount"] =  $cess_amount;
                        $ledger_entry[$output_cess_ledger_id]['ledger_id'] = $output_cess_ledger_id;
                   
                }

                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;
            }

        }elseif($transaction_purpose == 'Tax payable'){
            if($transaction_category =='Tax paid to INCOME TAX'){
                $bank_account_amount = $others_amount_tax + $interest_expense_amount + $voucher_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    if($interest_expense_amount > 0){
                   $ledger_entry[$intrest_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$intrest_ledger_id]["ledger_to"] = $intrest_ledger_id;
                    $ledger_entry[$intrest_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$intrest_ledger_id]["voucher_amount"] = $interest_expense_amount;
                    $ledger_entry[$intrest_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$intrest_ledger_id]["dr_amount"] = $interest_expense_amount;
                    $ledger_entry[$intrest_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$intrest_ledger_id]['ledger_id'] = $intrest_ledger_id; 
                    }
                    if($others_amount_tax > 0){
                    $ledger_entry[$others_expense_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$others_expense_id]["ledger_to"] = $others_expense_id;
                    $ledger_entry[$others_expense_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$others_expense_id]["voucher_amount"] = $others_amount_tax;
                    $ledger_entry[$others_expense_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$others_expense_id]["dr_amount"] = $others_amount_tax;
                    $ledger_entry[$others_expense_id]["cr_amount"] = 0;
                    $ledger_entry[$others_expense_id]['ledger_id'] = $others_expense_id; 
                }
                

            }else{

                if($sgst_amount > 0 ){                   
                        $ledger_entry[$input_sgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["ledger_to"] = $input_sgst_ledger_id;
                        $ledger_entry[$input_sgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_sgst_ledger_id]["voucher_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_sgst_ledger_id]["dr_amount"] = $sgst_amount;
                        $ledger_entry[$input_sgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_sgst_ledger_id]['ledger_id'] = $input_sgst_ledger_id;
                   
                }

                if($igst_amount > 0 ){                   
                        $ledger_entry[$input_igst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["ledger_to"] = $input_igst_ledger_id;
                        $ledger_entry[$input_igst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_igst_ledger_id]["voucher_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_igst_ledger_id]["dr_amount"] = $igst_amount;
                        $ledger_entry[$input_igst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_igst_ledger_id]['ledger_id'] = $input_igst_ledger_id;
                   
                }

                if($cgst_amount > 0 ){                   
                        $ledger_entry[$input_cgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["ledger_to"] = $input_cgst_ledger_id;
                        $ledger_entry[$input_cgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cgst_ledger_id]["voucher_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cgst_ledger_id]["dr_amount"] = $cgst_amount;
                        $ledger_entry[$input_cgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cgst_ledger_id]['ledger_id'] = $input_cgst_ledger_id;
                   
                }

                if($utgst_amount > 0 ){                   
                        $ledger_entry[$input_utgst_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["ledger_to"] = $input_utgst_ledger_id;
                        $ledger_entry[$input_utgst_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_utgst_ledger_id]["voucher_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_utgst_ledger_id]["dr_amount"] = $utgst_amount;
                        $ledger_entry[$input_utgst_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_utgst_ledger_id]['ledger_id'] = $input_utgst_ledger_id;
                   
                }

                if($cess_amount > 0 ){                   
                        $ledger_entry[$input_cess_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["ledger_to"] = $input_cess_ledger_id;
                        $ledger_entry[$input_cess_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$input_cess_ledger_id]["voucher_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$input_cess_ledger_id]["dr_amount"] = $cess_amount;
                        $ledger_entry[$input_cess_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$input_cess_ledger_id]['ledger_id'] = $input_cess_ledger_id;
                   
                }

                    $bank_account_amount =  $sgst_amount + $cgst_amount + $utgst_amount + $igst_amount + $cess_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;
                }
        }elseif($transaction_purpose == 'Interest'){  
                if($transaction_category == 'Interest Income earned'){             
                    $voucher_amount = $voucher_amount - $tds_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $tds_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $tds_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                }else{

                    if($input_type == 'interest liability'){
                        $interest_amount = $voucher_amount + $tds_amount; 
                        $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$from_ledger_id]["voucher_amount"] =  $interest_amount;
                        $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$from_ledger_id]["dr_amount"] = $interest_amount;
                        $ledger_entry[$from_ledger_id]["cr_amount"] =  0;
                        $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                        $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                        if($tds_amount > 0){
                            $ledger_entry[$tds_payable_ledger_id]["ledger_from"] = $from_ledger_id;
                            $ledger_entry[$tds_payable_ledger_id]["ledger_to"] = $tds_payable_ledger_id;
                            $ledger_entry[$tds_payable_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                            $ledger_entry[$tds_payable_ledger_id]["voucher_amount"] = $tds_amount;
                            $ledger_entry[$tds_payable_ledger_id]["converted_voucher_amount"] = 0;
                            $ledger_entry[$tds_payable_ledger_id]["dr_amount"] = 0;
                            $ledger_entry[$tds_payable_ledger_id]["cr_amount"] = $tds_amount;
                            $ledger_entry[$tds_payable_ledger_id]['ledger_id'] = $tds_payable_ledger_id;
                        }
                        
                    }else{
                        $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$from_ledger_id]["voucher_amount"] =  $voucher_amount;
                        $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                        $ledger_entry[$from_ledger_id]["cr_amount"] =  $voucher_amount;
                        $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                        $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                        $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                        $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                        $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    }
                }



        }elseif($transaction_purpose == 'Investments'){
            if($voucher_type == 'PAYMENTS'){
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0; 
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  $voucher_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else{
                    $bank_account_amount =  $voucher_amount - $others_amount_tax + $interest_expense_amount;
                    // Amount + profit - loss
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] =  $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["cr_amount"] =  0;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    if($others_amount_tax > 0){
                        $ledger_entry[$loss_on_sale_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$loss_on_sale_id]["ledger_to"] = $loss_on_sale_id;
                        $ledger_entry[$loss_on_sale_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$loss_on_sale_id]["voucher_amount"] =  $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$loss_on_sale_id]["dr_amount"] = $others_amount_tax;
                        $ledger_entry[$loss_on_sale_id]["cr_amount"] =  0;
                        $ledger_entry[$loss_on_sale_id]['ledger_id'] = $loss_on_sale_id;
                    }
                    if($interest_expense_amount > 0){
                        $ledger_entry[$profit_sale_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["ledger_to"] = $profit_sale_ledger_id;
                        $ledger_entry[$profit_sale_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$profit_sale_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["dr_amount"] = 0;
                        $ledger_entry[$profit_sale_ledger_id]["cr_amount"] = $interest_expense_amount;
                        $ledger_entry[$profit_sale_ledger_id]['ledger_id'] = $profit_sale_ledger_id;
                    }
            }
                    
        }elseif($transaction_purpose == 'Loan Borrowed and repaid'){
            if($voucher_type == 'RECEIPTS'){
                $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$from_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$from_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$from_ledger_id]["dr_amount"] =  $voucher_amount;
                $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
                $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                $ledger_entry[$to_ledger_id]["dr_amount"] = 0;
                $ledger_entry[$to_ledger_id]["cr_amount"] = $voucher_amount;
                $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
            }else{
               

                 $bank_account_amount = $others_amount_tax + $interest_expense_amount + $voucher_amount;
                    $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["ledger_to"] = $from_ledger_id;
                    $ledger_entry[$from_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$from_ledger_id]["voucher_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
                    $ledger_entry[$from_ledger_id]["cr_amount"] = $bank_account_amount;
                    $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;

                    $ledger_entry[$to_ledger_id]["ledger_from"] = $from_ledger_id;
                    $ledger_entry[$to_ledger_id]["ledger_to"] = $to_ledger_id;
                    $ledger_entry[$to_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                    $ledger_entry[$to_ledger_id]["voucher_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["converted_voucher_amount"] = 0;
                    $ledger_entry[$to_ledger_id]["dr_amount"] = $voucher_amount;
                    $ledger_entry[$to_ledger_id]["cr_amount"] = 0;
                    $ledger_entry[$to_ledger_id]['ledger_id'] = $to_ledger_id;
                    if($interest_expense_amount > 0){
                        $ledger_entry[$intrest_ledger_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["ledger_to"] = $intrest_ledger_id;
                        $ledger_entry[$intrest_ledger_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$intrest_ledger_id]["voucher_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]["dr_amount"] = $interest_expense_amount;
                        $ledger_entry[$intrest_ledger_id]["cr_amount"] = 0;
                        $ledger_entry[$intrest_ledger_id]['ledger_id'] = $intrest_ledger_id; 
                    }
                    if($others_amount_tax > 0){
                        $ledger_entry[$others_expense_id]["ledger_from"] = $from_ledger_id;
                        $ledger_entry[$others_expense_id]["ledger_to"] = $others_expense_id;
                        $ledger_entry[$others_expense_id]["journal_voucher_id"] = $general_voucher_id;
                        $ledger_entry[$others_expense_id]["voucher_amount"] = $others_amount_tax;
                        $ledger_entry[$others_expense_id]["converted_voucher_amount"] = 0;
                        $ledger_entry[$others_expense_id]["dr_amount"] = $others_amount_tax;
                        $ledger_entry[$others_expense_id]["cr_amount"] = 0;
                        $ledger_entry[$others_expense_id]['ledger_id'] = $others_expense_id; 
                    }
            }
        }
        

        $old_voucher_items = $this->general_model->getRecords('*', 'accounts_journal_voucher', array('journal_voucher_id' => $general_voucher_id, 'delete_status' => 0));
        /* echo "<pre>";
            print_r($old_voucher_items);
            print_r($vouchers);
          exit(); */
        $old_sales_ledger_ids = $this->getValues($old_voucher_items, 'journal_voucher_id');
            $not_deleted_ids = array();
            foreach ($ledger_entry as $key => $value) {
                if (($led_key = array_search($value['ledger_id'], $old_sales_ledger_ids)) !== false) {
                    unset($old_sales_ledger_ids[$led_key]);
                    $accounts_receipt_id = $old_voucher_items[$led_key]->accounts_receipt_id;
                array_push($not_deleted_ids, $accounts_receipt_id);
                    $value['journal_voucher_id'] = $general_voucher_id;
                    $value['delete_status']    = 0;
                $where = array('journal_voucher_id' => $accounts_receipt_id);
                    $post_data = array('data' => $value,
                                        'where' => $where,
                                        'voucher_date' => $voucher_date,
                                        'table' => ' tbl_journal_voucher',
                                        'sub_table' => 'accounts_journal_voucher',
                                        'primary_id' => 'journal_voucher_id',
                                        'sub_primary_id' => 'journal_voucher_id'
                                    );
                    $this->general_model->updateBunchVoucherCommon($post_data);
                }
            }

        $tables = array('accounts_journal_voucher');
        $this->db->where('journal_voucher_id', $general_voucher_id);
        $this->db->delete($tables);
        $this->db->insert_batch('accounts_journal_voucher', $ledger_entry);
        redirect("general_voucher/general_voucher_list", 'refresh');

    }

    public function transaction_purpose_list(){
        $journal_voucher_module_id       = $this->config->item('journal_voucher_module');
        $data['module_id']               = $journal_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['voucher_type'] = 'journal';
        $data['redirect_uri'] = 'general_voucher';
        

        if (!empty($this->input->post())){
            $columns             = array(
                    0 => 'gv.general_voucher_id',
                    1 => 'gv.voucher_date',
                    2 => 'gv.voucher_number',
                    3 => 'gv.reference_number',
                    4 => 'gv.receipt_amount' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order') [0]['column']];
            $dir                 = $this->input->post('order') [0]['dir'];
            $list_data           = $this->common->transaction_purpose_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search') ['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search') ['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } 

            $send_data = array();

            if (!empty($posts)) {
                foreach ($posts as $post){
                    $id = $this->encryption_url->encode($post->id);
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_trans"><a data-id="' . $id . '" data-toggle="tooltip" data-placement="bottom" title="Edit Transaction Purpose" class="edit_trans btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $id . '" data-path="general_voucher/delete" data-toggle="tooltip" data-placement="bottom" title="Delete Transaction purpose"> <i class="fa fa-trash-o"></i> </a></span>';

                    $cols .= '</div></div>';
                    
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                  
                    $nestedData['transaction_purpose']     = $post->transaction_purpose;
                    $nestedData['transaction_category']     = $post->transaction_category;
                    $nestedData['customise_option']     = $post->customise_option;
                    $nestedData['input_type']     = $post->input_type;
                    $nestedData['voucher_type']     = $post->voucher_type;
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view('general_voucher/transaction_purpose_list', $data);
        }
    }


    public function add_tansaction(){
        $journal_voucher_module_id       = $this->config->item('journal_voucher_module');
        $data['module_id']               = $journal_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $trans_data = array(
                "transaction_purpose" => trim($this->input->post('txt_transaction')),
                "input_type"       => trim($this->input->post('cmb_type')),
                "voucher_type" => trim($this->input->post('voucher_type')),
              "transaction_category" => trim($this->input->post('txt_transaction_category')),
                "customise_option" => trim($this->input->post('txt_customise_option')),
                "added_date"      => date('Y-m-d'),
                "status" => 1,
                "added_user_id"   => $this->session->userdata("SESS_USER_ID"),
                "branch_id"       => $this->session->userdata("SESS_BRANCH_ID") );
       

        if ($id = $this->general_model->insertData("tbl_transaction_purpose", $trans_data)){
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'tbl_transaction_purpose',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Transaction purpose Inserted' );
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $res['flag'] = true;
           // $successMsg = 'Added Transaction Purpose Successful.';
            $res['msg'] = 'Added Transaction Purpose Successful.'; 
            //$this->session->set_flashdata('product_stock_success',$successMsg);
        }else{
                $res['flag'] = false;
              //  $errorMsg = 'Transaction Purpose Unsuccessful Added';
                $res['msg'] = 'Transaction Purpose Unsuccessful Added'; 
               // $this->session->set_flashdata('product_stock_error',$errorMsg);
        } 
        echo json_encode($res); 

    }

    public function get_tansaction($id){
        $general_voucher_id              = $this->config->item('journal_voucher_module');
        $data['module_id']               = $general_voucher_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($general_voucher_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        //$id           = $this->input->post('id');
        $id  = $this->encryption_url->decode($id);
        $data = $this->general_model->getRecords('*', 'tbl_transaction_purpose', array('id' => $id));
        echo json_encode($data);
    }


    public function edit_tansaction(){
        $general_voucher_id              = $this->config->item('journal_voucher_module');
        $data['module_id']               = $general_voucher_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($general_voucher_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('trans_id');
        $trans_data = array(
                "transaction_purpose" => trim($this->input->post('txt_transaction_e')),
                "input_type"       => trim($this->input->post('cmb_type_e')),
                "voucher_type" => trim($this->input->post('voucher_type_e')),
                "transaction_category" => trim($this->input->post('txt_transaction_category_e')),
                "customise_option" => trim($this->input->post('txt_customise_option_e')),
                "updated_date"     => date('Y-m-d'),
                "updated_user_id"  => $this->session->userdata("SESS_USER_ID"),
                "branch_id"       => $this->session->userdata("SESS_BRANCH_ID") );

        if ($this->general_model->updateData("tbl_transaction_purpose", $trans_data, array('id' => $id))){
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'tbl_transaction_purpose',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Transaction purpose Updated' );
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $res['flag'] = true;
            //$successMsg = 'Updated Transaction Purpose Successful.';
            $res['msg'] = 'Updated Transaction Purpose Successful.'; 
           // $this->session->set_flashdata('product_stock_success',$successMsg);
        }else{
                $res['flag'] = false;
              //  $errorMsg = 'Updated Transaction Purpose Unsuccessful';
                $res['msg'] = 'Transaction Purpose Unsuccessful'; 
             //   $this->session->set_flashdata('product_stock_error',$errorMsg);
        } 

        echo json_encode($res); 
        //redirect("general_voucher/transaction_purpose_list", 'refresh');
    }

    public function delete(){
        $general_voucher_id              = $this->config->item('journal_voucher_module');
        $data['module_id']               = $general_voucher_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($general_voucher_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id  = $this->input->post('delete_id');
        $id  = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('tbl_transaction_purpose', ["status" => 0 ], array('id' => $id ))){
           redirect("general_voucher/general_voucher_list", 'refresh');
        }
    }



    public function delete_general_voucher() {
        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        $advance_voucher_table           = 'advance_voucher';
       $journal_voucher_module_id         = $this->config->item('journal_voucher_module');
        $data['module_id']               = $journal_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);
        $data  = array_merge($data, $section_modules);
       

        if ($id != '') {
            $advance_voucher_res = $this->general_model->updateData('tbl_journal_voucher', array('delete_status' => 1), array('journal_voucher_id' => $id));
            if ($advance_voucher_res) {
                $this->general_model->deleteVoucher(array('journal_voucher_id' => $id), 'tbl_journal_voucher', 'accounts_journal_voucher');
        }

          
            $successMsg = 'General Voucher Deleted Successfully';
            $this->session->set_flashdata('advance_voucher_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'tbl_journal_voucher',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'General Voucher Deleted');
              $this->general_model->insertData('log', $log_data); 
            redirect('general_voucher/general_voucher_list', 'refresh');
        } else {
            $errorMsg = 'General Voucher Delete Unsuccessful';
            $this->session->set_flashdata('advance_voucher_error',$errorMsg);
            redirect('general_voucher/general_voucher_list', 'refresh');
        }
    }

    public function get_trans_purpose_det(){ 
        $transaction_id = $this->input->post('trans_purpose');
        $voucher_row = $this->transaction_purpose_call_det($transaction_id);
        $data['input_type']  = $input_type = $voucher_row[0]->input_type;
        $data['voucher_type'] = $voucher_row[0]->voucher_type;
        $data['transaction_category'] = $voucher_row[0]->transaction_category;
        $data['transaction_purpose'] = $voucher_row[0]->transaction_purpose;
        
        
        echo json_encode($data);
    }


    public function get_trans_purpose_det_old(){ 
        $transaction_id = $this->input->post('trans_purpose');
        $voucher_row = $this->db->select("*")->from('tbl_transaction_purpose')->where('id', $transaction_id)->get()->result();
        $data['input_type']  = $input_type = $voucher_row[0]->input_type;
        $data['voucher_type'] = $voucher_row[0]->voucher_type;
        if($input_type == 'customer'){
            $data['payee'] = $this->customer_call();
        }elseif($input_type == 'suppliers'){
            $data['payee'] = $this->supplier_call();
        }elseif($input_type == 'financial year'){
            $data['payee'] = $this->get_fincialyear();
        }else{
            $data['payee'] = array();
        }
        
        echo json_encode($data);
    }

    public function get_transaction_purpose_det($transaction_id){        
        //$transaction_purpose = $this->db->select("*")->from('tbl_transaction_purpose')->where('id', $transaction_id)->get()->result();
       $transaction_purpose = $this->transaction_purpose_call_det($transaction_id);
        return $transaction_purpose;
    }
    
    function get_fincialyear(){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $this->db->select('year_id,from_date,to_date');                  
        $this->db->where('branch_id',$branch_id);
        $qry = $this->db->get('tbl_financial_year');
        $result = $qry->result_array();
        $finace_date = array();
        if(!empty($result)){
            $i = 0;
            foreach ($result as $key => $value) {

                $from_date = date('m-Y',strtotime($value['from_date']));
                $to_date = date('m-Y',strtotime($value['to_date']));
                $date = $from_date." ".$to_date;
                $finace_date[$i]['id'] = $value['year_id'];
                $finace_date[$i]['date'] = $date;
                $i++;
            }
        }

        return $finace_date;
    }


    function general_voucher_list(){
        $journal_voucher_module_id       = $this->config->item('journal_voucher_module');
        $data['module_id']               = $journal_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($journal_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);       

        if (!empty($this->input->post())){
            $columns             = array(
                    0 => 'gv.general_voucher_id',
                    1 => 'gv.voucher_date',
                    2 => 'gv.voucher_number',
                    3 => 'gv.reference_number',
                    4 => 'gv.receipt_amount' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order') [0]['column']];
            $dir                 = $this->input->post('order') [0]['dir'];
            $list_data           = $this->common->journal_voucher_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search') ['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search') ['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } 

            $send_data = array();

            if (!empty($posts)) {
                foreach ($posts as $post){
                    $general_voucher_id = $this->encryption_url->encode($post->journal_voucher_id);
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    $cols .= '<span> <a href="' . base_url('general_voucher/edit/') . $general_voucher_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit General Voucher"> <i class="fa fa-pencil"></i> </a></span>';                   

                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $general_voucher_id . '" data-path="general_voucher/delete_general_voucher" data-toggle="tooltip" data-placement="bottom" title="Delete General Voucher"> <i class="fa fa-trash-o"></i> </a></span>';

                    $cols .= '</div></div>';
                    
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    
                    $nestedData['voucher_date']   = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = $post->voucher_number;
                   $nestedData['option'] = $post->purpose_option;
                    $nestedData['amount']    = $this->precise_amount(str_replace(",", ",<br/>", $post->amount),2);
                    $nestedData['expense_amount']    = $this->precise_amount(str_replace(",", ",<br/>", $post->interest_expense_amount),2);
                    $nestedData['from_account']   = $post->from_account;
                    $nestedData['to_account']     = $post->to_account;

                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view('general_voucher/general_voucher_list', $data);
        }

    }

    public function createOption(){

        $user_id = $this->session->userdata('SESS_USER_ID');
        $this->db->select('*');
        $this->db->from('supplier');
        $res = $this->db->get();
        $result = $res->result();

        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type','suppliers');
        $sup = $this->db->get();
        $result_option = $sup->result();

       $option_array = array();

    $i = 1;
        foreach ($result as $key => $value) {   
                $suplier_name = $value->supplier_name;
                $suplier_id = $value->supplier_id;
                $branch_id = $value->branch_id;
                $date = date('Y-m-d');
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
        }

        if(!empty($option_array)){
            $table = "tbl_transaction_purpose_option";
            $this->db->insert_batch($table, $option_array);
        }
    }

    public function createOption_finance(){

        $user_id = $this->session->userdata('SESS_USER_ID');
        $this->db->select('*');
        $this->db->from('tbl_financial_year');
        $res = $this->db->get();
        $result = $res->result();

        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type','financial year');
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
                        $option_array[$i]['added_user_id'] = $user_id;
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

     public function get_parners(){ 
        $input_type = $this->input->post('type');
        $string = "*";
            $table = "tbl_shareholder";
            $where = array(
                "delete_status" => 0,
                "sharholder_type" => $input_type,
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID')
            );
            $order = array("sharholder_name" => "asc");
            $data = $this->general_model->getRecords($string, $table, $where, $order = "");
             echo json_encode($data);
    }
    
     public function getAllBank(){ 
            $data = $this->bank_account_call_new();
             echo json_encode($data);
    }
    
}