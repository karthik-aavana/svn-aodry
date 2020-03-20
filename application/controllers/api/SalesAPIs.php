<?php
ini_set( 'display_errors', 0 );
require APPPATH . 'libraries/REST_Controller.php';
class SalesAPIs extends REST_Controller {
    
      /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public $branch_id = 0;
    public $user_id = 0;
    public $SESS_FINANCIAL_YEAR_TITLE = '';
    public $SESS_FINANCIAL_YEAR_ID = '';
    public $SESS_DEFAULT_CURRENCY_TEXT = '';
    public $SESS_DEFAULT_CURRENCY_CODE = '';
    public $SESS_DEFAULT_CURRENCY = '';
    public $SESS_DEFAULT_CURRENCY_SYMBOL = '';
    public $modules = array();
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array(
            'common_api',
            'ion_auth',
            'form_validation'));

        $this->load->model([
            'general_model' ,
            'product_model' ,
            'service_model' ,
            'Voucher_model' ,
            'ledger_model' ]);
        
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_get($id = 0)
    {
        if(!empty($id)){
            $data = $this->db->get_where("sales", ['sales_id' => $id])->row_array();
        }else{
            $data = $this->db->get("sales")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
    }
      
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_post(){

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        $post_req = json_decode(file_get_contents("php://input"),true); 
        //echo "<pre>";print_r($post_req);exit;
        $resp = array();
        try {
            $resp = $this->common_api->GetBranchDetails($post_req);
            $method = $post_req['Method'];
            if(@$resp['modules']){
                $this->modules = $resp['modules'];
                
                if(!empty($this->modules['modules'])){
                    if($method == 'CreateSalesInvoice'){
                        $data            = $this->common_api->get_default_country_state();
                        $sales_module_id = $this->config->item('sales_module');
                        $module_id       = $sales_module_id;
                        $privilege       = "add_privilege";
                        $section_modules = $this->common_api->get_section_modules($sales_module_id , $this->modules , $privilege);
                        $data            = array_merge($data , $section_modules);
                        $access_settings = $section_modules['access_settings'];
                        
                        /*$currency = $this->input->post('currency_id');*/

                        /* Create Invoice number */

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
                        $sales_data = $post_req['sales'];

                        if ($access_settings[0]->invoice_creation == "automatic"){
                            $primary_id      = "sales_id";
                            $table_name      = $this->config->item('sales_table');
                            $date_field_name = "sales_date";
                            $current_date    = date('Y-m-d');
                            $invoice_number  = $this->common_api->generate_invoice_number_api($this,$access_settings , $primary_id , $table_name , $date_field_name , $current_date);
                        } else {
                            $invoice_number = $sales_data['invoice_number'];
                        }

                        /* Get currency ID */
                        /*$currency_code = $sales_data['Currency_code'];*/
                        $customer = 0;
                        /*echo $this->session->userdata('SESS_BRANCH_ID');
                        echo $this->session->userdata('SESS_USER_ID');
                        exit();*/
                        
                        if(@$sales_data['Customer']){
                            $cust_detail = $this->getCustomer($sales_data['Customer']);
                            
                            if($cust_detail['flag']){
                                
                                $customer = $cust_detail['customer'];
                                $customer_id = $customer['customer_id'];
                                $billing_address = $customer['billing_address'];
                                $shipping_address = $customer['shipping_address'];
                                
                                $items = $sales_data['Items'];
                                $item_data = array();
                                $total_sub_total = $total_grand_total = $total_discount_amount = $total_tax_amount = $total_cess_amount = $total_taxable_amount = $total_tds_amount = $total_tcs_amount = $total_igst_amount = $total_cgst_amount = $total_sgst_amount = 0;

                                $Extra_charges = (@$sales_data['Extra_charges'] ? $sales_data['Extra_charges'] : array());
                                $is_gst = 0;
                                if ($access_settings[0]->tax_type == "gst"){
                                    if ($data['branch'][0]->branch_country_id == $billing_address['country_id']){
                                        $is_gst = 1;
                                    }else{
                                        if ($sales_data['Type_of_supply'] == "export_with_payment")
                                        $is_gst = 1;
                                    }
                                }
                                $Extra_charges = $this->getExtraChargesDetail($Extra_charges,$is_gst);
                                $total_other_amount = $Extra_charges['total_other_amount'];
                                $total_other_taxable_amount = $Extra_charges['total_other_taxable_amount'];
                                
                                
                                $item_id = array();
                                foreach ($items as $key => $value) {
                                    if(!@$value['aodry_product_id']){
                                       $item_id = $this->getItemId($value);
                                    }else{                                   
                                       $item_id['flag'] = 1;
                                       $item_id['product_id'] = $value['aodry_product_id'];
                                    }
                                    
                                    $value['item_type'] = strtolower($value['item_type']);
                                    
                                    if($item_id['flag']){
                                        $tax_detail = $this->getTaxDetail($value);
                                        $item_sub_total = $value['item_price'] * $value['item_quantity'];
                                        $item_discount_amount = 0;
                                        /* Item discount amount */
                                        if($tax_detail['discount_id'] > 0){
                                            $item_discount_amount = ($item_sub_total * (float)$value['item_discount_percentage']) / 100;
                                        }

                                        //$item_taxable_value = $item_sub_total - $item_discount_amount;
                                        // selling price with discount
                                        $item_value = $item_sub_total - $item_discount_amount;
                                       
                                        
                                        $item_tax_amount = 0;
                                        /*if($tax_detail['tax_id'] > 0){
                                            $item_tax_amount = ($item_taxable_value * (float)$value['item_tax_percentage']) / 100;
                                        }
                                        $item_tax_amount += $total_other_taxable_amount;*/
                                        $cal_tax = 0;
                                        $item_taxable_value = 0;
                                        $item_tax_amount = 0;
                                        if($tax_detail['tax_id'] > 0){
                                            // find the taxable value from selling price
                                            $cal_tax = 1 + ( (float)$value['item_tax_percentage'] / 100);
                                            $item_taxable_value = $item_value / $cal_tax;
                                            $item_taxable_value = round($item_taxable_value,2);

                                            $item_price = ($item_taxable_value / $value['item_quantity']);                                      
                                            $item_sub_total_item = $item_taxable_value;
                                            $item_tax_amount = ($item_taxable_value * (float)$value['item_tax_percentage']) / 100;
                                            $item_tax_amount = round($item_tax_amount,2);
                                        }
                                      
                                        $item_tds_amount = $item_tcs_amount = 0;
                                        if($tax_detail['tds_id'] > 0){
                                            if($value['item_type'] == 'product'){
                                                $item_tcs_amount = ($item_taxable_value * (float)$value['item_tds_percentage']) / 100;
                                            }else{
                                                $item_tds_amount = ($item_taxable_value * (float)$value['item_tds_percentage']) / 100;
                                            }
                                        }
                                       // $item_tax_amount += $item_tax_amount;
                                        
                                        $item_cess_amount = 0;
                                        /*if($tax_detail['cess_id'] > 0){
                                            $item_cess_amount = ($item_taxable_value * (float)$value['item_cess_percentage']) / 100;
                                        }*/
                                        $item_tax_percentage = (float)$value['item_tax_percentage'];

                                        $item_igst_amount = $item_cgst_amount = $item_sgst_amount = $item_cgst_percentage = $item_sgst_percentage = $item_igst_percentage = 0;
                                        
                                        if ($is_gst){
                                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;

                                            $cgst_amount_percentage = $tax_split_percentage;
                                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

                                            if ($data['branch'][0]->branch_country_id == $billing_address['country_id']){
                                                if ($data['branch'][0]->branch_state_id == $billing_address['state_id']) {
                                                    
                                                    /*$item_cgst_amount = ($item_tax_amount * $cgst_amount_percentage) / 100;
                                                    $item_sgst_amount = ($item_tax_amount * $sgst_amount_percentage) / 100;
                                                   
                                                    $item_cgst_percentage = ($item_tax_percentage * $cgst_amount_percentage) / 100;
                                                    $item_sgst_percentage = ($item_tax_percentage * $sgst_amount_percentage) / 100;*/

                                                    $item_cgst_amount = ($value['item_tax_amount'] / 2 );
                                                    $item_sgst_amount = ($value['item_tax_amount'] / 2 );
                                                   
                                                    $item_cgst_percentage = ($value['item_tax_percentage'] / 2);
                                                    $item_sgst_percentage = ($value['item_tax_percentage'] / 2);

                                                } else {
                                                    $item_igst_amount = $value['item_tax_amount'];
                                                    $item_igst_percentage = $value['item_tax_percentage'];
                                                }
                                                $is_gst = 1;
                                            } else {
                                                if ($sales_data['Type_of_supply'] == "export_with_payment"){
                                                    $item_igst_amount = $item_tax_amount;
                                                    $item_igst_percentage = $item_tax_percentage;
                                                    $is_gst = 1;
                                                }else{
                                                    $item_cess_amount = 0;
                                                    $item_tax_amount = 0;
                                                }
                                            }
                                        }
                                       
                                        /*$item_grand_total = ($item_taxable_value) + ($item_tax_amount) + $item_tcs_amount + ($item_cess_amount);
                                        $total_sub_total += $item_sub_total_item;
                                        $total_grand_total += $item_grand_total;
                                        $total_discount_amount += $item_discount_amount;
                                        $total_tax_amount += $item_tax_amount;
                                        $total_cess_amount += $item_cess_amount;
                                        $total_taxable_amount += $item_taxable_value;
                                        $total_tds_amount += $item_tds_amount;
                                        $total_tcs_amount += $item_tcs_amount;
                                        $total_igst_amount += $item_igst_amount;
                                        $total_cgst_amount += $item_cgst_amount;
                                        $total_sgst_amount += $item_sgst_amount;*/

                                        //ecom calculation
                                        $sales_item_sub_total = ($value['item_taxable_amount'] * $value['item_quantity']);

                                        $sales_item_taxable_value = ($value['item_taxable_amount'] * $value['item_quantity']) - $value['item_discount_amount'];



                                        $item_grand_total = ($item_taxable_value) + ($item_tax_amount) + $item_tcs_amount + ($item_cess_amount);
                                        $total_sub_total += $sales_item_sub_total;
                                        $total_grand_total += $item_grand_total;
                                        $total_discount_amount += $value['item_discount_amount'];
                                        $total_tax_amount += $value['item_tax_amount'];
                                        $total_cess_amount += $item_cess_amount;
                                        $total_taxable_amount += $sales_item_taxable_value;
                                        $total_tds_amount += $item_tds_amount;
                                        $total_tcs_amount += $item_tcs_amount;
                                        $total_igst_amount += $item_igst_amount;
                                        $total_cgst_amount += $item_cgst_amount;
                                        $total_sgst_amount += $item_sgst_amount;




                                        
                                        



                                        $item_data[] = array(
                                            "item_id"                    => $item_id['product_id'] ,
                                            "item_type"                  => $value['item_type'] ,
                                            "sales_item_quantity"        => $value['item_quantity'] ? (float)$value['item_quantity'] : 0 ,
                                            "sales_item_unit_price"      => $value['item_taxable_amount'] ? (float)$value['item_taxable_amount'] : 0 ,
                                            "sales_item_sub_total"       => $sales_item_sub_total ? (float) $sales_item_sub_total : 0 ,
                                            "sales_item_taxable_value"   => $sales_item_taxable_value ? (float) $sales_item_taxable_value : 0 ,
                                            "sales_item_discount_amount" => $value['item_discount_amount'] ? (float) $value['item_discount_amount'] : 0 ,
                                            "sales_item_discount_id"     => $tax_detail['discount_id'] ? (float)$tax_detail['discount_id'] : 0,
                                            "sales_item_tds_id"          => $tax_detail['tds_id'] ? (float)$tax_detail['tds_id'] : 0 ,
                                            "sales_item_tds_percentage"  => $value['item_tds_percentage'] ?(float)$value['item_tds_percentage'] : 0 ,
                                            "sales_item_tds_amount"      => ($item_tds_amount + $item_tcs_amount),
                                            "sales_item_grand_total"     => $value['item_grand_total'] ? (float)$value['item_grand_total'] : 0 ,
                                            "sales_item_tax_id"          => $tax_detail['tax_id'] ? (float)$tax_detail['tax_id'] : 0 ,
                                            "sales_item_tax_cess_id"          => 0,
                                            "sales_item_igst_percentage" => $item_igst_percentage ,
                                            "sales_item_igst_amount"     => $item_igst_amount ,
                                            "sales_item_cgst_percentage" => $item_cgst_percentage  ,
                                            "sales_item_cgst_amount"     => $item_cgst_amount ,
                                            "sales_item_sgst_percentage" => $item_sgst_percentage ,
                                            "sales_item_sgst_amount"     => $item_sgst_amount ,
                                            "sales_item_tax_percentage"  => $item_tax_percentage ? (float) $item_tax_percentage : 0 ,
                                            "sales_item_tax_cess_percentage"  =>$value['item_cess_percentage'] ?(float)$value['item_cess_percentage'] : 0 ,
                                            "sales_item_tax_amount"      => $value['item_tax_amount'] ? (float) $value['item_tax_amount'] : 0 ,
                                            'sales_item_tax_cess_amount' => 0 ,
                                            "sales_item_description"     => $value['item_description'] ? $value['item_description'] : "" ,
                                            "debit_note_quantity"        => 0
                                        );
                                    }

                                }

                               

                                if(!empty($item_data)){
                                    $Transport_details = (@$sales_data['Transport_details'] ? $sales_data['Transport_details'] : array());
                                    $Shipping_details = (@$sales_data['Shipping_details'] ? $sales_data['Shipping_details'] : array());
                                    $total_grand_total = $total_grand_total + $total_other_amount + $total_other_taxable_amount; 
                                    $customer_payable_amount = $total_grand_total;
                                    if($total_tds_amount > 0){
                                        $customer_payable_amount = bcsub($total_grand_total, $total_tds_amount);
                                    }

                                    $sales_main = array(
                                        'sales_date' => (@$sales_data['Invoice_date'] ? date('Y-m-d' ,strtotime($sales_data['Invoice_date'])) : date('Y-m-d')),
                                        "sales_invoice_number" => $invoice_number ,
                                        "sales_sub_total" => (float)$total_sub_total,
                                        "sales_grand_total" => $sales_data['Sales_grand_total'],
                                        "customer_payable_amount" => $sales_data['Sales_grand_total'],
                                        "sales_discount_amount"  => $total_discount_amount,
                                        "sales_tax_amount" => $total_tax_amount,
                                        "sales_tax_cess_amount" => 0,
                                        "sales_taxable_value" => (float)$total_taxable_amount ,
                                        "sales_tds_amount" => $total_tds_amount,
                                        "sales_tcs_amount" => $total_tcs_amount,
                                        "sales_igst_amount"  => $total_igst_amount,
                                        "sales_cgst_amount"  => $total_cgst_amount,
                                        "sales_sgst_amount"  => $total_sgst_amount,
                                        "from_account"       => 'customer' ,
                                        "to_account"         => 'sales' ,
                                        "sales_paid_amount"  => 0,
                                        "credit_note_amount" => 0,
                                        "debit_note_amount"  => 0,
                                        "ship_to_customer_id" => $customer['customer_id'],
                                        "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                        "sales_party_id"         => $customer['customer_id'],
                                        "sales_party_type"       => "customer" ,
                                        "sales_nature_of_supply" => (@$sales_data['Nature_of_supply'] ? $sales_data['Nature_of_supply'] : 'both'),
                                        "sales_order_number"     => (@$sales_data['Order_number'] ? $sales_data['Order_number'] : ''),
                                        "sales_type_of_supply"   => (@$sales_data['Type_of_supply'] ? $sales_data['Type_of_supply'] : 'regular'),
                                        "sales_gst_payable"      => (@$sales_data['Gst_payable'] ? $sales_data['Gst_payable'] : 'no'),
                                        "sales_billing_country_id" => $customer['billing_address']['country_id'] ,
                                        "sales_billing_state_id" => $customer['billing_address']['state_id'],
                                        "added_date"             => date('Y-m-d') ,
                                        "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                                        "branch_id"              => $this->session->userdata('SESS_BRANCH_ID'),
                                        "currency_id"            => $this->session->userdata('SESS_DEFAULT_CURRENCY'),
                                        "updated_date"           => "" ,
                                        "updated_user_id"        => "" ,
                                        "warehouse_id"           => "" ,
                                        "transporter_name"=> (@$Transport_details['name'] ? $Transport_details['name'] : ''),
                                        "transporter_gst_number" => (@$Transport_details['GST_number'] ? $Transport_details['GST_number'] : ''),
                                        "lr_no"                  => (@$Transport_details['LR_number'] ? $Transport_details['LR_number'] : '') ,
                                        "vehicle_no"             => (@$Transport_details['vehicle_number'] ? $Transport_details['vehicle_number'] : ''),
                                        "mode_of_shipment"       => (@$Shipping_details['mode_of_shipment'] ? $Shipping_details['mode_of_shipment'] : ''),
                                        "ship_by"   => (@$Shipping_details['ship_by'] ? $Shipping_details['ship_by'] : ''),
                                        "net_weight"          => (@$Shipping_details['net_weight'] ? $Shipping_details['net_weight'] : '') ,
                                        "gross_weight"        => (@$Shipping_details['gross_weight'] ? $Shipping_details['gross_weight'] : '') ,
                                        "origin"              => (@$Shipping_details['origin'] ? $Shipping_details['origin'] : '') ,
                                        "destination"         => (@$Shipping_details['destination'] ? $Shipping_details['destination'] : '') ,
                                        "shipping_type"       => (@$Shipping_details['shipping_type'] ? $Shipping_details['shipping_type'] : '') ,
                                        "shipping_type_place" => (@$Shipping_details['shipping_type_place'] ? $Shipping_details['shipping_type_place'] : '') ,
                                        "lead_time"           => (@$Shipping_details['lead_time'] ? $Shipping_details['lead_time'] : '') ,
                                        "shipping_address_id" => (@$customer['shipping_address']['shipping_id'] ? $customer['shipping_address']['shipping_id'] : 0),
                                        "warranty"            => (@$Shipping_details['warranty'] ? $Shipping_details['warranty'] : ''),
                                        "payment_mode"        => (@$Shipping_details['payment_mode'] ? $Shipping_details['payment_mode'] : '') ,
                                        "billing_address_id" => $customer['billing_address']['billing_id'] ,
                                        "freight_charge_amount"=> (@$Extra_charges['freight_charge_amount'] ? $Extra_charges['freight_charge_amount'] : 0) ,
                                        "freight_charge_tax_percentage" => (@$Extra_charges['freight_charge_percentage'] ? $Extra_charges['freight_charge_percentage'] : 0) ,
                                        "freight_charge_tax_amount" => (@$Extra_charges['freight_charge_tax_amount'] ? $Extra_charges['freight_charge_tax_amount'] : 0) ,
                                        "freight_charge_tax_id" => (@$Extra_charges['freight_charge_tax_id'] ? $Extra_charges['freight_charge_tax_id'] : 0) ,
                                        "total_freight_charge"  =>(@$Extra_charges['freight_charge_amount'] ? $Extra_charges['freight_charge_amount'] : 0) ,
                                        "insurance_charge_amount"  => (@$Extra_charges['insurance_charge_amount'] ? $Extra_charges['insurance_charge_amount'] : 0) ,
                                        "insurance_charge_tax_percentage"       => (@$Extra_charges['insurance_charge_percentage'] ? $Extra_charges['insurance_charge_percentage'] : 0) ,
                                        "insurance_charge_tax_amount" => (@$Extra_charges['insurance_charge_tax_amount'] ? $Extra_charges['insurance_charge_tax_amount'] : 0) ,
                                        "insurance_charge_tax_id" => (@$Extra_charges['insurance_charge_tax_id'] ? $Extra_charges['insurance_charge_tax_id'] : 0) ,
                                        "total_insurance_charge"                => (@$Extra_charges['insurance_charge_amount'] ? $Extra_charges['insurance_charge_amount'] : 0) ,
                                        "packing_charge_amount"                 => (@$Extra_charges['packing_charge_amount'] ? $Extra_charges['packing_charge_amount'] : 0) ,
                                        "packing_charge_tax_percentage"         => (@$Extra_charges['packing_charge_percentage'] ? $Extra_charges['packing_charge_percentage'] : 0) ,
                                        "packing_charge_tax_amount"             => (@$Extra_charges['packing_charge_tax_amount'] ? $Extra_charges['packing_charge_tax_amount'] : 0) ,
                                        "packing_charge_tax_id" => (@$Extra_charges['packing_charge_tax_id'] ? $Extra_charges['packing_charge_tax_id'] : 0) ,
                                        "total_packing_charge"                  => (@$Extra_charges['packing_charge_amount'] ? $Extra_charges['packing_charge_amount'] : 0) ,
                                        "incidental_charge_amount"              => (@$Extra_charges['incidental_charge_amount'] ? $Extra_charges['incidental_charge_amount'] : 0) ,
                                        "incidental_charge_tax_percentage"      => (@$Extra_charges['incidental_charge_percentage'] ? $Extra_charges['incidental_charge_percentage'] : 0) ,
                                        "incidental_charge_tax_amount"          => (@$Extra_charges['incidental_charge_tax_amount'] ? $Extra_charges['incidental_charge_tax_amount'] : 0) ,
                                        "incidental_charge_tax_id" => (@$Extra_charges['incidental_charge_tax_id'] ? $Extra_charges['incidental_charge_tax_id'] : 0) ,
                                        "total_incidental_charge"               => (@$Extra_charges['incidental_charge_amount'] ? $Extra_charges['incidental_charge_amount'] : 0) ,
                                        "inclusion_other_charge_amount"         => (@$Extra_charges['inclusion_other_charge_amount'] ? $Extra_charges['inclusion_other_charge_amount'] : 0) ,
                                        "inclusion_other_charge_tax_percentage" => (@$Extra_charges['inclusion_other_charge_percentage'] ? $Extra_charges['inclusion_other_charge_percentage'] : 0) ,
                                        "inclusion_other_charge_tax_amount"     => (@$Extra_charges['inclusion_other_charge_tax_amount'] ? $Extra_charges['inclusion_other_charge_tax_amount'] : 0) ,
                                        "inclusion_other_charge_tax_id" => (@$Extra_charges['inclusion_other_charge_tax_id'] ? $Extra_charges['inclusion_other_charge_tax_id'] : 0) ,
                                        "total_inclusion_other_charge"          => (@$Extra_charges['inclusion_other_charge_amount'] ? $Extra_charges['inclusion_other_charge_amount'] : 0) ,
                                        "exclusion_other_charge_amount"         => (@$Extra_charges['exclusion_other_charge_amount'] ? $Extra_charges['exclusion_other_charge_amount'] : 0) ,
                                        "exclusion_other_charge_tax_percentage" => (@$Extra_charges['exclusion_other_charge_percentage'] ? $Extra_charges['exclusion_other_charge_percentage'] : 0) ,
                                        "exclusion_other_charge_tax_amount"     =>(@$Extra_charges['exclusion_other_charge_tax_amount'] ? $Extra_charges['exclusion_other_charge_tax_amount'] : 0) ,
                                        "exclusion_other_charge_tax_id" => (@$Extra_charges['exclusion_other_charge_tax_id'] ? $Extra_charges['exclusion_other_charge_tax_id'] : 0) ,
                                        "total_exclusion_other_charge"          => (@$Extra_charges['exclusion_other_charge_amount'] ? $Extra_charges['exclusion_other_charge_amount'] : 0) ,
                                        "total_other_amount"         => (@$Extra_charges['total_other_amount'] ? $Extra_charges['total_other_amount'] : 0) ,
                                        "total_other_taxable_amount" => (@$Extra_charges['total_other_taxable_amount'] ? $Extra_charges['total_other_taxable_amount'] : 0) ,
                                        "round_off_amount" => 0,
                                        "added_by_api" => '1',
                                        "note1"      => (@$sales_data['Note1'] ? trim($sales_data['Note1']) : ''),
                                        "note2"      => (@$sales_data['Note2'] ? trim($sales_data['Note2']) : '')
                                    );



                                    $data_main   = array_map('trim' , $sales_main);
                                    
                                    $sales_table = $this->config->item('sales_table');
                                    $sales_id = $this->general_model->insertData($sales_table , $data_main);
                                    
                                    if ($sales_id) {
                                        $log_data              = array(
                                            'user_id'           => $this->session->userdata('SESS_USER_ID'),
                                            'table_id'          => $sales_id ,
                                            'table_name'        => $sales_table ,
                                            'financial_year_id' => $this->SESS_FINANCIAL_YEAR_ID,
                                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                                            'message'           => 'Sales Inserted');
                                        $data_main['sales_id'] = $sales_id;
                                        $log_table             = $this->config->item('log_table');
                                        $this->general_model->insertData($log_table , $log_data);
                                        
                                        $js_data               = $item_data;
                                        $item_table            = $this->config->item('sales_item_table');

                                        $js_data1 = array();
                                        foreach ($js_data as $key => $value) {
                                            $value['sales_id'] = $sales_id;
                                            $data_item  = array_map('trim' , $value);
                                            $js_data1[] = $data_item;
                                            
                                            if ($data_item['item_type'] == "product"){
                                                $product_data     = $this->common_api->product_field($this->session->userdata('SESS_BRANCH_ID') ,$data_item['item_id']);
                                                $product_result   = $this->general_model->getJoinRecords($product_data['string'] , $product_data['table'] , $product_data['where'],$product_data['join']);
                                                
                                                $product_quantity = ($product_result[0]->product_quantity - $value['sales_item_quantity']);
                                                $stockData        = array('product_quantity' => $product_quantity );
                                                $where            = array('product_id' => $value['item_id'] );
                                                $product_table    = $this->config->item('product_table');
                                                $this->general_model->updateData($product_table , $stockData , $where);
                                                // quantity history
                                                $history = array(
                                                    "item_id"          => $value['item_id'] ,
                                                    "item_type"        => 'product' ,
                                                    "reference_id"     => $sales_id ,
                                                    "reference_number" => $invoice_number ,
                                                    "reference_type"   => 'sales' ,
                                                    "quantity"         => $value['sales_item_quantity'] ,
                                                    "stock_type"       => 'indirect' ,
                                                    "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') ,
                                                    "added_date"       => date('Y-m-d') ,
                                                    "entry_date"       => date('Y-m-d') ,
                                                    "added_user_id"    => $this->session->userdata('SESS_USER_ID'));
                                                $this->general_model->insertData("quantity_history" , $history);
                                            }
                                        }
                                        //$this->general_model->insertData($item_table , $js_data1);
                                        $this->db->insert_batch($item_table, $js_data1);
                                        //sales lookup
                                        $look_up =  array(
                                                    'sales_id' => $sales_id,
                                                    'sales_invoice_number'  => $invoice_number,
                                                    'aodry_customer_id'  => $customer['customer_id'],
                                                    'added_date' => date('Y-m-d'),
                                                    'added_user_id' => $this->session->userdata('SESS_USER_ID'),
                                                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')
                                                );
                                        $this->db->insert('ecom_sales_sync', $look_up);
                                        if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){
                                            if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
                                                $action = "add";
                                                $branch_data = $data['branch'][0];
                                                $this->sales_voucher_entry($data_main , $js_data1 , $action , $data['branch'],$branch_data);
                                            }
                                        }

                                        $resp['status'] = 200;
                                        $resp['message'] = 'Sales Invoice addded successfully!';
                                        $resp['data'] = $look_up;
                                    }
                                   
                                    $logs = array('action_name' => 'CreateSalesInvoice','action_id'=> $sales_id,'status' => $resp['status'],'response' => $resp['message'],'user_id' => $this->session->userdata('SESS_USER_ID'),'branch_id' =>$this->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));
                                    $this->db->insert('ecom_sync_logs',$logs);
                                }else{
                                    $resp['status'] = 404;
                                    $resp['message'] = 'Invalid Items!';
                                }
                                /*echo "<pre>";
                                print_r($sales_main);
                                print_r($item_data);
                                exit();*/
                            }else{
                                $resp['status'] = 404;
                                $resp['message'] = $cust_detail['msg'];
                            }
                        

                        }else{
                            $resp['status'] = 404;
                            $resp['message'] = 'Customer detail not found.';
                        }
                    }elseif($method == 'PrintSalesInvoice'){
                        $order_details = $post_req['data'];
                        $order_id = $order_details['order_id'];
                        $sales_qry = $this->db->query("SELECT sales_id FROM `sales` WHERE branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND sales_order_number = '{$order_id}' ");
                        if($sales_qry->num_rows() > 0){
                            $sales_pdf = $sales_qry->result();
                            $sales_pdf_id = $sales_pdf[0]->sales_id;

                           // $id                = $this->encryption_url->decode($sales_pdf_id);
                            $id = $sales_pdf_id;
                            $data              = $this->common_api->get_default_country_state();
                            $sales_module_id   = $this->config->item('sales_module');
                            $data['module_id'] = $sales_module_id;
                            $modules           = $this->modules;
                            $privilege         = "view_privilege";
                            $data['privilege'] = "view_privilege";
                            $section_modules   = $this->common_api->get_section_modules($sales_module_id , $modules , $privilege);
                            $data              = array_merge($data , $section_modules);
                            $product_module_id             = $this->config->item('product_module');
                            $service_module_id             = $this->config->item('service_module');
                            $customer_module_id            = $this->config->item('customer_module');
                            $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
                            $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');
                            ob_start();
                            $html = ob_get_clean();
                            $html = utf8_encode($html);
                            
                            $branch_data = $data['branch'][0];
                            $data = $this->getSalesDetails($id, $branch_data, $data['branch']);
                            //echo "<pre>";print_r($data);exit();
                            
                            $paper_size  = 'a4';
                            $orientation = 'portrait';        
                           
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
                            
                            $html = $this->load->view('sales/pdf' , $data , true); 
                           //echo "<pre>";print_r($html);exit();
                            $resp['status'] = 200;
                            $resp['message'] = 'Sales Invoice Sent successfully!';
                            $resp['data'] = htmlentities($html, ENT_QUOTES);;
                        }else{
                            $resp['status'] = 404;
                            $resp['message'] = 'Sales Not Found!';
                        }  
                    }
                }else{
                    $resp['status'] = 404;
                    $resp['message'] = 'User access denied!';
                }            
            }
        }catch (Exception $e) {
            
        }

        // everything else results in a 404 Not Found
        if ($resp['status'] == 200) {
            $this->response($resp, REST_Controller::HTTP_OK);
        }else{
            $this->response($resp, REST_Controller::HTTP_NOT_FOUND);
        }
        exit();
    } 
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('sales', $input, array('sales_id'=>$id));
     
        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('sales', array('sales_id'=>$id));
       
        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);
    }

    public function getItemId($value){
        $resp = array();
        if(!empty($value)){
            $type = $value['item_type'];
            if($type == 'product' || $type == 'service'){

                if($value['item_main_category'][0] != ''){
                    $check_pro = $this->db->query("SELECT * FROM `category` WHERE LOWER(category_name) ='".strtolower(trim($value['item_main_category'][0]))."' AND branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND delete_status=0 AND category_type='{$type}' ");
                    if($check_pro->num_rows() > 0){
                        $category_id = $check_pro->result();
                        $category_id = $category_id[0]->category_id;
                    }else{
                        $category_code = $this->product_model->getMaxCategoryId();
                        $category_data = array(
                                                "category_code" => $category_code,
                                                "category_name" =>trim($value['item_main_category'][0]),
                                                "category_type" => $type,
                                                "added_date"    => date('Y-m-d'),
                                                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID')
                                            );
                        $category_id = $this->general_model->insertData('category', $category_data);
                    }

                    $sub_category_id = 0;
                    if($value['item_sub_category'] != ''){
                        $check_pro = $this->db->query("SELECT * FROM `sub_category` WHERE LOWER(sub_category_name) ='".strtolower(trim($value['item_sub_category']))."' AND branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND delete_status=0 AND category_id='{$category_id}'");
                        if($check_pro->num_rows() > 0){
                            $sub_category_id = $check_pro->result();
                            $sub_category_id = $sub_category_id[0]->sub_category_id;
                        }else{
                            $subcategory_code = $this->product_model->getMaxSubcategoryId();
                            $sub_category_data = array(
                                "category_id" => $category_id,
                                "sub_category_code" => $subcategory_code,
                                "sub_category_name" => trim($value['item_sub_category']),
                                "added_date" => date('Y-m-d'),
                                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
                            $sub_category_id = $this->general_model->insertData('sub_category', $sub_category_data);
                        }
                    }

                    $unit_qry = $this->db->query("SELECT id FROM uqc WHERE LOWER(uom)='".strtolower(trim($value['item_unit']))."'");
                    if($unit_qry->num_rows() > 0){
                        $unit_res = $unit_qry->result();
                        $unit_id = $unit_res[0]->id;
                    }else{
                        $uqc_data = array(
                                        "uom"           => trim($value['item_unit']),
                                        "description"   =>'',
                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                        "added_date"    => date('Y-m-d') 
                                    );
                        $unit_id = $this->general_model->insertData("uqc", $uqc_data);
                    }

                    if($type == 'product'){
                        $check_pro = $this->db->query("SELECT * FROM `products` WHERE LOWER(product_name) ='".strtolower(trim($value['item_name']))."' AND branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND delete_status=0 ");
                        if($check_pro->num_rows() > 0){
                            $check_res = $check_pro->result();
                            $product_id = $check_res[0]->product_id;
                            $resp['flag'] = 1;
                            $resp['product_id'] = $product_id;
                        }else{
                            $product_module_id = $this->config->item('product_module');
                            $data['module_id'] = $product_module_id;
                            $data['product_module_id'] = $product_module_id;
                            $modules           = $this->modules;
                            $privilege         = "view_privilege";
                            $data['privilege'] = "view_privilege";
                            $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);
                            $access_settings          = $section_modules['access_settings'];
                            $primary_id               = "product_id";
                            $table_name               = "products";
                            $date_field_name          = "added_date";
                            $current_date             = date('Y-m-d');
                            $invoice_number   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                            $product_data = array(
                                "product_code"           => $invoice_number,
                                "product_name"           => trim($value['item_name']),
                                "product_hsn_sac_code"   => trim($value['item_hsn_sac_code']),
                                "product_category_id"    => $category_id,
                                "product_subcategory_id" => $sub_category_id,
                                "product_quantity"       => 0,
                                "product_unit"           => trim($value['item_unit']),
                                "product_price"          => 0,
                                "product_tds_id"         => 0,
                                "product_tds_value"      => '',
                                "product_gst_id"         => 0,
                                "product_gst_value"      => '',
                                "product_details"        => trim($value['item_description']),
                                "is_assets"              => 'N',
                                "is_varients"            => 'N',
                                "product_unit_id"        => $unit_id,
                                "product_type"          => trim(strtolower($value['item_production_type'])),
                                "added_date"             => date('Y-m-d'),
                                "product_batch"         => $value['item_batch'],
                                "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                                "branch_id"              => $this->session->userdata('SESS_BRANCH_ID'));
                            //echo "<pre>";print_r($product_data);
                            $product_id = $this->general_model->insertData('products', $product_data);
                            $resp['flag'] = 1;
                            $resp['product_id'] = $product_id;
                            
                        }
                    }elseif ($type == 'service') {
                        $check_pro = $this->db->query("SELECT * FROM `services` WHERE LOWER(service_name) ='".strtolower(trim($value['item_name']))."' AND branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND delete_status=0 ");
                        if($check_pro->num_rows() > 0){
                            $check_res = $check_pro->result();
                            $product_id = $check_res[0]->service_id;
                            $resp['flag'] = 1;
                            $resp['product_id'] = $product_id;

                        }else{
                            $service_module_id          = $this->config->item('service_module');
                            $modules                    = $this->modules;
                            $privilege                  = "add_privilege";
                            $section_modules            = $this->get_section_modules($service_module_id, $modules, $privilege);
                            $primary_id               = "service_id";
                            $table_name               = "services";
                            $date_field_name          = "added_date";
                            $current_date             = date('Y-m-d');
                            $invoice_number   = $this->generate_invoice_number($section_modules['access_settings'], $primary_id, $table_name, $date_field_name, $current_date);

                            $title        = strtoupper(trim($this->input->post('service_name')));
                            $subgroup     = "Service";
                            $service_data = array(
                                    "service_code"           => $invoice_number,
                                    "service_name"           => trim($value['item_name']),
                                    "service_hsn_sac_code"   => trim($value['item_hsn_sac_code']),
                                    "service_category_id"    => $category_id,
                                    "service_subcategory_id" => $sub_category_id,
                                    "service_price"          => trim($value['item_unit']),
                                    "service_tds_id"         => 0,
                                    "service_tds_value"      => '',
                                    "service_gst_id"         => 0,
                                    "service_unit"           => $unit_id,
                                    "service_gst_value"      => '',
                                    "added_date"             => date('Y-m-d'),
                                    "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                                    "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
                                );

                            $service_id = $this->general_model->insertData("services", $service_data);
                            $resp['flag'] = 1;
                            $resp['product_id'] = $service_id;
                        }
                    }
                }else{
                    $resp['flag'] = 0;
                    $resp['message'] = 'Item category not found!';
                }
            }else{
                $resp['flag'] = 0;
                $resp['message'] = 'Invalid item type!';
            }
        }
        return $resp;
    }

    public function getTaxDetail($value){
        $resp = array();
        $resp['discount_id'] = 0;
        $resp['tds_id'] = 0;
        $resp['tax_id'] = 0;
        $resp['cess_id'] = 0;

        /* get discount detail */
        if($value['item_discount_percentage'] != ''){
            $disc_per = (float)$value['item_discount_percentage'];
            if($disc_per > 0){
                $disc_qry = $this->db->query("SELECT discount_id FROM `discount` WHERE branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND discount_value={$disc_per}");
                if($disc_qry->num_rows() > 0){
                    $dis_resp = $disc_qry->result();
                    $discount_id = $dis_resp[0]->discount_id;
                }else{
                    $discount_data = array(
                        "discount_name" => 'Discount',
                        "discount_value" => $disc_per,
                        "added_date" => date('Y-m-d'),
                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID')
                    );

                    $discount_id = $this->general_model->insertData("discount", $discount_data);
                }
                $resp['discount_id'] = $discount_id;
            }
        }

        if($value['item_tds_percentage'] != ''){
            $tds_per = (float)$value['item_tds_percentage'];
            if($tds_per > 0){
                $tax_name = 'TCS';
                if($value['item_type'] == 'service') $tax_name = 'TDS';

                $tds_qry = $this->db->query("SELECT tax_id FROM `tax` WHERE branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND tax_value={$tds_per} AND tax_name='{$tax_name}'");

                if($tds_qry->num_rows() > 0){
                    $tds_resp = $tds_qry->result();
                    $tds_id = $tds_resp[0]->tax_id;
                }else{
                    $tax_data = array(
                        "tax_name"        => trim($tax_name),
                        "tax_value"       => trim($tds_per),
                        "tax_description" => '',
                        "added_date"      => date('Y-m-d'),
                        "section_id" => 1,
                        "added_user_id"   => $this->session->userdata('SESS_USER_ID'),
                        "branch_id"       => $this->session->userdata('SESS_BRANCH_ID')
                    );

                    $tds_id = $this->general_model->insertData("tax", $tax_data);
                }
                $resp['tds_id'] = $tds_id;
            }
        }

        if($value['item_tax_percentage'] != ''){
            $tax_per = (float)$value['item_tax_percentage'];
            if($tax_per > 0){
                $tax_qry = $this->db->query("SELECT tax_id FROM `tax` WHERE branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND tax_value={$tax_per} AND tax_name='GST'");

                if($tax_qry->num_rows() > 0){
                    $tax_resp = $tax_qry->result();
                    $tax_id = $tax_resp[0]->tax_id;
                }else{
                    $tax_data = array(
                        "tax_name"        => 'GST',
                        "tax_value"       => trim($tax_per),
                        "tax_description" => '',
                        "added_date"      => date('Y-m-d'),
                        "section_id" => 1,
                        "added_user_id"   => $this->session->userdata('SESS_USER_ID'),
                        "branch_id"       => $this->session->userdata('SESS_BRANCH_ID')
                    );

                    $tax_id = $this->general_model->insertData("tax", $tax_data);
                }
                $resp['tax_id'] = $tax_id;
            }
        }

        if($value['item_cess_percentage'] != ''){
            $cess_per = (float)$value['item_cess_percentage'];
            if($cess_per > 0){
                $cess_qry = $this->db->query("SELECT tax_id FROM `tax` WHERE branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND tax_value={$cess_per} AND tax_name='CESS'");

                if($cess_qry->num_rows() > 0){
                    $cess_resp = $cess_qry->result();
                    $cess_id = $cess_resp[0]->tax_id;
                }else{
                    $tax_data = array(
                        "tax_name"        => 'CESS',
                        "tax_value"       => trim($cess_per),
                        "tax_description" => '',
                        "added_date"      => date('Y-m-d'),
                        "section_id" => 1,
                        "added_user_id"   => $this->session->userdata('SESS_USER_ID'),
                        "branch_id"       => $this->session->userdata('SESS_BRANCH_ID')
                    );

                    $cess_id = $this->general_model->insertData("tax", $tax_data);
                }

                $resp['cess_id'] = $cess_id;
            }
        }

        return $resp;
    }

    public function createGstTAX($tax_per){
        $tax_per = (float)$tax_per;
        $tax_id = 0;
        if($tax_per > 0){
            $tax_qry = $this->db->query("SELECT tax_id FROM `tax` WHERE branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND tax_value={$tax_per} AND tax_name='GST'");

            if($tax_qry->num_rows() > 0){
                $tax_resp = $tax_qry->result();
                $tax_id = $tax_resp[0]->tax_id;
            }else{
                $tax_data = array(
                    "tax_name"        => 'GST',
                    "tax_value"       => trim($tax_per),
                    "tax_description" => '',
                    "added_date"      => date('Y-m-d'),
                    "section_id" => 1,
                    "added_user_id"   => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"       => $this->session->userdata('SESS_BRANCH_ID')
                );

                $tax_id = $this->general_model->insertData("tax", $tax_data);
            }
        }
        return $tax_id;
    }

    public function getExtraChargesDetail($Extra_charges,$is_gst){
        $total_other_taxable_amount = $total_other_amount = 0;

        if(@$Extra_charges['freight_charge_amount'] && (float)$Extra_charges['freight_charge_amount'] > 0){
            $freight_charge_tax_amount  = ($Extra_charges['freight_charge_amount'] * $Extra_charges['freight_charge_percentage'] / 100);
            $total_other_amount += $Extra_charges['freight_charge_amount'];
            if($is_gst){
                $total_other_taxable_amount += $freight_charge_tax_amount;  
                $Extra_charges['freight_charge_tax_amount'] = $freight_charge_tax_amount;
                $Extra_charges['freight_charge_tax_id'] = $this->createGstTAX($Extra_charges['freight_charge_percentage']);
            }
        }

        if(@$Extra_charges['insurance_charge_amount'] && (float)$Extra_charges['insurance_charge_amount'] > 0){
            $insurance_charge_tax_amount  = ($Extra_charges['insurance_charge_amount'] * $Extra_charges['insurance_charge_percentage'] / 100);
            $total_other_amount += $Extra_charges['insurance_charge_amount'];
            if($is_gst){
                $total_other_taxable_amount += $insurance_charge_tax_amount;  
                $Extra_charges['insurance_charge_tax_amount'] = $insurance_charge_tax_amount;
                $Extra_charges['insurance_charge_tax_id'] = $this->createGstTAX($Extra_charges['insurance_charge_percentage']);
            }
        }

        if(@$Extra_charges['packing_charge_amount'] && (float)$Extra_charges['packing_charge_amount'] > 0){
            $packing_charge_tax_amount  = ($Extra_charges['packing_charge_amount'] * $Extra_charges['packing_charge_percentage'] / 100);
            $total_other_amount += $Extra_charges['packing_charge_amount'];
            if($is_gst){
                $total_other_taxable_amount += $packing_charge_tax_amount;
                $Extra_charges['packing_charge_tax_amount'] = $packing_charge_tax_amount;  
                $Extra_charges['packing_charge_tax_id'] = $this->createGstTAX($Extra_charges['packing_charge_percentage']);
            }
        }

        if(@$Extra_charges['incidental_charge_amount'] && (float)$Extra_charges['incidental_charge_amount'] > 0){
            $incidental_charge_tax_amount  = ($Extra_charges['incidental_charge_amount'] * $Extra_charges['incidental_charge_percentage'] / 100);
            $total_other_amount += $Extra_charges['incidental_charge_amount'];
            if($is_gst){
                $total_other_taxable_amount += $incidental_charge_tax_amount; 
                $Extra_charges['incidental_charge_tax_amount'] = $incidental_charge_tax_amount; 
                $Extra_charges['incidental_charge_tax_id'] = $this->createGstTAX($Extra_charges['incidental_charge_percentage']);
            }
        }

        if(@$Extra_charges['inclusion_other_charge_amount'] && (float)$Extra_charges['inclusion_other_charge_amount'] > 0){
            $inclusion_other_charge_tax_amount  = ($Extra_charges['inclusion_other_charge_amount'] * $Extra_charges['inclusion_other_charge_percentage'] / 100);
            $total_other_amount += $Extra_charges['inclusion_other_charge_amount'];
            if($is_gst){
                $total_other_taxable_amount += $inclusion_other_charge_tax_amount; 
                $Extra_charges['inclusion_other_charge_tax_amount'] = $inclusion_other_charge_tax_amount; 
                $Extra_charges['inclusion_charge_tax_id'] = $this->createGstTAX($Extra_charges['inclusion_charge_percentage']);
            }
        }

        if(@$Extra_charges['exclusion_other_charge_amount'] && (float)$Extra_charges['exclusion_other_charge_amount'] > 0){
            $exclusion_other_charge_tax_amount  = ($Extra_charges['exclusion_other_charge_amount'] * $Extra_charges['exclusion_other_charge_percentage'] / 100);
            $total_other_amount -= $Extra_charges['exclusion_other_charge_amount'];
            if($is_gst){
                $total_other_taxable_amount -= $exclusion_other_charge_tax_amount; 
                $Extra_charges['exclusion_other_charge_tax_amount'] = $exclusion_other_charge_tax_amount;
                $Extra_charges['exclusion_other_charge_tax_id'] = $this->createGstTAX($Extra_charges['exclusion_other_charge_percentage']);
            }
        }

        $Extra_charges['total_other_amount'] = $total_other_amount;
        $Extra_charges['total_other_taxable_amount'] = $total_other_taxable_amount;
        return $Extra_charges;
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
            $default_discount_id = $sales_ledger['Discount'];
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
            $discount_ledger_id = $this->ledger_model->getGroupLedgerId($discount_ary);
            /*$discount_ledger_id = $this->ledger_model->getGroupLedgerId(array(
                                                    'ledger_name' => 'Trade Discount Allowed',
                                                    'subgrp_1' => '',
                                                    'subgrp_2' => '',
                                                    'main_grp' => 'Direct Expenses',
                                                    'amount' =>  0
                                                ));*/
            $ledgers['discount_ledger_id'] = $discount_ledger_id;
            foreach ($js_data as $key => $value){
                $discount_sum = bcadd($discount_sum , $value['sales_item_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
            }
            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $converted_voucher_amount = $discount_sum;
            } else {
                $converted_voucher_amount = 0;
            }
            $vouchers_new[] = array(
                                "ledger_from"              => $discount_ledger_id,
                                "ledger_to"                => $sales_ledger_id,
                                "sales_voucher_id"         => '' ,
                                "voucher_amount"           => $discount_sum ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $discount_sum ,
                                "cr_amount"                => '',
                                'ledger_id'                => $discount_ledger_id
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

    public function sales_voucher_entry($data_main , $js_data , $action , $branch,$branch_data){

        $sales_voucher_module_id = $this->config->item('sales_voucher_module');
        $module_id               = $sales_voucher_module_id;
        $modules                 = $this->common_api->get_modules($branch_data);
        $privilege               = "add_privilege";
        $section_modules         = $this->common_api->get_section_modules($sales_voucher_module_id , $modules , $privilege);
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

            $voucher_number  =  $this->common_api->generate_invoice_number_api($this, $access_settings, $primary_id, $table_name, $date_field_name, $current_date);
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
            $this->general_model->addVouchers($table, $reference_key, $reference_table, $headers, $vouchers);
            
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

    public function getCustomer($customer){
        $flag = 1;
        $resp = array();
        if(@$customer['email']){
            $is_customer = 0;
            $is_address = $is_bill_address = 0;
            $txt_shipping_code = '';

            if(@$customer['shipping_address']){
                $shipping_address = $customer['shipping_address'];
            }else{
                $flag = 0;
                $resp['flag'] = 0;
                $resp['msg'] = 'Shipping address not found!';
            }

            if(@$customer['billing_address']){
                $billing_address = $customer['billing_address'];
            }else{
                $flag = 0;
                $resp['flag'] = 0;
                $resp['msg'] = 'Billing address not found!';
            }

            if($flag){
                $qry = $this->db->query("SELECT c.*,s.* FROM `customer` c LEFT JOIN shipping_address s ON c.customer_id=s.shipping_party_id WHERE c.branch_id='".$this->session->userdata('SESS_BRANCH_ID')."' AND s.shipping_party_type='customer' AND LOWER(customer_email) = '".strtolower($customer['email'])."'");
                $num_shipp_address= $qry->num_rows();

                if($num_shipp_address > 0){
                    $is_customer = 1;
                    $addresses = $qry->result_array();
                   
                    foreach ($addresses as $key => $value) {
                        $txt_shipping_code = $value['customer_code'].'-'.(count($addresses) + 1);
                        $resp['customer']['customer_id'] = $value['customer_id'];
                        $customer_id = $value['customer_id'];

                        if(strtolower(preg_replace('/\s+/', '', str_replace(' ', '', $value['shipping_address']))) == strtolower(preg_replace('/\s+/', '',str_replace(' ', '', $shipping_address['address1']))) && $value['address_pin_code'] == $shipping_address['zipcode']){
                            $value['shipping_id'] = $value['shipping_address_id'];
                            $is_address = 1;
                            $resp['flag'] = 1;
                            $resp['customer']['shipping_address'] = $value;
                            $shipping_address_data = $value;
                        }

                        if(strtolower(preg_replace('/\s+/', '',str_replace(' ', '', $value['shipping_address']))) == strtolower(preg_replace('/\s+/', '',str_replace(' ', '', $billing_address['address1']))) && $value['address_pin_code'] == $billing_address['zipcode']){
                            $is_bill_address = 1;
                            $resp['flag'] = 1;
                            $value['billing_id'] = $value['shipping_address_id'];
                            $resp['customer']['billing_address'] = $value;
                            $billing_address_data = $value;
                        }
                    }
                }
                
                if($is_customer == 0 || $is_address == 0 || $is_bill_address == 0){
                    
                    $customer_module_id = $this->config->item('customer_module');
                    $data['module_id'] = $customer_module_id;
                    $privilege = "add_privilege";
                    $data['privilege'] = "add_privilege";

                    $section_modules = $this->common_api->get_section_modules($customer_module_id, $this->modules, $privilege);
                    
                    $access_settings = $section_modules['access_settings'];
                    $primary_id = "customer_id";
                    $table_name = "customer";
                    $date_field_name = "added_date";
                    $current_date = date('Y-m-d');

                    /* Get country id */
                    $country_qry = $this->db->query("SELECT country_id FROM `countries` WHERE country_shortname='{$billing_address['country']}'");
                    $country_res = $country_qry->result();
                    if(empty($country_res)){
                        $flag = 0;
                        $resp['flag'] = 0;
                        $resp['msg'] = 'Country not found';
                    }

                    if($flag){
                        $country_id = $country_res[0]->country_id;
                        $ship_country_id = $country_id;

                        if(strtolower(trim($shipping_address['country'])) != strtolower(trim($billing_address['country']))){
                            $country_qry = $this->db->query("SELECT country_id FROM `countries` WHERE country_shortname='{$shipping_address['country']}'");
                            $country_res = $country_qry->result();
                            if(empty($country_res)){
                                $flag = 0;
                                $resp['flag'] = 0;
                                $resp['msg'] = 'Shipping Country not found';
                            }else{
                                $ship_country_id = $country_res[0]->country_id;
                            }
                        }

                        if($flag){
                            /* Get state id */
                            $state_qry = $this->db->query("SELECT state_id FROM `states` WHERE state_short_code='{$billing_address['state']}' AND country_id='{$country_id}'");
                            $state_res = $state_qry->result();
                            if(empty($state_res)){
                                $flag = 0;
                                $resp['flag'] = 0;
                                $resp['msg'] = 'State not found';
                            }
                        }

                        if($flag){
                            $state_id = $state_res[0]->state_id;
                            $ship_state_id = $state_id;
                            if(strtolower(trim($shipping_address['state'])) != strtolower(trim($billing_address['state']))){
                                $ship_state_qry = $this->db->query("SELECT state_id FROM `states` WHERE state_short_code='{$billing_address['state']}' AND country_id='{$ship_country_id}'");
                                $ship_state_res = $ship_state_qry->result();
                                if(empty($ship_state_res)){
                                    $flag = 0;
                                    $resp['flag'] = 0;
                                    $resp['msg'] = 'Shipping state not found';
                                }else{
                                    $ship_state_id = $ship_state_res[0]->state_id;
                                }
                            }

                            if($flag){
                                /* Get city id */
                                $cities_qry = $this->db->query("SELECT city_id FROM `cities` WHERE LOWER(city_name)='".strtolower($billing_address['city'])."' AND state_id='{$state_id}'");
                                if($cities_qry->num_rows() > 0){
                                    $cities_resp = $cities_qry->result();
                                    $city_id = $cities_resp[0]->city_id;

                                    $ship_city_id = $city_id;
                                    if(strtolower(trim($shipping_address['city'])) != strtolower(trim($billing_address['city']))){
                                        $ship_cities_qry = $this->db->query("SELECT city_id FROM `cities` WHERE LOWER(city_name)='".strtolower($billing_address['city'])."' AND state_id='{$state_id}'");
                                        $ship_city_res = $ship_cities_qry->result();
                                        if(empty($ship_city_res)){
                                            $flag = 0;
                                            $resp['flag'] = 0;
                                            $resp['msg'] = 'Shipping city not found';
                                        }else{
                                            $ship_city_id = $ship_city_res[0]->city_id;
                                        }
                                    }
                                }else{
                                    $flag = 0;
                                    $resp['flag'] = 0;
                                    $resp['msg'] = 'City not found';
                                }
                            }

                            if($flag){
                                if(!$is_customer){
                                    $sales_ledger = $this->config->item('sales_ledger');
                                    $default_customer_id = $sales_ledger['CUSTOMER'];
                                    $customer_ledger_name = $this->ledger_model->getDefaultLedgerId($default_customer_id);
                                    
                                    $customer_ary = array(
                                                    'ledger_name' => $customer['name'],
                                                    'second_grp' => '',
                                                    'primary_grp' => 'Sundry Debtors',
                                                    'main_grp' => 'Current Assets',
                                                    'default_ledger_id' => 0,
                                                    'default_value' => $customer['name'],
                                                    'amount' => 0
                                                );
                                    if(!empty($customer_ledger_name)){
                                        $customer_ledger = $customer_ledger_name->ledger_name;
                                        /*$customer_ledger = str_ireplace('{{SECTION}}',$section_name , $customer_ledger);*/
                                        $customer_ledger = str_ireplace('{{X}}',$customer['name'], $customer_ledger);
                                        $customer_ary['ledger_name'] = $customer_ledger;
                                        $customer_ary['primary_grp'] = $customer_ledger_name->sub_group_1;
                                        $customer_ary['second_grp'] = $customer_ledger_name->sub_group_2;
                                        $customer_ary['main_grp'] = $customer_ledger_name->main_group;
                                        $customer_ary['default_ledger_id'] = $customer_ledger_name->ledger_id;
                                    }
                                    $customer_ledger_id = $this->ledger_model->getGroupLedgerId($customer_ary);
                                    
                                    /* Get invoice number */
                                    $invoice_number = $this->common_api->generate_invoice_number_api($this,$access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                    $reference_number = $this->common_api->generate_reference_number_api($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

                                    $customer_data = array(
                                        "customer_name" => $customer['name'],
                                        "customer_code" => $invoice_number,
                                        "reference_number" => $reference_number,
                                        "reference_type" => 'customer',
                                        "customer_type" => $customer['type'],
                                        "customer_email" => $customer['email'],
                                        "customer_address" => $customer['shipping_address']['address1'],
                                        "customer_country_id" => $country_id,
                                        "customer_state_id" => $state_id,
                                        "customer_city_id" => $city_id,
                                        "contact_person" => $shipping_address['contact_person'],
                                        "ledger_id" => $customer_ledger_id, //$ledger_id,
                                        "customer_gstin_number" => $customer['GST_number'],
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        "updated_date" => "",
                                        "updated_user_id" => "",
                                        "customer_pan_number" => $customer['PAN'],
                                        "customer_postal_code" => $shipping_address['zipcode']);

                                    $table = "customer";
                                    $shipping_address_id = 0;
                                    $customer_id = $this->general_model->insertData($table, $customer_data);
                                    $txt_shipping_code = $invoice_number;
                                    $resp['customer']['customer_id'] = $customer_id;
                                }
                                $sh = 1;
                                if (!$is_address) {
                                    $txt_shipping_code = $invoice_number . "-".$sh;
                                    $sh++;
                                    $shipping_address_data = array(
                                        "shipping_address" => $customer['shipping_address']['address1'],
                                        "primary_address" => 'yes',
                                        "shipping_party_id" => $customer_id,
                                        "shipping_party_type" => 'customer',
                                        "contact_person" => $shipping_address['contact_person'],
                                        "department" => '',
                                        "email" => $customer['email'],
                                        "shipping_gstin" => $customer['GST_number'],
                                        "contact_number" => $shipping_address['contact_person'],
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" =>$this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        "country_id" => $ship_country_id,
                                        "state_id" => $ship_state_id,
                                        "city_id" => $ship_city_id,
                                        "shipping_code" => $txt_shipping_code,
                                        "address_pin_code" => $shipping_address['zipcode'],
                                        "updated_date" => "",
                                        "updated_user_id" => ""
                                    );
                                    $table = "shipping_address";
                                    $shipping_address_id = $this->general_model->insertData($table, $shipping_address_data);
                                    $shipping_address_data['shipping_id'] = $shipping_address_id;

                                    $table = "log";
                                    $log_data = array(
                                        'user_id' => $this->session->userdata('SESS_USER_ID'),
                                        'table_id' => $customer_id,
                                        'table_name' => 'customer',
                                        'financial_year_id' => $this->SESS_FINANCIAL_YEAR_ID,
                                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                        'message' => 'Customer/Shipping Address Inserted'
                                    );

                                    $this->general_model->insertData($table, $log_data);
                                }

                                if (!$is_bill_address) {
                                    $txt_shipping_code = $invoice_number . "-".$sh;
                                    $billing_address_data = array(
                                        "shipping_address" => $customer['billing_address']['address1'],
                                        "primary_address" => 'no',
                                        "shipping_party_id" => $customer_id,
                                        "shipping_party_type" => 'customer',
                                        "contact_person" => $billing_address['contact_person'],
                                        "department" => '',
                                        "email" => $customer['email'],
                                        "shipping_gstin" => $customer['GST_number'],
                                        "contact_number" => $billing_address['contact_person'],
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" =>$this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        "country_id" => $country_id,
                                        "state_id" => $state_id,
                                        "city_id" => $city_id,
                                        "shipping_code" => $txt_shipping_code,
                                        "address_pin_code" => $billing_address['zipcode'],
                                        "updated_date" => "",
                                        "updated_user_id" => ""
                                    );

                                    $table = "shipping_address";
                                    $billing_address_id = $this->general_model->insertData($table, $billing_address_data);
                                    $table = "log";
                                    $log_data = array(
                                        'user_id' => $this->session->userdata('SESS_USER_ID'),
                                        'table_id' => $customer_id,
                                        'table_name' => 'customer',
                                        'financial_year_id' => $this->SESS_FINANCIAL_YEAR_ID,
                                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                        'message' => 'Customer/Shipping Address Inserted'
                                    );

                                    $this->general_model->insertData($table, $log_data);
                                    $billing_address_data['billing_id'] = $billing_address_id;
                                }
                                $resp['flag'] = 1;
                                $resp['customer']['shipping_address'] = $shipping_address_data;
                                $resp['customer']['billing_address'] = $billing_address_data;
                            }
                        }
                    }
                }
            }
        }else{
            $flag = 0;
            $resp['flag'] = 0;
            $resp['msg'] = 'Email address not found';
        }
        return $resp;
    }

    public function getSalesDetails($id, $branch_data, $branch){
        
        $branch_data                 = $branch_data;
        $data['branch']              = $branch;
        $sales_module_id             = $this->config->item('sales_module');
        $data['email_module_id']     = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id']       = $sales_module_id;
        $data['sales_module_id'] = $sales_module_id;
        //$modules                 = $this->modules;
        $modules                 = $this->common_api->get_modules($branch_data);
        $privilege               = "view_privilege";
        $data['privilege']       = "view_privilege";
        $data['privilege']       = $privilege;
        $section_modules         = $this->common_api->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                    = array_merge($data , $section_modules);
        
        $sales_data = $this->common->sales_list_field1($id);
        $data['currency'] = $this->currency_call();
        $data['data'] = $this->general_model->getJoinRecords($sales_data['string'] , $sales_data['table'] , $sales_data['where'] , $sales_data['join']);

        $this->db->select('shipping_address,country_name');
        $this->db->from('shipping_address s');
        $this->db->join('countries c','s.country_id=c.country_id','left');
        $this->db->where('shipping_address_id',$data['data'][0]->billing_address_id);
        $billing_address = $this->db->get();
        $data['billing_address'] = $billing_address->result();
        /*print_r($data['billing_address']);exit();*/
        $item_types = $this->general_model->getRecords('item_type,sales_item_tds_percentage,sales_item_tds_id,sales_item_description' , 'sales_item' , array(
            'sales_id' => $id ));

        $service     = 0;
        $product     = 0;
        $description = 0;
        /*$sales_item_tax_amount = $sales_item_tax_amount + $item_data['freight_charge_tax_amount'] + $item_data['insurance_charge_tax_amount'] + $item_data['packing_charge_tax_amount'] + $item_data['incidental_charge_tax_amount'] + $item_data['inclusion_other_charge_tax_amount'] - $item_data['exclusion_other_charge_tax_amount'];*/
     
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
            if($value->sales_item_tds_percentage > 0 && $value->sales_item_tds_id != 0){
                $value->tds_module_type = $this->getTDSModule($value->sales_item_tds_id);
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
            $product_items       = $this->common->sales_items_product_list_field($id);
            $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        }

        $data['items'] = array_merge($sales_product_items , $sales_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $schemediscountExist = 0;
        $descriptionExist = 0;
        $cess_exist = 0;
      
        if ($data['data'][0]->sales_tax_amount > 0 && $data['data'][0]->sales_igst_amount > 0 && ($data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount > 0 && ($data['data'][0]->sales_cgst_amount > 0 || $data['data'][0]->sales_sgst_amount > 0) && $data['data'][0]->sales_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
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

        if ($data['data'][0]->sales_tcs_amount > 0)
        {
            /* Discount $data['data'][0]->sales_tds_amount > 0 || */
            $tdsExist = 1;
        }

        foreach ($data['items'] as $key => $value) {
            if ($value->sales_item_discount_amount > 0){
                    /* Discount */
                    $discountExist = 1;
                }

                if ($value->sales_item_scheme_discount_amount > 0){
                    /* Scheme Discount */
                    $schemediscountExist = 1;
                }

        }
        

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        if($data['data'][0]->sales_tax_cess_amount > 0){
            $cess_exist = 1;
        }
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
        }
        $data['nature_of_supply'] = $nature_of_supply;
        $note_data         = $this->template_note($data['data'][0]->note1 , $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->sales_billing_state_id);
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['cess_exist']        = $cess_exist;
        $data['tax_exist']         = $taxExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['schemediscountExist']  = $schemediscountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_id']     = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $customer_currency_code = $this->getCurrencyInfo($data['data'][0]->currency_id);
        $customer_curr_code = '';
        if(!empty($customer_currency_code))
        $customer_curr_code     = $customer_currency_code[0]->currency_code;
        $data['cust_currency_code']     = $customer_curr_code;
        $hsn_data = $this->common->hsn_list_item_field1($id);
        $data['hsn'] = $this->general_model->getPageJoinRecords($hsn_data);

        /* delivery challan request */
        $data['is_only_view'] = '0';
        if($this->input->post('is_only_view')){
            $data['is_only_view'] = '1';
            $data['data'][0]->sales_invoice_number = $this->input->post('invoice_number');
            $data['data'][0]->sales_date = $this->input->post('invoice_date');
        }
        return $data;
    }

    public function currency_call()
    {
        $currency_data = $this->common->currency_field();
        $data              = $this->general_model->getJoinRecords($currency_data['string'], $currency_data['table'], $currency_data['where'], $currency_data['join'], $currency_data['order']);
       /* $data          = $this->general_model->getRecords($currency_data['string'], $currency_data['table'], $currency_data['where']);*/
        return $data;
    }

    public function getTDSModule($id){
        $this->db->select('tax_name');
        $this->db->where('tax_id',$id);
        $qry = $this->db->get('tax');
        $result = $qry->result();
        $tax_type = '';
        if(!empty($result)){
            $tax_type = $result[0]->tax_name;
        }
        return $tax_type;
    }

    public function template_note($left_note, $right_note)
    {
        $val              = str_replace(array("\r\n#", "\\r\\n#"), " #", $left_note);
        $val              = str_replace(array("\r\n", "\\r\\n"), " <br>", $val);
        $note1            = $val;
        $template         = '';
        $j                = 0;
        $space            = 0;
        $text             = array();
        $template_content = array();
        $text[$j]         = '';

        for ($i = 0; $i < strlen($note1); $i++)
        {

            if ($note1[$i] == '#')
            {
                $space = 1;
            }

            if ($space == 1)
            {

                if ($note1[$i] != ' ')
                {
                    $template .= $note1[$i];
                }
                else
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                    $text[$j] .= $note1[$i];
                }

                if ($i == strlen($note1) - 1)
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                }

            }
            else
            {
                $text[$j] .= $note1[$i];
            }

        }
        $data_note1       = $text;
        $data_template1   = $template_content;
        $val              = str_replace(array("\r\n#", "\\r\\n#"), " #", $right_note);
        $val              = str_replace(array("\r\n", "\\r\\n"), " <br>", $val);
        $note2            = $val;
        $template         = '';
        $j                = 0;
        $space            = 0;
        $text             = array();
        $template_content = array();
        $text[$j]         = '';

        for ($i = 0; $i < strlen($note2); $i++)
        {

            if ($note2[$i] == '#')
            {
                $space = 1;
            }

            if ($space == 1)
            {

                if ($note2[$i] != ' ')
                {
                    $template .= $note2[$i];
                }
                else
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                    $text[$j] .= $note2[$i];
                }

                if ($i == strlen($note2) - 1)
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                }

            }
            else
            {
                $text[$j] .= $note2[$i];
            }

        }
        $data_note2     = $text;
        $data_template2 = $template_content;
        $note_data      = array('note1' => $data_note1, 'template1' => $data_template1,
            'note2'                         => $data_note2, 'template2' => $data_template2);
        return $note_data;
    }

    public function getBranchCurrencyCode()
    {
        $data          = $this->general_model->getRecords('*', 'currency', array('currency_id' => $this->session->userdata('SESS_DEFAULT_CURRENCY')));
        return $data;
        
    }

    public function getCurrencyInfo($id)
    {
        $data          = $this->general_model->getRecords('*', 'currency', array('currency_id' => $id));
        return $data;
        
    }
}