<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class General_bill extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'product_model',
                'service_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    function index()
    {
        $general_bill_module_id          = $this->config->item('general_bill_module');
        $data['module_id']               = $general_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_bill_module_id, $modules, $privilege);
        $data=array_merge($data,$section_modules);
        /*$data['access_modules']          = $data['modules'];
        $data['access_sub_modules']      = $data['sub_modules'];
        $data['access_module_privilege'] = $data['module_privilege'];
        $data['access_user_privilege']   = $data['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['access_common_settings'];*/
         $access_common_settings = $section_modules['access_common_settings'];
      
        $email_sub_module_id             = $this->config->item('email_sub_module');
        
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'general_bill_date',
                    1 => 'general_bill_invoice_number',
                    2 => 'purpose_of_transaction',
                    3 => 'general_bill_grand_total',
                    4 => 'currency_converted_grand_total',
                    5 => 'added_user',
                    6 => 'action',
            );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->general_bill_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $general_bill_id = $this->encryption_url->encode($post->general_bill_id);

                    $nestedData['general_bill_date']              = $post->general_bill_date;
                    $nestedData['general_bill_invoice_number']    = '(<a href="' . base_url('general_bill/view/') . $general_bill_id . '">' . $post->general_bill_invoice_number . '</a>) ';
                    $nestedData['purpose_of_transaction']         = $post->purpose_of_transaction;
                    $nestedData['general_bill_grand_total']       = $post->currency_symbol . ' ' . $post->general_bill_grand_total;
                    $nestedData['currency_converted_grand_total'] = $post->currency_converted_grand_total;

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;


                    // $cols          = '<ul class="list-inline">  <li>   <a href="' . base_url('general_bill/view/') . $general_bill_id . '"><i class="fa fa-eye text-orange"></i> General Bill Details</a>    </li>';
                    //  if ($data['access_module_privilege']->edit_privilege == "yes")
                    // {
                    //         $cols = '<ul class="list-inline"><li>  <a href="' . base_url('general_bill/edit/') . $general_bill_id . '"><i class="fa fa-pencil text-blue"></i></a>  </li>';
                    // }
                   if (in_array($general_bill_module_id , $data['active_delete']) ){
                        $cols = '<ul class="list-inline"><li>   <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $general_bill_id . '" data-path="general_bill/delete"  href="#" title="Delete General Bill" ><i class="fa fa-trash-o text-purple"></i> </a>       </li>';
                    }

                    $cols                 .= '</ul>';
                    $nestedData['action'] = $cols;

                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('general_bill/list', $data);
        }
    }

    function add()
    {
        $data                            = $this->get_default_country_state();
        $general_bill_module_id          = $this->config->item('general_bill_module');
        $data['module_id']               = $general_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_bill_module_id, $modules, $privilege);
        $data=array_merge($data,$section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $data['currency']          = $this->currency_call();
        $data['supplier']          = $this->supplier_call();
        $branch_details            = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id']   = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id']         = $branch_details['branch'][0]->branch_id;

        $access_settings        = $data['access_settings'];
        $primary_id             = "general_bill_id";
        $table_name             = $this->config->item('general_bill_table');
        $date_field_name        = "general_bill_date";
        $current_date           = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['ledger_data']    = $this->general_model->getRecords('*', 'ledgers', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $this->load->view('general_bill/add', $data);
    }

    function add_general_bill()
    {


        echo "interst ledger id " . $this->input->post('intrst_ledger_id');

        $general_bill_module_id          = $this->config->item('general_bill_module');
        $data['module_id']               = $general_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_bill_module_id, $modules, $privilege);
       $data=array_merge($data,$section_modules);
        $access_common_settings = $section_modules['access_common_settings'];
        $data['accounts_sub_module_id']  = $this->config->item('accounts_sub_module');
        $accounts_module_id              = $this->config->item('accounts_module');
        $modules_present                 = array(
                'accounts_module_id' => $accounts_module_id );
        $data['other_modules_present']   = $this->other_modules_present($modules_present, $modules['modules']);
        $currency                        = $this->input->post('currency_id');
        $access_settings = $section_modules['access_settings'];

        if ($access_settings[0]->invoice_creation == "automatic")
        {
            if ($this->input->post('invoice_number') != $this->input->post('invoice_number_old'))
            {
                $primary_id      = "general_bill_id";
                $table_name      = $this->config->item('general_bill_table');
                $date_field_name = "general_bill_date";
                $current_date    = $this->input->post('invoice_date');
                $invoice_number  = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
            }
            else
            {
                $invoice_number = $this->input->post('invoice_number');
            }
        }
        else
        {
            $invoice_number = $this->input->post('invoice_number');
        }
        //voucher number for general voucher

        $general_voucher_module_id = $this->config->item('general_voucher_module');
        $modules                   = $this->get_modules();
        $privilege                 = "add_privilege";
        $section_modules           = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
        $access_sub_modules        = $section_modules['access_sub_modules'];
        $access_settings           = $section_modules['access_settings'];
        $primary_id                = "general_voucher_id";
        $table_name                = $this->config->item('general_voucher_table');
        $date_field_name           = "voucher_date";
        $current_date              = $this->input->post('invoice_date');
        $voucher_number            = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        //voucher number for general voucher ends

        $purpose_of_transaction = $this->input->post('purpose_of_transaction');
        $type_of_transaction    = $this->input->post('type_of_transaction');
        $multiple_field         = $this->input->post('multiple_field');
        if ($this->input->post('mode_of_payment_text') == "CASH" || $this->input->post('mode_of_payment_text') == "BANK" || $this->input->post('mode_of_payment_text') == "OTHER PAYMENT MODE")
        {
            $mode_of_payment = strtolower($this->input->post('mode_of_payment_text'));
        }
        else
        {
            $mode_of_payment = $this->input->post('mode_of_payment');
        }
        if ($type_of_transaction == "Fixed Asset Purchase")
        {
            $party_type = "supplier";
        }
        else if ($type_of_transaction == "Fixed Asset Sold or Disposed")
        {
            $party_type = "customer";
        }
        else
        {
            $party_type = "";
        }

        $from_to_val = implode(',', $this->input->post('from_to'));

        $general_bill_data = array(
                "general_bill_date"           => $this->input->post('invoice_date'),
                "general_bill_invoice_number" => $invoice_number,
                "multiple_field"              => $this->input->post('multiple_field'),
                "currency_id"                 => $this->input->post('currency_id'),
                "purpose_of_transaction"      => $this->input->post('purpose_of_transaction'),
                "type_of_transaction"         => $this->input->post('type_of_transaction'),
                "mode_of_payment"             => $mode_of_payment,
                "bank_name"                   => $this->input->post('bank_name'),
                "cheque_date"                 => $this->input->post('cheque_date'),
                "cheque_number"               => $this->input->post('cheque_number'),
                "to_or_from_field"            => $from_to_val,
                "to_or_from_type"             => $this->input->post('from_to_type'),
                "branch_id"                   => $this->session->userdata('SESS_BRANCH_ID'),
                "to_account"                  => $this->input->post('to_acc'),
                "from_account"                => $this->input->post('from_acc'),
                "added_date"                  => date('Y-m-d'),
                "added_user_id"               => $this->session->userdata('SESS_USER_ID'),
                "financial_year_id"           => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') );


        if ($this->input->post('purpose_of_transaction') == "Fixed Assets")
        {
            $general_bill_data['party_id']           = $this->input->post('party');
            $general_bill_data['party_type']         = $party_type;
            $general_bill_data['nature_of_supply']   = $this->input->post('nature_of_supply');
            $general_bill_data['billing_country_id'] = $this->input->post('billing_country');
            $general_bill_data['billing_state_id']   = $this->input->post('billing_state');
            $general_bill_data['type_of_supply']     = $this->input->post('type_of_supply');

            $general_bill_data['general_bill_taxable_amount'] = $this->input->post('total_taxable_amount');
            $general_bill_data['general_bill_igst_amount']    = $this->input->post('total_igst_amount');
            $general_bill_data['general_bill_cgst_amount']    = $this->input->post('total_cgst_amount');
            $general_bill_data['general_bill_sgst_amount']    = $this->input->post('total_sgst_amount');
            $general_bill_data['general_bill_tax_amount']     = $this->input->post('total_tax_amount');
            $general_bill_data['general_bill_grand_total']    = $this->input->post('total_grand_total');
            $sub_total                                        = $this->input->post('total_taxable_amount');
            $grand_total                                      = $this->input->post('total_grand_total');
        }
        else
        {

            $general_bill_data['general_bill_taxable_amount'] = $this->input->post('total_grand_total');
            $general_bill_data['general_bill_grand_total']    = $this->input->post('total_grand_total');
            $sub_total                                        = $this->input->post('total_grand_total');
            $grand_total                                      = $this->input->post('total_grand_total');
        }

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $this->input->post('currency_id'))
        {
            $general_bill_data['currency_converted_sub_total']   = $sub_total;
            $general_bill_data['currency_converted_grand_total'] = $grand_total;
            $currency_converted_sub_total                        = $sub_total;
            $currency_converted_grand_total                      = $grand_total;
        }
        else
        {
            $general_bill_data['currency_converted_sub_total']   = "0.00";
            $general_bill_data['currency_converted_grand_total'] = "0.00";
            $currency_converted_sub_total                        = "0.00";
            $currency_converted_grand_total                      = "0.00";
        }



        $general_bill_table = $this->config->item('general_bill_table');
        $data_main          = array_map('trim', $general_bill_data);

        if ($general_bill_id = $this->general_model->insertData($general_bill_table, $data_main))
        {

            $ledger_item_data = $this->input->post('table_data');
            $js_data          = json_decode($ledger_item_data);



            foreach ($js_data as $key => $value)
            {
                if ($value->from_id != null && $value->to_id != null)
                {


                    $item_data  = array(
                            'general_bill_id' => $general_bill_id,
                            'from_id'         => $value->from_id,
                            'to_id'           => $value->to_id,
                            'type'            => "ledger",
                            'item_unit_price' => $value->item_grand_total,
                            'item_sub_total'  => $value->item_grand_total );
                    $tax_percnt = 0;

                    $to_id   = $value->to_id;
                    $from_id = $value->from_id;

                    if ($type_of_transaction == "Fixed Asset Purchase")
                    {
                        $tax_percnt                        = $value->item_igst + $value->item_cgst + $value->item_sgst;
                        $item_data['item_taxable_value']   = $value->item_taxable_value;
                        $item_data['item_igst_percentage'] = $value->item_igst;
                        $item_data['item_cgst_percentage'] = $value->item_cgst;
                        $item_data['item_sgst_percentage'] = $value->item_sgst;
                        $item_data['item_igst_amount']     = $value->item_igst_amount;
                        $item_data['item_cgst_amount']     = $value->item_cgst_amount;
                        $item_data['item_sgst_amount']     = $value->item_sgst_amount;
                        $item_data['item_tax_percentage']  = $tax_percnt;
                        $item_data['item_tax_amount']      = $value->item_tax_amount;
                        $item_data['item_grand_total']     = $value->item_grand_total;
                    }
                    else
                    {
                        $item_data['item_taxable_value'] = $value->item_grand_total;
                        $item_data['item_grand_total']   = $value->item_grand_total;
                    }

                    echo "<pre>";
                    print_r($item_data);
                    $id = $this->general_model->insertData("general_bill_item", $item_data);
                }
            }
            if ($type_of_transaction == "Loan Repaid To Lender" || $type_of_transaction == "Instalment or EMI Paid To Lender")
            {


                $item_data['general_bill_id']    = $general_bill_id;
                $item_data['from_id']            = $this->input->post('intrst_ledger_id');
                $item_data['to_id']              = $to_id;
                $item_data['type']               = "ledger";
                $item_data['item_unit_price']    = $this->input->post('amount_intrst');
                $item_data['item_sub_total']     = $this->input->post('amount_intrst');
                $item_data['item_taxable_value'] = $this->input->post('amount_intrst');
                $item_data['item_grand_total']   = $this->input->post('amount_intrst');
                $id                              = $this->general_model->insertData("general_bill_item", $item_data);
                echo "interest ";
                echo "<pre>";
                print_r($item_data);
            }
            else if ($type_of_transaction == "Loan Repaid By Borrower" || $type_of_transaction == "Instalment or EMI Repaid By Borrower")
            {
                $item_data['general_bill_id']    = $general_bill_id;
                $item_data['from_id']            = $from_id;
                $item_data['to_id']              = $this->input->post('intrst_ledger_id');
                $item_data['type']               = "ledger";
                $item_data['item_unit_price']    = $this->input->post('amount_intrst');
                $item_data['item_sub_total']     = $this->input->post('amount_intrst');
                $item_data['item_taxable_value'] = $this->input->post('amount_intrst');
                $item_data['item_grand_total']   = $this->input->post('amount_intrst');

                $id = $this->general_model->insertData("general_bill_item", $item_data);
                echo "<pre>";
                print_r($item_data);
            }
            

            if (isset($data['other_modules_present']['accounts_module_id']))
            {

                foreach ($data['access_sub_modules'] as $key => $value)
                {

                    if (isset($data['accounts_sub_module_id']))
                    {

                        if ($data['accounts_sub_module_id'] == $value->sub_module_id)
                        {

                            //general_voucher and accounts_general_voucher entry

                            $general_voucher_data = array(
                                    "branch_id"                 => $this->session->userdata('SESS_BRANCH_ID'),
                                    "voucher_number"            => $voucher_number,
                                    "voucher_type"              => "general_bill",
                                    "voucher_date"              => $this->input->post('invoice_date'),
                                    "description"               => "",
                                    "reference_id"              => $general_bill_id,
                                    "from_account"              => $this->input->post('from_acc'),
                                    "to_account"                => $this->input->post('to_acc'),
                                    "reference_type"            => "general_bill",
                                    "reference_number"          => $invoice_number,
                                    "added_user_id"             => $this->session->userdata('SESS_USER_ID'),
                                    "added_date"                => date('Y-m-d'),
                                    "updated_user_id"           => "",
                                    "updated_date"              => "",
                                    "currency_id"               => $this->input->post('currency_id'),
                                    "currency_converted_amount" => $currency_converted_grand_total,
                                    "financial_year_id"         => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    "receipt_amount"            => $grand_total,
                                    "note1"                     => $this->input->post('note1'),
                                    "note2"                     => $this->input->post('note2') );

                            $general_voucher_id = $this->general_model->insertData("general_voucher", $general_voucher_data);

                            $ledger_item_data = $this->input->post('table_data');
                            $js_data          = json_decode($ledger_item_data);



                            //general_voucher and accounts_general_voucher entry ends


                            $cash_ledger_id = $this->ledger_model->getDefaultLedger('CASH');

                            if ($this->input->post('mode_of_payment') == $cash_ledger_id)
                            {
                                $voucher_status = 0;
                            }
                            else
                            {
                                $voucher_status = 1;
                            }

                            $voucher_data = array(
                                    "branch_id"                 => $this->session->userdata('SESS_BRANCH_ID'),
                                    "voucher_date"              => $this->input->post('invoice_date'),
                                    "reference_id"              => $general_bill_id,
                                    "reference_type"            => "general_bill",
                                    "reference_number"          => $invoice_number,
                                    "party_id"                  => $from_to_val,
                                    "party_type"                => "ledger",
                                    "voucher_status"            => $voucher_status,
                                    "added_user_id"             => $this->session->userdata('SESS_USER_ID'),
                                    "added_date"                => date('Y-m-d'),
                                    "currency_id"               => $this->input->post('currency_id'),
                                    "currency_converted_amount" => $currency_converted_grand_total,
                                    "financial_year_id"         => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    "receipt_amount"            => $this->input->post('amount') );


                            $from_acc    = $this->input->post('from_acc');
                            $to_acc      = $this->input->post('to_acc');
                            $ledger_from = $this->input->post('from_acc_id');
                            $ledger_to   = $this->input->post('to_acc_id');

                            if ($type_of_transaction == "cash deposit in bank" || $type_of_transaction == "cash withdrawal from bank" || $purpose_of_transaction == "Bank to Bank")
                            {
                                $i = 0;
                                foreach ($js_data as $key => $value)
                                {
                                    $general_data[$i] = array(
                                            "ledger_from"              => $value->from_id,
                                            "ledger_to"                => $value->to_id,
                                            "voucher_amount"           => $value->item_grand_total,
                                            "converted_voucher_amount" => $value->item_grand_total,
                                            "dr_amount"                => $value->item_grand_total,
                                            "cr_amount"                => "" );

                                    $i++;
                                }
                                $accounts_voucher_table = "accounts_general_voucher";
                                $accounts_field         = "general_voucher_id";
                                $this->accounts_general_voucher_entry($general_voucher_id, $general_data, $tax_data               = "", "add", $currency, $multiple_field, $type_of_transaction, $accounts_voucher_table, $accounts_field);

                                $contra_data            = $this->contra_voucher_section($data_main['general_bill_date'], $from_acc, $to_acc, "add_privilege", $mode_of_payment);
                                $voucher_data           = array_merge($contra_data, $voucher_data);
                                $voucher_table          = $this->config->item('contra_voucher_table');
                                $accounts_voucher_table = "accounts_contra_voucher";
                                $accounts_field         = "contra_voucher_id";
                            }
                            else if ($type_of_transaction == "cash receipt" || $type_of_transaction == "Deposit Withdraw" || $type_of_transaction == "Fixed Asset Sold or Disposed" || $type_of_transaction == "Tax Receivable" || $purpose_of_transaction == "Indirect Income" || $type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature" || $type_of_transaction == "Advance Taken" || $type_of_transaction == "Receipt of Advance Given" || $type_of_transaction == "Loan Borrowed" || $type_of_transaction == "Capital Invested" || $type_of_transaction == "Additional Capital Invested" || $type_of_transaction == "Loan Repaid By Borrower" || $type_of_transaction == "Instalment or EMI Repaid By Borrower")
                            {

                                $i = 0;
                                foreach ($js_data as $key => $value)
                                {
                                    $from_ledger_id = $value->from_id;

                                    $general_data[$i] = array(
                                            "ledger_from"              => $value->from_id,
                                            "ledger_to"                => $value->to_id,
                                            "voucher_amount"           => $value->item_grand_total,
                                            "converted_voucher_amount" => $value->item_grand_total,
                                            "dr_amount"                => $value->item_grand_total,
                                            "cr_amount"                => "" );

                                    $i++;
                                }

                                if ($type_of_transaction == "Loan Repaid By Borrower" || $type_of_transaction == "Instalment or EMI Repaid By Borrower")
                                {
                                    $general_data[$i]['ledger_from']              = $from_ledger_id;
                                    $general_data[$i]['ledger_to']                = $this->input->post('intrst_ledger_id');
                                    $general_data[$i]['voucher_amount']           = $this->input->post('amount_intrst');
                                    $general_data[$i]['converted_voucher_amount'] = $this->input->post('amount_intrst');
                                    $general_data[$i]['dr_amount']                = $this->input->post('amount_intrst');
                                    $general_data[$i]['cr_amount']                = "";
                                }

                                $accounts_voucher_table = "accounts_general_voucher";
                                $accounts_field         = "general_voucher_id";
                                $this->accounts_general_voucher_entry($general_voucher_id, $general_data, $tax_data               = "", "add", $currency, $multiple_field, $type_of_transaction, $accounts_voucher_table, $accounts_field);

                                $receipt_data = $this->receipt_voucher_section($data_main['general_bill_date'], $from_acc, $to_acc, "add_privilege", $mode_of_payment);
                                $voucher_data = array_merge($receipt_data, $voucher_data);

                                $voucher_table          = $this->config->item('receipt_voucher_table');
                                $accounts_voucher_table = "accounts_receipt_voucher";
                                $accounts_field         = "receipt_voucher_id";
                            }
                            else if ($type_of_transaction == "cash payment" || $type_of_transaction == "Deposit Made" || $type_of_transaction == "Fixed Asset Purchase" || $type_of_transaction == "Tax Payable" || $type_of_transaction == "Investment Made" || $type_of_transaction == "Advance Given" || $type_of_transaction == "Advance Tax Paid" || $type_of_transaction == "Payment of Advance Taken" || $type_of_transaction == "Loan Repaid To Lender" || $type_of_transaction == "Instalment or EMI Paid To Lender" || $type_of_transaction == "Capital Withdrawn" || $type_of_transaction == "Loan Given")
                            {

                                //accounts general voucher
                                if ($type_of_transaction == "Fixed Asset Purchase")
                                {
                                    $i              = 0;
                                    $igst_ledger_id = $this->ledger_model->getDefaultLedger('IGST');
                                    $cgst_ledger_id = $this->ledger_model->getDefaultLedger('CGST');
                                    $sgst_ledger_id = $this->ledger_model->getDefaultLedger('SGST');
                                    foreach ($js_data as $key => $value)
                                    {
                                        $general_data[$i] = array(
                                                "general_voucher_id"       => $general_voucher_id,
                                                "ledger_from"              => $value->from_id,
                                                "ledger_to"                => $value->to_id,
                                                "voucher_amount"           => $value->item_taxable_value,
                                                "converted_voucher_amount" => $value->item_taxable_value,
                                                "dr_amount"                => $value->item_taxable_value,
                                                "cr_amount"                => "" );

                                        $tax_data = array(
                                                "igst_ledger_id"    => $igst_ledger_id,
                                                "cgst_ledger_id"    => $cgst_ledger_id,
                                                "sgst_ledger_id"    => $sgst_ledger_id,
                                                "total_igst_amount" => $this->input->post('total_igst_amount'),
                                                "total_cgst_amount" => $this->input->post('total_cgst_amount'),
                                                "total_sgst_amount" => $this->input->post('total_sgst_amount'),
                                                "grand_total"       => $this->input->post('total_grand_total') );

                                        $i++;
                                    }
                                }
                                else
                                {
                                    $i = 0;
                                    foreach ($js_data as $key => $value)
                                    {
                                        $to_ledger_id     = $value->to_id;
                                        $general_data[$i] = array(
                                                "ledger_from"              => $value->from_id,
                                                "ledger_to"                => $value->to_id,
                                                "voucher_amount"           => $value->item_grand_total,
                                                "converted_voucher_amount" => $value->item_grand_total,
                                                "dr_amount"                => $value->item_grand_total,
                                                "cr_amount"                => "" );

                                        $i++;
                                    }
                                    $tax_data = "";
                                }

                                if ($type_of_transaction == "Loan Repaid To Lender" || $type_of_transaction == "Instalment or EMI Paid To Lende[$i]r")
                                {
                                    $general_data[$i]['ledger_from']              = $this->input->post('intrst_ledger_id');
                                    $general_data[$i]['ledger_to']                = $to_ledger_id;
                                    $general_data[$i]['voucher_amount']           = $this->input->post('amount_intrst');
                                    $general_data[$i]['converted_voucher_amount'] = $this->input->post('amount_intrst');
                                    $general_data[$i]['dr_amount']                = $this->input->post('amount_intrst');
                                    $general_data[$i]['cr_amount']                = "";
                                }

                                $accounts_voucher_table = "accounts_general_voucher";
                                $accounts_field         = "general_voucher_id";
                                $this->accounts_general_voucher_entry($general_voucher_id, $general_data, $tax_data, "add", $currency, $multiple_field, $type_of_transaction, $accounts_voucher_table, $accounts_field);
                                $payment_data           = $this->payment_voucher_section($data_main['general_bill_date'], $from_acc, $to_acc, "add_privilege", $mode_of_payment);
                                $voucher_data           = array_merge($payment_data, $voucher_data);
                                $voucher_table          = $this->config->item('payment_voucher_table');
                                $accounts_voucher_table = "accounts_payment_voucher";
                                $accounts_field         = "payment_voucher_id";
                            }



                            if ($id = $this->general_model->insertData($voucher_table, $voucher_data))
                            {
                                // $ledger_id    = array('ledger_from'  => $ledger_from,
                                //                      'ledger_to' => $ledger_to);
                                // $ledger_entry = array('grand_total' => $data_main['general_bill_grand_total']);
                                // $this->voucher_entry($id, $ledger_id, $ledger_entry, "add", $currency,$accounts_voucher_table,$accounts_field);
                                $this->accounts_general_voucher_entry($id, $general_data, $tax_data = "", "add", $currency, $multiple_field, $type_of_transaction, $accounts_voucher_table, $accounts_field);
                            }
                        }
                    }
                }
            } //accounts module active check
            exit;
            redirect('general_bill', 'refresh');
        } //general bill table insert condition ends
        else
        {
            exit;
            redirect('general_bill', 'refresh');
        }
    }

