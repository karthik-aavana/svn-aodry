<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class Report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('report_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
        $report_module_id = $this->config->item('report_module');
        $data['module_id'] = $report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($report_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $this->load->view('report/list', $data);
    }

    public function sales_pdf() {
        $this->load->view('reports/sales_pdf');
    }

    public function report_download() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $spreadsheet = new Spreadsheet();
        $Excel_writer = new Xls($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();
        if ($this->input->post('submit') == "Advance Receipts XL") {
            $string = "a.party_id,a.voucher_date,a.financial_year_id,a.advance_id,a.voucher_sub_total,a.receipt_amount as gross_adv,s.state_name,s.state_code,ai.item_tax_percentage";
            $from = "advance_voucher a";
            $join = [
                'advance_voucher_item ai' => "ai.advance_id=a.advance_id",
                'states s' => "s.state_id=a.billing_state_id"];
            $where = array(
                'a.refund_status' => 0,
                'a.delete_status' => 0,
                'a.voucher_date >=' => $from_date,
                'a.voucher_date <=' => $to_date,
                'a.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'a.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'));
            $order = [
                'a.advance_id' => "desc"];
            $group_by = [
                'a.billing_state_id',
                'ai.item_tax_percentage'];
            $rec_data = $this->report_model->getJoinRecords($string, $from, $where, $join, $order, $group_by);
            foreach (range('A', 'E') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'Place Of Supply');
            $activeSheet->setCellValue("B1", 'Rate');
            $activeSheet->setCellValue("C1", 'Applicable % of Tax Rate');
            $activeSheet->setCellValue("D1", 'Gross Advance Received');
            $activeSheet->setCellValue("E1", 'Cess Amount');
            $x = 2;
            for ($i = 0; $i < count($rec_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $rec_data[$i]->state_code . '- ' . $rec_data[$i]->state_name);
                $activeSheet->setCellValue('B' . $x, $rec_data[$i]->item_tax_percentage);
                $activeSheet->setCellValue('C' . $x, '');
                $activeSheet->setCellValue('D' . $x, $rec_data[$i]->gross_adv);
                $activeSheet->setCellValue('E' . $x, '');
                $x++;
            }
        } elseif ($this->input->post('submit') == "B2B Receipts XL") {
            $string = "s.sales_party_id,s.sales_date as inv_date,s.sales_grand_total,s.sales_gst_payable as reverse,s.sales_type_of_supply as invoice_type,s.sales_invoice_number as invoice_no,s.financial_year_id,s.sales_id,s.sales_sub_total as total,si.sales_item_tax_percentage as tax_val,c.customer_gstin_number,c.customer_name as receiver_name,s.sales_grand_total as gross_adv,s.sales_taxable_value as taxable_val,st.state_name,st.state_code,c.customer_gst_registration_type,s.sales_billing_state_id";
            $from = "sales_item si";
            $join = [
                'sales s' => "si.sales_id = s.sales_id",
                'states st' => "st.state_id=s.sales_billing_state_id",
                'customer c' => "c.customer_id=s.sales_party_id"];
            $where = array(
                's.delete_status' => 0,
                'si.delete_status' => 0,
                's.sales_date >=' => $from_date,
                's.sales_date <=' => $to_date,
                's.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                's.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Registered');
            $order = [
                's.sales_id' => "desc"];
            $group_by = [
                's.sales_billing_state_id',
                'si.sales_item_tax_percentage'];
            $b2b_data = $this->report_model->getJoinRecords($string, $from, $where, $join, $order, $group_by);
            foreach (range('A', 'M') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'GSTIN/UIN of Recipient');
            $activeSheet->setCellValue("B1", 'Receiver Name');
            $activeSheet->setCellValue("C1", 'Invoice Number');
            $activeSheet->setCellValue("D1", 'Invoice date');
            $activeSheet->setCellValue("E1", 'Invoice Value');
            $activeSheet->setCellValue("F1", 'Place Of Supply');
            $activeSheet->setCellValue("G1", 'Reverse Charge');
            $activeSheet->setCellValue("H1", 'Invoice Type');
            $activeSheet->setCellValue("I1", 'E-Commerce GSTIN');
            $activeSheet->setCellValue("J1", 'Rate');
            $activeSheet->setCellValue("K1", 'Applicable % of Tax Rate');
            $activeSheet->setCellValue("L1", 'Taxable Value');
            $activeSheet->setCellValue("M1", 'Cess Amount');
            $x = 2;
            for ($i = 0; $i < count($b2b_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $b2b_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $b2b_data[$i]->receiver_name);
                $activeSheet->setCellValue('C' . $x, $b2b_data[$i]->invoice_no);
                $activeSheet->setCellValue('D' . $x, $b2b_data[$i]->inv_date);
                $activeSheet->setCellValue('E' . $x, $b2b_data[$i]->sales_grand_total);
                $activeSheet->setCellValue('F' . $x, $b2b_data[$i]->state_code . '- ' . $b2b_data[$i]->state_name);
                $activeSheet->setCellValue('G' . $x, $b2b_data[$i]->reverse);
                $activeSheet->setCellValue('H' . $x, $b2b_data[$i]->invoice_type);
                $activeSheet->setCellValue('I' . $x, '');
                $activeSheet->setCellValue('J' . $x, $b2b_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $b2b_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $x++;
            }
        } elseif ($this->input->post('submit') == "B2CL Receipts XL") {
            $string = "s.sales_party_id,s.sales_date as inv_date,s.sales_grand_total,s.sales_gst_payable as reverse,s.sales_type_of_supply as invoice_type,s.sales_invoice_number as invoice_no,s.financial_year_id,s.sales_id,s.sales_sub_total as total,si.sales_item_tax_percentage as tax_val,c.customer_gstin_number,c.customer_name as receiver_name,s.sales_grand_total as gross_adv,s.sales_taxable_value as taxable_val,st.state_name,st.state_code,c.customer_gst_registration_type,s.sales_billing_state_id";
            $from = "sales_item si";
            $join = [
                'sales s' => "si.sales_id = s.sales_id",
                'states st' => "st.state_id=s.sales_billing_state_id",
                'customer c' => "c.customer_id=s.sales_party_id"];
            $where = array(
                's.delete_status' => 0,
                'si.delete_status' => 0,
                's.sales_grand_total >' => '250000.00',
                's.sales_date >=' => $from_date,
                's.sales_date <=' => $to_date,
                's.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                's.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Unregistered');
            $order = [
                's.sales_id' => "desc"];
            $group_by = [
                's.sales_billing_state_id',
                'si.sales_item_tax_percentage'];
            $b2cl_data = $this->report_model->getJoinRecords($string, $from, $where, $join, $order, $group_by);
            foreach (range('A', 'M') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'GSTIN/UIN of Recipient');
            $activeSheet->setCellValue("B1", 'Receiver Name');
            $activeSheet->setCellValue("C1", 'Invoice Number');
            $activeSheet->setCellValue("D1", 'Invoice date');
            $activeSheet->setCellValue("E1", 'Invoice Value');
            $activeSheet->setCellValue("F1", 'Place Of Supply');
            $activeSheet->setCellValue("G1", 'Reverse Charge');
            $activeSheet->setCellValue("H1", 'Invoice Type');
            $activeSheet->setCellValue("I1", 'E-Commerce GSTIN');
            $activeSheet->setCellValue("J1", 'Rate');
            $activeSheet->setCellValue("K1", 'Applicable % of Tax Rate');
            $activeSheet->setCellValue("L1", 'Taxable Value');
            $activeSheet->setCellValue("M1", 'Cess Amount');
            $x = 2;
            for ($i = 0; $i < count($b2cl_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $b2cl_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $b2cl_data[$i]->receiver_name);
                $activeSheet->setCellValue('C' . $x, $b2cl_data[$i]->invoice_no);
                $activeSheet->setCellValue('D' . $x, $b2cl_data[$i]->inv_date);
                $activeSheet->setCellValue('E' . $x, $b2cl_data[$i]->sales_grand_total);
                $activeSheet->setCellValue('F' . $x, $b2cl_data[$i]->state_code . '- ' . $b2cl_data[$i]->state_name);
                $activeSheet->setCellValue('G' . $x, $b2cl_data[$i]->reverse);
                $activeSheet->setCellValue('H' . $x, $b2cl_data[$i]->invoice_type);
                $activeSheet->setCellValue('I' . $x, '');
                $activeSheet->setCellValue('J' . $x, $b2cl_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $b2cl_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $x++;
            }
        } elseif ($this->input->post('submit') == "B2CS Receipts XL") {
            $string = "s.sales_party_id,s.sales_date as inv_date,s.sales_grand_total,s.sales_gst_payable as reverse,s.sales_type_of_supply as invoice_type,s.sales_invoice_number as invoice_no,s.financial_year_id,s.sales_id,s.sales_sub_total as total,si.sales_item_tax_percentage as tax_val,c.customer_gstin_number,c.customer_name as receiver_name,s.sales_grand_total as gross_adv,s.sales_taxable_value as taxable_val,st.state_name,st.state_code,c.customer_gst_registration_type,s.sales_billing_state_id";
            $from = "sales_item si";
            $join = [
                'sales s' => "si.sales_id = s.sales_id",
                'states st' => "st.state_id=s.sales_billing_state_id",
                's.sales_date >=' => $from_date,
                's.sales_date <=' => $to_date,
                'customer c' => "c.customer_id=s.sales_party_id"];
            $where = array(
                's.delete_status' => 0,
                'si.delete_status' => 0,
                's.sales_grand_total <' => '250000.00',
                's.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                's.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Unregistered');
            $order = [
                's.sales_id' => "desc"];
            $group_by = [
                's.sales_billing_state_id',
                'si.sales_item_tax_percentage'];
            $b2cl_data = $this->report_model->getJoinRecords($string, $from, $where, $join, $order, $group_by);
            foreach (range('A', 'G') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'Type');
            $activeSheet->setCellValue("B1", 'Place Of Supply');
            $activeSheet->setCellValue("C1", 'Rate');
            $activeSheet->setCellValue("D1", 'Applicable % of Tax Rate');
            $activeSheet->setCellValue("E1", 'Taxable Value');
            $activeSheet->setCellValue("F1", 'Cess Amount');
            $activeSheet->setCellValue("G1", 'E-Commerce GSTIN');
            $x = 2;
            for ($i = 0; $i < count($b2cl_data); $i++) {
                $activeSheet->setCellValue('A' . $x, '');
                $activeSheet->setCellValue('B' . $x, $b2cl_data[$i]->state_code . '- ' . $b2cl_data[$i]->state_name);
                $activeSheet->setCellValue('C' . $x, $b2cl_data[$i]->tax_val);
                $activeSheet->setCellValue('D' . $x, '');
                $activeSheet->setCellValue('E' . $x, $b2cl_data[$i]->taxable_val);
                $activeSheet->setCellValue('F' . $x, '');
                $activeSheet->setCellValue('G' . $x, '');
                $x++;
            }
        } elseif ($this->input->post('submit') == "HSN Receipts XL") {
            $string = "s.financial_year_id,s.sales_id,s.sales_tax_amount as tax_val,s.sales_igst_amount as tot_igst,s.sales_sgst_amount as tot_sgst,s.sales_cgst_amount as tot_cgst,s.sales_taxable_value as taxable_val,s.sales_grand_total as total_val,sum(si.sales_item_quantity) as qty,pr.product_hsn_sac_code";
            $from = "sales_item si";
            $join = [
                'sales s' => "si.sales_id = s.sales_id",
                'products pr' => "pr.product_id=si.item_id"];
            $where = array(
                's.delete_status' => 0,
                'si.delete_status' => 0,
                's.sales_date >=' => $from_date,
                's.sales_date <=' => $to_date,
                's.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                's.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'));
            $order = [
                's.sales_id' => "desc"];
            $group_by = [
                'pr.product_hsn_sac_code'];
            $hsn_data = $this->report_model->getJoinRecords($string, $from, $where, $join, $order, $group_by);
            foreach (range('A', 'J') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'HSN');
            $activeSheet->setCellValue("B1", 'Description');
            $activeSheet->setCellValue("C1", 'UQC');
            $activeSheet->setCellValue("D1", 'Total Quantity');
            $activeSheet->setCellValue("E1", 'Total Value');
            $activeSheet->setCellValue("F1", 'Taxable Value');
            $activeSheet->setCellValue("G1", 'Integrated Tax Amount');
            $activeSheet->setCellValue("H1", 'Central Tax Amount');
            $activeSheet->setCellValue("I1", 'State/UT Tax Amount');
            $activeSheet->setCellValue("J1", 'Cess Amount');
            $x = 2;
            for ($i = 0; $i < count($hsn_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $hsn_data[$i]->product_hsn_sac_code);
                $activeSheet->setCellValue('B' . $x, '');
                $activeSheet->setCellValue('C' . $x, '');
                $activeSheet->setCellValue('D' . $x, $hsn_data[$i]->qty);
                $activeSheet->setCellValue('E' . $x, $hsn_data[$i]->total_val);
                $activeSheet->setCellValue('F' . $x, $hsn_data[$i]->taxable_val);
                $activeSheet->setCellValue('G' . $x, $hsn_data[$i]->tot_igst);
                $activeSheet->setCellValue('H' . $x, $hsn_data[$i]->tot_cgst);
                $activeSheet->setCellValue('I' . $x, $hsn_data[$i]->tot_sgst);
                $activeSheet->setCellValue('J' . $x, '');
                $x++;
            }
        } elseif ($this->input->post('submit') == "EXEMP Receipts XL") {
            //$exemp_data = $this->report_model->getExempReceiptData($month_hidden);
            foreach (range('A', 'D') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'Description');
            $activeSheet->setCellValue("B1", 'Nil Rated Supplies');
            $activeSheet->setCellValue("C1", 'Exempted(other than nil rated/non GST supply)');
            $activeSheet->setCellValue("D1", 'Non-GST Supplies');
            $x = 2;
            $activeSheet->setCellValue('A' . $x, 'Inter-State supplies to registered persons');
            $activeSheet->setCellValue('B' . $x, $exemp_data["reg_inter_nill"]);
            $activeSheet->setCellValue('C' . $x, $exemp_data["reg_inter_exempt"]);
            $activeSheet->setCellValue('D' . $x, '');
            $x++;
            $activeSheet->setCellValue('A' . $x, 'Intra-State supplies to registered persons');
            $activeSheet->setCellValue('B' . $x, $exemp_data["reg_intra_nill"]);
            $activeSheet->setCellValue('C' . $x, $exemp_data["reg_intra_exempt"]);
            $activeSheet->setCellValue('D' . $x, '');
            $x++;
            $activeSheet->setCellValue('A' . $x, 'Inter-State supplies to unregistered persons');
            $activeSheet->setCellValue('B' . $x, $exemp_data["unreg_inter_nill"]);
            $activeSheet->setCellValue('C' . $x, $exemp_data["unreg_inter_exempt"]);
            $activeSheet->setCellValue('D' . $x, '');
            $x++;
            $activeSheet->setCellValue('A' . $x, 'Intra-State supplies to unregistered persons');
            $activeSheet->setCellValue('B' . $x, $exemp_data["unreg_intra_nill"]);
            $activeSheet->setCellValue('C' . $x, $exemp_data["unreg_intra_exempt"]);
            $activeSheet->setCellValue('D' . $x, '');
            $x++;
        } elseif ($this->input->post('submit') == "CDNR Receipts XL") {
            $string = "ci.credit_note_item_id,cr.credit_note_id,cr.credit_note_date as voucher_date,cr.sales_credit_note_grand_total as inv_total,cr.credit_note_gst_payable as reverse,cr.sales_credit_note_invoice_number as cr_invoice_no,ci.credit_note_item_tax_percentage as tax_val,c.customer_state_code,c.customer_state_id,c.customer_name,c.customer_gstin_number,cr.credit_note_taxable_value as taxable_val,st.state_name,sl.sales_date as inv_date,st.state_code,sl.sales_invoice_number as invoice_no";
            $from = "credit_note_item ci";
            $join = [
                'credit_note cr' => "cr.credit_note_id = ci.credit_note_id",
                'sales sl' => "sl.sales_id = cr.sales_id",
                'customer c' => "c.customer_id = cr.credit_note_party_id AND cr.credit_note_party_type='customer' ",
                'states st' => "st.state_id=sl.sales_billing_state_id"];
            $where = array(
                'cr.delete_status' => 0,
                'ci.delete_status' => 0,
                'cr.credit_note_date >=' => $from_date,
                'cr.credit_note_date <=' => $to_date,
                'cr.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'cr.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Registered');
            $order = [
                'cr.credit_note_id' => "desc"];
            $group_by = [
                'sl.sales_billing_state_id',
                'ci.credit_note_item_tax_percentage'];
            $cr_data = $this->report_model->getJoinRecords($string, $from, $where, $join, $order, $group_by);
            $dr_string = "dr.debit_note_party_id,dr.debit_note_id,dr.debit_note_date as voucher_date,dr.debit_note_grand_total as inv_total,dr.debit_note_gst_payable as reverse,dr.debit_note_invoice_number as dr_invoice_no,dr.financial_year_id,dr.debit_note_id,di.debit_note_item_tax_percentage as tax_val,c.customer_state_code,c.customer_state_id,c.customer_gstin_number,c.customer_name,dr.debit_note_taxable_value as taxable_val,st.state_name,sl.sales_date as inv_date,sl.sales_billing_state_id,st.state_code,sl.sales_invoice_number as invoice_no";
            $dr_from = "debit_note_item di";
            $dr_join = [
                'debit_note dr' => "di.debit_note_id = dr.debit_note_id",
                'sales sl' => "sl.sales_id = dr.sales_id",
                'customer c' => "c.customer_id = dr.debit_note_party_id AND dr.debit_note_party_type='customer'",
                'states st' => "st.state_id=sl.sales_billing_state_id"];
            $dr_where = array(
                'dr.delete_status' => 0,
                'di.delete_status' => 0,
                'dr.debit_note_date >=' => $from_date,
                'dr.debit_note_date <=' => $to_date,
                'dr.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'dr.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Registered');
            $dr_order = [
                'dr.debit_note_id' => "desc"];
            $dr_group_by = [
                'sl.sales_billing_state_id',
                'di.debit_note_item_tax_percentage'];
            $dr_data = $this->report_model->getJoinRecords($dr_string, $dr_from, $dr_where, $dr_join, $dr_order, $dr_group_by);
            $refund_string = "r.party_id,r.refund_id,r.voucher_date as voucher_date,r.receipt_amount,r.voucher_number as rf_invoice_no,r.reference_number,r.financial_year_id,r.refund_id,ri.item_tax_percentage as tax_val,c.customer_state_code,c.customer_state_id,c.customer_name,c.customer_gstin_number,r.voucher_sub_total as taxable_val,st.state_name,r.billing_state_id,st.state_code";
            $refund_from = "refund_voucher_item ri";
            $refund_join = [
                'refund_voucher r' => "r.refund_id = ri.refund_voucher_id",
                'customer c' => "c.customer_id = r.party_id AND r.party_type='customer'",
                'states st' => "st.state_id=r.billing_state_id"];
            $refund_where = array(
                'r.delete_status' => 0,
                'ri.delete_status' => 0,
                'r.voucher_date >=' => $from_date,
                'r.voucher_date <=' => $to_date,
                'r.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'r.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Registered');
            $refund_order = [
                'r.refund_id' => "desc"];
            $refund_group_by = [
                'r.billing_state_id',
                'ri.item_tax_percentage'];
            $refund_data = $this->report_model->getJoinRecords($refund_string, $refund_from, $refund_where, $refund_join, $refund_order, $refund_group_by);
            foreach (range('A', 'N') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'GSTIN/UIN of Recipient');
            $activeSheet->setCellValue("B1", 'Receiver Name');
            $activeSheet->setCellValue("C1", 'Invoice/Advance Receipt Number');
            $activeSheet->setCellValue("D1", 'Invoice/Advance Receipt date');
            $activeSheet->setCellValue("E1", 'Note/Refund Voucher Number');
            $activeSheet->setCellValue("F1", 'Note/Refund Voucher date');
            $activeSheet->setCellValue("G1", 'Document Type');
            $activeSheet->setCellValue("H1", 'Place Of Supply');
            $activeSheet->setCellValue("I1", 'Note/Refund Voucher Value');
            $activeSheet->setCellValue("J1", 'Rate');
            $activeSheet->setCellValue("K1", 'Applicable % of Tax Rate');
            $activeSheet->setCellValue("L1", 'Taxable Value');
            $activeSheet->setCellValue("M1", 'Cess Amount');
            $activeSheet->setCellValue("N1", 'Pre GST');
            $x = 2;
            for ($i = 0; $i < count($cr_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $cr_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $cr_data[$i]->customer_name);
                $activeSheet->setCellValue('C' . $x, $cr_data[$i]->invoice_no);
                $activeSheet->setCellValue('D' . $x, $cr_data[$i]->inv_date);
                $activeSheet->setCellValue('E' . $x, $cr_data[$i]->cr_invoice_no);
                $activeSheet->setCellValue('F' . $x, $cr_data[$i]->voucher_date);
                $activeSheet->setCellValue('G' . $x, 'C');
                $activeSheet->setCellValue('H' . $x, $cr_data[$i]->state_code . '- ' . $cr_data[$i]->state_name);
                $activeSheet->setCellValue('I' . $x, $cr_data[$i]->inv_total);
                $activeSheet->setCellValue('J' . $x, $cr_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $cr_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $activeSheet->setCellValue('N' . $x, $cr_data[$i]->reverse);
                $x++;
            }
            for ($i = 0; $i < count($dr_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $dr_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $dr_data[$i]->customer_name);
                $activeSheet->setCellValue('C' . $x, $dr_data[$i]->invoice_no);
                $activeSheet->setCellValue('D' . $x, $dr_data[$i]->inv_date);
                $activeSheet->setCellValue('E' . $x, $dr_data[$i]->dr_invoice_no);
                $activeSheet->setCellValue('F' . $x, $dr_data[$i]->voucher_date);
                $activeSheet->setCellValue('G' . $x, 'D');
                $activeSheet->setCellValue('H' . $x, $dr_data[$i]->state_code . '- ' . $dr_data[$i]->state_name);
                $activeSheet->setCellValue('I' . $x, $dr_data[$i]->inv_total);
                $activeSheet->setCellValue('J' . $x, $dr_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $dr_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $activeSheet->setCellValue('N' . $x, $dr_data[$i]->reverse);
                $x++;
            }
            for ($i = 0; $i < count($refund_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $refund_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $refund_data[$i]->customer_name);
                $activeSheet->setCellValue('C' . $x, $refund_data[$i]->reference_number);
                $activeSheet->setCellValue('D' . $x, '');
                $activeSheet->setCellValue('E' . $x, $refund_data[$i]->rf_invoice_no);
                $activeSheet->setCellValue('F' . $x, $refund_data[$i]->voucher_date);
                $activeSheet->setCellValue('G' . $x, 'R');
                $activeSheet->setCellValue('H' . $x, $refund_data[$i]->state_code . '- ' . $refund_data[$i]->state_name);
                $activeSheet->setCellValue('I' . $x, $refund_data[$i]->receipt_amount);
                $activeSheet->setCellValue('J' . $x, $refund_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $refund_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $activeSheet->setCellValue('N' . $x, 'no');
                $x++;
            }
        } elseif ($this->input->post('submit') == "CDNUR Receipts XL") {
            $string = "cr.credit_note_id,cr.credit_note_date as voucher_date,cr.sales_credit_note_grand_total as inv_total,cr.credit_note_gst_payable as reverse,cr.sales_credit_note_invoice_number as cr_invoice_no,ci.credit_note_item_tax_percentage as tax_val,c.customer_state_code,c.customer_state_id,c.customer_name,c.customer_gstin_number,cr.credit_note_taxable_value as taxable_val,st.state_name,sl.sales_date as inv_date,st.state_code,sl.sales_invoice_number as invoice_no";
            $from = "credit_note_item ci";
            $join = [
                'credit_note cr' => "cr.credit_note_id = ci.credit_note_id",
                'sales sl' => "sl.sales_id = cr.sales_id",
                'customer c' => "c.customer_id = cr.credit_note_party_id AND cr.credit_note_party_type='customer'",
                'states st' => "st.state_id=sl.sales_billing_state_id"];
            $where = array(
                'cr.delete_status' => 0,
                'ci.delete_status' => 0,
                'cr.credit_note_date >=' => $from_date,
                'cr.credit_note_date <=' => $to_date,
                'cr.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'cr.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Unregistered');
            $order = [
                'cr.credit_note_id' => "desc"];
            $group_by = [
                'sl.sales_billing_state_id',
                'ci.credit_note_item_tax_percentage'];
            $cr_data = $this->report_model->getJoinRecords($string, $from, $where, $join, $order, $group_by);
            $dr_string = "dr.debit_note_party_id,dr.debit_note_id,dr.debit_note_date as voucher_date,dr.debit_note_grand_total as inv_total,dr.debit_note_gst_payable as reverse,dr.debit_note_invoice_number as dr_invoice_no,dr.financial_year_id,dr.debit_note_id,di.debit_note_item_tax_percentage as tax_val,c.customer_state_code,c.customer_state_id,c.customer_gstin_number,c.customer_name,dr.debit_note_taxable_value as taxable_val,st.state_name,sl.sales_date as inv_date,sl.sales_billing_state_id,st.state_code,sl.sales_invoice_number as invoice_no";
            $dr_from = "debit_note_item di";
            $dr_join = [
                'debit_note dr' => "di.debit_note_id = dr.debit_note_id",
                'sales sl' => "sl.sales_id = dr.sales_id",
                'customer c' => "c.customer_id = dr.debit_note_party_id AND dr.debit_note_party_type='customer'",
                'states st' => "st.state_id=sl.sales_billing_state_id"];
            $dr_where = array(
                'dr.delete_status' => 0,
                'di.delete_status' => 0,
                'dr.debit_note_date >=' => $from_date,
                'dr.debit_note_date <=' => $to_date,
                'dr.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'dr.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Unregistered');
            $dr_order = [
                'dr.debit_note_id' => "desc"];
            $dr_group_by = [
                'sl.sales_billing_state_id',
                'di.debit_note_item_tax_percentage'];
            $dr_data = $this->report_model->getJoinRecords($dr_string, $dr_from, $dr_where, $dr_join, $dr_order, $dr_group_by);
            $refund_string = "r.party_id,r.refund_id,r.voucher_date as voucher_date,r.receipt_amount,r.voucher_number as rf_invoice_no,r.reference_number,r.financial_year_id,r.refund_id,ri.item_tax_percentage as tax_val,c.customer_state_code,c.customer_state_id,c.customer_name,c.customer_gstin_number,r.voucher_sub_total as taxable_val,st.state_name,r.billing_state_id,st.state_code";
            $refund_from = "refund_voucher_item ri";
            $refund_join = [
                'refund_voucher r' => "r.refund_id = ri.refund_voucher_id",
                'customer c' => "c.customer_id = r.party_id AND r.party_type='customer'",
                'states st' => "st.state_id=r.billing_state_id"];
            $refund_where = array(
                'r.delete_status' => 0,
                'ri.delete_status' => 0,
                'r.voucher_date >=' => $from_date,
                'r.voucher_date <=' => $to_date,
                'r.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'r.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'c.customer_gst_registration_type' => 'Unregistered');
            $refund_order = [
                'r.refund_id' => "desc"];
            $refund_group_by = [
                'r.billing_state_id',
                'ri.item_tax_percentage'];
            $refund_data = $this->report_model->getJoinRecords($refund_string, $refund_from, $refund_where, $refund_join, $refund_order, $refund_group_by);
            foreach (range('A', 'N') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $activeSheet->setCellValue("A1", 'GSTIN/UIN of Recipient');
            $activeSheet->setCellValue("B1", 'Receiver Name');
            $activeSheet->setCellValue("C1", 'Invoice/Advance Receipt Number');
            $activeSheet->setCellValue("D1", 'Invoice/Advance Receipt date');
            $activeSheet->setCellValue("E1", 'Note/Refund Voucher Number');
            $activeSheet->setCellValue("F1", 'Note/Refund Voucher date');
            $activeSheet->setCellValue("G1", 'Document Type');
            $activeSheet->setCellValue("H1", 'Place Of Supply');
            $activeSheet->setCellValue("I1", 'Note/Refund Voucher Value');
            $activeSheet->setCellValue("J1", 'Rate');
            $activeSheet->setCellValue("K1", 'Applicable % of Tax Rate');
            $activeSheet->setCellValue("L1", 'Taxable Value');
            $activeSheet->setCellValue("M1", 'Cess Amount');
            $activeSheet->setCellValue("N1", 'Pre GST');
            $x = 2;
            for ($i = 0; $i < count($cr_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $cr_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $cr_data[$i]->customer_name);
                $activeSheet->setCellValue('C' . $x, $cr_data[$i]->invoice_no);
                $activeSheet->setCellValue('D' . $x, $cr_data[$i]->inv_date);
                $activeSheet->setCellValue('E' . $x, $cr_data[$i]->cr_invoice_no);
                $activeSheet->setCellValue('F' . $x, $cr_data[$i]->voucher_date);
                $activeSheet->setCellValue('G' . $x, 'C');
                $activeSheet->setCellValue('H' . $x, $cr_data[$i]->state_code . '- ' . $cr_data[$i]->state_name);
                $activeSheet->setCellValue('I' . $x, $cr_data[$i]->inv_total);
                $activeSheet->setCellValue('J' . $x, $cr_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $cr_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $activeSheet->setCellValue('N' . $x, $cr_data[$i]->reverse);
                $x++;
            }
            for ($i = 0; $i < count($dr_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $dr_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $dr_data[$i]->customer_name);
                $activeSheet->setCellValue('C' . $x, $dr_data[$i]->invoice_no);
                $activeSheet->setCellValue('D' . $x, $dr_data[$i]->inv_date);
                $activeSheet->setCellValue('E' . $x, $dr_data[$i]->dr_invoice_no);
                $activeSheet->setCellValue('F' . $x, $dr_data[$i]->voucher_date);
                $activeSheet->setCellValue('G' . $x, 'D');
                $activeSheet->setCellValue('H' . $x, $dr_data[$i]->state_code . '- ' . $dr_data[$i]->state_name);
                $activeSheet->setCellValue('I' . $x, $dr_data[$i]->inv_total);
                $activeSheet->setCellValue('J' . $x, $dr_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $dr_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $activeSheet->setCellValue('N' . $x, $dr_data[$i]->reverse);
                $x++;
            }
            for ($i = 0; $i < count($refund_data); $i++) {
                $activeSheet->setCellValue('A' . $x, $refund_data[$i]->customer_gstin_number);
                $activeSheet->setCellValue('B' . $x, $refund_data[$i]->customer_name);
                $activeSheet->setCellValue('C' . $x, $refund_data[$i]->reference_number);
                $activeSheet->setCellValue('D' . $x, '');
                $activeSheet->setCellValue('E' . $x, $refund_data[$i]->rf_invoice_no);
                $activeSheet->setCellValue('F' . $x, $refund_data[$i]->voucher_date);
                $activeSheet->setCellValue('G' . $x, 'R');
                $activeSheet->setCellValue('H' . $x, $refund_data[$i]->state_code . '- ' . $refund_data[$i]->state_name);
                $activeSheet->setCellValue('I' . $x, $refund_data[$i]->receipt_amount);
                $activeSheet->setCellValue('J' . $x, $refund_data[$i]->tax_val);
                $activeSheet->setCellValue('K' . $x, '');
                $activeSheet->setCellValue('L' . $x, $refund_data[$i]->taxable_val);
                $activeSheet->setCellValue('M' . $x, '');
                $activeSheet->setCellValue('N' . $x, 'no');
                $x++;
            }
        }

        $month_value = 'GST';

        $filename = $month_value . "_report.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $filename . '');
        header('Cache-Control: max-age=0');
        $Excel_writer->save('php://output');
        redirect('report/sales', 'refresh');
    }

    public function sales() {
        $sales_module_id = $this->config->item('sales_module');
        $data['module_id'] = $sales_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');

        $user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['common_settings'][0]->default_notification_date;
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'invoice',
                2 => 'customer',
                3 => 'customer_gstin_number',
                4 => 'customer_address',
                5 => 'nature_of_supply',
                6 => 'shipping_address',
                7 => 'place_of_supply',
                8 => 'type_of_supply',
                9 => 'gst_payable',
                10 => 'e_way_bill',
                11 => 'taxable_amount',
                12 => 'igst_amount',
                13 => 'cgst_amount',
                14 => 'sgst_amount',
                15 => 'invoice_amount',
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->sales_list_field();
            $list_data['where'] = array(
                's.delete_status' => 0,
                's.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                's.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                's.converted_grand_total !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'sales';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_customer_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_received_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "") {
                $filter_search = array();
                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = $this->input->post('filter_from_date');
                $filter_search['filter_to_date'] = $this->input->post('filter_to_date');
                $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount')));
                $filter_search['filter_received_amount'] = ($this->input->post('filter_received_amount') == '' ? '' : implode(",", $this->input->post('filter_received_amount')));
                $filter_search['filter_payment_status'] = ($this->input->post('filter_payment_status') == '' ? '' : implode(",", $this->input->post('filter_payment_status')));
                $filter_search['filter_pending_amount'] = ($this->input->post('filter_pending_amount') == '' ? '' : implode(",", $this->input->post('filter_pending_amount')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_due'] = ($this->input->post('filter_due') == '' ? '' : implode(",", $this->input->post('filter_due')));
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            // $string=$list_data['string'];
            $string = "sum(t.sales_taxable_value) as tot_sales_taxable_value, sum(t.sales_igst_amount) as tot_sales_igst_amount, sum(t.sales_cgst_amount) as tot_sales_cgst_amount, sum(t.sales_sgst_amount) as tot_sales_sgst_amount, sum(t.converted_grand_total) as tot_sales_grand_total, sum(t.converted_credit_note_amount) as tot_credit_note_amount, sum(t.converted_debit_note_amount) as tot_debit_note_amount, (sum(t.converted_grand_total)+sum(t.converted_credit_note_amount)-sum(t.converted_debit_note_amount)) as tot_grand_total, sum(t.sales_paid_amount) as tot_sales_paid_amount, (sum(t.converted_grand_total)+sum(t.converted_credit_note_amount)-sum(t.converted_debit_note_amount)-sum(t.converted_paid_amount)) as tot_sales_pending_amount";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);

            // $list_data['string'] = $string;

            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->sales_date . ' + 15 days'));
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->sales_date));
                    $nestedData['invoice'] = $post->sales_invoice_number;
                    $nestedData['customer'] = $post->customer_name;
                    $nestedData['grand_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $post->currency_converted_amount . ' (INV)';
                    if ($post->converted_credit_note_amount != 0) {
                        $nestedData['grand_total'] .= '<br>' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $post->converted_credit_note_amount . ' (CN)';
                    }
                    if ($post->converted_debit_note_amount != 0) {
                        $nestedData['grand_total'] .= '<br>' . $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $post->converted_debit_note_amount . ' (DN)';
                    }
                    // $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['paid_amount'] = $post->converted_paid_amount;
                    if ($post->converted_paid_amount == 0) {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    } elseif (($post->converted_paid_amount + $post->converted_debit_note_amount) < ($post->currency_converted_amount + $post->converted_credit_note_amount)) {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    } else {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }
                    $nestedData['pending_amount'] = ($post->currency_converted_amount + $post->converted_credit_note_amount) - ($post->converted_paid_amount + $post->converted_debit_note_amount);
                    $nestedData['receivable_date'] = $post->receivable_date;
                    $nestedData['due'] = $post->due;
                    // if ($data['access_module_privilege']->add_privilege == "yes")
                    // {
                    //     if ($post->sales_paid_amount < ($post->sales_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                    //     {
                    //         $cols = '<ul class="list-inline"> <li>                        <a href="' . base_url('receipt_voucher/add_sales_receipt/') . $sales_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                        </li>';
                    //     }
                    // } $cols                  .= '<li> <a href="#" data-toggle="modal" onclick="addToModel(' . $post->sales_id . ')" data-target="#myModal"><i class="fa fa-eye text-orange"></i> Follow Up dates</a> </li></ul>';
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }
                    // $cols                 .= '</ul>';
                    // $nestedData['action'] = $cols;
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->distinct_customer();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_invoice_amount();
            $data['invoice_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_received_amount();
            $data['received_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_pending_amount();
            $data['pending_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_due_day();
            $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_currency_converted_records('sales');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->sum_sales();
            $data['total_sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');

            $list_data = $this->common->sales_sum_gt();
            $total_sum = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');
            // print_r($total_sum );die;
            $ar = [
            ];
            foreach ($total_sum as $key) {
                if ($key->currency_converted_amount == 0) {
                    $ar[] = $key->sales_grand_total + $key->credit_note_amount;
                } else {
                    $ar[] = $key->currency_converted_amount + $key->converted_credit_note_amount;
                }
            }

            $data['coverted_total'] = array_sum($ar);


            $this->load->view('reports/sales', $data);
        }
    }

    public function purchase_report() {
        $purchase_module_id = $this->config->item('purchase_module');
        $data['module_id'] = $purchase_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');

        $user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['common_settings'][0]->default_notification_date;
        $purchase_return_module_id = $this->config->item('purchase_return_module');
        $modules_present = array(
            'purchase_return_module_id' => $purchase_return_module_id);
        $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'supplier',
                2 => 'grand_total',
                3 => 'currency_converted_amount',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'invoice',
                7 => 'action',
                8 => 'due_date',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->purchase_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $purchase_id = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date'] = $post->purchase_date;
                    $nestedData['supplier'] = $post->supplier_name;
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $post->purchase_grand_total . ' (INV)';
                    if ($post->credit_note_amount > 0) {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->credit_note_amount . ' (CN)';
                    }
                    if ($post->debit_note_amount > 0) {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->debit_note_amount . ' (DN)';
                    }
                    $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['paid_amount'] = $post->purchase_paid_amount;
                    $nestedData['payment_status'] = date('Y-m-d', strtotime($post->purchase_date . '+' . $default_date . 'days'));
                    $nestedData['invoice'] = $post->purchase_invoice_number;
                    if ($data['access_module_privilege']->add_privilege == "yes") {
                        if ($post->purchase_paid_amount < ($post->purchase_grand_total + $post->credit_note_amount - $post->debit_note_amount)) {
                            $cols = '<ul class="action_ul_custom"><li>                                    <a href="' . base_url('payment_voucher/add_purchase_payment/') . $purchase_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                                    </li>';
                        }
                    }
                    $cols .= '<li>                    <a href="#" data-toggle="modal" onclick="addToModel(' . $post->purchase_id . ')" data-target="#myModal1"><i class="fa fa-eye text-orange"></i> Follow Up dates</a>                    </li>';
                    $d1 = date('Y-m-d', strtotime($post->purchase_date . '+' . $default_date . 'days'));
                    $date1 = date_create($d1);
                    $date2 = date_create(date('Y-m-d'));
                    $diff = date_diff($date1, $date2);
                    $over_due_date = $diff->format("%R%a");
                    if ($over_due_date >= 0) {
                        $nestedData['due_date'] = str_replace("+", "", $over_due_date . ' exceeded');
                    } else {
                        $nestedData['due_date'] = str_replace("-", "", $over_due_date . ' remaining');
                    }
                    $cols .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view('reports/purchase', $data);
        }
    }

    public function expense_bill_reports() {
        $expense_bill_module_id = $this->config->item('expense_bill_module');
        $data['module_id'] = $expense_bill_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'invoice',
                2 => 'payee',
                3 => 'grand_total',
                4 => 'currency_converted_amount',
                5 => 'paid_amount',
                6 => 'added_user',
                7 => 'action',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->expense_bill_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $nestedData['date'] = $post->expense_bill_date;
                    $nestedData['invoice'] = $post->expense_bill_invoice_number;
                    $nestedData['payee'] = $post->supplier_name;
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $post->expense_bill_grand_total;
                    $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['paid_amount'] = $post->expense_bill_paid_amount;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $expense_bill_id = $this->encryption_url->encode($post->expense_bill_id);
                    $cols = '<ul class="list-inline">            <li>            <a href="' . base_url('expense_bill/view/') . $expense_bill_id . '"><i class="fa fa-eye text-orange"></i> Expense Bill Details</a>            </li>';
                    $cols .= '<li>        <a href="#" data-toggle="modal" onclick="addToModel(' . $post->expense_bill_id . ')" data-target="#myModal"><i class="fa fa-eye text-orange"></i>Follow Up dates</a>        </li>';
                    if ($data['access_module_privilege']->add_privilege == "yes") {
                        if ($post->expense_bill_paid_amount < $post->expense_bill_grand_total) {
                            $cols .= '<li>                            <a href="' . base_url('payment_voucher/add_expense_payment/') . $expense_bill_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                            </li>';
                        }
                    }
                    if ($data['access_module_privilege']->edit_privilege == "yes") {
                        if ($post->expense_bill_paid_amount == 0 || $post->expense_bill_paid_amount == null) {
                            $cols .= '<li>                <a href="' . base_url('expense_bill/edit/') . $expense_bill_id . '"><i class="fa fa-pencil text-blue"></i> Edit Expense Bill</a>                </li>';
                        }
                    }
                    $cols .= '<li>                        <a href="' . base_url('expense_bill/pdf/') . $expense_bill_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i> Download PDF</a>                    </li>';
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }
                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY')) {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $expense_bill_id . '" data-path="expense_bill/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->expense_bill_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>                    </li>';
                    }
                    $cols .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view("reports/expense_bill", $data);
        }
    }

    public function custom_sales() {
        $list_data = $this->common->sales_list_field();
        $data['posts'] = $this->general_model->getPageJoinRecords($list_data);
        $this->load->view('reports/sales_demo123', $data);
    }

    public function sales_test() {
        $sales_report_module_id = $this->config->item('sales_report_module');
        $data['module_id'] = $sales_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $sales_module_id = $this->config->item('sales_module');
        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);

        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'date',
                1 => 'invoice',
                2 => 'customer',
                3 => 'Nature_of_Supply',
                4 => 'bill_to_name',
                5 => 'bill_to_address',
                6 => 'ship_to_name',
                7 => 'shipping_address',
                8 => 'place_of_supply',
                9 => 'sales_type_of_supply',
                10 => 'sales_gst_payable',
                11 => 'sales_e_way_bill_number',
                12 => 'sales_taxable_value',
                13 => 'sales_cgst_amount',
                14 => 'sales_sgst_amount',
                15 => 'sales_igst_amount',
                16 => 'grand_total'
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->sales_list_field();
            $list_data['where'] = array(
                's.delete_status' => 0,
                's.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                's.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                's.sales_grand_total !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'sales';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            // $string=$list_data['string'];
            // $list_data['string'] = "sum(s.sales_taxable_value*s.currency_converted_rate) as tot_sales_taxable_value, sum(s.sales_igst_amount*s.currency_converted_rate) as tot_sales_igst_amount, sum(s.sales_cgst_amount*s.currency_converted_rate) as tot_sales_cgst_amount, sum(s.sales_sgst_amount*s.currency_converted_rate) as tot_sales_sgst_amount, sum(s.sales_grand_total*s.currency_converted_rate) as tot_sales_grand_total, sum(s.credit_note_amount*s.currency_converted_rate) as tot_credit_note_amount, sum(s.debit_note_amount*s.currency_converted_rate) as tot_debit_note_amount, (sum(s.sales_grand_total*s.currency_converted_rate)+sum(s.credit_note_amount*s.currency_converted_rate)-sum(s.debit_note_amount*s.currency_converted_rate)) as tot_grand_total, sum(s.sales_paid_amount*s.currency_converted_rate) as tot_sales_paid_amount, (sum(s.sales_grand_total*s.currency_converted_rate)+sum(s.credit_note_amount*s.currency_converted_rate)-sum(s.debit_note_amount*s.currency_converted_rate)-sum(s.sales_paid_amount*s.currency_converted_rate)) as tot_sales_pending_amount";
            // $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data);
            // $list_data['string'] = $string;

            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_customer_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_received_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "" || $this->input->post('filter_nature_of_supply') != "" || $this->input->post('filter_billing_to') != "" || $this->input->post('filter_shipping_to') != ""|| $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_type_of_supply') != "" || $this->input->post('filter_payable_on_reverse_charge') != "" || $this->input->post('filter_taxable_amount') != "" || $this->input->post('filter_cgst_from') != "" || $this->input->post('filter_cgst_to') != "" || $this->input->post('filter_sgst_from') != "" || $this->input->post('filter_sgst_to') != "" || $this->input->post('filter_utgst_from') != "" || $this->input->post('filter_utgst_to') != "" || $this->input->post('filter_igst_from') != "" || $this->input->post('filter_igst_to') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_invoice_amount') != "" || $this->input->post('filter_to_invoice_amount') != "" || $this->input->post('filter_from_due_days') != "" || $this->input->post('filter_to_due_days') != "") {
                $filter_search = array();
                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_nature_of_supply'] = ($this->input->post('filter_nature_of_supply') == '' ? '' : implode(",", $this->input->post('filter_nature_of_supply')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount')));
                $filter_search['filter_received_amount'] = ($this->input->post('filter_received_amount') == '' ? '' : implode(",", $this->input->post('filter_received_amount')));
                $filter_search['filter_payment_status'] = ($this->input->post('filter_payment_status') == '' ? '' : implode(",", $this->input->post('filter_payment_status')));
                $filter_search['filter_pending_amount'] = ($this->input->post('filter_pending_amount') == '' ? '' : implode(",", $this->input->post('filter_pending_amount')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_due'] = ($this->input->post('filter_due') == '' ? '' : implode(",", $this->input->post('filter_due')));

                $filter_search['filter_billing_to'] = ($this->input->post('filter_billing_to') == '' ? '' : implode(",", $this->input->post('filter_billing_to')));

                $filter_search['filter_shipping_to'] = ($this->input->post('filter_shipping_to') == '' ? '' : implode(",", $this->input->post('filter_shipping_to')));

                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_type_of_supply'] = ($this->input->post('filter_type_of_supply') == '' ? '' : implode(",", $this->input->post('filter_type_of_supply')));
                $filter_search['filter_payable_on_reverse_charge'] = ($this->input->post('filter_payable_on_reverse_charge') == '' ? '' : implode(",", $this->input->post('filter_payable_on_reverse_charge')));
                $filter_search['filter_taxable_amount'] = ($this->input->post('filter_taxable_amount') == '' ? '' : implode(",", $this->input->post('filter_taxable_amount')));
                /*$filter_search['filter_cgst'] = ($this->input->post('filter_cgst') == '' ? '' : implode(",", $this->input->post('filter_cgst')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));
                $filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));
                $filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));*/
                $filter_search['filter_cgst_from_sales'] = $this->input->post('filter_cgst_from') ?: 0;
                $filter_search['filter_cgst_to_sales'] = $this->input->post('filter_cgst_to') ?: 0;
                $filter_search['filter_sgst_from_sales'] = $this->input->post('filter_sgst_from') ?: 0;
                $filter_search['filter_sgst_to_sales'] = $this->input->post('filter_sgst_to') ?: 0;
                $filter_search['filter_utgst_from_sales'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_sales'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['filter_igst_from_sales'] = $this->input->post('filter_igst_from') ?: 0;
                $filter_search['filter_igst_to_sales'] = $this->input->post('filter_igst_to') ?: 0;
                $filter_search['filter_sales_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount') ?: 0;
                $filter_search['filter_sales_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount') ?: 0;
                $filter_search['filter_sales_from_invoice_amount'] = $this->input->post('filter_from_invoice_amount') ?: 0;
                $filter_search['filter_sales_to_invoice_amount'] = $this->input->post('filter_to_invoice_amount') ?: 0;
                $filter_search['filter_from_due_day'] = $this->input->post('filter_from_due_days') ?: 0;
                $filter_search['filter_to_due_day'] = $this->input->post('filter_to_due_days') ?: 0;
               
                $list_data['limit'] = $limit;

                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            // $string=$list_data['string'];
            $string = "sum(t.sales_taxable_value) as tot_sales_taxable_value, sum(t.sales_igst_amount) as tot_sales_igst_amount, sum(t.sales_cgst_amount) as tot_sales_cgst_amount, sum(t.sales_sgst_amount) as tot_sales_sgst_amount,sum(t.sgst) as tot_sales_sgst_amount_new, sum(t.utgst) as tot_sales_utgst_amount_new, sum(t.sales_grand_total) as tot_sales_grand_total, sum(t.sales_paid_amount) as tot_sales_paid_amount";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);

            // $list_data['string'] = $string;
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) { //echo "<pre>";print_r($posts);die;
                    $recivable_date = date('Y-m-d', strtotime($post->sales_date . ' + 15 days'));
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->sales_date));
                    $nestedData['invoice'] = $post->sales_invoice_number;
                    if(in_array($sales_module_id, $data['active_view'])){
                        $nestedData['invoice'] = '<a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>';
                    }
                    $nestedData['customer'] = $post->customer_name;
                    // $nestedData['grand_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $post->sales_grand_total;
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' .$this->precise_amount($post->sales_grand_total, 2);
                    /* if ($post->converted_credit_note_amount != 0) {
                      $nestedData['grand_total'] .= '<br>' . $this->precise_amount($this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $post->converted_credit_note_amount ,2).' (CN)';

                      }
                      if ($post->converted_debit_note_amount != 0) {
                      $nestedData['grand_total'] .= '<br>' .  $this->precise_amount($this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $post->converted_debit_note_amount ,2) . ' (DN)';
                      } */
                    // $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    /* $nestedData['paid_amount'] = $this->precise_amount($post->sales_paid_amount,2);
                      if ($post->sales_paid_amount == 0) {
                      $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                      } elseif (($post->sales_paid_amount + $post->converted_debit_note_amount) < ($post->converted_grand_total + $post->converted_credit_note_amount)) {
                      $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                      } else {
                      $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                      } */
                    /* $nestedData['pending_amount'] = ($post->converted_grand_total + $post->converted_credit_note_amount) - ($post->sales_paid_amount + $post->converted_debit_note_amount);
                      $nestedData['pending_amount']=$this->precise_amount($nestedData['pending_amount'],2); */
                    $nestedData['receivable_date'] = date('d-m-Y', strtotime($post->receivable_date));
                    $nestedData['due'] = $post->due;
                    $nestedData['nature_of_supply'] = $post->sales_nature_of_supply;
                    $nestedData['bill_to_name'] = $post->customer_name;
                    $nestedData['bill_to_address'] = $post->bill_to_address ? $post->bill_to_address : "-";
                    $nestedData['ship_to_name'] = $post->ship_to_name ? $post->ship_to_name : "-";
                    $nestedData['shipping_address'] = $post->shipping_address ? $post->shipping_address : "-";
                    $nestedData['place_of_supply'] = $post->place_of_supply ? $post->place_of_supply : "Outside India";
                    $nestedData['sales_type_of_supply'] = ucwords($post->sales_type_of_supply ? $post->sales_type_of_supply : "-");
                    $nestedData['sales_type_of_supply'] = str_replace("_", ' ', $nestedData['sales_type_of_supply']);
                    $nestedData['sales_gst_payable'] = ucwords($post->sales_gst_payable ? $post->sales_gst_payable : "-");
                    $nestedData['sales_e_way_bill_number'] = $post->sales_e_way_bill_number ? $post->sales_e_way_bill_number : "-";
                    $nestedData['sales_taxable_value'] = $post->currency_symbol.' '.$this->precise_amount($post->sales_taxable_value, 2);
                    $nestedData['sales_cgst_amount'] = $this->precise_amount($post->sales_cgst_amount, 2);
                    $nestedData['sales_sgst_amount'] = $this->precise_amount($post->sales_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['sales_utgst_amount'] = $this->precise_amount($post->sales_sgst_amount, 2);
                        $nestedData['sales_sgst_amount'] = "0.00";
                    } else {
                        $nestedData['sales_sgst_amount'] = $this->precise_amount($post->sales_sgst_amount, 2);
                        $nestedData['sales_utgst_amount'] = "0.00";
                    }*/
                    // $nestedData['sales_sgst_amount'] = $this->precise_amount($post->sales_sgst_amount,2);
                    $nestedData['sales_igst_amount'] = $this->precise_amount($post->sales_igst_amount, 2);

                    //print_r($nestedData['nature_of_supply']);
                    // if ($data['access_module_privilege']->add_privilege == "yes")
                    // {
                    //     if ($post->sales_paid_amount < ($post->sales_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                    //     {
                    //         $cols = '<ul class="list-inline"> <li>                        <a href="' . base_url('receipt_voucher/add_sales_receipt/') . $sales_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                        </li>';
                    //     }
                    // } $cols                  .= '<li> <a href="#" data-toggle="modal" onclick="addToModel(' . $post->sales_id . ')" data-target="#myModal"><i class="fa fa-eye text-orange"></i> Follow Up dates</a> </li></ul>';
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }
                    // $cols                 .= '</ul>';
                    // $nestedData['action'] = $cols;
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->distinct_customer();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_billing_to();
            $data['billing_name'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_shipping_to();
            $data['shipping_name'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_place_of_supply();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_type_of_supply();
            $data['type_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_gst_payable();
            $data['sales_gst_payable'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_taxable_value();
            $data['sales_taxable_value'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);


            $list_data = $this->common->distinct_sales_cgst_amount();
            $data['sales_cgst_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_sgst_amount();
            $data['sales_sgst_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);


            $list_data = $this->common->distinct_sales_utgst_amount();
            $data['sales_utgst_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_igst_amount();
            $data['sales_igst_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_converted_grand_total();
            $data['converted_grand_total'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_invoice_amount();
            $data['invoice_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_nature_of_supply();
            $data['nature_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            //$invoice_amount=round($invoice_amount,2);
            //$data['invoice_amount'] =$invoice_amount;
            $list_data = $this->common->distinct_sales_received_amount();
            $data['received_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_pending_amount();
            $data['pending_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_due_day();
            $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_currency_converted_records('sales');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->sum_sales();
            $data['total_sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');

            $list_data = $this->common->sales_sum_gt();
            $total_sum = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');
            // print_r($total_sum );die;
            $ar = [];
            foreach ($total_sum as $key) {
                if ($key->converted_grand_total == 0) {
                    $ar[] = $key->sales_grand_total + $key->credit_note_amount;
                } else {
                    $ar[] = $key->converted_grand_total + $key->converted_credit_note_amount;
                }
            }

            $data['coverted_total'] = array_sum($ar);

            $this->load->view('reports/sales_test', $data);
        }
    }

    public function purchase_test() {
        $purchase_report_module_id = $this->config->item('purchase_report_module');
        $data['module_id'] = $purchase_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $purchase_module_id = $this->config->item('purchase_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);

        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'receivable_date',
                8 => 'due'
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->purchase_list_field();
            $list_data['where'] = array(
                'p.delete_status' => 0,
                'p.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'p.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'p.purchase_grand_total !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'purchase';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_supplier_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_received_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "" || $this->input->post('filter_nature_of_supply') != "" || $this->input->post('filter_reference_number') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_gst_payable') != "" || $this->input->post('filter_order_number') != "" || $this->input->post('filter_dilevery_number') != "" || $this->input->post('filter_e_way_billing') != "" ||$this->input->post('filter_cgst_from') != "" || $this->input->post('filter_cgst_to') != "" || $this->input->post('filter_sgst_from') != "" || $this->input->post('filter_sgst_to') != "" || $this->input->post('filter_utgst_from') != "" || $this->input->post('filter_utgst_to') != "" || $this->input->post('filter_igst_from') != "" || $this->input->post('filter_igst_to') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_invoice_amount') != "" || $this->input->post('filter_to_invoice_amount') != "" || $this->input->post('filter_supplier_invoice_from') != "" || $this->input->post('filter_supplier_invoice_to') != "" || $this->input->post('filter_order_from') != "" || $this->input->post('filter_order_to') != "" || $this->input->post('filter_from_due_days') != "" || $this->input->post('filter_to_due_days') != "") {
                $filter_search = array();
                $filter_search['filter_supplier_name'] = ($this->input->post('filter_supplier_name') == '' ? '' : implode(",", $this->input->post('filter_supplier_name')));
                $filter_search['filter_supplier_invoice_from'] = ($this->input->post('filter_supplier_invoice_from') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_supplier_invoice_from'))));
                $filter_search['filter_supplier_invoice_to'] = ($this->input->post('filter_supplier_invoice_to') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_supplier_invoice_to'))));
                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_invoice_amount'] = $this->input->post('filter_from_invoice_amount');
                $filter_search['filter_to_invoice_amount'] = $this->input->post('filter_to_invoice_amount');
                /*$filter_search['filter_cgst'] = ($this->input->post('filter_cgst') == '' ? '' : implode(",", $this->input->post('filter_cgst')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));
                $filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));
                $filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));*/
                $filter_search['filter_cgst_from_purchase'] = $this->input->post('filter_cgst_from') ?: 0;
                $filter_search['filter_cgst_to_purchase'] = $this->input->post('filter_cgst_to') ?: 0;
                $filter_search['filter_sgst_from_purchase'] = $this->input->post('filter_sgst_from') ?: 0;
                $filter_search['filter_sgst_to_purchase'] = $this->input->post('filter_sgst_to') ?: 0;
                $filter_search['filter_utgst_from_purchase'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_purchase'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['filter_igst_from_purchase'] = $this->input->post('filter_igst_from') ?: 0;
                $filter_search['filter_igst_to_purchase'] = $this->input->post('filter_igst_to') ?: 0;
                $filter_search['filter_e_way_billing'] = ($this->input->post('filter_e_way_billing') == '' ? '' : implode(",", $this->input->post('filter_e_way_billing')));
                $filter_search['filter_dilevery_number'] = ($this->input->post('filter_dilevery_number') == '' ? '' : implode(",", $this->input->post('filter_dilevery_number')));
                $filter_search['filter_order_number'] = ($this->input->post('filter_order_number') == '' ? '' : implode(",", $this->input->post('filter_order_number')));
                $filter_search['filter_gst_payable'] = ($this->input->post('filter_gst_payable') == '' ? '' : implode(",", $this->input->post('filter_gst_payable')));
                $filter_search['filter_nature_of_supply'] = ($this->input->post('filter_nature_of_supply') == '' ? '' : implode(",", $this->input->post('filter_nature_of_supply')));
                $filter_search['filter_reference_number'] = ($this->input->post('filter_reference_number') == '' ? '' : implode(",", $this->input->post('filter_reference_number')));
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_order_from'] = ($this->input->post('filter_order_from') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_order_from'))));
                $filter_search['filter_order_to'] = ($this->input->post('filter_order_to') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_order_to'))));
                $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount')));
                $filter_search['filter_received_amount'] = ($this->input->post('filter_received_amount') == '' ? '' : implode(",", $this->input->post('filter_received_amount')));
                $filter_search['filter_payment_status'] = ($this->input->post('filter_payment_status') == '' ? '' : implode(",", $this->input->post('filter_payment_status')));
                $filter_search['filter_pending_amount'] = ($this->input->post('filter_pending_amount') == '' ? '' : implode(",", $this->input->post('filter_pending_amount')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_due'] = ($this->input->post('filter_due') == '' ? '' : implode(",", $this->input->post('filter_due')));
                $filter_search['filter_pur_from_due_day'] = $this->input->post('filter_from_due_days') ?: 0;
                $filter_search['filter_pur_to_due_day'] = $this->input->post('filter_to_due_days') ?: 0;
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $string = "sum(t.purchase_taxable_value*t.currency_converted_rate) as tot_purchase_taxable_value, sum(t.purchase_igst_amount) as tot_purchase_igst_amount, sum(t.purchase_cgst_amount) as tot_purchase_cgst_amount, sum(t.sgst) as tot_purchase_sgst_amount, sum(t.utgst) as tot_purchase_utgst_amount, sum(t.purchase_grand_total) as tot_purchase_grand_total, sum(t.credit_note_amount) as tot_credit_note_amount, sum(t.debit_note_amount) as tot_debit_note_amount, (sum(t.purchase_grand_total)+sum(t.credit_note_amount)-sum(t.debit_note_amount)) as tot_grand_total, sum(t.purchase_paid_amount) as tot_purchase_paid_amount, (sum(t.purchase_grand_total)+sum(t.credit_note_amount)-sum(t.debit_note_amount)-sum(t.purchase_paid_amount)) as tot_purchase_pending_amount";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->purchase_date . ' + 15 days'));
                    $purchase_id = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->purchase_date));
                    $nestedData['invoice'] = $post->purchase_invoice_number;
                    if(in_array($purchase_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                    }
                    $nestedData['supplier'] = $post->supplier_name;
                    $nestedData['grand_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' . $this->precise_amount($post->purchase_grand_total, 2);
                    /* if ($post->credit_note_amount != 0) {
                      $nestedData['grand_total'] .= '<br>' . $this->precise_amount($post->credit_note_amount ,2). ' (CN)';
                      //$nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->credit_note_amount . ' (CN)';
                      }
                      if ($post->debit_note_amount != 0) {
                      // $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->debit_note_amount . ' (DN)';
                      $nestedData['grand_total'] .= '<br>' . $this->precise_amount($post->debit_note_amount,2) . ' (DN)';
                      } */
                    // $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['paid_amount'] = $this->precise_amount($post->purchase_paid_amount, 2);
                    if ($post->purchase_paid_amount == 0) {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    } elseif (($post->purchase_paid_amount + $post->debit_note_amount) < ($post->purchase_grand_total + $post->credit_note_amount)) {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    } else {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }
                    $nestedData['pending_amount'] = ($post->purchase_grand_total + $post->credit_note_amount) - ($post->purchase_paid_amount + $post->debit_note_amount);
                    $nestedData['receivable_date'] = date('d-m-Y', strtotime($post->receivable_date));
                    $nestedData['due'] = $post->due;
                    $nestedData['nature_of_supply'] = ucwords($post->purchase_nature_of_supply ? $post->purchase_nature_of_supply : "-");
                    $nestedData['reference_number'] = $post->purchase_supplier_invoice_number ? $post->purchase_supplier_invoice_number : "-";
                    $nestedData['place_of_supply'] = $post->place_of_supply ? $post->place_of_supply : "Outside India";
                    $nestedData['purchase_gst_payable'] = ucwords($post->purchase_gst_payable);
                    $purchase_supplier_date = $post->purchase_supplier_date;
                    if ($purchase_supplier_date == "1970-01-01" || $purchase_supplier_date == "0000-00-00") {
                        $nestedData['purchase_supplier_date'] = "-";
                    } else {
                        $nestedData['purchase_supplier_date'] = date('d-m-Y', strtotime($purchase_supplier_date));
                    }
                    $purchase_order_date = $post->purchase_order_date;
                    if ($purchase_order_date == "1970-01-01" || $purchase_order_date == "0000-00-00") {
                        $nestedData['purchase_order_date'] = "-";
                    } else {
                        $nestedData['purchase_order_date'] = date('d-m-Y', strtotime($purchase_order_date));
                    }
                    $nestedData['purchase_order_number'] = $post->purchase_order_number ? $post->purchase_order_number : "-";
                    $nestedData['delivery_challan_number'] = $post->purchase_delivery_challan_number ? $post->purchase_delivery_challan_number : "-";
                    $nestedData['e_way_bill_number'] = $post->purchase_e_way_bill_number ? $post->purchase_e_way_bill_number : '-';
                    $nestedData['purchase_taxable_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->purchase_taxable_value, 2);
                    $nestedData['purchase_cgst_amount'] = $this->precise_amount($post->purchase_cgst_amount, 2);
                    $nestedData['purchase_sgst_amount'] = $this->precise_amount($post->purchase_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->purchase_sgst_amount, 2);
                        $nestedData['purchase_sgst_amount'] = "0.00";
                    } else {
                        $nestedData['purchase_sgst_amount'] = $this->precise_amount($post->purchase_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }*/
                    $nestedData['purchase_igst_amount'] = $this->precise_amount($post->purchase_igst_amount, 2);

                    $nestedData['due'] = $post->due;


                    // if ($data['access_module_privilege']->add_privilege == "yes")
                    // {
                    //     if ($post->purchase_paid_amount < ($post->purchase_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                    //     {
                    //         $cols = '<ul class="list-inline"> <li>                        <a href="' . base_url('receipt_voucher/add_sales_receipt/') . $post->purchase_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                        </li>';
                    //     }
                    // }
                    // $cols                  .= '<li> <a href="#" data-toggle="modal" onclick="addToModel(' . $post->purchase_id . ')" data-target="#myModal"><i class="fa fa-eye text-orange"></i> Follow Up dates</a> </li></ul>';
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }
                    // $cols                 .= '</ul>';
                    // $nestedData['action'] = $cols;
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->distinct_supplier();
            $data['suppliers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_supplier_invoice();
            $data['supplier_invoice'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_nature_of_supply_purchase();
            $data['nature_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_place_of_supply_purchase();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_on_reverse_charge_purchase();
            $data['gst_payable'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_order_number();
            $data['order_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_delivery_challan_number();
            $data['delivery_challen_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_e_way();
            $data['purchase_e_way'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_cgst();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_sgst();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_utgst();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_igst();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            /* print_r($data['order_number']);
              exit(); */





            //////////////////////////////////////////////////////////
            $list_data = $this->common->distinct_purchase_invoice_amount();
            $data['invoice_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_purchase_received_amount();
            $data['received_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_purchase_pending_amount();
            $data['pending_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_purchase_due_day();
            $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_currency_converted_records('purchase');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->sum_purchase();
            $data['total_sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');
            $list_data = $this->common->purchase_converted_sum();
            $total_sum = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');
            // print_r($total_sum );die;
            $ar = [];
            foreach ($total_sum as $key) {
                if ($key->converted_grand_total == 0) {
                    $ar[] = $key->purchase_grand_total + $key->credit_note_amount;
                } else {
                    $ar[] = $key->converted_grand_total + $key->converted_credit_note_amount;
                }
            }
            $data['converted_sum'] = array_sum($ar);

            $this->load->view('reports/purchase_test', $data);
        }
    }

    public function expense_bill_report() {
        $expense_bill_report_module_id = $this->config->item('expense_bill_report_module');
        $data['module_id'] = $expense_bill_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($expense_bill_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $expense_bill_module_id = $this->config->item('expense_bill_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'receivable_date',
                8 => 'due'
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->expense_bill_list_field();
            $list_data['where'] = array(
                'e.delete_status' => 0,
                'e.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'e.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'e.expense_bill_grand_total !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'expense_bill';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);

            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_supplier_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_received_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "" || $this->input->post('filter_type_of_expence') != "" || $this->input->post('filter_supplier_invoice_number') != "" || $this->input->post('filter_from_tds') != "" || $this->input->post('filter_to_tds') != "" || $this->input->post('filter_cgst_from') != "" || $this->input->post('filter_cgst_to') != "" || $this->input->post('filter_sgst_from') != "" || $this->input->post('filter_sgst_to') != "" || $this->input->post('filter_utgst_from') != "" || $this->input->post('filter_utgst_to') != "" || $this->input->post('filter_igst_from') != "" || $this->input->post('filter_igst_to') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_due_days') != "" || $this->input->post('filter_to_due_days') != "" || $this->input->post('filter_from_invoice_amount') != "" || $this->input->post('filter_to_invoice_amount') != "") {
                $filter_search = array();
                $filter_search['filter_supplier_name'] = ($this->input->post('filter_supplier_name') == '' ? '' : implode(",", $this->input->post('filter_supplier_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_type_of_expence'] = ($this->input->post('filter_type_of_expence') == '' ? '' : implode(",", $this->input->post('filter_type_of_expence')));
                $filter_search['filter_supplier_invoice_number'] = ($this->input->post('filter_supplier_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_supplier_invoice_number')));
                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_invoice_amount'] = $this->input->post('filter_from_invoice_amount');
                $filter_search['filter_to_invoice_amount'] = $this->input->post('filter_to_invoice_amount');
                $filter_search['filter_tds'] = ($this->input->post('filter_tds') == '' ? '' : implode(",", $this->input->post('filter_tds')));
                /*$filter_search['filter_cgst'] = ($this->input->post('filter_cgst') == '' ? '' : implode(",", $this->input->post('filter_cgst')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));
                $filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));
                $filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));*/
                $filter_search['filter_cgst_from_expence'] = $this->input->post('filter_cgst_from') ?: 0;
                $filter_search['filter_cgst_to_expence'] = $this->input->post('filter_cgst_to') ?: 0;
                $filter_search['filter_sgst_from_expence'] = $this->input->post('filter_sgst_from') ?: 0;
                $filter_search['filter_sgst_to_expence'] = $this->input->post('filter_sgst_to') ?: 0;
                $filter_search['filter_utgst_from_expence'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_expence'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['filter_igst_from_expence'] = $this->input->post('filter_igst_from') ?: 0;
                $filter_search['filter_igst_to_expence'] = $this->input->post('filter_igst_to') ?: 0;
                $filter_search['filter_tds_from_expence'] = $this->input->post('filter_from_tds') ?: 0;
                $filter_search['filter_tds_to_expence'] = $this->input->post('filter_to_tds') ?: 0;
                $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount')));
                $filter_search['filter_received_amount'] = ($this->input->post('filter_received_amount') == '' ? '' : implode(",", $this->input->post('filter_received_amount')));
                $filter_search['filter_payment_status'] = ($this->input->post('filter_payment_status') == '' ? '' : implode(",", $this->input->post('filter_payment_status')));
                $filter_search['filter_pending_amount'] = ($this->input->post('filter_pending_amount') == '' ? '' : implode(",", $this->input->post('filter_pending_amount')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_ex_from_due_day'] = $this->input->post('filter_from_due_days') ?: 0;
                $filter_search['filter_ex_to_due_day'] = $this->input->post('filter_to_due_days') ?: 0;
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $string = "sum(t.expense_bill_item_taxable_value) as tot_expense_bill_tax_amount, sum(t.expense_bill_item_igst_amount) as tot_expense_bill_igst_amount, sum(t.expense_bill_item_cgst_amount) as tot_expense_bill_cgst_amount, sum(t.sgst) as tot_expense_bill_sgst_amount,sum(t.utgst) as tot_expense_bill_utgst_amount, sum(t.expense_bill_item_grand_total) as tot_expense_bill_grand_total, sum(t.expense_bill_item_tds_amount) as tot_expense_bill_tds_amount, (sum(t.expense_bill_grand_total)-sum(t.expense_bill_paid_amount)) as tot_expense_bill_pending_amount";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->expense_bill_date . ' + 15 days'));
                    $expense_bill_id = $this->encryption_url->encode($post->expense_bill_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->expense_bill_date));
                    $nestedData['invoice'] = $post->expense_bill_invoice_number;
                    if(in_array($expense_bill_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('expense_bill/view/') . $expense_bill_id . '">' . $post->expense_bill_invoice_number . '</a>';
                    }
                    $nestedData['supplier'] = $post->supplier_name;
                    $nestedData['grand_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->expense_bill_item_grand_total, 2);
                    
                    $nestedData['pending_amount'] = $this->precise_amount($post->expense_bill_grand_total - $post->expense_bill_paid_amount, 2);
                    $nestedData['receivable_date'] = date('d-m-Y', strtotime($post->receivable_date));
                    $nestedData['due'] = $post->due;
                    $nestedData['paid_amount'] = $this->precise_amount($post->expense_bill_paid_amount, 2);
                    if ($post->expense_bill_paid_amount == 0) {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    } elseif (($post->expense_bill_paid_amount) < ($post->expense_bill_grand_total)) {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    } else {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }
                    $nestedData['supplier_invoice_number'] = $post->expense_bill_supplier_invoice_number ? $post->expense_bill_supplier_invoice_number : "-";
                    $nestedData['tds_amount'] = $this->precise_amount($post->expense_bill_item_tds_amount, 2);
                    $nestedData['taxable_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->expense_bill_item_taxable_value, 2);
                    $nestedData['cgst_amount'] = $this->precise_amount($post->expense_bill_item_cgst_amount, 2);
                    $nestedData['sgst_amount'] = $this->precise_amount($post->expense_bill_item_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->expense_bill_item_sgst_amount, 2);
                        $nestedData['sgst_amount'] = "0.00";
                    } else {
                        $nestedData['sgst_amount'] = $this->precise_amount($post->expense_bill_item_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }*/
                    $nestedData['igst_amount'] = $this->precise_amount($post->expense_bill_item_igst_amount, 2);
                    $nestedData['expense_title'] = $post->expense_title ? $post->expense_title : "-";
                    // if ($data['access_module_privilege']->add_privilege == "yes")
                    // {
                    //     if ($post->converted_paid_amount < ($post->currency_converted_amount))
                    //     {
                    //         $cols = '<ul class="list-inline"> <li>                        <a href="' . base_url('receipt_voucher/add_sales_receipt/') . $post->expense_bill_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                        </li>';
                    //     }
                    // }
                    // $cols                  .= '<li> <a href="#" data-toggle="modal" onclick="addToModel(' . $post->expense_bill_id . ')" data-target="#myModal"><i class="fa fa-eye text-orange"></i> Follow Up dates</a> </li></ul>';
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }
                    // $cols                 .= '</ul>';
                    // $nestedData['action'] = $cols;
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "total_list_record" => $total_list_record,
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->distinct_supplier_expense_bill();
            $data['suppliers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_bill_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_bill_type();
            $data['type_of_expence'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_supplier_invoice_number();
            $data['supplier_invoice'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_tds();
            $data['tds'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_cgst();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_sgst();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_utgst();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_expense_igst();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            /*
              $list_data = $this->common->distinct_expense_bill_received_amount();
              $data['received_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
              $list_data = $this->common->distinct_expense_bill_pending_amount();
              $data['pending_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
              $list_data = $this->common->distinct_sales_due_day();
              $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']); */

            $list_data = $this->common->distinct_currency_converted_records('expense_bill');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->sum_expense_bill();
            $data['total_sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');


            $list_data = $this->common->sum_expense_sum();
            $total_sum = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group'] = '');


            $ar = [
            ];
            foreach ($total_sum as $key) {
                if ($key->currency_converted_amount == '0') {
                    $ar[] = $key->expense_bill_grand_total;
                } else {
                    $ar[] = $key->currency_converted_amount;
                }
            }

            $data['total_converted_amount'] = array_sum($ar);


            $this->load->view('reports/expense_bill_report', $data);
        }
    }

    public function credit_note_report() {
        $sales_credit_note_report_module_id = $this->config->item('sales_credit_note_report_module');
        $data['module_id'] = $sales_credit_note_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_credit_note_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $sales_module_id = $this->config->item('sales_module');
        $sales_credit_note_module_id = $this->config->item('sales_credit_note_module');
        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'receivable_date',
                8 => 'due',
                9 => 'action',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->sales_credit_note_list_field();
            $list_data['search'] = 'all';
            $list_data['section'] = 'credit_note';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_customer_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_received_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "" || $this->input->post('filter_tax') != "" || $this->input->post('filter_cgst_from') != "" || $this->input->post('filter_cgst_to') != "" || $this->input->post('filter_sgst_from') != "" || $this->input->post('filter_sgst_to') != "" || $this->input->post('filter_utgst_from') != "" || $this->input->post('filter_utgst_to') != "" || $this->input->post('filter_igst_from') != "" || $this->input->post('filter_igst_to') != "" || $this->input->post('filter_reference_number') != "" || $this->input->post('filter_nature_of_supply') != "" || $this->input->post('filter_billing_name') != "" || $this->input->post('filter_shipping_to') != "" || $this->input->post('filter_customer_billing_name') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_type_of_supply') != "" || $this->input->post('filter_gst_payable') != "" || $this->input->post('filter_billing_currency') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_invoice_amount') != "" || $this->input->post('filter_to_invoice_amount') != "" /*|| $this->input->post('filter_utgst') != ""*/) {
                $filter_search = array();
                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_reference_number'] = ($this->input->post('filter_reference_number') == '' ? '' : implode(",", $this->input->post('filter_reference_number')));
                /*$filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));*/
                $filter_search['filter_nature_of_supply'] = ($this->input->post('filter_nature_of_supply') == '' ? '' : implode(",", $this->input->post('filter_nature_of_supply')));
                $filter_search['filter_customer_billing_name'] = ($this->input->post('filter_customer_billing_name') == '' ? '' : implode(",", $this->input->post('filter_customer_billing_name')));
                $filter_search['filter_billing_name'] = ($this->input->post('filter_billing_name') == '' ? '' : implode(",", $this->input->post('filter_billing_name')));
                $filter_search['filter_shipping_to'] = ($this->input->post('filter_shipping_to') == '' ? '' : implode(",", $this->input->post('filter_shipping_to')));
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_type_of_supply'] = ($this->input->post('filter_type_of_supply') == '' ? '' : implode(",", $this->input->post('filter_type_of_supply')));

                $filter_search['filter_gst_payable'] = ($this->input->post('filter_gst_payable') == '' ? '' : implode(",", $this->input->post('filter_gst_payable')));

                $filter_search['filter_billing_currency'] = ($this->input->post('filter_billing_currency') == '' ? '' : implode(",", $this->input->post('filter_billing_currency')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_cgst_from_cn'] = $this->input->post('filter_cgst_from') ?: 0;
                $filter_search['filter_cgst_to_cn'] = $this->input->post('filter_cgst_to') ?: 0;
                $filter_search['filter_sgst_from_cn'] = $this->input->post('filter_sgst_from') ?: 0;
                $filter_search['filter_sgst_to_cn'] = $this->input->post('filter_sgst_to') ?: 0;
                $filter_search['filter_utgst_from_cn'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_cn'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['filter_igst_from_cn'] = $this->input->post('filter_igst_from') ?: 0;
                $filter_search['filter_igst_to_cn'] = $this->input->post('filter_igst_to') ?: 0;
                /*$filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));
                $filter_search['filter_cgst'] = ($this->input->post('filter_cgst') == '' ? '' : implode(",", $this->input->post('filter_cgst')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));*/
                $filter_search['filter_from_taxable_amount'] = ($this->input->post('filter_from_taxable_amount'));
                $filter_search['filter_to_taxable_amount'] = ($this->input->post('filter_to_taxable_amount'));
                $filter_search['filter_from_invoice_amount'] = ($this->input->post('filter_from_invoice_amount'));
                $filter_search['filter_to_invoice_amount'] = ($this->input->post('filter_to_invoice_amount'));
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.sales_credit_note_taxable_value) as tot_sales_cn_taxable_value, sum(t.sales_credit_note_cgst_amount) as tot_sales_cn_cgst_amount, sum(t.sgst) as tot_sales_cn_sgst_amount, sum(t.utgst) as tot_sales_cn_utgst_amount, sum(t.sales_credit_note_igst_amount) as tot_sales_cn_igst_amount, sum(t.sales_credit_note_grand_total) as tot_sales_cn_total_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->sales_credit_note_date . ' + 15 days'));
                    $sales_credit_note_id = $this->encryption_url->encode($post->sales_credit_note_id); 
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->sales_credit_note_date));
                    $nestedData['invoice'] = $post->sales_credit_note_invoice_number;
                    if(in_array($sales_credit_note_module_id, $data['active_view'])){
                        $nestedData['invoice'] = '<a href="' . base_url('sales_credit_note/view/') . $sales_credit_note_id . '">' . $post->sales_credit_note_invoice_number . '</a>';
                    }
                    $nestedData['supplier'] = $post->customer_name;
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $this->precise_amount($post->sales_credit_note_grand_total, 2);
                    $nestedData['taxable'] = $post->currency_symbol .' '.$this->precise_amount($post->sales_credit_note_taxable_value, 2);
                    $nestedData['cgst'] = $this->precise_amount($post->sales_credit_note_cgst_amount, 2);
                    $nestedData['sgst'] = $this->precise_amount($post->sales_credit_note_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->sales_credit_note_sgst_amount, 2);
                        $nestedData['sgst'] = "0.00";
                    } else {
                        $nestedData['sgst'] = $this->precise_amount($post->sales_credit_note_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }*/
                    $nestedData['igst'] = $this->precise_amount($post->sales_credit_note_igst_amount, 2);
                    $nestedData['from_account'] = $post->from_account;
                    $nestedData['nature_of_supply'] = ucwords($post->sales_credit_note_nature_of_supply);
                    $nestedData['gst_payable'] = ucwords($post->sales_credit_note_gst_payable);
                    $nestedData['Sales_invoice_number'] = $post->sales_invoice_number;
                    if(in_array($sales_module_id, $data['active_view'])){
                        $nestedData['Sales_invoice_number'] = '<a href="' . base_url('sales/view/') . $sales_id  . '">' . $post->sales_invoice_number . '</a>';
                    }
                    $nestedData['credit_note_type_of_supply'] = $post->sales_credit_note_type_of_supply;
                    $nestedData['country_name'] = $post->country_name ? $post->country_name : "-";
                    $nestedData['bill_to_name'] = $post->customer_name;
                    $nestedData['billing_address'] = $post->billing_address ? $post->billing_address : "-";
                    $nestedData['shipping_name'] = $post->ship_to_name ? $post->ship_to_name : "-";
                    $nestedData["shipping_address"] = $post->shipping_address ? $post->shipping_address : "-";
                    $nestedData["place_of_supply"] = $post->state_name ? $post->state_name : "Outside India";
                    $nestedData["sales_type_of_supply"] = ucwords($post->sales_credit_note_type_of_supply);
                    $nestedData["sales_type_of_supply"] = str_replace("_", ' ', $nestedData["sales_type_of_supply"]);
                    $nestedData["billing_currency"] = $post->currency_name;
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();

            $list_data = $this->common->distinct_cn_customer();
            $data['customer'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_reference_number();
            $data['reference_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_nature_of_supply();
            $data['nature_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_billing_country();
            $data['billing_country'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_type_of_supply();
            $data['type_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_place_of_supply();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_bill_to_name();
            $data['bill_to_name'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_ship_to_name();
            $data['shipping_name'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_billing_currency();
            $data['billing_currency'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_cgst();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_sgst();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_utgst();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_credit_note_igst();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            /* $list_data = $this->common->distinct_credit_note_amount();
              $data['invoice_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']); */

            $list_data = $this->common->distinct_purchase_pending_amount();
            $data['pending_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_due_day();
            $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_from_customer_credit_note();
            $data['from_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_taxable_customer_credit_note();
            $data['taxable'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');


            $list_data = $this->common->credit_note_sum();
            $data['sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');


            $this->load->view('reports/credit_note_report', $data);
        }
    }

    public function debit_note_report() {
        $sales_debit_note_report_module_id = $this->config->item('sales_debit_note_report_module');
        $data['module_id'] = $sales_debit_note_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_debit_note_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $sales_module_id = $this->config->item('sales_module');
        $sales_debit_note_module_id = $this->config->item('sales_debit_note_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        // $default_date      = $section_modules['common_settings'][0]->default_notification_date;
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'receivable_date',
                8 => 'due',
                9 => 'action',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->sales_debit_note_list_field();
            $list_data['search'] = 'all';
            $list_data['section'] = 'debit_note';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_customer_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_received_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "" || $this->input->post('filter_tax') != "" ||$this->input->post('filter_cgst_from') != "" || $this->input->post('filter_cgst_to') != "" || $this->input->post('filter_sgst_from') != "" || $this->input->post('filter_sgst_to') != "" || $this->input->post('filter_utgst_from') != "" || $this->input->post('filter_utgst_to') != "" || $this->input->post('filter_igst_from') != "" || $this->input->post('filter_igst_to') != "" || $this->input->post('filter_reference_number') != "" || $this->input->post('filter_nature_of_supply') != "" || $this->input->post('filter_billing_name') != "" || $this->input->post('filter_shipping_to') != "" || $this->input->post('filter_customer_billing_name') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_type_of_supply') != "" || $this->input->post('filter_gst_payable') != "" || $this->input->post('filter_billing_currency') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_invoice_amount') != "" || $this->input->post('filter_to_invoice_amount') != "") {
                $filter_search = array();
                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_reference_number'] = ($this->input->post('filter_reference_number') == '' ? '' : implode(",", $this->input->post('filter_reference_number')));
                $filter_search['filter_nature_of_supply'] = ($this->input->post('filter_nature_of_supply') == '' ? '' : implode(",", $this->input->post('filter_nature_of_supply')));
                $filter_search['filter_customer_billing_name'] = ($this->input->post('filter_customer_billing_name') == '' ? '' : implode(",", $this->input->post('filter_customer_billing_name')));
                $filter_search['filter_billing_name'] = ($this->input->post('filter_billing_name') == '' ? '' : implode(",", $this->input->post('filter_billing_name')));
                $filter_search['filter_shipping_to'] = ($this->input->post('filter_shipping_to') == '' ? '' : implode(",", $this->input->post('filter_shipping_to')));
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_type_of_supply'] = ($this->input->post('filter_type_of_supply') == '' ? '' : implode(",", $this->input->post('filter_type_of_supply')));

                $filter_search['filter_gst_payable'] = ($this->input->post('filter_gst_payable') == '' ? '' : implode(",", $this->input->post('filter_gst_payable')));

                $filter_search['filter_billing_currency'] = ($this->input->post('filter_billing_currency') == '' ? '' : implode(",", $this->input->post('filter_billing_currency')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                /*$filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));
                $filter_search['filter_cgst'] = ($this->input->post('filter_cgst') == '' ? '' : implode(",", $this->input->post('filter_cgst')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));
                $filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));*/
                $filter_search['filter_cgst_from_dn'] = $this->input->post('filter_cgst_from') ?: 0;
                $filter_search['filter_cgst_to_dn'] = $this->input->post('filter_cgst_to') ?: 0;
                $filter_search['filter_sgst_from_dn'] = $this->input->post('filter_sgst_from') ?: 0;
                $filter_search['filter_sgst_to_dn'] = $this->input->post('filter_sgst_to') ?: 0;
                $filter_search['filter_utgst_from_dn'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_dn'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['filter_igst_from_dn'] = $this->input->post('filter_igst_from') ?: 0;
                $filter_search['filter_igst_to_dn'] = $this->input->post('filter_igst_to') ?: 0;
                $filter_search['filter_from_taxable_amount'] = ($this->input->post('filter_from_taxable_amount'));
                $filter_search['filter_to_taxable_amount'] = ($this->input->post('filter_to_taxable_amount'));
                $filter_search['filter_from_invoice_amount'] = ($this->input->post('filter_from_invoice_amount'));
                $filter_search['filter_to_invoice_amount'] = ($this->input->post('filter_to_invoice_amount'));
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.sales_debit_note_taxable_value) as tot_sales_dn_taxable_value, sum(t.sales_debit_note_cgst_amount) as tot_sales_dn_cgst_amount, sum(t.sgst) as tot_sales_dn_sgst_amount,sum(t.utgst) as tot_sales_dn_utgst_amount, sum(t.sales_debit_note_igst_amount) as tot_sales_dn_igst_amount, sum(t.sales_debit_note_grand_total) as tot_sales_dn_total_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            //
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->sales_debit_note_date . ' + 15 days'));
                    $sales_debit_note_id = $this->encryption_url->encode($post->sales_debit_note_id);
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->sales_debit_note_date));
                    $nestedData['invoice'] = $post->sales_debit_note_invoice_number;
                    if(in_array($sales_debit_note_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('sales_debit_note/view/') . $sales_debit_note_id . '">' . $post->sales_debit_note_invoice_number . '</a>';
                    }
                    $nestedData['customer'] = $post->customer_name;
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $this->precise_amount($post->sales_debit_note_grand_total, 2);
                    $nestedData['pending_amount'] = $post->sales_debit_note_type_of_supply;
                    $nestedData['payment_status'] = $post->sales_debit_note_type_of_supply;
                    $nestedData['gst_payable'] = ucwords($post->sales_debit_note_gst_payable);
                    $nestedData['sales_invoice_number'] = $post->sales_invoice_number;
                    if(in_array($sales_module_id, $data['active_view'])){
                        $nestedData['sales_invoice_number'] = '<a href="' . base_url('sales/view/') . $sales_id  . '">' . $post->sales_invoice_number . '</a>';
                    }
                    $nestedData['taxable'] = $post->currency_symbol.' '.$this->precise_amount($post->sales_debit_note_taxable_value, 2);
                    $nestedData['cgst'] = $this->precise_amount($post->sales_debit_note_cgst_amount, 2);
                    if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->sales_debit_note_sgst_amount, 2);
                        $nestedData['sgst'] = "0.00";
                    } else {
                        $nestedData['sgst'] = $this->precise_amount($post->sales_debit_note_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }
                    $nestedData['igst'] = $this->precise_amount($post->sales_debit_note_igst_amount, 2);
                    $nestedData['from_account'] = $post->from_account;
                    $nestedData['nature_of_supply'] = ucwords($post->sales_debit_note_nature_of_supply);
                    $nestedData['country_name'] = $post->country_name ? $post->country_name : "-";
                    $nestedData['bill_to_name'] = $post->customer_name;
                    $nestedData['billing_address'] = $post->billing_address ? $post->billing_address : "-";
                    $nestedData['shipping_name'] = $post->ship_to_name ? $post->ship_to_name : "-";
                    $nestedData['shipping_address'] = $post->shipping_address ? $post->shipping_address : "-";
                    $nestedData['place_of_supply'] = $post->state_name ? $post->state_name : "Outside India";
                    $nestedData['type_of_supply'] = ucwords($post->sales_debit_note_type_of_supply ? $post->sales_debit_note_type_of_supply : "-");
                    $nestedData['type_of_supply'] = str_replace("_", ' ', $nestedData['type_of_supply']);
                    $nestedData['currency_name'] = $post->currency_name ? $post->currency_name : "-";
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;

                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->distinct_dn_customer();
            $data['customer'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_note_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_note_reference();
            $data['reference_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);


            $list_data = $this->common->distinct_debit_nature_of_supply();
            $data['nature_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_billing_country();
            $data['billing_country'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_type_of_supply();
            $data['type_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_place_of_supply();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_bill_to_name();
            $data['bill_to_name'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_ship_to_name();
            $data['shipping_name'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_billing_currency();
            $data['billing_currency'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_cgst();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_debit_utgst();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            // print_r($data['utgst']);
            // exit();
            $list_data = $this->common->distinct_debit_sgst();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_igst();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_note_amount();
            $data['invoice_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_debit_note_reference_number();
            $data['received_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->pyment_to_debit_note();
            $data['pyment_to_debit_note'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_pending_amount();
            $data['pending_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->pyment_to_debit_note_type_of_supply();
            $data['type_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_due_day();
            $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_taxable_customer_debit_note();
            $data['taxable'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');

            $list_data = $this->common->debit_note_sum();
            $data['sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');



            $this->load->view('reports/debit_note_report', $data);
        }
    }

    public function purchase_debit_note_report() {
        $purchase_debit_note_report_module_id = $this->config->item('purchase_debit_note_report_module');
        $data['module_id'] = $purchase_debit_note_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_debit_note_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $purchase_module_id = $this->config->item('purchase_module');
        $purchase_debit_note_module_id = $this->config->item('purchase_debit_note_module');
        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'nature_supply',
                7 => 'receivable_date',
                8 => 'taxable_value',
                9 => 'action',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->purchase_debit_note_list_field();
            $list_data['search'] = 'all';
            $list_data['section'] = 'purchase_debit_note';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_supplier_name') != "" ||
                    $this->input->post('filter_invoice_number') != "" ||
                    $this->input->post('filter_from_date') != "" ||
                    $this->input->post('filter_to_date') != "" ||
                    $this->input->post('filter_invoice_amount') != "" ||
                    $this->input->post('filter_reference_number') != "" ||
                    $this->input->post('filter_billing_country') != "" ||
                    $this->input->post('filter_nature_supply') != "" ||
                    $this->input->post('filter_receivable_date') != "" ||
                    $this->input->post('filter_taxable_value') != "" ||
                    $this->input->post('filter_cgst_from') != "" ||
                    $this->input->post('filter_cgst_to') != "" || 
                    $this->input->post('filter_sgst_from') != "" || 
                    $this->input->post('filter_sgst_to') != "" || 
                    $this->input->post('filter_utgst_from') != "" || 
                    $this->input->post('filter_utgst_to') != "" || 
                    $this->input->post('filter_igst_from') != "" || 
                    $this->input->post('filter_igst_to') != "" ||
                    /*$this->input->post('filter_cgst') != "" ||
                    $this->input->post('filter_sgst') != "" ||
                    $this->input->post('filter_igst') != "" ||*/
                    $this->input->post('filter_gst_payable') != "" ||
                    $this->input->post('filter_debit_note_amount') != "" ||
                    $this->input->post('filter_place_of_supply') != "" ||
                    $this->input->post('filter_type_of_supply') != "" ||
                    $this->input->post('filter_supplier_cn_number') != "" ||
                    $this->input->post('filter_supplier_cn_from') != "" ||
                    $this->input->post('filter_supplier_cn_to') != "" ||
                    $this->input->post('filter_from_taxable_amount') != "" ||
                    $this->input->post('filter_to_taxable_amount') != "" ||
                    $this->input->post('filter_from_invoice_amount') != "" ||
                    $this->input->post('filter_to_invoice_amount') != "") {
                $filter_search = array();
                $filter_search['filter_supplier_name'] = ($this->input->post('filter_supplier_name') == '' ? '' : implode(",", $this->input->post('filter_supplier_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_reference_number'] = ($this->input->post('filter_reference_number') == '' ? '' : implode(",", $this->input->post('filter_reference_number')));
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_type_of_supply'] = ($this->input->post('filter_type_of_supply') == '' ? '' : implode(",", $this->input->post('filter_type_of_supply')));
                $filter_search['filter_supplier_cn_number'] = ($this->input->post('filter_supplier_cn_number') == '' ? '' : implode(",", $this->input->post('filter_supplier_cn_number')));
                $filter_search['filter_supplier_cn_from'] = ($this->input->post('filter_supplier_cn_from') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_supplier_cn_from'))));
                $filter_search['filter_supplier_cn_to'] = ($this->input->post('filter_supplier_cn_to') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_supplier_cn_to'))));
                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_invoice_amount'] = $this->input->post('filter_from_invoice_amount');
                $filter_search['filter_to_invoice_amount'] = $this->input->post('filter_to_invoice_amount');
                /* $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount'))); */

                $filter_search['filter_billing_country'] = ($this->input->post('filter_billing_country') == '' ? '' : implode(",", $this->input->post('filter_billing_country')));
                $filter_search['filter_nature_supply'] = ($this->input->post('filter_nature_supply') == '' ? '' : implode(",", $this->input->post('filter_nature_supply')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_taxable_value'] = ($this->input->post('filter_taxable_value') == '' ? '' : implode(",", $this->input->post('filter_taxable_value')));
                /*$filter_search['filter_cgst'] = ($this->input->post('filter_cgst') == '' ? '' : implode(",", $this->input->post('filter_cgst')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));
                $filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));
                $filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));*/
                $filter_search['filter_cgst_from_purchase_dn'] = $this->input->post('filter_cgst_from') ?: 0;
                $filter_search['filter_cgst_to_purchase_dn'] = $this->input->post('filter_cgst_to') ?: 0;
                $filter_search['filter_sgst_from_purchase_dn'] = $this->input->post('filter_sgst_from') ?: 0;
                $filter_search['filter_sgst_to_purchase_dn'] = $this->input->post('filter_sgst_to') ?: 0;
                $filter_search['filter_utgst_from_purchase_dn'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_purchase_dn'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['filter_igst_from_purchase_dn'] = $this->input->post('filter_igst_from') ?: 0;
                $filter_search['filter_igst_to_purchase_dn'] = $this->input->post('filter_igst_to') ?: 0;
                $filter_search['filter_gst_payable'] = ($this->input->post('filter_gst_payable') == '' ? '' : implode(",", $this->input->post('filter_gst_payable')));
                $filter_search['filter_debit_note_amount'] = ($this->input->post('filter_debit_note_amount') == '' ? '' : implode(",", $this->input->post('filter_debit_note_amount')));
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.purchase_debit_note_taxable_value) as tot_purchase_dn_taxable_value, sum(t.purchase_debit_note_cgst_amount) as tot_purchase_dn_cgst_amount, sum(t.sgst) as tot_purchase_dn_sgst_amount,sum(t.utgst) as tot_purchase_dn_utgst_amount, sum(t.purchase_debit_note_igst_amount) as tot_purchase_dn_igst_amount, sum(t.purchase_debit_note_grand_total) as tot_purchase_dn_total_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->purchase_debit_note_date . ' + 15 days'));
                    $purchase_debit_note_id = $this->encryption_url->encode($post->purchase_debit_note_id);
                    $purchase_id = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->purchase_debit_note_date));
                    $nestedData['invoice'] = $post->purchase_debit_note_invoice_number;
                    if(in_array($purchase_debit_note_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('purchase_debit_note/view/') . $purchase_debit_note_id . '">' . $post->purchase_debit_note_invoice_number . '</a>';
                    }
                    $nestedData['supplier'] = $post->supplier_name;
                    $nestedData['grand_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' .$this->precise_amount($post->purchase_debit_note_grand_total, 2);
                    $nestedData['pending_amount'] = $post->purchase_debit_note_nature_of_supply;
                    $nestedData['purchase_invoice_number'] = $post->purchase_invoice_number;
                    if(in_array($purchase_module_id, $data['active_view'])){
                        $nestedData['purchase_invoice_number'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                    }
                    $nestedData['filter_billing_country'] = $post->billing_country;
                    $nestedData['taxable'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') . ' ' .$this->precise_amount($post->purchase_debit_note_taxable_value, 2);
                    $nestedData['cgst'] = $this->precise_amount($post->purchase_debit_note_cgst_amount, 2);
                    $nestedData['sgst'] = $this->precise_amount($post->purchase_debit_note_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->purchase_debit_note_sgst_amount, 2);
                        $nestedData['sgst'] = "0.00";
                    } else {
                        $nestedData['sgst'] = $this->precise_amount($post->purchase_debit_note_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }*/
                    $nestedData['igst'] = $this->precise_amount($post->purchase_debit_note_igst_amount, 2);
                    $nestedData['to_account'] = $post->to_account;
                    $nestedData['nature_supply'] = ucwords($post->purchase_debit_note_nature_of_supply);
                    $nestedData['debit_note_gst_payable'] = ucwords($post->purchase_debit_note_gst_payable);
                    $nestedData['debit_note_amount'] = $this->precise_amount($post->purchase_debit_note_grand_total, 2);
                    $nestedData['billing_country'] = $post->billing_country;
                    $nestedData['place_of_supply'] = $post->place_of_supply ? $post->place_of_supply : "Outside India";
                    $nestedData['type_of_supply'] = ucwords($post->purchase_debit_note_type_of_supply);
                    $nestedData['type_of_supply'] = str_replace("_", ' ', $nestedData['type_of_supply']);
                    $nestedData['supplier_credit_note_number'] = $post->purchase_supplier_debit_note_number ? $post->purchase_supplier_debit_note_number : "-";
                    $supplier_debit_note_date = date('d-m-Y', strtotime($post->purchase_supplier_debit_note_date));
                    if ($supplier_debit_note_date == "01-01-1970" || $supplier_debit_note_date == "0000-00-00") {
                        $nestedData['supplier_debit_note_date'] = "-";
                    } else {
                        $nestedData['supplier_debit_note_date'] = $supplier_debit_note_date;
                    }
                    $nestedData['currency_name'] = $post->currency_name;
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;

                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->distinct_dn_supplier();
            $data['suppliers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_debit_note_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            /* $list_data = $this->common->distinct_purchase_debit_note_invoice_amount();
              $data['invoice_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']); */
            $list_data = $this->common->distinct_purchase_debit_note_purchase_reference();
            $data['purchase_reference'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_debit_note_billing_country();
            $data['billing_country'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_debit_note_nature_supply();
            $data['nature_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_debit_note_place_of_supply();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_debit_note_type_of_supply();
            $data['type_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_debit_note_igst_value();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_debit_note_cgst_value();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);


            $list_data = $this->common->distinct_purchase_debit_note_sgst_value();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_purchase_debit_note_utgst_value();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_credit_note_number();
            $data['credit_note_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_purchase_debit_note_taxable_value();
            $data['taxable_value'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_purchase_debit_note_tax_value();
            $data['tax'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);


            $list_data = $this->common->distinct_purchase_debit_note_amount();
            $data['debit_note_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_debit_sum();
            $data['sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group'] = '');



            $this->load->view('reports/purchase_debit_note_report', $data);
        }
    }

    public function purchase_credit_note_report() {
        $purchase_credit_note_report_module_id = $this->config->item('purchase_credit_note_report_module');
        $data['module_id'] = $purchase_credit_note_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($purchase_credit_note_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $purchase_module_id = $this->config->item('purchase_module');
        $purchase_credit_note_module_id = $this->config->item('purchase_credit_note_module');
        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'receivable_date',
                8 => 'due',
                9 => 'action',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->purchase_credit_note_list_field();
            $list_data['search'] = 'all';
            $list_data['section'] = 'purchase_credit_note';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_supplier_name') != "" ||
                    $this->input->post('filter_invoice_number') != "" ||
                    $this->input->post('filter_from_date') != "" ||
                    $this->input->post('filter_to_date') != "" ||
                    $this->input->post('filter_invoice_amount') != "" ||
                    $this->input->post('filter_reference_number') != "" ||
                    $this->input->post('filter_billing_country') != "" ||
                    $this->input->post('filter_nature_supply') != "" ||
                    $this->input->post('filter_receivable_date') != "" ||
                    $this->input->post('filter_taxable_value') != "" ||
                    /*$this->input->post('filter_cgst') != "" ||
                    $this->input->post('filter_sgst') != "" ||
                    $this->input->post('filter_igst') != "" ||*/
                    $this->input->post('filter_cgst_from') != "" || 
                    $this->input->post('filter_cgst_to') != "" || 
                    $this->input->post('filter_sgst_from') != "" || 
                    $this->input->post('filter_sgst_to') != "" || 
                    $this->input->post('filter_utgst_from') != "" || 
                    $this->input->post('filter_utgst_to') != "" || 
                    $this->input->post('filter_igst_from') != "" || 
                    $this->input->post('filter_igst_to') != "" ||
                    $this->input->post('filter_gst_payable') != "" ||
                    $this->input->post('filter_debit_note_amount') != "" ||
                    $this->input->post('filter_place_of_supply') != "" ||
                    $this->input->post('filter_type_of_supply') != "" ||
                    $this->input->post('filter_supplier_cn_number') != "" ||
                    $this->input->post('filter_supplier_cn_from') != "" ||
                    $this->input->post('filter_supplier_cn_to') != "" ||
                    $this->input->post('filter_from_taxable_amount') != "" ||
                    $this->input->post('filter_to_taxable_amount') != "" ||
                    $this->input->post('filter_from_invoice_amount') != "" ||
                    $this->input->post('filter_to_invoice_amount') != "") {
                $filter_search = array();
                $filter_search['filter_supplier_name'] = ($this->input->post('filter_supplier_name') == '' ? '' : implode(",", $this->input->post('filter_supplier_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_reference_number'] = ($this->input->post('filter_reference_number') == '' ? '' : implode(",", $this->input->post('filter_reference_number')));
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_type_of_supply'] = ($this->input->post('filter_type_of_supply') == '' ? '' : implode(",", $this->input->post('filter_type_of_supply')));
                $filter_search['filter_supplier_cn_number'] = ($this->input->post('filter_supplier_cn_number') == '' ? '' : implode(",", $this->input->post('filter_supplier_cn_number')));
                $filter_search['filter_supplier_cn_from'] = ($this->input->post('filter_supplier_cn_from') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_supplier_cn_from'))));
                $filter_search['filter_supplier_cn_to'] = ($this->input->post('filter_supplier_cn_to') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_supplier_cn_to'))));
                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_invoice_amount'] = $this->input->post('filter_from_invoice_amount');
                $filter_search['filter_to_invoice_amount'] = $this->input->post('filter_to_invoice_amount');

                $filter_search['filter_billing_country'] = ($this->input->post('filter_billing_country') == '' ? '' : implode(",", $this->input->post('filter_billing_country')));
                $filter_search['filter_nature_supply'] = ($this->input->post('filter_nature_supply') == '' ? '' : implode(",", $this->input->post('filter_nature_supply')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_taxable_value'] = ($this->input->post('filter_taxable_value') == '' ? '' : implode(",", $this->input->post('filter_taxable_value')));
                /*$filter_search['filter_cgst'] = ($this->input->post('filter_cgst') == '' ? '' : implode(",", $this->input->post('filter_cgst')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));
                $filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));
                $filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));*/
                $filter_search['filter_cgst_from_purchase_cn'] = $this->input->post('filter_cgst_from') ?: 0;
                $filter_search['filter_cgst_to_purchase_cn'] = $this->input->post('filter_cgst_to') ?: 0;
                $filter_search['filter_sgst_from_purchase_cn'] = $this->input->post('filter_sgst_from') ?: 0;
                $filter_search['filter_sgst_to_purchase_cn'] = $this->input->post('filter_sgst_to') ?: 0;
                $filter_search['filter_utgst_from_purchase_cn'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_purchase_cn'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['filter_igst_from_purchase_cn'] = $this->input->post('filter_igst_from') ?: 0;
                $filter_search['filter_igst_to_purchase_cn'] = $this->input->post('filter_igst_to') ?: 0;
                $filter_search['filter_gst_payable'] = ($this->input->post('filter_gst_payable') == '' ? '' : implode(",", $this->input->post('filter_gst_payable')));
                $filter_search['filter_debit_note_amount'] = ($this->input->post('filter_debit_note_amount') == '' ? '' : implode(",", $this->input->post('filter_debit_note_amount')));

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.purchase_credit_note_taxable_value) as tot_purchase_cn_taxable_value, sum(t.purchase_credit_note_cgst_amount) as tot_purchase_cn_cgst_amount, sum(t.sgst) as tot_purchase_cn_sgst_amount, sum(t.utgst) as tot_purchase_cn_utgst_amount, sum(t.purchase_credit_note_igst_amount) as tot_purchase_cn_igst_amount, sum(t.purchase_credit_note_grand_total) as tot_purchase_cn_total_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->purchase_credit_note_date . ' + 15 days'));
                    $purchase_credit_note_id = $this->encryption_url->encode($post->purchase_credit_note_id);
                    $purchase_id = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->purchase_credit_note_date));
                    $nestedData['invoice'] = $post->purchase_credit_note_invoice_number;
                    if(in_array($purchase_credit_note_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('purchase_credit_note/view/') . $purchase_credit_note_id . '">' . $post->purchase_credit_note_invoice_number . '</a>';
                    }
                    $nestedData['supplier'] = $post->supplier_name;
                    $nestedData['grand_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->purchase_credit_note_grand_total, 2);
                    $nestedData['nature_of_supply'] = ucwords($post->purchase_credit_note_nature_of_supply);
                    $nestedData['receivable_date'] = $post->receivable_date;
                    $nestedData['purchase_invoice_number'] = $post->purchase_invoice_number;
                    if(in_array($purchase_module_id, $data['active_view'])){
                        $nestedData['purchase_invoice_number'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                    }
                    $nestedData['payment_status'] = $post->purchase_credit_note_type_of_supply;

                    $nestedData['taxable'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->purchase_credit_note_taxable_value, 2);
                    $nestedData['cgst'] = $this->precise_amount($post->purchase_credit_note_cgst_amount, 2);
                    $nestedData['sgst'] = $this->precise_amount($post->purchase_credit_note_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->purchase_credit_note_sgst_amount, 2);
                        $nestedData['sgst'] = "0.00";
                    } else {
                        $nestedData['sgst'] = $this->precise_amount($post->purchase_credit_note_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }*/
                    $nestedData['igst'] = $this->precise_amount($post->purchase_credit_note_igst_amount, 2);
                    $nestedData['from_account'] = $post->from_account;
                    $nestedData['pending_amount'] = $post->purchase_credit_note_nature_of_supply;
                    $nestedData['total'] = 'sdsdsd';
                    $nestedData['credit_note_gst_payable'] = ucwords($post->purchase_credit_note_gst_payable);
                    $nestedData['credit_note_type_of_supply'] = $post->purchase_credit_note_type_of_supply;
                    $nestedData["billing_country"] = $post->billing_country;
                    $nestedData["place_of_supply"] = $post->place_of_supply ? $post->place_of_supply : "Outside India";
                    $nestedData["type_of_supply"] = ucwords($post->purchase_credit_note_type_of_supply);
                    $nestedData["type_of_supply"] = str_replace('_', ' ', $nestedData["type_of_supply"]);
                    $nestedData['debit_note_number'] = $post->purchase_supplier_credit_note_number ? $post->purchase_supplier_credit_note_number : "-";
                    $debit_note_date = date('d-m-Y', strtotime($post->purchase_supplier_credit_note_date));
                    if ($post->purchase_supplier_credit_note_date == "1970-01-01" || $post->purchase_supplier_credit_note_date == "0000-00-00") {
                        $nestedData['debit_note_date'] = "-";
                    } else {
                        $nestedData['debit_note_date'] = $debit_note_date;
                    }
                    $nestedData['billing_currency'] = $post->billing_currency;
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;

                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();

            $list_data = $this->common->distinct_supplier_purchase_credit_note_name();
            $data['suppliers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_note_invoice_number();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_note_reference_number();
            $data['reference_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_nature_of_supply();
            $data['nature_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_note_billing_country();
            $data['billing_country'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_note_place_of_supply();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_type_of_supply();
            $data['type_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_supplier_dn_number();
            $data['credit_note_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_credit_note_cgst();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_credit_note_sgst();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_credit_note_utgst();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_credit_note_igst();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_purchase_pending_amount();
            $data['pending_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_sales_due_day();
            $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_note_paid_to();
            $data['test'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->purchase_credit_note_grand_total();
            $data['sum_grand_total'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');

            $list_data = $this->common->purchase_credit_sum();
            $data['sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');


            $this->load->view('reports/purchase_credit_note_report', $data);
        }
    }

    public function advance_voucher_report() {
        $advance_voucher_report_module_id = $this->config->item('advance_voucher_report_module');
        $data['module_id'] = $advance_voucher_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($advance_voucher_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $advance_voucher_module_id = $this->config->item('advance_voucher_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'receivable_date',
                8 => 'due',
                9 => 'action',
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->advance_voucher_list_field();
            $list_data['where'] = array(
                'av.delete_status' => 0,
                'av.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'av.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'av.receipt_amount !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'advance_voucher';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_customer_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_billing_country') != "" || $this->input->post('filter_payment_mode') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "" || $this->input->post('filter_tax') != "" || $this->input->post('filter_sgst') != "" || $this->input->post('filter_igst') != "" || $this->input->post('filter_billing_currency') != "" || $this->input->post('from_taxable_amount') || $this->input->post('to_taxable_amount') || $this->input->post('from_sgst_amount') || $this->input->post('to_sgst_amount') || $this->input->post('from_cgst_amount') || $this->input->post('to_cgst_amount') || $this->input->post('from_igst_amount') || $this->input->post('to_igst_amount') || $this->input->post('from_reciept_amount') || $this->input->post('to_reciept_amount') || $this->input->post('filter_utgst_from') != "" || $this->input->post('filter_utgst_to') != "") {
                $filter_search = array();
                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));

                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_billing_country'] = ($this->input->post('filter_billing_country') == '' ? '' : implode(",", $this->input->post('filter_billing_country')));
                $filter_search['filter_payment_mode'] = ($this->input->post('filter_payment_mode') == '' ? '' : implode(",", $this->input->post('filter_payment_mode')));
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_billing_currency'] = ($this->input->post('filter_billing_currency') == '' ? '' : implode(",", $this->input->post('filter_billing_currency')));

                $filter_search['from_taxable_amount'] = $this->input->post('from_taxable_amount');
                $filter_search['to_taxable_amount'] = $this->input->post('to_taxable_amount');
                $filter_search['from_sgst_amount'] = $this->input->post('from_sgst_amount');
                $filter_search['to_sgst_amount'] = $this->input->post('to_sgst_amount');
                $filter_search['from_cgst_amount'] = $this->input->post('from_cgst_amount');
                $filter_search['to_cgst_amount'] = $this->input->post('to_cgst_amount');
                $filter_search['from_igst_amount'] = $this->input->post('from_igst_amount');
                $filter_search['to_igst_amount'] = $this->input->post('to_igst_amount');
                $filter_search['filter_utgst_from_advance'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_advance'] = $this->input->post('filter_utgst_to') ?: 0;
                $filter_search['from_reciept_amount'] = $this->input->post('from_reciept_amount');
                $filter_search['to_reciept_amount'] = $this->input->post('to_reciept_amount');

                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_due'] = ($this->input->post('filter_due') == '' ? '' : implode(",", $this->input->post('filter_due')));

                $filter_search['filter_tax'] = ($this->input->post('filter_tax') == '' ? '' : implode(",", $this->input->post('filter_tax')));
                $filter_search['filter_sgst'] = ($this->input->post('filter_sgst') == '' ? '' : implode(",", $this->input->post('filter_sgst')));
                $filter_search['filter_igst'] = ($this->input->post('filter_igst') == '' ? '' : implode(",", $this->input->post('filter_igst')));
                /*$filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));*/

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.voucher_sub_total) as tot_voucher_sub_total, sum(t.voucher_igst_amount) as tot_voucher_igst_amount, sum(t.voucher_cgst_amount) as tot_voucher_cgst_amount, sum(t.sgst) as tot_voucher_sgst_amount,sum(t.utgst) as tot_voucher_utgst_amount, sum(t.receipt_amount) as tot_receipt_amount";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    // $recivable_date                = date('Y-m-d', strtotime($post->voucher_date . ' + 15 days'));
                    $advance_voucher_id = $this->encryption_url->encode($post->advance_voucher_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['invoice'] = $post->voucher_number;
                    if(in_array($advance_voucher_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('advance_voucher/view/') . $advance_voucher_id . '">' . $post->voucher_number . '</a>';
                    }
                    $nestedData['customer'] = $post->customer_name;
                    $nestedData['billing_country'] = $post->country_name;
                    $nestedData['to_account'] = $post->to_account;
                    $nestedData['place_of_supply'] = $post->state_name;
                    $nestedData['billing_currency'] = $post->currency_name;
                    // $nestedData['payment_mode'] = $post->payment_mode;
                    $nestedData['payment_mode'] = $post->from_account ? $post->from_account : "-";

                    // $nestedData['receivable_date'] = $post->reference_number;
                    // $nestedData['paid_amount']     = $post->receipt_amount;
                    $nestedData['receipt_amount'] = $this->precise_amount($post->receipt_amount, 2);
                    // $nestedData['payment_status']  = $post->from_account;
                    // $nestedData['voucher_tax_amount']  = ($post->voucher_tax_amount*$post->currency_converted_rate);
                    //$nestedData['from_account'] = $post->from_account;
                    $nestedData['voucher_cess_amount'] = $post->voucher_cess_amount;

                    $nestedData['cgst_amount'] = $this->precise_amount(($post->voucher_cgst_amount), 2);
                    $nestedData['sgst_amount'] = $this->precise_amount($post->voucher_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->voucher_sgst_amount, 2);
                        $nestedData['sgst_amount'] = "0.00";
                    } else {
                        $nestedData['sgst_amount'] = $this->precise_amount($post->voucher_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }*/
                    $nestedData['igst_amount'] = $this->precise_amount(($post->voucher_igst_amount), 2);
                    $nestedData['taxable_value'] = $nestedData['receipt_amount'] - ($nestedData['cgst_amount'] + $nestedData['sgst_amount'] + $nestedData['igst_amount'] + $nestedData['voucher_cess_amount']);
                    $nestedData['taxable_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->voucher_sub_total, 2);
                    $nestedData['receipt_amount'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->receipt_amount, 2);
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;

                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->distinct_advanced_voucher_list();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_advanced_voucher_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_advanced_voucher_country();
            $data['country'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_advanced_voucher_invoice_amount();
            $data['amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_advanced_voucher_from_account();
            $data['from_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_advanced_voucher_to_account();
            $data['to_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_advanced_voucher_taxable();
            $data['taxable'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_advanced_voucher_cgst();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_advanced_voucher_sgst();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_advanced_voucher_utgst();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_advanced_voucher_igst();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->advance_voucher_sum();
            $data['sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');

            $list_data = $this->common->distinct_currency_converted_records('advance_voucher');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_place_of_supply_advance();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_advance_voucher_currency();
            $data['advance_currency'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);


            $this->load->view('reports/advance_voucher_report', $data);
        }
    }

    public function receipt_voucher_report() {
        $receipt_voucher_report_module_id = $this->config->item('receipt_voucher_report_module');
        $data['module_id'] = $receipt_voucher_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($receipt_voucher_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $sales_module_id = $this->config->item('sales_module');
        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'invoice',
                2 => 'customer',
                3 => 'reference_number',
                4 => 'amount',
                5 => 'currency_converted_amount',
                6 => 'from_account',
                7 => 'added_user',
                8 => 'action',
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->receipt_voucher_list_field();
            $list_data['where'] = array(
                'rv.delete_status' => 0,
                'rv.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'rv.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'rv.receipt_amount !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'recipt_voucher';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_customer_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_due') != "" || $this->input->post('from_reciept_amount') != '' || $this->input->post('to_reciept_amount') != '' || $this->input->post('from_gain_loss') != '' || $this->input->post('to_gain_loss') != '' || $this->input->post('from_discount') != '' || $this->input->post('to_discount') != '' || $this->input->post('from_other_charges') != '' || $this->input->post('to_other_charges') != '' || $this->input->post('from_round_off') != '' || $this->input->post('to_round_off') != '' || $this->input->post('from_pending_amount') != '' || $this->input->post('to_pending_amount') != '' || $this->input->post('from_received_amount') != '' || $this->input->post('to_received_amount') != '' || $this->input->post('from_invoice_amount') != '' || $this->input->post('to_invoice_amount') != '') {
                $filter_search = array();
                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));

                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));


                $filter_search['from_reciept_amount'] = $this->input->post('from_reciept_amount');
                $filter_search['to_reciept_amount'] = $this->input->post('to_reciept_amount');


                $filter_search['from_gain_loss'] = $this->input->post('from_gain_loss');
                $filter_search['to_gain_loss'] = $this->input->post('to_gain_loss');
                $filter_search['from_discount'] = $this->input->post('from_discount');
                $filter_search['to_discount'] = $this->input->post('to_discount');
                $filter_search['from_other_charges'] = $this->input->post('from_other_charges');
                $filter_search['to_other_charges'] = $this->input->post('to_other_charges');
                $filter_search['from_round_off'] = $this->input->post('from_round_off');
                $filter_search['to_round_off'] = $this->input->post('to_round_off');
                $filter_search['from_pending_amount'] = $this->input->post('from_pending_amount');
                $filter_search['to_pending_amount'] = $this->input->post('to_pending_amount');
                $filter_search['from_received_amount'] = $this->input->post('from_received_amount');
                $filter_search['to_received_amount'] = $this->input->post('to_received_amount');
                $filter_search['from_invoice_amount'] = $this->input->post('from_invoice_amount');
                $filter_search['to_invoice_amount'] = $this->input->post('to_invoice_amount');

                $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount')));
                $filter_search['filter_payment_status'] = ($this->input->post('filter_payment_status') == '' ? '' : implode(",", $this->input->post('filter_payment_status')));
                $filter_search['filter_pending_amount'] = ($this->input->post('filter_pending_amount') == '' ? '' : implode(",", $this->input->post('filter_pending_amount')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_due'] = ($this->input->post('filter_due') == '' ? '' : implode(",", $this->input->post('filter_due')));
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $string = "sum(t.main_receipt_amount) as tot_receipt_amount,sum(t.minus_gain_loss) as tot_minus_gain_loss_amount, sum(t.plus_gain_loss) as tot_gain_loss_amount, sum(t.discount) as tot_discount_amount, sum(t.other_charges) as tot_other_charger_amount, sum(t.minus_round_off) as tot_round_minus_off_amount  , sum(t.plus_round_off) as tot_round_off_amount, sum(t.given_receipt_amount) as given_receipt_amount, sum(t.customer_payable_amount) as sales_grand_total, sum(t.Invoice_pending) as Invoice_pending, sum(t.receipt_total_paid) as receipt_received_paid";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            //print_r($posts);
            $tot_receipt_amount = 0;
            $is_exist = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = $post->voucher_date;
                    $receipt_id = $this->encryption_url->encode($post->receipt_id);
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['invoice'] = $post->voucher_number;
                    if(in_array($receipt_voucher_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('receipt_voucher/view_details/') . $receipt_id . '">' . $post->voucher_number . '</a>';
                    }
                    $nestedData['customer'] = $post->customer_name;
                    // $reference_number = explode(",", $post->reference_number);
                    $nestedData['reference_number'] = $post->sales_invoice_number;
                    if(in_array($sales_module_id, $data['active_view'])){
                        $nestedData['reference_number'] = ' <a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>';
                    }
                    if(!in_array($post->voucher_number,$is_exist)){
                        $tot_receipt_amount += $post->main_receipt_amount;
                        array_push($is_exist, $post->voucher_number);
                    }

                    // $nestedData['pending_amount']   = $post->to_account;
                    // $nestedData['reference_number'] = $post->reference_number;
                    // $converted_receipt_amount = explode(",", $post->receipt_amount);
                    // $nestedData['receipt_amount'] = implode("; ", $converted_receipt_amount);
                    $nestedData['receipt_amount'] = $this->precise_amount($post->main_receipt_amount, 2);
                    $nestedData['from_account'] = $post->from_account;
                    $exchange_gain_loss = $this->precise_amount($post->exchange_gain_loss, 2);
                    $exchange_gain_loss_type = $post->exchange_gain_loss_type;
                    if ($exchange_gain_loss_type == 'minus') {
                        $nestedData['exchange_gain_loss'] = $this->precise_amount($exchange_gain_loss * -1, 2);
                    } else {
                        $nestedData['exchange_gain_loss'] = $exchange_gain_loss;
                    }
                    $nestedData['discount'] = $this->precise_amount($post->discount, 2);
                    $nestedData['other_charges'] = $this->precise_amount($post->other_charges, 2);
                    $round_off = $this->precise_amount($post->round_off, 2);
                    $round_off_icon = $post->round_off_icon;
                    if ($round_off_icon == 'minus') {
                        $nestedData['round_off'] = $this->precise_amount($round_off * -1, 2);
                    } else {
                        $nestedData['round_off'] = $round_off;
                    }
                    $nestedData['Invoice_pending'] = $this->precise_amount($post->Invoice_pending, 2);
                    $nestedData['Invoice_total_received'] = $this->precise_amount($post->receipt_total_paid, 2);
                    $nestedData['invoice_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->customer_payable_amount, 2);
                    $nestedData['payment_mode'] = $post->from_account;
                    $nestedData['given_receipt_amount'] = $this->precise_amount($post->given_receipt_amount, 2);

                    // $nestedData['recived_amount']   = $post->invoice_total;
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;

                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }

                    $send_data[] = $nestedData;
                }
            }
            $total_list_record[0]->tot_receipt_amount = $tot_receipt_amount; 
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $list_data = $this->common->recipt_voucher_distinct_customer();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_recipt_voucher_invoice_number();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_recipt_voucher_invoice();
            $data['sale_reference'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_sales_due_day();
            $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_receipt_voucher_amount();
            $data['grand_total'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_to_account();
            $data['to_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_from_account();
            $data['from_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            /* $list_data = $this->common->distinct_recipt_voucher_voucher_invoice();
              $data['due_day'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']); */


            $list_data = $this->common->distinct_currency_converted_records('receipt_voucher');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            // $list_data              = $this->common->receipt_voucher_sum();
            // $data['sum']        = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join']='', $list_data['order']     = "", $list_data['group']='');


            $this->load->view('reports/receipt_voucher_report', $data);
        }
    }

    public function payment_voucher_report() {
        $payment_voucher_report_module_id = $this->config->item('payment_voucher_report_module');
        $data['module_id'] = $payment_voucher_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($payment_voucher_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        
        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $purchase_module_id = $this->config->item('purchase_module');
        $expense_bill_module_id = $this->config->item('expense_bill_module');
        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'date',
                1 => 'invoice',
                2 => 'supplier',
                3 => 'reference_number',
                4 => 'amount',
                5 => 'currency_converted_amount',
                6 => 'from_account',
                7 => 'added_user',
                8 => 'action',
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->payment_voucher_list_field();
            $list_data['where'] = array(
                'pv.delete_status' => 0,
                'pv.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'pv.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'pv.receipt_amount !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'payment_voucher';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_supplier_name') != "" ||
                    $this->input->post('filter_invoice_number') != "" ||
                    $this->input->post('filter_from_date') != "" ||
                    $this->input->post('filter_to_date') != "" ||
                    $this->input->post('filter_reference_number') != "" ||
                    $this->input->post('filter_invoice_amount') != "" ||
                    $this->input->post('filter_payment_status') != "" ||
                    $this->input->post('filter_paid_amount') != "" ||
                    $this->input->post('filter_receivable_date') != "" ||
                    $this->input->post('filter_total_paid_amount') != "" ||
                    $this->input->post('filter_voucher_type') != "" ||
                    $this->input->post('filter_billing_currency') != "" ||
                    $this->input->post('from_paid_amount') != "" ||
                    $this->input->post('to_paid_amount') != "" ||
                    $this->input->post('from_total_paid_amount') != "" ||
                    $this->input->post('to_total_paid_amount') != "" ||
                    $this->input->post('from_gain_loss') != "" ||
                    $this->input->post('to_gain_loss') != "" ||
                    $this->input->post('from_discount') != "" ||
                    $this->input->post('to_discount') != "" ||
                    $this->input->post('from_other_charges') != "" ||
                    $this->input->post('to_other_charges') != "" ||
                    $this->input->post('from_round_off') != "" ||
                    $this->input->post('to_round_off') != "" ||
                    $this->input->post('from_pending_amount') != "" ||
                    $this->input->post('to_pending_amount') != "" ||
                    $this->input->post('from_invoice_amount') != "" ||
                    $this->input->post('to_invoice_amount') != "" ||
                    $this->input->post('from_received_amount') != "" ||
                    $this->input->post('to_received_amount') != "" 
            ) {
                $filter_search = array();
                $filter_search['filter_supplier_name'] = ($this->input->post('filter_supplier_name') == '' ? '' : implode(",", $this->input->post('filter_supplier_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));

                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));


                $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount')));

                $filter_search['filter_billing_currency'] = ($this->input->post('filter_billing_currency') == '' ? '' : implode(",", $this->input->post('filter_billing_currency')));

                $filter_search['filter_reference_number'] = ($this->input->post('filter_reference_number') == '' ? '' : implode(",", $this->input->post('filter_reference_number')));
                /*$filter_search['filter_reference_number_type'] = ($this->input->post('filter_reference_number_type') == '' ? '' : implode(",", $this->input->post('filter_reference_number_type')));*/
    
                $filter_search['filter_payment_status'] = ($this->input->post('filter_payment_status') == '' ? '' : implode(",", $this->input->post('filter_payment_status')));
                $filter_search['filter_paid_amount'] = ($this->input->post('filter_paid_amount') == '' ? '' : implode(",", $this->input->post('filter_paid_amount')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_total_paid_amount'] = ($this->input->post('filter_total_paid_amount') == '' ? '' : implode(",", $this->input->post('filter_total_paid_amount')));
                $filter_search['filter_voucher_type'] = ($this->input->post('filter_voucher_type') == '' ? '' : implode(",", $this->input->post('filter_voucher_type')));
                $filter_search['from_paid_amount'] = $this->input->post('from_paid_amount');
                $filter_search['to_paid_amount'] = $this->input->post('to_paid_amount');
                $filter_search['from_total_paid_amount'] = $this->input->post('from_total_paid_amount');
                $filter_search['to_total_paid_amount'] = $this->input->post('to_total_paid_amount');
                $filter_search['from_gain_loss'] = $this->input->post('from_gain_loss');
                $filter_search['to_gain_loss'] = $this->input->post('to_gain_loss');
                $filter_search['from_discount'] = $this->input->post('from_discount');
                $filter_search['to_discount'] = $this->input->post('to_discount');
                $filter_search['from_other_charges'] = $this->input->post('from_other_charges');
                $filter_search['to_other_charges'] = $this->input->post('to_other_charges');
                $filter_search['from_round_off'] = $this->input->post('from_round_off');
                $filter_search['to_round_off'] = $this->input->post('to_round_off');
                $filter_search['from_pending_amount'] = $this->input->post('from_pending_amount');
                $filter_search['to_pending_amount'] = $this->input->post('to_pending_amount');
                $filter_search['from_invoice_amount'] = $this->input->post('from_invoice_amount');
                $filter_search['to_invoice_amount'] = $this->input->post('to_invoice_amount');
                $filter_search['from_received_amount'] = $this->input->post('from_received_amount');
                $filter_search['to_received_amount'] = $this->input->post('to_received_amount');
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $string = "sum(t.payment_amount) as tot_paid_amount, sum(t.receipt_amount) as tot_of_total_paid_amount, sum(t.minus_gain_loss) as tot_minus_gain_loss_amount, sum(t.plus_gain_loss) as tot_gain_loss_amount, sum(t.discount) as tot_discount_amount, sum(t.other_charges) as tot_other_charger_amount, sum(t.minus_round_off) as tot_round_off_minus_amount,  sum(t.plus_round_off) as tot_round_off_amount, sum(t.Invoice_pending) as tot_pending_amount, sum(t.invoice_amount) as tot_invoice_amount, sum(total_received_amount) as tot_received_amount";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);

            $send_data = array();
            //print_r($posts);
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = $post->voucher_date;
                    $payment_id = $this->encryption_url->encode($post->payment_id);
                    $purchase_id = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['supplier'] = $post->supplier_name;
                    $nestedData['invoice'] = $post->voucher_number;
                    if(in_array($payment_voucher_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('payment_voucher/view_details/') . $payment_id . '">' . $post->voucher_number . '</a>';
                    }
                    $nestedData['date'] = date('d-m-Y', strtotime($post->voucher_date));
                    // $nestedData['reference_number'] = $post->reference_number;
                    $nestedData['invoice_total'] = $this->precise_amount($post->receipt_amount, 2);

                    /*$nestedData['reference_number'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';*/
                    $converted_receipt_amount = explode(",", $post->receipt_amount);
                    // $nestedData['invoice_total'] = implode("; ", $converted_receipt_amount);
                    //$nestedData['invoice_total']=$this->precise_amount($nestedData['invoice_total'],2);
                    $nestedData['voucher_type'] = $post->reference_voucher_type ? $post->reference_voucher_type : "-";
                    if ($nestedData['voucher_type'] == 'expense') {
                        $nestedData['voucher_type'] = 'Expense Bill';
                        $nestedData['reference_number'] = $post->purchase_invoice_number;
                        if(in_array($expense_bill_module_id, $data['active_view'])){
                            $nestedData['reference_number'] = ' <a href="' . base_url('expense_bill/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                        }
                    } elseif ($nestedData['voucher_type'] == 'purchase') {
                        $nestedData['voucher_type'] = 'Purchase Bill';
                        $nestedData['reference_number'] = $post->purchase_invoice_number;
                        if(in_array($purchase_module_id, $data['active_view'])){
                            $nestedData['reference_number'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                        }
                    } else {
                        $nestedData['voucher_type'] = 'Excess';
                        $nestedData['reference_number'] = $post->purchase_invoice_number;
                        if(in_array($purchase_module_id, $data['active_view'])){
                            $nestedData['reference_number'] = ' <a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>';
                        }
                    }
                    $nestedData['currency_name'] = $post->currency_name ? $post->currency_name : "-";
                    //$nestedData['payment_mode'] = $post->to_account;
                    $nestedData['paid_amount'] = $this->precise_amount($post->payment_amount, 2);
                    $nestedData['total_paid_amount'] = $this->precise_amount($post->receipt_amount, 2);
                    // $nestedData['voucher_type']     = $post->voucher_type;
                    $exchange_gain_loss = $this->precise_amount($post->exchange_gain_loss, 2);
                    $exchange_gain_loss_type = $post->exchange_gain_loss_type;
                    if ($exchange_gain_loss_type == 'minus') {
                        $nestedData['exchange_gain_loss'] = $this->precise_amount($exchange_gain_loss * -1, 2);
                    } else {
                        $nestedData['exchange_gain_loss'] = $this->precise_amount($exchange_gain_loss, 2);
                    }
                    $nestedData['discount'] = $this->precise_amount($post->discount, 2);
                    $nestedData['other_charges'] = $this->precise_amount($post->other_charges, 2);
                    $round_off = $this->precise_amount($post->round_off, 2);
                    $round_off_icon = $post->round_off_icon;
                    if ($round_off_icon == 'minus') {
                        $nestedData['round_off'] = $this->precise_amount($round_off * -1, 2);
                    } else {
                        $nestedData['round_off'] = $this->precise_amount($round_off, 2);
                    }
                    $nestedData['Invoice_pending'] = $this->precise_amount($post->Invoice_pending, 2);
                    $nestedData['Invoice_total_paid'] = $this->precise_amount($post->Invoice_total_paid, 2);
                    $nestedData['invoice_total'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->invoice_amount, 2);
                    $nestedData['Invoice_total_paid'] = $this->precise_amount($post->total_received_amount, 2);
                    $nestedData['payment_mode'] = $post->from_account;

                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;

                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();

            $list_data = $this->common->distinct_billing_currency();
            $data['billing_currency'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_supplier_payment_voucher();
            $data['supplier'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_payment_voucher_invoice_number();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_payment_voucher_invoice();
            $data['invoices_ref'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_payment_voucher_invoice();
            $data['invoice_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_receipt_voucher_amount();
            $data['grand_total'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_payment_voucher_to_account();
            $data['to_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_payment_voucher_from_account();
            $data['from_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);


            $list_data = $this->common->distinct_payment_voucher_paid_amount();
            $data['paid_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_payment_voucher_total_paid_amount();
            $data['total_paid_amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_payment_voucher_voucher_type();
            $data['voucher_type'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_currency_converted_records('payment_voucher');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $this->load->view('reports/payment_voucher_report', $data);
        }
    }

    public function refund_voucher_report() {
        $refund_voucher_report_module_id = $this->config->item('refund_voucher_report_module');
        $data['module_id'] = $refund_voucher_report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($refund_voucher_report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $advance_voucher_module_id = $this->config->item('advance_voucher_module');
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'supplier',
                1 => 'invoice',
                2 => 'date',
                3 => 'grand_total',
                4 => 'paid_amount',
                5 => 'payment_status',
                6 => 'pending_amount',
                7 => 'receivable_date',
                8 => 'due',
                9 => 'action',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->refund_voucher_list_field();
            $list_data['where'] = array(
                'rv.delete_status' => 0,
                'rv.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'rv.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'rv.receipt_amount !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'refund_voucher';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_customer_name') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_invoice_amount') != "" || $this->input->post('filter_received_amount') != "" || $this->input->post('filter_payment_status') != "" || $this->input->post('filter_pending_amount') != "" || $this->input->post('filter_receivable_date') != "" || $this->input->post('filter_tax') != "" || $this->input->post('filter_from_refund_amount') != "" || $this->input->post('filter_to_refund_amount') != "" || $this->input->post('filter_billing_country') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_billing_country') != "" || $this->input->post('filter_billing_currency') != "" || $this->input->post('filter_payment_mode') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_cgst_amount') != "" || $this->input->post('filter_to_cgst_amount') != "" || $this->input->post('filter_from_sgst_amount') != "" || $this->input->post('filter_to_sgst_amount') != "" || $this->input->post('filter_from_igst_amount') != "" || $this->input->post('filter_to_igst_amount') != "" || $this->input->post('filter_from_reciept_amount') != "" || $this->input->post('filter_to_reciept_amount') != "" || $this->input->post('filter_utgst_from') != "" || $this->input->post('filter_utgst_to') != "") {
                $filter_search = array();
                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));
                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));
                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));
                $filter_search['filter_from_refund_amount'] = $this->input->post('filter_from_refund_amount');
                $filter_search['filter_to_refund_amount'] = $this->input->post('filter_to_refund_amount');
                $filter_search['filter_billing_country'] = ($this->input->post('filter_billing_country') == '' ? '' : implode(",", $this->input->post('filter_billing_country')));
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));
                $filter_search['filter_billing_currency'] = ($this->input->post('filter_billing_currency') == '' ? '' : implode(",", $this->input->post('filter_billing_currency')));

                $filter_search['filter_payment_mode'] = ($this->input->post('filter_payment_mode') == '' ? '' : implode(",", $this->input->post('filter_payment_mode')));

                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');



                $filter_search['filter_from_cgst_amount'] = $this->input->post('filter_from_cgst_amount');
                $filter_search['filter_to_cgst_amount'] = $this->input->post('filter_to_cgst_amount');


                $filter_search['filter_from_sgst_amount'] = $this->input->post('filter_from_sgst_amount');
                $filter_search['filter_to_sgst_amount'] = $this->input->post('filter_to_sgst_amount');


                $filter_search['filter_from_igst_amount'] = $this->input->post('filter_from_igst_amount');
                $filter_search['filter_to_igst_amount'] = $this->input->post('filter_to_igst_amount');


                $filter_search['filter_from_reciept_amount'] = $this->input->post('filter_from_reciept_amount');
                $filter_search['filter_to_reciept_amount'] = $this->input->post('filter_to_reciept_amount');

                $filter_search['filter_invoice_amount'] = ($this->input->post('filter_invoice_amount') == '' ? '' : implode(",", $this->input->post('filter_invoice_amount')));
                $filter_search['filter_received_amount'] = ($this->input->post('filter_received_amount') == '' ? '' : implode(",", $this->input->post('filter_received_amount')));
                $filter_search['filter_payment_status'] = ($this->input->post('filter_payment_status') == '' ? '' : implode(",", $this->input->post('filter_payment_status')));
                $filter_search['filter_pending_amount'] = ($this->input->post('filter_pending_amount') == '' ? '' : implode(",", $this->input->post('filter_pending_amount')));
                $filter_search['filter_receivable_date'] = $this->input->post('filter_receivable_date');
                $filter_search['filter_utgst_from_refund'] = $this->input->post('filter_utgst_from') ?: 0;
                $filter_search['filter_utgst_to_refund'] = $this->input->post('filter_utgst_to') ?: 0;
                /*$filter_search['filter_utgst'] = ($this->input->post('filter_utgst') == '' ? '' : implode(",", $this->input->post('filter_utgst')));*/


                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $string = "sum(t.voucher_sub_total) as tot_voucher_sub_total, sum(t.voucher_igst_amount) as tot_voucher_igst_amount, sum(t.voucher_cgst_amount) as tot_voucher_cgst_amount, sum(t.sgst) as tot_voucher_sgst_amount, sum(t.utgst) as tot_voucher_utgst_amount, sum(t.receipt_amount) as tot_receipt_amount";

            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            //print_r($posts);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $recivable_date = date('Y-m-d', strtotime($post->voucher_date . ' + 15 days'));
                    $refund_id = $this->encryption_url->encode($post->refund_id);
                    $reference_id = $this->encryption_url->encode($post->reference_id);
                    $nestedData['date'] = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['invoice'] = $post->voucher_number;
                    if(in_array($refund_voucher_module_id, $data['active_view'])){
                        $nestedData['invoice'] = ' <a href="' . base_url('refund_voucher/view/') . $refund_id . '">' . $post->voucher_number . '</a>';
                    }
                    $nestedData['customer'] = $post->customer_name;
                    //$nestedData['country'] = $post->country_name;
                    $nestedData['reference_number'] = $post->reference_number;
                    if(in_array($advance_voucher_module_id, $data['active_view'])){
                        $nestedData['reference_number'] = ' <a href="' . base_url('advance_voucher/view/') . $reference_id . '">' . $post->reference_number . '</a>';
                    }
                    $nestedData['refund_amount'] = $this->precise_amount($post->receipt_amount, 2);
                    $nestedData['billing_country'] = $post->billing_country;
                    // $nestedData['receivable_date'] = $post->reference_number;
                    // $nestedData['paid_amount']     = $post->receipt_amount;
                    $nestedData['receipt_amount'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->receipt_amount, 2);
                    // $nestedData['payment_status']  = $post->from_account;
                    // $nestedData['voucher_tax_amount']  = $post->voucher_tax_amount;
                    $nestedData['from_account'] = $post->from_account;
                    $nestedData['to_account'] = $post->to_account;
                    $nestedData['place_of_supply'] = $post->place_of_supply ?: "Outside India";
                    $nestedData['billing_currency'] = $post->currency_name;
                    // $nestedData['payment_mode'] = $post->payment_mode; 
                    $nestedData['payment_mode'] = $post->to_account;
                    $nestedData['taxable_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->voucher_sub_total, 2);
                    $nestedData['cgst_amount'] = $this->precise_amount($post->voucher_cgst_amount, 2);
                    $nestedData['sgst_amount'] = $this->precise_amount($post->voucher_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst'] = $this->precise_amount($post->voucher_sgst_amount, 2);
                        $nestedData['sgst_amount'] = "0.00";
                    } else {
                        $nestedData['sgst_amount'] = $this->precise_amount($post->voucher_sgst_amount, 2);
                        $nestedData['utgst'] = "0.00";
                    }*/
                    $nestedData['igst_amount'] = $this->precise_amount($post->voucher_igst_amount, 2);
                    $email_sub_module = 0;
                    $recurrence_sub_module = 0;

                    if (in_array($email_sub_module_id, $data['access_sub_modules'])) {
                        $email_sub_module = 1;
                    }
                    if (in_array($recurrence_sub_module_id, $data['access_sub_modules'])) {
                        $recurrence_sub_module = 1;
                    }

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();

            $list_data = $this->common->distinct_refund_voucher_list();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_refund_voucher_invoice();
            $data['invoices'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_refund_voucher_country();
            $data['country'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_refund_voucher_currency();
            $data['refund_currency'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_refund_voucher_invoice_amount();
            $data['amount'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_gst_place_of_supply_refund();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            // $list_data               = $this->common->distinct_advanced_voucher_from_account();
            // $data['from_account']  = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join']       = "", $list_data['order']      = "", $list_data['group']);

            $list_data = $this->common->distinct_refund_voucher_from_account();
            $data['from_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_refund_voucher_to_account();
            $data['to_account'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_refund_voucher_taxable();
            $data['taxable'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_recipt_voucher_cgst();
            $data['cgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_recipt_voucher_sgst();
            $data['sgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_recipt_voucher_utgst();
            $data['utgst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_recipt_voucher_igst();
            $data['igst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_refund_voucher_reference_invoice_amount();
            $data['reference_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->refund_voucher_sum();
            $data['sum'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = '', $list_data['order'] = "", $list_data['group'] = '');

            $list_data = $this->common->distinct_currency_converted_records('refund_voucher');
            $data['currency_converted_records'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);


            $this->load->view('reports/refund_voucher_report', $data);
        }
    }

    public function all_reports() {
        $report_module_id = $this->config->item('report_module');
        $data['module_id'] = $report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($report_module_id, $modules, $privilege);

        $data['sales_report_module_id'] = $this->config->item('sales_report_module');
        $data['sales_credit_note_report_module_id'] = $this->config->item('sales_credit_note_report_module');
        $data['sales_debit_note_report_module_id'] = $this->config->item('sales_debit_note_report_module');
        $data['purchase_report_module_id'] = $this->config->item('purchase_report_module');
        $data['purchase_credit_note_report_module_id'] = $this->config->item('purchase_credit_note_report_module');
        $data['purchase_debit_note_report_module_id'] = $this->config->item('purchase_debit_note_report_module');
        $data['expense_bill_report_module_id'] = $this->config->item('expense_bill_report_module');
        $data['advance_voucher_report_module_id'] = $this->config->item('advance_voucher_report_module');
        $data['refund_voucher_report_module_id'] = $this->config->item('refund_voucher_report_module');
        $data['receipt_voucher_report_module_id'] = $this->config->item('receipt_voucher_report_module');
        $data['payment_voucher_report_module_id'] = $this->config->item('payment_voucher_report_module');
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $list_data = $this->common->sales_report_front_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['sales'] = $posts;

        $list_data = $this->common->sales_debit_note_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['sales_dn'] = $posts;

        $list_data = $this->common->sales_credit_note_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['sales_cn'] = $posts;

        $list_data = $this->common->purchase_list_report_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['purchase'] = $posts;

        $list_data = $this->common->purchase_debit_note_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['purchase_dn'] = $posts;

        $list_data = $this->common->purchase_credit_note_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['purchase_cn'] = $posts;

        $list_data = $this->common->expense_bill_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['expence_bill'] = $posts;

        $list_data = $this->common->advance_voucher_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['advance_voucher'] = $posts;

        $list_data = $this->common->refund_voucher_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['refund_voucher'] = $posts;

        $list_data = $this->common->receipt_voucher_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['receipt_voucher'] = $posts;

        $list_data = $this->common->payment_voucher_report_list_field();
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            $posts = $this->general_model->getPageJoinRecords($list_data);
            $data['payment_voucher'] = $posts;
            // echo "<pre>";
            // print_r($data);
            // exit();

        $this->load->view('report/all_reports', $data);
    }

    // function tds_report()
    // {
    //     $report_module_id                = $this->config->item('report_module');
    //     $data['module_id']               = $report_module_id;
    //     $modules                         = $this->modules;
    //     $privilege                       = "view_privilege";
    //     $data['privilege']               = $privilege;
    //     $section_modules                 = $this->get_section_modules($report_module_id, $modules, $privilege);
    //     $data['access_modules']          = $section_modules['modules'];
    //     $data['access_sub_modules']      = $section_modules['sub_modules'];
    //     $data['access_module_privilege'] = $section_modules['module_privilege'];
    //     $data['access_user_privilege']   = $section_modules['user_privilege'];
    //     $data['access_settings']         = $section_modules['settings'];
    //     $data['access_common_settings']  = $section_modules['common_settings'];
    //     foreach ($modules['modules'] as $key => $value)
    //     {
    //         $data['active_modules'][$key] = $value->module_id;
    //         if ($value->view_privilege == "yes")
    //         {
    //             $data['active_view'][$key] = $value->module_id;
    //         } if ($value->edit_privilege == "yes")
    //         {
    //             $data['active_edit'][$key] = $value->module_id;
    //         } if ($value->delete_privilege == "yes")
    //         {
    //             $data['active_delete'][$key] = $value->module_id;
    //         } if ($value->add_privilege == "yes")
    //         {
    //             $data['active_add'][$key] = $value->module_id;
    //         }
    //     } $this->load->view('report/tds_report', $data);
    // }

    public function tds_report() {
        $report_module_id = $this->config->item('report_module');
        $data['module_id'] = $report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'expense_bill_date',
                1 => 'expense_bill_item_description',
                2 => 'expense_bill_item_sub_total',
                3 => 'expense_bill_item_tds_percentage',
                4 => 'expense_bill_item_tds_amount',
            );

            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->tds_report_list($from_date, $to_date);

            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }



            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $expense_bill_item_id = $this->encryption_url->encode($post->expense_bill_item_id);
                    $nestedData['expense_bill_date'] = $post->expense_bill_date;
                    $nestedData['expense_bill_item_description'] = $post->expense_bill_item_description;
                    $nestedData['expense_bill_item_sub_total'] = $post->expense_bill_item_sub_total;
                    $nestedData['expense_bill_item_tds_percentage'] = $post->expense_bill_item_tds_percentage;
                    $nestedData['expense_bill_item_tds_amount'] = $post->expense_bill_item_tds_amount;

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('report/tds_report', $data);
        }
    }

    public function leads_report() {
        $crm_module_id = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($crm_module_id, $modules, $privilege);

        $lead_module_id = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($lead_module_id, $modules, $privilege);


        /* presents all the needed */
        $data = array_merge($data, $section_modules);


        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'lead_date',
                1 => 'asr_no',
                2 => 'group',
                3 => 'stages',
                4 => 'source',
                5 => 'business_type',
                6 => 'next_action_date',
                7 => 'priority',
                8 => 'added_user',
                9 => 'action'
            );

            $search_data = array(
                'asr_no' => '',
                'customer' => '',
                'group' => '',
                'stages' => '',
                'lead_from' => '',
                'lead_to' => '',
                'next_action_from' => '',
                'next_action_to' => ''
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->leads_list_field($search_data);

            $list_data['search'] = 'all';
            $list_data['section'] = 'leads';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_asr_no') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_customer_name') != "" || $this->input->post('filter_from_next_action_date') != "" || $this->input->post('filter_to_next_action_date') != "" || $this->input->post('filter_group') != "" || $this->input->post('filter_stages') != "" || $this->input->post('filter_source') != "" || $this->input->post('filter_business_type') != "" || $this->input->post('filter_priority') != "") {
                $filter_search = array();
                $filter_search['filter_asr_no'] = ($this->input->post('filter_asr_no') == '' ? '' : implode(",", $this->input->post('filter_asr_no')));

                $filter_search['filter_from_date'] = $this->input->post('filter_from_date');
                $filter_search['filter_to_date'] = $this->input->post('filter_to_date');

                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));

                $filter_search['filter_from_next_action_date'] = $this->input->post('filter_from_next_action_date');

                $filter_search['filter_to_next_action_date'] = $this->input->post('filter_to_next_action_date');

                $filter_search['filter_group'] = ($this->input->post('filter_group') == '' ? '' : implode(",", $this->input->post('filter_group')));

                $filter_search['filter_stages'] = ($this->input->post('filter_stages') == '' ? '' : implode(",", $this->input->post('filter_stages')));

                $filter_search['filter_source'] = ($this->input->post('filter_source') == '' ? '' : implode(",", $this->input->post('filter_source')));

                $filter_search['filter_business_type'] = ($this->input->post('filter_business_type') == '' ? '' : implode(",", $this->input->post('filter_business_type')));

                $filter_search['filter_priority'] = ($this->input->post('filter_priority') == '' ? '' : implode(",", $this->input->post('filter_priority')));

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $lead_id = $this->encryption_url->encode($post->lead_id);
                    $nestedData['asr_no'] = $post->asr_no;
                    $nestedData['lead_date'] = date('d-m-Y', strtotime($post->lead_date));
                    $nestedData['customer'] = $post->customer_code . ' - ' . $post->customer_name;

                    $nestedData['group'] = $post->lead_group_name;
                    $nestedData['stages'] = $post->lead_stages_name;
                    $nestedData['source'] = $post->lead_source_name;
                    $nestedData['business_type'] = $post->lead_business_type;

                    $nestedData['next_action_date'] = date('d-m-Y', strtotime($post->next_action_date));
                    $nestedData['priority'] = $post->priority;

                    // $nestedData['added_user']       = $post->first_name . ' ' . $post->last_name;

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {

            $data['customers'] = $this->customer_call();

            $list_data = $this->common->distinct_asr_no();
            $data['asr_no'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->leads_group_list_field();
            $data['group'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $list_data = $this->common->leads_stages_list_field();
            $data['stages'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $list_data = $this->common->leads_source_list_field();
            $data['source'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $list_data = $this->common->leads_business_type_list_field();
            $data['business_type'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $this->load->view('reports/leads_report', $data);
        }
    }

    public function logs_report() {
        $lead_module_id = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'lead_date',
                1 => 'asr_no',
                2 => 'group',
                3 => 'stages',
                4 => 'source',
                5 => 'business_type',
                6 => 'next_action_date',
                7 => 'priority',
                8 => 'added_user',
                9 => 'action'
            );

            $search_data = array(
                'asr_no' => '',
                'customer' => '',
                'group' => '',
                'stages' => '',
                'lead_from' => '',
                'lead_to' => '',
                'next_action_from' => '',
                'next_action_to' => ''
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->leads_list_field($search_data);

            $list_data['search'] = 'all';
            $list_data['section'] = 'leads';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            if ($this->input->post('filter_asr_no') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_customer_name') != "" || $this->input->post('filter_from_next_action_date') != "" || $this->input->post('filter_to_next_action_date') != "" || $this->input->post('filter_group') != "" || $this->input->post('filter_stages') != "" || $this->input->post('filter_source') != "" || $this->input->post('filter_business_type') != "" || $this->input->post('filter_priority') != "") {
                $filter_search = array();
                $filter_search['filter_asr_no'] = ($this->input->post('filter_asr_no') == '' ? '' : implode(",", $this->input->post('filter_asr_no')));

                $filter_search['filter_from_date'] = $this->input->post('filter_from_date');
                $filter_search['filter_to_date'] = $this->input->post('filter_to_date');

                $filter_search['filter_customer_name'] = ($this->input->post('filter_customer_name') == '' ? '' : implode(",", $this->input->post('filter_customer_name')));

                $filter_search['filter_from_next_action_date'] = $this->input->post('filter_from_next_action_date');

                $filter_search['filter_to_next_action_date'] = $this->input->post('filter_to_next_action_date');

                $filter_search['filter_group'] = ($this->input->post('filter_group') == '' ? '' : implode(",", $this->input->post('filter_group')));

                $filter_search['filter_stages'] = ($this->input->post('filter_stages') == '' ? '' : implode(",", $this->input->post('filter_stages')));

                $filter_search['filter_source'] = ($this->input->post('filter_source') == '' ? '' : implode(",", $this->input->post('filter_source')));

                $filter_search['filter_business_type'] = ($this->input->post('filter_business_type') == '' ? '' : implode(",", $this->input->post('filter_business_type')));

                $filter_search['filter_priority'] = ($this->input->post('filter_priority') == '' ? '' : implode(",", $this->input->post('filter_priority')));

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $lead_id = $this->encryption_url->encode($post->lead_id);
                    $nestedData['asr_no'] = $post->asr_no;
                    $nestedData['lead_date'] = $post->lead_date;
                    $nestedData['customer'] = $post->customer_code . ' - ' . $post->customer_name;

                    $nestedData['group'] = $post->lead_group_name;
                    $nestedData['stages'] = $post->lead_stages_name;
                    $nestedData['source'] = $post->lead_source_name;
                    $nestedData['business_type'] = $post->lead_business_type;

                    $nestedData['next_action_date'] = date('d-m-Y', strtotime($post->next_action_date));
                    $nestedData['priority'] = $post->priority;

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {

            $data['customers'] = $this->customer_call();

            $list_data = $this->common->distinct_asr_no();
            $data['asr_no'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'] = "", $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->leads_group_list_field();
            $data['group'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $list_data = $this->common->leads_stages_list_field();
            $data['stages'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $list_data = $this->common->leads_source_list_field();
            $data['source'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $list_data = $this->common->leads_business_type_list_field();
            $data['business_type'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "");

            $this->load->view('reports/logs_report', $data);
        }
    }

    public function gst_report() {
        $gst_report_id = $this->config->item('gst_report');
        $data['module_id'] = $gst_report_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($gst_report_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $sales_module_id = $this->config->item('sales_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);


        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'voucher_number',
                1 => 'customer_name',
                2 => 'gstin',
                3 => 'invoice_number',
                4 => 'invoice_date',
                5 => 'taxable_value',
                6 => 'hsn_code',
                7 => 'quantity',
                8 => 'uom',
                9 => 'rate',
                10 => 'place_of_supply',
                11 => 'tax_rate',
                12 => 'cgst_amount',
                13 => 'sgst_amount',
                14 => 'igst_amount',
                15 => 'utgst_amount',
                16 => 'cess_rate',
                17 => 'cess_amount',
                18 => 'invoice_value'
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->gst_list_field();
            $list_data['where'] = array(
                'S.delete_status' => 0,
                'S.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'S.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'SI.sales_item_tax_percentage !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'sales_gst';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            if ($this->input->post('filter_voucher') != "" || $this->input->post('filter_customer') != "" || $this->input->post('filter_gstin') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('from_date') != "" || $this->input->post('to_date') != "" || $this->input->post('from_invoice_amount') != "" || $this->input->post('to_invoice_amount') != "" || $this->input->post('from_taxable_amount') != "" || $this->input->post('to_taxable_amount') != "" || $this->input->post('filter_hsn_number') != "" || $this->input->post('filter_quantity') != "" || $this->input->post('filter_uom') != "" || $this->input->post('filter_from_price') != "" || $this->input->post('filter_to_price') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_gst_rate') != "" || $this->input->post('from_cgst_amount') != "" || $this->input->post('to_cgst_amount') != "" || $this->input->post('from_sgst_amount') != "" || $this->input->post('to_sgst_amount') != "" || $this->input->post('from_igst_amount') != "" || $this->input->post('to_igst_amount') != "" || $this->input->post('from_ugst_amount') != "" || $this->input->post('to_ugst_amount') != "" || $this->input->post('filter_cess_rate') != "" || $this->input->post('from_cess_amount') != "" || $this->input->post('to_cess_amount') != "") {
                $filter_search = array();

                $filter_search['filter_voucher'] = ($this->input->post('filter_voucher') == '' ? '' : implode(",", $this->input->post('filter_voucher')));

                $filter_search['filter_customer'] = ($this->input->post('filter_customer') == '' ? '' : implode(",", $this->input->post('filter_customer')));

                $filter_search['filter_gstin'] = ($this->input->post('filter_gstin') == '' ? '' : implode(",", $this->input->post('filter_gstin')));

                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));

                $filter_search['from_date'] = ($this->input->post('from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('from_date'))));
                $filter_search['to_date'] = ($this->input->post('to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('to_date'))));

                $filter_search['from_invoice_amount'] = $this->input->post('from_invoice_amount');

                $filter_search['to_invoice_amount'] = $this->input->post('to_invoice_amount');

                $filter_search['from_taxable_amount'] = $this->input->post('from_taxable_amount');

                $filter_search['to_taxable_amount'] = $this->input->post('to_taxable_amount');

                $filter_search['filter_hsn_number'] = ($this->input->post('filter_hsn_number') == '' ? '' : implode(",", $this->input->post('filter_hsn_number')));


                $filter_search['filter_quantity'] = ($this->input->post('filter_quantity') == '' ? '' : implode(",", $this->input->post('filter_quantity')));

                $filter_search['filter_uom'] = ($this->input->post('filter_uom') == '' ? '' : implode(",", $this->input->post('filter_uom')));

                //$filter_search['filter_price'] = ($this->input->post('filter_price') == '' ? '' : implode(",", $this->input->post('filter_price')));

                $filter_search['filter_from_price'] = $this->input->post('filter_from_price');
                $filter_search['filter_to_price'] = $this->input->post('filter_to_price');
                
                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));

                $filter_search['filter_gst_rate'] = ($this->input->post('filter_gst_rate') == '' ? '' : implode(",", $this->input->post('filter_gst_rate')));

                $filter_search['from_cgst_amount'] = $this->input->post('from_cgst_amount');

                $filter_search['to_cgst_amount'] = $this->input->post('to_cgst_amount');

                $filter_search['from_sgst_amount'] = $this->input->post('from_sgst_amount');

                $filter_search['to_sgst_amount'] = $this->input->post('to_sgst_amount');

                $filter_search['from_igst_amount'] = $this->input->post('from_igst_amount');

                $filter_search['to_igst_amount'] = $this->input->post('to_igst_amount');

                $filter_search['from_ugst_amount'] = $this->input->post('from_ugst_amount');

                $filter_search['to_ugst_amount'] = $this->input->post('to_ugst_amount');

                $filter_search['filter_cess_rate'] = ($this->input->post('filter_cess_rate') == '' ? '' : implode(",", $this->input->post('filter_cess_rate')));

                $filter_search['from_cess_amount'] = $this->input->post('from_cess_amount');

                $filter_search['to_cess_amount'] = $this->input->post('to_cess_amount');

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.sales_item_taxable_value) as tot_taxable_value, sum(t.sales_grand_total) as invoice_value, sum(t.sales_item_igst_amount) as igst_amount, sum(t.sales_item_cgst_amount) as cgst_amount, sum(t.SGST) as sgst_amount, sum(t.UTGST) as utgst_amount, sum(t.sales_item_tax_cess_amount) as cess_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $gst_percentage = $post->sales_item_cgst_percentage + $post->sales_item_sgst_percentage + $post->sales_item_igst_percentage;
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $nestedData['voucher_number'] = $post->sales_invoice_number;
                    if(in_array($sales_module_id, $data['active_view'])){
                        $nestedData['voucher_number'] = '<a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>';
                    }
                    $nestedData['customer_name'] = $post->customer_name;
                    $nestedData['gstin'] = '-';
                    if($post->customer_gstin_number != ''){
                        $nestedData['gstin'] = $post->customer_gstin_number;
                    }
                    $nestedData['invoice_number'] = $post->sales_invoice_number;
                    $nestedData['invoice_date'] = date('d-m-Y', strtotime($post->sales_date));
                    $nestedData['taxable_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->sales_item_taxable_value, 2);
                    $nestedData['hsn_code'] = $post->product_hsn_sac_code;
                    $nestedData['quantity'] = round($post->sales_item_quantity, 2);
                    $nestedData['uom'] = $post->uom ? $post->uom : '-';
                    $nestedData['rate'] = $this->precise_amount($post->sales_item_unit_price, 2);
                    $nestedData['invoice_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->sales_grand_total, 2);
                    if ($post->state_name == '' && $post->sales_billing_state_id == 0) {
                        $state_name = 'Outside India';
                    } else {
                        $state_name = $post->state_name;
                    }
                    $nestedData['sgst_amount'] = $this->precise_amount($post->sales_item_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst_amount'] = $this->precise_amount($post->sales_item_sgst_amount, 2);
                        $nestedData['sgst_amount'] = $this->precise_amount(0, 2);
                    } else {
                        $nestedData['sgst_amount'] = $this->precise_amount($post->sales_item_sgst_amount, 2);
                        $nestedData['utgst_amount'] = $this->precise_amount(0, 2);
                    }*/
                    //echo $post->is_utgst;
                    //   $nestedData['utgst_amount'] = round($post->sales_item_sgst_amount,2);
                    //$nestedData['sgst_amount'] = 0;
                    //$nestedData['sgst_amount'] = round($post->sales_item_sgst_amount,2);
                    //    $nestedData['utgst_amount'] = 0;
                    $nestedData['place_of_supply'] = $state_name;
                    $nestedData['tax_rate'] = (float)($gst_percentage) . "%";
                    $nestedData['cgst_amount'] = $this->precise_amount($post->sales_item_cgst_amount, 2);

                    $nestedData['igst_amount'] = $this->precise_amount($post->sales_item_igst_amount, 2);

                    $nestedData['cess_rate'] = (float)($post->sales_item_tax_cess_percentage) . "%";
                    $nestedData['cess_amount'] = $this->precise_amount($post->sales_item_tax_cess_amount, 2);
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_customer_gst_sales();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_customer_gst_sales_gstin();
            $data['customers_gstin'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_gst_sales();
            $data['voucher_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_hsn_sales();
            $data['hsn_code'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_price_sales();
            $data['unit_price'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_UOM_sales();
            $data['uom'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_place_of_supply_sales();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_gst_sales();
            $data['tax_percentage_gst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_cess_sales();
            $data['tax_percentage_cess'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_quantity_sales();
            $data['quantity'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $this->load->view('report/gst_report', $data);
        }
    }

    public function tcs_report_sales() {
        $report_module_id = $this->config->item('tcs_sales_report_module');
        $data['module_id'] = $report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'sales_date',
                1 => 'customer',
                2 => 'pan_number',
                3 => 'payable_us',
                4 => 'rate_of_tax',
                5 => 'taxable_value',
                6 => 'amount',
            );

            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->tcs_report_sales_list();

            $list_data['search'] = 'all';
            $list_data['section'] = 'tcs_sales_payable';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            if ($this->input->post('filter_customer') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_pan') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_tcs_amount') != "" || $this->input->post('filter_to_tcs_amount') != "" || $this->input->post('filter_tcs_section') != "" || $this->input->post('filter_tcs_percentage') != "") {
                $filter_search['filter_customer'] = ($this->input->post('filter_customer') == '' ? '' : implode(",", $this->input->post('filter_customer')));

                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));

                $filter_search['filter_pan'] = ($this->input->post('filter_pan') == '' ? '' : implode(",", $this->input->post('filter_pan')));
                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_tcs_amount'] = $this->input->post('filter_from_tcs_amount');
                $filter_search['filter_to_tcs_amount'] = $this->input->post('filter_to_tcs_amount');
                $filter_search['filter_tcs_section'] = ($this->input->post('filter_tcs_section') == '' ? '' : implode(",", $this->input->post('filter_tcs_section')));
                $filter_search['filter_tcs_percentage'] = ($this->input->post('filter_tcs_percentage') == '' ? '' : implode(",", $this->input->post('filter_tcs_percentage')));

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.taxable_value) as tot_taxable_value, sum(t.amount) as tot_tds_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $payable = '';
                    if($post->section_name != ''){
                        $payable = 'TCS payable u/s' . $post->section_name;
                    }
                    $pan_number = '-';
                    if ($post->customer_pan_number != '') {
                        $pan_number = $post->customer_pan_number;
                    } else if ($post->customer_gstin_number != '') {
                        $pan_number = substr($post->customer_gstin_number, 2, 10);
                    }

                    $nestedData['sales_date'] = date('d-m-Y', strtotime($post->sales_date));
                    ;
                    $nestedData['customer'] = $post->customer_name;
                    $nestedData['pan_number'] = $pan_number;
                    $nestedData['payable_us'] = $payable;
                    $nestedData['rate_of_tax'] = (float)($post->sales_item_tds_percentage) . "%";
                    $nestedData['taxable_value'] = $this->precise_amount($post->taxable_value, 2);
                    $nestedData['amount'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->amount, 2);

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_customer_tcs_sales();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_tcs_section_sales();
            $data['tcs_section'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_tcs_percentage_sales();
            $data['tcs_percentage'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_customer_tcs_pan_sales();
            $data['customer_pan'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $this->load->view('report/tcs_report_sales', $data);
        }
    }

    public function tds_report_sales() {
        $report_module_id = $this->config->item('tds_sales_report_module');
        $data['module_id'] = $report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'sales_date',
                1 => 'customer',
                2 => 'pan_number',
                3 => 'payable_us',
                4 => 'rate_of_tax',
                5 => 'taxable_value',
                6 => 'amount',
            );

            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->tds_report_sales_list();

            $list_data['search'] = 'all';
            $list_data['section'] = 'tds_sales_payable';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            if ($this->input->post('filter_customer') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_tan') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_tds_amount') != "" || $this->input->post('filter_to_tds_amount') != "" || $this->input->post('filter_tds_section') != "" || $this->input->post('filter_tds_percentage') != "") {
                $filter_search['filter_customer'] = ($this->input->post('filter_customer') == '' ? '' : implode(",", $this->input->post('filter_customer')));

                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));

                $filter_search['filter_tan'] = ($this->input->post('filter_tan') == '' ? '' : implode(",", $this->input->post('filter_tan')));
                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_tds_amount'] = $this->input->post('filter_from_tds_amount');
                $filter_search['filter_to_tds_amount'] = $this->input->post('filter_to_tds_amount');
                $filter_search['filter_tds_section'] = ($this->input->post('filter_tds_section') == '' ? '' : implode(",", $this->input->post('filter_tds_section')));
                $filter_search['filter_tds_percentage'] = ($this->input->post('filter_tds_percentage') == '' ? '' : implode(",", $this->input->post('filter_tds_percentage')));

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.taxable_value) as tot_taxable_value, sum(t.amount) as tot_tds_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $payable = '-';
                    if($post->section_name != ''){
                        $payable = 'TDS receivable u/s' . $post->section_name;
                    }
                    $pan_number = '-';
                    if ($post->customer_tan_number != '') {
                        $pan_number = $post->customer_tan_number;
                    }

                    $nestedData['sales_date'] = date('d-m-Y', strtotime($post->sales_date));
                    ;
                    $nestedData['customer'] = $post->customer_name;
                    $nestedData['pan_number'] = '-'/*$pan_number*/;
                    $nestedData['payable_us'] = $payable;
                    $nestedData['rate_of_tax'] = (float)($post->sales_item_tds_percentage) . "%";
                    $nestedData['taxable_value'] = $this->precise_amount($post->taxable_value, 2);
                    $nestedData['amount'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->amount, 2);

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_customer_tds_sales();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_tds_section_sales();
            $data['tds_section'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_tds_percentage_sales();
            $data['tds_percentage'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $this->load->view('report/tds_report_sales', $data);
        }
    }

    public function tcs_report_purchase() {
        $report_module_id = $this->config->item('tcs_purchase_report_module');
        $data['module_id'] = $report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'sales_date',
                1 => 'customer',
                2 => 'pan_number',
                3 => 'payable_us',
                4 => 'rate_of_tax',
                5 => 'taxable_value',
                6 => 'amount',
            );

            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->tcs_report_purchase_list();

            $list_data['search'] = 'all';
            $list_data['section'] = 'tcs_purcahse_recievable';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            if ($this->input->post('filter_customer') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_tan') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_tcs_amount') != "" || $this->input->post('filter_to_tcs_amount') != "" || $this->input->post('filter_tcs_section') != "" || $this->input->post('filter_tcs_percentage') != "") {
                $filter_search['filter_customer'] = ($this->input->post('filter_customer') == '' ? '' : implode(",", $this->input->post('filter_customer')));

                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));

                $filter_search['filter_tan'] = ($this->input->post('filter_tan') == '' ? '' : implode(",", $this->input->post('filter_tan')));
                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_tcs_amount'] = $this->input->post('filter_from_tcs_amount');
                $filter_search['filter_to_tcs_amount'] = $this->input->post('filter_to_tcs_amount');
                $filter_search['filter_tcs_section'] = ($this->input->post('filter_tcs_section') == '' ? '' : implode(",", $this->input->post('filter_tcs_section')));
                $filter_search['filter_tcs_percentage'] = ($this->input->post('filter_tcs_percentage') == '' ? '' : implode(",", $this->input->post('filter_tcs_percentage')));

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.taxable_value) as tot_taxable_value, sum(t.amount) as tot_tds_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $payable = 'TCS receivable u/s' . $post->section_name;

                    $nestedData['sales_date'] = date('d-m-Y', strtotime($post->purchase_date));
                    ;
                    $nestedData['customer'] = $post->supplier_name;
                    $nestedData['tan_number'] = $post->supplier_tan_number ? $post->supplier_tan_number: '-';
                    $nestedData['payable_us'] = $payable;
                    $nestedData['rate_of_tax'] = (float)($post->purchase_item_tds_percentage) . "%";
                    $nestedData['taxable_value'] = $this->precise_amount($post->taxable_value, 2);
                    $nestedData['amount'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->amount, 2);

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_supplier_tcs_purchase();
            $data['supplier'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_tcs_section_purchase();
            $data['tds_section'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tcs_percentage_purchase();
            $data['tcs_percentage'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $this->load->view('report/tcs_report_purchase', $data);
        }
    }

    public function tds_report_expense() {
        $report_module_id = $this->config->item('tds_expense_report_module');
        $data['module_id'] = $report_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($report_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'sales_date',
                1 => 'customer',
                2 => 'pan_number',
                3 => 'payable_us',
                4 => 'rate_of_tax',
                5 => 'taxable_value',
                6 => 'amount',
            );

            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->tds_report_expense_list();

            $list_data['search'] = 'all';
            $list_data['section'] = 'tds_expense_recievable';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            if ($this->input->post('filter_customer') != "" || $this->input->post('filter_from_date') != "" || $this->input->post('filter_to_date') != "" || $this->input->post('filter_tan') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_tcs_amount') != "" || $this->input->post('filter_to_tcs_amount') != "" || $this->input->post('filter_tcs_section') != "" || $this->input->post('filter_tcs_percentage') != "") {
                $filter_search['filter_customer'] = ($this->input->post('filter_customer') == '' ? '' : implode(",", $this->input->post('filter_customer')));

                $filter_search['filter_from_date'] = ($this->input->post('filter_from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_from_date'))));
                $filter_search['filter_to_date'] = ($this->input->post('filter_to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('filter_to_date'))));

                $filter_search['filter_tan'] = ($this->input->post('filter_tan') == '' ? '' : implode(",", $this->input->post('filter_tan')));

                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');
                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');
                $filter_search['filter_from_tcs_amount'] = $this->input->post('filter_from_tcs_amount');
                $filter_search['filter_to_tcs_amount'] = $this->input->post('filter_to_tcs_amount');
                $filter_search['filter_tcs_section'] = ($this->input->post('filter_tcs_section') == '' ? '' : implode(",", $this->input->post('filter_tcs_section')));
                $filter_search['filter_tcs_percentage'] = ($this->input->post('filter_tcs_percentage') == '' ? '' : implode(",", $this->input->post('filter_tcs_percentage')));

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.taxable_value) as tot_taxable_value, sum(t.amount) as tot_tds_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $payable = '-';
                    if($post->section_name != ''){
                        $payable = 'TDS receivable u/s' . $post->section_name;
                    } 

                    $nestedData['sales_date'] = date('d-m-Y', strtotime($post->expense_bill_date));
                    ;
                    $nestedData['customer'] = $post->supplier_name;
                    $nestedData['pan_number'] = $post->supplier_tan_number ? $post->supplier_tan_number: '-';
                    $nestedData['payable_us'] = $payable;
                    $nestedData['rate_of_tax'] = (float)($post->expense_bill_item_tds_percentage) . "%";
                    $nestedData['taxable_value'] = $this->precise_amount($post->taxable_value, 2);
                    $nestedData['amount'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->amount, 2);

                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_supplier_tds_expense();
            $data['supplier'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_tds_section_expense();
            $data['tds_section'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tds_percentage_expense();
            $data['tcs_percentage'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $this->load->view('report/tds_report_expense', $data);
        }
    }

    public function gst_credit_note_report() {
        $sales_module_id = $this->config->item('gst_sales_credit_note_report');
        $data['module_id'] = $sales_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');
        $sales_module_id = $this->config->item('sales_module');
        $sales_credit_note_module_id = $this->config->item('sales_credit_note_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);

        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/


        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'voucher_number',
                1 => 'customer_name',
                2 => 'gstin',
                3 => 'invoice_number',
                4 => 'invoice_date',
                5 => 'taxable_value',
                6 => 'hsn_code',
                7 => 'quantity',
                8 => 'uom',
                9 => 'rate',
                10 => 'place_of_supply',
                11 => 'tax_rate',
                12 => 'cgst_amount',
                13 => 'sgst_amount',
                14 => 'igst_amount',
                15 => 'utgst_amount',
                16 => 'cess_rate',
                17 => 'cess_amount',
                18 => 'invoice_value'
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->gst_credit_note_list_field();
            $list_data['where'] = array(
                'S.delete_status' => 0,
                'S.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'S.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'SI.sales_credit_note_item_tax_percentage >' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'sales_credit_note_gst';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            if ($this->input->post('filter_voucher') != "" || $this->input->post('filter_customer') != "" || $this->input->post('filter_gstin') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('from_date') != "" || $this->input->post('to_date') != "" || $this->input->post('from_invoice_amount') != "" || $this->input->post('to_invoice_amount') != "" || $this->input->post('from_taxable_amount') != "" || $this->input->post('to_taxable_amount') != "" || $this->input->post('filter_hsn_number') != "" || $this->input->post('filter_quantity') != "" || $this->input->post('filter_uom') != "" || $this->input->post('filter_from_price') != "" || $this->input->post('filter_to_price') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_gst_rate') != "" || $this->input->post('from_cgst_amount') != "" || $this->input->post('to_cgst_amount') != "" || $this->input->post('from_sgst_amount') != "" || $this->input->post('to_sgst_amount') != "" || $this->input->post('from_igst_amount') != "" || $this->input->post('to_igst_amount') != "" || $this->input->post('from_ugst_amount') != "" || $this->input->post('to_ugst_amount') != "" || $this->input->post('filter_cess_rate') != "" || $this->input->post('from_cess_amount') != "" || $this->input->post('to_cess_amount') != "") {
                $filter_search = array();

                $filter_search['filter_voucher'] = ($this->input->post('filter_voucher') == '' ? '' : implode(",", $this->input->post('filter_voucher')));

                $filter_search['filter_customer'] = ($this->input->post('filter_customer') == '' ? '' : implode(",", $this->input->post('filter_customer')));

                $filter_search['filter_gstin'] = ($this->input->post('filter_gstin') == '' ? '' : implode(",", $this->input->post('filter_gstin')));

                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));

                $filter_search['from_date'] = ($this->input->post('from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('from_date'))));
                $filter_search['to_date'] = ($this->input->post('to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('to_date'))));

                $filter_search['from_invoice_amount'] = $this->input->post('from_invoice_amount');

                $filter_search['to_invoice_amount'] = $this->input->post('to_invoice_amount');

                $filter_search['from_taxable_amount'] = $this->input->post('from_taxable_amount');

                $filter_search['to_taxable_amount'] = $this->input->post('to_taxable_amount');

                $filter_search['filter_hsn_number'] = ($this->input->post('filter_hsn_number') == '' ? '' : implode(",", $this->input->post('filter_hsn_number')));


                $filter_search['filter_quantity'] = ($this->input->post('filter_quantity') == '' ? '' : implode(",", $this->input->post('filter_quantity')));

                $filter_search['filter_uom'] = ($this->input->post('filter_uom') == '' ? '' : implode(",", $this->input->post('filter_uom')));

                $filter_search['filter_from_price'] = $this->input->post('filter_from_price');
                $filter_search['filter_to_price'] = $this->input->post('filter_to_price');

                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));

                $filter_search['filter_gst_rate'] = ($this->input->post('filter_gst_rate') == '' ? '' : implode(",", $this->input->post('filter_gst_rate')));

                $filter_search['from_cgst_amount'] = $this->input->post('from_cgst_amount');

                $filter_search['to_cgst_amount'] = $this->input->post('to_cgst_amount');

                $filter_search['from_sgst_amount'] = $this->input->post('from_sgst_amount');

                $filter_search['to_sgst_amount'] = $this->input->post('to_sgst_amount');

                $filter_search['from_igst_amount'] = $this->input->post('from_igst_amount');

                $filter_search['to_igst_amount'] = $this->input->post('to_igst_amount');

                $filter_search['from_ugst_amount'] = $this->input->post('from_ugst_amount');

                $filter_search['to_ugst_amount'] = $this->input->post('to_ugst_amount');

                $filter_search['filter_cess_rate'] = ($this->input->post('filter_cess_rate') == '' ? '' : implode(",", $this->input->post('filter_cess_rate')));

                $filter_search['from_cess_amount'] = $this->input->post('from_cess_amount');

                $filter_search['to_cess_amount'] = $this->input->post('to_cess_amount');

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.sales_credit_note_item_sub_total) as tot_taxable_value, sum(t.sales_credit_note_grand_total) as invoice_value, sum(t.sales_credit_note_item_igst_amount) as igst_amount, sum(t.sales_credit_note_item_cgst_amount) as cgst_amount, sum(t.SGST) as sgst_amount, sum(t.UTGST) as utgst_amount, sum(t.sales_credit_note_item_tax_cess_amount) as cess_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);

            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $sales_credit_note_id = $this->encryption_url->encode($post->sales_credit_note_id);
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $gst_percentage = $post->sales_credit_note_item_cgst_percentage + $post->sales_credit_note_item_sgst_percentage + $post->sales_credit_note_item_igst_percentage;
                    $nestedData['voucher_number'] = $post->sales_credit_note_invoice_number;
                    if(in_array($sales_credit_note_module_id, $data['active_view'])){
                        $nestedData['voucher_number'] = '<a href="' . base_url('sales_credit_note/view/') . $sales_credit_note_id . '">' . $post->sales_credit_note_invoice_number . '</a>';
                    }
                    $nestedData['customer_name'] = $post->customer_name;
                    $nestedData['gstin'] = $post->customer_gstin_number ? $post->customer_gstin_number:'-';
                    $nestedData['invoice_number'] = $post->sales_invoice_number;
                    if(in_array($sales_module_id, $data['active_view'])){
                        $nestedData['invoice_number'] = '<a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>';
                    }
                    $nestedData['invoice_date'] = date('d-m-Y', strtotime($post->sales_credit_note_date));
                    $nestedData['taxable_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->sales_credit_note_item_sub_total, 2);
                    $nestedData['hsn_code'] = $post->product_hsn_sac_code;
                    $nestedData['quantity'] = round($post->sales_credit_note_item_quantity, 2);
                    $nestedData['uom'] = $post->uom ? $post->uom : '-';
                    $nestedData['rate'] = $this->precise_amount($post->sales_credit_note_item_unit_price, 2);
                    $nestedData['invoice_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->sales_credit_note_grand_total, 2);
                    if ($post->state_name == '' && $post->sales_credit_note_billing_state_id == 0) {
                        $state_name = 'Outside India';
                    } else {
                        $state_name = $post->state_name;
                    }
                    $nestedData['sgst_amount'] = $this->precise_amount($post->sales_credit_note_item_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst_amount'] = $this->precise_amount($post->sales_credit_note_item_sgst_amount, 2);
                        $nestedData['sgst_amount'] = $this->precise_amount(0,2);
                    } else {
                        $nestedData['sgst_amount'] = $this->precise_amount($post->sales_credit_note_item_sgst_amount, 2);
                        $nestedData['utgst_amount'] = $this->precise_amount(0,2);
                    }*/

                    $nestedData['place_of_supply'] = $state_name;
                    $nestedData['tax_rate'] = (float)($gst_percentage) . "%";
                    $nestedData['cgst_amount'] = $this->precise_amount($post->sales_credit_note_item_cgst_amount, 2);

                    $nestedData['igst_amount'] = $this->precise_amount($post->sales_credit_note_item_igst_amount, 2);

                    $nestedData['cess_rate'] = (float)($post->sales_credit_note_item_tax_cess_percentage) . "%";
                    $nestedData['cess_amount'] = $this->precise_amount($post->sales_credit_note_item_tax_cess_amount, 2);
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_customer_gst_sales_credit_note();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_customer_gst_sales_credit_note_gstin();
            $data['customers_gstin'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_gst_sales_credit_note();
            $data['voucher_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_gst_sales_credit_note_invoice();
            $data['invoice_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_hsn_sales_credit_note();
            $data['hsn_code'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_price_sales_credit_note();
            $data['unit_price'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_UOM_sales_credit_note();
            $data['uom'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_place_of_supply_sales_credit_note();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_gst_sales_credit_note();
            $data['tax_percentage_gst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_cess_sales_credit_note();
            $data['tax_percentage_cess'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_quantity_sales_credit_note();
            $data['quantity'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $this->load->view('report/gst_credit_note_report', $data);
        }
    }

    public function gst_debit_note_report() {
        $sales_module_id = $this->config->item('gst_sales_debit_note_report');
        $data['module_id'] = $sales_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');

        $sales_module_id = $this->config->item('sales_module');
        $sales_debit_note_module_id = $this->config->item('sales_debit_note_module');
       /* $user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);


        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'voucher_number',
                1 => 'customer_name',
                2 => 'gstin',
                3 => 'invoice_number',
                4 => 'invoice_date',
                5 => 'taxable_value',
                6 => 'hsn_code',
                7 => 'quantity',
                8 => 'uom',
                9 => 'rate',
                10 => 'place_of_supply',
                11 => 'tax_rate',
                12 => 'cgst_amount',
                13 => 'sgst_amount',
                14 => 'igst_amount',
                15 => 'utgst_amount',
                16 => 'cess_rate',
                17 => 'cess_amount',
                18 => 'invoice_value'
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->gst_debit_note_list_field();
            $list_data['where'] = array(
                'S.delete_status' => 0,
                'S.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'S.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'SI.sales_debit_note_item_tax_percentage >' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'sales_debit_note_gst';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;

            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            if ($this->input->post('filter_voucher') != "" || $this->input->post('filter_customer') != "" || $this->input->post('filter_gstin') != "" || $this->input->post('filter_invoice_number') != "" || $this->input->post('from_date') != "" || $this->input->post('to_date') != "" || $this->input->post('from_invoice_amount') != "" || $this->input->post('to_invoice_amount') != "" || $this->input->post('from_taxable_amount') != "" || $this->input->post('to_taxable_amount') != "" || $this->input->post('filter_hsn_number') != "" || $this->input->post('filter_quantity') != "" || $this->input->post('filter_uom') != "" || $this->input->post('filter_from_price') != "" || $this->input->post('filter_to_price') != "" || $this->input->post('filter_place_of_supply') != "" || $this->input->post('filter_gst_rate') != "" || $this->input->post('from_cgst_amount') != "" || $this->input->post('to_cgst_amount') != "" || $this->input->post('from_sgst_amount') != "" || $this->input->post('to_sgst_amount') != "" || $this->input->post('from_igst_amount') != "" || $this->input->post('to_igst_amount') != "" || $this->input->post('from_ugst_amount') != "" || $this->input->post('to_ugst_amount') != "" || $this->input->post('filter_cess_rate') != "" || $this->input->post('from_cess_amount') != "" || $this->input->post('to_cess_amount') != "") {
                $filter_search = array();

                $filter_search['filter_voucher'] = ($this->input->post('filter_voucher') == '' ? '' : implode(",", $this->input->post('filter_voucher')));

                $filter_search['filter_customer'] = ($this->input->post('filter_customer') == '' ? '' : implode(",", $this->input->post('filter_customer')));

                $filter_search['filter_gstin'] = ($this->input->post('filter_gstin') == '' ? '' : implode(",", $this->input->post('filter_gstin')));

                $filter_search['filter_invoice_number'] = ($this->input->post('filter_invoice_number') == '' ? '' : implode(",", $this->input->post('filter_invoice_number')));

                $filter_search['from_date'] = ($this->input->post('from_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('from_date'))));
                $filter_search['to_date'] = ($this->input->post('to_date') == '' ? '' : date('Y-m-d', strtotime($this->input->post('to_date'))));

                $filter_search['from_invoice_amount'] = $this->input->post('from_invoice_amount');

                $filter_search['to_invoice_amount'] = $this->input->post('to_invoice_amount');

                $filter_search['from_taxable_amount'] = $this->input->post('from_taxable_amount');

                $filter_search['to_taxable_amount'] = $this->input->post('to_taxable_amount');

                $filter_search['filter_hsn_number'] = ($this->input->post('filter_hsn_number') == '' ? '' : implode(",", $this->input->post('filter_hsn_number')));


                $filter_search['filter_quantity'] = ($this->input->post('filter_quantity') == '' ? '' : implode(",", $this->input->post('filter_quantity')));

                $filter_search['filter_uom'] = ($this->input->post('filter_uom') == '' ? '' : implode(",", $this->input->post('filter_uom')));

                $filter_search['filter_from_price'] = $this->input->post('filter_from_price');
                $filter_search['filter_to_price'] = $this->input->post('filter_to_price');

                $filter_search['filter_place_of_supply'] = ($this->input->post('filter_place_of_supply') == '' ? '' : implode(",", $this->input->post('filter_place_of_supply')));

                $filter_search['filter_gst_rate'] = ($this->input->post('filter_gst_rate') == '' ? '' : implode(",", $this->input->post('filter_gst_rate')));

                $filter_search['from_cgst_amount'] = $this->input->post('from_cgst_amount');

                $filter_search['to_cgst_amount'] = $this->input->post('to_cgst_amount');

                $filter_search['from_sgst_amount'] = $this->input->post('from_sgst_amount');

                $filter_search['to_sgst_amount'] = $this->input->post('to_sgst_amount');

                $filter_search['from_igst_amount'] = $this->input->post('from_igst_amount');

                $filter_search['to_igst_amount'] = $this->input->post('to_igst_amount');

                $filter_search['from_ugst_amount'] = $this->input->post('from_ugst_amount');

                $filter_search['to_ugst_amount'] = $this->input->post('to_ugst_amount');

                $filter_search['filter_cess_rate'] = ($this->input->post('filter_cess_rate') == '' ? '' : implode(",", $this->input->post('filter_cess_rate')));

                $filter_search['from_cess_amount'] = $this->input->post('from_cess_amount');

                $filter_search['to_cess_amount'] = $this->input->post('to_cess_amount');

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }
            $string = "sum(t.sales_debit_note_item_sub_total) as tot_taxable_value, sum(t.sales_debit_note_grand_total) as invoice_value, sum(t.sales_debit_note_item_igst_amount) as igst_amount, sum(t.sales_debit_note_item_cgst_amount) as cgst_amount, sum(t.SGST) as sgst_amount, sum(t.UTGST) as utgst_amount, sum(t.sales_debit_note_item_tax_cess_amount) as cess_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string);
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $sales_debit_note_id = $this->encryption_url->encode($post->sales_debit_note_id);
                    $sales_id = $this->encryption_url->encode($post->sales_id);
                    $gst_percentage = $post->sales_debit_note_item_cgst_percentage + $post->sales_debit_note_item_sgst_percentage + $post->sales_debit_note_item_igst_percentage;
                    $nestedData['voucher_number'] = $post->sales_debit_note_invoice_number;
                    if(in_array($sales_debit_note_module_id, $data['active_view'])){
                        $nestedData['voucher_number'] = '<a href="' . base_url('sales_debit_note/view/') . $sales_debit_note_id . '">' . $post->sales_debit_note_invoice_number . '</a>';
                    }
                    $nestedData['customer_name'] = $post->customer_name;
                    $nestedData['gstin'] = $post->customer_gstin_number ? $post->customer_gstin_number : '-';
                    $nestedData['invoice_number'] = $post->sales_invoice_number;
                    if(in_array($sales_module_id, $data['active_view'])){
                        $nestedData['invoice_number'] = '<a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>';
                    }
                    $nestedData['invoice_date'] = date('d-m-Y', strtotime($post->sales_debit_note_date));
                    $nestedData['taxable_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->sales_debit_note_item_sub_total, 2);
                    $nestedData['hsn_code'] = $post->product_hsn_sac_code;
                    $nestedData['quantity'] = round($post->sales_debit_note_item_quantity, 2);
                    $nestedData['uom'] = $post->uom ? $post->uom :'-';
                    $nestedData['rate'] = $this->precise_amount($post->sales_debit_note_item_unit_price, 2);
                    $nestedData['invoice_value'] = $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') .' '.$this->precise_amount($post->sales_debit_note_grand_total, 2);
                    if ($post->state_name == '' && $post->sales_debit_note_billing_state_id == 0) {
                        $state_name = 'Outside India';
                    } else {
                        $state_name = $post->state_name;
                    }
                    $nestedData['sgst_amount'] = $this->precise_amount($post->sales_debit_note_item_sgst_amount, 2);
                    /*if ($post->is_utgst == 1) {
                        $nestedData['utgst_amount'] = $this->precise_amount($post->sales_debit_note_item_sgst_amount, 2);
                        $nestedData['sgst_amount'] = $this->precise_amount(0, 2);
                    } else {
                        $nestedData['sgst_amount'] = $this->precise_amount($post->sales_debit_note_item_sgst_amount, 2);
                        $nestedData['utgst_amount'] = $this->precise_amount(0, 2);
                    }*/
                    $nestedData['place_of_supply'] = $state_name;
                    $nestedData['tax_rate'] = (float)($gst_percentage) . "%";
                    $nestedData['cgst_amount'] = $this->precise_amount($post->sales_debit_note_item_cgst_amount, 2);
                    $nestedData['igst_amount'] = $this->precise_amount($post->sales_debit_note_item_igst_amount, 2);
                    $nestedData['cess_rate'] = (float)($post->sales_debit_note_item_tax_cess_percentage) . "%";
                    $nestedData['cess_amount'] = $this->precise_amount($post->sales_debit_note_item_tax_cess_amount, 2);
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_customer_gst_sales_debit_note();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_customer_gst_sales_debit_note_gstin();
            $data['customers_gstin'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_gst_sales_debit_note();
            $data['voucher_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_gst_sales_debit_note_reference();
            $data['invoice_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_hsn_sales_debit_note();
            $data['hsn_code'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_price_sales_debit_note();
            $data['unit_price'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_UOM_sales_debit_note();
            $data['uom'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_place_of_supply_sales_debit_note();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_gst_sales_debit_note();
            $data['tax_percentage_gst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_cess_sales_debit_note();
            $data['tax_percentage_cess'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_quantity_sales_debit_note();
            $data['quantity'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $this->load->view('report/gst_debit_note_report', $data);
        }
    }

    public function hsn_report() {
        $sales_module_id = $this->config->item('hsn_report_module');
        $data['module_id'] = $sales_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($sales_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $recurrence_sub_module_id = $this->config->item('recurrence_sub_module');

        /*$user_module_id = $this->config->item('user_module');
        $data['module_id'] = $user_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($user_module_id, $modules, $privilege);


        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;*/
        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'hsn',
                1 => 'description',
                2 => 'uqc',
                3 => 'total_quantity',
                4 => 'total_value',
                5 => 'taxable_value',
                6 => 'integrated_tax_amount',
                7 => 'central_tax_amount',
                8 => 'state_tax_amount',
                9 => 'cess_amount'
                
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->hsn_report_list_field();
            $list_data['where'] = array(
                'S.delete_status' => 0,
                'S.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'S.financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'S.sales_grand_total !=' => 0
            );
            $list_data['search'] = 'all';
            $list_data['section'] = 'sales_hsn';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            /*print_r($this->db->last_query());*/
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }



            if ($this->input->post('filter_hsn') != "" || $this->input->post('filter_uqc') != "" || $this->input->post('filter_quantity') != "" || $this->input->post('filter_from_total_amount') != "" || $this->input->post('filter_to_total_amount') != "" || $this->input->post('filter_from_taxable_amount') != "" || $this->input->post('filter_to_taxable_amount') != "" || $this->input->post('filter_from_igst_amount') != "" || $this->input->post('filter_from_cgst_amount') != "" || $this->input->post('filter_from_sgst_amount') != "" || $this->input->post('filter_to_igst_amount') != "" || $this->input->post('filter_to_cgst_amount') != "" || $this->input->post('filter_to_sgst_amount') != "" || $this->input->post('filter_from_cess_amount') != "" || $this->input->post('filter_to_cess_amount') != "" ) {
                $filter_search = array();

                $filter_search['filter_hsn'] = ($this->input->post('filter_hsn') == '' ? '' : implode(",", $this->input->post('filter_hsn')));

                $filter_search['filter_uqc'] = ($this->input->post('filter_uqc') == '' ? '' : implode(",", $this->input->post('filter_uqc')));

                $filter_search['filter_quantity'] = ($this->input->post('filter_quantity') == '' ? '' : implode(",", $this->input->post('filter_quantity')));

                $filter_search['filter_from_total_amount'] = $this->input->post('filter_from_total_amount');

                $filter_search['filter_to_total_amount'] = $this->input->post('filter_to_total_amount');

                $filter_search['filter_from_taxable_amount'] = $this->input->post('filter_from_taxable_amount');

                $filter_search['filter_to_taxable_amount'] = $this->input->post('filter_to_taxable_amount');

                $filter_search['filter_from_igst_amount'] = $this->input->post('filter_from_igst_amount');

                $filter_search['filter_from_cgst_amount'] = $this->input->post('filter_from_cgst_amount');

                $filter_search['filter_from_sgst_amount'] = $this->input->post('filter_from_sgst_amount');

                $filter_search['filter_to_igst_amount'] = $this->input->post('filter_to_igst_amount');

                $filter_search['filter_to_cgst_amount'] = $this->input->post('filter_to_cgst_amount');

                $filter_search['filter_to_sgst_amount'] = $this->input->post('filter_to_sgst_amount');

                $filter_search['filter_from_cess_amount'] = $this->input->post('filter_from_cess_amount');

                $filter_search['filter_to_cess_amount'] = $this->input->post('filter_to_cess_amount');

                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }




            $string = "sum(t.sales_item_taxable_value) as tot_taxable_value, sum(t.sales_item_sub_total) as invoice_value, sum(t.sales_item_igst_amount) as igst_amount, sum(t.sales_item_cgst_amount) as cgst_amount, sum(t.sales_item_sgst_amount) as sgst_amount, sum(t.sales_item_tax_cess_amount) as cess_amount";
            $total_list_record = $this->general_model->getPageJoinRecordsCount1($list_data, $string); 
            $send_data = array();//echo "<pre>";print_r($total_list_record);die;
            if (!empty($posts)) {
                foreach ($posts as $post) {// echo "<pre>";print_r($posts);die;
                    $gst_percentage = $post->sales_item_cgst_percentage + $post->sales_item_sgst_percentage + $post->sales_item_igst_percentage;
                    $nestedData['hsn'] = $post->hsn_sac_code;
                    $nestedData['description'] = "-";
                    $nestedData['uqc'] = $post->T_uom;
                    $nestedData['total_quantity'] = $post->sales_item_quantity;
                    $nestedData['total_value'] = number_format($post->sales_item_sub_total, 2);
                    $nestedData['taxable_value'] = number_format($post->sales_item_taxable_value, 2);
                    $nestedData['integrated_tax_amount'] = number_format($post->sales_item_igst_amount, 2);
                    $nestedData['central_tax_amount'] = number_format($post->sales_item_cgst_amount, 2);
                    $nestedData['state_tax_amount'] = number_format($post->sales_item_sgst_amount, 2);
                    $nestedData['cess_amount'] = number_format($post->sales_item_tax_cess_amount, 2);
                    
                    
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data,
                "total_list_record" => $total_list_record);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_hsn_number();
            $data['hsn_code'] = $this->general_model->getJoinRecords($list_data['string'], 
            $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_hsn_UOM_sales(); 
            $data['uom'] = $this->general_model->getJoinRecords($list_data['string'], 
            $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = 
            "", $list_data['group']);

            $list_data = $this->common->distinct_hsn_quantity_sales();
            $data['quantity'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            /*$list_data = $this->common->distinct_customer_gst_sales();
            $data['customers'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $list_data = $this->common->distinct_customer_gst_sales_gstin();
            $data['customers_gstin'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_gst_sales();
            $data['voucher_number'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_voucher_hsn_sales();
            $data['hsn_code'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_price_sales();
            $data['unit_price'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_UOM_sales();
            $data['uom'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_place_of_supply_sales();
            $data['place_of_supply'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_gst_sales();
            $data['tax_percentage_gst'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_tax_percentage_cess_sales();
            $data['tax_percentage_cess'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);

            $list_data = $this->common->distinct_gst_quantity_sales();
            $data['quantity'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);*/

            $this->load->view('reports/hsn_report', $data);
        }
    }
}
