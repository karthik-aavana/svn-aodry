<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Recurrence extends MY_Controller
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
        $recurrence_module_id            = $this->config->item('recurrence_module');
        $data['recurrence_module_id']    = $recurrence_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($recurrence_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0 => 'added_date',
                    1 => 'invoice_number',
                    2 => 'invoice_type',
                    3 => 'recurrence_date',
                    4 => 'next_generation_date',
                    5 => 'grand_total',
                    6 => 'added_user',
                    7 => 'action', );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data1           = $this->common->recurrence_sales_list_field();
            $list_data2           = $this->common->recurrence_expense_bill_list_field();
            $list_data1['search'] = 'all';
            $list_data2['search'] = 'all';

            $totalData1     = $this->general_model->getPageJoinRecordsCount($list_data1);
            $totalFiltered1 = $totalData1;
            $totalData2     = $this->general_model->getPageJoinRecordsCount($list_data2);
            $totalFiltered2 = $totalData2;

            $totalData     = bcadd($totalData1, $totalData2);
            $totalFiltered = bcadd($totalFiltered1, $totalFiltered2);

            if (empty($this->input->post('search')['value']))
            {
                $list_data1['limit']  = $limit;
                $list_data1['start']  = $start;
                $list_data1['search'] = 'all';

                $list_data2['limit']  = $limit;
                $list_data2['start']  = $start;
                $list_data2['search'] = 'all';
                $posts1               = $this->general_model->getPageJoinRecords($list_data1);
                $posts2               = $this->general_model->getPageJoinRecords($list_data2);
                $posts                = array_merge($posts1, $posts2);
            }
            else
            {
                $search               = $this->input->post('search')['value'];
                $list_data1['limit']  = $limit;
                $list_data1['start']  = $start;
                $list_data1['search'] = $search;

                $list_data2['limit']  = $limit;
                $list_data2['start']  = $start;
                $list_data2['search'] = $search;
                $posts1               = $this->general_model->getPageJoinRecords($list_data1);
                $posts2               = $this->general_model->getPageJoinRecords($list_data2);
                $posts                = array_merge($posts1, $posts2);

                $totalFiltered1 = $this->general_model->getPageJoinRecordsCount($list_data1);
                $totalFiltered2 = $this->general_model->getPageJoinRecordsCount($list_data2);
                $totalFiltered  = bcadd($totalFiltered1, $totalFiltered2);
            }
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $recurrence_id                      = $this->encryption_url->encode($post->recurrence_id);
                    $nestedData['added_date']           = $post->added_date;
                    $nestedData['invoice_number']       = $post->invoice_number;
                    $nestedData['invoice_type']         = $post->invoice_type;
                    $nestedData['recurrence_date']      = $post->recurrence_date;
                    $nestedData['next_generation_date'] = $post->next_generation_date;
                    $nestedData['grand_total']          = $post->grand_total;
                    $nestedData['added_user']           = $post->first_name . ' ' . $post->last_name;
                    $cols                               = '<ul class="action_ul_custom">';
                    if (in_array($recurrence_module_id, $data['active_delete']))
                    {
                        $cols .= '<li><a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $recurrence_id . '" data-path="recurrence/delete" data-delete_message="Are you sure! You want to stop this recurrence invoice?" href="#" title="Delete Recurrence Invoice" ><i class="fa fa-trash-o text-purple"></i> Delete Recurrence Invoice</a></li>';
                    }
                    $cols                 .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('recurrence/list', $data);
        }
    }

    function get_recurrence_ajax()
    {
        $data                            = $this->get_default_country_state();
        $recurrence_module_id            = $this->config->item('recurrence_module');
        $data['module_id']               = $recurrence_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($recurrence_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $invoice_id                      = $this->encryption_url->decode($this->input->post('invoice_id'));
        $invoice_type                    = $this->input->post('invoice_type');
        $data                            = $this->general_model->getRecords('*', 'recurrence', array(
                'delete_status'     => 0,
                'invoice_id'        => $invoice_id,
                'invoice_type'      => $invoice_type,
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') ));
        echo json_encode($data);
    }

    function add_recurrence_invoice()
    {
        $data                            = $this->get_default_country_state();
        $recurrence_module_id            = $this->config->item('recurrence_module');
        $data['module_id']               = $recurrence_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($recurrence_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $invoice_id                      = $this->encryption_url->decode($this->input->post('invoice_id'));
        $invoice_type                    = $this->input->post('invoice_type');
        $recurrence_date                 = $this->input->post('recurrence_date');
        $added_month_consider            = $this->input->post('added_month_consider');
        $month                           = date('m');
        $year                            = date('Y');
        foreach ($recurrence_date as $key => $value)
        {
            $generation_date      = $value . '-' . $month . '-' . $year;
            $next_generation_date = strtotime($generation_date);
            if ($added_month_consider == '1')
            {
                $next_generation_date = date('Y-m-d', $next_generation_date);
                if ($next_generation_date <= date('Y-m-d'))
                {
                    $next_generation_date = date("Y-m-d", strtotime("+1 month", strtotime($next_generation_date)));
                }
            }
            else
            {
                $next_generation_date = date("Y-m-d", strtotime("+1 month", $next_generation_date));
            }
            $recurrence_data = array(
                    'recurrence_date'      => $value,
                    'next_generation_date' => $next_generation_date,
                    'added_month_consider' => $added_month_consider,
                    'invoice_id'           => $invoice_id,
                    'invoice_type'         => $invoice_type,
                    'added_date'           => date('Y-m-d'),
                    'added_user_id'        => $this->session->userdata('SESS_USER_ID'),
                    'branch_id'            => $this->session->userdata('SESS_BRANCH_ID'),
                    'financial_year_id'    => $this->session->userdata('SESS_FINANCIAL_YEAR_ID') );
            if ($recurrence_id   = $this->general_model->insertData('recurrence', $recurrence_data))
            {
                $log_data  = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $recurrence_id,
                        'table_name'        => 'recurrence_table',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'Recurrence invoice inserted for ' . $invoice_type );
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table, $log_data);
            }
        }
        echo json_encode('success');
    }

    public function delete()
    {
        $recurrence_module_id            = $this->config->item('recurrence_module');
        $data['module_id']               = $recurrence_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($recurrence_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('recurrence', array(
                        'delete_status' => 1 ), array(
                        'recurrence_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'recurrence',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Recurrence Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('recurrence');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Recurrence can not be Deleted.');
            redirect("recurrence", 'refresh');
        }
    }

}
