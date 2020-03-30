var alphaBatch = ['0','1','A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z','AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];
if (typeof product_ajax === 'undefined' || product_ajax === null) {
    var product_ajax = "no";
}

$('#product_option').on('change',function(){
    var id = $(this).val();
    var batch = $(this).find('option:selected').attr('batch');
    var batch_val = $(this).find('option:selected').attr('batch_val');
    var serial = $(this).find('option:selected').attr('serial');
    var code = $(this).find('option:selected').attr('code');
    batch = parseInt(batch) + 1;
    
    $('[name=batch_serial]').val(batch);
    $('[name=product_code]').val(code);
    $('[name=product_scheme_no]').val(batch);
    batch = alphaBatch[batch]+batch_val;
    $('#product_batch').val(batch);
    $('[name=batch_parent_product_id]').val(id);
}) 

$(document).on("click", ".open_product_modal", function () {
    var selected = 'current';
    var module_id = $("#product_module_id").val();
    var privilege = $("#privilege").val();
    $.ajax({
        url: base_url + 'general/generate_date_reference',
        type: 'POST',
        data: {
            date: selected,
            privilege: privilege,
            module_id: module_id
        },
        success: function (data) {
            var parsedJson = $.parseJSON(data);
            var product_code = parsedJson.reference_no;
            $(".modal-body #product_code").val(product_code);
            $('.modal-body #category_type').html('');
            $('.modal-body #category_type').append('<option value="product" selected>Product</option>');
            if (parsedJson.access_settings[0].invoice_readonly == "yes") {
            $('#product_code').attr('readonly', 'true');
            }
        }
    });
});
$(document).ready(function () {
    var name_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
    var hsn_regex = /^[0-9]+[0-9 ]+$/;
    var product_name_exist = 0;



    $("#product_modal_edit").click(function (event) {
        var code = $('#product_code').val();
        var name = $('[name=product_name_edit]').val();
        var category = $('#product_category').val();
        var subcategory = $('#product_subcategory').val();
        var hsn_sac_code = $('#product_hsn_sac_code').val();
        var unit = $('#product_unit').val();
        var product_type = $('#product_type').val();
        var product_mrp = $('[name=product_mrp]').val();
        var product_brand = $('#product_brand').val();

        if (product_name_exist == 1) {
            $("#err_product_name").text(name + " name already exists!");
            return false;
        }
        if (code == null || code == "") {
            $("#err_product_code").text("Please Enter product Code.");
            return false;
        } else {
            $("#err_product_code").text("");
        }
        if (!code.match(name_regex)) {
            $('#err_product_code').text("Please Enter Valid product Code.");
            return false;
        } else {
            $("#err_product_code").text("");
        }
        if (name == null || name == "") {
            $("#err_product_name").text("Please Enter Product Name.");
            return false;
        } else {
            $("#err_product_name").text("");
        }
        /*if (!name.match(name_regex)) {
            $('#err_product_name').text("Please Enter Valid Product Name ");
            return false;
        } else {
            $("#err_product_name").text("");
        }*/
        if (product_type == "") {
            $("#err_product_type").text("Select the Product Type.");
            return false;
        } else {
            $("#err_product_type").text("");
        }

        if (category == "" || category == null) {
            $("#err_product_category").text("Select the Category.");
            return false;
        } else {
            $("#err_product_category").text("");
        }
        if (unit == "") {
            $("#err_product_unit").text("Select the unit.");
            return false;
        } else {
            $("#err_product_unit").text("");
        }
        
        if(product_mrp == ''){
            $("#err_product_mrp").text("Please Enter Valid MRP");
            return false;
        }else {
            $("#err_product_mrp").text("");
        }

        if (hsn_sac_code == "" || hsn_sac_code==null) {
            $("#err_product_hsn_sac_code").text("Select the HSN Code.");
            return false;
        } else {
            $("#err_product_hsn_sac_code").text("");
        }
         if (!hsn_sac_code.match(hsn_regex)) {
            $('#err_product_hsn_sac_code').text("Please Enter Valid Product HSN Code");
            return false;
        } else {
            $("#err_product_hsn_sac_code").text("");
        }

        /*if (product_brand == "" || product_brand==null) {
            $("#err_product_brand").text("Select the Brand.");
            return false;
        } else {
            $("#err_product_brand").text("");
        }*/
        
    });
    
    $("#product_modal_submit").click(function (event) {
        var code = $('#product_code').val();
        var name = $('[name=product_name]').val();
        var product_brand = $('[name=product_brand]').val();
        var product_mrp = $('[name=product_mrp]').val();
        var category = $('#product_category').val();
        var subcategory = $('#product_subcategory').val();
        var hsn_sac_code = $('#product_hsn_sac_code').val();
        var unit = $('#product_unit').val();
        var product_type = $('#product_type').val();
        var product_discount = $('#product_discount').val();
        var allowedFiles = [".png", ".jpg", ".gif","jpeg"];
        var fileUpload = $("#product_image");
        var lblError = $("#lblError");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + allowedFiles.join('|') + ")$");
        

        if (product_name_exist == 1) {
            $("#err_product_name").text(name + " name already exists!");
            return false;
        }
        if (code == null || code == "") {
            $("#err_product_code").text("Please Enter product Code.");
            return false;
        } else {
            $("#err_product_code").text("");
        }
        if (!code.match(name_regex)) {
            $('#err_product_code').text("Please Enter Valid product Code.");
            return false;
        } else {
            $("#err_product_code").text("");
        }
        if (name == null || name == "") {
            $("#err_product_name").text("Please Enter Product Name.");
            return false;
        } else {
            $("#err_product_name").text("");
        }
        if (!name.match(name_regex)) {
            $('#err_product_name').text("Please Enter Valid Product Name ");
            return false;
        } else {
            $("#err_product_name").text("");
        }
        if (product_type == "") {
            $("#err_product_type").text("Select the Product Type.");
            return false;
        } else {
            $("#err_product_type").text("");
        }

        if (category == "" || category == null) {
            $("#err_product_category").text("Select the Category.");
            return false;
        } else {
            $("#err_product_category").text("");
        }
        if (unit == "") {
            $("#err_product_unit").text("Select the unit.");
            return false;
        } else {
            $("#err_product_unit").text("");
        }

        if(product_mrp == ''){
            $("#err_product_mrp").text("Please Enter Valid MRP");
            return false;
        }else {
            $("#err_product_mrp").text("");
        }

        if (hsn_sac_code == "" || hsn_sac_code==null) {
            $("#err_product_hsn_sac_code").text("Select the HSN Code.");
            return false;
        } else {
            $("#err_product_hsn_sac_code").text("");
        }
         if (!hsn_sac_code.match(hsn_regex)) {
            $('#err_product_hsn_sac_code').text("Please Enter Valid Product HSN Code");
            return false;
        } else {
            $("#err_product_hsn_sac_code").text("");
        }

        if($("#product_image").val() != ''){
            if (!regex.test(fileUpload.val().toLowerCase())) {
                lblError.html("Please upload files having extensions: <b>" + allowedFiles.join(', ') + "</b> only.");
                return false;
            }else{
                lblError.html("");
            }
        }else{
            lblError.html("");
        }

        var formData = new FormData();
        formData.append('product_code', $('#product_code').val());
        formData.append('product_name', $('[name=product_name]').val());
        formData.append('product_brand', $('[name=product_brand]').val());
        formData.append('product_quantity', $('#product_quantity').val());
        formData.append('product_category', $('#product_category').val());
        formData.append('product_subcategory', $('#product_subcategory').val());
        formData.append('product_hsn_sac_code', $('#product_hsn_sac_code').val());
        formData.append('product_price', $('#product_price').val());
        formData.append('product_unit', $('#product_unit').val());
        formData.append('product_tax', $('#product_tax').val());
        formData.append('tds_tax_product', $('#tds_tax_product').val());
        formData.append('product_tds_code', $('#product_tds_code').val());
        formData.append('tds_id', $('#tds_id').val());
        formData.append('product_discount', $('#product_discount').val());
        formData.append('product_serial', $('#product_serial').val());
        formData.append('product_selling_price', $('#product_selling_price').val());
        formData.append('product_mrp' , $('#product_mrp').val());
        formData.append('gst_tax_product', $('#gst_tax_product').val());
        formData.append('product_gst_code', $('#product_gst_code').val());
        formData.append('product_description', $('#product_description').val());
        formData.append('product_batch', $('#product_batch').val());
        formData.append('product_type', $('#product_type').val());
        formData.append('product_sku' , $('#product_sku').val());
        formData.append('asset' , 'N');
        formData.append('varient' , 'N');
        formData.append('product_image', $('input[type=file]')[0].files[0]);
        if (product_ajax == "yes") {
            // console.log($('#product_code').val())
            $.ajax({
                url: base_url + 'product/add_product_ajax',
                dataType: 'JSON',
                method: 'POST',
                data: formData,
                contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                processData: false,
                    //{
                    // 'product_code': $('#product_code').val(),
                    // 'product_name': $('[name=product_name]').val(),
                    // 'product_quantity': $('#product_quantity').val(),
                    // 'product_category': $('#product_category').val(),
                    // 'product_subcategory': $('#product_subcategory').val(),
                    // 'product_hsn_sac_code': $('#product_hsn_sac_code').val(),
                    // 'product_price': $('#product_price').val(),
                    // 'product_unit': $('#product_unit').val(),
                    // 'product_tax': $('#product_tax').val(),
                    // 'tds_tax_product' : $('#tds_tax_product').val(),
                    // 'product_tds_code': $('#product_tds_code').val(),
                    // 'tds_id': $('#tds_id').val(),
                    // 'product_type': $('#product_type').val(),
                    // 'product_batch': $('#product_batch').val(),
                    // 'product_sku' : $('#product_sku').val(),
                    // 'product_serial':$('#product_serial').val(),
                    // 'product_selling_price': $('#product_selling_price').val(),
                    // 'product_mrp' : $('#product_mrp').val(),
                    // 'gst_tax_product' : $('#gst_tax_product').val(),
                    // 'product_gst_code' : $('#product_gst_code').val(),
                    // 'product_description' : $('#product_description').val(),
                    // 'product_unit' : $('#product_unit').val(),
                    // 'asset' : 'N',
                    // 'varient' : 'N'
                    //}   
                //},
                success: function (result) {
                    var product_name = result['product_name'];
                    var product_id = result['product_id'];
                    var state_id = $('#billing_state').val();
                    var country_id = $('#billing_country').val();
                    $('#input_purchase_code').val(product_name);
                    var addRow = true;
                    if($(document).find('#brand_id').length > 0){
                        var brand_id = $(document).find('#brand_id').val();
                        if(brand_id != 0 && brand_id != product_brand){
                            addRow = false;
                        }
                    }
                    if(addRow){
                        $.ajax({
                            url: base_url + 'purchase/get_table_items/' + product_id + '-product-yes-gst',
                            type: "GET",
                            dataType: "JSON",
                            success: function (data) {
                                $('#table-total').show();
                                add_row(data);
                                $('#input_purchase_code').val('');
                                $('[name=service_hsn_sac_code]').val('');
                            }
                        });
                    }
                    $("#productForm")[0].reset();
                    // $("#product_unit").change();
                    // $("#product_category").change();
                    // $("#product_subcategory").change();
                    $("#product_unit").select2().val('').trigger('change.select2');
                    $("#product_category").select2().val('').trigger('change.select2');
                    $("#product_subcategory").select2().val('').trigger('change.select2');
                    $("#product_tax").select2().val('0').trigger('change.select2');
                    $("#item_modal").modal("hide");
                    $('body').css('position','relative');
                    // $("#product_tax").change();
                }
            });
        }
    });

    $("#ajax_product_code").on("blur", function (event) {
        var code = $('#ajax_product_code').val();
        if (code == null || code == "") {
            $("#err_product_code").text("Please Enter Code.");
            return false;
        } else {
            $("#err_product_code").text("");
        }
        if (!code.match(name_regex)) {
            $('#err_product_code').text("Please Enter Valid Code.");
            return false;
        } else {
            $("#err_product_code").text("");
        }
    });

    $("#product_category").on("change", function (event) {
         var product_code = $('#product_code').val();
         var category_id = $('#product_category').val();
          $.ajax({
            url: base_url + 'product/get_product_sku',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'product_code': product_code,
                'category_id': category_id
            },
            success: function (result) {
                $("#product_sku").val(result);
            }
        });
    })
    var flag_exit = 0;
    var proXhr = null;

    $(".product_code_edit").on("blur", function (event) {
        checkValidArticle(1);
    });

    $("[name=product_name_edit]").on("change", function (event) {
        checkValidArticle(1);
    });

    $("[name=product_code]").on("blur", function (event) {
        var product_id = $('#product_id').val();
        if(!product_id)
        checkValidArticle();
    });

    $("[name=product_name]").on("change", function (event) {
        checkValidArticle();
    });
    
    function checkValidArticle(is_edit = 0){
        var product_code = $('[name=product_code]').val();
        var product_name = $('[name=product_name]').val();
        var product_id = $('#product_id').val();
        if(is_edit){
            var product_code = $('[name=product_code]').val();
            var product_name = $('[name=product_name_edit]').val();
        }
        
        if (product_code == null || product_code == "") {
            $("#err_product_code").text("Please Enter Product Name.");
            return false;
        } else {
            $("#err_product_code").text("");
        }
        if (!product_code.match(name_regex)) {
            $('#err_product_code').text("Please Enter Valid Product Name ");
            return false;
        } else {
            $("#err_product_code").text("");
        }
        $('#product_modal_submit').attr('disabled',false);
        proXhr = $.ajax({
            url: base_url + 'product/get_check_product_code',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'product_code': product_code,
                'product_name': product_name,
                'product_id': product_id
            },
            beforeSend : function()    {           
                if(proXhr != null) {
                    proXhr.abort();
                }
            },
            success: function (result) {
                if (result.length > 0) {
                    $('#product_modal_submit').attr('disabled',true);
                    $("#err_product_code").text(product_code + " article already exists with this product name! Changed Batch!");
                    $('#product_modal_edit').attr('disabled',true);
                    $("#err_product_code").text(product_code + " article already exists with this product name! Changed Batch!");
                    
                    flag_exit = 1;
                } else if(flag_exit == 1) {
                    $('#product_modal_submit').attr('disabled',false);
                    $("#err_product_code").text("");
                    $('#product_modal_edit').attr('disabled',false);
                    $("#err_product_code").text("");
                    flag_exit = 0;
                }
            }
        });
    }

    $("#hsn_sac_code").on("blur", function (event) {
        if ($('#hsn_sac_code').val() != "") {
            var hsn_sac_code = $('#hsn_sac_code').val();
            if (hsn_sac_code == null || hsn_sac_code == "") {
                $("#err_product_hsn_sac_code").text("Please Enter HSN/SAC Code");
                return false;
            } else {
                $("#err_product_hsn_sac_code").text("");
            }
            if (!hsn_sac_code.match(hsn_regex)) {
                $('#err_product_hsn_sac_code').text("Please Enter Valid HSN/SAC Code.");
                return false;
            } else {
                $("#err_product_hsn_sac_code").text("");
            }
        }
    });
    
    $('#product_category').change(function () {
        var id = $(this).val();
        var sub_cat_id = $('#subcategory_hidden').val();
        $('#product_subcategory').html('');
        $('#product_subcategory').append('<option value="">Select</option>');
        $.ajax({
            url: base_url + 'product/get_subcategory',
            method: "POST",
            dataType: "JSON",
            data: {
                id: id
            },
            success: function (data) {
                for (i = 0; i < data.length; i++) {
                    if (sub_cat_id == data[i].sub_category_id) {
                        $('#product_subcategory').append('<option value="' + data[i].sub_category_id + '" selected>' + data[i].sub_category_name + '</option>');
                    } else {
                        $('#product_subcategory').append('<option value="' + data[i].sub_category_id + '">' + data[i].sub_category_name + '</option>');
                    }
                }
            }
        });
    });
  
    $("#product_price").on("blur", function (event) {
        var price = $('#product_price').val();
        if (price == null || price == "") {
            $("#err_product_price").text("Please Enter Price");
            return false;
        } else {
            $("#err_product_price").text("");
        }
        if (!price.match(price_regex)) {
            $('#err_product_price').text("Please Enter Valid Price. (Ex - 1000 or 100.10)");
            return false;
        } else {
            $("#err_product_price").text("");
        }
    });
    $("#gst_tax_product").change(function (event) {
        var tax_id = $('#gst_tax_product').val();
        if (tax_id != "" && tax_id != null && typeof tax_id != 'undefined') {
            $.ajax({
                url: base_url + 'tax/get_tax_perctage',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'tax_id': tax_id
                },
                success: function (result) {
                    var tax_value_gst = parseFloat(result[0].tax_value).toFixed(2);
                    tax_value_gst_dec_value = tax_value_gst.split('.');
                    if (tax_value_gst_dec_value[1] != '00') {
                        tax_value_gst = tax_value_gst + '%';
                    } else {
                        tax_value_gst = tax_value_gst_dec_value[0] + '%';
                    }
                    $('#product_gst_code').val(tax_value_gst);
                }
            });
        } else {
            $('#product_gst_code').val("");
        }
    });
    $("#tds_tax_product").change(function (event) {
        var tax_id = $('#tds_tax_product').val();
        if (tax_id != "") {
            $.ajax({
                url: base_url + 'tax/get_tax_perctage',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'tax_id': tax_id
                },
                success: function (result) {
                    //console.log(result[0].tax_value);
                    var tax_value_tds = parseFloat(result[0].tax_value).toFixed(2);
                    tax_value_tcs_dec_value = tax_value_tds.split('.');
                    if (tax_value_tcs_dec_value[1] != '00') {
                        tax_value_tds = tax_value_tds + '%';
                    } else {
                       tax_value_tds = tax_value_tcs_dec_value[0] + '%';
                    }
                    //console.log(tax_value_tcs_dec_value[1]);
                    $('#product_tds_code').val(tax_value_tds);
                }
            });
        } else {
            $('#product_tds_code').val("");
        }
    });

    
});
