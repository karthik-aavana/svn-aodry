<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Investments extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
        //gg
    }

    public function index() {
        $investments_module_id = $this->config->item('investments_module');
        $data['investments_module_id'] = $investments_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($investments_module_id, $modules, $privilege);

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
            $list_data = $this->common->investment_list_field();
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
                    $shareholder_id = $this->encryption_url->encode($post->investments_id);
                    $nestedData['investments_code'] = $post->investments_code;
                    $nestedData['investments_type'] = $post->investments_type;
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['investments_module_id'], $data['active_edit'])) {
                       /* $cols .= '<span><a href="' . base_url('investments/edit/') . $shareholder_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';*/

                       $cols .= '<span data-toggle="modal" data-target="#edit_investments_modal" data-backdrop="static" data-keyboard="false"><a data-id="' . $shareholder_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_investment btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['investments_module_id'], $data['active_delete'])) {  $ledger_id =$post->ledger_id;  
                        $this->db->select('ledger_id');
                        $this->db->from('accounts_journal_voucher');
                        $this->db->where('ledger_id',$ledger_id);
                        $sup = $this->db->get();
                        $result_option = $sup->result();  
                        if(empty($result_option)){                        
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $shareholder_id . '" data-path="investments/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                       
                    }
                    $cols .= '</div></div>';
                    $disabled = '';
                    if(!in_array($data['investments_module_id'], $data['active_delete']) && !in_array($data['investments_module_id'], $data['active_edit'])){
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
            $this->load->view('investments/list', $data);
        }
    }

    public function add() {
        
        $investments_module_id = $this->config->item('investments_module');
        $data['module_id'] = $investments_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($investments_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "investments_id";
        $table_name = "tbl_investments";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('investments/add', $data);
    }   

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $investments_module_id = $this->config->item('investments_module');
        $data['module_id'] = $investments_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($investments_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'shar.*';
        $table = 'tbl_investments shar';
        $where = array('shar.investments_id' => $id);
      $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        $this->load->view('investments/edit', $data);
    }

    public function edit_investments() {

        $investments_module_id = $this->config->item('investments_module');
        $data['module_id'] = $investments_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($investments_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('investment_id');
        $ledger_id = $this->input->post('ledger_id');
        $investment_name = trim($this->input->post('investment_name'));
        $general_ledger = $this->config->item('general_ledger');
        $default_investment_id = $general_ledger['Investments']; 
        $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_investment_id);
     
        if(!empty($partner_ledger_name)){
            $investment_ledger = $partner_ledger_name->ledger_name;
            $investment_ledger_name = str_ireplace('{{X}}',$investment_name, $investment_ledger);
        }
        $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$investment_ledger_name}' WHERE ledger_id='{$ledger_id}'");

            $investment_data = array(
                "investments_code" =>  $this->input->post('investment_code'),
                "investments_type" => $this->input->post('investment_name'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => date('Y-m-d'),
                "ledger_id" => $ledger_id,
                "updated_user_id" => $this->session->userdata('SESS_USER_ID') 
            );

        $table = "tbl_investments";
        
        $where = array("investments_id" => $id);
        if ($this->general_model->updateData($table, $investment_data, $where)) {     
            $type_input = 'mature';
            $type_input1 = 'redeem';
            $type_input2 = 'sold';
            $type_input3 = 'withdraw';
            $type_input4 = 'made';      
           $this->updateOptionInvestments($id,$investment_name,$type_input);
           $this->updateOptionInvestments($id,$investment_name,$type_input1);
           $this->updateOptionInvestments($id,$investment_name,$type_input2);
           $this->updateOptionInvestments($id,$investment_name,$type_input3);
           $this->updateOptionInvestments($id,$investment_name,$type_input4);
            $successMsg = 'Investments Updated Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_investments',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Investments Updated');
                $this->general_model->insertData($table, $log_data);   
            $result['flag'] = true;
            $result['msg'] = $successMsg;         

            //redirect('investments', 'refresh');
        } else {
            $errorMsg = 'Investments Update Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Investments can not be Updated.');
            $result['flag'] = false;
            $result['msg'] = $errorMsg;
           // redirect("investments", 'refresh');
        }
        echo json_encode($result);
    }

    public function add_investments() {
        $investments_module_id = $this->config->item('investments_module');
        $data['module_id'] = $investments_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($investments_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
            
        $investment_name = trim($this->input->post('investment_name'));
            

        $general_ledger = $this->config->item('general_ledger');
        
            
            $default_investment_id = $general_ledger['Investments']; 
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_investment_id);
            
            $partner_ary = array(
                        'ledger_name' => $investment_name,
                        'second_grp' => '',
                        'primary_grp' => '',
                        'main_grp' => 'Investments',
                        'default_ledger_id' => $default_investment_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;                    
                    $partner_ledger = str_ireplace('{{X}}',$investment_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = $partner_ledger_name->sub_group_1;
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
                $deposit_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary); 
        
           

            $deposit_data = array(
                "investments_code" =>  $this->input->post('investment_code'),
                "investments_type" => $this->input->post('investment_name'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "added_date" => date('Y-m-d'),
                "ledger_id" => $deposit_ledger_id,
                "added_user_id" => $this->session->userdata('SESS_USER_ID') 
            );
            
        $table = "tbl_investments";
        $id = $this->general_model->insertData($table, $deposit_data);
        if ($id) {            
            $type_input = 'mature';
            $type_input1 = 'redeem';
            $type_input2 = 'sold';
            $type_input3 = 'withdraw';
            $type_input4 = 'made';
            $this->createOption_deposit($id,$investment_name,$type_input);
            $this->createOption_deposit($id,$investment_name,$type_input1);
            $this->createOption_deposit($id,$investment_name,$type_input2);
            $this->createOption_deposit($id,$investment_name,$type_input3);
            $this->createOption_deposit($id,$investment_name,$type_input4);
            
            $successMsg = 'Investments Added Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_investments',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Investments Inserted');
            $this->general_model->insertData($table, $log_data);
           $result['flag'] = true;
           $result['msg'] = $successMsg;
        }else{
            $errorMsg = 'Investments Add Unsuccessful';
            $result['flag'] = false;
            $result['msg'] = $errorMsg;
            $this->session->set_flashdata('partner_error',$errorMsg);
        }
        //redirect("investments", 'refresh');
        echo json_encode($result);
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $investments_module_id = $this->config->item('investments_module');
        $data['module_id'] = $investments_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($investments_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "tbl_investments";
        $data = array("delete_status" => 1);
        $where = array("investments_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
           $type_input = 'mature';
            $type_input1 = 'redeem';
            $type_input2 = 'sold';
            $type_input3 = 'withdraw';
            $type_input4 = 'made';      
           $this->deleteOptionFixed($id,$type_input);
           $this->deleteOptionFixed($id,$type_input1);
           $this->deleteOptionFixed($id,$type_input2);
           $this->deleteOptionFixed($id,$type_input3);
           $this->deleteOptionFixed($id,$type_input4);
            $successMsg = 'Investments Deleted Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_investments',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Investments Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('investments');
        } else {
            $errorMsg = 'Investments can not be Deleted.';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Investments can not be Deleted.');
            redirect("investments", 'refresh');
        }
    }

    public function investmentValidation(){
        $investment_name = trim($this->input->post('investment_name'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT investments_id FROM tbl_investments WHERE investments_type like '".$investment_name."' AND investments_id != '{$id}' ")->num_rows();

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

    function updateOptionInvestments($id,$investment_name,$type_input){
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

    function deleteOptionFixed($id,$type_input){
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $this->db->select('customise_option,id');
        $this->db->from('tbl_transaction_purpose');
        $this->db->where('input_type',$type_input);
         $this->db->where('branch_id',$branch_id);
        $sup = $this->db->get();
        $result_option = $sup->result();
        $option_array = array();    
        $date = date('Y-m-d');        
        $user_id = $this->session->userdata('SESS_USER_ID');             
        foreach ($result_option as $key1 => $value1) { 
            $deposit_option = $value1->customise_option;
            $parent_id = $value1->id;           
            $option_array['delete_status'] = 1; 
            $where = array("payee_id" => $id,"parent_id"=>$parent_id);
             $table = "tbl_transaction_purpose_option";
            $this->general_model->updateData($table, $option_array, $where);
        }
    }

     public function get_investmentcode() {
        $investments_module_id = $this->config->item('investments_module');
        $data['module_id'] = $investments_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($investments_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "investments_id";
        $table_name = "tbl_investments";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $invoice_number = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $result['invoice_number']= $invoice_number;
        echo json_encode($result);
    }

    public function get_investment($id)
    {
        $id                   = $this->encryption_url->decode($id);
        $investments_module_id   = $this->config->item('investments_module');
        $data['module_id']    = $investments_module_id;
        $modules              = $this->modules;
        $privilege            = "edit_privilege";
        $data['privilege']    = "edit_privilege";
        $section_modules      = $this->get_section_modules($investments_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data = $this->general_model->getRecords('*', 'tbl_investments', array(
                'investments_id'   => $id,
                'delete_status' => 0 ));
        echo json_encode($data);
    }


}
