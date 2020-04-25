<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_challan extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'product_model',
            'service_model']);
        $this->modules = $this->get_modules();
    }

    function index() {
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($delivery_challan_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        /* Modules Present */
        $data['delivery_challan_module_id'] = $delivery_challan_module_id;
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id'] = $this->config->item('email_module');
        $data['recurrence_module_id'] = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id'] = $this->config->item('recurrence_sub_module');

        if (!empty($this->input->post())) {
            $currency = $this->getBranchCurrencyCode();
            $currency_code = $currency[0]->currency_code;
            $currency_symbol = $currency[0]->currency_symbol;

            $columns = array(
                0 => 'date',
                1 => 'invoice',
                2 => 'customer',
                3 => 'grand_total',
                4 => 'currency_converted_amount',
                5 => 'added_user',
                6 => 'action',);
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->delivery_challan_list_field();
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
                    $nestedData['grand_total'] = $currency_symbol . ' ' . $post->delivery_challan_grand_total;
                    $nestedData['customer'] = $post->customer_name;

                    if ($post->reference_type == 'sales') {
                        $sales_fields = $this->general_model->getRecords('*', 'sales', array('sales_id' => $post->reference_id));

                        $nestedData['grand_total'] = $sales_fields[0]->sales_grand_total;
                    } elseif ($post->reference_type == 'sales_credit_note') {
                        $sales_fields = $this->general_model->getRecords('*', 'sales_credit_note', array('sales_credit_note_id' => $post->reference_id));

                        $nestedData['grand_total'] = $sales_fields[0]->sales_credit_note_grand_total;
                    } elseif ($post->reference_type == 'sales_debit_note') {
                        $sales_fields = $this->general_model->getRecords('*', 'sales_debit_note', array('sales_debit_note_id' => $post->reference_id));

                        $nestedData['grand_total'] = $sales_fields[0]->sales_debit_note_grand_total;
                    } elseif ($post->reference_type == 'purchase_return') {
                        $supplier = $this->general_model->getRecords('*', 'supplier', array('supplier_id' => $post->delivery_challan_party_id));
                        $nestedData['customer'] = $supplier[0]->supplier_name;

                        $sales_fields = $this->general_model->getRecords('*', 'purchase_return', array('purchase_return_id' => $post->reference_id));

                        $nestedData['grand_total'] = $sales_fields[0]->purchase_return_grand_total;
                    }
                    $nestedData['grand_total'] = $this->precise_amount($nestedData['grand_total'], $access_common_settings[0]->amount_precision);

                    $nestedData['date'] = date('d-m-Y',strtotime($post->delivery_challan_date));
                    $nestedData['invoice'] = $post->delivery_challan_invoice_number;


                    $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $delivery_challan_id = $this->encryption_url->encode($post->delivery_challan_id);
                    
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    if (in_array($delivery_challan_module_id, $data['active_view'])) {
                        $cols .= '<span><a href="' . base_url('delivery_challan/pdf/') . $delivery_challan_id . '"  class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Download PDF" target="_blank"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }
                    if (in_array($data['email_module_id'], $data['active_view'])) {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules'])) {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail"><a data-id="' . $delivery_challan_id . '" data-name="regular" href="javascript:void(0);" class="btn btn-app pdf_button composeMail" data-toggle="tooltip" data-placement="bottom" title="Email Delivery Challan"><i class="fa fa-envelope-o"></i></a></span>';
                        }
                    }

                    if (in_array($delivery_challan_module_id, $data['active_delete'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $delivery_challan_id . '" data-path="delivery_challan/delete" class="delete_button"><a  href="#"  class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Delete Delivery Challan" ><i class="fa fa-trash-o"></i></a></span>';
                    }                    
                    $cols .= '</div></div>';                   
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
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
            $this->load->view('delivery_challan/list', $data);
        }
    }

    function get_sales_invoice_number() {
        $customer_id = $this->input->post('customer_id');
        $invoice_type = $this->input->post('invoice_type');
        $data['sales_invoice'] = array();
        $data['sales_debit_invoice'] = array();
        $data['sales_credit_invoice'] = array();
        $data['purchase_return'] = array();

        if ($invoice_type == 'customer') {
            /* Sales Invoice */
            $invoice_data = $this->common->get_customer_sales_invoice_number($customer_id, "1");
            $data['sales_invoice'] = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);
            if (!empty($data['sales_invoice'])) {
                foreach ($data['sales_invoice'] as $key => $value) {
                    $data['sales_invoice'][$key]->en_id = $this->encryption_url->encode($value->sales_id);
                }
            }
            /* sales credit note invoice */
            $invoice_data = $this->common->get_customer_sales_credit_invoice_number($customer_id, "1");
            $data['sales_credit_invoice'] = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);
            if (!empty($data['sales_credit_invoice'])) {
                foreach ($data['sales_credit_invoice'] as $key => $value) {
                    $data['sales_credit_invoice'][$key]->en_id = $this->encryption_url->encode($value->sales_credit_note_id);
                }
            }
            /* sales debit invoices */
            $invoice_data = $this->common->get_customer_sales_debit_invoice_number($customer_id, "1");
            $data['sales_debit_invoice'] = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);
            if (!empty($data['sales_debit_invoice'])) {
                foreach ($data['sales_debit_invoice'] as $key => $value) {
                    $data['sales_debit_invoice'][$key]->en_id = $this->encryption_url->encode($value->sales_debit_note_id);
                }
            }
        } else {
            /* sales debit invoices */
            $invoice_data = $this->common->get_supplier_purchase_return_invoice_number($customer_id, "1");
            $data['purchase_return'] = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);

            if (!empty($data['purchase_return'])) {
                foreach ($data['purchase_return'] as $key => $value) {
                    $data['purchase_return'][$key]->en_id = $this->encryption_url->encode($value->purchase_return_id);
                }
            }
        }
        echo json_encode($data);
    }

    function add() {
        $data = $this->get_default_country_state();
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($delivery_challan_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['delivery_challan_module_id'] = $delivery_challan_module_id;
        $data['module_id'] = $delivery_challan_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['supplier_module_id'] = $this->config->item('supplier_module');
        $data['customer_module_id'] = $this->config->item('customer_module');
        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['discount_module_id'] = $this->config->item('discount_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');


        $data['customer'] = $this->customer_call();
        $data['supplier'] = $this->supplier_call();
        $data['currency'] = $this->currency_call();

        if ($data['access_settings'][0]->discount_visible == "yes") {

            $data['discount'] = $this->discount_call();
        }

        if ($data['access_settings'][0]->tax_type == "gst" || $data['access_settings'][0]->item_access == "single_tax") {

            $data['tax'] = $this->tax_call();
        }


        if ($data['access_settings'][0]->item_access == "service" || $data['access_settings'][0]->item_access == "both") {

            $data['sac'] = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }
        if ($data['access_settings'][0]->item_access == "product" || $data['access_settings'][0]->item_access == "both") {

            $data['inventory_access'] = $data['access_common_settings'][0]->inventory_advanced;
            $data['product_category'] = $this->product_category_call();
            $data['uqc'] = $this->uqc_call();
            $data['chapter'] = $this->chapter_call();
            $data['hsn'] = $this->hsn_call();

            if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }
        }
        $access_settings = $data['access_settings'];
        $primary_id = "delivery_challan_id";
        $table_name = 'delivery_challan';
        $date_field_name = "delivery_challan_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('delivery_challan/add', $data);
    }

    public function add_delivery_challan() {

        $data = $this->get_default_country_state();
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $module_id = $delivery_challan_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $section_modules = $this->get_section_modules($delivery_challan_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['delivery_challan_module_id'] = $delivery_challan_module_id;
        $data['module_id'] = $delivery_challan_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['customer_module_id'] = $this->config->item('customer_module');
        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['discount_module_id'] = $this->config->item('discount_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $access_settings = $section_modules['access_settings'];

        if ($access_settings[0]->invoice_creation == "automatic") {
            $primary_id = "delivery_challan_id";
            $table_name = 'delivery_challan';
            $date_field_name = "delivery_challan_date";
            $current_date = date('Y-m-d',strtotime($this->input->post('invoice_date')));

            $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }

        $customer = explode("-", $this->input->post('customer'));

        $delivery_challan_data = array(
            "delivery_challan_date" => date('Y-m-d',strtotime($this->input->post('invoice_date'))),
            "delivery_challan_invoice_number" => $invoice_number,
            "delivery_challan_party_id" => $this->input->post('customer'),
            "delivery_challan_party_type" => ($this->input->post('reference_type') == 'purchase_return' ? "supplier" : "customer"),
            "delivery_challan_party_id" => $this->input->post('customer'),
            'reference_type' => $this->input->post('reference_type'),
            'reference_id' => $this->input->post('reference_id'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID')
        );

        $data_main = array_map('trim', $delivery_challan_data);
        $delivery_table = 'delivery_challan';

        if ($delivery_challan_id = $this->general_model->insertData($delivery_table, $data_main)) {
            $successMsg = 'Delivery Challan Added Successfully';
            $this->session->set_flashdata('delivery_challan_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $delivery_challan_id,
                'table_name' => $delivery_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Delivery Challan Inserted');
            $data_main['delivery_challan_id'] = $delivery_challan_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
        } else {
            $errorMsg = 'Delivery Challan Add Unsuccessful';
            $this->session->set_flashdata('delivery_challan_error',$errorMsg);
            redirect('Delivery_challan', 'refresh');
        }

        $action = $this->input->post('submit');

        redirect('Delivery_challan', 'refresh');
    }

    public function view_sales_credit_note($id) {
        $id = $this->encryption_url->decode($id);
        $data = array();
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $sales_credit_note_module_id = $this->config->item('sales_credit_note_module');
        $data['email_module_id'] = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id'] = $sales_credit_note_module_id;
        $data['sales_credit_note_module_id'] = $sales_credit_note_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_credit_note_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $sales_credit_note_data = $this->common->sales_credit_note_list_field1($id);

        $data['data'] = $this->general_model->getJoinRecords($sales_credit_note_data['string'], $sales_credit_note_data['table'], $sales_credit_note_data['where'], $sales_credit_note_data['join']);

        $item_types = $this->general_model->getRecords('item_type,sales_credit_note_item_description', 'sales_credit_note_item', array(
            'sales_credit_note_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->sales_credit_note_item_description != "") {
                $description++;
            }

            if ($value->item_type == "service") {
                $service = 1;
            } else

            if ($value->item_type == "product") {
                $product = 1;
            } else

            if ($value->item_type == "product_inventory") {
                $product = 2;
            }
        }

        $sales_credit_note_service_items = array();
        $sales_credit_note_product_items = array();


        if (($data['data'][0]->sales_credit_note_nature_of_supply == "service" || $data['data'][0]->sales_credit_note_nature_of_supply == "both") && $service == 1) {

            $service_items = $this->common->sales_credit_note_items_service_list_field($id);
            $sales_credit_note_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->sales_credit_note_nature_of_supply == "product" || $data['data'][0]->sales_credit_note_nature_of_supply == "both") {

            if ($product == 2) {
                $product_items = $this->common->sales_credit_note_items_product_inventory_list_field($id);
                $sales_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else

            if ($product == 1) {
                $product_items = $this->common->sales_credit_note_items_product_list_field($id);
                $sales_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
        }



        $data['items'] = array_merge($sales_credit_note_product_items, $sales_credit_note_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;
        $cessExist = 0;

        if ($data['data'][0]->sales_credit_note_tax_cess_amount > 0) {
            $cessExist = 1;
        }

        if ($data['data'][0]->sales_credit_note_tax_amount > 0 && $data['data'][0]->sales_credit_note_igst_amount > 0 && ($data['data'][0]->sales_credit_note_cgst_amount == 0 && $data['data'][0]->sales_credit_note_sgst_amount == 0)) {

            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->sales_credit_note_tax_amount > 0 && ($data['data'][0]->sales_credit_note_cgst_amount > 0 || $data['data'][0]->sales_credit_note_sgst_amount > 0) && $data['data'][0]->sales_credit_note_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->sales_credit_note_tax_amount > 0 && ($data['data'][0]->sales_credit_note_igst_amount == 0 && $data['data'][0]->sales_credit_note_cgst_amount == 0 && $data['data'][0]->sales_credit_note_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->sales_credit_note_tax_amount == 0 && ($data['data'][0]->sales_credit_note_igst_amount == 0 && $data['data'][0]->sales_credit_note_cgst_amount == 0 && $data['data'][0]->sales_credit_note_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->sales_credit_note_tds_amount > 0 || $data['data'][0]->sales_credit_note_tcs_amount > 0) {
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->sales_credit_note_discount_amount > 0) {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0) {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->sales_credit_note_billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['tax_exist'] = $taxExist;
        $data['cess_exist'] = $cessExist;
        $data['is_utgst'] = $is_utgst;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;

        $this->load->view('sales_credit_note/view', $data);
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        /*$delivery_challan_module_id = $this->config->item('delivery_challan_module');*/
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $data['module_id'] = $delivery_challan_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($delivery_challan_module_id, $modules, $privilege);
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'customer_module_id' => $customer_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $this->general_model->updateData('delivery_challan_item', array(
            'delete_status' => 1), array(
            'delivery_challan_id' => $id));
        if ($this->general_model->updateData('delivery_challan', array(
                    'delete_status' => 1), array(
                    'delivery_challan_id' => $id))) {
            $successMsg = 'Delivery Challan Deleted Successfully';
            $this->session->set_flashdata('delivery_challan_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'delivery_challan',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Delivery Challan Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('delivery_challan');
        } else {
            $errorMsg = 'Delivery Challan Delete Unsuccessful';
            $this->session->set_flashdata('delivery_challan_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Category can not be Deleted.');
            redirect("quotation", 'refresh');
        }
    }

    public function pdf($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $data['module_id'] = $delivery_challan_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($delivery_challan_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);

        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $data['currency'] = $this->currency_call();
        $delivery_challan_data = $this->common->delivery_challan_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($delivery_challan_data['string'], $delivery_challan_data['table'], $delivery_challan_data['where'], $delivery_challan_data['join']);

        $reference_id = $data['data'][0]->reference_id;
        $reference_type = $data['data'][0]->reference_type;
        $delivery_challan_data = $data['data'];

        if ($reference_type == 'sales_credit_note') {
            $data = $this->sales_credit_details($this->encryption_url->encode($reference_id));
            $data['data'][0]->sales_credit_note_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->sales_credit_note_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        } elseif ($reference_type == 'sales_debit_note') {

            $data = $this->sales_debit_details($this->encryption_url->encode($reference_id));
            $data['data'][0]->sales_debit_note_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->sales_debit_note_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        } elseif ($reference_type == 'sales') {

            $data = $this->sales_details($this->encryption_url->encode($reference_id));
            $data['data'][0]->sales_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->sales_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        } elseif ($reference_type == 'purchase_return') {

            $data = $this->PurchaseReturnDetails($this->encryption_url->encode($reference_id));

            $data['data'][0]->purchase_return_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->purchase_return_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        }
        $data['title'] = 'Delivery Challan';

        $pdf_json = $data['access_settings'][0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);

        $html = $this->load->view($reference_type . '/pdf', $data, true);

        include APPPATH . "third_party/dompdf/autoload.inc.php";

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

        $dompdf->stream($delivery_challan_data[0]->delivery_challan_invoice_number, array(
            'Attachment' => 0));
    }

    public function pdf_OLD($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $data['module_id'] = $delivery_challan_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($delivery_challan_module_id, $modules, $privilege);
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
        $delivery_challan_data = $this->common->delivery_challan_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($delivery_challan_data['string'], $delivery_challan_data['table'], $delivery_challan_data['where'], $delivery_challan_data['join']);
        $country_data = $this->common->country_field($data['data'][0]->delivery_challan_billing_country_id);
        $data['data_country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data = $this->common->state_field($data['data'][0]->delivery_challan_billing_country_id, $data['data'][0]->delivery_challan_billing_state_id);
        $data['data_state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));


        if ($inventory_access[0]->inventory_advanced == "yes") {
            $product_items = $this->common->delivery_challan_items_product_inventory_list_field($id);
            $delivery_challan_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        } else {
            $product_items = $this->common->delivery_challan_items_product_list_field($id);
            $delivery_challan_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items = $this->common->delivery_challan_items_service_list_field($id);
        $delivery_challan_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($delivery_challan_product_items, $delivery_challan_service_items);
        if ($delivery_challan_product_items && $delivery_challan_service_items) {
            $nature_of_supply = "Product/Service";
        } elseif ($delivery_challan_product_items) {
            $nature_of_supply = "Product";
        } elseif ($delivery_challan_service_items) {
            $nature_of_supply = "Service";
        } $data['nature_of_supply'] = $nature_of_supply;
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city'] = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data['currency'] = $this->currency_call();
        $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        $dpcount = 0;
        $dtcount = 0;
        foreach ($data['items'] as $value) {
            $cgst = bcadd($cgst, $value->delivery_challan_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->delivery_challan_item_sgst_amount, 2);
            if ($value->delivery_challan_item_description != "" && $value->delivery_challan_item_description != null) {
                $dpcount++;
            } if ($value->delivery_challan_item_discount_amount != "" && $value->delivery_challan_item_discount_amount != null && $value->delivery_challan_item_discount_amount != 0) {
                $dtcount++;
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
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $pdf = $this->general_model->getRecords('settings.*', 'settings', [
            'module_id' => 2,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);

        $pdf_json = $pdf[0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);


        $html = $this->load->view('delivery_challan/pdf1', $data, true);
        include(APPPATH . 'third_party/mpdf60/mpdf.php');
        $mpdf = new mPDF();
        $mpdf->allow_charset_conversion = true;
        $mpdf->charset_in = 'UTF-8';
        $mpdf->WriteHTML($html);
        $mpdf->Output($data['data'][0]->delivery_challan_invoice_number . '.pdf', 'I');
    }

    public function email_popup($id){
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $data['module_id'] = $delivery_challan_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($delivery_challan_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);

        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $data['currency'] = $this->currency_call();
        $delivery_challan_data = $this->common->delivery_challan_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($delivery_challan_data['string'], $delivery_challan_data['table'], $delivery_challan_data['where'], $delivery_challan_data['join']);

        $reference_id = $data['data'][0]->reference_id;
        $reference_type = $data['data'][0]->reference_type;
        $delivery_challan_data = $data['data'];

        if ($reference_type == 'sales_credit_note') {
            $data = $this->sales_credit_details($this->encryption_url->encode($reference_id));
            $data['data'][0]->sales_credit_note_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->sales_credit_note_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        } elseif ($reference_type == 'sales_debit_note') {

            $data = $this->sales_debit_details($this->encryption_url->encode($reference_id));
            $data['data'][0]->sales_debit_note_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->sales_debit_note_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        } elseif ($reference_type == 'sales') {

            $data = $this->sales_details($this->encryption_url->encode($reference_id));
            $data['data'][0]->sales_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->sales_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        } elseif ($reference_type == 'purchase_return') {

            $data = $this->PurchaseReturnDetails($this->encryption_url->encode($reference_id));

            $data['data'][0]->purchase_return_date = $delivery_challan_data[0]->delivery_challan_date;
            $data['data'][0]->purchase_return_invoice_number = $delivery_challan_data[0]->delivery_challan_invoice_number;
        }
        $data['title'] = 'Delivery Challan';

        $pdf_json = $data['access_settings'][0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);

        $html = $this->load->view($reference_type . '/pdf', $data, true);

        include APPPATH . "third_party/dompdf/autoload.inc.php";

        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();
        $paper_size  = 'a4';
        $orientation = 'portrait';
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        $file_path                      = "././pdf_form/";
        $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $delivery_challan_data[0]->delivery_challan_invoice_number);
        $file_name = str_replace('/','_',$file_name);
        file_put_contents($file_path . $file_name . '.pdf', $output);


        $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
        $data['pdf_file_name']          = $file_name . '.pdf';
        /*$sales_data                     = $this->common->sales_list_field1($id);
        $data['data']                   = $this->general_model->getJoinRecords($sales_data['string'] , $sales_data['table'] , $sales_data['where'] , $sales_data['join']);*/
        $branch_data                    = $this->common->branch_field();
        $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'] , $branch_data['table'] , $branch_data['where'] , $branch_data['join'] , $branch_data['order']);
        $data['email_setup']            = $this->general_model->getRecords('*' , 'email_setup' , array(
            'delete_status' => 0 ,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ,
            'added_user_id' => $this->session->userdata('SESS_USER_ID') ));
        $data['email_template']   = $this->general_model->getRecords('*' , 'email_template' , array(
            'module_id'     => $delivery_challan_module_id ,
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
