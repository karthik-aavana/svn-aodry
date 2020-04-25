<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Email_template extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model([
            'general_model']);
        $this->modules = $this->get_modules();
    }

    public function index() {
        $email_module_id = $this->config->item('email_module');
        $data['email_module_id'] = $email_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($email_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        if (!empty($this->input->post())) {
            $columns = array(
                0 => 'added_date',
                1 => 'email_template_name',
                2 => 'subject',
                3 => 'message',
                4 => 'signature',
                5 => 'module_name',
                6 => 'added_user',
                7 => 'action',);
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];
            $list_data = $this->common->email_template_list_field();
            $list_data['search'] = 'all';
            $totalData = $this->general_model->getPageJoinRecordsCount($list_data);
            $totalFiltered = $totalData;
            if (empty($this->input->post('search')['value'])) {
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = 'all';
                $posts = $this->general_model->getPageJoinRecords($list_data);
            } else {
                $search = $this->input->post('search')['value'];
                $list_data['limit'] = $limit;
                $list_data['start'] = $start;
                $list_data['search'] = $search;
                $posts = $this->general_model->getPageJoinRecords($list_data);
                $totalFiltered = $this->general_model->getPageJoinRecordsCount($list_data);
            } $send_data = array();
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $nestedData['added_date'] = $post->added_date;
                    $nestedData['email_template_name'] = $post->email_template_name;
                    $nestedData['subject'] = $post->subject;
                    $nestedData['message'] = str_replace(array(
                        "\r\n",
                        "\\r\\n",
                        "\\n",
                        "\n"), "<br>", $post->message);
                    $nestedData['signature'] = str_replace(array(
                        "\r\n",
                        "\\r\\n",
                        "\\n",
                        "\n"), "<br>", $post->signature);
                    $nestedData['module_name'] = $post->module_name;
                    $nestedData['added_user'] = $post->first_name . ' ' . $post->last_name;
                    $email_template_id = $this->encryption_url->encode($post->email_template_id);
                    $cols = '<div class="box-body hide action_button"><div class="btn-group">';
                    if(in_array($email_module_id, $data['active_view'])){                   
                        $cols .= '<span data-target="#view_modal" data-toggle="modal" data-backdrop="static" data-keyboard="false"><a data-id="' . $email_template_id . '" class="btn btn-xs btn-app view_template" data-toggle="tooltip" data-placement="bottom" title="View"><i class="fa fa-eye"></i></a></span>';
                    }
                    if(in_array($email_module_id, $data['active_edit'])){  
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#edit_modal"><a data-id="' . $email_template_id . '" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-xs btn-app edit_template"><i class="fa fa-pencil"></i></a></span>';
                    }
                    if(in_array($email_module_id, $data['active_delete'])){  
                        $cols .= '<span data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#delete_modal"><a class="delete_button btn btn-xs btn-app"  data-id="' . $email_template_id . '" data-path="email_template/delete" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash"></i></a></span>';
                    }
                    $cols .= '</div></div>';                   
                    $nestedData['action'] = $cols.'<input type="checkbox" name="check_item" class="form-check-input checkBoxClass minimal">';
                    $send_data[] = $nestedData;
                }
            } $json_data = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $send_data);
            echo json_encode($json_data);
        } else {
            $this->load->view('email_template/list', $data);
        }
    }

    public function get_module_id() {
        $email_module_id = $this->config->item('email_module');
        $data['module_id'] = $email_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($email_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_sub_module_id = $this->config->item('email_sub_module');
        $string = "m.module_id,m.module_name";
        $table = "active_sub_modules am";
        $where = array(
            'm.delete_status' => 0,
            'am.branch_id' => $this->session->userdata("SESS_BRANCH_ID"),
            'am.sub_module_id' => $email_sub_module_id);
        $join = array(
            'modules m' => 'm.module_id=am.module_id');
        $module = $this->general_model->getJoinRecords($string, $table, $where, $join);
        echo json_encode($module);
    }

    public function add_email_template() {
        $email_module_id = $this->config->item('email_module');
        $data['module_id'] = $email_module_id;
        $modules = $this->modules;
        $privilege = "add_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($email_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $email_data = array(
            'module_id' => $this->input->post('module_id'),
            'email_template_name' => $this->input->post('email_template_name'),
            'subject' => $this->input->post('subject'),
            'message' => $this->input->post('message'),
            'signature' => $this->input->post('signature'),
            "added_date" => date('Y-m-d'),
            'added_user_id' => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));

        $resp = array();
        if ($email_template_id = $this->general_model->insertData('email_template', $email_data)) {
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $email_template_id,
                'table_name' => 'email_template',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Email Template Inserted');
            $this->general_model->insertData('log', $log_data);
            $resp['flag'] = true;
            $resp['msg'] = 'Email Template Added Successfully';
        }else{
            $resp['flag'] = false;
            $resp['msg'] = 'Email Template Add UnSuccessful'; 
        }
        echo json_encode($resp);
    }

    public function get_email_template() {
        $id = $this->encryption_url->decode($this->input->post('id'));
        $email_module_id = $this->config->item('email_module');
        $data['module_id'] = $email_module_id;
        $modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($email_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $data['data'] = $this->general_model->getRecords('*', 'email_template', array(
            'email_template_id' => $id,
            'delete_status' => 0));
        $email_sub_module_id = $this->config->item('email_sub_module');
        $string = "m.module_id,m.module_name";
        $table = "active_sub_modules am";
        $where = array(
            'm.delete_status' => 0,
            'am.sub_module_id' => $email_sub_module_id);
        $join = array(
            'modules m' => 'm.module_id=am.module_id');
        $data['module'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        echo json_encode($data);
    }

    public function get_email_templates() {
        $id = $this->input->post('id');
        $email_module_id = $this->config->item('email_module');
        $data['module_id'] = $email_module_id;
        /*$modules = $this->modules;
        $privilege = "view_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($email_module_id, $modules, $privilege);*/

        /* presents all the needed */
        /*$data = array_merge($data, $section_modules);*/

        $data['data'] = $this->general_model->getRecords('*', 'email_template', array(
            'email_template_id' => $id,
            'delete_status' => 0));
        $email_sub_module_id = $this->config->item('email_sub_module');
        $string = "m.module_id,m.module_name";
        $table = "active_sub_modules am";
        $where = array(
            'm.delete_status' => 0,
            'am.sub_module_id' => $email_sub_module_id);
        $join = array(
            'modules m' => 'm.module_id=am.module_id');
        $data['module'] = $this->general_model->getJoinRecords($string, $table, $where, $join);
        echo json_encode($data);
    }

    public function edit_email_template($id) {
        $id = $this->encryption_url->decode($id);
        $email_module_id = $this->config->item('email_module');
        $data['module_id'] = $email_module_id;
        $modules = $this->modules;
        $privilege = "edit_privilege";
        $data['privilege'] = $privilege;
        $section_modules = $this->get_section_modules($email_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);
        $resp = array();
        $email_data = array(
            'module_id' => $this->input->post('module_id'),
            'email_template_name' => $this->input->post('email_template_name'),
            'subject' => $this->input->post('subject'),
            'message' => $this->input->post('message'),
            'signature' => $this->input->post('signature'),
            "updated_date" => date('Y-m-d'),
            'updated_user_id' => $this->session->userdata('SESS_USER_ID'),
            "branch_id" => $this->session->userdata('SESS_BRANCH_ID'));
        if ($this->general_model->updateData('email_template', $email_data, array(
                    'email_template_id' => $id))) {
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'email_template',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Email Template Updated');
            $this->general_model->insertData('log', $log_data);
            $resp['flag'] = true;
            $resp['msg'] = 'Email Template Updated Successfully';
        } else{
            $resp['flag'] = false;
            $resp['msg'] = 'Email Template Update UnSuccessful'; 
        }
        echo json_encode($resp);
    }

    public function delete() {
        $email_module_id = $this->config->item('email_module');
        $data['module_id'] = $email_module_id;
        $modules = $this->modules;
        $privilege = "delete_privilege";
        $data['privilege'] = "delete_privilege";
        $section_modules = $this->get_section_modules($email_module_id, $modules, $privilege);

        /* presents all the needed */
        $data = array_merge($data, $section_modules);

        $id = $this->input->post('delete_id');
        $id = $this->encryption_url->decode($id);
        if ($this->general_model->updateData('email_template', array(
                    'delete_status' => 1), array(
                    'email_template_id' => $id))) {
            $successMsg = 'Email Template Deleted successfully';
            $this->session->set_flashdata('email_template_success',$successMsg);
            $log_data = array(
                'user_id' => $this->session->userdata('SESS_USER_ID'),
                'table_id' => $id,
                'table_name' => 'email_template',
                'financial_year_id' => $this->session->userdata('SESS_FINANCIAL_YEAR_ID'),
                "branch_id" => $this->session->userdata('SESS_BRANCH_ID'),
                'message' => 'Email Template Deleted');
            $this->general_model->insertData('log', $log_data);
        } else{
            $errorMsg = 'Email Template Delete Unsuccessful';
            $this->session->set_flashdata('email_template_error',$errorMsg);
        }
        redirect('email_template');
    }

    public function send_email($id) {
        $id = $this->encryption_url->decode($id);
        $branch_data = $this->common->branch_field();
        $branch = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $attachment_file = $this->input->post('pdf_file_path');
        $from = $this->input->post('from_email');
        $from_email = $this->general_model->getRecords('*', 'email_setup', array(
            'email_setup_id' => $from,
            'delete_status' => 0,
            'added_user_id' => $this->session->userdata('SESS_USER_ID')));
        $to = $this->input->post('to_email');
        $to_email = explode(',', $to);
        $cc = $this->input->post('cc_email');
        $cc_email = explode(',', $cc);
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $message = str_replace(array(
            "\r\n",
            "\\r\\n"), "<br>", $message);
        $redirect = $this->input->post('redirect');
        require APPPATH . 'third_party/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
       // $mail->isSMTP();
        /* $mail->Host       = $from_email[0]->smtp_host; */
        //$mail->Host = 'smtp.gmail.com';
        //$mail->SMTPAuth = true;
        /* $mail->Username   = $from_email[0]->smtp_username;
          $mail->Password   = $from_email[0]->smtp_password;
          $mail->SMTPSecure = $from_email[0]->smtp_secure;
          $mail->Port       = $from_email[0]->smtp_port; */
      //  $mail->Username = "chetna.b@aavana.in";
        //$mail->Password = "ZXCVzxcv";
        //$mail->SMTPSecure = 'tls';
      //  $mail->Port = '587';


        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "chetna.b@aavana.in";
        $mail->Password   = "ZXCVzxcv";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = '587';
        $mail->IsHTML(true);
        $mail->CharSet    = 'UTF-8';

       // $mail->IsHTML(true);
        $mail->setFrom($from_email[0]->reply_mail, $branch[0]->branch_name . ', ' . $from_email[0]->from_name);
        $mail->addReplyTo($from_email[0]->reply_mail, $branch[0]->branch_name . ', ' . $from_email[0]->from_name);
        $mail->addAddress($to_email[0]);

        $i = 0;
        foreach ($to_email as $value) {
            if ($i == 1) {
                $mail->addCC($value);
            }
            $i = 1;
        }
        foreach ($cc_email as $value) {
            $mail->addCC($value);
        }

        $mail->isHTML(true);
        $bodyContent = $message;
        $mail->Subject = $subject;
        $mail->Body = $bodyContent;
        $mail->addAttachment($attachment_file);
        if (isset($_FILES["attachments"]["name"]) && $_FILES["attachments"]["name"] != "") {
            $file_tmp = $_FILES["attachments"]["tmp_name"];
            $file_name = $_FILES["attachments"]["name"];
            $mail->addAttachment($file_tmp, $file_name);
        }
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $this->session->set_flashdata('email_send', 'success');
            redirect($redirect, 'refresh');
        }
    }

    public function send_email_compose() {
        $id = $this->input->post('id');
        
        $id = $this->encryption_url->decode($id);
        $branch_data = $this->common->branch_field();
        $branch = $this->general_model->getJoinRecords($branch_data['string'], $branch_data['table'], $branch_data['where'], $branch_data['join'], $branch_data['order']);
        $attachment_file = $this->input->post('pdf_file_path');
        $from = $this->input->post('from_email');
        $from_email = $this->general_model->getRecords('*', 'email_setup', array(
            'email_setup_id' => $from,
            'delete_status' => 0,
            'added_user_id' => $this->session->userdata('SESS_USER_ID')));
        $to = $this->input->post('to_email');
        $to_email = explode(',', $to);
        $cc = $this->input->post('cc_email');
        $cc_email = explode(',', $cc);
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        // $from_email               = $this->input->post('from_email');
        $message = str_replace(array(
            "\r\n",
            "\\r\\n"), "<br>", $message);
        $redirect = $this->input->post('redirect');
        require APPPATH . 'third_party/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
      //  $mail->isSMTP();
        /* $mail->Host       = $from_email[0]->smtp_host; */
        //$mail->Host = 'smtp.gmail.com';
        //$mail->SMTPAuth = true;
        /* $mail->Username   = $from_email[0]->smtp_username;
          $mail->Password   = $from_email[0]->smtp_password;
          $mail->SMTPSecure = $from_email[0]->smtp_secure;
          $mail->Port       = $from_email[0]->smtp_port; */
        //$mail->Username = "chetna.b@aavana.in";
       // $mail->Password = "ZXCVzxcv";
       // $mail->SMTPSecure = 'tls';
      //  $mail->Port = '587';
        //$mail->IsHTML(true);


        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "chetna.b@aavana.in";
        $mail->Password   = "ZXCVzxcv";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = '587';
        $mail->IsHTML(true);
        $mail->CharSet    = 'UTF-8';
        //$mail->setFrom($from_email[0]->reply_mail, $branch[0]->branch_name . ', ' . $from_email[0]->from_name);
        $mail->setFrom($from);
        $mail->addReplyTo($from);
        // $mail->addReplyTo($from_email[0]->reply_mail, $branch[0]->branch_name . ', ' . $from_email[0]->from_name);
        $mail->addAddress($to_email[0]);

        $i = 0;
        foreach ($to_email as $value) {
            if ($i == 1) {
                $mail->addCC($value);
            }
            $i = 1;
        }
        foreach ($cc_email as $value) {
            $mail->addCC($value);
        }

        $mail->isHTML(true);
        $bodyContent = $message;
        $mail->Subject = $subject;
        $mail->Body = $bodyContent;
        $mail->addAttachment($attachment_file);
        if (isset($_FILES["attachments"]["name"]) && $_FILES["attachments"]["name"] != "") {
            $file_tmp = $_FILES["attachments"]["tmp_name"];
            $file_name = $_FILES["attachments"]["name"];
            $mail->addAttachment($file_tmp, $file_name);
        }
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $this->session->set_flashdata('email_send', 'success');
            redirect($redirect, 'refresh');
        }
    }

}
