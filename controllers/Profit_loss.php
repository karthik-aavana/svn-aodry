<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Profit_loss extends MY_Controller {
	public $data = array();
    public $branch_id = 0;
    function __construct(){
        parent::__construct();
        $this->load->model([
            'general_model' ,
            'report_model' ,
            'service_model' ,
            'ledger_model' ]);
        $this->modules = $this->get_modules();
        $this->branch_id = $this->session->userdata('SESS_BRANCH_ID');
    }

    function index(){
        $report_module_id         = $this->config->item('report_module');
        $data['module_id']               = $report_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($report_module_id, $modules, $privilege);

        $data['ledgers']                 = $this->ledger_model->GetLedgersName();
        $data['financial_year'] = $this->GetFinancialYear();
        $data['financial_year_id'] = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['currency'] = $this->currency_call();
        $this->load->view('reports/profit_loss', $data);
    }

    public function getPLReport(){
        $year_id = $this->input->post('year_id');
        /*$this->session->set_userdata('year_id',$year_id);*/

        /* get firm details */
        /*$firm_detail = $this->company_model->getFirmDetail($firm_id,$acc_id);*/

        if($year_id != '0' && $year_id != ''){
            $this->db->select('from_date,to_date');
            $this->db->where('year_id',$year_id);
            $qry = $this->db->get('tbl_financial_year');
            $years_date = $qry->result_array();
            $from_date = $years_date[0]['from_date'];
            $to_date = $years_date[0]['to_date'];
        }else{
            $from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
            $to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
        }

        $data  = array('branch_id' => $this->branch_id,'from_date' => $from_date,'to_date'=>$to_date);
        $main_ary = $this->report_model->MaingroupReport($data);
        $grp_array = $this->report_model->getPLReportAry($main_ary,$data);
        /*$stock_id = $grp_array['stock_id'];*/
        $tr_html = '';
        $blank = "<tr><td></td><td></td><td></td></tr>";
        foreach ($grp_array['final_ary'] as $key => $value) {
            if($value[0] != '') {
                $class = $class1 = '';
                if(@$value['is_heading']) $class = 'font-weight-bold b_bottom';
                if(@$value['is_main']) $class = 'font-weight-bold';
                if(@$value['is_left']) $class1 = 'text-left';
                $class .= ' padding_'.($value['space']*30);
                $amount = $value[2];
                /*if($value[0] == 'Closing Stock (C)'){
                    $amount = "<input type='text' class='form-control normal_input' name='closing_stock' data-id='{$stock_id}' value='".round($amount,2)."'>";
                }elseif($value[0] == 'Opening Stock (E)'){
                    $amount = "<input type='text' class='form-control normal_input' name='opening_stock' data-id='{$stock_id}' value='".round($amount,2)."'>";
                }*/
                if(@$value['ledger_id']){
                    $ledger_id = $this->encryption_url->encode($value['ledger_id']);
                    $value[0] = "<a href='".base_url()."ledger_view/details/{$ledger_id}' target='_blank'>".$value[0]."</a>";
                } 
                $tr_html .= "<tr><td class='{$class}'>{$value[0]}</td><td class='{$class1}'>{$value[1]}</td><td>{$amount}</td></tr>";
            }else{
                $tr_html .=$blank;
            }
        }
        $json = array();
        /*$json=$firm_detail;*/
        $json['html'] = $tr_html;
        $json['peroid'] = date('M Y',strtotime($from_date)) .' - '. date('M Y',strtotime($to_date));
        echo json_encode($json);
    }

    public function updateStock(){
        $stock_id = $this->input->post('id');
        $firm_id = $this->input->post('firm_id');
        $acc_id = $this->input->post('acc_id');
        $stock = $this->input->post('sum');
        $stock_cl = $this->input->post('column');
        $this->data['flag'] = true;
        $this->data['msg'] = 'Stock updated successfully!';

        if($stock_id != '0' && $stock_id != ''){
            $this->db->set("{$stock_cl}",$stock);
            $this->db->set("updated_by",$this->session->userdata('userId'));
            $this->db->set("updated_ts",date('Y-m-d H:i:s'));
            $this->db->where('stock_id',$stock_id);
            $qry = $this->db->update('tbl_stock');
        }else{
            $insert = array('firm_id'=>$firm_id,'acc_id' => $acc_id,"{$stock_cl}" => $stock,'created_by' =>$this->session->userdata('userId'),'created_ts' => date('Y-m-d H:i:s'));
            $qry = $this->db->insert('tbl_stock',$insert);
        }
        
        if(!$qry){
            $this->data['flag'] = false;
            $this->data['msg'] = 'Something went wrong!';
        }
        echo json_encode($this->data);
    }

    public function downloadPLReport(){
        $year_id = $this->input->post('year_id');
        /*$this->session->set_userdata('firm_id',$firm_id);
        $this->session->set_userdata('acc_id',$acc_id);
        $this->session->set_userdata('year_id',$year_id);*/
        /* get firm details */
        $firm_detail = $this->getFirmDetail();
       
        /* end */
        if($year_id != '0' && $year_id != ''){
            $this->db->select('from_date,to_date');
            $this->db->where('year_id',$year_id);
            $qry = $this->db->get('tbl_financial_year');
            $years_date = $qry->result_array();
            $from_date = $years_date[0]['from_date'];
            $to_date = $years_date[0]['to_date'];
        }else{
            $from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
            $to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
        }
        $firm_detail['from_date'] = $from_date;
        $firm_detail['to_date'] = $to_date;

        $data  = array('branch_id' => $this->branch_id,'from_date' => $from_date,'to_date'=>$to_date);
        $main_ary = $this->report_model->MaingroupReport($data);
        $grp_array = $this->report_model->getPLReportAry($main_ary,$data);
      
        $final_ary = array();
        $final_ary['data'] = $grp_array['final_ary'];
        $final_ary['firm_detail'] = $firm_detail;

        if($this->input->post('type') == 'excel'){
            $this->exportReportExcel($final_ary);
        }
        if($this->input->post('type') == 'csv'){
            $this->csvDownloadReport($final_ary);
        }
        if($this->input->post('type') == 'pdf'){
            $this->PDFDownloadReport($final_ary);
        }
    }

    public function exportReportExcel($export_data){
        $firm_detail = $export_data['firm_detail'];
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);
        
        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $firm_detail['company_name'].':');
        $object->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $firm_detail['primary_address']);
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, 1, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'From : '.date('d-m-Y',strtotime($firm_detail['from_date'])));
        $object->getActiveSheet()->setCellValueByColumnAndRow(4, 1, 'To : '.date('d-m-Y',strtotime($firm_detail['to_date'])));
        $excel_row = 2;
       
        if(!empty($export_data['data'])){
            foreach ($export_data['data'] as $key => $value) {
                $column = 0;
                foreach ($value as $key => $d) {
                    if($column < 3)
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column, $excel_row, $d);
                    $column++;
                }
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "PL_report_{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }

    public function csvDownloadReport($export_data) {
        $firm_detail = $export_data['firm_detail'];
        $from_date = strtotime(date('Y-m-d'));
        $file_name = "PL_report_{$from_date}.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');
       
        $f = fopen('php://output', 'w');
        fputcsv($f, array($firm_detail['company_name'],$firm_detail['primary_address'],'From : '.date('d-m-Y',strtotime($firm_detail['from_date'])),'To : '.date('d-m-Y', strtotime($firm_detail['to_date']))));
        foreach ($export_data['data'] as $line) {
            $line = array_slice($line, 0, 3);
            fputcsv($f, $line);
        }
    }  

    public function PDFDownloadReport($export_data){
        
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $from_date = strtotime(date('Y-m-d'));
        $file_name = "PL_report_{$from_date}";
        $html = $this->load->view('pdf/profit_pdf' , $export_data , true);

        include(APPPATH . "third_party/dompdf/autoload.inc.php");
        //and now im creating new instance dompdf
        $dompdf = new Dompdf\Dompdf();
        $dompdf->load_html($html);
        $paper_size  = 'a4';
        $orientation = 'portrait';

        // THE FOLLOWING LINE OF CODE IS YOUR CONCERN
        $dompdf->set_paper($paper_size , $orientation);
        $dompdf->render();
        $dompdf->stream($file_name , array('Attachment' => 0 ));
    }
}