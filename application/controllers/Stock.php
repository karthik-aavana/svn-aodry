<?php

defined('BASEPATH') OR exit('NO direct script access allowed');

class Stock extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
        $this->load->helper('image_upload_helper');
    }
    
    public function index() {
        $stock_module_id = $this->config->item('stock_module');
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
         $data = array_merge($data, $section_modules);
        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'branch_code',
                1 => 'branch_name',
                2 => 'department_name',
                3 => 'sub_department_name',
                4 => 'category_name',
                5 => 'sub_category_name',
                6 => 'product_name',
                7 => 'product_code',
                8 => 'size',
                9 => 'colour',
                10 => 'product_hsn_sac_code',
                11 => 'sales_date',
                12 => 'customer_name',
                13 => 'sales_invoice_number',
                14 => 'uom',
                15 => 'sales_item_quantity',
                16 => 'gross_sales',
                17 => 'discount',
                18 => 'net_sales',
                19 => 'tax'
            );


            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_sales_stock_report();
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
                    $qty = $post->sales_item_quantity;
                    $price = $post->sales_item_unit_price;
                    $discount = $post->sales_item_discount_amount;
                    $gross_sales = $qty * $price;
                    $net_sales = $gross_sales - $discount;
                    $tax = $post->sales_item_tds_amount + $post->sales_item_igst_amount + $post->sales_item_sgst_amount + $post->sales_item_cgst_amount +  $post->sales_item_tax_cess_amount;

                    $combination_id = $post->product_combination_id;
                    $colour_val = '-';
                     $size_val = '-';
                    if($combination_id != ''){
                     $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                    'combination_id'   => $combination_id,
                    'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                    $varient_value_id = $combination_data[0]->varient_value_id;

                    $sql = "SELECT V.varient_key,VV.varients_value  FROM  varients_value VV
                    JOIN varients V ON V.varients_id = VV.varients_id
                     WHERE varients_value_id IN (".$varient_value_id.")";
                     $qry = $this->db->query($sql);
                     
                     $key = '';
                     if($qry->num_rows() > 0){
                        $var_lal = $qry->result_array();
                        foreach ($var_lal as $key => $value) {
                            $key = strtolower($value['varient_key']);
                            if($key == 'colour' || $key == 'colours' || $key == 'color'|| $key == 'colors'){
                               $colour_val = $value['varients_value'];
                            }

                            if($key == 'size' || $key == 'sizes'){
                               $size_val = $value['varients_value'];
                            }
                        }
                    }
                }

                    $nestedData['branch_code'] = $post->customer_code;
                    $nestedData['branch_name'] = $post->customer_name;
                    $nestedData['store_location'] = $post->store_location;
                    $nestedData['department_name'] = $post->department_name;
                    $nestedData['sub_department_name'] = $post->sub_department_name;
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['sub_category_name'] = $post->sub_category_name;
                    $nestedData['article_number'] = $post->product_code;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_barcode'] = $post->product_barcode;
                    $nestedData['size'] = $size_val;
                    $nestedData['colour'] = $colour_val;
                    $nestedData['product_hsn_sac_code'] = $post->product_hsn_sac_code;
                    $nestedData['sales_date'] = date('d-m-Y', strtotime($post->sales_date));
                    $nestedData['customer_name'] = $post->customer_name;
                    $nestedData['sales_invoice_number'] = $post->sales_invoice_number;
                    $nestedData['uom'] = $post->uom;
                    $nestedData['sales_item_quantity'] = $post->sales_item_quantity;
                    $nestedData['gross_sales'] = $this->precise_amount($gross_sales,$access_common_settings[0]->amount_precision);
                    $nestedData['discount'] = $this->precise_amount($post->sales_item_discount_amount,$access_common_settings[0]->amount_precision);
                    $nestedData['net_sales'] = $this->precise_amount($net_sales,$access_common_settings[0]->amount_precision);
                    $nestedData['tax'] = $this->precise_amount($tax,$access_common_settings[0]->amount_precision);
                    $nestedData['brand_name'] = $post->brand_name;
                    $send_data[] = $nestedData;
                    
                }
            }

            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
           
        } else {
            $this->load->view('stock/list', $data);
        }
    }


    public function purchase_stock() {
        $stock_module_id = $this->config->item('stock_module');
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
         $data = array_merge($data, $section_modules);
       

        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'branch_code',
                1 => 'branch_name',
                2 => 'department_name',
                3 => 'sub_department_name',
                4 => 'category_name',
                5 => 'sub_category_name',
                6 => 'product_name',
                7 => 'product_code',
                8 => 'size',
                9 => 'colour',
                10 => 'product_hsn_sac_code',
                11 => 'purchase_date',
                12 => 'supplier_name',
                13 => 'purchase_invoice_number',
                14 => 'uom',
                15 => 'purchase_item_quantity',
                16 => 'gross_purchase',
                17 => 'discount',
                18 => 'net_purchase',
                19 => 'tax'
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_purchase_stock_report();
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
            } $send_data = array();
            if (!empty($posts)) {
               
                foreach ($posts as $post) {
                    $qty = $post->purchase_item_quantity;
                    $price = $post->purchase_item_unit_price;
                    $discount = $post->purchase_item_discount_amount;
                    $gross_purchase = $qty * $price;
                    $net_purchase = $gross_purchase - $discount;
                    $tax = $post->purchase_item_tds_amount + $post->purchase_item_igst_amount + $post->purchase_item_cgst_amount + $post->purchase_item_sgst_amount +  $post->purchase_item_tax_cess_amount;

                    $combination_id = $post->product_combination_id;
                    $colour_val = '-';
                    $size_val = '-';
                    if($combination_id != ''){
                     $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                    'combination_id'   => $combination_id,
                    'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                    $varient_value_id = $combination_data[0]->varient_value_id;

                    $sql = "SELECT V.varient_key,VV.varients_value  FROM  varients_value VV
                    JOIN varients V ON V.varients_id = VV.varients_id
                     WHERE varients_value_id IN (".$varient_value_id.")";
                     $qry = $this->db->query($sql);                    
                     $key = '';
                     if($qry->num_rows() > 0){
                        $var_lal = $qry->result_array();
                        foreach ($var_lal as $key => $value) {
                            $key = strtolower($value['varient_key']);
                            if($key == 'colour' || $key == 'colours' || $key == 'color'|| $key == 'colors'){
                               $colour_val = $value['varients_value'];
                            }

                            if($key == 'size' || $key == 'sizes'){
                               $size_val = $value['varients_value'];
                            }
                        }
                    }
                }

                    $nestedData['branch_code'] = $post->supplier_code;
                    $nestedData['branch_name'] = $post->supplier_name;
                    $nestedData['department_name'] = $post->department_name;
                    $nestedData['sub_department_name'] = $post->sub_department_name;
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['sub_category_name'] = $post->sub_category_name;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['article_number'] = $post->product_code;
                    $nestedData['product_barcode'] = $post->product_barcode;
                    $nestedData['size'] = $size_val;
                    $nestedData['colour'] = $colour_val;
                    $nestedData['product_hsn_sac_code'] = $post->product_hsn_sac_code;
                    $nestedData['purchase_date'] = date('d-m-Y', strtotime($post->purchase_date));
                    $nestedData['supplier_name'] = $post->supplier_name;
                    $nestedData['purchase_grn_number'] = $post->purchase_grn_number;
                    $nestedData['purchase_invoice_number'] = $post->purchase_invoice_number;
                    $nestedData['uom'] = $post->uom;
                    $nestedData['product_mrp_price'] = $this->precise_amount($post->product_mrp_price, $access_common_settings[0]->amount_precision);
                    $nestedData['purchase_item_quantity'] = $post->purchase_item_quantity;
                    $nestedData['gross_purchase'] = $this->precise_amount($gross_purchase,$access_common_settings[0]->amount_precision);
                    $nestedData['discount'] = $this->precise_amount($post->purchase_item_discount_amount,$access_common_settings[0]->amount_precision);
                    $nestedData['net_purchase'] = $this->precise_amount($net_purchase,$access_common_settings[0]->amount_precision);
                    $nestedData['tax'] = $this->precise_amount($tax,$access_common_settings[0]->amount_precision);
                    $nestedData['brand_name'] = $post->brand_name;
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('stock/purchase_stock_list', $data);
        }
    }

    public function closing_stock() {
        $stock_module_id = $this->config->item('stock_module');
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
         $data = array_merge($data, $section_modules);
        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'branch_code',
                1 => 'branch_name',
                2 => 'department_name',
                3 => 'sub_department_name',
                4 => 'category_name',
                5 => 'sub_category_name',
                6 => 'product_name',
                7 => 'product_code',
                8 => 'size',
                9 => 'colour',
                10 => 'product_hsn_sac_code',
                11 => 'uom',
                12 => 'product_quantity',
                13 => 'map',
                14 => 'gst',
                15 => 'selling_price'
            );

 

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_closing_stock_report();
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
                    $price = $post->price;
                    $tax = (int) $post->igst + (int) $post->sgst + (int) $post->cgst;
                    //$basic_price = $post->product_basic_price;
                    $basic_price = (int) $post->price;
                   // $closing = (int) $post->product_quantity + (int) $post->product_opening_quantity;
                    $closing = (int) $post->purchase_qty + (int) $post->product_opening_quantity - (int) $post->sales_qty;
                    $map = $basic_price * $closing;

                    $combination_id = $post->product_combination_id;
                    $colour_val = '-';
                    $size_val = '-';
                    if($combination_id != ''){
                     $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                    'combination_id'   => $combination_id,
                    'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                    $varient_value_id = $combination_data[0]->varient_value_id;

                    $sql = "SELECT V.varient_key,VV.varients_value  FROM  varients_value VV
                    JOIN varients V ON V.varients_id = VV.varients_id
                     WHERE varients_value_id IN (".$varient_value_id.")";
                     $qry = $this->db->query($sql);
                     $key = '';
                     if($qry->num_rows() > 0){
                        $var_lal = $qry->result_array();
                        foreach ($var_lal as $key => $value) {
                            $key = strtolower($value['varient_key']);
                            if($key == 'colour' || $key == 'colours' || $key == 'color'|| $key == 'colors'){
                               $colour_val = $value['varients_value'];
                            }

                            if($key == 'size' || $key == 'sizes'){
                               $size_val = $value['varients_value'];
                            }
                        }
                    }
                }

                    // $nestedData['branch_code'] = $post->branch_code;
                    // $nestedData['branch_name'] = $post->branch_name;
                    $nestedData['branch_code'] = $post->supplier_code;
                    $nestedData['branch_name'] = $post->supplier_name;
                    $nestedData['store_location'] = $post->store_location;
                    $nestedData['department_name'] = $post->department_name;
                    $nestedData['sub_department_name'] = $post->sub_department_name;
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['sub_category_name'] = $post->sub_category_name;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_code'] = $post->product_code;
                    $nestedData['product_barcode'] = $post->product_barcode;
                    $nestedData['size'] = $size_val;
                    $nestedData['colour'] = $colour_val;
                    $nestedData['product_hsn_sac_code'] = $post->product_hsn_sac_code;
                    $nestedData['uom'] = $post->uom;
                    $nestedData['product_quantity'] = $closing;
                    $nestedData['map'] = $this->precise_amount($map,$access_common_settings[0]->amount_precision);
                    $nestedData['gst'] = $this->precise_amount($tax,$access_common_settings[0]->amount_precision);
                    $nestedData['selling_price'] = $this->precise_amount($price,$access_common_settings[0]->amount_precision);
                    $nestedData['brand_name'] = $post->brand_name;
                    $nestedData['opening_stock'] = (int) $post->product_opening_quantity;
                    $nestedData['purchase_qty'] = (int) $post->purchase_qty;
                    $nestedData['sales_qty'] = (int) $post->sales_qty;
                    $nestedData['product_batch'] = $post->product_batch;
                    $send_data[] = $nestedData;
                    
                }
            }

            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
           
        } else {
            $this->load->view('stock/closing_stock_list', $data);
        }
    }


    public function brand_sales_stock() {
        $stock_module_id = $this->config->item('stock_module');
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
         $data = array_merge($data, $section_modules);
        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'brand_name',               
                1 => 'category_name',
                2 => 'sub_category_name',
                3 => 'product_name',
                4 => 'product_code',
                5 => 'product_hsn_sac_code',
                6 => 'sales_date',
                7 => 'customer_name',
                8 => 'sales_invoice_number',
                9 => 'uom',
                10 => 'sales_item_quantity',
                11 => 'gross_sales',
                12 => 'discount',
                12 => 'sch_discount',
                13 => 'net_sales',
                14 => 'tax'
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_brandwise_sales_stock_report();
            $list_data['search'] = 'all';
             $list_data['section'] = 'sales_stock';
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
            if ($this->input->post('filter_brand') != "" ) {

                $filter_search['filter_brand'] = ($this->input->post('filter_brand') == '' ? '' : implode(",", $this->input->post('filter_brand')));
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();


            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $qty = $post->sales_item_quantity;
                    $price = $post->sales_item_unit_price;
                    $discount = $post->sales_item_discount_amount;
                    $gross_sales = $qty * $price;
                    $net_sales = $gross_sales - $discount;
                    $tax = $post->sales_item_tds_amount + $post->sales_item_igst_amount + $post->sales_item_sgst_amount + $post->sales_item_cgst_amount +  $post->sales_item_tax_cess_amount;

                    $nestedData['brand_name'] = $post->brand_name;
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['sub_category_name'] = $post->sub_category_name;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_code'] = $post->product_code;                    
                    $nestedData['product_hsn_sac_code'] = $post->product_hsn_sac_code;
                    $nestedData['sales_date'] = date('d-m-Y', strtotime($post->sales_date));
                    $nestedData['customer_name'] = $post->customer_name;
                    $nestedData['sales_invoice_number'] = $post->sales_invoice_number;
                    $nestedData['uom'] = $post->uom;
                    $nestedData['sales_item_quantity'] = $post->sales_item_quantity;
                    $nestedData['gross_sales'] = $this->precise_amount($gross_sales,$access_common_settings[0]->amount_precision);
                    $nestedData['discount'] = $this->precise_amount($post->sales_item_discount_amount,$access_common_settings[0]->amount_precision).'<br>('.$this->precise_amount($post->item_discount_percentage,$access_common_settings[0]->amount_precision).'%)';
                    $nestedData['sch_discount'] = $this->precise_amount($post->sales_item_scheme_discount_amount,$access_common_settings[0]->amount_precision).'<br>('.$this->precise_amount($post->sales_item_scheme_discount_percentage,$access_common_settings[0]->amount_precision).'%)';
                    $nestedData['net_sales'] = $this->precise_amount($net_sales,$access_common_settings[0]->amount_precision);
                    $nestedData['tax'] = $this->precise_amount($tax,$access_common_settings[0]->amount_precision);
                    $send_data[] = $nestedData;
                    
                }
            }

            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
           
        } else {
            $list_data = $this->common->distinct_brand_sales();
            $data['brand'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $this->load->view('stock/brand_sales_stock_list', $data);
        }
    }


    public function brand_purchase_stock() {
         $stock_module_id = $this->config->item('stock_module');
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
         $data = array_merge($data, $section_modules);
       

        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'brand_name',
                1 => 'category_name',
                2 => 'sub_category_name',
                3 => 'product_name',
                4 => 'product_code',
                5 => 'product_hsn_sac_code',
                6 => 'purchase_date',
                7 => 'supplier_name',
                8 => 'purchase_invoice_number',
                9 => 'uom',
                10 => 'purchase_item_quantity',
                11 => 'gross_purchase',
                12 => 'discount',
                13 => 'net_purchase',
                14 => 'tax'
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_brandwise_purchase_stock_report();
            $list_data['search'] = 'all';
            $list_data['section'] = 'purchase_stock';
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
            if ($this->input->post('filter_brand') != "" ) {

                $filter_search['filter_brand'] = ($this->input->post('filter_brand') == '' ? '' : implode(",", $this->input->post('filter_brand')));
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['filter_search'] = $filter_search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }$send_data = array();
            if (!empty($posts)) {
               
                foreach ($posts as $post) {
                    $qty = $post->purchase_item_quantity;
                    $price = $post->purchase_item_unit_price;
                    $discount = $post->purchase_item_discount_amount;
                    $gross_purchase = $qty * $price;
                    $net_purchase = $gross_purchase - $discount;
                    $tax = $post->purchase_item_tds_amount + $post->purchase_item_igst_amount + $post->purchase_item_cgst_amount + $post->purchase_item_sgst_amount +  $post->purchase_item_tax_cess_amount;
                   

                    $nestedData['brand_name'] = $post->brand_name;
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['sub_category_name'] = $post->sub_category_name;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_code'] = $post->product_code;                   
                    $nestedData['product_hsn_sac_code'] = $post->product_hsn_sac_code;
                    $nestedData['purchase_date'] = date('d-m-Y', strtotime($post->purchase_date));
                    $nestedData['supplier_name'] = $post->supplier_name;
                    $nestedData['purchase_invoice_number'] = $post->purchase_invoice_number;
                    $nestedData['uom'] = $post->uom;
                    $nestedData['purchase_item_quantity'] = $post->purchase_item_quantity;
                    $nestedData['gross_purchase'] = $this->precise_amount($gross_purchase,$access_common_settings[0]->amount_precision);
                    $nestedData['discount'] = $this->precise_amount($post->purchase_item_discount_amount,$access_common_settings[0]->amount_precision);
                    $nestedData['net_purchase'] = $this->precise_amount($net_purchase,$access_common_settings[0]->amount_precision);
                    $nestedData['tax'] = $this->precise_amount($tax,$access_common_settings[0]->amount_precision);
                    $send_data[] = $nestedData;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $list_data = $this->common->distinct_brand_purchase();
            $data['brand'] = $this->general_model->getJoinRecords($list_data['string'], $list_data['table'], $list_data['where'], $list_data['join'], $list_data['order'] = "", $list_data['group']);
            $this->load->view('stock/brand_purchase_stock_list', $data);
        }
    }

    public function brand_closing_stock() {
        $stock_module_id = $this->config->item('stock_module');
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
         $data = array_merge($data, $section_modules);
        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'brand_name',
                1 => 'category_name',
                2 => 'sub_category_name',
                3 => 'product_name',
                4 => 'product_code',
                5 => 'product_hsn_sac_code',
                6 => 'uom',
                7 => 'product_quantity',
                8 => 'map',
                9 => 'gst',
                10 => 'selling_price'
            );

 

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_brandwise_closing_stock_report();
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
            /*print_r($this->db->last_query());*/
            $send_data = array();

            if (!empty($posts)) {
                
                foreach ($posts as $post) {
                    /*$price = $post->price;*/
                    /*$tax = $post->igst + $post->sgst + $post->cgst;*/
                    //$basic_price = $post->product_basic_price;
                    /*$basic_price = (@$post->price != '' ? $post->price : 0);*/

                    $closing =  (float)$post->product_quantity + (float) $post->product_opening_quantity;
                    /*$map = $basic_price * $closing;*/
                    $value_of_stock = $post->product_price * $closing;

                    $nestedData['brand_name'] = $post->brand_name;
                    $nestedData['category_name'] = $post->category_name;
                    $nestedData['sub_category_name'] = $post->sub_category_name;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_code'] = $post->product_code;
                    $nestedData['product_hsn_sac_code'] = $post->product_hsn_sac_code;
                    $nestedData['uom'] = $post->uom;
                    $nestedData['product_batch'] = $post->product_batch;
                    $nestedData['product_quantity'] = round($closing,2);
                    $nestedData['value_of_stock'] = $this->precise_amount($value_of_stock,$access_common_settings[0]->amount_precision);
                    /*$nestedData['map'] = $this->precise_amount($map,$access_common_settings[0]->amount_precision);
                    $nestedData['gst'] = $this->precise_amount($tax,$access_common_settings[0]->amount_precision);
                    $nestedData['selling_price'] = $this->precise_amount($price,$access_common_settings[0]->amount_precision);*/
                    $send_data[] = $nestedData;
                    
                }
            }

            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
           
        } else {
            $this->load->view('stock/brand_closing_stock_list', $data);
        }
    }
}