<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_voucher extends MY_Controller
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
        $cash_voucher_module_id        = $this->config->item('cash_voucher_module');
        $data['module_id'] = $cash_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($cash_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $data['voucher_type'] = 'cash';
        $data['redirect_uri'] = 'cash_voucher';
        
        if (!empty($this->input->post()))
        {
            $columns             = array(
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
            $list_data           = $this->common->cash_voucher_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
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
                    $cash_voucher_id            = $this->encryption_url->encode($post->cash_voucher_id);
                    $disabled = '';
                    if(!in_array($cash_voucher_module_id, $data['active_delete']) && !in_array($cash_voucher_module_id, $data['active_edit'])){
                        $disabled = 'disabled';
                    }
                    $nestedData['action'] = '<input type="checkbox" value="'.$cash_voucher_id.'" name="check_voucher" vtype="cash"'.$disabled.'>';
                    $nestedData['voucher_date']   = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = $post->voucher_number;
                    if (in_array($cash_voucher_module_id , $data['active_view']))
                    {
                        $nestedData['voucher_number'] = '<a href="' . base_url('cash_voucher/view_details/') . $cash_voucher_id . '">' . $post->voucher_number . '</a>';
                    }

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
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('cash_voucher/list', $data);
        }
    }

    function view_details($id)
    {
        $cash_voucher_id               = $this->encryption_url->decode($id);
        $cash_voucher_module_id        = $this->config->item('cash_voucher_module');
        $data['module_id']               = $cash_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($cash_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $voucher_details = $this->common->cash_voucher_details($cash_voucher_id);
        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
        //  echo "<pre>";
        // print_r($data['data']);
        // exit;
        $this->load->view('cash_voucher/view_details', $data);
    }

}