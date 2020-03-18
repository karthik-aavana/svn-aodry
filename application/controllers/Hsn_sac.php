<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hsn_sac extends MY_Controller
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
               0 => 'type',
                1 => 'hsn_code',
                2 => 'description',
                3 => 'action',
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
                   $nestedData['type'] = $post->type;
                    $nestedData['hsn_code'] = $post->hsn_code;
                    $nestedData['description'] = $post->description;

                    $cols = '';
                    
                    $cols .= '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_category_modal" data-id="' . $hsn_id . '" title="Edit" class="edit_category btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a> | ';
                   
                    $cols .= '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $hsn_id . '" data-path="category/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                        
                    }
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
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



public function add()
    {
        

        // $hsn_module_id   = $this->config->item('hsn_module');
         //$data['module_id']    = $hsn_module_id;
        //$modules              = $this->modules;
        $privilege            = "add_privilege";
        $data['privilege']    = "add_privilege";
        //$section_modules      = $this->get_section_modules($country_module_id, $modules, $privilege);
        
        /* presents all the needed */
        //$data=array_merge($data,$section_modules);

        $this->load->view('hsn_sac/list', $data);
    }



     public function addHsn()
    {
        

        $this->form_validation->set_rules('hsnType', 'Type', 'trim|required');
        $this->form_validation->set_rules('hsnCode', 'Code', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->add();
        	
        }
        else
        {
        	
            
            $hsnData = array(                    
                    "hsn_type" => trim($this->input->post('hsnType')),
                    "itc_hs_codes" => trim($this->input->post('hsnCode')),
                    "description" => trim($this->input->post('description'))
                );


            if ($id            = $this->general_model->insertData('hsn',$hsnData))
            {


                /*$log_data = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $id,
                        'table_name'        => 'hsn',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'Hsn Inserted' );
                $this->general_model->insertData('log', $log_data);*/
            }
            else
            {
                $this->session->set_flashdata('fail', 'HSN can not be Inserted.');
            }
        } echo json_encode($id);
    }

   public function gethsnCode()
    {
        $hsnId    = $this->input->post('hsnId');
        $hsnCode = $this->input->post('hsnCode');
        $data      = $this->general_model->getRecords('count(*) as num_hsn_code', 'hsn', array(
                'delete_status' => 0,
                'itc_hs_codes'     => $hsnCode,
                'hsn_id!='      => $hsnId,
                ));
        echo json_encode($data);
    }







}
