<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Balance_sheet extends MY_Controller {
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
        $this->load->view('reports/balance_sheet', $data);
    }

    public function getBSReport(){
        /*$firm_id = $this->input->post('firm_id');
        $acc_id = $this->input->post('acc_id');*/
        $year_id = $this->input->post('year_id');
        /*$this->session->set_userdata('firm_id',$firm_id);
        $this->session->set_userdata('acc_id',$acc_id);*/
        /*$this->session->set_userdata('year_id',$year_id);*/

        /* get firm details */
        $firm_detail = $this->getFirmDetail();

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

        $data  = array('from_date' => $from_date,'to_date'=>$to_date);

        $grp_array = $this->getBSReportAry($data);

        $tr_html = $tr_html_A = $tr_html_B = '';
        $total_A_amnt = $total_B_amnt = 0;
        foreach ($grp_array as $key => $value) {

            $class = $class1 = '';
            if(@$value['is_heading']) $class = 'font-weight-bold b_bottom main_head';
            if(@$value['is_main']) $class = 'font-weight-bold';
            if(@$value['is_left']) $class1 = 'text-left';
            
            $class .= ' padding_'.($value['space']*30);
            
            if(@$value['ledger_id']){
                $class1 .= ' f_blue';
                $ledger_id = $this->encryption_url->encode($value['ledger_id']);
                $value[0] = "<a href='".base_url()."ledger_view/details/{$ledger_id}' target='_blank'>".$value[0]."</a>";
            }

            $tr_html_A .= "<tr><td class='{$class}'>{$value[0]}</td><td class='{$class1}'>{$value[1]}</td><td>{$value[2]}</td></tr>";
            if($value[0] == 'Total (A)'){
                $total_A_amnt = $value[2];
                $tr_html_A .= "<tr><td colspan='3'></td></tr>";
            } 
        }

        /*foreach ($grp_array['report_B'] as $key => $value) {
            $class = $class1 = '';
            if(@$value['is_heading']) $class = 'font-weight-bold b_bottom';
            if(@$value['is_main']) $class = 'font-weight-bold';
            if(@$value['is_left']) $class1 = 'text-left';
           
            if($value[0] == 'Total (B)') $total_B_amnt = $value[2];
            $class .= ' padding_'.($value['space']*30);
            $tr_html_B .= "<tr><td class='{$class}'>{$value[0]}</td><td class='{$class1}'>{$value[1]}</td><td>{$value[2]}</td></tr>";
        }*/

        $total_A_amnt = str_replace(',', '', $total_A_amnt);
        $total_B_amnt = str_replace(',', '', $total_B_amnt);

        $Difference = number_format(($total_A_amnt - $total_B_amnt),2);

        $tr_html .= $tr_html_A.$tr_html_B; 
        $opn_html = "<table class='table'><tr><td class='font-weight-bold b_bottom' colspan=''>Difference in opening balance</td><td>{$Difference}</td><td style='width:50%;'>&nbsp</td></tr></table>";
        $tr_html .= "<tr><td class='font-weight-bold b_bottom' colspan='2'>Difference in opening balance</td><td>{$Difference}</td></tr>";
        $json = array();
        /*$json=$firm_detail;*/
        $json['html'] = $tr_html;
        $json['html_A'] = $tr_html_A;
        $json['html_B'] = $tr_html_B;
        $json['opn_html'] = $opn_html;
        $json['peroid'] = date('M Y',strtotime($from_date)) .' - '. date('M Y',strtotime($to_date));
        echo json_encode($json);
    }

    function getBSReportAry($data){
        $grp_array = $this->report_model->MaingroupReport($data);
        $pl_ary = $this->report_model->getPLReportAry($grp_array,$data);
       
        $capital_liabilities = array('Capital','Reserves & Surplus','Loans (Liability)','Current Liabilities','Suspense');
        $final_ary = array();
        $i = 0;
        $final_ary[$i] = array('Capital & Liabilities','','','space'=>0,'is_heading'=>true); 
        $i++;
        $total_a = 0;
      
        foreach ($capital_liabilities as $k => $temp_grp) {
            $main_grp_ary = $grp_array[$temp_grp];
            $final_ary[$i] = array($temp_grp,'',''); 
            $main_key = $i;
            $i++;
            $total_loss = 0;
            foreach ($main_grp_ary as $key => $main_grp) {
                if($key == 'is_sub_2'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $final_ary[$i]['space'] = 1;
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $k2 => $sub_2) {
                            $final_ary[$i] = array($k2,'','');
                            
                            $sub_key_2 = $i;
                            $i++;
                            $total_sub_2 = 0;
                            foreach ($sub_2 as $key => $value) {
                                $total_sub_2 += abs($value['total']);
                                $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                                $final_ary[$i]['space'] = 3;
                                $final_ary[$i]['is_left'] = 1;
                                $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                                $i++;
                            }
                            $final_ary[$sub_key_2] = array($k2,number_format($total_sub_2,2),'');
                            $final_ary[$sub_key_2]['space'] = 2;
                            $total_sub_1 += abs($total_sub_2);
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif($key == 'is_sub_1'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary[$i] = array($k1,'','');
                        $sub_key_1 = $i;
                        $i++;
                        foreach ($sub_1 as $key => $value) {
                            $total_sub_1 += abs($value['total']);
                            $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                            $final_ary[$i]['space'] = 2;
                            $final_ary[$i]['is_left'] = 1;
                            $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                            $i++;
                        }
                        $final_ary[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif ($key == 'is_ledger') {
                    
                    foreach ($main_grp['ledgers'] as $key => $value) {
                        $total_loss += abs($value['total']);
                        $final_ary[$i] = array($value['name'],number_format($value['total'],2),'');
                        $final_ary[$i]['space'] = 1;
                        $final_ary[$i]['is_left'] = 1;
                        $final_ary[$i]['ledger_id'] =$value['ledger_id'];
                        $i++;
                    }
                }

            }
            if($temp_grp == 'Reserves & Surplus'){
                $total_loss += $pl_ary['net_pl'];
                $final_ary[$i] = array('Net profit from p/l statement',number_format($pl_ary['net_pl'],2),'');
                $final_ary[$i]['space'] = 1;
                $final_ary[$i]['is_left'] = 1;
                $i++;
            }
            $final_ary[$main_key] = array($temp_grp,'',number_format($total_loss,2),'is_main'=>true);
            $final_ary[$main_key]['space'] = 0;
            $total_a += $total_loss; 
        }
        $final_ary[$i] = array('Total (A)','',number_format($total_a,2),'space'=>0,'is_main'=>true);
        $i++;
        $j = 0;
        $Assets = array('Fixed Asset','Investments','Loans & Advances (Asset)','Current Assets');//,'Cash & Cash Equivalent'
        $final_ary_B = array();
        $final_ary_B[$j] = array('Assets','','','space'=>0,'is_heading'=>true); 
        $j++;
        $total_b = 0;
        foreach ($Assets as $k => $temp_grp) {
            $main_grp_ary = $grp_array[$temp_grp];
            $final_ary_B[$j] = array($temp_grp,'',''); 
            $main_key = $j;
            $j++;
            $total_loss = 0;
            foreach ($main_grp_ary as $key => $main_grp) {
                
                if($key == 'is_sub_2'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary_B[$j] = array($k1,'','');
                        $final_ary_B[$j]['space'] = 1;
                        $sub_key_1 = $j;
                        $j++;
                        foreach ($sub_1 as $k2 => $sub_2) {
                            $final_ary_B[$j] = array($k2,'','');
                            
                            $sub_key_2 = $j;
                            $j++;
                            $total_sub_2 = 0;
                            foreach ($sub_2 as $key => $value) {
                                $total_sub_2 += abs($value['total']);
                                $final_ary_B[$j] = array($value['name'],number_format($value['total'],2),'');
                                $final_ary_B[$j]['space'] = 3;
                                $final_ary_B[$j]['is_left'] = 1;
                                $final_ary_B[$j]['ledger_id'] =$value['ledger_id'];
                                $j++;
                            }
                            $final_ary_B[$sub_key_2] = array($k2,number_format($total_sub_2,2),'');
                            $final_ary_B[$sub_key_2]['space'] = 2;
                            $total_sub_1 += abs($total_sub_2);
                        }
                        $final_ary_B[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary_B[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif($key == 'is_sub_1'){
                    foreach ($main_grp as $k1 => $sub_1) {
                        $total_sub_1 = 0;
                        $final_ary_B[$j] = array($k1,'','');
                        $sub_key_1 = $j;
                        $j++;
                        foreach ($sub_1 as $key => $value) {
                            $total_sub_1 += abs($value['total']);
                            $final_ary_B[$j] = array($value['name'],number_format($value['total'],2),'');
                            $final_ary_B[$j]['space'] = 2;
                            $final_ary_B[$j]['is_left'] = 1;
                            $final_ary_B[$j]['ledger_id'] =$value['ledger_id'];
                            $j++;
                        }
                        $final_ary_B[$sub_key_1] = array($k1,number_format($total_sub_1,2),'');
                        $final_ary_B[$sub_key_1]['space'] = 1;
                        $total_loss += abs($total_sub_1);
                    }
                }elseif ($key == 'is_ledger') {
                    
                    foreach ($main_grp['ledgers'] as $key => $value) {
                        $total_loss += abs($value['total']);
                        $final_ary_B[$j] = array($value['name'],number_format($value['total'],2),'');
                        $final_ary_B[$j]['space'] = 1;
                        $final_ary_B[$j]['is_left'] = 1;
                        $final_ary_B[$j]['ledger_id'] =$value['ledger_id'];
                        $j++;
                    }
                }
            }
            $final_ary_B[$main_key] = array($temp_grp,'',number_format($total_loss,2),'is_main'=>true);
            $final_ary_B[$main_key]['space'] = 0;
            $total_b += abs($total_loss); 
        }
        $final_ary_B[$j] = array('Total (B)','',number_format($total_b,2),'space'=>0,'is_main'=>true);

        $resp = array();
        /*$resp['report_A'] = $final_ary;
        $resp['report_B'] = $final_ary_B;*/
        $final_ary = array_merge($final_ary,$final_ary_B);
        return $final_ary;
    }

    public function downloadBSReport(){
        $branch_id = $this->branch_id;
        $year_id = $this->input->post('year_id');
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

        $data  = array('from_date' => $from_date,'to_date'=>$to_date);
        $final_ary = $this->getBSReportAry($data);
        $export_data = array();
        $export_data['firm_detail'] = $firm_detail;
        $export_data['data'] = $final_ary;
        if($this->input->post('type') == 'excel'){
            $this->exportReportExcel($export_data);
        }
        if($this->input->post('type') == 'csv'){
            $this->csvDownloadReport($export_data);
        }
        if($this->input->post('type') == 'pdf'){
            $this->PDFDownloadReport($export_data);
        }
        
    }

    public function exportReportExcel($export_data){
        $firm_detail = $export_data['firm_detail'];
        $from_date = strtotime(date('Y-m-d'));
        require_once APPPATH . "/third_party/PHPExcel.php";
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '000000'),
                'underline' => true
            ));

        $main_color = array(
            'font'  => array(
                'bold'  => false,
                'color' => array('rgb' => 'FF0000'),
                'size'  => 11,
            ));
        $total_color = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '047104'),
                'size'  => 11,
            ));
        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $firm_detail['company_name'].':');
        $object->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $firm_detail['primary_address']);
        $object->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'From : '.date('d-m-Y',strtotime($firm_detail['from_date'])));
        $object->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'To : '.date('d-m-Y',strtotime($firm_detail['to_date'])));
        $excel_row = 2;
        /*echo "<pre>";
        print_r($export_data['data']); exit();*/
        if(!empty($export_data['data'])){
            foreach ($export_data['data'] as $key => $value) {
                /*$column = 0;*/
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $value[0]);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $value[1]);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $value[2]);
                //if(@$value['is_main']) $object->getActiveSheet()->getStyle($excel_row)->applyFromArray($main_color);
                
                /*foreach ($value as $key => $d) {
                    $column++;
                }*/
                if(@$value['is_main']) $object->getActiveSheet()->getStyle('C'.$excel_row)->applyFromArray($main_color);
                if(@$value['is_heading']) $object->getActiveSheet()->getStyle('A'.$excel_row)->applyFromArray($styleArray);
                if($value[0] == 'Total (A)' || $value[0] == 'Total (B)') {
                    $object->getActiveSheet()->getStyle('A'.$excel_row)->applyFromArray($total_color);
                    $object->getActiveSheet()->getStyle('C'.$excel_row)->applyFromArray($total_color);
                }
                $excel_row++;
            }
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        $file_name = "BS_report_{$from_date}.xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        $object_writer->save('php://output');
    }

    public function csvDownloadReport($export_data) {
        $firm_detail = $export_data['firm_detail'];
        $from_date = strtotime(date('Y-m-d'));
        $file_name = "BS_report_{$from_date}.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'";');
        
        $f = fopen('php://output', 'w');//
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
        $file_name = "BS_report_{$from_date}";
        $html = $this->load->view('pdf/balance_pdf' , $export_data , true);

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