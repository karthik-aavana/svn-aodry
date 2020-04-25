<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales extends MY_Controller{
    public $company_type = 'common';
    function __construct(){
        parent::__construct();
        $this->load->model([
            'general_model' ,
            'product_model' ,
            'service_model' ,
            'Voucher_model' ,
            'ledger_model' ]);
        
        //$this->load->library('sales_lib');
        /*$this->load->library('ProductHook');*/
        $this->modules = $this->get_modules();
        if($this->session->userdata('SESS_BRANCH_ID') == 76){
            $this->company_type = 'pharma';
        }
    }
    
    function index(){
        $sales_module_id        = $this->config->item('sales_module');
        $data['sales_module_id'] = $sales_module_id;
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;
        $section_modules        = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /* Modules Present */
        $data['sales_module_id']           = $sales_module_id;
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id']           = $this->config->item('email_module');
        $data['sales_voucher_module']      = $this->config->item('sales_voucher_module');
        $data['recurrence_module_id']      = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id']  = $this->config->item('recurrence_sub_module');
        
        if (!empty($this->input->post())){
            $columns             = array(
                                    0 => 's.sales_id',
                                    1 => 's.sales_date',
                                    2 => 'c.customer_name',
                                    3 => 's.sales_grand_total'
                                    /*3 => 'converted_grand_total' ,
                                    4 => 'paid_amount' ,
                                    5 => 'payment_status' ,
                                    6 => 'pending_amount' ,
                                    7 => 'added_user' ,
                                    9 => 'billing_currency'*/
                                );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->sales_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])){
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } 
            $send_data = array();
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code']     = $currency[0]->currency_code;
            $data['currency_symbol']   = $currency_symbol = $currency[0]->currency_symbol;
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $billing_currency = $post->currency_symbol;
                    $nestedData['billing_currency'] = $billing_currency." (".round($post->currency_converted_rate,2).")";
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $nestedData['date']        = date('d-m-Y', strtotime($post->sales_date));
                    $sales_invoice_number = $post->sales_invoice_number;
                    if($post->sales_brand_invoice_number != '') $sales_invoice_number = $post->sales_brand_invoice_number;
                    $nestedData['customer']    = $post->customer_name . ' (<a href="' . base_url('sales/view/') . $sales_id . '">' . $sales_invoice_number . '</a>)<br> ';
                    $nestedData['grand_total'] = $currency_symbol . ' ' . $this->precise_amount($post->sales_grand_total , $access_common_settings[0]->amount_precision) . ' (INV)';
                    if ($post->credit_note_amount > 0) {
                        $nestedData['grand_total'] .= '<br>' . $currency_symbol . ' ' . $this->precise_amount($post->credit_note_amount , $access_common_settings[0]->amount_precision) . ' (CN)';
                    }
                    if ($post->debit_note_amount > 0) {
                        $nestedData['grand_total'] .= '<br>' . $currency_symbol . ' ' . $this->precise_amount($post->debit_note_amount , $access_common_settings[0]->amount_precision) . ' (DN)';
                    }
                    /* get customer ledger ID */
                    $customer_ledger_id = $post->ledger_id;
                    $sales_ledg = $this->config->item('sales_ledger');
                    if(!$customer_ledger_id){
                        $customer_ledger_id = $sales_ledg['CUSTOMER'];
                        $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($customer_ledger_id);
                            
                        $customer_ary = array(
                                        'ledger_name' => $post->customer_name,
                                        'second_grp' => '',
                                        'primary_grp' => 'Sundry Debtors',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => 0,
                                        'default_value' => $post->customer_name,
                                        'amount' => 0
                                    );
                        if(!empty($customer_ledger_name)){
                            $customer_ledger = $customer_ledger_name->ledger_name;
                            /*$customer_ledger = str_ireplace('{{SECTION}}',$section_name , $customer_ledger);*/
                            $customer_ledger = str_ireplace('{{X}}', $post->customer_name, $customer_ledger);
                            $customer_ary['ledger_name'] = $customer_ledger;
                            $customer_ary['primary_grp'] = $customer_ledger_name->sub_group_1;
                            $customer_ary['second_grp'] = $customer_ledger_name->sub_group_2;
                            $customer_ary['main_grp'] = $customer_ledger_name->main_group;
                            $customer_ary['default_ledger_id'] = $customer_ledger_name->ledger_id;
                        }
                        $customer_ledger_id = $this->ledger_model->getGroupLedgerId($customer_ary);
                    }
                    /*$customer_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                                        'ledger_name' => $post->customer_name,
                                                        'subgrp_2' => 'Sundry Debtors',
                                                        'subgrp_1' => '',
                                                        'main_grp' => 'Current Assets',
                                                        'amount' =>  0
                                        ));*/


                    $this->db->select('sales_voucher_id');
                    $this->db->from('sales_voucher');
                    $this->db->where('reference_id',$post->sales_id);
                    $this->db->where('delete_status',0);
                    $this->db->where('reference_type','sales');
                    $get_sv_qry = $this->db->get();
                    $ref_id = $get_sv_qry->result();
                    $sales_voucher_id = '';
                    if(!empty($ref_id)){
                        $sales_voucher_id = $ref_id[0]->sales_voucher_id;
                    }
                    
                    $this->db->select('voucher_amount,voucher_type');
                    $this->db->from('sales s');
                    $this->db->join('sales_voucher sv','s.sales_id=sv.reference_id','left');
                    $this->db->join('accounts_sales_voucher a','sv.sales_voucher_id=a.sales_voucher_id','left');
                    $this->db->where('s.sales_id',$post->sales_id);
                    $this->db->where('sv.delete_status',0);
                    $this->db->where('sv.reference_type','sales');
                    $this->db->where('a.ledger_id',$customer_ledger_id);
                    
                    $get_customer_qry = $this->db->get();
                    $sales_ledgers = $get_customer_qry->result();
                    $this->db->select('voucher_amount,voucher_type,sales_credit_note_id,sales_credit_note_invoice_number');
                    $this->db->from('sales_credit_note s');
                    $this->db->join('sales_voucher sv','s.sales_credit_note_id=sv.reference_id','left');
                    $this->db->join('accounts_sales_voucher a','sv.sales_voucher_id=a.sales_voucher_id','left');
                    $this->db->where('s.sales_id',$post->sales_id);
                    $this->db->where('sv.delete_status',0);
                    $this->db->where('sv.reference_type','sales_credit_note');
                    $this->db->where('a.ledger_id',$customer_ledger_id);
                    
                    $get_customer_qry = $this->db->get();
                    $sales_credit_ledgers = $get_customer_qry->result();
                   
                    $this->db->select('voucher_amount,voucher_type,reference_type,sales_debit_note_id, sales_debit_note_invoice_number');
                    $this->db->from('sales_debit_note s');
                    $this->db->join('sales_voucher sv','s.sales_debit_note_id=sv.reference_id','left');
                    $this->db->join('accounts_sales_voucher a','sv.sales_voucher_id=a.sales_voucher_id','left');
                    $this->db->where('s.sales_id',$post->sales_id);
                    $this->db->where('sv.delete_status',0);
                    $this->db->where('sv.reference_type','sales_debit_note');
                    $this->db->where('a.ledger_id',$customer_ledger_id);
                    
                    $get_customer_qry = $this->db->get();
                    /*print_r($this->db->last_query());*/
                    $sales_debit_ledgers = $get_customer_qry->result();
                     
                    /* calculate total receiable and net recevable */
                    $net_receivable = 0;
                    $total_receivable = array();
                    $nestedData['total_receivable'] = '';
                    if(!empty($sales_ledgers)){
                        foreach ($sales_ledgers as $k => $led) {
                            $net_receivable += $led->voucher_amount;
                            array_push($total_receivable, $this->precise_amount($led->voucher_amount,$access_common_settings[0]->amount_precision).'(INV)');
                        }
                    }
                    if(!empty($sales_credit_ledgers)){
                        foreach ($sales_credit_ledgers as $key => $led) {
                            $sales_credit_note_id = $this->encryption_url->encode($led->sales_credit_note_id);
                            $nestedData['customer'] .=  ' (<a href="' . base_url('sales_credit_note/view/') . $sales_credit_note_id . '">' . $led->sales_credit_note_invoice_number . '</a>)<br>';
                            array_push($total_receivable, $this->precise_amount($led->voucher_amount,$access_common_settings[0]->amount_precision).'(CN)');
                            $net_receivable -= $led->voucher_amount;
                        }
                    }
                    if(!empty($sales_debit_ledgers)){
                        /*print_r($sales_debit_ledgers);*/
                        foreach ($sales_debit_ledgers as $key => $led) {
                            $sales_debit_note_id = $this->encryption_url->encode($led->sales_debit_note_id);
                            $nestedData['customer'] .=  ' (<a href="' . base_url('sales_debit_note/view/') . $sales_debit_note_id . '">' . $led->sales_debit_note_invoice_number . '</a>)<br>';
                            array_push($total_receivable, $this->precise_amount($led->voucher_amount,$access_common_settings[0]->amount_precision).'(DN)');
                            $net_receivable += $led->voucher_amount;
                        }
                    }
                    $nestedData['total_receivable'] = implode("<br>", $total_receivable);
                    $nestedData['net_receivable'] =$this->precise_amount($net_receivable , $access_common_settings[0]->amount_precision);
                    
                    /*if($sales_voucher_id != ''){
                        $sales_voucher_id = $this->encryption_url->encode($sales_voucher_id);

                        $nestedData['sales_voucher_view'] = ' <a href="' .base_url('sales_voucher/view_details/') . $sales_voucher_id.'" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View Voucher">' . '<i class="fa fa-eye" aria-hidden="true"></i>' . '</a>'. '  ' .' <form class="form-style" action="' .base_url('sales_ledger').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$sales_voucher_id.'"> <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="View Ledger"><button type="submit" class="sales_action">' . '<i class="fa fa-eye" aria-hidden="true"></i></button></a></form>';
                    }
                    */
                    /* get advance total */
                    $this->db->select('SUM(adjusted_amount) as adjusted_amount');
                    $this->db->where('sales_id',$post->sales_id);
                    $advance_qry = $this->db->get('advance_paid_history');
                    $advance_total = $advance_qry->result();
                    $this->db->select('receipt_amount,receipt_total_paid');
                    $this->db->where('reference_id',$post->sales_id);
                    $this->db->where('delete_status','0');
                    $receipt_qry = $this->db->get('receipt_invoice_reference');
                    $receipt_voucher = $receipt_qry->result();
                    $receipt = array();
                    $advance_receipt = array();
                    $total_recived = 0;
                    if(!empty($advance_total)){
                        foreach ($advance_total as $key => $value) {
                            if($value->adjusted_amount > 0){
                                $total_recived += $value->adjusted_amount;
                                array_push($receipt, $this->precise_amount($value->adjusted_amount , $access_common_settings[0]->amount_precision)."(ADV)");
                                array_push($advance_receipt, $this->precise_amount($value->adjusted_amount , $access_common_settings[0]->amount_precision)."(ADV)");
                            }
                        }
                    }
                    if(!empty($receipt_voucher)){
                        foreach ($receipt_voucher as $key => $value) {
                            $total_recived += $value->receipt_total_paid;
                            array_push($receipt, $this->precise_amount($value->receipt_total_paid, $access_common_settings[0]->amount_precision)."(RCP)");
                        }
                    }
                    $nestedData['received_amount'] = '0.00';
                    if(!empty($receipt)){
                        $nestedData['received_amount'] = implode("<br>", $receipt);
                    }
                    $nestedData['converted_grand_total'] = $this->precise_amount($post->converted_grand_total , $access_common_settings[0]->amount_precision);
                    $nestedData['added_user']     = $post->first_name . ' ' . $post->last_name;
                    $pending_amount = $net_receivable - $total_recived;
                    if($pending_amount < 0){
                        $pending_amount += $post->excess_return;
                    }                    
                    $nestedData['pending_amount'] = '0.00';
                    $nestedData['pending_amount'] = $this->precise_amount(($pending_amount), $access_common_settings[0]->amount_precision);
                    $excess_data = $this->getExcessAmount($post->sales_id);
                    $excess_ary = array();
                    $total_excess = 0;
                    if(!empty($excess_data)){
                        foreach ($excess_data as $key => $value) {
                            $total_excess += $value->excess_amount;
                            /*array_push($excess_ary, $this->precise_amount(($value->excess_amount), $access_common_settings[0]->amount_precision)."(EXC)");*/
                            array_push($excess_ary, $value->excess_id);
                        }
                    }
                    $is_pending_minus = false;
                    if (round($pending_amount,2) < 0){
                        $total_excess += abs($pending_amount);
                        $nestedData['payment_status'] = '<span class="label label-success">Excess Received</span>';
                        $is_pending_minus = true;
                    }elseif (round($pending_amount,2) == 0){
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }else if ($net_receivable > $pending_amount){
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }else{
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($sales_module_id , $data['active_view']))
                    {
                        $cols .= '<span><a href="' . base_url('sales/view/') . $sales_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="View Sales"><i class="fa fa-eye"></i></a></span>';
                    }
                    if (in_array($sales_module_id , $data['active_edit']))
                    {
                        if ($post->sales_paid_amount == 0 && $post->credit_note_amount == 0 && $post->debit_note_amount == 0)
                        {
                            $cols .= '<span><a href="' . base_url('sales/edit/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Edit Sales"><i class="fa fa-pencil"></i></a></span>';
                        }
                    }
                    if (in_array($data['receipt_voucher_module_id'] , $data['active_add']))
                    {
                        if ($pending_amount > 0) {
                            $cols .= '<span><a href="' . base_url('receipt_voucher/add_sales_receipt/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Receive Payment"><i class="fa fa-money"></i></a></span>';
                        }
                    }
                    if (in_array($data['advance_voucher_module_id'] , $data['active_view']))
                    {
                        if(!empty($advance_receipt))
                        $cols .= '<span><a class="btn btn-app get_advance" data-toggle="tooltip" data-placement="bottom" title="Advance Vouchers"><i class="fa fa-file-text-o" aria-hidden="true"></i></a></span>';    
                    }
                    /*$cols .= '<span><a data-backdrop="static" data-keyboard="false" href="javascript:void(0);" data-toggle="tooltip" onclick="addToModel(' . $post->sales_id . ')" class="btn btn-app" title="Follow Up Dates"  data-placement="bottom"><i class="fa fa-book"></i></a></span>';*/
                    $cols .= '<span><a href="javascript:void(0);" class="btn btn-app get_excess" data-toggle="tooltip" data-id="' . $sales_id . '" data-placement="bottom" title="Excess History"><i class="fa fa-history"></i></a></span>';  
                    if (in_array($sales_module_id , $data['active_view']))
                    {
                        $customer_currency_code = $this->getCurrencyInfo($post->currency_id);
                        $customer_curr_code = '';
                        if(!empty($customer_currency_code))
                        $customer_curr_code     = $customer_currency_code[0]->currency_code;
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#pdf_type_modal"><a href1="'.base_url('sales/pdf/') . $sales_id .'"  class="btn btn-app pdf_button" b_curr="'.$this->session->userdata('SESS_DEFAULT_CURRENCY').'"  b_code="'.$data['currency_code'].'" c_code="'.$customer_curr_code.'" c_curr="'.$post->currency_id.'" data-id="' . $sales_id . '" data-name="regular" href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }
                    if (in_array($data['email_module_id'] , $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'] , $data['access_sub_modules']))
                        {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail"><a data-id="' . $sales_id . '" data-name="regular" href="javascript:void(0);" class="btn btn-app pdf_button composeMail" data-toggle="tooltip" data-placement="bottom" title="Email Sales"><i class="fa fa-envelope-o"></i></a></span>';
                        }
                    }
                    if($total_excess > 0){
                        $nestedData['pending_amount'] = ($is_pending_minus == true ? '-':'').$total_excess.'(EXC)';
                        if (in_array($data['advance_voucher_module_id'] , $data['active_add']))
                        {
                            $excess_ids = 0;
                            if(!empty($excess_ary)) $excess_ids = implode(',', $excess_ary);
                            $cols .= '<span data-backdrop="static" data-keyboard="false" class="get_excess_amount" data-id="' . $sales_id . '"> <input type="hidden" value="'.$total_excess.'" name="excess_amount"><input type="hidden" value="'.$excess_ids.'" name="excess_ids"><a href="#" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Excess Amount"> <i class="fa fa-money"></i></a></span>';
                        }
                    }

                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $conversion_date = $post->currency_converted_date;
                        if($conversion_date == '0000-00-00') $conversion_date = $post->added_date;
                        $conversion_date = date('d-m-Y',strtotime($conversion_date));
                        
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#convert_currency_modal"><a href="javascript:void(0);" class="btn btn-app convert_currency" data-id="' . $sales_id . '" data-path="sales/convert_currency" data-conversion_date="'.$conversion_date.'" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $this->precise_amount($post->sales_grand_total, $access_common_settings[0]->amount_precision) . '" data-rate="' . $this->precise_amount($post->currency_converted_rate, $access_common_settings[0]->amount_precision) . '" data-toggle="tooltip" data-placement="bottom" title="Convert Currency"><i class="fa fa-exchange"></i></a></span>';
                    }

                    if($sales_voucher_id != ''){
                        $sales_voucher_id = $this->encryption_url->encode($sales_voucher_id);
                        if(in_array($data['sales_voucher_module'], $data['active_view'])){
                            $cols .= '<span><a href="' .base_url('sales_voucher/view_details/') . $sales_voucher_id.'" target="_blank" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="View Voucher"><i class="fa fa-eye"></i></a></span>';

                            $cols .= '<span><form action="' .base_url('sales_ledger').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$sales_voucher_id.'"> <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" class="btn btn-app" title="View Ledger"><button type="submit" class="sales_action">' . '<i class="fa fa-eye" aria-hidden="true"></i></button></a></form></span>';
                        }
                    }

                    if (in_array($sales_module_id , $data['active_delete']))
                    {
                        if($post->sales_paid_amount == 0){
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $sales_id . '" data-path="sales/delete" class="delete_button" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" ><a  href="javascript:void(0);" class="btn btn-app " data-toggle="tooltip" data-placement="bottom" title="Delete Sales"><i class="fa fa-trash-o"></i></a></span>';
                        }
                    }
                   

                    $cols .= '<input type="hidden" value="'.$post->customer_id.'" name="customer_id">';
                    $cols .= '<input type="hidden" value="'.$post->sales_id.'" name="sales_id">';
                    $cols .= '</div>';                    
                    $cols .= '</div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal" value="'.$post->sales_id.'">';
                    $send_data[]= $nestedData;
                }
            }
            $json_data = array(
                "draw"            => intval($this->input->post('draw')) ,
                "recordsTotal"    => intval($totalData) ,
                "recordsFiltered" => intval($totalFiltered) ,
                "data"            => $send_data );
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            // $list_data        = $this->common->sales_list_field();
            //  $data['posts']    = $this->general_model->getPageJoinRecords($list_data);
            $this->load->view('sales/list' , $data);
        }
        
    }

    function getAdvanceNumber(){
        $json = array();
        $data = $this->get_default_country_state();
        $advance_voucher_module_id         = $this->config->item('advance_voucher_module');
        $data['module_id']                 = $advance_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "view_privilege";
        $data['privilege']                 = "view_privilege";
        $section_modules                   = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data  = array_merge($data , $section_modules);
        $sales_id = $this->encryption_url->decode($this->input->post('sales_id'));
        $access_settings          = $data['access_settings'];
        $primary_id               = "advance_voucher_id";
        $table_name               = $this->config->item('advance_voucher_table');
        $date_field_name          = "voucher_date";
        $current_date             = date('Y-m-d');
        $json['advance_voucher_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $general_voucher_module_id = $this->config->item('general_voucher_module');
        $section_modules           = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
        
        $access_settings           = $section_modules['access_settings'];
        $primary_id                = "general_voucher_id";
        $table_name                = $this->config->item('general_voucher_table');
        $date_field_name           = "voucher_date";
        $current_date              = date('Y-m-d');
        $json['general_voucher_number']= $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $json['date']   = date('Y-m-d');
        $json['excess_amount'] = 0;
        $json['excess_id'] = 0;
        $excess_data = $this->getExcessAmount($sales_id);
        if(!empty($excess_data)){
            $json['excess_amount'] = $this->precise_amount($excess_data[0]->excess_amount, 2); 
            $json['excess_id'] = $excess_data[0]->excess_id;  
        }
        echo json_encode($json);
    }

    function getExcessAmount($sales_id){
        $this->db->select('excess_amount,receipt_id,sales_id,excess_id');
        $this->db->where('sales_id',$sales_id);
        $this->db->where('delete_status','0');
        $this->db->where('is_used','0');
        $excess_qry = $this->db->get('sales_excess_amount');
        return $excess_qry->result();
    }

    function add()
    {
        $data              = $this->get_default_country_state();
        $sales_module_id   = $this->config->item('sales_module');
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data              = array_merge($data , $section_modules);
        /* Modules Present */
        $data['sales_module_id']           = $sales_module_id;
        $data['module_id']                 = $sales_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('product_module');
        $data['service_module_id']         = $this->config->item('service_module');
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['category_module_id']        = $this->config->item('category_module');
        $data['subcategory_module_id']     = $this->config->item('subcategory_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['customer'] = $this->customer_call();
        $data['currency'] = $this->currency_call();
        $data['brands'] = $this->brand_call();
        $data['shipping_address'] = $this->general_model->getRecords('*', 'shipping_address', array('delete_status' => 0,'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        /*if($this->company_type == 'pharma'){
            $currecny =$this->sales_lib->sales_type('pharma',$data);
        }else{*/
            if ($data['access_settings'][0]->discount_visible == "yes")
            {
                $data['discount'] = $this->discount_call();
            }
            if ($data['access_settings'][0]->tax_type == "gst" || $data['access_settings'][0]->item_access == "single_tax")
            {
                $data['tax'] = $this->tax_call();
            }
            if ($data['access_settings'][0]->item_access == "service" || $data['access_settings'][0]->item_access == "both")
            {
                $data['sac']              = $this->sac_call();
                $data['service_category'] = $this->service_category_call();
            }
            if ($data['access_settings'][0]->item_access == "product" || $data['access_settings'][0]->item_access == "both")
            {
                $data['inventory_access'] = $data['access_common_settings'][0]->inventory_advanced;
                $data['product_category'] = $this->product_category_call();
                $data['uqc']              = $this->uqc_call();
                $data['uqc_service']      = $this->uqc_product_service_call('service');
                $data['uqc_product']      = $this->uqc_product_service_call('product');
                $data['chapter']          = $this->chapter_call();
                $data['hsn']              = $this->hsn_call();
                $data['tax_tds']          = $this->tax_call_type('TDS');
                $data['tax_tcs']          = $this->tax_call_type('TCS');
                $data['tax_gst']          = $this->tax_call_type('GST');
                $data['tax_section'] = $this->tax_section_call();
                /*Customization for leather craft*/
                $data['department'] = $this->general_model->getRecords('*', 'department', array('delete_status' => 0,'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                if ($data['inventory_access'] == "yes")
                {
                    $data['get_product_inventory'] = $this->get_product_inventory();
                    $data['varients_key']          = $this->general_model->getRecords('*' , 'varients' , array(
                        'delete_status' => 0 ,
                        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                }
            }
            $access_settings        = $data['access_settings'];
            $primary_id             = "sales_id";
            $table_name             = $this->config->item('sales_table');
            $date_field_name        = "sales_date";
            $current_date           = date('Y-m-d');
            $data['invoice_number'] = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);
            $this->load->view('sales/add' , $data);
        /*}*/
        
    }

    public function getAllExcessDetail() {
        $sales_id = $this->encryption_url->decode($this->input->post('sales_id'));
       
        $this->db->select('v.*,h.*');
        $this->db->from('sales_excess_history h');
        $this->db->join('advance_voucher v', 'h.reference_id=v.advance_voucher_id');
        $this->db->where('h.sales_id', $sales_id);
        $this->db->where('h.reference_type', 'advance_voucher');
        $this->db->where('v.delete_status', '0');
        $result = $this->db->get();
        $history = $result->result();
       
        $tr = '';
        $send_data = array();
        if (!empty($history)) {
            foreach ($history as $key => $value) {
                $send_data[] = array(
                                    'voucher_number' => $value->voucher_number,
                                    'voucher_date' => ($value->voucher_date),
                                    'excess_amount' => $this->precise_amount($value->excess_amount, 2),
                );
            }
        }
        $this->db->select('v.*,h.*');
        $this->db->from('sales_excess_history h');
        $this->db->join('general_voucher v', 'h.reference_id=v.general_voucher_id');
        $this->db->where('h.sales_id', $sales_id);
        $this->db->where('h.reference_type', 'general_voucher');
        $this->db->where('v.delete_status', '0');
        $result = $this->db->get();
        $history = $result->result();
        if (!empty($history)) {
            foreach ($history as $key => $value) {
                $send_data[] = array(
                                    'voucher_number' => $value->voucher_number,
                                    'voucher_date' => ($value->voucher_date),
                                    'reference_type' => $value->reference_type,
                                    'excess_amount' => $this->precise_amount($value->excess_amount, 2),
                );
            }
        }
        echo json_encode($send_data);
    }

    function edit($id){
        $id                = $this->encryption_url->decode($id);
        /*echo $id;exit();*/
        $data              = $this->get_default_country_state();
        $sales_module_id   = $this->config->item('sales_module');
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data              = array_merge($data , $section_modules);
        
        /* Modules Present */
        $data['sales_module_id']           = $sales_module_id;
        $data['module_id']                 = $sales_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('product_module');
        $data['service_module_id']         = $this->config->item('service_module');
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['category_module_id']        = $this->config->item('category_module');
        $data['subcategory_module_id']     = $this->config->item('subcategory_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $data['data'] = $this->general_model->getRecords('*' , 'sales' , array(
            'sales_id' => $id ));
        $country  = $this->general_model->getRecords('*', 'countries', array('country_name' => 'india' ));
        $country_id = $country[0]->country_id;
        if($data['data'][0]->sales_billing_state_id == 0){
                $data['shipping_address'] = $this->general_model->getRecords('*' , 'shipping_address' , array(
                'shipping_party_id'   => $data['data'][0]->sales_party_id ,
                'shipping_party_type' => $data['data'][0]->sales_party_type ,
                'country_id!=' =>       $country_id
            ));
        }else{
                $data['shipping_address'] = $this->general_model->getRecords('*' , 'shipping_address' , array(
                'shipping_party_id'   => $data['data'][0]->sales_party_id ,
                'shipping_party_type' => $data['data'][0]->sales_party_type ,
                'state_id' =>       $data['data'][0]->sales_billing_state_id
                ));
        }
        $data['shipping_address'] = $this->general_model->getRecords('*' , 'shipping_address' , array(
            'shipping_party_id'   => $data['data'][0]->sales_party_id ,
            'shipping_party_type' => $data['data'][0]->sales_party_type ,
            'delete_status' => 0
        ));
        $item_types = $this->general_model->getRecords('item_type,sales_item_description' , 'sales_item' , array(
            'sales_id' => $id ));
        $service     = 0;
        $product     = 0;
        $description = 0;
        foreach ($item_types as $key => $value){
            if ($value->sales_item_description != "")
            {
                $description++;
            }
            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else if ($value->item_type == "product")
            {
                $product = 1;
            }
            else if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }
        }
        $data['product_exist'] = $product;
        $data['service_exist'] = $service;
        $data['customer'] = $this->customer_call();
        $data['currency'] = $this->currency_call();
        if ($data['data'][0]->sales_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax"){
            $data['tax'] = $this->tax_call();
        }
        if ($data['data'][0]->sales_nature_of_supply == "service" || $data['data'][0]->sales_nature_of_supply == "both"){
            $data['sac']              = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }
        if ($data['data'][0]->sales_nature_of_supply == "product" || $data['data'][0]->sales_nature_of_supply == "both") {
            if ($product == 2){
                $data['inventory_access'] = "yes";
            }else {
                $data['inventory_access'] = "no";
            }
            $data['product_category'] = $this->product_category_call();
            $data['uqc']              = $this->uqc_call();
            $data['uqc_service']      = $this->uqc_product_service_call('service');
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['chapter']          = $this->chapter_call();
            $data['hsn']              = $this->hsn_call();
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['brands'] = $this->brand_call();
            $data['tax_section'] = $this->tax_section_call();
            if ($data['inventory_access'] == "yes"){
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key']          = $this->general_model->getRecords('*' , 'varients' , array(
                    'delete_status' => 0 ,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
            }
        }
        $sales_service_items = array();
        $sales_product_items = array();
        if (($data['data'][0]->sales_nature_of_supply == "service" || $data['data'][0]->sales_nature_of_supply == "both") && $service == 1)
        {
            $service_items       = $this->common->sales_items_service_list_field($id);
            $sales_service_items = $this->general_model->getJoinRecords($service_items['string'] , $service_items['table'] , $service_items['where'] , $service_items['join']);
        }
        if ($data['data'][0]->sales_nature_of_supply == "product" || $data['data'][0]->sales_nature_of_supply == "both")
        {
            /*if ($product == 2)
            {
                $product_items       = $this->common->sales_items_product_inventory_list_field($id);
                $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
            }
            else */
            if ($product == 1)
            {
                $product_items       = $this->common->sales_items_product_list_field($id);
                $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
                if($data['data'][0]->brand_id != 0){
                    $brand_data = $this->db->query('SELECT invoice_readonly FROM brand WHERE brand_id='.$data['data'][0]->brand_id);
                    $data['brand_data'] = $brand_data->result();
                }
            }
        }
        $data['items'] = array_merge($sales_product_items , $sales_service_items);
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;
        if ($data['data'][0]->sales_tax_amount > 0 && $data['data'][0]->sales_igst_amount > 0 && ($data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;
            /*$data['data'][0]->sales_igst_amount = $data['data'][0]->sales_igst_amount + $data['data'][0]->total_other_taxable_amount;*/
        }
        elseif ($data['data'][0]->sales_tax_amount > 0 && ($data['data'][0]->sales_cgst_amount > 0 || $data['data'][0]->sales_sgst_amount > 0) && $data['data'][0]->sales_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
            /*$tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            $data['data'][0]->sales_cgst_amount = $data['data'][0]->sales_cgst_amount + ($data['data'][0]->total_other_taxable_amount * $cgst_amount_percentage / 100);
            $data['data'][0]->sales_sgst_amount = $data['data'][0]->sales_sgst_amount + ($data['data'][0]->total_other_taxable_amount * $sgst_amount_percentage / 100);*/
        }
        elseif ($data['data'][0]->sales_tax_amount > 0 && ($data['data'][0]->sales_igst_amount == 0 && $data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount == 0 && ($data['data'][0]->sales_igst_amount == 0 && $data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }
        if($data['data'][0]->sales_tax_cess_amount > 0){
            $cessExist = 1;
        }
        if ($data['data'][0]->sales_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }
        /*if ($data['data'][0]->sales_tds_amount > 0 || $data['data'][0]->sales_tcs_amount > 0 || $data['access_settings'][0]->tds_visible == "yes")
        {*/
        if ($data['data'][0]->sales_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }
        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->sales_billing_state_id);
        
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']        = $cessExist;
        $data['is_utgst']           = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
         /*Customization for leather craft*/
        $data['department'] = $this->general_model->getRecords('*', 'department', array('delete_status' => 0,'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        $department_id = $data['data'][0]->department_id;
         $data['sub_department'] = $this->general_model->getRecords('*', 'sub_department', array('delete_status'  => 0, 'department_id'   => $department_id, 'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        /*echo "<pre>";
        print_r($data);
        exit();*/
        $this->load->view('sales/edit' , $data);
    }

    public function get_sales_suggestions($term , $inventory_advanced , $item_access , $brand_id= '')
    {
        /*echo $term;*/
        /*$sales_module_id   = $this->config->item('sales_module');
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($sales_module_id , $modules , $privilege);*/
        //echo $inventory_access[0]->inventory_advanced;
        /*if ($inventory_advanced == "yes")
        {
            $suggestions_query = $this->common->item_inventory_suggestions_field($item_access , $term);
            $data              = $this->general_model->getQueryRecords($suggestions_query);
        }
        else
        {
        }*/
        if($term == '-') $term ='';
        $suggestions_query = $this->common->item_suggestions_field($item_access , $term , $brand_id);

        $data              = $this->general_model->getQueryRecords($suggestions_query);
        // $data["product_inventoery"]=$inventory_access[0]->inventory_advanced;
        echo json_encode($data);
    }

    public function get_table_items($code)
    {
        /* 0-id, 1-type, 2-discount, 3-tax , */
        $sales_module_id   = $this->config->item('sales_module');
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = $privilege;
        /*$section_modules   = $this->get_section_modules($sales_module_id , $modules , $privilege);*/
        $item_code = explode("-" , $code);
        if ($item_code[1] == "service"){
            $service_data = $this->common->service_field($item_code[0]);
            $data = $this->general_model->getJoinRecords($service_data['string'] , $service_data['table'] , $service_data['where'] , $service_data['join']);
        } else {
            $product_data = $this->common->product_field($item_code[0]);
            $data         = $this->general_model->getJoinRecords($product_data['string'] , $product_data['table'] , $product_data['where'] , $product_data['join']);
        }
        /*else if ($item_code[1] == "product_inventory") {
            $product_inventory_data = $this->common->product_inventory_field($item_code[0]);
            $data                   = $this->general_model->getJoinRecords($product_inventory_data['string'] , $product_inventory_data['table'] , $product_inventory_data['where'] , $product_inventory_data['join']);
        }*/
        $discount_data = array();
        $tax_data      = array();
        $tds_data      = array();
        if ($item_code[2] == 'yes')
        {
            $discount_data = $this->discount_call();
        }
        /*if ($item_code[3] == 'gst' || $item_code[3] == 'single_tax')
        {*/
            $tax_data = $this->tax_call();
        /*}*/
        $data['discount']          = $discount_data;
        $data['tax']               = $tax_data;
        $branch_details            = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id']   = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id']         = $branch_details['branch'][0]->branch_id;
        $data['item_id']           = $item_code[0];
        $data['item_type']         = $item_code[1];
        echo json_encode($data);
    }

    public function add_sales()
    {
        /*echo "<pre>";
        print_r($this->input->post());
        exit;*/

        $data            = $this->get_default_country_state();
        $sales_module_id = $this->config->item('sales_module');
        $module_id       = $sales_module_id;
        $modules         = $this->modules;
        $privilege       = "add_privilege";
        $section_modules = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data            = array_merge($data , $section_modules);
        /* Modules Present */
        $data['sales_module_id']           = $sales_module_id;
        $data['module_id']                 = $sales_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('product_module');
        $data['service_module_id']         = $this->config->item('service_module');
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['category_module_id']        = $this->config->item('category_module');
        $data['subcategory_module_id']     = $this->config->item('subcategory_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $access_settings = $section_modules['access_settings'];
        $currency = $this->input->post('currency_id');
        if ($access_settings[0]->invoice_creation == "automatic"){
            $primary_id      = "sales_id";
            $table_name      = $this->config->item('sales_table');
            $date_field_name = "sales_date";
            $current_date    = date('Y-m-d',strtotime($this->input->post('invoice_date')));
            $invoice_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }
        $customer   = explode("-" , $this->input->post('customer'));
        $total_cess_amnt= $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0 ;
        $sales_data = array(
            "sales_date"                            => date('Y-m-d',strtotime($this->input->post('invoice_date'))),
            "sales_invoice_number"                  => $invoice_number ,
            "sales_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
            "sales_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
            "sales_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
            "sales_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
            "sales_tax_cess_amount"                 => 0 ,
            "sales_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
            "sales_tds_amount"                      => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0 ,
            "sales_tcs_amount"
                      => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0 ,
            "sales_igst_amount"                     => 0 ,
            "sales_cgst_amount"                     => 0 ,
            "sales_sgst_amount"                     => 0 ,
            "from_account"                          => 'customer' ,
            "to_account"                            => 'sales' ,
            "sales_paid_amount"                     => 0 ,
            "credit_note_amount"                    => 0 ,
            "debit_note_amount"                     => 0 ,
            "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
            "sales_party_id"                        => $this->input->post('customer') ,
            "ship_to_customer_id"                   => $this->input->post('ship_to') ,
            "sales_party_type"                      => "customer" ,
            "sales_nature_of_supply"                => $this->input->post('nature_of_supply') ,
            "sales_order_number"                    => $this->input->post('order_number') ,
            "sales_type_of_supply"                  => $this->input->post('type_of_supply') ,
            "sales_gst_payable"                     => $this->input->post('gst_payable') ,
            "sales_billing_country_id"              => $this->input->post('billing_country') ,
            "sales_billing_state_id"                => $this->input->post('billing_state') ,
            "added_date"                            => date('Y-m-d') ,
            "added_user_id"                         => $this->session->userdata('SESS_USER_ID') ,
            "branch_id"                             => $this->session->userdata('SESS_BRANCH_ID') ,
            "currency_id"                           => $this->input->post('currency_id') ,
            "updated_date"                          => "" ,
            "updated_user_id"                       => "" ,
            "warehouse_id"                          => "" ,
            "transporter_name"                      => $this->input->post('transporter_name') ,
            "transporter_gst_number"                => $this->input->post('transporter_gst_number') ,
            "lr_no"                                 => $this->input->post('lr_no') ,
            "vehicle_no"                            => $this->input->post('vehicle_no') ,
            "mode_of_shipment"                      => $this->input->post('mode_of_shipment') ,
            "ship_by"                               => $this->input->post('ship_by') ,
            "net_weight"                            => $this->input->post('net_weight') ,
            "gross_weight"                          => $this->input->post('gross_weight') ,
            "origin"                                => $this->input->post('origin') ,
            "destination"                           => $this->input->post('destination') ,
            "shipping_type"                         => $this->input->post('shipping_type') ,
            "shipping_type_place"                   => $this->input->post('shipping_type_place') ,
            "lead_time"                             => $this->input->post('lead_time') ,
            "shipping_address_id"                   => $this->input->post('shipping_address') ,
            "warranty"                              => $this->input->post('warranty') ,
            "payment_mode"                          => $this->input->post('payment_mode') ,
            "billing_address_id" => $this->input->post('billing_address') ,
            "freight_charge_amount"                 => $this->input->post('freight_charge_amount') ? (float) $this->input->post('freight_charge_amount') : 0 ,
            "freight_charge_tax_percentage"         => $this->input->post('freight_charge_tax_percentage') ? (float) $this->input->post('freight_charge_tax_percentage') : 0 ,
            "freight_charge_tax_amount"             => $this->input->post('freight_charge_tax_amount') ? (float) $this->input->post('freight_charge_tax_amount') : 0 ,
            "total_freight_charge"                  => $this->input->post('total_freight_charge') ? (float) $this->input->post('total_freight_charge') : 0 ,
            "insurance_charge_amount"               => $this->input->post('insurance_charge_amount') ? (float) $this->input->post('insurance_charge_amount') : 0 ,
            "insurance_charge_tax_percentage"       => $this->input->post('insurance_charge_tax_percentage') ? (float) $this->input->post('insurance_charge_tax_percentage') : 0 ,
            "insurance_charge_tax_amount"           => $this->input->post('insurance_charge_tax_amount') ? (float) $this->input->post('insurance_charge_tax_amount') : 0 ,
            "total_insurance_charge"                => $this->input->post('total_insurance_charge') ? (float) $this->input->post('total_insurance_charge') : 0 ,
            "packing_charge_amount"                 => $this->input->post('packing_charge_amount') ? (float) $this->input->post('packing_charge_amount') : 0 ,
            "packing_charge_tax_percentage"         => $this->input->post('packing_charge_tax_percentage') ? (float) $this->input->post('packing_charge_tax_percentage') : 0 ,
            "packing_charge_tax_amount"             => $this->input->post('packing_charge_tax_amount') ? (float) $this->input->post('packing_charge_tax_amount') : 0 ,
            "total_packing_charge"                  => $this->input->post('total_packing_charge') ? (float) $this->input->post('total_packing_charge') : 0 ,
            "incidental_charge_amount"              => $this->input->post('incidental_charge_amount') ? (float) $this->input->post('incidental_charge_amount') : 0 ,
            "incidental_charge_tax_percentage"      => $this->input->post('incidental_charge_tax_percentage') ? (float) $this->input->post('incidental_charge_tax_percentage') : 0 ,
            "incidental_charge_tax_amount"          => $this->input->post('incidental_charge_tax_amount') ? (float) $this->input->post('incidental_charge_tax_amount') : 0 ,
            "total_incidental_charge"               => $this->input->post('total_incidental_charge') ? (float) $this->input->post('total_incidental_charge') : 0 ,
            "inclusion_other_charge_amount"         => $this->input->post('inclusion_other_charge_amount') ? (float) $this->input->post('inclusion_other_charge_amount') : 0 ,
            "inclusion_other_charge_tax_percentage" => $this->input->post('inclusion_other_charge_tax_percentage') ? (float) $this->input->post('inclusion_other_charge_tax_percentage') : 0 ,
            "inclusion_other_charge_tax_amount"     => $this->input->post('inclusion_other_charge_tax_amount') ? (float) $this->input->post('inclusion_other_charge_tax_amount') : 0 ,
            "total_inclusion_other_charge"          => $this->input->post('total_other_inclusive_charge') ? (float) $this->input->post('total_other_inclusive_charge') : 0 ,
            "exclusion_other_charge_amount"         => $this->input->post('exclusion_other_charge_amount') ? (float) $this->input->post('exclusion_other_charge_amount') : 0 ,
            "exclusion_other_charge_tax_percentage" => $this->input->post('exclusion_other_charge_tax_percentage') ? (float) $this->input->post('exclusion_other_charge_tax_percentage') : 0 ,
            "exclusion_other_charge_tax_amount"     => $this->input->post('exclusion_other_charge_tax_amount') ? (float) $this->input->post('exclusion_other_charge_tax_amount') : 0 ,
            "total_exclusion_other_charge"          => $this->input->post('total_other_exclusive_charge') ? (float) $this->input->post('total_other_exclusive_charge') : 0 ,
            "total_other_amount"                    => $this->input->post('total_other_amount') ? (float) $this->input->post('total_other_amount') : 0 ,
            "total_other_taxable_amount"            =>$this->input->post('total_other_taxable_amount') ? (float) $this->input->post('total_other_taxable_amount') : 0 ,
            "note1"                                 => $this->input->post('note1') ,
            "note2"                                 => $this->input->post('note2')
        );
        $sales_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $sales_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $sales_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $sales_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $sales_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $sales_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        $round_off_value = $sales_data['sales_grand_total'];
        /*Cutomize for Leather Craft*/
        if(@$this->input->post('cmb_department')){
            $sales_data['department_id'] = $this->input->post('cmb_department');
        }

        if(@$this->input->post('cmb_subdepartment')){
            $sales_data['sub_department_id'] = $this->input->post('cmb_subdepartment');
        }

        if(@$this->input->post('cash_discount')){
            $sales_data['sales_cash_discount'] = $this->input->post('cash_discount');
        }

        if(@$this->input->post('brand_invoice_number')){
            $sales_data['sales_brand_invoice_number'] = $this->input->post('brand_invoice_number');
        }

        if(@$this->input->post('brand_id')){
            $sales_data['brand_id'] = $this->input->post('brand_id');
        }

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" && $this->input->post('round_off_key') == "yes"){
            if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 ){
                $round_off_value = $this->input->post('round_off_value');
            }
        }
        $sales_data['round_off_amount'] = bcsub($sales_data['sales_grand_total'] , $round_off_value,$section_modules['access_common_settings'][0]->amount_precision);
        $sales_data['sales_grand_total'] = $round_off_value;
        $sales_data['customer_payable_amount'] = $sales_data['sales_grand_total'];
        if (isset($sales_data['sales_tds_amount']) && $sales_data['sales_tds_amount'] > 0){
            $sales_data['customer_payable_amount'] = bcsub($sales_data['sales_grand_total'], $sales_data['sales_tds_amount']);
        }
        //$sales_tax_amount = $sales_data['sales_tax_amount'];
        $sales_tax_amount = $sales_data['sales_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));
        
        if ($section_modules['access_settings'][0]->tax_type == "gst")
        {
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            if ($sales_data['sales_billing_state_id'] != 0){
                if ($data['branch'][0]->branch_state_id == $sales_data['sales_billing_state_id']){
                    $sales_data['sales_igst_amount'] = 0;
                    $sales_data['sales_cgst_amount'] = ($sales_tax_amount * $cgst_amount_percentage) / 100;
                    $sales_data['sales_sgst_amount'] = ($sales_tax_amount * $sgst_amount_percentage) / 100;
                    $sales_data['sales_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $sales_data['sales_igst_amount'] = $sales_tax_amount;
                    $sales_data['sales_cgst_amount'] = 0;
                    $sales_data['sales_sgst_amount'] = 0;
                    $sales_data['sales_tax_cess_amount'] = $total_cess_amnt;
                }
            } else {
                if ($sales_data['sales_type_of_supply'] == "export_with_payment"){
                    $sales_data['sales_igst_amount'] = $sales_tax_amount;
                    $sales_data['sales_cgst_amount'] = 0;
                    $sales_data['sales_sgst_amount'] = 0;
                    $sales_data['sales_tax_cess_amount'] = $total_cess_amnt;
                }
            }
        }
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
            $sales_data['converted_grand_total'] = $sales_data['sales_grand_total'];
        }else{
            $sales_data['converted_grand_total'] = 0;
        }
        $data_main   = array_map('trim' , $sales_data);
        $sales_table = $this->config->item('sales_table');   
        
        $sales_id = $this->general_model->insertData($sales_table , $data_main);
        if ($sales_id) {
            $successMsg = 'Sales Added Successfully';
            $this->session->set_flashdata('sales_success',$successMsg);
            $log_data              = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $sales_id ,
                'table_name'        => $sales_table ,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'Sales Inserted' );
            $data_main['sales_id'] = $sales_id;
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $sales_item_data       = $this->input->post('table_data');
            $js_data               = json_decode($sales_item_data);
            $js_data               = array_reverse($js_data);
            $item_table            = $this->config->item('sales_item_table');
            
            if (!empty($js_data))
            {
                $js_data1 = array();
                foreach ($js_data as $key => $value)
                {
                    if ($value != null && $value != '') {
                        $item_id   = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "item_id"                    => $value->item_id ,
                            "item_type"                  => $value->item_type ,
                            "sales_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "sales_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "sales_item_free_quantity"   => (@$value->free_item_quantity ? (float) $value->free_item_quantity : 0),
                            "sales_item_mrp_price"      => (@$value->item_mrp_price ? (float) $value->item_mrp_price : 0),
                            "sales_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "sales_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "sales_item_cash_discount_amount" => (@$value->item_cash_discount ? (float) $value->item_cash_discount : 0) ,
                            "sales_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0) ,
                            "sales_item_discount_id"     => (@$value->item_discount_id ? (float) $value->item_discount_id : 0 ),
                            "sales_item_tds_id"          => $value->item_tds_id ? (float) $value->item_tds_id : 0 ,
                            "sales_item_tds_percentage"  => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ,
                            "sales_item_tds_amount"      => $value->item_tds_amount ? (float) $value->item_tds_amount : 0 ,
                            "sales_item_grand_total"     => $value->item_grand_total ? (float) $value->item_grand_total : 0 ,
                            "sales_item_tax_id"          => $value->item_tax_id ? (float) $value->item_tax_id : 0 ,
                            "sales_item_tax_cess_id"          => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0 ,
                            "sales_item_igst_percentage" => 0 ,
                            "sales_item_igst_amount"     => 0 ,
                            "sales_item_cgst_percentage" => 0 ,
                            "sales_item_cgst_amount"     => 0 ,
                            "sales_item_sgst_percentage" => 0 ,
                            "sales_item_sgst_amount"     => 0 ,
                            "sales_item_tax_percentage"  => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ,
                            "sales_item_tax_cess_percentage"  => 0 ,
                            "sales_item_tax_amount"      => $value->item_tax_amount ? (float) $value->item_tax_amount : 0 ,
                            'sales_item_tax_cess_amount' => 0 ,
                            "sales_item_description"     => $value->item_description ? $value->item_description : "" ,
                            "sales_item_uom_id"  => (@$value->item_uom ? $value->item_uom : ""),
                            "debit_note_quantity" => 0 ,
                            "sales_id"   => $sales_id );
                        $sales_item_tax_amount     = $item_data['sales_item_tax_amount'];
                        $sales_item_tax_percentage = $item_data['sales_item_tax_percentage'];
                        if ($section_modules['access_settings'][0]->tax_type == "gst")
                        {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0 ;
                            
                            if ($sales_data['sales_billing_state_id'] != 0){
                                if ($data['branch'][0]->branch_state_id == $sales_data['sales_billing_state_id'])
                                {
                                    $item_data['sales_item_igst_amount'] = 0;
                                    $item_data['sales_item_cgst_amount'] = ($sales_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['sales_item_sgst_amount'] = ($sales_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['sales_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['sales_item_igst_percentage'] = 0;
                                    $item_data['sales_item_cgst_percentage'] = ($sales_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['sales_item_sgst_percentage'] = ($sales_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['sales_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                                else
                                {
                                    $item_data['sales_item_igst_amount'] = $sales_item_tax_amount;
                                    $item_data['sales_item_cgst_amount'] = 0;
                                    $item_data['sales_item_sgst_amount'] = 0;
                                    $item_data['sales_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['sales_item_igst_percentage'] = $sales_item_tax_percentage;
                                    $item_data['sales_item_cgst_percentage'] = 0;
                                    $item_data['sales_item_sgst_percentage'] = 0;
                                    $item_data['sales_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                            else
                            {
                                if ($sales_data['sales_type_of_supply'] == "export_with_payment")
                                {
                                    $item_data['sales_item_igst_amount'] = $sales_item_tax_amount;
                                    $item_data['sales_item_cgst_amount'] = 0;
                                    $item_data['sales_item_sgst_amount'] = 0;
                                    $item_data['sales_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['sales_item_igst_percentage'] = $sales_item_tax_percentage;
                                    $item_data['sales_item_cgst_percentage'] = 0;
                                    $item_data['sales_item_sgst_percentage'] = 0;
                                    $item_data['sales_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                        }

                        /* Customization leather craft fields */
                        if(@$value->item_basic_total){
                            $item_data['sales_item_basic_total'] = $value->item_basic_total;
                        }
                        if(@$value->item_selling_price){
                            $item_data['sales_item_selling_price'] = $value->item_selling_price;
                        }
                        if(@$value->item_mrkd_discount_amount){
                            $item_data['sales_item_mrkd_discount_amount'] = $value->item_mrkd_discount_amount;
                        }
                        if(@$value->item_mrkd_discount_id){
                            $item_data['sales_item_mrkd_discount_id'] = $value->item_mrkd_discount_id;
                        }
                        if(@$value->item_mrkd_discount_percentage){
                            $item_data['sales_item_mrkd_discount_percentage'] = $value->item_mrkd_discount_percentage;
                        }
                        if(@$value->item_mrgn_discount_amount){
                            $item_data['sales_item_mrgn_discount_amount'] = $value->item_mrgn_discount_amount;
                        }
                        if(@$value->item_mrgn_discount_id){
                            $item_data['sales_item_mrgn_discount_id'] = $value->item_mrgn_discount_id;
                        }
                        if(@$value->item_mrgn_discount_percentage){
                            $item_data['sales_item_mrgn_discount_percentage'] = $value->item_mrgn_discount_percentage;
                        }

                        if(@$value->item_scheme_discount_amount){
                            $item_data['sales_item_scheme_discount_amount'] = $value->item_scheme_discount_amount;
                        }
                        if(@$value->item_scheme_discount_id){
                            $item_data['sales_item_scheme_discount_id'] = $value->item_scheme_discount_id;
                        }
                        if(@$value->item_scheme_discount_percentage){
                            $item_data['sales_item_scheme_discount_percentage'] = $value->item_scheme_discount_percentage;
                        }

                        if(@$value->item_out_tax_percentage){
                            $item_data['sales_item_out_tax_percentage'] = $value->item_out_tax_percentage;
                        }
                        if(@$value->item_out_tax_amount){
                            $item_data['sales_item_out_tax_amount'] = $value->item_out_tax_amount;
                        }
                        if(@$value->item_out_tax_id){
                            $item_data['sales_item_out_tax_id'] = $value->item_out_tax_id;
                        }
                       
                        /* End leather Craft */
                        $data_item  = array_map('trim' , $item_data);
                        $js_data1[] = $data_item;
                        /*if (){*/
                            if ($data_item['item_type'] == "product"){
                                $product_data     = $this->common->product_field($data_item['item_id']);
                                $product_result   = $this->general_model->getJoinRecords($product_data['string'] , $product_data['table'] , $product_data['where'],$product_data['join']);
                                //selling price for product
                                if($product_result[0]->product_selling_price == 0){
                                    $product_selling_price = $value->item_sub_total ? (float) $value->item_sub_total : 0;
                                }else{
                                    $product_selling_price = $product_result[0]->product_selling_price;
                                }
                                if(@$value->free_item_quantity){
                                    if($value->free_item_quantity > 0) $value->item_quantity = $value->item_quantity + $value->free_item_quantity;
                                }
                                if($product_result[0]->equal_uom_id){
                                    if($product_result[0]->equal_uom_id == $value->item_uom){
                                        $value->item_quantity = $value->item_quantity/$product_result[0]->equal_unit_number;
                                    }
                                }
                                $product_quantity = ($product_result[0]->product_quantity - $value->item_quantity);
                                $stockData        = array('product_quantity' => $product_quantity,'product_selling_price' => $product_selling_price);  
                               
                                $where            = array('product_id' => $value->item_id );

                                $product_table    = $this->config->item('product_table');
                                $this->general_model->updateData($product_table , $stockData , $where);

                                /*$this->producthook->UpdateProductStock(array('product_id' => $value->item_id,'product_quantity' => $product_quantity));*/
                                // quantity history
                                $history = array(
                                    "item_id"          => $value->item_id ,
                                    "item_type"        => 'product' ,
                                    "reference_id"     => $sales_id ,
                                    "reference_number" => $invoice_number ,
                                    "reference_type"   => 'sales' ,
                                    "quantity"         => $value->item_quantity ,
                                    "stock_type"       => 'indirect' ,
                                    "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') ,
                                    "added_date"       => date('Y-m-d') ,
                                    "entry_date"       => date('Y-m-d') ,
                                    "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
                                $this->general_model->insertData("quantity_history" , $history);
                            }
                        /*}*/
                        $this->general_model->insertData($item_table , $data_item);
                    }
                }
                /*$this->db->insert_batch($item_table, $js_data1); */
                if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){
                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
                        $action = "add";
                        $this->sales_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                    }
                }
            }

            if ($this->input->post('section_area') == 'convert_quotation'){
                $quotation_id    = $this->input->post('quotation_id');
                $quotation_table = 'quotation';
                $quotation_data  = array(
                    'sales_id' => $sales_id );
                $quotation_where = array(
                    'quotation_id'  => $quotation_id ,
                    'delete_status' => 0 );
                $this->general_model->updateData($quotation_table , $quotation_data , $quotation_where);
                $successMsg = 'Quotation Converted Successfully';
                $this->session->set_flashdata('sales_success',$successMsg);
            }

            if ($this->input->post('section_area') == 'convert_performa'){
                $performa_id    = $this->input->post('performa_id');
                $performa_table = 'performa';
                $performa_data  = array(
                    'sales_id' => $sales_id );
                $performa_where = array(
                    'performa_id'  => $performa_id ,
                    'delete_status' => 0 );
                $this->general_model->updateData($performa_table , $performa_data , $performa_where);
            }
        } 
        $action = $this->input->post('submit');
        if ($action == 'pay_now') {
            $sales_id = $this->encryption_url->encode($sales_id);
            redirect('receipt_voucher/add_sales_receipt/' . $sales_id , 'refresh');
        }else{
            redirect('sales' , 'refresh');
        }
    }
    public function sales_vouchers($section_modules , $data_main , $js_data , $branch){
        $invoice_from = $data_main['from_account'];
        $invoice_to   = $data_main['to_account'];
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
        if ((($data_main['sales_tax_amount'] > 0 && ($data_main['sales_igst_amount'] > 0 || $data_main['sales_cgst_amount'] > 0 || $data_main['sales_sgst_amount'] > 0 || $data_main['sales_tax_cess_amount'] > 0)) || $data_main['sales_tax_cess_amount'] > 0 ) && $data_main['sales_gst_payable'] != "yes"){
            $present = "gst";
            if ($data_main['sales_billing_state_id'] != 0 && $data_main['sales_type_of_supply'] == 'regular'){
                if ($branch[0]->branch_state_id == $data_main['sales_billing_state_id']){
                    $present = "cgst";
                } else {
                    $present = "igst";
                }
            }else{
                if ($data_main['sales_type_of_supply'] == "export_with_payment")
                {
                    $present = "out_of_country";
                }
            }
          
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
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['sales_billing_state_id']);
                            if($is_utgst == '1') $gst_lbl = 'UTGST';

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
                                if($is_utgst == '1') {
                                    $sgst_ledger = $utgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['sales_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $utgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                }else{
                                    $sgst_ledger = $sgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['sales_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                }
                                
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
                    }elseif($present == "igst"){
                        if ($value['sales_item_igst_percentage'] > 0) {
                            /*$default_igst_id = $sales_ledger['IGST@X'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);*/
                           
                            $igst_ary = array(
                                            'ledger_name' => 'Output IGST@'.$value['sales_item_igst_percentage'].'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['sales_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_x)){
                                $igst_ledger = $igst_x->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['sales_item_igst_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                $igst_ary['main_grp'] = $igst_x->main_group;
                                $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                            
                            if (in_array($igst_tax_ledger , $igst_slab))
                            {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['sales_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }
                            else
                            {
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['sales_item_igst_amount'];
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
                    }elseif ($present == "out_of_country"){
                        if ($value['sales_item_igst_percentage'] > 0) {

                            $default_igst_id = $sales_ledger['IGST_PAY'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'IGST @ payable',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['sales_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['sales_item_igst_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            
                            if (in_array($igst_tax_ledger , $igst_slab)){
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['sales_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['sales_item_igst_amount'];
                            }

                            $default_igst_id = $sales_ledger['IGST_REV'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'IGST Refund receviable',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['sales_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['sales_item_igst_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                            if(!in_array($igst_tax_ledger, $igst_slab_minus)) $igst_slab_minus[] = $igst_tax_ledger;
                            if (in_array($igst_tax_ledger , $igst_slab)) {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['sales_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['sales_item_igst_amount'];
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

                            $default_cess_id = $sales_ledger['CESS_REV'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                           
                            $cess_ary = array(
                                            'ledger_name' => 'Compensation Cess @ Refund receviable',
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

                            if(!in_array($cess_tax_ledger, $cess_slab_minus)) $cess_slab_minus[] = $cess_tax_ledger;
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
        }/*else if (($data_main['sales_tax_amount'] > 0  && ($data_main['sales_igst_amount'] == 0 && $data_main['sales_cgst_amount'] == 0 && $data_main['sales_sgst_amount'] == 0)) && $data_main['sales_gst_payable'] != "yes"){
            $present = "single_tax";
            $tax_slab       = array();
            $tax_slab_minus = array();
            $tax_slab_items = array();
            foreach ($js_data as $key => $value){
                if ($value['sales_item_tax_percentage'] > 0){
                    
                    $tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'TAX@'.$value['sales_item_tax_percentage'].'%',
                                                                        'subgrp_1' => 'TAX',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));
                    if (in_array($tax_ledger , $tax_slab)){
                        $tax_slab_items[$tax_ledger] = bcadd($tax_slab_items[$tax_ledger] , $value['sales_item_tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                    } else{
                        $tax_slab[]                  = $tax_ledger;
                        $tax_slab_items[$tax_ledger] = $value['sales_item_tax_amount'];
                    }
                }
            }
           
            if (in_array($charges_sub_module_id , $section_modules['access_sub_modules'])){
                $freight_charge_id = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'Freight_charge@'.$data_main['freight_charge_tax_percentage'].'%',
                                                                        'subgrp_1' => 'TAX',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => $data_main['freight_charge_tax_amount']
                                                                    ));
                $tax_charges_array[0]['tax_percentage'] = $data_main['freight_charge_tax_percentage'];
                $tax_charges_array[0]['tax_id']         = $data_main['freight_charge_tax_id'];
                $tax_charges_array[0]['tax_amount']     = $data_main['freight_charge_tax_amount'];
                $tax_charges_array[0]['ledger_id']      = $freight_charge_id;
                
                $insurance_charge_id = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'Insurance_charge@'.$data_main['insurance_charge_tax_percentage'].'%',
                                                                        'subgrp_1' => 'TAX',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => $data_main['insurance_charge_tax_amount']
                                                                    ));
                $tax_charges_array[1]['tax_percentage'] = $data_main['insurance_charge_tax_percentage'];
                $tax_charges_array[1]['tax_id']         = $data_main['insurance_charge_tax_id'];
                $tax_charges_array[1]['tax_amount']     = $data_main['insurance_charge_tax_amount'];
                $tax_charges_array[1]['ledger_id']      = $insurance_charge_id;
                $packing_charge_id = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'Packing_charge@'.$data_main['packing_charge_tax_percentage'].'%',
                                                                        'subgrp_1' => 'TAX',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => $data_main['packing_charge_tax_amount']
                                                                    ));
                $tax_charges_array[2]['tax_percentage'] = $data_main['packing_charge_tax_percentage'];
                $tax_charges_array[2]['tax_id']         = $data_main['packing_charge_tax_id'];
                $tax_charges_array[2]['tax_amount']     = $data_main['packing_charge_tax_amount'];
                $tax_charges_array[2]['ledger_id']      = $packing_charge_id;
                $incidental_charge_id = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'Incidental_charge@'.$data_main['incidental_charge_tax_percentage'].'%',
                                                                        'subgrp_1' => 'TAX',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => $data_main['incidental_charge_tax_amount']
                                                                    ));
                $tax_charges_array[3]['tax_percentage'] = $data_main['incidental_charge_tax_percentage'];
                $tax_charges_array[3]['tax_id']         = $data_main['incidental_charge_tax_id'];
                $tax_charges_array[3]['tax_amount']     = $data_main['incidental_charge_tax_amount'];
                $tax_charges_array[3]['ledger_id']     = $incidental_charge_id;
                
                $inclusion_other_charge_id = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'Inclusion_other_charge@'.$data_main['inclusion_other_charge_tax_percentage'].'%',
                                                                        'subgrp_1' => 'TAX',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' =>  $data_main['inclusion_other_charge_tax_amount']
                                                                    ));
                $tax_charges_array[4]['tax_percentage'] = $data_main['inclusion_other_charge_tax_percentage'];
                $tax_charges_array[4]['tax_id']         = $data_main['inclusion_other_charge_tax_id'];
                $tax_charges_array[4]['tax_amount']     = $data_main['inclusion_other_charge_tax_amount'];
                $tax_charges_array[4]['ledger_id']      = $inclusion_other_charge_id;
                
                $exclusion_other_id = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => 'Incidental_charge@'.$data_main['incidental_charge_tax_percentage'].'%',
                                                                        'subgrp_1' => 'TAX',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => $data_main['incidental_charge_tax_amount']
                                                                    ));                                                   
                $tax_charges_array[5]['tax_percentage'] = $data_main['exclusion_other_charge_tax_percentage'];
                $tax_charges_array[5]['tax_id']         = $data_main['exclusion_other_charge_tax_id'];
                $tax_charges_array[5]['tax_amount']     = $data_main['exclusion_other_charge_tax_amount'];
                $tax_charges_array[5]['ledger_id']      = $exclusion_other_id;
                foreach ($tax_charges_array as $key => $value){
                    if ($tax_charges_array[$key]['tax_percentage'] > 0){
                        $tax_ledger = $value['ledger_id'];
                        
                        if (in_array($tax_ledger , $tax_slab)){
                            if($key == 5){
                                $tax_slab_items[$tax_ledger] = bcsub($tax_slab_items[$tax_ledger] , $tax_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $tax_slab_items[$tax_ledger] = bcadd($tax_slab_items[$tax_ledger] , $tax_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }
                        } else {
                            $tax_slab[] = $tax_ledger;
                            $tax_slab_items[$tax_ledger] = $tax_charges_array[$key]['tax_amount'];
                            if($key == 5){
                                if (!in_array($tax_ledger , $tax_slab_minus)){
                                    $tax_slab_minus[] = $tax_ledger;
                                }
                            }
                        }
                    }
                }
            }
        }*/

        /* Charges modules tax */
        if($data_main['sales_type_of_supply'] != 'export_without_payment' && $data_main['sales_gst_payable'] != "yes"){
            if (in_array($charges_sub_module_id , $section_modules['access_sub_modules'])){
                $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                $cgst_amount_percentage = $tax_split_percentage;
                $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                $extra_cahrges_ary = array('freight_charge','insurance_charge','packing_charge','incidental_charge','inclusion_other_charge','exclusion_other_charge');
                $i = 0;
                foreach ($extra_cahrges_ary as $key => $value) {
                    
                    $igst_charges_array[$i]['tax_percentage'] = $data_main[$value.'_tax_percentage'];
                    $cgst_charges_array[$i]['tax_percentage'] = ($data_main[$value.'_tax_percentage'] * $cgst_amount_percentage) / 100;
                    $sgst_charges_array[$i]['tax_percentage'] = ($data_main[$value.'_tax_percentage'] * $sgst_amount_percentage) / 100;
                    $igst_charges_array[$i]['tax_id'] = $data_main[$value.'_tax_id'];
                    $cgst_charges_array[$i]['tax_id'] = $data_main[$value.'_tax_id'];
                    $sgst_charges_array[$i]['tax_id'] = $data_main[$value.'_tax_id'];
                    $igst_charges_array[$i]['tax_amount'] = $data_main[$value.'_tax_amount'];
                    $cgst_charges_array[$i]['tax_amount'] = ($data_main[$value.'_tax_amount'] * $cgst_amount_percentage) / 100;
                    $sgst_charges_array[$i]['tax_amount'] = ($data_main[$value.'_tax_amount'] * $sgst_amount_percentage) / 100;
                    $i++;
                }
                $present = "gst";
                if ($data_main['sales_billing_state_id'] != 0 && $data_main['sales_type_of_supply'] == 'regular'){
                    if ($branch[0]->branch_state_id == $data_main['sales_billing_state_id']){
                        $present = "cgst";
                    } else {
                        $present = "igst";
                    }
                }else{
                    if ($data_main['sales_type_of_supply'] == "export_with_payment")
                    {
                        $present = "out_of_country";
                    }
                }
                foreach ($igst_charges_array as $key => $value){

                    if ($present == "cgst"){
                        if ($cgst_charges_array[$key]['tax_percentage'] > 0 || $sgst_charges_array[$key]['tax_percentage'] > 0)
                        {
                            if ($key != 5){
                                /*$default_cgst_id = $sales_ledger['CGST@X'];
                                $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);*/
                               
                                $cgst_ary = array(
                                                'ledger_name' => 'Output CGST@'.$cgst_charges_array[$key]['tax_percentage'].'%',
                                                'second_grp' => 'CGST',
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Liabilities',
                                                'default_ledger_id' => 0,
                                                'default_value' => $cgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($cgst_x)){
                                    $cgst_ledger = $cgst_x->ledger_name;
                                    $cgst_ledger = str_ireplace('{{X}}',$cgst_charges_array[$key]['tax_percentage'] , $cgst_ledger);
                                    $cgst_ary['ledger_name'] = $cgst_ledger;
                                    $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                    $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                    $cgst_ary['main_grp'] = $cgst_x->main_group;
                                    $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                                }
                                $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);

                               /* $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => 'CGST@'.$cgst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => 'CGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['sales_billing_state_id']);
                                if($is_utgst == '1') $gst_lbl = 'UTGST';

                                /*$default_sgst_id = $sales_ledger[$gst_lbl.'@X'];
                                $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);*/
                               
                                $sgst_ary = array(
                                                'ledger_name' => 'Output '.$gst_lbl.'@'.$sgst_charges_array[$key]['tax_percentage'].'%',
                                                'second_grp' => $gst_lbl,
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Liabilities',
                                                'default_ledger_id' => 0,
                                                'default_value' => $sgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($sgst_x)){
                                    if($is_utgst == '1'){
                                        $sgst_ledger = $utgst_x->ledger_name;
                                        $sgst_ledger = str_ireplace('{{X}}',$sgst_charges_array[$key]['tax_percentage'] , $sgst_ledger);
                                        $sgst_ary['ledger_name'] = $sgst_ledger;
                                        $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                        $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                        $sgst_ary['main_grp'] = $utgst_x->main_group;
                                        $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                    }else{

                                        $sgst_ledger = $sgst_x->ledger_name;
                                        $sgst_ledger = str_ireplace('{{X}}',$sgst_charges_array[$key]['tax_percentage'] , $sgst_ledger);
                                        $sgst_ary['ledger_name'] = $sgst_ledger;
                                        $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                        $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                        $sgst_ary['main_grp'] = $sgst_x->main_group;
                                        $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                    }
                                }
                                $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                                /*$sgst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => $gst_lbl.'@'.$sgst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => $gst_lbl,
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                            }else {
                                /*$default_cgst_id = $sales_ledger['CGST@X'];
                                $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);*/
                                
                                $cgst_ary = array(
                                                'ledger_name' => 'Output CGST@'.$cgst_charges_array[$key]['tax_percentage'].'%',
                                                'second_grp' => 'CGST',
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Liabilities',
                                                'default_ledger_id' => 0,
                                                'default_value' => $cgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($cgst_x)){
                                    $cgst_ledger = $cgst_x->ledger_name;
                                    $cgst_ledger = str_ireplace('{{X}}',$cgst_charges_array[$key]['tax_percentage'] , $cgst_ledger);
                                    $cgst_ary['ledger_name'] = $cgst_ledger;
                                    $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                    $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                    $cgst_ary['main_grp'] = $cgst_x->main_group;
                                    $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                                }
                                $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                                /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => 'CGST@'.$cgst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => 'CGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['sales_billing_state_id']);
                                if($is_utgst == '1') $gst_lbl = 'UTGST';

                                /*$default_sgst_id = $sales_ledger[$gst_lbl.'@X'];
                                $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);*/
                                
                                $sgst_ary = array(
                                                'ledger_name' => 'Output '.$gst_lbl.'@'.$sgst_charges_array[$key]['tax_percentage'].'%',
                                                'second_grp' => $gst_lbl,
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Liabilities',
                                                'default_ledger_id' => 0,
                                                'default_value' => $sgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($sgst_ledger_name)){
                                    if($is_utgst == '1'){
                                        $sgst_ledger = $utgst_x->ledger_name;
                                        $sgst_ledger = str_ireplace('{{X}}',$sgst_charges_array[$key]['tax_percentage'] , $sgst_ledger);
                                        $sgst_ary['ledger_name'] = $sgst_ledger;
                                        $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                        $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                        $sgst_ary['main_grp'] = $utgst_x->main_group;
                                        $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                    }else{
                                        $sgst_ledger = $sgst_x->ledger_name;
                                        $sgst_ledger = str_ireplace('{{X}}',$sgst_charges_array[$key]['tax_percentage'] , $sgst_ledger);
                                        $sgst_ary['ledger_name'] = $sgst_ledger;
                                        $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                        $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                        $sgst_ary['main_grp'] = $sgst_x->main_group;
                                        $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                    }
                                }
                                $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                                /*$sgst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => $gst_lbl.'@'.$sgst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => $gst_lbl,
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                            }
                            if (in_array($cgst_tax_ledger , $cgst_slab)){
                                if ($key != 5){
                                    $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $cgst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }else{
                                    $cgst_slab_items[$cgst_tax_ledger] = bcsub($cgst_slab_items[$cgst_tax_ledger] , $cgst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }
                            }else{
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $cgst_charges_array[$key]['tax_amount'];
                                if ($key == 5 && !in_array($cgst_tax_ledger , $cgst_slab_minus)){
                                    $cgst_slab_minus[] = $cgst_tax_ledger;
                                }
                            }
                            if (in_array($sgst_tax_ledger , $sgst_slab)) {
                                if ($key != 5){
                                    $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $sgst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }else{
                                    $sgst_slab_items[$sgst_tax_ledger] = bcsub($sgst_slab_items[$sgst_tax_ledger] , $sgst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }
                            }else{
                                $sgst_slab[] = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $sgst_charges_array[$key]['tax_amount'];
                                if ($key == 5 && !in_array($sgst_tax_ledger , $sgst_slab_minus)){
                                    $sgst_slab_minus[] = $sgst_tax_ledger;
                                }
                            }
                        }
                    } elseif($present == "igst"){
                        if ($igst_charges_array[$key]['tax_percentage'] > 0) {
                            /*if ($key != 5){*/
                                $igst_ary = array(
                                                'ledger_name' => 'Output IGST@'.$igst_charges_array[$key]['tax_percentage'].'%',
                                                'second_grp' => 'IGST',
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Liabilities',
                                                'default_ledger_id' => 0,
                                                'default_value' => $igst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($igst_x)){
                                    $igst_ledger = $igst_x->ledger_name;
                                    $igst_ledger = str_ireplace('{{X}}',$igst_charges_array[$key]['tax_percentage'] , $igst_ledger);
                                    $igst_ary['ledger_name'] = $igst_ledger;
                                    $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                    $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                    $igst_ary['main_grp'] = $igst_x->main_group;
                                    $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                                }
                                $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                                /*$igst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => 'IGST@'.$igst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => 'IGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                            /*} else {
                                $igst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => 'IGST@'.$igst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => 'IGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));
                                
                            }*/
                            if (in_array($igst_tax_ledger , $igst_slab)){
                                if ($key != 5){
                                    $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $igst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }else{
                                    $igst_slab_items[$igst_tax_ledger] = bcsub($igst_slab_items[$igst_tax_ledger] , $igst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }
                            } else {
                                $igst_slab[] = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $igst_charges_array[$key]['tax_amount'];
                                
                                if ($key == 5 && !in_array($igst_tax_ledger , $igst_slab_minus)){
                                    $igst_slab_minus[] = $igst_tax_ledger;
                                }
                            }
                        }
                    } elseif ($present == "out_of_country"){
                        if ($igst_charges_array[$key]['tax_percentage'] > 0) {
                            $default_igst_id = $sales_ledger['IGST_PAY'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                                
                            $igst_ary = array(
                                            'ledger_name' => 'IGST @ payable',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $igst_charges_array[$key]['tax_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$igst_charges_array[$key]['tax_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                            /*$igst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                'ledger_name' => 'IGST @ payable',
                                                                'subgrp_1' => 'IGST',
                                                                'subgrp_2' => 'Duties and taxes',
                                                                'main_grp' => 'Current Liabilities',
                                                                'amount' => 0
                                                            ));*/
                           
                            if (in_array($igst_tax_ledger , $igst_slab)){
                                if ($key != 5){
                                    $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $igst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }else{
                                    $igst_slab_items[$igst_tax_ledger] = bcsub($igst_slab_items[$igst_tax_ledger] , $igst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }
                            } else {
                                $igst_slab[] = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $igst_charges_array[$key]['tax_amount'];
                                
                                if ($key == 5 && !in_array($igst_tax_ledger , $igst_slab_minus)){
                                    $igst_slab_minus[] = $igst_tax_ledger;
                                }
                            }

                            $default_igst_id = $sales_ledger['IGST_REV'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                                
                            $igst_ary = array(
                                            'ledger_name' => 'IGST Refund receviable',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => $igst_charges_array[$key]['tax_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$igst_charges_array[$key]['tax_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                            /*$igst_tax_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                    'ledger_name' => 'IGST Refund receviable',
                                                                    'subgrp_1' => 'IGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                            if (in_array($igst_tax_ledger , $igst_slab)){
                                if ($key != 5){
                                    $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $igst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }else{
                                    $igst_slab_items[$igst_tax_ledger] = bcsub($igst_slab_items[$igst_tax_ledger] , $igst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }
                            } else {
                                $igst_slab[] = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $igst_charges_array[$key]['tax_amount'];
                                
                                if ($key != 5 && !in_array($igst_tax_ledger , $igst_slab_minus)){
                                    $igst_slab_minus[] = $igst_tax_ledger;
                                }
                            }
                        }
                    }
                }
            }
        }
        /* Tax rate slab ends */
        /* TDS SLAB */
        if ($data_main['sales_tds_amount'] > 0 || $data_main['sales_tcs_amount'] > 0){
            $tds_slab                      = array();
            $tds_slab_minus                = array();
            $tds_slab_items                = array();
            $data_main['total_tds_amount'] = 0;
            $data_main['total_tcs_amount'] = 0;
            foreach ($js_data as $key => $value){
                if ($value['sales_item_tds_percentage'] > 0){
                    
                    $string       = 'tds.section_name,td.tax_name';
                    $table        = 'tax td';
                    $where        = array('td.delete_status' => 0 ,'td.tax_id' => $value['sales_item_tds_id'] );
                    $join         = array('tax_section tds' => 'td.section_id = tds.section_id');
                    $tds_data     = $this->general_model->getJoinRecords($string , $table , $where , $join);
                    
                    if(!empty($tds_data)){
                        $section_name = $tds_data[0]->section_name;
                        $module_type  = strtoupper($tds_data[0]->tax_name);
                    }else{
                        $module_type = 'TCS';
                        $section_name = '193';
                    }
                    if (strtoupper($module_type) == "TCS"){
                        $payment_type = "Payable";
                        $tds_subgroup = "TCS Payable u/s ";

                        $default_tds_id = $sales_ledger['TCS_PAY'];
                        $tds_ledger_name = $this->ledger_model->getDefaultLedgerId($default_tds_id);
                            
                        $tds_ary = array(
                                        'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['sales_item_tds_percentage'].'%',
                                        'second_grp' => '',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['sales_item_tds_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($tds_ledger_name)){
                            $tds_ledger = $tds_ledger_name->ledger_name;
                            $tds_ledger = str_ireplace('{{SECTION}}',$section_name , $tds_ledger);
                            $tds_ledger = str_ireplace('{{X}}',$value['sales_item_tds_percentage'] , $tds_ledger);
                            $tds_ary['ledger_name'] = $tds_ledger;
                            $tds_ary['primary_grp'] = $tds_ledger_name->sub_group_1;
                            $tds_ary['second_grp'] = $tds_ledger_name->sub_group_2;
                            $tds_ary['main_grp'] = $tds_ledger_name->main_group;
                            $tds_ary['default_ledger_id'] = $tds_ledger_name->ledger_id;
                        }
                        $tds_ledger = $this->ledger_model->getGroupLedgerId($tds_ary);

                        /*$tds_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['sales_item_tds_percentage'].'%',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'subgrp_1' => '',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' =>  0
                                                                    ));*/
                    } else {
                        $payment_type = "Receivable";
                        $tds_subgroup = "TDS Receivable u/s";
                        $default_tds_id = $sales_ledger['TDS_REV'];
                        $tds_ledger_name = $this->ledger_model->getDefaultLedgerId($default_tds_id);
                            
                        $tds_ary = array(
                                        'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['sales_item_tds_percentage'].'%',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['sales_item_tds_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($tds_ledger_name)){
                            $tds_ledger = $tds_ledger_name->ledger_name;
                            $tds_ledger = str_ireplace('{{SECTION}}',$section_name, $tds_ledger);
                            $tds_ledger = str_ireplace('{{X}}',$value['sales_item_tds_percentage'] , $tds_ledger);
                            $tds_ary['ledger_name'] = $tds_ledger;
                            $tds_ary['primary_grp'] = $tds_ledger_name->sub_group_1;
                            $tds_ary['second_grp'] = $tds_ledger_name->sub_group_2;
                            $tds_ary['main_grp'] = $tds_ledger_name->main_group;
                            $tds_ary['default_ledger_id'] = $tds_ledger_name->ledger_id;
                        }
                        $tds_ledger = $this->ledger_model->getGroupLedgerId($tds_ary);

                        /*$tds_ledger = $this->ledger_model->getGroupLedgerId(array(
                                                                        'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['sales_item_tds_percentage'].'%',
                                                                        'subgrp_2' => '',
                                                                        'subgrp_1' => '',
                                                                        'main_grp' => 'Current Assets',
                                                                        'amount' =>  0
                                                                    ));*/
                    }
                    $tds_title    = $module_type . " " . $payment_type . " under u/s " . $section_name;
                    
                    if (in_array($tds_ledger , $tds_slab)){
                        $tds_slab_items[$tds_ledger] = bcadd($tds_slab_items[$tds_ledger] , $value['sales_item_tds_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                    }else {
                        $tds_slab[]                  = $tds_ledger;
                        $tds_slab_items[$tds_ledger] = $value['sales_item_tds_amount'];
                    }
                    if ($module_type == "TCS"){
                        if (!in_array($tds_ledger , $tds_slab_minus)){
                            $tds_slab_minus[] = $tds_ledger;
                        }
                    }
                }
            }
        }
        /* tds ends */
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
        $string             = 'ledger_id,customer_name';
        $table              = 'customer';
        $where              = array('customer_id' => $data_main['sales_party_id']);
        $customer_data      = $this->general_model->getRecords($string , $table , $where , $order = "");
        $customer_name = $customer_data[0]->customer_name;
        $customer_ledger_id = $customer_data[0]->ledger_id;

        if(!$customer_ledger_id){
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
        }
        /*$customer_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => $customer_name,
                                            'subgrp_2' => 'Sundry Debtors',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Current Assets',
                                            'amount' =>  0
                                        ));*/
       
        $ledgers['customer_ledger_id'] = $customer_ledger_id;
        $ledger_from                   = $customer_ledger_id;
        $ledgers['ledger_from'] = $ledger_from;
        $ledgers['ledger_to']   = $sales_ledger_id;
        $vouchers              = array();
        $vouchers_new          = array();
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        if ($data_main['sales_gst_payable'] != "yes"){
            $grand_total = $data_main['sales_grand_total'];
        } else {
            $total_tax_amount = ($data_main['sales_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount'] + $data_main['sales_tax_cess_amount']);
            $grand_total      = bcsub($data_main['sales_grand_total'] , $total_tax_amount,2);
        }
        if ($data_main['sales_type_of_supply'] == "export_with_payment"){
            $total_tax_amount = ($data_main['sales_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount'] + $data_main['sales_tax_cess_amount']);
            $grand_total      = bcsub($data_main['sales_grand_total'] , $total_tax_amount,2);
        }
        if (isset($data_main['sales_tds_amount']) && $data_main['sales_tds_amount'] > 0){
            $grand_total = bcsub($grand_total , $data_main['sales_tds_amount'],2);
        }
        
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
        $this->db->set('customer_payable_amount',$grand_total);
        $this->db->where('sales_id',$data_main['sales_id']);
        $this->db->update('sales');
        $sub_total = $data_main['sales_sub_total'];
        /* discount slab */
        $discount_sum = 0;
        if ($data_main['sales_discount_amount'] > 0){
           
            foreach ($js_data as $key => $value){
                $discount_sum = bcadd($discount_sum , $value['sales_item_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                if(@$value['sales_item_scheme_discount_amount']){
                    $discount_sum = bcadd($discount_sum , $value['sales_item_scheme_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                }
            }
        
            $sub_total = bcsub($sub_total , $discount_sum,2);
        }
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
            $converted_voucher_amount = $sub_total;
        } else{
            $converted_voucher_amount = 0;
        }
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
        if ($data_main['sales_tds_amount'] > 0 || $data_main['sales_tcs_amount'] > 0){
            foreach ($tds_slab_items as $key => $value) {
                if ($key == 0){
                    continue;
                }
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $value;
                } else{
                    $converted_voucher_amount = 0;
                }
                /*if ($data_main['sales_tcs_amount'] > 0) {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }*/
                if (in_array($key, $tds_slab_minus)){
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_to'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_from'];
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
        
        if (!empty($cgst_slab_items) || !empty($sgst_slab_items)) {
            foreach ($cgst_slab_items as $key => $value)
            {
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $value;
                }
                else
                {
                    $converted_voucher_amount = 0;
                }
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
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $value;
                }
                else
                {
                    $converted_voucher_amount = 0;
                }
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
        } elseif (!empty($igst_slab_items)) {
            
            foreach ($igst_slab_items as $key => $value) {
                $converted_voucher_amount = 0;
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $value;
                }
                if (in_array($key , $igst_slab_minus)) {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_from'];
                }else{
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_to'];
                }
                /*$dr_amount = '';
                $cr_amount = $value;
                $ledger_to = $ledgers['ledger_to'];*/
                $vouchers_new[] = array(
                            "ledger_from"              => $key,
                            "ledger_to"                => $ledgers['ledger_to'],
                            "sales_voucher_id"         => '' ,
                            "voucher_amount"           => $value ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $dr_amount ,
                            "cr_amount"                => $cr_amount,
                            'ledger_id'                => $key
                        );
            }
        }

        if($data_main['sales_tax_cess_amount'] > 0 && $data_main['sales_type_of_supply'] != 'export_without_payment' && $data_main['sales_gst_payable'] != "yes"){
            foreach ($cess_slab_items as $key => $value) {
                $converted_voucher_amount = 0;
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                    $converted_voucher_amount = $value;
                } 
                if (in_array($key , $cess_slab_minus)) {
                    $dr_amount = $value;
                    $cr_amount = '';
                }else{
                    $dr_amount = '';
                    $cr_amount = $value;
                }
                $vouchers_new[] = array(
                            "ledger_from"              => $key,
                            "ledger_to"                => $ledgers['ledger_to'],
                            "sales_voucher_id"         => '' ,
                            "voucher_amount"           => $value ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $dr_amount,
                            "cr_amount"                => $cr_amount,
                            'ledger_id'                => $key
                        );
            }
        }
        
        if (in_array($charges_sub_module_id , $section_modules['access_sub_modules'])){
            $default_Freight_id = $sales_ledger['Freight'];
            $Freight_ledger_name = $this->ledger_model->getDefaultLedgerId($default_Freight_id);
                
            $Freight_ary = array(
                            'ledger_name' => 'Freight collected',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Income',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($Freight_ledger_name)){
                $Freight_ledger = $Freight_ledger_name->ledger_name;
                /*$Freight_ledger = str_ireplace('{{SECTION}}',$section_name , $Freight_ledger);*/
                /*$Freight_ledger = str_ireplace('{{X}}',$Freight_name, $Freight_ledger);*/
                $Freight_ary['ledger_name'] = $Freight_ledger;
                $Freight_ary['primary_grp'] = $Freight_ledger_name->sub_group_1;
                $Freight_ary['second_grp'] = $Freight_ledger_name->sub_group_2;
                $Freight_ary['main_grp'] = $Freight_ledger_name->main_group;
                $Freight_ary['default_ledger_id'] = $Freight_ledger_name->ledger_id;
            }
            $freight_charge_ledger_id = $this->ledger_model->getGroupLedgerId($Freight_ary);

            /*$freight_charge_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => 'Freight collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Income',
                                            'amount' =>  0
                                        ));*/
            $default_insurance_id = $sales_ledger['Insurance'];
            $insurance_ledger_name = $this->ledger_model->getDefaultLedgerId($default_insurance_id);
                
            $insurance_ary = array(
                            'ledger_name' => 'Insurance Charges collected',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Income',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($insurance_ledger_name)){
                $insurance_ary['ledger_name'] = $insurance_ledger_name->ledger_name;
                $insurance_ary['primary_grp'] = $insurance_ledger_name->sub_group_1;
                $insurance_ary['second_grp'] = $insurance_ledger_name->sub_group_2;
                $insurance_ary['main_grp'] = $insurance_ledger_name->main_group;
                $insurance_ary['default_ledger_id'] = $insurance_ledger_name->ledger_id;
            }
            $insurance_charge_ledger_id = $this->ledger_model->getGroupLedgerId($insurance_ary);

            /*$insurance_charge_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => 'Insurance Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Income',
                                            'amount' =>  0
                                        ));*/

            $default_packing_id = $sales_ledger['Packing'];
            $packing_ledger_name = $this->ledger_model->getDefaultLedgerId($default_packing_id);
                
            $packing_ary = array(
                            'ledger_name' => 'Packing Charges collected',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Income',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($packing_ledger_name)){
                $packing_ary['ledger_name'] = $packing_ledger_name->ledger_name;
                $packing_ary['primary_grp'] = $packing_ledger_name->sub_group_1;
                $packing_ary['second_grp'] = $packing_ledger_name->sub_group_2;
                $packing_ary['main_grp'] = $packing_ledger_name->main_group;
                $packing_ary['default_ledger_id'] = $packing_ledger_name->ledger_id;
            }
            $packing_charge_ledger_id = $this->ledger_model->getGroupLedgerId($packing_ary);
            
            /*$packing_charge_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => 'Packing Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Income',
                                            'amount' =>  0
                                        ));*/

            $default_incidental_id = $sales_ledger['Incidental'];
            $incidental_ledger_name = $this->ledger_model->getDefaultLedgerId($default_incidental_id);
                
            $incidental_ary = array(
                            'ledger_name' => 'Incidental Charges collected',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Income',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($incidental_ledger_name)){
                $incidental_ary['ledger_name'] = $incidental_ledger_name->ledger_name;
                $incidental_ary['primary_grp'] = $incidental_ledger_name->sub_group_1;
                $incidental_ary['second_grp'] = $incidental_ledger_name->sub_group_2;
                $incidental_ary['main_grp'] = $incidental_ledger_name->main_group;
                $incidental_ary['default_ledger_id'] = $incidental_ledger_name->ledger_id;
            }
            $incidental_charge_ledger_id = $this->ledger_model->getGroupLedgerId($incidental_ary);

            /*$incidental_charge_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => 'Incidental Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Income',
                                            'amount' =>  0
                                        ));*/
            $default_other_inclusive_id = $sales_ledger['Inclusive'];
            $other_inclusive_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_inclusive_id);
                
            $other_inclusive_ary = array(
                            'ledger_name' => 'Other Inclusive Charges collected',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Income',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($other_inclusive_ledger_name)){
                $other_inclusive_ary['ledger_name'] = $other_inclusive_ledger_name->ledger_name;
                $other_inclusive_ary['primary_grp'] = $other_inclusive_ledger_name->sub_group_1;
                $other_inclusive_ary['second_grp'] = $other_inclusive_ledger_name->sub_group_2;
                $other_inclusive_ary['main_grp'] = $other_inclusive_ledger_name->main_group;
                $other_inclusive_ary['default_ledger_id'] = $other_inclusive_ledger_name->ledger_id;
            }
            $other_inclusive_charge_ledger_id = $this->ledger_model->getGroupLedgerId($other_inclusive_ary);

            /*$other_inclusive_charge_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => 'Other Inclusive Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Income',
                                            'amount' =>  0
                                        ));*/
            $default_other_exclusive_id = $sales_ledger['Exclusive'];
            $other_exclusive_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_exclusive_id);
                
            $other_exclusive_ary = array(
                            'ledger_name' => 'Other Exclusive Charges collected',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Expenses',
                            'default_ledger_id' => 0,
                            'amount' => 0
                        );
            if(!empty($other_exclusive_ledger_name)){
                $other_exclusive_ary['ledger_name'] = $other_exclusive_ledger_name->ledger_name;
                $other_exclusive_ary['primary_grp'] = $other_exclusive_ledger_name->sub_group_1;
                $other_exclusive_ary['second_grp'] = $other_exclusive_ledger_name->sub_group_2;
                $other_exclusive_ary['main_grp'] = $other_exclusive_ledger_name->main_group;
                $other_exclusive_ary['default_ledger_id'] = $other_exclusive_ledger_name->ledger_id;
            }
            $other_exclusive_charge_ledger_id = $this->ledger_model->getGroupLedgerId($other_exclusive_ary);

            /*$other_exclusive_charge_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                            'ledger_name' => 'Other Exclusive Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Income',
                                            'amount' =>  0
                                        ));*/
            if (isset($freight_charge_ledger_id) && $data_main['freight_charge_amount'] > 0) {
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                    $converted_voucher_amount = $data_main['freight_charge_amount'];
                } else {
                    $converted_voucher_amount = 0;
                }
                $vouchers_new[] = array(
                                "ledger_from"              => $freight_charge_ledger_id,
                                "ledger_to"                => $ledgers['ledger_to'],
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['freight_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => '',
                                "cr_amount"                => $data_main['freight_charge_amount'],
                                'ledger_id'                => $freight_charge_ledger_id
                            );
            } 
            if (isset($insurance_charge_ledger_id) && $data_main['insurance_charge_amount'] > 0)
            {
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $data_main['insurance_charge_amount'];
                }
                else
                {
                    $converted_voucher_amount = 0;
                }
                $vouchers_new[] = array(
                                "ledger_from"              => $insurance_charge_ledger_id,
                                "ledger_to"                => $ledgers['ledger_to'],
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['insurance_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => '',
                                "cr_amount"                => $data_main['insurance_charge_amount'],
                                'ledger_id'                => $insurance_charge_ledger_id
                            );
            } 
            if (isset($packing_charge_ledger_id) && $data_main['packing_charge_amount'] > 0)
            {
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $data_main['packing_charge_amount'];
                }
                else
                {
                    $converted_voucher_amount = 0;
                }
                $vouchers_new[] = array(
                                "ledger_from"              => $packing_charge_ledger_id ,
                                "ledger_to"                => $ledgers['ledger_to'] ,
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['packing_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => '' ,
                                "cr_amount"                => $data_main['packing_charge_amount'],
                                'ledger_id'                => $packing_charge_ledger_id
                            );
            } if (isset($incidental_charge_ledger_id) && $data_main['incidental_charge_amount'] > 0)
            {
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $data_main['incidental_charge_amount'];
                }
                else
                {
                    $converted_voucher_amount = 0;
                }
                $vouchers_new[] = array(
                                "ledger_from"              => $incidental_charge_ledger_id ,
                                "ledger_to"                => $ledgers['ledger_to'] ,
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['incidental_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => '' ,
                                "cr_amount"                => $data_main['incidental_charge_amount'],
                                'ledger_id'                => $incidental_charge_ledger_id
                            );
            } 

            if (isset($other_inclusive_charge_ledger_id) && $data_main['inclusion_other_charge_amount'] > 0){
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $data_main['inclusion_other_charge_amount'];
                }
                else
                {
                    $converted_voucher_amount = 0;
                }
                $vouchers_new[] = array(
                                    "ledger_from"              => $other_inclusive_charge_ledger_id ,
                                    "ledger_to"                => $ledgers['ledger_to'] ,
                                    "sales_voucher_id"         => '' ,
                                    "voucher_amount"           => $data_main['inclusion_other_charge_amount'] ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => '' ,
                                    "cr_amount"                => $data_main['inclusion_other_charge_amount'],
                                    'ledger_id'                => $other_inclusive_charge_ledger_id
                                );
            } if (isset($other_exclusive_charge_ledger_id) && $data_main['exclusion_other_charge_amount'] > 0)
            {
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $data_main['exclusion_other_charge_amount'];
                }
                else
                {
                    $converted_voucher_amount = 0;
                }
                $vouchers_new[] = array(
                                    "ledger_from"              => $other_exclusive_charge_ledger_id ,
                                    "ledger_to"                => $ledgers['ledger_from'] ,
                                    "sales_voucher_id"         => '' ,
                                    "voucher_amount"           => $data_main['exclusion_other_charge_amount'] ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => $data_main['exclusion_other_charge_amount'] ,
                                    "cr_amount"                => '',
                                    'ledger_id'                => $other_exclusive_charge_ledger_id
                                );
            }
        }
        /* discount slab */
        $discount_sum = 0;
        if ($data_main['sales_discount_amount'] > 0){
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
                $discount_sum = bcadd($discount_sum , $value['sales_item_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
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

        if (@$data_main['sales_cash_discount']){
             
            $discount_ary = array(
                            'ledger_name' => 'Cash Discount',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Indirect Expenses',
                            'default_ledger_id' => 0,
                            'amount' => $data_main['sales_cash_discount']
                        );
           
            $cash_discount_ledger_id = $this->ledger_model->getGroupLedgerId($discount_ary);
            
            $ledgers['cash_discount_ledger_id'] = $cash_discount_ledger_id;
           
            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $converted_voucher_amount = $data_main['sales_cash_discount'];
            } else {
                $converted_voucher_amount = 0;
            }
            $vouchers_new[] = array(
                                "ledger_from"              => $cash_discount_ledger_id,
                                "ledger_to"                => $sales_ledger_id,
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['sales_cash_discount'],
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['sales_cash_discount'],
                                "cr_amount"                => '',
                                'ledger_id'                => $cash_discount_ledger_id
                            );
        }
        /* discount slab ends */
        /* Round off */
        if ($data_main['round_off_amount'] > 0 || $data_main['round_off_amount'] < 0){
            $round_off_amount = $data_main['round_off_amount'];
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
            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                $converted_voucher_amount = $round_off_amount;
            } else {
                $converted_voucher_amount = 0;
            }
            
            if(!empty($roundoff_ledger_name)){
                $round_off_ary['ledger_name'] = $roundoff_ledger_name->ledger_name;
                $round_off_ary['primary_grp'] = $roundoff_ledger_name->sub_group_1;
                $round_off_ary['second_grp'] = $roundoff_ledger_name->sub_group_2;
                $round_off_ary['main_grp'] = $roundoff_ledger_name->main_group;
                $round_off_ary['default_ledger_id'] = $roundoff_ledger_name->ledger_id;
            }
            $round_off_ledger_id = $this->ledger_model->getGroupLedgerId($round_off_ary);

           // $round_off_ledger_id            = $this->ledger_model->addLedger($title , $subgroup);
            
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
        /*print_r($vouchers_new);exit();*/
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
        return $vouchers;
    }

    public function sales_voucher_entry($data_main , $js_data , $action , $branch){
        $sales_voucher_module_id = $this->config->item('sales_module');
        $module_id               = $sales_voucher_module_id;
        $modules                 = $this->get_modules();
        $privilege               = "view_privilege";
        $section_modules         = $this->get_section_modules($sales_voucher_module_id , $modules , $privilege);
        
        $access_sub_modules    = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings       = $section_modules['access_settings'];
        /* generated voucher number */
        $vouchers = $this->sales_vouchers($section_modules , $data_main , $js_data , $branch);
        $grand_total = $data_main['sales_grand_total'];
        /*if ($data_main['sales_gst_payable'] != "yes"){
        } else {
            $total_tax_amount = ($data_main['sales_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount']);
            $grand_total      = bcsub($data_main['sales_grand_total'] , $total_tax_amount,$section_modules['access_common_settings'][0]->amount_precision);
        }*/
        $table           = 'sales_voucher';
        $reference_key   = 'sales_voucher_id';
        $reference_table = 'accounts_sales_voucher';
        if ($action == "add"){
            /* generated voucher number */
            $primary_id      = "sales_voucher_id";
            $table_name      = $this->config->item('sales_voucher_table');
            $date_field_name = "voucher_date";
            $current_date    = $data_main['sales_date'];
            $voucher_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);
            $headers = array(
                "voucher_date"      => $data_main['sales_date'] ,
                "voucher_number"    => $voucher_number ,
                "party_id"          => $data_main['sales_party_id'] ,
                "party_type"        => $data_main['sales_party_type'] ,
                "reference_id"      => $data_main['sales_id'] ,
                "reference_type"    => 'sales' ,
                "reference_number"  => $data_main['sales_invoice_number'] ,
                "receipt_amount"    => $grand_total ,
                "from_account"      => $data_main['from_account'] ,
                "to_account"        => $data_main['to_account'] ,
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "description"       => '' ,
                "added_date"        => date('Y-m-d') ,
                "added_user_id"     => $this->session->userdata('SESS_USER_ID') ,
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                "currency_id"       => $data_main['currency_id'] ,
                "note1"             => $data_main['note1'] ,
                "note2"             => $data_main['note2']
            );
            /*$sales_voucher_ary = array(
                    'company_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'voucher_number' => $voucher_number ,
                    'invoice_date' => $data_main['sales_date'],
                    'invoice_number' => $data_main['sales_invoice_number'],
                    'invoice_narration' => '',
                    'invoice_total' => $grand_total,
                    'voucher_type' => 'sales',
                    'voucher_status' => '1',
                    'customer_comments' => ''
            );*/
            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                $headers['converted_receipt_amount'] = $grand_total;
            }else{
                $headers['converted_receipt_amount'] = 0;
            }
            $this->general_model->addVouchers($table , $reference_key , $reference_table , $headers , $vouchers);
            
        } else if ($action == "edit"){
            $headers = array(
                "voucher_date"      => $data_main['sales_date'] ,
                "party_id"          => $data_main['sales_party_id'] ,
                "party_type"        => $data_main['sales_party_type'] ,
                "reference_id"      => $data_main['sales_id'] ,
                "reference_type"    => 'sales' ,
                "reference_number"  => $data_main['sales_invoice_number'] ,
                "receipt_amount"    => $grand_total ,
                "from_account"      => $data_main['from_account'] ,
                "to_account"        => $data_main['to_account'] ,
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "description"       => '' ,
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                "currency_id"       => $data_main['currency_id'] ,
                "updated_date"      => date('Y-m-d') ,
                "updated_user_id"   => $this->session->userdata('SESS_USER_ID') ,
                "note1"             => $data_main['note1'] ,
                "note2"             => $data_main['note2']
            );
            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $headers['converted_receipt_amount'] = $grand_total;
            } else {
                $headers['converted_receipt_amount'] = 0;
            }
            $sales_voucher_data = $this->general_model->getRecords('sales_voucher_id' , 'sales_voucher' , array(
                'reference_id'  => $data_main['sales_id'],'reference_type' => 'sales','delete_status' => 0));
            if ($sales_voucher_data){
                $sales_voucher_id        = $sales_voucher_data[0]->sales_voucher_id;
                $this->general_model->updateData('sales_voucher', $headers,array('sales_voucher_id' => $sales_voucher_id ));
                $string = 'accounts_sales_id,delete_status,ledger_id,voucher_amount,dr_amount,cr_amount';
                $table = 'accounts_sales_voucher';
                $where = array('sales_voucher_id' => $sales_voucher_id);
                $old_sales_voucher_items = $this->general_model->getRecords($string , $table , $where , $order
                    = "");
                $old_sales_ledger_ids = $this->getValues($old_sales_voucher_items,'ledger_id');
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
                /*if (count($old_sales_voucher_items) == count($vouchers)) {
                    foreach ($old_sales_voucher_items as $key => $value) {
                        $vouchers[$key]['sales_voucher_id'] = $sales_voucher_id;
                        $vouchers[$key]['delete_status']    = 0;
                        $table                              = 'accounts_sales_voucher';
                        $where                              = array(
                            'accounts_sales_id' => $value->accounts_sales_id );
                        $this->general_model->updateBunchVoucher($vouchers[$key],$where,$headers['voucher_date']);
                        $this->general_model->updateData($table , $vouchers[$key] , $where);
                    }
                }else if (count($old_sales_voucher_items) < count($vouchers)) {
                    $i = 0;
                    foreach ($old_sales_voucher_items as $key => $value)
                    {
                        $vouchers[$key]['sales_voucher_id'] = $sales_voucher_id;
                        $vouchers[$key]['delete_status']    = 0;
                        $table                              = 'accounts_sales_voucher';
                        $where                              = array(
                            'accounts_sales_id' => $value->accounts_sales_id );
                        $this->general_model->updateBunchVoucher($vouchers[$key],$where,$headers['voucher_date']);
                        $this->general_model->updateData($table , $vouchers[$key] , $where);
                        $i                                  = $key;
                    }
                    for ($j = $i + 1; $j < count($vouchers); $j++)
                    {
                        $vouchers[$j]['sales_voucher_id'] = $sales_voucher_id;
                        $table                            = 'accounts_sales_voucher';
                        $this->general_model->insertData($table , $vouchers[$j]);
                    }
                }
                else
                {
                    $i = 0;
                    foreach ($old_sales_voucher_items as $key => $value) {
                        $vouchers[$key]['sales_voucher_id'] = $sales_voucher_id;
                        $vouchers[$key]['delete_status']    = 0;
                        $table                              = 'accounts_sales_voucher';
                        $where                              = array(
                            'accounts_sales_id' => $value->accounts_sales_id );
                        $this->general_model->updateBunchVoucher($vouchers[$key],$where,$headers['voucher_date']);
                        $this->general_model->updateData($table , $vouchers[$key] , $where);
                        $i                                  = $key;
                        if (($key + 1) == count($vouchers))
                        {
                            break;
                        }
                    }
                    for ($j = $i + 1; $j < count($old_sales_voucher_items); $j++)
                    {
                        $table      = 'accounts_sales_voucher';
                        $where      = array(
                            'accounts_sales_id' => $old_sales_voucher_items[$j]->accounts_sales_id );
                        $sales_data = array(
                            'delete_status' => 1 );
                        $this->general_model->updateBunchVoucher($vouchers[$key], $where, $headers['voucher_date']);
                        $this->general_model->updateData($table , $sales_data , $where);
                    }
                }*/
            }
        }
    }
    public function edit_sales() {
        $data            = $this->get_default_country_state();
        $sales_id        = $this->input->post('sales_id');
        $sales_module_id = $this->config->item('sales_module');
        $module_id       = $sales_module_id;
        $modules         = $this->modules;
        $privilege       = "edit_privilege";
        $section_modules = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* Modules Present */
        $data['sales_module_id']           = $sales_module_id;
        $data['module_id']                 = $sales_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('product_module');
        $data['service_module_id']         = $this->config->item('service_module');
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['category_module_id']        = $this->config->item('category_module');
        $data['subcategory_module_id']     = $this->config->item('subcategory_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $currency = $this->input->post('currency_id');
        if ($section_modules['access_settings'][0]->invoice_creation == "automatic")
        {
            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old'))
            {
                $primary_id      = "sales_id";
                $table_name      = $this->config->item('sales_table');
                $date_field_name = "sales_date";
                $current_date    = date('Y-m-d',strtotime($this->input->post('invoice_date')));
                $invoice_number  = $this->generate_invoice_number($section_modules['access_settings'] , $primary_id , $table_name , $date_field_name , $current_date);
            }
            else
            {
                $invoice_number = $this->input->post('invoice_number');
            }
        }else{
            $invoice_number = $this->input->post('invoice_number');
        }
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0 ;
        $sales_data = array(
            "sales_date"                            => date('Y-m-d',strtotime($this->input->post('invoice_date'))) ,
            "sales_invoice_number"                  => $invoice_number ,
            "sales_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
            "sales_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
            "sales_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
            "sales_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
            "sales_tax_cess_amount"                 => 0 ,
            "sales_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
            "sales_tds_amount"                      => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0 ,
            "sales_tcs_amount"
                      => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0 ,
            "sales_igst_amount"                     => 0 ,
            "sales_cgst_amount"                     => 0 ,
            "sales_sgst_amount"                     => 0 ,
            "from_account"                          => 'customer' ,
            "to_account"                            => 'sales' ,
            "sales_paid_amount"                     => 0 ,
            "credit_note_amount"                    => 0 ,
            "debit_note_amount"                     => 0 ,
            "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
            "sales_party_id"                        => $this->input->post('customer') ,
            "ship_to_customer_id"                   => $this->input->post('ship_to') ,
            "sales_party_type"                      => "customer" ,
            "sales_nature_of_supply"                => $this->input->post('nature_of_supply') ,
            "sales_order_number"                    => $this->input->post('order_number') ,
            "sales_type_of_supply"                  => $this->input->post('type_of_supply') ,
            "sales_gst_payable"                     => $this->input->post('gst_payable') ,
            /*"department"                            => $this->input->post('department') ,*/
            "sales_billing_country_id"              => $this->input->post('billing_country') ,
            "sales_billing_state_id"                => $this->input->post('billing_state') ,
            "branch_id"                             => $this->session->userdata('SESS_BRANCH_ID') ,
            "currency_id"                           => $this->input->post('currency_id') ,
            "updated_date"                          => date('Y-m-d') ,
            "updated_user_id"                       => $this->session->userdata('SESS_USER_ID') ,
            "transporter_name"                      => $this->input->post('transporter_name') ,
            "transporter_gst_number"                => $this->input->post('transporter_gst_number') ,
            "lr_no"                                 => $this->input->post('lr_no') ,
            "vehicle_no"                            => $this->input->post('vehicle_no') ,
            "mode_of_shipment"                      => $this->input->post('mode_of_shipment') ,
            "ship_by"                               => $this->input->post('ship_by') ,
            "net_weight"                            => $this->input->post('net_weight') ,
            "gross_weight"                          => $this->input->post('gross_weight') ,
            "origin"                                => $this->input->post('origin') ,
            "destination"                           => $this->input->post('destination') ,
            "shipping_type"                         => $this->input->post('shipping_type') ,
            "shipping_type_place"                   => $this->input->post('shipping_type_place') ,
            "lead_time"                             => $this->input->post('lead_time') ,
            "shipping_address_id"                   => $this->input->post('shipping_address') ,
            "warranty"                              => $this->input->post('warranty') ,
            "payment_mode"                          => $this->input->post('payment_mode') ,
            /*"billing_address_id" => $this->input->post('billing_address'),*/
            "freight_charge_amount"                 => $this->input->post('freight_charge_amount') ? (float) $this->input->post('freight_charge_amount') : 0 ,
            "freight_charge_tax_percentage"         => $this->input->post('freight_charge_tax_percentage') ? (float) $this->input->post('freight_charge_tax_percentage') : 0 ,
            "freight_charge_tax_amount"             => $this->input->post('freight_charge_tax_amount') ? (float) $this->input->post('freight_charge_tax_amount') : 0 ,
            "total_freight_charge"                  => $this->input->post('total_freight_charge') ? (float) $this->input->post('total_freight_charge') : 0 ,
            "insurance_charge_amount"               => $this->input->post('insurance_charge_amount') ? (float) $this->input->post('insurance_charge_amount') : 0 ,
            "insurance_charge_tax_percentage"       => $this->input->post('insurance_charge_tax_percentage') ? (float) $this->input->post('insurance_charge_tax_percentage') : 0 ,
            "insurance_charge_tax_amount"           => $this->input->post('insurance_charge_tax_amount') ? (float) $this->input->post('insurance_charge_tax_amount') : 0 ,
            "total_insurance_charge"                => $this->input->post('total_insurance_charge') ? (float) $this->input->post('total_insurance_charge') : 0 ,
            "packing_charge_amount"                 => $this->input->post('packing_charge_amount') ? (float) $this->input->post('packing_charge_amount') : 0 ,
            "packing_charge_tax_percentage"         => $this->input->post('packing_charge_tax_percentage') ? (float) $this->input->post('packing_charge_tax_percentage') : 0 ,
            "packing_charge_tax_amount"             => $this->input->post('packing_charge_tax_amount') ? (float) $this->input->post('packing_charge_tax_amount') : 0 ,
            "total_packing_charge"                  => $this->input->post('total_packing_charge') ? (float) $this->input->post('total_packing_charge') : 0 ,
            "incidental_charge_amount"              => $this->input->post('incidental_charge_amount') ? (float) $this->input->post('incidental_charge_amount') : 0 ,
            "incidental_charge_tax_percentage"      => $this->input->post('incidental_charge_tax_percentage') ? (float) $this->input->post('incidental_charge_tax_percentage') : 0 ,
            "incidental_charge_tax_amount"          => $this->input->post('incidental_charge_tax_amount') ? (float) $this->input->post('incidental_charge_tax_amount') : 0 ,
            "total_incidental_charge"               => $this->input->post('total_incidental_charge') ? (float) $this->input->post('total_incidental_charge') : 0 ,
            "inclusion_other_charge_amount"         => $this->input->post('inclusion_other_charge_amount') ? (float) $this->input->post('inclusion_other_charge_amount') : 0 ,
            "inclusion_other_charge_tax_percentage" => $this->input->post('inclusion_other_charge_tax_percentage') ? (float) $this->input->post('inclusion_other_charge_tax_percentage') : 0 ,
            "inclusion_other_charge_tax_amount"     => $this->input->post('inclusion_other_charge_tax_amount') ? (float) $this->input->post('inclusion_other_charge_tax_amount') : 0 ,
            "total_inclusion_other_charge"          => $this->input->post('total_other_inclusive_charge') ? (float) $this->input->post('total_other_inclusive_charge') : 0 ,
            "exclusion_other_charge_amount"         => $this->input->post('exclusion_other_charge_amount') ? (float) $this->input->post('exclusion_other_charge_amount') : 0 ,
            "exclusion_other_charge_tax_percentage" => $this->input->post('exclusion_other_charge_tax_percentage') ? (float) $this->input->post('exclusion_other_charge_tax_percentage') : 0 ,
            "exclusion_other_charge_tax_amount"     => $this->input->post('exclusion_other_charge_tax_amount') ? (float) $this->input->post('exclusion_other_charge_tax_amount') : 0 ,
            "total_exclusion_other_charge"          => $this->input->post('total_other_exclusive_charge') ? (float) $this->input->post('total_other_exclusive_charge') : 0 ,
            "total_other_amount"                    => $this->input->post('total_other_amount') ? (float) $this->input->post('total_other_amount') : 0 ,
            "total_other_taxable_amount"             =>$this->input->post('total_other_taxable_amount') ? (float) $this->input->post('total_other_taxable_amount') : 0 ,
            "note1"                                 => $this->input->post('note1') ,
            "note2"                                 => $this->input->post('note2')
        );
        $sales_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $sales_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $sales_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $sales_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $sales_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $sales_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        /* customize for leather craft*/
        if(@$this->input->post('cmb_department')){
            $sales_data['department_id'] = $this->input->post('cmb_department');
        }
        if(@$this->input->post('cmb_subdepartment')){
            $sales_data['sub_department_id'] = $this->input->post('cmb_subdepartment');
        }
        if(@$this->input->post('cash_discount')){
            $sales_data['sales_cash_discount'] = $this->input->post('cash_discount');
        }

        if(@$this->input->post('brand_invoice_number')){
            $sales_data['sales_brand_invoice_number'] = $this->input->post('brand_invoice_number');
        }

        if(@$this->input->post('brand_id')){
            $sales_data['brand_id'] = $this->input->post('brand_id');
        }

        $round_off_value = $sales_data['sales_grand_total'];
        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" || $this->input->post('round_off_key') == "yes"){
            if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 ){
                $round_off_value = $this->input->post('round_off_value');
            }
        }
        $sales_data['round_off_amount'] = bcsub($sales_data['sales_grand_total'] , $round_off_value,$section_modules['access_common_settings'][0]->amount_precision);
        $sales_data['sales_grand_total'] = $round_off_value;
        $sales_data['customer_payable_amount'] = $sales_data['sales_grand_total'];
        if (isset($sales_data['sales_tds_amount']) && $sales_data['sales_tds_amount'] > 0){
            $sales_data['customer_payable_amount'] = bcsub($sales_data['sales_grand_total'], $sales_data['sales_tds_amount']);
        }
        $tax_type         = $this->input->post('tax_type');
        $sales_tax_amount = $sales_data['sales_tax_amount'];
        $sales_tax_amount = $sales_data['sales_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));
        if ($section_modules['access_settings'][0]->tax_type == "gst"){
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            if ($sales_data['sales_billing_state_id'] != 0){
                if ($data['branch'][0]->branch_state_id == $sales_data['sales_billing_state_id']) {
                    $sales_data['sales_igst_amount'] = 0;
                    $sales_data['sales_cgst_amount'] = ($sales_tax_amount * $cgst_amount_percentage) / 100;
                    $sales_data['sales_sgst_amount'] = ($sales_tax_amount * $sgst_amount_percentage) / 100;
                    $sales_data['sales_tax_cess_amount'] = $total_cess_amnt;
                }
                else
                {
                    $sales_data['sales_igst_amount'] = $sales_tax_amount;
                    $sales_data['sales_cgst_amount'] = 0;
                    $sales_data['sales_sgst_amount'] = 0;
                    $sales_data['sales_tax_cess_amount'] = $total_cess_amnt;
                }
            }
            else
            {
                if ($sales_data['sales_type_of_supply'] == "export_with_payment")
                {
                    $sales_data['sales_igst_amount'] = $sales_tax_amount;
                    $sales_data['sales_cgst_amount'] = 0;
                    $sales_data['sales_sgst_amount'] = 0;
                    $sales_data['sales_tax_cess_amount'] = $total_cess_amnt;
                }
            }
        }
        
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
            $sales_data['converted_grand_total'] = $sales_data['sales_grand_total'];
        } else {
            $sales_data['converted_grand_total'] = 0;
        }
        $data_main   = array_map('trim' , $sales_data);
        $sales_table = $this->config->item('sales_table');
        $where       = array(
            'sales_id' => $sales_id );

        if ($this->general_model->updateData($sales_table , $data_main , $where)){
            $successMsg = 'Sales Updated Successfully';
            $this->session->set_flashdata('sales_success',$successMsg);
            $log_data              = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $sales_id,
                'table_name'        => $sales_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'Sales Updated' );
            $data_main['sales_id'] = $sales_id;
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $sales_item_data       = $this->input->post('table_data');
            $js_data               = json_decode($sales_item_data);
            $js_data               = array_reverse($js_data);
            $item_table            = $this->config->item('sales_item_table');
            if (!empty($js_data)) {
                $js_data1 = array();
                $new_item_ids = $this->getValues($js_data,'item_id'); 
                
                $string          = 'sales_item_id,sales_item_quantity,item_type,item_id';
                $table           = 'sales_item';
                $where           = array(
                    'sales_id'      => $sales_id ,
                    'delete_status' => 0 );
                $old_sales_items = $this->general_model->getRecords($string , $table , $where , $order           = "");
                $old_item_ids = $this->getValues($old_sales_items,'item_id');
                $not_deleted_ids= array();

                foreach ($old_sales_items as $key => $value){
                    if($value->item_type == 'product' ){
                        $product_string = '*';
                        $product_table  = 'products';
                        $product_where  = array(
                            'product_id' => $value->item_id );
                        $product        = $this->general_model->getRecords($product_string , $product_table , $product_where , $order          = "");
                        $product_qty    = bcadd($product[0]->product_quantity , $value->sales_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
                        $product_data   = array(
                            'product_quantity' => $product_qty );
                        $this->general_model->updateData($product_table , $product_data , $product_where);
                        /*$this->producthook->UpdateProductStock(array('product_id' => $value->item_id,'product_quantity' => $product_qty));*/
                        //update stock history
                        $where   = array(
                            'item_id'        => $value->item_id ,
                            'reference_id'   => $sales_id ,
                            'reference_type' => 'sales' ,
                            'delete_status'  => 0 );
                        $this->db->where($where);
                        $history = $this->db->get('quantity_history')->result();
                        if (!empty($history)){
                            $history_quantity        = bcadd($history[0]->quantity , $value->sales_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
                            $update_history_quantity = array(
                                'quantity'        => $history_quantity ,
                                'updated_date'    => date('Y-m-d') ,
                                'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                            $this->db->where($where);
                            $this->db->update('quantity_history' , $update_history_quantity);
                        }else{
                            // quantity history
                            $history = array(
                                "item_id"          => $value->item_id ,
                                "item_type"        => 'product' ,
                                "reference_id"     => $sales_id ,
                                "reference_number" => $invoice_number ,
                                "reference_type"   => 'sales' ,
                                "quantity"         => 0 ,
                                "stock_type"       => 'indirect' ,
                                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') ,
                                "added_date"       => date('Y-m-d') ,
                                "entry_date"       => date('Y-m-d') ,
                                "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
                            $this->general_model->insertData("quantity_history" , $history);
                        }
                    }
                }
                
                foreach ($js_data as $key => $value) {
                    if ($value != null) {
                        $item_id   = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "item_id"                    => $value->item_id ,
                            "item_type"                  => $value->item_type ,
                            "sales_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "sales_item_free_quantity"   => (@$value->free_item_quantity ? (float) $value->free_item_quantity : 0),
                            "sales_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "sales_item_mrp_price"      => (@$value->item_mrp_price ? (float) $value->item_mrp_price : 0),
                            "sales_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "sales_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "sales_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0) ,
                            "sales_item_cash_discount_amount" => (@$value->item_cash_discount ? (float) $value->item_cash_discount : 0) ,
                            "sales_item_discount_id"     => (@$value->item_discount_id ? (float) $value->item_discount_id : 0) ,
                            "sales_item_tds_id"          => $value->item_tds_id ? (float) $value->item_tds_id : 0 ,
                            "sales_item_tds_percentage"  => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ,
                            "sales_item_tds_amount"      => $value->item_tds_amount ? (float) $value->item_tds_amount : 0 ,
                            "sales_item_grand_total"     => $value->item_grand_total ? (float) $value->item_grand_total : 0 ,
                            "sales_item_tax_id"          => $value->item_tax_id ? (float)$value->item_tax_id : 0 ,
                            "sales_item_tax_cess_id"     => $value->item_tax_cess_id ? (float)$value->item_tax_cess_id : 0 ,
                            "sales_item_igst_percentage" => 0 ,
                            "sales_item_igst_amount"     => 0 ,
                            "sales_item_cgst_percentage" => 0 ,
                            "sales_item_cgst_amount"     => 0 ,
                            "sales_item_sgst_percentage" => 0 ,
                            "sales_item_sgst_amount"     => 0 ,
                            "sales_item_tax_percentage"  => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ,
                            "sales_item_tax_amount"      => $value->item_tax_amount ? (float) $value->item_tax_amount : 0 ,
                            "sales_item_tax_cess_percentage"  =>  0 ,
                            "sales_item_tax_cess_amount"      =>  0 ,
                            "sales_item_description"     => $value->item_description ? $value->item_description : "" ,
                            "sales_item_uom_id"  => (@$value->item_uom ? $value->item_uom : ""),
                            "debit_note_quantity"        => 0 ,
                            "sales_id"                   => $sales_id );
                        $sales_item_tax_amount     = $item_data['sales_item_tax_amount'];
                        $sales_item_tax_percentage = $item_data['sales_item_tax_percentage'];

                        /* Customization leather craft fields */
                        if(@$value->item_basic_total){
                            $item_data['sales_item_basic_total'] = $value->item_basic_total;
                        }
                        if(@$value->item_selling_price){
                            $item_data['sales_item_selling_price'] = $value->item_selling_price;
                        }
                        if(@$value->item_mrkd_discount_amount){
                            $item_data['sales_item_mrkd_discount_amount'] = $value->item_mrkd_discount_amount;
                        }
                        if(@$value->item_mrkd_discount_id){
                            $item_data['sales_item_mrkd_discount_id'] = $value->item_mrkd_discount_id;
                        }
                        if(@$value->item_mrkd_discount_percentage){
                            $item_data['sales_item_mrkd_discount_percentage'] = $value->item_mrkd_discount_percentage;
                        }
                        if(@$value->item_mrgn_discount_amount){
                            $item_data['sales_item_mrgn_discount_amount'] = $value->item_mrgn_discount_amount;
                        }
                        if(@$value->item_mrgn_discount_id){
                            $item_data['sales_item_mrgn_discount_id'] = $value->item_mrgn_discount_id;
                        }
                        if(@$value->item_mrgn_discount_percentage){
                            $item_data['sales_item_mrgn_discount_percentage'] = $value->item_mrgn_discount_percentage;
                        }

                        if(@$value->item_scheme_discount_amount){
                            $item_data['sales_item_scheme_discount_amount'] = $value->item_scheme_discount_amount;
                        }
                        if(@$value->item_scheme_discount_id){
                            $item_data['sales_item_scheme_discount_id'] = $value->item_scheme_discount_id;
                        }
                        if(@$value->item_scheme_discount_percentage){
                            $item_data['sales_item_scheme_discount_percentage'] = $value->item_scheme_discount_percentage;
                        }

                        if(@$value->item_out_tax_percentage){
                            $item_data['sales_item_out_tax_percentage'] = $value->item_out_tax_percentage;
                        }
                        if(@$value->item_out_tax_amount){
                            $item_data['sales_item_out_tax_amount'] = $value->item_out_tax_amount;
                        }
                        if(@$value->item_out_tax_id){
                            $item_data['sales_item_out_tax_id'] = $value->item_out_tax_id;
                        }
                       
                        /* End leather Craft */

                        if ($tax_type == "gst") {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0 ;
                            if ($sales_data['sales_billing_state_id'] != 0){
                                if ($data['branch'][0]->branch_state_id == $sales_data['sales_billing_state_id'])
                                {
                                    $item_data['sales_item_igst_amount'] = 0;
                                    $item_data['sales_item_cgst_amount'] = ($sales_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['sales_item_sgst_amount'] = ($sales_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['sales_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['sales_item_igst_percentage'] = 0;
                                    $item_data['sales_item_cgst_percentage'] = ($sales_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['sales_item_sgst_percentage'] = ($sales_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['sales_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                                else
                                {
                                    $item_data['sales_item_igst_amount'] = $sales_item_tax_amount;
                                    $item_data['sales_item_cgst_amount'] = 0;
                                    $item_data['sales_item_sgst_amount'] = 0;
                                    $item_data['sales_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['sales_item_igst_percentage'] = $sales_item_tax_percentage;
                                    $item_data['sales_item_cgst_percentage'] = 0;
                                    $item_data['sales_item_sgst_percentage'] = 0;
                                    $item_data['sales_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }else{
                                if ($sales_data['sales_type_of_supply'] == "export_with_payment"){
                                    $item_data['sales_item_igst_amount'] = $sales_item_tax_amount;
                                    $item_data['sales_item_cgst_amount'] = 0;
                                    $item_data['sales_item_sgst_amount'] = 0;
                                    $item_data['sales_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['sales_item_igst_percentage'] = $sales_item_tax_percentage;
                                    $item_data['sales_item_cgst_percentage'] = 0;
                                    $item_data['sales_item_sgst_percentage'] = 0;
                                    $item_data['sales_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                        }
                        
                        $table = 'sales_item';
                        if (($item_key = array_search($value->item_id, $old_item_ids)) !== false) {
                            unset($old_item_ids[$item_key]);
                            $sales_item_id = $old_sales_items[$item_key]->sales_item_id;
                            array_push($not_deleted_ids,$sales_item_id );
                            $where = array('sales_item_id' => $sales_item_id );
                            $this->general_model->updateData($table , $item_data , $where);
                        }else{
                            $this->general_model->insertData($table , $item_data);
                        }
                        /* update product stock */
                        if ($value->item_type == "product" || $value->item_type == 'product_inventory'){
                            $product_string = '*';
                            $product_table  = 'products';
                            $product_where  = array('product_id' => $value->item_id );
                            $product        = $this->general_model->getRecords($product_string , $product_table , $product_where , $order          = "");
                            
                            if(@$value->free_item_quantity){
                                if($value->free_item_quantity > 0) $quantity = $quantity + $value->free_item_quantity;
                            }
                            $product_qty    = bcsub($product[0]->product_quantity , $quantity,$section_modules['access_common_settings'][0]->amount_precision);
                            $product_data   = array('product_quantity' => $product_qty );
                            $this->general_model->updateData($product_table , $product_data , $product_where);
                            /*$this->producthook->UpdateProductStock(array('product_id' => $value->item_id,'product_quantity' => $product_qty));*/
                            //update stock history
                            $where   = array(
                                'item_id'        => $value->item_id ,
                                'reference_id'   => $sales_id ,
                                'reference_type' => 'sales' ,
                                'delete_status'  => 0 );
                            $this->db->where($where);
                            $history = $this->db->get('quantity_history')->result();
                            if (!empty($history)){
                                $history_quantity        = bcsub($history[0]->quantity , $quantity, $section_modules['access_common_settings'][0]->amount_precision);
                                $update_history_quantity = array(
                                    'quantity'        => $history_quantity ,
                                    'updated_date'    => date('Y-m-d') ,
                                    'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                                $this->db->where($where);
                                $this->db->update('quantity_history' , $update_history_quantity);
                            } else {
                                // quantity history
                                $history = array(
                                    "item_id"          => $value->item_id ,
                                    "item_type"        => 'product' ,
                                    "reference_id"     => $sales_id ,
                                    "reference_number" => $invoice_number ,
                                    "reference_type"   => 'sales' ,
                                    "quantity"         => $quantity ,
                                    "stock_type"       => 'indirect' ,
                                    "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') ,
                                    "added_date"       => date('Y-m-d') ,
                                    "entry_date"       => date('Y-m-d') ,
                                    "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
                                $this->general_model->insertData("quantity_history" , $history);
                            }
                        }
                        $data_item  = array_map('trim' , $item_data);
                        $js_data1[] = $data_item;
                    }
                }
                if(!empty($old_sales_items)){
                    foreach ($old_sales_items as $key => $items) {
                        if(!in_array( $items->sales_item_id,$not_deleted_ids)){
                            $table      = 'sales_item';
                            $where      = array(
                                'sales_item_id' => $items->sales_item_id );
                            $sales_data = array(
                                'delete_status' => 1 );
                           
                            $this->general_model->updateData($table , $sales_data , $where);
                        }
                    }
                }
                $item_data = $js_data1;

                if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){
                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
                        $action = "edit";
                        $this->sales_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                    }
                }
            } 
            redirect('sales' , 'refresh');
        }else{
            $errorMsg = 'Sales Update Unsuccessful';
            $this->session->set_flashdata('sales_error',$errorMsg);
            redirect('sales' , 'refresh');
        }
    }
    
    public function view($id){
        $id                          = $this->encryption_url->decode($id);
        $data                        = array();
        $data = $this->getSalesDetails($id);
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $this->load->view('sales/view' , $data);
    }

    public function pdf($id){
        $id                = $this->encryption_url->decode($id);
        $data              = $this->get_default_country_state();
        $sales_module_id   = $this->config->item('sales_module');
        $data['module_id'] = $sales_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($sales_module_id , $modules , $privilege);
        $data              = array_merge($data , $section_modules);
        $product_module_id             = $this->config->item('product_module');
        $service_module_id             = $this->config->item('service_module');
        $customer_module_id            = $this->config->item('customer_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);
        $data = $this->getSalesDetails($id,$this->input->post());
        
        $invoice_type = $this->input->post('pdf_type_check');
        $data['invoice_type'] = '';
            if ($invoice_type == "original")
            {
                $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
            }
            elseif ($invoice_type == "duplicate")
            {
                $data['invoice_type'] = "DUPLICATE FOR SUPPLIER";
            }
            elseif($invoice_type == "triplicate"){
                $data['invoice_type'] = "TRIPLICATE FOR TRANSPORTER";
            }
        if($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath')){
            
            $orientation = $this->input->post('cmb_orient');
            if($orientation == '' || $orientation == NULL){            
                $orientation = 'portrait';
            }
            
            $paper_size = $this->input->post('cmb_size');
            if($paper_size == '' || $paper_size == NULL){
                $paper_size  = 'a4';
            }
            

            $print_type = $this->input->post('cmb_invoice_type');
            if($print_type == '' || $print_type == NULL){
                $print_type  = 'tabular';
            }
        }else{
            $paper_size  = 'a4';
            $orientation = 'portrait';        
        }

        
        $print_currency = $this->input->post('print_currency');
        $converted_rate = 1;
        
        if($print_currency != $this->session->userdata('SESS_DEFAULT_CURRENCY')){
            if($data['data'][0]->currency_converted_rate > 0)
                $converted_rate = $data['data'][0]->currency_converted_rate;
        }else{
            $currency = $this->getBranchCurrencyCode();
            $data['data'][0]->currency_name = $currency[0]->currency_name;
            $data['data'][0]->currency_code = $currency[0]->currency_code;
            $data['data'][0]->currency_symbol = $currency[0]->currency_symbol;
            $data['data'][0]->currency_symbol_pdf = $currency[0]->currency_symbol_pdf;
            $data['data'][0]->unit = $currency[0]->unit;
            $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
        }
        $data['converted_rate'] = $converted_rate;
        $pdf_json            = $data['access_settings'][0]->pdf_settings;
        $rep                 = str_replace("\\" , '' , $pdf_json);
        $data['pdf_results'] = json_decode($rep , true);
        if($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath')){
            if($print_type == 'cash_invoice'){
                $html = $this->load->view('sales/half_pdf' , $data , true);
            }elseif($print_type == 'tabular'){
                $html = $this->load->view('sales/pdf' , $data , true); 
            }elseif($print_type == 'hsn_summary'){
                $html = $this->load->view('sales/pdf_hsn' , $data , true);
            }elseif($print_type == 'aodry_format'){
                $html = $this->load->view('sales/aodry_pdf' , $data , true);
            }
        }else{
            $html = $this->load->view('sales/pdf' , $data , true); 
        }
        /*echo $html;
        exit;*/
        /*include(APPPATH . 'third_party/tcpdf/tcpdf.php');
        //     // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddPage();
        ob_start();
        $pdf->WriteHTML($html);
        $pdf->Output($data['data'][0]->sales_invoice_number . '.pdf', 'I');*/
        
        include(APPPATH . "third_party/dompdf/autoload.inc.php");
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();
        //we test first.
        //included.
        //now we can use all methods of dompdf
        //first im giving our html text to this method.
       // echo $html;
        $dompdf->load_html($html);
        
        /*$dompdf->set_option('isHtml5ParserEnabled', true);*/
        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
       // $customPaper = array(0,0,360,360);
        if($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath')){
            if($paper_size == 'half'){
                 $dompdf->setPaper(array(0,0,595.28,420));
             }else{
                $dompdf->set_paper($paper_size,$orientation);     
             }
        }else{
            $dompdf->set_paper($paper_size,$orientation); 
        }
       
       
       // "a4" => array(0, 0, 595.28, 841.89)
       // $dompdf->set_paper($paper_size , $orientation);
        //and getting rend
        /*
        static $PAPER_SIZES = array(
        "4a0" => array(0, 0, 4767.87, 6740.79),
        "2a0" => array(0, 0, 3370.39, 4767.87),
        "a0" => array(0, 0, 2383.94, 3370.39),
        "a1" => array(0, 0, 1683.78, 2383.94),
        "a2" => array(0, 0, 1190.55, 1683.78),
        "a3" => array(0, 0, 841.89, 1190.55),
        "a4" => array(0, 0, 595.28, 841.89),
        "a5" => array(0, 0, 419.53, 595.28),
        "a6" => array(0, 0, 297.64, 419.53),
        "a7" => array(0, 0, 209.76, 297.64),
        "a8" => array(0, 0, 147.40, 209.76),
        "a9" => array(0, 0, 104.88, 147.40),
        "a10" => array(0, 0, 73.70, 104.88),
        "b0" => array(0, 0, 2834.65, 4008.19),
        "b1" => array(0, 0, 2004.09, 2834.65),
        "b2" => array(0, 0, 1417.32, 2004.09),
        "b3" => array(0, 0, 1000.63, 1417.32),
        "b4" => array(0, 0, 708.66, 1000.63),
        "b5" => array(0, 0, 498.90, 708.66),
        "b6" => array(0, 0, 354.33, 498.90),
        "b7" => array(0, 0, 249.45, 354.33),
        "b8" => array(0, 0, 175.75, 249.45),
        "b9" => array(0, 0, 124.72, 175.75),
        "b10" => array(0, 0, 87.87, 124.72),
        "c0" => array(0, 0, 2599.37, 3676.54),
        "c1" => array(0, 0, 1836.85, 2599.37),
        "c2" => array(0, 0, 1298.27, 1836.85),
        "c3" => array(0, 0, 918.43, 1298.27),
        "c4" => array(0, 0, 649.13, 918.43),
        "c5" => array(0, 0, 459.21, 649.13),
        "c6" => array(0, 0, 323.15, 459.21),
        "c7" => array(0, 0, 229.61, 323.15),
        "c8" => array(0, 0, 161.57, 229.61),
        "c9" => array(0, 0, 113.39, 161.57),
        "c10" => array(0, 0, 79.37, 113.39),
        "ra0" => array(0, 0, 2437.80, 3458.27),
        "ra1" => array(0, 0, 1729.13, 2437.80),
        "ra2" => array(0, 0, 1218.90, 1729.13),
        "ra3" => array(0, 0, 864.57, 1218.90),
        "ra4" => array(0, 0, 609.45, 864.57),
        "sra0" => array(0, 0, 2551.18, 3628.35),
        "sra1" => array(0, 0, 1814.17, 2551.18),
        "sra2" => array(0, 0, 1275.59, 1814.17),
        "sra3" => array(0, 0, 907.09, 1275.59),
        "sra4" => array(0, 0, 637.80, 907.09),
        "letter" => array(0, 0, 612.00, 792.00),
        "legal" => array(0, 0, 612.00, 1008.00),
        "ledger" => array(0, 0, 1224.00, 792.00),
        "tabloid" => array(0, 0, 792.00, 1224.00),
        "executive" => array(0, 0, 521.86, 756.00),
        "folio" => array(0, 0, 612.00, 936.00),
        "commercial #10 envelope" => array(0, 0, 684, 297),
        "catalog #10 1/2 envelope" => array(0, 0, 648, 864),
        "8.5x11" => array(0, 0, 612.00, 792.00),
        "8.5x14" => array(0, 0, 612.00, 1008.0),
        "11x17" => array(0, 0, 792.00, 1224.00),
        );*/
        $dompdf->render();
        ob_end_clean();
        $dompdf->stream($data['data'][0]->sales_invoice_number , array(
            'Attachment' => 0 ));
    }
    public function customize_pdf($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $sales_module_id                 = $this->config->item('sales_module');
        $data['module_id']               = $sales_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($sales_module_id , $modules , $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $product_module_id               = $this->config->item('product_module');
        $service_module_id               = $this->config->item('service_module');
        $customer_module_id              = $this->config->item('customer_module');
        $data['charges_sub_module_id']   = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']     = $this->config->item('notes_sub_module');
        $modules_present                 = array(
            'product_module_id'  => $product_module_id ,
            'service_module_id'  => $service_module_id ,
            'customer_module_id' => $customer_module_id );
        $data['other_modules_present']   = $this->other_modules_present($modules_present , $modules['modules']);
        ob_start();
        $html                            = ob_get_clean();
        $html                            = utf8_encode($html);
        $branch_data                     = $this->common->branch_field();
        $data['branch']                  = $this->general_model->getJoinRecords($branch_data['string'] , $branch_data['table'] , $branch_data['where'] , $branch_data['join'] , $branch_data['order']);
        $country_data                    = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']                 = $this->general_model->getRecords($country_data['string'] , $country_data['table'] , $country_data['where']);
        $state_data                      = $this->common->state_field($data['branch'][0]->branch_country_id , $data['branch'][0]->branch_state_id);
        $data['state']                   = $this->general_model->getRecords($state_data['string'] , $state_data['table'] , $state_data['where']);
        $city_data                       = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']                    = $this->general_model->getRecords($city_data['string'] , $city_data['table'] , $city_data['where']);
        $data['currency']                = $this->currency_call();
        $sales_data                      = $this->common->sales_list_field1($id);
        $data['data']                    = $this->general_model->getJoinRecords($sales_data['string'] , $sales_data['table'] , $sales_data['where'] , $sales_data['join']);
        $country_data                    = $this->common->country_field($data['data'][0]->sales_billing_country_id);
        $data['data_country']            = $this->general_model->getRecords($country_data['string'] , $country_data['table'] , $country_data['where']);
        $state_data                      = $this->common->state_field($data['data'][0]->sales_billing_country_id , $data['data'][0]->sales_billing_state_id);
        $data['data_state']              = $this->general_model->getRecords($state_data['string'] , $state_data['table'] , $state_data['where']);
        $inventory_access = $this->general_model->getRecords('inventory_advanced' , 'common_settings' , array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ,
            'delete_status' => 0 ));
        /*if ($inventory_access[0]->inventory_advanced == "yes")
        {
            $product_items       = $this->common->sales_items_product_inventory_list_field($id);
            $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        }
        else
        {
        }*/
        $product_items       = $this->common->sales_items_product_list_field($id);
        $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        $service_items       = $this->common->sales_items_service_list_field($id);
        $sales_service_items = $this->general_model->getJoinRecords($service_items['string'] , $service_items['table'] , $service_items['where'] , $service_items['join']);
        $data['items'] = array_merge($sales_product_items , $sales_service_items);
        $invoice_type  = $this->input->post('pdf_type_check');
        if ($sales_product_items && $sales_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($sales_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($sales_service_items)
        {
            $nature_of_supply = "Service";
        } $data['nature_of_supply'] = $nature_of_supply;
        $igst    = 0;
        $cgst    = 0;
        $sgst    = 0;
        $dpcount = 0;
        $dtcount = 0;
        foreach ($data['items'] as $value)
        {
            $igst = bcadd($igst , $value->sales_item_igst_amount,$section_modules['access_common_settings'][0]->amount_precision);
            $cgst = bcadd($cgst , $value->sales_item_cgst_amount,$section_modules['access_common_settings'][0]->amount_precision);
            $sgst = bcadd($sgst , $value->sales_item_sgst_amount,$section_modules['access_common_settings'][0]->amount_precision);
            if ($value->sales_item_description != "" && $value->sales_item_description != null)
            {
                $dpcount++;
            } if ($value->sales_item_discount_amount != "" && $value->sales_item_discount_amount != null && $value->sales_item_discount_amount != 0)
            {
                $dtcount++;
            }
        } $data['igst_tax']              = $igst;
        $data['cgst_tax']              = $cgst;
        $data['sgst_tax']              = $sgst;
        $data['dpcount']               = $dpcount;
        $data['dtcount']               = $dtcount;
        $note_data                     = $this->template_note($data['data'][0]->note1 , $data['data'][0]->note2);
        $data['note1']                 = $note_data['note1'];
        $data['template1']             = $note_data['template1'];
        $data['note2']                 = $note_data['note2'];
        $data['template2']             = $note_data['template2'];
        $html                          = $this->load->view('sales/custom_pdf' , $data , true);
        include(APPPATH . 'third_party/mpdf60/mpdf.php');
        $pdf                           = new mPDF('utf-8' , array(200 , 130 ));
        $pdf->allow_charset_conversion = true;
        $pdf->charset_in               = 'UTF-8';
        $pdf->AddPage('L' , // L - landscape, P - portrait
                      '' , '' , '' , '' , 10 , // margin_left
                      10 , // margin right
                      10 , // margin top
                      20 , // margin bottom
                      18 , // margin header
                      12); // margin footer
        $pdf->WriteHTML($html);
        $pdf->Output($data['data'][0]->sales_invoice_number . '.pdf' , 'I');
    }
    public function delete()
    {
        $id       = $this->input->post('delete_id');
        $sales_id = $this->encryption_url->decode($id);
        if ($sales_id != "") {
            $sales_module_id        = $this->config->item('sales_module');
            $data['module_id']      = $sales_module_id;
            $modules                = $this->modules;
            $privilege              = "delete_privilege";
            $data['privilege']      = "delete_privilege";
            $section_modules        = $this->get_section_modules($sales_module_id , $modules , $privilege);
            /* presents all the needed */
            $data                   = array_merge($data , $section_modules);
            $access_common_settings = $section_modules['access_common_settings'];
            $sales_voucher_id = $this->general_model->getRecords('sales_voucher_id', 'sales_voucher', array(
            'reference_id'   => $sales_id, 'reference_type' => 'sales'));
            if(!empty($sales_voucher_id))
            $this->general_model->deleteVoucher(array('sales_voucher_id' =>$sales_voucher_id[0]->sales_voucher_id),'sales_voucher','accounts_sales_voucher');
            /*$this->general_model->updateData('sales_voucher' , array(
                'delete_status' => 1 ) , array(
                'reference_id'   => $sales_id ,
                'reference_type' => 'sales' ));*/
            $credit_notes = $this->general_model->getRecords('sales_credit_note_id' , 'sales_credit_note' , array('sales_id'      => $sales_id , 'delete_status' => 0 ));
            $this->general_model->updateData('sales_credit_note' , array(
                'delete_status' => 1 ) , array(
                'sales_id' => $sales_id ));
            foreach ($credit_notes as $key => $value)
            {
                $sales_voucher_id = $this->general_model->getRecords('sales_voucher_id', 'sales_voucher', array(
            'reference_id'   => $value->sales_credit_note_id, 'reference_type' => 'sales_credit_note'));
                if(!empty($sales_voucher_id))
                $this->general_model->deleteVoucher(array('sales_voucher_id' => $sales_voucher_id[0]->sales_voucher_id),'sales_voucher','accounts_sales_voucher');
                /*$this->general_model->updateData('sales_voucher' , array(
                    'delete_status' => 1 ) , array(
                    'reference_id'   => $value->sales_credit_note_id ,
                    'reference_type' => 'sales_credit_note' ));*/
                $sales_credit_note_items = $this->general_model->getRecords('*' , 'sales_credit_note_item' , array(
                    'sales_credit_note_id' => $value->sales_credit_note_id ,
                    'delete_status'        => 0 ));
                $this->general_model->updateData('sales_credit_note_item' , array(
                    'delete_status' => 1 ) , array(
                    'sales_credit_note_id' => $value->sales_credit_note_id ));
                foreach ($sales_credit_note_items as $k => $val)
                {
                    if ($val->item_type == "product" || $val->item_type == "product_inventory")
                    {
                        $product_data     = $this->common->product_field($val->item_id);
                        $product_result   = $this->general_model->getJoinRecords($product_data['string'] , $product_data['table'] , $product_data['where'],$product_data['join']);
                        $product_quantity = ((int)$product_result[0]->product_quantity + (int)$val->sales_credit_note_item_quantity);
                        $data             = array(
                            'product_quantity' => $product_quantity );
                        $where            = array(
                            'product_id' => $val->item_id );
                        $product_table    = $this->config->item('product_table');
                        $this->general_model->updateData($product_table , $data , $where);
                        /*$this->producthook->UpdateProductStock(array('product_id' => $value->item_id,'product_quantity' => $product_quantity));*/
                        //update stock history
                        $where = array(
                            'item_id'        => $val->item_id ,
                            'reference_id'   => $value->sales_credit_note_id ,
                            'reference_type' => 'sales_credit_note' );
                        $history_data = array(
                            'delete_status'   => 1 ,
                            'updated_date'    => date('Y-m-d') ,
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                        $this->db->where($where);
                        $this->db->update('quantity_history' , $history_data);
                    }
                    /*else if ($val->item_type == "product_inventory")
                    {
                        $product_data     = $this->common->product_inventory_field($val->item_id);
                        $product_result   = $this->general_model->getJoinRecords($product_data['string'] , $product_data['table'] , $product_data['where'] , $product_data['join'] , $product_data['order']);
                        $product_quantity = ($product_result[0]->quantity + $val->sales_credit_note_item_quantity);
                        $data             = array(
                            'quantity' => $product_quantity );
                        $where            = array(
                            'product_inventory_varients_id' => $val->item_id );
                        $product_table    = 'product_inventory_varients';
                        $this->general_model->updateData($product_table , $data , $where);
                        //update stock history
                        $where = array(
                            'item_id'        => $val->item_id ,
                            'reference_id'   => $value->sales_credit_note_id ,
                            'reference_type' => 'sales_credit_note' );
                        $history_data = array(
                            'delete_status'   => 1 ,
                            'updated_date'    => date('Y-m-d') ,
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                        $this->db->where($where);
                        $this->db->update('quantity_history' , $history_data);
                    }*/
                }
            }
            $debit_notes = $this->general_model->getRecords('sales_debit_note_id' , 'sales_debit_note' , array(
                'sales_id'      => $sales_id ,
                'delete_status' => 0 ));
            $this->general_model->updateData('sales_debit_note' , array(
                'delete_status' => 1 ) , array(
                'sales_id' => $sales_id ));
            foreach ($debit_notes as $key => $value)
            {
                $sales_voucher_id = $this->general_model->getRecords('sales_voucher_id', 'sales_voucher', array(
            'reference_id'   => $value->sales_debit_note_id , 'reference_type' => 'sales_debit_note'));
        
                $this->general_model->deleteVoucher(array('sales_voucher_id' => $sales_voucher_id[0]->sales_voucher_id),'sales_voucher','accounts_sales_voucher');
                $sales_debit_note_items = $this->general_model->getRecords('*' , 'sales_debit_note_item' , array(
                    'sales_debit_note_id' => $value->sales_debit_note_id ,
                    'delete_status'       => 0 ));
                $this->general_model->updateData('sales_debit_note_item' , array(
                    'delete_status' => 1 ) , array(
                    'sales_debit_note_id' => $value->sales_debit_note_id ));
                foreach ($sales_debit_note_items as $k1 => $val1)
                {
                    if ($val1->item_type == "product" || $val1->item_type == "product_inventory")
                    {
                        $product_data     = $this->common->product_field($val1->item_id);
                        $product_result   = $this->general_model->getJoinRecords($product_data['string'] , $product_data['table'] , $product_data['where'],$product_data['join']);
                        $product_quantity = ((int)$product_result[0]->product_quantity - (int)$val1->sales_debit_note_item_quantity);
                        $data             = array(
                            'product_quantity' => $product_quantity );
                        $where            = array(
                            'product_id' => $val1->item_id );
                        $product_table    = $this->config->item('product_table');
                        $this->general_model->updateData($product_table , $data , $where);
                        /*$this->producthook->UpdateProductStock(array('product_id' => $value->item_id,'product_quantity' => $product_quantity));*/
                        //update stock history
                        $where = array(
                            'item_id'        => $val1->item_id ,
                            'reference_id'   => $value->sales_debit_note_id ,
                            'reference_type' => 'sales_debit_note' );
                        $history_data = array(
                            'delete_status'   => 1 ,
                            'updated_date'    => date('Y-m-d') ,
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                        $this->db->where($where);
                        $this->db->update('quantity_history' , $history_data);
                    }
                }
            }
            $this->general_model->updateData('receipt_voucher' , array(
                'delete_status' => 1 ) , array(
                'reference_id'   => $sales_id ,
                'reference_type' => 'sales' ));
            $where            = "(reference_id like '%," . $sales_id . "%' or reference_id like '%" . $sales_id . ",%')  and reference_type='sales' and delete_status=0";
            $receipt_vouchers = $this->general_model->getRecords('*' , 'receipt_voucher' , $where);
            /* foreach starts */
            foreach ($receipt_vouchers as $key => $value)
            {
                $old_reference_id = explode(',' , $value->reference_id);
                $i                = 0;
                $flag             = 0;
                $new_reference_id = '';
                $flag_key         = "";
                foreach ($old_reference_id as $k => $val)
                {
                    if ($val == $sales_id)
                    {
                        $flag_key = $k;
                        $flag     = 1;
                    }
                    else
                    {
                        if ($new_reference_id == "")
                        {
                            $new_reference_id = $val;
                        }
                        else
                        {
                            $new_reference_id .= ',' . $val;
                        }
                    }
                }
                if ($flag == 1) {
                    $new_reference_number         = '';
                    $new_receipt_amount           = '';
                    $new_converted_receipt_amount = '';
                    $new_invoice_total            = '';
                    $new_invoice_paid_amount      = '';
                    $new_invoice_balance_amount   = '';
                    $old_reference_number         = explode(',' , $value->reference_number);
                    $old_receipt_amount           = explode(',' , $value->imploded_receipt_amount);
                    $old_converted_receipt_amount = explode(',' , $value->imploded_converted_receipt_amount);
                    $old_invoice_total            = explode(',' , $value->invoice_total);
                    $old_invoice_paid_amount      = explode(',' , $value->invoice_paid_amount);
                    $old_invoice_balance_amount   = explode(',' , $value->invoice_balance_amount);
                    foreach ($old_reference_number as $k => $val)
                    {
                        if ($k != $flag_key)
                        {
                            if ($new_reference_number == "")
                            {
                                $new_reference_number = $val;
                            }
                            else
                            {
                                $new_reference_number .= ',' . $val;
                            }
                        }
                    }
                    $receipt_amount = "";
                    foreach ($old_receipt_amount as $k => $val)
                    {
                        if ($k != $flag_key)
                        {
                            if ($new_receipt_amount == "")
                            {
                                $new_receipt_amount = $val;
                            }
                            else
                            {
                                $new_receipt_amount .= ',' . $val;
                            }
                        }
                        else
                        {
                            $receipt_amount = $val;
                        }
                    }
                    $converted_receipt_amount = "";
                    foreach ($old_converted_receipt_amount as $k => $val)
                    {
                        if ($k != $flag_key)
                        {
                            if ($new_converted_receipt_amount == "")
                            {
                                $new_converted_receipt_amount = $val;
                            }
                            else
                            {
                                $new_converted_receipt_amount .= ',' . $val;
                            }
                        }
                        else
                        {
                            $converted_receipt_amount = $val;
                        }
                    }
                    foreach ($old_invoice_total as $k => $val)
                    {
                        if ($k != $flag_key)
                        {
                            if ($new_invoice_total == "")
                            {
                                $new_invoice_total = $val;
                            }
                            else
                            {
                                $new_invoice_total .= ',' . $val;
                            }
                        }
                    }
                    foreach ($old_invoice_paid_amount as $k => $val)
                    {
                        if ($k != $flag_key)
                        {
                            if ($new_invoice_paid_amount == "")
                            {
                                $new_invoice_paid_amount = $val;
                            }
                            else
                            {
                                $new_invoice_paid_amount .= ',' . $val;
                            }
                        }
                    }
                    foreach ($old_invoice_balance_amount as $k => $val)
                    {
                        if ($k != $flag_key)
                        {
                            if ($new_invoice_balance_amount == "")
                            {
                                $new_invoice_balance_amount = $val;
                            }
                            else
                            {
                                $new_invoice_balance_amount .= ',' . $val;
                            }
                        }
                    }
                    $receipt_grand_total           = $this->precise_amount(bcsub($value->receipt_amount , $receipt_amount,$section_modules['access_common_settings'][0]->amount_precision) , $access_common_settings[0]->amount_precision);
                    $converted_receipt_grand_total = $this->precise_amount(bcsub($value->converted_receipt_amount , $converted_receipt_amount,$section_modules['access_common_settings'][0]->amount_precision) , $access_common_settings[0]->amount_precision);
                    $receipt_voucher_data = array(
                        'reference_id'                      => $new_reference_id ,
                        'reference_number'                  => $new_reference_number ,
                        'receipt_amount'                    => $receipt_grand_total ,
                        'converted_receipt_amount'          => $converted_receipt_grand_total ,
                        'imploded_receipt_amount'           => $new_receipt_amount ,
                        'imploded_converted_receipt_amount' => $new_converted_receipt_amount ,
                        'invoice_total'                     => $new_invoice_total ,
                        'invoice_paid_amount'               => $new_invoice_paid_amount ,
                        'invoice_balance_amount'            => $new_invoice_balance_amount
                    );
                    $this->general_model->updateData('receipt_voucher' , $receipt_voucher_data , array(
                        'receipt_id' => $value->receipt_id ));
                    $accounts_receipt = $this->general_model->getRecords('*' , 'accounts_receipt_voucher' , array(
                        'receipt_voucher_id' => $value->receipt_id ,
                        'delete_status'      => 0 ));
                    $data1            = array(
                        'voucher_amount'           => $receipt_grand_total ,
                        'converted_voucher_amount' => $converted_receipt_grand_total ,
                        'dr_amount'                => $receipt_grand_total ,
                        'cr_amount'                => 0 );
                    $data2            = array(
                        'voucher_amount'           => $receipt_grand_total ,
                        'converted_voucher_amount' => $converted_receipt_grand_total ,
                        'dr_amount'                => 0 ,
                        'cr_amount'                => $receipt_grand_total );
                    $this->general_model->updateData('accounts_receipt_voucher' , $data1 , array(
                        'accounts_receipt_id' => $accounts_receipt[0]->accounts_receipt_id ));
                    $this->general_model->updateData('accounts_receipt_voucher' , $data2 , array(
                        'accounts_receipt_id' => $accounts_receipt[1]->accounts_receipt_id ));
                }
            }
            /* foreach ends */
            $advance_vouchers = $this->general_model->getRecords('advance_voucher_id,receipt_amount,converted_receipt_amount' , 'advance_voucher' , array(
                'reference_id'   => $sales_id ,
                'reference_type' => 'sales' ,
                'delete_status'  => 0 ,
            ));
            foreach ($advance_vouchers as $key => $value){
                $updated_advance_data = array(
                    'reference_id'   => '' ,
                    'reference_type' => '' ,
                );
                $advance_where        = array(
                    'advance_voucher_id'     => $value->advance_id ,
                    'reference_id'   => $sales_id ,
                    'reference_type' => 'sales' ,
                    'delete_status'  => 0 ,
                );
                $this->general_model->updateData('advance_voucher' , $updated_advance_data , $advance_where);
                $sales_datas = $this->general_model->getRecords('sales_id,sales_paid_amount,converted_paid_amount' , 'sales' , array(
                    'sales_id' => $sales_id
                ));
                foreach ($sales_datas as $key4 => $value4)
                {
                    $paid_amount           = bcsub($value4->paid_amount , $value->receipt_amount,$section_modules['access_common_settings'][0]->amount_precision);
                    $converted_paid_amount = bcsub($value4->converted_paid_amount , $value->converted_receipt_amount,$section_modules['access_common_settings'][0]->amount_precision);
                    $updated_sales_data = array(
                        'paid_amount'           => $this->precise_amount($paid_amount , $access_common_settings[0]->amount_precision) ,
                        'converted_paid_amount' => $this->precise_amount($converted_paid_amount , $access_common_settings[0]->amount_precision) ,
                    );
                    $sales_where        = array(
                        'sales_id' => $value4->sales_id ,
                    );
                    $this->general_model->updateData('sales' , $updated_sales_data , $sales_where);
                }
            }
            $sales_items = $this->general_model->getRecords('*' , 'sales_item' , array(
                'sales_id' => $sales_id ));
            $this->general_model->updateData('sales' , array(
                'delete_status' => 1 ) , array(
                'sales_id' => $sales_id ));
            $this->general_model->updateData('quotation' , array(
                'sales_id' => 0 ) , array(
                'sales_id'      => $sales_id ,
                'delete_status' => 0 ));
            foreach ($sales_items as $key => $value)
            {
                if ($value->item_type == "product" || $value->item_type == "product_inventory")
                {
                    $product_string = '*';
                    $product_table  = 'products';
                    $product_where  = array(
                        'product_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string , $product_table , $product_where, $order          = "");
                    $product_qty    = bcadd($product[0]->product_quantity , $value->sales_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
                    $product_data   = array(
                        'product_quantity' => $product_qty );
                    $this->general_model->updateData($product_table , $product_data , $product_where);
                    /*$this->producthook->UpdateProductStock(array('product_id' => $value->item_id,'product_quantity' => $product_qty));*/
                    //update stock history
                    $where = array(
                        'item_id'        => $value->item_id ,
                        'reference_id'   => $sales_id ,
                        'reference_type' => 'sales' );
                    $history_data = array(
                        'delete_status'   => 1 ,
                        'updated_date'    => date('Y-m-d') ,
                        'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                    $this->db->where($where);
                    $this->db->update('quantity_history' , $history_data);
                }
                /*else if ($value->item_type == "product_inventory")
                {
                    $product_string = '*';
                    $product_table  = 'product_inventory_varients';
                    $product_where  = array(
                        'product_inventory_varients_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string , $product_table , $product_where , $order          = "");
                    $product_qty    = bcadd($product[0]->quantity , $value->sales_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
                    $product_data   = array(
                        'quantity' => $product_qty );
                    $this->general_model->updateData($product_table , $product_data , $product_where);
                    //update stock history
                    $where = array(
                        'item_id'        => $value->item_id ,
                        'reference_id'   => $sales_id ,
                        'reference_type' => 'sales' );
                    $history_data = array(
                        'delete_status'   => 1 ,
                        'updated_date'    => date('Y-m-d') ,
                        'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                    $this->db->where($where);
                    $this->db->update('quantity_history' , $history_data);
                }*/
            }
            $successMsg = 'Sales Deleted Successfully';
            $this->session->set_flashdata('sales_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $sales_id ,
                'table_name'        => 'sales' ,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'Sales Deleted' );
            $this->general_model->insertData('log' , $log_data);
            $redirect = 'sales';
            if($this->input->post('delete_redirect') != '') $redirect = $this->input->post('delete_redirect');
            redirect($redirect , 'refresh');
            
        }
        else
        {
            $errorMsg = 'Sales Delete Unsuccessful';
            $this->session->set_flashdata('sales_error',$errorMsg);
            redirect('sales' , 'refresh');
        }
    }
    public function email($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $sales_module_id                 = $this->config->item('sales_module');
        $data['module_id']               = $sales_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($sales_module_id , $modules , $privilege);
       
        $data['access_modules']          = $section_modules['active_modules'];
        $data['access_sub_modules']      = $section_modules['access_sub_modules'];
        /*$data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];*/
        $data['access_settings']         = $section_modules['access_settings'];
        $data['access_common_settings']  = $section_modules['access_common_settings'];
        $email_sub_module_id         = $this->config->item('email_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }
        }
        $email_sub_module = 0;
        foreach ($data['access_sub_modules'] as $key => $value) {
            if ($email_sub_module_id == $value)
            {
                $email_sub_module = 1;
            }
        } 
        if ($email_sub_module == 1){
            ob_start();
            $html                 = ob_get_clean();
            $html                 = utf8_encode($html);
            $data = $this->getSalesDetails($id,$this->input->post());
            $currency = $this->getBranchCurrencyCode();
            $data['data'][0]->currency_code = $currency[0]->currency_code;
            $data['data'][0]->currency_symbol = $currency[0]->currency_symbol;
            $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
            // $html                           = $this->load->view('sales/pdf', $data, true);
            $pdf                  = $this->general_model->getRecords('settings.*' , 'settings' , [
                'module_id' => 2 ,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);
            $pdf_json            = $pdf[0]->pdf_settings;
            $rep                 = str_replace("\\" , '' , $pdf_json);
            $data['pdf_results'] = json_decode($rep , true);
            $data['is_utgst'] = $this->general_model->checkIsUtgst($data['data'][0]->sales_billing_state_id);
            /*$html                 = $this->load->view('sales/pdf_view3' , $data , true);*/
            $html                 = $this->load->view('sales/pdf' , $data , true);
        
            include APPPATH . "third_party/dompdf/autoload.inc.php";
            //and now im creating new instance dompdf
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->sales_invoice_number);
            $dompdf = new Dompdf\Dompdf();
            
            $paper_size  = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';
            $sales_data                     = $this->common->sales_list_field1($id);
            $data['data']                   = $this->general_model->getJoinRecords($sales_data['string'] , $sales_data['table'] , $sales_data['where'] , $sales_data['join']);
            $branch_data                    = $this->common->branch_field();
            $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'] , $branch_data['table'] , $branch_data['where'] , $branch_data['join'] , $branch_data['order']);
            $data['email_setup']            = $this->general_model->getRecords('*' , 'email_setup' , array(
                'delete_status' => 0 ,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ,
                'added_user_id' => $this->session->userdata('SESS_USER_ID') ));
            $data['email_template']         = $this->general_model->getRecords('*' , 'email_template' , array(
                'module_id'     => $sales_module_id ,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ,
                'delete_status' => 0 ));
            $this->load->view('sales/email' , $data);
        } else {
            $this->load->view('sales' , $data);
        }
    }
    public function convert_currency(){
        $id                 = $this->input->post('convert_currency_id');
        $id                 = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');
        $converted_date = date('Y-m-d', strtotime($this->input->post('conversion_date')));
        $data               = array(
            'currency_converted_rate' => $new_converted_rate ,
            'converted_grand_total'   => $this->input->post('converted_grand_total'),
            'currency_converted_date' => $converted_date );
        $this->general_model->updateData('sales' , $data , array(
            'sales_id' => $id ));
        //update converted voucher amount in account sales voucher table
        $sales_voucher_data = array(
            'converted_receipt_amount' => $this->input->post('converted_grand_total'));
        $this->general_model->updateData('sales_voucher' , $sales_voucher_data , array(
            'reference_id'   => $id ,
            'delete_status'  => 0 ,
            'reference_type' => 'sales' ));
        $sales_voucher = $this->general_model->getRecords('sales_voucher_id' , 'sales_voucher' , array(
            'reference_id'   => $id ,
            'delete_status'  => 0 ,
            'reference_type' => 'sales' ));
        $accounts_sales_voucher = $this->general_model->getRecords('*' , 'accounts_sales_voucher' , array(
            'sales_voucher_id' => $sales_voucher[0]->sales_voucher_id ,
            'delete_status'    => 0 ));
        foreach ($accounts_sales_voucher as $key1 => $value1){
            $new_converted_voucher_amount = bcmul($accounts_sales_voucher[$key1]->voucher_amount , $new_converted_rate);
            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount );
            $where                    = array(
                'accounts_sales_id' => $accounts_sales_voucher[$key1]->accounts_sales_id );
            $voucher_table            = "accounts_sales_voucher";
            $this->general_model->updateData($voucher_table , $converted_voucher_amount , $where);
        }
        redirect('sales' , 'refresh');
    }
    public function email_popup($id){
        $id                              = $this->encryption_url->decode($id);
        $sales_module_id                 = $this->config->item('sales_module');
         $sales_module_id                 = $this->config->item('sales_module');
        $data['module_id']               = $sales_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($sales_module_id , $modules , $privilege);
       
        $data['access_modules']          = $section_modules['active_modules'];
        $data['access_sub_modules']      = $section_modules['access_sub_modules'];
        /*$data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];*/
        $data['access_settings']         = $section_modules['access_settings'];
        $data['access_common_settings']  = $section_modules['access_common_settings'];
        $email_sub_module_id         = $this->config->item('email_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }
        }
        $email_sub_module = 0;
        foreach ($data['access_sub_modules'] as $key => $value) {
            if ($email_sub_module_id == $value)
            {
                $email_sub_module = 1;
            }
        } 
        if($email_sub_module == 1){
            ob_start();
            $html                 = ob_get_clean();
            $html                 = utf8_encode($html);
            $data = $this->getSalesDetails($id,$this->input->post());
            $currency = $this->getBranchCurrencyCode();
            $data['data'][0]->currency_code = $currency[0]->currency_code;
            $data['data'][0]->currency_symbol = $currency[0]->currency_symbol;
            $data['data'][0]->currency_symbol_pdf = $currency[0]->currency_symbol_pdf;
            $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
            // $html                           = $this->load->view('sales/pdf', $data, true);
            $pdf                  = $this->general_model->getRecords('settings.*' , 'settings' , [
                'module_id' => 2 ,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);
            $pdf_json            = $pdf[0]->pdf_settings;
            $rep                 = str_replace("\\" , '' , $pdf_json);
            $data['pdf_results'] = json_decode($rep , true);
            /*$html                 = $this->load->view('sales/pdf_view3' , $data , true);*/
            if($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath')){
                $html = $this->load->view('sales/pdf_hsn' , $data , true);
            }else{
                $html = $this->load->view('sales/pdf' , $data , true);
            }
        
            include APPPATH . "third_party/dompdf/autoload.inc.php";
            //and now im creating new instance dompdf
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->sales_invoice_number);
            $file_name = str_replace('/','_',$file_name);
            $dompdf = new Dompdf\Dompdf();
            
            $paper_size  = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';
            $sales_data                     = $this->common->sales_list_field1($id);
            $data['data']                   = $this->general_model->getJoinRecords($sales_data['string'] , $sales_data['table'] , $sales_data['where'] , $sales_data['join']);
            $branch_data                    = $this->common->branch_field();
            $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'] , $branch_data['table'] , $branch_data['where'] , $branch_data['join'] , $branch_data['order']);
            $data['email_setup']            = $this->general_model->getRecords('*' , 'email_setup' , array(
                'delete_status' => 0 ,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ,
                'added_user_id' => $this->session->userdata('SESS_USER_ID') ));
            $data['email_template']   = $this->general_model->getRecords('*' , 'email_template' , array(
                'module_id'     => $sales_module_id ,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ,
                'delete_status' => 0 ));
            //$this->load->view('sales/email' , $data);
            $data['data'][0]->pdf_file_path = $data['pdf_file_path'];
            $data['data'][0]->pdf_file_name = $data['pdf_file_name'];
            $data['data'][0]->email_template = $data['email_template'];
            $data['data'][0]->firm_name = $data['branch'][0]->firm_name;
            $result = json_encode($data['data']);
            echo $result;
        }
        
    }

    public function get_brand_invoice_number(){
        $id                = $this->input->post('brand_id');
        $brand_access      = $this->general_model->getRecords('*' , 'brand' , array(
                        'delete_status' => 0 ,
                        'brand_id' => $id ,
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ));
        
        $data              = $this->get_default_country_state();
        $sales_module_id   = $this->config->item('brand_module');
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data              = array_merge($data , $section_modules);
        $access_settings        = $data['access_settings'];
       
        $primary_id             = "brand_id";
        $table_name             = 'sales';
        $date_field_name        = "sales_date";
        $current_date           = date('Y-m-d');
        $access_settings[0]->settings_invoice_first_prefix = $brand_access[0]->brand_invoice_first_prefix;
        $access_settings[0]->settings_invoice_last_prefix = $brand_access[0]->brand_invoice_last_prefix;
        $access_settings[0]->invoice_type = $brand_access[0]->invoice_type;
        $access_settings[0]->invoice_creation = $brand_access[0]->invoice_creation;
        /*$access_settings[0]->invoice_seperation = $brand_access[0]->invoice_seperation;*/
        $access_settings[0]->invoice_seperation = '';
        
        $brand_invoice_number = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date,'',$id);
        $json = array();
        $json['brand_invoice_number'] = $brand_invoice_number;
        $json['readonly'] = $brand_access[0]->invoice_readonly;

        echo json_encode($json);
        exit;
    }

    public function brand_sales(){
        $sales_module_id        = $this->config->item('brand_module');
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;
        $section_modules        = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
                  
         if (!empty($this->input->post())) {
            $columns = array(
                0 => 'brand_name',
                1 => 'invoice_number',
                2 => 'customer_name',
                3 => 'place_of_supply',
                4 => 'scheme_discount',
                5 => 'trade_discount_amount',
                6 => 'taxable_amount',
                7 => 'gst_percentage',
                8 => 'gst_amount',
                7 => 'tds_percentage',
                8 => 'tds_amount',
                9 => 'invoice_amount',
                10 => 'sales_date',
                11 => 'cess_percentage',
                12 => 'cess_amount',
            );


            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_invoice_brand_report();
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
                    $igst = ($post->sales_igst_amount) ? $post->sales_igst_amount : 0;
                    $sgst = ($post->sales_sgst_amount) ? $post->sales_sgst_amount : 0;
                    $cgst = ($post->sales_cgst_amount) ? $post->sales_cgst_amount : 0;

                    $gst_amount = $igst + $cgst + $sgst;

                    $nestedData['brand_name'] = $post->brand_name;
                    $nestedData['invoice_number'] = $post->sales_invoice_number ;
                    $nestedData['customer_name'] = $post->customer_name;
                    $nestedData['place_of_supply'] = $post->place_of_supply;
                    $nestedData['discount_amount'] = $this->precise_amount($post->sales_discount_amount,$access_common_settings[0]->amount_precision);
                    $nestedData['cash_discount_amount'] = $this->precise_amount($post->sales_cash_discount,$access_common_settings[0]->amount_precision);
                    $nestedData['gst_amount'] = $this->precise_amount($gst_amount,$access_common_settings[0]->amount_precision);                    
                    $nestedData['tds_amount'] = $this->precise_amount($post->sales_tcs_amount,$access_common_settings[0]->amount_precision);
                    $nestedData['sales_date'] = date('d-m-Y', strtotime($post->sales_date));
                    $nestedData['cess_amount'] = $this->precise_amount($post->sales_tax_cess_amount,$access_common_settings[0]->amount_precision);
                    $nestedData['invoice_amount'] = $this->precise_amount($post->sales_grand_total,$access_common_settings[0]->amount_precision);
                    $nestedData['taxable_amount'] = $this->precise_amount($post->sales_taxable_value,$access_common_settings[0]->amount_precision);
                  
                    $send_data[] = $nestedData;
                    
                }
            }

            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
           
        } else {
            $this->load->view('sales/brand_invoice', $data);
        }
    }
    public function get_billing_popup (){
        $party_id = $this->input->post('party_id');
        $party_type =  'customer';
        if($this->input->post('party_type') != '') $party_type = $this->input->post('party_type');
        
        $country  = $this->general_model->getRecords('*', 'countries', array('country_name' => 'india' ));
        $country_id = $country[0]->country_id;
        $list_data  = $this->common->billing_address_list_popup($party_id,$party_type);
        $shipping_address_data = $this->general_model->getPageJoinRecords($list_data);
        $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
       $send = array();
       if(!empty($shipping_address_data)){
       foreach ($shipping_address_data as $com) {
            $id = $com->shipping_address_id;
            $state_id = $com->state_id;
            $country_id_shipping = $com->country_id;
            if($country_id_shipping != $country_id){
                $type = 'other';
            }else{
                $type = 'same';
            }
            $primary_address = $com->primary_address;
            $nestedData['shipping_code'] = $com->shipping_code;
            $nestedData['shipping_address'] = $com->shipping_address;
            $nestedData['contact_person'] = $com->contact_person;
            $nestedData['gst'] = $com->shipping_gstin;
            $nestedData['state'] = $com->state_name;
            if($primary_address == 'yes'){
                $nestedData['action'] = '<input type="radio" name="apply_billing" value="'. $id.'" id="apply_billing_'. $id.'" checked/><input type="hidden" name="apply_country_id" value="'. $country_id_shipping.'" id="apply_country_id_'. $id.'" checked/><input type="hidden" name="state_id_suppuly" id="state_id_suppuly_'.$id.'" value="'.$state_id.'"><input type="hidden" name="country_type" id="country_type_'.$id.'" value="'.$type.'">';
            }else{
            $nestedData['action'] = '<input type="radio" name="apply_billing" value="'. $id.'" id="apply_billing_'. $id.'"/><input type="hidden" name="apply_country_id" value="'. $country_id_shipping.'" id="apply_country_id_'. $id.'" /><input type="hidden" name="state_id_suppuly" id="state_id_suppuly_'.$id.'" value="'.$state_id.'"><input type="hidden" name="country_type" id="country_type_'.$id.'" value="'.$type.'">';
            }
            $send[] = $nestedData;
       }
       $totalData = $totalData;
       }else{
        $totalData = 0;
       }
       
       
       $totalFiltered = 10;
       $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "shipping_address_id" => $id,
                "data"            => $send);
        echo json_encode($json_data);
    }
}