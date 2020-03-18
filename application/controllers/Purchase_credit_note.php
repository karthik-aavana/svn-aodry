<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_credit_note extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'general_model',
            'product_model',
            'service_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $purchase_credit_note_module_id         = $this->config->item('purchase_credit_note_module');
        $data['purchase_credit_note_module_id'] = $purchase_credit_note_module_id;
        $modules                                = $this->modules;
        $privilege                              = "view_privilege";
        $data['privilege']                      = $privilege;
        $section_modules                        = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);
        $access_common_settings                 = $section_modules['access_common_settings'];

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        $data['purchase_return_module_id'] = $this->config->item('purchase_return_module');
        $currency = $this->getBranchCurrencyCode();
        $currency_code     = $currency[0]->currency_code;
        $currency_symbol   = $currency[0]->currency_symbol;
        

        if (!empty($this->input->post()))
        {
            $columns = array(
                0 => 'date',
                1 => 'supplier',
                2 => 'grand_total',
                3 => 'purchase_supplier_credit_note_number',
                4 => 'invoice_status',
                5 => 'added_user',
                6 => 'action');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->purchase_credit_note_list_field();
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
            }

            $send_data = array();

            if (!empty($posts))
            {

                foreach ($posts as $post)
                {
                    $purchase_credit_note_id   = $this->encryption_url->encode($post->purchase_credit_note_id);
                    $purchase_id   = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date']        = date('d-m-Y',strtotime($post->purchase_credit_note_date));
                    /*$nestedData['supplier']    = $post->supplier_name . ' (<a href="' . base_url('purchase_credit_note/view/') . $purchase_credit_note_id . '">' . $post->purchase_credit_note_invoice_number . '</a>) ';*/
                    $nestedData['supplier']    = $post->supplier_name .($post->purchase_supplier_credit_note_number != '' ? ' ( '. $post->purchase_supplier_credit_note_number . ' ) ' : '');
                    $nestedData['invoice_number'] = ' <a href="' . base_url('purchase_credit_note/view/') . $purchase_credit_note_id . '">' . $post->purchase_credit_note_invoice_number . '</a>';
                    $nestedData['reference_invoice_number'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                    $nestedData['grand_total'] = $currency_symbol . ' ' . $this->precise_amount($post->purchase_credit_note_grand_total, $access_common_settings[0]->amount_precision) . ' (INV)';

                    $nestedData['credit_note_number'] = $post->purchase_supplier_credit_note_number;
                    $nestedData['converted_grand_total'] = $this->precise_amount($post->converted_grand_total, $access_common_settings[0]->amount_precision);

                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $nestedData['invoice_status'] = '<span class="label label-danger">Not Converted</span>';
                    }
                    else
                    {
                        $nestedData['invoice_status'] = '<span class="label label-success">Converted</span>';
                    }
                    $this->db->select('purchase_voucher_id');
                    $this->db->from('purchase_voucher');
                    $this->db->where('reference_id',$post->purchase_credit_note_id);
                    $this->db->where('delete_status',0);
                    $this->db->where('reference_type','purchase_credit_note');
                    $get_pv_qry = $this->db->get();
                    $ref_id = $get_pv_qry->result();
                    $purchase_cn_voucher_id = '';
                    if(!empty($ref_id)){
                        $purchase_cn_voucher_id = $ref_id[0]->purchase_voucher_id;
                    }
                   /* if($purchase_cn_voucher_id != ''){
                        $purchase_cn_voucher_id = $this->encryption_url->encode($purchase_cn_voucher_id);
                        $nestedData['purchase_cn_voucher_view'] = ' <a href="' .base_url('purchase_voucher/view_details/') . $purchase_cn_voucher_id.'" target="_blank">' . '<i class="fa fa-file" aria-hidden="true" title="Voucher View"></i>' . '</a>'. '  ' .' <form  action="' .base_url('purchase_credit_ledger').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$purchase_cn_voucher_id.'"><button type="submit">' . '<i class="fa fa-file" aria-hidden="true" title="Ledger View"></i></button></form>';
                    }*/
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    
					$cols = '<div class="box-body hide action_button"><div class="btn-group">';
					
                    $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" href="' . base_url('purchase_credit_note/view/') . $purchase_credit_note_id . '" title="View Purchase Credit Note"><i class="fa fa-eye"></i></a></span>';

					$cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" href="' . base_url('purchase_credit_note/edit/') . $purchase_credit_note_id . '" title="Edit Purchase Credit Note"><i class="fa fa-pencil"></i></a></span>';

                    $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" href="' . base_url('purchase_credit_note/pdf/') . $purchase_credit_note_id . '" title="Download PDF" target="_blank"><i class="fa fa-file-pdf-o"></i></a></span>';

                    /*if (in_array($purchase_credit_note_module_id, $data['active_view']))
                    {
					 if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
					 {
					     $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" href="' . base_url('purchase_credit_note/email/') . $purchase_credit_note_id . '" title="Email purchase credit note"><i class="fa fa-envelope-o"></i></a></span>';
					 }
                    } */
                    /*if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                     $cols .= '<span data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" ><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" data-id="' . $purchase_credit_note_id . '" data-path="purchase_credit_note/convert_currency" data-currency_code="' . $currency_code . '" data-grand_total="' . $post->purchase_credit_note_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i></a></span>';
                    }*/

                     if($purchase_cn_voucher_id != ''){
                        $purchase_cn_voucher_id = $this->encryption_url->encode($purchase_cn_voucher_id);
                        $cols .= '<span><a href="' .base_url('purchase_voucher/view_details/') . $purchase_cn_voucher_id.'" target="_blank" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="View Voucher"><i class="fa fa-eye"></i></a></span>';

                        $cols .= '<span><form  action="' .base_url('purchase_credit_ledger').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$purchase_cn_voucher_id.'"><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" class="btn btn-app" title="View Ledger"><button type="submit" class="sales_action">' . '<i class="fa fa-eye" aria-hidden="true"></i></button></a></form></span>';

                    }

                    if (in_array($purchase_credit_note_module_id, $data['active_delete']))
                    {
                    $cols .= '<span data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $purchase_credit_note_id . '" data-path="purchase_credit_note/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" href="#" title="Delete Purchase Credit Note" ><i class="fa fa-trash-o"></i></a></span>';
                    }
                    $cols .= '</div></div>';                   
 					$nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
										
                    $send_data[]          = $nestedData;
                }

            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('purchase_credit_note/list', $data);
        }

    }

    public function add()
    {
        $data                           = $this->get_default_country_state();
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $modules                        = $this->modules;
        $privilege                      = "add_privilege";
        $data['privilege']              = $privilege;
        $section_modules                = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['purchase_credit_note_module_id'] = $purchase_credit_note_module_id;
        $data['module_id']                      = $purchase_credit_note_module_id;
        $data['notes_module_id']                = $this->config->item('notes_module');
        $data['product_module_id']              = $this->config->item('product_module');
        $data['service_module_id']              = $this->config->item('service_module');
        $data['supplier_module_id']             = $this->config->item('supplier_module');
        $data['category_module_id']             = $this->config->item('category_module');
        $data['subcategory_module_id']          = $this->config->item('subcategory_module');
        $data['tax_module_id']                  = $this->config->item('tax_module');
        $data['discount_module_id']             = $this->config->item('discount_module');
        $data['accounts_module_id']             = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');

        $data['supplier'] = $this->supplier_call();
        $data['currency'] = $this->currency_call();

        if ($data['access_settings'][0]->discount_visible == "yes")
        {
            $data['discount'] = $this->discount_call();
        }

        if ($data['access_settings'][0]->tax_type == "gst" || $data['access_settings'][0]->item_access == "single_tax")
        {
            $data['tax'] = $this->tax_call();
        }

        if ($data['access_settings'][0]->item_access == "service" || $data['access_settings'][0]->item_access == "both")
        {
            $data['sac']              = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }

        if ($data['access_settings'][0]->item_access == "product" || $data['access_settings'][0]->item_access == "both")
        {
            $data['inventory_access'] = $data['access_common_settings'][0]->inventory_advanced;
            $data['product_category'] = $this->product_category_call();
            $data['uqc']              = $this->uqc_call();
            $data['chapter']          = $this->chapter_call();
            $data['hsn']              = $this->hsn_call();
            $data['uqc_service']      = $this->uqc_product_service_call('service');
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();

            if ($data['inventory_access'] == "yes")
            {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key']          = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
            }

        }

        $access_settings        = $data['access_settings'];
        $primary_id             = "purchase_credit_note_id";
        $table_name             = $this->config->item('purchase_credit_note_table');
        $date_field_name        = "purchase_credit_note_date";
        $current_date           = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('purchase_credit_note/add', $data);
    }

    public function get_purchase_invoice_number()
    {
        $supplier_id  = $this->input->post('supplier_id');
        $invoice_data = $this->common->get_supplier_invoice_number_field($supplier_id, "0");
        $data         = $this->general_model->getRecords($invoice_data['string'], $invoice_data['table'], $invoice_data['where'], $invoice_data['order']);

        echo json_encode($data);
    }

    public function get_purchase_credit_note_suggestions($term, $inventory_advanced, $item_access)
    {
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $modules                        = $this->modules;
        $privilege                      = "add_privilege";
        $data['privilege']              = $privilege;
        $section_modules                = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);

        /*if ($inventory_advanced == "yes")
        {
            $suggestions_query = $this->common->item_inventory_suggestions_field($item_access, $term);
            $data              = $this->general_model->getQueryRecords($suggestions_query);
        }
        else
        {
        }*/
        $suggestions_query = $this->common->item_suggestions_field($item_access, $term);
        $data              = $this->general_model->getQueryRecords($suggestions_query);

        // $data["product_inventoery"]=$inventory_access[0]->inventory_advanced;
        echo json_encode($data);

    }

    public function get_table_items($code)
    {
        /* 0-id, 1-type, 2-discount, 3-tax , */

        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $modules                        = $this->modules;
        $privilege                      = "add_privilege";
        $data['privilege']              = $privilege;
        $section_modules                = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);

        $item_code = explode("-", $code);

        if ($item_code[1] == "service")
        {
            $service_data = $this->common->service_field($item_code[0]);

            $data = $this->general_model->getJoinRecords($service_data['string'], $service_data['table'], $service_data['where'], $service_data['join']);
        }else{
            
            $product_data = $this->common->product_field($item_code[0]);
            $data         = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);
           
        }

        $discount_data = array();
        $tax_data      = array();
        $tds_data      = array();

        if ($item_code[2] == 'yes')
        {
            $discount_data = $this->discount_call();
        }

        if ($item_code[3] == 'gst' || $item_code[3] == 'single_tax')
        {
            $tax_data = $this->tax_call();
        }

        $data['discount']          = $discount_data;
        $data['tax']               = $tax_data;
        $branch_details            = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id']   = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id']         = $branch_details['branch'][0]->branch_id;
        $data['item_id']           = $item_code[0];
        $data['item_type']         = $item_code[1];
        echo json_encode($data);

    }

    public function get_purchase_item()
    {
        $purchase_id = $this->input->post('purchase_id');

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));

        $product_items                = $this->common->purchase_items_product_list_field($purchase_id, 0);
        $purchase_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        /*if ($inventory_access[0]->inventory_advanced == "yes")
        {
            $product_items                = $this->common->purchase_items_product_inventory_list_field($purchase_id, 0);
            $purchase_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }
        else
        {
        }*/

        $service_items                = $this->common->purchase_items_service_list_field($purchase_id);
        $purchase_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items']                = array_merge($purchase_items_product_items, $purchase_items_service_items);
        $branch_details               = $this->get_default_country_state();
        $data['branch_country_id']    = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id']      = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id']            = $branch_details['branch'][0]->branch_id;
        $discount_data                = array();
        $tax_data                     = array();
        $tds_data                     = array();

        $discount_data = $this->discount_call();
        $tax_data      = $this->tax_call();

        $data['discount'] = $discount_data;
        $data['tax']      = $tax_data;

        $purchase_data = $this->general_model->getRecords('currency_id', 'purchase', array(
            'purchase_id'   => $purchase_id,
            'delete_status' => 0));
        $data['currency'] = $this->general_model->getRecords('currency_id,currency_name', 'currency', array(
            'currency_id' => $purchase_data[0]->currency_id));
        echo json_encode($data);
    }

    public function add_purchase_credit_note(){
       /* echo "<per>";
        print_r($this->input->post());
        exit();*/
        $data                        = $this->get_default_country_state();
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $module_id                   = $purchase_credit_note_module_id;
        $modules                     = $this->modules;
        $privilege                   = "add_privilege";
        $section_modules             = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['purchase_credit_note_module_id'] = $purchase_credit_note_module_id;
        $data['module_id']                   = $purchase_credit_note_module_id;
        $data['notes_module_id']             = $this->config->item('notes_module');
        $data['product_module_id']           = $this->config->item('product_module');
        $data['service_module_id']           = $this->config->item('service_module');
        $data['supplier_module_id']          = $this->config->item('supplier_module');
        $data['category_module_id']          = $this->config->item('category_module');
        $data['subcategory_module_id']       = $this->config->item('subcategory_module');
        $data['tax_module_id']               = $this->config->item('tax_module');
        $data['discount_module_id']          = $this->config->item('discount_module');
        $data['accounts_module_id']          = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id']    = $this->config->item('accounts_sub_module');

        $access_settings = $section_modules['access_settings'];

        $currency = $this->input->post('currency_id');
        
        if ($access_settings[0]->invoice_creation == "automatic"){
            $primary_id      = "purchase_credit_note_id";
            $table_name      = $this->config->item('purchase_credit_note_table');
            $date_field_name = "purchase_credit_note_date";
            $current_date    = date('Y-m-d',strtotime($this->input->post('invoice_date')));

            $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        }else{
            $invoice_number = $this->input->post('invoice_number');
        }
        

        $supplier               = explode("-", $this->input->post('supplier'));
        $total_cess_amnt= $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0 ;
        $purchase_credit_note_data = array(
            "purchase_credit_note_date"                    => date('Y-m-d',strtotime($this->input->post('invoice_date'))),
            "purchase_id"                                  => $this->input->post('purchase_invoice_number'),
            "purchase_credit_note_invoice_number"          => $invoice_number,
            "purchase_credit_note_sub_total"               => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "purchase_supplier_credit_note_date"         => ($this->input->post('purchase_supplier_credit_note_date') != '' ? date('Y-m-d' , strtotime($this->input->post('purchase_supplier_credit_note_date'))) : ''),
            "purchase_supplier_credit_note_number"         => $this->input->post('supplier_credit_note_no'),
            "purchase_credit_note_grand_total"             => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "purchase_credit_note_discount_amount"         => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0,
            "purchase_credit_note_tax_amount"              => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "purchase_credit_note_taxable_value"           => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0,
            "purchase_credit_note_gst_payable"              => $this->input->post('gst_payable') ,
            "purchase_credit_note_tds_amount"              => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0,
            "purchase_credit_note_tcs_amount"              => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0,
            "purchase_credit_note_igst_amount"             => 0,
            "purchase_credit_note_cgst_amount"             => 0,
            "purchase_credit_note_sgst_amount"             => 0,
            "from_account"                              => 'purchase',
            "to_account"                                => 'supplier',
            "financial_year_id"                         => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "purchase_credit_note_party_id"                => $this->input->post('supplier'),
            "purchase_credit_note_party_type"              => "supplier",
            "purchase_credit_note_nature_of_supply"        => $this->input->post('nature_of_supply'),
            "purchase_credit_note_supplier_invoice_number" => $this->input->post('supplier_ref'),
            "purchase_credit_note_supplier_date"           => ($this->input->post('supplier_date') != '' ? date('Y-m-d' , strtotime($this->input->post('supplier_date'))) : ''),
            "purchase_credit_note_delivery_challan_number" => $this->input->post('delivery_challan_number'),
            "purchase_credit_note_delivery_date"           => ($this->input->post('delivery_date') != '' ? date('Y-m-d', strtotime($this->input->post('delivery_date'))) : ''),
            "purchase_credit_note_e_way_bill_number"       => $this->input->post('e_way_bill'),
            "purchase_credit_note_type_of_supply"          => $this->input->post('type_of_supply'),
            "purchase_credit_note_billing_country_id"      => $this->input->post('billing_country'),
            "purchase_credit_note_billing_state_id"        => $this->input->post('billing_state'),
            "added_date"                                => date('Y-m-d'),
            "added_user_id"                             => $this->session->userdata('SESS_USER_ID'),
            "branch_id"                                 => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id"                               => $this->input->post('currency_id'),
            "updated_date"                              => "",
            "updated_user_id"                           => "",
            "warehouse_id"                              => "",
            "transporter_name"                          => $this->input->post('transporter_name'),
            "transporter_gst_number"                    => $this->input->post('transporter_gst_number'),
            "lr_no"                                     => $this->input->post('lr_no'),
            "vehicle_no"                                => $this->input->post('vehicle_no'),
            "mode_of_shipment"                          => $this->input->post('mode_of_shipment'),
            "ship_by"                                   => $this->input->post('ship_by'),
            "net_weight"                                => $this->input->post('net_weight'),
            "gross_weight"                              => $this->input->post('gross_weight'),
            "origin"                                    => $this->input->post('origin'),
            "destination"                               => $this->input->post('destination'),
            "shipping_type"                             => $this->input->post('shipping_type'),
            "shipping_type_place"                       => $this->input->post('shipping_type_place'),
            "lead_time"                                 => $this->input->post('lead_time'),
            "shipping_address_id"                       => $this->input->post('shipping_address_id'),
            /*"billing_address_id"                        => $this->input->post('billing_address_id'),*/
            "warranty"                                  => $this->input->post('warranty'),
            "payment_mode"                              => $this->input->post('payment_mode'),
            "freight_charge_amount"                     => $this->input->post('freight_charge_amount') ? (float) $this->input->post('freight_charge_amount') : 0,
            "freight_charge_tax_percentage"             => $this->input->post('freight_charge_tax_percentage') ? (float) $this->input->post('freight_charge_tax_percentage') : 0,
            "freight_charge_tax_amount"                 => $this->input->post('freight_charge_tax_amount') ? (float) $this->input->post('freight_charge_tax_amount') : 0,
            "total_freight_charge"                      => $this->input->post('total_freight_charge') ? (float) $this->input->post('total_freight_charge') : 0,
            "insurance_charge_amount"                   => $this->input->post('insurance_charge_amount') ? (float) $this->input->post('insurance_charge_amount') : 0,
            "insurance_charge_tax_percentage"           => $this->input->post('insurance_charge_tax_percentage') ? (float) $this->input->post('insurance_charge_tax_percentage') : 0,
            "insurance_charge_tax_amount"               => $this->input->post('insurance_charge_tax_amount') ? (float) $this->input->post('insurance_charge_tax_amount') : 0,
            "total_insurance_charge"                    => $this->input->post('total_insurance_charge') ? (float) $this->input->post('total_insurance_charge') : 0,
            "packing_charge_amount"                     => $this->input->post('packing_charge_amount') ? (float) $this->input->post('packing_charge_amount') : 0,
            "packing_charge_tax_percentage"             => $this->input->post('packing_charge_tax_percentage') ? (float) $this->input->post('packing_charge_tax_percentage') : 0,
            "packing_charge_tax_amount"                 => $this->input->post('packing_charge_tax_amount') ? (float) $this->input->post('packing_charge_tax_amount') : 0,
            "total_packing_charge"                      => $this->input->post('total_packing_charge') ? (float) $this->input->post('total_packing_charge') : 0,
            "incidental_charge_amount"                  => $this->input->post('incidental_charge_amount') ? (float) $this->input->post('incidental_charge_amount') : 0,
            "incidental_charge_tax_percentage"          => $this->input->post('incidental_charge_tax_percentage') ? (float) $this->input->post('incidental_charge_tax_percentage') : 0,
            "incidental_charge_tax_amount"              => $this->input->post('incidental_charge_tax_amount') ? (float) $this->input->post('incidental_charge_tax_amount') : 0,
            "total_incidental_charge"                   => $this->input->post('total_incidental_charge') ? (float) $this->input->post('total_incidental_charge') : 0,
            "inclusion_other_charge_amount"             => $this->input->post('inclusion_other_charge_amount') ? (float) $this->input->post('inclusion_other_charge_amount') : 0,
            "inclusion_other_charge_tax_percentage"     => $this->input->post('inclusion_other_charge_tax_percentage') ? (float) $this->input->post('inclusion_other_charge_tax_percentage') : 0,
            "inclusion_other_charge_tax_amount"         => $this->input->post('inclusion_other_charge_tax_amount') ? (float) $this->input->post('inclusion_other_charge_tax_amount') : 0,
            "total_inclusion_other_charge"              => $this->input->post('total_other_inclusive_charge') ? (float) $this->input->post('total_other_inclusive_charge') : 0,
            "exclusion_other_charge_amount"             => $this->input->post('exclusion_other_charge_amount') ? (float) $this->input->post('exclusion_other_charge_amount') : 0,
            "exclusion_other_charge_tax_percentage"     => $this->input->post('exclusion_other_charge_tax_percentage') ? (float) $this->input->post('exclusion_other_charge_tax_percentage') : 0,
            "exclusion_other_charge_tax_amount"         => $this->input->post('exclusion_other_charge_tax_amount') ? (float) $this->input->post('exclusion_other_charge_tax_amount') : 0,
            "total_exclusion_other_charge"              => $this->input->post('total_other_exclusive_charge') ? (float) $this->input->post('total_other_exclusive_charge') : 0,
            "total_other_amount"                        => $this->input->post('total_other_amount') ? (float) $this->input->post('total_other_amount') : 0,
            "total_other_taxable_amount"            =>$this->input->post('total_other_taxable_amount') ? (float) $this->input->post('total_other_taxable_amount') : 0 ,
            "note1"                                     => $this->input->post('note1'),
            "note2"                                     => $this->input->post('note2')
        );
    
        $purchase_credit_note_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $purchase_credit_note_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $purchase_credit_note_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $purchase_credit_note_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $purchase_credit_note_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $purchase_credit_note_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;

        $round_off_value = $purchase_credit_note_data['purchase_credit_note_grand_total'];

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" && $this->input->post('round_off_key') == "yes"){
            if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 ){
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $purchase_credit_note_data['round_off_amount'] = bcsub($purchase_credit_note_data['purchase_credit_note_grand_total'], $round_off_value, $section_modules['access_common_settings'][0]->amount_precision);

        $purchase_credit_note_data['purchase_credit_note_grand_total'] = $round_off_value;

        $purchase_credit_note_tax_amount = $purchase_credit_note_data['purchase_credit_note_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));

        if ($section_modules['access_settings'][0]->tax_type == "gst"){
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

            if ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'intra_state'){
                $purchase_credit_note_data['purchase_credit_note_igst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_cgst_amount'] = ($purchase_credit_note_tax_amount * $cgst_amount_percentage) / 100;
                $purchase_credit_note_data['purchase_credit_note_sgst_amount'] = ($purchase_credit_note_tax_amount * $sgst_amount_percentage) / 100;
                $purchase_credit_note_data['purchase_credit_note_tax_cess_amount'] = $total_cess_amnt;

            } elseif($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'inter_state'){
                $purchase_credit_note_data['purchase_credit_note_igst_amount'] = $purchase_credit_note_tax_amount;
                $purchase_credit_note_data['purchase_credit_note_cgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_sgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_tax_cess_amount'] = $total_cess_amnt;

            }elseif ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'import'){
                $purchase_credit_note_data['purchase_credit_note_igst_amount'] = $purchase_credit_note_tax_amount;
                $purchase_credit_note_data['purchase_credit_note_cgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_sgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_tax_cess_amount'] = $total_cess_amnt;
            }
            
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id'))
        {
            $purchase_credit_note_data['converted_grand_total'] = $purchase_credit_note_data['purchase_credit_note_grand_total'];
        }
        else
        {
            $purchase_credit_note_data['converted_grand_total'] = 0;
        }

        $data_main               = array_map('trim', $purchase_credit_note_data);
        $purchase_credit_note_table = $this->config->item('purchase_credit_note_table');
        /*echo "<pre>";
        print_r($purchase_credit_note_data);exit();
        */
        if ($purchase_credit_note_id = $this->general_model->insertData($purchase_credit_note_table, $data_main)){

            /*if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {*/

                $purchase_id = $data_main['purchase_id'];
                $old_amount = $this->general_model->getRecords("credit_note_amount,converted_credit_note_amount,supplier_payable_amount", "purchase", array('purchase_id' => $purchase_id));

                $new_amount           = bcadd($old_amount[0]->credit_note_amount, $data_main["purchase_credit_note_grand_total"], $section_modules['access_common_settings'][0]->amount_precision);
                $cust_new_amount      = bcadd($old_amount[0]->supplier_payable_amount, ($data_main["purchase_credit_note_grand_total"] - $data_main["purchase_credit_note_tds_amount"]), $section_modules['access_common_settings'][0]->amount_precision);
                $converted_new_amount = bcadd($old_amount[0]->converted_credit_note_amount, $data_main["converted_grand_total"], $section_modules['access_common_settings'][0]->amount_precision);
                
                $this->general_model->updateData("purchase", array(
                    'credit_note_amount' => $new_amount, 
                    'converted_credit_note_amount' => $converted_new_amount,
                    'supplier_payable_amount' => $cust_new_amount), array(
                    'purchase_id' => $purchase_id));
            /*}*/
            $successMsg = 'Purchase Credit Note Added Successfully';
            $this->session->set_flashdata('purchase_cn_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $purchase_credit_note_id,
                'table_name'        => $purchase_credit_note_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'purchase_credit_note Inserted');
            $data_main['purchase_credit_note_id'] = $purchase_credit_note_id;
            $log_table                         = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $purchase_credit_note_item_data = $this->input->post('table_data');
            $js_data                     = json_decode($purchase_credit_note_item_data);
            $js_data               = array_reverse($js_data);
            $item_table                  = $this->config->item('purchase_credit_note_item_table');
           
            if (!empty($js_data)){
                $js_data1 = array();

                foreach ($js_data as $key => $value){

                    if ($value == null || ($value->item_grand_total <= 0 && $value->item_quantity <= 0)){

                    }else{
                        $item_id   = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "item_id"                                => $value->item_id,
                            "item_type"                              => $value->item_type,
                            "purchase_credit_note_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "purchase_credit_note_item_unit_price"      => (@$value->item_price ? (float) $value->item_price : 0),
                            "purchase_credit_note_item_sub_total"       => (@$value->item_sub_total ? (float) $value->item_sub_total : 0),
                            "purchase_credit_note_item_taxable_value"   => (@$value->item_taxable_value ? (float) $value->item_taxable_value : 0),
                            "purchase_credit_note_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0),
                            "purchase_credit_note_item_discount_id"     => (@$value->item_discount_id ? (float) $value->item_discount_id : 0),
                            "purchase_credit_note_item_tds_id"          => (@$value->item_tds_id ? (float) $value->item_tds_id : 0),
                            "purchase_credit_note_item_tds_percentage"  => (@$value->item_tds_percentage ? (float) $value->item_tds_percentage : 0),
                            "purchase_credit_note_item_tds_amount"      => (@$value->item_tds_amount ? (float) $value->item_tds_amount : 0),
                            "purchase_credit_note_item_grand_total"     => (@$value->item_grand_total ? (float) $value->item_grand_total : 0),
                            "purchase_credit_note_item_tax_id"          => (@$value->item_tax_id ? (float) $value->item_tax_id : 0),
                            "purchase_credit_note_item_tax_cess_id"     => (@$value->item_tax_cess_id ? (float)$value->item_tax_cess_id : 0) ,
                            "purchase_credit_note_item_igst_percentage" => 0,
                            "purchase_credit_note_item_igst_amount"     => 0,
                            "purchase_credit_note_item_cgst_percentage" => 0,
                            "purchase_credit_note_item_cgst_amount"     => 0,
                            "purchase_credit_note_item_sgst_percentage" => 0,
                            "purchase_credit_note_item_sgst_amount"     => 0,
                            "purchase_credit_note_item_tax_percentage"  => (@$value->item_tax_percentage ? (float) $value->item_tax_percentage : 0),
                            "purchase_credit_note_item_tax_amount"      => (@$value->item_tax_amount ? (float) $value->item_tax_amount : 0),
                            "purchase_credit_note_item_tax_cess_percentage"  =>  0 ,
                            "purchase_credit_note_item_tax_cess_amount"      =>  0 ,
                            "purchase_credit_note_item_description"     => (@$value->item_description ? $value->item_description : ""),
                            "debit_note_quantity"                    => 0,
                            "purchase_credit_note_id"                   => $purchase_credit_note_id);

                        $purchase_credit_note_item_tax_amount     = $item_data['purchase_credit_note_item_tax_amount'];
                        $purchase_credit_note_item_tax_percentage = $item_data['purchase_credit_note_item_tax_percentage'];

                        if ($section_modules['access_settings'][0]->tax_type == "gst")
                        {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0 ;
                            if ($data['branch'][0]->branch_country_id == $purchase_credit_note_data['purchase_credit_note_billing_country_id'])
                            {

                                if ($data['branch'][0]->branch_state_id == $purchase_credit_note_data['purchase_credit_note_billing_state_id'])
                                {
                                    $item_data['purchase_credit_note_item_igst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_cgst_amount'] = ($purchase_credit_note_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_credit_note_item_sgst_amount'] = ($purchase_credit_note_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_credit_note_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_credit_note_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                    $item_data['purchase_credit_note_item_igst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_cgst_percentage'] = ($purchase_credit_note_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_credit_note_item_sgst_percentage'] = ($purchase_credit_note_item_tax_percentage * $sgst_amount_percentage) / 100;
                                }else{
                                    $item_data['purchase_credit_note_item_igst_amount'] = $purchase_credit_note_item_tax_amount;
                                    $item_data['purchase_credit_note_item_cgst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_credit_note_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                    $item_data['purchase_credit_note_item_igst_percentage'] = $purchase_credit_note_item_tax_percentage;
                                    $item_data['purchase_credit_note_item_cgst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_percentage'] = 0;
                                }

                            }else{

                                if ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == "export_with_payment"){
                                    $item_data['purchase_credit_note_item_igst_amount'] = $purchase_credit_note_item_tax_amount;
                                    $item_data['purchase_credit_note_item_cgst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_amount'] = 0;

                                    $item_data['purchase_credit_note_item_igst_percentage'] = $purchase_credit_note_item_tax_percentage;
                                    $item_data['purchase_credit_note_item_cgst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_credit_note_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }

                            }

                        }

                        $data_item  = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                        $this->db->insert($item_table, $data_item);
                        if ($data_item['item_type'] == "product" || $data_item['item_type'] == "product_inventory" && $value->item_quantity > 0){
                            
                            $product_data     = $this->common->product_field($data_item['item_id']);
                            $product_result   = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'],$product_data['join']);
                            
                            $product_quantity = ($product_result[0]->product_quantity + $value->item_quantity);
                            /*$stockData        = array('product_quantity' => $product_quantity);*/
                            /* update Product Price */
                            $pro_price = $this->getAVGItemPrice($data_item['item_id']);
                            /* END */
                            $stockData        = array(
                                'product_price' => $pro_price,
                                'product_quantity' => $product_quantity);

                            $where = array('product_id' => $value->item_id);
                            $product_table = $this->config->item('product_table');
                            $this->general_model->updateData($product_table, $stockData, $where);

                            // quantity history
                            $history = array(
                                "item_id"          => $value->item_id,
                                "item_type"        => 'product',
                                "reference_id"     => $purchase_credit_note_id,
                                "reference_number" => $invoice_number,
                                "reference_type"   => 'purchase_credit_note',
                                "quantity"         => $value->item_quantity,
                                "stock_type"       => 'indirect',
                                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                                "added_date"       => date('Y-m-d'),
                                "entry_date"       => date('Y-m-d'),
                                "added_user_id"    => $this->session->userdata('SESS_USER_ID'));
                            $this->general_model->insertData("quantity_history", $history);
                        }
                    }
                }
                /*$this->general_model->insertData($item_table, $js_data1);*/
                /*$this->db->insert_batch($item_table, $js_data1);*/

                if (in_array($data['accounts_module_id'], $section_modules['active_add'])){

                    if (in_array($data['accounts_sub_module_id'], $section_modules['access_sub_modules']))
                    {
                        $action = "add";
                        $this->purchase_credit_note_voucher_entry($data_main, $js_data1, $action, $data['branch']);
                    }

                }

            }

        }
        else
        {
            $errorMsg = 'Purchase Credit Note Add Unsuccessful';
            $this->session->set_flashdata('purchase_cn_error',$errorMsg);
            redirect('purchase_credit_note', 'refresh');
        }

        $action = $this->input->post('submit');

        redirect('purchase_credit_note', 'refresh');

    }

    public function purchase_credit_note_vouchers($section_modules , $data_main , $js_data , $branch){
        $invoice_from = $data_main['from_account'];
        $invoice_to   = $data_main['to_account'];
        $ledgers      = array();

        $access_sub_modules    = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings       = $section_modules['access_settings'];
        $purchase_ledger = $this->config->item('purchase_ledger');

        $default_cgst_id = $purchase_ledger['CGST@X'];
        $cgst_x = $this->ledger_model->getDefaultLedgerId($default_cgst_id);

        $default_sgst_id = $purchase_ledger['SGST@X'];
        $sgst_x = $this->ledger_model->getDefaultLedgerId($default_sgst_id);

        $default_utgst_id = $purchase_ledger['UTGST@X'];
        $utgst_x = $this->ledger_model->getDefaultLedgerId($default_utgst_id);

        $default_igst_id = $purchase_ledger['IGST@X'];
        $igst_x = $this->ledger_model->getDefaultLedgerId($default_igst_id);
        /* Tax rate slab */
        $present = "";
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

        if ((($data_main['purchase_credit_note_tax_amount'] > 0 && ($data_main['purchase_credit_note_igst_amount'] > 0 || $data_main['purchase_credit_note_cgst_amount'] > 0 || $data_main['purchase_credit_note_sgst_amount'] > 0 || $data_main['purchase_credit_note_tax_cess_amount'] > 0)) || $data_main['purchase_credit_note_tax_cess_amount'] > 0 )){
            $present = "gst";

            if ($branch[0]->branch_country_id == $data_main['purchase_credit_note_billing_country_id']){
                if ($branch[0]->branch_state_id == $data_main['purchase_credit_note_billing_state_id']){
                    $present = "no_igst";
                } else {
                    $present = "igst";
                }
            }
            

            if($data_main['purchase_credit_note_gst_payable'] != "yes" && $present != "gst"){
               
                foreach ($js_data as $key => $value){
                    if ($present != "igst"){
                        if ($value['purchase_credit_note_item_cgst_percentage'] > 0 || $value['purchase_credit_note_item_sgst_percentage'] > 0){
                            $cgst_ary = array(
                                            'ledger_name' => 'Input CGST@'.$value['purchase_credit_note_item_cgst_percentage'].'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_x)){
                                $cgst_ledger = $cgst_x->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_cgst_percentage'] , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_x->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                            }
                            /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);*/
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                            /*
                            $cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'CGST@'.$value['purchase_credit_note_item_cgst_percentage'].'%',
                                                                        'subgrp_1' => 'CGST',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            
                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_credit_note_billing_state_id']);
                            if($is_utgst == '1') $gst_lbl = 'UTGST';
                            $sgst_ary = array(
                                            'ledger_name' => 'Input '.$gst_lbl.'@'.$value['purchase_credit_note_item_sgst_percentage'].'%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($sgst_x)){
                                if($is_utgst == '1') {
                                    $sgst_ledger = $utgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $utgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                }else{
                                    $sgst_ledger = $sgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                }
                                
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => $gst_lbl.'@'.$value['purchase_credit_note_item_sgst_percentage'].'%',
                                                                        'subgrp_1' => $gst_lbl,
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/

                            if (in_array($cgst_tax_ledger , $cgst_slab)) {
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $value['purchase_credit_note_item_cgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);   
                            }else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['purchase_credit_note_item_cgst_amount'];
                            }

                            if (in_array($sgst_tax_ledger , $sgst_slab)){
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $value['purchase_credit_note_item_sgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $sgst_slab[]                       = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['purchase_credit_note_item_sgst_amount'];
                            }
                        }
                    }else{
                        if ($value['purchase_credit_note_item_igst_percentage'] > 0) {
                            $igst_ary = array(
                                            'ledger_name' => 'Input IGST@'.$value['purchase_credit_note_item_igst_percentage'].'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_x)){
                                $igst_ledger = $igst_x->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_igst_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                $igst_ary['main_grp'] = $igst_x->main_group;
                                $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'IGST@'.$value['purchase_credit_note_item_igst_percentage'].'%',
                                                                        'subgrp_1' => 'IGST',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                            if (in_array($igst_tax_ledger , $igst_slab))
                            {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['purchase_credit_note_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }
                            else
                            {
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['purchase_credit_note_item_igst_amount'];
                            }
                        }
                    }
                    if ($value['purchase_credit_note_item_tax_cess_percentage'] > 0){
                        $default_cess_id = $purchase_ledger['CESS@X'];
                        $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                       
                        $cess_ary = array(
                                        'ledger_name' => 'Input Compensation Cess @'.$value['purchase_credit_note_item_tax_cess_percentage'].'%',
                                        'second_grp' => 'Cess',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['purchase_credit_note_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_tax_cess_percentage'] , $cess_ledger);
                            $cess_ary['ledger_name'] = $cess_ledger;
                            $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                            $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                            $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                            $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                        }
                        $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);
                        /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => 'Compensation Cess @'.$value['purchase_credit_note_item_tax_cess_percentage'].'%',
                                                                        'subgrp_1' => 'Cess',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' => 0
                                                                    ));*/
                        if (in_array($cess_tax_ledger , $cess_slab)){
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger] , $value['purchase_credit_note_item_tax_cess_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[]                       = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['purchase_credit_note_item_tax_cess_amount'];
                        }
                    }
                }
            }elseif($data_main['purchase_credit_note_gst_payable'] == "yes" && $present != "gst"){

                foreach ($js_data as $key => $value){
                    if ($present != "igst"){
                        if ($value['purchase_credit_note_item_cgst_percentage'] > 0 || $value['purchase_credit_note_item_sgst_percentage'] > 0){
                            $default_cgst_id = $purchase_ledger['CGST_REV'];
                            $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                           
                            $cgst_ary = array(
                                            'ledger_name' => 'CGST - RCM ITC availed',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_ledger_name)){
                                $cgst_ledger = $cgst_ledger_name->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_cgst_percentage'] , $cgst_ledger);
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
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_credit_note_billing_state_id']);
                            if($is_utgst == '1') $gst_lbl = 'UTGST';
                            $default_sgst_id = $purchase_ledger[$gst_lbl.'_REV'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                           
                            $sgst_ary = array(
                                            'ledger_name' => $gst_lbl . ' - RCM ITC availed',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($sgst_ledger_name)){
                                $sgst_ledger = $sgst_ledger_name->ledger_name;
                                $sgst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_sgst_percentage'] , $sgst_ledger);
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
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $value['purchase_credit_note_item_cgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);   
                            }else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['purchase_credit_note_item_cgst_amount'];
                            }

                            if (in_array($sgst_tax_ledger , $sgst_slab)){
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $value['purchase_credit_note_item_sgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $sgst_slab[]                       = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['purchase_credit_note_item_sgst_amount'];
                            }

                            /* reverse process */
                            $default_cgst_id = $purchase_ledger['CGST_PAY'];
                            $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                           
                            $cgst_ary = array(
                                            'ledger_name' => 'CGST - RCM payable',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_ledger_name)){
                                $cgst_ledger = $cgst_ledger_name->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_cgst_percentage'] , $cgst_ledger);
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
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_credit_note_billing_state_id']);
                            if($is_utgst == '1') $gst_lbl = 'UTGST';
                            $default_sgst_id = $purchase_ledger[$gst_lbl.'_PAY'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                           
                            $sgst_ary = array(
                                            'ledger_name' => $gst_lbl . ' - RCM payable',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($sgst_ledger_name)){
                                $sgst_ledger = $sgst_ledger_name->ledger_name;
                                $sgst_ledger = str_ireplace('{{X}}',$value['purchase_item_sgst_percentage'] , $sgst_ledger);
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
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger] , $value['purchase_credit_note_item_cgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);   
                            }else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['purchase_credit_note_item_cgst_amount'];
                            }

                            if(!in_array($sgst_tax_ledger, $sgst_slab_minus)) array_push($sgst_slab_minus, $sgst_tax_ledger);
                            if (in_array($sgst_tax_ledger , $sgst_slab)){
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger] , $value['purchase_credit_note_item_sgst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else{
                                $sgst_slab[]                       = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['purchase_credit_note_item_sgst_amount'];
                            }
                        }
                    }else{
                        if ($value['purchase_credit_note_item_igst_percentage'] > 0) {
                            $default_igst_id = $purchase_ledger['IGST_REV'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'IGST - RCM ITC availed',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_igst_percentage'] , $igst_ledger);
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
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['purchase_credit_note_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }
                            else
                            {
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['purchase_credit_note_item_igst_amount'];
                            }
                            /* reverse process */
                            $default_igst_id = $purchase_ledger['IGST_PAY'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'IGST - RCM payable',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_credit_note_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_igst_percentage'] , $igst_ledger);
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
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger] , $value['purchase_credit_note_item_igst_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                            }else {
                                $igst_slab[]                       = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['purchase_credit_note_item_igst_amount'];
                            }
                        }
                    }
                    if ($value['purchase_credit_note_item_tax_cess_percentage'] > 0){
                        $default_cess_id = $purchase_ledger['CESS_REV'];
                        $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                       
                        $cess_ary = array(
                                        'ledger_name' => 'Compensation Cess - RCM ITC availed',
                                        'second_grp' => 'Cess',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['purchase_credit_note_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_tax_cess_percentage'] , $cess_ledger);
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
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger] , $value['purchase_credit_note_item_tax_cess_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[]                       = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['purchase_credit_note_item_tax_cess_amount'];
                        }

                        /* reverese process */
                        $default_cess_id = $purchase_ledger['CESS_PAY'];
                        $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                       
                        $cess_ary = array(
                                        'ledger_name' => 'Compensation Cess - RCM payable',
                                        'second_grp' => 'Cess',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['purchase_credit_note_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_tax_cess_percentage'] , $cess_ledger);
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
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger] , $value['purchase_credit_note_item_tax_cess_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[]                       = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['purchase_credit_note_item_tax_cess_amount'];
                        }
                    }
                }
            }
        }

        if($data_main['purchase_credit_note_type_of_supply'] != 'import'){
            if (in_array($charges_sub_module_id , $section_modules['access_sub_modules'])){
                $present = "gst";

                if ($branch[0]->branch_country_id == $data_main['purchase_credit_note_billing_country_id']){
                    if ($branch[0]->branch_state_id == $data_main['purchase_credit_note_billing_state_id']){
                        $present = "no_igst";
                    } else {
                        $present = "igst";
                    }
                }
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
                    if ($data_main['purchase_credit_note_type_of_supply'] == 'intra_state'){
                        if ($cgst_charges_array[$key]['tax_percentage'] > 0 || $sgst_charges_array[$key]['tax_percentage'] > 0){
                            if($data_main['purchase_credit_note_gst_payable'] != "yes"){ 
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
                                $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);
                                /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'CGST@'.$cgst_charges_array[$key]['tax_percentage'].'%',
                                                                    'subgrp_1' => 'CGST',
                                                                    'subgrp_2' => 'Duties and taxes',
                                                                    'main_grp' => 'Current Liabilities',
                                                                    'amount' => 0
                                                                ));*/
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_credit_note_billing_state_id']);
                                if($is_utgst == '1') $gst_lbl = 'UTGST';
                                $default_sgst_id = $purchase_ledger[$gst_lbl.'@X'];
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
                                $default_cgst_id = $purchase_ledger['CGST_REV'];
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
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_credit_note_billing_state_id']);
                                if($is_utgst == '1') $gst_lbl = 'UTGST';
                                $default_sgst_id = $purchase_ledger[$gst_lbl.'_REV'];
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

                                $default_cgst_id = $purchase_ledger['CGST_PAY'];
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

                                $default_sgst_id = $purchase_ledger[$gst_lbl.'_PAY'];
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
                            if($data_main['purchase_credit_note_gst_payable'] != "yes"){
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
                                $default_igst_id = $purchase_ledger['IGST_REV'];
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

                                $default_igst_id = $purchase_ledger['IGST_PAY'];
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
        if ($data_main['purchase_credit_note_tds_amount'] > 0 || $data_main['purchase_credit_note_tcs_amount'] > 0)
        {
            $tds_slab                      = array();
            $tds_slab_minus                = array();
            $tds_slab_items                = array();
            $data_main['total_tds_amount'] = 0;
            $data_main['total_tcs_amount'] = 0;
            foreach ($js_data as $key => $value){

                if ($value['purchase_credit_note_item_tds_percentage'] > 0){
                    
                    $string       = 'tds.section_name,td.tax_name';
                    $table        = 'tax td';
                    $where        = array('td.delete_status' => 0 ,'td.tax_id' => $value['purchase_credit_note_item_tds_id'] );
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
                        $tds_subgroup = "TCS Receivable u/s ";
                        $default_tds_id = $purchase_ledger['TCS_REV'];
                        $tds_ledger_name = $this->ledger_model->getDefaultLedgerId($default_tds_id);
                            
                        $tds_ary = array(
                                        'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['purchase_credit_note_item_tds_percentage'].'%',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['purchase_credit_note_item_tds_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($tds_ledger_name)){
                            $tds_ledger = $tds_ledger_name->ledger_name;
                            $tds_ledger = str_ireplace('{{SECTION}}',$section_name, $tds_ledger);
                            $tds_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_tds_percentage'] , $tds_ledger);
                            $tds_ary['ledger_name'] = $tds_ledger;
                            $tds_ary['primary_grp'] = $tds_ledger_name->sub_group_1;
                            $tds_ary['second_grp'] = $tds_ledger_name->sub_group_2;
                            $tds_ary['main_grp'] = $tds_ledger_name->main_group;
                            $tds_ary['default_ledger_id'] = $tds_ledger_name->ledger_id;
                        }
                        $tds_ledger = $this->ledger_model->getGroupLedgerId($tds_ary);
                        /*$tds_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => $tds_subgroup.$section_name.'@'.$value['purchase_credit_note_item_tds_percentage'].'%',
                                                                        'subgrp_2' => '',
                                                                        'subgrp_1' => '',
                                                                        'main_grp' => 'Current Assets',
                                                                        'amount' =>  0
                                                                    ));*/
                    } else {
                        $payment_type = "Receivable";
                        $tds_subgroup = "TDS payable u/s ";
                        $default_tds_id = $purchase_ledger['TDS_PAY'];
                        $tds_ledger_name = $this->ledger_model->getDefaultLedgerId($default_tds_id);
                            
                        $tds_ary = array(
                                        'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['purchase_credit_note_item_tds_percentage'].'%',
                                        'second_grp' => '',
                                        'primary_grp' => '',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['purchase_credit_note_item_tds_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($tds_ledger_name)){
                            $tds_ledger = $tds_ledger_name->ledger_name;
                            $tds_ledger = str_ireplace('{{SECTION}}',$section_name, $tds_ledger);
                            $tds_ledger = str_ireplace('{{X}}',$value['purchase_credit_note_item_tds_percentage'] , $tds_ledger);
                            $tds_ary['ledger_name'] = $tds_ledger;
                            $tds_ary['primary_grp'] = $tds_ledger_name->sub_group_1;
                            $tds_ary['second_grp'] = $tds_ledger_name->sub_group_2;
                            $tds_ary['main_grp'] = $tds_ledger_name->main_group;
                            $tds_ary['default_ledger_id'] = $tds_ledger_name->ledger_id;
                        }
                        $tds_ledger = $this->ledger_model->getGroupLedgerId($tds_ary);
                        /*$tds_ledger = $this->ledger_model->addGroupLedger(array(
                                                                        'ledger_name' => $tds_subgroup.$section_name.'@'.$value['purchase_credit_note_item_tds_percentage'].'%',
                                                                        'subgrp_2' => 'Duties and taxes',
                                                                        'subgrp_1' => '',
                                                                        'main_grp' => 'Current Liabilities',
                                                                        'amount' =>  0
                                                                    ));*/
                    }

                    $tds_title    = $module_type . " " . $payment_type . " under u/s " . $section_name;
                    $tds_subgroup = $tds_subgroup;
                    
                    
                    if (in_array($tds_ledger , $tds_slab)){
                        $tds_slab_items[$tds_ledger] = bcadd($tds_slab_items[$tds_ledger] , $value['purchase_credit_note_item_tds_amount'],$section_modules['access_common_settings'][0]->amount_precision);
                    }else {
                        $tds_slab[]                  = $tds_ledger;
                        $tds_slab_items[$tds_ledger] = $value['purchase_credit_note_item_tds_amount'];
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
        $default_purchase_id = $purchase_ledger['PURCHASE'];
        $purchase_ledger_name = $this->ledger_model->getDefaultLedgerId($default_purchase_id);
            
        $purchase_ary = array(
                        'ledger_name' => 'Purchases',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Purchases group',
                        'amount' => 0
                    );
        if(!empty($purchase_ledger_name)){
            $purchase_ledger_nm = $purchase_ledger_name->ledger_name;
            $purchase_ary['ledger_name'] = $purchase_ledger_nm;
            $purchase_ary['primary_grp'] = $purchase_ledger_name->sub_group_1;
            $purchase_ary['second_grp'] = $purchase_ledger_name->sub_group_2;
            $purchase_ary['main_grp'] = $purchase_ledger_name->main_group;
            $purchase_ary['default_ledger_id'] = $purchase_ledger_name->ledger_id;
        }
        $purchase_credit_note_ledger_id = $this->ledger_model->getGroupLedgerId($purchase_ary);
        /*$purchase_credit_note_ledger_id            = $this->ledger_model->getDefaultLedger('Purchases');
        if($purchase_credit_note_ledger_id == 0){
            $purchase_credit_note_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                                    'ledger_name' => 'Purchases',
                                                                    'subgrp_2' => '',
                                                                    'subgrp_1' => '',
                                                                    'main_grp' => 'Purchases group',
                                                                    'amount' => 0
                                                                ));

        }*/
        $ledgers['purchase_credit_note_ledger_id'] = $purchase_credit_note_ledger_id;

        $string             = 'ledger_id,supplier_name';
        $table              = 'supplier';
        $where              = array('supplier_id' => $data_main['purchase_credit_note_party_id']);
        $supplier_data      = $this->general_model->getRecords($string , $table , $where , $order = "");
        $supplier_name = $supplier_data[0]->supplier_name;
        $supplier_ledger_id = $supplier_data[0]->ledger_id;

        if(!$supplier_ledger_id){
            $supplier_ledger_id = $purchase_ledger['SUPPLIER'];
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
        $ledgers['ledger_to']   = $purchase_credit_note_ledger_id;

        $vouchers              = array();
        $vouchers_new          = array();
        $charges_sub_module_id = $this->config->item('charges_sub_module');

        if ($data_main['purchase_credit_note_gst_payable'] != "yes"){
            $grand_total = $data_main['purchase_credit_note_grand_total'];
        } else {
            $total_tax_amount = ($data_main['purchase_credit_note_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount'] + $data_main['purchase_credit_note_tax_cess_amount']);
            $grand_total      = bcsub($data_main['purchase_credit_note_grand_total'] , $total_tax_amount,$section_modules['access_common_settings'][0]->amount_precision);
        }

        if (isset($data_main['purchase_credit_note_tds_amount']) && $data_main['purchase_credit_note_tds_amount'] > 0){
            $grand_total = bcsub($grand_total , $data_main['purchase_credit_note_tds_amount'],$section_modules['access_common_settings'][0]->amount_precision);
        }
        
        $vouchers_new[] = array(
                            "ledger_from"              => $supplier_ledger_id,
                            "ledger_to"                => $ledgers['ledger_to'] ,
                            "purchase_voucher_id"         => '' ,
                            "voucher_amount"           => $grand_total ,
                            "converted_voucher_amount" => 0,
                            "dr_amount"                => 0,
                            "cr_amount"                => $grand_total ,
                            'ledger_id'                => $supplier_ledger_id
                        );

        $sub_total = $data_main['purchase_credit_note_sub_total'];

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
            $converted_voucher_amount = $sub_total;
        } else{
            $converted_voucher_amount = 0;
        }

        $vouchers_new[] = array(
                            "ledger_from"              => $ledgers['ledger_from'],
                            "ledger_to"                => $purchase_credit_note_ledger_id,
                            "purchase_voucher_id"         => '' ,
                            "voucher_amount"           => $sub_total ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $sub_total ,
                            "cr_amount"                => 0,
                            'ledger_id'                => $purchase_credit_note_ledger_id
                        );

        if ($data_main['purchase_credit_note_tds_amount'] > 0 || $data_main['purchase_credit_note_tcs_amount'] > 0){
            foreach ($tds_slab_items as $key => $value) {
                if ($key == 0){
                    continue;
                }
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $value;
                } else{
                    $converted_voucher_amount = 0;
                }

                /*if ($data_main['purchase_credit_note_tcs_amount'] > 0) {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }*/

                if (!in_array($key, $tds_slab_minus)){
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }

                $vouchers_new[] = array(
                                "ledger_from"              => $key,
                                "ledger_to"                => $ledger_to,
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $value ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $key
                            );
            }
        }
        
        if(!empty($cgst_slab_items) || !empty($sgst_slab_items)){
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

                if (!in_array($key , $cgst_slab_minus)){
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }else{
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                }

                if ($value > 0){
                    $vouchers_new[] = array(
                            "ledger_from"              => $key,
                            "ledger_to"                => $ledger_to,
                            "purchase_voucher_id"         => '' ,
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

                if (!in_array($key , $sgst_slab_minus)) {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }else{
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                }

                $vouchers_new[] = array(
                            "ledger_from"              => $key,
                            "ledger_to"                => $ledger_to,
                            "purchase_voucher_id"         => '' ,
                            "voucher_amount"           => $value ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $dr_amount ,
                            "cr_amount"                => $cr_amount,
                            'ledger_id'                => $key
                        );
            }
        }elseif ($igst_slab_items) {
            foreach ($igst_slab_items as $key => $value) {

                $converted_voucher_amount = 0;
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id'])
                {
                    $converted_voucher_amount = $value;
                }

                if (!in_array($key , $igst_slab_minus)) {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }else{
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                }
                /*$dr_amount = '';
                $cr_amount = $value;
                $ledger_to = $ledgers['ledger_to'];*/

                $vouchers_new[] = array(
                            "ledger_from"              => $key,
                            "ledger_to"                => $ledgers['ledger_to'],
                            "purchase_voucher_id"         => '' ,
                            "voucher_amount"           => $value ,
                            "converted_voucher_amount" => $converted_voucher_amount ,
                            "dr_amount"                => $dr_amount ,
                            "cr_amount"                => $cr_amount,
                            'ledger_id'                => $key
                        );
            }
        }

        if ((($data_main['purchase_credit_note_tax_amount'] > 0  && ($data_main['purchase_credit_note_igst_amount'] > 0 || $data_main['purchase_credit_note_cgst_amount'] > 0 || $data_main['purchase_credit_note_sgst_amount'] > 0)) || $data_main['purchase_credit_note_tax_cess_amount'] > 0 )){
            /*if ($present != "igst"){
                
            }else {
               
            }*/
            if($data_main['purchase_credit_note_tax_cess_amount'] > 0){
                foreach ($cess_slab_items as $key => $value) {

                    $converted_voucher_amount = 0;
                    if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                        $converted_voucher_amount = $value;
                    } 

                    if (!in_array($key , $cess_slab_minus)) {
                        $dr_amount = $value;
                        $cr_amount = '';
                        $ledger_to = $ledgers['ledger_to'];
                    }else{
                        $dr_amount = '';
                        $cr_amount = $value;
                        $ledger_to = $ledgers['ledger_from'];
                    }

                    $vouchers_new[] = array(
                                "ledger_from"              => $key,
                                "ledger_to"                => $ledgers['ledger_to'],
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $value ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $key
                            );
                }
            }
        }
        else if (($data_main['purchase_credit_note_tax_amount'] > 0 && ($data_main['purchase_credit_note_igst_amount'] == 0 && $data_main['purchase_credit_note_cgst_amount'] == 0 && $data_main['purchase_credit_note_sgst_amount'] == 0)) && $data_main['purchase_credit_note_gst_payable'] != "yes"){
            foreach ($tax_slab_items as $key => $value){

                $converted_voucher_amount = 0;
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                    $converted_voucher_amount = $value;
                }

                if (!in_array($key , $tax_slab_minus)){
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }
                else
                {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                }

                $vouchers_new[] = array(
                                    "ledger_from"              => $key,
                                    "ledger_to"                => $ledgers['ledger_to'],
                                    "purchase_voucher_id"         => '' ,
                                    "voucher_amount"           => $value ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => $dr_amount ,
                                    "cr_amount"                => $cr_amount,
                                    'ledger_id'                => $key
                                );
            }
        }

        if (in_array($charges_sub_module_id , $section_modules['access_sub_modules'])){
            $default_Freight_id = $purchase_ledger['Freight'];
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
                $Freight_ary['ledger_name'] = $Freight_ledger;
                $Freight_ary['primary_grp'] = $Freight_ledger_name->sub_group_1;
                $Freight_ary['second_grp'] = $Freight_ledger_name->sub_group_2;
                $Freight_ary['main_grp'] = $Freight_ledger_name->main_group;
                $Freight_ary['default_ledger_id'] = $Freight_ledger_name->ledger_id;
            }
            $freight_charge_ledger_id = $this->ledger_model->getGroupLedgerId($Freight_ary);
            /*$freight_charge_ledger_id = $this->ledger_model->addGroupLedger(array(
                                            'ledger_name' => 'Freight PAID',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_insurance_id = $purchase_ledger['Insurance'];
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
                                            'ledger_name' => 'Insurance Charges PAID',
                                            'subgrp_2' => '',   
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_packing_id = $purchase_ledger['Packing'];
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
                                            'ledger_name' => 'Packing Charges PAID',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_incidental_id = $purchase_ledger['Incidental'];
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
                                            'ledger_name' => 'Incidental Charges PAID',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_other_inclusive_id = $purchase_ledger['Inclusive'];
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
                                            'ledger_name' => 'Other Inclusive Charges PAID',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
                                            'amount' =>  0
                                        ));*/
            $default_other_exclusive_id = $purchase_ledger['Exclusive'];
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
                                            'ledger_name' => 'Other Exclusive Charges PAID',
                                            'subgrp_2' => '',
                                            'subgrp_1' => '',
                                            'main_grp' => 'Direct Expenses',
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
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['freight_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['freight_charge_amount'],
                                "cr_amount"                => 0,
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
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['insurance_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['insurance_charge_amount'],
                                "cr_amount"                => 0,
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
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['packing_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['packing_charge_amount'] ,
                                "cr_amount"                => 0,
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
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $data_main['incidental_charge_amount'] ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $data_main['incidental_charge_amount'] ,
                                "cr_amount"                => 0,
                                'ledger_id'                => $incidental_charge_ledger_id
                            );
            } if (isset($other_inclusive_charge_ledger_id) && $data_main['inclusion_other_charge_amount'] > 0)
            {

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
                                    "purchase_voucher_id"         => '' ,
                                    "voucher_amount"           => $data_main['inclusion_other_charge_amount'] ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => $data_main['inclusion_other_charge_amount'] ,
                                    "cr_amount"                => 0,
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
                                    "purchase_voucher_id"         => '' ,
                                    "voucher_amount"           => $data_main['exclusion_other_charge_amount'] ,
                                    "converted_voucher_amount" => $converted_voucher_amount ,
                                    "dr_amount"                => 0 ,
                                    "cr_amount"                => $data_main['exclusion_other_charge_amount'],
                                    'ledger_id'                => $other_exclusive_charge_ledger_id
                                );
            }
        }

        /* discount slab */
        $discount_sum = 0;
        if ($data_main['purchase_credit_note_discount_amount'] > 0){
            $default_discount_id = $purchase_ledger['Discount'];
            $discount_ledger_name = $this->ledger_model->getDefaultLedgerId($default_discount_id);
                
            $discount_ary = array(
                            'ledger_name' => 'Trade Discount Received',
                            'second_grp' => '',
                            'primary_grp' => '',
                            'main_grp' => 'Direct Income',
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
            /*$discount_ledger_id            = $this->ledger_model->getDefaultLedger('Discount');*/
            /*$discount_ledger_id = $this->ledger_model->addGroupLedger(array(
                                                    'ledger_name' => 'Trade Discount Received',
                                                    'subgrp_1' => '',
                                                    'subgrp_2' => '',
                                                    'main_grp' => 'Direct Expenses',
                                                    'amount' =>  0
                                                ));*/
            $ledgers['discount_ledger_id'] = $discount_ledger_id;

            foreach ($js_data as $key => $value){
                $discount_sum = bcadd($discount_sum , $value['purchase_credit_note_item_discount_amount'],$section_modules['access_common_settings'][0]->amount_precision);
            }

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $converted_voucher_amount = $discount_sum;
            } else {
                $converted_voucher_amount = 0;
            }

            $vouchers_new[] = array(
                                "ledger_from"              => $discount_ledger_id,
                                "ledger_to"                => $purchase_credit_note_ledger_id,
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $discount_sum ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => 0,
                                "cr_amount"                => $discount_sum ,
                                'ledger_id'                => $discount_ledger_id
                            );
        }
        /* discount slab ends */

        /* Round off */

        if ($data_main['round_off_amount'] > 0 || $data_main['round_off_amount'] < 0){

            $round_off_amount = $data_main['round_off_amount'];

            if ($round_off_amount < 0){
                $round_off_amount = $round_off_amount;
                $dr_amount        = $round_off_amount;
                $cr_amount        = 0;

                $ledger_to = $ledgers['ledger_from'];
                $default_roundoff_id = $purchase_ledger['RoundOff_Received'];
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
                $dr_amount        = 0;
                $cr_amount        = $round_off_amount;
                $ledger_to = $ledgers['ledger_to'];
                $default_roundoff_id = $purchase_ledger['RoundOff_Given'];
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
                    'main_grp' => 'Indirect Incomes',
                    'amount' =>  0
                );*/
            }

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                $converted_voucher_amount = $round_off_amount;
            } else {
                $converted_voucher_amount = 0;
            }

           // $round_off_ledger_id            = $this->ledger_model->addLedger($title , $subgroup);
            $round_off_ledger_id            = $this->ledger_model->addGroupLedger($round_off_ary);

            $ledgers['round_off_ledger_id'] = $round_off_ledger_id;

            $vouchers_new[] = array(
                                "ledger_from"              => $round_off_ledger_id,
                                "ledger_to"                => $ledger_to,
                                "purchase_voucher_id"         => '' ,
                                "voucher_amount"           => $round_off_amount ,
                                "converted_voucher_amount" => $converted_voucher_amount ,
                                "dr_amount"                => $dr_amount ,
                                "cr_amount"                => $cr_amount,
                                'ledger_id'                => $round_off_ledger_id
                            );
        }

        $vouchers = array();
        $voucher_keys = array();
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

    public function purchase_credit_note_voucher_entry($data_main , $js_data , $action , $branch){
        $purchase_credit_note_voucher_module_id = $this->config->item('purchase_voucher_module');
        $module_id               = $purchase_credit_note_voucher_module_id;
        $modules                 = $this->get_modules();
        $privilege               = "add_privilege";
        $section_modules         = $this->get_section_modules($purchase_credit_note_voucher_module_id , $modules , $privilege);

        $access_sub_modules    = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings       = $section_modules['access_settings'];

        /* generated voucher number */

        $vouchers = $this->purchase_credit_note_vouchers($section_modules , $data_main , $js_data , $branch);
        $grand_total = $data_main['purchase_credit_note_grand_total'];
        /*if ($data_main['purchase_credit_note_gst_payable'] != "yes"){
        } else {
            $total_tax_amount = ($data_main['purchase_credit_note_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount']);
            $grand_total      = bcsub($data_main['purchase_credit_note_grand_total'] , $total_tax_amount,$section_modules['access_common_settings'][0]->amount_precision);
        }*/

        $table           = 'purchase_voucher';
        $reference_key   = 'purchase_voucher_id';
        $reference_table = 'accounts_purchase_voucher';

        if ($action == "add"){
            /* generated voucher number */
            $primary_id      = "purchase_voucher_id";
            $table_name      = $this->config->item('purchase_voucher_table');
            $date_field_name = "voucher_date";
            $current_date    = $data_main['purchase_credit_note_date'];
            $voucher_number  = $this->generate_invoice_number($access_settings , $primary_id , $table_name , $date_field_name , $current_date);

            $headers = array(
                "voucher_date"      => $data_main['purchase_credit_note_date'] ,
                "voucher_number"    => $voucher_number ,
                "party_id"          => $data_main['purchase_credit_note_party_id'] ,
                "party_type"        => $data_main['purchase_credit_note_party_type'] ,
                "reference_id"      => $data_main['purchase_credit_note_id'] ,
                "reference_type"    => 'purchase_credit_note' ,
                "reference_number"  => $data_main['purchase_credit_note_invoice_number'] ,
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

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']){
                $headers['converted_receipt_amount'] = $grand_total;
            }else{
                $headers['converted_receipt_amount'] = 0;
            }
            $this->general_model->addVouchers($table , $reference_key , $reference_table , $headers , $vouchers);
            
        } else if ($action == "edit"){
            $headers = array(
                "voucher_date"      => $data_main['purchase_credit_note_date'] ,
                "party_id"          => $data_main['purchase_credit_note_party_id'] ,
                "party_type"        => $data_main['purchase_credit_note_party_type'] ,
                "reference_id"      => $data_main['purchase_credit_note_id'] ,
                "reference_type"    => 'purchase_credit_note' ,
                "reference_number"  => $data_main['purchase_credit_note_invoice_number'] ,
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

            $purchase_credit_note_voucher_data = $this->general_model->getRecords('purchase_voucher_id' , 'purchase_voucher' , array(
                'reference_id'  => $data_main['purchase_credit_note_id'],'reference_type' => 'purchase_credit_note','delete_status' => 0));

            if ($purchase_credit_note_voucher_data){
                $purchase_voucher_id        = $purchase_credit_note_voucher_data[0]->purchase_voucher_id;
                $this->general_model->updateData('purchase_voucher', $headers,array('purchase_voucher_id' => $purchase_voucher_id ));
                $string = 'accounts_purchase_id,delete_status,ledger_id,voucher_amount,dr_amount,cr_amount';
                $table = 'accounts_purchase_voucher';
                $where = array('purchase_voucher_id' => $purchase_voucher_id);

                $old_purchase_credit_note_voucher_items = $this->general_model->getRecords($string , $table , $where , $order
                    = "");
                $old_purchase_credit_note_ledger_ids = $this->getValues($old_purchase_credit_note_voucher_items,'ledger_id');
                $not_deleted_ids = array();

                foreach ($vouchers as $key => $value) {
                    if (($led_key = array_search($value['ledger_id'], $old_purchase_credit_note_ledger_ids)) !== false) {
                        unset($old_purchase_credit_note_ledger_ids[$led_key]);
                        $accounts_purchase_id = $old_purchase_credit_note_voucher_items[$led_key]->accounts_purchase_id;
                        array_push($not_deleted_ids,$accounts_purchase_id );
                        $value['purchase_voucher_id'] = $purchase_voucher_id;
                        $value['delete_status']    = 0;
                        $table                     = 'accounts_purchase_voucher';
                        $where                     = array('accounts_purchase_id' => $accounts_purchase_id );
                        $post_data = array('data' => $value,
                                            'where' => $where,
                                            'voucher_date' => $headers['voucher_date'],
                                            'table' => 'purchase_voucher',
                                            'sub_table' => 'accounts_purchase_voucher',
                                            'primary_id' => 'purchase_voucher_id',
                                            'sub_primary_id' => 'purchase_voucher_id'
                                        );
                        $this->general_model->updateBunchVoucherCommon($post_data);
                        $this->general_model->updateData($table , $value , $where);
                    }else{
                        $value['purchase_voucher_id'] = $purchase_voucher_id;
                        $table                     = 'accounts_purchase_voucher';
                        $this->general_model->insertData($table , $value);
                    }
                }

                if(!empty($old_purchase_credit_note_voucher_items)){
                    $revert_ary = array();
                    foreach ($old_purchase_credit_note_voucher_items as $key => $value) {
                        if(!in_array($value->accounts_purchase_id, $not_deleted_ids)){
                            $revert_ary[] = $value;
                            $table      = 'accounts_purchase_voucher';
                            $where      = array('accounts_purchase_id' => $value->accounts_purchase_id );
                            $purchase_credit_note_data = array('delete_status' => 1 );
                            $this->general_model->updateData($table , $purchase_credit_note_data , $where);
                        }
                    }
                    if(!empty($revert_ary)) $this->general_model->revertLedgerAmount($revert_ary,$headers['voucher_date']);
                }
            }
        }
    }

    function edit($id){
        $id                = $this->encryption_url->decode($id);
        /*echo $id;exit();*/
        $data              = $this->get_default_country_state();
        $purchase_credit_note_module_id   = $this->config->item('purchase_module');
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($purchase_credit_note_module_id , $modules , $privilege);
        /* presents all the needed */
        $data              = array_merge($data , $section_modules);
        
        /* Modules Present */
        $data['purchase_credit_note_module_id'] = $purchase_credit_note_module_id;
        $data['module_id']                 = $purchase_credit_note_module_id;
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

        $data['data'] = $this->general_model->getRecords('*' , 'purchase_credit_note' , array(
            'purchase_credit_note_id' => $id ));

        $purchase_invoice = $this->general_model->getRecords('purchase_invoice_number' , 'purchase' , array(
            'purchase_id' => $data['data'][0]->purchase_id ));
        $data['purchase_invoice_number'] = $purchase_invoice[0]->purchase_invoice_number;
        
        $data['shipping_address'] = $this->general_model->getRecords('*' , 'shipping_address' , array(
            'shipping_party_id'   => $data['data'][0]->purchase_credit_note_party_id ,
            'shipping_party_type' => $data['data'][0]->purchase_credit_note_party_type ,
        ));

        $item_types = $this->general_model->getRecords('item_type,purchase_credit_note_item_description' , 'purchase_credit_note_item' , array(
            'purchase_credit_note_id' => $id ));

        $service     = 0;
        $product     = 0;
        $description = 0;
        foreach ($item_types as $key => $value){
            if ($value->purchase_credit_note_item_description != "")
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
        }

        $data['product_exist'] = $product;
        $data['service_exist'] = $service;

        $data['supplier'] = $this->supplier_call();
        $data['currency'] = $this->currency_call();
        $data['tax'] = $this->tax_call();

        if ($data['data'][0]->purchase_credit_note_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax"){
        }

        if ($data['data'][0]->purchase_credit_note_nature_of_supply == "service" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both"){
            $data['sac']              = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }

        if ($data['data'][0]->purchase_credit_note_nature_of_supply == "product" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both") {

            if ($product == 2){
                $data['inventory_access'] = "yes";
            }else {
                $data['inventory_access'] = "no";
            }

            $data['product_category'] = $this->product_category_call();
            $data['uqc']              = $this->uqc_call();
            $data['uqc_service']      = $this->uqc_product_service_call('service');
            $data['uqc_product']      = $this->uqc_product_service_call('product');
            $data['chapter']          = $this->chapter_call();
            $data['hsn']              = $this->hsn_call();
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();

            if ($data['inventory_access'] == "yes"){
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key']          = $this->general_model->getRecords('*' , 'varients' , array(
                    'delete_status' => 0 ,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
            }
        }

        $purchase_credit_note_service_items = array();
        $purchase_credit_note_product_items = array();
        if (($data['data'][0]->purchase_credit_note_nature_of_supply == "service" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both") && $service == 1)
        {

            $service_items       = $this->common->purchase_credit_note_items_service_list_field($id);
            $purchase_credit_note_service_items = $this->general_model->getJoinRecords($service_items['string'] , $service_items['table'] , $service_items['where'] , $service_items['join']);
        }

        if ($data['data'][0]->purchase_credit_note_nature_of_supply == "product" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both")
        {
            /*if ($product == 2)
            {
                $product_items       = $this->common->purchase_credit_note_items_product_inventory_list_field($id);
                $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
            }
            if ($product == 1)
            {
            }
            else */
            $product_items       = $this->common->purchase_credit_note_items_product_list_field($id);
            $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        }

        $data['items'] = array_merge($purchase_credit_note_product_items , $purchase_credit_note_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist        = 0;

        if ($data['data'][0]->purchase_credit_note_tax_amount > 0 && $data['data'][0]->purchase_credit_note_igst_amount > 0 && ($data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {
            /* igst tax slab */
            $igstExist = 1;

        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount > 0 && ($data['data'][0]->purchase_credit_note_cgst_amount > 0 || $data['data'][0]->purchase_credit_note_sgst_amount > 0) && $data['data'][0]->purchase_credit_note_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount > 0 && ($data['data'][0]->purchase_credit_note_igst_amount == 0 && $data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount == 0 && ($data['data'][0]->purchase_credit_note_igst_amount == 0 && $data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if($data['data'][0]->purchase_credit_note_tax_cess_amount > 0){
            $cessExist = 1;
        }

        if ($data['data'][0]->purchase_credit_note_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes")
        {
            /* Discount */
            $discountExist    = 1;
            $data['discount'] = $this->discount_call();
        }
        if ($data['data'][0]->purchase_credit_note_tds_amount > 0 || $data['data'][0]->purchase_credit_note_tcs_amount > 0 || $data['access_settings'][0]->tds_visible == "yes")
        {
            /* Discount */
            $tdsExist = 1;
        }
        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes")
        {
            /* Discount */
            $descriptionExist = 1;
        }

        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_credit_note_billing_state_id);
        
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']        = $cessExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
        /*echo "<pre>";
        print_r($data);
        exit();*/
        $this->load->view('purchase_credit_note/edit' , $data);

    }

    public function edit_purchase_credit_note()
    {

        $data            = $this->get_default_country_state();
        $purchase_credit_note_id        = $this->input->post('purchase_credit_note_id');
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $module_id       = $purchase_credit_note_module_id;
        $modules         = $this->modules;
        $privilege       = "edit_privilege";
        $section_modules = $this->get_section_modules($purchase_credit_note_module_id , $modules , $privilege);


        /* Modules Present */
        $data['purchase_credit_note_module_id']           = $purchase_credit_note_module_id;
        $data['module_id']                 = $purchase_credit_note_module_id;
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
                $primary_id      = "purchase_credit_note_id";
                $table_name      = $this->config->item('purchase_credit_note_table');
                $date_field_name = "purchase_credit_note_date";
                $current_date    = date('Y-m-d',strtotime($this->input->post('invoice_date')));
                $invoice_number  = $this->generate_invoice_number($section_modules['access_settings'] , $primary_id , $table_name , $date_field_name , $current_date);
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
        $purchase_credit_note_data = array(
            "purchase_credit_note_date"                            => date('Y-m-d',strtotime($this->input->post('invoice_date'))) ,
            "purchase_credit_note_invoice_number"                  => $invoice_number ,
            "purchase_supplier_credit_note_date"                   => date('Y-m-d' , strtotime($this->input->post('purchase_supplier_credit_note_date'))),
            "purchase_supplier_credit_note_number"                 => $this->input->post('supplier_credit_note_no'),
            "purchase_credit_note_sub_total"                       => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0 ,
            "purchase_credit_note_grand_total"                     => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0 ,
            "purchase_credit_note_discount_amount"                 => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0 ,
            "purchase_credit_note_tax_amount"                      => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0 ,
            "purchase_credit_note_tax_cess_amount"                 => 0 ,
            "purchase_credit_note_taxable_value"                   => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0 ,
            "purchase_credit_note_tds_amount"                      => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0 ,
            "purchase_credit_note_tcs_amount"                      => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0 ,
            "purchase_credit_note_igst_amount"                     => 0 ,
            "purchase_credit_note_cgst_amount"                     => 0 ,
            "purchase_credit_note_sgst_amount"                     => 0 ,
            "from_account"                          => 'supplier' ,
            "to_account"                            => 'purchase' ,
            "financial_year_id"                     => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
            "purchase_credit_note_party_id"                        => $this->input->post('supplier') ,
            "purchase_credit_note_party_type"                      => "supplier" ,
            "purchase_credit_note_nature_of_supply"                => $this->input->post('nature_of_supply') , 
            "purchase_credit_note_type_of_supply"                  => $this->input->post('type_of_supply') ,
            "purchase_credit_note_gst_payable"                     => $this->input->post('gst_payable') ,
            "purchase_credit_note_billing_country_id"              => $this->input->post('billing_country') ,
            "purchase_credit_note_billing_state_id"                => $this->input->post('billing_state') ,
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
            /*"shipping_address_id"                   => $this->input->post('shipping_address') ,*/
            "warranty"                              => $this->input->post('warranty') ,
            "payment_mode"                          => $this->input->post('payment_mode') ,
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

        $purchase_credit_note_data['freight_charge_tax_id']         = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $purchase_credit_note_data['insurance_charge_tax_id']       = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $purchase_credit_note_data['packing_charge_tax_id']         = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $purchase_credit_note_data['incidental_charge_tax_id']      = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $purchase_credit_note_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $purchase_credit_note_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        $round_off_value = $purchase_credit_note_data['purchase_credit_note_grand_total'];

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" || $this->input->post('round_off_key') == "yes"){
            if($this->input->post('round_off_value') !="" && $this->input->post('round_off_value') > 0 ){
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $purchase_credit_note_data['round_off_amount'] = bcsub($purchase_credit_note_data['purchase_credit_note_grand_total'] , $round_off_value,$section_modules['access_common_settings'][0]->amount_precision);

        $purchase_credit_note_data['purchase_credit_note_grand_total'] = $round_off_value;

        $tax_type         = $this->input->post('tax_type');
        $purchase_credit_note_tax_amount = $purchase_credit_note_data['purchase_credit_note_tax_amount'];
        $purchase_credit_note_tax_amount = $purchase_credit_note_data['purchase_credit_note_tax_amount'] + (float)($this->input->post('total_other_taxable_amount'));

        if ($section_modules['access_settings'][0]->tax_type == "gst"){
            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
            if ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'intra_state') {
                $purchase_credit_note_data['purchase_credit_note_igst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_cgst_amount'] = ($purchase_credit_note_tax_amount * $cgst_amount_percentage) / 100;
                $purchase_credit_note_data['purchase_credit_note_sgst_amount'] = ($purchase_credit_note_tax_amount * $sgst_amount_percentage) / 100;
                $purchase_credit_note_data['purchase_credit_note_tax_cess_amount'] = $total_cess_amnt;

            }elseif($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'inter_state'){
                $purchase_credit_note_data['purchase_credit_note_igst_amount'] = $purchase_credit_note_tax_amount;
                $purchase_credit_note_data['purchase_credit_note_cgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_sgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_tax_cess_amount'] = $total_cess_amnt;

            }elseif ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == "import"){
                $purchase_credit_note_data['purchase_credit_note_igst_amount'] = $purchase_credit_note_tax_amount;
                $purchase_credit_note_data['purchase_credit_note_cgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_sgst_amount'] = 0;
                $purchase_credit_note_data['purchase_credit_note_tax_cess_amount'] = $total_cess_amnt;
            }
            
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')){
            $purchase_credit_note_data['converted_grand_total'] = $purchase_credit_note_data['purchase_credit_note_grand_total'];
        } else {
            $purchase_credit_note_data['converted_grand_total'] = 0;
        }

        $old_purchase_credit_note_data = $this->general_model->getRecords('*', 'purchase_credit_note', array(
            'purchase_credit_note_id' => $purchase_credit_note_id));
        $purchase_id   = $old_purchase_credit_note_data[0]->purchase_id;
        $old_amount = $this->general_model->getRecords("credit_note_amount, converted_credit_note_amount,supplier_payable_amount", "purchase", array(
            'purchase_id' => $purchase_id));
        $new_amount           = bcsub($old_amount[0]->credit_note_amount, $old_purchase_credit_note_data[0]->purchase_credit_note_grand_total, $section_modules['access_common_settings'][0]->amount_precision);
        $cust_new_amount           = bcsub($old_amount[0]->supplier_payable_amount, ($old_purchase_credit_note_data[0]->purchase_credit_note_grand_total - $old_purchase_credit_note_data[0]->purchase_credit_note_tds_amount), $section_modules['access_common_settings'][0]->amount_precision);
        $new_converted_amount = bcsub($old_amount[0]->converted_credit_note_amount, $old_purchase_credit_note_data[0]->converted_grand_total, $section_modules['access_common_settings'][0]->amount_precision);
        $this->general_model->updateData("purchase", array(
            'credit_note_amount'           => $new_amount,
            'converted_credit_note_amount' => $new_converted_amount,
            'supplier_payable_amount' => $cust_new_amount), array(
            'purchase_id' => $purchase_id));

        $data_main   = array_map('trim' , $purchase_credit_note_data);
        $purchase_credit_note_table = $this->config->item('purchase_credit_note_table');
        $where       = array('purchase_credit_note_id' => $purchase_credit_note_id );

        if ($this->general_model->updateData($purchase_credit_note_table , $data_main , $where)){

            $old_amount1 = $this->general_model->getRecords("credit_note_amount,supplier_payable_amount", "purchase", array(
                'purchase_id' => $purchase_id));
   
            $new_amount1 = bcadd($old_amount1[0]->credit_note_amount, $purchase_credit_note_data['purchase_credit_note_grand_total'], $section_modules['access_common_settings'][0]->amount_precision);
            $cust_new_amount1 = bcadd($old_amount1[0]->supplier_payable_amount, ($purchase_credit_note_data['purchase_credit_note_grand_total'] - $purchase_credit_note_data['purchase_credit_note_tds_amount']), $section_modules['access_common_settings'][0]->amount_precision);
            /*echo $purchase_credit_note_data['purchase_credit_note_grand_total'] - $purchase_credit_note_data['purchase_credit_note_tds_amount'].'<br>';
            echo $this->db->last_query();
            echo  $cust_new_amount1;*/
            $this->general_model->updateData("purchase", array(
                'credit_note_amount' => $new_amount1,'supplier_payable_amount' => $cust_new_amount1), array(
                'purchase_id' => $purchase_id));
            $successMsg = 'Purchase Credit Note Updated Successfully';
            $this->session->set_flashdata('purchase_cn_success',$successMsg);
            $log_data              = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID') ,
                'table_id'          => $purchase_credit_note_id ,
                'table_name'        => $purchase_credit_note_table ,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') ,
                'message'           => 'purchase_credit_note Updated' );
            $data_main['purchase_credit_note_id'] = $purchase_credit_note_id;
            $log_table             = $this->config->item('log_table');
            $this->general_model->insertData($log_table , $log_data);
            $purchase_credit_note_item_data       = $this->input->post('table_data');

            $js_data               = json_decode($purchase_credit_note_item_data);
            $js_data               = array_reverse($js_data);
            $item_table            = $this->config->item('purchase_credit_note_item_table');
            if (!empty($js_data)) {
                $js_data1 = array();
                $new_item_ids = $this->getValues($js_data,'item_id'); 
                
                $string          = 'purchase_credit_note_item_id,purchase_credit_note_item_quantity,item_type,item_id';
                $table           = 'purchase_credit_note_item';
                $where           = array(
                    'purchase_credit_note_id'      => $purchase_credit_note_id ,
                    'delete_status' => 0 );
                $old_purchase_credit_note_items = $this->general_model->getRecords($string , $table , $where , $order           = "");
                $old_item_ids = $this->getValues($old_purchase_credit_note_items,'item_id');

                $not_deleted_ids= array();

                foreach ($old_purchase_credit_note_items as $key => $value){
                    if($value->item_type == 'product' ){
                    $product_string = '*';
                    $product_table  = 'products';
                    $product_where  = array(
                        'product_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string , $product_table , $product_where , $order = "");
                    if(!empty($product)){
                        $product_qty    = bcadd($product[0]->product_quantity , $value->purchase_credit_note_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
                        /* update Product Price */
                        $pro_price = $this->getAVGItemPrice($value->item_id );
                        /* END */
                        $product_data        = array(
                            'product_price' => $pro_price,
                            'product_quantity' => $product_qty);

                        /*$product_data   = array('product_quantity' => $product_qty );*/
                        $this->general_model->updateData($product_table , $product_data , $product_where);

                        //update stock history
                        $where   = array(
                            'item_id'        => $value->item_id ,
                            'reference_id'   => $purchase_credit_note_id ,
                            'reference_type' => 'purchase_credit_note' ,
                            'delete_status'  => 0 );
                        $this->db->where($where);
                        $history = $this->db->get('quantity_history')->result();
                        if (!empty($history))
                        {
                            $history_quantity        = bcadd($history[0]->quantity , $value->purchase_credit_note_item_quantity,$section_modules['access_common_settings'][0]->amount_precision);
                            $update_history_quantity = array(
                                'quantity'        => $history_quantity ,
                                'updated_date'    => date('Y-m-d') ,
                                'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                            $this->db->where($where);
                            $this->db->update('quantity_history' , $update_history_quantity);
                        }
                        else
                        {
                            // quantity history
                            $history = array(
                                "item_id"          => $value->item_id ,
                                "item_type"        => 'product' ,
                                "reference_id"     => $purchase_credit_note_id ,
                                "reference_number" => $invoice_number ,
                                "reference_type"   => 'purchase_credit_note' ,
                                "quantity"         => 0 ,
                                "stock_type"       => 'indirect' ,
                                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') ,
                                "added_date"       => date('Y-m-d') ,
                                "entry_date"       => date('Y-m-d') ,
                                "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
                            $this->general_model->insertData("quantity_history" , $history);
                        }
                    }
                }
            }
                foreach ($js_data as $key => $value) {
                    if ($value != null) {
                        $item_id   = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity  = $value->item_quantity;
                        $item_data = array(
                            "item_id"                    => $value->item_id ,
                            "item_type"                  => $value->item_type ,
                            "purchase_credit_note_item_quantity"        => $value->item_quantity ? (float) $value->item_quantity : 0 ,
                            "purchase_credit_note_item_unit_price"      => $value->item_price ? (float) $value->item_price : 0 ,
                            "purchase_credit_note_item_sub_total"       => $value->item_sub_total ? (float) $value->item_sub_total : 0 ,
                            "purchase_credit_note_item_taxable_value"   => $value->item_taxable_value ? (float) $value->item_taxable_value : 0 ,
                            "purchase_credit_note_item_discount_amount" => (@$value->item_discount_amount ? (float) $value->item_discount_amount : 0) ,
                            "purchase_credit_note_item_discount_id"     => (@$value->item_discount_id ? (float) $value->item_discount_id : 0) ,
                            "purchase_credit_note_item_tds_id"          => (@$value->item_tds_id ? (float) $value->item_tds_id : 0) ,
                            "purchase_credit_note_item_tds_percentage"  => (@$value->item_tds_percentage ? (float) $value->item_tds_percentage : 0 ),
                            "purchase_credit_note_item_tds_amount"      => (@$value->item_tds_amount ? (float) $value->item_tds_amount : 0 ),
                            "purchase_credit_note_item_grand_total"     => (@$value->item_grand_total ? (float) $value->item_grand_total : 0 ),
                            "purchase_credit_note_item_tax_id"          => (@$value->item_tax_id ? (float)$value->item_tax_id : 0 ),
                            "purchase_credit_note_item_tax_cess_id"     => (@$value->item_tax_cess_id ? (float)$value->item_tax_cess_id : 0 ),
                            "purchase_credit_note_item_igst_percentage" => 0 ,
                            "purchase_credit_note_item_igst_amount"     => 0 ,
                            "purchase_credit_note_item_cgst_percentage" => 0 ,
                            "purchase_credit_note_item_cgst_amount"     => 0 ,
                            "purchase_credit_note_item_sgst_percentage" => 0 ,
                            "purchase_credit_note_item_sgst_amount"     => 0 ,
                            "purchase_credit_note_item_tax_percentage"  => (@$value->item_tax_percentage ? (float) $value->item_tax_percentage : 0 ),
                            "purchase_credit_note_item_tax_amount"      => (@$value->item_tax_amount ? (float) $value->item_tax_amount : 0 ),
                            "purchase_credit_note_item_tax_cess_percentage"  =>  0 ,
                            "purchase_credit_note_item_tax_cess_amount"      =>  0 ,
                            "purchase_credit_note_item_description"     => (@$value->item_description ? $value->item_description : "" ),
                            "debit_note_quantity"        => 0 ,
                            "purchase_credit_note_id"                   => $purchase_credit_note_id );

                        $purchase_credit_note_item_tax_amount     = $item_data['purchase_credit_note_item_tax_amount'];
                        $purchase_credit_note_item_tax_percentage = $item_data['purchase_credit_note_item_tax_percentage'];

                        if ($section_modules['access_settings'][0]->tax_type == "gst") {
                            $tax_split_percentage   = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = (@$value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = (@$value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0) ;

                            if ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'intra_state') {
                                    $item_data['purchase_credit_note_item_igst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_cgst_amount'] = ($purchase_credit_note_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_credit_note_item_sgst_amount'] = ($purchase_credit_note_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_credit_note_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_credit_note_item_igst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_cgst_percentage'] = ($purchase_credit_note_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_credit_note_item_sgst_percentage'] = ($purchase_credit_note_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_credit_note_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                            } elseif ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'inter_state') {
                                    $item_data['purchase_credit_note_item_igst_amount'] = $purchase_credit_note_item_tax_amount;
                                    $item_data['purchase_credit_note_item_cgst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_credit_note_item_igst_percentage'] = $purchase_credit_note_item_tax_percentage;
                                    $item_data['purchase_credit_note_item_cgst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                
                            }elseif ($purchase_credit_note_data['purchase_credit_note_type_of_supply'] == 'import') {
                                
                                    $item_data['purchase_credit_note_item_igst_amount'] = $purchase_credit_note_item_tax_amount;
                                    $item_data['purchase_credit_note_item_cgst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_amount'] = 0;
                                    $item_data['purchase_credit_note_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_credit_note_item_igst_percentage'] = $purchase_credit_note_item_tax_percentage;
                                    $item_data['purchase_credit_note_item_cgst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_sgst_percentage'] = 0;
                                    $item_data['purchase_credit_note_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                
                            }
                        }
                        $table = 'purchase_credit_note_item';
                        if (($item_key = array_search($value->item_id, $old_item_ids)) !== false) {

                            unset($old_item_ids[$item_key]);
                            $purchase_credit_note_item_id = $old_purchase_credit_note_items[$item_key]->purchase_credit_note_item_id;
                            array_push($not_deleted_ids,$purchase_credit_note_item_id );
                            $where = array('purchase_credit_note_item_id' => $purchase_credit_note_item_id );
                           
                            $this->general_model->updateData($table , $item_data , $where);

                        }else{
                            $this->general_model->insertData($table , $item_data);
                        }
                        /* update product stock */
                        if ($value->item_type == "product" || $value->item_type == 'product_inventory'){
                            $product_string = '*';
                            $product_table  = 'products';
                            $product_where  = array('product_id' => $value->item_id );
                            $product        = $this->general_model->getRecords($product_string , $product_table , $product_where , $order          = "");
                            $product_qty    = bcsub($product[0]->product_quantity , $quantity,$section_modules['access_common_settings'][0]->amount_precision);
                            /* update Product Price */
                            $pro_price = $this->getAVGItemPrice($value->item_id );
                            /* END */
                            $product_data        = array(
                                'product_price' => $pro_price,
                                'product_quantity' => $product_qty);

                            /*$product_data   = array('product_quantity' => $product_qty );*/
                            $this->general_model->updateData($product_table , $product_data , $product_where);

                            //update stock history
                            $where   = array(
                                'item_id'        => $value->item_id ,
                                'reference_id'   => $purchase_credit_note_id ,
                                'reference_type' => 'purchase_credit_note' ,
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
                                    "reference_id"     => $purchase_credit_note_id ,
                                    "reference_number" => $invoice_number ,
                                    "reference_type"   => 'purchase_credit_note' ,
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

                if(!empty($old_purchase_credit_note_items)){
                    foreach ($old_purchase_credit_note_items as $key => $items) {
                        if(!in_array( $items->purchase_credit_note_item_id,$not_deleted_ids)){
                            $table      = 'purchase_credit_note_item';
                            $where      = array(
                                'purchase_credit_note_item_id' => $items->purchase_credit_note_item_id );
                            $purchase_credit_note_data = array(
                                'delete_status' => 1 );
                           
                            $this->general_model->updateData($table , $purchase_credit_note_data , $where);
                        }
                    }
                }
        
                $item_data = $js_data1;
                
                if (in_array($data['accounts_module_id'] , $section_modules['active_add'])){

                    if (in_array($data['accounts_sub_module_id'] , $section_modules['access_sub_modules'])){

                        $action = "edit";
                        $this->purchase_credit_note_voucher_entry($data_main , $js_data1 , $action , $data['branch']);
                    }
                }
            }
            redirect('purchase_credit_note' , 'refresh');
        }else {
            $errorMsg = 'Purchase Credit Note Update Unsuccessful';
            $this->session->set_flashdata('purchase_cn_error',$errorMsg);
            redirect('purchase_credit_note' , 'refresh');
        }
    }

    public function delete(){
        $id                             = $this->input->post('delete_id');
        $purchase_credit_note_id        = $this->encryption_url->decode($id);
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $data['module_id']              = $purchase_credit_note_module_id;
        $modules                        = $this->modules;
        $privilege                      = "delete_privilege";
        $data['privilege']              = "delete_privilege";
        $section_modules                = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);
        $data                           = array_merge($data, $section_modules);

        $data['data'] = $this->general_model->getRecords('*', 'purchase_credit_note', array(
            'purchase_credit_note_id' => $purchase_credit_note_id));
        $data['items'] = $this->general_model->getRecords('*', 'purchase_credit_note_item', array(
            'purchase_credit_note_id' => $purchase_credit_note_id,
            'delete_status'           => 0));

        $purchase_voucher_id = $this->general_model->getRecords('purchase_voucher_id', 'purchase_voucher', array(
            'reference_id'   => $purchase_credit_note_id,
            'reference_type' => 'purchase_credit_note'));

        if(!empty($purchase_voucher_id))
        $this->general_model->deleteVoucher(array('purchase_voucher_id' => $purchase_voucher_id[0]->purchase_voucher_id),'purchase_voucher','accounts_purchase_voucher');
        /*$this->general_model->updateData('purchase_voucher', array(
            'delete_status' => 1), array(
            'reference_id'   => $purchase_credit_note_id,
            'reference_type' => 'purchase_credit_note'));*/

        if ($this->general_model->updateData('purchase_credit_note', array(
            'delete_status' => 1), array(
            'purchase_credit_note_id' => $purchase_credit_note_id)))
        {
            $purchase_id = $data['data'][0]->purchase_id;
            $old_amount  = $this->general_model->getRecords("credit_note_amount, converted_credit_note_amount", "purchase", array(
                'purchase_id' => $purchase_id));
            $new_amount           = bcsub($old_amount[0]->credit_note_amount, $data['data'][0]->purchase_credit_note_grand_total, $section_modules['access_common_settings'][0]->amount_precision);
            $new_converted_amount = bcsub($old_amount[0]->converted_credit_note_amount, $data['data'][0]->converted_grand_total, $section_modules['access_common_settings'][0]->amount_precision);
            $this->general_model->updateData("purchase", array(
                'credit_note_amount'           => $new_amount,
                'converted_credit_note_amount' => $new_converted_amount), array(
                'purchase_id' => $purchase_id));

            foreach ($data['items'] as $key => $value)
            {

                if ($value->item_type == "product" || $value->item_type == "product_inventory")
                {
                    $product_data     = $this->common->product_field($value->item_id);
                    $product_result   = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'],$product_data['join']);
                    $product_quantity = ($product_result[0]->product_quantity - $value->purchase_credit_note_item_quantity);
                    /* update Product Price */
                    $pro_price = $this->getAVGItemPrice($value->item_id );
                    /* END */
                    $data1        = array(
                        'product_price' => $pro_price,
                        'product_quantity' => $product_quantity);
                            
                    /*$data1            = array(
                        'product_quantity' => $product_quantity);*/
                    $where = array(
                        'product_id' => $value->item_id);
                    $product_table = $this->config->item('product_table');
                    $this->general_model->updateData($product_table, $data1, $where);

                    //update stock history
                    $where = array(
                        'item_id'        => $value->item_id,
                        'reference_id'   => $purchase_credit_note_id,
                        'reference_type' => 'purchase_credit_note');

                    $history_data = array(
                        'delete_status'   => 1,
                        'updated_date'    => date('Y-m-d'),
                        'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                    $this->db->where($where);
                    $this->db->update('quantity_history', $history_data);

                }
            }
            $successMsg = 'Purchase Credit Note Deleted Successfully';
            $this->session->set_flashdata('purchase_cn_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                'table_id'          => $purchase_credit_note_id,
                'table_name'        => 'purchase_credit_note',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'purchase_credit_note Deleted');
            $this->general_model->insertData('log', $log_data);

            $redirect = 'purchase_credit_note';
            if($this->input->post('delete_redirect') != '') $redirect = $this->input->post('delete_redirect');
            redirect($redirect , 'refresh');
        }
        else
        {
            $errorMsg = 'Purchase Credit Note Delete Unsuccessful';
            $this->session->set_flashdata('purchase_cn_error',$errorMsg);
            redirect("purchase_credit_note", 'refresh');
        }

    }

    public function view($id)
    {
        $id                          = $this->encryption_url->decode($id);
        $data                        = array();
        $branch_data                 = $this->common->branch_field();
        $data['branch']              = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $data['email_module_id']     = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id']                   = $purchase_credit_note_module_id;
        $data['purchase_credit_note_module_id'] = $purchase_credit_note_module_id;
        $modules                             = $this->modules;
        $privilege                           = "view_privilege";
        $data['privilege']                   = "view_privilege";
        $data['privilege']                   = $privilege;
        $section_modules                     = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $purchase_credit_note_data = $this->common->purchase_credit_note_list_field1($id);

        $data['data'] = $this->general_model->getJoinRecords($purchase_credit_note_data['string'], $purchase_credit_note_data['table'], $purchase_credit_note_data['where'], $purchase_credit_note_data['join']);

        $item_types = $this->general_model->getRecords('item_type,purchase_credit_note_item_description', 'purchase_credit_note_item', array(
            'purchase_credit_note_id' => $id));

        $service     = 0;
        $product     = 0;
        $description = 0;

        foreach ($item_types as $key => $value)
        {

            if ($value->purchase_credit_note_item_description != "")
            {
                $description++;
            }

            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else

            if ($value->item_type == "product")
            {
                $product = 1;
            }
            else

            if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }

        }

        $purchase_credit_note_service_items = array();
        $purchase_credit_note_product_items = array();
      

        if (($data['data'][0]->purchase_credit_note_nature_of_supply == "service" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both") && $service == 1)
        {

            $service_items                   = $this->common->purchase_credit_note_items_service_list_field($id);
            $purchase_credit_note_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_credit_note_nature_of_supply == "product" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both")
        {

            if ($product == 2)
            {
                $product_items                   = $this->common->purchase_credit_note_items_product_inventory_list_field($id);
                $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
            else

            if ($product == 1)
            {
                $product_items                   = $this->common->purchase_credit_note_items_product_list_field($id);
                $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }

        }



        $data['items'] = array_merge($purchase_credit_note_product_items, $purchase_credit_note_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist = 0;

        if($data['data'][0]->purchase_credit_note_tax_cess_amount > 0){
            $cessExist = 1;
        }

        if ($data['data'][0]->purchase_credit_note_tax_amount > 0 && $data['data'][0]->purchase_credit_note_igst_amount > 0 && ($data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount > 0 && ($data['data'][0]->purchase_credit_note_cgst_amount > 0 || $data['data'][0]->purchase_credit_note_sgst_amount > 0) && $data['data'][0]->purchase_credit_note_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount > 0 && ($data['data'][0]->purchase_credit_note_igst_amount == 0 && $data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount == 0 && ($data['data'][0]->purchase_credit_note_igst_amount == 0 && $data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->purchase_credit_note_tcs_amount > 0)
        {
            /* Discount $data['data'][0]->purchase_credit_note_tds_amount > 0 || */
            $tdsExist = 1;
        }

        if ($data['data'][0]->purchase_credit_note_discount_amount > 0)
        {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_credit_note_billing_state_id);

        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']         = $cessExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $data['currency_text']   = $currency[0]->currency_text;
        $this->load->view('purchase_credit_note/view', $data);
    }

    public function pdf($id)
    {
        $id                          = $this->encryption_url->decode($id);
        $data                        = $this->get_default_country_state();
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        $data['module_id']           = $purchase_credit_note_module_id;
        $modules                     = $this->modules;
        $privilege                   = "view_privilege";
        $data['privilege']           = "view_privilege";
        $section_modules             = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);
        $data                        = array_merge($data, $section_modules);

        $product_module_id             = $this->config->item('product_module');
        $service_module_id             = $this->config->item('service_module');
        $supplier_module_id            = $this->config->item('supplier_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $data['currency']       = $this->currency_call();
        $purchase_credit_note_data = $this->common->purchase_credit_note_list_field1($id);
        $data['data']           = $this->general_model->getJoinRecords($purchase_credit_note_data['string'], $purchase_credit_note_data['table'], $purchase_credit_note_data['where'], $purchase_credit_note_data['join']);
        $item_types = $this->general_model->getRecords('item_type,purchase_credit_note_item_description', 'purchase_credit_note_item', array(
            'purchase_credit_note_id' => $id));

        $service     = 0;
        $product     = 0;
        $description = 0;

        foreach ($item_types as $key => $value)
        {

            if ($value->purchase_credit_note_item_description != "")
            {
                $description++;
            }

            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else

            if ($value->item_type == "product")
            {
                $product = 1;
            }
            else

            if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }

        }

        $purchase_credit_note_service_items = array();
        $purchase_credit_note_product_items = array();

        if (($data['data'][0]->purchase_credit_note_nature_of_supply == "service" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both") && $service == 1){

            $service_items                   = $this->common->purchase_credit_note_items_service_list_field($id);
            $purchase_credit_note_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_credit_note_nature_of_supply == "product" || $data['data'][0]->purchase_credit_note_nature_of_supply == "both"){
            /*if ($product == 2){
                $product_items = $this->common->purchase_credit_note_items_product_inventory_list_field($id);
                $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else if ($product == 1){
            }*/
            $product_items = $this->common->purchase_credit_note_items_product_list_field($id);
            $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_credit_note_product_items, $purchase_credit_note_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $descriptionExist = 0;
        $cessExist = 0;

        if($data['data'][0]->purchase_credit_note_tax_cess_amount > 0){
            $cessExist = 1;
        }

        if ($data['data'][0]->purchase_credit_note_tax_amount > 0 && $data['data'][0]->purchase_credit_note_igst_amount > 0 && ($data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount > 0 && ($data['data'][0]->purchase_credit_note_cgst_amount > 0 || $data['data'][0]->purchase_credit_note_sgst_amount > 0) && $data['data'][0]->purchase_credit_note_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount > 0 && ($data['data'][0]->purchase_credit_note_igst_amount == 0 && $data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->purchase_credit_note_tax_amount == 0 && ($data['data'][0]->purchase_credit_note_igst_amount == 0 && $data['data'][0]->purchase_credit_note_cgst_amount == 0 && $data['data'][0]->purchase_credit_note_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->purchase_credit_note_tcs_amount > 0)
        {
            /* Discount $data['data'][0]->purchase_credit_note_tds_amount > 0 || */
            $tdsExist = 1;
        }

        if ($data['data'][0]->purchase_credit_note_discount_amount > 0)
        {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_credit_note_billing_state_id);

        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['cess_exist']         = $cessExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $data['currency_text']   = $currency[0]->currency_name;
        $data['currency_symbol_pdf']   = $currency[0]->currency_symbol_pdf;
        $data['data'][0]->unit = $currency[0]->unit;
        $data['data'][0]->decimal_unit = $currency[0]->decimal_unit;
        if ($purchase_credit_note_product_items && $purchase_credit_note_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($purchase_credit_note_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($purchase_credit_note_service_items)
        {
            $nature_of_supply = "Service";
        }

        $data['nature_of_supply'] = $nature_of_supply;

        $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";

        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];

        $pdf_json            = $data['access_settings'][0]->pdf_settings;
        $rep                 = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);

        $html = $this->load->view('purchase_credit_note/pdf', $data, true);

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

        $dompdf->stream($data['data'][0]->purchase_credit_note_invoice_number, array(
            'Attachment' => 0));

    }

    public function email($id){
        $id                              = $this->encryption_url->decode($id);
        $purchase_credit_note_module_id     = $this->config->item('purchase_credit_note_module');
        $data['module_id']               = $purchase_credit_note_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($purchase_credit_note_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['active_modules'];
        $data['access_sub_modules']      = $section_modules['access_sub_modules'];
        /*$data['access_module_privilege'] = $section_modules['access_module_privilege'];
        $data['access_user_privilege']   = $section_modules['access_user_privilege'];*/
        $data['access_settings']         = $section_modules['access_settings'];
        $data['access_common_settings']  = $section_modules['access_common_settings'];

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $email_sub_module_id         = $this->config->item('email_sub_module');

        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;

            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            }

            if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            }

            if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            }

            if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }

        }

        $email_sub_module = 0;

        foreach ($data['access_sub_modules'] as $key => $value)
        {

            if ($email_sub_module_id == $value)
            {
                $email_sub_module = 1;
            }

        }

        if ($email_sub_module == 1)
        {
            ob_start();
            $html                   = ob_get_clean();
            $html                   = utf8_encode($html);
            $branch_data            = $this->common->branch_field();
            $data['branch']         = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $country_data           = $this->common->country_field($data['branch'][0]->branch_country_id);
            $data['country']        = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data             = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
            $data['state']          = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
            $city_data              = $this->common->city_field($data['branch'][0]->branch_city_id);
            $data['city']           = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
            $data['currency']       = $this->currency_call();
            $purchase_credit_note_data = $this->common->purchase_credit_note_list_field1($id);
            $data['data']           = $this->general_model->getJoinRecords($purchase_credit_note_data['string'], $purchase_credit_note_data['table'], $purchase_credit_note_data['where'], $purchase_credit_note_data['join']);
            $country_data           = $this->common->country_field($data['data'][0]->purchase_credit_note_billing_country_id);
            $data['data_country']   = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data             = $this->common->state_field($data['data'][0]->purchase_credit_note_billing_country_id, $data['data'][0]->purchase_credit_note_billing_state_id);
            $data['data_state']     = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));

            /*if ($inventory_access[0]->inventory_advanced == "yes")
            {
                $product_items                   = $this->common->purchase_credit_note_items_product_inventory_list_field($id);
                $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
            else
            {
            }*/
            $product_items                   = $this->common->purchase_credit_note_items_product_list_field($id);
            $purchase_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);

            $service_items                   = $this->common->purchase_credit_note_items_service_list_field($id);
            $purchase_credit_note_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items']                   = array_merge($purchase_credit_note_product_items, $purchase_credit_note_service_items);
            $igst                            = 0;
            $cgst                            = 0;
            $sgst                            = 0;
            $dpcount                         = 0;
            $dtcount                         = 0;
            $tax_exist                       = 0;
            $tds_exist = 0;
            foreach ($data['items'] as $value)
            {
                $igst = bcadd($igst, $value->purchase_credit_note_item_igst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $cgst = bcadd($cgst, $value->purchase_credit_note_item_cgst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $sgst = bcadd($sgst, $value->purchase_credit_note_item_sgst_amount, $section_modules['access_common_settings'][0]->amount_precision);

                if ($value->purchase_credit_note_item_description != "" && $value->purchase_credit_note_item_description != null)
                {
                    $dpcount++;
                }

                if ($value->purchase_credit_note_item_discount_amount != "" && $value->purchase_credit_note_item_discount_amount != null && $value->purchase_credit_note_item_discount_amount != 0)
                {
                    $dtcount++;
                }

            }

            if($data['data'][0]->purchase_credit_note_tax_amount > 0) $tax_exist= 1;
            if($data['data'][0]->purchase_credit_note_tds_amount > 0) $tds_exist= 1;

            $data['igst_tax']  = $igst;
            $data['cgst_tax']  = $cgst;
            $data['sgst_tax']  = $sgst;
            $data['tax_exist']  = $tax_exist;
            $data['tds_exist']  = $tds_exist;
            $data['dpcount']   = $dpcount;
            $data['dtcount']   = $dtcount;
            $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1']     = $note_data['note1'];
            $data['template1'] = $note_data['template1'];
            $data['note2']     = $note_data['note2'];
            $data['template2'] = $note_data['template2'];
            $currency = $this->getBranchCurrencyCode();
            $data['currency_code']     = $currency[0]->currency_code;
            $data['currency_symbol']   = $currency[0]->currency_symbol;
            $data['currency_text']   = $currency[0]->currency_text;
            $data['currency_symbol_pdf']   = $currency[0]->currency_symbol_pdf;
            $html              = $this->load->view('purchase_credit_note/pdf', $data, true);
           // include APPPATH . 'third_party/mpdf60/mpdf.php';
          //  $mpdf                           = new mPDF();
            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path                      = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_credit_note_invoice_number);
            $file_name = str_replace('/','_',$file_name);
            $dompdf = new Dompdf\Dompdf();
            
            $paper_size  = 'a4';
            $orientation = 'portrait';
            $dompdf->load_html($html);
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);

            /*require_once __DIR__ . '/third_party/autoload.php';
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';*/
            /*$mpdf->WriteHTML($html);*/
            /*$mpdf->Output($file_path . $file_name . '.pdf', 'F');*/
            $data['pdf_file_path']  = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']  = $file_name . '.pdf';
            $purchase_credit_note_data = $this->common->purchase_credit_note_list_field1($id);
            $data['data']           = $this->general_model->getJoinRecords($purchase_credit_note_data['string'], $purchase_credit_note_data['table'], $purchase_credit_note_data['where'], $purchase_credit_note_data['join']);
            $branch_data            = $this->common->branch_field();
            $data['branch']         = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']    = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id'     => $purchase_credit_note_module_id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('purchase_credit_note/email', $data);
        }
        else
        {
            $this->load->view('purchase_credit_note', $data);
        }

    }

    public function convert_currency()
    {
        $id                   = $this->input->post('convert_currency_id');
        $id                   = $this->encryption_url->decode($id);
        $new_converted_amount = $this->input->post('currency_converted_amount');
        $new_converted_rate   = $this->input->post('convertion_rate');
        $credit_note_data     = $this->general_model->getRecords('purchase_id,currency_converted_amount', 'purchase_credit_note', array(
            'purchase_credit_note_id' => $id));
        $old_credit_note_converted_amount = $credit_note_data[0]->currency_converted_amount;

        $purchase_data = $this->general_model->getRecords('converted_credit_note_amount', 'purchase', array(
            'purchase_id' => $credit_note_data[0]->purchase_id));
        $old_purchase_con_credit_amt = $purchase_data[0]->converted_credit_note_amount;

        $converted_credit_amount       = bcsub($old_purchase_con_credit_amt, $old_credit_note_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);
        $total_converted_credit_amount = bcadd($converted_credit_amount, $new_converted_amount, $section_modules['access_common_settings'][0]->amount_precision);

        $data = array(
            'currency_converted_rate'   => $new_converted_rate,
            'currency_converted_amount' => $this->input->post('currency_converted_amount'));
        $this->general_model->updateData('purchase_credit_note', $data, array(
            'purchase_credit_note_id' => $id));

        $pdata = array(
            'converted_credit_note_amount' => $total_converted_credit_amount);
        $this->general_model->updateData('purchase', $pdata, array(
            'purchase_id' => $credit_note_data[0]->purchase_id));

        //update converted voucher amount in account purchase voucher table

        $purchase_voucher_data = array(
            'currency_converted_amount' => $this->input->post('currency_converted_amount'));
        $this->general_model->updateData('purchase_voucher', $purchase_voucher_data, array(
            'reference_id'   => $id,
            'delete_status'  => 0,
            'reference_type' => 'purchase_credit_note'));

        $purchase_voucher = $this->general_model->getRecords('purchase_voucher_id', 'purchase_voucher', array(
            'reference_id'   => $id,
            'delete_status'  => 0,
            'reference_type' => 'purchase_credit_note'));

        $accounts_purchase_voucher = $this->general_model->getRecords('*', 'accounts_purchase_voucher', array(
            'purchase_voucher_id' => $purchase_voucher[0]->purchase_voucher_id,
            'delete_status'       => 0));

        foreach ($accounts_purchase_voucher as $key1 => $value1)
        {
            $new_converted_voucher_amount = bcmul($accounts_purchase_voucher[$key1]->voucher_amount, $new_converted_rate, $section_modules['access_common_settings'][0]->amount_precision);

            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount);
            $where = array(
                'accounts_purchase_id' => $accounts_purchase_voucher[$key1]->accounts_purchase_id);
            $voucher_table = "accounts_purchase_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }

        redirect('purchase_credit_note', 'refresh');
    }

}
