<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('common_model');
    }

    public function getState($id)
    {



        $string              = 's.*';
        $table               = 'states s';
        $join['countries c'] = 'c.country_id = s.country_id';

        $where = array(
                's.country_id' => $id );

        $data = $this->common_model->getJoinRecords($string, $table, $where, $ordr = "", $join);

        echo json_encode($data);
    }

    public function getCity($id)
    {
        $string           = 'c.*';
        $table            = 'cities c';
        $join['states s'] = 's.state_id = c.state_id';

        $where = array(
                'c.state_id' => $id );

        $data = $this->common_model->getJoinRecords($string, $table, $where, $join);
        echo json_encode($data);
    }

    public function template_note($data_note, $data_note2)
    {


        $val = str_replace(array(
                "\r\n#",
                "\\r\\n#" ), " #", $data_note);
        $val = str_replace(array(
                "\r\n",
                "\\r\\n" ), " <br>", $val);


        $note             = $val;
        $template         = '';
        $j                = 0;
        $space            = 0;
        $text             = array();
        $template_content = array();
        for ($i = 0; $i < strlen($note); $i++)
        {
            if ($note[$i] == '#')
            {
                $space = 1;
            }

            if ($space == 1)
            {
                if ($note[$i] != ' ')
                {
                    $template .= $note[$i];
                }
                else
                {
                    $res = $this->template_model->get_template_by_tag($template);
                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j]           = 'match';
                        $j++;
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                    $text[$j] .= $note[$i];
                }
                if ($i == strlen($note) - 1)
                {
                    $res = $this->template_model->get_template_by_tag($template);
                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j]           = 'match';
                        $j++;
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                }
            }
            else
            {
                $text[$j] .= $note[$i];
            }
        }

        $data['note']     = $text;
        $data['template'] = $template_content;



        $val = str_replace(array(
                "\r\n#",
                "\\r\\n#" ), " #", $data_note2);
        $val = str_replace(array(
                "\r\n",
                "\\r\\n" ), " <br>", $val);


        $note2            = $val;
        $template         = '';
        $j                = 0;
        $space            = 0;
        $text             = array();
        $template_content = array();
        for ($i = 0; $i < strlen($note2); $i++)
        {
            if ($note2[$i] == '#')
            {
                $space = 1;
            }

            if ($space == 1)
            {
                if ($note2[$i] != ' ')
                {
                    $template .= $note2[$i];
                }
                else
                {
                    $res = $this->template_model->get_template_by_tag($template);
                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j]           = 'match';
                        $j++;
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                    $text[$j] .= $note2[$i];
                }
                if ($i == strlen($note2) - 1)
                {
                    $res = $this->template_model->get_template_by_tag($template);
                    if ($res)
                    {
                        $template_content[] = $res;
                        $j++;
                        $text[$j]           = 'match';
                        $j++;
                    }
                    else
                    {
                        $text[$j] .= $template;
                    }
                    $template = '';
                    $space    = 0;
                }
            }
            else
            {
                $text[$j] .= $note2[$i];
            }
        }

        $data['note2']     = $text;
        $data['template2'] = $template_content;
    }

}

