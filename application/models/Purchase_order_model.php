<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_order_model extends CI_model
{

    function __construct()
    {
        parent::__construct();
    }

    public function editPurchaseOrder($item_data, $purchase_order_id)
    {
        $data                = $this->db->escape_str($item_data);
        $this->db->where('purchase_order_id', $purchase_order_id);
        $this->db->where('delete_status', 0);
        $purchase_order_item = $this->db->get('purchase_order_item')->result();
        $num_exist_rec       = count($purchase_order_item);
        $num_item_rec        = count($item_data);
        if ($num_exist_rec > $num_item_rec)
        {
            foreach ($purchase_order_item as $key => $value)
            {
                $this->db->where('purchase_order_item_id', $value->purchase_order_item_id);
                $this->db->update('purchase_order_item', $data[$key]);
                
                $i = $key;
                if (($key + 1) == $num_item_rec)
                {                    
                    break;
                }
            }
            for ($j = $i + 1; $j < $num_exist_rec; $j++)
            {
                $this->db->where('purchase_order_item_id', $purchase_order_item[$j]->purchase_order_item_id);
                $this->db->update('purchase_order_item', array(
                        'delete_status' => 1 ));
            }
        }
        else if ($num_exist_rec < $num_item_rec)
        {
            foreach ($purchase_order_item as $key => $value)
            {
                $this->db->where('purchase_order_item_id', $value->purchase_order_item_id);
                $this->db->update('purchase_order_item', $data[$key]);
                $i = $key;
            }
            for ($j = $i + 1; $j < $num_item_rec; $j++)
            {
                $this->db->insert('purchase_order_item', $data[$j]);
            }
        }
        else
        {
            foreach ($purchase_order_item as $key => $value)
            {
                $this->db->where('purchase_order_item_id', $value->purchase_order_item_id);
                $this->db->update('purchase_order_item', $data[$key]);
            }
        }
        return true;
    }

}

