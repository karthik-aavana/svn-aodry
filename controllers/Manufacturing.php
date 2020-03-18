<?php

defined('BASEPATH') OR exit('NO direct script access allowed');

class Manufacturing extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
        $this->load->helper('image_upload_helper');
        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
    }

    function index()
    {
 		$product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0  => 'raw_material_code',
                    1  => 'raw_material_hsn_sac_code',
                    2  => 'raw_material_name',
                    3  => 'category_igst',
                    4  => 'category_name',
                    5  => 'raw_material_price',
                    6  => 'raw_material_quantity',
                    7  => 'product_damaged_quantity',
                    8  => 'product_unit',
                    9  => 'addded_user',
                    10 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->raw_materials_varient();
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
                    $raw_material_id                         = $this->encryption_url->encode($post->raw_material_id);
                    $nestedData['added_date']                = $post->added_date;
                    $nestedData['raw_material_code']         = $post->raw_material_code;
                    $nestedData['raw_material_hsn_sac_code'] = "<a href='" . base_url('manufacturing/get_manufacturing_varient/') . $raw_material_id . "''>" . $post->raw_material_hsn_sac_code . "</a>";
                    $nestedData['raw_material_hsn_sac_code'] = $post->raw_material_price;
                    $nestedData['raw_material_name']         = "<a href='" . base_url('manufacturing/get_manufacturing_varient/') . $raw_material_id . "''>" . $post->raw_material_name . "</a>";
                    $nestedData['raw_material_igst']         = $post->raw_material_igst;
                    $nestedData['raw_material_price']        = $post->raw_material_price;
                    $nestedData['raw_material_quantity']     = $post->raw_material_quantity;
                    $nestedData['raw_material_quantity']     = '<a data-toggle="modal" data-target="#quantity_products" class="quantity_change" style="cursor:pointer;" data-pid="' . $raw_material_id . '" data-qty="' . $post->raw_material_quantity . '" title="" >' . $post->raw_material_quantity;
                    '</a>';

                    $nestedData['raw_material_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                            = '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="get_product(' . $post->raw_material_id . ')" data-target="#edit_raw_material">Edit Varient</a>';


                    $cols                 .= '<a data-toggle="modal" data-target="#delete_modal" data-id="' . $raw_material_id . '" data-path="manufacturing//delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Product</a>';
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

            $this->load->view('manufacturing/list', $data);
        }
    }

    function add()
    {

     	$product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        foreach ($modules['modules'] as $key => $value)
        {
            $data['active_modules'][$key] = $value->module_id;
            if ($value->view_privilege == "yes")
            {
                $data['active_view'][$key] = $value->module_id;
            } if ($value->edit_privilege == "yes")
            {
                $data['active_edit'][$key] = $value->module_id;
            } if ($value->delete_privilege == "yes")
            {
                $data['active_delete'][$key] = $value->module_id;
            } if ($value->add_privilege == "yes")
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
        $primary_id               = "raw_material_id";
        $table_name               = "raw_materials";
        $date_field_name          = "added_date";
        $current_date             = date('Y-m-d');
        $data['invoice_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['varients_key']     = $this->general_model->getRecords('*', 'varients', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
        $this->load->view('Manufacturing/add', $data);
    }

    public function add_raw_material()
    {
        // print_r($js_data   = json_decode($this->input->post('table_data'),true));die;
     	$product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);



        $raw_data = array(
                "raw_material_code"           => $this->input->post('ajax_raw_material_code'),
                "raw_material_name"           => $this->input->post('raw_materials_name'),
                "raw_material_hsn_sac_code"   => $this->input->post('raw_material_hsn_sac_code'),
                "raw_material_category_id"    => $this->input->post('raw_material_category'),
                "raw_material_subcategory_id" => $this->input->post('raw_material_subcategory'),
                "raw_material_quantity"       => $this->input->post('raw_material_quantity'),
                "raw_material_price"          => $this->input->post('raw_material_price'),
                "raw_material_tax_id"         => $this->input->post('product_tax'),
                "raw_material_igst"           => $this->input->post('product_igst'),
                "raw_material_cgst"           => $this->input->post('product_cgst'),
                "raw_material_sgst"           => $this->input->post('product_sgst'),
                "added_date"                  => date('Y-m-d'),
                "added_user_id"               => $this->session->userdata('SESS_USER_ID'),
                "branch_id"                   => $this->session->userdata('SESS_BRANCH_ID') );

        $barcode_symbology = $this->input->post('barcode_symbology');
        $branch_id         = $this->session->userdata('SESS_BRANCH_ID');

        if ($raw_material_id = $this->general_model->insertData("raw_materials", $raw_data))
        {


            $raw_material_data = $this->input->post('table_data');
            $js_data           = json_decode($raw_material_data, true);

            foreach ($js_data as $key => $value)
            {

                $value['raw_material_id'] = $raw_material_id;
                $value['added_date']      = date('Y-m-d');
                $value['added_user_id']   = $this->session->userdata('SESS_USER_ID');
                $value['branch_id']       = $this->session->userdata('SESS_BRANCH_ID');



                if ($raw_materials_varients_id = $this->general_model->insertData("raw_materials_varients", $value))
                {


                    $this->general_model->insertData("quantity_history", [
                            'item_id'       => $raw_materials_varients_id,
                            'item_type'     => 'raw_material',
                            'quantity'      => $value['quantity'],
                            'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                            'added_user_id' => $this->session->userdata('SESS_USER_ID') ]);


                    $a[] = $raw_materials_varients_id;

                    //barcode generation

                    $code   = sprintf('%08d', $raw_materials_varients_id);
                    $height = array(
                            '0' => 20,
                            '1' => 30,
                            '2' => 50,
                            '3' => 60 );
                    if (!is_dir('assets/images/barcode/' . $branch_id . '/' . $code))
                    {
                        mkdir('./assets/images/barcode/' . $branch_id . '/' . $code, 0777, TRUE);
                    }
                    for ($i = 0; $i < 4; $i++)
                    {
                        $file = Zend_Barcode::draw($barcode_symbology, 'image', array(
                                        'text'      => $code,
                                        'barHeight' => $height[$i],
                                        'drawText'  => 1,
                                        'factor'    => 1 ), array());

                        $store_image = imagepng($file, "./assets/images/barcode/" . $branch_id . "/{$code}/{$code}" . $height[$i] . ".png");
                    }
                    $barcode_path = "assets/images/barcode/" . $branch_id . "/{$code}/";
                    $barcode_data = array(
                            'barcode'           => $barcode_path,
                            'barcode_symbology' => $barcode_symbology,
                            'barcode_number'    => $code );
                    $this->general_model->updateData('raw_materials_varients', $barcode_data, array(
                            'raw_material_varients_id' => $raw_materials_varients_id ));
                    //barcode ends
                }
                $key_val = json_decode($this->input->post('key_value'), true);
            }
            for ($i = 0; $i < count($key_val); $i++)
            {

                foreach ($key_val[$i] as $k => $v)
                {


                    $varients_key_value = array(
                            'varients_id'              => $k,
                            'varients_value_id'        => $v,
                            'raw_material_varients_id' => $a[$i]
                    );
                    $this->general_model->insertData("raw_materials_varients_value", $varients_key_value);
                }
            }

            redirect("manufacturing/add", 'refresh');
        }
        else
        {
            $this->session->set_flashdata('fail', 'Product can not be Inserted.');
            redirect("manufacturing/add", 'refresh');
        }
    }

    function get_manufacturing_varient($id)
    {

        $product_module_id         = $this->config->item('product_module');
        $data['product_module_id'] = $product_module_id;
        $modules                   = $this->modules;
        $privilege                 = "view_privilege";
        $data['privilege']         = $privilege;
        $section_modules           = $this->get_section_modules($product_module_id, $modules, $privilege);
        $access_common_settings     = $section_modules['access_common_settings'];
        /* presents all the needed */
        $data = array_merge($data, $section_modules);


        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0  => 'manufacturing_code',
                    1  => 'manufacturing_hsn_sac_code',
                    2  => 'manufacturing_name',
                    3  => 'category_igst',
                    4  => 'category_name',
                    5  => 'manufacturing_price',
                    6  => 'manufacturing_quantity',
                    7  => 'manufacturing_damaged_quantity',
                    8  => 'manufacturing_unit',
                    9  => 'manufacturing_user',
                    10 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $id                  = $this->encryption_url->decode($id);
            $list_data           = $this->common->raw_materials_varients_list($id);
            $list_data1          = $this->common->raw_materials_varients_list1($id);
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
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $varients_value = array();
                    foreach ($posts1 as $post1)
                    {
                        if ($post1->raw_material_varients_id == $post->raw_material_varients_id)
                        {
                            $varients_value[] = $post1->varients_value;
                        }
                    }
                    // print_r($varients_value);
                    $varients_values                = implode('/', $varients_value);
                    $varients_value                 = array();
                    $raw_material_id                = $this->encryption_url->encode($post->raw_material_id);
                    $nestedData['quantity']         = "<a onclick='get_quantity(" . $post->raw_material_varients_id . ")' data-toggle='modal' data-target='#quantity_model'>" . $post->q . "</a>";
                    $nestedData['product_code']     = $post->raw_material_name . '/' . $varients_values;
                    $nestedData['varient_name']     = $post->varient_name . '<span id=' . $post->raw_material_varients_id . ' style="display:none">test</span>';
                    $nestedData['product_name']     = $post->varient_code;
                    $nestedData['reordering_point'] = $post->reordering_point;
                    $nestedData['purchase_price']   = $post->purchase_price;
                    $nestedData['product_price']    = $post->raw_material_price;
                    $nestedData['selling_price']    = $post->selling_price;
                    $nestedData['damaged_stock']    = "<a onclick='get_damaged_quantity(" . $post->raw_material_varients_id . ")' data-toggle='modal' data-target='#damages_quantity'>" . $post->damaged_stock . "</a>";
                    $nestedData['product_quantity'] = '<a data-toggle="modal" data-target="#quantity_products" class="quantity_change" style="cursor:pointer;" data-pid="' . $raw_material_id . '" data-qty="' . $post->raw_material_quantity . '" title="" >' . $post->raw_material_quantity;
                    '</a>';

                    $nestedData['varient_unit'] = $post->varient_unit;
                    // $cols                       = '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="get_key_val('.$post->product_inventory_varients_id.')" data-target="#keyValue">Edit Varient</a>';

                    $cols = '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="getVarients(' . $post->raw_material_varients_id . ')" data-target="#productVarient">Edit Product</a>';

                    $cols .= '<a data-toggle="modal" data-target="#delete_modal" data-id="' . $raw_material_id . '" data-path="product/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Product</a>';

                    $cols .= '<a class="delete_button btn btn-xs btn-warning"  data-toggle="modal" onclick="stock(' . $post->raw_material_varients_id . ')" data-target="#stock_management">Move to Damaged</a>';


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
            $data['varients_key'] = $this->general_model->getRecords('*', 'varients', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));
            $data['id']           = $id;
            $data['uqc']          = $this->uqc_call();
            $this->load->view('manufacturing/rawmaterials_varients', $data);
        }
    }

    function update_quantity()
    {

        // print_r( $this->input->post());
        $id      = $this->input->post('qp_id');
        $get_qty = $this->general_model->getRecords('raw_materials_varients.quantity', 'raw_materials_varients', [
                'raw_materials_varients.raw_material_varients_id' => $id ]);


        $quantity         = $this->input->post('quantity_new');
        $reference_number = $this->input->post('reference_number');
        $user_date        = $this->input->post('user_date');
        $new_price        = $this->input->post('new_price');

        $sum_quantity = $quantity + $get_qty[0]->quantity;

        $url   = $this->input->post('url');
        $table = 'raw_materials_varients';
        $where = array(
                'raw_material_varients_id' => $id );

        $this->general_model->updateData($table, [
                'quantity' => $sum_quantity ], $where);
        $quantity_data = array(
                'item_id'          => $id,
                'item_type'        => 'raw_material',
                'reference_number' => $reference_number,
                'quantity'         => $quantity,
                'added_date'       => date('Y-m-d'),
                'new_price'        => $new_price,
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'entry_date'       => $user_date );

        $this->general_model->insertData("quantity_history", $quantity_data);
        redirect('manufacturing/get_manufacturing_varient/' . $url, 'refresh');
    }

    function get_quantity_values($id)
    {

        // $vals = $this->general_model->getRecords('*', 'raw_materials_varients', [
        //                'raw_material_varients_id' => $id]);
        // $vals = $this->general_model->getJoinRecords('raw_materials_varients.*,quantity_history.*', 'raw_materials_varients', [
        //                'raw_materials_varients.raw_material_varients_id' => $id,'quantity_history.item_type' => 'raw_material'], ['quantity_history' => 'quantity_history.item_id = raw_materials_varients.raw_material_varients_id']);

        $v = $this->general_model->getRecords('*', 'quantity_history', [
                'item_id'    => $id,
                'item_type'  => 'raw_material',
                'stock_type' => 'direct' ]);

        // print_r($vals);
        echo "<table class='table-striped' width='100%'>";
        echo "<tr><th>Quantity</th><th>Price</th><th>date</th></tr>";
        foreach ($v as $key)
        {


            echo "<tr><td>" . $key->quantity . "</td><td>" . $key->new_price . "</td><td>" . $key->added_date . "</td></tr>";
        }

        echo "</table>";
    }

    function get_varient_values($id)
    {

        $varients              = $this->general_model->getRecords('*', 'raw_materials_varients', [
                'raw_material_varients_id' => $id,
                'delete_status'            => 0 ]);
        $val['id']             = $varients[0]->raw_material_varients_id;
        $val['code']           = $varients[0]->varient_code;
        $val['name']           = $varients[0]->varient_name;
        $val['selling_price']  = $varients[0]->selling_price;
        $val['purchase_price'] = $varients[0]->purchase_price;
        $val['quantity']       = $varients[0]->quantity;
        $val['varient_unit']   = $varients[0]->varient_unit;


        echo json_encode($val);
    }

    function edit_product_vaarient()
    {

        // print_r($this->input->post());die;

        $table = 'raw_materials_varients';
        $data  = array(
                'varient_name'   => $this->input->post('varient_name'),
                'varient_code'   => $this->input->post('varient_code'),
                'purchase_price' => $this->input->post('purchase_price'),
                'selling_price'  => $this->input->post('selling_price'),
                // 'quantity'       => $this->input->post('quantity'),
                'varient_unit'   => $this->input->post('varient_unit')
        );

        $where = array(
                'raw_material_varients_id' => $this->input->post('id') );

        if ($this->general_model->updateData($table, $data, $where))
        {
            echo "success";
        }
    }

    function move_to_damage()
    {

        // print_r($this->input->post());die;
        $id            = $this->input->post('stock_p_id');
        $damaged_stock = $this->input->post('damaged_stock');
        $url           = $this->input->post('stock_url');

        $values          = $this->general_model->getRecords('*', 'raw_materials_varients', [
                'raw_material_varients_id' => $id ]);
        $existing_damage = $values[0]->damaged_stock;
        $final_damage    = $existing_damage + $damaged_stock;


        $quantity    = $values[0]->quantity;
        $where       = array(
                'raw_material_varients_id' => $id );
        $final_stock = $quantity - $damaged_stock;

        $iasd = $this->general_model->insertData('quantity_history', [
                'item_type'  => 'raw_material',
                'item_id'    => $id,
                'stock_type' => 'damaged',
                'quantity'   => $damaged_stock,
                'added_date' => date('Y-m-d') ]);

        if ($this->general_model->updateData('raw_materials_varients', [
                        'quantity'      => $final_stock,
                        'damaged_stock' => $final_damage ], $where))
        {
            redirect('manufacturing/get_manufacturing_varient/' . $url, 'refresh');
        }
        else
        {
            echo "failed";
        }
    }

    function get_stock($id)
    {

        $values = $this->general_model->getRecords('*', 'raw_materials_varients', [
                'raw_material_varients_id' => $id ]);

        $quantity['quantity'] = $values[0]->quantity;

        echo json_encode($quantity);
    }

    function get_damaged_products($id)
    {

        $values = $this->general_model->getRecords('*', 'raw_materials_varients', [
                'raw_material_varients_id' => $id ]);

        $quantity['damaged_stock'] = $values[0]->damaged_stock;

        echo json_encode($quantity);
    }

    function manufacturing_product()
    {

        $manufacturing_module_id         = $this->config->item('manufacturing_module');
        $data['module_id']               = $manufacturing_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($manufacturing_module_id, $modules, $privilege);
            /* presents all the needed */
        $data              = array_merge($data, $section_modules);

        $data['product_category'] = $this->product_category_call();
        $data['tax']              = $this->tax_call();
        $data['uqc']              = $this->uqc_call();
        $data['chapter']          = $this->chapter_call();
        $data['hsn']              = $this->hsn_call();
        $access_settings          = $data['access_settings'];
        $primary_id               = "manufacturing_order_id";
        $table_name               = "manufacturing_order";
        $date_field_name          = "added_date";
        $current_date             = date('Y-m-d');
        $data['invoice_number']   = $this->generate_invoice_number($access_settings, $primary_id, $table_name, $date_field_name, $current_date);
        $data['varients_key']     = $this->general_model->getRecords('*', 'varients', array(
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $list_data  = $this->common->raw_materials_varients_list_all();
        $list_data1 = $this->common->raw_materials_varients_list_all1();
        $posts      = $this->general_model->getPageJoinRecords($list_data);
        $posts1     = $this->general_model->getPageJoinRecords($list_data1);

        $p_list_data  = $this->common->all_products_varients_list();
        $p_list_data1 = $this->common->all_products_varients_list1();
        $p_posts      = $this->general_model->getPageJoinRecords($p_list_data);
        $p_posts1     = $this->general_model->getPageJoinRecords($p_list_data1);

        // foreach ($posts as $post)
        // {
        //     $varients_value = array();
        //     foreach ($posts1 as $post1)
        //     {
        //         if ($post1->raw_material_varients_id == $post->raw_material_varients_id)
        //         {
        //             $varients_value[] = $post1->varients_value;
        //         }
        //     }
        //     $varients_values   = implode('/', $varients_value);
        //     $varients_value    = array();
        //     $final_val[]       = $post->raw_material_name . '/' . $varients_values;
        //     $data['final_val'] = $final_val;
        // }
        // die;
        foreach ($p_posts as $post)
        {
            $varients_value = array();
            foreach ($p_posts1 as $post1)
            {
                if ($post1->product_inventory_id == $post->product_inventory_id)
                {
                    $varients_value[] = $post1->varients_value;
                }
            }
            $varients_values           = implode('/', $varients_value);
            $varients_value            = array();
            $final_val[]               = $post->product_name . '/' . $varients_values;
            $data['product_final_val'] = $final_val;
        }

        // $data['raw_materials'] = $this->general_model->getRecords('*','product_inventory_varients',['delete_status'=>0,]);

        $data['raw_materials'] = $this->general_model->getJoinRecords('*', 'product_inventory_varients', [
                'product_inventory.delete_status' => 0,
                'product_inventory.type'          => 'raw-material' ], [
                'product_inventory' => 'product_inventory.product_inventory_id =product_inventory_varients.product_inventory_id' ]);

        $data['varient_code'] = $this->general_model->getRecords('*', 'product_inventory_varients', [
                'delete_status' => 0 ]);


        $data['labour']    = $this->general_model->getJoinRecords('l.*,c.*', 'labour l', [
                'l.delete_status' => 0 ], [
                'labour_classification c' => 'l.classification_id = c.labour_classification_id' ]
        );
        $data['over_head'] = $this->general_model->getRecords('oh.*', 'over_head oh', [
                'oh.delete_status' => 0 ]);

        $this->load->view('manufacturing/manufacturing_product', $data, $final_val);
    }

    function add_manufacturing_product()
    {

        print_r($this->input->post());
    }

    function add_classification()
    {

        $this->input->post('classification_name');

        $arr = array(
                'labour_classification_name' => $this->input->post('classification_name'),
                'added_date'                 => date('d-m-y'),
                'added_user_id'              => $this->session->userdata('SESS_USER_ID'),
                'branch_id'                  => $this->session->userdata('SESS_BRANCH_ID')
        );

        if ($id = $this->general_model->insertData('labour_classification', $arr))
        {

            // $data['data'] = $this->general_model->getRecords('count(*) as c','labour_classification');

            $data['data'] = $this->general_model->getRecords('*', 'labour_classification', array(
                    'delete_status' => 0,
                    'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

            $data['labour_classification_id'] = $id;
            echo json_encode($data);
        }
    }

    function add_labour_data()
    {


        $arr = array(
                'activity_name'     => $this->input->post(),
                'classification_id' => $this->input->post(),
                'no_of_labour'      => $this->input->post(),
                'no_of_hours'       => $this->input->post(),
                'no_of_days'        => $this->input->post(),
                'total_no_hours'    => $this->input->post(),
                'cost_per_hour'     => $this->input->post(),
                'added_date'        => $this->input->post(),
                'added_user_id'     => $this->session->userdata('SESS_USER_ID'),
                'branch_id'         => $this->session->userdata('SESS_USER_ID')
        );

        if ($this->general_model->insertData('labour_classification', $arr))
        {

        }
    }

    function add_manufacturing_inventory()
    {
        if ($this->input->post('hasVarients') == "checked")
        {


            $product_data      = array(
                    "product_code"           => $this->input->post('ajax_product_code'),
                    "product_name"           => $this->input->post('product_name'),
                    "product_hsn_sac_code"   => $this->input->post('product_hsn_sac_code'),
                    "product_category_id"    => $this->input->post('product_category'),
                    "product_subcategory_id" => $this->input->post('product_subcategory'),
                    "product_quantity"       => $this->input->post('product_quantity'),
                    "product_price"          => $this->input->post('product_price'),
                    "product_tax_id"         => $this->input->post('product_tax'),
                    "product_igst"           => $this->input->post('product_igst'),
                    "product_cgst"           => $this->input->post('product_cgst'),
                    "product_sgst"           => $this->input->post('product_sgst'),
                    "type"                   => 'manufacturing',
                    "added_date"             => date('Y-m-d'),
                    "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"              => $this->session->userdata('SESS_BRANCH_ID') );
            $barcode_symbology = $this->input->post('barcode_symbology');
            $branch_id         = $this->session->userdata('SESS_BRANCH_ID');

            // print_r($product_data);die;
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
                                'added_user_id' => $this->session->userdata('SESS_USER_ID') ]);


                        $a[] = $product_inventory_varients_id;

                        //barcode generation

                        $code   = sprintf('%08d', $product_inventory_varients_id);
                        $height = array(
                                '0' => 20,
                                '1' => 30,
                                '2' => 50,
                                '3' => 60 );
                        if (!is_dir('assets/images/barcode/' . $branch_id . '/' . $code))
                        {
                            mkdir('./assets/images/barcode/' . $branch_id . '/' . $code, 0777, TRUE);
                        }
                        for ($i = 0; $i < 4; $i++)
                        {
                            $file = Zend_Barcode::draw($barcode_symbology, 'image', array(
                                            'text'      => $code,
                                            'barHeight' => $height[$i],
                                            'drawText'  => 1,
                                            'factor'    => 1 ), array());

                            $store_image = imagepng($file, "./assets/images/barcode/" . $branch_id . "/{$code}/{$code}" . $height[$i] . ".png");
                        }
                        $barcode_path = "assets/images/barcode/" . $branch_id . "/{$code}/";
                        $barcode_data = array(
                                'barcode'           => $barcode_path,
                                'barcode_symbology' => $barcode_symbology,
                                'barcode_number'    => $code );
                        $this->general_model->updateData('product_inventory_varients', $barcode_data, array(
                                'product_inventory_varients_id' => $product_inventory_varients_id ));
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

                // redirect("product/varient_list", 'refresh');
            }
        }
        else
        {
            $product_data                  = array(
                    "product_code"           => $this->input->post('ajax_product_code'),
                    "product_name"           => $this->input->post('product_name'),
                    "product_hsn_sac_code"   => $this->input->post('product_hsn_sac_code'),
                    "product_category_id"    => $this->input->post('product_category'),
                    "product_subcategory_id" => $this->input->post('product_subcategory'),
                    "product_quantity"       => $this->input->post('product_quantity'),
                    "product_price"          => $this->input->post('product_price'),
                    "product_tax_id"         => $this->input->post('product_tax'),
                    "product_igst"           => $this->input->post('product_igst'),
                    "product_cgst"           => $this->input->post('product_cgst'),
                    "product_sgst"           => $this->input->post('product_sgst'),
                    "type"                   => 'manufacturing',
                    "added_date"             => date('Y-m-d'),
                    "added_user_id"          => $this->session->userdata('SESS_USER_ID'),
                    "branch_id"              => $this->session->userdata('SESS_BRANCH_ID') );
            $product_inventory_id          = $this->general_model->insertData("product_inventory", $product_data);
            $value['product_inventory_id'] = $product_inventory_id;
            $value['added_date']           = date('Y-m-d');
            $value['added_user_id']        = $this->session->userdata('SESS_USER_ID');
            $value['branch_id']            = $this->session->userdata('SESS_BRANCH_ID');
            $product_inventory_varients_id = $this->general_model->insertData("product_inventory_varients", $value);
        }
    }

    function test()
    {

        $p_list_data  = $this->general_model->getRecords('*', 'product_inventory_varients', [
                'delete_status' => 0 ]);
        echo "<pre>";
        print_r($p_list_data);
        $p_list_data1 = $this->common->products_varients_list1();
        $p_posts1     = $this->general_model->getPageJoinRecords($p_list_data1);
        print_r($p_posts1);
        die;
        foreach ($p_list_data as $post)
        {
            $varients_value = array();
            foreach ($p_list_data as $post1)
            {
                if ($post1->product_inventory_id == $post->product_inventory_id)
                {
                    $varients_value[] = $post1->varients_value;
                }
            }
            $varients_values           = implode('/', $varients_value);
            $varients_value            = array();
            $final_val[]               = $post->product_name . '/' . $varients_values;
            $data['product_final_val'] = $final_val;
        }
        echo "<pre>";
        print_r($final_val);
    }

    function get_raw_materials_price()
    {

        $id = $this->input->post('id');

        $res = $this->general_model->getRecords("*", 'product_inventory_varients', [
                'product_inventory_varients_id' => $id ]);
        echo json_encode($res);
    }

    function manufacturing_post()
    {
        $manufacturing_order_array = [
                'manufacturing_order_code'     => $this->input->post('ajax_product_code'),
                'product_id'                   => $this->input->post('product_name_select'),
                'manufacturing_order_quantity' => $this->input->post('quantity'),
                'added_date'                   => date('Y-m-d'),
                'updated_date'                 => date('Y-m-d'),
                'branch_id'                    => $this->session->userdata("SESS_BRANCH_ID"),
                'added_user_id'                => $this->session->userdata("SESS_USER_ID")
        ];

        $manufacturing_id = $this->general_model->insertData('manufacturing_order', $manufacturing_order_array);


        $over_head       = json_decode($this->input->post('o_h_quantity'), true);
        // print_r($over_head);die;
        $over_head_count = count($over_head);

        for ($i = 0; $i < $over_head_count; $i++)
        {

            $over_head[$i]['manufacturing_order_id'] = $manufacturing_id;
            $this->general_model->insertData('over_head_reference', $over_head[$i]);
        }

        $labour_data       = json_decode($this->input->post('q_values'), true);
        $labour_data_count = count($labour_data);

        for ($i = 0; $i < $labour_data_count; $i++)
        {

            $labour_data[$i]['manufacturing_order_id'] = $manufacturing_id;
            $this->general_model->insertData('labour_reference', $labour_data[$i]);
        }


        $r_values = json_decode($this->input->post('r_values'), true);

        $r_values_count = count($r_values);

        for ($i = 0; $i < $r_values_count; $i++)
        {

            $r_values[$i]['manufacturing_order_id'] = $manufacturing_id;
            $this->general_model->insertData('raw_material_reference', $r_values[$i]);
        }

        redirect('manufacturing/list1', 'refresh');
    }

    function list1()
    {
        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($labour_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);


        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'manufacturing_order_code',
                    1 => 'varient_code',
                    2 => 'manufacturing_order_quantity',
                    3 => 'manufacturing_order_stock',
                    4 => 'added_date',
                    5 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->manufacturing_order_list();
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
                    $manufacturing_id                           = $this->encryption_url->encode($post->manufacturing_order_id);
                    $nestedData['added_date']                   = $post->added_date;
                    $nestedData['manufacturing_order_code']     = $post->manufacturing_order_code;
                    $nestedData['activity_name']                = 'test';
                    $nestedData['varient_code']                 = $post->varient_code;
                    $nestedData['manufacturing_order_quantity'] = $post->manufacturing_order_quantity;
                    $nestedData['added_user']                   = $post->first_name . ' ' . $post->last_name;
                    $nestedData['manufacturing_order_stock']    = $post->manufacturing_order_stock;
                    $cols                                       = ' <a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onClick ="get_product_stock(' . $post->manufacturing_order_id . ')" data-target="#quantity_model"><span class="glyphicon glyphicon-star-empty"></span> Add Stock</a>';
                    $cols                                       .= '<a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onclick="getRawMaterials(' . $post->manufacturing_order_id . ')" data-target="#raw_material_availability"><span class="glyphicon glyphicon-star-empty"></span>Raw materials</a>';
                    $cols                                       .= '<a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onclick="getLabour(' . $post->manufacturing_order_id . ')" data-target="#labour_availability"><span class="glyphicon glyphicon-star-empty"></span>Labours</a>';
                    $cols                                       .= '<a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onclick="getOverHead(' . $post->manufacturing_order_id . ')" data-target="#over_head_availability"><span class="glyphicon glyphicon-star-empty"></span>Over Head</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#make" data-id="' . $manufacturing_id . '" data-path="labour/delete" onclick="make(' . $post->manufacturing_order_id . ')" title="Make" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-star-empty"></span>Make</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#delete_modal" data-id="' . $manufacturing_id . '" data-path="labour/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span>Delete</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#stock_movement" data-id="' . $manufacturing_id . '" data-path="labour/delete" onclick="stockMovement(' . $post->manufacturing_order_id . ')" title="Make" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-star-empty"></span>Stock movement</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#stock_history" data-id="' . $manufacturing_id . '" data-path="labour/delete" onclick="shl(' . $post->manufacturing_order_id . ')" title="Make" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-star-empty"></span>Stock History</a>';

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


          $data['move_to_branch'] = $this->general_model->getJoinRecords('b.branch_id,b.branch_name','branch b',['b.delete_status'=>0],['firm f' => 'f.firm_id = b.firm_id']);

            $this->load->view('manufacturing/list1', $data);
        }
    }

    function get_stocks($id)
    {


        $v = $this->general_model->getRecords('*', 'quantity_history', [
                'item_id'    => $id,
                'item_type'  => 'manufacturing_order',
                'stock_type' => 'direct' ]);

        // print_r($vals);
        echo "<table class='table-striped' width='100%'>";
        echo "<tr><th>Quantity</th><th>Price</th><th>date</th></tr>";
        foreach ($v as $key)
        {


            echo "<tr><td>" . $key->quantity . "</td><td>" . $key->new_price . "</td><td>" . $key->added_date . "</td></tr>";
        }

        echo "</table>";
    }

    function add_stock()
    {
        $id      = $this->input->post('qp_id');
        $get_qty = $this->general_model->getRecords('manufacturing_order.manufacturing_order_stock', 'manufacturing_order', [
                'manufacturing_order.manufacturing_order_id' => $id ]);


        $quantity         = $this->input->post('quantity_new');
        $reference_number = $this->input->post('reference_number');
        $user_date        = $this->input->post('user_date');
        $new_price        = $this->input->post('new_price');

        $sum_quantity = $quantity + $get_qty[0]->manufacturing_order_stock;

        $url   = $this->input->post('url');
        $table = 'manufacturing_order';
        $where = array(
                'manufacturing_order_id' => $id );

        $this->general_model->updateData($table, [
                'manufacturing_order_stock' => $sum_quantity ], $where);
        $quantity_data = array(
                'item_id'          => $id,
                'item_type'        => 'manufacturing_order',
                'reference_number' => $reference_number,
                'quantity'         => $quantity,
                'added_date'       => date('Y-m-d'),
                'new_price'        => $new_price,
                'branch_id'        => $this->session->userdata('SESS_BRANCH_ID'),
                'added_user_id'    => $this->session->userdata('SESS_USER_ID'),
                'entry_date'       => $user_date );

        $this->general_model->insertData("quantity_history", $quantity_data);
        redirect('manufacturing/list1');
    }

    function get_raw_materials($id)
    {

        $table  = "raw_material_reference rm";
        $string = "rm.quantity,rm.price,rm.cost,rv.quantity as stock,rv.varient_code as r_name";
        $join   = [
                'manufacturing_order mo'    => 'mo.manufacturing_order_id = rm.raw_material_reference_id',
                'product_inventory_varients rv' => 'rm.raw_material_varient_id = rv.product_inventory_varients_id'
        ];
        $where  = [
                'rm.manufacturing_order_id' => $id ];
        $val    = $this->general_model->getJoinRecords($string, $table, $where, $join);

        echo "<table class='table-striped'  width='100%'>";
        echo "<tr><th>Name</th><th>Quantity Required</th><th>Price</th><th>Instock</th></tr>";
        foreach ($val as $key)
        {
            echo "<tr><td>" . $key->r_name . "</td><td>" . $key->quantity . "</td><td>" . $key->price . "</td><td>" . $key->stock . "</td></tr>";
        }
        echo "</table>";
    }

    function make($id)
    {
        $table  = "labour_reference lr";
        $string = "lr.no_of_labour,lr.total_no_hours,lr.cost_per_hour,l.activity_name";
        $join   = [
                'labour l' => 'l.labour_id = lr.labour_id'
        ];
        $where  = [
                'lr.labour_reference_id' => $id ];
        $val    = $this->general_model->getJoinRecords($string, $table, $where, $join);

        $table1  = "raw_material_reference rm";
        $string1 = "rm.quantity,rm.price,rm.cost,rv.product_inventory_varients_id,rv.quantity as stock,rv.varient_code as r_name";
        $join1   = [
                'manufacturing_order mo'    => 'mo.manufacturing_order_id = rm.raw_material_reference_id',
                'product_inventory_varients rv' => 'rm.raw_material_varient_id = rv.product_inventory_varients_id'
        ];
        $where1  = [
                'rm.manufacturing_order_id' => $id ];
        $val1    = $this->general_model->getJoinRecords($string1, $table1, $where1, $join1);

        $table2  = "over_head_reference ohr";
        $string2 = "ohr.quantity,ohr.price,ohr.cost,oh.over_head_name";
        $join2   = [
                'over_head oh' => 'oh.over_head_id = ohr.over_head_id'
        ];
        $where2  = [
                'ohr.over_head_reference_id' => $id ];
        $val2    = $this->general_model->getJoinRecords($string2, $table2, $where2, $join2);

        echo "<h4>Over Head</h4>";
        echo "<table class='table-striped'  width='100%'>";
        echo "<tr><th>Name</th><th>Quantity </th><th>Cost Per Unit</th><th>Total Cost</th></tr>";
        foreach ($val2 as $key2)
        {
            echo "<tr><td>" . $key2->over_head_name . "</td><td>" . $key2->quantity . "</td><td>" . $key2->price . "</td><td>" . $key2->cost . "</td></tr>";
        }
        echo "</table>";
        echo "<h4>Raw Materials</h4>";
        echo "<table class='table-striped'  width='100%'>";
        echo "<tr><th>Name</th><th>Quantity Required</th><th>Price</th><th>Instock</th><th>Action</th></tr>";
        foreach ($val1 as $key1)
        {

            if ($key1->quantity >= $key1->stock)
            {
                $check[]        = 'present';
                $purchase_order = "<a href=" . base_url('purchase_order/add') . ">Purchase</a>";
            }
            else
            {
                $check[]        = 'notpresent';
                $purchase_order = "Available";
            }

            echo "<tr><td>" . $key1->r_name . "</td><td>" . $key1->quantity . "</td><td>" . $key1->price . "</td><td>" . $key1->stock . "</td><td>$purchase_order</td></tr>";
        }
        echo "</table>";
        echo "<h4>Labour</h4>";
        echo "<table class='table-striped'  width='100%'>";
        echo "<tr><th>No of labour </th><th>total no hours Required</th><th>cost per hour</th><th>activity_name</th></tr>";
        foreach ($val as $key)
        {
            echo "<tr><td>" . $key->no_of_labour . "</td><td>" . $key->total_no_hours . "</td><td>" . $key->cost_per_hour . "</td><td>" . $key->activity_name . "</td></tr>";
        }
        echo "</table>";
        if (isset($check))
        {
            if (!in_array('present', $check))
            {
              echo "<form method='post' action =".base_url('manufacturing/add_make').">";
              echo "<input type='hidden' name = 'id' value= '".$id."'>";
                echo "<button type='submit'>Make</button>";
                echo "</form>";
            }
        }
    }

    function get_stock_($id){


      $stock = $this->general_model->getRecords('manufacturing_order_stock','manufacturing_order',['delete_status' => 0,'manufacturing_order_id' => $id]);

      return $stock[0]->manufacturing_order_stock;

    }

    function get_stock_history($id){

      $table  = "stock_movement sm";
        $string = "sm.quantity,sm.reference_no,sm.reason,b.branch_name";
        $join   = [
                'branch b' => 'b.branch_id = sm.to_branch_id',
        ];
        $where  = [
                'sm.manufacturing_order_id' => $id ];

      $history = $this->general_model->getJoinRecords($string,$table,$where,$join);

      echo json_encode($history) ;
    }

      function stock_value($id){ 

      echo $this->get_stock_($id);
      }


    function move_stock_to(){

      $stock_movement =['to_branch_id' => $this->input->post('to_branch'),
                        'from_branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
                        'reference_no'=> $this->input->post('stock_mo_id'),
                        'manufacturing_order_id'=> $this->input->post('stock_mo_id'),
                        'reason'=> $this->input->post('reason'),
                        'added_user_id'=> $this->session->userdata('SESS_USER_ID'),
                        'added_date'=> date('y-m-d'),
                        'quantity' => $this->input->post('stock_to_move'),
                        'branch_id' => $this->session->userdata('SESS_BRANCH_ID')
                        ];

          $stock_to_move = $this->input->post('stock_to_move');
          $stock_mo_id = $this->input->post('stock_mo_id');

        $stock = $this->get_stock_($stock_mo_id);


         $final_stock = ($stock - $stock_to_move);

         $this->general_model->insertData('stock_movement',$stock_movement);

         $this->general_model->updateData('manufacturing_order',['manufacturing_order_stock' =>$final_stock],['manufacturing_order_id' => $stock_mo_id]);

         redirect("manufacturing/list1", 'refresh');

    }

    function get_stock_history_listing($id1){

        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($labour_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);


        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'manufacturing_order_code',
                    1 => 'varient_code',
                    2 => 'manufacturing_order_quantity',
                    3 => 'manufacturing_order_stock',
                    4 => 'added_date',
                    5 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->manufacturing_order_history_list($id1);
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
                    // $manufacturing_id                           = $this->encryption_url->encode($post->manufacturing_order_id);
                    $nestedData['quantity']                   = $post->quantity;
                    $nestedData['added_date']                   = $post->added_date;
                    $nestedData['reference_no']     = $post->reference_no;
                    $nestedData['activity_name']                = $post->branch_name;
                    $nestedData['reason']                 = $post->reason;
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
          $data['move_to_branch'] = $this->general_model->getJoinRecords('b.branch_id,b.branch_name','branch b',['b.delete_status'=>0],['firm f' => 'f.firm_id = b.firm_id']);
            $this->load->view('manufacturing/list1', $data);
    }
}

          function add_make(){

        $id = $this->input->post('id');

        $process_values = $this->general_model->getRecords('manufacturing_order_code,product_id,manufacturing_order_quantity','manufacturing_order',['delete_status' => 0,'manufacturing_order_id' => $id]);

          $m_array=['status' => 'Yet to begin','manufacturing_order_id'=>$id,'manufacturing_order_code' => $process_values[0]->manufacturing_order_code,'product_id' => $process_values[0]->product_id, 'added_date' =>date('Y-m-d'),'added_user_id' =>$this->session->userdata('SESS_USER_ID'),'branch_id'=>$this->session->userdata('SESS_BRANCH_ID'),'quantity' =>$process_values[0]->manufacturing_order_quantity];

         $m_process_id = $this->general_model->insertData('manufacturing_process',$m_array);


        $table  = "labour_reference lr";
        $string = "lr.*";
       
        $where  = [
                'lr.labour_reference_id' => $id ];
        $labour    = $this->general_model->getRecords($string, $table, $where);
        
        foreach ($labour as $labourkey) {
            
            $this->general_model->insertData('labour_process_reference',[

            'manufacturing_process_id' => $m_process_id,
            'labour_id' => $labourkey->labour_id,
            'no_of_labour' => $labourkey->no_of_labour,
            'total_no_hours' => $labourkey->total_no_hours,
            'cost_per_hour' => $labourkey->cost_per_hour,
            'total_cost' => $labourkey->total_cost,
            'added_date' => date('y-m-d'),
            'added_user_id' => $this->session->userdata('SESS_USER_ID'),
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID'),
            ]);
        }

        $table1  = "raw_material_reference rm";
        $string1 = "rm.*";
        $where1  = [
                'rm.manufacturing_order_id' => $id ];
        $raw_materials    = $this->general_model->getRecords($string1, $table1, $where1);

        foreach ($raw_materials as $raw_materialskey ) { 
           
             $this->general_model->insertData('raw_material_process_reference',[
            'manufacturing_process_id' => $m_process_id,
            'raw_material_varient_id' => $raw_materialskey->raw_material_varient_id,
            'quantity' => $raw_materialskey->quantity,
            'price' => $raw_materialskey->price,
            'cost' => $raw_materialskey->cost,
            'added_date' => date('y-m-d'),
            'added_user_id' => $this->session->userdata('SESS_USER_ID'),
            'branch_id' => $this->session->userdata('SESS_BRANCH_ID')]);

        }

         $table2  = "over_head_reference ohr";
        $string2 = "ohr.*";

        $where2  = [
                'ohr.over_head_reference_id' => $id ];
        $over_head    = $this->general_model->getRecords($string2, $table2, $where2);

         foreach ($over_head as $over_headkey ) { 
            
            $o_arr = ['manufacturing_process_id' => $m_process_id,
            'over_head_id' => $over_headkey->over_head_id,
            'unit' => $over_headkey->unit,
            'quantity' => $over_headkey->quantity,
            'price' => $over_headkey->price,
            'cost' => $over_headkey->cost,
            'added_date' => date('y-m-d'),
            'added_user_id' =>$this->session->userdata('SESS_USER_ID'),
            'updated_user_id' =>$this->session->userdata('SESS_BRANCH_ID')];
             $this->general_model->insertData('over_head_process_reference',$o_arr);
        }
         redirect("manufacturing/manufacturing_process_list", 'refresh');

            
          }

          function manufacturing_process_list(){

        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($labour_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);


        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'added_date',
                    1 => 'manufacturing_order_code',
                    2 => 'added_user',
                    3 => 'status',
                    4 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->manufacturing_process_list();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getPageJoinRecordsCount($list_data);
            // print_r($list_data );die;
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
                    $manufacturing_id                           = $this->encryption_url->encode($post->manufacturing_process_id);
                    $nestedData['added_date']                   = $post->added_date;
                    $nestedData['manufacturing_order_code']     = $post->manufacturing_order_code;

                    $nestedData['added_user']                   = $post->first_name . ' ' . $post->last_name;
                    $nestedData['varient_code']    = "<a href ='".base_url('manufacturing/details/'.$post->manufacturing_process_id)."'>".$post->varient_code;
                    $nestedData['status']    = "<span data-id =".$post->manufacturing_process_id." class='staus_change'>".$post->status."</span>";
                    $cols                                       = ' <a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onClick ="get_product_stock(' . $post->manufacturing_process_id . ')" data-target="#quantity_model"><span class="glyphicon glyphicon-star-empty"></span> Add Stock</a>';
                    $cols                                       .= '<a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onclick="getRawMaterials(' . $post->manufacturing_process_id . ')" data-target="#raw_material_availability"><span class="glyphicon glyphicon-star-empty"></span>Raw materials</a>';
                    $cols                                       .= '<a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onclick="getLabour(' . $post->manufacturing_process_id . ')" data-target="#labour_availability"><span class="glyphicon glyphicon-star-empty"></span>Labours</a>';
                    $cols                                       .= '<a class="delete_button btn btn-xs btn-danger" data-toggle="modal" onclick="getOverHead(' . $post->manufacturing_process_id . ')" data-target="#over_head_availability"><span class="glyphicon glyphicon-star-empty"></span>Over Head</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#make" data-id="' . $manufacturing_id . '" data-path="labour/delete" onclick="make(' . $post->manufacturing_process_id . ')" title="Make" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-star-empty"></span>Make</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#delete_modal" data-id="' . $manufacturing_id . '" data-path="labour/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span>Delete</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#stock_movement" data-id="' . $manufacturing_id . '" data-path="labour/delete" onclick="stockMovement(' . $post->manufacturing_process_id . ')" title="Make" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-star-empty"></span>Stock movement</a>';
                    $cols                                       .= '<a data-toggle="modal" data-target="#stock_history" data-id="' . $manufacturing_id . '" data-path="labour/delete" onclick="shl(' . $post->manufacturing_process_id . ')" title="Make" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-star-empty"></span>Stock History</a>';

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

            $this->load->view('manufacturing/manufacturing_process_list', $data);
        }

          }

          function change_status(){


            $m_id = $this->input->post('m_id');
            $status = $this->input->post('status');

            switch ($status) {
              case 'Yet to begin':
                $this->general_model->updateData('manufacturing_process',['status'=>'started'],['manufacturing_process_id' => $m_id]);
                echo "started";
                break;
              case 'started':
                 $this->general_model->updateData('manufacturing_process',['status'=>'wip'],['manufacturing_process_id' => $m_id]);
                echo "wip";
                break;
              case 'wip':
                $this->general_model->updateData('manufacturing_process',['status'=>'finished'],['manufacturing_process_id' => $m_id]);
                echo "finished";
                break;
              case 'finished':
                $this->general_model->updateData('manufacturing_process',['status'=>'blocked'],['manufacturing_process_id' => $m_id]);
                echo "blocked";
                break;
              case 'blocked':
               $this->general_model->updateData('manufacturing_process',['status'=>'Yet to begin'],['manufacturing_process_id' => $m_id]);
                echo "Yet to begin";
                break; 
            }
          }
          function details($id){

            // $get_process_id = $this->general_model->

        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules            = $this->get_section_modules($labour_module_id, $modules, $privilege);
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $table  = "labour_process_reference lr";
        $string = "lr.no_of_labour,lr.total_no_hours,lr.cost_per_hour,l.activity_name,lr.total_cost";
        $join   = [
                'labour l' => 'l.labour_id = lr.labour_id'
        ];
        $where  = [
                'lr.manufacturing_process_id' => $id ];
        $data['labour']    = $this->general_model->getJoinRecords($string, $table, $where, $join);

// raw materials
        $table1  = "raw_material_process_reference rm";
        $string1 = "rm.quantity,rm.price,rm.cost,rv.product_inventory_varients_id,rv.quantity as stock,rv.varient_code as r_name";
        $join1   = [
               
                'product_inventory_varients rv' => 'rm.raw_material_varient_id = rv.product_inventory_varients_id'
        ];
        $where1  = [
                'rm.manufacturing_process_id' => $id ];
        $data['raw_materials']    = $this->general_model->getJoinRecords($string1, $table1, $where1, $join1);



        $table2  = "over_head_process_reference ohr";
        $string2 = "ohr.quantity,ohr.price,ohr.cost,oh.over_head_name";
        $join2   = [
                'over_head oh' => 'oh.over_head_id = ohr.over_head_id'
        ];
        $where2  = [
                'ohr.manufacturing_process_id' => $id ];
        $data['over_head']    = $this->general_model->getJoinRecords($string2, $table2, $where2, $join2);
        $data['over_head_select'] = $this->general_model->getRecords('oh.*', 'over_head oh', [
                'oh.delete_status' => 0 ]);

        $data['manufacturing_process'] = $this->general_model->getJoinRecords('mp.*,pv.product_inventory_varients_id,pv.varient_code','manufacturing_process mp',['mp.manufacturing_process_id' => $id],['product_inventory_varients pv' => 'pv.product_inventory_varients_id = mp.product_id']);
        $data['scrap'] = $this->general_model->getRecords('scrap.*','scrap',['manufacturing_process_id' => $id]);
        $data['raw_materials_select'] = $this->general_model->getJoinRecords('*', 'product_inventory_varients', [
                'product_inventory.delete_status' => 0,
                'product_inventory.type'          => 'raw-material' ], [
                'product_inventory' => 'product_inventory.product_inventory_id =product_inventory_varients.product_inventory_id' ]);

        $data['labour1']    = $this->general_model->getJoinRecords('l.*,c.*', 'labour l', [
                'l.delete_status' => 0 ], [
                'labour_classification c' => 'l.classification_id = c.labour_classification_id' ]
        );

        $data['scrap_sum'] = $this->general_model->getRecords('sum(price) as price','scrap',['manufacturing_process_id' => $id]);
        $data['labour_sum'] = $this->general_model->getRecords('sum(total_cost) as price','labour_process_reference',['manufacturing_process_id' => $id]);
        $data['over_head_sum'] = $this->general_model->getRecords('sum(cost) as price','over_head_process_reference',['manufacturing_process_id' => $id]);
        $data['raw_material_sum'] = $this->general_model->getRecords('sum(cost) as price','raw_material_process_reference',['manufacturing_process_id' => $id]);

        $data['process_id'] = $id;
          $this->load->view('manufacturing/details',$data);

          }

          function add_over_heads_to_process(){

             $over_head       = json_decode($this->input->post('o_h_quantity'), true);
        // print_r($over_head);die;
        $over_head_count = count($over_head);
        $manufacturing_id = $this->input->post('process_id');
        // $manufacturing_id = $this->input->post('process_id');

        for ($i = 0; $i < $over_head_count; $i++)
        {
             // $over_head[$i]['product_id'] = $product_id;
           $over_head[$i]['manufacturing_process_id'] = $manufacturing_id;
            $this->general_model->insertData('over_head_process_reference', $over_head[$i]);
        }
        redirect("manufacturing/details/$manufacturing_id", 'refresh');

          }

          function add_raw_materials_to_process(){

             $r_values = json_decode($this->input->post('r_values'), true);

        $r_values_count = count($r_values);
        $manufacturing_id = $this->input->post('process_id');

        for ($i = 0; $i < $r_values_count; $i++)
        {

            $r_values[$i]['manufacturing_process_id'] = $manufacturing_id;
            $this->general_model->insertData('raw_material_process_reference', $r_values[$i]);
        }

        redirect("manufacturing/details/$manufacturing_id", 'refresh');
          }

          function add_labour_to_process(){

             $manufacturing_id = $this->input->post('process_id');
            $labour_data       = json_decode($this->input->post('q_values'), true);
        $labour_data_count = count($labour_data);

        for ($i = 0; $i < $labour_data_count; $i++)
        {

            $labour_data[$i]['manufacturing_process_id'] = $manufacturing_id;
            $this->general_model->insertData('labour_process_reference', $labour_data[$i]);
        }
         redirect("manufacturing/details/$manufacturing_id", 'refresh');
          }


          function add_scrap(){

            $mp = $this->input->post('process_id');

            $arr = [
                'type' => $this->input->post('scrap_type'),
                'reason' => $this->input->post('scrap_reason'),
                'price' => $this->input->post('scrap_price'),
                'quantity' => $this->input->post('scrap_quantity'),
                'manufacturing_process_id' => $this->input->post('process_id'),
                'reference_no' => $this->input->post('scrap_reference_no')
            ];

            $this->general_model->insertData('scrap',$arr);

            redirect("manufacturing/details/$mp",'refresh');

          }

}

