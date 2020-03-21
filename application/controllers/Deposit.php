<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Deposit extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
        //gg
    }

    public function index() {
        $deposit_module_id = $this->config->item('deposit_module');
        $data['deposit_module_id'] = $deposit_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($deposit_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

       

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'action',
                1 => 'shareholder_code',
                2 => 'shareholder_name',
                3 => 'country',
                4 => 'state',
                5 => 'city',
                6 => 'type',
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->deposit_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            } 
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $shareholder_id = $this->encryption_url->encode($post->deposit_id);
                    $nestedData['deposit_type'] = $post->deposit_type;
                    $nestedData['comments'] = $post->comments;
                    $nestedData['others_name'] = ($post->others_name)?$post->others_name:'NA';
                    $nestedData['deposit_bank'] = ($post->deposit_bank)?$post->deposit_bank:'NA';
                    $nestedData['deposit_date'] =  date('d-m-Y', strtotime($post->deposit_date));
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['deposit_module_id'], $data['active_edit'])) {
                        $cols .= '<span><a href="' . base_url('deposit/edit/') . $shareholder_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['deposit_module_id'], $data['active_delete'])) {     
                        $ledger_id = $post->ledger_id;  
                        $this->db->select('ledger_id');
                        $this->db->from('accounts_journal_voucher');
                        $this->db->where('ledger_id',$ledger_id);
                        $sup = $this->db->get();
                        $result_option = $sup->result();  
                        if(empty($result_option)){                     
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $shareholder_id . '" data-path="deposit/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                       
                    }
                    $cols .= '</div></div>';
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('deposit/list', $data);
        }
    }

    public function add() {
        
        $deposit_module_id = $this->config->item('deposit_module');
        $data['module_id'] = $deposit_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($deposit_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "deposit_id";
        $table_name = "tbl_deposit";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['bank_account']     = $this->bank_account_call_new();
        $this->load->view('deposit/add', $data);
    }   

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $deposit_module_id = $this->config->item('deposit_module');
        $data['module_id'] = $deposit_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($deposit_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'shar.*';
        $table = 'tbl_deposit shar';
        $where = array('shar.deposit_id' => $id);
      $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        
        $data['bank_account']     = $this->bank_account_call_new();
        $this->load->view('deposit/edit', $data);
    }

    public function edit_deposit() {

        $deposit_module_id = $this->config->item('deposit_module');
        $data['module_id'] = $deposit_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($deposit_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('deposit_id');
       if($this->input->post('cmb_deposit_type') == 'others'){  
            $type = $this->input->post('cmb_deposit_type');     
            $deposit_name = trim($this->input->post('txt_deposit_name'));
            $type_input = 'other';
        }else{
            $type = $this->input->post('cmb_deposit_type');
            $bank_name = explode("/", $this->input->post('cmb_bank'));
            $bank_id = $bank_name[0];
            $deposit_name = $bank_name[1];
            if($type == 'fixed deposit'){
               $type_input = 'fixed'; 
            }else{
                $type_input = 'recurring';
            }            
        }

        /*$ledger_id = $this->input->post('ledger_id');

        $general_ledger = $this->config->item('general_ledger');
        if($type == 'fixed deposit'){            
            $default_fixed_id = $general_ledger['Fixed_Deposit'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            
            if(!empty($partner_ledger_name)){
                $partner_ledger = $partner_ledger_name->ledger_name;
                $partner_ledger_name = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
            }   
        }elseif($type == 'recurring deposit'){            
            $default_fixed_id = $general_ledger['Recurring_Deposit'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
           
            if(!empty($partner_ledger_name)){
                $partner_ledger = $partner_ledger_name->ledger_name;
                $partner_ledger_name = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
            }
        }elseif($type == 'others'){
            
            $default_fixed_id = $general_ledger['Other_Deposits']; 
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            
          
            if(!empty($partner_ledger_name)){
                $partner_ledger = $partner_ledger_name->ledger_name;
                $partner_ledger_name = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
            }
        }
           
         $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$partner_ledger_name}' WHERE ledger_id='{$ledger_id}'");*/
         $general_ledger = $this->config->item('general_ledger');
        if($type == 'fixed deposit'){            
            $default_fixed_id = $general_ledger['Fixed_Deposit'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            $partner_ary = array(
                        'ledger_name' => $deposit_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $deposit_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary);  
        }elseif($type == 'recurring deposit'){
            
            $default_fixed_id = $general_ledger['Recurring_Deposit'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            
            $partner_ary = array(
                        'ledger_name' => $deposit_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $deposit_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary); 
        }elseif($type == 'others'){
            
            $default_fixed_id = $general_ledger['Other_Deposits']; 
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            
            $partner_ary = array(
                        'ledger_name' => $deposit_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $deposit_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary); 
        }
            $deposit_data = array(
                "deposit_code" =>  $this->input->post('deposit_code'),
                "deposit_type" => $this->input->post('cmb_deposit_type'),
                "deposit_date" => date('Y-m-d', strtotime($this->input->post('txt_date_of_deposit'))),
                "deposit_bank" => $this->input->post('cmb_bank'),
                "others_name" => $this->input->post('txt_deposit_name'),
                "comments" => $this->input->post('comments'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => date('Y-m-d'),
                "ledger_id" => $deposit_ledger_id,
                "updated_user_id" => $this->session->userdata('SESS_USER_ID') 
            );

        $table = "tbl_deposit";
        
        $where = array("deposit_id" => $id);
        if ($this->general_model->updateData($table, $deposit_data, $where)) {           
           $this->updateOption_deposit($id,$deposit_name,$type_input);
            $successMsg = 'Deposit Updated Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_deposit',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Deposit Updated');
                $this->general_model->insertData($table, $log_data);            

            redirect('deposit', 'refresh');
        } else {
            $errorMsg = 'Deposit Update Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'customer can not be Updated.');
            redirect("deposit", 'refresh');
        }
    }

    public function add_deposit() {
        $deposit_module_id = $this->config->item('deposit_module');
        $data['module_id'] = $deposit_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($deposit_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        if($this->input->post('cmb_deposit_type') == 'others'){  
            $type = $this->input->post('cmb_deposit_type');     
            $deposit_name = trim($this->input->post('txt_deposit_name'));
            $type_input = 'other';
            $type_input1 = 'interest other';
        }else{
            $type = $this->input->post('cmb_deposit_type');
            $bank_name = explode("/", $this->input->post('cmb_bank'));
            $bank_id = $bank_name[0];
            $deposit_name = $bank_name[1];
            //$deposit_name = ucfirst($type).'@'.$deposit_name;
            if($type == 'fixed deposit'){
               $type_input = 'fixed'; 
               $type_input1 = 'interest fixed';
            }else{
                $type_input = 'recurring';
                $type_input1 = 'interest recurring';
            }            
        }

        $general_ledger = $this->config->item('general_ledger');
        if($type == 'fixed deposit'){            
            $default_fixed_id = $general_ledger['Fixed_Deposit'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            $partner_ary = array(
                        'ledger_name' => $deposit_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $deposit_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary);  
        }elseif($type == 'recurring deposit'){
            
            $default_fixed_id = $general_ledger['Recurring_Deposit'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            
            $partner_ary = array(
                        'ledger_name' => $deposit_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $deposit_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary); 
        }elseif($type == 'others'){
            
            $default_fixed_id = $general_ledger['Other_Deposits']; 
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            
            $partner_ary = array(
                        'ledger_name' => $deposit_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Current Assets',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$deposit_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $deposit_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary); 
        }
           

            $deposit_data = array(
                "deposit_code" =>  $this->input->post('deposit_code'),
                "deposit_type" => $this->input->post('cmb_deposit_type'),
                "deposit_date" => date('Y-m-d', strtotime($this->input->post('txt_date_of_deposit'))),
                "deposit_bank" => $this->input->post('cmb_bank'),
                "others_name" => $this->input->post('txt_deposit_name'),
                "comments" => $this->input->post('comments'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "added_date" => date('Y-m-d'),
                "ledger_id" => $deposit_ledger_id,
                "added_user_id" => $this->session->userdata('SESS_USER_ID') 
            );
            
        $table = "tbl_deposit";
        $id = $this->general_model->insertData($table, $deposit_data);
        if ($id) {            
            $this->createOption_deposit($id,$deposit_name,$type_input);
            $this->createOption_deposit($id,$deposit_name,$type_input1);
            $successMsg = 'Deposit Added Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_deposit',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Deposit Inserted');
            $this->general_model->insertData($table, $log_data);
           
        }else{
            $errorMsg = 'Deposit Add Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);
        }
        redirect("deposit", 'refresh');
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $deposit_module_id = $this->config->item('deposit_module');
        $data['module_id'] = $deposit_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($deposit_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "tbl_deposit";
        $data = array("delete_status" => 1);
        $where = array("deposit_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
           $this->deleteOptionFixed($id);
            $successMsg = 'Deposit Deleted Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_deposit',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Deposit Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('deposit');
        } else {
            $errorMsg = 'Depsit can not be Deleted.';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Deposit can not be Deleted.');
            redirect("deposit", 'refresh');
        }
    }

    public function DepositValidation(){
        $deposit_name = trim($this->input->post('deposit_name'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT deposit_id FROM tbl_deposit WHERE  others_name like '".$deposit_name."' AND deposit_id != '{$id}' ")->num_rows();

        echo  json_encode(array('rows' => $rows ));
    }


    public function BankValidation(){
        $deposit_bank = trim($this->input->post('deposit_bank'));
        $id = $this->input->post('id');
        $deposit_tye = trim($this->input->post('deposit_tye'));
        
        $rows = $this->db->query("SELECT deposit_id FROM tbl_deposit WHERE deposit_bank like '".$deposit_bank."' AND deposit_type = '".$deposit_tye."' AND deposit_id != '{$id}' ")->num_rows();

        echo  json_encode(array('rows' => $rows ));
    }


 function createOption_deposit($id,$deposit_name,$type_input){
         $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $user_id = $this->session->userdata('SESS_USER_ID');
        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type',$type_input);
        $this->db->where('branch_id',$branch_id);
        $sup = $this->db->get();
        $result_option = $sup->result();
        $option_array = array();    
        $date = date('Y-m-d');
           $i = 1;     
        foreach ($result_option as $key1 => $value1) { 
            $deposit_option = $value1->customise_option;
            $parent_id = $value1->id;

            $deposit_option = str_ireplace('{{X}}',$deposit_name, $deposit_option);
            $option_array[$i]['purpose_option'] = $deposit_option;
            $option_array[$i]['parent_id'] =  $parent_id;
            $option_array[$i]['payee_id'] = $id;
            $option_array[$i]['branch_id'] = $branch_id;
            $option_array[$i]['added_user_id'] = $user_id;
            $option_array[$i]['added_date'] = $date;

            $i = $i + 1;
        }
     
        if(!empty($option_array)){
            $table = "tbl_transaction_purpose_option";
            $this->db->insert_batch($table, $option_array);
        }
    }

    function updateOption_deposit($id,$deposit_name,$type_input){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $user_id = $this->session->userdata('SESS_USER_ID');

        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type',$type_input);
        $this->db->where('branch_id',$branch_id);
        $sup = $this->db->get();
        $result_option = $sup->result();
        $option_array = array();    
        $date = date('Y-m-d');
       
             
        foreach ($result_option as $key1 => $value1) { 
            $deposit_option = $value1->customise_option;
            $parent_id = $value1->id;

            $deposit_option = str_ireplace('{{X}}',$deposit_name, $deposit_option);
            $option_array['purpose_option'] = $deposit_option;
            $option_array['parent_id'] =  $parent_id;
            $option_array['payee_id'] = $id;
            $option_array['branch_id'] = $branch_id;
            $option_array['added_user_id'] = $user_id;
            $option_array['added_date'] = $date;
            $where = array("payee_id" => $id,"parent_id"=>$parent_id);
             $table = "tbl_transaction_purpose_option";
            $this->general_model->updateData($table, $option_array, $where);
        }
    }

    function deleteOptionFixed($id){
            $option_array['delete_status'] = 1; 
            $where = array("payee_id" => $id);
             $table = "tbl_transaction_purpose_option";
            $this->general_model->updateData($table, $option_array, $where);
    }


}
