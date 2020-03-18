<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_billing extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'product_model',
                'service_model' ]);
        $this->modules = $this->get_modules();
    }

     function billing(){
        $sales_module_id        = $this->config->item('sales_module');
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;
        $section_modules        = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $this->load->view('pos_billing/billing', $data);
     }

    function index(){
        $sales_module_id        = $this->config->item('sales_module');
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;
        $section_modules        = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];

        /* Modules Present */
        $data['sales_module_id']           = $sales_module_id;
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id']           = $this->config->item('email_module');
        $data['recurrence_module_id']      = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id']  = $this->config->item('recurrence_sub_module');
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'invoice',
                    2 => 'customer',
                    3 => 'customer',
                    4 => 'customer',
                    5 => 'grand_total',
                    6 => 'added_user',
                    7 => 'action'
            );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->pos_billing_list_field();
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
                    $nestedData['date']        = $post->pos_billing_date;
                    $nestedData['invoice']     = $post->pos_billing_invoice_number;
                    // if($post->pos_billing_party_id>0)
                    // {
                    //     $customer_name = $this->general_model->getRecords('customer_name','customer',array('customer_id'=>$post->pos_billing_party_id,'delete_status'=>0));
                    //     $nestedData['customer'] = $customer_name[0]->customer_name;
                    // }
                    // else
                    // {
                    $nestedData['customer']    = $post->pos_billing_party_type;
                    // }
                    $nestedData['mobile']      = $post->pos_billing_mobile;
                    $nestedData['email']       = $post->pos_billing_email;
                    $nestedData['grand_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $post->pos_billing_grand_total;
                    // $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['added_user']  = $post->first_name . ' ' . $post->last_name;
                    $pos_billing_id            = $this->encryption_url->encode($post->pos_billing_id);
                    $cols                      = '<ul class="list-inline">        <li>        <a href="' . base_url('pos_billing/view/') . $pos_billing_id . '"><i class="fa fa-eye text-orange"></i> POS Billing Details</a>        </li>';
                    if (in_array($sales_module_id , $data['active_edit']))
                    {
                        $cols .= '<li><a href="' . base_url('pos_billing/edit/') . $pos_billing_id . '"><i class="fa fa-pencil text-blue"></i> Edit POS Billing</a></li>';
                    }

                    $cols             .= '<li><a href="' . base_url('pos_billing/pdf/') . $pos_billing_id . '" target="_blank"><i class="fa fa-file-pdf-o text-teal" title="Download PDF"></i> Download PDF</a></li>';
                    $email_sub_module = 0;
                    if (in_array($data['receipt_voucher_module_id'] , $data['active_add']))
                    {
                        // foreach ($data['access_sub_modules'] as $key => $value)
                        // {
                            // if ($email_sub_module_id == $value->sub_module_id)
                            // {
                                // $email_sub_module = 1;
                            // }
                        // }
                    }
                    if ($email_sub_module == 1)
                    {
                        $cols .= '<li>                <a href="' . base_url('pos_billing/email/') . $pos_billing_id . '"><i class="fa fa-envelope-o text-purple"></i> Email POS Billing</a>                </li>';
                    }
                    if (in_array($sales_module_id , $data['active_delete']))
                    {
                        $cols .= '<li>       <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $pos_billing_id . '" data-path="pos_billing/delete"  href="#" title="Delete POS Billing" ><i class="fa fa-trash-o text-purple"></i> Delete POS Billing</a>       </li>';
                    }
                    $cols                 .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            }
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data
            );
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('pos_billing/list', $data);
        }
    }

    function add()
    {
        $data                              = $this->get_default_country_state();
        $pos_billing_module_id             = $this->config->item('pos_billing_module');
        $data['module_id']                 = $pos_billing_module_id;
        $modules                           = $this->modules;
        $privilege                         = "add_privilege";
        $data['privilege']                 = "add_privilege";
        $section_modules                   = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $data['access_modules']            = $section_modules['modules'];
        $data['access_sub_modules']        = $section_modules['sub_modules'];
        $data['access_module_privilege']   = $section_modules['module_privilege'];
        $data['access_user_privilege']     = $section_modules['user_privilege'];
        $data['access_settings']           = $section_modules['settings'];
        $data['access_common_settings']    = $section_modules['common_settings'];
        $product_module_id                 = $this->config->item('product_module');
        $service_module_id                 = $this->config->item('service_module');
        $customer_module_id                = $this->config->item('customer_module');
        $category_module_id                = $this->config->item('category_module');
        $subcategory_module_id             = $this->config->item('subcategory_module');
        $tax_module_id                     = $this->config->item('tax_module');
        $discount_module_id                = $this->config->item('discount_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        // $modules_present                   = array('product_module_id' => $product_module_id, 'service_module_id' => $service_module_id, 'customer_module_id' => $customer_module_id);
        $modules_present                   = array(
                'product_module_id'     => $product_module_id,
                'service_module_id'     => $service_module_id,
                'customer_module_id'    => $customer_module_id,
                'category_module_id'    => $category_module_id,
                'subcategory_module_id' => $subcategory_module_id,
                'tax_module_id'         => $tax_module_id,
                'discount_module_id'    => $discount_module_id
        );

        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
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
        $data['customer']         = $this->customer_call();
        $data['discount']         = $this->discount_call();
        $data['currency']         = $this->currency_call();
        $data['product_category'] = $this->product_category_call();
        $data['service_category'] = $this->service_category_call();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_call();
        $data['sac']              = $this->sac_call();
        $data['chapter']          = $this->chapter_call();
        $data['hsn']              = $this->hsn_call();
        $access_settings          = $data['access_settings'];
        $primary_id               = "pos_billing_id";
        $table_name               = 'pos_billing';
        $date_field_name          = "pos_billing_date";
        $current_date             = date('Y-m-d');
        $data['invoice_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('pos_billing/add', $data);
    }

    public function add_pos_billing()
    {
        $pos_billing_module_id = $this->config->item('pos_billing_module');
        $module_id             = $pos_billing_module_id;
        $modules               = $this->modules;
        $privilege             = "add_privilege";
        $section_modules       = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $access_settings       = $section_modules['settings'];
        if ($access_settings[0]->invoice_creation == "automatic")
        {
            $primary_id      = "pos_billing_id";
            $table_name      = 'pos_billing';
            $date_field_name = "pos_billing_date";
            $current_date    = $this->input->post('invoice_date');
            $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        }
        else
        {
            $invoice_number = $this->input->post('invoice_number');
        }
        $pos_billing_data = array(
                "pos_billing_date"           => $this->input->post('invoice_date'),
                "pos_billing_invoice_number" => $invoice_number,
                "pos_billing_sub_total"      => $this->input->post('total_sub_total'),
                "pos_billing_grand_total"    => $this->input->post('total_grand_total'),
                "pos_billing_discount_value" => $this->input->post('total_discount_amount'),
                "pos_billing_tax_amount"     => $this->input->post('total_tax_amount'),
                "pos_billing_taxable_value"  => $this->input->post('total_taxable_amount'),
                "pos_billing_igst_amount"    => $this->input->post('total_igst_amount'),
                "pos_billing_cgst_amount"    => $this->input->post('total_cgst_amount'),
                "pos_billing_sgst_amount"    => $this->input->post('total_sgst_amount'),
                "financial_year_id"          => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                // "pos_billing_party_id"       => $this->input->post('customer'),
                "pos_billing_party_type"     => $this->input->post('customer_name'),
                "pos_billing_mobile"         => $this->input->post('mobile'),
                "pos_billing_email"          => $this->input->post('email'),
                // "pos_billing_nature_of_supply"   => $this->input->post('nature_of_supply'),
                // "pos_billing_type_of_supply"     => $this->input->post('type_of_supply'),
                // "pos_billing_gst_payable"        => $this->input->post('gstPayable'),
                // "pos_billing_billing_country_id" => $this->input->post('billing_country'),
                // "pos_billing_billing_state_id"   => $this->input->post('billing_state'),
                "added_date"                 => date('Y-m-d'),
                "added_user_id"              => $this->session->userdata('SESS_USER_ID'),
                "branch_id"                  => $this->session->userdata('SESS_BRANCH_ID'),
                // "currency_id"                => $this->input->post('currency_id'),
                "note1"                      => $this->input->post('note1'),
                "note2"                      => $this->input->post('note2')
        );

        $data_main         = array_map('trim', $pos_billing_data);
        $pos_billing_table = 'pos_billing';
        if ($pos_billing_id    = $this->general_model->insertData($pos_billing_table, $data_main))
        {
            $log_data                    = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $pos_billing_id,
                    'table_name'        => $pos_billing_table,
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'POS Billing Inserted' );
            $data_main['pos_billing_id'] = $pos_billing_id;
            $log_table                   = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $pos_billing_item_data       = $this->input->post('table_data');
            $js_data                     = json_decode($pos_billing_item_data);
            $item_table                  = 'pos_billing_item';
            foreach ($js_data as $key => $value)
            {
                if ($value == null)
                {

                }
                else
                {
                    $item_id   = $value->item_id;
                    $item_type = $value->item_type;
                    $quantity  = $value->item_quantity;
                    $item_data = array(
                            "item_id"                          => $value->item_id,
                            "item_type"                        => $value->item_type,
                            "pos_billing_item_quantity"        => $value->item_quantity,
                            "pos_billing_item_unit_price"      => $value->item_price,
                            "pos_billing_item_sub_total"       => $value->item_sub_total,
                            "pos_billing_item_taxable_value"   => $value->item_taxable_value,
                            "pos_billing_item_discount_amount" => $value->item_discount_amount,
                            "pos_billing_item_discount_id"     => $value->item_discount,
                            "pos_billing_item_grand_total"     => $value->item_grand_total,
                            "pos_billing_item_igst_percentage" => $value->item_igst,
                            "pos_billing_item_igst_amount"     => $value->item_igst_amount,
                            "pos_billing_item_cgst_percentage" => $value->item_cgst,
                            "pos_billing_item_cgst_amount"     => $value->item_cgst_amount,
                            "pos_billing_item_sgst_percentage" => $value->item_sgst,
                            "pos_billing_item_sgst_amount"     => $value->item_sgst_amount,
                            "pos_billing_item_tax_percentage"  => $value->item_tax_percentage,
                            "pos_billing_item_tax_amount"      => $value->item_tax_amount,
                            "pos_billing_item_description"     => $value->item_description,
                            "pos_billing_id"                   => $pos_billing_id );
                    $data_item = array_map('trim', $item_data);
                    if ($this->general_model->insertData($item_table, $data_item))
                    {
                        if ($data_item['item_type'] == "product")
                        {
                            $product_data     = $this->common->product_field($data_item['item_id']);
                            $product_result   = $this->general_model->getRecords($product_data['string'], $product_data['table'], $product_data['where']);
                            $product_quantity = ($product_result[0]->product_quantity - $value->item_quantity);
                            $paid_amount      = array(
                                    'product_quantity' => $product_quantity );
                            $where            = array(
                                    'product_id' => $value->item_id );
                            $product_table    = $this->config->item('product_table');
                            $this->general_model->updateData($product_table, $paid_amount, $where);
                        }
                        else if ($data_item['item_type'] == "product_inventory")
                        {
                            $product_data     = $this->common->product_inventory_field($data_item['item_id']);
                            $product_result   = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);
                            $product_quantity = ($product_result[0]->quantity - $value->item_quantity);
                            $qty              = array(
                                    'quantity' => $product_quantity );
                            $where            = array(
                                    'product_inventory_varients_id' => $value->item_id );
                            $product_table    = 'product_inventory_varients';
                            $this->general_model->updateData($product_table, $qty, $where);

                            // quantity history
                            $history = array(
                                    "item_id"          => $value->item_id,
                                    "item_type"        => 'product_inventory',
                                    "reference_id"     => $pos_billing_id,
                                    "reference_number" => $invoice_number,
                                    "reference_type"   => 'pos_billing',
                                    "quantity"         => $value->item_quantity,
                                    "stock_type"       => 'indirect',
                                    "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                                    "added_date"       => date('Y-m-d'),
                                    "entry_date"       => date('Y-m-d'),
                                    "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
                            $this->general_model->insertData("quantity_history", $history);
                        }
                    }
                }
            }
            redirect('pos_billing', 'refresh');
        }
        else
        {
            redirect('pos_billing', 'refresh');
        }
    }

    function edit($id)
    {
        $id                                = $this->encryption_url->decode($id);
        $data                              = $this->get_default_country_state();
        $pos_billing_module_id             = $this->config->item('pos_billing_module');
        $data['module_id']                 = $pos_billing_module_id;
        $modules                           = $this->modules;
        $privilege                         = "edit_privilege";
        $data['privilege']                 = "edit_privilege";
        $section_modules                   = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $data['access_modules']            = $section_modules['modules'];
        $data['access_sub_modules']        = $section_modules['sub_modules'];
        $data['access_module_privilege']   = $section_modules['module_privilege'];
        $data['access_user_privilege']     = $section_modules['user_privilege'];
        $data['access_settings']           = $section_modules['settings'];
        $data['access_common_settings']    = $section_modules['common_settings'];
        $product_module_id                 = $this->config->item('product_module');
        $service_module_id                 = $this->config->item('service_module');
        $customer_module_id                = $this->config->item('customer_module');
        $category_module_id                = $this->config->item('category_module');
        $subcategory_module_id             = $this->config->item('subcategory_module');
        $tax_module_id                     = $this->config->item('tax_module');
        $discount_module_id                = $this->config->item('discount_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id']    = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id']     = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']       = $this->config->item('notes_sub_module');
        // $modules_present                   = array('product_module_id' => $product_module_id, 'service_module_id' => $service_module_id, 'customer_module_id' => $customer_module_id);
        $modules_present                   = array(
                'product_module_id'     => $product_module_id,
                'service_module_id'     => $service_module_id,
                'customer_module_id'    => $customer_module_id,
                'category_module_id'    => $category_module_id,
                'subcategory_module_id' => $subcategory_module_id,
                'tax_module_id'         => $tax_module_id,
                'discount_module_id'    => $discount_module_id
        );
        $data['other_modules_present']     = $this->other_modules_present($modules_present, $modules['modules']);
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

        $data['customer']         = $this->customer_call1();
        $data['discount']         = $this->discount_call1();
        $data['currency']         = $this->currency_call();
        $data['product_category'] = $this->product_category_call();
        $data['service_category'] = $this->service_category_call();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_call();
        $data['sac']              = $this->sac_call();
        $data['chapter']          = $this->chapter_call();
        $data['hsn']              = $this->hsn_call();
        $access_settings          = $data['access_settings'];
        $primary_id               = "pos_billing_id";
        $table_name               = 'pos_billing';
        $date_field_name          = "pos_billing_date";
        $current_date             = date('Y-m-d');
        $data['data']             = $this->general_model->getRecords('*', 'pos_billing', array(
                'pos_billing_id' => $id ));

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));


        if ($inventory_access[0]->inventory_advanced == "yes")
        {
            $product_items                   = $this->common->pos_billing_items_product_inventory_list_field($id);
            $pos_billing_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }
        else
        {
            $product_items                   = $this->common->pos_billing_items_product_list_field($id);
            $pos_billing_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items                   = $this->common->pos_billing_items_service_list_field($id);
        $pos_billing_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items']                   = array_merge($pos_billing_items_product_items, $pos_billing_items_service_items);

        $igst    = 0;
        $cgst    = 0;
        $sgst    = 0;
        $dpcount = 0;
        $dtcount = 0;
        foreach ($data['items'] as $value)
        {
            $igst = bcadd($igst, $value->pos_billing_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->pos_billing_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->pos_billing_item_sgst_amount, 2);
        }
        $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;

        $this->load->view('pos_billing/edit', $data);
    }

    public function edit_pos_billing()
    {
        $pos_billing_id        = $this->input->post('pos_billing_id');
        $pos_billing_module_id = $this->config->item('pos_billing_module');
        $module_id             = $pos_billing_module_id;
        $modules               = $this->modules;
        $privilege             = "edit_privilege";
        $section_modules       = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $access_settings       = $section_modules['settings'];

        if ($access_settings[0]->invoice_creation == "automatic")
        {
            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old'))
            {
                $primary_id      = "pos_billing_id";
                $table_name      = 'pos_billing';
                $date_field_name = "pos_billing_date";
                $current_date    = $this->input->post('invoice_date');
                $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
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
        $pos_billing_data = array(
                "pos_billing_date"           => $this->input->post('invoice_date'),
                "pos_billing_invoice_number" => $invoice_number,
                "pos_billing_sub_total"      => $this->input->post('total_sub_total'),
                "pos_billing_grand_total"    => $this->input->post('total_grand_total'),
                "pos_billing_discount_value" => $this->input->post('total_discount_amount'),
                "pos_billing_tax_amount"     => $this->input->post('total_tax_amount'),
                "pos_billing_taxable_value"  => $this->input->post('total_taxable_amount'),
                "pos_billing_igst_amount"    => $this->input->post('total_igst_amount'),
                "pos_billing_cgst_amount"    => $this->input->post('total_cgst_amount'),
                "pos_billing_sgst_amount"    => $this->input->post('total_sgst_amount'),
                "financial_year_id"          => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                // "pos_billing_party_id"           => $this->input->post('customer'),
                "pos_billing_party_type"     => $this->input->post('customer_name'),
                "pos_billing_mobile"         => $this->input->post('mobile'),
                "pos_billing_email"          => $this->input->post('email'),
                // "pos_billing_nature_of_supply"   => $this->input->post('nature_of_supply'),
                // "pos_billing_type_of_supply"     => $this->input->post('type_of_supply'),
                // "pos_billing_gst_payable"        => $this->input->post('gstPayable'),
                // "pos_billing_billing_country_id" => $this->input->post('billing_country'),
                // "pos_billing_billing_state_id"   => $this->input->post('billing_state'),
                "updated_date"               => date('Y-m-d'),
                "updated_user_id"            => $this->session->userdata('SESS_USER_ID'),
                "branch_id"                  => $this->session->userdata('SESS_BRANCH_ID'),
                "note1"                      => $this->input->post('note1'),
                "note2"                      => $this->input->post('note2') );

        $data_main         = array_map('trim', $pos_billing_data);
        $pos_billing_table = 'pos_billing';
        $where             = array(
                'pos_billing_id' => $pos_billing_id );
        if ($this->general_model->updateData($pos_billing_table, $data_main, $where))
        {
            $log_data                    = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $pos_billing_id,
                    'table_name'        => $pos_billing_table,
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'POS Billing Updated' );
            $data_main['pos_billing_id'] = $pos_billing_id;
            $log_table                   = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $pos_billing_item_data       = $this->input->post('table_data');
            $js_data                     = json_decode($pos_billing_item_data);
            $item_table                  = $this->config->item('sales_item_table');
            foreach ($js_data as $key => $value)
            {
                if ($value == null)
                {

                }
                else
                {
                    $item_id     = $value->item_id;
                    $item_type   = $value->item_type;
                    $quantity    = $value->item_quantity;
                    $item_data[] = array(
                            "item_id"                          => $value->item_id,
                            "item_type"                        => $value->item_type,
                            "pos_billing_item_quantity"        => $value->item_quantity,
                            "pos_billing_item_unit_price"      => $value->item_price,
                            "pos_billing_item_sub_total"       => $value->item_sub_total,
                            "pos_billing_item_taxable_value"   => $value->item_taxable_value,
                            "pos_billing_item_discount_amount" => $value->item_discount_amount,
                            "pos_billing_item_discount_id"     => $value->item_discount,
                            "pos_billing_item_grand_total"     => $value->item_grand_total,
                            "pos_billing_item_igst_percentage" => $value->item_igst,
                            "pos_billing_item_igst_amount"     => $value->item_igst_amount,
                            "pos_billing_item_cgst_percentage" => $value->item_cgst,
                            "pos_billing_item_cgst_amount"     => $value->item_cgst_amount,
                            "pos_billing_item_sgst_percentage" => $value->item_sgst,
                            "pos_billing_item_sgst_amount"     => $value->item_sgst_amount,
                            "pos_billing_item_tax_percentage"  => $value->item_tax_percentage,
                            "pos_billing_item_tax_amount"      => $value->item_tax_amount,
                            "pos_billing_item_description"     => $value->item_description,
                            "pos_billing_id"                   => $pos_billing_id );
                }
            }
            $string                = 'pos_billing_item_id,pos_billing_item_quantity,item_type,item_id';
            $table                 = 'pos_billing_item';
            $where                 = array(
                    'pos_billing_id' => $pos_billing_id,
                    'delete_status'  => 0 );
            $old_pos_billing_items = $this->general_model->getRecords($string, $table, $where, $order                 = "");

            foreach ($old_pos_billing_items as $key => $value)
            {
                if ($value->item_type == "product")
                {
                    $product_string = '*';
                    $product_table  = 'products';
                    $product_where  = array(
                            'product_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string, $product_table, $product_where, $order          = "");
                    $product_qty    = bcadd($product[0]->product_quantity, $value->pos_billing_item_quantity, 0);
                    $product_data   = array(
                            'product_quantity' => $product_qty );
                    $this->general_model->updateData($product_table, $product_data, $product_where);
                }
                else if ($value->item_type == "product_inventory")
                {
                    $product_string = '*';
                    $product_table  = 'product_inventory_varients';
                    $product_where  = array(
                            'product_inventory_varients_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string, $product_table, $product_where, $order          = "");
                    $product_qty    = bcadd($product[0]->quantity, $value->pos_billing_item_quantity, 0);
                    $product_data   = array(
                            'quantity' => $product_qty );
                    $this->general_model->updateData($product_table, $product_data, $product_where);

                    //update stock history
                    $where                   = array(
                            'item_id'        => $value->item_id,
                            'reference_id'   => $pos_billing_id,
                            'reference_type' => 'pos_billing',
                            'delete_status'  => 0 );
                    $this->db->where($where);
                    $history                 = $this->db->get('quantity_history')->result();
                    $history_quantity        = bcadd($history[0]->quantity, $value->pos_billing_item_quantity);
                    $update_history_quantity = array(
                            'quantity'        => $history_quantity,
                            'updated_date'    => date('Y-m-d'),
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                    $this->db->where($where);
                    $this->db->update('quantity_history', $update_history_quantity);
                }
            }

            if (count($old_pos_billing_items) == count($item_data))
            {
                foreach ($old_pos_billing_items as $key => $value)
                {
                    $table = 'pos_billing_item';
                    $where = array(
                            'pos_billing_item_id' => $value->pos_billing_item_id );
                    $this->general_model->updateData($table, $item_data[$key], $where);
                }
            }
            else if (count($old_pos_billing_items) < count($item_data))
            {
                foreach ($old_pos_billing_items as $key => $value)
                {
                    $table = 'pos_billing_item';
                    $where = array(
                            'pos_billing_item_id' => $value->pos_billing_item_id );
                    $this->general_model->updateData($table, $item_data[$key], $where);
                    $i     = $key;
                } for ($j = $i + 1; $j < count($item_data); $j++)
                {
                    $table = 'pos_billing_item';
                    $this->general_model->insertData($table, $item_data[$j]);
                }
            }
            else
            {
                foreach ($old_pos_billing_items as $key => $value)
                {
                    $table = 'pos_billing_item';
                    $where = array(
                            'pos_billing_item_id' => $value->pos_billing_item_id );
                    $this->general_model->updateData($table, $item_data[$key], $where);
                    $i     = $key;
                    if (($key + 1) == count($item_data))
                    {
                        break;
                    }
                }
                for ($j = $i + 1; $j < count($old_pos_billing_items); $j++)
                {
                    $table      = 'pos_billing_item';
                    $where      = array(
                            'pos_billing_item_id' => $old_pos_billing_items[$j]->pos_billing_item_id );
                    $sales_data = array(
                            'delete_status' => 1 );
                    $this->general_model->updateData($table, $sales_data, $where);
                }
            }

            $string                = 'pos_billing_item_id,pos_billing_item_quantity,item_type,item_id';
            $table                 = 'pos_billing_item';
            $where                 = array(
                    'pos_billing_id' => $pos_billing_id,
                    'delete_status'  => 0 );
            $new_pos_billing_items = $this->general_model->getRecords($string, $table, $where, $order                 = "");

            foreach ($new_pos_billing_items as $key => $value)
            {
                if ($value->item_type == "product")
                {
                    $product_string = '*';
                    $product_table  = 'products';
                    $product_where  = array(
                            'product_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string, $product_table, $product_where, $order          = "");
                    $product_qty    = bcsub($product[0]->product_quantity, $value->pos_billing_item_quantity, 0);
                    $product_data   = array(
                            'product_quantity' => $product_qty );
                    $this->general_model->updateData($product_table, $product_data, $product_where);
                }
                else if ($value->item_type == "product_inventory")
                {
                    $product_string = '*';
                    $product_table  = 'product_inventory_varients';
                    $product_where  = array(
                            'product_inventory_varients_id' => $value->item_id );
                    $product        = $this->general_model->getRecords($product_string, $product_table, $product_where, $order          = "");
                    $product_qty    = bcsub($product[0]->quantity, $value->pos_billing_item_quantity, 0);
                    $product_data   = array(
                            'quantity' => $product_qty );
                    $this->general_model->updateData($product_table, $product_data, $product_where);

                    //update stock history
                    $where                   = array(
                            'item_id'        => $value->item_id,
                            'reference_id'   => $pos_billing_id,
                            'reference_type' => 'pos_billing',
                            'delete_status'  => 0
                    );
                    $this->db->where($where);
                    $history                 = $this->db->get('quantity_history')->result();
                    $history_quantity        = bcsub($history[0]->quantity, $value->pos_billing_item_quantity);
                    $update_history_quantity = array(
                            'quantity'        => $history_quantity,
                            'updated_date'    => date('Y-m-d'),
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                    $this->db->where($where);
                    $this->db->update('quantity_history', $update_history_quantity);
                }
            }

            redirect('pos_billing', 'refresh');
        }
        else
        {
            redirect('pos_billing', 'refresh');
        }
    }

    public function view($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $pos_billing_module_id           = $this->config->item('pos_billing_module');
        $data['module_id']               = $pos_billing_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $product_module_id               = $this->config->item('product_module');
        $service_module_id               = $this->config->item('service_module');
        $customer_module_id              = $this->config->item('customer_module');
        $modules_present                 = array(
                'product_module_id'  => $product_module_id,
                'service_module_id'  => $service_module_id,
                'customer_module_id' => $customer_module_id );
        $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);
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
        $pos_billing_data = $this->common->pos_billing_list_field($id);
        $data['data']     = $this->general_model->getJoinRecords($pos_billing_data['string'], $pos_billing_data['table'], $pos_billing_data['where'], $pos_billing_data['join']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));

        if ($data['data'][0]->pos_billing_party_id > 0)
        {
            $string = 'c.*,ct.city_name as customer_city_name, st.state_name as customer_state_name, cu.country_name as customer_country_name';
            $table  = 'customer c';
            $join   = [
                    'cities ct'    => 'c.customer_city_id = ct.city_id',
                    'states st'    => 'c.customer_state_id = st.state_id',
                    'countries cu' => 'c.customer_country_id = cu.country_id' ];
            $where  = array(
                    'c.customer_id'   => $data['data'][0]->pos_billing_party_id,
                    'c.delete_status' => 0 );

            $data['customer'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }

        if ($inventory_access[0]->inventory_advanced == "yes")
        {
            $product_items             = $this->common->pos_billing_items_product_inventory_list_field($id);
            $pos_billing_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }
        else
        {
            $product_items             = $this->common->pos_billing_items_product_list_field($id);
            $pos_billing_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items             = $this->common->pos_billing_items_service_list_field($id);
        $pos_billing_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items']             = array_merge($pos_billing_product_items, $pos_billing_service_items);
        $branch_data               = $this->common->branch_field();
        $data['branch']            = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data              = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']           = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state']             = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data                 = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']              = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data['currency']          = $this->currency_call();
        $igst                      = 0;
        $cgst                      = 0;
        $sgst                      = 0;
        $dpcount                   = 0;
        $dtcount                   = 0;
        foreach ($data['items'] as $value)
        {
            $igst = bcadd($igst, $value->pos_billing_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->pos_billing_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->pos_billing_item_sgst_amount, 2);
            if ($value->pos_billing_item_description != "" && $value->pos_billing_item_description != null)
            {
                $dpcount++;
            } if ($value->pos_billing_item_discount_amount != "" && $value->pos_billing_item_discount_amount != null)
            {
                $dtcount++;
            }
        }
        $data['igst_tax'] = $igst;
        $data['cgst_tax'] = $cgst;
        $data['sgst_tax'] = $sgst;
        $data['dpcount']  = $dpcount;
        $data['dtcount']  = $dtcount;
        $this->load->view('pos_billing/view', $data);
    }

    public function pdf($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $pos_billing_module_id           = $this->config->item('pos_billing_module');
        $data['module_id']               = $pos_billing_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $product_module_id               = $this->config->item('product_module');
        $service_module_id               = $this->config->item('service_module');
        $customer_module_id              = $this->config->item('customer_module');
        $data['notes_sub_module_id']     = $this->config->item('notes_sub_module');
        $modules_present                 = array(
                'product_module_id'  => $product_module_id,
                'service_module_id'  => $service_module_id,
                'customer_module_id' => $customer_module_id );
        $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);
        $pos_billing_data                = $this->common->pos_billing_list_field($id);
        $data['data']                    = $this->general_model->getJoinRecords($pos_billing_data['string'], $pos_billing_data['table'], $pos_billing_data['where'], $pos_billing_data['join']);

        if ($data['data'][0]->pos_billing_party_id > 0)
        {
            $string = 'c.*,ct.city_name as customer_city_name, st.state_name as customer_state_name, cu.country_name as customer_country_name';
            $table  = 'customer c';
            $join   = [
                    'cities ct'    => 'c.customer_city_id = ct.city_id',
                    'states st'    => 'c.customer_state_id = st.state_id',
                    'countries cu' => 'c.customer_country_id = cu.country_id' ];
            $where  = array(
                    'c.customer_id'   => $data['data'][0]->pos_billing_party_id,
                    'c.delete_status' => 0 );

            $data['customer'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));


        if ($inventory_access[0]->inventory_advanced == "yes")
        {
            $product_items             = $this->common->pos_billing_items_product_inventory_list_field($id);
            $pos_billing_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }
        else
        {
            $product_items             = $this->common->pos_billing_items_product_list_field($id);
            $pos_billing_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $service_items             = $this->common->pos_billing_items_service_list_field($id);
        $pos_billing_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items']             = array_merge($pos_billing_product_items, $pos_billing_service_items);
        // if ($pos_billing_product_items && $pos_billing_service_items)
        // {
        //     $nature_of_supply = "Product/Service";
        // }
        // elseif ($pos_billing_product_items)
        // {
        //     $nature_of_supply = "Product";
        // }
        // elseif ($pos_billing_service_items)
        // {
        //     $nature_of_supply = "Service";
        // }
        // $data['nature_of_supply'] = $nature_of_supply;
        $branch_data               = $this->common->branch_field();
        $data['branch']            = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data              = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']           = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state']             = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data                 = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']              = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data['currency']          = $this->currency_call();
        $data['invoice_type']      = "ORIGINAL FOR RECIPIENT";
        $igst                      = 0;
        $cgst                      = 0;
        $sgst                      = 0;
        $dpcount                   = 0;
        $dtcount                   = 0;
        foreach ($data['items'] as $value)
        {
            $cgst = bcadd($cgst, $value->pos_billing_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->pos_billing_item_sgst_amount, 2);
            if ($value->pos_billing_item_description != "" && $value->pos_billing_item_description != null)
            {
                $dpcount++;
            } if ($value->pos_billing_item_discount_amount != "" && $value->pos_billing_item_discount_amount != null && $value->pos_billing_item_discount_amount != 0)
            {
                $dtcount++;
            }
        }
        $data['igst_tax']  = $igst;
        $data['cgst_tax']  = $cgst;
        $data['sgst_tax']  = $sgst;
        $data['dpcount']   = $dpcount;
        $data['dtcount']   = $dtcount;
        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        ob_start();
        $html              = ob_get_clean();
        $html              = utf8_encode($html);

        $pdf                 = $this->general_model->getRecords('settings.*', 'settings', [
                'module_id' => 2,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);
        // echo "<pre>";
        // print_r($pdf);
        // exit;
        $pdf_json            = $pdf[0]->pdf_settings;
        $rep                 = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);

        $html                           = $this->load->view('pos_billing/pdf', $data, true);
        include(APPPATH . 'third_party/mpdf60/mpdf.php');
        $mpdf                           = new mPDF();
        $mpdf->allow_charset_conversion = true;
        $mpdf->charset_in               = 'UTF-8';
        $mpdf->WriteHTML($html);
        $mpdf->Output($data['data'][0]->pos_billing_invoice_number . '.pdf', 'I');
    }

    public function delete()
    {
        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        $pos_billing_module_id           = $this->config->item('pos_billing_module');
        $pos_billing_module_id           = $this->config->item('pos_billing_module');
        $data['module_id']               = $pos_billing_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $product_module_id               = $this->config->item('product_module');
        $service_module_id               = $this->config->item('service_module');
        $customer_module_id              = $this->config->item('customer_module');
        $modules_present                 = array(
                'product_module_id'  => $product_module_id,
                'service_module_id'  => $service_module_id,
                'customer_module_id' => $customer_module_id );
        $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);

        $data['items'] = $this->general_model->getRecords('*', 'pos_billing_item', array(
                'pos_billing_id' => $id ));
        $this->general_model->updateData('pos_billing_item', array(
                'delete_status' => 1 ), array(
                'pos_billing_id' => $id ));

        foreach ($data['items'] as $key => $value)
        {
            if ($value->item_type == "product" && $value->pos_billing_item_quantity > 0)
            {
                $product_data     = $this->common->product_field($value->item_id);
                $product_result   = $this->general_model->getRecords($product_data['string'], $product_data['table'], $product_data['where']);
                $product_quantity = ($product_result[0]->product_quantity + $value->pos_billing_item_quantity);
                $data             = array(
                        'product_quantity' => $product_quantity );
                $where            = array(
                        'product_id' => $value->item_id );
                $product_table    = $this->config->item('product_table');
                $this->general_model->updateData($product_table, $data, $where);
            }
            else if ($value->item_type == "product_inventory" && $value->pos_billing_item_quantity > 0)
            {
                $product_data     = $this->common->product_inventory_field($value->item_id);
                $product_result   = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join'], $product_data['order']);
                $product_quantity = ($product_result[0]->quantity + $value->pos_billing_item_quantity);
                $data             = array(
                        'quantity' => $product_quantity );
                $where            = array(
                        'product_inventory_varients_id' => $value->item_id );
                $product_table    = 'product_inventory_varients';
                $this->general_model->updateData($product_table, $data, $where);

                //update stock history
                $where = array(
                        'item_id'        => $val->item_id,
                        'reference_id'   => $id,
                        'reference_type' => 'sales' );

                $history_data = array(
                        'delete_status'   => 1,
                        'updated_date'    => date('Y-m-d'),
                        'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                $this->db->where($where);
                $this->db->update('quantity_history', $history_data);
            }
        }

        if ($this->general_model->updateData('pos_billing', array(
                        'delete_status' => 1 ), array(
                        'pos_billing_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'pos_billing',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'POS Billing Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('pos_billing');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Category can not be Deleted.');
            redirect("quotation", 'refresh');
        }
    }

    public function email($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $pos_billing_module_id           = $this->config->item('pos_billing_module');
        $data['module_id']               = $pos_billing_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($pos_billing_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];

        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $email_sub_module_id         = $this->config->item('email_sub_module');
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
        $email_sub_module = 0;
        foreach ($data['access_sub_modules'] as $key => $value)
        {
            if ($email_sub_module_id == $value->sub_module_id)
            {
                $email_sub_module = 1;
            }
        }
        if ($email_sub_module == 1)
        {
            ob_start();
            $html             = ob_get_clean();
            $html             = utf8_encode($html);
            $branch_data      = $this->common->branch_field();
            $data['branch']   = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $country_data     = $this->common->country_field($data['branch'][0]->branch_country_id);
            $data['country']  = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data       = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
            $data['state']    = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
            $city_data        = $this->common->city_field($data['branch'][0]->branch_city_id);
            $data['city']     = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
            $data['currency'] = $this->currency_call();
            $pos_billing_data = $this->common->pos_billing_list_field($id);
            $data['data']     = $this->general_model->getJoinRecords($pos_billing_data['string'], $pos_billing_data['table'], $pos_billing_data['where'], $pos_billing_data['join']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status' => 0 ));


            if ($inventory_access[0]->inventory_advanced == "yes")
            {
                $product_items             = $this->common->pos_billing_items_product_inventory_list_field($id);
                $pos_billing_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
            else
            {
                $product_items             = $this->common->pos_billing_items_product_list_field($id);
                $pos_billing_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }

            $service_items             = $this->common->pos_billing_items_service_list_field($id);
            $pos_billing_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items']             = array_merge($pos_billing_product_items, $pos_billing_service_items);
            if ($pos_billing_product_items && $pos_billing_service_items)
            {
                $nature_of_supply = "Product/Service";
            }
            elseif ($pos_billing_product_items)
            {
                $nature_of_supply = "Product";
            }
            elseif ($pos_billing_service_items)
            {
                $nature_of_supply = "Service";
            } $data['nature_of_supply'] = $nature_of_supply;
            $igst                     = 0;
            $cgst                     = 0;
            $sgst                     = 0;
            $dpcount                  = 0;
            $dtcount                  = 0;
            foreach ($data['items'] as $value)
            {
                $igst = bcadd($igst, $value->pos_billing_item_igst_amount, 2);
                $cgst = bcadd($cgst, $value->pos_billing_item_cgst_amount, 2);
                $sgst = bcadd($sgst, $value->pos_billing_item_sgst_amount, 2);
                if ($value->pos_billing_item_description != "" && $value->pos_billing_item_description != null)
                {
                    $dpcount++;
                } if ($value->pos_billing_item_discount_amount != "" && $value->pos_billing_item_discount_amount != null && $value->pos_billing_item_discount_amount != 0)
                {
                    $dtcount++;
                }
            }
            $data['igst_tax']     = $igst;
            $data['cgst_tax']     = $cgst;
            $data['sgst_tax']     = $sgst;
            $data['dpcount']      = $dpcount;
            $data['dtcount']      = $dtcount;
            $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
            $note_data            = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
            $data['note1']        = $note_data['note1'];
            $data['template1']    = $note_data['template1'];
            $data['note2']        = $note_data['note2'];
            $data['template2']    = $note_data['template2'];

            $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                    'module_id' => 2,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);

            $pdf_json            = $pdf[0]->pdf_settings;
            $rep                 = str_replace("\\", '', $pdf_json);
            $data['pdf_results'] = json_decode($rep, true);

            $html                           = $this->load->view('pos_billing/pdf', $data, true);
            include(APPPATH . 'third_party/mpdf60/mpdf.php');
            $mpdf                           = new mPDF();
            $mpdf->allow_charset_conversion = true;
            $mpdf->charset_in               = 'UTF-8';
            $file_path                      = "././pdf_form/";
            $mpdf->WriteHTML($html);
            $file_name                      = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->pos_billing_invoice_number);
            $file_name = str_replace('/','_',$file_name);
            $mpdf->Output($file_path . $file_name . '.pdf', 'F');
            $data['pdf_file_path']          = 'pdf_form/' . $file_name . '.pdf';
            $data['pdf_file_name']          = $file_name . '.pdf';
            $pos_billing_data               = $this->common->pos_billing_list_field($id);
            $data['data']                   = $this->general_model->getJoinRecords($pos_billing_data['string'], $pos_billing_data['table'], $pos_billing_data['where'], $pos_billing_data['join']);
            $branch_data                    = $this->common->branch_field();
            $data['branch']                 = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup']            = $this->general_model->getRecords('*', 'email_setup', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'added_user_id' => $this->session->userdata('SESS_USER_ID') ));
            $data['email_template']         = $this->general_model->getRecords('*', 'email_template', array(
                    'module_id'     => $pos_billing_module_id,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status' => 0 ));
            $this->load->view('pos_billing/email', $data);
        }
        else
        {
            $this->load->view('pos_billing', $data);
        }
    }

}

