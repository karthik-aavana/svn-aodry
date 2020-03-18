<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class GroupLedgers extends MY_Controller {

    public $data = array();
    public $branch_id = 0;

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'product_model',
            'service_model',
            'groupledger_model']);
        $this->modules = $this->get_modules();
        $this->load->library('SSP');
        $this->branch_id = $this->session->userdata('SESS_BRANCH_ID');
    }

    function index() {
        /* $firm_id = 1;
          $this->db->select('*');
          $qry = $this->db->get('tbl_default_group');
          $default_ry = $qry->result_array();
          $insert_dt = $insert_sub = $is_added = array();

          foreach ($default_ry as $key => $value) {
          $k = $value['main_group_id'].'_'.$value['primary_sub_group'];
          if(!array_key_exists($k , $is_added)){
          $insert_dt = array(
          'branch_id' => $this->branch_id,
          'sub_group_name_1' => $value['primary_sub_group'],
          'sub_group_name_2' => $value['sec_sub_group'],
          'main_grp_id' => $value['main_group_id'],
          'group_status' => '1',
          'is_editable' => '0',
          'default_group_id' => $value['group_id'],
          'created_ts' => date('Y-m-d H:i:s'),
          'created_by' =>$this->branch_id,
          );
          $this->db->insert('tbl_sub_group',$insert_dt);
          $ins_id = $this->db->insert_id();
          if($value['main_group_id'] == '1'){
          $insert_sub = array(
          'firm_id' => $firm_id,
          'branch_id' => $this->branch_id ,
          'ledger_name' => 'Drawings',
          'sub_group_id' => $ins_id,
          'created_ts' => date('Y-m-d H:i:s'),
          'created_by' => $this->branch_id,
          );
          }else{

          $insert_sub = array(
          'firm_id' => $firm_id,
          'branch_id' => $this->branch_id ,
          'sub_group_id' => $ins_id,
          'created_ts' => date('Y-m-d H:i:s'),
          'created_by' =>$this->branch_id,
          );
          }
          $this->db->insert('tbl_ledgers',$insert_sub);
          }
          } */
        $expense_module_id = $this->config->item('general_voucher_module');
        $this->data['general_module_id'] = $expense_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $this->data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($expense_module_id, $modules, $privilege);
        /*$this->data['tds_section'] = $this->tds_section_call();*/
        $this->data['main_list'] = $this->groupledger_model->getAllMainGroup();
        /* presents all the needed */
        $this->data = array_merge($this->data, $section_modules);

        $this->load->view('group_ledger/group_ledger', $this->data);
    }

    public function GetCustomLedger() {

        $this->data['report_list'] = $this->groupledger_model->getAllReport();
        $this->data['main_list'] = $this->groupledger_model->getAllMainGroup();

        $table = 'tbl_ledgers l LEFT JOIN tbl_sub_group s ON l.sub_group_id= s.sub_grp_id LEFT JOIN tbl_main_group m ON s.main_grp_id=m.main_grp_id LEFT JOIN tbl_group_report r ON m.report_id=r.report_id';
        $primaryKey = 'sub_grp_id';
        $columns = array(
            array('db' => 'ledger_id', 'dt' => 'ledger_id'),
            array('db' => 's.main_grp_id', 'dt' => 'main_grp_id'),
            array('db' => 's.sub_grp_id', 'dt' => 'sub_grp_id'),
            array('db' => 'l.branch_id', 'dt' => 'branch_id'),
            array('db' => 'l.firm_id', 'dt' => 'firm_id'),
            array('db' => 's.default_group_id', 'dt' => 'default_group_id'),
            array('db' => 'is_editable', 'dt' => 'is_editable'),
            array('db' => 'grp_name', 'dt' => 'main_group_name', 'formatter' => function($d, $row) {
                    $main_grp = '';
                    if ($row['is_editable'] == '1') {
                        $main_grp = "<select class='form-control select2 disable_in' name='main_grp_id' data-id='{$row['ledger_id']}'>";
                        foreach ($this->data['main_list'] as $key => $list) {
                            $main_grp .= "<option value='{$list['main_grp_id']}' " . ($list['main_grp_id'] == $row['main_grp_id'] ? 'selected' : '' ) . ">{$list['grp_name']}</option>";
                        }
                        $main_grp .= "</select>";
                    } else {
                        $main_grp = "<select class='form-control select2 disable_in'>";
                        foreach ($this->data['main_list'] as $key => $list) {
                            if ($list['main_grp_id'] == $row['main_grp_id']) {
                                $main_grp .= "<option value='{$list['main_grp_id']}' " . ($list['main_grp_id'] == $row['main_grp_id'] ? 'selected' : '' ) . ">{$list['grp_name']}</option>";
                            }
                        }
                        $main_grp .= "</select>";
                        /* foreach ($this->data['main_list'] as $key => $list) {
                          if($list['main_grp_id'] == $row['main_grp_id']){
                          $main_grp = $list['grp_name'];
                          }
                          } */
                    }
                    return $main_grp;
                }),
            array('db' => 'sub_group_name_1', 'dt' => 'sub_group_1', 'formatter' => function($d, $row) {

                    if ($row['is_editable'] == '1') {
                        $d = "<input type='text' class='form-control disable_in' data-id='{$row['ledger_id']}' name='sub_group_name_1' value='{$d}' sid='{$row['default_group_id']}'>";
                    } else {
                        $d = "<input type='text' class='form-control disable_in' value='{$d}'>";
                    }
                    return $d;
                }),
            array('db' => 'sub_group_name_2', 'dt' => 'sub_group_2', 'formatter' => function($d, $row) {
                    if ($row['is_editable'] == '1') {
                        $d = "<input type='text' class='form-control disable_in' data-id='{$row['ledger_id']}' name='sub_group_name_2' value='{$d}' sid='{$row['default_group_id']}'>";
                    } else {

                        $d = "<input type='text' class='form-control disable_in' value='{$d}'>";
                    }
                    return $d;
                }),
            array('db' => 'ledger_name', 'dt' => 'ledger', 'formatter' => function($d, $row) {
                    return "<input type='text' class='form-control disable_in' data-id='{$row['ledger_id']}' name='ledger' value='{$d}'>";
                }),
            array('db' => 'm.report_id', 'dt' => 'report_id'),
            array('db' => 'report_name', 'dt' => 'report_name', 'formatter' => function($d, $row) {

                    $report_name = '';
                    foreach ($this->data['report_list'] as $key => $rep) {
                        if ($rep['report_id'] == $row['report_id']) {
                            $report_name = $rep['report_name'];
                        }
                    }
                    return $report_name;
                }),
            array('db' => 'group_status', 'dt' => 'status', 'formatter' => function($d, $row) {
                    $checked = $event = '';

                    $event = 'disabled';
                    if ($d == '1')
                        $checked = 'checked';
                    if ($row['is_editable'] == '1')
                        $event = "onClick='return blockGroup($(this));'";
                    $lbl = "<label class='switch'>
                                <input type='checkbox' class='checkbox' {$checked} {$event} data-id='{$row['sub_grp_id']}' name='group_status'>
                                <span class='slider round'></span> </label>";
                    return $lbl;
                }),
            array('db' => 'ledger_id', 'dt' => 'action', 'formatter' => function($d, $row) {

                    return "<input type='hidden' value='{$row['branch_id']}' name='branch_id'><input type='hidden' value='{$row['firm_id']}' name='firm_id'><a href='javascript:void(0);' class='edit_grp' data-id='{$row['ledger_id']}'><i class='fa fa-pencil'></i></a> | <a href='javascript:void(0);' class='update_grp' data-id='{$row['ledger_id']}' is_edit='{$row['is_editable']}'><i class='fa fa-floppy-o'></i></a>";
                }),
        );
        // Database connection details
        $sql_details = $this->config->item('sql_details');
        $extraWhere = " l.branch_id='" . $this->branch_id . "' ";

        if (null != $this->input->post('filter_main_group'))
            $extraWhere .= " AND s.main_grp_id='" . $this->input->post('filter_main_group') . "'";

        if (null != $this->input->post('filter_sub_group_1'))
            $extraWhere .= " AND s.sub_group_name_1='" . $this->input->post('filter_sub_group_1') . "'";

        if (null != $this->input->post('filter_sub_group_2'))
            $extraWhere .= " AND s.sub_group_name_2='" . $this->input->post('filter_sub_group_2') . "'";

        if (null != $this->input->post('filter_report'))
            $extraWhere .= " AND m.report_id='" . $this->input->post('filter_report') . "'";

        if (null != $this->input->post('filter_group_status'))
            $extraWhere .= " AND s.group_status='" . $this->input->post('filter_group_status') . "'";

        if (null != $this->input->post('filter_ledger'))
            $extraWhere .= " AND ledger_name='" . $this->input->post('filter_ledger') . "'";

        $json = $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $extraWhere);
        echo json_encode($json);
        exit();
    }

    public function updateLedgerInfo() {
        $ledger_id = $this->input->post('ledger_id');
        $primary_sub_group = $this->input->post('primary_sub_group');
        $sec_sub_group = $this->input->post('sec_sub_group');
        $main_group_id = $this->input->post('main_grp_id');
        $branch_id = $this->input->post('branch_id');
        $firm_id = $this->input->post('firm_id');
        $ledger = $this->input->post('ledger');
        $is_edit = $this->input->post('is_edit');

        $grp_id = $this->groupledger_model->getGroupId($ledger_id);
        $update_ary = array(
            'main_grp_id' => $main_group_id,
            'sub_group_name_1' => $primary_sub_group,
            'sub_group_name_2' => $sec_sub_group,
            'is_editable' => $is_edit,
            'branch_id' => $branch_id,
        );

        $update_ledger = array(
            'ledger_name' => $ledger,
            'firm_id' => $firm_id,
            'branch_id' => $branch_id,
            'updated_ts' => date('Y-m-d H:i:s'),
            'updated_by' => $this->branch_id,
        );
        $is_valid = $this->groupledger_model->validateLedger($update_ary, $update_ledger, $ledger_id, $grp_id);

        if ($is_valid['flag']) {
            $this->groupledger_model->updateLedgerInfo($ledger, $ledger_id, $update_ledger);
            if ($is_edit == '1') {
                $this->db->set($update_ary);
                $this->db->where('sub_grp_id', $grp_id);
                $this->db->update('tbl_sub_group');
            }
            $this->data['flag'] = true;
            $this->data['msg'] = 'Updated Successfully';
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_sub_group',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Ledger Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        } else {
            $this->data['flag'] = false;
            $this->data['msg'] = 'Updated Unsuccessful!';
            $this->data = $is_valid;
        }

        /* Update all sub group 2 name */
        echo json_encode($this->data);
    }

    public function addNewLedgerGroup() {
        $ledger = $this->input->post('ledger');
        $main_grp_id = $this->input->post('main_grp_id');
        $primary_sub_group = $this->input->post('primary_sub_group');
        $sec_sub_group = $this->input->post('sec_sub_group');
        $ledger = $this->input->post('ledger');

        $ledger_data = array('ledger_name' => $ledger,
            'branch_id' => $this->branch_id,
            'created_ts' => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('SESS_USER_ID')
        );
        $insert_data = array(
            'main_grp_id' => $main_grp_id,
            'sub_group_name_1' => $primary_sub_group,
            'sub_group_name_2' => $sec_sub_group,
            'group_status' => '1',
            'branch_id' => $this->branch_id,
            'is_editable' => '1',
            'created_ts' => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('SESS_USER_ID')
        );
        $is_valid = $this->groupledger_model->validateLedger($insert_data, $ledger_data);

        if ($is_valid['flag']) {
            $this->groupledger_model->addNewLedgergroup($insert_data, $ledger_data);
            $this->data['flag'] = true;
            $this->data['msg'] = 'Added successfully!';
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_sub_group',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Ledger Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        } else {
            $this->data = $is_valid;
        }
        echo json_encode($this->data);
        exit;
    }

    public function getPrimaryGroups() {
        $main_grp_id = $this->input->post('main_grp_id');
        $pri_grps = $this->groupledger_model->getPrimaryGroups($main_grp_id);
        $this->data['flag'] = false;
        if (!empty($pri_grps)) {
            $this->data['flag'] = true;
            $this->data['data'] = $pri_grps;
        }
        echo json_encode($this->data);
    }

    public function getSecondaryGroupsLedger() {
        $main_grp_id = $this->input->post('main_grp_id');
        $primary_sub_group = $this->input->post('primary_sub_group');
        $branch_id = $this->branch_id;
        $pri_grps = $this->groupledger_model->getSecondaryLedgerGroups($branch_id, $main_grp_id, $primary_sub_group);
        $this->data['flag'] = false;

        if (!empty($pri_grps)) {
            $this->data['flag'] = true;
            $this->data['data'] = $pri_grps;
        }
        echo json_encode($this->data);
    }

    public function updateLedgerStatus() {
        $sub_grp_id = $this->input->post('sub_grp_id');
        $status = $this->input->post('status');
        $update = array('group_status' => $status);
        $this->db->set($update);
        $this->db->where('sub_grp_id', $sub_grp_id);
        $this->db->update('tbl_sub_group');
        $this->data['msg'] = 'status updated successfully!';
        $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'tbl_sub_group',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Ledger Status Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        echo json_encode($this->data);
    }

}

?>