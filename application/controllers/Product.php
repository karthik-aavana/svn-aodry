<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Product extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'product_model',
            'general_model',
            'ledger_model']);
        $this->modules = $this->get_modules();
        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
        /*$this->load->library('ProductHook');*/
    }

    public function index(){

        $product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /*if($this->session->userdata('bulk_success')){
            if($this->session->flashdata('email_send') != 'success')
            $data['bulk_success'] = $this->session->userdata('bulk_success');
            $this->session->unset_userdata('bulk_success');
        }elseif ($this->session->userdata('bulk_error')) {
            $data['bulk_error'] = $this->session->userdata('bulk_error');
            $this->session->unset_userdata('bulk_error');
        }*/

        if (!empty($this->input->post())){
            $columns = array(
                0 => 'action',
                1  => 'product_code',
                2  => 'product_name',
                3  => 'product_type',
                4  => 'hsn',
                5  => 'product_unit',
                6  => 'product_price',
                7  => 'product_details');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];            
            $list_data           = $this->common->product_list_field();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;

              if($limit > -1){
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
            }

            if (empty($this->input->post('search')['value'])){
                // $list_data['limit']  = $limit;
                // $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);

            }else{
                $search              = $this->input->post('search')['value'];
                // $list_data['limit']  = $limit;
                // $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();
            $product_type_data = array();
            $product_type_data['rawmaterial'] = 'Raw Material';
            $product_type_data['semifinishedgoods'] = 'Semi Finished Goods';
            $product_type_data['finishedgoods'] = 'Finished Goods';

            if (!empty($posts)){
                foreach ($posts as $post){
                    $product_id                 = $this->encryption_url->encode($post->product_id);
                    $nestedData['product_code'] = $post->product_code;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_packing'] = ($post->packing != '' ? $post->packing : '-');
                    $nestedData['product_type']  = ($post->product_type ? $product_type_data[$post->product_type] : '');
                    $nestedData['hsn']          = $post->product_hsn_sac_code;
                    $nestedData['product_unit'] = $post->uom;
                    $nestedData['product_price'] = $this->precise_amount($post->product_price,$access_common_settings[0]->amount_precision);
                   $nestedData['product_details'] =  $post->product_details;
                     $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['product_module_id'], $data['active_edit'])){  

                        $cols .= '<span ><a  href="' . base_url('product/edit/') . $product_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_category btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['product_module_id'], $data['active_delete'])){

                        $item_type  = "product";
                        $exist_data = $this->common->database_product_exist($post->product_id, $item_type);

                        $exist_data_result = $this->general_model->getQueryRecords($exist_data);

                        if ($exist_data_result){
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal" data-path="sales/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }else{                            
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $product_id . '" data-path="product/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                    }
                    $cols .= '</div></div>';
                    $disabled = '';
                    if(!in_array($data['product_module_id'], $data['active_delete']) && !in_array($data['product_module_id'], $data['active_edit'])){
                        $disabled = 'disabled';
                    }
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal"'.$disabled.'>';
                    $send_data[]          = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }else{
            $this->load->view('product/list', $data);
        }

    }

    public function mainProductsList(){
        $product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        
        if (!empty($this->input->post())){
            $columns = array(
                0 => 'action',
                1  => 'product_code',
                2  => 'product_name',
                3  => 'product_type',
                4  => 'hsn',
                5  => 'product_unit',
                6  => 'product_price',
                7  => 'product_details');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $LeatherCraft_id = $this->config->item('LeatherCraft');
            $list_data           = $this->common->mainProduct_list_field($LeatherCraft_id);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;

            if (empty($this->input->post('search')['value'])){
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);

            }else{
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();
            $product_type_data = array();
            $product_type_data['rawmaterial'] = 'Raw Material';
            $product_type_data['semifinishedgoods'] = 'Semi Finished Goods';
            $product_type_data['finishedgoods'] = 'Finished Goods';

            if (!empty($posts)){
                foreach ($posts as $post){
                    $product_id                 = $this->encryption_url->encode($post->product_id);
                    $nestedData['product_code'] = $post->product_code;
                    $nestedData['product_packing'] = ($post->packing != '' ? $post->packing : '-');
                    $nestedData['brand_name'] = ($post->brand_name != '' ? $post->brand_name : '-');
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_type']  = ($post->product_type ? $product_type_data[$post->product_type] : '');
                    $nestedData['hsn']          = $post->product_hsn_sac_code;
                    $nestedData['product_unit'] = $post->uom;
                    $nestedData['product_price'] = $this->precise_amount($post->product_price,$access_common_settings[0]->amount_precision);
                   $nestedData['product_details'] =  $post->product_details;
                     $cols = '<div class="box-body hide action_button">
                        <div class="btn-group">';

                    if (in_array($data['product_module_id'], $data['active_edit'])){  

                        $cols .= '<span ><a  href="' . base_url('product/edit/') . $product_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_category btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['product_module_id'], $data['active_delete'])){

                        $item_type  = "product";

                        $sql = "SELECT product_id FROM products WHERE `product_combination_id` IN (SELECT combination_id FROM product_combinations WHERE product_id = " . $post->product_id . " AND status = 'Y')";
                        $qry = $this->db->query($sql);
                        if($qry->num_rows() > 0){
                            $var_lal = $qry->result_array();
                            $product_id_array = array();
                            $all_product_id = array();
                            foreach ($var_lal as $key => $value) {
                                $product_id_array[] = $value['product_id'];
                            }
                           
                            $all_product_id = implode(',', $product_id_array);
                        }else{
                           $all_product_id =  $post->product_id;
                        }
                        
                       
                        $exist_data = $this->common->database_product_exist_leathere($all_product_id, $item_type);

                        $exist_data_result = $this->general_model->getQueryRecords($exist_data);

                        if ($exist_data_result){
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal" data-path="product/delete" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }else{                            
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $product_id . '" data-path="product/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                    }

                    $cols .= '</div></div>';
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]          = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }
    }

    public function product_batchList(){

        $product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        /*if($this->session->userdata('bulk_success')){
            if($this->session->flashdata('email_send') != 'success')
            $data['bulk_success'] = $this->session->userdata('bulk_success');
            $this->session->unset_userdata('bulk_success');
        }elseif ($this->session->userdata('bulk_error')) {
            $data['bulk_error'] = $this->session->userdata('bulk_error');
            $this->session->unset_userdata('bulk_error');
        }*/

        if (!empty($this->input->post())){
            $columns = array(
                0 => 'product_id',
                1  => 'product_name',
                2  => 'product_batch',
                3  => 'product_mrp_price',
                4  => 'product_discount_value',
                5  => 'product_selling_price',
                6  => 'margin_discount_value',
                7  => 'product_gst_value',
                8  => 'product_basic_price'
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->product_batchlist_field($order,$dir);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;

            if (empty($this->input->post('search')['value'])){
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);

            }else{
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();
            $product_type_data = array();
            $product_type_data['rawmaterial'] = 'Raw Material';
            $product_type_data['semifinishedgoods'] = 'Semi Finished Goods';
            $product_type_data['finishedgoods'] = 'Finished Goods';

            if (!empty($posts)){
                foreach ($posts as $post){
                    $product_id                 = $this->encryption_url->encode($post->product_id);
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_batch'] = $post->product_batch;
                    $nestedData['mrp']  = $this->precise_amount($post->product_mrp_price,$access_common_settings[0]->amount_precision);
                    $nestedData['markdown_discount'] = ($post->product_discount_value != '' && $post->product_discount_value != 0 ? (float)$post->product_discount_value.'%' : '-');
                    $nestedData['selling_price'] = $this->precise_amount($post->product_selling_price,$access_common_settings[0]->amount_precision);
                    $nestedData['marginal_discount'] = ($post->margin_discount_value != '' && $post->margin_discount_value != 0 ? (float)$post->margin_discount_value.'%' : '-');
                    $nestedData['gst_Output'] =  ($post->product_gst_value != '' && $post->product_gst_value != 0 ? (float)$post->product_gst_value.'%' : '-');
                    $nestedData['basic_price'] = $this->precise_amount($post->product_basic_price,$access_common_settings[0]->amount_precision);
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';

                    if (in_array($data['product_module_id'], $data['active_edit'])){  

                        $cols .= '<span ><a  href="' . base_url('product/edit_batch/') . $product_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_category btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                    }

                    if (in_array($data['product_module_id'], $data['active_delete'])){

                        $item_type  = "product";
                        $exist_data = $this->common->database_product_exist($post->product_id, $item_type);

                        $exist_data_result = $this->general_model->getQueryRecords($exist_data);

                        if ($exist_data_result){
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#false_delete_modal" data-path="product/delete" data-delete_message="If you delete this record then its associated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }else{                            
                            $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its associated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $product_id . '" data-path="product/delete" data-return="product/product_batchList" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                        }
                    }

                    $cols .= '</div></div>';
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]          = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }else{
            $this->load->view('product/batchlist', $data);
        }

    }

    public function add_product_ajax(){
        // print_r($_FILES["image"]["name"]);
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        $this->load->helper('security');
        $product_type = $this->input->post('product_type');
        $title        = strtoupper(trim($this->input->post('product_name')));

        if ($product_type == 'asset'){
            $subgroup = "Asset";
        }else{
            $subgroup = "Product";
        }
        $url = '';
        if (isset($_FILES["product_image"]["name"]) && $_FILES["product_image"]["name"] != ""){

            $path_parts = pathinfo($_FILES["product_image"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/product_image/' . $this->session->userdata('SESS_BRANCH_ID'))){
                mkdir('./assets/product_image/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            } 
            $url = "assets/product_image/" . $this->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
            if (in_array($path_parts['extension'], array(
                            "jpg",
                            "jpeg",
                            "JPG",
                            "JPEG",
                            "png","PNG" ))){
                
                if (is_uploaded_file($_FILES["product_image"]["tmp_name"])){
                    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $url)){
                        $image_name = $image_path;
                    }
                }else{
                     $image_name = '';
                }
            }

        }else{
            $image_name = '';
        }
        $product_data = array(
            "product_code"           => $this->input->post('product_code'),
            "product_name"           => $this->input->post('product_name'),
            //"product_model_no"       => $this->input->post('product_model_no'),
            //"product_color"          => $this->input->post('product_color'),
            "product_hsn_sac_code"   => $this->input->post('product_hsn_sac_code'),
            "product_category_id"    => $this->input->post('product_category'),
            "product_subcategory_id" => $this->input->post('product_subcategory'),
            "product_quantity"       => $this->input->post('product_quantity'),
            "product_unit"           => $this->input->post('product_unit'),
            "product_price"          => $this->input->post('product_price'),
            // "product_tax_id"         => $this->input->post('product_tax'),
            "product_tax_value"      => $this->input->post('product_tax_value'),
            "product_tds_id"         => $this->input->post('tds_tax_product'),
            "product_tds_value"      => $this->input->post('product_tds_code'),
            "product_gst_id"         => $this->input->post('gst_tax_product'),
            "product_gst_value"      => $this->input->post('product_gst_code'),
            "product_details"        => $this->input->post('product_description'),
            "is_assets"              => $this->input->post('asset'),
            "is_varients"            => $this->input->post('varient'),
            "product_unit_id"        => $this->input->post('product_unit'),
            "product_discount_id"    => $this->input->post('product_discount'),
            "product_mrp_price"     => $this->input->post('product_mrp'),
            "product_selling_price" => $this->input->post('product_selling_price'),
            "product_sku"           => $this->input->post('product_sku'),
            "product_serail_no"     => $this->input->post('product_serial'),
            "product_batch"         => $this->input->post('product_batch'),
            'batch_parent_product_id' => $this->input->post('batch_parent_product_id'),
            'batch_serial' => $this->input->post('batch_serial'),
            "product_type"           => $product_type,
            "product_image"         => $image_name,
            "added_date"             => date('Y-m-d'),
            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID'));
        if($this->input->post('product_brand')){
            $product_data['brand_id'] = $this->input->post('product_brand');
        }
        
        if($this->input->post('product_opening_stock')){
            $product_data['product_opening_quantity'] = $this->input->post('product_opening_stock');
        }

        if ($id = $this->general_model->insertData('products', $product_data)){
            $update = array('batch_serial' => $this->input->post('batch_serial'));
            $this->general_model->updateData('products',$update,array('product_id' => $this->input->post('batch_parent_product_id')));
            /*$ecommerce = 1;
            if($ecommerce){
                $product_data['product_id'] = $id;
                $product_data['product_image'] = base_url().$url;
                $this->producthook->CreateProduct($product_data);

            }*/
            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $id,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Inserted');
            $this->general_model->insertData('log', $log_data);
        }else{
            $this->session->set_flashdata('fail', 'Product can not be Inserted.');
        }

        $data['product_name'] = $product_data['product_name'];
        $data['product_name'] = $product_data['product_name'];
        $data['product_id']   = $id;
        echo json_encode($data);
    }

    public function get_subcategory(){

        $id   = $this->input->post('id');
        $data = $this->general_model->getRecords('*', 'sub_category', array(
            'category_id'   => $id,
            'delete_status' => 0));
        echo json_encode($data);
    }

    public function get_product_use_code($product_id, $state_id, $country_id){

        $string = 'p.*,t.tax_value as tax_value';
        $table  = 'products p';
        $where  = array(
            'p.product_id' => $product_id);
        $join['tax t']    = 'p.product_tax_id = t.tax_id' . '#' . 'left';
        $data             = $this->general_model->getJoinRecords($string, $table, $where, $join);
        $data['discount'] = $this->general_model->getRecords('*', 'discount', array(
            'delete_status' => 0));
        $data2 = $this->general_model->getRecords('*', 'branch', array(
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')));
        $company_state_id   = $data2[0]->branch_state_id;
        $company_country_id = $data2[0]->branch_country_id;
        $tax_value          = $data[0]->tax_value;
        $gst                = "";

        if ($company_state_id == $state_id){
            $igst  = 0;
            $val   = bcdiv($tax_value, 2, 2);
            $calc1 = explode(".", $val);

            if ($calc1[1] == "00"){
                $cgst = $sgst = $calc1[0];
            }else{
                $cgst = $sgst = $val;
            }

            $gst = "cgst";
        }else{

            if ($company_country_id != $country_id){
                $igst = 0;
                $cgst = $sgst = 0;
            }else{

                if ($tax_value != null && $tax_value != ""){
                    $igst = $tax_value;
                }else{
                    $igst = 0;
                }

                $cgst = $sgst = 0;
            }

            $gst = "igst";
        }

        $data['tax_data'] = (object) [
            'igst' => $igst,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'gst'  => $gst];
        echo json_encode($data);
    }

    public function get_hsn_data(){
        $id   = $this->input->post('id');
        $data = $this->general_model->getRecords('*', 'hsn', array(
            'chapter_id' => $id));
        echo json_encode($data);
    }

    public function add(){

        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $data['product_module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['category_module_id']    = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id']         = $this->config->item('tax_module');
        $data['uqc_module_id']         = $this->config->item('uqc_module');
        $data['discount_module_id']         = $this->config->item('discount_module');
        $data['tax_gst']          = $this->tax_call_type('GST');
        $data['tax_tds']          = $this->tax_call_type('TCS');
        $data['product_category'] = $this->product_category_call();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_product_service_call('product');
        $data['chapter']          = $this->chapter_call();
        $data['tax_section']      = $this->tds_section_call();
        $data['hsn']              = $this->hsn_call();
        $data['discount']         = $this->discount_call();
        $data['brand']            = $this->brand_call();

        $access_settings          = $data['access_settings'];
        $primary_id               = "product_id";
        $table_name               = "products";
        $date_field_name          = "added_date";
        $current_date             = date('Y-m-d');
        
        $data['invoice_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);

        $data['varients_key']     = $this->general_model->getRecords('*', 'varients', array(
            'delete_status' => 0,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));

        $data['product_master']     = $this->general_model->getRecords('*', 'tbl_product_master', array(
            'status' => 1,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));

        // $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
        //     'delete_status' => 0,
        //     'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));

        $this->load->view('product/add', $data);
    }

    public function add_product(){
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        /*echo "<pre>";
        print_r($this->input->post());exit;*/

        $product_code = $this->input->post('product_code');
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $product_name = $this->input->post('product_name');
        $url = '';
        if (isset($_FILES["product_image"]["name"]) && $_FILES["product_image"]["name"] != ""){

            $path_parts = pathinfo($_FILES["product_image"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/product_image/' . $this->session->userdata('SESS_BRANCH_ID'))){
                mkdir('./assets/product_image/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            } 
            $url = "assets/product_image/" . $this->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
            if (in_array($path_parts['extension'], array(
                            "jpg",
                            "jpeg",
                             "JPG",
                            "JPEG",
                            "png","PNG" ))){

                if (is_uploaded_file($_FILES["product_image"]["tmp_name"])){
                    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $url)){
                        $image_name = $image_path;
                    }
                }else{
                    $image_name = '';
                }
            }

        }else{
            $image_name = '';
        }

        $product_data = array(
            "product_code"           => $this->input->post('product_code'),
            "product_name"           => $this->input->post('product_name'),
            "product_hsn_sac_code"   => $this->input->post('product_hsn_sac_code'),
            "product_category_id"    => $this->input->post('product_category'),
            "product_subcategory_id" => $this->input->post('product_subcategory'),
            "product_quantity"       => $this->input->post('product_quantity'),
            "product_unit"           => $this->input->post('product_unit'),
            "product_price"          => $this->input->post('product_price'),
            "product_tds_id"         => $this->input->post('tds_tax_product'),
            "product_tds_value"      => rtrim($this->input->post('product_tds_code'),'%'),
            "product_gst_id"         => $this->input->post('gst_tax_product'),
            "product_gst_value"      => rtrim($this->input->post('product_gst_code'),'%'),
            "product_discount_id"    => $this->input->post('product_discount'),
            "product_details"        => $this->input->post('product_description'),
            "is_assets"              => $this->input->post('asset'),
            "is_varients"            => $this->input->post('varient'),
            "product_unit_id"        => $this->input->post('product_unit'),
            "product_type"          => $this->input->post('product_type'),
            "product_mrp_price"     => $this->input->post('product_mrp'),
            "product_selling_price" => $this->input->post('product_selling_price'),
            "product_sku"           => $this->input->post('product_sku'),
            "product_serail_no"     => $this->input->post('product_serial'),
            "product_image"         => $image_name,
            "added_date"             => date('Y-m-d'),
            "product_batch"         => $this->input->post('product_batch'),
            'batch_parent_product_id' => $this->input->post('batch_parent_product_id'),
            /*'batch_serial' => $this->input->post('batch_serial'),*/
            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
        );

        if($this->input->post('batch_serial')){
            $product_data['batch_serial'] = $this->input->post('batch_serial');
        }

        if($this->input->post('product_batch') > 1){
            $product_data['product_code'] = $this->input->post('batch_parent_product_code');
        }

        if($this->input->post('margin_discount_value')){
            $product_data['margin_discount_value'] = $this->input->post('margin_discount_value');
            $product_data['margin_discount_id'] = $this->input->post('margin_discount');
        }

        if($this->input->post('product_discount_value')){
            $product_data['product_discount_value'] = $this->input->post('product_discount_value');
        }

        if($this->input->post('product_basic_price')){
            $product_data['product_basic_price'] = $this->input->post('product_basic_price');
        }

        if($this->input->post('profit_margin')){
            $product_data['product_profit_margin'] = $this->input->post('profit_margin');
        }

        if($this->input->post('product_brand')){
            $product_data['brand_id'] = $this->input->post('product_brand');
        }

        if($this->input->post('product_opening_stock')){
            $product_data['product_opening_quantity'] = $this->input->post('product_opening_stock');
        }

        if($this->input->post('product_packing')){
            $product_data['packing'] = $this->input->post('product_packing');
        }

        if($this->input->post('exp_date')){
            $product_data['exp_date'] = date('Y-m-d',strtotime($this->input->post('exp_date')));
        }

        if($this->input->post('mfg_date')){
            $product_data['mfg_date'] = date('Y-m-d',strtotime($this->input->post('mfg_date')));
        }

        if($this->input->post('equal_uom')){
            $product_data['equal_unit_number'] = $this->input->post('equal_uom');
        }

        if($this->input->post('product_equal_unit')){
            $product_data['equal_uom_id'] = $this->input->post('product_equal_unit');
        }

        /*echo "<pre>";
        print_r($product_data);exit();*/
        if ($product_id = $this->general_model->insertData('products', $product_data)){
            /* update batch range in parent product */
            $update = array('batch_serial' => $this->input->post('batch_serial'));
            $this->general_model->updateData('products',$update,array('product_id' => $this->input->post('batch_parent_product_id')));
            // update product id in combination table

            $update_compina_data = array('product_id' => $product_id);
            $this->general_model->updateData('product_combinations', $update_compina_data, array(
            'product_code' => $product_code, 'branch_id' => $branch_id));

            $a = 1;
            // batch insert for product varients
            $insert_product = array();
            $ecomm_variant_product = array();
            if(isset($_POST['combination'])){
                $product_combination_ids = $_POST['combination'];
                $length = count($_POST['combination']);
           
                for ($i = 0; $i < $length; $i++) {
                    $combination_id = $product_combination_ids[$i];
                    $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                    'combination_id'   => $combination_id,
                    'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                    
                    $combination_name = $combination_data[0]->combinations;
                    $varient_value_id = $combination_data[0]->varient_value_id;

                    $insert_product[$i]["product_code"] = $product_code.'-0'.$a;
                    if($this->session->userdata('SESS_FIRM_ID') == $this->config->item('LeatherCraft')) $insert_product[$i]["product_code"] = $product_code;
                    $insert_product[$i]["product_name"] = $product_name.'/'.$combination_name;
                    $insert_product[$i]["product_hsn_sac_code"] = $this->input->post('product_hsn_sac_code');
                    $insert_product[$i]["product_category_id"] = $this->input->post('product_category');
                    $insert_product[$i]["product_subcategory_id"] = $this->input->post('product_subcategory');
                    $insert_product[$i]["product_quantity"] = $this->input->post('product_quantity');
                    $insert_product[$i]["product_unit"] = $this->input->post('product_unit');
                    $insert_product[$i]["product_price"] = $this->input->post('product_price');
                    $insert_product[$i]["product_tds_id"] = $this->input->post('tds_tax_product');
                    $insert_product[$i]["product_tds_value"] = rtrim($this->input->post('product_tds_code'),'%');
                    $insert_product[$i]["product_gst_id"] = $this->input->post('gst_tax_product');
                    $insert_product[$i][ "product_gst_value"] =  rtrim($this->input->post('product_gst_code'),'%');
                    $insert_product[$i]["product_discount_id"] = $this->input->post('product_discount');
                    $insert_product[$i]["product_details"] = $this->input->post('product_description');
                    $insert_product[$i]["is_assets"] = $this->input->post('asset');
                    $insert_product[$i]["is_varients"] = 'N';
                    $insert_product[$i]["product_unit_id"] = $this->input->post('product_unit');
                    $insert_product[$i]["added_date"] = date('Y-m-d');
                    $insert_product[$i]["added_user_id"] = $this->session->userdata('SESS_USER_ID');
                    $insert_product[$i]["branch_id"] = $this->session->userdata('SESS_BRANCH_ID');
                    $insert_product[$i]["product_type"] = $this->input->post('product_type');
                    $insert_product[$i]["product_mrp_price" ] = $this->input->post('product_mrp');
                    $insert_product[$i]["product_selling_price"] = $this->input->post('product_selling_price');
                    $insert_product[$i]["product_sku"] = $this->input->post('product_sku');
                    $insert_product[$i]["product_serail_no"] = $this->input->post('product_serial');
                    $insert_product[$i]["product_image"] = $image_name;
                    $insert_product[$i]["product_batch"] = $this->input->post('product_batch');
                    $insert_product[$i]["product_combination_id"] = $combination_id; 
                    $update_compina_status = array('status' => 'Y');

                    if($this->input->post('margin_discount_value')){
                        $insert_product[$i]['margin_discount_value'] = $this->input->post('margin_discount_value');
                        $insert_product[$i]['margin_discount_id'] = $this->input->post('margin_discount');
                    }

                    if($this->input->post('product_discount_value')){
                        $insert_product[$i]['product_discount_value'] = $this->input->post('product_discount_value');
                    }

                    if($this->input->post('product_basic_price')){
                        $insert_product[$i]['product_basic_price'] = $this->input->post('product_basic_price');
                    }

                    if($this->input->post('profit_margin')){
                        $insert_product[$i]['product_profit_margin'] = $this->input->post('profit_margin');
                    }

                    if($this->input->post('product_brand')){
                        $insert_product[$i]['brand_id'] = $this->input->post('product_brand');
                    }

                    if($this->input->post('product_opening_stock')){
                        $insert_product[$i]['product_opening_quantity'] = $this->input->post('product_opening_stock');
                    }

                    if($this->input->post('mfg_date')){
                        $product_data['mfg_date'] = date('Y-m-d',strtotime($this->input->post('mfg_date')));
                    }

                    $this->general_model->updateData('product_combinations', $update_compina_status, array('combination_id' => $combination_id));   
                    $a++;
                    $variant_id = $this->general_model->insertData('products', $insert_product[$i]);

                    $insert_product[$i]['variant_id'] = $variant_id;
                    $ecomm_variant_product[$i] = $insert_product[$i];
                    $ecomm_variant_product[$i]['varient_value_id'] = $varient_value_id;
                }
                
                /*$this->db->insert_batch('products', $insert_product);*/
            }
       
            $l = 1;
            $n = 1;
            if($this->input->post('varient') == 'Y'){
                if(isset($_POST['varient_key']) ){ 
                    if(!empty($_POST['varient_key'])){                         
                        $data_product_var_val = array();
                        $length = count($_POST['varient_key']);
                        $varients_key = $_POST['varient_key'];
                
                        for ($k = 0; $k < $length; $k++) {
                            $varients_key_id = $varients_key[$k];
                            if($varients_key_id != ''){
                                $length_value = count($_POST['varient_value_'. $l]);
                                $varients_id = $_POST['varient_value_'. $l];                
                                    for ($m = 0; $m < $length_value; $m++) {
                                        $data_product_var_val[$n]['varients_value_id'] = $varients_id[$m];
                                        $data_product_var_val[$n]['varients_id'] =  $varients_key_id;
                                        $data_product_var_val[$n]['product_varients_id'] =  $product_id;
                                        $data_product_var_val[$n]['delete_status'] =  0;
                                        $n++;
                                    }
                                $l++;
                                
                                $this->db->insert_batch('product_varients_value', $data_product_var_val);
                            }
                        }
                    } 
                }
            }

            /*$ecommerce = 1;
            if($ecommerce){
                $product_data['variants'] = $ecomm_variant_product;
                $product_data['product_id'] = $product_id;
                $product_data['product_image'] = base_url().$url;
                $this->producthook->CreateProduct($product_data);
            }*/

            $successMsg = 'Product Added Successfully';
            $this->session->set_flashdata('product_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $product_id,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Inserted');
            $this->general_model->insertData('log', $log_data);
            redirect('product', 'refresh');
        }else{
            $errorMsg = 'Product Add Unsuccessful';
            $this->session->set_flashdata('product_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Product can not be Inserted.');
            redirect("product", 'refresh');
        }
        redirect("product", 'refresh');
    }

    public function product_add(){

        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['product_category'] = $this->product_category_call();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_product_service_call('product');
        $data['chapter']          = $this->chapter_call();
        $data['tds_section']      = $this->tds_section_call();        
        $data['hsn']              = $this->hsn_call();
        $access_settings          = $data['access_settings'];
        $primary_id               = "product_inventory_id";
        $table_name               = "product_inventory";
        $date_field_name          = "added_date";
        $current_date             = date('Y-m-d');
        $data['invoice_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['varients_key']     = $this->general_model->getRecords('*', 'varients', array(
            'delete_status' => 0,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));

        $this->load->view('product_inventory/add', $data);
    }

    public function add_bulk_upload_product()
    {
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/productCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();

        if (!$this->upload->do_upload('bulk_product')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_product',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        } else {
            $product_module_id = $this->config->item('product_module');
            $data['module_id'] = $product_module_id;
            $modules           = $this->modules;
            $privilege         = "add_privilege";
            $data['privilege'] = $privilege;
            $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

            /* presents all the needed */
            $data = array_merge($data, $section_modules);

            /* presents all the needed */
            $Updata = array('uploadData' => $this->upload->data());

            if (!empty($Updata['uploadData']['file_name'])) {
                $import_xls_file = $Updata['uploadData']['file_name'];
                $inputFileName = $path . $import_xls_file;
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);               
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    if(!empty($allDataInSheet)){
                        if(strtolower($allDataInSheet[1]['A']) == 'product name' && strtolower($allDataInSheet[1]['B']) == 'product type' && strtolower($allDataInSheet[1]['C']) == 'product hsn sac code' && strtolower($allDataInSheet[1]['D']) == 'category' && strtolower($allDataInSheet[1]['E']) == 'subcategory' && strtolower($allDataInSheet[1]['F']) == 'unit of measurement' && strtolower($allDataInSheet[1]['G']) == 'gst tax percentage' && strtolower($allDataInSheet[1]['H']) == 'tcs tax percentage' && strtolower($allDataInSheet[1]['I']) == 'selling price' && strtolower($allDataInSheet[1]['J']) == 'mrp' && strtolower($allDataInSheet[1]['K']) == 'serial number' && strtolower($allDataInSheet[1]['L']) == 'description' && strtolower($allDataInSheet[1]['M']) == 'discount'){

                                $header_row = array_shift($allDataInSheet);
                                $product_exist = $this->general_model->GetProductName();
                                $product_exist = array_column($product_exist, 'product_name', 'product_name');
                                $hsn = $this->general_model->hsn_call_product_bulk();
                                $hsn = array_column($hsn, 'hsn_code', 'hsn_code');
                                $category = $this->general_model->GetCategory_bulk('product');
                                $category = array_column($category, 'category_id', 'category_name');
                                $sub_category = $this->general_model->GetSubCategory_bulk('product');
                                $sub_category_id = array_column($sub_category, 'category_id_sub','subcategory_name');
                                $sub_category= array_column($sub_category, 'sub_category_id', 'subcategory_name');
                                $uom = $this->general_model->Get_uqc_bulk_latest('product');
                                $uom = array_column($uom, 'uom_id', 'uom');
                                $gst = $this->general_model->Get_tax_bulk('GST');
                                $gst = array_column($gst, 'tax_id', 'tax_value');
                                $tcs = $this->general_model->Get_tax_bulk('TCS');
                                $tcs = array_column($tcs, 'tax_id', 'tax_value');
                                $discount = $this->general_model->Get_discount_bulk();
                                $discount = array_column($discount, 'discount_id', 'discount_value');
                                //$hsn = $this->array_flatten($hsn);
                                $access_settings          = $data['access_settings'];
                                $primary_id               = "product_id";
                                $table_name               = "products";
                                $date_field_name          = "added_date";
                                $current_date             = date('Y-m-d');
                                $error_array = array();
                                foreach($allDataInSheet as $row){ 
                                    $product_name = strtolower(trim($row['A']));
                                    $product_type = strtolower(trim($row['B']));
                                    $hsn_number = trim($row['C']);
                                    $name_category= strtolower(trim($row['D']));
                                    $name_subcategory= strtolower(trim($row['E']));
                                    $unit_of_measurement= strtolower(trim($row['F']));
                                    $product_gst= trim($row['G']);
                                    $product_tcs= trim($row['H']);
                                    $discount_product = trim($row['M']);
                                    $product_sku = '';
                                    $product_category_id = '';
                                    $product_subcategory_id = '';
                                    $is_add = true;
                                    $error = '';
                                    $tcs_product_id ='';
                                    $gst_product_id = '';
                                    $discount_product_id = '';
                                    /*$product_id = 0;
                                    $product_batch = '';*/
                                    $product_code   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                    $batch = $this->get_bulk_check_product($product_name,0);
                                    if(count($batch) > 0){
                                        $batch_num = $batch[0]->num;
                                        $number = intval($batch_num)+1;
                                        $product_batch = 'BATCH-0'.$number;
                                        $batch_parent_product_id = $batch[0]->product_id;
                                        $batch_product_code  = $batch[0]->product_code;
                                        $batch_serial = $number;
                                    } else {
                                        $product_batch = "BATCH-01";
                                        $batch_parent_product_id = 0;
                                        $batch_product_code = 0;
                                        $batch_serial = 1;
                                    }
                                    if($product_type != '' && !empty($product_type)){
                                        if(($product_type == 'semifinishedgoods' || $product_type == 'finishedgoods' || $product_type == 'rawmaterial')){
                                            if($hsn_number != '' && !empty($hsn_number)){
                                                if(in_array($hsn_number, $hsn)){
                                                    if($name_category !='' && !empty($name_category)){
                                                        if(isset($category[$name_category]) && $is_add == true){
                                                           $product_category_id = $category[$name_category];
                                                           $product_sku = $this->get_product_sku_bulk($product_code,$product_category_id);
                                                           if($name_subcategory != '' || !empty($name_subcategory)){
                                                                if(isset($sub_category_id[$name_subcategory])){
                                                                    $subcategory_cat_value = $sub_category_id[$name_subcategory];
                                                                    if($product_category_id == $subcategory_cat_value){
                                                                        $product_subcategory_id = $sub_category[$name_subcategory];
                                                                    }else {
                                                                        $is_add = false;
                                                                        $error = "SubCategory Name is Not Exist! For Entered Category Name";
                                                                        $error_log .= $row['E'].' Undefined SubCategory Name! <br>';
                                                                    }
                                                                }else {
                                                                    $is_add = false;
                                                                    $error = "SubCategory Name is Not Exist! Please Update Your SubCategory Name";
                                                                    $error_log .= $row['E'].' Undefined SubCategory Name! <br>';
                                                                }  
                                                            }            
                                                        }else{
                                                            $is_add = false;
                                                            $error = "Category Name is Not Exist! Please Update Your Category Name";
                                                            $error_log .= $row['D'].' Undefined Category Name! <br>';
                                                        }
                                                        if(($unit_of_measurement !='' || !empty($unit_of_measurement)) && $is_add == true){
                                                            if(isset($uom[$unit_of_measurement])){
                                                                $product_unit_id = $uom[$unit_of_measurement];
                                                                if($product_gst != '' && !empty($product_gst)){
                                                                    $product_gst = $this->precise_amount($product_gst, 2);
                                                                    if(isset($gst[$product_gst])){
                                                                        $gst_product_id = $gst[$product_gst];
                                                                        if($product_tcs != '' && !empty($product_tcs)){
                                                                            $product_tcs = $this->precise_amount($product_tcs, 2);
                                                                            if(isset($tcs[$product_tcs])){
                                                                                $tcs_product_id = $tcs[$product_tcs];
                                                                                if($discount_product != '' && !empty($discount_product)){
                                                                                    $discount_product = $this->precise_amount($discount_product, 2);
                                                                                    if(isset($discount[$discount_product])){
                                                                                        $discount_product_id = $discount[$discount_product];
                                                                                    }else{
                                                                                        $is_add = false;
                                                                                        $error = "Discount Value is Not Exist! Please Update Your Discount value";
                                                                                        $error_log .= $row['M'].' Undefined Discount Value! <br>'; 
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                $is_add = false;
                                                                                $error = "TCS Value is Not Exist! Please Update Your TCS value";
                                                                                $error_log .= $row['H'].' Undefined TCS Value! <br>';
                                                                            }
                                                                        } 
                                                                    } else {
                                                                        $is_add = false;
                                                                        $error = "GST Value is Not Exist! Please Update Your GST value";
                                                                        $error_log .= $row['G'].' Undefined GST Value! <br>';
                                                                    }
                                                                }
                                                            } else {
                                                                $is_add = false;
                                                                $error = "Unit_Of_Measurement Name is Not Exist! Please Update Your Unit_Of_Measurement Name";
                                                                $error_log .= $row['F'].' Undefined Unit_Of_Measurement Name! <br>';
                                                            }
                                                        }elseif($is_add == true){
                                                            $is_add = false;
                                                             $error = "Unit_Of_Measurement Name Should Not Empty";
                                                             $error_log .= $row['E'].' Unit_Of_Measurement Name is Not Exist! <br>';
                                                        }
                                                    }else{
                                                        $is_add = false;
                                                        $error = "Category Name is Empty";
                                                    }
                                                }else{
                                                    $is_add = false;
                                                    $error = "HSN Number is Not Exist! Please Update HSN Data";
                                                    $error_log .= $row['C'].' Undefined HSN Number! <br>';
                                                }
                                            }else{
                                                $is_add = false;
                                                $error = "HSN number should not empty!";
                                                $error_log .= $row['B'].'HSN number should not empty! <br>';
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Incorrect Product Type!";
                                            $error_log .= $row['B'].' Incorrect Product Type! <br>';
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Product Type Is Empty!";
                                        $error_log .= $row['B'].' Product Type Is Empty!';
                                    }
                                    /*if(in_array($product_name, $product_exist)) {
                                        $is_add = false;
                                    }*/                   
                                    
                                    if($is_add){
                                        $headers = array(
                                            'product_code' => $product_code,
                                            "product_batch" => $product_batch,
                                            "product_hsn_sac_code" => trim($row['C']),
                                            "product_sku" => $product_sku,
                                            "product_serail_no" => trim($row['K']),
                                            "product_name" => trim($row['A']),
                                            "product_unit" => $product_unit_id,
                                            "product_unit_id" => $product_unit_id,
                                            "product_mrp_price" => trim($row['J']),
                                            "product_selling_price" => trim($row['I']),
                                            "product_category_id" => $product_category_id,
                                            "product_subcategory_id" => $product_subcategory_id,
                                            "product_tds_id" => $tcs_product_id,
                                            "product_tds_value" => trim($row['H']),
                                            "product_gst_id" => $gst_product_id,
                                            "product_gst_value" => trim($row['G']),
                                            "product_details" => trim($row['L']),
                                            "product_discount_id" => $discount_product_id,
                                            "product_type" => $product_type,
                                            'batch_parent_product_id' => $batch_parent_product_id,
                                            'batch_serial' => $batch_serial,
                                            "is_assets" => 'N' ,
                                            "is_varients" => 'N',
                                            "delete_status" => 0,
                                            "added_date" => date('Y-m-d'),
                                            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        );

                                        if($batch_parent_product_id){
                                            $headers['product_code'] = $batch_product_code;
                                        }
                                        /*$product_id = $this->db->insert($table_name,$headers);*/
                                        
                                        if($product_id = $this->general_model->insertData($table_name, $headers)){
                                            $update = array('batch_serial' => $batch_serial);
                                            $this->general_model->updateData('products',$update,array('product_id' => $batch_parent_product_id));
                                        }
                                        /*$ecommerce = 1;
                                        if($ecommerce){
                                            $headers['product_id'] = $product_id;
                                            $headers['product_image'] = '';
                                            $this->producthook->CreateProduct($headers);
                                        }*/
                                    } else {
                                        $error_array[] = $error_log;
                                    }
                                    /* $row['Error'] = $added_error;*/
                                    if(!$is_add && !empty($row)){
                                        array_unshift($row,$error);
                                        array_push($errors_email, array_values($row));
                                    }
                                } 
                                $log_data = array(
                                    'user_id'           => $this->session->userdata('user_id'),
                                    'table_id'          => 0,
                                    'table_name'        => 'products',
                                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                                    'message'           => 'Bulk Product Inserted');
                                $this->general_model->insertData('log', $log_data);
                                if(!empty($error_array)){
                                    $errorMsg = implode('<br>', $error_array);
                                    $this->session->set_flashdata('bulk_error_product',$errorMsg);
                                    /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array));   */ 
                                }else{
                                    $successMsg = 'Product imported successfully.';
                                    $this->session->set_flashdata('bulk_success_product',$successMsg);
                                    /*$this->session->set_userdata('bulk_success', $successMsg); */ 
                                }
                        }else{
                            $this->session->set_flashdata('bulk_error_product',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }      
                    }else{
                        $this->session->set_flashdata('bulk_error_product',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }      
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_product',"Error on file upload, please try again.");
                    /*$this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');*/
                }
            }
        }
        if(!empty($errors_email)){
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = $this->session->userdata('SESS_EMAIL');
            /*$to = 'harish.sr@aavana.in';*/
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
            $resp = $this->send_csv_mail($errors_email,'Product Bulk Import Error Logs, <br><br> PFA,',"Product bulk upload error logs in <{$import_xls_file}>",$to);
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
            $this->session->set_flashdata('bulk_error_product',"Error email has been sent to registered email ID.");
            /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
        }
        redirect("product", 'refresh');
    }

    public function add_bulk_upload_product_sanath()
    {
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/productCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();

        if (!$this->upload->do_upload('bulk_product')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_product',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        } else {
            $product_module_id = $this->config->item('product_module');
            $data['module_id'] = $product_module_id;
            $modules           = $this->modules;
            $privilege         = "add_privilege";
            $data['privilege'] = $privilege;
            $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

            /* presents all the needed */
            $data = array_merge($data, $section_modules);

            /* presents all the needed */
            $Updata = array('uploadData' => $this->upload->data());

            if (!empty($Updata['uploadData']['file_name'])) {
                $import_xls_file = $Updata['uploadData']['file_name'];
                $inputFileName = $path . $import_xls_file;
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);               
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    if(!empty($allDataInSheet)){
                        if(strtolower($allDataInSheet[1]['A']) == 'product name' && strtolower($allDataInSheet[1]['B']) == 'product type' && strtolower($allDataInSheet[1]['C']) == 'product hsn sac code' && strtolower($allDataInSheet[1]['D']) == 'category' && strtolower($allDataInSheet[1]['E']) == 'subcategory' && strtolower($allDataInSheet[1]['F']) == 'unit of measurement' && strtolower($allDataInSheet[1]['G']) == 'gst tax percentage' && strtolower($allDataInSheet[1]['H']) == 'tcs tax percentage' && strtolower($allDataInSheet[1]['I']) == 'selling price' && strtolower($allDataInSheet[1]['J']) == 'mrp' && strtolower($allDataInSheet[1]['K']) == 'serial number' && strtolower($allDataInSheet[1]['L']) == 'description' && strtolower($allDataInSheet[1]['M']) == 'discount' && strtolower($allDataInSheet[1]['N']) == 'brand' && strtolower($allDataInSheet[1]['O']) == 'opening stock'){

                                $header_row = array_shift($allDataInSheet);
                                $product_exist = $this->general_model->GetProductName();
                                $product_exist = array_column($product_exist, 'product_name', 'product_name');
                                $hsn = $this->general_model->hsn_call_product_bulk();
                                $hsn = array_column($hsn, 'hsn_code', 'hsn_code');
                                $brand = $this->general_model->GetBrand_bulk('product');
                                $brand = array_column($brand, 'brand_id', 'brand_name');
                                $category = $this->general_model->GetCategory_bulk('product');
                                $category = array_column($category, 'category_id', 'category_name');
                                $sub_category = $this->general_model->GetSubCategory_bulk('product');
                                $sub_category_id = array_column($sub_category, 'category_id_sub','subcategory_name');
                                $sub_category= array_column($sub_category, 'sub_category_id', 'subcategory_name');
                                $uom = $this->general_model->Get_uqc_bulk_latest('product');
                                $uom = array_column($uom, 'uom_id', 'uom');
                                $gst = $this->general_model->Get_tax_bulk('GST');
                                $gst = array_column($gst, 'tax_id', 'tax_value');
                                $tcs = $this->general_model->Get_tax_bulk('TCS');
                                $tcs = array_column($tcs, 'tax_id', 'tax_value');
                                $discount = $this->general_model->Get_discount_bulk();
                                $discount = array_column($discount, 'discount_id', 'discount_value');
                                //$hsn = $this->array_flatten($hsn);
                                $access_settings          = $data['access_settings'];
                                $primary_id               = "product_id";
                                $table_name               = "products";
                                $date_field_name          = "added_date";
                                $current_date             = date('Y-m-d');
                                $error_array = array();
                                foreach($allDataInSheet as $row){ 
                                    $product_name = strtolower(trim($row['A']));
                                    $product_type = strtolower(trim($row['B']));
                                    $hsn_number = trim($row['C']);
                                    $name_category= strtolower(trim($row['D']));
                                    $name_subcategory= strtolower(trim($row['E']));
                                    $unit_of_measurement= strtolower(trim($row['F']));
                                    $product_gst= trim($row['G']);
                                    $product_tcs= trim($row['H']);
                                    $discount_product = trim($row['M']);
                                    $brand_name = trim($row['N']);
                                    $opening_stock = trim($row['O']);
                                    $product_sku = '';
                                    $product_category_id = '';
                                    $product_subcategory_id = '';
                                    $is_add = true;
                                    $error = '';
                                    $tcs_product_id ='';
                                    $gst_product_id = '';
                                    $discount_product_id = '';
                                    $product_unit_id = '';
                                    /*$product_id = 0;
                                    $product_batch = '';*/
                                    $product_code   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                    $batch = $this->get_bulk_check_product($product_name,0);
                                    if(count($batch) > 0){
                                        $batch_num = $batch[0]->num;
                                        $number = intval($batch_num)+1;
                                        $product_batch = 'BATCH-0'.$number;
                                        $batch_parent_product_id = $batch[0]->product_id;
                                        $batch_product_code = $batch[0]->product_code;
                                        $batch_serial = $number;
                                    } else {
                                        $product_batch = "BATCH-01";
                                        $batch_parent_product_id = 0;
                                        $batch_serial = 1;
                                    }
                                    if($product_type != '' && !empty($product_type)){
                                        if(($product_type == 'semifinishedgoods' || $product_type == 'finishedgoods' || $product_type == 'rawmaterial')){
                                            if($hsn_number != '' && !empty($hsn_number)){
                                                if(in_array($hsn_number, $hsn)){
                                                    if($name_category !='' && !empty($name_category)){
                                                        if(isset($category[$name_category]) && $is_add == true){
                                                           $product_category_id = $category[$name_category];
                                                           $product_sku = $this->get_product_sku_bulk($product_code,$product_category_id);
                                                           if($name_subcategory != '' || !empty($name_subcategory)){
                                                                if(isset($sub_category_id[$name_subcategory])){
                                                                    $subcategory_cat_value = $sub_category_id[$name_subcategory];
                                                                    if($product_category_id == $subcategory_cat_value){
                                                                        $product_subcategory_id = $sub_category[$name_subcategory];
                                                                    }else {
                                                                        $is_add = false;
                                                                        $error = "SubCategory Name is Not Exist! For Entered Category Name";
                                                                        $error_log .= $row['E'].' Undefined SubCategory Name! <br>';
                                                                    }
                                                                }else {
                                                                    $is_add = false;
                                                                    $error = "SubCategory Name is Not Exist! Please Update Your SubCategory Name";
                                                                    $error_log .= $row['E'].' Undefined SubCategory Name! <br>';
                                                                }  
                                                            }            
                                                        }else{
                                                            $is_add = false;
                                                            $error = "Category Name is Not Exist! Please Update Your Category Name";
                                                            $error_log .= $row['D'].' Undefined Category Name! <br>';
                                                        }
                                                        if(($unit_of_measurement !='' || !empty($unit_of_measurement)) && $is_add == true){
                                                            if(isset($uom[$unit_of_measurement])){
                                                                $product_unit_id = $uom[$unit_of_measurement];
                                                                if($product_gst != '' && !empty($product_gst)){
                                                                    $product_gst = $this->precise_amount($product_gst, 2);
                                                                    if(isset($gst[$product_gst])){
                                                                        $gst_product_id = $gst[$product_gst];
                                                                        if($product_tcs != '' && !empty($product_tcs)){
                                                                            $product_tcs = $this->precise_amount($product_tcs, 2);
                                                                            if(isset($tcs[$product_tcs])){
                                                                                $tcs_product_id = $tcs[$product_tcs];
                                                                                if($discount_product != '' && !empty($discount_product)){
                                                                                    $discount_product = $this->precise_amount($discount_product, 2);
                                                                                    if(isset($discount[$discount_product])){
                                                                                        $discount_product_id = $discount[$discount_product];
                                                                                    }else{
                                                                                        $is_add = false;
                                                                                        $error = "Discount Value is Not Exist! Please Update Your Discount value";
                                                                                        $error_log .= $row['M'].' Undefined Discount Value! <br>'; 
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                $is_add = false;
                                                                                $error = "TCS Value is Not Exist! Please Update Your TCS value";
                                                                                $error_log .= $row['H'].' Undefined TCS Value! <br>';
                                                                            }
                                                                        } 
                                                                    } else {
                                                                        $is_add = false;
                                                                        $error = "GST Value is Not Exist! Please Update Your GST value";
                                                                        $error_log .= $row['G'].' Undefined GST Value! <br>';
                                                                    }
                                                                }
                                                            } else {
                                                                $is_add = false;
                                                                $error = "Unit_Of_Measurement Name is Not Exist! Please Update Your Unit_Of_Measurement Name";
                                                                $error_log .= $row['F'].' Undefined Unit_Of_Measurement Name! <br>';
                                                            }
                                                        }elseif($is_add == true){
                                                            $is_add = false;
                                                             $error = "Unit_Of_Measurement Name Should Not Empty";
                                                             $error_log .= $row['E'].' Unit_Of_Measurement Name is Not Exist! <br>';
                                                        }
                                                    }else{
                                                        $is_add = false;
                                                        $error = "Category Name is Empty";
                                                    }
                                                }else{
                                                    $is_add = false;
                                                    $error = "HSN Number is Not Exist! Please Update HSN Data";
                                                    $error_log .= $row['C'].' Undefined HSN Number! <br>';
                                                }
                                            }else{
                                                $is_add = false;
                                                $error = "HSN number should not empty!";
                                                $error_log .= $row['B'].'HSN number should not empty! <br>';
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Incorrect Product Type!";
                                            $error_log .= $row['B'].' Incorrect Product Type! <br>';
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Product Type Is Empty!";
                                        $error_log .= $row['B'].' Product Type Is Empty!';
                                    }
                                    /*if(in_array($product_name, $product_exist)) {
                                        $is_add = false;
                                    }*/
                                    if($is_add){
                                        if($brand_name != '' || !empty($brand_name)){
                                            
                                            if(!isset($brand[strtolower($brand_name)])){
                                                
                                                $is_add = false;
                                                $error = "Brand name is Not Exist! Please Update Your Brand name";
                                                $error_log .= $row['B'].' Brand name is Not Exist!';
                                            }else{
                                                $brand_id = $brand[strtolower($brand_name)];
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Brand name is empty!";
                                            $error_log .= $row['B'].' Brand name is empty!';
                                        }
                                    }

                                    if($is_add){
                                        $headers = array(
                                            'product_code' => $product_code,
                                            "product_batch" => $product_batch,
                                            "product_hsn_sac_code" => trim($row['C']),
                                            "product_sku" => $product_sku,
                                            "product_serail_no" => trim($row['K']),
                                            "product_name" => trim($row['A']),
                                            "product_unit" => $product_unit_id,
                                            "product_unit_id" => $product_unit_id,
                                            "product_mrp_price" => trim($row['J']),
                                            "product_selling_price" => trim($row['I']),
                                            "product_category_id" => $product_category_id,
                                            "product_subcategory_id" => $product_subcategory_id,
                                            "product_tds_id" => $tcs_product_id,
                                            "product_tds_value" => trim($row['H']),
                                            "product_gst_id" => $gst_product_id,
                                            "product_gst_value" => trim($row['G']),
                                            "product_details" => trim($row['L']),
                                            "product_discount_id" => $discount_product_id,
                                            "product_type" => $product_type,
                                            "brand_id" => $brand_id,
                                            "product_opening_quantity" => $opening_stock,
                                            'batch_parent_product_id' => $batch_parent_product_id,
                                            'batch_serial' => $batch_serial,
                                            "is_assets" => 'N' ,
                                            "is_varients" => 'N',
                                            "delete_status" => 0,
                                            "added_date" => date('Y-m-d'),
                                            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        );

                                        if($batch_parent_product_id){
                                            $headers['product_code'] = $batch_product_code;
                                        }
                                        /*$product_id = $this->db->insert($table_name,$headers);*/
                                        if($product_id = $this->general_model->insertData($table_name, $headers)){
                                            $update = array('batch_serial' => $batch_serial);
                                            $this->general_model->updateData('products',$update,array('product_id' => $batch_parent_product_id));
                                        }
                                        /*$ecommerce = 1;
                                        if($ecommerce){
                                            $headers['product_id'] = $product_id;
                                            $headers['product_image'] = '';
                                            $this->producthook->CreateProduct($headers);
                                        }*/
                                    } else {
                                        $error_array[] = $error_log;
                                    }
                                    /* $row['Error'] = $added_error;*/
                                    if(!$is_add && !empty($row)){
                                        array_unshift($row,$error);
                                        array_push($errors_email, array_values($row));
                                    }
                                } 
                                $log_data = array(
                                    'user_id'           => $this->session->userdata('user_id'),
                                    'table_id'          => 0,
                                    'table_name'        => 'products',
                                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                                    'message'           => 'Bulk Product Inserted');
                                $this->general_model->insertData('log', $log_data);
                                if(!empty($error_array)){
                                    $errorMsg = implode('<br>', $error_array);
                                    $this->session->set_flashdata('bulk_error_product',$errorMsg);
                                    /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array));   */ 
                                }else{
                                    $successMsg = 'Product imported successfully.';
                                    $this->session->set_flashdata('bulk_success_product',$successMsg);
                                    /*$this->session->set_userdata('bulk_success', $successMsg); */ 
                                }
                        }else{
                            $this->session->set_flashdata('bulk_error_product',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }      
                    }else{
                        $this->session->set_flashdata('bulk_error_product',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }      
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_product',"Error on file upload, please try again.");
                    /*$this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');*/
                }
            }
        }
        
        if(!empty($errors_email)){
            
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = $this->session->userdata('SESS_EMAIL');
            /*$to = 'harish.sr@aavana.in';*/
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
            $resp = $this->send_csv_mail($errors_email,'Product Bulk Import Error Logs, <br><br> PFA,',"Product bulk upload error logs in <{$import_xls_file}>",$to);
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
            $this->session->set_flashdata('bulk_error_product',"Error email has been sent to registered email ID.");
            /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
        }
       
        redirect("product", 'refresh');
    }

    public function bulkUploadProduct_Ilkka(){
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/productCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();

        if (!$this->upload->do_upload('bulk_product')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_product',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        } else {
            $product_module_id = $this->config->item('product_module');
            $data['module_id'] = $product_module_id;
            $modules           = $this->modules;
            $privilege         = "add_privilege";
            $data['privilege'] = $privilege;
            $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

            /* presents all the needed */
            $data = array_merge($data, $section_modules);

            /* presents all the needed */
            $Updata = array('uploadData' => $this->upload->data());

            if (!empty($Updata['uploadData']['file_name'])) {
                $import_xls_file = $Updata['uploadData']['file_name'];
                $inputFileName = $path . $import_xls_file;
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);               
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    if(!empty($allDataInSheet)){
                        if(strtolower($allDataInSheet[1]['A']) == 'product name' && strtolower($allDataInSheet[1]['B']) == 'product type' && strtolower($allDataInSheet[1]['C']) == 'product hsn sac code' && strtolower($allDataInSheet[1]['D']) == 'manufacture' && strtolower($allDataInSheet[1]['E']) == 'category' && strtolower($allDataInSheet[1]['F']) == 'subcategory' && strtolower($allDataInSheet[1]['G']) == 'packing' && strtolower($allDataInSheet[1]['H']) == 'gst tax percentage' && strtolower($allDataInSheet[1]['I']) == 'mrp' && strtolower($allDataInSheet[1]['J']) == 'marginal discount' && strtolower($allDataInSheet[1]['K']) == 'markdown discount' && strtolower($allDataInSheet[1]['L']) == 'serial number' && strtolower($allDataInSheet[1]['M']) == 'description' && strtolower($allDataInSheet[1]['N']) == 'mfg_date' && strtolower($allDataInSheet[1]['O']) == 'exp_date'){

                                $header_row = array_shift($allDataInSheet);
                                $product_exist = $this->general_model->GetProductName();
                                $product_exist = array_column($product_exist, 'product_name', 'product_name');
                                $hsn = $this->general_model->hsn_call_product_bulk();
                                $hsn = array_column($hsn, 'hsn_code', 'hsn_code');
                                $brand = $this->general_model->GetBrand_bulk('product');
                                $brand = array_column($brand, 'brand_id', 'brand_name');
                                $category = $this->general_model->GetCategory_bulk('product');
                                $category = array_column($category, 'category_id', 'category_name');
                                $sub_category = $this->general_model->GetSubCategory_bulk('product');
                                $sub_category_id = array_column($sub_category, 'category_id_sub','subcategory_name');
                                $sub_category= array_column($sub_category, 'sub_category_id', 'subcategory_name');
                                /*$uom = $this->general_model->Get_uqc_bulk_latest('product');
                                $uom = array_column($uom, 'uom_id', 'uom');*/
                                $gst = $this->general_model->Get_tax_bulk('GST');
                                $gst = array_column($gst, 'tax_id', 'tax_value');
                                /*$tcs = $this->general_model->Get_tax_bulk('TCS');
                                $tcs = array_column($tcs, 'tax_id', 'tax_value');*/
                                $discount = $this->general_model->Get_discount_bulk();
                                $discount = array_column($discount, 'discount_id', 'discount_value');
                                //$hsn = $this->array_flatten($hsn);
                                $access_settings          = $data['access_settings'];
                                $primary_id               = "product_id";
                                $table_name               = "products";
                                $date_field_name          = "added_date";
                                $current_date             = date('Y-m-d');
                                $error_array = array();
                                foreach($allDataInSheet as $row){ 
                                    $product_name = strtolower(trim($row['A']));
                                    $product_type = strtolower(trim($row['B']));
                                    $hsn_number = trim($row['C']);
                                    $manufacture= strtolower(trim($row['D']));
                                    $name_category= strtolower(trim($row['E']));
                                    $name_subcategory= strtolower(trim($row['F']));
                                    $packing= strtolower(trim($row['G']));
                                    $product_gst= trim($row['H']);
                                    $product_mrp= trim($row['I']);
                                    $marginal_dis= trim($row['J']);
                                    $markdown_dis= trim($row['K']);
                                    /*$discount_product = trim($row['M']);*/
                                    $product_sku = '';
                                    $product_category_id = '';
                                    $product_subcategory_id = '';
                                    $is_add = true;
                                    $error = '';
                                    $tcs_product_id ='';
                                    $gst_product_id = '';
                                    $discount_product_id = '';
                                    /*$product_id = 0;
                                    $product_batch = '';*/
                                    $product_code   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                    $batch = $this->get_bulk_check_product($product_name,0);
                                    if(count($batch) > 0){
                                        $batch_num = $batch[0]->num;
                                        $number = intval($batch_num)+1;
                                        $product_batch = 'BATCH-0'.$number;
                                        $batch_parent_product_id = $batch[0]->product_id;
                                        $batch_serial = $number;
                                    } else {
                                        $product_batch = "BATCH-01";
                                        $batch_parent_product_id = 0;
                                        $batch_serial = 1;
                                    }

                                    try {
                                        if($product_type != '' && !empty($product_type)){
                                            if(($product_type != 'semifinishedgoods' && $product_type != 'finishedgoods' && $product_type != 'rawmaterial')){
                                                $is_add = false;
                                                $error = "Incorrect Product Type!";
                                                $error_log .= $row['B'].' Incorrect Product Type! <br>';
                                                throw new Exception($error);
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Product Type Is Empty!";
                                            $error_log .= $row['B'].' Product Type Is Empty!';
                                            throw new Exception($error);
                                        }

                                        if($hsn_number != '' && !empty($hsn_number)){
                                            if(!in_array($hsn_number, $hsn)){
                                                $is_add = false;
                                                $error = "HSN Number is Not Exist! Please Update HSN Data";
                                                $error_log .= $row['C'].' Undefined HSN Number! <br>';
                                                throw new Exception($error);
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "HSN number should not empty!";
                                            $error_log .= $row['B'].'HSN number should not empty! <br>';
                                            throw new Exception($error);
                                        }

                                        if($name_category !='' && !empty($name_category)){

                                        }else{
                                            $is_add = false;
                                            $error = "Category Name is Empty";
                                            throw new Exception($error);
                                        }
                                    }catch (Exception $e) {
                                        
                                    }
                                    exit;
                                    if($product_type != '' && !empty($product_type)){
                                        if(($product_type == 'semifinishedgoods' || $product_type == 'finishedgoods' || $product_type == 'rawmaterial')){
                                            if($hsn_number != '' && !empty($hsn_number)){
                                                if(in_array($hsn_number, $hsn)){
                                                    if($name_category !='' && !empty($name_category)){
                                                        if(isset($category[$name_category]) && $is_add == true){
                                                           $product_category_id = $category[$name_category];
                                                           $product_sku = $this->get_product_sku_bulk($product_code,$product_category_id);
                                                           if($name_subcategory != '' || !empty($name_subcategory)){
                                                                if(isset($sub_category_id[$name_subcategory])){
                                                                    $subcategory_cat_value = $sub_category_id[$name_subcategory];
                                                                    if($product_category_id == $subcategory_cat_value){
                                                                        $product_subcategory_id = $sub_category[$name_subcategory];
                                                                    }else {
                                                                        $is_add = false;
                                                                        $error = "SubCategory Name is Not Exist! For Entered Category Name";
                                                                        $error_log .= $row['E'].' Undefined SubCategory Name! <br>';
                                                                    }
                                                                }else {
                                                                    $is_add = false;
                                                                    $error = "SubCategory Name is Not Exist! Please Update Your SubCategory Name";
                                                                    $error_log .= $row['E'].' Undefined SubCategory Name! <br>';
                                                                }  
                                                            }            
                                                        }else{
                                                            $is_add = false;
                                                            $error = "Category Name is Not Exist! Please Update Your Category Name";
                                                            $error_log .= $row['D'].' Undefined Category Name! <br>';
                                                        }
                                                        if(($unit_of_measurement !='' || !empty($unit_of_measurement)) && $is_add == true){
                                                            if(isset($uom[$unit_of_measurement])){
                                                                $product_unit_id = $uom[$unit_of_measurement];
                                                                if($product_gst != '' && !empty($product_gst)){
                                                                    $product_gst = $this->precise_amount($product_gst, 2);
                                                                    if(isset($gst[$product_gst])){
                                                                        $gst_product_id = $gst[$product_gst];
                                                                        if($product_tcs != '' && !empty($product_tcs)){
                                                                            $product_tcs = $this->precise_amount($product_tcs, 2);
                                                                            if(isset($tcs[$product_tcs])){
                                                                                $tcs_product_id = $tcs[$product_tcs];
                                                                                if($discount_product != '' && !empty($discount_product)){
                                                                                    $discount_product = $this->precise_amount($discount_product, 2);
                                                                                    if(isset($discount[$discount_product])){
                                                                                        $discount_product_id = $discount[$discount_product];
                                                                                    }else{
                                                                                        $is_add = false;
                                                                                        $error = "Discount Value is Not Exist! Please Update Your Discount value";
                                                                                        $error_log .= $row['M'].' Undefined Discount Value! <br>'; 
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                $is_add = false;
                                                                                $error = "TCS Value is Not Exist! Please Update Your TCS value";
                                                                                $error_log .= $row['H'].' Undefined TCS Value! <br>';
                                                                            }
                                                                        } 
                                                                    } else {
                                                                        $is_add = false;
                                                                        $error = "GST Value is Not Exist! Please Update Your GST value";
                                                                        $error_log .= $row['G'].' Undefined GST Value! <br>';
                                                                    }
                                                                }
                                                            } else {
                                                                $is_add = false;
                                                                $error = "Unit_Of_Measurement Name is Not Exist! Please Update Your Unit_Of_Measurement Name";
                                                                $error_log .= $row['F'].' Undefined Unit_Of_Measurement Name! <br>';
                                                            }
                                                        }elseif($is_add == true){
                                                            $is_add = false;
                                                             $error = "Unit_Of_Measurement Name Should Not Empty";
                                                             $error_log .= $row['E'].' Unit_Of_Measurement Name is Not Exist! <br>';
                                                        }
                                                    }else{
                                                        $is_add = false;
                                                        $error = "Category Name is Empty";
                                                    }
                                                }else{
                                                    $is_add = false;
                                                    $error = "HSN Number is Not Exist! Please Update HSN Data";
                                                    $error_log .= $row['C'].' Undefined HSN Number! <br>';
                                                }
                                            }else{
                                                $is_add = false;
                                                $error = "HSN number should not empty!";
                                                $error_log .= $row['B'].'HSN number should not empty! <br>';
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Incorrect Product Type!";
                                            $error_log .= $row['B'].' Incorrect Product Type! <br>';
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Product Type Is Empty!";
                                        $error_log .= $row['B'].' Product Type Is Empty!';
                                    }
                                    /*if(in_array($product_name, $product_exist)) {
                                        $is_add = false;
                                    }*/                   
                                    
                                    if($is_add){
                                        $headers = array(
                                            'product_code' => $product_code,
                                            "product_batch" => $product_batch,
                                            "product_hsn_sac_code" => trim($row['C']),
                                            "product_sku" => $product_sku,
                                            "product_serail_no" => trim($row['K']),
                                            "product_name" => trim($row['A']),
                                            "product_unit" => $product_unit_id,
                                            "product_unit_id" => $product_unit_id,
                                            "product_mrp_price" => trim($row['J']),
                                            "product_selling_price" => trim($row['I']),
                                            "product_category_id" => $product_category_id,
                                            "product_subcategory_id" => $product_subcategory_id,
                                            "product_tds_id" => $tcs_product_id,
                                            "product_tds_value" => trim($row['H']),
                                            "product_gst_id" => $gst_product_id,
                                            "product_gst_value" => trim($row['G']),
                                            "product_details" => trim($row['L']),
                                            "product_discount_id" => $discount_product_id,
                                            "product_type" => $product_type,
                                            "is_assets" => 'N' ,
                                            "is_varients" => 'N',
                                            "delete_status" => 0,
                                            "added_date" => date('Y-m-d'),
                                            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                        );
                                        /*$product_id = $this->db->insert($table_name,$headers);*/
                                        $product_id = $this->general_model->insertData($table_name, $headers);
                                        /*$ecommerce = 1;
                                        if($ecommerce){
                                            $headers['product_id'] = $product_id;
                                            $headers['product_image'] = '';
                                            $this->producthook->CreateProduct($headers);
                                        }*/
                                    } else {
                                        $error_array[] = $error_log;
                                    }
                                    /* $row['Error'] = $added_error;*/
                                    if(!$is_add && !empty($row)){
                                        array_unshift($row,$error);
                                        array_push($errors_email, array_values($row));
                                    }
                                } 
                                $log_data = array(
                                    'user_id'           => $this->session->userdata('user_id'),
                                    'table_id'          => 0,
                                    'table_name'        => 'products',
                                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                                    'message'           => 'Bulk Product Inserted');
                                $this->general_model->insertData('log', $log_data);
                                if(!empty($error_array)){
                                    $errorMsg = implode('<br>', $error_array);
                                    $this->session->set_flashdata('bulk_error_product',$errorMsg);
                                    /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array));   */ 
                                }else{
                                    $successMsg = 'Product imported successfully.';
                                    $this->session->set_flashdata('bulk_success_product',$successMsg);
                                    /*$this->session->set_userdata('bulk_success', $successMsg); */ 
                                }
                        }else{
                            $this->session->set_flashdata('bulk_error_product',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }      
                    }else{
                        $this->session->set_flashdata('bulk_error_product',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }      
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_product',"Error on file upload, please try again.");
                    /*$this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');*/
                }
            }
        }
        if(!empty($errors_email)){
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = $this->session->userdata('SESS_EMAIL');
            /*$to = 'harish.sr@aavana.in';*/
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
            $resp = $this->send_csv_mail($errors_email,'Product Bulk Import Error Logs, <br><br> PFA,',"Product bulk upload error logs in <{$import_xls_file}>",$to);
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
            $this->session->set_flashdata('bulk_error_product',"Error email has been sent to registered email ID.");
            /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
        }
        redirect("product", 'refresh');
    }

    function send_csv_mail ($csvData, $body, $subject,$to) {

        /*$to = 'chetna.b@aavana.in';*/
        $path = 'uploads/ProductErrors/error.csv';
        $fp = fopen($path, 'w');//fopen('php://temp', 'w+');
        foreach ($csvData as $line) fputcsv($fp, array_values($line));
        rewind($fp);
        fclose($fp);
        $emailDataSet = array(                         
                            'subject' =>$subject,                    
                            'message' => $body,
                            'email'=>  $to,
                            'csv_string' => $path
                        );

        require APPPATH . 'third_party/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
       
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "chetna.b@aavana.in";
        $mail->Password   = "ZXCVzxcv";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = '587';
        $mail->IsHTML(true);
        $mail->CharSet    = 'UTF-8';

       // $mail->IsHTML(true);
        $mail->setFrom("noreply@aodry.com", $subject);
        $mail->addReplyTo("noreply@aodry.com", $subject);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $bodyContent = $body;
        $mail->Subject = $subject;
        $mail->Body = $bodyContent;
        $mail->addAttachment($path);
        $resp = 'Success';
        if (!$mail->send()) {
            $resp = 'Message could not be sent.';
            $resp =  'Mailer Error : ' . $mail->ErrorInfo;
        } else {
            $this->session->set_flashdata('email_send_product', 'success');
        }
        
        return $resp;
    }

    public function add_product_inventory()
    {
        // print_r($js_data   = json_decode($this->input->post('table_data'),true));die;
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $product_data = array(
            "product_code"           => $this->input->post('ajax_product_code'),
            "product_name"           => $this->input->post('product_name'),
            "product_hsn_sac_code"   => $this->input->post('product_hsn_sac_code'),
            "product_category_id"    => $this->input->post('product_category'),
            "product_subcategory_id" => $this->input->post('product_subcategory'),
            "product_quantity"       => $this->input->post('product_quantity'),
            "product_price"          => $this->input->post('product_price'),
            "product_tax_id"         => $this->input->post('product_tax'),
            "product_tax_value"      => $this->input->post('product_tax_value'),
            "product_tds_id"         => $this->input->post('tds_id'),
            "product_tds_value"      => $this->input->post('product_tds_code'),
            "added_date"             => date('Y-m-d'),
            "type"                   => $this->input->post('p_type'),
            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID'));

        $barcode_symbology = $this->input->post('barcode_symbology');
        $branch_id         = $this->session->userdata('SESS_BRANCH_ID');

        if ($product_inventory_id = $this->general_model->insertData("product_inventory", $product_data))
        {

            $product_varient_data = $this->input->post('table_data');
            $js_data              = json_decode($product_varient_data, true);

            foreach ($js_data as $key => $value)
            {

                $value['product_inventory_id'] = $product_inventory_id;
                $value['added_date']           = date('Y-m-d');
                $value['added_user_id']        = $this->session->userdata('SESS_USER_ID');
                $value['branch_id']            = $this->session->userdata('SESS_BRANCH_ID');

                if ($product_inventory_varients_id = $this->general_model->insertData("product_inventory_varients", $value))
                {

                    $this->general_model->insertData("quantity_history", [
                        'item_id'       => $product_inventory_varients_id,
                        'item_type'     => 'product_inventory',
                        'quantity'      => $value['quantity'],
                        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                        'added_user_id' => $this->session->userdata('SESS_USER_ID')]);

                    $a[] = $product_inventory_varients_id;

                    //barcode generation

                    $code   = sprintf('%08d', $product_inventory_varients_id);
                    $height = array(
                        '0' => 20,
                        '1' => 30,
                        '2' => 50,
                        '3' => 60);

                    if (!is_dir('assets/images/barcode/' . $branch_id . '/' . $code))
                    {
                        mkdir('./assets/images/barcode/' . $branch_id . '/' . $code, 0777, true);
                    }

                    for ($i = 0; $i < 4; $i++)
                    {
                        $file = Zend_Barcode::draw($barcode_symbology, 'image', array(
                            'text'      => $code,
                            'barHeight' => $height[$i],
                            'drawText'  => 1,
                            'factor'    => 1), array());

                        $store_image = imagepng($file, "./assets/images/barcode/" . $branch_id . "/{$code}/{$code}" . $height[$i] . ".png");
                    }

                    $barcode_path = "assets/images/barcode/" . $branch_id . "/{$code}/";
                    $barcode_data = array(
                        'barcode'           => $barcode_path,
                        'barcode_symbology' => $barcode_symbology,
                        'barcode_number'    => $code);
                    $this->general_model->updateData('product_inventory_varients', $barcode_data, array(
                        'product_inventory_varients_id' => $product_inventory_varients_id));
                    //barcode ends
                }

                $key_val = json_decode($this->input->post('key_value'), true);
            }

            for ($i = 0; $i < count($key_val); $i++)
            {

                foreach ($key_val[$i] as $k => $v)
                {

                    $varients_key_value = array(
                        'varients_id'                   => $k,
                        'varients_value_id'             => $v,
                        'product_inventory_varients_id' => $a[$i]
                    );
                    $this->general_model->insertData("product_inventory_varients_value", $varients_key_value);
                }

            }
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $product_inventory_id,
                'table_name' => 'product_inventory',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Product Inventory Inserted');
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
            redirect("product/varient_list", 'refresh');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Product can not be Inserted.');
            redirect("product", 'refresh');
        }
    }

    public function edit($id)
    {
        $product_id                = $this->encryption_url->decode($id);
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $data['product_module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        $data['category_module_id']    = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id']         = $this->config->item('tax_module');
        $data['uqc_module_id']         = $this->config->item('uqc_module');
        $data['discount_module_id']         = $this->config->item('discount_module');
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['data'] = $products = $this->general_model->getRecords('*', 'products', array(
            'product_id'    => $product_id,
            'delete_status' => 0));
        $category_id = $products[0]->product_category_id;
        $select                      = "s.*";
        $table                       = "sub_category s";
        $join['category p']          = "p.category_id=s.category_id";
        $where['p.category_id']       = $category_id;
        $data['product_subcategory'] = $this->general_model->getJoinRecords($select, $table, $where, $join);
        $data['product_category']    = $this->product_category_call();
        $data['tax']                 = $this->tax_call();
        $data['uqc']                 = $this->uqc_product_service_call('product');
        $data['chapter']             = $this->chapter_call();
        $data['tds_section']         = $this->tds_section_call();
        $data['tax_section']      = $this->tds_section_call();
        $data['hsn']              = $this->hsn_call();
        $data['discount']         = $this->discount_call();
        $data['brand']            = $this->brand_call();
        // echo "<pre>";
        // print_r($data['tax']);
        // exit();
        $varient_key  = $this->general_model->getRecords('*', 'product_varients_value', array('product_varients_id' => $product_id));
        
        $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
            'delete_status' => 0,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
        $varient_key_value = array();
        $varient_value_dropdown = array();
        foreach ($varient_key as $key ) {
            $varient_key_value[$key->varients_id][] = $key->varients_value_id;
            $varient_value_dropdown[$key->varients_id] = $this->general_model->getRecords('*', 'varients_value', array(
                'varients_id'   => $key->varients_id,
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
        }
       /* echo "<pre>";
        print_r($varient_key);
        print_r($varient_value_dropdown);
        exit;*/
        $data['varient_key_value'] = $varient_key_value;
        $data['varient_value_dropdown'] = $varient_value_dropdown;
        $data['tax_gst']          = $this->tax_call_type('GST');
        $data['tax_tds']          = $this->tax_call_type('TCS');
        $data['product_master']     = $this->general_model->getRecords('*', 'tbl_product_master', array(
            'status' => 1,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
        $this->load->view('product/edit', $data);
    }

    public function edit_product(){
        $product_code = $this->input->post('product_code');       
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $product_code = $this->input->post('product_code');
        $branch_id = $this->session->userdata('SESS_BRANCH_ID');
        $product_name_edit = $this->input->post('product_name_edit');
        $product_id = $this->input->post('product_id');
        $url = '';
        if (isset($_FILES["product_image"]["name"]) && $_FILES["product_image"]["name"] != ""){

            $path_parts = pathinfo($_FILES["product_image"]["name"]);
            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/product_image/' . $this->session->userdata('SESS_BRANCH_ID'))){
                mkdir('./assets/product_image/' . $this->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            } 
            $url = "assets/product_image/" . $this->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
            if (in_array($path_parts['extension'], array(
                            "jpg","JPG",
                            "jpeg","JPEG",
                            "png","PNG"))){

                if (is_uploaded_file($_FILES["product_image"]["tmp_name"])){
                    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $url)){
                        $image_name = $image_path;
                    }
                }
            }
        }else{
            $image_name = $this->input->post('hidden_product_image');
        }

        $product_data     = array(
            "product_code"           => $this->input->post('product_code'),
            "product_name"           => $this->input->post('product_name_edit'),
            "product_hsn_sac_code"   => $this->input->post('product_hsn_sac_code'),
            "product_category_id"    => $this->input->post('product_category'),
            "product_subcategory_id" => $this->input->post('product_subcategory'),
            "product_quantity"       => $this->input->post('product_quantity'),
            "product_unit"           => $this->input->post('product_unit'),
            "product_price"          => $this->input->post('product_price'),
            "product_tds_id"         => $this->input->post('tds_tax_product'),
            "product_tds_value"      => rtrim($this->input->post('product_tds_code'),'%'),
            "product_gst_id"         => $this->input->post('gst_tax_product'),
            "product_gst_value"      => rtrim($this->input->post('product_gst_code'),'%'),
            "product_discount_id"    => $this->input->post('product_discount'),
            "product_details"        => $this->input->post('product_description'),
            "is_assets"              => $this->input->post('asset'),
            "is_varients"            => $this->input->post('varient'),
            "product_unit_id"        => $this->input->post('product_unit'),
            "product_type"           => $this->input->post('product_type'),
            "product_mrp_price"      => $this->input->post('product_mrp'),
            "product_selling_price"  => $this->input->post('product_selling_price'),
            "product_sku"            => $this->input->post('product_sku'),
            "product_serail_no"      => $this->input->post('product_serial'),
            "product_image"          => $image_name,
            "updated_date"           => date('Y-m-d'),
            "updated_user_id"        => $this->session->userdata('SESS_USER_ID')
        );

        if($this->input->post('product_batch')){
            $product_data['product_batch'] = $this->input->post('product_batch');
        }

        if($this->input->post('margin_discount_value')){
            $product_data['margin_discount_value'] = $this->input->post('margin_discount_value');
            $product_data['margin_discount_id'] = $this->input->post('margin_discount');
        }

        if($this->input->post('product_discount_value')){
            $product_data['product_discount_value'] = $this->input->post('product_discount_value');
        }

        if($this->input->post('product_basic_price')){
            $product_data['product_basic_price'] = $this->input->post('product_basic_price');
        }

        if($this->input->post('profit_margin')){
            $product_data['product_profit_margin'] = $this->input->post('profit_margin');
        }

        if($this->input->post('product_brand')){
            $product_data['brand_id'] = $this->input->post('product_brand');
        }

        if($this->input->post('product_opening_stock')){
            $product_data['product_opening_quantity'] = $this->input->post('product_opening_stock');
        }

        if($this->input->post('product_packing')){
            $product_data['packing'] = $this->input->post('product_packing');
        }

        if($this->input->post('exp_date')){
            $product_data['exp_date'] = date('Y-m-d',strtotime($this->input->post('exp_date')));
        }

        if($this->input->post('mfg_date')){
            $product_data['mfg_date'] = date('Y-m-d',strtotime($this->input->post('mfg_date')));
        }

        if($this->input->post('equal_uom')){
            $product_data['equal_unit_number'] = $this->input->post('equal_uom');
        }

        if($this->input->post('product_equal_unit')){
            $product_data['equal_uom_id'] = $this->input->post('product_equal_unit');
        }


        /*echo "<pre>";
        print_r($product_data);
        exit();*/
        $LeatherCraft_id = $this->config->item('LeatherCraft');
        $ecomm_variant_product = array();
        /*if($LeatherCraft_id == $this->session->userdata("SESS_BRANCH_ID") ){
            $where_array = array( 'product_code' => $product_code);
        }else{
             
        }*/
        $where_array = array( 'product_id' => $product_id);
        if ($this->general_model->updateData('products', $product_data, $where_array)){
            if($LeatherCraft_id == $this->session->userdata("SESS_BRANCH_ID") ){
                $data_update_com = $this->general_model->getRecords('*', 'product_combinations', array(
                    'product_id' => $product_id,
                    'status' => 'Y',
                    'branch_id' => $this->session->userdata("SESS_BRANCH_ID") ));
               
                foreach ($data_update_com as  $value) {       
                    unset($product_data['product_name']); 
                    unset($product_data['product_code']);
                    unset($product_data['is_assets']); 
                    unset($product_data['is_varients']);
                   $this->general_model->updateData('products', $product_data, array('product_combination_id' => $value->combination_id));
                }
            }
        
            if(isset($_POST['combination'])){
                $insert_product = array();
                $product_combination_ids = $_POST['combination'];
                $length = count($_POST['combination']);
            
                $keyword = $product_code.'-0';
                $this->db->select('*');
                $this->db->from('products');
                $this->db->like('product_code', $keyword);
                $res = $this->db->get();
                $count = $res->num_rows();
                $a = $count +1 ;
                for ($i = 0; $i < $length; $i++) {
                    $combination_id = $product_combination_ids[$i];
                    $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                    'combination_id'   => $combination_id,
                    'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                    $varient_value_id = $combination_data[0]->varient_value_id;
                    $combination_name = $combination_data[0]->combinations;

                    $insert_product[$i]["product_code"] = $product_code.'-0'.$a;
                    if($this->session->userdata('SESS_FIRM_ID') == $this->config->item('LeatherCraft')) $insert_product[$i]["product_code"] = $product_code;
                    $insert_product[$i]["product_name"] = $product_name_edit.'/'.$combination_name;
                    $insert_product[$i]["product_hsn_sac_code"] = $this->input->post('product_hsn_sac_code');
                    $insert_product[$i]["product_category_id"] = $this->input->post('product_category');
                    $insert_product[$i]["product_subcategory_id"] = $this->input->post('product_subcategory');
                    $insert_product[$i]["product_quantity"] = $this->input->post('product_quantity');
                    $insert_product[$i]["product_unit"] = $this->input->post('product_unit');
                    $insert_product[$i]["product_price"] = $this->input->post('product_price');
                    $insert_product[$i]["product_tds_id"] = $this->input->post('tds_tax_product');
                    $insert_product[$i]["product_tds_value"] = rtrim($this->input->post('product_tds_code'),'%');
                    $insert_product[$i]["product_gst_id"] = $this->input->post('gst_tax_product');
                    $insert_product[$i][ "product_gst_value"] =  rtrim($this->input->post('product_gst_code'),'%');
                    $insert_product[$i]["product_discount_id"] = $this->input->post('product_discount');
                    $insert_product[$i]["product_details"] = $this->input->post('product_description');
                    $insert_product[$i]["is_assets"] = $this->input->post('asset');
                    $insert_product[$i]["is_varients"] = 'N';
                    $insert_product[$i]["product_unit_id"] = $this->input->post('product_unit');
                    $insert_product[$i]["added_date"] = date('Y-m-d');
                    $insert_product[$i]["added_user_id"] = $this->session->userdata('SESS_USER_ID');
                    $insert_product[$i]["branch_id"] = $this->session->userdata('SESS_BRANCH_ID');
                    $insert_product[$i]["product_type"] = $this->input->post('product_type');
                    $insert_product[$i]["product_mrp_price"] = $this->input->post('product_mrp');
                    $insert_product[$i]["product_selling_price"] = $this->input->post('product_selling_price');
                    $insert_product[$i]["product_sku"] = $this->input->post('product_sku');
                    $insert_product[$i]["product_serail_no"] = $this->input->post('product_serial');
                    $insert_product[$i]["product_image"] = $image_name;
                    $insert_product[$i]["product_combination_id"] = $combination_id;
                    $update_compina_status = array('status' => 'Y');

                    if($this->input->post('margin_discount_value')){
                        $insert_product[$i]['margin_discount_value'] = $this->input->post('margin_discount_value');
                        $insert_product[$i]['margin_discount_id'] = $this->input->post('margin_discount');
                    }

                    if($this->input->post('product_discount_value')){
                        $insert_product[$i]['product_discount_value'] = $this->input->post('product_discount_value');
                    }

                    if($this->input->post('product_basic_price')){
                        $insert_product[$i]['product_basic_price'] = $this->input->post('product_basic_price');
                    }

                    if($this->input->post('profit_margin')){
                        $insert_product[$i]['product_profit_margin'] = $this->input->post('profit_margin');
                    }

                    if($this->input->post('product_brand')){
                        $insert_product[$i]['brand_id'] = $this->input->post('product_brand');
                    }
                    if($this->input->post('product_opening_stock')){
                        $insert_product[$i]['product_opening_quantity'] = $this->input->post('product_opening_stock');
                    }

                    if($this->input->post('exp_date')){
                        $product_data['exp_date'] = date('Y-m-d',strtotime($this->input->post('exp_date')));
                    }

                    if($this->input->post('mfg_date')){
                        $product_data['mfg_date'] = date('Y-m-d',strtotime($this->input->post('mfg_date')));
                    }

                    $this->general_model->updateData('product_combinations', $update_compina_status, array('combination_id' => $combination_id));   
                    $a++;   
                    $variant_id = $this->general_model->insertData('products', $insert_product[$i]);
                    
                    $insert_product[$i]['variant_id'] = $variant_id;
                    $ecomm_variant_product[$i] = $insert_product[$i];
                    $ecomm_variant_product[$i]['varient_value_id'] = $varient_value_id;  
                }

                /*$this->db->insert_batch('products', $insert_product);*/
            }
            $varient_data_name = array();
            $varient_data_already = $this->general_model->getRecords('*', 'product_varients_value', array('product_varients_id'   => $product_id));

            foreach ($varient_data_already as $varient) {            
                $varient_data_name[] = $varient->varients_value_id;         
            }

            $l = 1;
            $n = 1;
       
            if(isset($_POST['varient_key'])){                           
                $data_product_var_val = array();
                $length = count($_POST['varient_key']);
                $varients_key = $_POST['varient_key'];
                
                for ($k = 0; $k < $length; $k++) {
                    $length_value = count($_POST['varient_value_'. $l]);
                    $varients_id = $_POST['varient_value_'. $l];
                    $varients_key_id = $varients_key[$k];
                        for ($m = 0; $m < $length_value; $m++) {
                            $varients_value_id = $varients_id[$m];
                            if(!in_array($varients_value_id, $varient_data_name)){
                                $data_product_var_val[$n]['varients_value_id'] = $varients_id[$m];
                                $data_product_var_val[$n]['varients_id'] =  $varients_key_id;
                                $data_product_var_val[$n]['product_varients_id'] =  $product_id;
                                $data_product_var_val[$n]['delete_status'] =  0;
                            $n++;
                            }
                        }
                    $l++;
                }
                if(!empty($data_product_var_val))
                $this->db->insert_batch('product_varients_value', $data_product_var_val);
            }
            $successMsg = 'Product Updated Successfully';
            $this->session->set_flashdata('product_success',$successMsg);

            /*$ecommerce = 1;
            if($ecommerce){
                $product_data['variants'] = $ecomm_variant_product;
                $product_data['product_id'] = $product_id;
                $product_data['product_image'] = base_url().$url;
                $this->producthook->UpdateProduct($product_data);
            }*/

            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $product_id,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Updated');
            $this->general_model->insertData('log', $log_data);
            redirect('product', 'refresh');
        }else{
            $errorMsg = 'Product Update Unsuccessful';
            $this->session->set_flashdata('product_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Product can not be Updated.');
            redirect("product", 'refresh');
        }
    }

    public function delete(){
        $id                = $this->input->post('delete_id');
        $product_id                = $this->encryption_url->decode($id);
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
         $LeatherCraft_id = $this->config->item('LeatherCraft');
        if($LeatherCraft_id == $this->session->userdata("SESS_BRANCH_ID") ){
            $sql = "SELECT product_id FROM products WHERE `product_combination_id` IN (SELECT combination_id FROM product_combinations WHERE product_id = " . $product_id . " AND status = 'Y')";
                        $qry = $this->db->query($sql);
                        if($qry->num_rows() > 0){
                            $var_lal = $qry->result_array();
                            $product_id_array = array();
                            $all_product_id = array();
                            foreach ($var_lal as $key => $value) {
                                $product_id_array[] = $value['product_id'];
                            }
                           
                            $all_product_id = implode(',', $product_id_array);
                        }else{
                           $all_product_id =  $product_id;
                        }
            $sql_update = "UPDATE products SET delete_status = 1  WHERE product_id = " . $product_id . " OR product_id IN (" . $all_product_id . ")";
            $delete_id = $this->db->query($sql_update);
        }else{
           $delete_id = $this->general_model->updateData('products', array(
            'delete_status' => 1), array(
            'product_id' => $product_id));
        }

        if ($delete_id){
            //update stock history
            $where = array(
                'item_id'        => $product_id,
                'reference_id'   => '',
                'reference_type' => 'product',
                'delete_status'  => 0
            );
            $this->db->where($where);
            $history = $this->db->get('quantity_history')->result();

            if (!empty($history))
            {
                 $update_history_quantity = array(
                    'delete_status'        => 1,
                    'updated_date'    => date('Y-m-d'),
                    'updated_user_id' => $this->session->userdata('SESS_USER_ID'));
                $this->db->where($where);
                $this->db->update('quantity_history', $update_history_quantity);
            }
            $successMsg = 'Product Deleted Successfully';
            $this->session->set_flashdata('product_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $product_id,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Deleted');
            $this->general_model->insertData('log', $log_data);
            $redirect = 'product';
            if($this->input->post('delete_redirect') != '') $redirect = $this->input->post('delete_redirect');
            redirect($redirect , 'refresh');
        }
        else
        {
            $errorMsg = 'Product Delete Unsuccessful';
            $this->session->set_flashdata('product_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Product can not be Deleted.');
            redirect("product", 'refresh');
        }

    }

    public function get_product_quantity()
    {
        $id   = $this->input->post('id');
        $id   = $this->encryption_url->decode($id);
        $data = $this->general_model->getRecords('*', 'products', array(
            'product_id' => $id));
        echo json_encode($data);
    }

    public function add_damaged_products()
    {
        $product_id   = $this->input->post('product_id');
        $product_id   = $this->encryption_url->decode($product_id);
        $damaged_qty  = $this->input->post('damaged_quantity');
        $product_data = $this->general_model->getRecords('*', 'products', array(
            'product_id' => $product_id));
        $damaged_quantity = bcadd($product_data[0]->product_damaged_quantity, $damaged_qty);
        $quantity         = bcsub($product_data[0]->product_quantity, $damaged_qty);
        $data             = array(
            'product_quantity'         => $quantity,
            'product_damaged_quantity' => $damaged_quantity);
        $res = $this->general_model->updateData('products', $data, array(
            'product_id' => $product_id));
        $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $res,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Damaged Product Inserted');
            $this->general_model->insertData('log', $log_data);
             $quantity_data = array(
             'item_id'          => $product_id,
                'item_type'        => 'product',
                "reference_id"     => $product_id,
                "reference_number" => '',
                "reference_type"   => 'product',
                'quantity'         => $damaged_quantity,
                "stock_type"       => 'damaged',
                'added_date'       => date('Y-m-d'),
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'entry_date'       => date('Y-m-d')
        );

        $this->general_model->insertData("quantity_history", $quantity_data);

        echo json_encode($res);
    }

    public function update_existing_stock()
    {
        $product_id = $this->input->post('product_id');
        $product_id = $this->encryption_url->decode($product_id);
        $qty        = $this->input->post('quantity');
        $data       = array(
            'product_quantity' => $qty);
        $res = $this->general_model->updateData('products', $data, array(
            'product_id' => $product_id));
        $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $res,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Existing Stock Updated');
            $this->general_model->insertData('log', $log_data);
        $quantity_data = array(
             'item_id'          => $product_id,
                'item_type'        => 'product',
                "reference_id"     => $product_id,
                "reference_number" => '',
                "reference_type"   => 'product',
                'quantity'         => $qty,
                "stock_type"       => 'direct',
                'added_date'       => date('Y-m-d'),
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'entry_date'       => date('Y-m-d')
        );

        $this->general_model->insertData("quantity_history", $quantity_data);

        echo json_encode($res);
    }

    public function edit_damaged_products()
    {
        $product_id   = $this->input->post('product_id');
        $product_id   = $this->encryption_url->decode($product_id);
        $product_qty  = $this->input->post('product_quantity');
        $product_data = $this->general_model->getRecords('*', 'products', array(
            'product_id' => $product_id));
        $damaged_quantity = bcsub($product_data[0]->product_damaged_quantity, $product_qty);
        $quantity         = bcadd($product_data[0]->product_quantity, $product_qty);
        $data             = array(
            'product_quantity'         => $quantity,
            'product_damaged_quantity' => $damaged_quantity);
        $res = $this->general_model->updateData('products', $data, array(
            'product_id' => $product_id));
        $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $res,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Damaged Product Updated');
            $this->general_model->insertData('log', $log_data);
              $quantity_data = array(
             'item_id'          => $product_id,
                'item_type'        => 'product',
                "reference_id"     => $product_id,
                "reference_number" => '',
                "reference_type"   => 'product',
                'quantity'         => $damaged_quantity,
                "stock_type"       => 'move_to_stock',
                'added_date'       => date('Y-m-d'),
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'entry_date'       => date('Y-m-d')
        );

        $this->general_model->insertData("quantity_history", $quantity_data);

        echo json_encode($res);
    }
    public function get_bulk_check_product($product_name,$product_id = 0)
    {
        $product_name = strtoupper($product_name);
        $data         = $this->general_model->getRecords('*,count(*) num ', 'products', array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'product_name'  => $product_name,
            'product_id!='  => $product_id),"","product_name");
        return $data;
    }
    public function get_check_product()
    {
        $product_name = strtoupper(trim($this->input->post('product_name')));
        $product_id   = $this->input->post('product_id');
        $data         = $this->general_model->getRecords('*,count(*) num ', 'products', array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'product_name'  => $product_name,
            'batch_parent_product_id' => 0,
            'product_id!='  => $product_id),"","product_name");

        echo json_encode($data);
    }

    public function get_check_product_code()
    {
        $product_name = strtoupper(trim($this->input->post('product_name')));
        $product_name = preg_replace('!\s+!', ' ', strtolower(trim($product_name)));
        $product_code = strtoupper(trim($this->input->post('product_code')));
        $product_id   = $this->input->post('product_id');
        $data         = $this->general_model->getRecords('*,count(*) num ', 'products', array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'LOWER(product_name)'  => $product_name,
            'product_code'  => $product_code,
            'batch_parent_product_id' => 0,
            'product_id!='  => $product_id),"","product_name");

        echo json_encode($data);
    }

    public function varient_list()
    {
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data                = array_merge($data, $section_modules);
        $data['tds_section'] = $this->tds_section_call();

        if (!empty($this->input->post()))
        {
            $columns = array(
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
                10 => 'action');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->product_varient();
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
            }

            $send_data = array();

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
                    $nestedData['product_tds_value']    = $post->product_tds_value;
                    // $nestedData['category_name']        = $post->category_name;
                    $nestedData['product_price']    = $post->product_price;
                    $nestedData['type']             = $post->type;
                    $nestedData['product_quantity'] = '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#quantity_products" class="quantity_change" style="cursor:pointer;" data-pid="' . $product_id . '" data-qty="' . $post->product_quantity . '" title="" >' . $post->product_quantity;
                    '</a>';

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = '<a class="delete_button btn btn-xs btn-warning"  data-backdrop="static" data-keyboard="false" data-toggle="modal" onclick="get_product(' . $post->product_inventory_id . ')" data-target="#editProduct">Edit Product</a>';

                    $cols .= '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $product_id . '" data-path="product/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Product</a>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }

            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }
        else
        {

            $data['product_category'] = $this->product_category_call();
            $data['tax']              = $this->tax_call();
            $data['uqc']              = $this->uqc_product_service_call('product');
            $data['chapter']          = $this->chapter_call();
            $data['hsn']              = $this->hsn_call();

            $this->load->view('product/varient_list', $data);
        }

    }

    public function product_varient_edit($id)
    {

        $id                = $this->encryption_url->decode($id);
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        // $str = 'p.*,piv.varient_code,piv.varient_name,piv.purchase_price,piv.selling_price,piv.varient_unit,u.first_name,u.last_name,v.varient_key,val.varients_value';

        // $tab = "product_inventory p";

        // $joi['product_inventory_varients piv'] = "piv.product_inventory_id=p.product_inventory_id";

        // $joi['product_inventory_varients_value pivv'] ="pivv.product_inventory_varients_id = piv.product_inventory_varients_id";

        // $joi['varients v'] = "v.varients_id = pivv.varients_id";

        // $joi['varients_value val'] = "val.varients_value_id = pivv.varients_value_id";

        // $joi['users u'] = "u.id = p.added_user_id";

        // $wh=array(

        //             'piv.product_inventory_id' => $id,

        //             'piv.branch_id'=>$this->session->userdata('SESS_BRANCH_ID'),

        //             'piv.delete_status'=>0

        //             );

        // echo "<pre>";

        // $data['products_inventory'] = $this->general_model->getJoinRecords($str,$tab,$wh,$joi);

        // print_r($data['products_inventory']);die;
        //fetch product names
        $product_str   = 'p.*';
        $product_table = "product_inventory p";
        $product_where = array(
            'p.product_inventory_id' => $id,
            'p.branch_id'            => $this->session->userdata('SESS_BRANCH_ID'),
            'p.delete_status'        => 0
        );
        $data['product_inventory'] = $this->general_model->getRecords($product_str, $product_table, $product_where);

        $select                          = "s.*";
        $table                           = "sub_category s";
        $join['product_inventory p']     = "p.product_category_id=s.category_id";
        $where['p.product_inventory_id'] = $id;
        $data['product_subcategory']     = $this->general_model->getJoinRecords($select, $table, $where, $join);
        $data['product_category']        = $this->product_category_call();

        // echo "<pre>";
        // print_r($data['product_category']);die;
        $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
            'delete_status' => 0,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
        $data['tax']     = $this->tax_call();
        $data['uqc']     = $this->uqc_product_service_call('product');
        $data['chapter'] = $this->chapter_call();
        $data['hsn']     = $this->hsn_call();
        $this->load->view('product/product_varient_edit', $data);
    }

    public function fetch_product_varient($id){
        $product_module_id = $this->config->item('product_module');
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['product_module_id'] = $product_module_id;

        if (!empty($this->input->post()))
        {

            $columns = array(
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
                10 => 'action');
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $id                  = $this->encryption_url->decode($id);
            $list_data           = $this->common->get_products_varients_list($id);
            $list_data1          = $this->common->get_products_varients_list1($id);
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered       = $totalData;

            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $posts1              = $this->general_model->getPageJoinRecords($list_data1);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getPageJoinRecords($list_data);
                $posts1              = $this->general_model->getPageJoinRecords($list_data1);
                $totalFiltered       = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();

            if (!empty($posts))
            {

                foreach ($posts as $post)
                {
                    $varients_value = array();

                    foreach ($posts1 as $post1)
                    {

                        if ($post1->product_inventory_varients_id == $post->product_inventory_varients_id)
                        {
                            $varients_value[] = $post1->varients_value;
                        }

                    }

                    $varients_values                = implode('/', $varients_value);
                    $varients_value                 = array();
                    $product_id                     = $this->encryption_url->encode($post->product_inventory_id);
                    $nestedData['quantity']         = "<a onclick='get_quantity(" . $post->product_inventory_varients_id . ")' data-toggle='modal' data-target='#quantity_model'>" . $post->q . "</a>";
                    $nestedData['product_code']     = $post->product_name . '/' . $varients_values;
                    $nestedData['varient_name']     = $post->varient_name . '<span id=' . $post->product_inventory_varients_id . ' style="display:none">test</span>';
                    $nestedData['product_name']     = $post->varient_code;
                    $nestedData['purchase_price']   = $post->purchase_price;
                    $nestedData['product_price']    = $post->product_price;
                    $nestedData['selling_price']    = $post->selling_price;
                    $nestedData['damaged_stock']    = "<a onclick='get_damaged_quantity(" . $post->product_inventory_varients_id . ")' data-toggle='modal' data-target='#damages_quantity'>" . $post->damaged_stock . "</a>";
                    $nestedData['product_quantity'] = '<a data-backdrop="static" data-keyboard="false"  data-toggle="modal" data-target="#quantity_products" class="quantity_change" style="cursor:pointer;" data-pid="' . $product_id . '" data-qty="' . $post->product_quantity . '" title="" >' . $post->product_quantity;
                    '</a>';

                    $nestedData['varient_unit'] = $post->varient_unit;
                    // $cols                       = '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="get_key_val('.$post->product_inventory_varients_id.')" data-target="#keyValue">Edit Varient</a>';

                    $cols = '&nbsp;<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="getVarients(' . $post->product_inventory_varients_id . ')" data-target="#productVarient">Edit Product Varient</a>';

                    $cols .= '&nbsp;<a data-toggle="modal" data-target="#quantity_history_modal" data-id="' . $post->product_inventory_varients_id . '"  title="Delete" class=" quantity_history_view btn btn-xs" onclick="history(' . $post->product_inventory_varients_id . ')">Quantity History</a>';

                    $cols .= '&nbsp;<a data-toggle="modal" data-target="#delete_modal" data-id="' . $product_id . '" data-path="product/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Product</a>';

                    $cols .= '&nbsp;<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="stock(' . $post->product_inventory_varients_id . ')" data-target="#stock_management">Move to Damaged</a>';

                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }

            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }
        else
        {
            $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));
            $data['id']  = $id;
            $data['uqc'] = $this->uqc_product_service_call('product');
            $this->load->view('product/fetch_product_varient', $data);
        }

    }

    public function quantity_history_list($id){

        if (!empty($this->input->post()))
        {

            $columns = array(

                0 => 'quantity',
                1 => 'stock_type',
                2 => 'added_date',
                3 => 'user');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir   = $this->input->post('order')[0]['dir'];
            // $id                  = $this->encryption_url->decode($id);
            $list_data = $this->common->quantity_history_list_modal($id);

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

                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            }

            $send_data = array();

            if (!empty($posts))
            {

                foreach ($posts as $post)
                {
                    $nestedData['quantity']   = $post->quantity;
                    $nestedData['stock_type'] = $post->stock_type;
                    $nestedData['added_date'] = $post->added_date;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;

                    $send_data[] = $nestedData;
                }

            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }

    }

    public function get_varient_values($id){
        $varients = $this->general_model->getRecords('*', 'product_inventory_varients', [
            'product_inventory_varients_id' => $id,
            'delete_status'                 => 0]);

        $val['id']             = $varients[0]->product_inventory_varients_id;
        $val['code']           = $varients[0]->varient_code;
        $val['name']           = $varients[0]->varient_name;
        $val['selling_price']  = $varients[0]->selling_price;
        $val['purchase_price'] = $varients[0]->purchase_price;
        $val['quantity']       = $varients[0]->quantity;
        $val['varient_unit']   = $varients[0]->varient_unit;
        echo json_encode($val);
    }

    public function get_varient_key_values($id){

        $select                                   = "p.id,v.varient_key,vv.varients_value";
        $table                                    = "product_inventory_varients_value p";
        $join['varients v']                       = "v.varients_id=p.varients_id";
        $join['varients_value vv']                = "vv.varients_value_id=p.varients_value_id";
        $where['p.product_inventory_varients_id'] = $id;
        $where['p.delete_status']                 = 0;

        $varients = $this->general_model->getJoinRecords($select, $table, $where, $join);

        $sel                      = "v.varients_id,v.varient_key,vv.varients_value,vv.varients_value_id";
        $tab                      = "varients v";
        $joi["varients_value vv"] = "v.varients_id = vv.varients_id";
        $wh['v.delete_status']    = 0;

        $varients_key_value = $this->general_model->getJoinRecords($sel, $tab, $wh, $joi);

        foreach ($varients as $key)
        {

            echo "<form style>";
            echo "<div class='col-md-6'>";
            echo "<div class='form-group'>";
            echo "<lable>" . $key->varient_key . "</lable><br>";
            echo "<input type='text' name='varient_value' value=" . $key->varients_value . "><br>";
            echo "</div>";
            echo "</div>";
            echo "</form>";
        }
    }

    public function edit_product_vaarient(){

        $table = 'product_inventory_varients';
        $data  = array(
            'varient_name'   => $this->input->post('varient_name'),
            'varient_code'   => $this->input->post('varient_code'),
            'purchase_price' => $this->input->post('purchase_price'),
            'selling_price'  => $this->input->post('selling_price'),
            'quantity'       => $this->input->post('quantity'),
            'varient_unit'   => $this->input->post('varient_unit')
        );

        $where = array(
            'product_inventory_varients_id' => $this->input->post('id'));

        if ($id = $this->general_model->updateData($table, $data, $where))
        {
            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $id,
                'table_name'        => 'product_inventory_varients',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Varient Updated');
            $this->general_model->insertData('log', $log_data);
            echo "success";
        }

    }

    public function get_product_id($id)
    {
        $product = $this->general_model->getRecords('*', 'product_inventory', [
            'product_inventory_id' => $id,
            'delete_status'        => 0]);

        $products['id']                = $product[0]->product_inventory_id;
        $products['product_code']      = $product[0]->product_code;
        $products['product_name']      = $product[0]->product_name;
        $products['product_tds_id']    = $product[0]->product_tds_id;
        $products['product_tds_value'] = $product[0]->product_tds_value;
        // $products['product_tax_id'] = $product[0]->product_tax_id;
        $products['hsn'] = $product[0]->product_hsn_sac_code;

        // $products['igst']         = $product[0]->product_igst;

        // $products['cgst']         = $product[0]->product_cgst;
        // $products['sgst']         = $product[0]->product_sgst;
        $products['price'] = $product[0]->product_price;

        //$products['tax']          = $product[0]->product_tax_id . '-' . $product[0]->tax_value;

        //$products['tax_value']    = $product[0]->tax_value;

        echo json_encode($products);
    }

    public function ajax_edit_product(){

        $id = $this->input->post('id');

        $product_data = array(
            "product_code"         => $this->input->post('product_code'),
            "product_name"         => $this->input->post('product_name'),
            "product_hsn_sac_code" => $this->input->post('product_hsn_sac_code'),
            "product_price"        => $this->input->post('product_price'),

            // "product_tax_id"       => $this->input->post('product_tax'),
            // "product_tax_value"      => $this->input->post('product_tax_value'),
            "product_tds_id"       => $this->input->post('product_tds_id'),
            "product_tds_value"    => $this->input->post('product_tds_value'),

            "updated_date"         => date('Y-m-d'),
            "updated_user_id"      => $this->session->userdata('SESS_USER_ID'));

        if ($id1=$this->general_model->updateData('product_inventory', $product_data, array(
            'product_inventory_id' => $id)))
        {
            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $id1,
                'table_name'        => 'product_inventory',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Updated');
            $this->general_model->insertData('log', $log_data);
            echo "sucess";
        }

    }

    public function update_product_varients()
    {

        $product_varient_value_id = $this->input->post('product_varient_value_id');
        $id                       = $this->encryption_url->decode($product_varient_value_id);

        $product_inventory = $this->general_model->getRecords('product_inventory_id,barcode_symbology', 'product_inventory_varients', [
            'product_inventory_varients_id' => $id]);
        // print_r($product_inventory_id);die;
        $product_inventory_id = $product_inventory[0]->product_inventory_id;

        $product_varient_data = $this->input->post('table_data');
        $js_data              = json_decode($product_varient_data, true);

        $branch_id         = $this->session->userdata('SESS_BRANCH_ID');
        $barcode_symbology = $product_inventory[0]->barcode_symbology;

        foreach ($js_data as $key => $value)
        {

            $value['product_inventory_id'] = $id;
            $value['added_date']           = date('Y-m-d');
            $value['added_user_id']        = $this->session->userdata('SESS_USER_ID');
            $value['branch_id']            = $this->session->userdata('SESS_BRANCH_ID');

            if ($product_inventory_varients_id = $this->general_model->insertData("product_inventory_varients", $value))
            {

                $a[] = $product_inventory_varients_id;

                //barcode generation

                $code   = sprintf('%08d', $product_inventory_varients_id);
                $height = array(
                    '0' => 20,
                    '1' => 30,
                    '2' => 50,
                    '3' => 60);

                if (!is_dir('assets/images/barcode/' . $branch_id . '/' . $code))
                {
                    mkdir('./assets/images/barcode/' . $branch_id . '/' . $code, 0777, true);
                }

                for ($i = 0; $i < 4; $i++)
                {
                    $file = Zend_Barcode::draw($barcode_symbology, 'image', array(
                        'text'      => $code,
                        'barHeight' => $height[$i],
                        'drawText'  => 1,
                        'factor'    => 1), array());

                    $store_image = imagepng($file, "./assets/images/barcode/" . $branch_id . "/{$code}/{$code}" . $height[$i] . ".png");
                }

                $barcode_path = "assets/images/barcode/" . $branch_id . "/{$code}/";
                $barcode_data = array(
                    'barcode'           => $barcode_path,
                    'barcode_symbology' => $barcode_symbology,
                    'barcode_number'    => $code);
                $this->general_model->updateData('product_inventory_varients', $barcode_data, array(
                    'product_inventory_varients_id' => $product_inventory_varients_id));
                //barcode ends
            }

            $key_val = json_decode($this->input->post('key_value'), true);
        }

        for ($i = 0; $i < count($key_val); $i++)
        {

            foreach ($key_val[$i] as $k => $v)
            {

                $varients_key_value = array(
                    'varients_id'                   => $k,
                    'varients_value_id'             => $v,
                    'product_inventory_varients_id' => $a[$i]
                );
                $this->general_model->insertData("product_inventory_varients_value", $varients_key_value);
                // print_r($varients_key_value);
            }

        }
        $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => 0,
                'table_name'        => 'product_inventory_varients',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Varient Updated');
            $this->general_model->insertData('log', $log_data);
        redirect('product/fetch_product_varient/' . $product_varient_value_id, 'refresh');
    }

    public function get_quantity_values($id)
    {

        $vals = $this->general_model->getRecords('*', 'product_inventory_varients', [
            'product_inventory_varients_id' => $id]);

        $product_quantity['quantity'] = $vals[0]->quantity;
        $product_quantity['id']       = $id;

        echo json_encode($product_quantity);
    }

    public function update_quantity()
    {

        $quantity         = $this->input->post('quantity_new');
        $id               = $this->input->post('qp_id');
        $reference_number = $this->input->post('reference_number');
        $user_date        = $this->input->post('user_date');

        $sum_quantity = $quantity + $this->input->post('quantity_history');

        $url   = $this->input->post('url');
        $table = 'product_inventory_varients';
        $where = array(
            'product_inventory_varients_id' => $id);

        $this->general_model->updateData($table, [
            'quantity' => $sum_quantity], $where);
        $quantity_data = array(
            'item_id'          => $id,
            'item_type'        => 'product_inventory',
            'reference_number' => $reference_number,
            'quantity'         => $quantity,
            'added_date'       => date('Y-m-d'),
            'branch_id'        => $this->session->userdata('SESS_BRANCH_ID'),
            'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
            'entry_date'       => $user_date);

        $this->general_model->insertData("quantity_history", $quantity_data);
        $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => 0,
                'table_name'        => 'quantity',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Quantity Updated');
            $this->general_model->insertData('log', $log_data);
        redirect('product/fetch_product_varient/' . $url, 'refresh');
    }

    public function get_stock($id)
    {

        $values = $this->general_model->getRecords('*', 'product_inventory_varients', [
            'product_inventory_varients_id' => $id]);

        $quantity['quantity'] = $values[0]->quantity;

        echo json_encode($quantity);
    }

    public function move_to_damage()
    {

        // print_r($this->input->post());die;
        $id            = $this->input->post('stock_p_id');
        $damaged_stock = $this->input->post('damaged_stock');
        $url           = $this->input->post('stock_url');

        $values = $this->general_model->getRecords('*', 'product_inventory_varients', [
            'product_inventory_varients_id' => $id]);
        $existing_damage = $values[0]->damaged_stock;
        $final_damage    = $existing_damage + $damaged_stock;

        $quantity = $values[0]->quantity;
        $where    = array(
            'product_inventory_varients_id' => $id);
        $final_stock = $quantity - $damaged_stock;

        $iasd = $this->general_model->insertData('quantity_history', [
            'item_type'  => 'product_inventory',
            'item_id'    => $id,
            'stock_type' => 'damaged',
            'quantity'   => $damaged_stock,
            'added_date' => date('Y-m-d')]);

        if ($this->general_model->updateData('product_inventory_varients', [
            'quantity'      => $final_stock,
            'damaged_stock' => $final_damage], $where))
        {
            redirect('product/fetch_product_varient/' . $url, 'refresh');
        }
        else
        {
            echo "failed";
        }

    }

    public function get_damaged_products($id)
    {

        $values = $this->general_model->getRecords('*', 'product_inventory_varients', [
            'product_inventory_varients_id' => $id]);

        $quantity['damaged_stock'] = $values[0]->damaged_stock;

        echo json_encode($quantity);
    }

    public function move_to_stock()
    {

        // print_r($this->input->post());

        $url   = $this->input->post('move_to_url');
        $stock = $this->input->post('move_to_stock');
        $id    = $this->input->post('move_to_id');

        $values = $this->general_model->getRecords('*', 'product_inventory_varients', [
            'product_inventory_varients_id' => $id]);

        $where = array(
            'product_inventory_varients_id' => $id);
        $existing_stock = $values[0]->quantity;
        $total_damaged  = $values[0]->damaged_stock;

        $final_stock  = $stock + $existing_stock;
        $final_damage = $total_damaged - $stock;

        $this->general_model->updateData('product_inventory_varients', [
            'quantity'      => $final_stock,
            'damaged_stock' => $final_damage], $where);

        $this->general_model->insertData('quantity_history', [
            'item_type'  => 'product_inventory',
            'item_id'    => $id,
            'stock_type' => 'move_to_stock',
            'quantity'   => $stock,
            'added_date' => date('Y-m-d')]);

        redirect('product/fetch_product_varient/' . $url, 'refresh');
    }

    public function hsn_list(){

        if (!empty($this->input->post())){
            $columns = array(
                    0 => 'hsn_code',
                    1 => 'description' );
            $limit   = $this->input->post('length');
            $start   = $this->input->post('start');
            $order   = $columns[$this->input->post('order')[0]['column']];
            $dir     = $this->input->post('order')[0]['dir'];

            $list_data = $this->common->hsn_list_field();

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
            }
            $send_data = array();
            if (!empty($posts))
            {
                $i = 1;
                foreach ($posts as $post)
                {
                    $sac_id                        = $this->encryption_url->encode($post->hsn_id);
                    // $nestedData['sl_no']  = $i++;
                    $nestedData['hsn_code'] = '<span id="accounting_sac_code">' . $post->hsn_code . '</span><span id="hsn_id" style="display:none;">' . $post->hsn_id . '</span>';
                    $nestedData['description']     = $post->description;

                    $cols                 = '<span class="btn btn-info apply" class="close" data-dismiss="modal">Apply</span>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }
            }
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }
    }

    public function get_hsn_code($id) {
        $id = $id;
        $data = $this->general_model->getRecords('*', 'hsn', array('hsn_id' => $id, 'delete_status' => 0 ));
        echo json_encode($data);
    }

    public function tds_list()
    {

        if (!empty($this->input->post()))
        {
        $product_module_id = $this->config->item('product_module');
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $access_common_settings     = $section_modules['access_common_settings'];

            $columns = array(
                0 => 'tds_value',
                1 => 'description',
                2 => 'module_type'
                );
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir   = $this->input->post('order')[0]['dir'];

            $tds_section_id = $this->input->post('tds_section');
            $list_data      = $this->common->tds_field($tds_section_id);

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
            }

            $send_data = array();

            if (!empty($posts))
            {
                $i = 1;

                foreach ($posts as $post)
                {
                    $tds_id = $this->encryption_url->encode($post->tds_id);

                    $nestedData['tds_value'] = '<span id="accounting_tds">' . $this->precise_amount($post->tds_value,$access_common_settings[0]->amount_precision) . '</span>&nbsp;<span id="accounting_tds_id" style="display:none;">' . $post->tds_id . '</span>';
                    $nestedData['module_type'] = $post->module_type;
                    $nestedData['description'] = $post->description;

                    $cols                 = '<span class="btn btn-info apply" class="close" data-dismiss="modal">Apply</span>';
                    $nestedData['action'] = $cols;
                    $send_data[]          = $nestedData;
                }

            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $send_data);
            echo json_encode($json_data);
        }

    }

    public function sales_add_product_inventory(){
        $barcode_symbology    = $this->input->post('barcode_symbology');
        $branch_id            = $this->session->userdata('SESS_BRANCH_ID');
        $product_inventory_id = $this->input->post('product_inventory');

        $product_varient_data = $this->input->post('table_datas');
        $js_data              = json_decode($product_varient_data, true);

        foreach ($js_data as $key => $value)
        {

            $value['product_inventory_id'] = $product_inventory_id;
            $value['added_date']           = date('Y-m-d');
            $value['added_user_id']        = $this->session->userdata('SESS_USER_ID');
            $value['branch_id']            = $this->session->userdata('SESS_BRANCH_ID');

            if ($product_inventory_varients_id = $this->general_model->insertData("product_inventory_varients", $value))
            {

                $this->general_model->insertData("quantity_history", [
                    'item_id'       => $product_inventory_varients_id,
                    'item_type'     => 'product_inventory',
                    'quantity'      => $value['quantity'],
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                    'added_user_id' => $this->session->userdata('SESS_USER_ID')]);

                $a[] = $product_inventory_varients_id;

                $code   = sprintf('%08d', $product_inventory_varients_id);
                $height = array(
                    '0' => 20,
                    '1' => 30,
                    '2' => 50,
                    '3' => 60);

                if (!is_dir('assets/images/barcode/' . $branch_id . '/' . $code))
                {
                    mkdir('./assets/images/barcode/' . $branch_id . '/' . $code, 0777, true);
                }

                for ($i = 0; $i < 4; $i++)
                {
                    $file = Zend_Barcode::draw($barcode_symbology, 'image', array(
                        'text'      => $code,
                        'barHeight' => $height[$i],
                        'drawText'  => 1,
                        'factor'    => 1), array());

                    $store_image = imagepng($file, "./assets/images/barcode/" . $branch_id . "/{$code}/{$code}" . $height[$i] . ".png");
                }

                $barcode_path = "assets/images/barcode/" . $branch_id . "/{$code}/";
                $barcode_data = array(
                    'barcode'           => $barcode_path,
                    'barcode_symbology' => $barcode_symbology,
                    'barcode_number'    => $code);
                $this->general_model->updateData('product_inventory_varients', $barcode_data, array(
                    'product_inventory_varients_id' => $product_inventory_varients_id));
                //barcode ends
            }

            $key_val = json_decode($this->input->post('key_value'), true);
            // print_r($key_val);
        }

        // print_r($a);die;

        for ($i = 0; $i < count($key_val); $i++)
        {

            foreach ($key_val[$i] as $k => $v)
            {

                $varients_key_value = array(
                    'varients_id'                   => $k,
                    'varients_value_id'             => $v,
                    'product_inventory_varients_id' => $a[$i]
                );
            }

            $this->general_model->insertData("product_inventory_varients_value", $varients_key_value);
            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => 0,
                'table_name'        => 'product_inventory_varients_value',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'product_inventory_varients Inserted');
            $this->general_model->insertData('log', $log_data);
        }

    }

    public function combinations(){
        $send = array();
        $val = $this->input->post('value');
        $key = $this->input->post('key');
        $branch_id = $this->session->userdata("SESS_BRANCH_ID");
        $product_code = $this->input->post('product_code');
        $array_key =  json_decode($key);
        $array_value =  json_decode($val);
        $ar = array();
      /*  $tables = array('product_combinations');
        $where_cond = array('product_code' => , 'branch_id' => $branch_id);
        $this->db->where($where_cond);
        $this->db->delete($tables);*/

         $this->db->query("DELETE FROM product_combinations Where product_code = '$product_code' and branch_id = '$branch_id'");
        $product_val = array();
        foreach ($array_key as $key => $value ) {
           $val_key = $array_value[$key];
            foreach ($value as $value1  ) {
                
                $data = $this->general_model->getRecords('*', 'varients_value', array(
                'varients_value_id'   => $value1,
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                $ar[$val_key][] = $data[0]->varients_value;
                $product_val[$data[0]->varients_value] = $value1;
            }
       }
        
        // = $this->general_model->getRecords('*', 'varients', array(
          //  'delete_status' => 0,
            //'branch_id'     => $this->session->userdata('SESS_BRANCH_ID')));

        $counts = array_map("count", $ar);
        $total = array_product($counts);
        $res = [];

        $combinations = [];
        $curCombs = $total;
       

        foreach ($ar as $field => $vals) {
            $curCombs = $curCombs / $counts[$field];
            $combinations[$field] = $curCombs;
        }

        for ($i = 0; $i < $total; $i++) {
            foreach ($ar as $field => $vals) {
                $res[$i][$field] = $vals[($i / $combinations[$field]) % $counts[$field]];
            }
        }
        
        $i = 1;
       foreach ($res as $key => $value) {
            $var_val_id = '';   
            foreach ($value as $val_id) {
             $var_val_id .= $product_val[$val_id].',';
            }
            $var_val_id = rtrim($var_val_id, ",");
            $data_com[$i]['product_code'] = $product_code;
            $data_com[$i]['combinations'] = implode(" / ",$value);
            $data_com[$i]['status'] = 'N';
            $data_com[$i]['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
            $data_com[$i]['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
            $data_com[$i]['varient_value_id'] = $var_val_id;
           // $nestedData['action'] = '<input type="checkbox" name="combination" value="'.$id.'">';
            //$send[] = $nestedData;
           $i++;    
       }
       
       $this->db->insert_batch('product_combinations', $data_com);
       $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                'product_code'   => $product_code,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
        $LeatherCraft_id = $this->config->item('LeatherCraft');
        $check_product = '';
        if($LeatherCraft_id == $this->session->userdata("SESS_BRANCH_ID") ){
            $check_product = 'checked';
        }
       foreach ($combination_data as $com) {
            $nestedData['product_code'] = $com->product_code;
            $nestedData['name'] = $com->combinations;
            $nestedData['action'] = '<input type="checkbox" name="combination[]" value="'.$com->combination_id.'"'.$check_product.'>';
            $send[] = $nestedData;
       }
        $data_fin = array('data'=>$send);
        echo json_encode($data_fin);
    }

    public function fetch_combination(){
        $send = array();
        $id = $this->input->post('id');
        $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                'product_id'   => $id,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));

       foreach ($combination_data as $com) {
        if($com->status == 'Y'){
            $checked = 'checked disabled="true"';
        }else{
            $checked = '';
        }
            $nestedData['product_code'] = $com->product_code;
            $nestedData['name'] = $com->combinations;
           $nestedData['action'] = '<input type="checkbox" name="combination[]" value="'.$com->combination_id.'"'.$checked.'>';
            $send[] = $nestedData;
       }
        $data_fin = array('data'=>$send);
        echo json_encode($data_fin);
    }


    public function edit_combinations(){
        $send = array();
        $val = $this->input->post('value');
        $key = $this->input->post('key');
        $product_code = $this->input->post('product_code');
        $product_id = $this->input->post('product_id');
        $array_key =  json_decode($key);
        $array_value =  json_decode($val);
        $ar = array();
        $data_fin = array();
        $product_val = array();
        foreach ($array_key as $key => $value ) {
           $val_key = $array_value[$key];
            foreach ($value as $value1  ) {
                
                $data = $this->general_model->getRecords('*', 'varients_value', array(
                'varients_value_id'   => $value1,
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
                $ar[$val_key][] = $data[0]->varients_value;
                $product_val[$data[0]->varients_value] = $value1;
            }
       }
      

        $counts = array_map("count", $ar);
        $total = array_product($counts);
        $res = [];

        $combinations = [];
        $curCombs = $total;

        foreach ($ar as $field => $vals) {
            $curCombs = $curCombs / $counts[$field];
            $combinations[$field] = $curCombs;
        }

        for ($i = 0; $i < $total; $i++) {
            foreach ($ar as $field => $vals) {
                $res[$i][$field] = $vals[($i / $combinations[$field]) % $counts[$field]];
            }
        }

        // fetch already created combination data
         $combination_data_name = array();
         $combination_data_already = $this->general_model->getRecords('*', 'product_combinations', array('product_id'   => $product_id,
                    'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));

        foreach ($combination_data_already as $com) {            
            $combination_data_name[] = $com->combinations;         
        }
        //  created combination data for insert batch of product combination with duplicate check
        $i = 1;
       foreach ($res as $key => $value) {
            $combin_name_new =  implode(" / ",$value);
            if(!in_array($combin_name_new, $combination_data_name)){
                 $var_val_id = '';   
                foreach ($value as $val_id) {
                 $var_val_id .= $product_val[$val_id].',';
                }
                $var_val_id = rtrim($var_val_id, ",");
                $data_com[$i]['product_id'] = $product_id;
                $data_com[$i]['product_code'] = $product_code;
                $data_com[$i]['combinations'] = implode(" / ",$value);
                $data_com[$i]['status'] = 'N';
                $data_com[$i]['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
                $data_com[$i]['varient_value_id'] = $var_val_id;  
            }
           $i++;    
       }
      
       if(!empty($data_com)){
        $this->db->insert_batch('product_combinations', $data_com);
       }

       $combination_data = $this->general_model->getRecords('*', 'product_combinations', array(
                'product_code'   => $product_code,
                'branch_id'     => $this->session->userdata("SESS_BRANCH_ID") ));
       $nestedData = array();
       foreach ($combination_data as $com) {
            if($com->status == 'Y'){
                $checked = 'checked disabled="true"';
            }else{
                $checked = '';
            }
            $nestedData['product_code'] = $com->product_code;
            $nestedData['name'] = $com->combinations;
            $nestedData['action'] = '<input type="checkbox" name="combination[]" value="'.$com->combination_id.'"'.$checked.'>';
            $send[] = $nestedData;
        }
        $data_fin = array('data'=>$send);
        echo json_encode($data_fin);
    }

    public function getProductname(){
        $product_name = $this->input->post('product_name');
        $branch_id     = $this->session->userdata("SESS_BRANCH_ID");
        $this->db->select('product_name');
        $this->db->where('branch_id', $branch_id); 
        $this->db->like('product_name', $product_name);                
        $qry = $this->db->get('products');
        $pri_grps = $qry->result_array();
        $this->data['flag'] = false;
        if(!empty($pri_grps)){
            $this->data['flag'] = true;
            $this->data['data'] = $pri_grps;
        }
        echo json_encode($this->data);
    }

    public function getProductname_leatherCraft(){
        $product_name = $this->input->post('product_name');
        $branch_id     = $this->session->userdata("SESS_BRANCH_ID");
        $this->db->select('product_name');
        $this->db->where('branch_id', $branch_id); 
        $this->db->group_by('product_name');
        $this->db->order_by('product_id', 'DESC');              
        $qry = $this->db->get('products');
        $pri_grps = $qry->result_array();
        $this->data['flag'] = false;
        if(!empty($pri_grps)){
            $this->data['flag'] = true;
            $this->data['data'] = $pri_grps;
        }
        echo json_encode($this->data);
    }

    public function sales_product(){
        $product_module_id         = $this->config->item('sales_stock_report');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        if (!empty($this->input->post())){
            
            $columns  = array(
                    0  => 'product_name',
                    1  => 'reference_number',
                    2 => 'qty',                    
                    5 => 'amt',
                    5 => 'sales_cost',
                    5 => 'pur_amt',
                    6 => 'purchase_cost',
                    7 => 'gross_profit',
                    8 => 'margin_percntage'
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->get_sales_product_stock();
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
            if (!empty($posts)){
                $i = 1;
                foreach ($posts as $post){
                  
                    $product_id  = $post->product_id;
                    $sales_amt = round($post->sales_amt,2);
                    $sal_cn_amt = round($post->sal_cn_amt,2);
                    $sal_dn_amt = round($post->sal_dn_amt,2);
                    if($sal_cn_amt != '' && $sal_dn_amt != ''){
                        $unit_price = ($sales_amt + $sal_cn_amt + $sal_dn_amt) / 3;
                    }elseif($sal_cn_amt != ''){
                        $unit_price = ($sales_amt + $sal_cn_amt) / 2;
                    }elseif( $sal_dn_amt != ''){
                        $unit_price = ($sales_amt + $sal_dn_amt) / 2;
                    }else{
                        $unit_price = $sales_amt;
                    }
                    
                    $sales_qty = round($post->sales_qty,2);
                    $sal_dn_qty = round($post->sal_dn_qty,2);
                    $sal_cn_qty = round($post->sal_cn_qty,2);
                    $qty = $sales_qty + $sal_dn_qty - $sal_cn_qty;
                    $sales_cost = $unit_price * $qty;

                    $pur_amt = round($post->pur_amt,2);
                    $pur_cn_amt = round($post->pur_cn_amt,2);
                    $pur_dn_amt = round($post->pur_dn_amt,2);
                    if($pur_cn_amt != '' && $pur_dn_amt != ''){
                        $unit_price_pur = ($pur_amt + $pur_cn_amt + $pur_dn_amt) / 3;
                    }elseif($pur_cn_amt != ''){
                        $unit_price_pur = ($pur_amt + $pur_cn_amt) / 2;
                    }elseif( $pur_dn_amt != ''){
                        $unit_price_pur = ($pur_amt + $pur_dn_amt) / 2;
                    }else{
                        $unit_price_pur = $pur_amt;
                    }
                    
                   // $pur_qty = round($post->pur_qty,2);
                   // $pur_dn_qty = round($post->pur_dn_qty,2);
                   // $pur_cn_qty = round($post->pur_cn_qty,2);
                  //  $qty = $pur_qty - $pur_dn_qty + $pur_cn_qty;
                    $purchase_cost = $unit_price_pur * $qty;

                    $gross_profit =  $unit_price - $unit_price_pur;
                    $sales_cost_div = ($sales_cost > 0 ? $sales_cost : 1);
                    $margin_sell_percentage = ($gross_profit / $sales_cost_div) * 100;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['reference_number']    = $post->REF;
                    $nestedData['qty']  = $qty;
                    $nestedData['amt'] = $this->precise_amount($unit_price,2);
                    $nestedData['sales_cost'] = $this->precise_amount($sales_cost,2);
                    $nestedData['pur_amt'] = $this->precise_amount($unit_price_pur,2);
                    $nestedData['purchase_cost'] = $this->precise_amount($purchase_cost,2);
                    $nestedData['gross_profit'] = $this->precise_amount($gross_profit,2);
                     $nestedData['product_sku'] = $post->product_sku;
                    $nestedData['margin_percntage'] = round($margin_sell_percentage,2).'%';
                        $send_data[]          = $nestedData;
                        $i++;
                   }

                    
                }
                $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        } else {

            $this->load->view('product/sales_product_stock',$data);
        }
    }

    public function remove_image($id){       
        
        $this->general_model->updateData('products', array(
                'product_image' => '' ), array(
                'product_id'       => $id,
                'branch_id'       => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));
       
        $product_id = $this->encryption_url->encode($id);
              
        redirect('product/edit/'.$product_id, 'refresh');
    }

    public function get_product_sku_bulk($product_code , $category_id){
        $data   = $this->general_model->getRecords('category_code', 'category', array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,           
            'category_id ='  => $category_id),"","category_code");
        $item_category_code =  $data[0]->category_code;
        $category_code =  explode("-", $item_category_code);
                $sku_code = $category_code[1]."-".$product_code;
        return $sku_code;
    }

    public function get_product_sku(){
        $product_code = $this->input->post('product_code');
        $category_id   = $this->input->post('category_id');


        $data   = $this->general_model->getRecords('category_code', 'category', array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,           
            'category_id ='  => $category_id),"","category_code");
        $item_category_code =  $data[0]->category_code;
        $category_code =  explode("-", $item_category_code);
                $sku_code = $category_code[1]."-".$product_code;
        echo json_encode($sku_code);
    }

    public function product_batch() {
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $data['product_module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "view_privilege";
        $data['privilege'] = "view_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $data['tax_module_id']    = $this->config->item('tax_module');
        $data['tax_gst']          = $this->tax_call_type('GST');
        $data['discount']         = $this->discount_call();
        $data['products']   = $this->general_model->getRecords('product_id,product_code,product_batch,product_name,product_mrp_price,batch_serial,batch_parent_product_id', 'products', array(
            'batch_parent_product_id' => 0,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0),array('product_id'=>'DESC'),"");
        
        $this->load->view('product/product_batch', $data);
    }

    public function add_product_batch(){
        
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id; 
        $modules           = $this->modules;
        $privilege         = "add_privilege";
        $data['privilege'] = "add_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $main_pro_id = $this->input->post('batch_parent_product_id');
        $main_pro_detail = $this->general_model->getRecords('*', 'products', 
            array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,'product_id' => $main_pro_id),'',"");
        $main_pro_detail = $main_pro_detail[0];
        $product_code = $main_pro_detail->product_code;
        if($this->input->post('product_code')) $product_code = $this->input->post('product_code');
        $product_price = $main_pro_detail->product_price;
        if($this->input->post('product_price')) $product_price = $this->input->post('product_price');

        $product_data = array(
            "product_code"           => $product_code,
            "product_name"           => $main_pro_detail->product_name,
            "product_hsn_sac_code"   => $main_pro_detail->product_hsn_sac_code,
            "product_category_id"    => $main_pro_detail->product_category_id,
            "product_subcategory_id" => $main_pro_detail->product_subcategory_id,
            "product_quantity"       => 0,
            "brand_id"               => $main_pro_detail->brand_id,
            "product_unit"           => $main_pro_detail->product_unit,
            "product_price"          => $product_price,
            "product_tds_id"         => $main_pro_detail->product_tds_id,
            "product_tds_value"      => $main_pro_detail->product_tds_value,
            "product_gst_id"         => $this->input->post('gst_tax_product'),
            "product_gst_value"      => $this->input->post('product_gst_code'),
            "product_discount_id"    => $this->input->post('product_discount'),
            "product_details"        => $main_pro_detail->product_details,
            "is_assets"              => $main_pro_detail->is_assets,
            "is_varients"            => $main_pro_detail->is_varients,
            "product_unit_id"        => $main_pro_detail->product_unit_id,
            "product_type"          => $main_pro_detail->product_type,
            "product_mrp_price"     => $this->input->post('product_mrp'),
            "product_selling_price" => $this->input->post('product_selling_price'),
            "product_sku"           => $main_pro_detail->product_sku,
            "product_serail_no"     => $main_pro_detail->product_serail_no,
            "product_image"         => $main_pro_detail->product_image,
            'barcode'               => $main_pro_detail->barcode,
            'parent_id'             => $main_pro_detail->parent_id,
            'is_assets'             => $main_pro_detail->is_assets,
            "added_date"            => date('Y-m-d'),
            "product_batch"         => $this->input->post('product_batch'),
            'batch_parent_product_id' => $this->input->post('batch_parent_product_id'),
            /*'batch_serial' => $this->input->post('batch_serial'),*/
            "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
            "branch_id"              => $this->session->userdata('SESS_BRANCH_ID')
        );

        if($this->input->post('batch_serial')){
            $product_data['batch_serial'] = $this->input->post('batch_serial');
        }

        if($this->input->post('margin_discount_value')){
            $product_data['margin_discount_value'] = $this->input->post('margin_discount_value');
            $product_data['margin_discount_id'] = $this->input->post('margin_discount');
        }

        if($this->input->post('product_discount_value')){
            $product_data['product_discount_value'] = $this->input->post('product_discount_value');
        }

        if($this->input->post('product_basic_price')){
            $product_data['product_basic_price'] = $this->input->post('product_basic_price');
        }

        if($this->input->post('profit_margin')){
            $product_data['product_profit_margin'] = $this->input->post('profit_margin');
        }
        
        if ($product_id = $this->general_model->insertData('products', $product_data)){
            $update = array('batch_serial' => $this->input->post('batch_serial'));
            $this->general_model->updateData('products',$update,array('product_id' => $this->input->post('batch_parent_product_id')));

            $successMsg = 'Product Batch Added Successfully';
            $this->session->set_flashdata('product_success',$successMsg);
            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $product_id,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Batch Inserted');
            $this->general_model->insertData('log', $log_data);
            
        }else{
            $errorMsg = 'Product Batch Add Unsuccessful';
            $this->session->set_flashdata('product_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Product Batch can not be Inserted.');
        }
        redirect("product/product_batchList", 'refresh');
        exit;
    }

    public function edit_batch($id)
    {
        $product_id        = $this->encryption_url->decode($id);
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id;
        $data['product_module_id'] = $product_module_id;
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        $data['category_module_id']    = $this->config->item('category_module');
        $data['subcategory_module_id'] = $this->config->item('subcategory_module');
        $data['tax_module_id']         = $this->config->item('tax_module');
        
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $products = $this->general_model->getRecords('*', 'products', array(
            'product_id'    => $product_id,
            'delete_status' => 0));
        
        $data['products_detail'] =  $products[0];
        /*echo "<pre>";
        print_r($data['products_detail']);exit;*/
        $data['discount']         = $this->discount_call();
        $data['tax_gst']          = $this->tax_call_type('GST');
        $this->load->view('product/edit_batch', $data);
    }

    public function edit_product_batch(){
        $product_module_id = $this->config->item('product_module');
        $data['module_id'] = $product_module_id; 
        $modules           = $this->modules;
        $privilege         = "edit_privilege";
        $data['privilege'] = "edit_privilege";
        $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $product_id = $this->input->post('product_id');
        $main_pro_id = $this->input->post('batch_parent_product_id');
        if($main_pro_id == 0 || $main_pro_id == ''){
            $main_pro_id = $product_id;
        }
        $main_pro_detail = $this->general_model->getRecords('*', 'products', 
            array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,'product_id' => $main_pro_id),'',"");
        $main_pro_detail = $main_pro_detail[0];
        $product_code = $main_pro_detail->product_code;
        if($this->input->post('product_code')) $product_code = $this->input->post('product_code');
        $product_data = array(
            "product_code" => $product_code,
            "product_gst_id"         => $this->input->post('gst_tax_product'),
            "product_gst_value"      => $this->input->post('product_gst_code'),
            "product_discount_id"    => $this->input->post('product_discount'),
            "product_mrp_price"     => $this->input->post('product_mrp'),
            "product_selling_price" => $this->input->post('product_selling_price'),
            'margin_discount_value' => $this->input->post('margin_discount_value'),
            'margin_discount_id' => $this->input->post('margin_discount'),
            'product_discount_value' => $this->input->post('product_discount_value'),
            'product_basic_price' => $this->input->post('product_basic_price'),
            "updated_date"           => date('Y-m-d'),
            "updated_user_id"        => $this->session->userdata('SESS_USER_ID')
        );

        $product_price = $main_pro_detail->product_price;
        if($this->input->post('product_price')) $product_data['product_price'] = $this->input->post('product_price');

        if($this->input->post('profit_margin')){
            $product_data['product_profit_margin'] = $this->input->post('profit_margin');
        }
       
        if ($this->general_model->updateData('products', $product_data, array(
            'product_id' => $product_id))){
            $update = array('batch_serial' => $this->input->post('batch_serial'));
            $this->general_model->updateData('products',$update,array('product_id' => $this->input->post('batch_parent_product_id')));

            $successMsg = 'Product Batch Updated Successfully';
            $this->session->set_flashdata('product_success',$successMsg);

            /*$ecommerce = 1;
            if($ecommerce){
                $product_data['variants'] = $ecomm_variant_product;
                $product_data['product_id'] = $product_id;
                $product_data['product_image'] = base_url().$url;
                $this->producthook->UpdateProduct($product_data);
            }*/

            $log_data = array(
                'user_id'           => $this->session->userdata('user_id'),
                'table_id'          => $product_id,
                'table_name'        => 'products',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                'message'           => 'Product Batch Updated');
            $this->general_model->insertData('log', $log_data);
            redirect('product/product_batchList', 'refresh');
            
        }else{
            $errorMsg = 'Product Batch Update Unsuccessful';
            $this->session->set_flashdata('product_error',$errorMsg);
            $this->session->set_flashdata('fail', 'Product Batch can not be updated.');
        }
        redirect("product/product_batchList", 'refresh');
        exit;
    }

    public function stock_movement(){
        $product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        if (!empty($this->input->post())){
            
            $columns  = array(
                    0  => 'product_name',
                    1  => 'reference_number',
                    2 => 'qty',                    
                    5 => 'amt',
                    5 => 'sales_cost',
                    5 => 'pur_amt',
                    6 => 'purchase_cost',
                    7 => 'gross_profit',
                    8 => 'margin_percntage'
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->get_stock_movement();
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
            if (!empty($posts)){
                $i = 1;
                foreach ($posts as $post){
                  
                    $product_id  = $post->product_id;

                    $unit_price = round($post->purchase_price,2);
                    if($unit_price > 0){
                        $unit_price = $unit_price;
                    }else{
                        $unit_price = round($post->sales_price,2);
                    }
                    
                    $opening_quantity = round($post->product_opening_quantity,2);

                    $sales_qty = round($post->sales_qty,2);
                    $sal_dn_qty = round($post->sales_debit_qty,2);
                    $sal_cn_qty = round($post->sales_credit_qty,2);
                    $out_qty = $sales_qty + $sal_dn_qty - $sal_cn_qty;

                    
                    
                   $pur_qty = round($post->purchase_qty,2);
                   $pur_dn_qty = round($post->purchase_debit_qty,2);
                   $pur_cn_qty = round($post->purchase_credit_qty,2);
                   $in_qty  = $pur_qty - $pur_dn_qty + $pur_cn_qty;

                   $closing_stock = $opening_quantity +  $in_qty -  $out_qty;
                    $closing_stock = round($closing_stock,2);
                    $closing_value = $closing_stock * $unit_price;
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['product_sku'] = $post->product_sku;
                    $nestedData['opening_stock'] = $opening_quantity;
                    $nestedData['purchase_qty'] = $in_qty;
                    $nestedData['sales_qty']  = $out_qty;
                    $nestedData['closing_stock'] = round($closing_stock,2);
                    $nestedData['unit_price'] = $this->precise_amount($unit_price,2);
                    $nestedData['closing_value'] = $this->precise_amount($closing_value,2);

                        $send_data[]          = $nestedData;
                        $i++;
                   }

                    
                }
                $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        } else {

            $this->load->view('product/stock_movement',$data);
        }
    }

    public function add_bulk_upload_product_leathercraft(){
        $data =  $insData = array();
        $error_log = '';

        $path = 'uploads/productCSV/';
        require_once APPPATH . "/third_party/PHPExcel.php";
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'csv';
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);             
        $errors_email  = $header_row = array();

        if (!$this->upload->do_upload('bulk_product')) {
            /*$error = array('error' => );*/
            $this->session->set_flashdata('bulk_error_product',$this->upload->display_errors());
            /*$this->session->set_userdata('bulk_error', $this->upload->display_errors());*/
        } else {
            $product_module_id = $this->config->item('product_module');
            $data['module_id'] = $product_module_id;
            $modules           = $this->modules;
            $privilege         = "add_privilege";
            $data['privilege'] = $privilege;
            $section_modules   = $this->get_section_modules($product_module_id, $modules, $privilege);

            /* presents all the needed */
            $data = array_merge($data, $section_modules);

            /* presents all the needed */
            $Updata = array('uploadData' => $this->upload->data());
            
            if (!empty($Updata['uploadData']['file_name'])) {
                $import_xls_file = $Updata['uploadData']['file_name'];
                $inputFileName = $path . $import_xls_file;
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);               
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

                    if(!empty($allDataInSheet)){
                        
                        if(strtolower($allDataInSheet[1]['A']) == 'article*' && strtolower($allDataInSheet[1]['B']) == 'product name*' && strtolower($allDataInSheet[1]['C']) == 'product type*' && strtolower($allDataInSheet[1]['D']) == 'product hsn sac code*' && strtolower($allDataInSheet[1]['E']) == 'category*' && strtolower($allDataInSheet[1]['F']) == 'subcategory' && strtolower($allDataInSheet[1]['G']) == 'unit of measurement*' && strtolower($allDataInSheet[1]['H']) == 'gst tax percentage' && strtolower($allDataInSheet[1]['I']) == 'tcs tax percentage' && strtolower($allDataInSheet[1]['J']) == 'markdown discount' && strtolower($allDataInSheet[1]['K']) == 'mrp*' && strtolower($allDataInSheet[1]['L']) == 'serial number' && strtolower($allDataInSheet[1]['M']) == 'description' && strtolower($allDataInSheet[1]['N']) == 'marginal discount' && strtolower($allDataInSheet[1]['O']) == 'size' && strtolower($allDataInSheet[1]['P']) == 'colour' && strtolower($allDataInSheet[1]['Q']) == 'expiry date' && strtolower($allDataInSheet[1]['R']) == 'brand*' && strtolower($allDataInSheet[1]['S']) == 'opening stock' && strtolower($allDataInSheet[1]['T']) == 'purchase price' && strtolower($allDataInSheet[1]['U']) == 'batch' && strtolower($allDataInSheet[1]['V']) == 'ean code/barcode' && strtolower($allDataInSheet[1]['W']) == 'warehouse*'){
                                $header_row = array_shift($allDataInSheet);
                                $product_exist = $this->general_model->GetProductName();
                                $product_exist = array_column($product_exist, 'product_name', 'product_name');
                                $hsn = $this->general_model->hsn_call_product_bulk();
                                $hsn = array_column($hsn, 'hsn_code', 'hsn_code');
                                $category = $this->general_model->GetCategory_bulk_leathercraft('product');
                                $category = array_column($category, 'category_id', 'category_name');
                                $sub_category = $this->general_model->GetSubCategory_bulk_leathercraft('product');
                                /*$sub_category_id = array_column($sub_category, 'category_id_sub','subcategory_name');
                                $sub_category= array_column($sub_category, 'sub_category_id', 'subcategory_name');*/
                                $uom = $this->general_model->Get_uqc_bulk_leathercraft('product');
                                $uom = array_column($uom, 'uom_id', 'uom');
                                $warehouse = $this->general_model->Get_warehouse_bulk_leathercraft();
                                $warehouse = array_column($warehouse, 'warehouse_id', 'warehouse_name');                                
                                $gst = $this->general_model->Get_tax_bulk('GST');
                                $gst = array_column($gst, 'tax_id', 'tax_value');
                                $tcs = $this->general_model->Get_tax_bulk('TCS');
                                $tcs = array_column($tcs, 'tax_id', 'tax_value');
                                $discount = $this->general_model->Get_discount_bulk();
                                $discount = array_column($discount, 'discount_id', 'discount_value');
                                //$hsn = $this->array_flatten($hsn);
                                $brand = $this->general_model->GetBrand_bulk_leathercraft('product');
                                $brand = array_column($brand, 'brand_id', 'brand_name');
                                $access_settings          = $data['access_settings'];
                                $primary_id               = "product_id";
                                $table_name               = "products";
                                $date_field_name          = "added_date";
                                $current_date             = date('Y-m-d');
                                $error_array = array(); 

                                $type_array = array();
                                $type_array['raw material'] = "rawmaterial";
                                $type_array['semi finished goods'] ="semifinishedgoods";
                                $type_array['finished goods'] ="finishedgoods";

                                foreach($allDataInSheet as $row){ 
                                    $product_code = (trim($row['A']));
                                    $product_name = strtolower(trim($row['B']));
                                    $product_type = strtolower(trim($row['C']));
                                    $hsn_number = trim($row['D']);
                                    $name_category= strtolower(trim($row['E']));
                                    $name_category = str_replace(' ', '', $name_category);
                                    $name_subcategory= strtolower(trim($row['F']));
                                    $name_subcategory = str_replace(' ', '', $name_subcategory);
                                    $unit_of_measurement= strtolower(trim($row['G']));
                                    $unit_of_measurement = str_replace(' ', '', $unit_of_measurement);
                                    $product_gst= trim($row['H']);
                                    $product_gst = rtrim($product_gst, "%");
                                    $product_tcs= trim($row['I']);
                                    $product_tcs = rtrim($product_tcs, "%");
                                    $marginal_discount_product = trim($row['N']);
                                    $marginal_discount_product = rtrim($marginal_discount_product, "%");
                                    $discount_product = trim($row['J']);
                                    $discount_product = rtrim($discount_product, "%");
                                    $product_sku = '';
                                    $product_category_id = '';
                                    $product_subcategory_id = '';
                                    $is_add = true;
                                    $error = '';
                                    $tcs_product_id ='';
                                    $gst_product_id = '';
                                    $discount_product_id = '';
                                    $product_size = trim($row['O']);
                                    $product_colour = trim($row['P']);
                                    $product_name = trim($row['B']);
                                    $expiry_date = trim($row['Q']);
                                    $brand_name = trim($row['R']);
                                    $brand_name = str_replace(' ', '', $brand_name);
                                    $opening_stock = trim($row['S']);
                                    $purchase_price = trim($row['T']);
                                    $expiry_date = date('Y-m-d',strtotime($expiry_date));
                                    $product_batch = trim($row['U']);
                                    $product_barcode = trim($row['V']);
                                    $warehouse_name = trim($row['W']);
                                    $warehouse_name = str_replace(' ', '', $warehouse_name);
                                    $parent_id = 0;
                                    /*$product_code   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);*/
                                    $batch = $this->get_bulk_check_product_leathercraft($product_name,$product_code,0);
                                    $combination_id = NULL;
                                    if(count($batch) > 0){
                                        if($product_size != '' || $product_colour != '' ){
                                            $value = '';
                                            $var_val_id = '';
                                            $n = 1;
                                            $product_code_comb = $batch[0]->product_code;
                                            $varient_product_id = $batch[0]->product_id;
                                            if($product_colour != ''){
                                                $colour_key_id = $this->get_varient_key_id('Colour');
                                                $colour_id = $this->get_varient_value_id($product_colour,$colour_key_id);
                                                $value .= $product_colour.' / ';
                                                $var_val_id .= $colour_id.',';
                                                $data_product_var_val[$n]['varients_value_id'] = $colour_id;
                                                $data_product_var_val[$n]['varients_id'] =  $colour_key_id;
                                                $data_product_var_val[$n]['product_varients_id'] =  $varient_product_id;
                                                $data_product_var_val[$n]['delete_status'] =  0;
                                                $n = $n+1;
                                            }
                                          if($product_size != ''){
                                            $size_key_id = $this->get_varient_key_id('Size');
                                            $size_value_id = $this->get_varient_value_id($product_size,$size_key_id);
                                            $value .= $product_size.' / ';
                                            $var_val_id .= $size_value_id.',';
                                            $data_product_var_val[$n]['varients_value_id'] = $size_value_id;
                                             $data_product_var_val[$n]['varients_id'] =  $size_key_id;
                                             $data_product_var_val[$n]['product_varients_id'] =  $varient_product_id;
                                             $data_product_var_val[$n]['delete_status'] =  0;
                                          }
                                          $var_val_id = rtrim($var_val_id, ",");
                                          $value = rtrim($value, ' / ');
                                          
                                          
                                          $data_com = array();
                                          $data_com['product_code'] = $product_code_comb;
                                          $data_com['combinations'] = $value;
                                          $data_com['status'] = 'N';
                                          $data_com['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
                                          $data_com['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
                                          $data_com['varient_value_id'] = $var_val_id;
                                          $data_com['product_id'] = $varient_product_id;
                                          $combination_id = $this->general_model->insertData('product_combinations', $data_com);
                                
                                          $this->db->insert_batch('product_varients_value', $data_product_var_val);
                                          $is_varients = 'N';
                                          $product_name = $product_name.' / '.$value;
                                          $batch = $this->get_bulk_check_product_leathercraft($product_name,$product_code,0);

                                            if(count($batch) > 0){
                                                $batch_num=$batch[0]->num;
                                                $number = intval($batch_num)+1;
                                                $product_batch = 'BATCH-0'.$number; 
                                                $parent_id = $batch[0]->product_id;
                                            }else{
                                                $product_batch = "BATCH-01";
                                            }

                                           $update_compina_status = array('status' => 'Y');
                                             $this->general_model->updateData('product_combinations', $update_compina_status, array('combination_id' => $combination_id));
                                        }else{
                                            $batch_num=$batch[0]->num;
                                            $number = intval($batch_num)+1;
                                            $product_batch = 'BATCH-0'.$number; 
                                            $is_varients = 'N'; 
                                            $parent_id = $batch[0]->product_id;
                                            $combination_id = NULL;
                                        }                                        
                                    } else {
                                       if($product_size != '' || $product_colour != '' ){
                                          $is_varients = 'Y';                                           
                                       }
                                       $combination_id = NULL;
                                        $product_batch = "BATCH-01";
                                    }

                                    if(($product_type == 'semifinishedgoods' || $product_type == 'finishedgoods' || $product_type == 'rawmaterial')){
                                            $product_type = $product_type;
                                    }else{
                                        $p_type = '';
                                        $p_type = $type_array[$product_type];
                                        if($p_type != '' && !empty($p_type)){
                                                $product_type = $p_type;
                                        }
                                    }

                                    if($product_type != '' && !empty($product_type)){
                                        if(($product_type == 'semifinishedgoods' || $product_type == 'finishedgoods' || $product_type == 'rawmaterial')){
                                           /* if($hsn_number != '' && !empty($hsn_number)){
                                                if(in_array($hsn_number, $hsn)){*/
                                                    if($name_category !='' && !empty($name_category)){
                                                        if(isset($category[$name_category]) && $is_add == true){
                                                           $product_category_id = $category[$name_category];
                                                           $product_sku = $this->get_product_sku_bulk($product_code,$product_category_id);
                                                            if($name_subcategory != '' || !empty($name_subcategory)){
                                                                $subcategory_id = array();
                                                                foreach($sub_category as $val){
                                                                    if($val['category_id_sub'] == $product_category_id){
                                                                        $subcategory_id[$val['subcategory_name']] = $val['sub_category_id'];
                                                                    }
                                                                }
                                                                if(isset($subcategory_id[$name_subcategory])){
                                                                     $product_subcategory_id = $subcategory_id[$name_subcategory];
                                                                    /*if($product_category_id == $subcategory_cat_value){
                                                                        $product_subcategory_id = $sub_category[$name_subcategory];
                                                                    }else {
                                                                        $product_subcategory_id = '';
                                                                        //$is_add = false;
                                                                        //$error = "SubCategory Name is Not Exist! For Entered Category Name";
                                                                        //$error_log .= $row['F'].' Undefined SubCategory Name! <br>';
                                                                    }*/
                                                                }else {
                                                                    /*$is_add = false;
                                                                    $error = "SubCategory Name is Not Exist! Please Update Your SubCategory Name";
                                                                    $error_log .= $row['F'].' Undefined SubCategory Name! <br>';*/
                                                                    $product_subcategory_id = '';
                                                                }  
                                                            }            
                                                        }else{       
                                                            $product_category_id = $this->add_category($row['E']);
                                                            $product_sku = $this->get_product_sku_bulk($product_code,$product_category_id);
                                                            /*$is_add = false;
                                                            $error = "Category Name is Not Exist! Please Update Your Category Name";
                                                            $error_log .= $row['E'].' Undefined Category Name! <br>';*/
                                                        }
                                                        if(($unit_of_measurement !='' || !empty($unit_of_measurement)) && $is_add == true){
                                                            if(isset($uom[$unit_of_measurement])){
                                                                $product_unit_id = $uom[$unit_of_measurement];
                                                                if($product_gst != '' && !empty($product_gst)){
                                                                    $product_gst = $this->precise_amount($product_gst, 2);
                                                                    if(isset($gst[$product_gst])){
                                                                        $gst_product_id = $gst[$product_gst];
                                                                        if($product_tcs != '' && !empty($product_tcs)){
                                                                            $product_tcs = $this->precise_amount($product_tcs, 2);
                                                                            if(isset($tcs[$product_tcs])){
                                                                                $tcs_product_id = $tcs[$product_tcs];
                                                                                if($discount_product != '' && !empty($discount_product)){
                                                                                    $discount_product = $this->precise_amount($discount_product, 2);
                                                                                    if(isset($discount[$discount_product])){
                                                                                        $discount_product_id = $discount[$discount_product];
                                                                                    }else{
                                                                                        $discount_product_id = $this->add_discount($discount_product);
                                                                                       /* $is_add = false;
                                                                                        $error = "Marginal Discount Value is Not Exist! Please Update Your Discount value";
                                                                                        $error_log .= $row['J'].' Undefined Marginal Discount Value! <br>'; */
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                $tax_tcs_value = $product_tcs;
                                                                                $tcs_product_id = $this->add_tax('TCS',$tax_tcs_value);
                                                                               /* $is_add = false;
                                                                                $error = "TCS Value is Not Exist! Please Update Your TCS value";
                                                                                $error_log .= $row['I'].' Undefined TCS Value! <br>';*/
                                                                            }
                                                                        } 
                                                                    } else {
                                                                        // $is_add = false;
                                                                        // $error = "GST Value is Not Exist! Please Update Your GST value";
                                                                        // $error_log .= $row['H'].' Undefined GST Value! <br>';
                                                                        $tax_value = $product_gst;
                                                                        $gst_product_id = $this->add_tax('GST',$tax_value);
                                                                    }
                                                                }
                                                            } else {
                                                                $is_add = false;
                                                                $error = "Unit_Of_Measurement Name is Not Exist! Please Update Your Unit_Of_Measurement Name";
                                                                $error_log .= $row['G'].' Undefined Unit_Of_Measurement Name! <br>';
                                                            }
                                                        }elseif($is_add == true){
                                                            $is_add = false;
                                                             $error = "Unit_Of_Measurement Name Should Not Empty";
                                                             $error_log .= $row['F'].' Unit_Of_Measurement Name is Not Exist! <br>';
                                                        }
                                                    }else{
                                                        $is_add = false;
                                                        $error = "Category Name is Empty";
                                                    }
                                               /* }else{
                                                    $is_add = false;
                                                    $error = "HSN Number is Not Exist! Please Update HSN Data";
                                                    $error_log .= $row['D'].' Undefined HSN Number! <br>';
                                                }*/
                                            /*}else{
                                                $is_add = false;
                                                $error = "HSN number should not empty!";
                                                $error_log .= $row['C'].'HSN number should not empty! <br>';
                                            }*/
                                        }else{                                           
                                                $is_add = false;
                                                $error = "Incorrect Product Type!";
                                                $error_log .= $row['C'].' Incorrect Product Type! <br>';
                                            
                                        }
                                    }else{
                                        $is_add = false;
                                        $error = "Product Type Is Empty!";
                                        $error_log .= $row['C'].' Product Type Is Empty!';
                                    }
                                    /*if(in_array($product_name, $product_exist)) {
                                        $is_add = false;
                                    }*/ 

                                    $mrp = trim($row['K']);
                                    $mrp = (int)$mrp;
                                    if($is_add){
                                        if($mrp == 0 || $mrp =='' ){
                                            $is_add = false;
                                            $error = "MRP Is Empty!";
                                            $error_log .= $row['K'].' Invalid MRP Value.';
                                        }
                                    }
                                    $markdown_discount_product_id = '';
                                    $marginal_discount_product_id = '';
                                    if($is_add){
                                        if($marginal_discount_product != '' && !empty($marginal_discount_product)){
                                            $marginal_discount_product = $this->precise_amount($marginal_discount_product, 2);
                                            if(isset($discount[$marginal_discount_product])){
                                                $marginal_discount_product_id = $discount[$marginal_discount_product];
                                            }else{
                                                $marginal_discount_product_id = $this->add_discount($marginal_discount_product);
                                               /* $is_add = false;
                                                $error = "Marginal Discount Value is Not Exist! Please Update Your Discount value";
                                                $error_log .= $row['N'].' Undefined Markdown Discount Value! <br>'; */
                                            }
                                        }
                                    }
                                    $brand_id = 0;
                                    if($is_add){
                                        if($brand_name != '' || !empty($brand_name)){
                                            if(isset($brand[strtolower($brand_name)])){
                                                $brand_id = $brand[strtolower($brand_name)];
                                            }else{
                                                $is_add = false;
                                                $error = "Brand name is Not Exist! Please Update Your Brand name";
                                                $error_log .= $row['R'].' Brand name is Not Exist!';
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Brand name is empty!";
                                            $error_log .= $row['R'].' Brand name is empty!';
                                        }
                                    }
                                    $warehouse_id = 0;
                                    if($is_add){
                                        if($warehouse_name != '' || !empty($warehouse_name)){
                                            if(isset($warehouse[strtolower($warehouse_name)])){
                                                $warehouse_id = $warehouse[strtolower($warehouse_name)];
                                            }else{
                                                $is_add = false;
                                                $error = "Warehouse name is Not Exist! Please Update Your Warehouse name";
                                                $error_log .= $row['R'].' Warehouse name is Not Exist!';
                                            }
                                        }else{
                                            $is_add = false;
                                            $error = "Warehouse name is empty!";
                                            $error_log .= $row['R'].' Brand name is empty!';
                                        }
                                    }
                                   // product_basic_price
                                    $basic_price = 0;
                                    $gst_amt_basic = 0;
                                    $marginal_amnt = 0;
                                    $selling_price = 0;
                                    $markdwn_amnt = 0;
                                    if($mrp > 0){
                                        $selling_price = $mrp;
                                        $markdown_discount = $this->precise_amount($discount_product,2);
                                        if($markdown_discount > 0){
                                            $markdwn_amnt = ($mrp * $markdown_discount) / 100;
                                            $selling_price = $mrp - $markdwn_amnt;
                                        }
                                        if($product_gst > 0 ){
                                            $gst_amt_basic = ($selling_price * $product_gst) / (100 + $product_gst);
                                        }
                                        if($marginal_discount_product > 0){
                                            $marginal_amnt = ($selling_price * $marginal_discount_product) / 100;
                                        }
                                        $basic_price = $selling_price - $gst_amt_basic - $marginal_amnt;
                                    }

                                    if($is_add){                                        
                                        $headers = array(
                                            'product_code' => $product_code,
                                            "product_batch" => $product_batch,
                                            "product_opening_quantity" => $opening_stock,
                                            "product_hsn_sac_code" => trim($row['D']),
                                            "product_sku" => $product_sku,
                                            "product_serail_no" => trim($row['L']),
                                            "product_name" => $product_name,
                                            "product_unit" => $product_unit_id,
                                            "product_unit_id" => $product_unit_id,
                                            "product_price"  => $purchase_price,
                                            "product_mrp_price" => trim($row['K']),
                                            "product_selling_price" => $selling_price,
                                            "product_category_id" => $product_category_id,
                                            "product_subcategory_id" => $product_subcategory_id,
                                            "product_tds_id" => $tcs_product_id,
                                            "product_tds_value" => trim($row['I']),
                                            "product_gst_id" => $gst_product_id,
                                            "product_gst_value" => trim($row['H']),
                                            "product_details" => trim($row['M']),
                                            "product_discount_id" => $discount_product_id,
                                            "product_discount_value" => $this->precise_amount($discount_product,2),
                                            "product_type" => $product_type,
                                            "is_assets" => 'N',
                                            "is_varients" => $is_varients,
                                            "delete_status" => 0,
                                            "added_date" => date('Y-m-d'),
                                            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                            "batch_serial" => '',
                                            "batch_parent_product_id" => $parent_id,
                                            "product_basic_price" => $this->precise_amount($basic_price, 2),
                                            "margin_discount_value" => $marginal_discount_product,
                                            "margin_discount_id" => $marginal_discount_product_id,
                                            "brand_id" => $brand_id,
                                            "product_barcode" => $product_barcode,
                                            "warehouse_id" => $warehouse_id
                                        );
                                        if($expiry_date != '1970-01-01'){
                                            $headers["exp_date"] = $expiry_date;
                                        }

                                        if($is_varients == 'N'){
                                            $headers["product_combination_id"] = $combination_id;
                                        }

                                        $product_id = $this->general_model->insertData($table_name, $headers);
                                        /*if($next_batch > 1){
                                            $update = array('batch_serial' => $next_batch);
                                            $this->general_model->updateData('products',$update,array('product_id' => $parent_id));
                                        }*/
                                        if($is_varients == 'Y'){
                                            if($product_size != '' || $product_colour != '' ){
                                                $value = '';
                                                $var_val_id = '';
                                                $n = 1;
                                                $product_code = $product_code;
                                                $product_id = $product_id;
                                                $varient_product_id = $product_id;

                                                if($product_colour != ''){
                                                    $colour_key_id = $this->get_varient_key_id('Colour');
                                                    $colour_id = $this->get_varient_value_id($product_colour,$colour_key_id);
                                                    $value .= $product_colour.' / ';
                                                    $var_val_id .= $colour_id.',';
                                                    $data_product_var_val[$n]['varients_value_id'] = $colour_id;
                                                    $data_product_var_val[$n]['varients_id'] =  $colour_key_id;
                                                    $data_product_var_val[$n]['product_varients_id'] =  $varient_product_id;
                                                    $data_product_var_val[$n]['delete_status'] =  0;
                                                    $n = $n+1;
                                                }
                                                if($product_size != ''){
                                                    $size_key_id = $this->get_varient_key_id('Size');
                                                    $size_value_id = $this->get_varient_value_id($product_size,$size_key_id);
                                                    $value .= $product_size.' / ';
                                                    $var_val_id .= $size_value_id.',';
                                                    $data_product_var_val[$n]['varients_value_id'] = $size_value_id;
                                                    $data_product_var_val[$n]['varients_id'] =  $size_key_id;
                                                    $data_product_var_val[$n]['product_varients_id'] =  $varient_product_id;
                                                    $data_product_var_val[$n]['delete_status'] =  0;
                                                }

                                                $var_val_id = rtrim($var_val_id, ",");
                                                $value = rtrim($value, ' / ');
                                             
                                                $product_batch = "BATCH-01";
                                                $data_com = array();
                                                $data_com['product_code'] = $product_code;
                                                $data_com['combinations'] = $value;
                                                $data_com['status'] = 'Y';
                                                $data_com['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
                                                $data_com['branch_id'] = $this->session->userdata("SESS_BRANCH_ID");
                                                $data_com['varient_value_id'] = $var_val_id;
                                                $data_com['product_id'] = $product_id;
                                                $combination_id = $this->general_model->insertData('product_combinations', $data_com);
                                   
                                                $this->db->insert_batch('product_varients_value', $data_product_var_val);
                                              $is_varients = 'N';
                                                $product_name = $product_name.' / '.$value;
                                                if($combination_id){
                                                    $headers_var = array(
                                                        'product_code' => $product_code,
                                                        "product_opening_quantity" => $opening_stock,
                                                        "product_batch" => $product_batch,
                                                        "product_hsn_sac_code" => trim($row['D']),
                                                        "product_sku" => $product_sku,
                                                        "product_serail_no" => trim($row['L']),
                                                        "product_name" => $product_name,
                                                        "product_price"  => $purchase_price,
                                                        "batch_serial" => '',
                                                        "batch_parent_product_id" => $parent_id,
                                                        "product_unit" => $product_unit_id,
                                                        "product_unit_id" => $product_unit_id,
                                                        "product_mrp_price" => trim($row['K']),
                                                        "product_selling_price" => $selling_price,
                                                        "product_category_id" => $product_category_id,
                                                        "product_subcategory_id" => $product_subcategory_id,
                                                        "product_tds_id" => $tcs_product_id,
                                                        "product_tds_value" => trim($row['I']),
                                                        "product_gst_id" => $gst_product_id,
                                                        "product_gst_value" => trim($row['H']),
                                                        "product_details" => trim($row['M']),
                                                        "product_discount_id" => $discount_product_id,
                                                        "product_discount_value" => $this->precise_amount($discount_product,2),
                                                        "product_type" => $product_type,
                                                        "is_assets" => 'N' ,
                                                        "is_varients" => 'N',
                                                        "delete_status" => 0,
                                                        "added_date" => date('Y-m-d'),
                                                        "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                                                        "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                                                        "product_basic_price" => $this->precise_amount($basic_price, 2),
                                                        "margin_discount_value" => $marginal_discount_product,
                                                        "margin_discount_id" => $marginal_discount_product_id,
                                                        "brand_id" => $brand_id,
                                                        "product_barcode" => $product_barcode,
                                                        "warehouse_id" => $warehouse_id,
                                                        "product_combination_id" => $combination_id
                                                    );

                                                    if($expiry_date != '1970-01-01'){
                                                        $headers_var["exp_date"] = $expiry_date;
                                                    }

                                                    $product_id = $this->general_model->insertData($table_name, $headers_var);
                                          
                                                    $update_compina_status = array('status' => 'Y');
                                                    $this->general_model->updateData('product_combinations', $update_compina_status, array('combination_id' => $combination_id));
                                                }
                                            }
                                        }
                                        /*$product_id = $this->db->insert($table_name,$headers);*/
                                       
                                        /*$ecommerce = 1;
                                        if($ecommerce){
                                            $headers['product_id'] = $product_id;
                                            $headers['product_image'] = '';
                                            $this->producthook->CreateProduct($headers);
                                        }*/
                                    } else {
                                        $error_array[] = $error_log;
                                    }
                                    /* $row['Error'] = $added_error;*/
                                    if(!$is_add && !empty($row)){
                                        array_unshift($row,$error);
                                        array_push($errors_email, array_values($row));
                                    }
                                } 
                                $log_data = array(
                                    'user_id'           => $this->session->userdata('user_id'),
                                    'table_id'          => 0,
                                    'table_name'        => 'products',
                                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                                    'message'           => 'Bulk Product Inserted');
                                $this->general_model->insertData('log', $log_data);
                                if(!empty($error_array)){
                                    $errorMsg = implode('<br>', $error_array);
                                    $this->session->set_flashdata('bulk_error_product',$errorMsg);
                                    /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array));   */ 
                                }else{
                                    $successMsg = 'Product imported successfully.';
                                    $this->session->set_flashdata('bulk_success_product',$successMsg);
                                    /*$this->session->set_userdata('bulk_success', $successMsg); */ 
                                }
                        }else{
                            $this->session->set_flashdata('bulk_error_product',"File formate not correct!");
                            /*$this->session->set_userdata('bulk_error', "File formate not correct!");*/
                        }      
                    }else{
                        $this->session->set_flashdata('bulk_error_product',"Empty file!");
                        /*$this->session->set_userdata('bulk_error', 'Empty file!');*/
                    }      
                }catch (Exception $e) {
                    $this->session->set_flashdata('bulk_error_product',"Error on file upload, please try again.");
                    /*$this->session->set_userdata('bulk_error', 'Error on file upload, please try again.');*/
                }
            }
        }
        /*print_r($errors_email);
        exit;*/
        if(!empty($errors_email)){
            $to = $this->session->userdata('SESS_IDENTITY');
            $to = $this->session->userdata('SESS_EMAIL');
            /*$to = 'harish.sr@aavana.in';*/
            array_unshift($header_row, 'Errors');
            array_unshift($errors_email,$header_row);
            $resp = $this->send_csv_mail($errors_email,'Product Bulk Import Error Logs, <br><br> PFA,',"Product bulk upload error logs in <{$import_xls_file}>",$to);
            /*$this->session->set_userdata('bulk_error', 'Error email has been sent to registered email ID');*/
            $this->session->set_flashdata('bulk_error_product',"Error email has been sent to registered email ID.");
            /*$this->session->set_userdata('bulk_error', implode('<br>', $error_array)."<br>Error email has been sent to registered email ID"); */
            //print_r($errors_email);
        }
        redirect("product", 'refresh');
    }


   public function get_varient_key_id($key_name){
          $data = $this->general_model->getRecords('*', 'varients', array(
                                          'branch_id'    => $this->session->userdata('SESS_BRANCH_ID'),
                                          'varient_key'   => $key_name,
                                          'delete_status' => 0));

            if(empty($data)){
               $varient_data = array(
                              "varient_key" => trim($key_name),
                              "added_date" => date('Y-m-d'),
                              "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                              "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));

               $key_id = $this->general_model->insertData('varients', $varient_data);
            }else{
              $key_id = $data[0]->varients_id;
            }
        return $key_id;
   }

   public function get_varient_value_id($value_name,$key_id){
       $data = $this->general_model->getRecords('*', 'varients_value', array(
                                          'branch_id'    => $this->session->userdata('SESS_BRANCH_ID'),
                                          'varients_value'   => $value_name,
                                          'varients_id' => $key_id,
                                          'delete_status' => 0));
            if(empty($data)){
                  $varient_data = array(
                             'varients_id' => $key_id,
                             'varients_value' => trim($value_name),
                             'added_date' => date('Y-m-d'),
                             'added_user_id' => $this->session->userdata('SESS_USER_ID'),
                             'branch_id' => $this->session->userdata('SESS_BRANCH_ID'));

                  $value_id = $this->general_model->insertData('varients_value', $varient_data);
            }else{
               $value_id = $data[0]->varients_value_id;
            }

         return $value_id;
   }
   
    public function get_bulkdtee(){
        $det = $this->get_varient_key_id('Colour');
        echo $det;
    }
   
    public function add_product_master(){
        
        $data  = $this->general_model->getRecords('count(*) as num_product', 'tbl_product_master', array(
                'master_product_name' => trim($this->input->post('master_product_name')),
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        if($data[0]->num_product == 0) {
            $product_data  = array(
                    "master_product_name" => trim($this->input->post('master_product_name')),
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') );
            $id = $this->general_model->insertData('tbl_product_master', $product_data);
            $log_data  = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'tbl_product_master',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Product name Inserted' );
            $this->general_model->insertData('log', $log_data);
            $data['data']               = $this->general_model->getRecords('*', 'tbl_product_master', array(
                    'status' => 1));
            $data['id'] = $id;
            echo json_encode($data);
        }else{
            $result = 'duplicate' ; 
            echo json_encode($result);
        }
    }


    public function add_category($category){
            $category_code = $this->product_model->getMaxCategoryId();
            $category_data = array(
                    "category_code" => $category_code,
                    "category_name" => $category,
                    "category_type" => 'product',
                    "added_date"    => date('Y-m-d'),
                    "added_user_id" => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') );
            $id   = $this->general_model->insertData('category', $category_data);
            
            $log_data                        = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'category',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Category Inserted(Subcategory)' );
            $this->general_model->insertData('log', $log_data);            
            return $id;
            
    }

    public function add_tax($tax_name,$tax_value){
        $tax_data = array(
                "tax_name"        => $tax_name,
                "tax_value"       => $tax_value,
                "tax_description" => '',
                "section_id" => 0,
                "added_date"      => date('Y-m-d'),
                "added_user_id"   => $this->session->userdata("SESS_USER_ID"),
                "branch_id"       => $this->session->userdata("SESS_BRANCH_ID") );
        if($id = $this->general_model->insertData("tax", $tax_data)){
            $log_data = array(
                'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                'table_id'          => $id,
                'table_name'        => 'tax',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                'message'           => 'Tax Inserted' );
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
        return $id;
    }

    public function add_discount($percentage) {
        $discount_data = array(
            "discount_name" => 'Discount',
            "discount_value" => $percentage,
            "added_date" => date('Y-m-d'),
            "added_user_id" => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
            "updated_date" => "",
            "updated_user_id" => "");
        if($id = $this->general_model->insertData("discount", $discount_data)){
            $log_data = array(
                'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                'table_id'          => $id,
                'table_name'        => 'discount',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                'message'           => 'Discount Inserted' );
                $log_table = $this->config->item('log_table');
                $this->general_model->insertData($log_table , $log_data);
        }
       return $id;
    }

    public function get_bulk_check_product_leathercraft($product_name,$product_code,$product_id = 0){
        $product_name = strtoupper($product_name);
        $data         = $this->general_model->getRecords('*,count(*) num ', 'products', array(
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0,
            'product_code'  => $product_code,
            'product_name'  => $product_name,
            'product_id!='  => $product_id),"","product_name");
        return $data;
    }

}
