<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Outlet extends MY_Controller{
    public $company_type = 'common';
    function __construct(){
        parent::__construct();
        $this->load->model([
            'general_model' ,
            'product_model' ,
            'service_model' ,
            'Voucher_model' ,
            'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    function index(){
        $outlet_module_id        = $this->config->item('outlet_module');
        $data['outlet_module_id'] = $outlet_module_id;
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;

        $section_modules        = $this->get_section_modules($outlet_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $data['email_module_id']           = $this->config->item('email_module');
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        if (!empty($this->input->post())){
            $columns             = array(
                                    0 => 'ot.outlet_id',
                                    1 => 'ot.outlet_date',
                                    2 => 'b.branch_name',
                                    3 => 'ot.outlet_grand_total'
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
            $list_data           = $this->common->outlet_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
            if (empty($this->input->post('search')['value'])){
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search')['value'];
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
                    $nestedData['billing_currency'] = $currency_symbol." (".round($post->outlet_grand_total,2).")";
                    $outlet_id = $this->encryption_url->encode($post->outlet_id);
                    $nestedData['date']        = date('d-m-Y', strtotime($post->outlet_date));
                    $nestedData['invoice'] = $post->outlet_invoice_number;
                    
                    $nestedData['branch_name']    = $post->branch_name;
                    $nestedData['grand_total'] = $currency_symbol . ' ' . $this->precise_amount($post->outlet_grand_total , $access_common_settings[0]->amount_precision) . ' (INV)';
                    
                    $nestedData['added_user']     = $post->first_name . ' ' . $post->last_name;
                    
                    if ($post->transferred_status == 0){
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }else if ($post->transferred_status == 1){
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }else if ($post->transferred_status == 2){
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }else{
                        $nestedData['payment_status'] = '<span class="label label-warning">Canceled</span>';
                    }
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($outlet_module_id , $data['active_view']))
                    {
                        $cols .= '<span><a href="' . base_url('outlet/view/') . $outlet_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="View Outlet"><i class="fa fa-eye"></i></a></span>';
                    }
                    if (in_array($outlet_module_id , $data['active_edit']))
                    {
                        if ($post->transferred_status == 0) {
                            $cols .= '<span><a href="' . base_url('outlet/edit/') . $outlet_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Edit Outlet"><i class="fa fa-pencil"></i></a></span>';
                        }
                    }
                      
                    if (in_array($outlet_module_id , $data['active_view']))
                    {
                        $customer_currency_code = $this->getCurrencyInfo($post->currency_id);
                        $customer_curr_code = '';
                        if(!empty($customer_currency_code))
                        $customer_curr_code     = $customer_currency_code[0]->currency_code;
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#pdf_type_modal"><a href="'.base_url('outlet/pdf/') . $outlet_id .'"  class="btn btn-app pdf_button" target="_blank" data-id="' . $outlet_id . '" data-name="regular" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }
                    /*if (in_array($data['email_module_id'] , $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'] , $data['access_sub_modules']))
                        {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#composeMail"><a data-id="' . $outlet_id . '" data-name="regular" href="javascript:void(0);" class="btn btn-app pdf_button composeMail" data-toggle="tooltip" data-placement="bottom" title="Email outlet"><i class="fa fa-envelope-o"></i></a></span>';
                        }
                    }*/

                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $conversion_date = $post->currency_converted_date;
                        if($conversion_date == '0000-00-00') $conversion_date = $post->added_date;
                        $conversion_date = date('d-m-Y',strtotime($conversion_date));
                        
                        /*$cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#convert_currency_modal"><a href="javascript:void(0);" class="btn btn-app convert_currency" data-id="' . $outlet_id . '" data-path="outlet/convert_currency" data-conversion_date="'.$conversion_date.'" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $this->precise_amount($post->outlet_grand_total, $access_common_settings[0]->amount_precision) . '" data-rate="' . $this->precise_amount($post->currency_converted_rate, $access_common_settings[0]->amount_precision) . '" data-toggle="tooltip" data-placement="bottom" title="Convert Currency"><i class="fa fa-exchange"></i></a></span>';*/
                    }

                    if (in_array($outlet_module_id , $data['active_delete']))
                    {
                        if ($post->transferred_status == 0) {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $outlet_id . '" data-path="outlet/delete" class="delete_button" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" ><a  href="javascript:void(0);" class="btn btn-app " data-toggle="tooltip" data-placement="bottom" title="Delete outlet"><i class="fa fa-trash-o"></i></a></span>';
                        }
                    }
                    $e_way_bill_date = '';
                    if($post->outlet_e_way_bill_date != '' && $post->outlet_e_way_bill_date != '0000-00-00'){

                        $e_way_bill_date =  date('d-m-Y', strtotime($post->outlet_e_way_bill_date));
                    }
                    $e_way_bill_number = $post->outlet_e_way_bill_number;
                    /*$cols .= '<span><a href="javascript:void(0);" data-target="#e_way_bill_modal" class="btn btn-app e_way_bill" data-toggle="tooltip"  data-id="' . $outlet_id . '"e_way_bill_date="' . $e_way_bill_date . '" e_way_bill_number="' . $e_way_bill_number . '"  data-placement="bottom" title="E Way Bill"><i class="fa fa-road"></i></a></span>';*/ 

                    $cols .= '<input type="hidden" value="'.$post->to_branch_id.'" name="to_branch_id">';
                    $cols .= '<input type="hidden" value="'.$post->outlet_id.'" name="outlet_id">';
                    $cols .= '</div>';                    
                    $cols .= '</div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal" value="'.$post->outlet_id.'">';
                    $send_data[]= $nestedData;
                }
            }
            $json_data = array(
                "draw"            => intval($this->input->post('draw')) ,
                "recordsTotal"    => intval($totalData) ,
                "recordsFiltered" => intval($totalFiltered) ,
                "data"            => $send_data );
            echo json_encode($json_data);
        }else{
            $this->load->view('outlet/list' , $data);
        }
    }

    function add()
    {
        $data              = $this->get_default_country_state();
        $outlet_module_id   = $this->config->item('outlet_module');
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = $privilege;
        /**/
        $section_modules   = $this->get_section_modules($outlet_module_id , $modules , $privilege);
        /* presents all the needed */
        $data              = array_merge($data , $section_modules);
        
        /* Modules Present */
        $data['outlet_module_id']           = $outlet_module_id;
        $data['module_id']                 = $outlet_module_id;
        $data['product_module_id']         = $this->config->item('product_module');
        $data['service_module_id']         = $this->config->item('service_module');
        $data['customer_module_id']        = $this->config->item('customer_module');
        $data['category_module_id']        = $this->config->item('category_module');
        $data['subcategory_module_id']     = $this->config->item('subcategory_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        $data['uqc_module_id']        = $this->config->item('uqc_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['branches'] = $this->branch_call();
        $data['currency'] = $this->currency_call();

        if ($data['access_settings'][0]->discount_visible == "yes")
        {
            $data['discount'] = $this->discount_call();
        }
        if ($data['access_settings'][0]->tax_type == "gst" || $data['access_settings'][0]->item_access == "single_tax")
        {
            $data['tax'] = $this->tax_call();
        }
        
        if ($data['access_settings'][0]->item_access == "product" || $data['access_settings'][0]->item_access == "both")
        {
            $data['inventory_access'] = $data['access_common_settings'][0]->inventory_advanced;
            $data['product_category'] = $this->product_category_call();
            $data['uqc']              = $this->uqc_call();
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['chapter']          = $this->chapter_call();
            $data['hsn']              = $this->hsn_call();
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();
        }
        $access_settings        = $data['access_settings'];
        $primary_id             = "outlet_id";
        $table_name             = $this->config->item('outlet_table');
        $date_field_name        = "outlet_date";
        $current_date           = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);
        $this->load->view('outlet/add' , $data); 
    }

    function edit($id){
        $id                = $this->encryption_url->decode($id);
        
        $data              = $this->get_default_country_state();
        $outlet_module_id   = $this->config->item('outlet_module');
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($outlet_module_id , $modules , $privilege);
        /* presents all the needed */
        $data              = array_merge($data , $section_modules);
        
        /* Modules Present */
        $data['outlet_module_id']           = $outlet_module_id;
        $data['module_id']                 = $outlet_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('product_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        $data['uqc_module_id']        = $this->config->item('uqc_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        $data['branches'] = $this->branch_call();
        $data['data'] = $this->general_model->getRecords('*' , 'outlet' , array(
            'outlet_id' => $id ));
        /*$country  = $this->general_model->getRecords('*', 'countries', array('country_name' => 'india' ));
        $country_id = $country[0]->country_id;*/
        $data['product_exist'] = 1;
        $data['currency'] = $this->currency_call();
        if ($data['data'][0]->outlet_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax"){
            $data['tax'] = $this->tax_call();
        }
        $data['inventory_access'] = "no";
        $data['product_category'] = $this->product_category_call();
        $data['uqc']              = $this->uqc_call();
        $data['uqc_product']      = $this->uqc_product_service_call('product');
        /*$data['hsn']              = $this->hsn_call();*/
        $data['tax_tcs']          = $this->tax_call_type('TCS');
        $data['tax_gst']          = $this->tax_call_type('GST');
        $data['tax_section'] = $this->tax_section_call();
        
        $outlet_product_items = array();
        $product_items       = $this->common->outlet_items_product_list_field($id);
        $data['items'] = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;

        if ($data['data'][0]->outlet_tax_amount > 0 && $data['data'][0]->outlet_igst_amount > 0 && ($data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount > 0 && ($data['data'][0]->outlet_cgst_amount > 0 || $data['data'][0]->outlet_sgst_amount > 0) && $data['data'][0]->outlet_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount > 0 && ($data['data'][0]->outlet_igst_amount == 0 && $data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount == 0 && ($data['data'][0]->outlet_igst_amount == 0 && $data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }
        if($data['data'][0]->outlet_tax_cess_amount > 0){
            $cessExist = 1;
        }
        if ($data['data'][0]->outlet_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }
        
        if ($data['data'][0]->outlet_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }
        if ($data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->outlet_billing_state_id);
        
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']        = $cessExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
        
        $this->load->view('outlet/edit' , $data);
    }

    function get_all_items(){
        $product_data = $this->common->all_products_field();
        $data         = $this->general_model->getRecords($product_data['string'] , $product_data['table'] , $product_data['where']);
        $pros = array();
        foreach ($data as $key => $value) {
            $pros[$value->product_id] = $value; 
        }
        echo json_encode($pros);
    }

    public function add_outlet()
    {
        /*echo "<pre>";
        print_r($this->input->post());
        exit;*/

        $data            = $this->get_default_country_state();
        $outlet_module_id = $this->config->item('outlet_module');
        $module_id       = $outlet_module_id;
        $modules         = $this->modules;
        $privilege       = "add_privilege";
        $section_modules = $this->get_section_modules($outlet_module_id , $modules , $privilege);
        /* presents all the needed */
        $data            = array_merge($data , $section_modules);
        /* Modules Present */
        $data['outlet_module_id']           = $outlet_module_id;
        $data['module_id']                 = $outlet_module_id;
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
            $primary_id      = "outlet_id";
            $table_name      = $this->config->item('outlet_table');
            $date_field_name = "outlet_date";
            $current_date    = date('Y-m-d',strtotime($this->input->post('invoice_date')));
            $invoice_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }
        $customer   = explode("-" , $this->input->post('customer'));
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0 ;
        $outlet_data = array(
            "outlet_date"                            => date('Y-m-d',strtotime($this->input->post('invoice_date'))),
            "outlet_invoice_number"                  => $invoice_number ,
            "outlet_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
            "outlet_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
            "outlet_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
            "outlet_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
            "outlet_tax_cess_amount"                 => 0 ,
            "outlet_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
            "outlet_tds_amount"                      => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0 ,
            "outlet_tcs_amount"
                      => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0 ,
            "outlet_igst_amount"                     => 0 ,
            "outlet_cgst_amount"                     => 0 ,
            "outlet_sgst_amount"                     => 0 ,
            "from_account"                          => 'customer' ,
            "to_account"                            => 'outlet' ,
            "credit_note_amount"                    => 0 ,
            "debit_note_amount"                     => 0 ,
            "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
            "to_branch_id"                        => $this->input->post('to_branch_id'),
            "outlet_nature_of_supply"                => $this->input->post('nature_of_supply'),
            "outlet_type_of_supply"                  => $this->input->post('type_of_supply') ,
            "due_days"                              => $this->input->post('due_days'),
            "outlet_gst_payable"                     => $this->input->post('gst_payable') ,
            "outlet_billing_country_id"              => $this->input->post('billing_country') ,
            "outlet_billing_state_id"                => $this->input->post('billing_state') ,
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
        $outlet_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $outlet_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $outlet_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $outlet_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $outlet_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $outlet_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        $round_off_value = $outlet_data['outlet_grand_total'];
        /*Cutomize for Leather Craft*/
        
        if(@$this->input->post('cash_discount')){
            $outlet_data['outlet_cash_discount'] = $this->input->post('cash_discount');
        }

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" && $this->input->post('round_off_key') == "yes"){
            if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 ){
                $round_off_value = $this->input->post('round_off_value');
            }
        }
        $outlet_data['round_off_amount'] = bcsub($outlet_data['outlet_grand_total'] , $round_off_value,$section_modules['access_common_settings'][0]->amount_precision);
        $outlet_data['outlet_grand_total'] = $round_off_value;
        $outlet_data['customer_payable_amount'] = $outlet_data['outlet_grand_total'];
        if (isset($outlet_data['outlet_tds_amount']) && $outlet_data['outlet_tds_amount'] > 0){
            $outlet_data['customer_payable_amount'] = bcsub($outlet_data['outlet_grand_total'], $outlet_data['outlet_tds_amount']);
        }
        //$outlet_tax_amount = $outlet_data['outlet_tax_amount'];
        $outlet_tax_amount = $outlet_data['outlet_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));
        
        if ($section_modules['access_settings'][0]->tax_type == "gst")
        {
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            if ($outlet_data['outlet_billing_state_id'] != 0){
                if ($data['branch'][0]->branch_state_id == $outlet_data['outlet_billing_state_id']){
                    $outlet_data['outlet_igst_amount'] = 0;
                    $outlet_data['outlet_cgst_amount'] = ($outlet_tax_amount * $cgst_amount_percentage) / 100;
                    $outlet_data['outlet_sgst_amount'] = ($outlet_tax_amount * $sgst_amount_percentage) / 100;
                    $outlet_data['outlet_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $outlet_data['outlet_igst_amount'] = $outlet_tax_amount;
                    $outlet_data['outlet_cgst_amount'] = 0;
                    $outlet_data['outlet_sgst_amount'] = 0;
                    $outlet_data['outlet_tax_cess_amount'] = $total_cess_amnt;
                }
            } else {
                if ($outlet_data['outlet_type_of_supply'] == "export_with_payment"){
                    $outlet_data['outlet_igst_amount'] = $outlet_tax_amount;
                    $outlet_data['outlet_cgst_amount'] = 0;
                    $outlet_data['outlet_sgst_amount'] = 0;
                    $outlet_data['outlet_tax_cess_amount'] = $total_cess_amnt;
                }
            }
        }
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
            $outlet_data['converted_grand_total'] = $outlet_data['outlet_grand_total'];
        }else{
            $outlet_data['converted_grand_total'] = 0;
        }
        $data_main   = array_map('trim' , $outlet_data);
        $outlet_table = $this->config->item('outlet_table');
        $outlet_id = $this->general_model->insertData($outlet_table , $data_main);

        if ($outlet_id > 0) {

            $inlet_main = array(
                "inlet_date"    => date('Y-m-d',strtotime($this->input->post('invoice_date'))),
                "outlet_id"     => $outlet_id,
                "inlet_invoice_number"                  => $invoice_number ,
                "inlet_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
                "inlet_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
                "inlet_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
                "inlet_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
                "inlet_tax_cess_amount"                 => 0 ,
                "inlet_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
                "inlet_tcs_amount" => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0 ,
                "inlet_igst_amount"                     => 0 ,
                "inlet_cgst_amount"                     => 0 ,
                "inlet_sgst_amount"                     => 0 ,
                "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "from_branch_id"                        => $this->session->userdata('SESS_BRANCH_ID'),
                "inlet_nature_of_supply"                => $this->input->post('nature_of_supply'),
                "inlet_type_of_supply"                  => $this->input->post('type_of_supply') ,
                "due_days"                              => $this->input->post('due_days'),
                "inlet_gst_payable"                     => $this->input->post('gst_payable') ,
                "inlet_billing_country_id"              => $this->input->post('billing_country') ,
                "inlet_billing_state_id"                => $this->input->post('billing_state') ,
                "added_date"                            => date('Y-m-d') ,
                "added_user_id"                         => $this->session->userdata('SESS_USER_ID') ,
                "branch_id"                             => $this->input->post('to_branch_id'),
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
                "warranty"                              => $this->input->post('warranty') ,
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
            $inlet_table = $this->config->item('inlet_table');
            $inlet_main   = array_map('trim' , $inlet_main);
            $inlet_id = $this->general_model->insertData($inlet_table , $inlet_main);
            $successMsg = 'outlet Added Successfully';
            $this->session->set_flashdata('outlet_success',$successMsg);
            $log_data              = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $outlet_id ,
                'table_name'        => $outlet_table ,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'outlet Inserted' );
            $data_main['outlet_id'] = $outlet_id;
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $js_data               = $this->input->post('table_data');
            $js_data               = array_reverse($js_data);
            $item_table            = $this->config->item('outlet_item_table');
            $inlet_item_table            = $this->config->item('inlet_item_table');
            
            if (!empty($js_data)){
                $js_data1 = array();
                foreach ($js_data as $key => $value){
                    $value = json_decode($value);
                    if ($value != null && $value != '') {
                        $item_id   = $value->item_id;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "item_id"                    => ($value->item_id != 0) ?  $value->item_id : $product_id ,
                            "outlet_id"                  => $outlet_id,
                            "outlet_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "outlet_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "outlet_item_free_quantity"   => (@$value->free_item_quantity ? (float) $value->free_item_quantity : 0),
                            "outlet_item_mrp_price"      => (@$value->item_mrp_price ? (float) $value->item_mrp_price : 0),
                            "outlet_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "outlet_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "outlet_item_cash_discount_amount" => (@$value->item_cash_discount ? (float) $value->item_cash_discount : 0) ,
                            "outlet_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0) ,
                            "outlet_item_discount_id"     => (@$value->item_discount_id ? (float) $value->item_discount_id : 0 ),
                            "outlet_item_tds_id"          => $value->item_tds_id ? (float) $value->item_tds_id : 0 ,
                            "outlet_item_tds_percentage"  => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ,
                            "outlet_item_tds_amount"      => $value->item_tds_amount ? (float) $value->item_tds_amount : 0 ,
                            "outlet_item_grand_total"     => $value->item_grand_total ? (float) $value->item_grand_total : 0 ,
                            "outlet_item_tax_id"          => $value->item_tax_id ? (float) $value->item_tax_id : 0 ,
                            "outlet_item_tax_cess_id"          => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0 ,
                            "outlet_item_igst_percentage" => 0 ,
                            "outlet_item_igst_amount"     => 0 ,
                            "outlet_item_cgst_percentage" => 0 ,
                            "outlet_item_cgst_amount"     => 0 ,
                            "outlet_item_sgst_percentage" => 0 ,
                            "outlet_item_sgst_amount"     => 0 ,
                            "outlet_item_tax_percentage"  => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ,
                            "outlet_item_tax_cess_percentage"  => 0 ,
                            "outlet_item_tax_amount"      => $value->item_tax_amount ? (float) $value->item_tax_amount : 0 ,
                            'outlet_item_tax_cess_amount' => 0 ,
                            "outlet_item_description"     => $value->item_description ? $value->item_description : "" ,
                            "outlet_item_uom_id"  => (@$value->item_uom ? $value->item_uom : ""),
                            "debit_note_quantity" => 0
                            );

                        $inlet_item_data = array(
                            "item_id"                    => ($value->item_id != 0) ?  $value->item_id : $product_id ,
                            "inlet_id" => $inlet_id,
                            "inlet_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "inlet_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "inlet_item_free_quantity"   => (@$value->free_item_quantity ? (float) $value->free_item_quantity : 0),
                            "inlet_item_mrp_price"      => (@$value->item_mrp_price ? (float) $value->item_mrp_price : 0),
                            "inlet_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "inlet_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "inlet_item_cash_discount_amount" => (@$value->item_cash_discount ? (float) $value->item_cash_discount : 0) ,
                            "inlet_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0) ,
                            "inlet_item_discount_id"     => (@$value->item_discount_id ? (float) $value->item_discount_id : 0 ),
                            "inlet_item_tds_id"          => $value->item_tds_id ? (float) $value->item_tds_id : 0 ,
                            "inlet_item_tds_percentage"  => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ,
                            "inlet_item_tds_amount"      => $value->item_tds_amount ? (float) $value->item_tds_amount : 0 ,
                            "inlet_item_grand_total"     => $value->item_grand_total ? (float) $value->item_grand_total : 0 ,
                            "inlet_item_tax_id"          => $value->item_tax_id ? (float) $value->item_tax_id : 0 ,
                            "inlet_item_tax_cess_id"          => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0 ,
                            "inlet_item_igst_percentage" => 0 ,
                            "inlet_item_igst_amount"     => 0 ,
                            "inlet_item_cgst_percentage" => 0 ,
                            "inlet_item_cgst_amount"     => 0 ,
                            "inlet_item_sgst_percentage" => 0 ,
                            "inlet_item_sgst_amount"     => 0 ,
                            "inlet_item_tax_percentage"  => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ,
                            "inlet_item_tax_cess_percentage"  => 0 ,
                            "inlet_item_tax_amount"      => $value->item_tax_amount ? (float) $value->item_tax_amount : 0 ,
                            'inlet_item_tax_cess_amount' => 0 ,
                            "inlet_item_description"     => $value->item_description ? $value->item_description : "" ,
                            "inlet_item_uom_id"  => (@$value->item_uom ? $value->item_uom : ""),
                            "debit_note_quantity" => 0 );

                        $outlet_item_tax_amount     = $item_data['outlet_item_tax_amount'];
                        $outlet_item_tax_percentage = $item_data['outlet_item_tax_percentage'];
                        if ($section_modules['access_settings'][0]->tax_type == "gst")
                        {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0 ;
                            
                            if ($outlet_data['outlet_billing_state_id'] != 0){
                                if ($data['branch'][0]->branch_state_id == $outlet_data['outlet_billing_state_id'])
                                {
                                    $item_data['outlet_item_igst_amount'] = 0;
                                    $item_data['outlet_item_cgst_amount'] = ($outlet_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['outlet_item_sgst_amount'] = ($outlet_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['outlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['outlet_item_igst_percentage'] = 0;
                                    $item_data['outlet_item_cgst_percentage'] = ($outlet_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['outlet_item_sgst_percentage'] = ($outlet_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['outlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;

                                    $inlet_item_data['inlet_item_igst_amount'] = 0;
                                    $inlet_item_data['inlet_item_cgst_amount'] = ($outlet_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $inlet_item_data['inlet_item_sgst_amount'] = ($outlet_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $inlet_item_data['inlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $inlet_item_data['inlet_item_igst_percentage'] = 0;
                                    $inlet_item_data['inlet_item_cgst_percentage'] = ($outlet_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $inlet_item_data['inlet_item_sgst_percentage'] = ($outlet_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $inlet_item_data['inlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                                else
                                {
                                    $item_data['outlet_item_igst_amount'] = $outlet_item_tax_amount;
                                    $item_data['outlet_item_cgst_amount'] = 0;
                                    $item_data['outlet_item_sgst_amount'] = 0;
                                    $item_data['outlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['outlet_item_igst_percentage'] = $outlet_item_tax_percentage;
                                    $item_data['outlet_item_cgst_percentage'] = 0;
                                    $item_data['outlet_item_sgst_percentage'] = 0;
                                    $item_data['outlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;

                                    $inlet_item_data['inlet_item_igst_amount'] = $outlet_item_tax_amount;
                                    $inlet_item_data['inlet_item_cgst_amount'] = 0;
                                    $inlet_item_data['inlet_item_sgst_amount'] = 0;
                                    $inlet_item_data['inlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $inlet_item_data['inlet_item_igst_percentage'] = $outlet_item_tax_percentage;
                                    $inlet_item_data['inlet_item_cgst_percentage'] = 0;
                                    $inlet_item_data['inlet_item_sgst_percentage'] = 0;
                                    $inlet_item_data['inlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                            else
                            {
                                if ($outlet_data['outlet_type_of_supply'] == "export_with_payment")
                                {
                                    $item_data['outlet_item_igst_amount'] = $outlet_item_tax_amount;
                                    $item_data['outlet_item_cgst_amount'] = 0;
                                    $item_data['outlet_item_sgst_amount'] = 0;
                                    $item_data['outlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['outlet_item_igst_percentage'] = $outlet_item_tax_percentage;
                                    $item_data['outlet_item_cgst_percentage'] = 0;
                                    $item_data['outlet_item_sgst_percentage'] = 0;
                                    $item_data['outlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;

                                    $inlet_item_data['inlet_item_igst_amount'] = $outlet_item_tax_amount;
                                    $inlet_item_data['inlet_item_cgst_amount'] = 0;
                                    $inlet_item_data['inlet_item_sgst_amount'] = 0;
                                    $inlet_item_data['inlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $inlet_item_data['inlet_item_igst_percentage'] = $outlet_item_tax_percentage;
                                    $inlet_item_data['inlet_item_cgst_percentage'] = 0;
                                    $inlet_item_data['inlet_item_sgst_percentage'] = 0;
                                    $inlet_item_data['inlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                        }

                        /* Customization leather craft fields */
                        if(@$value->item_basic_total){
                            $item_data['outlet_item_basic_total'] = $value->item_basic_total;
                            $inlet_item_data['inlet_item_basic_total'] = $value->item_basic_total;
                        }
                        
                        if(@$value->item_selling_price){
                            $item_data['outlet_item_selling_price'] = $value->item_selling_price;
                            $inlet_item_data['inlet_item_selling_price'] = $value->item_selling_price;
                        }
                        
                        if(@$value->item_mrkd_discount_amount){
                            $item_data['outlet_item_mrkd_discount_amount'] = $value->item_mrkd_discount_amount;
                            $inlet_item_data['inlet_item_mrkd_discount_amount'] = $value->item_mrkd_discount_amount;
                        }
                        
                        if(@$value->item_mrkd_discount_id){
                            $item_data['outlet_item_mrkd_discount_id'] = $value->item_mrkd_discount_id;
                            $inlet_item_data['inlet_item_mrkd_discount_id'] = $value->item_mrkd_discount_id;
                        }
                        
                        if(@$value->item_mrkd_discount_percentage){
                            $item_data['outlet_item_mrkd_discount_percentage'] = $value->item_mrkd_discount_percentage;
                            $inlet_item_data['inlet_item_mrkd_discount_percentage'] = $value->item_mrkd_discount_percentage;
                        }
                        
                        if(@$value->item_mrgn_discount_amount){
                            $item_data['outlet_item_mrgn_discount_amount'] = $value->item_mrgn_discount_amount;
                            $inlet_item_data['inlet_item_mrgn_discount_amount'] = $value->item_mrgn_discount_amount;
                        }
                        
                        if(@$value->item_mrgn_discount_id){
                            $item_data['outlet_item_mrgn_discount_id'] = $value->item_mrgn_discount_id;
                            $inlet_item_data['inlet_item_mrgn_discount_id'] = $value->item_mrgn_discount_id;
                        }
                        
                        if(@$value->item_mrgn_discount_percentage){
                            $item_data['outlet_item_mrgn_discount_percentage'] = $value->item_mrgn_discount_percentage;
                            $inlet_item_data['inlet_item_mrgn_discount_percentage'] = $value->item_mrgn_discount_percentage;
                        }

                        if(@$value->item_scheme_discount_amount){
                            $item_data['outlet_item_scheme_discount_amount'] = $value->item_scheme_discount_amount;
                            $inlet_item_data['inlet_item_scheme_discount_amount'] = $value->item_scheme_discount_amount;
                        }
                        
                        if(@$value->item_scheme_discount_id){
                            $item_data['outlet_item_scheme_discount_id'] = $value->item_scheme_discount_id;
                            $inlet_item_data['inlet_item_scheme_discount_id'] = $value->item_scheme_discount_id;
                        }
                        
                        if(@$value->item_scheme_discount_percentage){
                            $item_data['outlet_item_scheme_discount_percentage'] = $value->item_scheme_discount_percentage;
                            $inlet_item_data['inlet_item_scheme_discount_percentage'] = $value->item_scheme_discount_percentage;
                        }

                        if(@$value->item_out_tax_percentage){
                            $item_data['outlet_item_out_tax_percentage'] = $value->item_out_tax_percentage;
                            $inlet_item_data['inlet_item_out_tax_percentage'] = $value->item_out_tax_percentage;
                        }
                        
                        if(@$value->item_out_tax_amount){
                            $item_data['outlet_item_out_tax_amount'] = $value->item_out_tax_amount;
                            $inlet_item_data['inlet_item_out_tax_amount'] = $value->item_out_tax_amount;
                        }
                        
                        if(@$value->item_out_tax_id){
                            $item_data['outlet_item_out_tax_id'] = $value->item_out_tax_id;
                            $inlet_item_data['inlet_item_out_tax_id'] = $value->item_out_tax_id;
                        }
                       
                        /* End leather Craft */
                        $data_item  = array_map('trim' , $item_data);
                        $inlet_item_data  = array_map('trim' , $inlet_item_data);
                        $js_data1[] = $data_item;
                        
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
                            "reference_id"     => $outlet_id ,
                            "reference_number" => $invoice_number ,
                            "reference_type"   => 'outlet' ,
                            "quantity"         => $value->item_quantity ,
                            "stock_type"       => 'indirect' ,
                            "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') ,
                            "added_date"       => date('Y-m-d') ,
                            "entry_date"       => date('Y-m-d') ,
                            "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
                        $this->general_model->insertData("quantity_history" , $history);
                        $this->general_model->insertData($item_table , $data_item);
                        $this->general_model->insertData($inlet_item_table , $inlet_item_data);
                        
                    }
                }
                /*$this->db->insert_batch($item_table, $js_data1); */
                /*if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){
                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
                        $action = "add";
                        $this->outlet_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                    }
                }*/
            }
        }
        redirect('outlet' , 'refresh');
    }

    public function edit_outlet() {
        /*echo "<pre>";
        print_r($_POST);
        exit;*/
        $data            = $this->get_default_country_state();
        $outlet_id        = $this->input->post('outlet_id');
        $outlet_module_id = $this->config->item('outlet_module');
        $module_id       = $outlet_module_id;
        $modules         = $this->modules;
        $privilege       = "edit_privilege";
        $section_modules = $this->get_section_modules($outlet_module_id , $modules , $privilege);
        /* Modules Present */
        $data['outlet_module_id']           = $outlet_module_id;
        $data['module_id']                 = $outlet_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('product_module');
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
                $primary_id      = "outlet_id";
                $table_name      = $this->config->item('outlet_table');
                $date_field_name = "outlet_date";
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
        $outlet_data = array(
            "outlet_date"                            => date('Y-m-d',strtotime($this->input->post('invoice_date'))) ,
            "outlet_invoice_number"                  => $invoice_number ,
            "outlet_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
            "outlet_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
            "outlet_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
            "outlet_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
            "outlet_tax_cess_amount"                 => 0 ,
            "outlet_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
            "outlet_tds_amount"                      => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0 ,
            "outlet_tcs_amount"
                      => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0 ,
            "outlet_igst_amount"                     => 0 ,
            "outlet_cgst_amount"                     => 0 ,
            "outlet_sgst_amount"                     => 0 ,
            "credit_note_amount"                    => 0 ,
            "debit_note_amount"                     => 0 ,
            "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
            "outlet_nature_of_supply"                => $this->input->post('nature_of_supply') ,
            "outlet_gst_payable"                     => $this->input->post('gst_payable') ,
            "outlet_billing_country_id"              => $this->input->post('billing_country') ,
            "outlet_billing_state_id"                => $this->input->post('billing_state') ,
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
            "due_days"                              =>$this->input->post('due_days'),
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
        $outlet_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $outlet_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $outlet_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $outlet_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $outlet_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $outlet_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        /* customize for leather craft*/
        
        if(@$this->input->post('cash_discount')){
            $outlet_data['outlet_cash_discount'] = $this->input->post('cash_discount');
        }

        $round_off_value = $outlet_data['outlet_grand_total'];
        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" || $this->input->post('round_off_key') == "yes"){
            if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 ){
                $round_off_value = $this->input->post('round_off_value');
            }
        }
        $outlet_data['round_off_amount'] = bcsub($outlet_data['outlet_grand_total'] , $round_off_value,$section_modules['access_common_settings'][0]->amount_precision);
        $outlet_data['outlet_grand_total'] = $round_off_value;
        $outlet_data['customer_payable_amount'] = $outlet_data['outlet_grand_total'];
        if (isset($outlet_data['outlet_tds_amount']) && $outlet_data['outlet_tds_amount'] > 0){
            $outlet_data['customer_payable_amount'] = bcsub($outlet_data['outlet_grand_total'], $outlet_data['outlet_tds_amount']);
        }
        $tax_type         = $this->input->post('tax_type');
        $outlet_tax_amount = $outlet_data['outlet_tax_amount'];
        $outlet_tax_amount = $outlet_data['outlet_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));
        if ($section_modules['access_settings'][0]->tax_type == "gst"){
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            if ($outlet_data['outlet_billing_state_id'] != 0){
                if ($data['branch'][0]->branch_state_id == $outlet_data['outlet_billing_state_id']) {
                    $outlet_data['outlet_igst_amount'] = 0;
                    $outlet_data['outlet_cgst_amount'] = ($outlet_tax_amount * $cgst_amount_percentage) / 100;
                    $outlet_data['outlet_sgst_amount'] = ($outlet_tax_amount * $sgst_amount_percentage) / 100;
                    $outlet_data['outlet_tax_cess_amount'] = $total_cess_amnt;
                }
                else
                {
                    $outlet_data['outlet_igst_amount'] = $outlet_tax_amount;
                    $outlet_data['outlet_cgst_amount'] = 0;
                    $outlet_data['outlet_sgst_amount'] = 0;
                    $outlet_data['outlet_tax_cess_amount'] = $total_cess_amnt;
                }
            }
            else
            {
                if ($outlet_data['outlet_type_of_supply'] == "export_with_payment")
                {
                    $outlet_data['outlet_igst_amount'] = $outlet_tax_amount;
                    $outlet_data['outlet_cgst_amount'] = 0;
                    $outlet_data['outlet_sgst_amount'] = 0;
                    $outlet_data['outlet_tax_cess_amount'] = $total_cess_amnt;
                }
            }
        }
        
        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
            $outlet_data['converted_grand_total'] = $outlet_data['outlet_grand_total'];
        } else {
            $outlet_data['converted_grand_total'] = 0;
        }
        $data_main   = array_map('trim' , $outlet_data);
        $outlet_table = $this->config->item('outlet_table');
        $where       = array(
            'outlet_id' => $outlet_id );

        if ($this->general_model->updateData($outlet_table , $data_main , $where)){
            $successMsg = 'outlet Updated Successfully';
            $this->session->set_flashdata('outlet_success',$successMsg);
            $log_data              = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $outlet_id,
                'table_name'        => $outlet_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'outlet Updated' );
            $data_main['outlet_id'] = $outlet_id;
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            
            $js_data               = $this->input->post('table_data');
            $js_data               = array_reverse($js_data);
            $item_table            = $this->config->item('outlet_item_table');
            if (!empty($js_data)) {
                foreach ($js_data as $key => $value) {
                    $js_data[$key] = json_decode($value);
                }
                $js_data1 = array();
                $new_item_ids = $this->getValues($js_data,'item_id'); 
                
                $string          = 'outlet_item_id,outlet_item_quantity,item_id';
                $table           = 'outlet_item';
                $where           = array(
                    'outlet_id'      => $outlet_id ,
                    'delete_status' => 0 );
                $old_outlet_items = $this->general_model->getRecords($string , $table , $where , $order           = "");
                $old_item_ids = $this->getValues($old_outlet_items,'item_id');
                $not_deleted_ids= array();

                foreach ($old_outlet_items as $key => $value){
                    
                    $product_string = '*';
                    $product_table  = 'products';
                    $product_where  = array(
                        'product_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string , $product_table , $product_where , $order          = "");
                    $product_qty    = bcadd($product[0]->product_quantity , $value->outlet_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
                    $product_data   = array(
                        'product_quantity' => $product_qty );
                    $this->general_model->updateData($product_table , $product_data , $product_where);
                    /*$this->producthook->UpdateProductStock(array('product_id' => $value->item_id,'product_quantity' => $product_qty));*/
                    //update stock history
                    $where   = array(
                        'item_id'        => $value->item_id ,
                        'reference_id'   => $outlet_id ,
                        'reference_type' => 'outlet' ,
                        'delete_status'  => 0 );
                    $this->db->where($where);
                    $history = $this->db->get('quantity_history')->result();
                    if (!empty($history)){
                        $history_quantity        = bcadd($history[0]->quantity , $value->outlet_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
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
                            "reference_id"     => $outlet_id ,
                            "reference_number" => $invoice_number ,
                            "reference_type"   => 'outlet' ,
                            "quantity"         => 0 ,
                            "stock_type"       => 'indirect' ,
                            "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') ,
                            "added_date"       => date('Y-m-d') ,
                            "entry_date"       => date('Y-m-d') ,
                            "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
                        $this->general_model->insertData("quantity_history" , $history);
                    }
                    
                }
                
                foreach ($js_data as $key => $value) {
                    
                    if ($value != null) {
                        $item_id   = ($value->item_id != 0) ?  $value->item_id : $product_id;
                        //$item_id   = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "item_id"                    => ($value->item_id != 0) ?  $value->item_id : $product_id , 
                            "outlet_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "outlet_item_free_quantity"   => (@$value->free_item_quantity ? (float) $value->free_item_quantity : 0),
                            "outlet_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "outlet_item_mrp_price"      => (@$value->item_mrp_price ? (float) $value->item_mrp_price : 0),
                            "outlet_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "outlet_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "outlet_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0) ,
                            "outlet_item_cash_discount_amount" => (@$value->item_cash_discount ? (float) $value->item_cash_discount : 0) ,
                            "outlet_item_discount_id"     => (@$value->item_discount_id ? (float) $value->item_discount_id : 0) ,
                            "outlet_item_tds_id"          => $value->item_tds_id ? (float) $value->item_tds_id : 0 ,
                            "outlet_item_tds_percentage"  => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ,
                            "outlet_item_tds_amount"      => $value->item_tds_amount ? (float) $value->item_tds_amount : 0 ,
                            "outlet_item_grand_total"     => $value->item_grand_total ? (float) $value->item_grand_total : 0 ,
                            "outlet_item_tax_id"          => $value->item_tax_id ? (float)$value->item_tax_id : 0 ,
                            "outlet_item_tax_cess_id"     => $value->item_tax_cess_id ? (float)$value->item_tax_cess_id : 0 ,
                            "outlet_item_igst_percentage" => 0 ,
                            "outlet_item_igst_amount"     => 0 ,
                            "outlet_item_cgst_percentage" => 0 ,
                            "outlet_item_cgst_amount"     => 0 ,
                            "outlet_item_sgst_percentage" => 0 ,
                            "outlet_item_sgst_amount"     => 0 ,
                            "outlet_item_tax_percentage"  => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ,
                            "outlet_item_tax_amount"      => $value->item_tax_amount ? (float) $value->item_tax_amount : 0 ,
                            "outlet_item_tax_cess_percentage"  =>  0 ,
                            "outlet_item_tax_cess_amount"      =>  0 ,
                            "outlet_item_description"     => $value->item_description ? $value->item_description : "" ,
                            "outlet_item_uom_id"  => (@$value->item_uom ? $value->item_uom : ""),
                            "debit_note_quantity"        => 0 ,
                            "outlet_id"                   => $outlet_id );
                        $outlet_item_tax_amount     = $item_data['outlet_item_tax_amount'];
                        $outlet_item_tax_percentage = $item_data['outlet_item_tax_percentage'];

                        /* Customization leather craft fields */
                        if(@$value->item_basic_total){
                            $item_data['outlet_item_basic_total'] = $value->item_basic_total;
                        }
                        if(@$value->item_selling_price){
                            $item_data['outlet_item_selling_price'] = $value->item_selling_price;
                        }
                        if(@$value->item_mrkd_discount_amount){
                            $item_data['outlet_item_mrkd_discount_amount'] = $value->item_mrkd_discount_amount;
                        }
                        if(@$value->item_mrkd_discount_id){
                            $item_data['outlet_item_mrkd_discount_id'] = $value->item_mrkd_discount_id;
                        }
                        if(@$value->item_mrkd_discount_percentage){
                            $item_data['outlet_item_mrkd_discount_percentage'] = $value->item_mrkd_discount_percentage;
                        }
                        if(@$value->item_mrgn_discount_amount){
                            $item_data['outlet_item_mrgn_discount_amount'] = $value->item_mrgn_discount_amount;
                        }
                        if(@$value->item_mrgn_discount_id){
                            $item_data['outlet_item_mrgn_discount_id'] = $value->item_mrgn_discount_id;
                        }
                        if(@$value->item_mrgn_discount_percentage){
                            $item_data['outlet_item_mrgn_discount_percentage'] = $value->item_mrgn_discount_percentage;
                        }

                        if(@$value->item_scheme_discount_amount){
                            $item_data['outlet_item_scheme_discount_amount'] = $value->item_scheme_discount_amount;
                        }
                        if(@$value->item_scheme_discount_id){
                            $item_data['outlet_item_scheme_discount_id'] = $value->item_scheme_discount_id;
                        }
                        if(@$value->item_scheme_discount_percentage){
                            $item_data['outlet_item_scheme_discount_percentage'] = $value->item_scheme_discount_percentage;
                        }

                        if(@$value->item_out_tax_percentage){
                            $item_data['outlet_item_out_tax_percentage'] = $value->item_out_tax_percentage;
                        }
                        if(@$value->item_out_tax_amount){
                            $item_data['outlet_item_out_tax_amount'] = $value->item_out_tax_amount;
                        }
                        if(@$value->item_out_tax_id){
                            $item_data['outlet_item_out_tax_id'] = $value->item_out_tax_id;
                        }
                       
                        /* End leather Craft */

                        if ($tax_type == "gst") {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0 ;
                            if ($outlet_data['outlet_billing_state_id'] != 0){
                                if ($data['branch'][0]->branch_state_id == $outlet_data['outlet_billing_state_id'])
                                {
                                    $item_data['outlet_item_igst_amount'] = 0;
                                    $item_data['outlet_item_cgst_amount'] = ($outlet_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['outlet_item_sgst_amount'] = ($outlet_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['outlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['outlet_item_igst_percentage'] = 0;
                                    $item_data['outlet_item_cgst_percentage'] = ($outlet_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['outlet_item_sgst_percentage'] = ($outlet_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['outlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                                else
                                {
                                    $item_data['outlet_item_igst_amount'] = $outlet_item_tax_amount;
                                    $item_data['outlet_item_cgst_amount'] = 0;
                                    $item_data['outlet_item_sgst_amount'] = 0;
                                    $item_data['outlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['outlet_item_igst_percentage'] = $outlet_item_tax_percentage;
                                    $item_data['outlet_item_cgst_percentage'] = 0;
                                    $item_data['outlet_item_sgst_percentage'] = 0;
                                    $item_data['outlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }else{
                                if ($outlet_data['outlet_type_of_supply'] == "export_with_payment"){
                                    $item_data['outlet_item_igst_amount'] = $outlet_item_tax_amount;
                                    $item_data['outlet_item_cgst_amount'] = 0;
                                    $item_data['outlet_item_sgst_amount'] = 0;
                                    $item_data['outlet_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['outlet_item_igst_percentage'] = $outlet_item_tax_percentage;
                                    $item_data['outlet_item_cgst_percentage'] = 0;
                                    $item_data['outlet_item_sgst_percentage'] = 0;
                                    $item_data['outlet_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                        }
                        
                        $table = 'outlet_item';
                        if (($item_key = array_search($value->item_id, $old_item_ids)) !== false) {
                            unset($old_item_ids[$item_key]);
                            $outlet_item_id = $old_outlet_items[$item_key]->outlet_item_id;
                            array_push($not_deleted_ids,$outlet_item_id );
                            $where = array('outlet_item_id' => $outlet_item_id );
                            $this->general_model->updateData($table , $item_data , $where);
                        }else{
                            $this->general_model->insertData($table , $item_data);
                        }
                        /* update product stock */
                        if ($value->item_type == "product" || $value->item_type == 'product_inventory'){
                            $product_string = '*';
                            $product_table  = 'products';
                            $product_where  = array('product_id' => $item_id );
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
                                'reference_id'   => $outlet_id ,
                                'reference_type' => 'outlet' ,
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
                                    "reference_id"     => $outlet_id ,
                                    "reference_number" => $invoice_number ,
                                    "reference_type"   => 'outlet' ,
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
                if(!empty($old_outlet_items)){
                    foreach ($old_outlet_items as $key => $items) {
                        if(!in_array( $items->outlet_item_id,$not_deleted_ids)){
                            $table      = 'outlet_item';
                            $where      = array(
                                'outlet_item_id' => $items->outlet_item_id );
                            $outlet_data = array(
                                'delete_status' => 1 );
                           
                            $this->general_model->updateData($table , $outlet_data , $where);
                        }
                    }
                }
                $item_data = $js_data1;

                /*if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){
                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
                        $action = "edit";
                        $this->outlet_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                    }
                }*/
            } 
            redirect('outlet' , 'refresh');
        }else{
            $errorMsg = 'outlet Update Unsuccessful';
            $this->session->set_flashdata('outlet_error',$errorMsg);
            redirect('outlet' , 'refresh');
        }
    }

    public function pdf($id){
        $id                = $this->encryption_url->decode($id);
        $data              = $this->get_default_country_state();
        $outlet_module_id   = $this->config->item('outlet_module');
        $data['module_id'] = $outlet_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($outlet_module_id , $modules , $privilege);
        $data              = array_merge($data , $section_modules);
        $product_module_id             = $this->config->item('product_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);
        $outlet_data = $this->common->outlet_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($outlet_data['string'] , $outlet_data['table'] , $outlet_data['where'] , $outlet_data['join']);
        
        $data['product_exist'] = 1;
        $outlet_product_items = array();
        $product_items       = $this->common->outlet_items_product_list_field($id);
        $data['items'] = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;

        if ($data['data'][0]->outlet_tax_amount > 0 && $data['data'][0]->outlet_igst_amount > 0 && ($data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount > 0 && ($data['data'][0]->outlet_cgst_amount > 0 || $data['data'][0]->outlet_sgst_amount > 0) && $data['data'][0]->outlet_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount > 0 && ($data['data'][0]->outlet_igst_amount == 0 && $data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount == 0 && ($data['data'][0]->outlet_igst_amount == 0 && $data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }
        if($data['data'][0]->outlet_tax_cess_amount > 0){
            $cessExist = 1;
        }
        if ($data['data'][0]->outlet_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }
        
        if ($data['data'][0]->outlet_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }
        if ($data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->outlet_billing_state_id);
        
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']        = $cessExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        
        
        $invoice_type = "original";

        $data['invoice_type'] = $invoice_type;
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
        
        $html = $this->load->view('outlet/pdf' , $data , true);

        include(APPPATH . "third_party/dompdf/autoload.inc.php");
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();
        
        $dompdf->load_html($html);
        $paper_size = 'a4';
        $orientation = 'portrait';
        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
       // $customPaper = array(0,0,360,360);
        
        $dompdf->set_paper($paper_size,$orientation);
        $dompdf->render();
        ob_end_clean();
        $dompdf->stream($data['data'][0]->outlet_invoice_number , array(
            'Attachment' => 0 ));
    }

    public function view($id){
        $id                = $this->encryption_url->decode($id);
        $data              = $this->get_default_country_state();
        $outlet_module_id   = $this->config->item('outlet_module');
        $data['module_id'] = $outlet_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($outlet_module_id , $modules , $privilege);
        $data              = array_merge($data , $section_modules);
        $product_module_id             = $this->config->item('product_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');
        
        $outlet_data = $this->common->outlet_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($outlet_data['string'] , $outlet_data['table'] , $outlet_data['where'] , $outlet_data['join']);
        
        $data['product_exist'] = 1;
        $outlet_product_items = array();
        $product_items       = $this->common->outlet_items_product_list_field($id);
        $data['items'] = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;

        if ($data['data'][0]->outlet_tax_amount > 0 && $data['data'][0]->outlet_igst_amount > 0 && ($data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount > 0 && ($data['data'][0]->outlet_cgst_amount > 0 || $data['data'][0]->outlet_sgst_amount > 0) && $data['data'][0]->outlet_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount > 0 && ($data['data'][0]->outlet_igst_amount == 0 && $data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->outlet_tax_amount == 0 && ($data['data'][0]->outlet_igst_amount == 0 && $data['data'][0]->outlet_cgst_amount == 0 && $data['data'][0]->outlet_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }
        if($data['data'][0]->outlet_tax_cess_amount > 0){
            $cessExist = 1;
        }
        if ($data['data'][0]->outlet_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }
        
        if ($data['data'][0]->outlet_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }
        if ($data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->outlet_billing_state_id);
        
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']        = $cessExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
        $invoice_type = "original";

        $data['invoice_type'] = $invoice_type;
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
        $this->load->view('outlet/view' , $data);
    }

    function delete(){
        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);

        $outlet_module_module_id          = $this->config->item('outlet_module');
        $data['module_id']               = $outlet_module_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($outlet_module_module_id, $modules, $privilege);
        $data                   = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        if ($this->general_model->updateData('outlet', array(
                        'delete_status' => 1 ), array(
                        'outlet_id' => $id )))
        {
            $successMsg = 'Outlet Deleted Successfully';
            $this->session->set_flashdata('outlet_success',$successMsg);
            $this->general_model->updateData('outlet_item', array(
                    'delete_status' => 1 ), array(
                    'outlet_id' => $id ));
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'outlet',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Outlet Deleted' );
            $this->general_model->insertData('log', $log_data);

            $redirect = 'outlet';
            if($this->input->post('delete_redirect') != '') $redirect = $this->input->post('delete_redirect');
           
            redirect($redirect , 'refresh');
        }
        else
        {
            $errorMsg = 'Outlet Delete Unsuccessful';
            $this->session->set_flashdata('outlet_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Outlet can not be Deleted.');
            redirect("outlet", 'refresh');
        }
    }
}