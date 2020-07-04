<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Inletproduct extends CI_Controller {
	public $branch_id = 0;
    public $user_id = 0;
    public $categories =''; 
    public $sub_category ='';
    public $gst = '';
    public $tcs = '';
    public $discount = '';
    public $brand = '';
    public $exist_varients = '';
    public $firm_id = '';
    public $modules = array();
	function __construct(){
		parent::__construct();
		$this->ci = &get_instance();
        $this->load->library(array(
            'common_api',
            'ion_auth'
        ));

        $this->load->model(['general_model']);
	}

    function index(){
        echo "<pre>";
        $count_qry = $this->db->query('SELECT inlet_id,branch_id FROM `inlet_transaction_cron` GROUP BY inlet_id');
        $history_resp  = $count_qry->result();
        if(!empty($history_resp)){
            $product_module_id = $this->config->item('product_module');
            $data['module_id'] = $product_module_id;
            $modules           = $this->modules;
            $privilege         = "add_privilege";
            $data['privilege'] = $privilege;
            
            /* presents all the needed */
            foreach ($history_resp as $key => $value) {
                $this->categories = $this->sub_category = $this->gst = '';
                $inlet_id = $value->inlet_id;
                $branch_id = $value->branch_id;

                $b_data = $this->common_api->get_default_country_state($branch_id);
                $this->getModulesDetails($value->branch_id);
                
                $section_modules = $this->common_api->get_section_modules($product_module_id , $this->modules , $privilege);
                $data = array_merge($data, $section_modules);
                
                $inlet_qry = $this->db->query("SELECT *,c.branch_id,it.updated_user_id as user_id,ct.category_name,sct.sub_category_name,b.brand_name FROM `inlet_transaction_cron` c JOIN products p ON p.product_id=c.item_id JOIN inlet_item i ON c.inlet_item_id=i.inlet_item_id JOIN inlet it ON it.inlet_id=c.inlet_id LEFT JOIN category ct ON ct.category_id=p.product_category_id LEFT JOIN sub_category sct ON sct.sub_category_id =p.product_category_id LEFT JOIN brand b ON b.brand_id=p.brand_id WHERE c.inlet_id={$inlet_id}");
                $history_resp  = $inlet_qry->result_array();
                
                $category = $this->getCategories($branch_id);
                $this->categories = array_column($category, 'category_id', 'category_name');
                
                $subcategory = $this->GetSubCategory($branch_id);
                $this->sub_category = array_column($subcategory, 'sub_category_id', 'subcategory_name');
                $gst = $this->Get_tax('GST',$branch_id);
                $this->gst = array_column($gst, 'tax_id', 'tax_value');
                $tcs = $this->Get_tax('TCS',$branch_id);
                $this->tcs = array_column($tcs, 'tax_id', 'tax_value');
                $discount = $this->Get_discount($branch_id);
                $this->discount = array_column($discount, 'discount_id', 'discount_value');
                $gst = $this->Get_tax('GST',$branch_id);
                $this->gst = array_column($gst, 'tax_id', 'tax_value');
                $brand = $this->GetBrands($branch_id);
                $this->brand = array_column($brand, 'brand_id', 'brand_name');
                $this->GetVariants($branch_id);
                $access_settings          = $data['access_settings'];
                $primary_id               = "product_id";
                $table_name               = "products";
                $date_field_name          = "added_date";
                $current_date             = date('Y-m-d');
                $lastElement = end($history_resp);
                $transferred_status = 1;

                foreach ($history_resp as $k => $inlet) {
                    $brand_id = $product_subcategory_id = $gst_product_id = $product_tds_id = $product_discount_id = 0;
                    $name_category = $inlet['category_name'];
                    $sub_category_name = $inlet['sub_category_name'];
                    
                    if($this->firm_id == 35){
                        $is_product_exist = $this->get_check_product_leathercraft($inlet);
                    }else{
                        $is_product_exist = $this->checkProductName($inlet);
                    }
                    
                    $product_gst = $inlet['product_gst_value'];
                    $product_tds_value = $inlet['product_tds_value'];
                    $product_discount_value = $inlet['product_discount_value'];
                    $brand_name = $inlet['brand_name'];
                    if(isset($this->categories[strtolower($name_category)])){
                        $product_category_id = $this->categories[strtolower($name_category)];
                    }else{
                        $product_category_id = $this->createCategory($name_category,$inlet['user_id'],$inlet['branch_id']);
                    }
                    
                    if($sub_category_name != ''){
                        if(isset($this->sub_category[strtolower($sub_category_name)])){
                            $product_subcategory_id = $this->sub_category[strtolower($sub_category_name)];
                        }else{
                            $product_subcategory_id = $this->createSubCategory($product_category_id,$sub_category_name,$inlet['user_id'],$inlet['branch_id']);
                        }
                    }
                    if($product_gst > 0){
                        if(isset($this->gst[$product_gst])){
                            $gst_product_id = $this->gst[$product_gst];
                        }else{
                            $gst_product_id = $this->add_tax('GST',$product_gst,$inlet['user_id'],$inlet['branch_id']);
                        }
                    }
                    if($product_tds_value > 0){
                        if(isset($this->tcs[$product_tds_value])){
                            $product_tds_id = $this->tcs[$product_tds_value];
                        }else{
                            $product_tds_id = $this->add_tax('TCS',$product_tds_value,$inlet['user_id'],$inlet['branch_id']);
                        }
                    }
                    if($product_discount_value > 0){
                        if(isset($this->discount[$product_discount_value])){
                            $product_discount_id = $this->discount[$product_discount_value];
                        }else{
                            $product_discount_id = $this->add_discount($product_discount_value,$inlet['user_id'],$inlet['branch_id']);
                        }
                    }

                    if($brand_name != ''){
                        if(isset($this->brand[strtolower($brand_name)])){
                            $brand_id = $this->brand[strtolower($brand_name)];
                        }else{
                            $brand_id = $this->add_brand($inlet['brand_id'],$branch_id);
                        }
                    }

                    $parent_id = 0;
                    $combination_id = NULL;
                    $product_code = $inlet['product_code'];
                    
                    if ($access_settings[0]->invoice_creation == "automatic" && !$is_product_exist){
                        $product_code  = $this->common_api->generate_invoice_number_api($this,$access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                    }
                    
                    if(!$is_product_exist){
                        $batch_serial =1;
                        $product_batch = 'BATCH-01';
                        $batch_parent_product_id = 0;
                    }else{
                        $batch_parent_product_id = $is_product_exist[0]->product_id;
                        if($access_settings[0]->invoice_creation == "automatic" )$product_code = $is_product_exist[0]->product_code;
                        $batch_serial = $is_product_exist[0]->batch_serial;
                        $batch_serial = $batch_serial + 1;
                        $product_batch = 'BATCH-0'.$batch_serial;
                        /*echo "<pre>";
                        print_r($is_product_exist[0]);
                        exit;*/
                    }
                    /*echo $product_code;
                    echo $product_batch;
                    exit;*/
                    $product_barcode = $product_code;
                    if($inlet['product_code'] != $inlet['product_barcode']){
                        $product_barcode = $inlet['product_barcode'];
                    }
                    $product_sku = $this->get_product_sku_bulk($product_code,$product_category_id,$branch_id);
                    if($inlet['is_varients'] == 'N' && $inlet['product_combination_id'] != NULL){
                        $varient_value_id = $this->db->select('varient_value_id,product_id')->from('product_combinations')->where('combination_id',$inlet['product_combination_id'])->get()->row();
                        $parent_id = $varient_value_id->product_id;
                        $varient_value_id = $varient_value_id->varient_value_id;
                        $varients_ids = $this->db->query("SELECT v.varients_id,vv.varients_value_id,v.varient_key,vv.varients_value FROM varients_value vv JOIN varients v ON vv.varients_id = v.varients_id WHERE varients_value_id IN ($varient_value_id)");
                        $varients_ids = $varients_ids->result();
                        if(!empty($varients_ids)){
                            $var_val_id = $var_values = '';
                            foreach ($varients_ids as $v) {
                                $key_name = $v->varient_key;
                                $value_name = trim($v->varients_value);
                                $value_id = 0;
                                $n = 1;
                                if(@$this->exist_varients[$key_name]){
                                    $key_id = $this->exist_varients[$key_name][0]->varients_id;
                                    foreach ($this->exist_varients[$key_name] as $exist_v) {
                                        if(trim(strtolower($value_name)) == trim(strtolower($exist_v->varients_value))){
                                            $value_id = $exist_v->varients_value_id;
                                            break;
                                        }
                                    }
                                    if(!$value_id){
                                        $varient_data = array(
                                                        'varients_id' => $key_id,
                                                        'varients_value' => trim($value_name),
                                                        'added_date' => date('Y-m-d'),
                                                        'added_user_id' => $inlet['user_id'],
                                                        'branch_id' => $branch_id
                                                    );
                                        $value_id = $this->general_model->insertData('varients_value', $varient_data);
                                        $this->GetVariants($branch_id);
                                    }
                                }else{

                                    $varient_data = array(
                                                  "varient_key" => trim($key_name),
                                                  "added_date" => date('Y-m-d'),
                                                  "added_user_id" => $inlet['user_id'],
                                                  "branch_id" => $branch_id);

                                   $key_id = $this->general_model->insertData('varients', $varient_data);
                                   $varient_data = array(
                                                        'varients_id' => $key_id,
                                                        'varients_value' => trim($value_name),
                                                        'added_date' => date('Y-m-d'),
                                                        'added_user_id' => $inlet['user_id'],
                                                        'branch_id' => $branch_id
                                                    );
                                    $value_id = $this->general_model->insertData('varients_value', $varient_data);
                                    $this->GetVariants($branch_id);
                                }
                                $var_val_id .= $value_id.',';
                                $var_values .= $value_name.' / ';
                                /*$data_product_var_val[$n]['varients_value_id'] = $value_id;
                                $data_product_var_val[$n]['varients_id'] =  $key_id;
                                $data_product_var_val[$n]['product_varients_id'] =  $varient_product_id;
                                $data_product_var_val[$n]['delete_status'] =  0;
                                $n = $n+1;*/
                            }
                        }

                        /* get varint parent product */
                        $parent_name = $this->db->select('product_name')->from('products')->where('product_id',$parent_id)->get()->row()->product_name;
                        if($this->firm_id == 35){
                            $is_parent_product_exist = $this->get_check_product_leathercraft(array('product_name'=>$parent_name,'product_code'=>$product_code,'branch_id'=>$branch_id));
                            
                        }else{
                            $is_parent_product_exist = $this->checkProductName(array('product_name'=>$parent_name,'branch_id'=>$branch_id));
                        }
                        
                        $exist_code = $product_code;
                        if(!$is_parent_product_exist){
                            $headers = array(
                                'product_code' => $product_code,
                                "product_batch" => $product_batch,
                                "product_opening_quantity" => 0,
                                "product_hsn_sac_code" => trim($inlet['product_hsn_sac_code']),
                                "product_sku" => $product_sku,
                                "product_serail_no" => trim($inlet['product_serail_no']),
                                "product_name" => $parent_name,
                                "product_unit" => $inlet['product_unit'],
                                "product_unit_id" => $inlet['product_unit_id'],
                                "product_price"  => $inlet['product_price'],
                                "product_mrp_price" => trim($inlet['product_mrp_price']),
                                "product_selling_price" => $inlet['product_selling_price'],
                                "product_category_id" => $product_category_id,
                                "product_subcategory_id" => $product_subcategory_id,
                                "product_tds_id" => $product_tds_id,
                                "product_tds_value" => trim($product_tds_value),
                                "product_gst_id" => $gst_product_id,
                                "product_gst_value" => trim($product_gst),
                                "product_details" => trim($inlet['product_details']),
                                "product_discount_id" => $product_discount_id,
                                "product_discount_value" => $product_discount_value,
                                "product_type" => $inlet['product_type'],
                                "is_assets" => $inlet['is_assets'],
                                "is_varients" => 'Y',
                                "delete_status" => 0,
                                "added_date" => date('Y-m-d'),
                                "added_user_id" => $inlet['user_id'],
                                "branch_id" => $inlet['branch_id'],
                                "batch_serial" => 1,
                                "batch_parent_product_id" => 0,
                                "product_basic_price" => $inlet['product_basic_price'],
                                "margin_discount_value" => $inlet['margin_discount_value'],
                                "margin_discount_id" => $inlet['margin_discount_id'],
                                "brand_id" => $brand_id,
                                "product_barcode" => $inlet['product_barcode']
                            );
                            $parent_id = $this->general_model->insertData($table_name, $headers);
                            $batch_serial =1;
                            $product_batch = 'BATCH-01';
                            $batch_parent_product_id = 0;
                            if($access_settings[0]->invoice_creation == "automatic" ){
                                $product_code = $product_code.'-01';
                            } 
                        }else{
                            $parent_id = $is_parent_product_exist[0]->product_id;
                            if($access_settings[0]->invoice_creation == "automatic" ){
                                $product_code = $is_parent_product_exist[0]->product_code;
                                $exist_code = $product_code;
                                $keyword = $product_code.'-0';
                                $this->db->select('*');
                                $this->db->from('products');
                                $this->db->where('branch_id', $inlet['branch_id']);
                                $this->db->like('product_code', $keyword);
                                $res = $this->db->get();
                                $count = $res->num_rows();
                                $a = $count +1 ;
                                $product_code = $product_code.'-0'.$a;
                            }
                        }

                        $data_com = array();
                        $data_com['product_code'] = $exist_code;
                        $data_com['combinations'] = $var_values;
                        $data_com['status'] = 'Y';
                        $data_com['product_id'] = $parent_id;
                        $data_com['branch_id'] = $branch_id;
                        $data_com['varient_value_id'] = $var_val_id;
                        
                        $combination_id = $this->general_model->insertData('product_combinations', $data_com);
                        /*$this->db->insert_batch('product_varients_value', $data_product_var_val);*/
                        /**/ 
                    }
                    /*echo $product_batch;
                    exit;*/
                    $headers = array(
                                'product_code' => $product_code,
                                "product_batch" => $product_batch,
                                "product_opening_quantity" => 0,
                                "product_quantity" => $inlet['inlet_item_current_quantity'],
                                "product_hsn_sac_code" => trim($inlet['product_hsn_sac_code']),
                                "product_sku" => $product_sku,
                                "product_serail_no" => trim($inlet['product_serail_no']),
                                "product_name" => $inlet['product_name'],
                                "product_unit" => $inlet['product_unit'],
                                "product_unit_id" => $inlet['product_unit_id'],
                                "product_price"  => $inlet['product_price'],
                                "product_mrp_price" => trim($inlet['product_mrp_price']),
                                "product_selling_price" => $inlet['product_selling_price'],
                                "product_category_id" => $product_category_id,
                                "product_subcategory_id" => $product_subcategory_id,
                                "product_tds_id" => $product_tds_id,
                                "product_tds_value" => trim($product_tds_value),
                                "product_gst_id" => $gst_product_id,
                                "product_gst_value" => trim($product_gst),
                                "product_details" => trim($inlet['product_details']),
                                "product_discount_id" => $product_discount_id,
                                "product_discount_value" => $product_discount_value,
                                "product_type" => $inlet['product_type'],
                                "is_assets" => $inlet['is_assets'],
                                "is_varients" => $inlet['is_varients'],
                                "delete_status" => 0,
                                "added_date" => date('Y-m-d'),
                                "added_user_id" => $inlet['user_id'],
                                "branch_id" => $inlet['branch_id'],
                                "batch_serial" => $batch_serial,
                                "batch_parent_product_id" => $batch_parent_product_id,
                                "parent_id" => $parent_id,
                                "product_basic_price" => $inlet['product_basic_price'],
                                "product_combination_id" => $combination_id,
                                "margin_discount_value" => $inlet['margin_discount_value'],
                                "margin_discount_id" => $inlet['margin_discount_id'],
                                "brand_id" => $brand_id,
                                "product_barcode" => $inlet['product_barcode']
                            );
                    
                    if($product_id = $this->general_model->insertData($table_name, $headers)){
                        
                        $this->db->where('cron_id',$inlet['cron_id']);
                        $this->db->delete('inlet_transaction_cron');

                        $transfered_qty = $inlet['inlet_item_current_quantity'] + $inlet['inlet_item_transferred_quantity'];

                        $this->db->query("UPDATE inlet_item SET inlet_item_transferred_quantity = inlet_item_transferred_quantity + inlet_item_current_quantity,inlet_item_current_quantity=0 WHERE inlet_item_id ={$inlet['inlet_item_id']}");

                        if($batch_parent_product_id != 0){
                            $update = array('batch_serial' => $batch_serial);
                            $this->general_model->updateData('products',$update,array('product_id' => $batch_parent_product_id));
                        }
                    }
                }

                $count_pending = $this->db->query("SELECT inlet_item_id FROM inlet_item WHERE inlet_id = {$inlet_id} AND inlet_item_quantity > inlet_item_transferred_quantity ");
                
                if($count_pending->num_rows() > 0){
                    $transferred_status = 4;
                }else{
                    $transferred_status = 1;
                    $this->db->where('outlet_id',$inlet['outlet_id']);
                    $this->db->set('transferred_status',1);
                    $this->db->update('outlet');
                }
                $this->db->where('inlet_id',$inlet_id);
                $this->db->set('transferred_status',$transferred_status);
                $this->db->update('inlet');
            }
        }
    }

    public function get_product_sku_bulk($product_code , $category_id,$branch_id){
        $data   = $this->general_model->getRecords('category_code', 'category', array(
            'branch_id'     => $branch_id,
            'delete_status' => 0,           
            'category_id ='  => $category_id),"","category_code");
        $item_category_code =  $data[0]->category_code;
        $category_code =  explode("-", $item_category_code);
                $sku_code = $category_code[1]."-".$product_code;
        return $sku_code;
    }

    function checkProductName($product){
        $product_name = preg_replace("/\s+/", "", strtolower($product['product_name']));
        $product_name_qry = $this->db->query("SELECT * FROM products WHERE REPLACE(LOWER(product_name),' ','') = '{$product_name}' AND branch_id='{$product['branch_id']}' and delete_status ='0' AND batch_parent_product_id=0");
        /*$data['products']   = $this->general_model->getRecords('product_id,product_code,product_batch,product_name,product_mrp_price,batch_serial,batch_parent_product_id', 'products', array(
            'batch_parent_product_id' => 0,
            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
            'delete_status' => 0),array('product_id'=>'DESC'),"");*/
        $product_resp = $product_name_qry->result();
        
        if(!empty($product_resp)){
            return $product_resp;
        }else{
            return false;
        }
    }

    public function Get_tax($tax,$branch_id){
        $this->db->select('tax_id,TRUNCATE(tax_value,2) as tax_value');
        $this->db->from('tax');
        $this->db->where('tax_name', $tax);
        $this->db->where('delete_status', '0');
        $this->db->where('branch_id', $branch_id);
        $query = $this->db->get(); 
      
        return $query->result_array();
    }

    public function GetBrands($branch_id){
        $this->db->select('brand_id,LOWER(brand_name) as brand_name');
        $this->db->from('brand');
        $this->db->where('delete_status', '0');
        $this->db->where('branch_id', $branch_id);
        $query = $this->db->get(); 
      
        return $query->result_array();
    }

    public function GetVariants($branch_id){
        $this->db->select('v.varients_id,v.varient_key,vv.varients_value,vv.varients_value_id');
        $this->db->from('varients v');
        $this->db->join('varients_value vv','v.varients_id=vv.varients_id','left');
        $this->db->where('v.delete_status', '0');
        $this->db->where('vv.delete_status', '0');
        $this->db->where('v.branch_id', $branch_id);
        $query = $this->db->get(); 
        $resp = $query->result();
        $this->exist_varients = array();
        if(!empty($resp)){
            foreach ($resp as $key => $value) {
                if(@$this->exist_varients[$value->varient_key]){
                    $this->exist_varients[$value->varient_key][] = $value;
                }else{
                    $this->exist_varients[$value->varient_key] = array();
                    $this->exist_varients[$value->varient_key][] = $value;
                }
            }
        }
    }

    public function add_tax($tax_name,$tax_value,$user_id,$branch_id){
        $tax_data = array(
                "tax_name"        => $tax_name,
                "tax_value"       => $tax_value,
                "tax_description" => '',
                "section_id" => 0,
                "added_date"      => date('Y-m-d'),
                "added_user_id"   => $user_id,
                "branch_id"       => $branch_id
            );
        $id = $this->general_model->insertData("tax", $tax_data);
        if($tax_name == 'GST'){
            $gst = $this->Get_tax('GST',$branch_id);
            $this->gst = array_column($gst, 'tax_id', 'tax_value');
        }else{
            $tcs = $this->Get_tax('TCS',$branch_id);
            $this->tcs = array_column($tcs, 'tax_id', 'tax_value');
        }
        return $id;
    }

    public function add_discount($percentage,$user_id,$branch_id) {
        $discount_data = array(
            "discount_name" => 'Discount',
            "discount_value" => $percentage,
            "added_date" => date('Y-m-d'),
            "added_user_id" => $user_id,
            "branch_id" => $branch_id,
            "updated_date" => "",
            "updated_user_id" => ""
        );
        $id = $this->general_model->insertData("discount", $discount_data);
        $discount = $this->Get_discount($branch_id);
        $this->discount = array_column($discount, 'discount_id', 'discount_value');
        return $id;
    }

    public function Get_discount($branch_id){
        $this->db->select('discount_id,TRUNCATE(discount_value,2) as discount_value');
        $this->db->from('discount');
        $this->db->where('delete_status', '0');
        $this->db->where('branch_id', $branch_id);
        $query = $this->db->get(); 
      
        return $query->result_array();
    }

    function getCategories($branch_id){
        $this->db->select('category_id,LOWER(category_name) as category_name');
        $this->db->from('category');
        $this->db->where('category_type', 'product');
        $this->db->where('delete_status', '0');
        $this->db->where('branch_id', $branch_id);
        $query = $this->db->get(); 
      
        return $query->result_array();
    }

    function createCategory($category_name,$user_id,$branch_id){
        $id = $this->db->select_max('category_id')->get('category')->row()->category_id;
        if ($id == null){
            $category_code = 'CAT-' . sprintf('%06d', intval(1));
        } else {
            $category_code = 'CAT-' . sprintf('%06d', intval($id) + 1);
        }
        
        $category_data = array(
                    "category_code" => $category_code,
                    "category_name" => trim($category_name),
                    "category_type" => trim('product'),
                    "added_date"    => date('Y-m-d'),
                    "added_user_id" => $user_id,
                    "branch_id"     => $branch_id
                );
        $id = $this->general_model->insertData('category', $category_data);
        $category = $this->getCategories($branch_id);
        $this->categories = array_column($category, 'category_id', 'category_name');
        return $id;
    }

    function createSubCategory($category_id,$subcategory_name,$user_id,$branch_id){
        $id = $this->db->select_max('sub_category_id')->get('sub_category')->row()->sub_category_id;
        if ($id == null)
        {
            $subcategory_code = 'SUBCAT-' . sprintf('%06d', intval(1));
        }
        else
        {
            $subcategory_code = 'SUBCAT-' . sprintf('%06d', intval($id) + 1);
        }
        
        $sub_category_data = array(
                "category_id" => $category_id,
                "sub_category_code" => $subcategory_code,
                "sub_category_name" => $subcategory_name,
                "added_date" => date('Y-m-d'),
                "added_user_id" => $user_id,
                "branch_id" => $branch_id
            );
        $id = $this->general_model->insertData('sub_category', $sub_category_data);
        $subcategory = $this->GetSubCategory($branch_id);
        $this->sub_category = array_column($subcategory, 'sub_category_id', 'subcategory_name');
        return $id;
    }

    public function GetSubCategory($branch_id){
        $this->db->select('su.sub_category_id,su.category_id as category_id_sub,LOWER(su.sub_category_name) as subcategory_name');
        $this->db->from('sub_category su');
        $this->db->join('category ca', 'su.category_id = ca.category_id');
        $this->db->where('ca.category_type', 'product');
        $this->db->where('su.delete_status', '0');
        $this->db->where('su.branch_id', $branch_id);
        $query = $this->db->get();
      
        return $query->result_array();
    }

    public function add_brand($brand_id,$branch_id){

        $this->db->query("INSERT INTO brand (brand_name, brand_invoice_first_prefix,brand_reference_first_prefix,brand_invoice_last_prefix,invoice_seperation,invoice_type,invoice_creation,invoice_readonly,branch_id,added_date,added_user_id)
        select brand_name, brand_invoice_first_prefix,brand_reference_first_prefix,brand_invoice_last_prefix,invoice_seperation,invoice_type,invoice_creation,invoice_readonly,{$branch_id},NOW(),added_user_id
        from brand
        where brand_id = $brand_id ");
        $insert_id = $this->db->insert_id();
        $brand = $this->GetBrands($branch_id);
        $this->brand = array_column($brand, 'brand_id', 'brand_name');
        return  $insert_id;
    }

    function getModulesDetails($branch_id){
        $branch_data = $this->ci->db->select('users.id as user_id,branch.branch_id,branch.firm_id,branch.financial_year_id,concat(YEAR(tbl_financial_year.from_date),"-",YEAR(tbl_financial_year.to_date)) as financial_year_title,branch.branch_default_currency,currency.currency_symbol,currency.currency_code,currency.currency_text')->from('users')->join('branch', 'users.branch_id = branch.branch_id')->join('currency', 'currency.currency_id = branch.branch_default_currency')->join('tbl_financial_year', 'tbl_financial_year.year_id = branch.financial_year_id')->where('branch.branch_id', $branch_id)->where('username !=', 'superadmin')->get()->row();
        
        if(!empty($branch_data)){
            $this->user_id = $branch_data->user_id;
            $this->firm_id = $branch_data->firm_id;
            $this->ci->session->set_userdata('SESS_BRANCH_ID',trim($branch_data->branch_id));
            $this->ci->session->set_userdata('SESS_USER_ID',trim($branch_data->user_id));
            $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_TITLE',trim($branch_data->financial_year_title));
            $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_ID',trim($branch_data->financial_year_id));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_TEXT',trim($branch_data->currency_text));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_CODE',trim($branch_data->currency_code));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY',trim($branch_data->branch_default_currency));
            $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_SYMBOL',trim($branch_data->currency_symbol));
            $this->modules = $this->common_api->get_modules($branch_data);

            return true;
        }else{
            return false;
        }
    }

    public function get_check_product_leathercraft($data){
        $product_name = strtoupper($data['product_name']);
        $data         = $this->general_model->getRecords('*,count(*) num ', 'products', array(
            'branch_id'     => $data['branch_id'],
            'delete_status' => 0,
            'product_code'  => $data['product_code'],
            'product_name'  => $data['product_name']
        ),"","product_name");
        return $data;
    }
}
?>