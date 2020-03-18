<?php

defined('BASEPATH') OR exit('NO direct script access allowed');

class Labour extends MY_Controller
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

        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($labour_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'activity_name',
                    1 => 'labour_classification_name',
                    2 => 'type',
                    3 => 'total_no_hours',
                    4 => 'added_user',
                    5 => 'action' );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->labour_list();
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
                    $labour_id                                = $this->encryption_url->encode($post->labour_id);
                    $nestedData['added_date']                 = $post->added_date;
                    $nestedData['labour_classification_name'] = $post->labour_classification_name;
                    $nestedData['activity_name']              = $post->activity_name;
                    $nestedData['type']                       = $post->type;
                    $nestedData['total_no_hours']             = $post->total_no_hours;


                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $cols                     = ' <a href="' . base_url('labour/edit/') . $labour_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>';


                    $cols                 .= ' <a data-toggle="modal" data-target="#delete_modal" data-id="' . $labour_id . '" data-path="labour/delete" title="Delete" class="delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
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

            $this->load->view('labour/list', $data);
        }
    }

    function add()
    {
        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($labour_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['labour_classification'] = $this->general_model->getRecords('*', 'labour_classification',
                                                                          array(
                        'delete_status' => 0,
                        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $this->load->view('labour/add', $data);
    }

    function add_labour()
    {
        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($labour_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post('no_of_days_per')))
        {

            $hrs   = $this->input->post('no_of_days_per') * $this->input->post('no_of_hours');
            // echo $hrs;die;
            $total = $hrs * $this->input->post('no_of_labour') * $this->input->post('cost_per_person');
        }
        else
        {
            $hrs   = $this->input->post('total_hours');
            $total = $hrs * $this->input->post('no_of_labour') * $this->input->post('cost_per_person');
        }


        $data = array(
                'activity_name'     => $this->input->post('activity_name'),
                'classification_id' => $this->input->post('classify'),
                'type'              => $this->input->post('labour_type'),
                'no_of_labour'      => $this->input->post('no_of_labour'),
                'no_of_hours'       => $this->input->post('no_of_hours'),
                'no_of_days'        => $this->input->post('no_of_days_per'),
                'total_no_hours'    => $hrs,
                'cost_per_hour'     => $this->input->post('cost_per_person'),
                'added_date'        => date('Y-m-d'),
                'total_cost'        => $total,
                'added_user_id'     => $this->session->userdata('SESS_USER_ID'),
                'branch_id'         => $this->session->userdata('SESS_BRANCH_ID') );
        // print_r( $data);die;

        if ($id = $this->general_model->insertData('labour', $data))
        {
            redirect('labour', 'refresh');
        }
    }

    function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($labour_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['labour_classification'] = $this->general_model->getRecords('*', 'labour_classification',
                                                                          array(
                        'delete_status' => 0,
                        'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ));

        $string = "l.*,lc.labour_classification_id,lc.labour_classification_name";
        $table  = "labour l";
        $join   = [
                'labour_classification lc' => "lc.labour_classification_id = l.classification_id" ];
        $where  = array(
                'l.labour_id'     => $id,
                'l.branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'l.delete_status' => 0 );

        $data['labour_data'] = $this->general_model->getJoinRecords($string, $table, $where, $join);


        $this->load->view('labour/edit', $data);
    }

    function edit_labour()
    {
        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($labour_module_id, $modules, $privilege);
        
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
            redirect('labour', 'refresh');
        }
        else
        {
            redirect('labour', 'refresh');
        }
    }

    function delete()
    {
        $labour_module_id                = $this->config->item('labour_module');
        $data['module_id']               = $labour_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($labour_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);

        if ($this->general_model->updateData('labour', [
                        "delete_status" => 1 ], array(
                        'labour_id' => $id )))
        {
            redirect("labour", 'refresh');
        }
    }

    function get_labour_price()
    {

        $id = $this->input->post('id');

        $va = $this->general_model->getRecords('*', 'labour', [
                'labour_id' => $id ]);

        echo json_encode($va);
    }

    function get_labour($id)
    {

        $table  = "labour_reference lr";
        $string = "lr.no_of_labour,lr.total_no_hours,lr.cost_per_hour,l.activity_name";
        $join   = [
                'labour l' => 'l.labour_id = lr.labour_id'
        ];
        $where  = [
                'lr.labour_reference_id' => $id ];
        $val    = $this->general_model->getJoinRecords($string, $table, $where, $join);
        echo "<table class='table-striped'  width='100%'>";
        echo "<tr><th>No of labour </th><th>total no hours Required</th><th>cost per hour</th><th>activity_name</th></tr>";
        foreach ($val as $key)
        {
            echo "<tr><td>" . $key->no_of_labour . "</td><td>" . $key->total_no_hours . "</td><td>" . $key->cost_per_hour . "</td><td>" . $key->activity_name . "</td></tr>";
        }
        echo "</table>";
    }

}

