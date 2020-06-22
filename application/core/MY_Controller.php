<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->driver('cache');
        ob_start();
        ob_clean();
        ob_flush();
        $this->load->model(['general_model', 'ledger_model']);
        // $this->generate_recurrence_invoice();
    }

    public function branch_call(){
        $branch_data  = $this->common->branch_list();
        $branch       = $this->general_model->getRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['order']);
        return $branch;
    }

    public function get_default_country_state()
    {
        if($this->session->userdata() == '0'){
            $branch_data  = $this->common->branch_before_update_field();
        }else{
            $branch_data  = $this->common->branch_field();
        }
        
        $branch       = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data = $this->common->country_field();
        $country      = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        if(!@$branch[0]->branch_country_id) $branch[0]->branch_country_id = 101;
        $state_data   = $this->common->state_field($branch[0]->branch_country_id);
        $state        = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        if(!@$branch[0]->branch_state_id) $branch[0]->branch_state_id = 0;
        $city_data    = $this->common->city_field($branch[0]->branch_state_id);
        $city         = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data         = array('branch' => $branch, 'country' => $country, 'state' => $state,
            'city'                         => $city);
        return $data;
    }

    public function get_default_country_state_old()
    {
        if($this->session->userdata('SESS_DETAILS_UPDATED') == '0'){
            $branch_data  = $this->common->branch_before_update_field();
        }else{
            $branch_data  = $this->common->branch_field();
        }
        $branch       = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        /*echo "<pre>";
        print_r($this->db->last_query());
        print_r($branch);
        exit;*/
        $country_data = $this->common->country_field();
        $country      = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        /*if(!@$branch[0]->branch_country_id) $branch[0]->branch_country_id = 101;*/
        $state = array();
        $state_data   = $this->common->state_field((@$branch[0]->branch_country_id ? $branch[0]->branch_country_id : 0));
        $state        = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        
        $city = array();
        if(@$branch[0]->branch_state_id){

            $city_data    = $this->common->city_field((@$branch[0]->branch_state_id ? $branch[0]->branch_state_id : 0));
            $city         = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        }
        $data         = array('branch' => $branch, 'country' => $country, 'state' => $state,
            'city'                         => $city);
        return $data;
    }

    

    public function getValues($ary,$key){
        $ids_ary = array();
        if(!empty($ary)){
            foreach ($ary as $k => $value) {
                array_push($ids_ary, $value->$key);
            }
        }
        return $ids_ary;
    }

    public function get_modules()
    {
        $sess_branch_id         = $this->session->userdata('SESS_BRANCH_ID');
        $sess_financial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $sess_user_id           = $this->session->userdata('SESS_USER_ID');
        $sess_default_currency  = $this->session->userdata('SESS_DEFAULT_CURRENCY');
       
        if (empty($sess_branch_id) || empty($sess_financial_year_id) || empty($sess_user_id) || empty($sess_default_currency))
        {
            $array_items = array('SESS_USER_ID' => '', 'SESS_BRANCH_ID' => '');
            $this->session->unset_userdata($array_items);
            redirect('auth/login', 'refresh');
        }

        $module_data = $this->common->module_field();
        $module      = $this->general_model->getJoinRecords($module_data['string'], $module_data['table'], $module_data['where'], $module_data['join']);
        $active_modules =$settings= $sub_module=  $active_view = $active_edit = $active_delete = $active_add = array();

        foreach ($module as $key => $value)
        {

            if (isset($value->module_id) && $value->module_id != "")
            {
                $active_modules[$key] = $value->module_id;

                if ($value->view_privilege == "yes")
                {
                    $active_view[$key] = $value->module_id;
                }

                if ($value->edit_privilege == "yes")
                {
                    $active_edit[$key] = $value->module_id;
                }

                if ($value->delete_privilege == "yes")
                {
                    $active_delete[$key] = $value->module_id;
                }

                if ($value->add_privilege == "yes")
                {
                    $active_add[$key] = $value->module_id;
                }

                $sub_module_data               = $this->common->sub_module_field($value->module_id);
                $sub_module[$value->module_id] = $this->general_model->getRecords($sub_module_data['string'], $sub_module_data['table'], $sub_module_data['where']);
                $settings_data                 = $this->common->settings_field($value->module_id);
                $settings[$value->module_id]   = $this->general_model->getRecords($settings_data['string'], $settings_data['table'], $settings_data['where']);
            }

        }

        $common_settings_data = $this->common->common_settings_field();
        $common_settings      = $this->general_model->getRecords($common_settings_data['string'], $common_settings_data['table'], $common_settings_data['where']);
        $data                 = array(
            'user_active_modules' => $active_modules,
            'user_active_add'     => $active_add,
            'user_active_edit'    => $active_edit,
            'user_active_view'    => $active_view,
            'user_active_delete'  => $active_delete,
            'modules'             => $module,
            'sub_modules'         => $sub_module,
            'common_settings'     => $common_settings,
            'settings'            => $settings
        );
        return $data;
    }

    public function sa_get_modules($user_id, $branch_id){
        $sess_user_id = $this->session->userdata('SESS_SA_USER_ID');

        if (empty($sess_user_id))
        {
            $this->session->sess_destroy();
            redirect('superadmin/auth/login', 'refresh');
        }
        $module_data = $this->common->sa_module_field($user_id, $branch_id);
        $module      = $this->general_model->getJoinRecords($module_data['string'], $module_data['table'], $module_data['where'], $module_data['join']);

        foreach ($module as $key => $value) {
            $sub_module_data               = $this->common->sub_module_field($value->module_id, $branch_id);
            $sub_module[$value->module_id] = $this->general_model->getRecords($sub_module_data['string'], $sub_module_data['table'], $sub_module_data['where']);
            $settings_data                 = $this->common->settings_field($value->module_id, $branch_id);
            $settings[$value->module_id]   = $this->general_model->getRecords($settings_data['string'], $settings_data['table'], $settings_data['where']);
        }

        $common_settings_data = $this->common->common_settings_field($branch_id);
        $common_settings      = $this->general_model->getRecords($common_settings_data['string'], $common_settings_data['table'], $common_settings_data['where']);
        $data                 = array('modules' => $module, 'sub_modules'       => $sub_module,
            'common_settings'                       => $common_settings, 'settings' => $settings);
        return $data;
    }

    public function sa_get_active_modules($group, $branch_id){
        $list_data           = $this->common->assigned_module_list_field($group, $branch_id);
        $data               = $this->general_model->getPageJoinRecords($list_data);
        return $data;
    }
    
    public function sa_getOnly_modules($user_id,$branch_id){
        $sess_user_id = $this->session->userdata('SESS_SA_USER_ID');
        $module_data = $this->common->sa_autoModule_field($branch_id);
        $module      = $this->general_model->getJoinRecords($module_data['string'], $module_data['table'], $module_data['where'], $module_data['join']);   
        
        foreach ($module as $key => $value) {
            $sub_module_data               = $this->common->sub_module_field($value->module_id, $branch_id);
            $sub_module[$value->module_id] = $this->general_model->getRecords($sub_module_data['string'], $sub_module_data['table'], $sub_module_data['where']);
            $settings_data                 = $this->common->settings_field($value->module_id, $branch_id);
            $settings[$value->module_id]   = $this->general_model->getRecords($settings_data['string'], $settings_data['table'], $settings_data['where']);
        }

        $common_settings_data = $this->common->common_settings_field($branch_id);
        $common_settings      = $this->general_model->getRecords($common_settings_data['string'], $common_settings_data['table'], $common_settings_data['where']);
        $data                 = array('modules' => $module, 'sub_modules'       => $sub_module,
            'common_settings'                       => $common_settings, 'settings' => $settings);
        return $data;
    }

    public function check_modules($module_id, $modules)
    {
        $flag = 0;
        $data = array();

        foreach ($modules as $key => $value)
        {

            if ($module_id == $value->module_id && $value->branch_id == $this->session->userdata('SESS_BRANCH_ID'))
            {
                $flag = 1;
                $data = $value;
            }

            if ($flag == 1)
            {
                break;
            }

        }
        return $data;
    }

    public function precise_amount($val, $precision)
    {

        $val = (float) $val;
        // $amt =  round($val,$GLOBALS['common_settings_amount_precision']);
        $dat = number_format($val, $precision, '.', '');
        return $dat;
    }

    public function check_sub_modules($module_id, $sub_modules)
    {
        $data = array();

        foreach ($sub_modules[$module_id] as $key => $value)
        {

            if ($module_id == $value->module_id && $value->branch_id == $this->session->userdata('SESS_BRANCH_ID'))
            {
                $data[] = $value->sub_module_id;
            }

        }
        return $data;
    }

    public function check_settings($module_id, $sub_modules)
    {
        $data = array();

        foreach ($sub_modules[$module_id] as $key => $value)
        {

            if ($module_id == $value->module_id && $value->branch_id == $this->session->userdata('SESS_BRANCH_ID'))
            {
                $data[] = $value;
            }

        }
        return $data;
    }

    public function get_section_modules($module_id, $modules, $privilege,$redirect ='')
    {
        if($this->session->userdata('SESS_PACKAGE_STATUS') == '0' && $module_id != 56 && $module_id != 37){
            redirect('auth/unauthorized', 'refresh');
        }

        if($this->session->userdata('SESS_DETAILS_UPDATED') == '0' && $module_id != 56 && $module_id != 37){
            redirect('company_setting', 'refresh');
        }

        if (in_array($module_id, $modules['user_active_modules']))
        {

            $check_value = "";

            if ($privilege == "add_privilege")
            {

                if (in_array($module_id, $modules['user_active_add']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "edit_privilege")
            {

                if (in_array($module_id, $modules['user_active_edit']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "delete_privilege")
            {

                if (in_array($module_id, $modules['user_active_delete']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "view_privilege")
            {

                if (in_array($module_id, $modules['user_active_view']))
                {
                    $check_value = "yes";
                }

            }
           
            if ($check_value != "yes")
            {
                redirect('auth/unauthorized', 'refresh');
            }

            $check_sub_modules = $this->check_sub_modules($module_id, $modules['sub_modules']);
            $check_settings    = $this->check_settings($module_id, $modules['settings']);

            // $data = array(

            //         'modules'     => $modules,

            //         'sub_modules' => $check_sub_modules,

            //         'settings'    => $check_settings,
            // );
            $data                           = array();
            $data['access_sub_modules']     = $check_sub_modules;
            $data['access_settings']        = $check_settings;
            $data['access_common_settings'] = $modules['common_settings'];

            // echo "<pre>";

            // print_r($check_sub_modules);

            // exit;
            //      $data['access_modules']     = $modules;
            $data['active_modules'] = $modules['user_active_modules'];
            $data['active_add']     = $modules['user_active_add'];
            $data['active_edit']    = $modules['user_active_edit'];
            $data['active_view']    = $modules['user_active_view'];
            $data['active_delete']  = $modules['user_active_delete'];

            return $data;
        }
        else
        {
            if($redirect != 'unauthorized'){
                redirect('auth/unauthorized', 'refresh');
            }else{
                return array();
            }
            
        }
    }

    public function check_privilege_section_modules($module_id, $modules, $privilege,$redirect ='')
    {

        if (in_array($module_id, $modules['user_active_modules']))
        {

            $check_value = "";

            if ($privilege == "add_privilege")
            {

                if (in_array($module_id, $modules['user_active_add']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "edit_privilege")
            {

                if (in_array($module_id, $modules['user_active_edit']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "delete_privilege")
            {

                if (in_array($module_id, $modules['user_active_delete']))
                {
                    $check_value = "yes";
                }

            }
            elseif ($privilege == "view_privilege")
            {

                if (in_array($module_id, $modules['user_active_view']))
                {
                    $check_value = "yes";
                }

            }
           
            if ($check_value != "yes")
            {
                return false;
            }

            $check_sub_modules = $this->check_sub_modules($module_id, $modules['sub_modules']);
            $check_settings    = $this->check_settings($module_id, $modules['settings']);

            // $data = array(

            //         'modules'     => $modules,

            //         'sub_modules' => $check_sub_modules,

            //         'settings'    => $check_settings,
            // );
            $data                           = array();
            $data['access_sub_modules']     = $check_sub_modules;
            $data['access_settings']        = $check_settings;
            $data['access_common_settings'] = $modules['common_settings'];

            // echo "<pre>";

            // print_r($check_sub_modules);

            // exit;
            //      $data['access_modules']     = $modules;
            $data['active_modules'] = $modules['user_active_modules'];
            $data['active_add']     = $modules['user_active_add'];
            $data['active_edit']    = $modules['user_active_edit'];
            $data['active_view']    = $modules['user_active_view'];
            $data['active_delete']  = $modules['user_active_delete'];

            return $data;
        }
        else
        {
            return false;
            
        }
    }
    public function generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, $option = "",$brand = 0)
    {
        $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));

        if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
        {

            if ($option == "1")
            {
                $current_date = $current_date;
            }
            else
            {
                $current_date = $financial_year[0] . '-04-01';
            }

        }
        else
        {
            $current_date = $current_date;
        }

        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        if($this->session->userdata('SESS_BRANCH_ID') == $this->config->item('Sanath')){
            if($first_prefix == "SAL"){
                $first_prefix = $first_prefix.date("y");
            }
        }
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_type       = $access_settings[0]->invoice_type;
        $invoice_creation   = $access_settings[0]->invoice_creation;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $count_condition    = "";
        if($invoice_seperation == 'no'){
            $invoice_seperation = '';
        }

        if ($invoice_creation == "automatic")
        {

            if ($invoice_type == "monthly")
            {
                $mont            = date('m', strtotime($current_date));
                $count_condition = 'month(' . $date_field_name . ') => ' . $mont;
            }
            else
            if ($invoice_type == "yearly")
            {
                $yea             = date('Y', strtotime($current_date));
                $count_condition = 'year(' . $date_field_name . ') => ' . $yea;
            }
        }
        
        $invoice_count_data = $this->common->invoice_count_field($primary_id, $table_name, $count_condition, $invoice_type,$brand);

        $invoice_count = $this->general_model->getRecords($invoice_count_data['string'], $invoice_count_data['table'], $invoice_count_data['where'], $invoice_count_data['order']);
        
        $count         = $invoice_count[0]->invoice_count;
        $invoice_count = sprintf('%03d', intval($count) + 1);
        $month         = explode("-", trim($current_date));
        
        if ($invoice_creation == "automatic")
        {
            $year_prefix = $month[0];

            if ($last_prefix == "month_with_number")
            {
                $last_prefix = $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            if ($last_prefix == "year_with_month")
            {
                $last_prefix = $invoice_seperation . $month[0] . $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            {
                $last_prefix = $invoice_seperation;
            }

            if ($invoice_type == "regular")
            {
                $reference_number = $first_prefix . $invoice_seperation . $invoice_count;
            }
            else
            {
                $reference_number = $first_prefix . $last_prefix . $invoice_count;
            }

        }
        else
        {
            $reference_number = $first_prefix . $invoice_count;
        }

        return $reference_number;
    }

public function generate_branch_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, $option = "",$company_id)
    {
        $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));

        if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
        {

            if ($option == "1")
            {
                $current_date = $current_date;
            }
            else
            {
                $current_date = $financial_year[0] . '-04-01';
            }

        }
        else
        {
            $current_date = $current_date;
        }

        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_type       = $access_settings[0]->invoice_type;
        $invoice_creation   = $access_settings[0]->invoice_creation;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $count_condition    = "";

        if ($invoice_creation == "automatic")
        {

            if ($invoice_type == "monthly")
            {
                $mont            = date('m', strtotime($current_date));
                $count_condition = 'month(' . $date_field_name . ') => ' . $mont;
            }
            else
            if ($invoice_type == "yearly")
            {
                $yea             = date('Y', strtotime($current_date));
                $count_condition = 'year(' . $date_field_name . ') => ' . $yea;
            }

        }

        $invoice_count_data = $this->common->branch_count_field($primary_id, $table_name, $count_condition, $invoice_type,$company_id);

        $invoice_count = $this->general_model->getRecords($invoice_count_data['string'], $invoice_count_data['table'], $invoice_count_data['where'], $invoice_count_data['order']);

        $count         = $invoice_count[0]->invoice_count;
        $invoice_count = sprintf('%03d', intval($count) + 1);
        $month         = explode("-", trim($current_date));

        if ($invoice_creation == "automatic")
        {
            $year_prefix = $month[0];

            if ($last_prefix == "month_with_number")
            {
                $last_prefix = $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            if ($last_prefix == "year_with_month")
            {
                $last_prefix = $invoice_seperation . $month[0] . $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            {
                $last_prefix = $invoice_seperation;
            }

            if ($invoice_type == "regular")
            {
                $reference_number = $first_prefix . $invoice_seperation . $invoice_count;
            }
            else
            {
                $reference_number = $first_prefix . $last_prefix . $invoice_count;
            }

        }
        else
        {
            $reference_number = $first_prefix . $invoice_count;
        }

        return $reference_number;
    }



    public function generate_stock_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, $option = "",$type)
    {
        $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));

        if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
        {

            if ($option == "1")
            {
                $current_date = $current_date;
            }
            else
            {
                $current_date = $financial_year[0] . '-04-01';
            }

        }
        else
        {
            $current_date = $current_date;
        }

        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_type       = $access_settings[0]->invoice_type;
        $invoice_creation   = $access_settings[0]->invoice_creation;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $count_condition    = "";

        if ($invoice_creation == "automatic")
        {

            if ($invoice_type == "monthly")
            {
                $mont            = date('m', strtotime($current_date));
                $count_condition = 'month(' . $date_field_name . ') => ' . $mont;
            }
            else
            if ($invoice_type == "yearly")
            {
                $yea             = date('Y', strtotime($current_date));
                $count_condition = 'year(' . $date_field_name . ') => ' . $yea;
            }

        }

        $invoice_count_data = $this->common->stock_count_field($primary_id, $table_name, $count_condition, $invoice_type,$type);

        $invoice_count = $this->general_model->getRecords($invoice_count_data['string'], $invoice_count_data['table'], $invoice_count_data['where'], $invoice_count_data['order']);

        $count         = $invoice_count[0]->invoice_count;
        $invoice_count = sprintf('%03d', intval($count) + 1);
        $month         = explode("-", trim($current_date));

        if ($invoice_creation == "automatic")
        {
            $year_prefix = $month[0];

            if ($last_prefix == "month_with_number")
            {
                $last_prefix = $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            if ($last_prefix == "year_with_month")
            {
                $last_prefix = $invoice_seperation . $month[0] . $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            {
                $last_prefix = $invoice_seperation;
            }

            if ($invoice_type == "regular")
            {
                $reference_number = $first_prefix . $invoice_seperation . $invoice_count;
            }
            else
            {
                $reference_number = $first_prefix . $last_prefix . $invoice_count;
            }

        }
        else
        {
            $reference_number = $first_prefix . $invoice_count;
        }

        return $reference_number;
    }

    public function generate_reference_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, $option = "")
    {
        $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));

        if ((date("Y") != $financial_year[0]) && (date("Y") != $financial_year[1]))
        {

            if ($option == "1")
            {
                $current_date = $current_date;
            }
            else
            {
                $current_date = $financial_year[0] . '-04-01';
            }

        }
        else
        {
            $current_date = $current_date;
        }

        $first_prefix       = $access_settings[0]->settings_reference_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_type       = $access_settings[0]->invoice_type;
        $invoice_creation   = $access_settings[0]->invoice_creation;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $count_condition    = "";

        if ($invoice_creation == "automatic")
        {

            if ($invoice_type == "monthly")
            {
                $mont            = date('m', strtotime($current_date));
                $count_condition = 'month(' . $date_field_name . ') => ' . $mont;
            }
            else
            if ($invoice_type == "yearly")
            {
                $yea             = date('Y', strtotime($current_date));
                $count_condition = 'year(' . $date_field_name . ') => ' . $yea;
            }

        }

        $invoice_count_data = $this->common->reference_count_field($primary_id, $table_name, $count_condition, $invoice_type);
        $invoice_count      = $this->general_model->getRecords($invoice_count_data['string'], $invoice_count_data['table'], $invoice_count_data['where'], $invoice_count_data['order']);
        $count              = $invoice_count[0]->invoice_count;
        $invoice_count      = sprintf('%03d', intval($count) + 1);
        $month              = explode("-", trim($current_date));

        if ($invoice_creation == "automatic")
        {
            $year_prefix = $month[0];

            if ($last_prefix == "month_with_number")
            {
                $last_prefix = $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            if ($last_prefix == "year_with_month")
            {
                $last_prefix = $invoice_seperation . $month[0] . $invoice_seperation . $month[1] . $invoice_seperation;
            }
            else
            {
                $last_prefix = $invoice_seperation;
            }

            if ($invoice_type == "regular")
            {
                $reference_number = $first_prefix . $invoice_seperation . $invoice_count;
            }
            else
            {
                $reference_number = $first_prefix . $last_prefix . $invoice_count;
            }

        }
        else
        {
            $reference_number = $first_prefix . $invoice_count;
        }

        return $reference_number;
    }

    public function get_check_invoice_number($table_name, $invoice_field_name, $invoice_number, $access_settings)
    {

        $input = array(
            'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status'     => 0,
            $invoice_field_name => $invoice_number
        );

        if ($access_settings[0]->invoice_type != "regular")
        {
            $input['financial_year_id'] = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        }

        $data = $this->general_model->getRecords('count(*) num', $table_name, $input);
        return $data;
    }

    public function customer_call()
    {
        $customer_data = $this->common->customer_field();
        $data          = $this->general_model->getJoinRecords($customer_data['string'], $customer_data['table'], $customer_data['where'], $customer_data['join'], $customer_data['order']);
        return $data;
    }

    public function supplier_call()
    {
        $supplier_data = $this->common->supplier_field();
        $data          = $this->general_model->getJoinRecords($supplier_data['string'], $supplier_data['table'], $supplier_data['where'], $supplier_data['join'], $supplier_data['order']);
        return $data;
    }

    public function customer_call1()
    {
        $customer_data = $this->common->customer_field1();
        $data          = $this->general_model->getJoinRecords($customer_data['string'], $customer_data['table'], $customer_data['where'], $customer_data['join'], $customer_data['order']);
        return $data;
    }

    public function supplier_call1()
    {
        $supplier_data = $this->common->supplier_field1();
        $data          = $this->general_model->getJoinRecords($supplier_data['string'], $supplier_data['table'], $supplier_data['where'], $supplier_data['join'], $supplier_data['order']);
        return $data;
    }

    public function payee_call()
    {
        $supplier_data = $this->common->payee_field();
        $data          = $this->general_model->getRecords($supplier_data['string'], $supplier_data['table'], $supplier_data['where'], $supplier_data['order']);
        return $data;
    }

    public function discount_call()
    {
        $discount_data = $this->common->discount_field();
        $data          = $this->general_model->getRecords($discount_data['string'], $discount_data['table'], $discount_data['where']);
        return $data;
    }

    public function brand_call(){
        $brand_data = $this->common->brand_field();
        $data          = $this->general_model->getRecords($brand_data['string'], $brand_data['table'], $brand_data['where']);
        return $data;
    }

    public function discount_call1()
    {
        $discount_data = $this->common->discount_field1();
        $data          = $this->general_model->getRecords($discount_data['string'], $discount_data['table'], $discount_data['where']);
        return $data;
    }

    public function currency_call()
    {
        $currency_data = $this->common->currency_field();
        $data              = $this->general_model->getJoinRecords($currency_data['string'], $currency_data['table'], $currency_data['where'], $currency_data['join'], $currency_data['order']);
       /* $data          = $this->general_model->getRecords($currency_data['string'], $currency_data['table'], $currency_data['where']);*/
        return $data;
    }

    public function getBranchCurrencyCode()
    {
        $data          = $this->general_model->getRecords('*', 'currency', array('currency_id' => $this->session->userdata('SESS_DEFAULT_CURRENCY')));
        return $data;
        
    }

    public function getCurrencyInfo($id)
    {
        $data          = $this->general_model->getRecords('*', 'currency', array('currency_id' => $id));
        return $data;
        
    }

    public function financial_year_call()
    {
        $financial_year_data = $this->common->financial_year_field();
        $data                = $this->general_model->getRecords($financial_year_data['string'], $financial_year_data['table'], $financial_year_data['where']);
        return $data;
    }

    public function product_category_call()
    {
        $category_data = $this->common->category_field('product');
        $data          = $this->general_model->getRecords($category_data['string'], $category_data['table'], $category_data['where']);
        return $data;
    }

    public function service_category_call()
    {
        $category_data = $this->common->category_field('service');
        $data          = $this->general_model->getRecords($category_data['string'], $category_data['table'], $category_data['where']);
        return $data;
    }

    public function bank_account_call()
    {
        $bank_account_data = $this->common->bank_account_field('bank_account');
        $data              = $this->general_model->getJoinRecords($bank_account_data['string'], $bank_account_data['table'], $bank_account_data['where'], $bank_account_data['join'], $bank_account_data['order']);
        return $data;
    }

    public function bank_account_call_new(){
        $bank_account_data = $this->common->bank_account_field_new('bank_account');
        $data              = $this->general_model->getRecords($bank_account_data['string'], $bank_account_data['table'], $bank_account_data['where'], $bank_account_data['order']);
        return $data;
    }
    

    /*public function tax_call()
    {
        $tax_data = $this->common->tax_field();
        $tax_data = $this->common->tax_field();
        $data     = $this->general_model->getRecords($tax_data['string'], $tax_data['table'], $tax_data['where']);
        return $data;
    }*/

    public function tax_call()
    {
        $tax_data = $this->common->tax_field_with_all_type();
        $data     = $this->general_model->getJoinRecords($tax_data['string'], $tax_data['table'], $tax_data['where'],$tax_data['join']);
        return $data;
    }
    public function uqc_call()
    {
        $uqc_data = $this->common->uqc_field();
        $data     = $this->general_model->getRecords($uqc_data['string'], $uqc_data['table'], $uqc_data['where']);
        return $data;
    }
    public function uqc_product_service_call($type)
    {
        $uqc_data = $this->common->uqc_field1($type);
        $data     = $this->general_model->getRecords($uqc_data['string'], $uqc_data['table'], $uqc_data['where']);
        return $data;
    }
    public function bulk_uqc_product_call($type)
    {
        $uqc_data = $this->common->bulk_uqc_field($type);
        $data     = $this->general_model->getRecords($uqc_data['string'], $uqc_data['table'], $uqc_data['where']);
        return $data;
    }
    public function sac_call()
    {
        $sac_data = $this->common->sac_field();
        $data     = $this->general_model->getRecords($sac_data['string'], $sac_data['table'], $sac_data['where']);
        return $data;
    }

    public function chapter_call()
    {
        $hsn_chapter_data = $this->common->hsn_chapter_field();
        $data             = $this->general_model->getRecords($hsn_chapter_data['string'], $hsn_chapter_data['table'], $hsn_chapter_data['where']);
        return $data;
    }

    public function tds_section_call()
    {
        $tds_section_data = $this->common->tds_section_field();
        $data             = $this->general_model->getRecords($tds_section_data['string'], $tds_section_data['table'], $tds_section_data['where']);
        return $data;
    }

    public function hsn_call()
    {
        $hsn_data = $this->common->hsn_field();
        $data     = $this->general_model->getRecords($hsn_data['string'], $hsn_data['table'], $hsn_data['where']);
        return $data;
    }

    public function user_accessibility_call()
    {
        $user_accessibility_data = $this->common->user_accessibility_field();
        $data                    = $this->general_model->getRecords($user_accessibility_data['string'], $user_accessibility_data['table'], $user_accessibility_data['where']);
        return $data;
    }

    public function country_call() {
        $uqc_data = $this->common->country_field();
        $data     = $this->general_model->getRecords($uqc_data['string'], $uqc_data['table'], $uqc_data['where']);
        return $data;
    }

    public function tax_section_call()
    {
        $tds_section_data = $this->common->tax_section_field();
        $data             = $this->general_model->getRecords($tds_section_data['string'], $tds_section_data['table'], $tds_section_data['where']);
        return $data;
    }

    public function product_call() {
        $product_data = $this->common->all_products_field();
        $data     = $this->general_model->getRecords($product_data['string'], $product_data['table'], $product_data['where']);
        return $data;
    }

    public function product_stage_call() {
        $product_data = $this->common->all_products_stage_field();
        $data     = $this->general_model->getRecords($product_data['string'], $product_data['table'], $product_data['where']);
        return $data;
    }

    

    public function other_modules_present($modules_present, $modules)
    {
        $data = array();

        foreach ($modules_present as $key => $value)
        {
            $modules_data = $this->check_modules($value, $modules);

            if (!empty($modules_data))
            {
                $user_modules = $this->user_accessibility_call();

                foreach ($user_modules as $key2 => $value2)
                {

                    if ($value2->module_id == $value)
                    {

                        if ($value2->add_privilege == "yes" || $value2->edit_privilege == "yes")
                        {
                            $data[$key] = $value;
                        }

                    }

                }

            }

        }
        return $data;
    }

    public function template_note($left_note, $right_note)
    {
        $val              = str_replace(array("\r\n#", "\\r\\n#"), " #", $left_note);
        $val              = str_replace(array("\r\n", "\\r\\n"), " <br>", $val);
        $note1            = $val;
        $template         = '';
        $j                = 0;
        $space            = 0;
        $text             = array();
        $template_content = array();
        $text[$j]         = '';

        for ($i = 0; $i < strlen($note1); $i++)
        {

            if ($note1[$i] == '#')
            {
                $space = 1;
            }

            if ($space == 1)
            {

                if ($note1[$i] != ' ')
                {
                    $template .= $note1[$i];
                }
                else
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                    $text[$j] .= $note1[$i];
                }

                if ($i == strlen($note1) - 1)
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                }

            }
            else
            {
                $text[$j] .= $note1[$i];
            }

        }
        $data_note1       = $text;
        $data_template1   = $template_content;
        $val              = str_replace(array("\r\n#", "\\r\\n#"), " #", $right_note);
        $val              = str_replace(array("\r\n", "\\r\\n"), " <br>", $val);
        $note2            = $val;
        $template         = '';
        $j                = 0;
        $space            = 0;
        $text             = array();
        $template_content = array();
        $text[$j]         = '';

        for ($i = 0; $i < strlen($note2); $i++)
        {

            if ($note2[$i] == '#')
            {
                $space = 1;
            }

            if ($space == 1)
            {

                if ($note2[$i] != ' ')
                {
                    $template .= $note2[$i];
                }
                else
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                    $text[$j] .= $note2[$i];
                }

                if ($i == strlen($note2) - 1)
                {
                    $res = $this->general_model->getRecords('*', 'note_template', array(
                        'hash_tag' => $template, 'delete_status' => 0));

                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j] = 'match';
                        $j++;
                        $text[$j] = '';
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                }

            }
            else
            {
                $text[$j] .= $note2[$i];
            }

        }
        $data_note2     = $text;
        $data_template2 = $template_content;
        $note_data      = array('note1' => $data_note1, 'template1' => $data_template1,
            'note2'                         => $data_note2, 'template2' => $data_template2);
        return $note_data;
    }

    public function sales_vouchers($data_main, $js_data, $ledgers, $currency)
    {
        $vouchers = array();

        if (isset($ledgers['freight_charge_ledger_id']) || isset($ledgers['insurance_charge_ledger_id']) || isset($ledgers['incidental_charge_ledger_id']) || isset($ledgers['packing_charge_ledger_id']) || isset($ledgers['other_inclusive_charge_ledger_id']) || isset($ledgers['other_exclusive_charge_ledger_id']))
        {
            $sales_grand_total = $data_main['sales_grand_total'];
        }
        else
        {
            $sales_grand_total = bcsub($data_main['sales_grand_total'], $data_main['total_other_amount'], 2);
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $converted_voucher_amount = $sales_grand_total;
        }
        else
        {
            $converted_voucher_amount = "0.00";
        }

        $vouchers[] = array("ledger_from" => $ledgers['ledger_from'],
            "ledger_to"                       => $ledgers['ledger_to'],
            "sales_voucher_id"                => '', "voucher_amount"                   => $sales_grand_total,
            "converted_voucher_amount"        => $converted_voucher_amount, "dr_amount" => $sales_grand_total,
            "cr_amount"                       => '');

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $converted_voucher_amount = $data_main['sales_sub_total'];
        }
        else
        {
            $converted_voucher_amount = "0.00";
        }

        $vouchers[] = array("ledger_from" => $ledgers['ledger_to'],
            "ledger_to"                       => $ledgers['ledger_from'],
            "sales_voucher_id"                => '', "voucher_amount"                   => $data_main['sales_sub_total'],
            "converted_voucher_amount"        => $converted_voucher_amount, "dr_amount" => '',
            "cr_amount"                       => $data_main['sales_sub_total']);
        $igst_sum     = 0;
        $cgst_sum     = 0;
        $sgst_sum     = 0;
        $discount_sum = 0;

        foreach ($js_data as $key => $value)
        {
            $igst_sum     = bcadd($igst_sum, $value['sales_item_igst_amount'], 2);
            $cgst_sum     = bcadd($cgst_sum, $value['sales_item_cgst_amount'], 2);
            $sgst_sum     = bcadd($sgst_sum, $value['sales_item_sgst_amount'], 2);
            $discount_sum = bcadd($discount_sum, $value['sales_item_discount_amount'], 2);
        }

        if (isset($ledgers['freight_charge_ledger_id']) || isset($ledgers['insurance_charge_ledger_id']) || isset($ledgers['incidental_charge_ledger_id']) || isset($ledgers['packing_charge_ledger_id']) || isset($ledgers['other_inclusive_charge_ledger_id']) || isset($ledgers['other_exclusive_charge_ledger_id']))
        {
            $other_tax_sum = ($data_main['freight_charge_tax_amount'] + $data_main['insurance_charge_tax_amount'] + $data_main['packing_charge_tax_amount'] + $data_main['incidental_charge_tax_amount'] + $data_main['inclusion_other_charge_tax_amount'] - $data_main['exclusion_other_charge_tax_amount']);

            if ($cgst_sum > 0 || $sgst_sum > 0)
            {
                $other_tax_sum = $other_tax_sum / 2;
                $cgst_sum      = bcadd($cgst_sum, $other_tax_sum, 2);
                $sgst_sum      = bcadd($sgst_sum, $other_tax_sum, 2);
            }
            else
            {
                $igst_sum = bcadd($igst_sum, $other_tax_sum, 2);
            }

        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $converted_voucher_amount = $igst_sum;
        }
        else
        {
            $converted_voucher_amount = "0.00";
        }

        $vouchers[] = array("ledger_from" => $ledgers['igst_ledger_id'],
            "ledger_to"                       => $ledgers['ledger_to'],
            "sales_voucher_id"                => '', "voucher_amount" => $igst_sum,
            "converted_voucher_amount"        => $converted_voucher_amount,
            "dr_amount"                       => '', "cr_amount"      => $igst_sum);

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $converted_voucher_amount = $cgst_sum;
        }
        else
        {
            $converted_voucher_amount = "0.00";
        }

        $vouchers[] = array("ledger_from" => $ledgers['cgst_ledger_id'],
            "ledger_to"                       => $ledgers['ledger_to'],
            "sales_voucher_id"                => '', "voucher_amount" => $cgst_sum,
            "converted_voucher_amount"        => $converted_voucher_amount,
            "dr_amount"                       => '', "cr_amount"      => $cgst_sum);

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $converted_voucher_amount = $sgst_sum;
        }
        else
        {
            $converted_voucher_amount = "0.00";
        }

        $vouchers[] = array("ledger_from" => $ledgers['sgst_ledger_id'],
            "ledger_to"                       => $ledgers['ledger_to'],
            "sales_voucher_id"                => '', "voucher_amount" => $sgst_sum,
            "converted_voucher_amount"        => $converted_voucher_amount,
            "dr_amount"                       => '', "cr_amount"      => $sgst_sum);

        if (isset($ledgers['freight_charge_ledger_id']))
        {

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $converted_voucher_amount = $data_main['freight_charge_amount'];
            }
            else
            {
                $converted_voucher_amount = "0.00";
            }

            $vouchers[] = array("ledger_from" => $ledgers['freight_charge_ledger_id'],
                "ledger_to"                       => $ledgers['ledger_to'], "sales_voucher_id" => '',
                "voucher_amount"                  => $data_main['freight_charge_amount'],
                "converted_voucher_amount"        => $converted_voucher_amount,
                "dr_amount"                       => '', "cr_amount"                           => $data_main['freight_charge_amount']);
        }

        if (isset($ledgers['insurance_charge_ledger_id']))
        {

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $converted_voucher_amount = $data_main['insurance_charge_amount'];
            }
            else
            {
                $converted_voucher_amount = "0.00";
            }

            $vouchers[] = array("ledger_from" => $ledgers['insurance_charge_ledger_id'],
                "ledger_to"                       => $ledgers['ledger_to'], "sales_voucher_id" => '',
                "voucher_amount"                  => $data_main['insurance_charge_amount'],
                "converted_voucher_amount"        => $converted_voucher_amount,
                "dr_amount"                       => '', "cr_amount"                           => $data_main['insurance_charge_amount']);
        }

        if (isset($ledgers['packing_charge_ledger_id']))
        {

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $converted_voucher_amount = $data_main['packing_charge_amount'];
            }
            else
            {
                $converted_voucher_amount = "0.00";
            }

            $vouchers[] = array("ledger_from" => $ledgers['packing_charge_ledger_id'],
                "ledger_to"                       => $ledgers['ledger_to'], "sales_voucher_id" => '',
                "voucher_amount"                  => $data_main['packing_charge_amount'],
                "converted_voucher_amount"        => $converted_voucher_amount,
                "dr_amount"                       => '', "cr_amount"                           => $data_main['packing_charge_amount']);
        }

        if (isset($ledgers['incidental_charge_ledger_id']))
        {

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $converted_voucher_amount = $data_main['incidental_charge_amount'];
            }
            else
            {
                $converted_voucher_amount = "0.00";
            }

            $vouchers[] = array("ledger_from" => $ledgers['incidental_charge_ledger_id'],
                "ledger_to"                       => $ledgers['ledger_to'], "sales_voucher_id" => '',
                "voucher_amount"                  => $data_main['incidental_charge_amount'],
                "converted_voucher_amount"        => $converted_voucher_amount,
                "dr_amount"                       => '', "cr_amount"                           => $data_main['incidental_charge_amount']);
        }

        if (isset($ledgers['other_inclusive_charge_ledger_id']))
        {

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $converted_voucher_amount = $data_main['inclusion_other_charge_amount'];
            }
            else
            {
                $converted_voucher_amount = "0.00";
            }

            $vouchers[] = array("ledger_from" => $ledgers['other_inclusive_charge_ledger_id'],
                "ledger_to"                       => $ledgers['ledger_to'], "sales_voucher_id" => '',
                "voucher_amount"                  => $data_main['inclusion_other_charge_amount'],
                "converted_voucher_amount"        => $converted_voucher_amount, "dr_amount"    => '',
                "cr_amount"                       => $data_main['inclusion_other_charge_amount']);
        }

        if (isset($ledgers['other_exclusive_charge_ledger_id']))
        {

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $converted_voucher_amount = $data_main['exclusion_other_charge_amount'];
            }
            else
            {
                $converted_voucher_amount = "0.00";
            }

            $vouchers[] = array("ledger_from" => $ledgers['other_exclusive_charge_ledger_id'],
                "ledger_to"                       => $ledgers['ledger_from'], "sales_voucher_id" => '',
                "voucher_amount"                  => $data_main['exclusion_other_charge_amount'],
                "converted_voucher_amount"        => $converted_voucher_amount, "dr_amount"      => $data_main['exclusion_other_charge_amount'],
                "cr_amount"                       => '');
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $converted_voucher_amount = $discount_sum;
        }
        else
        {
            $converted_voucher_amount = "0.00";
        }

        $vouchers[] = array("ledger_from" => $ledgers['discount_ledger_id'],
            "ledger_to"                       => $ledgers['ledger_from'],
            "sales_voucher_id"                => '', "voucher_amount"       => $discount_sum,
            "converted_voucher_amount"        => $converted_voucher_amount,
            "dr_amount"                       => $discount_sum, "cr_amount" => '');
        return $vouchers;
    }

    public function sales_voucher_entry($data_main, $js_data, $action, $currency)
    {
        $invoice_from            = $data_main['from_account'];
        $invoice_to              = $data_main['to_account'];
        $igst_ledger_id          = $this->ledger_model->getDefaultLedger('IGST');
        $cgst_ledger_id          = $this->ledger_model->getDefaultLedger('CGST');
        $sgst_ledger_id          = $this->ledger_model->getDefaultLedger('SGST');
        $discount_ledger_id      = $this->ledger_model->getDefaultLedger('Discount Given');
        $sales_ledger_id         = $this->ledger_model->getDefaultLedger('Sales');
        $sales_voucher_module_id = $this->config->item('sales_voucher_module');
        $module_id               = $sales_voucher_module_id;
        $modules                 = $this->get_modules();
        $privilege               = "add_privilege";
        $section_modules         = $this->get_section_modules($sales_voucher_module_id, $modules, $privilege);
        $access_sub_modules      = $section_modules['access_sub_modules'];
        $charges_sub_module_id   = $this->config->item('charges_sub_module');
        $access_settings         = $section_modules['access_settings'];
        $primary_id              = "sales_voucher_id";
        $table_name              = $this->config->item('sales_voucher_table');
        $date_field_name         = "voucher_date";
        $current_date            = $data_main['sales_date'];
        $voucher_number          = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $ledgers                 = array("igst_ledger_id" => $igst_ledger_id,
            "cgst_ledger_id"                                  => $cgst_ledger_id,
            "sgst_ledger_id"                                  => $sgst_ledger_id, "discount_ledger_id" => $discount_ledger_id,
            "sales_ledger_id"                                 => $sales_ledger_id);

        if ($invoice_from == "customer")
        {
            $string = '*';
            $table  = 'customer';
            // $join['contact_person c'] = 'c.contact_person_id=cust.customer_contact_person_id';
            $where              = array('customer_id' => $data_main['sales_party_id']);
            $customer_data      = $this->general_model->getRecords($string, $table, $where, $order = "");
            $customer_ledger_id = $customer_data[0]->ledger_id;
        }
        else
        {
            $title              = "CUSTOMER";
            $subgroup           = "Customer";
            $customer_ledger_id = $this->ledger_model->addLedger($title, $subgroup);
        }

        foreach ($access_sub_modules as $key => $value)
        {

            if (isset($charges_sub_module_id))
            {

                if ($charges_sub_module_id == $value->sub_module_id)
                {
                    $freight_charge_ledger_id                    = $this->ledger_model->getDefaultLedger('Freight Charge Received');
                    $insurance_charge_ledger_id                  = $this->ledger_model->getDefaultLedger('Insurance Charge Received');
                    $packing_charge_ledger_id                    = $this->ledger_model->getDefaultLedger('Packing Charge Received');
                    $incidental_charge_ledger_id                 = $this->ledger_model->getDefaultLedger('Incidental Charge Received');
                    $other_inclusive_charge_ledger_id            = $this->ledger_model->getDefaultLedger('Other Inclusive Charge Received');
                    $other_exclusive_charge_ledger_id            = $this->ledger_model->getDefaultLedger('Other Exclusive Charge Given');
                    $ledgers['freight_charge_ledger_id']         = $freight_charge_ledger_id;
                    $ledgers['insurance_charge_ledger_id']       = $insurance_charge_ledger_id;
                    $ledgers['packing_charge_ledger_id']         = $packing_charge_ledger_id;
                    $ledgers['incidental_charge_ledger_id']      = $incidental_charge_ledger_id;
                    $ledgers['other_inclusive_charge_ledger_id'] = $other_inclusive_charge_ledger_id;
                    $ledgers['other_exclusive_charge_ledger_id'] = $other_exclusive_charge_ledger_id;
                }

            }

        }
        $ledger_from                   = $customer_ledger_id;
        $ledgers['ledger_from']        = $ledger_from;
        $ledgers['customer_ledger_id'] = $ledger_from;
        $ledgers['ledger_to']          = $sales_ledger_id;
        $vouchers                      = $this->sales_vouchers($data_main, $js_data, $ledgers, $currency);
        $table                         = 'sales_voucher';
        $reference_key                 = 'sales_voucher_id';
        $reference_table               = 'accounts_sales_voucher';

        if ($action == "add")
        {

            $headers = array("voucher_date" => $data_main['sales_date'], "voucher_number"           => $voucher_number,
                "party_id"                      => $data_main['sales_party_id'], "party_type"           => $data_main['sales_party_type'],
                "reference_id"                  => $data_main['sales_id'], "reference_type"             => 'sales',
                "reference_number"              => $data_main['sales_invoice_number'], "receipt_amount" => $data_main['sales_grand_total'],
                "from_account"                  => $data_main['from_account'], "to_account"             => $data_main['to_account'],
                "financial_year_id"             => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "description"                   => '', "added_date"                                     => date('Y-m-d'),
                "added_user_id"                 => $this->session->userdata('SESS_USER_ID'),
                "branch_id"                     => $this->session->userdata('SESS_BRANCH_ID'),
                "currency_id"                   => $currency,
                "note1"                         => $data_main['note1'], "note2"                         => $data_main['note2']);

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $headers['converted_receipt_amount'] = $data_main['sales_grand_total'];
            }
            else
            {
                $headers['converted_receipt_amount'] = "0.00";
            }

            $this->general_model->addVouchers($table, $reference_key, $reference_table, $headers, $vouchers);
        }
        else
        if ($action == "edit")
        {
            $headers = array("voucher_date" => $data_main['sales_date'], "voucher_number"           => $voucher_number,
                "party_id"                      => $data_main['sales_party_id'], "party_type"           => $data_main['sales_party_type'],
                "reference_id"                  => $data_main['sales_id'], "reference_type"             => 'sales',
                "reference_number"              => $data_main['sales_invoice_number'], "receipt_amount" => $data_main['sales_grand_total'],
                "from_account"                  => $data_main['from_account'], "to_account"             => $data_main['to_account'],
                "financial_year_id"             => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "description"                   => '', "branch_id"                                      => $this->session->userdata('SESS_BRANCH_ID'),
                "currency_id"                   => $currency, "updated_date"                            => date('Y-m-d'),
                "updated_user_id"               => $this->session->userdata('SESS_USER_ID'),
                "note1"                         => $data_main['note1'], "note2"                         => $data_main['note2']);

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $headers['converted_receipt_amount'] = $data_main['sales_grand_total'];
            }
            else
            {
                $headers['converted_receipt_amount'] = "0.00";
            }

            $sales_voucher_data = $this->general_model->getRecords('sales_voucher_id', 'sales_voucher', array(
                'reference_id' => $data_main['sales_id'], 'delete_status' => 0));

            if ($sales_voucher_data)
            {
                $sales_voucher_id = $sales_voucher_data[0]->sales_voucher_id;
                $this->general_model->updateData('sales_voucher', $headers, array(
                    'sales_voucher_id' => $sales_voucher_id));
                $string = 'accounts_sales_id';
                $table  = 'accounts_sales_voucher';
                $where  = array('sales_voucher_id' => $sales_voucher_id,
                    'delete_status'                    => 0);
                $old_sales_voucher_items = $this->general_model->getRecords($string, $table, $where, $order = "");

                if (count($old_sales_voucher_items) == count($vouchers))
                {

                    foreach ($old_sales_voucher_items as $key => $value)
                    {
                        $vouchers[$key]['sales_voucher_id'] = $sales_voucher_id;
                        $table                              = 'accounts_sales_voucher';
                        $where                              = array('accounts_sales_id' => $value->accounts_sales_id);
                        $this->general_model->updateData($table, $vouchers[$key], $where);
                    }

                }
                else
                if (count($old_sales_voucher_items) < count($vouchers))
                {
                    $i = 0;

                    foreach ($old_sales_voucher_items as $key => $value)
                    {
                        $vouchers[$key]['sales_voucher_id'] = $sales_voucher_id;
                        $table                              = 'accounts_sales_voucher';
                        $where                              = array('accounts_sales_id' => $value->accounts_sales_id);
                        $this->general_model->updateData($table, $vouchers[$key], $where);
                        $i = $key;
                    }

                    for ($j = $i + 1; $j < count($vouchers); $j++)
                    {
                        $vouchers[$j]['sales_voucher_id'] = $sales_voucher_id;
                        $table                            = 'accounts_sales_voucher';
                        $this->general_model->insertData($table, $vouchers[$j]);
                    }

                }
                else
                {
                    $i = 0;

                    foreach ($old_sales_voucher_items as $key => $value)
                    {
                        $vouchers[$key]['sales_voucher_id'] = $sales_voucher_id;
                        $table                              = 'accounts_sales_voucher';
                        $where                              = array('accounts_sales_id' => $value->accounts_sales_id);
                        $this->general_model->updateData($table, $vouchers[$key], $where);
                        $i = $key;

                        if (($key + 1) == count($vouchers))
                        {
                            break;
                        }

                    }

                    for ($j = $i + 1; $j < count($old_sales_voucher_items); $j++)
                    {
                        $table      = 'accounts_sales_voucher';
                        $where      = array('accounts_sales_id' => $old_sales_voucher_items[$j]->accounts_sales_id);
                        $sales_data = array('delete_status' => 1);
                        $this->general_model->updateData($table, $sales_data, $where);
                    }

                }

            }

        }

    }

    public function expense_bill_voucher_entry($data_main, $js_data, $operation, $currency)
    {
        $supplier = $this->general_model->getRecords('ledger_id,supplier_name', 'supplier', array(
            'supplier_id' => $data_main['expense_bill_payee_id']));
        $ledger_supplier = $supplier[0]->ledger_id;

        foreach ($js_data as $key => $value)
        {
            $expense[] = $this->general_model->getRecords('ledger_id,expense_title', 'expense', array(
                'expense_id' => $value['expense_type_id']));
        }

        $expense_array_length = count($expense);
        $ledger_igst          = $this->ledger_model->getDefaultLedger('IGST');
        $ledger_cgst          = $this->ledger_model->getDefaultLedger('CGST');
        $ledger_sgst          = $this->ledger_model->getDefaultLedger('SGST');
        $ledger_tds           = $this->ledger_model->getDefaultLedger('TDS');
        $ledger_id            = array('ledger_supplier' => $ledger_supplier, 'ledger_igst' => $ledger_igst,
            'ledger_cgst'                                   => $ledger_cgst, 'ledger_sgst'     => $ledger_sgst,
            'ledger_tds'                                    => $ledger_tds);
        $ledger_entry = array('grand_total' => $data_main['expense_bill_grand_total'],
            'sub_total'                         => $data_main['expense_bill_sub_total'], 'igst_amount'   => $data_main['expense_bill_igst_amount'],
            'cgst_amount'                       => $data_main['expense_bill_cgst_amount'], 'sgst_amount' => $data_main['expense_bill_sgst_amount'],
            'tds_amount'                        => $data_main['expense_bill_tds_amount']);

        if ($operation == 'add')
        {
            $expense_voucher_module_id = $this->config->item('expense_voucher_module');
            $data['module_id']         = $expense_voucher_module_id;
            $modules                   = $this->get_modules();
            $privilege                 = "add_privilege";
            $data['privilege']         = "add_privilege";
            $section_modules           = $this->get_section_modules($expense_voucher_module_id, $modules, $privilege);
            $modules_present           = array('accounts_module_id' => $this->config->item('accounts_module'));
            $other_modules_present     = $this->other_modules_present($modules_present, $modules['modules']);
            $access_settings           = $section_modules['access_settings'];

            if ($access_settings[0]->invoice_creation == "automatic")
            {
                $primary_id      = "expense_voucher_id";
                $table_name      = 'expense_voucher';
                $date_field_name = "voucher_date";
                $current_date    = $data_main['expense_bill_date'];
                $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            }
            else
            {
                $invoice_number = $data_main['expense_bill_invoice_number'];
            }

            $expense_voucher_data = array('branch_id' => $data_main['branch_id'],
                'party_id'                                => $data_main['expense_bill_payee_id'], 'party_type' => $data_main['expense_bill_payee_type'],
                'voucher_number'                          => $invoice_number, 'voucher_type'                   => 'expense_bill',
                'voucher_date'                            => $data_main['expense_bill_date'], 'description'    => "",
                'reference_id'                            => $data_main['expense_bill_id'], 'from_account'     => 'expense-' . $expense[0][0]->expense_title,
                'to_account'                              => 'supplier-' . $supplier[0]->supplier_name,
                'reference_type'                          => 'expense_bill',
                'reference_number'                        => $data_main['expense_bill_invoice_number'],
                'added_user_id'                           => $data_main['added_user_id'], 'added_date'         => $data_main['added_date'],
                'currency_id'                             => $data_main['currency_id'], 'financial_year_id'    => $data_main['financial_year_id'],
                'receipt_amount'                          => $data_main['expense_bill_grand_total'],
                'note1'                                   => $data_main['note1'],
                'note2'                                   => $data_main['note2']);

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $expense_voucher_data['converted_grand_total'] = $data_main['expense_bill_grand_total'];
            }
            else
            {
                $expense_voucher_data['converted_grand_total'] = "0.00";
            }

            $expense_id = $this->general_model->insertData('expense_voucher', $expense_voucher_data);
            $data1      = array('expense_voucher_id' => $expense_id, 'ledger_from'                  => $ledger_id['ledger_supplier'],
                'ledger_to'                              => $expense[0][0]->ledger_id, 'voucher_amount' => $ledger_entry['grand_total'],
                'dr_amount'                              => "0.00", 'cr_amount'                         => $ledger_entry['grand_total']);

            $data2 = array('expense_voucher_id' => $expense_id, 'ledger_from'                      => $ledger_id['ledger_tds'],
                'ledger_to'                         => $ledger_id['ledger_supplier'], 'voucher_amount' => $ledger_entry['tds_amount'],
                'dr_amount'                         => "0.00", 'cr_amount'                             => $ledger_entry['tds_amount']);

            $i = 0;

            foreach ($js_data as $key => $value)
            {

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
                {
                    $data3[] = array('expense_voucher_id' => $expense_id, 'ledger_from'         => $expense[$i][0]->ledger_id,
                        'ledger_to'                           => $ledger_id['ledger_supplier'],
                        'voucher_amount'                      => $value['expense_bill_item_sub_total'],
                        'dr_amount'                           => $value['expense_bill_item_sub_total'],
                        'cr_amount'                           => "0.00", 'converted_voucher_amount' => $value['expense_bill_item_sub_total']);
                    // $data3[]['converted_voucher_amount'] = $value['expense_bill_item_sub_total'];
                }
                else
                {
                    $data3[] = array('expense_voucher_id' => $expense_id, 'ledger_from'         => $expense[$i][0]->ledger_id,
                        'ledger_to'                           => $ledger_id['ledger_supplier'],
                        'voucher_amount'                      => $value['expense_bill_item_sub_total'],
                        'dr_amount'                           => $value['expense_bill_item_sub_total'],
                        'cr_amount'                           => "0.00", 'converted_voucher_amount' => '0.00');
                }

                $i++;
            }

            $data4 = array('expense_voucher_id' => $expense_id, 'ledger_from'                      => $ledger_id['ledger_igst'],
                'ledger_to'                         => $ledger_id['ledger_supplier'], 'voucher_amount' => $ledger_entry['igst_amount'],
                'dr_amount'                         => $ledger_entry['igst_amount'], 'cr_amount'       => "0.00");
            $data5 = array('expense_voucher_id' => $expense_id, 'ledger_from'                      => $ledger_id['ledger_cgst'],
                'ledger_to'                         => $ledger_id['ledger_supplier'], 'voucher_amount' => $ledger_entry['cgst_amount'],
                'dr_amount'                         => $ledger_entry['cgst_amount'], 'cr_amount'       => "0.00");
            $data6 = array('expense_voucher_id' => $expense_id, 'ledger_from'                      => $ledger_id['ledger_sgst'],
                'ledger_to'                         => $ledger_id['ledger_supplier'], 'voucher_amount' => $ledger_entry['sgst_amount'],
                'dr_amount'                         => $ledger_entry['sgst_amount'], 'cr_amount'       => "0.00");

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $data1['converted_voucher_amount'] = $ledger_entry['grand_total'];
                $data2['converted_voucher_amount'] = $ledger_entry['tds_amount'];
                $data4['converted_voucher_amount'] = $ledger_entry['igst_amount'];
                $data5['converted_voucher_amount'] = $ledger_entry['cgst_amount'];
                $data6['converted_voucher_amount'] = $ledger_entry['sgst_amount'];
            }
            else
            {
                $data1['converted_voucher_amount'] = "0.00";
                $data2['converted_voucher_amount'] = "0.00";
                $data4['converted_voucher_amount'] = "0.00";
                $data5['converted_voucher_amount'] = "0.00";
                $data6['converted_voucher_amount'] = "0.00";
            }

            $this->general_model->insertData('accounts_expense_voucher', $data1);
            $this->general_model->insertData('accounts_expense_voucher', $data2);
            $this->general_model->insertBatchData('accounts_expense_voucher', $data3);
            $this->general_model->insertData('accounts_expense_voucher', $data4);
            $this->general_model->insertData('accounts_expense_voucher', $data5);
            $this->general_model->insertData('accounts_expense_voucher', $data6);
        }
        elseif ($operation == 'edit')
        {
            $expense_voucher_module_id = $this->config->item('expense_voucher_module');
            $data['module_id']         = $expense_voucher_module_id;
            $modules                   = $this->get_modules();
            $privilege                 = "edit_privilege";
            $data['privilege']         = "edit_privilege";
            $section_modules           = $this->get_section_modules($expense_voucher_module_id, $modules, $privilege);
            $modules_present           = array('accounts_module_id' => $this->config->item('accounts_module'));
            $other_modules_present     = $this->other_modules_present($modules_present, $modules['modules']);
            $access_settings           = $section_modules['access_settings'];

            if ($access_settings[0]->invoice_creation == "automatic")
            {
                $primary_id      = "expense_voucher_id";
                $table_name      = 'expense_voucher';
                $date_field_name = "voucher_date";
                $current_date    = $data_main['expense_bill_date'];
                $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            }
            else
            {
                $invoice_number = $data_main['expense_bill_invoice_number'];
            }

            $expense_voucher_data = array('party_id' => $data_main['expense_bill_payee_id'],
                'party_type'                             => $data_main['expense_bill_payee_type'],
                'voucher_number'                         => $invoice_number,
                'voucher_type'                           => 'expense_bill', 'voucher_date'                 => $data_main['expense_bill_date'],
                'description'                            => "", 'reference_id'                             => $data_main['expense_bill_id'],
                'from_account'                           => 'expense-' . $expense[0][0]->expense_title,
                'to_account'                             => 'supplier-' . $supplier[0]->supplier_name,
                'reference_type'                         => 'expense_bill',
                'reference_number'                       => $data_main['expense_bill_invoice_number'],
                'updated_user_id'                        => $data_main['updated_user_id'], 'updated_date'  => $data_main['updated_date'],
                'currency_id'                            => $data_main['currency_id'], 'financial_year_id' => $data_main['financial_year_id'],
                'receipt_amount'                         => $data_main['expense_bill_grand_total'],
                'note1'                                  => $data_main['note1'],
                'note2'                                  => $data_main['note2']);

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $expense_voucher_data['converted_grand_total'] = $data_main['expense_bill_grand_total'];
            }
            else
            {
                $expense_voucher_data['converted_grand_total'] = "0.00";
            }

            $expense_voucher = $this->general_model->getRecords('expense_voucher_id', 'expense_voucher', array(
                'reference_id' => $data_main['expense_bill_id'], 'delete_status' => 0));

            if ($expense_voucher)
            {
                $expense_id = $expense_voucher[0]->expense_voucher_id;
                $this->general_model->updateData('expense_voucher', $expense_voucher_data, array(
                    'expense_voucher_id' => $expense_id));

                if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
                {
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_supplier'],
                        'ledger_to'                                  => $expense[0][0]->ledger_id,
                        'voucher_amount'                             => $ledger_entry['grand_total'],
                        'converted_voucher_amount'                   => $ledger_entry['grand_total'],
                        'dr_amount'                                  => "0.00", 'cr_amount' => $ledger_entry['grand_total']);
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_tds'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['tds_amount'],
                        'converted_voucher_amount'                   => $ledger_entry['tds_amount'],
                        'dr_amount'                                  => "0.00", 'cr_amount' => $ledger_entry['tds_amount']);
                    $n = 0;

                    foreach ($js_data as $key => $value)
                    {
                        $voucher_data[] = array('expense_voucher_id' => $expense_id,
                            'ledger_from'                                => $expense[$n][0]->ledger_id,
                            'ledger_to'                                  => $ledger_id['ledger_supplier'],
                            'voucher_amount'                             => $value['expense_bill_item_sub_total'],
                            'converted_voucher_amount'                   => $value['expense_bill_item_sub_total'],
                            'dr_amount'                                  => $value['expense_bill_item_sub_total'],
                            'cr_amount'                                  => "0.00");
                        $n++;
                    }

                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_igst'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['igst_amount'],
                        'converted_voucher_amount'                   => $ledger_entry['igst_amount'],
                        'dr_amount'                                  => $ledger_entry['igst_amount'],
                        'cr_amount'                                  => "0.00");
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_cgst'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['cgst_amount'],
                        'converted_voucher_amount'                   => $ledger_entry['cgst_amount'],
                        'dr_amount'                                  => $ledger_entry['cgst_amount'],
                        'cr_amount'                                  => "0.00");
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_sgst'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['sgst_amount'],
                        'converted_voucher_amount'                   => $ledger_entry['sgst_amount'],
                        'dr_amount'                                  => $ledger_entry['sgst_amount'],
                        'cr_amount'                                  => "0.00");
                }
                else
                {
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_supplier'],
                        'ledger_to'                                  => $expense[0][0]->ledger_id,
                        'voucher_amount'                             => $ledger_entry['grand_total'],
                        'converted_voucher_amount'                   => '0.00',
                        'dr_amount'                                  => "0.00", 'cr_amount' => $ledger_entry['grand_total']);
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_tds'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['tds_amount'],
                        'converted_voucher_amount'                   => '0.00',
                        'dr_amount'                                  => "0.00", 'cr_amount' => $ledger_entry['tds_amount']);
                    $n = 0;

                    foreach ($js_data as $key => $value)
                    {
                        $voucher_data[] = array('expense_voucher_id' => $expense_id,
                            'ledger_from'                                => $expense[$n][0]->ledger_id,
                            'ledger_to'                                  => $ledger_id['ledger_supplier'],
                            'voucher_amount'                             => $value['expense_bill_item_sub_total'],
                            'converted_voucher_amount'                   => '0.00', 'dr_amount' => $value['expense_bill_item_sub_total'],
                            'cr_amount'                                  => "0.00");
                        $n++;
                    }

                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_igst'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['igst_amount'],
                        'converted_voucher_amount'                   => '0.00',
                        'dr_amount'                                  => $ledger_entry['igst_amount'],
                        'cr_amount'                                  => "0.00");
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_cgst'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['cgst_amount'],
                        'converted_voucher_amount'                   => '0.00',
                        'dr_amount'                                  => $ledger_entry['cgst_amount'],
                        'cr_amount'                                  => "0.00");
                    $voucher_data[] = array('expense_voucher_id' => $expense_id,
                        'ledger_from'                                => $ledger_id['ledger_sgst'],
                        'ledger_to'                                  => $ledger_id['ledger_supplier'],
                        'voucher_amount'                             => $ledger_entry['sgst_amount'],
                        'converted_voucher_amount'                   => '0.00',
                        'dr_amount'                                  => $ledger_entry['sgst_amount'],
                        'cr_amount'                                  => "0.00");
                }

                $old_accounts_expense = $this->general_model->getRecords('accounts_expense_id', 'accounts_expense_voucher', array(
                    'expense_voucher_id' => $expense_id, 'delete_status' => 0));
                $table = 'accounts_expense_voucher';

                if (count($old_accounts_expense) == count($voucher_data))
                {

                    foreach ($old_accounts_expense as $key => $value)
                    {
                        $where = array('accounts_expense_id' => $value->accounts_expense_id);
                        $this->general_model->updateData($table, $voucher_data[$key], $where);
                    }

                }
                else
                if (count($old_accounts_expense) < count($voucher_data))
                {

                    foreach ($old_accounts_expense as $key => $value)
                    {
                        $where = array('accounts_expense_id' => $value->accounts_expense_id);
                        $this->general_model->updateData($table, $voucher_data[$key], $where);
                        $i = $key;
                    }

                    for ($j = $i + 1; $j < count($voucher_data); $j++)
                    {
                        $this->general_model->insertData($table, $voucher_data[$j]);
                    }

                }
                else
                {

                    foreach ($old_accounts_expense as $key => $value)
                    {
                        $where = array('accounts_expense_id' => $value->accounts_expense_id);
                        $this->general_model->updateData($table, $voucher_data[$key], $where);
                        $i = $key;

                        if (($key + 1) == count($voucher_data))
                        {
                            break;
                        }

                    }

                    for ($j = $i + 1; $j < count($old_accounts_expense); $j++)
                    {
                        $where         = array('accounts_expense_id' => $old_accounts_expense[$j]->accounts_expense_id);
                        $accounts_data = array('delete_status' => 1);
                        $this->general_model->updateData($table, $accounts_data, $where);
                    }

                }

            }

        }

    }

    public function generate_recurrence_invoice()
    {
        $res = $this->general_model->getRecurrenceRecords();

        if ($res)
        {

            foreach ($res as $key => $value)
            {

                if ($value->invoice_type == 'sales')
                {
                    $sales_module_id = $this->config->item('sales_module');
                    $module_id       = $sales_module_id;
                    $modules         = $this->get_modules();
                    $privilege       = "add_privilege";
                    $section_modules = $this->get_section_modules($sales_module_id, $modules, $privilege);

// $data['access_modules']          = $section_modules['modules'];

// $data['access_sub_modules']      = $section_modules['sub_modules'];

// $data['access_module_privilege'] = $section_modules['module_privilege'];

// $data['access_user_privilege']   = $section_modules['user_privilege'];

// $data['access_settings']         = $section_modules['settings'];

// $data['access_common_settings']  = $section_modules['common_settings'];
                    // $data['accounts_sub_module_id']  = $this->config->item('accounts_sub_module');
                    $accounts_module_id            = $this->config->item('accounts_module');
                    $modules_present               = array('accounts_module_id' => $accounts_module_id);
                    $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
                    $access_settings               = $section_modules['access_settings'];

                    if ($access_settings[0]->invoice_creation == "automatic")
                    {
                        $primary_id      = "sales_id";
                        $table_name      = $this->config->item('sales_table');
                        $date_field_name = "sales_date";
                        $current_date    = $value->next_generation_date;
                        $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                    }
                    else
                    {
                        $invoice_number = $this->input->post('invoice_number');
                    }
                    $where     = array('sales_id' => $value->invoice_id, 'delete_status' => 0);
                    $table_rec = $this->general_model->getRecords('*', 'sales', $where);

                    if ($table_rec)
                    {
                        $sales_data = array("sales_date"        => $value->next_generation_date,
                            "sales_invoice_number"                  => $invoice_number,
                            "sales_sub_total"                       => $table_rec[0]->sales_sub_total,
                            "sales_grand_total"                     => $table_rec[0]->sales_grand_total,
                            "converted_grand_total"                 => $table_rec[0]->converted_grand_total,
                            "sales_discount_amount"                 => $table_rec[0]->sales_discount_amount,
                            "sales_tax_amount"                      => $table_rec[0]->sales_tax_amount,
                            "sales_taxable_value"                   => $table_rec[0]->sales_taxable_value,
                            "sales_igst_amount"                     => $table_rec[0]->sales_igst_amount,
                            "sales_cgst_amount"                     => $table_rec[0]->sales_cgst_amount,
                            "sales_sgst_amount"                     => $table_rec[0]->sales_sgst_amount,
                            "from_account"                          => $table_rec[0]->from_account,
                            "to_account"                            => $table_rec[0]->to_account,
                            "sales_paid_amount"                     => 0, "credit_note_amount" => 0,
                            "debit_note_amount"                     => 0, "financial_year_id"  => $table_rec[0]->financial_year_id,
                            "sales_party_id"                        => $table_rec[0]->sales_party_id,
                            "sales_party_type"                      => "customer",
                            "sales_nature_of_supply"                => $table_rec[0]->sales_nature_of_supply,
                            "sales_type_of_supply"                  => $table_rec[0]->sales_type_of_supply,
                            "sales_gst_payable"                     => $table_rec[0]->sales_gst_payable,
                            "sales_billing_country_id"              => $table_rec[0]->sales_billing_country_id,
                            "sales_billing_state_id"                => $table_rec[0]->sales_billing_state_id,
                            "added_date"                            => $value->next_generation_date,
                            "added_user_id"                         => $table_rec[0]->added_user_id,
                            "branch_id"                             => $table_rec[0]->branch_id,
                            "currency_id"                           => $table_rec[0]->currency_id,

// "updated_date"                      => "",
                            // "updated_user_id"                   => "",
                            "warehouse_id"                          => "",
                            "transporter_name"                      => $table_rec[0]->transporter_name,
                            "transporter_gst_number"                => $table_rec[0]->transporter_gst_number,
                            "lr_no"                                 => $table_rec[0]->lr_no,
                            "vehicle_no"                            => $table_rec[0]->vehicle_no,
                            "mode_of_shipment"                      => $table_rec[0]->mode_of_shipment,
                            "ship_by"                               => $table_rec[0]->ship_by,
                            "net_weight"                            => $table_rec[0]->net_weight,
                            "gross_weight"                          => $table_rec[0]->gross_weight,
                            "origin"                                => $table_rec[0]->origin,
                            "destination"                           => $table_rec[0]->destination,
                            "shipping_type"                         => $table_rec[0]->shipping_type,
                            "shipping_type_place"                   => $table_rec[0]->shipping_type_place,
                            "lead_time"                             => $table_rec[0]->lead_time,
                            "warranty"                              => $table_rec[0]->warranty,
                            "payment_mode"                          => $table_rec[0]->payment_mode,
                            "freight_charge_amount"                 => $table_rec[0]->freight_charge_amount,
                            "freight_charge_tax_percentage"         => $table_rec[0]->freight_charge_tax_percentage,
                            "freight_charge_tax_amount"             => $table_rec[0]->freight_charge_tax_amount,
                            "total_freight_charge"                  => $table_rec[0]->total_freight_charge,
                            "insurance_charge_amount"               => $table_rec[0]->insurance_charge_amount,
                            "insurance_charge_tax_percentage"       => $table_rec[0]->insurance_charge_tax_percentage,
                            "insurance_charge_tax_amount"           => $table_rec[0]->insurance_charge_tax_amount,
                            "total_insurance_charge"                => $table_rec[0]->total_insurance_charge,
                            "packing_charge_amount"                 => $table_rec[0]->packing_charge_amount,
                            "packing_charge_tax_percentage"         => $table_rec[0]->packing_charge_tax_percentage,
                            "packing_charge_tax_amount"             => $table_rec[0]->packing_charge_tax_amount,
                            "total_packing_charge"                  => $table_rec[0]->total_packing_charge,
                            "incidental_charge_amount"              => $table_rec[0]->incidental_charge_amount,
                            "incidental_charge_tax_percentage"      => $table_rec[0]->incidental_charge_tax_percentage,
                            "incidental_charge_tax_amount"          => $table_rec[0]->incidental_charge_tax_amount,
                            "total_incidental_charge"               => $table_rec[0]->total_incidental_charge,
                            "inclusion_other_charge_amount"         => $table_rec[0]->inclusion_other_charge_amount,
                            "inclusion_other_charge_tax_percentage" => $table_rec[0]->inclusion_other_charge_tax_percentage,
                            "inclusion_other_charge_tax_amount"     => $table_rec[0]->inclusion_other_charge_tax_amount,
                            "total_inclusion_other_charge"          => $table_rec[0]->total_inclusion_other_charge,
                            "exclusion_other_charge_amount"         => $table_rec[0]->exclusion_other_charge_amount,
                            "exclusion_other_charge_tax_percentage" => $table_rec[0]->exclusion_other_charge_tax_percentage,
                            "exclusion_other_charge_tax_amount"     => $table_rec[0]->exclusion_other_charge_tax_amount,
                            "total_exclusion_other_charge"          => $table_rec[0]->total_exclusion_other_charge,
                            "freight_charge_tax_id"                 => $table_rec[0]->freight_charge_tax_id,
                            "insurance_charge_tax_id"               => $table_rec[0]->insurance_charge_tax_id,
                            "packing_charge_tax_id"                 => $table_rec[0]->packing_charge_tax_id,
                            "incidental_charge_tax_id"              => $table_rec[0]->incidental_charge_tax_id,
                            "inclusion_other_charge_tax_id"         => $table_rec[0]->inclusion_other_charge_tax_id,
                            "exclusion_other_charge_tax_id"         => $table_rec[0]->exclusion_other_charge_tax_id,
                            "total_other_amount"                    => $table_rec[0]->total_other_amount,
                            "round_off_amount"                      => $table_rec[0]->round_off_amount,
                            "note1"                                 => $table_rec[0]->note1,
                            "note2"                                 => $table_rec[0]->note2
                        );
                        $data_main = array_map('trim', $sales_data);

                        if ($sales_id = $this->general_model->insertData('sales', $data_main))
                        {
                            $log_data = array('user_id' => $this->session->userdata('SESS_USER_ID'),
                                'table_id'                  => $sales_id, 'table_name' => 'sales',
                                'financial_year_id'         => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                'branch_id'                 => $this->session->userdata('SESS_BRANCH_ID'),
                                'message'                   => 'Recurrence Sales Invoice Inserted');
                            $data_main['sales_id'] = $sales_id;
                            $log_table             = $this->config->item('log_table');
                            $this->general_model->insertData($log_table, $log_data);
                            $where = array('sales_id' => $value->invoice_id,
                                'delete_status'           => 0);
                            $sales_item = $this->general_model->getRecords('*', 'sales_item', $where);
                            $js_data1   = array();

                            foreach ($sales_item as $k1 => $val1)
                            {
                                $item_data = array("item_id" => $val1->item_id,
                                    "item_type"                  => $val1->item_type,
                                    "sales_item_quantity"        => $val1->sales_item_quantity,
                                    "sales_item_unit_price"      => $val1->sales_item_unit_price,
                                    "sales_item_sub_total"       => $val1->sales_item_sub_total,
                                    "sales_item_taxable_value"   => $val1->sales_item_taxable_value,
                                    "sales_item_discount_amount" => $val1->sales_item_discount_amount,
                                    "sales_item_discount_id"     => $val1->sales_item_discount_id,
                                    "sales_item_grand_total"     => $val1->sales_item_grand_total,
                                    "sales_item_igst_percentage" => $val1->sales_item_igst_percentage,
                                    "sales_item_igst_amount"     => $val1->sales_item_igst_amount,
                                    "sales_item_cgst_percentage" => $val1->sales_item_cgst_percentage,
                                    "sales_item_cgst_amount"     => $val1->sales_item_cgst_amount,
                                    "sales_item_sgst_percentage" => $val1->sales_item_sgst_percentage,
                                    "sales_item_sgst_amount"     => $val1->sales_item_sgst_amount,
                                    "sales_item_tax_percentage"  => $val1->sales_item_tax_percentage,
                                    "sales_item_tax_amount"      => $val1->sales_item_tax_amount,
                                    "sales_item_description"     => $val1->sales_item_description,
                                    "debit_note_quantity"        => 0, "sales_id" => $sales_id);
                                $data_item  = array_map('trim', $item_data);
                                $js_data1[] = $data_item;

                                if ($this->general_model->insertData('sales_item', $data_item))
                                {

                                    if ($data_item['item_type'] == "product")
                                    {
                                        $product_data   = $this->common->product_field($data_item['item_id']);
                                        $product_result = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);

                                        if (isset($product_result) && $product_result)
                                        {
                                            $product_quantity = ($product_result[0]->product_quantity - $val1->sales_item_quantity);
                                            $paid_amount      = array('product_quantity' => $product_quantity);
                                            $where            = array('product_id' => $val1->item_id);
                                            $product_table    = $this->config->item('product_table');
                                            $this->general_model->updateData($product_table, $paid_amount, $where);
                                        }

                                    }
                                    else
                                    if ($data_item['item_type'] == "product_inventory")
                                    {
                                        $product_data   = $this->common->product_inventory_field($data_item['item_id']);
                                        $product_result = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);

                                        if (isset($product_result) && $product_result)
                                        {
                                            $product_quantity = ($product_result[0]->quantity - $val1->sales_item_quantity);
                                            $qty              = array(
                                                'quantity' => $product_quantity);
                                            $where = array(
                                                'product_inventory_varients_id' => $val1->item_id);
                                            $product_table = 'product_inventory_varients';
                                            $this->general_model->updateData($product_table, $qty, $where);

                                            // quantity history
                                            $history = array(
                                                "item_id"          => $val1->item_id,
                                                "item_type"        => 'product_inventory',
                                                "reference_id"     => $sales_id,
                                                "reference_number" => $invoice_number,
                                                "reference_type"   => 'sales',
                                                "quantity"         => $val1->sales_item_quantity,
                                                "stock_type"       => 'indirect',
                                                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                                                "added_date"       => date('Y-m-d'),
                                                "entry_date"       => date('Y-m-d'),
                                                "added_user_id"    => $this->session->userdata('SESS_USER_ID'));
                                            $this->general_model->insertData("quantity_history", $history);
                                        }

                                    }

                                }

                            }

                        }

                        if (isset($data['other_modules_present']['accounts_module_id']))
                        {

                            foreach ($data['access_sub_modules'] as $k2 => $val2)
                            {

                                if (isset($data['accounts_sub_module_id']))
                                {

                                    if ($data['accounts_sub_module_id'] == $val2->sub_module_id)
                                    {
                                        $action = "add";
                                        $this->sales_voucher_entry($data_main, $js_data1, $action, $table_rec[0]->currency_id);
                                    }

                                }

                            }

                        }

                    }

                }

                if ($value->invoice_type == 'expense_bill')
                {
                    $expense_bill_module_id = $this->config->item('expense_bill_module');
                    $data['module_id']      = $expense_bill_module_id;
                    $modules                = $this->get_modules();
                    $privilege              = "add_privilege";
                    $section_modules        = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);

                    $data = array_merge($data, $section_modules);

                    $accounts_module_id             = $this->config->item('accounts_module');
                    $data['accounts_sub_module_id'] = $this->config->item('accounts_sub_module');
                    $modules_present                = array('accounts_module_id' => $accounts_module_id);
                    $data['other_modules_present']  = $this->other_modules_present($modules_present, $modules['modules']);
                    $access_settings                = $section_modules['access_settings'];

                    if ($access_settings[0]->invoice_creation == "automatic")
                    {
                        $primary_id      = "expense_bill_id";
                        $table_name      = 'expense_bill';
                        $date_field_name = "expense_bill_date";
                        $current_date    = $value->next_generation_date;
                        $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                    }
                    else
                    {
                        $invoice_number = $this->input->post('invoice_number');
                    }

                    $where     = array('expense_bill_id' => $value->invoice_id, 'delete_status' => 0);
                    $table_rec = $this->general_model->getRecords('*', 'expense_bill', $where);

                    if ($table_rec)
                    {
                        $expense_bill_data = array("expense_bill_date" => $value->next_generation_date,
                            "expense_bill_invoice_number"                  => $invoice_number,
                            "expense_bill_payee_id"                        => $table_rec[0]->expense_bill_payee_id,
                            "expense_bill_payee_type"                      => $table_rec[0]->expense_bill_payee_type,
                            "expense_bill_transaction_type"                => $table_rec[0]->expense_bill_transaction_type,
                            "expense_bill_reference_number"                => $table_rec[0]->expense_bill_reference_number,
                            "expense_bill_sub_total"                       => $table_rec[0]->expense_bill_sub_total,
                            "expense_bill_tds_amount"                      => $table_rec[0]->expense_bill_tds_amount,
                            "expense_bill_net_amount"                      => $table_rec[0]->expense_bill_net_amount,
                            "expense_bill_igst_amount"                     => $table_rec[0]->expense_bill_igst_amount,
                            "expense_bill_cgst_amount"                     => $table_rec[0]->expense_bill_cgst_amount,
                            "expense_bill_sgst_amount"                     => $table_rec[0]->expense_bill_sgst_amount,
                            "expense_bill_tax_amount"                      => $table_rec[0]->expense_bill_tax_amount,
                            "expense_bill_grand_total"                     => $table_rec[0]->expense_bill_grand_total,
                            "currency_converted_amount"                    => $table_rec[0]->converted_grand_total,
                            "branch_id"                                    => $table_rec[0]->branch_id,
                            "financial_year_id"                            => $table_rec[0]->financial_year_id,
                            "currency_id"                                  => $table_rec[0]->currency_id,
                            "added_user_id"                                => $table_rec[0]->added_user_id,
                            "added_date"                                   => $value->next_generation_date,
                            "updated_user_id"                              => "",
                            "updated_date"                                 => "",
                            "note1"                                        => $table_rec[0]->note1,
                            "note2"                                        => $table_rec[0]->note2
                        );

                        $data_main = array_map('trim', $expense_bill_data);

                        if ($expense_bill_id = $this->general_model->insertData("expense_bill", $data_main))
                        {
                            $log_data = array('user_id' => $this->session->userdata('SESS_USER_ID'),
                                'table_id'                  => $expense_bill_id, 'table_name' => 'expense_bill',
                                'financial_year_id'         => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                'branch_id'                 => $this->session->userdata('SESS_BRANCH_ID'),
                                'message'                   => 'Recurrence Expense Bill Inserted');
                            $data_main['expense_bill_id'] = $expense_bill_id;
                            $log_table                    = $this->config->item('log_table');
                            $this->general_model->insertData($log_table, $log_data);

                            $expense_bill_item = $this->general_model->getRecords('*', 'expense_bill_item', array(
                                'expense_bill_id' => $value->invoice_id, 'delete_status' => 0));
                            $js_data1 = array();

                            foreach ($expense_bill_item as $k1 => $val1)
                            {
                                $item_data = array("expense_type_id" => $val1->expense_type_id,
                                    "expense_bill_item_description"      => $val1->expense_bill_item_description,
                                    "expense_bill_item_sub_total"        => $val1->expense_bill_item_sub_total,
                                    "expense_bill_item_tds_percentage"   => $val1->expense_bill_item_tds_percentage,
                                    "expense_bill_item_tds_amount"       => $val1->expense_bill_item_tds_amount,
                                    "expense_bill_item_net_amount"       => $val1->expense_bill_item_net_amount,
                                    "expense_bill_item_igst_percentage"  => $val1->expense_bill_item_igst_percentage,
                                    "expense_bill_item_igst_amount"      => $val1->expense_bill_item_igst_amount,
                                    "expense_bill_item_cgst_percentage"  => $val1->expense_bill_item_cgst_percentage,
                                    "expense_bill_item_cgst_amount"      => $val1->expense_bill_item_cgst_amount,
                                    "expense_bill_item_sgst_percentage"  => $val1->expense_bill_item_sgst_percentage,
                                    "expense_bill_item_sgst_amount"      => $val1->expense_bill_item_sgst_amount,
                                    "expense_bill_item_tax_percentage"   => $val1->expense_bill_item_tax_percentage,
                                    "expense_bill_item_tax_amount"       => $val1->expense_bill_item_tax_amount,
                                    "expense_bill_item_grand_total"      => $val1->expense_bill_item_grand_total,
                                    "expense_bill_id"                    => $expense_bill_id
                                );
                                $data_item  = array_map('trim', $item_data);
                                $js_data1[] = $data_item;
                                $this->general_model->insertData('expense_bill_item', $data_item);
                            }

                        }

                        if (isset($data['other_modules_present']['accounts_module_id']))
                        {

                            foreach ($data['access_sub_modules'] as $k1 => $val1)
                            {

                                if (isset($data['accounts_sub_module_id']))
                                {

                                    if ($data['accounts_sub_module_id'] == $val1->sub_module_id)
                                    {
                                        $this->expense_bill_voucher_entry($data_main, $js_data1, "add", $table_rec[0]->currency_id);
                                    }

                                }

                            }

                        }

                    }

                }

                $next_generation_date = date("Y-m-d", strtotime("+1 month", strtotime($value->next_generation_date)));
                $this->general_model->updateData('recurrence', array('next_generation_date' => $next_generation_date), array(
                    'recurrence_id' => $value->recurrence_id));
            }

        }

    }

    public function get_product_inventory()
    {
        $results = $this->general_model->getRecords('product_inventory.*', 'product_inventory', [
            'delete_status' => 0, 'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);
        return $results;
    }

    public function get_product_inventory_variants()
    {
        $results = $this->general_model->getRecords('product_inventory_varients.*', 'product_inventory_varients', [
            'delete_status' => 0, 'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);
        return $results;
    }

    public function get_products()
    {
        $results = $this->general_model->getRecords('products.*', 'products', ['delete_status' => 0,
            'branch_id'                                                                            => $this->session->userdata('SESS_BRANCH_ID')]);
        return $results;
    }

    public function get_services()
    {
        $results = $this->general_model->getRecords('services.*', 'services', ['delete_status' => 0,
            'branch_id'                                                                            => $this->session->userdata('SESS_BRANCH_ID')]);
        return $results;
    }

    public function get_default_branch_data($branch_id){
        $branch_data  = $this->common->branch_field_without_firm($branch_id);
        $branch       = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data = $this->common->country_field();
        $country      = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data   = $this->common->state_field($branch[0]->branch_country_id);
        $state        = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data    = $this->common->city_field($branch[0]->branch_state_id);
        $city         = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data         = array('branch' => $branch, 'country' => $country, 'state' => $state,
            'city'                         => $city);
        return $data;
    }


    public function supplier_data($id)
    {
        $supplier_data = $this->common->supplier_field_with_id($id);
        $data          = $this->general_model->getJoinRecords($supplier_data['string'], $supplier_data['table'], $supplier_data['where'], $supplier_data['join'], $supplier_data['order']);
        return $data;
    }

    public function customer_data($id)
    {
        $customer_data = $this->common->customer_field_with_id($id);
        $data          = $this->general_model->getJoinRecords($customer_data['string'], $customer_data['table'], $customer_data['where'], $customer_data['join'], $customer_data['order']);
        return $data;
    }

    public function tax_call_type($type)
    {
        $tax_data = $this->common->tax_field_with_type($type);
        $data     = $this->general_model->getJoinRecords($tax_data['string'], $tax_data['table'], $tax_data['where'],$tax_data['join']);
        return $data;
    }


    function sales_credit_details($id){
        $id                          = $this->encryption_url->decode($id);
        $data                        = $this->get_default_country_state();
        $sales_credit_note_module_id = $this->config->item('sales_credit_note_module');
        $data['module_id']           = $sales_credit_note_module_id;
        $modules                     = $this->modules;
        $privilege                   = "view_privilege";
        $data['privilege']           = "view_privilege";
        $section_modules             = $this->get_section_modules($sales_credit_note_module_id, $modules, $privilege);
        $data                        = array_merge($data, $section_modules);

        $product_module_id             = $this->config->item('product_module');
        $service_module_id             = $this->config->item('service_module');
        $customer_module_id            = $this->config->item('customer_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');

        $data['currency']       = $this->currency_call();
        $data['sales_credit_note_module_id']           = $sales_credit_note_module_id;
        $sales_credit_note_data = $this->common->sales_credit_note_list_field1($id);
        $data['data']           = $this->general_model->getJoinRecords($sales_credit_note_data['string'], $sales_credit_note_data['table'], $sales_credit_note_data['where'], $sales_credit_note_data['join']);

        $sales_data = $this->general_model->getRecords('shipping_address_id,billing_address_id', 'sales', array(
            'sales_id' => $data['data'][0]->sales_id));

        if(!empty($sales_data)){
            $this->db->select('shipping_address,country_name,department,contact_person,city_name,state_name,    address_pin_code');
            $this->db->from('shipping_address s');
            $this->db->join('countries c','s.country_id=c.country_id','left');
            $this->db->join(' cities city' ,'s.city_id=city.city_id' ,'left');
            $this->db->join(' states sta' ,'s.state_id=sta.state_id' ,'left');  
            $this->db->where('shipping_address_id',$sales_data[0]->billing_address_id);
            $billing_address = $this->db->get();
            $data['billing_address'] = $billing_address->result();
            // echo '<pre>';
            // print_r($data['billing_address'] );
            // exit();
            $data['data'][0]->billing_address_id = $sales_data[0]->billing_address_id;
            
            $this->db->select('shipping_address,country_name');
            $this->db->from('shipping_address s');
            $this->db->join('countries c','s.country_id=c.country_id','left');
            $this->db->where('shipping_address_id',$sales_data[0]->shipping_address_id);
            $shipping_address_id = $this->db->get();
            $shipping_addreses = $shipping_address_id->result();
            $data['data'][0]->shipping_address = $shipping_addreses[0]->shipping_address;
            $data['data'][0]->shipping_address_id = $sales_data[0]->shipping_address_id;
        }
        
        $item_types = $this->general_model->getRecords('item_type,sales_credit_note_item_description', 'sales_credit_note_item', array(
            'sales_credit_note_id' => $id));

        $service     = 0;
        $product     = 0;
        $description = 0;

        foreach ($item_types as $key => $value)
        {

            if ($value->sales_credit_note_item_description != "")
            {
                $description++;
            }

            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else

            if ($value->item_type == "product")
            {
                $product = 1;
            }
            else

            if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }

        }

        $sales_credit_note_service_items = array();
        $sales_credit_note_product_items = array();

        if (($data['data'][0]->sales_credit_note_nature_of_supply == "service" || $data['data'][0]->sales_credit_note_nature_of_supply == "both") && $service == 1){

            $service_items                   = $this->common->sales_credit_note_items_service_list_field($id);
            $sales_credit_note_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->sales_credit_note_nature_of_supply == "product" || $data['data'][0]->sales_credit_note_nature_of_supply == "both"){
            /*if ($product == 2){
                $product_items = $this->common->sales_credit_note_items_product_inventory_list_field($id);
                $sales_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else if ($product == 1){
            }*/
            $product_items = $this->common->sales_credit_note_items_product_list_field($id);
            $sales_credit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($sales_credit_note_product_items, $sales_credit_note_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $discountExist    = 0;
        $tdsExist         = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->sales_credit_note_tax_amount > 0 && $data['data'][0]->sales_credit_note_igst_amount > 0 && ($data['data'][0]->sales_credit_note_cgst_amount == 0 && $data['data'][0]->sales_credit_note_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->sales_credit_note_tax_amount > 0 && ($data['data'][0]->sales_credit_note_cgst_amount > 0 || $data['data'][0]->sales_credit_note_sgst_amount > 0) && $data['data'][0]->sales_credit_note_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->sales_credit_note_tax_amount > 0 && ($data['data'][0]->sales_credit_note_igst_amount == 0 && $data['data'][0]->sales_credit_note_cgst_amount == 0 && $data['data'][0]->sales_credit_note_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->sales_credit_note_tax_amount == 0 && ($data['data'][0]->sales_credit_note_igst_amount == 0 && $data['data'][0]->sales_credit_note_cgst_amount == 0 && $data['data'][0]->sales_credit_note_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->sales_credit_note_tcs_amount > 0)
        {
            //$data['data'][0]->sales_credit_note_tds_amount > 0 || 
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->sales_credit_note_discount_amount > 0)
        {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if($data['data'][0]->sales_credit_note_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->sales_credit_note_billing_state_id);
        $data['cess_exist']        = $cess_exist;
        $data['is_utgst']          = $is_utgst;
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['dtcount']    = $discountExist;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        if ($sales_credit_note_product_items && $sales_credit_note_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($sales_credit_note_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($sales_credit_note_service_items)
        {
            $nature_of_supply = "Service";
        }

        $data['nature_of_supply'] = $nature_of_supply;

        $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";

        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];

        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_id']     = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $customer_currency_code = $this->getCurrencyInfo($data['data'][0]->currency_id);
        $customer_curr_code = '';
        if(!empty($customer_currency_code))
        $customer_curr_code     = $customer_currency_code[0]->currency_code;
        $data['cust_currency_code']     = $customer_curr_code;
        $data['is_only_view'] = '0';
        if($this->input->post('is_only_view')){
            $data['is_only_view'] = '1';
            $data['data'][0]->sales_credit_note_invoice_number = $this->input->post('invoice_number');
            $data['data'][0]->sales_credit_note_date = $this->input->post('invoice_date');
        }
        return $data;
    }

    function sales_debit_details($id){
        $id                          = $this->encryption_url->decode($id);
        $data                        = $this->get_default_country_state();
        $sales_debit_note_module_id = $this->config->item('sales_debit_note_module');
        $data['module_id']           = $sales_debit_note_module_id;
        $modules                     = $this->modules;
        $privilege                   = "view_privilege";
        $data['privilege']           = "view_privilege";
        $section_modules             = $this->get_section_modules($sales_debit_note_module_id, $modules, $privilege);
        $data                        = array_merge($data, $section_modules);

        $product_module_id             = $this->config->item('product_module');
        $service_module_id             = $this->config->item('service_module');
        $customer_module_id            = $this->config->item('customer_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $data['currency']       = $this->currency_call();
        $sales_debit_note_data = $this->common->sales_debit_note_list_field1($id);
        $data['sales_debit_note_module_id'] = $sales_debit_note_module_id;
        $data['data']           = $this->general_model->getJoinRecords($sales_debit_note_data['string'], $sales_debit_note_data['table'], $sales_debit_note_data['where'], $sales_debit_note_data['join']);
        // echo '<pre>';
        // print_r($data);
        // exit;

        $sales_data = $this->general_model->getRecords('shipping_address_id,billing_address_id', 'sales', array(
            'sales_id' => $data['data'][0]->sales_id));

        if(!empty($sales_data)){
            $this->db->select('shipping_address,country_name,department,contact_person,city_name,state_name,    address_pin_code');
            $this->db->from('shipping_address s');
            $this->db->join('countries c','s.country_id=c.country_id','left');
            $this->db->join(' cities city' ,'s.city_id=city.city_id' ,'left');
            $this->db->join(' states sta' ,'s.state_id=sta.state_id' ,'left');
            $this->db->where('shipping_address_id',$sales_data[0]->billing_address_id);
            $billing_address = $this->db->get();
            $data['billing_address'] = $billing_address->result();
            // echo '<pre>';
            // print_r( $data['billing_address']);
            // exit();
            $data['data'][0]->billing_address_id = $sales_data[0]->billing_address_id;

            $this->db->select('shipping_address,country_name');
            $this->db->from('shipping_address s');
            $this->db->join('countries c','s.country_id=c.country_id','left');
            $this->db->where('shipping_address_id',$sales_data[0]->shipping_address_id);
            $shipping_address_id = $this->db->get();
            $shipping_addreses = $shipping_address_id->result();
            $data['data'][0]->shipping_address = $shipping_addreses[0]->shipping_address;
            $data['data'][0]->shipping_address_id = $sales_data[0]->shipping_address_id;
        }

        $item_types = $this->general_model->getRecords('item_type,sales_debit_note_item_description', 'sales_debit_note_item', array(
            'sales_debit_note_id' => $id));

        $service     = 0;
        $product     = 0;
        $description = 0;

        foreach ($item_types as $key => $value)
        {

            if ($value->sales_debit_note_item_description != "")
            {
                $description++;
            }

            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else

            if ($value->item_type == "product")
            {
                $product = 1;
            }
            else

            if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }

        }

        $sales_debit_note_service_items = array();
        $sales_debit_note_product_items = array();

        if (($data['data'][0]->sales_debit_note_nature_of_supply == "service" || $data['data'][0]->sales_debit_note_nature_of_supply == "both") && $service == 1){

            $service_items                   = $this->common->sales_debit_note_items_service_list_field($id);
            $sales_debit_note_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->sales_debit_note_nature_of_supply == "product" || $data['data'][0]->sales_debit_note_nature_of_supply == "both"){
            /*if ($product == 2){
                $product_items = $this->common->sales_debit_note_items_product_inventory_list_field($id);
                $sales_debit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            } else if ($product == 1){
            }*/
            $product_items = $this->common->sales_debit_note_items_product_list_field($id);
            $sales_debit_note_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        }

        $data['items'] = array_merge($sales_debit_note_product_items, $sales_debit_note_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $discountExist    = 0;
        $tdsExist         = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->sales_debit_note_tax_amount > 0 && $data['data'][0]->sales_debit_note_igst_amount > 0 && ($data['data'][0]->sales_debit_note_cgst_amount == 0 && $data['data'][0]->sales_debit_note_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->sales_debit_note_tax_amount > 0 && ($data['data'][0]->sales_debit_note_cgst_amount > 0 || $data['data'][0]->sales_debit_note_sgst_amount > 0) && $data['data'][0]->sales_debit_note_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->sales_debit_note_tax_amount > 0 && ($data['data'][0]->sales_debit_note_igst_amount == 0 && $data['data'][0]->sales_debit_note_cgst_amount == 0 && $data['data'][0]->sales_debit_note_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->sales_debit_note_tax_amount == 0 && ($data['data'][0]->sales_debit_note_igst_amount == 0 && $data['data'][0]->sales_debit_note_cgst_amount == 0 && $data['data'][0]->sales_debit_note_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->sales_debit_note_tcs_amount > 0)
        {
            //$data['data'][0]->sales_debit_note_tds_amount > 0 || 
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->sales_debit_note_discount_amount > 0)
        {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if($data['data'][0]->sales_debit_note_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->sales_debit_note_billing_state_id);
        $data['cess_exist']        = $cess_exist;
        $data['is_utgst']          = $is_utgst;
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['dtcount']    = $discountExist;
        $data['discount_exist'] = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        if ($sales_debit_note_product_items && $sales_debit_note_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($sales_debit_note_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($sales_debit_note_service_items)
        {
            $nature_of_supply = "Service";
        }

        $data['nature_of_supply'] = $nature_of_supply;

        $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";

        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];

        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_id']     = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $customer_currency_code = $this->getCurrencyInfo($data['data'][0]->currency_id);
        $customer_curr_code = '';
        if(!empty($customer_currency_code))
        $customer_curr_code     = $customer_currency_code[0]->currency_code;
        $data['cust_currency_code']     = $customer_curr_code;

        $data['is_only_view'] = '0';
        if($this->input->post('is_only_view')){
            $data['is_only_view'] = '1';
            $data['data'][0]->sales_debit_note_invoice_number = $this->input->post('invoice_number');
            $data['data'][0]->sales_debit_note_date = $this->input->post('invoice_date');
        }
        return $data;
    }

    function sales_details($id){
        $id                = $this->encryption_url->decode($id);
        $data              = $this->get_default_country_state();
        $sales_module_id   = $this->config->item('sales_module');
        $data['module_id'] = $sales_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($sales_module_id , $modules , $privilege);
        $data              = array_merge($data , $section_modules);

        $product_module_id             = $this->config->item('product_module');
        $service_module_id             = $this->config->item('service_module');
        $customer_module_id            = $this->config->item('customer_module');
        $data['charges_sub_module_id'] = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']   = $this->config->item('notes_sub_module');

        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);


        $data['currency'] = $this->currency_call();
        $sales_data       = $this->common->sales_list_field1($id);
        $data['data']     = $this->general_model->getJoinRecords($sales_data['string'] , $sales_data['table'] , $sales_data['where'] , $sales_data['join']);


        $item_types = $this->general_model->getRecords('item_type,sales_item_description' , 'sales_item' , array(
            'sales_id' => $id ));

        $service     = 0;
        $product     = 0;
        $description = 0;
        foreach ($item_types as $key => $value)
        {
            if ($value->sales_item_description != "")
            {
                $description++;
            }
            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else if ($value->item_type == "product")
            {
                $product = 1;
            }
            else if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }
        }

        $sales_service_items = array();
        $sales_product_items = array();
        if (($data['data'][0]->sales_nature_of_supply == "service" || $data['data'][0]->sales_nature_of_supply == "both") && $service == 1)
        {

            $service_items       = $this->common->sales_items_service_list_field($id);
            $sales_service_items = $this->general_model->getJoinRecords($service_items['string'] , $service_items['table'] , $service_items['where'] , $service_items['join']);
        }
        if ($data['data'][0]->sales_nature_of_supply == "product" || $data['data'][0]->sales_nature_of_supply == "both")
        {
            /*if ($product == 2)
            {
                $product_items       = $this->common->sales_items_product_inventory_list_field($id);
                $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
            }
            else if ($product == 1)
            {
            }*/
            $product_items       = $this->common->sales_items_product_list_field($id);
            $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        }
        $data['items'] = array_merge($sales_product_items , $sales_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $discountExist    = 0;
        $tdsExist         = 0;
        $descriptionExist = 0;
        if ($data['data'][0]->sales_tax_amount > 0 && $data['data'][0]->sales_igst_amount > 0 && ($data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount > 0 && ($data['data'][0]->sales_cgst_amount > 0 || $data['data'][0]->sales_sgst_amount > 0) && $data['data'][0]->sales_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount > 0 && ($data['data'][0]->sales_igst_amount == 0 && $data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount == 0 && ($data['data'][0]->sales_igst_amount == 0 && $data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }
        if ($data['data'][0]->sales_tds_amount > 0 || $data['data'][0]->sales_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->sales_discount_amount > 0)
        {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if($data['data'][0]->sales_tax_cess_amount > 0){
            $cess_exist = 1;
        }

        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->sales_billing_state_id);

        $data['cess_exist']        = $cess_exist;
        $data['is_utgst']          = $is_utgst;
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        $invoice_type = $this->input->post('pdf_type_check');
        if ($sales_product_items && $sales_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($sales_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($sales_service_items)
        {
            $nature_of_supply = "Service";
        }
        $data['nature_of_supply'] = $nature_of_supply;
        $data['invoice_type'] = '';

        if ($invoice_type == "original")
        {
            $data['invoice_type'] = "ORIGINAL FOR RECIPIENT";
        }
        elseif ($invoice_type == "duplicate")
        {
            $data['invoice_type'] = "DUPLICATE FOR SUPPLIER";
        }
        elseif($invoice_type == "triplicate")
        {
            $data['invoice_type'] = "TRIPLICATE FOR TRANSPORTER";
        }

        $note_data         = $this->template_note($data['data'][0]->note1 , $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        return $data;
    }

    function getSalesDetails($id){
        
        $branch_data                 = $this->common->branch_field();
        $data['branch']              = $this->general_model->getJoinRecords($branch_data['string'] , $branch_data['table'] , $branch_data['where'] , $branch_data['join'] , $branch_data['order']);
        // echo '<pre>';
        // print_r($data['branch'] );
        // exit;
        $sales_module_id             = $this->config->item('sales_module');
        $data['email_module_id']     = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id']       = $sales_module_id;
        $data['sales_module_id'] = $sales_module_id;
        $modules                 = $this->modules;
        $privilege               = "view_privilege";
        $data['privilege']       = "view_privilege";
        $data['privilege']       = $privilege;
        $section_modules         = $this->get_section_modules($sales_module_id , $modules , $privilege);
        /* presents all the needed */
        $data                    = array_merge($data , $section_modules);
        
        $sales_data = $this->common->sales_list_field1($id);
        $data['currency'] = $this->currency_call();
        $data['data'] = $this->general_model->getJoinRecords($sales_data['string'] , $sales_data['table'] , $sales_data['where'] , $sales_data['join']);
        /*echo "<pre>";
        print_r($data['data']);
        exit;*/

        $this->db->select('shipping_address,country_name,department,contact_person,city_name,state_name,    address_pin_code');
        $this->db->from('shipping_address s');
        $this->db->join('countries c','s.country_id=c.country_id','left');
        $this->db->join(' cities city' ,'s.city_id=city.city_id' ,'left');
        $this->db->join(' states sta' ,'s.state_id=sta.state_id' ,'left');
        $this->db->where('shipping_address_id',$data['data'][0]->billing_address_id);
        $billing_address = $this->db->get();
        $data['billing_address'] = $billing_address->result();
        /*print_r($data['billing_address']);exit();*/
        $item_types = $this->general_model->getRecords('item_type,sales_item_tds_percentage,sales_item_tds_id,sales_item_description' , 'sales_item' , array(
            'sales_id' => $id ));

        $service     = 0;
        $product     = 0;
        $description = 0;
        /*$sales_item_tax_amount = $sales_item_tax_amount + $item_data['freight_charge_tax_amount'] + $item_data['insurance_charge_tax_amount'] + $item_data['packing_charge_tax_amount'] + $item_data['incidental_charge_tax_amount'] + $item_data['inclusion_other_charge_tax_amount'] - $item_data['exclusion_other_charge_tax_amount'];*/
     
        foreach ($item_types as $key => $value){
            if ($value->sales_item_description != "")
            {
                $description++;
            }
            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else if ($value->item_type == "product")
            {
                $product = 1;
            }
            else if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }
            if($value->sales_item_tds_percentage > 0 && $value->sales_item_tds_id != 0){
                $value->tds_module_type = $this->getTDSModule($value->sales_item_tds_id);
            }
        } 

        $sales_service_items = array();
        $sales_product_items = array();
        if (($data['data'][0]->sales_nature_of_supply == "service" || $data['data'][0]->sales_nature_of_supply == "both") && $service == 1)
        {

            $service_items       = $this->common->sales_items_service_list_field($id);
            $sales_service_items = $this->general_model->getJoinRecords($service_items['string'] , $service_items['table'] , $service_items['where'] , $service_items['join']);
           
        }

        if ($data['data'][0]->sales_nature_of_supply == "product" || $data['data'][0]->sales_nature_of_supply == "both")
        {
            $product_items       = $this->common->sales_items_product_list_field($id);
            $sales_product_items = $this->general_model->getJoinRecords($product_items['string'] , $product_items['table'] , $product_items['where'] , $product_items['join']);
        }

        $data['items'] = array_merge($sales_product_items , $sales_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $tdsExist         = 0;
        $discountExist    = 0;
        $schemediscountExist = 0;
        $descriptionExist = 0;
        $cess_exist = 0;
      
        if ($data['data'][0]->sales_tax_amount > 0 && $data['data'][0]->sales_igst_amount > 0 && ($data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount > 0 && ($data['data'][0]->sales_cgst_amount > 0 || $data['data'][0]->sales_sgst_amount > 0) && $data['data'][0]->sales_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount > 0 && ($data['data'][0]->sales_igst_amount == 0 && $data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->sales_tax_amount == 0 && ($data['data'][0]->sales_igst_amount == 0 && $data['data'][0]->sales_cgst_amount == 0 && $data['data'][0]->sales_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->sales_tcs_amount > 0)
        {
            /* Discount $data['data'][0]->sales_tds_amount > 0 || */
            $tdsExist = 1;
        }

        foreach ($data['items'] as $key => $value) {
            if ($value->sales_item_discount_amount > 0){
                    /* Discount */
                    $discountExist = 1;
                }

                if ($value->sales_item_scheme_discount_amount > 0){
                    /* Scheme Discount */
                    $schemediscountExist = 1;
                }

        }
        

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        if($data['data'][0]->sales_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        if ($sales_product_items && $sales_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($sales_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($sales_service_items)
        {
            $nature_of_supply = "Service";
        }
        $data['nature_of_supply'] = $nature_of_supply;
        $note_data         = $this->template_note($data['data'][0]->note1 , $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->sales_billing_state_id);
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['sgst_exist']        = $sgstExist;
        $data['cess_exist']        = $cess_exist;
        $data['tax_exist']         = $taxExist;
        $data['is_utgst']          = $is_utgst;
        $data['discount_exist']    = $discountExist;
        $data['schemediscountExist']  = $schemediscountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_id']     = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $customer_currency_code = $this->getCurrencyInfo($data['data'][0]->currency_id);
        $customer_curr_code = '';
        if(!empty($customer_currency_code))
        $customer_curr_code     = $customer_currency_code[0]->currency_code;
        $data['cust_currency_code']     = $customer_curr_code;
        $hsn_data = $this->common->hsn_list_item_field1($id);
        $data['hsn'] = $this->general_model->getPageJoinRecords($hsn_data);

        /* delivery challan request */
        $data['is_only_view'] = '0';
        if($this->input->post('is_only_view')){
            $data['is_only_view'] = '1';
            $data['data'][0]->sales_invoice_number = $this->input->post('invoice_number');
            $data['data'][0]->sales_date = $this->input->post('invoice_date');
        }
        return $data;
    }

    function getQuotationDetail($id){
        $branch_data             = $this->common->branch_field();
        $data['branch']          = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $quotation_module_id     = $this->config->item('quotation_module');
        $data['email_module_id'] = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id']           = $quotation_module_id;
        $data['quotation_module_id'] = $quotation_module_id;
        $modules                     = $this->modules;
        $privilege                   = "view_privilege";
        $data['privilege']           = "view_privilege";
        $data['privilege']           = $privilege;
        $section_modules             = $this->get_section_modules($quotation_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['currency'] = $this->currency_call();
        $quotation_data   = $this->common->quotation_list_field1($id);
        $data['data']     = $this->general_model->getJoinRecords($quotation_data['string'], $quotation_data['table'], $quotation_data['where'], $quotation_data['join']);
        // echo "<pre>";
        // print_r($data['data']);
        // exit;
        $this->db->select('shipping_address,country_name,department,contact_person,city_name,state_name,    address_pin_code');
        $this->db->from('shipping_address s');
        $this->db->join('countries c','s.country_id=c.country_id','left');
        $this->db->join(' cities city' ,'s.city_id=city.city_id' ,'left');
        $this->db->join(' states sta' ,'s.state_id=sta.state_id' ,'left');
        $this->db->where('shipping_address_id',$data['data'][0]->billing_address_id);
        $billing_address = $this->db->get();
        $data['billing_address'] = $billing_address->result();
        // echo '<pre>'; print_r($data['data'] );exit();
        
        $item_types = $this->general_model->getRecords('item_type,quotation_item_description', 'quotation_item', array(
            'quotation_id' => $id));

        $service     = 0;
        $product     = 0;
        $description = 0;

        foreach ($item_types as $key => $value)
        {

            if ($value->quotation_item_description != "")
            {
                $description++;
            }

            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else

            if ($value->item_type == "product")
            {
                $product = 1;
            }
            else

            if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }

        }

        $quotation_service_items = array();
        $quotation_product_items = array();

        if (($data['data'][0]->quotation_nature_of_supply == "service" || $data['data'][0]->quotation_nature_of_supply == "both") && $service == 1)
        {

            $service_items           = $this->common->quotation_items_service_list_field($id);
            $quotation_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->quotation_nature_of_supply == "product" || $data['data'][0]->quotation_nature_of_supply == "both")
        {

            if ($product == 2)
            {
                $product_items           = $this->common->quotation_items_product_inventory_list_field($id);
                $quotation_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
            else

            if ($product == 1)
            {
                $product_items           = $this->common->quotation_items_product_list_field($id);
                $quotation_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }

        }

        $data['items'] = array_merge($quotation_product_items, $quotation_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $discountExist    = 0;
        $tdsExist         = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->quotation_tax_amount > 0 && $data['data'][0]->quotation_igst_amount > 0 && ($data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->quotation_tax_amount > 0 && ($data['data'][0]->quotation_cgst_amount > 0 || $data['data'][0]->quotation_sgst_amount > 0) && $data['data'][0]->quotation_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->quotation_tax_amount > 0 && ($data['data'][0]->quotation_igst_amount == 0 && $data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->quotation_tax_amount == 0 && ($data['data'][0]->quotation_igst_amount == 0 && $data['data'][0]->quotation_cgst_amount == 0 && $data['data'][0]->quotation_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->quotation_tds_amount > 0 || $data['data'][0]->quotation_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->quotation_discount_amount > 0)
        {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if($data['data'][0]->quotation_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        $data['is_utgst'] = $this->general_model->checkIsUtgst($data['data'][0]->quotation_billing_state_id);
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['cess_exist']        = $cess_exist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        if ($quotation_product_items && $quotation_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($quotation_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($quotation_service_items)
        {
            $nature_of_supply = "Service";
        }

        $data['nature_of_supply'] = $nature_of_supply;

        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_id']     = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $customer_currency_code = $this->getCurrencyInfo($data['data'][0]->currency_id);
        $customer_curr_code = '';
        if(!empty($customer_currency_code))
        $customer_curr_code     = $customer_currency_code[0]->currency_code;
        $data['cust_currency_code']     = $customer_curr_code;
        return $data;
    }

    function getPerformaDetail($id){
        $branch_data             = $this->common->branch_field();
        $data['branch']          = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $performa_module_id     = $this->config->item('performa_module');
        $data['email_module_id'] = $this->config->item('email_module');
        /* Sub Modules Present */
        $data['email_sub_module_id'] = $this->config->item('email_sub_module');

        $data['module_id']           = $performa_module_id;
        $data['performa_module_id'] = $performa_module_id;
        $modules                     = $this->modules;
        $privilege                   = "view_privilege";
        $data['privilege']           = "view_privilege";
        $data['privilege']           = $privilege;
        $section_modules             = $this->get_section_modules($performa_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['currency'] = $this->currency_call();
        $performa_data   = $this->common->performa_list_field1($id);
        $data['data']     = $this->general_model->getJoinRecords($performa_data['string'], $performa_data['table'], $performa_data['where'], $performa_data['join']);

        $this->db->select('shipping_address,country_name');
        $this->db->from('shipping_address s');
        $this->db->join('countries c','s.country_id=c.country_id','left');
        $this->db->where('shipping_address_id',$data['data'][0]->billing_address_id);
        $billing_address = $this->db->get();
        $data['billing_address'] = $billing_address->result();
        
        $item_types = $this->general_model->getRecords('item_type,performa_item_description', 'performa_item', array(
            'performa_id' => $id));

        $service     = 0;
        $product     = 0;
        $description = 0;

        foreach ($item_types as $key => $value)
        {

            if ($value->performa_item_description != "")
            {
                $description++;
            }

            if ($value->item_type == "service")
            {
                $service = 1;
            }
            else

            if ($value->item_type == "product")
            {
                $product = 1;
            }
            else

            if ($value->item_type == "product_inventory")
            {
                $product = 2;
            }

        }

        $performa_service_items = array();
        $performa_product_items = array();

        if (($data['data'][0]->performa_nature_of_supply == "service" || $data['data'][0]->performa_nature_of_supply == "both") && $service == 1)
        {

            $service_items           = $this->common->performa_items_service_list_field($id);
            $performa_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        }

        if ($data['data'][0]->performa_nature_of_supply == "product" || $data['data'][0]->performa_nature_of_supply == "both")
        {

            if ($product == 2)
            {
                $product_items           = $this->common->performa_items_product_inventory_list_field($id);
                $performa_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }
            else

            if ($product == 1)
            {
                $product_items           = $this->common->performa_items_product_list_field($id);
                $performa_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
            }

        }

        $data['items'] = array_merge($performa_product_items, $performa_service_items);

        $igstExist        = 0;
        $cgstExist        = 0;
        $sgstExist        = 0;
        $taxExist         = 0;
        $discountExist    = 0;
        $tdsExist         = 0;
        $descriptionExist = 0;

        if ($data['data'][0]->performa_tax_amount > 0 && $data['data'][0]->performa_igst_amount > 0 && ($data['data'][0]->performa_cgst_amount == 0 && $data['data'][0]->performa_sgst_amount == 0))
        {

            /* igst tax slab */
            $igstExist = 1;
        }
        elseif ($data['data'][0]->performa_tax_amount > 0 && ($data['data'][0]->performa_cgst_amount > 0 || $data['data'][0]->performa_sgst_amount > 0) && $data['data'][0]->performa_igst_amount == 0)
        {
            /* cgst tax slab */
            $cgstExist = 1;
            $sgstExist = 1;
        }
        elseif ($data['data'][0]->performa_tax_amount > 0 && ($data['data'][0]->performa_igst_amount == 0 && $data['data'][0]->performa_cgst_amount == 0 && $data['data'][0]->performa_sgst_amount == 0))
        {
            /* Single tax */
            $taxExist = 1;
        }
        elseif ($data['data'][0]->performa_tax_amount == 0 && ($data['data'][0]->performa_igst_amount == 0 && $data['data'][0]->performa_cgst_amount == 0 && $data['data'][0]->performa_sgst_amount == 0))
        {
            /* No tax */
            $igstExist = 0;
            $cgstExist = 0;
            $sgstExist = 0;
            $taxExist  = 0;
        }

        if ($data['data'][0]->performa_tds_amount > 0 || $data['data'][0]->performa_tcs_amount > 0)
        {
            /* Discount */
            $tdsExist = 1;
        }

        if ($data['data'][0]->performa_discount_amount > 0)
        {
            /* Discount */
            $discountExist = 1;
        }

        if ($description > 0)
        {
            /* Discount */
            $descriptionExist = 1;
        }
        $cess_exist = 0;
        if($data['data'][0]->performa_tax_cess_amount > 0){
            $cess_exist = 1;
        }
        $data['is_utgst'] = $this->general_model->checkIsUtgst($data['data'][0]->performa_billing_state_id);
        $data['igst_exist']        = $igstExist;
        $data['cgst_exist']        = $cgstExist;
        $data['cess_exist']        = $cess_exist;
        $data['sgst_exist']        = $sgstExist;
        $data['tax_exist']         = $taxExist;
        $data['discount_exist']    = $discountExist;
        $data['description_exist'] = $descriptionExist;
        $data['tds_exist']         = $tdsExist;

        if ($performa_product_items && $performa_service_items)
        {
            $nature_of_supply = "Product/Service";
        }
        elseif ($performa_product_items)
        {
            $nature_of_supply = "Product";
        }
        elseif ($performa_service_items)
        {
            $nature_of_supply = "Service";
        }

        $data['nature_of_supply'] = $nature_of_supply;

        $note_data         = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']     = $note_data['note1'];
        $data['template1'] = $note_data['template1'];
        $data['note2']     = $note_data['note2'];
        $data['template2'] = $note_data['template2'];
        $currency = $this->getBranchCurrencyCode();
        $data['currency_code']     = $currency[0]->currency_code;
        $data['currency_id']     = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        $data['currency_symbol']   = $currency[0]->currency_symbol;
        $customer_currency_code = $this->getCurrencyInfo($data['data'][0]->currency_id);
        $customer_curr_code = '';
        if(!empty($customer_currency_code))
        $customer_curr_code     = $customer_currency_code[0]->currency_code;
        $data['cust_currency_code']     = $customer_curr_code;
        return $data;
    }

    function getAVGItemPrice($id){
        $qry = $this->db->query("SELECT AVG(purchase_item_unit_price_after_discount) as purchase_item_unit_price FROM `purchase_item` WHERE item_id='{$id}' and item_type='product' AND delete_status=0 group By item_id");
        $pr_price = 0;
        $divide = 0;
        $result = $qry->result();
        if(!empty($result)){
            $pr_price = $result[0]->purchase_item_unit_price;
            if($pr_price > 0) $divide++;
        }

        $qry = $this->db->query("SELECT AVG(purchase_credit_note_item_unit_price) as purchase_item_unit_price FROM `purchase_credit_note_item` WHERE item_id='{$id}' and item_type='product' AND delete_status=0 group By item_id");
        $pr_cn_price = 0;
        $result = $qry->result();
        if(!empty($result)){
            $pr_cn_price = $result[0]->purchase_item_unit_price;
            if($pr_cn_price > 0) $divide++;
        }

        $qry = $this->db->query("SELECT AVG(purchase_debit_note_item_unit_price) as purchase_item_unit_price FROM `purchase_debit_note_item` WHERE item_id='{$id}' and item_type='product' AND delete_status=0 group By item_id");
        $pr_dn_price = 0;
        $result = $qry->result();
        if(!empty($result)){
            $pr_dn_price = $result[0]->purchase_item_unit_price;
            if($pr_dn_price > 0) $divide++;
        }

        $total_price = 0;
        if($divide > 0){
            $total_price = (round($pr_price,2) + round($pr_dn_price,2) + round($pr_cn_price,2)) / $divide;
        }

        return $total_price;
    }

    function getTDSModule($id){
        $this->db->select('tax_name');
        $this->db->where('tax_id',$id);
        $qry = $this->db->get('tax');
        $result = $qry->result();
        $tax_type = '';
        if(!empty($result)){
            $tax_type = $result[0]->tax_name;
        }
        return $tax_type;
    }

    function PurchaseReturnDetails($id){
        $id                              = $this->encryption_url->decode($id);
        $purchase_return_module_id       = $this->config->item('purchase_return_module');
        $data['module_id']               = $purchase_return_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($purchase_return_module_id, $modules, $privilege);
        $data                   = array_merge($data , $section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        /*$data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];*/
        $product_module_id               = $this->config->item('product_module');
        $service_module_id               = $this->config->item('service_module');
        $supplier_module_id              = $this->config->item('supplier_module');
        $data['charges_sub_module_id']   = $this->config->item('charges_sub_module');
        $data['notes_sub_module_id']     = $this->config->item('notes_sub_module');
        $modules_present                 = array(
                'product_module_id'  => $product_module_id,
                'service_module_id'  => $service_module_id,
                'supplier_module_id' => $supplier_module_id );
        $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);
        ob_start();
        $html                            = ob_get_clean();
        $html                            = utf8_encode($html);
        $branch_data                     = $this->common->branch_field();
        $data['branch']                  = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $country_data                    = $this->common->country_field($data['branch'][0]->branch_country_id);
        $data['country']                 = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                      = $this->common->state_field($data['branch'][0]->branch_country_id, $data['branch'][0]->branch_state_id);
        $data['state']                   = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);
        $city_data                       = $this->common->city_field($data['branch'][0]->branch_city_id);
        $data['city']                    = $this->general_model->getRecords($city_data['string'], $city_data['table'], $city_data['where']);
        $data['currency']                = $this->currency_call();
        $purchase_return_data            = $this->common->purchase_return_list_field1($id);
        $data['data']                    = $this->general_model->getJoinRecords($purchase_return_data['string'], $purchase_return_data['table'], $purchase_return_data['where'], $purchase_return_data['join']);
        $country_data                    = $this->common->country_field($data['data'][0]->purchase_return_billing_country_id);
        $data['data_country']            = $this->general_model->getRecords($country_data['string'], $country_data['table'], $country_data['where']);
        $state_data                      = $this->common->state_field($data['data'][0]->purchase_return_billing_country_id, $data['data'][0]->purchase_return_billing_state_id);
        $data['data_state']              = $this->general_model->getRecords($state_data['string'], $state_data['table'], $state_data['where']);

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));


       
            $product_items                 = $this->common->purchase_return_items_product_list_field($id);
            $purchase_return_product_items = $this->general_model->getJoinRecords($product_items['string'], $product_items['table'], $product_items['where'], $product_items['join']);
        

        $service_items                 = $this->common->purchase_return_items_service_list_field($id);
        $purchase_return_service_items = $this->general_model->getJoinRecords($service_items['string'], $service_items['table'], $service_items['where'], $service_items['join']);
        $data['items']                 = array_merge($purchase_return_product_items, $purchase_return_service_items);
        $igst                          = 0;
        $cgst                          = 0;
        $sgst                          = 0;
        $dpcount                       = 0;
        $dtcount                       = 0;
        $cess                          = 0;
        foreach ($data['items'] as $value)
        {
            $igst = bcadd($igst, $value->purchase_return_item_igst_amount, 2);
            $cgst = bcadd($cgst, $value->purchase_return_item_cgst_amount, 2);
            $sgst = bcadd($sgst, $value->purchase_return_item_sgst_amount, 2);
            $cess = bcadd($cess, $value->purchase_return_item_cess_amount, 2);
            if ($value->purchase_return_item_description != "" && $value->purchase_return_item_description != null)
            {
                $dpcount++;
            } if ($value->purchase_return_item_discount_amount != "" && $value->purchase_return_item_discount_amount != null && $value->purchase_return_item_discount_amount != 0)
            {
                $dtcount++;
            }
        } 
        $is_utgst = $this->general_model->checkIsUtgst($data['data'][0]->purchase_return_billing_state_id);
        $data['is_utgst']           = $is_utgst;
        $data['igst_tax']               = $igst;
        $data['cgst_tax']               = $cgst;
        $data['sgst_tax']               = $sgst;
        $data['dpcount']                = $dpcount;
        $data['dtcount']                = $dtcount;
        $data['cess_tax']               = $cess;
        $note_data                      = $this->template_note($data['data'][0]->note1, $data['data'][0]->note2);
        $data['note1']                  = $note_data['note1'];
        $data['template1']              = $note_data['template1'];
        $data['note2']                  = $note_data['note2'];
        $data['template2']              = $note_data['template2'];
        /* delivery challan request */
       /* $data['is_only_view'] = '0';
        if($this->input->post('is_only_view')){
            $data['is_only_view'] = '1';
            $data['data'][0]->purchase_return_invoice_number = $this->input->post('invoice_number');
            $data['data'][0]->purchase_return_date = $this->input->post('invoice_date');
        }*/
        return $data;
    }

    public function GetFinancialYear(){
        $this->db->select('*');
        $this->db->where('year_status','1');
        $this->db->where('branch_id',$this->session->userdata('SESS_BRANCH_ID'));
        $f_qry = $this->db->get('tbl_financial_year');
        return $f_qry->result_array();
    }

    public function getFirmDetail(){
        $this->db->select('f.firm_name as company_name,b.branch_address');
        $this->db->from('branch b');
        $this->db->join('firm f','f.firm_id=b.firm_id');
        $this->db->where('b.branch_id',$this->session->userdata('SESS_BRANCH_ID'));
        $r = $this->db->get();
        $firm = $r->result_array();
        $firm_detail = array();
        $firm_detail['company_name'] = $firm[0]['company_name'];
        $firm_detail['primary_address'] = $firm[0]['branch_address'];
        return $firm_detail;
    }

    public function ValidateVoucherDate($invoice_date){
        $this->db->select('from_date,to_date');
        $this->db->where('branch_id',$this->session->userdata('SESS_BRANCH_ID'));
        $this->db->where('from_date <=',$invoice_date);
        $this->db->where('year_status','1');
        $this->db->where('to_date >=',$invoice_date);
        $q = $this->db->get('tbl_financial_year');
        
        return $q->num_rows();
    }


    public function transaction_purpose_call(){
        $transaction_purpose = $this->common->transaction_purpose_field();
        $data = $this->general_model->getJoinRecords($transaction_purpose['string'], $transaction_purpose['table'], $transaction_purpose['where'], $transaction_purpose['join']);
        return $data;
    }


    public function transaction_purpose_call_det($id){
        $transaction_purpose = $this->common->transaction_purpose_field_det($id);
        $data = $this->general_model->getJoinRecords($transaction_purpose['string'], $transaction_purpose['table'], $transaction_purpose['where'], $transaction_purpose['join']);
        return $data;
    }

}
