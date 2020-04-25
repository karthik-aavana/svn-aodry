<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tax extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index(){
        $tax_module_id = $this->config->item('tax_module');
        $data['tax_module_id'] = $tax_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($tax_module_id, $modules, $privilege);
        $data['tax_section'] = $this->tax_section_call();
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(  
            		0 => 'action' ,               
                    1 => 'tax_name',
                    2 => 'tax_value',
                    3 => 'tax_description'
                     );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->tax_list_field();
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
                    $tax_id                        = $this->encryption_url->encode($post->tax_id);
                    if($post->tax_name == 'TDS' || $post->tax_name == 'TCS'){
                        if($post->section_name != ''){
                            $nestedData['tax_name']        = $post->tax_name.'(Sec '.$post->section_name.') @ '.round($post->tax_value,2).'%';
                        }else{
                            $nestedData['tax_name']        = $post->tax_name.' @ '.round($post->tax_value,2).'%';
                        }
                    }else{
                        $nestedData['tax_name']        = $post->tax_name.' @ '.round($post->tax_value,2).'%';
                    }
                    
                    $nestedData['tax_value']       = round($post->tax_value,2).'%';
                    $nestedData['tax_description'] = str_replace(array(
                            "\r\n",
                            "\\r\\n" ), "<br>", $post->tax_description);                   
                     $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';
                    if(in_array($data['tax_module_id'], $data['active_edit'])){
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_tax"><a data-id="' . $tax_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit Tax" class="edit_tax btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }
                    $product_id                    = $this->general_model->getRecords('*', 'products', array(
                            'product_tax_id' => $post->tax_id,
                            'delete_status'  => 0,
                            'branch_id'      => $this->session->userdata('SESS_BRANCH_ID') ));
                    $service_id                    = $this->general_model->getRecords('*', 'services', array(
                            'service_tax_id' => $post->tax_id,
                            'delete_status'  => 0,
                            'branch_id'      => $this->session->userdata('SESS_BRANCH_ID') ));
                    if(in_array($data['tax_module_id'], $data['active_delete'])){
                        if ($product_id || $service_id)
                        {
                            $cols .= '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal" title="Delete Tax" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        }
                        else
                        {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $tax_id . '" data-path="tax/delete" data-toggle="tooltip" data-placement="bottom" title="Delete Tax"> <i class="fa fa-trash-o"></i> </a></span>';
                        } 
                    }
                    $cols .= '</div></div>';
                    $disabled = '';
                    if(!in_array($data['tax_module_id'], $data['active_delete']) && !in_array($data['tax_module_id'], $data['active_edit'])){
                        $disabled = 'disabled';
                    }
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal"'.$disabled.'>';
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
            $this->load->view('tax/list', $data);
        }
    }

    public function add(){
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view('tax/add', $data);
    }

    public function add_tax()
    {
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $tax_data                        = array(
                "tax_name"        => trim($this->input->post('tax_name')),
                "tax_value"       => trim($this->input->post('tax_value')),
                "tax_description" => trim($this->input->post('description')),
                "added_date"      => date('Y-m-d'),
                "section_id" => $this->input->post('cmb_section'),
                "added_user_id"   => $this->session->userdata("SESS_USER_ID"),
                "branch_id"       => $this->session->userdata("SESS_BRANCH_ID") );
        $resp = array();
        if ($id = $this->general_model->insertData("tax", $tax_data))
        {
            $resp['flag'] = true;
            $resp['msg'] = 'Tax Added Successfully';
            /*$successMsg = 'Tax Added Successfully';
            $this->session->set_flashdata('tax_success',$successMsg);*/
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'tax',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Tax Inserted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
        }else{
            $resp['flag'] = false;
            $resp['msg'] = 'Tax Add Unsuccessful';
            /*$errorMsg = 'Tax Add Unsuccessful';
            $this->session->set_flashdata('tax_error',$errorMsg);*/
        }
        echo json_encode($resp);
    }

    public function edit($id){
        $id = $this->encryption_url->decode($id);
       // var_dump($id);
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
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
        } $data['data'] = $this->general_model->getRecords('*', 'tax', array(
                'tax_id'        => $id,
                'delete_status' => 0 ));
        $this->load->view('tax/edit', $data);
    }

    public function edit_tax()
    {
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('id');
        $tax_data                        = array(
                "tax_name"        => trim($this->input->post('tax_name')),
                "tax_value"       => trim($this->input->post('tax_value')),
                "tax_description" => trim($this->input->post('description')),
                "added_date"      => date('Y-m-d'),
                "added_user_id"   => $this->session->userdata("SESS_USER_ID"),
                "branch_id"       => $this->session->userdata("SESS_BRANCH_ID") );
        if ($id1 = $this->general_model->updateData("tax", $tax_data, array(
                        'tax_id'        => $id,
                        'delete_status' => 0 )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id1,
                    'table_name'        => 'tax',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Tax Updated' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
        } 
        redirect("tax", 'refresh');
    }

    public function add_tax_ajax()
    {
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $tax                             = $this->common->tax_field();
        $tax_value                       = trim($this->input->post('tax_value'));
        $tax_data                        = array(
                "tax_name"        => trim($this->input->post('tax_name')),
                "tax_value"       => trim($this->input->post('tax_value')),
                "tax_description" => trim($this->input->post('description')),
                "section_id" => $this->input->post('section_id'),
                "added_date"      => date('Y-m-d'),
                "added_user_id"   => $this->session->userdata("SESS_USER_ID"),
                "branch_id"       => $this->session->userdata("SESS_BRANCH_ID") );
        if($id = $this->general_model->insertData("tax", $tax_data)){
            $log_data = array(
                'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                'table_id'          => $id,
                'table_name'        => 'tax',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                'message'           => 'Tax Inserted' );
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
        //$data['data']                    = $this->general_model->getRecords($tax['string'], $tax['table'], $tax['where']);
        $type = trim($this->input->post('tax_name'));
        $data['data']              = $this->tax_call_type($type);
        $data['id']                      = $id;
        $data['tax_value']               = $tax_value;
        echo json_encode($data);
    }

    public function get_tax_ajax()
    {
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('tax');
        $data['data']                    = $this->general_model->getRecords('*', 'tax', array(
                'tax_id'        => $id,
                'delete_status' => 0 ));
        echo json_encode($data);
    }

    public function delete()
    {
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id  = $this->input->post('delete_id');
        $id  = $this->encryption_url->decode($id);
        if ($id1 = $this->general_model->updateData('tax', [
                        "delete_status" => 1 ], array(
                        'tax_id' => $id )))
        {
            $successMsg = 'Tax Deleted Successfully';
            $this->session->set_flashdata('tax_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                'table_id'          => $id1,
                'table_name'        => 'tax',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                'message'           => 'Tax Deleted' );
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }else{
            $errorMsg = 'Tax Delete Unsuccessful';
            $this->session->set_flashdata('tax_error',$errorMsg);
        }
        redirect("tax", 'refresh');
    }

    public function get_tax()
    {
        $tax_id    = $this->input->post('tax_id');
        //$tax_id = $this->encryption_url->decode($tax_id);
        $tax_value = $this->input->post('tax_value');
        $tax_name = $this->input->post('tax_name');
        $section_id = $this->input->post('section_id');  
        if($tax_name == 'CESS' || $tax_name == 'GST'){      
        $data = $this->general_model->getRecords('count(*) as num_tax_value', 'tax', array(
                'delete_status' => 0,
                'tax_value'     => $tax_value,
                'tax_name'     => $tax_name,
                'tax_id!='      => $tax_id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        }else{
            $data = $this->general_model->getRecords('count(*) as num_tax_value', 'tax', array(
                'delete_status' => 0,
                'tax_value'     => $tax_value,
                'tax_name'     => $tax_name,
                'section_id'   => $section_id,
                'tax_id!='     => $tax_id,
                'branch_id'    => $this->session->userdata('SESS_BRANCH_ID') ));
        }

        echo json_encode($data);
    }


    public function get_tax_modal($id){
        $id                              = $this->encryption_url->decode($id);
       // var_dump($id);
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        
        $data = $this->general_model->getRecords('*', 'tax', array(
                'tax_id'        => $id,
                'delete_status' => 0 ));
        echo json_encode($data);
    }

    public function edit_tax_modal()
    {
        $tax_module_id                   = $this->config->item('tax_module');
        $data['module_id']               = $tax_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($tax_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('tax_id');
        $tax_data                        = array(
                "tax_name"        => trim($this->input->post('tax_name_e')),
                "tax_value"       => trim($this->input->post('tax_value_e')),
                "tax_description" => trim($this->input->post('description_e')),
                "section_id" => $this->input->post('cmb_section_e'),
                "added_date"      => date('Y-m-d'),
                "added_user_id"   => $this->session->userdata("SESS_USER_ID"),
                "branch_id"       => $this->session->userdata("SESS_BRANCH_ID") );
        $resp = array();  
        if ($this->general_model->updateData("tax", $tax_data, array(
                        'tax_id'        => $id,
                        'delete_status' => 0 )))
        {
            $resp['flag'] = true;
            $resp['msg'] = 'Tax Updated Successfully';
            /*$successMsg = 'Tax Updated Successfully';
            $this->session->set_flashdata('tax_success',$successMsg);*/
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'tax',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Tax Updated' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
        }else{
            $resp['flag'] = false;
            $resp['msg'] = 'Tax Update Unsuccessful';
            /*$errorMsg = 'Tax Update Unsuccessful';
            $this->session->set_flashdata('tax_error',$errorMsg);
             redirect("tax", 'refresh');*/
        } 
        echo json_encode($resp);
    }

    public function get_tax_perctage(){
        $id   = $this->input->post('tax_id');

        $data = $this->general_model->getRecords('*', 'tax', array(
                'tax_id'        => $id,
                'delete_status' => 0 ));
        echo json_encode($data);
    }

}
