<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
if (!function_exists('pending_notification_sales'))
{

    function pending_notification_sales()
    {
        $ci = & get_instance();
        $ci->load->database();
        $q  = $ci->db->select("count(*) AS coun")->from('sales')->where('delete_status', 0)->where('sales_paid_amount', 0)->get();
        return $q->row();

    }

}if (!function_exists('partial_notification_sales'))
{

    function partial_notification_sales()
    {
        $ci    = & get_instance();
        $where = "(sales_grand_total+credit_note_amount) > (sales_paid_amount+debit_note_amount)";
        $ci->load->database();
        $q     = $ci->db->select("count(*) AS coun")->from('sales')->where('delete_status', 0)->where('sales_paid_amount !=', 0)->where($where)->get();
        return $q->row();

    }

}if (!function_exists('pending_notification_purchase'))
{

    function pending_notification_purchase()
    {
        $ci = & get_instance();
        $ci->load->database();
        $q  = $ci->db->select("count(*) AS coun")->from('purchase')->where('delete_status', 0)->where('purchase_paid_amount', 0)->get();
        return $q->row();

    }

}if (!function_exists('partial_notification_purchase'))
{

    function partial_notification_purchase()
    {
        $ci    = & get_instance();
        $where = "(purchase_grand_total+credit_note_amount) > (purchase_paid_amount+purchase_return_amount)";
        $ci->load->database();
        $q     = $ci->db->select("count(*) AS coun")->from('purchase')->where('delete_status', 0)->where('purchase_paid_amount !=', 0)->where($where)->get();
        return $q->row();

    }

}if (!function_exists('user_limit'))
{

    function user_limit()
    {
        $ci        = & get_instance();
        $ci->load->database();
        $branch_id = $ci->session->userdata('SESS_BRANCH_ID');
        $q         = $ci->db->select("count(*) AS coun,registered_type,registration_count")->from('customer')->join('common_settings', 'common_settings.branch_id = customer.branch_id')->where('common_settings.branch_id', $branch_id)->get();
        return $q->row_array();

    }

}if (!function_exists('supplier_count'))
{

    function supplier_count()
    {
        $ci        = & get_instance();
        $ci->load->database();
        $branch_id = $ci->session->userdata('SESS_BRANCH_ID');
        $q         = $ci->db->select("count(*) AS scount")->from('supplier')->get();
        return $q->row_array();

    }

}
if (!function_exists('sales_notification'))
{

    function sales_notification()
    {
        $f_year = get_fyear();
        $b_id   = get_b_id();
        $ci     = & get_instance();
        $ci->load->database();
        $ci->load->library('session');
        // $sql    = "SELECT COUNT(t.sales_id) FROM ( SELECT MAX(date) as MaxTime,type_id FROM followup WHERE type='sales' GROUP BY type_id) r right JOIN sales t ON r.type_id = t.sales_id WHERE t.delete_status = 0 AND t.sales_grand_total+t.credit_note_amount != t.sales_paid_amount+t.debit_note_amount and t.branch_id = $b_id AND t.financial_year_id = $f_year";

        $sql = "SELECT COUNT(r.sales_id) FROM (SELECT t.sales_id FROM followup f, sales t WHERE f.type_id = t.sales_id and f.type='sales' and f.date <= cast((now() + interval 2 day) as date) and f.follow_up_status != 1 and t.delete_status = 0 AND t.sales_grand_total+t.credit_note_amount != t.sales_paid_amount+t.debit_note_amount and t.branch_id = $b_id AND t.financial_year_id = $f_year group by t.sales_id) r";

        $query  = $ci->db->query($sql);
        $row    = $query->row();
        return $row;
    }

}
if (!function_exists('default_sales_notification'))
{

    function default_sales_notification()
    {
        $f_year = get_fyear();
        $b_id   = get_b_id();
        $ci     = & get_instance();
        $ci->load->database();
        $ci->load->library('session');

        $sql = "SELECT default_notification_date from common_settings where branch_id = $b_id and delete_status = 0";
        $query  = $ci->db->query($sql);
        $row    = $query->row();

        $sql = "SELECT COUNT(sales_id) FROM sales WHERE cast((now()) as date) = cast((sales_date + interval $row->default_notification_date day) as date) and delete_status = 0 and branch_id = $b_id AND financial_year_id = $f_year";

        $query  = $ci->db->query($sql);
        $row    = $query->row();
        // echo "<pre>";
        // print_r($row);
        // exit;
        return $row;
    }

}
if (!function_exists('purchase_notification'))
{

    function purchase_notification()
    {
        $f_year = get_fyear();
        $b_id   = get_b_id();
        $ci     = & get_instance();
        $ci->load->database();
        // $sql    = "SELECT COUNT(t.purchase_id) FROM ( SELECT MAX(date) as MaxTime,type_id FROM followup WHERE type='purchase' GROUP BY type_id) r right JOIN purchase t ON r.type_id = t.purchase_id WHERE t.delete_status = 0 AND t.branch_id = $b_id AND t.financial_year_id = $f_year";

        $sql = "SELECT COUNT(r.purchase_id) FROM (SELECT t.purchase_id FROM followup f, purchase t WHERE f.type_id = t.purchase_id and f.type='purchase' and f.date <= cast((now() + interval 2 day) as date) and f.follow_up_status != 1 and t.delete_status = 0 AND t.purchase_grand_total+t.credit_note_amount != t.purchase_paid_amount+t.debit_note_amount and t.branch_id = $b_id AND t.financial_year_id = $f_year group by t.purchase_id) r";

        $query  = $ci->db->query($sql);
        $row    = $query->row();
        return $row;
    }

}
if (!function_exists('default_purchase_notification'))
{

    function default_purchase_notification()
    {
        $f_year = get_fyear();
        $b_id   = get_b_id();
        $ci     = & get_instance();
        $ci->load->database();
        $ci->load->library('session');

        $sql = "SELECT default_notification_date from common_settings where branch_id = $b_id and delete_status = 0";
        $query  = $ci->db->query($sql);
        $row    = $query->row();

        $sql = "SELECT COUNT(purchase_id) FROM purchase WHERE cast((now()) as date) = cast((purchase_date + interval $row->default_notification_date day) as date) and delete_status = 0 and branch_id = $b_id AND financial_year_id = $f_year";
        $query  = $ci->db->query($sql);
        $row    = $query->row();
        // echo "<pre>";
        // print_r($row);
        // exit;
        return $row;
    }

}
if (!function_exists('expense_bill_notification'))
{

    function expense_bill_notification()
    {
        $f_year = get_fyear();
        $b_id   = get_b_id();
        $ci     = & get_instance();
        $ci->load->database();
        // $sql    = "SELECT COUNT(t.expense_bill_id) FROM ( SELECT MAX(date) as MaxTime,type_id FROM followup WHERE type='expense bill' GROUP BY type_id) r right JOIN expense_bill t ON r.type_id = t.expense_bill_id WHERE t.delete_status = 0 AND t.branch_id = $b_id AND t.financial_year_id = $f_year";

        $sql = "SELECT COUNT(r.expense_bill_id) FROM (SELECT t.expense_bill_id FROM followup f, expense_bill t WHERE f.type_id = t.expense_bill_id and f.type='expense_bill' and f.date <= cast((now() + interval 2 day) as date) and f.follow_up_status != 1 and t.delete_status = 0 AND t.expense_bill_grand_total != t.expense_bill_paid_amount and t.branch_id = $b_id AND t.financial_year_id = $f_year group by t.expense_bill_id) r";

        $query  = $ci->db->query($sql);
        $row    = $query->row();
        return $row;

    }

}
if (!function_exists('default_expense_bill_notification'))
{

    function default_expense_bill_notification()
    {
        $f_year = get_fyear();
        $b_id   = get_b_id();
        $ci     = & get_instance();
        $ci->load->database();
        $ci->load->library('session');

        $sql = "SELECT default_notification_date from common_settings where branch_id = $b_id and delete_status = 0";
        $query  = $ci->db->query($sql);
        $row    = $query->row();

        $sql = "SELECT COUNT(expense_bill_id) FROM expense_bill WHERE cast((now()) as date) = cast((expense_bill_date + interval $row->default_notification_date day) as date) and delete_status = 0 and branch_id = $b_id AND financial_year_id = $f_year";
        $query  = $ci->db->query($sql);
        $row    = $query->row();
        // echo "<pre>";
        // print_r($row);
        // exit;
        return $row;
    }

}

function get_b_id()
{
    $ci = & get_instance();
    $ci->load->library('session');
    return $ci->session->userdata('SESS_BRANCH_ID');

}

function get_fyear()
{
    $ci = & get_instance();
    $ci->load->library('session');
    return $ci->session->userdata('SESS_FINANCIAL_YEAR_ID');

}
