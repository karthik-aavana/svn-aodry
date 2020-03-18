<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Location extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('general_model');        
        $this->modules = $this->get_modules();
        $this->load->helper(array('form','url' ));
        $this->load->library('form_validation'); 
	}

	/*********Funtion for list country ********/
	public function country() {

		$location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
               0 => 'action',
               1 => 'country_name',
            );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->country_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
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
                    $country_id = $this->encryption_url->encode($post->country_id);
                    $nestedData['country_name'] = $post->country_name;
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_country"><a data-id="' . $country_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_country btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $country_id . '" data-path="location/delete_country" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
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
        } else {
            $this -> load -> view('location/country_list', $data);
        }
	}

	public function addCountry_modal() {
        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* form validation check */
        $this->form_validation->set_rules('country', 'Country Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $country = trim($this->input->post('country'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* Discount duplicate check */
            $data  = $this->general_model->getRecords('count(*) as country_count', 'countries', array(
                'delete_status' => 0,
                'country_name' => $country ));
            if($data[0]->country_count == 0) {               
                $country_data   = array(
                "country_name"  => $country,
                "added_date"     => date('Y-m-d'),
                "added_user_id"  => $session_user_id );
                if ($id    = $this->general_model->insertData("countries", $country_data)) {
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'countries',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Country Inserted' );
                    $this->general_model->insertData('log', $log_data);
                }
                else {
                   $this->session->set_flashdata('fail', 'Country can not be Inserted.');
                }
            echo json_encode($id);
            }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
                $this->session->set_flashdata('fail', 'Country is already exit.');
            }
        }
    }

    public function delete_country() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('countries', ["delete_status" => 1 ], array('country_id' => $id ))){
            redirect("location/country", 'refresh');
        }
    }
	
     public function get_country_modal($id) {
        $id = $this->encryption_url->decode($id);
        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);        

        $data = $this->general_model->getRecords('*', 'countries', array('country_id' => $id, 'delete_status' => 0 ));
        echo json_encode($data);
    }


    public function update_country_modal() {
        $location_module_id  = $this->config->item('location_module');
        $data['module_id']               = $location_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post("id");
        $country_name = $this->input->post('country_name');
        $data  = $this->general_model->getRecords('count(*) as num_country', 'countries', array(
                'delete_status'  => 0,
                'country_name' => $country_name,
                'country_id!='  => $id ));
         if($data[0]->num_country == 0) {

            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
             $countries_data   = array(
                    "country_name"   => $country_name,
                    "updated_date"    => date('Y-m-d'),
                    "updated_user_id" => $session_user_id);
            if ($this->general_model->updateData('countries', $countries_data, array('country_id' => $id ))) {
                 $log_data = array('user_id'  => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'countries',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'Country Updated' );
                    $this->general_model->insertData('log', $log_data);
                echo json_encode($id);
            }
        }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
        }
    }

    /*********Funtion for list State ********/
    public function state() {

        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($location_module_id, $modules, $privilege);
        $data['country']  = $this->country_call();
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
               0 => 'action',
               1 => 'country_name',
               2 => 'state_name',
               
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->state_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
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
                    $state_id = $this->encryption_url->encode($post->state_id);
                    $nestedData['country_name'] = $post->country_name;
                    $nestedData['state_name'] = $post->state_name;
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_state"><a data-id="' . $state_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_state btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $state_id . '" data-path="location/delete_state" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
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
        } else {
            $this->load->view('location/state_list', $data);
        }
    }

    public function addState_modal() {
        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* form validation check */
        $this->form_validation->set_rules('country', 'Country Name', 'trim|required');
        $this->form_validation->set_rules('state', 'State Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $country_id = trim($this->input->post('country'));
            $state = trim($this->input->post('state'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* Discount duplicate check */
            $data  = $this->general_model->getRecords('count(*) as state_count', 'states', array(
                'delete_status' => 0,
                'state_name' => $state, 
                'country_id' => $country_id ));
            if($data[0]->state_count == 0) {               
                $country_data   = array(
                "country_id"  => $country_id,
                "state_name"  => $state,
                "added_date"     => date('Y-m-d'),
                "added_user_id"  => $session_user_id );
                if ($id    = $this->general_model->insertData("states", $country_data)) {
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'states',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'State Inserted' );
                    $this->general_model->insertData('log', $log_data);
                }
                else {
                   $this->session->set_flashdata('fail', 'State can not be Inserted.');
                }
            echo json_encode($id);
            }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
                $this->session->set_flashdata('fail', 'State is already exit.');
            }
        }
    }

    public function get_state_modal($id) {
        $id = $this->encryption_url->decode($id);
        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);        

        $data = $this->general_model->getRecords('*', 'states', array('state_id' => $id, 'delete_status' => 0 ));
        echo json_encode($data);
    }

    
    public function update_state_modal() {
        $location_module_id  = $this->config->item('location_module');
        $data['module_id'] = $location_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post("id");
        $country_id = trim($this->input->post('country'));
        $state = trim($this->input->post('state'));
        $data  = $this->general_model->getRecords('count(*) as num_state', 'states', array(
                'delete_status'  => 0,
                'state_name' => $state, 
                'country_id' => $country_id,
                'state_id!='  => $id ));
         if($data[0]->num_state == 0) {
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            $countries_data   = array(
                    'state_name' => $state, 
                    'country_id' => $country_id,
                    "updated_date"    => date('Y-m-d'),
                    "updated_user_id" => $session_user_id);
            if ($this->general_model->updateData('states', $countries_data, array('state_id' => $id ))) {
                 $log_data = array('user_id'  => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'states',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'State Updated' );
                    $this->general_model->insertData('log', $log_data);
                echo json_encode($id);
            }
        }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
        }
    }

    public function delete_state() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('states', ["delete_status" => 1 ], array('state_id' => $id ))){
            redirect("location/state", 'refresh');
        }
    }


    /*********Funtion for list City ********/
    public function city() {

        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($location_module_id, $modules, $privilege);
        $data['country']  = $this->country_call();       
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
               0 => 'action',
               1 => 'country_name',
               2 => 'state_name',
               3 => 'city_name'               
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->city_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
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
                    $city_id = $this->encryption_url->encode($post->city_id);
                    $nestedData['country_name'] = $post->country_name;
                    $nestedData['state_name'] = $post->state_name;
                    $nestedData['city_name'] = $post->city_name;
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_city"><a data-id="' . $city_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_city btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $city_id . '" data-path="location/delete_city" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
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
        } else {
            $this->load->view('location/city_list', $data);
        }
    }

    public function addCity_modal() {
        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* form validation check */
        $this->form_validation->set_rules('country', 'Country Name', 'trim|required');
        $this->form_validation->set_rules('state', 'State Name', 'trim|required');
         $this->form_validation->set_rules('city', 'City Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->add();
        } else {
            $country_id = trim($this->input->post('country'));
            $state_id = trim($this->input->post('state'));
            $city = trim($this->input->post('city'));
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

            /* Discount duplicate check */
            $data  = $this->general_model->getRecords('count(*) as city_count', 'cities', array(
                'delete_status' => 0,
                'city_name' => $city, 
                'state_id' => $state_id ));
            if($data[0]->city_count == 0) {               
                $country_data   = array(
                "state_id"  => $state_id,
                "city_name"  => $city,
                "added_date"     => date('Y-m-d'),
                "added_user_id"  => $session_user_id );
                if ($id    = $this->general_model->insertData("cities", $country_data)) {
                    $log_data = array(
                            'user_id'           => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'cities',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'State Inserted' );
                    $this->general_model->insertData('log', $log_data);
                }
                else {
                   $this->session->set_flashdata('fail', 'City can not be Inserted.');
                }
            echo json_encode($id);
            }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
                $this->session->set_flashdata('fail', 'City is already exit.');
            }
        }
    }

    public function get_city_modal($id) {
        $id = $this->encryption_url->decode($id);
        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);        

        $data = $this->general_model->getJoinRecords('cities.*,s.state_id,c.country_id', 'cities', array('city_id' => $id, 'cities.delete_status' => 0 ),  array('states s' => 's.state_id=cities.state_id', 'countries c' => 'c.country_id=s.country_id' ));
        echo json_encode($data);
    }

    
    public function update_city_modal() {
        $location_module_id  = $this->config->item('location_module');
        $data['module_id'] = $location_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post("id");
        $state_id = trim($this->input->post('state'));
        $city = trim($this->input->post('city'));
        $data  = $this->general_model->getRecords('count(*) as num_state', 'cities', array(
                'delete_status'  => 0,
                'city_name' => $city, 
                'state_id' => $state_id,
                'city_id!='  => $id ));
         if($data[0]->num_state == 0) {
            $session_user_id = $this->session->userdata('SESS_USER_ID');
            $session_branch_id = $this->session->userdata('SESS_BRANCH_ID');
            $session_finacial_year_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
            $countries_data   = array(
                    'city_name' => $city, 
                    'state_id' => $state_id,
                    "updated_date"    => date('Y-m-d'),
                    "updated_user_id" => $session_user_id);
            if ($this->general_model->updateData('cities', $countries_data, array('city_id' => $id ))) {
                 $log_data = array('user_id'  => $session_user_id,
                            'table_id'          => $id,
                            'table_name'        => 'cities',
                            'financial_year_id' => $session_finacial_year_id,
                            "branch_id"         => $session_branch_id,
                            'message'           => 'City Updated' );
                    $this->general_model->insertData('log', $log_data);
                echo json_encode($id);
            }
        }else{
                $result = 'duplicate' ; 
                echo json_encode($result);
        }
    }

    public function delete_city() {
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('cities', ["delete_status" => 1 ], array('city_id' => $id ))){
            redirect("location/city", 'refresh');
        }
    }


}
