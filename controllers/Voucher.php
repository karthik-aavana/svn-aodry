<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'product_model',
            'service_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }

    function index() {

        $sales_voucher_module_id = $this->config->item('sales_voucher_module');
        $data['module_id'] = $sales_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');

        $this->load->view('voucher/view_journal', $data);
    }

    function add() {
        $sales_voucher_module_id = $this->config->item('sales_voucher_module');
        $data['module_id'] = $sales_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $this->load->view('voucher/general_voucher', $data);
    }

function email() {
        $sales_voucher_module_id = $this->config->item('sales_voucher_module');
        $data['module_id'] = $sales_voucher_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $this->load->view('voucher/email', $data);
    }


}
