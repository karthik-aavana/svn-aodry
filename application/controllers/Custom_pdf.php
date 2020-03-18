<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_pdf extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    function index()
    {
        $sales_module_id                 = $this->config->item('sales_module');
        $data['module_id']               = $sales_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($sales_module_id, $modules, $privilege);
        $data=array_merge($data,$section_modules);
        $email_sub_module_id             = $this->config->item('email_sub_module');
        $recurrence_sub_module_id        = $this->config->item('recurrence_sub_module');


        $pdf = $this->general_model->getRecords('settings.*', 'settings', [
                'module_id' => 2,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);


        $pdf_json            = $pdf[0]->pdf_settings;
        $rep                 = str_replace("\\", '', $pdf_json);
        $data['pdf_results'] = json_decode($rep, true);
        // print_r($data['pdf_results']);die;
        $this->load->view("custom_pdf/sales_pdf", $data);
    }

    function add_pdf_settings()
    {



        $sales_module_id                 = $this->config->item('sales_module');
        $data['module_id']               = $sales_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($sales_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $email_sub_module_id             = $this->config->item('email_sub_module');
        $recurrence_sub_module_id        = $this->config->item('recurrence_sub_module');
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
        // print_r($this->input->post());
        $company_name     = empty($this->input->post('name')) ? 'no' : 'yes';
        $logo             = empty($this->input->post('logo')) ? 'no' : 'yes';
        $address          = empty($this->input->post('address')) ? 'no' : 'yes';
        $country          = empty($this->input->post('country')) ? 'no' : 'yes';
        $state            = empty($this->input->post('state')) ? 'no' : 'yes';
        $mobile           = empty($this->input->post('mobile')) ? 'no' : 'yes';
        $landline         = empty($this->input->post('landline')) ? 'no' : 'yes';
        $email            = empty($this->input->post('email')) ? 'no' : 'yes';
        $gst              = empty($this->input->post('gst')) ? 'no' : 'yes';
        $pan              = empty($this->input->post('pan')) ? 'no' : 'yes';
        $iec              = empty($this->input->post('iec')) ? 'no' : 'yes';
        $lut              = empty($this->input->post('lut')) ? 'no' : 'yes';
        $cin              = empty($this->input->post('cin')) ? 'no' : 'yes';
        $to_company       = empty($this->input->post('to_company')) ? 'no' : 'yes';
        $to_address       = empty($this->input->post('to_address')) ? 'no' : 'yes';
        $to_country       = empty($this->input->post('to_country')) ? 'no' : 'yes';
        $to_state         = empty($this->input->post('to_state')) ? 'no' : 'yes';
        $to_mobile        = empty($this->input->post('to_mobile')) ? 'no' : 'yes';
        $to_email         = empty($this->input->post('to_email')) ? 'no' : 'yes';
        $to_state_code    = empty($this->input->post('to_state_code')) ? 'no' : 'yes';
        $place_of_supply  = empty($this->input->post('place_of_supply')) ? 'no' : 'yes';
        $billing_country  = empty($this->input->post('billing_country')) ? 'no' : 'yes';
        $nature_of_supply = empty($this->input->post('nature_of_supply')) ? 'no' : 'yes';
        $gst_payable      = empty($this->input->post('gst_payable')) ? 'no' : 'yes';
        $quantity         = empty($this->input->post('quantity')) ? 'no' : 'yes';
        $price            = empty($this->input->post('price')) ? 'no' : 'yes';
        $sub_total        = empty($this->input->post('sub_total')) ? 'no' : 'yes';
        $taxable_value    = empty($this->input->post('taxable_value')) ? 'no' : 'yes';
        $cgst             = empty($this->input->post('cgst')) ? 'no' : 'yes';
        $sgst             = empty($this->input->post('sgst')) ? 'no' : 'yes';
        $show_from        = empty($this->input->post('show_from')) ? 'no' : 'yes';
        $bordered         = empty($this->input->post('bordered')) ? 'no' : 'yes';
        $l_r              = empty($this->input->post('l_r')) ? 'no' : 'yes';
        $display_affliate = empty($this->input->post('display_affliate')) ? 'no' : 'yes';
        $igst             = empty($this->input->post('igst')) ? 'no' : 'yes';
        $tds              = empty($this->input->post('tds')) ? 'no' : 'yes';
        // $default_theme = empty($this->input->post('default_theme')) ? 'no' : 'yes';
        // $custom_theme = empty($this->input->post('custom_theme')) ? 'no' : 'yes';

        $sales_pdf_custom_array = [
                'company_name'     => $company_name,
                'logo'             => $logo,
                'address'          => $address,
                'country'          => $country,
                'state'            => $state,
                'mobile'           => $mobile,
                'landline'         => $landline,
                'email'            => $email,
                'gst'              => $gst,
                'pan'              => $pan,
                'iec'              => $iec,
                'lut'              => $lut,
                'cin'              => $cin,
                'to_company'       => $to_company,
                'to_address'       => $to_address,
                'to_country'       => $to_country,
                'to_state'         => $to_state,
                'to_mobile'        => $to_mobile,
                'to_email'         => $to_email,
                'to_state_code'    => $to_state_code,
                'place_of_supply'  => $place_of_supply,
                'billing_country'  => $billing_country,
                'nature_of_supply' => $nature_of_supply,
                'gst_payable'      => $gst_payable,
                'quantity'         => $quantity,
                'price'            => $price,
                'sub_total'        => $sub_total,
                'taxable_value'    => $taxable_value,
                'cgst'             => $cgst,
                'sgst'             => $sgst,
                'show_from'        => $show_from,
                'bordered'         => $bordered,
                'l_r'              => $l_r,
                'logo_align'       => $this->input->post('logo_align'),
                'heading_position' => $this->input->post('heading_position'),
                'theme'            => $this->input->post('theme'), //default  custom
                'background'       => $this->input->post('background'),
                'igst'             => $igst,
                'tds'              => $tds,
                'display_affliate' => $display_affliate
        ];

        $values = json_encode($sales_pdf_custom_array);

        $this->general_model->updateData('settings', [
                'pdf_settings' => $values ], [
                'module_id' => 2,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);

        redirect('custom_pdf');
    }

}

