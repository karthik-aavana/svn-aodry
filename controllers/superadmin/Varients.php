<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Varients extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('product_model');
        $this->modules = $this->get_modules();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

    }

    function index(){


    }

    function add(){


    		  $report_module_id                = $this->config->item('varients_module');
        $data['module_id']               = $report_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($report_module_id, $modules, $privilege);
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

    	$this->load->view('varients/add');

    }

}

    function edit(){

    }




}
