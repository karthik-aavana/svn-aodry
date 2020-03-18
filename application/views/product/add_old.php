<?php defined('BASEPATH') or exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
    </section>
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('product'); ?>">Product</a></li>
            <li class="active">Add Product</li>
        </ol>
    </div>
    <!-- Main content -->
    <section class="content mt-50">
        <div class="row">
            <!-- right column -->
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Product</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('product')">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="well">
                            <form role="form" id="form" method="post" action="<?php echo base_url('product/add_product'); ?>" encType="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_code">Product Code <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" tabindex="1"  id="ajax_product_code" name="ajax_product_code" value="<?php echo $invoice_number; ?>" <?php

if ($access_settings[0]->invoice_readonly == 'yes')
{
    echo "readonly";
}

?>>
                                            <span class="validation-color" id="err_product_code"><?php echo form_error('product_code'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="product_quantity">Stock</label>
                                            <input type="number" class="form-control" id="product_quantity" name="product_quantity" value="0" tabindex="3">
                                            <span class="validation-color" id="err_product_quantity"></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" class="form-control" id="c_type" name="c_type" value="product">
                                            <label for="product_category">Select Category <span class="validation-color">*</span></label>
                                            <div class="input-group">
                                                                      <?php

if (in_array($category_module_id, $active_add))
{

    ?>
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#category_modal" data-name="product" class="pull-right new_category">+</a>
                                                    </div>
<?php } ?>
                                                    <select class="form-control select2" id="product_category" name="product_category" style="width: 100%;" tabindex="5">
                                                        <option value="">Select</option>
                                                        <?php

foreach ($product_category as $row)
{
    echo "<option value='$row->category_id'" . set_select('product_category', $row->category_id) . ">$row->category_name</option>";
}

?>
                                                    </select>
                                                </div>
                                                <span class="validation-color" id="err_product_category"><?php echo form_error('product_category'); ?></span>
                                            </div>

                                            <div class="form-group">
                                                <label for="product_price">Unit Price <span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" id="product_price" name="product_price" value="<?php echo set_value('product_price'); ?>" tabindex="9">
                                                <span class="validation-color" id="err_product_price"><?php echo form_error('product_price'); ?></span>
                                            </div>
                                            <?php
                                            if($access_settings[0]->tax_type=="gst")
                                            {
                                                ?>
                                            
                                            <div class="form-group">
                                                <label for="product_hsn_sac_code">Product HSN Code </label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                    </div>
                                                    <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="<?php echo set_value('product_hsn_sac_code'); ?>" tabindex="11">
                                                </div>
                                                <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>
                                            </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <label for="product_type">Is it an asset ? <span class="validation-color">*</span></label>
                                                <br/>
                                                <label class="radio-inline">
                                                    <input type="radio" name="product_type" value="asset" class="minimal" /> Yes
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="product_type" value="product" class="minimal" checked="checked" /> No
                                                </label>
                                                <br/>
                                                <span class="validation-color" id="err_product_type"><?php echo form_error('product_type'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_name">Product Name <span class="validation-color">*</span></label>
                                                <input type="hidden" name="product_id" id="product_id" value="0">
                                                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo set_value('product_name'); ?>" tabindex="2">
                                                <span class="validation-color" id="err_product_name"><?php echo form_error('product_name'); ?></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="product_unit">Product Unit <span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="product_unit" name="product_unit" style="width: 100%;" tabindex="4">
                                                    <option value="">Select</option>
                                                    <?php

foreach ($uqc as $value)
{
    echo "<option value='$value->uom - $value->description'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";
}

?>
                                                </select>
                                                <span class="validation-color" id="err_product_unit"><?php echo form_error('product_unit'); ?></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="product_subcategory">Select Subcategory</label>
                                                <div class="input-group">
                                                                                                                      <?php

if (in_array($subcategory_module_id, $active_add))
{

    ?>
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="product" class="pull-right new_subcategory">+</a>
                                                        </div>
<?php } ?>
                                                        <select class="form-control select2" id="product_subcategory" name="product_subcategory" style="width: 100%;" tabindex="6">
                                                            <option value="">Select</option>
                                                        </select></div>
                                                        <span class="validation-color" id="err_product_subcategory"><?php echo form_error('product_subcategory'); ?></span>
                                                    </div>
                        <?php
                        if($access_settings[0]->tax_type=="gst" || $access_settings[0]->tax_type =="single_tax")
                        {
                            ?>
                        
                                                    <div class="form-group">
                                                        <label for="product_tax">Select Product Tax </label>
                                                        <div class="input-group">
                                                      <?php

if (in_array($tax_module_id, $active_add))
{

    ?>
                                                            <div class="input-group-addon">
                                                                <a href="" data-toggle="modal" data-target="#tax_modal" data-name="product" class="pull-right new_tax">+</a>
                                                            </div>
<?php } ?>
                                                            <select class="form-control select2" id="product_tax" name="product_tax" style="width: 100%;" tabindex="10">
                                                                <option value="0">Select</option>
                                                                <?php

foreach ($tax as $row)
{
    echo "<option value='$row->tax_id-$row->tax_value'" . set_select('tax', $row->tax_id) . ">$row->tax_name</option>";
}

?>
                                                            </select>
                                                        </div>
                                                        <span class="validation-color" id="err_product_tax"><?php echo form_error('product_tax'); ?></span>

                                                    </div>
<?php } ?>
    <?php 
    if($access_settings[0]->tds_visible =="yes")
    {

    ?>
                                                        <div class="form-group">
                                                <label for="product_hsn_sac_code">Select Product TDS </label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#tds_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                    </div>
                                                    <input type="text" class="form-control" id="product_tds_code" name="product_tds_code" value="" tabindex="11" readonly>
                                                     <input type="hidden" class="form-control" id="tds_id" name="tds_id" value="" tabindex="11">
                                                </div>
                                                <span class="validation-color" id="err_product_tds_code"></span>
                                            </div>
    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="box-footer">
                                                <button type="submit" id="product_modal_submit" class="btn btn-info">Add</button>
                                                <span class="btn btn-default" id="cancel" onclick="cancel('product')">Cancel</span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!--/.col (right) -->
                        </div></div>
                        <!-- /.row -->
                    </section>
                    <!-- /.content -->
                </div>
                <!-- /.content-wrapper -->
                <!-- Modal -->
                <?php 
$this->load->view('layout/footer');
if (in_array($tax_module_id, $active_add))
{
$this->load->view('tax/tax_modal');
}
$this->load->view('product/hsn_modal');
if (in_array($category_module_id, $active_add))
{
$this->load->view('category/category_modal');
}
if (in_array($subcategory_module_id, $active_add))
{
$this->load->view('subcategory/subcategory_modal');
}
 $this->load->view('product/tds_modal');
?>
                <script type="text/javascript">
					$(document).ready(function() {
						$('#category_type').html("<option value='product'>Product</option>");
						$.ajax({
							url : base_url + 'purchase/get_product_code',
							type : 'POST',
							success : function(data) {
								var parsedJson = $.parseJSON(data);
								var product_code = parsedJson.product_code;
								$("#code").val(product_code);
								$(".modal-body #category_type").val('product');
								// $(".modal-body .type_div").hide();
							}
						});
					});
                </script>
                <script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>
