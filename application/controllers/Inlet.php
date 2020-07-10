<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Inlet extends MY_Controller{
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
        $inlet_module_id        = $this->config->item('inlet_module');
        $data['inlet_module_id'] = $inlet_module_id;
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;

        $section_modules        = $this->get_section_modules($inlet_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $data['email_module_id']           = $this->config->item('email_module');
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        if (!empty($this->input->post())){
            $columns            = array(
                                    0 => 't.inlet_id',
                                    1 => 't.inlet_date',
                                    2 => 'b.branch_name',
                                    3 => 't.inlet_grand_total'
                                );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->inlet_list_field($order, $dir);

            $list_data['section'] = 'inlet';
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])){
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
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
                    
                    $inlet_id = $this->encryption_url->encode($post->inlet_id);
                    $nestedData['date']    = date('d-m-Y', strtotime($post->inlet_date));
                    $nestedData['invoice'] = $post->inlet_invoice_number;
                    $nestedData['branch_name'] = $post->branch_name;
                    $nestedData['total_items'] = $this->precise_amount($post->total_items , $access_common_settings[0]->amount_precision);
                    //$nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    /*  0=pending,1=completed,2=on-hold,3=processing,4=partial,5=failed,6=canceled*/
                    $inlet_status = $post->transferred_status;
                    if ($inlet_status == 0){
                        $nestedData['transfer_status'] = '<span class="label label-default">Pending</span>';
                    }else if ($inlet_status == 1){
                        $nestedData['transfer_status'] = '<span class="label label-success">Completed</span>';
                    }else if ($inlet_status == 2){
                        $nestedData['transfer_status'] = '<span class="label label-info">On-Hold</span>';
                    }else if ($inlet_status == 3){
                        $nestedData['transfer_status'] = '<span class="label label-primary">Processing</span>';
                    }else if ($inlet_status == 4){
                        $nestedData['transfer_status'] = '<span class="label label-info">Partial</span>';
                    }else{
                        $nestedData['transfer_status'] = '<span class="label label-danger">Canceled</span>';
                    }
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if (in_array($inlet_module_id , $data['active_view']))
                    {
                        $cols .= '<span><a href="' . base_url('inlet/view/') . $inlet_id . '" class="btn btn-app" data-placement="bottom" data-toggle="tooltip" title="View inlet"><i class="fa fa-eye"></i></a></span>';
                    }
                    if (in_array($inlet_module_id , $data['active_edit']))
                    {
                        if ($inlet_status == 0 || $inlet_status == 4) {
                            $cols .= '<span><a href="' . base_url('inlet/transfer_stock/') . $inlet_id . '" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Confirm and transfer stock"><i class="fa fa-pencil"></i></a></span>';
                        }
                    }

                    if (in_array($inlet_module_id , $data['active_view']))
                    {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#pdf_type_modal"><a href="'.base_url('inlet/pdf/') . $inlet_id .'"  class="btn btn-app pdf_button" target="_blank" data-id="' . $inlet_id . '" data-name="regular" data-toggle="tooltip" data-placement="bottom" title="Download PDF"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }
                    /*if (in_array($inlet_module_id , $data['active_delete'])){
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $inlet_id . '" data-path="inlet/delete" class="delete_button" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" ><a  href="javascript:void(0);" class="btn btn-app " data-toggle="tooltip" data-placement="bottom" title="Delete inlet"><i class="fa fa-trash-o"></i></a></span>';
                    }*/

                    $cols .= '<input type="hidden" value="'.$post->from_branch_id.'" name="from_branch_id">';
                    $cols .= '<input type="hidden" value="'.$post->inlet_id.'" name="inlet_id">';
                    $cols .= '</div>';                    
                    $cols .= '</div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal" value="'.$post->inlet_id.'">';
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
            $this->load->view('inlet/list' , $data);
        }
    }

    function transfer_stock($id){
        $id  = $this->encryption_url->decode($id);

        $branch_data = $this->common->branch_field();
        $data['branch']              = $this->general_model->getJoinRecords($branch_data['string'] , $branch_data['table'] , $branch_data['where'] , $branch_data['join'] , $branch_data['order']);
        $inlet_module_id             = $this->config->item('inlet_module');
        $data['email_module_id']     = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id']       = $inlet_module_id;
        $data['inlet_module_id'] = $inlet_module_id;
        $modules                 = $this->modules;
        $privilege               = "edit_privilege";
        $data['privilege']       = $privilege;
        $section_modules         = $this->get_section_modules($inlet_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                    = array_merge($data , $section_modules);
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        
        $inlet_data = $this->common->inlet_list_field1($id);
        $data['currency'] = $this->currency_call();
        $data['data'] = $this->general_model->getJoinRecords($inlet_data['string'] , $inlet_data['table'] , $inlet_data['where'] , $inlet_data['join']);

        $product_items = $this->common->inlet_items_product_list_field($id);
        $data['items'] = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $schemediscountExist = 0;
        $descriptionExist = 0;
        $cess_exist = 0;
      
        if ($data['data'][0]->inlet_tax_amount > 0 && $data['data'][0]->inlet_igst_amount > 0 && ($data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount > 0 && ($data['data'][0]->inlet_cgst_amount > 0 || $data['data'][0]->inlet_sgst_amount > 0) && $data['data'][0]->inlet_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount > 0 && ($data['data'][0]->inlet_igst_amount == 0 && $data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount == 0 && ($data['data'][0]->inlet_igst_amount == 0 && $data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->inlet_tcs_amount > 0)
        {
            /* Discount $data['data'][0]->inlet_tds_amount > 0 || */
            $tdsExist = 1;
        }

        foreach ($data['items'] as $key => $value) {
            if ($value->inlet_item_discount_amount > 0){
                    /* Discount */
                    $discountExist = 1;
                }

                if ($value->inlet_item_scheme_discount_amount > 0){
                    /* Scheme Discount */
                    $schemediscountExist = 1;
                }

        }
        
        $descriptionExist = 1;
        
        if($data['data'][0]->inlet_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        
        $data['nature_of_supply'] = "Product";
        $note_data         = $this->template_note($data['data'][0]->note1 , $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->inlet_billing_state_id);
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
        
        $this->load->view('inlet/transfer_stock' , $data);
    }

    function received_stock(){
        $table_data = json_decode($this->input->post('table_data'),true);
        $inlet_id = $this->input->post('inlet_id');
        $outlet_id = $this->input->post('outlet_id');

        if(!empty($table_data)){
            foreach ($table_data as $key => $value) {
                $quantity = $value['quantity'];
                $inlet_data[] = array(
                    'inlet_id' => $inlet_id,
                    'item_id' => $value['item_id'],
                    "inlet_item_id" => $value['inlet_item_id'],
                    'quantity' => $value['quantity'],
                    'transaction_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')
                );
                
                $this->db->query("UPDATE `inlet_item` SET inlet_item_current_quantity = {$quantity} WHERE inlet_item_id = {$value['inlet_item_id']}");
            }
            $this->db->insert_batch('inlet_transaction_cron',$inlet_data);
            /*0=pending,1=completed,2=on-hold,3=processing,4=failed,5=canceled*/
            $this->db->query("UPDATE `inlet` SET transferred_status=2,updated_user_id=".$this->session->userdata('SESS_USER_ID').",updated_date='".date('Y-m-d')."' WHERE inlet_id={$inlet_id}");
            $this->db->query("UPDATE `outlet` SET transferred_status=2,updated_user_id=".$this->session->userdata('SESS_USER_ID').",updated_date='".date('Y-m-d')."' WHERE outlet_id={$outlet_id}");
        }
        redirect('inlet' , 'refresh');
    }

    public function view($id){
        $id                = $this->encryption_url->decode($id);
        $data              = $this->get_default_country_state();
        $inlet_module_id   = $this->config->item('inlet_module');
        $data['module_id'] = $inlet_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($inlet_module_id , $modules , $privilege);
        $data              = array_merge($data , $section_modules);

        $product_module_id             = $this->config->item('product_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');
        
        $inlet_data = $this->common->inlet_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($inlet_data['string'] , $inlet_data['table'] , $inlet_data['where'] , $inlet_data['join']);
        
        $data['product_exist'] = 1;
        $inlet_product_items = array();
        $product_items       = $this->common->inlet_items_product_list_field($id);
        $data['items'] = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;
        
        if ($data['data'][0]->inlet_tax_amount > 0 && $data['data'][0]->inlet_igst_amount > 0 && ($data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount > 0 && ($data['data'][0]->inlet_cgst_amount > 0 || $data['data'][0]->inlet_sgst_amount > 0) && $data['data'][0]->inlet_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount > 0 && ($data['data'][0]->inlet_igst_amount == 0 && $data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount == 0 && ($data['data'][0]->inlet_igst_amount == 0 && $data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }
        if($data['data'][0]->inlet_tax_cess_amount > 0){
            $cessExist = 1;
        }
        if ($data['data'][0]->inlet_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }
        
        if ($data['data'][0]->inlet_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }
        if ($data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->inlet_billing_state_id);
        
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
        $this->load->view('inlet/view' , $data);
    }

    public function pdf($id){
        $id                = $this->encryption_url->decode($id);
        $data              = $this->get_default_country_state();
        $inlet_module_id   = $this->config->item('inlet_module');
        $data['module_id'] = $inlet_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($inlet_module_id , $modules , $privilege);
        $data              = array_merge($data , $section_modules);
        $product_module_id             = $this->config->item('product_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);
        $inlet_data = $this->common->inlet_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($inlet_data['string'] , $inlet_data['table'] , $inlet_data['where'] , $inlet_data['join']);
        
        $data['product_exist'] = 1;
        $inlet_product_items = array();
        $product_items       = $this->common->inlet_items_product_list_field($id);
        $data['items'] = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;

        if ($data['data'][0]->inlet_tax_amount > 0 && $data['data'][0]->inlet_igst_amount > 0 && ($data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount > 0 && ($data['data'][0]->inlet_cgst_amount > 0 || $data['data'][0]->inlet_sgst_amount > 0) && $data['data'][0]->inlet_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount > 0 && ($data['data'][0]->inlet_igst_amount == 0 && $data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->inlet_tax_amount == 0 && ($data['data'][0]->inlet_igst_amount == 0 && $data['data'][0]->inlet_cgst_amount == 0 && $data['data'][0]->inlet_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }
        if($data['data'][0]->inlet_tax_cess_amount > 0){
            $cessExist = 1;
        }
        if ($data['data'][0]->inlet_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }
        
        if ($data['data'][0]->inlet_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }
        if ($data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->inlet_billing_state_id);
        
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
        
        $html = $this->load->view('inlet/pdf' , $data , true);

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
        $dompdf->stream($data['data'][0]->inlet_invoice_number , array(
            'Attachment' => 0 ));
    }
}
?>