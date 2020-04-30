<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array(
            'ion_auth',
            'form_validation'));
        $this->load->helper(array(
            'url',
            'language'));
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->load->model('general_model');
        $this->lang->load('auth');
        
     //   $this->load->library('Mailer');
    }
    public function index() {
        if (!$this->ion_auth->logged_in()) {
            $this->session->sess_destroy();
            redirect('auth/login', 'refresh');
        } elseif (!$this->ion_auth->is_admin()) {
            redirect('auth/dashboard', 'refresh');
        } elseif ($this->session->userdata('SESS_USER_ID') != "" && $this->session->userdata('SESS_BRANCH_ID') != "" && !$this->ion_auth->is_admin()) {
            redirect('auth/dashboard', 'refresh');
        } else {
            $modules = $this->get_modules();
            /* foreach ($modules['modules'] as $key => $value)
              {
              $this->data['active_modules'][$key] = $value->module_id;
              if ($value->view_privilege == "yes")
              {
              $this->data['active_view'][$key] = $value->module_id;
              }
              if ($value->edit_privilege == "yes")
              {
              $this->data['active_edit'][$key] = $value->module_id;
              }
              if ($value->delete_privilege == "yes")
              {
              $this->data['active_delete'][$key] = $value->module_id;
              }
              if ($value->add_privilege == "yes")
              {
              $this->data['active_add'][$key] = $value->module_id;
              }
              } */
            $user_module_id = $this->config->item('user_module');
            $data['privilege_module_id'] = $this->config->item('privilege_module');
            $privilege = "view_privilege";
            $data['user_module_id'] = $user_module_id;
            $data['privilege'] = $privilege;
            $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
            /* presents all the needed */

            $this->data = array_merge($data, $section_modules);
            $this->data['access_common_settings '] = $access_common_settings = $section_modules['access_common_settings'];
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['users'] = $this->ion_auth->users()->result();
            foreach ($this->data['users'] as $k => $user) {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'index', $this->data);
        }
    }

    public function signup(){
        $sess_branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $sess_financial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $sess_user_id = $this->session->userdata('SESS_USER_ID'); 
        $sess_default_currency = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        
        /*$this->session->sess_destroy();*/
        if ($sess_branch_id != "" && $sess_financial_year_id != "" && $sess_user_id != "" && $sess_default_currency != "") {
            redirect('auth/dashboard', 'refresh');
        }
        $data = array();
        if($this->session->flashdata('auto_data')){
            $data = $this->session->flashdata('auto_data');
        }
        if($this->session->flashdata('error_message')){
            $data['error_message'] = $this->session->flashdata('error_message');
        }
        if($this->session->flashdata('message')){
            $data['message'] = $this->session->flashdata('message');
        }
        $country_data=$this->common->country_field();    
        $data['country']= $this->general_model->getRecords($country_data['string'],$country_data['table'],$country_data['where']);
        $state_data                      = $this->common->state_field(101);
        $data['state']                   = $this->general_model->getRecords($state_data['string'] , $state_data['table'] , $state_data['where']);
        $city_data                       = $this->common->city_field(17);
        $data['city']                    = $this->general_model->getRecords($city_data['string'] , $city_data['table'] , $city_data['where']);
        $this->load->view('auth/signup',$data);
    }

    public function unauthorized() {
        $modules = $this->get_modules();
        $user_module_id = $this->config->item('layout_module');
        $privilege = "view_privilege";
        $data['module_id'] = $user_module_id;
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege,'unauthorized');
        /* foreach ($modules['modules'] as $key => $value)
          {
          $data['active_modules'][$key] = $value->module_id;
          if ($value->view_privilege == "yes")
          {
          $data['active_view'][$key] = $value->module_id;
          }
          if ($value->edit_privilege == "yes")
          {
          $data['active_edit'][$key] = $value->module_id;
          }
          if ($value->delete_privilege == "yes")
          {
          $data['active_delete'][$key] = $value->module_id;
          }
          if ($value->add_privilege == "yes")
          {
          $data['active_add'][$key] = $value->module_id;
          }
          } */
        $data = array_merge($data, $section_modules);
      
        $this->load->view('unauthorized', $data);
    }

    public function login() {
        $sess_branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $sess_financial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $sess_user_id = $this->session->userdata('SESS_USER_ID'); 
        $sess_default_currency = $this->session->userdata('SESS_DEFAULT_CURRENCY');
        
        /*$this->session->sess_destroy();*/
        if ($sess_branch_id != "" && $sess_financial_year_id != "" && $sess_user_id != "" && $sess_default_currency != "") {
            redirect('auth/dashboard', 'refresh');
        }

        $this->data['title'] = $this->lang->line('login_heading');
        $this->form_validation->set_rules('branch_code', str_replace(':', '', $this->lang->line('login_code_label')), 'required');
        $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

        if ($this->form_validation->run() === true) {
            $remember = (bool) $this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('branch_code'), $this->input->post('identity'), $this->input->post('password'), $remember)) {
                $session_id = session_id();
                
                //Start login_auth data insert
                //$ip = $_SERVER['REMOTE_ADDR'];echo $ip;
                $table    = "login_auth";
                $log_data = array(
                        'user_id'             => $this->session->userdata('SESS_USER_ID'),
                        'session_id'          => $session_id,
                        'ip_address'          => $_SERVER['REMOTE_ADDR'],
                        'login_date_time'     => date('Y-m-d H:i:s'),
                        'branch_id'           => $this->session->userdata('SESS_BRANCH_ID'),
                        'status'              => 0,
                        'browser_details'     => $this->input->user_agent()
                    );
                /*$a = $this->general_model->insertData($table, $log_data); */
                
                //End login_auth data insert
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->session->set_flashdata('welcomeLoader',1);
                if($this->session->userdata('SESS_PACKAGE_STATUS') == '0' || $this->session->userdata('SESS_DETAILS_UPDATED') == '0'){
                    redirect('company_setting', 'refresh');
                }else{
                    redirect('auth/dashboard', 'refresh');
                }
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('auth/login', 'refresh');
            }
        } else {
            $this->cache->clean();
            ob_clean();
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            if(isset($_COOKIE['aodidentity'])){
                $name_value = $_COOKIE['aodidentity'];
            }else{
                $name_value = $this->form_validation->set_value('identity');
            }

            if(isset($_COOKIE['aodpassword'])){
                $password_value = $_COOKIE['aodpassword'];
            }else{
                $password_value = '';
            }

            if(isset($_COOKIE['aodbranch_code'])){
                $branch_code_value = $_COOKIE['aodbranch_code'];
            }else{
                $branch_code_value = $this->form_validation->set_value('branch_code');
            }

            $this->data['branch_code'] = array(
                'name' => 'branch_code',
                'id' => 'branch_code',
                'type' => 'text',
                'value' => $branch_code_value,
            );
            $this->data['identity'] = array(
                'name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $name_value,
            );
            $this->data['password'] = array(
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'value' => $password_value,
            );
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'login', $this->data);
        }
    }

    public function dashboard() {
        //print_r($this->db->last_query());exit();
        $modules = $this->get_modules();
        $layout_module_id = $this->config->item('layout_module');
        
        $currency = $this->currency_call();
        /*$currency_data = $this->common->currency_field();
        $currency = $this->general_model->getRecords($currency_data['string'], $currency_data['table'], $currency_data['where']);*/
        $data = $this->ion_auth_model->allReports();
        /*$data['currency'] = $currency;*/

        $data['module_id'] = $layout_module_id;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($layout_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['news'] = $this->general_model->getRecords('news_updates.*', 'news_updates', [
            'delete_status' => 0]);

        $r = $this->db->query("SELECT SUM(quotation_grand_total) as quotation_grand_total FROM quotation WHERE delete_status = 0 AND branch_id = '".$this->session->userdata('SESS_BRANCH_ID')."' ");
        $qt = $r->result_array();
        $data['quotation_total'] = $qt[0]['quotation_grand_total']; 

        $r = $this->db->query("SELECT SUM(sales_grand_total) as sales_grand_total FROM sales WHERE delete_status = 0 AND branch_id = '".$this->session->userdata('SESS_BRANCH_ID')."' ");
        $qt = $r->result_array();
        $data['sales_total'] = $qt[0]['sales_grand_total']; 

        $r = $this->db->query("SELECT SUM(purchase_grand_total) as purchase_grand_total FROM purchase WHERE delete_status = 0 AND branch_id = '".$this->session->userdata('SESS_BRANCH_ID')."' ");
        $qt = $r->result_array();
        $data['purchase_total'] = $qt[0]['purchase_grand_total']; 

        $r = $this->db->query("SELECT SUM(receipt_amount) as receipt_amount FROM receipt_voucher WHERE delete_status = 0 AND branch_id = '".$this->session->userdata('SESS_BRANCH_ID')."' ");
        $qt = $r->result_array();
        $data['receipt_total'] = $qt[0]['receipt_amount']; 

        $r = $this->db->query("SELECT SUM(receipt_amount) as receipt_amount FROM payment_voucher WHERE delete_status = 0 AND branch_id = '".$this->session->userdata('SESS_BRANCH_ID')."' ");
        $qt = $r->result_array();
        $data['payment_total'] = $qt[0]['receipt_amount']; 

        $r = $this->db->query("SELECT SUM(receipt_amount) as receipt_amount FROM advance_voucher WHERE delete_status = 0 AND branch_id = '".$this->session->userdata('SESS_BRANCH_ID')."' ");
        $qt = $r->result_array();
        $data['advance_total'] = $qt[0]['receipt_amount'];

        $this->load->view('dashboard', $data);
    }

    public function getCharts(){
        $timezone = 'Asia/Kolkata';
        $chartdata = $time_data = $resp = $year_ary = $time_ary = $tOrders =array();

        $where = ' WHERE branch_id ='.$this->session->userdata('SESS_BRANCH_ID');
        $filter = 'this_year';

        if($filter != 'today' && $filter != 'yesterday'){
            $pass = 'date';
            $is_check = $change_time = $custome_date = false; 

            if($filter == 'custome'){ 
                $time_data = $this->GetCustomDate($time_ary,$timezone);
                if(count($time_data) > 12){
                    $custome_date = true;
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;
                }
            }

            if($filter == 'this_week') $time_data = $this->getCurrentweekdays();
        
            if($filter == 'last_7_days') $time_data = $this->getLastNDays(7);

            if($filter == 'this_month'){$is_check = true;  $time_data = $this->getCurrentmonthdays(); $change_time = true;}

            if($filter == 'last_30_days'){$is_check = true; $time_data = $this->getLastNDays(30); $change_time = true;}

            if($filter == 'this_year'){ $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;}
           
        }

        $chartdata = $this->SalesGraph($where,$time_data,$filter);
        $purchaseData = $this->purchaseGraph($where,$time_data,$filter);
        $expenseData = $this->expenseGraph($where,$time_data,$filter);
        $resp = array();
        $resp['chartdata'] = $chartdata['sales'];
        $resp['sales_time_data'] = $chartdata['time_data'];
        $resp['purchase_data'] = $purchaseData['purchase'];
        $resp['purchase_time_data'] = $purchaseData['time_data'];
        $resp['expense_data'] = $expenseData['expense'];
        $resp['expense_time_data'] = $expenseData['time_data'];
        echo json_encode($resp);
        exit;
    }

    function utctotimezone($ndate,$timezone,$type){
        $date = new DateTime($ndate, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));

        if($type == 'date') return $date->format('d/m/Y'); 
        
        if($type == 'time') return $date->format('H:i:s'); 
        
        if($type == 'hour') return $date->format('H'); 
        
        if($type == 'month') return $date->format('m'); 
        
        if($type == 'year') return $date->format('Y'); 
    }

    function GetCustomDate($timedata,$timezone){
      $time_data = array();
      if(!empty($timedata)){
        foreach ($timedata as $key => $value) {
          $key = $this->utctotimezone($value['added_date'],$timezone,'date');
          if(!in_array($key , $time_data)){
            array_push($time_data,$key);
          }
        }
      }
      return array_reverse($time_data);
    }

    function getCurrentweekdays(){
        $dates = array();

        $date = date('Y-m-d');
        $ts = strtotime($date);
        $year = date('o', $ts);
        $week = date('W', $ts);

        for($i = 0; $i < 7; $i++) {
            $ts = strtotime($year.'W'.$week.$i);
            $dates[] = date("d/m/Y", $ts);
        }
        return $dates;
    }

    function getLastNDays($days, $format = 'd/m/Y'){
        $m = date("m"); $de= date("d"); $y= date("Y");
        $dateArray = array();
        for($i=0; $i<=$days-1; $i++){
            $dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y)); 
        }
        return array_reverse($dateArray);
    }

    function getCurrentmonthdays(){
        $dates = array();

        for($i = 1; $i <=  date('t'); $i++){
           $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT) . "/" . date('m') . "/" . date('Y');
        }
        return $dates;
    }

    function getCurrentyearmonth(){
        $months = array();
        for ($m=1; $m<=12; $m++) {
            
             $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
             $months[$m]['name'] = $month;
             $months[$m]['month'] = $m;
        }
        return $months;
    }

    function salesGraph(){
        $timezone = 'Asia/Kolkata';
        $chartdata = $resp = $year_ary = $time_ary = $tOrders = $received = $confirmd = $timedata = array();
       
        $where = ' WHERE branch_id ='.$this->session->userdata('SESS_BRANCH_ID').' AND delete_status=0 ';

        $filter = trim($this->input->post('filter'));
        if($filter == '' ) $filter = 'last_7_days';

        $current_date = date('Y-m-d');

        if($filter == 'today'){
            $where .= " AND DATE_FORMAT(sales_date, '%Y-%m-%d') = '{$current_date}'";
        }
        if($filter == 'yesterday'){
            $where .= " AND DATE_FORMAT(sales_date, '%Y-%m-%d') = '{$current_date}' - INTERVAL 1 DAY";
        }
        if($filter == 'this_week'){
            $where .= " AND YEARWEEK(DATE_FORMAT(sales_date, '%Y-%m-%d')) = YEARWEEK('{$current_date}')";
        }
        if($filter == 'last_7_days'){
            $where .= " AND DATE_FORMAT(sales_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 6 DAY";
        }
        if($filter == 'this_month'){
            $where .= " AND MONTH(sales_date) = MONTH('{$current_date}') AND YEAR(sales_date) = YEAR('{$current_date}')";
        }
        if($filter == 'last_30_days'){
            $where .= " AND DATE_FORMAT(sales_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 29 DAY";
        }
        if($filter == 'this_year'){
            $where .= " AND YEAR(sales_date) = YEAR('{$current_date}')";
        }

        $sales_qry = $this->db->query("SELECT COUNT('sales_id') as total_sales,SUM(customer_payable_amount) as sales_grand_total, SUM(sales_paid_amount) as sales_paid_amount,sales_date as added_date FROM sales {$where} GROUP BY DATE_FORMAT(sales_date, '%Y-%m-%d') ORDER BY `sales_id` DESC");
        $time_ary = $sales_qry->result_array();

        $year_share_sql = "SELECT DATE_FORMAT(sales_date, '%Y') as year FROM `sales` {$where} GROUP BY DATE_FORMAT(sales_date, '%Y') ORDER BY `year` ASC";

        $year_share_query = $this->db->query($year_share_sql);
        $year_ary = $year_share_query->result_array();
        $is_check = $change_time = $custome_date = false; 
        $total_sales = 0;

        foreach ($time_ary as $key => $value) {
            $total_sales += $value['total_sales'];
        }

        if($filter == 'today' || $filter == 'yesterday'){
   
            for($i=0;$i<=23;$i++){
                $time_val = $rev_val = $conf_val= $fail_val = 0;

                foreach ($time_ary as $time) {
                    
                    if($this->utctotimezone($time['added_date'],$timezone,'hour') == $i){
                        $time_val += $time['sales_grand_total'];
                        $rev_val += ($time['sales_grand_total'] - $time['sales_paid_amount']);
                        $conf_val += $time['sales_paid_amount'];
                    }
                }
                $tOrders[] = $time_val;
                $received[] = $rev_val;
                $confirmd[] = $conf_val;
            }
        }else{
            
            $pass = 'date';
            $is_check = $change_time = $custome_date = false; 

            if($filter == 'custome'){ 
                $time_data = $this->GetCustomDate($time_ary,$timezone);
                if(count($time_data) > 12){
                    $custome_date = true;
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;
                }
            }

            if($filter == 'this_week') $time_data = $this->getCurrentweekdays();
        
            if($filter == 'last_7_days') $time_data = $this->getLastNDays(7);

            if($filter == 'this_month'){$is_check = true;  $time_data = $this->getCurrentmonthdays(); $change_time = true;}

            if($filter == 'last_30_days'){$is_check = true; $time_data = $this->getLastNDays(30); $change_time = true;}

            if($filter == 'this_year'){ $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;}
            if($filter == 'all') { 
                $time_data = $year_ary; $pass = 'year'; $change_time = true;
               /* print_r($year_ary);*/
                if(count($year_ary) < 2){
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;
                }
            }

            $timedata = array();
            
            foreach ($time_data as $days){
                if($filter == 'this_year' || $custome_date == true) {
                    $timedata[] = $days['name'];
                    $days = $days['month'];
                }

                if($filter == 'all'){
                    if(count($year_ary) < 2){
                        $timedata[] = $days['name'];
                        $days = $days['month'];
                    }else{
                        $timedata[] = $days['year'];
                        $days = $days['year'];
                    }
                }

                $time_val = $rev_val = $conf_val = 0;
                $new_date = '';
               
                foreach ($time_ary as $time) {
                    if($this->utctotimezone($time['added_date'],$timezone,$pass) == $days){
                        $time_val += $time['sales_grand_total'];
                        $rev_val += ($time['sales_grand_total'] - $time['sales_paid_amount']);
                        $conf_val += $time['sales_paid_amount'];
                        $new_date = $this->utctotimezone($time['added_date'],$timezone,$pass);
                    }
                }
                if($is_check == true){
                    if($new_date != ''){
                        $tOrders[] = $time_val;
                        $received[] = $rev_val;
                        $confirmd[] = $conf_val;
                        $timedata[] = $new_date;
                    }
                }else{
                  
                    $tOrders[] = $time_val;
                    $received[] = $rev_val;
                    $confirmd[] = $conf_val;
                }
            }
        }
        if($change_time){
            $time_data = $timedata;
        }

        $tOrders = ['name'=>'Total Sales','data'=>$tOrders];
        $received = ['name' => 'Pending Amount','data'=>$received];
        $confirmd = ['name' => 'Received Amount','data'=>$confirmd];
        
        $salesGroup['sales'] = array($tOrders,$received,$confirmd);
        $salesGroup['total_sales'] = $total_sales;
        $salesGroup['total_invoice'] = number_format($this->precise_amount(array_sum($tOrders['data']) , 2),2);
        $salesGroup['total_pending'] = number_format($this->precise_amount(array_sum($received['data']) , 2),2);
        $salesGroup['total_received'] = number_format($this->precise_amount(array_sum($confirmd['data']) , 2),2);
        $salesGroup['total_sales'] = $total_sales;
        $salesGroup['time_data'] = $timedata;
        echo json_encode($salesGroup);
    }

    function purchaseGraph(){
        $timezone = 'Asia/Kolkata';
        $chartdata = $resp = $year_ary = $time_ary = $tOrders = $received = $confirmd = $timedata =array();
        $where = ' WHERE branch_id ='.$this->session->userdata('SESS_BRANCH_ID').' AND delete_status=0 ';
        $filter = trim($this->input->post('filter'));
        if($filter == '' ) $filter = 'last_7_days';

        $current_date = date('Y-m-d');

        if($filter == 'today'){
            $where .= " AND DATE_FORMAT(purchase_date, '%Y-%m-%d') = '{$current_date}'";
        }
        if($filter == 'yesterday'){
            $where .= " AND DATE_FORMAT(purchase_date, '%Y-%m-%d') = '{$current_date}' - INTERVAL 1 DAY";
        }
        if($filter == 'this_week'){
            $where .= " AND YEARWEEK(DATE_FORMAT(purchase_date, '%Y-%m-%d')) = YEARWEEK('{$current_date}')";
        }
        if($filter == 'last_7_days'){
            $where .= " AND DATE_FORMAT(purchase_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 6 DAY";
        }
        if($filter == 'this_month'){
            $where .= " AND MONTH(purchase_date) = MONTH('{$current_date}') AND YEAR(purchase_date) = YEAR('{$current_date}')";
        }
        if($filter == 'last_30_days'){
            $where .= " AND DATE_FORMAT(purchase_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 29 DAY";
        }
        if($filter == 'this_year'){
            $where .= " AND YEAR(purchase_date) = YEAR('{$current_date}')";
        }

        $purchase_qry = $this->db->query("SELECT COUNT('purchase_id') as total_purchase,SUM(supplier_payable_amount) as purchase_grand_total, SUM(purchase_paid_amount) as purchase_paid_amount,purchase_date as added_date FROM purchase {$where} GROUP BY DATE_FORMAT(purchase_date, '%Y-%m-%d') ORDER BY `purchase_id` DESC");
        $time_ary = $purchase_qry->result_array();

        $year_share_sql = "SELECT DATE_FORMAT(purchase_date, '%Y') as year FROM `purchase` {$where} GROUP BY DATE_FORMAT(purchase_date, '%Y') ORDER BY `year` ASC";

        $year_share_query = $this->db->query($year_share_sql);
        $year_ary = $year_share_query->result_array();
        $is_check = $change_time = $custome_date = false; 
        $total_purchase = 0;
        foreach ($time_ary as $key => $value) {
            $total_purchase += $value['total_purchase'];
        }

        if($filter == 'today' || $filter == 'yesterday'){
   
            for($i=0;$i<=23;$i++){
                $time_val = $rev_val = $conf_val= $fail_val = 0;

                foreach ($time_ary as $time) {
           
                    if($this->utctotimezone($time['added_date'],$timezone,'hour') == $i){
                        $time_val += $time['purchase_grand_total'];
                        $rev_val += ($time['purchase_grand_total'] - $time['purchase_paid_amount']);
                        $conf_val += $time['purchase_paid_amount'];
                    }
                }
                $tOrders[] = $time_val;
                $received[] = $rev_val;
                $confirmd[] = $conf_val;
            }
        }else{

            $pass = 'date';
            $is_check = $change_time = $custome_date = false; 

            if($filter == 'custome'){ 
                $time_data = $this->GetCustomDate($time_ary,$timezone);
                if(count($time_data) > 12){
                    $custome_date = true;
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;
                }
            }

            if($filter == 'this_week') $time_data = $this->getCurrentweekdays();
        
            if($filter == 'last_7_days') $time_data = $this->getLastNDays(7);

            if($filter == 'this_month'){$is_check = true;  $time_data = $this->getCurrentmonthdays(); $change_time = true;}

            if($filter == 'last_30_days'){$is_check = true; $time_data = $this->getLastNDays(30); $change_time = true;}

            if($filter == 'this_year'){ $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;}
            if($filter == 'all') { 
                $time_data = $year_ary; $pass = 'year'; $change_time = true;
                if(count($year_ary) < 2){
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth();
                }
            }
            $timedata = array();
            
            foreach ($time_data as $days){
                if($filter == 'this_year' || $custome_date == true) {
                    $timedata[] = $days['name'];
                    $days = $days['month'];
                }

                if($filter == 'all'){
                    if(count($year_ary) < 2){
                        $timedata[] = $days['name'];
                        $days = $days['month'];
                    }else{
                        $timedata[] = $days['year'];
                        $days = $days['year'];
                    }
                }

                $time_val = $rev_val = $conf_val = 0;
                $new_date = '';
               
                foreach ($time_ary as $time) {
                    if($this->utctotimezone($time['added_date'],$timezone,$pass) == $days){
                        $time_val += $time['purchase_grand_total'];
                        $rev_val += ($time['purchase_grand_total'] - $time['purchase_paid_amount']);
                        $conf_val += $time['purchase_paid_amount'];
                        $new_date = $this->utctotimezone($time['added_date'],$timezone,$pass);
                    }
                }
                if($is_check == true){
                    if($new_date != ''){
                        $tOrders[] = $time_val;
                        $received[] = $rev_val;
                        $confirmd[] = $conf_val;
                        $timedata[] = $new_date;
                    }
                }else{
                  
                    $tOrders[] = $time_val;
                    $received[] = $rev_val;
                    $confirmd[] = $conf_val;
                }
            }
        }
        if($change_time){
            $time_data = $timedata;
        }

        $tOrders = ['name'=>'Total Purchase','data'=>$tOrders];
        $received = ['name' => 'Pending Amount','data'=>$received];
        $confirmd = ['name' => 'Paid Amount','data'=>$confirmd];

        /*$failed = ['name' => 'Failed','data'=>$failed];*/
     
        $purchaseGroup['purchase'] = array($tOrders,$received,$confirmd);
        $purchaseGroup['total_purchase'] = $total_purchase;
        $purchaseGroup['total_invoice'] = number_format($this->precise_amount(array_sum($tOrders['data']) , 2),2);
        $purchaseGroup['total_pending'] = number_format($this->precise_amount(array_sum($received['data']) , 2),2);
        $purchaseGroup['total_received'] = number_format($this->precise_amount(array_sum($confirmd['data']) , 2),2);
        $purchaseGroup['time_data'] = $timedata;
        echo json_encode($purchaseGroup);
        exit();
    }

    function expenseGraph(){
        $timezone = 'Asia/Kolkata';
        $chartdata = $resp = $year_ary = $time_ary = $tOrders = $received = $confirmd = $timedata =array();
        $where = ' WHERE branch_id ='.$this->session->userdata('SESS_BRANCH_ID').' AND delete_status=0 ';
        $filter = trim($this->input->post('filter'));
        if($filter == '' ) $filter = 'last_7_days';

        $current_date = date('Y-m-d');

        if($filter == 'today'){
            $where .= " AND DATE_FORMAT(expense_bill_date, '%Y-%m-%d') = '{$current_date}'";
        }
        if($filter == 'yesterday'){
            $where .= " AND DATE_FORMAT(expense_bill_date, '%Y-%m-%d') = '{$current_date}' - INTERVAL 1 DAY";
        }
        if($filter == 'this_week'){
            $where .= " AND YEARWEEK(DATE_FORMAT(expense_bill_date, '%Y-%m-%d')) = YEARWEEK('{$current_date}')";
        }
        if($filter == 'last_7_days'){
            $where .= " AND DATE_FORMAT(expense_bill_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 6 DAY";
        }
        if($filter == 'this_month'){
            $where .= " AND MONTH(expense_bill_date) = MONTH('{$current_date}') AND YEAR(expense_bill_date) = YEAR('{$current_date}')";
        }
        if($filter == 'last_30_days'){
            $where .= " AND DATE_FORMAT(expense_bill_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 29 DAY";
        }
        if($filter == 'this_year'){
            $where .= " AND YEAR(expense_bill_date) = YEAR('{$current_date}')";
        }

        $purchase_qry = $this->db->query("SELECT COUNT('expense_bill_id') as total_expense,SUM(expense_bill_grand_total) as expense_bill_grand_total, SUM(expense_bill_paid_amount) as expense_bill_paid_amount,expense_bill_date as added_date FROM expense_bill {$where} GROUP BY DATE_FORMAT(expense_bill_date, '%Y-%m-%d') ORDER BY `expense_bill_id` DESC");
        $time_ary = $purchase_qry->result_array();
        $total_expense = 0;

        foreach ($time_ary as $key => $value) {
            $total_expense += $value['total_expense'];
        }

        $year_share_sql = "SELECT DATE_FORMAT(expense_bill_date, '%Y') as year FROM `expense_bill` {$where} GROUP BY DATE_FORMAT(expense_bill_date, '%Y') ORDER BY `year` ASC";

        $year_share_query = $this->db->query($year_share_sql);
        $year_ary = $year_share_query->result_array();
        $is_check = $change_time = $custome_date = false; 

        if($filter == 'today' || $filter == 'yesterday'){
   
            for($i=0;$i<=23;$i++){
                $time_val = $rev_val = $conf_val= $fail_val = 0;

                foreach ($time_ary as $time) {
           
                    if($this->utctotimezone($time['added_date'],$timezone,'hour') == $i){
                        $time_val += $time['expense_bill_grand_total'];
                        $rev_val += ($time['expense_bill_grand_total'] - $time['expense_bill_paid_amount']);
                        $conf_val += $time['expense_bill_paid_amount'];
                    }
                }
                $tOrders[] = $time_val;
                $received[] = $rev_val;
                $confirmd[] = $conf_val;
            }
        }else{

            $pass = 'date';
            $is_check = $change_time = $custome_date = false; 

            if($filter == 'custome'){ 
                $time_data = $this->GetCustomDate($time_ary,$timezone);
                if(count($time_data) > 12){
                    $custome_date = true;
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;
                }
            }

            if($filter == 'this_week') $time_data = $this->getCurrentweekdays();
        
            if($filter == 'last_7_days') $time_data = $this->getLastNDays(7);

            if($filter == 'this_month'){$is_check = true;  $time_data = $this->getCurrentmonthdays(); $change_time = true;}

            if($filter == 'last_30_days'){$is_check = true; $time_data = $this->getLastNDays(30); $change_time = true;}

            if($filter == 'this_year'){ $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;}

            if($filter == 'all') { 
                $time_data = $year_ary; $pass = 'year'; $change_time = true;
                if(count($year_ary) < 2){
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth();
                }
            }
            $timedata = array();
            
            foreach ($time_data as $days){
                if($filter == 'this_year' || $custome_date == true) {
                    $timedata[] = $days['name'];
                    $days = $days['month'];
                }

                if($filter == 'all'){
                    if(count($year_ary) < 2){
                        $timedata[] = $days['name'];
                        $days = $days['month'];
                    }else{
                        $timedata[] = $days['year'];
                        $days = $days['year'];
                    }
                }

                $time_val = $rev_val = $conf_val = 0;
                $new_date = '';
               
                foreach ($time_ary as $time) {
                    if($this->utctotimezone($time['added_date'],$timezone,$pass) == $days){
                        $time_val += $time['expense_bill_grand_total'];
                        $rev_val += ($time['expense_bill_grand_total'] - $time['expense_bill_paid_amount']);
                        $conf_val += $time['expense_bill_paid_amount'];
                        $new_date = $this->utctotimezone($time['added_date'],$timezone,$pass);
                    }
                }
                if($is_check == true){
                    if($new_date != ''){
                        $tOrders[] = $time_val;
                        $received[] = $rev_val;
                        $confirmd[] = $conf_val;
                        $timedata[] = $new_date;
                    }
                }else{
                  
                    $tOrders[] = $time_val;
                    $received[] = $rev_val;
                    $confirmd[] = $conf_val;
                }
            }
        }
        if($change_time){
            $time_data = $timedata;
        }

        $tOrders = ['name'=>'Total Expense','data'=>$tOrders];
        $received = ['name' => 'Pending Amount','data'=>$received];
        $confirmd = ['name' => 'Paid Amount','data'=>$confirmd];
        /*$failed = ['name' => 'Failed','data'=>$failed];*/
     
        $expenseGroup['expense'] = array($tOrders,$received,$confirmd);
        $expenseGroup['total_expense'] = $total_expense;
        $expenseGroup['total_invoice'] = number_format($this->precise_amount(array_sum($tOrders['data']) , 2),2);
        $expenseGroup['total_pending'] = number_format($this->precise_amount(array_sum($received['data']) , 2),2);
        $expenseGroup['total_received'] = number_format($this->precise_amount(array_sum($confirmd['data']) , 2),2);
        $expenseGroup['time_data'] = $timedata;
        echo json_encode($expenseGroup);
        exit();
    }

    function stockGraph(){
        $timezone = 'Asia/Kolkata';
        $chartdata = $resp = $year_ary = $time_ary = $tOrders = $received = $confirmd = $timedata =array();
        $where = ' WHERE branch_id ='.$this->session->userdata('SESS_BRANCH_ID').' AND delete_status=0 ';
        $filter = trim($this->input->post('filter'));
        if($filter == '' ) $filter = 'last_7_days';

        $current_date = date('Y-m-d');

        if($filter == 'today'){
            $where .= " AND DATE_FORMAT(added_date, '%Y-%m-%d') = '{$current_date}'";
        }
        if($filter == 'yesterday'){
            $where .= " AND DATE_FORMAT(added_date, '%Y-%m-%d') = '{$current_date}' - INTERVAL 1 DAY";
        }
        if($filter == 'this_week'){
            $where .= " AND YEARWEEK(DATE_FORMAT(added_date, '%Y-%m-%d')) = YEARWEEK('{$current_date}')";
        }
        if($filter == 'last_7_days'){
            $where .= " AND DATE_FORMAT(added_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 6 DAY";
        }
        if($filter == 'this_month'){
            $where .= " AND MONTH(added_date) = MONTH('{$current_date}') AND YEAR(added_date) = YEAR('{$current_date}')";
        }
        if($filter == 'last_30_days'){
            $where .= " AND DATE_FORMAT(added_date, '%Y-%m-%d') >= '{$current_date}' - INTERVAL 29 DAY";
        }
        if($filter == 'this_year'){
            $where .= " AND YEAR(added_date) = YEAR('{$current_date}')";
        }

        $product_qry = $this->db->query("SELECT COUNT(product_id) as total_products, SUM(product_quantity) as total_stock,SUM(product_damaged_quantity) as product_damaged_quantity, SUM(product_missing_quantity) as product_missing_quantity,added_date FROM products {$where} GROUP BY DATE_FORMAT(added_date, '%Y-%m-%d') ORDER BY `product_id` DESC");
        $time_ary = $product_qry->result_array();
        $total_products = 0;
        
        foreach ($time_ary as $key => $value) {
            $total_products += $value['total_products'];
        }

        $year_share_sql = "SELECT DATE_FORMAT(added_date, '%Y') as year FROM `products` {$where} GROUP BY DATE_FORMAT(added_date, '%Y') ORDER BY `year` ASC";

        $year_share_query = $this->db->query($year_share_sql);
        $year_ary = $year_share_query->result_array();
        $is_check = $change_time = $custome_date = false; 

        if($filter == 'today' || $filter == 'yesterday'){
   
            for($i=0;$i<=23;$i++){
                $time_val = $rev_val = $conf_val= $fail_val = 0;

                foreach ($time_ary as $time) {
           
                    if($this->utctotimezone($time['added_date'],$timezone,'hour') == $i){
                        $time_val += $time['total_stock'];
                        $rev_val += $time['product_damaged_quantity'];
                        $conf_val += $time['product_missing_quantity'];
                    }
                }
                $tOrders[] = $time_val;
                $received[] = $rev_val;
                $confirmd[] = $conf_val;
            }
        }else{

            $pass = 'date';
            $is_check = $change_time = $custome_date = false; 

            if($filter == 'custome'){ 
                $time_data = $this->GetCustomDate($time_ary,$timezone);
                if(count($time_data) > 12){
                    $custome_date = true;
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;
                }
            }

            if($filter == 'this_week') $time_data = $this->getCurrentweekdays();
        
            if($filter == 'last_7_days') $time_data = $this->getLastNDays(7);

            if($filter == 'this_month'){$is_check = true;  $time_data = $this->getCurrentmonthdays(); $change_time = true;}

            if($filter == 'last_30_days'){$is_check = true; $time_data = $this->getLastNDays(30); $change_time = true;}

            if($filter == 'this_year'){ $pass = 'month'; $time_data = $this->getCurrentyearmonth(); $change_time = true;}

            if($filter == 'all') { 
                $time_data = $year_ary; $pass = 'year'; $change_time = true;
                if(count($year_ary) < 2){
                    $pass = 'month'; $time_data = $this->getCurrentyearmonth();
                }
            }
            $timedata = array();
            
            foreach ($time_data as $days){
                if($filter == 'this_year' || $custome_date == true) {
                    $timedata[] = $days['name'];
                    $days = $days['month'];
                }

                if($filter == 'all'){
                    if(count($year_ary) < 2){
                        $timedata[] = $days['name'];
                        $days = $days['month'];
                    }else{
                        $timedata[] = $days['year'];
                        $days = $days['year'];
                    }
                }

                $time_val = $rev_val = $conf_val = 0;
                $new_date = '';
               
                foreach ($time_ary as $time) {
                    if($this->utctotimezone($time['added_date'],$timezone,$pass) == $days){
                      
                        $time_val += $time['total_stock'];
                        $rev_val += $time['product_damaged_quantity'];
                        $conf_val += $time['product_missing_quantity'];
                        $new_date = $this->utctotimezone($time['added_date'],$timezone,$pass);
                    }
                }
                if($is_check == true){
                    if($new_date != ''){
                        $tOrders[] = $time_val;
                        $received[] = $rev_val;
                        $confirmd[] = $conf_val;
                        $timedata[] = $new_date;
                    }
                }else{
                  
                    $tOrders[] = $time_val;
                    $received[] = $rev_val;
                    $confirmd[] = $conf_val;
                }
                /*echo $time_val."<br>";
                print_r($tOrders);*/
            }
        }
        if($change_time){
            $time_data = $timedata;
        }

        $tOrders = ['name'=>'Total Stock','data'=>$tOrders];
        $received = ['name' => 'Damaged','data'=>$received];
        $confirmd = ['name' => 'Missing','data'=>$confirmd];
        /*$failed = ['name' => 'Failed','data'=>$failed];*/
     
        $stockGroup['stocks'] = array($tOrders,$received,$confirmd);
        $stockGroup['total_products'] = $total_products;
        $stockGroup['total_stock'] = array_sum($tOrders['data']);
        $stockGroup['total_damaged'] = array_sum($received['data']);
        $stockGroup['total_missing'] = array_sum($confirmd['data']);
        $stockGroup['time_data'] = $timedata;
        echo json_encode($stockGroup);
        exit();
    }

    public function productChart(){
        $timezone = 'Asia/Kolkata';
        $chartdata = $time_data = $resp = $year_ary = $time_ary = $tOrders =array();

        $where = ' WHERE p.delete_status = 0 AND s.delete_status=0 AND s.item_type="product" AND p.branch_id='.$this->session->userdata('SESS_BRANCH_ID');
        
        $item_qry = $this->db->query("SELECT COUNT(item_id) as total_items,p.product_name,p.product_batch FROM `sales_item` s JOIN products p ON s.item_id=p.product_id {$where} GROUP BY p.product_id ORDER BY total_items DESC LIMIT 5");
        $items_res = $item_qry->result_array();

        $all_items = array();
        $call_chartdata = array();
        
        if(!empty($items_res)){
            foreach ($items_res as $key => $value) {
                $all_items[] = array('name'=>$value['product_name'], 'y' =>  (float)number_format((float)$value['total_items'], 2, '.', ''),'batch' => $value['product_batch']);
            }
           
            $call_chartdata['name']= 'Top Sales';
            $call_chartdata['data'] = $all_items;
        }

        echo json_encode($call_chartdata);
        exit;
    }

    public function logout() {
        $this->data['title'] = "Logout";
        $session_id = session_id();
        $sess_user_id = $this->session->userdata('SESS_USER_ID'); 
        /*if($session_id != "" && $sess_user_id != ""){ 
            $table    = "login_auth";
            $log_data = array(
                    'logout_date_time'    => date('Y-m-d H:i:s'),
                    'status'              => 1
                );
             $where = array(
                "session_id" => $session_id ,
                "user_id"    => $sess_user_id
                );
           $this->general_model->updateData($table, $log_data, $where ); 
        }*/
        
        $this->session->sess_destroy();
        $this->cache->clean();
        ob_clean();
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect('auth/login', 'refresh');
    }

    

    public function change_password() {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $user = $this->ion_auth->user()->row();
        if ($this->form_validation->run() === false) {
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
            $this->data['old_password'] = array(
                'name' => 'old',
                'id' => 'old',
                'type' => 'password',
            );
            $this->data['new_password'] = array(
                'name' => 'new',
                'id' => 'new',
                'type' => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            );
            $this->data['new_password_confirm'] = array(
                'name' => 'new_confirm',
                'id' => 'new_confirm',
                'type' => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            );
            $this->data['user_id'] = array(
                'name' => 'user_id',
                'id' => 'user_id',
                'type' => 'hidden',
                'value' => $user->id,
            );
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'change_password', $this->data);
        } else {
            $identity = $this->session->userdata('identity');
            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            if ($change) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->logout();
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('auth/change_password', 'refresh');
            }
        }
    }

    public function forgot_password() {
        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        } else {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
            $this->form_validation->set_rules('login_code', 'Login Code', 'required');
        }
        if ($this->form_validation->run() === false) {
            $this->data['type'] = $this->config->item('identity', 'ion_auth');
            $this->data['identity'] = array(
                'name' => 'identity',
                'id' => 'identity',
                'value' => $this->input->post('identity')
            );
            $this->data['login_code'] = array(
                'name' => 'login_code',
                'id' => 'login_code',
                'value' => $this->input->post('login_code')
            );
            if ($this->config->item('identity', 'ion_auth') != 'email') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
            } else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'forgot_password', $this->data);
        } else {
            $identity_column = $this->config->item('identity', 'ion_auth');           
            $identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->where('branch_code', $this->input->post('login_code'))->users()->row();
            
            if (empty($identity)) {
                if ($this->config->item('identity', 'ion_auth') != 'email') {
                    $this->ion_auth->set_error('forgot_password_identity_not_found');
                } else {
                    $this->ion_auth->set_error('forgot_password_email_not_found');
                }
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("auth/forgot_password", 'refresh');
            }

            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')}, $identity->{$this->config->item('login_code', 'ion_auth')});

            $mail_id = $forgotten['identity'];
            $forgot_code = $forgotten['forgotten_password_code'];

            $html =  $this->load->view('email_template/email_forgot', '', TRUE);
            $message = "<a href = " . base_url('auth/reset_password/' . $forgot_code) . ">".base_url('auth/reset_password/' . $forgot_code)."</a>";
            $html = str_replace('{{Password_Reset_link}}', $message, $html);
            $emailDataSet = array(                         
                                'subject' =>'Confidential Mail',                    
                                'message' => $html,
                                'email'=>  $mail_id, 
                            );
              
            $is_send = $this->mailer->sendEmail($emailDataSet);

            if ($is_send == true) {
                redirect("auth/login", 'refresh');
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("auth/forgot_password", 'refresh');
            }
        }
    }

    public function reset_password($code = null) {
        if (!$code) {
            show_404();
        }
        $user = $this->ion_auth->forgotten_password_check($code);
        /* echo "<pre>";
         print_r($user);
         exit;*/
        if ($user) {
            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');
            if ($this->form_validation->run() === false) {

                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id,
                );
                $this->data['branch_id'] = array(
                    'name' => 'branch_id',
                    'id' => 'branch_id',
                    'type' => 'hidden',
                    'value' => $user->branch_id,
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;
                $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'reset_password', $this->data);
            } else {
                if ($this->_valid_csrf_nonce() === false || $user->id != $this->input->post('user_id')) {
                    $this->ion_auth->clear_forgotten_password_code($code);
                    show_error($this->lang->line('error_csrf'));
                } else {
                    $identity = $user->{$this->config->item('identity', 'ion_auth')};
                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'), $this->input->post('branch_id'));

                    if ($change) {
                        $html =  $this->load->view('email_template/email_reset', '', TRUE);
                        $emailDataSet = array(                         
                                            'subject' =>'Confidential Mail',                    
                                            'message' => $html,
                                            'email'=>  $user->email, 
                                        );
                          
                        $is_send = $this->mailer->sendEmail($emailDataSet);

                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect("auth/login", 'refresh');
                    } else {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        } else {
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    public function verification($code = null) {
        if (!$code) {
            show_404();
        }
        $user = $this->ion_auth->forgotten_password_check($code);
        /* echo "<pre>";
         print_r($user);
         exit;*/
        if ($user) {
            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');
            if ($this->form_validation->run() === false) {
                
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                );
                $this->data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id,
                );
                $this->data['branch_id'] = array(
                    'name' => 'branch_id',
                    'id' => 'branch_id',
                    'type' => 'hidden',
                    'value' => $user->branch_id,
                );
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;
                $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'reset_password', $this->data);
            } else {
                if ($this->_valid_csrf_nonce() === false || $user->id != $this->input->post('user_id')) {
                    $this->ion_auth->clear_forgotten_password_code($code);
                    show_error($this->lang->line('error_csrf'));
                } else {
                    $identity = $user->{$this->config->item('identity', 'ion_auth')};
                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'), $this->input->post('branch_id'));

                    if ($change) {
                        $html =  $this->load->view('email_template/email_reset', '', TRUE);
                        $emailDataSet = array(                         
                                            'subject' =>'Confidential Mail',                    
                                            'message' => $html,
                                            'email'=>  $user->email, 
                                        );
                          
                        $is_send = $this->mailer->sendEmail($emailDataSet);

                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect("auth/login", 'refresh');
                    } else {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        } else {
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    public function activate($id, $code = false) {
        $id = $this->encryption_url->decode($id);
        $modules = $this->get_modules();
        foreach ($modules['modules'] as $key => $value) {
            $this->data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $this->data['active_view'][$key] = $value->module_id;
            }
            if ($value->edit_privilege == "yes") {
                $this->data['active_edit'][$key] = $value->module_id;
            }
            if ($value->delete_privilege == "yes") {
                $this->data['active_delete'][$key] = $value->module_id;
            }
            if ($value->add_privilege == "yes") {
                $this->data['active_add'][$key] = $value->module_id;
            }
        }
        if ($code !== false) {
            $activation = $this->ion_auth->activate($id, $code);
        } elseif ($this->ion_auth->is_admin()) {
            $activation = $this->ion_auth->activate($id);
        }
        if ($activation) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        } else {
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    public function deactivate($id = null) {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            return show_error('You must be an administrator to view this page.');
        }

        $modules = $this->get_modules();
        $user_module_id = $this->config->item('user_module');
        $privilege = "add_privilege";
        $this->data['module_id'] = $user_module_id;
        $this->data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        /* presents all the needed */

        $this->data = array_merge($this->data, $section_modules);
        $this->data['access_common_settings '] = $access_common_settings = $section_modules['access_common_settings'];
        foreach ($modules['modules'] as $key => $value) {
            $this->data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes") {
                $this->data['active_view'][$key] = $value->module_id;
            }
            if ($value->edit_privilege == "yes") {
                $this->data['active_edit'][$key] = $value->module_id;
            }
            if ($value->delete_privilege == "yes") {
                $this->data['active_delete'][$key] = $value->module_id;
            }
            if ($value->add_privilege == "yes") {
                $this->data['active_add'][$key] = $value->module_id;
            }
        }

        $id = $this->encryption_url->decode($id);
        $id = (int) $id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
        $this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');
        if ($this->form_validation->run() === false) {
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'deactivate_user', $this->data);
        } else {
              
            if ($this->input->post('confirm') == 'yes') {
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                }
            }else{
                $this->ion_auth_model->deactivate($id);
            }
            redirect('auth', 'refresh');
        }
    }

    public function create_user() {
        $modules = $this->get_modules();
        $user_module_id = $this->config->item('user_module');
        $privilege = "add_privilege";
        $data['module_id'] = $user_module_id;
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        /* presents all the needed */


        $data = array_merge($data, $section_modules);
        $data['groups'] = $this->general_model->getRecords("groups.*", "groups", [
            "delete_status" => 0,
            "branch_id"         => $this->session->userdata("SESS_BRANCH_ID")]);
        $data['access_common_settings '] = $access_common_settings = $section_modules['access_common_settings'];
        $this->load->view("auth/create_user", $data);
    }

    public function redirectUser() {
        if ($this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }
        redirect('/', 'refresh');
    }

    public function edit_user($id) {
        $id = $this->encryption_url->decode($id);
        $modules = $this->get_modules();
        $user_module_id = $this->config->item('user_module');
        $privilege = "edit_privilege";
        $data['module_id'] = $user_module_id;
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $this->data = array_merge($data, $section_modules);
        $this->data['access_common_settings '] = $access_common_settings = $section_modules['access_common_settings'];
        $this->data['title'] = $this->lang->line('edit_user_heading');
        if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
            redirect('auth', 'refresh');
        }
        $user = $this->ion_auth->user($id)->row();
        $groups = $this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();
        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
        $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'trim|required');
        $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'trim|required');
        if (isset($_POST) && !empty($_POST)) {
            if ($this->_valid_csrf_nonce() === false || $id != $this->input->post('id')) {
                show_error($this->lang->line('error_csrf'));
            }
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
            }
            if ($this->form_validation->run() === true) {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                );
                if ($this->input->post('password')) {
                    $data['password'] = $this->input->post('password');
                }
                if ($this->ion_auth->is_admin()) {
                    $groupData = $this->input->post('groups');
                    if (isset($groupData) && !empty($groupData)) {
                        $this->ion_auth->remove_from_group('', $id);
                        foreach ($groupData as $grp) {
                            $this->ion_auth->add_to_group($grp, $id);
                        }
                    }
                }
                if ($this->ion_auth->update($user->id, $data)) {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    $this->redirectUser();
                } else {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                    $this->redirectUser();
                }
            }
        }
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;
        $this->data['first_name'] = array(
            'name' => 'first_name',
            'id' => 'first_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('first_name', $user->first_name),
        );
        $this->data['last_name'] = array(
            'name' => 'last_name',
            'id' => 'last_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('last_name', $user->last_name),
        );
        $this->data['company'] = array(
            'name' => 'company',
            'id' => 'company',
            'type' => 'text',
            'value' => $this->form_validation->set_value('company', $user->company),
        );
        $this->data['phone'] = array(
            'name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'value' => $this->form_validation->set_value('phone', $user->phone),
        );
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'type' => 'password'
        );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id' => 'password_confirm',
            'type' => 'password'
        );
        $this->data['groups'] = $this->general_model->getRecords("groups.*", "groups", [
            "delete_status" => 0,
            "branch_id"         => $this->session->userdata("SESS_BRANCH_ID")]);
        $this->data['user_group'] = $this->general_model->getRecords("users_groups.*", "users_groups", [
            "user_id" => $id]);
        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'edit_user', $this->data);
    }

    public function create_group() {
        $this->data['title'] = $this->lang->line('create_group_title');
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }
        $this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');
        if ($this->form_validation->run() === true) {
            $new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
            if ($new_group_id) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("auth", 'refresh');
            }
        } else {
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['group_name'] = array(
                'name' => 'group_name',
                'id' => 'group_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('group_name'),
            );
            $this->data['description'] = array(
                'name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'value' => $this->form_validation->set_value('description'),
            );
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'create_group', $this->data);
        }
    }

    public function edit_group($id) {
        if (!$id || empty($id)) {
            redirect('auth', 'refresh');
        }
        $this->data['title'] = $this->lang->line('edit_group_title');
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }
        $group = $this->ion_auth->group($id)->row();
        $this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash');
        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === true) {
                $group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);
                if ($group_update) {
                    $this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
                } else {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                }
                redirect("auth", 'refresh');
            }
        }
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['group'] = $group;
        $readonly = $this->config->item('admin_group', 'ion_auth') === $group->name ? 'readonly' : '';
        $this->data['group_name'] = array(
            'name' => 'group_name',
            'id' => 'group_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_name', $group->name),
            $readonly => $readonly,
        );
        $this->data['group_description'] = array(
            'name' => 'group_description',
            'id' => 'group_description',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_description', $group->description),
        );
        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'edit_group', $this->data);
    }

    public function _get_csrf_nonce() {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);
        return array(
            $key => $value);
    }

    public function _valid_csrf_nonce() {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
            return true;
        }
        return false;
    }

    public function _render_page($view, $data = null, $returnhtml = false) {
        $this->viewdata = (empty($data)) ? $this->data : $data;
        $view_html = $this->load->view($view, $this->viewdata, $returnhtml);
        if ($returnhtml) {
            return $view_html;
        }
    }

    public function product_profit() {
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $data = $this->db->where('delete_status', 0)->where('branch_id', $branch_id)->get('sales')->result();
        $purchase = $this->db->where('delete_status', 0)->where('branch_id', $branch_id)->get('purchase')->result();
        $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sep = $oct = $nov = $dec = 0;
        $jan1 = array(
            "month" => "Jan",
            "sales" => 0,
            "purchase" => 0);
        $feb1 = array(
            "month" => "Feb",
            "sales" => 0,
            "purchase" => 0);
        $mar1 = array(
            "month" => "Mar",
            "sales" => 0,
            "purchase" => 0);
        $apr1 = array(
            "month" => "Apr",
            "sales" => 0,
            "purchase" => 0);
        $may1 = array(
            "month" => "May",
            "sales" => 0,
            "purchase" => 0);
        $jun1 = array(
            "month" => "Jun",
            "sales" => 0,
            "purchase" => 0);
        $jul1 = array(
            "month" => "Jul",
            "sales" => 0,
            "purchase" => 0);
        $aug1 = array(
            "month" => "Aug",
            "sales" => 0,
            "purchase" => 0);
        $sep1 = array(
            "month" => "Sep",
            "sales" => 0,
            "purchase" => 0);
        $oct1 = array(
            "month" => "Oct",
            "sales" => 0,
            "purchase" => 0);
        $nov1 = array(
            "month" => "Nov",
            "sales" => 0,
            "purchase" => 0);
        $dec1 = array(
            "month" => "Dec",
            "sales" => 0,
            "purchase" => 0);
        foreach ($data as $value) {
            $date = date_parse_from_format("Y-m-d", $value->sales_date);
            if ($date["month"] == 1) {
                $jan = $jan + $value->sales_grand_total;
                $jan1["sales"] = $jan;
            } elseif ($date["month"] == 2) {
                $feb = $feb + $value->sales_grand_total;
                $feb1["sales"] = $feb;
            } elseif ($date["month"] == 3) {
                $mar = $mar + $value->sales_grand_total;
                $mar1["sales"] = $mar;
            } elseif ($date["month"] == 4) {
                $apr = $apr + $value->sales_grand_total;
                $apr1["sales"] = $apr;
            } elseif ($date["month"] == 5) {
                $may = $may + $value->sales_grand_total;
                $may1["sales"] = $may;
            } elseif ($date["month"] == 6) {
                $jun = $jun + $value->sales_grand_total;
                $jun1["sales"] = $jun;
            } elseif ($date["month"] == 7) {
                $jul = $jul + $value->sales_grand_total;
                $jul1["sales"] = $jul;
            } elseif ($date["month"] == 8) {
                $aug = $aug + $value->sales_grand_total;
                $aug1["sales"] = $aug;
            } elseif ($date["month"] == 9) {
                $sep = $sep + $value->sales_grand_total;
                $sep1["sales"] = $sep;
            } elseif ($date["month"] == 10) {
                $oct = $oct + $value->sales_grand_total;
                $oct1["sales"] = $oct;
            } elseif ($date["month"] == 11) {
                $nov = $nov + $value->sales_grand_total;
                $nov1["sales"] = $nov;
            } elseif ($date["month"] == 12) {
                $dec = $dec + $value->sales_grand_total;
                $dec1["sales"] = $dec;
            }
        }
        $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sep = $oct = $nov = $dec = 0;
        foreach ($purchase as $value) {
            $date = date_parse_from_format("Y-m-d", $value->purchase_date);
            if ($date["month"] == 1) {
                $jan = $jan + $value->purchase_grand_total;
                $jan1["purchase"] = $jan;
            } elseif ($date["month"] == 2) {
                $feb = $feb + $value->purchase_grand_total;
                $feb1["purchase"] = $feb;
            } elseif ($date["month"] == 3) {
                $mar = $mar + $value->purchase_grand_total;
                $mar1["purchase"] = $mar;
            } elseif ($date["month"] == 4) {
                $apr = $apr + $value->purchase_grand_total;
                $apr1["purchase"] = $apr;
            } elseif ($date["month"] == 5) {
                $may = $may + $value->purchase_grand_total;
                $may1["purchase"] = $may;
            } elseif ($date["month"] == 6) {
                $jun = $jun + $value->purchase_grand_total;
                $jun1["purchase"] = $jun;
            } elseif ($date["month"] == 7) {
                $jul = $jul + $value->purchase_grand_total;
                $jul1["purchase"] = $jul;
            } elseif ($date["month"] == 8) {
                $aug = $aug + $value->purchase_grand_total;
                $aug1["purchase"] = $aug;
            } elseif ($date["month"] == 9) {
                $sep = $sep + $value->purchase_grand_total;
                $sep1["purchase"] = $sep;
            } elseif ($date["month"] == 10) {
                $oct = $oct + $value->purchase_grand_total;
                $oct1["purchase"] = $oct;
            } elseif ($date["month"] == 11) {
                $nov = $nov + $value->purchase_grand_total;
                $nov1["purchase"] = $nov;
            } elseif ($date["month"] == 12) {
                $dec = $dec + $value->purchase_grand_total;
                $dec1["purchase"] = $dec;
            }
        }
        $salesData = array();
        array_push($salesData, $jan1);
        array_push($salesData, $feb1);
        array_push($salesData, $mar1);
        array_push($salesData, $apr1);
        array_push($salesData, $may1);
        array_push($salesData, $jun1);
        array_push($salesData, $jul1);
        array_push($salesData, $aug1);
        array_push($salesData, $sep1);
        array_push($salesData, $oct1);
        array_push($salesData, $nov1);
        array_push($salesData, $dec1);
        echo json_encode($salesData, true);
    }

    public function edit() {
        $id = $this->input->post('id');
        $group = $this->input->post("cmb_group");
        $this->db->where('user_id',$id);
        $grp = $this->db->get('users_groups');
        $group_result = $grp->result();

        $user_module_id = $this->config->item('user_module');
        $privilege = "edit_privilege";
        $modules = $this->get_modules();
        $data['module_id'] = $user_module_id;
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);

        if(!empty($grp->result())){

            $previous_group_id = $group_result[0]->group_id;
            $id1 = $this->general_model->updateData("users_groups", [
                "group_id" => $group], [
                "user_id" => $id]);
            $successMsg = 'User Updated Successfully';
            $this->session->set_flashdata('new_user_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id1,
                'table_name' => 'users_groups',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'User Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
                /*new implementation - hari*/
                if($previous_group_id != $group){
                    if($this->general_model->updateData('user_accessibility', array(
                    'delete_status' => 1 ), array(
                    'user_id' => $id,'branch_id' => $this->session->userdata('SESS_BRANCH_ID')))){
                        
                        $list_data           = $this->common->assigned_module_list_field($group);
                        $assigned_data       = $this->general_model->getPageJoinRecords($list_data);

                        $data_item = array();
                        foreach ($assigned_data as $key => $value) {
                            $data_item[$key]['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
                            $data_item[$key]['user_id'] = $id;
                            $data_item[$key]['module_id'] = $value->module_id;
                            if ($value->add_privilege == 1) {
                                $data_item[$key]['add_privilege'] = "yes";
                            } else {
                                $data_item[$key]['add_privilege'] = "no";
                            }
                            if ($value->edit_privilege == 1) {
                                $data_item[$key]['edit_privilege'] = "yes";
                            } else {
                                $data_item[$key]['edit_privilege'] = "no";
                            }
                            if ($value->delete_privilege == 1) {
                                $data_item[$key]['delete_privilege'] = "yes";
                            } else {
                                $data_item[$key]['delete_privilege'] = "no";
                            }
                            if ($value->view_privilege == 1) {
                                $data_item[$key]['view_privilege'] = "yes";
                            } else {
                                $data_item[$key]['view_privilege'] = "no";
                            }
                        }
                        foreach ($data_item as $value) {
                            $this->general_model->insertData("user_accessibility", $value);
                        }
                    }
                }
        }else{
            $grp_data = array('user_id' => $id, 'group_id' => $group);
            $this->db->insert('users_groups',$grp_data);
        }
        
        if ($this->input->post("password")) {
            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password')
            );
            $this->ion_auth->update($id, $data);
        } else {
            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email')
            );
            $this->ion_auth->update($id, $data);
        }
        /*$user_accessibility_data = $this->common->user_accessibility_field($id);
        $user_accessibility_items = $this->general_model->getRecords($user_accessibility_data['string'], $user_accessibility_data['table'], $user_accessibility_data['where']);
        $active_modules = array();
        $modules = $this->get_modules();
        foreach ($modules['modules'] as $key => $value) {
            if (!in_array($value, $active_modules) && $value != "") {
                $active_modules[] = $value->module_id;
            }
        }
        $modules_assigned_section = $this->config->item('modules_assigned_section');
        $add_modules_assigned_section = $this->config->item('add_modules_assigned_section');
        $edit_modules_assigned_section = $this->config->item('edit_modules_assigned_section');
        $delete_modules_assigned_section = $this->config->item('delete_modules_assigned_section');
        $view_modules_assigned_section = $this->config->item('view_modules_assigned_section');
        if ($group == $this->config->item('admin_group')) {
            $module_section = $modules_assigned_section['admin'];
            $add_section = $add_modules_assigned_section['admin'];
            $edit_section = $edit_modules_assigned_section['admin'];
            $delete_section = $delete_modules_assigned_section['admin'];
            $view_section = $view_modules_assigned_section['admin'];
        }
        if ($group == $this->config->item('members_group')) {
            $module_section = $modules_assigned_section['members'];
            $add_section = array();
            $edit_section = array();
            $delete_section = array();
            $view_section = $view_modules_assigned_section['members'];
        }
        if ($group == $this->config->item('purchaser_group')) {
            $module_section = $modules_assigned_section['purchaser'];
            $add_section = $add_modules_assigned_section['purchaser'];
            $edit_section = $edit_modules_assigned_section['purchaser'];
            $delete_section = array();
            $view_section = $view_modules_assigned_section['purchaser'];
        }
        if ($group == $this->config->item('sales_person_group')) {
            $module_section = $modules_assigned_section['sales_person'];
            $add_section = $add_modules_assigned_section['sales_person'];
            $edit_section = $edit_modules_assigned_section['sales_person'];
            $delete_section = array();
            $view_section = $view_modules_assigned_section['sales_person'];
        }
        if ($group == $this->config->item('manager_group')) {
            $module_section = $modules_assigned_section['manager'];
            $add_section = $add_modules_assigned_section['manager'];
            $edit_section = $edit_modules_assigned_section['manager'];
            $delete_section = $delete_modules_assigned_section['manager'];
            $view_section = $view_modules_assigned_section['manager'];
        }
        if ($group == $this->config->item('accountant_group')) {
            $module_section = $modules_assigned_section['accountant'];
            $add_section = $add_modules_assigned_section['accountant'];
            $edit_section = $edit_modules_assigned_section['accountant'];
            $delete_section = $delete_modules_assigned_section['accountant'];
            $view_section = $view_modules_assigned_section['accountant'];
        }
        $data_item = array();
        foreach ($active_modules as $key => $value) {
            $data_item[$key]['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
            $data_item[$key]['user_id'] = $id;
            $data_item[$key]['module_id'] = $value;
            if (in_array($value, $add_section)) {
                $data_item[$key]['add_privilege'] = "yes";
            } else {
                $data_item[$key]['add_privilege'] = "no";
            }
            if (in_array($value, $edit_section)) {
                $data_item[$key]['edit_privilege'] = "yes";
            } else {
                $data_item[$key]['edit_privilege'] = "no";
            }
            if (in_array($value, $delete_section)) {
                $data_item[$key]['delete_privilege'] = "yes";
            } else {
                $data_item[$key]['delete_privilege'] = "no";
            }
            if (in_array($value, $view_section)) {
                $data_item[$key]['view_privilege'] = "yes";
            } else {
                $data_item[$key]['view_privilege'] = "no";
            }
        }
        if (count($user_accessibility_items) == count($data_item)) {
            foreach ($user_accessibility_items as $key => $value) {
                $table = 'user_accessibility';
                $where = array(
                    'accessibility_id' => $value->accessibility_id);
                $this->general_model->updateData($table, $data_item[$key], $where);
            }
        } elseif (count($user_accessibility_items) < count($data_item)) {
            $i = -1;
            foreach ($user_accessibility_items as $key => $value) {
                $table = 'user_accessibility';
                $where = array(
                    'accessibility_id' => $value->accessibility_id);
                $this->general_model->updateData($table, $data_item[$key], $where);
                $i = $key;
            }
            for ($j = $i + 1; $j < count($data_item); $j++) {
                $table = 'user_accessibility';
                $this->general_model->insertData($table, $data_item[$j]);
            }
        } else {
            $i = -1;
            foreach ($user_accessibility_items as $key => $value) {
                $table = 'user_accessibility';
                $where = array(
                    'accessibility_id' => $value->accessibility_id);
                $this->general_model->updateData($table, $data_item[$key], $where);
                $i = $key;
                if (($key + 1) == count($data_item)) {
                    break;
                }
            }
            for ($j = $i + 1; $j < count($user_accessibility_items); $j++) {
                $table = 'user_accessibility';
                $where = array(
                    'accessibility_id' => $user_accessibility_items[$j]->accessibility_id);
                $access_data = array(
                    'delete_status' => 1);
                $this->general_model->updateData($table, $access_data, $where);
            } 
        } */
        redirect("auth");
    }

    public function create_new_user() {
        $user_module_id = $this->config->item('user_module');
        $privilege = "add_privilege";
        $modules = $this->get_modules();
        $data['module_id'] = $user_module_id;
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email = strtolower($this->input->post('email'));
        $identity = $email;
        $password = $this->input->post('password');
        $additional_data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'company' => $this->input->post('company'),
            'phone' => $this->input->post('phone'),
        );
        $group = $this->input->post("cmb_group");
        
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $user_id = $this->ion_auth->register($branch_id, $identity, $password, $email, $additional_data);
        $group = $this->input->post("cmb_group");
        $id = $this->general_model->insertData("users_groups", [
            "user_id" => $user_id,
            "group_id" => $group]);
        $successMsg = 'User Added Successfully';
        $this->session->set_flashdata('new_user_success',$successMsg);
        $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'users_groups',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'New User Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);

        /*new implementation*/
        $list_data           = $this->common->assigned_module_list_field($group);
        $assigned_data       = $this->general_model->getPageJoinRecords($list_data);

        $data_item = array();
        foreach ($assigned_data as $key => $value) {
            $data_item[$key]['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
            $data_item[$key]['user_id'] = $user_id;
            $data_item[$key]['module_id'] = $value->module_id;
            if ($value->add_privilege == 1) {
                $data_item[$key]['add_privilege'] = "yes";
            } else {
                $data_item[$key]['add_privilege'] = "no";
            }
            if ($value->edit_privilege == 1) {
                $data_item[$key]['edit_privilege'] = "yes";
            } else {
                $data_item[$key]['edit_privilege'] = "no";
            }
            if ($value->delete_privilege == 1) {
                $data_item[$key]['delete_privilege'] = "yes";
            } else {
                $data_item[$key]['delete_privilege'] = "no";
            }
            if ($value->view_privilege == 1) {
                $data_item[$key]['view_privilege'] = "yes";
            } else {
                $data_item[$key]['view_privilege'] = "no";
            }
        }
        foreach ($data_item as $value) {
            $this->general_model->insertData("user_accessibility", $value);
        }

        /*$modules = $this->general_model->getActiveRemianingModules($user_id,$branch_id);*/
        redirect("auth");
        /*foreach($modules as $module ){
                $module_id = $module->module_id;
                $access_array = array("branch_id" => $branch_id,
                                      "user_id" => $user_id,
                                       "add_privilege" => 'yes',
                                       "edit_privilege" => 'yes',   
                                        "delete_privilege" => 'yes',    
                                        "view_privilege" => 'yes',  
                                        "module_id" => $module_id,
                                    );

            $id =  $this->general_model->insertData('user_accessibility',$access_array);
        }*/
        
        /*$active_modules = array();

        $modules = $this->get_modules();
        foreach ($modules['modules'] as $key => $value) {
            if (!in_array($value, $active_modules) && $value != "") {
                $active_modules[] = $value->module_id;
            }
        }
        $modules_assigned_section = $this->config->item('modules_assigned_section');
        $add_modules_assigned_section = $this->config->item('add_modules_assigned_section');
        $edit_modules_assigned_section = $this->config->item('edit_modules_assigned_section');
        $delete_modules_assigned_section = $this->config->item('delete_modules_assigned_section');
        $view_modules_assigned_section = $this->config->item('view_modules_assigned_section');
        if ($group == $this->config->item('admin_group')) {
            $module_section = $modules_assigned_section['admin'];
            $add_section = $add_modules_assigned_section['admin'];
            $edit_section = $edit_modules_assigned_section['admin'];
            $delete_section = $delete_modules_assigned_section['admin'];
            $view_section = $view_modules_assigned_section['admin'];
        }
        if ($group == $this->config->item('members_group')) {
            $module_section = $modules_assigned_section['members'];
            $add_section = array();
            $edit_section = array();
            $delete_section = array();
            $view_section = $view_modules_assigned_section['members'];
        }
        if ($group == $this->config->item('purchaser_group')) {
            $module_section = $modules_assigned_section['purchaser'];
            $add_section = $add_modules_assigned_section['purchaser'];
            $edit_section = $edit_modules_assigned_section['purchaser'];
            $delete_section = array();
            $view_section = $view_modules_assigned_section['purchaser'];
        }
        if ($group == $this->config->item('sales_person_group')) {
            $module_section = $modules_assigned_section['sales_person'];
            $add_section = $add_modules_assigned_section['sales_person'];
            $edit_section = $edit_modules_assigned_section['sales_person'];
            $delete_section = array();
            $view_section = $view_modules_assigned_section['sales_person'];
        }
        if ($group == $this->config->item('manager_group')) {
            $module_section = $modules_assigned_section['manager'];
            $add_section = $add_modules_assigned_section['manager'];
            $edit_section = $edit_modules_assigned_section['manager'];
            $delete_section = $delete_modules_assigned_section['manager'];
            $view_section = $view_modules_assigned_section['manager'];
        }
        if ($group == $this->config->item('accountant_group')) {
            $module_section = $modules_assigned_section['accountant'];
            $add_section = $add_modules_assigned_section['accountant'];
            $edit_section = $edit_modules_assigned_section['accountant'];
            $delete_section = $delete_modules_assigned_section['accountant'];
            $view_section = $view_modules_assigned_section['accountant'];
        }
        $data_item = array();
        foreach ($active_modules as $key => $value) {
            $data_item[$key]['branch_id'] = $this->session->userdata('SESS_BRANCH_ID');
            $data_item[$key]['user_id'] = $user_id;
            $data_item[$key]['module_id'] = $value;
            if (in_array($value, $add_section)) {
                $data_item[$key]['add_privilege'] = "yes";
            } else {
                $data_item[$key]['add_privilege'] = "no";
            }
            if (in_array($value, $edit_section)) {
                $data_item[$key]['edit_privilege'] = "yes";
            } else {
                $data_item[$key]['edit_privilege'] = "no";
            }
            if (in_array($value, $delete_section)) {
                $data_item[$key]['delete_privilege'] = "yes";
            } else {
                $data_item[$key]['delete_privilege'] = "no";
            }
            if (in_array($value, $view_section)) {
                $data_item[$key]['view_privilege'] = "yes";
            } else {
                $data_item[$key]['view_privilege'] = "no";
            }
        }*/
    }

    public function get_check_email() {
        $email = $this->input->post('email');
        $id = $this->input->post('id');
        $branch_id = $this->input->post('branch_id');
        if(!@$branch_id){
            $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        }
        $data = $this->general_model->getRecords('count(*) num', 'users', array(
            'email' => $email,
            'id !=' => $id,
            'branch_id' => $branch_id));
        
        echo json_encode($data);
    }

    public function forgot_password_mail() {
       
        $email = $this->input->post('identity');
        $val = $this->general_model->getRecords('users.*', 'users', [
            'users.email' => $email]);
        echo $forgot_code = md5(mt_rand(0, 10000));
        die;
        print_r($val);
        die;
    }
    public function send_mail($email_id, $message) {
        require APPPATH . 'third_party/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
      // $mail->isSMTP();
        /* $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "karthik.u@aavana.in";
        $mail->Password = "karthik@123";
        $mail->SMTPSecure = 'tls';
        $mail->Port = '587';
        $mail->IsHTML(true); */
        //$email_id = "karthik.t@aavana.in";
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "chetna.b@aavana.in";
        $mail->Password   = "ZXCVzxcv";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = '587';
        $mail->IsHTML(true);
        $mail->CharSet    = 'UTF-8';
        $from = 'Aavana Corporate Solution[noreply@aodry.com]';
        $mail->setFrom($from);
        $mail->addReplyTo($from);
        $mail->addAddress($email_id);
        $mail->isHTML(true);
        $bodyContent = 'Please click on the link to reset your password';
        $mail->Subject = "Confidential Mail";
        $mail->Body = 'Please click on the link to reset your password ' . $message;
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        } else {
            return true;
        }
    }
    /*public function reports() {
        $data['test'] = "test";
        $data = $this->ion_auth_model->allReports();
        $this->load->view('auth/dashboard', $data);
    }*/

    public function loader() {
        $data['test'] = "test";
        $data = $this->ion_auth_model->allReports();
        $this->load->view('loader/loader', $data);
    }

}
