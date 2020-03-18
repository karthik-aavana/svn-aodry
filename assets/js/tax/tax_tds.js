if (typeof tax_ajax === 'undefined' || tax_ajax === null)
{
    var tax_ajax = "no"
}
$(document).ready(function ()
{
    var tax_exist = 0;
    $("#add_tax").click(function (event)
    {
        var tax_name = $('#tax_name').val();
        var tax_value = $('#tax_value').val();
        var description = $('#tax_description').val();
        var section = $('#cmb_section').val();
        if (tax_name == null || tax_name == "")
        {
            $("#err_tax_name").text("Please Enter Tax Name.");
            return !1
        } else
        {
            $("#err_tax_name").text("")
        }
        if(section == ""){
            $('#err_tax_name_section').text("Please Select Section");
            return !1;
        }else
        {
            $('#err_tax_name_section').text("");
        }
        if (!tax_name.match(general_regex))
        {
            $('#err_tax_name').text(" Please Enter Valid Tax Name");
            return !1
        } else
        {
            $("#err_tax_name").text("")
        }
        if (tax_value == null || tax_value == "")
        {
            $("#err_tax_value").text("Please Enter Tax Value.");
            return !1
        } else
        {
            $("#err_tax_value").text("")
        }
        if (!tax_value.match(price_regex))
        {
            $('#err_tax_value').text(" Please Enter Valid Tax Percentage ");
            return !1
        } else
        {
            $("#err_tax_value").text("")
        }
        if (tax_value > 100){
            $("#err_tax_value").text("Please Enter Tax Value Between 1 to 100");
            return !1
        } else{
            $("#err_tax_value").text("")
        }
        if (tax_exist > 0)
        {
            $('#err_tax_value').text("The tax rate is already exist.");
            return !1
        } else
        {
            $('#err_tax_value').text("");
        }
        if (description != "")
        {
            if (!description.match(general_regex))
            {
                $('#err_description').text("Please Enter Valid Description");
                return !1
            } else
            {
                $("#err_description").text("")
            }
        }
        if (tax_ajax == "yes")
        {
            $.ajax(
                    {
                        url: base_url + "tax/add_tax_ajax",
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'tax_name': $('#tax_name').val(),
                                    'tax_value': $('#tax_value').val(),
                                    'description': $('#description').val(),
                                    'section_id': $('#cmb_section').val()
                                },
                                 beforeSend: function(){
                                    // Show image container
                                    $("#tax_tds_modal #loader").show();
                                },
                        success: function (result)
                        {
                            var tax_item_type = $('#tax_item_type').val();
                            var data = result.data;
                            if (tax_item_type == "product")
                            {
                                $('#tds_tax').html('');
                                $('#tds_tax').append('<option value="0">No Tax</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    var tax_value = parseFloat(data[i].tax_value).toFixed(2);
                                     $('#tds_tax').append('<option value="' + data[i].tax_id + '">' + data[i].tax_name + '('+data[i].section_name+') @ ' + parseFloat(tax_value) + '%</option>')
                                     $('#tds_tax_product').append('<option value="' + data[i].tax_id + '">' + data[i].tax_name + '('+data[i].section_name+') @ ' + parseFloat(tax_value) + '%</option>')
                                }
                                var tax_value = parseFloat(result.tax_value).toFixed(2);
                                $('#tds_tax_product').val(result.id).attr("selected", "selected");
                                $('#product_tds_code').val(tax_value);
                                //$("#tds_tax_product").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $("#tax_tds_modal").modal('hide');
                                $("#taxForm")[0].reset();
                               // $("#tds_tax").change()
                            } else if (tax_item_type == "service")
                            {
                                $('#tds_tax').html('');
                                $('#tds_tax').append('<option value="0">Select Tax</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    var tax_value = parseFloat(data[i].tax_value).toFixed(2);
                                    $('#tds_tax').append('<option value="' + data[i].tax_id + '">' + data[i].tax_name + '('+data[i].section_name+') @ ' + parseFloat(tax_value) + '%</option>')
                                }
                                $("#tax_tds_modal").modal('hide');
                                var tax_value = parseFloat(result.tax_value).toFixed(2);
                                $('#tds_tax').val(result.id).attr("selected", "selected");
                                $('#service_tds_code').val(tax_value);
                                //$("#tds_tax").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $("#taxForm")[0].reset();
                              //  $("#tds_tax").change()
                            }
                        }
                    })
        }
    });
    $("#tax_name").on("blur", function (event)
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
    });
    $("#tax_value").on("blur", function (event)
    {
        var tax_id = $('#tax_id').val();
        var tax_name = $('#tax_name').val();
        var tax_value = $('#tax_value').val();
         var section_id = $('#cmb_section').val();
        if (tax_value.length > 1)
        {
            if (tax_value == null || tax_value == "")
            {
                $("#err_tax_value").text("Please Enter Tax Value.");
                $('#s_gst').hide();
                return !1
            } else
            {
                $("#err_tax_value").text("")
            }
            if (!tax_value.match(price_regex))
            {
                $('#err_tax_value').text(" Please Enter Valid Tax Percentage ");
                return !1
            } else
            {
                $("#err_tax_value").text("")
            }
            var tax_percentage = $('#tax_value').val();
        
            if (tax_percentage > 100){
                $("#err_tax_value").text("Please Enter Tax Value Between 1 to 100");
                return !1
            } else{
                $("#err_tax_value").text("")
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
                            $('#err_tax_value').text("The tax rate is already exist.");
                             $('#tax_value').val('');
                            tax_exist = 1;
                        } else
                        {
                            $('#err_tax_value').text("");
                            tax_exist = 0;
                        }
                    }
                });
    });
    $("#description").on("blur", function (event)
    {
        var description = $('#tax_description').val();
        if (description != "" && description.length > 1)
        {
            if (!description.match(general_regex))
            {
                $('#err_description').text("Please Enter Valid Description");
                return !1
            } else
            {
                $("#err_description").text("")
            }
        }
    });
})
  
