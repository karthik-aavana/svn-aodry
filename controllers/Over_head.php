<?php

defined('BASEPATH') OR exit('NO direct script access allowed');

class Over_head extends MY_Controller
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
        $over_head_module_id             = $this->config->item('over_head_module');
        $data['module_id']               = $over_head_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($over_head_module_id, $modules, $privilege);
       
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'over_head_name',
                    1 => 'over_head_unit',
                    2 => 'over_head_cost_per_unit',
                    3 => 'quantity',
                    4 => 'added_user',
                    5 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->over_head_list();
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
                    $over_head_id                          = $this->encryption_url->encode($post->over_head_id);
                    $nestedData['added_date']              = $post->added_date;
                    $nestedData['over_head_unit']          = $post->over_head_unit;
                    $nestedData['over_head_name']          = $post->over_head_name;
                    $nestedData['over_head_cost_per_unit'] = $post->over_head_cost_per_unit;
                    $nestedData['quantity']                = $post->quantity;


                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = ' <a href="' . base_url('over_head/edit/') . $over_head_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>';

                    $cols                 .= ' <a data-toggle="modal" data-target="#delete_modal" data-id="' . $over_head_id . '" data-path="over_head/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
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

            $this->load->view('over_head/list', $data);
        }
    }

    function add()
    {
        $over_head_module_id             = $this->config->item('over_head_module');
        $data['module_id']               = $over_head_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($over_head_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['uqc'] = $this->general_model->getRecords('*', 'uqc',
                                                        array(
                        'delete_status' => 0 ));

        $this->load->view('over_head/add', $data);
    }

    function add_over_head()
    {
        $over_head_module_id             = $this->config->item('over_head_module');
        $data['module_id']               = $over_head_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($over_head_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data = array(
                'over_head_name'          => $this->input->post('over_head_name'),
                'over_head_unit'          => $this->input->post('over_head_unit'),
                'over_head_cost_per_unit' => $this->input->post('cost_per_unit'),
                'quantity'                => $this->input->post('over_head_quantity'),
                'added_date'              => date('Y-m-d'),
                'added_user_id'           => $this->session->userdata('SESS_USER_ID'),
                'branch_id'               => $this->session->userdata('SESS_BRANCH_ID') );

        if ($id = $this->general_model->insertData('over_head', $data))
        {
            redirect('over_head', 'refresh');
        }
    }

    function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $over_head_module_id             = $this->config->item('over_head_module');
        $data['module_id']               = $over_head_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($over_head_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['uqc'] = $this->general_model->getRecords('*', 'uqc', array(
                'delete_status' => 0 ));

        $data['over_head'] = $this->general_model->getRecords("*", "over_head", [
                'over_head_id' => $id ]);

        $this->load->view('over_head/edit', $data);
    }

    function edit_over_head()
    {
        $over_head_module_id             = $this->config->item('over_head_module');
        $data['module_id']               = $over_head_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($over_head_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $labour_id = $this->input->post('labour_id');

        $labour_data = array(
                'activity_name'     => $this->input->post('activity_name'),
                'classification_id' => $this->input->post('classify'),
                'type'              => $this->input->post('labour_type'),
                'no_of_labour'      => $this->input->post('no_of_labour'),
                'cost_per_hour'     => $this->input->post('cost_per_person'),
                'total_no_hours'    => $this->input->post('total_hours'),
                'updated_date'      => date('Y-m-d'),
                'updated_user_id'   => $this->session->userdata('SESS_USER_ID') );

        if ($this->input->post('labour_type') == "daily_basis")
        {
            $labour_data['no_of_hours'] = $this->input->post('no_of_hours');
            $labour_data['no_of_days']  = $this->input->post('no_of_days_per');
        }
        else
        {
            $labour_data['no_of_hours'] = "";
            $labour_data['no_of_days']  = "";
        }
        $table = "labour";
        $where = array(
                'labour_id' => $labour_id,
                'branch_id' => $this->session->userdata('SESS_BRANCH_ID') );
        if ($this->general_model->updateData($table, $labour_data, $where))
        {
            redirect('over_head', 'refresh');
        }
        else
        {
            redirect('over_head', 'refresh');
        }
    }

    function delete()
    {
        $over_head_module_id             = $this->config->item('over_head_module');
        $data['module_id']               = $over_head_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($over_head_module_id, $modules, $privilege);
        
        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        if ($this->general_model->updateData('labour', [
                        "delete_status" => 1 ], array(
                        'labour_id' => $id )))
        {
            redirect("labour", 'refresh');
        }
    }

    function get_price()
    {
        $id  = $this->input->post('id');
        $val = $this->general_model->getRecords('*', 'over_head', [
                'delete_status' => 0,
                'over_head_id'  => $id ]);

        echo json_encode($val);
    }

    function get_over_head($id)
    {
        $table  = "over_head_reference ohr";
        $string = "ohr.quantity,ohr.price,ohr.cost,oh.over_head_name";
        $join   = [
                'over_head oh' => 'oh.over_head_id = ohr.over_head_id'
        ];
        $where  = [
                'ohr.over_head_reference_id' => $id ];
        $val    = $this->general_model->getJoinRecords($string, $table, $where, $join);
        echo "<table class='table-striped'  width='100%'>";
        echo "<tr><th>Name</th><th>Quantity </th><th>Cost Per Unit</th><th>Total Cost</th></tr>";
        foreach ($val as $key)
        {
            echo "<tr><td>" . $key->over_head_name . "</td><td>" . $key->quantity . "</td><td>" . $key->price . "</td><td>" . $key->cost . "</td></tr>";
        }
        echo "</table>";
    }

}

