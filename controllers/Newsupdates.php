<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Newsupdates extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('product_model');
        $this->modules = $this->get_modules();
        $this->load->helper(array(
                'form',
                'url' ));
        $this->load->library('form_validation');
    }

    public function index()
    {
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'news_title',
                    1 => 'type',
                    2 => 'news_description',
                    3 => 'news_added_date',
                    4 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->newsupdate();
            $list_data['search'] = 'all';
            $totalData           = $this->general_model->getRecords('news_updates.*', 'news_updates', [
                    'news_updates.delete_status' => 0,
                    'news_updates.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ]);
            $totalFiltered       = $totalData;
            if (empty($this->input->post('search')['value']))
            {
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = 'all';
                $posts               = $this->general_model->getRecords('news_updates.*', 'news_updates', [
                        'news_updates.delete_status' => 0,
                        'news_updates.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ]);
            }
            else
            {
                $search              = $this->input->post('search')['value'];
                $list_data['limit']  = $limit;
                $list_data['start']  = $start;
                $list_data['search'] = $search;
                $posts               = $this->general_model->getRecords('news_updates.*', 'news_updates', [
                        'news_updates.delete_status' => 0,
                        'news_updates.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ]);
                $totalFiltered       = $this->general_model->getRecords('news_updates.*', 'news_updates', [
                        'news_updates.delete_status' => 0,
                        'news_updates.branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ]);
            } $send_data = array();
            if (!empty($posts))
            {
                foreach ($posts as $post)
                {
                    $description              = substr(str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\\n",
                            "\n" ), '', $post->news_description), 0, 100);
                    $nestedData['news_title'] = $post->news_title;
                    $nestedData['type']       = $post->type;
                    if (strlen($description) > 99)
                    {
                        $nestedData['news_description'] = $description . '...';
                    }
                    else
                    {
                        $nestedData['news_description'] = $description;
                    } $nestedData['news_added_date'] = $post->news_added_date;
                    $news_id                       = $this->encryption_url->encode($post->news_id);
                    $cols                          = '<a href="' . base_url('newsupdates/edit/') . $news_id . '" title="Edit" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span></a>';
                    $cols                          .= '                <a data-backdrop="static" data-keyboard="false" class="delete_button btn btn-xs btn-danger" data-toggle="modal" data-target="#delete_modal" data-id="' . $news_id . '" data-path="newsupdates/delete"  href="#" title="Delete" ><span class="glyphicon glyphicon-trash"></span></a>';
                    $cols                          .= '</ul>';
                    $nestedData['action']          = $cols;
                    $send_data[]                   = $nestedData;
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
            $data['currency'] = $this->currency_call();
            $this->load->view('newsupdates/list', $data);
        }
    }

    public function add()
    {
        $data                            = $this->get_default_country_state();
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $where         = [
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ];
        $data['users'] = $this->general_model->getRecords("users.username,users.id", "users", $where);
        $this->load->view('newsupdates/add', $data);
    }

    public function add_newsupdates()
    {
        $data                            = $this->get_default_country_state();
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $branch_id     = $this->session->userdata('SESS_BRANCH_ID');
        $added_user_id = $this->session->userdata("SESS_USER_ID");
        $title         = $this->input->post('title');
        $type          = $this->input->post('type');
        $description   = $this->input->post('description');
        $users         = $this->input->post('usersSelect');
        $users_list    = implode(",", $users);
        $insert_data   = [
                'branch_id'        => $branch_id,
                'added_user_id'    => $added_user_id,
                'news_title'       => $title,
                'type'             => $type,
                'news_description' => $description,
                'news_display_id'  => $users_list,
                'news_added_date'  => date('Y-m-d') ];
        if ($news_id       = $this->general_model->insertData("news_updates", $insert_data))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $news_id,
                    'table_name'        => 'news_updates',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'News Updates Inserted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('newsupdates');
        }
        else
        {
            redirect('newsupdates', 'refresh');
        }
    }

    function edit($id)
    {
        $data                            = $this->get_default_country_state();
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $ids              = $this->encryption_url->decode($id);
        $data['id']       = $id;
        $data['get_list'] = $this->general_model->getRecords('news_updates.*', 'news_updates', [
                'delete_status' => 0,
                "news_id"       => $ids,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $where            = [
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ];
        $data['users']    = $this->general_model->getRecords("users.username,users.id", "users", $where);
        $this->load->view('newsupdates/edit', $data);
    }

    function edit_newsupdates()
    {
        $data                            = $this->get_default_country_state();
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id          = $this->input->post('news_id');
        $news_id     = $this->encryption_url->decode($id);
        $title       = $this->input->post('title');
        $type        = $this->input->post('type');
        $description = $this->input->post('description');
        $users       = $this->input->post('usersSelect');
        $users_list  = implode(",", $users);
        $news_data   = [
                'news_title'        => $title,
                'type'              => $type,
                'news_description'  => $description,
                'news_display_id'   => $users_list,
                'news_updated_date' => date('Y-m-d') ];
        if ($this->general_model->updateData('news_updates', $news_data, [
                        'news_id' => $news_id ]))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $news_id,
                    'table_name'        => 'news_updates',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'News Updates Updated' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('newsupdates');
        }
        else
        {
            redirect('newsupdates', 'refresh');
        }
    }

    function all_news()
    {
        $data                            = $this->get_default_country_state();
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['news'] = $this->general_model->getRecords('news_updates.*', 'news_updates', [
                'delete_status' => 0,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $this->load->view('newsupdates/all_news', $data);
    }

    function update_new($id)
    {
        $data                            = $this->get_default_country_state();
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id           = $this->encryption_url->decode($id);
        $data['news'] = $this->general_model->getRecords('news_updates.*', 'news_updates', [
                'delete_status' => 0,
                "news_id"       => $id,
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID') ]);
        $this->load->view('newsupdates/update_new', $data);
    }

    function delete()
    {
        $data                            = $this->get_default_country_state();
        $news_updates_module_id          = $this->config->item('news_updates_module');
        $data['module_id']               = $news_updates_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($news_updates_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('news_updates', array(
                        'delete_status' => 1 ), array(
                        'news_id' => $id )))
        {
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'news_updates',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    'branch_id'         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'News Updates Deleted' );
            $table    = "log";
            $this->general_model->insertData($table, $log_data);
            redirect('newsupdates');
        }
        else
        {
            $this->session->set_flashdata('fail', 'News Updates can not be Deleted.');
            redirect("newsupdates", 'refresh');
        }
    }

}

