<?php

defined('BASEPATH') OR exit('No direct script access allowed');

Class Journal_voucher extends MY_Controller
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

    function index(){
        $general_voucher_module_id       = $this->config->item('general_voucher_module');
        $data['module_id']               = $general_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['voucher_type'] = 'journal';
        $data['redirect_uri'] = 'journal_voucher';

        if($this->session->userdata('bulk_success')){
            $data['bulk_success'] = $this->session->userdata('bulk_success');
            $this->session->unset_userdata('bulk_success');
        }elseif ($this->session->userdata('bulk_error')) {
            $data['bulk_error'] = $this->session->userdata('bulk_error');
            $this->session->unset_userdata('bulk_error');
        }

        if (!empty($this->input->post())){
            $columns             = array(
                    0 => 'gv.general_voucher_id',
                    1 => 'gv.voucher_date',
                    2 => 'gv.voucher_number',
                    3 => 'gv.reference_number',
                    4 => 'gv.receipt_amount' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order') [0]['column']];
            $dir                 = $this->input->post('order') [0]['dir'];
            $list_data           = $this->common->general_voucher_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search') ['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search') ['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } 

            $send_data = array();

            if (!empty($posts)) {
                foreach ($posts as $post)
                {
                    $general_voucher_id           = $this->encryption_url->encode($post->general_voucher_id);
                    $nestedData['action'] = '<input type="checkbox" value="'.$general_voucher_id.'" name="check_voucher" vtype="journal">';
                    $nestedData['voucher_date']   = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = '<a href="' . base_url('general_voucher/view_details/') . $general_voucher_id . '">' . $post->voucher_number . '</a>';

                    $nestedData['invoice_number'] = str_replace(",", ",<br/>", $post->reference_number);
                    $nestedData['grand_total']    = $post->currency_symbol . ' ' . $this->precise_amount(str_replace(",", ",<br/>", $post->receipt_amount),2);
                    $nestedData['from_account']   = $post->from_account;
                    $nestedData['to_account']     = $post->to_account;

                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        } else {
            $data['currency'] = $this->currency_call();
            $this->load->view('journal_voucher/list', $data);
        }
    }

    function view_details($id)
    {
        $general_voucher_id              = $this->encryption_url->decode($id);
        $general_voucher_module_id       = $this->config->item('general_voucher_module');

        $data['module_id']               = $general_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($general_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $voucher_details = $this->common->general_voucher_details($general_voucher_id);
        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);

        $this->load->view('journal_voucher/view_details', $data);
    }

}