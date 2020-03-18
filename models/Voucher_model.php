<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher_model extends CI_Model {

    function __construct(){
        parent::__construct();
    }

    public function revertLedgerAmount($response){
        $revert = $pre_revert = array();
        $this->db->trans_start();
        foreach ($response as $key => $data) {
            if($data['amount_type'] == 'DR'){
                $data['voucher_amount'] = (0 - $data['voucher_amount']);
            }
            $colum_name = $this->getQuadrants($data['invoice_date']);
            $year = date('Y',strtotime($data['invoice_date']));
            $date = date('Y-m-d H:i:s');
            
            $this->db->query("UPDATE tbl_reports_bunch SET {$colum_name} = {$colum_name} + {$data['voucher_amount']},total_bunch = total_bunch + {$data['voucher_amount']},closing_balance=closing_balance+{$data['voucher_amount']},updated_at='{$date}' WHERE year={$year} AND firm_id='{$data['firm_id']}' AND acc_id='{$data['company_id']}' AND ledger_id='{$data['ledger_id']}'");

            $this->db->query("UPDATE tbl_reports_bunch SET closing_balance=closing_balance+{$data['voucher_amount']},updated_at='{$date}' WHERE year > {$year} AND firm_id='{$data['firm_id']}' AND acc_id='{$data['company_id']}' AND ledger_id='{$data['ledger_id']}'");

            /*if($value['amount_type'] == 'DR'){
                $value['voucher_amount'] = $value['voucher_amount'] * (-1);
            }
            $colum_name = $this->getQuadrants($value['invoice_date']);
            $year = date('Y',strtotime($value['invoice_date']));
            $date = date('Y-m-d H:i:s');

            $qryy = $this->db->query("SELECT bunch_id FROM tbl_reports_bunch WHERE year={$year} AND firm_id='{$value['firm_id']}' AND acc_id='{$value['company_id']}' AND ledger_id='{$value['ledger_id']}'");
            $q = $qryy->result_array();
            $bunch_id = $q[0]['bunch_id'];

            $revert[] = array('bunch_id' => $bunch_id,
                            "{$colum_name}"=> $colum_name + $value['voucher_amount'],
                            'total_bunch'=> `total_bunch` + $value['voucher_amount'],
                            'closing_balance' => `closing_balance` + $value['voucher_amount'],
                            'invoice_date' => $value['invoice_date'],
                            'updated_at' => $date
                        );

            $qry = $this->db->query("SELECT bunch_id FROM tbl_reports_bunch WHERE year > {$year} AND firm_id='{$value['firm_id']}' AND acc_id='{$value['acc_id']}' AND ledger_id='{$value['ledger_id']}'");
            $rep = $qry->result_array();

            foreach ($rep as $k => $v) {
                $pre_revert[] = array(
                                    'bunch_id' => $v['bunch_id'],
                                    'closing_balance' => `closing_balance` + $value['voucher_amount'],
                                    'updated_at' => $date
                                );
            }*/

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
        /*$this->db->trans_start();
        $this->db->update_batch('tbl_reports_bunch',$revert, 'bunch_id'); 
        $this->db->update_batch('tbl_reports_bunch',$pre_revert, 'bunch_id'); */
    }

    public function addBunchVoucher($data,$invoice_date){
        $colum_name = $this->getQuadrants($invoice_date);
        $year = date('Y',strtotime($invoice_date));
        $firm_id = $data['firm_id'];
        $acc_id = $data['acc_id'];
        $ledger_id = $data['ledger_id'];
        $voucher_amount = $data['voucher_amount'];
        if($data['amount_type'] == 'CR'){
            $voucher_amount = (0 - $voucher_amount);
        }
        
        /* find closing balance*/
        $this->db->select('closing_balance');
        $this->db->where('year <',$year);
        $this->db->where('ledger_id',$data['ledger_id']);
        $this->db->where('acc_id',$data['acc_id']);
        $this->db->where('firm_id',$data['firm_id']);
        $this->db->order_by("year","desc");
        $qry = $this->db->get('tbl_reports_bunch');
        $close_resp = $qry->result_array();
        $closing_balance = 0;
        if(!empty($close_resp)){
            $closing_balance = $close_resp[0]['closing_balance'];
        }

        /* check is already exist */
        $this->db->select("bunch_id");
        $this->db->where('year',$year);
        $this->db->where('ledger_id',$data['ledger_id']);
        $this->db->where('acc_id',$data['acc_id']);
        $this->db->where('firm_id',$data['firm_id']);
        $r = $this->db->get('tbl_reports_bunch');
        $date = date('Y-m-d H:i:s');

        if($r->num_rows() == 0){
            $new_closing_balance = $closing_balance + $voucher_amount;
            $insert = array('ledger_id'=>$data['ledger_id'],
                            'firm_id' => $data['firm_id'],
                            'acc_id' => $data['acc_id'],
                            'year' => $year,
                            "{$colum_name}" => $voucher_amount,
                            'total_bunch' => $voucher_amount,
                            'closing_balance' => $new_closing_balance,
                            'created_at' => date('Y-m-d H:i:s')
                        );
            $this->db->insert('tbl_reports_bunch',$insert);
        }else{

            $this->db->query("UPDATE tbl_reports_bunch SET {$colum_name} = $colum_name + {$voucher_amount},total_bunch = total_bunch + {$voucher_amount},closing_balance=closing_balance+{$voucher_amount},updated_at='{$date}' WHERE year={$year} AND firm_id='{$firm_id}' AND acc_id='{$acc_id}' AND ledger_id='{$ledger_id}'");
        }

        $r = $this->db->query("UPDATE tbl_reports_bunch SET closing_balance=closing_balance+{$voucher_amount},updated_at='{$date}' WHERE year > {$year} AND firm_id='{$firm_id}' AND acc_id='{$acc_id}' AND ledger_id='{$ledger_id}'");

        if($r){
            return true;
        }else{
            return false;
        }
    }

    public function updateBunchVoucher($data,$invoice_date){
        $voucher_amount = $data['voucher_amount'];
        $firm_id = $data['firm_id'];
        $acc_id = $data['acc_id'];
       
        if($data['accounts_voucher_id'] != '0'){
            $this->db->select('ledger_id,voucher_amount,amount_type,invoice_date');
            $this->db->from('tbl_accounts_sales_voucher a');
            $this->db->join('tbl_sales_voucher s','a.voucher_id=s.voucher_id');
            $this->db->where('accounts_voucher_id',$data['accounts_voucher_id']);
            $old_qry = $this->db->get();
            $old_resp = $old_qry->result_array();
            
            $old_ledger_id = $old_resp[0]['ledger_id'];
            $old_amount_type = $old_resp[0]['amount_type'];
            $old_voucher_amount = $old_resp[0]['voucher_amount'];
            $old_invoice_date = $old_resp[0]['invoice_date'];

            $new_ledger_id = $data['ledger_id'];
            $new_amount_type = $data['amount_type'];
            $new_voucher_amount = $data['voucher_amount'];

            /*if($old_ledger_id == $new_ledger_id and $old_amount_type == $new_amount_type and $old_voucher_amount == $new_voucher_amount and $old_invoice_date == $invoice_date){
                $voucher_amount = 0;
            }else{*/
                /* Revert the process */
                if($old_amount_type == 'DR'){
                    $old_voucher_amount = $old_voucher_amount * (-1);
                }

                $colum_name = $this->getQuadrants($old_invoice_date);
               
                $year = date('Y',strtotime($old_invoice_date));
                $date = date('Y-m-d H:i:s');

                $this->db->query("UPDATE tbl_reports_bunch SET {$colum_name} = {$colum_name} + {$old_voucher_amount},total_bunch = total_bunch + {$old_voucher_amount},closing_balance=closing_balance+{$old_voucher_amount},updated_at='{$date}' WHERE year={$year} AND firm_id='{$firm_id}' AND acc_id='{$acc_id}' AND ledger_id='{$old_ledger_id}'");

                $this->db->query("UPDATE tbl_reports_bunch SET closing_balance=closing_balance+{$old_voucher_amount},updated_at='{$date}' WHERE year > {$year} AND firm_id='{$firm_id}' AND acc_id='{$acc_id}' AND ledger_id='{$old_ledger_id}'");

           /* }*/
        }

        return $this->addBunchVoucher($data,$invoice_date);
    }

    function getQuadrants($invoice_date){
        $month = date('m',strtotime($invoice_date));
        $quadrants = $this->config->item('quadrants');
        foreach ($quadrants as $key => $value) {
            if(in_array($month, $value)){
                $colum_name = $key;
                break;
            }
        }
        return $colum_name;
    }
}
?>