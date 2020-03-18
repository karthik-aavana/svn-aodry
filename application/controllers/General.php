<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class General extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model(['general_model','report_model']);
        $this->modules = $this->get_modules();
    }

    public function get_state()
    {
        $country_id = $this->input->post('id');
        $state_data = $this->common->state_field($country_id);
        $data       = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        echo json_encode($data);
    }

    public function ValidateInvoiceDate(){
        $invoice_date = date('Y-m-d',strtotime($this->input->post('invoice_date')));
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $this->db->select('from_date,to_date');
        $this->db->where('branch_id',$branch_id);
        $this->db->where('from_date <=',$invoice_date);
        $this->db->where('year_status','1');
        $this->db->where('to_date >=',$invoice_date);
        $q = $this->db->get('tbl_financial_year');
       
        echo $q->num_rows();
    }

    public function validateBranchCode(){
        $branch_code = $this->input->post('branch_code');
        $flag = true;
        if(@$branch_code){
            $this->db->select('branch_code');
            $this->db->from('branch');
            $this->db->where('branch_code',trim($branch_code));
            $this->db->where('delete_status',0);
            $check_qry = $this->db->get();
            if($check_qry->num_rows() > 0){
                $flag = false;
            }
        }
        echo json_encode(array('flag' => $flag));
    }

    public function get_shipping_address() {
        $party_id                      = $this->input->post('party_id');
        $party_type                    = $this->input->post('party_type');
        $state_id = 0;
        if($party_type == 'customer'){
            $cust_detail = $this->general_model->getRecords('customer_country_id,customer_state_id,customer_country_id', 'customer', array(
                'customer_id'   => $party_id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
            if(!empty($cust_detail)){
               
                $data['customer_detail']['out_of_india'] = true;
                $data['customer_detail']['customer_state_id'] = $state_id = $cust_detail[0]->customer_state_id;
                $UTGST = $this->config->item('UTGST');
                $is_utgst = 0;
                foreach ($UTGST as $key => $value) {
                    if($value == $cust_detail[0]->customer_state_id){
                        $is_utgst = 1;
                        break;
                    }
                }
                $data['customer_detail']['is_utgst'] = $is_utgst;
                $data['customer_detail']['customer_country_id'] = $cust_detail[0]->customer_country_id;
                if($cust_detail[0]->customer_country_id == '101'){ /* 101 for India */
                    $data['customer_detail']['out_of_india'] = false;
                }
            }
        }elseif($party_type == 'supplier'){
            $cust_detail = $this->general_model->getRecords('supplier_country_id,supplier_state_id,supplier_country_id', 'supplier', array(
                'supplier_id'   => $party_id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
            if(!empty($cust_detail)){
               
                $data['supplier_detail']['out_of_india'] = true;
                $data['supplier_detail']['supplier_state_id'] = $state_id = $cust_detail[0]->supplier_state_id;
                $UTGST = $this->config->item('UTGST');
                $is_utgst = 0;
                foreach ($UTGST as $key => $value) {
                    if($value == $cust_detail[0]->supplier_state_id){
                        $is_utgst = 1;
                        break;
                    }
                }
                $data['supplier_detail']['is_utgst'] = $is_utgst;
                $data['supplier_detail']['supplier_country_id'] = $cust_detail[0]->supplier_country_id;
                if($cust_detail[0]->supplier_country_id == '101'){ /* 101 for India */
                    $data['supplier_detail']['out_of_india'] = false;
                }
            }
            
        }

          $data['shipping_address_data'] = $this->general_model->getRecords('*', 'shipping_address', array(
            'delete_status'       => 0,
            'shipping_party_id'   => $party_id,
            'shipping_party_type' => $party_type,
            /*'state_id' => $state_id,*/
            'branch_id'           => $this->session->userdata('SESS_BRANCH_ID')));
        
        echo json_encode($data);
    }

    public function add_shipping_address()
    {
        $modal_shipping_address        = $this->input->post('modal_shipping_address');
        $modal_party_id                = $this->input->post('modal_party_id');
        $modal_party_type              = $this->input->post('modal_party_type');
        $modal_shipping_gstin          = $this->input->post('modal_shipping_gstin');
        $shipping_address_data         = array(
            'shipping_address'    => $modal_shipping_address,
            'shipping_gstin'      => $modal_shipping_gstin,
            'shipping_party_id'   => $modal_party_id,
            'shipping_party_type' => $modal_party_type,
            'added_date'          => date('Y-m-d'),
            'added_user_id'       => $this->session->userdata('SESS_USER_ID'),
            'branch_id'           => $this->session->userdata('SESS_BRANCH_ID'));
        $data['shipping_address_id']   = $this->general_model->insertData('shipping_address', $shipping_address_data);
        $data['shipping_address_data'] = $this->general_model->getRecords('*', 'shipping_address', array(
            'delete_status'       => 0,
            'shipping_party_id'   => $modal_party_id,
            'shipping_party_type' => $modal_party_type,
            'branch_id'           => $this->session->userdata('SESS_BRANCH_ID')));
        echo json_encode($data);
    }
    public function get_shipping_address_place_change() {
        $party_id                      = $this->input->post('party_id');
        $party_type                    = $this->input->post('party_type');
        $state_id =  $this->input->post('state_id');
        $country  = $this->general_model->getRecords('*', 'countries', array('country_name' => 'india' ));
        $country_id = $country[0]->country_id;
        if($state_id == 0){
             $data['shipping_address_data'] = $this->general_model->getRecords('*', 'shipping_address', array(
                'delete_status'       => 0,
                'shipping_party_id'   => $party_id,
                'shipping_party_type' => $party_type,
                'country_id!=' => $country_id,
                'branch_id'           => $this->session->userdata('SESS_BRANCH_ID')));
        }else{
            $data['shipping_address_data'] = $this->general_model->getRecords('*', 'shipping_address', array(
                'delete_status'       => 0,
                'shipping_party_id'   => $party_id,
                'shipping_party_type' => $party_type,
                'state_id' => $state_id,
                'branch_id'           => $this->session->userdata('SESS_BRANCH_ID')));           
        }
       
         echo json_encode($data);
    }

    public function get_shipping_address_place_edit() {
        $party_id                      = $this->input->post('party_id');
        $party_type                    = $this->input->post('party_type');
        $data['shipping_address_data'] = $this->general_model->getRecords('*', 'shipping_address', array(
                'delete_status'       => 0,
                'shipping_party_id'   => $party_id,
                'shipping_party_type' => $party_type,
                'branch_id'           => $this->session->userdata('SESS_BRANCH_ID')));
         echo json_encode($data);
    }


    public function get_city($state_id)
    {
        $city_data = $this->common->city_field($state_id);
        $data      = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        echo json_encode($data);
    }

    public function get_branch()
    {
        if (!empty($this->input->post('firm_id')) && $this->input->post('firm_id') != "")
        {
            $firm_id = $this->input->post('firm_id');
        }
        else
        {
            $firm_id = "";
        } $branch_data = $this->common->branch_field($firm_id);
        $data        = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        echo json_encode($data);
    }

    public function get_state_data($state_id)
    {
        $state_data = $this->common->state_field($country_id = "", $state_id);
        $data       = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        echo json_encode($data);
    }

    public function set_financial_year($financial_year_id)
    {
        $this->session->set_userdata('SESS_FINANCIAL_YEAR_ID', $financial_year_id);
        $financial_year_data  = $this->common->financial_year_field($financial_year_id);
        $data                 = $this->general_model->getRecords($financial_year_data['string'], $financial_year_data['table'], $financial_year_data['where']);
        $financial_year_title = $data[0]->financial_year_title;
        $this->session->set_userdata('SESS_FINANCIAL_YEAR_TITLE', trim($financial_year_title));
        $data                 = "success";
        echo json_encode($data);
    }

    public function generate_date_reference()
    {
        $date      = $this->input->post('date');
        $module_id = $this->input->post('module_id');
        $privilege = $this->input->post('privilege');
        if ($date == "current")
        {
            $date = date('Y-m-d');
        }
        $modules         = $this->modules;
        $privilege       = $privilege;
        $section_modules = $this->get_section_modules($module_id, $modules, $privilege);
        $access_settings = $section_modules['access_settings'];
        if ($module_id == $this->config->item('sales_module'))
        {
            $primary_id      = "sales_id";
            $table_name      = "sales";
            $date_field_name = "sales_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('quotation_module'))
        {
            $primary_id      = "quotation_id";
            $table_name      = "quotation";
            $date_field_name = "quotation_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('purchase_order_module'))
        {
            $primary_id      = "purchase_order_id";
            $table_name      = "purchase_order";
            $date_field_name = "purchase_order_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('purchase_module'))
        {
            $primary_id      = "purchase_id";
            $table_name      = "purchase";
            $date_field_name = "purchase_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('purchase_return_module'))
        {
            $primary_id      = "purchase_return_id";
            $table_name      = "purchase_return";
            $date_field_name = "purchase_return_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('product_module'))
        {
            $primary_id              = "product_id";
            $table_name              = "products";
            $date_field_name         = "added_date";
            $current_date            = $date;
            $data["access_settings"] = $access_settings;
        }
        elseif ($module_id == $this->config->item('service_module'))
        {
            $primary_id              = "service_id";
            $table_name              = "services";
            $date_field_name         = "added_date";
            $current_date            = $date;
            $data["access_settings"] = $access_settings;
        }
        elseif ($module_id == $this->config->item('customer_module'))
        {
            $primary_id              = "customer_id";
            $table_name              = "customer";
            $date_field_name         = "added_date";
            $current_date            = $date;
            $data["access_settings"] = $access_settings;
        }
        elseif ($module_id == $this->config->item('supplier_module'))
        {
            $primary_id              = "supplier_id";
            $table_name              = "supplier";
            $date_field_name         = "added_date";
            $current_date            = $date;
            $data["access_settings"] = $access_settings;
        }
        elseif ($module_id == $this->config->item('receipt_voucher_module'))
        {
            $primary_id      = "receipt_id";
            $table_name      = "receipt_voucher";
            $date_field_name = "voucher_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('expense_bill_module'))
        {
            $primary_id      = "expense_bill_id";
            $table_name      = "expense_bill";
            $date_field_name = "expense_bill_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('payment_voucher_module'))
        {
            $primary_id      = "payment_id";
            $table_name      = "payment_voucher";
            $date_field_name = "voucher_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('advance_voucher_module'))
        {
            $primary_id      = "advance_id";
            $table_name      = "advance_voucher";
            $date_field_name = "voucher_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('refund_voucher_module'))
        {
            $primary_id      = "refund_id";
            $table_name      = "refund_voucher";
            $date_field_name = "voucher_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('sales_credit_note_module'))
        {
            $primary_id      = "sales_credit_note_id";
            $table_name      = "sales_credit_note";
            $date_field_name = "sales_credit_note_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('sales_debit_note_module'))
        {
            $primary_id      = "sales_debit_note_id";
            $table_name      = "sales_debit_note";
            $date_field_name = "sales_debit_note_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('purchase_credit_note_module'))
        {
            $primary_id      = "purchase_credit_note_id";
            $table_name      = "purchase_credit_note";
            $date_field_name = "purchase_credit_note_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('purchase_debit_note_module'))
        {
            $primary_id      = "purchase_debit_note_id";
            $table_name      = "purchase_debit_note";
            $date_field_name = "purchase_debit_note_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('delivery_challan_module'))
        {
            $primary_id      = "delivery_challan_id";
            $table_name      = "delivery_challan";
            $date_field_name = "delivery_challan_date";
            $current_date    = $date;
        }
        elseif ($module_id == $this->config->item('general_bill_module'))
        {
            $primary_id      = "general_bill_id";
            $table_name      = "general_bill";
            $date_field_name = "general_bill_date";
            $current_date    = $date;
        }

        $invoice_number       = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, '1');
        $data["reference_no"] = $invoice_number;

        echo json_encode($data);
    }

    public function check_date_reference()
    {
        $invoice_number  = $this->input->post('invoice_number');
        $module_id       = $this->input->post('module_id');
        $privilege       = $this->input->post('privilege');
        $modules         = $this->modules;
        $privilege       = $privilege;
        $section_modules = $this->get_section_modules($module_id, $modules, $privilege);
        $access_settings = $section_modules['access_settings'];
        
        $access_settings = $section_modules['access_settings'];
        if ($module_id == $this->config->item('sales_module'))
        {
            $table_name         = "sales";
            $invoice_field_name = "sales_invoice_number";
        }
        elseif ($module_id == $this->config->item('quotation_module'))
        {
            $table_name         = "quotation";
            $invoice_field_name = "quotation_invoice_number";
        }
        elseif ($module_id == $this->config->item('purchase_order_module'))
        {
            $table_name         = "purchase_order";
            $invoice_field_name = "purchase_order_invoice_number";
        }
        elseif ($module_id == $this->config->item('purchase_module'))
        {
            $table_name         = "purchase";
            $invoice_field_name = "purchase_invoice_number";
        }
        elseif ($module_id == $this->config->item('purchase_return_module'))
        {
            $table_name         = "purchase_return";
            $invoice_field_name = "purchase_return_invoice_number";
        }
        elseif ($module_id == $this->config->item('receipt_voucher_module'))
        {
            $table_name         = "receipt_voucher";
            $invoice_field_name = "voucher_number";
        }
        elseif ($module_id == $this->config->item('expense_bill_module'))
        {
            $table_name         = "expense_bill";
            $invoice_field_name = "expense_bill_invoice_number";
        }
        elseif ($module_id == $this->config->item('payment_voucher_module'))
        {
            $table_name         = "payment_voucher";
            $invoice_field_name = "voucher_number";
        }
        elseif ($module_id == $this->config->item('advance_voucher_module'))
        {
            $table_name         = "advance_voucher";
            $invoice_field_name = "voucher_number";
        }
        elseif ($module_id == $this->config->item('refund_voucher_module'))
        {
            $table_name         = "refund_voucher";
            $invoice_field_name = "voucher_number";
        }
        elseif ($module_id == $this->config->item('sales_credit_note_module'))
        {
            $table_name         = "sales_credit_note";
            $invoice_field_name = "sales_credit_note_invoice_number";
        }
        elseif ($module_id == $this->config->item('sales_debit_note_module'))
        {
            $table_name         = "sales_debit_note";
            $invoice_field_name = "sales_debit_note_invoice_number";
        }
        elseif ($module_id == $this->config->item('purchase_credit_note_module'))
        {
            $table_name         = "purchase_credit_note";
            $invoice_field_name = "purchase_credit_note_invoice_number";
        }
        elseif ($module_id == $this->config->item('purchase_debit_note_module'))
        {
            $table_name         = "purchase_debit_note";
            $invoice_field_name = "purchase_debit_note_invoice_number";
        }
        elseif ($module_id == $this->config->item('delivery_challan_module'))
        {
            $table_name         = "delivery_challan";
            $invoice_field_name = "delivery_challan_invoice_number";
        } 


        $invoice_count = $this->get_check_invoice_number($table_name, $invoice_field_name, $invoice_number,$access_settings);
        if($privilege == 'edit_privilege'){
            if($invoice_count[0]->num == 1){
                $invoice_count[0]->num = 0;
            }
        }
        echo json_encode($invoice_count);
    }

    public function get_shipping_popup (){
        $party_id = $this->input->post('party_id');
        $shipping_id =  $this->input->post('shipping_id');
        $party_type =  'customer';
        $state_id =  $this->input->post('billing_state');
        $country  = $this->general_model->getRecords('*', 'countries', array('country_name' => 'india' ));
        $country_id = $country[0]->country_id;
        $list_data  = $this->common->shipping_address_list_popup($party_id,$party_type,$state_id,$country_id);
        $shipping_address_data = $this->general_model->getPageJoinRecords($list_data);
        $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
       $send = array();


       if(!empty($shipping_address_data)){
         foreach ($shipping_address_data as $com) { //echo "<pre>"; print_r($shipping_address_data);

            $primary_address = $com->primary_address;
            $id = $com->shipping_address_id;
            $nestedData['shipping_code'] = $com->shipping_code;
            $nestedData['shipping_address'] = $com->shipping_address;
            $nestedData['contact_person'] = $com->contact_person;
            $nestedData['gst'] = $com->shipping_gstin;
            $nestedData['state'] = $com->state_name;
            $state_id = $com->state_id;
            $country_id_shipping = $com->country_id;
            if($country_id_shipping != $country_id){
                $type = 'other';
            }else{
                $type = 'same';
            }

            if($primary_address == 'yes'){
                $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'. $id.'" checked/><input type="hidden" name="apply_country_id" value="'. $country_id_shipping.'" id="apply_country_id_'. $id.'" checked/><input type="hidden" name="state_id_suppuly" id="state_id_suppuly_'.$id.'" value="'.$state_id.'"><input type="hidden" name="country_type" id="country_type_'.$id.'" value="'.$type.'">';
            }else{
            $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'. $id.'"/><input type="hidden" name="apply_country_id" value="'. $country_id_shipping.'" id="apply_country_id_'. $id.'" /><input type="hidden" name="state_id_suppuly" id="state_id_suppuly_'.$id.'" value="'.$state_id.'"><input type="hidden" name="country_type" id="country_type_'.$id.'" value="'.$type.'">';
            }

            if($shipping_id == $id && $shipping_id != ""){

                   $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'.$id.'" checked />'; 
                }else if($shipping_id != ""){
                 $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'.$id.'"/>';
                }
                /*$nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'.$id.'"/>';*/
                $send[] = $nestedData;
           }
       }else{
        $totalData = 0;
       }
        $totalData = $totalData;
       $totalFiltered = 10;
       $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send);
       echo json_encode($json_data);
   }

   public function get_shipping_popup_edit (){
        $party_id = $this->input->post('party_id');
        $party_type =  'customer';
        $state_id =  $this->input->post('billing_state');
        $shipping_id =  $this->input->post('shipping_id');
        $ship_add = $this->input->post('ship_add');
        $country  = $this->general_model->getRecords('*', 'countries', array('country_name' => 'india' ));
        $country_id = $country[0]->country_id;
        $list_data  = $this->common->shipping_address_list_popup($party_id,$party_type,$state_id,$country_id);
        $shipping_address_data = $this->general_model->getPageJoinRecords($list_data);
        $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
       $send = array();
       if(!empty($shipping_address_data)){
        foreach ($shipping_address_data as $com) {

            $primary_address = $com->primary_address;
            $id = $com->shipping_address_id;
            $nestedData['shipping_code'] = $com->shipping_code;
            $nestedData['shipping_address'] = $com->shipping_address;
            $nestedData['contact_person'] = $com->contact_person;
            $nestedData['gst'] = $com->shipping_gstin;
            $nestedData['state'] = $com->state_name;
            $state_id = $com->state_id;
            $country_id_shipping = $com->country_id;
            if($country_id_shipping != $country_id){
                $type = 'other';
            }else{
                $type = 'same';
            }
            if($primary_address == 'yes'){
                $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'. $id.'" checked/><input type="hidden" name="apply_country_id" value="'. $country_id_shipping.'" id="apply_country_id_'. $id.'" checked/><input type="hidden" name="state_id_suppuly" id="state_id_suppuly_'.$id.'" value="'.$state_id.'"><input type="hidden" name="country_type" id="country_type_'.$id.'" value="'.$type.'">';
            }else{
            $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'. $id.'"/><input type="hidden" name="apply_country_id" value="'. $country_id_shipping.'" id="apply_country_id_'. $id.'" /><input type="hidden" name="state_id_suppuly" id="state_id_suppuly_'.$id.'" value="'.$state_id.'"><input type="hidden" name="country_type" id="country_type_'.$id.'" value="'.$type.'">';
            }

            if($shipping_id == $id && $shipping_id != ""){

               $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'.$id.'" checked />'; 
            }else if($shipping_id != ""){
             $nestedData['action'] = '<input type="radio" name="apply" value="'. $id.'" id="apply_'.$id.'"/>';
            }
           
            $send[] = $nestedData;
            }
       }else{
        $totalData = 0;
       }

       
       $totalData = $totalData;
       $totalFiltered = 10;
       $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send);
        echo json_encode($json_data);
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

    public function getLedgersListAll(){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $ledgers = $this->ledger_model->GetLedgersName();
        /*$data_type = $_POST["type"];*/
        $data_type = $this->input->post("type");
        $option = '';
        $option1 = '';
        $cash_default = $this->config->item('advance_ledger');
        $default_payment_id = $cash_default['Cash_Payment'];
        $default_payment_name = $this->ledger_model->getDefaultLedgerId($default_payment_id);
        $cash_led = 'cash';
        if(!empty($default_payment_name)){
            $cash_led = $default_payment_name->ledger_name;
        }
        
        if($data_type == 'bank') {
            $option = '<option value="">Select Ledger*</option>';
            $option1 = '<option value="">Select Ledger*</option>';
            $bank_array_check = array();
            $ledgers_bank = $this->ledger_model->GetLedgersNameBank();
            
            if(!empty($ledgers_bank)){
                foreach ($ledgers_bank as $key => $value) {
                    //Start For closing balance
                    $report_ary = array('branch_id' => $branch_id,'from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'),'ledger_id' => $value['ledger_id']);
        
                    $final_ary = $this->report_model->getLedgerReportAry($report_ary);  
                    if($final_ary['closing_balance'] < 0){
                        $option_type = 'DR';
                    }else{
                        $option_type = 'CR';
                    }
                    $closing_balance = number_format(abs($final_ary['closing_balance']),2);
                    //End For closing balance
                    $option .= '<option value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.' '.$option_type.'">'.$value['ledger_name'].'</option>';
                    $bank_array_check[$value['ledger_id']] = $value['ledger_id'];
                }
            }
            if(!empty($ledgers)){
                foreach ($ledgers as $key => $value) {
                    //Start For closing balance
                    $report_ary = array('branch_id' => $branch_id,'from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'),'ledger_id' => $value['ledger_id']);
                   
                    $final_ary = $this->report_model->getLedgerReportAry($report_ary);  
                    if($final_ary['closing_balance'] < 0){
                        $option_type = 'DR';
                    }else{
                        $option_type = 'CR';
                    }
                    $closing_balance = number_format(abs($final_ary['closing_balance']),2);
                    //End For closing balance
                    if(!in_array($value['ledger_id'], $bank_array_check)){
                        $option1 .= '<option value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.' '.$option_type.'">'.$value['ledger_name'].'</option>';
                    }   
                }
            }   
        }
        if($data_type == 'cash') {
            $option1 = '<option value="">Select Ledger*</option>';
            if(!empty($ledgers)){
                foreach ($ledgers as $key => $value) {
                    //Start For closing balance
                    $report_ary = array('branch_id' => $branch_id,'from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'),'ledger_id' => $value['ledger_id']);
        
                    $final_ary = $this->report_model->getLedgerReportAry($report_ary);  
                    if($final_ary['closing_balance'] < 0){
                        $option_type = 'DR';
                    }else{
                        $option_type = 'CR';
                    }
                    $closing_balance = number_format(abs($final_ary['closing_balance']),2);
                    //End For closing balance
                    if(strtolower($value['ledger_name']) == strtolower($cash_led)){
                        $option = '<option selected value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.' '.$option_type.'">'.$value['ledger_name'].'</option>';
                    } else {
                        $option1 .= '<option value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.' '.$option_type.'">'.$value['ledger_name'].'</option>';
                    }
                }
            }
        }
        if($data_type == 'contra') {
            $option1 = '<option value="">Select Ledger*</option>';
            $ledgers_bank = $this->ledger_model->GetLedgersNameBank();
            if(!empty($ledgers)){
                foreach ($ledgers as $key => $value) {
                    //Start For closing balance
                    $report_ary = array('branch_id' => $branch_id,'from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'),'ledger_id' => $value['ledger_id']);
        
                    $final_ary = $this->report_model->getLedgerReportAry($report_ary);  
                    if($final_ary['closing_balance'] < 0){
                        $option_type = 'DR';
                    }else{
                        $option_type = 'CR';
                    }
                    $closing_balance = number_format(abs($final_ary['closing_balance']),2);
                    //End For closing balance
                    if(strtolower($value['ledger_name']) == strtolower($cash_led)){
                        $option = '<option selected value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.' '.$option_type.'">'.$value['ledger_name'].'</option>';
                    }
                }
            }
            if(!empty($ledgers_bank)){
                foreach ($ledgers_bank as $key => $value) {
                    //Start For closing balance
                    $report_ary = array('branch_id' => $branch_id,'from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'),'ledger_id' => $value['ledger_id']);
        
                    $final_ary = $this->report_model->getLedgerReportAry($report_ary);  
                    if($final_ary['closing_balance'] < 0){
                        $option_type = 'DR';
                    }else{
                        $option_type = 'CR';
                    }
                    $closing_balance = number_format(abs($final_ary['closing_balance']),2);
                    //End For closing balance
                    $option1 .= '<option value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.' '.$option_type.'">'.$value['ledger_name'].'</option>';
                }
            }
        }
        if($data_type == 'journal') {
            $option1 = '';
            $option = '<option value="">Select Ledger*</option>';
            if(!empty($ledgers)){
                foreach ($ledgers as $key => $value) {
                    //Start For closing balance
                    $report_ary = array('branch_id' => $branch_id,'from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'),'ledger_id' => $value['ledger_id']);
        
                    $final_ary = $this->report_model->getLedgerReportAry($report_ary);
                    if($final_ary['closing_balance'] < 0){
                        $option_type = 'DR';
                    }else{
                        $option_type = 'CR';
                    }  
                    $closing_balance = number_format(abs($final_ary['closing_balance']),2);
                    
                    $option .= '<option value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.' '.$option_type.'">'.$value['ledger_name'].'</option>';
                    
                }
            }
        }
        $resp['option'] = $option;
        $resp['option1'] = $option1;
 
        echo json_encode($resp);
    }

    function getLedgersList(){
        $ledgers = $this->ledger_model->GetLedgersName();
        $option = '<option value="">Select Ledger*</option>';
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        
        if(!empty($ledgers)){
            foreach ($ledgers as $key => $value) {

                //Start For closing balance
                $report_ary = array('branch_id' => $branch_id,'from_date' => date('d-m-Y'), 'to_date' => date('d-m-Y'),'ledger_id' => $value['ledger_id']);
    
                $final_ary = $this->report_model->getLedgerReportAry($report_ary);  

                $closing_balance = number_format(abs($final_ary['closing_balance']),2);
                //End For closing balance
                $option .= '<option value="'.$value['ledger_id'].'" closing_balance ="'.$closing_balance.'">'.$value['ledger_name'].'</option>';
            }
        }
        echo $option;
    }

    public function get_subdepartment() {
        $department_id = $this->input->post('department_id');
        

        $data['sub_department'] = $this->general_model->getRecords('*', 'sub_department', array(
            'delete_status'       => 0,
            'department_id'   => $department_id,
            'branch_id'           => $this->session->userdata('SESS_BRANCH_ID')));
       //echo $this->db->last_query();
        echo json_encode($data);
    }
}