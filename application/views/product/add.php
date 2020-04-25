<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li>
                <a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="<?php echo base_url('product'); ?>">Product</a>
            </li>
            <li class="active">
                Add Product
            </li>
        </ol>
    </div>    
    <form role="form" id="form" method="post" action="<?php echo base_url('product/add_product'); ?>" encType="multipart/form-data">
        <section class="content mt-50">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add Product</h3>
                            <a class="btn btn-default pull-right back_button" id="cancel" onclick1="cancel('product')">Back</a>
                        </div>
                        <div class="box-body">										
                            <div class="row mb-0">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="product_code">Product Code<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" tabindex="1"  id="product_code" name="product_code" value="<?php echo $invoice_number; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_product_code"><?php echo form_error('product_code'); ?></span>
                                        <input type="hidden" name="batch_serial" value="1">
                                        <input type="hidden" name="batch_parent_product_id" value="0">
                                        <input type="hidden" name="batch_parent_product_code" value="0">
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
                                <div class="col-md-3 produc_varient">
                                    <div class="form-group">
                                        <label for="varient">Do we have Variant<span class="validation-color">*</span></label>
                                        <div class="form-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="varient" id="varient1" value="Y" >
                                                    Yes </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="varient" id="varient2" value="N" checked="checked">
                                                    No </label>
                                            </div>
                                        </div>
                                        <span class="validation-color" id="err_product_code"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="assets">Is it an asset..?<span class="validation-color">*</span></label>
                                        <div class="form-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="asset" id="asset1" value="Y">
                                                    Yes </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="asset" id="asset2" value="N" checked="checked">
                                                    No </label>
                                            </div>
                                        </div>
                                        <span class="validation-color" id="err_product_code"></span>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top:0" />
                            <div class="row">
                                <div class="col-md-3 produc_type">
                                    <div class="form-group">
                                        <label for="product_type">Product Type<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="product_type" name="product_type">
                                            <option value="">Select Product Type</option>
                                            <option value="rawmaterial">Raw Material</option>
                                            <option value="semifinishedgoods">Semi Finished Goods</option>
                                            <option value="finishedgoods" selected="selected">Finished Goods</option>
                                        </select>
                                        <span class="validation-color" id="err_product_type"></span>
                                    </div>
                                </div>
                                <div class="col-md-3 produc_hsn">
                                    <div class="form-group">
                                        <label for="product_hsn_sac_code">Product HSN Code<span class="validation-color">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#hsn_modal" class="pull-right"><span class="fa fa-eye"></span></a>
                                            </div>
                                            <input type="text" class="form-control" id="product_hsn_sac_code" name="product_hsn_sac_code" value="<?php echo set_value('service_hsn_sac_code'); ?>" tabindex="11">
                                        </div>
                                        <span class="validation-color" id="err_product_hsn_sac_code"><?php echo form_error('product_hsn_sac_code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3 product_category">
                                    <div class="form-group">
                                        <input type="hidden" class="form-control" id="c_type" name="c_type" value="product">
                                        <label for="product_category">Category <span class="validation-color">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <?php
                                                if (in_array($category_module_id,  $active_add)) {
                                                ?>
                                                    <a href="" data-toggle="modal" data-target="#category_modal" data-name="product" class="pull-right new_category">+</a>
                                                <?php } ?>
                                            </div>
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
                                            <div class="input-group-addon">
                                                <?php
                                                if (in_array($subcategory_module_id, $active_add)) {
                                                ?>
                                                <a href="" data-toggle="modal" data-target="#subcategory_modal" data-name="product" class="pull-right new_subcategory">+</a>
                                                <?php } ?>
                                            </div>
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
                                                <?php
                                                    if (in_array($uqc_module_id, $active_add)) {
                                                ?>
                                                    <a href="#" data-toggle="modal" data-name="product" data-target="#uom_modal" class="pull-right new_tax" title="Add UOM">+</a>
                                                <?php } ?>
                                            </div>												
                                            <select class="form-control select2" id="product_unit" name="product_unit" style="width: 100%;" tabindex="4">
                                                <option value="">Select UOM</option>
                                                <?php
                                                foreach ($uqc as $value) {
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
                                                <?php
                                                if (in_array($discount_module_id, $active_add)) {
                                                ?>
                                                    <a href="#" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-name="product" data-target="#add_discount_product" class="pull-right new_tax" title="Add Discount">+</a>
                                                <?php } ?>
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
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <?php
                                                if (in_array($tax_module_id, $active_add)) {
                                                ?>
                                                <a href="" data-toggle="modal" data-target="#tax_gst_modal" data-name="product" class="pull-right new_tax">+</a>
                                                <?php } ?>
                                            </div>
                                            <select class="form-control select2" id="gst_tax_product" name="gst_tax_product" style="width: 100%;" tabindex="6">
                                                <option value="">Select Tax</option>
                                                <?php
                                                foreach ($tax_gst as $row) {
                                                    echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . ">$row->tax_name @ " . (float)($row->tax_value) . "%</option>";
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
                                            <div class="input-group-addon">
                                                <?php
                                                if (in_array($tax_module_id, $active_add)) {
                                                ?>
                                                    <a href="" data-toggle="modal" data-target="#tax_tds_modal" data-name="product" class="pull-right new_tax">+</a>
                                                <?php } ?>
                                            </div>
                                            <select class="form-control select2" id="tds_tax_product" name="tds_tax_product" style="width: 100%;" tabindex="6">
                                                <option value="">Select Tax</option>
                                                <?php
                                                foreach ($tax_tds as $row) {
                                                    echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . ">$row->tax_name(Sec " . $row->section_name . ") @ " . (float)($row->tax_value) . "%</option>";
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
                                        <span class="validation-color" id="product_tds_code"></span>
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
                                        <label for="product_mrp">MRP </label>
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
                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="product_opening_stock">Opening Stock</label>
                                        <input type="number" class="form-control" id="product_opening_stock" name="product_opening_stock" >
                                        <span class="validation-color" id="err_product_opening_stock"></span>
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
                            <div id="varients-yes">
                                <hr>        
                                <div class="row">                                    
                                    <div class="col-sm-3">
                                        <div class="bg_variants">
                                            <div class="box-header with-border">
                                                <h3 class="box-title ml-0">Add Variant</h3>
                                                <?php $array_varients = htmlspecialchars(json_encode($varients_key)); ?>
                                                <input type="hidden" name="varients_array" id="varients_array" value="<?php echo $array_varients; ?>">
                                            </div>
                                            <div class="mt-15" id="row_0">                                           
                                                <div class="form-group">
                                                    <label>Variant Key</label>
                                                    <select class="form-control select2 key_varient" id="varient_key_1" name="varient_key[]">
                                                        <option value="">Select Variant Key</option>
                                                        <?php
                                                        foreach ($varients_key as $row) {
                                                            echo "<option value='$row->varients_id'" . set_select('category', $row->varients_id) . ">$row->varient_key</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>                                            
                                                <div class="form-group">
                                                    <label>Variant Value</label>
                                                    <select multiple="" class="form-control select2 value_varient" id="varient_value_1" name="varient_value_1[]"></select>
                                                </div> 
                                                <div id="app_row"></div>                                            
                                                <button type="button" class="btn btn-info" id="add_new_row">
                                                    <i class="fa fa-plus"></i>
                                                </button>                                            
                                                <button type="button"  class="btn btn-info"  name="create_combination" id="create_combination">
                                                    Create Combinations
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="box-header with-border">
                                            <h3 class="box-title ml-0">Combinations</h3>                                           
                                        </div>
                                        <table id="list_datatable" class="custom_datatable table table-bordered table-striped table-hover table-responsive mt-15" >
                                            <thead>
                                            <th>Product Code</th>
                                            <th>Product Name with Variant</th>
                                            <th>Action</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        						
                        <div class="box-footer">
                            <button type="submit" id="product_modal_submit" class="btn btn-info">Add</button>
                            <span class="btn btn-default back_button" id="cancel" onclick="cancel('product')">Cancel</span>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('product/hsn_modal');
$this->load->view('category/category_modal');
$this->load->view('subcategory/subcategory_modal');
$this->load->view('uqc/uom_modal');
$this->load->view('discount/discount_modal_product');
//$this->load->view('service/tds_modal');
if (in_array($tax_module_id, $active_add)) {
    $this->load->view('tax/tax_modal_tds');
    $this->load->view('tax/tax_modal_gst');
}
if (in_array($category_module_id, $active_add)) {
    $this->load->view('category/category_modal');
}
if (in_array($subcategory_module_id, $active_add)) {
    $this->load->view('subcategory/subcategory_modal');
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        var comp_table = $('#list_datatable').DataTable();
        varients_array = JSON.parse($('#varients_array').val());
        j = 1;
        $(document).on("click", "#add_new_row", function () {
            if ($(".value_varient ").val() == '') {
                alert_d.text ='please Select Values';
                PNotify.error(alert_d);
            } else {
                var prev_val = $('#varient_key_' + j).val();
                console.log(varients_array.length);
                j++;
                if ($('.key_varient').val() != "") {
                    var app = '<div class="row bg_variants" data-id="row_' + j + '"><div class="form-group"><label>Varient Key</label><select class="form-control select2 key_varient" id="varient_key_' + j + '" name="varient_key[]"><option value="">Select Varient Key</option></select></div><div class="form-group"><label>Varient Key</label> <select multiple="" class="form-control select2 value_varient" id="varient_value_' + j + '" name="varient_value_' + j + '[]"></select></div><a href="javascript:void(0);" id="remove_row" class="btn btn-info" title="Remove Row"><i class="fa fa-minus"></i></a></div>';
                    if (j > varients_array.length) {
                        //console.log(varients_array);
                        $("#err_add_more").text('cannot add more');
                    } else {
                        console.log(varients_array);
                        $("#err_add_more").text('');
                        $("#app_row").append(app);
                    }
                    for (var x = 0; x < varients_array.length; x++) {
                        // alert(varients_array[i].varients_id)

                        console.log(prev_val);
                        if (prev_val != varients_array[x].varients_id) {
                            $('#varient_key_' + j).append('<option value="' + varients_array[x].varients_id + '">' + varients_array[x].varient_key + '</option>');
                        }
                    }

                }
            }
            $('select').select2();
        });
        $(document).on('click', '#remove_row', function () {
            $(this).closest('.row').fadeOut(200);
        });
        var radioValue = $("input[name='varient']:checked").val();
        if (radioValue == 'N') {
            $("#varients-yes").slideUp(400);
            $("#varients-no").slideDown(400);
        }
        if (radioValue == 'Y') {
            $("#varients-no").slideUp(400);
            $("#varients-yes").slideDown(400);
        }
        $("input[name$='varient']").click(function () {
            var radioValue = $("input[name='varient']:checked").val();
            if (radioValue == 'N') {
                $("#varients-yes").slideUp(400);
                $("#varients-no").slideDown(400);
            }
            if (radioValue == 'Y') {
                $("#varients-no").slideUp(400);
                $("#varients-yes").slideDown(400);
            }
        });        
        $('#category_type').html("<option value='product'>Product</option>");
        $.ajax({
            url: base_url + 'purchase/get_product_code',
            type: 'POST',
            success: function (data) {
                var parsedJson = $.parseJSON(data);
                var product_code = parsedJson.product_code;
                $("#code").val(product_code);
                $(".modal-body #category_type").val('product');
                // $(".modal-body .type_div").hide();
            }
        });
        $(document).on('change', '.key_varient', function (event) {
            var id = $(this).attr('id');
            var id_index = id.split("_");//get the x value
            console.log(id_index);
            var value = $(this).val();
            console.log(value);
            $.ajax({
                url: base_url + 'varients/get_varient_value',
                dataType: 'JSON',
                method: 'POST',
                data: {'varient_id': value},
                success: function (result) {
                    var data = result;
                    $('#varient_value_' + id_index[2]).html('');
                    for (i = 0; i < data.length; i++) {
                        $('#varient_value_' + id_index[2]).append('<option value="' + data[i].varients_value_id + '">' + data[i].varients_value + '</option>');
                    }
                }
            });
        });
        $("#create_combination").click(function (event) {
            var arr = [];
            var arr1 = [];
            //var i = 0;
            $.each($(".value_varient"), function () {
                var id = $(this).attr('id');
                var val = $('#' + id).val();
                arr1.push(val);
            });
            var key2 = document.getElementsByName('varient_key[]');

            for (var i = 0; i < key2.length; i++) {
                var inp1 = key2[i];
                arr.push(inp1.value);
            }
            var jsonvalue = JSON.stringify(arr);
            var jsonkey = JSON.stringify(arr1);
            var product_code = $('#product_code').val();
            comp_table.destroy();
            comp_table = $('#list_datatable').DataTable({
                'ajax': {
                    url: base_url + 'product/Combinations',
                    type: 'post',
                    data: {'value': jsonvalue, 'key': jsonkey, 'product_code': product_code},
                },
                'paging': true,
                'searching': true,
                "bStateSave": true,
                'ordering': false,
                'columns': [
                    {'data': 'product_code'},
                    {'data': 'name'},
                    {'data': 'action'},
                            /*{'data' : 'status'}*/],
                /*'order' : [[0, 'desc']],*/
                "columnDefs": [
                    {"visible": true, "targets": 0}
                ]
            });
        });
        var combination_id = [];
        $.each($("input[name='combination']:checked"), function () {
            combination_id.push($(this).val());
        });
    });
</script>
<script src="<?php echo base_url('assets/js/product/') ?>product.js"></script>
<script>
    $(document).on('keyup', '[name=product_name]', function () {
        var product_name = $(this).val();
        if (product_name != '') {
            $.ajax({
                url: '<?= base_url(); ?>product/getProductname',
                type: 'post',
                dataType: 'json',
                data: {product_name: product_name},
                success: function (j) {
                    if (j.flag) {
                        var opt = '<option value="">Add (other)</option>';
                        $.each(j.data, function (k, v) {
                            opt += "<option value='" + v.product_name + "'>" + v.product_name + "</option>";
                        });
                        $('#product_name').html(opt);
                    } else {
                        $('#product_name').html('');
                    }
                }
            });
        }
    });
</script>
