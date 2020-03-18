<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'purchase_order_model',
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $service_module_id         = $this->config->item('service_module');
        $data['service_module_id'] = $service_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($service_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /*if($this->session->userdata('bulk_success')){
            if($this->session->flashdata('email_send') != 'success')
            $data['bulk_success'] = $this->session->userdata('bulk_success');
            $this->session->unset_userdata('bulk_success');
        }elseif ($this->session->userdata('bulk_error')) {
            $data['bulk_error'] = $this->session->userdata('bulk_error');
            $this->session->unset_userdata('bulk_error');
        }*/
        if (!empty($this->input->post()))
        {
             /* 2  => 'service_tax',
                3  => 'category_name',
                4  => 'service_price',
                5  => 'service_quantity',
                6  => 'service_damaged_quantity',
                7  => 'service_unit',
                8  => 'addded_user',*/
                
            $columns = array(
                0  => 'service_code',
                1  => 'service_name',
                2  => 'hsn_code',              
                3 => 'action');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->service_list_field();
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
                    $service_id                 = $this->encryption_url->encode($post->service_id);
                    $nestedData['added_date']   = $post->added_date;
                    $nestedData['service_code'] = $post->service_code;
                    $nestedData['service_name'] = $post->service_name;
                    $nestedData['hsn_code']       = $post->service_hsn_sac_code;
/*
                    if ($data['access_settings'][0]->tax_type == "gst")
                    {
                        $service_hsn_sac_code = "<br/>SAC : " . $post->service_hsn_sac_code;
                    }

                    $nestedData['service_name']     = $post->service_name . $service_hsn_sac_code;
                    $nestedData['service_tax']     = $this->precise_amount($post->service_tax_value,$access_common_settings[0]->amount_precision);
                    $nestedData['category_name']    = $post->category_name;
                    $nestedData['service_price']    = $this->precise_amount($post->service_price,$access_common_settings[0]->amount_precision);
                    $nestedData['service_quantity'] = '<a data-toggle="modal" data-target="#quantity_services" class="quantity_change" style="cursor:pointer;" data-pid="' . $service_id . '" data-qty="' . $post->service_quantity . '" title="" >' . $post->service_quantity;
                    '</a>';

                    if ($post->service_damaged_quantity > 0)
                    {
                        
                        $nestedData['service_damaged_quantity'] = '<a class="return_stock" data-toggle="modal" data-target="#return_stock" data-id="' . $service_id . '" href="#" title="Return to Stock" >'.$post->service_damaged_quantity.'</a>';
                    }
                    else
                    {
                        $nestedData['service_damaged_quantity'] = 0;
                    }

                    $nestedData['service_unit'] = $post->service_unit;
                    $nestedData['added_user']   = $post->first_name . ' ' . $post->last_name;
*/
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['service_module_id'], $data['active_edit'])) {
                        $cols .= '<span><a href="' . base_url('service/edit/') . $service_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['service_module_id'], $data['active_delete'])) {
                        $item_type = "service";
                        $exist_data = $this->common->database_service_exist($post->service_id, $item_type);

                        $exist_data_result = $this->general_model->getQueryRecords($exist_data);

                        if ($exist_data_result) {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal"><a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Delete" class="btn btn-app"><i class="fa fa-trash"></i></a><span>';
                        } else {
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a href="javascript:void(0);" data-id="' . $service_id . '" data-path="service/delete" title="Delete" data-toggle="tooltip" data-placement="bottom" class="delete_button btn btn-app"><i class="fa fa-trash"></i></a>';
                        }
                    }

                    if ($post->service_quantity > 0) {
                        $cols .= '<a class="damaged_services btn btn-xs btn-info" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#damaged_services" data-id="' . $service_id . '" href="#" title="Damaged services" ><span class="glyphicon glyphicon-wrench"></span> Move to Damaged</a>';
                    }
                    if ($post->service_damaged_quantity > 0) {
                        $cols .= '<a class="return_stock btn btn-xs btn-info" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#return_stock" data-id="' . $service_id . '" href="#" title="Return to Stock" ><span class="glyphicon glyphicon-circle-arrow-left"></span>Return to Stock</a>';
                    }
                    $cols .= '</div></div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[] = $nestedData;
                }

            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }
        else
        {
            $this->load->view('service/list', $data);
        }

    }

    public function add_service_ajax()
    {
        $service_module_id               = $this->config->item('service_module');
        $data['module_id']               = $service_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($service_module_id, $modules, $privilege);

        $data['access_settings']         = $section_modules['access_settings'];
        $data['access_common_settings']  = $section_modules['access_common_settings'];
        $this->form_validation->set_rules('service_code', 'Code', 'trim|required');
        $this->form_validation->set_rules('service_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('service_category', 'Category', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->add();
        }
        else
        {
            $title        = strtoupper(trim($this->input->post('service_name')));
            $subgroup     = "Service";
            $service_data = array(
                    "service_code"           => $this->input->post('service_code'),
                    "service_name"           => $this->input->post('service_name'),
                    "service_hsn_sac_code"   => $this->input->post('service_hsn_sac_code'),
                    "service_category_id"    => $this->input->post('service_category'),
                    "service_subcategory_id" => $this->input->post('service_subcategory'),
                    "service_price"          => $this->input->post('service_price'),
                    "service_tax_id"         => $this->input->post('service_tax'),
                    "service_tax_value"      => $this->input->post('service_tax_value'),
                    "service_tds_id"         => $this->input->post('tds_tax'),
                    "service_tds_value"      => $this->input->post('service_tds_code'),
                    "service_gst_id"         => $this->input->post('gst_tax'),
                    "service_gst_value"      => $this->input->post('service_gst_code'),
                    "service_unit"           => $this->input->post('service_unit'),
                    // "service_cgst"           => $this->input->post('service_cgst'),
                    // "service_sgst"           => $this->input->post('service_sgst'),
                    "added_date"             => date('Y-m-d'),
                    "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"              => $this->session->userdata('SESS_BRANCH_ID') );
            if ($id           = $this->general_model->insertData("services", $service_data))
            {
                $log_data = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $id,
                        'table_name'        => 'services',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'Service Inserted' );
                $this->general_model->insertData("log", $log_data);
            }
            else
            {
                $this->session->set_flashdata('fail', 'Service can not be Inserted.');
            }
        } $data['service_name'] = $service_data['service_name'];
        $data['service_id']   = $id;
        echo json_encode($data);
    }

    public function add()
    {

        $service_module_id          = $this->config->item('service_module');
        $modules                    = $this->modules;
        $privilege                  = "add_privilege";
        $data['privilege']          = $privilege;
        $section_modules            = $this->get_section_modules($service_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* Modules Present */
        $data['service_module_id']    = $service_module_id;
        $data['module_id']    = $service_module_id;

        $data['category_module_id']    = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id']         = $this->config->item('tax_module');

 
        $data['service_category'] = $this->service_category_call();
        $data['tax_gst']              = $this->tax_call_type('GST');
        $data['tax_tds']              = $this->tax_call_type('TDS');
        $data['uqc']              = $this->uqc_product_service_call('service');
        $data['tax_section'] = $this->tax_section_call();
        $data['sac']              = $this->sac_call();
        $data['tds_section']      = $this->tds_section_call();
        $access_settings          = $data['access_settings'];
        $primary_id               = "service_id";
        $table_name               = "services";
        $date_field_name          = "added_date";
        $current_date             = date('Y-m-d');
        $data['invoice_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        
        $this->load->view('service/add', $data);
    }

    public function add_bulk_upload_service()
    {
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/serviceCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();
        if (!$this->upload->do_upload('bulk_service')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_service',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        } else {
            $service_module_id          = $this->config->item('service_module');
            $modules                    = $this->modules;
            $privilege                  = "add_privilege";
            $data['privilege']          = $privilege;
            $section_modules            = $this->get_section_modules($service_module_id, $modules, $privilege);
            /* presents all the needed */
            $data=array_merge($data,$section_modules);

            /* presents all the needed */
            $Updata = array('uploadData' => $this->upload->data());
            if (!empty($Updata['uploadData']['file_name'])) {
                $import_xls_file = $Updata['uploadData']['file_name'];
                $inputFileName = $path . $import_xls_file;
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);               
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    if(!empty($allDataInSheet)){
                        if(strtolower($allDataInSheet[1]['A']) == 'service name' && strtolower($allDataInSheet[1]['B']) == 'unit price' && strtolower($allDataInSheet[1]['C']) == 'category' && strtolower($allDataInSheet[1]['D']) == 'subcategory' && strtolower($allDataInSheet[1]['E']) == 'hsn/sac code' && strtolower($allDataInSheet[1]['F']) == 'gst tax percentage' && strtolower($allDataInSheet[1]['G']) == 'tds tax percentage' && strtolower($allDataInSheet[1]['H']) == 'unit of measurement'){

                            $service_exist = $this->general_model->GetServiceName();
                            $service_exist = array_column($service_exist, 'service_name', 'service_name');
                            $category = $this->general_model->GetCategory_bulk('service');
                            $category = array_column($category, 'category_id', 'category_name');
                            $sub_category = $this->general_model->GetSubCategory_bulk('service');
                            $sub_category_id = array_column($sub_category, 'category_id_sub','subcategory_name');
                            $sub_category= array_column($sub_category, 'sub_category_id', 'subcategory_name');
                            $hsn = $this->general_model->hsn_call_product_bulk();
                            $hsn = array_column($hsn, 'hsn_code', 'hsn_code');
                            $uom = $this->general_model->Get_uqc_bulk_latest('service');
                            $uom = array_column($uom, 'uom_id', 'uom');
                            /*$uom = $this->general_model->Get_uom_bulk();
                            $uom = array_column($uom, 'uom_id', 'uom');*/
                            $gst = $this->general_model->Get_tax_bulk('GST');
                            $gst = array_column($gst, 'tax_id', 'tax_value');
                            $tds = $this->general_model->Get_tax_bulk('TDS');
                            $tds = array_column($tds, 'tax_id', 'tax_value');

                            $header_row = array_shift($allDataInSheet);
                            $access_settings          = $data['access_settings'];
                            $primary_id               = "service_id";
                            $table_name               = "services";
                            $date_field_name          = "added_date";
                            $current_date             = date('Y-m-d');
                            $error_array = array();
                            foreach($allDataInSheet as $row){
                                $service_name_exit = strtolower(trim($row['A']));
                                $unit_price = trim($row['B']);
                                $name_category= strtolower(trim($row['C']));
                                $name_subcategory= strtolower(trim($row['D']));
                                $hsn_number = trim($row['E']);
                                $service_gst= trim($row['F']);
                                $service_tds= trim($row['G']);
                                $unit_of_measurement= strtolower(trim($row['H']));
                                $is_add = true;
                                $error = '';
                                $tds_service_id ='';
                                $gst_service_id = '';
                                $service_subcategory_id = '';
                                $service_category_id = '';

                                $invoice_number   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                /*$service_name_exit = $this->get_bulk_check_service($service_name, 0);*/
                                if($service_name_exit != '' && !empty($service_name_exit)){
                                    if(!isset($service_exist[$service_name_exit])){
                                        if($unit_price != '' && !empty($unit_price)){
                                            if($name_category !='' && !empty($name_category)){
                                                if(isset($category[$name_category]) && $is_add == true){
                                                   $service_category_id = $category[$name_category];
                                                   if($name_subcategory != '' || !empty($name_subcategory)){
                                                        if(isset($sub_category_id[$name_subcategory])){
                                                            $subcategory_cat_value = $sub_category_id[$name_subcategory];
                                                            if($service_category_id == $subcategory_cat_value){
                                                                $service_subcategory_id = $sub_category[$name_subcategory];
                                                            }else {
                                                                 $is_add = false;
                                                                 $error = "SubCategory Name is Not Exist! For Entered Category Name";
                                                                 $error_log .= $row['D'].' Undefined SubCategory Name! <br>';
                                                            }
                                                        }else{
                                                            $is_add = false;
                                                            $error = "SubCategory Name is Not Exist! Please Update Your SubCategory Name";
                                                            $error_log .= $row['D'].' Undefined SubCategory Name! <br>';
                                                        }  
                                                    }            
                                                }else{
                                                    $is_add = false;
                                                    $error = "Category Name is Not Exist! Please Update Your Category Name";
                                                    $error_log .= $row['C'].' Undefined Category Name! <br>';
                                                }
                                                if($hsn_number != '' && !empty($hsn_number) && $is_add == true){
                                                    if(in_array($hsn_number, $hsn)){
                                                        if(($unit_of_measurement !='' || !empty($unit_of_measurement)) && $is_add == true){
                                                            if(isset($uom[$unit_of_measurement])){
                                                                $service_unit_id = $uom[$unit_of_measurement];
                                                                if($service_gst != '' && !empty($service_gst)){
                                                                    $service_gst = $this->precise_amount($service_gst,2);
                                                                    if(isset($gst[$service_gst])){
                                                                        $gst_service_id = $gst[$service_gst];
                                                                        if($service_tds != '' || !empty($service_tds)){
                                                                            $service_tds = $this->precise_amount($service_tds,2);
                                                                            if(isset($tds[$service_tds])){
                                                                                $tds_service_id = $tds[$service_tds];
                                                                            } else {
                                                                                $is_add = false;
                                                                                $error = "TDS Value is Not Exist! Please Update Your TDS value";
                                                                                $error_log .= $row['G'].' Undefined TDS Value! <br>';
                                                                            }
                                                                        } 
                                                                    }else {
                                                                        $is_add = false;
                                                                        $error = "GST Value is Not Exist! Please Update Your GST value";
                                                                        $error_log .= $row['F'].' Undefined GST Value! <br>';
                                                                    }
                                                                }
                                                            }else {
                                                                $is_add = false;
                                                                $error = "Unit_Of_Measurement Name is Not Exist! Please Update Your Unit_Of_Measurement Name";
                                                                $error_log .= $row['H'].' Undefined Unit_Of_Measurement Name! <br>';
                                                            }
                                                        }else{
                                                            $is_add = false;
                                                             $error = "Unit Of Measurement Name Should Not Empty";
                                                             $error_log .= $row['H'].' Unit_Of_Measurement Name is Not Exist! <br>';
                                                        }
                                                    }else{
                                                        $is_add = false;
                                                        $error = "HSN Number is Not Exist! Please Update HSN Data";
                                                        $error_log .= $row['C'].' Undefined HSN Number! <br>';
                                                    }
                                                }elseif($is_add == true){
                                                    $is_add = false;
                                                    $error = "HSN number should not empty!";
                                                    $error_log .= $row['B'].'HSN number should not empty! <br>';
                                                }
                                            }else{
                                                $is_add = false;
                                                $error = "Category Name is Empty";
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Unit Price Should Not Empty!";
                                            $error_log .= 'Unit Price Should Not Empty! <br>';
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Service Name Already Exit!";
                                        $error_log .= $row['A'].' Service Name Already Exit! <br>';
                                    }
                                }else{
                                    $is_add = false;
                                    $error = "Service Name Should Not Empty!";
                                }
                                if($is_add){
                                    $headers = array(
                                        'service_code' => $invoice_number,
                                        "service_hsn_sac_code" => trim($row['E']),
                                        "service_name" => trim($row['A']),
                                        "service_unit" => $service_unit_id,
                                        "service_price" => trim($row['B']),
                                        "service_category_id" => $service_category_id,
                                        "service_subcategory_id" => $service_subcategory_id,
                                        "service_tds_id" => $tds_service_id,
                                        "service_tds_value" => trim($row['G']),
                                        "service_gst_id" => $gst_service_id,
                                        "service_gst_value" => trim($row['F']),
                                        "delete_status" => 0,
                                        "added_date" => date('Y-m-d'),
                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                    );
                                    $this->db->insert($table_name,$headers);
                                }else {
                                    $error_array[] = $error_log;
                                }
                                /* $row['Error'] = $added_error;*/
                                if(!$is_add && !empty($row)){
                                    array_unshift($row,$error);
                                    array_push($errors_email, array_values($row));
                                }
                            }
                            $log_data = array(
                                'user_id'           => $this->session->userdata('SESS_USER_ID'),
                                'table_id'          => 0,
                                'table_name'        => 'services',
                                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                                'message'           => 'Bulk Service Inserted' );
                            $this->general_model->insertData("log", $log_data);

                            if(!empty($error_array)){
                                $errorMsg = implode('<br>', $error_array);
                                $this->session->set_flashdata('bulk_error_service',$errorMsg);
                                /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)); */   
                            }else{
                                $successMsg = 'Service imported successfully.';
                                $this->session->set_flashdata('bulk_success_service',$successMsg);
                                /*$this->session->set_userdata('bulk_success', $successMsg);  */
                            }
                            //get_bulk_check_service repet perpouse
                        }else{
                            $this->session->set_flashdata('bulk_error_service',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }
                    }else{
                        $this->session->set_flashdata('bulk_error_service',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_service',"Error on file upload, please try again.");
                    /*$this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');*/
                }
            }
        }
        if(!empty($errors_email)){
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = $this->session->userdata('SESS_EMAIL');
            /*$to = 'harish.sr@aavana.in';*/
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
           $resp = $this->send_csv_mail($errors_email,'Service Bulk Import Error Logs, <br><br> PFA,',"Service bulk upload error logs in <{$import_xls_file}>",$to);
           $this->session->set_flashdata('bulk_error_service',"Error email has been sent to registered email ID.");
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
             /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
        }
        redirect("service", 'refresh');
    }
    function send_csv_mail ($csvData, $body, $subject,$to) {

        /*$to = 'chetna.b@aavana.in';*/
        $path = 'uploads/ServiceErrors/error.csv';
        $fp = fopen($path, 'w');//fopen('php://temp', 'w+');
        foreach ($csvData as $line) fputcsv($fp, array_values($line));
        rewind($fp);
        fclose($fp);
        $emailDataSet = array(                         
                            'subject' =>$subject,                    
                            'message' => $body,
                            'email'=>  $to,
                            'csv_string' => $path
                        );

        require APPPATH . 'third_party/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
       
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "chetna.b@aavana.in";
        $mail->Password   = "ZXCVzxcv";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = '587';
        $mail->IsHTML(true);
        $mail->CharSet    = 'UTF-8';

       // $mail->IsHTML(true);
        $mail->setFrom("noreply@aodry.com", $subject);
        $mail->addReplyTo("noreply@aodry.com", $subject);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $bodyContent = $body;
        $mail->Subject = $subject;
        $mail->Body = $bodyContent;
        $mail->addAttachment($path);
        $resp = 'Success';
        if (!$mail->send()) {
            $resp = 'Message could not be sent.';
            $resp =  'Mailer Error : ' . $mail->ErrorInfo;
        } else {
            $this->session->set_flashdata('email_send_service', 'success');
        }
        
        return $resp;
    }

    public function add_service()
    {
        $service_module_id          = $this->config->item('service_module');
        $modules                    = $this->modules;
        $privilege                  = "add_privilege";
        $data['privilege']          = $privilege;
        $section_modules            = $this->get_section_modules($service_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* Modules Present */
        $data['service_module_id']    = $service_module_id;
        $data['module_id']    = $service_module_id;
        $this->form_validation->set_rules('service_code', 'Code', 'trim|required');
        $this->form_validation->set_rules('service_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('service_category', 'Category', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->add();
        }
        else
        {
           /* $service_tax = array();

        if ($this->input->post('service_tax') != "" && $this->input->post('service_tax') != 0)
        {
            $service_tax = explode("-", $this->input->post('service_tax'));
        }
        else
        {

            $service_tax[0] = 0;
            $service_tax[1] = 0;
        }*/
            $title        = strtoupper(trim($this->input->post('service_name')));
            $subgroup     = "Service";
            $service_data = array(
                    "service_code"           => $this->input->post('service_code'),
                    "service_name"           => $this->input->post('service_name'),
                    "service_hsn_sac_code"   => $this->input->post('service_hsn_sac_code'),
                    "service_category_id"    => $this->input->post('service_category'),
                    "service_subcategory_id" => $this->input->post('service_subcategory'),
                    "service_price"          => $this->input->post('service_price'),
                    "service_tds_id"         => $this->input->post('tds_tax'),
                    "service_tds_value"      => rtrim($this->input->post('service_tds_code'),'%'),
                    "service_gst_id"         => $this->input->post('gst_tax'),
                    "service_unit"           => $this->input->post('service_unit'),
                    "service_gst_value"      => rtrim($this->input->post('service_gst_code'),'%'),
                    "added_date"             => date('Y-m-d'),
                    "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"              => $this->session->userdata('SESS_BRANCH_ID') );
            if ($service_id           = $this->general_model->insertData("services", $service_data))
            {
                $successMsg = 'Service Added Successfully';
                $this->session->set_flashdata('service_success',$successMsg);
                $log_data = array(
                        'user_id'           => $this->session->userdata('SESS_USER_ID'),
                        'table_id'          => $service_id,
                        'table_name'        => 'services',
                        'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'Service Inserted' );
                $this->general_model->insertData("log", $log_data);
                redirect('service', 'refresh');
            }
            else
            {
                $errorMsg = 'Service Add Unsuccessful';
                $this->session->set_flashdata('service_error',$errorMsg);
                $this->session->set_flashdata('fail', 'Service can not be Inserted.');
                redirect("service", 'refresh');
            }
        } redirect("service", 'refresh');
    }

    public function edit($id)
    {
        $service_id                              = $this->encryption_url->decode($id);
      $service_module_id          = $this->config->item('service_module');
        $modules                    = $this->modules;
        $privilege                  = "add_privilege";
        $data['privilege']          = $privilege;
        $section_modules            = $this->get_section_modules($service_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* Modules Present */
        $data['service_module_id']    = $service_module_id;
        $data['module_id']    = $service_module_id;

        $data['category_module_id']    = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id']         = $this->config->item('tax_module');
        $data['tds_section']      = $this->tds_section_call();

        $select                      = "s.*";
        $table                       = "sub_category s";
        $join['services sr']          = "sr.service_category_id=s.category_id";
        
        $where                    = [
                's.delete_status' => 0,   
                'sr.service_id'      => $service_id ];
        $data['service_subcategory'] = $this->general_model->getJoinRecords($select, $table, $where, $join);
     
         $data['service_category'] = $this->service_category_call();
         $data['tax_gst']              = $this->tax_call_type('GST');
         $data['tax_tds']              = $this->tax_call_type('TDS');
         $data['uqc']              = $this->uqc_product_service_call('service');
      
        $data['tax_section'] = $this->tax_section_call();
        $data['sac']              = $this->sac_call();
        $string                   = "s.*,c.category_name,su.sub_category_name";
        $from                     = "services s";
        $where                    = [
                's.delete_status' => 0,
                'service_id'      => $service_id ];
        $join                     = [
                'category c'      => "c.category_id=s.service_category_id",
                'sub_category su' => "su.sub_category_id = s.service_subcategory_id". "#" . "left" ];
        $order                    = [
                's.service_id' => "desc" ];
        $data['data']             = $this->general_model->getJoinRecords($string, $from, $where, $join, $order);

    // echo "<pre>";
    // print_r($data);
    // exit;
        $this->load->view('service/edit', $data);
    }

        public function tds_list()
    {

        if (!empty($this->input->post()))
        {
        $service_module_id = $this->config->item('service_module');
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($service_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_common_settings     = $section_modules['access_common_settings'];

            $columns = array(
                0 => 'tds_value',
                1 => 'description',
                2 => 'module_type'
                );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir   = $this->input->post('order')[0]['dir'];

            $tds_section_id = $this->input->post('tds_section');
            $list_data      = $this->common->tds_field($tds_section_id);

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
                $i = 1;

                foreach ($posts as $post)
                {
                    $tds_id = $this->encryption_url->encode($post->tds_id);

                    $nestedData['tds_value'] = '<span id="accounting_tds">' . $this->precise_amount($post->tds_value,$access_common_settings[0]->amount_precision) . '</span>&nbsp;<span id="accounting_tds_id" style="display:none;">' . $post->tds_id . '</span>';
                    $nestedData['module_type'] = $post->module_type;
                    $nestedData['description'] = $post->description;

                    $cols                 = '<span class="btn btn-info apply" class="close" data-dismiss="modal">Apply</span>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }

            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }

    }

    public function edit_service()
    {

        $service_module_id          = $this->config->item('service_module');
        $modules                    = $this->modules;
        $privilege                  = "add_privilege";
        $data['privilege']          = $privilege;
        $section_modules            = $this->get_section_modules($service_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* Modules Present */
        $data['service_module_id']    = $service_module_id;
        $data['module_id']    = $service_module_id;

             /* $service_tax = array();

        if ($this->input->post('service_tax') != "" && $this->input->post('service_tax') != 0)
        {
            $service_tax = explode("-", $this->input->post('service_tax'));
        }
        else
        {

            $service_tax[0] = 0;
            $service_tax[1] = 0;
        } */

        $service_id                              = $this->input->post("service_id");
        $update                          = array(
                "service_code"           => $this->input->post('service_code'),
                "service_name"           => $this->input->post('service_name'),
                "service_hsn_sac_code"   => $this->input->post('service_hsn_sac_code'),
                "service_category_id"    => $this->input->post('service_category'),
                "service_subcategory_id" => $this->input->post('service_subcategory'),
                "service_price"          => $this->input->post('service_price'),
                "service_tds_id"         => $this->input->post('tds_tax'),
                "service_tds_value"      => rtrim($this->input->post('service_tds_code'),'%'),
                "service_gst_id"         => $this->input->post('gst_tax'),
                "service_unit"           => $this->input->post('service_unit'),
                "service_gst_value"      => rtrim($this->input->post('service_gst_code'),'%'),
                "updated_date"           => date('Y-m-d'),
                "updated_user_id"        => $this->session->userdata('SESS_USER_ID') );

        if ($this->general_model->updateData('services', $update, array(
                        'service_id' => $service_id )))
        {
            $successMsg = 'Service Updated Successfully';
            $this->session->set_flashdata('service_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $service_id,
                    'table_name'        => 'services',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Service Updated' );
            $this->general_model->insertData('log', $log_data);
            redirect('service', 'refresh');
        }
        else
        {
            $errorMsg = 'Service Update Unsuccessful';
            $this->session->set_flashdata('service_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Service can not be Updated.');
            redirect("service", 'refresh');
        }
    }

    public function delete()
    {
        $id                              = $this->input->post('delete_id');
        $service_id                              = $this->encryption_url->decode($id);
        $service_module_id          = $this->config->item('service_module');
        $modules                    = $this->modules;
        $privilege                  = "add_privilege";
        $data['privilege']          = $privilege;
        $section_modules            = $this->get_section_modules($service_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        /* Modules Present */
        $data['service_module_id']    = $service_module_id;
        $data['module_id']    = $service_module_id;
        if ($this->general_model->updateData('services', array(
                        'delete_status' => 1 ), array(
                        'service_id' => $service_id )))
        {
            $successMsg = 'Service Deleted Successfully';
            $this->session->set_flashdata('service_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('user_id'),
                    'table_id'          => $service_id,
                    'table_name'        => 'services',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Service Deleted' );
            $this->general_model->insertData('log', $log_data);
            redirect('service', 'refresh');
        }
        else
        {
            $errorMsg = 'Service Delete Unsuccessful';
            $this->session->set_flashdata('service_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Service can not be Deleted.');
            redirect("service", 'refresh');
        }
    }

    public function get_bulk_check_service($service_name,$service_id=0)
    {
        $data         = $this->general_model->getRecords('count(*) num', 'services', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0,
                'service_name'  => $service_name,
                'service_id!='  => $service_id ));
        return $data;
    }

    public function get_check_service()
    {
        $service_name = strtoupper(trim($this->input->post('service_name')));
        $service_id   = $this->input->post('service_id');
        $data         = $this->general_model->getRecords('count(*) num', 'services', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0,
                'service_name'  => $service_name,
                'service_id!='  => $service_id ));
        echo json_encode($data);
    }

    public function sac_list()
    {
        if (!empty($this->input->post()))
        {
            $columns = array(
                    0 => 'accounting_code',
                    1 => 'description' );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data = $this->common->sac_field();

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
                $i = 1;
                foreach ($posts as $post)
                {
                    $sac_id                        = $this->encryption_url->encode($post->sac_id);
                    // $nestedData['sl_no']  = $i++;
                    $nestedData['accounting_code'] = '<span id="accounting_sac_code">' . $post->accounting_code . '</span>';
                    $nestedData['description']     = $post->description;

                    $cols                 = '<span class="btn btn-info apply" class="close" data-dismiss="modal">Apply</span>';
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
    }


    public function hsn_list(){

        if (!empty($this->input->post())){
            $columns = array(
                    0 => 'hsn_code',
                    1 => 'description' );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data = $this->common->hsn_list_field();

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
                $i = 1;
                foreach ($posts as $post)
                {
                    $sac_id                        = $this->encryption_url->encode($post->hsn_id);
                    // $nestedData['sl_no']  = $i++;
                    $nestedData['hsn_code'] = '<span id="accounting_sac_code">' . $post->hsn_code . '</span><span id="hsn_id" style="display:none;">' . $post->hsn_id . '</span>';
                    $nestedData['description']     = $post->description;

                    $cols                 = '<span class="btn btn-info apply" class="close" data-dismiss="modal">Apply</span>';
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
    }

    public function get_hsn_code($id) {
        $id = $id;
        $data = $this->general_model->getRecords('*', 'hsn', array('hsn_id' => $id, 'delete_status' => 0 ));
        echo json_encode($data);
    }
}

