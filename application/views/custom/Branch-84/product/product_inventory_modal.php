<style type="text/css">
    #getRowsValue{display: none;}
    /*.add-more>.fa,.removeDiv>.fa{font-size: 20px;  }
    .add-more,.removeDiv{color: #0177a9 !important;cursor: pointer;}
    .add-more{margin-top: 25px;}
    .mt-25{margin-top: 25px}*/
    #getRowsValue{display: none;}
    .alert-custom{color:red;display: none;}
    .require_field{display: none;color:red}
</style>
    <div id="product_inventory_modal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4>Add Product</h4>
                </div>         
                <form id="productForm" method="post" action="<?php echo base_url('product/sales_add_product_inventory'); ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_code">Product Code <span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="product_code" name="product_code" value="<?php echo set_value('product_code'); ?>" tabindex="1">
                                    <span class="validation-color" id="err_product_code"><?php echo form_error('product_code'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_name">Product Name <span class="validation-color">*</span></label>
                                    <input type="hidden" name="product_id" id="product_id" value="0">
                                    <input list="product_name" class="form-control" name="product_name">
                                    <datalist id="product_name" >
                                    </datalist>
                                    <span class="validation-color" id="err_product_name"><?php echo form_error('product_name'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="product_brand">Company<span class="validation-color">*</span></label>
                                    <select class="form-control select2" id="product_brand" name="product_brand">
                                        <option value="0">General</option>
                                        <?php
                                        foreach ($brands as $key => $value) {
                                            echo "<option value='" . $value->brand_id . "'>" . $value->brand_name."</option>";
                                        }
                                        ?>
                                    </select>
                                    <span class="validation-color" id="err_product_brand"></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="product_brand">Opening Stock<span class="validation-color"></span></label>
                                    <input type="number" class="form-control" id="product_opening_stock" name="product_opening_stock" >
                                        <span class="validation-color" id="err_product_opening_stock"></span>
                                </div>
                            </div> 
                        </div>                         
                        <div class="row">
                            <div class="col-md-3 produc_type">
                                <div class="form-group">
                                    <label for="product_type">Product Type<span class="validation-color">*</span></label>
                                    <select class="form-control select2" id="product_type" name="product_type">
                                        <option value="">Select Product Type</option>
                                        <option value="rawmaterial">Raw Material</option>
                                        <option value="semifinishedgoods">Semi Finished Goods</option>
                                        <option value="finishedgoods">Finished Goods</option>
                                    </select>
                                    <span class="validation-color" id="err_product_type"></span>
                                </div>
                            </div>
                            <div class="col-md-3 produc_hsn">
                                <?php
                                    if ($access_settings[0]->tax_type == "gst") {
                                ?>
                                <div class="form-group">
                                    <label for="product_hsn_sac_code">HSN Code <span class="validation-color">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <a href="" data-toggle="modal" data-target="#hsn_modal" class="product_hsn_click pull-right"><span class="fa fa-eye"></span></a>
                                        </div>
                                        <input type="text" class="form-control" id="product_hsn_sac_code" name="service_hsn_sac_code" value="<?php echo set_value('service_hsn_sac_code'); ?>" tabindex="7">
                                    </div>
                                    <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>
                                </div>
                            <?php } ?> 
                            </div>
                            <div class="col-md-3 product_category">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="c_type" name="c_type" value="product">
                                    <label for="product_category">Category <span class="validation-color">*</span></label>
                                    <div class="input-group">
                                        <?php
                                        if (in_array($category_module_id,  $active_add)) {
                                            ?>
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#category_modal" data-name="product" class="pull-right new_category">+</a>
                                            </div>
                                        <?php } ?>
                                        <select class="form-control select2" id="product_category" name="product_category" style="width: 100%;" tabindex="5">
                                            <option value="">Select</option>
                                            <?php
                                            foreach ($product_category as $row) {
                                                echo "<option value='$row->category_id'" . set_select('product_category', $row->category_id) . ">$row->category_name</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <span class="validation-color" id="err_product_category"><?php echo form_error('product_category'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3 product_subcategory">
                                <div class="form-group ">
                                    <label for="product_subcategory">Subcategory</label>
                                    <div class="input-group">       
                                        <?php
                                        if (in_array($subcategory_module_id, $active_add)) {
                                            ?>
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="product" class="pull-right new_subcategory">+</a>
                                            </div>
                                        <?php } ?>
                                        <select class="form-control select2" id="product_subcategory" name="product_subcategory" style="width: 100%;" tabindex="6">
                                            <option value="">Select</option>
                                        </select></div>
                                    <span class="validation-color" id="err_product_subcategory"><?php echo form_error('product_subcategory'); ?></span>
                                    <input id="subcategory_hidden" name="subcategory_hidden" type="hidden">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_batch">Batch<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" name="product_batch" id="product_batch" value="BATCH-01" readonly >
                                    <span class="validation-color" id="err_product_batch"><?php echo form_error('product_batch'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3 product_uom">
                                <div class="form-group ">
                                    <label for="product_unit">Unit of Measurement<span class="validation-color">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <a href="#" data-toggle="modal" data-target="#uom_modal" data-name="product" title="Add UOM" class="pull-right new_tax">+</a>
                                        </div>                                              
                                        <select class="form-control select2" id="product_unit" name="product_unit" style="width: 100%;" tabindex="4">
                                            <option value="">Select UOM</option>
                                            <?php
                                            foreach ($uqc_product as $value) {
                                                echo "<option value='$value->id'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <span class="validation-color" id="err_product_unit"></span>
                                </div>
                            </div>
                            <div class="col-md-3 product_discount">
                                <div class="form-group ">
                                    <label for="product_discount">Discount<!-- <span class="validation-color">*</span> --></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-name="product" data-target="#add_discount_product" class="pull-right new_tax" title="Add Discount">+</a>
                                        </div>                                              
                                        <select class="form-control select2" id="product_discount" name="product_discount" style="width: 100%;" tabindex="4">
                                            <option value="">Select Discount</option>
                                            <?php
                                            foreach ($discount as $value) {
                                                echo "<option value='$value->discount_id'>".(float)$value->discount_value."%</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <span class="validation-color" id="err_product_discount"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_sku">SKU </label>
                                    <input type="text" class="form-control" id="product_sku" name="product_sku" value="" readonly="">
                                    <span class="validation-color" id="product_sku_code"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 product_gst">
                                <div class="form-group">
                                    <label for="gst_tax_product">Tax (GST)</label>
                                    <div class="input-group"><?php
                                        if (in_array($tax_module_id, $active_add)) {
                                            ?>
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#tax_gst_modal" data-name="product" class="pull-right new_tax">+</a>
                                            </div>
                                        <?php } ?>
                                        <select class="form-control select2" id="gst_tax_product" name="gst_tax_product" style="width: 100%;" tabindex="6">
                                            <option value="">Select Tax</option>
                                            <?php
                                            foreach ($tax_gst as $row) {
                                                echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . ">$row->tax_name@" . round($row->tax_value, 2) . "%</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <span class="validation-color" id="err_product_code"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_gst_code">GST Tax Percentage </label>
                                    <input type="text" class="form-control" id="product_gst_code" name="product_gst_code" value="" readonly>
                                    <span class="validation-color" id="product_gst_code"></span>
                                </div>
                            </div>                                    
                            <div class="col-md-3 product_tcs">
                                <div class="form-group">
                                    <label for="tds_tax_product">Tax (TCS)</label>
                                    <div class="input-group">
                                        <?php
                                        if (in_array($tax_module_id, $active_add)) {
                                            ?>
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#tax_tds_modal" data-name="product" class="pull-right new_tax">+</a>
                                            </div>
                                        <?php }
                                        ?>
                                        <select class="form-control select2" id="tds_tax_product" name="tds_tax_product" style="width: 100%;" tabindex="6">
                                            <option value="">Select Tax</option>
                                            <?php
                                            foreach ($tax_tcs as $row) {
                                                echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . ">$row->tax_name(Sec " . $row->section_name . ") @ " . round($row->tax_value, 2) . "%</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <span class="validation-color" id="err_product_code"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_tds_code">TCS Tax Percentage </label>
                                    <input type="text" class="form-control" id="product_tds_code" name="product_tds_code" value="" readonly>
                                    <span class="validation-color" id="err_product_tds_code"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_selling_price">Selling Price</label>
                                    <input type="number" class="form-control" id="product_selling_price" name="product_selling_price" value="" >
                                    <span class="validation-color" id="product_selling"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_mrp">MRP</label>
                                    <input type="number" class="form-control" id="product_mrp" name="product_mrp" value="" >
                                    <span class="validation-color" id="product_mrp_code"></span>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_mrp">Serial Number</label>
                                    <input type="text" class="form-control" id="product_serial" name="product_serial" value="" >
                                    <span class="validation-color" id="product_mrp_code"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_mrp">Product Image </label>
                                    <input type="file" class="form-control" id="product_image" name="product_image" >
                                    <span class="validation-color" id="lblError"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="product_description">Description</label>
                                    <textarea type="text" name="product_description" id="product_description" class="form-control" rows="3"></textarea>
                                    <span class="validation-color" id="err_product_description"></span>
                                </div>
                            </div>                              
                        </div>            
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="product_modal_submit" data-dismiss="modal" class="btn btn-info" >Add</button>
                        <button class="btn btn-default" id="product_inventory_cancel" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var product_ajax = "yes";

    $(document).on('click', '.product_hsn_click', function(){
        $('#hsn_modal .modal-title').text('HSN Lookup');
        $('#sac_table thead th:first-child').text("HSN Code");
    });
</script>
<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>