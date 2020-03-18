<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

    }

    public function getMaxCategoryId()
    {
        $id = $this->db->select_max('category_id')->get('category')->row()->category_id;
        if ($id == null)
        {
            $category_code = 'CAT-' . sprintf('%06d', intval(1));
        }
        else
        {
            $category_code = 'CAT-' . sprintf('%06d', intval($id) + 1);
        } return $category_code;
    }

    public function getMaxSubcategoryId()
    {
        $id = $this->db->select_max('sub_category_id')->get('sub_category')->row()->sub_category_id;
        if ($id == null)
        {
            $subcategory_code = 'SUBCAT-' . sprintf('%06d', intval(1));
        }
        else
        {
            $subcategory_code = 'SUBCAT-' . sprintf('%06d', intval($id) + 1);
        } return $subcategory_code;
    }

    public function getBarcodeProducts($term)
    {
        return $this->db->select('product_id,product_code,product_name')->from('products')->where('delete_status', 0)->where('(product_code like "%' . $term . '%" or product_name like "%' . $term . '%")', NULL, FALSE)->get()->result_array();
    }

}