//function ends

    function view($id)
    {

        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $general_bill_module_id          = $this->config->item('general_bill_module');
        $data['module_id']               = $general_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($general_bill_module_id, $modules, $privilege);
     /*    $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
       foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }
        }*/

 $data=array_merge($data,$section_modules);
        $data['currency']          = $this->currency_call();
        $data['supplier']          = $this->supplier_call();
        $branch_details            = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id']   = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id']         = $branch_details['branch'][0]->branch_id;

        $data['payment_mode'] = 'Mode of Payment';
        $string               = "g.*,gi.*";
        $table                = "general_bill g";
        $where                = array(
                'g.general_bill_id' => $id,
                'g.branch_id'       => $this->session->userdata('SESS_BRANCH_ID'),
                'g.delete_status'   => 0,
                'gi.delete_status'  => 0 );
        $join                 = [
                'general_bill_item gi' => "gi.general_bill_id = g.general_bill_id" ];


        $data['general_bill_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $data['ledger_data'] = $this->general_model->getRecords('*', 'ledgers', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        if (isset($data['general_bill_data'][0]->mode_of_payment))
        {
            if ($data['general_bill_data'][0]->mode_of_payment != 'cash' && $data['general_bill_data'][0]->mode_of_payment != 'bank' && $data['general_bill_data'][0]->mode_of_payment != 'other payment mode')
            {

                $data['mode_of_payment'] = $this->general_model->getRecords('ledger_title', 'ledgers', array(
                        'ledger_id' => $data['general_bill_data'][0]->mode_of_payment ));
            }
            else
            {
                $data['mode_of_payment'] = $this->general_model->getRecords('ledger_title', 'ledgers', array(
                        'ledger_title' => $data['general_bill_data'][0]->mode_of_payment ));
            }
        }
        if ($data['general_bill_data'][0]->purpose_of_transaction == "Bank to Bank")
        {
            $data['to_from_title'] = 'Amount Transfer To Bank';
            $data['payment_mode']  = 'Amount Transfer From Bank';
        }
        else if ($data['general_bill_data'][0]->purpose_of_transaction == "Indirect Income")
        {
            $data['to_from_title'] = 'Type of Income';
        }
        else
        {
            $data['to_from_title'] = $this->to_from_label($data['general_bill_data'][0]->type_of_transaction);
        }
        if ($data['general_bill_data'][0]->type_of_transaction == "Fixed Asset Purchase")
        {
            $data['party'] = 'Supplier';
        }
        else if ($data['general_bill_data'][0]->type_of_transaction == "Fixed Asset Sold or Disposed")
        {
            $data['party'] = 'Customer';
        }

        $to_from = explode(",", $data['general_bill_data'][0]->to_or_from_field);

        for ($k = 0; $k < count($to_from); $k++)
        {
            $data['from_to'][] = $this->general_model->getRecords('ledger_title', 'ledgers', array(
                    'ledger_id' => $to_from[$k] ));
        }

        $this->load->view('general_bill/view', $data);
    }

    function to_from_label($type_of_transaction)
    {

        if ($type_of_transaction == "cash deposit in bank")
        {
            $to_from_title = 'Name the deposited bank';
        }
        else if ($type_of_transaction == "cash withdrawal from bank")
        {
            $to_from_title = 'Name the withdrawal bank';
        }
        else if ($type_of_transaction == "cash receipt")
        {
            $to_from_title = 'Received From';
        }
        else if ($type_of_transaction == "cash payment")
        {
            $to_from_title = 'Paid To';
        }
        else if ($type_of_transaction == "Deposit Made")
        {
            $to_from_title = 'Deposited In';
        }
        else if ($type_of_transaction == "Deposit Withdraw")
        {
            $to_from_title = 'Deposited Withdraw';
        }
        else if ($type_of_transaction == "Fixed Asset Purchase")
        {
            $to_from_title = 'Purchase Of';
        }
        else if ($type_of_transaction == "Fixed Asset Sold or Disposed")
        {
            $to_from_title = 'Sale/Disposal';
        }
        else if ($type_of_transaction == "Tax Receivable")
        {
            $to_from_title = 'Taxes Received as';
        }
        else if ($type_of_transaction == "Tax Payable")
        {
            $to_from_title = 'Payment of';
        }
        else if ($type_of_transaction == "Investment Made")
        {
            $to_from_title = 'Investment in';
        }
        else if ($type_of_transaction == "Investment Withdraw / Sold / Redeem / Mature")
        {
            $to_from_title = 'Investment withdrawn/sold/redeem/mature';
        }
        else if ($type_of_transaction == "Loan Borrowed")
        {
            $to_from_title = 'From whom';
        }
        else if ($type_of_transaction == "Loan Repaid To Lender" || $type_of_transaction == "Instalment or EMI Paid To Lender" || $type_of_transaction == "Loan Given")
        {
            $to_from_title = 'For whom';
        }
        else if ($type_of_transaction == "Advance Taken")
        {
            $to_from_title = 'Advance Taken From';
        }
        else if ($type_of_transaction == "Advance Given")
        {
            $to_from_title = 'Advance Given To';
        }
        else if ($type_of_transaction == "Advance Tax Paid")
        {
            $to_from_title = 'Advance Tax Paid To';
        }
        else if ($type_of_transaction == "Payment of Advance Taken")
        {
            $to_from_title = 'Advance Refund To';
        }
        else if ($type_of_transaction == "Receipt of Advance Given")
        {
            $to_from_title = 'Advance Repaid By';
        }
        else if ($type_of_transaction == "Capital Invested" || $type_of_transaction == "Additional Capital Invested")
        {
            $to_from_title = 'From Whom';
        }
        else if ($type_of_transaction == "Capital Withdrawn")
        {
            $to_from_title = 'To Whom';
        }

        return $to_from_title;
    }

    function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $data                            = $this->get_default_country_state();
        $general_bill_module_id          = $this->config->item('general_bill_module');
        $data['module_id']               = $general_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_bill_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }
        }

        $data['data']    = $this->general_model->getRecords('*', 'general_bill', array(
                'general_bill_id' => $id ));
        $data['purpose'] = array(
                'Cash'            => 'Cash',
                'Deposit'         => 'Deposit',
                'Fixed Assets'    => 'Fixed Assets',
                'Duties & Taxes'  => 'Duties & Taxes',
                'Indirect Income' => 'Indirect Income',
                'Investment'      => 'Investment',
                'Loan'            => 'Loan',
                'Advance'         => 'Advance',
                'Bank to Bank'    => 'Bank to Bank',
                'Capital'         => 'Capital' );

        //echo $data['data'][0]->purpose_of_transaction;



        $this->load->view('general_bill/edit', $data);
    }

    public function voucher_entry($id, $ledger_id, $ledger_entry, $operation, $currency, $accounts_voucher_table, $accounts_field)
    {


        $data1 = array(
                $accounts_field  => $id,
                'ledger_from'    => $ledger_id['ledger_from'],
                'ledger_to'      => $ledger_id['ledger_to'],
                'voucher_amount' => $ledger_entry['grand_total'],
                'dr_amount'      => $ledger_entry['grand_total'],
                'cr_amount'      => "0.00" );
        $data2 = array(
                $accounts_field  => $id,
                'ledger_from'    => $ledger_id['ledger_to'],
                'ledger_to'      => $ledger_id['ledger_from'],
                'voucher_amount' => $ledger_entry['grand_total'],
                'dr_amount'      => "0.00",
                'cr_amount'      => $ledger_entry['grand_total'] );

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $data1['converted_voucher_amount'] = $ledger_entry['grand_total'];
            $data2['converted_voucher_amount'] = $ledger_entry['grand_total'];
        }
        else
        {
            $data1['converted_voucher_amount'] = "0.00";
            $data2['converted_voucher_amount'] = "0.00";
        }


        if ($operation == "add")
        {
            $this->general_model->insertData($accounts_voucher_table, $data1);
            $this->general_model->insertData($accounts_voucher_table, $data2);
        }
    }

    public function accounts_general_voucher_entry($id, $general_data, $tax_data = "", $operation, $currency, $multiple_field, $type_of_transaction, $accounts_voucher_table, $accounts_field)
    {


        echo $accounts_voucher_table . " entries <br>";

        for ($n = 0; $n < count($general_data); $n++)
        {
            $data1 = array(
                    $accounts_field  => $id,
                    'ledger_from'    => $general_data[$n]['ledger_from'],
                    'ledger_to'      => $general_data[$n]['ledger_to'],
                    'voucher_amount' => $general_data[$n]['voucher_amount'],
                    'dr_amount'      => $general_data[$n]['voucher_amount'],
                    'cr_amount'      => "0.00" );

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $data1['converted_voucher_amount'] = $general_data[$n]['voucher_amount'];
            }
            else
            {
                $data1['converted_voucher_amount'] = "0.00";
            }
            echo "<pre>";
            print_r($data1);
            $this->general_model->insertData($accounts_voucher_table, $data1);
        }
        $data2 = array(
                $accounts_field            => $id,
                'ledger_from'              => $general_data[0]['ledger_to'],
                'ledger_to'                => $general_data[0]['ledger_from'],
                'voucher_amount'           => $this->input->post('total_grand_total'),
                'converted_voucher_amount' => "",
                'dr_amount'                => "0.00",
                'cr_amount'                => $this->input->post('total_grand_total') );

        if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
        {
            $data2['converted_voucher_amount'] = $this->input->post('total_grand_total');
        }
        else
        {
            $data2['converted_voucher_amount'] = "0.00";
        }

        print_r($data2);

        $this->general_model->insertData($accounts_voucher_table, $data2);

        $k = 1;
        if ($type_of_transaction == "Fixed Asset Purchase" || $type_of_transaction == "Fixed Asset Sold or Disposed")
        {
            $tax_data1 = array(
                    $accounts_field  => $id,
                    'ledger_from'    => $tax_data['igst_ledger_id'],
                    'ledger_to'      => $general_data[0]['ledger_to'],
                    'voucher_amount' => $tax_data['total_igst_amount'],
                    'dr_amount'      => $tax_data['total_igst_amount'],
                    'cr_amount'      => "0.00" );
            $tax_data2 = array(
                    $accounts_field  => $id,
                    'ledger_from'    => $tax_data['cgst_ledger_id'],
                    'ledger_to'      => $general_data[0]['ledger_to'],
                    'voucher_amount' => $tax_data['total_cgst_amount'],
                    'dr_amount'      => $tax_data['total_cgst_amount'],
                    'cr_amount'      => "0.00" );
            $tax_data3 = array(
                    $accounts_field  => $id,
                    'ledger_from'    => $tax_data['sgst_ledger_id'],
                    'ledger_to'      => $general_data[0]['ledger_to'],
                    'voucher_amount' => $tax_data['total_sgst_amount'],
                    'dr_amount'      => $tax_data['total_sgst_amount'],
                    'cr_amount'      => "0.00" );

            echo "tax data";
            echo "<br>";
            print_r($tax_data1);
            print_r($tax_data2);
            print_r($tax_data3);

            if ($this->session->userdata('SESS_DEFAULT_CURRENCY') == $currency)
            {
                $tax_data1['converted_voucher_amount'] = $tax_data['total_igst_amount'];
                $tax_data2['converted_voucher_amount'] = $tax_data['total_cgst_amount'];
                $tax_data3['converted_voucher_amount'] = $tax_data['total_sgst_amount'];
            }
            else
            {
                $tax_data1['converted_voucher_amount'] = "0.00";
                $tax_data2['converted_voucher_amount'] = "0.00";
                $tax_data3['converted_voucher_amount'] = "0.00";
            }


            $this->general_model->insertData($accounts_voucher_table, $tax_data1);
            $this->general_model->insertData($accounts_voucher_table, $tax_data2);
            $this->general_model->insertData($accounts_voucher_table, $tax_data3);
        }
    }

    function receipt_voucher_section($date_field, $from_acc, $to_acc, $operation, $mode_of_payment)
    {
        $primary_id      = "receipt_id";
        $table_name      = $this->config->item('receipt_voucher_table');
        $date_field_name = "voucher_date";
        $current_date    = $date_field;

        $receipt_voucher_module_id = $this->config->item('receipt_voucher_module');
        $module_id                 = $receipt_voucher_module_id;
        $modules                   = $this->modules;
        $privilege                 = $operation;
        $section_modules           = $this->get_section_modules($receipt_voucher_module_id, $modules, $privilege);
        $access_settings           = $section_modules['settings'];
        $voucher_number            = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $receipt_voucher_data["voucher_number"] = $voucher_number;
        $receipt_voucher_data["voucher_type"]   = "receipt";
        $receipt_voucher_data["from_account"]   = $from_acc; //ledger-ledger name
        $receipt_voucher_data["to_account"]     = "ledger-" . $to_acc;
        $receipt_voucher_data["payment_mode"]   = $mode_of_payment;
        return $receipt_voucher_data;
    }

    function contra_voucher_section($date_field, $from_acc, $to_acc, $operation, $mode_of_payment)
    {
        $contra_voucher_module_id = $this->config->item('contra_voucher_module');
        $modules                  = $this->get_modules();
        $privilege                = $operation;
        $section_modules          = $this->get_section_modules($contra_voucher_module_id, $modules, $privilege);
        $access_settings          = $section_modules['settings'];
        $primary_id               = "contra_voucher_id";
        $table_name               = $this->config->item('contra_voucher_table');
        $date_field_name          = "voucher_date";
        $current_date             = $date_field;
        $voucher_number           = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $contra_voucher_data["voucher_number"] = $voucher_number;
        $contra_voucher_data["voucher_type"]   = "contra";
        $contra_voucher_data["from_account"]   = $from_acc; //ledger-ledger name
        $contra_voucher_data["to_account"]     = "ledger-" . $to_acc;
        $contra_voucher_data["payment_mode"]   = $mode_of_payment;
        return $contra_voucher_data;
    }

    function payment_voucher_section($date_field, $from_acc, $to_acc, $operation, $mode_of_payment)
    {
        $payment_voucher_module_id = $this->config->item('payment_voucher_module');
        $modules                   = $this->get_modules();
        $privilege                 = $operation;
        $section_modules           = $this->get_section_modules($payment_voucher_module_id, $modules, $privilege);
        $access_settings           = $section_modules['settings'];
        $primary_id                = "payment_id";
        $table_name                = $this->config->item('payment_voucher_table');
        $date_field_name           = "voucher_date";
        $current_date              = $date_field;
        $voucher_number            = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $payment_voucher_data["voucher_number"] = $voucher_number;
        $payment_voucher_data["voucher_type"]   = "payment";
        $payment_voucher_data["from_account"]   = "ledger-" . $from_acc; //ledger-ledger name
        $payment_voucher_data["to_account"]     = $to_acc;
        $payment_voucher_data["payment_mode"]   = $mode_of_payment;
        return $payment_voucher_data;
    }

    public function delete()
    {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $general_bill_module_id          = $this->config->item('general_bill_module');
        $data['module_id']               = $general_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_bill_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $data['accounts_sub_module_id']  = $this->config->item('accounts_sub_module');
        $accounts_module_id              = $this->config->item('accounts_module');
        $modules_present                 = array(
                'accounts_module_id' => $accounts_module_id );

        if ($this->general_model->updateData('general_bill', array(
                        'delete_status' => 1 ), array(
                        'general_bill_id' => $id )))
        {

            $this->general_model->updateData('general_bill_item', array(
                    'delete_status' => 1 ), array(
                    'general_bill_id' => $id ));

            //update general voucher table and account general voucher
            $general_voucher_id = $this->general_model->getRecords('*', 'general_voucher', array(
                    'reference_id'   => $id,
                    'reference_type' => 'general_bill',
                    'delete_status'  => 0 ));

            if ($this->general_model->updateData('general_voucher', array(
                            'delete_status' => 1 ), array(
                            'reference_id' => $id )))
            {
                $this->general_model->updateData('accounts_general_voucher', array(
                        'delete_status' => 1 ), array(
                        'general_voucher_id' => $general_voucher_id[0]->general_voucher_id ));
            }
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'general_bill',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'General Bill Deleted' );
            $this->general_model->insertData('log', $log_data);

            $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);

            if (isset($data['other_modules_present']['accounts_module_id']))
            {
                foreach ($data['access_sub_modules'] as $key => $value)
                {
                    if (isset($data['accounts_sub_module_id']))
                    {

                        if ($data['accounts_sub_module_id'] == $value->sub_module_id)
                        {


                            $contra_rec = $this->general_model->getRecords('*', 'contra_voucher', array(
                                    'reference_id'   => $id,
                                    'reference_type' => 'general_bill',
                                    'delete_status'  => 0 ));
                            if ($contra_rec)
                            {
                                $voucher_table = 'contra_voucher';
                                $field_name    = 'contra_voucher_id';
                                $acc_table     = 'accounts_contra_voucher';
                                $acc_field     = 'contra_voucher_id';
                            }
                            $recept_rec = $this->general_model->getRecords('*', 'receipt_voucher', array(
                                    'reference_id'   => $id,
                                    'reference_type' => 'general_bill',
                                    'delete_status'  => 0 ));

                            if ($recept_rec)
                            {
                                $voucher_table = 'receipt_voucher';
                                $field_name    = 'receipt_id';
                                $acc_table     = 'accounts_receipt_voucher';
                                $acc_field     = 'receipt_voucher_id';
                            }
                            $payment_rec = $this->general_model->getRecords('*', 'payment_voucher', array(
                                    'reference_id'   => $id,
                                    'reference_type' => 'general_bill',
                                    'delete_status'  => 0 ));
                            if ($payment_rec)
                            {
                                $voucher_table = 'payment_voucher';
                                $field_name    = 'payment_id';
                                $acc_table     = 'accounts_payment_voucher';
                                $acc_field     = 'payment_voucher_id';
                            }

                            $voucher_id = $this->general_model->getRecords($field_name, $voucher_table, array(
                                    'reference_id'   => $id,
                                    'reference_type' => 'general_bill',
                                    'delete_status'  => 0 ));


                            $this->general_model->updateData($voucher_table, array(
                                    'delete_status' => 1 ), array(
                                    'reference_id'   => $id,
                                    'reference_type' => 'general_bill' ));

                            $this->general_model->updateData($acc_table, array(
                                    'delete_status' => 1 ), array(
                                    $acc_field => $voucher_id[0]->$field_name ));
                        }
                    }
                }
            }

            redirect('general_bill');
        }
        else
        {
            $this->session->set_flashdata('fail', 'general Bill can not be Deleted.');
            redirect("general_bill", 'refresh');
        }
    }

}

