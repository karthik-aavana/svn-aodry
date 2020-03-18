<?php

defined('BASEPATH') OR exit('NO direct script access allowed');

class Testin extends MY_Controller
{

    function __construct()
    {
        parent::__construct();




        $this->load->model('general_model');
        $this->modules = $this->get_modules();

    }

    function index()
    {

        $password = $this->input->post('f_y_password');


        $where = ["branch_id" => $this->session->userdata('SESS_BRANCH_ID')];

        $val = $this->general_model->getRecords('common_settings.*', 'common_settings', $where);

        $passcode = $this->encryption->decrypt($val[0]->financial_year_password);
        if ($passcode == $password)
        {
            echo "sucess";
            $this->session->set_userdata("SESS_F_Y_PASSWORD", "financial_year_password");
        }
        else
        {
            echo "fail";
        }

    }

}
