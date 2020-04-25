<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Refund_voucher extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }

    public function index() {
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /*  $data['access_modules'] = $section_modules['modules'];
          $data['access_sub_modules'] = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege'] = $section_modules['user_privilege'];
          $data['access_settings'] = $section_modules['settings'];
          $data['access_common_settings'] = $section_modules['common_settings']; */
        $email_sub_module_id = $this->config->item('email_sub_module');
        $data['email_module_id']           = $this->config->item('email_module');
        $data['email_sub_module_id']       = $email_sub_module_id;
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'rv.refund_id',
                1 => 'rv.voucher_date',
                2 => 'rv.reference_number',
                3 => 'rv.to_account',
                4 => 'c.customer_name',
                5 => 'rv.receipt_amount');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->refund_voucher_list_field($order, $dir);
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
                    $refund_id = $this->encryption_url->encode($post->refund_id);

                    $nestedData['voucher_date'] = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = '<a href="' . base_url('refund_voucher/view/') . $refund_id . '">' . $post->voucher_number . '</a>';
                    $nestedData['customer'] = $post->customer_name;
                    $nestedData['reference_number'] = $post->reference_number;
                    $nestedData['amount'] = $post->currency_symbol . ' ' . $this->precise_amount($post->receipt_amount, 2);
                    $nestedData['currency_converted_amount'] = '';
                    $nestedData['to_account'] = $post->to_account;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;


                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($refund_voucher_module_id, $data['active_view'])){
                        $cols .= '<span><a href="' . base_url('refund_voucher/view/') . $refund_id . '" class="btn btn-app" data-toggle="tooltip" title="View Refund Voucher" data-placement="bottom">
                                    <i class="fa fa-eye"></i>
                            </a></span>';
                        $cols .= '<span><a href="' . base_url('refund_voucher/pdf/') . $refund_id . '" class="btn btn-app pdf_button" target="_blank" data-name="regular" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }

                    if (in_array($refund_voucher_module_id, $data['active_edit']) && $post->voucher_status != "2") {
                        $cols .= '<span><a href="' . base_url('refund_voucher/edit/') . $refund_id . '" class="btn btn-app" data-toggle="tooltip" title="Edit Refund Voucher" data-placement="bottom"><i class="fa fa-pencil"></i>
                            </a></span>';
                    }

                    $email_sub_module = 0;
                    $email_sub_module = 1;
                    if ($email_sub_module == 1) {
                        if (in_array($data['email_module_id'] , $data['active_view']))
                        {
                            if (in_array($data['email_sub_module_id'] , $data['access_sub_modules']))
                            {
                                $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail"><a  class="btn btn-app pdf_button composeMail" data-id="' . $refund_id . '" data-name="regular" href="#" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Email Refund Voucher">
                                    <i class="fa fa-envelope-o"></i></a></span>';
                            }
                        }
                    }

                    /* if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY') && $post->voucher_status != "2") {
                      $cols .= '<li>               <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $refund_id . '" data-path="refund_voucher/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->receipt_amount . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>            </li>';
                      } */
                    if (in_array($refund_voucher_module_id, $data['active_delete'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal">
                           <a href="#" class="btn btn-app delete_button"  data-id="' . $refund_id . '" data-path="refund_voucher/delete" href="#" data-placement="bottom" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" data-placement="bottom" data-toggle="tooltip"   title="Delete Refund Voucher"><i class="fa fa-trash-o"></i></a></span>';
                    }

                    $cols .= '</div>';
                    $cols .= '</div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal" value="' . $post->refund_id . '">';
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view('refund_voucher/list', $data);
        }
    }
    
    public function add() {
        $data = $this->get_default_country_state();
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
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

        $access_common_settings = $section_modules['access_common_settings'];
        /*  $data['access_modules'] = $section_modules['modules'];
          $data['access_sub_modules'] = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege'] = $section_modules['user_privilege'];
          $data['access_settings'] = $section_modules['settings'];
          $data['access_common_settings'] = $section_modules['common_settings']; */
        $data['access_common_settings'] = $section_modules['access_common_settings'];
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
        } $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $bank_account_module_id = $this->config->item('bank_account_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'customer_module_id' => $customer_module_id,
            'bank_account_module_id' => $bank_account_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $data['customer'] = $this->customer_call();
        $data['currency'] = $this->currency_call();
        $data['bank_account'] = $this->bank_account_call_new();
        $access_settings = $data['access_settings'];
        $primary_id = "refund_id";
        $table_name = 'refund_voucher';
        $date_field_name = "voucher_date";
        $current_date = date('Y-m-d');
        $data['voucher_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('refund_voucher/add', $data);
    }

    public function get_advance_invoice() {
        $customer_id = $this->input->post('customer_id');
        $string = 'voucher_number';
        $table = 'advance_voucher';
        $where = array(
            'party_id' => $customer_id,
            'delete_status' => 0,
            'refund_status' => 0);
        $data = $this->general_model->getRecords($string, $table, $where, $order = "");
        echo json_encode($data);
    }

    public function get_advance_items() {
        $data = $this->get_default_country_state();
        $invoice = $this->input->post('invoice');
        $string = 'advance_voucher_id,currency_id';
        $table = 'advance_voucher';
        $where = array(
            'voucher_number' => $invoice,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $advance_data = $this->general_model->getRecords($string, $table, $where, $order = "");
      
        $data['advance_id'] = $advance_data[0]->advance_voucher_id;
        $advance_id = $advance_data[0]->advance_voucher_id;
        $string = 'receipt_amount';
        $where = array(
            'voucher_number' => $invoice,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $data['receipt_amount'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        $data['currency'] = $this->general_model->getRecords('currency_id,currency_name', 'currency', array(
            'currency_id' => $advance_data[0]->currency_id), $order = "");
        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));



        $product_items = $this->common->advance_voucher_items_product_list_field($advance_id);
        $advance_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


        $service_items = $this->common->advance_voucher_items_service_list_field($advance_id);
        $advance_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

        $advance_product_items = $this->common->advance_voucher_items_product_advance_list_field($advance_id);
        $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $data['items'] = array_merge($advance_items_product_items, $advance_items_service_items, $voucher_items_advance_product_items);
        $branch_details = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id'] = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id'] = $branch_details['branch'][0]->branch_id;
        $data['tax'] = $this->tax_call();
        echo json_encode($data);
    }

    function add_refund() {
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /*
          $data['access_modules'] = $section_modules['modules'];
          $data['access_sub_modules'] = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege'] = $section_modules['user_privilege'];
          $data['access_settings'] = $section_modules['settings']; */
        $data['access_common_settings'] = $section_modules['access_common_settings'];
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $modules_present = array(
            'accounts_module_id' => $this->config->item('accounts_module'));
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $access_settings = $data['access_settings'];
        $currency = $this->input->post('currency_id');
        if ($access_settings[0]->invoice_creation == "automatic") {
            $primary_id = "refund_id";
            $table_name = 'refund_voucher';
            $date_field_name = "voucher_date";
            $current_date = date('Y-m-d',strtotime($this->input->post('voucher_date')));
            $voucher_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $voucher_number = $this->input->post('voucher_number');
        }
        $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array('customer_id' => $this->input->post('customer')));
        $ledger_customer = $customer[0]->ledger_id;
        $customer_name = $customer[0]->customer_name;
        $customer_ledger_id = $customer[0]->ledger_id;
        $refund_ledger = $this->config->item('refund_ledger');
        if(!$customer_ledger_id){
            $default_customer_id = $refund_ledger['CUSTOMER'];
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

        $ledger_customer = $customer[0]->ledger_id;
        $advance_voucher_id = $this->input->post('advance_voucher_id');
        $refund_amount = $this->input->post('receipt_amount');

        if ($this->input->post('payment_mode') == "other payment mode") {
            $payment_via = $this->input->post('payment_via');
            $reff_number = $this->input->post('ref_number');
        } else {
            $payment_via = "";
            $reff_number = "";
        }

        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode = $bank_acc_payment_mode[0];
            $from_acc = $bank_acc_payment_mode[1];

            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode));
            $from_ledger_id =  $ledger_bank_acc[0]->ledger_id;
            $ledger_from = $ledger_bank_acc[0]->ledger_id;
        } else {
            $payment_mode = $this->input->post('payment_mode');
            $from_acc = $this->input->post('payment_mode');
            $ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));

            if ($from_acc != '') {
                $default_refund_id = $refund_ledger['Other_Payment'];
                if (strtolower($from_acc) == "cash"){
                    $default_refund_id = $refund_ledger['Cash_Payment'];
                }

                $default_refund_name = $this->ledger_model->getDefaultLedgerId($default_refund_id);
                        
                $default_refund_ary = array(
                                'ledger_name' => strtolower($from_acc),
                                'second_grp' => '',
                                'primary_grp' => 'Cash & Cash Equivalent',
                                'main_grp' => 'Current Assets',
                                'default_value' => strtolower($from_acc),
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );
                if(!empty($default_refund_name)){
                    $default_led_nm = $default_refund_name->ledger_name;
                    $default_refund_ary['ledger_name'] = str_ireplace('{{PAYMENT_MODE}}',strtolower($from_acc), $default_led_nm);  
                    $default_refund_ary['primary_grp'] = $default_refund_name->sub_group_1;
                    $default_refund_ary['second_grp'] = $default_refund_name->sub_group_2;
                    $default_refund_ary['main_grp'] = $default_refund_name->main_group;
                    $default_refund_ary['default_ledger_id'] = $default_refund_name->ledger_id;
                }
                $from_ledger_id = $this->ledger_model->getGroupLedgerId($default_refund_ary);
                /*$from_ledger_id = $this->ledger_model->addGroupLedger(array(
                    'ledger_name' => $from_acc,
                    'subgrp_1' => '',
                    'subgrp_2' => (strtolower($from_acc) == 'cash' ? 'Cash & Cash Equivalent' : ''),
                    'main_grp' => 'Current Assets',
                    'amount' => 0
                ));*/
            }
        }
       // $cheque_date = date('Y-m-d',strtotime($this->input->post('cheque_date')));
        $cheque_date = ($this->input->post('cheque_date') != '' ? date('Y-m-d', strtotime($this->input->post('cheque_date'))) : '');
        if (!$cheque_date) {
            $cheque_date = null;
        }



        $total_tax_amount = $this->input->post('total_tax_amount');

        $receipt_amount_x = $this->input->post('receipt_amount');
        $sub_total = $this->input->post('total_sub_total');
        $voucher_date = date('Y-m-d',strtotime($this->input->post('voucher_date')));
        $state_billing_id = $this->input->post('billing_state');
        $gst_payable = $this->input->post('gst_payable');
        $gst_payable = ($gst_payable) ? $gst_payable : 'no';
        if ($gst_payable != 'yes') {
            $customer_amount = $receipt_amount_x;
        } else {
            $customer_amount = bcsub($receipt_amount_x, $total_tax_amount, 2);
        }
        $refund_data = array(
            "voucher_date" => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
            "voucher_number" => $voucher_number,
            "voucher_sub_total" => $this->input->post('total_sub_total'),
            "receipt_amount" => $this->input->post('receipt_amount'),
            "voucher_tax_amount" => $this->input->post('total_tax_amount'),
            "description" => $this->input->post('description'),
            "reference_id" => $this->input->post('advance_voucher_id'),
            "reference_number" => $this->input->post('advance_voucher_number'),
            "reference_type" => "advance",
            "from_account" => 'customer-' . $customer[0]->customer_name,
            "to_account" => $from_acc,
            "payment_mode" => $payment_mode,
            "bank_name" => $this->input->post('bank_name'),
            "payment_via" => $this->input->post('payment_via'),
            "ref_number" => $this->input->post('ref_number'),
            "cheque_date" => $cheque_date,
            "cheque_number" => $this->input->post('cheque_number'),
            "voucher_igst_amount" => $this->input->post('total_igst_amount'),
            "voucher_cgst_amount" => $this->input->post('total_cgst_amount'),
            "voucher_sgst_amount" => $this->input->post('total_sgst_amount'),
            "voucher_cess_amount" => $this->input->post('total_tax_cess_amount'),
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "party_id" => $this->input->post('customer'),
            "party_type" => "customer",
            "billing_country_id" => $this->input->post('billing_country'),
            "billing_state_id" => $this->input->post('billing_state'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
            "updated_date" => "",
            "updated_user_id" => "",
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2'),
            "gst_payable" => $gst_payable);
        /*    if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
          $refund_data['currency_converted_amount'] = $this->input->post('receipt_amount');
          } else {
          $refund_data['currency_converted_amount'] = "0.00";
          } */
        if ($payment_mode == "cash") {
            $refund_data['voucher_status'] = "0";
        } else {
            $refund_data['voucher_status'] = "1";
        }

        $data_main = array_map('trim', $refund_data);
        $refund_voucher_table = 'refund_voucher';
        if ($refund_id = $this->general_model->insertData($refund_voucher_table, $data_main)) {
            $successMsg = 'Refund Voucher Added Successfully';
            $this->session->set_flashdata('refund_voucher_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $refund_id,
                'table_name' => $refund_voucher_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Refund Voucher Inserted');
            $data_main['refund_id'] = $refund_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $refund_voucher_item_data = $this->input->post('table_data');
            $js_data = json_decode($refund_voucher_item_data);
            $item_table = 'refund_voucher_item';
            $i = 1;
            $j = 1;
            $ledger_entry = array();
            $data_item = array();
            foreach ($js_data as $key => $value) {
                if ($value == null) {
                    
                } else {
                    $item_id = $value->item_id;
                    $item_type = $value->item_type;
                    $quantity = $value->item_quantity;
                    $refund_data = array(
                        "item_id" => $value->item_id,
                        "item_type" => $value->item_type,
                        "item_sub_total" => $value->item_price,
                        "item_grand_total" => $value->item_grand_total,
                        "item_igst_percentage" => $value->item_igst,
                        "item_igst_amount" => $value->item_igst_amount,
                        "item_cgst_percentage" => $value->item_cgst,
                        "item_cgst_amount" => $value->item_cgst_amount,
                        "item_sgst_percentage" => $value->item_sgst,
                        "item_sgst_amount" => $value->item_sgst_amount,
                        "item_tax_percentage" => $value->item_tax_percentage,
                        "item_tax_id" => $value->item_tax_id,
                        "item_tax_cess_id" => $value->item_tax_cess_id,
                        "item_tax_cess_percentage" => $value->item_tax_cess_percentage,
                        "item_tax_cess_amount" => $value->item_tax_cess_amount,
                        "item_tax_amount" => $value->item_tax_amount,
                        "item_description" => $value->item_description,
                        "refund_voucher_id" => $refund_id);
                    $data_item[$j] = array_map('trim', $refund_data);
                    $j = $j + 1;


                    $advance_update[] = array(
                        'advance_voucher_id' => $this->input->post('advance_voucher_id'),
                        'refund_status' => 1,
                    );

                    /*                     * ******* GST TAX STARTS ******* */
                    if ($gst_payable != 'yes') {
                        if ($value->item_igst != '' && $value->item_igst_amount > 0) {
                            $default_igst_id = $refund_ledger['IGST@X'];
                            $igst_x = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                            $igst_ary = array(
                                            'ledger_name' => 'Output IGST@'.(float)$value->item_igst.'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_igst,
                                            'amount' => 0
                                        );
                            if(!empty($igst_x)){
                                $igst_ledger = $igst_x->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',(float)$value->item_igst , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                $igst_ary['main_grp'] = $igst_x->main_group;
                                $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_igst.'%',
                                'subgrp_1' => 'IGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (!isset($igst[$igst_tax_ledger])) {
                                $igst[$igst_tax_ledger] = 0;
                            }
                            $igst[$igst_tax_ledger] += $value->item_igst_amount;
                            $ledger_entry[$igst_tax_ledger]["ledger_from"] = $igst_tax_ledger;
                            $ledger_entry[$igst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$igst_tax_ledger]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$igst_tax_ledger]["voucher_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$igst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$igst_tax_ledger]["dr_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$igst_tax_ledger]["cr_amount"] = 0;
                            $ledger_entry[$igst_tax_ledger]['ledger_id'] = $igst_tax_ledger;
                            $i = $i + 1;
                        }

                        if ($value->item_cgst != '' && $value->item_cgst_amount > 0) {
                            $default_cgst_id = $refund_ledger['CGST@X'];
                            $cgst_x = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                            
                            $cgst_ary = array(
                                            'ledger_name' => 'Output CGST@'.(float)$value->item_cgst.'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_cgst,
                                            'amount' => 0
                                        );
                            if(!empty($cgst_x)){
                                $cgst_ledger = $cgst_x->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',(float)$value->item_cgst , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_x->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                            }
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                            /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'CGST@' . (float)$value->item_cgst.'%',
                                'subgrp_1' => 'CGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (!isset($cgst[$cgst_tax_ledger])) {
                                $cgst[$cgst_tax_ledger] = 0;
                            }
                            $cgst[$cgst_tax_ledger] += $value->item_cgst_amount;
                            $ledger_entry[$cgst_tax_ledger]["ledger_from"] = $cgst_tax_ledger;
                            $ledger_entry[$cgst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$cgst_tax_ledger]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$cgst_tax_ledger]["voucher_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$cgst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cgst_tax_ledger]["dr_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$cgst_tax_ledger]["cr_amount"] = 0;
                            $ledger_entry[$cgst_tax_ledger]['ledger_id'] = $cgst_tax_ledger;
                            $i = $i + 1;
                        }

                        if ($value->item_sgst != '' && $value->item_sgst_amount > 0) {
                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($state_billing_id);
                            if ($is_utgst == '1')
                                $gst_lbl = 'UTGST';

                            $default_sgst_id = $refund_ledger[$gst_lbl.'@X'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                           
                            $sgst_ary = array(
                                            'ledger_name' => 'Output '.$gst_lbl . '@' .(float)$value->item_sgst . '%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' =>  (float)$value->item_sgst,
                                            'amount' => 0
                                        );
                            if(!empty($sgst_ledger_name)){
                                $sgst_ledger = $sgst_ledger_name->ledger_name;
                                $sgst_ledger = str_ireplace('{{X}}', (float)$value->item_sgst , $sgst_ledger);
                                $sgst_ary['ledger_name'] = $sgst_ledger;
                                $sgst_ary['primary_grp'] = $sgst_ledger_name->sub_group_1;
                                $sgst_ary['second_grp'] = $sgst_ledger_name->sub_group_2;
                                $sgst_ary['main_grp'] = $sgst_ledger_name->main_group;
                                $sgst_ary['default_ledger_id'] = $sgst_ledger_name->ledger_id;
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => $gst_lbl . '@' . (float)$value->item_sgst.'%',
                                'subgrp_1' => 'SGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (!isset($sgst[$sgst_tax_ledger])) {
                                $sgst[$sgst_tax_ledger] = 0;
                            }
                            $sgst[$sgst_tax_ledger] += $value->item_sgst_amount;
                            $ledger_entry[$sgst_tax_ledger]["ledger_from"] = $sgst_tax_ledger;
                            $ledger_entry[$sgst_tax_ledger]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$sgst_tax_ledger]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$sgst_tax_ledger]["voucher_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$sgst_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$sgst_tax_ledger]["dr_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$sgst_tax_ledger]["cr_amount"] = 0;
                            $ledger_entry[$sgst_tax_ledger]['ledger_id'] = $sgst_tax_ledger;
                            $i = $i + 1;
                        }

                        /*                         * ******* GST TAX ENDS ******* */


                        /*                         * ******* CESS TAX STARTS ******* */

                        if ($value->item_tax_cess_percentage != '' && $value->item_tax_cess_amount > 0) {
                            $default_cess_id = $refund_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                           
                            $cess_ary = array(
                                            'ledger_name' => 'Output Compensation Cess @'.(float)$value->item_tax_cess_percentage.'%',
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
                            $ledger_entry[$cess_tax_ledger]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$cess_tax_ledger]["voucher_amount"] = $cess[$cess_tax_ledger];
                            $ledger_entry[$cess_tax_ledger]["converted_voucher_amount"] = 0;
                            $ledger_entry[$cess_tax_ledger]["dr_amount"] = $cess[$cess_tax_ledger];
                            $ledger_entry[$cess_tax_ledger]["cr_amount"] = 0;
                            $ledger_entry[$cess_tax_ledger]['ledger_id'] = $cess_tax_ledger;
                            $i = $i + 1;
                        }
                        /*                         * ******* CESS TAX ENDS ******* */
                    }
                }
                $i = $i + 1;
            }




            $ledger_entry[$from_ledger_id]["ledger_from"] = $from_ledger_id;
            $ledger_entry[$from_ledger_id]["ledger_to"] = $customer_ledger_id;
            $ledger_entry[$from_ledger_id]["refund_voucher_id"] = $refund_id;
            $ledger_entry[$from_ledger_id]["voucher_amount"] = $customer_amount;
            $ledger_entry[$from_ledger_id]["converted_voucher_amount"] = 0;
            $ledger_entry[$from_ledger_id]["dr_amount"] = 0;
            $ledger_entry[$from_ledger_id]["cr_amount"] = $customer_amount;
            $ledger_entry[$from_ledger_id]['ledger_id'] = $from_ledger_id;
            $i = $i + 1;
            /*$advance_ledger_id = $this->ledger_model->getDefaultLedger('Advance');
            if ($advance_ledger_id == 0) {
                $advance_ledger_id = $this->ledger_model->addGroupLedger(array(
                    'ledger_name' => 'Advance1',
                    'subgrp_2' => '',
                    'subgrp_1' => '',
                    'main_grp' => 'Advance',
                    'amount' => 0
                ));
            } */

            $ledger_entry[$customer_ledger_id]["ledger_from"] = $from_ledger_id;
            $ledger_entry[$customer_ledger_id]["ledger_to"] = $customer_ledger_id;
            $ledger_entry[$customer_ledger_id]["refund_voucher_id"] = $refund_id;
            $ledger_entry[$customer_ledger_id]["voucher_amount"] = $sub_total;
            $ledger_entry[$customer_ledger_id]["converted_voucher_amount"] = 0;
            $ledger_entry[$customer_ledger_id]["dr_amount"] = $sub_total;
            $ledger_entry[$customer_ledger_id]["cr_amount"] = 0;
            $ledger_entry[$customer_ledger_id]['ledger_id'] = $customer_ledger_id;
            $i = $i + 1;



            $this->db->insert_batch($item_table, $data_item);
            $res = $this->db->update_batch('advance_voucher', $advance_update, 'advance_voucher_id');
            $this->db->insert_batch('accounts_refund_voucher', $ledger_entry);

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

            redirect('refund_voucher', 'refresh');
        } else {
            $errorMsg = 'Refund Voucher Add Unsuccessful';
            $this->session->set_flashdata('refund_voucher_error',$errorMsg);
            redirect('refund_voucher', 'refresh');
        }
    }

    public function voucher_entry($refund_id, $ledger_id, $ledger_entry, $operation, $currency) {
        $data1 = array(
            'refund_voucher_id' => $refund_id,
            'ledger_from' => $ledger_id['ledger_cash_bank'],
            'ledger_to' => $ledger_id['ledger_customer'],
            'voucher_amount' => $ledger_entry['grand_total'],
            'dr_amount' => "0.00",
            'cr_amount' => $ledger_entry['grand_total']);

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency) {
            $data1['converted_voucher_amount'] = $ledger_entry['grand_total'];
        } else {
            $data1['converted_voucher_amount'] = "0.00";
        }
        $data2 = array(
            'refund_voucher_id' => $refund_id,
            'ledger_from' => $ledger_id['ledger_customer'],
            'ledger_to' => $ledger_id['ledger_cash_bank'],
            'voucher_amount' => $ledger_entry['sub_total'],
            'dr_amount' => $ledger_entry['sub_total'],
            'cr_amount' => "0.00");
        $data3 = array(
            'refund_voucher_id' => $refund_id,
            'ledger_from' => $ledger_id['ledger_igst'],
            'ledger_to' => $ledger_id['ledger_cash_bank'],
            'voucher_amount' => $ledger_entry['igst_amount'],
            'dr_amount' => $ledger_entry['igst_amount'],
            'cr_amount' => "0.00");
        $data4 = array(
            'refund_voucher_id' => $refund_id,
            'ledger_from' => $ledger_id['ledger_cgst'],
            'ledger_to' => $ledger_id['ledger_cash_bank'],
            'voucher_amount' => $ledger_entry['cgst_amount'],
            'dr_amount' => $ledger_entry['cgst_amount'],
            'cr_amount' => "0.00");
        $data5 = array(
            'refund_voucher_id' => $refund_id,
            'ledger_from' => $ledger_id['ledger_sgst'],
            'ledger_to' => $ledger_id['ledger_cash_bank'],
            'voucher_amount' => $ledger_entry['sgst_amount'],
            'dr_amount' => $ledger_entry['sgst_amount'],
            'cr_amount' => "0.00");
        if ($operation == "add") {
            $this->general_model->insertData('accounts_refund_voucher', $data1);
            $this->general_model->insertData('accounts_refund_voucher', $data2);
            $this->general_model->insertData('accounts_refund_voucher', $data3);
            $this->general_model->insertData('accounts_refund_voucher', $data4);
            $this->general_model->insertData('accounts_refund_voucher', $data5);
        } elseif ($operation == "edit") {
            $accounts_refund = $this->general_model->getRecords('accounts_refund_id', 'accounts_refund_voucher', array(
                'refund_voucher_id' => $refund_id,
                'delete_status' => 0));
            if ($accounts_refund) {
                $this->general_model->updateData('accounts_refund_voucher', $data1, array(
                    'accounts_refund_id' => $accounts_refund[0]->accounts_refund_id));
                $this->general_model->updateData('accounts_refund_voucher', $data2, array(
                    'accounts_refund_id' => $accounts_refund[1]->accounts_refund_id));
                $this->general_model->updateData('accounts_refund_voucher', $data3, array(
                    'accounts_refund_id' => $accounts_refund[2]->accounts_refund_id));
                $this->general_model->updateData('accounts_refund_voucher', $data4, array(
                    'accounts_refund_id' => $accounts_refund[3]->accounts_refund_id));
                $this->general_model->updateData('accounts_refund_voucher', $data5, array(
                    'accounts_refund_id' => $accounts_refund[4]->accounts_refund_id));
            }
        }
    }

    function edit($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
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

        $access_common_settings = $section_modules['access_common_settings'];
        $data['access_common_settings'] = $section_modules['access_common_settings'];
        /* $data['access_modules'] = $section_modules['modules'];
          $data['access_sub_modules'] = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege'] = $section_modules['user_privilege'];
          $data['access_settings'] = $section_modules['settings'];

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
          } */
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $bank_account_module_id = $this->config->item('bank_account_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'customer_module_id' => $customer_module_id,
            'bank_account_module_id' => $bank_account_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $data['customer'] = $this->customer_call1();
        $data['currency'] = $this->currency_call();
        $data['bank_account'] = $this->bank_account_call_new();
        $data['data'] = $this->general_model->getRecords('*', 'refund_voucher', array(
            'refund_id' => $id));
        $data['refund_id'] = $id;

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));


        $product_items = $this->common->refund_voucher_items_product_list_field($id);
        $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


        $service_items = $this->common->refund_voucher_items_service_list_field($id);
        $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

        $advance_product_items = $this->common->refund_voucher_items_product_advance_list_field($id);
        $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items, $voucher_items_advance_product_items);
        $data['tax'] = $this->tax_call();

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $discountExist = 0;
        $cessExist = 0;

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
            $taxExist = 0;
        }

        if ($data['data'][0]->voucher_cess_amount > 0) {
            $cessExist = 1;
        }

        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['tax_exist'] = $taxExist;
        $data['cess_exist'] = $cessExist;
        $data['is_utgst'] = $is_utgst;
        $data['discount_exist'] = $discountExist;
        $this->load->view('refund_voucher/edit', $data);
    }

    public function edit_refund() {
        $data = $this->get_default_country_state();
        $refund_id = $this->input->post('refund_id');
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $module_id = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /*
          $data['access_modules'] = $section_modules['modules'];
          $data['access_sub_modules'] = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege'] = $section_modules['user_privilege'];
          $data['access_settings'] = $section_modules['settings']; */
        $data['access_common_settings'] = $section_modules['access_common_settings'];
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $modules_present = array(
            'accounts_module_id' => $this->config->item('accounts_module'));
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $access_settings = $data['access_settings'];
        $currency = $this->input->post('currency_id');
        $refund_ledger = $this->config->item('refund_ledger');

       /* if ($access_settings[0]->invoice_creation == "automatic") {
            if ($this->input->post('voucher_number') != $this->input->post('voucher_number_old')) {
                $primary_id = "advance_id";
                $table_name = 'advance_voucher';
                $date_field_name = "voucher_date";
                $current_date = date('Y-m-d',strtotime($this->input->post('voucher_date')));
                $voucher_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else {
                $voucher_number = $this->input->post('voucher_number');
            }
        } else {
            $voucher_number = $this->input->post('voucher_number');
        }*/

        $voucher_number = $this->input->post('voucher_number');
        $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array('customer_id' => $this->input->post('customer')));
        $ledger_customer = $customer[0]->ledger_id;
        $customer_name = $customer[0]->customer_name;
        $customer_ledger_id = $customer[0]->ledger_id;
        $refund_ledger = $this->config->item('refund_ledger');
        if(!$customer_ledger_id){
            $default_customer_id = $refund_ledger['CUSTOMER'];
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

        $ledger_customer = $customer[0]->ledger_id;
        $advance_voucher_id = $this->input->post('advance_voucher_id');
        $refund_amount = $this->input->post('receipt_amount');

        if ($this->input->post('payment_mode') == "other payment mode") {
            $payment_via = $this->input->post('payment_via');
            $reff_number = $this->input->post('ref_number');
        } else {
            $payment_via = "";
            $reff_number = "";
        }
       

        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode = $bank_acc_payment_mode[0];
            $from_acc = $bank_acc_payment_mode[1];

            $ledger_bank_acc       = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode));
            $from_ledger_id =  $ledger_bank_acc[0]->ledger_id;
            $ledger_from = $ledger_bank_acc[0]->ledger_id;
        } else {
            $payment_mode = $this->input->post('payment_mode');
            $from_acc = $this->input->post('payment_mode');
            $ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));

            if ($from_acc != '') {
                $default_refund_id = $refund_ledger['Other_Payment'];
                if (strtolower($from_acc) == "cash"){
                    $default_refund_id = $refund_ledger['Cash_Payment'];
                }

                $default_refund_name = $this->ledger_model->getDefaultLedgerId($default_refund_id);
                        
                $default_refund_ary = array(
                                'ledger_name' => strtolower($from_acc),
                                'second_grp' => '',
                                'primary_grp' => 'Cash & Cash Equivalent',
                                'main_grp' => 'Current Assets',
                                'default_value' => strtolower($from_acc),
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );
                if(!empty($default_refund_name)){
                    $default_led_nm = $default_refund_name->ledger_name;
                    $default_refund_ary['ledger_name'] = str_ireplace('{{PAYMENT_MODE}}',strtolower($from_acc), $default_led_nm);  
                    $default_refund_ary['primary_grp'] = $default_refund_name->sub_group_1;
                    $default_refund_ary['second_grp'] = $default_refund_name->sub_group_2;
                    $default_refund_ary['main_grp'] = $default_refund_name->main_group;
                    $default_refund_ary['default_ledger_id'] = $default_refund_name->ledger_id;
                }
                $from_ledger_id = $this->ledger_model->getGroupLedgerId($default_refund_ary);
                /*$from_ledger_id = $this->ledger_model->addGroupLedger(array(
                    'ledger_name' => $from_acc,
                    'subgrp_1' => '',
                    'subgrp_2' => (strtolower($from_acc) == 'cash' ? 'Cash & Cash Equivalent' : ''),
                    'main_grp' => 'Current Assets',
                    'amount' => 0
                ));*/
            }
        }

       // $cheque_date = date('Y-m-d',strtotime($this->input->post('cheque_date')));
        $cheque_date = ($this->input->post('cheque_date') != '' ? date('Y-m-d', strtotime($this->input->post('cheque_date'))) : '');
        if (!$cheque_date) {
            $cheque_date = null;
        }

        $total_tax_amount = $this->input->post('total_tax_amount');

        $receipt_amount_x = $this->input->post('receipt_amount');
        $sub_total = $this->input->post('total_sub_total');
        $voucher_date = $this->input->post('voucher_date');
        $state_billing_id = $this->input->post('billing_state');
        $gst_payable = $this->input->post('gst_payable');
        $gst_payable = ($gst_payable) ? $gst_payable : 'no';
        if ($gst_payable != 'yes') {
            $customer_amount = $receipt_amount_x;
        } else {
            $customer_amount = bcsub($receipt_amount_x, $total_tax_amount, 2);
        }

        $refund_data = array(
            "voucher_date" => date('Y-m-d',strtotime($this->input->post('voucher_date'))),
            "voucher_number" => $voucher_number,
            "voucher_sub_total" => $this->input->post('total_sub_total'),
            "receipt_amount" => $this->input->post('receipt_amount'),
            "voucher_tax_amount" => $this->input->post('total_tax_amount'),
            "description" => $this->input->post('description'),
            "reference_id" => $this->input->post('advance_voucher_id'),
            "reference_number" => $this->input->post('reference_number'),
            "reference_type" => "advance",
            "from_account" => 'customer-' . $customer[0]->customer_name,
            "to_account" => $from_acc,
            "payment_mode" => $payment_mode,
            "payment_via" => $this->input->post('payment_via'),
            "ref_number" => $this->input->post('ref_number'),
            "bank_name" => $this->input->post('bank_name'),
            "cheque_date" => $cheque_date,
            "cheque_number" => $this->input->post('cheque_number'),
            "voucher_igst_amount" => $this->input->post('total_igst_amount'),
            "voucher_cgst_amount" => $this->input->post('total_cgst_amount'),
            "voucher_sgst_amount" => $this->input->post('total_sgst_amount'),
            "voucher_cess_amount" => $this->input->post('total_tax_cess_amount'),
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "party_id" => $this->input->post('customer'),
            "party_type" => "customer",
            "billing_country_id" => $this->input->post('billing_country'),
            "billing_state_id" => $this->input->post('billing_state'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2'),
            "gst_payable" => $gst_payable);
        /* if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
          $refund_data['currency_converted_amount'] = $this->input->post('receipt_amount');
          } else {
          $refund_data['currency_converted_amount'] = "0.00";
          }
          if ($payment_mode == "cash") {
          $refund_data['voucher_status'] = "0";
          } else {
          $refund_data['voucher_status'] = "1";
          } */

        $data_main = array_map('trim', $refund_data);
        $refund_voucher_table = 'refund_voucher';
        $where = array(
            'refund_id' => $refund_id);
        if ($this->general_model->updateData($refund_voucher_table, $data_main, $where)) {
            $successMsg = 'Refund Voucher Updated Successfully';
            $this->session->set_flashdata('refund_voucher_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $refund_id,
                'table_name' => $refund_voucher_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Refund Voucher Updated');
            $data_main['refund_id'] = $refund_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $voucher_item_data = $this->input->post('table_data');
            $js_data = json_decode($voucher_item_data);
            $item_table = 'refund_voucher_item';
            $i = 1;
            $j = 1;
            $ledger_entry = array();
            $data_item = array();
            foreach ($js_data as $key => $value) {
                if ($value == null) {
                    
                } else {
                    $item_id = $value->item_id;
                    $item_type = $value->item_type;
                    $quantity = $value->item_quantity;
                    $refund_data = array(
                        "item_id" => $value->item_id,
                        "item_type" => $value->item_type,
                        "item_sub_total" => $value->item_price,
                        "item_grand_total" => $value->item_grand_total,
                        "item_igst_percentage" => $value->item_igst,
                        "item_igst_amount" => $value->item_igst_amount,
                        "item_cgst_percentage" => $value->item_cgst,
                        "item_cgst_amount" => $value->item_cgst_amount,
                        "item_sgst_percentage" => $value->item_sgst,
                        "item_sgst_amount" => $value->item_sgst_amount,
                        "item_tax_percentage" => $value->item_tax_percentage,
                        "item_tax_id" => $value->item_tax_id,
                        "item_tax_cess_id" => $value->item_tax_cess_id,
                        "item_tax_cess_percentage" => $value->item_tax_cess_percentage,
                        "item_tax_cess_amount" => $value->item_tax_cess_amount,
                        "item_tax_amount" => $value->item_tax_amount,
                        "item_description" => $value->item_description,
                        "refund_voucher_id" => $refund_id);
                    $data_item[$j] = array_map('trim', $refund_data);
                    $j = $j + 1;


                    /*                     * ******* GST TAX STARTS ******* */
                    if ($gst_payable != 'yes') {
                        if ($value->item_igst != '' && $value->item_igst_amount > 0) {
                            $default_igst_id = $refund_ledger['IGST@X'];
                            $igst_x = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                            $igst_ary = array(
                                            'ledger_name' => 'Output IGST@'.(float)$value->item_igst.'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_igst,
                                            'amount' => 0
                                        );
                            if(!empty($igst_x)){
                                $igst_ledger = $igst_x->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',(float)$value->item_igst , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                $igst_ary['main_grp'] = $igst_x->main_group;
                                $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . (float)$value->item_igst.'%',
                                'subgrp_1' => 'IGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (!isset($igst[$igst_tax_ledger])) {
                                $igst[$igst_tax_ledger] = 0;
                            }
                            $igst[$igst_tax_ledger] = + $value->item_igst_amount;
                            $ledger_entry[$i]["ledger_from"] = $igst_tax_ledger;
                            $ledger_entry[$i]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$i]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$i]["voucher_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$i]["converted_voucher_amount"] = 0;
                            $ledger_entry[$i]["dr_amount"] = $igst[$igst_tax_ledger];
                            $ledger_entry[$i]["cr_amount"] = 0;
                            $ledger_entry[$i]['ledger_id'] = $igst_tax_ledger;
                            $i = $i + 1;
                        }

                        if ($value->item_cgst != '' && $value->item_cgst_amount > 0) {
                            $default_cgst_id = $refund_ledger['CGST@X'];
                            $cgst_x = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                           
                            $cgst_ary = array(
                                            'ledger_name' => 'Output CGST@'.(float)$value->item_cgst.'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$value->item_cgst,
                                            'amount' => 0
                                        );
                            if(!empty($cgst_x)){
                                $cgst_ledger = $cgst_x->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',(float)$value->item_cgst , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_x->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                            }
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);

                            /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'CGST@' . (float)$value->item_cgst.'%',
                                'subgrp_1' => 'CGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (!isset($cgst[$cgst_tax_ledger])) {
                                $cgst[$cgst_tax_ledger] = 0;
                            }
                            $cgst[$cgst_tax_ledger] = + $value->item_cgst_amount;
                            $ledger_entry[$i]["ledger_from"] = $cgst_tax_ledger;
                            $ledger_entry[$i]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$i]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$i]["voucher_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$i]["converted_voucher_amount"] = 0;
                            $ledger_entry[$i]["dr_amount"] = $cgst[$cgst_tax_ledger];
                            $ledger_entry[$i]["cr_amount"] = 0;
                            $ledger_entry[$i]['ledger_id'] = $cgst_tax_ledger;
                            $i = $i + 1;
                        }

                        if ($value->item_sgst != '' && $value->item_sgst_amount > 0) {
                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($state_billing_id);
                            if ($is_utgst == '1')
                                $gst_lbl = 'UTGST';
                            $default_sgst_id = $refund_ledger[$gst_lbl.'@X'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                           
                            $sgst_ary = array(
                                            'ledger_name' => 'Output '.$gst_lbl . '@' .(float)$value->item_sgst . '%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' =>  (float)$value->item_sgst,
                                            'amount' => 0
                                        );
                            if(!empty($sgst_ledger_name)){
                                $sgst_ledger = $sgst_ledger_name->ledger_name;
                                $sgst_ledger = str_ireplace('{{X}}', (float)$value->item_sgst , $sgst_ledger);
                                $sgst_ary['ledger_name'] = $sgst_ledger;
                                $sgst_ary['primary_grp'] = $sgst_ledger_name->sub_group_1;
                                $sgst_ary['second_grp'] = $sgst_ledger_name->sub_group_2;
                                $sgst_ary['main_grp'] = $sgst_ledger_name->main_group;
                                $sgst_ary['default_ledger_id'] = $sgst_ledger_name->ledger_id;
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => $gst_lbl . '@' . (float)$value->item_sgst.'%',
                                'subgrp_1' => 'SGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (!isset($sgst[$sgst_tax_ledger])) {
                                $sgst[$sgst_tax_ledger] = 0;
                            }
                            $sgst[$sgst_tax_ledger] = + $value->item_sgst_amount;
                            $ledger_entry[$i]["ledger_from"] = $sgst_tax_ledger;
                            $ledger_entry[$i]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$i]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$i]["voucher_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$i]["converted_voucher_amount"] = 0;
                            $ledger_entry[$i]["dr_amount"] = $sgst[$sgst_tax_ledger];
                            $ledger_entry[$i]["cr_amount"] = 0;
                            $ledger_entry[$i]['ledger_id'] = $sgst_tax_ledger;
                            $i = $i + 1;
                        }

                        /*                         * ******* GST TAX ENDS ******* */


                        /*                         * ******* CESS TAX STARTS ******* */

                        if ($value->item_tax_cess_percentage != '' && $value->item_tax_cess_amount > 0) {
                            $default_cess_id = $refund_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                           
                            $cess_ary = array(
                                            'ledger_name' => 'Output Compensation Cess @'.(float)$value->item_tax_cess_percentage.'%',
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
                            $cess[$cess_tax_ledger] = + $value->item_tax_cess_amount;
                            $ledger_entry[$i]["ledger_from"] = $cess_tax_ledger;
                            $ledger_entry[$i]["ledger_to"] = $customer_ledger_id;
                            $ledger_entry[$i]["refund_voucher_id"] = $refund_id;
                            $ledger_entry[$i]["voucher_amount"] = $cess[$cess_tax_ledger];
                            $ledger_entry[$i]["converted_voucher_amount"] = 0;
                            $ledger_entry[$i]["dr_amount"] = $cess[$cess_tax_ledger];
                            $ledger_entry[$i]["cr_amount"] = 0;
                            $ledger_entry[$i]['ledger_id'] = $cess_tax_ledger;
                            $i = $i + 1;
                        }
                        /*                         * ******* CESS TAX ENDS ******* */
                    }
                }
            }
            $ledger_entry[$i]["ledger_from"] = $from_ledger_id;
            $ledger_entry[$i]["ledger_to"] = $customer_ledger_id;
            $ledger_entry[$i]["refund_voucher_id"] = $refund_id;
            $ledger_entry[$i]["voucher_amount"] = $customer_amount;
            $ledger_entry[$i]["converted_voucher_amount"] = 0;
            $ledger_entry[$i]["dr_amount"] = 0;
            $ledger_entry[$i]["cr_amount"] = $customer_amount;
            $ledger_entry[$i]['ledger_id'] = $from_ledger_id;
            $i = $i + 1;
          /*  $advance_ledger_id = $this->ledger_model->getDefaultLedger('Advance');
            if ($advance_ledger_id == 0) {
                $advance_ledger_id = $this->ledger_model->addGroupLedger(array(
                    'ledger_name' => 'Advance1',
                    'subgrp_2' => '',
                    'subgrp_1' => '',
                    'main_grp' => 'Advance',
                    'amount' => 0
                ));
            } */

            $ledger_entry[$i]["ledger_from"] = $from_ledger_id;
            $ledger_entry[$i]["ledger_to"] = $customer_ledger_id;
            $ledger_entry[$i]["refund_voucher_id"] = $refund_id;
            $ledger_entry[$i]["voucher_amount"] = $sub_total;
            $ledger_entry[$i]["converted_voucher_amount"] = 0;
            $ledger_entry[$i]["dr_amount"] = $sub_total;
            $ledger_entry[$i]["cr_amount"] = 0;
            $ledger_entry[$i]['ledger_id'] = $customer_ledger_id;
            $i = $i + 1;
            $old_voucher_items = $this->general_model->getRecords('*', 'accounts_refund_voucher', array('refund_voucher_id' => $refund_id, 'delete_status' => 0));
            /* echo "<pre>";
              print_r($old_voucher_items);
              print_r($vouchers);
              exit(); */
            $old_sales_ledger_ids = $this->getValues($old_voucher_items, 'refund_voucher_id');
            $not_deleted_ids = array();
            foreach ($ledger_entry as $key => $value) {
                if (($led_key = array_search($value['ledger_id'], $old_sales_ledger_ids)) !== false) {
                    unset($old_sales_ledger_ids[$led_key]);
                    $accounts_receipt_id = $old_voucher_items[$led_key]->accounts_receipt_id;
                    array_push($not_deleted_ids, $accounts_receipt_id);
                    $value['refund_voucher_id'] = $refund_id;
                    $value['delete_status'] = 0;
                    $where = array('refund_voucher_id' => $accounts_receipt_id);
                    $post_data = array('data' => $value,
                        'where' => $where,
                        'voucher_date' => $voucher_date,
                        'table' => 'refund_voucher',
                        'sub_table' => 'accounts_refund_voucher',
                        'primary_id' => 'refund_id',
                        'sub_primary_id' => 'refund_voucher_id'
                    );
                    $this->general_model->updateBunchVoucherCommon($post_data);
                }
            }

            $tables = array('accounts_refund_voucher', $item_table);
            $this->db->where('refund_voucher_id', $refund_id);
            $this->db->delete($tables);
            $this->db->insert_batch('accounts_refund_voucher', $ledger_entry);
            $this->db->insert_batch($item_table, $data_item);
            redirect('refund_voucher', 'refresh');
        } else {
            $errorMsg = 'Refund Voucher Update Unsuccessful';
            $this->session->set_flashdata('refund_voucher_error',$errorMsg);
            redirect('refund_voucher', 'refresh');
        }
    }

    public function view($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /* $data['access_modules'] = $section_modules['modules'];
          $data['access_sub_modules'] = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege'] = $section_modules['user_privilege'];
          $data['access_settings'] = $section_modules['settings'];
          $data['access_common_settings'] = $section_modules['common_settings'];
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
          } */
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'customer_module_id' => $customer_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $advance_data = $this->common->refund_voucher_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($advance_data['string'], $advance_data['table'], $advance_data['where'], $advance_data['join']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));



        $product_items = $this->common->refund_voucher_items_product_list_field($id);
        $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


        $service_items = $this->common->refund_voucher_items_service_list_field($id);
        $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

        $advance_product_items = $this->common->refund_voucher_items_product_advance_list_field($id);
        $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items, $voucher_items_advance_product_items);
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city'] = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        $cess = 0;
        $dpcount = 0;
        $dtcount = 0;
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);
        foreach ($data['items'] as $value) {
            $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
            $igst = bcadd($igst, $value->item_igst_amount, 2);
            $cess = bcadd($cess, $value->item_tax_cess_amount, 2);
            if ($value->item_description != "" && $value->item_description != null) {
                $dpcount++;
            }
        } $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['cess_tax'] = $cess;
        $data['dpcount'] = $dpcount;
        $data['is_utgst'] = $is_utgst;
        $this->load->view('refund_voucher/view', $data);
    }

    public function pdf($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $data['access_common_settings'] = $section_modules['access_common_settings'];
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'customer_module_id' => $customer_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'customer_module_id' => $customer_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city'] = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data['currency'] = $this->currency_call();
        $refund_data = $this->common->refund_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($refund_data['string'], $refund_data['table'], $refund_data['where'], $refund_data['join']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));



        $product_items = $this->common->refund_voucher_items_product_list_field($id);
        $refund_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


        $service_items = $this->common->refund_voucher_items_service_list_field($id);
        $refund_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

        $advance_product_items = $this->common->refund_voucher_items_product_advance_list_field($id);
        $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $data['items'] = array_merge($refund_product_items, $refund_service_items, $voucher_items_advance_product_items);
        $invoice_type = $this->input->post('pdf_type_check');
        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        $cess = 0;
        $dpcount = 0;
        $dtcount = 0;
        foreach ($data['items'] as $value) {
            $igst = bcadd($igst, $value->item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
            $cess = bcadd($cess, $value->item_tax_cess_amount, 2);
            if ($value->item_description != "" && $value->item_description != null) {
                $dpcount++;
            }
        }
        $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['cess_tax'] = $cess;
        $data['dpcount'] = $dpcount;
        $data['dtcount'] = $dtcount;
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);
        $data['is_utgst'] = $is_utgst;
        $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1'] = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2'] = $note_data['note2'];
        $data['template2'] = $note_data['template2'];


        $pdf = $this->general_model->getRecords('settings.*', 'settings', [
            'module_id' => 2,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);


        $pdf_json = $pdf[0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);



        $html = $this->load->view('refund_voucher/pdf1', $data, true);



        include(APPPATH . "third_party/dompdf/autoload.inc.php");
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();
        //we test first.
        //included.
        //now we can use all methods of dompdf
        //first im giving our html text to this method.
        $dompdf->load_html($html);

        $paper_size = 'a4';
        $orientation = 'portrait';

        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size, $orientation);

        //and getting rend
        $dompdf->render();

        $dompdf->stream($data['data'][0]->voucher_number, array(
            'Attachment' => 0));
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        $refund_voucher_table = 'refund_voucher';
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $data['access_common_settings'] = $section_modules['access_common_settings'];
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $modules_present = array(
            'accounts_module_id' => $this->config->item('accounts_module'));
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        /*  $data['access_modules'] = $section_modules['modules'];
          $data['access_sub_modules'] = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege'] = $section_modules['user_privilege'];
          $data['access_settings'] = $section_modules['settings'];
          $data['access_common_settings'] = $section_modules['common_settings']; */
        $data['advance_id'] = $this->general_model->getRecords('reference_id', 'refund_voucher', array(
            'refund_id' => $id));
        $advance_id = $data['advance_id'][0]->reference_id;

        $this->general_model->updateData('refund_voucher_item', array('delete_status' => 1), array('refund_voucher_id' => $id));
        if ($this->general_model->updateData('refund_voucher', array('delete_status' => 1), array('refund_id' => $id))) {
            $this->general_model->updateData('advance_voucher', array('refund_status' => 0), array('advance_voucher_id' => $advance_id));

            $this->general_model->deleteCommonVoucher(array('table' => 'refund_voucher', 'where' => array('refund_id' => $id)), array('table' => 'accounts_refund_voucher', 'where' => array('refund_voucher_id' => $id)));

            $successMsg = 'Refund Voucher Deleted Successfully';
            $this->session->set_flashdata('refund_voucher_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'refund_voucher',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Refund Voucher Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('refund_voucher', 'refresh');
        } else {
            $errorMsg = 'Refund Voucher Delete Unsuccessful';
            $this->session->set_flashdata('refund_voucher_error',$errorMsg);
            redirect('refund_voucher', 'refresh');
        }
    }

    public function email($id) {
        $id = $this->encryption_url->decode($id);
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $email_sub_module_id = $this->config->item('email_sub_module');
        $email_sub_module = 0;
        foreach ($data['access_sub_modules'] as $key => $value) {
            if ($email_sub_module_id == $value->sub_module_id) {
                $email_sub_module = 1;
            }
        } if ($email_sub_module == 1) {
            ob_start();
            $html = ob_get_clean();
            $html = utf8_encode($html);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $country_data = $this->common->country_field($data['branch'][0]->branch_country_id);
            $data['country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
            $data['state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
            $city_data = $this->common->city_field($data['branch'][0]->branch_city_id);
            $data['city'] = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
            $data['currency'] = $this->currency_call();
            $refund_data = $this->common->refund_voucher_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($refund_data['string'], $refund_data['table'], $refund_data['where'], $refund_data['join']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));


            $product_items = $this->common->refund_voucher_items_product_list_field($id);
            $refund_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);

            $service_items = $this->common->refund_voucher_items_service_list_field($id);
            $refund_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

            $advance_product_items = $this->common->refund_voucher_items_product_advance_list_field($id);
            $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);
            $data['items'] = array_merge($refund_product_items, $refund_service_items, $voucher_items_advance_product_items);
            $invoice_type = $this->input->post('pdf_type_check');
            $igst = 0;
            $cgst = 0;
            $sgst = 0;
            $dpcount = 0;
            $dtcount = 0;
            foreach ($data['items'] as $value) {
                $igst = bcadd($igst, $value->item_igst_amount, 2);
                $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
                $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
                if ($value->item_description != "" && $value->item_description != null) {
                    $dpcount++;
                }
            } $data['igst_tax'] = $igst;
            $data['cgst_tax'] = $cgst;
            $data['sgst_tax'] = $sgst;
            $data['dpcount'] = $dpcount;
            $data['dtcount'] = $dtcount;
            $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1'] = $note_data['note1'];
            $data['template1'] = $note_data['template1'];
            $data['note2'] = $note_data['note2'];
            $data['template2'] = $note_data['template2'];
            $html = $this->load->view('refund_voucher/pdf', $data, true);
            include(APPPATH . 'third_party/mpdf60/mpdf.php');
            $mpdf = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in = 'UTF-8';
            $file_path = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
            $file_name = str_replace('/','_',$file_name);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F');
            $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name'] = $file_name . '.pdf';
            $refund_voucher_data = $this->common->refund_voucher_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($refund_voucher_data['string'], $refund_voucher_data['table'], $refund_voucher_data['where'], $refund_voucher_data['join']);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id' => $refund_voucher_module_id,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('refund_voucher/email', $data);
        } else {
            $this->load->view('refund_voucher', $data);
        }
    }

    public function convert_currency() {
        $id = $this->input->post('convert_currency_id');
        $id = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');

        $data = array(
            'currency_converted_rate' => $new_converted_rate,
            'currency_converted_amount' => $this->input->post('currency_converted_amount'));
        $this->general_model->updateData('refund_voucher', $data, array(
            'refund_id' => $id));

        $accounts_refund_voucher = $this->general_model->getRecords('*', 'accounts_refund_voucher', array(
            'refund_voucher_id' => $id,
            'delete_status' => 0));

        foreach ($accounts_refund_voucher as $key1 => $value1) {
            $new_converted_voucher_amount = bcmul($accounts_refund_voucher[$key1]->voucher_amount, $new_converted_rate, 2);

            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount);
            $where = array(
                'accounts_refund_id' => $accounts_refund_voucher[$key1]->accounts_refund_id);
            $voucher_table = "accounts_refund_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }

        redirect('refund_voucher', 'refresh');
    }

    function view_details($id) {
        $refund_voucher_id = $this->encryption_url->decode($id);
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];
        $email_sub_module_id = $this->config->item('email_sub_module');
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
        $voucher_details = $this->common->refund_voucher_details($refund_voucher_id);
        $data['data'] = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
        $this->load->view('refund_voucher/view_details', $data);
    }

    public function email_popup($id) {
        $id = $this->encryption_url->decode($id);
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $email_sub_module_id = $this->config->item('email_sub_module');
        $email_sub_module = 0;

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city'] = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data['currency'] = $this->currency_call();
        $refund_data = $this->common->refund_voucher_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($refund_data['string'], $refund_data['table'], $refund_data['where'], $refund_data['join']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));



        $product_items = $this->common->refund_voucher_items_product_list_field($id);
        $refund_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);

        $advance_product_items = $this->common->refund_voucher_items_product_advance_list_field($id);
        $voucher_items_advance_product_items = $this->general_model->getJoinRecords($advance_product_items['string'], $advance_product_items['table'], $advance_product_items['where'], $advance_product_items['join']);

        $service_items = $this->common->refund_voucher_items_service_list_field($id);
        $refund_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($refund_product_items, $refund_service_items, $voucher_items_advance_product_items);

        $invoice_type = $this->input->post('pdf_type_check');
        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        $cess = 0;
        $dpcount = 0;
        $dtcount = 0;
        foreach ($data['items'] as $value) {
            $igst = bcadd($igst, $value->item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
            $cess = bcadd($sgst, $value->item_tax_cess_amount, 2);
            if ($value->item_description != "" && $value->item_description != null) {
                $dpcount++;
            }
        }
        $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['cess_tax'] = $cess;
        $data['dpcount'] = $dpcount;
        $data['dtcount'] = $dtcount;
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->billing_state_id);
        $data['is_utgst'] = $is_utgst;
        $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1'] = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2'] = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        $pdf = $this->general_model->getRecords('settings.*', 'settings', [
            'module_id' => 2,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);


        $pdf_json = $pdf[0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);
        $html = $this->load->view('refund_voucher/pdf1', $data, true);
        /*     include(APPPATH . 'third_party/mpdf60/mpdf.php');
          $mpdf = new mPDF();
          $mpdf->allow_charset_conversion = true;
          $mpdf->charset_in = 'UTF-8';
          $file_path = "././pdf_form/";
          $mpdf->WriteHTML($html);
          $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
          $mpdf->Output($file_path . $file_name . '.pdf', 'F'); */

        include APPPATH . "third_party/dompdf/autoload.inc.php";

        //and now im creating new instance dompdf
        $file_path = "././pdf_form/";
        $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->voucher_number);
        $dompdf = new Dompdf\Dompdf();
        $paper_size = 'a4';
        $orientation = 'portrait';
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents($file_path . $file_name . '.pdf', $output);
        $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
        $data['pdf_file_name'] = $file_name . '.pdf';
        $refund_voucher_data = $this->common->refund_voucher_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($refund_voucher_data['string'], $refund_voucher_data['table'], $refund_voucher_data['where'], $refund_voucher_data['join']);
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'added_user_id' => $this->session->userdata('SESS_USER_ID')));
        $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
            'module_id' => $refund_voucher_module_id,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));
        $data['data'][0]->pdf_file_path = $data['pdf_file_path'];
        $data['data'][0]->pdf_file_name = $data['pdf_file_name'];
        $data['data'][0]->email_template = $data['email_template'];
        $data['data'][0]->firm_name = $data['branch'][0]->firm_name;
        $result = json_encode($data['data']);
        echo $result;
    }
}
