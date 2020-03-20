<?php
class ProductHook {
    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function CreateProduct($data){
        
        if($data['product_category_id'] != ''){

            $Cat = $this->ci->general_model->getRecords('category_name', 'category', array(
                    'category_id'   => $data['product_category_id'],
                    'delete_status' => 0));
            $data['product_category_name'] = $Cat[0]->category_name; 
        }

        if($data['product_subcategory_id'] != ''){

            $subCat = $this->ci->general_model->getRecords('sub_category_name', 'sub_category', array(
                    'sub_category_id'   => $data['product_subcategory_id'],
                    'delete_status' => 0));
            $data['product_sub_category_name'] = $subCat[0]->sub_category_name; 
        }

        $data['is_variable'] = 0;
        $attributes = array();
        if($data['is_varients'] == 'Y'){
            $data['is_variable'] = 1;
            foreach ($data['variants'] as $key => $value) {
                $varient_value_id = trim($value['varient_value_id']);
                if($varient_value_id != ''){
                    /*$varient_value_id = explode(',', $varient_value_id);*/
                    $variants_key = $this->ci->db->query("SELECT v.varients_value,v.varients_value_id,k.varients_id,k.varient_key FROM varients_value v JOIN varients k ON v.varients_id=k.varients_id WHERE v.varients_value_id IN ({$varient_value_id})");
                    $variants_key = $variants_key->result();
                    $variant_val = array();
                    foreach ($variants_key as $v) {
                        $attributes[$v->varient_key][] = $v->varients_value;
                        $variant_val[$v->varient_key][] = $v->varients_value;
                        $attributes[$v->varient_key] = array_unique($attributes[$v->varient_key]);
                    }
                    $data['variants'][$key]['attributes'] = $variant_val;
                }
            }
            $data['attributes'] = $attributes;
        }

        $user_id = $this->ci->session->userdata('SESS_USER_ID');
        $branch_id = $this->ci->session->userdata('SESS_BRANCH_ID');
        //ecom url 
        $ecom_url = $this->ci->general_model->getRecords('ecom_url', 'branch', array(
                    'branch_id'     => $branch_id,
                    'ecommerce'  => '1'));
        $url = $ecom_url[0]->ecom_url;

        //user detail
        $branch_detail = $this->ci->general_model->getRecords('*', 'users', array(
                    'id'     => $user_id));

        /*$branch_detail = $this->ci->db->query("SELECT email, password, branch_code FROM `users` WHERE id = {$user_id}");
        $branch_detail = $branch_detail->row();*/

        //login code
        $login_code = $this->ci->general_model->getRecords('token', 'ecom_branch_setting', array(
                    'branch_id'     => $branch_id));

        $data = array(
            'Method' => 'CreateProduct',
            'branch' => array(
                'User' => $branch_detail[0]->email,
                'Password' => base64_encode('123456'),
                'Code' => $branch_detail[0]->branch_code,
                'LoginCode' => $login_code[0]->token
            ),
            'data' => $data
        );

        /*$data = array(
            'Method' => 'CreateProduct',
            'branch' => array(
                'User' => 'credittest12@gmail.com',
                'Password' => base64_encode('123456'),
                'Code' => 'CODE054',
                'LoginCode' => ''
            ),
            'data' => $data
        );*/

        /* API URL */
        /*$url = 'http://192.168.1.36/Aodry-API/pro_api.php';*/
        //$url = 'http://192.168.1.86/fashnett/wp-json/api/v1/CreateProduct';
        $url = $url.'/CreateProduct';
        echo "<pre>";
        print_r($data);
        $result = $this->ci->common->postCurlData($url,$data);

        $result = json_decode($result,true);
        $look_up_ary = array();
        /*print_r($result);*/
        if(!empty($result)){
            if(@$result['status'] && $result['status'] == 200){ echo "<pre>";print_r($result);exit();
                $resp = $result['data'];
                foreach ($resp as $key => $value) {
                    $look_up_ary[] = array(
                                        'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                        'aodry_product_id' => $value['aodry_product_id'],
                                        'aodry_variant_id' => $value['aodry_variant_id'] ? $value['aodry_variant_id'] : 0,
                                        'woo_product_id' => $value['woo_product_id'],
                                        'woo_variant_id' => $value['woo_variant_id'] ? $value['woo_variant_id'] : 0,
                                        'pro_title'  => $value['pro_title'],
                                        'pro_selling_price' => $value['pro_selling_price'],
                                        'pro_stock' => 0,
                                        'pro_batch' => $value['pro_batch'],
                                        'added_date' => date('Y-m-d'),
                                        'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                    );
                }
            }else{
                $look_up_ary[] = array(
                                        'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                        'aodry_product_id' => $data['product_id'],
                                        'aodry_variant_id' => 0,
                                        'woo_product_id' => 0,
                                        'woo_variant_id' => 0,
                                        'pro_title'  => '',
                                        'pro_selling_price' => '',
                                        'pro_stock' => 0,
                                        'pro_batch' => '',
                                        'status' => $result['status'],
                                        'response' => $result['message'],
                                        'added_date' => date('Y-m-d'),
                                        'added_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                    );
            }
            $this->db->insert_batch('ecom_product_sync', $look_up_ary);
            $logs = array('action_name' => 'CreateProduct','action_id'=> $data['product_id'],'status' => $result['status'],'response' => $result['message'],'user_id' => $this->ci->session->userdata('SESS_UresultSER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));

                $this->ci->db->insert('ecom_sync_logs',$logs);
        }
        exit;
    }

    public function UpdateProduct($data){
        if(@$data['product_id']){
            $product_id = $data['product_id'];
            $pro_detail = $this->ci->db->query("SELECT * FROM `products` WHERE product_id={$product_id} ");
            $pro_detail = $pro_detail->row();
            if($pro_detail->parent_id > 0){
                $ecom_detail = $this->ci->db->query("SELECT * FROM `ecom_product_sync` WHERE aodry_variant_id={$product_id} AND branch_id='".$this->ci->session->userdata('SESS_BRANCH_ID')."' ");
            }else{
                $ecom_detail = $this->ci->db->query("SELECT * FROM `ecom_product_sync` WHERE aodry_product_id={$product_id} AND branch_id='".$this->ci->session->userdata('SESS_BRANCH_ID')."' ");
            }
            /*print_r($this->ci->db->last_query());*/
            if($ecom_detail->num_rows() > 0){
                $ecom_detail = $ecom_detail->row();
                $data['woo_product_id'] = $ecom_detail->woo_product_id;
                $data['woo_variant_id'] = $ecom_detail->woo_variant_id;
            }
        
        
            if($data['product_category_id'] != ''){

                $Cat = $this->ci->general_model->getRecords('category_name', 'category', array(
                        'category_id'   => $data['product_category_id'],
                        'delete_status' => 0));
                $data['product_category_name'] = $Cat[0]->category_name; 
            }

            if($data['product_subcategory_id'] != ''){

                $subCat = $this->ci->general_model->getRecords('sub_category_name', 'sub_category', array(
                        'sub_category_id'   => $data['product_subcategory_id'],
                        'delete_status' => 0));
                $data['product_sub_category_name'] = $subCat[0]->sub_category_name; 
            }

            $data['is_variable'] = 0;
            $attributes = array();
            if($data['is_varients'] == 'Y'){
                $data['is_variable'] = 1;
                foreach ($data['variants'] as $key => $value) {
                    $varient_value_id = trim($value['varient_value_id']);
                    if($varient_value_id != ''){
                        /*$varient_value_id = explode(',', $varient_value_id);*/
                        $variants_key = $this->ci->db->query("SELECT v.varients_value,v.varients_value_id,k.varients_id,k.varient_key FROM varients_value v JOIN varients k ON v.varients_id=k.varients_id WHERE v.varients_value_id IN ({$varient_value_id})");
                        $variants_key = $variants_key->result();
                        $variant_val = array();
                        foreach ($variants_key as $v) {
                            $attributes[$v->varient_key][] = $v->varients_value;
                            $variant_val[$v->varient_key][] = $v->varients_value;
                            $attributes[$v->varient_key] = array_unique($attributes[$v->varient_key]);
                        }
                        $data['variants'][$key]['attributes'] = $variant_val;
                    }
                }
                $data['attributes'] = $attributes;
            }

            $data = array(
                'Method' => 'UpdateProduct',
                'branch' => array(
                    'User' => 'credittest12@gmail.com',
                    'Password' => base64_encode('123456'),
                    'Code' => 'CODE054',
                    'LoginCode' => ''
                ),
                'data' => $data
            );

            echo "<pre>";
            print_r($data);exit;
            /* API URL */
            $url = 'http://localhost/Aodry-API/pro_api.php';
            /*$url = 'http://192.168.1.85/fashnett/wp-json/api/v1/UpdateProduct';*/
            $result = $this->ci->common->postCurlData($url,$data);
            $result = json_decode($result,true);
            $look_up_ary = array();
            
            if(!empty($result) && @$result['status']){
                if($result['status'] == 200){
                    $resp = $result['data'];
                    foreach ($resp as $key => $value) {
                        $is_exist = $this->ci->db->query("SELECT uniq_id FROM ecom_product_sync WHERE branch_id='".$this->ci->session->userdata('SESS_BRANCH_ID')."' AND woo_product_id ='{$value['woo_product_id']}' AND woo_variant_id='{$value['woo_variant_id']}' ");
                        if($is_exist->num_rows() <= 0){
                            $look_up_ary[] = array(
                                                'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                                'aodry_product_id' => $value['aodry_product_id'],
                                                'aodry_variant_id' => $value['aodry_variant_id'],
                                                'woo_product_id' => $value['woo_product_id'],
                                                'woo_variant_id' => $value['woo_variant_id'],
                                                'pro_title'  => $value['pro_title'],
                                                'pro_selling_price' => $value['pro_selling_price'],
                                                'pro_stock' => 0,
                                                'pro_batch' => $value['pro_batch'],
                                                'updated_date' => date('Y-m-d'),
                                                'updated_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                            );
                        }
                    }
                    if(!empty($look_up_ary)) $this->db->insert_batch('ecom_product_sync', $look_up_ary);
                }else{
                    $update_error = array(
                                            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                            'status' => $result['status'],
                                            'response' => $result['message'],
                                            'updated_date' => date('Y-m-d'),
                                            'updated_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                        );
                    $this->general_model->updateData('ecom_product_sync', $update_error, array('aodry_product_id' => $data['product_id']));
                }

                $logs = array('action_name' => 'UpdateProduct','action_id'=> $data['product_id'],'status' => $result['status'],'response' => $result['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));

                $this->ci->db->insert('ecom_sync_logs',$logs);
            }
        }
    }

    public function UpdateProductStock($data){
        if(@$data['product_id']){
            $product_id = $data['product_id'];
            $pro_detail = $this->ci->db->query("SELECT parent_id FROM `products` WHERE product_id={$product_id} ");
            $pro_detail = $pro_detail->row();
            if($pro_detail->parent_id > 0){
                $ecom_detail = $this->ci->db->query("SELECT woo_product_id,woo_variant_id,aodry_product_id,aodry_variant_id FROM `ecom_product_sync` WHERE aodry_variant_id={$product_id} AND branch_id='".$this->ci->session->userdata('SESS_BRANCH_ID')."' ");
            }else{
                $ecom_detail = $this->ci->db->query("SELECT woo_product_id,woo_variant_id,aodry_product_id,aodry_variant_id FROM `ecom_product_sync` WHERE aodry_product_id={$product_id} AND branch_id='".$this->ci->session->userdata('SESS_BRANCH_ID')."' ");
            }
            
            if($ecom_detail->num_rows() > 0){
                $ecom_detail = $ecom_detail->row();
                $data['aodry_product_id'] = $ecom_detail->aodry_product_id;
                $data['aodry_variant_id'] = $ecom_detail->aodry_variant_id;
                $data['woo_product_id'] = $ecom_detail->woo_product_id;
                $data['woo_variant_id'] = $ecom_detail->woo_variant_id;
            }

            $data = array(
                'Method' => 'UpdateProductStock',
                'branch' => array(
                    'User' => 'credittest12@gmail.com',
                    'Password' => base64_encode('123456'),
                    'Code' => 'CODE054',
                    'LoginCode' => ''
                ),
                'data' => $data
            );
            /* API URL */
            /*$url = 'http://192.168.1.36/Aodry-API/pro_api.php';*/
            $url = 'http://192.168.1.85/fashnett/wp-json/api/v1/UpdateProductStock';
            $result = $this->ci->common->postCurlData($url,$data);
            $result = json_decode($result,true);
            $look_up_ary = array();
           
            if(!empty($result) && @$result['status']){
                if($result['status'] == 200){
                    $update_error = array(
                                            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                            'product_quantity' => $data['product_quantity'],
                                            'status' => $result['status'],
                                            'response' => $result['message'],
                                            'updated_date' => date('Y-m-d'),
                                            'updated_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                        );
                    $this->general_model->updateData('ecom_product_sync', $update_error, array('aodry_product_id' => $data['product_id']));
                }else{
                    $update_error = array(
                                            'branch_id' => $this->ci->session->userdata('SESS_BRANCH_ID'),
                                            'product_quantity' => $data['product_quantity'],
                                            'status' => $result['status'],
                                            'response' => $result['message'],
                                            'updated_date' => date('Y-m-d'),
                                            'updated_user_id' => $this->ci->session->userdata('SESS_USER_ID')
                                        );
                    $this->general_model->updateData('ecom_product_sync', $update_error, array('aodry_product_id' => $data['product_id']));
                }

                $logs = array('action_name' => 'UpdateProductStock','action_id'=> $data['product_id'],'status' => $result['status'],'response' => $result['message'],'user_id' => $this->ci->session->userdata('SESS_USER_ID'),'branch_id' =>$this->ci->session->userdata('SESS_BRANCH_ID'),'created_at' => date('Y-m-d H:i:s'));

                $this->ci->db->insert('ecom_sync_logs',$logs);
            }
        }
    }
}