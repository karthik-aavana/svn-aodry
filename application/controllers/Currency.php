<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Currency extends MY_Controller
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

     public function index() {
        $currency_module_id = $this->config->item('currency_module');
        $data['currency_module_id'] = $currency_module_id;
        $modules = $this->modules;
        $privilege  = "view_privilege";
        $data['privilege'] = $privilege;
        $data['country']  = $this->country_call();
        $section_modules  = $this->get_section_modules($currency_module_id, $modules, $privilege);

        $data = array_merge($data,$section_modules);

        if (!empty($this->input->post())) {

            $columns = array( 
             			0=> 'action',                    
                        1 => 'country_name',
                        2 => 'currency_name',
                        3 => 'currency_code',
                        4 => 'currency_symbol',                    
                       );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->currencyListField();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;


           
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();

            if (!empty($posts)) {
                foreach ($posts as $post) {

                    $currency_id  = $this->encryption_url->encode($post->currency_id);                    
                    $nestedData['country_name'] = $post->country_name;
                    $nestedData['currency_name'] = $post->currency_name;
                    $nestedData['currency_code'] =  $post->currency_code;
                    $nestedData['currency_symbol'] = '<span style="font-family: DejaVu Sans; sans-serif;">'.$post->currency_symbol.'</span>';

                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_currency"><a data-id="' . $currency_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_currency btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                   
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $currency_id . '" data-path="currency/delete_currency" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                    
                    $cols .= '</div></div>';
					
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]          = $nestedData;
                }
            }

             $json_data = array(
                    "draw" => intval($this->input->post('draw')),
                    "recordsTotal" => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data" => $send_data );
            echo json_encode($json_data);
        } else {
            $this->load->view('currency/list', $data);
        }
    }


     public function addCurrency_modal() {
        
        $currency_module_id  = $this->config->item('currency_module');
        $data['currency_module_id']  = $currency_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($currency_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->form_validation->set_rules('country', 'Country', 'trim|required');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('iso', 'Iso', 'trim|required');
        $this->form_validation->set_rules('symbol', 'Symbol', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {    

            $country_id = trim($this->input->post('country'));
            $currency = trim($this->input->post('currency'));
            $iso = trim($this->input->post('iso'));
            $symbol = trim($this->input->post('symbol'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* Discount duplicate check */
            $data  = $this->general_model->getRecords('count(*) as currency_count', 'currency', array(
                'delete_status' => 0,
                'currency_name' => $currency, 
                'currency_code' => $iso,
                'country_id' => $country_id ));
            $data1  = $this->general_model->getRecords('count(*) as currency_count', 'currency', array(
                'delete_status' => 0, 
                'currency_code' => $iso,
                'country_id' => $country_id ));
            if($data[0]->currency_count == 0 && $data1[0]->currency_count == 0) {               
                $currency_data   = array(
                    "currency_code" => $iso,
                    "country_id"  => $country_id,
                    "currency_name"  => $currency,
                    'currency_symbol' => $symbol,
                    "added_date"     => date('Y-m-d'),
                    "added_user_id"  => $session_user_id );
                if ($id    = $this->general_model->insertData("currency", $currency_data)) {
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'currency',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Currency Inserted' );
                    $this->general_model->insertData('log', $log_data);
                }
                else {
                   $this->session->set_flashdata('fail', 'Currency can not be Inserted.');
                }
            echo json_encode($id);
            }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
                $this->session->set_flashdata('fail', 'Currency is already exit.');
            }
        } 
    }

    public function get_currency_modal($id) {
        $id = $this->encryption_url->decode($id);
        $currency_module_id  = $this->config->item('currency_module');
        $data['currency_module_id']  = $currency_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($currency_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);        

        $data = $this->general_model->getRecords('*', 'currency', array('currency_id' => $id, 'delete_status' => 0 ));
        echo json_encode($data);
    }

    
    public function updateCurrency_modal() {
        $currency_module_id  = $this->config->item('currency_module');
        $data['module_id'] = $currency_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($currency_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post("id");
        $country_id = trim($this->input->post('country'));
        $currency = trim($this->input->post('currency'));
        $iso = trim($this->input->post('iso'));
        $symbol = trim($this->input->post('currency_symbol'));

         $data  = $this->general_model->getRecords('count(*) as currency_count', 'currency', array(
                'delete_status' => 0,
                'currency_name' => $currency, 
                'currency_code' => $iso,
                'country_id' => $country_id,
                'currency_id!='  => $id ));

         if($data[0]->currency_count == 0) {
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            $currency_data   = array(
                     "currency_code" => $iso,
                    "country_id"  => $country_id,
                    "currency_name"  => $currency,
                    "currency_symbol" => $symbol,
                    "updated_date"    => date('Y-m-d'),
                    "updated_user_id" => $session_user_id);
            if ($this->general_model->updateData('currency', $currency_data, array('currency_id' => $id ))) {
                 $log_data = array('user_id'  => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'currency',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Currency Updated' );
                    $this->general_model->insertData('log', $log_data);
                echo json_encode($id);
            }
        }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
        }
    }

    public function delete_currency() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('currency', ["delete_status" => 1 ], array('currency_id' => $id ))){
            redirect("currency", 'refresh');
        }
    }


}