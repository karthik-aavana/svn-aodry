<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Refund_voucher extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
      //  $this->modules = $this->get_modules();
    }
  
//    function index() {
//        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
//        $data['module_id'] = $refund_voucher_module_id;
//        $modules = $this->modules;
//        $privilege = "view_privilege";
//        $data['privilege'] = "view_privilege";
//        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
//        $data['access_modules'] = $section_modules['modules'];
//        $data['access_sub_modules'] = $section_modules['sub_modules'];
//        $data['access_module_privilege'] = $section_modules['module_privilege'];
//        $data['access_user_privilege'] = $section_modules['user_privilege'];
//        $data['access_settings'] = $section_modules['settings'];
//        $data['access_common_settings'] = $section_modules['common_settings'];
//        $email_sub_module_id = $this->config->item('email_sub_module');
//        foreach ($modules['modules'] as $key => $value) {
//            $data['active_modules'][$key] = $value->module_id;
//            if ($value->view_privilege == "yes") {
//                $data['active_view'][$key] = $value->module_id;
//            } if ($value->edit_privilege == "yes") {
//                $data['active_edit'][$key] = $value->module_id;
//            } if ($value->delete_privilege == "yes") {
//                $data['active_delete'][$key] = $value->module_id;
//            } if ($value->add_privilege == "yes") {
//                $data['active_add'][$key] = $value->module_id;
//            }
//        } if (!empty($this->input->post())) {
//            $columns = array(
//                0 => 'voucher_date',
//                1 => 'voucher_number',
//                2 => 'customer',
//                3 => 'reference_number',
//                4 => 'amount',
//                5 => 'currency_converted_amount',
//                6 => 'to_account',
//                7 => 'added_user',
//                8 => 'action',);
//            $limit = $this->input->post('length');
//            $start = $this->input->post('start');
//            $order = $columns[$this->input->post('order')[0]['column']];
//            $dir = $this->input->post('order')[0]['dir'];
//            $list_data = $this->common->refund_voucher_list_field();
//            $list_data['search'] = 'all';
//            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
//            $totalFiltered = $totalData;
//            if (empty($this->input->post('search')['value'])) {
//                $list_data['limit'] = $limit;
//                $list_data['start'] = $start;
//                $list_data['search'] = 'all';
//                $posts = $this->general_model->getPageJoinRecords($list_data);
//            } else {
//                $search = $this->input->post('search')['value'];
//                $list_data['limit'] = $limit;
//                $list_data['start'] = $start;
//                $list_data['search'] = $search;
//                $posts = $this->general_model->getPageJoinRecords($list_data);
//                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
//            } $send_data = array();
//            if (!empty($posts)) {
//                foreach ($posts as $post) {
//                    $refund_id = $this->encryption_url->encode($post->refund_id);
//
//                    $nestedData['voucher_date'] = $post->voucher_date;
//                    $nestedData['voucher_number'] = '<a href="' . base_url('refund_voucher/view_details/') . $refund_id . '">' . $post->voucher_number . '</a>';
//                    $nestedData['customer'] = $post->customer_name;
//                    $nestedData['reference_number'] = $post->reference_number;
//                    $nestedData['amount'] = $post->currency_symbol . ' ' . $post->receipt_amount;
//                    $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
//                    $nestedData['to_account'] = $post->to_account;
//                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
//                    $cols = '<ul class="action_ul_custom">        <li>        <a href="' . base_url('refund_voucher/view/') . $refund_id . '"><i class="fa fa-eye text-orange"></i> Refund Details</a>        </li>';
//                    if ($data['access_module_privilege']->edit_privilege == "yes" && $post->voucher_status != "2") {
//                        $cols .= '<li>            <a href="' . base_url('refund_voucher/edit/') . $refund_id . '"><i class="fa fa-pencil text-blue"></i> Edit Refund</a>            </li>';
//                    } $cols .= '<li>    <a href="' . base_url('refund_voucher/pdf/') . $refund_id . '" target="_blank"><i class="fa fa-file-pdf-o text-teal" title="Download  PDF"></i> Download PDF</a>    </li>';
//                    $email_sub_module = 0;
//                    if ($data['access_module_privilege']->add_privilege == "yes") {
//                        foreach ($data['access_sub_modules'] as $key => $value) {
//                            if ($email_sub_module_id == $value->sub_module_id) {
//                                $email_sub_module = 1;
//                            }
//                        }
//                    } if ($email_sub_module == 1) {
//                        $cols .= '<li>            <a href="' . base_url('refund_voucher/email/') . $refund_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Refund Voucher</a>            </li>';
//                    } if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY') && $post->voucher_status != "2") {
//                        $cols .= '<li>               <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $refund_id . '" data-path="refund_voucher/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->receipt_amount . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>            </li>';
//                    } if ($data['access_module_privilege']->delete_privilege == "yes") {
//                        $cols .= '<li>       <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $refund_id . '" data-path="refund_voucher/delete"  href="#" title="Delete Advance" ><i class="fa fa-trash-o text-purple"></i> Delete Refund</a>       </li>';
//                    } $cols .= '</ul>';
//                    $nestedData['action'] = $cols;
//                    $send_data[] = $nestedData;
//                }
//            } $json_data = array(
//                "draw" => intval($this->input->post('draw')),
//                "recordsTotal" => intval($totalData),
//                "recordsFiltered" => intval($totalFiltered),
//                "data" => $send_data);
//            echo json_encode($json_data);
//        } else {
//            $data['currency'] = $this->currency_call();
//            $this->load->view('refund_voucher/list', $data);
//        }
//    }

    public function add() {
        $data = $this->get_default_country_state();
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data['access_modules'] = $section_modules['modules'];
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
        $data['bank_account'] = $this->bank_account_call();
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
        $string = 'advance_id,currency_id';
        $table = 'advance_voucher';
        $where = array(
            'voucher_number' => $invoice,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $advance_data = $this->general_model->getRecords($string, $table, $where, $order = "");
        $data['advance_id'] = $advance_data[0]->advance_id;
        $advance_id = $advance_data[0]->advance_id;
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


        if ($inventory_access[0]->inventory_advanced == "yes") {
            $product_items = $this->common->advance_voucher_items_product_inventory_list_field($advance_id);
            $advance_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        } else {
            $product_items = $this->common->advance_voucher_items_product_list_field($advance_id);
            $advance_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items = $this->common->advance_voucher_items_service_list_field($advance_id);
        $advance_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($advance_items_product_items, $advance_items_service_items);
        echo json_encode($data);
    }

    function add_refund() {
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $data['module_id'] = $refund_voucher_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($refund_voucher_module_id, $modules, $privilege);
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $modules_present = array(
            'accounts_module_id' => $this->config->item('accounts_module'));
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $access_settings = $section_modules['settings'];
        $currency = $this->input->post('currency_id');
        if ($access_settings[0]->invoice_creation == "automatic") {
            $primary_id = "refund_id";
            $table_name = 'refund_voucher';
            $date_field_name = "voucher_date";
            $current_date = $this->input->post('voucher_date');
            $voucher_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $voucher_number = $this->input->post('voucher_number');
        } $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array(
            'customer_id' => $this->input->post('customer')));
        $ledger_customer = $customer[0]->ledger_id;
        $advance_voucher_id = $this->input->post('advance_voucher_id');
        $refund_amount = $this->input->post('receipt_amount');
        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode = $bank_acc_payment_mode[0];
            $to_acc = $bank_acc_payment_mode[1];
            $ledger_bank_acc = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode[0]));
            $ledger_cash_bank = $ledger_bank_acc[0]->ledger_id;
        } else {
            $payment_mode = $this->input->post('payment_mode');
            $to_acc = $this->input->post('payment_mode');
            $ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));
        } $cheque_date = $this->input->post('cheque_date');
        if (!$cheque_date) {
            $cheque_date = null;
        } $refund_data = array(
            "voucher_date" => $this->input->post('voucher_date'),
            "voucher_number" => $voucher_number,
            "voucher_sub_total" => $this->input->post('total_sub_total'),
            "receipt_amount" => $this->input->post('receipt_amount'),
            "voucher_tax_amount" => $this->input->post('total_tax_amount'),
            "description" => $this->input->post('description'),
            "reference_id" => $this->input->post('advance_voucher_id'),
            "reference_number" => $this->input->post('advance_voucher_number'),
            "reference_type" => "advance",
            "from_account" => 'customer-' . $customer[0]->customer_name,
            "to_account" => $to_acc,
            "payment_mode" => $payment_mode,
            "bank_name" => $this->input->post('bank_name'),
            "payment_via" => $this->input->post('payment_via'),
            "ref_number" => $this->input->post('ref_number'),
            "cheque_date" => $cheque_date,
            "cheque_number" => $this->input->post('cheque_number'),
            "voucher_igst_amount" => $this->input->post('total_igst_amount'),
            "voucher_cgst_amount" => $this->input->post('total_cgst_amount'),
            "voucher_sgst_amount" => $this->input->post('total_sgst_amount'),
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
            "note2" => $this->input->post('note2'));
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $refund_data['currency_converted_amount'] = $this->input->post('receipt_amount');
        } else {
            $refund_data['currency_converted_amount'] = "0.00";
        }
        if ($payment_mode == "cash") {
            $refund_data['voucher_status'] = "0";
        } else {
            $refund_data['voucher_status'] = "1";
        }

        $data_main = array_map('trim', $refund_data);
        $refund_voucher_table = 'refund_voucher';
        if ($refund_id = $this->general_model->insertData($refund_voucher_table, $data_main)) {
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
                        "item_tax_amount" => $value->item_tax_amount,
                        "item_description" => $value->item_description,
                        "refund_voucher_id" => $refund_id);
                    $data_item = array_map('trim', $refund_data);
                    if ($this->general_model->insertData($item_table, $data_item)) {
                        $where = array(
                            'advance_id' => $this->input->post('advance_voucher_id'));
                        $table = 'advance_voucher';
                        $update_data = array(
                            'refund_status' => 1);
                        $this->general_model->updateData($table, $update_data, $where);
                    }
                }
            } $sales_id_amt = $this->general_model->getRecords('reference_id,receipt_amount,currency_converted_amount', 'advance_voucher', array(
                'advance_id' => $advance_voucher_id));
            $advance_amt = $sales_id_amt[0]->receipt_amount;
            $converted_advance_amt = $sales_id_amt[0]->currency_converted_amount;
            if ($sales_id_amt[0]->reference_id != "" && $sales_id_amt[0]->reference_id != null && $sales_id_amt[0]->reference_id != 0) {
                $sales_data = $this->general_model->getRecords('*', 'sales', array(
                    'sales_id' => $sales_id_amt[0]->reference_id));
                $paid_amt = bcsub($sales_data[0]->sales_paid_amount, $sales_id_amt[0]->receipt_amount, 2);
                $conv_paid_amt = bcsub($sales_data[0]->converted_paid_amount, $sales_id_amt[0]->currency_converted_amount, 2);
                $sales_where = array(
                    'sales_id' => $sales_id_amt[0]->reference_id);
                $sales_table = 'sales';
                $sales_update_data = array(
                    'sales_paid_amount' => $paid_amt,
                    'converted_paid_amount' => $conv_paid_amt);
                $this->general_model->updateData($sales_table, $sales_update_data, $sales_where);
            } if (isset($data['other_modules_present']['accounts_module_id'])) {
                foreach ($data['access_sub_modules'] as $key => $value) {
                    if (isset($data['accounts_sub_module_id'])) {
                        if ($data['accounts_sub_module_id'] == $value->sub_module_id) {
                            $ledger_igst = $this->ledger_model->getDefaultLedger('IGST');
                            $ledger_cgst = $this->ledger_model->getDefaultLedger('CGST');
                            $ledger_sgst = $this->ledger_model->getDefaultLedger('SGST');
                            $ledger_id = array(
                                'ledger_customer' => $ledger_customer,
                                'ledger_cash_bank' => $ledger_cash_bank,
                                'ledger_igst' => $ledger_igst,
                                'ledger_cgst' => $ledger_cgst,
                                'ledger_sgst' => $ledger_sgst,);
                            $ledger_entry = array(
                                'grand_total' => $data_main['receipt_amount'],
                                'sub_total' => $data_main['voucher_sub_total'],
                                'igst_amount' => $data_main['voucher_igst_amount'],
                                'cgst_amount' => $data_main['voucher_cgst_amount'],
                                'sgst_amount' => $data_main['voucher_sgst_amount']);
                            $this->voucher_entry($refund_id, $ledger_id, $ledger_entry, "add", $currency);
                        }
                    }
                }
            }
            if ($this->session->userdata('cat_type') != "" && $this->session->userdata('cat_type') != null && $this->session->userdata('cat_type') == 'customer_refund' && $payment_mode != "cash") {
                $this->session->unset_userdata('cat_type');
                if ($currency == $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                    redirect('bank_statement/bank_group', 'refresh');
                } else {
                    redirect('refund_voucher', 'refresh');
                }
            }
            redirect('refund_voucher', 'refresh');
        } else {
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
        $data['access_modules'] = $section_modules['modules'];
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
        $data['customer'] = $this->customer_call1();
        $data['currency'] = $this->currency_call();
        $data['bank_account'] = $this->bank_account_call();
        $data['data'] = $this->general_model->getRecords('*', 'refund_voucher', array(
            'refund_id' => $id));
        $data['refund_id'] = $id;

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));


        if ($inventory_access[0]->inventory_advanced == "yes") {
            $product_items = $this->common->refund_voucher_items_product_inventory_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        } else {
            $product_items = $this->common->refund_voucher_items_product_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items = $this->common->refund_voucher_items_service_list_field($id);
        $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items);
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
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $modules_present = array(
            'accounts_module_id' => $this->config->item('accounts_module'));
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];
        $access_settings = $section_modules['settings'];
        $currency = $this->input->post('currency_id');
        if ($access_settings[0]->invoice_creation == "automatic") {
            if ($this->input->post('voucher_number') != $this->input->post('voucher_number_old')) {
                $primary_id = "advance_id";
                $table_name = 'advance_voucher';
                $date_field_name = "voucher_date";
                $current_date = $this->input->post('voucher_date');
                $voucher_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else {
                $voucher_number = $this->input->post('voucher_number');
            }
        } else {
            $voucher_number = $this->input->post('voucher_number');
        } $customer = $this->general_model->getRecords('ledger_id,customer_name', 'customer', array(
            'customer_id' => $this->input->post('customer')));
        $ledger_customer = $customer[0]->ledger_id;
        if ($this->input->post('payment_mode') != "cash" && $this->input->post('payment_mode') != "bank" && $this->input->post('payment_mode') != "other payment mode") {
            $bank_acc_payment_mode = explode("/", $this->input->post('payment_mode'));
            $payment_mode = $bank_acc_payment_mode[0];
            $to_acc = $bank_acc_payment_mode[1];
            $ledger_bank_acc = $this->general_model->getRecords('ledger_id', 'bank_account', array(
                'bank_account_id' => $payment_mode[0]));
            $ledger_cash_bank = $ledger_bank_acc[0]->ledger_id;
        } else {
            $payment_mode = $this->input->post('payment_mode');
            $to_acc = $this->input->post('payment_mode');
            $ledger_cash_bank = $this->ledger_model->getDefaultLedger($this->input->post('payment_mode'));
        } $ledger_igst = $this->ledger_model->getDefaultLedger('IGST');
        $ledger_cgst = $this->ledger_model->getDefaultLedger('CGST');
        $ledger_sgst = $this->ledger_model->getDefaultLedger('SGST');
        $cheque_date = $this->input->post('cheque_date');
        if (!$cheque_date) {
            $cheque_date = null;
        } $refund_data = array(
            "voucher_date" => $this->input->post('voucher_date'),
            "voucher_number" => $voucher_number,
            "voucher_sub_total" => $this->input->post('total_sub_total'),
            "receipt_amount" => $this->input->post('receipt_amount'),
            "voucher_tax_amount" => $this->input->post('total_tax_amount'),
            "description" => $this->input->post('description'),
            "reference_id" => $this->input->post('advance_voucher_id'),
            "reference_number" => $this->input->post('reference_number'),
            "reference_type" => "advance",
            "from_account" => 'customer-' . $customer[0]->customer_name,
            "to_account" => $to_acc,
            "payment_mode" => $payment_mode,
            "payment_via" => $this->input->post('payment_via'),
            "ref_number" => $this->input->post('ref_number'),
            "bank_name" => $this->input->post('bank_name'),
            "cheque_date" => $cheque_date,
            "cheque_number" => $this->input->post('cheque_number'),
            "voucher_igst_amount" => $this->input->post('total_igst_amount'),
            "voucher_cgst_amount" => $this->input->post('total_cgst_amount'),
            "voucher_sgst_amount" => $this->input->post('total_sgst_amount'),
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
            "note2" => $this->input->post('note2'));
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $refund_data['currency_converted_amount'] = $this->input->post('receipt_amount');
        } else {
            $refund_data['currency_converted_amount'] = "0.00";
        }
        if ($payment_mode == "cash") {
            $refund_data['voucher_status'] = "0";
        } else {
            $refund_data['voucher_status'] = "1";
        }

        $data_main = array_map('trim', $refund_data);
        $refund_voucher_table = 'refund_voucher';
        $where = array(
            'refund_id' => $refund_id);
        if ($this->general_model->updateData($refund_voucher_table, $data_main, $where)) {
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
            foreach ($js_data as $key => $value) {
                if ($value == null) {
                    
                } else {
                    $item_id = $value->item_id;
                    $item_type = $value->item_type;
                    $quantity = $value->item_quantity;
                    $item_data[] = array(
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
                        "item_tax_amount" => $value->item_tax_amount,
                        "item_description" => $value->item_description,
                        "refund_voucher_id" => $refund_id);
                }
            } $string = 'refund_voucher_item_id,item_id,item_type';
            $table = 'refund_voucher_item';
            $where = array(
                'refund_voucher_id' => $refund_id,
                'delete_status' => 0);
            $old_refund_items = $this->general_model->getRecords($string, $table, $where, $order = "");
            if (count($old_refund_items) == count($item_data)) {
                foreach ($old_refund_items as $key => $value) {
                    $table = 'refund_voucher_item';
                    $where = array(
                        'refund_voucher_item_id' => $value->refund_voucher_item_id);
                    $this->general_model->updateData($table, $item_data[$key], $where);
                }
            } else if (count($old_refund_items) < count($item_data)) {
                foreach ($old_refund_items as $key => $value) {
                    $table = 'refund_voucher_item';
                    $where = array(
                        'refund_voucher_item_id' => $value->refund_voucher_item_id);
                    $this->general_model->updateData($table, $item_data[$key], $where);
                    $i = $key;
                } for ($j = $i + 1; $j < count($item_data); $j++) {
                    $table = 'refund_voucher_item';
                    $this->general_model->insertData($table, $item_data[$j]);
                }
            } else {
                foreach ($old_refund_items as $key => $value) {
                    $table = 'refund_voucher_item';
                    $where = array(
                        'refund_voucher_item_id' => $value->refund_voucher_item_id);
                    $this->general_model->updateData($table, $item_data[$key], $where);
                    $i = $key;
                    if (($key + 1) == count($item_data)) {
                        break;
                    }
                } for ($j = $i + 1; $j < count($old_refund_items); $j++) {
                    $table = 'refund_voucher_item';
                    $where = array(
                        'refund_voucher_item_id' => $old_refund_items[$j]->refund_voucher_item_id);
                    $refund_data = array(
                        'delete_status' => 1);
                    $this->general_model->updateData($table, $refund_data, $where);
                }
            } if (isset($data['other_modules_present']['accounts_module_id'])) {
                foreach ($data['access_sub_modules'] as $key => $value) {
                    if (isset($data['accounts_sub_module_id'])) {
                        if ($data['accounts_sub_module_id'] == $value->sub_module_id) {
                            $ledger_id = array(
                                'ledger_customer' => $ledger_customer,
                                'ledger_cash_bank' => $ledger_cash_bank,
                                'ledger_igst' => $ledger_igst,
                                'ledger_cgst' => $ledger_cgst,
                                'ledger_sgst' => $ledger_sgst);
                            $ledger_entry = array(
                                'grand_total' => $data_main['receipt_amount'],
                                'sub_total' => $data_main['voucher_sub_total'],
                                'igst_amount' => $data_main['voucher_igst_amount'],
                                'cgst_amount' => $data_main['voucher_cgst_amount'],
                                'sgst_amount' => $data_main['voucher_sgst_amount']);
                            $this->voucher_entry($refund_id, $ledger_id, $ledger_entry, "edit", $currency);
                        }
                    }
                }
            } redirect('refund_voucher', 'refresh');
        } else {
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
        $data['access_modules'] = $section_modules['modules'];
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
        } $product_module_id = $this->config->item('product_module');
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


        if ($inventory_access[0]->inventory_advanced == "yes") {
            $product_items = $this->common->refund_voucher_items_product_inventory_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        } else {
            $product_items = $this->common->refund_voucher_items_product_list_field($id);
            $voucher_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items = $this->common->refund_voucher_items_service_list_field($id);
        $voucher_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($voucher_items_product_items, $voucher_items_service_items);
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
        $dpcount = 0;
        $dtcount = 0;
        foreach ($data['items'] as $value) {
            $cgst = bcadd($cgst, $value->item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->item_sgst_amount, 2);
            if ($value->item_description != "" && $value->item_description != null) {
                $dpcount++;
            }
        } $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['dpcount'] = $dpcount;
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
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];
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


        if ($inventory_access[0]->inventory_advanced == "yes") {
            $product_items = $this->common->refund_voucher_items_product_inventory_list_field($id);
            $refund_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        } else {
            $product_items = $this->common->refund_voucher_items_product_list_field($id);
            $refund_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items = $this->common->refund_voucher_items_service_list_field($id);
        $refund_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($refund_product_items, $refund_service_items);
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


        $pdf = $this->general_model->getRecords('settings.*', 'settings', [
            'module_id' => 2,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);


        $pdf_json = $pdf[0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);



        $html = $this->load->view('refund_voucher/pdf1', $data, true);
        include(APPPATH . 'third_party/mpdf60/mpdf.php');
        $mpdf = new mPDF();
        $mpdf->allow_charset_conversion = true;
        $mpdf->charset_in = 'UTF-8';
        $mpdf->WriteHTML($html);
        $mpdf->Output($data['data'][0]->sales_invoice_number . '.pdf', 'I');
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
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $modules_present = array(
            'accounts_module_id' => $this->config->item('accounts_module'));
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];
        $data['advance_id'] = $this->general_model->getRecords('reference_id', 'refund_voucher', array(
            'refund_id' => $id));
        $advance_id = $data['advance_id'][0]->reference_id;
        $this->general_model->updateData('refund_voucher_item', array(
            'delete_status' => 1), array(
            'refund_voucher_id' => $id));
        if ($this->general_model->updateData('refund_voucher', array(
                    'delete_status' => 1), array(
                    'refund_id' => $id))) {
            if (isset($data['other_modules_present']['accounts_module_id'])) {
                foreach ($data['access_sub_modules'] as $key => $value) {
                    if (isset($data['accounts_sub_module_id'])) {
                        if ($data['accounts_sub_module_id'] == $value->sub_module_id) {
                            $this->general_model->updateData('accounts_refund_voucher', array(
                                'delete_status' => 1), array(
                                'refund_voucher_id' => $id));
                        }
                    }
                }
            } $this->general_model->updateData('advance_voucher', array(
                'refund_status' => 0), array(
                'advance_id' => $advance_id));
            $sales_id_amt = $this->general_model->getRecords('reference_id,receipt_amount,currency_converted_amount', 'advance_voucher', array(
                'advance_id' => $advance_id));
            if ($sales_id_amt[0]->reference_id != "" && $sales_id_amt[0]->reference_id != null && $sales_id_amt[0]->reference_id != 0) {
                $sales_data = $this->general_model->getRecords('*', 'sales', array(
                    'sales_id' => $sales_id_amt[0]->reference_id));
                $paid_amt = bcadd($sales_data[0]->sales_paid_amount, $sales_id_amt[0]->receipt_amount, 2);
                $conv_paid_amt = bcadd($sales_data[0]->converted_paid_amount, $sales_id_amt[0]->currency_converted_amount, 2);
                $sales_where = array(
                    'sales_id' => $sales_id_amt[0]->reference_id);
                $sales_table = 'sales';
                $sales_update_data = array(
                    'sales_paid_amount' => $paid_amt,
                    'converted_paid_amount' => $conv_paid_amt);
                $this->general_model->updateData($sales_table, $sales_update_data, $sales_where);
            } $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'refund_voucher',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Refund Voucher Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('refund_voucher', 'refresh');
        } else {
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
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
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
        } $email_sub_module = 0;
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


            if ($inventory_access[0]->inventory_advanced == "yes") {
                $product_items = $this->common->refund_voucher_items_product_inventory_list_field($id);
                $refund_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else {
                $product_items = $this->common->refund_voucher_items_product_list_field($id);
                $refund_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }

            $service_items = $this->common->refund_voucher_items_service_list_field($id);
            $refund_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items'] = array_merge($refund_product_items, $refund_service_items);
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
    
     function index() {
        $this->load->view('refund_voucher/list');
    }
}
