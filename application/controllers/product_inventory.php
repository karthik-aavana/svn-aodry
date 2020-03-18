<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Product_inventory extends MY_Controller
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
    }

    public function index()
    {

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
                    $nestedData['product_hsn_sac_code'] = "<a href='" . base_url('product_inventory/product_varient/') . $product_id . "''>" . $post->product_hsn_sac_code . "</a>";
                    $nestedData['product_name']         = "<a href='" . base_url('product_inventory/product_varient/') . $product_id . "''>" . $post->product_name . "</a>";
                    $nestedData['product_igst']         = $post->product_igst;
                    // $nestedData['category_name']        = $post->category_name;
                    $nestedData['product_price']    = $post->product_price;
                    $nestedData['product_quantity'] = '<a data-backdrop="static" data-keyboard="false"  data-toggle="modal" data-target="#quantity_products" class="quantity_change" style="cursor:pointer;" data-pid="' . $product_id . '" data-qty="' . $post->product_quantity . '" title="" >' . $post->product_quantity;
                    '</a>';

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" data-backdrop="static" data-keyboard="false" onclick="get_product(' . $post->product_inventory_id . ')" data-target="#editProduct">Edit Varient</a>';

                    $cols .= '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-id="' . $product_id . '" data-path="product_inventory/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Product</a>';
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
            $data['uqc']              = $this->uqc_call();
            $data['chapter']          = $this->chapter_call();
            $data['hsn']              = $this->hsn_call();

            $this->load->view('product_inventory/list', $data);
        }

    }

    public function add()
    {

        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];

        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;

            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            }

            if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            }

            if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            }

            if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }

        }

        $data['product_category'] = $this->product_category_call();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_call();
        $data['chapter']          = $this->chapter_call();
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

        $this->load->view('product/product_add', $data);
    }

    public function product_varient($id)
    {

// $id = $this->encryption_url->decode($id);
        // echo $id;
        $product_module_id               = $this->config->item('product_module');
        $data['module_id']               = $product_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($product_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['modules'];
        $data['access_sub_modules']      = $section_modules['sub_modules'];
        $data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];
        $data['access_settings']         = $section_modules['settings'];
        $data['access_common_settings']  = $section_modules['common_settings'];

        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;

            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            }

            if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            }

            if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            }

            if ($value->add_privilege == "yes")
            {
                $data['active_add'][$key] = $value->module_id;
            }

        }

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
                    $nestedData['product_quantity'] = '<a data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#quantity_products" class="quantity_change" style="cursor:pointer;" data-pid="' . $product_id . '" data-qty="' . $post->product_quantity . '" title="" >' . $post->product_quantity;
                    '</a>';

                    $nestedData['varient_unit'] = $post->varient_unit;
                    // $cols                       = '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="get_key_val('.$post->product_inventory_varients_id.')" data-target="#keyValue">Edit Varient</a>';

                    $cols = '<a class="delete_button btn btn-xs btn-warning" data-backdrop="static" data-keyboard="false" data-toggle="modal" onclick="getVarients(' . $post->product_inventory_varients_id . ')" data-target="#productVarient">Edit Product</a>';

                    $cols .= '<a data-toggle="modal" data-target="#delete_modal" data-id="' . $product_id . '" data-path="product/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Product</a>';

                    $cols .= '<a class="delete_button btn btn-xs btn-warning" data-backdrop="static" data-keyboard="false" data-toggle="modal" onclick="stock(' . $post->product_inventory_varients_id . ')" data-target="#stock_management">Move to Damaged</a>';

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
            $data['uqc'] = $this->uqc_call();
            $this->load->view('product_inventory/product_varient', $data);
        }

    }

    public function get_varient_values($id)
    {
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

    public function get_varient_key_values($id)
    {

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

// print_r($varients_key_value );die;

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

        // echo json_encode($final);
    }

    public function edit_product_vaarient()
    {

        // print_r($this->input->post());die;

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

        if ($this->general_model->updateData($table, $data, $where))
        {
            echo "success";
        }

    }

    public function get_product_id($id)
    {

        $product = $this->general_model->getJoinRecords('p.*,t.tax_value', 'product_inventory p', [
            'p.product_inventory_id' => $id,
            'p.delete_status'        => 0], [
            'tax t' => 't.tax_id = p.product_tax_id']);

        $products['id']           = $product[0]->product_inventory_id;
        $products['product_code'] = $product[0]->product_code;
        $products['product_name'] = $product[0]->product_name;
        // $products['product_tax_id'] = $product[0]->product_tax_id;
        $products['hsn']       = $product[0]->product_hsn_sac_code;
        $products['igst']      = $product[0]->product_igst;
        $products['cgst']      = $product[0]->product_cgst;
        $products['sgst']      = $product[0]->product_sgst;
        $products['price']     = $product[0]->product_price;
        $products['tax']       = $product[0]->product_tax_id . '-' . $product[0]->tax_value;
        $products['tax_value'] = $product[0]->tax_value;

        echo json_encode($products);
    }

    public function ajax_edit_product()
    {

        $id = $this->input->post('id');

        $product_data = array(
            "product_code"         => $this->input->post('product_code'),
            "product_name"         => $this->input->post('product_name'),
            "product_hsn_sac_code" => $this->input->post('product_hsn_sac_code'),
            "product_price"        => $this->input->post('product_price'),
            "product_tax_id"       => $this->input->post('product_tax'),
            "product_igst"         => $this->input->post('product_igst'),
            "product_cgst"         => $this->input->post('product_cgst'),
            "product_sgst"         => $this->input->post('product_sgst'),
            "updated_date"         => date('Y-m-d'),
            "updated_user_id"      => $this->session->userdata('SESS_USER_ID'));

        if ($this->general_model->updateData('product_inventory', $product_data, array(
            'product_inventory_id' => $id)))
        {
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

        redirect('product_inventory/product_varient/' . $product_varient_value_id, 'refresh');
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
        redirect('product_inventory/product_varient/' . $url, 'refresh');
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
            redirect('product_inventory/product_varient/' . $url, 'refresh');
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

        redirect('product_inventory/product_varient/' . $url, 'refresh');
    }

    public function hsn_list()
    {

        if (!empty($this->input->post()))
        {
            $columns = array(
                0 => 'itc_hs_codes',
                1 => 'description');
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir   = $this->input->post('order')[0]['dir'];

            $hsn_chapter_id = $this->input->post('hsn_chapter');
            $list_data      = $this->common->hsn_field($hsn_chapter_id);

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
                    $hsn_id = $this->encryption_url->encode($post->hsn_id);

                    $nestedData['itc_hs_codes'] = '<span id="accounting_hsn_code">' . $post->itc_hs_codes . '</span>';
                    $nestedData['description']  = $post->description;

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

    public function sales_add_product_inventory()
    {

// echo "<pre>";
        // print_r($this->input->post());die;

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
        }

    }

}
