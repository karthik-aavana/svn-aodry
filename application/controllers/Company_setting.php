<?php

defined('BASEPATH') OR exit('NO direct script access allowed');

class Company_setting extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
        $this->load->helper('image_upload_helper');
    }

    public function edit_old() {
       
        $location_module_id  = $this->config->item('location_module');
        $data['location_module_id']  = $location_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($location_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
         $branch_data                            = $this->get_default_country_state();
        $firm_id = $branch_data['branch'][0]->firm_id;

        if (!empty($this->input->post())) {
            $columns = array(
               0 => 'branch_code',
               1 => 'branch_name',
               2 => 'branch_address',
               3 => 'action',
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->branch_list_field($firm_id);
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
                    $branch_id = $this->encryption_url->encode($post->branch_id);
                    $nestedData['branch_code'] = $post->branch_code;
                    $nestedData['branch_name'] = $post->branch_name;
                    $nestedData['branch_address'] = $post->branch_address;

                    $cols = '';
                    
                    $cols .= '<a href="' . base_url('company_setting/edit/') . $branch_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a> | ';
                   
                    $cols .= '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $branch_id . '" data-path="company_setting/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
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
        } else {
            $this -> load -> view('company_setting/list', $data);
        }
    }

    public function index() {
        $data                            = $this->get_default_country_state_old();
        /*$data['currency']                = $this->general_model->getRecords('*', 'currency', array(
                'delete_status' => 0 ));*/
        $data['currency'] = $this->currency_call();
        $data['financial_year']          = $this->financial_year_call();
        $user_module_id                  = $this->config->item('user_module');
        $data['module_id']               = $user_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($user_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['data'] = $data['branch'];
        /*echo "<pre>";
        print_r($data['data'] );exit;*/
        // echo ($data['data'][0]->affliation_images);die;
        $this->load->view('company_setting/edit_old', $data);
    }

    public function update()
    {
        if (isset($_FILES["affliete_images"]["name"]) && $_FILES["affliete_images"]["name"] != "")
        {

            if (!is_dir('assets/affiliate/' . $this->session->userdata('SESS_BRANCH_ID')))
            {
                mkdir('./assets/affiliate/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            }
            $url        = "assets/affiliate/" . $this->session->userdata('SESS_BRANCH_ID');
            $image_json = upload_multiple_image('affliete_images', $url);
        }

        $this->general_model->Updatedata('common_settings', [
                'affliation_images' => $image_json ], [
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') ]);

        // print_r($this->input->post('affliete_images'));die;

        $fyp                             = $this->input->post('financial_year_password');
        $efyd                            = $this->encryption->encrypt($fyp);
        $user_module_id                  = $this->config->item('user_module');
        $data['module_id']               = $user_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($user_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('id');
        if (isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"] != "")
        {
            $path_parts = pathinfo($_FILES["logo"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID')))
            {
                mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            } $url = "assets/branch_files/" . $this->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
            
            if (in_array($path_parts['extension'], array(
                            "jpg",
                            "jpeg",
                            "png","PNG" )))
            {
                if (is_uploaded_file($_FILES["logo"]["tmp_name"]))
                {
                    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $url))
                    {
                        $image_name = $image_path;
                    }
                }
            }
        }
        else
        {
            $image_name = $this->input->post('hidden_logo_name');
        }
        if (isset($_FILES["import_export_code"]["name"]) && $_FILES["import_export_code"]["name"] != "")
        {
            $path_parts = pathinfo($_FILES["import_export_code"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID')))
            {
                mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            } $url = "assets/branch_files/" . $this->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
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
                if (is_uploaded_file($_FILES["import_export_code"]["tmp_name"]))
                {
                    if (move_uploaded_file($_FILES["import_export_code"]["tmp_name"], $url))
                    {
                        $iec_name = $image_path;
                    }
                }
            }
        }
        else
        {
            $iec_name = $this->input->post('hidden_iec_name');
        }
        if (isset($_FILES["shop_establishment"]["name"]) && $_FILES["shop_establishment"]["name"] != "")
        {
            $path_parts = pathinfo($_FILES["shop_establishment"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID')))
            {
                mkdir('./assets/branch_files/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            }
            $url = "assets/branch_files/" . $this->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
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
                if (is_uploaded_file($_FILES["shop_establishment"]["tmp_name"]))
                {
                    if (move_uploaded_file($_FILES["shop_establishment"]["tmp_name"], $url))
                    {
                        $shop_name = $image_path;
                    }
                }
            }
        }
        else
        {
            $shop_name = $this->input->post('hidden_shop_name');
        } 
        $firm_id   = $this->input->post('firm_id');
        $branch_id = $this->input->post('branch_id');
        $firm_data = array(
                "firm_name"            => $this->input->post('name'),
                "is_updated" => 1,
                "firm_short_name"      => $this->input->post('short_name'),
                "firm_registered_type" => $this->input->post('registered_type'),
                "firm_logo"            => $image_name,
                "firm_company_code"    => $this->input->post('company_code') 
            );
        if ($this->general_model->updateData('firm', $firm_data, array(
                        'firm_id' => $firm_id ))) {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $firm_id,
                    'table_name'        => 'firm',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Firm Updated' );
            $this->general_model->insertData('log', $log_data);
        } $branch_data = array(
                "firm_id"                          => $firm_id,
                "branch_name"                      => $this->input->post('name'),
                "branch_gstin_number"              => $this->input->post('branch_gstin_number'),
                "branch_gst_registration_type"     => $this->input->post('branch_gst_registration_type'),
                "branch_code"                      => $this->input->post('branch_code'),
                "branch_address"                   => $this->input->post('branch_address'),
                "branch_country_id"                => $this->input->post('country'),
                "branch_state_id"                  => $this->input->post('state'),
                "branch_city_id"                   => $this->input->post('city'),
                "branch_postal_code"               => $this->input->post('branch_postal_code'),
                "branch_email_address"             => $this->input->post('email'),
                "branch_mobile"                    => $this->input->post('mobile'),
                "branch_land_number"               => $this->input->post('land_number'),
                "branch_pan_number"                => $this->input->post('pan_number'),
                "branch_cin_number"                => $this->input->post('cin_number'),
                "branch_roc"                       => $this->input->post('branch_roc'),
                "branch_esi"                       => $this->input->post('branch_esi'),
                "branch_pf"                        => $this->input->post('branch_pf'),
                "branch_tan_number"                => $this->input->post('tan_number'),
                "branch_import_export_code"        => $iec_name,
                "branch_import_export_code_number" => $this->input->post('import_export_code_number'),
                "branch_lut_number"                => $this->input->post('lut_number'),
                "branch_shop_establishment"        => $shop_name,
                "branch_others"                    => $this->input->post('others'),
                "updated_date"                     => date('Y-m-d'),
                "updated_user_id"                  => $this->session->userdata('SESS_USER_ID'),
                "branch_default_currency"          => $this->input->post('currency_id'),
                 );//"financial_year_id"                => $this->input->post('financial_year_id')

        if($this->input->post('drug_no_1')){
            $branch_data['drug_licence_no_1'] = $this->input->post('drug_no_1');
        }

        if($this->input->post('drug_no_2')){
            $branch_data['drug_licence_no_2'] = $this->input->post('drug_no_2');
        }
       /* $this->session->set_userdata('SESS_FINANCIAL_YEAR_ID', $this->input->post('financial_year_id'));*/
        /*$this->session->set_userdata('SESS_FINANCIAL_YEAR_TITLE', $this->input->post('financial_year_title'));*/

        if ($this->general_model->updateData('branch', $branch_data, array(
                        'branch_id' => $branch_id )))
        {
            $this->session->set_userdata('SESS_DETAILS_UPDATED',1);
            $tax_split_percentage = 50;
                if($this->input->post('tax_split_percentage') != '') 
                    if($this->input->post('tax_split_percentage') <= 100 ) 
                    $tax_split_percentage = $this->input->post('tax_split_percentage');

            $common_settings_data = array(
                    "tax_split_percentage" => $tax_split_percentage,
                    "round_off_access"          => $this->input->post('round_off_access'),
                    "tax_split_equaly"          => $this->input->post('tax_split_equaly'),
                    "financial_year_password"   => $efyd,
                    'default_notification_date' => $this->input->post('default_notification_date'),
                    "invoice_footer"            => $this->input->post('invoice_footer') );
            $this->general_model->updateData('common_settings', $common_settings_data, array(
                    'branch_id' => $branch_id ));
            $log_data             = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $branch_id,
                    'table_name'        => 'branch',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Branch Updated' );
            /*$this->session->set_userdata('SESS_FINANCIAL_YEAR_ID', $this->input->post('financial_year_id'));*/
            $this->session->set_userdata('SESS_DEFAULT_CURRENCY', $this->input->post('currency_id'));
            $this->general_model->insertData('log', $log_data);
        } redirect('company_setting', 'refresh');
    }

    public function remove_logo(){
        $firm    = $this->general_model->getJoinRecords('f.firm_id', 'firm f', array(
                'branch_id'       => $this->session->userdata('SESS_BRANCH_ID'),
                'f.delete_status' => 0 ), array(
                'branch b' => 'b.firm_id=f.firm_id' ));
        $firm_id = $firm[0]->firm_id;
        $this->general_model->updateData('firm', array(
                'firm_logo' => '' ), array(
                'firm_id'       => $firm_id,
                'delete_status' => 0 ));
        redirect('company_setting', 'refresh');
    }

    public function remove_iec()
    {
        $this->general_model->updateData('branch', array(
                'branch_import_export_code' => '' ), array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));
        redirect('company_setting', 'refresh');
    }

    public function remove_shop(){
        
        $this->general_model->updateData('branch', array(
                'branch_shop_establishment' => '' ), array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));
        redirect('company_setting', 'refresh');
    }

    public function add(){
        
        $data                            = $this->get_default_country_state();
       /* $data['currency']                = $this->general_model->getRecords('*', 'currency', array(
                'delete_status' => 0 ));*/
        $data['currency'] = $this->currency_call();
        $data['financial_year']          = $this->financial_year_call();
        $company_setting_module_id       = $this->config->item('company_setting_module');
        $data['module_id']               = $company_setting_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($company_setting_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $access_settings        = $data['access_settings'];
        $branch = $data['branch'];
        $company_id = $branch[0]->firm_id;
        $primary_id             = "branch_id";
        $table_name             = "branch";
        $date_field_name        = "added_date";
        $current_date           = date('Y-m-d');
        $data['invoice_number'] = $this->generate_branch_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date,'',$company_id);
         
        $data['data'] = $data['branch'];
        $this->load->view('company_setting/add', $data);      
    }

    public function add_branch(){      
        $firm_id = $this->input->post('firm_id');
        $company_code = $this->input->post('company_code');
        $company_name = $this->input->post('company_name');
        $address = $this->input->post('address');
        $cmb_currency = $this->input->post('cmb_currency');
        $gst_number = $this->input->post('gst_number');

        $gst = $this->input->post('d_gst');
        if(isset($gst)){
            $gst = $this->input->post('d_gst');
        }else{
            $gst = 0;
        }
        $tds = $this->input->post('tds');
        if(isset($tds)){
            $tds = $this->input->post('tds');
        }else{
            $tds = 0;
        }

        $cess = $this->input->post('cess');
        if(isset($cess)){
            $cess = $this->input->post('cess');
        }else{
            $cess = 0;
        }

        $cmb_country = $this->input->post('cmb_country');
        $cmb_state = $this->input->post('cmb_state');
        $cmb_city = $this->input->post('cmb_city');
        $cgst = $this->input->post('cgst');
        $sgst = $this->input->post('sgst');
        $igst = $this->input->post('igst');
        $ugst = $this->input->post('ugst');
        $user_id = $this->session->userdata('SESS_USER_ID');
        $financial_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

         $branch_data = array("firm_id" => $firm_id,
                                "branch_name" => $company_name,
                                "branch_gstin_number" => $gst_number,
                                "branch_code" => $company_code,
                                "branch_address" => $address,
                                "branch_country_id" => $cmb_country,
                                "branch_state_id" => $cmb_state,
                                "branch_city_id" => $cmb_city,
                                "added_date" => date('Y-m-d'),
                                "added_user_id" => $user_id,
                                "branch_default_currency" => $cmb_currency,
                                "financial_year_id" => $financial_id,
                                "gst" => $gst,
                                "tds" => $tds,
                                "cess" => $cess,
                                "cgst_percentage" => $cgst,
                                "sgst_percentage" => $sgst,
                                "igst_percentage" => $igst,
                                "ugst_percentage" => $ugst
                            );
         $branch_id = $this->general_model->insertData('branch',$branch_data);
            

         if (isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"] != "") {
            $path_parts = pathinfo($_FILES["logo"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/branch_files/' . $branch_id))
            {
                mkdir('./assets/branch_files/' . $branch_id, 0777, TRUE);
            } $url = "assets/branch_files/" . $branch_id . "/" . $image_path;
            if (in_array($path_parts['extension'], array(
                            "jpg",
                            "jpeg",
                            "png" )))
            {
                if (is_uploaded_file($_FILES["logo"]["tmp_name"]))
                {
                    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $url))
                    {
                        $image_name = $image_path;
                    }
                }
            }
            $logo_data =   array("branch_logo" => $image_name);
            $this->general_model->updateData('branch', $logo_data, array('branch_id' => $branch_id ));
        }

        if(isset($_POST['custom_field'])){
            $data = array();
            $custom_field = $_POST['custom_field'];
            $custom_lablel = $_POST['custom_lablel'];
            $length = count($_POST['custom_field']);
           
            for ($i = 0; $i < $length; $i++) {
                $data[$i]['value'] = $custom_field[$i];
                $data[$i]['column_name'] =  $custom_lablel[$i];
                $data[$i]['branch_id'] =  $branch_id;
                $data[$i]['added_user_id'] =  $user_id;
                $data[$i]['added_date'] =  date('Y-m-d');
                        
            }

            $this->db->insert_batch('branch_additional_info', $data);
        }

        redirect('company_setting', 'refresh');
        

    }

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $data                            = $this->get_default_branch_data($id);
        /*$data['currency']                = $this->general_model->getRecords('*', 'currency', array(
                'delete_status' => 0 ));*/
        $data['currency'] = $this->currency_call();
        $data['financial_year']          = $this->financial_year_call();
        $user_module_id                  = $this->config->item('user_module');
        $data['module_id']               = $user_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($user_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);   

        $data['additional_info'] = $additional_info = $this->general_model->getRecords('*', 'branch_additional_info', array(
                'branch_id' => $id ));  
         $data['additional_info_count'] = count($additional_info);
                

        $data['data'] = $data['branch'];

        // echo ($data['data'][0]->affliation_images);die;
        $this->load->view('company_setting/edit', $data);
    }

    public function update_branch(){      
        $branch_id = $this->input->post('branch_id');
        $company_code = $this->input->post('company_code');
        $company_name = $this->input->post('company_name');
        $address = $this->input->post('address');
        $cmb_currency = $this->input->post('cmb_currency');
        $gst_number = $this->input->post('gst_number');
        $gst = $this->input->post('d_gst');
        if(isset($gst)){
            $gst = $this->input->post('d_gst');
        }else{
            $gst = 0;
        }
        $tds = $this->input->post('tds');
        if(isset($tds)){
            $tds = $this->input->post('tds');
        }else{
            $tds = 0;
        }
        $cess = $this->input->post('cess');
        if(isset($cess)){
            $cess = $this->input->post('cess');
        }else{
            $cess = 0;
        }
        
        $cmb_country = $this->input->post('cmb_country');
        $cmb_state = $this->input->post('cmb_state');
        $cmb_city = $this->input->post('cmb_city');
        $cgst = $this->input->post('cgst');
        $sgst = $this->input->post('sgst');
        $igst = $this->input->post('igst');
        $ugst = $this->input->post('ugst');
        $user_id = $this->session->userdata('SESS_USER_ID');
        $financial_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');

         $branch_data = array("branch_name" => $company_name,
                                "branch_gstin_number" => $gst_number,
                                "branch_code" => $company_code,
                                "branch_address" => $address,
                                "branch_country_id" => $cmb_country,
                                "branch_state_id" => $cmb_state,
                                "branch_city_id" => $cmb_city,
                                "branch_default_currency" => $cmb_currency,
                                "financial_year_id" => $financial_id,
                                "gst" => $gst,
                                "tds" => $tds,
                                "cess" => $cess,
                                "cgst_percentage" => $cgst,
                                "sgst_percentage" => $sgst,
                                "igst_percentage" => $igst,
                                "ugst_percentage" => $ugst,
                                "updated_user_id" => $user_id,
                                "updated_date" => date('Y-m-d')
                            );
         
         if($this->general_model->updateData('branch', $branch_data, array('branch_id' => $branch_id ))){
                $this->general_model->deleteData('branch_additional_info', array('branch_id' => $branch_id ));
                if (isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"] != "") {
                $path_parts = pathinfo($_FILES["logo"]["name"]);
                date_default_timezone_set('Asia/Kolkata');
                $date       = date_create();
                $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
                if (!is_dir('assets/branch_files/' . $branch_id))
                {
                    mkdir('./assets/branch_files/' . $branch_id, 0777, TRUE);
                } $url = "assets/branch_files/" . $branch_id . "/" . $image_path;
                if (in_array($path_parts['extension'], array(
                                "jpg",
                                "jpeg",
                                "png" )))
                {
                    if (is_uploaded_file($_FILES["logo"]["tmp_name"]))
                    {
                        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $url))
                        {
                            $image_name = $image_path;
                        }
                    }
                }
                $logo_data =   array("branch_logo" => $image_name);
                $this->general_model->updateData('branch', $logo_data, array('branch_id' => $branch_id ));
            }

            if(isset($_POST['custom_field'])){
                $data = array();
                $custom_field = $_POST['custom_field'];
                $custom_lablel = $_POST['custom_lablel'];
                $length = count($_POST['custom_field']);
               
                for ($i = 0; $i < $length; $i++) {
                    $data[$i]['value'] = $custom_field[$i];
                    $data[$i]['column_name'] =  $custom_lablel[$i];
                    $data[$i]['branch_id'] =  $branch_id;
                    $data[$i]['added_user_id'] =  $user_id;
                    $data[$i]['added_date'] =  date('Y-m-d');
                            
                }

                $this->db->insert_batch('branch_additional_info', $data);
            }
        }
        redirect('company_setting', 'refresh');
    }

    public function remove_logo_branch($id) {
        $branch_id = $this->encryption_url->encode($id);
        $logo_data =   array("branch_logo" => '');
        $this->general_model->updateData('branch', $logo_data, array('branch_id' => $id ));       
        redirect('company_setting/edit/'.$branch_id, 'refresh');
    }

    public function delete()
    {
        $user_module_id  = $this->config->item('user_module');
        $data['module_id']   = $user_module_id;
        $modules             = $this->modules;
        $privilege           = "delete_privilege";
        $data['privilege']   = "delete_privilege";
        $section_modules     = $this->get_section_modules($user_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id  = $this->input->post('delete_id');
        $id  = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('branch', array(
                'delete_status' => 1 ), array(
                'branch_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'branch',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Branch_id Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('company_setting');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Bategory can not be Deleted.');
            redirect("company_setting", 'refresh');
        }
    }

}
