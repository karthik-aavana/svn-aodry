<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends MY_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['general_model','Ion_auth_model']);
        // $this->load->helper('image_upload_helper');
    }

    function index()
    {
        $string = "*";
        $from = "warehouse";
        $where = ["delete_status" =>0];

        $data['warehouse_list'] = $this->general_model->getRecords($string,$from,$where);
        $this->load->view("super_admin/warehouse/list",$data);
    }

    function add()
    {
        $data['branch'] = $this->general_model->getRecords("*","branch",["delete_status" =>0]);
        $data['country'] = $this->general_model->getRecords("*","countries");
        
        $this->load->view("super_admin/warehouse/add",$data);
    }

    function add_warehouse()
    {
        $warehouse_data= array(
            "warehouse_name" => $this->input->post("warehouse_name"),
            "warehouse_address" => $this->input->post("address"),
            "warehouse_country_id" => $this->input->post("country"),
            "warehouse_state_id" => $this->input->post("state"),
            "warehouse_city_id" => $this->input->post("city"),
            "added_user_id" => $this->session->userdata("SESS_SA_USER_ID"),
            "added_date" => date("Y-m-d"),
            "branch_id" => $this->input->post("branch_id")
        );
        $this->general_model->insertData('warehouse',$warehouse_data);

        redirect("superadmin/warehouse");
    }

    function edit($id)
    {
        $id=$this->encryption_url->decode($id);

        $string="w.*";
        $table="warehouse w";
        $where=array("w.warehouse_id"=>$id,'w.delete_status'=>0);
        $join=array('branch b'=>'b.branch_id=b.branch_id');
        $data['data'] = $this->general_model->getJoinRecords($string,$table,$where,$join);

        $data['branch'] = $this->general_model->getRecords("*","branch",["delete_status" =>0]);

        /* Country Details */
        $country_data=$this->common->country_field();    
        $data['country']= $this->general_model->getRecords($country_data['string'],$country_data['table'],$country_data['where']);
        /* Country Details */

            /* State Details */
        $state_data=$this->common->state_field($data['data'][0]->warehouse_country_id);    
        $data['state']= $this->general_model->getRecords($state_data['string'],$state_data['table'],$state_data['where']);
        /* State Details */

            /* City Details */
        $city_data=$this->common->city_field($data['data'][0]->warehouse_state_id);    
        $data['city']= $this->general_model->getRecords($city_data['string'],$city_data['table'],$city_data['where']);
        /* City Details */

        $this->load->view("super_admin/warehouse/edit",$data);
    }

    function edit_warehouse()
    {
        $warehouse_id=$this->input->post('warehouse_id');
        $warehouse_data= array(
            "warehouse_name" => $this->input->post("warehouse_name"),
            "warehouse_address" => $this->input->post("address"),
            "warehouse_country_id" => $this->input->post("country"),
            "warehouse_state_id" => $this->input->post("state"),
            "warehouse_city_id" => $this->input->post("city"),
            "updated_user_id" => $this->session->userdata("SESS_SA_USER_ID"),
            "updated_date" => date("Y-m-d"),
            "branch_id" => $this->input->post("branch_id")
        );
        $this->general_model->updateData('warehouse', $warehouse_data, array('warehouse_id' => $warehouse_id));

        redirect("superadmin/warehouse");
    }

    function delete()
    {
        $warehouse_id=$this->encryption_url->decode($this->input->post('delete_id'));
        if($this->general_model->updateData('warehouse', array('delete_status' => '1'), array('warehouse_id' => $warehouse_id)))
        {
            $this->general_model->updateData('warehouse_item', array('delete_status' => '1'), array('warehouse_id' => $warehouse_id));
        }
        redirect("superadmin/warehouse");
    }
}