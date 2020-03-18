<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Uqc extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $uqc_module_id                   = $this->config->item('uqc_module');
        $data['uqc_module_id']           = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'action',
                    1 => 'uom',
                    2 => 'uom_type',
                    2 => 'description',
                    3 => 'addded_user');
					
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->uqc_list_field();
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
                    $uqc_id                    = $this->encryption_url->encode($post->id);
                    $nestedData['uom']         = $post->uom;
                    $nestedData['uom_type']    = $post->uom_type;
                    $nestedData['description'] = $post->description;
                    $nestedData['added_user']  = $post->first_name . ' ' . $post->last_name;
                   $cols = '<div class="box-body hide action_button"><div class="btn-group">';
				   $cols.= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_uom_modal"><a data-id="' . $uqc_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_uom btn btn-app"><i class="fa fa-pencil"></i></a></span>';
				   $cols.= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $uqc_id . '" data-path="uqc/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';                  
				   $cols .= '</div></div>';					
                   $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';				
					
                   $send_data[]               = $nestedData;
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
            $this->load->view('uqc/list', $data);
        }
    }

    public function add()
    {
        $uqc_module_id                   = $this->config->item('uqc_module');
        $data['module_id']               = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view("uqc/add", $data);
    }

    public function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $uqc_module_id                   = $this->config->item('uqc_module');
        $data['module_id']               = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['data'] = $this->general_model->getRecords("*", "uqc", [
                "delete_status" => 0,
                "id"            => $id ]);
        $this->load->view("uqc/edit", $data);
    }

    public function add_uqc()
    {
        $uqc_module_id                   = $this->config->item('uqc_module');
        $data['module_id']               = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $uqc_data                        = array(
                "uom"           => $this->input->post("uom"),
                "description"   => $this->input->post("description"),
                "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                "added_date"    => date('Y-m-d') );
        if ($id = $this->general_model->insertData("uqc", $uqc_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'uqc',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'UOM Inserted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
            redirect("uqc", 'refresh');
        }
    }
    public function add_uom_ajax()
    {
        $uqc_module_id  = $this->config->item('uqc_module');
        $data['module_id'] = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $uom = trim($this->input->post('uom'));
        $data=array_merge($data,$section_modules);
        $data  = $this->general_model->getRecords('count(*) as uqc_count,id', 'uqc', array(
            'delete_status' => 0,
            'uom' => $uom));
        $uom_value                       = trim($this->input->post('tax_value'));
        $id = $data[0]->id;
        if($data[0]->uqc_count == 0){
            $uqc_data                        = array(
                    "uom"        => trim($this->input->post('uom')),
                    "description"       => trim($this->input->post('description')),
                    "uom_type" => trim($this->input->post('uom_type')),
                    "added_date"      => date('Y-m-d'),
                    "added_user_id"   => $this->session->userdata("SESS_USER_ID"));
            $id                              = $this->general_model->insertData("uqc", $uqc_data);
            $log_data                        = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id,
                    'table_name'        => 'uqc',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Uom Inserted' );
        }
        //$data['data']                    = $this->general_model->getRecords($tax['string'], $tax['table'], $tax['where']);
        $type = trim($this->input->post('uom_type'));
        $data['data']              = $this->uqc_product_service_call($type);
        $data['id']                      = $id;
        $data['uom_value']               = $type;
        echo json_encode($data);
    }

    public function edit_uqc()
    {
        $uqc_module_id                   = $this->config->item('uqc_module');
        $data['module_id']               = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post("id");
        $update                          = array(
                "uom"             => $this->input->post("uom"),
                "description"     => $this->input->post("description"),
                "updated_user_id" => $this->session->userdata('SESS_USER_ID'),
                "updated_date"    => date('Y-m-d') );
        if ($id1 = $this->general_model->updateData('uqc', $update, array(
                        'id' => $id )))
        {
             $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id1,
                    'table_name'        => 'uqc',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'UOM Updated' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
            redirect("uqc", 'refresh');
        }
    }

    public function delete()
    {
        $uqc_module_id                   = $this->config->item('uqc_module');
        $data['module_id']               = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($id1 = $this->general_model->updateData('uqc', ["delete_status" => 1 ], array('id' => $id ))) {
            $successMsg = 'UOM Deleted Successfully';
            $this->session->set_flashdata('uom_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id1,
                    'table_name'        => 'uqc',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'UOM Deleted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
            redirect("uqc", 'refresh');
        }
        else{
            $errorMsg = 'UOM Delete Unsuccessful';
            $this->session->set_flashdata('uom_error',$errorMsg);
            redirect("uqc", 'refresh');
        }
    }

    /* POP UP Modal UQC added Written by Karthikeyan on 24th June 2019 */
    public function add_uqc_modal() {
        $uqc_module_id  = $this->config->item('uqc_module');
        $data['module_id'] = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* form validation check */
        $this->form_validation->set_rules('uom', 'Unique Quantity Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $uom = trim($this->input->post('uom'));
            $description = trim($this->input->post('description'));
            $uom_type = trim($this->input->post('uom_type'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* UQC duplicate check */

            $data  = $this->general_model->getRecords('count(*) as uqc_count', 'uqc', array(
                'delete_status' => 0,
                'uom' => $uom));
            $data_type  = $this->general_model->getRecords('uom_type', 'uqc', array(
                'delete_status' => 0,
                'uom' => $uom));
            $uom_type = '';
            if($data_type){
                $uom_type = $data_type[0]->uom_type;
            }
            $data_desc  = $this->general_model->getRecords('count(*) as uqc_count', 'uqc', array(
                'delete_status' => 0,
                'description' => $uom));
            $result = array();
            if($data[0]->uqc_count == 0 && $data_desc[0]->uqc_count == 0) {
               
                $uqc_data  = array(
                "uom"           => $this->input->post("uom"),
                "description"   => $this->input->post("description"),
                "uom_type"      => $this->input->post("uom_type"),
                "added_user_id" => $session_user_id,
                "added_date"    => date('Y-m-d'));
                if ($id    = $this->general_model->insertData("uqc", $uqc_data)) {
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'uqc',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'UOM Inserted' );
                    $this->general_model->insertData('log', $log_data);
                    $result['flag'] = true;
                    $result['msg'] = 'UOM Added Successfully';
                }
                else {
                    $result['flag'] = false;
                    $result['msg'] = 'UOM Add Unsuccessful';
                   $this->session->set_flashdata('fail', 'UQC can not be Inserted.');
                }
            }else{
                $type_check = trim($this->input->post("uom_type"));
                if(($data[0]->uqc_count > 0 && $uom_type == 'both') || ($uom_type == $type_check && $data[0]->uqc_count > 0)){
                    $result['resl'] = 'duplicate' ;
                    $result['type'] = $uom_type ;
                }else if($uom_type != $type_check){
                    $this->general_model->updateData('uqc', ["uom_type" => 'both' ], array(
                    'delete_status' => 0,
                    'uom' => $uom));
                    $result['flag'] = true;
                    $result['msg'] = 'UOM Added Successfully';
                }else{
                    $result['resl'] = 'duplicate_desc' ;
                }
            }
        }
        echo json_encode($result);
    }

    public function duplicate_check_uqc_modal() {
        $uqc_module_id  = $this->config->item('uqc_module');
        $data['module_id'] = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);

        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $uom = trim($this->input->post('uom'));
        $uom_type = trim($this->input->post('uom_type'));
        $data = array();
        $data  = $this->general_model->getRecords('count(*) as uqc_count', 'uqc', array(
                'delete_status' => 0,
                'uom' => $uom));
        $data_type  = $this->general_model->getRecords('uom_type', 'uqc', array(
                'delete_status' => 0,
                'uom' => $uom));
        $result = array();
        $result['uom_type'] = '';
        if($data_type){
            $result['uom_type'] = $data_type[0]->uom_type;
        }
        $result['uom_count'] = $data[0]->uqc_count;
        if($result['uom_type'] != $uom_type){
            $this->general_model->updateData('uqc', ["uom_type" => 'both' ], array(
            'delete_status' => 0,
            'uom' => $uom));
        }



        /*if($data[0]->uqc_count > 0){
             $result = 'duplicate' ;
        }*/
        echo json_encode($result);

    }
    public function get_uqc_modal($id) {
        $id                   = $this->encryption_url->decode($id);
       $uqc_module_id  = $this->config->item('uqc_module');
        $data['module_id'] = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);     

        $data = $this->general_model->getRecords('*', 'uqc', array(
                'id'   => $id,
                'delete_status' => 0 ));

        echo json_encode($data);
    }

    public function update_uqc_modal() {
        $uqc_module_id                   = $this->config->item('uqc_module');
        $data['module_id']               = $uqc_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($uqc_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post("id");
        $uom = $this->input->post('uom');
        $uom_type = $this->input->post('uom_type');
        
        $data = $this->general_model->getRecords('count(*) as num_uom', 'uqc', array(
                'delete_status'  => 0,
                'uom' => $uom,
                'id!='  => $id ));
        $data_type  = $this->general_model->getRecords('uom_type', 'uqc', array(
                'delete_status' => 0,
                'uom' => $uom));
            $uom_type = '';
        if($data_type){
            $uom_type = $data_type[0]->uom_type;
        }
        $data_desc = $this->general_model->getRecords('count(*) as num_uom', 'uqc', array(
                'delete_status'  => 0,
                'description' => $uom,
                'id!='  => $id ));
        if($data[0]->num_uom == 0 && $data_desc[0]->num_uom == 0) {
                $uom_data   = array(
                            "uom"  => $this->input->post("uom"),
                            "description"   => $this->input->post("description"),
                            "uom_type" => $this->input->post("uom_type"),
                            "updated_date"    => date('Y-m-d'),
                            "updated_user_id" => $this->session->userdata('SESS_USER_ID') );
                $result = array();
            if ($id1 = $this->general_model->updateData('uqc', $uom_data, array('id' => $id ))) {
                $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => $id1,
                    'table_name'        => 'uqc',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'UOM Updated' );
                    $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
                $result['flag'] = true;
                $result['msg'] = 'UOM Updated Successfully';
            }else{
                $result['flag'] = false;
                $result['msg'] = 'UOM Update Unsuccessful';
                $this->session->set_flashdata('fail', 'UQC can not be Updated.');
            }
        }else{
            $type_check = trim($this->input->post("uom_type"));
            if(($data[0]->num_uom > 0 && $uom_type == 'both') || ($data[0]->num_uom > 0 && $uom_type == $type_check)){
                $result['resl'] = 'duplicate' ;
                $result['type'] = $uom_type ;
            }else if($uom_type != $type_check){
                $this->general_model->updateData('uqc', ["uom_type" => 'both' ], array(
                'delete_status' => 0,
                'uom' => $uom));
                $result['flag'] = true;
                $result['msg'] = 'UOM Added Successfully';
            }else{
                $result['resl'] = 'duplicate_desc' ;
            }
        }
        echo json_encode($result);
    }

}
