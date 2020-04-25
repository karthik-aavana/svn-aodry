<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Trial_balance extends MY_Controller {
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
    	$report_module_id         = $this->config->item('trial_balance_report_module');
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
        $this->load->view('reports/trial_sheet', $data);
    }

    public function GetLedgersList(){
        $report_module_id         = $this->config->item('trial_balance_report_module');
        $data['module_id']               = $report_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($report_module_id, $modules, $privilege);
        $year_id = $this->input->post('year_id');
        /*$this->session->set_userdata('year_id',$year_id);*/
        
        /* get firm details */
       /* $firm_detail = $this->company_model->getFirmDetail($firm_id,$acc_id);*/
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
        $main_list = $this->report_model->getAllMainGroup();
        $main_grp_ary = array();
        foreach ($main_list as $key => $value) {
            $main_grp_ary['m_'.$value['main_grp_id']] = $value['grp_name'];
        }

        $qury = "SELECT l.ledger_id,l.ledger_name ,s.sub_group_name_1,s.sub_group_name_2,s.main_grp_id FROM tbl_ledgers l LEFT JOIN tbl_sub_group s ON l.sub_group_id= s.sub_grp_id WHERE l.branch_id='".$this->branch_id."' AND ledger_name != '' GROUP BY l.ledger_id ";
        $getAll = $this->db->query($qury);
        $response = $getAll->result_array();

        $tr = '';
        $cr_amnt = $dr_amnt = 0;
        $overall_opening = 0;
        if(!empty($response)){
            foreach ($response as $key => $value) {
                $ledger_ary = array(
                                'ledger_id' => $value['ledger_id'],
                                'branch_id' => $this->branch_id,
                                'from_date' => $from_date,
                                'to_date' => $to_date,
                            );
                $balance = $this->report_model->getLedgerReportAry($ledger_ary);
                $ledger_id = $this->encryption_url->encode($value['ledger_id']);

                $tr .=  "<tr>
                            <td>".$main_grp_ary['m_'.$value['main_grp_id']]."</td>
                            <td>{$value['sub_group_name_1']}</td>
                            <td>{$value['sub_group_name_2']}</td>
                            <td><a href='".base_url()."ledger_view/details/{$ledger_id}' target='_blank'>{$value['ledger_name']}</a></td>
                            <td class='text-right'>".number_format(abs($balance['opening_balance']),2)."</td>";
                        if($balance['closing_balance'] >= 0){
                            $tr .= "<td class='text-right'>".number_format($balance['closing_balance'],2)."</td><td></td>";
                            $dr_amnt += abs($balance['closing_balance']);
                        }else{
                            $tr .= "<td></td><td class='text-right'>".number_format(abs($balance['closing_balance']),2)."</td>";
                            $cr_amnt += abs($balance['closing_balance']);
                        }

                        if($balance['opening_balance'] < 0 ) {
                            $overall_opening = $overall_opening - abs($balance['opening_balance']);
                        }else{
                            $overall_opening = $overall_opening + abs($balance['opening_balance']);
                        }

                $tr .=  "</tr>";
            }
            $tr .='<tr>
                        <td colspan="5" class="font-weight-bold text-right">Total</td>
                        <td class="font-weight-bold text-right">'.number_format($dr_amnt,2).'</td>
                        <td class="font-weight-bold text-right">'.number_format($cr_amnt,2).'</td>
                    </tr>';

            $opn_blnc = $dr_amnt - $cr_amnt;
            if($opn_blnc > 0 || $opn_blnc < 0){
                $opn_blnc = $opn_blnc;
            }else{
                $opn_blnc = 0;
            }
            $tr .='<tr>
                    <td colspan="5" class="font-weight-bold text-right">Difference in opening balance </td>
                    <td colspan="2" class="font-weight-bold text-right">'.number_format(($opn_blnc),2).'</td>
                </tr>';

            $op_tr = '<tr>
                    <td colspan="5" class="font-weight-bold text-right">Opening balance </td>';
                    if($overall_opening < 0 ){
                        $op_tr .= '<td class="font-weight-bold text-right">'.number_format(($overall_opening),2).'</td><td></td>';
                    }else{
                        $op_tr .= '<td class="font-weight-bold text-right">'.number_format(($overall_opening),2).'</td><td></td>';
                    }
                    
            $op_tr .= '</tr>';
            $tr = $op_tr.$tr;
        }else{
            $tr = "<tr><td colspan='6'>No data found</td></tr>";
        }
        /*$this->data = $firm_detail;*/
        $this->data['html'] = $tr;
        echo json_encode($this->data);
        exit;
    }

    public function downloadReportSheet(){
        $report_module_id         = $this->config->item('trial_balance_report_module');
        $data['module_id']               = $report_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($report_module_id, $modules, $privilege);
        $year_id = $this->input->post('year_id');
        $branch_id = $this->branch_id;
        /*$this->session->set_userdata('year_id',$year_id);*/
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

        $main_list = $this->report_model->getAllMainGroup();
        $main_grp_ary = array();
        foreach ($main_list as $key => $value) {
            $main_grp_ary['m_'.$value['main_grp_id']] = $value['grp_name'];
        }
        $qury = "SELECT l.ledger_id,l.ledger_name ,s.sub_group_name_1,s.sub_group_name_2,s.main_grp_id FROM tbl_ledgers l LEFT JOIN tbl_sub_group s ON l.sub_group_id= s.sub_grp_id LEFT JOIN accounts_sales_voucher a ON l.ledger_id=a.ledger_id  WHERE l.branch_id='{$branch_id}' AND ledger_name != '' GROUP BY l.ledger_id ";
        $getAll = $this->db->query($qury);

        $response = $getAll->result_array();
        $tr = '';
        $cr_amnt = $dr_amnt = 0;
        $export_data = array();
        if(!empty($response)){
            foreach ($response as $key => $value) {
                $ledger_ary = array(
                                'ledger_id' => $value['ledger_id'],
                                'branch_id' => $this->branch_id,
                                'from_date' => $from_date,
                                'to_date' => $to_date,
                            );
                $balance = $this->report_model->getLedgerReportAry($ledger_ary);
                $debit = $credit = '';
                if($balance['closing_balance'] >= 0){
                    $debit = number_format($balance['closing_balance'],2);
                    $dr_amnt += abs($balance['closing_balance']);
                }else{
                    $credit = number_format(abs($balance['closing_balance']),2);
                    $cr_amnt += abs($balance['closing_balance']);
                }

                $export_data[] = array(
                                    'main_grp_name' => $main_grp_ary['m_'.$value['main_grp_id']],
                                    'sub_grp_1' => $value['sub_group_name_1'],
                                    'sub_grp_2' => $value['sub_group_name_2'],
                                    'ledger_name' => $value['ledger_name'],
                                    'debit' => $debit,
                                    'credit' => $credit
                                );
                
            }
            $export_data[] = array(
                                    'main_grp_name' => '',
                                    'sub_grp_1' => '',
                                    'sub_grp_2' => '',
                                    'ledger_name' => 'Total',
                                    'debit' => number_format($dr_amnt,2),
                                    'credit' => number_format($cr_amnt,2)
                                );
            
        }
        
        if($this->input->post('type') == 'excel'){
            $this->exportReportExcel($export_data,$firm_detail);
        }
        if($this->input->post('type') == 'csv'){
            $this->csvDownloadReport($export_data,$firm_detail);
        }
        if($this->input->post('type') == 'pdf'){
            $this->PDFDownloadReport($export_data,$firm_detail);
        }
    }

    public function exportReportExcel($export_data,$firm_detail){
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();

        $object->setActiveSheetIndex(0);
        
        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $firm_detail['company_name'].':');
        $object->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $firm_detail['primary_address']);
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, 1, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(3, 1, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(4, 1, date('d-m-Y',strtotime($firm_detail['from_date'])));
        $object->getActiveSheet()->setCellValueByColumnAndRow(5, 1, date('d-m-Y',strtotime($firm_detail['to_date'])));

        $table_columns = array("Main Group", "Sub Group1", "Sub Group2", "Ledger", "Debit" , "Credit");
        $column = 0;
        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 2, $field);
            $column++;
        }

        $excel_row = 3;
       
        if(!empty($export_data)){
            foreach ($export_data as $key => $value) {

                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $value['main_grp_name']);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value['sub_grp_1']);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value['sub_grp_2']);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $value['ledger_name']);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $value['debit']);
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $value['credit']);
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "Trial_report_{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }

    public function csvDownloadReport($array,$firm_detail) {
        $from_date = strtotime(date('Y-m-d'));
        $file_name = "Trial_report_{$from_date}.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');
       
        $f = fopen('php://output', 'w');
        fputcsv($f, array($firm_detail['company_name'],$firm_detail['primary_address'],date('d-m-Y',strtotime($firm_detail['from_date'])),date('d-m-Y',strtotime($firm_detail['to_date']))));
        fputcsv($f, array("Main Group", "Sub Group1", "Sub Group2", "Ledger", "Debit" , "Credit"));
        foreach ($array as $line) {
            fputcsv($f, $line);
        }
    }  

    public function PDFDownloadReport($export_data,$firm_detail){
        ob_start();
        $html = ob_get_clean();
        $html = utf8_encode($html);

        $from_date = strtotime(date('Y-m-d'));
        $file_name = "Trial_report_{$from_date}";
        $data['export_data'] = $export_data;
        $data['firm_detail'] = $firm_detail;
        $html = $this->load->view('pdf/trial_pdf' , $data , true);

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