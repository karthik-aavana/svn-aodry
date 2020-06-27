<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Share_holder extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
        //gg
    }

    public function index() {
        $shareholder_module_id = $this->config->item('shareholder_module');
        $data['shareholder_module_id'] = $shareholder_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($shareholder_module_id, $modules, $privilege);

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
            $list_data = $this->common->shareholder_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }
            if (empty($this->input->post('search')['value'])) {
                // $list_data['limit'] = $limit;
                // $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                // $list_data['limit'] = $limit;
                // $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            } 
            $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $shareholder_id = $this->encryption_url->encode($post->id);
                    $nestedData['shareholder_code'] = $post->sharholder_code;
                    $nestedData['shareholder_name'] = $post->sharholder_name;
                    $nestedData['shareholder_type'] = $post->sharholder_type;
                    $nestedData['shareholder_address'] = $post->sharholder_address;
                    $nestedData['sharholder_pan_number'] = $post->sharholder_pan_number;
                    $nestedData['date_of_birth'] =  date('d-m-Y', strtotime($post->date_of_birth));
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['shareholder_module_id'], $data['active_edit'])) {
                        $cols .= '<span><a href="' . base_url('share_holder/edit/') . $shareholder_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['shareholder_module_id'], $data['active_delete'])) {  
                        if($post->sharholder_type != 'shareholder'){
                        $ledger_id = $post->partner_ledger_id;  
                        $this->db->select('ledger_id');
                        $this->db->from('accounts_journal_voucher');
                        $this->db->where('ledger_id',$ledger_id);
                        $sup = $this->db->get();
                        $result_option = $sup->result();  
                        if(empty($result_option)){                  
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $shareholder_id . '" data-path="share_holder/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                       }else{
                        
                        $id = $post->id;
                        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
                        $this->db->select('*');
                        $this->db->from('tbl_journal_voucher');
                        $this->db->where('partner_shareholder_id',$id);
                        $this->db->where('input_type','shareholder');
                        $this->db->where('branch_id',$branch_id);
                        
                        $sup = $this->db->get();
                        $result_option = $sup->result();  
                        if(empty($result_option)){  
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $shareholder_id . '" data-path="share_holder/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                       }
                    }
                    $cols .= '</div></div>';
                    $disabled = '';
                    if(!in_array($data['shareholder_module_id'], $data['active_delete']) && !in_array($data['shareholder_module_id'], $data['active_edit'])){
                        $disabled = 'disabled';
                    }
                    $nestedData['action'] = $cols . '<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal"'.$disabled.'>';
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('shareholder/list', $data);
        }
    }

    public function add() {
        $data = $this->get_default_country_state();
        $shareholder_module_id = $this->config->item('shareholder_module');
        $data['module_id'] = $shareholder_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($shareholder_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $access_settings = $data['access_settings'];
        $primary_id = "id";
        $table_name = "tbl_shareholder";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('shareholder/add', $data);
    }   

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $shareholder_module_id = $this->config->item('shareholder_module');
        $data['module_id'] = $shareholder_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($shareholder_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'shar.*';
        $table = 'tbl_shareholder shar';
        $where = array('shar.id' => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");

       
        $this->load->view('shareholder/edit', $data);
    }

    public function edit_shareholder() {

        $shareholder_module_id = $this->config->item('shareholder_module');
        $data['module_id'] = $shareholder_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($shareholder_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('partner_id');
        $general_ledger = $this->config->item('general_ledger');     
        $partner_name = trim($this->input->post('partner_name'));
        $date_of_birth = date('Y-m-d', strtotime($this->input->post('txt_date_of_birth')));
        $address = $this->input->post('address');
        $pan_number = $this->input->post('txt_pan_number');
        $capital_amount = ($this->input->post('txt_capital_amount'))?$this->input->post('txt_capital_amount'):0;
        $eligible_claim = ($this->input->post('eligible_claim'))?$this->input->post('eligible_claim'):0;
       $remuneration = ($this->input->post('txt_remuneration'))?$this->input->post('txt_remuneration'):0;
        $roi_capital_intrest = ($this->input->post('txt_roi_capital_intrest'))?$this->input->post('txt_roi_capital_intrest'):0;
        $share_profit = ($this->input->post('txt_share_profit'))?$this->input->post('txt_share_profit'):0;
      $no_of_shares = ($this->input->post('txt_no_of_shares'))?$this->input->post('txt_no_of_shares'):0;
        $face_value = ($this->input->post('txt_face_value'))?$this->input->post('txt_face_value'):0;
        $security_premimum = ($this->input->post('txt_security_premimum'))?$this->input->post('txt_security_premimum'):0;
        $amount_paid_capital = ($this->input->post('txt_amount_paid_capital'))?$this->input->post('txt_amount_paid_capital'):0;
       
        if($this->input->post('txt_date_apptmnt') !=  ''){
            $date_apptmnt = date('Y-m-d', strtotime($this->input->post('txt_date_apptmnt')));
        }else{
            $date_apptmnt = '';
        }

        $type_director = $this->input->post('cmb_type_director');
        $partner_type = $this->input->post('partner_type');
        $type_share = $this->input->post('cmb_share_type');
        $partner_ledger_id = $this->input->post('ledger_id');

        if($this->input->post('partner_type') == 'partner'){
            
            $default_partner_id = $general_ledger['Partner'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_partner_id);

            if(!empty($partner_ledger_name)){
                $partner_ledger = $partner_ledger_name->ledger_name;                    
                $partner_ledger = str_ireplace('{{X}}',$partner_name, $partner_ledger);
            }
            $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$partner_ledger}' WHERE ledger_id='{$partner_ledger_id}'");

            
        }else if($this->input->post('partner_type') == 'director'){
           
            $default_partner_id = $general_ledger['Director'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_partner_id);
            
            if(!empty($partner_ledger_name)){
                $partner_ledger = $partner_ledger_name->ledger_name;                    
                $partner_ledger = str_ireplace('{{X}}',$partner_name, $partner_ledger);
            }
            $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$partner_ledger}' WHERE ledger_id='{$partner_ledger_id}'"); 
        }

         $sharholder_data = array(
            "sharholder_name" => $partner_name,
            "sharholder_code" => $this->input->post('partner_code'),
            "sharholder_type" => $this->input->post('partner_type'),
            "date_of_birth" => $date_of_birth,
            "sharholder_address" => $address,
            "sharholder_pan_number" => $pan_number, 
            "initial_capital_amount" => $capital_amount,  
            "eligible_to_claim" => $eligible_claim,
            "monthly_remuneration" => $remuneration,
            "rate_of_interest_capital" => $roi_capital_intrest,
            "percentage_share_of_profit" => $share_profit,
            "face_value_share" => $face_value,
            "security_premium" => $security_premimum,
            "no_of_shares" => $no_of_shares,
            "amount_paid_capital" => $amount_paid_capital,
            "date_of_appointment" => $date_apptmnt,
            "type_of_director" => $type_director,
            "type_of_share" => $type_share,
            "added_date" => date('Y-m-d'),
            "partner_ledger_id" => $partner_ledger_id,
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
            
        $table = "tbl_shareholder";
        
        $where = array("id" => $id);
        if ($this->general_model->updateData($table, $sharholder_data, $where)) {           
           if($this->input->post('partner_type') == 'director'){
               $type_input = "director loan";
               $this->updateOptionDirector($id,$partner_name,$type_input);
            }
            $type = $this->input->post('partner_type');
            $successMsg = $type.' Name Updated Successfully';           
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_shareholder',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Partner / Shareholder Updated');
                $this->general_model->insertData($table, $log_data);            

            redirect('share_holder', 'refresh');
        } else {            
            $type = $this->input->post('partner_type');
            $errorMsg = $type.' Name Updated Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'customer can not be Updated.');
            redirect("share_holder", 'refresh');
        }
    }

    public function add_shareholder() {
        $shareholder_module_id = $this->config->item('shareholder_module');
        $data['module_id'] = $shareholder_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($shareholder_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $general_ledger = $this->config->item('general_ledger');     
        $partner_name = trim($this->input->post('partner_name'));
        $date_of_birth = date('Y-m-d', strtotime($this->input->post('txt_date_of_birth')));
        $address = $this->input->post('address');
        $pan_number = $this->input->post('txt_pan_number');
        $capital_amount = ($this->input->post('txt_capital_amount'))?$this->input->post('txt_capital_amount'):0;
        $eligible_claim = ($this->input->post('eligible_claim'))?$this->input->post('eligible_claim'):0;
       $remuneration = ($this->input->post('txt_remuneration'))?$this->input->post('txt_remuneration'):0;
        $roi_capital_intrest = ($this->input->post('txt_roi_capital_intrest'))?$this->input->post('txt_roi_capital_intrest'):0;
        $share_profit = ($this->input->post('txt_share_profit'))?$this->input->post('txt_share_profit'):0;
      $no_of_shares = ($this->input->post('txt_no_of_shares'))?$this->input->post('txt_no_of_shares'):0;
        $face_value = ($this->input->post('txt_face_value'))?$this->input->post('txt_face_value'):0;
        $security_premimum = ($this->input->post('txt_security_premimum'))?$this->input->post('txt_security_premimum'):0;
        $amount_paid_capital = ($this->input->post('txt_amount_paid_capital'))?$this->input->post('txt_amount_paid_capital'):0;
       
        if($this->input->post('txt_date_apptmnt') !=  ''){
            $date_apptmnt = date('Y-m-d', strtotime($this->input->post('txt_date_apptmnt')));
        }else{
            $date_apptmnt = '';
        }

        $type_director = $this->input->post('cmb_type_director');
        $partner_type = $this->input->post('partner_type');
        $type_share = $this->input->post('cmb_share_type');
       if($this->input->post('partner_type') == 'partner'){
            
            $default_partner_id = $general_ledger['Partner'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_partner_id);
            
            $partner_ary = array(
                        'ledger_name' => $partner_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Capital',
                        'default_ledger_id' => $default_partner_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$partner_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $partner_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary);  
        }else if($this->input->post('partner_type') == 'director'){
           
            $default_partner_id = $general_ledger['Director'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_partner_id);
            
            $partner_ary = array(
                        'ledger_name' => $partner_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Loans (Liability)',
                        'default_ledger_id' => $default_partner_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$partner_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $partner_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary);  
        }else{
            $partner_ledger_id = '';
        }
           $sharholder_data = array(
            "sharholder_name" => $partner_name,
            "sharholder_code" => $this->input->post('partner_code'),
            "sharholder_type" => $this->input->post('partner_type'),
            "date_of_birth" => $date_of_birth,
            "sharholder_address" => $address,
            "sharholder_pan_number" => $pan_number, 
            "initial_capital_amount" => $capital_amount,  
            "eligible_to_claim" => $eligible_claim,
            "monthly_remuneration" => $remuneration,
            "rate_of_interest_capital" => $roi_capital_intrest,
            "percentage_share_of_profit" => $share_profit,
            "face_value_share" => $face_value,
            "security_premium" => $security_premimum,
            "no_of_shares" => $no_of_shares,
            "amount_paid_capital" => $amount_paid_capital,
            "date_of_appointment" => $date_apptmnt,
            "type_of_director" => $type_director,
            "type_of_share" => $type_share,
            "added_date" => date('Y-m-d'),
            "partner_ledger_id" => $partner_ledger_id,
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
        $table = "tbl_shareholder";
        if ($id = $this->general_model->insertData($table, $sharholder_data)) { 
            if($this->input->post('partner_type') == 'director'){
               $type_input = "director loan";
                $this->createOption_deposit($id,$partner_name,$type_input);
            }
            $type = $this->input->post('partner_type');
            $successMsg = $type.' Name Added Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_shareholder',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Partner / Shareholder Inserted');
            $this->general_model->insertData($table, $log_data);
           
        }else{    
            $type = $this->input->post('partner_type');        
            $errorMsg =  $type.' Name Add Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);
        }
        redirect("share_holder", 'refresh');
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $shareholder_module_id = $this->config->item('shareholder_module');
        $data['module_id'] = $shareholder_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($shareholder_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "tbl_shareholder";
        $data = array("delete_status" => 1);
        $where = array("id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
           $this->deleteOptionFixed($id);
            $successMsg = 'Partner / Shareholder Deleted Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_shareholder',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Partner / Shareholder Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('share_holder');
        } else {
            $errorMsg = 'Partner / Shareholder can not be Deleted.';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Partner / Shareholder can not be Deleted.');
            redirect("share_holder", 'refresh');
        }
    }

    public function PartnerValidation(){
        $partner_name = trim($this->input->post('partner_name'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT id FROM tbl_shareholder WHERE sharholder_name like '".$partner_name."' AND id != '{$id}' ")->num_rows();

        echo  json_encode(array('rows' => $rows ));
    }

    function createOption_deposit($id,$investment_name,$type_input){

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

            $deposit_option = str_ireplace('{{X}}',$investment_name, $deposit_option);
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

    function updateOptionDirector($id,$investment_name,$type_input){

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
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $user_id = $this->session->userdata('SESS_USER_ID');
             
        foreach ($result_option as $key1 => $value1) { 
            $deposit_option = $value1->customise_option;
            $parent_id = $value1->id;

            $deposit_option = str_ireplace('{{X}}',$investment_name, $deposit_option);
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
