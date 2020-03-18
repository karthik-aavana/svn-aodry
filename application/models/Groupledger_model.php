<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class GroupLedger_model extends CI_Model {
	function __construct(){
        parent::__construct();
    }
	public function GetVoucherLedgers($firm_id = 0,$company_id = 0) {		
		
		$this->db->select('ledger_id,ledger_name');
		$this->db->from('tbl_ledgers');
		if($firm_id != '0') $this->db->where('firm_id =', $firm_id);
		if($company_id != '0') $this->db->where('branch_id =', $company_id);
		$this->db->where('ledger_name !=', '');
		$query = $this->db->get(); 
		
		return $query->result_array();
	}
	public function getAllGroupfilter(){	
		$this->db->select('sec_sub_group,primary_sub_group,group_id');
		$sub_qry = $this->db->get('tbl_default_group');
		return $sub_qry->result_array();
	}
	public function getAllLedgerGroups($branch_id = 0){	
		$this->db->select('sub_group_name_1,sub_group_name_2,sub_grp_id');
		if($branch_id != 0) $this->db->where('branch_id',$branch_id);
		$sub_qry = $this->db->get('tbl_sub_group');
		return $sub_qry->result_array();
	}
	public function getAllReport(){
		$this->db->select('report_name,report_id');
		$report_qry = $this->db->get('tbl_group_report');
		$report_list = $report_qry->result_array();
		return $report_list;
	}
	public function getAllMainGroup(){
		$this->db->select('grp_name,main_grp_id');
		$main_qry = $this->db->get('tbl_main_group');
		return $main_qry->result_array();
	}
	public function getPrimaryGroups($main_grp_id){
		$this->db->select('sub_group_name_2 as primary_sub_group,sub_grp_id as group_id');
		$this->db->where('main_grp_id',$main_grp_id);
		$this->db->where('sub_group_name_2 !=','');
		$this->db->group_by('sub_group_name_2');
		$qry = $this->db->get('tbl_sub_group');
		return $qry->result_array();
	}
	public function getSecondaryGroups($main_grp_id,$primary_sub_group){
		/*$this->db->select('sec_sub_group,group_id');
		$this->db->where('main_group_id',$main_grp_id);
		$this->db->where('primary_sub_group =',$primary_sub_group);
		$this->db->group_by('primary_sub_group');
		$qry = $this->db->get('tbl_default_group');
		return $qry->result_array();*/

		$this->db->select('sub_group_name_1 as sec_sub_group,sub_grp_id as group_id');
		$this->db->where('main_grp_id',$main_grp_id);
		$this->db->where('sub_group_name_2 =',$primary_sub_group);
		$this->db->where('sub_group_name_1 !=','');
		$this->db->group_by('sub_group_name_2');
		$this->db->group_by('sub_group_name_1');
		$qry = $this->db->get('tbl_sub_group');
		return $qry->result_array();
	}
	public function getSecondaryLedgerGroups($branch_id,$main_grp_id,$primary_sub_group){
		$this->db->select('sub_group_name_2,sub_grp_id');
		$this->db->where('main_grp_id',$main_grp_id);
		$this->db->where('sub_group_name_1 =',$primary_sub_group);
		$this->db->where('sub_group_name_2 !=','');
		$this->db->group_by('sub_group_name_2');
		$qry = $this->db->get('tbl_sub_group');
		return $qry->result_array();
	}
	/* To get default branch groups */
	public function getAllDefaultGroupLedgers(){
		$this->db->select('ledger_id,main_group,sub_group_1,sub_group_2,ledger_name');
		$main_qry = $this->db->get('default_ledgers_cases');
		return $main_qry->result_array();
	}

	public function validateGroup($data,$id = 0){
		$resp = array();
		$resp['flag'] = true;
		if($data['sec_sub_group'] != ''){
			$this->db->select('group_id');
			$this->db->where('LOWER(sec_sub_group)',strtolower($data['sec_sub_group']));
			$this->db->where('group_id !=',$id);
			$count = $this->db->get('tbl_default_group');
			if($count->num_rows() > 0){
				$resp['flag'] = false;
				$resp['msg'] =' Secondory group already exist!';
			}
		}
		if($resp['flag'] && $data['primary_sub_group'] != ''){
			$this->db->select('group_id');
			$this->db->where('LOWER(primary_sub_group)',strtolower($data['primary_sub_group']));
			$this->db->where('main_group_id !=',$data['main_group_id']);
			$this->db->where('group_id !=',$id);
			$count = $this->db->get('tbl_default_group');
			if($count->num_rows() > 0){
				$resp['flag'] = false;
				$resp['msg'] =' Primary group already exist!';
			}else{
				$this->db->select('group_id');
				$this->db->where('LOWER(primary_sub_group)',strtolower($data['primary_sub_group']));
				$this->db->where('sec_sub_group =','');
				$this->db->where('group_id !=',$id);
				$count = $this->db->get('tbl_default_group');
				if($count->num_rows() > 0){
					$resp['flag'] = false;
					$resp['msg'] =' Primary group already exist!';
				}
			}
		}
		
		if($resp['flag'] && $data['sec_sub_group'] == '' && $data['primary_sub_group'] == ''){
			$this->db->select('group_id');
			$this->db->where('main_group_id',$data['main_group_id']);
			$this->db->where('primary_sub_group =','');
			$this->db->where('sec_sub_group =','');
			$this->db->where('group_id !=',$id);
			$count = $this->db->get('tbl_default_group');
			if($count->num_rows() > 0){
				$resp['flag'] = false;
				$resp['msg'] =' Main group already exist!';
			}
		}
		if($resp['flag']){
			if($data['sec_sub_group'] != ''){
				$this->db->select('group_id');
				$this->db->where('LOWER(primary_sub_group)',strtolower($data['sec_sub_group']));
				$count = $this->db->get('tbl_default_group');
				if($count->num_rows() > 0){
					$resp['flag'] = false;
					$resp['msg'] =' Secondary group already exist as a primary group!';
				}
			}
			if($resp['flag'] && $data['primary_sub_group'] != ''){
				$this->db->select('group_id');
				$this->db->where('LOWER(sec_sub_group)',strtolower($data['primary_sub_group']));
				$count = $this->db->get('tbl_default_group');
				if($count->num_rows() > 0){
					$resp['flag'] = false;
					$resp['msg'] =' Primary group already exist as a secondary group!';
				}
			}
		}
		return $resp;
	}
	public function validateLedger($data,$ledger,$ledger_id =0,$sub_grp_id=0){
		$resp = array();
		$resp['flag'] = true;
		$data['sub_group_name_2'] = trim($data['sub_group_name_2']);
		$data['sub_group_name_1'] = trim($data['sub_group_name_1']);
		$ledger['ledger_name'] = trim($ledger['ledger_name']);
		$where_in = " LOWER(grp_name) ='".strtolower($ledger['ledger_name'])."'";
		if($data['sub_group_name_2'] != '' ) $where_in .=  " OR LOWER(grp_name)= '".strtolower($data['sub_group_name_2'])."'";
		if($data['sub_group_name_1'] != '' ) $where_in .=  " OR LOWER(grp_name)= '".strtolower($data['sub_group_name_1'])."'";
		$main_qry = $this->db->query("SELECT grp_name FROM tbl_main_group WHERE (".$where_in.")");
		if($main_qry->num_rows() > 0){
			$rep = $main_qry->result_array();
			$grp_name = $rep[0]['grp_name'];
			if(strtolower($grp_name) == strtolower($data['sub_group_name_2'])){
				$resp['flag'] = false;
				$resp['msg'] = 'Sub group 2 already exist as a main group!';
			}else if(strtolower($grp_name) == strtolower($data['sub_group_name_1'])){
				$resp['flag'] = false;
				$resp['msg'] = 'Sub group 1 already exist as a main group!';
			}else if(strtolower($grp_name) == strtolower($ledger['ledger_name'])){
				$resp['flag'] = false;
				$resp['msg'] = 'Ledger already exist as a main group!';
			}
		}
		if($resp['flag']){
			$this->db->select('ledger_id');
			$this->db->from('tbl_ledgers l');
			$this->db->join('tbl_sub_group s', 'l.sub_group_id = s.sub_grp_id');
			$this->db->where('LOWER(ledger_name)',strtolower($ledger['ledger_name']));
			$this->db->where('LOWER(sub_group_name_1)',strtolower($data['sub_group_name_1']));
			$this->db->where('LOWER(sub_group_name_2)',strtolower($data['sub_group_name_2']));
			$this->db->where('main_grp_id',$data['main_grp_id']);
			if($ledger['branch_id'] != '0')$this->db->where('l.branch_id =',$ledger['branch_id']);
			if($ledger_id != '0')$this->db->where('ledger_id !=',$ledger_id);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				$resp['flag'] = false;
				$resp['msg'] = 'Same combination already exist!';
			}
			if($resp['flag'] && $ledger['ledger_name'] != ''){
				$this->db->select('ledger_id');
				$this->db->where('LOWER(ledger_name)',strtolower($ledger['ledger_name']));
				if($ledger['branch_id'] != '0')$this->db->where('branch_id =',$ledger['branch_id']);
				if($ledger_id != '0')$this->db->where('ledger_id !=',$ledger_id);
				$count = $this->db->get('tbl_ledgers');
				if($count->num_rows() > 0){
					$resp['flag'] = false;
					$resp['msg'] = 'Ledger already exist!';
				}
			}
			if($data['is_editable'] == '1'){
				if($resp['flag']  && $data['sub_group_name_2'] != ''){
					$this->db->select('ledger_id');
					$this->db->from('tbl_ledgers l');
					$this->db->join('tbl_sub_group s', 'l.sub_group_id = s.sub_grp_id');
					$where = '(LOWER(sub_group_name_1) = "'.strtolower($data['sub_group_name_2']).'" OR LOWER(ledger_name) ="'.strtolower($data['sub_group_name_2']).'")';
					$this->db->where($where);
					/*$this->db->where('LOWER(sub_group_name_1)',strtolower($data['sub_group_name_2']));
					$this->db->or_where('LOWER(ledger_name)',strtolower($data['sub_group_name_2']));*/
					if($ledger['branch_id'] != '0')$this->db->where('l.branch_id =',$ledger['branch_id']);
					/*$this->db->where('sub_grp_id !=',$sub_grp_id);*/
					$count = $this->db->get();
					if($count->num_rows() > 0){
						$resp['flag'] = false;
						$resp['msg'] =' Sub group 2 exist as a sub group 1 or ledger!';
					}else{
						$this->db->select('sub_grp_id');
						$this->db->where('LOWER(sub_group_name_2)',strtolower($data['sub_group_name_2']));
						$this->db->where('LOWER(sub_group_name_1) !=',strtolower($data['sub_group_name_1']));
						$this->db->where('main_grp_id !=',$data['main_grp_id']);
						$this->db->where('sub_grp_id !=',$sub_grp_id);
						if($ledger['branch_id'] != '0')$this->db->where('branch_id =',$ledger['branch_id']);
						$count = $this->db->get('tbl_sub_group');
						if($count->num_rows() > 0){
							$resp['flag'] = false;
							$resp['msg'] =' Sub Group 2 already exist!';
						}
					}
				}
				if($resp['flag'] && $data['sub_group_name_1'] != ''){
					
					$this->db->select('ledger_id');
					$this->db->from('tbl_ledgers l');
					$this->db->join('tbl_sub_group s', 'l.sub_group_id = s.sub_grp_id');
					$where = '(LOWER(sub_group_name_2) = "'.strtolower($data['sub_group_name_1']).'" OR LOWER(ledger_name) ="'.strtolower($data['sub_group_name_1']).'")';
					$this->db->where($where);
					if($ledger['branch_id'] != '0')$this->db->where('l.branch_id =',$ledger['branch_id']);
					/*$this->db->where('sub_grp_id !=',$sub_grp_id);*/
					$count = $this->db->get();
					if($count->num_rows() > 0){
						$resp['flag'] = false;
						$resp['msg'] =' Sub group 1 already exist as a ledger or Sub Group 2!';
					}else{
						
						/*$this->db->select('sub_grp_id');
						$this->db->where('LOWER(sub_group_name_1)',strtolower($data['sub_group_name_1']));
						$this->db->where('main_grp_id !=',$data['main_grp_id']);
						$this->db->where('sub_grp_id !=',$sub_grp_id);
						if($ledger['branch_id'] != '0')$this->db->where('branch_id =',$ledger['branch_id']);
						$count = $this->db->get('tbl_sub_group');
						if($count->num_rows() > 0){
							$resp['flag'] = false;
							$resp['msg'] =' Sub group 1 already exist!';
						}else{
							/*$this->db->select('sub_grp_id');
							$this->db->where('LOWER(sub_group_name_1)',strtolower($data['sub_group_name_1']));
							$this->db->where('sub_group_name_2 =','');
							$this->db->where('sub_grp_id !=',$sub_grp_id);
							if($ledger['branch_id'] != '0')$this->db->where('branch_id =',$ledger['branch_id']);
							$count = $this->db->get('tbl_sub_group');
							if($count->num_rows() > 0){
								$resp['flag'] = false;
								$resp['msg'] =' Primary group already exist!';
							}
						}*/
					}
				}
			}
		}
		if($resp['flag'] && $ledger['ledger_name'] != ''){
			$this->db->select('ledger_id');
			$this->db->from('tbl_ledgers l');
			$this->db->join('tbl_sub_group s', 'l.sub_group_id = s.sub_grp_id');
			$where = '(LOWER(sub_group_name_2) = "'.strtolower($ledger['ledger_name']).'" OR LOWER(sub_group_name_1) ="'.strtolower($ledger['ledger_name']).'")';
			$this->db->where($where);
			if($ledger['branch_id'] != '0')$this->db->where('l.branch_id =',$ledger['branch_id']);
			/*$this->db->where('sub_grp_id !=',$sub_grp_id);*/
			$count = $this->db->get();
			if($count->num_rows() > 0){
				$resp['flag'] = false;
				$resp['msg'] =' Ledger already exist as a Sub group 1 or Sub group 2!';
			}
		}
		return $resp;
	}
	public function getLedgerReportAry($dt){
        $ledger_id = $dt['ledger_id'];
        $firm_id = $dt['firm_id'];
        $branch_id = $dt['branch_id'];
        $from_date = $dt['from_date'];
        $to_date = $dt['to_date'];
        $current_yr = date('Y',strtotime($from_date));
        $previous_year = $current_yr - 1;
        /*********** if there is any default openeing balance **********/
        $old_balance = 0;
        $this->db->select('balance_id');
        $this->db->where('balance_upto_date <=',$from_date);
        $this->db->where('branch_id',$branch_id);
        $this->db->where('firm_id',$firm_id);
        $qry = $this->db->get('tbl_default_balance_date');
        
        if($qry->num_rows() > 0){
            $this->db->select('amount,amount_type');
            $this->db->where('ledger_id',$ledger_id);
            $this->db->where('status','1');
            $this->db->where('branch_id',$branch_id);
            $this->db->where('firm_id',$firm_id);
            $qry = $this->db->get('tbl_default_opening_balance');
            if($qry->num_rows() > 0){
                $old_bal = $qry->result_array();
                $old_balance = $old_bal[0]['amount'];
                if($old_bal[0]['amount_type'] == 'DR'){
                    $old_balance = 0 - $old_balance;
                }
            }
        }
        
        /* --------- Start to calculate opening balance -----------*/
        $this->db->select('closing_balance');
        $this->db->where('year < ',$current_yr);
        $this->db->where('ledger_id',$ledger_id);
        $this->db->where('branch_id',$branch_id);
        $this->db->where('firm_id',$firm_id);
        $this->db->order_by('year','DESC');
        $this->db->limit(1);
        $qry = $this->db->get('tbl_reports_bunch');
        $close_resp = $qry->result_array();
        $opening_balance = 0;
        if(!empty($close_resp)){
            $opening_balance = $close_resp[0]['closing_balance'];
        }
        $opening_balance = $opening_balance + $old_balance;
        $month = date('m',strtotime($from_date));
        $quadrants = $this->config->item('quadrants');
        $quadrants_colum = array();
        foreach ($quadrants as $key => $value) {
            if(in_array($month, $value)){
                $current_months = $value;
                break;
            }
            array_push($quadrants_colum, $key);
        }
        
        /* find before months quantrant balance */
        if(!empty($quadrants_colum)){
            $columns_name = implode(',', $quadrants_colum);
            $this->db->select("{$columns_name}");
            $this->db->where('year =',$current_yr);
            $this->db->where('ledger_id',$ledger_id);
            $this->db->where('branch_id',$branch_id);
            $this->db->where('firm_id',$firm_id);
            $months_report = $this->db->get('tbl_reports_bunch');
            $q_months_report = $months_report->result_array();
            if(!empty($q_months_report)){
                foreach ($q_months_report[0] as $key => $value) {
                    $opening_balance += $value;
                }
            }
        }
        /* find remaining months and date balance */
        $start_from_date = date('Y-m-d',strtotime('01-'.$current_months[0].'-'.$current_yr));
        
        $resp = $this->db->query("SELECT SUM(CASE WHEN amount_type = 'DR' THEN voucher_amount ELSE (-voucher_amount) END) TotalAmount FROM `tbl_accounts_sales_voucher` ts LEFT JOIN tbl_sales_voucher v ON ts.voucher_id=v.voucher_id WHERE v.firm_id='{$firm_id}' AND v.company_id='{$branch_id}' AND ledger_id='{$ledger_id}' AND v.voucher_status='active' AND (v.invoice_date >= '{$start_from_date}' AND v.invoice_date < '{$from_date}' )"); //(v.invoice_date BETWEEN '{$start_from_date}' AND '{$from_date}' )
        $result = $resp->result_array();
        if($result[0]['TotalAmount'] != null) $opening_balance += $result[0]['TotalAmount'];
        /* ------------ finish opening balance ------------ */
        $temp_voucher = $final_report = array();
        $dr_total = $cr_total = 0;
        $all_ledger = $this->getAccountLedgers($dt['firm_id'],$dt['branch_id']);
        foreach ($all_ledger as $key => $value) {
            $ledger_name['ledger_'.$value['ledger_id']] = $value['ledger_name'];
        }
        $resp = $this->db->query("SELECT * FROM `tbl_accounts_sales_voucher` ts  JOIN (SELECT voucher_id FROM tbl_accounts_sales_voucher WHERE ledger_id='{$dt['ledger_id']}') as t ON t.voucher_id=ts.voucher_id LEFT JOIN tbl_sales_voucher v ON ts.voucher_id=v.voucher_id WHERE v.firm_id='{$dt['firm_id']}' AND v.company_id='{$dt['branch_id']}' AND v.voucher_status='active' AND (v.invoice_date BETWEEN '{$dt['from_date']}' AND '{$dt['to_date']}') ");
        $result = $resp->result_array();
        if(!empty($result)){
            foreach ($result as $key => $value) {
                $temp_voucher['voucher_'.$value['voucher_id']][] = $value;
            }
            $i = 0;
            foreach ($temp_voucher as $key => $voucher) {
                $ledger_detail = $this->findLedgerDetail($voucher,$dt['ledger_id']);
                $amount_type = $ledger_detail['amount_type'];
                $ledger_amount = $ledger_detail['voucher_amount'];
                $is_first = true;
                foreach ($voucher as $key => $value) {
                    if($value['ledger_id'] != $dt['ledger_id'] && $amount_type != $value['amount_type']){
                        $final_report[$i]['voucher_date'] = $value['invoice_date'];
                        $final_report[$i]['voucher_type'] = $value['voucher_type'];
                        $final_report[$i]['voucher_no'] = $value['voucher_id'];
                        $final_report[$i]['ledger'] = $ledger_name['ledger_'.$value['ledger_id']];
                        if($is_first){
                            if($amount_type == 'DR'){
                                $final_report[$i]['DR'] = $ledger_amount;
                                $dr_total += $ledger_amount;
                            }else{
                                $final_report[$i]['CR'] = $ledger_amount;
                                $cr_total += $ledger_amount;
                            }
                            $is_first = false;
                        }
                        $i++;
                    }
                }
            }
        }
        $closing_balance = 0;
        if($opening_balance < 0){
            $cr_total = $cr_total + abs($opening_balance);
        }else{
            $dr_total = $dr_total + abs($opening_balance);
        }
        $closing_balance = $dr_total - $cr_total;
        
        if($closing_balance < 0){
            $dr_total = $dr_total + abs($closing_balance);
        }else{
            $cr_total = $cr_total + abs($closing_balance);
        }
        $dr_total = number_format(abs($dr_total),2);
        $cr_total = number_format(abs($cr_total),2);
        $responce = array();
        $responce['final_report'] = $final_report;
        $responce['opening_balance'] = $opening_balance;
        $responce['closing_balance'] = $closing_balance;
        $responce['dr_total'] = $dr_total;
        $responce['cr_total'] = $cr_total;
        return $responce;
    }
    function findLedgerDetail($voucher,$ledger_id){
        $resp = array();
        foreach ($voucher as $key => $v) {
            if($v['ledger_id'] == $ledger_id){
                $resp = $v;
                break;
            }
        }
        return $resp;
    }
	public function addNewSubgroup($data){
		
		$this->db->insert('tbl_default_group',$data);
		$ins_id = $this->db->insert_id();
		$user_id = $this->session->userdata('SESS_BRANCH_ID');
		$this->db->query("INSERT INTO tbl_sub_group (branch_id,sub_group_name_1,main_grp_id,sub_group_name_2,default_group_id,report_id,is_editable,created_ts,created_by)
							SELECT branch_id,'{$data['primary_sub_group']}','{$data['main_group_id']}','{$data['sec_sub_group']}','{$ins_id}','{$data['report_id']}','0',NOW(),'{$user_id}'
							FROM tbl_account_company
							ORDER BY branch_id;");
		return true;	
	}
	public function addNewLedgergroup($data,$ledger){
		$this->db->insert('tbl_sub_group',$data);
		$ledger['sub_group_id'] = $this->db->insert_id();
		$this->db->insert('tbl_ledgers',$ledger);
		$ledger_id = $this->db->insert_id();
		unset($ledger['ledger_name']);
		unset($ledger['sub_group_id']);
		$ledger['ledger_id'] = $ledger_id;
		$this->db->insert('tbl_default_opening_balance',$ledger);
	}
	public function updateLedgerInfo($ledger,$ledger_id,$update){
		$this->db->select('ledger_name');
		$this->db->where('ledger_id',$ledger_id);
		$this->db->where('ledger_name','');
		$query = $this->db->get('tbl_ledgers');
		$this->db->set('ledger_name',$ledger);
		$this->db->where('ledger_id',$ledger_id);
		$this->db->update('tbl_ledgers');
		
		if($query->num_rows() > 0){
			unset($update['ledger_name']);
			$update['ledger_id'] = $ledger_id;
			$update['created_by'] = $this->session->userdata('SESS_USER_ID');
			$update['created_ts'] = date('Y-m-d H:i:s');
			$this->db->insert('tbl_default_opening_balance',$update);
		}
	}
	public function getGroupId($ledger_id){
		$this->db->select('sub_group_id');
		$this->db->where('ledger_id',$ledger_id);
		$r = $this->db->get('tbl_ledgers');
		$rep = $r->result_array();
		return $rep[0]['sub_group_id'];
	}
	public function getAccountLedgers($firm_id,$branch_id){
		$this->db->select('ledger_name,ledger_id');
		$this->db->where('ledger_name !=','');
		$this->db->where('firm_id',$firm_id);
		$this->db->where('branch_id',$branch_id);
		$resp = $this->db->get('tbl_ledgers');
		$resp = $resp->result_array();
		return $resp;
	}
}
?>