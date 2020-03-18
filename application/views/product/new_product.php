<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Product</h3>
                        <a class="btn btn-default pull-right back_button" onclick="cancel('product')">Back</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product">Product <span class="validation-color">*</span> </label>
                                    <select class="form-control select2" id="product">
                                        <option>Select Product</option>
                                        <option>product1</option>
                                        <option>product2</option>
                                        <option>product3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_mrp">MRP <span class="validation-color">*</span> </label>
                                    <input type="number" class="form-control" id="product_mrp" name="product_mrp" value="" >
                                    <span class="validation-color" id=""></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_selling_price">Selling Price</label>
                                    <input type="number" class="form-control" id="product_selling_price" name="product_selling_price" value="" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="basic_price">Basic Price</label>
                                    <input type="number" name="" class="form-control" id="basic_price">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="markdown_discount">Markdown Discount</label>
                                    <select class="form-control select2" id="markdown_discount">
                                        <option>Select Markdown Discount</option>
                                        <option>20%</option>
                                        <option>40%</option>
                                        <option>50%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="marginal_discount">Marginal Discount</label>
                                    <select class="form-control select2" id="marginal_discount">
                                        <option>Select Marginal Discount</option>
                                        <option>20%</option>
                                        <option>40%</option>
                                        <option>50%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gst_tax_product">Tax (GST)</label>
                                    <select class="form-control select2" id="gst_tax_product" name="gst_tax_product">
                                        <option value="">Select Tax</option>
                                        <option>20%</option>
                                        <option>40%</option>
                                        <option>50%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_gst_code">GST Tax Percentage</label>
                                    <input type="text" class="form-control" id="product_gst_code" name="product_gst_code" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_batch">Product Batch</label>
                                    <input type="text" class="form-control" id="product_batch" name="" value="" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_scheme_no">Product Scheme Number</label>
                                    <input type="number" class="form-control" id="product_scheme_no" name="" >
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" id="submit_product" class="btn btn-info">
                                Add
                            </button>
                            <button class="btn btn-default" onclick="cancel('product')">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>