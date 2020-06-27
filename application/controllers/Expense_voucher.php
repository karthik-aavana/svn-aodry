<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Expense_voucher extends MY_Controller
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
        $expense_voucher_module_id       = $this->config->item('expense_voucher_module');
        $expense_bill_module_id       = $this->config->item('expense_bill_module');
        $data['module_id']               = $expense_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($expense_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'ev.expense_voucher_id',
                    1 => 'ev.voucher_number',
                    2 => 'ev.voucher_date',
                    3 => 's.supplier_name',
                    4 => 'ev.to_account',
                    5 => 'ev.receipt_amount');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order') [0]['column']];
            $dir                 = $this->input->post('order') [0]['dir'];
            $list_data           = $this->common->expense_voucher_list_field($order, $dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
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
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $expense_voucher_id           = $this->encryption_url->encode($post->expense_voucher_id);
                    $expense_id = $this->encryption_url->encode($post->reference_id);
                    $col = '<input type="checkbox" name="check_expense" class="form-check-input" value="'.$expense_id.'">';
                    if(in_array($expense_bill_module_id, $data['active_edit'])){
                        
                        $col .= '<input type="hidden" name="edit" value="'.base_url().'expense_bill/edit/'.$expense_id.'">';
                    }
                    if(in_array($expense_bill_module_id, $data['active_view'])){
                        $col .= '<input type="hidden" name="pdf" value="'.base_url().'expense_bill/pdf/'.$expense_id.'">';
                    }
                    if(in_array($expense_voucher_module_id, $data['active_view'])){
                        $col .= '<input type="hidden" name="view" value="'.base_url().'expense_voucher/view_details/'.$expense_id.'">';
                    }
                    if(in_array($expense_bill_module_id, $data['active_delete'])){
                         $col .= '<input type="hidden" name="delete" value="'.$expense_id.'">';
                    }
                    $nestedData['check']  = $col;//
                    $nestedData['voucher_date']   = date('d-m-Y', strtotime($post->voucher_date));
                    $nestedData['voucher_number'] = '<a href="' . base_url('expense_voucher/view_details/') . $expense_voucher_id . '">' . $post->voucher_number . '</a>';
                    $nestedData['invoice_number'] = str_replace(",", ",<br/>", $post->reference_number);
                    $nestedData['grand_total']    = $post->currency_symbol . ' ' . $this->precise_amount(str_replace(",", ",<br/>", $post->receipt_amount),2);
                    $nestedData['from_account']   = $post->supplier_name;
                    $nestedData['to_account']     = $post->to_account;
                    $send_data[] = $nestedData;
                }
            } 
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('expense_voucher/list', $data);
        }
    }
    function view_details($id)
    {
        $expense_voucher_id              = $this->encryption_url->decode($id);
        $expense_voucher_module_id       = $this->config->item('expense_voucher_module');
        $data['module_id']               = $expense_voucher_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($expense_voucher_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        
        $voucher_details = $this->common->expense_voucher_details($expense_voucher_id);
        $data['data']    = $this->general_model->getJoinRecords($voucher_details['string'], $voucher_details['table'], $voucher_details['where'], $voucher_details['join']);
        $this->load->view('expense_voucher/view_details', $data);
    }
}
