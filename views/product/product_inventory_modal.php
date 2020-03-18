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
                <form id="productForm" method="post" action="<?php echo base_url('product/ales_add_product_inventory'); ?>">
                    <div class="modal-body">
                        <div id="loader">
                            <h1 class="ml8">
                                <span class="letters-container">
                                    <span class="letters letters-left">
                                        <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                    </span>
                                </span>
                                <span class="circle circle-white"></span>
                                <span class="circle circle-dark"></span>
                                <span class="circle circle-container">
                                    <span class="circle circle-dark-dashed"></span>
                                </span>
                            </h1>
                        </div>
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
                                            <input type="hidden" name="batch_serial" value="1">
                                            <input type="hidden" name="batch_parent_product_id" value="0">
                                            <input type="hidden" name="batch_parent_product_code" value="0">
                                            <datalist id="product_name" >
                                            </datalist>
                                            <span class="validation-color" id="err_product_name"><?php echo form_error('product_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="product_brand">Opening Stock<span class="validation-color"></span></label>
                                             <input type="hidden" id="product_brand" name="product_brand">
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
                                                <option value="finishedgoods" selected="selected">Finished Goods</option>
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
                                                        echo "<option value='$row->tax_id'" . set_select('gst_tax', $row->tax_id . "-" . $row->tax_value) . ">$row->tax_name @ " . round($row->tax_value, 2) . "%</option>";
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
                        <button  id="product_modal_submit" data-dismiss="modal" class="btn btn-info" >Add</button>
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
<!-- <script type="text/javascript">
    $('#product_inventory_modal').on('show.bs.modal', function (e) {
        $(this).css("overflow-y", "auto");
    });
    $(document).ready(function () {
        $("#product_inventory").change(function () {
            var productVarientId = $(this).val();
            if (productVarientId != "")
            {
                $("#product_inventory_id").val(productVarientId);
                $('.get_num').show();
            } else
            {
                $("#product_inventory_id").val('');
            }
        });
        $(document).on('change', '.key_varient', function (event)
        {
            var id = $(this).attr('id');
            var id_index = id.split("_");//get the x value
            var value = $(this).val();
            $.ajax({
                url: base_url + 'varients/get_varient_value',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'varient_id': value
                },
                success: function (result)
                {
                    var data = result;
                    $('#varient_value_' + id_index[2]).html('');
                    for (i = 0; i < data.length; i++)
                    {
                        $('#varient_value_' + id_index[2]).append('<option value="' + data[i].varients_value_id + '">' + data[i].varients_value + '</option>');
                    }
                }
            });
        });
        varients_array = JSON.parse($('#varients_array').val());
        var x = 1;
        var in_array_data = new Array();
        $('#add_more').click(function ()
        {
            if ($('.key_varient').val() != "")
            {
                x++;
                var addVarients = "<div class='row'><div class='col-md-5'><div class='form-group'> <label for='varients_name'>Varients <span class='validation-color'>*</span></label> <select class='form-control select2 key_varient' id='varient_key_' + x + '' name='varient_key[]' style='width: 100%;'><option value=''>Select</option> </select> <span class='validation-color alert-custom' id='err_product_varient_key'></span></div></div><div class='col-md-5'><div class='form-group'> <label for='product_name'>Varient Value<span class='validation-color'>*</span></label> <select multiple='' class='form-control select2 value_varient' id='varient_value_' + x + '' name='varient_value[]'></select> <span class='validation-color' id='err_product_varient_value'></span></div></div><div class='col-md-2'> <button type='button' class='btn btn-info mt-25 removeDiv'>-</button></div></div>";
                $('#newRows').append(addVarients);
                for (var i = 0; i < varients_array.length; i++)
                {
                    $('#varient_key_' + x).append('<option value="' + varients_array[i].varients_id + '">' + varients_array[i].varient_key + '</option>');
                }
                $('.key_varient').select2();
                $('.value_varient').select2();
                call_css();
            }
        });
        $('body').on('click', '.removeDiv', function () {
            $(this).closest('.row').remove();
            x--;
        });
        $(document).on('click', '.removeTd', function () {
            $(this).parent('tr').empty();
        });
        $(document).on('click', '#getRowsValue', function () {
            var numberOfColoumns = ($('.get_num select').length) / 2;
            $('#generate_table_data:last').clone().insertAfter("#generate_table_data:last");
        });
        $("#product_inventory_modal_submit").click(function () {
            var tbl2 = $('.gen_table tbody tr').each(function (e) {
                $(this).find(".diff_varient").each(function () {
                    if ($(this).val() == '') {
                        $(this).closest('tr .diff_varient').next('.require_field').show();
                    } else {
                        $(this).closest('tr .diff_varient').next('.require_field').hide();
                    }
                });
            });
            if ($('.diff_varient').val() == "") {
                return false;
            } else {
                var json = html2json();
                var json1 = get_key_value();
                $("#table_datas").val(json);
                $("#key_value").val(json1);
                $("#product_modal_submit").submit();
            }
        });
        $("#product_inventory_modal_submit").click(function (e) {
            e.preventDefault();
            $.ajax({
                url: base_url + 'product/sales_add_product_inventory',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'barcode_symbology': $("#barcode_symbology").val(),
                    'table_datas': $("#table_datas").val(),
                    'product_inventory_id': $("#product_inventory_id").val(),
                    'key_value': $("#key_value").val(),
                    'var_val': $("#var_val").val(),
                    'product_inventory': $("#product_inventory").val(),
                    'varient_key': $("#varient_key").val(),
                    'varient_value': $("#varient_value").val(),
                    'varients_array': $("varients_array").val()
                },
                success: function (result) {
                }
            });
        });
    });
    function get_dynamic_table() {
        if ($(".value_varient").val() == "") {
            $('.alert-custom').html('Please select Varients and Value');
        } else {
            $('.alert-custom').html('');
            $("#getRowsValue").show();
            $(".barcode_symbology").show();
            $('#genarate_table').empty();
            $("#generate_table_data").empty();
            var numberOfColoumns = ($('.get_num select').length) / 2;
            for (t = 1; t <= numberOfColoumns; t++) {
                var rows = "<th>" + $('#varient_key_' + t + ' option:selected').text() + "</th>";
                var rowsText = $("select#varient_value_" + t + " option:selected").map(function () {
                    return '<option value=' + $(this).val() + ' >' + $(this).text() + '</option>';
                }).get();
                var cols = "<td><select name='var_val[]' class='form-control select2'>" + rowsText + "</select></td>";
                var remove = "<td>remove</td>";
                $('#genarate_table').append(rows);
                $("#generate_table_data").append(cols);
            }
            $('#genarate_table:last').append("<td class='removeTd'><b>Varient Code</b></td>");
            $('#genarate_table:last').append("<td class='removeTd'><b>Varient Name</b></td>");
            $('#genarate_table:last').append("<td class='removeTd'><b>Purchase price</b></td>");
            $('#genarate_table:last').append("<td class='removeTd'><b>Selling Price</b></td>");
            $('#genarate_table:last').append("<td class='removeTd'><b>Unit</b></td>");
            $('#genarate_table:last').append("<td class='removeTd'><b>Quantity</b></td>");
            $('#genarate_table:last').append("<td class='removeTd'><b>Action</b></td>");
            $('#generate_table_data:last').append("<td class='input_purchase'><input class='form-control diff_varient' type='text'><p class='require_field'>required</p></td>");
            $('#generate_table_data:last').append("<td class='input_purchase'><input  class='form-control diff_varient' type='text'><p class='require_field'>required</p></td>");
            $('#generate_table_data:last').append("<td class='input_purchase'><input class='form-control diff_varient' type='text'><p class='require_field'>required</p></td>");
            $('#generate_table_data:last').append("<td class='input_purchase'><input class='form-control diff_varient' type='text'><p class='require_field'>required</p></td>");
            $('#generate_table_data:last').append("<td class='input_purchase'><p class='require_field'>required</p><select class='form-control diff_varient'><?php
                        foreach ($uqc as $value) {
                            echo "<option value='$value->uom - $value->description'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";
                        }
                        ?></select></td>");
            $('#generate_table_data:last').append("<td class='input_purchase'><input class='form-control diff_varient' type='text'><p class='require_field'>required</p></td>");
            $('#generate_table_data:last').append("<td><button type='button' class='btn btn-info removeTd'>-</button></td>");
        }
    }
    function html2json() {
        var json = '{';
        var otArr = [];
        var rowCount = $('#gen_table tbody tr').length;
        var tbl2 = $('.gen_table tbody tr').each(function (e) {
            x = $(this).children();
            var itArr = [];
            var t = 0;
            var l = 0;
            var count = 0;
            var column = $('.gen_table tbody tr').length;
            $(this).find(".diff_varient").each(function () {
                varient_array = ['varient_code', 'varient_name', 'purchase_price', 'selling_price', 'varient_unit', 'quantity'];
                itArr.push('"' + varient_array[l] + '"' + ':' + '"' + this.value + '"');
                l++;
            });
            otArr.push('"' + e + '": {' + itArr.join(',') + '}');
        })
        json += otArr.join(",") + '}'
        return json;
    }
    function get_key_value() {
        var json = '{';
        var otArr = [];
        var rowCount = $('#gen_table tbody tr').length;
        // var i = 1;
        var tbl2 = $('.gen_table tbody tr').each(function (e) {
            x = $(this).children();
            var itArr = [];
            var t = 0;
            var l = 0;
            var count = 0;
            x.each(function () {
                count++;
                if ($(this).find(".select2 option:selected").text() != "") {
                    t++;
                    var r = $('#varient_key_' + t + ' option:selected').val();
                    itArr.push('"' + r + '"' + ':' + '"' + $(this).find('option:selected').val() + '"');
                }
            });
            otArr.push('"' + e + '": {' + itArr.join(',') + '}');
        });
        json += otArr.join(",") + '}'
        return json;
    }
</script> -->
