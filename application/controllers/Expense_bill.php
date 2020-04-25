<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_bill extends MY_Controller
{

    function __construct(){
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index(){
        $expense_bill_module_id          = $this->config->item('expense_bill_module');
        $data['expense_bill_module_id']  = $expense_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        $data['email_sub_module_id']             = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id']        = $this->config->item('recurrence_sub_module');
        $payment_voucher_module_id        = $this->config->item('payment_voucher_module');
        $expense_voucher_module_id        = $this->config->item('expense_voucher_module');
        $file_manager_module_id        = $this->config->item('file_manager_module');
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'expense_bill_id',
                    1 => 'e.expense_bill_invoice_number',
                    2 => 'e.expense_bill_date',
                    3 => 's.supplier_name',
                    4 => 'e.expense_bill_grand_total',
                    5 => 'e.supplier_receivable_amount',
                    6 => 'e.expense_bill_paid_amount',
                    7 => 'balance_payable');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->expense_bill_list_field_1($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $expense_bill_id                         = $this->encryption_url->encode($post->expense_bill_id);

                    $edit = $email = $pdf = $delete = $payment = 0;
                    if (in_array($expense_bill_module_id, $data['active_edit']))
                    {
                        if ($post->expense_bill_paid_amount == 0 || $post->expense_bill_paid_amount == null)
                            $edit = base_url('expense_bill/edit/') . $expense_bill_id;
                    }
                    if (in_array($expense_bill_module_id, $data['active_view'])){
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                            //$email = base_url('expense_bill/email/') . $expense_bill_id;
                            $email = $expense_bill_id;
                            $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                                    'module_id' => 2,
                                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);

                            $pdf_json            = $pdf[0]->pdf_settings;
                            $rep                 = str_replace("\\", '', $pdf_json);
                            $data['pdf_results'] = json_decode($rep, true);

                            $pdf = base_url('expense_bill/pdf/') . $expense_bill_id;
                    }
                    if (in_array($data['recurrence_sub_module_id'], $data['access_sub_modules']))
                        $recurrence_sub_module = 1;

                    if (in_array($expense_bill_module_id, $data['active_delete'])){
                        $delete = $expense_bill_id;
                    }
                    if (in_array($payment_voucher_module_id, $data['active_add'])){
                        if ($post->expense_bill_paid_amount < $post->supplier_receivable_amount)
                            $payment = base_url('payment_voucher/add_expense_payment/') . $expense_bill_id;
                    }
                    $this->db->select('expense_voucher_id');
                    $this->db->from('expense_voucher');
                    $this->db->where('reference_id',$post->expense_bill_id);
                    $this->db->where('delete_status',0);
                    $this->db->where('reference_type','expense_bill');
                    $get_pv_qry = $this->db->get();
                    $ref_id = $get_pv_qry->result();
                    $expense_voucher_id = '';
                    if(!empty($ref_id)){
                        $expense_voucher_id = $ref_id[0]->expense_voucher_id;
                    }

                    $expense_voucher_str = '' ; 
                    if($expense_voucher_id != ''){
                        if(in_array($expense_voucher_module_id, $data['active_view'])){
                            $expense_voucher_id = $this->encryption_url->encode($expense_voucher_id);
                            $expense_voucher_str = ' voucher_link="' .base_url('expense_voucher/view_details/') . $expense_voucher_id.'" ledger_action="' .base_url('expense_ledgers').'" reference_id="'.$expense_voucher_id.'" ';
                        }
                        /*$nestedData['expence_voucher_view'] = ' <a href="' .base_url('expense_voucher/view_details/') . $expense_voucher_id.'" target="_blank">' . '<i class="fa fa-file" aria-hidden="true" title="Voucher View"></i>' . '</a>'. '  ' .' <form  action="' .base_url('expense_ledgers').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$expense_voucher_id.'"><button type="submit">' . '<i class="fa fa-file" aria-hidden="true" title="Ledger View"></i></button></form>';*/
                    }

                    $nestedData['expense_bill_id']           = '<input type="checkbox" name="check_expense" class="form-check-input" value="'.$delete.'" edit="'.$edit.'" view="'.base_url('expense_bill/view/') . $expense_bill_id.'" pdf="' .$pdf. '" email="'.$email.'" payment="'.$payment.'" '.$expense_voucher_str.' >';
                    $nestedData['date']                      = date('d-m-Y',strtotime($post->expense_bill_date));
                    $nestedData['invoice']                   = ' <a href="' . base_url('expense_bill/view/') . $expense_bill_id  . '">' . $post->expense_bill_invoice_number . '</a>';
                    $expense_file = $post->expense_file;
                    if($expense_file != ''){
                        $url = base_url().'filemanager/?directory=Expense';
                        if(in_array($file_manager_module_id, $data['active_view'])){
                            $nestedData['invoice'] = ' <a href="' . base_url('expense_bill/view/') . $expense_bill_id  . '">' . $post->expense_bill_invoice_number . '</a>' ." ".' <a href="' . $url.'"target="_blank">' . '<i class="fa fa-folder-open" aria-hidden="true" title="Open Attachment"></i>' . '</a>';
                        }else{
                            $nestedData['invoice'] = ' <a href="' . base_url('expense_bill/view/') . $expense_bill_id  . '">' . $post->expense_bill_invoice_number . '</a>' ." ".' <i class="fa fa-folder-open" aria-hidden="true" title="Open Attachment"></i>';
                        }
                    }
                    $nestedData['payee']                     = $post->supplier_name;
                    $nestedData['grand_total']               = $this->precise_amount($post->expense_bill_grand_total,$access_common_settings[0]->amount_precision);
                    $nestedData['payable_amount']            = $this->precise_amount($post->supplier_receivable_amount,$access_common_settings[0]->amount_precision);
                    $nestedData['pending_amount'] = $this->precise_amount(($post->supplier_receivable_amount - $post->expense_bill_paid_amount),$access_common_settings[0]->amount_precision);
                    $nestedData['paid_amount']               = $this->precise_amount($post->expense_bill_paid_amount,$access_common_settings[0]->amount_precision);
                    /*$this->db->select('expense_voucher_id');
                    $this->db->from('expense_voucher');
                    $this->db->where('reference_id',$post->expense_bill_id);
                    $this->db->where('delete_status',0);
                    $this->db->where('reference_type','expense_bill');
                    $get_pv_qry = $this->db->get();
                    $ref_id = $get_pv_qry->result();
                    $expense_voucher_id = '';
                    if(!empty($ref_id)){
                        $expense_voucher_id = $ref_id[0]->expense_voucher_id;
                    }
                    
                    if($expense_voucher_id != ''){
                        $expense_voucher_id = $this->encryption_url->encode($expense_voucher_id);
                        $nestedData['expence_voucher_view'] = ' <a href="' .base_url('expense_voucher/view_details/') . $expense_voucher_id.'" target="_blank">' . '<i class="fa fa-file" aria-hidden="true" title="Voucher View"></i>' . '</a>'. '  ' .' <form  action="' .base_url('expense_ledgers').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$expense_voucher_id.'"><button type="submit">' . '<i class="fa fa-file" aria-hidden="true" title="Ledger View"></i></button></form>';
                    }*/
                    /*$nestedData['added_user']                = $post->first_name . ' ' . $post->last_name;*/
                    $cols = '<ul class="list-inline"><li><a href="' . base_url('expense_bill/view/') . $expense_bill_id . '"><i class="fa fa-eye text-orange"></i> Expense Bill Details</a></li>';
                    $cols .= '<li>        <a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" onclick="addToModel(' . $post->expense_bill_id . ')" data-target="#myModal"><i class="fa fa-eye text-orange"></i>Follow Up dates</a>        </li>';

                    if (in_array($expense_bill_module_id, $data['active_add']))
                    {
                        if ($post->expense_bill_paid_amount < $post->expense_bill_grand_total)
                        {
                            $cols .= '<li>                            <a href="' . base_url('payment_voucher/add_expense_payment/') . $expense_bill_id . '"><i class="fa fa-money text-yellow"></i> Receive Now</a>                            </li>';
                        }
                    }
                    if (in_array($expense_bill_module_id, $data['active_edit']))
                    {
                        if ($post->expense_bill_paid_amount == 0 || $post->expense_bill_paid_amount == null)
                        {
                            $cols .= '<li>                <a href="' . base_url('expense_bill/edit/') . $expense_bill_id . '"><i class="fa fa-pencil text-blue"></i> Edit Expense Bill</a>                </li>';
                        }
                    } $cols                  .= '<li>                        <a href="' . base_url('expense_bill/pdf/') . $expense_bill_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i> Download PDF</a>                    </li>';
                    $email_sub_module      = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($expense_bill_module_id, $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $email_sub_module = 1;
                        }
                        if (in_array($data['recurrence_sub_module_id'], $data['access_sub_modules']))
                        {
                            $recurrence_sub_module = 1;
                        }
                    }
                    if ($email_sub_module == 1)
                    {

                         $cols .= '<li> <a data-backdrop="static" data-keyboard="false" data-toggle="modal"  class="btn btn-app pdf_button composeMail" data-id="' . $expense_bill_id . '" data-name="regular" data-target="#composeMail" href="#" class="btn btn-app" data-toggle="tooltip" title="Email Quotation">
                                    <i class="fa fa-envelope-o"></i>
                            </a></li>';

                       // $cols .= '<li>                        <a href="' . base_url('expense_bill/email/') . $expense_bill_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Expense Bill</a>                        </li>';
                    }
                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<li><a data-backdrop="static" data-keyboard="false" data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $expense_bill_id . '" data-path="expense_bill/convert_currency" data-currency_code="" data-grand_total="' . $post->expense_bill_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>                    </li>';
                    } if ($recurrence_sub_module == 1)
                    {
                        $cols .= '<li>                        <a class="recurrence_invoice"  data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#recurrence_invoice" data-id="' . $expense_bill_id . '" data-type="expense_bill" href="#" title="Generate Recurrence Invoice" ><i class="fa fa-pencil text-blue"></i> Generate Recurrence Invoice</a>                        </li>';
                    }
                    if (in_array($expense_bill_module_id, $data['active_delete']))
                    {
                        if($post->expense_bill_paid_amount == 0){
                            $cols .= '<li><a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $expense_bill_id . '" data-path="expense_bill/delete"  href="#" title="Delete Expense Bill" ><i class="fa fa-trash-o text-purple"></i> Delete Expense Bill</a>               </li>';
                        }
                    } 
                    $cols                 .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view("expense_bill/list", $data);
        }
    }

    public function add(){
        $data              = $this->get_default_country_state();
        $expense_module_id = $this->config->item('expense_bill_module');
        $modules            = $this->modules;
        $privilege          = "add_privilege";
        $data['privilege']  = $privilege;
        $section_modules    = $this->get_section_modules($expense_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['expense_module_id']     = $expense_module_id;
        $data['module_id']             = $expense_module_id;
        $data['notes_module_id']       = $this->config->item('notes_module');
        $data['product_module_id']     = $this->config->item('product_module');
        $data['service_module_id']     = $this->config->item('service_module');
        $data['supplier_module_id']    = $this->config->item('supplier_module');
        $data['category_module_id']    = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id']         = $this->config->item('tax_module');
        $data['discount_module_id']    = $this->config->item('discount_module');
        $data['accounts_module_id']    = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');

        $data['supplier'] = $this->supplier_call();
        /*$data['currency'] = $this->currency_call();*/

        if ($data['access_settings'][0]->discount_visible == "yes"){
            $data['discount'] = $this->discount_call();
        }

        if ($data['access_settings'][0]->tax_type == "gst" || $data['access_settings'][0]->item_access == "single_tax"){
            $data['tax'] = $this->tax_call();
        }
       

        $access_settings        = $data['access_settings'];
        $primary_id             = "expense_bill_id";
        $table_name             = $this->config->item('expense_bill_table');
        $date_field_name        = "expense_bill_date";
        $current_date           = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        
        $this->load->view('expense_bill/add', $data);
    }

    public function get_expense_suggestions($term)
    {
        $expense_bill_module_id = $this->config->item('expense_bill_module');
        $modules            = $this->modules;
        $privilege          = "add_privilege";
        $data['privilege']  = $privilege;
        $section_modules    = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);

        $suggestions_query = $this->common->expences_suggestions_field($term);
        $data              = $this->general_model->getQueryRecords($suggestions_query);

        echo json_encode($data);
    }

    public function get_table_items($code){
        /* 0-id, 1-type, 2-discount, 3-tax , */

        $expense_bill_module_id = $this->config->item('expense_bill_module');
        $modules            = $this->modules;
        $privilege          = "add_privilege";
        $data['privilege']  = $privilege;
        $section_modules    = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);

        $item_code = explode("-", $code);
        $product_data = $this->common->expense_field($item_code[0]);
        $data         = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);

        $discount_data = array();
        $tax_data      = array();
        $tds_data      = array();

        if ($item_code[1] == 'yes'){
            $discount_data = $this->discount_call();
        }

        if ($item_code[2] == 'gst' || $item_code[3] == 'single_tax') {
            $tax_data = $this->tax_call();
        }

        $data['discount']          = $discount_data;
        $data['tax']               = $tax_data;
        $branch_details            = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id']   = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id']         = $branch_details['branch'][0]->branch_id;
        $data['item_id']           = $item_code[0];
        echo json_encode($data);

    }

    public function add_expense_bill(){
        $data            = $this->get_default_country_state();
        $expense_bill_module_id = $this->config->item('expense_bill_module');
        $module_id       = $expense_bill_module_id;
        $modules         = $this->modules;
        $privilege       = "add_privilege";
        $section_modules = $this->get_section_modules($expense_bill_module_id , $modules , $privilege);
        /* presents all the needed */
        $data            = array_merge($data , $section_modules);

        /* Modules Present */
        $data['expense_bill_module_id']    = $expense_bill_module_id;
        $data['module_id']                 = $expense_bill_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('expense_module');
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');

        $access_settings = $section_modules['access_settings'];

        if ($access_settings[0]->invoice_creation == "automatic"){
            $primary_id      = "expense_bill_id";
            $table_name      = $this->config->item('expense_bill_table');
            $date_field_name = "expense_bill_date";
            $current_date    = date('Y-m-d',strtotime($this->input->post('invoice_date')));
            $invoice_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }
        $supplier   = explode("-" , $this->input->post('supplier'));
        $total_cess_amnt= $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0 ;

        if (isset($_FILES["expense_file"]["name"]) && $_FILES["expense_file"]["name"] != ""){
            $path_parts = pathinfo($_FILES["expense_file"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Expense')){
                mkdir('./assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Expense', 0777, TRUE);
            } 
            $url = 'assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Expense/'.$image_path;
            if (in_array($path_parts['extension'], array("JPG","jpg","jpeg","JPEG","PNG","png","pdf","PDF" ))){
                if (is_uploaded_file($_FILES["expense_file"]["tmp_name"])){
                    if (move_uploaded_file($_FILES["expense_file"]["tmp_name"], $url)){
                        $image_name = $image_path;
                    }
                }
            }
        }else{
            $image_name = '';
        }

        $expense_bill_data = array(
            "expense_bill_date"                            => date('Y-m-d',strtotime($this->input->post('invoice_date'))) ,
            "expense_bill_invoice_number"                  => $invoice_number ,
            "expense_bill_supplier_invoice_number"      => $this->input->post('supplier_ref'),
            "expense_bill_supplier_date"                => ($this->input->post('supplier_date') != '' ? date('Y-m-d', strtotime($this->input->post('supplier_date'))) : ''),
            "expense_bill_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
            "expense_bill_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
            "expense_bill_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
            "expense_bill_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
            "expense_bill_tax_cess_amount"                 => 0 ,
            "expense_bill_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
            "expense_bill_tds_amount"                      => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0 ,
            "expense_bill_igst_amount"                     => 0 ,
            "expense_bill_cgst_amount"                     => 0 ,
            "expense_bill_sgst_amount"                     => 0 ,
            "expense_bill_paid_amount"                     => 0 ,
            "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
            "expense_bill_payee_id"                        => $this->input->post('supplier') ,
            "expense_bill_payee_type"                      => "supplier" ,
            "expense_bill_nature_of_supply"                => $this->input->post('nature_of_supply') ,
            "expense_bill_type_of_supply"                  => $this->input->post('type_of_supply') ,
            "expense_bill_gst_payable"                     => $this->input->post('gst_payable') ,
            "expense_bill_billing_country_id"              => $this->input->post('billing_country') ,
            "expense_bill_billing_state_id"                => $this->input->post('billing_state') ,
            "added_date"                            => date('Y-m-d') ,
            "added_user_id"                         => $this->session->userdata('SESS_USER_ID') ,
            "branch_id"                             => $this->session->userdata('SESS_BRANCH_ID') ,
            "updated_date"                          => "" ,
            "updated_user_id"                       => "" ,
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
            "note2"                                 => $this->input->post('note2'),
            "expense_file" => $image_name
        );

        $expense_bill_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $expense_bill_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $expense_bill_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $expense_bill_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $expense_bill_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $expense_bill_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;

        $round_off_value = $expense_bill_data['expense_bill_grand_total'];
        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" && $this->input->post('round_off_key') == "yes")
        {
           if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 )
            {
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $expense_bill_data['round_off_amount'] = bcsub($expense_bill_data['expense_bill_grand_total'] , $round_off_value,$section_modules['access_common_settings'][0]->amount_precision);

        $expense_bill_data['expense_bill_grand_total'] = $round_off_value;

        $expense_bill_data['supplier_receivable_amount'] = $expense_bill_data['expense_bill_grand_total'];
        if (isset($expense_bill_data['expense_bill_tds_amount']) && $expense_bill_data['expense_bill_tds_amount'] > 0){
            $expense_bill_data['supplier_receivable_amount'] = bcsub($expense_bill_data['expense_bill_grand_total'], $expense_bill_data['expense_bill_tds_amount']);
        }

        //$expense_bill_tax_amount = $expense_bill_data['expense_bill_tax_amount'];
        $expense_bill_tax_amount = $expense_bill_data['expense_bill_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));
       
        if ($section_modules['access_settings'][0]->tax_type == "gst")
        {
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            if ($expense_bill_data['expense_bill_type_of_supply'] != 'import'){
                if ($expense_bill_data['expense_bill_type_of_supply'] == 'intra_state'){
                    $expense_bill_data['expense_bill_igst_amount'] = 0;
                    $expense_bill_data['expense_bill_cgst_amount'] = ($expense_bill_tax_amount * $cgst_amount_percentage) / 100;
                    $expense_bill_data['expense_bill_sgst_amount'] = ($expense_bill_tax_amount * $sgst_amount_percentage) / 100;
                    $expense_bill_data['expense_bill_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $expense_bill_data['expense_bill_igst_amount'] = $expense_bill_tax_amount;
                    $expense_bill_data['expense_bill_cgst_amount'] = 0;
                    $expense_bill_data['expense_bill_sgst_amount'] = 0;
                    $expense_bill_data['expense_bill_tax_cess_amount'] = $total_cess_amnt;
                }
            }
        }
        
        $data_main   = array_map('trim' , $expense_bill_data);
        $expense_bill_table = $this->config->item('expense_bill_table');  
      
        $expense_bill_id = $this->general_model->insertData($expense_bill_table , $data_main);

        if ($expense_bill_id) {
            $successMsg = 'Expense Bill Added Successfully';
            $this->session->set_flashdata('expence_bill_success',$successMsg);
            $log_data              = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $expense_bill_id ,
                'table_name'        => $expense_bill_table ,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'expense_bill Inserted' );
            $data_main['expense_bill_id'] = $expense_bill_id;
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $expense_bill_item_data       = $this->input->post('table_data');
            $js_data               = json_decode($expense_bill_item_data);
            $js_data               = array_reverse($js_data);
            $item_table            = $this->config->item('expense_bill_item_table');
            
            if (!empty($js_data))
            {
                $js_data1 = array();
                foreach ($js_data as $key => $value)
                {
                    if ($value != null && $value != '') {
                        $item_id   = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "expense_type_id"                   => $value->item_id ,
                            "expense_bill_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "expense_bill_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "expense_bill_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "expense_bill_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "expense_bill_item_discount_amount" => $value->item_discount_amount ? (float) $value->item_discount_amount : 0 ,
                            "expense_bill_item_discount_id"     => $value->item_discount_id ? (float) $value->item_discount_id : 0 ,
                            "expense_bill_item_discount_percentage" => $value->item_discount_percentage ? (float) $value->item_discount_percentage : 0 ,
                            "expense_bill_item_tds_id"          => $value->item_tds_id ? (float) $value->item_tds_id : 0 ,
                            "expense_bill_item_tds_percentage"  => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ,
                            "expense_bill_item_tds_amount"      => $value->item_tds_amount ? (float) $value->item_tds_amount : 0 ,
                            "expense_bill_item_grand_total"     => $value->item_grand_total ? (float) $value->item_grand_total : 0 ,
                            "expense_bill_item_tax_id"          => $value->item_tax_id ? (float) $value->item_tax_id : 0 ,
                            "expense_bill_item_tax_cess_id"          => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0 ,
                            "expense_bill_item_igst_percentage" => 0 ,
                            "expense_bill_item_igst_amount"     => 0 ,
                            "expense_bill_item_cgst_percentage" => 0 ,
                            "expense_bill_item_cgst_amount"     => 0 ,
                            "expense_bill_item_sgst_percentage" => 0 ,
                            "expense_bill_item_sgst_amount"     => 0 ,
                            "expense_bill_item_tax_percentage"  => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ,
                            "expense_bill_item_tax_cess_percentage"  => 0 ,
                            "expense_bill_item_tax_amount"      => $value->item_tax_amount ? (float) $value->item_tax_amount : 0 ,
                            'expense_bill_item_tax_cess_amount' => 0 ,
                            "expense_bill_item_description"     => $value->item_description ? $value->item_description : "" ,
                            "expense_bill_id"                   => $expense_bill_id );

                        $expense_bill_item_tax_amount     = $item_data['expense_bill_item_tax_amount'];
                        $expense_bill_item_tax_percentage = $item_data['expense_bill_item_tax_percentage'];

                        if ($section_modules['access_settings'][0]->tax_type == "gst")
                        {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0 ;
                            
                            if ($expense_bill_data['expense_bill_type_of_supply'] != 'import'){
                                if ($data['branch'][0]->branch_state_id == $expense_bill_data['expense_bill_billing_state_id']) {
                                    $item_data['expense_bill_item_igst_amount'] = 0;
                                    $item_data['expense_bill_item_cgst_amount'] = ($expense_bill_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_sgst_amount'] = ($expense_bill_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['expense_bill_item_igst_percentage'] = 0;
                                    $item_data['expense_bill_item_cgst_percentage'] = ($expense_bill_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_sgst_percentage'] = ($expense_bill_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                                else
                                {
                                    $item_data['expense_bill_item_igst_amount'] = $expense_bill_item_tax_amount;
                                    $item_data['expense_bill_item_cgst_amount'] = 0;
                                    $item_data['expense_bill_item_sgst_amount'] = 0;
                                    $item_data['expense_bill_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['expense_bill_item_igst_percentage'] = $expense_bill_item_tax_percentage;
                                    $item_data['expense_bill_item_cgst_percentage'] = 0;
                                    $item_data['expense_bill_item_sgst_percentage'] = 0;
                                    $item_data['expense_bill_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                            /*else
                            {
                                if ($expense_bill_data['expense_bill_type_of_supply'] == "export_with_payment")
                                {
                                    $item_data['expense_bill_item_igst_amount'] = $expense_bill_item_tax_amount;
                                    $item_data['expense_bill_item_cgst_amount'] = 0;
                                    $item_data['expense_bill_item_sgst_amount'] = 0;
                                    $item_data['expense_bill_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['expense_bill_item_igst_percentage'] = $expense_bill_item_tax_percentage;
                                    $item_data['expense_bill_item_cgst_percentage'] = 0;
                                    $item_data['expense_bill_item_sgst_percentage'] = 0;
                                    $item_data['expense_bill_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }*/
                        }

                        $data_item  = array_map('trim' , $item_data);
                        $js_data1[] = $data_item;
                        
                    }
                }

                //$this->general_model->insertData($item_table , $js_data1);
                $this->db->insert_batch($item_table, $js_data1); 
                if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){

                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){
                        $action = "add";
                        $this->expense_bill_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                    }
                }
            }
        } else {
            $errorMsg = 'Expense Bill Add Unsuccessful';
            $this->session->set_flashdata('expence_bill_error',$errorMsg);
            redirect('expense_bill' , 'refresh');
        } 
        $action = $this->input->post('submit');
        if ($action == 'add') {
            redirect('expense_bill' , 'refresh');
        }else{
            $expense_bill_id = $this->encryption_url->encode($expense_bill_id);
            redirect('receipt_voucher/add_expense_bill_receipt/' . $expense_bill_id , 'refresh');
        }
    }

    public function expense_bill_vouchers($section_modules , $data_main , $js_data , $branch){
        /*$invoice_from = $data_main['from_account'];
        $invoice_to   = $data_main['to_account'];*/
        $ledgers      = array();

        $access_sub_modules    = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings       = $section_modules['access_settings'];
        $expense_ledger = $this->config->item('expense_ledger');

        $default_cgst_id = $expense_ledger['CGST@X'];
        $cgst_x = $this->ledger_model->getDefaultLedgerId($default_cgst_id);

        $default_sgst_id = $expense_ledger['SGST@X'];
        $sgst_x = $this->ledger_model->getDefaultLedgerId($default_sgst_id);

        $default_utgst_id = $expense_ledger['UTGST@X'];
        $utgst_x = $this->ledger_model->getDefaultLedgerId($default_utgst_id);

        $default_igst_id = $expense_ledger['IGST@X'];
        $igst_x = $this->ledger_model->getDefaultLedgerId($default_igst_id);

        /* Tax rate slab */
        $present = "";
        $converted_voucher_amount = 0;
        $new_ledger_ary = array();
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
        if ((($data_main['expense_bill_tax_amount'] > 0 && ($data_main['expense_bill_igst_amount'] > 0 || $data_main['expense_bill_cgst_amount'] > 0 || $data_main['expense_bill_sgst_amount'] > 0 || $data_main['expense_bill_tax_cess_amount'] > 0)) || $data_main['expense_bill_tax_cess_amount'] > 0 )){
            $present = "gst";

            if ($data_main['expense_bill_type_of_supply'] != 'import'){
                if ($branch[0]->branch_state_id == $data_main['expense_bill_billing_state_id']){
                    $present = "no_igst";
                } else {
                    $present = "igst";
                }
            }

            if($data_main['expense_bill_gst_payable'] != "yes" && $present != "gst"){
               
                    foreach ($js_data as $key => $value){
                        if ($present != "igst"){
                            if ($value['expense_bill_item_cgst_percentage'] > 0 || $value['expense_bill_item_sgst_percentage'] > 0){
                                $cgst_ary = array(
                                            'ledger_name' => 'Input CGST@'.$value['expense_bill_item_cgst_percentage'].'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                                if(!empty($cgst_x)){
                                    $cgst_ledger = $cgst_x->ledger_name;
                                    $cgst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_cgst_percentage'] , $cgst_ledger);
                                    $cgst_ary['ledger_name'] = $cgst_ledger;
                                    $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                    $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                    $cgst_ary['main_grp'] = $cgst_x->main_group;
                                    $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                                }
                                
                                $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                                /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                            'ledger_name' => 'CGST@'.$value['expense_bill_item_cgst_percentage'].'%',
                                                                            'subgrp_1' => 'CGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                                
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['expense_bill_billing_state_id']);
                                if($is_utgst == '1') $gst_lbl = 'UTGST';
                                $sgst_ary = array(
                                            'ledger_name' => 'Input '.$gst_lbl.'@'.$value['expense_bill_item_sgst_percentage'].'%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                                if(!empty($sgst_x)){
                                    if($is_utgst == '1') {
                                        $sgst_ledger = $utgst_x->ledger_name;
                                        $sgst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_sgst_percentage'] , $sgst_ledger);
                                        $sgst_ary['ledger_name'] = $sgst_ledger;
                                        $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                        $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                        $sgst_ary['main_grp'] = $utgst_x->main_group;
                                        $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                    }else{
                                        $sgst_ledger = $sgst_x->ledger_name;
                                        $sgst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_sgst_percentage'] , $sgst_ledger);
                                        $sgst_ary['ledger_name'] = $sgst_ledger;
                                        $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                        $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                        $sgst_ary['main_grp'] = $sgst_x->main_group;
                                        $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                    }
                                    
                                }
                                $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                                /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                            'ledger_name' => $gst_lbl.'@'.$value['expense_bill_item_sgst_percentage'].'%',
                                                                            'subgrp_1' => $gst_lbl,
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/

                                if (in_array($cgst_tax_ledger , $cgst_slab)) {
                                    $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $value['expense_bill_item_cgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);   
                                }else {
                                    $cgst_slab[] = $cgst_tax_ledger;
                                    $cgst_slab_items[$cgst_tax_ledger] = $value['expense_bill_item_cgst_amount'];
                                }

                                if (in_array($sgst_tax_ledger , $sgst_slab)){
                                    $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $value['expense_bill_item_sgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }else{
                                    $sgst_slab[]                       = $sgst_tax_ledger;
                                    $sgst_slab_items[$sgst_tax_ledger] = $value['expense_bill_item_sgst_amount'];
                                }
                            }
                        }else{
                            if ($value['expense_bill_item_igst_percentage'] > 0) {
                                $igst_ary = array(
                                            'ledger_name' => 'Input IGST@'.$value['expense_bill_item_igst_percentage'].'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_igst_percentage'],
                                            'amount' => 0
                                        );
                                if(!empty($igst_x)){
                                    $igst_ledger = $igst_x->ledger_name;
                                    $igst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_igst_percentage'] , $igst_ledger);
                                    $igst_ary['ledger_name'] = $igst_ledger;
                                    $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                    $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                    $igst_ary['main_grp'] = $igst_x->main_group;
                                    $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                                }
                                $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                                /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                            'ledger_name' => 'IGST@'.$value['expense_bill_item_igst_percentage'].'%',
                                                                            'subgrp_1' => 'IGST',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                                if (in_array($igst_tax_ledger , $igst_slab))
                                {
                                    $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['expense_bill_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                }
                                else
                                {
                                    $igst_slab[]                       = $igst_tax_ledger;
                                    $igst_slab_items[$igst_tax_ledger] = $value['expense_bill_item_igst_amount'];
                                }
                            }
                        }
                        if ($value['expense_bill_item_tax_cess_percentage'] > 0){
                            $default_cess_id = $expense_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                           
                            $cess_ary = array(
                                            'ledger_name' => 'Input Compensation Cess @'.$value['expense_bill_item_tax_cess_percentage'].'%',
                                            'second_grp' => 'Cess',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_tax_cess_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cess_ledger_name)){
                                $cess_ledger = $cess_ledger_name->ledger_name;
                                $cess_ledger = str_ireplace('{{X}}',$value['expense_bill_item_tax_cess_percentage'] , $cess_ledger);
                                $cess_ary['ledger_name'] = $cess_ledger;
                                $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                                $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                                $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                                $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                            }
                            $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);
                            /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                            'ledger_name' => 'Compensation Cess @'.$value['expense_bill_item_tax_cess_percentage'].'%',
                                                                            'subgrp_1' => 'Cess',
                                                                            'subgrp_2' => 'Duties and taxes',
                                                                            'main_grp' => 'Current Liabilities',
                                                                            'amount' => 0
                                                                        ));*/
                            if (in_array($cess_tax_ledger , $cess_slab)){
                                $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger] , $value['expense_bill_item_tax_cess_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $cess_slab[]                       = $cess_tax_ledger;
                                $cess_slab_items[$cess_tax_ledger] = $value['expense_bill_item_tax_cess_amount'];
                            }
                        }
                    }
                
            }elseif($data_main['expense_bill_gst_payable'] == "yes" && $present != "gst"){

                foreach ($js_data as $key => $value){
                    if ($present != "igst"){
                        if ($value['expense_bill_item_cgst_percentage'] > 0 || $value['expense_bill_item_sgst_percentage'] > 0){
                            $default_cgst_id = $expense_ledger['CGST_REV'];
                            $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                           
                            $cgst_ary = array(
                                            'ledger_name' => 'CGST - RCM ITC availed',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_ledger_name)){
                                $cgst_ledger = $cgst_ledger_name->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_cgst_percentage'] , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_ledger_name->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_ledger_name->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_ledger_name->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_ledger_name->ledger_id;
                            }
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                            /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'CGST - RCM ITC availed',
                                                                        'subgrp_1' => 'CGST',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            
                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['expense_bill_billing_state_id']);
                            if($is_utgst == '1') $gst_lbl = 'UTGST';
                            $default_sgst_id = $expense_ledger[$gst_lbl.'_REV'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                           
                            $sgst_ary = array(
                                            'ledger_name' => $gst_lbl . ' - RCM ITC availed',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($sgst_ledger_name)){
                                $sgst_ledger = $sgst_ledger_name->ledger_name;
                                $sgst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_sgst_percentage'] , $sgst_ledger);
                                $sgst_ary['ledger_name'] = $sgst_ledger;
                                $sgst_ary['primary_grp'] = $sgst_ledger_name->sub_group_1;
                                $sgst_ary['second_grp'] = $sgst_ledger_name->sub_group_2;
                                $sgst_ary['main_grp'] = $sgst_ledger_name->main_group;
                                $sgst_ary['default_ledger_id'] = $sgst_ledger_name->ledger_id;
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => $gst_lbl.' - RCM ITC availed',
                                                                        'subgrp_1' => $gst_lbl,
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/

                            if (in_array($cgst_tax_ledger , $cgst_slab)) {
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $value['expense_bill_item_cgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);   
                            }else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['expense_bill_item_cgst_amount'];
                            }

                            if (in_array($sgst_tax_ledger , $sgst_slab)){
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $value['expense_bill_item_sgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $sgst_slab[]                       = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['expense_bill_item_sgst_amount'];
                            }

                            /* reverse process */
                            $default_cgst_id = $expense_ledger['CGST_PAY'];
                            $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                           
                            $cgst_ary = array(
                                            'ledger_name' => 'CGST - RCM payable',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_ledger_name)){
                                $cgst_ledger = $cgst_ledger_name->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_cgst_percentage'] , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_ledger_name->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_ledger_name->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_ledger_name->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_ledger_name->ledger_id;
                            }
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                            /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'CGST - RCM payable',
                                                                        'subgrp_1' => 'CGST',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            
                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['expense_bill_billing_state_id']);
                            if($is_utgst == '1') $gst_lbl = 'UTGST';
                            $default_sgst_id = $expense_ledger[$gst_lbl.'_PAY'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                           
                            $sgst_ary = array(
                                            'ledger_name' => $gst_lbl . ' - RCM payable',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($sgst_ledger_name)){
                                $sgst_ledger = $sgst_ledger_name->ledger_name;
                                $sgst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_sgst_percentage'] , $sgst_ledger);
                                $sgst_ary['ledger_name'] = $sgst_ledger;
                                $sgst_ary['primary_grp'] = $sgst_ledger_name->sub_group_1;
                                $sgst_ary['second_grp'] = $sgst_ledger_name->sub_group_2;
                                $sgst_ary['main_grp'] = $sgst_ledger_name->main_group;
                                $sgst_ary['default_ledger_id'] = $sgst_ledger_name->ledger_id;
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => $gst_lbl.' - RCM payable',
                                                                        'subgrp_1' => $gst_lbl,
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            if(!in_array($cgst_tax_ledger, $cgst_slab_minus)) array_push($cgst_slab_minus, $cgst_tax_ledger);

                            if (in_array($cgst_tax_ledger , $cgst_slab)) {
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $value['expense_bill_item_cgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);   
                            }else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['expense_bill_item_cgst_amount'];
                            }

                            if(!in_array($sgst_tax_ledger, $sgst_slab_minus)) array_push($sgst_slab_minus, $sgst_tax_ledger);
                            if (in_array($sgst_tax_ledger , $sgst_slab)){
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $value['expense_bill_item_sgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $sgst_slab[]                       = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['expense_bill_item_sgst_amount'];
                            }
                        }
                    }else{
                        if ($value['expense_bill_item_igst_percentage'] > 0) {
                            $default_igst_id = $expense_ledger['IGST_REV'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'IGST - RCM ITC availed',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_igst_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'IGST - RCM ITC availed',
                                                                        'subgrp_1' => 'IGST',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            if (in_array($igst_tax_ledger , $igst_slab))
                            {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['expense_bill_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }
                            else
                            {
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['expense_bill_item_igst_amount'];
                            }
                            /* reverse process */
                            $default_igst_id = $expense_ledger['IGST_PAY'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'IGST - RCM payable',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['expense_bill_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['expense_bill_item_igst_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'IGST - RCM payable',
                                                                        'subgrp_1' => 'IGST',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            if(!in_array($igst_tax_ledger , $igst_slab_minus)) array_push($igst_slab_minus, $igst_tax_ledger);

                            if (in_array($igst_tax_ledger , $igst_slab)) {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['expense_bill_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else {
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['expense_bill_item_igst_amount'];
                            }
                        }
                    }
                    if ($value['expense_bill_item_tax_cess_percentage'] > 0){
                        $default_cess_id = $expense_ledger['CESS_REV'];
                        $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                       
                        $cess_ary = array(
                                        'ledger_name' => 'Compensation Cess - RCM ITC availed',
                                        'second_grp' => 'Cess',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['expense_bill_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['expense_bill_item_tax_cess_percentage'] , $cess_ledger);
                            $cess_ary['ledger_name'] = $cess_ledger;
                            $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                            $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                            $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                            $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                        }
                        $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);
                        /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'Compensation Cess - RCM ITC availed',
                                                                        'subgrp_1' => 'Cess',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                        if (in_array($cess_tax_ledger , $cess_slab)){
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger] , $value['expense_bill_item_tax_cess_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[]                       = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['expense_bill_item_tax_cess_amount'];
                        }

                        /* reverese process */
                        $default_cess_id = $expense_ledger['CESS_PAY'];
                        $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                       
                        $cess_ary = array(
                                        'ledger_name' => 'Compensation Cess - RCM payable',
                                        'second_grp' => 'Cess',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['expense_bill_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['expense_bill_item_tax_cess_percentage'] , $cess_ledger);
                            $cess_ary['ledger_name'] = $cess_ledger;
                            $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                            $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                            $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                            $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                        }
                        $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);
                        /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'Compensation Cess - RCM payable',
                                                                        'subgrp_1' => 'Cess',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/

                        if(!in_array($cess_tax_ledger, $cess_slab_minus)) array_push($cess_slab_minus, $cess_tax_ledger);

                        if (in_array($cess_tax_ledger , $cess_slab)){
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger] , $value['expense_bill_item_tax_cess_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[]                       = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['expense_bill_item_tax_cess_amount'];
                        }
                    }
                }
            }
        }

        if($data_main['expense_bill_type_of_supply'] != 'import'){
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

                foreach ($igst_charges_array as $key => $value){
                    if ($data_main['expense_bill_type_of_supply'] == 'intra_state'){
                        if ($cgst_charges_array[$key]['tax_percentage'] > 0 || $sgst_charges_array[$key]['tax_percentage'] > 0){
                            if($data_main['expense_bill_gst_payable'] != "yes"){

                                $cgst_ary = array(
                                            'ledger_name' => 'Input CGST@'.$cgst_charges_array[$key]['tax_percentage'].'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
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
                                /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);*/
                                $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                                /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'CGST@'.$cgst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => 'CGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['expense_bill_billing_state_id']);
                                if($is_utgst == '1') $gst_lbl = 'UTGST';
                                $default_sgst_id = $expense_ledger[$gst_lbl.'@X'];
                                $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                               
                                $sgst_ary = array(
                                                'ledger_name' => 'Input '.$gst_lbl . '@' . (float)$sgst_charges_array[$key]['tax_percentage'] . '%',
                                                'second_grp' => $gst_lbl,
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Assets',
                                                'default_ledger_id' => 0,
                                                'default_value' => $sgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($sgst_ledger_name)){
                                    $sgst_ledger = $sgst_ledger_name->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$sgst_charges_array[$key]['tax_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_ledger_name->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_ledger_name->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_ledger_name->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_ledger_name->ledger_id;
                                }
                                $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                                /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => $gst_lbl.'@'.$sgst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => $gst_lbl,
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/

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
                            }else{
                                $default_cgst_id = $expense_ledger['CGST_REV'];
                                $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                               
                                $cgst_ary = array(
                                                'ledger_name' => 'CGST - RCM ITC availed',
                                                'second_grp' => 'CGST',
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Assets',
                                                'default_ledger_id' => 0,
                                                'default_value' => $cgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($cgst_ledger_name)){
                                    $cgst_ledger = $cgst_ledger_name->ledger_name;
                                    $cgst_ledger = str_ireplace('{{X}}',$cgst_charges_array[$key]['tax_percentage'] , $cgst_ledger);
                                    $cgst_ary['ledger_name'] = $cgst_ledger;
                                    $cgst_ary['primary_grp'] = $cgst_ledger_name->sub_group_1;
                                    $cgst_ary['second_grp'] = $cgst_ledger_name->sub_group_2;
                                    $cgst_ary['main_grp'] = $cgst_ledger_name->main_group;
                                    $cgst_ary['default_ledger_id'] = $cgst_ledger_name->ledger_id;
                                }
                                $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                                /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'CGST - RCM ITC availed',
                                                                    'subgrp_1' => 'CGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['expense_bill_billing_state_id']);
                                if($is_utgst == '1') $gst_lbl = 'UTGST';
                                $default_sgst_id = $expense_ledger[$gst_lbl.'_REV'];
                                $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                               
                                $sgst_ary = array(
                                                'ledger_name' => $gst_lbl . ' - RCM ITC availed',
                                                'second_grp' => $gst_lbl,
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Assets',
                                                'default_ledger_id' => 0,
                                                'default_value' => (float)$sgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($sgst_ledger_name)){
                                    $sgst_ledger = $sgst_ledger_name->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',(float)$sgst_charges_array[$key]['tax_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_ledger_name->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_ledger_name->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_ledger_name->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_ledger_name->ledger_id;
                                }
                                $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                                /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => $gst_lbl.' - RCM ITC availed',
                                                                    'subgrp_1' => $gst_lbl,
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/

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
                                $default_cgst_id = $expense_ledger['CGST_PAY'];
                                $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                               
                                $cgst_ary = array(
                                                'ledger_name' => 'CGST - RCM payable',
                                                'second_grp' => 'CGST',
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Liabilities',
                                                'default_ledger_id' => 0,
                                                'default_value' => (float)$cgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($cgst_ledger_name)){
                                    $cgst_ledger = $cgst_ledger_name->ledger_name;
                                    $cgst_ledger = str_ireplace('{{X}}',(float)$cgst_charges_array[$key]['tax_percentage'] , $cgst_ledger);
                                    $cgst_ary['ledger_name'] = $cgst_ledger;
                                    $cgst_ary['primary_grp'] = $cgst_ledger_name->sub_group_1;
                                    $cgst_ary['second_grp'] = $cgst_ledger_name->sub_group_2;
                                    $cgst_ary['main_grp'] = $cgst_ledger_name->main_group;
                                    $cgst_ary['default_ledger_id'] = $cgst_ledger_name->ledger_id;
                                }
                                $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                                /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'CGST - RCM payable',
                                                                    'subgrp_1' => 'CGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                                $default_sgst_id = $expense_ledger[$gst_lbl.'_PAY'];
                                $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                               
                                $sgst_ary = array(
                                                'ledger_name' => $gst_lbl . ' - RCM payable',
                                                'second_grp' => $gst_lbl,
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Assets',
                                                'default_ledger_id' => 0,
                                                'default_value' => (float)$sgst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($sgst_ledger_name)){
                                    $sgst_ledger = $sgst_ledger_name->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',(float)$sgst_charges_array[$key]['tax_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_ledger_name->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_ledger_name->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_ledger_name->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_ledger_name->ledger_id;
                                }
                                $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                                /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => $gst_lbl.' - RCM payable',
                                                                    'subgrp_1' => $gst_lbl,
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/

                                if (in_array($cgst_tax_ledger , $cgst_slab)){
                                    if ($key != 5){
                                        $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $cgst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                    }else{
                                        $cgst_slab_items[$cgst_tax_ledger] = bcsub($cgst_slab_items[$cgst_tax_ledger] , $cgst_charges_array[$key]['tax_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                }else{
                                    $cgst_slab[] = $cgst_tax_ledger;
                                    $cgst_slab_items[$cgst_tax_ledger] = $cgst_charges_array[$key]['tax_amount'];
                                    if ($key != 5 && !in_array($cgst_tax_ledger , $cgst_slab_minus)){
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
                                    if ($key != 5 && !in_array($sgst_tax_ledger , $sgst_slab_minus)){
                                        $sgst_slab_minus[] = $sgst_tax_ledger;
                                    }
                                }
                            }
                        }
                    } else{
                        if ($igst_charges_array[$key]['tax_percentage'] > 0) {
                            if($data_main['expense_bill_gst_payable'] != "yes"){
                                $igst_ary = array(
                                            'ledger_name' => 'Input IGST@'.(float)$igst_charges_array[$key]['tax_percentage'].'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$igst_charges_array[$key]['tax_percentage'],
                                            'amount' => 0
                                        );
                                if(!empty($igst_x)){
                                    $igst_ledger = $igst_x->ledger_name;
                                    $igst_ledger = str_ireplace('{{X}}',(float)$igst_charges_array[$key]['tax_percentage'] , $igst_ledger);
                                    $igst_ary['ledger_name'] = $igst_ledger;
                                    $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                    $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                    $igst_ary['main_grp'] = $igst_x->main_group;
                                    $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                                }
                                $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                                /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'IGST@'.$igst_charges_array[$key]['tax_percentage'].'%',
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
                            }else{
                                $default_igst_id = $expense_ledger['IGST_REV'];
                                $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                               
                                $igst_ary = array(
                                                'ledger_name' => 'IGST - RCM ITC availed',
                                                'second_grp' => 'IGST',
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Assets',
                                                'default_ledger_id' => 0,
                                                'default_value' => (float)$igst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($igst_ledger_name)){
                                    $igst_ledger = $igst_ledger_name->ledger_name;
                                    $igst_ledger = str_ireplace('{{X}}',(float)$igst_charges_array[$key]['tax_percentage'] , $igst_ledger);
                                    $igst_ary['ledger_name'] = $igst_ledger;
                                    $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                    $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                    $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                    $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                                }
                                $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                                /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'IGST - RCM ITC availed',
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
                                $default_igst_id = $expense_ledger['IGST_PAY'];
                                $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                               
                                $igst_ary = array(
                                                'ledger_name' => 'IGST - RCM payable',
                                                'second_grp' => 'IGST',
                                                'primary_grp' => 'Duties and taxes',
                                                'main_grp' => 'Current Assets',
                                                'default_ledger_id' => 0,
                                                'default_value' => (float)$igst_charges_array[$key]['tax_percentage'],
                                                'amount' => 0
                                            );
                                if(!empty($igst_ledger_name)){
                                    $igst_ledger = $igst_ledger_name->ledger_name;
                                    $igst_ledger = str_ireplace('{{X}}',(float)$igst_charges_array[$key]['tax_percentage'] , $igst_ledger);
                                    $igst_ary['ledger_name'] = $igst_ledger;
                                    $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                    $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                    $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                    $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                                }
                                $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                                /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'IGST - RCM payable',
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
        }
                
        /* Tax rate slab ends */
        /* TDS SLAB */
        if ($data_main['expense_bill_tds_amount'] > 0){
            $tds_slab                      = array();
            $tds_slab_minus                = array();
            $tds_slab_items                = array();
            $data_main['total_tds_amount'] = 0;
           
            foreach ($js_data as $key => $value){

                if ($value['expense_bill_item_tds_percentage'] > 0){
                    
                    $string       = 'tds.section_name,td.tax_name';
                    $table        = 'tax td';
                    $where        = array('td.delete_status' => 0 ,'td.tax_id' => $value['expense_bill_item_tds_id'] );
                    $join         = array('tax_section tds' => 'td.section_id = tds.section_id');
                    $tds_data     = $this->general_model->getJoinRecords($string , $table , $where , $join);
                    if(!empty($tds_data)){
                        $section_name = $tds_data[0]->section_name;
                        $module_type  = strtoupper($tds_data[0]->tax_name);
                    }else{
                        $module_type = 'TDS';
                        $section_name = '193';
                    }
                    
                    $payment_type = "Receivable";
                    $tds_subgroup = "TDS payable u/s ";
                    

                    $tds_title    = $module_type . " " . $payment_type . " under u/s " . $section_name;
                    $tds_subgroup = $tds_subgroup;

                    $default_tds_id = $expense_ledger['TDS_PAY'];
                    $tds_ledger_name = $this->ledger_model->getDefaultLedgerId($default_tds_id);
                        
                    $tds_ary = array(
                                    'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['expense_bill_item_tds_percentage'].'%',
                                    'second_grp' => '',
                                    'primary_grp' => '',
                                    'main_grp' => 'Current Assets',
                                    'default_ledger_id' => 0,
                                    'default_value' => $value['expense_bill_item_tds_percentage'],
                                    'amount' => 0
                                );
                    if(!empty($tds_ledger_name)){
                        $tds_ledger = $tds_ledger_name->ledger_name;
                        $tds_ledger = str_ireplace('{{SECTION}}',$section_name, $tds_ledger);
                        $tds_ledger = str_ireplace('{{X}}',$value['expense_bill_item_tds_percentage'] , $tds_ledger);
                        $tds_ary['ledger_name'] = $tds_ledger;
                        $tds_ary['primary_grp'] = $tds_ledger_name->sub_group_1;
                        $tds_ary['second_grp'] = $tds_ledger_name->sub_group_2;
                        $tds_ary['main_grp'] = $tds_ledger_name->main_group;
                        $tds_ary['default_ledger_id'] = $tds_ledger_name->ledger_id;
                    }
                    $tds_ledger = $this->ledger_model->getGroupLedgerId($tds_ary);
                    /*$tds_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => $tds_subgroup.$section_name.'@'.$value['expense_bill_item_tds_percentage'].'%',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'subgrp_1' => '',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' =>  0
                                                                    ));*/
                    
                    if (in_array($tds_ledger , $tds_slab)){
                        $tds_slab_items[$tds_ledger] = bcadd($tds_slab_items[$tds_ledger] , $value['expense_bill_item_tds_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                    }else {
                        $tds_slab[]                  = $tds_ledger;
                        $tds_slab_items[$tds_ledger] = $value['expense_bill_item_tds_amount'];
                    }

                    if (strtoupper($module_type) == "TCS"){
                        if (!in_array($tds_ledger , $tds_slab_minus)){
                            $tds_slab_minus[] = $tds_ledger;
                        }
                    }
                }
            }
        }
        /* tds ends */
        $default_expense_id = $expense_ledger['EXPENSE'];
        $expense_ledger_name = $this->ledger_model->getDefaultLedgerId($default_expense_id);
            
        $EXPENSE_ary = array(
                        'ledger_name' => 'Expense Account',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Indirect Expenses',
                        'amount' => 0
                    );
        if(!empty($expense_ledger_name)){
            $expense_ledger_nm = $expense_ledger_name->ledger_name;
            $EXPENSE_ary['ledger_name'] = $expense_ledger_nm;
            $EXPENSE_ary['primary_grp'] = $expense_ledger_name->sub_group_1;
            $EXPENSE_ary['second_grp'] = $expense_ledger_name->sub_group_2;
            $EXPENSE_ary['main_grp'] = $expense_ledger_name->main_group;
            $EXPENSE_ary['default_ledger_id'] = $expense_ledger_name->ledger_id;
        }
        $expense_bill_ledger_id = $this->ledger_model->getGroupLedgerId($EXPENSE_ary);
        /*$expense_bill_ledger_id = $this->ledger_model->getDefaultLedger('Expense Account');
        if($expense_bill_ledger_id == 0){
            $expense_bill_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'Expense Account',
                                                                    'subgrp_2' => '',
                                                                    'subgrp_1' => '',
                                                                    'main_grp' => 'Indirect Expenses',
                                                                    'amount' => 0
                                                                ));

        }*/
        $ledgers['expense_bill_ledger_id'] = $expense_bill_ledger_id;

        $string             = 'ledger_id,supplier_name';
        $table              = 'supplier';
        $where              = array('supplier_id' => $data_main['expense_bill_payee_id']);
        $supplier_data      = $this->general_model->getRecords($string , $table , $where , $order = "");
        $supplier_name = $supplier_data[0]->supplier_name;
        $supplier_ledger_id = $supplier_data[0]->ledger_id;

        if(!$supplier_ledger_id){
            $supplier_ledger_id = $expense_ledger['SUPPLIER'];
            $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                
            $supplier_ary = array(
                            'ledger_name' => $supplier_name,
                            'second_grp' => '',
                            'primary_grp' => 'Sundry Creditors',
                            'main_grp' => 'Current Liabilities',
                            'default_ledger_id' => 0,
                            'default_value' => $supplier_name,
                            'amount' => 0
                        );
            if(!empty($supplier_ledger_name)){
                $supplier_ledger = $supplier_ledger_name->ledger_name;
                /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                $supplier_ledger = str_ireplace('{{X}}',$supplier_name, $supplier_ledger);
                $supplier_ary['ledger_name'] = $supplier_ledger;
                $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
            }
            $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
        }
        /*$supplier_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => $supplier_name,
                                            'subgrp_2' => 'Sundry Creditors',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Current Liabilities',
                                            'amount' =>  0
                                        ));*/
       
        $ledgers['supplier_ledger_id'] = $supplier_ledger_id;
        $ledger_from                   = $supplier_ledger_id;

        $ledgers['ledger_from'] = $ledger_from;
        $ledgers['ledger_to']   = $expense_bill_ledger_id;

        $vouchers              = array();
        $vouchers_new          = array();
        $charges_sub_module_id = $this->config->item('charges_sub_module');

        if ($data_main['expense_bill_gst_payable'] != "yes"){
            $grand_total = $data_main['expense_bill_grand_total'];
        } else {
            $total_tax_amount = ($data_main['expense_bill_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount'] + $data_main['expense_bill_tax_cess_amount']);
            $grand_total      = bcsub($data_main['expense_bill_grand_total'] , $total_tax_amount,$section_modules['access_common_settings'][0]->amount_precision);
        }

        if (isset($data_main['expense_bill_tds_amount']) && $data_main['expense_bill_tds_amount'] > 0){
            $grand_total = bcsub($grand_total , $data_main['expense_bill_tds_amount'],$section_modules['access_common_settings'][0]->amount_precision);
        }

        $items_ledgers_ary = array();
        $default_expense_id = $expense_ledger['EXPENSE_ITEM'];
        $expense_ledger_name = $this->ledger_model->getDefaultLedgerId($default_expense_id);
            
        $EXPENSE_ary = array(
                        'ledger_name' => 'Expense Account',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Indirect Expenses',
                        'default_value' => '',
                        'amount' => 0
                    );
        
        foreach ($js_data as $key => $value) {
            $item_id = $value['expense_type_id'];
            $exp_ledger_resp = $this->general_model->getRecords('ledger_id,expense_title','expense',array('expense_id' => $item_id));
            if(!empty($exp_ledger_resp)){
                $exp_ledger_id = $exp_ledger_resp[0]->ledger_id;
                $expense_ledger_title = $exp_ledger_resp[0]->expense_title;
            }
            $expense_ledger_title = ucwords(strtolower(trim($expense_ledger_title)));
            if(!$exp_ledger_id || $exp_ledger_id == ''){
                if(!empty($expense_ledger_name)){
                    $expense_ledger_nm = $expense_ledger_name->ledger_name;
                    $expense_ledger_nm = str_ireplace('{{ITEM_NAME}}', $expense_ledger_title , $expense_ledger_nm);
                    $EXPENSE_ary['ledger_name'] = $expense_ledger_nm;
                    $EXPENSE_ary['primary_grp'] = $expense_ledger_name->sub_group_1;
                    $EXPENSE_ary['second_grp'] = $expense_ledger_name->sub_group_2;
                    $EXPENSE_ary['main_grp'] = $expense_ledger_name->main_group;
                    $EXPENSE_ary['default_value'] = $expense_ledger_title;
                    $EXPENSE_ary['default_ledger_id'] = $expense_ledger_name->ledger_id;
                }
                $exp_ledger_id = $this->ledger_model->getGroupLedgerId($EXPENSE_ary);
                /*$exp_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => ucwords(strtolower(trim($expense_ledger))),
                                                'subgrp_1' => '',
                                                'subgrp_2' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' => 0
                                            ));*/
            }

            $vouchers_new[] = array(
                            "ledger_from"              => $exp_ledger_id,
                            "ledger_to"                => $ledgers['ledger_to'] ,
                            "expense_voucher_id"       => '' ,
                            "voucher_amount"           => $value['expense_bill_item_taxable_value'],
                            "converted_voucher_amount" => 0,
                            "dr_amount"                => $value['expense_bill_item_taxable_value'],
                            "cr_amount"                => 0,
                            'ledger_id'                => $exp_ledger_id,
                            'bill_item_id'             => $item_id,
                        );

            $items_ledgers_ary[] = array(
                                    'ledger_id' => $exp_ledger_id,
                                    "item_id" => $item_id
                                    );
        }
        
        $vouchers_new[] = array(
                            "ledger_from"              => $supplier_ledger_id,
                            "ledger_to"                => $ledgers['ledger_to'] ,
                            "expense_voucher_id"         => '' ,
                            "voucher_amount"           => $grand_total ,
                            "converted_voucher_amount" => 0,
                            "dr_amount"                => '',
                            "cr_amount"                => $grand_total ,
                            'ledger_id'                => $supplier_ledger_id
                        );
        /* update supplier amount needs to pay */
        $this->db->set(array('supplier_receivable_amount' => $grand_total));
        $this->db->where('expense_bill_id', $data_main['expense_bill_id']);
        $this->db->update('expense_bill');

        $data_main['supplier_receivable_amount'] = $data_main['expense_bill_grand_total'];

        $sub_total = $data_main['expense_bill_sub_total'];
        $converted_voucher_amount = 0;

        /*$vouchers_new[] = array(
                            "ledger_from"              => $ledgers['ledger_from'],
                            "ledger_to"                => $expense_bill_ledger_id,
                            "expense_voucher_id"         => '' ,
                            "voucher_amount"           => $sub_total ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $sub_total ,
                            "cr_amount"                => '',
                            'ledger_id'                => $expense_bill_ledger_id
                        );*/

        if ($data_main['expense_bill_tds_amount'] > 0){
            foreach ($tds_slab_items as $key => $value) {
                if ($key == 0){
                    continue;
                }
                
                if (in_array($key, $tds_slab_minus)){
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                } else {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                }

                $vouchers_new[] = array(
                                "ledger_from"              => $key,
                                "ledger_to"                => $ledger_to,
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $value ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $key
                            );
            }
        }
        
        if ((($data_main['expense_bill_tax_amount'] > 0  && ($data_main['expense_bill_igst_amount'] > 0 || $data_main['expense_bill_cgst_amount'] > 0 || $data_main['expense_bill_sgst_amount'] > 0)) || $data_main['expense_bill_tax_cess_amount'] > 0 )){
            if ($present != "igst"){
                foreach ($cgst_slab_items as $key => $value) {
                    if (in_array($key , $cgst_slab_minus)){
                        $dr_amount = '';
                        $cr_amount = $value;
                        $ledger_to = $ledgers['ledger_from'];
                    }else{
                        $dr_amount = $value;
                        $cr_amount = '';
                        $ledger_to = $ledgers['ledger_to'];
                    }

                    if ($value > 0){
                        $vouchers_new[] = array(
                                "ledger_from"              => $key,
                                "ledger_to"                => $ledger_to,
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $value ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $key
                            );
                    }
                }
               
                foreach ($sgst_slab_items as $key => $value){
                    if (in_array($key , $sgst_slab_minus)) {
                        $dr_amount = '';
                        $cr_amount = $value;
                        $ledger_to = $ledgers['ledger_from'];
                    }else{
                        $dr_amount = $value;
                        $cr_amount = '';
                        $ledger_to = $ledgers['ledger_to'];
                    }

                    $vouchers_new[] = array(
                                "ledger_from"              => $key,
                                "ledger_to"                => $ledger_to,
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $value ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $key
                            );
                }
            }else {
               
                foreach ($igst_slab_items as $key => $value) {
                    
                    if (in_array($key , $igst_slab_minus)) {
                        $dr_amount = '';
                        $cr_amount = $value;
                        $ledger_to = $ledgers['ledger_from'];
                    }else{
                        $dr_amount = $value;
                        $cr_amount = '';
                        $ledger_to = $ledgers['ledger_to'];
                    }
                    /*$dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_to'];*/

                    $vouchers_new[] = array(
                                "ledger_from"              => $key,
                                "ledger_to"                => $ledgers['ledger_to'],
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $value ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $key
                            );
                }
            }
            if($data_main['expense_bill_tax_cess_amount'] > 0){
                foreach ($cess_slab_items as $key => $value) {

                    if (in_array($key , $cess_slab_minus)) {
                        $dr_amount = '';
                        $cr_amount = $value;
                        $ledger_to = $ledgers['ledger_from'];
                    }else{
                        $dr_amount = $value;
                        $cr_amount = '';
                        $ledger_to = $ledgers['ledger_to'];
                    }

                    $vouchers_new[] = array(
                                "ledger_from"              => $key,
                                "ledger_to"                => $ledgers['ledger_to'],
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $value ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $key
                            );
                }
            }
        }
        else if (($data_main['expense_bill_tax_amount'] > 0 && ($data_main['expense_bill_igst_amount'] == 0 && $data_main['expense_bill_cgst_amount'] == 0 && $data_main['expense_bill_sgst_amount'] == 0)) && $data_main['expense_bill_gst_payable'] != "yes"){
            foreach ($tax_slab_items as $key => $value){

                if (in_array($key , $tax_slab_minus)){
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                }
                else
                {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }

                $vouchers_new[] = array(
                                    "ledger_from"              => $key,
                                    "ledger_to"                => $ledgers['ledger_to'],
                                    "expense_voucher_id"         => '' ,
                                    "voucher_amount"           => $value ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => $dr_amount ,
                                    "cr_amount"                => $cr_amount,
                                    'ledger_id'                => $key
                                );
            }
        }

        if (in_array($charges_sub_module_id , $section_modules['access_sub_modules'])){
            $default_Freight_id = $expense_ledger['Freight'];
            $Freight_ledger_name = $this->ledger_model->getDefaultLedgerId($default_Freight_id);
                
            $Freight_ary = array(
                            'ledger_name' => 'Freight PAID',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Expenses',
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
            /*$freight_charge_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => 'Freight collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_insurance_id = $expense_ledger['Insurance'];
            $insurance_ledger_name = $this->ledger_model->getDefaultLedgerId($default_insurance_id);
                
            $insurance_ary = array(
                            'ledger_name' => 'Insurance Charges PAID',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Expenses',
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
            /*$insurance_charge_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => 'Insurance Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_packing_id = $expense_ledger['Packing'];
            $packing_ledger_name = $this->ledger_model->getDefaultLedgerId($default_packing_id);
                
            $packing_ary = array(
                            'ledger_name' => 'Packing Charges PAID',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Expenses',
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
            /*$packing_charge_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => 'Packing Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_incidental_id = $expense_ledger['Incidental'];
            $incidental_ledger_name = $this->ledger_model->getDefaultLedgerId($default_incidental_id);
                
            $incidental_ary = array(
                            'ledger_name' => 'Incidental Charges PAID',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Expenses',
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
            /*$incidental_charge_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => 'Incidental Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_other_inclusive_id = $expense_ledger['Inclusive'];
            $other_inclusive_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_inclusive_id);
                
            $other_inclusive_ary = array(
                            'ledger_name' => 'Other Inclusive Charges PAID',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Expenses',
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
            /*$other_inclusive_charge_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => 'Other Inclusive Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_other_exclusive_id = $expense_ledger['Exclusive'];
            $other_exclusive_ledger_name = $this->ledger_model->getDefaultLedgerId($default_other_exclusive_id);
                
            $other_exclusive_ary = array(
                            'ledger_name' => 'Other Exclusive Charges PAID',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Income',
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
            /*$other_exclusive_charge_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => 'Other Exclusive Charges collected',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/

            if (isset($freight_charge_ledger_id) && $data_main['freight_charge_amount'] > 0) {

                $vouchers_new[] = array(
                                "ledger_from"              => $freight_charge_ledger_id,
                                "ledger_to"                => $ledgers['ledger_to'],
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['freight_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['freight_charge_amount'],
                                "cr_amount"                => '',
                                'ledger_id'                => $freight_charge_ledger_id
                            );
            } 
            if (isset($insurance_charge_ledger_id) && $data_main['insurance_charge_amount'] > 0){

                $vouchers_new[] = array(
                                "ledger_from"              => $insurance_charge_ledger_id,
                                "ledger_to"                => $ledgers['ledger_to'],
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['insurance_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['insurance_charge_amount'],
                                "cr_amount"                => '',
                                'ledger_id'                => $insurance_charge_ledger_id
                            );
            } 
            if (isset($packing_charge_ledger_id) && $data_main['packing_charge_amount'] > 0){
                $vouchers_new[] = array(
                                "ledger_from"              => $packing_charge_ledger_id ,
                                "ledger_to"                => $ledgers['ledger_to'] ,
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['packing_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['packing_charge_amount'] ,
                                "cr_amount"                => '',
                                'ledger_id'                => $packing_charge_ledger_id
                            );
            } 
            if (isset($incidental_charge_ledger_id) && $data_main['incidental_charge_amount'] > 0) {

                $vouchers_new[] = array(
                                "ledger_from"              => $incidental_charge_ledger_id ,
                                "ledger_to"                => $ledgers['ledger_to'] ,
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['incidental_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['incidental_charge_amount'] ,
                                "cr_amount"                => '',
                                'ledger_id'                => $incidental_charge_ledger_id
                            );
            } 
            if (isset($other_inclusive_charge_ledger_id) && $data_main['inclusion_other_charge_amount'] > 0){

                $vouchers_new[] = array(
                                    "ledger_from"              => $other_inclusive_charge_ledger_id ,
                                    "ledger_to"                => $ledgers['ledger_to'] ,
                                    "expense_voucher_id"         => '' ,
                                    "voucher_amount"           => $data_main['inclusion_other_charge_amount'] ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => $data_main['inclusion_other_charge_amount'] ,
                                    "cr_amount"                => '',
                                    'ledger_id'                => $other_inclusive_charge_ledger_id
                                );

            } 
            if (isset($other_exclusive_charge_ledger_id) && $data_main['exclusion_other_charge_amount'] > 0){

                $vouchers_new[] = array(
                                    "ledger_from"              => $other_exclusive_charge_ledger_id ,
                                    "ledger_to"                => $ledgers['ledger_from'] ,
                                    "expense_voucher_id"         => '' ,
                                    "voucher_amount"           => $data_main['exclusion_other_charge_amount'] ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => '' ,
                                    "cr_amount"                => $data_main['exclusion_other_charge_amount'],
                                    'ledger_id'                => $other_exclusive_charge_ledger_id
                                );
            }
        }

        /* discount slab */
        $discount_sum = 0;
        /*if ($data_main['expense_bill_discount_amount'] > 0){

            $discount_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                    'ledger_name' => 'Trade Discount',
                                                    'subgrp_1' => '',
                                                    'subgrp_2' => '',
                                                    'main_grp' => 'Direct Expenses',
                                                    'amount' =>  0
                                                ));
            $ledgers['discount_ledger_id'] = $discount_ledger_id;

            foreach ($js_data as $key => $value){
                $discount_sum = bcadd($discount_sum , $value['expense_bill_item_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
            }

            $vouchers_new[] = array(
                                "ledger_from"              => $discount_ledger_id,
                                "ledger_to"                => $expense_bill_ledger_id,
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $discount_sum ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => '' ,
                                "cr_amount"                => $discount_sum,
                                'ledger_id'                => $discount_ledger_id
                            );
        }*/
        /* discount slab ends */

        /* Round off */

        if ($data_main['round_off_amount'] > 0 || $data_main['round_off_amount'] < 0){

            $round_off_amount = $data_main['round_off_amount'];

            if ($round_off_amount > 0){
                $round_off_amount = $round_off_amount;
                $dr_amount        = '';
                $cr_amount        = $round_off_amount;

                $ledger_to = $ledgers['ledger_from'];
                $default_roundoff_id = $expense_ledger['RoundOff_Received'];
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
                    'ledger_name' => 'ROUND OFF',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Expenses',
                    'amount' =>  0
                );*/
            }else {
                $round_off_amount = ($round_off_amount * -1);
                $dr_amount        = $round_off_amount;
                $cr_amount        = '';
                $ledger_to = $ledgers['ledger_to'];
                $default_roundoff_id = $expense_ledger['RoundOff_Given'];
                $roundoff_ledger_name = $this->ledger_model->getDefaultLedgerId($default_roundoff_id);
                $round_off_ary = array(
                                'ledger_name' => 'ROUND OFF Given',
                                'second_grp' => '',
                                'primary_grp' => '',
                                'main_grp' => 'Indirect Expensess',
                                'default_ledger_id' => 0,
                                'amount' => 0
                            );
                /*$round_off_ary = array(
                    'ledger_name' => 'ROUND OFF',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Expenses',
                    'amount' =>  0
                );*/
            }

           // $round_off_ledger_id            = $this->ledger_model->addLedger($title , $subgroup);
            $round_off_ledger_id            = $this->ledger_model->addGroupLedger($round_off_ary);

            $ledgers['round_off_ledger_id'] = $round_off_ledger_id;

            $vouchers_new[] = array(
                                "ledger_from"              => $round_off_ledger_id,
                                "ledger_to"                => $ledger_to,
                                "expense_voucher_id"         => '' ,
                                "voucher_amount"           => $round_off_amount ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $round_off_ledger_id
                            );
        }

        $vouchers = array();
        $voucher_keys = array();
        /*if(!empty($vouchers_new)){
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
        }*/

        $resp = array();
        $resp['vouchers'] = $vouchers_new;
        $resp['items_ledgers'] = $items_ledgers_ary;
        /* Round off */
        return $resp;
    }

    public function expense_bill_voucher_entry($data_main , $js_data , $action , $branch){
        $expense_bill_voucher_module_id = $this->config->item('expense_bill_module');
        $module_id               = $expense_bill_voucher_module_id;
        $modules                 = $this->get_modules();
        $privilege               = "view_privilege";
        $section_modules         = $this->get_section_modules($expense_bill_voucher_module_id , $modules , $privilege);

        $access_sub_modules    = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings       = $section_modules['access_settings'];

        /* generated voucher number */

        $resp = $this->expense_bill_vouchers($section_modules , $data_main , $js_data , $branch);
        $vouchers = $resp['vouchers'];
        $item_ledger_ids = $resp['items_ledgers'];

        $grand_total = $data_main['expense_bill_grand_total'];
        /*if ($data_main['expense_bill_gst_payable'] != "yes"){
        } else {
            $total_tax_amount = ($data_main['expense_bill_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount']);
            $grand_total      = bcsub($data_main['expense_bill_grand_total'] , $total_tax_amount,$section_modules['access_common_settings'][0]->amount_precision);
        }*/

        $table           = 'expense_voucher';
        $reference_key   = 'expense_voucher_id';
        $reference_table = 'accounts_expense_voucher';

        if ($action == "add"){
            /* generated voucher number */
            $primary_id      = "expense_voucher_id";
            $table_name      = $this->config->item('expense_voucher_table');
            $date_field_name = "voucher_date";
            $current_date    = $data_main['expense_bill_date'];
            $voucher_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);

            $headers = array(
                "voucher_date"      => $data_main['expense_bill_date'] ,
                "voucher_number"    => $voucher_number ,
                "party_id"          => $data_main['expense_bill_payee_id'] ,
                "party_type"        => $data_main['expense_bill_payee_type'] ,
                "reference_id"      => $data_main['expense_bill_id'] ,
                "reference_type"    => 'expense_bill' ,
                "reference_number"  => $data_main['expense_bill_invoice_number'] ,
                "receipt_amount"    => $grand_total ,
                "from_account"      => $data_main['expense_bill_payee_id'] ,
                "to_account"        => $this->session->userdata('SESS_BRANCH_ID') ,
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "description"       => '' ,
                "added_date"        => date('Y-m-d') ,
                "added_user_id"     => $this->session->userdata('SESS_USER_ID') ,
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                "currency_id"       => $this->session->userdata('SESS_DEFAULT_CURRENCY') ,
                "note1"             => $data_main['note1'] ,
                "note2"             => $data_main['note2']
            );

            $headers['converted_receipt_amount'] = $grand_total;
            
            $this->general_model->addVouchers($table , $reference_key , $reference_table , $headers , $vouchers);
            
        } else if ($action == "edit"){
            $headers = array(
                "voucher_date"      => $data_main['expense_bill_date'] ,
                "party_id"          => $data_main['expense_bill_payee_id'] ,
                "party_type"        => $data_main['expense_bill_payee_type'] ,
                "reference_id"      => $data_main['expense_bill_id'] ,
                "reference_type"    => 'expense_bill' ,
                "reference_number"  => $data_main['expense_bill_invoice_number'] ,
                "receipt_amount"    => $grand_total ,
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                "description"       => '' ,
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID') ,
                "updated_date"      => date('Y-m-d') ,
                "updated_user_id"   => $this->session->userdata('SESS_USER_ID') ,
                "note1"             => $data_main['note1'] ,
                "note2"             => $data_main['note2']
            );

            $headers['converted_receipt_amount'] = $grand_total;
            
            $expense_bill_voucher_data = $this->general_model->getRecords('expense_voucher_id' , 'expense_voucher' , array('reference_id'  => $data_main['expense_bill_id'],'reference_type' => 'expense_bill','delete_status' => 0));

            if ($expense_bill_voucher_data){
                $expense_voucher_id        = $expense_bill_voucher_data[0]->expense_voucher_id;
                $this->general_model->updateData('expense_voucher', $headers,array('expense_voucher_id' => $expense_voucher_id ));
                $string = 'accounts_expense_id,delete_status,ledger_id,voucher_amount,dr_amount,cr_amount';
                $table = 'accounts_expense_voucher';
                $where = array('expense_voucher_id' => $expense_voucher_id);

                $old_expense_bill_voucher_items = $this->general_model->getRecords($string , $table , $where , $order
                    = "");
                $old_expense_bill_ledger_ids = $this->getValues($old_expense_bill_voucher_items,'ledger_id');
                $not_deleted_ids = array();

                foreach ($vouchers as $key => $value) {
                    if (($led_key = array_search($value['ledger_id'], $old_expense_bill_ledger_ids)) !== false) {
                        unset($old_expense_bill_ledger_ids[$led_key]);
                        /*echo "<pre>";
                        print_r($old_expense_bill_voucher_items[$led_key]);*/
                        $accounts_expense_bill_id = $old_expense_bill_voucher_items[$led_key]->accounts_expense_id;
                        array_push($not_deleted_ids,$accounts_expense_bill_id );
                        $value['expense_voucher_id'] = $expense_voucher_id;
                        $value['delete_status']    = 0;
                        $table                     = 'accounts_expense_voucher';
                        $where                     = array('accounts_expense_id' => $accounts_expense_bill_id );
                        $post_data = array('data' => $value,
                                            'where' => $where,
                                            'voucher_date' => $headers['voucher_date'],
                                            'table' => 'expense_voucher',
                                            'sub_table' => 'accounts_expense_voucher',
                                            'primary_id' => 'expense_voucher_id',
                                            'sub_primary_id' => 'expense_voucher_id'
                                        );
                        $this->general_model->updateBunchVoucherCommon($post_data);
                        $this->general_model->updateData($table , $value , $where);
                    }else{
                        $value['expense_voucher_id'] = $expense_voucher_id;
                        $table                     = 'accounts_expense_voucher';
                        $this->general_model->insertData($table , $value);
                    }
                }

                if(!empty($old_expense_bill_voucher_items)){
                    $revert_ary = array();

                    foreach ($old_expense_bill_voucher_items as $key => $value) {
                        if(!in_array($value->accounts_expense_id, $not_deleted_ids)){
                            
                            $revert_ary[] = $value;
                            $table      = 'accounts_expense_voucher';
                            $where      = array('accounts_expense_id' => $value->accounts_expense_id );
                            $expense_bill_data = array('delete_status' => 1 );
                            $this->general_model->updateData($table , $expense_bill_data , $where);
                        }
                    }
                    if(!empty($revert_ary)) $this->general_model->revertLedgerAmount($revert_ary,$headers['voucher_date']);
                }
            }
        }

        /*if (!empty($item_ledger_ids)) {
            $result = $this->db->select('expense_voucher_id')->where('reference_id',$data_main['expense_bill_id'])->where('reference_type','expense_bill')->get('expense_voucher')->row();
            if(!empty($result)){
                foreach ($item_ledger_ids as $key => $value) {
                    $this->db->set('bill_item_id',$value['item_id']);
                    $this->db->where('expense_voucher_id',$result->expense_voucher_id);
                    $this->db->where('ledger_id',$value['ledger_id']);
                    $this->db->where('delete_status',0);
                    $this->db->update('accounts_expense_voucher');
                    print_r($this->db->last_query());
                    echo "<br>";
                }
            }
        }*/
        
    }

    public function edit($id){
        $id                 = $this->encryption_url->decode($id);
        $data               = $this->get_default_country_state();
        $expense_bill_module_id = $this->config->item('expense_bill_module');
        $modules            = $this->modules;
        $privilege          = "edit_privilege";
        $data['privilege']  = "edit_privilege";
        $section_modules    = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['expense_bill_module_id']    = $expense_bill_module_id;
        $data['module_id']             = $expense_bill_module_id;
        $data['notes_module_id']       = $this->config->item('notes_module');
        $data['product_module_id']     = $this->config->item('expense_module');
        $data['supplier_module_id']    = $this->config->item('supplier_module');
        $data['tax_module_id']         = $this->config->item('tax_module');
        $data['discount_module_id']    = $this->config->item('discount_module');
        $data['accounts_module_id']    = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');

        $data['data'] = $this->general_model->getRecords('*', 'expense_bill', array(
            'expense_bill_id' => $id));

        $product     = 1;
        $description = 1;

        $data['product_exist'] = $product;

        $data['supplier'] = $this->supplier_call();
     
        if ($data['data'][0]->expense_bill_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax"){

            $data['tax'] = $this->tax_call();
        }

        $expense_bill_product_items = array();
        $product_items          = $this->common->expense_bill_items_product_list_field($id);
        $data['items'] = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
          
        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->expense_bill_tax_amount > 0 && $data['data'][0]->expense_bill_igst_amount > 0 && ($data['data'][0]->expense_bill_cgst_amount == 0 && $data['data'][0]->expense_bill_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->expense_bill_tax_amount > 0 && ($data['data'][0]->expense_bill_cgst_amount > 0 || $data['data'][0]->expense_bill_sgst_amount > 0) && $data['data'][0]->expense_bill_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->expense_bill_tax_amount > 0 && ($data['data'][0]->expense_bill_igst_amount == 0 && $data['data'][0]->expense_bill_cgst_amount == 0 && $data['data'][0]->expense_bill_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->expense_bill_tax_amount == 0 && ($data['data'][0]->expense_bill_igst_amount == 0 && $data['data'][0]->expense_bill_cgst_amount == 0 && $data['data'][0]->expense_bill_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->expense_bill_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }

        if ($data['data'][0]->expense_bill_tds_amount > 0 || $data['access_settings'][0]->tds_visible == "yes")
        {
            /* Discount */
            $tdsExist = 1;
        }

        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if($data['data'][0]->expense_bill_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->expense_bill_billing_state_id);

        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['cess_exist']        = $cess_exist;
        $data['tax_exist']         = $taxExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        $this->load->view('expense_bill/edit', $data);

    }

    public function edit_expense_bill(){
        $data            = $this->get_default_country_state();
        $expense_bill_id        = $this->input->post('expense_bill_id');
        $expense_bill_module_id = $this->config->item('expense_bill_module');
        $module_id       = $expense_bill_module_id;
        $modules         = $this->modules;
        $privilege       = "edit_privilege";
        $section_modules = $this->get_section_modules($expense_bill_module_id , $modules , $privilege);


        /* Modules Present */
        $data['expense_bill_module_id']           = $expense_bill_module_id;
        $data['module_id']                 = $expense_bill_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('product_module');
        $data['service_module_id']         = $this->config->item('service_module');
        $data['supplier_module_id']        = $this->config->item('supplier_module');
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


        $currency = $this->input->post('currency_id');
        if ($section_modules['access_settings'][0]->invoice_creation == "automatic")
        {
            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old'))
            {
                $primary_id      = "expense_bill_id";
                $table_name      = $this->config->item('expense_bill_table');
                $date_field_name = "expense_bill_date";
                $current_date    = date('Y-m-d',strtotime($this->input->post('invoice_date')));
                $invoice_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);
            }
            else
            {
                $invoice_number = $this->input->post('invoice_number');
            }
        }
        else
        {
            $invoice_number = $this->input->post('invoice_number');
        }
        $total_cess_amnt= $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0 ;

        if (isset($_FILES["expense_file"]["name"]) && $_FILES["expense_file"]["name"] != ""){
            $path_parts = pathinfo($_FILES["expense_file"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Expense')){
                mkdir('./assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Expense', 0777, TRUE);
            } 
            $url = 'assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Expense/'.$image_path;
            if (in_array($path_parts['extension'], array("JPG","jpg","jpeg","JPEG","PNG","png","pdf","PDF" ))){
                if (is_uploaded_file($_FILES["expense_file"]["tmp_name"])){
                    if (move_uploaded_file($_FILES["expense_file"]["tmp_name"], $url)){
                        $image_name = $image_path;
                    }
                }
            }
        }else{
            $image_name = $this->input->post('hidden_expense_file');
        }

        $expense_bill_data = array(
            "expense_bill_date"                            => date('Y-m-d',strtotime($this->input->post('invoice_date'))) ,
            "expense_bill_invoice_number"                  => $invoice_number ,
            "expense_bill_supplier_invoice_number"      => $this->input->post('supplier_ref'),
            "expense_bill_supplier_date"                => ($this->input->post('supplier_date') != '' ? date('Y-m-d', strtotime($this->input->post('supplier_date'))) : ''),
            "expense_bill_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
            "expense_bill_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
            "expense_bill_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
            "expense_bill_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
            "expense_bill_tax_cess_amount"                 => 0 ,
            "expense_bill_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
            "expense_bill_tds_amount"                      => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0 ,
            "expense_bill_igst_amount"                     => 0 ,
            "expense_bill_cgst_amount"                     => 0 ,
            "expense_bill_sgst_amount"                     => 0 ,
            /*"from_account"                          => 'supplier' ,
            "to_account"                            => 'expense_bill' ,*/
            "expense_bill_paid_amount"                  => 0 ,
            /*"expense_bill_supplier_invoice_number"      => $this->input->post('supplier_ref'),
            "expense_bill_supplier_date"                => date('Y-m-d', strtotime($this->input->post('supplier_date'))),
            "expense_bill_delivery_challan_number"      => $this->input->post('delivery_challan_number'),
            "expense_bill_delivery_date"                => date('Y-m-d', strtotime($this->input->post('delivery_date'))),
            "expense_bill_e_way_bill_number"            => $this->input->post('e_way_bill'),
            "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,*/
            "expense_bill_payee_id"                        => $this->input->post('supplier') ,
            "expense_bill_payee_type"                      => "supplier" ,
            
            /*"expense_bill_order_number"                    => $this->input->post('order_number') ,
            "expense_bill_order_date"                    => date('Y-m-d', strtotime($this->input->post('expense_bill_order_date'))) ,
            "expense_bill_received_via"                => $this->input->post('received_via'),
            "expense_bill_grn_number"                   => $this->input->post('grn_number'),
            "expense_bill_grn_date"                     => $this->input->post('grn_date'),*/
            "expense_bill_type_of_supply"                  => $this->input->post('type_of_supply') ,
            "expense_bill_gst_payable"                     => $this->input->post('gst_payable') ,
            "expense_bill_billing_country_id"              => $this->input->post('billing_country') ,
            "expense_bill_billing_state_id"                =>$this->input->post('billing_state') ,
            "branch_id"                             => $this->session->userdata('SESS_BRANCH_ID') ,
            "currency_id"                           => $this->input->post('currency_id') ,
            "updated_date"                          => date('Y-m-d') ,
            "updated_user_id"                       => $this->session->userdata('SESS_USER_ID') ,
            /*"transporter_name"                      => $this->input->post('transporter_name') ,
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
            "payment_mode"                          => $this->input->post('payment_mode') ,*/
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
            "note2"                                 => $this->input->post('note2'),
            "expense_file" => $image_name
        );

        $expense_bill_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $expense_bill_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $expense_bill_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $expense_bill_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $expense_bill_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $expense_bill_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        $round_off_value = $expense_bill_data['expense_bill_grand_total'];

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" || $this->input->post('round_off_key') == "yes"){
            if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 ){
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $expense_bill_data['round_off_amount'] = bcsub($expense_bill_data['expense_bill_grand_total'] , $round_off_value,$section_modules['access_common_settings'][0]->amount_precision);

        $expense_bill_data['expense_bill_grand_total'] = $round_off_value;

        $expense_bill_data['supplier_receivable_amount'] = $expense_bill_data['expense_bill_grand_total'];
        if (isset($expense_bill_data['expense_bill_tds_amount']) && $expense_bill_data['expense_bill_tds_amount'] > 0){
            $expense_bill_data['supplier_receivable_amount'] = bcsub($expense_bill_data['expense_bill_grand_total'], $expense_bill_data['expense_bill_tds_amount']);
        }

        $tax_type         = $this->input->post('tax_type');
        $expense_bill_tax_amount = $expense_bill_data['expense_bill_tax_amount'];
        $expense_bill_tax_amount = $expense_bill_data['expense_bill_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));
        if ($tax_type == "gst"){
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

            if ($expense_bill_data['expense_bill_type_of_supply'] != 'import'){
                if ($expense_bill_data['expense_bill_type_of_supply'] == 'intra_state'){
                    $expense_bill_data['expense_bill_igst_amount'] = 0;
                    $expense_bill_data['expense_bill_cgst_amount'] = ($expense_bill_tax_amount * $cgst_amount_percentage) / 100;
                    $expense_bill_data['expense_bill_sgst_amount'] = ($expense_bill_tax_amount * $sgst_amount_percentage) / 100;
                    $expense_bill_data['expense_bill_tax_cess_amount'] = $total_cess_amnt;
                }else{
                    $expense_bill_data['expense_bill_igst_amount'] = $expense_bill_tax_amount;
                    $expense_bill_data['expense_bill_cgst_amount'] = 0;
                    $expense_bill_data['expense_bill_sgst_amount'] = 0;
                    $expense_bill_data['expense_bill_tax_cess_amount'] = $total_cess_amnt;
                }
            }
            /*else
            {
                if ($expense_bill_data['expense_bill_type_of_supply'] == "export_with_payment")
                {
                    $expense_bill_data['expense_bill_igst_amount'] = $expense_bill_tax_amount;
                    $expense_bill_data['expense_bill_cgst_amount'] = 0;
                    $expense_bill_data['expense_bill_sgst_amount'] = 0;
                    $expense_bill_data['expense_bill_tax_cess_amount'] = $total_cess_amnt;
                }
            }*/
        }

        $expense_bill_data['currency_converted_amount'] = $expense_bill_data['expense_bill_grand_total'];
        $data_main   = array_map('trim' , $expense_bill_data);
        $expense_bill_table = $this->config->item('expense_bill_table');
        $where       = array(
            'expense_bill_id' => $expense_bill_id );
        
        if ($this->general_model->updateData($expense_bill_table , $data_main , $where)){
            $successMsg = 'Expense Bill Updated Successfully';
            $this->session->set_flashdata('expence_bill_success',$successMsg);
                $log_data              = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $expense_bill_id ,
                'table_name'        => $expense_bill_table ,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'expense_bill Updated' );
            $data_main['expense_bill_id'] = $expense_bill_id;
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $expense_bill_item_data       = $this->input->post('table_data');

            $js_data               = json_decode($expense_bill_item_data);
            $js_data               = array_reverse($js_data);
            $item_table            = $this->config->item('expense_bill_item_table');
            if (!empty($js_data)) {
                $js_data1 = array();
                $new_item_ids = $this->getValues($js_data,'item_id'); 
                
                $string          = 'expense_bill_item_id,expense_bill_item_quantity,expense_type_id';
                $table           =  $item_table;
                $where           = array(
                    'expense_bill_id' => $expense_bill_id ,
                    'delete_status' => 0 );
                $old_expense_bill_items = $this->general_model->getRecords($string , $table , $where , $order           = "");
                $old_item_ids = $this->getValues($old_expense_bill_items,'expense_type_id');
                $not_deleted_ids= array();
                foreach ($js_data as $key => $value) {
                    if ($value != null) {
                        $item_id   = $value->item_id;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "expense_type_id"                    => $value->item_id ,
                            "expense_bill_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "expense_bill_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "expense_bill_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "expense_bill_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "expense_bill_item_discount_amount" => $value->item_discount_amount ? (float) $value->item_discount_amount : 0 ,
                            "expense_bill_item_discount_id"     => $value->item_discount_id ? (float) $value->item_discount_id : 0 ,
                            "expense_bill_item_tds_id"          => $value->item_tds_id ? (float) $value->item_tds_id : 0 ,
                            "expense_bill_item_tds_percentage"  => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ,
                            "expense_bill_item_tds_amount"      => $value->item_tds_amount ? (float) $value->item_tds_amount : 0 ,
                            "expense_bill_item_grand_total"     => $value->item_grand_total ? (float) $value->item_grand_total : 0 ,
                            "expense_bill_item_tax_id"          => $value->item_tax_id ? (float)$value->item_tax_id : 0 ,
                            "expense_bill_item_tax_cess_id"     => $value->item_tax_cess_id ? (float)$value->item_tax_cess_id : 0 ,
                            "expense_bill_item_igst_percentage" => 0 ,
                            "expense_bill_item_igst_amount"     => 0 ,
                            "expense_bill_item_cgst_percentage" => 0 ,
                            "expense_bill_item_cgst_amount"     => 0 ,
                            "expense_bill_item_sgst_percentage" => 0 ,
                            "expense_bill_item_sgst_amount"     => 0 ,
                            "expense_bill_item_tax_percentage"  => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ,
                            "expense_bill_item_tax_amount"      => $value->item_tax_amount ? (float) $value->item_tax_amount : 0 ,
                            "expense_bill_item_tax_cess_percentage"  =>  0 ,
                            "expense_bill_item_tax_cess_amount"      =>  0 ,
                            "expense_bill_item_description"     => $value->item_description ? $value->item_description : "" ,
                            "expense_bill_id"                   => $expense_bill_id );

                        $expense_bill_item_tax_amount     = $item_data['expense_bill_item_tax_amount'];
                        $expense_bill_item_tax_percentage = $item_data['expense_bill_item_tax_percentage'];

                        if ($tax_type == "gst") {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0 ;

                            if ($data_main['expense_bill_type_of_supply'] != 'import'){

                                if ($data['branch'][0]->branch_state_id == $expense_bill_data['expense_bill_billing_state_id'])
                                {
                                    $item_data['expense_bill_item_igst_amount'] = 0;
                                    $item_data['expense_bill_item_cgst_amount'] = ($expense_bill_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_sgst_amount'] = ($expense_bill_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['expense_bill_item_igst_percentage'] = 0;
                                    $item_data['expense_bill_item_cgst_percentage'] = ($expense_bill_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_sgst_percentage'] = ($expense_bill_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['expense_bill_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                                else
                                {
                                    $item_data['expense_bill_item_igst_amount'] = $expense_bill_item_tax_amount;
                                    $item_data['expense_bill_item_cgst_amount'] = 0;
                                    $item_data['expense_bill_item_sgst_amount'] = 0;
                                    $item_data['expense_bill_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['expense_bill_item_igst_percentage'] = $expense_bill_item_tax_percentage;
                                    $item_data['expense_bill_item_cgst_percentage'] = 0;
                                    $item_data['expense_bill_item_sgst_percentage'] = 0;
                                    $item_data['expense_bill_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }/*else{
                                if ($expense_bill_data['expense_bill_type_of_supply'] == "export_with_payment"){
                                    $item_data['expense_bill_item_igst_amount'] = $expense_bill_item_tax_amount;
                                    $item_data['expense_bill_item_cgst_amount'] = 0;
                                    $item_data['expense_bill_item_sgst_amount'] = 0;
                                    $item_data['expense_bill_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['expense_bill_item_igst_percentage'] = $expense_bill_item_tax_percentage;
                                    $item_data['expense_bill_item_cgst_percentage'] = 0;
                                    $item_data['expense_bill_item_sgst_percentage'] = 0;
                                    $item_data['expense_bill_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }*/
                        }
                        
                        $table = 'expense_bill_item';
                        if (($item_key = array_search($value->item_id, $old_item_ids)) !== false) {
                            unset($old_item_ids[$item_key]);
                            $expense_bill_item_id = $old_expense_bill_items[$item_key]->expense_bill_item_id;
                            array_push($not_deleted_ids,$expense_bill_item_id );
                            $where = array('expense_bill_item_id' => $expense_bill_item_id );
                            $this->general_model->updateData($table , $item_data , $where);

                        }else{
                            $this->general_model->insertData($table , $item_data);
                        }
                       
                        $data_item  = array_map('trim' , $item_data);
                        $js_data1[] = $data_item;
                    }
                }

                if(!empty($old_expense_bill_items)){
                    foreach ($old_expense_bill_items as $key => $items) {
                        if(!in_array( $items->expense_bill_item_id,$not_deleted_ids)){
                            $table      = 'expense_bill_item';
                            $where      = array(
                                'expense_bill_item_id' => $items->expense_bill_item_id );
                            $expense_bill_data = array(
                                'delete_status' => 1 );
                            $this->db->where($where);
                            $this->db->delete($table);
                            /*$this->general_model->updateData($table , $expense_bill_data , $where);*/
                        }
                    }
                }
                $item_data = $js_data1;
                
                if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){

                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){

                        $action = "edit";
                        $this->expense_bill_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                    }
                }
            } 
            redirect('expense_bill' , 'refresh');
        }
        else{
            $errorMsg = 'Expense Bill Update Unsuccessful';
            $this->session->set_flashdata('expence_bill_error',$errorMsg);
            redirect('expense_bill' , 'refresh');
        }
    }

    public function get_expense()
    {
        $expense = $this->general_model->getRecords("*", "expense", [
                "delete_status" => 0,
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
        echo json_encode($expense);
    }

    public function get_tds()
    {
        $expense_id = $this->input->post("expense_id");
        $tds        = $this->general_model->getRecords("expense_tds", "expense", [
                "delete_status" => 0,
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID'),
                "expense_id"    => $expense_id ]);
        echo json_encode($tds);
    }
   
    public function view($id)
    {
        $id                 = $this->encryption_url->decode($id);
        $data               = $this->get_default_country_state();
        $expense_bill_module_id = $this->config->item('expense_bill_module');
        $modules            = $this->modules;
        $privilege          = "view_privilege";
        $data['privilege']  = "view_privilege";
        $section_modules    = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['expense_bill_module_id']    = $expense_bill_module_id;
        $data['module_id']                 = $expense_bill_module_id;
        $data['payment_voucher_module_id'] = $this->config->item('payment_voucher_module');
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('expense_module');
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');

        /* Modules Present */
        /*$data['expense_bill_module_id']= $expense_bill_module_id;
        $data['module_id']             = $expense_bill_module_id;
        $data['notes_module_id']       = $this->config->item('notes_module');
        $data['product_module_id']     = $this->config->item('expense_module');
        $data['supplier_module_id']    = $this->config->item('supplier_module');
        $data['tax_module_id']         = $this->config->item('tax_module');
        $data['discount_module_id']    = $this->config->item('discount_module');
        $data['accounts_module_id']    = $this->config->item('accounts_module');
        /* Sub Modules Present */
        /*$data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');*/
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
        } 
        $expense_bill_data = $this->common->expense_bill_list_field1($id);
        $data['data']      = $this->general_model->getJoinRecords($expense_bill_data['string'], $expense_bill_data['table'], $expense_bill_data['where'], $expense_bill_data['join']);
        $string            = "ei.*,e.*";
        $from              = "expense_bill_item ei";
        $join              = [
                'expense e' => "ei.expense_type_id = e.expense_id" ];
        $where             = [
                "ei.delete_status"   => 0,
                "ei.expense_bill_id" => $id ];
        $data['items']     = $this->general_model->getJoinRecords($string, $from, $where, $join);
        $data['expense']   = $this->general_model->getRecords("*", "expense", [
                "delete_status" => 0,
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $data['supplier']  = $this->general_model->getRecords("*", "supplier", [
                "delete_status" => 0,
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $data['currency']  = $this->currency_call();
        $igst              = 0;
        $cgst              = 0;
        $sgst              = 0;
        $dpcount           = 0;
        $discountExist = 0;
        $cess_exist = 0;

        foreach ($data['items'] as $value){
            $igst = bcadd($igst, $value->expense_bill_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->expense_bill_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->expense_bill_item_sgst_amount, 2);
            if ($value->expense_bill_item_description != "" && $value->expense_bill_item_description != null) {
                $dpcount++;
            }
            if ($data['data'][0]->expense_bill_discount_amount > 0){
                /* Discount */
                $discountExist = 1;
            }
        } 

        if($data['data'][0]->expense_bill_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->expense_bill_billing_state_id);

        $data['igst_exist'] = $igst;
        $data['cgst_exist'] = $cgst;
        $data['sgst_exist'] = $sgst;
        $data['dpcount']  = $dpcount;
        $data['cess_exist'] = $cess_exist;
        $data['discount_exist'] = $discountExist;
        $data['is_utgst'] = $is_utgst;
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        /*echo "<pre>";
        print_r($data);
        exit();*/
        $this->load->view("expense_bill/view", $data);
    }



    public function pdf($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $branch_data                     = $this->common->branch_field();
        $data['branch']                  = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data                    = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']                 = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                      = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state']                   = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data                       = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']                    = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $expense_bill_module_id          = $this->config->item('expense_bill_module');
        $data['module_id']               = $expense_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $data['expense_bill_module_id']    = $expense_bill_module_id;
        $data['module_id']                 = $expense_bill_module_id;
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('expense_module');
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
        ob_start();
        $html                            = ob_get_clean();
        $html                            = utf8_encode($html);
        $data['currency']                = $this->currency_call();
        $expense_bill_data               = $this->common->expense_bill_list_field1($id);
        $data['data']                    = $this->general_model->getJoinRecords($expense_bill_data['string'], $expense_bill_data['table'], $expense_bill_data['where'], $expense_bill_data['join']);

        /*echo'<pre>';print_r($data['data']);exit;*/
        $string                          = "ei.*,e.*";
        $from                            = "expense_bill_item ei";
        $join                            = ['expense e' => "ei.expense_type_id = e.expense_id" ];
        $where                           = ["ei.delete_status"   => 0,"ei.expense_bill_id" => $id ];
        $data['items']                   = $this->general_model->getJoinRecords($string, $from, $where, $join);
        $data['expense']                 = $this->general_model->getRecords("*", "expense", ["delete_status" => 0,
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $data['supplier']                = $this->general_model->getRecords("*", "supplier", ["delete_status" => 0,"branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $note_data                       = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $igst                            = 0;
        $cgst                            = 0;
        $sgst                            = 0;
        $dpcount                         = 0;
        $dtcount                         = 0;
        $tdscount                        = 0;
        $cess_exist = 0;
         $discountExist = 0;
        foreach ($data['items'] as $value)
        {
            $igst = bcadd($igst, $value->expense_bill_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->expense_bill_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->expense_bill_item_sgst_amount, 2);
            if ($value->expense_bill_item_description != "" && $value->expense_bill_item_description != null)
            {
                $dpcount++;
            } if ($value->expense_bill_item_tds_amount != "" && $value->expense_bill_item_tds_amount != null && $value->expense_bill_item_tds_amount != 0)
            {
                $tdscount++;
            }
            if ($data['data'][0]->expense_bill_discount_amount > 0){
                /* Discount */
                $discountExist = 1;
            }
        } 
        if($data['data'][0]->expense_bill_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->expense_bill_billing_state_id);

        $data['igst_exist'] = $igst;
        $data['cgst_exist'] = $cgst;
        $data['sgst_exist'] = $sgst;
        $data['dpcount']  = $dpcount;
        $data['cess_exist'] = $cess_exist;
        $data['discount_exist'] = $discountExist;
        $data['is_utgst'] = $is_utgst;
        
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $data['currency_text']   = $currency[0]->currency_name;
        $data['currency_symbol_pdf']   = $currency[0]->currency_symbol_pdf;
        $data['data'][0]->unit = $currency[0]->unit;
        $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
        
        $data['dtcount']   = $dtcount;
        $data['tdscount']  = $tdscount;
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];

        $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                'module_id' => 2,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);

        $pdf_json            = $pdf[0]->pdf_settings;
        $rep                 = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);       

        $html                           = $this->load->view('expense_bill/pdf1', $data, true);
        include APPPATH . "third_party/dompdf/autoload.inc.php";
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();

        //we test first.

        //included.

        //now we can use all methods of dompdf
        //first im giving our html text to this method.
        $dompdf->load_html($html);

        $paper_size  = 'a4';
        $orientation = 'portrait';

        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size, $orientation);

        //and getting rend
        $dompdf->render();

        $dompdf->stream($data['data'][0]->expense_bill_invoice_number, array(
            'Attachment' => 0));
        /*include(APPPATH . 'third_party/mpdf60/mpdf.php');
        $mpdf                           = new mPDF();
        $mpdf->allow_charset_conversion = true;
        $mpdf->charset_in               = 'UTF-8';
        $mpdf->WriteHTML($html);
        $mpdf->Output($data['data'][0]->expense_bill_invoice_number . '.pdf', 'I');*/
    }

    public function delete()
    {
        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        $expense_bill_module_id          = $this->config->item('expense_bill_module');
        $data['module_id']               = $expense_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        $data                   = array_merge($data, $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        if (isset($data['other_modules_present']['accounts_module_id']))
        {
            foreach ($data['access_sub_modules'] as $key => $value)
            {
                if (isset($data['accounts_sub_module_id']))
                {
                    if ($data['accounts_sub_module_id'] == $value->sub_module_id)
                    {
                        /* delete voucher from payment table */
                        $expense_voucher_id = $this->general_model->getRecords('expense_voucher_id', 'expense_voucher', array(
                            'reference_id'   => $id, 'reference_type' => 'expense_bill'));

                        if(!empty($expense_voucher_id)){
                            $this->general_model->deleteCommonVoucher(array('table' => 'expense_voucher', 'where' => array('expense_voucher_id' =>$expense_voucher_id[0]->expense_voucher_id)),array('table' => 'accounts_expense_voucher', 'where' => array('expense_voucher_id' =>$expense_voucher_id[0]->expense_voucher_id)));
                        }

                        /*$this->general_model->updateData('expense_voucher', array(
                                'delete_status' => 1 ), array(
                                'reference_id'   => $id,
                                'reference_type' => 'expense_bill' ));
                        $expense_voucher_id = $this->general_model->getRecords('expense_voucher_id', 'expense_voucher', array(
                                'reference_id'   => $id,
                                'reference_type' => 'expense_bill' ));
                        $this->general_model->updateData('accounts_expense_voucher', array(
                                'delete_status' => 1 ), array(
                                'expense_voucher_id' => $expense_voucher_id[0]->expense_voucher_id ));*/
                    }
                }
            }
        } 
        /*$this->general_model->updateData('expense_voucher', array(
                'delete_status' => 1 ), array(
                'reference_id'   => $id,
                'reference_type' => 'expense_bill' ));*/
        $expense_voucher_id = $this->general_model->getRecords('expense_voucher_id', 'expense_voucher', array(
                'reference_id'   => $id,
                'reference_type' => 'expense_bill' ));
        /*print_r($this->db->last_query());
        print_r($expense_voucher_id);exit();*/
        foreach ($expense_voucher_id as $key => $value)
        {
            $this->general_model->deleteCommonVoucher(array('table' => 'expense_voucher', 'where' => array('expense_voucher_id' =>$value->expense_voucher_id)),array('table' => 'accounts_expense_voucher', 'where' => array('expense_voucher_id' =>$value->expense_voucher_id)));
            /*$this->general_model->updateData('accounts_payment_voucher', array(
                    'delete_status' => 1 ), array(
                    'payment_voucher_id' => $value->payment_id ));*/
        } 
        
        $where              = "reference_id like '%," . $id . "%' or reference_id like '%" . $id . ",%' and reference_type='expense' and delete_status=0";
        $payment_voucher_id = $this->general_model->getRecords('*', 'payment_voucher', $where);
        foreach ($payment_voucher_id as $key => $value)
        {
            $old_reference_id = explode(',', $value->reference_id);
            $i                = 0;
            foreach ($old_reference_id as $k => $val)
            {
                if ($val == $id)
                    break;
                else
                    $i++;
            } $j                = 0;
            $k                = 0;
            $new_reference_id = '';
            foreach ($old_reference_id as $k1 => $val1)
            {
                if ($j == 0)
                {
                    if ($j != $i)
                    {
                        $new_reference_id .= $val1;
                    }
                    else
                    {
                        $k = 1;
                    }
                }
                else
                {
                    if ($j != $i)
                    {
                        if ($k == 1)
                        {
                            $new_reference_id .= $val1;
                        }
                        else
                        {
                            $new_reference_id .= ',' . $val1;
                        }
                    }
                } $j++;
            } $old_reference_number = explode(',', $value->reference_number);
            $j                    = 0;
            $k                    = 0;
            $new_reference_number = '';
            foreach ($old_reference_number as $k1 => $val1)
            {
                if ($j == 0)
                {
                    if ($j != $i)
                    {
                        $new_reference_number .= $val1;
                    }
                    else
                    {
                        $k = 1;
                    }
                }
                else
                {
                    if ($j != $i)
                    {
                        if ($k == 1)
                        {
                            $new_reference_number .= $val1;
                        }
                        else
                        {
                            $new_reference_number .= ',' . $val1;
                        }
                    }
                } $j++;
            } $old_receipt_amount = explode(',', $value->receipt_amount);
            $j                  = 0;
            $k                  = 0;
            $receipt_amount     = 0;
            $new_receipt_amount = '';
            foreach ($old_receipt_amount as $k1 => $val1)
            {
                if ($j == 0)
                {
                    if ($j != $i)
                    {
                        $new_receipt_amount .= $val1;
                    }
                    else
                    {
                        $k              = 1;
                        $receipt_amount = $val1;
                    }
                }
                else
                {
                    if ($j != $i)
                    {
                        if ($k == 1)
                        {
                            $new_receipt_amount .= $val1;
                        }
                        else
                        {
                            $new_receipt_amount .= ',' . $val1;
                        }
                    }
                    else
                    {
                        $receipt_amount = $val1;
                    }
                } $j++;
            } $old_invoice_total = explode(',', $value->invoice_total);
            $j                 = 0;
            $k                 = 0;
            $new_invoice_total = '';
            foreach ($old_invoice_total as $k1 => $val1)
            {
                if ($j == 0)
                {
                    if ($j != $i)
                    {
                        $new_invoice_total .= $val1;
                    }
                    else
                    {
                        $k = 1;
                    }
                }
                else
                {
                    if ($j != $i)
                    {
                        if ($k == 1)
                        {
                            $new_invoice_total .= $val1;
                        }
                        else
                        {
                            $new_invoice_total .= ',' . $val1;
                        }
                    }
                } $j++;
            } $old_invoice_paid_amount = explode(',', $value->invoice_paid_amount);
            $j                       = 0;
            $k                       = 0;
            $new_invoice_paid_amount = '';
            foreach ($old_invoice_paid_amount as $k1 => $val1)
            {
                if ($j == 0)
                {
                    if ($j != $i)
                    {
                        $new_invoice_paid_amount .= $val1;
                    }
                    else
                    {
                        $k = 1;
                    }
                }
                else
                {
                    if ($j != $i)
                    {
                        if ($k == 1)
                        {
                            $new_invoice_paid_amount .= $val1;
                        }
                        else
                        {
                            $new_invoice_paid_amount .= ',' . $val1;
                        }
                    }
                } $j++;
            } $old_invoice_balance_amount = explode(',', $value->invoice_balance_amount);
            $j                          = 0;
            $k                          = 0;
            $new_invoice_balance_amount = '';
            foreach ($old_invoice_balance_amount as $k1 => $val1)
            {
                if ($j == 0)
                {
                    if ($j != $i)
                    {
                        $new_invoice_balance_amount .= $val1;
                    }
                    else
                    {
                        $k = 1;
                    }
                }
                else
                {
                    if ($j != $i)
                    {
                        if ($k == 1)
                        {
                            $new_invoice_balance_amount .= $val1;
                        }
                        else
                        {
                            $new_invoice_balance_amount .= ',' . $val1;
                        }
                    }
                } $j++;
            } $receipt_grand_total  = bcsub($value->receipt_grand_total, $receipt_amount, 2);
            $payment_voucher_data = array(
                    'reference_id'           => $new_reference_id,
                    'reference_number'       => $new_reference_number,
                    'receipt_amount'         => $new_receipt_amount,
                    'receipt_grand_total'    => $receipt_grand_total,
                    'invoice_total'          => $new_invoice_total,
                    'invoice_paid_amount'    => $new_invoice_paid_amount,
                    'invoice_balance_amount' => $new_invoice_balance_amount );
            $this->general_model->updateData('payment_voucher', $payment_voucher_data, array(
                    'payment_id' => $value->payment_id ));
            $accounts_payment     = $this->general_model->getRecords('*', 'accounts_payment_voucher', array(
                    'payment_voucher_id' => $value->payment_id,
                    'delete_status'      => 0 ));
            $data1                = array(
                    'payment_voucher_id' => $value->payment_id,
                    'ledger_from'        => $accounts_payment[0]->ledger_from,
                    'ledger_to'          => $accounts_payment[0]->ledger_to,
                    'voucher_amount'     => $receipt_grand_total,
                    'dr_amount'          => $receipt_grand_total,
                    'cr_amount'          => "0.00" );
            $data2                = array(
                    'payment_voucher_id' => $value->payment_id,
                    'ledger_from'        => $accounts_payment[1]->ledger_from,
                    'ledger_to'          => $accounts_payment[1]->ledger_to,
                    'voucher_amount'     => $receipt_grand_total,
                    'dr_amount'          => "0.00",
                    'cr_amount'          => $receipt_grand_total );
            $this->general_model->updateData('accounts_payment_voucher', $data1, array(
                    'accounts_payment_id' => $accounts_payment[0]->accounts_payment_id ));
            $this->general_model->updateData('accounts_payment_voucher', $data2, array(
                    'accounts_payment_id' => $accounts_payment[1]->accounts_payment_id ));
        } 

        if ($this->general_model->updateData('expense_bill', array(
                        'delete_status' => 1 ), array(
                        'expense_bill_id' => $id )))
        {
            $successMsg = 'Expense Bill Deleted Successfully';
            $this->session->set_flashdata('expence_bill_success',$successMsg);
            $this->general_model->updateData('expense_bill_item', array(
                    'delete_status' => 1 ), array(
                    'expense_bill_id' => $id ));
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'expense_bill',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Expense Bill Deleted' );
            $this->general_model->insertData('log', $log_data);

            $redirect = 'expense_bill';
            if($this->input->post('delete_redirect') != '') $redirect = $this->input->post('delete_redirect');
           
            redirect($redirect , 'refresh');
           
        }
        else
        {
            $errorMsg = 'Expense Bill Delete Unsuccessful';
            $this->session->set_flashdata('expence_bill_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Category can not be Deleted.');
            redirect("expense_bill", 'refresh');
        }
    }

    public function email($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $expense_bill_module_id          = $this->config->item('expense_bill_module');
        $data['module_id']               = $expense_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $data['expense_bill_module_id']    = $expense_bill_module_id;
        $data['module_id']                 = $expense_bill_module_id;
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('expense_module');
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
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
        } 

        if (in_array($data['email_sub_module_id'], $data['access_sub_modules'])){
                        
            ob_start();
            $html              = ob_get_clean();
            $html              = utf8_encode($html);
            $data['currency']  = $this->currency_call();
            $expense_bill_data = $this->common->expense_bill_list_field1($id);
            $data['data']      = $this->general_model->getJoinRecords($expense_bill_data['string'], $expense_bill_data['table'], $expense_bill_data['where'], $expense_bill_data['join']);
            $string            = "ei.*,e.*";
            $from              = "expense_bill_item ei";
            $join              = [
                    'expense e' => "ei.expense_type_id = e.expense_id" ];
            $where             = [
                    "ei.delete_status"   => 0,
                    "ei.expense_bill_id" => $id ];
            $data['items']     = $this->general_model->getJoinRecords($string, $from, $where, $join);
            $data['expense']   = $this->general_model->getRecords("*", "expense", [
                    "delete_status" => 0,
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
            $data['supplier']  = $this->general_model->getRecords("*", "supplier", [
                    "delete_status" => 0,
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
            $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $igst              = 0;
            $cgst              = 0;
            $sgst              = 0;
            $dpcount           = 0;
            $dtcount           = 0;
            $tdscount          = 0;
            $cess_exist = 0;
         $discountExist = 0;
            foreach ($data['items'] as $value)
            {
                $igst = bcadd($igst, $value->expense_bill_item_igst_amount, 2);
                $cgst = bcadd($cgst, $value->expense_bill_item_cgst_amount, 2);
                $sgst = bcadd($sgst, $value->expense_bill_item_sgst_amount, 2);
                if ($value->expense_bill_item_description != "" && $value->expense_bill_item_description != null)
                {
                    $dpcount++;
                } if ($value->expense_bill_item_tds_amount != "" && $value->expense_bill_item_tds_amount != null && $value->expense_bill_item_tds_amount != 0)
                {
                    $tdscount++;
                }
                if ($data['data'][0]->expense_bill_discount_amount > 0){
                    /* Discount */
                    $discountExist = 1;
                }
            } 

            if($data['data'][0]->expense_bill_tax_cess_amount > 0){
                $cess_exist = 1;
            }
            $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->expense_bill_billing_state_id);
            $data['igst_exist'] = $igst;
            $data['cgst_exist'] = $cgst;
            $data['sgst_exist'] = $sgst;
            $data['cess_exist'] = $cess_exist;
            $data['discount_exist'] = $discountExist;
            $data['is_utgst'] = $is_utgst;
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code']     = $currency[0]->currency_code;
            $data['currency_symbol']   = $currency[0]->currency_symbol;
            $data['currency_text']   = $currency[0]->currency_name;
            $data['currency_symbol_pdf']   = $currency[0]->currency_symbol_pdf;
            $data['data'][0]->unit = $currency[0]->unit;
            $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
            $data['igst_tax']               = $igst;
            $data['cgst_tax']               = $cgst;
            $data['sgst_tax']               = $sgst;
            $data['dpcount']                = $dpcount;
            $data['dtcount']                = $dtcount;
            $data['tdscount']               = $tdscount;
            $data['note1']                  = $note_data['note1'];
            $data['template1']              = $note_data['template1'];
            $data['note2']                  = $note_data['note2'];
            $data['template2']              = $note_data['template2'];

            $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                'module_id' => 2,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);

            $pdf_json            = $pdf[0]->pdf_settings;
            $rep                 = str_replace("\\", '', $pdf_json);
            $data['pdf_results'] = json_decode($rep, true);
            $html                           = $this->load->view('expense_bill/pdf1', $data, true);
            /*include(APPPATH . 'third_party/mpdf60/mpdf.php');
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $file_path                      = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $file_name                      = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->expense_bill_invoice_number);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F');

            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';*/
            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->expense_bill_invoice_number);
            $file_name = str_replace('/','_',$file_name);
            $dompdf = new Dompdf\Dompdf();
            
            $paper_size  = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';

            $expense_bill_data              = $this->common->expense_bill_list_field1($id);
            $data['data']                   = $this->general_model->getJoinRecords($expense_bill_data['string'], $expense_bill_data['table'], $expense_bill_data['where'], $expense_bill_data['join']);
            $branch_data                    = $this->common->branch_field();
            $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']            = $this->general_model->getRecords('*', 'email_setup', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'added_user_id' => $this->session->userdata('SESS_USER_ID') ));
            $data['email_template']         = $this->general_model->getRecords('*', 'email_template', array(
                    'module_id'     => $expense_bill_module_id,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status' => 0 ));
            $this->load->view('expense_bill/email', $data);
        }
        else
        {
            $this->load->view('expense_bill', $data);
        }
    }

    public function convert_currency()
    {
        $id                 = $this->input->post('convert_currency_id');
        $id                 = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');
        $data               = array(
                'currency_converted_amount' => $this->input->post('currency_converted_amount') );
        $this->general_model->updateData('expense_bill', $data, array(
                'expense_bill_id' => $id ));

        //update converted voucher amount in account expense voucher table

        $expense_voucher_data = array(
                'currency_converted_amount' => $this->input->post('currency_converted_amount') );
        $this->general_model->updateData('expense_voucher', $expense_voucher_data, array(
                'reference_id'   => $id,
                'delete_status'  => 0,
                'reference_type' => 'expense_bill' ));

        $expense_voucher = $this->general_model->getRecords('expense_voucher_id', 'expense_voucher', array(
                'reference_id'   => $id,
                'delete_status'  => 0,
                'reference_type' => 'expense_bill' ));

        $accounts_expense_voucher = $this->general_model->getRecords('*', 'accounts_expense_voucher', array(
                'expense_voucher_id' => $expense_voucher[0]->expense_voucher_id,
                'delete_status'      => 0 ));

        foreach ($accounts_expense_voucher as $key1 => $value1)
        {

            $new_converted_voucher_amount = bcmul($accounts_expense_voucher[$key1]->voucher_amount, $new_converted_rate, 2);

            $converted_voucher_amount = array(
                    'converted_voucher_amount' => $new_converted_voucher_amount );
            $where                    = array(
                    'accounts_expense_id' => $accounts_expense_voucher[$key1]->accounts_expense_id );
            $voucher_table            = "accounts_expense_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }
        redirect('expense_bill', 'refresh');
    }


    public function email_popup($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $branch_data                     = $this->common->branch_field();
        $data['branch']                  = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data                    = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']                 = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                      = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state']                   = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data                       = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']                    = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $expense_bill_module_id          = $this->config->item('expense_bill_module');
        $data['module_id']               = $expense_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        $data = array_merge($data, $section_modules);
        $data['expense_bill_module_id']    = $expense_bill_module_id;
        $data['module_id']                 = $expense_bill_module_id;
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        $data['notes_module_id']           = $this->config->item('notes_module');
        $data['product_module_id']         = $this->config->item('expense_module');
        $data['supplier_module_id']        = $this->config->item('supplier_module');
        $data['tax_module_id']             = $this->config->item('tax_module');
        $data['discount_module_id']        = $this->config->item('discount_module');
        $data['accounts_module_id']        = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');
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
        } 

                        
            ob_start();
            $html              = ob_get_clean();
            $html              = utf8_encode($html);
            $data['currency']  = $this->currency_call();
            $expense_bill_data = $this->common->expense_bill_list_field1($id);
            $data['data']      = $this->general_model->getJoinRecords($expense_bill_data['string'], $expense_bill_data['table'], $expense_bill_data['where'], $expense_bill_data['join']);
            $string            = "ei.*,e.*";
            $from              = "expense_bill_item ei";
            $join              = [
                    'expense e' => "ei.expense_type_id = e.expense_id" ];
            $where             = [
                    "ei.delete_status"   => 0,
                    "ei.expense_bill_id" => $id ];
            $data['items']     = $this->general_model->getJoinRecords($string, $from, $where, $join);
            $data['expense']   = $this->general_model->getRecords("*", "expense", [
                    "delete_status" => 0,
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
            $data['supplier']  = $this->general_model->getRecords("*", "supplier", [
                    "delete_status" => 0,
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
            $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $igst              = 0;
            $cgst              = 0;
            $sgst              = 0;
            $dpcount           = 0;
            $dtcount           = 0;
            $tdscount          = 0;
            $cess_exist = 0;
         $discountExist = 0;
            foreach ($data['items'] as $value)
            {
                $igst = bcadd($igst, $value->expense_bill_item_igst_amount, 2);
                $cgst = bcadd($cgst, $value->expense_bill_item_cgst_amount, 2);
                $sgst = bcadd($sgst, $value->expense_bill_item_sgst_amount, 2);
                if ($value->expense_bill_item_description != "" && $value->expense_bill_item_description != null)
                {
                    $dpcount++;
                } if ($value->expense_bill_item_tds_amount != "" && $value->expense_bill_item_tds_amount != null && $value->expense_bill_item_tds_amount != 0)
                {
                    $tdscount++;
                }
                if ($data['data'][0]->expense_bill_discount_amount > 0){
                    /* Discount */
                    $discountExist = 1;
                }
            } 

            if($data['data'][0]->expense_bill_tax_cess_amount > 0){
                $cess_exist = 1;
            }
            $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->expense_bill_billing_state_id);
            $data['igst_exist'] = $igst;
            $data['cgst_exist'] = $cgst;
            $data['sgst_exist'] = $sgst;
            $data['cess_exist'] = $cess_exist;
            $data['discount_exist'] = $discountExist;
            $data['is_utgst'] = $is_utgst;
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code']     = $currency[0]->currency_code;
            $data['currency_symbol']   = $currency[0]->currency_symbol;
            $data['currency_text']   = $currency[0]->currency_name;
            $data['currency_symbol_pdf']   = $currency[0]->currency_symbol_pdf;
            $data['data'][0]->unit = $currency[0]->unit;
            $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
            $data['igst_tax']               = $igst;
            $data['cgst_tax']               = $cgst;
            $data['sgst_tax']               = $sgst;
            $data['dpcount']                = $dpcount;
            $data['dtcount']                = $dtcount;
            $data['tdscount']               = $tdscount;
            $data['note1']                  = $note_data['note1'];
            $data['template1']              = $note_data['template1'];
            $data['note2']                  = $note_data['note2'];
            $data['template2']              = $note_data['template2'];

            $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                'module_id' => 2,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);

            $pdf_json            = $pdf[0]->pdf_settings;
            $rep                 = str_replace("\\", '', $pdf_json);
            $data['pdf_results'] = json_decode($rep, true);
            $html                           = $this->load->view('expense_bill/pdf1', $data, true);
            /*include(APPPATH . 'third_party/mpdf60/mpdf.php');
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $file_path                      = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $file_name                      = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->expense_bill_invoice_number);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F');

            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';*/
            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->expense_bill_invoice_number);
            $dompdf = new Dompdf\Dompdf();
            
            $paper_size  = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';

            $expense_bill_data              = $this->common->expense_bill_list_field1($id);
            $data['data']                   = $this->general_model->getJoinRecords($expense_bill_data['string'], $expense_bill_data['table'], $expense_bill_data['where'], $expense_bill_data['join']);
            $branch_data                    = $this->common->branch_field();
            $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']            = $this->general_model->getRecords('*', 'email_setup', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'added_user_id' => $this->session->userdata('SESS_USER_ID') ));
            $data['email_template']         = $this->general_model->getRecords('*', 'email_template', array(
                    'module_id'     => $expense_bill_module_id,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status' => 0 ));
            $data['data'][0]->pdf_file_path = $data['pdf_file_path'];
            $data['data'][0]->pdf_file_name = $data['pdf_file_name'];
            $data['data'][0]->email_template = $data['email_template'];
            $data['data'][0]->firm_name = $data['branch'][0]->firm_name;
            $result = json_encode($data['data']);
            echo $result;
           
    }   

    public function remove_image($id){       
            $this->db->select('expense_file,branch_id');
            $this->db->from('expense_bill');
            $this->db->where('expense_bill_id',$id);                    
            $get_purchase_qry = $this->db->get();            
            $purchase = $get_purchase_qry->result();
            $expense_file = $purchase[0]->expense_file;
            $branch_id = $purchase[0]->branch_id;
            $path = FCPATH.'assets/images/BRANCH-'.$branch_id.'/Expense/'.$expense_file;
            unlink($path);
        
        $this->general_model->updateData('expense_bill', array(
                'expense_file' => '' ), array(
                'expense_bill_id'       => $id,
                'branch_id'       => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));
       
        $product_id = $this->encryption_url->encode($id);
              
        redirect('expense_bill/edit/'.$product_id, 'refresh');
    }

}
