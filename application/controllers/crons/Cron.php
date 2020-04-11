<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller {

	function __construct(){
		parent::__construct();
        /*$this->load->model('general_model');
        $this->modules = $this->get_modules();
        $this->load->helper('image_upload_helper');*/
	}

	public function expriredPlan(){
		$current_date = date('Y-m-d H:i:s');
		$qry = $this->db->query("UPDATE tbl_billing_info SET package_status='0',updated_date='{$current_date}' WHERE package_status='1' AND end_date < '{$current_date}' ");
		/*$result = $qry->result_array();
		foreach ($result as $key => $value) {
			
		}
		echo "<pre>";
		print_r($result);*/
	}
}
?>