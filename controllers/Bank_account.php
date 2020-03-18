<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_account extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index(){
        $bank_account_module_id          = $this->config->item('bank_account_module');
        $data['bank_account_module_id']  = $bank_account_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $ledger_title = 'Acc@{{BANK}}';
        if(!empty($bank_led)){
            $ledger_title = $bank_led->ledger_name;
        }
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['default_ledger_title'] = $ledger_title;

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'short_name',
                    1 => 'account_type',
                    2 => 'account_no',
                    3 => 'bank_name',
                    4 => 'bank_address',
                    5 => 'added_user_id',
                    6 => 'action',
            );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->bank_account_list_field();
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
                    $bank_account_id            = $this->encryption_url->encode($post->bank_account_id);
                    $nestedData['short_name']   = $post->account_holder;
                    $nestedData['account_type'] = $post->account_type;
                    $nestedData['account_no']   = $post->account_no;
                    $nestedData['bank_name']    = $post->bank_name;
                    $nestedData['bank_address'] = $post->bank_address;
                    $nestedData['added_user']   = $post->first_name . ' ' . $post->last_name;
                    
                    
                     $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    
                    $cols.= '<span data-toggle="modal" data-target="#edit_bank_modal" data-backdrop="static" data-keyboard="false"><a data-id="' . $bank_account_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_bank btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $advance_voucher            = $this->general_model->getRecords('*', 'advance_voucher', array(
                            'payment_mode'  => $post->bank_account_id,
                            'delete_status' => 0,
                            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                    $refund_voucher             = $this->general_model->getRecords('*', 'refund_voucher', array(
                            'payment_mode'  => $post->bank_account_id,
                            'delete_status' => 0,
                            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                    $receipt_voucher            = $this->general_model->getRecords('*', 'receipt_voucher', array(
                            'payment_mode'  => $post->bank_account_id,
                            'delete_status' => 0,
                            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                    $payment_voucher            = $this->general_model->getRecords('*', 'payment_voucher', array(
                            'payment_mode'  => $post->bank_account_id,
                            'delete_status' => 0,
                            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                    if ($advance_voucher || $refund_voucher || $receipt_voucher || $payment_voucher)
                    {
                        $cols .= '
                       <span data-toggle="modal" data-target="#false_delete_modal" data-backdrop="static" data-keyboard="false"><a title="Delete" class="btn btn-app"><i class="fa fa-trash-o"></i></a></span>';
                    }
                    else
                    {
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" ><a data-id="' . $bank_account_id . '" data-path="bank_account/delete" class="delete_button btn btn-app" href="#" data-toggle="tooltip" data-placement="bottom" title="Delete" ><i class="fa fa-trash-o"></i></a></span>';
                    }
                    $cols .= '</div></div>';
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]          = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data
            );
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('bank_account/list', $data);
        }
    }

    public function add(){
        $data                            = $this->get_default_country_state();
        $bank_account_module_id          = $this->config->item('bank_account_module');
        $data['module_id']               = $bank_account_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view('bank_account/add', $data);
    }

    public function get_bank_account(){
        $bank_account_data = $this->common->bank_account_field('bank_account');
        $data              = $this->general_model->getJoinRecords($bank_account_data['string'], $bank_account_data['table'], $bank_account_data['where'], $bank_account_data['join'], $bank_account_data['order']);
        echo json_encode($data);
    }

    public function get_bank_account_number_count(){
        $account_number = $this->input->post('account_number');
        $string            = 'COUNT(`account_no`) as account_count';
        $table             = 'bank_account';
        $where             = array('account_no' => $account_number );
        $data = $this->general_model->getJoinRecords($string, $table, $where, $join = "", $order = "");
        echo json_encode($data);
    }

    public function add_bank_account(){
        $bank_account_module_id          = $this->config->item('bank_account_module');
        $data['module_id']               = $bank_account_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $country                         = $this->input->post('country');
        $state                           = $this->input->post('state');
        $title                           = strtoupper(trim($this->input->post('ledger_title')));

        /*** Create the ledger added by karthik 10-08-2019 *********/
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $bank_ary = array(
                        'ledger_name' => $title,
                        'second_grp' => '',
                        'primary_grp' => 'Cash & Cash Equivalent',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => 0,
                        'default_value' => $title,
                        'amount' => 0
                    );
        if(!empty($bank_led)){
            $bnk_ledger = $title;
            $bank_ary['ledger_name'] = $bnk_ledger;
            $bank_ary['primary_grp'] = $bank_led->sub_group_1;
            $bank_ary['second_grp'] = $bank_led->sub_group_2;
            $bank_ary['main_grp'] = $bank_led->main_group;
            $bank_ary['default_ledger_id'] = $bank_led->ledger_id;
        }

        $ledger_id = $this->ledger_model->getGroupLedgerId($bank_ary);
        /*$ledger_id = $this->ledger_model->addGroupLedger(array('ledger_name' => 'Acc@'.$title,
                                                                        'subgrp_1' => $subgroup,
                                                                        'subgrp_2' => '',
                                                                        'main_grp' => 'Current Assets',
                                                                        'amount' => 0
                                                                    ));*/
         //$this->ledger_model->addLedger($title, $subgroup)
        $resp = array();                                                            
        if ($ledger_id){
            $account_number = $this->input->post('account_number');
            $string            = 'COUNT(`account_no`) as account_count';
            $table             = 'bank_account';
            $where             = array('account_no' => $account_number );
            $data = $this->general_model->getJoinRecords($string, $table, $where, $join = "", $order = "");
            if($data[0]->account_count == 0){
                $data  = array(
                        'short_name'      => $this->input->post('short_name'),
                        'account_holder'  => $this->input->post('account_holder'),
                        'account_type'    => $this->input->post('type'),
                        'account_no'      => $this->input->post('account_number'),
                        'bank_name'       => $this->input->post('bank_name1'),
                        'bank_address'    => $this->input->post('bank_address'),
                        'opening_balance' => $this->input->post('balance'),
                        'branch_name'     => $this->input->post('branch_name'),
                        'ifsc_code'       => $this->input->post('ifsc_code'),
                        'default_account' => $this->input->post('default'),
                        'ledger_id'       => $ledger_id,
                        "added_date"      => date('Y-m-d'),
                        "ledger_title"    => $title,
                        "added_user_id"   => $this->session->userdata('SESS_USER_ID'),
                        "branch_id"       => $this->session->userdata('SESS_BRANCH_ID'),
                        "updated_date"    => "",
                        "updated_user_id" => ""
                );
                $table = "bank_account";
                if ($id    = $this->general_model->insertData($table, $data)){
                    $resp['flag'] = true;
                    $resp['type'] = 'success';
                    $resp['msg'] = 'Bank Account Added Successfully';
                    $table    = "log";
                    $log_data = array(
                            'user_id'           => $this->session->userdata('SESS_USER_ID'),
                            'table_id'          => $id,
                            'table_name'        => 'bank_account',
                            'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                            'message'           => 'Bank Account Inserted'
                    );
                    $this->general_model->insertData($table, $log_data);
                }else{
                    $resp['flag'] = false;
                    $resp['msg'] = 'Bank Account Add Unsuccessful';
                }
            }else {
                $resp['flag'] = false;
                $resp['msg'] = 'duplicate';
            }
        } else{
            $resp['flag'] = false;
            $resp['msg'] = 'Something Went Wrong';
        }
        echo json_encode($resp);
    }

    public function add_bank_account_ajax(){
        $bank_account_module_id = $this->config->item('bank_account_module');
        $data['module_id'] = $bank_account_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $title = strtoupper(trim($this->input->post('ledger_title')));
        $subgroup = "Bank";
        $bank_ledger = $this->config->item('bank_ledger');
        $default_bank_id = $bank_ledger['bank'];
        $bank_led = $this->ledger_model->getDefaultLedgerId($default_bank_id);
        $bank_ary = array(
                        'ledger_name' => $title,
                        'second_grp' => '',
                        'primary_grp' => 'Cash & Cash Equivalent',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => 0,
                        'default_value' => $title,
                        'amount' => 0
                    );
        if(!empty($bank_led)){
            $bnk_ledger = $title;
            $bank_ary['ledger_name'] = $bnk_ledger;
            $bank_ary['primary_grp'] = $bank_led->sub_group_1;
            $bank_ary['second_grp'] = $bank_led->sub_group_2;
            $bank_ary['main_grp'] = $bank_led->main_group;
            $bank_ary['default_ledger_id'] = $bank_led->ledger_id;
        }
        /*$cgst_tax_ledger = $this->ledger_model->getGroupLedgerId($bank_ary);*/
        $ledger_id = $this->ledger_model->getGroupLedgerId($bank_ary);
        /*$ledger_id = $this->ledger_model->addGroupLedger(array('ledger_name' => 'Acc@'.$title,
                                                                        'subgrp_1' => '',
                                                                        'subgrp_2' => '',
                                                                        'main_grp' => 'Current Assets',
                                                                        'amount' => 0
                                                                    ));*/
        //= $this->ledger_model->addLedger($title, $subgroup)
        if ($ledger_id ){
            $data  = array(
                    'short_name'      => $this->input->post('short_name'),
                    'account_holder'  => $this->input->post('account_holder'),
                    'account_type'    => $this->input->post('type'),
                    'account_no'      => $this->input->post('account_number'),
                    'bank_name'       => $this->input->post('bank_name1'),
                    'bank_address'    => $this->input->post('address'),
                    'opening_balance' => $this->input->post('balance'),
                    'branch_name'     => $this->input->post('branch_name'),
                    'ifsc_code'       => $this->input->post('ifsc_code'),
                    'default_account' => $this->input->post('default'),
                    'ledger_id'       => $ledger_id,
                    "added_date"      => date('Y-m-d'),
                    "added_user_id"   => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"       => $this->session->userdata('SESS_BRANCH_ID'),
                    "ledger_title"    => $title,
                    "updated_date"    => "",
                    "updated_user_id" => ""
            );
            $table = "bank_account";
            if ($id    = $this->general_model->insertData($table, $data)){
                $log_data = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $id,
                        'table_name'        => 'bank_account',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'Bank Account Inserted(Modal)'
                );
                $table    = "log";
                $this->general_model->insertData($table, $log_data);
            }
            $string            = "bank_account_id,ledger_id,ledger_title";
            $table             = "bank_account";
            
            $where             = array(
                    "delete_status" => 0,
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') );
            $data['data'] = $this->general_model->getRecords($string, $table, $where);
            $string = "bank_account_id,ledger_id,ledger_title";
            $table = "bank_account";
            /*$join = ["tbl_ledgers l" => "l.ledger_id = b.ledger_id" ];*/
            $where  = array(
                    "delete_status"   => 0,
                    "branch_id"       => $this->session->userdata('SESS_BRANCH_ID'),
                    "bank_account_id" => $id );
            $data['bank_data'] = $this->general_model->getRecords($string, $table, $where);
            $data['id']        = $id . "/" . $data['bank_data'][0]->ledger_title;
            $data['ledger_id'] = $ledger_id;
            echo json_encode($data);
        }
    }

    public function edit($id){
        $id = $this->encryption_url->decode($id);
        $bank_account_module_id = $this->config->item('bank_account_module');
        $data['module_id'] = $bank_account_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string            = 'b.*,l.ledger_name';
        $table             = 'bank_account b';
        $join['tbl_ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array('b.bank_account_id' => $id );
        $data['data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join, $order = "");
        $this->load->view('bank_account/edit', $data);
    }

    public function get_bank_modal($id)
    {
        $id = $this->encryption_url->decode($id);
        $bank_account_module_id = $this->config->item('bank_account_module');
        $data['module_id'] = $bank_account_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string            = 'b.*,l.ledger_name';
        $table             = 'bank_account b';
        $join['tbl_ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array('b.bank_account_id' => $id );
        $data['data'] = $this->general_model->getJoinRecords($string, $table, $where, $join, $order = "");
        echo json_encode($data);
    }

    public function edit_bank_account(){
        $bank_account_module_id = $this->config->item('bank_account_module');
        $data['module_id'] = $bank_account_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $bank_account_id = $this->input->post('bank_account_id');
        $ledger_id = $this->input->post('ledger_id_old');
        $ledger_title_old = $this->input->post('ledger_title_old');
        $ledger_title = $this->input->post('edit_ledger_title');
        
        if ($ledger_title_old != $ledger_title){
            /* Update ledger name */
            $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$ledger_title}' WHERE ledger_id='{$ledger_id}'");
        }
        /*if ($ledger_title_old != $ledger_title){
            $this->general_model->updateData('tbl_ledgers', array(
                    'ledger_name' => $ledger_title ), array(
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'ledger_id'     => $ledger_id ));
        }*/
        $resp = array();                                                            
        $account_number = $this->input->post('edit_account_number');
        $string            = 'COUNT(`account_no`) as account_count';
        $table             = 'bank_account';
        $where             = array('account_no' => $account_number,
                                'bank_account_id!=' => $bank_account_id );
        $data = $this->general_model->getJoinRecords($string, $table, $where, $join = "", $order = "");
        if($data[0]->account_count == 0){
            $data1  = array(
                    'short_name'      => $this->input->post('edit_short_name'),
                    'account_holder'  => $this->input->post('edit_account_holder'),
                    'account_type'    => $this->input->post('edit_type'),
                    'account_no'      => $this->input->post('edit_account_number'),
                    'bank_name'       => $this->input->post('edit_bank_name1'),
                    'bank_address'    => $this->input->post('edit_bank_address'),
                    'opening_balance' => $this->input->post('edit_balance'),
                    'branch_name'     => $this->input->post('edit_branch_name'),
                    'ifsc_code'       => $this->input->post('edit_ifsc_code'),
                    'default_account' => $this->input->post('edit_default'),
                    'ledger_id'       => $ledger_id,
                    "ledger_title"    => $ledger_title,
                    "branch_id"       => $this->session->userdata('SESS_BRANCH_ID'),
                    "updated_date"    => date('Y-m-d'),
                    "updated_user_id" => $this->session->userdata('SESS_USER_ID') );
            $table = "bank_account";
            $where = array("bank_account_id" => $bank_account_id );
            if ($this->general_model->updateData($table, $data1, $where)){
                $resp['flag'] = true;
                $resp['msg'] = 'Bank Account Updated Successfully';
                $resp['type'] = 'success';
                $table    = "log";
                $log_data = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $bank_account_id,
                        'table_name'        => 'bank_account',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'bank account Updated'
                );
                $this->general_model->insertData($table, $log_data);
            }else{
                $resp['flag'] = false;
                $resp['msg'] = 'Bank Account Update Unsuccessful';
                $this->session->set_flashdata('fail', 'Bank account can not be Updated.');
            }
        }else{
            $resp['flag'] = false;
            $resp['msg'] = 'duplicate';
        }
        echo json_encode($resp);
    }

    public function delete(){
        $bank_account_module_id          = $this->config->item('bank_account_module');
        $data['module_id']               = $bank_account_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($bank_account_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        $string = "ledger_id";
        $table = "bank_account";
        $where = array("bank_account_id" => $id );
        $data['data']  = $this->general_model->getRecords($string, $table, $where, $order = "");
        /*$this->general_model->updateData('tbl_ledgers', array(
                'delete_status' => 1 ), array(
                'ledger_id' => $data['data'][0]->ledger_id ));*/
        $table = "bank_account";
        $data  = array("delete_status" => 1 );
        $where = array("bank_account_id" => $id );
        if ($this->general_model->updateData($table, $data, $where)){
            $successMsg = 'Bank Account Deleted Successfully';
            $this->session->set_flashdata('bank_account_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'bank_account',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'bank account Deleted'
            );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('bank_account');
        }else{
            $errorMsg = 'Bank Account Delete Unsuccessful';
            $this->session->set_flashdata('bank_account_error',$errorMsg);
            $this->session->set_flashdata('fail', 'bank account can not be Deleted.');
            redirect("bank_account", 'refresh');
        }
    }
}

