<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Module_settings extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model(['general_model' ]);
        $this->modules = $this->get_modules();
    }

    function index()
    {
        $privilege_module_id         = $this->config->item('privilege_module');
        $data['privilege_module_id'] = $privilege_module_id;
        $modules                     = $this->modules;
        $privilege                   = "view_privilege";
        $data['privilege']           = $privilege;
        $section_modules             = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

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
                    0 => 'module_id',
                    1 => 'settings_invoice_first_prefix',
                    2 => 'settings_invoice_last_prefix',
                    3 => 'invoice_seperation',
                    4 => 'invoice_type',
                    5 => 'invoice_creation',
                    6 => 'invoice_readonly',
                    7 => 'item_access',
                    8 => 'note_split',
                    9 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->module_settings_list_field();
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
                    $settings_id                        = $this->encryption_url->encode($post->settings_id);
                    $nestedData['module_name']          = $post->module_name;
                    $nestedData['invoice_first_prefix'] = $post->settings_invoice_first_prefix;
                    $nestedData['invoice_last_prefix']  = $post->settings_invoice_last_prefix;
                    $nestedData['invoice_seperation']   = $post->invoice_seperation;
                    $nestedData['invoice_type']         = $post->invoice_type;
                    $nestedData['invoice_creation']     = $post->invoice_creation;
                    $nestedData['invoice_readonly']     = $post->invoice_readonly;
                    $nestedData['item_access']          = $post->item_access;
                    $nestedData['note_split']           = $post->note_split;                   
				    $cols = '<div class="box-body hide action_button"><div class="btn-group">';				   
				    $cols.= '<span><a href="' . base_url('module_settings/edit/') . $settings_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $cols.='<span data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $settings_id . '" data-path="module_settings/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"><a  class="btn btn-app" href="#" data-toggle="tooltip" data-placement="bottom" title="Delete" ><i class="fa fa-trash"></i></a></span>';
                    $cols .= '</div></div>';                   
 					$nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]                        = $nestedData;
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
            $this->load->view('module_settings/list', $data);
        }
    }

    public function add()
    {
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            }
            if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            }
            if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            }
            if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }
        }
        $branch_id         = $this->session->userdata('SESS_BRANCH_ID');
        $data['module_id'] = $this->general_model->getActiveRemianingModulesPrivilege($branch_id);
        $this->load->view('module_settings/add', $data);
    }

    public function add_module_settings()
    {
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $gst = $this->input->post('gst');
        if(isset($gst)){
            $gst = $this->input->post('gst');
        }else{
            $gst = 'no';
        }
        $tds = $this->input->post('tds');
        if(isset($tds)){
            $tds = $this->input->post('tds');
        }else{
            $tds = 'no';
        }

        $tcs = $this->input->post('tcs');
        if(isset($tcs)){
            $tcs = $this->input->post('tcs');
        }else{
            $tcs = 'no';
        }

        $module_settings_data = array(
                "module_id"                       => $this->input->post('module_name'),
                "settings_invoice_first_prefix"   => $this->input->post('invoice_first_prefix'),
                "settings_reference_first_prefix" => $this->input->post('reference_first_prefix'),
                "settings_invoice_last_prefix"    => $this->input->post('invoice_last_prefix'),
                "invoice_seperation"              => $this->input->post('invoice_seperation'),
                "invoice_type"                    => $this->input->post('invoice_type'),
                "invoice_creation"                => $this->input->post('invoice_creation'),
                "invoice_readonly"                => $this->input->post('invoice_readonly'),
                "item_access"                     => $this->input->post('item_access'),
                "note_split"                      => $this->input->post('note_split'),
                "tds_visible"                     => $tds,
                "tcs_visible"                     => $tcs,
                "gst_visible"                     => $gst,
                "branch_id"                       => $this->session->userdata('SESS_BRANCH_ID') );
        if ($id = $this->general_model->insertData('settings', $module_settings_data)){
            $table    = "log";
            $log_data = array(
                    'table_id'   => $id,
                    'table_name' => 'module_settings',
                    'branch_id'  => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'    => 'Module Settings Inserted' );
            $this->general_model->insertData($table, $log_data);
        }
        redirect("module_settings", 'refresh');
    }

    public function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            }
            if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            }
            if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            }
            if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }
        }
        $branch_id    = $this->session->userdata('SESS_BRANCH_ID');
        $string       = "s.*,m.module_id,m.module_name";
        $table        = "settings s";
        $where        = array(
                's.branch_id'     => $branch_id,
                's.settings_id'   => $id,
                's.delete_status' => 0 );
        $join         = array(
                'modules m' => "m.module_id=s.module_id" );
        $data['data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        $this->load->view('module_settings/edit', $data);
    }

    public function edit_module_settings()
    {
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $settings_id = $this->input->post('settings_id');
         $gst = $this->input->post('gst');
        if(isset($gst)){
            $gst = $this->input->post('gst');
        }else{
            $gst = 'no';
        }
        $tds = $this->input->post('tds');
        if(isset($tds)){
            $tds = $this->input->post('tds');
        }else{
            $tds = 'no';
        }

        $tcs = $this->input->post('tcs');
        if(isset($tcs)){
            $tcs = $this->input->post('tcs');
        }else{
            $tcs = 'no';
        }
        
        $module_settings_data            = array(
                "module_id"                       => $this->input->post('module_name'),
                "settings_invoice_first_prefix"   => $this->input->post('invoice_first_prefix'),
                "settings_reference_first_prefix" => $this->input->post('reference_first_prefix'),
                "settings_invoice_last_prefix"    => $this->input->post('invoice_last_prefix'),
                "invoice_seperation"              => $this->input->post('invoice_seperation'),
                "invoice_type"                    => $this->input->post('invoice_type'),
                "invoice_creation"                => $this->input->post('invoice_creation'),
                "invoice_readonly"                => $this->input->post('invoice_readonly'),
                "item_access"                     => $this->input->post('item_access'),
                "note_split"                      => $this->input->post('note_split'),
                "tds_visible"                     => $tds,
                "tcs_visible"                     => $tcs,
                "gst_visible"                     => $gst,
                "branch_id"                       => $this->session->userdata('SESS_BRANCH_ID') );
        if ($this->general_model->updateData('settings', $module_settings_data, array(
                        'settings_id'   => $settings_id,
                        'delete_status' => 0 )))
        {
            $table    = "log";
            $log_data = array(
                    'table_id'   => $settings_id,
                    'table_name' => 'module_settings',
                    'branch_id'  => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'    => 'Module Settings Updated' );
            $this->general_model->insertData($table, $log_data);
        } redirect("module_settings", 'refresh');
    }

    public function delete()
    {
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        $table                           = "settings";
        $module_settings_data            = array(
                "delete_status" => 1 );
        $where                           = array(
                "settings_id" => $id );
        if ($this->general_model->updateData($table, $module_settings_data, $where))
        {
            $log_data = array(
                    'table_id'   => $id,
                    'table_name' => 'module_settings',
                    'branch_id'  => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'    => 'Module Settings Deleted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        } redirect("module_settings", 'refresh');
    }

}

