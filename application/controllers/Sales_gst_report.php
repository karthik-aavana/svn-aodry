<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_gst_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    function expense_tds_report() {
        $this->load->view("gst_reports/expense_tds_report");
    }

    function purchase_expense_tcs_report() {
        $this->load->view("gst_reports/purchase_expense_tcs_report");
    }

    function sales_tcs_report() {
        $this->load->view("gst_reports/sales_tcs_report");
    }

    function sales_tds_report() {
        $this->load->view("gst_reports/sales_tds_report");
    }

}
