<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Closing_balance extends MY_Controller {

    public $data = array();
    public $branch_id = 0;

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'groupledger_model']);
        $this->modules = $this->get_modules();
        $this->load->library('SSP');
        $this->branch_id = $this->session->userdata('SESS_BRANCH_ID');
    }

    function index() {
        $general_module_id = $this->config->item('general_voucher_module');
        $this->data['general_module_id'] = $general_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $this->data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($general_module_id, $modules, $privilege);
        $this->data['main_list'] = $this->groupledger_model->getAllMainGroup();
        $this->db->select('balance_upto_date');
        $this->db->where('branch_id',$this->branch_id);
        $qry = $this->db->get('tbl_default_balance_date');
        $close_date = '';
        if($qry->num_rows() > 0){
            $r = $qry->result_array();
            $close_date = date('d-m-Y',strtotime($r[0]['balance_upto_date']));
        }
        $this->data['close_date']= $close_date;

        $this->data = array_merge($this->data, $section_modules);

        $this->load->view('group_ledger/closing_balance', $this->data);
    }

    public function updateDefaultBalance(){
        $id = $this->input->post('id');
        $ledger_id = $this->input->post('ledger_id');
        if($id > 0){
            $update = array('amount' => ('' != $this->input->post('amount') ? $this->input->post('amount') : 0), 'amount_type' => $this->input->post('amount_type'),'updated_by' => $this->session->userdata('SESS_USER_ID'),'updated_ts' => date('Y-m-d H:i:s'));
            $this->db->set($update);
            $this->db->where('id',$id);
            $this->db->update('tbl_default_opening_balance');
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_default_opening_balance',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Default Opening Balance Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }else{
            $insert  = array('branch_id' => $this->branch_id,'ledger_id' => $ledger_id,'amount' => ('' != $this->input->post('amount') ? $this->input->post('amount') : 0), 'amount_type' => $this->input->post('amount_type'),'created_by' => $this->session->userdata('SESS_USER_ID'),'created_ts' => date('Y-m-d H:i:s'));
            $this->db->insert('tbl_default_opening_balance',$insert);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_default_opening_balance',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Default Opening Balance Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
        return true;
    }

    public function getCompanyUptoDate(){
        $date = '';
        $this->db->select('balance_upto_date');
        $this->db->where('branch_id',$this->branch_id);
        $qry = $this->db->get('tbl_default_balance_date');
        if($qry->num_rows() > 0){
            $r = $qry->result_array();
            $date = date('d-m-Y',strtotime($r[0]['balance_upto_date']));
        }
        echo $date;
        exit;
    }

    public function updateDefaultDate(){
        $this->data['flag'] = true;
        $this->data['msg'] = 'updated successfully!';
        $date = date('Y-m-d',strtotime($this->input->post('date')));
        /*$firm_id = $this->input->post('firm_id');
        $acc_id = $this->input->post('acc_id');*/
        $is_add = true;
        $tbl = '';
        $voucher_table = array('advance_voucher','bank_voucher','cash_voucher','contra_voucher','expense_voucher','general_voucher','payment_voucher','purchase_voucher','receipt_voucher','refund_voucher','sales_voucher');

        foreach ($voucher_table as $key => $table) {
            $this->db->select('voucher_date');
            $this->db->where('branch_id',$this->branch_id);
            $this->db->where('voucher_date <=',$date);
            $query = $this->db->get($table);
            
            if($query->num_rows() > 0){
                $tbl = $table;
                $is_add = false;
                break;
            }
        }

        if($is_add){
            $this->db->select('balance_upto_date');
            $this->db->where('branch_id',$this->branch_id);
            $qry = $this->db->get('tbl_default_balance_date');
            if($qry->num_rows() > 0){
                $update = array('balance_upto_date' => $date,
                                'updated_by' => $this->session->userdata('SESS_USER_ID'),
                                'updated_ts' => date('Y-m-d H:i:s')
                            );
                
                $this->db->set($update);
                $this->db->where('branch_id',$this->branch_id);
                $id1 = $this->db->update('tbl_default_balance_date');
                $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_default_balance_date',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Default Balance Date Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
            }else{
                $insert = array('balance_upto_date' => $date,
                                'branch_id' => $this->branch_id,
                                'created_by' => $this->session->userdata('SESS_USER_ID'),
                                'created_ts' => date('Y-m-d H:i:s')
                            );
                $this->db->insert('tbl_default_balance_date',$insert);
                $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_default_balance_date',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Default Balance Date Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
            }
        }else{
            $this->data['flag'] = false;
            $this->data['msg'] = 'We have records up to this date in '.str_replace('_', ' ', $tbl);
        }
        echo json_encode($this->data);
        exit;
    }

    public function getDefaultOpeningBalance(){
        $table = 'tbl_ledgers l LEFT JOIN tbl_default_opening_balance d ON d.ledger_id= l.ledger_id';
        $primaryKey = 'id';

        $columns = array(
                    array( 'db' => 'id', 'dt' => 'id' ),
                    array( 'db' => 'l.ledger_id', 'dt' => 'ledger_id' ),
                    array( 'db' => 'l.ledger_name', 'dt' => 'ledger_name' ),
                    array( 'db' => 'l.branch_id', 'dt' => 'branch_id' ),
                    array( 'db' => 'amount', 'dt' => 'amount' ,'formatter' => function($d,$row){
                        if($row['id'] == null) $row['id'] = 0;
                        return "<input type='number' name='ledger_amount' data-id='{$row['id']}' ledger_id='{$row['ledger_id']}' value='{$d}' class='form-control disable_in'>";
                    }),
                    array( 'db' => 'amount_type', 'dt' => 'amount_type' ,'formatter' => function($d,$row){
                        if($row['id'] == null) $row['id'] = 0;

                        $Cr_html = '<select class="form-control js-example-basic-single disable_in" name="amount_type" data-id="'.$row['id'].'" ledger_id="'.$row['ledger_id'].'">
                                <option value="CR" '.($row['amount_type'] == 'CR' ? 'selected' : '').'>CR</option>
                                <option value="DR" '.($row['amount_type'] == 'DR' ? 'selected' : '').'>DR</option>
                            </select>';
                        return $Cr_html;
                    }),
                    array( 'db' => 'id', 'dt' => 'action' ,'formatter' => function($d,$row){
                        /*if($this->access['m_update']){*/
                            if($d == null) $d = 0;
                            return "<input type='hidden' value='{$row['branch_id']}' name='branch_id'><a href='javascript:void(0);' data-id='{$d}' ledger_id='{$row['ledger_id']}' class='edit_ledger'><i class='fa fa-pencil'></i></a> | <a href='javascript:void(0);' class='sub_ledger' ledger_id='{$row['ledger_id']}' data-id='{$d}'><i class='fa fa-floppy-o'></i></a>";
                        /*}else{
                            return '-';
                        }*/
                    }),
                    array( 'db' => 'status', 'dt' => 'status' ,'formatter' => function($d,$row){
                        $checked = '';
                        if($row['id'] == null) $row['id'] = 0;

                        if($d == '1') $checked = 'checked';
                        /*if($this->access['m_delete']){*/
                            $lbl="<label class='switch'>
                                <input type='checkbox' class='checkbox' {$checked} onClick='return blockLedger($(this));' data-id='{$row['id']}' ledger_id='{$row['ledger_id']}' name='status'>
                                <span class='slider round'></span> </label>";
                        /*}else{
                            $lbl = '-';
                        }*/
                        return $lbl;
                    }),
                );
        // Database connection details
        $sql_details = $this->config->item('sql_details');

        $extraWhere = "";
        
        $extraWhere .= " l.branch_id='".$this->branch_id."' AND ledger_name != '' ";
       
        $json = $this->ssp->simple( $_POST, $sql_details, $table, $primaryKey, $columns, $extraWhere);
        echo json_encode($json);
        exit();
    }

    public function updateDefaultStatus(){
        $id = $this->input->post('id');
        $ledger_id = $this->input->post('ledger_id');
        if($id > 0){
            $this->db->set('status',$this->input->post('status'));
            $this->db->where('id',$id);
            $this->db->update('tbl_default_opening_balance');
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_default_opening_balance',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Default Status Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }else{
            $insert  = array('branch_id' => $this->branch_id,'ledger_id' => $ledger_id,'amount' => 0, 'amount_type' => 'DR','created_by' => $this->session->userdata('SESS_USER_ID'),'created_ts' => date('Y-m-d H:i:s'),'status' => $this->input->post('status'));
            $this->db->insert('tbl_default_opening_balance',$insert);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_default_opening_balance',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Default Status Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
        $this->data['flag'] = true;
        $this->data['msg'] = 'updated successfully!';
        echo json_encode($this->data);
    }
}
?>