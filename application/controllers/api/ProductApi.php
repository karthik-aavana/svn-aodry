<?php
ini_set( 'display_errors', 0 );
require APPPATH . 'libraries/REST_Controller.php';
class ProductApi extends REST_Controller {
	/**
     * Get All Data from this method.
     *
     * @return Response
    */
    public $branch_id = 0;
    public $user_id = 0;
    public $SESS_FINANCIAL_YEAR_TITLE = '';
    public $SESS_FINANCIAL_YEAR_ID = '';
    public $SESS_DEFAULT_CURRENCY_TEXT = '';
    public $SESS_DEFAULT_CURRENCY_CODE = '';
    public $SESS_DEFAULT_CURRENCY = '';
    public $SESS_DEFAULT_CURRENCY_SYMBOL = '';
    public $modules = array();
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array(
            'common_api',
            'common',
            'session',
            'ion_auth',
            'form_validation'));

        $this->load->model([
            'general_model' ,
            'product_model' ,
            'service_model' ,
            'Voucher_model' ,
            'ledger_model' ]);

        $this->ci = &get_instance();
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
	public function index_get($id = 0)
	{
        if(!empty($id)){
            $data = $this->db->get_where("sales", ['sales_id' => $id])->row_array();
        }else{
            $data = $this->db->get("sales")->result();
        }
     
        $this->response($data, REST_Controller::HTTP_OK);
	}
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_post(){

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        $post_req = json_decode(file_get_contents("php://input"),true);
        //echo "<pre>";print_r($post_req);exit();
        $resp = array();
        try {
            if(@$post_req['Method']){
                $method = $post_req['Method'];
                
                if($method == 'CreateNewProduct' || $method == 'UpdateProduct'){
                    if(@$post_req['branch']){
                        $branch = $post_req['branch'];
                        
                        if(@$branch['User'] && @$branch['Password'] && @$branch['Code'] && @$branch['LoginCode']){
                            $branch_code = $branch['Code'];
                            if ($this->ion_auth->login($branch['Code'], $branch['User'], base64_decode($branch['Password']),'0')) {

                                $query = $this->db->select('email,first_name,last_name, id,branch_id,branch_code, password, active, last_login')->where([
                                    'email' => $branch['User'],
                                    'branch_code' => $branch['Code'] ])->where('username !=', 'superadmin')->limit(1)->order_by('id', 'desc')->get('users');
                                if($query->num_rows() > 0){

                                    $branch_detail = $query->result();
                                    $this->branch_id = $branch_detail[0]->branch_id;

                                    $b_id = $this->branch_id;
                                    $qry = $this->db->select('branch_id,token')->where([
                                        'branch_id' => $b_id,
                                        'token' => $branch['LoginCode']
                                        ])->get('ecom_branch_setting');
                                    if($qry->num_rows() > 0){
                                        $data = $this->common_api->get_default_country_state($this->branch_id);
                                        
                                        $branch_data = $this->db->select('users.id as user_id,branch.branch_id,branch.financial_year_id,concat(YEAR(tbl_financial_year.from_date),"-",YEAR(tbl_financial_year.to_date)) as financial_year_title,branch.branch_default_currency,currency.currency_symbol,currency.currency_code,currency.currency_text')->from('users')->join('branch', 'users.branch_id = branch.branch_id')->join('currency', 'currency.currency_id = branch.branch_default_currency')->join('tbl_financial_year', 'tbl_financial_year.year_id = branch.financial_year_id')->where('users.id', $branch_detail[0]->id)->where('username !=', 'superadmin')->get()->row();
                                            
                                        $this->user_id = $branch_data->user_id;
                                        $this->ci->session->set_userdata('SESS_BRANCH_ID',trim($branch_detail[0]->branch_id));
                                        $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_TITLE',trim($branch_data->financial_year_title));
                                        $this->ci->session->set_userdata('SESS_FINANCIAL_YEAR_ID',trim($branch_data->financial_year_id));
                                        $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_TEXT',trim($branch_data->currency_text));
                                        $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_CODE',trim($branch_data->currency_code));
                                        $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY',trim($branch_data->branch_default_currency));
                                        $this->ci->session->set_userdata('SESS_DEFAULT_CURRENCY_SYMBOL',trim($branch_data->currency_symbol));

                                        $this->SESS_FINANCIAL_YEAR_TITLE = trim($branch_data->financial_year_title);
                                        $this->SESS_FINANCIAL_YEAR_ID = $branch_data->financial_year_id;
                                        $this->SESS_DEFAULT_CURRENCY_TEXT = $branch_data->currency_text;
                                        $this->SESS_DEFAULT_CURRENCY_CODE = $branch_data->currency_code;
                                        $this->SESS_DEFAULT_CURRENCY = $branch_data->branch_default_currency;
                                        $this->SESS_DEFAULT_CURRENCY_SYMBOL = $branch_data->currency_symbol;
                                        $this->modules = $this->common_api->get_modules($branch_data);
                                        if(!empty($this->modules['modules'])){
                                            $product_module_id = $this->config->item('product_module');
                                            $data['module_id'] = $product_module_id;
                                            $modules           = $this->modules;
                                            $privilege         = "add_privilege";
                                            $data['privilege'] = "add_privilege";
                                            $section_modules   = $this->common_api->get_section_modules($product_module_id, $modules, $privilege);
                                            $access_settings          = $section_modules['access_settings'];
                                            $primary_id               = "product_id";
                                            $table_name               = "products";
                                            $date_field_name          = "added_date";
                                            $current_date             = date('Y-m-d');

                                            /* presents all the needed */
                                            $data = array_merge($data, $section_modules); 
                                            $branch_id = $this->branch_id;
                                            $product_post = $post_req['data'];
                                            $product_name = $product_post['name'];
                                            $category_data = $this->common->category_field('product');
                                            $product_category = $this->general_model->getRecords($category_data['string'], $category_data['table'], $category_data['where']);
                                            $categoryName = 'General';
                                                $subCategories = '';

                                            if(!empty($product_post['categories'])){
                                                $categoryName = $product_post['categories'][0];
                                                if(!empty($product_post['sub_categories'])){
                                                    $subCategories = $product_post['sub_categories'][0];
                                                }
                                            }
                                            
                                            $category_resp = $this->findCategory($product_category,$categoryName,$subCategories);
                                            $unit_id = 0;
                                            if($product_post['product_unit'] != ''){
                                                $unit_id = $this->getProductUnit($product_post['product_unit']);
                                            }

                                            if($method == 'CreateNewProduct'){
                                                $product_code = $this->common_api->generate_invoice_number_api($this,$access_settings, $primary_id, $table_name, $date_field_name, $current_date);
                                               
                                                /*$product_category = $this->common_api->product_category_call();*/
                                                
                                                $numOfPros = $this->general_model->getRecords('*,count(*) num ', 'products', array(
                                                        'branch_id'     => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                                        'delete_status' => 0,
                                                        'LOWER(product_name)'  => strtolower(trim($product_name))),"","product_name");
                                                
                                                $product_batch = "BATCH-0";
                                                if(!empty($numOfPros)){
                                                    $product_batch = "BATCH-0".$numOfPros[0]->num;
                                                }
                                                
                                                $image_name = $this->download_remote_file_with_curl($product_post['image']);

                                                $is_varients = 'N';
                                                $ar = array();
                                                if($product_post['is_variant'] == 1 && !empty($product_post['variants']) && !empty($product_post['attributes'])){
                                                    $is_varients = 'Y';
                                                    $exist_keys = $this->general_model->getJoinRecords('k.varients_id,k.varient_key,k.type,v.varients_value_id,v.varients_value', 'varients k', array('k.delete_status' => 0,'v.delete_status' => 0,'k.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID')),array('varients_value v' => "k.varients_id=v.varients_id#left"));
                                                    
                                                    if(!empty($exist_keys)){
                                                        $rearrange_key = array();
                                                        foreach ($exist_keys as $key => $value) {
                                                            $k = strtolower(trim(str_ireplace(' ','_', trim($value->varient_key))));
                                                            $kv = strtolower(trim(str_ireplace(' ','_', trim($value->varients_value))));
                                                            $rearrange_key[$k]['key'] = $value->varient_key;
                                                            $rearrange_key[$k]['id'] = $value->varients_id;
                                                            $rearrange_key[$k]['values'][$kv] = $value->varients_value_id;
                                                        }
                                                    }
                                                    $mainKey = $varArr = $mainKeyValues = array();
                                                    foreach ($product_post['attributes'] as $key => $key_val) {
                                                        $check_key = strtolower(trim(str_ireplace(' ','_', trim($key))));
                                                        if(array_key_exists($check_key, $rearrange_key)){
                                                            array_push($mainKey, $rearrange_key[$check_key]['id']);
                                                            $existValues = $rearrange_key[$check_key]['values'];
                                                            foreach ($key_val as $value) {
                                                                $newVal = strtolower(trim(str_ireplace(' ','_', trim($value))));
                                                                if(array_key_exists($newVal, $existValues)){
                                                                    $valueId = $existValues[$newVal];
                                                                }else{
                                                                    $varient_data = array(
                                                                                'varients_id' => $rearrange_key[$check_key]['id'],
                                                                                'varients_value' => ucfirst(strtolower(trim($value))),
                                                                                'added_date' => date('Y-m-d'),
                                                                                'added_user_id' => $this->ci->session->userdata('SESS_USER_ID'),
                                                                                'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));

                                                                    $valueId = $this->general_model->insertData('varients_value', $varient_data);
                                                                }
                                                                array_push($mainKeyValues, $valueId);
                                                                $ar[$rearrange_key[$check_key]['id']][] = $value;
                                                                $varArr[$rearrange_key[$check_key]['id']][$valueId] = $value;
                                                            }
                                                        }else{
                                                            $varient_data = array(
                                                                "varient_key" => ucfirst(strtolower(trim($key))),
                                                                "added_date" => date('Y-m-d'),
                                                                "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                                                                "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'));

                                                            $keyId = $this->general_model->insertData('varients', $varient_data);
                                                            array_push($mainKey, $keyId);
                                                            foreach ($key_val as $value) {
                                                                
                                                                $varient_data = array(
                                                                            'varients_id' => $keyId,
                                                                            'varients_value' => ucfirst(strtolower(trim($value))),
                                                                            'added_date' => date('Y-m-d'),
                                                                            'added_user_id' => $this->ci->session->userdata('SESS_USER_ID'),
                                                                            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));

                                                                $valueId = $this->general_model->insertData('varients_value', $varient_data);
                                                                array_push($mainKeyValues, $valueId);
                                                                $ar[$keyId][] = $value;
                                                                $varArr[$keyId][$valueId] = $value;
                                                            }
                                                        }
                                                    }
                                                    
                                                    $counts = array_map("count", $ar);
                                                    $total = array_product($counts);
                                                    $available_comb = [];

                                                    $combinations = [];
                                                    $curCombs = $total;
                                                    foreach ($ar as $field => $vals) {
                                                        $curCombs = $curCombs / $counts[$field];
                                                        $combinations[$field] = $curCombs;
                                                    }

                                                    for ($i = 0; $i < $total; $i++) {
                                                        foreach ($ar as $field => $vals) {
                                                            $available_comb[$i][$field] = $vals[($i / $combinations[$field]) % $counts[$field]];
                                                        }
                                                    }
                                                    /*$i = 1;
                                                    foreach ($res as $key => $value) {
                                                        $data_com[$i]['product_code'] = $product_code;
                                                        $data_com[$i]['combinations'] = implode(" / ",$value);
                                                        $data_com[$i]['status'] = 'N';
                                                        $data_com[$i]['branch_id'] = $this->ci->session->userdata("SESS_BRANCH_ID");
                                                        $i++;    
                                                    }*/
                                                    
                                                }
                                                
                                                $product_data = array(
                                                    "product_code"           => $product_code,
                                                    "product_name"           => $product_name,
                                                    "product_hsn_sac_code"   => '',
                                                    "product_category_id"    => $category_resp['category_id'],
                                                    "product_subcategory_id" => $category_resp['sub_category_id'],
                                                    "product_quantity"       => 0,
                                                    "product_unit"           => $unit_id,
                                                    "product_price"          => 0,
                                                    "product_tds_id"         => 0,
                                                    "product_tds_value"      => 0,
                                                    "product_gst_id"         => 0,
                                                    "product_gst_value"      => 0,
                                                    "product_details"        => trim($product_post['description']),
                                                    "is_assets"              => 'N',
                                                    "is_varients"            => $is_varients,
                                                    "product_unit_id"        => $unit_id,
                                                    "product_type"          => 'finishedgoods',
                                                    "product_mrp_price"     => $product_post['regular_price'],
                                                    "product_selling_price" => $product_post['sale_price'],
                                                    "product_sku"           => $product_post['sku'],
                                                    "product_serail_no"     => '',
                                                    "product_image"         => $image_name,
                                                    "added_date"             => date('Y-m-d'),
                                                    "product_batch"         => $product_batch,
                                                    "added_user_id"          => $this->ci->session->userdata('SESS_USER_ID'),
                                                    "ecommerce"             => 1,
                                                    "branch_id"              => $this->ci->session->userdata('SESS_BRANCH_ID'));
                                                /**/

                                                $look_up_ary = array();
                                                if($product_id = $this->general_model->insertData('products', $product_data)){
                                                    $look_up_ary[] =  array(
                                                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                                                    'aodry_product_id' => $product_id,
                                                                    'aodry_variant_id' => 0,
                                                                    'woo_product_id' => $product_post['product_id'],
                                                                    'woo_variant_id' => 0,
                                                                    'pro_title'  => $product_name,
                                                                    'pro_selling_price' => (@$product_post['sale_price'] ? $product_post['sale_price'] : 0),
                                                                    'pro_stock' => 0,
                                                                    'pro_batch' => $product_batch,
                                                                    'added_date' => date('Y-m-d'),
                                                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                                                );
                                                    if($product_post['is_variant'] == 1 && !empty($product_post['variants'])){
                                                        $a = 1;
                                                        $insert_product = $comm = array();
                                                        $variants = $product_post['variants'];
                                                        foreach ($variants as $key => $value) {
                                                            $attributes = $value['attributes'];
                                                           
                                                            $combinations = '';
                                                                $i = 0;
                                                            foreach ($attributes as $key => $attr) {
                                                                $combinations .= $attr[0].' / ';
                                                                    $i++;
                                                            }
                                                            
                                                            foreach ($available_comb as $key => $v) {
                                                                $array_com = '';
                                                                $i = 0;
                                                                foreach ($v as $k1 => $v1) {
                                                                    $array_com .= $v1[$i].' / ';
                                                                    $i++;
                                                                }
                                                                 $is_diff = array_diff($v, $attributes);
                                                                    if(empty($is_diff)){
                                                                        $comm[] = array(
                                                                            'product_code' => $product_code,
                                                                            'combinations' => rtrim($combinations, ' / '),
                                                                            'status' => 'Y',
                                                                            'product_id' => $product_id,
                                                                            'branch_id' => $this->ci->session->userdata("SESS_BRANCH_ID")
                                                                        );

                                                                    }
                                                            }
                                                            $combination_name =  rtrim($combinations, ' / ');
                                                            $product_image = $this->download_remote_file_with_curl($value['image']);
                                                            /*if($value['image'] != ''){
                                                                $img_url =  $value['image'];
                                                                $path_parts = pathinfo($img_url);

                                                                date_default_timezone_set('Asia/Kolkata');
                                                                $date       = date_create();
                                                                $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
                                                                if (!is_dir('assets/product_image/' . $this->ci->session->userdata('SESS_BRANCH_ID'))){
                                                                    mkdir('./assets/product_image/' . $this->ci->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
                                                                } 
                                                                $url = "assets/product_image/" . $this->ci->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
                                                                if (in_array($path_parts['extension'], array(
                                                                                "jpg",
                                                                                "jpeg",
                                                                                "JPG",
                                                                                "JPEG",
                                                                                "png","PNG" ))){
                                                                    
                                                                    $this->download_remote_file_with_curl($img_url,$url);
                                                                    $product_image = $image_path;
                                                                }
                                                            }*/
                                                            $variants_name= $product_name.' / '.$combination_name;
                                                            $insert_product = array(
                                                                                'product_code' => $product_code.'-0'.$a,
                                                                                'parent_id'    => $product_id,
                                                                                'product_name' => $variants_name,
                                                                                'product_hsn_sac_code' =>'',
                                                                                'product_category_id' => $category_resp['category_id'],
                                                                                'product_subcategory_id' => $category_resp['sub_category_id'],
                                                                                'product_quantity' => 0,
                                                                                'product_unit' => $unit_id,
                                                                                "product_price"          => 0,
                                                                                "product_tds_id"         => 0,
                                                                                "product_tds_value"      => 0,
                                                                                "product_gst_id"         => 0,
                                                                                "product_gst_value"      => 0,
                                                                                'product_details' => $value['variation_description'],
                                                                                'is_assets' => 'N',
                                                                                'is_varients' => 'N',
                                                                                'product_unit_id' => $unit_id,
                                                                                'product_type' => 'finishedgoods',
                                                                                'product_mrp_price' => $value['display_regular_price'],
                                                                                'product_selling_price' => $value['display_price'],  
                                                                                'product_sku' => $value['sku'],
                                                                                'product_image' => $product_image,
                                                                                "product_batch" => $product_batch,
                                                                                'product_serail_no' =>'',
                                                                                'added_date' => date('Y-m-d'),
                                                                                'added_user_id' => $this->ci->session->userdata('SESS_USER_ID'),
                                                                                "ecommerce"             => 1,
                                                                                "branch_id"             => $this->ci->session->userdata('SESS_BRANCH_ID')
                                                                            );

                                                            $variant_id = $this->general_model->insertData('products', $insert_product);
                                                            $look_up_ary[] =  array(
                                                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                                                    'aodry_product_id' => $product_id,
                                                                    'aodry_variant_id' => $variant_id,
                                                                    'woo_product_id' => $product_post['product_id'],
                                                                    'woo_variant_id' => $value['variation_id'],
                                                                    'pro_title'  => $variants_name,
                                                                    'pro_selling_price' => $value['display_price'],
                                                                    'pro_stock' => 0,
                                                                    'pro_batch' => $product_batch,
                                                                    'added_date' => date('Y-m-d'),
                                                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                                                );
                                                            $a++;
                                                        }
                                                       
                                                        $variantPro = array();
                                                        if(!empty($varArr)){
                                                            foreach ($varArr as $key => $value) {
                                                                foreach ($value as $k => $v) {
                                                                    $variantPro[] = array(
                                                                                    'varients_value_id' => $k,
                                                                                    'varients_id'       => $key,
                                                                                    'product_varients_id'=> $product_id,
                                                                                    'delete_status'     => 0
                                                                                );
                                                                }
                                                            }
                                                        }
                                                        
                                                        /*$this->db->insert_batch('products', $insert_product);*/
                                                        $this->db->insert_batch('product_combinations', $comm);
                                                        $this->db->insert_batch('product_varients_value', $variantPro);
                                                    }
                                                }

                                                $this->db->insert_batch('ecom_product_sync', $look_up_ary);
                                                /*$lookup_id = $this->general_model->insertData('ecom_product_sync', $look_up_ary);*/
                                                $resp['status'] = 200;
                                                $resp['message'] = 'Product created successfully!';
                                                $resp['data'] = $look_up_ary;
                                                /*array('product_id'=>$product_id,'variant_id'=> 0,'lookup_id' => $lookup_id);*/
                                                $logs = array('action_name' => 'CreateNewProduct','action_id'=> $product_id,'status' => $resp['status'],'response' => $resp['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));
                                                $this->ci->db->insert('ecom_sync_logs',$logs);

                                            }elseif($method == 'UpdateProduct'){
                                                $aodry_product_id = $product_post['aodry_product_id'];
                                                $look_up_ary = array();
                                                if($aodry_product_id){
                                                    $this->db->select("aodry_product_id,aodry_variant_id,woo_product_id,woo_variant_id,pro_title");
                                                    $this->db->where('aodry_product_id',$aodry_product_id);
                                                    $this->db->from('ecom_product_sync');
                                                    $get = $this->db->get();
                                                    $result = $get->result();
                                                    $this->db->select("*");
                                                    $this->db->where('product_id',$aodry_product_id);
                                                    $this->db->from('products');
                                                    $get_pro = $this->db->get();
                                                    $pro_result = $get_pro->result();
                                                    $product_code = $product_batch = '';
                                                    if(!empty($pro_result)){
                                                        $product_code = $pro_result[0]->product_code;
                                                        $product_batch = $pro_result[0]->product_batch;
                                                    }
                                                    $is_varients = 'N';
                                                    $ar = array();
                                                    if($product_post['is_variant'] == 1 && !empty($product_post['variants']) && !empty($product_post['attributes']))
                                                        $is_varients = 'Y';
                                                    $image_name = $this->download_remote_file_with_curl($product_post['image']);
                                                    
                                                    if(!empty($result)){
                                                        $product_data     = array(
                                                                "product_name"           => $product_name,
                                                                "product_category_id"    => $category_resp['category_id'],
                                                                "product_subcategory_id" => $category_resp['sub_category_id'],
                                                                "product_details"        => trim($product_post['description']),
                                                                "is_varients"            => $is_varients,
                                                                "product_mrp_price"     => $product_post['regular_price'],
                                                                "product_selling_price" =>$product_post['sale_price'],
                                                                "product_sku"           => $product_post['sku'],
                                                                "product_image"         => $image_name,
                                                                "updated_date"           => date('Y-m-d'),
                                                                "updated_user_id"        => $this->ci->session->userdata('SESS_USER_ID')
                                                            );

                                                        $this->db->set($product_data);
                                                        $this->db->where('product_id',$aodry_product_id);
                                                        $this->db->where('branch_id',$this->ci->session->userdata('SESS_BRANCH_ID'));
                                                        $this->db->update('products');
                                                        $look_up_ary[] =  array(
                                                                    'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                                                    'aodry_product_id' => $aodry_product_id,
                                                                    'aodry_variant_id' => 0,
                                                                    'woo_product_id' => $product_post['product_id'],
                                                                    'woo_variant_id' => 0,
                                                                    'pro_title'  => $product_name,
                                                                    'pro_selling_price' => $product_post['sale_price'],
                                                                    'pro_stock' => 0,
                                                                    'pro_batch' => $product_batch,
                                                                    'added_date' => date('Y-m-d'),
                                                                    'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                                                );
                                                        if($product_post['is_variant'] == 1 && !empty($product_post['variants'])){
                                                            $total_count = $this->db->query("SELECT product_id FROM products WHERE parent_id={$aodry_product_id} ");
                                                            $a = $total_count->num_rows();
                                                            $a++;
                                                            foreach ($product_post['variants'] as $key => $variant) {
                                                                if(!@$variant['aodry_variant_id'] || $variant['aodry_variant_id'] <= 0){
                                                                    $exist_keys = $this->general_model->getJoinRecords('k.varients_id,k.varient_key,k.type,v.varients_value_id,v.varients_value', 'varients k', array('k.delete_status' => 0,'v.delete_status' => 0,'k.branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID')),array('varients_value v' => "k.varients_id=v.varients_id#left"));

                                                                    $rearrange_key = array();
                                                                    if(!empty($exist_keys)){
                                                                        
                                                                        foreach ($exist_keys as $key => $value) {
                                                                            $k = strtolower(trim(str_ireplace(' ','_', trim($value->varient_key))));
                                                                            $kv = strtolower(trim(str_ireplace(' ','_', trim($value->varients_value))));
                                                                            $rearrange_key[$k]['key'] = $value->varient_key;
                                                                            $rearrange_key[$k]['id'] = $value->varients_id;
                                                                            $rearrange_key[$k]['values'][$kv] = $value->varients_value_id;
                                                                        }
                                                                    }
                                                                    $v = $val_ids = array();
                                                                    
                                                                    foreach ($variant['attributes'] as $attr_key => $value) {
                                                                        array_push($v, $value);
                                                                        $check_key = strtolower(trim(str_ireplace(' ','_', trim($attr_key))));
                                                                        if(array_key_exists($check_key, $rearrange_key)){
                                                                            $keyId = $rearrange_key[$check_key]['id'];
                                                                            $existValues = $rearrange_key[$check_key]['values'];
                                                                            
                                                                            $newVal = strtolower(trim(str_ireplace(' ','_', trim($value))));
                                                                            if(array_key_exists($newVal, $existValues)){
                                                                                $valueId = $existValues[$newVal];
                                                                            }else{
                                                                                $varient_data = array(
                                                                                            'varients_id' => $keyId,
                                                                                            'varients_value' => ucfirst(strtolower(trim($value))),
                                                                                            'added_date' => date('Y-m-d'),
                                                                                            'added_user_id' => $this->ci->session->userdata('SESS_USER_ID'),
                                                                                            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));

                                                                                $valueId = $this->general_model->insertData('varients_value', $varient_data);
                                                                            }
                                                                        }else{
                                                                            $varient_data = array(
                                                                                "varient_key" => ucfirst(strtolower(trim($attr_key))),
                                                                                "added_date" => date('Y-m-d'),
                                                                                "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                                                                                "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'));

                                                                            $keyId = $this->general_model->insertData('varients', $varient_data);
                                                                            
                                                                                
                                                                            $varient_data = array(
                                                                                        'varients_id' => $keyId,
                                                                                        'varients_value' => ucfirst(strtolower(trim($value))),
                                                                                        'added_date' => date('Y-m-d'),
                                                                                        'added_user_id' => $this->ci->session->userdata('SESS_USER_ID'),
                                                                                        'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'));

                                                                            $valueId = $this->general_model->insertData('varients_value', $varient_data);
                                                                        }
                                                                        array_push($val_ids, $valueId);
                                                                        $variantPro[] = array(
                                                                                    'varients_value_id' => $valueId,
                                                                                    'varients_id'       => $keyId,
                                                                                    'product_varients_id'=> $aodry_product_id,
                                                                                    'delete_status'     => 0
                                                                                );
                                                                    }
                                                                    $combination_name = implode(" / ",$v);
                                                                    $comm = array(
                                                                        'product_code' => $product_code,
                                                                        'combinations' => $combination_name,
                                                                        'varient_value_id' => implode(" / ",$val_ids),
                                                                        'status' => 'Y',
                                                                        'product_id' => $aodry_product_id,
                                                                        'branch_id' => $this->ci->session->userdata("SESS_BRANCH_ID")
                                                                    );

                                                                    $combinations_id = $this->general_model->insertData('product_combinations', $comm);
                                                                   
                                                                    $this->db->insert_batch('product_varients_value', $variantPro);

                                                                    $variants_name = $product_name.' / '.$combination_name;
                                                                    $insert_product = array(
                                                                                'product_code' => $product_code.'-0'.$a,
                                                                                'parent_id'    => $aodry_product_id,
                                                                                'product_name' => $variants_name,
                                                                                'product_hsn_sac_code' =>'',
                                                                                'product_category_id' => $category_resp['category_id'],
                                                                                'product_subcategory_id' => $category_resp['sub_category_id'],
                                                                                'product_quantity' => 0,
                                                                                'product_unit' => $unit_id,
                                                                                "product_price"          => 0,
                                                                                "product_tds_id"         => 0,
                                                                                "product_tds_value"      => 0,
                                                                                "product_gst_id"         => 0,
                                                                                "product_gst_value"      => 0,
                                                                                'product_details' => $variant['variation_description'],
                                                                                'is_assets' => 'N',
                                                                                'is_varients' => 'N',
                                                                                'product_unit_id' => $unit_id,
                                                                                'product_type' => 'finishedgoods',
                                                                                'product_mrp_price' => $variant['display_regular_price'],
                                                                                'product_selling_price' => $variant['display_price'],  
                                                                                'product_sku' => $variant['sku'],
                                                                                'product_image' => $product_image,
                                                                                "product_batch" => $product_batch,
                                                                                'product_serail_no' =>'',
                                                                                'added_date' => date('Y-m-d'),
                                                                                'added_user_id' => $this->ci->session->userdata('SESS_USER_ID'),
                                                                                "ecommerce"             => 1,
                                                                                "branch_id"             => $this->ci->session->userdata('SESS_BRANCH_ID')
                                                                            );
                                                                    
                                                                    $variant_id = $this->general_model->insertData('products', $insert_product);

                                                                    $look_up =  array(
                                                                            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                                                            'aodry_product_id' => $aodry_product_id,
                                                                            'aodry_variant_id' => $variant_id,
                                                                            'woo_product_id' => $product_post['product_id'],
                                                                            'woo_variant_id' => $variant['variation_id'],
                                                                            'pro_title'  => $variants_name,
                                                                            'pro_selling_price' => $variant['display_price'],
                                                                            'pro_stock' => 0,
                                                                            'pro_batch' => $product_batch,
                                                                            'added_date' => date('Y-m-d'),
                                                                            'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                                                        );

                                                                    $this->db->insert('ecom_product_sync', $look_up);
                                                                    $look_up_ary[] = $look_up;
                                                                    $a++;
                                                                }
                                                            }
                                                        }
                                                        $resp['status'] = 200;
                                                        $resp['message'] = 'Product updated successfully!';
                                                        $resp['data'] = $look_up_ary;
                                                    }
                                                }

                                                $logs = array('action_name' => 'UpdateProduct','action_id'=> $aodry_product_id,'status' => $resp['status'],'response' => $resp['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));
                                                $this->ci->db->insert('ecom_sync_logs',$logs);
                                            }
                                        }
                                    
                                    }else{
                                        $resp['status'] = 404;
                                        $resp['message'] = 'Invalid Branch Token.';
                                    }
                                }else{
                                    $resp['status'] = 404;
                                    $resp['message'] = 'Invalid branch detail.';
                                }
                            }else{
                                $resp['status'] = 404;
                                $resp['message'] = 'Invalid branch detail.';
                            }
                        }else{
                            $resp['status'] = 404;
                            $resp['message'] = 'User details required!';
                        }
                    }else{
                        $resp['status'] = 404;
                        $resp['message'] = 'Branch details required';
                    }
                }else{
                    $resp['status'] = 404;
                    $resp['message'] = 'Method not found!';
                }
            }else{
                $resp['status'] = 404;
                $resp['message'] = 'Method not defined!';
            }
        } catch (Exception $e) {
            
        }

        if ($resp['status'] == 200) {
            $this->response($resp, REST_Controller::HTTP_OK);
        }else{
            $this->response($resp, REST_Controller::HTTP_NOT_FOUND);
        }
        exit();
    }

    function download_remote_file_with_curl($img_url){
        $image_name = '';
        if($img_url != ''){
            $path_parts = pathinfo($img_url);

            date_default_timezone_set('Asia/Kolkata');
            $date       = date_create();
            $image_path = $path_parts['filename'] . '_' . date_timestamp_get($date) . '.' . $path_parts['extension'];
            if (!is_dir('assets/product_image/' . $this->ci->session->userdata('SESS_BRANCH_ID'))){
                mkdir('./assets/product_image/' . $this->ci->session->userdata('SESS_BRANCH_ID'), 0777, TRUE);
            } 
            $url = "assets/product_image/" . $this->ci->session->userdata('SESS_BRANCH_ID') . "/" . $image_path;
            if (in_array($path_parts['extension'], array(
                            "jpg",
                            "jpeg",
                            "JPG",
                            "JPEG",
                            "png","PNG" ))){

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_POST, 0); 
                curl_setopt($ch,CURLOPT_URL,$img_url); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                $file_content = curl_exec($ch);
                curl_close($ch);
                $downloaded_file = fopen($url, 'w');
                fwrite($downloaded_file, $file_content);
                fclose($downloaded_file);
                $image_name = $image_path;
            }
        }
        return $image_name;
    }

    function findCategory($find,$catName,$subCategory){
        $catName = preg_replace('!\s+!', ' ', trim($catName));
        $catId = 0;
        foreach ($find as $key => $value) {
            $category_name = preg_replace('!\s+!', ' ', trim($value->category_name));
            if(strtolower($category_name) == strtolower($catName)){
                $catId = $value->category_id;
            }
        }
        
        if(!$catId){
            $category_code                   = $this->product_model->getMaxCategoryId();
            $category_data                   = array(
                    "category_code" => $category_code,
                    "category_name" => trim($catName),
                    "category_type" => 'product',
                    "added_date"    => date('Y-m-d'),
                    "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                    "branch_id"     => $this->ci->session->userdata('SESS_BRANCH_ID')
                );
            $catId                      = $this->general_model->insertData('category', $category_data);
            $type                       = 'product';
            $log_data                   = array(
                        'user_id'           => $this->ci->session->userdata('SESS_USER_ID'),
                        'table_id'          => $catId,
                        'table_name'        => 'category',
                        'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        "branch_id"         => $this->ci->session->userdata('SESS_BRANCH_ID'),
                        'message'           => 'Category Inserted(Subcategory)' );
            $this->general_model->insertData('log', $log_data);
        }

        $sub_category_id = 0;
       
        if(trim($subCategory) != ''){
            $subCat = $this->general_model->getRecords('*', 'sub_category', array(
                'category_id'   => $catId,
                'delete_status' => 0));
            $subCategory = preg_replace('!\s+!', ' ', trim($subCategory));

            foreach ($subCat as $key => $sub) {
                $sub_category_name = preg_replace('!\s+!', ' ', trim($sub->sub_category_name));
                if(strtolower($sub_category_name) == strtolower($subCategory)){
                    $sub_category_id = $value->sub_category_id;
                }
            }

            if(!$sub_category_id){
                $subcategory_code = $this->product_model->getMaxSubcategoryId();
                $sub_category_data = array(
                    "category_id" => $catId,
                    "sub_category_code" => $subcategory_code,
                    "sub_category_name" => $subCategory,
                    "added_date" => date('Y-m-d'),
                    "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                    "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'));
                $sub_category_id = $this->general_model->insertData('sub_category', $sub_category_data);
                $log_data = array(
                    'user_id' => $this->ci->session->userdata('user_id'),
                    'table_id' => $sub_category_id,
                    'table_name' => 'sub_category',
                    'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id" => $this->ci->session->userdata('SESS_BRANCH_ID'),
                    'message' => 'Subcategory Inserted');
                $this->general_model->insertData('log', $log_data);
            }
        }
        $resp = array('category_id' => $catId, 'sub_category_id' => $sub_category_id);
        return $resp;
    }

    function getProductUnit($unit_name){
        $unit_name = trim(strtoupper($unit_name));
        $unit_resp = $this->db->query("SELECT id FROM `uqc` WHERE UPPER(`uom`) = '{$unit_name}' AND uom_type='product' AND delete_status = 0");
        $unitResult = $unit_resp->result();
        $unitId = 0;
       
        if(!empty($unitResult)){
            $unitId = $unitResult[0]->id;
        }else{

            $uqc_data = array(
                            "uom"           => $unit_name,
                            "uom_type"      => 'product',
                            "description"   => '',
                            "added_user_id" => $this->ci->session->userdata('SESS_USER_ID'),
                            "added_date"    => date('Y-m-d') 
                        );

            if ($unitId = $this->general_model->insertData("uqc", $uqc_data)){
                $log_data = array(
                        'user_id'           => $this->ci->session->userdata("SESS_BRANCH_ID"),
                        'table_id'          => $unitId,
                        'table_name'        => 'uqc',
                        'financial_year_id' => $this->ci->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                        "branch_id"         => $this->ci->session->userdata("SESS_BRANCH_ID"),
                        'message'           => 'UOM Inserted' );
                        $log_table = $this->config->item('log_table');
                        $this->general_model->insertData($log_table , $log_data);
            }
        }
        return $unitId;
    }
}