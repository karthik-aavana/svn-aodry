<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	public function editSalesItem($data1, $sales_id) {
		$data = $this -> db -> escape_str($data1);
		$old_sales_items = $this -> db -> select('id,product_id,purchase_item_id,quantity') -> from('sales_items') -> where('sales_id', $sales_id) -> where('delete_status', 0) -> get() -> result();
		foreach ($old_sales_items as $key => $value) {
			if ($value -> item_type == "product") {
				$this -> db -> where('product_id', $value -> product_id);
				$product = $this -> db -> get('products') -> result();
				$product_qty = bcadd($product[0] -> quantity, $value -> quantity, 0);
				$this -> db -> where('product_id', $value -> product_id);
				$this -> db -> update('products', array('quantity' => $product_qty));
				$this -> db -> where('id', $value -> purchase_item_id);
				$purchase_item = $this -> db -> get('purchase_items') -> result();
				$purchase_item_qty = bcsub($purchase_item[0] -> sales_quantity, $value -> quantity, 0);
				$this -> db -> where('id', $value -> purchase_item_id);
				$this -> db -> update('purchase_items', array('sales_quantity' => $purchase_item_qty));
			}
		}
		if (count($old_sales_items) == count($data)) {
			foreach ($old_sales_items as $key => $value) {
				$this -> db -> where('id', $value -> id);
				$this -> db -> update('sales_items', $data[$key]);
			}
		} else if (count($old_sales_items) < count($data)) {
			foreach ($old_sales_items as $key => $value) {
				$this -> db -> where('id', $value -> id);
				$this -> db -> update('sales_items', $data[$key]);
				$i = $key;
			}
			for ($j = $i + 1; $j < count($data); $j++) {
				$this -> db -> insert('sales_items', $data[$j]);
			}
		} else {
			foreach ($old_sales_items as $key => $value) {
				$this -> db -> where('id', $value -> id);
				$this -> db -> update('sales_items', $data[$key]);
				$i = $key;
				if (($key + 1) == count($data)) {
					break;
				}
			}
			for ($j = $i + 1; $j < count($old_sales_items); $j++) {
				$this -> db -> where('id', $old_sales_items[$j] -> id);
				$this -> db -> update('sales_items', array('delete_status' => 1));
			}
		}
		$new_sales_items = $this -> db -> select('id,product_id,purchase_item_id,quantity') -> from('sales_items') -> where('sales_id', $sales_id) -> where('delete_status', 0) -> get() -> result();
		foreach ($new_sales_items as $key => $value) {
			if ($value -> item_type == "product") {
				$this -> db -> where('product_id', $value -> product_id);
				$product = $this -> db -> get('products') -> result();
				$product_qty = bcsub($product[0] -> quantity, $value -> quantity, 0);
				$this -> db -> where('product_id', $value -> product_id);
				$this -> db -> update('products', array('quantity' => $product_qty));
				$this -> db -> where('id', $value -> purchase_item_id);
				$purchase_item = $this -> db -> get('purchase_items') -> result();
				$purchase_item_qty = bcadd($purchase_item[0] -> sales_quantity, $value -> quantity, 0);
				$this -> db -> where('id', $value -> purchase_item_id);
				$this -> db -> update('purchase_items', array('sales_quantity' => $purchase_item_qty));
			}
		}
		return true;
	}

}
