<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->model('general_model');
        $this->load->model('ledger_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
        $purchase_module_id = $this->config->item('purchase_module');
        $data['purchase_module_id'] = $purchase_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);
        $access_common_settings = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['payment_voucher_module_id'] = $this->config->item('payment_voucher_module');
        $data['purchase_voucher_module_id'] = $this->config->item('purchase_voucher_module');
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');
        $data['purchase_return_module_id'] = $this->config->item('purchase_return_module');

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'supplier',
                2 => 'grand_total',
                3 => 'converted_grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'added_user',
                7 => 'action');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->purchase_list_field();
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

            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $purchase_file = $post->purchase_file;
                    $purchase_id = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->purchase_date));
                    $nestedData['supplier'] = $post->supplier_name . ' (<a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>)<br> ';
                    $url = base_url().'filemanager/?directory=Purchase';
                    if($purchase_file != ''){
                        $nestedData['supplier'] = ' <a href="' . $url.'"target="_blank">' . '<i class="fa fa-folder-open" aria-hidden="true" title="Open Attachment"></i>' . '</a>'.$post->supplier_name .'  '. '(<a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>)<br> ';
                    }
                    $nestedData['grand_total'] = /* $post->currency_symbol . */' ' . $this->precise_amount($post->purchase_grand_total, $access_common_settings[0]->amount_precision) . ' (INV)';

                    if ($post->credit_note_amount > 0) {
                        $nestedData['grand_total'] .= '<br>' . /* $post->currency_symbol . */ ' ' . $this->precise_amount($post->credit_note_amount, $access_common_settings[0]->amount_precision) . ' (CN)';
                    }
                    if ($post->debit_note_amount > 0) {
                        $nestedData['grand_total'] .= '<br>' . /* $post->currency_symbol . */ ' ' . $this->precise_amount($post->debit_note_amount, $access_common_settings[0]->amount_precision) . ' (DN)';
                    }
                    $supplier_ledger_id = $post->supplier_ledger_id;
                    $purchase_ledg = $this->config->item('purchase_ledger');
                    if(!$supplier_ledger_id){
                        $supplier_ledger_id = $purchase_ledg['SUPPLIER'];
                        $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                            
                        $supplier_ary = array(
                                        'ledger_name' => $post->supplier_name,
                                        'second_grp' => '',
                                        'primary_grp' => 'Sundry Creditors',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => 0,
                                        'default_value' => $post->supplier_name,
                                        'amount' => 0
                                    );
                        if(!empty($supplier_ledger_name)){
                            $supplier_ledger = $supplier_ledger_name->ledger_name;
                            /*$supplier_ledger = str_ireplace('{{SECTION}}',$section_name , $supplier_ledger);*/
                            $supplier_ledger = str_ireplace('{{X}}', $post->supplier_name, $supplier_ledger);
                            $supplier_ary['ledger_name'] = $supplier_ledger;
                            $supplier_ary['primary_grp'] = $supplier_ledger_name->sub_group_1;
                            $supplier_ary['second_grp'] = $supplier_ledger_name->sub_group_2;
                            $supplier_ary['main_grp'] = $supplier_ledger_name->main_group;
                            $supplier_ary['default_ledger_id'] = $supplier_ledger_name->ledger_id;
                        }
                        $supplier_ledger_id = $this->ledger_model->getGroupLedgerId($supplier_ary);
                    }
                    /* get supplier ledger ID */
                    /*$supplier_ledger_id = $this->ledger_model->addGroupLedger(array(
                        'ledger_name' => $post->supplier_name,
                        'subgrp_2' => 'Sundry Creditors',
                        'subgrp_1' => '',
                        'main_grp' => 'Current Assets',
                        'amount' => 0
                    ));*/
                    $this->db->select('purchase_voucher_id');
                    $this->db->from('purchase_voucher');
                    $this->db->where('reference_id',$post->purchase_id);
                    $this->db->where('delete_status',0);
                    $this->db->where('reference_type','purchase');
                    $get_pv_qry = $this->db->get();
                    $ref_id = $get_pv_qry->result();
                    $purchase_voucher_id = '';
                    if(!empty($ref_id)){
                        $purchase_voucher_id = $ref_id[0]->purchase_voucher_id;
                    }
                    $this->db->select('voucher_amount,voucher_type');
                    $this->db->from('purchase s');
                    $this->db->join('purchase_voucher sv', 's.purchase_id=sv.reference_id', 'left');
                    $this->db->join('accounts_purchase_voucher a', 'sv.purchase_voucher_id=a.purchase_voucher_id', 'left');
                    $this->db->where('s.purchase_id', $post->purchase_id);
                    $this->db->where('sv.delete_status', 0);
                    $this->db->where('sv.reference_type', 'purchase');
                    $this->db->where('a.ledger_id', $supplier_ledger_id);

                    $get_supplier_qry = $this->db->get();
                    $purchase_ledgers = $get_supplier_qry->result();

                    $this->db->select('voucher_amount,voucher_type, purchase_credit_note_id, purchase_credit_note_invoice_number');
                    $this->db->from('purchase_credit_note s');
                    $this->db->join('purchase_voucher sv', 's.purchase_credit_note_id=sv.reference_id', 'left');
                    $this->db->join('accounts_purchase_voucher a', 'sv.purchase_voucher_id=a.purchase_voucher_id', 'left');
                    $this->db->where('s.purchase_id', $post->purchase_id);
                    $this->db->where('sv.delete_status', 0);
                    $this->db->where('sv.reference_type', 'purchase_credit_note');
                    $this->db->where('a.ledger_id', $supplier_ledger_id);

                    $get_supplier_qry = $this->db->get();
                    $purchase_credit_ledgers = $get_supplier_qry->result();

                    $this->db->select('voucher_amount,voucher_type,reference_type, purchase_debit_note_id, purchase_debit_note_invoice_number');
                    $this->db->from('purchase_debit_note s');
                    $this->db->join('purchase_voucher sv', 's.purchase_debit_note_id=sv.reference_id', 'left');
                    $this->db->join('accounts_purchase_voucher a', 'sv.purchase_voucher_id=a.purchase_voucher_id', 'left');
                    $this->db->where('s.purchase_id', $post->purchase_id);
                    $this->db->where('sv.delete_status', 0);
                    $this->db->where('sv.reference_type', 'purchase_debit_note');
                    $this->db->where('a.ledger_id', $supplier_ledger_id);

                    $get_supplier_qry = $this->db->get();
                    /* print_r($this->db->last_query()); */
                    $purchase_debit_ledgers = $get_supplier_qry->result();

                    /* calculate total receiable and net recevable */
                    $net_receivable = 0;
                    $total_receivable = array();
                    $nestedData['total_receivable'] = '';
                    if (!empty($purchase_ledgers)) {
                        foreach ($purchase_ledgers as $k => $led) {
                            $net_receivable += $led->voucher_amount;
                            array_push($total_receivable, $this->precise_amount($led->voucher_amount, $access_common_settings[0]->amount_precision) . '(INV)');
                        }
                    }
                    if (!empty($purchase_credit_ledgers)) {
                        foreach ($purchase_credit_ledgers as $key => $led) {
                            $purchase_credit_note_id = $this->encryption_url->encode($led->purchase_credit_note_id);
                            $nestedData['supplier'] .= ' (<a href="' . base_url('purchase_credit_note/view/') . $purchase_credit_note_id . '">' . $led->purchase_credit_note_invoice_number . '</a>)<br> ';
                            array_push($total_receivable, $this->precise_amount($led->voucher_amount, $access_common_settings[0]->amount_precision) . '(CN)');
                            $net_receivable += $led->voucher_amount;
                        }
                    }

                    if (!empty($purchase_debit_ledgers)) {
                        /* print_r($purchase_debit_ledgers); */
                        foreach ($purchase_debit_ledgers as $key => $led) {
                            $purchase_debit_note_id = $this->encryption_url->encode($led->purchase_debit_note_id);
                            $nestedData['supplier'] .= ' (<a href="' . base_url('purchase_debit_note/view/') . $purchase_debit_note_id . '">' . $led->purchase_debit_note_invoice_number . '</a>)<br> ';
                            array_push($total_receivable, $this->precise_amount($led->voucher_amount, $access_common_settings[0]->amount_precision) . '(DN)');
                            $net_receivable -= $led->voucher_amount;
                        }
                    }
                    /*if($purchase_voucher_id != ''){
                        $purchase_voucher_id = $this->encryption_url->encode($purchase_voucher_id);
                        $nestedData['purchase_voucher_view'] = ' <a href="' .base_url('purchase_voucher/view_details/') . $purchase_voucher_id.'" target="_blank">' . '<i class="fa fa-file" aria-hidden="true" title="Voucher View"></i>' . '</a>'. '  ' .' <form  action="' .base_url('purchase_ledger').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$purchase_voucher_id.'"><button type="submit">' . '<i class="fa fa-file" aria-hidden="true" title="Ledger View"></i></button></form>';
                    }*/
                    $nestedData['total_receivable'] = implode("<br>", $total_receivable);
                    $nestedData['net_receivable'] = $this->precise_amount($net_receivable, $access_common_settings[0]->amount_precision);

                    $this->db->select('payment_amount,payment_total_paid');
                    $this->db->where('reference_id', $post->purchase_id);
                    $this->db->where('delete_status', '0');
                    $this->db->where('reference_type', 'purchase');
                    $receipt_qry = $this->db->get('payment_invoice_reference');
                    $receipt_voucher = $receipt_qry->result();
                    $receipt = array();
                    $total_recived = 0;

                    if (!empty($receipt_voucher)) {
                        foreach ($receipt_voucher as $key => $value) {
                            $total_recived += $value->payment_total_paid;
                            array_push($receipt, $this->precise_amount($value->payment_total_paid, $access_common_settings[0]->amount_precision) . "(RCP)");
                        }
                    }

                    $nestedData['received_amount'] = '0.00';
                    if (!empty($receipt)) {
                        $nestedData['received_amount'] = implode("<br>", $receipt);
                    }

                    $pending_amount = $net_receivable - $total_recived;
                    $nestedData['pending_amount'] = '0.00';
                    $nestedData['pending_amount'] = $this->precise_amount(($pending_amount), $access_common_settings[0]->amount_precision);

                    $nestedData['converted_grand_total'] = $this->precise_amount($post->converted_grand_total, $access_common_settings[0]->amount_precision);
                    $nestedData['paid_amount'] = $this->precise_amount($post->purchase_paid_amount, $access_common_settings[0]->amount_precision);

                    if (round($pending_amount, 2) <= 0) {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } else if ($net_receivable > $pending_amount) {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    } else {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;

                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    if (in_array($purchase_module_id, $data['active_view'])) {
                        $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="View Purchase" href="' . base_url('purchase/view/') . $purchase_id . '"><i class="fa fa-eye"></i></a></span>';
                        $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Download PDF" href="' . base_url('purchase/pdf/') . $purchase_id . '" target="_blank"><i class="fa fa-file-pdf-o"></i></a></span>';
                    }
                    /* $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModal1"><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Follow Up Dates" href="#" onclick="addToModel(' . $post->purchase_id . ')"><i class="fa fa-eye"></i></a>'; */

                    if ($pending_amount > 0) {
                        if (in_array($data['payment_voucher_module_id'], $data['active_add'])) {
                            $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Pay Now" href="' . base_url('payment_voucher/add_purchase_payment/') . $purchase_id . '"><i class="fa fa-money"></i></a></span>';
                        }
                    }

                    if (in_array($purchase_module_id, $data['active_edit'])) {
                        if ($post->is_edit == '1') {
                            if($net_receivable == $pending_amount){
                                $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Edit Purchase" href="' . base_url('purchase/edit/') . $purchase_id . '"><i class="fa fa-pencil"></i></a></span>';
                            }
                        }
                    }

                    /* if (in_array($data['purchase_return_module_id'], $data['access_sub_modules']))
                      {
                      $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Purchase Return" href="' . base_url('purchase/purchase_return/') . $purchase_id . '"><i class="fa fa-truck"></i></a></span>';
                      } */

                    /* if (in_array($purchase_module_id, $data['active_view']))
                      {
                      if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                      {
                      $cols .= '<span><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Email Purchase" href="' . base_url('purchase/email/') . $purchase_id . '"><i class="fa fa-envelope-o"></i></a></span>';
                      }
                      } */
                    /* if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                      {
                      $cols .= '<span data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal"><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" data-id="' . $purchase_id . '" data-path="purchase/convert_currency" data-currency_code="" data-grand_total="' . $post->purchase_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-money"></i></a></span>';
                      } */


                    if($purchase_voucher_id != ''){
                        $purchase_voucher_id = $this->encryption_url->encode($purchase_voucher_id);
                        if(in_array($data['purchase_voucher_module_id'], $data['active_view'])){
                            $cols .= '<span><a href="' .base_url('purchase_voucher/view_details/') . $purchase_voucher_id.'" target="_blank" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="View Voucher"><i class="fa fa-eye"></i></a></span>';

                            $cols .= '<span><form  action="' .base_url('purchase_ledger').'" method="POST" target="_blank"><input type="hidden" name="reference_id" value="'.$purchase_voucher_id.'"><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" class="btn btn-app" title="View Ledger"><button type="submit" class="sales_action">' . '<i class="fa fa-eye" aria-hidden="true"></i></button></a></form></span>';
                        }
                    } 

                    if (in_array($purchase_module_id, $data['active_delete'])) {
                        if($post->purchase_paid_amount == 0){
                            $cols .= '<span data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $purchase_id . '" data-path="purchase/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" ><a class="btn btn-app" data-toggle="tooltip" data-placement="bottom" href="#" title="Delete Purchase" ><i class="fa fa-trash-o"></i></a></span>';
                        }
                    }
                    $LeatherCraft_id = $this->config->item('LeatherCraft');

                    if($LeatherCraft_id == $this->session->userdata('SESS_BRANCH_ID')){
                        $cols .= '<span><a href="' .base_url('purchase/exportProductExcel/') . $purchase_id.'" target="_blank" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="View Voucher"><i class="fa fa-eye"></i></a></span>';
                    }
                    
                    $cols .= '</div></div>';
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
            $this->load->view('purchase/list', $data);
        }
    }

    public function add() {
        $data = $this->get_default_country_state();
        $purchase_module_id = $this->config->item('purchase_module');
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
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
        $data['uqc_module_id']        = $this->config->item('uqc_module');
        /* Sub Modules Present */
        $data['payment_voucher_module_id'] = $this->config->item('payment_voucher_module');
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $data['brands'] = $this->brand_call();
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
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();

            if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }
        }
        $data['department'] = $this->general_model->getRecords('*', 'department', array('delete_status' => 0,'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        $access_settings = $data['access_settings'];
        $primary_id = "purchase_id";
        $table_name = $this->config->item('purchase_table');
        $date_field_name = "purchase_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        /* echo "<pre>";
          print_r($data); exit(); */
        $this->load->view('purchase/add', $data);
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

    public function add_purchase() {
        $data = $this->get_default_country_state();
        $purchase_module_id = $this->config->item('purchase_module');
        $module_id = $purchase_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
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

        $access_settings = $section_modules['access_settings'];

        $currency = $this->input->post('currency_id');
        if ($access_settings[0]->invoice_creation == "automatic") {
            $primary_id = "purchase_id";
            $table_name = $this->config->item('purchase_table');
            $date_field_name = "purchase_date";
            $current_date = date('Y-m-d', strtotime($this->input->post('invoice_date')));
            $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }
        $supplier = explode("-", $this->input->post('supplier'));
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;

        if (isset($_FILES["purchase_file"]["name"]) && $_FILES["purchase_file"]["name"] != ""){
            $path_parts = pathinfo($_FILES["purchase_file"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Purchase')){
                mkdir('./assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Purchase', 0777, TRUE);
            } 
            $url = 'assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Purchase/'.$image_path;
            if (in_array($path_parts['extension'], array("JPG","jpg","jpeg","JPEG","PNG","png","pdf","PDF" ))){
                if (is_uploaded_file($_FILES["purchase_file"]["tmp_name"])){
                    if (move_uploaded_file($_FILES["purchase_file"]["tmp_name"], $url)){
                        $image_name = $image_path;
                    }
                }
            }
        }else{
            $image_name = '';
        }
        
        $purchase_data = array(
            "purchase_date" => date('Y-m-d', strtotime($this->input->post('invoice_date'))),
            "purchase_invoice_number" => $invoice_number,
            "purchase_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "purchase_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "purchase_discount_amount" => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0,
            "purchase_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "purchase_tax_cess_amount" => 0,
            "purchase_taxable_value" => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0,
            "purchase_tds_amount" => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0,
            "purchase_tcs_amount"
            => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0,
            "purchase_igst_amount" => 0,
            "purchase_cgst_amount" => 0,
            "purchase_sgst_amount" => 0,
            "from_account" => 'supplier',
            "to_account" => 'purchase',
            "purchase_paid_amount" => 0,
            "credit_note_amount" => 0,
            "debit_note_amount" => 0,
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "purchase_party_id" => $this->input->post('supplier'),
            "purchase_party_type" => "supplier",
            "purchase_nature_of_supply" => $this->input->post('nature_of_supply'),
            "purchase_type_of_supply" => $this->input->post('type_of_supply'),
            "purchase_order_number" => $this->input->post('order_number'),
            "purchase_order_date" => ($this->input->post('purchase_order_date') != '' ? date('Y-m-d', strtotime($this->input->post('purchase_order_date'))) : ''),
            "purchase_gst_payable" => $this->input->post('gst_payable'),
            "due_days"  => $this->input->post('due_days'),
            "purchase_billing_country_id" => $this->input->post('billing_country'),
            "purchase_billing_state_id" => $this->input->post('billing_state'),
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "purchase_supplier_invoice_number" => $this->input->post('supplier_ref'),
            "purchase_supplier_date" => ($this->input->post('supplier_date') != '' ? date('Y-m-d', strtotime($this->input->post('supplier_date'))) : ''),
            "purchase_delivery_challan_number" => $this->input->post('delivery_challan_number'),
            "purchase_delivery_date" => ($this->input->post('delivery_date') != '' ? date('Y-m-d', strtotime($this->input->post('delivery_date'))) : ''),
            "purchase_received_via" => $this->input->post('received_via'),
            "purchase_e_way_bill_number" => $this->input->post('e_way_bill'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "currency_id" => $this->input->post('currency_id'),
            "updated_date" => "",
            "updated_user_id" => "",
            "warehouse_id" => "",
            "transporter_name" => $this->input->post('transporter_name'),
            "transporter_gst_number" => $this->input->post('transporter_gst_number'),
            "lr_no" => $this->input->post('lr_no'),
            "purchase_grn_number" => $this->input->post('grn_number'),
            "purchase_grn_date" => ($this->input->post('grn_date') != '' ? date('Y-m-d', strtotime($this->input->post('grn_date'))) : ''),
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
            "note2" => $this->input->post('note2'),
            "purchase_file" => $image_name
        );

        $purchase_data['freight_charge_tax_id'] = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $purchase_data['insurance_charge_tax_id'] = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $purchase_data['packing_charge_tax_id'] = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $purchase_data['incidental_charge_tax_id'] = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $purchase_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $purchase_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;

        /*if(@$this->input->post('cmb_department')){
            $purchase_data['department_id'] = $this->input->post('cmb_department');
        }
        
        if(@$this->input->post('cmb_subdepartment')){
            $purchase_data['sub_department_id'] = $this->input->post('cmb_subdepartment');
        }*/

        $round_off_value = $purchase_data['purchase_grand_total'];
        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" && $this->input->post('round_off_key') == "yes") {
            if ($this->input->post('round_off_value') != "" && $this->input->post('round_off_value') > 0) {
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $purchase_data['round_off_amount'] = bcsub($purchase_data['purchase_grand_total'], $round_off_value, $section_modules['access_common_settings'][0]->amount_precision);

        $purchase_data['purchase_grand_total'] = $round_off_value;

        $purchase_data['supplier_payable_amount'] = $purchase_data['purchase_grand_total'];
        if (isset($purchase_data['purchase_tds_amount']) && $purchase_data['purchase_tds_amount'] > 0) {
            $purchase_data['supplier_payable_amount'] = bcsub($purchase_data['purchase_grand_total'], $purchase_data['purchase_tds_amount']);
        }

        //$purchase_tax_amount = $purchase_data['purchase_tax_amount'];
        $purchase_tax_amount = $purchase_data['purchase_tax_amount'] + (float) ($this->input->post('total_other_taxable_amount'));

        if ($section_modules['access_settings'][0]->tax_type == "gst") {

            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

            if ($purchase_data['purchase_type_of_supply'] != 'import') {
                if ($purchase_data['purchase_type_of_supply'] == 'intra_state') {
                    $purchase_data['purchase_igst_amount'] = 0;
                    $purchase_data['purchase_cgst_amount'] = ($purchase_tax_amount * $cgst_amount_percentage) / 100;
                    $purchase_data['purchase_sgst_amount'] = ($purchase_tax_amount * $sgst_amount_percentage) / 100;
                    $purchase_data['purchase_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $purchase_data['purchase_igst_amount'] = $purchase_tax_amount;
                    $purchase_data['purchase_cgst_amount'] = 0;
                    $purchase_data['purchase_sgst_amount'] = 0;
                    $purchase_data['purchase_tax_cess_amount'] = $total_cess_amnt;
                }
            } /* else {
              if ($purchase_data['purchase_type_of_supply'] == "export_with_payment"){
              $purchase_data['purchase_igst_amount'] = $purchase_tax_amount;
              $purchase_data['purchase_cgst_amount'] = 0;
              $purchase_data['purchase_sgst_amount'] = 0;
              $purchase_data['purchase_tax_cess_amount'] = $total_cess_amnt;
              }
              } */
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $purchase_data['converted_grand_total'] = $purchase_data['purchase_grand_total'];
        } else {
            $purchase_data['converted_grand_total'] = 0;
        }

        $data_main = array_map('trim', $purchase_data);
        $purchase_table = $this->config->item('purchase_table');
        /* echo "<pre>";
          print_r($this->input->post());
          echo "<br>";
          echo $purchase_data['purchase_billing_country_id'];
          print_r($purchase_data);
          exit(); */
        $purchase_id = $this->general_model->insertData($purchase_table, $data_main);

        if ($purchase_id) {
            $successMsg = 'Purchase Added Successfully';
            $this->session->set_flashdata('purchase_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $purchase_id,
                'table_name' => $purchase_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'purchase Inserted');
            $data_main['purchase_id'] = $purchase_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $purchase_item_data = $this->input->post('table_data');
            $js_data = json_decode($purchase_item_data);
            $js_data               = array_reverse($js_data);
            $item_table = $this->config->item('purchase_item_table');
            $LeatherCraft_id = $this->config->item('LeatherCraft');
            if (!empty($js_data)) {
                $js_data1 = array();
                foreach ($js_data as $key => $value) {

                    /*SK Customization*/
                    if($value->item_id == 0){
                        $product_module_id = $this->config->item('product_module');
                        $data['module_id'] = $product_module_id;
                        $modules           = $this->modules;
                        $privilege         = "add_privilege";
                        $data['privilege'] = "add_privilege";
                        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);
                        $access_settings          = $section_modules['access_settings'];
                        $primary_id1               = "product_id";
                        $table_name1               = "products";
                        $date_field_name1          = "added_date";
                        $current_date1             = date('Y-m-d');
                        $product_code = $this->generate_invoice_number($access_settings, $primary_id1, $table_name1, $date_field_name1, $current_date1);

                        $product_data = array(
                            "product_code"           => $product_code,
                            "product_name"           => $value->item_name,
                            "product_batch"          => 'BATCH-01',
                            //"product_category_id"    => $value->item_category,
                            "product_subcategory_id" => 0,
                            "product_quantity"       => $value->item_quantity,
                            "product_unit"           => $value->item_uom,
                            "product_unit_id"        => $value->item_uom,
                            "product_hsn_sac_code"   => $value->item_hsn_sac_code,
                            "product_price"          => $value->item_price,
                            "product_gst_id"         => $value->item_tax_id,
                            "product_gst_value"      => $value->item_tax_percentage,
                            "product_discount_id"    => $value->item_discount_id,
                            "product_details"        => $value->item_description,
                            "is_assets"              => 'N',
                            "is_varients"            => 'N',
                            "product_type"           => 'finishedgoods',
                            "added_date"             => date('Y-m-d'),
                            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
                        );
                        $product_id = $this->general_model->insertData('products', $product_data);
                        //$item_data['item_id']  => $product_id;
                        

                    }
                    /*SK Customization*/

                    if ($value != null && $value != '') {
                        if($LeatherCraft_id == $this->session->userdata('SESS_BRANCH_ID')){
                           $item_id =  $this->createBatchProduct($value->item_id,$this->input->post('grn_number'),$this->input->post('supplier'));
                        }else{
                            $item_id = $value->item_id;
                        }
                        
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $purchase_item_unit_price_after_discount = ($value->item_price ? (float) $value->item_price : 0);

                        if ($value->item_taxable_value > 0 && $value->item_quantity > 0)
                            $purchase_item_unit_price_after_discount = ($value->item_taxable_value / $value->item_quantity);

                        $item_data = array(
                            "item_id" => ($value->item_id != 0) ?  $item_id : $product_id,
                            "item_type" => $value->item_type,
                            "purchase_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "purchase_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "purchase_item_unit_price_after_discount" => $purchase_item_unit_price_after_discount,
                            "purchase_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "purchase_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "purchase_item_discount_amount" => $value->item_discount_amount ? (float) $value->item_discount_amount : 0,
                            "purchase_item_discount_id" => $value->item_discount_id ? (float) $value->item_discount_id : 0,
                            "purchase_item_discount_percentage" => $value->item_discount_percentage ? (float) $value->item_discount_percentage : 0,
                            "purchase_item_tds_id" => $value->item_tds_id ? (float) $value->item_tds_id : 0,
                            "purchase_item_tds_percentage" => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0,
                            "purchase_item_tds_amount" => $value->item_tds_amount ? (float) $value->item_tds_amount : 0,
                            "purchase_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "purchase_item_tax_id" => $value->item_tax_id ? (float) $value->item_tax_id : 0,
                            "purchase_item_tax_cess_id" => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0,
                            "purchase_item_igst_percentage" => 0,
                            "purchase_item_igst_amount" => 0,
                            "purchase_item_cgst_percentage" => 0,
                            "purchase_item_cgst_amount" => 0,
                            "purchase_item_sgst_percentage" => 0,
                            "purchase_item_sgst_amount" => 0,
                            "purchase_item_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "purchase_item_tax_cess_percentage" => 0,
                            "purchase_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            'purchase_item_tax_cess_amount' => 0,
                            "purchase_item_description" => $value->item_description ? $value->item_description : "",
                            "debit_note_quantity" => 0,
                            "purchase_id" => $purchase_id);

                        $purchase_item_tax_amount = $item_data['purchase_item_tax_amount'];
                        $purchase_item_tax_percentage = $item_data['purchase_item_tax_percentage'];

                        if ($section_modules['access_settings'][0]->tax_type == "gst") {
                            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0;

                            if ($data['branch'][0]->branch_country_id == $purchase_data['purchase_billing_country_id']) {

                                if ($data['branch'][0]->branch_state_id == $purchase_data['purchase_billing_state_id']) {
                                    $item_data['purchase_item_igst_amount'] = 0;
                                    $item_data['purchase_item_cgst_amount'] = ($purchase_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_item_sgst_amount'] = ($purchase_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['purchase_item_igst_percentage'] = 0;
                                    $item_data['purchase_item_cgst_percentage'] = ($purchase_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_item_sgst_percentage'] = ($purchase_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                } else {
                                    $item_data['purchase_item_igst_amount'] = $purchase_item_tax_amount;
                                    $item_data['purchase_item_cgst_amount'] = 0;
                                    $item_data['purchase_item_sgst_amount'] = 0;
                                    $item_data['purchase_item_tax_cess_amount'] = $item_tax_cess_amount;

                                    $item_data['purchase_item_igst_percentage'] = $purchase_item_tax_percentage;
                                    $item_data['purchase_item_cgst_percentage'] = 0;
                                    $item_data['purchase_item_sgst_percentage'] = 0;
                                    $item_data['purchase_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }
                            /* else
                              {
                              if ($purchase_data['purchase_type_of_supply'] == "export_with_payment")
                              {
                              $item_data['purchase_item_igst_amount'] = $purchase_item_tax_amount;
                              $item_data['purchase_item_cgst_amount'] = 0;
                              $item_data['purchase_item_sgst_amount'] = 0;
                              $item_data['purchase_item_tax_cess_amount'] = $item_tax_cess_amount;

                              $item_data['purchase_item_igst_percentage'] = $purchase_item_tax_percentage;
                              $item_data['purchase_item_cgst_percentage'] = 0;
                              $item_data['purchase_item_sgst_percentage'] = 0;
                              $item_data['purchase_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                              }
                              } */
                        }

                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                        /* if (){ */
                        $this->db->insert($item_table, $data_item);
                        if ($data_item['item_type'] == "product" || $data_item['item_type'] == "product_inventory") {
                            $product_data = $this->common->product_field($data_item['item_id']);
                            $product_result = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);
                            $product_quantity = ((int)$product_result[0]->product_quantity + (int)$value->item_quantity);
                            /* update Product Price */
                            $pro_price = $this->getAVGItemPrice($data_item['item_id']);
                            /* END */
                            $stockData = array(
                                'product_price' => $pro_price,
                                'product_quantity' => $product_quantity);
                            /* print_r($stockData); */
                            $where = array(
                                'product_id' => $item_id);
                            $product_table = $this->config->item('product_table');
                            $this->general_model->updateData($product_table, $stockData, $where);

                            // quantity history
                            $history = array(
                                "item_id" => $item_id,
                                "item_type" => 'product',
                                "reference_id" => $purchase_id,
                                "reference_number" => $invoice_number,
                                "reference_type" => 'purchase',
                                "quantity" => $value->item_quantity,
                                "stock_type" => 'indirect',
                                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                "added_date" => date('Y-m-d'),
                                "entry_date" => date('Y-m-d'),
                                "added_user_id" => $this->session->userdata('SESS_USER_ID'));
                            $this->general_model->insertData("quantity_history", $history);
                        }
                        /* } */

                        if ($this->input->post('section_area') == 'convert_purchase_order') {
                            $purchase_order_id = $this->input->post('purchase_order_id');
                            $purchase_order_table = 'purchase_order';
                            $purchase_order_data = array(
                                'purchase_id' => $purchase_id);
                            $purchase_order_where = array(
                                'purchase_order_id' => $purchase_order_id,
                                'delete_status' => 0);
                            $this->general_model->updateData($purchase_order_table, $purchase_order_data, $purchase_order_where);
                            $successMsg = 'Purchase Order Converted Successfully';
                            $this->session->set_flashdata('purchase_order_success',$successMsg);

                        }
                    }
                }

                //$this->general_model->insertData($item_table , $js_data1);
                /* $this->db->insert_batch($item_table, $js_data1); */
                if (in_array($data['accounts_module_id'], $section_modules['active_add'])) {

                    if (in_array($data['accounts_sub_module_id'], $section_modules['access_sub_modules'])) {
                        $action = "add";
                        $this->purchase_voucher_entry($data_main, $js_data1, $action, $data['branch']);
                    }
                }
            }
        } /* else {
          redirect('purchase' , 'refresh');
          }
          $action = $this->input->post('submit'); */
        redirect('purchase', 'refresh');
        /* if ($action == 'add') {
          }else{
          $purchase_id = $this->encryption_url->encode($purchase_id);
          redirect('receipt_voucher/add_purchase_receipt/' . $purchase_id , 'refresh');
          } */
    }

    public function purchase_vouchers($section_modules, $data_main, $js_data, $branch) {

        $invoice_from = $data_main['from_account'];
        $invoice_to = $data_main['to_account'];
        $ledgers = array();

        $access_sub_modules = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings = $section_modules['access_settings'];
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
        $igst_slab_minus = array();
        $cgst_slab_minus = array();
        $sgst_slab_minus = array();
        $cess_slab_minus = array();

        $igst_slab = array();
        $cgst_slab = array();
        $sgst_slab = array();
        $cess_slab = array();
        $igst_slab_items = array();
        $cgst_slab_items = array();
        $sgst_slab_items = array();
        $cess_slab_items = array();
        $new_ledger_ary = array();
        $igst_charges_array = array();
        $cgst_charges_array = array();
        $sgst_charges_array = array();
        if ((($data_main['purchase_tax_amount'] > 0 && ($data_main['purchase_igst_amount'] > 0 || $data_main['purchase_cgst_amount'] > 0 || $data_main['purchase_sgst_amount'] > 0 || $data_main['purchase_tax_cess_amount'] > 0)) || $data_main['purchase_tax_cess_amount'] > 0)) {
            $present = "gst";

            if ($data_main['purchase_type_of_supply'] != 'import') {
                if ($data_main['purchase_type_of_supply'] == 'intra_state') {
                    $present = "no_igst";
                } else {
                    $present = "igst";
                }
            }

            if ($data_main['purchase_gst_payable'] != "yes" && $present != "gst") {

                foreach ($js_data as $key => $value) {
                    if ($present != "igst") {
                        if ($value['purchase_item_cgst_percentage'] > 0 || $value['purchase_item_sgst_percentage'] > 0) {

                            $cgst_ary = array(
                                            'ledger_name' => 'Input CGST@'.$value['purchase_item_cgst_percentage'].'%',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_x)){
                                $cgst_ledger = $cgst_x->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['purchase_item_cgst_percentage'] , $cgst_ledger);
                                $cgst_ary['ledger_name'] = $cgst_ledger;
                                $cgst_ary['primary_grp'] = $cgst_x->sub_group_1;
                                $cgst_ary['second_grp'] = $cgst_x->sub_group_2;
                                $cgst_ary['main_grp'] = $cgst_x->main_group;
                                $cgst_ary['default_ledger_id'] = $cgst_x->ledger_id;
                            }
                            /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);*/
                            $cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($cgst_ary);

                            /*$cgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Input CGST@' . $value['purchase_item_cgst_percentage'] . '%',
                                'subgrp_1' => 'CGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            $gst_lbl = 'SGST';
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_billing_state_id']);
                            if ($is_utgst == '1')
                                $gst_lbl = 'UTGST';
                            $sgst_ary = array(
                                            'ledger_name' => 'Input '.$gst_lbl.'@'.$value['purchase_item_sgst_percentage'].'%',
                                            'second_grp' => $gst_lbl,
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_sgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($sgst_x)){
                                if($is_utgst == '1') {
                                    $sgst_ledger = $utgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['purchase_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $utgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $utgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $utgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $utgst_x->ledger_id;
                                }else{
                                    $sgst_ledger = $sgst_x->ledger_name;
                                    $sgst_ledger = str_ireplace('{{X}}',$value['purchase_item_sgst_percentage'] , $sgst_ledger);
                                    $sgst_ary['ledger_name'] = $sgst_ledger;
                                    $sgst_ary['primary_grp'] = $sgst_x->sub_group_1;
                                    $sgst_ary['second_grp'] = $sgst_x->sub_group_2;
                                    $sgst_ary['main_grp'] = $sgst_x->main_group;
                                    $sgst_ary['default_ledger_id'] = $sgst_x->ledger_id;
                                }
                                
                            }
                            $sgst_tax_ledger = $this->ledger_model->getGroupLedgerId($sgst_ary);
                            /*$sgst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Input '.$gst_lbl . '@' . $value['purchase_item_sgst_percentage'] . '%',
                                'subgrp_1' => $gst_lbl,
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (in_array($cgst_tax_ledger, $cgst_slab)) {
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger], $value['purchase_item_cgst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['purchase_item_cgst_amount'];
                            }

                            if (in_array($sgst_tax_ledger, $sgst_slab)) {
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger], $value['purchase_item_sgst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $sgst_slab[] = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['purchase_item_sgst_amount'];
                            }
                        }
                    } else {
                        if ($value['purchase_item_igst_percentage'] > 0) {
                            $igst_ary = array(
                                            'ledger_name' => 'Input IGST@'.$value['purchase_item_igst_percentage'].'%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_x)){
                                $igst_ledger = $igst_x->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['purchase_item_igst_percentage'] , $igst_ledger);
                                $igst_ary['ledger_name'] = $igst_ledger;
                                $igst_ary['primary_grp'] = $igst_x->sub_group_1;
                                $igst_ary['second_grp'] = $igst_x->sub_group_2;
                                $igst_ary['main_grp'] = $igst_x->main_group;
                                $igst_ary['default_ledger_id'] = $igst_x->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Input IGST@' . $value['purchase_item_igst_percentage'] . '%',
                                'subgrp_1' => 'IGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (in_array($igst_tax_ledger, $igst_slab)) {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $value['purchase_item_igst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $igst_slab[] = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['purchase_item_igst_amount'];
                            }
                        }
                    }
                    if ($value['purchase_item_tax_cess_percentage'] > 0) {
                        $default_cess_id = $purchase_ledger['CESS@X'];
                        $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                       
                        $cess_ary = array(
                                        'ledger_name' => 'Input Compensation Cess @'.$value['purchase_item_tax_cess_percentage'].'%',
                                        'second_grp' => 'Cess',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Assets',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['purchase_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['purchase_item_tax_cess_percentage'] , $cess_ledger);
                            $cess_ary['ledger_name'] = $cess_ledger;
                            $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                            $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                            $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                            $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                        }
                        $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);

                        /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                            'ledger_name' => 'Input Compensation Cess @' . $value['purchase_item_tax_cess_percentage'] . '%',
                            'subgrp_1' => 'Cess',
                            'subgrp_2' => 'Duties and taxes',
                            'main_grp' => 'Current Liabilities',
                            'amount' => 0
                        ));*/

                        if (in_array($cess_tax_ledger, $cess_slab)) {
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger], $value['purchase_item_tax_cess_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[] = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['purchase_item_tax_cess_amount'];
                        }
                    }
                }
              
            } elseif ($data_main['purchase_gst_payable'] == "yes" && $present != "gst") {

                foreach ($js_data as $key => $value) {
                    if ($present != "igst") {
                        if ($value['purchase_item_cgst_percentage'] > 0 || $value['purchase_item_sgst_percentage'] > 0) {

                            $default_cgst_id = $purchase_ledger['CGST_REV'];
                            $cgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cgst_id);
                           
                            $cgst_ary = array(
                                            'ledger_name' => 'CGST - RCM ITC availed',
                                            'second_grp' => 'CGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_ledger_name)){
                                $cgst_ledger = $cgst_ledger_name->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['purchase_item_cgst_percentage'] , $cgst_ledger);
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
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_billing_state_id']);
                            if ($is_utgst == '1')
                                $gst_lbl = 'UTGST';
                            $default_sgst_id = $purchase_ledger[$gst_lbl.'_REV'];
                            $sgst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_sgst_id);
                           
                            $sgst_ary = array(
                                            'ledger_name' => $gst_lbl . ' - RCM ITC availed',
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
                                'ledger_name' => $gst_lbl . ' - RCM ITC availed',
                                'subgrp_1' => $gst_lbl,
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (in_array($cgst_tax_ledger, $cgst_slab)) {
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger], $value['purchase_item_cgst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['purchase_item_cgst_amount'];
                            }

                            if (in_array($sgst_tax_ledger, $sgst_slab)) {
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger], $value['purchase_item_sgst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $sgst_slab[] = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['purchase_item_sgst_amount'];
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
                                            'default_value' => $value['purchase_item_cgst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cgst_ledger_name)){
                                $cgst_ledger = $cgst_ledger_name->ledger_name;
                                $cgst_ledger = str_ireplace('{{X}}',$value['purchase_item_cgst_percentage'] , $cgst_ledger);
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
                            $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_billing_state_id']);
                            if ($is_utgst == '1')
                                $gst_lbl = 'UTGST';
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
                                'ledger_name' => $gst_lbl . ' - RCM payable',
                                'subgrp_1' => $gst_lbl,
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (!in_array($cgst_tax_ledger, $cgst_slab_minus))
                                array_push($cgst_slab_minus, $cgst_tax_ledger);

                            if (in_array($cgst_tax_ledger, $cgst_slab)) {
                                $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger], $value['purchase_item_cgst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $cgst_slab[] = $cgst_tax_ledger;
                                $cgst_slab_items[$cgst_tax_ledger] = $value['purchase_item_cgst_amount'];
                            }

                            if (!in_array($sgst_tax_ledger, $sgst_slab_minus))
                                array_push($sgst_slab_minus, $sgst_tax_ledger);
                            if (in_array($sgst_tax_ledger, $sgst_slab)) {
                                $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger], $value['purchase_item_sgst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $sgst_slab[] = $sgst_tax_ledger;
                                $sgst_slab_items[$sgst_tax_ledger] = $value['purchase_item_sgst_amount'];
                            }
                        }
                    } else {
                        if ($value['purchase_item_igst_percentage'] > 0) {
                            $default_igst_id = $purchase_ledger['IGST_REV'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($default_igst_id);
                           
                            $igst_ary = array(
                                            'ledger_name' => 'IGST - RCM ITC availed',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['purchase_item_igst_percentage'] , $igst_ledger);
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
                            if (in_array($igst_tax_ledger, $igst_slab)) {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $value['purchase_item_igst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $igst_slab[] = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['purchase_item_igst_amount'];
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
                                            'default_value' => $value['purchase_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $igst_ledger = $igst_ledger_name->ledger_name;
                                $igst_ledger = str_ireplace('{{X}}',$value['purchase_item_igst_percentage'] , $igst_ledger);
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
                            if (!in_array($igst_tax_ledger, $igst_slab_minus))
                                array_push($igst_slab_minus, $igst_tax_ledger);

                            if (in_array($igst_tax_ledger, $igst_slab)) {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $value['purchase_item_igst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $igst_slab[] = $igst_tax_ledger;
                                $igst_slab_items[$igst_tax_ledger] = $value['purchase_item_igst_amount'];
                            }
                        }
                    }
                    if ($value['purchase_item_tax_cess_percentage'] > 0) {
                        $default_cess_id = $purchase_ledger['CESS_REV'];
                        $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($default_cess_id);
                       
                        $cess_ary = array(
                                        'ledger_name' => 'Compensation Cess - RCM ITC availed',
                                        'second_grp' => 'Cess',
                                        'primary_grp' => 'Duties and taxes',
                                        'main_grp' => 'Current Liabilities',
                                        'default_ledger_id' => 0,
                                        'default_value' => $value['purchase_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['purchase_item_tax_cess_percentage'] , $cess_ledger);
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
                        if (in_array($cess_tax_ledger, $cess_slab)) {
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger], $value['purchase_item_tax_cess_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[] = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['purchase_item_tax_cess_amount'];
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
                                        'default_value' => $value['purchase_item_tax_cess_percentage'],
                                        'amount' => 0
                                    );
                        if(!empty($cess_ledger_name)){
                            $cess_ledger = $cess_ledger_name->ledger_name;
                            $cess_ledger = str_ireplace('{{X}}',$value['purchase_item_tax_cess_percentage'] , $cess_ledger);
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

                        if (!in_array($cess_tax_ledger, $cess_slab_minus))
                            array_push($cess_slab_minus, $cess_tax_ledger);

                        if (in_array($cess_tax_ledger, $cess_slab)) {
                            $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger], $value['purchase_item_tax_cess_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $cess_slab[] = $cess_tax_ledger;
                            $cess_slab_items[$cess_tax_ledger] = $value['purchase_item_tax_cess_amount'];
                        }
                    }
                }
                /* Charges modules tax */
            }
        } else if (($data_main['purchase_tax_amount'] > 0 && ($data_main['purchase_igst_amount'] == 0 && $data_main['purchase_cgst_amount'] == 0 && $data_main['purchase_sgst_amount'] == 0)) && $data_main['purchase_gst_payable'] != "yes") {

            $present = "single_tax";
            $tax_slab = array();
            $tax_slab_minus = array();
            $tax_slab_items = array();
            /*foreach ($js_data as $key => $value) {
                if ($value['purchase_item_tax_percentage'] > 0) {

                    $tax_ledger = $this->ledger_model->addGroupLedger(array(
                        'ledger_name' => 'TAX@' . $value['purchase_item_tax_percentage'] . '%',
                        'subgrp_1' => 'TAX',
                        'subgrp_2' => 'Duties and taxes',
                        'main_grp' => 'Current Liabilities',
                        'amount' => 0
                    ));
                    if (in_array($tax_ledger, $tax_slab)) {
                        $tax_slab_items[$tax_ledger] = bcadd($tax_slab_items[$tax_ledger], $value['purchase_item_tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                    } else {
                        $tax_slab[] = $tax_ledger;
                        $tax_slab_items[$tax_ledger] = $value['purchase_item_tax_amount'];
                    }
                }
            }*/
            /* Charges */
            /* Charges modules tax */
            /* if (in_array($charges_sub_module_id , $section_modules['access_sub_modules'])){
              $freight_charge_id = $this->ledger_model->addGroupLedger(array(
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

              $insurance_charge_id = $this->ledger_model->addGroupLedger(array(
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

              $packing_charge_id = $this->ledger_model->addGroupLedger(array(
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

              $incidental_charge_id = $this->ledger_model->addGroupLedger(array(
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

              $inclusion_other_charge_id = $this->ledger_model->addGroupLedger(array(
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

              $exclusion_other_id = $this->ledger_model->addGroupLedger(array(
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
              } */
        }

        if ($data_main['purchase_type_of_supply'] != 'import') {
            if (in_array($charges_sub_module_id, $section_modules['access_sub_modules'])) {
                $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
                $cgst_amount_percentage = $tax_split_percentage;
                $sgst_amount_percentage = 100 - $cgst_amount_percentage;

                $extra_cahrges_ary = array('freight_charge', 'insurance_charge', 'packing_charge', 'incidental_charge', 'inclusion_other_charge', 'exclusion_other_charge');
                $i = 0;
                foreach ($extra_cahrges_ary as $key => $value) {

                    $igst_charges_array[$i]['tax_percentage'] = $data_main[$value . '_tax_percentage'];
                    $cgst_charges_array[$i]['tax_percentage'] = ($data_main[$value . '_tax_percentage'] * $cgst_amount_percentage) / 100;
                    $sgst_charges_array[$i]['tax_percentage'] = ($data_main[$value . '_tax_percentage'] * $sgst_amount_percentage) / 100;

                    $igst_charges_array[$i]['tax_id'] = $data_main[$value . '_tax_id'];
                    $cgst_charges_array[$i]['tax_id'] = $data_main[$value . '_tax_id'];
                    $sgst_charges_array[$i]['tax_id'] = $data_main[$value . '_tax_id'];

                    $igst_charges_array[$i]['tax_amount'] = $data_main[$value . '_tax_amount'];
                    $cgst_charges_array[$i]['tax_amount'] = ($data_main[$value . '_tax_amount'] * $cgst_amount_percentage) / 100;
                    $sgst_charges_array[$i]['tax_amount'] = ($data_main[$value . '_tax_amount'] * $sgst_amount_percentage) / 100;
                    $i++;
                }

                foreach ($igst_charges_array as $key => $value) {
                    if ($data_main['purchase_type_of_supply'] == 'intra_state') {
                        if ($cgst_charges_array[$key]['tax_percentage'] > 0 || $sgst_charges_array[$key]['tax_percentage'] > 0) {
                            if ($data_main['purchase_gst_payable'] != "yes") {
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
                                    'ledger_name' => 'Input CGST@' . $cgst_charges_array[$key]['tax_percentage'] . '%',
                                    'subgrp_1' => 'CGST',
                                    'subgrp_2' => 'Duties and taxes',
                                    'main_grp' => 'Current Liabilities',
                                    'amount' => 0
                                ));*/
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_billing_state_id']);
                                if ($is_utgst == '1')
                                    $gst_lbl = 'UTGST';

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
                                    'ledger_name' => 'Input '.$gst_lbl . '@' . $sgst_charges_array[$key]['tax_percentage'] . '%',
                                    'subgrp_1' => $gst_lbl,
                                    'subgrp_2' => 'Duties and taxes',
                                    'main_grp' => 'Current Liabilities',
                                    'amount' => 0
                                ));*/

                                if (in_array($cgst_tax_ledger, $cgst_slab)) {
                                    if ($key != 5) {
                                        $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger], $cgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $cgst_slab_items[$cgst_tax_ledger] = bcsub($cgst_slab_items[$cgst_tax_ledger], $cgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $cgst_slab[] = $cgst_tax_ledger;
                                    $cgst_slab_items[$cgst_tax_ledger] = $cgst_charges_array[$key]['tax_amount'];
                                    if ($key == 5 && !in_array($cgst_tax_ledger, $cgst_slab_minus)) {
                                        $cgst_slab_minus[] = $cgst_tax_ledger;
                                    }
                                }

                                if (in_array($sgst_tax_ledger, $sgst_slab)) {
                                    if ($key != 5) {
                                        $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger], $sgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $sgst_slab_items[$sgst_tax_ledger] = bcsub($sgst_slab_items[$sgst_tax_ledger], $sgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $sgst_slab[] = $sgst_tax_ledger;
                                    $sgst_slab_items[$sgst_tax_ledger] = $sgst_charges_array[$key]['tax_amount'];
                                    if ($key == 5 && !in_array($sgst_tax_ledger, $sgst_slab_minus)) {
                                        $sgst_slab_minus[] = $sgst_tax_ledger;
                                    }
                                }
                            } else {
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
                                    'ledger_name' => 'Input CGST - RCM ITC availed',
                                    'subgrp_1' => 'CGST',
                                    'subgrp_2' => 'Duties and taxes',
                                    'main_grp' => 'Current Liabilities',
                                    'amount' => 0
                                ));*/
                                $gst_lbl = 'SGST';
                                $is_utgst = $this->general_model->checkIsUtgst($data_main['purchase_billing_state_id']);
                                if ($is_utgst == '1')
                                    $gst_lbl = 'UTGST';

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
                                    'ledger_name' => 'Input '.$gst_lbl . ' - RCM ITC availed',
                                    'subgrp_1' => $gst_lbl,
                                    'subgrp_2' => 'Duties and taxes',
                                    'main_grp' => 'Current Liabilities',
                                    'amount' => 0
                                ));*/

                                if (in_array($cgst_tax_ledger, $cgst_slab)) {
                                    if ($key != 5) {
                                        $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger], $cgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $cgst_slab_items[$cgst_tax_ledger] = bcsub($cgst_slab_items[$cgst_tax_ledger], $cgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $cgst_slab[] = $cgst_tax_ledger;
                                    $cgst_slab_items[$cgst_tax_ledger] = $cgst_charges_array[$key]['tax_amount'];
                                    if ($key == 5 && !in_array($cgst_tax_ledger, $cgst_slab_minus)) {
                                        $cgst_slab_minus[] = $cgst_tax_ledger;
                                    }
                                }

                                if (in_array($sgst_tax_ledger, $sgst_slab)) {
                                    if ($key != 5) {
                                        $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger], $sgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $sgst_slab_items[$sgst_tax_ledger] = bcsub($sgst_slab_items[$sgst_tax_ledger], $sgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $sgst_slab[] = $sgst_tax_ledger;
                                    $sgst_slab_items[$sgst_tax_ledger] = $sgst_charges_array[$key]['tax_amount'];
                                    if ($key == 5 && !in_array($sgst_tax_ledger, $sgst_slab_minus)) {
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
                                    'ledger_name' => $gst_lbl . ' - RCM payable',
                                    'subgrp_1' => $gst_lbl,
                                    'subgrp_2' => 'Duties and taxes',
                                    'main_grp' => 'Current Liabilities',
                                    'amount' => 0
                                ));*/

                                if (in_array($cgst_tax_ledger, $cgst_slab)) {
                                    if ($key != 5) {
                                        $cgst_slab_items[$cgst_tax_ledger] = bcadd($cgst_slab_items[$cgst_tax_ledger], $cgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $cgst_slab_items[$cgst_tax_ledger] = bcsub($cgst_slab_items[$cgst_tax_ledger], $cgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $cgst_slab[] = $cgst_tax_ledger;
                                    $cgst_slab_items[$cgst_tax_ledger] = $cgst_charges_array[$key]['tax_amount'];
                                    if ($key != 5 && !in_array($cgst_tax_ledger, $cgst_slab_minus)) {
                                        $cgst_slab_minus[] = $cgst_tax_ledger;
                                    }
                                }

                                if (in_array($sgst_tax_ledger, $sgst_slab)) {
                                    if ($key != 5) {
                                        $sgst_slab_items[$sgst_tax_ledger] = bcadd($sgst_slab_items[$sgst_tax_ledger], $sgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $sgst_slab_items[$sgst_tax_ledger] = bcsub($sgst_slab_items[$sgst_tax_ledger], $sgst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $sgst_slab[] = $sgst_tax_ledger;
                                    $sgst_slab_items[$sgst_tax_ledger] = $sgst_charges_array[$key]['tax_amount'];
                                    if ($key != 5 && !in_array($sgst_tax_ledger, $sgst_slab_minus)) {
                                        $sgst_slab_minus[] = $sgst_tax_ledger;
                                    }
                                }
                            }
                        }
                    } else {
                        if ($igst_charges_array[$key]['tax_percentage'] > 0) {
                            if ($data_main['purchase_gst_payable'] != "yes") {
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
                                    'ledger_name' => 'Input IGST@' . $igst_charges_array[$key]['tax_percentage'] . '%',
                                    'subgrp_1' => 'IGST',
                                    'subgrp_2' => 'Duties and taxes',
                                    'main_grp' => 'Current Liabilities',
                                    'amount' => 0
                                ));*/

                                if (in_array($igst_tax_ledger, $igst_slab)) {
                                    if ($key != 5) {
                                        $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $igst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $igst_slab_items[$igst_tax_ledger] = bcsub($igst_slab_items[$igst_tax_ledger], $igst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $igst_slab[] = $igst_tax_ledger;
                                    $igst_slab_items[$igst_tax_ledger] = $igst_charges_array[$key]['tax_amount'];

                                    if ($key == 5 && !in_array($igst_tax_ledger, $igst_slab_minus)) {
                                        $igst_slab_minus[] = $igst_tax_ledger;
                                    }
                                }
                            } else {
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

                                if (in_array($igst_tax_ledger, $igst_slab)) {
                                    if ($key != 5) {
                                        $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $igst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $igst_slab_items[$igst_tax_ledger] = bcsub($igst_slab_items[$igst_tax_ledger], $igst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $igst_slab[] = $igst_tax_ledger;
                                    $igst_slab_items[$igst_tax_ledger] = $igst_charges_array[$key]['tax_amount'];

                                    if ($key == 5 && !in_array($igst_tax_ledger, $igst_slab_minus)) {
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

                                if (in_array($igst_tax_ledger, $igst_slab)) {
                                    if ($key != 5) {
                                        $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $igst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    } else {
                                        $igst_slab_items[$igst_tax_ledger] = bcsub($igst_slab_items[$igst_tax_ledger], $igst_charges_array[$key]['tax_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                                    }
                                } else {
                                    $igst_slab[] = $igst_tax_ledger;
                                    $igst_slab_items[$igst_tax_ledger] = $igst_charges_array[$key]['tax_amount'];

                                    if ($key != 5 && !in_array($igst_tax_ledger, $igst_slab_minus)) {
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
        if ($data_main['purchase_type_of_supply'] != "import") {
            if ($data_main['purchase_tds_amount'] > 0 || $data_main['purchase_tcs_amount'] > 0) {
                $tds_slab = array();
                $tds_slab_minus = array();
                $tds_slab_items = array();
                $data_main['total_tds_amount'] = 0;
                $data_main['total_tcs_amount'] = 0;
                foreach ($js_data as $key => $value) {

                    if ($value['purchase_item_tds_percentage'] > 0) {

                        $string = 'tds.section_name,td.tax_name';
                        $table = 'tax td';
                        $where = array('td.delete_status' => 0, 'td.tax_id' => $value['purchase_item_tds_id']);
                        $join = array('tax_section tds' => 'td.section_id = tds.section_id');
                        $tds_data = $this->general_model->getJoinRecords($string, $table, $where, $join);
                        if (!empty($tds_data)) {
                            $section_name = $tds_data[0]->section_name;
                            $module_type = strtoupper($tds_data[0]->tax_name);
                        } else {
                            $module_type = 'TCS';
                            $section_name = '193';
                        }

                        if (strtoupper($module_type) == "TCS") {
                            $payment_type = "Payable";
                            $tds_subgroup = "TCS Receivable u/s ";
                            $default_tds_id = $purchase_ledger['TCS_REV'];
                            $tds_ledger_name = $this->ledger_model->getDefaultLedgerId($default_tds_id);
                                
                            $tds_ary = array(
                                            'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['purchase_item_tds_percentage'].'%',
                                            'second_grp' => '',
                                            'primary_grp' => '',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_tds_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($tds_ledger_name)){
                                $tds_ledger = $tds_ledger_name->ledger_name;
                                $tds_ledger = str_ireplace('{{SECTION}}',$section_name, $tds_ledger);
                                $tds_ledger = str_ireplace('{{X}}',$value['purchase_item_tds_percentage'] , $tds_ledger);
                                $tds_ary['ledger_name'] = $tds_ledger;
                                $tds_ary['primary_grp'] = $tds_ledger_name->sub_group_1;
                                $tds_ary['second_grp'] = $tds_ledger_name->sub_group_2;
                                $tds_ary['main_grp'] = $tds_ledger_name->main_group;
                                $tds_ary['default_ledger_id'] = $tds_ledger_name->ledger_id;
                            }
                            $tds_ledger = $this->ledger_model->getGroupLedgerId($tds_ary);
                            /*$tds_ledger = $this->ledger_model->addGroupLedger(array(
                                    'ledger_name' => $tds_subgroup . $section_name . '@' . $value['purchase_item_tds_percentage'] . '%',
                                    'subgrp_2' => '',
                                    'subgrp_1' => '',
                                    'main_grp' => 'Current Assets',
                                    'amount' => 0
                                ));*/
                        } else {
                            $payment_type = "Receivable";
                            $tds_subgroup = "TDS payable u/s ";

                            $default_tds_id = $purchase_ledger['TDS_PAY'];
                            $tds_ledger_name = $this->ledger_model->getDefaultLedgerId($default_tds_id);
                                
                            $tds_ary = array(
                                            'ledger_name' => $tds_subgroup.' '.$section_name.'@'.$value['purchase_item_tds_percentage'].'%',
                                            'second_grp' => '',
                                            'primary_grp' => '',
                                            'main_grp' => 'Current Assets',
                                            'default_ledger_id' => 0,
                                            'default_value' => $value['purchase_item_tds_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($tds_ledger_name)){
                                $tds_ledger = $tds_ledger_name->ledger_name;
                                $tds_ledger = str_ireplace('{{SECTION}}',$section_name, $tds_ledger);
                                $tds_ledger = str_ireplace('{{X}}',$value['purchase_item_tds_percentage'] , $tds_ledger);
                                $tds_ary['ledger_name'] = $tds_ledger;
                                $tds_ary['primary_grp'] = $tds_ledger_name->sub_group_1;
                                $tds_ary['second_grp'] = $tds_ledger_name->sub_group_2;
                                $tds_ary['main_grp'] = $tds_ledger_name->main_group;
                                $tds_ary['default_ledger_id'] = $tds_ledger_name->ledger_id;
                            }
                            $tds_ledger = $this->ledger_model->getGroupLedgerId($tds_ary);

                            /*$tds_ledger = $this->ledger_model->addGroupLedger(array(
                                    'ledger_name' => $tds_subgroup . $section_name . '@' . $value['purchase_item_tds_percentage'] . '%',
                                    'subgrp_2' => 'Duties and taxes',
                                    'subgrp_1' => '',
                                    'main_grp' => 'Current Liabilities',
                                    'amount' => 0
                                ));*/
                        }



                        $tds_title = $module_type . " " . $payment_type . " under u/s " . $section_name;
                        $tds_subgroup = $tds_subgroup;
                        

                        if (in_array($tds_ledger, $tds_slab)) {
                            $tds_slab_items[$tds_ledger] = bcadd($tds_slab_items[$tds_ledger], $value['purchase_item_tds_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                        } else {
                            $tds_slab[] = $tds_ledger;
                            $tds_slab_items[$tds_ledger] = $value['purchase_item_tds_amount'];
                        }

                        if (strtoupper($module_type) == "TCS") {
                            if (!in_array($tds_ledger, $tds_slab_minus)) {
                                $tds_slab_minus[] = $tds_ledger;
                            }
                        }
                    }
                }
            }
        }
        /* tds ends */

        /*$purchase_ledger_id = $this->ledger_model->getDefaultLedger('Purchases');*/
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
        $purchase_ledger_id = $this->ledger_model->getGroupLedgerId($purchase_ary);
        
        /*if ($purchase_ledger_id == 0) {
            $purchase_ledger_id = $this->ledger_model->addGroupLedger(array(
                'ledger_name' => 'Purchases',
                'subgrp_2' => '',
                'subgrp_1' => '',
                'main_grp' => 'Purchases group',
                'amount' => 0
            ));
        }*/
        $ledgers['purchase_ledger_id'] = $purchase_ledger_id;

        $string = 'ledger_id,supplier_name';
        $table = 'supplier';
        $where = array('supplier_id' => $data_main['purchase_party_id']);
        $supplier_data = $this->general_model->getRecords($string, $table, $where, $order = "");
        $supplier_name = $supplier_data[0]->supplier_name;
        $supplier_ledger_id = $supplier_data[0]->ledger_id;

        if(!$supplier_ledger_id){
            $supplier_ledger_id = $purchase_ledger['SUPPLIER'];
            $supplier_ledger_name = $this->ledger_model->getDefaultLedgerId($supplier_ledger_id);
                
            $supplier_ary = array(
                            'ledger_name' => $supplier_name,
                            'second_grp' => '',
                            'primary_grp' => 'Sundry Creditors',
                            'main_grp' => 'Current Assets',
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
            'amount' => 0
        ));*/

        $ledgers['supplier_ledger_id'] = $supplier_ledger_id;
        $ledger_from = $supplier_ledger_id;

        $ledgers['ledger_from'] = $ledger_from;
        $ledgers['ledger_to'] = $purchase_ledger_id;

        $vouchers = array();
        $vouchers_new = array();
        $charges_sub_module_id = $this->config->item('charges_sub_module');

        if ($data_main['purchase_gst_payable'] != "yes") {
            $grand_total = $data_main['purchase_grand_total'];
        } else {
            $total_tax_amount = ($data_main['purchase_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount'] + $data_main['purchase_tax_cess_amount']);
            $grand_total = bcsub($data_main['purchase_grand_total'], $total_tax_amount, $section_modules['access_common_settings'][0]->amount_precision);
        }

        if (isset($data_main['purchase_tds_amount']) && $data_main['purchase_tds_amount'] > 0) {
            $grand_total = bcsub($grand_total, $data_main['purchase_tds_amount'], $section_modules['access_common_settings'][0]->amount_precision);
        }

        $this->db->set(array('supplier_payable_amount' => $grand_total));
        $this->db->where('purchase_id', $data_main['purchase_id']);
        $this->db->update('purchase');

        $vouchers_new[] = array(
            "ledger_from" => $supplier_ledger_id,
            "ledger_to" => $ledgers['ledger_to'],
            "purchase_voucher_id" => '',
            "voucher_amount" => $grand_total,
            "converted_voucher_amount" => 0,
            "dr_amount" => '',
            "cr_amount" => $grand_total,
            'ledger_id' => $supplier_ledger_id
        );

        $sub_total = $data_main['purchase_sub_total'];

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
            $converted_voucher_amount = $sub_total;
        } else {
            $converted_voucher_amount = 0;
        }

        $vouchers_new[] = array(
            "ledger_from" => $ledgers['ledger_from'],
            "ledger_to" => $purchase_ledger_id,
            "purchase_voucher_id" => '',
            "voucher_amount" => $sub_total,
            "converted_voucher_amount" => $converted_voucher_amount,
            "dr_amount" => $sub_total,
            "cr_amount" => '',
            'ledger_id' => $purchase_ledger_id
        );

        if ($data_main['purchase_tds_amount'] > 0 || $data_main['purchase_tcs_amount'] > 0) {
            foreach ($tds_slab_items as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $value;
                } else {
                    $converted_voucher_amount = 0;
                }

                /* if ($data_main['purchase_tcs_amount'] > 0) {
                  $dr_amount = '';
                  $cr_amount = $value;
                  $ledger_to = $ledgers['ledger_from'];
                  } else {
                  $dr_amount = $value;
                  $cr_amount = '';
                  $ledger_to = $ledgers['ledger_to'];
                  } */

                if (in_array($key, $tds_slab_minus)) {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                } else {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                }

                $vouchers_new[] = array(
                    "ledger_from" => $key,
                    "ledger_to" => $ledger_to,
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $value,
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $dr_amount,
                    "cr_amount" => $cr_amount,
                    'ledger_id' => $key
                );
            }
        }

        if (!empty($cgst_slab_items) || !empty($sgst_slab_items)) {
            foreach ($cgst_slab_items as $key => $value) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $value;
                } else {
                    $converted_voucher_amount = 0;
                }

                if (in_array($key, $cgst_slab_minus)) {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }

                if ($value > 0) {
                    $vouchers_new[] = array(
                        "ledger_from" => $key,
                        "ledger_to" => $ledger_to,
                        "purchase_voucher_id" => '',
                        "voucher_amount" => $value,
                        "converted_voucher_amount" => $converted_voucher_amount,
                        "dr_amount" => $dr_amount,
                        "cr_amount" => $cr_amount,
                        'ledger_id' => $key
                    );
                }
            }

            foreach ($sgst_slab_items as $key => $value) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $value;
                } else {
                    $converted_voucher_amount = 0;
                }

                if (in_array($key, $sgst_slab_minus)) {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }

                $vouchers_new[] = array(
                    "ledger_from" => $key,
                    "ledger_to" => $ledger_to,
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $value,
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $dr_amount,
                    "cr_amount" => $cr_amount,
                    'ledger_id' => $key
                );
            }
        } elseif (!empty($igst_slab_items)) {
            foreach ($igst_slab_items as $key => $value) {

                $converted_voucher_amount = 0;
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $value;
                }

                if (in_array($key, $igst_slab_minus)) {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }
                /* $dr_amount = '';
                  $cr_amount = $value;
                  $ledger_to = $ledgers['ledger_to']; */

                $vouchers_new[] = array(
                    "ledger_from" => $key,
                    "ledger_to" => $ledgers['ledger_to'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $value,
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $dr_amount,
                    "cr_amount" => $cr_amount,
                    'ledger_id' => $key
                );
            }
        }

        if ((($data_main['purchase_tax_amount'] > 0 && ($data_main['purchase_igst_amount'] > 0 || $data_main['purchase_cgst_amount'] > 0 || $data_main['purchase_sgst_amount'] > 0)) || $data_main['purchase_tax_cess_amount'] > 0)) {
            /* if ($present != "igst"){

              }else {


              } */
            if ($data_main['purchase_tax_cess_amount'] > 0) {
                foreach ($cess_slab_items as $key => $value) {

                    $converted_voucher_amount = 0;
                    if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                        $converted_voucher_amount = $value;
                    }

                    if (in_array($key, $cess_slab_minus)) {
                        $dr_amount = '';
                        $cr_amount = $value;
                        $ledger_to = $ledgers['ledger_from'];
                    } else {
                        $dr_amount = $value;
                        $cr_amount = '';
                        $ledger_to = $ledgers['ledger_to'];
                    }

                    $vouchers_new[] = array(
                        "ledger_from" => $key,
                        "ledger_to" => $ledgers['ledger_to'],
                        "purchase_voucher_id" => '',
                        "voucher_amount" => $value,
                        "converted_voucher_amount" => $converted_voucher_amount,
                        "dr_amount" => $dr_amount,
                        "cr_amount" => $cr_amount,
                        'ledger_id' => $key
                    );
                }
            }
        } else if (($data_main['purchase_tax_amount'] > 0 && ($data_main['purchase_igst_amount'] == 0 && $data_main['purchase_cgst_amount'] == 0 && $data_main['purchase_sgst_amount'] == 0)) && $data_main['purchase_gst_payable'] != "yes") {
            foreach ($tax_slab_items as $key => $value) {

                $converted_voucher_amount = 0;
                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $value;
                }

                if (in_array($key, $tax_slab_minus)) {
                    $dr_amount = '';
                    $cr_amount = $value;
                    $ledger_to = $ledgers['ledger_from'];
                } else {
                    $dr_amount = $value;
                    $cr_amount = '';
                    $ledger_to = $ledgers['ledger_to'];
                }

                $vouchers_new[] = array(
                    "ledger_from" => $key,
                    "ledger_to" => $ledgers['ledger_to'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $value,
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $dr_amount,
                    "cr_amount" => $cr_amount,
                    'ledger_id' => $key
                );
            }
        }

        if (in_array($charges_sub_module_id, $section_modules['access_sub_modules'])) {
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
                'ledger_name' => 'Freight PAID',
                'subgrp_2' => '',
                'subgrp_1' => '',
                'main_grp' => 'Direct Expenses',
                'amount' => 0
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
                'amount' => 0
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
                'amount' => 0
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
                'amount' => 0
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
                'amount' => 0
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
                'main_grp' => 'Direct Income',
                'amount' => 0
            ));*/

            if (isset($freight_charge_ledger_id) && $data_main['freight_charge_amount'] > 0) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $data_main['freight_charge_amount'];
                } else {
                    $converted_voucher_amount = 0;
                }

                $vouchers_new[] = array(
                    "ledger_from" => $freight_charge_ledger_id,
                    "ledger_to" => $ledgers['ledger_to'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $data_main['freight_charge_amount'],
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $data_main['freight_charge_amount'],
                    "cr_amount" => '',
                    'ledger_id' => $freight_charge_ledger_id
                );
            }
            if (isset($insurance_charge_ledger_id) && $data_main['insurance_charge_amount'] > 0) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $data_main['insurance_charge_amount'];
                } else {
                    $converted_voucher_amount = 0;
                }

                $vouchers_new[] = array(
                    "ledger_from" => $insurance_charge_ledger_id,
                    "ledger_to" => $ledgers['ledger_to'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $data_main['insurance_charge_amount'],
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $data_main['insurance_charge_amount'],
                    "cr_amount" => '',
                    'ledger_id' => $insurance_charge_ledger_id
                );
            }
            if (isset($packing_charge_ledger_id) && $data_main['packing_charge_amount'] > 0) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $data_main['packing_charge_amount'];
                } else {
                    $converted_voucher_amount = 0;
                }
                $vouchers_new[] = array(
                    "ledger_from" => $packing_charge_ledger_id,
                    "ledger_to" => $ledgers['ledger_to'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $data_main['packing_charge_amount'],
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $data_main['packing_charge_amount'],
                    "cr_amount" => '',
                    'ledger_id' => $packing_charge_ledger_id
                );
            } if (isset($incidental_charge_ledger_id) && $data_main['incidental_charge_amount'] > 0) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $data_main['incidental_charge_amount'];
                } else {
                    $converted_voucher_amount = 0;
                }

                $vouchers_new[] = array(
                    "ledger_from" => $incidental_charge_ledger_id,
                    "ledger_to" => $ledgers['ledger_to'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $data_main['incidental_charge_amount'],
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $data_main['incidental_charge_amount'],
                    "cr_amount" => '',
                    'ledger_id' => $incidental_charge_ledger_id
                );
            } if (isset($other_inclusive_charge_ledger_id) && $data_main['inclusion_other_charge_amount'] > 0) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $data_main['inclusion_other_charge_amount'];
                } else {
                    $converted_voucher_amount = 0;
                }

                $vouchers_new[] = array(
                    "ledger_from" => $other_inclusive_charge_ledger_id,
                    "ledger_to" => $ledgers['ledger_to'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $data_main['inclusion_other_charge_amount'],
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => $data_main['inclusion_other_charge_amount'],
                    "cr_amount" => '',
                    'ledger_id' => $other_inclusive_charge_ledger_id
                );
            } if (isset($other_exclusive_charge_ledger_id) && $data_main['exclusion_other_charge_amount'] > 0) {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                    $converted_voucher_amount = $data_main['exclusion_other_charge_amount'];
                } else {
                    $converted_voucher_amount = 0;
                }

                $vouchers_new[] = array(
                    "ledger_from" => $other_exclusive_charge_ledger_id,
                    "ledger_to" => $ledgers['ledger_from'],
                    "purchase_voucher_id" => '',
                    "voucher_amount" => $data_main['exclusion_other_charge_amount'],
                    "converted_voucher_amount" => $converted_voucher_amount,
                    "dr_amount" => '',
                    "cr_amount" => $data_main['exclusion_other_charge_amount'],
                    'ledger_id' => $other_exclusive_charge_ledger_id
                );
            }
        }

        /* discount slab */
        $discount_sum = 0;
        if ($data_main['purchase_discount_amount'] > 0) {
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

            /* $discount_ledger_id            = $this->ledger_model->getDefaultLedger('Discount'); */
            /*$discount_ledger_id = $this->ledger_model->addGroupLedger(array(
                'ledger_name' => 'Trade Discount Received',
                'subgrp_1' => '',
                'subgrp_2' => '',
                'main_grp' => 'Direct Income',
                'amount' => 0
            ));*/
            $ledgers['discount_ledger_id'] = $discount_ledger_id;

            foreach ($js_data as $key => $value) {
                $discount_sum = bcadd($discount_sum, $value['purchase_item_discount_amount'], $section_modules['access_common_settings'][0]->amount_precision);
            }

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $converted_voucher_amount = $discount_sum;
            } else {
                $converted_voucher_amount = 0;
            }

            $vouchers_new[] = array(
                "ledger_from" => $discount_ledger_id,
                "ledger_to" => $purchase_ledger_id,
                "purchase_voucher_id" => '',
                "voucher_amount" => $discount_sum,
                "converted_voucher_amount" => $converted_voucher_amount,
                "dr_amount" => '',
                "cr_amount" => $discount_sum,
                'ledger_id' => $discount_ledger_id
            );
        }
        /* discount slab ends */

        /* Round off */

        if ($data_main['round_off_amount'] > 0 || $data_main['round_off_amount'] < 0) {

            $round_off_amount = $data_main['round_off_amount'];

            if ($round_off_amount > 0) {
                $round_off_amount = $round_off_amount;
                $dr_amount = '';
                $cr_amount = $round_off_amount;

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
                    'main_grp' => 'Indirect Incomes',
                    'amount' => 0
                );*/
            } else {
                $round_off_amount = ($round_off_amount * -1);
                $dr_amount = $round_off_amount;
                $cr_amount = '';
                $ledger_to = $ledgers['ledger_to'];
                $default_roundoff_id = $purchase_ledger['RoundOff_Given'];
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
                    'ledger_name' => 'ROUND OFF',
                    'subgrp_1' => '',
                    'subgrp_2' => '',
                    'main_grp' => 'Indirect Expenses',
                    'amount' => 0
                );*/
            }

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
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
            /*$round_off_ledger_id = $this->ledger_model->addGroupLedger($round_off_ary);*/

            $ledgers['round_off_ledger_id'] = $round_off_ledger_id;

            $vouchers_new[] = array(
                "ledger_from" => $round_off_ledger_id,
                "ledger_to" => $ledger_to,
                "purchase_voucher_id" => '',
                "voucher_amount" => $round_off_amount,
                "converted_voucher_amount" => $converted_voucher_amount,
                "dr_amount" => $dr_amount,
                "cr_amount" => $cr_amount,
                'ledger_id' => $round_off_ledger_id
            );
        }

        $vouchers = array();
        $voucher_keys = array();
        if (!empty($vouchers_new)) {
            foreach ($vouchers_new as $key => $value) {
                $k = 'ledger_' . $value['ledger_id'];
                if (!array_key_exists($k, $vouchers)) {
                    $vouchers[$k] = $value;
                } else {
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

    public function purchase_voucher_entry($data_main, $js_data, $action, $branch) {
        $purchase_voucher_module_id = $this->config->item('purchase_module');
        $module_id = $purchase_voucher_module_id;
        $modules = $this->get_modules();
        $privilege = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_voucher_module_id, $modules, $privilege);

        $access_sub_modules = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings = $section_modules['access_settings'];

        /* generated voucher number */

        $vouchers = $this->purchase_vouchers($section_modules, $data_main, $js_data, $branch);
        $grand_total = $data_main['purchase_grand_total'];
        /* if ($data_main['purchase_gst_payable'] != "yes"){
          } else {
          $total_tax_amount = ($data_main['purchase_tax_amount'] + $data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount']);
          $grand_total      = bcsub($data_main['purchase_grand_total'] , $total_tax_amount,$section_modules['access_common_settings'][0]->amount_precision);
          } */

        $table = 'purchase_voucher';
        $reference_key = 'purchase_voucher_id';
        $reference_table = 'accounts_purchase_voucher';

        if ($action == "add") {
            /* generated voucher number */
            $primary_id = "purchase_voucher_id";
            $table_name = $this->config->item('purchase_voucher_table');
            $date_field_name = "voucher_date";
            $current_date = $data_main['purchase_date'];
            $voucher_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

            $headers = array(
                "voucher_date" => $data_main['purchase_date'],
                "voucher_number" => $voucher_number,
                "party_id" => $data_main['purchase_party_id'],
                "party_type" => $data_main['purchase_party_type'],
                "reference_id" => $data_main['purchase_id'],
                "reference_type" => 'purchase',
                "reference_number" => $data_main['purchase_invoice_number'],
                "receipt_amount" => $grand_total,
                "from_account" => $data_main['from_account'],
                "to_account" => $data_main['to_account'],
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "description" => '',
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "currency_id" => $data_main['currency_id'],
                "note1" => $data_main['note1'],
                "note2" => $data_main['note2']
            );

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $headers['converted_receipt_amount'] = $grand_total;
            } else {
                $headers['converted_receipt_amount'] = 0;
            }
            $this->general_model->addVouchers($table, $reference_key, $reference_table, $headers, $vouchers);
        } else if ($action == "edit") {
            $headers = array(
                "voucher_date" => $data_main['purchase_date'],
                "party_id" => $data_main['purchase_party_id'],
                "party_type" => $data_main['purchase_party_type'],
                "reference_id" => $data_main['purchase_id'],
                "reference_type" => 'purchase',
                "reference_number" => $data_main['purchase_invoice_number'],
                "receipt_amount" => $grand_total,
                "from_account" => $data_main['from_account'],
                "to_account" => $data_main['to_account'],
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "description" => '',
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "currency_id" => $data_main['currency_id'],
                "updated_date" => date('Y-m-d'),
                "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                "note1" => $data_main['note1'],
                "note2" => $data_main['note2']
            );

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $data_main['currency_id']) {
                $headers['converted_receipt_amount'] = $grand_total;
            } else {
                $headers['converted_receipt_amount'] = 0;
            }

            $purchase_voucher_data = $this->general_model->getRecords('purchase_voucher_id', 'purchase_voucher', array(
                'reference_id' => $data_main['purchase_id'], 'reference_type' => 'purchase', 'delete_status' => 0));

            if ($purchase_voucher_data) {
                $purchase_voucher_id = $purchase_voucher_data[0]->purchase_voucher_id;
                $this->general_model->updateData('purchase_voucher', $headers, array('purchase_voucher_id' => $purchase_voucher_id));
                $string = 'accounts_purchase_id,delete_status,ledger_id,voucher_amount,dr_amount,cr_amount';
                $table = 'accounts_purchase_voucher';
                $where = array('purchase_voucher_id' => $purchase_voucher_id);

                $old_purchase_voucher_items = $this->general_model->getRecords($string, $table, $where, $order = "");
                $old_purchase_ledger_ids = $this->getValues($old_purchase_voucher_items, 'ledger_id');
                $not_deleted_ids = array();

                foreach ($vouchers as $key => $value) {
                    if (($led_key = array_search($value['ledger_id'], $old_purchase_ledger_ids)) !== false) {
                        unset($old_purchase_ledger_ids[$led_key]);
                        $accounts_purchase_id = $old_purchase_voucher_items[$led_key]->accounts_purchase_id;
                        array_push($not_deleted_ids, $accounts_purchase_id);
                        $value['purchase_voucher_id'] = $purchase_voucher_id;
                        $value['delete_status'] = 0;
                        $table = 'accounts_purchase_voucher';
                        $where = array('accounts_purchase_id' => $accounts_purchase_id);
                        if($value['dr_amount'] < 0){
                            $value['cr_amount'] = abs($value['dr_amount']);
                            $value['dr_amount'] = 0;
                        }
                        $post_data = array('data' => $value,
                            'where' => $where,
                            'voucher_date' => $headers['voucher_date'],
                            'table' => 'purchase_voucher',
                            'sub_table' => 'accounts_purchase_voucher',
                            'primary_id' => 'purchase_voucher_id',
                            'sub_primary_id' => 'purchase_voucher_id'
                        );
                        $this->general_model->updateBunchVoucherCommon($post_data);
                        $this->general_model->updateData($table, $value, $where);
                    } else {
                        $value['purchase_voucher_id'] = $purchase_voucher_id;
                        $table = 'accounts_purchase_voucher';
                        $this->general_model->insertData($table, $value);
                    }
                }

                if (!empty($old_purchase_voucher_items)) {
                    $revert_ary = array();
                    foreach ($old_purchase_voucher_items as $key => $value) {
                        if (!in_array($value->accounts_purchase_id, $not_deleted_ids)) {
                            $revert_ary[] = $value;
                            $table = 'accounts_purchase_voucher';
                            $where = array('accounts_purchase_id' => $value->accounts_purchase_id);
                            $purchase_data = array('delete_status' => 1);
                            $this->general_model->updateData($table, $purchase_data, $where);
                        }
                    }
                    if (!empty($revert_ary))
                        $this->general_model->revertLedgerAmount($revert_ary, $headers['voucher_date']);
                }
            }
        }
    }

    public function get_purchase_suggestions($term, $inventory_advanced, $item_access) {
        /*$purchase_module_id = $this->config->item('purchase_module');
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);*/
        if($term == '-') $term ='';
        $LeatherCraft_id = $this->config->item('LeatherCraft');
        if($LeatherCraft_id == $this->session->userdata('SESS_BRANCH_ID')){
            $suggestions_query = $this->common->item_suggestions_field_leathercrafr($item_access, $term);
        }else{
            $suggestions_query = $this->common->item_suggestions_field($item_access, $term);
        }

        $data = $this->general_model->getQueryRecords($suggestions_query);
        echo json_encode($data);
    }

    public function get_table_items($code) {
        /* 0-id, 1-type, 2-discount, 3-tax , */

        $purchase_module_id = $this->config->item('purchase_module');
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        /*$section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);*/

        $item_code = explode("-", $code);

        if ($item_code[1] == "service") {
            $service_data = $this->common->service_field($item_code[0]);

            $data = $this->general_model->getJoinRecords($service_data['string'], $service_data['table'], $service_data['where'], $service_data['join']);
        } else {
            $product_data = $this->common->product_field($item_code[0]);
            $data = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);
        }

        $discount_data = array();
        $tax_data = array();
        $tds_data = array();

        if ($item_code[2] == 'yes') {
            $discount_data = $this->discount_call();
        }

        if ($item_code[3] == 'gst' || $item_code[3] == 'single_tax') {
            $tax_data = $this->tax_call();
        }

        $data['discount'] = $discount_data;
        $data['tax'] = $tax_data;
        $branch_details = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id'] = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id'] = $branch_details['branch'][0]->branch_id;
        $data['item_id'] = $item_code[0];
        $data['item_type'] = $item_code[1];
        echo json_encode($data);
    }

    public function view($id) {
        $id = $this->encryption_url->decode($id);
        $data = array();
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $purchase_module_id = $this->config->item('purchase_module');
        $data['payment_voucher_module_id'] = $this->config->item('payment_voucher_module');
        $data['email_module_id'] = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id'] = $purchase_module_id;
        $data['purchase_module_id'] = $purchase_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $purchase_data = $this->common->purchase_list_field1($id);
        
        $data['data'] = $this->general_model->getJoinRecords($purchase_data['string'], $purchase_data['table'], $purchase_data['where'], $purchase_data['join']);

        $item_types = $this->general_model->getRecords('item_type,purchase_item_description', 'purchase_item', array('purchase_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->purchase_item_description != "") {
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

        $purchase_service_items = array();
        $purchase_product_items = array();

        if (($data['data'][0]->purchase_nature_of_supply == "service" || $data['data'][0]->purchase_nature_of_supply == "both") && $service == 1) {
            $service_items = $this->common->purchase_items_service_list_field($id);
            $purchase_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_nature_of_supply == "product" || $data['data'][0]->purchase_nature_of_supply == "both") {

            /* if ($product == 2)
              {
              $product_items          = $this->common->purchase_items_product_inventory_list_field($id);
              $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              if ($product == 1)
              {
              } */
            $product_items = $this->common->purchase_items_product_list_field($id);
            $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_product_items, $purchase_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;
        $cess_exist = 0;

        if ($data['data'][0]->purchase_tax_amount > 0 && $data['data'][0]->purchase_igst_amount > 0 && ($data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {

            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_cgst_amount > 0 || $data['data'][0]->purchase_sgst_amount > 0) && $data['data'][0]->purchase_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount == 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_tds_amount > 0 || $data['data'][0]->purchase_tcs_amount > 0) {
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->purchase_discount_amount > 0) {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0) {
            /* Discount */
            $descriptionExist = 1;
        }

        if ($data['data'][0]->purchase_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_billing_state_id);

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
        $this->load->view('purchase/view', $data);
    }

    public function edit($id) {       
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_module_id = $this->config->item('purchase_module');
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
        $data['uqc_module_id']        = $this->config->item('uqc_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['shipping_sub_module_id'] = $this->config->item('shipping_sub_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['data'] = $this->general_model->getRecords('*', 'purchase', array(
            'purchase_id' => $id));

        $data['shipping_address'] = $this->general_model->getRecords('*', 'shipping_address', array(
            'shipping_party_id' => $data['data'][0]->purchase_party_id,
            'shipping_party_type' => $data['data'][0]->purchase_party_type
        ));

        $data['brands'] = $this->brand_call();

        $item_types = $this->general_model->getRecords('item_type,purchase_item_description', 'purchase_item', array(
            'purchase_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->purchase_item_description != "") {
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

        if ($data['data'][0]->purchase_tax_amount > 0 || $data['access_settings'][0]->tax_type != "no_tax") {

            $data['tax'] = $this->tax_call();
        }

        if ($data['data'][0]->purchase_nature_of_supply == "service" || $data['data'][0]->purchase_nature_of_supply == "both") {

            $data['sac'] = $this->sac_call();
            $data['service_category'] = $this->service_category_call();
        }

        if ($data['data'][0]->purchase_nature_of_supply == "product" || $data['data'][0]->purchase_nature_of_supply == "both") {

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
            $data['tax_tds']          = $this->tax_call_type('TDS');
            $data['tax_tcs']          = $this->tax_call_type('TCS');
            $data['tax_gst']          = $this->tax_call_type('GST');
            $data['tax_section'] = $this->tax_section_call();

            if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }
        }

        $purchase_service_items = array();
        $purchase_product_items = array();

        if (($data['data'][0]->purchase_nature_of_supply == "service" || $data['data'][0]->purchase_nature_of_supply == "both") && $service == 1) {

            $service_items = $this->common->purchase_items_service_list_field($id);
            $purchase_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_nature_of_supply == "product" || $data['data'][0]->purchase_nature_of_supply == "both") {

            /* if ($product == 2)
              {
              $product_items          = $this->common->purchase_items_product_inventory_list_field($id);
              $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              if ($product == 1)
              {
              } */
            $product_items = $this->common->purchase_items_product_list_field($id);
            $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_product_items, $purchase_service_items);

        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->purchase_tax_amount > 0 && $data['data'][0]->purchase_igst_amount > 0 && ($data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_cgst_amount > 0 || $data['data'][0]->purchase_sgst_amount > 0) && $data['data'][0]->purchase_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount == 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_discount_amount > 0 || $data['access_settings'][0]->discount_visible == "yes") {
            /* Discount */
            $discountExist = 1;
            $data['discount'] = $this->discount_call();
        }

        if ($data['data'][0]->purchase_tds_amount > 0 || $data['data'][0]->purchase_tcs_amount > 0 || $data['access_settings'][0]->tds_visible == "yes") {
            /* Discount */
            $tdsExist = 1;
        }

        if ($description > 0 || $data['access_settings'][0]->description_visible == "yes") {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if ($data['data'][0]->purchase_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_billing_state_id);
         $data['department'] = $this->general_model->getRecords('*', 'department', array('delete_status' => 0,'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        $department_id = $data['data'][0]->department_id;
         $data['sub_department'] = $this->general_model->getRecords('*', 'sub_department', array('delete_status'  => 0, 'department_id'   => $department_id, 'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['cess_exist'] = $cess_exist;
        $data['tax_exist'] = $taxExist;
        $data['is_utgst'] = $is_utgst;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;

        $this->load->view('purchase/edit', $data);
    }

    public function edit_purchase() {
        $data = $this->get_default_country_state();
        $purchase_id = $this->input->post('purchase_id');
        $purchase_module_id = $this->config->item('purchase_module');
        $module_id = $purchase_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);


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
        $access_settings = $section_modules['access_settings'];

        $currency = $this->input->post('currency_id');
        if ($section_modules['access_settings'][0]->invoice_creation == "automatic") {
            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old')) {
                $primary_id = "purchase_id";
                $table_name = $this->config->item('purchase_table');
                $date_field_name = "purchase_date";
                $current_date = date('Y-m-d', strtotime($this->input->post('invoice_date')));
                $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            } else {
                $invoice_number = $this->input->post('invoice_number');
            }
        } else {
            $invoice_number = $this->input->post('invoice_number');
        }
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;

        if (isset($_FILES["purchase_file"]["name"]) && $_FILES["purchase_file"]["name"] != ""){
            $path_parts = pathinfo($_FILES["purchase_file"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Purchase')){
                mkdir('./assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Purchase', 0777, TRUE);
            } 
            $url = 'assets/images/BRANCH-'.$this->session->userdata('SESS_BRANCH_ID').'/Purchase/'.$image_path;
            if (in_array($path_parts['extension'], array("JPG","jpg","jpeg","JPEG","PNG","png","pdf","PDF" ))){
                if (is_uploaded_file($_FILES["purchase_file"]["tmp_name"])){
                    if (move_uploaded_file($_FILES["purchase_file"]["tmp_name"], $url)){
                        $image_name = $image_path;
                    }
                }
            }
        }else{
            $image_name = $this->input->post('hidden_purchase_file');
        }

        $purchase_data = array(
            "purchase_date" => date('Y-m-d', strtotime($this->input->post('invoice_date'))),
            "purchase_invoice_number" => $invoice_number,
            "purchase_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "purchase_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "purchase_discount_amount" => $this->input->post('total_discount_amount') ? (float) $this->input->post('total_discount_amount') : 0,
            "purchase_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "purchase_tax_cess_amount" => 0,
            "purchase_taxable_value" => $this->input->post('total_taxable_amount') ? (float) $this->input->post('total_taxable_amount') : 0,
            "purchase_tds_amount" => $this->input->post('total_tds_amount') ? (float) $this->input->post('total_tds_amount') : 0,
            "purchase_tcs_amount"
            => $this->input->post('total_tcs_amount') ? (float) $this->input->post('total_tcs_amount') : 0,
            "purchase_igst_amount" => 0,
            "purchase_cgst_amount" => 0,
            "purchase_sgst_amount" => 0,
            "from_account" => 'supplier',
            "to_account" => 'purchase',
            "purchase_paid_amount" => 0,
            "credit_note_amount" => 0,
            "debit_note_amount" => 0,
            "purchase_supplier_invoice_number" => $this->input->post('supplier_ref'),
            "purchase_supplier_date" => ($this->input->post('supplier_date') != '' ? date('Y-m-d', strtotime($this->input->post('supplier_date'))) : ''),
            "purchase_delivery_challan_number" => $this->input->post('delivery_challan_number'),
            "purchase_delivery_date" => ($this->input->post('delivery_date') != '' ? date('Y-m-d', strtotime($this->input->post('delivery_date'))) : ''),
            "purchase_e_way_bill_number" => $this->input->post('e_way_bill'),
            "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "purchase_party_id" => $this->input->post('supplier'),
            "purchase_party_type" => "supplier",
            "purchase_nature_of_supply" => $this->input->post('nature_of_supply'),
            "purchase_order_number" => $this->input->post('order_number'),
            "purchase_order_date" => ($this->input->post('purchase_order_date') != '' ? date('Y-m-d', strtotime($this->input->post('purchase_order_date'))) : ''),
            "purchase_received_via" => $this->input->post('received_via'),
            "purchase_grn_number" => $this->input->post('grn_number'),
            "purchase_grn_date" => ($this->input->post('grn_date') != '' ? date('Y-m-d', strtotime($this->input->post('grn_date'))) : ''),
            "purchase_type_of_supply" => $this->input->post('type_of_supply'),
            "purchase_gst_payable" => $this->input->post('gst_payable'),
            "purchase_billing_country_id" => $this->input->post('billing_country'),
            "due_days"  => $this->input->post('due_days'),
            "purchase_billing_state_id" => $this->input->post('billing_state'),
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
            "note2" => $this->input->post('note2'),
            "purchase_file" => $image_name
        );

        $purchase_data['freight_charge_tax_id'] = $this->input->post('freight_charge_tax_id') ? (float) $this->input->post('freight_charge_tax_id') : 0;
        $purchase_data['insurance_charge_tax_id'] = $this->input->post('insurance_charge_tax_id') ? (float) $this->input->post('insurance_charge_tax_id') : 0;
        $purchase_data['packing_charge_tax_id'] = $this->input->post('packing_charge_tax_id') ? (float) $this->input->post('packing_charge_tax_id') : 0;
        $purchase_data['incidental_charge_tax_id'] = $this->input->post('incidental_charge_tax_id') ? (float) $this->input->post('incidental_charge_tax_id') : 0;
        $purchase_data['inclusion_other_charge_tax_id'] = $this->input->post('inclusion_other_charge_tax_id') ? (float) $this->input->post('inclusion_other_charge_tax_id') : 0;
        $purchase_data['exclusion_other_charge_tax_id'] = $this->input->post('exclusion_other_charge_tax_id') ? (float) $this->input->post('exclusion_other_charge_tax_id') : 0;
        
        /* customize for leather craft*/
        /* if(@$this->input->post('cmb_department')){
            $purchase_data['department_id'] = $this->input->post('cmb_department');
        }
        if(@$this->input->post('cmb_subdepartment')){
            $purchase_data['sub_department_id'] = $this->input->post('cmb_subdepartment');
        }*/

        $round_off_value = $purchase_data['purchase_grand_total'];

        if ($section_modules['access_common_settings'][0]->round_off_access == "yes" || $this->input->post('round_off_key') == "yes") {
            if ($this->input->post('round_off_value') != "" && $this->input->post('round_off_value') > 0) {
                $round_off_value = $this->input->post('round_off_value');
            }
        }

        $purchase_data['round_off_amount'] = bcsub($purchase_data['purchase_grand_total'], $round_off_value, $section_modules['access_common_settings'][0]->amount_precision);

        $purchase_data['purchase_grand_total'] = $round_off_value;

        $purchase_data['supplier_payable_amount'] = $purchase_data['purchase_grand_total'];
        if (isset($purchase_data['purchase_tds_amount']) && $purchase_data['purchase_tds_amount'] > 0) {
            $purchase_data['supplier_payable_amount'] = bcsub($purchase_data['purchase_grand_total'], $purchase_data['purchase_tds_amount']);
        }

        $tax_type = $this->input->post('tax_type');

        $purchase_tax_amount = $purchase_data['purchase_tax_amount'];
        $purchase_tax_amount = $purchase_data['purchase_tax_amount'] + (float) ($this->input->post('total_other_taxable_amount'));
        if ($tax_type == "gst") {
            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
            $cgst_amount_percentage = $tax_split_percentage;
            $sgst_amount_percentage = 100 - $cgst_amount_percentage;

            if ($purchase_data['purchase_type_of_supply'] != 'import') {
                if ($purchase_data['purchase_type_of_supply'] == 'intra_state') {
                    $purchase_data['purchase_igst_amount'] = 0;
                    $purchase_data['purchase_cgst_amount'] = ($purchase_tax_amount * $cgst_amount_percentage) / 100;
                    $purchase_data['purchase_sgst_amount'] = ($purchase_tax_amount * $sgst_amount_percentage) / 100;
                    $purchase_data['purchase_tax_cess_amount'] = $total_cess_amnt;
                } else {
                    $purchase_data['purchase_igst_amount'] = $purchase_tax_amount;
                    $purchase_data['purchase_cgst_amount'] = 0;
                    $purchase_data['purchase_sgst_amount'] = 0;
                    $purchase_data['purchase_tax_cess_amount'] = $total_cess_amnt;
                }
            }
            /* else
              {
              if ($purchase_data['purchase_type_of_supply'] == "export_with_payment")
              {
              $purchase_data['purchase_igst_amount'] = $purchase_tax_amount;
              $purchase_data['purchase_cgst_amount'] = 0;
              $purchase_data['purchase_sgst_amount'] = 0;
              $purchase_data['purchase_tax_cess_amount'] = $total_cess_amnt;
              }
              } */
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id')) {
            $purchase_data['converted_grand_total'] = $purchase_data['purchase_grand_total'];
        } else {
            $purchase_data['converted_grand_total'] = 0;
        }

        $data_main = array_map('trim', $purchase_data);
        $purchase_table = $this->config->item('purchase_table');
        $where = array(
            'purchase_id' => $purchase_id);

        if ($this->general_model->updateData($purchase_table, $data_main, $where)) {
            $successMsg = 'Purchase Updated Successfully';
            $this->session->set_flashdata('purchase_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $purchase_id,
                'table_name' => $purchase_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'purchase Updated');
            $data_main['purchase_id'] = $purchase_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $purchase_item_data = $this->input->post('table_data');

            $js_data = json_decode($purchase_item_data);
            $js_data               = array_reverse($js_data);
            $item_table = $this->config->item('purchase_item_table');
            if (!empty($js_data)) {
                $js_data1 = array();
                $new_item_ids = $this->getValues($js_data, 'item_id');

                $string = 'purchase_item_id,purchase_item_quantity,item_type,item_id';
                $table = 'purchase_item';
                $where = array(
                    'purchase_id' => $purchase_id,
                    'delete_status' => 0);
                $old_purchase_items = $this->general_model->getRecords($string, $table, $where, $order = "");
                $old_item_ids = $this->getValues($old_purchase_items, 'item_id');
                $not_deleted_ids = array();

                foreach ($old_purchase_items as $key => $value) {
                    if($value->item_type == 'product' ){
                    $product_string = '*';
                    $product_table = 'products';
                    $product_where = array(
                        'product_id' => $value->item_id);
                    $product = $this->general_model->getRecords($product_string, $product_table, $product_where, $order = "");
                    $product_qty = bcsub($product[0]->product_quantity, $value->purchase_item_quantity, $section_modules['access_common_settings'][0]->amount_precision);
                    /* update Product Price */
                    $pro_price = $this->getAVGItemPrice($value->item_id);
                    /* END */
                    $product_data = array(
                        'product_price' => $pro_price,
                        'product_quantity' => $product_qty);

                    $this->general_model->updateData($product_table, $product_data, $product_where);

                    //update stock history
                    $where = array(
                        'item_id' => $value->item_id,
                        'reference_id' => $purchase_id,
                        'reference_type' => 'purchase',
                        'delete_status' => 0);
                    $this->db->where($where);
                    $history = $this->db->get('quantity_history')->result();
                    if (!empty($history)) {
                        $history_quantity = bcsub($history[0]->quantity, $value->purchase_item_quantity, $section_modules['access_common_settings'][0]->amount_precision);
                        $update_history_quantity = array(
                            'quantity' => $history_quantity,
                            'updated_date' => date('Y-m-d'),
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                        $this->db->where($where);
                        $this->db->update('quantity_history', $update_history_quantity);
                    } else {
                        // quantity history
                        $history = array(
                            "item_id" => $value->item_id,
                            "item_type" => 'product',
                            "reference_id" => $purchase_id,
                            "reference_number" => $invoice_number,
                            "reference_type" => 'purchase',
                            "quantity" => 0,
                            "stock_type" => 'indirect',
                            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                            "added_date" => date('Y-m-d'),
                            "entry_date" => date('Y-m-d'),
                            "added_user_id" => $this->session->userdata('SESS_USER_ID'));
                        $this->general_model->insertData("quantity_history", $history);
                    }

                }
                }

                foreach ($js_data as $key => $value) {

                    /*SK Customization*/
                    if($value->item_id == 0){
                        $product_module_id = $this->config->item('product_module');
                        $data['module_id'] = $product_module_id;
                        $modules           = $this->modules;
                        $privilege         = "add_privilege";
                        $data['privilege'] = "add_privilege";
                        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);
                        $access_settings          = $section_modules['access_settings'];
                        $primary_id1               = "product_id";
                        $table_name1               = "products";
                        $date_field_name1          = "added_date";
                        $current_date1             = date('Y-m-d');
                        $product_code = $this->generate_invoice_number($access_settings, $primary_id1, $table_name1, $date_field_name1, $current_date1);

                        $product_data = array(
                            "product_code"           => $product_code,
                            "product_name"           => $value->item_name,
                            "product_batch"          => 'BATCH-01',
                            //"product_category_id"    => $value->item_category,
                            "product_subcategory_id" => 0,
                            "product_quantity"       => $value->item_quantity,
                            "product_unit"           => $value->item_uom,
                            "product_unit_id"        => $value->item_uom,
                            "product_hsn_sac_code"   => $value->item_hsn_sac_code,
                            "product_price"          => $value->item_price,
                            "product_gst_id"         => $value->item_tax_id,
                            "product_gst_value"      => $value->item_tax_percentage,
                            "product_discount_id"    => $value->item_discount_id,
                            "product_details"        => $value->item_description,
                            "is_assets"              => 'N',
                            "is_varients"            => 'N',
                            "product_type"           => 'finishedgoods',
                            "added_date"             => date('Y-m-d'),
                            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
                        );
                        $product_id = $this->general_model->insertData('products', $product_data);
                        //$item_data['item_id']  => $product_id;
                    }
                    /*SK Customization*/

                    if ($value != null) {
                        $item_id = ($value->item_id != 0) ?  $value->item_id : $product_id;
                        //$item_id = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $purchase_item_unit_price_after_discount = ($value->item_price ? (float) $value->item_price : 0);

                        if ($value->item_taxable_value > 0 && $value->item_quantity > 0)
                            $purchase_item_unit_price_after_discount = ($value->item_taxable_value / $value->item_quantity);

                        $item_data = array(
                            "item_id" => ($value->item_id != 0) ?  $value->item_id : $product_id,
                            "item_type" => $value->item_type,
                            "purchase_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "purchase_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "purchase_item_unit_price_after_discount" => $purchase_item_unit_price_after_discount,
                            "purchase_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "purchase_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "purchase_item_discount_amount" => $value->item_discount_amount ? (float) $value->item_discount_amount : 0,
                            "purchase_item_discount_id" => $value->item_discount_id ? (float) $value->item_discount_id : 0,
                            "purchase_item_tds_id" => $value->item_tds_id ? (float) $value->item_tds_id : 0,
                            "purchase_item_tds_percentage" => $value->item_tds_percentage ? (float) $value->item_tds_percentage : 0,
                            "purchase_item_tds_amount" => $value->item_tds_amount ? (float) $value->item_tds_amount : 0,
                            "purchase_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "purchase_item_tax_id" => $value->item_tax_id ? (float) $value->item_tax_id : 0,
                            "purchase_item_tax_cess_id" => $value->item_tax_cess_id ? (float) $value->item_tax_cess_id : 0,
                            "purchase_item_igst_percentage" => 0,
                            "purchase_item_igst_amount" => 0,
                            "purchase_item_cgst_percentage" => 0,
                            "purchase_item_cgst_amount" => 0,
                            "purchase_item_sgst_percentage" => 0,
                            "purchase_item_sgst_amount" => 0,
                            "purchase_item_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "purchase_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            "purchase_item_tax_cess_percentage" => 0,
                            "purchase_item_tax_cess_amount" => 0,
                            "purchase_item_description" => $value->item_description ? $value->item_description : "",
                            "debit_note_quantity" => 0,
                            "purchase_id" => $purchase_id);

                        $purchase_item_tax_amount = $item_data['purchase_item_tax_amount'];
                        $purchase_item_tax_percentage = $item_data['purchase_item_tax_percentage'];

                        if ($tax_type == "gst") {
                            $tax_split_percentage = $section_modules['access_common_settings'][0]->tax_split_percentage;
                            $cgst_amount_percentage = $tax_split_percentage;
                            $sgst_amount_percentage = 100 - $cgst_amount_percentage;
                            $item_tax_cess_amount = ($value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0 );
                            $item_tax_cess_percentage = $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0;

                            if ($data['branch'][0]->branch_country_id == $purchase_data['purchase_billing_country_id']) {

                                if ($data['branch'][0]->branch_state_id == $purchase_data['purchase_billing_state_id']) {
                                    $item_data['purchase_item_igst_amount'] = 0;
                                    $item_data['purchase_item_cgst_amount'] = ($purchase_item_tax_amount * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_item_sgst_amount'] = ($purchase_item_tax_amount * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_item_igst_percentage'] = 0;
                                    $item_data['purchase_item_cgst_percentage'] = ($purchase_item_tax_percentage * $cgst_amount_percentage) / 100;
                                    $item_data['purchase_item_sgst_percentage'] = ($purchase_item_tax_percentage * $sgst_amount_percentage) / 100;
                                    $item_data['purchase_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                } else {
                                    $item_data['purchase_item_igst_amount'] = $purchase_item_tax_amount;
                                    $item_data['purchase_item_cgst_amount'] = 0;
                                    $item_data['purchase_item_sgst_amount'] = 0;
                                    $item_data['purchase_item_tax_cess_amount'] = $item_tax_cess_amount;
                                    $item_data['purchase_item_igst_percentage'] = $purchase_item_tax_percentage;
                                    $item_data['purchase_item_cgst_percentage'] = 0;
                                    $item_data['purchase_item_sgst_percentage'] = 0;
                                    $item_data['purchase_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                                }
                            }/* else{
                              if ($purchase_data['purchase_type_of_supply'] == "export_with_payment"){
                              $item_data['purchase_item_igst_amount'] = $purchase_item_tax_amount;
                              $item_data['purchase_item_cgst_amount'] = 0;
                              $item_data['purchase_item_sgst_amount'] = 0;
                              $item_data['purchase_item_tax_cess_amount'] = $item_tax_cess_amount;
                              $item_data['purchase_item_igst_percentage'] = $purchase_item_tax_percentage;
                              $item_data['purchase_item_cgst_percentage'] = 0;
                              $item_data['purchase_item_sgst_percentage'] = 0;
                              $item_data['purchase_item_tax_cess_percentage'] = $item_tax_cess_percentage;
                              }
                              } */
                        }
                        $LeatherCraft_id = $this->config->item('LeatherCraft');
                        $table = 'purchase_item';
                        if (($item_key = array_search($value->item_id, $old_item_ids)) !== false) {
                            unset($old_item_ids[$item_key]);
                            $purchase_item_id = $old_purchase_items[$item_key]->purchase_item_id;
                            array_push($not_deleted_ids, $purchase_item_id);
                            //$item_id = $value->item_id;
                            $item_id = ($value->item_id != 0) ?  $value->item_id : $product_id;
                            if($LeatherCraft_id == $this->session->userdata('SESS_BRANCH_ID')){
                                    $item_id =  $this->updateBarcodeProduct($item_id,$this->input->post('grn_number'),$this->input->post('supplier'));
                            }
                            
                            $where = array('purchase_item_id' => $purchase_item_id);
                            $this->general_model->updateData($table, $item_data, $where);
                        } else {
                                if($LeatherCraft_id == $this->session->userdata('SESS_BRANCH_ID')){
                                    $item_id =  $this->createBatchProduct($value->item_id,$this->input->post('grn_number'),$this->input->post('supplier'));
                                }else{
                                    //$item_id = $value->item_id;
                                    $item_id = ($value->item_id != 0) ?  $value->item_id : $product_id;
                                }
                                $item_data['item_id'] = $item_id;

                            $this->general_model->insertData($table, $item_data);
                        }
                        /* update product stock */
                        if ($value->item_type == "product" || $value->item_type == 'product_inventory') {
                            $product_string = '*';
                            $product_table = 'products';
                            $product_where = array('product_id' => $item_id);
                            $product = $this->general_model->getRecords($product_string, $product_table, $product_where, $order = "");
                            $product_qty = bcadd($product[0]->product_quantity, $quantity, $section_modules['access_common_settings'][0]->amount_precision);
                            /* update Product Price */
                            $pro_price = $this->getAVGItemPrice($item_id);
                            /* END */
                            $product_data = array(
                                'product_price' => $pro_price,
                                'product_quantity' => $product_qty);
                            /* $product_data   = array('product_quantity' => $product_qty ); */
                            $this->general_model->updateData($product_table, $product_data, $product_where);

                            //update stock history
                            $where = array(
                                'item_id' => $item_id,
                                'reference_id' => $purchase_id,
                                'reference_type' => 'purchase',
                                'delete_status' => 0);
                            $this->db->where($where);
                            $history = $this->db->get('quantity_history')->result();

                            if (!empty($history)) {
                                $history_quantity = bcadd($history[0]->quantity, $quantity, $section_modules['access_common_settings'][0]->amount_precision);
                                $update_history_quantity = array(
                                    'quantity' => $history_quantity,
                                    'updated_date' => date('Y-m-d'),
                                    'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                                $this->db->where($where);
                                $this->db->update('quantity_history', $update_history_quantity);
                            } else {
                                // quantity history
                                $history = array(
                                    "item_id" => $item_id,
                                    "item_type" => 'product',
                                    "reference_id" => $purchase_id,
                                    "reference_number" => $invoice_number,
                                    "reference_type" => 'purchase',
                                    "quantity" => $quantity,
                                    "stock_type" => 'indirect',
                                    "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                    "added_date" => date('Y-m-d'),
                                    "entry_date" => date('Y-m-d'),
                                    "added_user_id" => $this->session->userdata('SESS_USER_ID'));
                                $this->general_model->insertData("quantity_history", $history);
                            }
                        }
                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                    }
                }

                if (!empty($old_purchase_items)) {
                    foreach ($old_purchase_items as $key => $items) {
                        if (!in_array($items->purchase_item_id, $not_deleted_ids)) {
                            $table = 'purchase_item';
                            $where = array(
                                'purchase_item_id' => $items->purchase_item_id);
                            $purchase_data = array(
                                'delete_status' => 1);

                            $this->general_model->updateData($table, $purchase_data, $where);
                        }
                    }
                }
                $item_data = $js_data1;

                if (in_array($data['accounts_module_id'], $section_modules['active_add'])) {

                    if (in_array($data['accounts_sub_module_id'], $section_modules['access_sub_modules'])) {

                        $action = "edit";
                        $this->purchase_voucher_entry($data_main, $js_data1, $action, $data['branch']);
                    }
                }
            }
            redirect('purchase', 'refresh');
        } else {
            $errorMsg = 'Purchase Update Unsuccessful';
            $this->session->set_flashdata('purchase_error',$errorMsg);
            redirect('purchase', 'refresh');
        }
    }

    public function delete() {

        $id = $this->input->post('delete_id');
        $purchase_id = $this->encryption_url->decode($id);

        if ($purchase_id != "") {
            $purchase_module_id = $this->config->item('purchase_module');
            $data['module_id'] = $purchase_module_id;
            $modules = $this->modules;
            $privilege = "delete_privilege";
            $data['privilege'] = "delete_privilege";
            $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);
            /* presents all the needed */
            $data = array_merge($data, $section_modules);
            $access_common_settings = $section_modules['access_common_settings'];

            /* delete voucher from payment table */
            $purchase_voucher_id = $this->general_model->getRecords('purchase_voucher_id', 'purchase_voucher', array('reference_id'   => $purchase_id, 'reference_type' => 'purchase'));

            if(!empty($purchase_voucher_id)){
                $this->general_model->deleteCommonVoucher(array('table' => 'purchase_voucher', 'where' => array('purchase_voucher_id' =>$purchase_voucher_id[0]->purchase_voucher_id)),array('table' => 'accounts_purchase_voucher', 'where' => array('purchase_voucher_id' =>$purchase_voucher_id[0]->purchase_voucher_id)));
            }

            /*$this->general_model->updateData('purchase_voucher', array(
                'delete_status' => 1), array(
                'reference_id' => $purchase_id,
                'reference_type' => 'purchase'));*/

            $credit_notes = $this->general_model->getRecords('purchase_credit_note_id', 'purchase_credit_note', array(
                'purchase_id' => $purchase_id,
                'delete_status' => 0));
            $this->general_model->updateData('purchase_credit_note', array(
                'delete_status' => 1), array(
                'purchase_id' => $purchase_id));

            foreach ($credit_notes as $key => $value) {
                /* delete voucher from payment table */
                $purchase_credit_note_id = $this->general_model->getRecords('purchase_voucher_id', 'purchase_voucher', array('reference_id'   => $value->purchase_credit_note_id, 'reference_type' => 'purchase_credit_note'));

                if(!empty($purchase_credit_note_id)){
                    $this->general_model->deleteCommonVoucher(array('table' => 'purchase_voucher', 'where' => array('purchase_voucher_id' =>$purchase_credit_note_id[0]->purchase_voucher_id)),array('table' => 'accounts_purchase_voucher', 'where' => array('purchase_voucher_id' =>$purchase_credit_note_id[0]->purchase_voucher_id)));
                }

                /*$this->general_model->updateData('purchase_voucher', array(
                    'delete_status' => 1), array(
                    'reference_id' => $value->purchase_credit_note_id,
                    'reference_type' => 'purchase_credit_note'));*/

                $purchase_credit_note_items = $this->general_model->getRecords('*', 'purchase_credit_note_item', array(
                    'purchase_credit_note_id' => $value->purchase_credit_note_id,
                    'delete_status' => 0));

                //$this->general_model->updateData('purchase_credit_note_item' , array(
                //    'delete_status' => 1 ) , array(
                //'purchase_credit_note_id' => $value->purchase_credit_note_id ));
                foreach ($purchase_credit_note_items as $k => $val) {
                    if ($val->item_type == "product" || $val->item_type == "product_inventory") {
                        $product_data = $this->common->product_field($val->item_id);
                        $product_result = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join'], $product_data['order']);
                        $product_quantity = ((int)$product_result[0]->product_quantity - (int)$val->purchase_credit_note_item_quantity);
                        /* update Product Price */
                        $pro_price = $this->getAVGItemPrice($val->item_id);
                        /* END */
                        $data = array(
                            'product_price' => $pro_price,
                            'product_quantity' => $product_quantity);

                        $where = array(
                            'product_id' => $val->item_id);
                        $product_table = $this->config->item('product_table');
                        $this->general_model->updateData($product_table, $data, $where);

                        //update stock history
                        $where = array(
                            'item_id' => $val->item_id,
                            'reference_id' => $value->purchase_credit_note_id,
                            'reference_type' => 'purchase_credit_note');

                        $history_data = array(
                            'delete_status' => 1,
                            'updated_date' => date('Y-m-d'),
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                        $this->db->where($where);
                        $this->db->update('quantity_history', $history_data);
                    }
                }
            }

            $debit_notes = $this->general_model->getRecords('purchase_debit_note_id', 'purchase_debit_note', array(
                'purchase_id' => $purchase_id,
                'delete_status' => 0));
            $this->general_model->updateData('purchase_debit_note', array(
                'delete_status' => 1), array(
                'purchase_id' => $purchase_id));

            foreach ($debit_notes as $key => $value) {

                /* delete voucher from payment table */
                $purchase_debit_note_id = $this->general_model->getRecords('purchase_voucher_id', 'purchase_voucher', array('reference_id'   => $value->purchase_debit_note_id, 'reference_type' => 'purchase_debit_note'));

                if(!empty($purchase_debit_note_id)){
                    $this->general_model->deleteCommonVoucher(array('table' => 'purchase_voucher', 'where' => array('purchase_voucher_id' =>$purchase_debit_note_id[0]->purchase_voucher_id)),array('table' => 'accounts_purchase_voucher', 'where' => array('purchase_voucher_id' =>$purchase_debit_note_id[0]->purchase_voucher_id)));
                }

                /*$this->general_model->updateData('purchase_voucher', array(
                    'delete_status' => 1), array(
                    'reference_id' => $value->purchase_debit_note_id,
                    'reference_type' => 'purchase_debit_note'));*/

                $purchase_debit_note_items = $this->general_model->getRecords('*', 'purchase_debit_note_item', array(
                    'purchase_debit_note_id' => $value->purchase_debit_note_id,
                    'delete_status' => 0));

                // $this->general_model->updateData('purchase_debit_note_item' , array(
                //    'delete_status' => 1 ) , array(
                //  'purchase_debit_note_id' => $value->purchase_debit_note_id ));
                foreach ($purchase_debit_note_items as $k1 => $val1) {
                    if ($val1->item_type == "product" || $val1->item_type == "product_inventory") {
                        $product_data = $this->common->product_field($val1->item_id);
                        $product_result = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join'], $product_data['order']);
                        $product_quantity = ((int)$product_result[0]->product_quantity - (int)$val1->purchase_debit_note_item_quantity);
                        /* update Product Price */
                        $pro_price = $this->getAVGItemPrice($val1->item_id);
                        /* END */
                        $data = array(
                            'product_price' => $pro_price,
                            'product_quantity' => $product_quantity);
                        /* $data             = array(
                          'product_quantity' => $product_quantity); */
                        $where = array(
                            'product_id' => $val1->item_id);
                        $product_table = $this->config->item('product_table');
                        $this->general_model->updateData($product_table, $data, $where);

                        //update stock history
                        $where = array(
                            'item_id' => $val->item_id,
                            'reference_id' => $value->purchase_debit_note_id,
                            'reference_type' => 'purchase_debit_note');

                        $history_data = array(
                            'delete_status' => 1,
                            'updated_date' => date('Y-m-d'),
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                        $this->db->where($where);
                        $this->db->update('quantity_history', $history_data);
                    }
                }
            }

            $this->general_model->updateData('payment_voucher', array(
                'delete_status' => 1), array(
                'reference_id' => $purchase_id,
                'reference_type' => 'purchase'));

            $where = "(reference_id like '%," . $purchase_id . "%' or reference_id like '%" . $purchase_id . ",%')  and reference_type='purchase' and delete_status=0";
            $payment_vouchers = $this->general_model->getRecords('*', 'payment_voucher', $where);

            /* foreach starts */
            foreach ($payment_vouchers as $key => $value) {
                $old_reference_id = explode(',', $value->reference_id);
                $i = 0;
                $flag = 0;
                $new_reference_id = '';
                $flag_key = "";
                foreach ($old_reference_id as $k => $val) {
                    if ($val == $purchase_id) {
                        $flag_key = $k;
                        $flag = 1;
                    } else {
                        if ($new_reference_id == "") {
                            $new_reference_id = $val;
                        } else {
                            $new_reference_id .= ',' . $val;
                        }
                    }
                }

                if ($flag == 1) {
                    $new_reference_number = '';
                    $new_receipt_amount = '';
                    $new_converted_receipt_amount = '';
                    $new_invoice_total = '';
                    $new_invoice_paid_amount = '';
                    $new_invoice_balance_amount = '';

                    $old_reference_number = explode(',', $value->reference_number);
                    $old_receipt_amount = explode(',', $value->imploded_receipt_amount);
                    $old_converted_receipt_amount = explode(',', $value->imploded_converted_receipt_amount);
                    $old_invoice_total = explode(',', $value->invoice_total);
                    $old_invoice_paid_amount = explode(',', $value->invoice_paid_amount);
                    $old_invoice_balance_amount = explode(',', $value->invoice_balance_amount);

                    foreach ($old_reference_number as $k => $val) {
                        if ($k != $flag_key) {
                            if ($new_reference_number == "") {
                                $new_reference_number = $val;
                            } else {
                                $new_reference_number .= ',' . $val;
                            }
                        }
                    }

                    $receipt_amount = "";
                    foreach ($old_receipt_amount as $k => $val) {
                        if ($k != $flag_key) {
                            if ($new_receipt_amount == "") {
                                $new_receipt_amount = $val;
                            } else {
                                $new_receipt_amount .= ',' . $val;
                            }
                        } else {
                            $receipt_amount = $val;
                        }
                    }

                    $converted_receipt_amount = "";
                    foreach ($old_converted_receipt_amount as $k => $val) {
                        if ($k != $flag_key) {
                            if ($new_converted_receipt_amount == "") {
                                $new_converted_receipt_amount = $val;
                            } else {
                                $new_converted_receipt_amount .= ',' . $val;
                            }
                        } else {
                            $converted_receipt_amount = $val;
                        }
                    }

                    foreach ($old_invoice_total as $k => $val) {
                        if ($k != $flag_key) {
                            if ($new_invoice_total == "") {
                                $new_invoice_total = $val;
                            } else {
                                $new_invoice_total .= ',' . $val;
                            }
                        }
                    }

                    foreach ($old_invoice_paid_amount as $k => $val) {
                        if ($k != $flag_key) {
                            if ($new_invoice_paid_amount == "") {
                                $new_invoice_paid_amount = $val;
                            } else {
                                $new_invoice_paid_amount .= ',' . $val;
                            }
                        }
                    }

                    foreach ($old_invoice_balance_amount as $k => $val) {
                        if ($k != $flag_key) {
                            if ($new_invoice_balance_amount == "") {
                                $new_invoice_balance_amount = $val;
                            } else {
                                $new_invoice_balance_amount .= ',' . $val;
                            }
                        }
                    }

                    $receipt_grand_total = bcsub($value->receipt_amount, $receipt_amount, $access_common_settings[0]->amount_precision);
                    $converted_receipt_grand_total = bcsub($value->converted_receipt_amount, $converted_receipt_amount, $access_common_settings[0]->amount_precision);

                    $receipt_voucher_data = array(
                        'reference_id' => $new_reference_id,
                        'reference_number' => $new_reference_number,
                        'receipt_amount' => $receipt_grand_total,
                        'converted_receipt_amount' => $converted_receipt_grand_total,
                        'imploded_receipt_amount' => $new_receipt_amount,
                        'imploded_converted_receipt_amount' => $new_converted_receipt_amount,
                        'invoice_total' => $new_invoice_total,
                        'invoice_paid_amount' => $new_invoice_paid_amount,
                        'invoice_balance_amount' => $new_invoice_balance_amount
                    );

                    $this->general_model->updateData('payment_voucher', $receipt_voucher_data, array(
                        'payment_id' => $value->payment_id));

                    $accounts_payment = $this->general_model->getRecords('*', 'accounts_payment_voucher', array(
                        'payment_voucher_id' => $value->payment_id,
                        'delete_status' => 0));
                    $data1 = array(
                        'voucher_amount' => $receipt_grand_total,
                        'converted_voucher_amount' => $converted_receipt_grand_total,
                        'dr_amount' => $receipt_grand_total,
                        'cr_amount' => 0);
                    $data2 = array(
                        'voucher_amount' => $receipt_grand_total,
                        'converted_voucher_amount' => $converted_receipt_grand_total,
                        'dr_amount' => 0,
                        'cr_amount' => $receipt_grand_total);
                    $this->general_model->updateData('accounts_payment_voucher', $data1, array(
                        'accounts_payment_id' => $accounts_payment[0]->accounts_payment_id));
                    $this->general_model->updateData('accounts_payment_voucher', $data2, array(
                        'accounts_payment_id' => $accounts_payment[1]->accounts_payment_id));
                }
            }

            /* foreach ends */

            $purchase_items = $this->general_model->getRecords('*', 'purchase_item', array(
                'purchase_id' => $purchase_id));
            $this->general_model->updateData('purchase', array(
                'delete_status' => 1), array(
                'purchase_id' => $purchase_id));

            $this->general_model->updateData('purchase_order', array(
                'purchase_id' => 0), array(
                'purchase_id' => $purchase_id,
                'delete_status' => 0));

            foreach ($purchase_items as $key => $value) {

                if ($value->item_type == "product") {
                    $product_string = '*';
                    $product_table = 'products';
                    $product_where = array(
                        'product_id' => $value->item_id);
                    $product = $this->general_model->getRecords($product_string, $product_table, $product_where, $order = "");


                    $product_qty = bcsub($product[0]->product_quantity, $value->purchase_item_quantity, $section_modules['access_common_settings'][0]->amount_precision);
                    /* update Product Price */
                    $pro_price = $this->getAVGItemPrice($value->item_id);
                    /* END */
                    $product_data = array(
                        'product_price' => $pro_price,
                        'product_quantity' => $product_qty);
                    /* $product_data = array(
                      'product_quantity' => $product_qty); */
                    $this->general_model->updateData($product_table, $product_data, $product_where);

                    //update stock history
                    $where = array(
                        'item_id' => $value->item_id,
                        'reference_id' => $purchase_id,
                        'reference_type' => 'purchase');

                    $history_data = array(
                        'delete_status' => 1,
                        'updated_date' => date('Y-m-d'),
                        'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                    $this->db->where($where);
                    $this->db->update('quantity_history', $history_data);
                } else
                if ($value->item_type == "product_inventory") {
                    $product_string = '*';
                    $product_table = 'product_inventory_varients';
                    $product_where = array(
                        'product_inventory_varients_id' => $value->item_id);
                    $product = $this->general_model->getRecords($product_string, $product_table, $product_where, $order = "");
                    $product_qty = bcadd($product[0]->quantity, $value->purchase_item_quantity, $section_modules['access_common_settings'][0]->amount_precision);
                    $product_data = array(
                        'quantity' => $product_qty);
                    $this->general_model->updateData($product_table, $product_data, $product_where);

                    //update stock history
                    $where = array(
                        'item_id' => $value->item_id,
                        'reference_id' => $purchase_id,
                        'reference_type' => 'purchase');

                    $history_data = array(
                        'delete_status' => 1,
                        'updated_date' => date('Y-m-d'),
                        'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                    $this->db->where($where);
                    $this->db->update('quantity_history', $history_data);
                }
            }
            $successMsg = 'Purchase Deleted Successfully';
            $this->session->set_flashdata('purchase_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $purchase_id,
                'table_name' => 'purchase',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Purchase Deleted');
            $this->general_model->insertData('log', $log_data);

            $redirect = 'purchase';
            if ($this->input->post('delete_redirect') != '')
                $redirect = $this->input->post('delete_redirect');
            redirect($redirect, 'refresh');
        }
        else {
            $errorMsg = 'Purchase Delete Unsuccessful';
            $this->session->set_flashdata('purchase_error',$errorMsg);
            redirect('purchase', 'refresh');
        }
    }

    public function pdf($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $purchase_module_id = $this->config->item('purchase_module');
        $data['module_id'] = $purchase_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);
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
        /*echo '<pre>'; print_r($data);exit();*/
        $purchase_data = $this->common->purchase_list_field1($id);
        $data['data'] = $this->general_model->getJoinRecords($purchase_data['string'], $purchase_data['table'], $purchase_data['where'], $purchase_data['join']);
        
       /* echo '<pre>'; print_r($data['data']);exit();*/
        $item_types = $this->general_model->getRecords('item_type,purchase_item_description', 'purchase_item', array(
            'purchase_id' => $id));

        $service = 0;
        $product = 0;
        $description = 0;

        foreach ($item_types as $key => $value) {

            if ($value->purchase_item_description != "") {
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

        $purchase_service_items = array();
        $purchase_product_items = array();

        if (($data['data'][0]->purchase_nature_of_supply == "service" || $data['data'][0]->purchase_nature_of_supply == "both") && $service == 1) {
            $service_items = $this->common->purchase_items_service_list_field($id);
            $purchase_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->purchase_nature_of_supply == "product" || $data['data'][0]->purchase_nature_of_supply == "both") {

            /* if ($product == 2)
              {
              $product_items          = $this->common->purchase_items_product_inventory_list_field($id);
              $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              if ($product == 1)
              {
              } */
            $product_items = $this->common->purchase_items_product_list_field($id);
            $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($purchase_product_items, $purchase_service_items);
        $igstExist = 0;
        $cgstExist = 0;
        $sgstExist = 0;
        $taxExist = 0;
        $tdsExist = 0;
        $discountExist = 0;
        $descriptionExist = 0;
        $cess_exist = 0;

        if ($data['data'][0]->purchase_tax_amount > 0 && $data['data'][0]->purchase_igst_amount > 0 && ($data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {

            /* igst tax slab */
            $igstExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_cgst_amount > 0 || $data['data'][0]->purchase_sgst_amount > 0) && $data['data'][0]->purchase_igst_amount == 0) {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
            /* Single tax */
            $taxExist = 1;
        } elseif ($data['data'][0]->purchase_tax_amount == 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
        }

        if ($data['data'][0]->purchase_tcs_amount > 0) {
            /* Discount $data['data'][0]->purchase_tds_amount > 0 || */
            $tdsExist = 1;
        }

        if ($data['data'][0]->purchase_discount_amount > 0) {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0) {
            /* Discount */
            $descriptionExist = 1;
        }

        if ($data['data'][0]->purchase_tax_cess_amount > 0) {
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_billing_state_id);

        $data['igst_exist'] = $igstExist;
        $data['cgst_exist'] = $cgstExist;
        $data['sgst_exist'] = $sgstExist;
        $data['tax_exist'] = $taxExist;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist'] = $tdsExist;
        $data['is_utgst'] = $is_utgst;
        $data['cess_exist'] = $cess_exist;

        if ($purchase_product_items && $purchase_service_items) {
            $nature_of_supply = "Product/Service";
        } elseif ($purchase_product_items) {
            $nature_of_supply = "Product";
        } elseif ($purchase_service_items) {
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

        $html = $this->load->view('purchase/pdf', $data, true);
        
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

        $dompdf->stream($data['data'][0]->purchase_invoice_number, array(
            'Attachment' => 0));
    }

    public function purchase_return($id) {
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
        $table = "purchase";
        $where = array(
            "purchase_id" => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        $product_items = $this->common->purchase_items_product_list_field($id, 0);
        $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        $service_items = $this->common->purchase_items_service_list_field($id);
        $purchase_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_product_items, $purchase_service_items);
        $access_settings = $data['access_settings'];
        $primary_id = "purchase_return_id";
        $table_name = "purchase_return";
        $date_field_name = "purchase_return_date";
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
        $this->load->view('purchase/purchase_return', $data);
    }

    public function get_purchase_item() {
        $purchase_id = $this->input->post('purchase_id');

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0));

        /* if ($inventory_access[0]->inventory_advanced == "yes")
          {
          $product_items                = $this->common->purchase_items_product_inventory_list_field($purchase_id, 0);
          $purchase_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
          }
          else
          { */
        $product_items = $this->common->purchase_items_product_list_field($purchase_id, 0);
        $purchase_items_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        //}

        $service_items = $this->common->purchase_items_service_list_field($purchase_id);
        $purchase_items_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items'] = array_merge($purchase_items_product_items, $purchase_items_service_items);

        $branch_details = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id'] = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id'] = $branch_details['branch'][0]->branch_id;
        $discount_data = $this->common->discount_field();
        $data['discount'] = $this->general_model->getRecords($discount_data['string'], $discount_data['table'], $discount_data['where']);
        $purchase_data = $this->general_model->getRecords('currency_id, purchase_billing_state_id', 'purchase', array(
            'purchase_id' => $purchase_id,
            'delete_status' => 0));
        $data['billing_state_id'] = $purchase_data[0]->purchase_billing_state_id;
        $data['tax'] = $this->tax_call();
        $data['currency'] = $this->general_model->getRecords('currency_id,currency_name', 'currency', array(
            'currency_id' => $purchase_data[0]->currency_id));
        echo json_encode($data);
    }

    public function email($id) {
        $id = $this->encryption_url->decode($id);
        $purchase_module_id = $this->config->item('purchase_module');
        $data['module_id'] = $purchase_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);

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
            $purchase_data = $this->common->purchase_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_data['string'], $purchase_data['table'], $purchase_data['where'], $purchase_data['join']);
            $country_data = $this->common->country_field($data['data'][0]->purchase_billing_country_id);
            $data['data_country'] = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
            $state_data = $this->common->state_field($data['data'][0]->purchase_billing_country_id, $data['data'][0]->purchase_billing_state_id);
            $data['data_state'] = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));

            /* if ($inventory_access[0]->inventory_advanced == "yes")
              {
              $product_items          = $this->common->purchase_items_product_inventory_list_field($id);
              $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
              }
              else
              {
              } */
            $product_items = $this->common->purchase_items_product_list_field($id);
            $purchase_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);

            $service_items = $this->common->purchase_items_service_list_field($id);
            $purchase_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
            $data['items'] = array_merge($purchase_product_items, $purchase_service_items);

            if ($purchase_product_items && $purchase_service_items) {
                $nature_of_supply = "Product/Service";
            } elseif ($purchase_product_items) {
                $nature_of_supply = "Product";
            } elseif ($purchase_service_items) {
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
                $igst = bcadd($igst, $value->purchase_item_igst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $cgst = bcadd($cgst, $value->purchase_item_cgst_amount, $section_modules['access_common_settings'][0]->amount_precision);
                $sgst = bcadd($sgst, $value->purchase_item_sgst_amount, $section_modules['access_common_settings'][0]->amount_precision);

                if ($value->purchase_item_description != "" && $value->purchase_item_description != null) {
                    $dpcount++;
                }

                if ($value->purchase_item_discount_amount != "" && $value->purchase_item_discount_amount != null && $value->purchase_item_discount_amount != 0) {
                    $dtcount++;
                }

                if ($value->purchase_item_description != "")
                    $description++;
            }

            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist = 0;
            $discountExist = 0;
            $tdsExist = 0;
            $descriptionExist = 0;

            if ($data['data'][0]->purchase_tax_amount > 0 && $data['data'][0]->purchase_igst_amount > 0 && ($data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {

                /* igst tax slab */
                $igstExist = 1;
            } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_cgst_amount > 0 || $data['data'][0]->purchase_sgst_amount > 0) && $data['data'][0]->purchase_igst_amount == 0) {
                /* cgst tax slab */
                $cgstExist = 1;
                $sgstExist = 1;
            } elseif ($data['data'][0]->purchase_tax_amount > 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
                /* Single tax */
                $taxExist = 1;
            } elseif ($data['data'][0]->purchase_tax_amount == 0 && ($data['data'][0]->purchase_igst_amount == 0 && $data['data'][0]->purchase_cgst_amount == 0 && $data['data'][0]->purchase_sgst_amount == 0)) {
                /* No tax */
                $igstExist = 0;
                $cgstExist = 0;
                $sgstExist = 0;
                $taxExist = 0;
            }

            if ($data['data'][0]->purchase_tds_amount > 0 || $data['data'][0]->purchase_tcs_amount > 0) {
                /* Discount */
                $tdsExist = 1;
            }

            if ($data['data'][0]->purchase_discount_amount > 0) {
                /* Discount */
                $discountExist = 1;
            }

            if ($description > 0) {
                /* Discount */
                $descriptionExist = 1;
            }

            $data['igst_exist'] = $igstExist;
            $data['cgst_exist'] = $cgstExist;
            $data['sgst_exist'] = $sgstExist;
            $data['tax_exist'] = $taxExist;
            $data['discount_exist'] = $discountExist;
            $data['description_exist'] = $descriptionExist;
            $data['tds_exist'] = $tdsExist;

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
            $html = $this->load->view('purchase/pdf', $data, true);

            include APPPATH . "third_party/dompdf/autoload.inc.php";

            //and now im creating new instance dompdf
            $file_path = "././pdf_form/";
            $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_invoice_number);
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
              $file_name = str_replace($data['access_settings'][0]->invoice_seperation, "_", $data['data'][0]->purchase_invoice_number);
              $mpdf->Output($file_path . $file_name . '.pdf', 'F');
              $data['pdf_file_path'] = 'pdf_form/' . $file_name . '.pdf';
              $data['pdf_file_name'] = $file_name . '.pdf'; */
            $purchase_data = $this->common->purchase_list_field1($id);
            $data['data'] = $this->general_model->getJoinRecords($purchase_data['string'], $purchase_data['table'], $purchase_data['where'], $purchase_data['join']);
            $branch_data = $this->common->branch_field();
            $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
            $data['email_setup'] = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID')));
            $data['email_template'] = $this->general_model->getRecords('*', 'email_template', array(
                'module_id' => $purchase_module_id,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0));
            $this->load->view('purchase/email', $data);
        } else {
            $this->load->view('purchase', $data);
        }
    }

    public function convert_currency() {
        $id = $this->input->post('convert_currency_id');
        $id = $this->encryption_url->decode($id);
        $new_converted_rate = $this->input->post('convertion_rate');

        $data = array(
            'currency_converted_rate' => $new_converted_rate,
            'converted_grand_total' => $this->input->post('converted_grand_total'));
        $this->general_model->updateData('purchase', $data, array(
            'purchase_id' => $id));

        //update converted voucher amount in account purchase voucher table

        $purchase_voucher_data = array(
            'converted_receipt_amount' => $this->input->post('converted_grand_total'));
        $this->general_model->updateData('purchase_voucher', $purchase_voucher_data, array(
            'reference_id' => $id,
            'delete_status' => 0,
            'reference_type' => 'purchase'));

        $purchase_voucher = $this->general_model->getRecords('purchase_voucher_id', 'purchase_voucher', array(
            'reference_id' => $id,
            'delete_status' => 0,
            'reference_type' => 'purchase'));
        $accounts_purchase_voucher = $this->general_model->getRecords('*', 'accounts_purchase_voucher', array(
            'purchase_voucher_id' => $purchase_voucher[0]->purchase_voucher_id,
            'delete_status' => 0));

        foreach ($accounts_purchase_voucher as $key1 => $value1) {
            $new_converted_voucher_amount = bcmul($accounts_purchase_voucher[$key1]->voucher_amount, $new_converted_rate, $section_modules['access_common_settings'][0]->amount_precision);
            $converted_voucher_amount = array(
                'converted_voucher_amount' => $new_converted_voucher_amount);
            $where = array(
                'accounts_purchase_id' => $accounts_purchase_voucher[$key1]->accounts_purchase_id);
            $voucher_table = "accounts_purchase_voucher";
            $this->general_model->updateData($voucher_table, $converted_voucher_amount, $where);
        }

        redirect('purchase', 'refresh');
    }

    public function remove_image($id){       
            $this->db->select('purchase_file,branch_id');
            $this->db->from('purchase');
            $this->db->where('purchase_id',$id);                    
            $get_purchase_qry = $this->db->get();            
            $purchase = $get_purchase_qry->result();
            $purchase_file = $purchase[0]->purchase_file;
            $branch_id = $purchase[0]->branch_id;
            $path = FCPATH.'assets/images/BRANCH-'.$branch_id.'/Purchase/'.$purchase_file;
            unlink($path);
        
        $this->general_model->updateData('purchase', array(
                'purchase_file' => '' ), array(
                'purchase_id'       => $id,
                'branch_id'       => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));
       
        $product_id = $this->encryption_url->encode($id);
              
        redirect('purchase/edit/'.$product_id, 'refresh');
    }


    public function GrnValidation(){
        $grn_number = trim($this->input->post('grn_number'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT purchase_id FROM  purchase WHERE purchase_grn_number = '".$grn_number."' AND purchase_id != '{$id}' ")->num_rows();

        echo json_encode(array('rows' => $rows ));
    }

    public function exportProductExcel($id){
       // $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $table_columns = array("Barcode", "Article", "COLOUR", "SIZE", "Qty", "MRP", "BRAND", "Category", "Sub Category", "HSN Code");

        $column = 0;

        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }
        $id = $this->encryption_url->decode($id);
        $list_data = $this->common->purchase_product_list_field($id);
        $posts = $this->general_model->getPageJoinRecords($list_data);
        // echo '<pre>';
        // print_r($posts);
        // exit();
        $excel_row = 2;
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $combination_id = $value->product_combination_id;
                    $colour_val = '-';
                    $size_val = '-';
                    if($combination_id != ''){
                     $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                    'combination_id'   => $combination_id,
                    'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                    $varient_value_id = $combination_data[0]->varient_value_id;

                    $sql = "SELECT V.varient_key,VV.varients_value  FROM  varients_value VV
                    JOIN varients V ON V.varients_id = VV.varients_id
                     WHERE varients_value_id IN (".$varient_value_id.")";
                     $qry = $this->db->query($sql);                    
                     $key = '';
                     if($qry->num_rows() > 0){
                        $var_lal = $qry->result_array();
                        foreach ($var_lal as $key1 => $val) {
                            $key = strtolower($val['varient_key']);
                            if($key == 'colour' || $key == 'colours' || $key == 'color'|| $key == 'colors'){
                               $colour_val = $val['varients_value'];
                            }

                            if($key == 'size' || $key == 'sizes'){
                               $size_val = $val['varients_value'];
                            }
                        }
                    }
                }
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $value->product_barcode);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value->product_code);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $colour_val);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $size_val);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $value->purchase_item_quantity);
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $value->product_mrp_price);  
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $value->brand_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $value->category_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $value->sub_category_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $value->product_hsn_sac_code);
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "Purchase Product Data.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }

    function createBatchProduct($item_id,$grn_number,$supplier_id){
        $barcode = '';

        $sup_data  = $this->general_model->getRecords('supplier_code', 'supplier', array(
        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
        'delete_status' => 0,
        'supplier_id'  => $supplier_id));
        $vendor_code = $sup_data[0]->supplier_code;

        $data  = $this->general_model->getRecords('*,count(*) num ', 'products', array(
        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
        'delete_status' => 0,
        'product_id'  => $item_id));
        $article_code = $data[0]->product_code;
        $combination_id = $data[0]->product_combination_id;

            $colour_code = '';
            $size_val = '';
            if($combination_id != ''){
                 $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                'combination_id'   => $combination_id,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                $varient_value_id = $combination_data[0]->varient_value_id;

                $sql = "SELECT V.varient_key,VV.varients_value,VV.variant_value_code FROM  varients_value VV
                JOIN varients V ON V.varients_id = VV.varients_id
                 WHERE varients_value_id IN (".$varient_value_id.")";
                 $qry = $this->db->query($sql);                    
                 $key = '';
                 if($qry->num_rows() > 0){
                    $var_lal = $qry->result_array();
                    foreach ($var_lal as $key1 => $val) {
                        $key = strtolower($val['varient_key']);
                        if($key == 'colour' || $key == 'colours' || $key == 'color'|| $key == 'colors'){
                           $colour_code = $val['variant_value_code'];
                        }

                        if($key == 'size' || $key == 'sizes'){
                               $size_val = $val['varients_value'];
                        }
                    }
                }

            }
            $barcode = $vendor_code.$grn_number.$article_code. $colour_code.$size_val;
            $product_name = $data[0]->product_name;
            $branch_id = $this->session->userdata("SESS_BRANCH_ID");
                               
                 

        $this->db->select('item_id');
        $this->db->from('purchase_item');
        $this->db->where('item_id',$item_id);
        $this->db->where('delete_status',0);
        $this->db->where('item_type','product');
        $get_pi_qry = $this->db->get();
        $ref_item_id = $get_pi_qry->result();
        $product_data = array();        
        if(!empty($ref_item_id)){ 

            $sql_purc = "SELECT COUNT(item_id) as num FROM  purchase_item
             WHERE item_id IN (SELECT product_id FROM products WHERE product_name = '".$product_name."' AND delete_status = 0 AND branch_id = '".$branch_id."') AND item_type = 'product'";
             $qry_pur = $this->db->query($sql_purc);
              $num_pur = $qry_pur->result_array();
            $num = $num_pur[0]['num'];        
            $num = (int) $num + 1;
            $batch = 'BATCH-0'.$num;
            $product_data["product_code"] = $data[0]->product_code;
            $product_data["product_name"] = $data[0]->product_name;
            $product_data["product_hsn_sac_code"] = $data[0]->product_hsn_sac_code;
            $product_data["product_category_id"] = $data[0]->product_category_id;
            $product_data["product_subcategory_id"] = $data[0]->product_subcategory_id;
            $product_data["product_quantity"] = $data[0]->product_quantity;
            $product_data["product_unit"] = $data[0]->product_unit;
            $product_data["product_price"] = $data[0]->product_price;
            $product_data["product_tds_id"] = $data[0]->product_tds_id;
            $product_data["product_tds_value"] = $data[0]->product_tds_value;
            $product_data["product_gst_id"] = $data[0]->product_gst_id;
            $product_data["product_gst_value"] = $data[0]->product_gst_value;
            $product_data["product_discount_id"] = $data[0]->product_discount_id;
            $product_data["product_details"] = $data[0]->product_details;
            $product_data["is_assets"] = $data[0]->is_assets;
            $product_data["is_varients"] = $data[0]->is_varients;
            $product_data["product_unit_id"] = $data[0]->product_unit_id;
            $product_data["product_type"] = $data[0]->product_type;
            $product_data["product_mrp_price"] = $data[0]->product_mrp_price;
            $product_data["product_selling_price"] = $data[0]->product_selling_price;
            $product_data["product_sku"] = $data[0]->product_sku;
            $product_data["product_serail_no"] = $data[0]->product_serail_no;
            $product_data["product_image"] = $data[0]->product_image;
            $product_data["added_date"] =  date('Y-m-d');
            $product_data["product_batch"] = $batch;
            $product_data['batch_parent_product_id'] = $item_id;
            $product_data["added_user_id"] = $this->session->userdata('SESS_USER_ID');
            $product_data["branch_id"] = $this->session->userdata('SESS_BRANCH_ID');
            $product_data['batch_serial'] = $data[0]->batch_serial;
            $product_data['margin_discount_value'] = $data[0]->margin_discount_value;
            $product_data['margin_discount_id'] = $data[0]->margin_discount_id;
            $product_data['product_discount_value'] = $data[0]->product_discount_value;        
            $product_data['product_basic_price'] = $data[0]->product_basic_price;
            $product_data['product_profit_margin'] = $data[0]->product_profit_margin;
            $product_data['brand_id'] = $data[0]->brand_id;
            $product_data['product_opening_quantity'] = 0;
            $product_data['packing'] = $data[0]->packing;
            $product_data['exp_date'] = $data[0]->exp_date;
            $product_data['mfg_date'] = $data[0]->mfg_date;
            $product_data['equal_unit_number'] = $data[0]->equal_unit_number;
            $product_data['equal_uom_id'] = $data[0]->equal_uom_id;
            $product_data['product_combination_id'] = $data[0]->product_combination_id;
            $product_data['product_barcode'] = $barcode;
            $product_data['GRN'] = $grn_number;

            $product_id = $this->general_model->insertData('products', $product_data);
            
        }else{  
            $update_barcode = array('product_barcode' => $barcode,'GRN' => $grn_number);
            $this->general_model->updateData('products', $update_barcode, array('product_id' => $item_id));           
          $product_id = $item_id;   
        }

        return $product_id;

    }


    function updateBarcodeProduct($item_id,$grn_number,$supplier_id){
        $barcode = '';

        $sup_data  = $this->general_model->getRecords('supplier_code', 'supplier', array(
        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
        'delete_status' => 0,
        'supplier_id'  => $supplier_id));
        $vendor_code = $sup_data[0]->supplier_code;

        $data  = $this->general_model->getRecords('*,count(*) num ', 'products', array(
        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
        'delete_status' => 0,
        'product_id'  => $item_id));
        $article_code = $data[0]->product_code;
        $combination_id = $data[0]->product_combination_id;

            $colour_code = '';
            $size_val = '';
            if($combination_id != ''){
                 $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                'combination_id'   => $combination_id,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                $varient_value_id = $combination_data[0]->varient_value_id;

                $sql = "SELECT V.varient_key,VV.varients_value,VV.variant_value_code FROM  varients_value VV
                JOIN varients V ON V.varients_id = VV.varients_id
                 WHERE varients_value_id IN (".$varient_value_id.")";
                 $qry = $this->db->query($sql);                    
                 $key = '';
                 if($qry->num_rows() > 0){
                    $var_lal = $qry->result_array();
                    foreach ($var_lal as $key1 => $val) {
                        $key = strtolower($val['varient_key']);
                        if($key == 'colour' || $key == 'colours' || $key == 'color'|| $key == 'colors'){
                           $colour_code = $val['variant_value_code'];
                        }

                        if($key == 'size' || $key == 'sizes'){
                               $size_val = $val['varients_value'];
                        }
                    }
                }

            }
            $barcode = $vendor_code.$grn_number.$article_code. $colour_code.$size_val;
        
              
            $update_barcode = array('product_barcode' => $barcode,'GRN' => $grn_number);
            $this->general_model->updateData('products', $update_barcode, array('product_id' => $item_id));           
          $product_id = $item_id;   
        

        return $product_id;

    }


}
