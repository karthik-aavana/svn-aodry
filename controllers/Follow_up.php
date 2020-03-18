<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Follow_up extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model([
                'general_model',
                'ledger_model' ]);
        $this->modules = $this->get_modules();
    }

    function follow_up()
    {
        $follow_date = $this->input->post('update_date');
        $follow_up   = [
                'type_id'    => $this->input->post('sales_id'),
                'type'       => $this->input->post('sales_type'),
                'date'       => date('Y-m-d', strtotime($this->input->post('update_date'))),
                'added_date' => date('Y-m-d'),
                'comments'   => $this->input->post('comments') ];
        if ($this->general_model->insertData('followup', $follow_up))
        {
            $data = [
                    'status' => "success",
                    'date'   => $follow_date ];
            echo json_encode($data);
        }
        else
        {
            echo "fail";
        }
    }

    function follow($id)
    {
        $sales_followup = $this->general_model->getRecords('followup.*', 'followup', [
                'type'    => 'customer',
                'type_id' => $id ], [
                'followup_id' => 'DESC' ]);
        if (!empty($sales_followup))
        {
            echo " <thead><tr style='padding:20px'>          <th>Date</th>          <th>Comments</th>          <th>Added Date</th>                      </tr></thead>";
            echo "<tbody>";
            foreach ($sales_followup as $follow_up)
            {
                echo "<tr>";
                echo "<td>" . $follow_up->date . "</td>";
                echo "<td>" . $follow_up->comments . "</td>";
                echo "<td>" . $follow_up->added_date . "</td>";
                echo "</tr>";
            } echo "</tbody>";
        }
        else
        {
            echo "";
        }
    }

    function follow_purchase($id)
    {
        $sales_followup = $this->general_model->getRecords('followup.*', 'followup', [
                'type'    => 'supplier',
                'type_id' => $id ], [
                'followup_id' => 'DESC' ]);
        if (!empty($sales_followup))
        {
            echo " <thead><tr style='padding:20px'>          <th>Date</th>          <th>Comments</th>          <th>Added Date</th>                      </tr></thead>";
            echo "<tbody>";
            foreach ($sales_followup as $follow_up)
            {
                echo "<tr>";
                echo "<td>" . $follow_up->date . "</td>";
                echo "<td>" . $follow_up->comments . "</td>";
                echo "<td>" . $follow_up->added_date . "</td>";
                echo "</tr>";
            } echo "</tbody>";
        }
        else
        {
            echo "";
        }
    }

    function follow_expense($id)
    {
        $sales_followup = $this->general_model->getRecords('followup.*', 'followup', [
                'type'    => 'expense bill',
                'type_id' => $id ], [
                'followup_id' => 'DESC' ]);
        if (!empty($sales_followup))
        {
            echo " <thead><tr style='padding:20px'>          <th>Date</th>          <th>Comments</th>          <th>Added Date</th>                      </tr></thead>";
            echo "<tbody>";
            foreach ($sales_followup as $follow_up)
            {
                echo "<tr>";
                echo "<td>" . $follow_up->date . "</td>";
                echo "<td>" . $follow_up->comments . "</td>";
                echo "<td>" . $follow_up->added_date . "</td>";
                echo "</tr>";
            } echo "</tbody>";
        }
        else
        {
            echo "";
        }
    }

}

