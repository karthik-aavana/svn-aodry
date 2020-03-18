<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LedgersUpdate extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model(['general_model','Ion_auth_model','groupledger_model']);
        $this->load->helper('image_upload_helper');
        $this->load->library('SSP');
	}

	function index(){

		$string = "firm.*,branch.*";
		$from = "branch";
		$join = ["firm" => "firm.firm_id = branch.firm_id"];
		$where = ["firm.delete_status" =>0];

		$data['branch_list'] = $this->general_model->getJoinRecords($string,$from,$where,$join);
        $data['main_list'] = $this->groupledger_model->getAllDefaultGroupLedgers();
       
		// print_r($data['firm_list']);
		$this->load->view("super_admin/ledgers/list",$data);
	}

	function GetLedgersDefault(){
		$table = 'branch_default_ledgers';// l LEFT JOIN default_ledgers_cases d ON d.ledger_id= l.default_ledger_id
		$primaryKey = 'ledger_id';
		$columns = array(
            array('db' => 'ledger_id', 'dt' => 'ledger_id'),
            array('db' => 'branch_id', 'dt' => 'branch_id'),
            array('db' => 'default_ledger_id', 'dt' => 'default_ledger_id'),
            array('db' => 'main_group', 'dt' => 'main_group'),
            array('db' => 'sub_group_1', 'dt' => 'sub_group_1'),
            array('db' => 'sub_group_2', 'dt' => 'sub_group_2'),
            array('db' => 'ledger_name', 'dt' => 'ledger_name'),
            array('db' => 'gst_payable', 'dt' => 'gst_payable'),
            array('db' => 'place_of_supply', 'dt' => 'place_of_supply'),
            array('db' => 'module', 'dt' => 'module'),
            array('db' => 'ledger_id', 'dt' => 'action', 'formatter' => function($d, $row) {

                return "<input type='hidden' value='{$row['branch_id']}' name='branch_id'><a href='javascript:void(0);' class='edit_grp' data-id='{$row['ledger_id']}' dlId='{$row['default_ledger_id']}'><i class='fa fa-pencil'></i></a><input type='hidden' name='ledger_name' value='{$row['ledger_name']}'><input type='hidden' name='gst_payable' value='{$row['gst_payable']}'><input type='hidden' name='place_of_supply' value='{$row['place_of_supply']}'><input type='hidden' name='module' value='{$row['module']}'> | <a href='javascript:void(0);' class='update_grp' data-id='{$row['ledger_id']}'><i class='fa fa-floppy-o'></i></a>";
            }),
        ); 
        // Database connection details
        $sql_details = $this->config->item('sql_details');
        $branch_id = $this->encryption_url->decode($this->input->post('branch_id'));
        $extraWhere = " branch_id='" .$branch_id. "' ";

        if (null != $this->input->post('module_name')){
            $extraWhere .= " AND module like '%" . $this->input->post('module_name') . "%'";
        }

        if (null != $this->input->post('gst_payable')){
            $extraWhere .= " AND (gst_payable='" . $this->input->post('gst_payable') . "' OR gst_payable='0') ";
        }

        if (null != $this->input->post('place_of_supply')){
            $extraWhere .= " AND (place_of_supply='" . $this->input->post('place_of_supply') . "' OR place_of_supply='0' ) ";
        }
        
        $json = $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $extraWhere);

        echo json_encode($json);
        exit();
	}

    function UpdateDefaultLedger(){
        $branch_id = $this->input->post('branch_id');
        $ledger_name = trim(stripslashes($this->input->post('ledger_name')));
        $ledger_id = $this->input->post('ledger_id');
        $default_ledger_id = $this->input->post('default_ledger_id');

        $r = $this->db->query("SELECT * FROM tbl_ledgers WHERE default_ledger_id='{$ledger_id}' AND branch_id='{$branch_id}' ");
        $already_led = $r->result();

        if(!empty($already_led)){
            foreach ($already_led as $key => $value) {
                $tbl_led_name = str_ireplace('{{X}}', $value->default_value, $ledger_name);
                $tbl_led_name = str_ireplace('{{ITEM_NAME}}', $value->default_value , $tbl_led_name);
                $this->db->set('ledger_name',trim($tbl_led_name));
                $this->db->where('ledger_id',$value->ledger_id);
                $this->db->where('branch_id',$branch_id);
                $this->db->update('tbl_ledgers');
            }
        }

        $this->db->set('ledger_name',$ledger_name);
        $this->db->where('ledger_id',$ledger_id);
        $this->db->where('branch_id',$branch_id);
        $default = $this->db->update('branch_default_ledgers');
        $json = array();
        $json['status'] = true;
        if(!$default){
            $json['error'] = 'Something wrong!';
            $json['status'] = false;
        }
        echo json_encode($json);
    }
}
?>