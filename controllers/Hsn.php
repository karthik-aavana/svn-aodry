<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hsn extends MY_Controller
{

    function __construct()
    {
        parent::__construct(); 

        $this->load->model('general_model');        
        $this->modules = $this->get_modules();
        $this->load->helper(array(
                'form',
                'url' ));
        $this->load->library('form_validation');      
    }

    public function index()
    {
        $hsn_module_id                   = $this->config->item('hsn_module');
        $data['hsn_module_id']           = $hsn_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($hsn_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);



        if (!empty($this->input->post()))   {
            $columns             = array(
                0 => 'action',
                1 => 'type',
                2 => 'hsn_code',
                3 => 'description',                
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->hsn_list_field();
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
                    $hsn_id                 = $this->encryption_url->encode($post->hsn_id);
                    $nestedData['type'] = ucwords($post->type);
                    $nestedData['hsn_code'] = $post->hsn_code;
                    $nestedData['description'] = $post->description;
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';                   
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_hsn_modal"><a data-id="' . $hsn_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_hsn btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $hsn_id . '" data-path="hsn/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                    $cols .= '</div></div>';					
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
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
            $this->load->view('hsn/list', $data);
        }
    }



    public function addHsn() {
        
        $hsn_module_id              = $this->config->item('hsn_module');
        $data['module_id']               = $hsn_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($hsn_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $this->form_validation->set_rules('hsnType', 'Type', 'trim|required');
        $this->form_validation->set_rules('hsnCode', 'Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();   
        } else {
            $type = trim($this->input->post('hsnType'));
            $hsnCode = trim($this->input->post('hsnCode'));
            $description = trim($this->input->post('description'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            $data  = $this->general_model->getRecords('count(*) as hsn_count', 'hsn', array('delete_status' => 0,'hsn_code' => $hsnCode ));
            if($data[0]->hsn_count == 0) {
                $hsnData = array(                    
                        "type" => $type,
                        "hsn_code" => $hsnCode,
                        "description" => $description,
                        "added_date"     => date('Y-m-d'),
                        "added_user_id"  => $session_user_id );
                if ($id = $this->general_model->insertData('hsn',$hsnData)){
                    $result['flag'] = true;
                    $result['msg'] = 'HSN Added Successfully';
                    /*$successMsg = 'HSN Added Successfully';
                    $this->session->set_flashdata('hsn_success',$successMsg);*/
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'hsn',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Hsn Inserted' );
                    $this->general_model->insertData('log', $log_data);
                }
                else
                {
                    $result['flag'] = false;
                    $result['msg'] = 'HSN Add Unsuccessful'; 
                    /*$errorMsg = 'HSN Add Unsuccessful';
                    $this->session->set_flashdata('hsn_error',$errorMsg);*/
                    $this->session->set_flashdata('fail', 'HSN can not be Inserted.');
                }
            }else{
                $result['flag'] = false;
                $result['msg'] = 'duplicate'; 
            }
        }
        echo json_encode($result); 
    }

   public function gethsnCode() {
        $hsnId  = $this->input->post('hsnId');
        $hsnCode = $this->input->post('hsnCode');
        $data   = $this->general_model->getRecords('count(*) as num_hsn_code', 'hsn', array(
                'delete_status' => 0,
                'hsn_codes'     => $hsnCode,
                'hsn_id!='      => $hsnId,
                ));
        echo json_encode($data);
    }



public function get_hsn_modal($id) {
        $id                   = $this->encryption_url->decode($id);
        $hsn_module_id              = $this->config->item('hsn_module');
        $data['module_id']               = $hsn_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($hsn_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);        

        $data = $this->general_model->getRecords('*', 'hsn', array('hsn_id' => $id, 'delete_status' => 0 ));
        echo json_encode($data);
    }

    public function update_hsn_modal() {
        $hsn_module_id              = $this->config->item('hsn_module');
        $data['module_id']               = $hsn_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($hsn_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post("id");
        $hsn_code = $this->input->post('hsn_code');
        $session_user_id = $this->session->userdata('SESS_USER_ID');
        $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $data = $this->general_model->getRecords('count(*) as num_hsn', 'hsn', array('delete_status' => 0, 'hsn_code' => $hsn_code, 'hsn_id!='  => $id));
        if($data[0]->num_hsn == 0) { 
             $discount_data   = array(
                    "type"   => $this->input->post('hsnType_edit'),
                    "hsn_code"  => $this->input->post('hsn_code'),
                    "description"  => $this->input->post('description'),
                    "updated_date"    => date('Y-m-d'),
                    "updated_user_id" => $session_user_id );
            $resp = array();
            if ($this->general_model->updateData('hsn', $discount_data, array('hsn_id' => $id ))) {
                $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'hsn',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Hsn Updated' );
                    $this->general_model->insertData('log', $log_data);
                    $resp['flag'] = true;
                    $resp['msg'] = 'HSN Updated Successfully';
            }else{
                $resp['flag'] = false;
                $resp['msg'] = 'HSN Update Unsuccessful'; 
            }
        }else{
            $resp['flag'] = false;
            $resp['msg'] = 'duplicate';
        }
        echo json_encode($resp);
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        $session_user_id = $this->session->userdata('SESS_USER_ID');
        $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        if ($this->general_model->updateData('hsn', ["delete_status" => 1 ], array('hsn_id' => $id ))){
            $successMsg = 'HSN Deleted Successfully';
            $this->session->set_flashdata('hsn_success',$successMsg);
            $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'hsn',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Hsn Deleted' );
                    $this->general_model->insertData('log', $log_data);
        } else{
            $errorMsg = 'HSN Delete Unsuccessful';
            $this->session->set_flashdata('hsn_error',$errorMsg);
        }
        redirect("hsn", 'refresh');
    }

}
