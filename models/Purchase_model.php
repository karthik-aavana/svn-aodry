<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function editPurchase($item_data, $purchase_id)
    {
        $data          = $this->db->escape_str($item_data);
        $this->db->where(array(
                'purchase_id'   => $purchase_id,
                'delete_status' => 0 ));
        $purchase_item = $this->db->get('purchase_item')->result();
        foreach ($purchase_item as $key => $value)
        {
            if ($value->item_type == 'product')
            {
                $where            = array(
                        'product_id'    => $value->item_id,
                        'delete_status' => 0 );
                $this->db->where($where);
                $product          = $this->db->get('products')->result();
                $product_quantity = bcsub($product[0]->product_quantity, $value->purchase_item_quantity);
                $update_quantity  = array(
                        'product_quantity' => $product_quantity );
                $this->db->where($where);
                $this->db->update('products', $update_quantity);
            }
            else if ($value->item_type == 'product_inventory')
            {
                $where            = array(
                        'product_inventory_varients_id' => $value->item_id,
                        'delete_status'                 => 0 );
                $this->db->where($where);
                $product          = $this->db->get('product_inventory_varients')->result();
                $product_quantity = bcsub($product[0]->quantity, $value->purchase_item_quantity);
                $update_quantity  = array(
                        'quantity' => $product_quantity );
                $this->db->where($where);
                $this->db->update('product_inventory_varients', $update_quantity);

                //update stock history
                $where                   = array(
                        'item_id'        => $value->item_id,
                        'reference_id'   => $purchase_id,
                        'reference_type' => 'purchase',
                        'delete_status'  => 0 );
                $this->db->where($where);
                $history                 = $this->db->get('quantity_history')->result();
                if(isset($history) && $history)
                {
                    $history_quantity        = bcsub($history[0]->quantity, $value->purchase_item_quantity);
                    $update_history_quantity = array(
                            'quantity'        => $history_quantity,
                            'updated_date'    => date('Y-m-d'),
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                    $this->db->where($where);
                    $this->db->update('quantity_history', $update_history_quantity);
                }
            }
        }
        $num_exist_rec = count($purchase_item);
        $num_item_rec  = count($item_data);
        if ($num_exist_rec > $num_item_rec)
        {
            foreach ($purchase_item as $key => $value)
            {
                $this->db->where('purchase_item_id', $value->purchase_item_id);
                $this->db->update('purchase_item', $data[$key]);
                
                $i = $key;
                if (($key + 1) == $num_item_rec)
                {                    
                    break;
                }
            }
            // if(isset($i))
            // {
                for ($j = $i + 1; $j < $num_exist_rec; $j++)
                {
                    $this->db->where('purchase_item_id', $purchase_item[$j]->purchase_item_id);
                    $this->db->update('purchase_item', array(
                            'delete_status' => 1 ));
                }
            // }
        }
        else if ($num_exist_rec < $num_item_rec)
        {
            foreach ($purchase_item as $key => $value)
            {
                $this->db->where('purchase_item_id', $value->purchase_item_id);
                $this->db->update('purchase_item', $data[$key]);
                $i = $key;
            }
            // if(isset($i))
            // {
                for ($j = $i + 1; $j < $num_item_rec; $j++)
                {
                    $this->db->insert('purchase_item', $data[$j]);
                }
            // }
        }
        else
        {
            foreach ($purchase_item as $key => $value)
            {
                $this->db->where('purchase_item_id', $value->purchase_item_id);
                $this->db->update('purchase_item', $data[$key]);
            }
        }
        $this->db->where(array(
                'purchase_id'   => $purchase_id,
                'delete_status' => 0 ));
        $new_purchase_item = $this->db->get('purchase_item')->result();
        foreach ($new_purchase_item as $key => $value)
        {
            if ($value->item_type == 'product')
            {
                $where            = array(
                        'product_id'    => $value->item_id,
                        'delete_status' => 0 );
                $this->db->where($where);
                $product          = $this->db->get('products')->result();
                $product_quantity = bcadd($product[0]->product_quantity, $value->purchase_item_quantity);
                $update_quantity  = array(
                        'product_quantity' => $product_quantity );
                $this->db->where($where);
                $this->db->update('products', $update_quantity);
            }
            else if ($value->item_type == 'product_inventory')
            {
                $where            = array(
                        'product_inventory_varients_id' => $value->item_id,
                        'delete_status'                 => 0 );
                $this->db->where($where);
                $product          = $this->db->get('product_inventory_varients')->result();
                $product_quantity = bcadd($product[0]->quantity, $value->purchase_item_quantity);
                $update_quantity  = array(
                        'quantity' => $product_quantity );
                $this->db->where($where);
                $this->db->update('product_inventory_varients', $update_quantity);

                //update stock history
                $where                   = array(
                        'item_id'        => $value->item_id,
                        'reference_id'   => $purchase_id,
                        'reference_type' => 'purchase',
                        'delete_status'  => 0 );
                $this->db->where($where);
                $history                 = $this->db->get('quantity_history')->result();
                if(isset($history) && $history)
                {
                    $history_quantity        = bcadd($history[0]->quantity, $value->purchase_item_quantity);
                    $update_history_quantity = array(
                            'quantity'        => $history_quantity,
                            'updated_date'    => date('Y-m-d'),
                            'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                    $this->db->where($where);
                    $this->db->update('quantity_history', $update_history_quantity);
                }
            }
        } return true;
    }

    public function deletePurchase($purchase_id)
    {
        $this->db->where(array(
                'purchase_id'   => $purchase_id,
                'delete_status' => 0 ));
        $purchase_item = $this->db->get('purchase_item')->result();
        foreach ($purchase_item as $key => $value)
        {
            if ($value->item_type == 'product')
            {
                $where            = array(
                        'product_id'    => $value->item_id,
                        'delete_status' => 0 );
                $this->db->where($where);
                $product          = $this->db->get('products')->result();
                $product_quantity = bcsub($product[0]->product_quantity, $value->purchase_item_quantity);
                $update_quantity  = array(
                        'product_quantity' => $product_quantity );
                $this->db->where($where);
                $this->db->update('products', $update_quantity);
            }
            else if ($value->item_type == 'product_inventory')
            {
                $where            = array(
                        'product_inventory_varients_id' => $value->item_id,
                        'delete_status'                 => 0 );
                $this->db->where($where);
                $product          = $this->db->get('product_inventory_varients')->result();
                $product_quantity = bcsub($product[0]->quantity, $value->purchase_item_quantity);
                $update_quantity  = array(
                        'quantity' => $product_quantity );
                $this->db->where($where);
                $this->db->update('product_inventory_varients', $update_quantity);


                //update stock history
                $where        = array(
                        'item_id'        => $value->item_id,
                        'reference_id'   => $purchase_id,
                        'reference_type' => 'purchase' );
                $history_data = array(
                        'delete_status'   => 1,
                        'updated_date'    => date('Y-m-d'),
                        'updated_user_id' => $this->session->userdata('SESS_USER_ID') );
                $this->db->where($where);
                $this->db->update('quantity_history', $history_data);
            }
        } $this->db->where(array(
                'purchase_id'   => $purchase_id,
                'delete_status' => 0 ));
        $this->db->update('purchase_item', array(
                'delete_status' => 1 ));
        $this->db->where(array(
                'purchase_id'   => $purchase_id,
                'delete_status' => 0 ));
        $res = $this->db->update('purchase', array(
                'delete_status' => 1 ));
        return $res;
    }

    public function updatePurchaseReturnItem($item_data, $purchase_return_id)
    {
        $this->db->where(array(
                'purchase_return_id' => $purchase_return_id,
                'delete_status'      => 0 ));
        $old_purchase_return_item = $this->db->get('purchase_return_item')->result();
        $data                     = $this->db->escape_str($item_data);
        foreach ($old_purchase_return_item as $key => $value)
        {
            $this->db->where('purchase_return_item_id', $value->purchase_return_item_id);
            $this->db->update('purchase_return_item', $data[$key]);
        } return true;
    }

    public function deletePurchaseReturn($purchase_return_id)
    {
        $this->db->where(array(
                'purchase_return_id' => $purchase_return_id,
                'delete_status'      => 0 ));
        $old_purchase_return_item = $this->db->get('purchase_return_item')->result();
        foreach ($old_purchase_return_item as $key => $value)
        {
            $this->db->where('purchase_return_item_id', $value->purchase_return_item_id);
            $this->db->update('purchase_return_item', array(
                    'delete_status' => 1 ));
        } $this->db->where('purchase_return_id', $purchase_return_id);
        $res = $this->db->update('purchase_return', array(
                'delete_status' => 1 ));
        return $res;
    }

}

