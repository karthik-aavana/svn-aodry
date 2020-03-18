<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->library(array(
                'email' ));
        $this->load->helper(array(
                'email' ));
        $this->modules = $this->get_modules();
    }

    public function pending_sales()
    {
        $sales_module_id                 = $this->config->item('sales_module');
        $data['module_id']               = $sales_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($sales_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $email_sub_module_id             = $this->config->item('email_sub_module');
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
        } if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'invoice',
                    2 => 'customer',
                    3 => 'grand_total',
                    4 => 'paid_amount',
                    5 => 'payment_status',
                    6 => 'added_user',
                    7 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->pending_sales_list_field();
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
                    $nestedData['date']        = $post->sales_date;
                    $nestedData['invoice']     = $post->sales_invoice_number;
                    $nestedData['customer']    = $post->customer_name;
                    $nestedData['grand_total'] = $post->sales_grand_total;
                    $nestedData['paid_amount'] = $post->sales_paid_amount;
                    if ($post->sales_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if ($post->sales_paid_amount < ($post->sales_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = '<div class="dropdown">                    <button type="button" class="btn btn-default gropdown-toggle" data-toggle="dropdown">                    Action                    <span class="caret"></span>                    </button>                    <ul class="dropdown-menu dropdown-menu-right">                    <li>                    <a href="' . base_url('sales/view/') . $post->sales_id . '"><i class="fa fa-file-text-o text-orange"></i>Sales Details</a>                    </li>';
                    if ($data['access_module_privilege']->add_privilege == "yes")
                    {
                        if ($post->sales_paid_amount < ($post->sales_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                        {
                            $cols .= '<li>                            <a data-backdrop="static" data-keyboard="false" href="#" class="pay_now_button" data-toggle="modal" data-target="#pay_now_modal" data-id="' . $post->sales_id . '"><i class="fa fa-money text-yellow"></i>Pay Now</a>                            </li>';
                        }
                    } if ($data['access_module_privilege']->edit_privilege == "yes")
                    {
                        if ($post->sales_paid_amount == 0)
                        {
                            $cols .= '<li>                            <a href="' . base_url('sales/edit/') . $post->sales_id . '"><i class="fa fa-pencil text-blue"></i>Edit Sales</a>                            </li>';
                        }
                    } $cols             .= '<li>                    <a data-backdrop="static" data-keyboard="false" class="pdf_button" data-toggle="modal" data-id="' . $post->sales_id . '" data-name="regular" data-target="#pdf_type_modal" href="#" title="Download as PDF" >                    <i class="fa fa-file-pdf-o text-teal"></i>Download As PDF</a>                    </li>';
                    $cols             .= '<li>                    <a class="pdf_button" data-toggle="modal" data-target="#pdf_type_modal" data-name="revised" data-backdrop="static" data-keyboard="false" data-id="' . $post->sales_id . '"  href="#" title="Download as PDF" ><i class="fa fa-file-pdf-o text-teal"></i>Revised Invoice Tax</a>                    </li>';
                    $email_sub_module = 0;
                    foreach ($data['access_sub_modules'] as $key => $value)
                    {
                        if ($email_sub_module_id == $value->sub_module_id)
                        {
                            $email_sub_module = 1;
                        }
                    } if ($email_sub_module == 1)
                    {
                        $cols .= '<li>                                <a href="' . base_url('sales/email/') . $post->sales_id . '"><i class="fa fa-envelope-o text-purple"></i>Email Sales</a>                                </li>';
                    } if ($data['access_module_privilege']->delete_privilege == "yes")
                    {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $post->sales_id . '" data-path="sales/delete"  href="#" title="Delete Sales" ><i class="fa fa-trash-o text-purple"></i>Delete Sales</a>                       </li>';
                    } if ($data['access_module_privilege']->add_privilege == "yes")
                    {
                        if ($post->sales_paid_amount > 0)
                        {
                            $cols .= '<li>                            <a href="' . base_url('sales/add_credit_note/') . $post->sales_id . '"><i class="fa fa-file-o text-maroon"></i>Create Credit Note</a>                            </li>';
                            $cols .= '<li>                            <a href="' . base_url('sales/add_debit_note/') . $post->sales_id . '"><i class="fa fa-file-o text-maroon"></i>Create Debit Note</a>                            </li>';
                        }
                    } $cols                 .= '</ul></div>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('notification/pending_sales', $data);
        }
    }

    public function partial_sales()
    {
        $sales_module_id                 = $this->config->item('sales_module');
        $data['module_id']               = $sales_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($sales_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $email_sub_module_id             = $this->config->item('email_sub_module');
        $recurrence_sub_module_id        = $this->config->item('recurrence_sub_module');
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
        } if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'customer',
                    2 => 'grand_total',
                    3 => 'currency_converted_amount',
                    4 => 'paid_amount',
                    5 => 'payment_status',
                    6 => 'pending_amount',
                    7 => 'added_user',
                    8 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->sales_list_field();
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
                    $sales_id                  = $this->encryption_url->encode($post->sales_id);
                    $nestedData['date']        = $post->sales_date;
                    $nestedData['customer']    = $post->customer_name . ' (<a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $post->sales_grand_total . ' (INV)';
                    if ($post->credit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->credit_note_amount . ' (CN)';
                    } if ($post->debit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->debit_note_amount . ' (DN)';
                    } $nestedData['currency_converted_amount'] = $post->converted_grand_total;
                    $nestedData['paid_amount']               = $post->sales_paid_amount;
                    if ($post->sales_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if (($post->sales_paid_amount + $post->debit_note_amount) < ($post->sales_grand_total + $post->credit_note_amount))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } $nestedData['pending_amount'] = ($post->sales_grand_total + $post->credit_note_amount) - ($post->sales_paid_amount + $post->debit_note_amount);
                    $nestedData['added_user']     = $post->first_name . ' ' . $post->last_name;
                    $cols                         = '        <ul class="list-inline">        <li>        <a href="' . base_url('sales/view/') . $sales_id . '"><i class="fa fa-file-text-o text-orange"></i> Sales Details</a>        </li>';
                    $cols                         .= '<li>        <a href="#" data-toggle="modal" onclick="addToModel(' . $post->sales_id . ')" data-target="#myModal"><i class="fa fa-file-text-o text-orange"></i>Follow Up dates</a>        </li>';
                    if ($data['access_module_privilege']->add_privilege == "yes")
                    {
                        if ($post->sales_paid_amount < ($post->sales_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                        {
                            $cols .= '<li>                        <a href="' . base_url('receipt_voucher/add_sales_receipt/') . $sales_id . '"><i class="fa fa-money text-yellow"></i> Receive Now</a>                        </li>';
                        }
                    } if ($data['access_module_privilege']->edit_privilege == "yes")
                    {
                        if ($post->sales_paid_amount == 0 && $post->credit_note_amount == 0 && $post->debit_note_amount == 0)
                        {
                            $cols .= '<li>            <a href="' . base_url('sales/edit/') . $sales_id . '"><i class="fa fa-pencil text-blue"></i> Edit Sales</a>            </li>';
                        }
                    } $cols                  .= '<li>            <a href="' . base_url('advance_voucher/show_advance_voucher/') . $sales_id . '"><i class="fa fa-eye text-blue"></i> Show Advance Voucher</a>            </li>';
                    $cols                  .= '<li>    <a data-backdrop="static" data-keyboard="false" class="pdf_button" data-toggle="modal" data-id="' . $sales_id . '" data-name="regular" data-target="#pdf_type_modal" href="#" title="Download PDF" >    <i class="fa fa-file-pdf-o text-teal"></i> Download PDF</a>    </li>';
                    $cols                  .= '<li>    <a class="pdf_button" data-toggle="modal" data-target="#pdf_type_modal" data-name="revised" data-backdrop="static" data-keyboard="false" data-id="' . $sales_id . '"  href="#" title="Download PDF" ><i class="fa fa-file-pdf-o text-teal"></i> Revised Invoice Tax</a>    </li>';
                    $email_sub_module      = 0;
                    $recurrence_sub_module = 0;
                    if ($data['access_module_privilege']->add_privilege == "yes")
                    {
                        foreach ($data['access_sub_modules'] as $key => $value)
                        {
                            if ($email_sub_module_id == $value->sub_module_id)
                            {
                                $email_sub_module = 1;
                            } if ($recurrence_sub_module_id == $value->sub_module_id)
                            {
                                $recurrence_sub_module = 1;
                            }
                        }
                    } if ($email_sub_module == 1)
                    {
                        $cols .= '<li>                <a href="' . base_url('sales/email/') . $sales_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Sales</a>                </li>';
                    } if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<li>                   <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $sales_id . '" data-path="sales/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->sales_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-money"></i> Convert Currency</a>                </li>';
                    } if ($recurrence_sub_module == 1)
                    {
                        $cols .= '<li>                <a class="recurrence_invoice" data-toggle="modal" data-target="#recurrence_invoice" data-id="' . $sales_id . '" data-type="sales" href="#" title="Generate Recurrence Invoice" ><i class="fa fa-pencil text-blue"></i> Generate Recurrence Invoice</a>                </li>';
                    } if ($data['access_module_privilege']->delete_privilege == "yes")
                    {
                        $cols .= '<li>                <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $sales_id . '" data-path="sales/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" href="#" title="Delete Sales"><i class="fa fa-trash-o text-purple"></i> Delete Sales</a>                </li>';
                    } $cols                 .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $list_data        = $this->common->sales_list_field();
            $data['posts']    = $this->general_model->getPageJoinRecords($list_data);
            $this->load->view('notification/notify_sales', $data);
        }
    }

    public function pending_purchase()
    {
        $purchase_module_id              = $this->config->item('purchase_module');
        $data['module_id']               = $purchase_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($purchase_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $email_sub_module_id             = $this->config->item('email_sub_module');
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
        } $purchase_return_module_id = $this->config->item('purchase_return_module');
        $modules_present           = array(
                'purchase_return_module_id' => $purchase_return_module_id );
        $other_modules_present     = $this->other_modules_present($modules_present, $modules['modules']);
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'supplier',
                    2 => 'grand_total',
                    3 => 'paid_amount',
                    4 => 'payment_status',
                    5 => 'added_user',
                    6 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->pending_purchase_list_field();
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
                    $nestedData['date']        = $post->purchase_date;
                    $nestedData['supplier']    = $post->supplier_name . ' (<a href="' . base_url('purchase/view/') . $post->purchase_id . '">' . $post->purchase_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $post->purchase_grand_total;
                    $nestedData['paid_amount'] = $post->purchase_paid_amount;
                    if ($post->purchase_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if ($post->purchase_paid_amount < ($post->purchase_grand_total))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = '<div class="dropdown">                    <button type="button" class="btn btn-default gropdown-toggle" data-toggle="dropdown">                    Action                    <span class="caret"></span>                    </button>                    <ul class="dropdown-menu dropdown-menu-right">                    <li>                    <a href="' . base_url('purchase/view/') . $post->purchase_id . '"><i class="fa fa-file-text-o text-orange"></i>Purchase Details</a>                    </li>';
                    if ($data['access_module_privilege']->edit_privilege == "yes")
                    {
                        if ($post->purchase_paid_amount == 0)
                        {
                            $cols .= '<li>                            <a href="' . base_url('purchase/edit/') . $post->purchase_id . '"><i class="fa fa-pencil text-blue"></i>Edit Purchase</a>                            </li>';
                        }
                    } if (isset($other_modules_present['purchase_return_module_id']) && $other_modules_present['purchase_return_module_id'] != "")
                    {
                        $cols .= '<li>                            <a href="' . base_url('purchase/purchase_return/') . $post->purchase_id . '"><i class="fa fa-truck text-purple"></i>Purchase Return</a>                            </li>';
                    } $cols             .= '<li>                            <a href="' . base_url('purchase/pdf/') . $post->purchase_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i>Download as PDF</a>                            </li>';
                    $email_sub_module = 0;
                    foreach ($data['access_sub_modules'] as $key => $value)
                    {
                        if ($email_sub_module_id == $value->sub_module_id)
                        {
                            $email_sub_module = 1;
                        }
                    } if ($email_sub_module == 1)
                    {
                        $cols .= '<li>                                <a href="' . base_url('purchase/email/') . $post->purchase_id . '"><i class="fa fa-envelope-o text-purple"></i>Email Purchase</a>                                </li>';
                    } if ($data['access_module_privilege']->delete_privilege == "yes")
                    {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $post->purchase_id . '" data-path="purchase/delete"  href="#" title="Delete Purchase" ><i class="fa fa-trash-o text-purple"></i>Delete Purchase</a>                       </li>';
                    } $cols                 .= '</ul></div>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('notification/pending_purchase', $data);
        }
    }

    public function partial_purchase()
    {
        $purchase_module_id              = $this->config->item('purchase_module');
        $data['module_id']               = $purchase_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($purchase_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];
        $email_sub_module_id             = $this->config->item('email_sub_module');
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
        } $purchase_return_module_id = $this->config->item('purchase_return_module');
        $modules_present           = array(
                'purchase_return_module_id' => $purchase_return_module_id );
        $other_modules_present     = $this->other_modules_present($modules_present, $modules['modules']);
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'supplier',
                    2 => 'grand_total',
                    3 => 'paid_amount',
                    4 => 'payment_status',
                    5 => 'added_user',
                    6 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->partial_purchase_list_field();
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
                    $nestedData['date']        = $post->purchase_date;
                    $nestedData['supplier']    = $post->supplier_name . ' (<a href="' . base_url('purchase/view/') . $post->purchase_id . '">' . $post->purchase_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $post->purchase_grand_total;
                    $nestedData['paid_amount'] = $post->purchase_paid_amount;
                    if ($post->purchase_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if ($post->purchase_paid_amount < ($post->purchase_grand_total))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = '<div class="dropdown">                    <button type="button" class="btn btn-default gropdown-toggle" data-toggle="dropdown">                    Action                    <span class="caret"></span>                    </button>                    <ul class="dropdown-menu dropdown-menu-right">                    <li>                    <a href="' . base_url('purchase/view/') . $post->purchase_id . '"><i class="fa fa-file-text-o text-orange"></i>Purchase Details</a>                    </li>';
                    if ($data['access_module_privilege']->edit_privilege == "yes")
                    {
                        if ($post->purchase_paid_amount == 0)
                        {
                            $cols .= '<li>                            <a href="' . base_url('purchase/edit/') . $post->purchase_id . '"><i class="fa fa-pencil text-blue"></i>Edit Purchase</a>                            </li>';
                        }
                    } if (isset($other_modules_present['purchase_return_module_id']) && $other_modules_present['purchase_return_module_id'] != "")
                    {
                        $cols .= '<li>                            <a href="' . base_url('purchase/purchase_return/') . $post->purchase_id . '"><i class="fa fa-truck text-purple"></i>Purchase Return</a>                            </li>';
                    } $cols             .= '<li>                            <a href="' . base_url('purchase/pdf/') . $post->purchase_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i>Download as PDF</a>                            </li>';
                    $email_sub_module = 0;
                    foreach ($data['access_sub_modules'] as $key => $value)
                    {
                        if ($email_sub_module_id == $value->sub_module_id)
                        {
                            $email_sub_module = 1;
                        }
                    } if ($email_sub_module == 1)
                    {
                        $cols .= '<li>                                <a href="' . base_url('purchase/email/') . $post->purchase_id . '"><i class="fa fa-envelope-o text-purple"></i>Email Purchase</a>                                </li>';
                    } if ($data['access_module_privilege']->delete_privilege == "yes")
                    {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $post->purchase_id . '" data-path="purchase/delete"  href="#" title="Delete Purchase" ><i class="fa fa-trash-o text-purple"></i>Delete Purchase</a>                       </li>';
                    } $cols                 .= '</ul></div>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('notification/partial_purchase', $data);
        }
    }

    function notify_sales()
    {
        $sales_module_id            = $this->config->item('sales_module');
        $modules                    = $this->modules;
        $privilege                  = "view_privilege";
        $data['privilege']          = $privilege;
        $section_modules            = $this->get_section_modules($sales_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* Modules Present */
        $data['sales_module_id']    = $sales_module_id;
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id']           = $this->config->item('email_module');
        $data['recurrence_module_id']      = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id']  = $this->config->item('recurrence_sub_module');

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'customer',
                    2 => 'grand_total',
                    3 => 'currency_converted_amount',
                    4 => 'paid_amount',
                    5 => 'payment_status',
                    6 => 'pending_amount',
                    7 => 'added_user',
                    8 => 'action',);
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->followup_sales_list_field();
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
            }

            $this->general_model->change_followup_status('sales');

            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $sales_id = $this->encryption_url->encode($post->sales_id);

                    $nestedData['date']        = $post->sales_date;
                    $nestedData['customer']    = $post->customer_name . ' (<a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $post->sales_grand_total . ' (INV)';
                    if ($post->credit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->credit_note_amount . ' (CN)';
                    }
                    if ($post->debit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->debit_note_amount . ' (DN)';
                    }
                    $nestedData['currency_converted_amount'] = $post->converted_grand_total;
                    $nestedData['paid_amount']               = $post->sales_paid_amount;
                    if ($post->sales_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if (($post->sales_paid_amount + $post->debit_note_amount) < ($post->sales_grand_total + $post->credit_note_amount))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }
                    // $nestedData['pending_amount'] = ($post->sales_grand_total + $post->credit_note_amount) - ($post->sales_paid_amount + $post->debit_note_amount);
                    $nestedData['pending_amount'] = bcsub(bcadd($post->sales_grand_total, $post->credit_note_amount, 2), bcadd($post->sales_paid_amount, $post->debit_note_amount, 2), 2);
                    $nestedData['added_user']     = $post->first_name . ' ' . $post->last_name;

                    $nestedData['follow_up_status'] = $post->follow_up_status;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';


                    if (in_array($sales_module_id, $data['active_view']))
                    {
                        $cols .= '<a href="' . base_url('sales/view/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="View Bill">
                                    <i class="fa fa-file-text-o"></i>
                            </a>';
                    }
                    if (in_array($sales_module_id, $data['active_edit']))
                    {
                        if ($post->sales_paid_amount == 0 && $post->credit_note_amount == 0 && $post->debit_note_amount == 0)
                        {
                            $cols .= '<a href="' . base_url('sales/edit/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Edit">
                                    <i class="fa fa-pencil"></i>
                            </a>';
                        }
                    }
                    if (in_array($data['receipt_voucher_module_id'], $data['active_add']))
                    {
                        if ($post->sales_paid_amount < ($post->sales_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                        {
                            $cols .= '<a href="' . base_url('receipt_voucher/add_sales_receipt/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Receive Payment">
                                    <i class="fa fa-money"></i>
                            </a>';
                        }
                    }

                    if (in_array($data['advance_voucher_module_id'], $data['active_view']))
                    {
                        $cols .= '<a href="' . base_url('advance_voucher/show_advance_voucher/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Advance Vouchers">
                                    <i class="fa fa-book"></i>
                            </a>';
                    }


                    $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" onclick="addToModel(' . $post->sales_id . ')" data-target="#followUp" class="btn btn-app" title="Follow Up Dates">
                                    <i class="fa fa-book"></i>
                            </a>';

                    // $cols                         .= '<li>        <a href="#" data-toggle="modal" onclick="addToModel(' . $post->sales_id . ')" data-target="#myModal"><i class="fa fa-file-text-o text-orange"></i>Follow Up dates</a>        </li>';

                    if (in_array($sales_module_id, $data['active_view']))
                    {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal"  class="btn btn-app pdf_button" data-id="' . $sales_id . '" data-name="regular" data-target="#pdf_type_modal" href="#" title="Download PDF">
                                    <i class="fa fa-file-pdf-o"></i>
                            </a>';
                    }

                    // $cols.='<a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" class="btn btn-app pdf_button" data-id="' . $sales_id . '" data-name="revised" data-target="#pdf_type_modal" href="#" title="Revised Tax Invoice">
                    //               <i class="fa fa-file-pdf-o"></i>
                    //       </a>';

                   
                    if (in_array($data['email_module_id'], $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $cols .= '<a href="' . base_url('sales/email/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Email Bill">
                                    <i class="fa fa-envelope-o"></i>
                            </a>';
                        }
                    }
                    if (in_array($data['recurrence_module_id'], $data['active_add']))
                    {
                            if (in_array($data['recurrence_sub_module_id'], $data['access_sub_modules']))
                            {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" class="btn btn-app recurrence_invoice" data-toggle="modal" data-target="#recurrence_invoice" data-id="' . $sales_id . '" data-type="sales" href="#" title="Generate Recurrence Invoice">
                                    <i class="fa fa-file-text-o"></i>
                            </a>';
                    }
                }
                    if (in_array($sales_module_id, $data['active_delete']))
                    {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" class="btn btn-app delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $sales_id . '" data-type="sales/delete" href="#" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" title="Delete Sales">
                                    <i class="fa fa-trash-o"></i>
                            </a>';
                    }

                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" class="btn btn-app convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $sales_id . '" data-path="sales/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->sales_grand_total . '" title="Convert Currency">
                                    <i class="fa fa-money"></i>
                            </a>';
                    }
                    $cols .= '</div>';
                    // $cols .= '<div class="pull-right">
                    //             <div class="btn-group">';
                    // $cols .= '<button type="button" class="btn btn-default btn-rounded">
                    //                     <span class="fa fa-file-pdf-o"></span>
                    //                 </button>';
                    // $cols .= '</div>';
                    // $cols .= '<div class="btn-group">';
                    // $cols .= '<button type="button" class="btn btn-default btn-rounded" data-toggle="dropdown">
                    //                     <span class="fa fa-trash-o"></span>
                    //                 </button>';
                    // $cols .= '</div>';
                    // $cols .= '</div>';

                    $cols                 .= '</div>';
//                    $nestedData['action'] = $cols;
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]          = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data);
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('notification/notify_sales', $data);
        }
    }

    function default_notify_sales()
    {
        $sales_module_id            = $this->config->item('sales_module');
        $modules                    = $this->modules;
        $privilege                  = "view_privilege";
        $data['privilege']          = $privilege;
        $section_modules            = $this->get_section_modules($sales_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* Modules Present */
        $data['sales_module_id']    = $sales_module_id;
        $data['receipt_voucher_module_id'] = $this->config->item('receipt_voucher_module');
        $data['advance_voucher_module_id'] = $this->config->item('advance_voucher_module');
        $data['email_module_id']           = $this->config->item('email_module');
        $data['recurrence_module_id']      = $this->config->item('recurrence_module');
        /* Sub Modules Present */
        $data['email_sub_module_id']       = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id']  = $this->config->item('recurrence_sub_module');

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'customer',
                    2 => 'grand_total',
                    3 => 'currency_converted_amount',
                    4 => 'paid_amount',
                    5 => 'payment_status',
                    6 => 'pending_amount',
                    7 => 'added_user',
                    8 => 'action',);
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];

            $def_date = $this->general_model->getRecords('default_notification_date','common_settings',array('branch_id'=>$this->session->userdata('SESS_BRANCH_ID'),'delete_status'=>0));

            $list_data           = $this->common->followup_sales_list_field($def_date[0]->default_notification_date);
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
            }

            // $this->general_model->change_followup_status('sales');

            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $sales_id = $this->encryption_url->encode($post->sales_id);

                    $nestedData['date']        = $post->sales_date;
                    $nestedData['customer']    = $post->customer_name . ' (<a href="' . base_url('sales/view/') . $sales_id . '">' . $post->sales_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $post->sales_grand_total . ' (INV)';
                    if ($post->credit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->credit_note_amount . ' (CN)';
                    }
                    if ($post->debit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->debit_note_amount . ' (DN)';
                    }
                    $nestedData['currency_converted_amount'] = $post->converted_grand_total;
                    $nestedData['paid_amount']               = $post->sales_paid_amount;
                    if ($post->sales_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if (($post->sales_paid_amount + $post->debit_note_amount) < ($post->sales_grand_total + $post->credit_note_amount))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }

                    $nestedData['pending_amount'] = bcsub(bcadd($post->sales_grand_total, $post->credit_note_amount, 2), bcadd($post->sales_paid_amount, $post->debit_note_amount, 2), 2);
                    $nestedData['added_user']     = $post->first_name . ' ' . $post->last_name;

                    $nestedData['follow_up_status'] = $post->follow_up_status;

                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';


                    if (in_array($sales_module_id, $data['active_view']))
                    {
                        $cols .= '<a href="' . base_url('sales/view/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="View Bill">
                                    <i class="fa fa-file-text-o"></i>
                            </a>';
                    }
                    if (in_array($sales_module_id, $data['active_edit']))
                    {
                        if ($post->sales_paid_amount == 0 && $post->credit_note_amount == 0 && $post->debit_note_amount == 0)
                        {
                            $cols .= '<a href="' . base_url('sales/edit/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Edit">
                                    <i class="fa fa-pencil"></i>
                            </a>';
                        }
                    }
                    if (in_array($data['receipt_voucher_module_id'], $data['active_add']))
                    {
                        if ($post->sales_paid_amount < ($post->sales_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                        {
                            $cols .= '<a href="' . base_url('receipt_voucher/add_sales_receipt/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Receive Payment">
                                    <i class="fa fa-money"></i>
                            </a>';
                        }
                    }

                    if (in_array($data['advance_voucher_module_id'], $data['active_view']))
                    {
                        $cols .= '<a href="' . base_url('advance_voucher/show_advance_voucher/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Advance Vouchers">
                                    <i class="fa fa-book"></i>
                            </a>';
                    }


                    $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal" onclick="addToModel(' . $post->sales_id . ')" data-target="#followUp" class="btn btn-app" title="Follow Up Dates">
                                    <i class="fa fa-book"></i>
                            </a>';

                    if (in_array($sales_module_id, $data['active_view']))
                    {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" data-toggle="modal"  class="btn btn-app pdf_button" data-id="' . $sales_id . '" data-name="regular" data-target="#pdf_type_modal" href="#" title="Download PDF">
                                    <i class="fa fa-file-pdf-o"></i>
                            </a>';
                    }
                   
                    if (in_array($data['email_module_id'], $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $cols .= '<a href="' . base_url('sales/email/') . $sales_id . '" class="btn btn-app" data-toggle="tooltip" title="Email Bill">
                                    <i class="fa fa-envelope-o"></i>
                            </a>';
                        }
                    }
                    if (in_array($data['recurrence_module_id'], $data['active_add']))
                    {
                            if (in_array($data['recurrence_sub_module_id'], $data['access_sub_modules']))
                            {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" class="btn btn-app recurrence_invoice" data-toggle="modal" data-target="#recurrence_invoice" data-id="' . $sales_id . '" data-type="sales" href="#" title="Generate Recurrence Invoice">
                                    <i class="fa fa-file-text-o"></i>
                            </a>';
                    }
                }
                    if (in_array($sales_module_id, $data['active_delete']))
                    {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" class="btn btn-app delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $sales_id . '" data-type="sales/delete" href="#" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" title="Delete Sales">
                                    <i class="fa fa-trash-o"></i>
                            </a>';
                    }

                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<a data-backdrop="static" data-keyboard="false" href="#" class="btn btn-app convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $sales_id . '" data-path="sales/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->sales_grand_total . '" title="Convert Currency">
                                    <i class="fa fa-money"></i>
                            </a>';
                    }
                    $cols .= '</div>';

                    $cols                 .= '</div>';

                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';

                    $send_data[]          = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data);
            echo json_encode($json_data);
        }
        else
        {
            $data['currency'] = $this->currency_call();
            $this->load->view('notification/default_notify_sales', $data);
        }
    }

    function notify_purchase()
    {
        $purchase_module_id              = $this->config->item('purchase_module');
        $data['purchase_module_id']      = $purchase_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($purchase_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['email_sub_module_id']           = $this->config->item('email_sub_module');
        $data['purchase_return_module_id']     = $this->config->item('purchase_return_module');
        // $modules_present               = array(
        //         'purchase_return_module_id' => $purchase_return_module_id );
        // $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'supplier',
                    2 => 'grand_total',
                    3 => 'currency_converted_amount',
                    4 => 'paid_amount',
                    5 => 'payment_status',
                    6 => 'added_user',
                    7 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->followup_purchase_list_field();
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
            }

            $this->general_model->change_followup_status('purchase');

            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $purchase_id               = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date']        = $post->purchase_date;
                    $nestedData['supplier']    = $post->supplier_name . ' (<a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $post->purchase_grand_total . ' (INV)';
                    if ($post->credit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->credit_note_amount . ' (CN)';
                    } if ($post->debit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->debit_note_amount . ' (DN)';
                    } $nestedData['currency_converted_amount'] = $post->converted_grand_total;
                    $nestedData['paid_amount']               = $post->purchase_paid_amount;
                    if ($post->purchase_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if (($post->purchase_paid_amount + $post->debit_note_amount) < ($post->purchase_grand_total + $post->credit_note_amount))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    }
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;

                    $nestedData['follow_up_status'] = $post->follow_up_status;

                    $cols                     = '<ul class="action_ul_custom">                    <li>                    <a href="' . base_url('purchase/view/') . $purchase_id . '"><i class="fa fa-file-text-o text-orange"></i> Purchase Details</a>                    </li>';
                    $cols                     .= '<li>                    <a href="#" data-toggle="modal" onclick="addToModel(' . $post->purchase_id . ')" data-target="#myModal1"><i class="fa fa-file-text-o text-orange"></i> Follow Up dates</a>                    </li>';
                    if(in_array($purchase_module_id, $data['active_add']))
                    {
                        if ($post->purchase_paid_amount < ($post->purchase_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                        {
                            $cols .= '<li>                                    <a href="' . base_url('payment_voucher/add_purchase_payment/') . $purchase_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                                    </li>';
                        }
                    }
                    if(in_array($purchase_module_id, $data['active_edit']))
                    {
                        if ($post->purchase_paid_amount == 0 && $post->credit_note_amount == 0 && $post->debit_note_amount == 0)
                        {
                            $cols .= '<li>                            <a href="' . base_url('purchase/edit/') . $purchase_id . '"><i class="fa fa-pencil text-blue"></i> Edit Purchase</a>                            </li>';
                        }
                    }
                    // if (isset($data['other_modules_present']['purchase_return_module_id']) && $data['other_modules_present']['purchase_return_module_id'] != "")
                    if (in_array($data['purchase_return_module_id'], $data['access_sub_modules']))
                    {
                        $cols .= '<li>                            <a href="' . base_url('purchase/purchase_return/') . $purchase_id . '"><i class="fa fa-truck text-purple"></i> Purchase Return</a>                            </li>';
                    }
                    $cols             .= '<li>                            <a href="' . base_url('purchase/pdf/') . $purchase_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i> Download PDF</a>                            </li>';
                    // $email_sub_module = 0;
                    if(in_array($purchase_module_id, $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $cols .= '<li><a href="' . base_url('purchase/email/') . $purchase_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Purchase</a></li>';
                        }
                    }
                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<li>                               <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $purchase_id . '" data-path="purchase/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->purchase_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>                            </li>';
                    }
                    if(in_array($purchase_module_id, $data['active_delete']))
                    {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $purchase_id . '" data-path="purchase/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" href="#" title="Delete Purchase" ><i class="fa fa-trash-o text-purple"></i> Delete Purchase</a>                       </li>';
                    } $cols                 .= '</ul>';
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
            $this->load->view('Notification/notify_purchase', $data);
        }
    }

    function default_notify_purchase()
    {
        $purchase_module_id              = $this->config->item('purchase_module');
        $data['purchase_module_id']      = $purchase_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($purchase_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['email_sub_module_id']           = $this->config->item('email_sub_module');
        $data['purchase_return_module_id']     = $this->config->item('purchase_return_module');
        // $modules_present               = array(
        //         'purchase_return_module_id' => $purchase_return_module_id );
        // $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'supplier',
                    2 => 'grand_total',
                    3 => 'currency_converted_amount',
                    4 => 'paid_amount',
                    5 => 'payment_status',
                    6 => 'added_user',
                    7 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];

            $def_date = $this->general_model->getRecords('default_notification_date','common_settings',array('branch_id'=>$this->session->userdata('SESS_BRANCH_ID'),'delete_status'=>0));

            $list_data           = $this->common->followup_purchase_list_field($def_date[0]->default_notification_date);
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
            }

            $this->general_model->change_followup_status('purchase');

            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $purchase_id               = $this->encryption_url->encode($post->purchase_id);
                    $nestedData['date']        = $post->purchase_date;
                    $nestedData['supplier']    = $post->supplier_name . ' (<a href="' . base_url('purchase/view/') . $purchase_id . '">' . $post->purchase_invoice_number . '</a>) ';
                    $nestedData['grand_total'] = $post->currency_symbol . ' ' . $post->purchase_grand_total . ' (INV)';
                    if ($post->credit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->credit_note_amount . ' (CN)';
                    } if ($post->debit_note_amount > 0)
                    {
                        $nestedData['grand_total'] .= '<br>' . $post->currency_symbol . ' ' . $post->debit_note_amount . ' (DN)';
                    } $nestedData['currency_converted_amount'] = $post->converted_grand_total;
                    $nestedData['paid_amount']               = $post->purchase_paid_amount;
                    if ($post->purchase_paid_amount == 0)
                    {
                        $nestedData['payment_status'] = '<span class="label label-danger">Pending</span>';
                    }
                    else if (($post->purchase_paid_amount + $post->debit_note_amount) < ($post->purchase_grand_total + $post->credit_note_amount))
                    {
                        $nestedData['payment_status'] = '<span class="label label-warning">Partial</span>';
                    }
                    else
                    {
                        $nestedData['payment_status'] = '<span class="label label-success">Completed</span>';
                    } $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = '<ul class="action_ul_custom">                    <li>                    <a href="' . base_url('purchase/view/') . $purchase_id . '"><i class="fa fa-file-text-o text-orange"></i> Purchase Details</a>                    </li>';
                    $cols                     .= '<li>                    <a href="#" data-toggle="modal" onclick="addToModel(' . $post->purchase_id . ')" data-target="#myModal1"><i class="fa fa-file-text-o text-orange"></i> Follow Up dates</a>                    </li>';
                    if(in_array($purchase_module_id, $data['active_add']))
                    {
                        if ($post->purchase_paid_amount < ($post->purchase_grand_total + $post->credit_note_amount - $post->debit_note_amount))
                        {
                            $cols .= '<li>                                    <a href="' . base_url('payment_voucher/add_purchase_payment/') . $purchase_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                                    </li>';
                        }
                    }
                    if(in_array($purchase_module_id, $data['active_edit']))
                    {
                        if ($post->purchase_paid_amount == 0 && $post->credit_note_amount == 0 && $post->debit_note_amount == 0)
                        {
                            $cols .= '<li>                            <a href="' . base_url('purchase/edit/') . $purchase_id . '"><i class="fa fa-pencil text-blue"></i> Edit Purchase</a>                            </li>';
                        }
                    }
                    // if (isset($data['other_modules_present']['purchase_return_module_id']) && $data['other_modules_present']['purchase_return_module_id'] != "")
                    if (in_array($data['purchase_return_module_id'], $data['access_sub_modules']))
                    {
                        $cols .= '<li>                            <a href="' . base_url('purchase/purchase_return/') . $purchase_id . '"><i class="fa fa-truck text-purple"></i> Purchase Return</a>                            </li>';
                    }
                    $cols             .= '<li>                            <a href="' . base_url('purchase/pdf/') . $purchase_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i> Download PDF</a>                            </li>';
                    // $email_sub_module = 0;
                    if(in_array($purchase_module_id, $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $cols .= '<li><a href="' . base_url('purchase/email/') . $purchase_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Purchase</a></li>';
                        }
                    }
                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<li>                               <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $purchase_id . '" data-path="purchase/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->purchase_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>                            </li>';
                    }
                    if(in_array($purchase_module_id, $data['active_delete']))
                    {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $purchase_id . '" data-path="purchase/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?" href="#" title="Delete Purchase" ><i class="fa fa-trash-o text-purple"></i> Delete Purchase</a>                       </li>';
                    } $cols                 .= '</ul>';
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
            $this->load->view('Notification/default_notify_purchase', $data);
        }
    }

    function notify_expense_bill()
    {
        $expense_bill_module_id          = $this->config->item('expense_bill_module');
        $data['expense_bill_module_id']  = $expense_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['email_sub_module_id']             = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id']        = $this->config->item('recurrence_sub_module');
        
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'invoice',
                    2 => 'payee',
                    3 => 'grand_total',
                    4 => 'currency_converted_amount',
                    5 => 'paid_amount',
                    6 => 'added_user',
                    7 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->followup_expense_bill_list_field();
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
            }

            $this->general_model->change_followup_status('expense_bill');

            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $expense_bill_id                         = $this->encryption_url->encode($post->expense_bill_id);

                    $nestedData['date']                      = $post->expense_bill_date;
                    $nestedData['invoice']                   = $post->expense_bill_invoice_number;
                    $nestedData['payee']                     = $post->supplier_name;
                    $nestedData['grand_total']               = $post->currency_symbol . ' ' . $post->expense_bill_grand_total;
                    $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['paid_amount']               = $post->expense_bill_paid_amount;
                    $nestedData['added_user']                = $post->first_name . ' ' . $post->last_name;

                    $nestedData['follow_up_status'] = $post->follow_up_status;

                    $cols                                    = '<ul class="list-inline">            <li>            <a href="' . base_url('expense_bill/view/') . $expense_bill_id . '"><i class="fa fa-file-text-o text-orange"></i> Expense Bill Details</a>            </li>';
                    $cols                                    .= '<li>        <a href="#" data-toggle="modal" onclick="addToModel(' . $post->expense_bill_id . ')" data-target="#myModal"><i class="fa fa-file-text-o text-orange"></i>Follow Up dates</a>        </li>';

                    if (in_array($expense_bill_module_id, $data['active_add']))
                    {
                        if ($post->expense_bill_paid_amount < $post->expense_bill_grand_total)
                        {
                            $cols .= '<li>                            <a href="' . base_url('payment_voucher/add_expense_payment/') . $expense_bill_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                            </li>';
                        }
                    }
                    if (in_array($expense_bill_module_id, $data['active_edit']))
                    {
                        if ($post->expense_bill_paid_amount == 0 || $post->expense_bill_paid_amount == null)
                        {
                            $cols .= '<li>                <a href="' . base_url('expense_bill/edit/') . $expense_bill_id . '"><i class="fa fa-pencil text-blue"></i> Edit Expense Bill</a>                </li>';
                        }
                    } $cols                  .= '<li>                        <a href="' . base_url('expense_bill/pdf/') . $expense_bill_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i> Download PDF</a>                    </li>';
                    $email_sub_module      = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($expense_bill_module_id, $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $email_sub_module = 1;
                        }
                        if (in_array($data['recurrence_sub_module_id'], $data['access_sub_modules']))
                        {
                            $recurrence_sub_module = 1;
                        }
                    }
                    if ($email_sub_module == 1)
                    {
                        $cols .= '<li>                        <a href="' . base_url('expense_bill/email/') . $expense_bill_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Expense Bill</a>                        </li>';
                    }
                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $expense_bill_id . '" data-path="expense_bill/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->expense_bill_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>                    </li>';
                    }
                    if ($recurrence_sub_module == 1)
                    {
                        $cols .= '<li>                        <a class="recurrence_invoice" data-toggle="modal" data-target="#recurrence_invoice" data-id="' . $expense_bill_id . '" data-type="expense_bill" href="#" title="Generate Recurrence Invoice" ><i class="fa fa-pencil text-blue"></i> Generate Recurrence Invoice</a>                        </li>';
                    }
                    if (in_array($expense_bill_module_id, $data['active_delete']))
                    {
                        $cols .= '<li>               <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $expense_bill_id . '" data-path="expense_bill/delete"  href="#" title="Delete Expense Bill" ><i class="fa fa-trash-o text-purple"></i> Delete Expense Bill</a>               </li>';
                    } $cols                 .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view("notification/notify_expense_bill", $data);
        }
    }

    function default_notify_expense_bill()
    {
        $expense_bill_module_id          = $this->config->item('expense_bill_module');
        $data['expense_bill_module_id']  = $expense_bill_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($expense_bill_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['email_sub_module_id']             = $this->config->item('email_sub_module');
        $data['recurrence_sub_module_id']        = $this->config->item('recurrence_sub_module');
        
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'date',
                    1 => 'invoice',
                    2 => 'payee',
                    3 => 'grand_total',
                    4 => 'currency_converted_amount',
                    5 => 'paid_amount',
                    6 => 'added_user',
                    7 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];

            $def_date = $this->general_model->getRecords('default_notification_date','common_settings',array('branch_id'=>$this->session->userdata('SESS_BRANCH_ID'),'delete_status'=>0));

            $list_data           = $this->common->followup_expense_bill_list_field($def_date[0]->default_notification_date);
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
            }

            // $this->general_model->change_followup_status('sales');

            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $nestedData['date']                      = $post->expense_bill_date;
                    $nestedData['invoice']                   = $post->expense_bill_invoice_number;
                    $nestedData['payee']                     = $post->supplier_name;
                    $nestedData['grand_total']               = $post->currency_symbol . ' ' . $post->expense_bill_grand_total;
                    $nestedData['currency_converted_amount'] = $post->currency_converted_amount;
                    $nestedData['paid_amount']               = $post->expense_bill_paid_amount;
                    $nestedData['added_user']                = $post->first_name . ' ' . $post->last_name;
                    $expense_bill_id                         = $this->encryption_url->encode($post->expense_bill_id);
                    $cols                                    = '<ul class="list-inline">            <li>            <a href="' . base_url('expense_bill/view/') . $expense_bill_id . '"><i class="fa fa-file-text-o text-orange"></i> Expense Bill Details</a>            </li>';
                    $cols                                    .= '<li>        <a href="#" data-toggle="modal" onclick="addToModel(' . $post->expense_bill_id . ')" data-target="#myModal"><i class="fa fa-file-text-o text-orange"></i>Follow Up dates</a>        </li>';

                    if (in_array($expense_bill_module_id, $data['active_add']))
                    {
                        if ($post->expense_bill_paid_amount < $post->expense_bill_grand_total)
                        {
                            $cols .= '<li>                            <a href="' . base_url('payment_voucher/add_expense_payment/') . $expense_bill_id . '"><i class="fa fa-money text-yellow"></i> Pay Now</a>                            </li>';
                        }
                    }
                    if (in_array($expense_bill_module_id, $data['active_edit']))
                    {
                        if ($post->expense_bill_paid_amount == 0 || $post->expense_bill_paid_amount == null)
                        {
                            $cols .= '<li>                <a href="' . base_url('expense_bill/edit/') . $expense_bill_id . '"><i class="fa fa-pencil text-blue"></i> Edit Expense Bill</a>                </li>';
                        }
                    } $cols                  .= '<li>                        <a href="' . base_url('expense_bill/pdf/') . $expense_bill_id . '" target="_blank"><i class="fa fa-file-pdf-o text-green"></i> Download PDF</a>                    </li>';
                    $email_sub_module      = 0;
                    $recurrence_sub_module = 0;
                    if (in_array($expense_bill_module_id, $data['active_view']))
                    {
                        if (in_array($data['email_sub_module_id'], $data['access_sub_modules']))
                        {
                            $email_sub_module = 1;
                        }
                        if (in_array($data['recurrence_sub_module_id'], $data['access_sub_modules']))
                        {
                            $recurrence_sub_module = 1;
                        }
                    }
                    if ($email_sub_module == 1)
                    {
                        $cols .= '<li>                        <a href="' . base_url('expense_bill/email/') . $expense_bill_id . '"><i class="fa fa-envelope-o text-purple"></i> Email Expense Bill</a>                        </li>';
                    }
                    if ($post->currency_id != $this->session->userdata('SESS_DEFAULT_CURRENCY'))
                    {
                        $cols .= '<li>                       <a data-backdrop="static" data-keyboard="false" class="convert_currency" data-toggle="modal" data-target="#convert_currency_modal" data-id="' . $expense_bill_id . '" data-path="expense_bill/convert_currency" data-currency_code="' . $post->currency_code . '" data-grand_total="' . $post->expense_bill_grand_total . '" href="#" title="Convert Currency" ><i class="fa fa-exchange"></i> Convert Currency</a>                    </li>';
                    } if ($recurrence_sub_module == 1)
                    {
                        $cols .= '<li>                        <a class="recurrence_invoice" data-toggle="modal" data-target="#recurrence_invoice" data-id="' . $expense_bill_id . '" data-type="expense_bill" href="#" title="Generate Recurrence Invoice" ><i class="fa fa-pencil text-blue"></i> Generate Recurrence Invoice</a>                        </li>';
                    }
                    if (in_array($expense_bill_module_id, $data['active_delete']))
                    {
                        $cols .= '<li>               <a data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $expense_bill_id . '" data-path="expense_bill/delete"  href="#" title="Delete Expense Bill" ><i class="fa fa-trash-o text-purple"></i> Delete Expense Bill</a>               </li>';
                    } $cols                 .= '</ul>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view("notification/notify_expense_bill", $data);
        }
    }

}

