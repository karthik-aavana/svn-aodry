<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account_sub_group extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
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
                    0 => 'subgroup_title',
                    1 => 'account_group_title',
                    2 => 'opening_balance',
                    3 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->account_sub_group_list_field();
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
                    $account_subgroup_id               = $this->encryption_url->encode($post->account_subgroup_id);
                    $nestedData['subgroup_title']      = $post->subgroup_title;
                    $nestedData['account_group_title'] = $post->account_group_title;
                    $nestedData['opening_balance']     = $post->opening_balance;
                    $cols                              = '<a data-toggle="modal" data-target="#edit_account_sub_group_modal" data-id="' . $account_subgroup_id . '" title="Edit" class="edit_account_sub_group btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>';
                    $sub_group_id                      = $this->general_model->getRecords('*', 'ledgers', array(
                            'account_subgroup_id' => $post->account_subgroup_id,
                            'delete_status'       => 0,
                            'branch_id'           => $this->session->userdata('SESS_BRANCH_ID') ));
                    if ($sub_group_id)
                    {
                        $cols .= '  <a data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                    }
                    else
                    {
                        $cols .= '  <a data-toggle="modal" data-target="#delete_modal" data-id="' . $account_subgroup_id . '" data-path="account_sub_group/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                    }

                    $nestedData['action'] = $cols;


                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('account_sub_group/list', $data);
        }
    }

    public function get_sub_group_data($id)
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

        $data                            = $this->general_model->getRecords('*', 'account_subgroup', array(
                'account_subgroup_id' => $id,
                'delete_status'       => 0,
                "branch_id"           => $this->session->userdata('SESS_BRANCH_ID') ));
        echo json_encode($data);
    }

    function get_all_account_sub_group()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $account_sub_group_data = $this->general_model->getRecords('*', 'account_subgroup', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        echo json_encode($account_sub_group_data);
    }

    public function add_subgroup_ajax()
    {
        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $sub_group_data = array(
                "account_group_id" => $this->input->post('account_group'),
                "subgroup_title"   => $this->input->post('subgroup_name'),
                "opening_balance"  => $this->input->post('opening_balance'),
                "formula"          => $this->input->post('formula'),
                "added_date"       => date('Y-m-d'),
                "added_user_id"    => $this->session->userdata('SESS_USER_ID'),
                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') );

        if ($id = $this->general_model->insertData('account_subgroup', $sub_group_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $id,
                    'table_name'        => 'account_subgroup',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Account Subgroup Inserted' );
            $this->general_model->insertData('log', $log_data);
        }
        else
        {
            $this->session->set_flashdata('fail', 'Subgroup can not be Inserted.');
        }
        if ($this->input->post('purpose_of_transaction'))
        {

            $where  = "ag.account_group_title='" . $this->input->post('purpose_of_transaction') . "' and as.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and as.delete_status=0";
            $string = "as.*";
            $table  = "account_subgroup as";
            $join   = [
                    'account_group ag' => 'ag.account_group_id = as.account_group_id' ];
            $where  = $where;

            $array_data['sub_group_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }



        $array_data['id'] = $id;

        echo json_encode($array_data);
    }

    public function get_sub_group_data_ajax()
    {

        $accounts_module_id              = $this->config->item('accounts_module');
        $data['module_id']               = $accounts_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($accounts_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $purpose = $this->input->post('purpose');

        $where = "ag.account_group_title='" . $this->input->post('purpose') . "' and as.branch_id=" . $this->session->userdata('SESS_BRANCH_ID') . " and as.delete_status=0";

        $string = "as.account_subgroup_id,as.subgroup_title";
        $table  = "account_subgroup as";
        $join   = [
                'account_group ag' => 'as.account_group_id = ag.account_group_id' ];
        $where  = $where;

        $subgroup_data = $this->general_model->getJoinRecords($string, $table, $where, $join);

        echo json_encode($subgroup_data);
    }

    public function edit_sub_group_modal()
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

        $sub_group_data = array(
                "account_group_id" => $this->input->post('group_title'),
                "subgroup_title"   => $this->input->post('sub_group_title'),
                "opening_balance"  => $this->input->post('opening_balance'),
                "formula"          => $this->input->post('formula'),
                "updated_date"     => date('Y-m-d'),
                "updated_user_id"  => $this->session->userdata('SESS_USER_ID') );
        if ($this->general_model->updateData('account_subgroup', $sub_group_data, array(
                        'account_subgroup_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $id,
                    'table_name'        => 'account_subgroup',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Account Subgroup Updated' );
            $this->general_model->insertData('log', $log_data);
        }
        else
        {
            $this->session->set_flashdata('fail', 'Account Subgroup can not be Updated.');
        }
        echo json_encode($id);
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
        if ($this->general_model->updateData('account_subgroup', array(
                        'delete_status' => 1 ), array(
                        'account_subgroup_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $id,
                    'table_name'        => 'account_subgroup',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Account Subgroup Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('account_sub_group', 'refresh');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Subgroup can not be Deleted.');
            redirect("account_sub_group", 'refresh');
        }
    }

}

