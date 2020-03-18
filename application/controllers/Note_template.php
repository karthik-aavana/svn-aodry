<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Note_template extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['notes_module_id']         = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = $privilege;
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        if (!empty($this->input->post()))
        {
            $columns             = array(
                    0 => 'hash_tag',
                    1 => 'title',
                    2 => 'content',
                    2 => 'user',
                    3 => 'action', );
            $limit               = $this->input->post('length');
            $start               = $this->input->post('start');
            $order               = $columns[$this->input->post('order')[0]['column']];
            $dir                 = $this->input->post('order')[0]['dir'];
            $list_data           = $this->common->note_template_list_field();
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
                    $nestedData['hash_tag']   = $post->hash_tag;
                    $nestedData['title']      = $post->title;
                    $nestedData['content']    = str_replace(array(
                            "\r\n",
                            "\\r\\n",
                            "\\n",
                            "\n" ), " <br>", $post->content);
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $note_template_id         = $this->encryption_url->encode($post->note_template_id);
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">'; 
                    $cols.=  '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_note_template_modal"><a data-id="' . $note_template_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="edit_note_template btn btn-app"><i class="fa fa-pencil"></i></a></span>';
                   // $cols.= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"><a data-id="' . $note_template_id . '" data-path="note_template/delete" class="btn btn-app" data-toggle="tooltip" data-placement="bottom" title="Delete" ><i class="fa fa-trash"></i></a></span>';
                    $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal" data-delete_message="If you delete this record then its assiociated records also will be delete!! Do you want to continue?"> <a class="btn btn-app delete_button" data-id="' . $note_template_id . '" data-path="note_template/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"> <i class="fa fa-trash-o"></i> </a></span>';
                    $cols .= '</div></div>';					
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[]              = $nestedData;
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
            $this->load->view('note_template/list', $data);
        }
    }

    public function add()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $this->load->view('note_template/add', $data);
    }

    public function add_note_template()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $note_data                       = array(
                'hash_tag'      => '#' . $this->input->post('hash_tag'),
                'title'         => $this->input->post('title'),
                'content'       => $this->input->post('content'),
                "added_date"    => date('Y-m-d'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID'),
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') );
        if ($note_template_id                = $this->general_model->insertData('note_template', $note_data))
        {
            $successMsg = 'Note Template Added Successfully';
            $this->session->set_flashdata('note_template_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $note_template_id,
                    'table_name'        => 'note_template',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Note Template Inserted' );
            $this->general_model->insertData('log', $log_data);
        }else{
            $errorMsg = 'Note Template Add Unsuccessful';
            $this->session->set_flashdata('note_template_error',$errorMsg);
        }
        redirect('note_template');
    }

    public function add_note_template_modal()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "add_privilege";
        $data['privilege']               = "add_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $note_data                       = array(
                'hash_tag'      => '#' . $this->input->post('hash_tag'),
                'title'         => $this->input->post('title'),
                'content'       => $this->input->post('content'),
                "added_date"    => date('Y-m-d'),
                'added_user_id' => $this->session->userdata('SESS_USER_ID'),
                "branch_id"     => $this->session->userdata('SESS_BRANCH_ID') );
        $result = array();
        if ($note_template_id                = $this->general_model->insertData('note_template', $note_data))
        {
            $result['flag'] = true;
            $result['msg'] = 'Note Template Added Successfully';
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $note_template_id,
                    'table_name'        => 'note_template',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Note Template Inserted' );
            $this->general_model->insertData('log', $log_data);
        } else {
            $result['flag'] = false;
            $result['msg'] = 'Note Template Add Unsuccessful';
        }
        echo json_encode($result);
    }

    public function edit($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data['template'] = $this->general_model->getRecords('*', 'note_template', array(
                'note_template_id' => $id,
                'delete_status'    => 0 ));
        $this->load->view('note_template/edit', $data);
    }

    public function update_note_template()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('id');
        $note_data                       = array(
                'hash_tag'        => $this->input->post('edit_hash_tag'),
                'title'           => $this->input->post('edit_title'),
                'content'         => $this->input->post('edit_content'),
                "updated_date"    => date('Y-m-d'),
                'updated_user_id' => $this->session->userdata('SESS_USER_ID'),
                "branch_id"       => $this->session->userdata('SESS_BRANCH_ID') );
        $result = array();
        if ($this->general_model->updateData('note_template', $note_data, array(
                        'note_template_id' => $id )))
        {
            $result['flag'] = true;
            $result['msg'] = 'Note Template Updated successfully';
            /*$successMsg = 'Note Template Updated successfully';
            $this->session->set_flashdata('note_template_success',$successMsg);*/
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'note_template',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Note Template Updated' );
            $this->general_model->insertData('log', $log_data);
        } else{
            $result['flag'] = false;
            $result['msg'] = 'Note Template Update Unsuccessful';
          /*  $errorMsg = 'Note Template Update Unsuccessful';
            $this->session->set_flashdata('note_template_error',$errorMsg);*/
        } 
        echo json_encode($result);
    }

    public function delete()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "delete_privilege";
        $data['privilege']               = "delete_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $id                              = $this->input->post('delete_id');
        $id                              = $this->encryption_url->decode($id);
        
        if ($this->general_model->updateData('note_template', array(
                        'delete_status' => 1 ), array(
                        'note_template_id' => $id ))){
            $successMsg = 'Note Template Deleted successfully';
            $this->session->set_flashdata('note_template_success',$successMsg);
            $log_data = array(
                    'user_id'           => $this->session->userdata('SESS_USER_ID'),
                    'table_id'          => $id,
                    'table_name'        => 'note_template',
                    'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                    "branch_id"         => $this->session->userdata('SESS_BRANCH_ID'),
                    'message'           => 'Note Template Deleted' );
            $this->general_model->insertData('log', $log_data);
        }else{
            $errorMsg = 'Note Template Delete Unsuccessful';
            $this->session->set_flashdata('note_template_error',$errorMsg);
        } 
        redirect('note_template');
    }

    public function get_note_template()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        $data                            = $this->general_model->getRecords('*', 'note_template', array(
                'branch_id'     => $this->session->userdata('SESS_BRANCH_ID'),
                'delete_status' => 0 ));
        echo json_encode($data);
    }

    public function edit_get_note_template($id)
    {
        $id                              = $this->encryption_url->decode($id);
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "edit_privilege";
        $data['privilege']               = "edit_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        
        /* presents all the needed */
        $data=array_merge($data,$section_modules);

        $data = $this->general_model->getRecords('*', 'note_template', array(
                'note_template_id' => $id,
                'delete_status'    => 0 ));
        echo json_encode($data);
    }

    public function get_note_template_by_tag()
    {
        $notes_module_id                 = $this->config->item('notes_module');
        $data['module_id']               = $notes_module_id;
        $modules                         = $this->modules;
        $privilege                       = "view_privilege";
        $data['privilege']               = "view_privilege";
        $section_modules                 = $this->get_section_modules($notes_module_id, $modules, $privilege);
        $data['access_modules']          = $section_modules['active_modules'];
        $data['access_sub_modules']      = $section_modules['access_sub_modules'];
        /*$data['access_module_privilege'] = $section_modules['module_privilege'];
        $data['access_user_privilege']   = $section_modules['user_privilege'];*/
        $data['access_settings']         = $section_modules['access_settings'];
        $data['access_common_settings']  = $section_modules['access_common_settings'];
        $hash_tag                        = $this->input->post('hash_tag');
        $data                            = $this->general_model->getRecords('*', 'note_template', array(
                'hash_tag'      => $hash_tag, 'delete_status' => 0 ));
        if ($data)
            echo json_encode('fail');
        else
            echo json_encode('success');
    }

}

