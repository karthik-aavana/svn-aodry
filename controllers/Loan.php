<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Loan extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
        //gg
    }

    public function index() {
        $loan_module_id = $this->config->item('loan_module');
        $data['loan_module_id'] = $loan_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($loan_module_id, $modules, $privilege);

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
            $list_data = $this->common->loan_list_field();
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
                    $shareholder_id = $this->encryption_url->encode($post->loan_id);

                    $nestedData['loan_type'] = $post->loan_type;                    
                    $nestedData['other_staus'] = ($post->other_staus)?$post->other_staus:'NA';
                    $nestedData['comments'] = $post->comments;
                    $nestedData['others_name'] = ($post->others_name)?$post->others_name:'NA';
                    $nestedData['loan_bank'] = ($post->loan_bank)?$post->loan_bank:'NA';
                    $nestedData['pan'] = ($post->pan)?$post->pan:'NA';
                    $nestedData['rate_of_interest'] = ($post->rate_of_interest)?$post->rate_of_interest:'NA';                    
                    $nestedData['loan_date'] =  date('d-m-Y', strtotime($post->loan_date));
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['loan_module_id'], $data['active_edit'])) {
                        $cols .= '<span><a href="' . base_url('loan/edit/') . $shareholder_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['loan_module_id'], $data['active_delete'])) {      
                        $ledger_id = $post->ledger_id;  
                        $this->db->select('ledger_id');
                        $this->db->from('accounts_journal_voucher');
                        $this->db->where('ledger_id',$ledger_id);
                        $sup = $this->db->get();
                        $result_option = $sup->result();  
                        if(empty($result_option)){                   
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $shareholder_id . '" data-path="loan/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
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
            $this->load->view('loan/list', $data);
        }
    }

    public function add() {
        
        $loan_module_id = $this->config->item('loan_module');
        $data['module_id'] = $loan_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($loan_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "loan_id";
        $table_name = "tbl_loans";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['bank_account']     = $this->bank_account_call_new();
        $this->load->view('loan/add', $data);
    }   

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $loan_module_id = $this->config->item('loan_module');
        $data['module_id'] = $loan_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($loan_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'shar.*';
        $table = 'tbl_loans shar';
        $where = array('shar.loan_id' => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        $status = array();
        $status["relative"] = 'Individual - relative';
        $status["non-relative"] = 'Individual - Not a Relative';
        $status["firm"] = 'Firm';
        $status["corporate"] = 'Body Corporate';
        $status["llp"] = 'LLP';
        $status["nbfc"] = 'NBFC';
        $status["others"] = 'Others';
        $data['status'] = $status;
        $data['bank_account']     = $this->bank_account_call_new();
        $this->load->view('loan/edit', $data);
    }

    public function edit_loan() {

        $loan_module_id = $this->config->item('loan_module');
        $data['module_id'] = $loan_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($loan_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('loan_id');
       if($this->input->post('cmb_loan_type') == 'others'){  
            $type = $this->input->post('cmb_loan_type');     
            $loan_name = trim($this->input->post('txt_loan_name'));
            $type_input = 'others loan';
        }else{
            $type = $this->input->post('cmb_loan_type');
            $loan_name = trim($this->input->post('cmb_bank'));
            $type_input = 'bank loan';           
        }  

        $ledger_id = $this->input->post('ledger_id');        
        $general_ledger = $this->config->item('general_ledger');
        $default_fixed_id = $general_ledger['Director'];
        $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
     
        if(!empty($partner_ledger_name)){
            $investment_ledger = $partner_ledger_name->ledger_name;
            $investment_ledger_name = str_ireplace('{{X}}',$loan_name, $investment_ledger);
        }
        $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$investment_ledger_name}' WHERE ledger_id='{$ledger_id}'");

            $loan_data = array(
                "loan_code" =>  $this->input->post('loan_code'),
                "loan_type" =>  $this->input->post('cmb_loan_type'),
                "other_staus" => $this->input->post('cmb_status'),
                "loan_date" => date('Y-m-d', strtotime($this->input->post('txt_date_of_loan'))),
                "loan_bank" => $this->input->post('cmb_bank'),
                "others_name" => $this->input->post('txt_loan_name'),
                "comments" => $this->input->post('comments'),
                "pan" => $this->input->post('txt_pan'),
                "rate_of_interest" => $this->input->post('txt_roi'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => date('Y-m-d'),
                "ledger_id" => $ledger_id,
                "updated_user_id" => $this->session->userdata('SESS_USER_ID') 
            );
            

        $table = "tbl_loans";
        
        $where = array("loan_id" => $id);
        if ($this->general_model->updateData($table, $loan_data, $where)) {           
           $this->updateOption_deposit($id,$loan_name,$type_input);
            $successMsg = 'Loan Updated Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'loan_id',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Loan Updated');
                $this->general_model->insertData($table, $log_data);            

            redirect('loan', 'refresh');
        } else {
            $errorMsg = 'Loan Update Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);           
            redirect("loan", 'refresh');
        }
    }

    public function add_loan() {
        $loan_module_id = $this->config->item('loan_module');
        $data['module_id'] = $loan_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($loan_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        if($this->input->post('cmb_loan_type') == 'others'){  
            $type = $this->input->post('cmb_loan_type');     
            $loan_name = trim($this->input->post('txt_loan_name'));
            $type_input = 'others loan';
        }else{
            $type = $this->input->post('cmb_loan_type');
            $loan_name = trim($this->input->post('cmb_bank'));
            $type_input = 'bank loan';           
        }

        $general_ledger = $this->config->item('general_ledger');
                  
            $default_fixed_id = $general_ledger['Director'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            $partner_ary = array(
                        'ledger_name' => $loan_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Loans (Liability)',
                        'default_ledger_id' => $default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$loan_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
            
            $loan_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary); 

            $loan_data = array(
                "loan_code" =>  $this->input->post('loan_code'),
                "loan_type" =>  $this->input->post('cmb_loan_type'),
                "other_staus" => $this->input->post('cmb_status'),
                "loan_date" => date('Y-m-d', strtotime($this->input->post('txt_date_of_loan'))),
                "loan_bank" => $this->input->post('cmb_bank'),
                "others_name" => $this->input->post('txt_loan_name'),
                "comments" => $this->input->post('comments'),
                "pan" => $this->input->post('txt_pan'),
                "rate_of_interest" => $this->input->post('txt_roi'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "added_date" => date('Y-m-d'),
                "ledger_id" => $loan_ledger_id,
                "added_user_id" => $this->session->userdata('SESS_USER_ID') 
            );
            

        $table = "tbl_loans";
        $id = $this->general_model->insertData($table, $loan_data);
        if ($id) {            
            $this->createOption_deposit($id,$loan_name,$type_input);
           
            $successMsg = 'Loans Added Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_loans',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Loans Inserted');
            $this->general_model->insertData($table, $log_data);
           
        }else{
            $errorMsg = 'Loans Add Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);
        }
        redirect("loan", 'refresh');
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $loan_module_id = $this->config->item('loan_module');
        $data['module_id'] = $loan_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($loan_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "tbl_loans";
        $data = array("delete_status" => 1);
        $where = array("loan_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
           $this->deleteOptionFixed($id);
            $successMsg = 'Loans Deleted Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_shareholder',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Loans Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('loan');
        } else {
            $errorMsg = 'Loans can not be Deleted.';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Loans can not be Deleted.');
            redirect("loan", 'refresh');
        }
    }

    public function LoanValidation(){
        $loan_name = trim($this->input->post('loan_name'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT loan_id FROM tbl_loans WHERE  others_name like '".$loan_name."' AND loan_id != '{$id}' ")->num_rows();

        echo  json_encode(array('rows' => $rows ));
    }


    public function BankValidation(){
        $loan_bank = trim($this->input->post('loan_bank'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT loan_id FROM tbl_deposit WHERE loan_bank like '".$deposit_bank."' AND loan_id != '{$id}' ")->num_rows();

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
