<?php
class Common
{
    public function __construct()
    {
        //  $this->CI = get_instance();
        $this->ci = &get_instance();
    }
    public function log_list_field()
    {
        $string = "l.*,u.first_name,u.last_name";
        $table  = "log l";
        $join   = [
            'users u' => 'l.user_id=u.id'];
        $where = array(
            'l.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $order = [
            'l.id' => 'desc'];
        $filter = array(
            'l.message',
            'u.first_name',
            'u.last_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function invoice_count_field($primary_id, $table_name, $count_condition, $invoice_type,$brand_id=0)
    {
        $string = "count(" . $primary_id . ") as invoice_count";
        $table  = $table_name;
        $where  = array(
            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        
        if($brand_id) $where['brand_id'] = $brand_id;

        if ($invoice_type != "regular")
        {
            $where['financial_year_id'] = $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        }
        if ($count_condition != "")
        {
            $cond = explode("=>", $count_condition);
            // $month_year                 = explode("-", trim($cond[1]));
            $where[trim($cond[0])] = trim($cond[1]);
        }
        $order = [
            $primary_id => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }

    public function reference_count_field($primary_id, $table_name, $count_condition, $invoice_type)
    {
        $string = "count(" . $primary_id . ") as invoice_count";
        $table  = $table_name;
        $where  = 'branch_id = ' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and (reference_number is not null and reference_number!="")';
        if ($invoice_type != "regular")
        {
            $where .= ' and financial_year_id = ' . $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        }
        if ($count_condition != "")
        {
            $cond = explode("=>", $count_condition);
            // $month = explode("-", trim($cond[1]));
            $where .= ' and ' . trim($cond[0]) . '=' . trim($cond[1]);
        }
        $order = [
            $primary_id => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_customer_invoice_number_field($customer_id, $balance = "", $currency_id = "")
    {
        $string = "*,customer_payable_amount as sales_grand_total";
        $table  = 'sales';
        $where  = array(
            'branch_id'        => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'delete_status'    => 0,
            'sales_party_id'   => $customer_id,
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            /*'is_edit'   => '0',*/
            'sales_party_type' => 'customer'
            // 'sales_paid_amount !='=>0
        );
        if ($balance == "0")
        {
            /*$where['(sales_grand_total+credit_note_amount-debit_note_amount-sales_paid_amount) >'] = 0;*/
            $where['(customer_payable_amount-sales_paid_amount) >'] = 0;
        }
        
        /*if ($balance == "1")
        {
            $where['sales_paid_amount >'] = 0;
        }*/
        if ($currency_id > 0)
        {
            $where['currency_id'] = $currency_id;
        }
        $order = [
            'sales_id' => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_customer_sales_invoice_number($customer_id, $balance = "", $currency_id = "")
    {
        $string = "*,customer_payable_amount as sales_grand_total";
        $table  = 'sales';
        $where  = array(
            'branch_id'        => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'delete_status'    => 0,
            'sales_party_id'   => $customer_id,
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'sales_party_type' => 'customer'
            // 'sales_paid_amount !='=>0
        );
        $order = [
            'sales_id' => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_customer_sales_credit_invoice_number($customer_id, $balance = "", $currency_id = "")
    {
        $string = "*";
        $table  = 'sales_credit_note';
        $where  = array(
            'branch_id'        => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'delete_status'    => 0,
            'sales_credit_note_party_id'   => $customer_id,
            'sales_credit_note_party_type' => 'customer'
            // 'sales_paid_amount !='=>0
        );
        $order = [
            'sales_credit_note_id' => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_customer_sales_debit_invoice_number($customer_id, $balance = "", $currency_id = "")
    {
        $string = "*";
        $table  = 'sales_debit_note';
        $where  = array(
            'branch_id'        => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'delete_status'    => 0,
            'sales_debit_note_party_id'   => $customer_id,
            'sales_debit_note_party_type' => 'customer'
            // 'sales_paid_amount !='=>0
        );
        $order = [
            'sales_debit_note_id' => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_supplier_purchase_return_invoice_number($supplier_id)
    {
        $string = "*";
        $table  = 'purchase_return';
        $where  = array(
            'branch_id'        => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'delete_status'    => 0,
            'purchase_return_party_id'   => $supplier_id,
            'purchase_return_party_type' => 'supplier'
            // 'sales_paid_amount !='=>0
        );
        $order = [
            'purchase_return_id' => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_supplier_invoice_number_field($supplier_id, $balance = "", $currency_id = "")
    {
        $string = "*,supplier_payable_amount as purchase_grand_total";
        $table  = 'purchase';
        $where  = array(
            'branch_id'           => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'delete_status'       => 0,
            'purchase_party_id'   => $supplier_id,
            'purchase_party_type' => 'supplier'
            // 'purchase_paid_amount !='=>0
        );
        if ($balance == "0")
        {
            /*$where['((purchase_grand_total+credit_note_amount)-(purchase_paid_amount+debit_note_amount)) >'] = 0;*/
        }
        else
        {
        }
        $where['financial_year_id'] = $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        if ($balance == "1")
        {
            /*$where['purchase_paid_amount >'] = 0;*/
            $where['(supplier_payable_amount-purchase_paid_amount) >'] = 0; 
            /*$where['is_edit'] = '0';*/
        }
        if ($currency_id > 0)
        {
            $where['currency_id'] = $currency_id;
        }
        $order = [
            'purchase_id' => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_expense_supplier_invoice_number_field($supplier_id, $balance = "", $currency_id = "")
    {
        $string = "eb.expense_bill_id as purchase_id,eb.supplier_receivable_amount as purchase_grand_total,eb.expense_bill_invoice_number as purchase_invoice_number,eb.expense_bill_paid_amount as purchase_paid_amount,'0' as purchase_return_amount";
        $table  = 'expense_bill eb';
        $where  = array(
            'eb.branch_id'               => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'eb.delete_status'           => 0,
            'eb.expense_bill_payee_id'   => $supplier_id,
            'eb.expense_bill_payee_type' => 'supplier'
            // 'purchase_paid_amount !='=>0
        );
        if ($balance == 0)
        {
            $where['(eb.expense_bill_grand_total-eb.expense_bill_paid_amount) >'] = 0;
        }
        if ($currency_id > 0)
        {
            $where['currency_id'] = $currency_id;
        }
        $order = [
            'eb.expense_bill_id' => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function currency_field()
    {
        $string = "cr.*,c.country_shortname,c.country_name";
        $table  = "currency cr";
        $join   = ['countries c' => 'cr.country_id = c.country_id'. '#' . 'left'];
        $where  = array('cr.delete_status' => 0);
        $order = ['currency_name' => "asc"];
        // if(!empty($this->ci->session->userdata('SESS_DEFAULT_CURRENCY')))
        // {
        //$where['currency.currency_id']=$this->ci->session->userdata('SESS_DEFAULT_CURRENCY');
        // }
        $data = array(
            'string' => $string,
            'join'   => $join,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function sales_list_field($order_ser='',$dir = ''){
        $string = "s.*,bst.state_name as place_of_supply,c.customer_id, st.customer_id as ship_to_id,c.customer_name,st.customer_name as ship_to_name, c.customer_code, c.customer_gst_registration_type, c.customer_gstin_number, c.customer_pan_number, c.customer_tan_number, c.customer_address, c.customer_country_id, c.customer_state_id, c.customer_state_code, c.customer_city_id, c.customer_postal_code, c.customer_mobile, c.customer_email, c.customer_contact_person_id, c.ledger_id,u.first_name,u.last_name, cur.currency_name, cur.currency_symbol, cur.currency_code, sc.sales_credit_note_invoice_number, sd.  sales_debit_note_invoice_number, cur.currency_text,sa.shipping_address,ba.shipping_address as bill_to_address ,IFNULL(MAX(f.date), DATE_ADD(s.sales_date,INTERVAL c.due_days DAY)) as receivable_date,IFNULL(DATEDIFF(f.date, CONVERT_TZ(CURRENT_TIMESTAMP,'-07:00','+05:30')), DATEDIFF(DATE_ADD(s.sales_date,INTERVAL c.due_days DAY), CONVERT_TZ(CURRENT_TIMESTAMP,'-07:00','+05:30'))) as due,bst.is_utgst,CASE bst.is_utgst when '1' then s.sales_sgst_amount ELSE 0 END as utgst, CASE bst.is_utgst when '0' then s.sales_sgst_amount ELSE 0
                END as sgst";
        $table  = "sales s";
        $join   = [
            'currency cur' => 's.currency_id = cur.currency_id',
            'customer c'   => 's.sales_party_id = c.customer_id',
            'customer st'   => 's.ship_to_customer_id = st.customer_id' . '#' . 'left',
            'users u'      => 's.added_user_id = u.id',
            'followup f'   => 'f.type_id = s.sales_id AND f.type="sales"' . '#' . 'left',
            'states bst'   => 'bst.state_id = s.sales_billing_state_id' . '#' . 'left',
            'sales_credit_note sc' => 'sc.sales_id = s.sales_id' . '#' . 'left',
            'sales_debit_note sd' => 'sc.sales_id = s.sales_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = s.shipping_address_id' . '#' . 'left',
            'shipping_address ba' => 'ba.shipping_address_id = s.billing_address_id' . '#' . 'left'];
            if($order_ser =='' || $dir == ''){
                $order = [ 's.sales_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        
        $where = array(
            's.delete_status'     => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'DATE_FORMAT(s.sales_date, "%d-%m-%Y")',
            'c.customer_name',
            's.sales_invoice_number',
            's.sales_grand_total');
        $group = array(
            's.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function sales_report_front_field(){
        $string = "count(*) as total_count, sum(sales_grand_total) as total_invoice";
        $table  = "sales";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        // $group = array(
        //     'sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function followup_sales_list_field($default = 0)
    {
        $string = "s.*,bst.state_name as place_of_supply,c.customer_id, c.customer_name, c.customer_code, c.customer_gst_registration_type, c.customer_gstin_number, c.customer_pan_number, c.customer_tan_number, c.customer_address, c.customer_country_id, c.customer_state_id, c.customer_state_code, c.customer_city_id, c.customer_postal_code, c.customer_mobile, c.customer_email, c.customer_contact_person_id, c.ledger_id,u.first_name,u.last_name, cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,f.follow_up_status,IFNULL(MAX(f.date), DATE_ADD(s.sales_date,INTERVAL 15 DAY)) as receivable_date,IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(s.sales_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP)) as due";
        $table  = "sales s";
        $join   = [
            'currency cur' => 's.currency_id = cur.currency_id',
            'customer c'   => 's.sales_party_id = c.customer_id',
            'users u'      => 's.added_user_id = u.id',
            'followup f'   => 'f.type_id = s.sales_id AND f.type="sales"' . '#' . 'left',
            'states bst'   => 'bst.state_id = s.sales_billing_state_id' . '#' . 'left'];
        $order = [
            "s.sales_id" => "desc"];
// $where  = array(
//         's.delete_status'     => 0,
//         's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
//         's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
// );
        if ($default == 0)
        {
            $where = "f.type_id = s.sales_id and f.type='sales' and f.date <= cast((now() + interval 2 day) as date) and s.delete_status = 0 AND (s.sales_grand_total + s.credit_note_amount) != (s.sales_paid_amount + s.debit_note_amount) and s.branch_id = " . $this->ci->session->userdata('SESS_BRANCH_ID') . " AND s.financial_year_id = " . $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') . "";
        }
        else
        {
            $where = "cast((now()) as date) = cast((s.sales_date + interval $default day) as date) and s.delete_status = 0 and s.branch_id = " . $this->ci->session->userdata('SESS_BRANCH_ID') . " AND s.financial_year_id = " . $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') . "";
        }
        $filter = array(
            'c.customer_name',
            's.sales_invoice_number',
            's.sales_date',
            's.sales_grand_total');
        $group = array(
            's.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_list_field()
    {
        $string = "p.*,s.supplier_name,s.supplier_id,s.ledger_id as supplier_ledger_id,u.first_name,u.last_name,IFNULL(MAX(f.date), DATE_ADD(p.purchase_date,INTERVAL s.payment_days DAY)) as receivable_date,IFNULL(DATEDIFF(f.date, CONVERT_TZ(CURRENT_TIMESTAMP,'-07:00','+05:30')), DATEDIFF(DATE_ADD(p.purchase_date,INTERVAL s.payment_days DAY), CONVERT_TZ(CURRENT_TIMESTAMP,'-07:00','+05:30'))) as due, st.state_name as place_of_supply, st.state_id, st.is_utgst, CASE st.is_utgst when '1' then p.purchase_sgst_amount ELSE 0
                END as utgst, CASE st.is_utgst when '0' then p.purchase_sgst_amount ELSE 0
                END as sgst,";
        //cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,
        $table  = "purchase p";
        $join   = [
            
            "supplier s"   => "p.purchase_party_id=s.supplier_id", //'currency cur' => 'p.currency_id = cur.currency_id',
            "users u"      => "u.id = p.added_user_id",
            'followup f'   => 'f.type_id = p.purchase_id AND f.type="purchase"' . '#' . 'left',
            'states st'   => 'p.purchase_billing_state_id = st.state_id' . '#' . 'left'];
        $order = [
            "p.purchase_id" => "desc"];
        $where = array(
            'p.delete_status'     => 0,
            'p.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'p.purchase_invoice_number',
            'p.purchase_date',
            'p.purchase_grand_total');
        $group = array(
            'p.purchase_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_list_report_field()
    {
        $string = "count(*) as purchase_count, sum(purchase_grand_total) as purchase_grand_total";
        //cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,
        $table  = "purchase";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function boe_list_field()
    {
        $string = "*";
        //cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,
        $table  = "boe";
        $join   = [];
        $order = [
            "boe_id" => "desc"];
        $where = array(
            'delete_status'     => '0',
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = array(
            'reference_number',
            'bank_name',
            'boe_number',
            'boe_grand_total');
        $group = array(
            'boe_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function followup_purchase_list_field($default = 0)
    {
        $string = "p.*,s.supplier_name,s.supplier_id,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text, follow_up_status, IFNULL(MAX(f.date), DATE_ADD(p.purchase_date,INTERVAL 15 DAY)) as receivable_date,IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(p.purchase_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP)) as due";
        $table  = "purchase p";
        $join   = [
            'currency cur' => 'p.currency_id = cur.currency_id',
            "supplier s"   => "p.purchase_party_id=s.supplier_id",
            "users u"      => "u.id = p.added_user_id",
            'followup f'   => 'f.type_id = p.purchase_id AND f.type="purchase"' . '#' . 'left'];
        $order = [
            "p.purchase_id" => "desc"];
// $where  = array(
//         'p.delete_status'     => 0,
//         'p.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
//         'p.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
// );
        if ($default == 0)
        {
            $where = "f.type_id = p.purchase_id and f.type='purchase' and f.date <= cast((now() + interval 2 day) as date) and p.delete_status = 0 AND (p.purchase_grand_total + p.credit_note_amount) != (p.purchase_paid_amount + p.debit_note_amount) and p.branch_id = " . $this->ci->session->userdata('SESS_BRANCH_ID') . " AND p.financial_year_id = " . $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') . "";
        }
        else
        {
            $where = "cast((now()) as date) = cast((p.purchase_date + interval $default day) as date) and p.delete_status = 0 and p.branch_id = " . $this->ci->session->userdata('SESS_BRANCH_ID') . " AND p.financial_year_id = " . $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') . "";
        }
        $filter = array(
            's.supplier_name',
            'p.purchase_invoice_number',
            'p.purchase_date',
            'p.purchase_grand_total');
        $group = array(
            'p.purchase_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function advance_list_field($customer_id, $total_amount, $currency_id = "")
    {
        $string = "a.*,cur.currency_symbol";
        $table  = "advance_voucher a";
        $join   = [
            'currency cur' => 'cur.currency_id=a.currency_id '];
        $order = [
            "a.advance_voucher_id" => "desc"];
        $where = array(
            'a.delete_status'     => 0,
            'a.refund_status'     => 0,
            'a.party_id'          => $customer_id,
            'a.receipt_amount <=' => $total_amount,
            'a.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'a.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
// if($currency_id>0)
// {
//     $where['a.currency_id']=$currency_id;
        // }
        $filter = array(
            'a.voucher_date',
            'a.voucher_number',
            'a.reference_number',
            'a.receipt_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function delivery_challan_list_field()
    {
        $string = "d.*,c.customer_name,u.first_name,u.last_name";
        // /cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text
        $table  = "delivery_challan d";
        $join   = [
            "customer c"   => "d.delivery_challan_party_id = c.customer_id",
            "users u"      => "d.added_user_id = u.id"];
            //'currency cur' => 'd.currency_id = cur.currency_id',
        $order = [
            "d.delivery_challan_id" => "desc"];
        $where = array(
            'd.delete_status'     => 0,
            'd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'd.delivery_challan_invoice_number',
            'd.delivery_challan_date',
            'd.delivery_challan_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function pos_billing_list_field($pos_billing_id = 0)
    {
        $string = "p.*,u.first_name,u.last_name";
        $table  = "pos_billing p";
        $join   = [
            "users u" => "p.added_user_id = u.id"];
        $order = [
            "p.pos_billing_id" => "desc"];
        $where = array(
            'p.delete_status'     => 0,
            'p.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        if ($pos_billing_id > 0)
        {
            $where['pos_billing_id'] = $pos_billing_id;
        }
        $filter = array(
            'p.pos_billing_invoice_number',
            'p.pos_billing_date',
            'p.pos_billing_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function receipt_voucher_list_field_1($id = 0, $order_ser='', $dir = '')
    {
        $string = "rv.*,c.customer_name,c.customer_address,c.customer_mobile,c.customer_email,c.customer_postal_code,c.customer_gstin_number,c.customer_tan_number,c.customer_pan_number,u.first_name,u.last_name";// cur.currency_symbol, cur.currency_code, cur.currency_text
        $table  = "receipt_voucher rv";
        $join   = [
            "customer c"   => "rv.party_id = c.customer_id",
            //'currency cur' => 'rv.currency_id = cur.currency_id',
            "users u"      => "rv.added_user_id = u.id"];
        
        if($order_ser =='' || $dir == ''){
                $order = [ 'rv.receipt_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'rv.delete_status'     => 0,
            'rv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID')
            //'rv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'
        );
        if (isset($id) && $id != 0 && $id != "")
        {
            $where = array(
                'rv.receipt_id' => $id,
                'rv.branch_id'  => $this->ci->session->userdata('SESS_BRANCH_ID')
            );
        }
        $filter = array(
            'c.customer_name',
            'rv.voucher_number',
            'DATE_FORMAT(rv.voucher_date, "%d-%m-%Y")',
            'rv.imploded_receipt_amount',
            'rv.receipt_amount',
            'rv.converted_receipt_amount',
            'rv.imploded_converted_receipt_amount',
            'rv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function receipt_voucher_list_field($id = 0){
        $string = "rv.*,c.customer_name,c.customer_address,c.customer_mobile,c.customer_email,c.customer_postal_code,c.customer_gstin_number,c.customer_tan_number,c.customer_pan_number,u.first_name,u.last_name,rr.receipt_amount as given_receipt_amount, rr.exchange_gain_loss, rr.exchange_gain_loss_type, rr.discount, rr.other_charges,rr.round_off,rr.round_off_icon,rr.Invoice_total_received,rr.Invoice_pending,s.sales_invoice_number,s.sales_id,s.sales_grand_total,rr.receipt_total_paid,rv.receipt_amount as main_receipt_amount, CASE rr.exchange_gain_loss_type when 'minus' THEN rr.exchange_gain_loss*-1 End as minus_gain_loss , CASE rr.exchange_gain_loss_type when 'plus' THEN rr.exchange_gain_loss End as plus_gain_loss, CASE rr.round_off_icon when 'minus' THEN rr.round_off*-1 End as minus_round_off, CASE rr.round_off_icon when 'plus' THEN rr.round_off End as plus_round_off,s.customer_payable_amount";// cur.currency_symbol, cur.currency_code, cur.currency_text
        $table  = "receipt_voucher rv";
        $join   = [
            "customer c"   => "rv.party_id = c.customer_id",
            "receipt_invoice_reference rr" => "rv.receipt_id = rr.receipt_id",
            'sales s' => 's.sales_id = rr.reference_id',
            "users u"      => "rv.added_user_id = u.id"];
        $order = [
            "rv.receipt_id" => "desc"];
        $where = array(
            'rv.delete_status'     => 0,
            'rv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        if (isset($id) && $id != 0 && $id != "")
        {
            $where = array(
                'rv.receipt_id' => $id,
                'rv.branch_id'  => $this->ci->session->userdata('SESS_BRANCH_ID')
            );
        }
        $filter = array(
            'c.customer_name',
            'rv.voucher_number',
            'rv.voucher_date',
            'rv.imploded_receipt_amount',
            'rv.receipt_amount',
            'rv.converted_receipt_amount',
            'rv.imploded_converted_receipt_amount',
            'rv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function receipt_voucher_report_list_field($id = 0){
        $string = "count(*) as receipt_voucher_count, sum(receipt_amount) as total_receipt_voucher_amount";// cur.currency_symbol, cur.currency_code, cur.currency_text
        $table  = "receipt_voucher";
        $join   = ["receipt_invoice_reference rr" => "rv.receipt_id = rr.receipt_id",
                    'sales s' => 'rr.reference_id = s.sales_id'];
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        if (isset($id) && $id != 0 && $id != "")
        {
            $where = array(
                'receipt_id' => $id,
                'branch_id'  => $this->ci->session->userdata('SESS_BRANCH_ID')
            );
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }

    public function sales_voucher_list_field($voucher_type='sales',$order_ser='',$dir = ''){
        $string = "sv.sales_voucher_id,sv.voucher_number,sv.voucher_date,sv.receipt_amount,sv.reference_number,sv.reference_id,sv.reference_type,sv.to_account,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,c.customer_name";
        $table  = "sales_voucher sv";
        $join   = [
            "customer c"   => "sv.party_id = c.customer_id",
            'currency cur' => 'sv.currency_id = cur.currency_id'];
        
        if($order_ser =='' || $dir == ''){
                $order = [ 'sv.sales_voucher_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'sv.reference_type' => $voucher_type,
            'sv.delete_status'     => 0,
            'sv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'sv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'sv.voucher_number',
            'DATE_FORMAT(sv.voucher_date, "%d-%m-%Y")',
            'sv.to_account',
            'sv.receipt_amount',
            'sv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function sales_voucher_details($sales_voucher_id)
    {
        $string = "sv.voucher_number,sv.voucher_date,sv.reference_number,sv.receipt_amount,sv.reference_id,av.accounts_sales_id,av.cr_amount,av.dr_amount,av.voucher_amount,l.ledger_name as from_name,l.ledger_name,l.ledger_name as to_name,av.converted_voucher_amount,sv.currency_converted_rate,av.sales_voucher_id,sv.reference_type";
        $table  = "accounts_sales_voucher av";
        $join   = [
            'sales_voucher sv' => 'sv.sales_voucher_id = av.sales_voucher_id',
            'tbl_ledgers l'        => 'l.ledger_id = av.ledger_id'];
        $where = [
            'av.sales_voucher_id' => $sales_voucher_id,
            'av.delete_status'    => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_voucher_list_field($voucher_type = 'purchase')
    {
        $string = "pv.purchase_voucher_id,pv.voucher_number,pv.voucher_date,pv.receipt_amount,pv.reference_number,pv.reference_type,pv.reference_id,pv.to_account,s.supplier_name";//cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,
        $table  = "purchase_voucher pv";
        $join   = [
            "supplier s"   => "pv.party_id = s.supplier_id"
            ];//'currency cur' => 'pv.currency_id = cur.currency_id'
        $order = [
            "pv.purchase_voucher_id" => "desc"];
        $where = array(
            'pv.reference_type' => $voucher_type,
            'pv.delete_status'     => 0,
            'pv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );//
        $filter = array(
            's.supplier_name',
            'pv.voucher_number',
            'pv.voucher_date',
            'pv.receipt_amount',
            'pv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function expense_voucher_list_field($order_ser='',$dir = '')
    {
        $string = "ev.expense_voucher_id,ev.voucher_number,ev.voucher_date,ev.receipt_amount,ev.reference_number,ev.reference_id,ev.to_account,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,s.supplier_name";
        $table  = "expense_voucher ev";
        $join   = [
            "supplier s"   => "ev.party_id = s.supplier_id",
            'currency cur' => 'ev.currency_id = cur.currency_id'];
        if($order_ser =='' || $dir == ''){
                $order = [ 'ev.expense_voucher_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'ev.delete_status'     => 0,
            'ev.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'ev.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'ev.voucher_number',
            'DATE_FORMAT(ev.voucher_date , "%d-%m-%Y")',
            'ev.receipt_amount',
            'ev.to_account',
            'ev.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function purchase_voucher_details($purchase_voucher_id){
        $string = "pv.voucher_number,pv.voucher_date,pv.reference_number,pv.receipt_amount,pv.reference_id,av.accounts_purchase_id,av.cr_amount,av.dr_amount,l.ledger_name as from_name,l.ledger_name,l.ledger_name as to_name,av.voucher_amount,av.converted_voucher_amount,av.purchase_voucher_id,pv.reference_type";
        $table  = "accounts_purchase_voucher av";
        $join   = [
            'purchase_voucher pv' => 'pv.purchase_voucher_id = av.purchase_voucher_id',
            'tbl_ledgers l'           => 'l.ledger_id = av.ledger_id',
            ];
        $where = [
            'av.purchase_voucher_id' => $purchase_voucher_id,
            'av.delete_status'       => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function expense_voucher_details($expense_voucher_id)
    {
        $string = "ev.voucher_number,ev.voucher_date,ev.reference_number,ev.receipt_amount,ev.reference_id,av.accounts_expense_id,av.cr_amount,av.voucher_amount,av.dr_amount,l.ledger_name,l.ledger_id,av.converted_voucher_amount,av.expense_voucher_id";
        $table  = "accounts_expense_voucher av";
        $join   = [
            'expense_voucher ev' => 'ev.expense_voucher_id = av.expense_voucher_id',
            'tbl_ledgers l'      => 'l.ledger_id = av.ledger_id'];
        $where = [
            'ev.expense_voucher_id' => $expense_voucher_id,
            'ev.delete_status'      => 0,
            'av.delete_status'      => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function contra_voucher_list_field()
    {
        $string = "cv.contra_voucher_id,cv.voucher_number,cv.voucher_date,cv.receipt_amount,cv.reference_number,cv.from_account,cv.to_account,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "contra_voucher cv";
        $join   = [
            'currency cur' => 'cv.currency_id = cur.currency_id'];
        $order = [
            "cv.contra_voucher_id" => "desc"];
        $where = array(
            'cv.delete_status'     => 0,
            'cv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'cv.voucher_number',
            'cv.voucher_date',
            'cv.receipt_amount',
            'cv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function bank_voucher_list_field()
    {
        $string = "cv.bank_voucher_id,cv.voucher_number,cv.voucher_date,cv.receipt_amount,cv.reference_number,cv.from_account,cv.to_account,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "bank_voucher cv";
        $join   = [
            'currency cur' => 'cv.currency_id = cur.currency_id'.'#left'];
        $order = [
            "cv.bank_voucher_id" => "desc"];
        $where = array(
            'cv.delete_status'     => 0,
            'cv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'cv.voucher_number',
            'cv.voucher_date',
            'cv.receipt_amount',
            'cv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function cash_voucher_list_field() {
        $string = "cv.cash_voucher_id,cv.voucher_number,cv.voucher_date,cv.receipt_amount,cv.reference_number,cv.from_account,cv.to_account,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "cash_voucher cv";
        $join   = [
            'currency cur' => 'cv.currency_id = cur.currency_id'.'#left'];
        $order = [
            "cv.cash_voucher_id" => "desc"];
        $where = array(
            'cv.delete_status'     => 0,
            'cv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'cv.voucher_number',
            'cv.voucher_date',
            'cv.receipt_amount',
            'cv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function general_voucher_list_field($order_ser='',$dir = '')
    {
        $string = "gv.general_voucher_id,gv.voucher_number,gv.voucher_date,gv.receipt_amount,gv.reference_number,gv.from_account,gv.to_account,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "general_voucher gv";
        $join   = [
            'currency cur' => 'gv.currency_id = cur.currency_id'];
        if($order_ser =='' || $dir == ''){
                $order = [ 'gv.general_voucher_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'gv.delete_status'     => 0,
            'gv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'gv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'gv.to_account',
            'gv.from_account',
            'gv.voucher_number',
            'DATE_FORMAT(gv.voucher_date, "%d-%m-%Y")',
            'gv.receipt_amount',
            'gv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }


     public function transaction_purpose_list_field($order_ser='',$dir = '')
    {
        $string = "*";
        $table  = "tbl_transaction_purpose";
        $join   = array();
        $where = array('status' => 1,'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $filter = array(
            'transaction_purpose',
            'input_type',
            'voucher_type');
        $order = [ 'id' => 'desc' ];

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function contra_voucher_details($contra_voucher_id)
    {
        $string = "cv.voucher_number,cv.voucher_date,cv.reference_number,cv.receipt_amount,cv.reference_id,av.accounts_contra_id,av.voucher_amount,av.cr_amount,av.dr_amount,l.ledger_name,av.converted_voucher_amount,av.contra_voucher_id";
        $table  = "accounts_contra_voucher av";
        $join   = [
            'contra_voucher cv' => 'cv.contra_voucher_id = av.contra_voucher_id',
            'tbl_ledgers l'          => 'l.ledger_id = av.ledger_id'];
        $where = [
            'av.contra_voucher_id' => $contra_voucher_id,
            'av.delete_status'     => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function bank_voucher_details($bank_voucher_id) {
        $string = "cv.voucher_number,cv.voucher_date,cv.reference_number,cv.receipt_amount,cv.reference_id,av.accounts_bank_id,av.voucher_amount,av.cr_amount,av.dr_amount,l.ledger_name,av.converted_voucher_amount,av.bank_voucher_id";
        $table  = "accounts_bank_voucher av";
        $join   = [
            'bank_voucher cv' => 'cv.bank_voucher_id = av.bank_voucher_id',
            'tbl_ledgers l'          => 'l.ledger_id = av.ledger_id'];
        $where = [
            'av.bank_voucher_id' => $bank_voucher_id,
            'av.delete_status'     => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function cash_voucher_details($cash_voucher_id) {
        $string = "cv.voucher_number,cv.voucher_date,cv.reference_number,cv.receipt_amount,cv.reference_id,av.accounts_cash_id,av.voucher_amount,av.cr_amount,av.dr_amount,l.ledger_name,av.converted_voucher_amount,av.cash_voucher_id";
        $table  = "accounts_cash_voucher av";
        $join   = [
            'cash_voucher cv' => 'cv.cash_voucher_id = av.cash_voucher_id',
            'tbl_ledgers l'          => 'l.ledger_id = av.ledger_id'];
        $where = [
            'av.cash_voucher_id' => $cash_voucher_id,
            'av.delete_status'     => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function general_voucher_details($general_voucher_id){
        $string = "gv.voucher_number,gv.voucher_date,gv.reference_number,gv.receipt_amount,gv.reference_id,av.accounts_general_id,av.cr_amount,av.dr_amount,l.ledger_name,av.converted_voucher_amount,av.voucher_amount,av.general_voucher_id";
        $table  = "accounts_general_voucher av";
        $join   = [
            'general_voucher gv' => 'gv.general_voucher_id = av.general_voucher_id',
            'tbl_ledgers l'          => 'l.ledger_id = av.ledger_id'];
        $where = [
            'av.general_voucher_id' => $general_voucher_id,
            'av.delete_status'      => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function receipt_voucher_details($receipt_voucher_id){
        $string = "rv.voucher_number,rv.voucher_date,rv.reference_number,rv.receipt_amount,rv.converted_receipt_amount,rv.reference_id,av.accounts_receipt_id,av.cr_amount,av.dr_amount,l.ledger_name,av.voucher_amount,av.converted_voucher_amount,av.receipt_voucher_id";
        $table  = "accounts_receipt_voucher av";
        $join   = [
            'receipt_voucher rv' => 'rv.receipt_id = av.receipt_voucher_id',
            'tbl_ledgers l'          => 'l.ledger_id = av.ledger_id'
           ];
        $where = [
            'av.receipt_voucher_id' => $receipt_voucher_id,
            'av.delete_status'      => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function payment_voucher_details($payment_voucher_id) {
        $string = "rv.voucher_number,rv.voucher_date,rv.reference_number,rv.receipt_amount,rv.converted_receipt_amount,rv.reference_id,av.accounts_payment_id,av.cr_amount,av.dr_amount,l.ledger_name,av.voucher_amount,av.converted_voucher_amount,av.payment_voucher_id";
        $table  = "accounts_payment_voucher av";
        $join   = [
            'payment_voucher rv' => 'rv.payment_id = av.payment_voucher_id',
            'tbl_ledgers l'      => 'l.ledger_id = av.ledger_id'
           ];
        $where = [
            'av.payment_voucher_id' => $payment_voucher_id,
            'av.delete_status'      => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    
    /*public function payment_voucher_details($payment_voucher_id)
    {
        $string = "pv.voucher_number,pv.voucher_date,pv.reference_number,pv.receipt_amount,pv.converted_receipt_amount,pv.reference_id,av.accounts_payment_id,av.cr_amount,av.dr_amount,l.ledger_title as from_name,lr.ledger_title as to_name,av.receipt_amount,av.payment_voucher_id";
        $table  = "accounts_payment_voucher av";
        $join   = [
            'payment_voucher pv' => 'pv.payment_id = av.payment_voucher_id',
            'ledgers l'          => 'l.ledger_id = av.ledger_from',
            'ledgers lr'         => 'lr.ledger_id=av.ledger_to'];
        $where = [
            'av.payment_voucher_id' => $payment_voucher_id,
            'av.delete_status'      => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }*/
    public function advance_voucher_details($advance_voucher_id){

        $string = "av.voucher_number, av.voucher_date, av.reference_number, av.receipt_amount, av.receipt_amount, av.reference_id, aav.accounts_advance_id, aav.cr_amount, aav.dr_amount, aav.voucher_amount, l.ledger_name as from_name, l.ledger_name, l.ledger_name as to_name, aav.converted_voucher_amount, aav.advance_voucher_id";
        $table  = "accounts_advance_voucher aav";
        $join   = [
            'advance_voucher av' => 'av.advance_voucher_id = aav.advance_voucher_id',
             'tbl_ledgers l'        => 'l.ledger_id = aav.ledger_id'];
        $where = [
            'aav.advance_voucher_id' => $advance_voucher_id,
            'aav.delete_status'      => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function refund_voucher_details($refund_voucher_id)
    {
        $string = "rv.voucher_number,rv.voucher_date,rv.reference_number,rv.receipt_amount,rv.receipt_amount,rv.reference_id,av.accounts_refund_id,av.cr_amount,av.dr_amount,l.ledger_title as from_name,lr.ledger_title as to_name,av.converted_voucher_amount,av.refund_voucher_id";
        $table  = "accounts_refund_voucher av";
        $join   = [
            'refund_voucher rv' => 'rv.refund_id = av.refund_voucher_id',
            'ledgers l'         => 'l.ledger_id = av.ledger_from',
            'ledgers lr'        => 'lr.ledger_id=av.ledger_to'];
        $where = [
            'av.refund_voucher_id' => $refund_voucher_id,
            'av.delete_status'     => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function advance_voucher_list_field($order_ser='',$dir = '')
    {
       
        $string = "av.*, av.voucher_sub_total as taxable_value, av.voucher_sgst_amount as sgst_amount, av.voucher_cgst_amount as cgst_amount, av.voucher_igst_amount as igst_amount, c.customer_name,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,con.country_name,CASE when av.billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name, st.is_utgst, CASE st.is_utgst when '1' then av.voucher_sgst_amount ELSE 0
                END as utgst, CASE st.is_utgst when '0' then av.voucher_sgst_amount ELSE 0
                END as sgst, (av.receipt_amount - av.adjusted_amount) as unadjusted_amount";
        $table  = "advance_voucher av";
        $join   = [
            'currency cur'  => 'av.currency_id = cur.currency_id' . '#' . 'left',
            "customer c"    => "av.party_id = c.customer_id",
            "users u"       => "av.added_user_id = u.id",
            'states st' => 'st.state_id = av.billing_state_id' . '#' . 'left',
            'countries con' => 'con.country_id = av.billing_country_id' . '#' . 'left']; 
       if($order_ser =='' || $dir == ''){
                $order = [ 'av.advance_voucher_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'av.delete_status'     => 0,
            'av.refund_status'     => 0,
            'av.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(        
            'DATE_FORMAT(av.voucher_date, "%d-%m-%Y")',
            'c.customer_name',
            'av.voucher_number',
            'av.receipt_amount',
            'state_name',
            'av.reference_number',
            '(av.receipt_amount - av.adjusted_amount)');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function advance_voucher_report_list_field()
    {
        $string = "count(*) as advance_count, sum(receipt_amount) as receipt_amount";
        $table  = "advance_voucher";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }
    public function advance_gst_report_list_field($from_date,$to_date)
    {
        $string = "st.state_code, st.state_name as state_name, avi.item_tax_percentage, sum(avi.item_sub_total) as item_grand_total, sum(avi.item_cess_amount) as item_cess_amount";
        $table  = "advance_voucher_item avi";
        $join   = [
            "advance_voucher av"   => "avi.advance_voucher_id = av.advance_voucher_id",
            "states st"     => 'av.billing_state_id = st.state_id'. " # " . 'left'];
        $where = array(
            'av.voucher_date >=' => $from_date,
            'av.voucher_date <=' => $to_date,
            'av.billing_state_id!='     => 0,
            'av.delete_status'     => 0,
            'avi.delete_status'     => 0,
            'av.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'avi.item_gst_id',
            'av.advance_voucher_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    public function gst_b2cl_report_list_field($from_date,$to_date)
    {
        $string = "s.sales_invoice_number, s.sales_date, s.sales_grand_total, CASE when s.sales_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name,si.sales_item_tax_percentage, sum(si.sales_item_taxable_value) as taxable_value, sum(si.sales_item_tax_cess_amount) as cess_amount, si.sales_item_tax_id, st.state_code"; 
        $table  = "sales_item si";
        $join   = [
            "sales s"   => "si.sales_id = s.sales_id",
            "customer c"    => "s.sales_party_id = c.customer_id". "#" . 'left',
            "states st"     => 's.sales_billing_state_id = st.state_id'. " # " . 'left'];
        $where = array(
            's.sales_date >=' => $from_date,
            's.sales_date <=' => $to_date,
            's.sales_grand_total >=' => 250000,
            'c.customer_gstin_number' => '',
            's.sales_type_of_supply' => 'regular',
            's.delete_status'     => 0,
            'si.delete_status'     => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'si.sales_item_tax_id',
            's.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    public function gst_b2cs_report_list_field($from_date,$to_date)
    {
        $string = "CASE when s.sales_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name,st.state_code, si.sales_item_tax_percentage, sum(si.sales_item_taxable_value) as taxable_value, sum(si.sales_item_tax_cess_amount) as cess_amount"; 
        $table  = "sales_item si";
        $join   = [
            "sales s"   => "si.sales_id = s.sales_id",
            "customer c"    => "s.sales_party_id = c.customer_id". "#" . 'left',
            "states st"     => 's.sales_billing_state_id = st.state_id'. " # " . 'left'];
        $where = array(
            's.sales_date >=' => $from_date,
            's.sales_date <=' => $to_date,
            's.sales_grand_total <' => 250000,
            'c.customer_gstin_number' => '',
            's.sales_type_of_supply' => 'regular',
            's.delete_status'     => 0,
            'si.delete_status'     => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'si.sales_item_tax_id',
            's.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    public function credit_debit_note_b2b_report_list_field($from_date,$to_date)
    { 
        $sql = 'SELECT * FROM ((SELECT cs.customer_gstin_number as gstin_number,cs.customer_name as customer_name,s.sales_invoice_number as invoice_number,s.sales_date as sales_invoice_date,sc.sales_credit_note_invoice_number as note_invoice_number,sc.sales_credit_note_date as note_invoice_date,st.state_code as state_code,st.state_name as state_name,sc.sales_credit_note_grand_total as note_voucher_grand_value,scn.sales_credit_note_item_tax_percentage as gst_value,sum(scn.sales_credit_note_item_taxable_value) as taxable_value,sum(scn.sales_credit_note_item_tax_cess_amount) as cess_amount,"C" as document_type FROM sales_credit_note_item scn join sales_credit_note sc on sc.sales_credit_note_id=scn.sales_credit_note_id join sales s on s.sales_id=sc.sales_id left join customer cs on cs.customer_id=sc.sales_credit_note_party_id left join states st on sc.sales_credit_note_billing_state_id = st.state_id where s.sales_type_of_supply = "regular" and sc.sales_credit_note_date >="'.$from_date.'" and sc.sales_credit_note_date <="'.$to_date.'" and cs.customer_gstin_number != "" and scn.delete_status=0 and sc.delete_status=0 and s.delete_status=0 and sc.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'" and sc.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'"  group by gst_value,sc.sales_credit_note_id) UNION ALL (SELECT cs.customer_gstin_number as gstin_number,cs.customer_name as customer_name,s.sales_invoice_number as invoice_number,s.sales_date as sales_invoice_date,sd.sales_debit_note_invoice_number as note_invoice_number,sd.sales_debit_note_date as note_invoice_date,st.state_code as state_code,st.state_name as state_name,sd.sales_debit_note_grand_total as note_voucher_grand_value,sdn.sales_debit_note_item_tax_percentage as gst_value,sum(sdn.sales_debit_note_item_taxable_value) as taxable_value,sum(sdn.sales_debit_note_item_tax_cess_amount) as cess_amount,"D" as document_type FROM sales_debit_note_item sdn join sales_debit_note sd on sd.sales_debit_note_id=sdn.sales_debit_note_id join sales s on s.sales_id=sd.sales_id left join customer cs on cs.customer_id=sd.sales_debit_note_party_id left join states st on sd.sales_debit_note_billing_state_id=st.state_id where s.sales_type_of_supply = "regular" and sd.sales_debit_note_date >="'.$from_date.'" and sd.sales_debit_note_date <="'.$to_date.'" and cs.customer_gstin_number != "" and sdn.delete_status=0 and sd.delete_status=0 and s.delete_status=0 and sd.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') . '" and sd.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'" group by gst_value,sd.sales_debit_note_id)) as t';

        return $sql;
    }
    public function credit_debit_note_b2c_report_list_field($from_date,$to_date)
    { 
        $sql = 'SELECT * FROM ((SELECT sc.sales_credit_note_invoice_number as note_invoice_number,sc.sales_credit_note_date as note_invoice_date,st.state_code as state_code,st.state_name as state_name, st.state_name as place_of_supply, sc.sales_credit_note_grand_total as note_voucher_grand_value,scn.sales_credit_note_item_tax_percentage as gst_value,sum(scn.sales_credit_note_item_taxable_value) as taxable_value,sum(scn.sales_credit_note_item_tax_cess_amount) as cess_amount,s.sales_invoice_number as invoice_number,s.sales_date as sales_invoice_date,s.sales_type_of_supply as type_of_supply, "C" as document_type FROM sales_credit_note_item scn join sales_credit_note sc on sc.sales_credit_note_id=scn.sales_credit_note_id join sales s on s.sales_id=sc.sales_id left join customer cs on cs.customer_id=sc.sales_credit_note_party_id left join states st on sc.sales_credit_note_billing_state_id=st.state_id where sc.sales_credit_note_date >="'.$from_date.'" and sc.sales_credit_note_date <="'.$to_date.'" and cs.customer_gstin_number = "" and scn.delete_status=0 and sc.delete_status=0 and s.delete_status=0 and sc.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'" and sc.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'"  group by scn.sales_credit_note_item_tax_id, sc.sales_credit_note_id) UNION ALL (SELECT sd.sales_debit_note_invoice_number as note_invoice_number,sd.sales_debit_note_date as note_invoice_date,st.state_code as state_code,st.state_name as state_name, st.state_name as place_of_supply,sd.sales_debit_note_grand_total as note_voucher_grand_value,sdn.sales_debit_note_item_tax_percentage as gst_value,sum(sdn.sales_debit_note_item_taxable_value) as taxable_value,sum(sdn.sales_debit_note_item_tax_cess_amount) as cess_amount,s.sales_invoice_number as invoice_number,s.sales_date as sales_invoice_date,s.sales_type_of_supply as type_of_supply, "D" as document_type FROM sales_debit_note_item sdn join sales_debit_note sd on sd.sales_debit_note_id=sdn.sales_debit_note_id join sales s on s.sales_id=sd.sales_id left join customer cs on cs.customer_id=sd.sales_debit_note_party_id left join states st on sd.sales_debit_note_billing_state_id=st.state_id where sd.sales_debit_note_date >="'.$from_date.'" and sd.sales_debit_note_date <="'.$to_date.'" and cs.customer_gstin_number = "" and sdn.delete_status=0 and sd.delete_status=0 and s.delete_status=0 and sd.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') . '" and sd.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'" group by sdn.sales_debit_note_item_tax_id, sd.sales_debit_note_id)) as t';
        return $sql;
    }
    public function credit_debit_note_b2cs_report_list_field($from_date,$to_date)
    { 
        $sql = 'SELECT * FROM ((SELECT sc.sales_credit_note_invoice_number as note_invoice_number,sc.sales_credit_note_date as note_invoice_date,st.state_code as state_code,st.state_name as state_name, st.state_name as place_of_supply, sc.sales_credit_note_grand_total as note_voucher_grand_value,scn.sales_credit_note_item_tax_percentage as gst_value,sum(scn.sales_credit_note_item_taxable_value) as taxable_value,sum(scn.sales_credit_note_item_tax_cess_amount) as cess_amount,s.sales_invoice_number as invoice_number,s.sales_date as sales_invoice_date,s.sales_type_of_supply as type_of_supply, "C" as document_type FROM sales_credit_note_item scn join sales_credit_note sc on sc.sales_credit_note_id=scn.sales_credit_note_id join sales s on s.sales_id=sc.sales_id left join customer cs on cs.customer_id=sc.sales_credit_note_party_id left join states st on sc.sales_credit_note_billing_state_id=st.state_id where sc.sales_credit_note_date >="'.$from_date.'" and sc.sales_credit_note_date <="'.$to_date.'" and cs.customer_gstin_number = "" and s.sales_type_of_supply = "regular" and sc.sales_credit_note_grand_total < 250000 and scn.delete_status=0 and sc.delete_status=0 and s.delete_status=0 and sc.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'" and sc.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'"  group by scn.sales_credit_note_item_tax_id, sc.sales_credit_note_id) UNION ALL (SELECT sd.sales_debit_note_invoice_number as note_invoice_number,sd.sales_debit_note_date as note_invoice_date,st.state_code as state_code,st.state_name as state_name, st.state_name as place_of_supply,sd.sales_debit_note_grand_total as note_voucher_grand_value,sdn.sales_debit_note_item_tax_percentage as gst_value,sum(sdn.sales_debit_note_item_taxable_value) as taxable_value,sum(sdn.sales_debit_note_item_tax_cess_amount) as cess_amount,s.sales_invoice_number as invoice_number,s.sales_date as sales_invoice_date,s.sales_type_of_supply as type_of_supply, "D" as document_type FROM sales_debit_note_item sdn join sales_debit_note sd on sd.sales_debit_note_id=sdn.sales_debit_note_id join sales s on s.sales_id=sd.sales_id left join customer cs on cs.customer_id=sd.sales_debit_note_party_id left join states st on sd.sales_debit_note_billing_state_id=st.state_id where sd.sales_debit_note_date >="'.$from_date.'" and sd.sales_debit_note_date <="'.$to_date.'" and cs.customer_gstin_number = "" and s.sales_type_of_supply = "regular" and sd.sales_debit_note_grand_total < 250000 and sdn.delete_status=0 and sd.delete_status=0 and s.delete_status=0 and sd.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') . '" and sd.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'" group by sdn.sales_debit_note_item_tax_id, sd.sales_debit_note_id)) as t';
        return $sql;
    }
    public function exempt_supply_report_list_field($from_date,$to_date)
    { 
        $sql = 'SELECT * FROM ((select "Intra-State supplies to registered persons" as discription, sum(si.sales_item_taxable_value) as amount from sales s JOIN customer cs ON cs.customer_id = s.sales_party_id JOIN sales_item si ON si.sales_id = s.sales_id where s.sales_billing_state_id IN (SELECT branch_state_id FROM branch where branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'") AND s.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'" AND s.sales_date >="'.$from_date.'" and s.sales_date <="'.$to_date.'" AND s.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'" and si.sales_item_tax_amount = 0 and cs.customer_gstin_number != "" AND s.sales_type_of_supply = "regular" AND si.delete_status = 0 AND s.delete_status = 0) UNION ALL (select "Intra-State supplies to unregistered persons" as discription, sum(si.sales_item_taxable_value) as amount from sales s JOIN customer cs ON cs.customer_id = s.sales_party_id JOIN sales_item si ON si.sales_id = s.sales_id where s.sales_billing_state_id IN (SELECT branch_state_id FROM branch where branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'") AND s.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'" AND s.sales_date >="'.$from_date.'" and s.sales_date <="'.$to_date.'" AND s.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'" and si.sales_item_tax_amount = 0 and cs.customer_gstin_number = "" AND s.sales_type_of_supply = "regular" AND si.delete_status = 0 AND s.delete_status = 0) UNION ALL (select "Inter-State supplies to registered persons" as discription, sum(si.sales_item_taxable_value) as amount from sales s JOIN customer cs ON cs.customer_id = s.sales_party_id JOIN sales_item si ON si.sales_id = s.sales_id where s.sales_billing_state_id NOT IN (SELECT branch_state_id FROM branch where branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'") AND s.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'" AND s.sales_date >="'.$from_date.'" and s.sales_date <="'.$to_date.'" AND s.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'" and si.sales_item_tax_amount = 0 and cs.customer_gstin_number != "" AND s.sales_type_of_supply = "regular" AND si.delete_status = 0 AND s.delete_status = 0) UNION ALL (select "Inter-State supplies to unregistered persons" as discription, sum(si.sales_item_taxable_value) as amount from sales s JOIN customer cs ON cs.customer_id = s.sales_party_id JOIN sales_item si ON si.sales_id = s.sales_id where s.sales_billing_state_id NOT IN (SELECT branch_state_id FROM branch where branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'") AND s.financial_year_id = "'.$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') .'" AND s.sales_date >="'.$from_date.'" and s.sales_date <="'.$to_date.'" AND s.branch_id = "'.$this->ci->session->userdata('SESS_BRANCH_ID') .'" and si.sales_item_tax_amount = 0 and cs.customer_gstin_number = "" AND s.sales_type_of_supply = "regular" AND si.delete_status = 0 AND s.delete_status = 0)) as t';
        return $sql;
    }  

    public function exports_report_list_field($from_date,$to_date)
    {
        $string = "s.sales_invoice_number, s.sales_date,s.sales_grand_total, si.sales_item_tax_percentage, sum(si.sales_item_taxable_value) as taxable_value, sum(si.sales_item_tax_cess_amount) as cess_amount,s.sales_type_of_supply"; 
        $table  = "sales_item si";
        $join   = [
            "sales s"   => "si.sales_id = s.sales_id"];
        $where = array(
            's.sales_date >=' => $from_date,
            's.sales_date <=' => $to_date,
            's.sales_type_of_supply !=' => 'regular',
            'si.delete_status'     => 0,
            's.delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'si.sales_item_tax_id',
            's.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }

    public function sales_gst_documents_report_list_field($access_settings,$from_date,$to_date)
    {
        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $prefix = $first_prefix.$invoice_seperation.$last_prefix;
        $index = 1;
        if($last_prefix == 'month_with_number'){
            $index = 2;
        }
        $string = "SUBSTRING_INDEX(sales_invoice_number, '".$invoice_seperation."',".$index." ) as invoice_prefix, count(sales_invoice_number) as count_invoice,MAX(sales_id) as end_id, MIN(sales_id) as start_id,'sales' as type, 'Invoices for outward supply' as nature_of_document"; 
        $table  = "sales";
        $where = array(
            'sales_date >=' => $from_date,
            'sales_date <=' => $to_date,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'invoice_prefix');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
     public function sales_credit_note_gst_documents_report_list_field($access_settings,$from_date,$to_date)
    {
        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $prefix = $first_prefix.$invoice_seperation.$last_prefix;
        $index = 1;
        if($last_prefix == 'month_with_number'){
            $index = 2;
        }
        $string = "SUBSTRING_INDEX(sales_credit_note_invoice_number, '".$invoice_seperation."',".$index." ) as invoice_prefix, count(sales_credit_note_invoice_number) as count_invoice,MAX(sales_credit_note_id) as end_id,MIN(sales_credit_note_id) as start_id,'sales_credit_note' as type,'Credit Note' as nature_of_document"; 
        $table  = "sales_credit_note";
        $where = array(
            'sales_credit_note_date >=' => $from_date,
            'sales_credit_note_date <=' => $to_date,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'invoice_prefix');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    public function sales_debit_note_gst_documents_report_list_field($access_settings,$from_date,$to_date)
    {
        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $prefix = $first_prefix.$invoice_seperation.$last_prefix;
        $index = 1;
        if($last_prefix == 'month_with_number'){
            $index = 2;
        }
        $string = "SUBSTRING_INDEX(sales_debit_note_invoice_number, '".$invoice_seperation."',".$index." ) as invoice_prefix, count(sales_debit_note_invoice_number) as count_invoice,MAX(sales_debit_note_id) as end_id,MIN(sales_debit_note_id) as start_id,'sales_debit_note' as type,'Debit Note' as nature_of_document"; 
        $table  = "sales_debit_note";
        $where = array(
            'sales_debit_note_date >=' => $from_date,
            'sales_debit_note_date <=' => $to_date,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'invoice_prefix');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    public function advance_voucher_gst_documents_report_list_field($access_settings,$from_date,$to_date)
    {
        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $prefix = $first_prefix.$invoice_seperation.$last_prefix;
        $index = 1;
        if($last_prefix == 'month_with_number'){
            $index = 2;
        }
        $string = "SUBSTRING_INDEX(voucher_number, '".$invoice_seperation."',".$index." ) as invoice_prefix, count(voucher_number) as count_invoice,MAX(advance_voucher_id) as end_id,MIN(advance_voucher_id) as start_id,'advance_voucher' as type,'Receipt Voucher' as nature_of_document"; 
        $table  = "advance_voucher";
        $where = array(
            'voucher_date >=' => $from_date,
            'voucher_date <=' => $to_date,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'invoice_prefix');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    
    public function delivery_challen_gst_documents_report_list_field($access_settings,$from_date,$to_date)
    {
        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $prefix = $first_prefix.$invoice_seperation.$last_prefix;
        $index = 1;
        if($last_prefix == 'month_with_number'){
            $index = 2;
        }
        $string = "SUBSTRING_INDEX(delivery_challan_invoice_number, '".$invoice_seperation."',".$index." ) as invoice_prefix, count(delivery_challan_invoice_number) as count_invoice,MAX(delivery_challan_id) as end_id,MIN(delivery_challan_id) as start_id,'delivery_challan' as type,'Delivery Challan in case other than by way of supply' as nature_of_document"; 
        $table  = "delivery_challan";
        $where = array(
            'delivery_challan_date >=' => $from_date,
            'delivery_challan_date <=' => $to_date,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'invoice_prefix');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    public function refund_voucher_gst_documents_report_list_field($access_settings,$from_date,$to_date)
    {
        $first_prefix       = $access_settings[0]->settings_invoice_first_prefix;
        $last_prefix        = $access_settings[0]->settings_invoice_last_prefix;
        $invoice_seperation = $access_settings[0]->invoice_seperation;
        $prefix = $first_prefix.$invoice_seperation.$last_prefix;
        $index = 1;
        if($last_prefix == 'month_with_number'){
            $index = 2;
        }
        $string = "SUBSTRING_INDEX(voucher_number, '".$invoice_seperation."',".$index." ) as invoice_prefix, count(voucher_number) as count_invoice,MAX(refund_id) as end_id, MIN(refund_id) as start_id, 'refund_voucher' as type,'Refund Voucher' as nature_of_document"; 
        $table  = "refund_voucher";
        $where = array(
            'voucher_date >=' => $from_date,
            'voucher_date <=' => $to_date,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $group = array(
            'invoice_prefix');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => $group
        );
        return $data;
    }
    public function min_sales_gst_documents_invoice_list_field($minimum)
    {
        $string = "sales_invoice_number as min_invoice_number"; 
        $table  = "sales";
        $where = array(
            'sales_id' => $minimum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function max_sales_gst_documents_invoice_list_field($maximum)
    {
        $string = "sales_invoice_number as max_invoice_number"; 
        $table  = "sales";
        $where = array(
            'sales_id' => $maximum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function min_sales_debit_note_gst_documents_invoice_list_field($minimum)
    {
        $string = "sales_debit_note_invoice_number as min_invoice_number"; 
        $table  = "sales_debit_note";
        $where = array(
            'sales_debit_note_id' => $minimum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function max_sales_debit_note_gst_documents_invoice_list_field($maximum)
    {
        $string = "sales_debit_note_invoice_number as max_invoice_number"; 
        $table  = "sales_debit_note";
        $where = array(
            'sales_debit_note_id' => $maximum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function min_delivery_challen_gst_documents_invoice_list_field($minimum)
    {
        $string = "delivery_challan_invoice_number as min_invoice_number"; 
        $table  = "delivery_challan";
        $where = array(
            'delivery_challan_id' => $minimum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function max_delivery_challen_gst_documents_invoice_list_field($maximum)
    {
        $string = "delivery_challan_invoice_number as max_invoice_number"; 
        $table  = "delivery_challan";
        $where = array(
            'delivery_challan_id' => $maximum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function min_refund_voucher_gst_documents_invoice_list_field($minimum)
    {
        $string = "voucher_number as min_invoice_number"; 
        $table  = "refund_voucher";
        $where = array(
            'refund_id' => $minimum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function max_refund_voucher_gst_documents_invoice_list_field($maximum)
    {
        $string = "voucher_number as max_invoice_number"; 
        $table  = "refund_voucher";
        $where = array(
            'refund_id' => $maximum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function min_advance_voucher_gst_documents_invoice_list_field($minimum)
    {
        $string = "voucher_number as min_invoice_number"; 
        $table  = "advance_voucher";
        $where = array(
            'advance_voucher_id' => $minimum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function max_advance_voucher_gst_documents_invoice_list_field($maximum)
    {
        $string = "voucher_number as max_invoice_number"; 
        $table  = "advance_voucher";
        $where = array(
            'advance_voucher_id' => $maximum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function min_sales_credit_note_gst_documents_invoice_list_field($minimum)
    {
        $string = "sales_credit_note_invoice_number as min_invoice_number"; 
        $table  = "sales_credit_note";
        $where = array(
            'sales_credit_note_id' => $minimum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function max_sales_credit_note_gst_documents_invoice_list_field($maximum)
    {
        $string = "sales_credit_note_invoice_number as max_invoice_number"; 
        $table  = "sales_credit_note";
        $where = array(
            'sales_credit_note_id' => $maximum,
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }
    public function gst_hsn_summary_list_field($from_date,$to_date){
        $string = "sum(SI.sales_item_taxable_value) as sales_item_taxable_value, S.sales_id, SI.sales_item_igst_percentage, SI.sales_item_tax_percentage, SI.sales_item_sgst_percentage,sum(SI.sales_item_sub_total) as sales_item_sub_total, SI.sales_item_cgst_percentage, SI.sales_item_tax_cess_percentage, sum(SI.sales_item_igst_amount) as sales_item_igst_amount, sum(SI.sales_item_sgst_amount) as sales_item_sgst_amount, sum(SI.sales_item_cgst_amount) as sales_item_cgst_amount, sum(SI.sales_item_tax_cess_amount) as sales_item_tax_cess_amount, ST.is_utgst,S.sales_grand_total, P.product_unit_id,
            CASE SI.item_type when 'product' then sum(SI.sales_item_quantity) 
                when 'service' then 0 END as  sales_item_quantity,
            CASE SI.item_type when 'product' then P.product_unit_id 
                when 'service' then SR.service_unit 
                END as  unit_id,
            CASE ST.is_utgst when '1' then SI.sales_item_sgst_amount ELSE 0
                END as UTGST, 
            CASE ST.is_utgst when '0' then SI.sales_item_sgst_amount ELSE 0
                END as SGST,
            CASE SI.item_type when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as  hsn_sac_code, 
            CASE SI.item_type when 'product' then U.uom ELSE UR.uom
                END as T_uom,
            CASE SI.item_type when 'product' then U.description ELSE UR.description
                END as T_description, hs.description";
        $table  = "sales_item SI";
        $join   = [
            'sales S' => 'S.sales_id = SI.sales_id',
            'products P'   => 'SI.item_id = P.product_id  and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id and SI.item_type = "product"' . '#' . 'left',
            'uqc UR'      => 'UR.id = SR.service_unit and SI.item_type = "service"' . '#' . 'left',
            'states ST'   => 'ST.state_id = S.sales_billing_state_id' . '#' . 'left',
            'hsn hs' => '(hs.hsn_code = P.product_hsn_sac_code) OR (hs.hsn_code = SR.service_hsn_sac_code)'. '#' . 'left'];
        $order = [
            "S.sales_id" => "desc"];
        $where = array(
            'S.sales_date >=' => $from_date,
            'S.sales_date <=' => $to_date,
            'S.delete_status'     => 0,
            'SI.delete_status'     => 0,
            'S.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'U.uom',
            'UR.uom',
            'P.product_hsn_sac_code',
            'SI.sales_item_sub_total',
            'SR.service_hsn_sac_code',
            'sales_item_tax_cess_amount',
            'SI.sales_item_taxable_value',
            'SI.sales_item_sgst_amount',
            'SI.sales_item_cgst_amount',
            'SI.sales_item_igst_amount'
            );
        $group = array('hsn_sac_code','T_uom');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function general_bill_list_field()
    {
        $string = "g.*,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "general_bill g";
        $join   = [
            "users u"      => "g.added_user_id = u.id",
            'currency cur' => 'g.currency_id = cur.currency_id'];
        $order = [
            "g.general_bill_id" => "desc"];
        $where = array(
            'g.delete_status'     => 0,
            'g.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'g.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'g.general_bill_invoice_number',
            'g.general_bill_date',
            'g.general_bill_grand_total',
            'g.currency_converted_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function refund_voucher_list_field($order_ser='',$dir = '')
    {
        $string = "rv.*,c.customer_name,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,con.country_name as billing_country,con.country_id, st.state_name as place_of_supply, st.is_utgst, CASE st.is_utgst when '1' then rv.voucher_sgst_amount ELSE 0
                END as utgst, CASE st.is_utgst when '0' then rv.voucher_sgst_amount ELSE 0
                END as sgst";
        $table  = "refund_voucher rv";
        $join   = [
            'currency cur'  => 'rv.currency_id = cur.currency_id',
            "customer c"    => "rv.party_id = c.customer_id",
            "users u"       => "rv.added_user_id = u.id",
            'countries con' => 'con.country_id = rv.billing_country_id',
            "states st"     => 'rv.billing_state_id = st.state_id'. " # " . 'left'];
        if($order_ser =='' || $dir == ''){
                $order = [ 'rv.refund_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'rv.delete_status'     => 0,
            'rv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'DATE_FORMAT(rv.voucher_date, "%d-%m-%Y")',
            'rv.to_account',
            'rv.receipt_amount',
            'rv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function refund_voucher_report_list_field()
    {
        $string = "count(*) as refund_count_voucher, sum(receipt_amount) as receipt_amount";
        $table  = "refund_voucher";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }

    public function payment_voucher_list_field_1($id = 0, $order_ser='', $dir = '')
    {
        $string = "pv.*,s.supplier_name,s.supplier_address,s.supplier_mobile,s.supplier_email,s.supplier_postal_code,s.supplier_gstin_number,s.supplier_tan_number,s.supplier_pan_number,u.first_name,u.last_name, cur.currency_name";

       
        $table  = "payment_voucher pv";
        $join   = [
            "supplier s"   => "pv.party_id = s.supplier_id",            
            'currency cur' => 'pv.currency_id = cur.currency_id'. '#' .'left',
            "users u"      => "pv.added_user_id = u.id"];
        if($order_ser =='' || $dir == ''){
                $order = [ 'pv.payment_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            } 
        $where = array(
            'pv.delete_status'     => 0,
            'pv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        if (isset($id) && $id != 0 && $id != "")
        {
            $where = array(
                'pv.payment_id' => $id,
                'pv.branch_id'  => $this->ci->session->userdata('SESS_BRANCH_ID')
            );
        }
        $filter = array(
            's.supplier_name',
            'pv.voucher_number',
            'DATE_FORMAT(pv.voucher_date, "%d-%m-%Y")',
            'pv.receipt_amount',
            'pv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function payment_voucher_list_field($id = 0)
    {
        $string = "pv.*,s.supplier_name,s.supplier_address,s.supplier_mobile,s.supplier_email,s.supplier_postal_code,s.supplier_gstin_number,s.supplier_tan_number,s.supplier_pan_number,u.first_name,u.last_name, cur.currency_name, pr.exchange_gain_loss, pr.exchange_gain_loss_type, pr.discount, pr.other_charges,pr.round_off,pr.round_off_icon,pr.Invoice_total_paid,pr.Invoice_pending,pr.payment_amount,pr.reference_type as reference_voucher_type, CASE pr.reference_type when 'purchase' Then p.purchase_invoice_number when 'expense' Then e.expense_bill_invoice_number 
             when 'excess_amount' Then 'Excess Amount' END as purchase_invoice_number, CASE pr.reference_type when 'purchase' Then p.purchase_grand_total when 'expense' Then e.supplier_receivable_amount   when 'excess_amount' Then 0 END as invoice_amount, , CASE pr.reference_type when 'purchase' Then p.purchase_id when 'expense' Then e.expense_bill_id END as purchase_id, CASE pr.exchange_gain_loss_type when 'minus' THEN pr.exchange_gain_loss*-1 End as minus_gain_loss , CASE pr.exchange_gain_loss_type when 'plus' THEN pr.exchange_gain_loss End as plus_gain_loss, CASE pr.round_off_icon when 'minus' THEN pr.round_off*-1 End as minus_round_off, CASE pr.round_off_icon when 'plus' THEN pr.round_off End as plus_round_off,CASE pr.reference_type when 'purchase' Then p.purchase_grand_total-pr.Invoice_pending when 'expense' Then e.supplier_receivable_amount-pr.Invoice_pending   when 'excess_amount' Then 0 END as total_received_amount";
 
        $table  = "payment_voucher pv";
        $join   = [
            "supplier s"   => "pv.party_id = s.supplier_id",
            "payment_invoice_reference pr" => "pv.payment_id = pr.payment_id",
            "purchase p" => 'p.purchase_id = pr.reference_id'. '#' .'left',
            "expense_bill e" => 'e.expense_bill_id = pr.reference_id'. '#' .'left',
            'currency cur' => 'pv.currency_id = cur.currency_id'. '#' .'left',
            "users u"      => "pv.added_user_id = u.id"];
        $order = [
            "pv.payment_id" => "desc"];
        $where = array(
            'pv.delete_status'     => 0,
            'pv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        if (isset($id) && $id != 0 && $id != "")
        {
            $where = array(
                'pv.payment_id' => $id,
                'pv.branch_id'  => $this->ci->session->userdata('SESS_BRANCH_ID')
            );
        }
        $filter = array(
            's.supplier_name',
            'pv.voucher_number',
            'pv.voucher_date',
            'pv.receipt_amount',
            'pv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function payment_voucher_report_list_field()
    {
        $string = "count(*) as payment_voucher_count, sum(receipt_amount) as payment_voucher_paid_amount";
 
        $table  = "payment_voucher pv";
        $join   = [
            "supplier s"   => "pv.party_id = s.supplier_id",
            "payment_invoice_reference pr" => "pv.payment_id = pr.payment_id",
            "purchase p" => 'p.purchase_id = pr.reference_id'. '#' .'left',
            "expense_bill e" => 'e.expense_bill_id = pr.reference_id'. '#' .'left',
            'currency cur' => 'pv.currency_id = cur.currency_id'. '#' .'left',
            "users u"      => "pv.added_user_id = u.id"];
        $where = array(
            'pv.delete_status'     => 0,
            'pv.receipt_amount !=' => 0,
            'pv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }
    public function boe_voucher_list_field($voucher_type='boe')
    {
        $string = "sv.payment_id,sv.voucher_number,sv.voucher_date,sv.receipt_amount,sv.reference_number,sv.reference_id,sv.to_account,sv.from_account";
        $table  = "payment_voucher sv";
        
        $order = ["sv.payment_id" => "desc"];
        $where = array(
            'sv.reference_type' => $voucher_type,
            'sv.delete_status'     => 0,
            'sv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'sv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'sv.voucher_number',
            'sv.voucher_date',
            'sv.receipt_amount',
            'sv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function boe_voucher_details($payment_voucher_id)
    {
        $string = "sv.voucher_number,sv.voucher_date,sv.reference_number,sv.receipt_amount,sv.reference_id,av.accounts_payment_id,av.cr_amount,av.dr_amount,av.voucher_amount,l.ledger_name as from_name,l.ledger_name,l.ledger_name as to_name,av.payment_voucher_id,sv.reference_type";
        $table  = "accounts_payment_voucher av";
        $join   = [
            'payment_voucher sv' => 'sv.payment_id = av.payment_voucher_id',
            'tbl_ledgers l'        => 'l.ledger_id = av.ledger_id'];
        $where = [
            'av.payment_voucher_id' => $payment_voucher_id,
            'av.delete_status'    => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
  
    public function quotation_list_field($order_ser='',$dir = '')
    {
        $string = "q.*,c.customer_name,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "quotation q";
        $join   = [
            'currency cur' => 'q.currency_id = cur.currency_id',
            "customer c"   => "q.quotation_party_id = c.customer_id",
            "users u"      => "q.added_user_id = u.id"];
        if($order_ser =='' || $dir == ''){
                $order = [ 'q.quotation_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'q.delete_status'     => 0,
            'q.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'q.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'DATE_FORMAT(q.quotation_date, "%d-%m-%Y")',
            'q.quotation_invoice_number',
            'q.quotation_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function performa_list_field($order_ser='',$dir = ''){
        $string = "q.*,c.customer_name,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "performa q";
        $join   = [
            'currency cur' => 'q.currency_id = cur.currency_id',
            "customer c"   => "q.performa_party_id = c.customer_id",
            "users u"      => "q.added_user_id = u.id"];
        if($order_ser =='' || $dir == ''){
                $order = [ 'q.performa_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'q.delete_status'     => 0,
            'q.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'q.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'DATE_FORMAT(q.performa_date, "%d-%m-%Y")',
            'q.performa_invoice_number',
            'q.performa_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
//  function expense_bill_list_field()
// {
//     $string = "e.expense_bill_id,e.expense_bill_date,e.expense_bill_paid_amount,e.expense_bill_invoice_number,e.expense_bill_grand_total,e.currency_converted_amount,s.supplier_name,u.first_name,u.last_name,cur.currency_symbol,cur.currency_id,IFNULL(MAX(f.date), DATE_ADD(e.expense_bill_date,INTERVAL 15 DAY)) as receivable_date,IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(e.expense_bill_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP)) as due";
//     $table ="expense_bill e";
//     $join =['currency cur'=>'e.currency_id = cur.currency_id',
//     "supplier s"=>"e.expense_bill_payee_id = s.supplier_id",
//     "users u"=>"e.added_user_id = u.id",
//     'followup f'=>'f.type_id = e.expense_bill_id AND f.type="expense bill"'.'#'.'left'];
//     $order =["e.expense_bill_id"=>"desc"];
//     $where=array('e.delete_status' =>0,
//                 'e.branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),
//             'e.financial_year_id' =>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
//             'f.type' => 'expense bill'
//         );
//      $group = array('e.expense_bill_id');
//     $filter=array('e.expense_bill_date','e.expense_bill_invoice_number','e.payee_name','e.expense_bill_grand_total');
//     $data = array('string' => $string,
//                 'table' => $table,
//                 'where' => $where,
//                 'join' => $join,
//                 'filter' => $filter,
//                 'order' => $order,
//                 'group' => $group
//                  );
//     return  $data;
// }
// $join =['currency cur'=>'e.currency_id = cur.currency_id',
//     "supplier s"=>"e.expense_bill_payee_id = s.supplier_id",
//     "users u"=>"e.added_user_id = u.id",
    //     'followup f'=>'f.type_id = e.expense_bill_id AND f.type="expense bill"'.'#'.'left'];
    public function expense_bill_list_field()
    {
        $string = "e.*,s.supplier_name,s.supplier_id,u.first_name,u.last_name,IFNULL(MAX(f.date), DATE_ADD(e.expense_bill_date,INTERVAL s.payment_days DAY)) as receivable_date,IFNULL(DATEDIFF(f.date, CONVERT_TZ(CURRENT_TIMESTAMP,'-07:00','+05:30')), DATEDIFF(DATE_ADD(e.expense_bill_date,INTERVAL s.payment_days  DAY), CONVERT_TZ(CURRENT_TIMESTAMP,'-07:00','+05:30'))) as due, ep.expense_id, ep.expense_title, eb.    expense_bill_item_tds_amount, eb.expense_bill_item_igst_amount, eb.expense_bill_item_cgst_amount, eb.expense_bill_item_sgst_amount, eb.expense_bill_item_taxable_value, st.is_utgst, CASE st.is_utgst when '1' then eb.expense_bill_item_sgst_amount ELSE 0 END as utgst, CASE st.is_utgst when '0' then eb.expense_bill_item_sgst_amount ELSE 0
                END as sgst, eb.expense_bill_item_grand_total";
        $table  = "expense_bill e";
        $join   = [
            "supplier s"   => "e.expense_bill_payee_id=s.supplier_id",//'currency cur' => 'e.currency_id = cur.currency_id',
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id",
            "expense ep" =>"eb.expense_type_id = ep.expense_id",
            "users u"      => "u.id = e.added_user_id",
            "states st"  =>"e.expense_bill_billing_state_id = st.state_id"."#"."left",
            'followup f'   => 'f.type_id = e.expense_bill_id AND f.type="expense_bill"' . '#' . 'left'];
        $order = [
            "e.expense_bill_id" => "desc"];
        $where = array(
            'e.delete_status'     => 0,
            'e.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'e.expense_bill_invoice_number',
            'e.expense_bill_date',
            'e.expense_bill_grand_total');
        $group = array(
            'eb.expense_bill_item_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function expense_bill_report_list_field()
    {
        $string = "count(*) as expences_count, sum(eb.expense_bill_item_grand_total) as expense_bill_grand_total";
        $table  = "expense_bill e";
         $join   = [
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id"];
        $where = array(
            'e.delete_status'     => 0,
            'e.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => '',
            'order'  => '',
            'group'  => ''
        );
        return $data;
    }

    public function expense_bill_list_field_1($order_ser='',$dir = '')
    {
        $string = "e.*,s.supplier_name,s.supplier_id,u.first_name,u.last_name, (e.supplier_receivable_amount - e.expense_bill_paid_amount) as balance_payable";
        $table  = "expense_bill e";
        $join   = [
            "supplier s"   => "e.expense_bill_payee_id=s.supplier_id",//'currency cur' => 'e.currency_id = cur.currency_id',
            "users u"      => "u.id = e.added_user_id"];
        if($order_ser =='' || $dir == ''){
                $order = [ 'e.expense_bill_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            } 
        $where = array(
            'e.delete_status'     => 0,
            'e.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'e.expense_bill_invoice_number',
            'DATE_FORMAT(e.expense_bill_date, "%d-%m-%Y")',
            'e.expense_bill_grand_total',
            'e.supplier_receivable_amount',
            'e.expense_bill_paid_amount',
            '(e.supplier_receivable_amount - e.expense_bill_paid_amount)');
        $group = array();
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }

    public function followup_expense_bill_list_field($default = 0)
    {
        $string = "e.*,s.supplier_name,s.supplier_id,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text, follow_up_status, IFNULL(MAX(f.date), DATE_ADD(e.expense_bill_date,INTERVAL 15 DAY)) as receivable_date,IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(e.expense_bill_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP)) as due";
        $table  = "expense_bill e";
        $join   = [
            'currency cur' => 'e.currency_id = cur.currency_id',
            "supplier s"   => "e.expense_bill_payee_id=s.supplier_id",
            "users u"      => "u.id = e.added_user_id",
            'followup f'   => 'f.type_id = e.expense_bill_id AND f.type="expense_bill"' . '#' . 'left'];
        $order = [
            "e.expense_bill_id" => "desc"];
// $where  = array(
//         'e.delete_status'     => 0,
//         'e.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
//         'e.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
// );
        if ($default == 0)
        {
            $where = "f.type_id = e.expense_bill_id and f.type='expense_bill' and f.date <= cast((now() + interval 2 day) as date) and f.follow_up_status != 1 and e.delete_status = 0 AND e.expense_bill_grand_total != e.expense_bill_paid_amount and e.branch_id = " . $this->ci->session->userdata('SESS_BRANCH_ID') . " AND e.financial_year_id = " . $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') . "";
        }
        else
        {
            $where = "cast((now()) as date) = cast((e.expense_bill_date + interval $default day) as date) and e.delete_status = 0 and e.branch_id = " . $this->ci->session->userdata('SESS_BRANCH_ID') . " AND e.financial_year_id = " . $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID') . "";
        }
        $filter = array(
            's.supplier_name',
            'e.purchase_invoice_number',
            'e.purchase_date',
            'e.purchase_grand_total');
        $group = array(
            'e.expense_bill_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
       public function purchase_order_list_field()
    {
        $string = "po.*,s.supplier_name,u.first_name,u.last_name";
        $table  = "purchase_order po";
        $join   = [
            "supplier s"   => "po.purchase_order_party_id = s.supplier_id",
            "users u"      => "po.added_user_id = u.id"];
        $order = [
            "po.purchase_order_id" => "desc"];
        $where = array(
            'po.delete_status'     => 0,
            'po.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'po.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'po.purchase_order_invoice_number',
            'po.purchase_order_date',
            'po.purchase_order_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function purchase_order_list_field1($purchase_order_id)
    {
        $string = "po.*,st1.state_name as place_of_supply,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   sa.contact_person,
                   sa.department,
                   s.supplier_name,
                   s.supplier_address,
                   s.supplier_mobile,
                   s.supplier_email,
                   s.supplier_postal_code,
                   s.supplier_gstin_number,
                   s.supplier_state_id,
                   s.supplier_tan_number,
                   s.supplier_pan_number
                   ";
        $table = "purchase_order po";
        $join  = [
            
            'supplier s'          => 'po.purchase_order_party_id = s.supplier_id',
            'states st1'          => 'po.purchase_order_billing_state_id = st1.state_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = po.shipping_address_id' . '#' . 'left'];
           /* 'shipping_address sa' => 's.supplier_id = sa.shipping_party_id' . '#' . 'left'];*/
        $where['po.purchase_order_id'] = $purchase_order_id;//'currency cur'        => 'cur.currency_id = po.currency_id',
        /* $where = array(
                'po.purchase_order_id'  => $purchase_order_id,
                'sa.shipping_party_type' => 'supplier',
                'sa.primary_address' => 'yes'
            );*/

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
        }
    public function purchase_return_list_field()
    {
        $string = "pr.*,s.supplier_name,p.purchase_invoice_number,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "purchase_return pr";
        $join   = [
            'purchase p' => 'pr.purchase_id = p.purchase_id',
            'currency cur' => 'pr.currency_id = cur.currency_id' . '#' . 'left',
            "supplier s"   => "pr.purchase_return_party_id=s.supplier_id",
            "users u"      => "u.id = pr.added_user_id"];
        $order = [
            "pr.purchase_return_id" => "desc"];
        $where = array(
            'pr.delete_status'     => 0,
            'pr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'pr.purchase_return_invoice_number',
            'pr.purchase_return_date',
            'pr.purchase_return_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function purchase_list_field1($purchase_id)
    {
        $string = "p.*,st1.state_name as place_of_supply,
                   co.country_name as billing_country,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   sa.contact_person,
                   sa.department,
                   s.supplier_name,
                   s.supplier_address,
                   s.supplier_mobile,
                   s.supplier_email,
                   s.supplier_postal_code,
                   s.supplier_gstin_number,
                   s.supplier_state_id,
                   s.supplier_tan_number,
                   s.supplier_pan_number
                   ";//cur.*,
        $table = "purchase p";
        $join = [
            'supplier s'          => 'p.purchase_party_id = s.supplier_id',//'currency cur'        => 'p.currency_id = cur.currency_id',
            'states st1'          => 'p.purchase_billing_state_id = st1.state_id' . '#' . 'left',
            'countries co'        => 'p.purchase_billing_country_id = co.country_id' . '#' . 'left',

           'shipping_address sa' => 'sa.shipping_address_id = p.shipping_address_id' . '#' . 'left'];

        $where['p.purchase_id'] = $purchase_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_return_list_field1($purchase_return_id)
    {
        $string = "pr.*,p.purchase_id,p.purchase_invoice_number,st1.state_name as place_of_supply,
                   cur.*,
                   sa.shipping_address as shipping_address_sa,
                   sa.shipping_gstin,
                   s.supplier_name,
                   s.supplier_address,
                   s.supplier_mobile,
                   s.supplier_email,
                   s.supplier_postal_code,
                   s.supplier_gstin_number,
                   s.supplier_state_id,
                   s.supplier_tan_number,
                   s.supplier_pan_number,
                   s.supplier_state_code,
                   ct.city_name as supplier_city_name,
                   cs.state_name as supplier_state_name,
                   cu.country_name as supplier_country_name";
        $table = "purchase_return pr";
        $join  = [
            'currency cur'        => 'cur.currency_id = pr.currency_id',
            'purchase p'          => 'pr.purchase_id = p.purchase_id',
            'supplier s'          => 'pr.purchase_return_party_id = s.supplier_id',
            'cities ct'           => 's.supplier_city_id = ct.city_id',
            'states cs'           => 's.supplier_state_id = cs.state_id',
            'countries cu'        => 's.supplier_country_id = cu.country_id',
            'states st1'          => 'pr.purchase_return_billing_state_id = st1.state_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = pr.shipping_address' . '#' . 'left'];
        $where['pr.purchase_return_id'] = $purchase_return_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function module_field()
    {
        $string = "am.*,ua.add_privilege,ua.edit_privilege,ua.view_privilege,
                   ua.delete_privilege";
        $table = "active_modules am";
        $join  = [
            'user_accessibility ua' => 'ua.module_id = am.module_id and ua.user_id =' . $this->ci->session->userdata('SESS_USER_ID') . '#' . 'left'];
        $where = array(
            'am.delete_status' => 0,
            'am.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'ua.delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sa_module_field($user_id, $branch_id)
    {
        $string = "am.*,ua.add_privilege,ua.edit_privilege,ua.view_privilege,
                   ua.delete_privilege";
        $table = "active_modules am";
        $join  = [
            'user_accessibility ua' => 'ua.module_id = am.module_id and ua.user_id =' . $user_id . '#' . 'left'];
        $where = array(
            'am.delete_status' => 0,
            'am.branch_id'     => $branch_id,
            'ua.delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function sa_autoModule_field($branch_id)
    {
        $string = "am.module_id";
        $table = "active_modules am";
        
        $where = array(
            'am.branch_id'     => $branch_id,
            'am.delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function sub_module_field($module_id = "", $branch_id = "")
    {
        $string = "active_sub_modules.*";
        $table  = "active_sub_modules";
        $where  = array(
            'active_sub_modules.delete_status' => 0
        );
        if ($module_id != "")
        {
            $where['active_sub_modules.module_id'] = $module_id;
        }
        if ($branch_id != "")
        {
            $where['active_sub_modules.branch_id'] = $branch_id;
        }
        else
        {
            $where['active_sub_modules.branch_id'] = $this->ci->session->userdata('SESS_BRANCH_ID');
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function common_settings_field($branch_id = "")
    {
        $string = "common_settings.*";
        $table  = "common_settings";
        $where  = array(
            'common_settings.delete_status' => 0);
        if ($branch_id != "")
        {
            $where['common_settings.branch_id'] = $branch_id;
        }
        else
        {
            $where['common_settings.branch_id'] = $this->ci->session->userdata('SESS_BRANCH_ID');
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function user_accessibility_field($user_id = "")
    {
        $string = "*";
        $table  = "user_accessibility";
        $where  = array(
            'delete_status' => 0,
            'user_id'       => $this->ci->session->userdata('SESS_USER_ID'),
            'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($user_id != "")
        {
            $where['user_id'] = $user_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function module_group_assigned_privilege($user_id = "",$module_id)
    {
        $string = "add_privilege,edit_privilege,delete_privilege,view_privilege,m.is_report";
        $table  = "group_accessibility g";
        $where  = array(
            'g.delete_status' => 0,
            'g.module_id'       => $module_id,
            'u.user_id'       => $user_id,
            'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "users_groups u" => "u.group_id = g.group_id" . "#" . "left",
            "modules m" => "m.module_id = g.module_id"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join
        );
        return $data;
    }
    public function get_user_accessibility_privilege_list_field($id)
    {
        $string = "u.*,m.is_report";
        $table  = "user_accessibility u";
        $where  = array(
            'u.delete_status' => 0,
            'u.accessibility_id' => $id);
        $join = [
            "modules m" => "u.module_id = m.module_id"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join
        );
        return $data;
    }
    public function branch_field_old($firm_id = "")
    {
       
        $string = "f.*,br.*,com.*,con.country_name as branch_country_name,sta.state_name as branch_state_name,sta.state_code as branch_state_code,cit.city_name as branch_city_name";
        $table  = "branch br";
        $where  = array(
            'br.delete_status' => 0);
        $join = [
            "common_settings com" => "com.branch_id = br.branch_id" . "#" . "left",
            "firm f"              => "f.firm_id = br.firm_id",
            "countries con"       => "br.branch_country_id = con.country_id",
            "states sta"          => "br.branch_state_id = sta.state_id" . "#" . "left",
            "cities cit"          => "br.branch_city_id = cit.city_id" . "#" . "left"];
        if ($firm_id != "")
        {
            $where['br.firm_id'] = $firm_id;
            // $where['br.branch_id']=$this->ci->session->userdata('SESS_BRANCH_ID');
        }
        else
        {
            // $join['firm f']="f.firm_id = br.firm_id";
            $where['br.branch_id'] = $this->ci->session->userdata('SESS_BRANCH_ID');
        }
        $order = [
            "br.branch_id" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function branch_field($firm_id = "")
    {
        $string = "f.*,br.*,con.country_name as branch_country_name,sta.state_name as branch_state_name,sta.state_code as branch_state_code,cit.city_name as branch_city_name,sta.state_short_code";
        $table  = "branch br";
        $where  = array(
            'br.delete_status' => 0);
        $join = [
            "common_settings com" => "com.branch_id = br.branch_id" . "#" . "left",
            "firm f"              => "f.firm_id = br.firm_id",
            "countries con"       => "br.branch_country_id = con.country_id",
            "states sta"          => "br.branch_state_id = sta.state_id" . "#" . "left",
            "cities cit"          => "br.branch_city_id = cit.city_id" . "#" . "left"];
        if ($firm_id != "")
        {
            $where['br.firm_id'] = $firm_id;
            // $where['br.branch_id']=$this->ci->session->userdata('SESS_BRANCH_ID');
        }
        else
        {
            // $join['firm f']="f.firm_id = br.firm_id";
            $where['br.branch_id'] = $this->ci->session->userdata('SESS_BRANCH_ID');
        }
        $order = [
            "br.branch_id" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function customer_field()
    {
        $string = "c.*,con.country_name as customer_country_name,sta.state_name as customer_state_name,sta.state_code as customer_state_code,cit.city_name as customer_city_name";
        $table  = "customer c";
        $where  = array(
            'c.delete_status' => 0,
            'c.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "countries con" => "c.customer_country_id = con.country_id" . "#" . "left",
            "states sta"    => "c.customer_state_id = sta.state_id" . "#" . "left",
            "cities cit"    => "c.customer_city_id = cit.city_id" . "#" . "left"];
        $order = [
            "c.customer_name" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function customer_field1()
    {
        $string = "c.*,con.country_name as customer_country_name,sta.state_name as customer_state_name,sta.state_code as customer_state_code,cit.city_name as customer_city_name";
        $table  = "customer c";
        $where  = array(
            'c.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "countries con" => "c.customer_country_id = con.country_id" . "#" . "left",
            "states sta"    => "c.customer_state_id = sta.state_id" . "#" . "left",
            "cities cit"    => "c.customer_city_id = cit.city_id" . "#" . "left"];
        $order = [
            "c.customer_name" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function expense_field($expense_id = "")
    {
        $string = "p.expense_id as expense_id,p.expense_title as product_name,p.expense_gst_id as product_tax_id,p.expense_gst_value as product_tax_value,p.expense_tds_id,p.expense_tds_value,p.ledger_id,p.expense_hsn_code";
        $table = "expense p";
        $join  = [
            'tax td' => 'td.tax_id = p.expense_tds_id' . '#' . 'left'];
        $where = array(
            'p.delete_status' => 0,
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($expense_id != "")
        {
            $where['p.expense_id'] = $expense_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'join'   => $join,
            'where'  => $where
        );
        return $data;
    }

    public function product_field($product_id = ""){
        $string = "p.product_id,p.product_code,p.product_hsn_sac_code,p.product_name,p.product_price,p.product_quantity,p.product_damaged_quantity,p.product_gst_id as product_tax_id,p.product_gst_value as product_tax_value,p.product_tds_id,p.product_tds_value,p.product_details,p.ledger_id,td.tax_name as module_type,p.product_batch,p.product_quantity,p.product_selling_price, p.product_mrp_price, p.product_discount_id,p.margin_discount_value,p.margin_discount_id,p.product_basic_price,p.product_unit,u.uom,p.equal_unit_number,p.equal_uom_id";
        $table = "products p";
        $join  = [
            'tax td' => 'td.tax_id = p.product_tds_id' . '#' . 'left',
            "uqc u"  => "u.id = p.product_unit_id" . '#left']; 
        $where = array(
            'p.delete_status' => 0,
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($product_id != "")
        {
            $where['p.product_id'] = $product_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'join'   => $join,
            'where'  => $where,
            'order'  => ""
        );
        return $data;
    }

    public function sales_saleing_price() {
        $string = "s.sales_sub_total";
        $table = "sales s";
        $where = array(
            's.delete_status' => 0,
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function product_inventory_field($product_inv_id = "")
    {
        /*$string = "pv.product_inventory_varients_id as product_id ,pv.varient_code as product_code,pv.varient_name as product_name,pv.purchase_price as product_price,pv.quantity,p.product_tax_id,p.product_hsn_sac_code,p.product_tax_value,p.product_tds_id,p.product_tds_value,p.product_details,td.module_type";*/
        $string = "product_id,product_code,product_name,product_price,product_quantity as quantity,product_gst_id as product_tax_id,product_hsn_sac_code,product_gst_value as product_tax_value,product_tds_id,product_tds_value,product_details,p.product_tds_id,p.product_tds_value,p.product_gst_id,p.product_gst_value,td.tax_name as module_type";
        $table = " products p";
        $join = [
            'tax td'              => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = array(
            'p.delete_status' => 0,
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($product_inv_id != "")
        {
            $where['p.product_id'] = $product_inv_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'order'  => ""
        );
        return $data;
    }
    public function product_varient_field_old($product_id = "")
    {
        $string = "pv.product_inventory_varients_id,pv.varient_code,pv.varient_name,pv.barcode_symbology,pv.selling_price,pv.varient_unit,c.category_name";
        $table = "product_inventory_varients pv";
        $join  = [
            "product_inventory p" => "p.product_inventory_id = pv.product_inventory_id",
            "category c"          => "c.category_id=p.product_category_id"];
        $where = array(
            'pv.delete_status' => 0,
            'p.delete_status'  => 0,
            'pv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($product_id != "")
        {
            $where['product_inventory_varients_id'] = $product_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'order'  => ""
        );
        return $data;
    }

    public function product_varient_field($product_id = ""){
        $string = 'p.product_name as item_name, p.product_id as item_id, "product" as item_type, "code128" as item_barcode_symbology,  " - " as varient_name,u.uom as varient_unit, c.category_name, p.product_code as item_code, p.product_sku, p.product_selling_price as selling_price, p.product_serail_no, c.category_code ';
        $table = "products p";
        $join  = ["category c"          => "c.category_id=p.product_category_id",
                "uqc u"  => "u.id = p.product_unit_id"
                ];
        $where = array(
            'p.delete_status'  => 0,
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($product_id != "")
        {
            $where['p.product_id'] = $product_id;
        }
        $group=array('p.product_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'order'  => "",
            'group'  => $group
        );
        return $data;
    }

  
    public function supplier_field(){
        $string = "s.*,con.country_name as customer_country_name,sta.state_name as customer_state_name,sta.state_code as customer_state_code,cit.city_name as customer_city_name";
        $table  = "supplier s";
        $where = array(
            's.delete_status' => 0,
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "countries con" => "s.supplier_country_id = con.country_id". '#' . 'left',
            "states sta"    => "s.supplier_state_id = sta.state_id". '#' . 'left',
            "cities cit"    => "s.supplier_city_id = cit.city_id". '#' . 'left'];
        $order = [
            "s.supplier_name" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function supplier_field1()
    {
        $string = "s.*,con.country_name as customer_country_name,sta.state_name as customer_state_name,sta.state_code as customer_state_code,cit.city_name as customer_city_name";
        $table  = "supplier s";
        $where = array(
            's.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "countries con" => "s.supplier_country_id = con.country_id",
            "states sta"    => "s.supplier_state_id = sta.state_id",
            "cities cit"    => "s.supplier_city_id = cit.city_id"];
        $order = [
            "s.supplier_name" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function payee_field()
    {
        $string = "p.payee_id as supplier_id,p.payee_name as supplier_name";
        $table  = "payee p";
        $where = array(
            'p.delete_status' => 0,
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $order = [
            "p.payee_id" => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function discount_field()
    {
        $string = "discount_id,discount_name,discount_value";
        $table  = "discount";
        $where = array(
            'delete_status' => 0,
            'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function brand_field()
    {
        $string = "brand_id,brand_name";
        $table  = "brand";
        $where = array(
            'delete_status' => 0,
            'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }

    public function discount_field1()
    {
        $string = "discount_id,discount_name,discount_value";
        $table  = "discount";
        $where = array(
            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function category_field($type = "")
    {
        $string = "*";
        $table  = "category";
        $where = array(
            'delete_status' => 0,
            'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($type != "")
        {
            $where['category_type'] = $type;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function bank_account_field()
    {
        $string = "b.bank_account_id,l.ledger_id,l.ledger_title";
        $table  = "bank_account b";
        $where  = array(
            'b.delete_status' => 0,
            'b.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "ledgers l" => "l.ledger_id = b.ledger_id"];
        $order = [
            "b.bank_account_id" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
// $string="*";
// $table="bank_account";
// $where=array('delete_status' =>0,'branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'));
// $data = array('string' => $string,
//             'table' => $table,
//             'where' => $where
//             );
        // return  $data;
    }
    public function bank_account_field_new()
    {
        $string = "b.bank_account_id,b.ledger_title";
        $table  = "bank_account b";
        $where  = array(
            'b.ledger_title !=' => '',
            'b.delete_status' => 0,
            'b.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $order = ["b.bank_account_id" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
// $string="*";
// $table="bank_account";
// $where=array('delete_status' =>0,'branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'));
// $data = array('string' => $string,
//             'table' => $table,
//             'where' => $where
//             );
        // return  $data;
    }
    public function uqc_field()
    {
        $string = "*";
        $table  = "uqc";
        $where = array(
            'delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function uqc_field1($type)
    {
        $string = "*";
        $table  = "uqc";
        $where = "(uom_type ='{$type}' OR uom_type = 'both') AND delete_status = 0";
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function bulk_uqc_field($type)
    {
        $string = "id as uom_id,LOWER(uom) as uom";
        $table  = "uqc";
        $where = "(uom_type ='{$type}' OR uom_type = 'both') AND delete_status = 0";
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function hsn_field($hsn_chapter_id = "")
    {
        $string = "*";
        $table  = "hsn";
        if ($hsn_chapter_id > 0)
        {
            $where = array(
                'delete_status' => 0,
                'chapter_id'    => $hsn_chapter_id);
        }
        else
        {
            $where = array(
                'delete_status' => 0);
        }
        $order = [
            "hsn_id" => "desc"];
        $filter = array(
            'itc_hs_codes',
            'description');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'filter' => $filter
        );
        return $data;
    }
    public function tds_field($tds_chapter_id = "")
    {
        $string = "*";
        $table  = "tds";
        if ($tds_chapter_id > 0)
        {
            $where = array(
                'delete_status' => 0,
                'section_id'    => $tds_chapter_id);
        }
        else
        {
            $where = array(
                'delete_status' => 0);
        }
        $order = [
            "tds_id" => "desc"];
        $filter = array(
            'tds_value',
            'description');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'filter' => $filter
        );
        return $data;
    }
    public function sac_field()
    {
        $string = "*";
        $table  = "sac";
        $where = array(
            'delete_status' => 0);
        $order = [
            "sac_id" => "desc"];
        $filter = array(
            'accounting_code',
            'description');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'filter' => $filter
        );
        return $data;
    }
    public function hsn_chapter_field()
    {
        $string = "*";
        $table  = "hsn_chapter";
        $where = array(
            'delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function tds_section_field()
    {
        $string = "*";
        $table  = "tax_section";
        $where = array(
            'delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function tax_section_field(){
        
        $string = "*";
        $table  = "tax_section";
        $where = array(
            'delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function tax_field()
    {
        $string = "*";
        $table  = "tax";
        $where = array(
            'delete_status' => 0,
            'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function tax_field_with_all_type(){
        $string = "t.*,s.section_name";
        $table  = "tax t";
        $join   = [
            "tax_section s" => "t.section_id=s.section_id". '#' . 'left'];
        $where = array(
            't.delete_status' => 0,
            't.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join' => $join
        );
        return $data;
    }
    public function tax_field_with_type($type){
        $string = "t.*,s.section_name";
        $table  = "tax t";
        $join   = [
            "tax_section s" => "t.section_id=s.section_id". '#' . 'left'];
        $where = array(
            't.tax_name' => $type,
            't.delete_status' => 0,
            't.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join' => $join
        );
        return $data;
    }
    public function country_field($country_id = "")
    {
        $string = "*";
        $table  = "countries";
        $where  = array();
        if ($country_id != "")
        {
            $where = array(
                'country_id' => $country_id);
        }
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function financial_year_field($financial_year_id = "")
    {
        $string = "*";
        $table  = "financial_year";
        $where  = array();
        if ($financial_year_id != "")
        {
            $where = array(
                'financial_year_id' => $financial_year_id);
        }
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function state_field($country_id = "", $state_id = "")
    {
        $string = "*";
        $table  = "states";
        $where  = array();
        if ($country_id != "")
        {
            $where = array(
                'country_id' => $country_id);
        }
        if ($state_id != "")
        {
            $where['state_id'] = $state_id;
        }
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function state_code_field($state_id = "")
    {
        $string = "state_code";
        $table  = "states";
        $where  = array();
        if ($state_id != "")
        {
            $where['state_id'] = $state_id;
        }
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function city_field($state_id = "", $city_id = "")
    {
        $string = "*";
        $table  = "cities";
        $where  = array();
        if ($state_id != "")
        {
            $where['state_id'] = $state_id;
        }
        if ($city_id != "")
        {
            $where['city_id'] = $city_id;
        }
        $where['delete_status'] = 0;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function sales_field()
    {
    }
    //for subcatogory values
    public function settings_field($module_id, $branch_id = "")
    {
        $string = "s.*";
        $table  = "settings s";
        $where  = array(
            's.delete_status' => 0,
            's.module_id'     => $module_id
        );
        if ($branch_id != "")
        {
            $where['s.branch_id'] = $branch_id;
        }
        else
        {
            $where['s.branch_id'] = $this->ci->session->userdata('SESS_BRANCH_ID');
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function expences_suggestions_field($term){
        $sql = 'SELECT * FROM ((SELECT  expense_id as item_id,expense_title as item_code,branch_id,delete_status,expense_title as item_name FROM  expense where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and expense_title like "%' . $term . '%" order by expense_id desc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
        return $sql;
    }

    public function item_suggestions_field($item_access, $term , $brand_id = '')
    {
        $brand_where = '';
        if($brand_id != '' && $brand_id != 0) $brand_where = ' and brand_id ='.$brand_id; 

        $pro_where = '';
        $ser_where = '';
        if($term != ''){
            $pro_where = ' AND (product_code like "%' . $term . '%" or product_name like "%' . $term . '%" or product_barcode like "%' . $term . '%") ';
            $ser_where = ' and (service_code like "%' . $term . '%" or service_name like "%' . $term . '%") ';
        }
        if ($item_access == "both")
        {

            $sql = 'SELECT * FROM ((SELECT product_id as item_id,product_code as item_code, product_hsn_sac_code as hsn_sac_code, product_name as item_name,"product" as item_type, delete_status, branch_id, product_batch,product_quantity,product_opening_quantity FROM products where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and  is_varients="N" '.$brand_where.' '.$pro_where.' order by product_id asc)' .
            'UNION ALL' .
            '(SELECT service_id as item_id,service_code as item_code,service_hsn_sac_code as hsn_sac_code,service_name as item_name,"service" as item_type,delete_status,branch_id, "" as product_batch, 0 as product_quantity,0 as product_opening_quantity FROM services where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' '.$ser_where.' order by service_id desc)
                ) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
               
            // $data=$this->db->query($sql)->result();
        }
        elseif ($item_access == "product")
        {
            $sql = 'SELECT * FROM ((SELECT product_id as item_id,product_code as item_code,product_hsn_sac_code as hsn_sac_code,product_name as item_name,"product" as item_type,delete_status,branch_id,product_batch,product_quantity,product_opening_quantity FROM products where delete_status=0 and is_varients="N" '.$brand_where.' AND branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' '.$pro_where.' order by product_id asc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
            // $data=$this->db->query($sql)->result();
        }
        else
        {
            $sql = 'SELECT * FROM ((SELECT service_id as item_id,service_code as item_code,service_hsn_sac_code as hsn_sac_code,service_name as item_name,"service" as item_type,delete_status,branch_id, "" as product_batch, 0 as product_quantity,0 as product_opening_quantity FROM services where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' '.$ser_where.' order by service_id desc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID');
            /*echo $sql;*/
            // $data=$this->db->query($sql)->result();
        }
        return $sql;
    }

    public function item_suggestions_field_leathercrafr($item_access, $term , $brand_id = '')
    {
        $brand_where = '';
        if($brand_id != '' && $brand_id != 0) $brand_where = ' and brand_id ='.$brand_id; 

        $pro_where = '';
        $ser_where = '';
        if($term != ''){
            $pro_where = ' AND (product_code like "%' . $term . '%" or product_name like "%' . $term . '%") ';
            $ser_where = ' and (service_code like "%' . $term . '%" or service_name like "%' . $term . '%") ';
        }
        if ($item_access == "both")
        {

            $sql = 'SELECT * FROM ((SELECT product_id as item_id,product_code as item_code, product_hsn_sac_code as hsn_sac_code, product_name as item_name,"product" as item_type, delete_status, branch_id, product_batch,product_quantity,product_opening_quantity FROM products where delete_status=0 and product_combination_id IS NOT NULL AND branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and batch_parent_product_id = 0  AND is_varients="N" '.$brand_where.' '.$pro_where.' order by product_id desc)' .
            'UNION ALL' .
            '(SELECT service_id as item_id,service_code as item_code,service_hsn_sac_code as hsn_sac_code,service_name as item_name,"service" as item_type,delete_status,branch_id, "" as product_batch, 0 as product_quantity,0 as product_opening_quantity FROM services where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' '.$ser_where.' order by service_id desc)
                ) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
               
            // $data=$this->db->query($sql)->result();
        }
        elseif ($item_access == "product")
        {
            $sql = 'SELECT * FROM ((SELECT product_id as item_id,product_code as item_code,product_hsn_sac_code as hsn_sac_code,product_name as item_name,"product" as item_type,delete_status,branch_id,product_batch,product_quantity,product_opening_quantity FROM products where delete_status=0 and product_combination_id IS NOT NULL AND batch_parent_product_id = 0 AND is_varients="N" '.$brand_where.' AND branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' '.$pro_where.' order by product_id asc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
            // $data=$this->db->query($sql)->result();
        }
        else
        {
            $sql = 'SELECT * FROM ((SELECT service_id as item_id,service_code as item_code,service_hsn_sac_code as hsn_sac_code,service_name as item_name,"service" as item_type,delete_status,branch_id, "" as product_batch, 0 as product_quantity,0 as product_opening_quantity FROM services where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' '.$ser_where.' order by service_id desc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID');
            /*echo $sql;*/
            // $data=$this->db->query($sql)->result();
        }
        return $sql;
    }

    public function all_products_field(){
        
        $string = "*";
        $table  = "products";
        $where = array(
            'delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function all_products_stage_field(){
        
        $string = "*";
        $table  = "products";
        $where = array(
            'delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function item_inventory_suggestions_field($item_access, $term)
    {
        if ($item_access == "both")
        {
            $sql = 'SELECT * FROM ((SELECT pv.product_inventory_varients_id  as item_id,pv.varient_code as item_code,p.product_hsn_sac_code as hsn_sac_code,pv.varient_name as item_name,"product_inventory" as item_type,pv.delete_status,pv.branch_id,pv.barcode_number FROM product_inventory_varients pv left join product_inventory p on p.product_inventory_id=pv.product_inventory_id where pv.delete_status=0 and pv.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and pv.varient_code like "%' . $term . '%" or pv.varient_name like "%' . $term . '%" or pv.barcode_number like "%' . $term . '%" order by pv.product_inventory_varients_id desc)' .
            'UNION ALL'
            . '(SELECT service_id as item_id,service_code as item_code,service_hsn_sac_code as hsn_sac_code,service_name as item_name,"service" as item_type,delete_status,branch_id,"" as barcode_number FROM services where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and service_code like "%' . $term . '%" or service_name like "%' . $term . '%" order by service_id desc)
                ) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
        }
        elseif ($item_access == "product")
        {
            $sql = 'SELECT * FROM ((SELECT pv.product_inventory_varients_id as item_id,pv.varient_code as item_code,p.product_hsn_sac_code as hsn_sac_code,pv.varient_name as item_name,"product_inventory" as item_type,pv.delete_status,pv.branch_id FROM product_inventory_varients pv left join product_inventory p on p.product_inventory_id=pv.product_inventory_id where pv.delete_status=0 and pv.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and pv.varient_code like "%' . $term . '%" or pv.varient_name like "%' . $term . '%" or pv.barcode_number like "%' . $term . '%" order by pv.product_inventory_varients_id desc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
        }
        else
        {
            $sql = 'SELECT * FROM ((SELECT service_id as item_id,service_code as item_code,service_hsn_sac_code as hsn_sac_code,service_name as item_name,"service" as item_type,delete_status,branch_id FROM services where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and service_code like "%' . $term . '%" or service_name like "%' . $term . '%" order by service_id desc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
        }
        return $sql;
    }
    public function product_suggestions_field_old($item_access, $term)
    {
        if ($item_access[0]->item_access == "product" || $item_access[0]->item_access == "both")
        {
            $sql = 'SELECT * FROM ((SELECT product_inventory_varients_id as item_id,varient_code as item_code,varient_name as item_name,"product" as item_type,barcode_symbology,delete_status,branch_id FROM product_inventory_varients where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and varient_code like "%' . $term . '%" or varient_name like "%' . $term . '%" order by product_inventory_varients_id desc)) AS u where u.delete_status=0 && u.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
            // $data=$this->db->query($sql)->result();
            return $sql;
        }
        else
        {
            return false;
        }
    }

     public function product_suggestions_field($item_access, $term){
        
            $sql = 'SELECT product_name as item_name,product_id as item_id,"product" as item_type,"code128" as item_barcode_symbology, product_code as item_code FROM products where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and product_name like "%' . $term . '%" order by product_name desc';
            // $data=$this->db->query($sql)->result();
            return $sql;
       
    }
    //for Service values
    public function service_field($service_id = "")
    {
        $string = "s.*,td.tax_name,'' as product_batch";
        $table  = "services s";
        $join   = [
            'tax td' => 'td.tax_id = s.service_tds_id' . '#' . 'left'];
        $where = array(
            's.delete_status' => 0,
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        if ($service_id != "")
        {
            $where['service_id'] = $service_id;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'join'   => $join,
            'where'  => $where
        );
        return $data;
    }
    public function sales_item_field($sales_id)
    {
        $string = 'si.*';
        $table  = 'sales_item si';
        $where  = array(
            'si.sales_id'      => $sales_id,
            'si.delete_status' => 0);
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function boe_purchase_invoices_field($boe_id){
        $string = "b.*,pr.purchase_invoice_number";
        $table  = "boe_purchase_tbl b";
        $join   = [
            'purchase pr' => 'b.purchase_id = pr.purchase_id'
        ];
        $where = [
            'b.boe_id'   => $boe_id,
            'b.delete_status' => '0'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function boe_items_product_list_field($boe_id)
    {
        $string = "b.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,U.uom as product_unit,pr.product_tax_id,pr.product_tds_id,pr.product_batch,pr.product_tax_value";
        $table  = "boe_items b";
        $join   = [
            'products pr' => 'b.item_id = pr.product_id',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'b.boe_id'   => $boe_id,
            'b.delete_status' => '0',
            'b.item_type'     => 'product'
            ];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function expense_bill_items_product_list_field($exp_id){
        $string = "pi.*,pr.expense_id as item_id,pr.expense_title as product_code,pr.expense_title as product_name,dt.discount_value,pr.expense_gst_id as product_tax_id,pr.expense_tds_id as product_tds_id,pr.expense_gst_value as product_tax_value,td.tax_name as tds_module_type,pr.expense_hsn_code";
        $table  = "expense_bill_item pi";
        $join   = [
            'expense pr' => 'pi.expense_type_id = pr.expense_id',
            'discount dt' => 'dt.discount_id = pi.expense_bill_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pi.expense_bill_item_tds_id' . '#' . 'left'
        ];
        $where = [
            'pi.expense_bill_id'   => $exp_id,
            'pi.delete_status' => 0];
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_items_product_list_field($purchase_id, $return_quantity = "")
    {
        $string = "pi.*,p.currency_id,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value,U.uom as product_unit,pr.product_tax_id,pr.product_batch,pr.product_tds_id,pr.product_tax_value,td.tax_name as tds_module_type";
        $table  = "purchase_item pi";
        $join   = [            
            'products pr' => 'pi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = pi.purchase_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'purchase p' => 'pi.purchase_id = p.purchase_id',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_id'   => $purchase_id,
            'pi.delete_status' => 0,
            'pi.item_type'     => 'product'];
        if ($return_quantity == "0")
        {
            $where['(pi.purchase_item_quantity-pi.debit_note_quantity) >'] = 0;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_items_product_inventory_list_field($purchase_id, $return_quantity = "")
    {
        $string = "pi.*,pu.currency_id,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value ,pr.varient_unit as product_unit,p.product_tax_id,p.product_tds_id,p.product_tax_value,td.tax_name as tds_module_type";
        $table  = "purchase_item pi";
        $join   = [            
            'product_inventory_varients pr' => 'pi.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = pi.purchase_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left',
            'purchase pu' => 'pi.purchase_id = pu.purchase_id'
        ];
        $where = [
            'pi.purchase_id'   => $purchase_id,
            'pi.delete_status' => 0,
            'pi.item_type'     => 'product_inventory'];
        if ($return_quantity == "0")
        {
            $where['(pi.purchase_item_quantity-pi.debit_note_quantity) >'] = 0;
        }
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function boe_items_service_list_field($boe_id)
    {
        $string = "b.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,sr.service_tax_value,sr.service_tax_id,sr.service_tds_id,U.uom as product_unit";

        $table  = "boe_items b";
        $join   = [
            'services sr' => 'b.item_id = sr.service_id',
            'uqc U'      => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'b.boe_id'   => $boe_id,
            'b.delete_status' => '0',
            'b.item_type'     => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_items_service_list_field($purchase_id)
    {
        $string = "pi.*,p.currency_id,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value,sr.service_tax_value,sr.service_tax_id,sr.service_tds_id,td.tax_name as tds_module_type,U.uom as product_unit";

        $table  = "purchase_item pi";
        $join   = [
            'services sr' => 'pi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = pi.purchase_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',            
            'purchase p' => 'pi.purchase_id = p.purchase_id',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_id'   => $purchase_id,
            'pi.delete_status' => 0,
            'pi.item_type'     => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_return_items_product_list_field($purchase_return_id)
    {
        $string = "pri.*,pi.purchase_item_quantity, pi.debit_note_quantity, pr.product_name, pr.product_code, pr.product_hsn_sac_code,U.uom as product_unit";
        $table  = "purchase_return_item pri";
        $join   = [
            'products pr'      => 'pri.item_id = pr.product_id',
            'purchase_item pi' => 'pi.purchase_item_id = pri.purchase_item_id',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'];
        $where = [
            'pri.purchase_return_id' => $purchase_return_id,
            'pri.delete_status'      => 0,
            'pri.item_type'          => 'product'
            ];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_return_items_product_inventory_list_field($purchase_return_id)
    {
        $string = "pri.*,pi.purchase_item_quantity, pi.debit_note_quantity, pv.varient_name as product_name, pv.varient_code as product_code, p.product_hsn_sac_code,pv.varient_unit as product_unit";
        $table  = "purchase_return_item pri";
        $join   = [
            'product_inventory_varients pv' => 'pri.item_id = pv.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pv.product_inventory_id',
            'purchase_item pi'              => 'pi.purchase_item_id = pri.purchase_item_id'];
        $where = [
            'pri.purchase_return_id' => $purchase_return_id,
            'pri.delete_status'      => 0,
            'pri.item_type'          => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_return_items_service_list_field($purchase_return_id)
    {
        $string = "pri.*,sr.service_name,sr.service_code,sr.service_hsn_sac_code,U.uom as product_unit";
        $table  = "purchase_return_item pri";
        $join   = [
            'services sr' => 'pri.item_id = sr.service_id',
        'uqc U'      => 'U.id = sr.service_unit' . '#' . 'left'];
        $where = [
            'pri.purchase_return_id' => $purchase_return_id,
            'pri.delete_status'      => 0,
            'item_type'              => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_order_items_product_list_field($purchase_order_id)
    {
        $string = "pi.*,p.currency_id,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value,U.uom as product_unit,pr.product_tax_id,pr.product_batch,pr.product_tds_id,pr.product_tax_value,td.tax_name as tds_module_type";
        $table  = "purchase_order_item pi";
        $join   = [            
            'products pr' => 'pi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = pi.purchase_order_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'purchase_order p' => 'pi.purchase_order_id = p.purchase_order_id',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_order_id'   => $purchase_order_id,
            'pi.delete_status' => 0,
            'pi.item_type'     => 'product'];
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_order_items_product_list_field1($purchase_order_id)
    {
        $string = "poi.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value as item_discount_percentage as item_discount_percentage, U.uom as product_unit,pr.product_tax_id as item_tax_id,pr.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type";
        $table  = "purchase_order_item poi";
        $join   = [
            'products pr' => 'poi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = poi.purchase_order_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'poi.purchase_order_id' => $purchase_order_id,
            'poi.delete_status'     => 0,
            'poi.item_type'         => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_order_items_product_inventory_list_field($purchase_order_id)
    {
        $string = "poi.*,pv.product_inventory_varients_id as product_id,pv.varient_code as product_code,pv.varient_name as product_name, p.product_hsn_sac_code,pv.purchase_price as product_price,dt.discount_value as item_discount_percentage,pv.varient_unit as product_unit,p.product_tax_id as item_tax_id,p.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type";
        $table  = "purchase_order_item poi";
        $join   = [
            'product_inventory_varients pv' => 'poi.item_id = pv.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pv.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = poi.purchase_order_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'poi.purchase_order_id' => $purchase_order_id,
            'poi.delete_status'     => 0,
            'poi.item_type'         => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_order_items_service_list_field($purchase_order_id)
    {
        $string = "poi.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value as item_discount_percentage,sr.service_tax_value as item_tax_percentage,sr.service_tax_id as item_tax_id,td.tax_name as tds_module_type,U.uom as product_unit";

        $table  = "purchase_order_item poi";
        $join   = [
            'services sr' => 'poi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = poi.purchase_order_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'poi.purchase_order_id' => $purchase_order_id,
            'poi.delete_status'     => 0,
            'poi.item_type'         => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_items_product_list_field($sales_id)
    {
        $string = "si.*,pr.product_id,pr.product_code,pr.product_name,pr.packing,pr.mfg_date,pr.exp_date,b.brand_name,pr.product_image, pr.product_hsn_sac_code,pr.product_price,dt.discount_value as item_discount_percentage,pr.product_tax_id as item_tax_id,pr.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type,pr.product_batch,U.uom as product_unit, US.uom as product_unit_sales";
        $table  = "sales_item si";
        $join   = [
            'products pr' => 'si.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = si.sales_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = si.sales_item_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left',
            'uqc US'      => 'US.id = si.sales_item_uom_id' . '#' . 'left',
            'brand b'      => 'b.brand_id = pr.brand_id' . '#' . 'left',
        ];
        $where = [
            'si.sales_id'      => $sales_id,
            'si.delete_status' => 0,
            'si.item_type'     => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_items_product_inventory_list_field($sales_id)
    {
        $string = "si.*,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value as item_discount_percentage,pr.varient_unit as product_unit,p.product_tax_id as item_tax_id,p.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type";
        $table  = "sales_item si";
        $join   = [
            'product_inventory_varients pr' => 'si.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = si.sales_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'si.sales_id'      => $sales_id,
            'si.delete_status' => 0,
            'si.item_type'     => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function delivery_challan_items_product_list_field($delivery_challan_id)
    {
        $string = "pi.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value as item_discount_percentage,U.uom as product_unit";
        $table  = "delivery_challan_item di";
        $join   = [
            'products pr' => 'pi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = pi.delivery_challan_item_discount_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'pi.delivery_challan_id' => $delivery_challan_id,
            'pi.delete_status'       => 0,
            'pi.item_type'           => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function delivery_challan_items_product_inventory_list_field($delivery_challan_id)
    {
        $string = "pi.*,pv.product_inventory_varients_id as product_id,pv.varient_code as product_code,pv.varient_name as product_name, p.product_hsn_sac_code,pv.selling_price as product_price,dt.discount_value as item_discount_percentage,pv.varient_unit as product_unit";
        $table  = "delivery_challan_item pi";
        $join   = [
            'product_inventory_varients pv' => 'pi.item_id = pv.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pv.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = pi.delivery_challan_item_discount_id' . '#' . 'left'];
        $where = [
            'pi.delivery_challan_id' => $delivery_challan_id,
            'pi.delete_status'       => 0,
            'pi.item_type'           => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function pos_billing_items_product_list_field($pos_billing_id)
    {
        $string = "di.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value as item_discount_percentage,pr.product_unit";
        $table  = "pos_billing_item di";
        $join   = [
            'products pr' => 'di.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = di.pos_billing_item_discount_id' . '#' . 'left'];
        $where = [
            'di.pos_billing_id' => $pos_billing_id,
            'di.delete_status'  => 0,
            'di.item_type'      => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function pos_billing_items_product_inventory_list_field($pos_billing_id)
    {
        $string = "di.*,pv.product_inventory_varients_id as product_id,pv.varient_code as product_code,pv.varient_name as product_name, p.product_hsn_sac_code,pv.selling_price as product_price,dt.discount_value as item_discount_percentage,pv.varient_unit as product_unit";
        $table  = "pos_billing_item di";
        $join   = [
            'product_inventory_varients pv' => 'di.item_id = pv.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pv.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = di.pos_billing_item_discount_id' . '#' . 'left'];
        $where = [
            'di.pos_billing_id' => $pos_billing_id,
            'di.delete_status'  => 0,
            'di.item_type'      => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function advance_voucher_items_product_list_field($advance_voucher_id)
    {
        $string = "ai.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,U.uom as product_unit,pr.product_gst_id,pr.product_tds_id,pr.product_tax_id,td.tax_name as tds_module_type,pr.product_batch";
        $table  = "advance_voucher_item ai";
        $join   = [
            'products pr' => 'ai.item_id = pr.product_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = ai.item_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'];
        $where = [
            'ai.advance_voucher_id' => $advance_voucher_id,
            'ai.delete_status'      => 0,
            'ai.item_type'          => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function advance_voucher_items_product_advance_list_field($advance_voucher_id)
    {
        $string = "ai.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,U.uom as product_unit,pr.product_gst_id,pr.product_tds_id,pr.product_tax_id,td.tax_name as tds_module_type";
        $table  = "advance_voucher_item ai";
        $join   = [
            'advance_products pr' => 'ai.item_id = pr.product_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = ai.item_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'];
        $where = [
            'ai.advance_voucher_id' => $advance_voucher_id,
            'ai.delete_status'      => 0,
            'ai.item_type'          => 'advance'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function advance_voucher_items_product_inventory_list_field($advance_voucher_id)
    {
        $string = "ai.*,pv.product_inventory_varients_id as product_id,pv.varient_code as product_code,pv.varient_name as product_name, p.product_hsn_sac_code,pv.varient_unit as product_unit,p.product_cgst,p.product_sgst,p.product_igst";
        $table  = "advance_voucher_item ai";
        $join   = [
            'product_inventory_varients pv' => 'ai.item_id = pv.product_inventory_varients_id' . '#' . 'left',
            'product_inventory p'           => 'p.product_inventory_id = pv.product_inventory_id'];
        $where = [
            'ai.advance_voucher_id' => $advance_voucher_id,
            'ai.delete_status'      => 0,
            'ai.item_type'          => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function refund_voucher_items_product_list_field($refund_id)
    {
        $string = "ri.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_batch";
        $table  = "refund_voucher_item ri";
        $join   = [
            'products pr' => 'ri.item_id = pr.product_id' . '#' . 'left'];
        $where = [
            'ri.refund_voucher_id' => $refund_id,
            'ri.delete_status'     => 0,
            'ri.item_type'         => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function refund_voucher_items_product_inventory_list_field($refund_id)
    {
        $string = "ri.*,pv.product_inventory_varients_id as product_id,pv.varient_code as product_code,pv.varient_name as product_name, p.product_hsn_sac_code";
        $table  = "refund_voucher_item ri";
        $join   = [
            'product_inventory_varients pv' => 'ri.item_id = pv.product_inventory_varients_id' . '#' . 'left',
            'product_inventory p'           => 'p.product_inventory_id = pv.product_inventory_id'];
        $where = [
            'ri.refund_voucher_id' => $refund_id,
            'ri.delete_status'     => 0,
            'ri.item_type'         => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function performa_items_product_list_field($performa_id)
    {
        $string = "qi.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value as item_discount_percentage,U.uom as product_unit,pr.product_tax_id as item_tax_id,pr.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type,pr.product_batch";
        $table  = "performa_item qi";
        $join   = [
            'products pr' => 'qi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = qi.performa_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'qi.performa_id'  => $performa_id,
            'qi.delete_status' => 0,
            'qi.item_type'     => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function performa_items_product_inventory_list_field($performa_id)
    {
        $string = "qi.*,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value as item_discount_percentage,pr.varient_unit as product_unit,p.product_tax_id as item_tax_id,p.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type,pr.product_batch";
        $table  = "performa_item qi";
        $join   = [
            'product_inventory_varients pr' => 'qi.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = qi.performa_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'qi.performa_id'  => $performa_id,
            'qi.delete_status' => 0,
            'qi.item_type'     => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function quotation_items_product_list_field($quotation_id)
    {
        $string = "qi.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value as item_discount_percentage,U.uom as product_unit,pr.product_tax_id as item_tax_id,pr.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type,pr.product_batch";
        $table  = "quotation_item qi";
        $join   = [
            'products pr' => 'qi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = qi.quotation_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'qi.quotation_id'  => $quotation_id,
            'qi.delete_status' => 0,
            'qi.item_type'     => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function quotation_items_product_inventory_list_field($quotation_id)
    {
        $string = "qi.*,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value as item_discount_percentage,pr.varient_unit as product_unit,p.product_tax_id as item_tax_id,p.product_tax_value as item_tax_percentage,td.tax_name as tds_module_type,pr.product_batch";
        $table  = "quotation_item qi";
        $join   = [
            'product_inventory_varients pr' => 'qi.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = qi.quotation_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'qi.quotation_id'  => $quotation_id,
            'qi.delete_status' => 0,
            'qi.item_type'     => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function quotation_list_field1($quotation_id)
    {
        $string = "q.*,st1.state_name as place_of_supply,
                   cur.*,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   sa.department,
                   sa.contact_person,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number
                   ";
        $table = "quotation q";
        $join  = [
            'currency cur'        => 'cur.currency_id = q.currency_id',
            'customer c'          => 'q.quotation_party_id = c.customer_id',
            'states st1'          => 'q.quotation_billing_state_id = st1.state_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = q.shipping_address_id' . '#' . 'left'];
        $where['q.quotation_id'] = $quotation_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function performa_list_field1($performa_id)
    {
        $string = "q.*,st1.state_name as place_of_supply,
                   cur.*,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   c.customer_name,
                   c.due_days,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number
                   ";
        $table = "performa q";
        $join  = [
            'currency cur'        => 'cur.currency_id = q.currency_id',
            'customer c'          => 'q.performa_party_id = c.customer_id',
            'states st1'          => 'q.performa_billing_state_id = st1.state_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = q.shipping_address_id' . '#' . 'left'];
        $where['q.performa_id'] = $performa_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_list_field1($sales_id)
    {
        $string = "s.*,st1.state_name as place_of_supply,
                   co.country_name as billing_country,
                   cur.*,
                   sa.shipping_address  as shipping_address,
                   sa.contact_person,
                   sa.shipping_gstin,
                   sa.department,
                   c.customer_name,
                   c.due_days,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number,
                   c.drug_licence_no,
                   c.food_licence_number,
                   st2.state_code as customer_state_code,
                   st1.state_name as customer_state_name,
                   st1.state_short_code,
                   ct.city_name as customer_city
                   ";
        $table = "sales s";
        $join  = [
            'currency cur'        => 's.currency_id = cur.currency_id',
            'customer c'          => 's.sales_party_id = c.customer_id',
            'cities ct'    => 'c.customer_city_id = ct.city_id' . '#' . 'left',
            'states st1'          => 's.sales_billing_state_id = st1.state_id' . '#' . 'left',
            'countries co'        => 's.sales_billing_country_id = co.country_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = s.shipping_address_id' . '#' . 'left',
            'states st2'          => 'c.customer_state_id = st2.state_id' . '#' . 'left',
        ];
        $where['s.sales_id'] = $sales_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function delivery_challan_list_field1($delivery_challan_id)
    {
        $string = "d.*,st1.state_name as place_of_supply,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number,
                   c.customer_state_code,
                   ct.city_name as customer_city_name,
                   cs.state_name as customer_state_name,
                   cu.country_name as customer_country_name";
        $table = "delivery_challan d";
        $join  = [
            'customer c'   => 'd.delivery_challan_party_id = c.customer_id',
            'cities ct'    => 'c.customer_city_id = ct.city_id' . '#' . 'left',
            'states cs'    => 'c.customer_state_id = cs.state_id' . '#' . 'left',
            'countries cu' => 'c.customer_country_id = cu.country_id' . '#' . 'left',
            'states st1'   => 'd.delivery_challan_billing_state_id = st1.state_id' . '#' . 'left'];
            //'currency cur' => 'cur.currency_id = d.currency_id',
        $where['d.delivery_challan_id'] = $delivery_challan_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function refund_list_field1($refund_id)
    {
        $string = "rv.*,st1.state_name as place_of_supply,
                   cur.*,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number,
                   c.customer_state_code,
                   ct.city_name as customer_city_name,
                   cs.state_name as customer_state_name,
                   cu.country_name as customer_country_name";
        $table = "refund_voucher rv";
        $join  = [
            'currency cur' => 'cur.currency_id = rv.currency_id',
            'customer c'   => 'rv.party_id = c.customer_id',
            'cities ct'    => 'c.customer_city_id = ct.city_id',
            'states cs'    => 'c.customer_state_id = cs.state_id',
            'countries cu' => 'c.customer_country_id = cu.country_id',
            'states st1'   => 'rv.billing_state_id = st1.state_id' . '#' . 'left'];
        $where['rv.refund_id'] = $refund_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function receipt_voucher_list_field1($receipt_id)
    {
        // cur.*,
        $string = "rv.*,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number,
                   c.customer_state_code,
                   ct.city_name as customer_city_name,
                   cs.state_name as customer_state_name,
                   cu.country_name as customer_country_name";
        $table = "receipt_voucher rv";
        $join  = [
            'customer c'   => 'rv.party_id = c.customer_id',
            'cities ct'    => 'c.customer_city_id = ct.city_id',
            'states cs'    => 'c.customer_state_id = cs.state_id',
            'countries cu' => 'c.customer_country_id = cu.country_id'];//'currency cur' => 'cur.currency_id = rv.currency_id',
        $where['rv.receipt_id'] = $receipt_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function payment_voucher_list_field1($payment_id)
    {
        $string = "pv.*,
                   
                   s.supplier_name,
                   s.supplier_address,
                   s.supplier_mobile,
                   s.supplier_email,
                   s.supplier_postal_code,
                   s.supplier_gstin_number,
                   s.supplier_state_id,
                   s.supplier_tan_number,
                   s.supplier_pan_number,
                   s.supplier_state_code,
                   ct.city_name as supplier_city_name,
                   cs.state_name as supplier_state_name,
                   cu.country_name as supplier_country_name";//cur.*,
        $table = "payment_voucher pv";
        $join  = [
            'supplier s'   => 'pv.party_id = s.supplier_id',
            'cities ct'    => 's.supplier_city_id = ct.city_id',
            'states cs'    => 's.supplier_state_id = cs.state_id',
            'countries cu' => 's.supplier_country_id = cu.country_id'];//'currency cur' => 'cur.currency_id = pv.currency_id',
        $where['pv.payment_id'] = $payment_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function advance_voucher_list_field1($advance_voucher_id)
    {
        $string = "av.*,st1.state_name as place_of_supply,
                   cur.*,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number,
                   c.customer_state_code,
                   ct.city_name as customer_city_name,
                   cs.state_name as customer_state_name,
                   cu.country_name as customer_country_name";
        $table = "advance_voucher av";
        $join  = [
            'currency cur' => 'cur.currency_id = av.currency_id' . '#' . 'left',
            'customer c'   => 'av.party_id = c.customer_id' . '#' . 'left',
            'cities ct'    => 'c.customer_city_id = ct.city_id' . '#' . 'left',
            'states cs'    => 'c.customer_state_id = cs.state_id' . '#' . 'left',
            'countries cu' => 'c.customer_country_id = cu.country_id' . '#' . 'left',
            'states st1'   => 'av.billing_state_id = st1.state_id' . '#' . 'left'];
        $where['av.advance_voucher_id'] = $advance_voucher_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function refund_voucher_list_field1($refund_id)
    {
        $string = "rv.*,st1.state_name as place_of_supply,
                   cur.*,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number,
                   c.customer_state_code,
                   ct.city_name as customer_city_name,
                   cs.state_name as customer_state_name,
                   cu.country_name as customer_country_name";
        $table = "refund_voucher rv";
        $join  = [
            'currency cur' => 'cur.currency_id = rv.currency_id',
            'customer c'   => 'rv.party_id = c.customer_id',
            'cities ct'    => 'c.customer_city_id = ct.city_id',
            'states cs'    => 'c.customer_state_id = cs.state_id',
            'countries cu' => 'c.customer_country_id = cu.country_id',
            'states st1'   => 'rv.billing_state_id = st1.state_id' . '#' . 'left'];
        $where['rv.refund_id'] = $refund_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_items_service_list_field($sales_id)
    {
        $string = "si.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value as item_discount_percentage,sr.service_tax_value as item_tax_percentage,sr.service_tax_id as item_tax_id,td.tax_name as tds_module_type,U.uom as product_unit, US.uom as product_unit_sales";
        $table  = "sales_item si";
        $join   = [
            'services sr' => 'si.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = si.sales_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = sr.service_unit' . '#' . 'left',
            'uqc US'      => 'US.id = si.sales_item_uom_id' . '#' . 'left'
        ];
        $where = [
            'si.sales_id'      => $sales_id,
            'si.delete_status' => 0,
            'si.item_type'     => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function delivery_challan_items_service_list_field($delivery_challan_id)
    {
        $string = "di.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value as item_discount_percentage,U.uom as product_unit";
        

        $table  = "delivery_challan_item di";
        $join   = [
            'services sr' => 'di.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = di.delivery_challan_item_discount_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'];
        $where = [
            'di.delivery_challan_id' => $delivery_challan_id,
            'di.delete_status'       => 0,
            'di.item_type'           => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function pos_billing_items_service_list_field($pos_billing_id)
    {
        $string = "pi.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value as item_discount_percentage";
        $table  = "pos_billing_item pi";
        $join   = [
            'services sr' => 'pi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = pi.pos_billing_item_discount_id' . '#' . 'left'];
        $where = [
            'pi.pos_billing_id' => $pos_billing_id,
            'pi.delete_status'  => 0,
            'pi.item_type'      => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function advance_voucher_items_service_list_field($advance_voucher_id)
    {
        $string = "ai.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_gst_id,sr.service_tds_id,sr.service_tax_id,U.uom as product_unit";
        

        $table  = "advance_voucher_item ai";
        $join   = [
            'services sr' => 'ai.item_id = sr.service_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'];
        $where = [
            'ai.advance_voucher_id' => $advance_voucher_id,
            'ai.delete_status'      => 0,
            'ai.item_type'          => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function refund_voucher_items_product_advance_list_field($refund_id){
         $string = "ri.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code";
        $table  = "refund_voucher_item ri";
        $join   = [
            'advance_products pr' => 'ri.item_id = pr.product_id' . '#' . 'left'];
        $where = [
            'ri.refund_voucher_id' => $refund_id,
            'ri.delete_status'     => 0,
            'ri.item_type'         => 'advance'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function refund_voucher_items_service_list_field($refund_id)
    {
        $string = "ri.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code";
        $table  = "refund_voucher_item ri";
        $join   = [
            'services sr' => 'ri.item_id = sr.service_id' . '#' . 'left'];
        $where = [
            'ri.refund_voucher_id' => $refund_id,
            'ri.delete_status'     => 0,
            'ri.item_type'         => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function quotation_items_service_list_field($quotation_id)
    {
        $string = "qi.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value as item_discount_percentage,sr.service_tax_value as item_tax_percentage,sr.service_tax_id as item_tax_id,td.tax_name as tds_module_type,U.uom as product_unit";
        

        $table  = "quotation_item qi";
        $join   = [
            'services sr' => 'qi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = qi.quotation_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'qi.quotation_id'  => $quotation_id,
            'qi.delete_status' => 0,
            'qi.item_type'     => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function performa_items_service_list_field($performa_id)
    {
        $string = "qi.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value as item_discount_percentage,sr.service_tax_value as item_tax_percentage,sr.service_tax_id as item_tax_id,td.tax_name as tds_module_type,U.uom as product_unit";
        

        $table  = "performa_item qi";
        $join   = [
            'services sr' => 'qi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = qi.performa_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'qi.performa_id'  => $performa_id,
            'qi.delete_status' => 0,
            'qi.item_type'     => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function note_template_list_field()
    {
        $string = 'n.*,u.first_name,u.last_name';
        $table  = 'note_template n';
        $join   = [
            "users u" => "n.added_user_id=u.id"];
        $order = [
            "note_template_id" => "desc"];
        $where = array(
            'n.delete_status' => 0,
            'n.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = array(
            'n.hash_tag',
            'n.title',
            'n.content');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function email_setup_list_field()
    {
        $string = 'es.*,u.first_name,u.last_name';
        $table  = 'email_setup es';
        $join   = [
            "users u" => "es.added_user_id=u.id"];
        $order = [
            "email_setup_id" => "desc"];
        $where = array(
            'es.delete_status' => 0,
            'es.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'es.added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
        );
        $filter = array(
            'es.email_protocol',
            'es.smtp_host',
            'es.smtp_username',
            'es.from_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function email_template_list_field()
    {
        $string = 'et.*,u.first_name,u.last_name,m.module_name';
        $table  = 'email_template et';
        $join   = [
            "users u"   => "et.added_user_id=u.id",
            "modules m" => "et.module_id=m.module_id"];
        $order = [
            "email_template_id" => "desc"];
        $where = array(
            'et.delete_status' => 0,
            'et.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = array(
            'et.email_template_name',
            'et.subject',
            'm.module_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function partial_sales_list_field()
    {
        $string = "s.*,c.customer_name,u.first_name,u.last_name,r.MaxTime, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "sales s";
        $join   = [
            'currency cur'                                                                                => 's.currency_id = cur.currency_id',
            "customer c"                                                                                  => "s.sales_party_id = c.customer_id",
            "users u"                                                                                     => "s.added_user_id = u.id",
            "( SELECT MAX(date) as MaxTime,type_id FROM followup WHERE type='sales' GROUP BY type_id) r " => 'r.type_id = s.sales_id # left'];
        $order = [
            "s.sales_id" => "desc"];
        $where = array(
            's.delete_status'                                                                             => 0,
            '((s.sales_grand_total+s.credit_note_amount) - (s.sales_paid_amount+s.debit_note_amount)) > ' => 0,
            // 's.sales_paid_amount !=' =>0,
            's.branch_id'                                                                                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'                                                                         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            's.sales_invoice_number',
            's.sales_date',
            's.sales_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function pending_sales_list_field()
    {
        $string = "s.*,c.customer_name,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text";
        $table  = "sales s";
        $join   = [
            'currency cur' => 's.currency_id = cur.currency_id',
            "customer c"   => "s.sales_party_id = c.customer_id",
            "users u"      => "s.added_user_id = u.id"];
        $order = [
            "s.sales_id" => "desc"];
        $where = array(
            's.delete_status'     => 0,
            's.sales_paid_amount' => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            's.sales_invoice_number',
            's.sales_date',
            's.sales_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function partial_purchase_list_field()
    {
        $string = "p.*,s.supplier_name,u.first_name,u.last_name,cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,r.MaxTime";
        $table  = "purchase p";
        $join   = [
            'currency cur'                                                                                   => 'p.currency_id = cur.currency_id',
            "supplier s"                                                                                     => "p.purchase_party_id=s.supplier_id",
            "users u"                                                                                        => "u.id = p.added_user_id",
            "( SELECT MAX(date) as MaxTime,type_id FROM followup WHERE type='purchase' GROUP BY type_id) r " => 'r.type_id = p.purchase_id # left'];
        $order = [
            "p.purchase_id" => "desc"];
        $where = array(
            'p.delete_status'                                                                                       => 0,
            '((p.purchase_grand_total+p.credit_note_amount) - (p.purchase_paid_amount+p.purchase_return_amount)) >' => 0,
            'p.purchase_paid_amount !='                                                                             => 0,
            'p.branch_id'                                                                                           => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'                                                                                   => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'p.purchase_invoice_number',
            'p.purchase_date',
            'p.purchase_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function pending_purchase_list_field()
    {
        $string = "p.*,s.*,u.first_name,u.last_name,cur.*";
        $table  = "purchase p";
        $join   = [
            'currency cur' => 'p.currency_id = cur.currency_id',
            "supplier s"   => "p.purchase_party_id=s.supplier_id",
            "users u"      => "u.id = p.added_user_id"];
        $order = [
            "p.purchase_id" => "desc"];
        $where = array(
            'p.delete_status'        => 0,
            'p.purchase_paid_amount' => 0,
            'p.branch_id'            => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'    => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'p.purchase_invoice_number',
            'p.purchase_date',
            'p.purchase_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function sales_credit_note_list_field($order_ser='',$dir = '')
    {
        $string = "cr.*,s.sales_invoice_number,c.customer_id, st.state_id,stc.customer_id as ship_to_id, stc.customer_name as ship_to_name, c.customer_name,u.first_name,u.last_name,cur.currency_name,cur.currency_id as cur_currency_id, cur.currency_symbol, cur.currency_code, cur.currency_text,coun.country_id as billing_country_id, coun.country_name, sh.shipping_address as billing_address,sa.shipping_address,st.state_name, st.is_utgst, CASE st.is_utgst when '1' then cr.sales_credit_note_sgst_amount ELSE 0
                END as utgst, CASE st.is_utgst when '0' then cr.sales_credit_note_sgst_amount 
                ELSE 0 END as sgst,";
        $table  = "sales_credit_note cr";
        $join   = [
            'currency cur' => 'cr.currency_id = cur.currency_id'.'#'.'left',
            "sales s"      => "s.sales_id = cr.sales_id",
            "customer c"   => "cr.sales_credit_note_party_id = c.customer_id",
            "customer stc"   => "cr.sales_credit_note_ship_to_customer_id = stc.customer_id" . '#' . 'left',
            "users u"      => "cr.added_user_id = u.id",
            "countries coun" => "cr.sales_credit_note_billing_country_id = coun.country_id".'#'.'left',
            "shipping_address sh" => "cr.billing_address_id = sh.shipping_address_id ".'#'.'left',
            "shipping_address sa" => "cr.shipping_address_id = sa.shipping_address_id".'#'.'left',
            "states st"    => "cr.sales_credit_note_billing_state_id = st.state_id".'#'.'left'];
        if($order_ser =='' || $dir == ''){
                $order = [ 'cr.sales_credit_note_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            } 
        $where = array(
            'cr.delete_status'     => 0,
            'cr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'cr.sales_credit_note_invoice_number',
            'DATE_FORMAT(cr.sales_credit_note_date, "%d-%m-%Y")',
            'cr.sales_credit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function sales_credit_note_report_list_field()
    {
        $string = "count(*) as credit_count, sum(sales_credit_note_grand_total) as sales_credit_note_grand_total";
        $table  = "sales_credit_note";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }
    public function sales_debit_note_list_field($order_ser='',$dir = '')
    {
        $string = "dr.*,s.sales_invoice_number,c.customer_name,c.customer_id,stc.customer_id as ship_to_id, stc.customer_name as ship_to_name, u.first_name,u.last_name,cur.currency_name,cur.currency_id as cur_currency_id,  cur.currency_symbol, cur.currency_code, cur.currency_text ,con.country_name,con.country_id, sh.shipping_address,bs.shipping_address as billing_address, st.state_name,st.is_utgst,CASE st.is_utgst when '1' then dr.sales_debit_note_sgst_amount ELSE 0
                END as utgst, CASE st.is_utgst when '0' then dr.sales_debit_note_sgst_amount ELSE 0
                END as sgst,";
        $table  = "sales_debit_note dr";
        $join   = [
            'currency cur' => 'dr.currency_id = cur.currency_id',
            "sales s"      => "s.sales_id = dr.sales_id",
            "customer c"   => "dr.sales_debit_note_party_id = c.customer_id",
            "customer stc"   => "dr.sales_debit_note_ship_to_customer_id = stc.customer_id" . '#' . 'left',
            "users u"      => "dr.added_user_id = u.id",
            "countries con"=> "dr.sales_debit_note_billing_country_id = con.country_id".'#'.'left',
            "shipping_address sh" => "dr.shipping_address_id = sh.shipping_address_id".'#'.'left',
            "shipping_address bs" => "dr.billing_address_id = bs.shipping_address_id".'#'.'left',
            "states st" => "dr.sales_debit_note_billing_state_id = st.state_id".'#'.'left'
            ];
        if($order_ser =='' || $dir == ''){
                $order = [ 'dr.sales_debit_note_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $where = array(
            'dr.delete_status'     => 0,
            'dr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'dr.sales_debit_note_invoice_number',
            'DATE_FORMAT(dr.sales_debit_note_date, "%d-%m-%Y")',
            'dr.sales_debit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function sales_debit_note_report_list_field()
    {
        $string = "count(*) as debit_count, sum(sales_debit_note_grand_total) as sales_debit_note_grand_total";
        $table  = "sales_debit_note";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }
    public function purchase_credit_note_list_field()
    {
        $string = "cr.*,p.purchase_invoice_number,u.first_name,u.last_name,s.supplier_name, DATE_ADD(cr.purchase_credit_note_date,INTERVAL 15 DAY) as receivable_date,co.country_name as billing_country,co.country_id, st.state_name as place_of_supply,st.state_id, cu.currency_name as billing_currency, st.is_utgst,CASE st.is_utgst when '1' then cr.purchase_credit_note_sgst_amount ELSE 0
                END as utgst, CASE st.is_utgst when '0' then cr.purchase_credit_note_sgst_amount ELSE 0 END as sgst";
        //cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,
        $table = "purchase_credit_note cr";
        $join  = [
            "purchase p"   => "p.purchase_id = cr.purchase_id",
            "supplier s"   => "cr.purchase_credit_note_party_id = s.supplier_id",
            "users u"      => "cr.added_user_id = u.id",
            "countries co" => "cr.purchase_credit_note_billing_country_id = co.country_id".'#'.'left',
            "states st"    => "cr.purchase_credit_note_billing_state_id = st.state_id".'#'.'left',
            "currency cu" => "cr.currency_id = cu.currency_id". '#' . 'left'];//'currency cur' => 'cr.currency_id = cur.currency_id',
        $order = [
            "cr.purchase_credit_note_id" => "desc"];
        $where = array(
            'cr.delete_status'     => 0,
            'cr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'cr.purchase_credit_note_invoice_number',
            'cr.purchase_credit_note_date',
            'cr.purchase_credit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function purchase_credit_note_report_list_field()
    {
        $string = "count(*) as purchase_cn_count, sum(purchase_credit_note_grand_total) as purchase_credit_note_grand_total";
        //cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text,
        $table = "purchase_credit_note";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }
    public function purchase_debit_note_list_field()
    {
        $string = "cr.*,p.purchase_invoice_number,u.first_name,u.last_name,s.supplier_name,DATE_ADD(cr.purchase_debit_note_date,INTERVAL 15 DAY) as receivable_date,co.country_name as billing_country,st.state_id, st.state_name as place_of_supply,crr.currency_name, st.is_utgst, CASE st.is_utgst when '1' then cr.purchase_debit_note_sgst_amount ELSE 0
            END as utgst, CASE st.is_utgst when '0' then cr.purchase_debit_note_sgst_amount ELSE 0 END as sgst";
        $table = "purchase_debit_note cr";
        $join  = [
            "purchase p"   => "p.purchase_id = cr.purchase_id",
            "supplier s"   => "cr.purchase_debit_note_party_id = s.supplier_id",
            "users u"      => "cr.added_user_id = u.id",
            'countries co' => 'cr.purchase_debit_note_billing_country_id = co.country_id' . '#' . 'left',
            'states st' => 'cr.purchase_debit_note_billing_state_id = st.state_id' . '#' . 'left',
            'currency crr' => 'cr.currency_id = crr.currency_id' . '#' . 'left'];
            //'currency cur' => 'cr.currency_id = cur.currency_id',
        $order = [
            "cr.purchase_debit_note_id" => "desc"];
        $where = array(
            'cr.delete_status'     => 0,
            'cr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'cr.purchase_debit_note_invoice_number',
            'cr.purchase_debit_note_date',
            'cr.purchase_debit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function purchase_debit_note_report_list_field()
    {
        $string = "count(*) as purchase_dn_count, sum(purchase_debit_note_grand_total) as purchase_debit_note_grand_total";
        $table = "purchase_debit_note";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => '',
            'order'  => ''
        );
        return $data;
    }
    public function sales_credit_note_list_field1($sales_credit_note_id)
    {
        $string = "sc.*,st1.state_name as place_of_supply,
                   co.country_name as billing_country,
                   cur.*,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   sa.department,
                   sa.contact_person,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number
                   ";
        $table = "sales_credit_note sc";
        $join = [
            'currency cur'        => 'sc.currency_id = cur.currency_id',
            'customer c'          => 'sc.sales_credit_note_party_id = c.customer_id',
            'states st1'          => 'sc.sales_credit_note_billing_state_id = st1.state_id' . '#' . 'left',
            'countries co'        => 'sc.sales_credit_note_billing_country_id = co.country_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = sc.shipping_address_id' . '#' . 'left'];
        $where['sc.sales_credit_note_id'] = $sales_credit_note_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    // public function sales_credit_note_list_field1($sales_credit_note_id)
    // {
    //     $string = "cr.*,s.sales_id,s.sales_invoice_number,st1.state_name as place_of_supply,
    //                cur.*,
    //                sa.shipping_address as shipping_address_sa,
    //                sa.shipping_gstin,
    //                c.customer_name,
    //                c.customer_address,
    //                c.customer_mobile,
    //                c.customer_email,
    //                c.customer_postal_code,
    //                c.customer_gstin_number,
    //                c.customer_state_id,
    //                c.customer_tan_number,
    //                c.customer_pan_number,
    //                c.customer_state_code,
    //                ct.city_name as customer_city_name,
    //                cs.state_name as customer_state_name,
    //                cu.country_name as customer_country_name";
    //     $table = "sales_credit_note cr";
    //     $join  = [
    //         'currency cur'        => 'cur.currency_id = cr.currency_id',
    //         'sales s'             => 'cr.sales_id = s.sales_id',
    //         'customer c'          => 'cr.sales_credit_note_party_id = c.customer_id',
    //         'cities ct'           => 'c.customer_city_id = ct.city_id',
    //         'states cs'           => 'c.customer_state_id = cs.state_id',
    //         'countries cu'        => 'c.customer_country_id = cu.country_id',
    //         'states st1'          => 'cr.credit_note_billing_state_id = st1.state_id' . '#' . 'left',
    //         'shipping_address sa' => 'sa.shipping_address_id = cr.shipping_address' . '#' . 'left'];
    //     $where['cr.sales_credit_note_id'] = $sales_credit_note_id;
    //     $data = array(
    //         'string' => $string,
    //         'table'  => $table,
    //         'where'  => $where,
    //         'join'   => $join
    //     );
    //     return $data;
    // }
    public function sales_credit_note_items_product_list_field($sales_credit_note_id)
    {
        $string = "sci.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value,U.uom as product_unit,pr.product_tax_id,pr.product_tds_id,pr.product_tax_value,td.tax_name as tds_module_type,pr.product_batch";
        $table  = "sales_credit_note_item sci";
        $join   = [
            'products pr' => 'sci.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = sci.sales_credit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'sci.sales_credit_note_id' => $sales_credit_note_id,
            'sci.delete_status'           => 0,
            'sci.item_type'               => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_credit_note_items_product_inventory_list_field($sales_credit_note_id)
    {
        $string = "sci.*,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value ,pr.varient_unit as product_unit,p.product_tax_id,p.product_tds_id,p.product_tax_value,td.tax_name as tds_module_type";
        $table  = "sales_credit_note_item sci";
        $join   = [
            'product_inventory_varients pr' => 'sci.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = sci.sales_credit_note_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'sci.sales_credit_note_id' => $sales_credit_note_id,
            'sci.delete_status'           => 0,
            'sci.item_type'               => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
        public function sales_credit_note_items_service_list_field($sales_credit_note_id)
    {
        $string = "sci.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value,sr.service_tax_value,sr.service_tax_id,sr.service_tds_id,td.tax_name as tds_module_type,U.uom as product_unit";
        $table  = "sales_credit_note_item sci";
        $join   = [
            'services sr' => 'sci.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = sci.sales_credit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'sci.sales_credit_note_id' => $sales_credit_note_id,
            'sci.delete_status'           => 0,
            'sci.item_type'               => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_debit_note_list_field1($sales_debit_note_id)
    {
        $string = "sd.*,st1.state_name as place_of_supply,
                   co.country_name as billing_country,
                   cur.*,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   sa.department,
                   sa.contact_person,
                   c.customer_name,
                   c.customer_address,
                   c.customer_mobile,
                   c.customer_email,
                   c.customer_postal_code,
                   c.customer_gstin_number,
                   c.customer_state_id,
                   c.customer_tan_number,
                   c.customer_pan_number
                   ";
        $table = "sales_debit_note sd";
        $join = [
            'currency cur'        => 'sd.currency_id = cur.currency_id',
            'customer c'          => 'sd.sales_debit_note_party_id = c.customer_id',
            'states st1'          => 'sd.sales_debit_note_billing_state_id = st1.state_id' . '#' . 'left',
            'countries co'        => 'sd.sales_debit_note_billing_country_id = co.country_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = sd.shipping_address_id' . '#' . 'left'];
        $where['sd.sales_debit_note_id'] = $sales_debit_note_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_debit_note_items_product_list_field($sales_debit_note_id)
    {
        $string = "sdi.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value,U.uom as product_unit,pr.product_tax_id,pr.product_tds_id,pr.product_tax_value,td.tax_name as tds_module_type,pr.product_batch";
        $table  = "sales_debit_note_item sdi";
        $join   = [
            'products pr' => 'sdi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = sdi.sales_debit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'sdi.sales_debit_note_id' => $sales_debit_note_id,
            'sdi.delete_status'          => 0,
            'sdi.item_type'              => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_debit_note_items_product_inventory_list_field($sales_debit_note_id)
    {
        $string = "sdi.*,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value ,pr.varient_unit as product_unit,p.product_tax_id,p.product_tds_id,p.product_tax_value,td.tax_name as tds_module_type";
        $table  = "sales_debit_note_item sdi";
        $join   = [
            'product_inventory_varients pr' => 'sdi.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = sdi.sales_debit_note_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'sdi.sales_debit_note_id' => $sales_debit_note_id,
            'sdi.delete_status'          => 0,
            'sdi.item_type'              => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function sales_debit_note_items_service_list_field($sales_debit_note_id)
    {
        $string = "sdi.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value,sr.service_tax_value,sr.service_tax_id,sr.service_tds_id,td.tax_name as tds_module_type,U.uom as product_unit";
        $table  = "sales_debit_note_item sdi";
        $join   = [
            'services sr' => 'sdi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = sdi.sales_debit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'sdi.sales_debit_note_id' => $sales_debit_note_id,
            'sdi.delete_status'          => 0,
            'sdi.item_type'              => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_credit_note_list_field1($purchase_credit_note_id)
    {
        $string = "p.*,st1.state_name as place_of_supply,
                   co.country_name as billing_country,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   sa.contact_person,
                   sa.department,
                   s.supplier_name,
                   s.supplier_address,
                   s.supplier_mobile,
                   s.supplier_email,
                   s.supplier_postal_code,
                   s.supplier_gstin_number,
                   s.supplier_state_id,
                   s.supplier_tan_number,
                   s.supplier_pan_number
                   ";
        $table = "purchase_credit_note p";
        $join = [
            'supplier s'          => 'p.purchase_credit_note_party_id = s.supplier_id',
            'states st1'          => 'p.purchase_credit_note_billing_state_id = st1.state_id' . '#' . 'left',
            'countries co'        => 'p.purchase_credit_note_billing_country_id = co.country_id' . '#' . 'left',
           'shipping_address sa' => 'sa.shipping_address_id = p.shipping_address_id' . '#' . 'left'];

        $where['p.purchase_credit_note_id'] = $purchase_credit_note_id;

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_credit_note_items_product_list_field($purchase_credit_note_id)
    {
        $string = "pi.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value,U.uom as product_unit,pr.product_tax_id,pr.product_tds_id,pr.product_batch,pr.product_tax_value,td.tax_name as tds_module_type";
        $table  = "purchase_credit_note_item pi";
        $join   = [
            'products pr' => 'pi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = pi.purchase_credit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_credit_note_id' => $purchase_credit_note_id,
            'pi.delete_status'           => 0,
            'pi.item_type'               => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_credit_note_items_product_inventory_list_field($purchase_credit_note_id)
    {
        $string = "pi.*,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value ,pr.varient_unit as product_unit,p.product_tax_id,p.product_tds_id,p.product_tax_value,td.tax_name as tds_module_type";
        $table  = "purchase_credit_note_item pi";
        $join   = [
            'product_inventory_varients pr' => 'pi.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = pi.purchase_credit_note_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_credit_note_id' => $purchase_credit_note_id,
            'pi.delete_status'           => 0,
            'pi.item_type'               => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_credit_note_items_service_list_field($purchase_credit_note_id)
    {
        $string = "pi.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value,sr.service_tax_value,sr.service_tax_id,sr.service_tds_id,td.tax_name as tds_module_type,U.uom as product_unit";
        $table  = "purchase_credit_note_item pi";
        $join   = [
            'services sr' => 'pi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = pi.purchase_credit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_credit_note_id' => $purchase_credit_note_id,
            'pi.delete_status'           => 0,
            'pi.item_type'               => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_debit_note_list_field1($purchase_debit_note_id)
    {
        $string = "p.*,st1.state_name as place_of_supply,
                   co.country_name as billing_country,
                   sa.shipping_address as shipping_address,
                   sa.shipping_gstin,
                   sa.contact_person,
                   sa.department,
                   s.supplier_name,
                   s.supplier_address,
                   s.supplier_mobile,
                   s.supplier_email,
                   s.supplier_postal_code,
                   s.supplier_gstin_number,
                   s.supplier_state_id,
                   s.supplier_tan_number,
                   s.supplier_pan_number
                   ";
        $table = "purchase_debit_note p";
        $join = [
            'supplier s'          => 'p.purchase_debit_note_party_id = s.supplier_id',
            'states st1'          => 'p.purchase_debit_note_billing_state_id = st1.state_id' . '#' . 'left',
            'countries co'        => 'p.purchase_debit_note_billing_country_id = co.country_id' . '#' . 'left',
            'shipping_address sa' => 'sa.shipping_address_id = p.shipping_address_id' . '#' . 'left'];
            //'currency cur'        => 'p.currency_id = cur.currency_id',
        $where['p.purchase_debit_note_id'] = $purchase_debit_note_id;
    
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_debit_note_items_product_list_field($purchase_debit_note_id)
    {
        $string = "pi.*,pr.product_id,pr.product_code,pr.product_name, pr.product_hsn_sac_code,pr.product_price,dt.discount_value,U.uom as product_unit,pr.product_batch,pr.product_tax_id,pr.product_tds_id,pr.product_tax_value,td.tax_name as tds_module_type";
        $table  = "purchase_debit_note_item pi";
        $join   = [
            'products pr' => 'pi.item_id = pr.product_id',
            'discount dt' => 'dt.discount_id = pi.purchase_debit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = pr.product_tds_id' . '#' . 'left',
            'uqc U'      => 'U.id = pr.product_unit_id' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_debit_note_id' => $purchase_debit_note_id,
            'pi.delete_status'          => 0,
            'pi.item_type'              => 'product'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_debit_note_items_product_inventory_list_field($purchase_debit_note_id)
    {
        $string = "pi.*,pr.product_inventory_varients_id as product_id,pr.varient_code as product_code,pr.varient_name as product_name, p.product_hsn_sac_code,pr.purchase_price as product_price,dt.discount_value ,pr.varient_unit as product_unit,p.product_tax_id,p.product_tds_id,p.product_tax_value,td.tax_name as tds_module_type";
        $table  = "purchase_debit_note_item pi";
        $join   = [
            'product_inventory_varients pr' => 'pi.item_id = pr.product_inventory_varients_id',
            'product_inventory p'           => 'p.product_inventory_id = pr.product_inventory_id',
            'discount dt'                   => 'dt.discount_id = pi.purchase_debit_note_item_discount_id' . '#' . 'left',
            'tax td'                        => 'td.tax_id = p.product_tds_id' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_debit_note_id' => $purchase_debit_note_id,
            'pi.delete_status'          => 0,
            'pi.item_type'              => 'product_inventory'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_debit_note_items_service_list_field($purchase_debit_note_id)
    {
        $string = "pi.*,sr.service_id,sr.service_code,sr.service_name, sr.service_hsn_sac_code,sr.service_price,dt.discount_value,sr.service_tax_value,sr.service_tax_id,sr.service_tds_id,td.tax_name as tds_module_type,U.uom as product_unit";

        $table  = "purchase_debit_note_item pi";
        $join   = [
            'services sr' => 'pi.item_id = sr.service_id',
            'discount dt' => 'dt.discount_id = pi.purchase_debit_note_item_discount_id' . '#' . 'left',
            'tax td'      => 'td.tax_id = sr.service_tds_id' . '#' . 'left',
            'uqc U'       => 'U.id = sr.service_unit' . '#' . 'left'
        ];
        $where = [
            'pi.purchase_debit_note_id' => $purchase_debit_note_id,
            'pi.delete_status'          => 0,
            'pi.item_type'              => 'service'];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function expense_bill_list_field1($expense_bill_id)
    {
        $string = "eb.*,
                   sa.contact_person,
                   sa.department,
                   s.supplier_name,
                   s.supplier_address,
                   s.supplier_mobile,
                   s.supplier_email,
                   s.supplier_postal_code,
                   s.supplier_gstin_number,
                   s.supplier_state_id,
                   s.supplier_tan_number,
                   s.supplier_pan_number,
                   s.supplier_state_code,
                   ct.city_name as supplier_city_name,
                   cs.state_name as supplier_state_name,
                   cu.country_name as supplier_country_name";
        $table = "expense_bill eb";
        $join  = [
            'supplier s'   => 'eb.expense_bill_payee_id = s.supplier_id',
            'cities ct'    => 's.supplier_city_id = ct.city_id',
            'states cs'    => 's.supplier_state_id = cs.state_id'.'#'.'left',
            'countries cu' => 's.supplier_country_id = cu.country_id'.'#'.'left',
            'shipping_address sa' => 'sa.shipping_party_id = eb.expense_bill_payee_id' . '#' . 'left'];
            //'currency cur' => 'cur.currency_id = eb.currency_id',
        // $where['eb.expense_bill_id'] = $expense_bill_id;
        $where =  array(
            'eb.expense_bill_id' => $expense_bill_id,
            'sa.shipping_party_type' => 'supplier',
            'sa.primary_address' => 'yes'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }
    public function category_list_field()
    {
        $string = 'c.*,u.first_name,u.last_name';
        $table  = 'category c';
        $join   = [
            "users u" => "c.added_user_id=u.id"];
        $order = [
            "category_id" => "desc"];
        $where = array(
            'c.delete_status' => 0,
            'c.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = array(
            'c.category_code',
            'c.category_name',
            'c.category_type');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function tds_report_list($from_date, $to_date)
    {
        $string = 'ei.*,e.expense_bill_date';
        $table  = 'expense_bill_item ei';
        $join   = [
            "expense_bill e" => "e.expense_bill_id=ei.expense_bill_id"];
        $order = [
            "ei.expense_bill_item_id" => "desc"];
        $where = array(
            'ei.delete_status'        => 0,
            'e.delete_status'         => 0,
            'e.branch_id'             => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'     => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.expense_bill_date >= ' => $from_date,
            'e.expense_bill_date <= ' => $to_date
        );
        $filter = array(
            'e.expense_bill_date',
            'ei.expense_bill_item_description',
            'ei.expense_bill_item_sub_total',
            'ei.expense_bill_item_tds_percentage',
            'ei.expense_bill_item_tds_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function account_sub_group_list_field()
    {
        $string = 'as.*,a.account_group_title';
        $table  = 'account_subgroup as';
        $join   = [
            "account_group a" => "a.account_group_id=as.account_group_id"];
        $order = [
            "account_group_id" => "desc"];
        $where = array(
            'as.delete_status' => 0,
            'as.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = "";
        $data   = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function ledger_list_field()
    {
        $string = 'l.*,a.subgroup_title';
        $table  = 'ledgers l';
        $join   = [
            "account_subgroup a" => "a.account_subgroup_id=l.account_subgroup_id"];
        $order = [
            "ledger_id" => "desc"];
        $where = array(
            'l.delete_status' => 0,
            'l.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = "";
        $data   = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function account_group_list_field()
    {
        $string = 'a.*';
        $table  = 'account_group a';
        $join   = "";
        $order  = [
            "account_group_id" => "desc"];
        $where = array(
            'a.delete_status' => 0,
            'a.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = "";
        $data   = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function customer_list_field()
    {
        $string = 'cust.*,c.city_name,co.country_name,st.state_name,u.first_name,u.last_name';
        $table  = 'customer cust';
        // $join['contact_person cp']='cp.contact_person_id=cust.customer_contact_person_id';
        $join['cities c']     = 'cust.customer_city_id=c.city_id' . '#' . 'left';
        $join['states st']    = 'cust.customer_state_id=st.state_id';
        $join['countries co'] = 'cust.customer_country_id=co.country_id';
        $join['users u']      = 'cust.added_user_id=u.id';
        $where = array(
            'cust.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cust.delete_status' => 0
        );
        $order = [
            "cust.customer_id" => "desc"];
        $filter = array(
            'cust.customer_name',
            'cust.customer_code',
            'co.country_name',
            'st.state_name',
            'c.city_name'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function contact_person_list_field(){
        $string = 'con.*,c.city_name,co.country_name,st.state_name,u.first_name,u.last_name';
        $table  = 'contact_person con';
        // $join['contact_person cp']='cp.contact_person_id=cust.customer_contact_person_id';
        $join['cities c']     = 'con.contact_person_city_id=c.city_id';
        $join['states st']    = 'con.contact_person_state_id=st.state_id';
        $join['countries co'] = 'con.contact_person_country_id=co.country_id';
        $join['users u']      = 'con.added_user_id=u.id';
        $where = array(
            'con.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'con.delete_status' => 0
        );
        $order = [
            "con.contact_person_id" => "desc"];
        $filter = array(
            'con.contact_person_name',
            'con.contact_person_code');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function bank_account_list_field()
    {
        $string          = 'b.*,u.first_name,u.last_name';
        $table           = 'bank_account b';
        $join['users u'] = 'b.added_user_id=u.id';
        $where = array(
            'b.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'b.delete_status' => 0
        );
        $order = [
            "b.bank_account_id" => "desc"];
        $filter=array('b.bank_name','b.account_no','b.bank_address');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function discount_list_field()
    {
        $string = 'd.*,u.first_name,u.last_name';
        $table  = 'discount d';
        $join   = [
            "users u" => "u.id = d.added_user_id"];
        $where = array(
            'd.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'd.delete_status' => 0
        );
        $order = [
            "d.discount_id" => "desc"];
        $filter = array(
            'd.discount_name',
            'd.discount_value');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function expense_list_field($order_ser='',$dir = '')
    {
        $string = 'e.*,u.first_name,u.last_name';
        $table  = 'expense e';
        $join   = [
            "users u" => "u.id = e.added_user_id"];
        $where = array(
            'e.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.delete_status' => 0
        );
        if($order_ser =='' || $dir == ''){
                $order = [ 'e.expense_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }
        $filter = array(
            'e.expense_title',
            'e.expense_hsn_code',
            'e.expense_description');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

     public function product_batchlist_field($order_ser,$dir){
        $string             = "p.*,c.category_name,u.first_name,u.last_name,m.uom";
        $table              = "products p";
        $join['category c'] = "c.category_id=p.product_category_id";
        $join['users u']    = "u.id = p.added_user_id";
         $join['uqc m']    = 'm.id = p.product_unit_id' . '#' . 'left';
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0
        );

        if($order_ser =='' || $dir == ''){
            $order = [ "p.product_id" => "desc"];
        }else{
            $order = [ $order_ser => $dir ];
        }
      
        $filter = array(
            'p.product_code',
            'p.product_name',
            'p.product_model_no'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function product_list_field(){
        $string             = "p.*,c.category_name,u.first_name,u.last_name,m.uom";
        $table              = "products p";
        $join['category c'] = "c.category_id=p.product_category_id";
        $join['users u']    = "u.id = p.added_user_id";
        $join['uqc m']    = 'm.id = p.product_unit_id' . '#' . 'left';
        $where = array(
            /*'p.batch_parent_product_id' => 0,*/
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0
        );
        $order = [
            "p.product_id" => "desc"];
        $filter = array(
            'p.product_code',
            'p.product_name',
            'p.product_model_no'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function billing_info_list($order_ser='',$dir = ''){
        $string         = "b.*,f.firm_company_code,f.firm_name,p.payment_method,p.Id";
        $table          = "tbl_billing_info b";
        $join['firm f'] = "b.firm_id=f.firm_id";
        $join['payment_methods p'] = "b.package = p.Id";
        
        $where = array();
        if($order_ser =='' || $dir == ''){
            $order = ["b.bill_id" => "desc"];
        }else{
            $order = [ $order_ser => $dir ];
        }
        $filter = array(
            'f.firm_company_code',
            'b.amount',
            'f.firm_name',
            'p.payment_method'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function mainProduct_list_field($LeatherCraft_id){
        $string             = "p.*,c.category_name,u.first_name,u.last_name,m.uom,b.brand_name";
        $table              = "products p";
        $join['category c'] = "c.category_id=p.product_category_id";
        $join['users u']    = "u.id = p.added_user_id";
        $join['uqc m']    = 'm.id = p.product_unit_id' . '#' . 'left';
        $join['brand b']    = 'b.brand_id = p.brand_id' . '#' . 'left';
        if($LeatherCraft_id == $this->ci->session->userdata('SESS_BRANCH_ID')){
            $where = array(
            'p.batch_parent_product_id' => 0,
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0,
            'p.product_combination_id' => NULL
            );
        }else{
            $where = array(
            'p.batch_parent_product_id' => 0,
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0
            );
        }
        
        $order = [
            "p.product_id" => "desc"];
        $filter = array(
            'p.product_code',
            'p.product_name',
            'p.product_model_no'
        );
        $group = array();
        if($LeatherCraft_id == $this->ci->session->userdata('SESS_BRANCH_ID'))
            $group=array('p.product_name','p.product_code');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }

    public function service_list_field(){
        $string             = "s.*,c.category_name,u.first_name,u.last_name";
        $table              = "services s";
        $join['category c'] = "c.category_id=s.service_category_id";
        $join['users u']    = "u.id = s.added_user_id";
        $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0
        );
        $order = [
            "s.service_id" => "desc"];
        $filter = array(
            's.service_code',
            's.service_name',
            's.service_hsn_sac_code');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function subcategory_list_field()
    {
        $string = 's.*,c.category_name  ,u.first_name,u.last_name';
        $table  = 'sub_category s';
        $join   = [
            "users u"    => "s.added_user_id=u.id",
            'category c' => "c.category_id=s.category_id"];
        $order = [
            "s.sub_category_id" => "desc"];
        $where = array(
            's.delete_status' => 0,
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $filter = array(
            's.sub_category_code',
            's.sub_category_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function varients_list_field()
    {
        $string = "v.varients_id,v.varient_key,v.added_date,vv.varients_value,vv.varients_value_id,u.first_name,u.last_name";
        $table  = "varients v";
        $join   = [
            'varients_value vv' => 'v.varients_id = vv.varients_id' . '#' . 'left',
            "users u"           => "v.added_user_id=u.id"];
        $where = array(
            'v.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'vv.delete_status' => 0);
        // $group=array('vv.varients_id');
        $filter = array(
            'v.varient_key',
            'vv.varients_value');
         $order = [ 
            "vv.varients_value_id" => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => ""
        );
        return $data;
    }
    public function supplier_list_field()
    {
        $string = 's.*,c.city_name,co.country_name,st.state_name,u.first_name,u.last_name';
        $table  = 'supplier s';
        // $join['contact_person cp']='cp.contact_person_id=s.supplier_contact_person_id';
        $join['cities c']     = 's.supplier_city_id=c.city_id' . '#' . 'left';
        $join['states st']    = 's.supplier_state_id=st.state_id';
        $join['countries co'] = 's.supplier_country_id=co.country_id';
        $join['users u']      = 's.added_user_id=u.id';
        $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0
        );
        $order = [
            "s.supplier_id" => "desc"];
        $filter = array(
            's.supplier_name',
            's.supplier_code',
            'c.city_name',
            'st.state_name',
            'co.country_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function tax_list_field($id='')
    {
        $string          = 't.*,s.section_name';
        $table           = 'tax t';
         $join   = [
            "tax_section s" => "t.section_id=s.section_id". '#' . 'left'];
    if($id == ''){
        $where = array(
            't.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            't.delete_status' => 0
        );
    }else{
        $where = array(
            't.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            't.delete_status' => 0,
            "t.tax_id" => $id
        );
    }
        
        $order = [
            "t.tax_id" => "desc"];
        $filter = array(
            't.tax_name',
            't.tax_value',
            's.section_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function uqc_list_field()
    {
        $string          = 'uqc.*,u.first_name,u.last_name';
        $table           = 'uqc';
        $join['users u'] = 'uqc.added_user_id=u.id';
        $where = array(
            'uqc.delete_status' => 0
        );
        $order = [
            "uqc.id" => "desc"];
        $filter = array(
            'uqc.uom'
            );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function user_group_list_field()
    {
        $string          = '*';
        $table           = 'groups';
        $where = array(
            'delete_status' => 0,
            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $order = [
            "id" => "desc"];
        $filter = array(
            'group_name',
            'description'
            );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
     public function active_module_list_field()
    {
        $string = "m.module_id,m.module_name,m.is_report";
        $table  = "active_modules am";
        $join   = [
            "modules m"        => "am.module_id = m.module_id"];
        $where = array(
            'm.delete_status'     => 0,
            'am.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        $order = [
            "m.is_report" => "desc",
            "m.module_name" => "asc"];
        $filter = array(
            'm.module_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function assigned_module_list_field($group_id)
    {
        $string = "*";
        $table  = "group_accessibility";
        $where = array(
            'delete_status'     => 0,
            'branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'group_id '  => $group_id
        );
        $filter = array(
            'module_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => '',
            'filter' => $filter,
            'order'  => ''
        );
        return $data;
    }
    public function module_settings_list_field()
    {
        $string            = 's.*,m.module_name';
        $table             = 'settings s';
        $join['modules m'] = 's.module_id=m.module_id';
        $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0
        );
        $order = [
            "s.settings_id" => "desc"];
        $filter = array(
            's.settings_invoice_first_prefix',
            's.settings_invoice_last_prefix',
            's.invoice_seperation',
            's.invoice_type',
            's.invoice_creation',
            's.item_access');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function module_brand_list_field()
    {
        $string            = '*';
        $table             = 'brand';
        $where = array(
            'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0
        );
        $order = [
            "brand_id" => "desc"];
        $filter = array(
            'brand_invoice_first_prefix',
            'brand_invoice_last_prefix',
            'invoice_seperation',
            'invoice_type',
            'invoice_creation',
            'item_access');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function recurrence_sales_list_field()
    {
        $string = "r.*,s.sales_id as id,s.sales_invoice_number as invoice_number,s.sales_grand_total as grand_total,u.first_name,u.last_name";
        $table  = "recurrence r";
        $join   = [
            'sales s' => 's.sales_id = r.invoice_id',
            "users u" => "r.added_user_id = u.id"];
        $order = [
            "r.recurrence_id" => "desc"];
        $where = array(
            'r.invoice_type'      => 'sales',
            'r.delete_status'     => 0,
            'r.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'r.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.sales_invoice_number',
            's.sales_date',
            's.sales_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function recurrence_expense_bill_list_field()
    {
        $string = "r.*,e.expense_bill_id as id,e.expense_bill_invoice_number as invoice_number,e.expense_bill_grand_total as grand_total,u.first_name,u.last_name";
        $table  = "recurrence r";
        $join   = [
            'expense_bill e' => 'e.expense_bill_id = r.invoice_id',
            "users u"        => "r.added_user_id = u.id"];
        $order = [
            "r.recurrence_id" => "desc"];
        $where = array(
            'r.invoice_type'      => 'expense_bill',
            'r.delete_status'     => 0,
            'r.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'r.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'e.expense_bill_invoice_number',
            'e.expense_bill_date',
            'e.expense_bill_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function newsupdate()
    {
        $string = "news_updates.*";
        $table  = "news_updates";
        $order  = [
            "news_updates.news_id" => "desc"];
        $where = array(
            'news_updates.delete_status' => 0,
            'news_updates.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
            // 'news_updates.added_user_id' =>$this->ci->session->userdata('SESS_USER_ID')
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
        );
        return $data;
    }
    public function customer_suggestions_field($term)
    {
        $sql = 'SELECT * FROM customer where delete_status=0 and branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . '';
        return $sql;
    }
    public function database_product_exist($product_id, $product_type)
    {
        $sql = "SELECT distinct item_id FROM (
                    (SELECT item_id FROM advance_voucher_item inner join advance_voucher on advance_voucher.advance_voucher_id=advance_voucher_item.advance_voucher_id WHERE item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_order_item inner join purchase_order on purchase_order.purchase_order_id=purchase_order_item.purchase_order_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_item inner join purchase on purchase.purchase_id=purchase_item.purchase_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_return_item inner join purchase_return on purchase_return.purchase_return_id=purchase_return_item.purchase_return_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_credit_note_item inner join purchase_credit_note on purchase_credit_note.purchase_credit_note_id=purchase_credit_note_item.purchase_credit_note_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_debit_note_item inner join purchase_debit_note on purchase_debit_note.purchase_debit_note_id=purchase_debit_note_item.purchase_debit_note_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM quotation_item inner join quotation on quotation.quotation_id=quotation_item.quotation_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_item inner join sales on sales.sales_id=sales_item.sales_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_credit_note_item inner join sales_credit_note on sales_credit_note.sales_credit_note_id=sales_credit_note_item.sales_credit_note_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_debit_note_item inner join sales_debit_note on sales_debit_note.sales_debit_note_id=sales_debit_note_item.sales_debit_note_id WHERE  item_id =" . $product_id . " and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                     ) tab";
        return $sql;
    }

    public function database_product_exist_leathere($product_id, $product_type)
    {
        $sql = "SELECT distinct item_id FROM (
                    (SELECT item_id FROM advance_voucher_item inner join advance_voucher on advance_voucher.advance_voucher_id=advance_voucher_item.advance_voucher_id WHERE item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_order_item inner join purchase_order on purchase_order.purchase_order_id=purchase_order_item.purchase_order_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_item inner join purchase on purchase.purchase_id=purchase_item.purchase_id WHERE  item_id IN (" . $product_id . " ) and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_return_item inner join purchase_return on purchase_return.purchase_return_id=purchase_return_item.purchase_return_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_credit_note_item inner join purchase_credit_note on purchase_credit_note.purchase_credit_note_id=purchase_credit_note_item.purchase_credit_note_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_debit_note_item inner join purchase_debit_note on purchase_debit_note.purchase_debit_note_id=purchase_debit_note_item.purchase_debit_note_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM quotation_item inner join quotation on quotation.quotation_id=quotation_item.quotation_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_item inner join sales on sales.sales_id=sales_item.sales_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_credit_note_item inner join sales_credit_note on sales_credit_note.sales_credit_note_id=sales_credit_note_item.sales_credit_note_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_debit_note_item inner join sales_debit_note on sales_debit_note.sales_debit_note_id=sales_debit_note_item.sales_debit_note_id WHERE  item_id IN (" . $product_id . ") and item_type='" . $product_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                     ) tab";
        return $sql;
    }
        public function database_service_exist($service_id, $service_type)
    {
        $sql = "SELECT distinct item_id FROM (
                    (SELECT item_id FROM advance_voucher_item inner join advance_voucher on advance_voucher.advance_voucher_id=advance_voucher_item.advance_voucher_id WHERE item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_order_item inner join purchase_order on purchase_order.purchase_order_id=purchase_order_item.purchase_order_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_item inner join purchase on purchase.purchase_id=purchase_item.purchase_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_return_item inner join purchase_return on purchase_return.purchase_return_id=purchase_return_item.purchase_return_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_credit_note_item inner join purchase_credit_note on purchase_credit_note.purchase_credit_note_id=purchase_credit_note_item.purchase_credit_note_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM purchase_debit_note_item inner join purchase_debit_note on purchase_debit_note.purchase_debit_note_id=purchase_debit_note_item.purchase_debit_note_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM quotation_item inner join quotation on quotation.quotation_id=quotation_item.quotation_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_item inner join sales on sales.sales_id=sales_item.sales_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_credit_note_item inner join sales_credit_note on sales_credit_note.sales_credit_note_id=sales_credit_note_item.sales_credit_note_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                    UNION
                    (SELECT item_id FROM sales_debit_note_item inner join sales_debit_note on sales_debit_note.sales_debit_note_id=sales_debit_note_item.sales_debit_note_id WHERE  item_id =" . $service_id . " and item_type='" . $service_type . "' and branch_id=" . $this->ci->session->userdata('SESS_BRANCH_ID') . " LIMIT 1)
                     ) tab";
        return $sql;
    }
    public function distinct_customer()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_billing_to()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_shipping_to()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.ship_to_customer_id = c.customer_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.ship_to_customer_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_place_of_supply()
    {
        $string = "st.state_id,st.state_name, s.sales_billing_state_id, CASE when s.sales_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name";
        $table  = "sales s";
        $join   = [
            'states st' => "st.state_id = s.sales_billing_state_id" . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_billing_state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_type_of_supply()
    {
        $string = "s.sales_type_of_supply";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_type_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_gst_payable()
    {
        $string = "s.sales_gst_payable";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_gst_payable');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_taxable_value()
    {
        $string = "s.sales_taxable_value";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_taxable_value');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
     public function distinct_sales_cgst_amount()
    {
        $string = "s.sales_cgst_amount";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_sgst_amount()
    {
        $string = "s.sales_sgst_amount,st.is_utgst";
        $table  = "sales s";
        $join = [
            'states st'   => 'st.state_id = s.sales_billing_state_id' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            'st.is_utgst !=' => 1,
            's.delete_status'                => 0);
        $group = array(
            's.sales_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }public function distinct_sales_utgst_amount()
    {
        $string = "s.sales_sgst_amount,st.is_utgst";
        $table  = "sales s";
        $join = [
            'states st'   => 'st.state_id = s.sales_billing_state_id' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            'st.is_utgst' => 1,
            's.delete_status'                => 0);
        $group = array(
            's.sales_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_igst_amount()
    {
        $string = "s.sales_igst_amount";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_converted_grand_total()
    {
        $string = "s.converted_grand_total";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.converted_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function recipt_voucher_distinct_customer()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "customer c";
        $join   = [
            'receipt_voucher rv' => 'rv.party_id = c.customer_id and rv.party_type = "customer"'];
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array(
            'rv.party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_supplier()
    {
        $string = "s.supplier_id,s.supplier_name";
        $table  = "supplier s";
        $join   = [
            'purchase p' => 'p.purchase_party_id = s.supplier_id and p.purchase_party_type = "supplier"'];
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_supplier_expense_bill()
    {
        $string = "s.supplier_id,s.supplier_name";
        $table  = "expense_bill e";
        $join   = [
            'supplier s' => 'e.expense_bill_payee_id = s.supplier_id and e.expense_bill_payee_type = "supplier"'];
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.delete_status'                => 0);
        $group = array(
            's.supplier_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_supplier_payment_voucher()
    {
        $string = "s.supplier_id,s.supplier_name";
        $table  = "supplier s";
        $join   = [
            'payment_voucher p' => 'p.party_id = s.supplier_id and p.party_type = "supplier"'];
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.receipt_amount !=' => 0,
            'p.delete_status'                => 0);
        $group = array(
            'p.party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_invoice()
    {
        $string = "s.sales_id,s.sales_invoice_number";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_nature_of_supply()
    {
        $string = "s.sales_nature_of_supply";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.sales_nature_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_invoice()
    {
        $string = "p.purchase_id,p.purchase_invoice_number";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_supplier_invoice()
    {
        $string = "p.purchase_supplier_invoice_number";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_supplier_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }

     public function distinct_nature_of_supply_purchase()
    {
        $string = "p.purchase_nature_of_supply";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_nature_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_place_of_supply_purchase()
    {
        $string = "st.state_id,st.state_name,p.purchase_billing_state_id, CASE when p.purchase_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name";
        $table  = "purchase p";
        $join   =[
            "states st" => "p.purchase_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_billing_state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_gst_on_reverse_charge_purchase()
    {
        $string = "p.purchase_gst_payable";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_gst_payable');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_order_number()
    {
        $string = "p.purchase_order_number";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_order_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_delivery_challan_number()
    {
        $string = "p.purchase_delivery_challan_number";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_delivery_challan_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_e_way()
    {
        $string = "p.purchase_e_way_bill_number";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_e_way_bill_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_cgst()
    {
        $string = "p.purchase_cgst_amount";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_sgst()
    {
        $string = "p.purchase_sgst_amount";
        $table  = "purchase p";
        $join =[
        'states st'   => 'p.purchase_billing_state_id = st.state_id' . '#' . 'left'];
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'st.is_utgst !=' => 1,
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join' => $join
        );
        return $data;
    }
    public function distinct_purchase_utgst()
    {
        $string = "p.purchase_sgst_amount";
        $table  = "purchase p";
        $join =[
        'states st'   => 'p.purchase_billing_state_id = st.state_id' . '#' . 'left'];
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'st.is_utgst ' => 1,
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join' => $join
        );
        return $data;
    }
    public function distinct_purchase_igst()
    {
        $string = "p.purchase_igst_amount";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.delete_status'                => 0);
        $group = array(
            'p.purchase_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
     public function distinct_dn_supplier()
    {
        $string = "s.supplier_id,s.supplier_name";
        $table  = "purchase_debit_note pd";
        $join   = [
            "supplier s"   => "pd.purchase_debit_note_party_id = s.supplier_id".'#'.'left'];
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            's.supplier_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }

    public function distinct_purchase_debit_note_invoice()
    {
        $string = "pd.purchase_debit_note_id,pd.purchase_debit_note_invoice_number";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_expense_bill_invoice()
    {
        $string = "e.expense_bill_id,e.expense_bill_invoice_number";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.delete_status'                => 0);
        $group = array(
            'e.expense_bill_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_invoice_amount()
    {
        $string = "(s.converted_grand_total + s.converted_credit_note_amount - s.converted_debit_note_amount) as sales_invoice_amount";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            '(s.converted_grand_total + s.converted_credit_note_amount - s.converted_debit_note_amount)');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_invoice_amount()
    {
        $string = "(p.converted_grand_total + p.converted_credit_note_amount - p.converted_debit_note_amount) as purchase_invoice_amount";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.converted_grand_total !=' => 0,
            'p.delete_status'                => 0);
        $group = array(
            '(p.converted_grand_total + p.converted_credit_note_amount - p.converted_debit_note_amount)');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_invoice_amount()
    {
        $string = "(pd.purchase_debit_note_grand_total) as purchase_debit_note_invoice_amount";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_nature_supply()
    {
        $string = "pd.purchase_debit_note_nature_of_supply";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_nature_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_taxable_value()
    {
        $string = "pd.purchase_debit_note_taxable_value";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_taxable_value');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_tax_value()
    {
        $string = "pd.purchase_debit_note_cgst_amount,pd.purchase_debit_note_sgst_amount,pd.purchase_debit_note_igst_amount";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_igst_value()
    {
        $string = "pd.purchase_debit_note_igst_amount";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_cgst_value()
    {
        $string = "pd.purchase_debit_note_cgst_amount";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_sgst_value()
    {
        $string = "pd.purchase_debit_note_sgst_amount";
        $table  = "purchase_debit_note pd";
        $join = [
        'states st' => 'pd.purchase_debit_note_billing_state_id = st.state_id' . '#' . 'left'];
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'st.is_utgst !=' => 1,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'  =>$join
        );
        return $data;
    }
    public function distinct_purchase_debit_note_utgst_value()
    {
        $string = "pd.purchase_debit_note_sgst_amount";
        $table  = "purchase_debit_note pd";
        $join = [
        'states st' => 'pd.purchase_debit_note_billing_state_id = st.state_id' . '#' . 'left'];
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'st.is_utgst' => 1,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'  =>$join
        );
        return $data;
    }
    public function distinct_purchase_debit_note_amount()
    {
        $string = "pd.purchase_debit_note_grand_total";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_expense_bill_type()
    {
        $string = "ep.expense_id, ep.expense_title";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.delete_status'                => 0);
        $join =[
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id",
            "expense ep" =>"eb.expense_type_id = ep.expense_id"];
        $group = array(
            'ep.expense_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_expense_supplier_invoice_number()
    {
        $string = "e.expense_bill_supplier_invoice_number";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.delete_status'                => 0);
        $group = array(
            'e.expense_bill_supplier_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_expense_tds()
    {
        $string = "eb.expense_bill_item_tds_amount";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.delete_status'                => 0);
        $join =[
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id"];
        $group = array(
            'eb.expense_bill_item_tds_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_expense_cgst()
    {
        $string = "eb.expense_bill_item_cgst_amount";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.delete_status'                => 0);
        $join =[
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id"];
        $group = array(
            'eb.expense_bill_item_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_expense_sgst()
    {
        $string = "eb.expense_bill_item_sgst_amount";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'st.is_utgst !=' => 1,
            'e.delete_status'                => 0);
        $join =[
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id",
            "states st"  =>"e.expense_bill_billing_state_id = st.state_id"."#"."left"];
        $group = array(
            'eb.expense_bill_item_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_expense_utgst()
    {
        $string = "eb.expense_bill_item_sgst_amount";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'st.is_utgst' => 1,
            'e.delete_status'                => 0);
        $join =[
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id",
            "states st"  =>"e.expense_bill_billing_state_id = st.state_id"."#"."left"];
        $group = array(
            'eb.expense_bill_item_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_expense_igst()
    {
        $string = "eb.expense_bill_item_igst_amount";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.delete_status'                => 0);
        $join =[
            "expense_bill_item eb" =>"e.expense_bill_id = eb.expense_bill_id"];
        $group = array(
            'eb.expense_bill_item_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_sales_received_amount()
    {
        $string = "s.converted_paid_amount as sales_paid_amount";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.sales_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            's.converted_paid_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_received_amount()
    {
        $string = "p.converted_paid_amount as purchase_paid_amount";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.converted_grand_total !=' => 0,
            'p.delete_status'                => 0);
        $group = array(
            'p.converted_paid_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_purchase_reference()
    {
        $string = "p.purchase_invoice_number";
        $table  = "purchase_debit_note pd";
        $join   = [
            'purchase p' => 'p.purchase_id = pd.purchase_id'];
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'p.purchase_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_billing_country()
    {
        $string = "c.country_id, c.country_name";
        $table  = "purchase_debit_note pd";
        $join   = [
            'countries c' => 'c.country_id = pd.purchase_debit_note_billing_country_id'];
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'c.country_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_place_of_supply()
    {
        $string = "st.state_id, st.state_name, pd.purchase_debit_note_billing_state_id, CASE when pd.purchase_debit_note_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name";
        $table  = "purchase_debit_note pd";
        $join   = [
            'states st' => 'pd.purchase_debit_note_billing_state_id = st.state_id'.'#'.'left'];
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_billing_state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_debit_note_type_of_supply()
    {
        $string = "pd.purchase_debit_note_type_of_supply";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_debit_note_type_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_credit_note_number()
    {
        $string = "pd.purchase_supplier_debit_note_number";
        $table  = "purchase_debit_note pd";
        $where = array(
            'pd.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pd.delete_status'     => 0,
            'pd.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pd.purchase_supplier_debit_note_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            //'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_expense_bill_received_amount()
    {
        $string = "e.converted_paid_amount as expense_bill_paid_amount";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.currency_converted_amount !=' => 0,
            'e.delete_status'                => 0);
        $group = array(
            'e.converted_paid_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_pending_amount()
    {
        $string = "(s.converted_grand_total + s.converted_credit_note_amount - s.converted_debit_note_amount - s.converted_paid_amount) as sales_pending_amount";
        $table  = "sales s";
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.converted_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            '(s.converted_grand_total + s.converted_credit_note_amount - s.converted_debit_note_amount - s.converted_paid_amount)');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
//  function distinct_purchase_pending_amount()
// {
//         $string="(p.purchase_grand_total + p.credit_note_amount - p.debit_note_amount - p.purchase_paid_amount) as purchase_pending_amount";
//     $table="purchase p";
//     $where=array('p.branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'),'p.delete_status'=>0);
//     $group=array('(p.purchase_grand_total + p.credit_note_amount - p.debit_note_amount - p.purchase_paid_amount)');
//     $data = array('string' => $string,
//                 'table' => $table,
//                 'where' => $where,
//                 'group' => $group
//                 );
//     return  $data;
    // }
    public function distinct_expense_bill_pending_amount()
    {
        $string = "(e.currency_converted_amount - e.converted_paid_amount) as expense_bill_pending_amount";
        $table  = "expense_bill e";
        $where = array(
            'e.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'e.currency_converted_amount !=' => 0,
            'e.delete_status'                => 0);
        $group = array(
            '(e.currency_converted_amount - e.converted_paid_amount)');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_sales_due_day()
    {
        $string = "IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(s.sales_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP)) as due_day";
        $table  = "sales s";
        $join   = [
            'followup f' => 'f.type_id = s.sales_id AND f.type="sales"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.converted_grand_total !=' => 0,
            's.delete_status'                => 0);
        $group = array(
            'IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(s.sales_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP))');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_recipt_voucher_invoice(){
        $string = "s.sales_id,s.sales_invoice_number,rv.voucher_number,rv.receipt_id";
        $table  = "receipt_voucher rv";
        $join   = ["receipt_invoice_reference rr" => "rv.receipt_id = rr.receipt_id",
                    'sales s' => 's.sales_id = rr.reference_id'];
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array('rr.reference_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_recipt_voucher_invoice_number(){
        $string = "rv.voucher_number,rv.receipt_id";
        $table  = "receipt_voucher rv";
        $join   = ["receipt_invoice_reference rr" => "rv.receipt_id = rr.receipt_id"];
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array('rv.voucher_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_payment_voucher_invoice(){          

        $string = "pv.payment_id,pv.voucher_number, pv.reference_number, CASE pr.reference_type when 'purchase' Then p.purchase_invoice_number when 'expense' Then e.expense_bill_invoice_number 
             when 'excess_amount' Then 'Excess Amount' END as purchase_invoice_number, CASE pr.reference_type when 'purchase' Then p.purchase_grand_total when 'expense' Then e.supplier_receivable_amount   when 'excess_amount' Then 0 END as invoice_total, , CASE pr.reference_type when 'purchase' Then p.purchase_id when 'expense' Then e.expense_bill_id END as reference_id";
        $table  = "payment_voucher pv";
        $join   = ["payment_invoice_reference pr" => "pv.payment_id = pr.payment_id",
                     "purchase p" => 'p.purchase_id = pr.reference_id'. '#' .'left',
            "expense_bill e" => 'e.expense_bill_id = pr.reference_id'. '#' .'left',
                    'purchase p' => 'p.purchase_id = pr.reference_id'];
        
        $where = array(
            'pv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.receipt_amount !=' => 0,
            'pv.delete_status'                => 0);
        $group = array('pv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_payment_voucher_invoice_number(){          

        $string = "pv.payment_id,pv.voucher_number,CASE pr.reference_type when 'purchase' Then p.purchase_invoice_number when 'expense' Then e.expense_bill_invoice_number 
             when 'excess_amount' Then 'Excess Amount' END as purchase_invoice_number, CASE pr.reference_type when 'purchase' Then p.purchase_grand_total when 'expense' Then e.supplier_receivable_amount   when 'excess_amount' Then 0 END as invoice_total, , CASE pr.reference_type when 'purchase' Then p.purchase_id when 'expense' Then e.expense_bill_id END as reference_id";
        $table  = "payment_voucher pv";
        $join   = ["payment_invoice_reference pr" => "pv.payment_id = pr.payment_id",
                     "purchase p" => 'p.purchase_id = pr.reference_id'. '#' .'left',
            "expense_bill e" => 'e.expense_bill_id = pr.reference_id'. '#' .'left',
                    'purchase p' => 'p.purchase_id = pr.reference_id'];
        
        $where = array(
            'pv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.receipt_amount !=' => 0,
            'pv.delete_status'                => 0);
        $group = array('pv.voucher_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_billing_currency(){
        $string = "c.currency_id,c.currency_name";
        $table  = "payment_voucher pv";
        $join   = ["currency c" => "pv.currency_id = c.currency_id"];
        
        $where = array(
            'pv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.receipt_amount !=' => 0,
            'pv.delete_status'                => 0);
        $group = array('pv.currency_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_recipt_voucher_voucher_invoice()
    {
        $string = "rv.invoice_total";
        $table  = "receipt_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array(
            'rv.invoice_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_dn_customer()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "sales_debit_note dn";
        $join   = [
            'customer c' => 'c.customer_id = dn.sales_debit_note_party_id'];
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'c.customer_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'join'   => $join,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_cn_customer()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "sales_credit_note cr";
        $join   = [
            'customer c' => 'c.customer_id = cr.sales_credit_note_party_id'];
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'c.customer_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'join'   => $join,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_credit_note_invoice()
    {
        $string = "cr.sales_credit_note_invoice_number";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'cr.sales_credit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_receipt_voucher_amount()
    {
        $string = "rv.receipt_amount as receipt_grand_total";
        $table  = "receipt_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array(
            'rv.receipt_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_to_account()
    {
        $string = "rv.to_account";
        $table  = "receipt_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array(
            'rv.to_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_from_account()
    {
        $string = "rv.from_account";
        $table  = "receipt_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array('rv.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_payment_voucher_to_account(){
        $string = "pv.to_account";
        $table  = "payment_voucher pv";
        $where = array(
            'pv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.receipt_amount !=' => 0,
            'pv.delete_status'                => 0);
        $group = array(
            'pv.to_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_payment_voucher_from_account(){
        $string = "pv.from_account";
        $table  = "payment_voucher pv";
        $where = array(
            'pv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.receipt_amount !=' => 0,
            'pv.delete_status'                => 0);
        $group = array(
            'pv.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_payment_voucher_paid_amount()
    {
        $string = "pv.receipt_amount";
        $table  = "payment_voucher pv";
        $where = array(
            'pv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.delete_status'     => 0);
        $group = array(
            'pv.receipt_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_payment_voucher_total_paid_amount()
    {
        $string = "pv.invoice_paid_amount";
        $table  = "payment_voucher pv";
        $where = array(
            'pv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.delete_status'     => 0);
        $group = array(
            'pv.invoice_paid_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_payment_voucher_voucher_type()
    {
        $string = "pv.reference_type";
        $table  = "payment_voucher pv";
        $join = [
            'payment_invoice_reference pr' => "pv.payment_id = pr.payment_id"];
        $where = array(
            'pv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'pv.delete_status'     => 0);
        $group = array(
            'pv.reference_type');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_amount()
    {
        $string = "cr.sales_credit_note_grand_total";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0);
        $group = array(
            'cr.sales_credit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_debit_note_amount()
    {
        $string = "dn.sales_debit_note_grand_total";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0);
        $group = array(
            'dn.sales_debit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_credit_note_reference_number()
    {
        $string = "s.sales_invoice_number";
        $table  = "sales_credit_note cr";
        $join   = [
            'sales s' => 'cr.sales_id = s.sales_id'.'#'.'left'];
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            's.sales_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_nature_of_supply()
    {
        $string = "cr.sales_credit_note_nature_of_supply";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
        'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'cr.sales_credit_note_nature_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_debit_note_reference_number()
    {
        $string = "s.sales_invoice_number";
        $table  = "sales s";
        $join   = [
            'sales_debit_note cr' => 's.sales_id = cr.sales_id'];
        $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0,
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            's.sales_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_credit_note_invoice_number()
    {
        $string = "pr.purchase_credit_note_invoice_number";
        $table  = "purchase_credit_note pr";
        $where = array(
            'pr.branch_id'                       => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pr.delete_status'                   => 0,
            'pr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pr.purchase_credit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_credit_note_invoice_amount()
    {
        $string = "pr.purchase_credit_note_grand_total";
        $table  = "purchase_credit_note pr";
        $where = array(
            'pr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pr.delete_status' => 0);
        $group = array(
            'pr.purchase_credit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_credit_nature_of_supply()
    {
        $string = "pr.purchase_credit_note_nature_of_supply";
        $table  = "purchase_credit_note pr";
        $where = array(
            'pr.branch_id'                       => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pr.delete_status'                   => 0,
            'pr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pr.purchase_credit_note_nature_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_credit_type_of_supply()
    {
        $string = "pr.purchase_credit_note_type_of_supply";
        $table  = "purchase_credit_note pr";
        $where = array(
            'pr.branch_id'                       => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pr.delete_status'                   => 0,
            'pr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pr.purchase_credit_note_type_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_credit_supplier_dn_number()
    {
        $string = "pr.purchase_supplier_credit_note_number";
        $table  = "purchase_credit_note pr";
        $where = array(
            'pr.branch_id'                       => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pr.delete_status'                   => 0,
            'pr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pr.purchase_supplier_credit_note_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_credit_note_reference_number()
    {
        $string = "p.purchase_invoice_number,p.purchase_id";
        $table  = "purchase p";
        $join   = [
            'purchase_credit_note pr' => 'p.purchase_id = pr.purchase_id'];
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0,
            'p.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'p.purchase_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_credit_note_billing_country()
    {
        $string = "con.country_name,con.country_id";
        $table  = "purchase_credit_note p";
        $join   = [
            'countries con' => 'p.purchase_credit_note_billing_country_id = con.country_id'];
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0,
            'p.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'con.country_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function purchase_credit_note_place_of_supply()
    {
        $string = "st.state_name,st.state_id,p.purchase_credit_note_billing_state_id, CASE when p.purchase_credit_note_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name";
        $table  = "purchase_credit_note p";
        $join   = [
            "states st"    => "p.purchase_credit_note_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0,
            'p.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'p.purchase_credit_note_billing_state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_purchase_pending_amount()
    {
        $string = "(p.converted_grand_total + p.converted_credit_note_amount - p.converted_debit_note_amount - p.converted_paid_amount) as purchase_pending_amount";
        $table  = "purchase p";
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.converted_grand_total !=' => 0,
            'p.delete_status'                => 0);
        $group = array(
            '(p.converted_grand_total + p.converted_credit_note_amount - p.converted_debit_note_amount - p.converted_paid_amount)');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_due_day()
    {
        $string = "IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(p.purchase_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP)) as due_day";
        $table  = "purchase p";
        $join   = [
            'followup f' => 'f.type_id = p.purchase_id AND f.type="purchase"' . '#' . 'left'];
        $where = array(
            'p.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'p.converted_grand_total !=' => 0,
            'p.delete_status'                => 0);
        $group = array(
            'IFNULL(DATEDIFF(f.date, CURRENT_TIMESTAMP), DATEDIFF(DATE_ADD(p.purchase_date,INTERVAL 15 DAY), CURRENT_TIMESTAMP))');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_from_customer_credit_note()
    {
        $string = "from_account";
        $table  = 'sales_credit_note c';
        $where  = array(
            'c.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'c.delete_status' => 0);
        $group = array(
            'c.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            // 'join' => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_taxable_customer_credit_note()
    {
        $string = "c.sales_credit_note_taxable_value,c.sales_credit_note_igst_amount,c.sales_credit_note_cgst_amount,c.sales_credit_note_sgst_amount";
        $table  = 'sales_credit_note c';
        $where  = array(
            'c.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'c.delete_status' => 0);
        // $group=array('c.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
// 'join' => $join,
            // 'group' => $group
        );
        return $data;
    }
    public function distinct_taxable_customer_debit_note()
    {
        $string = "d.sales_debit_note_taxable_value,d.sales_debit_note_igst_amount,d.sales_debit_note_cgst_amount,d.sales_debit_note_sgst_amount";
        $table  = 'sales_debit_note d';
        $where  = array(
            'd.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'd.delete_status' => 0);
        // $group=array('c.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where
// 'join' => $join,
            // 'group' => $group
        );
        return $data;
    }
    public function distinct_debit_note_invoice()
    {
        $string = "dn.sales_debit_note_invoice_number";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'dn.sales_debit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_debit_note_reference()
    {
        $string = "s.sales_invoice_number";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            'sales s' => ' dn.sales_id = s.sales_id'];
        $group = array(
            's.sales_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
     public function distinct_debit_nature_of_supply()
    {
        $string = "dn.sales_debit_note_nature_of_supply";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        
        $group = array(
            'dn.sales_debit_note_nature_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
        );
        return $data;
    }
    public function distinct_debit_billing_country()
    {
        $string = "con.country_id,con.country_name";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "countries con"=> "dn.sales_debit_note_billing_country_id = con.country_id".'#'.'left',];
        $group = array(
            'con.country_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_billing_country()
    {
        $string = "con.country_id,con.country_name";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
        'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "countries con"=> "cr.sales_credit_note_billing_country_id = con.country_id".'#'.'left',];
        $group = array(
            'con.country_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_debit_place_of_supply()
    {
        $string = "st.state_id,st.state_name,dn.sales_debit_note_billing_state_id, CASE when dn.sales_debit_note_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "states st" => "dn.sales_debit_note_billing_state_id = st.state_id".'#'.'left',];
        $group = array(
            'st.state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_place_of_supply()
    {
        $string = "st.state_id,st.state_name, cr.sales_credit_note_billing_state_id, CASE when cr.sales_credit_note_billing_state_id = 0 then 'Outside India' ELSE st.state_name  END as state_name";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
        'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "states st" => "cr.sales_credit_note_billing_state_id = st.state_id".'#'.'left',];
        $group = array(
            'cr.sales_credit_note_billing_state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
     public function distinct_debit_type_of_supply()
    {
        $string = "dn.sales_debit_note_type_of_supply";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'dn.sales_debit_note_type_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            //'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_type_of_supply()
    {
        $string = "cr.sales_credit_note_type_of_supply";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
        'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'cr.sales_credit_note_type_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            //'join'   => $join
        );
        return $data;
    }
    public function distinct_debit_bill_to_name()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "customer c"   => "dn.sales_debit_note_party_id = c.customer_id",];
        $group = array(
            'c.customer_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_debit_ship_to_name()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "customer c"   => "dn.sales_debit_note_ship_to_customer_id = c.customer_id",];
        $group = array(
            'c.customer_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_bill_to_name()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "customer c"   => "cr.sales_credit_note_party_id = c.customer_id",];
        $group = array(
            'c.customer_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_ship_to_name()
    {
        $string = "c.customer_id,c.customer_name";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            "customer c"   => "cr.sales_credit_note_ship_to_customer_id = c.customer_id",];
        $group = array(
            'c.customer_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
     public function distinct_debit_billing_currency()
    {
        $string = "cur.currency_id,cur.currency_name";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            'currency cur' => 'dn.currency_id = cur.currency_id',];
        $group = array(
            'cur.currency_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_billing_currency()
    {
        $string = "cur.currency_id,cur.currency_name";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $join   = [
            'currency cur' => 'cr.currency_id = cur.currency_id'.'#'.'left',];
        $group = array(
            'cur.currency_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_debit_cgst()
    {
        $string = "dn.sales_debit_note_cgst_amount";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'dn.sales_debit_note_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            //'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_cgst()
    {
        $string = "cr.sales_credit_note_cgst_amount";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'cr.sales_credit_note_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            //'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_sgst()
    {
        $string = "cr.sales_credit_note_sgst_amount";
        $table  = "sales_credit_note cr";
        $join  = [
        "states st"    => "cr.sales_credit_note_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'st.is_utgst !=' => 1,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'cr.sales_credit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_utgst()
    {
        $string = "cr.sales_credit_note_sgst_amount";
        $table  = "sales_credit_note cr";
        $join  = [
        "states st"    => "cr.sales_credit_note_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'st.is_utgst ' => 1,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'cr.sales_credit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_credit_note_igst()
    {
        $string = "cr.sales_credit_note_igst_amount";
        $table  = "sales_credit_note cr";
        $where = array(
            'cr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.delete_status' => 0,
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'cr.sales_credit_note_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            //'join'   => $join
        );
        return $data;
    }
    public function distinct_debit_sgst()
    {
        $string = "dn.sales_debit_note_sgst_amount, st.is_utgst";
        $table  = "sales_debit_note dn";
        $join =[
            "states st" => "dn.sales_debit_note_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'st.is_utgst !=' => 1,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'dn.sales_debit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_debit_utgst()
    {
        $string = "dn.sales_debit_note_sgst_amount, st.is_utgst";
        $table  = "sales_debit_note dn";
        $join =[
            "states st" => "dn.sales_debit_note_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'st.is_utgst' => 1,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'dn.sales_debit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_debit_igst()
    {
        $string = "dn.sales_debit_note_igst_amount";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0,
            'dn.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'dn.sales_debit_note_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            //'join'   => $join
        );
        return $data;
    }
    public function pyment_to_debit_note()
    {
        $string = "dn.from_account";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0);
        $group = array(
            'dn.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function pyment_to_debit_note_type_of_supply()
    {
        $string = "dn.sales_debit_note_type_of_supply";
        $table  = "sales_debit_note dn";
        $where = array(
            'dn.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dn.delete_status' => 0);
        $group = array(
            'dn.sales_debit_note_type_of_supply');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_advanced_voucher_list()
    {
        $string = "c.customer_name,c.customer_id";
        $table  = "advance_voucher av";
        $join   = [
            'customer c' => 'c.customer_id = av.party_id'];
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        $group = array(
            'av.party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_advanced_voucher_invoice()
    {
        $string = "av.voucher_number,av.advance_voucher_id";
        $table  = "advance_voucher av";
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'av.voucher_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_advanced_voucher_country()
    {
        $string = "c.country_name,c.country_id";
        $table  = "advance_voucher av";
        $join   = [
            'countries c' => 'c.country_id = av.billing_country_id'];
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        $group = array(
            'av.billing_country_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }


    public function distinct_gst_place_of_supply_advance(){
        $string = "av.billing_state_id, CASE when av.billing_state_id = 0 then 'Outside India' ELSE ST.state_name  END as state_name";
        $table  = "advance_voucher av";
        $join   = ['states ST'   => 'ST.state_id = av.billing_state_id' . '#' . 'left'];
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        $group = array('av.billing_state_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    

    public function distinct_advance_voucher_currency(){
        $string = "c.currency_name,av.currency_id";
        $table  = "advance_voucher av";
        $join   = [
            'currency c' => 'c.currency_id = av.currency_id'];
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        $group = array('av.currency_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );

        return $data;

    }

    public function distinct_advanced_voucher_invoice_amount(){
        $string = "av.receipt_amount as receipt_amount";
        $table  = "advance_voucher av";
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        $group = array(
            'av.receipt_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_advanced_voucher_from_account()
    {
        $string = "av.from_account";
        $table  = "advance_voucher av";
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'av.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_advanced_voucher_to_account()
    {
        $string = "av.to_account";
        $table  = "advance_voucher av";
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'av.to_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_advanced_voucher_taxable()
    {
        $string = "av.voucher_tax_amount";
        $table  = "advance_voucher av";
        $where = array(
            'av.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'av.receipt_amount !=' => 0,
            'av.delete_status'                => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'av.voucher_tax_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_advanced_voucher_cgst()
    {
        $string = "av.voucher_cgst_amount";
        $table  = "advance_voucher av";
        $where = array(
            'av.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.delete_status' => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'av.voucher_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_advanced_voucher_sgst()
    {
        $string = "av.voucher_sgst_amount";
        $table  = "advance_voucher av";
        $join =[
            'states st' => 'av.billing_state_id = st.state_id ' . '#' . 'left'];
        $where = array(
            'av.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.delete_status' => 0,
            'st.is_utgst !=' => 1,
            'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'av.voucher_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'  => $join
        );
        return $data;
    }
    public function distinct_advanced_voucher_utgst()
    {
        $string = "av.voucher_sgst_amount";
        $table  = "advance_voucher av";
        $join =[
            'states st' => 'av.billing_state_id = st.state_id ' . '#' . 'left'];
        $where = array(
            'av.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.delete_status' => 0,
            'st.is_utgst ' => 1,
            'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'av.voucher_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'  => $join
        );
        return $data;
    }
    public function distinct_advanced_voucher_igst()
    {
        $string = "av.voucher_igst_amount";
        $table  = "advance_voucher av";
        $where = array(
            'av.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.delete_status' => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'av.voucher_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_refund_voucher_list()
    {
        $string = "c.customer_name,c.customer_id";
        $table  = "refund_voucher rv";
        $join   = [
            'customer c' => 'c.customer_id = rv.party_id'];
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array(
            'rv.party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }
    public function distinct_refund_voucher_invoice()
    {
        $string = "rv.voucher_number,rv.refund_id";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'rv.voucher_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_refund_voucher_currency()
    {
        $string = "c.currency_name,rv.currency_id";
        $table  = "refund_voucher rv";
        $join   = [
            'currency c' => 'c.currency_id = rv.currency_id'];
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array('rv.currency_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );

        return $data;

    }



    public function distinct_refund_voucher_country(){

        $string = "c.country_name,c.country_id";
        $table  = "refund_voucher rv";
        $join   = [
            'countries c' => 'c.country_id = rv.billing_country_id'];
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array(
            'rv.billing_country_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'   => $join
        );
        return $data;
    }

     public function distinct_gst_place_of_supply_refund(){
        $string = "rv.billing_state_id, CASE when rv.billing_state_id = 0 then 'Outside India' ELSE ST.state_name  END as state_name";
        $table  = "refund_voucher rv";
        $join   = ['states ST'   => 'ST.state_id = rv.billing_state_id' . '#' . 'left'];
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array('rv.billing_state_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_refund_voucher_invoice_amount()
    {
        $string = "rv.receipt_amount as receipt_amount";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        $group = array(
            'rv.receipt_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_refund_voucher_reference_invoice_amount()
    {
        $string = "rv.reference_number,rv.reference_id";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.delete_status'                => 0
            ,
            'rv.receipt_amount !=' => 0,
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'rv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_refund_voucher_from_account()
    {
        $string = "rv.from_account";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'rv.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_refund_voucher_to_account()
    {
        $string = "rv.to_account";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'rv.receipt_amount !=' => 0,
            'rv.delete_status'                => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'rv.to_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_refund_voucher_taxable()
    {
        $string = "rv.voucher_tax_amount";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.delete_status' => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'rv.voucher_tax_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_recipt_voucher_cgst()
    {
        $string = "rv.voucher_cgst_amount";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.delete_status' => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'rv.voucher_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_recipt_voucher_sgst()
    {
        $string = "rv.voucher_sgst_amount";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.delete_status' => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'rv.voucher_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_recipt_voucher_utgst()
    {
        $string = "rv.voucher_sgst_amount";
        $table  = "refund_voucher rv";
        $join = [
            "states st"     => 'rv.billing_state_id = st.state_id'. " # " . 'left'];
        $where = array(
            'rv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.delete_status' => 0,
            'st.is_utgst ' => 1,
            'rv.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'rv.voucher_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join' =>$join
        );
        return $data;
    }
    public function distinct_recipt_voucher_igst()
    {
        $string = "rv.voucher_igst_amount";
        $table  = "refund_voucher rv";
        $where = array(
            'rv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.delete_status' => 0);
        // ,'av.financial_year_id'=>$this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $group = array(
            'rv.voucher_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_supplier_purchase_credit_note()
    {
        $string = "s.supplier_id,s.supplier_name";
        $table  = "supplier s";
        $join   = [
            'purchase_credit_note p' => 'p.purchase_credit_note_party_id = s.supplier_id and p.purchase_credit_note_party_type = "supplier"'];
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0);
        $group = array(
            'p.purchase_credit_note_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_supplier_purchase_credit_note_name()
    {
        $string = "s.supplier_id,s.supplier_name";
        $table  = "purchase_credit_note p";
        $join   = [
            'supplier s' => 'p.purchase_credit_note_party_id = s.supplier_id and p.purchase_credit_note_party_type = "supplier"'];
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0,
            'p.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            's.supplier_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function purchase_credit_note_paid_to()
    {
        $string = "p.from_account";
        $table  = "purchase_credit_note p";
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0);
        $group = array(
            'p.from_account');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            // 'join' => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_credit_note_cgst()
    {
        $string = "pc.purchase_credit_note_cgst_amount";
        $table  = "purchase_credit_note pc";
        $where = array(
            'pc.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pc.delete_status' => 0,
            'pc.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pc.purchase_credit_note_cgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_purchase_credit_note_sgst()
    {
        $string = "pc.purchase_credit_note_sgst_amount";
        $table  = "purchase_credit_note pc";
        $join = [
        "states st"    => "pc.purchase_credit_note_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'pc.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pc.delete_status' => 0,
            'st.is_utgst !=' => 1,
            'pc.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pc.purchase_credit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'  => $join
        );
        return $data;
    }
    public function distinct_purchase_credit_note_utgst()
    {
        $string = "pc.purchase_credit_note_sgst_amount";
        $table  = "purchase_credit_note pc";
        $join = [
        "states st"    => "pc.purchase_credit_note_billing_state_id = st.state_id".'#'.'left'];
        $where = array(
            'pc.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pc.delete_status' => 0,
            'st.is_utgst' => 1,
            'pc.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pc.purchase_credit_note_sgst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group,
            'join'  => $join
        );
        return $data;
    }
    public function distinct_purchase_credit_note_igst()
    {
        $string = "pc.purchase_credit_note_igst_amount";
        $table  = "purchase_credit_note pc";
        $where = array(
            'pc.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'pc.delete_status' => 0,
            'pc.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'));
        $group = array(
            'pc.purchase_credit_note_igst_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_currency_converted_records($type)
    {
        if ($type == 'sales')
        {
            $string = "c.customer_id,c.customer_name";
            $table  = "customer c";
            $join   = [
                'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"'];
            $where = array(
                's.branch_id'                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
                's.financial_year_id'         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                's.converted_grand_total' => 0,
                's.delete_status'             => 0);
            $group = array(
                's.sales_party_id');
            $data = array(
                'string' => $string,
                'table'  => $table,
                'where'  => $where,
                'join'   => $join,
                'group'  => $group
            );
            return $data;
        }
        elseif ($type == 'purchase')
        {
            $string = "s.supplier_id,s.supplier_name";
            $table  = "supplier s";
            $join   = [
                'purchase p' => 'p.purchase_party_id = s.supplier_id and p.purchase_party_type = "supplier"'];
            $where = array(
                'p.branch_id'                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
                'p.financial_year_id'         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'p.converted_grand_total' => 0,
                'p.delete_status'             => 0);
            $group = array(
                'p.purchase_party_id');
            $data = array(
                'string' => $string,
                'table'  => $table,
                'where'  => $where,
                'join'   => $join,
                'group'  => $group
            );
            return $data;
        }
        elseif ($type == 'expense_bill')
        {
            $string = "s.supplier_id,s.supplier_name";
            $table  = "supplier s";
            $join   = [
                'expense_bill e' => 'e.expense_bill_payee_id = s.supplier_id and e.expense_bill_payee_type = "supplier"'];
            $where = array(
                'e.branch_id'                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
                'e.financial_year_id'         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'e.currency_converted_amount' => 0,
                'e.delete_status'             => 0);
            $group = array(
                'e.expense_bill_payee_id');
            $data = array(
                'string' => $string,
                'table'  => $table,
                'where'  => $where,
                'join'   => $join,
                'group'  => $group
            );
            return $data;
        }
        elseif ($type == 'advance_voucher')
        {
            $string = "c.customer_name,c.customer_id";
            $table  = "advance_voucher av";
            $join   = [
                'customer c' => 'c.customer_id = av.party_id'];
            $where = array(
                'av.branch_id'                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
                'av.financial_year_id'         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'av.receipt_amount' => 0,
                'av.delete_status'             => 0);
            $group = array(
                'av.party_id');
            $data = array(
                'string' => $string,
                'table'  => $table,
                'where'  => $where,
                'group'  => $group,
                'join'   => $join
            );
            return $data;
        }
        elseif ($type == 'refund_voucher')
        {
            $string = "c.customer_name,c.customer_id";
            $table  = "refund_voucher rv";
            $join   = [
                'customer c' => 'c.customer_id = rv.party_id'];
            $where = array(
                'rv.branch_id'                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
                'rv.financial_year_id'         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'rv.receipt_amount' => 0,
                'rv.delete_status'             => 0);
            $group = array(
                'rv.party_id');
            $data = array(
                'string' => $string,
                'table'  => $table,
                'where'  => $where,
                'group'  => $group,
                'join'   => $join
            );
            return $data;
        }
        elseif ($type == 'receipt_voucher')
        {
            $string = "c.customer_id,c.customer_name";
            $table  = "customer c";
            $join   = [
                'receipt_voucher rv' => 'rv.party_id = c.customer_id and rv.party_type = "customer"'];
            $where = array(
                'rv.branch_id'                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
                'rv.financial_year_id'         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'rv.receipt_amount' => 0,
                'rv.delete_status'             => 0);
            $group = array(
                'rv.party_id');
            $data = array(
                'string' => $string,
                'table'  => $table,
                'where'  => $where,
                'join'   => $join,
                'group'  => $group
            );
            return $data;
        }
        elseif ($type == 'payment_voucher')
        {
            $string = "s.supplier_id,s.supplier_name";
            $table  = "supplier s";
            $join   = [
                'payment_voucher p' => 'p.party_id = s.supplier_id and p.party_type = "supplier"'];
            $where = array(
                'p.branch_id'                 => $this->ci->session->userdata('SESS_BRANCH_ID'),
                'p.financial_year_id'         => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'p.receipt_amount' => 0,
                'p.delete_status'             => 0);
            $group = array(
                'p.party_id');
            $data = array(
                'string' => $string,
                'table'  => $table,
                'where'  => $where,
                'join'   => $join,
                'group'  => $group
            );
            return $data;
        }
    }
    public function purchase_credit_note_grand_total()
    {
        $string = "sum(cr.purchase_credit_note_grand_total) as total ,sum(cr.purchase_credit_note_taxable_value) as taxable_total,sum(cr.purchase_credit_note_cgst_amount) as total_cgst,sum(cr.purchase_credit_note_sgst_amount) as total_sgst,,sum(cr.purchase_credit_note_igst_amount) as total_igst";
        $table  = "purchase_credit_note cr";
        $join   = [
            'currency cur' => 'cr.currency_id = cur.currency_id',
            "purchase p"   => "p.purchase_id = cr.purchase_id",
            "supplier s"   => "cr.purchase_credit_note_party_id = s.supplier_id",
            "users u"      => "cr.added_user_id = u.id"];
        // $order =["cr.purchase_credit_note_id"=>"desc"];
        $where = array(
            'cr.delete_status'     => 0,
            'cr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'cr.credit_note_invoice_number',
            'cr.credit_note_date',
            'cr.sales_credit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
// 'filter' => $filter,
            // 'order' => $order
        );
        return $data;
    }
    public function sum_sales()
    {
        $string = "sum(s.sales_grand_total) as sum_of_grand_total, sum(s.sales_paid_amount) as sales_paid_amount, sum(s.sales_grand_total+s.credit_note_amount - s.sales_paid_amount-s.debit_note_amount) as pending_amount";
        $table  = "sales s";
        $order  = [
            "s.sales_id" => "desc"];
        $where = array(
            's.delete_status'     => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $join = [
            'currency cur' => 's.currency_id = cur.currency_id',
            'customer c'   => 's.sales_party_id = c.customer_id',
            'users u'      => 's.added_user_id = u.id',
            'followup f'   => 'f.type_id = s.sales_id AND f.type="sales"' . '#' . 'left'];
        $filter = array(
            'c.customer_name',
            's.sales_invoice_number',
            's.sales_date',
            's.sales_grand_total');
        // $group = array('s.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
            // 'group' => $group
        );
        return $data;
    }
    public function sum_purchase()
    {
        $string = "sum(s.purchase_grand_total) as sum_of_grand_total, sum(s.purchase_paid_amount) as paid_amount, sum(s.purchase_grand_total+s.credit_note_amount - s.purchase_paid_amount-s.debit_note_amount) as pending_amount";
        $table  = "purchase s";
        $order  = [
            "s.purchase_id" => "desc"];
        $where = array(
            's.delete_status'     => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $join = [
            'currency cur' => 's.currency_id = cur.currency_id',
            'customer c'   => 's.purchase_party_id = c.customer_id',
            'users u'      => 's.added_user_id = u.id',
            'followup f'   => 'f.type_id = s.purchase_id AND f.type="purchase"' . '#' . 'left'];
        $filter = array(
            'c.customer_name',
            's.purchase_invoice_number',
            's.purchase_date',
            's.purchase_grand_total');
        // $group = array('s.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
            // 'group' => $group
        );
        return $data;
    }
    public function sum_expense_bill()
    {
        $string = "sum(e.expense_bill_grand_total) as sum_of_grand_total, sum(e.expense_bill_paid_amount) as paid_amount, sum(e.expense_bill_grand_total- e.expense_bill_paid_amount) as pending_amount";
        $table = "expense_bill e";
        $join  = [
            'currency cur' => 'e.currency_id = cur.currency_id',
            "supplier s"   => "e.expense_bill_payee_id=s.supplier_id",
            "users u"      => "u.id = e.added_user_id",
            'followup f'   => 'f.type_id = e.expense_bill_id AND f.type="expense_bill"' . '#' . 'left'];
        $order = [
            "e.expense_bill_id" => "desc"];
        $where = array(
            'e.delete_status'     => 0,
            'e.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'e.purchase_invoice_number',
            'e.purchase_date',
            'e.purchase_grand_total');
        // $group = array('e.expense_bill_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
            // 'group' => $group
        );
        return $data;
    }
    public function credit_note_sum()
    {
        $string = "sum(cr.sales_credit_note_grand_total) as grand_total, sum(cr.sales_credit_note_taxable_value) as taxable_value,sum(cr.sales_credit_note_igst_amount) as igst_sum,sum(cr.sales_credit_note_cgst_amount) as cgst_sum,sum(cr.sales_credit_note_sgst_amount) as sgst_sum  ";
        $table  = "sales_credit_note cr";
        $join   = [
            'currency cur' => 'cr.currency_id = cur.currency_id',
            "sales s"      => "s.sales_id = cr.sales_id",
            "customer c"   => "cr.sales_credit_note_party_id = c.customer_id",
            "users u"      => "cr.added_user_id = u.id"];
        // $order =["cr.sales_credit_note_id"=>"desc"];
        $where = array(
            'cr.delete_status'     => 0,
            'cr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'cr.sales_credit_note_invoice_number',
            'cr.sales_credit_note_date',
            'cr.sales_credit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter
            // 'order' => $order
        );
        return $data;
    }
    public function debit_note_sum()
    {
        $string = "sum(dr.sales_debit_note_grand_total) as grand_total, sum(dr.sales_debit_note_taxable_value) as taxable_value,sum(dr.sales_debit_note_igst_amount) as igst_sum,sum(dr.sales_debit_note_cgst_amount) as cgst_sum,sum(dr.sales_debit_note_sgst_amount) as sgst_sum  ";
        $table  = "sales_debit_note dr";
        $join   = [
            'currency cur' => 'dr.currency_id = cur.currency_id',
            "sales s"      => "s.sales_id = dr.sales_id",
            "customer c"   => "dr.sales_debit_note_party_id = c.customer_id",
            "users u"      => "dr.added_user_id = u.id"];
        // $order =["dr.sales_debit_note_id"=>"desc"];
        $where = array(
            'dr.delete_status'     => 0,
            'dr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'dr.sales_debit_note_invoice_number',
            'dr.sales_debit_note_date',
            'dr.sales_debit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter
            // 'order' => $order
        );
        return $data;
    }
    public function purchase_credit_sum()
    {
        $string = "sum(cr.purchase_credit_note_grand_total) as grand_total, sum(cr.purchase_credit_note_taxable_value) as taxable_value,sum(cr.purchase_credit_note_igst_amount) as igst_sum,sum(cr.purchase_credit_note_cgst_amount) as cgst_sum,sum(cr.purchase_credit_note_sgst_amount) as sgst_sum  ";
        $table  = "purchase_credit_note cr";
        $join   = [
            'currency cur' => 'cr.currency_id = cur.currency_id',
            "purchase p"   => "p.purchase_id = cr.purchase_id",
            "supplier s"   => "cr.purchase_credit_note_party_id = s.supplier_id",
            "users u"      => "cr.added_user_id = u.id"];
        $order = [
            "cr.purchase_credit_note_id" => "desc"];
        $where = array(
            'cr.delete_status'     => 0,
            'cr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'cr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'cr.credit_note_invoice_number',
            'cr.credit_note_date',
            'cr.sales_credit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function purchase_debit_sum()
    {
        $string = "sum(dr.purchase_debit_note_grand_total) as grand_total, sum(dr.purchase_debit_note_taxable_value) as taxable_value,sum(dr.purchase_debit_note_igst_amount) as igst_sum,sum(dr.purchase_debit_note_cgst_amount) as cgst_sum,sum(dr.purchase_debit_note_sgst_amount) as sgst_sum  ";
        $table  = "purchase_debit_note dr";
        $join   = [
            'currency cur' => 'dr.currency_id = cur.currency_id',
            "purchase p"   => "p.purchase_id = dr.purchase_id",
            "supplier s"   => "dr.purchase_debit_note_party_id = s.supplier_id",
            "countries c"  => "c.country_id = dr.purchase_debit_note_billing_country_id",
            "users u"      => "dr.added_user_id = u.id"];
        $order = [
            "dr.purchase_debit_note_id" => "desc"];
        $where = array(
            'dr.delete_status'     => 0,
            'dr.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'dr.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'dr.debit_note_invoice_number',
            'dr.debit_note_date',
            'dr.debit_note_grand_total');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function advance_voucher_sum()
    {
        $string = "sum(av.receipt_amount) as grand_total, sum(av.voucher_tax_amount) as taxable_value,sum(av.voucher_igst_amount) as igst_sum,sum(av.voucher_cgst_amount) as cgst_sum,sum(av.voucher_sgst_amount) as sgst_sum  ";
        $table = "advance_voucher av";
        $join  = [
            'currency cur'  => 'av.currency_id = cur.currency_id',
            "customer c"    => "av.party_id = c.customer_id",
            "users u"       => "av.added_user_id = u.id",
            'countries con' => 'con.country_id = av.billing_country_id'];
        $order = [
            "av.advance_voucher_id" => "desc"];
        $where = array(
            'av.delete_status'     => 0,
            'av.refund_status'     => 0,
            'av.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'av.voucher_number',
            'av.voucher_date',
            'av.receipt_amount',
            'av.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function refund_voucher_sum()
    {
        $string = "sum(rv.receipt_amount) as grand_total, sum(rv.voucher_tax_amount) as taxable_value,sum(rv.voucher_igst_amount) as igst_sum,sum(rv.voucher_cgst_amount) as cgst_sum,sum(rv.voucher_sgst_amount) as sgst_sum";
        $table  = "refund_voucher rv";
        $join   = [
            'currency cur'  => 'rv.currency_id = cur.currency_id',
            "customer c"    => "rv.party_id = c.customer_id",
            "users u"       => "rv.added_user_id = u.id",
            'countries con' => 'con.country_id = rv.billing_country_id'];
        $order = [
            "rv.refund_id" => "desc"];
        $where = array(
            'rv.delete_status'     => 0,
            'rv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'rv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'c.customer_name',
            'rv.voucher_number',
            'rv.voucher_date',
            'rv.receipt_amount',
            'rv.reference_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function sales_sum_gt()
    {
        $string = "s.*";
        $table  = "sales s";
        $order  = [
            "s.sales_id" => "desc"];
        $where = array(
            's.delete_status'     => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $join = [
            'currency cur' => 's.currency_id = cur.currency_id',
            'customer c'   => 's.sales_party_id = c.customer_id',
            'users u'      => 's.added_user_id = u.id',
            'followup f'   => 'f.type_id = s.sales_id AND f.type="sales"' . '#' . 'left'];
        $filter = array(
            'c.customer_name',
            's.sales_invoice_number',
            's.sales_date',
            's.sales_grand_total');
        // $group = array('s.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
            // 'group' => $group
        );
        return $data;
    }
    public function sum_expense_sum()
    {
        $string = "e.*";
        $table = "expense_bill e";
        $join  = [
            'currency cur' => 'e.currency_id = cur.currency_id',
            "supplier s"   => "e.expense_bill_payee_id=s.supplier_id",
            "users u"      => "u.id = e.added_user_id",
            'followup f'   => 'f.type_id = e.expense_bill_id AND f.type="expense_bill"' . '#' . 'left'];
        $order = [
            "e.expense_bill_id" => "desc"];
        $where = array(
            'e.delete_status'     => 0,
            'e.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'e.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            's.supplier_name',
            'e.purchase_invoice_number',
            'e.purchase_date',
            'e.purchase_grand_total');
        // $group = array('e.expense_bill_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
            // 'group' => $group
        );
        return $data;
    }
    public function purchase_converted_sum()
    {
        $string = "s.*";
        $table  = "purchase s";
        $order  = [
            "s.purchase_id" => "desc"];
        $where = array(
            's.delete_status'     => 0,
            's.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $join = [
            'currency cur' => 's.currency_id = cur.currency_id',
            'customer c'   => 's.purchase_party_id = c.customer_id',
            'users u'      => 's.added_user_id = u.id',
            'followup f'   => 'f.type_id = s.purchase_id AND f.type="purchase"' . '#' . 'left'];
        $filter = array(
            'c.customer_name',
            's.purchase_invoice_number',
            's.purchase_date',
            's.purchase_grand_total');
        // $group = array('s.sales_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
            // 'group' => $group
        );
        return $data;
    }
    public function product_varient()
    {
        $string                                 = "p.*,piv.varient_code,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name";
        $table                                  = "product_inventory p";
        $join['product_inventory_varients piv'] = "piv.product_inventory_id=p.product_inventory_id";
        $join['users u']                        = "u.id = p.added_user_id";
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0
        );
        $order = [
            "p.product_inventory_id" => "desc"];
        $filter = array(
            'p.product_code',
            'p.product_name',
            'p.product_model_no');
        $group = array(
            'P.product_inventory_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function raw_materials_varient()
    {
        $string                             = "r.*,riv.varient_code,riv.varient_name,riv.purchase_price,riv.selling_price,riv.varient_unit,u.first_name,u.last_name";
        $table                              = "raw_materials r";
        $join['raw_materials_varients riv'] = "riv.raw_material_id=r.raw_material_id";
        $join['users u']                    = "u.id = r.added_user_id";
        $where = array(
            'r.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'r.delete_status' => 0
        );
        $order = [
            "r.raw_material_id" => "desc"];
        $filter = array(
            'r.product_code',
            'r.product_name',
            'r.product_model_no');
        $group = array(
            'r.raw_material_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function labour_list()
    {
        $string                           = "l.*,lc.labour_classification_id,lc.labour_classification_name,u.first_name,u.last_name";
        $table                            = "labour l";
        $join['labour_classification lc'] = "l.classification_id=lc.labour_classification_id";
        $join['users u']                  = "u.id = l.added_user_id";
        $where = array(
            'l.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'l.delete_status' => 0
        );
        $order = [
            "l.labour_id" => "desc"];
        $filter = array(
            'l.activity_name',
            'l.type');
        $group = array(
            'l.labour_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function over_head_list()
    {
        $string          = "o.*,u.first_name,u.last_name";
        $table           = "over_head o";
        $join['users u'] = "u.id = o.added_user_id";
        $where = array(
            'o.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'o.delete_status' => 0
        );
        $order = [
            "o.over_head_id" => "desc"];
        $filter = array(
            'o.over_head_name',
            'o.over_head_cost_per_unit');
        $group = array(
            'o.over_head_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function get_products_varients_list($id)
    {
        $string                                        = 'p.*,pivv.product_inventory_varients_id,piv.varient_code,piv.quantity as q,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value,piv.damaged_stock';
        $table                                         = "product_inventory p";
        $join['product_inventory_varients piv']        = "piv.product_inventory_id=p.product_inventory_id";
        $join['product_inventory_varients_value pivv'] = "pivv.product_inventory_varients_id = piv.product_inventory_varients_id";
        $join['varients v']                            = "v.varients_id = pivv.varients_id";
        $join['varients_value val']                    = "val.varients_value_id = pivv.varients_value_id";
        $join['users u'] = "u.id = p.added_user_id";
        $where = array(
            'piv.product_inventory_id' => $id,
            'piv.branch_id'            => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'piv.delete_status'        => 0
        );
        $group = [
            "pivv.product_inventory_varients_id"];
        $filter = array(
            'p.product_code' => 'p.product_name');
        $order = array(
            'piv.product_inventory_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function get_products_varients_list1($id)
    {
        $string                                        = 'p.*,pivv.product_inventory_varients_id,piv.varient_code,piv.quantity as q,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value';
        $table                                         = "product_inventory p";
        $join['product_inventory_varients piv']        = "piv.product_inventory_id=p.product_inventory_id";
        $join['product_inventory_varients_value pivv'] = "pivv.product_inventory_varients_id = piv.product_inventory_varients_id";
        $join['varients v']                            = "v.varients_id = pivv.varients_id";
        $join['varients_value val']                    = "val.varients_value_id = pivv.varients_value_id";
        $join['users u'] = "u.id = p.added_user_id";
        $where = array(
            'piv.product_inventory_id' => $id,
            'piv.branch_id'            => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'piv.delete_status'        => 0
        );
        $group = [
            "pivv.id"];
        $filter = array(
            'p.product_code' => 'p.product_name');
        $order = array(
            'piv.product_inventory_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function all_products_varients_list()
    {
        $string                                        = 'p.*,pivv.product_inventory_varients_id,piv.varient_code,piv.quantity as q,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value,piv.damaged_stock';
        $table                                         = "product_inventory p";
        $join['product_inventory_varients piv']        = "piv.product_inventory_id=p.product_inventory_id";
        $join['product_inventory_varients_value pivv'] = "pivv.product_inventory_varients_id = piv.product_inventory_varients_id";
        $join['varients v']                            = "v.varients_id = pivv.varients_id";
        $join['varients_value val']                    = "val.varients_value_id = pivv.varients_value_id";
        $join['users u'] = "u.id = p.added_user_id";
        $where = array(
            'p.type'            => 'product',
            'piv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'piv.delete_status' => 0
        );
        $group = [
            "pivv.product_inventory_varients_id"];
        $filter = array(
            'p.product_code' => 'p.product_name');
        $order = array(
            'piv.product_inventory_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function all_products_varients_list1()
    {
        $string                                        = 'p.*,pivv.product_inventory_varients_id,piv.varient_code,piv.quantity as q,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value';
        $table                                         = "product_inventory p";
        $join['product_inventory_varients piv']        = "piv.product_inventory_id=p.product_inventory_id";
        $join['product_inventory_varients_value pivv'] = "pivv.product_inventory_varients_id = piv.product_inventory_varients_id";
        $join['varients v']                            = "v.varients_id = pivv.varients_id";
        $join['varients_value val']                    = "val.varients_value_id = pivv.varients_value_id";
        $join['users u'] = "u.id = p.added_user_id";
        $where = array(
            'p.type'            => 'product',
            'piv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'piv.delete_status' => 0
        );
        $group = [
            "pivv.id"];
        $filter = array(
            'p.product_code' => 'p.product_name');
        $order = array(
            'piv.product_inventory_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function quantity_history_list_modal($id)
    {
        $string                                 = 'q.*,piv.varient_code,piv.varient_name,piv.product_inventory_varients_id,piv.varient_unit,u.first_name,u.last_name';
        $table                                  = "quantity_history q";
        $join['product_inventory_varients piv'] = "piv.product_inventory_varients_id=q.item_id";
        $join['users u'] = "u.id = q.added_user_id";
        $where = array(
            'q.item_id'         => $id,
            'q.branch_id'       => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'q.delete_status'   => 0,
            'piv.delete_status' => 0
        );
        $filter = array(
            'q.quantity',
            'q.stock_type',
            'u.first_name',
            'u.last_name',
            'q.added_date'
        );
        //$group  = ["q.item_id"];
        $group = "";
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => "",
            'group'  => $group
        );
        return $data;
    }
    public function leads_list_field($search_data = array())
    {
        $string                   = 'l.*,c.customer_code,c.customer_name,u.first_name,u.last_name,g.lead_group_name,st.lead_stages_name,s.lead_source_name,lb.lead_business_type';
        $table                    = 'leads l';
        $join['customer c']       = 'l.party_id=c.customer_id';
        $join['lead_group g']     = 'l.group=g.lead_group_id';
        $join['lead_stages st']   = 'l.stages=st.lead_stages_id';
        $join['lead_source s']    = 'l.source=s.lead_source_id';
        $join['lead_business lb'] = 'l.business_type=lb.lead_business_id';
        $join['users u']          = 'l.assign_to=u.id';

// $where=array(

//             'l.branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'),

//             'l.delete_status'=>0
        //             );

        $condition = '(l.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and l.delete_status=0)';

        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {
            $condition_type = 'asr_no';
        }
        else

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {
            $condition_type = 'customer';
        }
        else

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {
            $condition_type = 'group';
        }
        else

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {
            $condition_type = 'stages';
        }
        else

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {
            $condition_type = 'lead_from';
        }
        else

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {
            $condition_type = 'lead_to';
        }
        else

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {
            $condition_type = 'next_action_from';
        }
        else

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {
            $condition_type = 'next_action_to';
        }

        else

        if ($search_data['assign'] != "" && $search_data['assign'] != null)
        {
            $condition_type = 'assign';
        }


        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {

            if ($condition_type == 'asr_no')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.asr_no = '" . $search_data['asr_no'] . "'";
        }

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {

            if ($condition_type == 'customer')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.party_id = '" . $search_data['customer'] . "'";
        }

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {

            if ($condition_type == 'group')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.group = '" . $search_data['group'] . "'";
        }

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {

            if ($condition_type == 'stages')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.stages = '" . $search_data['stages'] . "'";
        }

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {

            if ($condition_type == 'lead_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date >= '" . $search_data['lead_from'] . "'";
        }

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {

            if ($condition_type == 'lead_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date <= '" . $search_data['lead_to'] . "'";
        }

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {

            if ($condition_type == 'next_action_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date >= '" . $search_data['next_action_from'] . "'";
        }

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {

            if ($condition_type == 'next_action_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date <= '" . $search_data['next_action_to'] . "'";
        }

        if ($search_data['assign'] != "" && $search_data['assign'] != null && $search_data['assign'] == 'Current_User')
        {
            $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
            //$condition .= "l.assign <= '" . $search_data['assign'] . "'";
            //echo $condition;

        }


        if (($search_data['asr_no'] != "" && $search_data['asr_no'] != null) || ($search_data['customer'] != "" && $search_data['customer'] != null) || ($search_data['group'] != "" && $search_data['group'] != null) || ($search_data['stages'] != "" && $search_data['stages'] != null) || ($search_data['lead_from'] != "" && $search_data['lead_from'] != null) || ($search_data['lead_to'] != "" && $search_data['lead_to'] != null) || ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null) || ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null))
        {
            $condition .= ')';
        }

        $where = $condition;

        $order = [
            "l.lead_id" => "desc"];
        $filter = array(
            'l.lead_date',
            'l.asr_no',
            'c.customer_code',
            'c.customer_name',
            'l.next_action_date',
            'g.lead_group_name',
            'st.lead_stages_name',
            's.lead_source_name',
            'lb.lead_business_type',
            'l.priority',
            'u.first_name',
            'u.last_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;

    }
    public function todays_leads_list_field($user_type = '')
    {
        $string                   = 'l.*,c.customer_code,c.customer_name,u.first_name,u.last_name,g.lead_group_name,st.lead_stages_name,s.lead_source_name,lb.lead_business_type';
        $table                    = 'leads l';
        $join['customer c']       = 'l.party_id=c.customer_id';
        $join['lead_group g']     = 'l.group=g.lead_group_id';
        $join['lead_stages st']   = 'l.stages=st.lead_stages_id';
        $join['lead_source s']    = 'l.source=s.lead_source_id';
        $join['lead_business lb'] = 'l.business_type=lb.lead_business_id';
        $join['users u']          = 'l.assign_to=u.id';

// $where=array(

//             'l.branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'),

//             'l.delete_status'=>0
        //             );
        
        $condition = 'l.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and l.delete_status=0 and l.next_action_date = cast((now()) as date) ';

        if($user_type != 'All_User'){ 
             $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
         }
         
        // $condition .= "";
        //$condition .= ' and l.added_user_id='.$this->ci->session->userdata('SESS_USER_ID');
        $where = $condition;

        $order = [
            "l.lead_id" => "desc"];
        $filter = array(
            'l.lead_date',
            'l.asr_no',
            'c.customer_code',
            'c.customer_name',
            'l.next_action_date',
            'g.lead_group_name',
            'st.lead_stages_name',
            's.lead_source_name',
            'lb.lead_business_type',
            'l.priority',
            'u.first_name',
            'u.last_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;

    }

    public function missed_leads_list_field($user_type = '')
    {
        $string                   = 'l.*,c.customer_code,c.customer_name,u.first_name,u.last_name,g.lead_group_name,st.lead_stages_name,s.lead_source_name,lb.lead_business_type';
        $table                    = 'leads l';
        $join['customer c']       = 'l.party_id=c.customer_id';
        $join['lead_group g']     = 'l.group=g.lead_group_id';
        $join['lead_stages st']   = 'l.stages=st.lead_stages_id';
        $join['lead_source s']    = 'l.source=s.lead_source_id';
        $join['lead_business lb'] = 'l.business_type=lb.lead_business_id';
        $join['users u']          = 'l.assign_to=u.id';

// $where=array(

//             'l.branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'),

//             'l.delete_status'=>0
        //             );

        $condition = 'l.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and l.delete_status=0 and l.next_action_date < cast((now()) as date)';

        // $condition .= "";
        if($user_type != 'All_User'){ 
             $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
         }
        
        $where = $condition;

        $order = [
            "l.lead_id" => "desc"];
        $filter = array(
            'l.lead_date',
            'l.asr_no',
            'c.customer_code',
            'c.customer_name',
            'l.next_action_date',
            'g.lead_group_name',
            'st.lead_stages_name',
            's.lead_source_name',
            'lb.lead_business_type',
            'l.priority',
            'u.first_name',
            'u.last_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;

    }
    public function leads_group_list_field()
    {
        $string          = 'lg.*,u.first_name,u.last_name';
        $table           = 'lead_group lg';
        $join['users u'] = 'lg.added_user_id=u.id';
        $where = array(
            'lg.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'lg.delete_status' => 0
        );
        $order = [
            "lg.lead_group_id" => "desc"];
        $filter = array(
            'lg.added_date',
            'lg.lead_group_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function leads_stages_list_field()
    {
        $string                = 'ls.*,lg.lead_group_name,u.first_name,u.last_name';
        $table                 = 'lead_stages ls';
        $join['lead_group lg'] = 'ls.lead_group_id=lg.lead_group_id';
        $join['users u']       = 'ls.added_user_id=u.id';
        $where = array(
            'ls.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'ls.delete_status' => 0
        );
        $order = [
            "ls.lead_stages_id" => "desc"];
        $filter = array(
            'ls.added_date',
            'ls.lead_stages_name',
            'lg.lead_group_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function leads_source_list_field()
    {
        $string          = 'ls.*,u.first_name,u.last_name';
        $table           = 'lead_source ls';
        $join['users u'] = 'ls.added_user_id=u.id';
        $where = array(
            'ls.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'ls.delete_status' => 0
        );
        $order = [
            "ls.lead_source_id" => "desc"];
        $filter = array(
            'ls.added_date',
            'ls.lead_source_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function leads_business_type_list_field()
    {
        $string          = 'lb.*,u.first_name,u.last_name';
        $table           = 'lead_business lb';
        $join['users u'] = 'lb.added_user_id=u.id';
        $where = array(
            'lb.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'lb.delete_status' => 0
        );
        $order = [
            "lb.lead_business_id" => "desc"];
        $filter = array(
            'lb.added_date',
            'lb.lead_business_type');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function raw_materials_varients_list($id)
    {
        $string                                   = 'r.*,rvv.raw_material_varients_id,riv.reordering_point,riv.varient_code,riv.quantity as q,riv.varient_name,riv.purchase_price,riv.selling_price,riv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value,riv.damaged_stock';
        $table                                    = "raw_materials r";
        $join['raw_materials_varients riv']       = "riv.raw_material_id=r.raw_material_id";
        $join['raw_materials_varients_value rvv'] = "rvv.raw_material_varients_id = riv.raw_material_varients_id";
        $join['varients v']                       = "v.varients_id = rvv.varients_id";
        $join['varients_value val']               = "val.varients_value_id = rvv.varients_value_id";
        $join['users u'] = "u.id = r.added_user_id";
        $where = array(
            'riv.raw_material_id' => $id,
            'riv.branch_id'       => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'riv.delete_status'   => 0
        );
        $group = [
            "rvv.raw_material_varients_id"];
        $filter = array(
            'r.product_code' => 'r.product_name');
        $order = array(
            'riv.raw_material_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function raw_materials_varients_list1($id)
    {
        $string                                   = 'r.*,rvv.raw_material_varients_id,riv.reordering_point,riv.varient_code,riv.quantity as q,riv.varient_name,riv.purchase_price,riv.selling_price,riv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value,riv.damaged_stock';
        $table                                    = "raw_materials r";
        $join['raw_materials_varients riv']       = "riv.raw_material_id=r.raw_material_id";
        $join['raw_materials_varients_value rvv'] = "rvv.raw_material_varients_id = riv.raw_material_varients_id";
        $join['varients v']                       = "v.varients_id = rvv.varients_id";
        $join['varients_value val']               = "val.varients_value_id = rvv.varients_value_id";
        $join['users u'] = "u.id = r.added_user_id";
        $where = array(
            'riv.raw_material_id' => $id,
            'riv.branch_id'       => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'riv.delete_status'   => 0
        );
        $group = [
            "rvv.id"];
        $filter = array(
            'r.product_code' => 'r.product_name');
        $order = array(
            'riv.raw_material_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    // raw materials
    public function raw_materials_varients_list_all()
    {
        $string                                   = 'r.*,rvv.raw_material_varients_id,riv.reordering_point,riv.varient_code,riv.quantity as q,riv.varient_name,riv.purchase_price,riv.selling_price,riv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value,riv.damaged_stock';
        $table                                    = "raw_materials r";
        $join['raw_materials_varients riv']       = "riv.raw_material_id=r.raw_material_id";
        $join['raw_materials_varients_value rvv'] = "rvv.raw_material_varients_id = riv.raw_material_varients_id";
        $join['varients v']                       = "v.varients_id = rvv.varients_id";
        $join['varients_value val']               = "val.varients_value_id = rvv.varients_value_id";
        $join['users u'] = "u.id = r.added_user_id";
        $where = array(
            'riv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'riv.delete_status' => 0
        );
        $group = [
            "rvv.raw_material_varients_id"];
        $filter = array(
            'r.product_code' => 'r.product_name');
        $order = array(
            'riv.raw_material_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function raw_materials_varients_list_all1()
    {
        $string                                   = 'r.*,rvv.raw_material_varients_id,riv.reordering_point,riv.varient_code,riv.quantity as q,riv.varient_name,riv.purchase_price,riv.selling_price,riv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value,riv.damaged_stock';
        $table                                    = "raw_materials r";
        $join['raw_materials_varients riv']       = "riv.raw_material_id=r.raw_material_id";
        $join['raw_materials_varients_value rvv'] = "rvv.raw_material_varients_id = riv.raw_material_varients_id";
        $join['varients v']                       = "v.varients_id = rvv.varients_id";
        $join['varients_value val']               = "val.varients_value_id = rvv.varients_value_id";
        $join['users u'] = "u.id = r.added_user_id";
        $where = array(
            'riv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'riv.delete_status' => 0
        );
        $group = [
            "rvv.id"];
        $filter = array(
            'r.product_code' => 'r.product_name');
        $order = array(
            'riv.raw_material_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_asr_no()
    {
        $string = "l.lead_id,l.asr_no";
        $table  = "leads l";
        $where = array(
            'l.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'l.delete_status' => 0);
        $group = array(
            'l.lead_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;
    }
    public function products_varients_list()
    {
        $string                                        = 'p.*,pivv.product_inventory_varients_id,piv.varient_code,piv.quantity as q,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value,piv.damaged_stock';
        $table                                         = "product_inventory p";
        $join['product_inventory_varients piv']        = "piv.product_inventory_id=p.product_inventory_id";
        $join['product_inventory_varients_value pivv'] = "pivv.product_inventory_varients_id = piv.product_inventory_varients_id";
        $join['varients v']                            = "v.varients_id = pivv.varients_id";
        $join['varients_value val']                    = "val.varients_value_id = pivv.varients_value_id";
        $join['users u'] = "u.id = p.added_user_id";
        $where = array(
            // 'piv.product_inventory_id' => $id,
            'piv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'piv.delete_status' => 0
        );
        $group = [
            "p.product_inventory_id"];
        $filter = array(
            'p.product_code' => 'p.product_name');
        $order = array(
            'piv.product_inventory_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function products_varients_list1()
    {
        $string                                        = 'p.*,pivv.product_inventory_varients_id,piv.varient_code,piv.quantity as q,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value';
        $table                                         = "product_inventory p";
        $join['product_inventory_varients piv']        = "piv.product_inventory_id=p.product_inventory_id";
        $join['product_inventory_varients_value pivv'] = "pivv.product_inventory_varients_id = piv.product_inventory_varients_id";
        $join['varients v']                            = "v.varients_id = pivv.varients_id";
        $join['varients_value val']                    = "val.varients_value_id = pivv.varients_value_id";
        $join['users u'] = "u.id = p.added_user_id";
        $where = array(
            // 'piv.product_inventory_id' => $id,
            'piv.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'piv.delete_status' => 0
        );
        $group = [
            "pivv.id"];
        $filter = array(
            'p.product_code' => 'p.product_name');
        $order = array(
            'piv.product_inventory_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function manufacturing_order_list()
    {
        $string                               = 'mo.*,p.varient_code,u.first_name,u.last_name';
        $table                                = "manufacturing_order mo";
        $join['product_inventory_varients p'] = "p.product_inventory_varients_id=mo.product_id";
        $join['users u'] = "u.id = mo.added_user_id";
        $where = array(
            'mo.delete_status' => 0
        );
        $group = [
            "mo.manufacturing_order_id"];
        $filter = array(
            'mo.manufacturing_order_code' => 'mo.manufacturing_order_code');
        $order = array(
            'mo.manufacturing_order_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function product_varient_raw_materials()
    {
        $string                                 = "p.*,piv.varient_code,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name";
        $table                                  = "product_inventory p";
        $join['product_inventory_varients piv'] = "piv.product_inventory_id=p.product_inventory_id";
        $join['users u']                        = "u.id = p.added_user_id";
        $where = array(
            'p.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0,
            'p.type'          => 'raw-material'
        );
        $order = [
            "p.product_inventory_id" => "desc"];
        $filter = array(
            'p.product_code',
            'p.product_name',
            'p.product_model_no');
        $group = array(
            'P.product_inventory_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function manufacturing_order_history_list($id)
    {
        $string          = "sm.reference_no,sm.reason,sm.quantity,sm.added_date,b.branch_name";
        $table           = "stock_movement sm";
        $join['users u'] = "u.id = sm.added_user_id";
        $join            = [
            'branch b' => 'b.branch_id = sm.to_branch_id'
        ];
        $where = array(
            'sm.branch_id'              => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'sm.delete_status'          => 0,
            'sm.manufacturing_order_id' => $id
        );
        $order = [
            "sm.stock_movement_id" => "desc"];
        $filter = array(
            'sm.reference_no',
            'sm.reason',
            'sm.quantity');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'filter' => $filter,
            'where'  => $where,
            'join'   => $join,
            'order'  => $order
        );
        return $data;
    }
    public function manufacturing_process_list()
    {
        $string                               = 'mp.*,u.first_name,u.last_name,p.varient_code';
        $table                                = "manufacturing_process mp";
        $join['product_inventory_varients p'] = "p.product_inventory_varients_id=mp.product_id";
        $join['users u'] = "u.id = mp.added_user_id";
        $where = array(
            'mp.delete_status' => 0
        );
        $group = [
            "mp.manufacturing_process_id"];
// $filter = array(
        //         'mp.manufacturing_order_code' => 'mp.manufacturing_order_code' );
        $order = array(
            'mp.manufacturing_process_id' => 'asc');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            // 'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function hsn_list_field() {
        $string          = 'hsn.*,u.first_name,u.last_name';
        $table           = 'hsn';
        $join['users u'] = 'hsn.added_user_id=u.id';
        $where = array(
            'hsn.delete_status' => 0
        );
        $order = [
            "hsn.hsn_id" => "desc"];
        $filter = array(
            'hsn.   type',
            'hsn.hsn_code',
            'hsn.description');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    
    public function country_list_field() {
        $string          = '*';
        $table           = 'countries';        
        $where = array(
            'countries.delete_status' => 0
        );
        $order = [
            "countries.country_id" => "desc"];
        $filter = array('countries.country_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function state_list_field() {
        $string          = 'states.*,c.country_name';
        $table           = 'states';
        $join['countries c'] = 'states.country_id=c.country_id';
        $where = array(
            'states.delete_status' => 0
        );
        $order = [
            "states.state_id" => "desc"];
        $filter = array('states.state_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function city_list_field() {
        $string          = 'cities.*,c.country_name,s.state_name';
        $table           = 'cities';
        $join['states s'] = 'cities.state_id=s.state_id';
        $join['countries c'] = 's.country_id=c.country_id';
        $where = array(
            'cities.delete_status' => 0
        );
        $order = [
            "cities.city_id" => "desc"];
        $filter = array('cities.city_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
public function currencyListField() {
        $string          = 'currency.*,c.country_name';
        $table           = 'currency';
        $join['countries c'] = 'currency.country_id=c.country_id';
        $where = array(
            'currency.delete_status' => 0
        );
        $order = [
            "currency.currency_id" => "desc"];
        $filter = array('currency.currency_name','currency.currency_code','c.country_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function branch_field_without_firm($branch_id = "") {
        $string = "br.*,con.country_name as branch_country_name,sta.state_name as branch_state_name,sta.state_code as branch_state_code,cit.city_name as branch_city_name";
        $table  = "branch br";
        $where  = array(
            'br.delete_status' => 0);
        $join = [
            "countries con"       => "br.branch_country_id = con.country_id",
            "states sta"          => "br.branch_state_id = sta.state_id" . "#" . "left",
            "cities cit"          => "br.branch_city_id = cit.city_id" . "#" . "left"];
        if ($branch_id != "") {
            $where['br.branch_id'] = $branch_id;            
        } else {
            // $join['firm f']="f.firm_id = br.firm_id";
            $where['br.branch_id'] = $this->ci->session->userdata('SESS_BRANCH_ID');
        }
        $order = [
            "br.branch_id" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function branch_list_field($firm_id)
    {
        $string = '*';
        $table  = 'branch';
        
        $order = ["branch_id" => "desc"];
        $where = array(
            'delete_status' => 0,
            'firm_id'     => $firm_id
        );
        $filter = array('branch_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,            
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function shipping_address_list_field(){
        $string = "s.*, CASE when s.shipping_party_type = 'supplier' then sup.supplier_name ELSE cust.customer_name  END as party_name";
        $table  = 'shipping_address s';
       
        $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0
        );
        $join = ["customer cust" => "s.shipping_party_id = cust.customer_id" . "#" . "left",
                "supplier sup" => "s.shipping_party_id = sup.supplier_id" . "#" . "left"];
        $order = ["s.shipping_address_id" => "desc"];

        $filter = array('sup.supplier_name', 'cust.customer_name', 's.shipping_address','s.shipping_code','s.email','s.contact_number','s.contact_person');
       
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'filter' => $filter,
            'join'   => $join,
            'order'  => $order
        );
        return $data;
    }
    public function shipping_address_list_field_leathercraft(){
        $string = "s.*, CASE when s.shipping_party_type = 'supplier' then sup.supplier_name ELSE cust.customer_name  END as party_name";
        $table  = 'shipping_address s';
       
        $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0,
            's.shipping_party_type' => 'customer'
        );
        $join = ["customer cust" => "s.shipping_party_id = cust.customer_id" . "#" . "left",
                "supplier sup" => "s.shipping_party_id = sup.supplier_id" . "#" . "left"];
        $order = ["s.shipping_address_id" => "desc"];

        $filter = array('sup.supplier_name', 'cust.customer_name', 's.shipping_address','s.shipping_code','s.email','s.contact_number','s.contact_person');
       
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'filter' => $filter,
            'join'   => $join,
            'order'  => $order
        );
        return $data;
    }
    public function shipping_address_list_popup($party_id,$party_type,$state_id,$country_id=''){
        $string = 's.*,sta.state_name';
        $table  = 'shipping_address s';
       
        $where = array(
        's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
        's.delete_status' => 0,
        's.shipping_party_id'   => $party_id,
        's.shipping_party_type' => $party_type,
        /*'s.country_id!=' => $country_id  */    
        );
        /*if($state_id == 0){
         
        }else{
          $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0,
            's.shipping_party_id'   => $party_id,
            's.shipping_party_type' => $party_type,
            's.state_id' => $state_id,
        ); 
        } */
        
        $join = ["states sta" => "s.state_id = sta.state_id" . "#" . "left"];
        $order = ["s.shipping_address_id" => "desc"];
       
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function billing_address_list_popup($party_id,$party_type){
        $string = 's.*,sta.state_name';
        $table  = 'shipping_address s';
       
        $where = array(
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.delete_status' => 0,
            's.shipping_party_id'   => $party_id,
            's.shipping_party_type' => $party_type
        );
        $join = ["states sta" => "s.state_id = sta.state_id" . "#" . "left"];
        $order = ["s.shipping_address_id" => "desc"];
       
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
   public function supplier_field_with_id($supplier_id){
        $string = "s.*,con.country_name as customer_country_name,sta.state_name as customer_state_name,sta.state_code as customer_state_code,cit.city_name as customer_city_name";
        $table  = "supplier s";
        $where = array(
            's.supplier_id' => $supplier_id,
            's.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "countries con" => "s.supplier_country_id = con.country_id" . "#" . "left",
            "states sta"    => "s.supplier_state_id = sta.state_id" . "#" . "left",
            "cities cit"    => "s.supplier_city_id = cit.city_id" . "#" . "left"];
        $order = [
            "s.supplier_name" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
     public function customer_field_with_id($customer_id){
        $string = "c.*,con.country_name as customer_country_name,sta.state_name as customer_state_name,sta.state_code as customer_state_code,cit.city_name as customer_city_name";
        $table  = "customer c";
        $where  = array(
            'c.customer_id' => $customer_id,
            'c.delete_status' => 0,
            'c.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $join = [
            "countries con" => "c.customer_country_id = con.country_id" . "#" . "left",
            "states sta"    => "c.customer_state_id = sta.state_id" . "#" . "left",
            "cities cit"    => "c.customer_city_id = cit.city_id" . "#" . "left"];
        $order = [
            "c.customer_name" => "asc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order,
            'join'   => $join
        );
        return $data;
    }
    public function branch_count_field($primary_id, $table_name, $count_condition, $invoice_type,$company_id)
    {
        $string = "count(" . $primary_id . ") as invoice_count";
        $table  = $table_name;
        $where  = array('firm_id' => $company_id);
        if ($invoice_type != "regular")
        {
            $where['financial_year_id'] = $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID');
        }
        if ($count_condition != "")
        {
            $cond = explode("=>", $count_condition);
            // $month_year                 = explode("-", trim($cond[1]));
            $where[trim($cond[0])] = trim($cond[1]);
        }
        $order = [
            $primary_id => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
     public function stock_count_field($primary_id, $table_name, $count_condition, $invoice_type,$type)
    {
        $string = "count(" . $primary_id . ") as invoice_count";
        $table  = $table_name;
        $where  = array('stock_type' => $type, 'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        if ($count_condition != "")
        {
            $cond = explode("=>", $count_condition);
            // $month_year                 = explode("-", trim($cond[1]));
            $where[trim($cond[0])] = trim($cond[1]);
        }
        $order = [
            $primary_id => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'order'  => $order
        );
        return $data;
    }
    public function get_product_stock(){
        $string = "sum(P.purchase_item_quantity) as pur_qty, sum(PD.purchase_debit_note_item_quantity) as pur_dn_qty, sum(PC.purchase_credit_note_item_quantity) as pur_cn_qty, AVG(P.purchase_item_unit_price_after_discount) as pur_amt, AVG(PC.purchase_credit_note_item_unit_price) as pur_cn_amt, AVG(PD.purchase_debit_note_item_unit_price) as pur_dn_amt, PT.product_code,PT.product_name, PT.product_id,PT.product_quantity,PT.product_damaged_quantity,PT.product_alert_quantity, PT.product_missing_quantity, PT.product_opening_quantity, GROUP_CONCAT( DISTINCT PR.purchase_invoice_number, ' - ', P.purchase_item_quantity) as REF, GROUP_CONCAT( DISTINCT PRD.purchase_debit_note_invoice_number, ' - ', PD.purchase_debit_note_item_quantity) as REFD, GROUP_CONCAT(DISTINCT PRC.purchase_credit_note_invoice_number, ' - ', PC.purchase_credit_note_item_quantity) as REFC,PT.product_sku ";
        $table  = "products PT";
        $where = array(
            'PT.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'PT.delete_status'     => 0 );
 
        $join = [
             "purchase_item P"  => "PT.product_id = P.item_id and P.item_type = 'product'" ,
             "purchase PR"   => "P.purchase_id = PR.purchase_id",
             "purchase_debit_note_item PD" => "PT.product_id = PD.item_id and PD.item_type = 'product'" . "#" . "left",
             "purchase_debit_note PRD" => " PD.purchase_debit_note_id = PRD.purchase_debit_note_id" . "#" . "left",
             "purchase_credit_note_item PC" => "PT.product_id = PC.item_id and PC.item_type = 'product'" . "#" . "left",
            "purchase_credit_note PRC" => "PC.purchase_credit_note_id = PRC.purchase_credit_note_id" . "#" . "left"];
        $order = array('product_id' => 'desc');
        $group = array('PT.product_id');
        $filter = array( 'PT.product_name', 'PRC.purchase_credit_note_invoice_number',
            'PR.purchase_invoice_number', 'PRD.purchase_debit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,            
            'filter' => $filter,
            'group'  => $group,
            'order' => $order
        );
        return $data;
    }
    public function get_product_stage_stock($stage){
        $string = "p.product_id,p.product_name,sum(Case When D.stock_type = 'missing' THEN D.quantity else 0 End) as missing, sum(Case When D.stock_type = 'damaged' and D.stock_refer = '$stage' THEN D.quantity else 0 End) as damaged, sum(Case When D.stock_type = 'found' and D.stock_refer = '$stage' THEN D.quantity else 0 End) as fixed,p.product_sku,max(D.product_damage_id) as product_damage_id";
        $table  = "product_damaged D";
        $where = array(
            'p.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0);
        $join = ["products p"  => "p.product_id = D.item_id" ];
        $group = array('D.item_id');
        $order = array('product_damage_id' => 'desc');
        $filter = array('p.product_name');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter, 
            'group'  => $group,
            'order' => $order
        );
        return $data;
    }
    public function get_product_stage_damaged_stock($stage){
        $string = "p.product_id,p.product_name,sum(Case When D.stock_type = 'missing' and D.stock_refer = '$stage' THEN D.quantity else 0 End) as missing, sum(Case When D.stock_type = 'damaged'  THEN D.quantity else 0 End) as damaged, sum(Case When D.stock_type = 'fixed' and D.stock_refer = '$stage' THEN D.quantity else 0 End) as fixed, p.product_sku,max(D.product_damage_id) as product_damage_id";
        $table  = "product_damaged D";
        $where = array(
            'p.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status' => 0);
        $join = ["products p"  => "p.product_id = D.item_id"];
        $order = array('product_damage_id' => 'desc');
        $group = array(
            'D.item_id');
        $filter = array('p.product_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'order' => $order,
            'filter' => $filter,
            'group'  => $group
        );
        return $data;
    }
    /*SELECT p.product_id,p.product_name,sum(Case When D.`stock_type` = 'missing' THEN D.quantity else 0 End) as missing, sum(Case When D.`stock_type` = 'damaged' THEN D.quantity else 0 End) as damaged, sum(Case When D.`stock_type` = 'fixing' THEN D.quantity else 0 End) as fixed FROM `product_damaged` D JOIN products p ON p.product_id = D.item_id where `stock_type` = 'missing' GROUP BY `item_id`*/
    public function sales_voucher_list_advance_field($id){
        $string = "c.customer_name,c.customer_id,av.voucher_number,  av.advance_voucher_id, av.receipt_amount, av.adjusted_amount, s.sales_id, s.sales_invoice_number, s.sales_grand_total,s.sales_paid_amount";
        $table  = "advance_voucher av ";
        $join   = [
            'sales s' => 'av.party_id = s.sales_party_id',
            'customer c' => 'c.customer_id = s.sales_party_id'];
        $order = [
            "av.sales_id" => "asc"];
        $where = array(
            'av.delete_status'     => 0,
            's.delete_status'     => 0,
            'av.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'av.advance_voucher_id' => $id
        );
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,            
            'order'  => $order
        );
        return $data;
    }
    public function get_product_stock_missing_damaged_history($id){
        $string = "PT.product_id,PT.product_code,PT.product_name, D.reference_date as REF_DATE, D.added_date,D.reference_number  as REF_NUM, D.quantity as QTY,D.product_damage_id,D.comments, D.stock_type";
        $table  = "products PT";
        $join   = ['product_damaged D' => 'PT.product_id = D.item_id and D.stock_type IN ("fixed","found" )'];
        $order = ["D.product_damage_id" => "asc"];
        $where = array(
            'PT.delete_status'     => 0,
            'PT.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'PT.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'D.item_id' => $id
        );
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,            
            'order'  => $order
        );
        return $data;
    }
    public function get_product_stock_missing_history($id){
        $string = "PT.product_id,PT.product_code,PT.product_name, D.reference_date as REF_DATE, D.added_date,D.reference_number  as REF_NUM, D.quantity as QTY,D.product_damage_id,D.comments, D.stock_type";
        $table  = "products PT";
        $join   = ['product_damaged D' => 'PT.product_id = D.item_id and D.stock_type IN ("missing") '];
        $order = ["D.product_damage_id" => "asc"];
        $where = array(
            'PT.delete_status'     => 0,
            'PT.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'PT.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'D.item_id' => $id
        );
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,            
            'order'  => $order
        );
        return $data;
    }
    public function get_product_stock_damaged_history($id){
        $string = "PT.product_id,PT.product_code,PT.product_name, D.reference_date as REF_DATE, D.added_date,D.reference_number  as REF_NUM, D.quantity as QTY,D.product_damage_id,D.comments, D.stock_type";
        $table  = "products PT";
        $join   = ['product_damaged D' => 'PT.product_id = D.item_id and D.stock_type IN ("damaged") '];
        $order = ["D.product_damage_id" => "asc"];
        $where = array(
            'PT.delete_status'     => 0,
            'PT.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'PT.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'D.item_id'=> $id
        );
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,            
            'order'  => $order
        );
        return $data;
    }
    public function get_sales_product_stock(){
        $string = "(SELECT sum(SI.sales_item_quantity) FROM `sales_item` `SI` WHERE `PT`.`product_id` = `SI`.`item_id` AND SI.item_type = 'product') as sales_qty,
                    ,(SELECT sum(SDI.sales_debit_note_item_quantity) FROM `sales_debit_note_item` `SDI` WHERE `PT`.`product_id` = `SDI`.`item_id` AND SDI.item_type = 'product') as sal_dn_qty, 
                    ,(SELECT sum(`SCI`.sales_credit_note_item_quantity) FROM `sales_credit_note_item` `SCI` WHERE `PT`.`product_id` = `SCI`.`item_id` AND SCI.item_type = 'product') as sal_cn_qty, AVG(S.sales_item_unit_price) as sales_amt, AVG(SC.sales_credit_note_item_unit_price) as sal_cn_amt, AVG(SD.sales_debit_note_item_unit_price) as sal_dn_amt, sum(P.purchase_item_quantity) as pur_qty, sum(PD.purchase_debit_note_item_quantity) as pur_dn_qty, sum(PC.purchase_credit_note_item_quantity) as pur_cn_qty, AVG(P.purchase_item_unit_price) as pur_amt, AVG(PC.purchase_credit_note_item_unit_price) as pur_cn_amt, AVG(PD.purchase_debit_note_item_unit_price) as pur_dn_amt, PT.product_code,PT.product_name,PT.product_id,PT.product_quantity,PT.product_damaged_quantity,PT.product_alert_quantity,PT.product_missing_quantity, CONCAT_WS(',',SR.sales_invoice_number,SRD.sales_debit_note_invoice_number,SRC.sales_credit_note_invoice_number) as REF,
                     PT.product_sku";
        $table  = "products PT";
        $order = ["PT.product_id" => "desc"];
        $where = array(
            'PT.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'PT.delete_status'     => 0 );
        $join = [
             "sales_item S"  => "PT.product_id = S.item_id and S.item_type = 'product'" ,
             "sales SR"   => "S.sales_id = SR.sales_id",
             "sales_debit_note_item SD" => " PT.product_id = SD.item_id and SD.item_type = 'product'" . "#" . "left",
             "sales_debit_note SRD" => " SD.sales_debit_note_id = SRD.sales_debit_note_id" . "#" . "left",
             "sales_credit_note_item SC" => "PT.product_id = SC.item_id and SC.item_type = 'product'" . "#" . "left",
            "sales_credit_note SRC" => "SC.sales_credit_note_id = SRC.sales_credit_note_id" . "#" . "left",
            "purchase_item P"  => "PT.product_id = P.item_id and P.item_type = 'product'" ,
             "purchase PR"   => "P.purchase_id = PR.purchase_id",
             "purchase_debit_note_item PD" => " PT.product_id = PD.item_id and PD.item_type = 'product'" . "#" . "left",
             "purchase_debit_note PRD" => " PD.purchase_debit_note_id = PRD.purchase_debit_note_id" . "#" . "left",
             "purchase_credit_note_item PC" => "PT.product_id = PC.item_id and PC.item_type = 'product'" . "#" . "left",
            "purchase_credit_note PRC" => "PC.purchase_credit_note_id = PRC.purchase_credit_note_id" . "#" . "left"];
        $group = array('PT.product_id');
        $filter = array('PT.product_name', 'PT.product_quantity', 'PT.product_damaged_quantity', 'PT.product_alert_quantity', 'PT.product_missing_quantity');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group,
            'order'  => $order
        );
        return $data;
    }
    public function gst_list_field(){
        /**/
        $string = "S.sales_invoice_number, SI.sales_item_quantity, SI.sales_item_unit_price, ST.state_name,S.sales_billing_state_id, SI.sales_item_taxable_value, S.sales_id, SI.sales_item_igst_percentage, SI.sales_item_tax_percentage, SI.sales_item_sgst_percentage, SI.sales_item_cgst_percentage, SI.sales_item_tax_cess_percentage, SI.sales_item_igst_amount, SI.sales_item_sgst_amount, SI.sales_item_cgst_amount, SI.sales_item_tax_cess_amount, S.sales_date, S.sales_party_id, C.customer_name, C.customer_gstin_number, ST.is_utgst,S.sales_grand_total, P.product_unit_id, CASE ST.is_utgst when '1' then SI.sales_item_sgst_amount ELSE 0
                END as UTGST, CASE ST.is_utgst when '0' then SI.sales_item_sgst_amount ELSE 0
                END as SGST, C.customer_id,
            CASE SI.item_type when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as  product_hsn_sac_code,
                CASE SI.item_type when 'product' then U.uom ELSE US.uom END as uom";
        $table  = "sales_item SI";
        $join   = [
            'sales S' => 'S.sales_id = SI.sales_id',
            'customer C' => 'C.customer_id = S.sales_party_id',
            'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id and SI.item_type = "product"'. '#' .'left',
            'uqc US'      => 'US.id = SR.service_unit and SI.item_type = "service"'. '#' .'left',
            'states ST'   => 'ST.state_id = S.sales_billing_state_id' . '#' . 'left'];
        $order = [
            "S.sales_id" => "desc"];
        $where = array(
            'S.delete_status'     => 0,
            'S.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage >' => 0,
        );
        $filter = array(
            'C.customer_name',
            'S.sales_invoice_number',
            'C.customer_gstin_number',
            'S.sales_id',
            'S.sales_date',
            'ST.state_name',
            'US.uom',
            'U.uom',
            'S.sales_grand_total',
            'P.product_hsn_sac_code',
            'SI.sales_item_taxable_value',
            'SI.sales_item_taxable_value');
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function hsn_report_list_field(){
        $string = "sum(SI.sales_item_taxable_value) as sales_item_taxable_value, S.sales_id, SI.sales_item_igst_percentage, SI.sales_item_tax_percentage, SI.sales_item_sgst_percentage,sum(SI.sales_item_sub_total) as sales_item_sub_total,sum(SI.sales_item_quantity) as sales_item_quantity, SI.sales_item_cgst_percentage, SI.sales_item_tax_cess_percentage, sum(SI.sales_item_igst_amount) as sales_item_igst_amount, sum(SI.sales_item_sgst_amount) as sales_item_sgst_amount, sum(SI.sales_item_cgst_amount) as sales_item_cgst_amount, sum(SI.sales_item_tax_cess_amount) as sales_item_tax_cess_amount, ST.is_utgst,S.sales_grand_total, P.product_unit_id,
            CASE SI.item_type when 'product' then P.product_unit_id 
                when 'service' then SR.service_unit 
                END as  unit_id,
            CASE ST.is_utgst when '1' then SI.sales_item_sgst_amount ELSE 0
                END as UTGST, 
            CASE ST.is_utgst when '0' then SI.sales_item_sgst_amount ELSE 0
                END as SGST,
            CASE SI.item_type when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as  hsn_sac_code, 
            CASE SI.item_type when 'product' then U.uom ELSE UR.uom
                END as T_uom";
        $table  = "sales_item SI";
        $join   = [
            'sales S' => 'S.sales_id = SI.sales_id',
            'products P'   => 'SI.item_id = P.product_id  and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id and SI.item_type = "product"' . '#' . 'left',
            'uqc UR'      => 'UR.id = SR.service_unit and SI.item_type = "service"' . '#' . 'left',
            'states ST'   => 'ST.state_id = S.sales_billing_state_id' . '#' . 'left'];
        $order = [
            "S.sales_id" => "desc"];
        $where = array(
            'S.delete_status'     => 0,
            'SI.delete_status'     => 0,
            'S.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'U.uom',
            'UR.uom',
            'P.product_hsn_sac_code',
            'SI.sales_item_sub_total',
            'SR.service_hsn_sac_code',
            'sales_item_tax_cess_amount',
            'SI.sales_item_taxable_value',
            'SI.sales_item_sgst_amount',
            'SI.sales_item_cgst_amount',
            'SI.sales_item_igst_amount'
            );
        $group = array('hsn_sac_code','T_uom');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }

    public function tcs_report_sales_list(){
        $string = 'SI.sales_item_tds_id,S.sales_id,SUM(SI.sales_item_taxable_value) as taxable_value, SI.sales_item_tds_percentage, SUM(SI.sales_item_tds_amount) as amount, S.sales_date, S.sales_party_id, C.customer_name, C.customer_id, TS.section_name, C.customer_pan_number, C.customer_gstin_number';
        $table  = 'sales_item SI';
        $join   = ["sales S" => "SI.sales_id = S.sales_id",
                    "customer C" => "C.customer_id = S.sales_party_id",
                    "tax T" => "T.tax_id = SI.sales_item_tds_id ",
                    "tax_section TS" => "TS.section_id = T.section_id"];
        $order = ["S.sales_id" => "desc"];
        $where = array(
            'S.delete_status'        => 0,
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            'S.branch_id'             => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id'     => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array('C.customer_name', 'SI.sales_item_taxable_value', 'SI.sales_item_tds_percentage');
        $group = array('S.sales_date, S.sales_party_id, SI.sales_item_tds_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
public function tds_report_sales_list(){
        $string = 'SI.sales_item_tds_id,S.sales_id,SUM(SI.sales_item_taxable_value) as taxable_value, SI.sales_item_tds_percentage, SUM(SI.sales_item_tds_amount) as amount, S.sales_date, S.sales_party_id, C.customer_name, TS.section_name, C.customer_pan_number, C.customer_gstin_number, C.customer_tan_number,C.customer_id';
        $table  = 'sales_item SI';
        $join   = ["sales S" => "SI.sales_id = S.sales_id",
                    "customer C" => "C.customer_id = S.sales_party_id",
                    "tax T" => "T.tax_id = SI.sales_item_tds_id ",
                    "tax_section TS" => "TS.section_id = T.section_id".'#'.'left'];
        $order = ["S.sales_id" => "desc"];
        $where = array(
            'S.delete_status'        => 0,
            'SI.sales_item_tds_percentage >'        => 0,
            'T.tax_name' => 'TDS',
            'S.branch_id'             => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id'     => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array('C.customer_name', 'SI.sales_item_taxable_value', 'SI.sales_item_tds_percentage');
        $group = array('S.sales_date, S.sales_party_id, SI.sales_item_tds_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function tcs_report_purchase_list(){
        $string = 'P.purchase_date, S.supplier_name, PI.purchase_item_tds_id, PI.purchase_item_tds_percentage, SUM(PI.purchase_item_tds_amount) as amount, SUM(PI.purchase_item_taxable_value) as taxable_value,  S.supplier_tan_number, TS.section_name,S.supplier_id,P.purchase_id';
        $table  = 'purchase_item PI';
        $join   = ["purchase P" => "P.purchase_id = PI.purchase_id",
                    "supplier S" => "S.supplier_id = P.purchase_party_id",
                    "tax T" => "T.tax_id = PI.purchase_item_tds_id ",
                    "tax_section TS" => "TS.section_id = T.section_id"];
        $order = ["P.purchase_id" => "desc"];
        $where = array(
            'P.delete_status'        => 0,
            'PI.purchase_item_tds_percentage >'  => 0,
            'T.tax_name' => 'TCS',
            'P.branch_id'             => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.financial_year_id'     => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array('S.supplier_name', 'PI.purchase_item_taxable_value', 'PI.purchase_item_tds_percentage');
        $group = array('P.purchase_date, P.purchase_party_id, PI.purchase_item_tds_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }
    public function tds_report_expense_list(){
        $string = 'SUM(EI.expense_bill_item_taxable_value) as taxable_value, EI.expense_bill_item_tds_id, SUM(EI.expense_bill_item_tds_amount) as amount, EI.expense_bill_item_tds_percentage, S.supplier_tan_number, TS.section_name, E.expense_bill_date, S.supplier_name,S.supplier_id,E.expense_bill_id';
        $table  = 'expense_bill_item EI';
        $join   = ["expense_bill E" => "EI.expense_bill_id = E.expense_bill_id",
                    "supplier S" => "S.supplier_id = E.expense_bill_payee_id",
                    "tax T" => "T.tax_id = EI.expense_bill_item_tds_id ",
                    "tax_section TS" => "TS.section_id = T.section_id".'#'.'left'];
        $order = ["E.expense_bill_id" => "desc"];
        $where = array(
            'E.delete_status'        => 0,
            'EI.expense_bill_item_tds_percentage >'  => 0,
            'T.tax_name' => 'TDS',
            'E.branch_id'             => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'E.financial_year_id'     => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array('S.supplier_name', 'EI.expense_bill_item_taxable_value', 'EI.expense_bill_item_tds_percentage');
        $group = array('E.expense_bill_date, E.expense_bill_payee_id,  EI.expense_bill_item_tds_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_supplier_tcs_purchase(){
        $string = "S.supplier_name,S.supplier_id, S.supplier_tan_number";
        $table  = "supplier S";
        $join   = [
            'purchase P' => 'S.supplier_id = P.purchase_party_id',        
            'purchase_item PI' => 'P.purchase_id = PI.purchase_id',
            'tax T' => "T.tax_id = PI.purchase_item_tds_id "];
        $where = array(
            'P.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'PI.purchase_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            'P.delete_status'                => 0);
        $group = array(
            'P.purchase_party_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }


    public function distinct_tcs_section_purchase(){
        $string = "TS.section_name";
        $table  = "tax T";
        $join   = ['purchase_item PI' => "T.tax_id = PI.purchase_item_tds_id  ",
                    "tax_section TS" => "TS.section_id = T.section_id",
                    "purchase P" => "P.purchase_id = PI.purchase_id"];
        $where = array(
            'P.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'PI.purchase_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            'P.delete_status'                => 0);
        $group = array('TS.section_name');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function distinct_tcs_percentage_purchase(){
        $string = "PI.purchase_item_tds_percentage";
        $table  = "tax T";
        $join   = ['purchase_item PI' => "T.tax_id = PI.purchase_item_tds_id  ",
                    "tax_section TS" => "TS.section_id = T.section_id",
                    "purchase P" => "P.purchase_id = PI.purchase_id"];
        $where = array(
            'P.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'PI.purchase_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            'P.delete_status'                => 0);
        $group = array('PI.purchase_item_tds_percentage');      

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function gst_credit_note_list_field(){
        $string = "S.sales_credit_note_invoice_number, SI.sales_credit_note_item_quantity, SI.sales_credit_note_item_unit_price, sa.sales_invoice_number, sa.sales_id, ST.state_name,S.sales_credit_note_billing_state_id, SI.sales_credit_note_item_taxable_value, S.sales_credit_note_id, SI.sales_credit_note_item_igst_percentage, SI.sales_credit_note_item_sgst_percentage, SI.sales_credit_note_item_cgst_percentage, SI.sales_credit_note_item_tax_cess_percentage, SI.sales_credit_note_item_tax_percentage, SI.sales_credit_note_item_igst_amount, SI.sales_credit_note_item_sgst_amount, SI.sales_credit_note_item_cgst_amount, SI.sales_credit_note_item_tax_cess_amount, S.sales_credit_note_date, S.sales_credit_note_party_id, C.customer_name, C.customer_gstin_number, ST.is_utgst, SI.sales_credit_note_item_grand_total, SI.sales_credit_note_item_sub_total, S.sales_credit_note_grand_total, P.product_unit_id, CASE SI.item_type 
                when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as  product_hsn_sac_code, CASE ST.is_utgst when '1' then SI.sales_credit_note_item_sgst_amount ELSE 0
                END as UTGST, CASE ST.is_utgst when '0' then SI.sales_credit_note_item_sgst_amount ELSE 0
                END as SGST, C.customer_id,
                CASE SI.item_type when 'product' then U.uom ELSE US.uom END as uom";
        $table  = "sales_credit_note_item SI";
        $join   = [
            'sales_credit_note S' => 'S.sales_credit_note_id = SI.sales_credit_note_id',
            'sales sa' => 'sa.sales_id = S.sales_id',
            'customer C' => 'C.customer_id = S.sales_credit_note_party_id',
            'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id  and SI.item_type = "product"' . '#' . 'left',
            'uqc US'      => 'US.id = SR.service_unit  and SI.item_type = "service"'. '#' .'left',
            'states ST'   => 'ST.state_id = S.sales_credit_note_billing_state_id' . '#' . 'left'];
        $order = [
            "S.sales_credit_note_id" => "desc"];
        $where = array(
            'S.delete_status'     => 0,
            'SI.sales_credit_note_item_tax_percentage >' => 0,
            'S.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'ST.state_name',
            'product_hsn_sac_code',
            'S.sales_credit_note_invoice_number',
            'C.customer_name',
            'U.uom',
            'C.customer_gstin_number', 
            'S.sales_credit_note_id', 
            'S.sales_credit_note_grand_total',
            'SI.sales_credit_note_item_sub_total',
            'SI.sales_credit_note_item_taxable_value');
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function gst_debit_note_list_field(){
        $string = "S.sales_debit_note_invoice_number, SI.sales_debit_note_item_quantity, SI.sales_debit_note_item_unit_price, sa.sales_invoice_number, sa.sales_id, ST.state_name,S.sales_debit_note_billing_state_id, SI.sales_debit_note_item_taxable_value, S.sales_debit_note_id, SI.sales_debit_note_item_igst_percentage, SI.sales_debit_note_item_sgst_percentage, SI.sales_debit_note_item_cgst_percentage, SI.sales_debit_note_item_tax_cess_percentage, SI.sales_debit_note_item_tax_percentage, SI.sales_debit_note_item_igst_amount, SI.sales_debit_note_item_sgst_amount, SI.sales_debit_note_item_cgst_amount, SI.sales_debit_note_item_tax_cess_amount, S.sales_debit_note_date, S.sales_debit_note_party_id, C.customer_name, C.customer_gstin_number, ST.is_utgst, SI.sales_debit_note_item_grand_total, SI.sales_debit_note_item_sub_total, S.sales_debit_note_grand_total, P.product_unit_id, CASE SI.item_type 
                when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as  product_hsn_sac_code, CASE ST.is_utgst when '1' then SI.sales_debit_note_item_sgst_amount ELSE 0
                END as UTGST, CASE ST.is_utgst when '0' then SI.sales_debit_note_item_sgst_amount ELSE 0
                END as SGST, C.customer_id, CASE SI.item_type when 'product' then U.uom ELSE US.uom END as uom ";
        $table  = "sales_debit_note_item SI";
        $join   = [
            'sales_debit_note S' => 'S.sales_debit_note_id = SI.sales_debit_note_id',
            'sales sa' => 'sa.sales_id = S.sales_id',
            'customer C' => 'C.customer_id = S.sales_debit_note_party_id',
            'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id' . '#' . 'left',
            'uqc US'      => 'US.id = SR.service_unit'. '#' .'left',
            'states ST'   => 'ST.state_id = S.sales_debit_note_billing_state_id' . '#' . 'left'];
        $order = [
            "S.sales_debit_note_id" => "desc"];
        $where = array(
            'S.delete_status'     => 0,
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            'S.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'ST.state_name',
            'product_hsn_sac_code',
            'S.sales_debit_note_invoice_number',
            'C.customer_name',
            'C.customer_gstin_number',
            'S.sales_debit_note_id',
            'U.uom',
            'SI.sales_debit_note_item_sub_total',
            'S.sales_debit_note_grand_total',
            'SI.sales_debit_note_item_taxable_value');
        
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }
    public function get_product_refence($product_id){
        $string = "PR.purchase_invoice_number, P.purchase_item_quantity, PRD.purchase_debit_note_invoice_number, PD.purchase_debit_note_item_quantity,  PRC.purchase_credit_note_invoice_number, PC.purchase_credit_note_item_quantity";
        $table  = "products PT";
        $where = array(
            'PT.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'PT.delete_status'     => 0,
            'PT.product_id' => $product_id );
        $join = [
             "purchase_item P"  => "PT.product_id = P.item_id and P.item_type = 'product'" ,
             "purchase PR"   => "P.purchase_id = PR.purchase_id",
             "purchase_debit_note_item PD" => " PT.product_id = PD.item_id and PD.item_type = 'product'" . "#" . "left",
             "purchase_debit_note PRD" => " PD.purchase_debit_note_id = PRD.purchase_debit_note_id" . "#" . "left",
             "purchase_credit_note_item PC" => "PT.product_id = PC.item_id and PC.item_type = 'product'" . "#" . "left",
            "purchase_credit_note PRC" => "PC.purchase_credit_note_id = PRC.purchase_credit_note_id" . "#" . "left"];
       // $group = array('PT.product_id');
        $filter = array( 'PT.product_name', 'PRC.purchase_credit_note_invoice_number',
            'PR.purchase_invoice_number', 'PRD.purchase_debit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,            
            'filter' => $filter,
           // 'group'  => $group
        );
        return $data;
    }
    public function distinct_customer_tcs_sales(){
        $string = "c.customer_id,c.customer_name,c.customer_pan_number";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"',        
            'sales_item SI' => 's.sales_id = SI.sales_id',
            'tax T' => "T.tax_id = SI.sales_item_tds_id "];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            's.delete_status'                => 0);
        $group = array(
            's.sales_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_customer_tcs_pan_sales(){
        $string = "c.customer_pan_number";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"',        
            'sales_item SI' => 's.sales_id = SI.sales_id',
            'tax T' => "T.tax_id = SI.sales_item_tds_id "];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            's.delete_status'                => 0);
        $group = array(
            'c.customer_pan_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_tcs_section_sales(){
        $string = "TS.section_name";
        $table  = "tax T";
        $join   = ['sales_item SI' => "T.tax_id = SI.sales_item_tds_id ",
                    "tax_section TS" => "TS.section_id = T.section_id",
                    'sales s' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            's.delete_status'                => 0);
        $group = array(
            'TS.section_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
     public function distinct_tcs_percentage_sales(){
        $string = "SI.sales_item_tds_percentage";
        $table  = "tax T";
        $join   = ['sales_item SI' => "T.tax_id = SI.sales_item_tds_id ",
                    'sales s' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TCS',
            's.delete_status'                => 0);
        $group = array(
            'SI.sales_item_tds_percentage');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_customer_tds_sales(){
        $string = "c.customer_id,c.customer_name,c.customer_tan_number";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"',        
            'sales_item SI' => 's.sales_id = SI.sales_id',
            'tax T' => "T.tax_id = SI.sales_item_tds_id "];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TDS',
            's.delete_status'                => 0);
        $group = array(
            's.sales_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_tds_section_sales(){
        $string = "TS.section_name";
        $table  = "tax T";
        $join   = ['sales_item SI' => "T.tax_id = SI.sales_item_tds_id ",
                    "tax_section TS" => "TS.section_id = T.section_id",
                    'sales s' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TDS',
            's.delete_status'                => 0);
        $group = array(
            'SI.sales_item_tds_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
     public function distinct_tds_percentage_sales(){
        $string = "SI.sales_item_tds_percentage";
        $table  = "tax T";
        $join   = ['sales_item SI' => "T.tax_id = SI.sales_item_tds_id ",
                    'sales s' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TDS',
            's.delete_status'                => 0);
        $group = array(
            'SI.sales_item_tds_percentage');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_customer_gst_sales(){
        $string = "c.customer_id,c.customer_name";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"',        
            'sales_item SI' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('c.customer_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }
    public function distinct_customer_gst_sales_gstin(){
        $string = "c.customer_gstin_number";
        $table  = "customer c";
        $join   = [
            'sales s' => 's.sales_party_id = c.customer_id and s.sales_party_type = "customer"',        
            'sales_item SI' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('c.customer_gstin_number');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function distinct_voucher_gst_sales(){
        $string = "s.sales_invoice_number,s.sales_id";
        $table  = "sales s";
        $join   = ['sales_item SI' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage >' => 0,
            's.delete_status'                => 0);
        $group = array('s.sales_invoice_number');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function distinct_voucher_hsn_sales(){
        $string = "CASE SI.item_type
                when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as hsn_sac_code";
        $table  = "sales s";
        $join   = ['sales_item SI' => 's.sales_id = SI.sales_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
                    'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage >' => 0,
            's.delete_status'                => 0);
        $group = array('hsn_sac_code');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function distinct_hsn_number(){
        $string = "CASE SI.item_type
                when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as hsn_sac_code,
                CASE SI.item_type when 'product' then U.uom ELSE UR.uom
                END as T_uom";
        $table  = "sales_item SI";
        $join   = ['sales s' => 's.sales_id = SI.sales_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
                    'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
                    'uqc U'      => 'U.id = P.product_unit_id and SI.item_type = "product"' . '#' . 'left',
                    'uqc UR'      => 'UR.id = SR.service_unit and SI.item_type = "service"' . '#' . 'left',
                ];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('hsn_sac_code');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }


    public function distinct_gst_price_sales(){
        $string = "SI.sales_item_unit_price";
        $table  = "sales s";
        $join   = ['sales_item SI' => 's.sales_id = SI.sales_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_item_unit_price');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_gst_quantity_sales(){
        $string = "SI.sales_item_quantity";
        $table  = "sales s";
        $join   = ['sales_item SI' => 's.sales_id = SI.sales_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.delete_status'                => 0);
        $group = array('SI.sales_item_quantity');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function distinct_hsn_quantity_sales(){
        $string = "distinct sum(SI.sales_item_quantity) as sales_item_quantity, 
        CASE SI.item_type when 'product' then P.product_unit_id 
                when 'service' then SR.service_unit 
                END as  unit_id,
           CASE SI.item_type
                when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as hsn_sac_code";
        $table  = "sales_item SI";
        $join   = ['sales s' => 's.sales_id = SI.sales_id',
                 'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"'  . '#' . 'left',
                 'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
                 'uqc U'      => 'U.id = P.product_unit_id and SI.item_type = "product"' . '#' . 'left',
                'uqc UR'      => 'UR.id = SR.service_unit and SI.item_type = "service"' . '#' . 'left'
                     ];
      
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.delete_status'                => 0,
            'SI.delete_status'     => 0
        );
        $group = array('hsn_sac_code','unit_id','sales_item_quantity');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function distinct_gst_UOM_sales(){
        $string = "CASE SI.item_type when 'product' then U.id ELSE US.id
                END as product_unit_id,CASE SI.item_type when 'product' then U.uom ELSE US.uom END as uom, CASE SI.item_type when 'product' then U.id ELSE US.id
                END as product_uom_id";
        $table  = "sales_item SI";
        $join   = [
            'sales S' => 'S.sales_id = SI.sales_id',
            'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id  and SI.item_type = "product"' . '#' . 'left',
            'uqc US'      => 'US.id = SR.service_unit  and SI.item_type = "service"'. '#' .'left'];
       
        $where = array(
            'S.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage !=' => 0,
            'S.delete_status'                => 0);
        $group = array('product_uom_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_hsn_UOM_sales(){
       $string = "CASE SI.item_type when 'product' then P.product_unit_id 
                when 'service' then SR.service_unit 
                END as  unit_id,
                CASE SI.item_type when 'product' then U.uom ELSE UR.uom
                END as T_uom";
        $table  = "sales s";
        $join   = ['sales_item SI' => 's.sales_id = SI.sales_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
                    'services SR'  => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
                    'uqc U' => 'U.id = P.product_unit_id and SI.item_type = "product"' . '#' . 'left',
                    'uqc UR' => 'UR.id = SR.service_unit and SI.item_type = "service"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            's.delete_status'                => 0);
        $group = array('unit_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_gst_place_of_supply_sales(){
        $string = "S.sales_billing_state_id, CASE when S.sales_billing_state_id = 0 then 'Outside India' ELSE ST.state_name  END as state_name";
        $table  = "sales S";
        $join   = ['sales_item SI' => 'S.sales_id = SI.sales_id',
                    'states ST'   => 'ST.state_id = S.sales_billing_state_id' . '#' . 'left'];
        $where = array(
            'S.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage > ' => 0,
            'S.delete_status'                => 0);
        $group = array('S.sales_billing_state_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_tax_percentage_gst_sales(){
        $string = "SI.sales_item_tax_percentage";
        $table  = "sales s";
        $join   = ['sales_item SI' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_item_tax_percentage');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }

    public function distinct_tax_percentage_cess_sales(){
        $string = "SI.sales_item_tax_cess_percentage";
        $table  = "sales s";
        $join   = ['sales_item SI' => 's.sales_id = SI.sales_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_item_tax_cess_percentage');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }
    public function distinct_customer_gst_sales_debit_note(){
        $string = "c.customer_id,c.customer_name";
        $table  = "customer c";
        $join   = [
            'sales_debit_note s' => 's.sales_debit_note_party_id = c.customer_id and s.sales_debit_note_party_type = "customer"',        
            'sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('s.sales_debit_note_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_customer_gst_sales_debit_note_gstin(){
        $string = "c.customer_gstin_number";
        $table  = "customer c";
        $join   = [
            'sales_debit_note s' => 's.sales_debit_note_party_id = c.customer_id and s.sales_debit_note_party_type = "customer"',        
            'sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('c.customer_gstin_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_supplier_tds_expense(){
        $string = "S.supplier_name,S.supplier_id, S.supplier_tan_number";
        $table  = "supplier S";
        $join   = [            
            "expense_bill E" => "S.supplier_id = E.expense_bill_payee_id",     
            'expense_bill_item EI' => 'EI.expense_bill_id = E.expense_bill_id',
            'tax T' => "T.tax_id = EI.expense_bill_item_tds_id "];
        $where = array(
            'E.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'E.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'EI.expense_bill_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TDS',
            'E.delete_status'                => 0);
        $group = array('E.expense_bill_payee_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }


    public function distinct_tds_section_expense(){
        $string = "TS.section_name";
        $table  = "tax T";
        $join   = ['expense_bill_item EI' => "T.tax_id = EI.expense_bill_item_tds_id",
                    "tax_section TS" => "TS.section_id = T.section_id",
                    "expense_bill E" => 'EI.expense_bill_id = E.expense_bill_id'];
        $where = array(
            'E.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'E.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'EI.expense_bill_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TDS',
            'E.delete_status'                => 0);
        $group = array('TS.section_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }


    public function distinct_tds_percentage_expense(){
        $string = "EI.expense_bill_item_tds_percentage";
        $table  = "tax T";
        $join   = ['expense_bill_item EI' => "T.tax_id = EI.expense_bill_item_tds_id",
                    "tax_section TS" => "TS.section_id = T.section_id".'#'.'left',
                    "expense_bill E" => 'EI.expense_bill_id = E.expense_bill_id'];
        $where = array(
            'E.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'E.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'EI.expense_bill_item_tds_percentage > ' => 0,
            'T.tax_name' => 'TDS',
            'E.delete_status'                => 0);
        $group = array('EI.expense_bill_item_tds_percentage');      

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );

        return $data;

    }
    public function distinct_customer_gst_sales_credit_note(){
        $string = "c.customer_id,c.customer_name";
        $table  = "customer c";
        $join   = [
            'sales_credit_note s' => 's.sales_credit_note_party_id = c.customer_id and s.sales_credit_note_party_type = "customer"',        
            'sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('s.sales_credit_note_party_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_customer_gst_sales_credit_note_gstin(){
        $string = "c.customer_gstin_number";
        $table  = "customer c";
        $join   = [
            'sales_credit_note s' => 's.sales_credit_note_party_id = c.customer_id and s.sales_credit_note_party_type = "customer"',        
            'sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('c.customer_gstin_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_voucher_gst_sales_credit_note(){
        $string = "s.sales_credit_note_invoice_number,s.sales_credit_note_id";
        $table  = "sales_credit_note_item SI";
        $join   = ['sales_credit_note s' => 'SI.sales_credit_note_id = s.sales_credit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('s.sales_credit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_voucher_gst_sales_credit_note_invoice(){
        $string = "sa.sales_id,sa.sales_invoice_number";
        $table  = "sales_credit_note s";
        $join   = ['sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id',
                    'sales sa' => 's.sales_id = sa.sales_id' ];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('sa.sales_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_voucher_gst_sales_debit_note(){
        $string = "s.sales_debit_note_invoice_number,s.sales_debit_note_id";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('s.sales_debit_note_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_voucher_gst_sales_debit_note_reference(){
        $string = "sa.sales_id, sa.sales_invoice_number";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id',
                   'sales sa' => 's.sales_id = sa.sales_id' ];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('sa.sales_invoice_number');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_voucher_hsn_sales_credit_note(){
        $string = "CASE SI.item_type
                when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as hsn_sac_code";
        $table  = "sales_credit_note s";
        $join   = ['sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
                    'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('hsn_sac_code');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_voucher_hsn_sales_debit_note(){
        $string = "CASE SI.item_type
                when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as hsn_sac_code";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
                    'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('hsn_sac_code');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_price_sales_credit_note(){
        $string = "SI.sales_credit_note_item_unit_price";
        $table  = "sales_credit_note s";
        $join   = ['sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_credit_note_item_unit_price');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_price_sales_debit_note(){
        $string = "SI.sales_debit_note_item_unit_price";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_debit_note_item_unit_price');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_quantity_sales_credit_note(){
        $string = "SI.sales_credit_note_item_quantity";
        $table  = "sales_credit_note s";
        $join   = ['sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_credit_note_item_quantity');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_quantity_sales_debit_note(){
        $string = "SI.sales_debit_note_item_quantity";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id',
                    'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_debit_note_item_quantity');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_UOM_sales_credit_note(){
        $string = "CASE SI.item_type when 'product' then U.id ELSE US.id
                END as product_unit_id,CASE SI.item_type when 'product' then U.uom ELSE US.uom END as uom, CASE SI.item_type when 'product' then U.id ELSE US.id
                END as product_uom_id";
        $table  = "sales_credit_note_item SI";
         $join   = [
            'sales_credit_note S' => 'S.sales_credit_note_id = SI.sales_credit_note_id',
            'sales sa' => 'sa.sales_id = S.sales_id',
            'customer C' => 'C.customer_id = S.sales_credit_note_party_id',
            'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id  and SI.item_type = "product"' . '#' . 'left',
            'uqc US'      => 'US.id = SR.service_unit  and SI.item_type = "service"'. '#' .'left',
            'states ST'   => 'ST.state_id = S.sales_credit_note_billing_state_id' . '#' . 'left'];
        $where = array(
            'S.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            'S.delete_status'                => 0);
        $group = array('product_uom_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_UOM_sales_debit_note(){
         $string = "CASE SI.item_type when 'product' then U.id ELSE US.id
                END as product_unit_id,CASE SI.item_type when 'product' then U.uom ELSE US.uom END as uom, CASE SI.item_type when 'product' then U.id ELSE US.id
                END as product_uom_id";

        $table = "sales_debit_note_item SI" ;

        $join   = [
            'sales_debit_note s' => 's.sales_debit_note_id = SI.sales_debit_note_id',
            'products P'   => 'SI.item_id = P.product_id and SI.item_type = "product"' . '#' . 'left',
            'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
            'uqc U'      => 'U.id = P.product_unit_id  and SI.item_type = "product"' . '#' . 'left',
            'uqc US'      => 'US.id = SR.service_unit  and SI.item_type = "service"'. '#' .'left'];
       
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('product_uom_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_place_of_supply_sales_credit_note(){
        $string = "S.sales_credit_note_billing_state_id, CASE when S.sales_credit_note_billing_state_id = 0 then 'Outside India' ELSE ST.state_name  END as state_name";
        $table  = "sales_credit_note S";
        $join   = ['sales_credit_note_item SI' => 'S.sales_credit_note_id = SI.sales_credit_note_id',
                    'states ST'   => 'ST.state_id = S.sales_credit_note_billing_state_id' . '#' . 'left'];
        $where = array(
            'S.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            'S.delete_status'                => 0);
        $group = array('S.sales_credit_note_billing_state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_gst_place_of_supply_sales_debit_note(){
        $string = "s.sales_debit_note_billing_state_id, CASE when s.sales_debit_note_billing_state_id = 0 then 'Outside India' ELSE ST.state_name  END as state_name";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id',
                    'states ST'   => 'ST.state_id = s.sales_debit_note_billing_state_id' . '#' . 'left'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('s.sales_debit_note_billing_state_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_tax_percentage_gst_sales_credit_note(){
        $string = "SI.sales_credit_note_item_tax_percentage";
        $table  = "sales_credit_note s";
        $join   = ['sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_credit_note_item_tax_percentage');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_tax_percentage_gst_sales_debit_note(){
        $string = "SI.sales_debit_note_item_tax_percentage";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_debit_note_item_tax_percentage');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
     public function distinct_tax_percentage_cess_sales_credit_note(){
        $string = "SI.sales_credit_note_item_tax_cess_percentage";
        $table  = "sales_credit_note s";
        $join   = ['sales_credit_note_item SI' => 's.sales_credit_note_id = SI.sales_credit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_credit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_credit_note_item_tax_cess_percentage');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
    public function distinct_tax_percentage_cess_sales_debit_note(){
        $string = "SI.sales_debit_note_item_tax_cess_percentage";
        $table  = "sales_debit_note s";
        $join   = ['sales_debit_note_item SI' => 's.sales_debit_note_id = SI.sales_debit_note_id'];
        $where = array(
            's.branch_id'                    => $this->ci->session->userdata('SESS_BRANCH_ID'),
            's.financial_year_id'            => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
            'SI.sales_debit_note_item_tax_percentage > ' => 0,
            's.delete_status'                => 0);
        $group = array('SI.sales_debit_note_item_tax_cess_percentage');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }


    public function barcode_list_field($id=''){
        $string = 'b.*,p.product_name, "product" as item_type, "code128" as item_barcode_symbology,  " - " as varient_name,u.uom as varient_unit, c.category_name, p.product_code as item_code, p.product_sku, p.product_selling_price, p.product_serail_no,c.category_code';
        $table  = 'tbl_barcode b';
        $join   = ["products p" => "p.product_id=b.product_id",
                    "category c" => "c.category_id=p.product_category_id",
                    "uqc u"  => "u.id = p.product_unit_id"
                ];
        $order = [
            "b.id" => "desc"];
        if($id == ''){
             $where = array('b.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID')
        );
        }else{
             $where = array('b.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'), 'b.id' => $id
        );
        }
       
        $filter = array('p.product_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    //Added for CRM all reports
    public function leads_history_list_field($search_data = array())
    {
        $string                   = 'lh.*,c.customer_code,c.customer_name,u.first_name,u.last_name,g.lead_group_name,st.lead_stages_name,s.lead_source_name,lb.lead_business_type, c.customer_id,ur.first_name as fname,ur.last_name as lname, l.lead_date, l.asr_no, l.priority,l.source,l.party_id,l.business_type';
        
        $table                    = 'leads_history lh';
         $join['leads l'] = 'l.lead_id=lh.lead_id';
        $join['customer c']       = 'l.party_id=c.customer_id';
        $join['lead_group g']     = 'l.group=g.lead_group_id';
        $join['lead_stages st']   = 'l.stages=st.lead_stages_id';
        $join['lead_source s']    = 'l.source=s.lead_source_id';
        $join['lead_business lb'] = 'l.business_type=lb.lead_business_id';
        $join['users u']          = 'lh.assign_to=u.id';
        $join['users ur']          = 'lh.added_user_id=ur.id';
       

// $where=array(

//             'l.branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'),

//             'l.delete_status'=>0
        //             );

        $condition = '(l.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and l.delete_status=0)';

        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {
            $condition_type = 'asr_no';
        }
        else

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {
            $condition_type = 'customer';
        }
        else

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {
            $condition_type = 'group';
        }
        else

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {
            $condition_type = 'stages';
        }
        else

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {
            $condition_type = 'lead_from';
        }
        else

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {
            $condition_type = 'lead_to';
        }
        else

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {
            $condition_type = 'next_action_from';
        }
        else

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {
            $condition_type = 'next_action_to';
        }

        else

        if ($search_data['assign'] != "" && $search_data['assign'] != null)
        {
            $condition_type = 'assign';
        }


        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {

            if ($condition_type == 'asr_no')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.asr_no = '" . $search_data['asr_no'] . "'";
        }

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {

            if ($condition_type == 'customer')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.party_id = '" . $search_data['customer'] . "'";
        }

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {

            if ($condition_type == 'group')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.group = '" . $search_data['group'] . "'";
        }

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {

            if ($condition_type == 'stages')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.stages = '" . $search_data['stages'] . "'";
        }

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {

            if ($condition_type == 'lead_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date >= '" . $search_data['lead_from'] . "'";
        }

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {

            if ($condition_type == 'lead_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date <= '" . $search_data['lead_to'] . "'";
        }

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {

            if ($condition_type == 'next_action_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date >= '" . $search_data['next_action_from'] . "'";
        }

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {

            if ($condition_type == 'next_action_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date <= '" . $search_data['next_action_to'] . "'";
        }

        if ($search_data['assign'] != "" && $search_data['assign'] != null && $search_data['assign'] == 'Current_User')
        {
            $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
            //$condition .= "l.assign <= '" . $search_data['assign'] . "'";
            //echo $condition;

        }


        if (($search_data['asr_no'] != "" && $search_data['asr_no'] != null) || ($search_data['customer'] != "" && $search_data['customer'] != null) || ($search_data['group'] != "" && $search_data['group'] != null) || ($search_data['stages'] != "" && $search_data['stages'] != null) || ($search_data['lead_from'] != "" && $search_data['lead_from'] != null) || ($search_data['lead_to'] != "" && $search_data['lead_to'] != null) || ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null) || ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null))
        {
            $condition .= ')';
        }

        $where = $condition;

        $order = ['c.customer_id' => 'asc',
            "l.lead_id" => "asc"];
        $filter = array(
            'l.lead_date',
            'l.asr_no',
            'c.customer_code',
            'c.customer_name',
            'l.next_action_date',
            'g.lead_group_name',
            'st.lead_stages_name',
            's.lead_source_name',
            'lb.lead_business_type',
            'l.priority',
            'u.first_name',
            'u.last_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;

    }

    public function leads_report_list_field($search_data = array(),$user_type = '')
    {
        $string                   = 'l.*,c.customer_code,c.customer_name,u.first_name,u.last_name,g.lead_group_name,st.lead_stages_name,s.lead_source_name,lb.lead_business_type';
        $table                    = 'leads l';
        $join['customer c']       = 'l.party_id=c.customer_id';
        $join['lead_group g']     = 'l.group=g.lead_group_id';
        $join['lead_stages st']   = 'l.stages=st.lead_stages_id';
        $join['lead_source s']    = 'l.source=s.lead_source_id';
        $join['lead_business lb'] = 'l.business_type=lb.lead_business_id';
        $join['users u']          = 'l.assign_to=u.id';

// $where=array(

//             'l.branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'),

//             'l.delete_status'=>0
        //             );

        $condition = '(l.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and l.delete_status=0)';

         if($user_type != 'All_User'){ 
             $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
         }

        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {
            $condition_type = 'asr_no';
        }
        else

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {
            $condition_type = 'customer';
        }
        else

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {
            $condition_type = 'group';
        }
        else

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {
            $condition_type = 'stages';
        }
        else

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {
            $condition_type = 'lead_from';
        }
        else

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {
            $condition_type = 'lead_to';
        }
        else

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {
            $condition_type = 'next_action_from';
        }
        else

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {
            $condition_type = 'next_action_to';
        }

        else

        if ($search_data['assign'] != "" && $search_data['assign'] != null)
        {
            $condition_type = 'assign';
        }


        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {

            if ($condition_type == 'asr_no')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.asr_no = '" . $search_data['asr_no'] . "'";
        }

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {

            if ($condition_type == 'customer')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.party_id = '" . $search_data['customer'] . "'";
        }

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {

            if ($condition_type == 'group')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.group = '" . $search_data['group'] . "'";
        }

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {

            if ($condition_type == 'stages')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.stages = '" . $search_data['stages'] . "'";
        }

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {

            if ($condition_type == 'lead_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date >= '" . $search_data['lead_from'] . "'";
        }

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {

            if ($condition_type == 'lead_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date <= '" . $search_data['lead_to'] . "'";
        }

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {

            if ($condition_type == 'next_action_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date >= '" . $search_data['next_action_from'] . "'";
        }

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {

            if ($condition_type == 'next_action_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date <= '" . $search_data['next_action_to'] . "'";
        }

        if ($search_data['assign'] != "" && $search_data['assign'] != null && $search_data['assign'] == 'Current_User')
        {
            $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
            //$condition .= "l.assign <= '" . $search_data['assign'] . "'";
            //echo $condition;

        }


        if (($search_data['asr_no'] != "" && $search_data['asr_no'] != null) || ($search_data['customer'] != "" && $search_data['customer'] != null) || ($search_data['group'] != "" && $search_data['group'] != null) || ($search_data['stages'] != "" && $search_data['stages'] != null) || ($search_data['lead_from'] != "" && $search_data['lead_from'] != null) || ($search_data['lead_to'] != "" && $search_data['lead_to'] != null) || ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null) || ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null))
        {
            $condition .= ')';
        }

        $where = $condition;

        $order = [
            "l.lead_id" => "desc"];
        $filter = array(
            'l.lead_date',
            'l.asr_no',
            'c.customer_code',
            'c.customer_name',
            'l.next_action_date',
            'g.lead_group_name',
            'st.lead_stages_name',
            's.lead_source_name',
            'lb.lead_business_type',
            'l.priority',
            'u.first_name',
            'u.last_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;

    }

    public function delete_leads_report_list_field($search_data = array(),$user_type = '')
    {
        $string                   = 'l.*,c.customer_code,c.customer_name,u.first_name,u.last_name,g.lead_group_name,st.lead_stages_name,s.lead_source_name,lb.lead_business_type';
        $table                    = 'leads l';
        $join['customer c']       = 'l.party_id=c.customer_id';
        $join['lead_group g']     = 'l.group=g.lead_group_id';
        $join['lead_stages st']   = 'l.stages=st.lead_stages_id';
        $join['lead_source s']    = 'l.source=s.lead_source_id';
        $join['lead_business lb'] = 'l.business_type=lb.lead_business_id';
        $join['users u']          = 'l.assign_to=u.id';

// $where=array(

//             'l.branch_id'=>$this->ci->session->userdata('SESS_BRANCH_ID'),

//             'l.delete_status'=>0
        //             );

        $condition = '(l.branch_id=' . $this->ci->session->userdata('SESS_BRANCH_ID') . ' and l.delete_status=1)';

         if($user_type != 'All_User'){ 
             $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
         }

        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {
            $condition_type = 'asr_no';
        }
        else

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {
            $condition_type = 'customer';
        }
        else

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {
            $condition_type = 'group';
        }
        else

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {
            $condition_type = 'stages';
        }
        else

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {
            $condition_type = 'lead_from';
        }
        else

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {
            $condition_type = 'lead_to';
        }
        else

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {
            $condition_type = 'next_action_from';
        }
        else

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {
            $condition_type = 'next_action_to';
        }

        else

        if ($search_data['assign'] != "" && $search_data['assign'] != null)
        {
            $condition_type = 'assign';
        }


        if ($search_data['asr_no'] != "" && $search_data['asr_no'] != null)
        {

            if ($condition_type == 'asr_no')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.asr_no = '" . $search_data['asr_no'] . "'";
        }

        if ($search_data['customer'] != "" && $search_data['customer'] != null)
        {

            if ($condition_type == 'customer')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.party_id = '" . $search_data['customer'] . "'";
        }

        if ($search_data['group'] != "" && $search_data['group'] != null)
        {

            if ($condition_type == 'group')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.group = '" . $search_data['group'] . "'";
        }

        if ($search_data['stages'] != "" && $search_data['stages'] != null)
        {

            if ($condition_type == 'stages')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.stages = '" . $search_data['stages'] . "'";
        }

        if ($search_data['lead_from'] != "" && $search_data['lead_from'] != null)
        {

            if ($condition_type == 'lead_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date >= '" . $search_data['lead_from'] . "'";
        }

        if ($search_data['lead_to'] != "" && $search_data['lead_to'] != null)
        {

            if ($condition_type == 'lead_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.lead_date <= '" . $search_data['lead_to'] . "'";
        }

        if ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null)
        {

            if ($condition_type == 'next_action_from')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date >= '" . $search_data['next_action_from'] . "'";
        }

        if ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null)
        {

            if ($condition_type == 'next_action_to')
            {
                $condition .= ' and ( ';
            }
            else
            {
                $condition .= ' or ';
            }

            $condition .= "l.next_action_date <= '" . $search_data['next_action_to'] . "'";
        }

        if ($search_data['assign'] != "" && $search_data['assign'] != null && $search_data['assign'] == 'Current_User')
        {
            $condition .= ' and l.assign_to='.$this->ci->session->userdata('SESS_USER_ID');
            //$condition .= "l.assign <= '" . $search_data['assign'] . "'";
            //echo $condition;

        }


        if (($search_data['asr_no'] != "" && $search_data['asr_no'] != null) || ($search_data['customer'] != "" && $search_data['customer'] != null) || ($search_data['group'] != "" && $search_data['group'] != null) || ($search_data['stages'] != "" && $search_data['stages'] != null) || ($search_data['lead_from'] != "" && $search_data['lead_from'] != null) || ($search_data['lead_to'] != "" && $search_data['lead_to'] != null) || ($search_data['next_action_from'] != "" && $search_data['next_action_from'] != null) || ($search_data['next_action_to'] != "" && $search_data['next_action_to'] != null))
        {
            $condition .= ')';
        }

        $where = $condition;

        $order = [
            "l.lead_id" => "desc"];
        $filter = array(
            'l.lead_date',
            'l.asr_no',
            'c.customer_code',
            'c.customer_name',
            'l.next_action_date',
            'g.lead_group_name',
            'st.lead_stages_name',
            's.lead_source_name',
            'lb.lead_business_type',
            'l.priority',
            'u.first_name',
            'u.last_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;

    }

    public function leads_added_user_list_field()
    {
        $string          = 'ls.added_user_id,u.first_name,u.last_name';
        $table           = 'users u';
        $join['leads_history ls'] = 'ls.added_user_id=u.id';

        $where = array(
            'ls.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'ls.delete_status' => 0
        );

        $order = [
            "u.first_name" => "desc"];
        $filter = array(
            'u.first_name');

        $group = array(
            'ls.added_user_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;

    }

    public function leads_assigned_user_list_field()
    {
        $string          = 'ls.assign_to,u.first_name,u.last_name';
        $table           = 'users u';
        $join['leads_history ls'] = 'ls.assign_to=u.id';

        $where = array(
            'ls.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'ls.delete_status' => 0
        );

        $order = [
            "u.first_name" => "desc"];
        $filter = array(
            'u.first_name');

        $group = array(
            'ls.assign_to');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;

    }


    public function deleted_asr_no()
    {
        $string = "l.lead_id,l.asr_no";
        $table  = "leads l";

        $where = array(
            'l.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'l.delete_status' => 1);
        $group = array(
            'l.lead_id');

        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'group'  => $group
        );
        return $data;

    }

     
 public function transaction_purpose_field(){
        $string = "op.*,t.input_type,t.voucher_type,t.transaction_category,t.id as trans_id";
        $table  = "tbl_transaction_purpose_option op";
        $join   = ["tbl_transaction_purpose t" => "t.id=op.parent_id"];
        $where = array('op.delete_status' => 0, 't.status' => 1,'op.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $order = array('op.purpose_option');
        $filter = array('op.purpose_option');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function transaction_purpose_field_det($id)
    {
        $string = "op.*,t.input_type,t.voucher_type,t.transaction_category,t.id as trans_id,t.transaction_purpose";
        $table  = "tbl_transaction_purpose_option op";
        $join   = ["tbl_transaction_purpose t" => "t.id=op.parent_id"];
        $where = array('t.status' => 1,'op.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'), 'op.id' => $id);
        $order = array('op.purpose_option');
        $filter = array('op.purpose_option');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    /*$order_ser='',$dir = ''*/
    public function journal_voucher_list_field(){
        $string = "gv.journal_voucher_id, gv.voucher_number, gv.voucher_date, gv.amount, gv.interest_expense_amount, gv.from_account, gv.to_account,gv.transaction_purpose_id, tp.purpose_option";
        $table  = "tbl_journal_voucher gv";
        $join   = ['tbl_transaction_purpose_option tp' => 'gv.transaction_purpose_id = tp.id'];
        $order = [ 'gv.journal_voucher_id' => 'desc' ];
        //$join   = ['currency cur' => 'gv.currency_id = cur.currency_id'],cur.currency_name, cur.currency_symbol, cur.currency_code, cur.currency_text;
        /*if($order_ser =='' || $dir == ''){
                $order = [ 'gv.general_voucher_id' => 'desc' ];
            }else{
                $order = [ $order_ser => $dir ];
            }*/
        $where = array(
            'gv.delete_status'     => 0,
            'gv.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'gv.financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID')
        );
        $filter = array(
            'gv.interest_expense_amount',
            'gv.voucher_number',
            'DATE_FORMAT(gv.voucher_date, "%d-%m-%Y")',
            'gv.amount',
            'tp.purpose_option');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function shareholder_list_field(){
        $string = 'shar.*';
        $table  = ' tbl_shareholder shar';
        $join   = ['users u' => 'shar.added_user_id=u.id'. '#' . 'left'];
       
        $where = array(
            'shar.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'shar.delete_status' => 0
        );
        $order = [
            "shar.id" => "desc"];
        $filter = array(
            'shar.sharholder_name',
            'shar.sharholder_code'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function deposit_list_field(){
        $string = 'shar.*';
        $table  = ' tbl_deposit shar';
        $join   = ['users u' => 'shar.added_user_id=u.id'. '#' . 'left'];
       
        $where = array(
            'shar.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'shar.delete_status' => 0
        );
        $order = [
            "shar.deposit_id" => "desc"];
        $filter = array(
            'shar.deposit_type',
            'shar.others_name',
            'shar.deposit_bank',            
            'DATE_FORMAT(shar.deposit_date, "%d-%m-%Y")',
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function fixed_asset_list_field(){
        $string = 'shar.*';
        $table  = ' tbl_fixed_assets shar';
        $join   = ['users u' => 'shar.added_user_id=u.id'. '#' . 'left'];
       
        $where = array(
            'shar.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'shar.delete_status' => 0
        );
        $order = [
            "shar.fixed_assets_id" => "desc"];
        $filter = array(
            'shar.fixed_assets_code',
            'shar.particulars',
            'shar.name_of_assets_purchase',
            'shar.rate_depreciation_company_act',
            'shar.rate_depreciation_income_tax',
            'DATE_FORMAT(shar.date_of_use, "%d-%m-%Y")',
            'DATE_FORMAT(shar.date_purchase, "%d-%m-%Y")'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function investment_list_field(){
        $string = 'shar.*';
        $table  = ' tbl_investments shar';
        $join   = ['users u' => 'shar.added_user_id=u.id'. '#' . 'left'];
       
        $where = array(
            'shar.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'shar.delete_status' => 0
        );
        $order = [
            "shar.investments_id" => "desc"];
        $filter = array(
            'shar.investments_type',
            'shar.investments_code'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }   

    public function loan_list_field(){
        $string = 'shar.*';
        $table  = ' tbl_loans shar';
        $join   = ['users u' => 'shar.added_user_id=u.id'. '#' . 'left'];
       
        $where = array(
            'shar.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'shar.delete_status' => 0
        );
        $order = [
            "shar.loan_id" => "desc"];
        $filter = array(
            'shar.others_name',
            'shar.loan_code'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function postCurlData($uri,$post_ary){

        $curl = curl_init();
        curl_setopt_array($curl, array(

          //CURLOPT_URL => "http://localhost/fashnett/wp-admin/admin.php?page=Product_API",
          CURLOPT_URL => $uri,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($post_ary),
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json",
          ),
        ));
        //"postman-token: f0021dc7-a9b3-3584-ed54-7caf32911882"

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        print_r($response);
        return $response;
    }

    public function get_sales_stock_report(){
        $string = "P.product_name, P.product_code, P.product_barcode, P.product_hsn_sac_code, P.product_id, P.product_combination_id, SI.sales_item_quantity, SI.sales_item_unit_price, S.sales_invoice_number, C.customer_name, C.customer_code, S.sales_date, SI.sales_item_discount_amount, SI.sales_item_tds_amount, SI.sales_item_igst_amount, SI.sales_item_sgst_amount, SI.sales_item_cgst_amount, SI.sales_item_tax_cess_amount, D.department_name, SD.sub_department_name, U.uom, CT.category_name, B.branch_name, B.branch_code, SC.sub_category_name,BD.brand_name,sa.store_location";
        $table  = "products P";
        $where = array(
            'P.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0 );
        $join = [
             "sales_item SI"  => "P.product_id = SI.item_id and SI.item_type = 'product'" ,
             "sales S"   => "S.sales_id = SI.sales_id",
             "department D"   => "D.department_id = S.department_id". "#" . "left",
             "sub_department SD"   => "S.sub_department_id = SD.sub_department_id". "#" . "left",
             "customer C" => "C.customer_id = S.sales_party_id",
             "shipping_address sa" => "sa.shipping_party_id = C.customer_id",
             "branch B" => "B.branch_id = P.branch_id",
            'uqc U'      => 'U.id = P.product_unit_id  and SI.item_type = "product"' . '#' . 'left',
            "category CT" => "CT.category_id=P.product_category_id",
            "sub_category SC" => "SC.sub_category_id=P.product_subcategory_id". "#" . "left",
            "brand BD" => "P.brand_id = BD.brand_id". "#" . "left"
             ];
        $group = array('S.sales_id,P.product_id');
        $filter = array(
            'P.product_name',
            'P.product_code',
            'sa.store_location',
            'SC.sub_category_name',
            'CT.category_name',
            'C.customer_code',
            'C.customer_name',
            'B.branch_name',
            'P.product_barcode',
            'P.product_hsn_sac_code',
            'S.sales_invoice_number',
            'U.uom',
            'S.sales_date',
            'SI.sales_item_discount_amount'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group
        );
        return $data;
    }


    public function get_purchase_stock_report(){
        $string = "P.product_name, P.product_code, P.product_barcode, P.product_hsn_sac_code, P.product_id, P.product_combination_id, P.product_mrp_price, PI.purchase_item_quantity, PI.purchase_item_unit_price, PS.purchase_invoice_number, C.supplier_name, C.supplier_code, PS.purchase_date, PI.purchase_item_discount_amount, PI.purchase_item_tds_amount, PI.purchase_item_igst_amount, PI.purchase_item_cgst_amount, PI.purchase_item_sgst_amount, PI.purchase_item_tax_cess_amount, D.department_name, SD.sub_department_name, U.uom, CT.category_name, B.branch_name, B.branch_code, SC.sub_category_name,BD.brand_name,PS.purchase_grn_number";
        $table  = "products P";
        $where = array(
            'P.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0 );
       
        $join = [
             "purchase_item PI"  => "P.product_id = PI.item_id and PI.item_type = 'product'" ,
             "purchase PS"   => "PS.purchase_id = PI.purchase_id",
             "department D"   => "D.department_id = PS.department_id". "#" . "left",
             "sub_department SD"   => "PS.sub_department_id = SD.sub_department_id". "#" . "left",
             "supplier C" => "C.supplier_id = PS.purchase_party_id",
             "branch B" => "B.branch_id = P.branch_id",
            'uqc U'      => 'U.id = P.product_unit_id  and PI.item_type = "product"' . '#' . 'left',
            "category CT" => "CT.category_id=P.product_category_id",
            "sub_category SC" => "SC.sub_category_id=P.product_subcategory_id". "#" . "left",
            "brand BD" => "P.brand_id = BD.brand_id". "#" . "left"
             ];
        $group = array('PS.purchase_id,P.product_id');
       $filter = array(
            'P.product_name',
            'C.supplier_code',
            'C.supplier_name',
            'CT.category_name',
            'SC.sub_category_name',
            'BD.brand_name',
            'P.product_code',
            'P.product_barcode',
            'P.product_hsn_sac_code',
            'PS.purchase_date',
            'PS.purchase_invoice_number',
            'PS.purchase_grn_number',
            'U.uom',
            'P.product_mrp_price'
        );
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group
        );
        return $data;
    }

    public function department_list_field(){
        $string = 'd.*,u.first_name,u.last_name';
        $table  = 'department d';
        $join   = ["users u" => "d.added_user_id=u.id"];

        $order = ["department_id" => "desc"];

        $where = array('d.delete_status' => 0,'d.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));

        $filter = array('d.department_code','d.department_name');

        $data = array('string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        
        return $data;
    }

    public function subdepartment_list_field(){
        $string = 's.*, d.department_name, u.first_name, u.last_name';
        $table  = 'sub_department s';
        
        $join   = ["users u" => "s.added_user_id=u.id",
            'department d' => "d.department_id=s.department_id"];

        $order = ["s.sub_department_id" => "desc"];

        $where = array('s.delete_status' => 0,
            's.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'));
        $filter = array('s.sub_department_code',
            's.sub_department_name','d.department_name');
        $data = array('string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function get_closing_stock_report(){
        $string = "P.product_name, P.product_code, P.product_barcode, P.product_hsn_sac_code, P.product_id, P.product_basic_price, P.product_quantity, P.product_combination_id, (select SUM(SI.sales_item_quantity) from sales_item SI where P.product_id = SI.item_id and SI.item_type = 'product') as sales_qty, AVG(SI.sales_item_unit_price) as price, SUM(SI.sales_item_igst_amount) as igst, SUM(SI.sales_item_sgst_amount) as sgst, SUM(SI.sales_item_cgst_amount) as cgst, D.department_name, SD.sub_department_name, U.uom, CT.category_name, B.branch_name, B.branch_code, SC.sub_category_name, BD.brand_name, P.product_opening_quantity, (select SUM(PI.purchase_item_quantity) from purchase_item PI where `P`.`product_id` = `PI`.`item_id` and `PI`.`item_type` = 'product') as purchase_qty, PI.purchase_id, P.product_batch, C.supplier_name,C.supplier_code, sa.store_location, P.product_mrp_price, P.product_selling_price, P.product_price, AVG(PI.purchase_item_unit_price) as purchase_price";
        $table  = "products P";
        $where = array(
            'P.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'  => 0,
            'P.product_combination_id != ' => NULL );
        $join = [
             "purchase_item PI"  => "P.product_id = PI.item_id and PI.item_type = 'product'". "#" . "left" ,
             "purchase PU"   => "PU.purchase_id = PI.purchase_id". "#" . "left",
             "sales_item SI"  => "P.product_id = SI.item_id and SI.item_type = 'product'". "#" . "left" ,
             "supplier C" => "C.supplier_id = PU.purchase_party_id". "#" . "left" ,
             "shipping_address sa" => "sa.shipping_party_id = C.supplier_id". "#" . "left" ,
             "department D"   => "D.department_id = PU.department_id". "#" . "left",
        "sub_department SD" => "PU.sub_department_id = SD.sub_department_id". "#" . "left",            
             "branch B" => "B.branch_id = P.branch_id",
            'uqc U' => 'U.id = P.product_unit_id' . '#' . 'left',
            "category CT" => "CT.category_id=P.product_category_id",
            "sub_category SC" => "SC.sub_category_id=P.product_subcategory_id". "#" . "left",
            "brand BD" => "P.brand_id = BD.brand_id". "#" . "left"
             ];
        $group = array('P.product_id');
        $filter = array(
                        'P.product_name',
                        'C.supplier_name',
                        'sa.store_location',
                        'CT.category_name',
                        'SC.sub_category_name',
                        'BD.brand_name',
                        'P.product_code',
                        'P.product_batch',
                        'P.product_barcode',
                        'P.product_hsn_sac_code',
                        'U.uom',
                        'P.product_opening_quantity'
                    );
        
        $order = ["PI.purchase_id" => "desc"];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }

    public function hsn_list_item_field1($sales_id){

        $string = "sum(SI.sales_item_taxable_value) as sales_item_taxable_value, S.sales_id, SI.sales_item_igst_percentage, SI.sales_item_tax_percentage, SI.sales_item_sgst_percentage,sum(SI.sales_item_sub_total) as sales_item_sub_total, sum(SI.sales_item_quantity) as sales_item_quantity, SI.sales_item_cgst_percentage, SI.sales_item_tax_cess_percentage, sum(SI.sales_item_igst_amount) as sales_item_igst_amount, sum(SI.sales_item_sgst_amount) as sales_item_sgst_amount, sum(SI.sales_item_cgst_amount) as sales_item_cgst_amount, sum(SI.sales_item_tax_cess_amount) as sales_item_tax_cess_amount, 
            CASE SI.item_type when 'product' then P.product_hsn_sac_code 
                when 'service' then SR.service_hsn_sac_code 
                END as  hsn_sac_code, ";
        $table  = "sales_item SI";
        $join   = ['sales S' => 'S.sales_id = SI.sales_id', 
                'products P'   => 'SI.item_id = P.product_id  and SI.item_type = "product"' . '#' . 'left',
                'services SR'   => 'SI.item_id = SR.service_id and SI.item_type = "service"' . '#' . 'left',
                'hsn hs' => '(hs.hsn_code = P.product_hsn_sac_code) OR (hs.hsn_code = SR.service_hsn_sac_code)'. '#' . 'left'];
        $group = array('hsn_sac_code');
        $order = array();
        $filter = array();
        $where['S.sales_id'] = $sales_id;
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order,
            'group'  => $group
        );
        return $data;
    }

    public function get_invoice_brand_report(){
        $string = "S.*, ST.state_name as place_of_supply, BR.brand_name, C.customer_name, B.branch_name, B.branch_code";
        $table  = "sales S";
        $where = array(
            'S.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'S.delete_status' => 0 );
        $join = [
             "states ST" => "S.sales_billing_state_id = ST.state_id". "#" . "left",
             "brand BR"  => "BR.brand_id = S.brand_id". "#" . "left",
             "customer C" => "C.customer_id = S.sales_party_id",
             "branch B" => "B.branch_id = S.branch_id"
             ];
        $group = array('S.sales_id,BR.brand_id');
        $filter = array('C.customer_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group
        );
        return $data;
    }

    public function get_brandwise_sales_stock_report(){
        $string = "P.product_name, P.product_code, P.product_hsn_sac_code, P.product_id, P.product_combination_id, SI.sales_item_quantity, SI.sales_item_unit_price, S.sales_invoice_number, C.customer_name, S.sales_date, SI.sales_item_discount_amount, SI.sales_item_tds_amount, SI.sales_item_igst_amount, SI.sales_item_sgst_amount, SI.sales_item_cgst_amount, SI.sales_item_tax_cess_amount, U.uom, CT.category_name, B.branch_name, B.branch_code, SC.sub_category_name, CASE P.brand_id when '0' then 'General' ELSE BR.brand_name END as brand_name, SI.sales_item_scheme_discount_amount, SI.sales_item_scheme_discount_percentage, dt.discount_value as item_discount_percentage";
        $table  = "products P";
        $where = array(
            'P.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status' => 0 );
        $join = [
             "sales_item SI"  => "P.product_id = SI.item_id and SI.item_type = 'product'" ,
             "brand BR" => "BR.brand_id = P.brand_id". "#" . "left",
             "sales S"  => "S.sales_id = SI.sales_id",             
             "customer C" => "C.customer_id = S.sales_party_id",
             "branch B" => "B.branch_id = P.branch_id",
            'uqc U'     => 'U.id = SI.sales_item_uom_id  and SI.item_type = "product"' . '#' . 'left',
            "category CT" => "CT.category_id=P.product_category_id",
            'discount dt' => 'dt.discount_id = SI.sales_item_discount_id' . '#' . 'left',
            "sub_category SC" => "SC.sub_category_id=P.product_subcategory_id". "#" . "left",
             ];
        $group = array('S.sales_id,BR.brand_id,P.product_id');
        $filter = array('BR.brand_name','P.product_name','CT.category_name','SC.sub_category_name','P.product_code','P.product_hsn_sac_code','U.uom','DATE_FORMAT(S.sales_date, "%d-%m-%Y")',
            'C.customer_name','S.sales_invoice_number', 'SI.sales_item_quantity','SI.sales_item_scheme_discount_amount', 'SI.sales_item_scheme_discount_percentage', 'dt.discount_value','SI.sales_item_discount_amount');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group
        );
        return $data;
    }


    public function get_brandwise_purchase_stock_report(){
        $string = "P.product_name, P.product_code, P.product_hsn_sac_code, P.product_id, P.product_combination_id, PI.purchase_item_quantity, PI.purchase_item_unit_price, PS.purchase_invoice_number, C.supplier_name, PS.purchase_date, PI.purchase_item_discount_amount, PI.purchase_item_tds_amount, PI.purchase_item_igst_amount, PI.purchase_item_cgst_amount, PI.purchase_item_sgst_amount, PI.purchase_item_tax_cess_amount, U.uom, CT.category_name, CASE P.brand_id when '0' then 'General' ELSE BR.brand_name END as brand_name,  SC.sub_category_name";
        $table  = "products P";
        $where = array(
            'P.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0 );
       
        $join = [
            "purchase_item PI"  => "P.product_id = PI.item_id and PI.item_type = 'product'",
             "brand BR"   => "BR.brand_id = P.brand_id". "#" . "left",
             "purchase PS"   => "PS.purchase_id = PI.purchase_id",
             "supplier C" => "C.supplier_id = PS.purchase_party_id",
             "branch B" => "B.branch_id = P.branch_id",
            'uqc U'      => 'U.id = P.product_unit_id  and PI.item_type = "product"' . '#' . 'left',
            "category CT" => "CT.category_id=P.product_category_id",
            "sub_category SC" => "SC.sub_category_id=P.product_subcategory_id". "#" . "left",
             ];
        $group = array('PS.purchase_id,BR.brand_id,P.product_id');
       $filter = array('BR.brand_name','P.product_name','CT.category_name','SC.sub_category_name','P.product_code','P.product_hsn_sac_code','U.uom','DATE_FORMAT(PS.purchase_date, "%d-%m-%Y")',
            'C.supplier_name','PS.purchase_invoice_number', 'PI.purchase_item_quantity');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group
        );
        return $data;
    }

    public function get_brandwise_closing_stock_report(){
        $string = "P.product_name, P.product_code, P.product_hsn_sac_code, P.product_id,P.product_price,P.product_batch, P.product_basic_price, P.product_quantity, P.product_combination_id, U.uom, CT.category_name, CASE P.brand_id when '0' then 'General' ELSE BR.brand_name END as brand_name,  SC.sub_category_name,P.product_opening_quantity,P.brand_id";
        //SI.sales_item_quantity, AVG(SI.sales_item_unit_price) as price, SUM(SI.sales_item_igst_amount) as igst, SUM(SI.sales_item_sgst_amount) as sgst, SUM(SI.sales_item_cgst_amount) as cgst,
        $table  = "products P";
        $where = array(
            'P.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0 );
        $join = [
             /*"sales_item SI"  => "P.product_id = SI.item_id and SI.item_type = 'product'" . "#" . "left",
             "sales S"   => "S.sales_id = SI.sales_id". "#" . "left",*/           
             "brand BR"   => "BR.brand_id = P.brand_id". "#" . "left",         
             "branch B" => "B.branch_id = P.branch_id",
            'uqc U' => 'U.id = P.product_unit_id' . '#' . 'left',// and SI.item_type = "product"
            "category CT" => "CT.category_id=P.product_category_id",
            "sub_category SC" => "SC.sub_category_id=P.product_subcategory_id". "#" . "left",
             ];
        $group = array('P.product_id');//BR.brand_id,
        $filter = array('BR.brand_name','P.product_name','CT.category_name','SC.sub_category_name','P.product_code','P.product_hsn_sac_code','U.uom');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group
        );
        return $data;
    }


    public function get_stock_movement(){
        $string = "P.product_id, P.product_quantity, P.product_sku, P.product_code, P.product_name, P.product_price as price, P.product_opening_quantity, SUM(S.sales_item_quantity) as sales_qty, SUM(PU.purchase_item_quantity) as purchase_qty, SUM(SC.sales_credit_note_item_quantity) as sales_credit_qty, SUM(SD.sales_debit_note_item_quantity) as sales_debit_qty, SUM(PUD.purchase_debit_note_item_quantity) as purchase_debit_qty, SUM(PUC.purchase_credit_note_item_quantity) as purchase_credit_qty, P.product_selling_price, AVG(PU. purchase_item_unit_price) as purchase_price, AVG(S.sales_item_unit_price) as sales_price";
        $table  = "products P";
        $order = ["P.product_id" => "desc"];
        $where = array(
            'P.branch_id'         => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0 );
        $join = [
            "purchase_item PU" => "PU.item_id = P.product_id and PU.item_type = 'product'",
            "sales_item S" => "P.product_id = S.item_id and S.item_type = 'product'",
            "sales_credit_note_item SC" => "SC.item_id = P.product_id and SC.item_type = 'product'" . "#" . "left",
            "sales_debit_note_item SD" => "SD.item_id = P.product_id and SD.item_type = 'product'" . "#" . "left",
            "purchase_debit_note_item PUD" => "PUD.item_id = P.product_id and PUD.item_type = 'product'" . "#" . "left",
            "purchase_credit_note_item PUC" => "PUC.item_id = P.product_id and PUC.item_type = 'product'" . "#" . "left",
        ];

        $group = array('P.product_id');
        $filter = array('P.product_name');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'  => $join,
            'filter' => $filter,
            'group'  => $group,
            'order'  => $order
        );
        return $data;
    }

    public function general_ledger_details($journal_voucher_id){
        $string = "sv.voucher_number, sv.voucher_date, av.journal_voucher_id, av.cr_amount, av.dr_amount, l.ledger_name as from_name, l.ledger_name, l.ledger_name as to_name,  av.journal_voucher_id, sv.voucher_type, tp.purpose_option";
        $table  = "accounts_journal_voucher av";
        $join   = [
            'tbl_journal_voucher sv' => 'sv.journal_voucher_id = av.journal_voucher_id',
            'tbl_transaction_purpose_option tp' => 'sv.transaction_purpose_id = tp.id',
            'tbl_ledgers l'        => 'l.ledger_id = av.ledger_id'];
        $where = [
            'av.journal_voucher_id' => $journal_voucher_id,
            'av.delete_status'    => 0];
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join
        );
        return $data;
    }

    public function purchase_product_list_field($id){
        $string = 'pr.product_barcode, pr.product_code, pr.product_mrp_price, pr.product_hsn_sac_code, pr.product_combination_id, PI.purchase_item_quantity, CT.category_name, SC.sub_category_name, BD.brand_name, PI.item_id';
        $table  = 'purchase_item PI';
        $join = [
                "products pr"  => "pr.product_id = PI.item_id and PI.item_type = 'product'" ,
                "category CT" => "CT.category_id=pr.product_category_id",
                "sub_category SC" => "SC.sub_category_id=pr.product_subcategory_id". "#" . "left",
                "brand BD" => "pr.brand_id = BD.brand_id". "#" . "left"
                ];
        $where = array(
            'pr.branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'PI.purchase_id' => $id
        );
        $order = ["PI.item_id" => "asc"];
        $filter = array();
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'filter' => $filter,
            'order'  => $order
        );
        return $data;
    }

    public function distinct_brand_closing(){
        $string = "CASE P.brand_id when '0' then 'General' ELSE BR.brand_name END as brand_name,P.brand_id";
        $table  = "products P";
        $join   = [
            "brand BR"   => "BR.brand_id = P.brand_id". "#" . "left",         
            "sales_item SI"  => "P.product_id = SI.item_id and SI.item_type = 'product'" ,
             "sales S"   => "S.sales_id = SI.sales_id"];
        $where = array('P.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0);
        $group = array('P.brand_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

    public function distinct_brand_sales(){
        $string = "CASE P.brand_id when '0' then 'General' ELSE BR.brand_name END as brand_name,P.brand_id";
        $table  = "products P";
        $join   = [
            "brand BR"   => "BR.brand_id = P.brand_id". "#" . "left",         
            "sales_item SI"  => "P.product_id = SI.item_id and SI.item_type = 'product'" ,
             "sales S"   => "S.sales_id = SI.sales_id"];
        $where = array('P.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0);
        $group = array('P.brand_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }

      public function distinct_brand_purchase(){
        $string = "CASE P.brand_id when '0' then 'General' ELSE BR.brand_name END as brand_name,P.brand_id";
        $table  = "products P";
        $join   = [
            "brand BR"   => "BR.brand_id = P.brand_id". "#" . "left",         
            "purchase_item PI"  => "P.product_id = PI.item_id and PI.item_type = 'product'",
             "purchase PS"   => "PS.purchase_id = PI.purchase_id",];
        $where = array('P.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
            'P.delete_status'     => 0);
        $group = array('P.brand_id');
        $data = array(
            'string' => $string,
            'table'  => $table,
            'where'  => $where,
            'join'   => $join,
            'group'  => $group
        );
        return $data;
    }
}
