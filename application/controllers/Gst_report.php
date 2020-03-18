<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . "vendor/autoload.php";

class Gst_report extends MY_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('report_model');
        $this->modules = $this->get_modules();
    }
    function index($id= 0){
        $report_module_id         = $this->config->item('report_module');
        $data['module_id']               = $report_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($report_module_id, $modules, $privilege);
        $data['path_data'] = array();
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $this->load->view('gstr/list', $data);
    }
    public function download_report(){
        $report_module_id         = $this->config->item('report_module');
        $data['module_id']               = $report_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($report_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $gstr_filter_type = $this->input->post('gstr_filter_type');
        $from_month = $this->input->post('from_month');
        $from_date = $this->input->post('from_date');
        $from_date = date('Y-m-d', strtotime($from_date));
        $to_date = $this->input->post('to_date');
        $to_date = date('Y-m-d', strtotime($to_date));
        $gst_report_type = $this->input->post('gst_report_type');
        $download_format_type = $this->input->post('download_format_type');
        
        if(!empty($from_month) || $from_month != ''){
            $from_date = '01-'.$from_month;
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-t', strtotime($from_date));
        }
        $gstr_report = array();
        if($download_format_type == 'excel') {  /*!empty($_POST['report_type'])*/
            $i = 0;
            foreach($_POST['report_type'] as $gst_report_type) {
                if($gst_report_type == 'gstr1_advance'){
                    $gstr_report['Advance'] = $this->advances_report($from_date,$to_date); 
                }else if($gst_report_type == 'gstr1_b2cl'){
                    $gstr_report['b2cl'] = $this->b2cl_report($from_date,$to_date);
                }else if($gst_report_type == 'gstr1_b2cs'){
                    $gstr_report['B2Cs'] = $this->b2cs_report($from_date,$to_date);
                }else if($gst_report_type == 'credit_debit_note_b2b'){
                    $gstr_report['Credit Debit Note B2B'] = $this->credit_debit_note_b2b_report($from_date,$to_date);
                }else if($gst_report_type == 'credit_debit_note_b2c'){
                    $gstr_report['Credit Debit Note B2C'] = $this->credit_debit_note_b2c_report($from_date,$to_date);
                }else if($gst_report_type == 'gstr1_exports'){
                    $gstr_report['Exports'] = $this->exports_report($from_date,$to_date);
                }else if($gst_report_type == 'hsn_summary'){
                    $gstr_report['HSN Summary'] = $this->hsn_summary_report($from_date,$to_date);
                }else if($gst_report_type == 'documents'){
                    $gstr_report['Documents'] = $this->gst_documents_report($from_date,$to_date);
                }else if($gst_report_type == 'exempt_supply'){
                    $gstr_report['Exempt Supply'] = $this->gst_exempt_supply_report($from_date,$to_date);
                }else if($gst_report_type == 'credit_debit_note_b2cs'){
                    $gstr_report['Credit Debit Note B2Cs'] = $this->credit_debit_note_b2cs_report($from_date,$to_date);
                }
                $i++;
            }
            $this->exportGstr1ReportExcel($gstr_report);
        }

        if($download_format_type == 'csv' || $download_format_type == 'pdf'){

            $gstr_report['Advance'] = $this->advances_report($from_date,$to_date);
            $gstr_report['b2cl'] = $this->b2cl_report($from_date,$to_date);
            $gstr_report['B2Cs'] = $this->b2cs_report($from_date,$to_date);
            $gstr_report['Credit Debit Note B2B'] = $this->credit_debit_note_b2b_report($from_date,$to_date);
            $gstr_report['Credit Debit Note B2C'] = $this->credit_debit_note_b2c_report($from_date,$to_date);
            $gstr_report['Credit Debit Note B2Cs'] = $this->credit_debit_note_b2cs_report($from_date,$to_date);
            $gstr_report['Exports'] = $this->exports_report($from_date,$to_date);
            $gstr_report['HSN Summary'] = $this->hsn_summary_report($from_date,$to_date);
            $gstr_report['Documents'] = $this->gst_documents_report($from_date,$to_date);
            $gstr_report['Exempt Supply'] = $this->gst_exempt_supply_report($from_date,$to_date);
            $report_data = array();
            if($download_format_type == 'csv'){
                $report_data['path_data'] = $this->exportGstr1ReportCsv($gstr_report);
                $report_data['report_format'] = 'csv';
            }
            if($download_format_type == 'pdf'){
                $report_data['path_data'] = $this->PDFDownloadGstr1Report($gstr_report);
                $report_data['report_format'] = 'pdf';
            }
            echo json_encode($report_data);
        }
    }
    /*public function ajax_count_reports(){
        $gstr_filter_type = $this->input->post('gstr_filter_type');
        $from_month = $this->input->post('from_month');
        $from_date = $this->input->post('from_date');
        $from_date = date('Y-m-d', strtotime($from_date));
        $to_date = $this->input->post('to_date');
        $to_date = date('Y-m-d', strtotime($to_date));
        $gst_report_type = $this->input->post('gst_report_type');
        $download_format_type = $this->input->post('download_format_type');
        if(!empty($from_month) || $from_month != ''){
            $from_date = '01-'.$from_month;
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-t', strtotime($from_date));
        }
        $totalData = 0;
        if($gst_report_type == 'gstr1_advance'){
            $list_data = $this->common->advance_gst_report_list_field($from_date,$to_date);  
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data); 
        }else if($gst_report_type == 'gstr1_b2cl'){
            $list_data = $this->common->gst_b2cl_report_list_field($from_date,$to_date);
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
        }else if($gst_report_type == 'gstr1_b2cs'){
            $list_data = $this->common->gst_b2cs_report_list_field($from_date,$to_date);
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
        }else if($gst_report_type == 'credit_debit_note_b2b'){
            $list_data = $this->common->credit_debit_note_b2b_report_list_field($from_date,$to_date);
            $totalData = $this->general_model->getQueryRecords($list_data);
            $totalData = sizeof($totalData);
        }else if($gst_report_type == 'credit_debit_note_b2c'){
            $list_data = $this->common->credit_debit_note_b2c_report_list_field($from_date,$to_date);
            $totalData = $this->general_model->getQueryRecords($list_data);
            $totalData = sizeof($totalData);
        }else if($gst_report_type == 'gstr1_exports'){
            $list_data = $this->common->exports_report_list_field($from_date,$to_date);
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
        }else if($gst_report_type == 'hsn_summary'){
            $list_data = $this->common->gst_hsn_summary_list_field($from_date,$to_date);
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
        }else if($gst_report_type == 'documents'){
            $totalData = $this->gst_documents_report_count($download_format_type,$from_date,$to_date);
            $totalData = sizeof($totalData);
        }else if($gst_report_type == 'exempt_supply'){
            //$list_data = $this->common->exempt_supply_report_list_field($from_date,$to_date);
            //$totalData = $this->general_model->getQueryRecords($list_data);
            $totalData = 1;
        }
    echo json_encode($totalData);
    }*/
    public function advances_report($from_date,$to_date) {
     	$advance_voucher_module_id  = $this->config->item('advance_voucher_module');
        $data['advance_voucher_module_id']  = $advance_voucher_module_id;
        $modules = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules = $this->get_section_modules($advance_voucher_module_id, $modules, $privilege);
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;
      
    	$list_data = $this->common->advance_gst_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getPageJoinRecords($list_data);
        $table_columns['headers'] = array("Place Of Supply", "Rate", "Applicable % of Tax Rate", "Gross Advance Received", "Cess Amount");
        $table_columns['data_type'] = 'Advances';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $state_name = $value->state_code .'-'. $value->state_name;
                if($value->state_code == 0 || $value->state_code == ''){
                    $state_name = $value->state_name;
                }
                $item_cess_amount = $value->item_cess_amount ? $value->item_cess_amount : '-';
                $temp = array($state_name,(float)$value->item_tax_percentage,'',$this->precise_amount($value->item_grand_total,2),$this->precise_amount($item_cess_amount,2));
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    public function b2cl_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->gst_b2cl_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getPageJoinRecords($list_data);
        $table_columns['headers'] = array("Invoice Number", "Invoice Date", "Invoice Value", "Place Of Supply", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "E-Commerce GSTIN", "Sale from Bonded WH");
        $table_columns['data_type'] = 'B2CL';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $sales_date = date('d-m-Y',strtotime($value->sales_date));
                $state_name = $value->state_code .'-'. $value->state_name;
                if($value->state_code == 0 || $value->state_code == ''){
                    $state_name = $value->state_name;
                }
                $item_cess_amount = $value->cess_amount ? $value->cess_amount : '-';
                $temp = array($value->sales_invoice_number,$sales_date,$this->precise_amount($value->sales_grand_total,2), $state_name,(float)$value->sales_item_tax_percentage,'',$this->precise_amount($value->taxable_value,2),$this->precise_amount($item_cess_amount,2),'','N');
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    /*public function exportGstb2clReportExcel($export_data){
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $table_columns = array("Invoice Number", "Invoice Date", "Invoice Value", "Place Of Supply", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "E-Commerce GSTIN", "Sale from Bonded WH");

        $column = 0;

        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }

        $excel_row = 2;
        if(!empty($export_data)){            
            foreach ($export_data as $key => $value) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $value->sales_invoice_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, date('d-m-Y',strtotime($value->sales_date)));
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value->sales_grand_total ? number_format($value->sales_grand_total,2) : '');
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $value->state_code .'-'. $value->state_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $value->sales_item_tax_percentage);
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '-');
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $value->taxable_value ? number_format($value->taxable_value,2) : '');  
                $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $value->cess_amount ? number_format($value->cess_amount,2) : '');
                $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '-');
                $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, 'N');
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "GSTR1 B2CL Report{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }
    public function exportGstb2clReportCsv($export_data) {
        $from_date = strtotime(date('Y-m-d'));
        $file_name = "GSTR1 B2CL Report_{$from_date}.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');
       
        $f = fopen('php://output', 'w');

        fputcsv($f, array("Invoice Number", "Invoice Date", "Invoice Value", "Place Of Supply", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "E-Commerce GSTIN", "Sale from Bonded WH"));     
        foreach ($export_data as $key => $value) { 
            $line = array($value->sales_invoice_number,date('d-m-Y',strtotime($value->sales_date)),$value->sales_grand_total, $value->state_code .'-'. $value->state_name,$value->sales_item_tax_percentage,'-', $value->taxable_value,$value->cess_amount,'-','N');
                
            fputcsv($f, $line);
        }   
    }*/
    public function b2cs_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->gst_b2cs_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getPageJoinRecords($list_data);
        $table_columns['headers'] = array("Type","Place Of Supply", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "E-Commerce GSTIN");
        $table_columns['data_type'] = 'B2Cs';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $state_name = $value->state_code .'-'. $value->state_name;
                if($value->state_code == 0 || $value->state_code == ''){
                    $state_name = $value->state_name;
                }
                $item_cess_amount = $value->cess_amount ? $value->cess_amount : '-';
                $temp = array('OE', $state_name,(float) $value->sales_item_tax_percentage,'',$this->precise_amount($value->taxable_value,2),$this->precise_amount($item_cess_amount,2),'');
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
          
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    /*public function exportGstb2csReportExcel($export_data){
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $table_columns = array("Type","Place Of Supply", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "E-Commerce GSTIN");

        $column = 0;

        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }

        $excel_row = 2;
        if(!empty($export_data)){            
            foreach ($export_data as $key => $value) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, '-');
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value->state_code .'-'. $value->state_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value->sales_item_tax_percentage);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '-');
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $value->taxable_value ? number_format($value->taxable_value,2) : '');  
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $value->cess_amount ? number_format($value->cess_amount,2) : '');
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '-');
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "GSTR1 B2CS Report{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }
    public function exportGstb2csReportCsv($export_data) {
        $from_date = strtotime(date('Y-m-d'));
        $file_name = "GSTR1 B2CS Report_{$from_date}.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');
       
        $f = fopen('php://output', 'w');

        fputcsv($f, array("Type","Place Of Supply", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "E-Commerce GSTIN"));     
        foreach ($export_data as $key => $value) { 
            $line = array('-',$value->state_code .'-'. $value->state_name,$value->sales_item_tax_percentage,'-', $value->taxable_value,$value->cess_amount,'-');
                
            fputcsv($f, $line);
        }   
    }*/
    public function credit_debit_note_b2b_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->credit_debit_note_b2b_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getQueryRecords($list_data);
        
        $table_columns['headers'] = array("GSTIN/UIN of Recipient","Receiver Name","Invoice/Advance Receipt Number","Invoice/Advance Receipt date","Note/Refund Voucher Number","Note/Refund Voucher date","Document Type","Place Of Supply","Note/Refund Voucher Value", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "Pre GST");
        $table_columns['data_type'] = 'Credit Debit Note - B2B';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $sales_invoice_date = date('d-m-Y',strtotime($value->sales_invoice_date));
                $sales_c_d_date = date('d-m-Y',strtotime($value->note_invoice_date));
                $state_name = $value->state_code .'-'. $value->state_name;
                if($value->state_code == 0 || $value->state_code == ''){
                    $state_name = 'Out of Country';
                }
                $cess_amount = $value->cess_amount ? $value->cess_amount : '-';
                $temp = array($value->gstin_number,$value->customer_name,$value->invoice_number,$sales_invoice_date,$value->note_invoice_number,$sales_c_d_date,$value->document_type, $state_name,$this->precise_amount($value->note_voucher_grand_value,2),(float)$value->gst_value,'',$this->precise_amount($value->taxable_value,2),$this->precise_amount($cess_amount,2),'N');
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
            
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    /*public function exportGst_credit_debit_b2bReportExcel($export_data){
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $table_columns = array("GSTIN/UIN of Recipient","Receiver Name","Invoice/Advance Receipt Number","Invoice/Advance Receipt date","Note/Refund Voucher Number","Note/Refund Voucher date","Document Type","Place Of Supply","Note/Refund Voucher Value", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "Pre GST");

        $column = 0;

        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }

        $excel_row = 2;
        if(!empty($export_data)){            
            foreach ($export_data as $key => $value) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $value->gstin_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value->customer_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value->invoice_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, date('d-m-Y',strtotime($value->sales_invoice_date)));
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $value->note_invoice_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, date('d-m-Y',strtotime($value->note_invoice_date)));
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '-');
                $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $value->state_code .'-'. $value->state_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $value->note_voucher_grand_value);
                $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $value->gst_value);
                $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '-');
                $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $value->taxable_value ? number_format($value->taxable_value,2) : '');  
                $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $value->cess_amount ? number_format($value->cess_amount,2) : '');
                $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, '-');
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "Credit Debit Note B2B Report{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }
    public function exportGst_credit_debit_b2bReportCsv($export_data) {
        $from_date = strtotime(date('Y-m-d'));
        $file_name = "Credit Debit Note B2B Report_{$from_date}.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');
       
        $f = fopen('php://output', 'w');

        fputcsv($f, array("GSTIN/UIN of Recipient","Receiver Name","Invoice/Advance Receipt Number","Invoice/Advance Receipt date","Note/Refund Voucher Number","Note/Refund Voucher date","Document Type","Place Of Supply","Note/Refund Voucher Value", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "Pre GST"));     
        foreach ($export_data as $key => $value) { 
            $line = array($value->gstin_number,$value->customer_name,$value->invoice_number,date('d-m-Y',strtotime($value->sales_invoice_date)),$value->note_invoice_number,date('d-m-Y',strtotime($value->note_invoice_date)),'-',$value->state_code .'-'. $value->state_name,$value->note_voucher_grand_value,$value->gst_value, '-',$value->taxable_value, $value->cess_amount,'-');
                
            fputcsv($f, $line);
        }   
    }*/
    public function credit_debit_note_b2c_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->credit_debit_note_b2c_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getQueryRecords($list_data);
        $table_columns['headers'] = array("UR Type","Note/Refund Voucher Number","Note/Refund Voucher date","Document Type","Invoice/Advance Receipt Number","Invoice/Advance Receipt date", "Place Of Supply","Note/Refund Voucher Value", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "Pre GST");
        $table_columns['data_type'] = 'Credit Debit Note - B2C';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $sales_date = date('d-m-Y',strtotime($value->sales_invoice_date));
                $state_name = $value->state_code .'-'. $value->state_name;
                $sales_c_d_date = date('d-m-Y',strtotime($value->note_invoice_date));
                if($value->state_code == 0 || $value->state_code == ''){
                    $state_name = 'Out of Country';
                }
                $cess_amount = $value->cess_amount ? $value->cess_amount : '-';
                $type_of_supply = $value->type_of_supply;
                $ur_type = '';
                if($type_of_supply == 'export_without_payment'){
                    $ur_type = 'EXPWOP';
                }else if($type_of_supply == 'export_with_payment'){
                    $ur_type = 'EXPWP';
                }else{
                    if($value->note_voucher_grand_value > 250000){
                        $ur_type = 'B2CL';
                    }else{
                        $ur_type = 'B2Cs';
                    }
                }
                $temp = array($ur_type, $value->note_invoice_number, $sales_c_d_date, $value->document_type,$value->invoice_number,$sales_date,$state_name, $this->precise_amount($value->note_voucher_grand_value,2),(float)$value->gst_value,'',$this->precise_amount($value->taxable_value,2),$this->precise_amount($cess_amount,2),'N');
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;

        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    public function credit_debit_note_b2cs_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->credit_debit_note_b2cs_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getQueryRecords($list_data);

        $table_columns['headers'] = array("Note/Refund Voucher Number","Note/Refund Voucher date","Document Type","Invoice/Advance Receipt Number","Invoice/Advance Receipt date", "Place Of Supply","Note/Refund Voucher Value", "Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount", "Pre GST");
        $table_columns['data_type'] = 'Credit Debit Note - B2Cs';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $sales_date = date('d-m-Y',strtotime($value->sales_invoice_date));
                $state_name = $value->state_code .'-'. $value->state_name;
                $sales_c_d_date = date('d-m-Y',strtotime($value->note_invoice_date));
                if($value->state_code == 0 || $value->state_code == ''){
                    $state_name = 'Out of Country';
                }
                $cess_amount = $value->cess_amount ? $value->cess_amount : '-';
                $type_of_supply = $value->type_of_supply;
                $ur_type = '';
                $temp = array($value->note_invoice_number, $sales_c_d_date, $value->document_type,$value->invoice_number,$sales_date,$state_name, $this->precise_amount($value->note_voucher_grand_value,2),(float)$value->gst_value,'',$this->precise_amount($value->taxable_value,2),$this->precise_amount($cess_amount,2),'N');
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;

        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    public function exports_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->exports_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getPageJoinRecords($list_data);
        $table_columns['headers'] = array("Export Type","Invoice Number","Invoice date","Invoice Value","Port Code","Shipping Bill Number", "Shipping Bill Date","Rate", "Applicable % of Tax Rate", "Taxable Value", "Cess Amount");
        $table_columns['data_type'] = 'Exports';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $sales_date = date('d-m-Y',strtotime($value->sales_date));
                $cess_amount = $value->cess_amount ? $value->cess_amount : '-';
                $sales_type_of_supply = $value->sales_type_of_supply ? $value->sales_type_of_supply:'';
                $type_of_supply = '';
                if($sales_type_of_supply == 'export_without_payment'){
                    $type_of_supply = 'WOPAY';
                }else{
                    $type_of_supply = 'WPAY';
                }
                $temp = array($type_of_supply, $value->sales_invoice_number,$sales_date,$this->precise_amount($value->sales_grand_total,2),'','','',(float)$value->sales_item_tax_percentage,'',$this->precise_amount($value->taxable_value,2),$this->precise_amount($cess_amount,2));
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
            
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    public function hsn_summary_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->gst_hsn_summary_list_field($from_date,$to_date);
        
        $posts = $this->general_model->getPageJoinRecords($list_data);
        $table_columns['headers'] = array("HSN","Description","UQC","Total Quantity","Total Value","Taxable Value", "Integrated Tax Amount","Central Tax Amount", "State/UT Tax Amount", "Cess Amount");
        $table_columns['data_type'] = 'HSN Summary';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $description = $value->description ? $value->description : '-';
                $uom = $value->T_uom ? $value->T_uom : '';
                if(!empty($value->T_description)){
                    $uom = $value->T_uom .'-'.$value->T_description;
                }
                $temp = array($value->hsn_sac_code,$description, $uom, $value->sales_item_quantity,$this->precise_amount($value->sales_item_sub_total,2),$this->precise_amount($value->sales_item_taxable_value,2),$this->precise_amount($value->sales_item_igst_amount,2),$this->precise_amount($value->sales_item_cgst_amount,2),$this->precise_amount($value->sales_item_sgst_amount,2),$this->precise_amount($value->sales_item_tax_cess_amount,2));
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    public function gst_documents_report($from_date,$to_date) {
        /*sales report data*/ 
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $access_settings        = $data['access_settings'];
        $sales_data = $this->common->sales_gst_documents_report_list_field($access_settings,$from_date,$to_date);
        $sales_posts = $this->general_model->getPageJoinRecords($sales_data);
        
        /*credit note report data*/ 
        $sales_credit_note_module_id = $this->config->item('sales_credit_note_module');
        $credit_note_modules = $this->modules;
        $credit_note_privilege = "view_privilege";
        $credit_note_section_modules = $this->get_section_modules($sales_credit_note_module_id, $credit_note_modules, $credit_note_privilege);
        /* presents all the needed */
        $credit_note_access_settings = $credit_note_section_modules['access_settings'];
        $sales_credit_note_data = $this->common->sales_credit_note_gst_documents_report_list_field($credit_note_access_settings,$from_date,$to_date);
        $sales_credit_note_posts = $this->general_model->getPageJoinRecords($sales_credit_note_data);

        /*debit note report data*/ 
        $sales_debit_note_module_id = $this->config->item('sales_debit_note_module');
        $debit_note_modules = $this->modules;
        $debit_note_privilege = "view_privilege";
        $debit_note_section_modules = $this->get_section_modules($sales_debit_note_module_id, $debit_note_modules, $debit_note_privilege);
        /* presents all the needed */
        $debit_note_access_settings        = $debit_note_section_modules['access_settings'];
        $sales_debit_note_data = $this->common->sales_debit_note_gst_documents_report_list_field($debit_note_access_settings,$from_date,$to_date);
        $sales_debit_note_posts = $this->general_model->getPageJoinRecords($sales_debit_note_data);

        /*Advance challen report data*/
        $advance_voucher_module_id = $this->config->item('advance_voucher_module');
        $advance_voucher_module = $this->modules;
        $advance_voucher_privilege = "view_privilege";
        $advance_voucher_section_modules = $this->get_section_modules($advance_voucher_module_id, $advance_voucher_module, $advance_voucher_privilege);
        /* presents all the needed */
        $advance_voucher_access_settings = $advance_voucher_section_modules['access_settings'];
        $advance_voucher_data = $this->common->advance_voucher_gst_documents_report_list_field($advance_voucher_access_settings,$from_date,$to_date);
        $advance_voucher_posts = $this->general_model->getPageJoinRecords($advance_voucher_data);

        /*delivery challen report data*/
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $delivery_challan_modules = $this->modules;
        $delivery_challan_privilege = "view_privilege";
        $delivery_challan_section_modules = $this->get_section_modules($delivery_challan_module_id, $delivery_challan_modules, $delivery_challan_privilege);
        /* presents all the needed */
        $delivery_challan_access_settings = $delivery_challan_section_modules['access_settings'];
        $delivery_challen_data = $this->common->delivery_challen_gst_documents_report_list_field($delivery_challan_access_settings,$from_date,$to_date);
        $delivery_challen_posts = $this->general_model->getPageJoinRecords($delivery_challen_data);

        /*Refund Voucher report data*/
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $refund_voucher_modules = $this->modules;
        $refund_voucher_privilege = "view_privilege";
        $refund_voucher_section_modules = $this->get_section_modules($refund_voucher_module_id, $refund_voucher_modules, $refund_voucher_privilege);
        $refund_access_settings = $refund_voucher_section_modules['access_settings'];
        $refund_voucher_data = $this->common->refund_voucher_gst_documents_report_list_field($refund_access_settings,$from_date,$to_date);
        $refund_voucher_posts = $this->general_model->getPageJoinRecords($refund_voucher_data);

        $data_merged = array_merge((array)$sales_posts,(array)$sales_credit_note_posts,(array)$sales_debit_note_posts,(array)$advance_voucher_posts,(array)$delivery_challen_posts,(array)$refund_voucher_posts);
        $table_columns['headers'] = array("Nature of Document","Sr. No. From","Sr. No. To","Total Number","Cancelled");
        $table_columns['data_type'] = 'Documents';
        if(!empty($data_merged)){            
            foreach ($data_merged as $key => $value) {
                $min = $value->start_id;
                $max = $value->end_id;
                if($value->type == 'sales'){
                    $list_data = $this->common->min_sales_gst_documents_invoice_list_field($min);
                    $posts_min = $this->general_model->getPageJoinRecords($list_data);
                    $list_data = $this->common->max_sales_gst_documents_invoice_list_field($max);
                    $posts_max = $this->general_model->getPageJoinRecords($list_data);
                }else if($value->type == 'sales_credit_note'){
                    $list_data = $this->common->min_sales_credit_note_gst_documents_invoice_list_field($min);
                    $posts_min = $this->general_model->getPageJoinRecords($list_data);
                    $list_data = $this->common->max_sales_credit_note_gst_documents_invoice_list_field($max);
                    $posts_max = $this->general_model->getPageJoinRecords($list_data);
                }else if($value->type == 'sales_debit_note'){
                    $list_data = $this->common->min_sales_debit_note_gst_documents_invoice_list_field($min);
                    $posts_min = $this->general_model->getPageJoinRecords($list_data);
                    $list_data = $this->common->max_sales_debit_note_gst_documents_invoice_list_field($max);
                    $posts_max = $this->general_model->getPageJoinRecords($list_data);
                }else if($value->type == 'advance_voucher'){
                    $list_data = $this->common->min_advance_voucher_gst_documents_invoice_list_field($min);
                    $posts_min = $this->general_model->getPageJoinRecords($list_data);
                    $list_data = $this->common->max_advance_voucher_gst_documents_invoice_list_field($max);
                    $posts_max = $this->general_model->getPageJoinRecords($list_data);
                }else if($value->type == 'delivery_challan'){
                    $list_data = $this->common->min_delivery_challen_gst_documents_invoice_list_field($min);
                    $posts_min = $this->general_model->getPageJoinRecords($list_data);
                    $list_data = $this->common->max_delivery_challen_gst_documents_invoice_list_field($max);
                    $posts_max = $this->general_model->getPageJoinRecords($list_data);
                }else if($value->type == 'refund_voucher'){
                    $list_data = $this->common->min_refund_voucher_gst_documents_invoice_list_field($min);
                    $posts_min = $this->general_model->getPageJoinRecords($list_data);
                    $list_data = $this->common->max_refund_voucher_gst_documents_invoice_list_field($max);
                    $posts_max = $this->general_model->getPageJoinRecords($list_data);
                }
                $min_invoice_value = $posts_min[0]->min_invoice_number ? $posts_min[0]->min_invoice_number : '-';
                $max_invoice_value = $posts_max[0]->max_invoice_number ? $posts_max[0]->max_invoice_number : '-';
                $invoice_count = $value->count_invoice ? $value->count_invoice : 0;
                $temp = array();
                $temp = array($value->nature_of_document, $min_invoice_value, $max_invoice_value, $invoice_count, '');
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
            
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        } */      
    }
    public function gst_documents_report_count($download_format_type,$from_date,$to_date) {
        /*sales report data*/ 
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $access_settings        = $data['access_settings'];
        $sales_data = $this->common->sales_gst_documents_report_list_field($access_settings,$from_date,$to_date);
        $sales_posts = $this->general_model->getPageJoinRecords($sales_data);
        
        /*credit note report data*/ 
        $sales_credit_note_module_id = $this->config->item('sales_credit_note_module');
        $credit_note_modules = $this->modules;
        $credit_note_privilege = "view_privilege";
        $credit_note_section_modules = $this->get_section_modules($sales_credit_note_module_id, $credit_note_modules, $credit_note_privilege);
        /* presents all the needed */
        $credit_note_access_settings = $credit_note_section_modules['access_settings'];
        $sales_credit_note_data = $this->common->sales_credit_note_gst_documents_report_list_field($credit_note_access_settings,$from_date,$to_date);
        $sales_credit_note_posts = $this->general_model->getPageJoinRecords($sales_credit_note_data);

        /*debit note report data*/ 
        $sales_debit_note_module_id = $this->config->item('sales_debit_note_module');
        $debit_note_modules = $this->modules;
        $debit_note_privilege = "view_privilege";
        $debit_note_section_modules = $this->get_section_modules($sales_debit_note_module_id, $debit_note_modules, $debit_note_privilege);
        /* presents all the needed */
        $debit_note_access_settings        = $debit_note_section_modules['access_settings'];
        $sales_debit_note_data = $this->common->sales_debit_note_gst_documents_report_list_field($debit_note_access_settings,$from_date,$to_date);
        $sales_debit_note_posts = $this->general_model->getPageJoinRecords($sales_debit_note_data);

        /*Advance challen report data*/
        $advance_voucher_module_id = $this->config->item('advance_voucher_module');
        $advance_voucher_module = $this->modules;
        $advance_voucher_privilege = "view_privilege";
        $advance_voucher_section_modules = $this->get_section_modules($advance_voucher_module_id, $advance_voucher_module, $advance_voucher_privilege);
        /* presents all the needed */
        $advance_voucher_access_settings = $advance_voucher_section_modules['access_settings'];
        $advance_voucher_data = $this->common->advance_voucher_gst_documents_report_list_field($advance_voucher_access_settings,$from_date,$to_date);
        $advance_voucher_posts = $this->general_model->getPageJoinRecords($advance_voucher_data);
        
        /*delivery challen report data*/
        $delivery_challan_module_id = $this->config->item('delivery_challan_module');
        $delivery_challan_modules = $this->modules;
        $delivery_challan_privilege = "view_privilege";
        $delivery_challan_section_modules = $this->get_section_modules($delivery_challan_module_id, $delivery_challan_modules, $delivery_challan_privilege);
        /* presents all the needed */
        $delivery_challan_access_settings = $delivery_challan_section_modules['access_settings'];
        $delivery_challen_data = $this->common->delivery_challen_gst_documents_report_list_field($delivery_challan_access_settings,$from_date,$to_date);
        $delivery_challen_posts = $this->general_model->getPageJoinRecords($delivery_challen_data);

        /*Refund Voucher report data*/
        $refund_voucher_module_id = $this->config->item('refund_voucher_module');
        $refund_voucher_modules = $this->modules;
        $refund_voucher_privilege = "view_privilege";
        $refund_voucher_section_modules = $this->get_section_modules($refund_voucher_module_id, $refund_voucher_modules, $refund_voucher_privilege);
        $refund_access_settings = $refund_voucher_section_modules['access_settings'];
        $refund_voucher_data = $this->common->refund_voucher_gst_documents_report_list_field($refund_access_settings,$from_date,$to_date);
        $refund_voucher_posts = $this->general_model->getPageJoinRecords($refund_voucher_data);

        $data_merged = array_merge((array)$sales_posts,(array)$sales_debit_note_posts,(array)$delivery_challen_posts,(array)$refund_voucher_posts);
        return $data_merged;
    }
    public function gst_exempt_supply_report($from_date,$to_date) {
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
        $default_date = $section_modules['access_common_settings'][0]->default_notification_date;

        $list_data = $this->common->exempt_supply_report_list_field($from_date,$to_date);
        $posts = $this->general_model->getQueryRecords($list_data);
        $table_columns['headers'] = array("Description","Nil Rated Supplies","Exempted(other than nil rated/non GST supply)","Non-GST Supplies");
        $table_columns['data_type'] = 'Exempt Supply';
        if(!empty($posts)){            
            foreach ($posts as $key => $value) {
                $temp = array();
                $temp = array($value->discription,$this->precise_amount($value->amount,2),'','');
                $nested_data[] = $temp;
            }
            $table_columns['data'] = $nested_data;
        }
        return $table_columns;
        /*if($download_format_type == 'excel'){
            $this->exportGstr1ReportExcel($table_columns);
        }
        if($download_format_type == 'csv'){
            $this->exportGstr1ReportCsv($table_columns);
        }
        if($download_format_type == 'pdf'){
            $this->PDFDownloadGstr1Report($table_columns);
        }*/
    }
    public function exportGstr1ReportExcel($export_data){
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();
        $y = 0;
        foreach ($export_data as $key => $value) {
            /*$object->setActiveSheetIndex($i);*/
            $column = 0;
            $sheetId =$y;
            $object->createSheet($sheetId);
            $object->setActiveSheetIndex($sheetId);
            $object->getActiveSheet()->setTitle($value['data_type']);
            foreach($value['headers'] as $field){
                $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                $column++;
            }

            $excel_row = 2;
            $i = 0;
            if(!empty($value['data'])){
                foreach ($value['data'] as $values) {
                    $col = 0;
                    foreach ($values as $k => $temp) {
                        $object->getActiveSheet()->setCellValueByColumnAndRow($col, $excel_row, $temp);
                        $col++;  
                    }
                    $excel_row++;
                }
            }else{
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, 'No Record Is Available In-Between This Period');
            }
            $y++;
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "GSTR1 Report.xls"/*GSTR1 Report{$from_date}.xls*/;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }
   /* public function exportGstr1ReportCsv($export_data) {
        $zip_name = 'gstr1_csv.zip';
        $zip = new ZipArchive;
        $zip->open($zip_name, ZipArchive::CREATE);

        $temp_directory = "././pdf_form/";

        // create each CSV file and add to the ZIP folder
        foreach($export_data as $key => $value) {
            $file = $value['data_type'].'.csv'; 
            $zip->addFile($temp_directory, $file);
            $handle = fopen($temp_directory . $file, 'w');

            // add your data to the CSV file
            fputcsv($handle, $value['headers']);
            if(!empty($value['data'])){
                foreach ($value['data'] as $key => $values) { 
                    $line = $values;
                        
                    fputcsv($handle, $line);
                }
            }else{
                fputcsv($handle, 'No Record Is Available In-Between This Period');
            }
            fclose($handle);

            // add this file to the ZIP folder
            $zip->addFile($temp_directory . $file);

            // now delete this CSV file
            if(is_file($temp_directory . $file)) {
                unlink($temp_directory . $file);
            }
        }
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zip_name);
        header('Content-Length: ' . filesize($zip_name));
        readfile($zip_name);  
        $zip->close();
    }*/
    public function exportGstr1ReportCsv($export_data) {
        /*$from_date = strtotime(date('Y-m-d'));
        $file_name = "GSTR1 Report.csv";//"GSTR1 Report_{$from_date}.csv"
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');*/
        $file_path = "././pdf_form/";
        $path_data = array();
        foreach ($export_data as $key => $value) {
            $file_name = $value['data_type'];
            $f = fopen($file_path.$file_name.'.csv', 'w');
            fputcsv($f, $value['headers']);
            if(!empty($value['data'])){
                foreach ($value['data'] as $key => $values) { 
                    $line = $values;
                        
                    fputcsv($f, $line);
                }
            }else{
                $empty_line = array();
                $empty_line[] = 'No Record Is Available In-Between This Period';
                fputcsv($f, $empty_line);
            }
            $path_data[$value['data_type']][0] = 'pdf_form/' . $file_name . '.csv';
            $path_data[$value['data_type']][1] = $file_name . '.csv';
        }
        return $path_data;        
    }
    public function PDFDownloadGstr1Report($export_data){
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $path_data = array();
        foreach ($export_data as $key => $value) {
            $from_date = strtotime(date('Y-m-d'));
            $file_name = $value['data_type'];/*$value['data_type']."_GSTR1_report_{$from_date}"*/
            $file_path = "././pdf_form/";
            $data['export_data'] = $value;
            $html = $this->load->view('pdf/gstr_report_pdf' , $data , true);

            include(APPPATH . "third_party/dompdf/autoload.inc.php");
            //and now im creating new instance dompdf
            $dompdf = new Dompdf\Dompdf();
            $paper_size  = 'a4';
            $orientation = 'portrait';
            if($value['data_type'] == 'Credit Debit Note - B2C' || $value['data_type'] == 'Credit Debit Note - B2B' || $value['data_type'] == 'HSN Summary' || $value['data_type'] == 'Credit Debit Note - B2Cs'){
                $orientation = 'landscape';
            }
            // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
            $dompdf->set_paper($paper_size , $orientation);
            $dompdf->load_html($html);
            $path_data[$value['data_type']][0] = 'pdf_form/' . $file_name . '.pdf';
            $path_data[$value['data_type']][1] = $file_name . '.pdf';
            $dompdf->render();
            $output = $dompdf->output();
            file_put_contents($file_path . $file_name . '.pdf', $output);
        }
        return $path_data;
    }
    /*public function PDFDownloadGstr1Report($export_data){
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $from_date = strtotime(date('Y-m-d'));
        $file_name = "GSTR1_report_{$from_date}";
        $data['export_data'] = $export_data;
        $html = $this->load->view('pdf/gstr_report_pdf' , $data , true);

        include(APPPATH . "third_party/dompdf/autoload.inc.php");
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();
        $dompdf->load_html($html);
        $paper_size  = 'a4';
        $orientation = 'portrait';
        if($export_data['data_type'] == 'Credit Debit Note - B2C' || $export_data['data_type'] == 'Credit Debit Note - B2B' || $export_data['data_type'] == 'HSN Summary' || $export_data['data_type'] == 'Credit Debit Note - B2Cs'){
            $orientation = 'landscape';
        }
        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size , $orientation);
        $dompdf->render();
        $output = $dompdf->output();


        $dompdf->stream($file_name , array('Attachment' => 1 ));
    }*/
}