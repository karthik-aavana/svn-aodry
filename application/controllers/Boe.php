<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Boe extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->model('general_model');
        $this->load->model('ledger_model');
        $this->modules = $this->get_modules();
    }
    
    public function index() {
        $boe_module_id = $this->config->item('BOE_module');
        $data['boe_module_id'] = $boe_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        $access_common_settings = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');
        $data['purchase_return_module_id'] = $this->config->item('purchase_return_module');
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'voucher_number',
                2 => 'date',
                3 => 'supplier',
                4 => 'grand_total',
                5 => 'converted_grand_total',
                6 => 'paid_amount',
                7 => 'payment_status',
                8 => 'added_user');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->boe_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

              if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
            if (empty($this->input->post('search')['value'])) {
                // $list_data['limit'] = $limit;
                // $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                // $list_data['limit'] = $limit;
                // $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $boe_id = $this->encryption_url->encode($post->boe_id);
                    $col = '<input type="checkbox" name="check_boe" class="form-check-input" value="' . $boe_id . '">';
                    if(in_array($boe_module_id, $data['active_edit'])){
                        $col .= '<input type="hidden" name="edit" value="' . base_url() . 'boe/edit/' . $boe_id . '">';
                    }
                    if(in_array($boe_module_id, $data['active_view'])){
                        $col .= '<input type="hidden" name="view" value="' . base_url() . 'boe/view/' . $boe_id . '"><input type="hidden" name="pdf" value="' . base_url() . 'boe/pdf/' . $boe_id . '">';
                    }
                    if(in_array($boe_module_id, $data['active_delete'])){
                        $col .= '<input type="hidden" name="delete" value="' . $boe_id . '">';
                    }
                    $nestedData['check'] = $col;//'.base_url().'boe/delete_boe/'
                    $nestedData['date'] = date('d-m-Y', strtotime($post->boe_date));
                    $nestedData['voucher_number'] = "<a href='" . base_url() . 'boe/view/' . $boe_id . "'>" . $post->boe_number . "</a>";
                    $nestedData['reference_number'] = $post->reference_number;
                    $nestedData['net_duties'] = $this->precise_amount($post->boe_grand_total, $access_common_settings[0]->amount_precision);
                    $nestedData['bcd_amount'] = $this->precise_amount($post->boe_bcd_amount, $access_common_settings[0]->amount_precision);
                    $nestedData['igst_amount'] = $this->precise_amount($post->boe_tax_amount, $access_common_settings[0]->amount_precision);
                    $nestedData['other_duties'] = $this->precise_amount($post->boe_cess_amount, $access_common_settings[0]->amount_precision);
                    $purchase_items = $this->common->boe_purchase_invoices_field($post->boe_id);
                    $purchase_invoice = $this->general_model->getJoinRecords($purchase_items['string'], $purchase_items['table'], $purchase_items['where'], $purchase_items['join']);
                    $purchase_added = array();
                    if (!empty($purchase_invoice)) {
                        foreach ($purchase_invoice as $key => $value) {
                            array_push($purchase_added, $value->purchase_invoice_number);
                        }
                    }
                    $nestedData['purchase_invoice'] = (!empty($purchase_added) ? implode('<br>', $purchase_added) : '');

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
            $this->load->view('boe/list_boe', $data);
        }
    }
    public function add() {
        $data = $this->get_default_country_state();
        $boe_module_id = $this->config->item('BOE_module');
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /* Modules Present */
        $data['boe_module_id'] = $boe_module_id;
        $data['module_id'] = $boe_module_id;
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
        $data['supplier'] = $this->supplier_call();
        /* $data['currency'] = $this->currency_call(); */
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
            $data['chapter'] = $this->chapter_call();
            $data['hsn'] = $this->hsn_call();
            if ($data['inventory_access'] == "yes") {
                $data['get_product_inventory'] = $this->get_product_inventory();
                $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            }
        }
        $data['purchase_invoice'] = $this->general_model->getRecords('purchase_id,purchase_invoice_number', 'purchase', array('delete_status' => 0, 'purchase_type_of_supply' => 'import', 'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        $access_settings = $data['access_settings'];
        $primary_id = "boe_id";
        $table_name = 'boe';
        $date_field_name = "boe_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['bank_account'] = $this->bank_account_call_new();
        /* echo "<pre>";
          print_r($data); exit(); */
        $this->load->view('boe/add_boe', $data);
    }
    public function add_boe() {
        $data = $this->get_default_country_state();
        $boe_module_id = $this->config->item('BOE_module');
        $module_id = $boe_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /* Modules Present */
        $data['boe_module_id'] = $boe_module_id;
        $data['module_id'] = $boe_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $access_settings = $section_modules['access_settings'];
        if ($access_settings[0]->invoice_creation == "automatic") {
            $primary_id = "boe_id";
            $table_name = $this->config->item('boe_table');
            $date_field_name = "boe_date";
            $current_date = date('Y-m-d', strtotime($this->input->post('boe_date')));
            $reference_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        } else {
            $reference_number = $this->input->post('reference_number');
        }
        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;
        $boe_data = array(
            "boe_date" => date('Y-m-d', strtotime($this->input->post('boe_date'))),
            "reference_number" => $reference_number,
            "boe_number" => $this->input->post('boe_number'),
            "CIN" => $this->input->post('cin'),
            "CIN_date" => ($this->input->post('cin_date') != '' ? date('Y-m-d', strtotime($this->input->post('cin_date'))) : ''),
            "bank_name" => $this->input->post('bank_name'),
            "bank_id" => $this->input->post('bank_id'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "boe_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "boe_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "boe_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "boe_bcd_amount" => $this->input->post('total_bcd_amount') ? (float) $this->input->post('total_bcd_amount') : 0,
            "boe_other_duties_amount" => $this->input->post('total_other_duties_amount') ? (float) $this->input->post('total_other_duties_amount') : 0,
            "boe_cess_amount" => $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0,
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "updated_date" => "",
            "updated_user_id" => "",
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2')
        );

        $data_main = array_map('trim', $boe_data);
        $boe_table = $this->config->item('boe_table');
        $boe_id = $this->general_model->insertData($boe_table, $data_main);
        if ($boe_id) {
            $successMsg = 'BOE Added successfully';
            $this->session->set_flashdata('boe_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $boe_id,
                'table_name' => $boe_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'BOE Inserted');
            $data_main['boe_id'] = $boe_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $boe_item_data = $this->input->post('table_data');
            $js_data = json_decode($boe_item_data);
            $item_table = $this->config->item('boe_item_table');
            $igst_slab_items = $igst_slab = $cess_slab = $cess_slab_items = $other_slab = $other_slab_items = $bcd_slab_items = array();
            $purchases = array();
            if (!empty($this->input->post('purchase_invoice'))) {
                $purchase_invoice = $this->input->post('purchase_invoice');
                foreach ($purchase_invoice as $key => $value) {
                    $purchases[] = array('boe_id' => $boe_id, 'purchase_id' => $value);
                }
                $this->db->insert_batch('boe_purchase_tbl', $purchases);
            }
            if (!empty($js_data)) {
                $js_data1 = array();
                foreach ($js_data as $key => $value) {
                    if ($value != null && $value != '') {
                        $item_id = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $item_data = array(
                            "item_id" => $value->item_id,
                            "item_type" => $value->item_type,
                            "boe_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "boe_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "boe_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "boe_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "boe_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "boe_item_igst_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "boe_item_igst_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            "boe_item_bcd_percentage" => $value->bcd_percentage ? (float) $value->bcd_percentage : 0,
                            "boe_item_bcd_amount" => $value->item_bcd_amount ? (float) $value->item_bcd_amount : 0,
                            "boe_item_tax_other_duties_percentage" => $value->other_duties_percentage ? (float) $value->other_duties_percentage : 0,
                            "boe_item_tax_other_duties_amount" => $value->item_other_duties_amount ? (float) $value->item_other_duties_amount : 0,
                            "boe_item_tax_cess_percentage" => $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0,
                            "boe_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "boe_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            'boe_tax_cess_amount' => $value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0,
                            "boe_item_description" => (@$value->item_description ? $value->item_description : "" ),
                            "boe_id" => $boe_id
                        );

                        $boe_ledger = $this->config->item('boe_ledger');

                        if ($item_data['boe_item_bcd_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['customDuty'];
                            $bcd_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $bcd_ary = array(
                                            'ledger_name' => 'Customes Duty',
                                            'second_grp' => '',
                                            'primary_grp' => 'Sundry Creditors',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => '',
                                            'amount' => 0
                                        );
                            if(!empty($bcd_ledger_name)){
                                $bcd_ledger = $bcd_ledger_name->ledger_name;
                                $bcd_ary['ledger_name'] = $bcd_ledger;
                                $bcd_ary['primary_grp'] = $bcd_ledger_name->sub_group_1;
                                $bcd_ary['second_grp'] = $bcd_ledger_name->sub_group_2;
                                $bcd_ary['main_grp'] = $bcd_ledger_name->main_group;
                                $bcd_ary['default_ledger_id'] = $bcd_ledger_name->ledger_id;
                            }
                            $bcd_ledger = $this->ledger_model->getGroupLedgerId($bcd_ary);
                    
                            /*$bcd_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Customes Duty',
                                'subgrp_1' => 'Customes',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (array_key_exists($bcd_ledger, $bcd_slab_items)) {
                                $bcd_slab_items[$bcd_ledger] = bcadd($bcd_slab_items[$bcd_ledger], $item_data['boe_item_bcd_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $bcd_slab_items[$bcd_ledger] = $item_data['boe_item_bcd_amount'];
                            }
                        }

                        if ($item_data['boe_item_igst_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['IGST@X'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $igst_ary = array(
                                            'ledger_name' => 'IGST@' . (float)$item_data['boe_item_igst_percentage'] . '%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$item_data['boe_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $bcd_ledger = $igst_ledger_name->ledger_name;
                                $igst_ary['ledger_name'] = $bcd_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);

                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . $item_data['boe_item_igst_percentage'] . '%',
                                'subgrp_1' => 'IGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (array_key_exists($igst_tax_ledger, $igst_slab_items)) {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $item_data['boe_item_igst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $igst_slab_items[$igst_tax_ledger] = $item_data['boe_item_igst_amount'];
                            }
                        }

                        if ($item_data['boe_item_tax_cess_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $cess_ary = array(
                                            'ledger_name' => 'Output Compensation Cess @' . (float)$item_data['boe_item_tax_cess_percentage'] . '%',
                                            'second_grp' => 'Cess',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$item_data['boe_item_tax_cess_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cess_ledger_name)){
                                $bcd_ledger = $cess_ledger_name->ledger_name;
                                $cess_ary['ledger_name'] = $bcd_ledger;
                                $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                                $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                                $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                                $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                            }
                            $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);

                            /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Compensation Cess',
                                'subgrp_1' => 'Cess',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (array_key_exists($cess_tax_ledger, $cess_slab_items)) {
                                $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger], $item_data['boe_tax_cess_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $cess_slab_items[$cess_tax_ledger] = $item_data['boe_tax_cess_amount'];
                            }
                        }

                        if ($item_data['boe_item_tax_other_duties_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['OtherImp'];
                            $other_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $other_ary = array(
                                            'ledger_name' => 'Other Import Duties',
                                            'second_grp' => 'Customes',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => '',
                                            'amount' => 0
                                        );
                            if(!empty($other_ledger_name)){
                                $bcd_ledger = $other_ledger_name->ledger_name;
                                $other_ary['ledger_name'] = $bcd_ledger;
                                $other_ary['primary_grp'] = $other_ledger_name->sub_group_1;
                                $other_ary['second_grp'] = $other_ledger_name->sub_group_2;
                                $other_ary['main_grp'] = $other_ledger_name->main_group;
                                $other_ary['default_ledger_id'] = $other_ledger_name->ledger_id;
                            }
                            $other_duties_tax_ledger = $this->ledger_model->getGroupLedgerId($other_ary);
                            /*$other_duties_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Other Import Duties',
                                'subgrp_1' => 'Customes',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (array_key_exists($other_duties_tax_ledger, $other_slab_items)) {
                                $other_slab_items[$other_duties_tax_ledger] = bcadd($other_slab_items[$other_duties_tax_ledger], $item_data['boe_item_tax_other_duties_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $other_slab_items[$other_duties_tax_ledger] = $item_data['boe_item_tax_other_duties_amount'];
                            }
                        }

                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                    }
                }

                $this->db->insert_batch($item_table, $js_data1);
                $vouchers = array();

                if (!empty($bcd_slab_items)) {
                    foreach ($bcd_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if (!empty($igst_slab_items)) {
                    foreach ($igst_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if (!empty($cess_slab_items)) {
                    foreach ($cess_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if (!empty($other_slab_items)) {
                    foreach ($other_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if ($data_main['bank_name'] != '') {
                    $string             = 'ledger_id';
                    $table              = 'bank_account';
                    $where              = array('bank_account_id' => $data_main['bank_id'] );
                    $bank_data      = $this->general_model->getRecords($string , $table , $where , $order = "");
                    
                    $bank_ledger = $bank_data[0]->ledger_id;

                    /*$bank_ledger = $this->ledger_model->addGroupLedger(array(
                        'ledger_name' => $data_main['bank_name'],
                        'subgrp_1' => '',
                        'subgrp_2' => '',
                        'main_grp' => 'Current Assets',
                        'amount' => 0
                    ));*/

                    $vouchers[] = array(
                        "ledger_from" => $bank_ledger,
                        "ledger_to" => $bank_ledger,
                        "payment_voucher_id" => '',
                        "voucher_amount" => $data_main['boe_grand_total'],
                        "converted_voucher_amount" => $data_main['boe_grand_total'],
                        "dr_amount" => '',
                        "cr_amount" => $data_main['boe_grand_total'],
                        'ledger_id' => $bank_ledger
                    );
                }

                $data_main['boe_id'] = $boe_id;

                if (in_array($data['accounts_module_id'], $section_modules['active_add'])) {

                    if (in_array($data['accounts_sub_module_id'], $section_modules['access_sub_modules'])) {
                        $action = "add";
                        $this->boe_voucher_entry($data_main, $vouchers, $action, $data['branch']);
                    }
                }
            }
        } else {
            $errorMsg = 'BOE Add Unsuccessful';
            $this->session->set_flashdata('boe_error',$errorMsg);
            redirect('boe', 'refresh');
        }

        redirect('boe', 'refresh');
    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $boe_module_id = $this->config->item('BOE_module');
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['boe_module_id'] = $boe_module_id;
        $data['module_id'] = $boe_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');

        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['data'] = $this->general_model->getRecords('*', 'boe', array(
            'boe_id' => $id));

        $this->db->select('p.purchase_id,purchase_invoice_number');
        $this->db->from('purchase p');
        $this->db->join('boe_purchase_tbl b', 'p.purchase_id = b.purchase_id', 'left');
        $this->db->where('purchase_type_of_supply', 'import');
        $this->db->where('p.branch_id', $this->session->userdata('SESS_BRANCH_ID'));
        $this->db->where('b.purchase_id', null);
        $purchase = $this->db->get();
        $invoices = $purchase->result();

        $purchase_items = $this->common->boe_purchase_invoices_field($id);
        $data['purchase_invoice'] = $this->general_model->getJoinRecords($purchase_items['string'], $purchase_items['table'], $purchase_items['where'], $purchase_items['join']);
        $purchase_added = array();
        if (!empty($data['purchase_invoice'])) {
            foreach ($data['purchase_invoice'] as $key => $value) {
                array_push($purchase_added, $value->purchase_id);
            }
        }
        $data['purchase_added'] = $purchase_added;
        if (!empty($invoices))
            $data['purchase_invoice'] = array_merge($data['purchase_invoice'], $invoices);

        $boe_service_items = array();
        $boe_product_items = array();

        $service_items = $this->common->boe_items_service_list_field($id);
        $boe_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

        $product_items = $this->common->boe_items_product_list_field($id);
        $boe_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        $data['items'] = array_merge($boe_product_items, $boe_service_items);
        $data['bank_account'] = $this->bank_account_call_new();
        $this->load->view('boe/edit', $data);
    }

    public function edit_boe() {
        $data = $this->get_default_country_state();
        $boe_id = $this->input->post('boe_id');
        $boe_module_id = $this->config->item('BOE_module');
        $module_id = $boe_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['boe_module_id'] = $boe_module_id;
        $data['module_id'] = $boe_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');
        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
        $access_settings = $section_modules['access_settings'];

        $total_cess_amnt = $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0;

        $boe_data = array(
            "boe_date" => date('Y-m-d', strtotime($this->input->post('boe_date'))),
            "boe_number" => $this->input->post('boe_number'),
            "CIN" => $this->input->post('cin'),
            "CIN_date" => ($this->input->post('cin_date') != '' ? date('Y-m-d', strtotime($this->input->post('cin_date'))) : ''),
            "bank_name" => $this->input->post('bank_name'),
            "bank_id" => $this->input->post('bank_id'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "boe_sub_total" => (float) $this->input->post('total_sub_total') ? (float) $this->input->post('total_sub_total') : 0,
            "boe_grand_total" => $this->input->post('total_grand_total') ? (float) $this->input->post('total_grand_total') : 0,
            "boe_tax_amount" => $this->input->post('total_tax_amount') ? (float) $this->input->post('total_tax_amount') : 0,
            "boe_bcd_amount" => $this->input->post('total_bcd_amount') ? (float) $this->input->post('total_bcd_amount') : 0,
            "boe_other_duties_amount" => $this->input->post('total_other_duties_amount') ? (float) $this->input->post('total_other_duties_amount') : 0,
            "boe_cess_amount" => $this->input->post('total_tax_cess_amount') ? (float) $this->input->post('total_tax_cess_amount') : 0,
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "note1" => $this->input->post('note1'),
            "note2" => $this->input->post('note2')
        );

        $data_main = array_map('trim', $boe_data);
        $boe_table = $this->config->item('boe_table');
        $where = array('boe_id' => $boe_id);

        if ($this->general_model->updateData($boe_table, $data_main, $where)) {
            $successMsg = 'BOE Updated successfully';
            $this->session->set_flashdata('boe_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $boe_id,
                'table_name' => $boe_table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'BOE Updated');
            $data_main['boe_id'] = $boe_id;
            $log_table = $this->config->item('log_table');
            $this->general_model->insertData($log_table, $log_data);
            $boe_item_data = $this->input->post('table_data');
            $js_data = json_decode($boe_item_data);
            $item_table = $this->config->item('boe_item_table');

            $igst_slab_items = $igst_slab = $cess_slab = $cess_slab_items = $other_slab_items = $bcd_slab_items = array();
            $purchases = array();
            $this->db->where('boe_id', $boe_id);
            $this->db->delete('boe_purchase_tbl');
            if (!empty($this->input->post('purchase_invoice'))) {
                $purchase_invoice = $this->input->post('purchase_invoice');

                foreach ($purchase_invoice as $key => $value) {
                    $purchases[] = array('boe_id' => $boe_id, 'purchase_id' => $value);
                }
                $this->db->insert_batch('boe_purchase_tbl', $purchases);
            }

            if (!empty($js_data)) {
                $js_data1 = array();

                $this->db->where('boe_id', $boe_id);
                $this->db->delete($item_table);

                foreach ($js_data as $key => $value) {
                    if ($value != null && $value != '') {
                        $item_id = $value->item_id;
                        $item_type = $value->item_type;
                        $quantity = $value->item_quantity;
                        $item_data = array(
                            "item_id" => $value->item_id,
                            "item_type" => $value->item_type,
                            "boe_item_quantity" => $value->item_quantity ? (float) $value->item_quantity : 0,
                            "boe_item_unit_price" => $value->item_price ? (float) $value->item_price : 0,
                            "boe_item_sub_total" => $value->item_sub_total ? (float) $value->item_sub_total : 0,
                            "boe_item_taxable_value" => $value->item_taxable_value ? (float) $value->item_taxable_value : 0,
                            "boe_item_grand_total" => $value->item_grand_total ? (float) $value->item_grand_total : 0,
                            "boe_item_igst_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "boe_item_igst_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            "boe_item_bcd_percentage" => $value->bcd_percentage ? (float) $value->bcd_percentage : 0,
                            "boe_item_bcd_amount" => $value->item_bcd_amount ? (float) $value->item_bcd_amount : 0,
                            "boe_item_tax_cess_percentage" => $value->item_tax_cess_percentage ? (float) $value->item_tax_cess_percentage : 0,
                            "boe_tax_percentage" => $value->item_tax_percentage ? (float) $value->item_tax_percentage : 0,
                            "boe_item_tax_amount" => $value->item_tax_amount ? (float) $value->item_tax_amount : 0,
                            'boe_tax_cess_amount' => $value->item_tax_cess_amount ? (float) $value->item_tax_cess_amount : 0,
                            "boe_item_tax_other_duties_percentage" => $value->other_duties_percentage ? (float) $value->other_duties_percentage : 0,
                            "boe_item_tax_other_duties_amount" => $value->item_other_duties_amount ? (float) $value->item_other_duties_amount : 0,
                            "boe_item_description" => (@$value->item_description ? $value->item_description : "" ),
                            "boe_id" => $boe_id
                        );

                        $boe_ledger = $this->config->item('boe_ledger');

                        if ($item_data['boe_item_bcd_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['customDuty'];
                            $bcd_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $bcd_ary = array(
                                            'ledger_name' => 'Customes Duty',
                                            'second_grp' => '',
                                            'primary_grp' => 'Sundry Creditors',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => '',
                                            'amount' => 0
                                        );
                            if(!empty($bcd_ledger_name)){
                                $bcd_ledger = $bcd_ledger_name->ledger_name;
                                $bcd_ary['ledger_name'] = $bcd_ledger;
                                $bcd_ary['primary_grp'] = $bcd_ledger_name->sub_group_1;
                                $bcd_ary['second_grp'] = $bcd_ledger_name->sub_group_2;
                                $bcd_ary['main_grp'] = $bcd_ledger_name->main_group;
                                $bcd_ary['default_ledger_id'] = $bcd_ledger_name->ledger_id;
                            }
                            $bcd_ledger = $this->ledger_model->getGroupLedgerId($bcd_ary);
                            /*$bcd_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Customes Duty',
                                'subgrp_1' => 'Customes',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (array_key_exists($bcd_ledger, $bcd_slab_items)) {
                                $bcd_slab_items[$bcd_ledger] = bcadd($bcd_slab_items[$bcd_ledger], $item_data['boe_item_bcd_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $bcd_slab_items[$bcd_ledger] = $item_data['boe_item_bcd_amount'];
                            }
                        }

                        if ($item_data['boe_item_igst_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['IGST@X'];
                            $igst_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $igst_ary = array(
                                            'ledger_name' => 'IGST@' . (float)$item_data['boe_item_igst_percentage'] . '%',
                                            'second_grp' => 'IGST',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$item_data['boe_item_igst_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($igst_ledger_name)){
                                $bcd_ledger = $igst_ledger_name->ledger_name;
                                $igst_ary['ledger_name'] = $bcd_ledger;
                                $igst_ary['primary_grp'] = $igst_ledger_name->sub_group_1;
                                $igst_ary['second_grp'] = $igst_ledger_name->sub_group_2;
                                $igst_ary['main_grp'] = $igst_ledger_name->main_group;
                                $igst_ary['default_ledger_id'] = $igst_ledger_name->ledger_id;
                            }
                            $igst_tax_ledger = $this->ledger_model->getGroupLedgerId($igst_ary);
                            /*$igst_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'IGST@' . $item_data['boe_item_igst_percentage'] . '%',
                                'subgrp_1' => 'IGST',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (array_key_exists($igst_tax_ledger, $igst_slab_items)) {
                                $igst_slab_items[$igst_tax_ledger] = bcadd($igst_slab_items[$igst_tax_ledger], $item_data['boe_item_igst_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $igst_slab_items[$igst_tax_ledger] = $item_data['boe_item_igst_amount'];
                            }
                        }

                        if ($item_data['boe_item_tax_cess_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['CESS@X'];
                            $cess_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $cess_ary = array(
                                            'ledger_name' => 'Output Compensation Cess @' . (float)$item_data['boe_item_tax_cess_percentage'] . '%',
                                            'second_grp' => 'Cess',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => (float)$item_data['boe_item_tax_cess_percentage'],
                                            'amount' => 0
                                        );
                            if(!empty($cess_ledger_name)){
                                $bcd_ledger = $cess_ledger_name->ledger_name;
                                $cess_ary['ledger_name'] = $bcd_ledger;
                                $cess_ary['primary_grp'] = $cess_ledger_name->sub_group_1;
                                $cess_ary['second_grp'] = $cess_ledger_name->sub_group_2;
                                $cess_ary['main_grp'] = $cess_ledger_name->main_group;
                                $cess_ary['default_ledger_id'] = $cess_ledger_name->ledger_id;
                            }
                            $cess_tax_ledger = $this->ledger_model->getGroupLedgerId($cess_ary);
                            /*$cess_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Compensation Cess',
                                'subgrp_1' => 'Cess',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/

                            if (array_key_exists($cess_tax_ledger, $cess_slab_items)) {
                                $cess_slab_items[$cess_tax_ledger] = bcadd($cess_slab_items[$cess_tax_ledger], $item_data['boe_tax_cess_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $cess_slab_items[$cess_tax_ledger] = $item_data['boe_tax_cess_amount'];
                            }
                        }

                        if ($item_data['boe_item_tax_other_duties_percentage'] > 0) {
                            $bcd_ledger = $boe_ledger['OtherImp'];
                            $other_ledger_name = $this->ledger_model->getDefaultLedgerId($bcd_ledger);
                                
                            $other_ary = array(
                                            'ledger_name' => 'Other Import Duties',
                                            'second_grp' => 'Customes',
                                            'primary_grp' => 'Duties and taxes',
                                            'main_grp' => 'Current Liabilities',
                                            'default_ledger_id' => 0,
                                            'default_value' => '',
                                            'amount' => 0
                                        );
                            if(!empty($other_ledger_name)){
                                $bcd_ledger = $other_ledger_name->ledger_name;
                                $other_ary['ledger_name'] = $bcd_ledger;
                                $other_ary['primary_grp'] = $other_ledger_name->sub_group_1;
                                $other_ary['second_grp'] = $other_ledger_name->sub_group_2;
                                $other_ary['main_grp'] = $other_ledger_name->main_group;
                                $other_ary['default_ledger_id'] = $other_ledger_name->ledger_id;
                            }
                            $other_duties_tax_ledger = $this->ledger_model->getGroupLedgerId($other_ary);
                            /*$other_duties_tax_ledger = $this->ledger_model->addGroupLedger(array(
                                'ledger_name' => 'Other Import Duties',
                                'subgrp_1' => 'Customes',
                                'subgrp_2' => 'Duties and taxes',
                                'main_grp' => 'Current Liabilities',
                                'amount' => 0
                            ));*/
                            if (array_key_exists($other_duties_tax_ledger, $other_slab_items)) {
                                $other_slab_items[$other_duties_tax_ledger] = bcadd($other_slab_items[$other_duties_tax_ledger], $item_data['boe_item_tax_other_duties_amount'], $section_modules['access_common_settings'][0]->amount_precision);
                            } else {
                                $other_slab_items[$other_duties_tax_ledger] = $item_data['boe_item_tax_other_duties_amount'];
                            }
                        }

                        $data_item = array_map('trim', $item_data);
                        $js_data1[] = $data_item;
                    }
                }

                $this->db->insert_batch($item_table, $js_data1);
                $vouchers = array();

                if (!empty($bcd_slab_items)) {
                    foreach ($bcd_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if (!empty($igst_slab_items)) {
                    foreach ($igst_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if (!empty($cess_slab_items)) {
                    foreach ($cess_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if (!empty($other_slab_items)) {
                    foreach ($other_slab_items as $key => $value) {

                        $vouchers[] = array(
                            "ledger_from" => $key,
                            "ledger_to" => $boe_id,
                            "payment_voucher_id" => '',
                            "voucher_amount" => $value,
                            "converted_voucher_amount" => $value,
                            "dr_amount" => $value,
                            "cr_amount" => '',
                            'ledger_id' => $key
                        );
                    }
                }

                if ($data_main['bank_name'] != '') {
                    $string             = 'ledger_id';
                    $table              = 'bank_account';
                    $where              = array('bank_account_id' => $data_main['bank_id'] );
                    $bank_data      = $this->general_model->getRecords($string , $table , $where , $order = "");
                    
                    $bank_ledger = $bank_data[0]->ledger_id;
                    /*$bank_ledger = $this->ledger_model->addGroupLedger(array(
                        'ledger_name' => $data_main['bank_name'],
                        'subgrp_1' => '',
                        'subgrp_2' => '',
                        'main_grp' => 'Current Assets',
                        'amount' => 0
                    ));*/

                    $vouchers[] = array(
                        "ledger_from" => $bank_ledger,
                        "ledger_to" => $bank_ledger,
                        "payment_voucher_id" => '',
                        "voucher_amount" => $data_main['boe_grand_total'],
                        "converted_voucher_amount" => $data_main['boe_grand_total'],
                        "dr_amount" => '',
                        "cr_amount" => $data_main['boe_grand_total'],
                        'ledger_id' => $bank_ledger
                    );
                }

                $data_main['boe_id'] = $boe_id;

                if (in_array($data['accounts_module_id'], $section_modules['active_add'])) {

                    if (in_array($data['accounts_sub_module_id'], $section_modules['access_sub_modules'])) {
                        $action = "edit";
                        $this->boe_voucher_entry($data_main, $vouchers, $action, $data['branch']);
                    }
                }
            }
        } else {
            $errorMsg = 'BOE Update Unsuccessful';
            $this->session->set_flashdata('boe_error',$errorMsg);
            redirect('boe', 'refresh');
        }
        redirect('boe', 'refresh');
    }

    public function boe_voucher_entry($data_main, $vouchers, $action, $branch) {
        $purchase_voucher_module_id = $this->config->item('payment_voucher_module');
        $module_id = $purchase_voucher_module_id;
        $modules = $this->get_modules();
        $privilege = "add_privilege";
        $section_modules = $this->get_section_modules($purchase_voucher_module_id, $modules, $privilege);

        $access_sub_modules = $section_modules['access_sub_modules'];
        $charges_sub_module_id = $this->config->item('charges_sub_module');
        $access_settings = $section_modules['access_settings'];
        $grand_total = $data_main['boe_grand_total'];

        $table = 'payment_voucher';
        $reference_key = 'payment_voucher_id';
        $reference_table = 'accounts_payment_voucher';

        if ($action == "add") {
            /* generated voucher number */
            $primary_id = "payment_id";
            $table_name = $this->config->item('payment_voucher_table');
            $date_field_name = "voucher_date";
            $current_date = $data_main['boe_date'];
            $voucher_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

            $headers = array(
                "voucher_date" => $data_main['boe_date'],
                "voucher_number" => $voucher_number . '-BOE',
                "party_id" => '',
                "party_type" => '',
                "reference_id" => $data_main['boe_id'],
                "reference_type" => 'boe',
                "reference_number" => $data_main['reference_number'],
                "receipt_amount" => $grand_total,
                "from_account" => '',
                "to_account" => $data_main['bank_name'],
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "description" => '',
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "currency_id" => $this->session->userdata('SESS_DEFAULT_CURRENCY'),
                "note1" => $data_main['note1'],
                "note2" => $data_main['note2']
            );

            $headers['converted_receipt_amount'] = 0;
            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                $headers['converted_receipt_amount'] = $grand_total;
            }
            $this->general_model->addVouchers($table, $reference_key, $reference_table, $headers, $vouchers);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => $table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Voucher Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);

        } else if ($action == "edit") {
            $headers = array(
                "voucher_date" => $data_main['boe_date'],
                "receipt_amount" => $grand_total,
                "financial_year_id" => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "description" => '',
                "currency_id" => $this->session->userdata('SESS_DEFAULT_CURRENCY'),
                "updated_date" => date('Y-m-d'),
                "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                "note1" => $data_main['note1'],
                "note2" => $data_main['note2']
            );

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                $headers['converted_receipt_amount'] = $grand_total;
            } else {
                $headers['converted_receipt_amount'] = 0;
            }

            $boe_voucher_data = $this->general_model->getRecords('payment_id', 'payment_voucher', array('reference_id' => $data_main['boe_id'], 'reference_type' => 'boe', 'delete_status' => 0));

            if ($boe_voucher_data) {
                $payment_id = $boe_voucher_data[0]->payment_id;
                $this->general_model->updateData('payment_voucher', $headers, array('payment_id' => $payment_id));
                $string = 'accounts_payment_id,delete_status,ledger_id,voucher_amount,dr_amount,cr_amount';
                $table = 'accounts_payment_voucher';
                $where = array('payment_voucher_id' => $payment_id);

                $old_boe_voucher_items = $this->general_model->getRecords($string, $table, $where, $order = "");
                $old_boe_ledger_ids = $this->getValues($old_boe_voucher_items, 'ledger_id');
                $not_deleted_ids = array();

                foreach ($vouchers as $key => $value) {
                    if (($led_key = array_search($value['ledger_id'], $old_boe_ledger_ids)) !== false) {
                        unset($old_boe_ledger_ids[$led_key]);
                        $accounts_payment_id = $old_boe_voucher_items[$led_key]->accounts_payment_id;
                        array_push($not_deleted_ids, $accounts_payment_id);
                        $value['payment_voucher_id'] = $payment_id;
                        $value['delete_status'] = 0;
                        $table = 'accounts_payment_voucher';
                        $where = array('accounts_payment_id' => $accounts_payment_id);
                        $post_data = array('data' => $value,
                            'where' => $where,
                            'voucher_date' => $headers['voucher_date'],
                            'table' => 'payment_voucher',
                            'sub_table' => 'accounts_payment_voucher',
                            'primary_id' => 'payment_id',
                            'sub_primary_id' => 'payment_voucher_id'
                        );
                        $this->general_model->updateBunchVoucherCommon($post_data);
                        $this->general_model->updateData($table, $value, $where);
                    } else {
                        $value['payment_voucher_id'] = $payment_id;
                        $table = 'accounts_payment_voucher';
                        $this->general_model->insertData($table, $value);
                    }
                }

                if (!empty($old_boe_voucher_items)) {
                    $revert_ary = array();
                    foreach ($old_boe_voucher_items as $key => $value) {
                        if (!in_array($value->accounts_payment_id, $not_deleted_ids)) {
                            $revert_ary[] = $value;
                            $table = 'accounts_payment_voucher';
                            $where = array('accounts_payment_id' => $value->accounts_payment_id);
                            $purchase_data = array('delete_status' => 1);
                            $this->general_model->updateData($table, $purchase_data, $where);
                        }
                    }
                    if (!empty($revert_ary))
                        $this->general_model->revertLedgerAmount($revert_ary, $headers['voucher_date']);
                }
            }
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => $table,
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Voucher Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
    }

    public function delete_boe() {
        $id = $this->input->post('delete_id');
        $boe_id = $this->encryption_url->decode($id);
        $item_table = $this->config->item('boe_item_table');

        $boe_module_id = $this->config->item('BOE_module');
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* delete Items */
        $this->db->where('boe_id', $boe_id);
        $this->db->delete($item_table);

        /* delete purchases invoices */
        $this->db->where('boe_id', $boe_id);
        $this->db->delete('boe_purchase_tbl');

        /* delete voucher from payment table */
        $payment_id = $this->general_model->getRecords('payment_id', 'payment_voucher', array(
            'reference_id' => $boe_id, 'reference_type' => 'boe'));

        if (!empty($payment_id)) {
            $this->general_model->deleteCommonVoucher(array('table' => 'payment_voucher', 'where' => array('payment_id' => $payment_id[0]->payment_id)), array('table' => 'accounts_payment_voucher', 'where' => array('payment_voucher_id' => $payment_id[0]->payment_id)));
        }

        $this->general_model->updateData('boe', array(
            'delete_status' => 1), array(
            'boe_id' => $boe_id));
        $successMsg = 'BOE Deleted successfully';
        $this->session->set_flashdata('boe_success',$successMsg);
        $log_data = array(
            'user_id' => $this->session->userdata('SESS_USER_ID'),
            'table_id' => $boe_id,
            'table_name' => 'boe',
            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            'message' => 'Boe Deleted');

        $this->general_model->insertData('log', $log_data);
        $redirect = 'boe';
        if ($this->input->post('delete_redirect') != '')
            $redirect = $this->input->post('delete_redirect');
        redirect($redirect, 'refresh');
    }

    public function view($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $boe_module_id = $this->config->item('BOE_module');
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /* Modules Present */
        $data['boe_module_id'] = $boe_module_id;
        $data['module_id'] = $boe_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');

        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['data'] = $this->general_model->getRecords('*', 'boe', array(
            'boe_id' => $id));

        $this->db->select('p.purchase_id,purchase_invoice_number');
        $this->db->from('purchase p');
        $this->db->join('boe_purchase_tbl b', 'p.purchase_id = b.purchase_id', 'left');
        $this->db->where('purchase_type_of_supply', 'import');
        $this->db->where('p.branch_id', $this->session->userdata('SESS_BRANCH_ID'));
        $this->db->where('b.purchase_id', null);
        $purchase = $this->db->get();
        $invoices = $purchase->result();

        $purchase_items = $this->common->boe_purchase_invoices_field($id);
        $data['purchase_invoice'] = $this->general_model->getJoinRecords($purchase_items['string'], $purchase_items['table'], $purchase_items['where'], $purchase_items['join']);
        $purchase_added = array();
        if (!empty($data['purchase_invoice'])) {
            foreach ($data['purchase_invoice'] as $key => $value) {
                array_push($purchase_added, $value->purchase_id);
            }
        }
        $data['purchase_added'] = $purchase_added;
        if (!empty($invoices))
            $data['purchase_invoice'] = array_merge($data['purchase_invoice'], $invoices);

        $boe_service_items = array();
        $boe_product_items = array();

        $service_items = $this->common->boe_items_service_list_field($id);
        $boe_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

        $product_items = $this->common->boe_items_product_list_field($id);
        $boe_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        $data['items'] = array_merge($boe_product_items, $boe_service_items);
        $data['bank_account'] = $this->bank_account_call();

        $bcdExist = 0;
        $igstExist = 0;
        $cess_exist = 0;
        $other_duties = 0;

        if ($data['data'][0]->boe_tax_amount > 0) {
            /* igst tax slab */
            $igstExist = 1;
        }
        if ($data['data'][0]->boe_cess_amount > 0) {
            $cess_exist = 1;
        }
        if ($data['data'][0]->boe_bcd_amount > 0) {
            $bcdExist = 1;
        }
        if ($data['data'][0]->boe_other_duties_amount > 0) {
            $other_duties = 1;
        }

        $data['igst_exist'] = $igstExist;
        $data['cess_exist'] = $cess_exist;
        $data['other_duties'] = $other_duties;
        $data['bcd_exist'] = $bcdExist;
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code'] = $currency[0]->currency_code;
        $data['currency_symbol'] = $currency[0]->currency_symbol;
        $this->load->view('boe/view', $data);
    }

    public function pdf($id) {
        $id = $this->encryption_url->decode($id);
        $data = $this->get_default_country_state();
        $branch_data = $this->common->branch_field();
        $data['branch'] = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $boe_module_id = $this->config->item('BOE_module');
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($boe_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);
        /* Modules Present */
        $data['boe_module_id'] = $boe_module_id;
        $data['module_id'] = $boe_module_id;
        $data['notes_module_id'] = $this->config->item('notes_module');
        $data['product_module_id'] = $this->config->item('product_module');
        $data['service_module_id'] = $this->config->item('service_module');

        $data['category_module_id'] = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id'] = $this->config->item('tax_module');
        $data['accounts_module_id'] = $this->config->item('accounts_module');
        /* Sub Modules Present */
        $data['notes_sub_module_id'] = $this->config->item('notes_sub_module');
        $data['transporter_sub_module_id'] = $this->config->item('transporter_sub_module');
        $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');

        $data['data'] = $this->general_model->getRecords('*', 'boe', array(
            'boe_id' => $id));

        $this->db->select('p.purchase_id,purchase_invoice_number');
        $this->db->from('purchase p');
        $this->db->join('boe_purchase_tbl b', 'p.purchase_id = b.purchase_id', 'left');
        $this->db->where('p.branch_id', $this->session->userdata('SESS_BRANCH_ID'));
        $this->db->where('purchase_type_of_supply', 'import');
        $this->db->where('b.purchase_id', null);
        $purchase = $this->db->get();
        $invoices = $purchase->result();

        $purchase_items = $this->common->boe_purchase_invoices_field($id);
        $data['purchase_invoice'] = $this->general_model->getJoinRecords($purchase_items['string'], $purchase_items['table'], $purchase_items['where'], $purchase_items['join']);
        $purchase_added = array();
        if (!empty($data['purchase_invoice'])) {
            foreach ($data['purchase_invoice'] as $key => $value) {
                array_push($purchase_added, $value->purchase_id);
            }
        }
        $data['purchase_added'] = $purchase_added;
        if (!empty($invoices))
            $data['purchase_invoice'] = array_merge($data['purchase_invoice'], $invoices);

        $boe_service_items = array();
        $boe_product_items = array();

        $service_items = $this->common->boe_items_service_list_field($id);
        $boe_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);

        $product_items = $this->common->boe_items_product_list_field($id);
        $boe_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        $data['items'] = array_merge($boe_product_items, $boe_service_items);
        $data['bank_account'] = $this->bank_account_call();

        $bcdExist = 0;
        $igstExist = 0;
        $cess_exist = 0;
        $other_duties = 0;
        if ($data['data'][0]->boe_tax_amount > 0) {
            /* igst tax slab */
            $igstExist = 1;
        }
        if ($data['data'][0]->boe_cess_amount > 0) {
            $cess_exist = 1;
        }
        if ($data['data'][0]->boe_bcd_amount > 0) {
            $bcdExist = 1;
        }
        if ($data['data'][0]->boe_other_duties_amount > 0) {
            $other_duties = 1;
        }

        $data['igst_exist'] = $igstExist;
        $data['cess_exist'] = $cess_exist;
        $data['other_duties'] = $other_duties;
        $data['bcd_exist'] = $bcdExist;
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
        $note_data                     = $this->template_note($data['data'][0]->note1 , $data['data'][0]->note2);
        $data['note1']                 = $note_data['note1'];
        $data['template1']             = $note_data['template1'];
        $data['note2']                 = $note_data['note2'];
        $data['template2']             = $note_data['template2'];

        $html = $this->load->view('boe/pdf', $data, true);

        include APPPATH . "third_party/dompdf/autoload.inc.php";
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();

        $dompdf->load_html($html);

        $paper_size = 'a4';
        $orientation = 'portrait';
        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size, $orientation);

        //and getting rend
        $dompdf->render();

        $dompdf->stream($data['data'][0]->reference_number, array(
            'Attachment' => 0));
    }

}
