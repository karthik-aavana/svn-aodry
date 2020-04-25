<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_ledger extends MY_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model([
                'general_model',
                'product_model',
                'service_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    function index(){
        $payment_voucher_module_id       = $this->config->item('payment_voucher_module');
        $data['module_id']               = $payment_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);
        $data['reference_type']          = 'payment';
        $data['ledgers']                 = $this->ledger_model->GetLedgersName();

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['currency'] = $this->currency_call();
        $this->load->view('payment_voucher/ledgers', $data);
    }

    function GetAllVoucher(){

        $voucher_id = $this->input->post('voucher_id');
        $reference_type = $this->input->post('reference_type');
        $page = $this->input->post('page');
        if(!$page) $page = 1;
        $limit = 10;
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        /*$getDatePickerFormate = $this->getDatePickerFormate($company_id,$firm_id);*/
        /*$this->session->set_userdata('date_formate',$getDatePickerFormate);*/

        $from_date = date('Y-01-01');
        $to_date = date('Y-12-31');
        $this->db->select('from_date,to_date');
        $this->db->where('is_current','1');
        $this->db->where('branch_id',$this->session->userdata('SESS_BRANCH_ID'));
        $q = $this->db->get('tbl_financial_year');
        $r = $q->result_array();
        if(!empty($r)){
            $from_date = date('Y-m-d',strtotime($r[0]['from_date']));
            $to_date = date('Y-m-d',strtotime($r[0]['to_date']));
        }
        /*$date_formate = $this->getDateFormate($company_id,$firm_id);*/
        $date_formate = 'd-m-Y';

        if(null != $this->input->post('invoice_to')) $to_date = date('Y-m-d',strtotime($this->input->post('invoice_to')));

        if(null != $this->input->post('invoice_from')) $from_date = date('Y-m-d',strtotime($this->input->post('invoice_from')));

        $sql = "SELECT payment_id FROM `payment_voucher` ";
        if($voucher_id != 0 && $voucher_id != ''){
            $sql .= " WHERE payment_id='{$voucher_id}' ";
        }else{
            $sql .= " WHERE branch_id='{$branch_id}' AND voucher_date BETWEEN '{$from_date}' AND '{$to_date}'";
        }

        $sub_ary = array();
        $extraWhere = '';
        /*if($voucher_id != '0') $sql .= " AND payment_id = '{$voucher_id}' ";*/
        if(null != $this->input->post('invoice_ledger')){
            $sub_ary[] = "ledger_id = '".$this->input->post('invoice_ledger')."'";
        }
        if(null != $this->input->post('invoice_cr')){

            if($this->input->post('invoice_cr') == 'CR'){
                $sub_ary[] = " cr_amount > 0 ";
            }else{
                $sub_ary[] = " dr_amount > 0";
            }
        }

        if(null != $this->input->post('invoice_status')){
            $sub_ary[] = " delete_status = '".$this->input->post('invoice_status')."'";
        }

        if(!empty($sub_ary)){
            $where = implode(' AND ', $sub_ary);
            $extraWhere = " AND payment_id IN (SELECT payment_voucher_id FROM accounts_payment_voucher WHERE {$where})";
        }
        if($reference_type == 'boe'){
            $extraWhere .= " AND reference_type = '".$reference_type."' ";
        }else{
            $extraWhere .= " AND reference_type != 'boe' ";
        }
         
        $sql .= $extraWhere;
       
        $cnt_qry = $this->db->query($sql);
        $count_all = $cnt_qry->num_rows(); 
        $pages = ceil($count_all/$limit);
        $end = (($page -1)*$limit);
        
        $sql .= " ORDER BY payment_id DESC LIMIT $end,$limit"; 
        $qry = $this->db->query($sql);
        $vids_ary = array();
        if($qry->num_rows() > 0){
            $vids = $qry->result_array();
            foreach ($vids as $key => $value) {
                array_push($vids_ary, $value['payment_id']);
            }
        }

        $response= array();

        if(!empty($vids_ary)){
            $this->db->select('ts.voucher_number,ts.reference_id,ts.payment_id,ts.voucher_date,ts.receipt_amount,ts.reference_number,ts.reference_id,ts.delete_status,ts.description,tsa.ledger_id,tsa.voucher_amount,tsa.dr_amount,tsa.cr_amount,tsa.accounts_payment_id,tsa.payment_voucher_id');
            $this->db->where_in('ts.payment_id',$vids_ary);
            //$this->db->where_in('ts.payment_id',array('28'));
            $this->db->where('ledger_id != ','');
            if($reference_type == 'boe'){
                $this->db->where('reference_type',$reference_type);
            }else{
                $this->db->where('reference_type !=','boe');
            }
            $this->db->where('tsa.delete_status','0');
            $this->db->order_by('ts.payment_id', 'DESC');
            $this->db->join('accounts_payment_voucher tsa','ts.payment_id=tsa.payment_voucher_id','left');
            $getAll = $this->db->get('payment_voucher ts');
            $response = $getAll->result_array();
        }

        $voucher_ary = $json = array();
        $tr =  $pagination = $pages_dropdown = '';

        if(!empty($response)){
            $ledger_list = $this->ledger_model->GetVoucherLedgers();
            $resp =array();
            $total_voucher = array();
            foreach ($response as $key => $value) {
                $vid = $value['payment_id'];
                $total_voucher['v_'.$vid] = (@$total_voucher['v_'.$vid] ? $total_voucher['v_'.$vid]+1 : 1); 
            }
            
            foreach ($response as $key => $value) {

                $vid = $value['payment_id'];
                $accounts_voucher_id = ($value['accounts_payment_id'] != '' ? $value['accounts_payment_id']: 0);
                /* Ledger dropdown with selected option */
                $ledger_html = "<input type='hidden' value='{$accounts_voucher_id}' data-id='{$vid}' name='accounts_voucher_id'><select data-id='{$vid}' class='form-control js-example-basic-single disable_in' name='ledger_id'><option value=''>Select Ledger</option>";

                if(!empty($ledger_list)){
                    foreach ($ledger_list as $key => $ld) {
                        $selected = '';
                        if($ld['ledger_id'] == $value['ledger_id']){
                            $selected = 'selected';
                        }
                        $ledger_html .="<option value='{$ld['ledger_id']}' {$selected}>{$ld['ledger_name']}</option>";
                    }
                }
                $ledger_html .="</select>";
                /*end*/
                /* CR/DR HTML */
                $amount_type = '';
                if($value['dr_amount'] > 0){
                    $amount_type = 'DR';
                }elseif($value['cr_amount'] > 0){
                    $amount_type = 'CR';
                }
                $Cr_html = '<select class="form-control js-example-basic-single disable_in" name="amount_type" data-id="'.$vid.'"><option value="" '.($amount_type == '' ? 'selected' : '').'>Select CR/DR</option>
                        <option value="CR" '.($amount_type == 'CR' ? 'selected' : '').'>CR</option>
                        <option value="DR" '.($amount_type == 'DR' ? 'selected' : '').'>DR</option>
                    </select>';
                /**/

                if(@$resp['voucher_'.$vid]){
                    $action = '';
                    if($value['delete_status'] == '0'){
                       /* if($this->access['m_update']){*/

                            $action = "<a href='javascript:void(0);' class='add_ledger' data-id='".$vid."'><i class='icon-plus'></i></a>";
                        /*}*/
                    }

                    if($resp['voucher_'.$vid] >= 2){
                        $rowspan = '';
                        if($resp['voucher_'.$vid] == 2){

                            $rowspan = 'rowspan="'.($total_voucher['v_'.$vid] - 2).'"';
                        }
                        $tr .=  '<tr style="display:none;" data-id="'.$vid.'" '.$rowspan.'>';
                        if($value['delete_status'] == '0'){ 
                            $action .=" | <a href='javascript:void(0);' aid='".$accounts_voucher_id."' class='remove_led' data-id='".$vid."'><i class='icon-minus'></i></a>";
                        }
                    }else{
                        $tr .=  '<tr data-id="'.$vid.'">';
                    }
                    $colspan = 3;
                    /*if($this->voucher_type == 'purchase' || $this->voucher_type == 'sales')*/ 
                    $colspan = 5;
                    $tr .= "<td colspan='{$colspan}'></td>";
                    $tr .= "<td>{$ledger_html}</td>";
                    $tr .= "<td><input data-id='{$vid}' type='text' value='".number_format((float)$value['voucher_amount'], 2, '.', '')."' class='disable_in form-control text-right' name='voucher_amount'></td>";
                    $tr .= "<td>{$Cr_html}</td>";
                    /*if($this->access['m_update']){*/
                        /*$tr .= "<td>{$action}</td>";*/
                    /*}else{
                        $tr .= "<td>-</td>";
                    }*/
                  //  $tr .= "<td></td>";
                   // $tr .= "<td></td>";
                    $tr .= '</tr>'; 
                }else{
                    $checked = '';
                    if($value['delete_status'] == '0') $checked = 'checked';
                    $tr .=  '<tr class="main_tr" data-id="'.$vid.'">';
                    $tr .= "<td>{$value['voucher_number']}</td>";
                    $tr .="<td><div class='input-group date datepicker'><input type='text' class='form-control disable_in' data-id='{$vid}'  value='".date($date_formate,strtotime($value['voucher_date']))."' name='invoice_date' placeholder='Invoice To Date*'><span class='input-group-addon input-group-append'> <span class='mdi mdi-calendar input-group-text'></span> </span></div></td>";
                    /*if($this->voucher_type == 'purchase' || $this->voucher_type == 'sales')*/
                    $tr .= "<td><input data-id='{$vid}' type='text' value='{$value['reference_number']}' class='disable_in form-control' name='invoice_number'></td>";
                //    $tr .= "<td><input data-id='{$vid}' type='text' value='' class='disable_in form-control' name='invoice_narration'></td>";

                    /*if($this->voucher_type == 'purchase' || $this->voucher_type == 'sales')*/
                        $tr .= "<td><input data-id='{$vid}' type='text' value='".number_format((float)$value['receipt_amount'], 2, '.', '')."' class='disable_in form-control text-right' name='invoice_total'></td>";
                    if($total_voucher['v_'.$vid] > 2){
                        $tr .= "<td><span class='details-control expand'></span></td>";
                    }else{
                        $tr .= "<td></td>";
                    }
                    
                    $tr .= "<td>{$ledger_html}</td>";
                    $tr .= "<td><input data-id='{$vid}' type='text' value='".number_format((float)$value['voucher_amount'], 2, '.', '')."' class='disable_in form-control text-right' name='voucher_amount'></td>";
                    $tr .= "<td>{$Cr_html}</td>";
                    /*if($this->access['m_update']){*/
                        $reference_id = $sales_id = $this->encryption_url->encode($value['reference_id']);
                        if($value['delete_status'] == '0'){
                            /*$tr .= "<td><a href='". base_url('payment_voucher/edit/') .$reference_id."' class='edit_led' data-id='".$vid."'><i class='glyphicon glyphicon-edit'></i></a> ";*///| <a href='javascript:void(0);' data-id='".$vid."' class='sub_led'><i class='glyphicon glyphicon-check'></i></a></td>
                        }else{
                           /* $tr .= "<td></td>";*/
                        }
                    /*}else{
                        $tr .= "<td>-</td>";
                    }
                    if($this->access['m_delete']){*/
                       // $tr .= "<td><label class='switch'><input type='checkbox' class='checkbox' {$checked} onclick='return doconfirm($(this))' data-id='{$vid}' name='delete_status'><span class='slider round'></span></label></td>";
                    /*}else{
                        $tr .= "<td>-</td>";
                    }*/
                  //  $tr .= "<td>{$value['description']}</td>";
                    $tr .= '</tr>'; 
                }
                $resp['voucher_'.$vid] = (@$resp['voucher_'.$vid] ? $resp['voucher_'.$vid]+1 : 1); 
                $tr .= '</tr>'; 
            }
           
            if($pages > 1){
                $prev_page = ($page == '1' ? '1' : ($page - 1));
                $next_page = ($page == $pages ? $page : ($page + 1));

                $pagination .= '<li class="paginate_button page-item previous '.($page == 1 ? 'disabled' : 'enable').'" id=""><a href="javascript:void(0);" aria-controls="acc_company" page="'.$prev_page.'" class="page-link">Previous</a></li>';
                $pagination .= '<li class="paginate_button page-item active" id=""><a href="javascript:void(0);" aria-controls="acc_company" page="'.$page.'" class="page-link">'.$page.'</a></li>';
                $pagination .= '<li class="paginate_button page-item next '.($page == $pages ? 'disabled' : 'enable').'" id=""><a href="javascript:void(0);" aria-controls="acc_company" page="'.$next_page.'" class="page-link">Next</a></li>';

                $pages_dropdown = "<select id='chnage_page' class='form-control custom_select2'>";
                for ($i=1; $i <= $pages; $i++) { 
                    $pages_dropdown .= "<option value='{$i}' ".($page==$i ? 'selected':'').">{$i}</option>";
                }
                $pages_dropdown .= "</select>"; 
            }
        }else{
            $colspan = 11;
            /*if($this->voucher_type == 'purchase' || $this->voucher_type == 'sales')*/
            $colspan = 12;
            $tr = "<td colspan='{$colspan}' style='text-align:center;'>No records found</td>";
        }
        $json['tbody'] = $tr;
        $json['from_date'] = date('d-m-Y',strtotime($from_date));
        $json['to_date'] = date('d-m-Y',strtotime($to_date));
        $json['pagination'] = $pagination;
        $json['pages_dropdown'] = $pages_dropdown;
        echo json_encode($json);
        exit();
    }

    function boe_ledger(){
        $boe_module_id         = $this->config->item('BOE_module');
        $data['module_id']               = $boe_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($boe_module_id, $modules, $privilege);

        $data['ledgers']                 = $this->ledger_model->GetLedgersName();

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['currency'] = $this->currency_call();
        $data['reference_type'] = 'boe';
        $this->load->view('payment_voucher/ledgers', $data);
    }
}