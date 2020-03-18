<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Similar extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('general_model');
    }

    function get_state($id)
    {
        $string              = 's.*';
        $table               = 'states s';
        $join['countries c'] = 'c.country_id = s.country_id';
        $where               = array(
                's.country_id' => $id );
        $data                = $this->general_model->getJoinRecords($string, $table, $where, $order               = "", $join);
        echo json_encode($data);
    }

}

