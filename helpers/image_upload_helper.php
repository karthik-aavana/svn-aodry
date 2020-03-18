<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');if (!function_exists('image_upload'))
{

    function image_upload($img, $max_size)
    {
        $config['upload_path']   = 'assets/affiliate/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']      = $max_size;
        // $config['max_width']     = $max_width;
        // $config['max_height']    = $max_height;
        $config['file_name']     = $img . "-" . time() . '.jpg';
        $CI                      = & get_instance();
        $CI->load->library('upload', $config);
        if (!$CI->upload->do_upload($img))
        {
            $error = array('error' => $CI->upload->display_errors());
            print_r($error);
        }
        else
        {
            return $config['file_name'];
        }

    }



    function upload_multiple_image($userfile, $upload_path) {
        $CI = & get_instance();
        $CI->load->library('upload');
        $config['upload_path'] = $upload_path;
        $data = array();
        $files = $_FILES;
        if (!empty($_FILES[$userfile]['name'][0])) {
            $filesCount = count($_FILES[$userfile]['name']);

            for ($i = 0; $i < $filesCount; $i++) {
                $_FILES['userfile']['name'] = $files[$userfile]['name'][$i];
                $_FILES['userfile']['type'] = $files[$userfile]['type'][$i];
                $_FILES['userfile']['tmp_name'] = $files[$userfile]['tmp_name'][$i];
                $_FILES['userfile']['error'] = $files[$userfile]['error'][$i];
                $_FILES['userfile']['size'] = $files[$userfile]['size'][$i];

                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['file_name'] = time() . mt_rand() . '.jpg';
                $config['max_size'] = '100000'; 
                $config['max_width'] = '2048';
                $config['max_height'] = '1080';

                $CI->upload->initialize($config);
                if (!$CI->upload->do_upload('userfile')) {
                    echo $CI->upload->display_errors();
                    exit();
                } else {
                    $data[] = $CI->upload->data('file_name');
                }
            }
            return json_encode($data);
        } else {
            return '';
        }
    }

}