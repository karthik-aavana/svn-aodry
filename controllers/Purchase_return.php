<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_return extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
        $sales_module_id = $this->config->item('purchase_return_module');
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /* Modules Present */
        $data['sales_module_id'] = $sales_module_id;
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id'] = $this->config->item('email_module');
        $data['recurrence_module_id'] = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id'] = $this->config->item('recurrence_sub_module');
        if (!empty($this->input->post())) {
            // echo "test";die;
            $columns = array(
                0 => 'action',
                1 => 'voucher_no',
                2 => 'purchase_voucher_no',
                3 => 'supplier',
                4 => 'grand_total');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->purchase_return_list_field();
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
                    $purchase_return_id = $this->encryption_url->encode($post->purchase_return_id);
                    $purchase_id = $this->encryption_url->encode($post->purchase_id);
                    // $nestedData['date']                      = $post->purchase_return_date;
                    $nestedData['voucher_no'] = ' <a href="' . base_url('purchase_return/view/') . $purchase_return_id . '">' . $post->purchase_return_invoice_number . '</a>';
                    $nestedData['purchase_voucher_no'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                    $nestedData['supplier'] = $post->supplier_name ? $post->supplier_name : '';
                    /*$nestedData['supplier'] = $post->supplier_name . ' (<a href="' . base_url('purchase_return/view/') . $purchase_return_id . '">' . $post->purchase_return_invoice_number . '</a>) ';*/
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $this->precise_amount($post->purchase_return_grand_total, 2);
                    //  $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    // $nestedData['added_user']                = $post->first_name . ' ' . $post->last_name;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    $cols .= '<span><a href="' . base_url('purchase_return/view/') . $purchase_return_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="View Purchase Return">
                                    <i class="fa fa-eye"></i>
                            </a></span>';


                    if (in_array($sales_module_id, $data['active_edit'])) {

                        $cols .= '<span><a href="' . base_url('purchase_return/edit/') . $purchase_return_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Edit Purchase Return">
                                    <i class="fa fa-pencil"></i>
                            </a></span>';
                    }

                    // $cols .= '<li><a href="' . base_url('purchase_return/pdf/') . $purchase_return_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i> Download PDF</a></li>';

                    $cols .= '<span><a href="' . base_url('purchase_return/pdf/') . $purchase_return_id . '" target="_blank" class="btn btn-app pdf_button" data-name="regular" data-toggle="tooltip" data-placement="bottom" title="Download PDF">
                                    <i class="fa fa-file-pdf-o"></i>
                            </a></span>';

                    /* $email_sub_module = 0;
                      if (in_array($data['receipt_voucher_module_id'] , $data['active_add'])){
                      // foreach ($data['access_sub_modules'] as $key => $value)
                      // {
                      // if ($email_sub_module_id == $value->sub_module_id)
                      // {
                      // $email_sub_module = 1;
                      // }
                      // }
                      }

                      if ($email_sub_module == 1){
                      $cols .= '<li><a href="' . base_url('purchase_return/email/') . $purchase_return_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Purchase Return</a>                                </li>';
                      } */
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail" >
                        <a class="btn btn-app composeMail" data-id="' . $purchase_return_id . '" data-name="regular"  href="javascript:void(0);" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="Email Purchase Return"><i class="fa fa-envelope-o"></i></a></span>';



                    /* if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY')){
                      $cols .= '<li><a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $purchase_return_id . '" data-path="purchase_return/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->purchase_return_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a></li>';
                      } */
                    if (in_array($sales_module_id, $data['active_delete'])) {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a href="#" class="btn btn-app delete_button"  data-id="' . $purchase_return_id . '" data-path="purchase_return/delete" href="#" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" data-toggle="tooltip" data-placement="bottom" title="Delete Purchase Return"><i class="fa fa-trash-o"></i></a></span>';
                    }
                    $cols .= '</div>';
                    $cols .= '</div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal" value="' . $post->purchase_return_id . '">';

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
            $this->load->view('purchase_return/list', $data);
        }
    }

    public function add() {
        $data = $this->get_default_country_state();
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
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
        $data['supplier'] = $this->supplier_call();
        $data['discount'] = $this->discount_call();
        $data['currency'] = $this->currency_call();
        $data['product_category'] = $this->product_category_call();
        $data['service_category'] = $this->service_category_call();
        $data['tax'] = $this->tax_call();

        $data['uqc'] = $this->uqc_call();
        $data['sac'] = $this->sac_call();
        $data['chapter'] = $this->chapter_call();
        $data['hsn'] = $this->hsn_call();
        $access_settings = $data['access_settings'];
        $primary_id = "purchase_return_id";
        $table_name = "purchase_return";
        $date_field_name = "purchase_return_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('purchase_return/add', $data);
    }

    public function get_purchase_invoice_number() {
        $supplier_id = $this->input->post('supplier_id');

        $invoice_data = $this->common->get_supplier_invoice_number_field($supplier_id);
        $data = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);
        echo json_encode($data);
    }

    public function add_purchase_return() {
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /*  $data['access_modules']            = $section_modules['modules'];
          $data['access_sub_modules']        = $section_modules['sub_modules'];
          $data['access_module_privilege']   = $section_modules['module_privilege'];
          $data['access_user_privilege']     = $section_modules['user_privilege'];
          $data['access_settings']           = $section_modules['settings'];
          $data['access_common_settings']    = $section_modules['common_settings']; */
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
        $purchase_return_data = array(
            "purchase_return_date" => date('Y-m-d', strtotime($this->input->post('invoice_date'))),
            "purchase_return_invoice_number" => $this->input->post('invoice_number'),
            "purchase_id" => $this->input->post('purchase_invoice_number'),
            "purchase_return_sub_total" => $this->input->post('total_sub_total'),
            "purchase_return_grand_total" => $this->input->post('total_grand_total'),
            "purchase_return_discount_value" => $this->input->post('total_discount_amount'),
            "purchase_return_tax_amount" => $this->input->post('total_tax_amount'),
            "purchase_return_taxable_value" => $this->input->post('total_taxable_amount'),
            "purchase_return_igst_amount" => $this->input->post('total_igst_amount'),
            "purchase_return_cgst_amount" => $this->input->post('total_cgst_amount'),
            "purchase_return_sgst_amount" => $this->input->post('total_sgst_amount'),
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "purchase_return_party_id" => $this->input->post('supplier'),
            "purchase_return_party_type" => "supplier",
            "purchase_return_type_of_supply" => $this->input->post('type_of_supply'),
            "purchase_return_gst_payable" => $this->input->post('gstPayable'),
            "purchase_return_billing_country_id" => $this->input->post('billing_country'),
            "purchase_return_billing_state_id" => $this->input->post('billing_state'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
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
            "shipping_address" => $this->input->post('shipping_address'),
            "warranty" => $this->input->post('warranty'),
            "payment_mode" => $this->input->post('payment_mode'),
            "freight_charge_amount" => $this->input->post('freight_charge_amount'),
            "freight_charge_tax" => $this->input->post('freight_charge_tax'),
            "freight_charge_tax_amount" => $this->input->post('freight_charge_tax_amount'),
            "total_freight_charge" => $this->input->post('total_freight_charge'),
            "insurance_charge_amount" => $this->input->post('insurance_charge_amount'),
            "insurance_charge_tax" => $this->input->post('insurance_charge_tax'),
            "insurance_charge_tax_amount" => $this->input->post('insurance_charge_tax_amount'),
            "total_insurance_charge" => $this->input->post('total_insurance_charge'),
            "packing_charge_amount" => $this->input->post('packing_charge_amount'),
            "packing_charge_tax" => $this->input->post('packing_charge_tax'),
            "packing_charge_tax_amount" => $this->input->post('packing_charge_tax_amount'),
            "total_packing_charge" => $this->input->post('total_packing_charge'),
            "incidental_charge_amount" => $this->input->post('incidental_charge_amount'),
            "incidental_charge_tax" => $this->input->post('incidental_charge_tax'),
            "incidental_charge_tax_amount" => $this->input->post('incidental_charge_tax_amount'),
            "total_incidental_charge" => $this->input->post('total_incidental_charge'),
            "inclusion_other_charge_amount" => $this->input->post('inclusion_other_charge_amount'),
            "inclusion_other_charge_tax" => $this->input->post('inclusion_other_charge_tax'),
            "inclusion_other_charge_tax_amount" => $this->input->post('inclusion_other_charge_tax_amount'),
            "total_inclusion_other_charge" => $this->input->post('total_other_inclusive_charge'),
            "exclusion_other_charge_amount" => $this->input->post('exclusion_other_charge_amount'),
            "exclusion_other_charge_tax" => $this->input->post('exclusion_other_charge_tax'),
            "exclusion_other_charge_tax_amount" => $this->input->post('exclusion_other_charge_tax_amount'),
            "total_exclusion_other_charge" => $this->input->post('total_other_exclusive_charge'),
            "total_other_amount" => $this->input->post('total_other_amount'),
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2'),
             "freight_charge_tax_id" => $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id'): 0,
         "insurance_charge_tax_id" => $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0,
         "packing_charge_tax_id" => $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0,
         "incidental_charge_tax_id" => $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0,
         "inclusion_other_charge_tax_id" => $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0,
         "exclusion_other_charge_tax_id" => $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0,
            "purchase_return_cess_amount" => $this->input->post('total_tax_cess_amount'));
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $purchase_return_data['currency_converted_amount'] = $this->input->post('total_grand_total');
        } else {
            $purchase_return_data['currency_converted_amount'] = "0.00";
        }
        $data_main = array_map('trim', $purchase_return_data);
        $purchase_item_data = $this->input->post('table_data');
        $js_data1 = json_decode($purchase_item_data);
        $js_data = array();
        $i = 0;
        foreach ($js_data1 as $key => $value) {
            if ($value && $value->checkbox_value == "on") {
                $temp = new stdClass();
                foreach ($value as $k => $v) {
                    $temp->$k = $v;
                    $js_data[$i] = $temp;
                } $i++;
            }
        }


        if ($purchase_return_id = $this->general_model->insertData("purchase_return", $data_main)) {
            $old_amount = $this->general_model->getRecords("purchase_return_amount", "purchase", array(
                'purchase_id' => $data_main['purchase_id']));
            $new_amount = bcadd($old_amount[0]->purchase_return_amount, $data_main["purchase_return_grand_total"]);
            $this->general_model->updateData("purchase", array(
                'purchase_return_amount' => $new_amount), array(
                'purchase_id' => $data_main['purchase_id']));
            $successMsg = 'Purchase Return Added successfully';
            $this->session->set_flashdata('purchase_return_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $purchase_return_id,
                'table_name' => 'purchase_return',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Purchase Return Inserted');
            $this->general_model->insertData("log", $log_data);
            foreach ($js_data as $key => $value) {
                if ($value != null && $value != '') {
                    $product_id = $value->item_id;
                    $quantity = $value->item_quantity;
                    $item_data = array(
                        "item_id" => $value->item_id,
                        "item_type" => $value->item_type,
                        "purchase_return_item_quantity" => $value->item_quantity,
                        "purchase_return_item_unit_price" => $value->item_price,
                        "purchase_return_item_sub_total" => $value->item_sub_total,
                        "purchase_return_item_taxable_value" => $value->item_taxable_value,
                        "purchase_return_item_discount_amount" => $value->item_discount_amount,
                        "purchase_return_item_discount_id" => $value->item_discount,
                        "purchase_return_item_grand_total" => $value->item_grand_total,
                        "purchase_return_item_igst_percentage" => $value->item_igst,
                        "purchase_return_item_igst_amount" => $value->item_igst_amount,
                        "purchase_return_item_cgst_percentage" => $value->item_cgst,
                        "purchase_return_item_cgst_amount" => $value->item_cgst_amount,
                        "purchase_return_item_sgst_percentage" => $value->item_sgst,
                        "purchase_return_item_sgst_amount" => $value->item_sgst_amount,
                        "purchase_return_item_tax_percentage" => $value->item_tax_percentage,
                        "purchase_return_item_tax_amount" => $value->item_tax_amount,
                        "purchase_return_item_description" => $value->item_description,
                        "purchase_item_id" => $value->purchase_item_id,
                        "purchase_return_id" => $purchase_return_id,
                        "purchase_return_item_tax_id" => $value->item_tax_id,
                        "purchase_return_item_cess_id" => $value->item_tax_cess_id,
                        "purchase_return_item_cess_percentage" => $value->item_tax_cess_percentage,
                        "purchase_return_item_cess_amount" => $value->item_tax_cess_amount);
                    $this->general_model->insertData("purchase_return_item", $item_data);
                }
            }
            redirect('purchase_return', 'refresh');
        } else {
            $errorMsg = 'Purchase Return Add Unsuccessful';
            $this->session->set_flashdata('purchase_return_error',$errorMsg);
            redirect('purchase_return', 'refresh');
        }
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        /* $data['access_modules']          = $section_modules['modules'];
          $data['access_sub_modules']      = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege']   = $section_modules['user_privilege'];
          $data['access_settings']         = $section_modules['settings'];
          $data['access_common_settings']  = $section_modules['common_settings'];
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
          } */
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
        $table = "purchase_return";
        $where = array("purchase_return_id" => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));


        /*  if ($inventory_access[0]->inventory_advanced == "yes")
          {
          $product_items          = $this->common->purchase_return_items_product_inventory_list_field($id);
          $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
          }
          else
          { */
        $product_items = $this->common->purchase_return_items_product_list_field($id);
        $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        //  }

        $service_items = $this->common->purchase_return_items_service_list_field($id);
        $purchase_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_product_items, $purchase_service_items);
        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $cessExist = 0;

        if ($data['data'][0]->purchase_return_tax_amount > 0 && $data['data'][0]->purchase_return_igst_amount > 0 && ($data['data'][0]->purchase_return_sgst_amount == 0 && $data['data'][0]->purchase_return_cgst_amount == 0)) {
            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_return_tax_amount > 0 && ($data['data'][0]->purchase_return_cgst_amount > 0 || $data['data'][0]->purchase_return_sgst_amount > 0) && $data['data'][0]->purchase_return_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_return_tax_amount > 0 && ($data['data'][0]->purchase_return_igst_amount == 0 && $data['data'][0]->purchase_return_cgst_amount == 0 && $data['data'][0]->purchase_return_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_return_tax_amount == 0 && ($data['data'][0]->purchase_return_igst_amount == 0 && $data['data'][0]->purchase_return_cgst_amount == 0 && $data['data'][0]->purchase_return_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_return_cess_amount > 0) {
            /* No tax */
            $cessExist = 1;
        }





        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_return_billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['tax_exist'] = $taxExist;
        $data['cess_exist'] = $cessExist;
        $data['is_utgst'] = $is_utgst;
        $this->load->view('purchase_return/edit', $data);
    }

    public function edit_purchase_return() {
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /* $data['access_modules']            = $section_modules['modules'];
          $data['access_sub_modules']        = $section_modules['sub_modules'];
          $data['access_module_privilege']   = $section_modules['module_privilege'];
          $data['access_user_privilege']     = $section_modules['user_privilege'];
          $data['access_settings']           = $section_modules['settings'];
          $data['access_common_settings']    = $section_modules['common_settings']; */
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
        $access_settings = $section_modules['access_settings'];
        if ($access_settings[0]->invoice_creation == "automatic") {
            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old')) {
                $primary_id = "purchase_return_id";
                $table_name = "purchase_return";
                $date_field_name = "purchase_return_date";
                $current_date = date('Y-m-d', strtotime($this->input->post('invoice_date')));
                $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else {
                $invoice_number = $this->input->post('invoice_number');
            }
        } else {
            $invoice_number = $this->input->post('invoice_number');
        } $purchase_return_data = array(
            "purchase_return_date" => date('Y-m-d', strtotime($this->input->post('invoice_date'))),
            "purchase_return_invoice_number" => $invoice_number,
            "purchase_id" => $this->input->post('purchase_invoice_number'),
            "purchase_return_sub_total" => $this->input->post('total_sub_total'),
            "purchase_return_grand_total" => $this->input->post('total_grand_total'),
            "purchase_return_discount_value" => $this->input->post('total_discount_amount'),
            "purchase_return_tax_amount" => $this->input->post('total_tax_amount'),
            "purchase_return_taxable_value" => $this->input->post('total_taxable_amount'),
            "purchase_return_igst_amount" => $this->input->post('total_igst_amount'),
            "purchase_return_cgst_amount" => $this->input->post('total_cgst_amount'),
            "purchase_return_sgst_amount" => $this->input->post('total_sgst_amount'),
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "purchase_return_party_id" => $this->input->post('supplier'),
            "purchase_return_party_type" => "supplier",
            "purchase_return_type_of_supply" => $this->input->post('type_of_supply'),
            "purchase_return_gst_payable" => $this->input->post('gstPayable'),
            "purchase_return_billing_country_id" => $this->input->post('billing_country'),
            "purchase_return_billing_state_id" => $this->input->post('billing_state'),
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
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
            "shipping_address" => $this->input->post('shipping_address'),
            "warranty" => $this->input->post('warranty'),
            "payment_mode" => $this->input->post('payment_mode'),
            "freight_charge_amount" => $this->input->post('freight_charge_amount'),
            "freight_charge_tax" => $this->input->post('freight_charge_tax'),
            "freight_charge_tax_amount" => $this->input->post('freight_charge_tax_amount'),
            "total_freight_charge" => $this->input->post('total_freight_charge'),
            "insurance_charge_amount" => $this->input->post('insurance_charge_amount'),
            "insurance_charge_tax" => $this->input->post('insurance_charge_tax'),
            "insurance_charge_tax_amount" => $this->input->post('insurance_charge_tax_amount'),
            "total_insurance_charge" => $this->input->post('total_insurance_charge'),
            "packing_charge_amount" => $this->input->post('packing_charge_amount'),
            "packing_charge_tax" => $this->input->post('packing_charge_tax'),
            "packing_charge_tax_amount" => $this->input->post('packing_charge_tax_amount'),
            "total_packing_charge" => $this->input->post('total_packing_charge'),
            "incidental_charge_amount" => $this->input->post('incidental_charge_amount'),
            "incidental_charge_tax" => $this->input->post('incidental_charge_tax'),
            "incidental_charge_tax_amount" => $this->input->post('incidental_charge_tax_amount'),
            "total_incidental_charge" => $this->input->post('total_incidental_charge'),
            "inclusion_other_charge_amount" => $this->input->post('inclusion_other_charge_amount'),
            "inclusion_other_charge_tax" => $this->input->post('inclusion_other_charge_tax'),
            "inclusion_other_charge_tax_amount" => $this->input->post('inclusion_other_charge_tax_amount'),
            "total_inclusion_other_charge" => $this->input->post('total_other_inclusive_charge'),
            "exclusion_other_charge_amount" => $this->input->post('exclusion_other_charge_amount'),
            "exclusion_other_charge_tax" => $this->input->post('exclusion_other_charge_tax'),
            "exclusion_other_charge_tax_amount" => $this->input->post('exclusion_other_charge_tax_amount'),
            "total_exclusion_other_charge" => $this->input->post('total_other_exclusive_charge'),
            "total_other_amount" => $this->input->post('total_other_amount'),
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2'),
             "freight_charge_tax_id" => $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id'): 0,
         "insurance_charge_tax_id" => $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0,
         "packing_charge_tax_id" => $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0,
         "incidental_charge_tax_id" => $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0,
         "inclusion_other_charge_tax_id" => $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0,
         "exclusion_other_charge_tax_id" => $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0,
            "purchase_return_cess_amount" => $this->input->post('total_tax_cess_amount'));
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $purchase_return_data['currency_converted_amount'] = $this->input->post('total_grand_total');
        } else {
            $purchase_return_data['currency_converted_amount'] = "0.00";
        } $data_main = array_map('trim', $purchase_return_data);
        $purchase_return_id = $this->input->post('purchase_return_id');
        $purchase_item_data = $this->input->post('table_data');
        $js_data = json_decode($purchase_item_data);
        $old_purchase_amount = $this->general_model->getRecords("purchase_return_amount", "purchase", array(
            'purchase_id' => $data_main['purchase_id']));
        $old_purchase_return_amount = $this->general_model->getRecords("purchase_return_grand_total", "purchase_return", array(
            'purchase_return_id' => $purchase_return_id));
        $new_purchase_return_amount = bcsub($old_purchase_amount[0]->purchase_return_amount, $old_purchase_return_amount[0]->purchase_return_grand_total);
        $this->general_model->updateData("purchase", array(
            'purchase_return_amount' => $new_purchase_return_amount), array(
            'purchase_id' => $data_main['purchase_id']));
        if ($this->general_model->updateData("purchase_return", $data_main, array(
                    'purchase_return_id' => $purchase_return_id))) {
            $old_amount = $this->general_model->getRecords("purchase_return_amount", "purchase", array(
                'purchase_id' => $data_main['purchase_id']));
            $new_amount = bcadd($old_amount[0]->purchase_return_amount, $data_main["purchase_return_grand_total"]);
            $this->general_model->updateData("purchase", array(
                'purchase_return_amount' => $new_amount), array(
                'purchase_id' => $data_main['purchase_id']));
            $successMsg = 'Purchase Return Updated successfully';
            $this->session->set_flashdata('purchase_return_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $purchase_return_id,
                'table_name' => 'purchase_return',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Purchase Return Updated');
            $this->general_model->insertData("log", $log_data);
            foreach ($js_data as $key => $value) {
                if ($value == null) {
                    
                } else {
                    $product_id = $value->item_id;
                    $quantity = $value->item_quantity;
                    $item_data[] = array(
                        "item_id" => $value->item_id,
                        "item_type" => $value->item_type,
                        "purchase_return_item_quantity" => $value->item_quantity,
                        "purchase_return_item_unit_price" => $value->item_price,
                        "purchase_return_item_sub_total" => $value->item_sub_total,
                        "purchase_return_item_taxable_value" => $value->item_taxable_value,
                        "purchase_return_item_discount_amount" => $value->item_discount_amount,
                        "purchase_return_item_discount_id" => $value->item_discount,
                        "purchase_return_item_grand_total" => $value->item_grand_total,
                        "purchase_return_item_igst_percentage" => $value->item_igst,
                        "purchase_return_item_igst_amount" => $value->item_igst_amount,
                        "purchase_return_item_cgst_percentage" => $value->item_cgst,
                        "purchase_return_item_cgst_amount" => $value->item_cgst_amount,
                        "purchase_return_item_sgst_percentage" => $value->item_sgst,
                        "purchase_return_item_sgst_amount" => $value->item_sgst_amount,
                        "purchase_return_item_tax_percentage" => $value->item_tax_percentage,
                        "purchase_return_item_tax_amount" => $value->item_tax_amount,
                        "purchase_return_item_description" => $value->item_description,
                        "purchase_item_id" => $value->purchase_item_id,
                        "purchase_return_id" => $purchase_return_id,
                        "purchase_return_item_tax_id" => $value->item_tax_id,
                        "purchase_return_item_cess_id" => $value->item_tax_cess_id,
                        "purchase_return_item_cess_percentage" => $value->item_tax_cess_percentage,
                        "purchase_return_item_cess_amount" => $value->item_tax_cess_amount);
                }
            }
            if ($this->purchase_model->updatePurchaseReturnItem($item_data, $purchase_return_id)) {
                if ($value->item_type == 'product') {
                    
                }
            } else {
                redirect('purchase_return', 'refresh');
            }
            redirect('purchase_return', 'refresh');
        } else {
            $errorMsg = 'Purchase Return Update Unsuccessful';
            $this->session->set_flashdata('purchase_return_error',$errorMsg);
            redirect('purchase_return', 'refresh');
        } redirect('purchase_return', 'refresh');
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data['access_modules'] = $section_modules['modules'];
        $data['access_sub_modules'] = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege'] = $section_modules['user_privilege'];
        $data['access_settings'] = $section_modules['settings'];
        $data['access_common_settings'] = $section_modules['common_settings'];
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $supplier_module_id = $this->config->item('supplier_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'supplier_module_id' => $supplier_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        $purchase_return_data = $this->general_model->getRecords('*', 'purchase_return', array(
            'purchase_return_id' => $id));
        $purchase_id = $purchase_return_data[0]->purchase_id;
        $old_purchase_amount = $this->general_model->getRecords("purchase_return_amount", "purchase", array(
            'purchase_id' => $purchase_id));
        $old_purchase_return_amount = $this->general_model->getRecords("purchase_return_grand_total", "purchase_return", array(
            'purchase_return_id' => $id));
        $new_purchase_return_amount = bcsub($old_purchase_amount[0]->purchase_return_amount, $old_purchase_return_amount[0]->purchase_return_grand_total);
        $this->general_model->updateData("purchase", array(
            'purchase_return_amount' => $new_purchase_return_amount), array(
            'purchase_id' => $purchase_id));
        if ($this->purchase_model->deletePurchaseReturn($id)) {
            $successMsg = 'Purchase Return Deleted successfully';
            $this->session->set_flashdata('purchase_return_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'purchase_return',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Purchase Return Deleted');
            $this->general_model->insertData('log', $log_data);
            redirect('purchase_return');
        } else {
            $errorMsg = 'Purchase Return Delete Unsuccessful';
            $this->session->set_flashdata('purchase_return_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Category can not be Deleted.');
            redirect("purchase_return", 'refresh');
        }
    }

    public function view($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /* $data['access_modules']          = $section_modules['modules'];
          $data['access_sub_modules']      = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege']   = $section_modules['user_privilege'];
          $data['access_settings']         = $section_modules['settings'];
          $data['access_common_settings']  = $section_modules['common_settings']; */
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $supplier_module_id = $this->config->item('supplier_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'supplier_module_id' => $supplier_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        /* foreach ($modules['modules'] as $key => $value)
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
          } */
        $purchase_return_data = $this->common->purchase_return_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($purchase_return_data['string'], $purchase_return_data['table'], $purchase_return_data['where'], $purchase_return_data['join']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));



        $product_items = $this->common->purchase_return_items_product_list_field($id);
        $purchase_return_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


        $service_items = $this->common->purchase_return_items_service_list_field($id);
        $purchase_return_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_return_product_items, $purchase_return_service_items);
        $igst = 0;
        $cgst = 0;
        $cess = 0;
        $sgst = 0;
        $dpcount = 0;
        $dtcount = 0;
        foreach ($data['items'] as $value) {
            $igst = bcadd($igst, $value->purchase_return_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->purchase_return_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->purchase_return_item_sgst_amount, 2);
            $cess = bcadd($cess, $value->purchase_return_item_cess_amount, 2);

            if ($value->purchase_return_item_description != "" && $value->purchase_return_item_description != null) {
                $dpcount++;
            } if ($value->purchase_return_item_discount_amount != "" && $value->purchase_return_item_discount_amount != null) {
                $dtcount++;
            }
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_return_billing_state_id);
        $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['cess_tax'] = $cess;
        $data['dpcount'] = $dpcount;
        $data['dtcount'] = $dtcount;
        $data['is_utgst'] = $is_utgst;
        $data['currency'] = $this->currency_call();
        /* delivery challan request */
        $data['is_only_view'] = '0';
        if ($this->input->post('is_only_view')) {
            $data['is_only_view'] = '1';
            $data['data'][0]->purchase_return_invoice_number = $this->input->post('invoice_number');
            $data['data'][0]->purchase_return_date = date('Y-m-d', strtotime($this->input->post('invoice_date')));
        }
        $this->load->view('purchase_return/view', $data);
    }

    public function pdf($id) {
        $id = $this->encryption_url->decode($id);
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /*   $data['access_modules']          = $section_modules['modules'];
          $data['access_sub_modules']      = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege']   = $section_modules['user_privilege'];
          $data['access_settings']         = $section_modules['settings'];
          $data['access_common_settings']  = $section_modules['common_settings']; */
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $supplier_module_id = $this->config->item('supplier_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'supplier_module_id' => $supplier_module_id);
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
        $purchase_return_data = $this->common->purchase_return_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($purchase_return_data['string'], $purchase_return_data['table'], $purchase_return_data['where'], $purchase_return_data['join']);
        $country_data = $this->common->country_field($data['data'][0]->purchase_return_billing_country_id);
        $data['data_country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data = $this->common->state_field($data['data'][0]->purchase_return_billing_country_id, $data['data'][0]->purchase_return_billing_state_id);
        $data['data_state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));



        $product_items = $this->common->purchase_return_items_product_list_field($id);
        $purchase_return_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);


        $service_items = $this->common->purchase_return_items_service_list_field($id);
        $purchase_return_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_return_product_items, $purchase_return_service_items);
        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        $dpcount = 0;
        $dtcount = 0;
        $cess = 0;
        foreach ($data['items'] as $value) {
            $igst = bcadd($igst, $value->purchase_return_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->purchase_return_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->purchase_return_item_sgst_amount, 2);
            $cess = bcadd($cess, $value->purchase_return_item_cess_amount, 2);
            if ($value->purchase_return_item_description != "" && $value->purchase_return_item_description != null) {
                $dpcount++;
            } if ($value->purchase_return_item_discount_amount != "" && $value->purchase_return_item_discount_amount != null && $value->purchase_return_item_discount_amount != 0) {
                $dtcount++;
            }
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_return_billing_state_id);
        $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['dpcount'] = $dpcount;
        $data['dtcount'] = $dtcount;
        $data['cess_tax'] = $cess;
        $data['is_utgst'] = $is_utgst;
        $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1'] = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2'] = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        $html = $this->load->view('purchase_return/pdf', $data, true);





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

        $dompdf->stream($data['data'][0]->purchase_return_invoice_number, array(
            'Attachment' => 0));


        /*

          include(APPPATH . 'third_party/mpdf60/mpdf.php');
          $mpdf                           = new mPDF();
          $mpdf->allow_charset_conversion = true;
          $mpdf->charset_in               = 'UTF-8';
          $mpdf->WriteHTML($html);
          $mpdf->Output($data['data'][0]->purchase_return_invoice_number . '.pdf', 'I'); */
    }

    public function email($id) {
        $id = $this->encryption_url->decode($id);
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
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
            $purchase_return_data = $this->common->purchase_return_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_return_data['string'], $purchase_return_data['table'], $purchase_return_data['where'], $purchase_return_data['join']);
            $country_data = $this->common->country_field($data['data'][0]->purchase_return_billing_country_id);
            $data['data_country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data = $this->common->state_field($data['data'][0]->purchase_return_billing_country_id, $data['data'][0]->purchase_return_billing_state_id);
            $data['data_state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));


            if ($inventory_access[0]->inventory_advanced == "yes") {
                $product_items = $this->common->purchase_return_items_product_inventory_list_field($id);
                $purchase_return_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else {
                $product_items = $this->common->purchase_return_items_product_list_field($id);
                $purchase_return_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }

            $service_items = $this->common->purchase_return_items_service_list_field($id);
            $purchase_return_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items'] = array_merge($purchase_return_product_items, $purchase_return_service_items);
            $igst = 0;
            $cgst = 0;
            $sgst = 0;
            $dpcount = 0;
            $dtcount = 0;
            foreach ($data['items'] as $value) {
                $igst = bcadd($igst, $value->purchase_return_item_igst_amount, 2);
                $cgst = bcadd($cgst, $value->purchase_return_item_cgst_amount, 2);
                $sgst = bcadd($sgst, $value->purchase_return_item_sgst_amount, 2);
                if ($value->purchase_return_item_description != "" && $value->purchase_return_item_description != null) {
                    $dpcount++;
                } if ($value->purchase_return_item_discount_amount != "" && $value->purchase_return_item_discount_amount != null && $value->purchase_return_item_discount_amount != 0) {
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
            $html = $this->load->view('purchase_return/pdf', $data, true);
            include(APPPATH . 'third_party/mpdf60/mpdf.php');
            $mpdf = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in = 'UTF-8';
            $file_path = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_return_invoice_number);
            $file_name = str_replace('/','_',$file_name);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F');
            $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name'] = $file_name . '.pdf';
            $purchase_return_data = $this->common->purchase_return_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_return_data['string'], $purchase_return_data['table'], $purchase_return_data['where'], $purchase_return_data['join']);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id' => $purchase_return_module_id,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('purchase_return/email', $data);
        } else {
            $this->load->view('purchase_return', $data);
        }
    }

    public function convert_currency() {
        $id = $this->input->post('convert_currency_id');
        $id = $this->encryption_url->decode($id);
        $data = array(
            'currency_converted_amount' => $this->input->post('currency_converted_amount'));
        $this->general_model->updateData('purchase_return', $data, array(
            'purchase_return_id' => $id));
        redirect('purchase_return', 'refresh');
    }

    public function email_popup($id) {
        $id = $this->encryption_url->decode($id);
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $data['module_id'] = $purchase_return_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /*   $data['access_modules']          = $section_modules['modules'];
          $data['access_sub_modules']      = $section_modules['sub_modules'];
          $data['access_module_privilege'] = $section_modules['module_privilege'];
          $data['access_user_privilege']   = $section_modules['user_privilege'];
          $data['access_settings']         = $section_modules['settings'];
          $data['access_common_settings']  = $section_modules['common_settings']; */
        $product_module_id = $this->config->item('product_module');
        $service_module_id = $this->config->item('service_module');
        $supplier_module_id = $this->config->item('supplier_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $modules_present = array(
            'product_module_id' => $product_module_id,
            'service_module_id' => $service_module_id,
            'supplier_module_id' => $supplier_module_id);
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
        $purchase_return_data = $this->common->purchase_return_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($purchase_return_data['string'], $purchase_return_data['table'], $purchase_return_data['where'], $purchase_return_data['join']);
        $country_data = $this->common->country_field($data['data'][0]->purchase_return_billing_country_id);
        $data['data_country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data = $this->common->state_field($data['data'][0]->purchase_return_billing_country_id, $data['data'][0]->purchase_return_billing_state_id);
        $data['data_state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));

        $product_items = $this->common->purchase_return_items_product_list_field($id);
        $purchase_return_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        $service_items = $this->common->purchase_return_items_service_list_field($id);
        $purchase_return_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_return_product_items, $purchase_return_service_items);
        $igst = 0;
        $cgst = 0;
        $sgst = 0;
        $dpcount = 0;
        $dtcount = 0;
        $cess = 0;
        foreach ($data['items'] as $value) {
            $igst = bcadd($igst, $value->purchase_return_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->purchase_return_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->purchase_return_item_sgst_amount, 2);
            $cess = bcadd($cess, $value->purchase_return_item_cess_amount, 2);
            if ($value->purchase_return_item_description != "" && $value->purchase_return_item_description != null) {
                $dpcount++;
            } if ($value->purchase_return_item_discount_amount != "" && $value->purchase_return_item_discount_amount != null && $value->purchase_return_item_discount_amount != 0) {
                $dtcount++;
            }
        } $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['cess_tax'] = $cess;
        $data['dpcount'] = $dpcount;
        $data['dtcount'] = $dtcount;
        $note_data = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1'] = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2'] = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_return_billing_state_id);
        $data['is_utgst'] = $is_utgst;
        $html = $this->load->view('purchase_return/pdf', $data, true);
        /* include(APPPATH . 'third_party/mpdf60/mpdf.php');
          $mpdf                           = new mPDF();
          $mpdf->allow_charset_conversion = true;
          $mpdf->charset_in               = 'UTF-8';
          $file_path                      = "././pdf_form/";
          $mpdf->WriteHTML($html);
          $file_name                      = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_return_invoice_number);
          $mpdf->Output($file_path . $file_name . '.pdf', 'F'); */

        include APPPATH . "third_party/dompdf/autoload.inc.php";
        //and now im creating new instance dompdf
        $file_path = "././pdf_form/";
        $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_return_invoice_number);
        $dompdf = new Dompdf\Dompdf();
        $paper_size = 'a4';
        $orientation = 'portrait';
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents($file_path . $file_name . '.pdf', $output);
        $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
        $data['pdf_file_name'] = $file_name . '.pdf';
        $purchase_return_data = $this->common->purchase_return_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($purchase_return_data['string'], $purchase_return_data['table'], $purchase_return_data['where'], $purchase_return_data['join']);
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
            'delete_status' => 0,
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'added_user_id' => $this->session->userdata('SESS_USER_ID')));
        $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
            'module_id' => $purchase_return_module_id,
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
