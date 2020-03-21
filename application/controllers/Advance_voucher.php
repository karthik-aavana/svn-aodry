<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advance_voucher extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
                'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }
    function index() {
        $advance_voucher_module_id  = $this->config->item('advance_voucher_module');
        $data['advance_voucher_module_id']  = $advance_voucher_module_id;
        $modules = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $email_sub_module_id             = $this->config->item('email_sub_module');
        if (!empty($this->input->post())) {
            $columns             = array(
                    0 => 'av.advance_voucher_id',
                    1 => 'av.voucher_date',
                    2 => 'av.advance_voucher_id',
                    3 => 'c.customer_name',
                    4 => 'av.receipt_amount',
                    5 => 'state_name',
                    6 => 'unadjusted_amount',);
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->advance_voucher_list_field($order,$dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])) {
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
            } $send_data = array();
            if (!empty($posts)) {

                foreach ($posts as $post) {
                    $unadjusted_amount = $post->receipt_amount - $post->adjusted_amount;
                    $advance_id = $this->encryption_url->encode($post->advance_voucher_id);
                    $nestedData['voucher_date'] = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] =  ' <a href="' . base_url('advance_voucher/view/') . $advance_id . '">' . $post->voucher_number . '</a>';
                    $nestedData['customer']  = $post->customer_name;
                    $nestedData['reference_number'] = $post->reference_number;
                    $nestedData['amount'] = $post->currency_symbol . ' ' . $this->precise_amount($post->receipt_amount, 2);
                    if ($post->billing_state_id == 0) {
                        $nestedData['place_of_supply'] = "Out of Country";
                    } else {
                    $nestedData['place_of_supply'] = $post->state_name;
                    }
                    $nestedData['unadjusted_amount'] = $this->precise_amount($unadjusted_amount, 2);
                    //$nestedData['added_user']                = $post->first_name . ' ' . $post->last_name;
                  //  $cols = '<ul class="list-inline">        <li>        <a href="' . base_url('advance_voucher/view/') . $advance_id . '"><i class="fa fa-eye text-orange"></i> Advance Details</a>        </li>';
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                     $cols .= '<span> <a href="' . base_url('advance_voucher/view/') . $advance_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" data-original-title="View Advance Voucher"> <i class="fa fa-eye"></i> </a></span>';
                    if ($unadjusted_amount > 0) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#connectSales"> <a href="#" class="connectSales btn btn-app" data-toggle="tooltip" data-id="' . $advance_id . '"data-placement="bottom" title="Connect to Sales"> <i class="fa fa-link"></i> </a></span>';
                    }
                    if (in_array($advance_voucher_module_id, $data['active_edit']) && $post->is_from_sales == 0) {
                        if ($post->reference_id == 0 || $post->reference_id == "" || $post->reference_id == null) {
                            if ($unadjusted_amount > 0) {

                           $cols .= '<span> <a href="' . base_url('advance_voucher/edit/') . $advance_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Advance Voucher"> <i class="fa fa-pencil"></i> </a></span>';
                           }
                        }
                    }
                     $customer_currency_code = $this->getCurrencyInfo($post->currency_id);
                        $customer_curr_code = '';
                        $currency = $this->getBranchCurrencyCode();
                        
                    $branc_currency_code = $currency[0]->currency_code;
                        if(!empty($customer_currency_code))
                        $customer_curr_code     = $customer_currency_code[0]->currency_code;

                    if($branc_currency_code != $customer_curr_code){
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#pdf_type_modal"><a href1="' . base_url('advance_voucher/pdf/') . $advance_id . '" class="btn btn-app pdf_button" b_curr="'.$this->session->userdata('SESS_DEFAULT_CURRENCY').'"  b_code="'.$branc_currency_code.'" c_code="'.$customer_curr_code.'" c_curr="'.$post->currency_id.'" data-id="' . $advance_id . '" data-name="regular" target="_blank" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }else{
                        $cols .= '<span data-backdrop="static" data-keyboard="false" ><a href="' . base_url('advance_voucher/pdf/') . $advance_id . '" target="_blank" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }
                    

                    
                    $email_sub_module = 0;
                   /* if ($data['access_module_privilege']->add_privilege == "yes")
                    {
                        foreach ($data['access_sub_modules'] as $key => $value)
                        {
                            if ($email_sub_module_id == $value->sub_module_id)
                            {
                                $email_sub_module = 1;
                            }
                        }
                      } */
                    if (in_array($email_sub_module_id, $data['active_view'])) {

                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail"><a href="#" data-toggle="tooltip"  class="btn btn-app composeMail" data-id="' . $advance_id . '" data-name="regular"  data-placement="bottom" title="Email Advance Voucher"><i class="fa fa-envelope-o"></i></a></span>';

                    }

                $cols .= '<span> <a href="' . base_url('advance_voucher/view_details/') . $advance_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" data-original-title="View Ledger Details" target="_blank"> <i class="fa fa-eye"></i> </a></span>';

                  /*  if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY') && $post->voucher_status != "2"){
                        $cols .= '<li>               <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $advance_id . '" data-path="advance_voucher/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->receipt_amount . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>            </li>';
                    }
                    */
                    if (in_array($advance_voucher_module_id, $data['active_delete'])) {

                         // $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $advance_id . '" data-path="advance_voucher/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-placement="bottom" data-toggle="tooltip" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';

                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $advance_id . '" data-path="advance_voucher/delete" data-toggle="tooltip" data-placement="bottom" title="Delete Advance Voucher"> <i class="fa fa-trash-o"></i> </a></span>';

                        if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                            $conversion_date = $post->conversion_date;
                        if($conversion_date == '0000-00-00') $conversion_date = $post->added_date;
                        $conversion_date = date('d-m-Y',strtotime($conversion_date));
                        $cols .= '<span data-toggle="tooltip" data-placement="bottom" title="Convert Currency"><a href="#" class="btn btn-app convert_currency" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#convert_currency_modal" data-id="' . $advance_id . '" data-conversion_date="'.$conversion_date.'" data-path="advance_voucher/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $this->precise_amount($post->receipt_amount, $access_common_settings[0]->amount_precision) . '" data-rate="' . $this->precise_amount($post->currency_converted_rate, $access_common_settings[0]->amount_precision) . '" >
                                    <i class="fa fa-exchange"></i>
                            </a> </span>';
                    }
                    }
                    $cols .= '</div>';

                    $cols .= '</div></div>';

                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';


                    $send_data[]          = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view('advance_voucher/list', $data);
        }
    }

    public function add() {
        $data = $this->get_default_country_state();
        $advance_voucher_module_id         = $this->config->item('advance_voucher_module');
        $data['module_id']                 = $advance_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "add_privilege";
        $data['privilege']                 = "add_privilege";
        $section_modules                   = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);

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

        $product_module_id  = $data['product_module_id'] = $this->config->item('product_module');
        $service_module_id  = $data['service_module_id'] = $this->config->item('service_module');
        $customer_module_id = $data['customer_module_id'] = $this->config->item('customer_module');
        $category_module_id = $data['category_module_id'] = $this->config->item('category_module');
        $subcategory_module_id = $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $tax_module_id  = $data['tax_module_id'] = $this->config->item('tax_module');
        $discount_module_id = $data['discount_module_id'] = $this->config->item('discount_module');

        $accounts_module_id = $data['accounts_module_id'] = $this->config->item('accounts_module');
        $bank_account_module_id  = $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $modules_present                   = array(
                'product_module_id'      => $product_module_id,
                'service_module_id'      => $service_module_id,
                'customer_module_id'     => $customer_module_id,
                'category_module_id'     => $category_module_id,
                'subcategory_module_id'  => $subcategory_module_id,
                'tax_module_id'          => $tax_module_id,
                'discount_module_id'     => $discount_module_id,
                'accounts_module_id'     => $accounts_module_id,
            'bank_account_module_id' => $bank_account_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);

        $data['customer']         = $this->customer_call();
        $data['currency']         = $this->currency_call();
        $data['product_category'] = $this->product_category_call();
        $data['service_category'] = $this->service_category_call();
        $data['bank_account']     = $this->bank_account_call_new();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_call();
        $data['sac']              = $this->sac_call();
        $data['chapter']          = $this->chapter_call();
        $data['hsn']              = $this->hsn_call();
        $access_settings          = $data['access_settings'];
        $primary_id               = "advance_voucher_id";
        $table_name               = $this->config->item('advance_voucher_table');
        $date_field_name          = "voucher_date";
        $current_date             = date('Y-m-d');
        $data['voucher_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['inventory_access'] = $data['access_common_settings'][0]->inventory_advanced;
        $data['access_settings'][0]->discount_visible = 'no';
        $this->load->view('advance_voucher/add', $data);
    }

    public function AdvanceFromSales() {
        $customer_id = $this->input->post('customer_id');
        $sales_id = $this->encryption_url->decode($this->input->post('sales_id'));
        $voucher_date = date('Y-m-d', strtotime($this->input->post('voucher_date')));
        $advance_ledger = $this->config->item('advance_ledger');

        $voucher_amount = $this->input->post('voucher_amount');
        $customer = $this->general_model->getRecords('customer_name,ledger_id,customer_country_id,customer_state_id', 'customer', array('customer_id' => $customer_id));

        $sales_data = $this->general_model->getRecords('sales_invoice_number', 'sales', array('sales_id' => $sales_id));

        $customer_name = $customer[0]->customer_name;
        $customer_ledger_id = $customer[0]->ledger_id;
        if (!$customer_ledger_id) {
            $default_customer_id = $advance_ledger['CUSTOMER'];
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

        $excess_id = $this->input->post('excess_id');
        $voucher_number = $this->input->post('voucher_number');
        $voucher_number .= '-EX';
        $currency  = $this->session->userdata('SESS_DEFAULT_CURRENCY');

        if ($this->input->post('voucher_type') == 'advance') {
            $reference_type = 'advance_voucher';

            $advance_voucher_module_id         = $this->config->item('advance_voucher_module');
            $data['module_id']                 = $advance_voucher_module_id;
            $modules                           = $this->modules;
            $privilege                         = "add_privilege";
            $data['privilege']                 = "add_privilege";
            $section_modules                   = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
            $data = array_merge($data, $section_modules);
            $product_module_id  = $data['product_module_id'] = $this->config->item('product_module');
            $service_module_id  = $data['service_module_id'] = $this->config->item('service_module');
            $customer_module_id = $data['customer_module_id'] = $this->config->item('customer_module');
            $category_module_id = $data['category_module_id'] = $this->config->item('category_module');
            $subcategory_module_id = $data['subcategory_module_id'] = $this->config->item('subcategory_module');
            $tax_module_id  = $data['tax_module_id'] = $this->config->item('tax_module');
            $discount_module_id = $data['discount_module_id'] = $this->config->item('discount_module');

            $accounts_module_id = $data['accounts_module_id'] = $this->config->item('accounts_module');
            $bank_account_module_id  = $data['bank_account_module_id'] = $this->config->item('bank_account_module');
            $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
            $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
            $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
            $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
            $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
            $modules_present                   = array(
                    'product_module_id'      => $product_module_id,
                    'service_module_id'      => $service_module_id,
                    'customer_module_id'     => $customer_module_id,
                    'category_module_id'     => $category_module_id,
                    'subcategory_module_id'  => $subcategory_module_id,
                    'tax_module_id'          => $tax_module_id,
                    'discount_module_id'     => $discount_module_id,
                    'accounts_module_id'     => $accounts_module_id,
                'bank_account_module_id' => $bank_account_module_id);

            $data['other_modules_present']     = $this->other_modules_present($modules_present, $modules['modules']);

            $access_settings          = $data['access_settings'];

            $advance_data = array(
                    "voucher_date"        => date('Y-m-d', strtotime($this->input->post('voucher_date'))),
                    "voucher_number"      => $voucher_number,
                    "voucher_sub_total"   => $voucher_amount,
                    "receipt_amount"      => $voucher_amount,
                    "is_from_sales"       => '1',
                    "voucher_tax_amount"  => 0,
                    "description"         => '',
                    "reference_id"        => '',
                    "reference_number"    => "",
                    "from_account"        => '',
                    "to_account"          => 'customer-' . $customer[0]->customer_name,
                    "payment_mode"        => '',
                    "bank_name"           => '',
                    "cheque_date" => '',
                    "cheque_number"       => '',
                    "payment_via"         => '',
                    "ref_number"          => '',
                    "voucher_igst_amount" => 0,
                    "voucher_cgst_amount" => 0,
                    "voucher_sgst_amount" => 0,
                    "voucher_cess_amount" => 0,
                    "financial_year_id"   => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "party_id"            => $customer_id,
                    "party_type"          => "customer",
                    "billing_country_id"  => $customer[0]->customer_country_id,
                    "billing_state_id"    => $customer[0]->customer_state_id,
                    "added_date"          => date('Y-m-d'),
                    "added_user_id"       => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"           => $this->session->userdata('SESS_BRANCH_ID'),
                    "currency_id"         => $this->session->userdata('SESS_DEFAULT_CURRENCY'),
                    "updated_date"        => "",
                    "updated_user_id"     => "",
                    "note1"               => '',
                    "note2"               => '',
                    "grand_total_without_roundoff" => 0,
                "round_off_amount" => 0,
                    "gst_payable" => 'no');
            $data_main             = array_map('trim', $advance_data);
            $advance_voucher_table = 'advance_voucher';
            if ($advance_id = $this->general_model->insertData($advance_voucher_table, $data_main)) {
                $reference_id = $advance_id;
                $item_id = $this->addAdvaceProduct('Advance voucher amount');
                $item_data = array(
                            "item_id"              => $item_id,
                            "item_type"            => 'advance',
                            "item_quantity"        => 1,
                            "item_price"            => $voucher_amount,
                            "item_sub_total"       => $voucher_amount,
                            "item_grand_total"     => $voucher_amount,
                            "item_gst_id"          => '',
                            "item_igst_percentage" => 0,
                            "item_igst_amount"     => 0,
                            "item_cgst_percentage" => 0,
                            "item_cgst_amount"     => 0,
                            "item_sgst_percentage" => 0,
                            "item_sgst_amount"     => 0,
                            "item_tax_percentage"  => 0,
                            "item_tax_amount" => 0,
                            "item_cess_percentage" => 0,
                            "item_cess_amount"     => 0,
                            "item_description"     => '',
                            "item_cess_id"         => '',
                            "item_tds_id"          => '',
                    "advance_voucher_id" => $advance_id);
                $this->general_model->insertData('advance_voucher_item', $item_data);

                $log_data                  = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $advance_id,
                        'table_name'        => $advance_voucher_table,
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message' => 'Advance Receipt Inserted');
                $data_main['advance_id']   = $advance_id;
                $log_table                 = $this->config->item('log_table');
                $this->general_model->insertData($log_table, $log_data);
            }

            /*$advance_ledger_id            = $this->ledger_model->getDefaultLedger('Advance from Customer '.$customer_name);*/
            $advance_ledger_id            = $this->ledger_model->getDefaultLedger('Excess Received');
            if($advance_ledger_id == 0){
                $default_exc_id = $advance_ledger['EXCESS'];
                $exc_ledger_name = $this->ledger_model->getDefaultLedgerId($default_exc_id);
                    
                $exc_ary = array(
                                'ledger_name' =>'Excess Received',
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Indirect Incomes',
                                'default_ledger_id' => 0,
                                'default_value' => '',
                                'amount' => 0
                            );
                if(!empty($exc_ledger_name)){
                    $exc_ary['ledger_name'] = $exc_ledger_name->ledger_name;
                    $exc_ary['primary_grp'] = $exc_ledger_name->sub_group_1;
                    $exc_ary['second_grp'] = $exc_ledger_name->sub_group_2;
                    $exc_ary['main_grp'] = $exc_ledger_name->main_group;
                    $exc_ary['default_ledger_id'] = $exc_ledger_name->ledger_id;
                }
                $advance_ledger_id = $this->ledger_model->getGroupLedgerId($exc_ary);

                /*$advance_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'Excess Received',
                                                                    'subgrp_2' => '',
                                                                    'subgrp_1' => '',
                                                                    'main_grp' => 'Indirect Incomes',
                                                                    'amount' => 0
                                                                ));*/
            }
            $ledger_table = 'accounts_advance_voucher';
            $ledger_table_id = 'advance_voucher_id';
        } elseif ($this->input->post('voucher_type') == 'income') {
            $general_voucher_data = array(
                                    "branch_id"                 => $this->session->userdata('SESS_BRANCH_ID'),
                                    "voucher_number"            => $voucher_number,
                                    "voucher_type"              => "general_bill",
                                    "voucher_date"              => $voucher_date,
                                    "description"               => "",
                                    "reference_id" => $sales_id,
                                    "from_account"              => "",
                                    "to_account"                => "",
                                    "reference_type"            => "sales",
                                    "reference_number"          => $sales_data[0]->sales_invoice_number,
                                    "added_user_id"             => $this->session->userdata('SESS_USER_ID'),
                                    "added_date"                => date('Y-m-d'),
                                    "updated_user_id"           => "",
                                    "updated_date"              => "",
                                    "currency_id"               => $currency,
                                    "financial_year_id"         => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    "receipt_amount"            => $voucher_amount,
                                    "note1"                     => '',
                                    "note2"                     => '');

            $advance_id = $this->general_model->insertData("general_voucher", $general_voucher_data);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $advance_id,
                'table_name' => 'general_voucher',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'General Voucher Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
            $reference_id = $advance_id;
            $reference_type = 'general_voucher';

            $advance_ledger_id = $this->ledger_model->getDefaultLedger('Payable to Customer '.$customer_name);
            /*$advance_ledger_id = $this->ledger_model->getDefaultLedger('Excess Received');*/
            if($advance_ledger_id == 0){
                $advance_ledger_id = $this->ledger_model->addGroupLedger(array(
                    'ledger_name' => 'Payable to Customer ' . $customer_name,
                                                                    'subgrp_2' => 'Sundry Debtors',
                                                                    'subgrp_1' => '',
                                                                    'main_grp' => 'Current Assets',
                                                                    'amount' => 0
                                                                ));
            }
            $ledger_table = 'accounts_general_voucher';
            $ledger_table_id = 'general_voucher_id';
        }

        /* ledger posting */
        $i = 0;
        $ledger_entry = array();
        $ledger_entry[$i]["ledger_from"] = $customer_ledger_id;
        $ledger_entry[$i]["ledger_to"] = $customer_ledger_id;
        $ledger_entry[$i][$ledger_table_id] = $advance_id;
        $ledger_entry[$i]["voucher_amount"] = $voucher_amount;
        $ledger_entry[$i]["converted_voucher_amount"] = 0;
        $ledger_entry[$i]["dr_amount"] = $voucher_amount;
        $ledger_entry[$i]["cr_amount"] = 0;
        $ledger_entry[$i]['ledger_id'] = $customer_ledger_id;
        $i = $i + 1;

        $ledger_entry[$i]["ledger_from"] = $advance_ledger_id;
        $ledger_entry[$i]["ledger_to"] = $advance_ledger_id;
        $ledger_entry[$i][$ledger_table_id] = $advance_id;
        $ledger_entry[$i]["voucher_amount"] = $voucher_amount;
        $ledger_entry[$i]["converted_voucher_amount"] = 0;
        $ledger_entry[$i]["dr_amount"] = 0;
        $ledger_entry[$i]["cr_amount"] = $voucher_amount;
        $ledger_entry[$i]['ledger_id'] = $advance_ledger_id;


        $this->db->insert_batch($ledger_table, $ledger_entry);
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        foreach ($ledger_entry as $key => $value) {
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

        /* update voucher amount */
        $this->db->query("UPDATE sales SET excess_return = excess_return + $voucher_amount WHERE sales_id='{$sales_id}'");

        /* update excess used */
        if($excess_id != '0'){
            $excess_id = explode(',', $excess_id);
            $update = array('is_used' => '1');
            $this->db->set($update);
            $this->db->where_in('excess_id', $excess_id);
            $this->db->update('sales_excess_amount');
            $excess_id = implode(',', $excess_id);
        }

        $update = array('sales_id' => $sales_id,'excess_amount' => $voucher_amount,'is_used' => '1','reference_type' => $reference_type , 'reference_id' => $reference_id,'receipt_excess_id'=> $excess_id ,'created_at' => date('Y-m-d H:i:s'),'created_by' => $this->session->userdata('SESS_USER_ID'));
        $this->db->insert('sales_excess_history',$update);
        echo true;
    }

    public function getAllAdvanceDetail() {
        $sales_id = $this->input->post('sales_id');
        $customer_id = $this->input->post('c_id');
        $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array('customer_id' => $customer_id));
        $customer_name = '';
        if (!empty($customer))
            $customer_name = $customer[0]->customer_name;

        $sales = $this->general_model->getRecords('sales_invoice_number', 'sales', array('sales_id' => $sales_id));

        $reference_number = '';
        if (!empty($sales))
            $reference_number = $sales[0]->sales_invoice_number;

        $this->db->select('v.*,h.*');
        $this->db->from('advance_paid_history h');
        $this->db->join('advance_voucher v', 'h.advance_voucher_id=v.advance_voucher_id');
        $this->db->where('h.sales_id', $sales_id);
        $this->db->where('v.delete_status', '0');
        $result = $this->db->get();
        $history = $result->result();
        $tr = '';
        $send_data = array();
        if (!empty($history)) {
            foreach ($history as $key => $value) {
                $send_data[] = array('customer_name' => $customer_name,
                                    'voucher_number' => $value->voucher_number,
                                    'reference_number' => $reference_number,
                    'adjusted_amount' => $this->precise_amount($value->adjusted_amount, 2),
                );
            }
        }
        echo json_encode($send_data);
    }

    public function add_advance() {

        $advance_voucher_module_id         = $this->config->item('advance_voucher_module');
        $data['module_id']                 = $advance_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "add_privilege";
        $data['privilege']                 = "add_privilege";
        $section_modules                   = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $advance_ledger = $this->config->item('advance_ledger');
        $product_module_id  = $data['product_module_id'] = $this->config->item('product_module');
        $service_module_id  = $data['service_module_id'] = $this->config->item('service_module');
        $customer_module_id = $data['customer_module_id'] = $this->config->item('customer_module');
        $category_module_id = $data['category_module_id'] = $this->config->item('category_module');
        $subcategory_module_id = $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $tax_module_id  = $data['tax_module_id'] = $this->config->item('tax_module');
        $discount_module_id = $data['discount_module_id'] = $this->config->item('discount_module');

        $accounts_module_id = $data['accounts_module_id'] = $this->config->item('accounts_module');
        $bank_account_module_id  = $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $modules_present                   = array(
                'product_module_id'      => $product_module_id,
                'service_module_id'      => $service_module_id,
                'customer_module_id'     => $customer_module_id,
                'category_module_id'     => $category_module_id,
                'subcategory_module_id'  => $subcategory_module_id,
                'tax_module_id'          => $tax_module_id,
                'discount_module_id'     => $discount_module_id,
                'accounts_module_id'     => $accounts_module_id,
            'bank_account_module_id' => $bank_account_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $currency                          = $this->input->post('currency_id');
        $access_settings          = $data['access_settings'];
        if ($access_settings[0]->invoice_creation == "automatic") {
            $primary_id      = "advance_voucher_id";
            $table_name      = 'advance_voucher';
            $date_field_name = "voucher_date";
            $current_date    =date('Y-m-d',strtotime($this->input->post('voucher_date')));
            $voucher_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $voucher_number = $this->input->post('voucher_number');
        }
        $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array('customer_id' => $this->input->post('customer')));
        $ledger_customer = $customer[0]->ledger_id;
        $customer_name = $customer[0]->customer_name;
        $customer_ledger_id = $customer[0]->ledger_id;

        if(!$customer_ledger_id){
            $default_customer_id = $advance_ledger['CUSTOMER'];
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
        /*if ($customer_name != '') {
            $customer_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => $customer_name,
                                                                    'subgrp_2' => 'Sundry Debtors',
                                                                    'subgrp_1' => '',
                                                                    'main_grp' => 'Current Assets',
                                                                    'amount' => 0
                                                                ));
        }*/

        /*$ledger_igst     = $this->ledger_model->getDefaultLedger('IGST');
        $ledger_cgst     = $this->ledger_model->getDefaultLedger('CGST');
        $ledger_sgst     = $this->ledger_model->getDefaultLedger('SGST');*/
        if ($this->input->post('payment_mode') == "other payment mode") {
            $payment_via = $this->input->post('payment_via');
            $reff_number = $this->input->post('ref_number');
        } else {
            $payment_via = "";
            $reff_number = "";
        } 

        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode          = $bank_acc_payment_mode[0];
            $from_acc              = $bank_acc_payment_mode[1];
           
            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode));
            $from_ledger_id =  $ledger_bank_acc[0]->ledger_id;
            $ledger_from = $ledger_bank_acc[0]->ledger_id;

        } else {
            $payment_mode     = $this->input->post('payment_mode');
            $from_acc         = $this->input->post('payment_mode');
            /*$ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));*/

            if ($from_acc != '') {
                $default_payment_id = $advance_ledger['Other_Payment'];
                if (strtolower($payment_mode) == "cash"){
                    $default_payment_id = $advance_ledger['Cash_Payment'];
                }
                $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
                $default_payment_ary = array(
                                'ledger_name' => $from_acc,
                                'second_grp' => '',
                                'primary_grp' => 'Cash & Cash Equivalent',
                                'main_grp' => 'Current Assets',
                                'default_ledger_id' => 0,
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
                /*$from_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => $from_acc,
                                                    'subgrp_1' => '',
                                                    'subgrp_2' => (strtolower($from_acc) == 'cash' ? 'Cash & Cash Equivalent' : ''),
                                                    'main_grp' => 'Current Assets',
                                                    'amount' => 0
                                                ));*/
                    
            }
        }

       // $cheque_date = date('Y-m-d', strtotime($this->input->post('cheque_date')));
        $cheque_date = ($this->input->post('cheque_date') != '' ? date('Y-m-d', strtotime($this->input->post('cheque_date'))) : '');
        if (!$cheque_date) {
            $cheque_date = null;
        }

       $round_off_plus = $this->input->post('round_off_plus');
       $round_off_minus = $this->input->post('round_off_minus');

        if ($round_off_plus > 0) {
            $round_off_amount = $round_off_plus;
        } elseif ($round_off_minus < 0) {
            $round_off_amount = $round_off_minus;
        } else {
            $round_off_amount = 0;
        }
        $sub_total = $this->input->post('total_sub_total');
        $receipt_amount_x =  $this->input->post('receipt_amount');
        $total_tax_amount = $this->input->post('total_tax_amount');


        $voucher_date = date('Y-m-d',strtotime($this->input->post('voucher_date')));

        $gst_payable = $this->input->post('gst_payable');
        $gst_payable = ($gst_payable) ? $gst_payable : 'no';
        if ($gst_payable != 'yes') {
            $customer_amount = $receipt_amount_x;
        } else {
           // $customer_amount = bcsub($receipt_amount_x, $total_tax_amount,2);
            $customer_amount = $receipt_amount_x;
        }
        $state_billing_id = $this->input->post('billing_state');
        $advance_data = array(
                "voucher_date"        => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
                "voucher_number"      => $voucher_number,
                "voucher_sub_total"   => $this->input->post('total_sub_total'),
                "receipt_amount"      => $this->input->post('receipt_amount'),
                "voucher_tax_amount"  => $this->input->post('total_tax_amount'),
                "description"         => $this->input->post('description'),
                "reference_id"        => "",
                "reference_number"    => "",
                "from_account"        => $from_acc,
                "to_account"          => 'customer-' . $customer[0]->customer_name,
                "payment_mode"        => $payment_mode,
                "bank_name"           => $this->input->post('bank_name'),
                "cheque_date"         => $cheque_date,
                "cheque_number"       => $this->input->post('cheque_number'),
                "payment_via"         => $payment_via,
                "ref_number"          => $reff_number,
                "voucher_igst_amount" => $this->input->post('total_igst_amount'),
                "voucher_cgst_amount" => $this->input->post('total_cgst_amount'),
                "voucher_sgst_amount" => $this->input->post('total_sgst_amount'),
                "voucher_cess_amount" => $this->input->post('total_tax_cess_amount'),
                "financial_year_id"   => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "party_id"            => $this->input->post('customer'),
                "party_type"          => "customer",
                "billing_country_id"  => $this->input->post('billing_country'),
                "billing_state_id"    => $this->input->post('billing_state'),
                "added_date"          => date('Y-m-d'),
                "added_user_id"       => $this->session->userdata('SESS_USER_ID'),
                "branch_id"           => $this->session->userdata('SESS_BRANCH_ID'),
                "currency_id"         => $this->input->post('currency_id'),
                "updated_date"        => "",
                "updated_user_id"     => "",
                "note1"               => $this->input->post('note1'),
                "note2"               => $this->input->post('note2'),
                "grand_total_without_roundoff" => $this->input->post('without_reound_off_grand_total'),
                "round_off_amount" => $round_off_amount,
            "gst_payable" => $gst_payable);
      /*  if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id'))
        {
            $advance_data['currency_converted_amount'] = $this->input->post('receipt_amount');
        }
        else
        {
            $advance_data['currency_converted_amount'] = "0.00";
          } */
        if ($payment_mode == "cash") {
            $advance_data['voucher_status'] = "0";
        } else {
            $advance_data['voucher_status'] = "1";
        }
        $data_main             = array_map('trim', $advance_data);
        $advance_voucher_table = 'advance_voucher';
        if ($advance_id = $this->general_model->insertData($advance_voucher_table, $data_main)) {
            $successMsg = 'Advance Voucher Added Successfully';
            $this->session->set_flashdata('advance_voucher_success',$successMsg);
            $log_data                  = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $advance_id,
                    'table_name'        => $advance_voucher_table,
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Advance Receipt Inserted');
            $data_main['advance_id']   = $advance_id;
            $log_table                 = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $advance_voucher_item_data = $this->input->post('table_data');
            $js_data                   = json_decode($advance_voucher_item_data);
            $item_table                = 'advance_voucher_item';
            $igst = array();
            $cgst = array();
            $sgst = array();
            $cess = array();
            $tds = array();
            $ledger_entry = array();
            $i = 1;
            foreach ($js_data as $key => $value) {
                if ($value != null) {

                    $item_data = array(
                            "item_id"              => $value->item_id,
                            "item_type"            => $value->item_type,
                            "item_quantity"         => $value->item_quantity,
                            "item_price"            => $value->item_price,
                            "item_sub_total"       => $value->item_sub_total,
                            "item_grand_total"     => $value->item_grand_total,
                            "item_gst_id"          => $value->item_tax_id,
                            "item_igst_percentage" => $value->item_percentage_igst,
                            "item_igst_amount"     => $value->item_tax_amount_igst,
                            "item_cgst_percentage" => $value->item_percentage_cgst,
                            "item_cgst_amount"     => $value->item_tax_amount_cgst,
                            "item_sgst_percentage" => $value->item_percentage_sgst,
                            "item_sgst_amount"     => $value->item_tax_amount_sgst,
                            "item_tax_percentage"  => $value->item_tax_percentage,
                            "item_tax_amount" => $value->item_tax_amount,
                            "item_cess_percentage" => $value->item_tax_cess_percentage,
                            "item_cess_amount"     => $value->item_tax_cess_amount,
                            "item_description"     => $value->item_description,
                            "item_cess_id"         => $value->item_tax_cess_id,
                            "item_tds_id"          => $value->item_tds_id,
                        "advance_voucher_id" => $advance_id);
                    $data_item[$i] = array_map('trim', $item_data);
                    /*                     * ******* GST TAX STARTS ******* */
                    if ($gst_payable != 'yes') {

                        if ($value->item_percentage_igst != '' && $value->item_percentage_igst != 0) {
                            $default_igst_id = $advance_ledger['IGST@X'];
                            $igst_x = $this->ledger_model->getDefaultLedgerId($default_igst_id);

                            $igst_ary = array(
                                            'ledger_name'=>'Input IGST@'.(float)$value->item_percentage_igst.'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_percentage_igst,
                                            'amount' => 0
                                        );
                            if(!empty($igst_x)){
                                $igst_ledger = $igst_x->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_igst , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                $igst_ary['main_grp'] = $igst_x->main_group;
                                $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($igst[$igst_tax_ledger])) {
                                $igst[$igst_tax_ledger] = 0;
                            }
                            $igst[$igst_tax_ledger] += $value->item_tax_amount_igst;
                            $ledger_entry[$igst_tax_ledger]["ledger_from"] = $igst_tax_ledger;
                            $ledger_entry[$igst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$igst_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$igst_tax_ledger]["voucher_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$igst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$igst_tax_ledger]["dr_amount"] = 0;
                            $ledger_entry[$igst_tax_ledger]["cr_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$igst_tax_ledger]['ledger_id'] = $igst_tax_ledger;

                            /* Advance paid starts */
                            $default_igst_paid_advance_id = $advance_ledger['IGST_paid_on_advance'];
                            $igst_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_igst_paid_advance_id);

                            $igst_advance_ary = array(
                                            'ledger_name'=>'IGST paid on advance',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($igst_advance_paid_x)){
                                $igst_advance_ledger = $igst_advance_paid_x->ledger_name;
                               // $igst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_igst , $igst_ledger);
                                $igst_advance_ary['ledger_name'] = $igst_advance_ledger;
                                $igst_advance_ary['primary_grp'] = $igst_advance_paid_x->sub_group_1;
                                $igst_advance_ary['second_grp'] = $igst_advance_paid_x->sub_group_2;
                                $igst_advance_ary['main_grp'] = $igst_advance_paid_x->main_group;
                                $igst_advance_ary['default_ledger_id'] = $igst_advance_paid_x->ledger_id;
                            }
                            $igst_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($igst_advance_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($igst_advance[$igst_tax_advance_ledger])) {
                                $igst_advance[$igst_tax_advance_ledger] = 0;
                            }
                            $igst_advance[$igst_tax_advance_ledger] += $value->item_tax_amount_igst;
                            $ledger_entry[$igst_tax_advance_ledger]["ledger_from"] = $igst_tax_advance_ledger;
                            $ledger_entry[$igst_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$igst_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$igst_tax_advance_ledger]["voucher_amount"] = $igst_advance[$igst_tax_advance_ledger];
                            $ledger_entry[$igst_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$igst_tax_advance_ledger]["dr_amount"] = $igst_advance[$igst_tax_advance_ledger];
                            $ledger_entry[$igst_tax_advance_ledger]["cr_amount"] = 0;
                            $ledger_entry[$igst_tax_advance_ledger]['ledger_id'] = $igst_tax_advance_ledger;

                            /* Advance paid ends */
                            $i = $i + 1;
                        }

                        if ($value->item_percentage_cgst != '' && $value->item_percentage_cgst != 0) {
                            $default_cgst_id = $advance_ledger['CGST@X'];
                            $cgst_x = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                            $cgst_ary = array(
                                            'ledger_name' => 'Input CGST@'.(float)$value->item_percentage_cgst.'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_percentage_cgst,
                                            'amount' => 0
                                        );
                            if(!empty($cgst_x)){
                                $cgst_ledger = $cgst_x->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_cgst , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_x->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                            }
                            /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);*/
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);

                            /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'CGST@' . (float)$value->item_percentage_cgst.'%',
                                                                            'subgrp_1' => 'CGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($cgst[$cgst_tax_ledger])) {
                                $cgst[$cgst_tax_ledger] = 0;
                            }
                            $cgst[$cgst_tax_ledger] += $value->item_tax_amount_cgst;
                            $ledger_entry[$cgst_tax_ledger]["ledger_from"] = $cgst_tax_ledger;
                            $ledger_entry[$cgst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cgst_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cgst_tax_ledger]["voucher_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$cgst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cgst_tax_ledger]["dr_amount"] = 0;
                            $ledger_entry[$cgst_tax_ledger]["cr_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$cgst_tax_ledger]['ledger_id'] = $cgst_tax_ledger;

                             /* Advance paid starts */
                            $default_cgst_paid_advance_id = $advance_ledger['CGST_paid_on_advance'];
                            $cgst_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_cgst_paid_advance_id);

                            $cgst_advance_ary = array(
                                            'ledger_name'=>'CGST paid on advance',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($cgst_advance_paid_x)){
                                $cgst_advance_ledger = $cgst_advance_paid_x->ledger_name;
                              //  $cgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_cgst , $igst_ledger);
                                $cgst_advance_ary['ledger_name'] = $cgst_advance_ledger;
                                $cgst_advance_ary['primary_grp'] = $cgst_advance_paid_x->sub_group_1;
                                $cgst_advance_ary['second_grp'] = $cgst_advance_paid_x->sub_group_2;
                                $cgst_advance_ary['main_grp'] = $cgst_advance_paid_x->main_group;
                                $cgst_advance_ary['default_ledger_id'] = $cgst_advance_paid_x->ledger_id;
                            }
                            $cgst_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($cgst_advance_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($cgst_advance[$cgst_tax_advance_ledger])) {
                                $cgst_advance[$cgst_tax_advance_ledger] = 0;
                            }
                            $cgst_advance[$cgst_tax_advance_ledger] += $value->item_tax_amount_cgst;
                            $ledger_entry[$cgst_tax_advance_ledger]["ledger_from"] = $cgst_tax_advance_ledger;
                            $ledger_entry[$cgst_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cgst_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cgst_tax_advance_ledger]["voucher_amount"] = $cgst_advance[$cgst_tax_advance_ledger];
                            $ledger_entry[$cgst_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cgst_tax_advance_ledger]["dr_amount"] = 0;
                            $ledger_entry[$cgst_tax_advance_ledger]["cr_amount"] = $cgst_advance[$cgst_tax_advance_ledger];
                            $ledger_entry[$cgst_tax_advance_ledger]['ledger_id'] = $cgst_tax_advance_ledger;

                            /* Advance paid ends */

                            $i = $i + 1;
                        }

                        if ($value->item_percentage_sgst != '' && $value->item_percentage_sgst != 0) {
                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($state_billing_id);
                            if ($is_utgst == '1')
                                $gst_lbl = 'UTGST';

                            $default_sgst_id = $advance_ledger[$gst_lbl.'@X'];
                            $sgst_x = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                            
                            $sgst_ary = array(
                                            'ledger_name' => 'Input '.$gst_lbl.'@'. (float)$value->item_percentage_sgst.'%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' =>  (float)$value->item_percentage_sgst,
                                            'amount' => 0
                                        );
                            if(!empty($sgst_x)){
                                if($is_utgst != '1') {
                                    $sgst_ledger = $sgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}', (float)$value->item_percentage_sgst , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                }else{
                                    $default_utgst_id = $advance_ledger['UTGST@X'];
                                    $utgst_x = $this->ledger_model->getDefaultLedgerId($default_utgst_id);
                                    $sgst_ledger = $utgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}', (float)$value->item_percentage_sgst , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $utgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                }
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);

                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => $gst_lbl . '@' . (float)$value->item_percentage_sgst.'%',
                                                                            'subgrp_1' => 'SGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($sgst[$sgst_tax_ledger])) {
                                $sgst[$sgst_tax_ledger] = 0;
                            }
                            $sgst[$sgst_tax_ledger] += $value->item_tax_amount_sgst;
                            $ledger_entry[$sgst_tax_ledger]["ledger_from"] = $sgst_tax_ledger;
                            $ledger_entry[$sgst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$sgst_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$sgst_tax_ledger]["voucher_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$sgst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$sgst_tax_ledger]["dr_amount"] = 0;
                            $ledger_entry[$sgst_tax_ledger]["cr_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$sgst_tax_ledger]['ledger_id'] = $sgst_tax_ledger;


                              /* Advance paid starts */
                            $default_sgst_paid_advance_id = $advance_ledger[$gst_lbl.'_paid_on_advance'];
                            $sgst_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_sgst_paid_advance_id);

                            $sgst_advance_ary = array(
                                            'ledger_name'=>$gst_lbl.' paid on advance',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($sgst_advance_paid_x)){
                                $sgst_advance_ledger = $sgst_advance_paid_x->ledger_name;
                               // $sgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_sgst , $sgst_ledger);
                                $sgst_advance_ary['ledger_name'] = $sgst_advance_ledger;
                                $sgst_advance_ary['primary_grp'] = $sgst_advance_paid_x->sub_group_1;
                                $sgst_advance_ary['second_grp'] = $sgst_advance_paid_x->sub_group_2;
                                $sgst_advance_ary['main_grp'] = $sgst_advance_paid_x->main_group;
                                $sgst_advance_ary['default_ledger_id'] = $sgst_advance_paid_x->ledger_id;
                            }
                            $sgst_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($sgst_advance_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($sgst_advance[$sgst_tax_advance_ledger])) {
                                $sgst_advance[$sgst_tax_advance_ledger] = 0;
                            }
                            $sgst_advance[$sgst_tax_advance_ledger] += $value->item_tax_amount_sgst;
                            $ledger_entry[$sgst_tax_advance_ledger]["ledger_from"] = $sgst_tax_advance_ledger;
                            $ledger_entry[$sgst_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$sgst_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$sgst_tax_advance_ledger]["voucher_amount"] = $sgst_advance[$sgst_tax_advance_ledger];
                            $ledger_entry[$sgst_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$sgst_tax_advance_ledger]["dr_amount"] = $sgst_advance[$sgst_tax_advance_ledger];
                            $ledger_entry[$sgst_tax_advance_ledger]["cr_amount"] = 0;
                            $ledger_entry[$sgst_tax_advance_ledger]['ledger_id'] = $sgst_tax_advance_ledger;

                            /* Advance paid ends */
                            $i = $i + 1;
                        }

                        /** ******* GST TAX ENDS ******* */


                        /** ******* CESS TAX STARTS ******* */

                        if ($value->item_tax_cess_percentage != '' && $value->item_tax_cess_percentage != 0) {
                            $default_cess_id = $advance_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                           
                            $cess_ary = array(
                                            'ledger_name' => 'Input Compensation Cess @'.(float)$value->item_tax_cess_percentage.'%',
                                            'second_grp' => 'Cess',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_tax_cess_percentage,
                                            'amount' => 0
                                        );
                            if(!empty($cess_ledger_name)){
                                $cess_ledger = $cess_ledger_name->ledger_name;
                                $cess_ledger = str_ireplace('{{X}}',(float)$value->item_tax_cess_percentage , $cess_ledger);
                                $cess_ary['ledger_name'] = $cess_ledger;
                                $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                                $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                                $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                                $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                            }
                            $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);
                            /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Cess@' . (float)$value->item_tax_cess_percentage.'%',
                                                                            'subgrp_1' => 'Cess',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($cess[$cess_tax_ledger])) {
                                $cess[$cess_tax_ledger] = 0;
                            }

                            $cess[$cess_tax_ledger] += $value->item_tax_cess_amount;
                            $ledger_entry[$cess_tax_ledger]["ledger_from"] = $cess_tax_ledger;
                            $ledger_entry[$cess_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cess_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cess_tax_ledger]["voucher_amount"] =  $cess[$cess_tax_ledger];
                            $ledger_entry[$cess_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cess_tax_ledger]["dr_amount"] =  0;
                            $ledger_entry[$cess_tax_ledger]["cr_amount"] = $cess[$cess_tax_ledger];
                            $ledger_entry[$cess_tax_ledger]['ledger_id'] = $cess_tax_ledger;

                             /* Advance paid starts */
                            $default_cess_paid_advance_id = $advance_ledger['Cess_paid_on_advance'];
                            $cess_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_cess_paid_advance_id);

                            $cess_advance_ary = array(
                                            'ledger_name'=> 'Cess paid on advance',
                                            'second_grp' => '',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($cess_advance_paid_x)){
                                $cess_advance_ledger = $cess_advance_paid_x->ledger_name;
                                //$sgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_sgst , $sgst_ledger);
                                $cess_advance_ary['ledger_name'] = $cess_advance_ledger;
                                $cess_advance_ary['primary_grp'] = $cess_advance_paid_x->sub_group_1;
                                $cess_advance_ary['second_grp'] = $cess_advance_paid_x->sub_group_2;
                                $cess_advance_ary['main_grp'] = $cess_advance_paid_x->main_group;
                                $cess_advance_ary['default_ledger_id'] = $cess_advance_paid_x->ledger_id;
                            }
                            $cess_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($cess_advance_ary);

                            
                            if (!isset($cess_advance[$cess_tax_advance_ledger])) {
                                $cess_advance[$cess_tax_advance_ledger] = 0;
                            }
                            $cess_advance[$cess_tax_advance_ledger] += $value->item_tax_cess_amount;
                            $ledger_entry[$cess_tax_advance_ledger]["ledger_from"] = $cess_tax_advance_ledger;
                            $ledger_entry[$cess_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cess_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cess_tax_advance_ledger]["voucher_amount"] = $cess_advance[$cess_tax_advance_ledger];
                            $ledger_entry[$cess_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cess_tax_advance_ledger]["dr_amount"] = $cess_advance[$cess_tax_advance_ledger];
                            $ledger_entry[$cess_tax_advance_ledger]["cr_amount"] = 0;
                            $ledger_entry[$cess_tax_advance_ledger]['ledger_id'] = $cess_tax_advance_ledger;

                            /* Advance paid ends */
                            $i = $i + 1;
                    }
                        /*                         * ******* CESS TAX ENDS ******* */
            }
                }
            $i = $i + 1;
        }

        $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
        $ledger_entry[$from_ledger_id]["ledger_to"] = $customer_ledger_id;
        $ledger_entry[$from_ledger_id]["advance_voucher_id"] = $advance_id;
        $ledger_entry[$from_ledger_id]["voucher_amount"] = $customer_amount;
        $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
        $ledger_entry[$from_ledger_id]["dr_amount"] = $customer_amount;
        $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
        $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;
         $i = $i + 1;
        /*$advance_ledger_id            = $this->ledger_model->getDefaultLedger('Advance');
        if ($advance_ledger_id == 0) {
        $advance_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                                'ledger_name' => 'Advance',
                                                                'subgrp_2' => '',
                                                                'subgrp_1' => '',
                                                                'main_grp' => 'Current Assets',
                                                                'amount' => 0
                                                            ));
        }*/

        $ledger_entry[$customer_ledger_id]["ledger_from"] = $from_ledger_id;
        $ledger_entry[$customer_ledger_id]["ledger_to"] = $customer_ledger_id;
        $ledger_entry[$customer_ledger_id]["advance_voucher_id"] = $advance_id;
        $ledger_entry[$customer_ledger_id]["voucher_amount"] = $customer_amount; //$sub_total;
        $ledger_entry[$customer_ledger_id]["converted_voucher_amount"] = 0;
        $ledger_entry[$customer_ledger_id]["dr_amount"] = 0;
        $ledger_entry[$customer_ledger_id]["cr_amount"] = $customer_amount; //$sub_total;
        $ledger_entry[$customer_ledger_id]['ledger_id'] = $customer_ledger_id;
        $i = $i + 1;

        $round_off_plus =  $this->input->post('round_off_plus');
        $round_off_minus =  $this->input->post('round_off_minus');
        if ($round_off_plus > 0 || $round_off_minus > 0) {

           // $round_off_amount = $data_main['round_off_amount'];

            if ($round_off_plus > 0) {
                $round_off_amount = $round_off_plus;
                $dr_amount        = $round_off_plus;
                $cr_amount        = 0;

                $title     = "ROUND OFF";
                $subgroup  = "ROUND OFF";
                $ledger_to = $ledgers['ledger_from'];
                $default_roundoff_id = $advance_ledger['RoundOff_Given'];
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
                    'ledger_name' => 'ROUND OFF',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Expenses',
                    'amount' =>  0
                );*/
            } else {
                $round_off_amount = $round_off_minus;
                $dr_amount        = 0;
                $cr_amount        = $round_off_amount;

                $title     = "ROUND OFF";
                $subgroup  = "ROUND OFF";
                $default_roundoff_id = $advance_ledger['RoundOff_Received'];
                $roundoff_ledger_name = $this->ledger_model->getDefaultLedgerId($default_roundoff_id);
                $round_off_ary = array(
                                'ledger_name' => 'ROUND OFF Received',
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Indirect Incomes',
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );
                // $ledger_to = $ledgers['ledger_to'];
                /*$round_off_ary = array(
                    'ledger_name' => 'ROUND OFF',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Incomes',
                    'amount' =>  0
                );*/
            }
            if(!empty($roundoff_ledger_name)){
                $round_off_ary['ledger_name'] = $roundoff_ledger_name->ledger_name;
                $round_off_ary['primary_grp'] = $roundoff_ledger_name->sub_group_1;
                $round_off_ary['second_grp'] = $roundoff_ledger_name->sub_group_2;
                $round_off_ary['main_grp'] = $roundoff_ledger_name->main_group;
                $round_off_ary['default_ledger_id'] = $roundoff_ledger_name->ledger_id;
            }
            $round_off_ledger_id = $this->ledger_model->getGroupLedgerId($round_off_ary);
            /*$round_off_ledger_id = $this->ledger_model->addGroupLedger($round_off_ary);*/

            $ledgers['round_off_ledger_id'] = $round_off_ledger_id;

            $ledger_entry[$round_off_ledger_id]["ledger_from"] = $round_off_ledger_id;
            $ledger_entry[$round_off_ledger_id]["ledger_to"] = $customer_ledger_id;
            $ledger_entry[$round_off_ledger_id]["advance_voucher_id"] =  '';
            $ledger_entry[$round_off_ledger_id]["voucher_amount"] = round($round_off_amount, 2);
            $ledger_entry[$round_off_ledger_id]["converted_voucher_amount"] = 0;
            $ledger_entry[$round_off_ledger_id]["dr_amount"] = round($dr_amount, 2);
            $ledger_entry[$round_off_ledger_id]["cr_amount"] = round($cr_amount, 2);
            $ledger_entry[$round_off_ledger_id]["ledger_id"] = $round_off_ledger_id;
            $i = $i + 1;
        }
       
        $this->db->insert_batch($item_table, $data_item);
        $this->db->insert_batch('accounts_advance_voucher', $ledger_entry);
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

            /*
            if ($this->general_model->insertData($item_table, $data_item))
                    {

                    }

             if (isset($data['other_modules_present']['accounts_module_id']))
            {
                foreach ($data['access_sub_modules'] as $key => $value)
                {
                    if (isset($data['accounts_sub_module_id']))
                    {
                        if ($data['accounts_sub_module_id'] == $value->sub_module_id)
                        {
                            $ledger_id    = array(
                                    'ledger_customer'  => $ledger_customer,
                                    'ledger_cash_bank' => $ledger_cash_bank,
                                    'ledger_igst'      => $ledger_igst,
                                    'ledger_cgst'      => $ledger_cgst,
                                    'ledger_sgst'      => $ledger_sgst );
                            $ledger_entry = array(
                                    'grand_total' => $data_main['receipt_amount'],
                                    'sub_total'   => $data_main['voucher_sub_total'],
                                    'igst_amount' => $data_main['voucher_igst_amount'],
                                    'cgst_amount' => $data_main['voucher_cgst_amount'],
                                    'sgst_amount' => $data_main['voucher_sgst_amount'] );
                            $this->voucher_entry($advance_id, $ledger_id, $ledger_entry, "add", $currency);
                        }
                    }
                }
            }
            if ($this->session->userdata('cat_type') != "" && $this->session->userdata('cat_type') != null && $this->session->userdata('cat_type') == 'customer_advance' && $payment_mode != "cash")
            {
                $this->session->unset_userdata('cat_type');
                if ($currency == $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                {
                    redirect('bank_statement/bank_group', 'refresh');
                }
                else
                {
                    redirect('advance_voucher', 'refresh');
                }
              } */
           redirect('advance_voucher', 'refresh');
        } else {
            $errorMsg = 'Advance Voucher Add Unsuccessful';
            $this->session->set_flashdata('advance_voucher_error',$errorMsg);
            redirect('advance_voucher', 'refresh');
        }
    }

    public function voucher_entry($advance_id, $ledger_id, $ledger_entry, $operation, $currency) {
        $data1 = array(
                'advance_voucher_id' => $advance_id,
                'ledger_from'        => $ledger_id['ledger_cash_bank'],
                'ledger_to'          => $ledger_id['ledger_customer'],
                'voucher_amount'     => $ledger_entry['grand_total'],
                'dr_amount'          => $ledger_entry['grand_total'],
            'cr_amount' => "0.00");
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency) {
            $data1['converted_voucher_amount'] = $ledger_entry['grand_total'];
        } else {
            $data1['converted_voucher_amount'] = "0.00";
        }

        $data2 = array(
                'advance_voucher_id' => $advance_id,
                'ledger_from'        => $ledger_id['ledger_customer'],
                'ledger_to'          => $ledger_id['ledger_cash_bank'],
                'voucher_amount'     => $ledger_entry['sub_total'],
                'dr_amount'          => "0.00",
            'cr_amount' => $ledger_entry['sub_total']);
        $data3 = array(
                'advance_voucher_id' => $advance_id,
                'ledger_from'        => $ledger_id['ledger_igst'],
                'ledger_to'          => $ledger_id['ledger_cash_bank'],
                'voucher_amount'     => $ledger_entry['igst_amount'],
                'dr_amount'          => "0.00",
            'cr_amount' => $ledger_entry['igst_amount']);
        $data4 = array(
                'advance_voucher_id' => $advance_id,
                'ledger_from'        => $ledger_id['ledger_cgst'],
                'ledger_to'          => $ledger_id['ledger_cash_bank'],
                'voucher_amount'     => $ledger_entry['cgst_amount'],
                'dr_amount'          => "0.00",
            'cr_amount' => $ledger_entry['cgst_amount']);
        $data5 = array(
                'advance_voucher_id' => $advance_id,
                'ledger_from'        => $ledger_id['ledger_sgst'],
                'ledger_to'          => $ledger_id['ledger_cash_bank'],
                'voucher_amount'     => $ledger_entry['sgst_amount'],
                'dr_amount'          => "0.00",
            'cr_amount' => $ledger_entry['sgst_amount']);
        if ($operation == "add") {
            $this->general_model->insertData('accounts_advance_voucher', $data1);
            $this->general_model->insertData('accounts_advance_voucher', $data2);
            $this->general_model->insertData('accounts_advance_voucher', $data3);
            $this->general_model->insertData('accounts_advance_voucher', $data4);
            $this->general_model->insertData('accounts_advance_voucher', $data5);
        } elseif ($operation == "edit") {
            $accounts_advance = $this->general_model->getRecords('accounts_advance_id', 'accounts_advance_voucher', array(
                    'advance_voucher_id' => $advance_id,
                'delete_status' => 0));
            if ($accounts_advance) {
                $this->general_model->updateData('accounts_advance_voucher', $data1, array(
                    'accounts_advance_id' => $accounts_advance[0]->accounts_advance_id));
                $this->general_model->updateData('accounts_advance_voucher', $data2, array(
                    'accounts_advance_id' => $accounts_advance[1]->accounts_advance_id));
                $this->general_model->updateData('accounts_advance_voucher', $data3, array(
                    'accounts_advance_id' => $accounts_advance[2]->accounts_advance_id));
                $this->general_model->updateData('accounts_advance_voucher', $data4, array(
                    'accounts_advance_id' => $accounts_advance[3]->accounts_advance_id));
                $this->general_model->updateData('accounts_advance_voucher', $data5, array(
                    'accounts_advance_id' => $accounts_advance[4]->accounts_advance_id));
            }
        }
    }

    public function edit($id) {
        $id                                = $this->encryption_url->decode($id);
        $data                              = $this->get_default_country_state();

         $advance_voucher_module_id         = $this->config->item('advance_voucher_module');
        $data['module_id']                 = $advance_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "edit_privilege";
        $data['privilege']                 = "edit_privilege";
        $section_modules                   = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $product_module_id  = $data['product_module_id'] = $this->config->item('product_module');
        $service_module_id  = $data['service_module_id'] = $this->config->item('service_module');
        $customer_module_id = $data['customer_module_id'] = $this->config->item('customer_module');
        $category_module_id = $data['category_module_id'] = $this->config->item('category_module');
        $subcategory_module_id = $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $tax_module_id  = $data['tax_module_id'] = $this->config->item('tax_module');
        $discount_module_id = $data['discount_module_id'] = $this->config->item('discount_module');

        $accounts_module_id = $data['accounts_module_id'] = $this->config->item('accounts_module');
        $bank_account_module_id  = $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $modules_present                   = array(
                'product_module_id'      => $product_module_id,
                'service_module_id'      => $service_module_id,
                'customer_module_id'     => $customer_module_id,
                'category_module_id'     => $category_module_id,
                'subcategory_module_id'  => $subcategory_module_id,
                'tax_module_id'          => $tax_module_id,
                'discount_module_id'     => $discount_module_id,
                'accounts_module_id'     => $accounts_module_id,
            'bank_account_module_id' => $bank_account_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $currency                          = $this->input->post('currency_id');
        $access_settings          = $data['access_settings'];

        $data['customer']         = $this->customer_call1();
        $data['currency']         = $this->currency_call();
        $data['bank_account']     = $this->bank_account_call_new();
        $data['product_category'] = $this->product_category_call();
        $data['service_category'] = $this->service_category_call();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_call();
        $data['sac']              = $this->sac_call();
        $data['chapter']          = $this->chapter_call();
        $data['hsn']              = $this->hsn_call();
        $data['discount'] = $this->discount_call();
        $data['data']             = $this->general_model->getRecords('*', 'advance_voucher', array(
            'advance_voucher_id' => $id));
        $data['advance_id']       = $id;
        $inventory_access         = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));

        $data['access_settings'][0]->discount_visible = 'no';
       /* if ($inventory_access[0]->inventory_advanced == "yes")
        {
            $product_items               = $this->common->advance_voucher_items_product_inventory_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }
        else
        { */
            $product_items  = $this->common->advance_voucher_items_product_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);

            $advance_product_items  = $this->common->advance_voucher_items_product_advance_list_field($id);
            $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

       // }


        $service_items               = $this->common->advance_voucher_items_service_list_field($id);
        $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items, $voucher_items_advance_product_items);
         $data['inventory_access'] = $data['access_common_settings'][0]->inventory_advanced;

        $item_types = $this->general_model->getRecords('item_type,item_description', 'advance_voucher_item', array(
            'advance_voucher_id' => $id));

        $service     = 0;
        $product     = 0;
        $description = 0;
        foreach ($item_types as $key => $value) {
            if ($value->item_description != "") {
                $description++;
            }
            if ($value->item_type == "service") {
                $service = 1;
            } else if ($value->item_type == "product") {
                $product = 1;
            } else if ($value->item_type == "product_inventory") {
                $product = 2;
            }
        }

        $data['product_exist'] = $product;
        $data['service_exist'] = $service;



        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;

        if ($data['data'][0]->voucher_tax_amount > 0 && $data['data'][0]->voucher_igst_amount > 0 && ($data['data'][0]->voucher_cgst_amount == 0 && $data['data'][0]->voucher_sgst_amount == 0)) {
            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->voucher_tax_amount > 0 && ($data['data'][0]->voucher_cgst_amount > 0 || $data['data'][0]->voucher_sgst_amount > 0) && $data['data'][0]->voucher_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->voucher_tax_amount > 0 && ($data['data'][0]->voucher_igst_amount == 0 && $data['data'][0]->voucher_cgst_amount == 0 && $data['data'][0]->voucher_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->voucher_tax_amount == 0 && ($data['data'][0]->voucher_igst_amount == 0 && $data['data'][0]->voucher_cgst_amount == 0 && $data['data'][0]->voucher_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->voucher_cess_amount > 0) {
            $cessExist = 1;
        }


        if ($data['data'][0]->voucher_tds_amount > 0 || $data['data'][0]->voucher_tds_amount > 0 || $data['access_settings'][0]->tds_visible == "yes") {
            /* Discount */
            $tdsExist = 1;
        }
        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes") {
            /* Discount */
            $descriptionExist = 1;
        }

        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);

        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']        = $cessExist;
        $data['is_utgst']           = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        /* bank default ledger title for payment mode*/
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $ledger_title = 'Acc@{{BANK}}';
        if(!empty($bank_led)){
            $ledger_title = $bank_led->ledger_name;
        }
        $data['default_ledger_title'] = $ledger_title;

        $this->load->view('advance_voucher/edit', $data);
    }

    public function edit_advance() {
        $data                              = $this->get_default_country_state();
        $advance_id                        = $this->input->post('advance_voucher_id');
        $advance_voucher_module_id = $this->config->item('advance_voucher_module');
        ;
        $data['module_id']                 = $advance_voucher_module_id;
        $modules                           = $this->modules;
        $privilege                         = "edit_privilege";
        $data['privilege']                 = "edit_privilege";
        $section_modules                   = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $advance_ledger = $this->config->item('advance_ledger');

        $product_module_id  = $data['product_module_id'] = $this->config->item('product_module');
        $service_module_id  = $data['service_module_id'] = $this->config->item('service_module');
        $customer_module_id = $data['customer_module_id'] = $this->config->item('customer_module');
        $category_module_id = $data['category_module_id'] = $this->config->item('category_module');
        $subcategory_module_id = $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $tax_module_id  = $data['tax_module_id'] = $this->config->item('tax_module');
        $discount_module_id = $data['discount_module_id'] = $this->config->item('discount_module');

        $accounts_module_id = $data['accounts_module_id'] = $this->config->item('accounts_module');
        $bank_account_module_id  = $data['bank_account_module_id'] = $this->config->item('bank_account_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $modules_present                   = array(
                'product_module_id'      => $product_module_id,
                'service_module_id'      => $service_module_id,
                'customer_module_id'     => $customer_module_id,
                'category_module_id'     => $category_module_id,
                'subcategory_module_id'  => $subcategory_module_id,
                'tax_module_id'          => $tax_module_id,
                'discount_module_id'     => $discount_module_id,
                'accounts_module_id'     => $accounts_module_id,
            'bank_account_module_id' => $bank_account_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $access_settings          = $data['access_settings'];
        $currency                          = $this->input->post('currency_id');
        if ($access_settings[0]->invoice_creation == "automatic") {
            if ($this->input->post('voucher_number') != $this->input->post('voucher_number_old')) {
                $primary_id      = "advance_id";
                $table_name      = 'advance_voucher';
                $date_field_name = "voucher_date";
                $current_date    = date('Y-m-d',strtotime($this->input->post('voucher_date')));
                $voucher_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else {
                $voucher_number = $this->input->post('voucher_number');
            }
        } else {
                $voucher_number = $this->input->post('voucher_number');
            }
        $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array('customer_id' => $this->input->post('customer')));
        $ledger_customer = $customer[0]->ledger_id;
        $customer_name = $customer[0]->customer_name;
        $customer_ledger_id = $customer[0]->ledger_id;

        if(!$customer_ledger_id){
            $default_customer_id = $advance_ledger['CUSTOMER'];
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
        /*if ($customer_name != '') {
            $customer_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                            'ledger_name' => $customer_name,
                                                            'subgrp_2' => 'Sundry Debtors',
                                                            'subgrp_1' => '',
                                                            'main_grp' => 'Current Assets',
                                                            'amount' => 0
                                                        ));
        }*/

        /*$ledger_igst     = $this->ledger_model->getDefaultLedger('IGST');
        $ledger_cgst     = $this->ledger_model->getDefaultLedger('CGST');
        $ledger_sgst     = $this->ledger_model->getDefaultLedger('SGST');*/
        if ($this->input->post('payment_mode') == "other payment mode") {
            $payment_via = $this->input->post('payment_via');
            $reff_number = $this->input->post('ref_number');
        } else {
            $payment_via = "";
            $reff_number = "";
        } 
        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode          = $bank_acc_payment_mode[0];
            $from_acc              = $bank_acc_payment_mode[1];
           
            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode));
            $from_ledger_id =  $ledger_bank_acc[0]->ledger_id;
            $ledger_from = $ledger_bank_acc[0]->ledger_id;

        } else {
            $payment_mode     = $this->input->post('payment_mode');
            $from_acc         = $this->input->post('payment_mode');
            /*$ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));*/
            $default_payment_id = $advance_ledger['Other_Payment'];
            if (strtolower($from_acc) == "cash"){
                $default_payment_id = $advance_ledger['Cash_Payment'];
            }

            $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
                    
            $default_payment_ary = array(
                            'ledger_name' => $from_acc,
                            'second_grp' => '',
                            'primary_grp' => 'Cash & Cash Equivalent',
                            'main_grp' => 'Current Assets',
                            'default_ledger_id' => 0,
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
            /*if ($from_acc != '') {
                        $from_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                                'ledger_name' => $from_acc,
                                                                        'subgrp_1' => '',
                                                                        'subgrp_2' => (strtolower($from_acc) == 'cash' ? 'Cash & Cash Equivalent' : ''),
                                                                        'main_grp' => 'Current Assets',
                                                                        'amount' => 0
                                                                    ));
            }*/
        }
        $cheque_date = ($this->input->post('cheque_date') != '' ? date('Y-m-d', strtotime($this->input->post('cheque_date'))) : '');
        if (!$cheque_date) {
            $cheque_date = null;
        }
       $round_off_plus = $this->input->post('round_off_plus');
       $round_off_minus = $this->input->post('round_off_minus');

        if ($round_off_plus > 0) {
            $round_off_amount = $round_off_plus;
        } elseif ($round_off_minus < 0) {
            $round_off_amount = $round_off_minus;
        } else {
            $round_off_amount = 0;
        }
        $sub_total = $this->input->post('total_sub_total');
        $receipt_amount_x =  $this->input->post('receipt_amount');
        $total_tax_amount = $this->input->post('total_tax_amount');

        $voucher_date = date('Y-m-d',strtotime($this->input->post('voucher_date')));

        $gst_payable = $this->input->post('gst_payable');
        $gst_payable = ($gst_payable) ? $gst_payable : 'no';
        if ($gst_payable != 'yes') {
            $customer_amount = $receipt_amount_x;
        } else {
            $customer_amount = bcsub($receipt_amount_x, $total_tax_amount,2);
        }
        $state_billing_id = $this->input->post('billing_state');
        $advance_data = array(
                "voucher_date"        => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
                "voucher_number"      => $voucher_number,
                "voucher_sub_total"   => $this->input->post('total_sub_total'),
                "receipt_amount"      => $this->input->post('receipt_amount'),
                "voucher_tax_amount"  => $this->input->post('total_tax_amount'),
                "description"         => $this->input->post('description'),
                "reference_id"        => "",
                "reference_number"    => "",
                "from_account"        => $from_acc,
                "to_account"          => 'customer-' . $customer[0]->customer_name,
                "payment_mode"        => $payment_mode,
                "bank_name"           => $this->input->post('bank_name'),
                "cheque_date"         => $cheque_date,
                "cheque_number"       => $this->input->post('cheque_number'),
                "payment_via"         => $payment_via,
                "ref_number"          => $reff_number,
                "voucher_igst_amount" => $this->input->post('total_igst_amount'),
                "voucher_cgst_amount" => $this->input->post('total_cgst_amount'),
                "voucher_sgst_amount" => $this->input->post('total_sgst_amount'),
                "voucher_cess_amount" => $this->input->post('total_tax_cess_amount'),
                "financial_year_id"   => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "party_id"            => $this->input->post('customer'),
                "party_type"          => "customer",
                "billing_country_id"  => $this->input->post('billing_country'),
                "billing_state_id"    => $this->input->post('billing_state'),
                "branch_id"           => $this->session->userdata('SESS_BRANCH_ID'),
                "currency_id"         => $this->input->post('currency_id'),
                "updated_date"        => date('Y-m-d'),
                "updated_user_id"     => $this->session->userdata('SESS_USER_ID'),
                "note1"               => $this->input->post('note1'),
                "note2"               => $this->input->post('note2'),
                "grand_total_without_roundoff" => $this->input->post('without_reound_off_grand_total'),
                "round_off_amount" => $round_off_amount,
            "gst_payable" => $gst_payable);
        if ($payment_mode == "cash") {
            $advance_data['voucher_status'] = "0";
        } else {
            $advance_data['voucher_status'] = "1";
        }

        $data_main             = array_map('trim', $advance_data);
        $advance_voucher_table = 'advance_voucher';
        $where = array('advance_voucher_id' => $advance_id);
        if ($this->general_model->updateData($advance_voucher_table, $data_main, $where)) {
            $successMsg = 'Advance Voucher Updated Successfully';
            $this->session->set_flashdata('advance_voucher_success',$successMsg);
            $log_data                = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $advance_id,
                    'table_name'        => $advance_voucher_table,
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Advance Voucher Updated');
            $data_main['advance_id'] = $advance_id;
            $log_table               = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $voucher_item_data       = $this->input->post('table_data');
            $js_data                 = json_decode($voucher_item_data);
            $advance_voucher_item_data = $this->input->post('table_data');
            $js_data                   = json_decode($advance_voucher_item_data);
            $item_table                = 'advance_voucher_item';
            $igst = array();
            $cgst = array();
            $sgst = array();
            $cess = array();
            $tds = array();
            $ledger_entry = array();

            $i = 1;
            $j = 1;
            foreach ($js_data as $key => $value) {
                if ($value != null) {

                   $item_data = array(
                            "item_id"              => $value->item_id,
                            "item_type"            => $value->item_type,
                            "item_quantity"         => $value->item_quantity,
                            "item_price"            => $value->item_price,
                            "item_sub_total"       => $value->item_sub_total,
                            "item_grand_total"     => $value->item_grand_total,
                            "item_gst_id"          => $value->item_tax_id,
                            "item_igst_percentage" => $value->item_percentage_igst,
                            "item_igst_amount"     => $value->item_tax_amount_igst,
                            "item_cgst_percentage" => $value->item_percentage_cgst,
                            "item_cgst_amount"     => $value->item_tax_amount_cgst,
                            "item_sgst_percentage" => $value->item_percentage_sgst,
                            "item_sgst_amount"     => $value->item_tax_amount_sgst,
                            "item_tax_percentage"  => $value->item_tax_percentage,
                        "item_tax_amount" => $value->item_tax_amount,
                            "item_cess_percentage" => $value->item_tax_cess_percentage,
                            "item_cess_amount"     => $value->item_tax_cess_amount,
                            "item_description"     => $value->item_description,
                            "item_cess_id"         => $value->item_tax_cess_id,
                            "item_tds_id"          => $value->item_tds_id,
                        "advance_voucher_id" => $advance_id);
                    $data_item[$j] = array_map('trim', $item_data);
                    $j = $j + 1;
                    /*                     * ******* GST TAX STARTS ******* */
                    if ($gst_payable != 'yes') {
                        if ($value->item_percentage_igst != '' && $value->item_percentage_igst != 0) {
                            $default_igst_id = $advance_ledger['IGST@X'];
                            $igst_x = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'Input IGST@'.(float)$value->item_percentage_igst.'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_percentage_igst,
                                            'amount' => 0
                                        );
                            if(!empty($igst_x)){
                                $igst_ledger = $igst_x->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_igst , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                $igst_ary['main_grp'] = $igst_x->main_group;
                                $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($igst[$igst_tax_ledger])) {
                                $igst[$igst_tax_ledger] = 0;
                            }
                            $igst[$igst_tax_ledger] += $value->item_tax_amount_igst;
                            if ($igst[$igst_tax_ledger] > 0) {
                            $ledger_entry[$igst_tax_ledger]["ledger_from"] = $igst_tax_ledger;
                            $ledger_entry[$igst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$igst_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$igst_tax_ledger]["voucher_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$igst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$igst_tax_ledger]["dr_amount"] = 0;
                            $ledger_entry[$igst_tax_ledger]["cr_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$igst_tax_ledger]['ledger_id'] = $igst_tax_ledger;

                            /* Advance paid starts */
                            $default_igst_paid_advance_id = $advance_ledger['IGST_paid_on_advance'];
                            $igst_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_igst_paid_advance_id);

                            $igst_advance_ary = array(
                                            'ledger_name'=>'IGST paid on advance',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($igst_advance_paid_x)){
                                $igst_advance_ledger = $igst_advance_paid_x->ledger_name;
                               // $igst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_igst , $igst_ledger);
                                $igst_advance_ary['ledger_name'] = $igst_advance_ledger;
                                $igst_advance_ary['primary_grp'] = $igst_advance_paid_x->sub_group_1;
                                $igst_advance_ary['second_grp'] = $igst_advance_paid_x->sub_group_2;
                                $igst_advance_ary['main_grp'] = $igst_advance_paid_x->main_group;
                                $igst_advance_ary['default_ledger_id'] = $igst_advance_paid_x->ledger_id;
                            }
                            $igst_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($igst_advance_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($igst_advance[$igst_tax_advance_ledger])) {
                                $igst_advance[$igst_tax_advance_ledger] = 0;
                            }
                            $igst_advance[$igst_tax_advance_ledger] += $value->item_tax_amount_igst;
                            $ledger_entry[$igst_tax_advance_ledger]["ledger_from"] = $igst_tax_advance_ledger;
                            $ledger_entry[$igst_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$igst_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$igst_tax_advance_ledger]["voucher_amount"] = $igst_advance[$igst_tax_advance_ledger];
                            $ledger_entry[$igst_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$igst_tax_advance_ledger]["dr_amount"] = $igst_advance[$igst_tax_advance_ledger];
                            $ledger_entry[$igst_tax_advance_ledger]["cr_amount"] = 0;
                            $ledger_entry[$igst_tax_advance_ledger]['ledger_id'] = $igst_tax_advance_ledger;

                            /* Advance paid ends */
                            $i = $i + 1;
                            }
                        }

                        if ($value->item_percentage_cgst != '' && $value->item_percentage_cgst != 0) {
                            $default_cgst_id = $advance_ledger['CGST@X'];
                            $cgst_x = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                            $cgst_ary = array(
                                            'ledger_name' => 'Input CGST@'.(float)$value->item_percentage_cgst.'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_percentage_cgst,
                                            'amount' => 0
                                        );
                            if(!empty($cgst_x)){
                                $cgst_ledger = $cgst_x->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_cgst , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_x->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                            }
                            /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);*/
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);

                            /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'CGST@' . (float)$value->item_percentage_cgst.'%',
                                                                            'subgrp_1' => 'CGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($cgst[$cgst_tax_ledger])) {
                                $cgst[$cgst_tax_ledger] = 0;
                            }

                            $cgst[$cgst_tax_ledger] += $value->item_tax_amount_cgst;
                            $ledger_entry[$cgst_tax_ledger]["ledger_from"] = $cgst_tax_ledger;
                            $ledger_entry[$cgst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cgst_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cgst_tax_ledger]["voucher_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$cgst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cgst_tax_ledger]["dr_amount"] = 0;
                            $ledger_entry[$cgst_tax_ledger]["cr_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$cgst_tax_ledger]['ledger_id'] = $cgst_tax_ledger;
                              /* Advance paid starts */
                            $default_cgst_paid_advance_id = $advance_ledger['CGST_paid_on_advance'];
                            $cgst_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_cgst_paid_advance_id);

                            $cgst_advance_ary = array(
                                            'ledger_name'=>'CGST paid on advance',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($cgst_advance_paid_x)){
                                $cgst_advance_ledger = $cgst_advance_paid_x->ledger_name;
                              //  $cgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_cgst , $igst_ledger);
                                $cgst_advance_ary['ledger_name'] = $cgst_advance_ledger;
                                $cgst_advance_ary['primary_grp'] = $cgst_advance_paid_x->sub_group_1;
                                $cgst_advance_ary['second_grp'] = $cgst_advance_paid_x->sub_group_2;
                                $cgst_advance_ary['main_grp'] = $cgst_advance_paid_x->main_group;
                                $cgst_advance_ary['default_ledger_id'] = $cgst_advance_paid_x->ledger_id;
                            }
                            $cgst_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($cgst_advance_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($cgst_advance[$cgst_tax_advance_ledger])) {
                                $cgst_advance[$cgst_tax_advance_ledger] = 0;
                            }
                            $cgst_advance[$cgst_tax_advance_ledger] += $value->item_tax_amount_cgst;
                            $ledger_entry[$cgst_tax_advance_ledger]["ledger_from"] = $cgst_tax_advance_ledger;
                            $ledger_entry[$cgst_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cgst_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cgst_tax_advance_ledger]["voucher_amount"] = $cgst_advance[$cgst_tax_advance_ledger];
                            $ledger_entry[$cgst_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cgst_tax_advance_ledger]["dr_amount"] = 0;
                            $ledger_entry[$cgst_tax_advance_ledger]["cr_amount"] = $cgst_advance[$cgst_tax_advance_ledger];
                            $ledger_entry[$cgst_tax_advance_ledger]['ledger_id'] = $cgst_tax_advance_ledger;

                            /* Advance paid ends */
                            $i = $i + 1;
                        }

                        if ($value->item_percentage_sgst != '' && $value->item_percentage_sgst != 0) {
                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($state_billing_id);
                            if ($is_utgst == '1')
                                $gst_lbl = 'UTGST';

                            $default_sgst_id = $advance_ledger[$gst_lbl.'@X'];
                            $sgst_x = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                            
                            $sgst_ary = array(
                                            'ledger_name' => 'Input '.$gst_lbl.'@'.(float)$value->item_percentage_sgst.'%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_percentage_sgst,
                                            'amount' => 0
                                        );
                            if(!empty($sgst_x)){
                                if($is_utgst != '1') {
                                    $sgst_ledger = $sgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_sgst , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                }else{
                                    $default_utgst_id = $advance_ledger['UTGST@X'];
                                    $utgst_x = $this->ledger_model->getDefaultLedgerId($default_utgst_id);
                                    $sgst_ledger = $utgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_sgst , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $utgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                }
                                
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);

                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => $gst_lbl . '@' . (float)$value->item_percentage_sgst.'%',
                                                                            'subgrp_1' => 'SGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($sgst[$sgst_tax_ledger])) {
                                $sgst[$sgst_tax_ledger] = 0;
                            }
                            $sgst[$sgst_tax_ledger] += $value->item_tax_amount_sgst;
                            $ledger_entry[$sgst_tax_ledger]["ledger_from"] = $sgst_tax_ledger;
                            $ledger_entry[$sgst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$sgst_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$sgst_tax_ledger]["voucher_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$sgst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$sgst_tax_ledger]["dr_amount"] = 0;
                            $ledger_entry[$sgst_tax_ledger]["cr_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$sgst_tax_ledger]['ledger_id'] = $sgst_tax_ledger;
                             /* Advance paid starts */
                            $default_sgst_paid_advance_id = $advance_ledger[$gst_lbl.'_paid_on_advance'];
                            $sgst_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_sgst_paid_advance_id);

                            $sgst_advance_ary = array(
                                            'ledger_name'=>$gst_lbl.' paid on advance',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($sgst_advance_paid_x)){
                                $sgst_advance_ledger = $sgst_advance_paid_x->ledger_name;
                               // $sgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_sgst , $sgst_ledger);
                                $sgst_advance_ary['ledger_name'] = $sgst_advance_ledger;
                                $sgst_advance_ary['primary_grp'] = $sgst_advance_paid_x->sub_group_1;
                                $sgst_advance_ary['second_grp'] = $sgst_advance_paid_x->sub_group_2;
                                $sgst_advance_ary['main_grp'] = $sgst_advance_paid_x->main_group;
                                $sgst_advance_ary['default_ledger_id'] = $sgst_advance_paid_x->ledger_id;
                            }
                            $sgst_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($sgst_advance_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_percentage_igst.'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($sgst_advance[$sgst_tax_advance_ledger])) {
                                $sgst_advance[$sgst_tax_advance_ledger] = 0;
                            }
                            $sgst_advance[$sgst_tax_advance_ledger] += $value->item_tax_amount_sgst;
                            $ledger_entry[$sgst_tax_advance_ledger]["ledger_from"] = $sgst_tax_advance_ledger;
                            $ledger_entry[$sgst_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$sgst_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$sgst_tax_advance_ledger]["voucher_amount"] = $sgst_advance[$sgst_tax_advance_ledger];
                            $ledger_entry[$sgst_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$sgst_tax_advance_ledger]["dr_amount"] = $sgst_advance[$sgst_tax_advance_ledger];
                            $ledger_entry[$sgst_tax_advance_ledger]["cr_amount"] = 0;
                            $ledger_entry[$sgst_tax_advance_ledger]['ledger_id'] = $sgst_tax_advance_ledger;

                            /* Advance paid ends */
                            $i = $i + 1;
                        }

                        /*                         * ******* GST TAX ENDS ******* */


                        /*                         * ******* CESS TAX STARTS ******* */

                        if ($value->item_tax_cess_percentage != '' && $value->item_tax_cess_percentage != 0) {
                            $default_cess_id = $advance_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                           
                            $cess_ary = array(
                                            'ledger_name' => 'Input Compensation Cess @'.(float)$value->item_tax_cess_percentage.'%',
                                            'second_grp' => 'Cess',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_tax_cess_percentage,
                                            'amount' => 0
                                        );
                            if(!empty($cess_ledger_name)){
                                $cess_ledger = $cess_ledger_name->ledger_name;
                                $cess_ledger = str_ireplace('{{X}}',(float)$value->item_tax_cess_percentage , $cess_ledger);
                                $cess_ary['ledger_name'] = $cess_ledger;
                                $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                                $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                                $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                                $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                            }
                            $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);

                            /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Cess@' . (float)$value->item_tax_cess_percentage.'%',
                                                                            'subgrp_1' => 'Cess',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (!isset($cess[$cess_tax_ledger])) {
                                $cess[$cess_tax_ledger] = 0;
                            }
                            $cess[$cess_tax_ledger] += $value->item_tax_cess_amount;
                            $ledger_entry[$cess_tax_ledger]["ledger_from"] = $cess_tax_ledger;
                            $ledger_entry[$cess_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cess_tax_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cess_tax_ledger]["voucher_amount"] =  $cess[$cess_tax_ledger];
                            $ledger_entry[$cess_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cess_tax_ledger]["dr_amount"] =  0;
                            $ledger_entry[$cess_tax_ledger]["cr_amount"] = $cess[$cess_tax_ledger];
                            $ledger_entry[$cess_tax_ledger]['ledger_id'] = $cess_tax_ledger;

                            /* Advance paid starts */
                            $default_cess_paid_advance_id = $advance_ledger['Cess_paid_on_advance'];
                            $cess_advance_paid_x = $this->ledger_model->getDefaultLedgerId($default_cess_paid_advance_id);

                            $cess_advance_ary = array(
                                            'ledger_name'=> 'Cess paid on advance',
                                            'second_grp' => '',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => 0,
                                            'amount' => 0
                                        );
                            if(!empty($cess_advance_paid_x)){
                                $cess_advance_ledger = $cess_advance_paid_x->ledger_name;
                                //$sgst_ledger = str_ireplace('{{X}}',(float)$value->item_percentage_sgst , $sgst_ledger);
                                $cess_advance_ary['ledger_name'] = $cess_advance_ledger;
                                $cess_advance_ary['primary_grp'] = $cess_advance_paid_x->sub_group_1;
                                $cess_advance_ary['second_grp'] = $cess_advance_paid_x->sub_group_2;
                                $cess_advance_ary['main_grp'] = $cess_advance_paid_x->main_group;
                                $cess_advance_ary['default_ledger_id'] = $cess_advance_paid_x->ledger_id;
                            }
                            $cess_tax_advance_ledger = $this->ledger_model->getGroupLedgerId($cess_advance_ary);

                            
                            if (!isset($cess_advance[$cess_tax_advance_ledger])) {
                                $cess_advance[$cess_tax_advance_ledger] = 0;
                            }
                            $cess_advance[$cess_tax_advance_ledger] += $value->item_tax_cess_amount;
                            $ledger_entry[$cess_tax_advance_ledger]["ledger_from"] = $cess_tax_advance_ledger;
                            $ledger_entry[$cess_tax_advance_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cess_tax_advance_ledger]["advance_voucher_id"] = $advance_id;
                            $ledger_entry[$cess_tax_advance_ledger]["voucher_amount"] = $cess_advance[$cess_tax_advance_ledger];
                            $ledger_entry[$cess_tax_advance_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cess_tax_advance_ledger]["dr_amount"] = $cess_advance[$cess_tax_advance_ledger];
                            $ledger_entry[$cess_tax_advance_ledger]["cr_amount"] = 0;
                            $ledger_entry[$cess_tax_advance_ledger]['ledger_id'] = $cess_tax_advance_ledger;

                            /* Advance paid ends */
                            $i = $i + 1;
                        }
                        /*                         * ******* CESS TAX ENDS ******* */
                    }
                }
            $i = $i + 1;
        }

        $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
        $ledger_entry[$from_ledger_id]["ledger_to"] = $customer_ledger_id;
        $ledger_entry[$from_ledger_id]["advance_voucher_id"] = $advance_id;
        $ledger_entry[$from_ledger_id]["voucher_amount"] = $customer_amount;
        $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
        $ledger_entry[$from_ledger_id]["dr_amount"] = $customer_amount;
        $ledger_entry[$from_ledger_id]["cr_amount"] = 0;
        $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;
         $i = $i + 1;
        /*$advance_ledger_id            = $this->ledger_model->getDefaultLedger('Advance');
            if ($advance_ledger_id == 0) {
            $advance_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'Advance',
                                                                    'subgrp_2' => '',
                                                                    'subgrp_1' => '',
                                                                    'main_grp' => 'Current Assets',
                                                                    'amount' => 0
                                                                ));
            }*/

        $ledger_entry[$customer_ledger_id]["ledger_from"] = $from_ledger_id;
        $ledger_entry[$customer_ledger_id]["ledger_to"] = $customer_ledger_id;
        $ledger_entry[$customer_ledger_id]["advance_voucher_id"] = $advance_id;
        $ledger_entry[$customer_ledger_id]["voucher_amount"] = $customer_amount; //$sub_total;
        $ledger_entry[$customer_ledger_id]["converted_voucher_amount"] = 0;
        $ledger_entry[$customer_ledger_id]["dr_amount"] = 0;
        $ledger_entry[$customer_ledger_id]["cr_amount"] = $customer_amount; //$sub_total;
        $ledger_entry[$customer_ledger_id]['ledger_id'] = $customer_ledger_id;
        $i = $i + 1;

        $round_off_plus =  $this->input->post('round_off_plus');
        $round_off_minus =  $this->input->post('round_off_minus');

        if ($round_off_plus > 0 || $round_off_minus > 0) {
            if ($round_off_plus > 0) {
                $round_off_amount = $round_off_plus;
                $dr_amount        = $round_off_plus;
                $cr_amount        = 0;

                $title     = "ROUND OFF";
                $subgroup  = "ROUND OFF";
                $ledger_to = $ledgers['ledger_from'];
                $default_roundoff_id = $advance_ledger['RoundOff_Given'];
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
                    'ledger_name' => 'ROUND OFF',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Expenses',
                    'amount' =>  0
                );*/
            } else {
                $round_off_amount = $round_off_minus;
                $dr_amount        = 0;
                $cr_amount        = $round_off_amount;

                $title     = "ROUND OFF";
                $subgroup  = "ROUND OFF";
                $default_roundoff_id = $advance_ledger['RoundOff_Received'];
                $roundoff_ledger_name = $this->ledger_model->getDefaultLedgerId($default_roundoff_id);
                $round_off_ary = array(
                                'ledger_name' => 'ROUND OFF Received',
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Indirect Incomes',
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );
                // $ledger_to = $ledgers['ledger_to'];
                /*$round_off_ary = array(
                    'ledger_name' => 'ROUND OFF',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Incomes',
                    'amount' =>  0
                );*/
            }
            if(!empty($roundoff_ledger_name)){
                $round_off_ary['ledger_name'] = $roundoff_ledger_name->ledger_name;
                $round_off_ary['primary_grp'] = $roundoff_ledger_name->sub_group_1;
                $round_off_ary['second_grp'] = $roundoff_ledger_name->sub_group_2;
                $round_off_ary['main_grp'] = $roundoff_ledger_name->main_group;
                $round_off_ary['default_ledger_id'] = $roundoff_ledger_name->ledger_id;
            }
            $round_off_ledger_id = $this->ledger_model->getGroupLedgerId($round_off_ary);
            $ledgers['round_off_ledger_id'] = $round_off_ledger_id;

            $ledger_entry[$round_off_ledger_id]["ledger_from"] = $round_off_ledger_id;
            $ledger_entry[$round_off_ledger_id]["ledger_to"] = $customer_ledger_id;
            $ledger_entry[$round_off_ledger_id]["advance_voucher_id"] =  '';
            $ledger_entry[$round_off_ledger_id]["voucher_amount"] = round($round_off_amount, 2);
            $ledger_entry[$round_off_ledger_id]["converted_voucher_amount"] = 0;
            $ledger_entry[$round_off_ledger_id]["dr_amount"] = round($dr_amount, 2);
            $ledger_entry[$round_off_ledger_id]["cr_amount"] = round($cr_amount, 2);
            $ledger_entry[$round_off_ledger_id]["ledger_id"] = $round_off_ledger_id;
            $i = $i + 1;
        }

        $old_voucher_items = $this->general_model->getRecords('*', 'accounts_advance_voucher', array('advance_voucher_id' => $advance_id, 'delete_status' => 0));
        /* echo "<pre>";
            print_r($old_voucher_items);
            print_r($vouchers);
          exit(); */
        $old_sales_ledger_ids = $this->getValues($old_voucher_items, 'advance_voucher_id');
            $not_deleted_ids = array();
            foreach ($ledger_entry as $key => $value) {
                if (($led_key = array_search($value['ledger_id'], $old_sales_ledger_ids)) !== false) {
                    unset($old_sales_ledger_ids[$led_key]);
                    $accounts_receipt_id = $old_voucher_items[$led_key]->accounts_receipt_id;
                array_push($not_deleted_ids, $accounts_receipt_id);
                    $value['advance_voucher_id'] = $advance_id;
                    $value['delete_status']    = 0;
                $where = array('advance_voucher_id' => $accounts_receipt_id);
                    $post_data = array('data' => $value,
                                        'where' => $where,
                                        'voucher_date' => $voucher_date,
                                        'table' => 'advance_voucher',
                                        'sub_table' => 'accounts_advance_voucher',
                                        'primary_id' => 'advance_voucher_id',
                                        'sub_primary_id' => 'advance_voucher_id'
                                    );
                    $this->general_model->updateBunchVoucherCommon($post_data);
                }
            }

        $tables = array('accounts_advance_voucher', $item_table);
        $this->db->where('advance_voucher_id', $advance_id);
        $this->db->delete($tables);
        $this->db->insert_batch('accounts_advance_voucher', $ledger_entry);
        $this->db->insert_batch($item_table, $data_item);

        redirect('advance_voucher', 'refresh');
        } else {
            $errorMsg = 'Advance Voucher Update Unsuccessful';
            $this->session->set_flashdata('advance_voucher_error',$errorMsg);
            redirect('advance_voucher', 'refresh');
        }
    }

    public function view($id) {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $advance_voucher_module_id       = $this->config->item('advance_voucher_module');
        $data['module_id']               = $advance_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);

        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        $email_sub_module_id             = $this->config->item('email_sub_module');


        $product_module_id               = $this->config->item('product_module');
        $service_module_id               = $this->config->item('service_module');
        $customer_module_id              = $this->config->item('customer_module');
        $bank_account_module_id          = $this->config->item('bank_account_module');
        $modules_present                 = array(
                'product_module_id'  => $product_module_id,
                'service_module_id'  => $service_module_id,
            'customer_module_id' => $customer_module_id);
        $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);
        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        }
        $advance_data = $this->common->advance_voucher_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($advance_data['string'], $advance_data['table'], $advance_data['where'], $advance_data['join']);
        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));


      /*  if ($inventory_access[0]->inventory_advanced == "yes")
        {
            $product_items               = $this->common->advance_voucher_items_product_inventory_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }
        else
        { */
            $product_items               = $this->common->advance_voucher_items_product_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
       // }

        $service_items               = $this->common->advance_voucher_items_service_list_field($id);
        $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

         $advance_product_items  = $this->common->advance_voucher_items_product_advance_list_field($id);
        $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items, $voucher_items_advance_product_items);
        $branch_data                 = $this->common->branch_field();
        $data['branch']              = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data                = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']             = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                  = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state']               = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data                   = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']                = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $igst                        = 0;
        $cgst                        = 0;
        $sgst                        = 0;
        $dpcount                     = 0;
        $dtcount                     = 0;
        $cess = 0;
        foreach ($data['items'] as $value) {
            $igst = bcadd($igst, $value->item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
            $cess = bcadd($cess, $value->item_cess_amount, 2);
            if ($value->item_description != "" && $value->item_description != null) {
                $dpcount++;
            }
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);
        $data['is_utgst']  = $is_utgst;
        $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['dpcount']  = $dpcount;
        $data['cess']  = $cess;
        $this->load->view('advance_voucher/view', $data);
    }

    public function pdf($id) {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $advance_voucher_module_id       = $this->config->item('advance_voucher_module');
        $data['module_id']               = $advance_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);

        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];


        $product_module_id               = $this->config->item('product_module');
        $service_module_id               = $this->config->item('service_module');
        $customer_module_id              = $this->config->item('customer_module');
        $data['notes_sub_module_id']     = $this->config->item('notes_sub_module');
        $modules_present                 = array(
                'product_module_id'  => $product_module_id,
                'service_module_id'  => $service_module_id,
            'customer_module_id' => $customer_module_id);
        $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);
        $advance_data                    = $this->common->advance_voucher_list_field1($id);
        $data['data']                    = $this->general_model->getJoinRecords($advance_data['string'], $advance_data['table'], $advance_data['where'], $advance_data['join']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));


            $product_items               = $this->common->advance_voucher_items_product_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


        $service_items               = $this->common->advance_voucher_items_service_list_field($id);
        $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
         $advance_product_items  = $this->common->advance_voucher_items_product_advance_list_field($id);
         $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items, $voucher_items_advance_product_items);
        $branch_data                 = $this->common->branch_field();
        $data['branch']              = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data                = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']             = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                  = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state']               = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data                   = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']                = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data['currency']            = $this->currency_call();
        $igst                        = 0;
        $cgst                        = 0;
        $sgst                        = 0;
        $dpcount                     = 0;
        $dtcount                     = 0;
        $cess = 0;
        foreach ($data['items'] as $value) {
            $igst = bcadd($igst, $value->item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
            $cess = bcadd($cess, $value->item_cess_amount, 2);
            if ($value->item_description != "" && $value->item_description != null) {
                $dpcount++;
            }
        }
        
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);
        $data['is_utgst']  = $is_utgst;
        $data['igst_tax']  = $igst;
        $data['cgst_tax']  = $cgst;
        $data['sgst_tax']  = $sgst;
        $data['cess_tax']  = $cess;
        $data['dpcount']   = $dpcount;
        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        ob_start();
        $html              = ob_get_clean();
        $html              = utf8_encode($html);

        $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                'module_id' => 2,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);
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

        $pdf_json            = $pdf[0]->pdf_settings;
        $rep                 = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);


        $html                           = $this->load->view('advance_voucher/pdf', $data, true);
        /*    include(APPPATH . 'third_party/mpdf60/mpdf.php');
        $mpdf                           = new mPDF();
        $mpdf->allow_charset_conversion = true;
        $mpdf->charset_in               = 'UTF-8';
        $mpdf->WriteHTML($html);
        $mpdf->Output($data['data'][0]->voucher_number . '.pdf', 'I'); */

         include(APPPATH . "third_party/dompdf/autoload.inc.php");
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();
        //we test first.
        //included.
        //now we can use all methods of dompdf
        //first im giving our html text to this method.
        $dompdf->load_html($html);

        $paper_size  = 'a4';
        $orientation = 'portrait';

        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size , $orientation);

        //and getting rend
        $dompdf->render();

        $dompdf->stream($data['data'][0]->voucher_number , array(
            'Attachment' => 0 ));
    }

    public function delete() {
        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        $advance_voucher_table           = 'advance_voucher';
        $advance_voucher_module_id       = $this->config->item('advance_voucher_module');
        $data['module_id']               = $advance_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data                        = array_merge($data, $section_modules);
        /*   $advance_data                    = $this->general_model->getRecords('reference_id,receipt_amount,currency_converted_amount', 'advance_voucher', array(
                'advance_id'    => $id,
                'delete_status' => 0 ));
        $sales_id                        = $advance_data[0]->reference_id;
        $receipt_amount                  = $advance_data[0]->receipt_amount;
          $conv_receipt_amount             = $advance_data[0]->currency_converted_amount; */
        // $this->general_model->updateData('advance_voucher_item', array(
        //         'delete_status' => 1 ), array(
        //         'advance_id' => $id ));

        if ($id != '') {
            $advance_voucher_res = $this->general_model->updateData('advance_voucher', array('delete_status' => 1), array('advance_voucher_id' => $id));
            if ($advance_voucher_res) {
                $this->general_model->deleteVoucher(array('advance_voucher_id' => $id), 'advance_voucher', 'accounts_advance_voucher');
        }

           /* if ($sales_id > 0)
            {
                $sdata                = $this->general_model->getRecords('sales_invoice_number,sales_paid_amount,converted_paid_amount', 'sales', array(
                        'sales_id'      => $sales_id,
                        'delete_status' => 0 ));
                $new_paid_amount      = bcsub($sdata[0]->sales_paid_amount, $receipt_amount, 2);
                $new_conv_paid_amount = bcsub($sdata[0]->converted_paid_amount, $conv_receipt_amount, 2);
                $update_sales         = array(
                        'sales_paid_amount'     => $new_paid_amount,
                        'converted_paid_amount' => $new_conv_paid_amount );
                $this->general_model->updateData('sales', $update_sales, array(
                        'sales_id' => $sales_id ));
            }*/ 
            $successMsg = 'Advance Voucher Deleted Successfully';
            $this->session->set_flashdata('advance_voucher_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'advance_voucher',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Advance Voucher Deleted' );
              $this->general_model->insertData('log', $log_data); 
            redirect('advance_voucher', 'refresh');
        } else {
            $errorMsg = 'Advance Voucher Delete Unsuccessful';
            $this->session->set_flashdata('advance_voucher_error',$errorMsg);
            redirect('advance_voucher', 'refresh');
        }
    }

    public function email($id) {
        $id                              = $this->encryption_url->decode($id);
        $advance_voucher_module_id       = $this->config->item('advance_voucher_module');
        $data['module_id']               = $advance_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];


        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $email_sub_module_id         = $this->config->item('email_sub_module');
        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        } $email_sub_module = 0;
        if (in_array($email_sub_module_id, $data['active_view'])) {
            ob_start();
            $html         = ob_get_clean();
            $html         = utf8_encode($html);
            $advance_data = $this->common->advance_voucher_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($advance_data['string'], $advance_data['table'], $advance_data['where'], $advance_data['join']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));



                $product_items               = $this->common->advance_voucher_items_product_list_field($id);
                $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


            $service_items               = $this->common->advance_voucher_items_service_list_field($id);
            $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items']               = array_merge($voucher_items_product_items, $voucher_items_service_items);
            $branch_data                 = $this->common->branch_field();
            $data['branch']              = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $country_data                = $this->common->country_field($data['branch'][0]->branch_country_id);
            $data['country']             = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data                  = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
            $data['state']               = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
            $city_data                   = $this->common->city_field($data['branch'][0]->branch_city_id);
            $data['city']                = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
            $data['currency']            = $this->currency_call();
            $igst                        = 0;
            $cgst                        = 0;
            $sgst                        = 0;
            $dpcount                     = 0;
            $dtcount                     = 0;
            $cess = 0;
            foreach ($data['items'] as $value) {
                $igst = bcadd($igst, $value->item_igst_amount, 2);
                $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
                $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
                $cess = bcadd($cess, $value->item_cess_amount, 2);
                if ($value->item_description != "" && $value->item_description != null) {
                    $dpcount++;
                }
            } $data['igst_tax']               = $igst;
            $data['cgst_tax']               = $cgst;
            $data['sgst_tax']               = $sgst;
             $data['cess_tax']  = $cess;
            $data['dpcount']                = $dpcount;
            $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);
            $data['is_utgst']  = $is_utgst;
            $note_data                      = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1']                  = $note_data['note1'];
            $data['template1']              = $note_data['template1'];
            $data['note2']                  = $note_data['note2'];
            $data['template2']              = $note_data['template2'];
            $html                           = $this->load->view('advance_voucher/pdf', $data, true);
           /* include(APPPATH . 'third_party/mpdf60/mpdf.php');
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $file_path                      = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $file_name                      = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F'); */

            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
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
            $advance_voucher_data           = $this->common->advance_voucher_list_field1($id);
            $data['data']                   = $this->general_model->getJoinRecords($advance_voucher_data['string'], $advance_voucher_data['table'], $advance_voucher_data['where'], $advance_voucher_data['join']);
            $branch_data                    = $this->common->branch_field();
            $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']            = $this->general_model->getRecords('*', 'email_setup', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template']         = $this->general_model->getRecords('*', 'email_template', array(
                    'module_id'     => $advance_voucher_module_id,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('advance_voucher/email', $data);
        } else {
            $this->load->view('advance_voucher', $data);
        }
    }

    public function get_advance_voucher() {
        $sid                  = $this->input->post('a_id');
        $data                 = $this->general_model->getRecords('sales_party_id', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $customer_id          = $data[0]->sales_party_id;
        $data['detail']       = $this->general_model->getRecords('*', 'advance_voucher', array(
                'party_id'      => $customer_id,
                'delete_status' => 0,
            'refund_status' => 0));
        $sales_invoice        = $this->general_model->getRecords('sales_grand_total', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $sales_paid           = $this->general_model->getRecords('sales_paid_amount', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $debit_amount         = $this->general_model->getRecords('debit_note_amount', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $credit_amount        = $this->general_model->getRecords('credit_note_amount', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $total_amount1        = bcsub($sales_invoice[0]->sales_grand_total, $sales_paid[0]->sales_paid_amount, 2);
        $total_amount2        = bcsub($total_amount1, floatval($debit_amount[0]->debit_note_amount), 2);
        $total_amount         = bcadd($total_amount2, floatval($credit_amount[0]->credit_note_amount), 2);
        $data['total_amount'] = $total_amount;
        echo json_encode($data);
    }

    public function show_advance_voucher($sales_id) {
        $sid                             = $this->encryption_url->decode($sales_id);
        $data                            = $this->general_model->getRecords('sales_party_id,sales_invoice_number,sales_paid_amount,currency_id', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $customer_id                     = $data[0]->sales_party_id;
        $currency_id                     = $data[0]->currency_id;
        $data['customer']                = $this->general_model->getRecords('customer_name', 'customer', array(
                'customer_id'   => $customer_id,
            'delete_status' => 0));
        $advance_voucher_module_id       = $this->config->item('advance_voucher_module');
        $data['module_id']               = $advance_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        } $data['detail'] = $this->general_model->getRecords('*', 'advance_voucher', array(
                'party_id'      => $customer_id,
                'currency_id'   => $currency_id,
                'delete_status' => 0,
            'refund_status' => 0));
        $sales_invoice  = $this->general_model->getRecords('sales_grand_total', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $sales_paid     = $this->general_model->getRecords('sales_paid_amount', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $debit_amount   = $this->general_model->getRecords('debit_note_amount', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $credit_amount  = $this->general_model->getRecords('credit_note_amount', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));
        $total_amount1  = bcsub($sales_invoice[0]->sales_grand_total, $sales_paid[0]->sales_paid_amount, 2);
        $total_amount2  = bcsub($total_amount1, floatval($debit_amount[0]->debit_note_amount), 2);
        $total_amount   = bcadd($total_amount2, floatval($credit_amount[0]->credit_note_amount), 2);
        if (!empty($this->input->post())) {
            $columns             = array(
                    0 => 'voucher_date',
                    1 => 'voucher_number',
                    2 => 'reference_number',
                    3 => 'receipt_amount',
                4 => 'action',);
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->advance_list_field($customer_id, $total_amount, $currency_id);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])) {
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
            } $send_data = array();
            if (!empty($posts)) {

                foreach ($posts as $post) {
                    $nestedData['voucher_date']     = date('Y-m-d',strtotime($post->voucher_date));
                    $nestedData['voucher_number']   = $post->voucher_number;
                    $nestedData['reference_number'] = $post->reference_number;
                    $nestedData['grand_total']      = $post->currency_symbol . ' ' . $post->receipt_amount;
                    $advance_id                     = $this->encryption_url->encode($post->advance_id);
                    $cols                           = '<td>';
                    if (($post->reference_number == "" || $post->reference_number == null)) {
                        if ($post->currency_converted_amount != 0.00) {
                            $cols .= '<a style="color:green" href="' . base_url('advance_voucher/activate_voucher_data/') . $advance_id . '/' . $sales_id . '">Connect</a>';
                            if ($data['access_module_privilege']->edit_privilege == "yes") {
                                $cols .= '<a href="' . base_url('advance_voucher/edit/') . $advance_id . '"><i class="fa fa-pencil text-blue"></i></a>';
                            }
                        }
                    } else {
                        $cols .= '<a style="color:red" href="' . base_url('advance_voucher/deactivate_voucher/') . $advance_id . '/' . $sales_id . '">Disconnect</a>';
                    } $cols .= '<a href="' . base_url('advance_voucher/pdf/') . $advance_id . '" target="_blank"><i class="fa fa-file-pdf-o text-blue"></i></a>';
                    if ($data['access_module_privilege']->delete_privilege == "yes") {
                        $cols .= '<a href="' . base_url('advance_voucher/delete_advance_voucher/') . $advance_id . '/' . $sales_id . '"><i class="fa fa-trash-o text-purple"></i></a>';
                    } $cols                 .= '</td>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $data['currency']             = $this->currency_call();
            $data['sales_invoice_number'] = $data[0]->sales_invoice_number;
            $data['sales_paid_amount']    = $data[0]->sales_paid_amount;
            $data['sales_id']             = $sales_id;
            $this->load->view('advance_voucher/advance_voucher_list', $data);
        }
    }

    public function activate_voucher_data($advance_id, $sales_id) {
        $advance_id = $this->encryption_url->decode($advance_id);
        $sid        = $this->encryption_url->decode($sales_id);

        $data = $this->general_model->getRecords('sales_invoice_number,sales_paid_amount,converted_paid_amount', 'sales', array(
                'sales_id'      => $sid,
            'delete_status' => 0));


        $advance_data    = array(
                'reference_id'     => $sid,
                'reference_type'   => 'sales',
            'reference_number' => $data[0]->sales_invoice_number);
        $this->general_model->updateData('advance_voucher', $advance_data, array(
            'advance_id' => $advance_id));
        $advance_receipt = $this->general_model->getRecords('receipt_amount,currency_converted_amount', 'advance_voucher', array(
                'advance_id'    => $advance_id,
            'delete_status' => 0));

        $update_amount     = bcadd($data[0]->sales_paid_amount, $advance_receipt[0]->receipt_amount, 2);
        $update_con_amount = bcadd($data[0]->converted_paid_amount, $advance_receipt[0]->currency_converted_amount, 2);

        $update_sales = array(
                'sales_paid_amount'     => $update_amount,
            'converted_paid_amount' => $update_con_amount);
        $this->general_model->updateData('sales', $update_sales, array(
            'sales_id' => $sid));
        redirect('advance_voucher/show_advance_voucher/' . $sales_id, 'refresh');
    }

    public function deactivate_voucher($advance_id, $sales_id) {
        $advance_id                = $this->encryption_url->decode($advance_id);
        $sid                       = $sales_id;
        $data                      = $this->general_model->getRecords('reference_id', 'advance_voucher', array(
                'advance_id'    => $advance_id,
            'delete_status' => 0));
        $sales_id                  = $data[0]->reference_id;
        $sdata                     = $this->general_model->getRecords('sales_invoice_number,sales_paid_amount,converted_paid_amount', 'sales', array(
                'sales_id'      => $sales_id,
            'delete_status' => 0));
        $advance_data              = array(
                'reference_id'     => null,
                'reference_type'   => null,
            'reference_number' => null);
        $this->general_model->updateData('advance_voucher', $advance_data, array(
            'advance_id' => $advance_id));
        $advance_receipt           = $this->general_model->getRecords('receipt_amount,currency_converted_amount', 'advance_voucher', array(
                'advance_id'    => $advance_id,
            'delete_status' => 0));
        $new_paid_amount           = bcsub($sdata[0]->sales_paid_amount, $advance_receipt[0]->receipt_amount, 2);
        $new_converted_paid_amount = bcsub($sdata[0]->converted_paid_amount, $advance_receipt[0]->currency_converted_amount, 2);

        $update_sales = array(
                'sales_paid_amount'     => $new_paid_amount,
            'converted_paid_amount' => $new_converted_paid_amount);
        $this->general_model->updateData('sales', $update_sales, array(
            'sales_id' => $sales_id));
        redirect('advance_voucher/show_advance_voucher/' . $sid, 'refresh');
    }

    public function delete_advance_voucher($advance_id, $sales_id) {
        $advance_id          = $this->encryption_url->decode($advance_id);
        $sid                 = $sales_id;
        $data                = $this->general_model->getRecords('reference_id,receipt_amount,currency_converted_amount', 'advance_voucher', array(
                'advance_id'    => $advance_id,
            'delete_status' => 0));
        $sales_id            = $data[0]->reference_id;
        $receipt_amount      = $data[0]->receipt_amount;
        $covn_receipt_amount = $data[0]->currency_converted_amount;
        $update_advance      = array(
            'delete_status' => 1);
        $this->general_model->updateData('advance_voucher', $update_advance, array(
            'advance_id' => $advance_id));
        $this->general_model->updateData('advance_voucher_item', $update_advance, array(
            'advance_id' => $advance_id));
        if ($sales_id != null || $sales_id != "") {
            $sdata                = $this->general_model->getRecords('sales_invoice_number,sales_paid_amount', 'sales', array(
                    'sales_id'      => $sales_id,
                'delete_status' => 0));
            $new_paid_amount      = bcsub($sdata[0]->sales_paid_amount, $receipt_amount, 2);
            $new_conv_paid_amount = bcsub($sdata[0]->converted_paid_amount, $covn_receipt_amount, 2);
            $update_sales         = array(
                    'sales_paid_amount'     => $new_paid_amount,
                'converted_paid_amount' => $new_conv_paid_amount);
            $this->general_model->updateData('sales', $update_sales, array(
                'sales_id' => $sales_id));
        }
        redirect('advance_voucher/show_advance_voucher/' . $sid, 'refresh');
    }

    public function convert_currency() {
        $id                   = $this->input->post('convert_currency_id');
        $id                   = $this->encryption_url->decode($id);
        $new_converted_amount = $this->input->post('currency_converted_amount');
        $new_converted_rate   = $this->input->post('convertion_rate');
        $converted_date = date('Y-m-d', strtotime($this->input->post('conversion_date')));
        
        $advance_receipt = $this->general_model->getRecords('reference_id,receipt_amount,converted_receipt_amount', 'advance_voucher', array(
            'advance_voucher_id'    => $id,
            'delete_status' => 0));

        if ($advance_receipt[0]->reference_id != 0 && $advance_receipt[0]->reference_id != null) {

            $sales_data = $this->general_model->getRecords('converted_paid_amount', 'sales', array(
                    'sales_id'      => $advance_receipt[0]->reference_id,
                'delete_status' => 0));

            echo $sales_data[0]->converted_paid_amount;
            $conv_amt          = bcsub($advance_receipt[0]->currency_converted_amount, $sales_data[0]->converted_paid_amount, 2);
            $update_con_amount = bcadd($conv_amt, $new_converted_amount, 2);

            $update_sales = array(
                'converted_paid_amount' => $update_con_amount);
            $this->general_model->updateData('sales', $update_sales, array(
                'sales_id' => $advance_receipt[0]->reference_id));
        }

        $data = array(
                'currency_converted_rate'   => $new_converted_rate,
            'conversion_date' => $converted_date,
            'converted_receipt_amount' => $this->input->post('currency_converted_amount'));
        $result = $this->general_model->updateData('advance_voucher', $data, array(
            'advance_voucher_id' => $id));
        //update converted_voucher_amount in  accounts_advance_voucher table
/*
        $accounts_advance_voucher = $this->general_model->getRecords('*', 'accounts_advance_voucher', array(
                'advance_voucher_id' => $id,
            'delete_status' => 0));

        foreach ($accounts_advance_voucher as $key1 => $value1) {

            $new_converted_voucher_amount = bcmul($accounts_advance_voucher[$key1]->voucher_amount, $new_converted_rate, 2);

            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount);
            $where                    = array(
                'accounts_advance_id' => $accounts_advance_voucher[$key1]->accounts_advance_id);
            $voucher_table            = "accounts_advance_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }*/

        redirect('advance_voucher', 'refresh');
    }

    function view_details($id) {
        $advance_voucher_id              = $this->encryption_url->decode($id);
        $advance_voucher_module_id       = $this->config->item('advance_voucher_module');
        /*$data['module_id']               = $advance_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $email_sub_module_id             = $this->config->item('email_sub_module');
       foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        }*/

        $data['advance_voucher_module_id'] = $advance_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $voucher_details = $this->common->advance_voucher_details($advance_voucher_id);
        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
        
        $this->load->view('advance_voucher/view_details', $data);
    }

    public function get_advance_sales($id) {
      //  $id = $this->input->post('id');
        $id = $this->encryption_url->decode($id);
        $list_data = $this->common->sales_voucher_list_advance_field($id);
        $list_data['search'] = 'all';
        $post = $this->general_model->getPageJoinRecords($list_data);
        $columns = array(
                0 => 'customer',
                1 => 'advance_voucher',
                2 => 'advance_voucher_amount',
                3 => 'unadjusted_amount',
                4 => 'adjusted_amount',
                5 => 'reference_invoice',
                6 => 'action',
            );
        $nestedData = array();
        if (!empty($post)) {
            $receipt_amount = $post[0]->receipt_amount;
            $adjusted_amount_av = $post[0]->adjusted_amount;
            $sales_paid_amount = $post[0]->sales_paid_amount;
            $sales_grand_total = $post[0]->sales_grand_total;
            $adjusted_amount = $this->precise_amount(($sales_grand_total - $sales_paid_amount), 2);
            $unadjusted_amount = $this->precise_amount(($receipt_amount - $adjusted_amount_av), 2);
            $customer_id = $post[0]->customer_id;
            $nestedData['customer'] = $post[0]->customer_name . '<input type="hidden" name="customer_id" id="customer_id" class="form-control disable_in" value="' . $customer_id . '" style="width: 140px"/>';
            $nestedData['advance_voucher'] =  $post[0]->voucher_number;
            $nestedData['advance_voucher_amount'] = $this->precise_amount($post[0]->receipt_amount, 2);

            $nestedData['adjusted_amount'] =  '<input type="number" name="adj_amount" data-id="1" class="form-control adj_amount disable_in" value="0" style="width: 140px"/><input type="hidden" name="adj_amount_hidden" data-id="1" class="adj_amount_hidden" value="0"/>';
            $nestedData['reference_invoice'] =  '<select class="form-control disable_in sales_inv" name="reference_invoice" data-id="1" style="width: 140px"><option value="">Select Invoice</option>';

            $array_dropdown = array();
            foreach ($post as $value) {
                $sales_paid_amount1 = $value->sales_paid_amount;
                $sales_grand_total1 = $value->sales_grand_total;
                $adjusted_amount1 = $sales_grand_total1 - $sales_paid_amount1;
                if ($adjusted_amount1 > 0) {
                    $nestedData['reference_invoice'] .= '<option value="' . $value->sales_id . '">' . $value->sales_invoice_number . '</option>';
                    $array_dropdown[] = array('sales_id' => $value->sales_id, 'invoice_number' => $value->sales_invoice_number);
              }
            }
           $array_dropdown = htmlspecialchars(json_encode($array_dropdown));
            $nestedData['unadjusted_amount'] = $unadjusted_amount . '<input type="hidden" name="hidden_unadjusted" id="hidden_unadjusted" class="form-control disable_in" value="' . $unadjusted_amount . '" style="width: 140px"/> <input type="hidden" name="invoice_array" id="invoice_array" value="' . $array_dropdown . '"><input type="hidden" name="advance_id" id="advance_id" value="' . $id . '">';

            $nestedData['reference_invoice'] .=  '</select><br><span name="orginal_value" data-id="1"></span>';
            $nestedData['action']        =  '<a href="JavaScript:void(0);" class="btn btn-info btn-xs edit_cell" data-id="1"><i class="fa fa-pencil"></i></a> | <a class="btn btn-info btn-xs save_cell" href="JavaScript:void(0);" data-id="1"><i class="fa fa-save"></i></a> | <a href="JavaScript:void(0);" class="btn btn-info btn-xs" id="add_row"><i class="fa fa-plus"></i></a> ';
        }
        $temp = array();
        
        if(!empty($nestedData)){
            $temp[0] = $nestedData;
             $totalData = 1; 
         }else{
              $totalData = 0;
         }
     
       $totalFiltered = 10;
       $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $temp);
            echo json_encode($json_data);
       // echo json_encode($nestedData);
    }

    public function get_invoice_amount($inv_id) {
        $sales_id = $this->input->post('inv_id');
        $invoice  = $this->general_model->getJoinRecords('*', 'sales s', array('s.sales_id' =>  $sales_id), array('customer c' =>  's.sales_party_id = c.customer_id'));
        $sales_grand_total = $invoice[0]->sales_grand_total;
        $sales_paid_amount = $invoice[0]->sales_paid_amount;
        $customer_name = $invoice[0]->customer_name;
        $customer_ledger_id = $invoice[0]->ledger_id;
        $sales_id = $invoice[0]->sales_id;

        $advance_ledger = $this->config->item('advance_ledger');

        /* get customer ledger ID */
        if(!$customer_ledger_id){
            $default_customer_id = $advance_ledger['CUSTOMER'];
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

        $this->db->select('voucher_amount,voucher_type');
        $this->db->from('sales s');
        $this->db->join('sales_voucher sv', 's.sales_id=sv.reference_id', 'left');
        $this->db->join('accounts_sales_voucher a', 'sv.sales_voucher_id=a.sales_voucher_id', 'left');
        $this->db->where('s.sales_id', $sales_id);
        $this->db->where('sv.delete_status', 0);
        $this->db->where('sv.reference_type', 'sales');
        $this->db->where('a.ledger_id', $customer_ledger_id);

                    $get_customer_qry = $this->db->get();
                    $sales_ledgers = $get_customer_qry->result();

                    $this->db->select('voucher_amount,voucher_type');
                    $this->db->from('sales_credit_note s');
        $this->db->join('sales_voucher sv', 's.sales_credit_note_id=sv.reference_id', 'left');
        $this->db->join('accounts_sales_voucher a', 'sv.sales_voucher_id=a.sales_voucher_id', 'left');
        $this->db->where('s.sales_id', $sales_id);
        $this->db->where('sv.delete_status', 0);
        $this->db->where('sv.reference_type', 'sales_credit_note');
        $this->db->where('a.ledger_id', $customer_ledger_id);

        $get_customer_qry = $this->db->get();
        $sales_credit_ledgers = $get_customer_qry->result();

        $this->db->select('voucher_amount,voucher_type,reference_type');
        $this->db->from('sales_debit_note s');
        $this->db->join('sales_voucher sv', 's.sales_debit_note_id=sv.reference_id', 'left');
        $this->db->join('accounts_sales_voucher a', 'sv.sales_voucher_id=a.sales_voucher_id', 'left');
        $this->db->where('s.sales_id', $sales_id);
        $this->db->where('sv.delete_status', 0);
        $this->db->where('sv.reference_type', 'sales_debit_note');
        $this->db->where('a.ledger_id', $customer_ledger_id);

        $get_customer_qry = $this->db->get();
        /* print_r($this->db->last_query()); */
        $sales_debit_ledgers = $get_customer_qry->result();

                    /* calculate total receiable and net recevable */
        $net_receivable = 0;
        
        if (!empty($sales_ledgers)) {
            foreach ($sales_ledgers as $k => $led) {
                $net_receivable += $led->voucher_amount;
            }
        }
        
        if (!empty($sales_credit_ledgers)) {
            foreach ($sales_credit_ledgers as $key => $led) {
                $net_receivable -= $led->voucher_amount;
            }
        }

        if (!empty($sales_debit_ledgers)) {
            /* print_r($sales_debit_ledgers); */
            foreach ($sales_debit_ledgers as $key => $led) {
                $net_receivable += $led->voucher_amount;
            }
        }



       // $adjusted_amount = $sales_grand_total - $sales_paid_amount;
        $adjusted_amount =  $net_receivable - $sales_paid_amount;
        $remaiming = array('adjusted_amount' => $adjusted_amount);

        echo json_encode($remaiming);
    }

    public function update_advance_sales() {
        $advance_voucher_id = $this->input->post('advance_id');
        $advance_amount = $this->input->post('advance_amount');
        $valueJSON = $this->input->post('valueJSON');
        $customer_id  = $this->input->post('customer_id');
        $sales_array = json_decode($valueJSON);
        $sales_array = (array) $sales_array;
        /* $advance  = $this->general_model->getRecords('*', 'advance_voucher', array('advance_voucher_id' =>  $advance_voucher_id));
         $adjusted_amount_old = $advance[0]->adjusted_amount;
         $advance_amount = $advance_amount + $adjusted_amount_old;*/
        foreach ($sales_array as $value) {
           $sales_id = $value->id;
           $sales_amount = $value->amount;
           /*$sales_update[] = array(
                'sales_id' => $sales_id,
                'sales_paid_amount' => $sales_amount,
            ); */

           $this->db->query("UPDATE sales SET sales_paid_amount = sales_paid_amount + $sales_amount WHERE  sales_id = $sales_id");

           

           $advance_history[] = array(
                'advance_voucher_id' => $advance_voucher_id,
                'sales_id' => $sales_id,
                'customer_id' => $customer_id,
                'adjusted_amount' => $sales_amount,
            );
        }
        $res_his = $this->db->insert_batch('advance_paid_history', $advance_history);
    if ($res_his) {
        $this->db->query("UPDATE advance_voucher SET adjusted_amount = adjusted_amount + $advance_amount WHERE  advance_voucher_id = $advance_voucher_id") ;
          /*  $res = $this->db->update_batch('sales', $sales_update, 'sales_id');
            if ($res) {
                $this->db->where('advance_voucher_id', $advance_voucher_id);
                $this->db->update('advance_voucher', array('adjusted_amount' => $advance_amount));
            } */
        }
        $array = array('success');
        echo json_encode($array);
    }

    public function email_popup($id) {
        $id                              = $this->encryption_url->decode($id);
        $advance_voucher_module_id       = $this->config->item('advance_voucher_module');
        $data['module_id']               = $advance_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];


        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $email_sub_module_id         = $this->config->item('email_sub_module');
        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        } $email_sub_module = 0;

            ob_start();
            $html         = ob_get_clean();
            $html         = utf8_encode($html);
            $advance_data = $this->common->advance_voucher_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($advance_data['string'], $advance_data['table'], $advance_data['where'], $advance_data['join']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));



                $product_items               = $this->common->advance_voucher_items_product_list_field($id);
                $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


            $service_items = $this->common->advance_voucher_items_service_list_field($id);
            $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $advance_product_items  = $this->common->advance_voucher_items_product_advance_list_field($id);
         $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items, $voucher_items_advance_product_items);
            $branch_data                 = $this->common->branch_field();
            $data['branch']              = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $country_data                = $this->common->country_field($data['branch'][0]->branch_country_id);
            $data['country']             = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data                  = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
            $data['state']               = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
            $city_data                   = $this->common->city_field($data['branch'][0]->branch_city_id);
            $data['city']                = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
            $data['currency']            = $this->currency_call();
            $igst                        = 0;
            $cgst                        = 0;
            $sgst                        = 0;
            $dpcount                     = 0;
            $dtcount                     = 0;
            $cess = 0;
        foreach ($data['items'] as $value) {
                $igst = bcadd($igst, $value->item_igst_amount, 2);
                $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
                $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
                $cess = bcadd($cess, $value->item_cess_amount, 2);
            if ($value->item_description != "" && $value->item_description != null) {
                    $dpcount++;
                }
            } $data['igst_tax']               = $igst;
            $data['cgst_tax']               = $cgst;
            $data['sgst_tax']               = $sgst;
             $data['cess_tax']  = $cess;
            $data['dpcount']                = $dpcount;
            $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);
            $data['is_utgst']  = $is_utgst;
            $note_data                      = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1']                  = $note_data['note1'];
            $data['template1']              = $note_data['template1'];
            $data['note2']                  = $note_data['note2'];
            $data['template2']              = $note_data['template2'];
            $html                           = $this->load->view('advance_voucher/pdf', $data, true);
           /* include(APPPATH . 'third_party/mpdf60/mpdf.php');
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $file_path                      = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $file_name                      = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F'); */

            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
            $dompdf = new Dompdf\Dompdf();

            $paper_size  = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);




            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';
            $advance_voucher_data           = $this->common->advance_voucher_list_field1($id);
            $data['data']                   = $this->general_model->getJoinRecords($advance_voucher_data['string'], $advance_voucher_data['table'], $advance_voucher_data['where'], $advance_voucher_data['join']);
            $branch_data                    = $this->common->branch_field();
            $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']            = $this->general_model->getRecords('*', 'email_setup', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template']         = $this->general_model->getRecords('*', 'email_template', array(
                    'module_id'     => $advance_voucher_module_id,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));

            $data['data'][0]->pdf_file_path = $data['pdf_file_path'];
            $data['data'][0]->pdf_file_name = $data['pdf_file_name'];
            $data['data'][0]->email_template = $data['email_template'];
            $data['data'][0]->firm_name = $data['branch'][0]->firm_name;
            $result = json_encode($data['data']);
            echo $result;
    }

    public function addAdvaceProduct($product_name) {

        $product_name = trim($product_name);

      $query = $this->db->select('product_id')->from('advance_products')->where('branch_id', $this->session->userdata('SESS_BRANCH_ID'))->where('LOWER(product_name)', strtolower($product_name), 'none')->get();

        if ($query->num_rows() > 0) {
          return $query->row()->product_id;
        } else {

           $query_count = $this->db->select('product_id')->from('advance_products')->where('branch_id', $this->session->userdata('SESS_BRANCH_ID'))->get();
           $num = $query_count->num_rows();
           $num = $num + 1;
            $product_code = 'ADP-000' . $num;

           $product_data = array(
            "product_code"           => $product_code,
            "product_name"           => $product_name,
            "product_model_no"       => '',
            "product_color"          => '',
            "product_hsn_sac_code"   => '',
            "product_category_id"    => '',
            "product_subcategory_id" => '',
            "product_quantity"       => 1,
            "product_unit"           => '',
            "product_price"          => 0,
            "product_tax_id"         => '',
            "product_tax_value"      => 0,
            "product_type"           => 'advance',
            "added_date"             => date('Y-m-d'),
            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID'));

            $this->db->insert('advance_products', $product_data);
            $id  = $this->db->insert_id();
            $this->db->insert('log', array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'advance_products',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Ledger created'));
            return $id;
        }
    }

}
