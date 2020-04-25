<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment_voucher extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();

    }

    public function index()
    {
        $payment_voucher_module_id         = $this->config->item('payment_voucher_module');
        $data['payment_voucher_module_id'] = $payment_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "view_privilege";
        $data['privilege']                 = $privilege;
        $section_modules                   = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        $access_common_settings = $section_modules['access_common_settings'];

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['email_module_id']     = $this->config->item('email_module');
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');
        $purchase_module_id = $this->config->item('purchase_module');
        $expense_bill_module_id = $this->config->item('expense_bill_module');

        if (!empty($this->input->post()))
        {
            $columns = array(
                0 => 'pv.payment_id',
                1 => 'pv.voucher_date',
                2 => 'pv.voucher_number',
                3 => 's.supplier_name',
                4 => 'pv.reference_number',
                5 => 'pv.receipt_amount'
            );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->payment_voucher_list_field_1($id=0, $order, $dir);
            $list_data['search'] = 'all';

            $totalData     = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();

            if (!empty($posts))
            {

                foreach ($posts as $post)
                {
                    $payment_id                              = $this->encryption_url->encode($post->payment_id);
                    $purchase_id = $this->encryption_url->encode($post->reference_id);
                    $nestedData['voucher_date']              = date('d-m-Y',strtotime($post->voucher_date));
                    $nestedData['voucher_number']            = '<a href="' . base_url('payment_voucher/view_details/') . $payment_id . '">' . $post->voucher_number . '</a>';
                    $nestedData['supplier']                  = $post->supplier_name;
                    $reference_number          = str_replace(",", ",<br/>", $post->reference_number);
                    if($post->reference_type == 'expense'){
                        if(in_array($expense_bill_module_id, $data['active_view'])){
                            $nestedData['reference_number'] = ' <a href="' . base_url('expense_bill/view/') . $purchase_id . '">' . $reference_number . '</a>';
                        }else{
                            $nestedData['reference_number'] = $reference_number;
                        }
                    }else{
                        if(in_array($purchase_module_id, $data['active_view'])){
                            $nestedData['reference_number'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $reference_number . '</a>';
                        }else{
                            $nestedData['reference_number'] = $reference_number;
                        }
                    }
                    $amount =  str_replace(",", ",<br/> ", $post->imploded_receipt_amount);
                    $nestedData['amount'] = $this->precise_amount($amount,2);
                    $nestedData['currency_converted_amount'] = $this->precise_amount($post->converted_receipt_amount, $access_common_settings[0]->amount_precision);
                    $nestedData['to_account']                = $post->to_account;
                    $nestedData['added_user']                = $post->first_name . ' ' . $post->last_name;
					$cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    if (in_array($payment_voucher_module_id, $data['active_view'])){
                        $cols .= '<span><a data-toggle="tooltip" data-placement="bottom" href="' . base_url('payment_voucher/view_details/') . $payment_id . '"  title="View Payment Voucher" class="btn btn-app"><i class="fa fa-eye"></i></a></span>';
                        $cols .= '<span><a data-toggle="tooltip" data-placement="bottom" href="' . base_url('payment_voucher/pdf/') . $payment_id . '" target="_blank" title="Download PDF" class="btn btn-app"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }
                    if (in_array($payment_voucher_module_id, $data['active_edit']) && $post->voucher_status != "2")
                    {
                        $cols .= '<span><a data-toggle="tooltip" data-placement="bottom" href="' . base_url('payment_voucher/edit/') . $payment_id . '" title="Edit Payment Voucher" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }
                    /*if (in_array($data['email_module_id'], $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $cols .= '<span><a href="' . base_url('payment_voucher/email/') . $payment_id . '" data-toggle="tooltip" data-placement="bottom" title="Email Payment Voucher" class="btn btn-app"><i class="fa fa-envelope-o"></i></a></span>';
                        }

                    } */

                    /*if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY') && $post->voucher_status != "2")
                    {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" ><a data-id="' . $payment_id . '" data-path="payment_voucher/convert_currency" data-currency_code="" data-grand_total="' . $this->precise_amount($post->receipt_amount, $access_common_settings[0]->amount_precision) . '" data-receipt_amount="' . $this->precise_amount($post->imploded_receipt_amount, $access_common_settings[0]->amount_precision) . '" href="#" data-toggle="tooltip" data-placement="bottom" title="Convert Currency" class="btn btn-app"><i class="fa fa-exchange"></i></a></span>';
                    }*/

                    if (in_array($payment_voucher_module_id, $data['active_delete']))
                    {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $payment_id . '" data-path="payment_voucher/delete" ><a  href="#" data-toggle="tooltip" data-placement="bottom" title="Delete Payment Voucher" class="btn btn-app"><i class="fa fa-trash-o"></i></a></span>';
                    }

                    $cols .= '</div></div>';
 					$nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    
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
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('payment_voucher/list', $data);
        }

    }

    public function add()
    {
        $receipt_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "add_privilege";
        $data['privilege']         = "add_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $ledger_title = 'Acc@{{BANK}}';
        if(!empty($bank_led)){
            $ledger_title = $bank_led->ledger_name;
        }
        $data['default_ledger_title'] = $ledger_title;

        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        if ($this->input->post('payment_type_check') != "")
        {
            $data['payment_type_check'] = $this->input->post('payment_type_check');
        }
        else
        {
            $data['payment_type_check'] = 'purchase';
        }

        if ($this->session->userdata('cat_type') != "" && $this->session->userdata('cat_type') != null && $this->session->userdata('cat_type') == "suppliers")
        {
            $data['payment_type_check'] = 'purchase';
        }

        if ($this->session->userdata('cat_type') != "" && $this->session->userdata('cat_type') != null && $this->session->userdata('cat_type') == "expense")
        {
            $data['payment_type_check'] = 'expense';
        }
       
        $data['supplier'] = $this->supplier_call();
        $data['bank_account']   = $this->bank_account_call_new();
        $data['currency']       = $this->currency_call();
        $access_settings        = $data['access_settings'];
        $primary_id             = "payment_id";
        $table_name             = $this->config->item('payment_voucher_table');
        $date_field_name        = "voucher_date";
        $current_date           = date('Y-m-d');
        $data['voucher_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('payment_voucher/add', $data);

    }

    public function get_purchase_invoice_number()
    {
        $supplier_id    = $this->input->post('supplier_id');
        $currency_id    = $this->input->post('currency_id');
        $reference_type = $this->input->post('reference_type');
        $balance        = 0;

        if ($reference_type == "expense")
        {
            $invoice_data = $this->common->get_expense_supplier_invoice_number_field($supplier_id, $balance, $currency_id);
        }
        else
        {
            $invoice_data = $this->common->get_supplier_invoice_number_field($supplier_id, $balance, $currency_id);
        }

        $data['data'] = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);
        echo json_encode($data);

    }

    public function get_purchase_details()
    {
        $purchase_id    = $this->input->post('purchase_id');
        $reference_type = $this->input->post('reference_type');

        if ($reference_type == "expense")
        {
            $string = "eb.expense_bill_id as purchase_id,eb.expense_bill_grand_total as purchase_grand_total,eb.expense_bill_invoice_number as purchase_invoice_number,eb.expense_bill_paid_amount as purchase_paid_amount,'0' as credit_note_amount,'0' as debit_note_amount";
            $data   = $this->general_model->getRecords($string, 'expense_bill eb', array(
                'eb.expense_bill_id' => $purchase_id));
        }
        else
        {
            $data = $this->general_model->getRecords('*', 'purchase', array(
                'purchase_id' => $purchase_id));
        }
        echo json_encode($data);

    }

    public function add_payment(){
        /*echo "<pre>";
        print_r($this->input->post());
        exit();*/
        $all_reference = $this->input->post('invoice_data');
        $invoice_data = json_decode($all_reference,true);
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "add_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['payment_voucher_module_id'] = $payment_voucher_module_id;
        $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['accounts_module_id']     = $this->config->item('accounts_module');
        $data['notes_module_id']        = $this->config->item('notes_module');
        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $access_settings = $section_modules['access_settings'];
        $currency = $this->input->post('currency_id');

        if ($access_settings[0]->invoice_creation == "automatic"){

            if ($this->input->post('voucher_number') != $this->input->post('voucher_number_old')){
                $primary_id      = "payment_id";
                $table_name      = $this->config->item('payment_voucher_table');
                $date_field_name = "voucher_date";
                $current_date    = date('Y-m-d',strtotime($this->input->post('voucher_date')));
                $voucher_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else{
                $voucher_number = $this->input->post('voucher_number');
            }
        }else{
            $voucher_number = $this->input->post('voucher_number');
        }

        $payment_amount_arr  = $this->input->post('payment_amount');
        $payment_grand_total = 0;

        $reference_numbers = $reference_number_text = $payment_amount =$remaining_amount= $paid_amount=$invoice_total = array();
        
        if(!empty($invoice_data)){
           
            foreach ($invoice_data as $key => $value) {
                /*$invoice_data[$key]['paid_amount'] +=(@$value['payment_amount'] ? $value['payment_amount'] : 0);*/
                if(@$invoice_data[$key]['paid_amount']){
                    $invoice_data[$key]['paid_amount'] +=(@$value['payment_total_paid'] ? $value['payment_total_paid'] : 0);
                }else{
                    $invoice_data[$key]['paid_amount'] = 0;
                }
                if($value['reference_number'] != 'excess_amount'){
                    array_push($reference_number_text, $value['reference_number_text']);
                    array_push($remaining_amount, $value['pending_amount']);
                    array_push($paid_amount, $value['paid_amount']);
                    array_push($invoice_total, $value['invoice_total']);
                }
                array_push($reference_numbers, $value['reference_number']);
                array_push($payment_amount, $value['payment_amount']);
            }
        }
       
        $payment_grand_total = $this->input->post('total_payment_amount');

        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode          = $bank_acc_payment_mode[0];
            /*$ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode[0]));
            $ledger_from = $ledger_bank_acc[0]->ledger_id;*/
            $from_acc    = $bank_acc_payment_mode[1];
        } else{
            $ledger_from  = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));
            $payment_mode = $this->input->post('payment_mode');
            $from_acc     = $this->input->post('payment_mode');
        }

        $supplier = $this->general_model->getRecords('ledger_id,supplier_name', 'supplier', array(
            'supplier_id' => $this->input->post('supplier')));

        $supplier_name = $supplier[0]->supplier_name;
        $supplier_ledger_id = $supplier[0]->ledger_id;
        

        /*$ledger_to   = $supplier[0]->ledger_id;*/
        $cheque_date = ($this->input->post('cheque_date') != '' ? date('Y-m-d', strtotime($this->input->post('cheque_date'))) : '');

        if (!$cheque_date) {
            $cheque_date = null;
        }

        $payment_data = array(
            "voucher_date"            => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
            "voucher_number"          => $voucher_number,
            "party_id"                => $this->input->post('supplier'),
            "party_type"              => 'supplier',
            "reference_id"            => implode(",", $reference_numbers),
            "reference_type"          => $this->input->post('reference_type'),
            "reference_number"        => implode(",", $reference_number_text),
            "from_account"            => $from_acc,
            "to_account"              => 'supplier-' . $supplier[0]->supplier_name,
            "imploded_receipt_amount" => implode(",", $payment_amount),
            "invoice_balance_amount"  => implode(",", $remaining_amount),
            "invoice_paid_amount"     => implode(",", $paid_amount),
            "invoice_total"           => implode(",", $invoice_total),
            "receipt_amount"          => $payment_grand_total,
            "payment_mode"            => $payment_mode,
            "payment_via"             => $this->input->post('payment_via'),
            "reff_number"             => $this->input->post('ref_number'),
            "financial_year_id"       => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "bank_name"               => $this->input->post('bank_name'),
            "cheque_number"           => $this->input->post('cheque_number'),
            "cheque_date"             => $cheque_date,
            "description"             => $this->input->post('description'),
            "added_date"              => date('Y-m-d'),
            "added_user_id"           => $this->session->userdata('SESS_USER_ID'),
            "branch_id"               => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id"             => $this->input->post('currency_id'),
            "updated_date"            => "",
            "updated_user_id"         => "",
            "note1"                   => $this->input->post('note1'),
            "note2"                   => $this->input->post('note2'));

        /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $payment_data['converted_payment_amount']          = $payment_grand_total;
            $payment_data['imploded_converted_payment_amount'] = $payment_grand_total;
        }else {
            $payment_data['converted_payment_amount']          = 0;
            $payment_data['imploded_converted_payment_amount'] = 0;
        }*/

        if ($payment_mode == "cash"){
            $payment_data['voucher_status'] = "0";
        } else{
            $payment_data['voucher_status'] = "1";
        }

        $data_main = array_map('trim', $payment_data);

        $payment_voucher_table = $this->config->item('payment_voucher_table');
        $sub_payment_total = 0;
        if ($payment_id = $this->general_model->insertData($payment_voucher_table, $data_main)) {
            /*$purchase_id_data       = explode(",", $data_main['reference_id']);
            $payment_amount_data = explode(",", $data_main['imploded_payment_amount']);*/
            $reference_data = array();
            $data_main['total_excess_amount'] = 0;
            foreach ($invoice_data as $key7 => $value7) {
                $purchase_paid_amount = $value7['paid_amount'];
                if(!isset($value7['payment_total_paid']))  $value7['payment_total_paid'] = 0;
                $reference_type = $data_main['reference_type'];
                if($value7['reference_number'] == 'excess_amount') $reference_type = 'excess_amount';
                $reference_data[] = array('payment_id' => $payment_id,
                                        'reference_id' => $value7['reference_number'],
                                        'reference_type' => $reference_type,
                                        'Invoice_total_paid' => ($purchase_paid_amount - $value7['payment_total_paid']),
                                        'Invoice_pending' => (@$value7['pending_amount'] ? $value7['pending_amount'] : 0) ,
                                        'payment_amount' => (@$value7['payment_amount'] ? $value7['payment_amount'] : 0) ,
                                        'exchange_gain_loss' => (@$value7['gain_loss_amount'] ? $value7['gain_loss_amount'] : 0) ,
                                        'exchange_gain_loss_type' => (@$value7['gain_loss_amount_icon'] ? $value7['gain_loss_amount_icon'] : 0) ,
                                        'discount' => (@$value7['discount'] ? $value7['discount'] : 0) ,
                                        'other_charges' => (@$value7['other_charges'] ? $value7['other_charges'] : 0) ,
                                        'round_off' => (@$value7['round_off'] ? $value7['round_off'] : 0) ,
                                        'round_off_icon' => (@$value7['icon_round_off'] ? $value7['icon_round_off'] : 0) ,
                                        'payment_total_paid' => (@$value7['payment_total_paid'] ? $value7['payment_total_paid'] : 0) 
                                    );

                if ($data_main['reference_type'] == 'purchase' && $value7['reference_number'] != 'excess_amount') {

                    $paid_amount       = array('purchase_paid_amount' => $purchase_paid_amount);
                    if($value7['pending_amount'] <= 0 ){
                        $paid_amount       = array('purchase_paid_amount' => $purchase_paid_amount,'is_edit' => '0');
                    }

                    $where = array('purchase_id' => $value7['reference_number']);
                    $purchase_table = $this->config->item('purchase_table');
                    $this->general_model->updateData($purchase_table, $paid_amount, $where);
                    $successMsg = 'Payment Voucher Added Successfully';
                    $this->session->set_flashdata('payment_voucher_purchase_success',$successMsg);

                } elseif($data_main['reference_type'] == 'expense'  && $value7['reference_number'] != 'excess_amount'){
                    
                    $paid_amount       = array('expense_bill_paid_amount' => $purchase_paid_amount);

                    $where = array('expense_bill_id' => $value7['reference_number']);
                    $purchase_table = $this->config->item('expense_bill_table');
                    $this->general_model->updateData($purchase_table, $paid_amount, $where);
                    $successMsg = 'Payment Voucher Added Successfully';
                    $this->session->set_flashdata('payment_voucher_success',$successMsg);
                } elseif($value7['reference_number'] == 'excess_amount'){
                    $excess_purchase_id = $this->input->post('excess_purchase_id');
                    $data_main['total_excess_amount'] += $value7['payment_amount'];
                    /*if($excess_purchase_id){
                        $excess = array('purchase_id' => $excess_purchase_id,
                                        'payment_id' => $payment_id,
                                        'excess_amount' => $value7['payment_amount'],
                                        'created_at' => date('Y-m-d'),
                                        'created_by' => $this->session->userdata('SESS_USER_ID'));

                        $this->db->insert('purchase_excess_amount',$excess);
                    }*/
                }
                $sub_payment_total += $value7['payment_amount'];
            }

            $this->db->insert_batch('payment_invoice_reference',$reference_data);
            $successMsg = 'Payment Voucher Added Successfully';
            $this->session->set_flashdata('payment_voucher_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $payment_id,
                'table_name'        => $payment_voucher_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'payment Voucher Inserted');
            $data_main['payment_id'] = $payment_id;
            $data_main['sub_payment_total'] = $sub_payment_total;
            $log_table               = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);

            if (in_array($data['accounts_module_id'], $data['active_add'])){

                if (in_array($data['accounts_sub_module_id'], $data['access_sub_modules'])){
                    /*$this->voucher_entry($payment_id, $ledger_from, $ledger_to, $data_main['payment_amount'], "add", $currency);*/
                    $this->VoucherEntry($data_main,$reference_data,$supplier_name, $supplier_ledger_id , "add");
                }
            }

            if ($this->session->userdata('cat_type') != "" && $this->session->userdata('cat_type') != null && $this->session->userdata('cat_type') == 'supplier' && $payment_mode != "cash"){
                $this->session->unset_userdata('cat_type');

                if ($currency == $this->session->userdata('SESS_DEFAULT_CURRENCY')){
                    redirect('bank_statement/bank_group', 'refresh');
                } else{
                    redirect('payment_voucher', 'refresh');
                }
            }
            if($this->input->post('redirect') != ''){
                redirect($this->input->post('redirect'), 'refresh');
            }else{
                redirect('payment_voucher', 'refresh');
            }
            
        }else{
            $errorMsg = 'Payment Voucher Add Unsuccessful';
            $this->session->set_flashdata('payment_voucher_error',$errorMsg);
            redirect('payment_voucher', 'refresh');
        }
    }

    public function VoucherEntry($data_main,$reference_data,$supplier_name,$supplier_ledger_id=0,$operation){
        $vouchers = array();
        $payment_id = $data_main['payment_id'];
        $exchang_gain = $exchang_loss = $discount = $other_charges = $round_off_plus = $round_off_minus = 0;
        $payment_ledger = $this->config->item('payment_ledger');

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
       
        if(!$supplier_ledger_id){
            $supplier_ledger_id = $payment_ledger['SUPPLIER'];
            $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                
            $supplier_ary = array(
                            'ledger_name' => $supplier_name,
                            'second_grp' => '',
                            'primary_grp' => 'Sundry Creditors',
                            'main_grp' => 'Current Liabilities',
                            'default_ledger_id' => 0,
                            'default_value' => $supplier_name,
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
        /*$supplier_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => $supplier_name,
                                            'subgrp_2' => 'Sundry Creditors',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Current Liabilities',
                                            'amount' =>  0
                                        ));*/
        $vouchers[] = array(
            'payment_voucher_id' => $data_main['payment_id'],
            'ledger_from'        => $supplier_ledger_id,
            'ledger_to'          => $supplier_ledger_id,
            'ledger_id'          => $supplier_ledger_id,
            'voucher_amount'     => $data_main['receipt_amount'] + $discount + $other_charges + $exchang_loss - $exchang_gain + $round_off_minus - $round_off_plus - $data_main['total_excess_amount'],
            'dr_amount'          => $data_main['receipt_amount'] + $discount + $other_charges + $exchang_loss - $exchang_gain + $round_off_minus - $round_off_plus - $data_main['total_excess_amount'],
            'cr_amount'          => 0);

        $payment_ledger_title = $data_main['payment_mode'];


        if ($data_main['payment_mode'] != "cash" && $data_main['payment_mode'] != "bank" && $data_main['payment_mode'] != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $data_main['payment_mode']);
            $payment_ledger_title          = $bank_acc_payment_mode[0];
            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_ledger_title));
            $bank_id =  $ledger_bank_acc[0]->ledger_id;
            $ledger_from = $ledger_bank_acc[0]->ledger_id;
            /*$from_acc    = $bank_acc_payment_mode[1];*/
        }else{

            if(strtolower($payment_ledger_title) == 'bank' ) $payment_ledger_title = 'Bank A/C';
            $default_payment_id = $payment_ledger['Other_Payment'];
            if (strtolower($data_main['payment_mode']) == "cash"){
                $default_payment_id = $payment_ledger['Cash_Payment'];
            }

            $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
                    
            $default_payment_ary = array(
                            'ledger_name' => $payment_ledger_title,
                            'second_grp' => '',
                            'primary_grp' => 'Cash & Cash Equivalent',
                            'main_grp' => 'Current Assets',
                            'default_value' => $payment_ledger_title,
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($default_payment_name)){
                $default_led_nm = $default_payment_name->ledger_name;
                $default_payment_ary['ledger_name'] = str_ireplace('{{PAYMENT_MODE}}',$payment_ledger_title, $default_led_nm);
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
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $bank_id,
                'ledger_to'          => $bank_id,
                'ledger_id'          => $bank_id,
                'voucher_amount'     => $data_main['receipt_amount'],
                'dr_amount'          => 0,
                'cr_amount'          => $data_main['receipt_amount']);

        if ($data_main['total_excess_amount'] > 0){
            $default_exc_id = $payment_ledger['ExcessPay'];
            $exc_ledger_name = $this->ledger_model->getDefaultLedgerId($default_exc_id);
           
            $exc_ary = array(
                            'ledger_name' => 'Excess Pay',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Current Assets',
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
            /*$bank_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => 'Excess Pay',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Current Assets',
                                                'amount' =>  0
                                            ));*/

            $vouchers[] = array(
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $exchange_loss_id,
                'ledger_to'          => $supplier_ledger_id,
                'ledger_id'          => $exchange_loss_id,
                'voucher_amount'     => $data_main['total_excess_amount'],
                'dr_amount'          => $data_main['total_excess_amount'],
                'cr_amount'          => 0
            );
        }
     
        if($exchang_loss > 0){
            $default_exc_id = $payment_ledger['ExcessGain'];
            $exc_ledger_name = $this->ledger_model->getDefaultLedgerId($default_exc_id);
           
            $exc_ary = array(
                            'ledger_name' => 'Exchange Gain',
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
                                                'ledger_name' => 'Exchange Gain',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $exchange_loss_id,
                'ledger_to'          => $exchange_loss_id,
                'ledger_id'          => $exchange_loss_id,
                'voucher_amount'     => $exchang_loss,
                'dr_amount'          => 0,
                'cr_amount'          => $exchang_loss);
        }
        
        if($exchang_gain > 0){
            $default_exc_id = $payment_ledger['ExcessLoss'];
            $exc_ledger_name = $this->ledger_model->getDefaultLedgerId($default_exc_id);
           
            $exc_ary = array(
                            'ledger_name' => 'Exchange Loss',
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
                                                'ledger_name' => 'Exchange Loss',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Incomes',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $exchange_gain_id,
                'ledger_to'          => $exchange_gain_id,
                'ledger_id'          => $exchange_gain_id,
                'voucher_amount'     => $exchang_gain,
                'dr_amount'          => $exchang_gain,
                'cr_amount'          => 0);
        }
        
        if($discount > 0){
            $default_dis_id = $payment_ledger['Discount'];
            $dis_ledger_name = $this->ledger_model->getDefaultLedgerId($default_dis_id);
           
            $dis_ary = array(
                            'ledger_name' => 'Discount Received',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Indirect Incomes',
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
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $discount_id,
                'ledger_to'          => $discount_id,
                'ledger_id'          => $discount_id,
                'voucher_amount'     => $discount,
                'dr_amount'          => 0,
                'cr_amount'          => $discount);
        }
       
        if($other_charges > 0){
            $default_other_id = $payment_ledger['Other_Charges'];
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
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $other_charges_id,
                'ledger_to'          => $other_charges_id,
                'ledger_id'          => $other_charges_id,
                'voucher_amount'     => $other_charges,
                'dr_amount'          => 0,
                'cr_amount'          => $other_charges);
        }
       
        if($round_off_plus > 0){
            $default_roundoff_id = $payment_ledger['RoundOff_Received'];
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
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $round_off_plus_id,
                'ledger_to'          => $round_off_plus_id,
                'ledger_id'          => $round_off_plus_id,
                'voucher_amount'     => $round_off_plus,
                'dr_amount'          => $round_off_plus,
                'cr_amount'          => 0);
        }
        
        if($round_off_minus > 0){
            $default_roundoff_id = $payment_ledger['RoundOff_Given'];
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
                                                'ledger_name' => 'ROUND OFF',
                                                'subgrp_2' => '',
                                                'subgrp_1' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' =>  0
                                            ));*/
            $vouchers[] = array(
                'payment_voucher_id' => $data_main['payment_id'],
                'ledger_from'        => $round_off_minus_id,
                'ledger_to'          => $round_off_minus_id,
                'ledger_id'          => $round_off_minus_id,
                'voucher_amount'     => $round_off_minus,
                'dr_amount'          => 0,
                'cr_amount'          => $round_off_minus);
        }

       
        if(!empty($vouchers)){

            if ($operation == "add"){
                foreach ($vouchers as $key => $value){
                    $this->db->insert('accounts_payment_voucher', $value); 
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
                /*$this->db->insert_batch('accounts_payment_voucher',$vouchers);
                $this->general_model->addVouchers($table , $reference_key , $reference_table , $headers , $vouchers);*/
            
            }elseif ($operation == "edit"){
                
                $old_voucher_items = $this->general_model->getRecords('*', 'accounts_payment_voucher', array('payment_voucher_id' => $payment_id,'delete_status'      => 0));
                /*echo "<pre>";
                print_r($old_voucher_items);
                print_r($vouchers);
                 exit();*/
                $old_purchase_ledger_ids = $this->getValues($old_voucher_items,'ledger_id');
                $not_deleted_ids = array();
                foreach ($vouchers as $key => $value) {
                    if (($led_key = array_search($value['ledger_id'], $old_purchase_ledger_ids)) !== false) {
                        unset($old_purchase_ledger_ids[$led_key]);
                        $accounts_payment_id = $old_voucher_items[$led_key]->accounts_payment_id;
                        array_push($not_deleted_ids,$accounts_payment_id );
                        $value['payment_voucher_id'] = $payment_id;
                        $value['delete_status']    = 0;
                        $where                     = array('payment_voucher_id' => $accounts_payment_id );
                        $post_data = array('data' => $value,
                                            'where' => $where,
                                            'voucher_date' => $data_main['voucher_date'],
                                            'table' => 'payment_voucher',
                                            'sub_table' => 'accounts_payment_voucher',
                                            'primary_id' => 'payment_id',
                                            'sub_primary_id' => 'payment_voucher_id'
                                        );
                        $this->general_model->updateBunchVoucherCommon($post_data);
                        $this->general_model->updateData('accounts_payment_voucher' , $value , array('accounts_payment_id' => $accounts_payment_id ));
                    }else{
                        $value['payment_voucher_id'] = $payment_id;
                        $table                     = 'accounts_payment_voucher';
                        $this->general_model->insertData($table , $value);
                    }
                }
                
                if(!empty($old_voucher_items)){
                    $revert_ary = array();
                    
                    foreach ($old_voucher_items as $key => $value) {
                        if(!in_array($value->accounts_payment_id, $not_deleted_ids)){
                            $revert_ary[] = $value;
                            $table      = 'accounts_payment_voucher';
                            $where      = array('accounts_payment_id' => $value->accounts_payment_id );
                            $purchase_data = array('delete_status' => 1 );
                            $this->general_model->updateData($table , $purchase_data , $where);
                        }
                    }
                    
                    if(!empty($revert_ary)) $this->general_model->revertLedgerAmount($revert_ary,$data_main['voucher_date']);
                }
                /* Delete old ledgers */

                /*$this->db->where('payment_voucher_id',$data_main['payment_id']);
                $this->db->delete('accounts_payment_voucher');*/
                /* Add New ledgers */
                /*$this->db->insert_batch('accounts_payment_voucher',$vouchers);*/
                /*if ($accounts_payment){
                    $this->general_model->updateData('accounts_payment_voucher', $data1, array(
                        'accounts_payment_id' => $accounts_payment[0]->accounts_payment_id));
                    $this->general_model->updateData('accounts_payment_voucher', $data2, array(
                        'accounts_payment_id' => $accounts_payment[1]->accounts_payment_id));
                }*/
            }
        }
    }

    public function edit($id){
        $id                        = $this->encryption_url->decode($id);
       
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "edit_privilege";
        $data['privilege']         = "edit_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /* bank default ledger title for payment mode*/
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $ledger_title = 'Acc@{{BANK}}';
        if(!empty($bank_led)){
            $ledger_title = $bank_led->ledger_name;
        }
        $data['default_ledger_title'] = $ledger_title;
        
        $data['payment_voucher_module_id'] = $payment_voucher_module_id;
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['bank_account_module_id']    = $this->config->item('bank_account_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        $data['notes_module_id']           = $this->config->item('notes_module');

        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        /*$data['supplier'] = $this->supplier_call();*/

        /*$data['currency'] = $this->currency_call();*/

        $this->db->select('r.*,rv.reference_id,rv.exchange_gain_loss,rv.exchange_gain_loss_type,rv.discount,rv.other_charges,rv.round_off,rv.round_off_icon,rv.payment_amount as invoice_payment_amount,rv.payment_total_paid,rv.Invoice_pending,rv.Invoice_total_paid,c.supplier_name,c.supplier_mobile');
        $this->db->from('payment_voucher r');
        $this->db->join('payment_invoice_reference rv','r.payment_id=rv.payment_id','left');
        $this->db->join('supplier c','r.party_id=c.supplier_id','left');
        $this->db->where('r.payment_id',$id);
        $d = $this->db->get();
        
        $data['data'] = $d->result();
        /*$data['data']     = $this->general_model->getRecords('*', 'payment_voucher', array(
            'payment_id' => $id));*/
        $service = 0;
        $bank_exist = 0;
        $reference_type = $data['data'][0]->reference_type;
        if(!empty($data['data'])){
            if ($data['data'][0]->payment_mode != "" && $data['data'][0]->payment_mode != "cash" && $data['data'][0]->payment_mode != "other payment mode" && $data['data'][0]->payment_mode != "bank"){
                $bank_exist = 1;
            }
            $supplier_id = $data['data'][0]->party_id;
            if($reference_type == 'purchase'){

                $this->db->select('*,supplier_payable_amount as purchase_grand_total');
                $this->db->where('purchase_party_id',$supplier_id);
                $s_data = $this->db->get('purchase');
                $s_data = $s_data->result();
                $data['purchase_data'] = $s_data;
                $purchase_data = array();
                foreach ($s_data as $key => $value) {
                    $purchase_data['purchase_'.$value->purchase_id] = $value;
                }
            }else{
                $this->db->select('*,e.expense_bill_id as purchase_id,e.supplier_receivable_amount as supplier_payable_amount,expense_bill_paid_amount as purchase_paid_amount,expense_bill_invoice_number as purchase_invoice_number');
                $this->db->where('expense_bill_payee_id',$supplier_id);
                $s_data = $this->db->get('expense_bill e');
                $s_data = $s_data->result();

                $data['purchase_data'] = $s_data;
                $purchase_data = array();
                foreach ($s_data as $key => $value) {
                    $purchase_data['purchase_'.$value->expense_bill_id] = $value;
                }
            }
        }

        $purchase_data_1 = array();
       
        if(!empty($purchase_data)){
            foreach ($data['data'] as $key => $value) {
                $is_edit = 0;
                if(array_key_exists('purchase_'.$value->reference_id, $purchase_data)){
                    
                    $purchase_invoice = $purchase_data['purchase_'.$value->reference_id];
                    $this->db->select('payment_id,payment_amount,payment_total_paid');
                    $this->db->from('payment_invoice_reference');
                    $this->db->where('reference_id',$value->reference_id);
                    $this->db->order_by('payment_invoice_id','DESC');
                    $this->db->limit(1);
                    $last_re = $this->db->get();
                    $last_payment = $last_re->result();
                    /*if($reference_type == 'purchase'){*/
                        $invoice_total = $purchase_invoice->supplier_payable_amount;
                        /*$paid_amount = ($purchase_invoice->purchase_paid_amount - $last_payment[0]->payment_amount);
                        $pending_amount = $invoice_total - $paid_amount - $value->receipt_amount;*/
                        $paid_amount = ($purchase_invoice->purchase_paid_amount - $last_payment[0]->payment_total_paid);
                        if($value->exchange_gain_loss_type == 'minus'){
                            $paid_amount -= $value->exchange_gain_loss;
                        }

                        if($value->round_off_icon == 'plus'){
                            $paid_amount -= $value->round_off;
                        }

                        $pending_amount = $invoice_total - $paid_amount - $value->payment_total_paid;
                        $data['data'][$key]->invoice_total = $invoice_total;
                        $data['data'][$key]->invoice_paid_amount = $paid_amount;
                        $data['data'][$key]->invoice_pending = $pending_amount;
                        $data['data'][$key]->reference_number = $purchase_invoice->purchase_invoice_number;
                        
                        if($last_payment[0]->payment_id == $value->payment_id && $pending_amount > 0)$is_edit = 1;

                        if($is_edit == 0){
                            $data['data'][$key]->invoice_paid_amount = $value->Invoice_total_paid;
                            $data['data'][$key]->invoice_pending = $value->Invoice_pending;
                        }
                        
                    /*}else{
                        $invoice_total = $purchase_invoice->supplier_receivable_amount;
                        $paid_amount = ($purchase_invoice->purchase_paid_amount - $last_payment[0]->payment_amount);
                        $pending_amount = $invoice_total - $paid_amount - $value->receipt_amount;
                        $data['data'][$key]->invoice_total = $invoice_total;
                        $data['data'][$key]->invoice_paid_amount = $paid_amount;
                        $data['data'][$key]->invoice_pending = $pending_amount;
                        $data['data'][$key]->reference_number = $purchase_invoice->purchase_invoice_number;
                        $is_edit = 0;
                        if($last_payment[0]->payment_id == $value->payment_id && $pending_amount > 0)$is_edit = 1;
                        $data['data'][$key]->is_edit = $is_edit;
                    }*/
                }
                $data['data'][$key]->is_edit = $is_edit;
            }
        }
        
        $data['invoice_data'] = $purchase_data_1;
        $data['bank_exist'] = $bank_exist;

        if (in_array($data['bank_account_module_id'], $data['active_modules']) || $bank_exist > 0){
            $data['bank_account'] = $this->bank_account_call_new();
        }

        $data['payment_id'] = $id;
        /*echo "<pre>";
        print_r($data);
        exit();*/
        $this->load->view('payment_voucher/edit', $data);
    }

    public function edit_payment(){
        $all_reference = $this->input->post('invoice_data');
        $invoice_data = json_decode($all_reference,true);
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "edit_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['payment_voucher_module_id'] = $payment_voucher_module_id;
        $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['accounts_module_id']     = $this->config->item('accounts_module');
        $data['notes_module_id']        = $this->config->item('notes_module');
        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $access_settings = $section_modules['access_settings'];
        $currency = $this->input->post('currency_id');

        if ($access_settings[0]->invoice_creation == "automatic") {

            if ($this->input->post('voucher_number') != $this->input->post('voucher_number_old')) {
                $primary_id      = "payment_id";
                $table_name      = $this->config->item('payment_voucher_table');
                $date_field_name = "voucher_date";
                $current_date    = date('Y-m-d',strtotime($this->input->post('voucher_date')));
                $voucher_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else{
                $voucher_number = $this->input->post('voucher_number');
            }
        }else{
            $voucher_number = $this->input->post('voucher_number');
        }

        $payment_id          = $this->input->post('payment_id');
        $payment_amount_arr  = $this->input->post('receipt_amount');
        $payment_grand_total = 0;

        /*foreach ($payment_amount_arr as $key9 => $value9){
            $payment_grand_total = bcadd($payment_grand_total, $value9, $section_modules['access_common_settings'][0]->amount_precision);
        }*/

        $payment_amount_arr  = $this->input->post('receipt_amount');
        $payment_grand_total = 0;

        $reference_numbers = $reference_number_text = $payment_amount =$remaining_amount= $paid_amount=$invoice_total = array();

        if(!empty($invoice_data)){
           
            foreach ($invoice_data as $key => $value) {
                if(@$invoice_data[$key]['paid_amount']){
                    $invoice_data[$key]['paid_amount'] +=(@$value['payment_total_paid'] ? $value['payment_total_paid'] : 0);
                }else{
                    $invoice_data[$key]['paid_amount'] = 0;
                }
                
                if($value['reference_number'] != 'excess_amount'){
                    array_push($reference_number_text, $value['reference_number_text']);
                    array_push($remaining_amount, $value['pending_amount']);
                    array_push($paid_amount, $value['paid_amount']);
                    array_push($invoice_total, $value['invoice_total']);
                }
                array_push($reference_numbers, $value['reference_number']);
                array_push($payment_amount, $value['payment_amount']);
            }
        }
       
        $payment_grand_total = $this->input->post('total_payment_amount');

        $payment_amount_old           = $this->input->post('payment_amount_old');
        $converted_payment_amount_old = $this->input->post('converted_payment_amount_old');

        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode"){
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode          = $bank_acc_payment_mode[0];
            /*$ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode[0]));
            $ledger_from = $ledger_bank_acc[0]->ledger_id;*/
            $from_acc    = $bank_acc_payment_mode[1];
        } else {
            $ledger_from  = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));
            $payment_mode = $this->input->post('payment_mode');
            $from_acc     = $this->input->post('payment_mode');
        }

        $supplier = $this->general_model->getRecords('ledger_id,supplier_name', 'supplier', array(
            'supplier_id' => $this->input->post('supplier')));
        $ledger_to   = $supplier[0]->ledger_id;
        $supplier_name = $supplier[0]->supplier_name;
        $supplier_ledger_id = $supplier[0]->ledger_id;
        $cheque_date = date('Y-m-d', strtotime($this->input->post('cheque_date')));

        if (!$cheque_date){
            $cheque_date = null;
        }
        $payment_data = array(
            "voucher_date"            => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
            "voucher_number"          => $voucher_number,
            "party_id"                => $this->input->post('supplier'),
            "party_type"              => 'supplier',
            "reference_id"            => implode(",", $reference_numbers),
            "reference_type"          => $this->input->post('reference_type'),
            "reference_number"        => implode(",", $reference_number_text),
            "from_account"            => $from_acc,
            "to_account"              => 'supplier-' . $supplier[0]->supplier_name,
            /*"imploded_payment_amount" => implode(",", $payment_amount),*/
            "invoice_balance_amount"  => implode(",", $remaining_amount),
            "invoice_paid_amount"     => implode(",", $paid_amount),
            "invoice_total"           => implode(",", $invoice_total),
            "payment_mode"            => $payment_mode,
            "receipt_amount"          => $payment_grand_total,
            "financial_year_id"       => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "payment_via"             => $this->input->post('payment_via'),
            "reff_number"             => $this->input->post('reff_number'),
            "bank_name"               => $this->input->post('bank_name'),
            "cheque_number"           => $this->input->post('cheque_number'),
            "cheque_date"             => $cheque_date,
            "description"             => $this->input->post('description'),
            "updated_date"            => date('Y-m-d'),
            "updated_user_id"         => $this->session->userdata('SESS_USER_ID'),
            "note1"                   => $this->input->post('note1'),
            "note2"                   => $this->input->post('note2'),
            "branch_id"               => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id"             => $this->input->post('currency_id'));

        /*$payment_data['converted_payment_amount']          = 0;
        $payment_data['imploded_converted_payment_amount'] = 0;
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
            $payment_data['converted_payment_amount']          = $payment_grand_total;
            $payment_data['imploded_converted_payment_amount'] = implode(",", $this->input->post('payment_amount'));
        }*/

        $payment_data['voucher_status'] = "1";

        if ($payment_mode == "cash"){
            $payment_data['voucher_status'] = "0";
        }

        $where = array( 'payment_id' => $payment_id);
        $payment_voucher_table = $this->config->item('payment_voucher_table');
        $data_main             = array_map('trim', $payment_data);

        if ($this->general_model->updateData($payment_voucher_table, $data_main, $where)){
            $purchase_id_data       = $reference_numbers;
            $this->db->select('*');
            $this->db->where('delete_status',0);
            $this->db->where('payment_id',$payment_id);
            $old_purchase_qry = $this->db->get('payment_invoice_reference');
            $old_salse_data = $old_purchase_qry->result();
            $old_purchase = array();
            if(!empty($old_salse_data)){
                foreach ($old_salse_data as $key => $value) {
                    $old_purchase['purchase_'.$value->reference_id] = $value;
                }
            }

            $voucher_data = array();
            $sub_payment_total = 0;

            /* delete old excess amount */
            /*$this->db->where('payment_id',$payment_id);
            $this->db->where('delete_status','0');
            $this->db->delete('purchase_excess_amount');*/
            $this->db->where('payment_id',$payment_id);
            $this->db->where('reference_id','0');
            $this->db->delete('payment_invoice_reference');
            $data_main['total_excess_amount'] = 0;

            foreach ($invoice_data as $key => $value) {
                $purchase_paid_amount = $value['paid_amount'];
                if(!isset($value['payment_total_paid']))  $value['payment_total_paid'] = 0;
                $reference_type = $data_main['reference_type'];
                if($value['reference_number'] == 'excess_amount') $reference_type = 'excess_amount';
                $reference_data = array('payment_id' => $payment_id,
                                        'reference_id' => $value['reference_number'],
                                        'reference_type' => $reference_type,
                                        'Invoice_total_paid' => ($purchase_paid_amount - $value['payment_total_paid']),
                                        'Invoice_pending' => (@$value['pending_amount'] ? $value['pending_amount'] : 0) ,
                                        'payment_amount' => (@$value['payment_amount'] ? $value['payment_amount'] : 0) ,
                                        'exchange_gain_loss' => (@$value['gain_loss_amount'] ? $value['gain_loss_amount'] : 0) ,
                                        'exchange_gain_loss_type' => (@$value['gain_loss_amount_icon'] ? $value['gain_loss_amount_icon'] : 0) ,
                                        'discount' => (@$value['discount'] ? $value['discount'] : 0) ,
                                        'other_charges' => (@$value['other_charges'] ? $value['other_charges'] : 0) ,
                                        'round_off' => (@$value['round_off'] ? $value['round_off'] : 0) ,
                                        'round_off_icon' => (@$value['icon_round_off'] ? $value['icon_round_off'] : 0) ,
                                        'payment_total_paid' => (@$value['payment_total_paid'] ? $value['payment_total_paid'] : 0) 
                                    );

                $voucher_data[] = $reference_data;

                if($value['reference_number'] != 'excess_amount'){
                    $k = 'purchase_'.$value['reference_number'];
                    if(array_key_exists($k, $old_purchase)){
                        if($value['is_edit'] != '0'){
                            $this->db->set($reference_data);
                            $this->db->where('payment_invoice_id',$old_purchase[$k]->payment_invoice_id);
                            $this->db->update('payment_invoice_reference');
                        }
                        unset($old_purchase[$k]);
                    }else{
                        
                        $this->db->insert('payment_invoice_reference',$reference_data);
                    }

                    if($value['is_edit'] != '0'){
                        if($payment_data['reference_type'] == 'purchase'){
                            $paid_amount       = array('purchase_paid_amount' => $purchase_paid_amount);
                            if($value['pending_amount'] <= 0 ){
                                $paid_amount       = array('purchase_paid_amount' => $purchase_paid_amount,'is_edit' => '0');
                            }
                            /*$paid_amount       = array('purchase_paid_amount' => $purchase_paid_amount);*/
                            $where = array('purchase_id' => $value['reference_number']);
                            $purchase_table = $this->config->item('purchase_table');
                            $this->general_model->updateData($purchase_table, $paid_amount, $where);
                            
                        }elseif($payment_data['reference_type'] == 'expense'){
                            $paid_amount       = array('expense_bill_paid_amount' => $purchase_paid_amount);
                            $where = array('expense_bill_id' => $value['reference_number']);
                            $purchase_table = $this->config->item('expense_bill_table');
                            $this->general_model->updateData($purchase_table, $paid_amount, $where);
                        }
                    }

                }elseif($value['reference_number'] == 'excess_amount'){
                    $excess_purchase_id = $this->input->post('excess_purchase_id');
                    $data_main['total_excess_amount'] += $value['payment_amount'];
                 
                    $this->db->insert('payment_invoice_reference',$reference_data);
                    /*$excess_purchase_id = $this->input->post('excess_purchase_id');
                    
                    if($excess_purchase_id){
                        $excess = array('purchase_id' => $excess_purchase_id,
                                        'payment_id' => $payment_id,
                                        'excess_amount' => $value['payment_amount'],
                                        'created_at' => date('Y-m-d'),
                                        'created_by' => $this->session->userdata('SESS_USER_ID'));

                        $this->db->insert('purchase_excess_amount',$excess);
                    }*/
                }
                $sub_payment_total += $value['payment_amount'];
            }

            if(!empty($old_purchase)){
               
                foreach ($old_purchase as $key => $value) {
                    /* revert paid amount from purchase invoice */

                    $payment_total_paid = $value->payment_total_paid;
                    $purchase_data = $this->general_model->getRecords('*', 'purchase', array(
                        'purchase_id' => $value->reference_id));
                   if(!empty($purchase_data)){
                        $purchase_paid_amount = bcsub($purchase_data[0]->purchase_paid_amount, $payment_total_paid, $section_modules['access_common_settings'][0]->amount_precision);
                        
                        $paid_amount = array('purchase_paid_amount' => $purchase_paid_amount);

                        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
                            $purchase_converted_paid_amount          = bcsub($purchase_data[0]->converted_paid_amount, $payment_total_paid, $section_modules['access_common_settings'][0]->amount_precision);
                           
                            $paid_amount['converted_paid_amount'] = $purchase_converted_paid_amount;
                        }
                        
                        $where = array('purchase_id' => $value->reference_id);
                        $purchase_table = $this->config->item('purchase_table');
                        $this->general_model->updateData($purchase_table, $paid_amount, $where);
                    }
                    /* update status delete */
                    $refer_sts = array('delete_status' => 1);
                    $where = array('payment_invoice_id' => $value->payment_invoice_id);
                   
                    $this->general_model->updateData('payment_invoice_reference',$refer_sts,$where);
                }
            }
            $successMsg = 'Payment Voucher Updated Successfully';
            $this->session->set_flashdata('payment_voucher_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $payment_id,
                'table_name'        => $payment_voucher_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'payment Voucher Updated');
            $data_main['payment_id'] = $payment_id;
            $data_main['sub_payment_total'] = $sub_payment_total;
            $log_table               = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
           
            if (in_array($data['accounts_module_id'], $data['active_add'])){

                if (in_array($data['accounts_sub_module_id'], $data['access_sub_modules'])){
                 
                    $this->VoucherEntry($data_main,$voucher_data,$supplier_name,$supplier_ledger_id , "edit");
                }
            }

            redirect('payment_voucher', 'refresh');
        }else {
            $errorMsg = 'Payment Voucher Update Unsuccessful';
            $this->session->set_flashdata('payment_voucher_error',$errorMsg);
            redirect('payment_voucher', 'refresh');
        }
    }

    public function delete()
    {
       
        $id                        = $this->input->post('delete_id');
        $payment_id                = $this->encryption_url->decode($id);
        $payment_voucher_table     = $this->config->item('payment_voucher_table');
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "delete_privilege";
        $data['privilege']         = "delete_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data                   = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        $payment_data = $this->general_model->getRecords('*', $payment_voucher_table, array(
            'payment_id' => $payment_id));

        $this->general_model->deleteCommonVoucher(array('table' => 'payment_voucher', 'where' => array('payment_id' =>$payment_id)),array('table' => 'accounts_payment_voucher', 'where' => array('payment_voucher_id' =>$payment_id)));
        
        if ($this->general_model->updateData($payment_voucher_table, array(
            'delete_status' => 1), array(
            'payment_id' => $payment_id))) {

            foreach ($payment_data as $key => $value){
                $this->db->select('*');
                $this->db->where("payment_id",$payment_id);
                $this->db->where("delete_status",'0');
                $qry = $this->db->get("payment_invoice_reference");
                $refre_data = $qry->result();

                /*$purchase_id_data              = explode(",", $value->reference_id);
                $receipt_amount_data           = explode(",", $value->imploded_receipt_amount);
                $converted_receipt_amount_data = explode(",", $value->imploded_converted_receipt_amount);*/
                if(!empty($refre_data)){
                    foreach ($refre_data as $key => $value7) {
                        if ($value->reference_type == "purchase"){
                            $purchase_data = $this->general_model->getRecords('*', 'purchase', array(
                            'purchase_id' => $value7->reference_id));
                            $purchase_paid_amount = bcsub($purchase_data[0]->purchase_paid_amount, $value7->payment_amount, $section_modules['access_common_settings'][0]->amount_precision);
                            $data                 = array(
                            'purchase_paid_amount' => $this->precise_amount($purchase_paid_amount, $access_common_settings[0]->amount_precision));

                            $where = array( 'purchase_id' => $value7->reference_id);
                            $purchase_table = 'purchase';

                        }elseif ($value->reference_type == "expense"){
                            $purchase_data = $this->general_model->getRecords('*', 'expense_bill', array(
                            'expense_bill_id' => $value7->reference_id));
                            $purchase_paid_amount = bcsub($purchase_data[0]->expense_bill_paid_amount, $value7->payment_amount, $section_modules['access_common_settings'][0]->amount_precision);
                            $data                 = array(
                            'expense_bill_paid_amount' => $this->precise_amount($purchase_paid_amount, $access_common_settings[0]->amount_precision));

                            $where = array( 'expense_bill_id' => $value7->reference_id);
                            $purchase_table = 'expense_bill';
                        }

                        $this->general_model->updateData($purchase_table, $data, $where);
                        $this->general_model->updateData('payment_invoice_reference', array('delete_status' => '1'), array('payment_invoice_id' => $value7->payment_invoice_id));
                    }
                }
            }
            $successMsg = 'Payment Voucher Deleted Successfully';
            $this->session->set_flashdata('payment_voucher_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $payment_id,
                'table_name'        => 'payment_voucher',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Payment Voucher Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('payment_voucher', 'refresh');
        } else {
            $errorMsg = 'Payment Voucher Delete Unsuccessful';
            $this->session->set_flashdata('payment_voucher_error',$errorMsg);
            redirect("payment_voucher", 'refresh');
        }

    }

    public function pdf($id)
    {
        $id                        = $this->encryption_url->decode($id);
        $branch_data       = $this->common->branch_field();
            $data['branch']    = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $country_data      = $this->common->country_field($data['branch'][0]->branch_country_id);
            $data['country']   = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data        = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
            $data['state']     = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
            $city_data         = $this->common->city_field($data['branch'][0]->branch_city_id);
            $data['city']      = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = "view_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);
        $data                      = array_merge($data, $section_modules);

        $supplier_module_id          = $this->config->item('supplier_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $data['currency'] = $this->currency_call();
        $payment_data     = $this->common->payment_voucher_list_field($id);
        $data['data']     = $this->general_model->getJoinRecords($payment_data['string'], $payment_data['table'], $payment_data['where'], $payment_data['join']);

        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];

        $pdf_json            = $data['access_settings'][0]->pdf_settings;
        $rep                 = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);
        $currency_code = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency_code[0]->currency_code;
            $data['currency_text'] = $currency_code[0]->currency_name;
            $data['currency_symbol_pdf'] = $currency_code[0]->currency_symbol_pdf;
            $data['data'][0]->unit = $currency_code[0]->unit;
            $data['data'][0]->decimal_unit = $currency_code[0]->decimal_unit;

        $html = $this->load->view('payment_voucher/pdf', $data, true);

        include APPPATH . "third_party/dompdf/autoload.inc.php";

        $dompdf = new Dompdf\Dompdf();

        $dompdf->load_html($html);

        $paper_size  = 'a4';
        $orientation = 'portrait';

        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size, $orientation);

        //and getting rend
        $dompdf->render();

        $dompdf->stream($data['data'][0]->voucher_number, array(
            'Attachment' => 0));

    }

    public function email($id)
    {
        $id                        = $this->encryption_url->decode($id);
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = "view_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $email_sub_module = 0;

        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
        {
            $email_sub_module = 1;
        }

        if ($email_sub_module == 1)
        {
            ob_start();
            $html              = ob_get_clean();
            $html              = utf8_encode($html);
            $branch_data       = $this->common->branch_field();
            $data['branch']    = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $country_data      = $this->common->country_field($data['branch'][0]->branch_country_id);
            $data['country']   = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data        = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
            $data['state']     = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
            $city_data         = $this->common->city_field($data['branch'][0]->branch_city_id);
            $data['city']      = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
            $data['currency']  = $this->currency_call();

            /*$this->db->select('*');
            $this->db->where('payment_id',$id);
            $results = $this->db->get('payment_voucher');
            $d = $results->result();

            if($d[0]->reference_type == 'purchase'){*/
                $payment_data      = $this->common->payment_voucher_list_field1($id);
            /*}else{
                $payment_data      = $this->common->payment_voucher_list_field1($id);
            }*/

            $data['data']      = $this->general_model->getJoinRecords($payment_data['string'], $payment_data['table'], $payment_data['where'], $payment_data['join']);
            $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1']     = $note_data['note1'];
            $data['template1'] = $note_data['template1'];
            $data['note2']     = $note_data['note2'];
            $data['template2'] = $note_data['template2'];

            $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                'module_id' => 2,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);

            $pdf_json            = $pdf[0]->pdf_settings;
            $rep                 = str_replace("\\", '', $pdf_json);
            $data['pdf_results'] = json_decode($rep, true);
            $currency_code = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency_code[0]->currency_code;
            $data['currency_text'] = $currency_code[0]->currency_name;
            $data['currency_symbol_pdf'] = $currency_code[0]->currency_symbol_pdf;

            $html = $this->load->view('payment_voucher/pdf', $data, true);
            /*include APPPATH . 'third_party/mpdf60/mpdf.php';
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $file_path                      = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F');
            $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name'] = $file_name . '.pdf';*/
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
            $file_name = str_replace('/','_',$file_name);
            include APPPATH . "third_party/dompdf/autoload.inc.php";
            $dompdf = new Dompdf\Dompdf();
            
            $paper_size  = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name'] = $file_name . '.pdf';
            $payment_voucher_data  = $this->common->payment_voucher_list_field1($id);
            $data['data']          = $this->general_model->getJoinRecords($payment_voucher_data['string'], $payment_voucher_data['table'], $payment_voucher_data['where'], $payment_voucher_data['join']);
            $branch_data           = $this->common->branch_field();
            $data['branch']        = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']   = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id'     => $payment_voucher_module_id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('payment_voucher/email', $data);
        }
        else
        {
            $this->load->view('payment_voucher', $data);
        }

    }

    public function add_purchase_payment($id){
        $id                        = $this->encryption_url->decode($id);
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "add_privilege";
        $data['privilege']         = "add_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /* bank default ledger title for payment mode*/
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $ledger_title = 'Acc@{{BANK}}';
        if(!empty($bank_led)){
            $ledger_title = $bank_led->ledger_name;
        }
        $data['default_ledger_title'] = $ledger_title;

        $data['payment_voucher_module_id'] = $payment_voucher_module_id;
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['bank_account_module_id']    = $this->config->item('bank_account_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        $data['notes_module_id']           = $this->config->item('notes_module');

        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['supplier'] = $this->supplier_call();

        if (in_array($data['bank_account_module_id'], $data['active_modules']))
        {
            $data['bank_account'] = $this->bank_account_call_new();
        }

        /*$data['currency'] = $this->currency_call();*/
        $data['data']  = $this->general_model->getRecords('*', 'purchase', array(
            'purchase_id' => $id));
        
        $access_settings = $data['access_settings'];
        $primary_id      = "payment_id";
        $table_name      = $this->config->item('payment_voucher_table');
        $date_field_name = "voucher_date";
        $current_date    = date('Y-m-d');

        $data['voucher_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['data'][0]->note1 = "";
        $data['data'][0]->note2 = "";
        $this->load->view('payment_voucher/add_purchase_payment', $data);
    }

    public function add_expense_payment($id){
        $id                        = $this->encryption_url->decode($id);
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "add_privilege";
        $data['privilege']         = "add_privilege";
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        
        /* bank default ledger title for payment mode*/
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $ledger_title = 'Acc@{{BANK}}';
        if(!empty($bank_led)){
            $ledger_title = $bank_led->ledger_name;
        }
        $data['default_ledger_title'] = $ledger_title;

        $data['payment_voucher_module_id'] = $payment_voucher_module_id;
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['bank_account_module_id']    = $this->config->item('bank_account_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        $data['notes_module_id']           = $this->config->item('notes_module');

        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['supplier'] = $this->supplier_call();

        if (in_array($data['bank_account_module_id'], $data['active_modules']))
        {
            $data['bank_account'] = $this->bank_account_call_new();
        }

        /*$data['currency'] = $this->currency_call();*/
        $this->db->select('*,e.expense_bill_id as purchase_id,e.supplier_receivable_amount as supplier_payable_amount,expense_bill_paid_amount as purchase_paid_amount,expense_bill_invoice_number as purchase_invoice_number,e.expense_bill_grand_total as purchase_grand_total');
        $this->db->where('expense_bill_id',$id);
        $s_data = $this->db->get('expense_bill e');
        $s_data = $s_data->result();
        $data['purchase_data'] = $s_data;
        $data['data']  = $s_data;
        /*$this->general_model->getRecords('*', 'expense_bill', array(
            'expense_bill_id' => $id));*/
        /*print_r($data['data']);exit();*/
        
        $access_settings = $data['access_settings'];
        $primary_id      = "payment_id";
        $table_name      = $this->config->item('payment_voucher_table');
        $date_field_name = "voucher_date";
        $current_date    = date('Y-m-d');

        $data['voucher_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['data'][0]->note1 = "";
        $data['data'][0]->note2 = "";
        $this->load->view('payment_voucher/add_expense_payment', $data);
    }

    public function convert_currency()
    {
        $id                 = $this->input->post('convert_currency_id');
        $id                 = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');

        $old_converted_receipt_amt = $this->general_model->getRecords('converted_receipt_amount', 'payment_voucher', array(
            'payment_id' => $id));
        $old_converted_receipt_array = explode(",", $old_converted_receipt_amt[0]->converted_receipt_amount);

        $payment_data = $this->general_model->getRecords('receipt_amount,reference_id,reference_type', 'payment_voucher', array(
            'payment_id' => $id));
        $receipt_amount_data = explode(",", $payment_data[0]->receipt_amount);
        $reference_type      = $payment_data[0]->reference_type;

        foreach ($receipt_amount_data as $key => $value)
        {
            $array_receipt_amount[] = bcmul($value, $new_converted_rate);
        }

        $converted_receipt_amount = implode(',', $array_receipt_amount);
        $reference_id_data        = explode(",", $payment_data[0]->reference_id);
        // $receipt_amount_data = explode(",", $payment_data[0]->receipt_amount);

        $data = array(
            'currency_converted_rate'   => $new_converted_rate,
            'currency_converted_amount' => $this->input->post('currency_converted_amount'),
            'converted_receipt_amount'  => $converted_receipt_amount);
        $this->general_model->updateData('payment_voucher', $data, array(
            'payment_id' => $id));

        foreach ($reference_id_data as $key => $value)
        {
            $new_converted_amount = bcmul($receipt_amount_data[$key], $new_converted_rate);

            if ($reference_type == "purchase")
            {
                $purchase_data = $this->general_model->getRecords('*', 'purchase', array(
                    'purchase_id' => $value));

                if ($old_converted_receipt_array[$key7] != 0)
                {
                    $converted_paid_amount = bcsub($purchase_data[0]->converted_paid_amount, $old_converted_receipt_array[$key], $section_modules['access_common_settings'][0]->amount_precision);

                    $total_converted_paid_amount = bcadd($converted_paid_amount, $new_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);
                }
                else
                {
                    $total_converted_paid_amount = bcadd($purchase_data[0]->converted_paid_amount, $new_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);
                }

// $converted_paid_amount = bcsub($purchase_data[0]->converted_paid_amount, $old_converted_receipt_array[$key]);
                // $total_converted_paid_amount = bcadd($converted_paid_amount, $new_converted_amount);

                $paid_converted_amount = array(
                    'converted_paid_amount' => $total_converted_paid_amount);
                $where = array(
                    'purchase_id' => $value);
                $purchase_table = $this->config->item('purchase_table');
                $this->general_model->updateData($purchase_table, $paid_converted_amount, $where);
            }
            else
            if ($reference_type == "expense")
            {
                $expense_data = $this->general_model->getRecords('*', 'expense_bill', array(
                    'expense_bill_id' => $value));

                if ($old_converted_receipt_array[$key7] != 0)
                {
                    $converted_paid_amount = bcsub($expense_data[0]->converted_paid_amount, $old_converted_receipt_array[$key], $section_modules['access_common_settings'][0]->amount_precision);

                    $total_converted_paid_amount = bcadd($converted_paid_amount, $new_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);
                }
                else
                {
                    $total_converted_paid_amount = bcadd($expense_data[0]->converted_paid_amount, $new_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);
                }

// $converted_paid_amount = bcsub($expense_data[0]->converted_paid_amount, $old_converted_receipt_array[$key]);
                // $total_converted_paid_amount = bcadd($converted_paid_amount, $new_converted_amount);

                $paid_converted_amount = array(
                    'converted_paid_amount' => $total_converted_paid_amount);
                $where = array(
                    'expense_bill_id' => $value);
                $expense_bill_table = $this->config->item('expense_bill_table');
                $this->general_model->updateData($expense_bill_table, $paid_converted_amount, $where);
            }

        }

        //update converted_voucher_amount in  accounts_payment_voucher/accounts_expense_voucher table

        $accounts_payment_voucher = $this->general_model->getRecords('*', 'accounts_payment_voucher', array(
            'payment_voucher_id' => $id,
            'delete_status'      => 0));

        foreach ($accounts_payment_voucher as $key1 => $value1)
        {
            $new_converted_voucher_amount = bcmul($accounts_payment_voucher[$key1]->voucher_amount, $new_converted_rate);

            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount);
            $where = array(
                'accounts_payment_id' => $accounts_payment_voucher[$key1]->accounts_payment_id);
            $voucher_table = "accounts_payment_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }

        redirect('payment_voucher', 'refresh');

    }

    public function view_details($id) {
        $payment_voucher_id        = $this->encryption_url->decode($id);
        /*$payment_voucher_id = 203;*/
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $data['module_id']         = $payment_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        // $email_sub_module_id             = $this->config->item('email_sub_module');
        /*echo $payment_voucher_id;*/
        $voucher_details = $this->common->payment_voucher_details($payment_voucher_id);

        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
       
        $this->load->view('payment_voucher/view_details', $data);
    }
}
