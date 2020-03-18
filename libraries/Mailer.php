<?php
require_once APPPATH . "third_party/PHPMailer/PHPMailerAutoload.php";
class Mailer{

    public function sendEmail($dataSet){

        $CI = &get_instance();

        $data = array(

            'title'           => isset($dataSet['subject']) ? $dataSet['subject'] : "",

            'content'         => isset($dataSet['message']) ? $dataSet['message'] : "",

            'email'           => isset($dataSet['email']) ? $dataSet['email'] : "",

            'showUnsubscribe' => isset($dataSet['showUnsubscribe']) ? $dataSet['showUnsubscribe'] : false,

            'link'            => isset($dataSet['link']) ? $dataSet['link'] : null

        );

        $mail = new PHPMailer();

        try {

            //Server settings
            //$mail->isSMTP();
            //$mail->IsMail();
            //$mail->SMTPDebug  = 3;

            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = "chetna.b@aavana.in";
            $mail->Password   = "ZXCVzxcv";
            $mail->SMTPSecure = 'tls';
            $mail->Port       = '587';
            $mail->IsHTML(true);
            $mail->CharSet    = 'UTF-8';
            
            /*$mail->SMTPOptions = array(

                'ssl' => array(

                    'verify_peer'       => false,

                    'verify_peer_name'  => false,

                    'allow_self_signed' => true

                )

            ); */

            //Recipients
            $from = 'Aavana Corporate Solution[noreply@aodry.com]';
            $mail->setFrom($from);
            $mail->addReplyTo($from);
            $mail->addAddress($data['email']);
            $mail->isHTML(true);

            $mail->Subject = $data['title'];

            $mail->Body    = $data['content'];

            if (!$mail->send()){
                return $mail->ErrorInfo;
            }else{
                return true;
            }
        }catch (Exception $e){
            $CI->session->set_flashdata('error', $mail->ErrorInfo);
            return false;
        }

    }

}