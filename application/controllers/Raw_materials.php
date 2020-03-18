<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Raw_materials extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    function index()
    {
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);


        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0  => 'product_code',
                    1  => 'product_hsn_sac_code',
                    2  => 'product_name',
                    3  => 'category_igst',
                    4  => 'category_name',
                    5  => 'product_price',
                    6  => 'product_quantity',
                    7  => 'product_damaged_quantity',
                    8  => 'product_unit',
                    9  => 'addded_user',
                    10 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->product_varient_raw_materials();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $product_id                         = $this->encryption_url->encode($post->product_inventory_id);
                    $nestedData['added_date']           = $post->added_date;
                    $nestedData['product_code']         = $post->product_code;
                    $nestedData['product_hsn_sac_code'] = "<a href='" . base_url('product/fetch_product_varient/') . $product_id . "''>" . $post->product_hsn_sac_code . "</a>";
                    $nestedData['product_name']         = "<a href='" . base_url('product/fetch_product_varient/') . $product_id . "''>" . $post->product_name . "</a>";
                    $nestedData['product_igst']         = $post->product_tax_value;
                    // $nestedData['category_name']        = $post->category_name;
                    $nestedData['product_price']        = $post->product_price;
                    $nestedData['type']                 = $post->type;
                    $nestedData['product_quantity']     = '<a data-toggle="modal" data-target="#quantity_products" class="quantity_change" style="cursor:pointer;" data-pid="' . $product_id . '" data-qty="' . $post->product_quantity . '" title="" >' . $post->product_quantity;
                    '</a>';

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="get_product(' . $post->product_inventory_id . ')" data-target="#editProduct">Edit Varient</a>';


                    $cols                 .= '<a data-toggle="modal" data-target="#delete_modal" data-id="' . $product_id . '" data-path="product/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Product</a>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            } $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }
        else
        {

            $data['product_category'] = $this->product_category_call();
            $data['tax']              = $this->tax_call();
            $data['uqc']              = $this->uqc_call();
            $data['chapter']          = $this->chapter_call();
            $data['hsn']              = $this->hsn_call();

            $this->load->view('raw_materials/list', $data);
        }
    }

}
