<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $log_module_id                   = $this->config->item('log_module');
        $data['module_id']               = $log_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($log_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
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
        } if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'timestamp',
                    1 => 'message',
                    2 => 'user' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->log_list_field();
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
                    $nestedData['timestamp'] = $post->timestamp;
                    $nestedData['message']   = $post->message;
                    $nestedData['user']      = $post->first_name . ' ' . $post->last_name;
                    $send_data[]             = $nestedData;
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
            $this->load->view('log/list');
        }
    }

}

