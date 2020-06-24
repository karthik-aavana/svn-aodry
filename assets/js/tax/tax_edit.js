if (typeof tax_ajax === 'undefined' || tax_ajax === null)
{
    var tax_ajax = "no"
}
var general_regex = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
var price_regex = /^\$?[0-9]+(\.[0-9][0-9])?$/;
$(document).ready(function ()
{
    var tax_exist = 0;
    
    $("#edit_tax_submit").click(function (event)
    {
        var tax_name = $('#tax_name_e').val();
        var tax_value = $('#tax_value_e').val();
        var tax_section=$('#cmb_section_e').val();
        //var tax_section_text=$('#cmb_section_e').text();
        var description = $('#description_e').val();
    
        if (tax_name == null || tax_name == "")
        {
            $("#err_tax_name").text("Please Select Tax Type.");
            return !1
        } else
        {
            $("#err_tax_name").text("")
        }
        if((tax_section == null && tax_name == 'TCS')  || (tax_section == null && tax_name == 'TDS')  || (tax_section == "" && tax_name == 'TDS') || (tax_section == "" && tax_name == 'TCS')){
            $("#err_tax_section_e").text("Please Select Section.");
            return !1
        } else {
             $("#err_tax_section_e").text("")
        }        
        if (tax_value == null || tax_value == "")
        {
            $("#err_tax_value_e").text("Please Enter Tax Value.");
            return !1
        } else
        {
            $("#err_tax_value_e").text("")
        }
        if (!tax_value.match(price_regex))
        {
            $('#err_tax_value_e').text(" Please Enter Valid Tax Percentage ");
            return !1
        } else
        {
            $("#err_tax_value_e").text("")
        }
        if (tax_exist > 0)
        {
            $('#err_tax_value_e').text("The tax rate is already exist.");
            return !1
        } else
        {
            $('#err_tax_value_e').text("");
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
        if(!(tax_value >= 0 && tax_value <= 100)){
            $('#err_tax_value_e').text("Please Enter Valid Tax Percentage.");
            return !1;
        } else {
            $('#err_tax_value_e').text("");
        }
        if (tax_ajax == "no")
        {
            var data = $('#form_tax_e').serialize();
            $.ajax(
            {
                url: base_url + "tax/edit_tax_modal",
                dataType: 'JSON',
                method: 'POST',
                data: data ,
                beforeSend: function(){
                // Show image container
                $("#loader_coco").show();
                },
                success:function(result)
                {         
                    setTimeout(function () {// wait for 5 secs(2)
                            $(document).find('[name=check_item]').trigger('change');
                            //location.reload(); // then reload the page.(3)
                        },500);       
                    if(result.flag){

                        $('#edit_tax').modal('hide');
                        dTable.destroy();
                        dTable = GetAllTax();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d); 
                    }else{
                        alert_d.text = result.msg;
                        PNotify.error(alert_d);   
                    }
                    $("#loader_coco").hide();
                },
                error: function(msg){
                    alert_d.text = 'Something Went Wrong';
                    PNotify.error(alert_d);
                }             
            });
        }
        if (tax_ajax == "yes")
        {
            $.ajax(
                    {
                        url: base_url + "tax/edit_tax_ajax",
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'tax_name': $('#tax_name').val(),
                                    'tax_value': $('#tax_value_e').val(),
                                    'description': $('#description_e').val(),
                                    'section_id': $('#cmb_section_e').val()
                                },
                        success: function (result)
                        {
                            var tax_item_type = $('#tax_item_type').val();
                            var data = result.data;
                            if (tax_item_type == "product")
                            {
                                $('#product_tax').html('');
                                $('#product_tax').append('<option value="0">No Tax</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#product_tax').append('<option value="' + data[i].tax_id + '-' + data[i].tax_value + '">' + data[i].tax_name + '</option>')
                                }
                                var tax_value = parseFloat(result.tax_value).toFixed(2);
                                $("#product_tax").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $("#taxForm")[0].reset();
                                $("#product_tax").change()
                            } else if (tax_item_type = "service")
                            {
                                $('#service_tax').html('');
                                $('#service_tax').append('<option value="0">No Tax</option>');
                                for (i = 0; i < data.length; i++)
                                {
                                    $('#service_tax').append('<option value="' + data[i].tax_id + '-' + data[i].tax_value + '">' + data[i].tax_name + '</option>')
                                }
                                var tax_value = parseFloat(result.tax_value).toFixed(2);
                                $("#service_tax").select2().val(result.id + '-' + tax_value).trigger('change.select2');
                                $("#taxForm")[0].reset();
                                $("#service_tax").change()
                            }
                        }
                    })
        }
    });
    $("#tax_value_e").on("blur keyup", function (event) {
       check_tax();
    });
    $("#tax_name_e").on("change", function (event)  {
       check_tax();
    });
  $("#cmb_section_e").on("change", function (event) {
       check_tax();
    });
    function check_tax() {
        var tax_id = $('#tax_id_e').val();
        var tax_name = $('#tax_name_e').val();
        var tax_value = $('#tax_value_e').val();
        // var section_id = $('#cmb_section').val();
            if (tax_value == null || tax_value == "")
            {
                $("#err_tax_value_e").text("Please Enter Tax Value.");
                return !1
            } else
            {
                $("#err_tax_value_e").text("")
            }
            if (!tax_value.match(price_regex))
            {
                $('#err_tax_value_e').text(" Please Enter Valid Tax Percentage ");
                return !1
            } else
            {
                $("#err_tax_value_e").text("")
            }
            if (parseFloat(tax_value) > 100){
                $("#err_tax_value_e").text("Please Enter Tax Value Between 1 to 100");
                return false;
            } else{
                $("#err_tax_value_e").text("")
            }
            $.ajax(
                {
                    url: base_url + 'tax/get_tax',
                    type: "POST",
                    dataType: "json",
                    data: { tax_value: tax_value, tax_id: tax_id, tax_name: tax_name },
                    success: function (data)
                    {
                        if (data[0].num_tax_value > 0)
                        {
                            $('#err_tax_value_e').text("The tax rate is already exist.");
                             // $('#err_tax_value_e').val('');
                            tax_exist = 1;
                        } else
                        {
                            $('#err_tax_value_e').text("");
                            tax_exist = 0;
                        }
                    }
                });    
    };
    /*$("#description_e").on("blur", function (event)
    {
        var description = $('#description_e').val();
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
    })*/
})
