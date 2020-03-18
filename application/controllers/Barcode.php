<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode extends MY_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('product_model');
        $this->modules = $this->get_modules();
        $this->load->helper(array('form','url' ));
        $this->load->library('form_validation');

        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
    }

    private function set_barcode($code){
        //load library
        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
        //generate barcode
        Zend_Barcode::render('code128', 'image', array('text' => $code ), array());
    }

    public function index(){
        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */

        $data=array_merge($data,$section_modules);
        if (!empty($this->input->post())){
            $columns = array(
                0 => 'id',
                1 => 'product_name',
                2 => 'category_type',
                3 => 'orientation',
            );

            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->barcode_list_field();
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
            } $send_data = array();
            if (!empty($posts)){
                foreach ($posts as $post)
                {
                    $barcode_id = $this->encryption_url->encode($post->id);
                    $nestedData['product_name'] = $post->product_name;
                    $nestedData['style'] = $post->bar_style;
                    $nestedData['unit'] = $post->unit;
                    $nestedData['selling_price'] = $post->selling_price;
                    $nestedData['category'] = $post->category;
                    $nestedData['sku'] = $post->sku;
                    $nestedData['serial_no'] = $post->serial_no;
                    if($post->orientation == ''){
                        $orientation = ' - ';
                    }else{
                        $orientation = $post->orientation;
                    }
                    $nestedData['orientation'] = $orientation;
                    if($post->height == '' || $post->height == 0){
                        $height = ' - ';
                    }else{
                        $height = $post->height;
                    }
                    $nestedData['height'] = $height;
                    if($post->width == '' || $post->width == 0){
                        $width = ' - ';
                    }else{
                        $width = $post->width;
                    }
                    $nestedData['width'] = $width;
                    $nestedData['action'] = '<a href="' . base_url('barcode/generate_barcode_from_list/') . $barcode_id . '">Generate Barcode</a>';
                    $send_data[]          = $nestedData;
                }
            }
            $json_data = array(
                    "draw"            => intval($this->input->post('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $send_data );
            echo json_encode($json_data);
        }else{            
            $this->load->view('barcode/list', $data);
        }

    }

    public function add(){
        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view('barcode/add', $data);
    }

   
    function get_barcode(){
        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view('barcode/get_barcode', $data);
    }

    function print_barcode(){
        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->form_validation->set_rules('style', "style", 'required');

        if ($this->form_validation->run() == true){

            $unit = ($this->input->post('unit_chk')) ? 'Yes' : 'No';
            $price = ($this->input->post('price_chk')) ? 'Yes' : 'No';

            $category = ($this->input->post('category_chk')) ? 'Yes' : 'No';
            $sku = ($this->input->post('sku_chk')) ? 'Yes' : 'No';
            $style      = $this->input->post('style');
            $weight      = $this->input->post('cf_width');
            $height      = $this->input->post('cf_height');
            $orientation      = $this->input->post('cf_orientation');
            $bci_size   = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            $currencies = $this->currency_call();            
            $item_data  = $this->input->post('table_data');
            $js_data    = json_decode($item_data);


            foreach ($js_data as $key => $value){
                $pid      = $value->item_id;
                $quantity = $value->item_quantity;
                $barcode_symbology = 'code128';
                $branch_id         = $this->session->userdata('SESS_BRANCH_ID');
                $barcode_id_generate = $this->generate_barcode($value->item_code, $branch_id, $barcode_symbology, $unit, $price, $category, $sku, 'No', $style, $weight, $height, $orientation,$pid,$bci_size);
                $category_code =  explode("-", $value->item_category_code);
                $sku_code = $category_code[1]."-".$value->item_code;

                
                $barcodes[] = array(
                        'code'          => $value->item_code,
                        'name'          => $value->item_name,
                        'barcode'       => $this->product_barcode($barcode_id_generate, $value->item_barcode_symbology, $bci_size),
                        'selling_price' => $this->input->post('price_chk') ? $value->item_selling_price : FALSE,
                        'unit'          => $this->input->post('unit_chk') ? $value->item_unit : FALSE,
                        'category'      => $this->input->post('category_chk') ? $value->item_category : FALSE,
                        'sku'      => $this->input->post('sku_chk') ? $sku_code : FALSE,
                        'quantity'      => $quantity,
                        'style_height'  => $bci_size
                );
            }

            $data['barcodes'] = $barcodes;
            //$this->data['currencies'] = $currencies;
            $data['style']    = $style;
            $data['items']    = false;
            $this->load->view('barcode/print_barcode', $data);
        }
    }

    function product_barcode($barcode_id, $bcs = 'code128', $height = 60){
        // $branch_id=$this->session->userdata('SESS_BRANCH_ID');
        // $code=sprintf('%08d', $product_code);
        // $image="assets/images/barcode/".$branch_id."/{$code}.png";

        $string       = '*';
        $table        = 'tbl_barcode ';
        $where        = array('id' => $barcode_id );
        $barcode_val = $this->general_model->getRecords($string, $table, $where, $order        = "");       
        $barcode_path = $barcode_val[0]->image_path;
        $item_code = $barcode_val[0]->barcode;
        
       // $barcode_path = $path.$item_code."/";
        // "assets/images/barcode/1/"

      //  $last = explode("/", $barcode_path[0]->barcode, 6);

        
        $last = explode("/", $barcode_path, 6);
        //  return "assets/images/barcode/1/00000191/" . $last[4] . $height . ".png";       

        return $barcode_path. $item_code . $height . ".png";
    }

    function gen_barcode($product_code = NULL, $bcs = 'code128', $height = 60, $text = 1){
        // $drawText = ($text != 1) ? FALSE : TRUE;
        // $this->load->library('zend');
        // $this->zend->load('Zend/Barcode');
        // $barcodeOptions = array('text' => $product_code, 'barHeight' => $height, 'drawText' => $drawText, 'factor' => 1);
        // $rendererOptions = array('imageType' => 'png', 'horizontalPosition' => 'center', 'verticalPosition' => 'middle');
        // $imageResource = Zend_Barcode::render($bcs, 'image', $barcodeOptions, $rendererOptions);
        //$imageResource=
        return $imageResource;
    }

    public function get_product_suggestions($term)
    {
        $module_id     = $this->config->item('product_module');
        $branch_id     = $this->session->userdata('SESS_BRANCH_ID');
        $settings_data = $this->common->settings_field($module_id);
      
        $item_access   = $this->general_model->getRecords($settings_data['string'], $settings_data['table'], $settings_data['where']);

        $suggestions_query = $this->common->product_suggestions_field($item_access, $term);
        $data              = $this->general_model->getQueryRecords($suggestions_query);
        echo json_encode($data);
    }

    public function get_table_items($code)
    {
        $item_code = explode("-", $code);

        $product_data = $this->common->product_varient_field($item_code[0]);

        //$data         = $this->general_model->getRecords($product_data['string'], $product_data['table'], $product_data['where']);

        $data = $this->general_model->getJoinRecords($product_data['string'], $product_data['table'], $product_data['where'], $product_data['join']);

        $discount_data             = $this->common->discount_field();
        $data['discount']          = $this->general_model->getRecords($discount_data['string'], $discount_data['table'], $discount_data['where']);
        $branch_details            = $this->get_default_country_state();
        $data['branch_country_id'] = $branch_details['branch'][0]->branch_country_id;
        $data['branch_state_id']   = $branch_details['branch'][0]->branch_state_id;
        $data['branch_id']         = $branch_details['branch'][0]->branch_id;
        $data['item_id']           = $item_code[0];
        $data['item_type']         = $item_code[1];
        echo json_encode($data);
    }

    function get_suggestions(){

        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term)
        {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }
        $module_id         = $this->config->item('product_module');
        $branch_id         = $this->session->userdata('SESS_BRANCH_ID');
        $settings_data     = $this->common->settings_field($module_id);
        $item_access       = $this->general_model->getRecords($settings_data['string'], $settings_data['table'], $settings_data['where']);
        $suggestions_query = $this->common->item_suggestions_field($item_access, $term);
        $data              = $this->general_model->getQueryRecords($suggestions_query);

        $this->sma->send_json($data);
    }


    function generate_barcode($product_code, $branch_id, $barcode_symbology, $unit, $price, $category, $sku, $serial, $style, $width, $height_val, $orientation,$pid,$bci_size){
            $code   = $product_code;
            $height = array(
                    '0' => 20,
                    '1' => 30,
                    '2' => 50,
                    '3' => 60 );
        if (!is_dir('assets/images/barcode/' . $branch_id . '/' . $code)){
            mkdir('./assets/images/barcode/' . $branch_id . '/' . $code, 0777, TRUE);
        }

        for ($i = 0; $i < 4; $i++){
            $file = Zend_Barcode::draw($barcode_symbology, 'image', array(
                            'text'      => $code,
                            'barHeight' => $height[$i],
                            'drawText'  => 1,
                            'factor'    => 1 ), array());

             imagepng($file, "./assets/images/barcode/" . $branch_id . "/{$code}/{$code}" . $height[$i] . ".png");
         $store_image = "{$code}" . $height[$i] . ".png";
        }        
        
        $barcode_path = "assets/images/barcode/" . $branch_id . "/{$code}/";
        $barcode_data = array('barcode' => $code,
                                'product_id' => $pid,
                                'bar_style' => $style,
                                'barcode_symbology'  => $barcode_symbology,
                                'barcode_image' => $store_image,
                                'image_path' => $barcode_path,
                                'height' => $height_val,
                                'width' => $width,
                                'orientation' => $orientation,
                                'selling_price' => $price,
                                'unit' => $unit,
                                'category' => $category,
                                'serial_no' => $serial,
                                'sku' => $sku,
                                'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                                'added_date' => date('Y-m-d'),
                                'added_user_id' => $this->session->userdata('SESS_USER_ID')
                            );

         $data  = $this->general_model->getRecords('id', 'tbl_barcode', $barcode_data );
         if(!empty($data)){
           $barcode_id =  $data[0]->id;
         }else{
            $barcode_id = $this->general_model->insertData("tbl_barcode", $barcode_data);
            $log_data = array(
                    'user_id'           => $this->session->userdata("SESS_BRANCH_ID"),
                    'table_id'          => 0,
                    'table_name'        => 'tbl_barcode',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata("SESS_BRANCH_ID"),
                    'message'           => 'Barcode Inserted' );
                    $log_table = $this->config->item('log_table');
                    $this->general_model->insertData($log_table , $log_data);
         }        
        return $barcode_id;
    }


    public function generate_barcode_from_list($id){
        $id  = $this->encryption_url->decode($id);
        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);

        /* presents all the needed */

        $data=array_merge($data,$section_modules);

        $list_data           = $this->common->barcode_list_field($id);
        $data['posts'] = $this->general_model->getPageJoinRecords($list_data);
        $this->load->view('barcode/generate_barcode', $data);
    }


    function print_barcode_list(){
        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->form_validation->set_rules('style', "style", 'required');

        if ($this->form_validation->run() == true){

            $unit = ($this->input->post('unit_chk')) ? 'Yes' : 'No';
            $price = ($this->input->post('price_chk')) ? 'Yes' : 'No';
            $category = ($this->input->post('category_chk')) ? 'Yes' : 'No';
            $sku = ($this->input->post('sku_chk')) ? 'Yes' : 'No';
            $serial = ($this->input->post('sku_serial')) ? 'Yes' : 'No';
            $style      = $this->input->post('style');
            $weight      = $this->input->post('cf_width');
            $height      = $this->input->post('cf_height');
            $orientation      = $this->input->post('cf_orientation');
            $bci_size   = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            $currencies = $this->currency_call();            
            $item_data  = $this->input->post('table_data');
            $js_data    = json_decode($item_data);


          
                $pid      = $this->input->post('item_id');
                $quantity = $this->input->post('item_quantity');
                $barcode_symbology = 'code128';
                $branch_id         = $this->session->userdata('SESS_BRANCH_ID');
                $barcode_id_generate = $this->generate_barcode($this->input->post('item_code'), $branch_id, $barcode_symbology, $unit, $price, $category, $sku, $serial, $style, $weight, $height, $orientation,$pid,$bci_size);
                $category = $this->input->post('item_category_code');
                $category_code =  explode("-", $category);
                $item_code = $this->input->post('item_code');
                $sku_code = $category_code[1]."-".$item_code;
                $barcodes[] = array(
                        'code'          => $this->input->post('item_code'),
                        'name'          => $this->input->post('item_name'),
                        'barcode'       => $this->product_barcode($barcode_id_generate, $this->input->post('item_barcode_symbology'), $bci_size),
                        'selling_price' => $this->input->post('price_chk') ? $this->input->post('item_selling_price') : FALSE,
                        'unit'          => $this->input->post('unit_chk') ? $this->input->post('item_unit') : FALSE,
                        'category'      => $this->input->post('category_chk') ? $this->input->post('item_category') : FALSE,
                        'sku'      => $this->input->post('sku_chk') ? $sku_code : FALSE,
                        'quantity'      => $quantity,
                        'style_height'  => $bci_size
                );
            

            $data['barcodes'] = $barcodes;
            //$this->data['currencies'] = $currencies;
            $data['style']    = $style;
            $data['items']    = false;

             
            $this->load->view('barcode/print_barcode', $data);
        }
    }

}

