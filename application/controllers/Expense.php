<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expense extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }
    public function index() {
        $expense_module_id = $this->config->item('expense_module');
        $data['expense_module_id'] = $expense_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($expense_module_id, $modules, $privilege);
        $data['tds_section'] = $this->tds_section_call();
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'e.expense_id',
                1 => 'e.expense_title',
                2 => 'e.expense_hsn_code',
                3 => 'e.expense_hsn_code',
                4 => 'e.expense_description');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->expense_list_field($order, $dir);
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
                    $ledger_name = '-';
                    if($post->ledger_id){
                        $ledger_name = $this->ledger_model->getUniqLedgersName($post->ledger_id);
                    }
                    $expense_id = $this->encryption_url->encode($post->expense_id);
                    $nestedData['added_date'] = $post->added_date;
                    $nestedData['expense_title'] = $post->expense_title;
                    $nestedData['hsn'] = ($post->expense_hsn_code != '' ? $post->expense_hsn_code : '-');
                    $nestedData['expense_description'] = $post->expense_description;
                    $nestedData['ledger'] = $ledger_name;
                    $nestedData['expense_tds'] = $post->expense_tds;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;                                        
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';                    
                    $cols .= '<span data-toggle="modal" data-target="#expense_edit_modal" data-backdrop="static" data-keyboard="false"><a data-toggle="tooltip" data-placement="bottom" data-id="' . $expense_id . '"  title="Edit Expense" data-section="edit_expense" class="edit_button btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $expense_bill_item = $this->general_model->getJoinRecords('ei.*', 'expense_bill_item ei', array(
                        'ei.expense_type_id' => $post->expense_id,
                        'ei.delete_status' => 0,
                        'e.branch_id' => $this->session->userdata('SESS_BRANCH_ID')), array(
                        'expense_bill e' => 'e.expense_bill_id=ei.expense_bill_id'));
                    if ($expense_bill_item) {
                        $cols .= ' <span data-toggle="modal" data-target="#false_delete_modal" data-backdrop="static" data-keyboard="false"><a data-toggle="tooltip" data-placement="bottom" data-id="' . $expense_id . '" title="Delete Expense" class="delete_button btn btn-app"><i class="fa fa-trash"></i></a>';
                    } else {
                        $cols .= ' <span data-toggle="modal" data-target="#delete_modal" data-backdrop="static" data-keyboard="false"><a data-toggle="tooltip" data-placement="bottom" data-id="' . $expense_id . '" title="Delete Expense" data-path="expense/delete" class="delete_button btn btn-app"><i class="fa fa-trash"></i></a></span>';
                    }       
                    $cols .= '</div></div>';                   
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass">';
                                       
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $data['tax_gst'] = $this->tax_call_type('GST');
            $data['tax_tds'] = $this->tax_call_type('TDS');
            $this->load->view('expense/list', $data);
        }
    }

    public function get_expense_ajax($id) {
        $id = $this->encryption_url->decode($id);
        $expense_module_id = $this->config->item('expense_module');
        $data['module_id'] = $expense_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($expense_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $this->db->select('expense_title,e.ledger_id,e.expense_hsn_code,expense_tds,expense_tds_id,expense_tds_value,expense_description,ledger_name');
        $this->db->from('expense e');
        $this->db->join('tbl_ledgers l ','e.ledger_id = l.ledger_id','left');
        $this->db->where('e.expense_id',$id);
        $t = $this->db->get();
        $data = $t->row();
        /*$data = $this->general_model->getRecords('expense_title,ledger_id,expense_tds,expense_tds_id,expense_tds_value,expense_description', 'expense', array(
            'expense_id' => $id,
            'delete_status' => 0));*/
        echo json_encode($data);
    }

    function add_expense_ajax() {
        $expense_module_id = $this->config->item('expense_module');
        $data['module_id'] = $expense_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($expense_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $title = $this->input->post("expense_name");
        $expense_hsn_code = $this->input->post('expense_hsn_code');
        $tds = $this->input->post("expense_tds");
        $expense_tds_id = $this->input->post("expense_tds_id");
        $expense_tds_value = $this->input->post("expense_tds_value");
        $ledger_title = strtoupper(trim($title));
        $subgroup = "Expense";
        $expense_ledger = $this->config->item('expense_ledger');
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
        if(!empty($expense_ledger_name)){
            $expense_ledger_nm = $expense_ledger_name->ledger_name;
            $expense_ledger_nm = str_ireplace('{{ITEM_NAME}}',ucwords(strtolower(trim($title))) , $expense_ledger_nm);
            $EXPENSE_ary['ledger_name'] = $expense_ledger_nm;
            $EXPENSE_ary['primary_grp'] = $expense_ledger_name->sub_group_1;
            $EXPENSE_ary['second_grp'] = $expense_ledger_name->sub_group_2;
            $EXPENSE_ary['main_grp'] = $expense_ledger_name->main_group;
            $EXPENSE_ary['default_value'] = ucwords(strtolower(trim($title)));
            $EXPENSE_ary['default_ledger_id'] = $expense_ledger_name->ledger_id;
        }
        $ledger_id = $this->ledger_model->getGroupLedgerId($EXPENSE_ary);
        /*$ledger_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => ucwords(strtolower(trim($title))),
                                                'subgrp_1' => '',
                                                'subgrp_2' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' => 0
                                            ));*/
        if ($ledger_id){ 
            $expense_data = array(
                "expense_title" => $title,
                "expense_hsn_code" => $expense_hsn_code,
                'expense_description' => $this->input->post('expense_description'),
                "expense_tds" => $tds,
                "expense_tds_id" => $expense_tds_id,
                "expense_tds_value" => $expense_tds_value,
                "expense_gst_id" => $this->input->post("expense_gst_id"),
                "expense_gst_value" => $this->input->post("expense_gst_per"),
                "added_date" => date('Y-m-d'),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "updated_date" => '',
                "updated_user_id" => '',
                "ledger_id"       => $ledger_id, 
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID')
            );
        }

        $data_main = array_map('trim', $expense_data);
        $resp = array();
        if ($id = $this->general_model->insertData("expense", $data_main)) {

            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'expense',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Expense Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
                $resp['flag'] = true;
                $resp['msg'] = 'Expense Added Successfully';
        }else{
            $resp['flag'] = false;
            $resp['msg'] = 'Expense Add Unsuccessful';
        }
        /* $data['data']  = $this->general_model->getRecords("*", "expense", [
          "delete_status" => 0,
          "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
          $data['id']    = $id;
          $data['value'] = $title; */
        echo json_encode($resp);
        exit();
    }

    function edit_expense_ajax($id) {
        $id = $this->encryption_url->decode($id);
        $expense_module_id = $this->config->item('expense_module');
        $data['module_id'] = $expense_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($expense_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $title = $this->input->post("expense_name");
        $expense_hsn_code = $this->input->post('expense_hsn_code');
        $ledger_id = $this->input->post("ledger_id");
        $expense_ledger_title = $this->input->post("expense_ledger");
        $expense_name = strtoupper(trim($this->input->post('expense_name')));
        $expense_description = trim($this->input->post('expense_description'));
        if(!$expense_ledger_title) $expense_ledger_title = $expense_name;
        $expense_ledger_def = $this->config->item('expense_ledger');
        $default_expense_id = $expense_ledger_def['EXPENSE_ITEM'];
        $expense_ledger_name = $this->ledger_model->getDefaultLedgerId($default_expense_id);
            
        $EXPENSE_ary = array(
                        'ledger_name' => 'Expense Account',
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Indirect Expenses',
                        'default_value' => '',
                        'amount' => 0
                    );
        if(!empty($expense_ledger_name)){
            $expense_ledger_nm = $expense_ledger_name->ledger_name;
            $expense_ledger_nm = str_ireplace('{{ITEM_NAME}}',ucwords(strtolower(trim($expense_ledger_title))) , $expense_ledger_nm);
            $EXPENSE_ary['ledger_name'] = $expense_ledger_nm;
            $EXPENSE_ary['primary_grp'] = $expense_ledger_name->sub_group_1;
            $EXPENSE_ary['second_grp'] = $expense_ledger_name->sub_group_2;
            $EXPENSE_ary['main_grp'] = $expense_ledger_name->main_group;
            $EXPENSE_ary['default_value'] = ucwords(strtolower(trim($expense_ledger_title)));
            $EXPENSE_ary['default_ledger_id'] = $expense_ledger_name->ledger_id;
        }
        $ledger_id = $this->ledger_model->getGroupLedgerId($EXPENSE_ary);
        /*$ledger_id = $this->ledger_model->addGroupLedger(array(
                                                'ledger_name' => ucwords(strtolower(trim($expense_ledger))),
                                                'subgrp_1' => '',
                                                'subgrp_2' => '',
                                                'main_grp' => 'Indirect Expenses',
                                                'amount' => 0
                                            ));*/
        $tds = $this->input->post("expense_tds");
        $expense_tds_id = $this->input->post("expense_tds_id");
        $expense_tds_value = $this->input->post("expense_tds_value");

        /*$this->general_model->updateData('ledgers', array(
            'ledger_title' => $expense_name), array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'ledger_id' => $ledger_id));*/
        $ledger_resp = $this->db->select('ledger_id')->where('expense_id',$id)->get('expense')->row();
        $old_ledger_id  = $ledger_resp->ledger_id;

        if($old_ledger_id != $ledger_id){
            $old_bill_vouchers = $this->db->select('a.*,e.voucher_date')->join('expense_voucher e','a.expense_voucher_id = e.expense_voucher_id')->where('bill_item_id',$id)->where('a.delete_status',0)->get('accounts_expense_voucher a')->result();
            $revert_ary = array();
            
            foreach ($old_bill_vouchers as $key => $value) {
                /*$revert_ary[$value->expense_voucher_id][] = $value;*/
                /*print_r($value);*/
                $this->general_model->revertLedgerAmount(array($value),$value->voucher_date);
                $this->db->set('ledger_id',$ledger_id);
                $this->db->where('accounts_expense_id',$value->accounts_expense_id);
                $this->db->update('accounts_expense_voucher');

                $update_voucher = array();
                $update_voucher['ledger_id'] = $ledger_id;
                $update_voucher['voucher_amount'] = $value->voucher_amount;
                if($value->dr_amount > 0){
                    $update_voucher['amount_type'] = 'DR';
                }else{
                    $update_voucher['amount_type'] = 'CR';
                }
                $update_voucher['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
                /*print_r($update_voucher);*/
                $this->general_model->addBunchVoucher($update_voucher,$value->voucher_date);
                /*print_r($this->db->last_query());*/
            }
        }   

        $expense_data = array(
            "expense_title" => $title,
            "expense_hsn_code" => $expense_hsn_code,
            'ledger_id' => $ledger_id,
            "expense_description" => $expense_description,
            "expense_tds" => $tds,
            "expense_tds_id" => $expense_tds_id,
            "expense_tds_value" => $expense_tds_value,
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
        $data_main = array_map('trim', $expense_data);
        $resp = array();
        if ($id = $this->general_model->updateData("expense", $data_main, array(
            'expense_id' => $id))) {
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'expense',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Expense Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
                $resp['flag'] = true;
                $resp['msg'] = 'Expense Updated Successfully';
        }else{
            $resp['flag'] = false;
            $resp['msg'] = 'Expense Update Unsuccessful'; 
        }
        echo json_encode($resp);
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        $expense_module_id = $this->config->item('expense_module');
        $data['module_id'] = $expense_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($expense_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /*$expense = $this->general_model->getRecords('ledger_id', 'expense', array(
            'expense_id' => $id,
            'delete_status' => 0));*/
        /*$this->general_model->updateData('ledgers', array(
            'delete_status' => 1), array(
            'ledger_id' => $expense[0]->ledger_id));*/
        if ($id = $this->general_model->updateData('expense', array(
            'delete_status' => 1), array(
            'expense_id' => $id))) {
            $successMsg = 'Expense Deleted Successfully';
            $this->session->set_flashdata('expence_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'expense',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Expense Deleted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }else{
            $errorMsg = 'Expense Delete Unsuccessful';
            $this->session->set_flashdata('expence_error',$errorMsg);
            redirect("expense", 'refresh');
        }
        redirect("expense", 'refresh');
    }
}
