<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Purchase_order extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('purchase_order_model');
        $this->load->model('general_model');
        $this->load->model('ledger_model');
        $this->modules = $this->get_modules();
    }
    public function index() {
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /* Modules Present */
        $data['purchase_order_module_id'] = $purchase_order_module_id;
        $data['payment_voucher_module_id'] = $this->config->item('payment_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id'] = $this->config->item('email_module');
        $data['recurrence_module_id'] = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id'] = $this->config->item('recurrence_sub_module');
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'supplier',
                2 => 'grand_total',
                3 => 'converted_grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'added_user',
                8 => 'action');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->purchase_order_list_field();
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
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency[0]->currency_code;
            $data['currency_symbol'] = $currency[0]->currency_symbol;
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $purchase_order_id = $this->encryption_url->encode($post->purchase_order_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->purchase_order_date));
                    $nestedData['supplier'] = $post->supplier_name . ' (<a href="' . base_url('purchase_order/view/') . $purchase_order_id . '">' . $post->purchase_order_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $currency[0]->currency_symbol . ' ' . $this->precise_amount($post->purchase_order_grand_total, $access_common_settings[0]->amount_precision);
                    $nestedData['converted_grand_total'] = $this->precise_amount($post->converted_grand_total, $access_common_settings[0]->amount_precision);
                    if ($post->purchase_id != "" && $post->purchase_id > 0 && $post->purchase_id != null) {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } else {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($purchase_order_module_id, $data['active_view'])) {
                        $cols .= '<span><a href="' . base_url('purchase_order/view/') . $purchase_order_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="View Purchase Order">
                                    <i class="fa fa-eye"></i>
                            </a></span>';
                    }
                    $cols .= '<span><a href="' . base_url('purchase_order/pdf/') . $purchase_order_id .' "class="btn btn-app pdf_button" data-name="regular" data-toggle="tooltip" data-placement="bottom" title="Download PDF" target="_blank"><i class="fa fa-file-pdf-o"></i></a></span>';
                    if (in_array($purchase_order_module_id, $data['active_edit'])) {
                        if($post->purchase_id == "" || $post->purchase_id == 0 || $post->purchase_id == null)
                        $cols .= '<span> <a href="' . base_url('purchase_order/edit/') . $purchase_order_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Edit Purchase Order">
                                    <i class="fa fa-pencil"></i>
                            </a></span>';
                    }
                    if ($post->purchase_id == "" || $post->purchase_id == 0 || $post->purchase_id == null) {
                        $cols .= '<span><a href="' . base_url('purchase_order/convert_purchase_order/') . $purchase_order_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Move to Purchase">
                                    <i class="fa fa-reply"></i>
                            </a></span>';
                    }

                    /*if (in_array($data['email_module_id'], $data['active_view'])) {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules'])) {
                            $cols .= '<span> <a href="' . base_url('purchase_order/email/') . $purchase_order_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Email purchase Order">
                                    <i class="fa fa-envelope-o"></i>
                            </a></span>';
                        }
                    }*/
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail" >
                        <a class="btn btn-app composeMail" data-id="' . $purchase_order_id . '" data-name="regular"  href="javascript:void(0);" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Email purchase Order"><i class="fa fa-envelope-o"></i></a></span>';
                    
                    if (in_array($purchase_order_module_id, $data['active_delete'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a class="btn btn-app delete_button" data-id="' . $purchase_order_id . '" data-path="purchase_order/delete" href="#" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" data-placement="bottom" data-toggle="tooltip" title="Delete Purchase Order">
                                    <i class="fa fa-trash-o"></i></a>
                            </span>';
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
            $this->load->view('purchase_order/list', $data);
        }
    }
    public function add() {
        $data = $this->get_default_country_state();
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /* Modules Present */
        $data['purchase_order_module_id'] = $purchase_order_module_id;
        $data['module_id'] = $purchase_order_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['supplier_module_id'] = $this->config->item('supplier_module');
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
            $data['uqc_service']      = $this->uqc_product_service_call('service');
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['chapter'] = $this->chapter_call();
            $data['hsn'] = $this->hsn_call();
            $data['tax_tds'] = $this->tax_call_type('TDS');
            $data['tax_tcs'] = $this->tax_call_type('TCS');
            $data['tax_gst'] = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();
            if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }
        }
        $access_settings = $data['access_settings'];
        $primary_id = "purchase_order_id";
        $table_name = $this->config->item('purchase_order_table');
        $date_field_name = "purchase_order_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        /* echo "<pre>";
          print_r($data); exit(); */
        $this->load->view('purchase_order/add', $data);
    }
    public function get_supplier_place() {
        $supplier_id = $this->input->post('supplier_id');
        $table = 'supplier';
        $where = array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'supplier_id' => $supplier_id,
            'delete_status' => 0);
        $data['data'] = $this->general_model->getRecords('*', $table, $where);
        $branch_data = $this->common->branch_field();
        $branch_details = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        if ($data['data'][0]->supplier_country_id != $branch_details[0]->branch_country_id) {
            $data['state'] = 0;
        } else {
            $table1 = 'states';
            $where1 = array(
                'country_id' => $data['data'][0]->supplier_country_id,
                'delete_status' => 0);
            $data['state'] = $this->general_model->getRecords('*', $table1, $where1);
        }
        echo json_encode($data);
    }
    public function get_product_code() {
        if ($data1 = $this->general_model->getRecords('product_id', 'products', "", array(
            'product_id' => 'desc'))) {
            $no = $data1[0]->product_id;
        } else {
            $no = 0;
        }
        $sum = sprintf('%03d', intval($no) + 1);
        $final_reference_no = "PCODE-" . $sum;
        $data = [
            "product_code" => $final_reference_no];
        echo json_encode($data);
    }
    public function add_purchase_order() {
        $data = $this->get_default_country_state();
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $module_id = $purchase_order_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /* Modules Present */
        $data['purchase_order_module_id'] = $purchase_order_module_id;
        $data['module_id'] = $purchase_order_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['supplier_module_id'] = $this->config->item('supplier_module');
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
            $primary_id = "purchase_order_id";
            $table_name = $this->config->item('purchase_order_table');
            $date_field_name = "purchase_order_date";
            $current_date = date('Y-m-d',strtotime($this->input->post('invoice_date')));
            $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }
        $supplier = explode("-", $this->input->post('supplier'));
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;
        $purchase_order_data = array(
            "purchase_order_date" => date('Y-m-d',strtotime($this->input->post('invoice_date'))),
            "purchase_order_invoice_number" => $invoice_number,
            "purchase_order_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "purchase_order_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "purchase_order_discount_amount" => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0,
            "purchase_order_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "purchase_order_tax_cess_amount" => 0,
            "purchase_order_taxable_value" => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0,
            "purchase_order_tds_amount" => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0,
            "purchase_order_tcs_amount"
            => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0,
            "purchase_order_igst_amount" => 0,
            "purchase_order_cgst_amount" => 0,
            "purchase_order_sgst_amount" => 0,
            "from_account" => 'supplier',
            "to_account" => 'purchase_order',
            "purchase_order_paid_amount" => 0,
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "purchase_order_party_id" => $this->input->post('supplier'),
            "purchase_order_party_type" => "supplier",
            "purchase_order_nature_of_supply" => $this->input->post('nature_of_supply'),
            "purchase_order_type_of_supply" => $this->input->post('type_of_supply'),
            "purchase_order_order_number" => $this->input->post('order_number'),
            "purchase_order_order_date" => date('Y-m-d', strtotime($this->input->post('purchase_order_order_date'))),
            "purchase_order_gst_payable" => $this->input->post('gst_payable'),
            "purchase_order_billing_country_id" => $this->input->post('billing_country'),
            "purchase_order_billing_state_id" => $this->input->post('billing_state'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "purchase_order_supplier_invoice_number" => $this->input->post('supplier_ref'),
            "purchase_order_supplier_date" => date('Y-m-d', strtotime($this->input->post('supplier_date'))),
            "purchase_order_delivery_challan_number" => $this->input->post('delivery_challan_number'),
            "purchase_order_delivery_date" => date('Y-m-d', strtotime($this->input->post('delivery_date'))),
            "purchase_order_received_via" => $this->input->post('received_via'),
            "purchase_order_e_way_bill_number" => $this->input->post('e_way_bill'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
            "updated_date" => "",
            "updated_user_id" => "",
            "warehouse_id" => "",
            "transporter_name" => $this->input->post('transporter_name'),
            "transporter_gst_number" => $this->input->post('transporter_gst_number'),
            "lr_no" => $this->input->post('lr_no'),
            "purchase_order_grn_number" => $this->input->post('grn_number'),
            "purchase_order_grn_date" => $this->input->post('grn_date'),
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
            "shipping_address_id" => $this->input->post('shipping_address_id'),
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
        $purchase_order_data['freight_charge_tax_id'] = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $purchase_order_data['insurance_charge_tax_id'] = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $purchase_order_data['packing_charge_tax_id'] = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $purchase_order_data['incidental_charge_tax_id'] = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $purchase_order_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $purchase_order_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        $round_off_value = $purchase_order_data['purchase_order_grand_total'];
        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" && $this->input->post('round_off_key') == "yes") {
            if ($this->input->post('round_off_value') != "" && $this->input->post('round_off_value') > 0) {
                $round_off_value = $this->input->post('round_off_value');
            }
        }
        $purchase_order_data['round_off_amount'] = bcsub($purchase_order_data['purchase_order_grand_total'], $round_off_value, $section_modules['access_common_settings'][0]->amount_precision);
        $purchase_order_data['purchase_order_grand_total'] = $round_off_value;
        $purchase_order_data['supplier_payable_amount'] = $purchase_order_data['purchase_order_grand_total'];
        if (isset($purchase_order_data['purchase_order_tds_amount']) && $purchase_order_data['purchase_order_tds_amount'] > 0) {
            $purchase_order_data['supplier_payable_amount'] = bcsub($purchase_order_data['purchase_order_grand_total'], $purchase_order_data['purchase_order_tds_amount']);
        }
        //$purchase_order_tax_amount = $purchase_order_data['purchase_order_tax_amount'];
        $purchase_order_tax_amount = $purchase_order_data['purchase_order_tax_amount'] + (float) ($this->input->post('total_other_taxable_amount'));
        if ($section_modules['access_settings'][0]->tax_type == "gst") {
            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            if ($data['branch'][0]->branch_country_id == $purchase_order_data['purchase_order_billing_country_id']) {
                if ($data['branch'][0]->branch_state_id == $purchase_order_data['purchase_order_billing_state_id']) {
                    $purchase_order_data['purchase_order_igst_amount'] = 0;
                    $purchase_order_data['purchase_order_cgst_amount'] = ($purchase_order_tax_amount * $cgst_amount_percentage) / 100;
                    $purchase_order_data['purchase_order_sgst_amount'] = ($purchase_order_tax_amount * $sgst_amount_percentage) / 100;
                    $purchase_order_data['purchase_order_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $purchase_order_data['purchase_order_igst_amount'] = $purchase_order_tax_amount;
                    $purchase_order_data['purchase_order_cgst_amount'] = 0;
                    $purchase_order_data['purchase_order_sgst_amount'] = 0;
                    $purchase_order_data['purchase_order_tax_cess_amount'] = $total_cess_amnt;
                }
            } /* else {
              if ($purchase_order_data['purchase_order_type_of_supply'] == "export_with_payment"){
              $purchase_order_data['purchase_order_igst_amount'] = $purchase_order_tax_amount;
              $purchase_order_data['purchase_order_cgst_amount'] = 0;
              $purchase_order_data['purchase_order_sgst_amount'] = 0;
              $purchase_order_data['purchase_order_tax_cess_amount'] = $total_cess_amnt;
              }
              } */
        }
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $purchase_order_data['converted_grand_total'] = $purchase_order_data['purchase_order_grand_total'];
        } else {
            $purchase_order_data['converted_grand_total'] = 0;
        }
        $data_main = array_map('trim', $purchase_order_data);
        $purchase_order_table = $this->config->item('purchase_order_table');
        /* echo "<pre>";
          print_r($this->input->post());
          echo "<br>";
          echo $purchase_order_data['purchase_order_billing_country_id'];
          print_r($purchase_order_data);
          exit(); */
        $purchase_order_id = $this->general_model->insertData($purchase_order_table, $data_main);
        if ($purchase_order_id) {
            $successMsg = 'Purchase Order Added Successfully';
            $this->session->set_flashdata('purchase_order_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $purchase_order_id,
                'table_name' => $purchase_order_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'purchase_order Inserted');
            $data_main['purchase_order_id'] = $purchase_order_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $purchase_order_item_data = $this->input->post('table_data');
            $js_data = json_decode($purchase_order_item_data);
            $js_data               = array_reverse($js_data);
            $item_table = $this->config->item('purchase_order_item_table');
            if (!empty($js_data)) {
                $js_data1 = array();
                foreach ($js_data as $key => $value) {
                    if ($value != null && $value != '') {
                        $item_id = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $item_data = array(
                            "item_id" => $value->item_id,
                            "item_type" => $value->item_type,
                            "purchase_order_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "purchase_order_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "purchase_order_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "purchase_order_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "purchase_order_item_discount_amount" => $value->item_discount_amount ? (float) $value->item_discount_amount : 0,
                            "purchase_order_item_discount_id" => $value->item_discount_id ? (float) $value->item_discount_id : 0,
                            "purchase_order_item_discount_percentage" => $value->item_discount_percentage ? (float) $value->item_discount_percentage : 0,
                            "purchase_order_item_tds_id" => $value->item_tds_id ? (float) $value->item_tds_id : 0,
                            "purchase_order_item_tds_percentage" => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0,
                            "purchase_order_item_tds_amount" => $value->item_tds_amount ? (float) $value->item_tds_amount : 0,
                            "purchase_order_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "purchase_order_item_tax_id" => $value->item_tax_id ? (float) $value->item_tax_id : 0,
                            "purchase_order_item_tax_cess_id" => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0,
                            "purchase_order_item_igst_percentage" => 0,
                            "purchase_order_item_igst_amount" => 0,
                            "purchase_order_item_cgst_percentage" => 0,
                            "purchase_order_item_cgst_amount" => 0,
                            "purchase_order_item_sgst_percentage" => 0,
                            "purchase_order_item_sgst_amount" => 0,
                            "purchase_order_item_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "purchase_order_item_tax_cess_percentage" => 0,
                            "purchase_order_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            'purchase_order_item_tax_cess_amount' => 0,
                            "purchase_order_item_description" => $value->item_description ? $value->item_description : "",
                            "purchase_order_id" => $purchase_order_id);

                        $purchase_order_item_tax_amount = $item_data['purchase_order_item_tax_amount'];
                        $purchase_order_item_tax_percentage = $item_data['purchase_order_item_tax_percentage'];

                        if ($section_modules['access_settings'][0]->tax_type == "gst") {
                            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0;

                            if ($data['branch'][0]->branch_country_id == $purchase_order_data['purchase_order_billing_country_id']) {

                                if ($data['branch'][0]->branch_state_id == $purchase_order_data['purchase_order_billing_state_id']) {
                                    $item_data['purchase_order_item_igst_amount'] = 0;
                                    $item_data['purchase_order_item_cgst_amount'] = ($purchase_order_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_sgst_amount'] = ($purchase_order_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['purchase_order_item_igst_percentage'] = 0;
                                    $item_data['purchase_order_item_cgst_percentage'] = ($purchase_order_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_sgst_percentage'] = ($purchase_order_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                } else {
                                    $item_data['purchase_order_item_igst_amount'] = $purchase_order_item_tax_amount;
                                    $item_data['purchase_order_item_cgst_amount'] = 0;
                                    $item_data['purchase_order_item_sgst_amount'] = 0;
                                    $item_data['purchase_order_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['purchase_order_item_igst_percentage'] = $purchase_order_item_tax_percentage;
                                    $item_data['purchase_order_item_cgst_percentage'] = 0;
                                    $item_data['purchase_order_item_sgst_percentage'] = 0;
                                    $item_data['purchase_order_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                            /* else
                              {
                              if ($purchase_order_data['purchase_order_type_of_supply'] == "export_with_payment")
                              {
                              $item_data['purchase_order_item_igst_amount'] = $purchase_order_item_tax_amount;
                              $item_data['purchase_order_item_cgst_amount'] = 0;
                              $item_data['purchase_order_item_sgst_amount'] = 0;
                              $item_data['purchase_order_item_tax_cess_amount'] = $item_tax_cess_amount;

                              $item_data['purchase_order_item_igst_percentage'] = $purchase_order_item_tax_percentage;
                              $item_data['purchase_order_item_cgst_percentage'] = 0;
                              $item_data['purchase_order_item_sgst_percentage'] = 0;
                              $item_data['purchase_order_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                              }
                              } */
                        }

                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                        $this->db->insert($item_table, $data_item);
                    }
                }
            }
        }
        redirect('purchase_order', 'refresh');
    }

    public function view($id) {
        $id = $this->encryption_url->decode($id);
        $data = array();
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $data['email_module_id'] = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id'] = $purchase_order_module_id;
        $data['purchase_order_module_id'] = $purchase_order_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $purchase_order_data = $this->common->purchase_order_list_field1($id);

        $data['data'] = $this->general_model->getJoinRecords($purchase_order_data['string'], $purchase_order_data['table'], $purchase_order_data['where'], $purchase_order_data['join']);

        $item_types = $this->general_model->getRecords('item_type,purchase_order_item_description', 'purchase_order_item', array('purchase_order_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->purchase_order_item_description != "") {
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

        $purchase_order_service_items = array();
        $purchase_order_product_items = array();

        if (($data['data'][0]->purchase_order_nature_of_supply == "service" || $data['data'][0]->purchase_order_nature_of_supply == "both") && $service == 1) {
            $service_items = $this->common->purchase_order_items_service_list_field($id);
            $purchase_order_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "product" || $data['data'][0]->purchase_order_nature_of_supply == "both") {

            /* if ($product == 2)
              {
              $product_items          = $this->common->purchase_order_items_product_inventory_list_field($id);
              $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              if ($product == 1)
              {
              } */
            $product_items = $this->common->purchase_order_items_product_list_field($id);
            $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_order_product_items, $purchase_order_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;
        $cess_exist = 0;

        if ($data['data'][0]->purchase_order_tax_amount > 0 && $data['data'][0]->purchase_order_igst_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {

            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount > 0 || $data['data'][0]->purchase_order_sgst_amount > 0) && $data['data'][0]->purchase_order_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount == 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_order_tds_amount > 0 || $data['data'][0]->purchase_order_tcs_amount > 0) {
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->purchase_order_discount_amount > 0) {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0) {
            /* Discount */
            $descriptionExist = 1;
        }

        if ($data['data'][0]->purchase_order_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_order_billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['tax_exist'] = $taxExist;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;
        $data['is_utgst'] = $is_utgst;
        $data['cess_exist'] = $cess_exist;
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code'] = $currency[0]->currency_code;
        $data['currency_symbol'] = $currency[0]->currency_symbol;
        /* echo "<pre>";
          print_r($data);
          exit(); */
        $this->load->view('purchase_order/view', $data);
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['purchase_order_module_id'] = $purchase_order_module_id;
        $data['module_id'] = $purchase_order_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['supplier_module_id'] = $this->config->item('supplier_module');
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

        $data['data'] = $this->general_model->getRecords('*', 'purchase_order', array(
            'purchase_order_id' => $id));

        $data['shipping_address'] = $this->general_model->getRecords('*', 'shipping_address', array(
            'shipping_party_id' => $data['data'][0]->purchase_order_party_id,
            'shipping_party_type' => $data['data'][0]->purchase_order_party_type
        ));

        $item_types = $this->general_model->getRecords('item_type,purchase_order_item_description', 'purchase_order_item', array(
            'purchase_order_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->purchase_order_item_description != "") {
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

        $data['supplier'] = $this->supplier_call();
        $data['currency'] = $this->currency_call();

        if ($data['data'][0]->purchase_order_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax") {

            $data['tax'] = $this->tax_call();
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "service" || $data['data'][0]->purchase_order_nature_of_supply == "both") {

            $data['sac'] = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "product" || $data['data'][0]->purchase_order_nature_of_supply == "both") {

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
            $data['tax_tds'] = $this->tax_call_type('TDS');
            $data['tax_tcs'] = $this->tax_call_type('TCS');
            $data['tax_gst'] = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();

            /* if ($data['inventory_access'] == "yes")
              {
              $data['get_product_inventory'] = $this->get_product_inventory();
              $data['varients_key']          = $this->general_model->getRecords('*', 'varients', array(
              'delete_status' => 0,
              'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
              } */
        }

        $purchase_order_service_items = array();
        $purchase_order_product_items = array();

        if (($data['data'][0]->purchase_order_nature_of_supply == "service" || $data['data'][0]->purchase_order_nature_of_supply == "both") && $service == 1) {

            $service_items = $this->common->purchase_order_items_service_list_field($id);
            $purchase_order_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "product" || $data['data'][0]->purchase_order_nature_of_supply == "both") {

            /* if ($product == 2)
              {
              $product_items          = $this->common->purchase_order_items_product_inventory_list_field($id);
              $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              if ($product == 1)
              {
              } */
            $product_items = $this->common->purchase_order_items_product_list_field($id);
            $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_order_product_items, $purchase_order_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->purchase_order_tax_amount > 0 && $data['data'][0]->purchase_order_igst_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount > 0 || $data['data'][0]->purchase_order_sgst_amount > 0) && $data['data'][0]->purchase_order_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount == 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_order_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes") {
            /* Discount */
            $discountExist = 1;
            $data['discount'] = $this->discount_call();
        }

        if ($data['data'][0]->purchase_order_tds_amount > 0 || $data['data'][0]->purchase_order_tcs_amount > 0 || $data['access_settings'][0]->tds_visible == "yes") {
            /* Discount */
            $tdsExist = 1;
        }

        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes") {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if ($data['data'][0]->purchase_order_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_order_billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['cess_exist'] = $cess_exist;
        $data['tax_exist'] = $taxExist;
        $data['is_utgst'] = $is_utgst;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;

        $this->load->view('purchase_order/edit', $data);
    }

    public function edit_purchase_order() {
        $data = $this->get_default_country_state();
        $purchase_order_id = $this->input->post('purchase_order_id');
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $module_id = $purchase_order_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);


        /* Modules Present */
        $data['purchase_order_module_id'] = $purchase_order_module_id;
        $data['module_id'] = $purchase_order_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['supplier_module_id'] = $this->config->item('supplier_module');
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


        $currency = $this->input->post('currency_id');
        if ($section_modules['access_settings'][0]->invoice_creation == "automatic") {
            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old')) {
                $primary_id = "purchase_order_id";
                $table_name = $this->config->item('purchase_order_table');
                $date_field_name = "purchase_order_date";
                $current_date = date('Y-m-d',strtotime($this->input->post('invoice_date')));
                $invoice_number = $this->generate_invoice_number($section_modules['access_settings'], $primary_id, $table_name, $date_field_name, $current_date);
            } else {
                $invoice_number = $this->input->post('invoice_number');
            }
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;
        $purchase_order_data = array(
            "purchase_order_date" => date('Y-m-d',strtotime($this->input->post('invoice_date'))),
            "purchase_order_invoice_number" => $invoice_number,
            "purchase_order_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "purchase_order_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "purchase_order_discount_amount" => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0,
            "purchase_order_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "purchase_order_tax_cess_amount" => 0,
            "purchase_order_taxable_value" => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0,
            "purchase_order_tds_amount" => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0,
            "purchase_order_tcs_amount"
            => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0,
            "purchase_order_igst_amount" => 0,
            "purchase_order_cgst_amount" => 0,
            "purchase_order_sgst_amount" => 0,
            "from_account" => 'supplier',
            "to_account" => 'purchase_order',
            "purchase_order_paid_amount" => 0,
            "purchase_order_supplier_invoice_number" => $this->input->post('supplier_ref'),
            "purchase_order_supplier_date" => date('Y-m-d', strtotime($this->input->post('supplier_date'))),
            "purchase_order_delivery_challan_number" => $this->input->post('delivery_challan_number'),
            "purchase_order_delivery_date" => date('Y-m-d', strtotime($this->input->post('delivery_date'))),
            "purchase_order_e_way_bill_number" => $this->input->post('e_way_bill'),
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "purchase_order_party_id" => $this->input->post('supplier'),
            "purchase_order_party_type" => "supplier",
            "purchase_order_nature_of_supply" => $this->input->post('nature_of_supply'),
            "purchase_order_order_number" => $this->input->post('order_number'),
            "purchase_order_order_date" => date('Y-m-d', strtotime($this->input->post('purchase_order_order_date'))),
            "purchase_order_received_via" => $this->input->post('received_via'),
            "purchase_order_grn_number" => $this->input->post('grn_number'),
            "purchase_order_grn_date" => $this->input->post('grn_date'),
            "purchase_order_type_of_supply" => $this->input->post('type_of_supply'),
            "purchase_order_gst_payable" => $this->input->post('gst_payable'),
            "purchase_order_billing_country_id" => $this->input->post('billing_country'),
            "purchase_order_billing_state_id" => $this->input->post('billing_state'),
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
            "shipping_address_id" => $this->input->post('shipping_address_id'),
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

        $purchase_order_data['freight_charge_tax_id'] = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $purchase_order_data['insurance_charge_tax_id'] = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $purchase_order_data['packing_charge_tax_id'] = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $purchase_order_data['incidental_charge_tax_id'] = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $purchase_order_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $purchase_order_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        $round_off_value = $purchase_order_data['purchase_order_grand_total'];

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" || $this->input->post('round_off_key') == "yes") {
            if ($this->input->post('round_off_value') != "" && $this->input->post('round_off_value') > 0) {
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $purchase_order_data['round_off_amount'] = bcsub($purchase_order_data['purchase_order_grand_total'], $round_off_value, $section_modules['access_common_settings'][0]->amount_precision);

        $purchase_order_data['purchase_order_grand_total'] = $round_off_value;

        $purchase_order_data['supplier_payable_amount'] = $purchase_order_data['purchase_order_grand_total'];
        if (isset($purchase_order_data['purchase_order_tds_amount']) && $purchase_order_data['purchase_order_tds_amount'] > 0) {
            $purchase_order_data['supplier_payable_amount'] = bcsub($purchase_order_data['purchase_order_grand_total'], $purchase_order_data['purchase_order_tds_amount']);
        }

        $tax_type = $this->input->post('tax_type');
        $purchase_order_tax_amount = $purchase_order_data['purchase_order_tax_amount'];
        $purchase_order_tax_amount = $purchase_order_data['purchase_order_tax_amount'] + (float) ($this->input->post('total_other_taxable_amount'));
        if ($tax_type == "gst") {
            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

            if ($data['branch'][0]->branch_country_id == $purchase_order_data['purchase_order_billing_country_id']) {

                if ($data['branch'][0]->branch_state_id == $purchase_order_data['purchase_order_billing_state_id']) {
                    $purchase_order_data['purchase_order_igst_amount'] = 0;
                    $purchase_order_data['purchase_order_cgst_amount'] = ($purchase_order_tax_amount * $cgst_amount_percentage) / 100;
                    $purchase_order_data['purchase_order_sgst_amount'] = ($purchase_order_tax_amount * $sgst_amount_percentage) / 100;
                    $purchase_order_data['purchase_order_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $purchase_order_data['purchase_order_igst_amount'] = $purchase_order_tax_amount;
                    $purchase_order_data['purchase_order_cgst_amount'] = 0;
                    $purchase_order_data['purchase_order_sgst_amount'] = 0;
                    $purchase_order_data['purchase_order_tax_cess_amount'] = $total_cess_amnt;
                }
            }
            /* else
              {
              if ($purchase_order_data['purchase_order_type_of_supply'] == "export_with_payment")
              {
              $purchase_order_data['purchase_order_igst_amount'] = $purchase_order_tax_amount;
              $purchase_order_data['purchase_order_cgst_amount'] = 0;
              $purchase_order_data['purchase_order_sgst_amount'] = 0;
              $purchase_order_data['purchase_order_tax_cess_amount'] = $total_cess_amnt;
              }
              } */
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $purchase_order_data['converted_grand_total'] = $purchase_order_data['purchase_order_grand_total'];
        } else {
            $purchase_order_data['converted_grand_total'] = 0;
        }

        $data_main = array_map('trim', $purchase_order_data);
        $purchase_order_table = $this->config->item('purchase_order_table');
        $where = array(
            'purchase_order_id' => $purchase_order_id);

        if ($this->general_model->updateData($purchase_order_table, $data_main, $where)) {
            $successMsg = 'Purchase Order Updated Successfully';
            $this->session->set_flashdata('purchase_order_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $purchase_order_id,
                'table_name' => $purchase_order_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'purchase_order Updated');
            $data_main['purchase_order_id'] = $purchase_order_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $purchase_order_item_data = $this->input->post('table_data');

            $js_data = json_decode($purchase_order_item_data);
            $js_data               = array_reverse($js_data);
            $item_table = $this->config->item('purchase_order_item_table');
            if (!empty($js_data)) {
                $js_data1 = array();
                $new_item_ids = $this->getValues($js_data, 'item_id');

                $string = 'purchase_order_item_id,purchase_order_item_quantity,item_type,item_id';
                $table = 'purchase_order_item';
                $where = array(
                    'purchase_order_id' => $purchase_order_id,
                    'delete_status' => 0);
                $old_purchase_order_items = $this->general_model->getRecords($string, $table, $where, $order = "");
                $old_item_ids = $this->getValues($old_purchase_order_items, 'item_id');
                $not_deleted_ids = array();


                foreach ($js_data as $key => $value) {
                    if ($value != null) {
                        $item_id = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $item_data = array(
                            "item_id" => $value->item_id,
                            "item_type" => $value->item_type,
                            "purchase_order_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "purchase_order_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "purchase_order_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "purchase_order_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "purchase_order_item_discount_amount" => $value->item_discount_amount ? (float) $value->item_discount_amount : 0,
                            "purchase_order_item_discount_id" => $value->item_discount_id ? (float) $value->item_discount_id : 0,
                            "purchase_order_item_tds_id" => $value->item_tds_id ? (float) $value->item_tds_id : 0,
                            "purchase_order_item_tds_percentage" => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0,
                            "purchase_order_item_tds_amount" => $value->item_tds_amount ? (float) $value->item_tds_amount : 0,
                            "purchase_order_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "purchase_order_item_tax_id" => $value->item_tax_id ? (float) $value->item_tax_id : 0,
                            "purchase_order_item_tax_cess_id" => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0,
                            "purchase_order_item_igst_percentage" => 0,
                            "purchase_order_item_igst_amount" => 0,
                            "purchase_order_item_cgst_percentage" => 0,
                            "purchase_order_item_cgst_amount" => 0,
                            "purchase_order_item_sgst_percentage" => 0,
                            "purchase_order_item_sgst_amount" => 0,
                            "purchase_order_item_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "purchase_order_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            "purchase_order_item_tax_cess_percentage" => 0,
                            "purchase_order_item_tax_cess_amount" => 0,
                            "purchase_order_item_description" => $value->item_description ? $value->item_description : "",
                            "purchase_order_id" => $purchase_order_id);

                        $purchase_order_item_tax_amount = $item_data['purchase_order_item_tax_amount'];
                        $purchase_order_item_tax_percentage = $item_data['purchase_order_item_tax_percentage'];

                        if ($tax_type == "gst") {
                            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0;

                            if ($data['branch'][0]->branch_country_id == $purchase_order_data['purchase_order_billing_country_id']) {

                                if ($data['branch'][0]->branch_state_id == $purchase_order_data['purchase_order_billing_state_id']) {
                                    $item_data['purchase_order_item_igst_amount'] = 0;
                                    $item_data['purchase_order_item_cgst_amount'] = ($purchase_order_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_sgst_amount'] = ($purchase_order_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_order_item_igst_percentage'] = 0;
                                    $item_data['purchase_order_item_cgst_percentage'] = ($purchase_order_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_sgst_percentage'] = ($purchase_order_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_order_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                } else {
                                    $item_data['purchase_order_item_igst_amount'] = $purchase_order_item_tax_amount;
                                    $item_data['purchase_order_item_cgst_amount'] = 0;
                                    $item_data['purchase_order_item_sgst_amount'] = 0;
                                    $item_data['purchase_order_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_order_item_igst_percentage'] = $purchase_order_item_tax_percentage;
                                    $item_data['purchase_order_item_cgst_percentage'] = 0;
                                    $item_data['purchase_order_item_sgst_percentage'] = 0;
                                    $item_data['purchase_order_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }/* else{
                              if ($purchase_order_data['purchase_order_type_of_supply'] == "export_with_payment"){
                              $item_data['purchase_order_item_igst_amount'] = $purchase_order_item_tax_amount;
                              $item_data['purchase_order_item_cgst_amount'] = 0;
                              $item_data['purchase_order_item_sgst_amount'] = 0;
                              $item_data['purchase_order_item_tax_cess_amount'] = $item_tax_cess_amount;
                              $item_data['purchase_order_item_igst_percentage'] = $purchase_order_item_tax_percentage;
                              $item_data['purchase_order_item_cgst_percentage'] = 0;
                              $item_data['purchase_order_item_sgst_percentage'] = 0;
                              $item_data['purchase_order_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                              }
                              } */
                        }

                        $table = 'purchase_order_item';
                        if (($item_key = array_search($value->item_id, $old_item_ids)) !== false) {
                            unset($old_item_ids[$item_key]);
                            $purchase_order_item_id = $old_purchase_order_items[$item_key]->purchase_order_item_id;
                            array_push($not_deleted_ids, $purchase_order_item_id);
                            $where = array('purchase_order_item_id' => $purchase_order_item_id);
                            $this->general_model->updateData($table, $item_data, $where);
                        } else {
                            $this->general_model->insertData($table, $item_data);
                        }

                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                    }
                }

                if (!empty($old_purchase_order_items)) {
                    foreach ($old_purchase_order_items as $key => $items) {
                        if (!in_array($items->purchase_order_item_id, $not_deleted_ids)) {
                            $table = 'purchase_order_item';
                            $where = array(
                                'purchase_order_item_id' => $items->purchase_order_item_id);
                            $purchase_order_data = array(
                                'delete_status' => 1);

                            $this->general_model->updateData($table, $purchase_order_data, $where);
                        }
                    }
                }
            }
            redirect('purchase_order', 'refresh');
        } else {
            $errorMsg = 'Purchase Order Update Unsuccessful';
            $this->session->set_flashdata('purchase_order_error',$errorMsg);
            redirect('purchase_order', 'refresh');
        }
    }

    public function convert_purchase_order($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_module_id = $this->config->item('purchase_order_module');
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['purchase_module_id'] = $purchase_module_id;
        $data['module_id'] = $purchase_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['supplier_module_id'] = $this->config->item('supplier_module');
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

        $data['data'] = $this->general_model->getRecords('*', 'purchase_order', array(
            'purchase_order_id' => $id));

        $data['shipping_address'] = $this->general_model->getRecords('*', 'shipping_address', array(
            'shipping_party_id' => $data['data'][0]->purchase_order_party_id,
            'shipping_party_type' => $data['data'][0]->purchase_order_party_type
        ));

        $item_types = $this->general_model->getRecords('item_type,purchase_order_item_description', 'purchase_order_item', array(
            'purchase_order_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->purchase_order_item_description != "") {
                $description++;
            }

            if ($value->item_type == "service") {
                $service = 1;
            } else
            if ($value->item_type == "product" || $value->item_type == "product_inventory") {
                $product = 1;
            } else
            if ($value->item_type == "product_inventory") {
                $product = 2;
            }
        }

        $data['product_exist'] = $product;
        $data['service_exist'] = $service;

        $data['supplier'] = $this->supplier_call();
        $data['currency'] = $this->currency_call();

        if ($data['data'][0]->purchase_order_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax") {

            $data['tax'] = $this->tax_call();
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "service" || $data['data'][0]->purchase_order_nature_of_supply == "both") {

            $data['sac'] = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "product" || $data['data'][0]->purchase_order_nature_of_supply == "both") {

            if ($product == 2) {
                $data['inventory_access'] = "yes";
            } else {
                $data['inventory_access'] = "no";
            }

            $data['product_category'] = $this->product_category_call();
            $data['uqc'] = $this->uqc_call();
            $data['chapter'] = $this->chapter_call();
            $data['hsn'] = $this->hsn_call();
            $data['uqc_service']      = $this->uqc_product_service_call('service');
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();
            /*if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }*/
        }

        $purchase_order_service_items = array();
        $purchase_order_product_items = array();

        if (($data['data'][0]->purchase_order_nature_of_supply == "service" || $data['data'][0]->purchase_order_nature_of_supply == "both") && $service == 1) {

            $service_items = $this->common->purchase_order_items_service_list_field($id);
            $purchase_order_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "product" || $data['data'][0]->purchase_order_nature_of_supply == "both") {
            $product_items = $this->common->purchase_order_items_product_list_field($id);
            $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_order_product_items, $purchase_order_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->purchase_order_tax_amount > 0 && $data['data'][0]->purchase_order_igst_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount > 0 || $data['data'][0]->purchase_order_sgst_amount > 0) && $data['data'][0]->purchase_order_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount == 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_order_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes") {
            /* Discount */
            $discountExist = 1;
            $data['discount'] = $this->discount_call();
        }

        if ($data['data'][0]->purchase_order_tds_amount > 0 || $data['data'][0]->purchase_order_tcs_amount > 0 || $data['access_settings'][0]->tds_visible == "yes") {
            /* Discount */
            $tdsExist = 1;
        }

        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes") {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if ($data['data'][0]->purchase_order_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_order_billing_state_id);
        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['cess_exist'] = $cess_exist;
        $data['tax_exist'] = $taxExist;
        $data['is_utgst'] = $is_utgst;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;

        $this->load->view('purchase_order/convert_purchase_order', $data);
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $data['module_id'] = $purchase_order_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);
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
        if ($this->general_model->updateData('purchase_order', array(
                    'delete_status' => 1), array(
                    'purchase_order_id' => $id))) {
            $successMsg = 'Purchase Order Deleted Successfully';
            $this->session->set_flashdata('purchase_order_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'purchase_order',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'purchase order Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('purchase_order');
        } else {
            $errorMsg = 'Purchase Order Delete Unsuccessful';
            $this->session->set_flashdata('purchase_order_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Category can not be Deleted.');
            redirect("purchase_order", 'refresh');
        }
    }

    public function pdf($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $data['module_id'] = $purchase_order_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);

        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $supplier_module_id = $this->config->item('supplier_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $data['currency'] = $this->currency_call();
        $purchase_order_data = $this->common->purchase_order_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($purchase_order_data['string'], $purchase_order_data['table'], $purchase_order_data['where'], $purchase_order_data['join']);
    /*    echo '<pre>';print_r(  $data['data']);exit;*/
        $item_types = $this->general_model->getRecords('item_type,purchase_order_item_description', 'purchase_order_item', array(
            'purchase_order_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->purchase_order_item_description != "") {
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

        $purchase_order_service_items = array();
        $purchase_order_product_items = array();

        if (($data['data'][0]->purchase_order_nature_of_supply == "service" || $data['data'][0]->purchase_order_nature_of_supply == "both") && $service == 1) {

            $service_items = $this->common->purchase_order_items_service_list_field($id);
            $purchase_order_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_order_nature_of_supply == "product" || $data['data'][0]->purchase_order_nature_of_supply == "both") {

            /* if ($product == 2)
              {
              $product_items          = $this->common->purchase_order_items_product_inventory_list_field($id);
              $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              if ($product == 1)
              {
              } */
            $product_items = $this->common->purchase_order_items_product_list_field($id);
            $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_order_product_items, $purchase_order_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $discountExist = 0;
        $tdsExist = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->purchase_order_tax_amount > 0 && $data['data'][0]->purchase_order_igst_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {

            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount > 0 || $data['data'][0]->purchase_order_sgst_amount > 0) && $data['data'][0]->purchase_order_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_order_tax_amount == 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_order_tds_amount > 0 || $data['data'][0]->purchase_order_tcs_amount > 0) {
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->purchase_order_discount_amount > 0) {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0) {
            /* Discount */
            $descriptionExist = 1;
        }

        $cess_exist = 0;
        if ($data['data'][0]->purchase_order_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_order_billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['tax_exist'] = $taxExist;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;
        $data['is_utgst'] = $is_utgst;
        $data['cess_exist'] = $cess_exist;

        if ($purchase_order_product_items && $purchase_order_service_items) {
            $nature_of_supply = "Product/Service";
        } elseif ($purchase_order_product_items) {
            $nature_of_supply = "Product";
        } elseif ($purchase_order_service_items) {
            $nature_of_supply = "Service";
        }

        $data['nature_of_supply'] = $nature_of_supply;

        $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";

        $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1'] = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2'] = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code'] = $currency[0]->currency_code;
        $data['currency_symbol'] = $currency[0]->currency_symbol;
        $data['currency_text'] = $currency[0]->currency_name;
        $data['currency_symbol_pdf'] = $currency[0]->currency_symbol_pdf;
        $data['data'][0]->unit = $currency[0]->unit;
        $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
        
        $pdf_json = $data['access_settings'][0]->pdf_settings;
        $rep = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);

        $html = $this->load->view('purchase_order/pdf', $data, true);

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

        $dompdf->stream($data['data'][0]->purchase_order_invoice_number, array(
            'Attachment' => 0));
    }

    public function purchase_order_return($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_order_return_module_id = $this->config->item('purchase_order_return_module');
        $data['module_id'] = $purchase_order_return_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_order_return_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];


        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $supplier_module_id = $this->config->item('supplier_module');
        $category_module_id = $this->config->item('category_module');
        $subcategory_module_id = $this->config->item('subcategory_module');
        $tax_module_id = $this->config->item('tax_module');
        $discount_module_id = $this->config->item('discount_module');
        $accounts_module_id = $this->config->item('accounts_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'supplier_module_id' => $supplier_module_id,
            'category_module_id' => $category_module_id,
            'subcategory_module_id' => $subcategory_module_id,
            'tax_module_id' => $tax_module_id,
            'discount_module_id' => $discount_module_id,
            'accounts_module_id' => $accounts_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);

        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;

            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            }

            if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            }

            if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            }

            if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        }
        $data['supplier'] = $this->supplier_call1();
        $data['discount'] = $this->discount_call1();
        $data['currency'] = $this->currency_call();
        $data['product_category'] = $this->product_category_call();
        $data['service_category'] = $this->service_category_call();
        $data['tax'] = $this->tax_call();
        $data['uqc'] = $this->uqc_call();
        $data['sac'] = $this->sac_call();
        $data['chapter'] = $this->chapter_call();
        $data['hsn'] = $this->hsn_call();
        $string = "*";
        $table = "purchase_order";
        $where = array(
            "purchase_order_id" => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        $product_items = $this->common->purchase_order_items_product_list_field($id, 0);
        $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        $service_items = $this->common->purchase_order_items_service_list_field($id);
        $purchase_order_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_order_product_items, $purchase_order_service_items);
        $access_settings = $data['access_settings'];
        $primary_id = "purchase_order_return_id";
        $table_name = "purchase_order_return";
        $date_field_name = "purchase_order_return_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['data'][0]->transporter_name = "";
        $data['data'][0]->transporter_gst_number = "";
        $data['data'][0]->lr_no = "";
        $data['data'][0]->vehicle_no = "";
        $data['data'][0]->mode_of_shipment = "";
        $data['data'][0]->ship_by = "";
        $data['data'][0]->net_weight = "";
        $data['data'][0]->gross_weight = "";
        $data['data'][0]->origin = "";
        $data['data'][0]->destination = "";
        $data['data'][0]->shipping_type = "";
        $data['data'][0]->shipping_type_place = "";
        $data['data'][0]->lead_time = "";
        $data['data'][0]->shipping_address = "";
        $data['data'][0]->warranty = "";
        $data['data'][0]->payment_mode = "";
        $data['data'][0]->freight_charge_amount = "";
        $data['data'][0]->freight_charge_tax = "";
        $data['data'][0]->freight_charge_tax_amount = "";
        $data['data'][0]->total_freight_charge = "";
        $data['data'][0]->insurance_charge_amount = "";
        $data['data'][0]->insurance_charge_tax = "";
        $data['data'][0]->insurance_charge_tax_amount = "";
        $data['data'][0]->total_insurance_charge = "";
        $data['data'][0]->packing_charge_amount = "";
        $data['data'][0]->packing_charge_tax = "";
        $data['data'][0]->packing_charge_tax_amount = "";
        $data['data'][0]->total_packing_charge = "";
        $data['data'][0]->incidental_charge_amount = "";
        $data['data'][0]->incidental_charge_tax = "";
        $data['data'][0]->incidental_charge_tax_amount = "";
        $data['data'][0]->total_incidental_charge = "";
        $data['data'][0]->inclusion_other_charge_amount = "";
        $data['data'][0]->inclusion_other_charge_tax = "";
        $data['data'][0]->inclusion_other_charge_tax_amount = "";
        $data['data'][0]->total_inclusion_other_charge = "";
        $data['data'][0]->exclusion_other_charge_amount = "";
        $data['data'][0]->exclusion_other_charge_tax = "";
        $data['data'][0]->exclusion_other_charge_tax_amount = "";
        $data['data'][0]->total_exclusion_other_charge = "";
        $data['data'][0]->total_other_amount = "";
        $data['data'][0]->note1 = "";
        $data['data'][0]->note2 = "";
        $data['tax'] = $this->tax_call();
        $this->load->view('purchase_order/purchase_order_return', $data);
    }

    public function get_purchase_order_item() {
        $purchase_order_id = $this->input->post('purchase_order_id');

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));

        /* if ($inventory_access[0]->inventory_advanced == "yes")
          {
          $product_items                = $this->common->purchase_order_items_product_inventory_list_field($purchase_order_id, 0);
          $purchase_order_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
          }
          else
          { */
        $product_items = $this->common->purchase_order_items_product_list_field($purchase_order_id, 0);
        $purchase_order_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        //}

        $service_items = $this->common->purchase_order_items_service_list_field($purchase_order_id);
        $purchase_order_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_order_items_product_items, $purchase_order_items_service_items);

        $branch_details = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id'] = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id'] = $branch_details['branch'][0]->branch_id;
        $discount_data = $this->common->discount_field();
        $data['discount'] = $this->general_model->getRecords($discount_data['string'], $discount_data['table'], $discount_data['where']);
        $purchase_order_data = $this->general_model->getRecords('currency_id, purchase_order_billing_state_id', 'purchase_order', array(
            'purchase_order_id' => $purchase_order_id,
            'delete_status' => 0));
        $data['billing_state_id'] = $purchase_order_data[0]->purchase_order_billing_state_id;
        $data['tax'] = $this->tax_call();
        $data['currency'] = $this->general_model->getRecords('currency_id,currency_name', 'currency', array(
            'currency_id' => $purchase_order_data[0]->currency_id));
        echo json_encode($data);
    }

    public function email($id) {
        $id = $this->encryption_url->decode($id);
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $data['module_id'] = $purchase_order_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;

            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            }

            if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            }

            if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            }

            if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        }

        $email_sub_module = 0;

        if (in_array($data['email_sub_module_id'], $data['access_sub_modules'])) {
            $email_sub_module = 1;
        }

        if ($email_sub_module == 1) {
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
            $purchase_order_data = $this->common->purchase_order_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_order_data['string'], $purchase_order_data['table'], $purchase_order_data['where'], $purchase_order_data['join']);
            $country_data = $this->common->country_field($data['data'][0]->purchase_order_billing_country_id);
            $data['data_country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data = $this->common->state_field($data['data'][0]->purchase_order_billing_country_id, $data['data'][0]->purchase_order_billing_state_id);
            $data['data_state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));

            /* if ($inventory_access[0]->inventory_advanced == "yes")
              {
              $product_items          = $this->common->purchase_order_items_product_inventory_list_field($id);
              $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              {
              } */
            $product_items = $this->common->purchase_order_items_product_list_field($id);
            $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);

            $service_items = $this->common->purchase_order_items_service_list_field($id);
            $purchase_order_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items'] = array_merge($purchase_order_product_items, $purchase_order_service_items);

            if ($purchase_order_product_items && $purchase_order_service_items) {
                $nature_of_supply = "Product/Service";
            } elseif ($purchase_order_product_items) {
                $nature_of_supply = "Product";
            } elseif ($purchase_order_service_items) {
                $nature_of_supply = "Service";
            }

            $data['nature_of_supply'] = $nature_of_supply;
            $igst = 0;
            $cgst = 0;
            $sgst = 0;
            $dpcount = 0;
            $dtcount = 0;
            $description = 0;

            foreach ($data['items'] as $value) {
                $igst = bcadd($igst, $value->purchase_order_item_igst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $cgst = bcadd($cgst, $value->purchase_order_item_cgst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $sgst = bcadd($sgst, $value->purchase_order_item_sgst_amount, $section_modules['access_common_settings'][0]->amount_precision);

                if ($value->purchase_order_item_description != "" && $value->purchase_order_item_description != null) {
                    $dpcount++;
                }

                if ($value->purchase_order_item_discount_amount != "" && $value->purchase_order_item_discount_amount != null && $value->purchase_order_item_discount_amount != 0) {
                    $dtcount++;
                }

                if ($value->purchase_order_item_description != "")
                    $description++;
            }

            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
            $discountExist = 0;
            $tdsExist = 0;
            $descriptionExist = 0;

            if ($data['data'][0]->purchase_order_tax_amount > 0 && $data['data'][0]->purchase_order_igst_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {

                /* igst tax slab */
                $igstExist = 1;
            } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount > 0 || $data['data'][0]->purchase_order_sgst_amount > 0) && $data['data'][0]->purchase_order_igst_amount == 0) {
                /* cgst tax slab */
                $cgstExist = 1;
                $sgstExist = 1;
            } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
                /* Single tax */
                $taxExist = 1;
            } elseif ($data['data'][0]->purchase_order_tax_amount == 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
                /* No tax */
                $igstExist = 0;
                $cgstExist = 0;
                $sgstExist = 0;
                $taxExist = 0;
            }

            if ($data['data'][0]->purchase_order_tds_amount > 0 || $data['data'][0]->purchase_order_tcs_amount > 0) {
                /* Discount */
                $tdsExist = 1;
            }

            if ($data['data'][0]->purchase_order_discount_amount > 0) {
                /* Discount */
                $discountExist = 1;
            }

            if ($description > 0) {
                /* Discount */
                $descriptionExist = 1;
            }

            $cess_exist = 0;
            if ($data['data'][0]->purchase_order_tax_cess_amount > 0) {
                $cess_exist = 1;
            }
            $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_order_billing_state_id);

            $data['igst_exist'] = $igstExist;
            $data['cgst_exist'] = $cgstExist;
            $data['sgst_exist'] = $sgstExist;
            $data['tax_exist'] = $taxExist;
            $data['discount_exist'] = $discountExist;
            $data['description_exist'] = $descriptionExist;
            $data['tds_exist'] = $tdsExist;
            $data['is_utgst'] = $is_utgst;
            $data['cess_exist'] = $cess_exist;

            $data['igst_tax'] = $igst;
            $data['cgst_tax'] = $cgst;
            $data['sgst_tax'] = $sgst;
            $data['dpcount'] = $dpcount;
            $data['dtcount'] = $dtcount;
            $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1'] = $note_data['note1'];
            $data['template1'] = $note_data['template1'];
            $data['note2'] = $note_data['note2'];
            $data['template2'] = $note_data['template2'];
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency[0]->currency_code;
            $data['currency_symbol'] = $currency[0]->currency_symbol;
            $data['currency_text'] = $currency[0]->currency_text;
            $data['currency_symbol_pdf'] = $currency[0]->currency_symbol_pdf;
            $html = $this->load->view('purchase_order/pdf', $data, true);

            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_order_invoice_number);
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

            /* include APPPATH . 'third_party/mpdf60/mpdf.php';
              $mpdf                           = new mPDF();
              $mpdf->allow_charset_conversion = true;
              $mpdf->charset_in               = 'UTF-8';
              $file_path                      = "././pdf_form/";
              $mpdf->WriteHTML($html);
              $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_order_invoice_number);
              $mpdf->Output($file_path . $file_name . '.pdf', 'F');
              $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
              $data['pdf_file_name'] = $file_name . '.pdf'; */
            $purchase_order_data = $this->common->purchase_order_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_order_data['string'], $purchase_order_data['table'], $purchase_order_data['where'], $purchase_order_data['join']);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id' => $purchase_order_module_id,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('purchase_order/email', $data);
        } else {
            $this->load->view('purchase_order', $data);
        }
    }

    public function convert_currency() {
        $id = $this->input->post('convert_currency_id');
        $id = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');

        $data = array(
            'currency_converted_rate' => $new_converted_rate,
            'converted_grand_total' => $this->input->post('converted_grand_total'));
        $this->general_model->updateData('purchase_order', $data, array(
            'purchase_order_id' => $id));

        //update converted voucher amount in account purchase_order voucher table

        $purchase_order_voucher_data = array(
            'converted_receipt_amount' => $this->input->post('converted_grand_total'));
        $this->general_model->updateData('purchase_order_voucher', $purchase_order_voucher_data, array(
            'reference_id' => $id,
            'delete_status' => 0,
            'reference_type' => 'purchase_order'));
        $purchase_order_voucher = $this->general_model->getRecords('purchase_order_voucher_id', 'purchase_order_voucher', array(
            'reference_id' => $id,
            'delete_status' => 0,
            'reference_type' => 'purchase_order'));
        $accounts_purchase_order_voucher = $this->general_model->getRecords('*', 'accounts_purchase_order_voucher', array(
            'purchase_order_voucher_id' => $purchase_order_voucher[0]->purchase_order_voucher_id,
            'delete_status' => 0));
        foreach ($accounts_purchase_order_voucher as $key1 => $value1) {
            $new_converted_voucher_amount = bcmul($accounts_purchase_order_voucher[$key1]->voucher_amount, $new_converted_rate, $section_modules['access_common_settings'][0]->amount_precision);
            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount);
            $where = array(
                'accounts_purchase_order_id' => $accounts_purchase_order_voucher[$key1]->accounts_purchase_order_id);
            $voucher_table = "accounts_purchase_order_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }
        redirect('purchase_order', 'refresh');
    }
    public function email_popup($id) {
        $id = $this->encryption_url->decode($id);
        $purchase_order_module_id = $this->config->item('purchase_order_module');
        $data['module_id'] = $purchase_order_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_order_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        foreach ($modules['modules'] as $key => $value) {
            $data['active_modules'][$key] = $value->module_id;

            if ($value->view_privilege == "yes") {
                $data['active_view'][$key] = $value->module_id;
            }

            if ($value->edit_privilege == "yes") {
                $data['active_edit'][$key] = $value->module_id;
            }

            if ($value->delete_privilege == "yes") {
                $data['active_delete'][$key] = $value->module_id;
            }

            if ($value->add_privilege == "yes") {
                $data['active_add'][$key] = $value->module_id;
            }
        }

        $email_sub_module = 0;

        if (in_array($data['email_sub_module_id'], $data['access_sub_modules'])) {
            $email_sub_module = 1;
        }

        if ($email_sub_module == 1) {
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
            $purchase_order_data = $this->common->purchase_order_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_order_data['string'], $purchase_order_data['table'], $purchase_order_data['where'], $purchase_order_data['join']);
            $country_data = $this->common->country_field($data['data'][0]->purchase_order_billing_country_id);
            $data['data_country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data = $this->common->state_field($data['data'][0]->purchase_order_billing_country_id, $data['data'][0]->purchase_order_billing_state_id);
            $data['data_state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));

            /* if ($inventory_access[0]->inventory_advanced == "yes")
              {
              $product_items          = $this->common->purchase_order_items_product_inventory_list_field($id);
              $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              {
              } */
            $product_items = $this->common->purchase_order_items_product_list_field($id);
            $purchase_order_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);

            $service_items = $this->common->purchase_order_items_service_list_field($id);
            $purchase_order_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items'] = array_merge($purchase_order_product_items, $purchase_order_service_items);

            if ($purchase_order_product_items && $purchase_order_service_items) {
                $nature_of_supply = "Product/Service";
            } elseif ($purchase_order_product_items) {
                $nature_of_supply = "Product";
            } elseif ($purchase_order_service_items) {
                $nature_of_supply = "Service";
            }

            $data['nature_of_supply'] = $nature_of_supply;
            $igst = 0;
            $cgst = 0;
            $sgst = 0;
            $dpcount = 0;
            $dtcount = 0;
            $description = 0;

            foreach ($data['items'] as $value) {
                $igst = bcadd($igst, $value->purchase_order_item_igst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $cgst = bcadd($cgst, $value->purchase_order_item_cgst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $sgst = bcadd($sgst, $value->purchase_order_item_sgst_amount, $section_modules['access_common_settings'][0]->amount_precision);

                if ($value->purchase_order_item_description != "" && $value->purchase_order_item_description != null) {
                    $dpcount++;
                }

                if ($value->purchase_order_item_discount_amount != "" && $value->purchase_order_item_discount_amount != null && $value->purchase_order_item_discount_amount != 0) {
                    $dtcount++;
                }

                if ($value->purchase_order_item_description != "")
                    $description++;
            }

            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
            $discountExist = 0;
            $tdsExist = 0;
            $descriptionExist = 0;

            if ($data['data'][0]->purchase_order_tax_amount > 0 && $data['data'][0]->purchase_order_igst_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {

                /* igst tax slab */
                $igstExist = 1;
            } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_cgst_amount > 0 || $data['data'][0]->purchase_order_sgst_amount > 0) && $data['data'][0]->purchase_order_igst_amount == 0) {
                /* cgst tax slab */
                $cgstExist = 1;
                $sgstExist = 1;
            } elseif ($data['data'][0]->purchase_order_tax_amount > 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
                /* Single tax */
                $taxExist = 1;
            } elseif ($data['data'][0]->purchase_order_tax_amount == 0 && ($data['data'][0]->purchase_order_igst_amount == 0 && $data['data'][0]->purchase_order_cgst_amount == 0 && $data['data'][0]->purchase_order_sgst_amount == 0)) {
                /* No tax */
                $igstExist = 0;
                $cgstExist = 0;
                $sgstExist = 0;
                $taxExist = 0;
            }

            if ($data['data'][0]->purchase_order_tds_amount > 0 || $data['data'][0]->purchase_order_tcs_amount > 0) {
                /* Discount */
                $tdsExist = 1;
            }

            if ($data['data'][0]->purchase_order_discount_amount > 0) {
                /* Discount */
                $discountExist = 1;
            }

            if ($description > 0) {
                /* Discount */
                $descriptionExist = 1;
            }

            $cess_exist = 0;
            if ($data['data'][0]->purchase_order_tax_cess_amount > 0) {
                $cess_exist = 1;
            }
            $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_order_billing_state_id);

            $data['igst_exist'] = $igstExist;
            $data['cgst_exist'] = $cgstExist;
            $data['sgst_exist'] = $sgstExist;
            $data['tax_exist'] = $taxExist;
            $data['discount_exist'] = $discountExist;
            $data['description_exist'] = $descriptionExist;
            $data['tds_exist'] = $tdsExist;
            $data['is_utgst'] = $is_utgst;
            $data['cess_exist'] = $cess_exist;

            $data['igst_tax'] = $igst;
            $data['cgst_tax'] = $cgst;
            $data['sgst_tax'] = $sgst;
            $data['dpcount'] = $dpcount;
            $data['dtcount'] = $dtcount;
            $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1'] = $note_data['note1'];
            $data['template1'] = $note_data['template1'];
            $data['note2'] = $note_data['note2'];
            $data['template2'] = $note_data['template2'];
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code'] = $currency[0]->currency_code;
            $data['currency_symbol'] = $currency[0]->currency_symbol;
            $data['currency_text'] = $currency[0]->currency_text;
            $data['currency_symbol_pdf'] = $currency[0]->currency_symbol_pdf;
            $data['data'][0]->unit = $currency[0]->unit;
            $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
            $html = $this->load->view('purchase_order/pdf', $data, true);

            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_order_invoice_number);
            $dompdf = new Dompdf\Dompdf();

            $paper_size = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name'] = $file_name . '.pdf';
            $purchase_order_data = $this->common->purchase_order_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_order_data['string'], $purchase_order_data['table'], $purchase_order_data['where'], $purchase_order_data['join']);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id' => $purchase_order_module_id,
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
