<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class purchase_voucher extends MY_Controller
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
        $purchase_voucher_module_id         = $this->config->item('purchase_voucher_module');
        $data['module_id']               = $purchase_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($purchase_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $email_sub_module_id             = $this->config->item('email_sub_module');
        $data['voucher_type'] ='purchase';
        if (!empty($this->input->post()))
        {
            $voucher_type='purchase';
            if($this->input->post('voucher_type')){
                $voucher_type = $this->input->post('voucher_type');
            }
            $columns = array(
                    0 => 'voucher_date',
                    1 => 'voucher_number',
                    2 => 'invoice_number',
                    3 => 'grand_total',
                    4 => 'from_account',
                    5 => 'to_account' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order') [0]['column']];
            $dir                 = $this->input->post('order') [0]['dir'];
            $list_data           = $this->common->purchase_voucher_list_field($voucher_type);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            $currency = $this->getBranchCurrencyCode();
            $currency_code     = $currency[0]->currency_code;
            $currency_symbol   = $currency[0]->currency_symbol;
            if (empty($this->input->post('search') ['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search') ['value'];
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
                    $purchase_voucher_id             = $this->encryption_url->encode($post->purchase_voucher_id);
                    $purchase_id = $this->encryption_url->encode($post->reference_id);
                    $nestedData['check']  = '<input type="checkbox" name="check_purchase" class="form-check-input" value="'.$purchase_id.'"><input type="hidden" name="edit" value="'.base_url().$post->reference_type.'/edit/'.$purchase_id.'"><input type="hidden" name="view" value="'.base_url('purchase_voucher/view_details/') . $purchase_voucher_id . '"><input type="hidden" name="pdf" value="'.base_url().$post->reference_type.'/pdf/'.$purchase_id.'"><input type="hidden" name="delete" value="'.$purchase_id.'">';
                    $nestedData['voucher_date']   = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = '<a href="' . base_url('purchase_voucher/view_details/') . $purchase_voucher_id . '">' . $post->voucher_number . '</a>';

                    $nestedData['invoice_number'] = str_replace(",", ",<br/>", $post->reference_number);
                    $nestedData['grand_total']    = $currency_symbol . ' ' . $this->precise_amount(str_replace(",", ",<br/>", $post->receipt_amount),2);
                    $nestedData['from_account']   = $post->supplier_name;
                    $nestedData['to_account']     = $post->to_account;

                    $send_data[] = $nestedData;
                }
            } 
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data 
                );
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('purchase_voucher/list', $data);
        }
    }

    function view_details($id)
    {
        $purchase_voucher_id                = $this->encryption_url->decode($id);
        $purchase_voucher_module_id         = $this->config->item('purchase_voucher_module');
        $data['module_id']               = $purchase_voucher_module_id;
        $data['purchase_voucher_module_id'] = $purchase_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        // exit;

                $section_modules            = $this->get_section_modules($purchase_voucher_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $voucher_details = $this->common->purchase_voucher_details($purchase_voucher_id);
        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
       
        $this->load->view('purchase_voucher/view_details', $data);
    }

    function purchase_credit_note_voucher()
    {
        $purchase_voucher_module_id         = $this->config->item('purchase_voucher_module');
        $data['module_id']               = $purchase_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($purchase_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $email_sub_module_id             = $this->config->item('email_sub_module');
        $data['voucher_type'] ='purchase_credit_note';
        $data['currency'] = $this->currency_call();
        $this->load->view('purchase_voucher/list', $data);
        
    }

    function purchase_debit_note_voucher()
    {
        $purchase_voucher_module_id         = $this->config->item('purchase_voucher_module');
        $data['module_id']               = $purchase_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($purchase_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $email_sub_module_id             = $this->config->item('email_sub_module');
        $data['voucher_type'] ='purchase_debit_note';
        $data['currency'] = $this->currency_call();
        $this->load->view('purchase_voucher/list', $data);
        
    }
}

