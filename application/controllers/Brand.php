<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model(['general_model' ]);
        $this->load->library('SSP');
        $this->modules = $this->get_modules();
    }

    function index(){
        $brand_module_id         = $this->config->item('brand_module');
        $data['brand_module_id'] = $brand_module_id;
        $modules                     = $this->modules;
        $privilege                   = "view_privilege";
        $data['privilege']           = $privilege;
        $section_modules             = $this->get_section_modules($brand_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        foreach ($modules['modules'] as $key => $value){
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }
        }
        $this->load->view('brand/list', $data);   
    }

    public function getBrandList(){
        $table = 'brand';
        $primaryKey = 'brand_id';
        $columns = array(
            array('db' => 'brand_id', 'dt' => 'brand_id'),
            array('db' => 'brand_name', 'dt' => 'brand_name'),
            array('db' => 'brand_invoice_first_prefix', 'dt' => 'invoice_first_prefix'),
            array('db' => 'brand_invoice_last_prefix', 'dt' => 'invoice_last_prefix'),
            /*array('db' => 'invoice_seperation', 'dt' => 'invoice_seperation'),*/
            array('db' => 'invoice_type', 'dt' => 'invoice_type'),
            array('db' => 'invoice_creation', 'dt' => 'invoice_creation'),
            array('db' => 'invoice_readonly', 'dt' => 'invoice_readonly'),
            array('db' => 'brand_id', 'dt' => 'action', 'formatter' => function($d, $row) {
                $d = $this->encryption_url->encode($d);
                $cols = '<div class="box-body hide action_button"><div class="btn-group">';                
                $cols.= '<span><a data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-app editBrand"><i class="fa fa-pencil"></i></a><input type="hidden" name="brand_name" value="'.$row['brand_name'].'"><input type="hidden" name="invoice_first_prefix" value="'.$row['brand_invoice_first_prefix'].'"><input type="hidden" name="invoice_last_prefix" value="'.$row['brand_invoice_last_prefix'].'"><input type="hidden" name="invoice_seperation" value=""><input type="hidden" name="invoice_type" value="'.$row['invoice_type'].'"><input type="hidden" name="invoice_creation" value="'.$row['invoice_creation'].'"><input type="hidden" name="invoice_readonly" value="'.$row['invoice_readonly'].'"><input type="hidden" name="brand_id" value="'.$d.'"></span>';
                $cols.='<span data-backdrop="static" data-keyboard="false" class="delete_button" data-toggle="modal" data-target="#delete_modal" data-id="' . $d . '" data-path="brand/delete" data-delete_message="If you delete this record then its associated records also will be delete!! Do you want to continue?"><a  class="btn btn-app" href="#" data-toggle="tooltip" data-placement="bottom" title="Delete" ><i class="fa fa-trash"></i></a></span>';
                $cols .= '</div></div>';                   
                return $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
            }),
        );
        // Database connection details
        $sql_details = $this->config->item('sql_details');
        $extraWhere = " branch_id='" . $this->session->userdata('SESS_BRANCH_ID') . "' AND delete_status=0 ";

        /*if($this->input->post('search')){
            $ext = $this->input->post('search')['value'];
            $extraWhere .= ' AND brand_name like "%'.$ext.'%" ';
        }*/

        $json = $this->ssp->simple($_POST, $sql_details, $table, $primaryKey, $columns, $extraWhere);
        echo json_encode($json);
        exit;
    }

    public function add()
    {
        $brand_module_id = $this->config->item('brand_module');
        $data['module_id'] = $brand_module_id;
        $data['brand_module_id'] = $brand_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($brand_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        
        $this->load->view('brand/add', $data);
    }

    public function validateBrand(){
        $brand_name = $this->input->post('brand_name');
        $brand_id = $this->input->post('brand_id');
        $brand_id = $this->encryption_url->decode($brand_id);
        $resp = $this->db->query("SELECT brand_name FROM brand WHERE LOWER(brand_name) like '".strtolower(trim($brand_name))."' AND brand_id != '".$brand_id."' AND branch_id = ".$this->session->userdata('SESS_BRANCH_ID'));
        
        echo json_encode($resp->num_rows());
    }

    public function add_brand(){
        $brand_module_id             = $this->config->item('brand_module');
        $data['module_id']           = $brand_module_id;
        $modules                     = $this->modules;
        $privilege                   = "add_privilege";
        $data['privilege']           = $privilege;
        $section_modules             = $this->get_section_modules($brand_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $json = array();
        $brand_data = array(
            "brand_name"             => $this->input->post('brand_name'),
            "added_date"             => date('Y-m-d'),
            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
        );
        if(@$this->input->post('invoice_first_prefix')){
            $brand_data['brand_invoice_first_prefix'] = $this->input->post('invoice_first_prefix');
        }
        if(@$this->input->post('reference_first_prefix')){
            $brand_data['brand_reference_first_prefix'] = $this->input->post('reference_first_prefix');
        }
        if(@$this->input->post('invoice_last_prefix')){
            $brand_data['brand_invoice_last_prefix'] = $this->input->post('invoice_last_prefix');
        }
        if(@$this->input->post('invoice_seperation')){
            $brand_data['invoice_seperation'] = $this->input->post('invoice_seperation');
        }
        if(@$this->input->post('invoice_type')){
            $brand_data['invoice_type'] = $this->input->post('invoice_type');
        }
        if(@$this->input->post('invoice_creation')){
            $brand_data['invoice_creation'] = $this->input->post('invoice_creation');
        }
        if(@$this->input->post('invoice_readonly')){
            $brand_data['invoice_readonly'] = $this->input->post('invoice_readonly');
        }
        

        if ($id = $this->general_model->insertData('brand', $brand_data)){
            $table    = "log";

            $log_data = array(
                    'user_id' => $this->session->userdata('SESS_USER_ID'),
                    'table_id'   => $id,
                    'table_name' => 'brand',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'  => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'    => 'Brand Inserted' );
            $this->general_model->insertData($table, $log_data);
            $json['brand_id'] = $id;
            $json['all_brand'] = $this->general_model->getRecords('*', 'brand', array('delete_status' => 0,'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
            $json['flag'] = true;
            $json['msg'] = 'Brand Added Successfully';
        }else{
            $json['flag'] = false;
            $json['msg'] = 'Brand Add Unsuccessful';
        }
        echo json_encode($json);
    }

    public function edit_brand(){
        $brand_module_id             = $this->config->item('brand_module');
        $data['module_id']               = $brand_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($brand_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $json = array();
        $brand_id = $this->input->post('brand_id');
        $brand_id = $this->encryption_url->decode($brand_id);
        $brand_data = array(
                "brand_name"                   => $this->input->post('brand_name'),
                "brand_invoice_first_prefix"   => $this->input->post('invoice_first_prefix'),
                "brand_reference_first_prefix" => $this->input->post('reference_first_prefix'),
                "brand_invoice_last_prefix"    => $this->input->post('invoice_last_prefix'),
                "invoice_seperation"           => $this->input->post('invoice_seperation'),
                "invoice_type"                 => $this->input->post('invoice_type'),
                "invoice_creation"             => $this->input->post('invoice_creation'),
                "invoice_readonly"             => $this->input->post('invoice_readonly'),
                "updated_date"             => date('Y-m-d'),
                "updated_user_id"          => $this->session->userdata('SESS_USER_ID'),
                "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
            );

        if ($this->general_model->updateData('brand', $brand_data, array('brand_id' => $brand_id))){
            $table    = "log";
            $log_data = array(
                    'user_id' => $this->session->userdata('SESS_USER_ID'),
                    'table_id'   => $brand_id,
                    'table_name' => 'brand',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'    => 'Brand Updated' );
            $this->general_model->insertData($table, $log_data);

            $json['flag'] = true;
            $json['msg'] = 'Brand Updated Successfully';
        }else{
            $json['flag'] = false;
            $json['msg'] = 'Brand Update Unsuccessful';
        }
        echo json_encode($json);
    }

    public function delete(){
        $brand_module_id    = $this->config->item('brand_module');
        $data['module_id']  = $brand_module_id;
        $modules            = $this->modules;
        $privilege          = "delete_privilege";
        $data['privilege']  = $privilege;
        $section_modules    = $this->get_section_modules($brand_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);
        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        $table                           = "brand";
        $module_settings_data            = array(
                "delete_status" => 1 );
        $where                           = array(
                "brand_id" => $id );
        if ($this->general_model->updateData($table, $module_settings_data, $where)){
            $log_data = array(
                    'table_id'   => $id,
                    'table_name' => 'brand',
                    'branch_id'  => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'    => 'Brand Deleted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
        }
        redirect("brand", 'refresh');
    }
}
?>