<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Quotation extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'product_model',
            'service_model']);
        $this->modules = $this->get_modules();
    }

    public function index() {
        $quotation_module_id = $this->config->item('quotation_module');
        $data['quotation_module_id'] = $quotation_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /*if($this->session->flashdata('quotation_success')){
            $data['quotation_success'] = $this->session->flashdata('quotation_success');
            //$this->session->unset_userdata('quotation_success');
        }elseif($this->session->userdata('quotation_error')){
            $data['quotation_error'] = $this->session->userdata('quotation_error');
            $this->session->unset_userdata('quotation_error');
        }*/
        $access_common_settings = $section_modules['access_common_settings'];
        /* Modules Present */
        $sales_module_id = $this->config->item('sales_module');
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id'] = $this->config->item('email_module');
        $data['recurrence_module_id'] = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id'] = $this->config->item('recurrence_sub_module');
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'q.quotation_id',
                1 => 'q.quotation_date',
                2 => 'c.customer_name',
                3 => 'quotation_grand_total');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->quotation_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
             if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
            if (empty($this->input->post('search')['value'])) {
                // $list_data['limit'] = $limit;
                // $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                // $list_data['limit'] = $limit;
                // $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $send_data = array();
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency[0]->currency_code;
            $data['currency_symbol'] = $currency[0]->currency_symbol;
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $quotation_id = $this->encryption_url->encode($post->quotation_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->quotation_date));
                    $nestedData['customer'] = $post->customer_name . ' (' . $post->quotation_invoice_number . ') ';
                    if (in_array($quotation_module_id, $data['active_view'])) {
                        $nestedData['customer'] = $post->customer_name . ' (<a href="' . base_url('quotation/view/') . $quotation_id . '">' . $post->quotation_invoice_number . '</a>) ';
                    }
                    if($this->session->userdata('SESS_FIRM_ID') == $this->config->item('LeatherCraft')){
                        $nestedData['customer'] = $post->customer_name .' - '. $post->store_location. ' (<a href="' . base_url('quotation/view/') . $quotation_id . '">' . $post->quotation_invoice_number . '</a>) ';
                    }
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $this->precise_amount($post->quotation_grand_total, $access_common_settings[0]->amount_precision);
                    $nestedData['converted_grand_total'] = $this->precise_amount($post->converted_grand_total, $access_common_settings[0]->amount_precision);
                    $is_complate = 0;
                    if ($post->sales_id != "" || $post->sales_id != 0 || $post->sales_id != null) {
                        $is_complate = 1;
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } else {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($quotation_module_id, $data['active_view'])) {
                        $cols .= '<span><a href="' . base_url('quotation/view/') . $quotation_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="View Quotation">
                                    <i class="fa fa-eye"></i>
                            </a></span>';
                    }
                    if (in_array($quotation_module_id, $data['active_edit']) && $is_complate == '0') {
                        if ($post->quotation_paid_amount == 0 && $post->credit_note_amount == 0 && $post->debit_note_amount == 0) {
                            $cols .= '<span><a href="' . base_url('quotation/edit/') . $quotation_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Edit Quotation">
                                    <i class="fa fa-pencil"></i></a></span>';
                        }
                    }
                    if (in_array($sales_module_id, $data['active_edit']) && $is_complate == '0') {
                        $cols .= '<span><a href="' . base_url('quotation/convert_quotation/') . $quotation_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Move to Sales">
                                    <i class="fa fa-reply"></i>
                            </a></span>';
                    }
                    if (in_array($quotation_module_id, $data['active_view'])) {
                        $customer_currency_code = $this->getCurrencyInfo($post->currency_id);
                        $customer_curr_code = '';
                        if (!empty($customer_currency_code))
                            $customer_curr_code = $customer_currency_code[0]->currency_code;
                        $cols .= '<span><a href="' . base_url('quotation/pdf/') . $quotation_id . '" class="btn btn-app pdf_button" b_curr="' . $this->session->userdata('SESS_DEFAULT_CURRENCY') . '"  b_code="' . $data['currency_code'] . '" c_code="' . $customer_curr_code . '" c_curr="' . $post->currency_id . '" data-id="' . $quotation_id . '" data-toggle="tooltip" data-placement="bottom" title="Download PDF" target="_blank"><i class="fa fa-file-pdf-o"></i></a></span>';
                        /* $cols .= '<a data-name="regular" data-target="#pdf_type_modal" href="' . base_url('quotation/pdf/') . $quotation_id . '" target="_blank" data-placement="bottom" data-toggle="tooltip" title="Quotation PDF" class="btn btn-app pdf_button"><i class="fa fa-file-pdf-o"></i></a>'; */
                    }
                    if (in_array($data['email_module_id'], $data['active_view'])) {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules'])) {
                            /* $cols .= '<a href="' . base_url('quotation/email/') . $quotation_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Email Quotation">
                              <i class="fa fa-envelope-o"></i>
                              </a>'; */

                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail"><a data-id="' . $quotation_id . '" data-name="regular" href="javascript:void(0);" class="btn btn-app composeMail" data-placement="bottom" data-toggle="tooltip" title="Email Quotation"><i class="fa fa-envelope-o"></i></a></span>';
                        }
                    }
                    /* if (in_array($data['recurrence_module_id'], $data['active_add']))
                      {

                      if (in_array($data['recurrence_sub_module_id'], $data['access_sub_modules']))
                      {
                      $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#recurrence_invoice"><a href="#" class="btn btn-app recurrence_invoice"  data-id="' . $quotation_id . '" data-type="quotation" href="#" data-toggle="tooltip" data-placement="bottom" title="Generate Recurrence Quotation"><i class="fa fa-eye"></i></a></span>';
                      }
                      } */
                    if (in_array($quotation_module_id, $data['active_delete'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a class="btn btn-app delete_button" data-id="' . $quotation_id . '" data-path="quotation/delete" href="#" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" data-placement="bottom" data-toggle="tooltip" title="Delete Quotation">
                                    <i class="fa fa-trash-o"></i></a>
                            </span>';
                    }
                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                        $conversion_date = $post->currency_converted_date;
                        if($conversion_date == '0000-00-00') $conversion_date = $post->added_date;
                        $conversion_date = date('d-m-Y',strtotime($conversion_date));
                        
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#convert_currency_modal"><a href="javascript:void(0);" class="btn btn-app convert_currency" data-id="' . $quotation_id . '" data-path="quotation/convert_currency" data-conversion_date="'.$conversion_date.'" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $this->precise_amount($post->quotation_grand_total, $access_common_settings[0]->amount_precision) . '" data-rate="' . $this->precise_amount($post->currency_converted_rate, $access_common_settings[0]->amount_precision) . '" data-toggle="tooltip" data-placement="bottom" title="Convert Currency"><i class="fa fa-exchange"></i></a></span>';

                       /* $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#convert_currency_modal"><a href="#" class="btn btn-app convert_currency"  data-id="' . $quotation_id . '" data-path="quotation/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->quotation_grand_total . '" data-toggle="tooltip" data-placement="bottom" title="Convert Currency"><i class="fa fa-exchange"></i></a></span>';*/
                    }
                    $cols .= '</div>';
                    $cols .= '</div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
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
            $data['currency'] = $this->currency_call();

            $this->load->view('quotation/list', $data);
        }
    }

    public function add() {
        $data = $this->get_default_country_state();
        $quotation_module_id = $this->config->item('quotation_module');
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['quotation_module_id'] = $quotation_module_id;
        $data['module_id'] = $quotation_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['customer_module_id'] = $this->config->item('customer_module');
        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['discount_module_id'] = $this->config->item('discount_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        $data['uqc_module_id']        = $this->config->item('uqc_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['customer'] = $this->customer_call();
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
            $data['uqc_service']      = $this->uqc_product_service_call('service');
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['chapter'] = $this->chapter_call();
            $data['hsn'] = $this->hsn_call();
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();

            if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }
        }

        $access_settings = $data['access_settings'];
        $primary_id = "quotation_id";
        $table_name = $this->config->item('quotation_table');
        $date_field_name = "quotation_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('quotation/add', $data);
    }

    public function add_quotation() {
        $data = $this->get_default_country_state();
        $quotation_module_id = $this->config->item('quotation_module');
        $module_id = $quotation_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['quotation_module_id'] = $quotation_module_id;
        $data['module_id'] = $quotation_module_id;
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

        $currency = $this->input->post('currency_id');

        if ($access_settings[0]->invoice_creation == "automatic") {
            $primary_id = "quotation_id";
            $table_name = $this->config->item('quotation_table');
            $date_field_name = "quotation_date";
            $current_date = $this->input->post('invoice_date');
            $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }

        $customer = explode("-", $this->input->post('customer'));
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;

        $quotation_data = array(
            "quotation_date" => date('Y-m-d', strtotime($this->input->post('invoice_date'))),
            "quotation_invoice_number" => $invoice_number,
            "quotation_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "quotation_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "quotation_discount_amount" => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0,
            "quotation_tax_cess_amount" => 0,
            "quotation_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "quotation_taxable_value" => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0,
            "quotation_tds_amount" => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0,
            "quotation_tcs_amount" => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0,
            "quotation_igst_amount" => 0,
            "quotation_cgst_amount" => 0,
            "quotation_sgst_amount" => 0,
            "from_account" => 'customer',
            "to_account" => 'quotation',
            "quotation_paid_amount" => 0,
            "credit_note_amount" => 0,
            "debit_note_amount" => 0,
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "quotation_party_id" => $this->input->post('customer'),
            "ship_to_customer_id" => $this->input->post('ship_to'),
            "quotation_party_type" => "customer",
            "quotation_nature_of_supply" => $this->input->post('nature_of_supply'),
            "quotation_order_number" => $this->input->post('order_number'),
            "quotation_type_of_supply" => $this->input->post('type_of_supply'),
            "quotation_gst_payable" => $this->input->post('gst_payable'),
            "quotation_billing_country_id" => $this->input->post('billing_country'),
            "quotation_billing_state_id" => $this->input->post('billing_state'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
            "updated_date" => "",
            "updated_user_id" => "",
            "warehouse_id" => "",
            "transporter_name" => $this->input->post('transporter_name'),
            "transporter_gst_number" => $this->input->post('transporter_gst_number'),
            "lr_no" => $this->input->post('lr_no'),
            "vehicle_no" => $this->input->post('vehicle_no'),
            "mode_of_shipment" => $this->input->post('mode_of_shipment'),
            "ship_by" => $this->input->post('ship_by'),
            "net_weight" => $this->input->post('net_weight'),
            "gross_weight" => $this->input->post('gross_weight'),
            "origin" => $this->input->post('origin'),
            "destination" => $this->input->post('destination'),
            "shipping_type" => $this->input->post('shipping_type'),
            "shipping_type_place" => $this->input->post('shipping_type_place'),
            "lead_time" => $this->input->post('lead_time'),
            "shipping_address_id" => $this->input->post('shipping_address'),
            "billing_address_id" => $this->input->post('billing_address'),
            "warranty" => $this->input->post('warranty'),
            "payment_mode" => $this->input->post('payment_mode'),
            "freight_charge_amount" => $this->input->post('freight_charge_amount') ? (float) $this->input->post('freight_charge_amount') : 0,
            "freight_charge_tax_percentage" => $this->input->post('freight_charge_tax_percentage') ? (float) $this->input->post('freight_charge_tax_percentage') : 0,
            "freight_charge_tax_amount" => $this->input->post('freight_charge_tax_amount') ? (float) $this->input->post('freight_charge_tax_amount') : 0,
            "total_freight_charge" => $this->input->post('total_freight_charge') ? (float) $this->input->post('total_freight_charge') : 0,
            "insurance_charge_amount" => $this->input->post('insurance_charge_amount') ? (float) $this->input->post('insurance_charge_amount') : 0,
            "insurance_charge_tax_percentage" => $this->input->post('insurance_charge_tax_percentage') ? (float) $this->input->post('insurance_charge_tax_percentage') : 0,
            "insurance_charge_tax_amount" => $this->input->post('insurance_charge_tax_amount') ? (float) $this->input->post('insurance_charge_tax_amount') : 0,
            "total_insurance_charge" => $this->input->post('total_insurance_charge') ? (float) $this->input->post('total_insurance_charge') : 0,
            "packing_charge_amount" => $this->input->post('packing_charge_amount') ? (float) $this->input->post('packing_charge_amount') : 0,
            "packing_charge_tax_percentage" => $this->input->post('packing_charge_tax_percentage') ? (float) $this->input->post('packing_charge_tax_percentage') : 0,
            "packing_charge_tax_amount" => $this->input->post('packing_charge_tax_amount') ? (float) $this->input->post('packing_charge_tax_amount') : 0,
            "total_packing_charge" => $this->input->post('total_packing_charge') ? (float) $this->input->post('total_packing_charge') : 0,
            "incidental_charge_amount" => $this->input->post('incidental_charge_amount') ? (float) $this->input->post('incidental_charge_amount') : 0,
            "incidental_charge_tax_percentage" => $this->input->post('incidental_charge_tax_percentage') ? (float) $this->input->post('incidental_charge_tax_percentage') : 0,
            "incidental_charge_tax_amount" => $this->input->post('incidental_charge_tax_amount') ? (float) $this->input->post('incidental_charge_tax_amount') : 0,
            "total_incidental_charge" => $this->input->post('total_incidental_charge') ? (float) $this->input->post('total_incidental_charge') : 0,
            "inclusion_other_charge_amount" => $this->input->post('inclusion_other_charge_amount') ? (float) $this->input->post('inclusion_other_charge_amount') : 0,
            "inclusion_other_charge_tax_percentage" => $this->input->post('inclusion_other_charge_tax_percentage') ? (float) $this->input->post('inclusion_other_charge_tax_percentage') : 0,
            "inclusion_other_charge_tax_amount" => $this->input->post('inclusion_other_charge_tax_amount') ? (float) $this->input->post('inclusion_other_charge_tax_amount') : 0,
            "total_inclusion_other_charge" => $this->input->post('total_other_inclusive_charge') ? (float) $this->input->post('total_other_inclusive_charge') : 0,
            "exclusion_other_charge_amount" => $this->input->post('exclusion_other_charge_amount') ? (float) $this->input->post('exclusion_other_charge_amount') : 0,
            "exclusion_other_charge_tax_percentage" => $this->input->post('exclusion_other_charge_tax_percentage') ? (float) $this->input->post('exclusion_other_charge_tax_percentage') : 0,
            "exclusion_other_charge_tax_amount" => $this->input->post('exclusion_other_charge_tax_amount') ? (float) $this->input->post('exclusion_other_charge_tax_amount') : 0,
            "total_exclusion_other_charge" => $this->input->post('total_other_exclusive_charge') ? (float) $this->input->post('total_other_exclusive_charge') : 0,
            "total_other_amount" => $this->input->post('total_other_amount') ? (float) $this->input->post('total_other_amount') : 0,
            "total_other_taxable_amount" => $this->input->post('total_other_taxable_amount') ? (float) $this->input->post('total_other_taxable_amount') : 0,
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2')
        );

        $quotation_data['freight_charge_tax_id'] = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $quotation_data['insurance_charge_tax_id'] = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $quotation_data['packing_charge_tax_id'] = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $quotation_data['incidental_charge_tax_id'] = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $quotation_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $quotation_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;

        if(@$this->input->post('cash_discount')){
            $quotation_data['quotation_cash_discount'] = $this->input->post('cash_discount');
        }

        $round_off_value = $quotation_data['quotation_grand_total'];

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" && $this->input->post('round_off_key') == "yes") {
            if ($this->input->post('round_off_value') != "" && $this->input->post('round_off_value') > 0) {
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $quotation_data['round_off_amount'] = bcsub($quotation_data['quotation_grand_total'], $round_off_value, $section_modules['access_common_settings'][0]->amount_precision);

        $quotation_data['quotation_grand_total'] = $round_off_value;

        $quotation_tax_amount = $quotation_data['quotation_tax_amount'] + (float) ($this->input->post('total_other_taxable_amount'));

        if ($section_modules['access_settings'][0]->tax_type == "gst") {
            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

            if ($data['branch'][0]->branch_country_id == $quotation_data['quotation_billing_country_id']) {

                if ($data['branch'][0]->branch_state_id == $quotation_data['quotation_billing_state_id']) {
                    $quotation_data['quotation_igst_amount'] = 0;
                    $quotation_data['quotation_cgst_amount'] = ($quotation_tax_amount * $cgst_amount_percentage) / 100;
                    $quotation_data['quotation_sgst_amount'] = ($quotation_tax_amount * $sgst_amount_percentage) / 100;
                    $quotation_data['quotation_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $quotation_data['quotation_igst_amount'] = $quotation_tax_amount;
                    $quotation_data['quotation_cgst_amount'] = 0;
                    $quotation_data['quotation_sgst_amount'] = 0;
                    $quotation_data['quotation_tax_cess_amount'] = $total_cess_amnt;
                }
            } else {

                if ($quotation_data['quotation_type_of_supply'] == "export_with_payment") {
                    $quotation_data['quotation_igst_amount'] = $quotation_tax_amount;
                    $quotation_data['quotation_cgst_amount'] = 0;
                    $quotation_data['quotation_sgst_amount'] = 0;
                    $quotation_data['quotation_tax_cess_amount'] = $total_cess_amnt;
                }
            }
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $quotation_data['converted_grand_total'] = $quotation_data['quotation_grand_total'];
        } else {
            $quotation_data['converted_grand_total'] = 0;
        }

        $data_main = array_map('trim', $quotation_data);
        $quotation_table = $this->config->item('quotation_table');

        if ($quotation_id = $this->general_model->insertData($quotation_table, $data_main)) {
            $successMsg = 'Quotation Added Successfully';
                $this->session->set_flashdata('quotation_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $quotation_id,
                'table_name' => $quotation_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Quotation Inserted');
            $data_main['quotation_id'] = $quotation_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $quotation_item_data = $this->input->post('table_data');
            $js_data = json_decode($quotation_item_data);
            $js_data               = array_reverse($js_data);
            $item_table = $this->config->item('quotation_item_table');

            if (!empty($js_data)) {
                $js_data1 = array();

                foreach ($js_data as $key => $value) {

                    /*SK Customization*/
                    if($value->item_id == 0){
                        $product_module_id = $this->config->item('product_module');
                        $data['module_id'] = $product_module_id;
                        $modules           = $this->modules;
                        $privilege         = "add_privilege";
                        $data['privilege'] = "add_privilege";
                        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);
                        $access_settings          = $section_modules['access_settings'];
                        $primary_id1               = "product_id";
                        $table_name1               = "products";
                        $date_field_name1          = "added_date";
                        $current_date1             = date('Y-m-d');
                        $product_code = $this->generate_invoice_number($access_settings, $primary_id1, $table_name1, $date_field_name1, $current_date1);

                        $product_data = array(
                            "product_code"           => $product_code,
                            "product_name"           => $value->item_name,
                            "product_batch"          => 'BATCH-01',
                            //"product_category_id"    => $value->item_category,
                            "product_subcategory_id" => 0,
                            "product_quantity"       => $value->item_quantity,
                            "product_unit"           => $value->item_uom,
                            "product_unit_id"        => $value->item_uom,
                            "product_hsn_sac_code"   => $value->item_hsn_sac_code,
                            "product_price"          => $value->item_price,
                            "product_gst_id"         => $value->item_tax_id,
                            "product_gst_value"      => $value->item_tax_percentage,
                            "product_discount_id"    => $value->item_discount_id,
                            "product_details"        => $value->item_description,
                            "is_assets"              => 'N',
                            "is_varients"            => 'N',
                            "product_type"           => 'finishedgoods',
                            "added_date"             => date('Y-m-d'),
                            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
                        );
                        $product_id = $this->general_model->insertData('products', $product_data);
                        //$item_data['item_id']  => $product_id;

                    }
                    /*SK Customization*/

                    if ($value == null) {
                        
                    } else {
                        $item_id = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $item_data = array(
                            "item_id" => ($value->item_id != 0) ?  $value->item_id : $product_id ,
                            "item_type" => $value->item_type,
                            "quotation_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "quotation_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "quotation_item_free_quantity"   => (@$value->free_item_quantity ? (float) $value->free_item_quantity : 0),
                            "quotation_item_mrp_price"      => (@$value->item_mrp_price ? (float) $value->item_mrp_price : 0),
                            "quotation_item_cash_discount_amount" => (@$value->item_cash_discount ? (float) $value->item_cash_discount : 0) ,
                            "quotation_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "quotation_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "quotation_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0),
                            "quotation_item_discount_id" => (@$value->item_discount_id ? (float) $value->item_discount_id : 0),
                            "quotation_item_tds_id" => $value->item_tds_id ? (float) $value->item_tds_id : 0,
                            "quotation_item_tds_percentage" => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0,
                            "quotation_item_tds_amount" => $value->item_tds_amount ? (float) $value->item_tds_amount : 0,
                            "quotation_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "quotation_item_tax_id" => $value->item_tax_id ? (float) $value->item_tax_id : 0,
                            "quotation_item_tax_cess_id" => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0,
                            "quotation_item_igst_percentage" => 0,
                            "quotation_item_igst_amount" => 0,
                            "quotation_item_cgst_percentage" => 0,
                            "quotation_item_cgst_amount" => 0,
                            "quotation_item_sgst_percentage" => 0,
                            "quotation_item_sgst_amount" => 0,
                            "quotation_item_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "quotation_item_tax_cess_percentage" => 0,
                            "quotation_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            'quotation_item_tax_cess_amount' => 0,
                            "quotation_item_description" => $value->item_description ? $value->item_description : "",
                            "quotation_item_uom_id"  => (@$value->item_uom ? $value->item_uom : ""),
                            "debit_note_quantity" => 0,
                            "quotation_id" => $quotation_id);

                        $quotation_item_tax_amount = $item_data['quotation_item_tax_amount'];
                        $quotation_item_tax_percentage = $item_data['quotation_item_tax_percentage'];
                        /* Customization leather craft fields */
                        if(@$value->item_basic_total){
                            $item_data['quotation_item_basic_total'] = $value->item_basic_total;
                        }
                        if(@$value->item_selling_price){
                            $item_data['quotation_item_selling_price'] = $value->item_selling_price;
                        }
                        if(@$value->item_mrkd_discount_amount){
                            $item_data['quotation_item_mrkd_discount_amount'] = $value->item_mrkd_discount_amount;
                        }
                        if(@$value->item_mrkd_discount_id){
                            $item_data['quotation_item_mrkd_discount_id'] = $value->item_mrkd_discount_id;
                        }
                        if(@$value->item_mrkd_discount_percentage){
                            $item_data['quotation_item_mrkd_discount_percentage'] = $value->item_mrkd_discount_percentage;
                        }
                        if(@$value->item_mrgn_discount_amount){
                            $item_data['quotation_item_mrgn_discount_amount'] = $value->item_mrgn_discount_amount;
                        }
                        if(@$value->item_mrgn_discount_id){
                            $item_data['quotation_item_mrgn_discount_id'] = $value->item_mrgn_discount_id;
                        }
                        if(@$value->item_mrgn_discount_percentage){
                            $item_data['quotation_item_mrgn_discount_percentage'] = $value->item_mrgn_discount_percentage;
                        }

                        if(@$value->item_scheme_discount_amount){
                            $item_data['quotation_item_scheme_discount_amount'] = $value->item_scheme_discount_amount;
                        }
                        if(@$value->item_scheme_discount_id){
                            $item_data['quotation_item_scheme_discount_id'] = $value->item_scheme_discount_id;
                        }
                        if(@$value->item_scheme_discount_percentage){
                            $item_data['quotation_item_scheme_discount_percentage'] = $value->item_scheme_discount_percentage;
                        }

                        if(@$value->item_out_tax_percentage){
                            $item_data['quotation_item_out_tax_percentage'] = $value->item_out_tax_percentage;
                        }
                        if(@$value->item_out_tax_amount){
                            $item_data['quotation_item_out_tax_amount'] = $value->item_out_tax_amount;
                        }
                        if(@$value->item_out_tax_id){
                            $item_data['quotation_item_out_tax_id'] = $value->item_out_tax_id;
                        }
                       
                        /* End leather Craft */

                        if ($section_modules['access_settings'][0]->tax_type == "gst") {
                            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0;
                            if ($data['branch'][0]->branch_country_id == $quotation_data['quotation_billing_country_id']) {

                                if ($data['branch'][0]->branch_state_id == $quotation_data['quotation_billing_state_id']) {
                                    $item_data['quotation_item_igst_amount'] = 0;
                                    $item_data['quotation_item_cgst_amount'] = ($quotation_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['quotation_item_sgst_amount'] = ($quotation_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['quotation_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['quotation_item_igst_percentage'] = 0;
                                    $item_data['quotation_item_cgst_percentage'] = ($quotation_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['quotation_item_sgst_percentage'] = ($quotation_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['quotation_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                } else {
                                    $item_data['quotation_item_igst_amount'] = $quotation_item_tax_amount;
                                    $item_data['quotation_item_cgst_amount'] = 0;
                                    $item_data['quotation_item_sgst_amount'] = 0;
                                    $item_data['quotation_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['quotation_item_igst_percentage'] = $quotation_item_tax_percentage;
                                    $item_data['quotation_item_cgst_percentage'] = 0;
                                    $item_data['quotation_item_sgst_percentage'] = 0;
                                    $item_data['quotation_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            } else {

                                if ($quotation_data['quotation_type_of_supply'] == "export_with_payment") {
                                    $item_data['quotation_item_igst_amount'] = $quotation_item_tax_amount;
                                    $item_data['quotation_item_cgst_amount'] = 0;
                                    $item_data['quotation_item_sgst_amount'] = 0;
                                    $item_data['quotation_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['quotation_item_igst_percentage'] = $quotation_item_tax_percentage;
                                    $item_data['quotation_item_cgst_percentage'] = 0;
                                    $item_data['quotation_item_sgst_percentage'] = 0;
                                    $item_data['quotation_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                        }

                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;

                        if ($this->general_model->insertData($item_table, $data_item)) {
                            
                        }
                    }
                }
            }
        } else {
            $errorMsg = 'Quotation Add Unsuccessful';
            $this->session->set_flashdata('quotation_error',$errorMsg);
            redirect('quotation', 'refresh');
        }

        $action = $this->input->post('submit');

        if ($action == 'add') {
            redirect('quotation', 'refresh');
        } else {
            redirect('quotation', 'refresh');
        }
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $quotation_module_id = $this->config->item('quotation_module');
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['quotation_module_id'] = $quotation_module_id;
        $data['module_id'] = $quotation_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['customer_module_id'] = $this->config->item('customer_module');
        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['discount_module_id'] = $this->config->item('discount_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        $data['uqc_module_id']        = $this->config->item('uqc_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['data'] = $this->general_model->getRecords('*', 'quotation', array(
            'quotation_id' => $id));

        $data['shipping_address'] = $this->general_model->getRecords('*', 'shipping_address', array(
            'shipping_party_id' => $data['data'][0]->quotation_party_id,
            'shipping_party_type' => $data['data'][0]->quotation_party_type
        ));

        $item_types = $this->general_model->getRecords('item_type,quotation_item_description', 'quotation_item', array(
            'quotation_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->quotation_item_description != "") {
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

        $data['product_exist'] = $product;
        $data['service_exist'] = $service;

        $data['customer'] = $this->customer_call();
        $data['currency'] = $this->currency_call();
        if ($data['data'][0]->quotation_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax") {

            $data['tax'] = $this->tax_call();
        }

        if ($data['data'][0]->quotation_nature_of_supply == "service" || $data['data'][0]->quotation_nature_of_supply == "both") {

            $data['sac'] = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }

        if ($data['data'][0]->quotation_nature_of_supply == "product" || $data['data'][0]->quotation_nature_of_supply == "both") {

            if ($product == 2) {
                $data['inventory_access'] = "yes";
            } else {
                $data['inventory_access'] = "no";
            }

            $data['product_category'] = $this->product_category_call();
            $data['uqc'] = $this->uqc_call();
            $data['uqc_service']      = $this->uqc_product_service_call('service');
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['chapter'] = $this->chapter_call();
            $data['hsn'] = $this->hsn_call();
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();

            if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }
        }

        $quotation_service_items = array();
        $quotation_product_items = array();

        if (($data['data'][0]->quotation_nature_of_supply == "service" || $data['data'][0]->quotation_nature_of_supply == "both") && $service == 1) {

            $service_items = $this->common->quotation_items_service_list_field($id);
            $quotation_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->quotation_nature_of_supply == "product" || $data['data'][0]->quotation_nature_of_supply == "both") {

            if ($product == 2) {
                $product_items = $this->common->quotation_items_product_inventory_list_field($id);
                $quotation_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else

            if ($product == 1) {
                $product_items = $this->common->quotation_items_product_list_field($id);
                $quotation_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
        }

        $data['items'] = array_merge($quotation_product_items, $quotation_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;
        $cessExist = 0;

        if ($data['data'][0]->quotation_tax_amount > 0 && $data['data'][0]->quotation_igst_amount > 0 && ($data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0)) {
            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->quotation_tax_amount > 0 && ($data['data'][0]->quotation_cgst_amount > 0 || $data['data'][0]->quotation_sgst_amount > 0) && $data['data'][0]->quotation_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->quotation_tax_amount > 0 && ($data['data'][0]->quotation_igst_amount == 0 && $data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->quotation_tax_amount == 0 && ($data['data'][0]->quotation_igst_amount == 0 && $data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->quotation_tax_cess_amount > 0) {
            $cessExist = 1;
        }

        if ($data['data'][0]->quotation_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes") {
            /* Discount */
            $discountExist = 1;
            $data['discount'] = $this->discount_call();
        }


        if ($data['data'][0]->quotation_tds_amount > 0 || $data['data'][0]->quotation_tcs_amount > 0 || $data['access_settings'][0]->tds_visible == "yes") {
            /* Discount */
            $tdsExist = 1;
        }

        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes") {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->quotation_billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['cess_exist'] = $cessExist;
        $data['tax_exist'] = $taxExist;
        $data['is_utgst'] = $is_utgst;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;

        $this->load->view('quotation/edit', $data);
    }

    public function convert_quotation($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $sales_module_id = $this->config->item('sales_module');
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($sales_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['sales_module_id'] = $sales_module_id;
        $data['module_id'] = $sales_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['customer_module_id'] = $this->config->item('customer_module');
        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['discount_module_id'] = $this->config->item('discount_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        $data['uqc_module_id']        = $this->config->item('uqc_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['data'] = $this->general_model->getRecords('*', 'quotation', array(
            'quotation_id' => $id));

        $data['shipping_address'] = $this->general_model->getRecords('*', 'shipping_address', array(
            'shipping_party_id' => $data['data'][0]->quotation_party_id,
            'shipping_party_type' => $data['data'][0]->quotation_party_type
        ));
        $item_types = $this->general_model->getRecords('item_type,quotation_item_description', 'quotation_item', array(
            'quotation_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->quotation_item_description != "") {
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

        $data['product_exist'] = $product;
        $data['service_exist'] = $service;

        $data['customer'] = $this->customer_call();
        $data['currency'] = $this->currency_call();
        $data['uqc'] = $this->uqc_call();
        $data['uqc_service']   = $this->uqc_product_service_call('service');
        $data['uqc_product']   = $this->uqc_product_service_call('product');
        $data['tax_tds']          = $this->tax_call_type('TDS');
        $data['tax_tcs']          = $this->tax_call_type('TCS');
        $data['tax_gst']          = $this->tax_call_type('GST');
        $data['tax_section'] = $this->tax_section_call();
        if ($data['data'][0]->quotation_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax") {
            $data['tax'] = $this->tax_call();
        }

        if ($data['data'][0]->quotation_nature_of_supply == "service" || $data['data'][0]->quotation_nature_of_supply == "both") {

            $data['sac'] = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }

        if ($data['data'][0]->quotation_nature_of_supply == "product" || $data['data'][0]->quotation_nature_of_supply == "both") {

            if ($product == 2) {
                $data['inventory_access'] = "yes";
            } else {
                $data['inventory_access'] = "no";
            }

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

        $quotation_service_items = array();
        $quotation_product_items = array();

        if (($data['data'][0]->quotation_nature_of_supply == "service" || $data['data'][0]->quotation_nature_of_supply == "both") && $service == 1) {

            $service_items = $this->common->quotation_items_service_list_field($id);
            $quotation_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->quotation_nature_of_supply == "product" || $data['data'][0]->quotation_nature_of_supply == "both") {

            if ($product == 2) {
                $product_items = $this->common->quotation_items_product_inventory_list_field($id);
                $quotation_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else

            if ($product == 1) {
                $product_items = $this->common->quotation_items_product_list_field($id);
                $quotation_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
        }

        $data['items'] = array_merge($quotation_product_items, $quotation_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;
        $cess_exist = 0;

        if ($data['data'][0]->quotation_tax_amount > 0 && $data['data'][0]->quotation_igst_amount > 0 && ($data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0)) {
            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->quotation_tax_amount > 0 && ($data['data'][0]->quotation_cgst_amount > 0 || $data['data'][0]->quotation_sgst_amount > 0) && $data['data'][0]->quotation_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->quotation_tax_amount > 0 && ($data['data'][0]->quotation_igst_amount == 0 && $data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->quotation_tax_amount == 0 && ($data['data'][0]->quotation_igst_amount == 0 && $data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->quotation_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes") {
            /* Discount */
            $discountExist = 1;
            $data['discount'] = $this->discount_call();
        }

        if ($data['data'][0]->quotation_tds_amount > 0 || $data['data'][0]->quotation_tcs_amount > 0 || $data['access_settings'][0]->tds_visible == "yes") {
            /* Discount */
            $tdsExist = 1;
        }

        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes") {
            /* Discount */
            $descriptionExist = 1;
        }
        if ($data['data'][0]->quotation_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->quotation_billing_state_id);
        $data['is_utgst'] = $is_utgst;
        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['tax_exist'] = $taxExist;
        $data['cess_exist'] = $cess_exist;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;


        $access_settings = $data['access_settings'];
        $primary_id = "sales_id";
        $table_name = $this->config->item('sales_table');
        $date_field_name = "sales_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);


        $this->load->view('quotation/convert_quotation', $data);
    }

    public function edit_quotation() {
        $data = $this->get_default_country_state();
        $quotation_id = $this->input->post('quotation_id');
        $quotation_module_id = $this->config->item('quotation_module');
        $module_id = $quotation_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);

        /* Modules Present */
        $data['quotation_module_id'] = $quotation_module_id;
        $data['module_id'] = $quotation_module_id;
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

        $currency = $this->input->post('currency_id');
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;

        if ($section_modules['access_settings'][0]->invoice_creation == "automatic") {

            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old')) {
                $primary_id = "quotation_id";
                $table_name = $this->config->item('quotation_table');
                $date_field_name = "quotation_date";
                $current_date = $this->input->post('invoice_date');
                $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else {
                $invoice_number = $this->input->post('invoice_number');
            }
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }

        $quotation_data = array(
            "quotation_date" => date('Y-m-d', strtotime($this->input->post('invoice_date'))),
            "quotation_invoice_number" => $invoice_number,
            "quotation_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "quotation_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "quotation_discount_amount" => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0,
            "quotation_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "quotation_tax_cess_amount" => 0,
            "quotation_taxable_value" => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0,
            "quotation_tds_amount" => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0,
            "quotation_tcs_amount" => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0,
            "quotation_igst_amount" => 0,
            "quotation_cgst_amount" => 0,
            "quotation_sgst_amount" => 0,
            "from_account" => 'customer',
            "to_account" => 'quotation',
            "quotation_paid_amount" => 0,
            "credit_note_amount" => 0,
            "debit_note_amount" => 0,
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "quotation_party_id" => $this->input->post('customer'),
            "ship_to_customer_id" => $this->input->post('ship_to') ,
            "quotation_party_type" => "customer",
            "quotation_nature_of_supply" => $this->input->post('nature_of_supply'),
            "quotation_order_number" => $this->input->post('order_number'),
            "quotation_type_of_supply" => $this->input->post('type_of_supply'),
            "quotation_gst_payable" => $this->input->post('gst_payable'),
            "quotation_billing_country_id" => $this->input->post('billing_country'),
            "quotation_billing_state_id" => $this->input->post('billing_state'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "transporter_name" => $this->input->post('transporter_name'),
            "transporter_gst_number" => $this->input->post('transporter_gst_number'),
            "lr_no" => $this->input->post('lr_no'),
            "vehicle_no" => $this->input->post('vehicle_no'),
            "mode_of_shipment" => $this->input->post('mode_of_shipment'),
            "ship_by" => $this->input->post('ship_by'),
            "net_weight" => $this->input->post('net_weight'),
            "gross_weight" => $this->input->post('gross_weight'),
            "origin" => $this->input->post('origin'),
            "destination" => $this->input->post('destination'),
            "shipping_type" => $this->input->post('shipping_type'),
            "shipping_type_place" => $this->input->post('shipping_type_place'),
            "lead_time" => $this->input->post('lead_time'),
            "shipping_address_id" => $this->input->post('shipping_address'),
            /*"billing_address_id" => $this->input->post('billing_address'),*/
            "warranty" => $this->input->post('warranty'),
            "payment_mode" => $this->input->post('payment_mode'),
            "freight_charge_amount" => $this->input->post('freight_charge_amount') ? (float) $this->input->post('freight_charge_amount') : 0,
            "freight_charge_tax_percentage" => $this->input->post('freight_charge_tax_percentage') ? (float) $this->input->post('freight_charge_tax_percentage') : 0,
            "freight_charge_tax_amount" => $this->input->post('freight_charge_tax_amount') ? (float) $this->input->post('freight_charge_tax_amount') : 0,
            "total_freight_charge" => $this->input->post('total_freight_charge') ? (float) $this->input->post('total_freight_charge') : 0,
            "insurance_charge_amount" => $this->input->post('insurance_charge_amount') ? (float) $this->input->post('insurance_charge_amount') : 0,
            "insurance_charge_tax_percentage" => $this->input->post('insurance_charge_tax_percentage') ? (float) $this->input->post('insurance_charge_tax_percentage') : 0,
            "insurance_charge_tax_amount" => $this->input->post('insurance_charge_tax_amount') ? (float) $this->input->post('insurance_charge_tax_amount') : 0,
            "total_insurance_charge" => $this->input->post('total_insurance_charge') ? (float) $this->input->post('total_insurance_charge') : 0,
            "packing_charge_amount" => $this->input->post('packing_charge_amount') ? (float) $this->input->post('packing_charge_amount') : 0,
            "packing_charge_tax_percentage" => $this->input->post('packing_charge_tax_percentage') ? (float) $this->input->post('packing_charge_tax_percentage') : 0,
            "packing_charge_tax_amount" => $this->input->post('packing_charge_tax_amount') ? (float) $this->input->post('packing_charge_tax_amount') : 0,
            "total_packing_charge" => $this->input->post('total_packing_charge') ? (float) $this->input->post('total_packing_charge') : 0,
            "incidental_charge_amount" => $this->input->post('incidental_charge_amount') ? (float) $this->input->post('incidental_charge_amount') : 0,
            "incidental_charge_tax_percentage" => $this->input->post('incidental_charge_tax_percentage') ? (float) $this->input->post('incidental_charge_tax_percentage') : 0,
            "incidental_charge_tax_amount" => $this->input->post('incidental_charge_tax_amount') ? (float) $this->input->post('incidental_charge_tax_amount') : 0,
            "total_incidental_charge" => $this->input->post('total_incidental_charge') ? (float) $this->input->post('total_incidental_charge') : 0,
            "inclusion_other_charge_amount" => $this->input->post('inclusion_other_charge_amount') ? (float) $this->input->post('inclusion_other_charge_amount') : 0,
            "inclusion_other_charge_tax_percentage" => $this->input->post('inclusion_other_charge_tax_percentage') ? (float) $this->input->post('inclusion_other_charge_tax_percentage') : 0,
            "inclusion_other_charge_tax_amount" => $this->input->post('inclusion_other_charge_tax_amount') ? (float) $this->input->post('inclusion_other_charge_tax_amount') : 0,
            "total_inclusion_other_charge" => $this->input->post('total_other_inclusive_charge') ? (float) $this->input->post('total_other_inclusive_charge') : 0,
            "exclusion_other_charge_amount" => $this->input->post('exclusion_other_charge_amount') ? (float) $this->input->post('exclusion_other_charge_amount') : 0,
            "exclusion_other_charge_tax_percentage" => $this->input->post('exclusion_other_charge_tax_percentage') ? (float) $this->input->post('exclusion_other_charge_tax_percentage') : 0,
            "exclusion_other_charge_tax_amount" => $this->input->post('exclusion_other_charge_tax_amount') ? (float) $this->input->post('exclusion_other_charge_tax_amount') : 0,
            "total_exclusion_other_charge" => $this->input->post('total_other_exclusive_charge') ? (float) $this->input->post('total_other_exclusive_charge') : 0,
            "total_other_amount" => $this->input->post('total_other_amount') ? (float) $this->input->post('total_other_amount') : 0,
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2')
        );

        if(@$this->input->post('cash_discount')){
            $quotation_data['quotation_cash_discount'] = $this->input->post('cash_discount');
        }

        $quotation_data['freight_charge_tax_id'] = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $quotation_data['insurance_charge_tax_id'] = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $quotation_data['packing_charge_tax_id'] = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $quotation_data['incidental_charge_tax_id'] = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $quotation_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $quotation_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;

        $round_off_value = $quotation_data['quotation_grand_total'];


        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" || $this->input->post('round_off_key') == "yes") {
            if ($this->input->post('round_off_value') != "" && $this->input->post('round_off_value') > 0) {
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $quotation_data['round_off_amount'] = bcsub($quotation_data['quotation_grand_total'], $round_off_value, $section_modules['access_common_settings'][0]->amount_precision);

        $quotation_data['quotation_grand_total'] = $round_off_value;

        $tax_type = $this->input->post('tax_type');
        $quotation_tax_amount = $quotation_data['quotation_tax_amount'];
        $quotation_tax_amount = $quotation_data['quotation_tax_amount'] + (float) ($this->input->post('total_other_taxable_amount'));
        if ($tax_type == "gst") {
            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

            if ($data['branch'][0]->branch_country_id == $quotation_data['quotation_billing_country_id']) {
                if ($data['branch'][0]->branch_state_id == $quotation_data['quotation_billing_state_id']) {
                    $quotation_data['quotation_igst_amount'] = 0;
                    $quotation_data['quotation_cgst_amount'] = ($quotation_tax_amount * $cgst_amount_percentage) / 100;
                    $quotation_data['quotation_sgst_amount'] = ($quotation_tax_amount * $sgst_amount_percentage) / 100;
                    $quotation_data['quotation_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $quotation_data['quotation_igst_amount'] = $quotation_tax_amount;
                    $quotation_data['quotation_cgst_amount'] = 0;
                    $quotation_data['quotation_sgst_amount'] = 0;
                    $quotation_data['quotation_tax_cess_amount'] = $total_cess_amnt;
                }
            } else {

                if ($quotation_data['quotation_type_of_supply'] == "export_with_payment") {
                    $quotation_data['quotation_igst_amount'] = $quotation_tax_amount;
                    $quotation_data['quotation_cgst_amount'] = 0;
                    $quotation_data['quotation_sgst_amount'] = 0;
                    $quotation_data['quotation_tax_cess_amount'] = $total_cess_amnt;
                }
            }
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $quotation_data['converted_grand_total'] = $quotation_data['quotation_grand_total'];
        } else {
            $quotation_data['converted_grand_total'] = 0;
        }

        $data_main = array_map('trim', $quotation_data);
        $quotation_table = $this->config->item('quotation_table');
        $where = array(
            'quotation_id' => $quotation_id);

        if ($this->general_model->updateData($quotation_table, $data_main, $where)) {
            $successMsg = 'Quotation Updated Successfully';
            $this->session->set_flashdata('quotation_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $quotation_id,
                'table_name' => $quotation_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Quotation Updated');
            $data_main['quotation_id'] = $quotation_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $quotation_item_data = $this->input->post('table_data');
            $js_data = json_decode($quotation_item_data);
            $js_data               = array_reverse($js_data);
            $item_table = $this->config->item('quotation_item_table');

            if (!empty($js_data)) {
                $js_data1 = array();
                $string = 'quotation_item_id,quotation_item_quantity,item_type,item_id';
                $table = 'quotation_item';
                $where = array(
                    'quotation_id' => $quotation_id,
                    'delete_status' => 0);
                $old_quotation_items = $this->general_model->getRecords($string, $table, $where, $order = "");
                $old_item_ids = $this->getValues($old_quotation_items, 'item_id');
                $not_deleted_ids = array();
               
                foreach ($js_data as $key => $value) {

                    /*SK Customization*/
                    if($value->item_id == 0){
                        $product_module_id = $this->config->item('product_module');
                        $data['module_id'] = $product_module_id;
                        $modules           = $this->modules;
                        $privilege         = "add_privilege";
                        $data['privilege'] = "add_privilege";
                        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);
                        $access_settings          = $section_modules['access_settings'];
                        $primary_id1               = "product_id";
                        $table_name1               = "products";
                        $date_field_name1          = "added_date";
                        $current_date1             = date('Y-m-d');
                        $product_code = $this->generate_invoice_number($access_settings, $primary_id1, $table_name1, $date_field_name1, $current_date1);

                        $product_data = array(
                            "product_code"           => $product_code,
                            "product_name"           => $value->item_name,
                            "product_batch"          => 'BATCH-01',
                            //"product_category_id"    => $value->item_category,
                            "product_subcategory_id" => 0,
                            "product_quantity"       => $value->item_quantity,
                            "product_unit"           => $value->item_uom,
                            "product_unit_id"        => $value->item_uom,
                            "product_hsn_sac_code"   => $value->item_hsn_sac_code,
                            "product_price"          => $value->item_price,
                            "product_gst_id"         => $value->item_tax_id,
                            "product_gst_value"      => $value->item_tax_percentage,
                            "product_discount_id"    => $value->item_discount_id,
                            "product_details"        => $value->item_description,
                            "is_assets"              => 'N',
                            "is_varients"            => 'N',
                            "product_type"           => 'finishedgoods',
                            "added_date"             => date('Y-m-d'),
                            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
                        );
                        $product_id = $this->general_model->insertData('products', $product_data);
                        //$item_data['item_id']  => $product_id;

                    }
                    /*SK Customization*/

                    if ($value != null) {
                        $item_id   = ($value->item_id != 0) ?  $value->item_id : $product_id;
                        //$item_id = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $item_data = array(
                            "item_id" => ($value->item_id != 0) ?  $value->item_id : $product_id ,
                            "item_type" => $value->item_type,
                            "quotation_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "quotation_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "quotation_item_free_quantity"   => (@$value->free_item_quantity ? (float) $value->free_item_quantity : 0),
                            "quotation_item_mrp_price"      => (@$value->item_mrp_price ? (float) $value->item_mrp_price : 0),
                            "quotation_item_cash_discount_amount" => (@$value->item_cash_discount ? (float) $value->item_cash_discount : 0) ,
                            "quotation_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "quotation_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "quotation_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0),
                            "quotation_item_discount_id" => $value->item_discount_id ? (float) $value->item_discount_id : 0,
                            "quotation_item_tds_id" => $value->item_tds_id ? (float) $value->item_tds_id : 0,
                            "quotation_item_tds_percentage" => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0,
                            "quotation_item_tds_amount" => $value->item_tds_amount ? (float) $value->item_tds_amount : 0,
                            "quotation_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "quotation_item_tax_id" => $value->item_tax_id ? (float) $value->item_tax_id : 0,
                            "quotation_item_tax_cess_id" => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0,
                            "quotation_item_igst_percentage" => 0,
                            "quotation_item_igst_amount" => 0,
                            "quotation_item_cgst_percentage" => 0,
                            "quotation_item_cgst_amount" => 0,
                            "quotation_item_sgst_percentage" => 0,
                            "quotation_item_sgst_amount" => 0,
                            "quotation_item_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "quotation_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            "quotation_item_tax_cess_percentage" => 0,
                            "quotation_item_tax_cess_amount" => 0,
                            "quotation_item_description" => $value->item_description ? $value->item_description : "",
                            "quotation_item_uom_id"  => (@$value->item_uom ? $value->item_uom : ""),
                            "debit_note_quantity" => 0,
                            "quotation_id" => $quotation_id);

                        $quotation_item_tax_amount = $item_data['quotation_item_tax_amount'];
                        $quotation_item_tax_percentage = $item_data['quotation_item_tax_percentage'];
                        /* Customization leather craft fields */
                        if(@$value->item_basic_total){
                            $item_data['quotation_item_basic_total'] = $value->item_basic_total;
                        }
                        if(@$value->item_selling_price){
                            $item_data['quotation_item_selling_price'] = $value->item_selling_price;
                        }
                        if(@$value->item_mrkd_discount_amount){
                            $item_data['quotation_item_mrkd_discount_amount'] = $value->item_mrkd_discount_amount;
                        }
                        if(@$value->item_mrkd_discount_id){
                            $item_data['quotation_item_mrkd_discount_id'] = $value->item_mrkd_discount_id;
                        }
                        if(@$value->item_mrkd_discount_percentage){
                            $item_data['quotation_item_mrkd_discount_percentage'] = $value->item_mrkd_discount_percentage;
                        }
                        if(@$value->item_mrgn_discount_amount){
                            $item_data['quotation_item_mrgn_discount_amount'] = $value->item_mrgn_discount_amount;
                        }
                        if(@$value->item_mrgn_discount_id){
                            $item_data['quotation_item_mrgn_discount_id'] = $value->item_mrgn_discount_id;
                        }
                        if(@$value->item_mrgn_discount_percentage){
                            $item_data['quotation_item_mrgn_discount_percentage'] = $value->item_mrgn_discount_percentage;
                        }

                        if(@$value->item_scheme_discount_amount){
                            $item_data['quotation_item_scheme_discount_amount'] = $value->item_scheme_discount_amount;
                        }
                        if(@$value->item_scheme_discount_id){
                            $item_data['quotation_item_scheme_discount_id'] = $value->item_scheme_discount_id;
                        }
                        if(@$value->item_scheme_discount_percentage){
                            $item_data['quotation_item_scheme_discount_percentage'] = $value->item_scheme_discount_percentage;
                        }

                        if(@$value->item_out_tax_percentage){
                            $item_data['quotation_item_out_tax_percentage'] = $value->item_out_tax_percentage;
                        }
                        if(@$value->item_out_tax_amount){
                            $item_data['quotation_item_out_tax_amount'] = $value->item_out_tax_amount;
                        }
                        if(@$value->item_out_tax_id){
                            $item_data['quotation_item_out_tax_id'] = $value->item_out_tax_id;
                        }
                       
                        /* End leather Craft */
                        if ($tax_type == "gst") {
                            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0;

                            if ($data['branch'][0]->branch_country_id == $quotation_data['quotation_billing_country_id']) {

                                if ($data['branch'][0]->branch_state_id == $quotation_data['quotation_billing_state_id']) {
                                    $item_data['quotation_item_igst_amount'] = 0;
                                    $item_data['quotation_item_cgst_amount'] = ($quotation_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['quotation_item_sgst_amount'] = ($quotation_item_tax_amount * $sgst_amount_percentage) / 100;

                                    $item_data['quotation_item_igst_percentage'] = 0;
                                    $item_data['quotation_item_cgst_percentage'] = ($quotation_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['quotation_item_sgst_percentage'] = ($quotation_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['quotation_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['quotation_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                } else {
                                    $item_data['quotation_item_igst_amount'] = $quotation_item_tax_amount;
                                    $item_data['quotation_item_cgst_amount'] = 0;
                                    $item_data['quotation_item_sgst_amount'] = 0;

                                    $item_data['quotation_item_igst_percentage'] = $quotation_item_tax_percentage;
                                    $item_data['quotation_item_cgst_percentage'] = 0;
                                    $item_data['quotation_item_sgst_percentage'] = 0;
                                    $item_data['quotation_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['quotation_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            } else {

                                if ($quotation_data['quotation_type_of_supply'] == "export_with_payment") {
                                    $item_data['quotation_item_igst_amount'] = $quotation_item_tax_amount;
                                    $item_data['quotation_item_cgst_amount'] = 0;
                                    $item_data['quotation_item_sgst_amount'] = 0;

                                    $item_data['quotation_item_igst_percentage'] = $quotation_item_tax_percentage;
                                    $item_data['quotation_item_cgst_percentage'] = 0;
                                    $item_data['quotation_item_sgst_percentage'] = 0;
                                    $item_data['quotation_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['quotation_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                        }

                        $table = 'quotation_item';
                        if (($item_key = array_search($value->item_id, $old_item_ids)) !== false) {
                            unset($old_item_ids[$item_key]);
                            $quotation_item_id = $old_quotation_items[$item_key]->quotation_item_id;
                            array_push($not_deleted_ids, $quotation_item_id);
                            $where = array('quotation_item_id' => $quotation_item_id);
                            $this->general_model->updateData($table, $item_data, $where);
                        } else {
                            $this->general_model->insertData($table, $item_data);
                        }

                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                    }
                }

                if (!empty($old_quotation_items)) {
                    foreach ($old_quotation_items as $key => $items) {
                        if (!in_array($items->quotation_item_id, $not_deleted_ids)) {
                            $table = 'quotation_item';
                            $where = array('quotation_item_id' => $items->quotation_item_id);
                            $sales_data = array('delete_status' => 1);

                            $this->general_model->updateData($table, $sales_data, $where);
                        }
                    }
                }
            }
            redirect('quotation', 'refresh');
        } else {
            $errorMsg = 'Quotation Update Unsuccessful';
            $this->session->set_flashdata('quotation_error',$errorMsg);
            redirect('quotation', 'refresh');
        }
    }

    public function view($id) {
        $id = $this->encryption_url->decode($id);
        $data = array();
        $data = $this->getQuotationDetail($id);

        $this->load->view('quotation/view', $data);
    }

    public function pdf($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $quotation_module_id = $this->config->item('quotation_module');
        $data['module_id'] = $quotation_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);

        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $customer_module_id = $this->config->item('customer_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);
        $data = $this->getQuotationDetail($id);


        $invoice_type = $this->input->post('pdf_type_check');

        if ($invoice_type == "original") {
            $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
        } elseif ($invoice_type == "duplicate") {
            $data['invoice_type'] = "DUPLICATE FOR SUPPLIER";
        } else {
            $data['invoice_type'] = "TRIPLICATE FOR TRANSPORTER";
        }

        $print_currency = $this->input->post('print_currency');
        $converted_rate = 1;
        if ($print_currency != $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
            if ($data['data'][0]->currency_converted_rate > 0)
                $converted_rate = $data['data'][0]->currency_converted_rate;
        }else {
            $currency = $this->getBranchCurrencyCode();
            $data['data'][0]->currency_code = $currency[0]->currency_code;
            $data['data'][0]->currency_symbol = $currency[0]->currency_symbol;
            $data['data'][0]->currency_name = $currency[0]->currency_name;
            $data['data'][0]->currency_symbol_pdf = $currency[0]->currency_symbol_pdf;
            $data['data'][0]->unit = $currency[0]->unit;
            $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
        }

        $data['converted_rate'] = $converted_rate;

        $pdf_json = $data['access_settings'][0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);
        
        if($this->session->userdata('SESS_FIRM_ID') == $this->config->item('Sanath')){
            $hsn_data = $this->common->hsn_quotation_list_item_field1($id);
            $data['hsn'] = $this->general_model->getPageJoinRecords($hsn_data);
        }

        $html = $this->load->view('quotation/pdf', $data, true);

        include APPPATH . "third_party/dompdf/autoload.inc.php";
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();

        $dompdf->load_html($html);

        $paper_size = 'a4';
        $orientation = 'portrait';

        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size, $orientation);

        //and getting rend
        $dompdf->render();

        $dompdf->stream($data['data'][0]->quotation_invoice_number, array(
            'Attachment' => 0));
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        $quotation_module_id = $this->config->item('quotation_module');
        $quotation_module_id = $this->config->item('quotation_module');
        $data['module_id'] = $quotation_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
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
        // $this->general_model->updateData('quotation_item', array(
        //     'delete_status' => 1), array(
        //     'quotation_id' => $id));

        if ($this->general_model->updateData('quotation', array(
                    'delete_status' => 1), array(
                    'quotation_id' => $id))) {
            $successMsg = 'Quotation Deleted Successfully';
            $this->session->set_flashdata('quotation_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'quotation',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Quotation Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('quotation');
        } else {
            $errorMsg = 'Quotation Delete Unsuccessful';
            $this->session->set_flashdata('quotation_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Category can not be Deleted.');
            redirect("quotation", 'refresh');
        }
    }

    public function email($id) {
        $id = $this->encryption_url->decode($id);
        $quotation_module_id = $this->config->item('quotation_module');
        $data['module_id'] = $quotation_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        $data['access_modules'] = $section_modules['active_modules'];
        $data['access_sub_modules'] = $section_modules['access_sub_modules'];
        /* $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege']   = $section_modules['user_privilege']; */
        $data['access_settings'] = $section_modules['access_settings'];
        $data['access_common_settings'] = $section_modules['access_common_settings'];

        $email_sub_module_id = $this->config->item('email_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
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

        $email_sub_module = 0;
        foreach ($data['access_sub_modules'] as $key => $value) {
            if ($email_sub_module_id == $value) {
                $email_sub_module = 1;
            }
        }
        if($this->session->userdata('SESS_FIRM_ID') == $this->config->item('Sanath')){
            $hsn_data = $this->common->hsn_quotation_list_item_field1($id);
            $data['hsn'] = $this->general_model->getPageJoinRecords($hsn_data);
        }

        if ($email_sub_module == 1) {
            ob_start();
            $html = ob_get_clean();
            $html = utf8_encode($html);
            $data = $this->getQuotationDetail($id);
            $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
            $currency = $this->getBranchCurrencyCode();
            $data['data'][0]->currency_code = $currency[0]->currency_code;
            $data['data'][0]->currency_symbol = $currency[0]->currency_symbol;
            
            $html = $this->load->view('quotation/pdf', $data, true);
            /* echo $html;exit(); */
            /* include APPPATH . 'third_party/mpdf60/mpdf.php';
              $mpdf                           = new mPDF();
              $mpdf->allow_charset_conversion = true;
              $mpdf->charset_in               = 'UTF-8';
              $file_path                      = "././pdf_form/";
              $mpdf->WriteHTML($html);
              $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->quotation_invoice_number);
              $mpdf->Output($file_path . $file_name . '.pdf', 'F'); */
            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->quotation_invoice_number);
            $file_name = str_replace('/','_',$file_name);
            $dompdf = new Dompdf\Dompdf();

            $paper_size = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name'] = $file_name . '.pdf';
            $quotation_data = $this->common->quotation_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($quotation_data['string'], $quotation_data['table'], $quotation_data['where'], $quotation_data['join']);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id' => $quotation_module_id,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('quotation/email', $data);
        } else {
            $this->load->view('quotation', $data);
        }
    }

    public function convert_currency() {
        $id = $this->input->post('convert_currency_id');
        $id = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');
        $converted_date = date('Y-m-d', strtotime($this->input->post('conversion_date')));
        $data = array(
            'currency_converted_rate' => $new_converted_rate,
            'converted_grand_total' => $this->input->post('currency_converted_amount'),
            'currency_converted_date' => $converted_date );
        $this->general_model->updateData('quotation', $data, array(
            'quotation_id' => $id));
        redirect('quotation', 'refresh');
    }

    public function email_popup($id) {
        $id = $this->encryption_url->decode($id);
        $quotation_module_id = $this->config->item('quotation_module');
        $data['module_id'] = $quotation_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        $data['access_modules'] = $section_modules['active_modules'];
        $data['access_sub_modules'] = $section_modules['access_sub_modules'];
        /* $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege']   = $section_modules['user_privilege']; */
        $data['access_settings'] = $section_modules['access_settings'];
        $data['access_common_settings'] = $section_modules['access_common_settings'];

        $email_sub_module_id = $this->config->item('email_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
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

        $email_sub_module = 0;
        foreach ($data['access_sub_modules'] as $key => $value) {
            if ($email_sub_module_id == $value) {
                $email_sub_module = 1;
            }
        }

        if ($email_sub_module == 1) {
            ob_start();
            $html = ob_get_clean();
            $html = utf8_encode($html);
            $data = $this->getQuotationDetail($id);
            $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
            $currency = $this->getBranchCurrencyCode();
            $data['data'][0]->currency_code = $currency[0]->currency_code;
            $data['data'][0]->currency_symbol = $currency[0]->currency_symbol;
             $data['data'][0]->currency_symbol_pdf = $currency[0]->currency_symbol_pdf;
            $html = $this->load->view('quotation/pdf', $data, true);
            /* echo $html;exit(); */
            /* include APPPATH . 'third_party/mpdf60/mpdf.php';
              $mpdf                           = new mPDF();
              $mpdf->allow_charset_conversion = true;
              $mpdf->charset_in               = 'UTF-8';
              $file_path                      = "././pdf_form/";
              $mpdf->WriteHTML($html);
              $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->quotation_invoice_number);
              $mpdf->Output($file_path . $file_name . '.pdf', 'F'); */
            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->quotation_invoice_number);
            $dompdf = new Dompdf\Dompdf();

            $paper_size = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name'] = $file_name . '.pdf';
            $quotation_data = $this->common->quotation_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($quotation_data['string'], $quotation_data['table'], $quotation_data['where'], $quotation_data['join']);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id' => $quotation_module_id,
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

}
