<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function get_check_ledger()
    {
        $ledger_name = strtoupper(trim($this->input->post('ledger_name')));
        $expense_id   = $this->input->post('expense_id');
        if(!$expense_id){
            $expense_id = 0;
        }else{
            $expense_id = $this->encryption_url->decode($expense_id);
        }

        $q = $this->db->query(" SELECT count(*) num FROM expense WHERE expense_title like '".$ledger_name."' AND branch_id=".$this->session->userdata('SESS_BRANCH_ID')." AND delete_status=0 AND expense_id != ".$expense_id);
        
        $data = $q->result();
        /*$data        = $this->general_model->getRecords('count(*) num', 'ledgers', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0,
                'ledger_title'  => $ledger_name,
                'ledger_id!='   => $ledger_id ));*/
        echo json_encode($data);
    }

    function index()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['accounts_module_id']      = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'ledger_title',
                    1 => 'opening_balance',
                    2 => 'dr_amount',
                    3 => 'cr_amount',
                    4 => 'closing_balance',
                    5 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->ledger_model->get_ledger_list($start, $limit);
            $count_list_data     = $this->ledger_model->get_ledger_list_count();
            $list_data['search'] = 'all';
            $totalData           = count($count_list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->ledger_model->get_ledger_list($start, $limit);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->ledger_model->get_ledger_list_serach($search, $start, $limit);
                $count_posts         = $this->ledger_model->get_ledger_list_serach_count($search);
                $totalFiltered       = count($count_posts);
            }
            $total_list_record = $this->ledger_model->get_tot_ledger_amt();

            $send_data = array();
            $equatn    = '';


            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $ledger_id = $this->encryption_url->encode($post->ledger_id);


                    $string = "as.formula";
                    $table  = "ledgers l";
                    $join   = [
                            'account_subgroup as' => 'as.account_subgroup_id = l.account_subgroup_id' ];
                    $where  = array(
                            'l.delete_status' => 0,
                            'l.ledger_id'     => $post->ledger_id );

                    $formula     = $this->general_model->getJoinRecords($string, $table, $where, $join);
                    $closing_bal = 0;
                    if ($formula[0]->formula == 'ob-dr+cr')
                    {
                        $equatn      = 'ob-dr+cr';
                        $diff        = bcsub($post->opening_balance, $post->dr_amount, 2);
                        $closing_bal = bcadd($diff, $post->cr_amount, 2);
                    }
                    else
                    {
                        $equatn      = 'ob+dr-cr';
                        $add         = bcadd($post->opening_balance, $post->dr_amount, 2);
                        $closing_bal = bcsub($add, $post->cr_amount, 2);
                    }

                    $nestedData['ledger_title']    = ' <a href="' . base_url('ledger/view_ledger_details/') . $ledger_id . '">' . $post->ledger_title . '</a> ';
                    $nestedData['opening_balance'] = $post->opening_balance;
                    $nestedData['dr_amount']       = $post->dr_amount;
                    $nestedData['cr_amount']       = $post->cr_amount;
                    $nestedData['closing_balance'] = $closing_bal;


                    $default_ledger_data = $this->general_model->getRecords('*', 'default_ledgers', array(
                            'ledger_id'     => $post->ledger_id,
                            'delete_status' => 0,
                            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
                    $check_data          = $this->checker_ledger_exist_accnt_table($post->ledger_id);



                    $cols = '';

                    if (in_array($accounts_module_id, $data['active_edit']))
                    {
                        if ($default_ledger_data)
                        {
                            $cols .= '  <a data-toggle="modal" data-target="#false_edit_modal" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>';
                        }
                        else
                        {
                            $cols .= '<a data-toggle="modal" data-target="#edit_ledger_modal" data-id="' . $ledger_id . '" title="Edit" class="edit_ledger btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>';
                        }
                    }

                    if (in_array($accounts_module_id, $data['active_delete']))
                    {
                        if ($default_ledger_data)
                        {
                            $cols .= '  <a data-toggle="modal" data-target="#default_data_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        else if ($check_data['advance_voucher'] || $check_data['contra_voucher'] || $check_data['expense_voucher'] || $check_data['general_voucher'] || $check_data['payment_voucher'] || $check_data['purchase_voucher'] || $check_data['receipt_voucher'] || $check_data['refund_voucher'] || $check_data['sales_voucher'])
                        {
                            $cols .= '  <a data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        else
                        {
                            $cols .= '  <a data-toggle="modal" data-target="#delete_modal" data-id="' . $ledger_id . '" data-path="ledger/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            }


            if (!empty($count_list_data))
            {
                $diff            = 0;
                $closing_bal     = 0;
                $tot_closing_bal = 0;
                foreach ($count_list_data as $all_data)
                {
                    if ($equatn == 'ob-dr+cr')
                    {
                        $diff        = bcsub($all_data->opening_balance, $all_data->dr_amount, 2);
                        $closing_bal = bcadd($diff, $all_data->cr_amount, 2);
                    }
                    else
                    {
                        $diff        = bcadd($all_data->opening_balance, $all_data->dr_amount, 2);
                        $closing_bal = bcsub($diff, $all_data->cr_amount, 2);
                    }

                    $tot_closing_bal = bcadd($tot_closing_bal, $closing_bal, 2);
                }
            }

            $json_data = array(
                    "draw"                  => intval($this->input->post('draw')),
                    "recordsTotal"          => intval($totalData),
                    "recordsFiltered"       => intval($totalFiltered),
                    "data"                  => $send_data,
                    "total_closing_balance" => $tot_closing_bal,
                    "total_list_record"     => $total_list_record );
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('ledger/list', $data);
        }
    }

    function checker_ledger_exist_accnt_table($idd)
    {

        $string                        = "acc.*";
        $table                         = "accounts_advance_voucher acc";
        $join                          = [
                'advance_voucher av' => 'av.advance_voucher_id = acc.advance_voucher_id' ];
        $where                         = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'av.branch_id'      => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['advance_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                       = "acc.*";
        $table                        = "accounts_contra_voucher acc";
        $join                         = [
                'contra_voucher cv' => 'cv.contra_voucher_id = acc.contra_voucher_id' ];
        $where                        = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'cv.branch_id'      => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['contra_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                        = "acc.*";
        $table                         = "accounts_expense_voucher acc";
        $join                          = [
                'expense_voucher ev' => 'ev.expense_voucher_id = acc.expense_voucher_id' ];
        $where                         = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'ev.branch_id'      => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['expense_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                        = "acc.*";
        $table                         = "accounts_general_voucher acc";
        $join                          = [
                'general_voucher gv' => 'gv.general_voucher_id = acc.general_voucher_id' ];
        $where                         = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'gv.branch_id'      => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['general_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                        = "acc.*";
        $table                         = "accounts_payment_voucher acc";
        $join                          = [
                'payment_voucher pv' => 'pv.payment_id = acc.payment_voucher_id' ];
        $where                         = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'pv.branch_id'      => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['payment_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                         = "acc.*";
        $table                          = "accounts_purchase_voucher acc";
        $join                           = [
                'purchase_voucher puv' => 'puv.purchase_voucher_id = acc.purchase_voucher_id' ];
        $where                          = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'puv.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['purchase_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                        = "acc.*";
        $table                         = "accounts_receipt_voucher acc";
        $join                          = [
                'receipt_voucher rev' => 'rev.receipt_id = acc.receipt_voucher_id' ];
        $where                         = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'rev.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['receipt_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                       = "acc.*";
        $table                        = "accounts_refund_voucher acc";
        $join                         = [
                'refund_voucher refv' => 'refv.refund_id = acc.refund_voucher_id' ];
        $where                        = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'refv.branch_id'    => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['refund_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string                      = "acc.*";
        $table                       = "accounts_sales_voucher acc";
        $join                        = [
                'sales_voucher sv' => 'sv.sales_voucher_id = acc.sales_voucher_id' ];
        $where                       = array(
                'acc.ledger_from'   => $idd,
                'acc.delete_status' => 0,
                'sv.branch_id'      => $this->session->userdata('SESS_BRANCH_ID') );
        $array_data['sales_voucher'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        return $array_data;
    }

    public function add_ledger_modal()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $ledger_data = array(
                "account_subgroup_id" => $this->input->post('account_sub_group'),
                "ledger_title"        => $this->input->post('ledger_title'),
                "opening_balance"     => $this->input->post('opening_balance'),
                "added_date"          => date('Y-m-d'),
                "added_user_id"       => $this->session->userdata('SESS_USER_ID'),
                "branch_id"           => $this->session->userdata('SESS_BRANCH_ID') );

        if ($id = $this->general_model->insertData('ledgers', $ledger_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $id,
                    'table_name'        => 'ledgers',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Ledgers Inserted' );
            $this->general_model->insertData('log', $log_data);
        }
        else
        {

        }
        echo json_encode($id);
    }

    public function add_ledger_general_bill()
    {

        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $purpose_of_transaction = $this->input->post('purpose_of_transaction');

        if ($purpose_of_transaction == "Loan" && $this->input->post('type_text') == "Orgrnaization")
        {
            $bank_account   = $this->input->post('bank_account');
            $sub_string_acc = substr($bank_account, -4);
            $title          = $this->input->post('ledger_title');
            $ledger_title   = $title . "-" . $sub_string_acc;
        }
        else
        {
            $ledger_title = $this->input->post('ledger_title');
        }

        $ledger_data = array(
                "account_subgroup_id" => $this->input->post('account_sub_group'),
                "ledger_title"        => $ledger_title,
                "opening_balance"     => 0.00,
                "added_date"          => date('Y-m-d'),
                "added_user_id"       => $this->session->userdata('SESS_USER_ID'),
                "branch_id"           => $this->session->userdata('SESS_BRANCH_ID') );

        if ($id = $this->general_model->insertData('ledgers', $ledger_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $id,
                    'table_name'        => 'ledgers',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Ledgers Inserted' );
            $this->general_model->insertData('log', $log_data);
        }
        else
        {
            $this->session->set_flashdata('fail', 'Ledgers can not be Inserted.');
        }

        //director/organization table entry
        if ($purpose_of_transaction == "Capital")
        {
            $director = array(
                    "director_name"    => $this->input->post('ledger_title'),
                    "director_address" => $this->input->post('address'),
                    "director_mobile"  => $this->input->post('mobile'),
                    "director_type"    => $this->input->post('account_sub_group'),
                    "ledger_id"        => $id,
                    "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                    "added_date"       => date('Y-m-d'),
                    "added_user_id"    => $this->session->userdata('SESS_USER_ID') );
            $this->general_model->insertData('director', $director);
        }
        else if ($purpose_of_transaction == "Loan")
        {
            $organization_data = array(
                    "name"           => $this->input->post('ledger_title'),
                    "address"        => $this->input->post('address'),
                    "mobile_no"      => $this->input->post('mobile'),
                    "type"           => $this->input->post('account_sub_group'),
                    "ledger_id"      => $id,
                    "account_number" => $this->input->post('bank_account'),
                    "branch_id"      => $this->session->userdata('SESS_BRANCH_ID'),
                    "added_date"     => date('Y-m-d'),
                    "added_user_id"  => $this->session->userdata('SESS_USER_ID') );

            if ($this->input->post('type_text') == "Orgrnaization")
            {
                $organization_data["account_number"] = $this->input->post('bank_account');
                $organization_data["type_value"]     = "Orgrnaization";
            }
            else if ($this->input->post('type_text') == "Person")
            {
                $organization_data["pan_number"] = $this->input->post('pan_no');
                $organization_data["type_value"] = "Person";
            }


            $this->general_model->insertData('organization', $organization_data);
        }

        $where = "ag.account_group_title='" . $this->input->post('purpose_of_transaction') . "' and as.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and as.delete_status=0";

        $string = "l.*";
        $table  = "ledgers l";
        $join   = [
                'account_subgroup as' => 'as.account_subgroup_id = l.account_subgroup_id',
                'account_group ag'    => 'ag.account_group_id = as.account_group_id' ];
        $where  = $where;

        $array_data['ledger_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $array_data['id'] = $id;

        echo json_encode($array_data);
    }

    public function get_ledger_data($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data                            = $this->general_model->getRecords('*', 'ledgers', array(
                'ledger_id'     => $id,
                'delete_status' => 0,
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ));
        echo json_encode($data);
    }

    public function edit_ledger_modal()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('id');

        $ledger_data = array(
                "account_subgroup_id" => $this->input->post('sub_group_title'),
                "ledger_title"        => $this->input->post('ledger_title'),
                "opening_balance"     => $this->input->post('opening_balance'),
                "updated_date"        => date('Y-m-d'),
                "updated_user_id"     => $this->session->userdata('SESS_USER_ID') );
        if ($this->general_model->updateData('ledgers', $ledger_data, array(
                        'ledger_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $id,
                    'table_name'        => 'ledgers',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Ledger Updated' );
            $this->general_model->insertData('log', $log_data);
        }
        else
        {
            $this->session->set_flashdata('fail', 'Ledger can not be Updated.');
        }
        echo json_encode($id);
    }

    public function get_all_ledgers()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $type    = $this->input->post("type");
        $purpose = $this->input->post("purpose");

        if ($type == "cash receipt" || $type == "cash payment")
        {
            $where = "(ag.account_group_title='" . $this->config->item('Sundry Debtors') . "' or ag.account_group_title='" . $this->config->item('Sundry Creditors') . "' or ag.account_group_title='" . $this->config->item('Suspense') . "') and ag.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and ag.delete_status=0 and as.delete_status=0 ";

            $data_items['string'] = "as.account_subgroup_id";
            $data_items['table']  = "account_group ag";
            $data_items['join']   = [
                    'account_subgroup as' => 'as.account_group_id = ag.account_group_id' ];
            $data_items['where']  = $where;

            $ledger_items = $this->general_model->getJoinRecords($data_items['string'], $data_items['table'], $data_items['where'], $data_items['join']);


            for ($i = 0; $i < count($ledger_items); $i++)
            {
                $ledger_data[] = $this->general_model->getRecords('*', 'ledgers', array(
                        'account_subgroup_id' => $ledger_items[$i]->account_subgroup_id,
                        'delete_status'       => 0,
                        'branch_id'           => $this->session->userdata('SESS_BRANCH_ID') ));
            }
        }
        else if ($type == "Advance Taken" || $type == "Advance Given" || $type == "Payment of Advance Taken" || $type == "Receipt of Advance Given" || $type == "Advance Tax Paid")
        {
            if ($type == "Advance Taken" || $type == "Payment of Advance Taken")
            {

                $where = "ag.account_group_title='" . $this->config->item('Sundry Debtors') . "' and ag.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and ag.delete_status=0 and as.delete_status=0 ";
            }
            else if ($type == "Advance Given" || $type == "Receipt of Advance Given")
            {

                $where = "ag.account_group_title='" . $this->config->item('Sundry Creditors') . "' and ag.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and ag.delete_status=0 and as.delete_status=0 ";
            }
            else if ($type == "Advance Tax Paid")
            {
                $where = "ag.account_group_title='" . $this->config->item('Duties & Taxes') . "' and ag.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and ag.delete_status=0 and as.delete_status=0 ";
            }


            $data_items['string'] = "as.account_subgroup_id";
            $data_items['table']  = "account_group ag";
            $data_items['join']   = [
                    'account_subgroup as' => 'as.account_group_id = ag.account_group_id' ];
            $data_items['where']  = $where;

            $ledger_items = $this->general_model->getJoinRecords($data_items['string'], $data_items['table'], $data_items['where'], $data_items['join']);

            for ($i = 0; $i < count($ledger_items); $i++)
            {
                $ledger_data[] = $this->general_model->getRecords('*', 'ledgers', array(
                        'account_subgroup_id' => $ledger_items[$i]->account_subgroup_id,
                        'delete_status'       => 0,
                        'branch_id'           => $this->session->userdata('SESS_BRANCH_ID') ));
            }
        }
        else if ($type == "Capital Invested" || $type == "Additional Capital Invested" || $type == "Capital Withdrawn")
        {

            $where = "ag.account_group_title='" . $this->config->item('Capital') . "' and ag.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and ag.delete_status=0 and as.delete_status=0 ";

            $data_items['string'] = "as.account_subgroup_id";
            $data_items['table']  = "account_group ag";
            $data_items['join']   = [
                    'account_subgroup as' => 'as.account_group_id = ag.account_group_id' ];
            $data_items['where']  = $where;

            $ledger_items = $this->general_model->getJoinRecords($data_items['string'], $data_items['table'], $data_items['where'], $data_items['join']);

            for ($i = 0; $i < count($ledger_items); $i++)
            {
                $ledger_data[] = $this->general_model->getRecords('*', 'ledgers', array(
                        'account_subgroup_id' => $ledger_items[$i]->account_subgroup_id,
                        'delete_status'       => 0,
                        'branch_id'           => $this->session->userdata('SESS_BRANCH_ID') ));
            }
        }
        else if ($type == "Fixed Asset Purchase" || $type == "Fixed Asset Sold or Disposed")
        {
            $where = "ag.account_group_title='" . $this->input->post('purpose') . "' and as.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and as.delete_status=0 and l.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " ";

            $string = "l.*";
            $table  = "ledgers l";
            $join   = [
                    'account_subgroup as' => 'as.account_subgroup_id = l.account_subgroup_id',
                    'account_group ag'    => 'ag.account_group_id = as.account_group_id' ];
            $where  = $where;

            $ledger_data['data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            if ($type == "Fixed Asset Purchase")
            {
                $string = "l.*,s.supplier_country_id as country_id,s.supplier_state_id as state_id";
                $where  = "ag.account_group_title='" . $this->config->item('Sundry Creditors') . "' and ag.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and ag.delete_status=0 and as.delete_status=0 ";
                $join   = [
                        'account_subgroup as' => 'as.account_subgroup_id = l.account_subgroup_id',
                        'account_group ag'    => 'ag.account_group_id = as.account_group_id',
                        'supplier s'          => 's.ledger_id=l.ledger_id' ];
            }
            else if ($type == "Fixed Asset Sold or Disposed")
            {
                $string = "l.*,c.customer_country_id as country_id,c.customer_state_id as state_id";
                $where  = "ag.account_group_title='" . $this->config->item('Sundry Debtors') . "' and ag.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and ag.delete_status=0 and as.delete_status=0 ";
                $join   = [
                        'account_subgroup as' => 'as.account_subgroup_id = l.account_subgroup_id',
                        'account_group ag'    => 'ag.account_group_id = as.account_group_id',
                        'customer c'          => 'c.ledger_id=l.ledger_id' ];
            }
            $string = $string;
            $table  = "ledgers l";
            $join   = $join;
            $where  = $where;

            $ledger_data['party'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }
        else
        {
            $where = "ag.account_group_title='" . $this->input->post('purpose') . "' and as.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and as.delete_status=0 and l.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " ";

            $string = "l.*";
            $table  = "ledgers l";
            $join   = [
                    'account_subgroup as' => 'as.account_subgroup_id = l.account_subgroup_id',
                    'account_group ag'    => 'ag.account_group_id = as.account_group_id' ];
            $where  = $where;

            $ledger_data = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }
        // else if($type=="deposit made" || $type=="deposit withdraw")
        // {
        //  $where="ag.account_group_title='".$this->input->post('purpose')."' and as.branch_id=".$this->session->userdata('SESS_BRANCH_ID')." and as.delete_status=0";
        // $string="l.*";
        // $table="ledgers l";
        // $join=['account_subgroup as'=>'as.account_subgroup_id = l.account_subgroup_id','account_group ag'=>'ag.account_group_id = as.account_group_id'];
        // $where=$where;
        // $ledger_data = $this->general_model->getJoinRecords($string, $table, $where, $join);
        // }
        // else if($type=="Fixed asset purchase" || $type=="Fixed asset sold or disposed")
        // {
        // }
        //print_r($ledger_items[0]->account_subgroup_id);


        echo json_encode($ledger_data);
    }

    public function get_interest_ledgers()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $type    = $this->input->post("type");
        $purpose = $this->input->post("purpose");

        $where = "ag.account_group_title='" . $this->input->post('purpose') . "' and as.subgroup_title='" . $this->input->post('type') . "' and as.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and as.delete_status=0 and l.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " ";

        $string = "l.*";
        $table  = "ledgers l";
        $join   = [
                'account_subgroup as' => 'as.account_subgroup_id = l.account_subgroup_id',
                'account_group ag'    => 'ag.account_group_id = as.account_group_id' ];
        $where  = $where;

        $ledger_data['data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        echo json_encode($ledger_data);
    }

    public function get_cash_bank_other()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $type    = $this->input->post("type");
        $purpose = $this->input->post("purpose");

        if ($purpose == "Cash")
        {

            $where = "ledger_title='cash'  and branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and delete_status=0";
        }
        else if ($purpose == "Bank to Bank")
        {
            $where = "(ledger_title='bank' or ledger_title='other payment mode') and branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and delete_status=0 ";
        }
        else
        {
            $where = "(ledger_title='cash' or ledger_title='bank' or ledger_title='other payment mode') and branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and delete_status=0 ";
        }
        $string = "*";
        $table  = "ledgers";
        $where  = $where;

        $ledger_data = $this->general_model->getRecords($string, $table, $where);
        echo json_encode($ledger_data);
    }

    public function view_ledger_details($id)
    {
        if (!$this->input->is_ajax_request()) {
                $id                              = $this->encryption_url->decode($id);
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $data['accounts_module_id']      = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['title'] = $this->general_model->getRecords("ledger_title", "ledgers", array(
                'ledger_id' => $id ));

        $data['idd'] = $id;

        $this->load->view('ledger/list_details', $data);
        }
        else
            {
                 if (!empty($this->input->post()))
        {
            $columns   = array(
                    0 => 'voucher_date',
                    1 => 'voucher_number',
                    2 => 'reference_number',
                    3 => 'particulars_title',
                    4 => 'voucher_type',
                    5 => 'dr_amount',
                    6 => 'cr_amount' );
            $limit     = $this->input->post('length');
            $start     = $this->input->post('start');
            $order     = $columns[$this->input->post('order')[0]['column']];
            $dir       = $this->input->post('order')[0]['dir'];
            
            $list_data['id'] = $id;

            $returnData = $this->ledger_model->get_ledger_details($list_data);
            $totalData           = count($returnData);
             $totalFiltered           = $totalData;

            $list_data['limit']  = $limit;
            $list_data['start']  = $start;

            if (empty($this->input->post('search')['value']))
            {
                $list_data['search'] = 'all';
                $posts = $this->ledger_model->get_ledger_details($list_data);
               
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['search'] = $search;
                $posts               = $this->ledger_model->get_ledger_details($list_data);
                $totalFiltered       = count($posts);
            }


            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $ledger_id                  = $this->encryption_url->encode($post->ledger_to);
                    $nestedData['voucher_date'] = $post->voucher_date;
                    $voucher_split              = explode('/', $post->voucher_number);
                    $refernce_split             = explode('/', $post->reference_number);
                    $decode_voucher_id          = $this->encryption_url->encode($post->voucher_id);
                    $decode_reference_id        = $this->encryption_url->encode($post->reference_id);
                    if ($post->voucher_type == "advance")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('advance_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "expense" || $post->voucher_type == "expense_bill")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('expense_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "purchase")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('purchase_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "refund")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('refund_voucher/view/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "sales")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('sales_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "contra")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('contra_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "payment")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('payment_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "receipt")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('receipt_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else if ($post->voucher_type == "general" || $post->voucher_type == "general_bill")
                    {
                        $nestedData['voucher_number'] = ' <a href="' . base_url('general_voucher/view_details/') . $decode_voucher_id . '">' . $post->voucher_number . '</a> ';
                    }
                    else
                    {
                        $nestedData['voucher_number'] = $post->voucher_number;
                    }


                    /* reference tye */


                    if ($post->reference_type == "sales")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('sales/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "expense_bill" || $post->reference_type == "expense")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('expense_bill/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "purchase")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('purchase/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "general_bill")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('general_bill/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "advance")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('advance_voucher/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "sales_debit_note")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('sales_debit_note/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "sales_credit_note")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('sales_credit_note/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "purchase_debit_note")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('purchase_debit_note/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    else if ($post->reference_type == "purchase_credit_note")
                    {
                        $nestedData['reference_number'] = ' <a href="' . base_url('purchase_credit_note/view/') . $post->reference_id . '">' . $post->reference_number . '</a> ';
                    }
                    
                    else
                    {
                        $nestedData['reference_number'] = $post->reference_number;
                    }


                    $nestedData['particulars_title'] = ' <a href="' . base_url('ledger/view_ledger_details/') . $ledger_id . '">' . $post->particulars_title . '</a> ';
                    $nestedData['voucher_type']      = $post->voucher_type;
                    $nestedData['dr_amount']         = $post->dr_amount;
                    $nestedData['cr_amount']         = $post->cr_amount;

                    $send_data[] = $nestedData;
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
            $this->load->view('ledger/list_details', $data, $title);
        }

            }


    }

    public function view_ledger_details2($id)
    {


        // $id    = $this->encryption_url->decode($id);

       
    }

    public function delete()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('ledgers', array(
                        'delete_status' => 1 ), array(
                        'ledger_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $id,
                    'table_name'        => 'ledgers',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Ledger Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('ledger', 'refresh');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Ledger can not be Deleted.');
            redirect("ledger", 'refresh');
        }
    }

}

