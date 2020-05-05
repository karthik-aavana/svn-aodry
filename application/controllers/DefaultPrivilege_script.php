<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DefaultPrivilege_script extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
       // $id = 1;
        $this->db->select('branch_id');
        $this->db->from('branch');
        //$this->db->where('branch_id',$id);
        $res = $this->db->get();
        $result_branch = $res->result();
        foreach ($result_branch as $key => $value) {
            $branch_id = $value->branch_id;
             // insert Groups
            $this->db->query("INSERT INTO `groups` (`id`, `name`, `description`, `delete_status`, `branch_id`, `added_user_id`, `added_date`, `updated_user_id`, `updated_date`) VALUES (NULL, 'admin', 'Admin have all the privileges', '0', ".$branch_id.", '1', '0000-00-00', '0', '0000-00-00');");
                $group_id = $this->db->insert_id();

                $p = $this->db->select('m.*')->from('modules m')->where('m.delete_status', 0)->get();
                $module_bulk = $p->result();

                // insert Groups accessibility
            $data_item = array();
            foreach ($module_bulk as $key => $value) {
                $data_item[$key]['branch_id'] = $branch_id;
                $data_item[$key]['module_id'] = $value->module_id;                
                $data_item[$key]['group_id'] = $group_id;
                if($value->is_report == 1){
                    $data_item[$key]['add_privilege'] = 0;
                    $data_item[$key]['edit_privilege'] = 0;
                    $data_item[$key]['delete_privilege'] = 0;
                    $data_item[$key]['view_privilege'] = 1;
                }else{
                    $data_item[$key]['add_privilege'] = 1;
                    $data_item[$key]['edit_privilege'] = 1;
                    $data_item[$key]['delete_privilege'] = 1;
                    $data_item[$key]['view_privilege'] = 1;
                }
                $data_item[$key]['delete_status'] = 0;                
                $data_item[$key]['added_user_id'] = 1;
                $data_item[$key]['added_date'] = date("Y-m-d");
            }
            $this->db->insert_batch("group_accessibility", $data_item);

            // get all users in branch
            $this->db->select('id as user_id');
            $this->db->from('users');
            $this->db->where('branch_id',$branch_id);
            $this->db->where('delete_status',0);
            $res = $this->db->get();
            $result = $res->result();
            // insert group users
            if(!empty($result)){
                foreach ($result as $key => $va) {
                 $this->general_model->insertData("users_groups", [
                            "user_id" =>$va->user_id,
                            "group_id" => $group_id]);
                }
            }

           
           
        }

    }

    public function active_module() {
       // $id = 1;
        $this->db->select('branch_id');
        $this->db->from('branch');
        $res = $this->db->get();
        $result_branch = $res->result();
        foreach ($result_branch as $key => $value) {
            

            // get all users in branch
            $this->db->select('id as user_id');
            $this->db->from('users');
            $this->db->where('branch_id',$branch_id);
            $this->db->where('delete_status',0);
            $res = $this->db->get();
            $result = $res->result();
           
            // get all remaining modules
            $q = $this->db->select('m.*')->from('modules m')->where('m.delete_status', 0)->where('m.module_id NOT IN (select module_id from active_modules where delete_status=0 and branch_id=' . $branch_id . ' )', NULL, FALSE)->get();
            $remain_module = $q->result();
            $active_module = array();
            foreach ($remain_module as $key => $value) {
                $active_module[$key]['module_id'] = $value->module_id;
                $active_module[$key]['branch_id'] = $branch_id;
                $active_module[$key]['delete_status'] = 0;

            }
             // insert active modules
            $this->db->insert_batch("active_modules", $active_module);

           
        }

    }

    public function user_accessibility() {
       // $id = 1;
        $this->db->select('branch_id');
        $this->db->from('branch');
        $res = $this->db->get();
        $result_branch = $res->result();
        foreach ($result_branch as $key => $value) {
            // get all users in branch
            $this->db->select('id as user_id');
            $this->db->from('users');
            $this->db->where('branch_id',$branch_id);
            $this->db->where('delete_status',0);
            $res = $this->db->get();
            $result = $res->result();
           
            // get all remaining modules
            $q = $this->db->select('m.*')->from('modules m')->where('m.delete_status', 0)->where('m.module_id NOT IN (select module_id from active_modules where delete_status=0 and branch_id=' . $branch_id . ' )', NULL, FALSE)->get();
            $remain_module = $q->result();
            

            $data_item_user = array();
            if(!empty($result)){
                foreach ($result as $key => $va) {
                    foreach ($remain_module as $keys => $user_insert) {

                        $data_item_user[$keys]['branch_id'] = $branch_id;
                        $data_item_user[$keys]['module_id'] = $user_insert->module_id;
                        $data_item_user[$keys]['user_id'] = $va->user_id;
                        if($user_insert->is_report == 1){
                            $data_item_user[$keys]['add_privilege'] = 'no';
                            $data_item_user[$keys]['edit_privilege'] = 'no';
                            $data_item_user[$keys]['delete_privilege'] = 'no';
                            $data_item_user[$keys]['view_privilege'] = 'yes';
                        }else{
                            $data_item_user[$keys]['add_privilege'] = 'yes';
                            $data_item_user[$keys]['edit_privilege'] = 'yes';
                            $data_item_user[$keys]['delete_privilege'] = 'yes';
                            $data_item_user[$keys]['view_privilege'] = 'yes';
                        }
                        $data_item_user[$keys]['delete_status'] = 0;  
                    }
                    // insert user accessibility
                    $this->db->insert_batch("user_accessibility",$data_item_user);
                }
            }
        }

    }

}