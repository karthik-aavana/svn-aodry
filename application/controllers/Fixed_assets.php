<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Fixed_assets extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
    }

    public function index() {
        $fixed_assets_id = $this->config->item('fixed_assets_module');
        $data['fixed_assets_id'] = $fixed_assets_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($fixed_assets_id, $modules, $privilege);

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
            $list_data = $this->common->fixed_asset_list_field();
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
            $array_tpe = array();
            $array_tpe['plant & machinery'] = "Plant & Machinery";
            $array_tpe['vechicle'] = "Vechicle";
            $array_tpe['furniture'] = "Furniture";
            $array_tpe['computers and accessories'] = "Computers and Accessories";
            $array_tpe['office equipments'] =  "Office Equipments";
            $array_tpe['software'] = "Software";
            $array_tpe['trademark'] = "Trademark";
            $array_tpe['patent'] = "Patent";
            $array_tpe['goodwill'] = "Goodwill";
            $array_tpe['building'] = "Building";
            $array_tpe['land'] = "Land";
            $array_tpe['jewellery'] = "Jewellery";
            $array_tpe['electrical items'] = "Electrical Items";
       
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $shareholder_id = $this->encryption_url->encode($post->fixed_assets_id);
                    $nestedData['particulars'] =  $array_tpe[$post->particulars];
                    $nestedData['name_of_assets_purchase'] = $post->name_of_assets_purchase;
                    $nestedData['rate_depreciation_income_tax'] = $post->rate_depreciation_income_tax;
                    $nestedData['rate_depreciation_company_act'] = round($post->rate_depreciation_company_act,2);
                    $nestedData['date_of_use'] = date('d-m-Y', strtotime($post->date_of_use));
                    $nestedData['date_purchase'] =  date('d-m-Y', strtotime($post->date_purchase));
                    
                    $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['fixed_assets_id'], $data['active_edit'])) {
                        $cols .= '<span><a href="' . base_url('fixed_assets/edit/') . $shareholder_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['fixed_assets_id'], $data['active_delete'])) {  
                        $ledger_id = $post->ledger_id;  
                        $this->db->select('ledger_id');
                        $this->db->from('accounts_journal_voucher');
                        $this->db->where('ledger_id',$ledger_id);
                        $sup = $this->db->get();
                        $result_option = $sup->result();  
                        if(empty($result_option)){                        
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $shareholder_id . '" data-path="fixed_assets/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                       }
                    }
                    $cols .= '</div></div>';
                    $disabled = '';
                    if(!in_array($data['fixed_assets_id'], $data['active_delete']) && !in_array($data['fixed_assets_id'], $data['active_edit'])){
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
            $this->load->view('fixed_assets/list', $data);
        }
    }

    public function add() {
        
        $fixed_assets_id = $this->config->item('fixed_assets_module');
        $data['module_id'] = $fixed_assets_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($fixed_assets_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "fixed_assets_id";
        $table_name = "tbl_fixed_assets";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $data['invoice_number'] = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $this->load->view('fixed_assets/add', $data);
    }   

    public function edit($id) {
        $id = $this->encryption_url->decode($id);
        $fixed_assets_id = $this->config->item('fixed_assets_module');
        $data['module_id'] = $fixed_assets_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($fixed_assets_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $string = 'shar.*';
        $table = 'tbl_fixed_assets shar';
        $where = array('shar.fixed_assets_id' => $id);
        $data['data'] = $this->general_model->getRecords($string, $table, $where, $order = "");
        $array_tpe = array();
        $array_tpe['plant & machinery'] = "Plant & Machinery";
        $array_tpe['vechicle'] = "Vechicle";
        $array_tpe['furniture'] = "Furniture";
        $array_tpe['computers and accessories'] = "Computers and Accessories";
        $array_tpe['office equipments'] =  "Office Equipments";
        $array_tpe['software'] = "Software";
        $array_tpe['trademark'] = "Trademark";
        $array_tpe['patent'] = "Patent";
        $array_tpe['goodwill'] = "Goodwill";
        $array_tpe['building'] = "Building";
        $array_tpe['land'] = "Land";
        $array_tpe['jewellery'] = "Jewellery";
        $array_tpe['electrical items'] = "Electrical Items";
        $data['type']  = $array_tpe;
        $data['bank_account']     = $this->bank_account_call_new();
        $this->load->view('fixed_assets/edit', $data);
    }

    public function edit_fixed_assets() {

        $fixed_assets_id = $this->config->item('fixed_assets_module');
        $data['module_id'] = $fixed_assets_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules = $this->get_section_modules($fixed_assets_id, $modules, $privilege);

        /* presents all the needed */
            $data = array_merge($data, $section_modules);

            $id = $this->input->post('fixed_assets_id');
            $assets_type = trim($this->input->post('cmb_assets_type'));
            $asset_name = trim($this->input->post('txt_asset_name'));       
            $general_ledger = $this->config->item('general_ledger');
            $ledger_id = $this->input->post('ledger_id');
            
            $default_fixed_id = $general_ledger['Fixed_Assets'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
           if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;  
                    $sub_group = $partner_ledger_name->sub_group_1; 
                    $partner_ledger = str_ireplace('{{X}}',$asset_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $primary_grp  = str_ireplace('{{X}}',$assets_type, $sub_group);
                    $main_grp = $partner_ledger_name->main_group;
            }

            

            $string = 'shar.*';
            $table = 'tbl_sub_group shar';
            $where = array('shar.sub_group_name_1' => $primary_grp);

            $ledger_det = $this->general_model->getRecords($string, $table, $where, $order = "");
         
             if(!empty($ledger_det)){
                 $sub_grp_id = $ledger_det[0]->sub_grp_id;
             }else{
                    $main_grp_id  = $this->db->select('main_grp_id')->from('tbl_main_group')->like('LOWER(grp_name)', strtolower($main_grp), 'none')->get()->row();
              
                  if(!$main_grp_id){
                    $main_grp_id = 5;
                  }else{
                    $main_grp_id = $main_grp_id->main_grp_id;
                  }

                  $subgroup_1 = $primary_grp;
                  $subgroup_2 = '';

              /* Check sub group and take a Id from default table */

              $sub_grp_id = $this->db->select('sub_grp_id')->from('tbl_sub_group s')->join('tbl_ledgers l','s.sub_grp_id=l.sub_group_id','left')->where('main_grp_id',$main_grp_id)->where('(LOWER(sub_group_name_1) = "'.strtolower($subgroup_1).'" AND LOWER(sub_group_name_2)="'.strtolower($subgroup_2).'") ')->where('l.sub_group_id IS NULL')->get()->row();
              if(!$sub_grp_id){
                $add_sub = $this->db->insert('tbl_sub_group',array('main_grp_id' => $main_grp_id,'sub_group_name_1'=>$subgroup_1,'sub_group_name_2'=>$subgroup_2,'group_status'=>'1','is_editable'=>'1','branch_id'=> $this->session->userdata('SESS_BRANCH_ID')));
                $sub_grp_id = $this->db->insert_id();
              }else{
                $sub_grp_id = $sub_grp_id->sub_grp_id;
              }
             }
            
            $this->db->query("UPDATE tbl_ledgers SET ledger_name='{$partner_ledger}', sub_group_id='{$sub_grp_id}' WHERE ledger_id='{$ledger_id}'");
          
            $fixed_assets_data = array(
                "fixed_assets_code" => $this->input->post('fixed_assets_code'),
                "particulars" => $assets_type,
                "name_of_assets_purchase" => $asset_name,
                "date_purchase" => date('Y-m-d', strtotime($this->input->post('txt_date_of_purchase'))),
                "date_of_use" => date('Y-m-d', strtotime($this->input->post('txt_date_of_asset_put'))),
                "rate_depreciation_income_tax" => $this->input->post('txt_rate_of_depr_it'),
                "rate_depreciation_company_act" => $this->input->post('txt_rate_of_depr_comp_act'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date" => date('Y-m-d'),
                "ledger_id" => $ledger_id,
                "updated_user_id" => $this->session->userdata('SESS_USER_ID') 
            );
            
        $table = "tbl_fixed_assets";
        
        $where = array("fixed_assets_id" => $id);
        if ($this->general_model->updateData($table, $fixed_assets_data, $where)) {
            $type_input = 'fixed asset';           
           $this->updateOptionFixed($id,$asset_name,$type_input);
            $successMsg = 'Fixed Assets Updated Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_fixed_assets',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Fixed Assets Updated');
                $this->general_model->insertData($table, $log_data);            

            redirect('fixed_assets', 'refresh');
        } else {
            $errorMsg = 'Fixed Assets Update Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);           
            redirect("fixed_assets", 'refresh');
        }
    }

    public function add_fixed_assets() {
        $fixed_assets_id = $this->config->item('fixed_assets_module');
        $data['module_id'] = $fixed_assets_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($fixed_assets_id, $modules, $privilege);

        /* presents all the needed */
            $data = array_merge($data, $section_modules); 
            $assets_type = trim($this->input->post('cmb_assets_type'));
            $asset_name = trim($this->input->post('txt_asset_name'));       

            $general_ledger = $this->config->item('general_ledger');
            
            $default_fixed_id = $general_ledger['Fixed_Assets'];
            $partner_ledger_name = $this->ledger_model->getDefaultLedgerId($default_fixed_id);
            
            $partner_ary = array(
                        'ledger_name' => $asset_name,
                        'second_grp' => '',
                        'primary_grp' => $assets_type,
                        'main_grp' => 'Fixed Assets',
                        'default_ledger_id' =>$default_fixed_id,
                        'default_value' => 0,
                        'amount' => 0
                    );
                if(!empty($partner_ledger_name)){
                    $partner_ledger = $partner_ledger_name->ledger_name;    
                    $sub_group = $partner_ledger_name->sub_group_1;                
                    $partner_ledger = str_ireplace('{{X}}',$asset_name, $partner_ledger);
                    $partner_ary['ledger_name'] = $partner_ledger;
                    $partner_ary['primary_grp'] = str_ireplace('{{X}}',$assets_type, $sub_group);
                    $partner_ary['second_grp'] = $partner_ledger_name->sub_group_2;
                    $partner_ary['main_grp'] = $partner_ledger_name->main_group;
                    $partner_ary['default_ledger_id'] = $partner_ledger_name->ledger_id;
                }
            
            $fixed_assets_ledger_id = $this->ledger_model->getGroupLedgerId($partner_ary); 
            $fixed_assets_data = array(
                "fixed_assets_code" => $this->input->post('fixed_assets_code'),
                "particulars" => $assets_type,
                "name_of_assets_purchase" => $asset_name,
                "date_purchase" => date('Y-m-d', strtotime($this->input->post('txt_date_of_purchase'))),
                "date_of_use" => date('Y-m-d', strtotime($this->input->post('txt_date_of_asset_put'))),
                "rate_depreciation_income_tax" => $this->input->post('txt_rate_of_depr_it'),
                "rate_depreciation_company_act" => $this->input->post('txt_rate_of_depr_comp_act'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                "added_date" => date('Y-m-d'),
                "ledger_id" => $fixed_assets_ledger_id,
                "added_user_id" => $this->session->userdata('SESS_USER_ID') 
            );
            
        $table = "tbl_fixed_assets";
        $id = $this->general_model->insertData($table, $fixed_assets_data);
        if ($id) {            
            $type_input = 'fixed asset';
            $this->createOption_fixed_assets($id,$asset_name,$type_input);
            $successMsg = 'Fixed Assets Added Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $table = "log";
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_fixed_assets',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Fixed Assets Inserted');
            $this->general_model->insertData($table, $log_data);
           
        }else{
            $errorMsg = 'Fixed Assets Add Unsuccessful';
            $this->session->set_flashdata('partner_error',$errorMsg);
        }
         redirect('fixed_assets', 'refresh');
    }

    public function delete() {
        $id = $this->input->post('delete_id');
        $fixed_assets_id = $this->config->item('fixed_assets_module');
        $data['module_id'] = $fixed_assets_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($fixed_assets_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        $table = "tbl_fixed_assets";
        $data = array("delete_status" => 1);
        $where = array("fixed_assets_id" => $id);
        if ($this->general_model->updateData($table, $data, $where)) {
           $type_input = 'fixed asset';           
           $this->deleteOptionFixed($id,$type_input);
            $successMsg = 'Fixed Assets Deleted Successfully';
            $this->session->set_flashdata('partner_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'tbl_fixed_assets',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Fixed Assets Deleted');
            $table = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('fixed_assets');
        } else {
            $errorMsg = 'Fixed Assets can not be Deleted.';
            $this->session->set_flashdata('partner_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Partner / Shareholder can not be Deleted.');
            redirect("fixed_assets", 'refresh');
        }
    }

    public function FixedAssetsValidation(){
        $deposit_name = trim($this->input->post('fixed_asset_name'));
        $id = $this->input->post('id');
        
        $rows = $this->db->query("SELECT fixed_assets_id FROM tbl_fixed_assets WHERE  name_of_assets_purchase like '".$deposit_name."' AND fixed_assets_id != '{$id}' ")->num_rows();

        echo  json_encode(array('rows' => $rows ));
    }


    

    function createOption_fixed_assets($id,$fixed_asset_name,$type_input){
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
           $i = 1;     
        foreach ($result_option as $key1 => $value1) { 
            $deposit_option = $value1->customise_option;
            $parent_id = $value1->id;

            $deposit_option = str_ireplace('{{X}}',$fixed_asset_name, $deposit_option);
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

    function updateOptionFixed($id,$deposit_name,$type_input){
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


}
