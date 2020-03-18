<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_person extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $customer_module_id              = $this->config->item('customer_module');
        $data['customer_module_id']      = $customer_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($customer_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'contact_person_code',
                    1 => 'contact_person_name',
                    2 => 'party_type',
                    3 => 'contact_person_mobile',
                    4 => 'contact_person_email',
                    5 => 'contact_person_country_id',
                    6 => 'contact_person_state_id',
                    7 => 'contact_person_city_id',
                    8 => 'added_user',
                    9 => 'action'
            );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->contact_person_list_field();
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
                    $contact_person_id                   = $this->encryption_url->encode($post->contact_person_id);
                    $nestedData['contact_person_code']   = $post->contact_person_code;
                    $nestedData['contact_person_name']   = $post->contact_person_name;
                    $nestedData['party_type']            = $post->party_type;
                    $nestedData['contact_person_mobile'] = $post->contact_person_mobile;
                    $nestedData['contact_person_email']  = $post->contact_person_email;
                    $nestedData['country_name']          = $post->country_name;
                    $nestedData['state_name']            = $post->state_name;
                    $nestedData['city_name']             = $post->city_name;

                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;

                    if (in_array($data['customer_module_id'], $data['active_edit']))
                    {
                        $cols = '<a href="' . base_url('contact_person/edit/') . $contact_person_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span> Edit Contact Person</a>';
                    }

                    if (in_array($data['customer_module_id'], $data['active_delete']))
                    {
                        $cols .= ' <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $contact_person_id . '" data-path="contact_person/delete" href="#" title="Delete" ><span class="glyphicon glyphicon-trash"></span> Delete Contact Person</a>';
                    }

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
            $this->load->view('contact_person/list', $data);
        }
    }

    public function add()
    {
        $data                            = $this->get_default_country_state();
        $customer_module_id              = $this->config->item('customer_module');
        $data['module_id']               = $customer_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($customer_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view('contact_person/add', $data);
    }

    public function edit($id)
    {
        $id = $this->encryption_url->decode($id);

        $customer_module_id              = $this->config->item('customer_module');
        $data['module_id']               = $customer_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($customer_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $string          = 'con.*';
        $table           = 'contact_person con';
        $where           = array(
                'con.contact_person_id' => $id );
        $data['data']    = $this->general_model->getRecords($string, $table, $where, $order           = "");
        $string          = 'c.*';
        $table           = 'countries c';
        $where           = array(
                'c.delete_status' => 0 );
        $data['country'] = $this->general_model->getRecords($string, $table, $where);
        $string          = 'st.*';
        $table           = 'states st';
        $where           = array(
                'st.country_id' => $data['data'][0]->contact_person_country_id );
        $data['state']   = $this->general_model->getRecords($string, $table, $where);
        $string          = 'ct.*';
        $table           = 'cities ct';
        $where           = array(
                'ct.state_id' => $data['data'][0]->contact_person_state_id );
        $data['city']    = $this->general_model->getRecords($string, $table, $where);

        $data['customer'] = $this->customer_call();
        $data['supplier'] = $this->supplier_call();

        $this->load->view('contact_person/edit', $data);
    }

    public function edit_contact_person()
    {
        $customer_module_id              = $this->config->item('customer_module');
        $data['module_id']               = $customer_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($customer_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $country                         = $this->input->post('country');
        $state                           = $this->input->post('state');

        $contact_person_id   = $this->input->post('contact_person_id');
        $contact_person_data = array(
                "contact_person_name"        => $this->input->post('contact_person_name'),
                "contact_person_code"        => $this->input->post('contact_person_code'),
                "contact_person_address"     => $this->input->post('address'),
                "contact_person_country_id"  => $this->input->post('country'),
                "contact_person_state_id"    => $this->input->post('state'),
                "contact_person_city_id"     => $this->input->post('city'),
                "contact_person_postal_code" => $this->input->post('postal_code'),
                "contact_person_email"       => $this->input->post('email'),
                "contact_person_mobile"      => $this->input->post('mobile'),
                "contact_person_telephone"   => $this->input->post('telephone'),
                "contact_person_website"     => $this->input->post('website'),
                "party_type"                 => $this->input->post('party_type'),
                "party_id"                   => $this->input->post('party_id'),
                "contact_person_department"  => $this->input->post('department'),
                "contact_person_designation" => $this->input->post('designation'),
                "contact_person_industry"    => $this->input->post('industry'),
                // "added_date"                   =>  date('Y-m-d'),
                // "added_user_id"                =>  $this->session->userdata('SESS_USER_ID'),
                "branch_id"                  => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date"               => date('Y-m-d'),
                "updated_user_id"            => $this->session->userdata('SESS_USER_ID')
        );
        $table               = "contact_person";
        if ($this->general_model->updateData($table, $contact_person_data, array(
                        'contact_person_id' => $contact_person_id )))
        {
            $table    = "log";
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $contact_person_id,
                    'table_name'        => 'contact_person',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Contact Person Updated' );
            $this->general_model->insertData($table, $log_data);
        }
        redirect("contact_person", 'refresh');
    }

    public function add_contact_person()
    {
        $customer_module_id              = $this->config->item('customer_module');
        $data['module_id']               = $customer_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($customer_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $country                         = $this->input->post('country');
        $state                           = $this->input->post('state');

        $contact_person_data = array(
                "contact_person_name"        => $this->input->post('contact_person_name'),
                "contact_person_code"        => $this->input->post('contact_person_code'),
                "contact_person_address"     => $this->input->post('address'),
                "contact_person_country_id"  => $this->input->post('country'),
                "contact_person_state_id"    => $this->input->post('state'),
                "contact_person_city_id"     => $this->input->post('city'),
                "contact_person_postal_code" => $this->input->post('postal_code'),
                "contact_person_email"       => $this->input->post('email'),
                "contact_person_mobile"      => $this->input->post('mobile'),
                "contact_person_telephone"   => $this->input->post('telephone'),
                "contact_person_website"     => $this->input->post('website'),
                "party_type"                 => $this->input->post('party_type'),
                "party_id"                   => $this->input->post('party_id'),
                "contact_person_department"  => $this->input->post('department'),
                "contact_person_designation" => $this->input->post('designation'),
                "contact_person_industry"    => $this->input->post('industry'),
                "added_date"                 => date('Y-m-d'),
                "added_user_id"              => $this->session->userdata('SESS_USER_ID'),
                "branch_id"                  => $this->session->userdata('SESS_BRANCH_ID'),
                "updated_date"               => "",
                "updated_user_id"            => ""
        );
        $table               = "contact_person";
        if ($id                  = $this->general_model->insertData($table, $contact_person_data))
        {
            $table    = "log";
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'contact_person',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Contact Person Inserted' );
            $this->general_model->insertData($table, $log_data);
        }
        redirect("contact_person", 'refresh');
    }

    public function delete()
    {
        $customer_module_id              = $this->config->item('customer_module');
        $data['module_id']               = $customer_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($customer_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);

        $table = "contact_person";
        $data  = array(
                "delete_status" => 1 );
        $where = array(
                "contact_person_id" => $id );
        if ($this->general_model->updateData($table, $data, $where))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'contact_person',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Contact Person Deleted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('contact_person');
        }
        else
        {
            $this->session->set_flashdata('fail', 'contact_person can not be Deleted.');
            redirect("contact_person", 'refresh');
        }
    }

    public function get_party_name()
    {
        $party_type = $this->input->post('party_type');
        $output     = '<option value="">Select</option>';
        if ($party_type == 'customer')
        {
            $customer = $this->customer_call();
            foreach ($customer as $row)
            {
                $output .= "<option value='" . $row->customer_id . "'>" . $row->customer_name . "</option>";
            }
        }
        elseif ($party_type == 'supplier')
        {
            $supplier = $this->supplier_call();
            foreach ($supplier as $row)
            {
                $output .= "<option value='" . $row->supplier_id . "'>" . $row->supplier_name . "</option>";
            }
        }

        echo json_encode($output);
    }

}

