<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class boe_voucher extends MY_Controller{

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
        $boe_voucher_module_id         = $this->config->item('BOE_module');
        $data['module_id']               = $boe_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($boe_voucher_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $email_sub_module_id             = $this->config->item('email_sub_module');
        $data['voucher_type'] ='boe';
        if (!empty($this->input->post()))
        {
            $voucher_type='boe';
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
            $list_data           = $this->common->boe_voucher_list_field($voucher_type);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
              if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
            $currency = $this->getBranchCurrencyCode();
            $currency_code     = $currency[0]->currency_code;
            $currency_symbol   = $currency[0]->currency_symbol;
            if (empty($this->input->post('search') ['value']))
            {
                // $list_data['limit']  = $limit;
                // $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search') ['value'];
                // $list_data['limit']  = $limit;
                // $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } 
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $boe_voucher_id = $this->encryption_url->encode($post->payment_id);
                    $boe_id = $this->encryption_url->encode($post->reference_id);
                    $nestedData['check']  = '<input type="checkbox" name="check_boe" class="form-check-input" value="'.$boe_id.'"><input type="hidden" name="edit" value="'.base_url().'boe/edit/'.$boe_id.'"><input type="hidden" name="view" value="'.base_url('boe_voucher/view_details/') . $boe_voucher_id . '"><input type="hidden" name="pdf" value="'.base_url().'boe/pdf/'.$boe_id.'"><input type="hidden" name="delete" value="'.$boe_id.'">';

                    $nestedData['voucher_date']   = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = '<a href="' . base_url('boe_voucher/view_details/') . $boe_voucher_id . '">' . $post->voucher_number . '</a>';

                    $nestedData['invoice_number'] = str_replace(",", ",<br/>", $post->reference_number);
                    $nestedData['grand_total']    = $currency_symbol . ' ' . $this->precise_amount(str_replace(",", ",<br/>", $post->receipt_amount),2);
                    $nestedData['from_account']   = $post->from_account;
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
            $this->load->view('boe_voucher/list', $data);
        }
    }

    function view_details($id){
        $boe_voucher_id                = $this->encryption_url->decode($id);
        $boe_voucher_module_id         = $this->config->item('BOE_module');
        $data['module_id']               = $boe_voucher_module_id;
        $data['boe_voucher_module_id'] = $boe_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        // exit;

                $section_modules            = $this->get_section_modules($boe_voucher_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $voucher_details = $this->common->boe_voucher_details($boe_voucher_id);
        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
       
        $this->load->view('boe_voucher/view_details', $data);
    }
}