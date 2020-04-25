<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Privilege extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    function index()
    {

    }

    function user()
    {
        $data["data"] = $this->general_model->getJoinRecords("users.*,branch.*,firm.*", "users", [
                'users.delete_status' => 0,
                'users.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ], [
                "branch" => "users.branch_id = branch.branch_id",
                "firm"   => "branch.firm_id = firm.firm_id" ]);
        $this->load->view("privileges/user_list", $data);
    }

    function update_user($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $branch_id                       = $this->session->userdata('SESS_BRANCH_ID');
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string                           = "user_accessibility.*,modules.module_name";
        $table                            = "user_accessibility";
        $join                             = [
                "modules" => "modules.module_id = user_accessibility.module_id" ];
        $where                            = [
                "user_accessibility.user_id"       => $id,
                "user_accessibility.branch_id"     => $branch_id,
                "user_accessibility.delete_status" => 0 ];
        $data['privilege_active_modules'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        $data['modules']                  = $this->general_model->getActiveRemianingModulesNew($id, $branch_id);
        $data['user_id']                  = $id;
        $this->load->view("privileges/user_add", $data);
    }

    function get_privilege($id)
    {
        /*$data = $this->general_model->getRecords('*', 'user_accessibility', array(
                'accessibility_id' => $id,
                'delete_status'    => 0 ));*/
        $list_data  = $this->common->get_user_accessibility_privilege_list_field($id);
        $posts      = $this->general_model->getPageJoinRecords($list_data);
        echo json_encode($posts);
    }
    function get_module_group_assigned_privilege()
    {
        $module_id = $this->input->post('module_id');
        $user_id = $this->input->post('user_id');

        $list_data           = $this->common->module_group_assigned_privilege($user_id,$module_id);

        $assigned_data       = $this->general_model->getPageJoinRecords($list_data);
       
        echo json_encode($assigned_data);
    }

    function update_privilege()
    {
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $acc_id                          = $this->input->post('acc_id');
        $privilege_data                  = array(
                "add_privilege"    => $this->input->post('add_privilege'),
                "edit_privilege"   => $this->input->post('edit_privilege'),
                "delete_privilege" => $this->input->post('delete_privilege'),
                "view_privilege"   => $this->input->post('view_privilege') );
        $data                            = $this->general_model->updateData('user_accessibility', $privilege_data, array(
                'accessibility_id' => $acc_id ));
        $successMsg = 'Privilege Updated Successfully';
        $this->session->set_flashdata('privilege_success',$successMsg);
        $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => 0,
                'table_name' => 'user_accessibility',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'User Accessibility Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        echo json_encode($data);
    }

    public function update_user_data()
    {
        $privilege_module_id             = $this->config->item('privilege_module');
        $data['module_id']               = $privilege_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($privilege_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $access_array                    = array(
                "branch_id"        => $this->session->userdata('SESS_BRANCH_ID'),
                "user_id"          => $this->input->post('user_id'),
                "add_privilege"    => $this->input->post('add') != "" ? 'yes' : 'no',
                "edit_privilege"   => $this->input->post('edit') != "" ? 'yes' : 'no',
                "delete_privilege" => $this->input->post('delete') != "" ? 'yes' : 'no',
                "view_privilege"   => $this->input->post('view') != "" ? 'yes' : 'no',
                "module_id"        => $this->input->post('module_id'), );
        $id                        = $this->general_model->insertData('user_accessibility', $access_array);
        $successMsg = 'Module Added Successfully';
        $this->session->set_flashdata('module_add',$successMsg);
        $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'user_accessibility',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'User Accessibility Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        $user_id                         = $this->encryption_url->encode($this->input->post('user_id'));
        redirect('privilege/update_user/' . $user_id, 'refresh');
    }

}
