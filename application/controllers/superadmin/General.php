<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class General extends MY_Controller
{
    function __construct() {
    	parent::__construct();
    	// $this->load->model('log_model');
        $this->load->model('general_model');
        //$this->load->library('form_validation');

    }

    public function get_state($country_id){    

        /* State Details */
        $state_data=$this->common->state_field($country_id);  
 
        $data= $this->general_model->getRecords($state_data['string'],$state_data['table'],$state_data['where']);
        /* State Details */

        echo json_encode($data);
    }
      public function get_state_code($state_id){    

        /* State Details */
        $state_data=$this->common->state_code_field($state_id);  
 
        $data= $this->general_model->getRecords($state_data['string'],$state_data['table'],$state_data['where']);
        /* State Details */

        echo json_encode($data);
    }
    public function get_city($state_id){    

        /* City Details */
        $city_data=$this->common->city_field($state_id);    
        $data= $this->general_model->getRecords($city_data['string'],$city_data['table'],$city_data['where']);
        /* City Details */
        echo json_encode($data);
    }
    public function get_branch(){

      if(!empty($this->input->post('firm_id')) && $this->input->post('firm_id') !="")
      {
        $firm_id=$this->input->post('firm_id');
      }
      else
      {
        $firm_id="";
      }
        /* Branch Details */
        $branch_data=$this->common->branch_field($firm_id);    
        $data= $this->general_model->getJoinRecords($branch_data['string'],$branch_data['table'],$branch_data['where'],$branch_data['join'],$branch_data['order']);
        /* Branch Details */

        echo json_encode($data);
    }

    public function get_state_data($state_id){    

        /* State Details */
        $state_data=$this->common->state_field($country_id="",$state_id);    
        $data= $this->general_model->getRecords($state_data['string'],$state_data['table'],$state_data['where']);
        /* State Details */
        echo json_encode($data);
    }

    /* get reference no */

    public function generate_date_reference() {
        $date = $this->input->post('date');
        $module_id = $this->input->post('module_id');
        $privilege = $this->input->post('privilege');
if($date=="current")
{
  $date=date('Y-m-d');
}

$modules=$this->modules;
$privilege=$privilege;
$section_modules=$this->get_section_modules($module_id,$modules,$privilege);

 $access_settings=$section_modules['settings'];

        if($module_id==$this->config->item('sales_module'))
        {
            $primary_id="sales_id";
            $table_name="sales";
            $date_field_name="sales_date";
            $current_date=$date;
        }
        elseif ($module_id ==$this->config->item('quotation_module')) {
           $primary_id="quotation_id";
           $table_name="quotation";
           $date_field_name="quotation_date";
           $current_date=$date;
       }
       elseif ($module_id ==$this->config->item('purchase_order_module')) {
           $primary_id="purchase_order_id";
           $table_name="purchase_order";
           $date_field_name="purchase_order_date";
           $current_date=$date;
       }
       elseif ($module_id ==$this->config->item('purchase_module')) {
           $primary_id="purchase_id";
           $table_name="purchase";
           $date_field_name="purchase_date";
           $current_date=$date;
       }
       elseif ($module_id ==$this->config->item('purchase_return_module')) {
           $primary_id="purchase_return_id";
           $table_name="purchase_return";
           $date_field_name="purchase_return_date";
           $current_date=$date;
       }
        elseif ($module_id ==$this->config->item('product_module')) {
           $primary_id="product_id";
           $table_name="products";
           $date_field_name="added_date";
           $current_date=$date;
       }
       elseif ($module_id ==$this->config->item('service_module')) {
           $primary_id="service_id";
           $table_name="services";
           $date_field_name="added_date";
           $current_date=$date;
       }
        elseif ($module_id ==$this->config->item('receipt_voucher_module')) {
           $primary_id="receipt_id";
           $table_name="receipt_voucher";
           $date_field_name="voucher_date";
           $current_date=$date;
       }
         elseif ($module_id ==$this->config->item('expense_bill_module')) {
           $primary_id="expense_bill_id";
           $table_name="expense_bill";
           $date_field_name="expense_bill_date";
           $current_date=$date;
       }
          elseif ($module_id ==$this->config->item('payment_voucher_module')) {
           $primary_id="payment_id";
           $table_name="payment_voucher";
           $date_field_name="voucher_date";
           $current_date=$date;
       }
           elseif ($module_id ==$this->config->item('advance_voucher_module')) {
           $primary_id="advance_id";
           $table_name="advance_voucher";
           $date_field_name="voucher_date";
           $current_date=$date;
       }
         elseif ($module_id ==$this->config->item('refund_voucher_module')) {
           $primary_id="refund_id";
           $table_name="refund_voucher";
           $date_field_name="voucher_date";
           $current_date=$date;
       }
       elseif ($module_id ==$this->config->item('credit_note_module')) {
           $primary_id="credit_note_id";
           $table_name="credit_note";
           $date_field_name="credit_note_date";
           $current_date=$date;
       }
       elseif ($module_id ==$this->config->item('delivery_challan_module')) {
           $primary_id="delivery_challan_id";
           $table_name="delivery_challan";
           $date_field_name="delivery_challan_date";
           $current_date=$date;
       }


       $invoice_number=$this->generate_invoice_number($access_settings,$primary_id,$table_name,$date_field_name,$current_date);


       $data = ["reference_no" => $invoice_number];
       echo json_encode($data);
   }
}	