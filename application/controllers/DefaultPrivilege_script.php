<?php

defined('BASEPATH') or exit('No direct script access allowed');

class DefaultPrivilege_script extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index() {
    	/*$this->db->select('branch_id');
    	$this->db->from('branch');
    	$res = $this->db->get();
    	$result = $res->result();*/
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $this->db->select('id as user_id');
        $this->db->from('users');
        $this->db->where('branch_id',$branch_id);
        $this->db->where('delete_status',0);
        $res = $this->db->get();
        $result = $res->result();

        /*********************group ******************************/
        $this->db->query("INSERT INTO `groups` (`id`, `name`, `description`, `delete_status`, `branch_id`, `added_user_id`, `added_date`, `updated_user_id`, `updated_date`) VALUES (NULL, 'admin', 'Admin have all the privileges', '0', ".$branch_id.", '1', '0000-00-00', '0', '0000-00-00');");

        $this->db->select('max(id) as group_id');
        $this->db->from('groups');
        $this->db->where('branch_id',$branch_id);
        $this->db->where('delete_status',0);
        /*
        echo '<pre>';
            print_r($user);
            exit();*/
        $grp = $this->db->get();
        $grp_result = $grp->result();
        $group_id = $grp_result[0]->group_id;
        
        /*****************group accessibility ******************/
        $p = $this->db->select('m.*')->from('modules m')->where('m.delete_status', 0)->get();
        $module_bulk = $p->result();

        $active_module = array();
        $data_item = array();
        foreach ($module_bulk as $key => $value) {
            $data_item[$key]['branch_id'] = $branch_id;
            $data_item[$key]['module_id'] = $value->module_id;
            $active_module[$key]['module_id'] = $value->module_id;
            $active_module[$key]['branch_id'] = $branch_id;
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
            $active_module[$key]['delete_status'] = 0;
            $data_item[$key]['added_user_id'] = 1;
            $data_item[$key]['added_date'] = date("Y-m-d");
        }

        foreach ($active_module as $values) {
            $this->general_model->insertData("active_modules", $values);
        }

        foreach ($data_item as $val) {
            $this->general_model->insertData("group_accessibility", $val);
        }

        $data_item_user = array();
        if(!empty($result)){
            foreach ($result as $key => $va) {
                foreach ($module_bulk as $keys => $user_insert) {
                    /***************user accessibility*******************/
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
                /****************user group*****************/
                $this->general_model->insertData("users_groups", [
                        "user_id" =>$va->user_id,
                        "group_id" => $group_id]);

                foreach ($data_item_user as $var){
                  $this->general_model->insertData("user_accessibility",$var);
                }
            }
        }
    }
}