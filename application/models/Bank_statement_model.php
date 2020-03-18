<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_statement_model extends CI_Model
{

    public function getCustomerInvoices($id)
    {
        $amount         = $this->session->userdata('amount');
        $amount_per     = ($amount * 5) / 100;
        $receipt_amount = $amount - $amount_per;

        $sql   = "SELECT a.sales_id,a.sales_currency_converted_amount,a.sales_invoice_number,a.currency_converted_amount,a.receipt_id,a.converted_paid_amount,a.voucher_status,a.voucher_date,a.sales_date from ((SELECT s.sales_id,s.currency_converted_amount as 'sales_currency_converted_amount',s.sales_invoice_number,rv.currency_converted_amount,rv.receipt_id,s.converted_paid_amount,rv.voucher_status,rv.voucher_date,s.sales_date FROM customer c,sales s,receipt_voucher rv WHERE s.sales_party_id=c.customer_id and rv.party_id=c.customer_id and rv.reference_type='sales' and rv.voucher_status=1 and rv.party_id='" . $id . "' and rv.party_type='customer' and rv.financial_year_id=" . $this->session->userdata('SESS_FINANCIAL_YEAR_ID') . " and rv.delete_status=0 and rv.currency_converted_amount<=" . $amount . " and rv.currency_converted_amount>=" . $receipt_amount . ") union (SELECT s.sales_id,s.currency_converted_amount,s.sales_invoice_number,rv.currency_converted_amount,rv.receipt_id,s.converted_paid_amount,rv.voucher_status,rv.voucher_date,s.sales_date FROM customer c,sales s,receipt_voucher rv WHERE s.sales_party_id=c.customer_id and s.sales_id=rv.reference_id and rv.reference_type='sales' and (rv.voucher_status=0 or rv.voucher_status=3) and s.sales_party_id='" . $id . "' and s.sales_party_type='customer' and rv.financial_year_id=" . $this->session->userdata('SESS_FINANCIAL_YEAR_ID') . " and (rv.payment_mode='' or rv.payment_mode IS NULL) and s.converted_paid_amount<s.currency_converted_amount and rv.delete_status=0 and (s.currency_converted_amount-s.converted_paid_amount)>=" . $amount . ")) a order by a.sales_invoice_number asc";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getSuppliersInvoices($id)
    {
        $amount         = $this->session->userdata('amount');
        $amount_per     = ($amount * 5) / 100;
        $receipt_amount = $amount - $amount_per;

        $sql = "SELECT a.purchase_id,a.currency_converted_amount,a.reference_no,a.receipt_amount,a.id,a.paid_amount,a.voucher_status,a.voucher_date,a.date from ((SELECT p.purchase_id,p.currency_converted_amount,p.reference_no,ph.receipt_amount,ph.id,p.paid_amount,ph.voucher_status,ph.voucher_date,p.date FROM suppliers s,purchases p,payment_header ph WHERE p.supplier_id=s.supplier_id and p.purchase_id=ph.purchase_id and ph.voucher_status=1 and p.supplier_id=" . $id . " and p.purchase_id=ph.purchase_id and ph.financial_year_id=" . $this->session->userdata('financial_year_id') . " and ph.delete_status=0 and ph.receipt_amount<=" . $amount . " and ph.receipt_amount>=" . $receipt_amount . ") union (SELECT p.purchase_id,p.currency_converted_amount,p.reference_no,ph.receipt_amount,ph.id,p.paid_amount,ph.voucher_status,ph.voucher_date,p.date FROM suppliers s,purchases p,payment_header ph WHERE p.supplier_id=s.supplier_id and p.purchase_id=ph.purchase_id and (ph.voucher_status=0 or ph.voucher_status=3) and p.supplier_id=" . $id . " and p.purchase_id=ph.purchase_id and ph.financial_year_id=" . $this->session->userdata('financial_year_id') . " and(ph.payment_mode='' or ph.payment_mode IS NULL) and p.paid_amount<p.currency_converted_amount and ph.delete_status=0 and (p.currency_converted_amount-p.paid_amount)>=" . $amount . ")) as a order by a.reference_no asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function updateCustomer($id)
    {
        $condition = array(
                'receipt_id'     => $id,
                'voucher_status' => 1 );
        $this->db->where($condition);
        $this->db->update('receipt_voucher', array(
                'voucher_status' => 2 ));

        // $condition = array('transaction_id' => $id, 'accounts_voucher_status' => 1);
        // $this->db->where($condition);
        // $this->db->update('account_receipts',array('accounts_voucher_status' => 2));
        // $condition = array('id' => $id, 'voucher_status' => 3);
        // $this->db->where($condition);
        // $this->db->update('receipt_header',array('voucher_status' => 4));
        // $condition = array('transaction_id' => $id, 'accounts_voucher_status' => 3);
        // $this->db->where($condition);
        // $this->db->update('account_receipts',array('accounts_voucher_status' => 4));

        $split_status = 0;
        $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $display_status = 1;

            $this->db->where('sub_statement_id', $this->session->userdata('id'), 'display_status', 0);
            $this->db->update('sub_statement', array(
                    'display_status' => 1 ));

            $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
            $res = $this->db->get('sub_statement')->result();
            foreach ($res as $row)
            {
                if ($row->display_status == 0)
                {
                    $display_status = 0;
                }
            }

            if ($display_status == 1)
            {
                $condition = array(
                        'bank_statement_id' => $this->session->userdata('statement_id'),
                        'display_status'    => 0 );
                $this->db->where($condition);
                $this->db->update('bank_statement', array(
                        'display_status' => 1 ));
            }
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $this->session->userdata('statement_id'),
                    'display_status'    => 0 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 1 ));
        }

        return true;
    }

    public function updateCustomerAdvance($id)
    {
        $condition = array(
                'advance_id'     => $id,
                'voucher_status' => 1 );
        $this->db->where($condition);
        $this->db->update('advance_voucher', array(
                'voucher_status' => 2 ));

        $split_status = 0;
        $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $display_status = 1;

            $this->db->where('sub_statement_id', $this->session->userdata('id'), 'display_status', 0);
            $this->db->update('sub_statement', array(
                    'display_status' => 1 ));

            $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
            $res = $this->db->get('sub_statement')->result();
            foreach ($res as $row)
            {
                if ($row->display_status == 0)
                {
                    $display_status = 0;
                }
            }

            if ($display_status == 1)
            {
                $condition = array(
                        'bank_statement_id' => $this->session->userdata('statement_id'),
                        'display_status'    => 0 );
                $this->db->where($condition);
                $this->db->update('bank_statement', array(
                        'display_status' => 1 ));
            }
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $this->session->userdata('statement_id'),
                    'display_status'    => 0 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 1 ));
        }

        return true;
    }

    public function updateCustomerRefund($id)
    {
        $condition = array(
                'refund_id'      => $id,
                'voucher_status' => 1 );
        $this->db->where($condition);
        $this->db->update('refund_voucher', array(
                'voucher_status' => 2 ));

        $split_status = 0;
        $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $display_status = 1;

            $this->db->where('sub_statement_id', $this->session->userdata('id'), 'display_status', 0);
            $this->db->update('sub_statement', array(
                    'display_status' => 1 ));

            $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
            $res = $this->db->get('sub_statement')->result();
            foreach ($res as $row)
            {
                if ($row->display_status == 0)
                {
                    $display_status = 0;
                }
            }

            if ($display_status == 1)
            {
                $condition = array(
                        'bank_statement_id' => $this->session->userdata('statement_id'),
                        'display_status'    => 0 );
                $this->db->where($condition);
                $this->db->update('bank_statement', array(
                        'display_status' => 1 ));
            }
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $this->session->userdata('statement_id'),
                    'display_status'    => 0 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 1 ));
        }

        return true;
    }

    public function updateContra($id)
    {
        $condition = array(
                'contra_voucher_id' => $id,
                'voucher_status'    => 1 );
        $this->db->where($condition);
        $this->db->update('contra_voucher', array(
                'voucher_status' => 2 ));

        $split_status = 0;
        $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $display_status = 1;

            $this->db->where('sub_statement_id', $this->session->userdata('id'), 'display_status', 0);
            $this->db->update('sub_statement', array(
                    'display_status' => 1 ));

            $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
            $res = $this->db->get('sub_statement')->result();
            foreach ($res as $row)
            {
                if ($row->display_status == 0)
                {
                    $display_status = 0;
                }
            }

            if ($display_status == 1)
            {
                $condition = array(
                        'bank_statement_id' => $this->session->userdata('statement_id'),
                        'display_status'    => 0 );
                $this->db->where($condition);
                $this->db->update('bank_statement', array(
                        'display_status' => 1 ));
            }
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $this->session->userdata('statement_id'),
                    'display_status'    => 0 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 1 ));
        }

        return true;
    }

    public function updateSuppliers($id)
    {
        $condition = array(
                'payment_id'     => $id,
                'voucher_status' => 1 );
        $this->db->where($condition);
        $this->db->update('payment_voucher', array(
                'voucher_status' => 2 ));

        // $condition = array('transaction_id' => $id, 'accounts_voucher_status' => 1);
        // $this->db->where($condition);
        // $this->db->update('account_payments',array('accounts_voucher_status' => 2));
        // $condition = array('id' => $id, 'voucher_status' => 3);
        // $this->db->where($condition);
        // $this->db->update('payment_header',array('voucher_status' => 4));
        // $condition = array('transaction_id' => $id, 'accounts_voucher_status' => 3);
        // $this->db->where($condition);
        // $this->db->update('account_payments',array('accounts_voucher_status' => 4));

        $split_status = 0;
        $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $display_status = 1;

            $this->db->where('sub_statement_id', $this->session->userdata('id'), 'display_status', 0);
            $this->db->update('sub_statement', array(
                    'display_status' => 1 ));

            $this->db->where('bank_statement_id', $this->session->userdata('statement_id'));
            $res = $this->db->get('sub_statement')->result();
            foreach ($res as $row)
            {
                if ($row->display_status == 0)
                {
                    $display_status = 0;
                }
            }

            if ($display_status == 1)
            {
                $condition = array(
                        'bank_statement_id' => $this->session->userdata('statement_id'),
                        'display_status'    => 0 );
                $this->db->where($condition);
                $this->db->update('bank_statement', array(
                        'display_status' => 1 ));
            }
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $this->session->userdata('statement_id'),
                    'display_status'    => 0 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 1 ));
        }
        return true;
    }

    public function removeCustomer($tid, $sid, $id)
    {
        $delete  = array();
        $delete1 = '';
        $delete2 = '';

        $condition = array(
                'receipt_id'     => $tid,
                'voucher_status' => 2 );
        $this->db->where($condition);
        $this->db->update('receipt_voucher', array(
                'voucher_status' => 1 ));

        // $condition = array('transaction_id' => $tid, 'accounts_voucher_status' => 2);
        // $this->db->where($condition);
        // $this->db->update('account_receipts',array('accounts_voucher_status' => 1));
        // $condition = array('transaction_id' => $tid, 'accounts_voucher_status' => 4, 'delete_status' => 0);
        // $this->db->where($condition);
        // $this->db->update('account_receipts',array('delete_status' => 1));
        // $condition = array('id' => $tid, 'voucher_status' => 4, 'delete_status' => 0);
        // $this->db->where($condition);
        // $receipt_header=$this->db->get('receipt_header')->result();
        // if($receipt_header)
        // {
        //     $this->db->where(array('sales_id'=>$receipt_header[0]->sales_id,'delete_status' => 0));
        //     $sales=$this->db->get('sales')->result();
        //     $paid_amount=($sales[0]->paid_amount)-($receipt_header[0]->receipt_amount);
        //     $this->db->where(array('sales_id'=>$receipt_header[0]->sales_id,'delete_status' => 0));
        //     $this->db->update('sales',array('paid_amount'=>$paid_amount));
        //     $this->db->where(array('id'=>$receipt_header[0]->id,'delete_status' => 0));
        //     $this->db->update('receipt_header',array('delete_status' => 1));
        //     $condition = array('sales_id' => $receipt_header[0]->sales_id, 'voucher_type !=' => 'sales', 'delete_status' => 0);
        //     $this->db->where($condition);
        //     $receipt_header2=$this->db->get('receipt_header')->result();
        //     if(!$receipt_header2)
        //     {
        //         $condition = array('sales_id' => $receipt_header[0]->sales_id, 'voucher_type' => 'sales','delete_status' => 0);
        //         $this->db->where($condition);
        //         $receipt_header3=$this->db->get('receipt_header')->result();
        //         $delete1=$receipt_header3[0]->invoice_no;
        //         $delete2='customer_'.$receipt_header3[0]->sales_id;
        //     }
        // }

        $split_status = 0;
        $this->db->where('bank_statement_id', $sid);
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $this->db->where(array(
                    'sub_statement_id' => $id,
                    'display_status'   => 1 ));
            $this->db->update('sub_statement', array(
                    'display_status' => 0 ));

            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }

        $condition = array(
                'bank_statement_id' => $sid,
                'voucher_id'        => $tid,
                'sub_statement_id'  => $id );
        $this->db->where($condition);
        $this->db->delete('categorized_statement');

        return true;
    }

    public function removeCustomerAdvance($tid, $sid, $id)
    {
        $delete  = array();
        $delete1 = '';
        $delete2 = '';

        $condition = array(
                'advance_id'     => $tid,
                'voucher_status' => 2 );
        $this->db->where($condition);
        $this->db->update('advance_voucher', array(
                'voucher_status' => 1 ));

        $split_status = 0;
        $this->db->where('bank_statement_id', $sid);
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $this->db->where(array(
                    'sub_statement_id' => $id,
                    'display_status'   => 1 ));
            $this->db->update('sub_statement', array(
                    'display_status' => 0 ));

            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }

        $condition = array(
                'bank_statement_id' => $sid,
                'voucher_id'        => $tid,
                'sub_statement_id'  => $id );
        $this->db->where($condition);
        $this->db->delete('categorized_statement');

        return true;
    }

    public function removeCustomerRefund($tid, $sid, $id)
    {
        $delete  = array();
        $delete1 = '';
        $delete2 = '';

        $condition = array(
                'refund_id'      => $tid,
                'voucher_status' => 2 );
        $this->db->where($condition);
        $this->db->update('refund_voucher', array(
                'voucher_status' => 1 ));

        $split_status = 0;
        $this->db->where('bank_statement_id', $sid);
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $this->db->where(array(
                    'sub_statement_id' => $id,
                    'display_status'   => 1 ));
            $this->db->update('sub_statement', array(
                    'display_status' => 0 ));

            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }

        $condition = array(
                'bank_statement_id' => $sid,
                'voucher_id'        => $tid,
                'sub_statement_id'  => $id );
        $this->db->where($condition);
        $this->db->delete('categorized_statement');

        return true;
    }

    public function removeContra($tid, $sid, $id)
    {
        $delete  = array();
        $delete1 = '';
        $delete2 = '';

        $condition = array(
                'contra_voucher_id' => $tid,
                'voucher_status'    => 2 );
        $this->db->where($condition);
        $this->db->update('contra_voucher', array(
                'voucher_status' => 1 ));

        $split_status = 0;
        $this->db->where('bank_statement_id', $sid);
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $this->db->where(array(
                    'sub_statement_id' => $id,
                    'display_status'   => 1 ));
            $this->db->update('sub_statement', array(
                    'display_status' => 0 ));

            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }

        $condition = array(
                'bank_statement_id' => $sid,
                'voucher_id'        => $tid,
                'sub_statement_id'  => $id );
        $this->db->where($condition);
        $this->db->delete('categorized_statement');

        return true;
    }

    public function removeSuppliers($tid, $sid, $id)
    {
        $delete  = array();
        $delete1 = '';
        $delete2 = '';

        $condition = array(
                'payment_id'     => $tid,
                'voucher_status' => 2 );
        $this->db->where($condition);
        $this->db->update('payment_voucher', array(
                'voucher_status' => 1 ));

        // $condition = array('transaction_id' => $tid, 'accounts_voucher_status' => 2);
        // $this->db->where($condition);
        // $this->db->update('account_payments',array('accounts_voucher_status' => 1));
        // $condition = array('transaction_id' => $tid, 'accounts_voucher_status' => 4, 'delete_status' => 0);
        // $this->db->where($condition);
        // $this->db->update('account_payments',array('delete_status' => 1));
        // $condition = array('id' => $tid, 'voucher_status' => 4, 'delete_status' => 0);
        // $this->db->where($condition);
        // $payment_header=$this->db->get('payment_header')->result();
        // if($payment_header)
        // {
        //     $this->db->where(array('purchase_id'=>$payment_header[0]->purchase_id,'delete_status' => 0));
        //     $purchase=$this->db->get('purchases')->result();
        //     $paid_amount=($purchase[0]->paid_amount)-($payment_header[0]->receipt_amount);
        //     $this->db->where(array('purchase_id'=>$payment_header[0]->purchase_id,'delete_status' => 0));
        //     $this->db->update('purchases',array('paid_amount'=>$paid_amount));
        //     $this->db->where(array('id'=>$payment_header[0]->id,'delete_status' => 0));
        //     $this->db->update('payment_header',array('delete_status' => 1));
        //     $condition = array('purchase_id' => $payment_header[0]->purchase_id, 'voucher_type !=' => 'journal','delete_status' => 0);
        //     $this->db->where($condition);
        //     $payment_header2=$this->db->get('payment_header')->result();
        //     if(!$payment_header2)
        //     {
        //         $condition = array('purchase_id' => $payment_header[0]->purchase_id,'voucher_type' => 'journal','delete_status' => 0);
        //         $this->db->where($condition);
        //         $payment_header3=$this->db->get('payment_header')->result();
        //         $delete1=$payment_header3[0]->invoice_no;
        //         $delete2='suppliers_'.$payment_header3[0]->purchase_id;
        //     }
        //     if($payment_header[0]->voucher_type=='expense' || $payment_header[0]->voucher_type=='journal')
        //     {
        //         $this->db->where(array('expense_id'=>$payment_header[0]->expense_id,'delete_status' => 0));
        //         $this->db->update('expense',array('delete_status' => 1));
        //         $condition = array('transaction_id' => $payment_header[0]->id, 'accounts_voucher_status' => 4, 'delete_status' => 0);
        //         $this->db->where($condition);
        //         $this->db->update('account_payments',array('delete_status' => 1));
        //     }
        // }

        $split_status = 0;
        $this->db->where('bank_statement_id', $sid);
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $this->db->where('sub_statement_id', $id, 'display_status', 1);
            $this->db->update('sub_statement', array(
                    'display_status' => 0 ));

            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $sid,
                    'display_status'    => 1 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'display_status' => 0 ));
        }

        $condition = array(
                'bank_statement_id' => $sid,
                'voucher_id'        => $tid,
                'sub_statement_id'  => $id );
        $this->db->where($condition);
        $this->db->delete('categorized_statement');

        // $delete[0]=$delete1;
        // $delete[1]=$delete2;
        return true;
    }

    public function merge_statement($sid)
    {
        $this->db->where('bank_statement_id', $sid);
        $data    = $this->db->get('categorized_statement')->result();
        $delete  = array();
        $delete1 = array();
        $delete2 = array();

        foreach ($data as $raw)
        {
            if ($raw->party_type == "suppliers" || $raw->party_type == "expense")
            {
                $condition = array(
                        'payment_id'     => $raw->voucher_id,
                        'voucher_status' => 2 );
                $this->db->where($condition);
                $this->db->update('payment_voucher', array(
                        'voucher_status' => 1 ));

                // $condition = array('transaction_id' => $raw->transaction_id, 'accounts_voucher_status' => 2);
                // $this->db->where($condition);
                // $this->db->update('account_payments',array('accounts_voucher_status' => 1));
                // $condition = array('transaction_id' => $raw->transaction_id, 'accounts_voucher_status' => 4, 'delete_status' => 0);
                // $this->db->where($condition);
                // $this->db->update('account_payments',array('delete_status' => 1));
                // $condition = array('id' => $raw->transaction_id, 'voucher_status' => 4, 'delete_status' => 0);
                // $this->db->where($condition);
                // $payment_header=$this->db->get('payment_header')->result();
                // if($payment_header)
                // {
                //     $this->db->where(array('purchase_id'=>$payment_header[0]->purchase_id,'delete_status' => 0));
                //     $purchase=$this->db->get('purchases')->result();
                //     $paid_amount=($purchase[0]->paid_amount)-($payment_header[0]->receipt_amount);
                //     $this->db->where(array('purchase_id'=>$payment_header[0]->purchase_id,'delete_status' => 0));
                //     $this->db->update('purchases',array('paid_amount'=>$paid_amount));
                //     $this->db->where(array('id'=>$payment_header[0]->id,'delete_status' => 0));
                //     $this->db->update('payment_header',array('delete_status' => 1));
                //     $condition = array('purchase_id' => $payment_header[0]->purchase_id, 'voucher_type !=' => 'journal','delete_status' => 0);
                //     $this->db->where($condition);
                //     $payment_header2=$this->db->get('payment_header')->result();
                //     if(!$payment_header2)
                //     {
                //         $condition = array('purchase_id' => $payment_header[0]->purchase_id,'voucher_type' => 'journal','delete_status' => 0);
                //         $this->db->where($condition);
                //         $payment_header3=$this->db->get('payment_header')->result();
                //         $delete1[]=$payment_header3[0]->invoice_no;
                //         $delete2[]='suppliers_'.$payment_header3[0]->purchase_id;
                //     }
                //    if($payment_header[0]->voucher_type=='expense' || $payment_header[0]->voucher_type=='journal')
                //     {
                //         $this->db->where(array('expense_id'=>$payment_header[0]->expense_id,'delete_status' => 0));
                //         $this->db->update('expense',array('delete_status' => 1));
                //         $condition = array('transaction_id' => $payment_header[0]->id, 'accounts_voucher_status' => 4, 'delete_status' => 0);
                //         $this->db->where($condition);
                //         $this->db->update('account_payments',array('delete_status' => 1));
                //     }
                // }
            }

            if ($raw->party_type == "customer")
            {
                $condition = array(
                        'receipt_id'     => $raw->voucher_id,
                        'voucher_status' => 2 );
                $this->db->where($condition);
                $this->db->update('receipt_voucher', array(
                        'voucher_status' => 1 ));

                // $condition = array('transaction_id' => $raw->transaction_id, 'accounts_voucher_status' => 2);
                // $this->db->where($condition);
                // $this->db->update('account_receipts',array('accounts_voucher_status' => 1));
                // $condition = array('transaction_id' => $raw->transaction_id, 'accounts_voucher_status' => 4, 'delete_status' => 0);
                // $this->db->where($condition);
                // $this->db->update('account_receipts',array('delete_status' => 1));
                // $condition = array('id' => $raw->transaction_id, 'voucher_status' => 4, 'delete_status' => 0);
                // $this->db->where($condition);
                // $receipt_header=$this->db->get('receipt_header')->result();
                // if($receipt_header)
                // {
                //     $this->db->where(array('sales_id'=>$receipt_header[0]->sales_id,'delete_status' => 0));
                //     $sales=$this->db->get('sales')->result();
                //     $paid_amount=($sales[0]->paid_amount)-($receipt_header[0]->receipt_amount);
                //     $this->db->where(array('sales_id'=>$receipt_header[0]->sales_id,'delete_status' => 0));
                //     $this->db->update('sales',array('paid_amount'=>$paid_amount));
                //     $this->db->where(array('id'=>$receipt_header[0]->id,'delete_status' => 0));
                //     $this->db->update('receipt_header',array('delete_status' => 1));
                //     $condition = array('sales_id' => $receipt_header[0]->sales_id, 'voucher_type !=' => 'sales', 'delete_status' => 0);
                //     $this->db->where($condition);
                //     $receipt_header2=$this->db->get('receipt_header')->result();
                //     if(!$receipt_header2)
                //     {
                //         // $this->db->where('sales_id',$receipt_header2[0]->sales_id);
                //         // $this->db->delete('sales');
                //         // $this->db->where('sales_id',$receipt_header2[0]->sales_id);
                //         // $this->db->delete('sales_items');
                //         // $condition = array('sales_id' => $receipt_header2[0]->sales_id, 'accounts_voucher_status' => 3);
                //         // $this->db->where($condition);
                //         // $this->db->delete('account_receipts');
                //         // $condition = array('sales_id' => $receipt_header2[0]->sales_id, 'voucher_status' => 3);
                //         // $this->db->where($condition);
                //         // $this->db->delete('receipt_header');
                //         $condition = array('sales_id' => $receipt_header[0]->sales_id, 'voucher_type' => 'sales','delete_status' => 0);
                //         $this->db->where($condition);
                //         $receipt_header3=$this->db->get('receipt_header')->result();
                //         $delete1[]=$receipt_header3[0]->invoice_no;
                //         $delete2[]='customer_'.$receipt_header3[0]->sales_id;
                //         // $delete='customer_'.$receipt_header2[0]->sales_id;
                //     }
                // }
            }

            // if($raw->type=="expense")
            // {
            // }
            // if($raw->type=="income")
            // {
            // }
        }

        $condition = array(
                'bank_statement_id' => $sid );
        $this->db->where($condition);
        $this->db->delete('categorized_statement');

        //$this->db->where('statement_id',$sid,'split_status',1);
        $condition = array(
                'bank_statement_id' => $sid,
                'split_status'      => 1 );
        $this->db->where($condition);
        $this->db->update('bank_statement', array(
                'split_status' => 0 ));

        $condition = array(
                'bank_statement_id' => $sid,
                'display_status'    => 1 );
        $this->db->where($condition);
        $this->db->update('bank_statement', array(
                'display_status' => 0 ));

        $this->db->where('bank_statement_id', $sid);
        $this->db->delete('sub_statement');

        // $delete[0]=$delete1;
        // $delete[1]=$delete2;
        return true;
    }

    public function suspense_statement($sid, $id)
    {
        $split_status = 0;
        $this->db->where('bank_statement_id', $sid);
        $res          = $this->db->get('bank_statement')->result();
        foreach ($res as $row)
        {
            if ($row->split_status == 1)
            {
                $split_status = 1;
            }
        }

        if ($split_status == 1)
        {
            $suspense_status = 1;

            // $this->db->where('id',$id,'display_status',0);
            $condition = array(
                    'sub_statement_id' => $id,
                    'suspense_status'  => 0 );
            $this->db->where($condition);
            $this->db->update('sub_statement', array(
                    'suspense_status' => 1 ));

            // $this->db->where('statement_id',$this->session->userdata('statement_id'));
            $condition = array(
                    'bank_statement_id' => $sid );
            $this->db->where($condition);
            $res       = $this->db->get('sub_statement')->result();
            foreach ($res as $row)
            {
                if ($row->suspense_status == 0)
                {
                    $suspense_status = 0;
                }
            }

            if ($suspense_status == 1)
            {
                $condition = array(
                        'bank_statement_id' => $sid,
                        'suspense_status'   => 0 );
                $this->db->where($condition);
                $this->db->update('bank_statement', array(
                        'suspense_status' => 1 ));
            }
        }
        else
        {
            $condition = array(
                    'bank_statement_id' => $sid,
                    'suspense_status'   => 0 );
            $this->db->where($condition);
            $this->db->update('bank_statement', array(
                    'suspense_status' => 1 ));
        }
        return true;
    }

}

