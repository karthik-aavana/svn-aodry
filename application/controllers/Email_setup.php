<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Email_setup extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $email_module_id         = $this->config->item('email_module');
        $data['email_module_id'] = $email_module_id;
        $modules                 = $this->modules;
        $privilege               = "view_privilege";
        $data['privilege']       = $privilege;
        $section_modules         = $this->get_section_modules($email_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'added_date',
                    1 => 'email_protocol',
                    2 => 'smtp_host',
                    3 => 'smtp_port',
                    4 => 'smtp_secure',
                    5 => 'smtp_username',
                    6 => 'reply_mail',
                    7 => 'reply_mail',
                    8 => 'added_user',
                    9 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->email_setup_list_field();
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
                    $nestedData['added_date']     = $post->added_date;
                    $nestedData['email_protocol'] = $post->email_protocol;
                    $nestedData['smtp_host']      = $post->smtp_host;
                    $nestedData['smtp_port']      = $post->smtp_port;
                    $nestedData['smtp_secure']    = $post->smtp_secure;
                    $nestedData['smtp_username']  = $post->smtp_username;
                    $nestedData['from_name']      = $post->from_name;
                    $nestedData['reply_mail']     = $post->reply_mail;
                    $nestedData['added_user']     = $post->first_name . ' ' . $post->last_name;
                    $email_setup_id               = $this->encryption_url->encode($post->email_setup_id);
                    $cols                         = '<a href="' . base_url('email_setup/edit/') . $email_setup_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>                        <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $email_setup_id . '" data-path="email_setup/delete"  href="#" title="Delete" ><span class="glyphicon glyphicon-trash"></span></a>';
                    $nestedData['action']         = $cols;
                    $send_data[]                  = $nestedData;
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
            $this->load->view('email_setup/list', $data);
        }
    }

    public function add()
    {
        $email_module_id                 = $this->config->item('email_module');
        $data['module_id']               = $email_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($email_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

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
        } $this->load->view('email_setup/add', $data);
    }

    public function add_email_setup()
    {
        $this->add_email($this->input->post());
        redirect('email_setup', 'refresh');
    }

    public function add_email_setup_modal()
    {
        $this->add_email($this->input->post());
        $res    = $this->general_model->getRecords('*', 'email_setup', array(
                'delete_status' => 0,
                'added_user_id' => $this->session->userdata('SESS_USER_ID') ));
        $output = '<option value="">Select</option>';
        foreach ($res as $value)
        {
            if ($value->smtp_username == $this->input->post('smtp_user_name'))
                $output .= '<option value="' . $value->email_setup_id . '" selected>' . $value->smtp_username . '</option>';
            else
                $output .= '<option value="' . $value->email_setup_id . '">' . $value->smtp_username . '</option>';
        } echo json_encode($output);
    }

    public function add_email()
    {
        $email_module_id                 = $this->config->item('email_module');
        $data['module_id']               = $email_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($email_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $email_data                      = array(
                'email_protocol' => $this->input->post('email_protocol'),
                'smtp_host'      => $this->input->post('smtp_host'),
                'smtp_port'      => $this->input->post('smtp_port'),
                'smtp_secure'    => $this->input->post('smtp_secure'),
                'smtp_username'  => $this->input->post('smtp_user_name'),
                'smtp_password'  => $this->input->post('smtp_password'),
                'from_name'      => $this->input->post('from_name'),
                'reply_mail'     => $this->input->post('reply_mail'),
                'added_date'     => date('Y-m-d'),
                'added_user_id'  => $this->session->userdata('SESS_USER_ID'),
                'branch_id'      => $this->session->userdata('SESS_BRANCH_ID') );
        if ($email_setup_id                  = $this->general_model->insertData('email_setup', $email_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $email_setup_id,
                    'table_name'        => 'email_setup',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Email Setup Inserted' );
            $this->general_model->insertData('log', $log_data);
        }
    }

    public function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $email_module_id                 = $this->config->item('email_module');
        $data['module_id']               = $email_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($email_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

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
        $data['data'] = $this->general_model->getRecords('*', 'email_setup', array(
                'email_setup_id' => $id,
                'delete_status'  => 0,
                'added_user_id'  => $this->session->userdata('SESS_USER_ID') ));
        $this->load->view('email_setup/edit', $data);
    }

    public function edit_email_setup()
    {
        $email_module_id                 = $this->config->item('email_module');
        $data['module_id']               = $email_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($email_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $email_setup_id                  = $this->input->post('email_setup_id');
        $email_data                      = array(
                'email_protocol'  => $this->input->post('email_protocol'),
                'smtp_host'       => $this->input->post('smtp_host'),
                'smtp_port'       => $this->input->post('smtp_port'),
                'smtp_secure'     => $this->input->post('smtp_secure'),
                'smtp_username'   => $this->input->post('smtp_user_name'),
                'smtp_password'   => $this->input->post('smtp_password'),
                'from_name'       => $this->input->post('from_name'),
                'reply_mail'      => $this->input->post('reply_mail'),
                'updated_date'    => date('Y-m-d'),
                'updated_user_id' => $this->session->userdata('SESS_USER_ID'),
                'branch_id'       => $this->session->userdata('SESS_BRANCH_ID') );
        if ($email_setup_id                  = $this->general_model->updateData('email_setup', $email_data, array(
                'email_setup_id' => $email_setup_id,
                'delete_status'  => 0,
                'added_user_id'  => $this->session->userdata('SESS_USER_ID') )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $email_setup_id,
                    'table_name'        => 'email_setup',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Email Setup Updated' );
            $this->general_model->insertData('log', $log_data);
        } redirect('email_setup', 'refresh');
    }

    public function delete()
    {
        $email_module_id                 = $this->config->item('email_module');
        $data['module_id']               = $email_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($email_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('email_setup', array(
                        'delete_status' => 1,
                        'added_user_id' => $this->session->userdata('SESS_USER_ID') ), array(
                        'email_setup_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'email_setup',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Email Setup Deleted' );
            $this->general_model->insertData('log', $log_data);
        } redirect('email_setup');
    }

}

