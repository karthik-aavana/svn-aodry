<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ledger_view extends MY_Controller {
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
        $this->load->library('SSP');
        $this->branch_id = $this->session->userdata('SESS_BRANCH_ID');
    }

    function index($id= 0){
        $ledger_view_report_module_id         = $this->config->item('ledger_view_report_module');
        $data['module_id']               = $ledger_view_report_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($ledger_view_report_module_id, $modules, $privilege);

        $data['ledgers']   = $this->ledger_model->GetLedgersName();
        $data['ledger_id'] = $this->encryption_url->decode($id);
        $data['financial_year'] = $this->GetFinancialYear();
        $data['financial_year_id'] = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['currency'] = $this->currency_call();
        $this->load->view('reports/ledger_view', $data);
    }
    
    public function getLedgerReports(){
        $ledger_id = $this->input->post('ledger_id');
        $year_id = $this->input->post('year_id');
        /*$this->session->set_userdata('year_id',$year_id);
        $this->session->set_userdata('ledger_id',$ledger_id);*/
        $style = '';
        $isBank = $this->check_bank_Ledger($ledger_id);
        /*if($isBank == 0){
            $style = "style='display:none'";
        }*/
        $page = $this->input->post('page');
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

        /* ------- start closing balance -----------*/
        
        /*$resp = $this->db->query("SELECT SUM(CASE WHEN amount_type = 'DR' THEN voucher_amount ELSE (-voucher_amount) END) closeAmount FROM `tbl_accounts_sales_voucher` ts LEFT JOIN tbl_sales_voucher v ON ts.voucher_id=v.voucher_id WHERE v.firm_id='{$firm_id}' AND v.company_id='{$acc_id}' AND ledger_id='{$ledger_id}' AND (v.invoice_date BETWEEN '{$from_date}' AND '{$to_date}')");
        $result = $resp->result_array();
        if($result[0]['closeAmount'] != null) $closing_balance = $result[0]['closeAmount'];*/

        /*------------ finish closing balance ---------*/
         
        if(!$page) $page = 1;
        $limit = 15;
        
        /*$count_all = $cnt_qry->num_rows();
        $pages = ceil($count_all/$limit);
        $end = (($page -1)*$limit);*/
        $report_ary = array('branch_id' => $this->branch_id,'from_date' => $from_date, 'to_date' => $to_date,'ledger_id' => $ledger_id);
        
        $final_ary = $this->report_model->getLedgerReportAry($report_ary);
        $tr = "<tr><td></td>
                    <td></td>
                    <td>Opening Balance</td>";
                    if($final_ary['opening_balance'] > 0){
                    $tr .= "<td class='text-right'>".number_format(abs($final_ary['opening_balance']),2)."</td><td></td>";
                    }else{
                    $tr .= "<td></td>
                        <td class='text-right'>".number_format(abs($final_ary['opening_balance']),2)."</td>";
                    }

            $tr .= "<td ".$style."></td></tr>";
           $closing_balance = $final_ary['opening_balance'] ;
           $closing_balance_type = '';
        if(!empty($final_ary['final_report'])){
            $last_item = NULL;
            $i=0;
            foreach ($final_ary['date_asc'] as $k => $val){

            foreach ($final_ary['final_report'][$val] as $key => $value) {
                $ledger = ucfirst($value['ledger']);
                if($value['voucher_no'] == $last_item){
                    $vdate = '';
                    $voucher_type = '';
                    $voucher_number = '';
                    $tr .="<tr style='display:none;'' data-id=$i>
                        <td>{$vdate}</td>
                        <td>{$voucher_number}</td>
                        <td>{$ledger}</td>";
                }else{
                    $i++;
                    $vdate = date('d-m-Y',strtotime($value['voucher_date']));
                    $voucher_type = $value['voucher_type'].'_voucher/'.$value['voucher_no'];
                    $voucher_hyperlink_type = $value['voucher_type'].'_voucher';
                    $voucher_id = $this->encryption_url->encode($value['voucher_id']);
                    if($value['voucher_type'] == 'advance' || $value['voucher_type'] == 'refund'){
                        $voucher_number = '<a href="' . base_url($voucher_hyperlink_type.'/view/') .$voucher_id. '">' . $value['voucher_number'] . '</a>'."<span class='fa fa-plus pull-right toggle-row'></span>";
                    }else if($value['voucher_type'] == 'jouranl'){
                        $voucher_number = '<a href="' . base_url('general_voucher/view_details/') .$voucher_id. '">' . $value['voucher_number'] . '</a>'."<span class='fa fa-plus pull-right toggle-row'></span>";
                    }else{
                         $voucher_number = '<a href="' . base_url($voucher_hyperlink_type.'/view_details/') .$voucher_id. '">' . $value['voucher_number'] . '</a>'."<span class='fa fa-plus pull-right toggle-row'></span>";
                    }
                    $tr .="<tr class='main_tr' data-id=$i>
                        <td>{$vdate}</td>
                        <td>{$voucher_number}</td>
                        <td>{$ledger}</td>";
                }
                if(@$value['DR']){
                    $closing_balance = $closing_balance + $value['DR'];
                    if($closing_balance < 0){
                        $closing_balance_type = number_format(abs($closing_balance),2)." CR";
                    }else{
                        $closing_balance_type = number_format(abs($closing_balance),2)." DR";
                    }
                    $tr .= "<td class='text-right'>".number_format($value['DR'],2)."</td><td></td><td ".$style.">".$closing_balance_type."</td>";
                }elseif(@$value['CR']){
                    $closing_balance = $closing_balance + $value['CR'] * -1;
                    if($closing_balance < 0){
                        $closing_balance_type = number_format(abs($closing_balance),2)." CR";
                    }else{
                        $closing_balance_type = number_format(abs($closing_balance),2)." DR";
                    }
                    $tr .= "<td></td><td class='text-right'>".number_format($value['CR'],2)."</td><td ".$style.">".$closing_balance_type."</td>";
                }else{
                    $tr .= "<td></td><td></td><td ".$style."></td>";
                }
                $tr .= "</tr>";
                  //  $opening_balance_first = $closing_balance;
                $last_item = $value['voucher_no'];
                }
            }
        }
        $tr .= "<tr class='main_tr'>
                    <td></td>
                    <td></td>
                    <td>Closing Balance</td>";
                    if($final_ary['closing_balance'] < 0){
                        $tr .= "<td class='text-right'>".number_format(abs($final_ary['closing_balance']),2)."</td><td></td>";
                    }else{
                        $tr .= "<td></td><td class='text-right'>".number_format(abs($final_ary['closing_balance']),2)."</td>";
                    }
               $tr .="<td ".$style."></td></tr>
                <tr>
                    <td colspan='3'></td>
                    <td class='font-weight-bold text-right'>{$final_ary['dr_total']}</td>
                    <td class='font-weight-bold text-right'>{$final_ary['cr_total']}</td><td ".$style." ></td>
                </tr>";
        /*$pagination =''; 
        if($pages > 1){
            $prev_page = ($page == '1' ? '1' : ($page - 1));
            $next_page = ($page == $pages ? $page : ($page + 1));

            $pagination .= '<li class="paginate_button page-item previous '.($page == 1 ? 'disabled' : 'enable').'" id=""><a href="javascript:void(0);" aria-controls="acc_company" page="'.$prev_page.'" class="page-link">Previous</a></li>';
            $pagination .= '<li class="paginate_button page-item active" id=""><a href="javascript:void(0);" aria-controls="acc_company" page="'.$page.'" class="page-link">'.$page.'</a></li>';
            $pagination .= '<li class="paginate_button page-item next '.($page == $pages ? 'disabled' : 'enable').'" id=""><a href="javascript:void(0);" aria-controls="acc_company" page="'.$next_page.'" class="page-link">Next</a></li>';
        }*/

        $this->data['html'] = $tr;
        $this->data['isBank'] = $isBank;
        /*$this->data['pagination'] = $pagination;*/
        echo json_encode($this->data);
        exit;
    }

    public function getLedgersYears(){
        $acc_id = $this->input->post('acc_id');
        $firm_id = $this->input->post('firm_id');
        $resp = $this->company_model->getAllFinacialYear($firm_id,$acc_id);
        
        $option = '<option value="">Select Financial Year*</option>';
        if(!empty($resp)){
            foreach ($resp as $key => $value) {
               
                if($value['from_date'] != '0000-00-00 00:00:00'&& $value['from_date'] != '1970-01-01 01:00:00' && $value['to_date'] != '0000-00-00 00:00:00' && $value['to_date'] != '1970-01-01 01:00:00'){
                    $duration = date('m/Y',strtotime($value['from_date'])).' - '.date('m/Y',strtotime($value['to_date']));
                    if($duration != '01/1970 - 01/1970')
                    $option .= '<option value="'.$value['year_id'].'">'.$duration.'</option>';
                }
            }
        }
        echo $option;
    }

    public function ExportReport(){
        $ledger_id = $this->input->post('ledger_id');
        $page = $this->input->post('page');
        $year_id = $this->input->post('year_id');

        /* get firm details */
        $firm_detail = $this->getFirmDetail();
        $isBank = $this->check_bank_Ledger($ledger_id);
        
        if($year_id != '0'){
            $this->db->select('from_date,to_date');
            $this->db->where('year_id',$year_id);
            $qry = $this->db->get('tbl_financial_year');
            $years_date = $qry->result_array();
            if(!empty($years_date)){
                $from_date = $years_date[0]['from_date'];
                $to_date = $years_date[0]['to_date'];
            }else{
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d');
            }
        }else{
            $from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
            $to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
        }
        $firm_detail['from_date'] = $from_date;
        $firm_detail['to_date'] = $to_date;

        $report_ary = array('branch_id' => $this->branch_id,'ledger_id' => $ledger_id,'from_date' => $from_date, 'to_date' => $to_date);

        $final_ary = $this->report_model->getLedgerReportAry($report_ary);        
        $final_ary['firm_detail'] = $firm_detail;
        $final_ary['ledger_name'] = $this->report_model->getLedgerName($ledger_id);
        $final_ary['isBank'] = $isBank;
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
        
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $object->setActiveSheetIndex(0);
        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $export_data['firm_detail']['company_name'].':');
        $object->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $export_data['firm_detail']['primary_address']);
        $object->getActiveSheet()->setCellValueByColumnAndRow(4, 1, date('d-m-Y',strtotime($export_data['firm_detail']['from_date'])));
        $object->getActiveSheet()->setCellValueByColumnAndRow(5, 1, date('d-m-Y',strtotime($export_data['firm_detail']['to_date'])));

        if($export_data['isBank'] == 1){
            $table_columns = array("Voucher Date", "Voucher Number", "Ledger", "Amount DR", "Amount CR","Closing Balance");
        }else{
            $table_columns = array("Voucher Date", "Voucher Number", "Ledger", "Amount DR", "Amount CR");
        }

        $column = 0;

        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 2, $field);
            $column++;
        }

        $excel_row = 3;
        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Opening Balance');
        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, ($export_data['opening_balance'] > 0 ? number_format(abs($export_data['opening_balance']),2) : ''));
        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, ($export_data['opening_balance'] <= 0 ? number_format(abs($export_data['opening_balance']),2) : ''));

        if($export_data['isBank'] == 1){
             $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
        }

        $excel_row++;
            $closing_balance = $export_data['opening_balance'] ;
            $closing_balance_type = '';
        if(!empty($export_data['final_report'])){
            foreach ($export_data['date_asc'] as $k => $val){              
            foreach ($export_data['final_report'][$val] as $key => $value) {

                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, date('d-m-Y',strtotime($value['voucher_date'])));
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value['voucher_no']);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value['ledger']);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, (@$value['DR'] ? number_format($value['DR'],2) : ''));
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, (@$value['CR'] ? number_format($value['CR'],2) : ''));
                if(@$value['DR']){
                    $closing_balance = $closing_balance + $value['DR'];
                    if($closing_balance < 0){
                        $closing_balance_type = number_format(abs($closing_balance),2)." CR";
                    }else{
                        $closing_balance_type = number_format(abs($closing_balance),2)." DR";
                    } 
                    if($export_data['isBank'] == 1){
                    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $closing_balance_type);
                }                   
                }elseif(@$value['CR']){
                    $closing_balance = $closing_balance + $value['CR'] * -1;
                    if($closing_balance < 0){
                        $closing_balance_type = number_format(abs($closing_balance),2)." CR";
                    }else{
                        $closing_balance_type = number_format(abs($closing_balance),2)." DR";
                    }    
                    if($export_data['isBank'] == 1){
                    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $closing_balance_type);
                    }                           
                }else{
                    if($export_data['isBank'] == 1){
                    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
                    }
                }
               
                
                $excel_row++;
                
            }
            }

            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Closing Balance');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, ($export_data['closing_balance'] < 0 ? number_format(abs($export_data['closing_balance']),2) : ''));
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, ($export_data['closing_balance'] >= 0 ? number_format(abs($export_data['closing_balance']),2) : ''));
            if($export_data['isBank'] == 1){
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
            }
            $excel_row++;
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "Ledger_report_{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }

    public function csvDownloadReport($export_data) {
        $from_date = strtotime(date('Y-m-d'));
        $file_name = "Ledger_report_{$from_date}.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');
       
        $f = fopen('php://output', 'w');

        fputcsv($f, array($export_data['firm_detail']['company_name'],$export_data['firm_detail']['primary_address'],date('d-m-Y',strtotime($export_data['firm_detail']['from_date'])),date('d-m-Y', strtotime($export_data['firm_detail']['to_date']))));
        if($export_data['isBank'] == 1){
            fputcsv($f, array("Voucher Date", "Voucher Number", "Ledger", "Amount DR", "Amount CR", "Closing Balance"));
        }else{
            fputcsv($f, array("Voucher Date", "Voucher Number", "Ledger", "Amount DR", "Amount CR"));
        }
        

        fputcsv($f, array('','','Opening Balance',($export_data['opening_balance'] > 0 ? number_format(abs($export_data['opening_balance']),2) : ''),($export_data['opening_balance'] <= 0 ? number_format(abs($export_data['opening_balance']),2) : '')));
        $closing_balance = $export_data['opening_balance'] ;
            $closing_balance_type = '';
         foreach ($export_data['date_asc'] as $k => $val){              
            foreach ($export_data['final_report'][$val] as $key => $value) {

                if(@$value['DR']){
                    $closing_balance = $closing_balance + $value['DR'];
                    if($closing_balance < 0){
                        $closing_balance_type = number_format(abs($closing_balance),2)." CR";
                    }else{
                        $closing_balance_type = number_format(abs($closing_balance),2)." DR";
                    }
                                     
                }elseif(@$value['CR']){
                    $closing_balance = $closing_balance + $value['CR'] * -1;
                    if($closing_balance < 0){
                        $closing_balance_type = number_format(abs($closing_balance),2)." CR";
                    }else{
                        $closing_balance_type = number_format(abs($closing_balance),2)." DR";
                    }    
                                              
                }else{
                    $closing_balance_type = '';
                }
               

                if($export_data['isBank'] == 1){
                    $line = array($value['voucher_date'],$value['voucher_no'],$value['ledger'],(@$value['DR'] ? number_format($value['DR'],2) : ''),(@$value['CR'] ? number_format($value['CR'],2) : ''),$closing_balance_type);
                }else{
                    $line = array($value['voucher_date'],$value['voucher_no'],$value['ledger'],(@$value['DR'] ? number_format($value['DR'],2) : ''),(@$value['CR'] ? number_format($value['CR'],2) : ''));
                }
                
                fputcsv($f, $line);
            }
        }
        if($export_data['isBank'] == 1){
            fputcsv($f, array('','','Closing Balance',($export_data['closing_balance'] < 0 ? number_format(abs($export_data['closing_balance']),2) : ''),($export_data['closing_balance'] >= 0 ? number_format(abs($export_data['closing_balance']),2) : ''),''));
        }else{
            fputcsv($f, array('','','Closing Balance',($export_data['closing_balance'] < 0 ? number_format(abs($export_data['closing_balance']),2) : ''),($export_data['closing_balance'] >= 0 ? number_format(abs($export_data['closing_balance']),2) : '')));
        }
        
    }  

    public function PDFDownloadReport($export_data){
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $from_date = strtotime(date('Y-m-d'));
        $file_name = "Ledger_report_{$from_date}";
        $data['export_data'] = $export_data;
        $html = $this->load->view('pdf/ledger_pdf' , $data , true);

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

    function check_bank_Ledger($id){
        $branch_id = $this->branch_id;
        $this->db->select('ledger_id');
        $this->db->where('ledger_id',$id);
        $this->db->where('branch_id',$branch_id);
        $qry = $this->db->get('bank_account');
        
        if($qry->num_rows() > 0){
            return 1;   
        }else{
            return 0; 
        }
    }
}
?>