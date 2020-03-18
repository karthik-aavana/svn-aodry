<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class My_404 extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    public function index()
    {
        $modules = $this->get_modules();
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
        } $this->output->set_status_header('404');
        $this->load->view('unauthorized', $data);
    }

    function test()
    {
        $this->load->view('premium_user');
    }

    function send_mail()
    {
        require APPPATH . 'third_party/PHPMailer/PHPMailerAutoload.php';
        $email            = $this->input->post('email');
        $mail             = new PHPMailer;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "karthik.u@aavana.in";
        $mail->Password   = "karthik@123";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = '587';
        $mail->IsHTML(true);
        $mail->setFrom('karthik.u@aavana.in', 'karthik');
        $mail->addReplyTo('karthik.u@aavana.in', 'karthik');
        $mail->addAddress('karthik.u@aavana.in');
        $mail->isHTML(true);
        $bodyContent      = '';
        $mail->Subject    = "Request for Premium upgradation";
        $mail->Body       = 'Email :' . $email;
        if (!$mail->send())
        {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
        else
        {
            $this->load->view('premium_user', [
                    "sucess" => "Sucessfully sent mail we will get back to you As soon as possible" ]);
        }
    }

}

