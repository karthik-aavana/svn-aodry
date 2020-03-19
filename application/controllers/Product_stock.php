<?php

defined('BASEPATH') OR exit('NO direct script access allowed');

class Product_stock extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
        $this->load->helper('image_upload_helper');
    }
    public function index() {
        $stock_module_id = $this->config->item('damaged_stock_module');
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);

        $data['products'] = $this->product_call();
        /* presents all the needed */
        $branch_id =$this->session->userdata('SESS_BRANCH_ID');
        $data['products_all'] = $this->general_model->getRecords('*', 'products', [
            'delete_status' => 0, 'product_quantity > ' => 0, 'branch_id' => $branch_id ]);
        $data=array_merge($data,$section_modules);       
        $access_settings        = $data['access_settings'];
         $type = 'damaged';
        $primary_id  = "product_damage_id";
        $table_name             = "product_damaged";
        $date_field_name        = "added_date";
        $current_date           = date('Y-m-d');

        $data['invoice_number'] = $this->generate_stock_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, '', $type);

        if (!empty($this->input->post())) {

            $columns = array(
                0 => 'product_name',
                0 => 'product_sku',
                1 => 'reference_number',
                2 => 'qty',
                3 => 'product_quantity',
                4 => 'amt',
                5 => 'purchase_cost',
                6 => 'product_alert_quantity',
                7 => 'product_damaged_quantity',
                8 => 'product_missing_quantity',
                9 => 'view',
                10 => 'action'
            );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->get_product_stock();
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
                $i = 1;
                foreach ($posts as $post) {
                    $ref = '';
                    if($post->REF != ''){
                         $ref .= $post->REF.', ';
                    }
                    if($post->REFD != ''){
                        $ref .= $post->REFD.', ';
                    }
                    if($post->REFC != ''){
                        $ref .= $post->REFC.', ';
                    }
                    $product_id = $post->product_id;
                    $pur_amt = round($post->pur_amt, 2);
                    $pur_cn_amt = round($post->pur_cn_amt, 2);
                    $pur_dn_amt = round($post->pur_dn_amt, 2);
                    if ($pur_cn_amt != '' && $pur_dn_amt != '') {
                        $unit_price = round((($pur_amt + $pur_cn_amt + $pur_dn_amt) / 3),2);
                    } elseif ($pur_cn_amt != '') {
                        $unit_price = round((($pur_amt + $pur_cn_amt) / 2),2);
                    } elseif ($pur_dn_amt != '') {
                        $unit_price = round((($pur_amt + $pur_dn_amt) / 2),2);
                    } else {
                        $unit_price = $pur_amt;
                    }
                    $pur_qty = round($post->pur_qty, 2);
                    $pur_dn_qty = round($post->pur_dn_qty, 2);
                    $pur_cn_qty = round($post->pur_cn_qty, 2);
                    $opening_qty = 0;
                    if(isset($post->product_opening_quantity)){
                       $opening_qty = round($post->product_opening_quantity, 2); 
                    }
                    
                    $qty = $pur_qty - $pur_dn_qty + $pur_cn_qty;
                    $purchase_cost = $unit_price * $qty;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_sku'] = $post->product_sku;
                  // $nestedData['reference_number'] = $ref;
                    $nestedData['reference_number'] = '<a href="#" class="reference" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#view_reference" data-id="' . $product_id . '"><i class="fa fa-info-circle"></i></a>';
                    $nestedData['qty'] = $qty;
                    $nestedData['product_quantity'] = $post->product_quantity + $opening_qty;
                    $nestedData['amt'] = $this->precise_amount($unit_price, 2);
                    $nestedData['purchase_cost'] = $this->precise_amount($purchase_cost, 2);
                    $reoder = ($post->product_alert_quantity) ? $post->product_alert_quantity : 0;
                    $nestedData['product_alert_quantity'] = '<input type="text" name="reorder" id="reorder_' . $product_id . '" value="' . $reoder . '" data-id="' . $product_id . '" class="disable_in"/><span><a href="#" data-id="' . $product_id . '" class="edit_cell" ><i class="fa fa-pencil"></i> | </a> <a class="save_cell" data-id="' . $product_id . '" href="#"><i class="fa fa-save"></i></a></span>';
                    $nestedData['product_damaged_quantity'] = ($post->product_damaged_quantity) ? $post->product_damaged_quantity : 0;
                    $nestedData['product_missing_quantity'] = ($post->product_missing_quantity) ? $post->product_missing_quantity : 0;
                    $nestedData['view'] = '<a href="#" class="history_damage" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#view_product" data-id="' . $product_id . '"><i class="fa fa-eye"></i></a>';
                    $cols = '';
                    $cols .= '<a href="#" class="damaged_product" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#damaged_product" data-id="' . $product_id . '"><i class="fa fa-eye"></i></a>';
                    $nestedData['action'] = $cols;
                    $send_data[] = $nestedData;
                    $i++;
                }
            }
            $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('product_stock/list', $data);
        }
    }

    public function add_product_stock() {
        $reference_date = date('Y-m-d',strtotime($this->input->post('reference_date')));
        $reference_number = $this->input->post('reference_number');
        $cmb_product = $this->input->post('cmb_product');
        $txt_quantity = $this->input->post('txt_quantity');
        $cmb_move_product = $this->input->post('cmb_move_product');
        $txt_comments = $this->input->post('txt_comments');

        $user_id = $this->session->userdata('SESS_USER_ID');
        $financial_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        if (isset($_FILES["attachment"]["name"]) && $_FILES["attachment"]["name"] != "") {
            $path_parts = pathinfo($_FILES["attachment"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/damage_attachment/' . $this->session->userdata('SESS_BRANCH_ID'))) {
                mkdir('./assets/damage_attachment/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            } $url = "assets/damage_attachment/" . $this->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
            if (in_array($path_parts['extension'], array(
                        "jpg",
                        "jpeg",
                        "png"))) {
                if (is_uploaded_file($_FILES["attachment"]["tmp_name"])) {
                    if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $url)) {
                        $image_name = $image_path;
                    }
                }
            }
        } else {
            $image_name = '';
        }

        $damage_data = array("item_id" => $cmb_product,
            "item_type" => "product_inventory",
            "reference_number" => $reference_number,
            "reference_date" => $reference_date,
            "reference_type" => 'from_product',
            "quantity" => $txt_quantity,
            "stock_type" => $cmb_move_product,
            "branch_id" => $branch_id,
            "comments" => $txt_comments,
            "added_date" => date('Y-m-d'),
            "added_user_id" => $user_id,
            "attachment" => $image_name,
            "delete_status" => 0,
            "stock_refer" => $cmb_move_product
        );
        if($damage_id = $this->general_model->insertData('product_damaged', $damage_data)){
            $successMsg = 'Damaged/Missing Stock Reported Successfully';
            $this->session->set_flashdata('product_stock_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $damage_id,
                'table_name' => 'product_damaged',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Product Damage Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }

        if ($damage_id) {
            if($iasd = $this->general_model->insertData('quantity_history', [
                'item_type' => 'product_inventory',
                'item_id' => $cmb_product,
                'reference_id' => $damage_id,
                'stock_type' => $cmb_move_product,
                "reference_type" => 'moving_' . $cmb_move_product,
                'quantity' => $txt_quantity,
                'added_user_id' => $user_id,
                'added_date' => date('Y-m-d')])){
                $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $iasd,
                'table_name' => 'product_inventory',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Product Inventory Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
            }

            $values = $this->general_model->getRecords('*', 'products', [
                'product_id' => $cmb_product]);
            $existing_damage = $values[0]->product_damaged_quantity;
            $existing_missing = $values[0]->product_missing_quantity;

            $quantity = $values[0]->product_quantity;
            $where = array('product_id' => $cmb_product);
            $final_stock = $quantity - $txt_quantity;
            if ($cmb_move_product == 'damaged') {
                $final_damage = $existing_damage + $txt_quantity;
                $result = $this->general_model->updateData('products', ['product_quantity' => $final_stock,
                    'product_damaged_quantity' => $final_damage], $where);
            } else {
                $final_missing = $existing_missing + $txt_quantity;
                $result = $this->general_model->updateData('products', ['product_quantity' => $final_stock,
                    'product_missing_quantity' => $final_missing], $where);
            }

            if ($result) {               
               $res['flag'] = true;
               $res['msg'] = 'Damaged/Missing Stock Reported Successfully'; 
                $this->session->set_flashdata('product_stock_success',$successMsg);
            } else {
                $res['flag'] = false;
                $errorMsg = 'Damaged/Missing Stock Reported Unsuccessful';
                $res['msg'] = 'Damaged/Missing Stock Reporting Unsuccessful'; 
                $this->session->set_flashdata('product_stock_error',$errorMsg);
            }
        } else {
            $res['flag'] = false;
            $res['msg'] = 'Damaged/Missing Stock Reporting Unsuccessful'; 
            $errorMsg = 'Damaged/Missing Stock Reported Unsuccessful';
            $this->session->set_flashdata('product_stock_error',$errorMsg);
        }
        echo json_encode($res); 
    }

    public function product_details() {
        $id = $this->input->post('id');
        $product_data = $this->general_model->get_product_stock_damaged($id);
        $send = array();
        if (!empty($product_data)) {
            foreach ($product_data as $com) {
                $product_damage_id = $com->product_damage_id;
                $stock_type = $com->stock_type;
                $product_id = $com->product_id;
                if ($stock_type == 'damaged') {
                    $selected_damaged = 'selected';
                    $selected_missing = '';
                } else {
                    $selected_missing = 'selected';
                    $selected_damaged = '';
                }

                $ref_date = date("d-m-Y", strtotime($com->REF_DATE));
                $nestedData['reference_date'] = '<div class="input-group date">
                                        <input type="text" class="form-control datepicker disable_in" data-id="' . $product_damage_id . '" name="reported_date" id="reported_date_' . $product_damage_id . '" value="' . $ref_date . '" autocomplete="off">
                                        <div class="input-group-addon hide">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div><input type="hidden" id="product_id_' . $product_damage_id . '" name="product_id" value="' . $product_id . '"> <input type="hidden" id="old_type_' . $product_damage_id . '" name="old_type" value="' . $stock_type . '">';
                //  $nestedData['reference_date'] = $com->REF_DATE;
                $nestedData['reference_number'] = $com->REF_NUM;
                $nestedData['product_quantity'] = '<input type="number" name="quality" id="quality_' . $product_damage_id . '" value="' . $com->QTY . '" data-id="' . $product_damage_id . '" min="0" max="9" class="form-control disable_in"/><input type="hidden" id="old_quantity_' . $product_damage_id . '" name="old_quantity" value="' . $com->QTY . '">';
                $nestedData['movement'] = '<select class="form-control disable_in" id="movement_' . $product_damage_id . '" name="movement" tabindex="-1" aria-hidden="true" data-id="' . $product_damage_id . '">
                                        <option value="">Select Movement Type</option>
                                        <option value="damaged" ' . $selected_damaged . '>Damaged</option>
                                        <option value="missing" ' . $selected_missing . '>Missing</option>
                                    </select>';
                $nestedData['comments'] = '<input type="text" name="comments" id="comments_' . $product_damage_id . '" rows="2" class="form-control disable_in" data-id="' . $product_damage_id . '" value="' . $com->comments . '">';
                $nestedData['action'] = '<a href="#" class="edit_damage_cell" data-id="' . $product_damage_id . '"><i class="fa fa-pencil"></i></a> | <a class="btn btn-info btn-xs save_damage_cell" href="#" data-id="' . $product_damage_id . '"><i class="fa fa-save"></i></a>';
                $send[] = $nestedData;
            }
        }
        $data_fin = array('data' => $send);
        echo json_encode($data_fin);
    }

    public function update_damage_stock() {
        $reference_date = $this->input->post('reference_date');
        $reference_date = date("Y-m-d", strtotime($reference_date));
        $txt_quantity = $this->input->post('quantity');
        $cmb_move_product = $this->input->post('move_product');
        $comments = $this->input->post('comments');
        $old_type = $this->input->post('old_type');
        $product_id = $this->input->post('product_id');
        $quantity_old = $this->input->post('quantity_old');
        $id = $this->input->post('id');



        $user_id = $this->session->userdata('SESS_USER_ID');
        $financial_id = $this->session->userdata('SESS_FINANCIAL_YEAR_ID');
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');

        $damage_data = array("item_id" => $product_id,
            "item_type" => "product_inventory",
            "reference_date" => $reference_date,
            "reference_type" => 'from_product',
            "quantity" => $txt_quantity,
            "stock_type" => $cmb_move_product,
            "branch_id" => $branch_id,
            "comments" => $comments,
            "updated_date" => date('Y-m-d'),
            "updated_user_id" => $user_id,
            "stock_refer" => $cmb_move_product
        );
        $where_damage = array('product_damage_id' => $id);
        if($damage_id = $this->general_model->updateData('product_damaged', $damage_data, $where_damage)){
            $successMsg = 'Damaged/Missing Stock Report Updated successfully';
            $this->session->set_flashdata('product_stock_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $damage_id,
                'table_name' => 'product_damaged',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Product Damage Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
        if ($damage_id) {
            $where_quanity = array('reference_id' => $id);
            if($iasd = $this->general_model->updateData('quantity_history', [
                'item_type' => 'product_inventory',
                'item_id' => $product_id,
                'stock_type' => $cmb_move_product,
                "reference_type" => 'from_product' . $cmb_move_product,
                'quantity' => $txt_quantity,
                "updated_date" => date('Y-m-d'),
                "updated_user_id" => $user_id], $where_quanity)){
                $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $iasd,
                'table_name' => 'product_inventory',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Product Inventory Updated');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
            }
            $values = $this->general_model->getRecords('*', 'products', [
                'product_id' => $product_id]);
            $existing_damage = $values[0]->product_damaged_quantity;
            $existing_missing = $values[0]->product_missing_quantity;
            $quantity = $values[0]->product_quantity;
            $where = array('product_id' => $product_id);
            $differnce = $quantity_old - $txt_quantity;
            if ($cmb_move_product == $old_type) {

                if ($cmb_move_product == 'damaged' && $differnce != 0) {
                    if ($differnce > 0) {
                        $final_damage = $existing_damage - abs($differnce);
                        $final_stock = $quantity + abs($differnce);
                    } else {
                        $final_damage = $existing_damage + abs($differnce);
                        $final_stock = $quantity - abs($differnce);
                    }
                    $result = $this->general_model->updateData('products', ['product_quantity' => $final_stock, 'product_damaged_quantity' => $final_damage], $where);
                } else {

                    if ($differnce > 0) {
                        $final_missing = $existing_missing - abs($differnce);
                        $final_stock = $quantity + abs($differnce);
                    } else {

                        $final_missing = $existing_missing + abs($differnce);
                        $final_stock = $quantity - abs($differnce);
                    }
                    $result = $this->general_model->updateData('products', ['product_quantity' => $final_stock, 'product_missing_quantity' => $final_missing], $where);
                }
            } else {
                if ($cmb_move_product == 'damaged') {
                    $final_damage = $existing_damage + $txt_quantity;
                    if ($differnce == 0) {
                        $final_missing = $existing_missing - $quantity_old;
                        $final_stock = $quantity;
                    } elseif ($differnce > 0) {
                        $final_missing = $existing_missing - abs($differnce);
                        $final_stock = $quantity + abs($differnce);
                    } else {
                        $final_missing = $existing_missing + abs($differnce);
                        $final_stock = $quantity - abs($differnce);
                    }
                } else {
                    $final_missing = $existing_missing + $txt_quantity;
                    if ($differnce == 0) {
                        $final_stock = $quantity;
                        $final_damage = $existing_damage - $quantity_old;
                    } elseif ($differnce > 0) {
                        $final_stock = $quantity + abs($differnce);
                        $final_missing = $existing_damage - abs($differnce);
                    } else {
                        $final_damage = $existing_damage + abs($differnce);
                        $final_stock = $quantity - abs($differnce);
                    }
                }
                $result = $this->general_model->updateData('products', ['product_quantity' => $final_stock, 'product_missing_quantity' => $final_missing, 'product_damaged_quantity' => $final_damage], $where);
            }
            if ($result) {
                $successMsg = 'Damaged/Missing Stock Report Update Successfully';
                $this->session->set_flashdata('product_stock_success',$successMsg);
                $res['flag'] = true;              
                $res['msg'] = 'Damaged/Missing Stock Report Update Successfully'; 
            } else {
                $errorMsg = 'Damaged/Missing Stock Report Update Unsuccessful';
                $this->session->set_flashdata('product_stock_error',$errorMsg);
                $res['flag'] = false;              
                $res['msg'] = 'Damaged/Missing Stock Report Update Unsuccessful'; 
            }
        } else {
            $errorMsg = 'Damaged/Missing Stock Report Update Unsuccessful';
            $this->session->set_flashdata('product_stock_error',$errorMsg);
            $res['flag'] = false;              
            $res['msg'] = 'Damaged/Missing Stock Report Update Unsuccessful'; 
        }

        echo json_encode($res);
    }

    public function generate_stock_ref_number() {
        $type = $this->input->post('type');
        if ($type == 'damaged') {
            $stock_module_id = $this->config->item('damaged_stock_module');
        } elseif ($type == 'missing') {
            $stock_module_id = $this->config->item('missing_stock_module');
        } else {
            $stock_module_id = $this->config->item('stock_module');
        }
        $data['stock_module_id'] = $stock_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules = $this->get_section_modules($stock_module_id, $modules, $privilege);
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_settings = $data['access_settings'];
        $primary_id = "product_damage_id";
        $table_name = "product_damaged";
        $date_field_name = "added_date";
        $current_date = date('Y-m-d');
        $invoice_number = $this->generate_stock_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date, '', $type);
        $num_invoice = array('invoice_number' => $invoice_number);
        echo json_encode($num_invoice);
    }

    public function update_reorder() {
        $product_id = $this->input->post('product_id');
        $reorder = $this->input->post('reorder');
        $reorderData = array('product_alert_quantity' => $reorder);
        $where = array('product_id' => $product_id);
        $product_table = $this->config->item('product_table');
        $this->general_model->updateData($product_table, $reorderData, $where);
        echo true;
    }

    public function damaged_history($id) {
        $list_data = $this->common->get_product_stock_missing_damaged_history($id);
        $list_data['search'] = 'all';
        $post = $this->general_model->getPageJoinRecords($list_data);
        $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
        $temp = array();
        $columns = array(
            0 => 'report_date',
            1 => 'reference_date',
            2 => 'reference_number',
            3 => 'qty',
            4 => 'type',
            5 => 'comment',
        );
        if (!empty($post)) {

            foreach ($post as $value) {
                $ref_date = date("d-m-Y", strtotime($value->REF_DATE));
                $report_date = date("d-m-Y", strtotime($value->added_date));
                $nestedData['report_date'] = $report_date;
                $nestedData['reference_date'] = $ref_date;
                $nestedData['reference_number'] = $value->REF_NUM;
                $nestedData['qty'] = $value->QTY;
                $nestedData['type'] = $value->stock_type;
                $nestedData['comment'] = $value->comments;
                $temp[] = $nestedData;
            }
        }

        $totalFiltered = 10;
        $json_data = array(
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $temp);
        echo json_encode($json_data);
    }


    public function reference_history($id) {
        
       // $id = $this->input->post('id');
        $list_data = $this->common->get_product_refence($id);
        $list_data['search'] = 'all';
        $post = $this->general_model->getPageJoinRecords($list_data);
        $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
        $temp = array();
        $columns = array(
            0 => 'sno',
            1 => 'reference_number',
            2 => 'qty'
        );
        if (!empty($post)) {
            $i = 1;
          // print_r($post);
            foreach ($post as $value) {
                $ref = '';
                $qty = '';
               if($value->purchase_invoice_number != ''){
                $ref = $value->purchase_invoice_number;
                $qty = $value->purchase_item_quantity;
                $nestedData['sno'] = $i;
                $nestedData['reference_number'] = $ref;
                $nestedData['qty'] = $qty;
                $temp[] = $nestedData;
                $i = $i + 1;
               }
               if($value->purchase_debit_note_invoice_number != ''){
                $ref = $value->purchase_debit_note_invoice_number;
                
                 $qty = $value->purchase_debit_note_item_quantity;
                $nestedData['sno'] = $i;
                $nestedData['reference_number'] = $ref;
                $nestedData['qty'] = $qty;
                $temp[] = $nestedData;
                $i = $i + 1;
               }

               if($value->purchase_credit_note_invoice_number != ''){
                $ref = $value->purchase_credit_note_invoice_number;
                $qty = $value->purchase_credit_note_item_quantity;
                
                $nestedData['sno'] = $i;
                $nestedData['reference_number'] = $ref;
                $nestedData['qty'] = $qty;
                $temp[] = $nestedData;
                $i = $i + 1;
               } 
                
            }
        }

        $totalFiltered = 10;
        $json_data = array(
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $temp);
        echo json_encode($json_data);
    }

}
