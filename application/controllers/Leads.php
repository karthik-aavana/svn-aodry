<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Leads extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
        $this->load->helper('image_upload_helper');
    }

    public function index()
    {
        $crm_module_id     = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($crm_module_id, $modules, $privilege);

        $lead_module_id    = $this->config->item('lead_module');
        $data['lead_module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0  => 'lead_date',
                    1  => 'asr_no',
                    2  => 'group',
                    3  => 'stages',
                    // 4 => 'product_service',
                    5  => 'source',
                    6  => 'business_type',
                    7  => 'next_action_date',
                    8  => 'priority',
                    9  => 'added_user',
                    10 => 'action'
            );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $asr_no           = $this->input->post('asr_no');
            $customer         = $this->input->post('customer');
            $group            = $this->input->post('group');
            $stages           = $this->input->post('stages');
            $lead_from        = $this->input->post('lead_from');
            $lead_to          = $this->input->post('lead_to');
            $next_action_from = $this->input->post('next_action_from');
            $next_action_to   = $this->input->post('next_action_to');

            $search_data = array(
                    'asr_no'           => $asr_no,
                    'customer'         => $customer,
                    'group'            => $group,
                    'stages'           => $stages,
                    'lead_from'        => $lead_from,
                    'lead_to'          => $lead_to,
                    'next_action_from' => $next_action_from,
                    'next_action_to'   => $next_action_to
            );

            $list_data           = $this->common->leads_list_field($search_data);
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
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $lead_id                 = $this->encryption_url->encode($post->lead_id);
                    $nestedData['lead_date'] = $post->lead_date;
                    $nestedData['asr_no']    = $post->asr_no;
                    $nestedData['customer']  = $post->customer_code . ' - ' . $post->customer_name;

                    // $group_data=$this->general_model->getRecords('lead_group_name','lead_group',array('lead_group_id'=>$post->group,'delete_status'=>0));
                    // $nestedData['group']            = $group_data[0]->lead_group_name;
                    // $stages_data=$this->general_model->getRecords('lead_stages_name','lead_stages',array('lead_stages_id'=>$post->stages,'delete_status'=>0));
                    // $nestedData['stages']           = $stages_data[0]->lead_stages_name;
                    // $nestedData['product_service']  = $post->product_service;
                    // $source_data=$this->general_model->getRecords('lead_source_name','lead_source',array('lead_source_id'=>$post->source,'delete_status'=>0));
                    // $nestedData['source']           = $source_data[0]->lead_source_name;
                    // $business_type_data=$this->general_model->getRecords('lead_business_type','lead_business',array('lead_business_id'=>$post->business_type,'delete_status'=>0));
                    // $nestedData['business_type']    = $business_type_data[0]->lead_business_type;

                    $nestedData['group']         = $post->lead_group_name;
                    $nestedData['stages']        = $post->lead_stages_name;
                    $nestedData['source']        = $post->lead_source_name;
                    $nestedData['business_type'] = $post->lead_business_type;

                    $nestedData['next_action_date'] = $post->next_action_date;
                    $nestedData['priority']         = $post->priority;

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;

                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols = '<a href="' . base_url('leads/edit/') . $lead_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span> Edit Leads</a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="change_stages btn btn-xs btn-warning" data-toggle="modal" data-target="#change_stages" data-id="' . $lead_id . '" href="#" title="Change Stages"><span class="glyphicon glyphicon-edit"></span> Change Stages</a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_delete']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $lead_id . '" data-path="leads/delete" data-delete_message="If you delete this record, the history related to this will also been deleted! Do you want to continue?" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span> Delete Leads</a>';
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            }

            $group_data = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

            $customer_data = $this->customer_call();

            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data,
                    "group_data"      => $group_data,
                    "customer_data"   => $customer_data
            );
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('leads/list', $data);
        }
    }

    public function add()
    {
        $data              = $this->get_default_country_state();
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        // $customer_module_id            = $this->config->item('customer_module');
        // $modules_present               = array('customer_module_id' => $customer_module_id);
        // $data['other_modules_present'] = $this->other_modules_present($modules_present, $modules['modules']);

        $data['customer_module_id'] = $this->config->item('customer_module');

        $data['customer'] = $this->customer_call();

        $access_settings        = $data['access_settings'];
        $primary_id             = "lead_id";
        $table_name             = $this->config->item('leads_table');
        $date_field_name        = "lead_date";
        $current_date           = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $data['group'] = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $data['source'] = $this->general_model->getRecords('lead_source_id,lead_source_name', 'lead_source', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $data['business_type'] = $this->general_model->getRecords('lead_business_id,lead_business_type', 'lead_business', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $data['users'] = $this->general_model->getRecords('id,first_name,last_name', 'users', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));

        $data['inventory_access'] = $inventory_access[0]->inventory_advanced;

        $data['product_inventory_variants'] = $this->get_product_inventory_variants();
        $data['products']                   = $this->get_products();
        $data['services']                   = $this->get_services();

        $this->load->view('leads/add', $data);
    }

    public function add_lead()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $country                         = $this->input->post('country');
        $state                           = $this->input->post('state');

        $attachment = "";
        if (isset($_FILES["attachment"]["name"]) && $_FILES["attachment"]["name"] != "")
        {
            $path_parts = pathinfo($_FILES["attachment"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID') . '/leads'))
            {
                mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID') . '/leads', 0777, TRUE);
            }
            $url = "assets/branch_files/" . $this->session->userdata('SESS_BRANCH_ID') . "/leads/" . $image_path;
            if (in_array($path_parts['extension'], array(
                            "jpg",
                            "jpeg",
                            "png",
                            "pdf",
                            "doc",
                            "docx",
                            "xls",
                            "xlsx" )))
            {
                if (is_uploaded_file($_FILES["attachment"]["tmp_name"]))
                {
                    if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $url))
                    {
                        $attachment = $image_path;
                    }
                }
            }
        }

        $lead_data = array(
                "lead_date"        => $this->input->post('lead_date'),
                "asr_no"           => $this->input->post('asr_no'),
                "party_id"         => $this->input->post('customer'),
                "party_type"       => 'customer',
                "group"            => $this->input->post('group'),
                "stages"           => $this->input->post('stages'),
                // "services"       =>  implode(",", $this->input->post('services')),
                "source"           => $this->input->post('source'),
                "business_type"    => $this->input->post('business_type'),
                "next_action_date" => $this->input->post('next_action_date'),
                "comments"         => $this->input->post('comments'),
                "expected_closing" => $this->input->post('expected_closing'),
                "priority"         => $this->input->post('priority'),
                "evange_list"      => $this->input->post('evange_list'),
                "attachment"       => $attachment,
                "assign_to"        => $this->input->post('assign_to'),
                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                "added_date"       => date('Y-m-d'),
                "added_user_id"    => $this->session->userdata('SESS_USER_ID'),
                "updated_date"     => "",
                "updated_user_id"  => ""
        );

        if ($data['access_settings'][0]->item_access == 'service' || $data['access_settings'][0]->item_access == 'both')
        {
            $lead_data['services'] = implode(",", $this->input->post('services'));
        }

        if ($data['access_settings'][0]->item_access == 'product' || $data['access_settings'][0]->item_access == 'both')
        {
            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status' => 0 ));

            if ($inventory_access[0]->inventory_advanced == 'yes')
            {
                $lead_data['product_inventory_variants'] = implode(",", $this->input->post('products'));
            }
            else
            {
                $lead_data['products'] = implode(",", $this->input->post('products'));
            }
        }

        $table = "leads";
        if ($id    = $this->general_model->insertData($table, $lead_data))
        {
            $table    = "log";
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'leads',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead (Opportunity) Inserted' );
            $this->general_model->insertData($table, $log_data);

            $lead_history_data = array(
                    "group"            => $this->input->post('group'),
                    "stages"           => $this->input->post('stages'),
                    "next_action_date" => $this->input->post('next_action_date'),
                    "comments"         => $this->input->post('comments'),
                    "assign_to"        => $this->input->post('assign_to'),
                    'lead_id'          => $id,
                    "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                    "added_date"       => date('Y-m-d'),
                    "added_user_id"    => $this->session->userdata('SESS_USER_ID'),
                    "updated_date"     => "",
                    "updated_user_id"  => ""
            );
            // $this->general_model->insertData('leads_history', $lead_history_data);
            if ($id                = $this->general_model->insertData('leads_history', $lead_history_data))
            {
                $log_data = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $id,
                        'table_name'        => 'leads_history',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'Lead History Inserted' );
                $table    = "log";
                $this->general_model->insertData($table, $log_data);
            }

            $stages_data = $this->general_model->getRecords('closed_stages_status', 'lead_stages', array(
                    'lead_stages_id' => $this->input->post('stages'),
                    'delete_status'  => 0,
                    'branch_id'      => $this->session->userdata('SESS_BRANCH_ID') ));
            if ($stages_data[0]->closed_stages_status == 1)
            {
                $data1                            = $this->get_default_country_state();
                $customer_module_id               = $this->config->item('customer_module');
                $data1['module_id']               = $customer_module_id;
                $modules                          = $this->modules;
                $privilege                        = "add_privilege";
                $data1['privilege']               = "add_privilege";
                $section_modules                  = $this->get_section_modules($customer_module_id, $modules, $privilege);
                $data1['access_modules']          = $section_modules['modules'];
                $data1['access_sub_modules']      = $section_modules['sub_modules'];
                $data1['access_module_privilege'] = $section_modules['module_privilege'];
                $data1['access_user_privilege']   = $section_modules['user_privilege'];
                $data1['access_settings']         = $section_modules['settings'];
                $data1['access_common_settings']  = $section_modules['common_settings'];

                $access_settings  = $data1['access_settings'];
                $primary_id       = "customer_id";
                $table_name       = "customer";
                $date_field_name  = "added_date";
                $current_date     = date('Y-m-d');
                $reference_number = $this->generate_reference_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

                $customer_data = array(
                        'reference_number' => $reference_number,
                        'reference_type'   => 'leads' );
                $where         = 'customer_id = ' . $this->input->post('customer') . ' and delete_status=0 and branch_id = ' . $this->session->userdata('SESS_BRANCH_ID') . ' and (reference_number is not null and reference_number!="")';
                $this->general_model->updateData('customer', $customer_data, $where);
            }
        }
        redirect("leads", 'refresh');
    }

    public function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['customer'] = $this->customer_call();

        $data['data'] = $this->general_model->getRecords('*', 'leads', array(
                'lead_id'       => $id,
                'delete_status' => 0 ));

        $data['lead_stages'] = $this->general_model->getRecords('closed_stages_status', 'lead_stages', array(
                'lead_stages_id' => $data['data'][0]->stages,
                'delete_status'  => 0,
                'branch_id'      => $this->session->userdata('SESS_BRANCH_ID') ));

        // echo "<pre>";
        // echo $data['data'][0]->stages;
        // print_r($data['lead_stages']);
        // exit;

        $data['group'] = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $data['stages'] = $this->general_model->getRecords('lead_stages_id,lead_stages_name', 'lead_stages', array(
                'lead_group_id' => $data['data'][0]->group,
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $data['source'] = $this->general_model->getRecords('lead_source_id,lead_source_name', 'lead_source', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $data['business_type'] = $this->general_model->getRecords('lead_business_id,lead_business_type', 'lead_business', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));

        $data['inventory_access'] = $inventory_access[0]->inventory_advanced;

        $data['product_inventory_variants'] = $this->get_product_inventory_variants();
        $data['products']                   = $this->get_products();
        $data['services']                   = $this->get_services();

        $this->load->view('leads/edit', $data);
    }

    public function edit_lead()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $country                         = $this->input->post('country');
        $state                           = $this->input->post('state');

        $attachment = "";
        if (isset($_FILES["attachment"]["name"]) && $_FILES["attachment"]["name"] != "")
        {
            $path_parts = pathinfo($_FILES["attachment"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID') . '/leads'))
            {
                mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID') . '/leads', 0777, TRUE);
            }
            $url = "assets/branch_files/" . $this->session->userdata('SESS_BRANCH_ID') . "/leads/" . $image_path;
            if (in_array($path_parts['extension'], array(
                            "jpg",
                            "jpeg",
                            "png",
                            "pdf",
                            "doc",
                            "docx",
                            "xls",
                            "xlsx" )))
            {
                if (is_uploaded_file($_FILES["attachment"]["tmp_name"]))
                {
                    if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $url))
                    {
                        $attachment = $image_path;
                    }
                }
            }
        }
        else
        {
            $attachment = $this->input->post('attachment1');
        }

        $lead_id = $this->input->post('lead_id');

        $lead_data = array(
                "lead_date"        => $this->input->post('lead_date'),
                "asr_no"           => $this->input->post('asr_no'),
                "party_id"         => $this->input->post('customer'),
                "party_type"       => 'customer',
                // "group"             =>  $this->input->post('group'),
                // "stages"            =>  $this->input->post('stages'),
                "source"           => $this->input->post('source'),
                "business_type"    => $this->input->post('business_type'),
                // "next_action_date"  =>  $this->input->post('next_action_date'),
                // "comments"          =>  $this->input->post('comments'),
                "expected_closing" => $this->input->post('expected_closing'),
                "priority"         => $this->input->post('priority'),
                "evange_list"      => $this->input->post('evange_list'),
                "attachment"       => $attachment,
                // "assign_to"         =>  $this->input->post('assign_to'),
                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                "added_date"       => date('Y-m-d'),
                "added_user_id"    => $this->session->userdata('SESS_USER_ID'),
                "updated_date"     => "",
                "updated_user_id"  => ""
        );
        if ($data['access_settings'][0]->item_access == 'service' || $data['access_settings'][0]->item_access == 'both')
        {
            $lead_data['services'] = implode(",", $this->input->post('services'));
        }
        if ($data['access_settings'][0]->item_access == 'product' || $data['access_settings'][0]->item_access == 'both')
        {
            $inventory_access = $this->general_model->getRecords('inventory_advanced', 'common_settings', array(
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'delete_status' => 0 ));

            if ($inventory_access[0]->inventory_advanced == 'yes')
            {
                $lead_data['product_inventory_variants'] = implode(",", $this->input->post('products'));
            }
            else
            {
                $lead_data['products'] = implode(",", $this->input->post('products'));
            }
        }

        $table = "leads";
        if ($this->general_model->updateData($table, $lead_data, array(
                        'lead_id' => $lead_id )))
        {
            $table    = "log";
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $lead_id,
                    'table_name'        => 'leads',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead (Opportunity) Updated' );
            $this->general_model->insertData($table, $log_data);
        }
        redirect("leads", 'refresh');
    }

    public function delete()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        if ($this->general_model->updateData('leads_history', array(
                        "delete_status" => 1 ), array(
                        "lead_id" => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'leads_history(lead_id)',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead History Deleted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        }

        $table = "leads";
        $data  = array(
                "delete_status" => 1 );
        $where = array(
                "lead_id" => $id );
        if ($this->general_model->updateData($table, $data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'leads',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Deleted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('leads');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Lead can not be Deleted.');
            redirect("leads", 'refresh');
        }
    }

    public function get_leads_history()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $lead_id = $this->encryption_url->decode($this->input->post('id'));

        $string             = 'lh.*,u.first_name,u.last_name';
        $table              = 'leads_history lh';
        $join['users u']    = 'lh.added_user_id=u.id';
        $where              = array(
                'lh.branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'lh.lead_id'       => $lead_id,
                'lh.delete_status' => 0,
        );
        $order              = [
                "lh.leads_history_id" => "desc" ];
        $leads_history_data = $this->general_model->getJoinRecords($string, $table, $where, $join, $order);

        $data1['group']     = $leads_history_data[0]->group;
        $data1['stages']    = $leads_history_data[0]->stages;
        $data1['assign_to'] = $leads_history_data[0]->assign_to;

        $history = '';
        $i       = 0;
        foreach ($leads_history_data as $key => $value)
        {
            $history .= "Staff - " . $value->first_name . " " . $value->last_name . " : Changed";
            if (isset($leads_history_data[$i + 1]))
            {
                $group_data = $this->general_model->getRecords('lead_group_name', 'lead_group', array(
                        'lead_group_id' => $leads_history_data[$i + 1]->group,
                        'delete_status' => 0 ));
                $group      = $group_data[0]->lead_group_name;

                $stages_data = $this->general_model->getRecords('lead_stages_name', 'lead_stages', array(
                        'lead_stages_id' => $leads_history_data[$i + 1]->stages,
                        'delete_status'  => 0 ));
                $stages      = $stages_data[0]->lead_stages_name;

                $history .= " From " . $group . "-" . $stages;
            }

            $group_data = $this->general_model->getRecords('lead_group_name', 'lead_group', array(
                    'lead_group_id' => $value->group,
                    'delete_status' => 0 ));
            $group      = $group_data[0]->lead_group_name;

            $stages_data = $this->general_model->getRecords('lead_stages_name', 'lead_stages', array(
                    'lead_stages_id' => $value->stages,
                    'delete_status'  => 0 ));
            $stages      = $stages_data[0]->lead_stages_name;

            $users_data = $this->general_model->getRecords('first_name,last_name', 'users', array(
                    'id'            => $value->assign_to,
                    'delete_status' => 0 ));
            $users      = $users_data[0]->first_name . ' ' . $users_data[0]->last_name;

            $history .= " To " . $group . "-" . $stages . "\n";
            $history .= "Next Action Date : " . $value->next_action_date . ", Assign to : " . $users . "\n";
            $history .= "Comments : " . $value->comments . "\n\n";
            $i++;
        }

        $data1['history']    = $history;
        $data1['group_data'] = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        $data1['users']      = $this->general_model->getRecords('id,first_name,last_name', 'users', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $data1['stages_data'] = $this->general_model->getRecords('lead_stages_id,lead_stages_name', 'lead_stages', array(
                'lead_group_id' => $data1['group'],
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        // $data1['stages_data1'] = $this->general_model->getRecords('closed_stages_status','lead_stages',array('lead_stages_id'=>$data1['stages'],'delete_status'=>0,'branch_id'=>$this->session->userdata('SESS_BRANCH_ID')));

        echo json_encode($data1);
    }

    public function change_stages()
    {
        $lead_id = $this->encryption_url->decode($this->input->post('lead_id'));

        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $group            = $this->input->post('group');
        $stages           = $this->input->post('stages');
        $next_action_date = $this->input->post('next_action_date');
        $comments         = $this->input->post('comments');
        $assign_to        = $this->input->post('assign_to');

        $stages_data = $this->general_model->getRecords('closed_stages_status', 'lead_stages', array(
                'lead_stages_id' => $stages,
                'delete_status'  => 0,
                'branch_id'      => $this->session->userdata('SESS_BRANCH_ID') ));
        if ($stages_data[0]->closed_stages_status == 1)
        {
            $data1                            = $this->get_default_country_state();
            $customer_module_id               = $this->config->item('customer_module');
            $data1['module_id']               = $customer_module_id;
            $modules                          = $this->modules;
            $privilege                        = "add_privilege";
            $data1['privilege']               = "add_privilege";
            $section_modules1                 = $this->get_section_modules($customer_module_id, $modules, $privilege);
            $data1['access_modules']          = $section_modules1['modules'];
            $data1['access_sub_modules']      = $section_modules1['sub_modules'];
            $data1['access_module_privilege'] = $section_modules1['module_privilege'];
            $data1['access_user_privilege']   = $section_modules1['user_privilege'];
            $data1['access_settings']         = $section_modules1['settings'];
            $data1['access_common_settings']  = $section_modules1['common_settings'];

            $lead_data        = $this->general_model->getRecords('party_id', 'leads', array(
                    'lead_id'       => $lead_id,
                    'delete_status' => 0 ));
            $access_settings  = $data1['access_settings'];
            $primary_id       = "customer_id";
            $table_name       = "customer";
            $date_field_name  = "added_date";
            $current_date     = date('Y-m-d');
            $reference_number = $this->generate_reference_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

            $customer_data = array(
                    'reference_number' => $reference_number );
            $where         = 'customer_id = ' . $lead_data[0]->party_id . ' and reference_type = "leads" and delete_status=0 and branch_id = ' . $this->session->userdata('SESS_BRANCH_ID') . ' and (reference_number is null or reference_number = "")';
            $this->general_model->updateData('customer', $customer_data, $where);
        }
        $lead_data = array(
                "group"            => $group,
                "stages"           => $stages,
                "next_action_date" => $next_action_date,
                "comments"         => $comments,
                "assign_to"        => $assign_to,
                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date"     => date('Y-m-d'),
                "updated_user_id"  => $this->session->userdata('SESS_USER_ID')
        );
        $this->general_model->updateData('leads', $lead_data, array(
                'lead_id'       => $lead_id,
                'delete_status' => 0 ));

        $lead_history_data = array(
                "group"            => $group,
                "stages"           => $stages,
                "next_action_date" => $next_action_date,
                "comments"         => $comments,
                "assign_to"        => $assign_to,
                "lead_id"          => $lead_id,
                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                "added_date"       => date('Y-m-d'),
                "added_user_id"    => $this->session->userdata('SESS_USER_ID'),
                "updated_date"     => "",
                "updated_user_id"  => ""
        );

        if ($id = $this->general_model->insertData('leads_history', $lead_history_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'leads_history',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead History Inserted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        }

        echo json_encode('success');
    }

    public function get_group()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $group_data = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        echo json_encode($group_data);
    }

    public function add_group()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $group_name = $this->input->post('group_name');

        $group_data = array(
                'lead_group_name' => $group_name,
                'added_date'      => date('Y-m-d'),
                'added_user_id'   => $this->session->userdata('SESS_USER_ID'),
                'branch_id'       => $this->session->userdata('SESS_BRANCH_ID')
        );
        if ($id         = $this->general_model->insertData('lead_group', $group_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_group',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Group Inserted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        }
        $data['id']   = $id;
        $data['data'] = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        echo json_encode($data);
    }

    public function get_stages()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $group_id    = $this->input->post('group');
        $stages_data = $this->general_model->getRecords('lead_stages_id,lead_stages_name', 'lead_stages', array(
                'lead_group_id' => $group_id,
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        echo json_encode($stages_data);
    }

    public function get_stage()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $stages_id   = $this->input->post('stages');
        $stages_data = $this->general_model->getRecords('closed_stages_status', 'lead_stages', array(
                'lead_stages_id' => $stages_id,
                'delete_status'  => 0,
                'branch_id'      => $this->session->userdata('SESS_BRANCH_ID') ));

        echo json_encode($stages_data);
    }

    public function add_stages()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $stages_name = $this->input->post('stages_name');
        $group_id    = $this->input->post('group_name1');

        $stages_data = array(
                'lead_stages_name' => $stages_name,
                'lead_group_id'    => $group_id,
                'added_date'       => date('Y-m-d'),
                'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID')
        );
        if ($id          = $this->general_model->insertData('lead_stages', $stages_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_stages',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Stages Inserted'
            );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        }
        $data['id']   = $id;
        $data['data'] = $this->general_model->getRecords('lead_stages_id,lead_stages_name', 'lead_stages', array(
                'lead_group_id' => $group_id,
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        echo json_encode($data);
    }

    public function add_source()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $source_name = $this->input->post('source_name');

        $source_data = array(
                'lead_source_name' => $source_name,
                'added_date'       => date('Y-m-d'),
                'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID')
        );
        if ($id          = $this->general_model->insertData('lead_source', $source_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_source',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Source Inserted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        }
        $data['id']   = $id;
        $data['data'] = $this->general_model->getRecords('lead_source_id,lead_source_name', 'lead_source', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        echo json_encode($data);
    }

    public function add_business_type()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $business_type = $this->input->post('business_type');

        $business_data = array(
                'lead_business_type' => $business_type,
                'added_date'         => date('Y-m-d'),
                'added_user_id'      => $this->session->userdata('SESS_USER_ID'),
                'branch_id'          => $this->session->userdata('SESS_BRANCH_ID')
        );
        if ($id            = $this->general_model->insertData('lead_business', $business_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_business',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Business Type Inserted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        }
        $data['id']   = $id;
        $data['data'] = $this->general_model->getRecords('lead_business_id,lead_business_type', 'lead_business', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        echo json_encode($data);
    }

    public function list_group()
    {
        $crm_module_id     = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($crm_module_id, $modules, $privilege);

        $lead_module_id         = $this->config->item('lead_module');
        $data['lead_module_id'] = $lead_module_id;
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;
        $section_modules        = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0 => 'added_date',
                    1 => 'lead_group_name',
                    2 => 'added_user',
                    3 => 'action'
            );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data           = $this->common->leads_group_list_field();
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
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $lead_group_id                 = $this->encryption_url->encode($post->lead_group_id);
                    $nestedData['added_date']      = $post->added_date;
                    $nestedData['lead_group_name'] = $post->lead_group_name;
                    $nestedData['added_user']      = $post->first_name . ' ' . $post->last_name;

                    $cols = '';
                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="edit_group btn btn-xs btn-warning" data-toggle="modal" data-target="#group_modal" data-id="' . $lead_group_id . '" data-name="' . $post->lead_group_name . '" href="#" title="Edit Group"><span class="glyphicon glyphicon-edit"></span></a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_delete']))
                    {
                        $lead_id = $this->general_model->getRecords('*', 'leads', array(
                                'group'         => $post->lead_group_id,
                                'delete_status' => 0,
                                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

                        $lead_stages_id = $this->general_model->getRecords('*', 'lead_stages', array(
                                'lead_group_id' => $post->lead_group_id,
                                'delete_status' => 0,
                                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

                        if ($lead_id || $lead_stages_id)
                        {
                            $cols .= ' <a data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        else
                        {
                            $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $lead_group_id . '" data-path="leads/delete_group" data-delete_message="" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('leads/list_group', $data);
        }
    }

    public function edit_group()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $group_id   = $this->input->post('group_id');
        $group_id   = $this->encryption_url->decode($group_id);
        $group_name = $this->input->post('group_name');

        $group_data = array(
                'lead_group_name' => $group_name,
                'updated_date'    => date('Y-m-d'),
                'updated_user_id' => $this->session->userdata('SESS_USER_ID'),
                'branch_id'       => $this->session->userdata('SESS_BRANCH_ID')
        );
        $where      = array(
                'lead_group_id' => $group_id,
                'delete_status' => 0 );
        if ($this->general_model->updateData('lead_group', $group_data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $group_id,
                    'table_name'        => 'lead_group',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Group Updated' );
            $table    = "log";
            $id       = $this->general_model->insertData($table, $log_data);
        }

        echo json_encode($id);
    }

    public function delete_group()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        // if($this->general_model->updateData('lead_stages', array("delete_status" => 1), array("lead_group_id" => $id)))
        // {
        //     $log_data = array(
        //                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
        //                    'table_id'          => $id,
        //                    'table_name'        => 'leads_stages(lead_group_id)',
        //                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
        //                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
        //                    'message'           => 'Lead Stages Deleted');
        //     $table    = "log";
        //     $this->general_model->insertData($table, $log_data);
        // }

        $table = "lead_group";
        $data  = array(
                "delete_status" => 1 );
        $where = array(
                "lead_group_id" => $id );
        if ($this->general_model->updateData($table, $data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_group',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Group Deleted'
            );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('leads/list_group');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Lead Group can not be Deleted.');
            redirect("leads/list_group", 'refresh');
        }
    }

    public function list_stages()
    {
        $crm_module_id     = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($crm_module_id, $modules, $privilege);

        $lead_module_id    = $this->config->item('lead_module');
        $data['lead_module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0 => 'added_date',
                    1 => 'lead_stages_name',
                    2 => 'lead_group_name',
                    3 => 'added_user',
                    4 => 'action'
            );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data           = $this->common->leads_stages_list_field();
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
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $lead_stages_id                 = $this->encryption_url->encode($post->lead_stages_id);
                    $nestedData['added_date']       = $post->added_date;
                    $nestedData['lead_stages_name'] = $post->lead_stages_name;
                    if ($post->closed_stages_status == 1)
                    {
                        $nestedData['lead_stages_name'] .= ' <span class="label label-success">Closed Stage</span>';
                    }
                    $nestedData['lead_group_name'] = $post->lead_group_name;
                    $nestedData['added_user']      = $post->first_name . ' ' . $post->last_name;

                    $cols = '';
                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="edit_stages btn btn-xs btn-warning" data-toggle="modal" data-target="#stages_modal" data-id="' . $lead_stages_id . '" data-group_id="' . $post->lead_group_id . '" data-name="' . $post->lead_stages_name . '" href="#" title="Edit stages"><span class="glyphicon glyphicon-edit"></span></a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_delete']))
                    {
                        $lead_id = $this->general_model->getRecords('*', 'leads', array(
                                'stages'        => $post->lead_stages_id,
                                'delete_status' => 0,
                                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

                        if ($lead_id)
                        {
                            $cols .= ' <a data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        else
                        {
                            $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $lead_stages_id . '" data-path="leads/delete_stages" data-delete_message="" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('leads/list_stages', $data);
        }
    }

    public function edit_stages()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $stages_id   = $this->input->post('stages_id');
        $stages_id   = $this->encryption_url->decode($stages_id);
        $stages_name = $this->input->post('stages_name');

        $stages_data = array(
                'lead_stages_name' => $stages_name,
                'updated_date'     => date('Y-m-d'),
                'updated_user_id'  => $this->session->userdata('SESS_USER_ID'),
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID')
        );
        $where       = array(
                'lead_stages_id' => $stages_id,
                'delete_status'  => 0 );
        if ($this->general_model->updateData('lead_stages', $stages_data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $stages_id,
                    'table_name'        => 'lead_stages',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Stages Updated' );
            $table    = "log";
            $id       = $this->general_model->insertData($table, $log_data);
        }

        echo json_encode($id);
    }

    public function delete_stages()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "lead_stages";
        $data  = array(
                "delete_status" => 1 );
        $where = array(
                "lead_stages_id" => $id );
        if ($this->general_model->updateData($table, $data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_stages',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Stages Deleted'
            );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('leads/list_stages');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Lead Stages can not be Deleted.');
            redirect("leads/list_stages", 'refresh');
        }
    }

    public function list_source()
    {
        $crm_module_id     = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($crm_module_id, $modules, $privilege);

        $lead_module_id    = $this->config->item('lead_module');
        $data['lead_module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0 => 'added_date',
                    1 => 'lead_source_name',
                    2 => 'added_user',
                    3 => 'action'
            );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data           = $this->common->leads_source_list_field();
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
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $lead_source_id                 = $this->encryption_url->encode($post->lead_source_id);
                    $nestedData['added_date']       = $post->added_date;
                    $nestedData['lead_source_name'] = $post->lead_source_name;
                    $nestedData['added_user']       = $post->first_name . ' ' . $post->last_name;

                    $cols = '';
                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="edit_source btn btn-xs btn-warning" data-toggle="modal" data-target="#source_modal" data-id="' . $lead_source_id . '" data-name="' . $post->lead_source_name . '" href="#" title="Edit Source"><span class="glyphicon glyphicon-edit"></span></a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_delete']))
                    {
                        $lead_id = $this->general_model->getRecords('*', 'leads', array(
                                'source'        => $post->lead_source_id,
                                'delete_status' => 0,
                                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

                        if ($lead_id)
                        {
                            $cols .= ' <a data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        else
                        {
                            $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $lead_source_id . '" data-path="leads/delete_source" data-delete_message="" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('leads/list_source', $data);
        }
    }

    public function edit_source()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $source_id   = $this->input->post('source_id');
        $source_id   = $this->encryption_url->decode($source_id);
        $source_name = $this->input->post('source_name');

        $source_data = array(
                'lead_source_name' => $source_name,
                'updated_date'     => date('Y-m-d'),
                'updated_user_id'  => $this->session->userdata('SESS_USER_ID'),
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID')
        );
        $where       = array(
                'lead_source_id' => $source_id,
                'delete_status'  => 0 );
        if ($this->general_model->updateData('lead_source', $source_data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $source_id,
                    'table_name'        => 'lead_source',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Source Updated' );
            $table    = "log";
            $id       = $this->general_model->insertData($table, $log_data);
        }

        echo json_encode($id);
    }

    public function delete_source()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);


        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "lead_source";
        $data  = array(
                "delete_status" => 1 );
        $where = array(
                "lead_source_id" => $id );
        if ($this->general_model->updateData($table, $data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_source',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Source Deleted'
            );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('leads/list_source');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Lead Source can not be Deleted.');
            redirect("leads/list_source", 'refresh');
        }
    }

    public function list_business_type()
    {
        $crm_module_id     = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($crm_module_id, $modules, $privilege);

        $lead_module_id    = $this->config->item('lead_module');
        $data['lead_module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0 => 'added_date',
                    1 => 'lead_business_type',
                    2 => 'added_user',
                    3 => 'action'
            );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data           = $this->common->leads_business_type_list_field();
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
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $lead_business_id                 = $this->encryption_url->encode($post->lead_business_id);
                    $nestedData['added_date']         = $post->added_date;
                    $nestedData['lead_business_type'] = $post->lead_business_type;
                    $nestedData['added_user']         = $post->first_name . ' ' . $post->last_name;

                    $cols = '';
                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="edit_business_type btn btn-xs btn-warning" data-toggle="modal" data-target="#business_type_modal" data-id="' . $lead_business_id . '" data-name="' . $post->lead_business_type . '" href="#" title="Edit Business Type"><span class="glyphicon glyphicon-edit"></span></a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_delete']))
                    {
                        $lead_id = $this->general_model->getRecords('*', 'leads', array(
                                'business_type' => $post->lead_business_id,
                                'delete_status' => 0,
                                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

                        if ($lead_id)
                        {
                            $cols .= ' <a data-toggle="modal" data-target="#false_delete_modal" title="Delete" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        else
                        {
                            $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $lead_business_id . '" data-path="leads/delete_business_type" data-delete_message="" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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
            $this->load->view('leads/list_business_type', $data);
        }
    }

    public function edit_business_type()
    {
        $lead_module_id    = $this->config->item('lead_module');
        $data['module_id'] = $lead_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($lead_module_id, $modules, $privilege);

        $business_id   = $this->input->post('business_id');
        $business_id   = $this->encryption_url->decode($business_id);
        $business_type = $this->input->post('business_type');

        $business_type_data = array(
                'lead_business_type' => $business_type,
                'updated_date'       => date('Y-m-d'),
                'updated_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'branch_id'          => $this->session->userdata('SESS_BRANCH_ID')
        );
        $where              = array(
                'lead_business_id' => $business_id,
                'delete_status'    => 0 );
        if ($this->general_model->updateData('lead_business', $business_type_data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $business_id,
                    'table_name'        => 'lead_business',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Business Type Updated' );
            $table    = "log";
            $id       = $this->general_model->insertData($table, $log_data);
        }

        echo json_encode($id);
    }

    public function delete_business_type()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "lead_business";
        $data  = array(
                "delete_status" => 1 );
        $where = array(
                "lead_business_id" => $id );
        if ($this->general_model->updateData($table, $data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'lead_business',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Lead Business Type Deleted'
            );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('leads/list_business_type');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Lead Business Type can not be Deleted.');
            redirect("leads/list_business_type", 'refresh');
        }
    }

    public function closed_stages()
    {
        $lead_module_id                  = $this->config->item('lead_module');
        $data['module_id']               = $lead_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($lead_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $closed_stages_name = $this->input->post('closed_stages_name');
        $group              = $this->input->post('group');

        $where = array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') );
        $this->general_model->updateData('lead_stages', array(
                'closed_stages_status' => 0 ), $where);

        $where = array(
                "lead_stages_id" => $closed_stages_name,
                "delete_status"  => 0,
                "branch_id"      => $this->session->userdata('SESS_BRANCH_ID') );
        $this->general_model->updateData('lead_stages', array(
                "closed_stages_status" => 1 ), $where);

        echo json_encode('success');
    }

    public function todays_target()
    {
        $crm_module_id     = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($crm_module_id, $modules, $privilege);
        $lead_module_id         = $this->config->item('lead_module');
        $data['lead_module_id'] = $lead_module_id;
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;
        $section_modules        = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0  => 'lead_date',
                    1  => 'asr_no',
                    2  => 'group',
                    3  => 'stages',
                    // 4 => 'product_service',
                    5  => 'source',
                    6  => 'business_type',
                    7  => 'next_action_date',
                    8  => 'priority',
                    9  => 'added_user',
                    10 => 'action'
            );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];
            
            $list_data           = $this->common->todays_leads_list_field();
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
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $lead_id                 = $this->encryption_url->encode($post->lead_id);
                    $nestedData['lead_date'] = $post->lead_date;
                    $nestedData['asr_no']    = $post->asr_no;
                    $nestedData['customer']  = $post->customer_code . ' - ' . $post->customer_name;
                    $nestedData['group']            = $post->lead_group_name;
                    $nestedData['stages']           = $post->lead_stages_name;
                    $nestedData['source']           = $post->lead_source_name;
                    $nestedData['business_type']    = $post->lead_business_type;
                    $nestedData['next_action_date'] = $post->next_action_date;
                    $nestedData['priority']         = $post->priority;

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;

                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols = '<a href="' . base_url('leads/edit/') . $lead_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span> Edit Leads</a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="change_stages btn btn-xs btn-warning" data-toggle="modal" data-target="#change_stages" data-id="' . $lead_id . '" href="#" title="Change Stages"><span class="glyphicon glyphicon-edit"></span> Change Stages</a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_delete']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $lead_id . '" data-path="leads/delete" data-delete_message="If you delete this record, the history related to this will also been deleted! Do you want to continue?" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span> Delete Leads</a>';
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            }

            $group_data = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

            $customer_data = $this->customer_call();

            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data,
                    "group_data"      => $group_data,
                    "customer_data"   => $customer_data
            );
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('leads/todays_target', $data);
        }
    }

    public function missed_targets()
    {
        $crm_module_id     = $this->config->item('crm_module');
        $data['module_id'] = $crm_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($crm_module_id, $modules, $privilege);
        $lead_module_id         = $this->config->item('lead_module');
        $data['lead_module_id'] = $lead_module_id;
        $modules                = $this->modules;
        $privilege              = "view_privilege";
        $data['privilege']      = $privilege;
        $section_modules        = $this->get_section_modules($lead_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns = array(
                    0  => 'lead_date',
                    1  => 'asr_no',
                    2  => 'group',
                    3  => 'stages',
                    // 4 => 'product_service',
                    5  => 'source',
                    6  => 'business_type',
                    7  => 'next_action_date',
                    8  => 'priority',
                    9  => 'added_user',
                    10 => 'action'
            );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];
            
            $list_data           = $this->common->missed_leads_list_field();
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
            $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $lead_id                 = $this->encryption_url->encode($post->lead_id);
                    $nestedData['lead_date'] = $post->lead_date;
                    $nestedData['asr_no']    = $post->asr_no;
                    $nestedData['customer']  = $post->customer_code . ' - ' . $post->customer_name;
                    $nestedData['group']            = $post->lead_group_name;
                    $nestedData['stages']           = $post->lead_stages_name;
                    $nestedData['source']           = $post->lead_source_name;
                    $nestedData['business_type']    = $post->lead_business_type;
                    $nestedData['next_action_date'] = $post->next_action_date;
                    $nestedData['priority']         = $post->priority;
                    $nestedData['added_user']       = $post->first_name . ' ' . $post->last_name;

                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols = '<a href="' . base_url('leads/edit/') . $lead_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span> Edit Leads</a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_edit']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="change_stages btn btn-xs btn-warning" data-toggle="modal" data-target="#change_stages" data-id="' . $lead_id . '" href="#" title="Change Stages"><span class="glyphicon glyphicon-edit"></span> Change Stages</a>';
                    }

                    if (in_array($data['lead_module_id'], $data['active_delete']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $lead_id . '" data-path="leads/delete" data-delete_message="If you delete this record, the history related to this will also been deleted! Do you want to continue?" href="#" title="Delete"><span class="glyphicon glyphicon-trash"></span> Delete Leads</a>';
                    }

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            }

            $group_data = $this->general_model->getRecords('lead_group_id,lead_group_name', 'lead_group', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

            $customer_data = $this->customer_call();

            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data,
                    "group_data"      => $group_data,
                    "customer_data"   => $customer_data
            );
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('leads/missed_targets', $data);
        }
    }
}
