<div id="product_inventory_model" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Add Product</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="productfForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-md-4">
                                    <label for="product_code">Product Code <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="ajax_product_code" name="ajax_product_code" value="<?php echo set_value('product_code'); ?>" tabindex="1" <?php
                                    if ($access_settings[0]->invoice_readonly == 'yes')
                                    {
                                        echo "readonly";
                                    }
                                    ?>>
                                    <span class="validation-color" id="err_product_code"><?php echo form_error('product_code'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="product_name">Product Name <span class="validation-color">*</span></label>
                                    <input type="hidden" name="product_id" id="product_id" value="0">

                                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo set_value('product_name'); ?>" tabindex="2">
                                    <span class="validation-color" id="err_product_name"><?php echo form_error('product_name'); ?></span>
                                </div>


                                <div class="form-group col-md-4">
                                    <label for="product_model_no">Model Number</label>
                                    <input type="text" class="form-control" id="product_model_no" name="product_model_no" value="<?php echo set_value('product_model_no'); ?>" tabindex="3">
                                    <span class="validation-color" id="err_product_model_no"><?php echo form_error('product_model_no'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-4">
                                    <label for="product_price">Price <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="product_price" name="product_price" value="<?php echo set_value('product_price'); ?>" tabindex="5">
                                    <span class="validation-color" id="err_product_price"><?php echo form_error('product_price'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="product_quantity">Stock</label>
                                    <input type="number" class="form-control" id="product_quantity" name="product_quantity" value="<?php echo set_value('product_quantity'); ?>" tabindex="3">
                                    <span class="validation-color" id="err_product_quantity"></span>
                                </div>



                                <div class="form-group col-md-4">
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


                            </div>
                            <div class="col-md-12">


                                <div class="form-group col-md-4">
                                    <input type="hidden" class="form-control" name="c_type" value="product">
                                    <label for="product_category">Select Category <span class="validation-color">*</span></label>
                                    <?php
                                    if (isset($other_modules_present['category_module_id']) && $other_modules_present['category_module_id'] != "")
                                    {
                                        ?>
                                        <a href="" data-toggle="modal" data-target="#category_modal" data-name="product" data-name="product" class="pull-right new_category" >+ Add Category</a>
<?php } ?>

                                    <select class="form-control select2" id="product_category" name="product_category" style="width: 100%;" tabindex="7">
                                        <option value="">Select</option>
                                        <?php
                                        foreach ($product_category as $row)
                                        {
                                            echo "<option value='$row->category_id'" . set_select('product_category', $row->category_id) . ">$row->category_name</option>";
                                        }
                                        ?>
                                    </select>
                                    <span class="validation-color" id="err_product_category"><?php echo form_error('product_category'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="product_subcategory">Select Subcategory <span class="validation-color">*</span></label>

                                    <?php
                                    if (isset($other_modules_present['subcategory_module_id']) && $other_modules_present['subcategory_module_id'] != "")
                                    {
                                        ?>
                                        <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="product" data-name="product" class="pull-right new_subcategory">+ Add Subcategory</a>
<?php } ?>

                                    <select class="form-control select2" id="product_subcategory" name="product_subcategory" style="width: 100%;" tabindex="8">
                                        <option value="">Select</option>
                                    </select>
                                    <span class="validation-color" id="err_product_subcategory"><?php echo form_error('product_subcategory'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="product_hsn_sac_code">Product HSN Code<span class="validation-color">*</span></label><a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span>Product HSN Lookup</span></a>
                                    <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="<?php echo set_value('product_hsn_sac_code'); ?>" tabindex="9">
                                    <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>
                                </div>

                            </div>
                            <div class="col-md-12">

                                <div class="form-group col-md-4">
                                    <label for="product_color">Color</label>
                                    <input type="text" class="form-control" id="product_color" name="product_color" value="<?php echo set_value('product_color'); ?>" tabindex="6">
                                    <span class="validation-color" id="err_product_color"><?php echo form_error('product_color'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="product_tax">Select Product Tax </label>
                                    <?php
                                    if (isset($other_modules_present['tax_module_id']) && $other_modules_present['tax_module_id'] != "")
                                    {
                                        ?>
                                        <a href="" data-toggle="modal" data-target="#tax_modal" data-name="product" data-name="product" class="pull-right new_tax">+ Add Tax</a>
<?php } ?>

                                    <select class="form-control select2" id="product_tax" name="product_tax" style="width: 100%;" tabindex="10">
                                        <option value="0">No Tax</option>
                                        <?php
                                        foreach ($tax as $row)
                                        {
                                            echo "<option value='$row->tax_id-$row->tax_value'" . set_select('product_tax', $row->tax_id - $row->tax_value) . ">$row->tax_name</option>";
                                        }
                                        ?>
                                    </select>
                                    <span class="validation-color" id="err_product_tax"><?php echo form_error('product_tax'); ?></span>
                                    <div id="p-tax">
                                        <input type="hidden" name="product_igst" id="product_igst" value="0.00" />
                                        <input type="hidden" name="product_cgst" id="product_cgst" value="0.00" />
                                        <input type="hidden" name="product_sgst" id="product_sgst" value="0.00" />
                                        <br>IGST : <span id="p_igst">0.00</span>
                                        <br>CGST : <span id="p_cgst">0.00</span>
                                        <br>SGST : <span id="p_sgst">0.00</span>
                                    </div>
                                </div>






                                <div class="form-group col-md-4">
                                    <label for="product_type">Is it an asset ? <span class="validation-color">*</span></label>
                                    <br/>
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="asset" /> Yes
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="product" checked="checked" /> No
                                    </label>
                                    <br/>
                                    <span class="validation-color" id="err_product_type"><?php echo form_error('product_type'); ?></span>
                                </div>
                            </div>





                        </div>
                        <div class="col-sm-12">
                            <div class="box-footer">
                                <button type="submit" id="product_modal_submit" class="btn btn-info" data-dismiss="modal">Add</button>
                                <span class="btn btn-default" id="product_inventory_cancel" data-dismiss="modal">Cancel</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var product_ajax = "yes";
</script>
<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>
<script type="text/javascript">
    $('#product_modal').on('show.bs.modal', function (e) {
        $(this).css("overflow-y", "auto");
    });
</script>
