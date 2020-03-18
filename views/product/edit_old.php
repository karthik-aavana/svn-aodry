<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('product'); ?>">Product</a></li>
                <li class="active">Edit Product</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Product</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('product')">Back</a>
                    </div>
                    <div class="box-body">
                        <div class="well">
                            <form role="form" id="form" method="post" action="<?php echo base_url('product/edit_product'); ?>" encType="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_code">Product Code <span class="validation-color">*</span></label>
                                            <input type="hidden" name="product_id" id="product_id" value="<?php echo $data[0]->product_id; ?>">
                                            <input type="text" class="form-control" tabindex="1" id="ajax_product_code" name="ajax_product_code" value="<?php echo $data[0]->product_code; ?>" <?php
                                            if ($access_settings[0]->invoice_readonly == 'yes')
                                            {
                                                echo "readonly";
                                            }
                                            ?>>
                                            <span class="validation-color" id="err_product_code"><?php echo form_error('product_code'); ?></span>
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
                                                    <?php foreach ($product_category as $cat)
                                                    {
                                                        ?>
                                                        <option value='<?php echo $cat->category_id ?>' <?php
                                                        if ($cat->category_id == $data[0]->product_category_id)
                                                        {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $cat->category_name ?></option>
<?php } ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_category"><?php echo form_error('product_category'); ?></span>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="product_price">Unit Price <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" id="product_price" name="product_price" value="<?php echo precise_amount($data[0]->product_price); ?>" tabindex="9">
                                            <span class="validation-color" id="err_product_price"><?php echo form_error('product_price'); ?></span>
                                        </div>
                                         <?php
                                            if($access_settings[0]->tax_type=="gst" || $data[0]->product_hsn_sac_code !="" )
                                            {
                                                ?>
                                        <div class="form-group">
                                            <label for="product_hsn_sac_code">Product HSN Code</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                </div>
                                                <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="<?php echo $data[0]->product_hsn_sac_code; ?>" tabindex="11">
                                            </div>
                                            <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>
                                        </div>
                                            <?php } ?>
                                        <div class="form-group">
                                            <input type="hidden" name="old_product_type" value="<?php echo $data[0]->product_type; ?>">
                                            <label for="product_type">Is it an asset ? <span class="validation-color">*</span></label>
                                            <br/>
                                            <label class="radio-inline">
                                                <input class="minimal" type="radio" name="product_type" value="asset" <?php if ($data[0]->product_type == 'asset') echo "checked='checked'" ?> /> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input class="minimal" type="radio" name="product_type" value="product" <?php if ($data[0]->product_type == 'product') echo "checked='checked'" ?>/> No
                                            </label>
                                            <br/>
                                            <span class="validation-color" id="err_product_type"><?php echo form_error('product_type'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_name">Product Name <span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $data[0]->product_name; ?>" tabindex="2">
                                            <span class="validation-color" id="err_product_name"><?php echo form_error('product_name'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="product_unit">Product Unit <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="product_unit" name="product_unit" style="width: 100%;" tabindex="4">
                                                <option value="">Select</option>
                                                <?php foreach ($uqc as $vuqc)
                                                {
                                                    ?>
                                                    <option value='<?php echo $vuqc->uom . " - " . $vuqc->description ?>' <?php
                                                            if ($vuqc->uom . " - " . $vuqc->description == $data[0]->product_unit)
                                                            {
                                                                echo "selected";
                                                            }
                                                            ?>><?php echo $vuqc->uom . ' - ' . $vuqc->description ?></option>
<?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_product_unit"><?php echo form_error('product_unit'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="product_subcategory">Select Subcategory </label>
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
                                                    <?php foreach ($product_subcategory as $sub)
                                                    {
                                                        ?>
                                                        <option value='<?php echo $sub->sub_category_id ?>' <?php
                                                                if ($sub->sub_category_id == $data[0]->product_subcategory_id)
                                                                {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $sub->sub_category_name ?></option>
<?php } ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_subcategory"><?php echo form_error('product_subcategory'); ?></span>
                                        </div>
                                          <?php
                        if($access_settings[0]->tax_type=="gst" || $access_settings[0]->tax_type =="single_tax" || $data[0]->product_tax_id !=0 || $data[0]->product_tax_id !="")
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
                                                    <?php foreach ($tax as $t)
                                                    {
                                                        ?>
                                                        <option value='<?php echo $t->tax_id . "-" . $t->tax_value; ?>'
                                                                    <?php
                                                                    if ($t->tax_id == $data[0]->product_tax_id)
                                                                    {
                                                                        echo "selected";
                                                                    }
                                                                    ?>>
    <?php echo $t->tax_name ?>
                                                        </option>
<?php } ?>
                                                </select>
                                            </div>
                                            <span class="validation-color" id="err_product_tax"><?php echo form_error('product_tax'); ?></span>
                                            
                                        </div>
                                                                <?php } ?>
                                             <?php 
    if($access_settings[0]->tds_visible =="yes" || $data[0]->product_tds_id !=0 || $data[0]->product_tds_id != "")
    {

    ?>
                                                  <div class="form-group">
                                                <label for="product_hsn_sac_code">Select Product TDS </label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <a href="" data-toggle="modal" data-target="#tds_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                                    </div>
                                                    <input type="text" class="form-control" id="product_tds_code1" name="product_tds_code1" value="<?php echo precise_amount($data[0]->product_tds_value); ?>" tabindex="11" readonly>
                                                    <input type="hidden" class="form-control" id="product_tds_code" name="product_tds_code" value="<?php echo precise_amount($data[0]->product_tds_value); ?>" tabindex="11">
                                                     
                                                     <input type="hidden" class="form-control" id="tds_id" name="tds_id" value="<?php echo $data[0]->product_tds_id; ?>" tabindex="11">
                                                </div>
                                                <span class="validation-color" id="err_product_tds_code"></span>
                                            </div>
    <?php } ?>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button class="btn btn-default" id="cancel" onclick="cancel('product')">Cancel</button>
                                    <button type="submit" id="product_modal_submit" class="btn btn-info">Update</button>
                                </div>
                        </div>
                        </form>
                    </div>>
                </div>
            </div>
        </div>
    </section>
</div>
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
    $(document).ready(function () {
        $('#category_type').html("<option value='product'>Product</option>");
    });
</script>
<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>
