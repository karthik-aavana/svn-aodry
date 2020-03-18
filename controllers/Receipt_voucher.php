<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Receipt_voucher extends MY_Controller
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
        $receipt_voucher_module_id         = $this->config->item('receipt_voucher_module');
        $data['receipt_voucher_module_id'] = $receipt_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "view_privilege";
        $data['privilege']                 = $privilege;
        $section_modules                   = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

        $access_common_settings = $section_modules['access_common_settings'];

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['email_module_id']     = $this->config->item('email_module');
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        if (!empty($this->input->post()))
        {
            $columns = array(
                0 => 'receipt_id',
                1 => 'rv.voucher_date',
                2 => 'rv.voucher_number',
                3 => 'c.customer_name',
                4 => 'rv.reference_number',
                5 => 'rv.receipt_amount'
            );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->receipt_voucher_list_field_1($id=0, $order, $dir);
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
            $receipt_ids = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    if(!in_array($post->receipt_id, $receipt_ids)){
                        array_push($receipt_ids, $post->receipt_id);
                        $receipt_id                              = $this->encryption_url->encode($post->receipt_id);
                        $sales_id = $this->encryption_url->encode($post->reference_id);
                        $nestedData['voucher_date']              = date('d-m-Y', strtotime($post->voucher_date));
                        $nestedData['voucher_number']            = '<a href="' . base_url('receipt_voucher/view_details/') . $receipt_id . '">' . $post->voucher_number . '</a>';
                        $nestedData['customer']                  = $post->customer_name;
                        $reference_number         = str_replace(",", ",<br/>", $post->reference_number);
                        $nestedData['reference_number'] = ' <a href="' . base_url('sales/view/') . $sales_id . '">' . $reference_number . '</a>';
                        $nestedData['amount']                    = $this->precise_amount($post->receipt_amount, $access_common_settings[0]->amount_precision); /*$post->currency_symbol ' ' . str_replace(",", ",<br/> " .  " ", $post->imploded_receipt_amount); */
                        /*$nestedData['currency_converted_amount'] = $this->precise_amount($post->converted_receipt_amount, $access_common_settings[0]->amount_precision);*/
                        $nestedData['from_account']              = $post->from_account;
                        $nestedData['added_user']                = $post->first_name . ' ' . $post->last_name;
                                            
                        $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                        $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="View Receipt Voucher" href="' . base_url('receipt_voucher/view_details/') . $receipt_id . '"><i class="fa fa-eye"></i></a></span>';
                        if (in_array($receipt_voucher_module_id, $data['active_edit']) && $post->voucher_status != "2")
                        {
                            $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Edit Receipt Voucher" href="' . base_url('receipt_voucher/edit/') . $receipt_id . '"><i class="fa fa-pencil"></i></a></span>';
                        }
                        $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Download PDF" href="' . base_url('receipt_voucher/pdf/') . $receipt_id . '" target="_blank"><i class="fa fa-file-pdf-o"></i></a></span>';
                        if (in_array($data['email_module_id'], $data['active_view']))
                        {
                            if (in_array($data['email_sub_module_id'], $data['access_sub_modules'])){
                                $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail"><a href="#" class="btn btn-app composeMail" data-toggle="tooltip"  data-id="' . $receipt_id . '" data-name="regular"  data-placement="bottom" title="Email Receipt Voucher"> <i class="fa fa-envelope-o"></i></a></span>';
                            }
                        }               
                        if (in_array($receipt_voucher_module_id, $data['active_delete']))
                        {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a data-id="' . $receipt_id . '" data-path="receipt_voucher/delete" data-toggle="tooltip" data-placement="bottom" title="Delete Receipt Voucher" href="#" class="btn btn-app delete_button" ><i class="fa fa-trash"></i></a></span>';
                        }
                        $cols .= '</div></div>';                   
                        $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';                    
                        $send_data[]          = $nestedData;
                    }
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
            /*$data['currency'] = $this->currency_call();*/
            $this->load->view('receipt_voucher/list', $data);
        }

    }

    public function add()
    {
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "add_privilege";
        $data['privilege']         = "add_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

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

        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['bank_account_module_id'] = $this->config->item('bank_account_module');

        // $modules_present                 = array(

        //'bank_account_module_id' => $bank_account_module_id );
        // $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);

        $data['customer']       = $this->customer_call();
        $data['bank_account']   = $this->bank_account_call_new();
        $data['currency']       = $this->currency_call();
        $access_settings        = $data['access_settings'];
        $primary_id             = "receipt_id";
        $table_name             = $this->config->item('receipt_voucher_table');
        $date_field_name        = "voucher_date";
        $current_date           = date('Y-m-d');
        $data['voucher_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('receipt_voucher/add', $data);

    }

    public function edit($id){
        $id                        = $this->encryption_url->decode($id);
       
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "edit_privilege";
        $data['privilege']         = "edit_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

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

        $data['receipt_voucher_module_id'] = $receipt_voucher_module_id;
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['bank_account_module_id']    = $this->config->item('bank_account_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        $data['notes_module_id']           = $this->config->item('notes_module');

        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        /*$data['customer'] = $this->customer_call();*/

        /*$data['currency'] = $this->currency_call();*/

        $this->db->select('r.*,rv.receipt_invoice_id,rv.reference_id,rv.exchange_gain_loss,rv.exchange_gain_loss_type,rv.discount,rv.other_charges,rv.round_off,rv.round_off_icon,rv.receipt_amount as invoice_receipt_amount,rv.Invoice_pending,rv.Invoice_total_received,receipt_total_paid,c.customer_name,c.customer_mobile');
        $this->db->from('receipt_voucher r');
        $this->db->join('receipt_invoice_reference rv','r.receipt_id=rv.receipt_id','left');
        $this->db->join('customer c','r.party_id=c.customer_id','left');
        $this->db->where('r.receipt_id',$id);
        $d = $this->db->get();
        
        $data['data'] = $d->result();
        /*$data['data']     = $this->general_model->getRecords('*', 'receipt_voucher', array(
            'receipt_id' => $id));*/
        $service = 0;
        $bank_exist = 0;
        if(!empty($data['data'])){
            if ($data['data'][0]->payment_mode != "" && $data['data'][0]->payment_mode != "cash" && $data['data'][0]->payment_mode != "other payment mode" && $data['data'][0]->payment_mode != "bank"){
                $bank_exist = 1;
            }
            $customer_id = $data['data'][0]->party_id;
            $this->db->select('*,customer_payable_amount as sales_grand_total');
            $this->db->where('sales_party_id',$customer_id);
            $s_data = $this->db->get('sales');
            $s_data = $s_data->result();
            $data['sales_data'] = $s_data;
            $sales_data = array();
            foreach ($s_data as $key => $value) {
                $sales_data['sales_'.$value->sales_id] = $value;
            }
        }

        $sales_data_1 = array();
        if(!empty($sales_data)){
            foreach ($data['data'] as $key => $value) {
                
                if(array_key_exists('sales_'.$value->reference_id, $sales_data)){
                    $sales_invoice = $sales_data['sales_'.$value->reference_id];
                    $this->db->select('receipt_id,Invoice_pending,Invoice_total_received');
                    $this->db->from('receipt_invoice_reference');
                    $this->db->where('reference_id',$value->reference_id);
                    $this->db->order_by('receipt_invoice_id','DESC');
                    $this->db->limit(1);
                    $last_re = $this->db->get();
                    $last_receipt = $last_re->result();

                    $invoice_total = $sales_invoice->customer_payable_amount;
                    $paid_amount = ($sales_invoice->sales_paid_amount - $value->receipt_total_paid);
                    if($value->exchange_gain_loss_type == 'minus'){
                        $paid_amount -= $value->exchange_gain_loss;
                    }

                    if($value->round_off_icon == 'plus'){
                        $paid_amount -= $value->round_off;
                    }
                    /*$paid_amount = ($sales_invoice->sales_paid_amount - $value->receipt_amount);*/
                    $pending_amount = $invoice_total - $paid_amount - $value->receipt_total_paid;
                    /*$pending_amount = $invoice_total - $paid_amount - $value->receipt_amount;*/
                    $data['data'][$key]->invoice_total = $invoice_total;
                    $data['data'][$key]->invoice_paid_amount = $paid_amount;
                    $data['data'][$key]->invoice_pending = $pending_amount;
                    $data['data'][$key]->reference_number = $sales_invoice->sales_invoice_number;
                    $is_edit = 0;
                    if($last_receipt[0]->receipt_id == $value->receipt_id && $pending_amount > 0)$is_edit = 1;
                    if($is_edit == 0){
                        $data['data'][$key]->invoice_paid_amount = $value->Invoice_total_received;
                        $data['data'][$key]->invoice_pending = $value->Invoice_pending;
                    }
                    $data['data'][$key]->is_edit = $is_edit;
                }
            }
        }

        /* Get excess amount */
        $this->db->select('*');
        $this->db->where('receipt_id',$id);
        $this->db->where('delete_status','0');
        $excess_qry = $this->db->get('sales_excess_amount');
        $excess_data = $excess_qry->result();
        if(!empty($excess_data)){
            $excess_data = array('excess_sales_id' =>  $excess_data[0]->sales_id,
                                'is_used' =>  $excess_data[0]->is_used,
                                'excess_amount' => $excess_data[0]->excess_amount);
            $data['excess_data'] = $excess_data;
        }
        $data['invoice_data'] = $sales_data_1;
        $data['bank_exist'] = $bank_exist;

        if (in_array($data['bank_account_module_id'], $data['active_modules']) || $bank_exist > 0){
            $data['bank_account'] = $this->bank_account_call_new();
        }

        $data['receipt_id'] = $id;
        /*echo "<pre>";
        print_r($data);
        exit();*/
        $this->load->view('receipt_voucher/edit', $data);
    }

    public function get_sales_invoice_number(){
        $customer_id    = $this->input->post('customer_id');
        $currency_id    = $this->input->post('currency_id');
        $reference_type = $this->input->post('reference_type');

        $balance = 0;
        $data    = array();

        if ($reference_type == "sales"){
            $invoice_data = $this->common->get_customer_invoice_number_field($customer_id, $balance, $currency_id);
            $data['data'] = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);
        }

        echo json_encode($data);
    }

    public function get_sales_details(){
        $sales_id       = $this->input->post('sales_id');
        $reference_type = $this->input->post('reference_type');
        $data           = array();

        if ($reference_type == "sales"){
            $data = $this->general_model->getRecords('*', 'sales', array('sales_id' => $sales_id));
        }
        echo json_encode($data);
    }

    public function add_receipt(){
        /*echo "<pre>";
        print_r($this->input->post());
        exit();*/
        $all_reference = $this->input->post('invoice_data');
        $invoice_data = json_decode($all_reference,true);
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "add_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['receipt_voucher_module_id'] = $receipt_voucher_module_id;
        $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['accounts_module_id']     = $this->config->item('accounts_module');
        $data['notes_module_id']        = $this->config->item('notes_module');
        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $access_settings = $section_modules['access_settings'];
        $currency = $this->input->post('currency_id');

        if ($access_settings[0]->invoice_creation == "automatic"){

            if ($this->input->post('voucher_number') != $this->input->post('voucher_number_old')){
                $primary_id      = "receipt_id";
                $table_name      = $this->config->item('receipt_voucher_table');
                $date_field_name = "voucher_date";
                $current_date    = date('Y-m-d', strtotime($this->input->post('voucher_date')));
                $voucher_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else{
                $voucher_number = $this->input->post('voucher_number');
            }
        }else{
            $voucher_number = $this->input->post('voucher_number');
        }

        $receipt_amount_arr  = $this->input->post('receipt_amount');
        $receipt_grand_total = 0;

        $reference_numbers = $reference_number_text = $receipt_amount =$remaining_amount= $paid_amount=$invoice_total = array();
        /*print_r($invoice_data);*/
        if(!empty($invoice_data)){
           
            foreach ($invoice_data as $key => $value) {
                if($value['reference_number'] != 'excess_amount'){
                    /*$invoice_data[$key]['paid_amount'] +=(@$value['receipt_amount'] ? $value['receipt_amount'] : 0);*/
                    $invoice_data[$key]['paid_amount'] +=(@$value['receipt_total_paid'] ? $value['receipt_total_paid'] : 0);
                    array_push($reference_number_text, $value['reference_number_text']);
                    array_push($remaining_amount, $value['pending_amount']);
                    array_push($paid_amount, $value['paid_amount']);
                    array_push($invoice_total, $value['invoice_total']);
                }
                array_push($reference_numbers, $value['reference_number']);
                array_push($receipt_amount, $value['receipt_amount']);
            }
        }
       
        $receipt_grand_total = $this->input->post('total_receipt_amount');

        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode          = $bank_acc_payment_mode[0];
            /*$ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode[0]));*/
            /*$ledger_from = $ledger_bank_acc[0]->ledger_id;*/
            $from_acc    = $bank_acc_payment_mode[1];
        } else{
            $ledger_from  = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));
            $payment_mode = $this->input->post('payment_mode');
            $from_acc     = $this->input->post('payment_mode');
        }

        $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array(
            'customer_id' => $this->input->post('customer')));

        $customer_name = $customer[0]->customer_name;
        $customer_ledger_id = $customer[0]->ledger_id;
        

        /*$ledger_to   = $customer[0]->ledger_id;*/
        $cheque_date = ($this->input->post('cheque_date') != '' ? date('Y-m-d',strtotime($this->input->post('cheque_date'))) : '');

        if (!$cheque_date) {
            $cheque_date = null;
        }

        $receipt_data = array(
            "voucher_date"            => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
            "voucher_number"          => $voucher_number,
            "party_id"                => $this->input->post('customer'),
            "party_type"              => 'customer',
            "reference_id"            => implode(",", $reference_numbers),
            "reference_type"          => $this->input->post('reference_type'),
            "reference_number"        => implode(",", $reference_number_text),
            "from_account"            => $from_acc,
            "to_account"              => 'customer-' . $customer[0]->customer_name,
            "imploded_receipt_amount" => implode(",", $receipt_amount),
            "invoice_balance_amount"  => implode(",", $remaining_amount),
            "invoice_paid_amount"     => implode(",", $paid_amount),
            "invoice_total"           => implode(",", $invoice_total),
            "receipt_amount"          => $receipt_grand_total,
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

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $receipt_data['converted_receipt_amount']          = $receipt_grand_total;
            $receipt_data['imploded_converted_receipt_amount'] = $receipt_grand_total;
        }else {
            $receipt_data['converted_receipt_amount']          = 0;
            $receipt_data['imploded_converted_receipt_amount'] = 0;
        }

        if ($payment_mode == "cash"){
            $receipt_data['voucher_status'] = "0";
        } else{
            $receipt_data['voucher_status'] = "1";
        }

        $data_main = array_map('trim', $receipt_data);

        $receipt_voucher_table = $this->config->item('receipt_voucher_table');
        $sub_receipt_total = 0;
        if ($receipt_id = $this->general_model->insertData($receipt_voucher_table, $data_main)) {
            /*$sales_id_data       = explode(",", $data_main['reference_id']);
            $receipt_amount_data = explode(",", $data_main['imploded_receipt_amount']);*/
            $reference_data = array();
            foreach ($invoice_data as $key7 => $value7) {

                if ($data_main['reference_type'] == 'sales' && $value7['reference_number'] != 'excess_amount') {
                    /*$sales_data = $this->general_model->getRecords('*', 'sales', array(
                        'sales_id' => $value7));*/
                    /*$sales_paid_amount = $sales_data[0]->sales_paid_amount;
                    $total_paid_amount = bcadd($sales_paid_amount, $receipt_amount_data[$key7], $section_modules['access_common_settings'][0]->amount_precision);*/
                    $sales_paid_amount = $value7['paid_amount'];
                    $paid_amount       = array('sales_paid_amount' => $sales_paid_amount);
                    if($value7['pending_amount'] <= 0){
                        $paid_amount['is_edit'] = '0';
                    }

                    if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
                        $sales_converted_paid_amount          = $value['paid_amount'];//$sales_data[0]->converted_paid_amount
                        /*$total_converted_paid_amount          = bcadd($sales_converted_paid_amount, $receipt_amount_data[$key7]);*/
                        $paid_amount['converted_paid_amount'] = $sales_paid_amount;//$total_converted_paid_amount;
                    }
                    
                    $reference_data[] = array('receipt_id' => $receipt_id,
                                            'reference_id' => $value7['reference_number'],
                                            'receipt_amount' => $value7['receipt_amount'],
                                            'Invoice_total_received' => ($sales_paid_amount-$value7['receipt_total_paid']),
                                            'Invoice_pending' => $value7['pending_amount'],
                                            'exchange_gain_loss' => $value7['gain_loss_amount'],
                                            'exchange_gain_loss_type' => $value7['gain_loss_amount_icon'],
                                            'discount' => $value7['discount'],
                                            'other_charges' => $value7['other_charges'],
                                            'round_off' => $value7['round_off'],
                                            'round_off_icon' => $value7['icon_round_off'],
                                            'receipt_total_paid' => $value7['receipt_total_paid']
                                        );

                    $where = array('sales_id' => $value7['reference_number']);
                    $sales_table = $this->config->item('sales_table');
                    $this->general_model->updateData($sales_table, $paid_amount, $where);
                    $successMsg = 'Receipt Voucher Added Successfully';
                    $this->session->set_flashdata('receipt_voucher_sales',$successMsg);

                }elseif($value7['reference_number'] == 'excess_amount'){
                    $excess_sales_id = $this->input->post('excess_sales_id');

                    if($excess_sales_id){
                        $excess = array('sales_id' => $excess_sales_id,
                                        'receipt_id' => $receipt_id,
                                        'excess_amount' => $value7['receipt_amount'],
                                        'created_at' => date('Y-m-d'),
                                        'created_by' => $this->session->userdata('SESS_USER_ID'));

                        $this->db->insert('sales_excess_amount',$excess);
                    }
                }
                $sub_receipt_total += $value7['receipt_amount'];
            }

            $this->db->insert_batch('receipt_invoice_reference',$reference_data);
            $successMsg = 'Receipt Voucher Added Successfully';
            $this->session->set_flashdata('receipt_voucher_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $receipt_id,
                'table_name'        => $receipt_voucher_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Receipt Voucher Inserted');
            $data_main['receipt_id'] = $receipt_id;
            $data_main['sub_receipt_total'] = $sub_receipt_total;
            $log_table               = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);

            if (in_array($data['accounts_module_id'], $data['active_add'])){

                if (in_array($data['accounts_sub_module_id'], $data['access_sub_modules'])){
                    /*$this->voucher_entry($receipt_id, $ledger_from, $ledger_to, $data_main['receipt_amount'], "add", $currency);*/
                    $this->VoucherEntry($data_main,$reference_data,$customer_name , "add",$customer_ledger_id);
                }
            }

            if ($this->session->userdata('cat_type') != "" && $this->session->userdata('cat_type') != null && $this->session->userdata('cat_type') == 'customer' && $payment_mode != "cash"){
                $this->session->unset_userdata('cat_type');

                if ($currency == $this->session->userdata('SESS_DEFAULT_CURRENCY')){
                    redirect('bank_statement/bank_group', 'refresh');
                }
            }

            if($this->input->post('section_area') != ''){
                redirect($this->input->post('section_area'), 'refresh');
            }else{
                redirect('receipt_voucher', 'refresh');
            }
            
        }else{
            $errorMsg = 'Receipt Voucher Add Unsuccessful';
            $this->session->set_flashdata('receipt_voucher_error',$errorMsg);
            redirect('receipt_voucher', 'refresh');
        }
        exit;
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

        $payment_ledger = $data_main['payment_mode'];

        if ($data_main['payment_mode'] != "cash" && $data_main['payment_mode'] != "bank" && $data_main['payment_mode'] != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $data_main['payment_mode']);
            $payment_ledger          = $bank_acc_payment_mode[0];
            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_ledger));
            $bank_id = $ledger_bank_acc[0]->ledger_id; 
            /*$from_acc    = $bank_acc_payment_mode[1];*/
        }else{
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

    public function voucher_entry($receipt_id, $ledger_from, $ledger_to, $voucher_amount, $operation, $currency)
    {
        $data1 = array(
            'receipt_voucher_id' => $receipt_id,
            'ledger_from'        => $ledger_from,
            'ledger_to'          => $ledger_to,
            'voucher_amount'     => $voucher_amount,
            'dr_amount'          => $voucher_amount,
            'cr_amount'          => 0);

        $data2 = array(
            'receipt_voucher_id' => $receipt_id,
            'ledger_from'        => $ledger_to,
            'ledger_to'          => $ledger_from,
            'voucher_amount'     => $voucher_amount,
            'dr_amount'          => 0,
            'cr_amount'          => $voucher_amount);

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $data1['converted_voucher_amount'] = $voucher_amount;
            $data2['converted_voucher_amount'] = $voucher_amount;
        }
        else
        {
            $data1['converted_voucher_amount'] = 0;
            $data2['converted_voucher_amount'] = 0;
        }

        if ($operation == "add")
        {
            $this->general_model->insertData('accounts_receipt_voucher', $data1);
            $this->general_model->insertData('accounts_receipt_voucher', $data2);
        }
        elseif ($operation == "edit")
        {
            $accounts_receipt = $this->general_model->getRecords('accounts_receipt_id', 'accounts_receipt_voucher', array(
                'receipt_voucher_id' => $receipt_id,
                'delete_status'      => 0));

            if ($accounts_receipt)
            {
                $this->general_model->updateData('accounts_receipt_voucher', $data1, array(
                    'accounts_receipt_id' => $accounts_receipt[0]->accounts_receipt_id));
                $this->general_model->updateData('accounts_receipt_voucher', $data2, array(
                    'accounts_receipt_id' => $accounts_receipt[1]->accounts_receipt_id));
            }

        }

    }

    public function edit_receipt(){
        $all_reference = $this->input->post('invoice_data');
        $invoice_data = json_decode($all_reference,true);
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "edit_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['receipt_voucher_module_id'] = $receipt_voucher_module_id;
        $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['accounts_module_id']     = $this->config->item('accounts_module');
        $data['notes_module_id']        = $this->config->item('notes_module');
        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $access_settings = $section_modules['access_settings'];
        $currency = $this->input->post('currency_id');

        if ($access_settings[0]->invoice_creation == "automatic") {

            if ($this->input->post('voucher_number') != $this->input->post('voucher_number_old')) {
                $primary_id      = "receipt_id";
                $table_name      = $this->config->item('receipt_voucher_table');
                $date_field_name = "voucher_date";
                $current_date    = date('Y-m-d',strtotime($this->input->post('voucher_date')));
                $voucher_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else{
                $voucher_number = $this->input->post('voucher_number');
            }
        }else{
            $voucher_number = $this->input->post('voucher_number');
        }

        $receipt_id          = $this->input->post('receipt_id');
        $receipt_amount_arr  = $this->input->post('receipt_amount');
        $receipt_grand_total = 0;

        /*foreach ($receipt_amount_arr as $key9 => $value9){
            $receipt_grand_total = bcadd($receipt_grand_total, $value9, $section_modules['access_common_settings'][0]->amount_precision);
        }*/

        $receipt_amount_arr  = $this->input->post('receipt_amount');
        $receipt_grand_total = 0;

        $reference_numbers = $reference_number_text = $receipt_amount =$remaining_amount= $paid_amount=$invoice_total = array();

        if(!empty($invoice_data)){
           
            foreach ($invoice_data as $key => $value) {
                $invoice_data[$key]['paid_amount'] +=(@$value['receipt_total_paid'] ? $value['receipt_total_paid'] : 0);
                if($value['reference_number'] != 'excess_amount'){
                    array_push($reference_number_text, $value['reference_number_text']);
                    array_push($remaining_amount, $value['pending_amount']);
                    array_push($paid_amount, $value['paid_amount']);
                    array_push($invoice_total, $value['invoice_total']);
                }
                array_push($reference_numbers, $value['reference_number']);
                array_push($receipt_amount, $value['receipt_amount']);
            }
        }
       
        $receipt_grand_total = $this->input->post('total_receipt_amount');

        $receipt_amount_old           = $this->input->post('receipt_amount_old');
        $converted_receipt_amount_old = $this->input->post('converted_receipt_amount_old');

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

        $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array(
            'customer_id' => $this->input->post('customer')));
        $ledger_to   = $customer[0]->ledger_id;
        $customer_name = $customer[0]->customer_name;
        $customer_ledger_id = $customer[0]->ledger_id;
        $cheque_date = ($this->input->post('cheque_date') != '' ? date('Y-m-d',strtotime($this->input->post('cheque_date'))) : '');

        if (!$cheque_date){
            $cheque_date = null;
        }
        $receipt_data = array(
            "voucher_date"            => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
            "voucher_number"          => $voucher_number,
            "party_id"                => $this->input->post('customer'),
            "party_type"              => 'customer',
            "reference_id"            => implode(",", $reference_numbers),
            "reference_type"          => $this->input->post('reference_type'),
            "reference_number"        => implode(",", $reference_number_text),
            "from_account"            => $from_acc,
            "to_account"              => 'customer-' . $customer[0]->customer_name,
            "imploded_receipt_amount" => implode(",", $receipt_amount),
            "invoice_balance_amount"  => implode(",", $remaining_amount),
            "invoice_paid_amount"     => implode(",", $paid_amount),
            "invoice_total"           => implode(",", $invoice_total),
            "payment_mode"            => $payment_mode,
            "receipt_amount"          => $receipt_grand_total,
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

        $receipt_data['converted_receipt_amount']          = 0;
        $receipt_data['imploded_converted_receipt_amount'] = 0;
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
            $receipt_data['converted_receipt_amount']          = $receipt_grand_total;
            $receipt_data['imploded_converted_receipt_amount'] = implode(",", $this->input->post('receipt_amount'));
        }

        $receipt_data['voucher_status'] = "1";

        if ($payment_mode == "cash"){
            $receipt_data['voucher_status'] = "0";
        }

        $where = array( 'receipt_id' => $receipt_id);
        $receipt_voucher_table = $this->config->item('receipt_voucher_table');
        $data_main             = array_map('trim', $receipt_data);

        if ($this->general_model->updateData($receipt_voucher_table, $data_main, $where)){
            $sales_id_data       = $reference_numbers;
            $this->db->select('*');
            $this->db->where('delete_status',0);
            $this->db->where('receipt_id',$receipt_id);
            $old_sales_qry = $this->db->get('receipt_invoice_reference');
            $old_salse_data = $old_sales_qry->result();
            $old_sales = array();
            if(!empty($old_salse_data)){
                foreach ($old_salse_data as $key => $value) {
                    $old_sales['sales_'.$value->reference_id] = $value;
                }
            }

            $voucher_data = array();
            $sub_receipt_total = 0;

            /* delete old excess amount */
            $this->db->where('receipt_id',$receipt_id);
            $this->db->where('delete_status','0');
            $this->db->delete('sales_excess_amount');
           
            foreach ($invoice_data as $key => $value) {
                
                if($value['reference_number'] != 'excess_amount'){
                    $sales_paid_amount = $value['paid_amount'];
                    $paid_amount       = array('sales_paid_amount' => $sales_paid_amount);

                    if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
                        $sales_converted_paid_amount          = $value['paid_amount'];
                        $paid_amount['converted_paid_amount'] = $sales_paid_amount;
                    }

                    $reference_data = array('receipt_id' => $receipt_id,
                                            'reference_id' => $value['reference_number'],
                                            'receipt_amount' => $value['receipt_amount'],
                                            'Invoice_total_received' => ($sales_paid_amount-$value['receipt_amount']),
                                            'Invoice_pending' => $value['pending_amount'],
                                            'exchange_gain_loss' => $value['gain_loss_amount'],
                                            'exchange_gain_loss_type' => $value['gain_loss_amount_icon'],
                                            'discount' => $value['discount'],
                                            'other_charges' => $value['other_charges'],
                                            'round_off' => $value['round_off'],
                                            'round_off_icon' => $value['icon_round_off'],
                                            'receipt_total_paid' => $value['receipt_total_paid']
                                        );
                    $voucher_data[] = $reference_data;
                    $k = 'sales_'.$value['reference_number'];

                    if(array_key_exists($k, $old_sales)){
                        if($value['is_edit'] != '0'){
                            $this->db->set($reference_data);
                            $this->db->where('receipt_invoice_id',$old_sales[$k]->receipt_invoice_id);
                            $this->db->update('receipt_invoice_reference');
                        }
                        unset($old_sales[$k]);
                    }else{
                        $this->db->insert('receipt_invoice_reference',$reference_data);
                    }
                    if($value['is_edit'] != '0'){
                        $where = array('sales_id' => $value['reference_number']);
                        $sales_table = $this->config->item('sales_table');
                        $this->general_model->updateData($sales_table, $paid_amount, $where);
                    }

                }elseif($value['reference_number'] == 'excess_amount'){
                    $excess_sales_id = $this->input->post('excess_sales_id');
                    
                    if($excess_sales_id){
                        $excess = array('sales_id' => $excess_sales_id,
                                        'receipt_id' => $receipt_id,
                                        'excess_amount' => $value['receipt_amount'],
                                        'created_at' => date('Y-m-d'),
                                        'created_by' => $this->session->userdata('SESS_USER_ID'));

                        $this->db->insert('sales_excess_amount',$excess);
                    }
                }
                
                $sub_receipt_total += $value['receipt_amount'];
            }

            if(!empty($old_sales)){
                foreach ($old_sales as $key => $value) {
                    /* revert paid amount from sales invoice */

                    $receipt_total_paid = $value->receipt_total_paid;
                    $sales_data = $this->general_model->getRecords('*', 'sales', array(
                        'sales_id' => $value->reference_id));
                    $sales_paid_amount = bcsub($sales_data[0]->sales_paid_amount, $receipt_total_paid, $section_modules['access_common_settings'][0]->amount_precision);
                    
                    $paid_amount = array('sales_paid_amount' => $sales_paid_amount);

                    if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
                        $sales_converted_paid_amount          = bcsub($sales_data[0]->converted_paid_amount, $receipt_total_paid, $section_modules['access_common_settings'][0]->amount_precision);
                       
                        $paid_amount['converted_paid_amount'] = $sales_converted_paid_amount;
                    }
                    
                    $where = array('sales_id' => $value->reference_id);
                    $sales_table = $this->config->item('sales_table');
                    $this->general_model->updateData($sales_table, $paid_amount, $where);
                    
                    /* update status delete */
                    $refer_sts = array('delete_status',1);
                    $where = array('receipt_invoice_id',$value->receipt_invoice_id);
                    $this->general_model->updateData('receipt_invoice_reference',$refer_sts,$where);
                }
            }
            $successMsg = 'Receipt Voucher Updated Successfully';
            $this->session->set_flashdata('receipt_voucher_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $receipt_id,
                'table_name'        => $receipt_voucher_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Receipt Voucher Updated');
            $data_main['receipt_id'] = $receipt_id;
            $data_main['sub_receipt_total'] = $sub_receipt_total;
            $log_table               = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);

            /*if (in_array($data['accounts_module_id'], $data['active_add'])){

                if (in_array($data['accounts_sub_module_id'], $data['access_sub_modules'])){
                    $this->voucher_entry($receipt_id, $ledger_from, $ledger_to, $data_main['receipt_amount'], "edit", $currency);
                }
            }*/

            if (in_array($data['accounts_module_id'], $data['active_add'])){

                if (in_array($data['accounts_sub_module_id'], $data['access_sub_modules'])){
                    /*$this->voucher_entry($receipt_id, $ledger_from, $ledger_to, $data_main['receipt_amount'], "add", $currency);*/

                    $this->VoucherEntry($data_main,$voucher_data,$customer_name , "edit",$customer_ledger_id);
                }
            }

            redirect('receipt_voucher', 'refresh');
        }else {
            $errorMsg = 'Receipt Voucher Update Unsuccessful';
            $this->session->set_flashdata('receipt_voucher_error',$errorMsg);
            redirect('receipt_voucher', 'refresh');
        }
    }

    public function delete()
    {
        $id                        = $this->input->post('delete_id');
        $receipt_id                = $this->encryption_url->decode($id);
        $receipt_voucher_table     = $this->config->item('receipt_voucher_table');
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "delete_privilege";
        $data['privilege']         = "delete_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data                   = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        $receipt_data = $this->general_model->getRecords('*', $receipt_voucher_table, array(
            'receipt_id' => $receipt_id));

        $receipt_invoice_data = $this->general_model->getRecords('*', 'receipt_invoice_reference', array(
            'receipt_id' => $receipt_id));
        /* delete voucher from payment table */
        
        
        $this->general_model->deleteCommonVoucher(array('table' => 'receipt_voucher', 'where' => array('receipt_id' =>$receipt_id)),array('table' => 'accounts_receipt_voucher', 'where' => array('receipt_voucher_id' =>$receipt_id)));
        

        if ($this->general_model->updateData($receipt_voucher_table, array(
            'delete_status' => 1), array(
            'receipt_id' => $receipt_id))){

            foreach ($receipt_data as $key => $value) {
                
                foreach ($receipt_invoice_data as $key => $value7) {
                    if ($value->reference_type == "sales")
                    {
                        $sales_data = $this->general_model->getRecords('*', 'sales', array(
                            'sales_id' => $value7->reference_id));
                        $sales_paid_amount = bcsub($sales_data[0]->sales_paid_amount, $value7->receipt_total_paid);
                        $data              = array(
                            'sales_paid_amount' => $this->precise_amount($sales_paid_amount, $access_common_settings[0]->amount_precision));

                        $where = array(
                            'sales_id' => $value7->reference_id);
                        $sales_table = 'sales';
                        $this->general_model->updateData($sales_table, $data, $where);
                    }
                }
                /* delete sales receipt invoice records */
                $this->db->where('receipt_id',$value->receipt_id);
                $this->db->delete('receipt_invoice_reference');
                /* delete sales excess invoice records */
                $this->db->set('delete_status','1');
                $this->db->where('receipt_id',$value->receipt_id);
                $this->db->update('sales_excess_amount');
            }
            $successMsg = 'Receipt Voucher Deleted Successfully';
            $this->session->set_flashdata('receipt_voucher_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $receipt_id,
                'table_name'        => 'receipt_voucher',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Receipt Voucher Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('receipt_voucher', 'refresh');
        }
        else
        {
            $errorMsg = 'Receipt Voucher Delete Unsuccessful';
            $this->session->set_flashdata('receipt_voucher_error',$errorMsg);
            redirect("receipt_voucher", 'refresh');
        }

    }

    public function pdf($id)
    {
        $id                        = $this->encryption_url->decode($id);
        $data                      = $this->get_default_country_state();
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = "view_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);
        $data                      = array_merge($data, $section_modules);

        $customer_module_id          = $this->config->item('customer_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $data['currency'] = $this->currency_call();
        $receipt_data     = $this->common->receipt_voucher_list_field($id);
        $data['data']     = $this->general_model->getJoinRecords($receipt_data['string'], $receipt_data['table'], $receipt_data['where'], $receipt_data['join']);

        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $data['currency_text']   = $currency[0]->currency_name;
        $data['currency_symbol_pdf']   = $currency[0]->currency_symbol_pdf;
        $data['data'][0]->unit = $currency[0]->unit;
        $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
        $pdf_json            = $data['access_settings'][0]->pdf_settings;

        $rep                 = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);

        $html = $this->load->view('receipt_voucher/pdf', $data, true);

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
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = "view_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

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
            /*$data['currency']  = $this->currency_call();*/
            /*$receipt_data      = $this->common->receipt_voucher_list_field1($id);
            $data['data']      = $this->general_model->getJoinRecords($receipt_data['string'], $receipt_data['table'], $receipt_data['where'], $receipt_data['join']);*/
            $this->db->select('r.*,rv.reference_id,rv.exchange_gain_loss,rv.exchange_gain_loss_type,rv.discount,rv.other_charges,rv.round_off,rv.round_off_icon,rv.receipt_amount as invoice_receipt_amount,c.*');
            $this->db->from('receipt_voucher r');
            $this->db->join('receipt_invoice_reference rv','r.receipt_id=rv.receipt_id','left');
            $this->db->join('customer c','r.party_id=c.customer_id','left');
            $this->db->where('r.receipt_id',$id);
            $d = $this->db->get();
            
            $data['data'] = $d->result();
            $currency_code = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency_code[0]->currency_code;
            $data['currency_text'] = $currency_code[0]->currency_name;
            $service = 0;
            $bank_exist = 0;
            if(!empty($data['data'])){
                if ($data['data'][0]->payment_mode != "" && $data['data'][0]->payment_mode != "cash" && $data['data'][0]->payment_mode != "other payment mode" && $data['data'][0]->payment_mode != "bank"){
                    $bank_exist = 1;
                }
                $customer_id = $data['data'][0]->party_id;
                $this->db->select('*');
                $this->db->where('sales_party_id',$customer_id);
                $s_data = $this->db->get('sales');
                $s_data = $s_data->result();
                $data['sales_data'] = $s_data;
                $sales_data = array();
                foreach ($s_data as $key => $value) {
                    $sales_data['sales_'.$value->sales_id] = $value;
                }
            }

            $sales_data_1 = array();
            if(!empty($sales_data)){
                foreach ($data['data'] as $key => $value) {
                    if(array_key_exists('sales_'.$value->reference_id, $sales_data)){
                        $sales_invoice = $sales_data['sales_'.$value->reference_id];
                        $this->db->select('receipt_id');
                        $this->db->from('receipt_invoice_reference');
                        $this->db->where('reference_id',$value->reference_id);
                        $this->db->order_by('receipt_invoice_id','DESC');
                        $this->db->limit(1);
                        $last_re = $this->db->get();
                        $last_receipt = $last_re->result();

                        $is_edit = 0;
                        if($last_receipt[0]->receipt_id == $value->receipt_id)$is_edit = 1;
                        $invoice_total = $sales_invoice->sales_grand_total;
                        $paid_amount = $sales_invoice->sales_paid_amount;
                        $pending_amount = $invoice_total - $paid_amount;
                        $data['data'][$key]->invoice_total = $invoice_total;
                        $data['data'][$key]->invoice_paid_amount = $paid_amount;
                        $data['data'][$key]->invoice_pending = $pending_amount;
                        $data['data'][$key]->reference_number = $sales_invoice->sales_invoice_number;
                        $data['data'][$key]->is_edit = $is_edit;
                    }
                }
            }
            /*$data['invoice_data'] = $sales_data_1;
            $data['bank_exist'] = $bank_exist;*/
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

            $html = $this->load->view('receipt_voucher/pdf1', $data, true);
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
            $file_name = str_replace('/','_',$file_name);
            /*include APPPATH . 'third_party/mpdf60/mpdf.php';
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $mpdf->WriteHTML($html);*/
            /*$mpdf->Output($file_path . $file_name . '.pdf', 'F');*/
            $file_path                      = "././pdf_form/";
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
            $receipt_voucher_data  = $this->common->receipt_voucher_list_field1($id);
            $data['data']          = $this->general_model->getJoinRecords($receipt_voucher_data['string'], $receipt_voucher_data['table'], $receipt_voucher_data['where'], $receipt_voucher_data['join']);
            $branch_data           = $this->common->branch_field();
            $data['branch']        = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']   = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id'     => $receipt_voucher_module_id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('receipt_voucher/email', $data);
        }
        else
        {
            $this->load->view('receipt_voucher', $data);
        }

    }

    public function add_sales_receipt($id){
        $id                        = $this->encryption_url->decode($id);
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "edit_privilege";
        $data['privilege']         = "edit_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

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

        $data['receipt_voucher_module_id'] = $receipt_voucher_module_id;
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['bank_account_module_id']    = $this->config->item('bank_account_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        $data['notes_module_id']           = $this->config->item('notes_module');

        $data['notes_sub_module_id']    = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['customer'] = $this->customer_call();

        if (in_array($data['bank_account_module_id'], $data['active_modules']))
        {
            $data['bank_account'] = $this->bank_account_call_new();
        }

        /*$data['currency'] = $this->currency_call();*/
        $data['data']  = $this->general_model->getRecords('*', 'sales', array(
            'sales_id' => $id));
        
        $access_settings = $data['access_settings'];
        $primary_id      = "receipt_id";
        $table_name      = $this->config->item('receipt_voucher_table');
        $date_field_name = "voucher_date";
        $current_date    = date('Y-m-d');

        $data['voucher_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['data'][0]->note1 = "";
        $data['data'][0]->note2 = "";
        $this->load->view('receipt_voucher/add_sales_receipt', $data);
    }

    public function convert_currency()
    {
        $id                 = $this->input->post('convert_currency_id');
        $id                 = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');

        $old_converted_receipt_amt = $this->general_model->getRecords('converted_receipt_amount', 'receipt_voucher', array(
            'receipt_id' => $id));
        $old_converted_receipt_array = explode(",", $old_converted_receipt_amt[0]->converted_receipt_amount);

        $receipt_data = $this->general_model->getRecords('receipt_amount,reference_id,reference_type', 'receipt_voucher', array(
            'receipt_id' => $id));
        $receipt_amount_data = explode(",", $receipt_data[0]->receipt_amount);

        foreach ($receipt_amount_data as $key => $value)
        {
            $array_receipt_amount[] = bcmul($value, $new_converted_rate, $section_modules['access_common_settings'][0]->amount_precision);
        }

        $converted_receipt_amount = implode(',', $array_receipt_amount);

        $sales_id_data = explode(",", $receipt_data[0]->reference_id);
        // $receipt_amount_data = explode(",", $receipt_data[0]->receipt_amount);

        $data = array(
            'currency_converted_rate'   => $new_converted_rate,
            'currency_converted_amount' => $this->input->post('currency_converted_amount'),
            'converted_receipt_amount'  => $converted_receipt_amount
        );

        $this->general_model->updateData('receipt_voucher', $data, array(
            'receipt_id' => $id));

        // $sales_id = $this->general_model->getRecords('reference_id,reference_type,receipt_amount', 'receipt_voucher', array('receipt_id' => $id));

        foreach ($sales_id_data as $key => $value)
        {
            $new_converted_amount = bcmul($receipt_amount_data[$key], $new_converted_rate, $section_modules['access_common_settings'][0]->amount_precision);

            $sales_data = $this->general_model->getRecords('*', 'sales', array(
                'sales_id' => $value));

            if ($old_converted_receipt_array[$key7] != 0)
            {
                $converted_paid_amount = bcsub($sales_data[0]->converted_paid_amount, $old_converted_receipt_array[$key], $section_modules['access_common_settings'][0]->amount_precision);

                $total_converted_paid_amount = bcadd($converted_paid_amount, $new_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);
            }
            else
            {
                $total_converted_paid_amount = bcadd($sales_data[0]->converted_paid_amount, $new_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);
            }

            $paid_converted_amount = array(
                'converted_paid_amount' => $total_converted_paid_amount);
            $where = array(
                'sales_id' => $value);
            $sales_table = $this->config->item('sales_table');
            $this->general_model->updateData($sales_table, $paid_converted_amount, $where);
        }

        //update converted_voucher_amount in  accounts_receipt_voucher table

        $accounts_receipt_voucher = $this->general_model->getRecords('*', 'accounts_receipt_voucher', array(
            'receipt_voucher_id' => $id,
            'delete_status'      => 0));

        foreach ($accounts_receipt_voucher as $key1 => $value1)
        {
            $new_converted_voucher_amount = bcmul($accounts_receipt_voucher[$key1]->voucher_amount, $new_converted_rate, $section_modules['access_common_settings'][0]->amount_precision);

            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount);
            $where = array(
                'accounts_receipt_id' => $accounts_receipt_voucher[$key1]->accounts_receipt_id);
            $voucher_table = "accounts_receipt_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }

        redirect('receipt_voucher', 'refresh');

    }

    public function view_details($id)
    {
        $receipt_voucher_id        = $this->encryption_url->decode($id);
        /*$receipt_voucher_id = 203;*/
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        // $email_sub_module_id             = $this->config->item('email_sub_module');
        /*echo $receipt_voucher_id;*/
        $voucher_details = $this->common->receipt_voucher_details($receipt_voucher_id);

        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
       /* print_r($this->db->last_query());*/
        /*SELECT `rv`.`voucher_number`, `rv`.`voucher_date`, `rv`.`reference_number`, `rv`.`receipt_amount`, `rv`.`converted_receipt_amount`, `rv`.`reference_id`, `av`.`accounts_receipt_id`, `av`.`cr_amount`, `av`.`dr_amount`, `l`.`ledger_title` as `from_name`, `lr`.`ledger_title` as `to_name`, `av`.`converted_voucher_amount`, `av`.`receipt_voucher_id` FROM `accounts_receipt_voucher` `av` JOIN `receipt_voucher` `rv` ON `rv`.`receipt_id` = `av`.`receipt_voucher_id` JOIN `ledgers` `l` ON `l`.`ledger_id` = `av`.`ledger_from` JOIN `ledgers` `lr` ON `lr`.`ledger_id`=`av`.`ledger_to` WHERE `av`.`receipt_voucher_id` = '180' AND `av`.`delete_status` = 0*/
          /*echo "<pre>";

         print_r($data['data']);
         exit;*/
        $this->load->view('receipt_voucher/view_details', $data);
    }


    public function email_popup($id)
    {
        $id                        = $this->encryption_url->decode($id);
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $data['module_id']         = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = "view_privilege";
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $email_sub_module = 0;

        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
        {
            $email_sub_module = 1;
        }

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
            /*$data['currency']  = $this->currency_call();*/
            /*$receipt_data      = $this->common->receipt_voucher_list_field1($id);
            $data['data']      = $this->general_model->getJoinRecords($receipt_data['string'], $receipt_data['table'], $receipt_data['where'], $receipt_data['join']);*/
            $this->db->select('r.*,rv.reference_id,rv.exchange_gain_loss,rv.exchange_gain_loss_type,rv.discount,rv.other_charges,rv.round_off,rv.round_off_icon,rv.receipt_amount as invoice_receipt_amount,c.*');
            $this->db->from('receipt_voucher r');
            $this->db->join('receipt_invoice_reference rv','r.receipt_id=rv.receipt_id','left');
            $this->db->join('customer c','r.party_id=c.customer_id','left');
            $this->db->where('r.receipt_id',$id);
            $d = $this->db->get();
            
            $data['data'] = $d->result();
            $currency_code = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency_code[0]->currency_code;
            $data['currency_text'] = $currency_code[0]->currency_name;
            $data['currency_symbol']   = $currency_code[0]->currency_symbol;
            $data['currency_symbol_pdf']   = $currency_code[0]->currency_symbol_pdf;
            $data['data'][0]->unit = $currency_code[0]->unit;
            $data['data'][0]->decimal_unit = $currency_code[0]->decimal_unit;            
            $service = 0;
            $bank_exist = 0;
            if(!empty($data['data'])){
                if ($data['data'][0]->payment_mode != "" && $data['data'][0]->payment_mode != "cash" && $data['data'][0]->payment_mode != "other payment mode" && $data['data'][0]->payment_mode != "bank"){
                    $bank_exist = 1;
                }
                $customer_id = $data['data'][0]->party_id;
                $this->db->select('*');
                $this->db->where('sales_party_id',$customer_id);
                $s_data = $this->db->get('sales');
                $s_data = $s_data->result();
                $data['sales_data'] = $s_data;
                $sales_data = array();
                foreach ($s_data as $key => $value) {
                    $sales_data['sales_'.$value->sales_id] = $value;
                }
            }

            $sales_data_1 = array();
            if(!empty($sales_data)){
                foreach ($data['data'] as $key => $value) {
                    if(array_key_exists('sales_'.$value->reference_id, $sales_data)){
                        $sales_invoice = $sales_data['sales_'.$value->reference_id];
                        $this->db->select('receipt_id');
                        $this->db->from('receipt_invoice_reference');
                        $this->db->where('reference_id',$value->reference_id);
                        $this->db->order_by('receipt_invoice_id','DESC');
                        $this->db->limit(1);
                        $last_re = $this->db->get();
                        $last_receipt = $last_re->result();

                        $is_edit = 0;
                        if($last_receipt[0]->receipt_id == $value->receipt_id)$is_edit = 1;
                        $invoice_total = $sales_invoice->sales_grand_total;
                        $paid_amount = $sales_invoice->sales_paid_amount;
                        $pending_amount = $invoice_total - $paid_amount;
                        $data['data'][$key]->invoice_total = $invoice_total;
                        $data['data'][$key]->invoice_paid_amount = $paid_amount;
                        $data['data'][$key]->invoice_pending = $pending_amount;
                        $data['data'][$key]->reference_number = $sales_invoice->sales_invoice_number;
                        $data['data'][$key]->is_edit = $is_edit;
                    }
                }
            }
            /*$data['invoice_data'] = $sales_data_1;
            $data['bank_exist'] = $bank_exist;*/
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

            $html = $this->load->view('receipt_voucher/pdf', $data, true);
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
            /*include APPPATH . 'third_party/mpdf60/mpdf.php';
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $mpdf->WriteHTML($html);*/
            /*$mpdf->Output($file_path . $file_name . '.pdf', 'F');*/
            $file_path                      = "././pdf_form/";
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
            $receipt_voucher_data  = $this->common->receipt_voucher_list_field1($id);
            $data['data']          = $this->general_model->getJoinRecords($receipt_voucher_data['string'], $receipt_voucher_data['table'], $receipt_voucher_data['where'], $receipt_voucher_data['join']);
            $branch_data           = $this->common->branch_field();
            $data['branch']        = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']   = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id'     => $receipt_voucher_module_id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $data['data'][0]->pdf_file_path = $data['pdf_file_path'];
            $data['data'][0]->pdf_file_name = $data['pdf_file_name'];
            $data['data'][0]->email_template = $data['email_template'];
            $data['data'][0]->firm_name = $data['branch'][0]->firm_name;
            $result = json_encode($data['data']);
            echo $result;

    }
}
