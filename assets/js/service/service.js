if (typeof service_ajax === 'undefined' || service_ajax === null)
{
    var service_ajax = "no"
}
$(document).on("click", ".open_service_modal", function ()
{
    var selected = 'current';
    var module_id = $("#service_module_id").val();
    var privilege = $("#privilege").val();
    $.ajax(
            {
                url: base_url + 'general/generate_date_reference',
                type: 'POST',
                data:
                        {
                            date: selected,
                            privilege: privilege,
                            module_id: module_id
                        },
                success: function (data)
                {
                    var parsedJson = $.parseJSON(data);
                    var service_code = parsedJson.reference_no;
                    $(".modal-body #service_code").val(service_code);
                    $('.modal-body #category_type').html('');
                    $('.modal-body #category_type').append('<option value="service" selected>Service</option>');
                    if (parsedJson.access_settings[0].invoice_readonly == "yes")
                    {
                        $('#service_code').attr('readonly', 'true');
                    }
                }
            })
});
$(document).ready(function () {
    var service_name_exist = 0;
    $("#service_modal_submit").click(function (event)
    {
        var code = $('#service_code').val();
        var name = $('#service_name').val();
        var category = $('#service_category').val();
        var subcategory = $('#service_subcategory').val();
        var hsn_sac_code = $('#service_hsn_sac_code').val();
        var price = $('#service_price').val();
        var tax = $('#service_tax').val();
        var service_unit = $('#service_unit').val();
        // var add_category=$('#category_name').val();
        // return !1;
        var alphnum_regex =/^\s*(?=.*[1-9])\d*(?:\.\d{1,2})?\s*$/;
        //var alphnum_must=/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;
        var amount_regex=/^\d+(\.\d{1,2})?$/;   
        var hsn_regex=/^[0-9]+[0-9 ]+$/; 
        var alphnum_regex_name = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var atlst_alpha=/[-a-zA-Z]+/;
  if (code == null || code == "")
        {
            $("#err_service_code").text("Please Enter Service Code.");
            return !1
        } else
        {
            $("#err_service_code").text("")
        }
        /*if (!code.match(alphnum_regex))
        {
            $('#err_service_name').text("Please Enter Valid Service Code.");
            return !1
        } else
        {
            $("#err_service_name").text("")
        }*/
        if (name == null || name == "")
        {
            $("#err_service_name").text("Please Enter Service Name.");
            return !1
        } else
        {
            $("#err_service_name").text("")
        }
        if (!name.match(alphnum_regex_name))
        {
            $("#err_service_name").text("Please Enter valid Service Name.");
            return !1
        } else
        {
            $("#err_service_name").text("");
        }
        if(!name.match(atlst_alpha)) {
            $("#err_service_name").text("Service name must contain one Charecter Atleast.");
            return !1
        } else {
             $("#err_service_name").text("");
        }
        if(!(name == null || name == "")) {
                var i,temp="";
                var addr_trim=$.trim(name);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_service_name").text("Please Enter Valid Service Name.");
                            return false; 
                        }
                    }
                     if(addr_trim[i] != addr_trim[i+1]) {
                        temp='';
                     } 
                }  
            }
        if (service_name_exist == 1)
        {
            $("#err_service_name").text(name + " Name already exists!");
            return false;
        }
        if (!hsn_sac_code.match(hsn_regex)) {
                $('#err_service_hsn_sac_code').text("Please Enter Valid HSN/SAC Code.");
                return !1
            } else {
                $("#err_service_hsn_sac_code").text("")
            }
      
        
        // if (!name.match(name_regex))
        // {
        //     $('#err_service_name').text("Please Enter Valid Service Name ");
        //     return !1
        // } else
        // {
        //     $("#err_service_name").text("")
        // }
         if (category == "")
        {
            $("#err_service_category").text("Please Select the Category.");
            return !1
        } else
        {
            $("#err_service_category").text("")
        }
        
        if (price == null || price == "")
        {
            $("#err_service_price").text("Please Enter Service Price");
            return !1
        } else
        {
            $("#err_service_price").text("")
        }
        if (!price.match(amount_regex))
        {
            $('#err_service_price').text(" Please Enter Valid Service Price. (Ex - 1000 or 100.10)");
            return !1
        } else
        {
            $("#err_service_price").text("")
        }
        if (service_unit == null || service_unit == "")
        {
            var f_id = $(this).parents('form:first').attr('id');
            $("#"+f_id+" #err_product_unit").text("Please Select Unit Of Measurement");
            return !1
        } else
        {
            $("#err_product_unit").text("")
        }
        
        if (service_ajax == "yes")
        {
            $.ajax(
                    {
                        url: base_url + 'service/add_service_ajax',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'service_code': $('#service_code').val(),
                                    'service_name': $('#service_name').val(),
                                    'service_category': $('#service_category').val(),
                                    'service_subcategory': $('#service_subcategory').val(),
                                    'service_hsn_sac_code': $('#service_hsn_sac_code').val(),
                                    'service_price': $('#service_price').val(),
                                    'gst_tax': $('#gst_tax').val(),
                                    'tds_tax': $('#tds_tax').val(),
                                    'service_tds_code' : $('#service_tds_code').val(),
                                    'service_gst_code' : $('#service_gst_code').val(),
                                    'service_unit' : $('#service_unit').val()
                                    // 'service_igst': $('#service_igst').val(),
                                    // 'service_cgst': $('#service_cgst').val(),
                                    // 'service_sgst': $('#service_sgst').val()
                                },
                                beforeSend: function(){
                                    // Show image container
                                    $("#service_modal #loader").show();
                                },
                        success: function (result)
                        {
                            $("#service_modal #loader").hide();
                            var service_id = result['service_id'];
                            var service_name = result['service_name'];
                            var customer_id = $('#customer').val();
                            var billing_state_id = $('#billing_state').val();
                            var billing_country_id = $('#billing_country').val();
                            $('#input_service_code').val(service_name);
                            $.ajax(
                                    {
                                        url: base_url + 'sales/get_table_items/' + service_id + '-service-yes-gst',
                                        type: "GET",
                                        dataType: "JSON",
                                        success: function (data)
                                        {
                                            $('#table-total').show();
                                            add_row(data);
                                            $('[name=service_hsn_sac_code]').val('');
                                            $('#input_service_code').val('');
                                        }
                                    });
                            $("#serviceForm")[0].reset();
                            $("#service_unit").select2().val('').trigger('change.select2');
                            $("#service_category").select2().val('').trigger('change.select2');
                            $("#service_subcategory").select2().val('').trigger('change.select2');
                            $("#service_tax").select2().val('0').trigger('change.select2');
                            calculateGrandTotal();
                             $("#item_modal").modal("hide");
                             $('body').css('position','relative');
                            //$("#service_tax").change();
                        }
                    })
        }
    });
   /* $("#service_code").on("blur", function (event)
    {
        var code = $('#service_code').val();
        if (code == null || code == "")
        {
            $("#err_service_code").text("Please Enter Code.");
            return !1
        } else
        {
            $("#err_service_code").text("")
        }
        if (!code.match(name_regex))
        {
            $('#err_service_code').text("Please Enter Valid Code.");
            return !1
        } else
        {
            $("#err_service_code").text("")
        }
    });*/
    $("#service_name").on("keyup", function (event)
    {
        var service_name = $('#service_name').val();
        var service_id = $('#service_id').val();
        var name_firstAlpha=/[-a-zA-Z\s0-9]+$/;
        if (service_name.length > 1 && service_name != "")
        {
           /* if (service_name == null || service_name == "")
            {
                $("#err_service_name").text("Please Enter Service Name.");
                return !1
            } else
            {
                $("#err_service_name").text("")
            }*/
            // if (!service_name.match(name_firstAlpha))
            // {
            //     $('#err_service_name').text("Please Enter Valid Service Name ");
            //     return !1
            // } else
            // {
            //     $("#err_service_name").text("")
            // }
        }
        $.ajax({
            url: base_url + 'service/get_check_service',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'service_name': service_name,
                'service_id': service_id
            },
            success: function (result) {
                if (result[0].num > 0)
                {
                    $("#err_service_name").text(service_name + " name already exists!");
                    service_name_exist = 1;
                    return !1;
                } else
                {
                    $("#err_service_name").text("");
                    service_name_exist = 0;
                }
            }
        });
    });
   /* $("#service_name").on("blur", function (event) {
        var service_name = $('#service_name').val();
        var service_id = $('#service_id').val();
        $.ajax({
            url: base_url + 'service/get_check_service',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'service_name': service_name,
                'service_id': service_id
            },
            success: function (result) {
                if (result[0].num > 0)
                {
                    $("#err_service_name").text(service_name + " name already exists!");
                    service_name_exist = 1;
                } else
                {
                    $("#err_service_name").text("");
                    service_name_exist = 0;
                }
            }
        });
    });*/
    $("#service_hsn_sac_code").on("blur", function (event)
    {
        if ($('#service_hsn_sac_code').val() != "")
        {
            var hsn_sac_code = $('#service_hsn_sac_code').val();
            if (hsn_sac_code == null || hsn_sac_code == "")
            {
                $("#err_service_hsn_sac_code").text("Please Enter HSN/SAC Code");
                return !1
            } else
            {
                $("#err_service_hsn_sac_code").text("")
            }
            if (!hsn_sac_code.match(hsn_regex))
            {
                $('#err_service_hsn_sac_code').text("Please Enter Valid HSN/SAC Code.");
                return !1
            } else
            {
                $("#err_service_hsn_sac_code").text("")
            }
        }
    });
    $(document).on('change',"#service_category",function (event)
    {
        var category = $(this).val();
        //$('#service_category').change(function(){
        //var value=$(this).val();
        if(category != "" || category != 0){
            $('#subcategory_disable').removeClass('disable_div');
        }
        else {
            $('#subcategory_disable').addClass('disable_div');
        }
        if (category == "") {
            $("#err_service_category").text("Select the Category.")
            return !1
        } else
        {
            $("#err_service_category").text("");
        }
    });
    $('#service_category').change(function () {
        var id = $(this).val();
        $('#service_subcategory').html('');
        $('#service_subcategory').append('<option value="">Select</option>');
        $.ajax({
            url: base_url + 'product/get_subcategory',
            method: "POST",
            dataType: "JSON",
            data: {id: id},
            success: function (data) {
                for (i = 0; i < data.length; i++) {
                    $('#service_subcategory').append('<option value="' + data[i].sub_category_id + '">' + data[i].sub_category_name + '</option>');
                }
            }
        });
    });
    // $("#service_subcategory").change(function (event)
    // {
    //    var subcategory = $('#service_subcategory').val();
    //    if (subcategory == "")
    //    {
    //       $("#err_service_subcategory").text("Select the Subcategory.");
    //       return !1
    //    }
    //    else
    //    {
    //       $("#err_service_subcategory").text("")
    //    }
    // });
    $("#service_price").on("blur", function (event)
    {
        var num_regex = /^\$?[0-9]+(\.[0-9][0-9])?$/;
        // var alphnum_regex = /^\s*(?=.*[1-9])\d*(?:\.\d{1,2})?\s*$/;
        var amount_regex=/^\d+(\.\d{1,2})?$/;
        var price = $('#service_price').val();
        if (price.length > 1 && price != "")
        {
            if (price == null || price == "")
            {
                $("#err_service_price").text("Please Enter Price");
                return !1
            } else
            {
                $("#err_service_price").text("")
            }
            if (!price.match(amount_regex))
            {
                $('#err_service_price').text("Please Enter Valid Price. (Ex - 1000 or 100.10)");
                return !1
            } else
            {
                $("#err_service_price").text("")
            }
        }
    });
    $('#service_price').on("keyup",function(){
        var price_service = $(this).val();
        var after2_decimal=/^[0-9]+\.[0-9]{3,}$/;
        if(price_service.match(after2_decimal)){
            $('#err_service_price').text("Please Enter Upto  two decimal only");
                return !1
            } else 
            {
                $("#err_service_price").text("");
            }
        });
        /*var service_name=$(this).val();
        var lastchar=service_name.slice(-1);
        var splitChar=service_name.split('.');
        if(lastchar == '.'){
            if(splitChar[1].length > 2){
                $('#err_service_price').text("Please Enter Valid Price.U can Enter Maximum Two decimal value)");
                return !1
            } else {
            $("#err_service_price").text("")
            }
        }*/
    
    $("#service_tax").on("change", function ()
    {
        var tax = $("#service_tax").val();
        if (tax == '0')
        {
            $("#service_tax_value").val(0);
        } else
        {
            result = tax.split("-");
            var igst = parseFloat(result[1]);
            $("#service_tax_value").val(igst.toFixed(2));
        }
    });
    $("#gst_tax").change(function (event){
        var tax_id = $('#gst_tax').val();
        $.ajax({
                url: base_url + 'tax/get_tax_perctage',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'tax_id': tax_id
                },
                success: function (result) {
                    var tax_value_gst = parseFloat(result[0].tax_value).toFixed(2);
                    tax_value_gst_dec_value=tax_value_gst.split('.');
                    console.log(tax_value_gst);
                    if(tax_value_gst_dec_value[1] != '00'){
                        tax_value_gst=tax_value_gst+'%';
                    } else {
                        tax_value_gst=tax_value_gst_dec_value[0]+'%';
                    }
                    $('#service_gst_code').val(tax_value_gst);
                }
            });
});
    $("#tds_tax").change(function (event){
        var tax_id = $('#tds_tax').val();
        $.ajax({
                url: base_url + 'tax/get_tax_perctage',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'tax_id': tax_id
                },
                success: function (result) {
                    
                    var tax_value = parseFloat(result[0].tax_value).toFixed(2);
                    tax_value_tds_dec_value=tax_value.split('.');
                    if(tax_value_tds_dec_value[1] != '00'){
                        tax_value=tax_value+'%';
                    } else {
                        tax_value=tax_value_tds_dec_value[0]+'%';
                    }
                    $('#service_tds_code').val(tax_value);
                }
            });
    });
/*    $("#tax").change(function (event)
    {
       var tax = $('#tax').val();
       if (tax == "")
       {
          $("#err_service_tax").text("");
          $("#s_igst").text('');
          $("#s_cgst").text('');
          $("#s_sgst").text('')
          return !1
       }
       else
       {
          $("#err_service_tax").text("");
          $.ajax(
          {
             url: base_url + 'tax/get_tax_ajax',
             dataType: 'JSON',
             method: 'POST',
             data:
             {
                'tax': $('#tax').val()
             },
             success: function (result)
             {
                var data = result['data'];
                if(data[0])
                     {
                         $("#s_igst").text(data[0].tax_value);
                         $("#s_cgst").text((data[0].tax_value/2).toFixed(2));
                         $("#s_sgst").text((data[0].tax_value/2).toFixed(2));
                     }
                     else
                     {
                         $("#s_igst").text("0.00");
                         $("#s_cgst").text("0.00");
                         $("#s_sgst").text("0.00");
                     }
             }
          })
       }
    })*/
})
