<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payee extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    function add_payee_ajax()
    {
        $name         = trim($this->input->post("payee_name"));
        $panno        = trim($this->input->post("pan_no"));
        $add          = array(
                "payee_name"       => $name,
                "payee_pan_number" => $panno,
                "added_date"       => date('Y-m-d'),
                "added_user_id"    => $this->session->userdata('SESS_USER_ID'),
                "updated_date"     => '',
                "updated_user_id"  => '',
                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID') );
        $data_main    = array_map('trim', $add);
        $id           = $this->general_model->insertData("payee", $data_main);
        $data['data'] = $this->general_model->getRecords("*", "payee", [
                "delete_status" => 0,
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $data['id']   = $id;
        echo json_encode($data);
    }

}

