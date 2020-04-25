<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Financial_year extends MY_Controller{
    public $data = array();
    public $branch_id = 0;
    function __construct(){
        parent::__construct();
        $this->load->model([
            'general_model' ,
            'product_model' ,
            'service_model' ,
            'ledger_model' ]);
        $this->modules = $this->get_modules();
        $this->load->library('SSP');
        $this->branch_id = $this->session->userdata('SESS_BRANCH_ID');
    }

    function index(){
        $financial_year_module_id               = $this->config->item('financial_year_module');
        $this->data['financial_year_module_id'] = $financial_year_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $this->data['privilege']         = $privilege;
        $section_modules                 = $this->get_section_modules($financial_year_module_id, $modules, $privilege);

        $this->data['tds_section']      = $this->tds_section_call();
        /* presents all the needed */
        $this->data=array_merge($this->data,$section_modules);
        
        $this->load->view('financial_year/financial_year',$this->data);
    }

    public function addNewYear(){
        $financial_year_module_id               = $this->config->item('financial_year_module');
        $this->data['financial_year_module_id'] = $financial_year_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $this->data['privilege']         = $privilege;
        $section_modules                 = $this->get_section_modules($financial_year_module_id, $modules, $privilege);
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $year_from_date = '01-'.$this->input->post('year_from_date');
        $year_from_date = date('Y-m-01 H:i:s',strtotime($year_from_date));
        $year_to_date = '01-'.$this->input->post('year_to_date');
        $last_date = date('t',strtotime($year_to_date));
        $year_to_date = $last_date.'-'.$this->input->post('year_to_date');
        $year_to_date = date('Y-m-d H:i:s',strtotime($year_to_date));

        /* find date diff */
        $date1 = $year_from_date;
        $date2 = $year_to_date;

        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;

        if($diff < 0){
            $this->data['flag'] = false;
            $this->data['msg'] = 'Select valid date!';
        }elseif($diff != 12){
            $this->data['flag'] = false;
            $this->data['msg'] = "Your financial year can't be more than 12 months!";
        }else{
            $is_current = $this->input->post('is_current');
            if($is_current == '') $is_current = 0;
            $this->db->select('year_id,from_date,to_date');
            /*$this->db->where('to_date > ',$year_from_date);*/
            $this->db->where('branch_id',$branch_id);
            $qry = $this->db->get('tbl_financial_year');
            $result = $qry->result_array();
            $is_added = $qry->num_rows();
            $flag = true;
            if(!empty($result)){
                foreach ($result as $key => $value) {
                    if(strtotime($value['to_date']) > strtotime($year_from_date) ){
                        if(strtotime($value['to_date']) <  strtotime($year_to_date)){
                            $flag = false;
                            $this->data['flag'] = false;
                            $this->data['msg'] = "Financial year already exist!";
                            break;
                        }else if(strtotime($value['to_date']) <  strtotime($year_to_date)){
                            $flag = false;
                            $this->data['flag'] = false;
                            $this->data['msg'] = "Financial year already exist!";
                            break;
                        }
                    }
                }
                if($flag){
                    $this->db->select('year_id,from_date,to_date');
                    $this->db->where('to_date =',$year_to_date);
                    $this->db->where('from_date =',$year_from_date);
                    $this->db->where('branch_id',$branch_id);
                    $qry = $this->db->get('tbl_financial_year');

                    if($qry->num_rows() > 0){
                        $flag = false;
                        $this->data['flag'] = false;
                        $this->data['msg'] = "Financial year already exist!";
                    }
                }
                if($is_current == '1'){
                    $this->db->set('is_current','0');
                    $this->db->where('branch_id',$branch_id);
                    $this->db->update('tbl_financial_year');
                }
            }else{
                $is_current = 1;
            }
            if($flag){
                $addAcc = array(
                            'branch_id' => $branch_id,
                            'from_date' => $year_from_date,
                            'to_date' => $year_to_date,
                            'is_current' => $is_current,
                            'created_ts' => date('Y-m-d H:i:s'),
                            'created_by' => $this->session->userdata('userId')
                        );

                if($is_added == 0){
                    $default_year = array(
                            'branch_id' => $branch_id,
                            'is_default' => '1',
                            'created_ts' => date('Y-m-d H:i:s'),
                            'created_by' => $this->session->userdata('userId')
                        );
                    $this->general_model->insertData("tbl_financial_year", $default_year);
                }

                $accId = $this->general_model->insertData("tbl_financial_year", $addAcc);
                $this->createOption_finance($accId,$year_from_date,$year_to_date);
                if($is_current ==  '1'){
                    $this->session->set_userdata('SESS_FINANCIAL_YEAR_ID', $accId);
                    $this->db->set('financial_year_id', $accId);
                    $this->db->where('branch_id',$this->session->userdata('SESS_BRANCH_ID'));
                    $this->db->update('branch');
                }
                $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => 0,
                    'table_name'        => 'tbl_financial_year',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Financial Year Inserted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
                $this->data['flag'] = true;
                $this->data['msg'] = 'Financial year added successfully!';
                
            }else{
                $this->data['flag'] = false;
                $this->data['msg'] = 'Financial year already exist!';
            }
        }

        echo json_encode($this->data);
        exit;
    }

    public function getAllAccYear(){
        $financial_year_module_id               = $this->config->item('financial_year_module');
        $data['financial_year_module_id']       = $financial_year_module_id;
        $this->data['financial_year_module_id'] = $financial_year_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $this->data['privilege']         = $privilege;
        $section_modules                 = $this->get_section_modules($financial_year_module_id, $modules, $privilege);
        /* presents all the needed */
        $data                   = array_merge($data , $section_modules);
        $branch_id = $this->branch_id;
        $table = 'tbl_financial_year f JOIN branch a ON f.branch_id=a.branch_id';
        $primaryKey = 'year_id';
        $columns = array(
                    array( 'db' => 'year_id', 'dt' => 'year_id' ),
                    array( 'db' => 'a.firm_id', 'dt' => 'firm_id' ),
                    array( 'db' => 'f.branch_id', 'dt' => 'branch_id' ),
                    /*array( 'db' => 'company_name', 'dt' => 'company_name' ),
                    array( 'db' => 'branch_name','dt' => 'acc_name','formatter' => function($d,$row){
                        return ucfirst($d);
                    }),*/
                    array( 'db' => 'is_current','dt' => 'is_current'),
                    array( 'db' => 'year_status','dt' => 'year_status'),
                    array( 'db' => 'from_date','dt' => 'from_date'),
                    array( 'db' => 'to_date','dt' => 'to_date'),
                    array( 'db' => 'year_id','dt' => 'action'),
                    array( 'db' => 'year_status','dt' => 'status','formatter' => function($d,$row){
                        $checked = '';
                        
                        if($d == '1') $checked = 'checked';
                            return "<label class='switch'>
                                    <input type='checkbox' {$checked} class='checkbox' name='acc_status' data-id='{$row['year_id']}' onClick='return doconfirm_status($(this))'><span class='slider round'></span></label>";
                        
                    }),
                );
        // Database connection details
        $sql_details = $this->config->item('sql_details');

        $extraWhere = " delete_status='0' AND f.branch_id='".$this->branch_id."'";

        if(null != $this->input->post('filter_acc_sts'))
            $extraWhere .= " AND (year_status='".$this->input->post('filter_acc_sts')."' || is_default='1') ";
            
        $json = $this->ssp->simple( $_POST, $sql_details, $table, $primaryKey, $columns, $extraWhere);
        $temp = array();
        if(!empty($json['data'])){
            foreach ($json['data'] as $key => $d) {
                $k = 'acc_'.$d['branch_id'];
                if(@$temp[$k]){
                    $d['acc_name'] = '';
                    $d['company_name'] = '';
                    $cls = 'disable_in';
                    $checked = '';

                    if($d['is_current'] == '1'){
                        $cls = '';
                        $checked = 'checked';
                    }
                    $from_date = date('m-Y',strtotime($d['from_date']));
                    $to_date = date('m-Y',strtotime($d['to_date']));

                    $d['is_current'] = "<div class='form-check'><label class='form-check-label'>
                                        <input type='radio' ".($d['year_status'] == 0 ? 'disabled' : '')." class='form-check-input current_yr {$cls}' name='current_{$k}' {$checked} data-id='{$k}'>
                                        <i class='input-helper'></i></label></div>";
                    $d['from_date'] = "<input type='hidden' value='{$branch_id}' name='branch_id'><input type='hidden' value='{$d['firm_id']}' name='firm_id'><div id='datepicker-{$d['year_id']}' class='input-group date datepicker'>
                                    <input type='text' year_id='{$d['year_id']}' data-id='{$k}' class='form-control disable_in date_input' name='from_date' placeholder='Invoice From Date' value='{$from_date}'>
                                    <span class='input-group-addon input-group-append hide'> <span class='fa fa-calendar input-group-text'></span></span></div>";
                    /*$d['to_date'] = "<div id='datepicker-{$d['year_id']}' class='input-group date datepicker'>
                                    <input type='text' data-id='{$k}' class='form-control disable_in date_input' name='to_date' placeholder='Invoice To Date' value='{$to_date}'>
                                    <span class='input-group-addon input-group-append'> <span class='mdi mdi-calendar input-group-text'></span></span></div>";*/
                    $d['to_date'] = "<input type='text' class='form-control disable_in' name='to_date' placeholder='Invoice To Date' value='{$to_date}'></div>";
                    $d['action'] = "";
                }else{
                    $d['is_current'] = '';
                    $d['from_date'] = '';
                    $d['to_date'] = '';
                    $active_edit = '';
                    $active_delete = '';
                    $d['action'] = '';
                    if (in_array($financial_year_module_id , $data['active_edit']))
                    {
                        $d['action'] = "<a href='javascript:void(0);' class='edit_fin_year' data-id='{$k}' year_id='{$d['year_id']}'><i class='fa fa-pencil'></i></a> | <a href='javascript:void(0);' class='submit_acc_detail' data-id='{$k}' year_id='{$d['year_id']}'><i class='fa fa-floppy-o'></i></a>";
                    }
                   
                    $d['status'] = '';
                }
                $temp[$k][] = $d;
            }
            $final_ary = array();
           
            foreach ($temp as $key => $acc_years) {

                foreach ($acc_years as $k => $year) {
                    $final_ary[] = $year;
                }
            }

            $json['data']= $final_ary;
        }

        echo json_encode($json);
        exit();
    }

    public function updateFinanceYear(){
        $financial_year_module_id               = $this->config->item('financial_year_module');
        $this->data['financial_year_module_id'] = $financial_year_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $this->data['privilege']         = $privilege;
        $section_modules                 = $this->get_section_modules($financial_year_module_id, $modules, $privilege);
        $update_ary = $this->input->post('update_ary');
        $update_ary = json_decode($update_ary,true);
        $error = array();
        foreach ($update_ary as $key => $yr) {
            $year_from_date = '01-'.$yr['from_date'];
            $year_from_date = date('Y-m-01 H:i:s',strtotime($year_from_date));
            $year_from = date('Y', strtotime($year_from_date));

            $year_to_date = '01-'.$yr['to_date'];
            $last_date = date('t',strtotime($year_to_date));
            $year_to_date = $last_date.'-'.$yr['to_date'];
            $year_to_date = date('Y-m-d H:i:s',strtotime($year_to_date));
            $year_to = date('Y', strtotime($year_to_date));

            $valid = $this->validateYearDate($year_from_date,$year_to_date);
            if($valid['flag']){
                $updat = array(
                            'is_current' => ($yr['is_current'] == '1' ? '1' : '0'),
                            'from_date' => $year_from_date,
                            'to_date' => $year_to_date
                        );
                
                $this->general_model->updateData('tbl_financial_year',$updat,array('year_id' => $yr['year_id']));

                if($updat['is_current'] ==  '1'){
                    $financial_year_title = $year_from.'-'.$year_to;
                    if($year_from == $year_to) $financial_year_title = $year_from;
                    $this->db->set('financial_year_id',$yr['year_id']);
                    $this->db->where('branch_id',$this->session->userdata('SESS_BRANCH_ID'));
                    $this->db->update('branch');

                    $this->session->set_userdata('SESS_FINANCIAL_YEAR_ID', $yr['year_id']);
                    $this->session->set_userdata('SESS_FINANCIAL_YEAR_TITLE', $financial_year_title);
                }       
            }else{
                $error[] =array('year_id'=> $yr['year_id'], 'msg' => $valid['msg']);
            }
        }
        $this->data['flag'] = true; 
        if(!empty($error)){
            $this->data['flag'] = false; 
            $this->data['error'] = $error; 
        }
        $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => 0,
                    'table_name'        => 'tbl_financial_year',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Financial Year Updated' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
        echo json_encode($this->data);
        exit;
    }

    public function validateYearDate($year_from_date,$year_to_date){
        $date1 = $year_from_date;
        $date2 = $year_to_date;

        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
       
        if($diff < 0){
            $this->data['flag'] = false;
            $this->data['msg'] = 'Select valid date!';
        }elseif($diff > 12){
            $this->data['flag'] = false;
            $this->data['msg'] = "Your financial year can't be more than 12 months!";
        }else{
            $this->data['flag'] = true;
        }
        return $this->data;
    }

    public function changeYearStatus(){
        $financial_year_module_id               = $this->config->item('financial_year_module');
        $this->data['financial_year_module_id'] = $financial_year_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $this->data['privilege']         = $privilege;
        $section_modules                 = $this->get_section_modules($financial_year_module_id, $modules, $privilege);
        $updateData = array('year_status'=>$this->input->post('sts'));
        $this->db->where(array('year_id'=>$this->input->post('id')));
        if ($this->db->update('tbl_financial_year', $updateData)) {
            echo json_encode(TRUE);
        } else {
            echo json_encode(FALSE);
        }
    }


    public function createOption_finance($finance_year_id,$from_date,$to_date){

        $user_id = $this->session->userdata('SESS_USER_ID');      
        $branch_id = $this->session->userdata("SESS_BRANCH_ID");
        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type','financial year');
        $this->db->where('branch_id',$branch_id);
        $sup = $this->db->get();
        $result_option = $sup->result();
        

        $option_array = array();
        $i = 1;
      
        $finance_year = date('Y',strtotime($from_date)) .'-'.date('y',strtotime($to_date));
        $date = date('Y-m-d');
        
       
        if($from_date!= '' && $from_date!= '0000-00-00 00:00:00' && $to_date!= '' && $to_date!= '0000-00-00 00:00:00'){
             
        
            foreach ($result_option as $key1 => $value1) { 
                $finance_year_option = $value1->customise_option;
                $parent_id = $value1->id;
                $finance_year_option = str_ireplace('{{X}}',$finance_year, $finance_year_option);
                $option_array[$i]['purpose_option'] = $finance_year_option;
                $option_array[$i]['parent_id'] =  $parent_id;
                $option_array[$i]['payee_id'] = $finance_year_id;
                $option_array[$i]['branch_id'] = $branch_id;
                $option_array[$i]['added_user_id'] = $user_id;
                $option_array[$i]['added_date'] = $date;

                $i = $i + 1;
            }  
        }    
        
     
        if(!empty($option_array)){
            $table = "tbl_transaction_purpose_option";
            $this->db->insert_batch($table, $option_array);
        } 
    }
}
?>