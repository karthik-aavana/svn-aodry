<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DefaultLedger_script extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->model('general_model');
        $this->load->model('ledger_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
    	$this->db->select('branch_id');
    	$this->db->from('branch');
    	$res = $this->db->get();
    	$result = $res->result();
    	foreach ($result as $key => $value) {
    		
    			$this->db->query("INSERT INTO branch_default_ledgers (default_ledger_id, main_group ,sub_group_1,sub_group_2,ledger_name,gst_payable,place_of_supply,module,branch_id) Select ledger_id, main_group ,sub_group_1,sub_group_2,ledger_name,gst_payable,place_of_supply,module,".$value->branch_id." from default_ledgers_cases ");
    		
    	}
    }
}