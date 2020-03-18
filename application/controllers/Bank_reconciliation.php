<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_reconciliation extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'bank_statement_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string        = 'u.id,u.first_name,u.last_name';
        $from          = 'users u';
        $where         = array(
                'u.delete_status' => 0,
                'u.branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'u.id !='         => 1,
                'u.active'        => 1
        );
        $data['users'] = $this->general_model->getRecords($string, $from, $where);

        $string       = 'b.*,l.*';
        $from         = 'bank_account b';
        $join         = array(
                'ledgers l' => 'l.ledger_id = b.ledger_id' );
        $where        = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID')
        );
        $data['bank'] = $this->general_model->getJoinRecords($string, $from, $where, $join);

        $this->load->view('bank_reconciliation/bank_reconciliation', $data);
    }

    public function list_data()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string        = 'u.id,u.first_name,u.last_name';
        $from          = 'users u';
        $where         = array(
                'u.delete_status' => 0,
                'u.branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'u.id !='         => 1,
                'u.active'        => 1
        );
        $data['users'] = $this->general_model->getRecords($string, $from, $where);

        $string            = 'b.*,l.*';
        $table             = 'bank_account b';
        $join['ledgers l'] = 'l.ledger_id=b.ledger_id';
        $where             = array(
                'b.delete_status' => 0,
                'b.branch_id'     => $this->session->userdata('SESS_BRANCH_ID')
        );
        $data['bank']      = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $user_id        = $this->input->post('user_id');
        // echo "<pre>";
        // print_r($user_id);
        // exit;
        $bank_ledger_id = $this->input->post('bank_ledger_id');
        if ($this->input->post('from_date') == '')
        {
            $financial_year = explode('-', $this->session->userdata('SESS_FINANCIAL_YEAR_TITLE'));
            $month          = $this->input->post('month');

            if ($month == '01' || $month == '02' || $month == '03')
            {
                $year = $financial_year[1];
            }
            else
            {
                $year = $financial_year[0];
            }

            $last_day = date('t', strtotime($month . '/01/2018'));

            $from_date = $year . '-' . $month . '-01';
            $to_date   = $year . '-' . $month . '-' . $last_day;
        }
        else
        {
            $this->session->set_userdata('advance_search', 'true');
            $from_date = $this->input->post('from_date');
            $to_date   = $this->input->post('to_date');
        }

        if ($user_id == "" && $bank_ledger_id == "")
        {
            $user_id        = $this->session->userdata('user_id');
            $bank_ledger_id = $this->session->userdata('bank_ledger_id');
            $from_date      = $this->session->userdata('from_date');
            $to_date        = $this->session->userdata('to_date');

            $post_data = array(
                    'user_id'        => $user_id,
                    'bank_ledger_id' => $bank_ledger_id,
                    'from_date'      => $from_date,
                    'to_date'        => $to_date
            );
        }
        else
        {
            $post_data = array(
                    'user_id'        => $user_id,
                    'bank_ledger_id' => $bank_ledger_id,
                    'from_date'      => $from_date,
                    'to_date'        => $to_date
            );
        }

        $data['post_data'] = $post_data;

        $this->session->set_userdata('user_id', $user_id);
        $this->session->set_userdata('bank_ledger_id', $bank_ledger_id);
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);

        $condition_rv = '(';
        $condition_av = '(';
        $condition_rf = '(';
        $condition_pv = '(';
        $condition_cv = '(';
        $i            = 0;
        foreach ($user_id as $key => $value)
        {
            if ($i == 0)
            {
                $condition_rv .= 'rv.added_user_id = ' . $value;
                $condition_av .= 'av.added_user_id = ' . $value;
                $condition_rf .= 'rv.added_user_id = ' . $value;
                $condition_pv .= 'pv.added_user_id = ' . $value;
                $condition_cv .= 'cv.added_user_id = ' . $value;

                $i = 1;
            }
            else
            {
                $condition_rv .= ' or rv.added_user_id = ' . $value;
                $condition_av .= ' or av.added_user_id = ' . $value;
                $condition_rf .= ' or rv.added_user_id = ' . $value;
                $condition_pv .= ' or pv.added_user_id = ' . $value;
                $condition_cv .= ' or cv.added_user_id = ' . $value;
            }
        }
        $condition_rv .= ')';
        $condition_av .= ')';
        $condition_rf .= ')';
        $condition_pv .= ')';
        $condition_cv .= ')';

        /* Receipt voucher data */
        $string = "rv.*,c.customer_name";
        $table  = "receipt_voucher rv";
        $join   = [
                "customer c" => "rv.party_id = c.customer_id" ];
        $where  = "rv.delete_status = 0 and rv.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and rv.payment_mode != 'cash' and rv.currency_converted_amount > 0 and rv.voucher_status = 1 and rv.party_type = 'customer' and " . $condition_rv;

        $data['receipt_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string = "rv.*,l.ledger_title as customer_name";
        $table  = "receipt_voucher rv";
        $join   = [
                "ledgers l" => "rv.party_id = l.ledger_id" ];
        $where  = "rv.delete_status = 0 and rv.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and rv.payment_mode != 'cash' and rv.currency_converted_amount > 0 and rv.voucher_status = 1 and rv.party_type = 'ledger' and " . $condition_rv;

        $data['receipt_voucher_data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        /* Advance voucher data */
        $string = "av.*,c.customer_name";
        $table  = "advance_voucher av";
        $join   = [
                "customer c" => "av.party_id = c.customer_id" ];
        // $where	=	array('av.delete_status' =>0,
        //              'av.branch_id' =>$this->session->userdata('SESS_BRANCH_ID'),
        //              'av.payment_mode !='=> 'cash',
        //              'av.currency_converted_amount >' => 0,
        //              'av.added_user_id' => $user_id,
        //              'av.voucher_status' => 1
        //      	);
        $where  = "av.delete_status = 0 and av.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and av.payment_mode != 'cash' and av.currency_converted_amount > 0 and av.voucher_status = 1 and " . $condition_av;

        $data['advance_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        /* Refund voucher data */
        $string = "rv.*,c.customer_name";
        $table  = "refund_voucher rv";
        $join   = [
                "customer c" => "rv.party_id = c.customer_id" ];
        // $where	=	array('rv.delete_status' => 0,
        //              'rv.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
        //              'rv.payment_mode !='=> 'cash',
        //              'rv.currency_converted_amount >' => 0,
        //              'rv.added_user_id' => $user_id,
        //              'rv.voucher_status' => 1
        //     		);
        $where  = "rv.delete_status = 0 and rv.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and rv.payment_mode != 'cash' and rv.currency_converted_amount > 0 and rv.voucher_status = 1 and " . $condition_rf;

        $data['refund_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        /* Payment voucher data */
        $string = "pv.*,s.supplier_name";
        $table  = "payment_voucher pv";
        $join   = [
                "supplier s" => "pv.party_id = s.supplier_id" ];
        $where  = "pv.delete_status = 0 and pv.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and pv.payment_mode != 'cash' and pv.currency_converted_amount > 0 and pv.voucher_status = 1 and pv.party_type = 'supplier' and " . $condition_pv;

        $data['payment_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string = "pv.*,l.ledger_title as supplier_name";
        $table  = "payment_voucher pv";
        $join   = [
                "ledgers l" => "pv.party_id = l.ledger_id" ];
        $where  = "pv.delete_status = 0 and pv.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and pv.payment_mode != 'cash' and pv.currency_converted_amount > 0 and pv.voucher_status = 1 and pv.party_type = 'ledger' and " . $condition_pv;

        $data['payment_voucher_data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $string = "cv.*,l.ledger_title";
        $table  = "contra_voucher cv";
        $join   = [
                "ledgers l" => "cv.party_id = l.ledger_id" ];
        $where  = "cv.delete_status = 0 and cv.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and cv.payment_mode != 'cash' and cv.currency_converted_amount > 0 and cv.voucher_status = 1 and cv.party_type = 'ledger' and " . $condition_cv;

        $data['contra_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $this->load->view('bank_reconciliation/bank_reconciliation', $data);
    }

    public function get_bank_statement()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);


        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $voucher_id     = $this->input->post('voucher_id');
        $voucher_type   = $this->input->post('voucher_type');
        $voucher_amount = $this->input->post('voucher_amount');
        $bank_ledger_id = $this->input->post('bank_ledger_id');
        $from_date      = $this->input->post('from_date');
        $to_date        = $this->input->post('to_date');

        $condition = '(';
        $i         = 0;
        foreach ($bank_ledger_id as $key => $value)
        {
            if ($i == 0)
            {
                $condition .= 'b.bank_ledger_id = ' . $value;
                $i         = 1;
            }
            else
            {
                $condition .= ' or b.bank_ledger_id = ' . $value;
            }
        }
        $condition .= ')';

        $string = 'b.*';
        $table  = 'bank_statement b';
        // $where             = "b.delete_status = 0 and b.branch_id = ". $this->session->userdata('SESS_BRANCH_ID')." and b.split_status = 0 and b.display_status = 0 and (b.credit = '".$voucher_amount."' or b.debit = '".$voucher_amount."') and b.bank_ledger_id = '".$bank_ledger_id."' and b.date >= '".$from_date."' and b.date <= '".$to_date."'";

        $where = "b.delete_status = 0 and b.branch_id = " . $this->session->userdata('SESS_BRANCH_ID') . " and b.split_status = 0 and b.display_status = 0 and (b.credit = '" . $voucher_amount . "' or b.debit = '" . $voucher_amount . "') and " . $condition . " and b.date >= '" . $from_date . "' and b.date <= '" . $to_date . "'";

        $bank_statement_data = $this->general_model->getRecords($string, $table, $where);

        $output = "";

        $voucher_id = $this->encryption_url->encode($voucher_id);
        foreach ($bank_statement_data as $key => $value)
        {
            $bank_statement_id = $this->encryption_url->encode($value->bank_statement_id);
            $output            .= "<tr>";
            $output            .= "<td>" . $value->date . "</td>";
            $output            .= "<td>" . $value->description . "</td>";
            $output            .= "<td>" . $value->reference_no . "</td>";
            $output            .= "<td>" . $value->debit . "</td>";
            $output            .= "<td>" . $value->credit . "</td>";
            $output            .= "<td>" . $value->closing_balance . "</td>";
            // $output .= '<td><a href="" title="Move to categorized" data-voucher_id="'.$voucher_id.'" data-voucher_type="'.$voucher_type.'" data-bank_statement_id="'.$value->bank_statement_id.'" class="store_statement"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>';
            $output            .= '<td><a href="' . base_url() . 'bank_reconciliation/store_statement/' . $voucher_id . '/' . $voucher_type . '/' . $bank_statement_id . '" title="Move to categorized"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a></td>';
            $output            .= "</tr>";
        }

        echo $output;
    }

    public function store_statement($voucher_id, $voucher_type, $bank_statement_id)
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "add_privilege";
        $data['privilege']             = "add_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);


        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $voucher_id        = $this->encryption_url->decode($voucher_id);
        $bank_statement_id = $this->encryption_url->decode($bank_statement_id);

        if ($voucher_type == "receipt_voucher")
        {
            $receipt_voucher_data = $this->general_model->getRecords('reference_type', 'receipt_voucher', array(
                    'receipt_id' => $voucher_id ));
            if ($receipt_voucher_data[0]->reference_type == 'sales')
            {
                $type = 'customer';
            }
            else
            {
                $type = 'ledger';
            }
        }
        else if ($voucher_type == "advance_voucher")
        {
            $type = 'customer_advance';
        }
        else if ($voucher_type == "refund_voucher")
        {
            $type = 'customer_refund';
        }
        else if ($voucher_type == "payment_voucher")
        {
            $payment_voucher_data = $this->general_model->getRecords('reference_type', 'payment_voucher', array(
                    'payment_id' => $voucher_id ));
            if ($payment_voucher_data[0]->reference_type == 'purchase')
            {
                $type = 'suppliers';
            }
            else if ($payment_voucher_data[0]->reference_type == 'expense')
            {
                $type = 'expense';
            }
            else
            {
                $type = 'ledger';
            }
        }
        else if ($voucher_type == "contra_voucher")
        {
            $type = 'ledger';
        }

        $categorized_statement_data = array(
                'bank_statement_id' => $bank_statement_id,
                'voucher_id'        => $voucher_id,
                'sub_statement_id'  => '0',
                'split_status'      => '0',
                'party_type'        => $type,
                'voucher_type'      => $voucher_type
        );
        $this->general_model->insertData('categorized_statement', $categorized_statement_data);

        $this->session->set_userdata('statement_id', $bank_statement_id);

        $res = 0;
        if (($type == "suppliers" || $type == "expense" || $type == "ledger") && $voucher_type == "payment_voucher")
        {
            $res = $this->bank_statement_model->updateSuppliers($voucher_id);
        }

        if (($type == "customer" || $type == "ledger") && $voucher_type == "receipt_voucher")
        {
            $res = $this->bank_statement_model->updateCustomer($voucher_id);
        }

        if ($type == "customer_advance" && $voucher_type == "advance_voucher")
        {
            $res = $this->bank_statement_model->updateCustomerAdvance($voucher_id);
        }

        if ($type == "customer_refund" && $voucher_type == "refund_voucher")
        {
            $res = $this->bank_statement_model->updateCustomerRefund($voucher_id);
        }

        if ($type == "ledger" && $voucher_type == "contra_voucher")
        {
            $res = $this->bank_statement_model->updateContra($voucher_id);
        }

        $this->session->unset_userdata('statement_id');

        redirect('bank_reconciliation/list_data');
    }

    public function view_vouchers()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "view_privilege";
        $data['privilege']             = "view_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string        = 'u.id,u.first_name,u.last_name';
        $from          = 'users u';
        $where         = array(
                'u.delete_status' => 0,
                'u.branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'u.id !='         => 1,
                'u.active'        => 1
        );
        $data['users'] = $this->general_model->getRecords($string, $from, $where);

        if ($this->input->post('user_id'))
        {
            $user_id = $this->input->post('user_id');
        }
        else
        {
            $user_id = $this->session->userdata('SESS_USER_ID');
        }
        if ($this->input->post('categorized_type'))
        {
            $categorized_type = $this->input->post('categorized_type');
        }
        else
        {
            $categorized_type = 'matched';
        }

        $data['user_id']          = $user_id;
        $data['categorized_type'] = $categorized_type;

        if ($categorized_type == 'all')
        {
            /* Receipt voucher data */
            // $string =   "rv.*,c.customer_name";
            // $table  =   "receipt_voucher rv";
            // $join   =   ["customer c"=>"rv.party_id = c.customer_id"];
            // $where  =   array('rv.delete_status' => 0,
            //                 'rv.branch_id'      => $this->session->userdata('SESS_BRANCH_ID'),
            //                 'rv.payment_mode !='=> 'cash',
            //                 'rv.currency_converted_amount >' => 0,
            //                 'rv.added_user_id'  => $user_id,
            //                 'rv.voucher_status !=' => 0
            //             );
            // $data['receipt_voucher_data'] = $this->general_model->getJoinRecords($string,$table,$where,$join);

            /* Advance voucher data */
            // $string =   "av.*,c.customer_name";
            // $table  =   "advance_voucher av";
            // $join   =   ["customer c" => "av.party_id = c.customer_id"];
            // $where  =   array('av.delete_status' =>0,
            //                 'av.branch_id' =>$this->session->userdata('SESS_BRANCH_ID'),
            //                 'av.payment_mode !='=> 'cash',
            //                 'av.currency_converted_amount >' => 0,
            //                 'av.added_user_id' => $user_id,
            //                 'av.voucher_status !=' => 0
            //             );
            // $data['advance_voucher_data']=$this->general_model->getJoinRecords($string,$table,$where,$join);

            /* Refund voucher data */
            // $string =   "rv.*,c.customer_name";
            // $table  =   "refund_voucher rv";
            // $join   =   ["customer c" => "rv.party_id = c.customer_id"];
            // $where  =   array('rv.delete_status' => 0,
            //                 'rv.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            //                 'rv.payment_mode !='=> 'cash',
            //                 'rv.currency_converted_amount >' => 0,
            //                 'rv.added_user_id' => $user_id,
            //                 'rv.voucher_status !=' => 0
            //             );
            // $data['refund_voucher_data']=$this->general_model->getJoinRecords($string,$table,$where,$join);

            /* Payment voucher data */
            // $string =   "pv.*,s.supplier_name";
            // $table  =   "payment_voucher pv";
            // $join   =   ["supplier s" => "pv.party_id = s.supplier_id"];
            // $where  =   array('pv.delete_status' => 0,
            //                 'pv.branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            //                 'pv.payment_mode !='=> 'cash',
            //                 'pv.currency_converted_amount >' => 0,
            //                 'pv.added_user_id' => $user_id,
            //                 'pv.voucher_status !=' => 0
            //             );
            // $data['payment_voucher_data']=$this->general_model->getJoinRecords($string,$table,$where,$join);

            /* Unmatch data */
            /* Receipt voucher data */
            $string                        = "rv.*,c.customer_name,bs.date";
            $table                         = "receipt_voucher rv";
            $join                          = array(
                    "customer c"               => "rv.party_id = c.customer_id",
                    "categorized_statement cs" => "rv.receipt_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                         = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'customer',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 2
            );
            $data['receipt_voucher_data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                          = "rv.*,l.ledger_title as customer_name,bs.date";
            $table                           = "receipt_voucher rv";
            $join                            = array(
                    "ledgers l"                => "rv.party_id = l.ledger_id",
                    "categorized_statement cs" => "rv.receipt_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                           = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'ledger',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 2
            );
            $data['receipt_voucher_data1_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Advance voucher data */
            $string                        = "av.*,c.customer_name,bs.date";
            $table                         = "advance_voucher av";
            $join                          = array(
                    "customer c"               => "av.party_id = c.customer_id",
                    "categorized_statement cs" => "av.advance_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id",
            );
            $where                         = array(
                    'av.delete_status'               => 0,
                    'av.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'av.payment_mode !='             => 'cash',
                    'av.currency_converted_amount >' => 0,
                    'av.added_user_id'               => $user_id,
                    'av.voucher_status'              => 2
            );
            $data['advance_voucher_data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Refund voucher data */
            $string                       = "rv.*,c.customer_name,bs.date";
            $table                        = "refund_voucher rv";
            $join                         = array(
                    "customer c"               => "rv.party_id = c.customer_id",
                    "categorized_statement cs" => "rv.refund_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id",
            );
            $where                        = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 2
            );
            $data['refund_voucher_data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Payment voucher data */
            $string                        = "pv.*,s.supplier_name,bs.date";
            $table                         = "payment_voucher pv";
            $join                          = array(
                    "supplier s"               => "pv.party_id = s.supplier_id",
                    "categorized_statement cs" => "pv.payment_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                         = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'supplier',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 2
            );
            $data['payment_voucher_data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                          = "pv.*,l.ledger_title as supplier_name,bs.date";
            $table                           = "payment_voucher pv";
            $join                            = array(
                    "ledgers l"                => "pv.party_id = l.ledger_id",
                    "categorized_statement cs" => "pv.payment_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                           = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'ledger',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 2
            );
            $data['payment_voucher_data1_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Contra voucher data */
            $string                       = "cv.*,l.ledger_title,bs.date";
            $table                        = "contra_voucher cv";
            $join                         = array(
                    "ledgers l"                => "cv.party_id = l.ledger_id",
                    "categorized_statement cs" => "cv.contra_voucher_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                        = array(
                    'cv.delete_status'               => 0,
                    'cv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'cv.payment_mode !='             => 'cash',
                    'cv.currency_converted_amount >' => 0,
                    'cv.party_type'                  => 'ledger',
                    'cv.added_user_id'               => $user_id,
                    'cv.voucher_status'              => 2
            );
            $data['contra_voucher_data1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Match data */
            /* Receipt voucher data */
            $string                       = "rv.*,c.customer_name";
            $table                        = "receipt_voucher rv";
            $join                         = [
                    "customer c" => "rv.party_id = c.customer_id" ];
            $where                        = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'customer',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 1
            );
            $data['receipt_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                         = "rv.*,l.ledger_title as customer_name";
            $table                          = "receipt_voucher rv";
            $join                           = [
                    "ledgers l" => "rv.party_id = l.ledger_id" ];
            $where                          = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'ledger',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 1
            );
            $data['receipt_voucher_data_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Advance voucher data */
            $string                       = "av.*,c.customer_name";
            $table                        = "advance_voucher av";
            $join                         = [
                    "customer c" => "av.party_id = c.customer_id" ];
            $where                        = array(
                    'av.delete_status'               => 0,
                    'av.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'av.payment_mode !='             => 'cash',
                    'av.currency_converted_amount >' => 0,
                    'av.added_user_id'               => $user_id,
                    'av.voucher_status'              => 1
            );
            $data['advance_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Refund voucher data */
            $string                      = "rv.*,c.customer_name";
            $table                       = "refund_voucher rv";
            $join                        = [
                    "customer c" => "rv.party_id = c.customer_id" ];
            $where                       = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 1
            );
            $data['refund_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Payment voucher data */
            $string                       = "pv.*,s.supplier_name";
            $table                        = "payment_voucher pv";
            $join                         = [
                    "supplier s" => "pv.party_id = s.supplier_id" ];
            $where                        = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'supplier',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 1
            );
            $data['payment_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                         = "pv.*,l.ledger_title as supplier_name";
            $table                          = "payment_voucher pv";
            $join                           = [
                    "ledgers l" => "pv.party_id = l.ledger_id" ];
            $where                          = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'ledger',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 1
            );
            $data['payment_voucher_data_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Contra voucher data */
            $string                      = "cv.*,l.ledger_title";
            $table                       = "contra_voucher cv";
            $join                        = [
                    "ledgers l" => "cv.party_id = l.ledger_id" ];
            $where                       = array(
                    'cv.delete_status'               => 0,
                    'cv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'cv.payment_mode !='             => 'cash',
                    'cv.currency_converted_amount >' => 0,
                    'cv.party_type'                  => 'ledger',
                    'cv.added_user_id'               => $user_id,
                    'cv.voucher_status'              => 1
            );
            $data['contra_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }
        else if ($categorized_type == 'matched')
        {
            /* Receipt voucher data */
            $string                       = "rv.*,c.customer_name,bs.date";
            $table                        = "receipt_voucher rv";
            $join                         = array(
                    "customer c"               => "rv.party_id = c.customer_id",
                    "categorized_statement cs" => "rv.receipt_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                        = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'customer',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 2
            );
            $data['receipt_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                         = "rv.*,l.ledger_title as customer_name,bs.date";
            $table                          = "receipt_voucher rv";
            $join                           = array(
                    "ledgers l"                => "rv.party_id = l.ledger_id",
                    "categorized_statement cs" => "rv.receipt_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                          = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'ledger',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 2
            );
            $data['receipt_voucher_data_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Advance voucher data */
            $string                       = "av.*,c.customer_name,bs.date";
            $table                        = "advance_voucher av";
            $join                         = array(
                    "customer c"               => "av.party_id = c.customer_id",
                    "categorized_statement cs" => "av.advance_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id",
            );
            $where                        = array(
                    'av.delete_status'               => 0,
                    'av.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'av.payment_mode !='             => 'cash',
                    'av.currency_converted_amount >' => 0,
                    'av.added_user_id'               => $user_id,
                    'av.voucher_status'              => 2
            );
            $data['advance_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Refund voucher data */
            $string                      = "rv.*,c.customer_name,bs.date";
            $table                       = "refund_voucher rv";
            $join                        = array(
                    "customer c"               => "rv.party_id = c.customer_id",
                    "categorized_statement cs" => "rv.refund_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id",
            );
            $where                       = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 2
            );
            $data['refund_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Payment voucher data */
            $string                       = "pv.*,s.supplier_name,bs.date";
            $table                        = "payment_voucher pv";
            $join                         = array(
                    "supplier s"               => "pv.party_id = s.supplier_id",
                    "categorized_statement cs" => "pv.payment_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                        = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'supplier',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 2
            );
            $data['payment_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                         = "pv.*,l.ledger_title as supplier_name,bs.date";
            $table                          = "payment_voucher pv";
            $join                           = array(
                    "ledgers l"                => "pv.party_id = l.ledger_id",
                    "categorized_statement cs" => "pv.payment_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                          = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'ledger',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 2
            );
            $data['payment_voucher_data_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Contra voucher data */
            $string                      = "cv.*,l.ledger_title,bs.date";
            $table                       = "contra_voucher cv";
            $join                        = array(
                    "ledgers l"                => "cv.party_id = l.ledger_id",
                    "categorized_statement cs" => "cv.contra_voucher_id = cs.voucher_id",
                    "bank_statement bs"        => "cs.bank_statement_id = bs.bank_statement_id"
            );
            $where                       = array(
                    'cv.delete_status'               => 0,
                    'cv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'cv.payment_mode !='             => 'cash',
                    'cv.currency_converted_amount >' => 0,
                    'cv.party_type'                  => 'ledger',
                    'cv.added_user_id'               => $user_id,
                    'cv.voucher_status'              => 2
            );
            $data['contra_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }
        else
        {
            /* Receipt voucher data */
            $string                       = "rv.*,c.customer_name";
            $table                        = "receipt_voucher rv";
            $join                         = [
                    "customer c" => "rv.party_id = c.customer_id" ];
            $where                        = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'customer',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 1
            );
            $data['receipt_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                         = "rv.*,l.ledger_title as customer_name";
            $table                          = "receipt_voucher rv";
            $join                           = [
                    "ledgers l" => "rv.party_id = l.ledger_id" ];
            $where                          = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.party_type'                  => 'ledger',
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 1
            );
            $data['receipt_voucher_data_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Advance voucher data */
            $string                       = "av.*,c.customer_name";
            $table                        = "advance_voucher av";
            $join                         = [
                    "customer c" => "av.party_id = c.customer_id" ];
            $where                        = array(
                    'av.delete_status'               => 0,
                    'av.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'av.payment_mode !='             => 'cash',
                    'av.currency_converted_amount >' => 0,
                    'av.added_user_id'               => $user_id,
                    'av.voucher_status'              => 1
            );
            $data['advance_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Refund voucher data */
            $string                      = "rv.*,c.customer_name";
            $table                       = "refund_voucher rv";
            $join                        = [
                    "customer c" => "rv.party_id = c.customer_id" ];
            $where                       = array(
                    'rv.delete_status'               => 0,
                    'rv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'rv.payment_mode !='             => 'cash',
                    'rv.currency_converted_amount >' => 0,
                    'rv.added_user_id'               => $user_id,
                    'rv.voucher_status'              => 1
            );
            $data['refund_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Payment voucher data */
            $string                       = "pv.*,s.supplier_name";
            $table                        = "payment_voucher pv";
            $join                         = [
                    "supplier s" => "pv.party_id = s.supplier_id" ];
            $where                        = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'supplier',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 1
            );
            $data['payment_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            $string                         = "pv.*,l.ledger_title as supplier_name";
            $table                          = "payment_voucher pv";
            $join                           = [
                    "ledgers l" => "pv.party_id = l.ledger_id" ];
            $where                          = array(
                    'pv.delete_status'               => 0,
                    'pv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'pv.payment_mode !='             => 'cash',
                    'pv.currency_converted_amount >' => 0,
                    'pv.party_type'                  => 'ledger',
                    'pv.added_user_id'               => $user_id,
                    'pv.voucher_status'              => 1
            );
            $data['payment_voucher_data_1'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

            /* Contra voucher data */
            $string                      = "cv.*,l.ledger_title";
            $table                       = "contra_voucher cv";
            $join                        = [
                    "ledgers l" => "cv.party_id = l.ledger_id" ];
            $where                       = array(
                    'cv.delete_status'               => 0,
                    'cv.branch_id'                   => $this->session->userdata('SESS_BRANCH_ID'),
                    'cv.payment_mode !='             => 'cash',
                    'cv.currency_converted_amount >' => 0,
                    'cv.party_type'                  => 'ledger',
                    'cv.added_user_id'               => $user_id,
                    'cv.voucher_status'              => 1
            );
            $data['contra_voucher_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        }

        // echo "<pre>";
        // print_r($data['receipt_voucher_data']);
        // print_r($data['payment_voucher_data']);
        // exit;

        $this->load->view('bank_reconciliation/view_vouchers', $data);
    }

    public function remove_categorized()
    {
        $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
        $data['module_id']             = $bank_reconciliation_module_id;
        $modules                       = $this->modules;
        $privilege                     = "delete_privilege";
        $data['privilege']             = "delete_privilege";
        $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $voucher_id   = $this->input->post('voucher_id');
        $voucher_type = $this->input->post('voucher_type');

        $where                      = array(
                'voucher_id'    => $voucher_id,
                'voucher_type'  => $voucher_type,
                'delete_status' => 0 );
        $categorized_statement_data = $this->general_model->getRecords('*', 'categorized_statement', $where);
        $this->general_model->deleteData('categorized_statement', $where);

        if ($categorized_statement_data[0]->split_status == '1')
        {
            $where = array(
                    'sub_statement_id' => $categorized_statement_data[0]->sub_statement_id,
                    'display_status'   => 1 );
            $this->general_model->updateData('sub_statement', array(
                    'display_status' => 0 ), $where);

            $where = array(
                    'bank_statement_id' => $categorized_statement_data[0]->bank_statement_id,
                    'display_status'    => 1 );
            $this->general_model->updateData('bank_statement', array(
                    'display_status' => 0 ), $where);
        }
        else
        {
            $where = array(
                    'bank_statement_id' => $categorized_statement_data[0]->bank_statement_id,
                    'display_status'    => 1 );
            $this->general_model->updateData('bank_statement', array(
                    'display_status' => 0 ), $where);
        }

        if ($voucher_type == 'receipt_voucher')
        {
            $where = array(
                    'receipt_id'     => $voucher_id,
                    'voucher_status' => 2 );
            $this->db->update('receipt_voucher', array(
                    'voucher_status' => 1 ));
        }
        else if ($voucher_type == 'payment_voucher')
        {
            $where = array(
                    'payment_id'     => $voucher_id,
                    'voucher_status' => 2 );
            $this->db->update('payment_voucher', array(
                    'voucher_status' => 1 ));
        }
        else if ($voucher_type == 'advance_voucher')
        {
            $where = array(
                    'advance_id'     => $voucher_id,
                    'voucher_status' => 2 );
            $this->db->update('advance_voucher', array(
                    'voucher_status' => 1 ));
        }
        else if ($voucher_type == 'refund_voucher')
        {
            $where = array(
                    'refund_id'      => $voucher_id,
                    'voucher_status' => 2 );
            $this->db->update('refund_voucher', array(
                    'voucher_status' => 1 ));
        }
        else if ($voucher_type == 'contra_voucher')
        {
            $where = array(
                    'contra_voucher_id' => $voucher_id,
                    'voucher_status'    => 2 );
            $this->db->update('contra_voucher', array(
                    'voucher_status' => 1 ));
        }

        redirect('bank_reconciliation/view_vouchers');
    }

    // public function get_ledger()
    // {
    //     $bank_reconciliation_module_id = $this->config->item('bank_reconciliation_module');
    //     $data['module_id']             = $bank_reconciliation_module_id;
    //     $modules                       = $this->modules;
    //     $privilege                     = "view_privilege";
    //     $data['privilege']             = "view_privilege";
    //     $section_modules               = $this->get_section_modules($bank_reconciliation_module_id, $modules, $privilege);
    //     $data['access_modules']          = $section_modules['modules'];
    //     $data['access_sub_modules']      = $section_modules['sub_modules'];
    //     $data['access_module_privilege'] = $section_modules['module_privilege'];
    //     $data['access_user_privilege']   = $section_modules['user_privilege'];
    //     $data['access_settings']         = $section_modules['settings'];
    //     $data['access_common_settings']  = $section_modules['common_settings'];
    //     $voucher_id = $this->input->post('voucher_id');
    //     $voucher_type = $this->input->post('voucher_type');
    //     $option = "";
    //     if($voucher_type=='receipt_voucher')
    //     {
    //         $receipt_voucher_data = $this->general_model->getRecords('*','receipt_voucher',array('receipt_id'=>$voucher_id,'delete_status'=>0));
    //         if($receipt_voucher_data[0]->party_type=='customer')
    //         {
    //             $customer_data = $this->customer_call();
    //             foreach ($customer_data as $key => $value)
    //             {
    //                 if($receipt_voucher_data[0]->party_id == $value->customer_id)
    //                 {
    //                     $option .= '<option value="'.$value->customer_id.'" selected>'.$value->customer_name.'</option>';
    //                 }
    //                 else
    //                 {
    //                     $option .= '<option value="'.$value->customer_id.'">'.$value->customer_name.'</option>';
    //                 }
    //             }
    //         }
    //     }
    //     if($voucher_type=='payment_voucher')
    //     {
    //     }
    //     if($voucher_type=='advance_voucher')
    //     {
    //     }
    //     if($voucher_type=='refund_voucher')
    //     {
    //     }
    // }
}
