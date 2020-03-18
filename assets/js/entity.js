if (typeof customer_ajax === 'undefined' || customer_ajax === null)
{
    var customer_ajax = "no"
}

$(document).ready(function ()
{
    $("#customer_modal_submit").click(function (event)
    {
        var entity_name = $('#entity_name').val();

        var panno = $('#pan_no').val();

        if (entity_name == null || entity_name == "")
        {
            $("#err_entity_name").text("Please Enter Customer Name.");
            return !1
        } else
        {
            $("#err_entity_name").text("")
        }
        if (!entity_name.match(name_regex))
        {
            $('#err_entity_name').text("Please Enter Valid Customer Name ");
            return !1
        } else
        {
            $("#err_entity_name").text("")
        }


        if (panno.length > 2)
        {
            $("#pan_no").val(panno.toUpperCase());
            if (!panno.match(pan_regex))
            {
                $('#err_pan_no').text("Please Enter Valid Pan Number (Ex:AAAAA9999A)");
                return !1
            } else
            {
                $("#err_pan_no").text("")
            }
            if (panno.length != 10)
            {
                $('#err_pan_no').text("Please Enter 10 Digit Pan Number");
                return !1
            } else
            {
                $("#err_pan_no").text("")
            }
        }



        if (customer_ajax == 'yes')
        {

            $.ajax(
                    {
                        url: base_url + 'expense_bill/add_entity_ajax',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'customer_name': $('#entity_name').val(),
                                    'panno': $('#pan_no').val()

                                },
                        success: function (result)
                        {
                            // alert(result.['id']);

                            var data = result.data;
                            $('#entity').html('');
                            $('#entity').append('<option value="">Select</option>');
                            for (i = 0; i < data.length; i++)
                            {
                                $('#entity').append('<option value="' + data[i].payee_id + '">' + data[i].payee_name + '</option>')
                            }
                            $('#entity').val(result.id).attr("selected", "selected");
                            $('#entity').change();
                            $("#customerForm")[0].reset();
                        }
                    })
        }
    });
    $("#entity_name").on("blur", function (event)
    {
        var entity_name = $('#entity_name').val();
        if (entity_name == null || entity_name == "")
        {
            $("#err_entity_name").text("Please Enter Customer Name.");
            return !1
        } else
        {
            $("#err_entity_name").text("")
        }
        if (!entity_name.match(name_regex))
        {
            $('#err_entity_name').text("Please Enter Valid Customer Name ");
            return !1
        } else
        {
            $("#err_entity_name").text("")
        }
    });

    $("#pan_no").on("blur", function (event)
    {
        var panno = $('#pan_no').val();
        if (panno != "" && panno.length > 2)
        {
            $("#pan_no").val(panno.toUpperCase());
            if (!panno.match(pan_regex))
            {
                $('#err_pan_no').text("Please Enter Valid Pan Number (Ex:AAAAA9999A)");
                return !1
            } else
            {
                $("#err_pan_no").text("")
            }
            if (panno.length != 10)
            {
                $('#err_pan_no').text("Please Enter 10 Digit Pan Number");
                return !1
            } else
            {
                $("#err_pan_no").text("")
            }
        }
    });



})
