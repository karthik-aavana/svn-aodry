if (typeof tax_ajax === 'undefined' || tax_ajax === null)
{
    var tax_ajax = "no"
}
var tax_exist = 0;
$(document).ready(function ()
{
    $("#add_tax_gst").click(function (event)
    {
       
        var description = $('#tax_description').val();
        var tax_name = $('#tax_name_gst').val();
        var tax_value = $('#tax_value_gst').val();
        var section_id = $('#cmb_section_gst').val();
        var price_regex = /^\$?[0-9]+(\.[0-9][0-9])?$/;
        var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
/*        if (tax_name == null || tax_name == "")
        {
            $("#err_tax_name_gst").text("Please Enter Tax Name.");
            return !1
        } else
        {
            $("#err_tax_name_gst").text("")
        }*/
        /*if (!tax_name.match(general_regex))
        {
            $('#err_tax_name_gst').text(" Please Enter Valid Tax Name");
            return !1
        } else
        {
            $("#err_tax_name_gst").text("")
        }*/
        if (tax_value == null || tax_value == "")
        {
            $("#err_tax_value_gst").text("Please Enter Tax Value.");
            return !1
        } else
        {
            $("#err_tax_value_gst").text("")
        }
        if (!tax_value.match(price_regex))
        {
            $('#err_tax_value_gst').text(" Please Enter Valid Tax Percentage ");
            return !1
        } else
        {
            $("#err_tax_value_gst").text("")
        }
        if (tax_exist > 0)
        {
            $('#err_tax_value_gst').text("The tax rate is already exist.");
            return !1
        } else
        {
            $('#err_tax_value_gst').text("");
        }
        if (description != "")
        {
            if (!description.match(general_regex))
            {
                $('#err_description_gst').text("Please Enter Valid Description");
                return !1
            } else
            {
                $("#err_description_gst").text("")
            }
        }
        console.log(tax_ajax,'tax_ajax',$('#tax_value_gst').val(),$('#tax_name_gst').val());

        if (tax_ajax == "yes")
        {
            $.ajax(
                    {
                        url: base_url + "tax/add_tax_ajax",
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'tax_name': $('#tax_name_gst').val(),
                                    'tax_value': $('#tax_value_gst').val(),
                                    'description': $('#tax_description').val(),
                                    'section_id': $('#cmb_section_gst').val()
                                },
                                beforeSend: function(){
                                    // Show image container
                                    $("#tax_gst_modal #loader").show();
                                },
                        success: function (result)
                        {

                            var tax_item_type = $('#tax_item_type_gst').val();
                            var data = result.data;
                             $("#tax_gst_modal #loader").hide();
                            console.log(tax_item_type);

                            if (tax_item_type == "product")
                            {
                                $('#gst_tax').html('');
                                $('#gst_tax').append('<option value="">Select Tax</option>');
                                $('#gst_tax').append('<option value="0">No Tax</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    var tax_value1 = parseFloat(data[i].tax_value).toFixed(2);
                                    $('#gst_tax').append('<option value="' + data[i].tax_id + '" per="'+ parseFloat(tax_value1) +'">' + data[i].tax_name + ' @ ' + parseFloat(tax_value1) + '%</option>')
                                    $('#gst_tax_product').append('<option value="' + data[i].tax_id + '" per="'+ parseFloat(tax_value1) +'">' + data[i].tax_name + ' @ ' + parseFloat(tax_value1) + '%</option>')
                                }
                                var tax_value = parseFloat(result.tax_value).toFixed(2);

                               // var tax_value = parseFloat(result.tax_value).toFixed(2);
                               $('#gst_tax_product').val(result.id).attr("selected", "selected").trigger('change');
                               $('#product_gst_code').val(tax_value);
                                //$("#gst_tax_product").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $("#taxForm")[0].reset();
                               // $("#gst_tax").change();
                            } else if (tax_item_type == "service")
                            {
                                /*$('#gst_tax').html('');
                                $('#gst_tax').append('<option value="">Select</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#gst_tax').append('<option value="' + data[i].tax_id + '">' + data[i].tax_name + '@' + data[i].tax_value +'</option>')
                                }
                                var tax_value = parseFloat(result.tax_value).toFixed(2);
                                $("#gst_tax").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $("#taxForm_gst")[0].reset();
                                $("#gst_tax").change();*/
                                
                                $('#gst_tax').html('');
                                $('#gst_tax').append('<option value="">Select Tax</option>');
                                $('#gst_tax').append('<option value="0">No Tax</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    var tax_value1 = parseFloat(data[i].tax_value).toFixed(2);
                                    $('#gst_tax').append('<option value="' + data[i].tax_id + '">' + data[i].tax_name + ' @ ' + parseFloat(tax_value1) + '%</option>')
                                }
                                    var tax_value = parseFloat(result.tax_value).toFixed(2);
                                    $('#gst_tax').val(result.id).attr("selected", "selected");
                                    $('#service_gst_code').val(tax_value);
                               // console.log(tax_value);
                                //$("#tds_tax").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $("#taxForm_gst")[0].reset();
                              //  $("#tds_tax").change()
                            }
                        }
                    })
        }
    });
   /* $("#tax_name_gst").on("blur", function (event)
    {
        var tax_name = $('#tax_name').val();
        if (tax_name.length > 1)
        {
            if (tax_name == null || tax_name == "")
            {
                $("#err_tax_name").text("Please Enter Tax Name.");
                return !1
            } else
            {
                $("#err_tax_name").text("")
            }
            if (!tax_name.match(general_regex))
            {
                $('#err_tax_name').text(" Please Enter Valid Tax Name");
                return !1
            } else
            {
                $("#err_tax_name").text("")
            }
        }
    });*/
    $("#tax_value_gst").on("blur", function (event)
    {
        
        check_tax();
    });
    $("#tax_value_gst").mouseleave(function(event){
        check_tax();
    });

    function check_tax(){
        var tax_id = $('#tax_id_gst').val();
        var tax_name = $('#tax_name_gst').val();
        var tax_value = $('#tax_value_gst').val();
         var section_id = $('#cmb_section_gst').val();
        if (tax_value.length > 1)
        {
            if (tax_value == null || tax_value == "")
            {
                $("#err_tax_value_gst").text("Please Enter Tax Value.");
                return !1
            } else
            {
                $("#err_tax_value_gst").text("")
            }
            // if (!tax_value.match(price_regex))
            // {
            //     $('#err_tax_value_gst').text(" Please Enter Valid Tax Percentage ");
            //     return !1
            // } else
            // {
            //     $("#err_tax_value_gst").text("")
            // }
            var tax_percentage = $('#tax_value_gst').val();
        
            if (tax_percentage >= 100){
                $("#err_tax_value_gst").text("Please Enter Tax Value Between 1 to 100");
                return !1
            } else{
                $("#err_tax_value_gst").text("")
            }
        }
        $.ajax(
                {
                    url: base_url + 'tax/get_tax',
                    type: "POST",
                    dataType: "json",
                    data: { tax_value: tax_value, tax_id: tax_id, tax_name: tax_name, section_id : section_id },
                    success: function (data)
                    {
                        console.log(data[0].num_tax_value);
                        if (data[0].num_tax_value > 0)
                        {
                            $('#err_tax_value_gst').text("The tax rate is already exist.");
                             // $('#tax_value_gst').val('');
                            tax_exist = 1;
                        } else
                        {
                            $('#err_tax_value_gst').text("");
                            tax_exist = 0;
                        }
                    }
                });
    }        
    $("#tax_description").on("blur", function (event)
    {
        var description = $('#tax_description').val();
        if (description != "" && description.length > 1)
        {
            if (!description.match(general_regex))
            {
                $('#err_description_gst').text("Please Enter Valid Description");
                return !1
            } else
            {
                $("#err_description_gst").text("")
            }
        }
    });
})
  
