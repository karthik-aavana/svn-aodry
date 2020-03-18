if (typeof payee_ajax === 'undefined' || payee_ajax === null)
{
    var payee_ajax = "no"
}

$(document).ready(function ()
{
    $("#payee_submit").click(function (event)
    {
        var payee_name = $('#payee_name').val();

        var panno = $('#pan_no').val();

        if (payee_name == null || payee_name == "")
        {
            $("#err_payee_name").text("Please Enter Payee Name.");
            return !1;
        } else
        {
            $("#err_payee_name").text("")
        }
        if (!payee_name.match(general_regex))
        {
            $('#err_payee_name').text("Please Enter Valid Payee Name ");
            return !1;
        } else
        {
            $("#err_payee_name").text("")
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



        if (payee_ajax == 'yes')
        {

            $.ajax(
                    {
                        url: base_url + 'payee/add_payee_ajax',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'payee_name': $('#payee_name').val(),
                                    'pan_no': $('#pan_no').val()

                                },
                        success: function (result)
                        {
                            // alert(result.['id']);

                            var data = result.data;
                            $('#payee').html('');
                            $('#payee').append('<option value="">Select</option>');
                            for (i = 0; i < data.length; i++)
                            {
                                $('#payee').append('<option value="' + data[i].payee_id + '">' + data[i].payee_name + '</option>')
                            }
                            $('#payee').val(result.id).attr("selected", "selected");
                            $('#payee').change();
                            $("#payeeForm")[0].reset();
                        }
                    })
        }
    });
    $("#payee_name").on("blur", function (event)
    {
        var payee_name = $('#payee_name').val();
        if (payee_name.length > 2)
        {
            if (payee_name == null || payee_name == "")
            {
                $("#err_payee_name").text("Please Enter Payee Name.");
                return !1;
            } else
            {
                $("#err_payee_name").text("");
            }
            if (!payee_name.match(general_regex))
            {
                $('#err_payee_name').text("Please Enter Valid Payee Name ");
                return !1;
            } else
            {
                $("#err_payee_name").text("");
            }
        } else
        {
            $("#err_payee_name").text("");
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
                $("#err_pan_no").text("");
            }
            if (panno.length != 10)
            {
                $('#err_pan_no').text("Please Enter 10 Digit Pan Number");
                return !1
            } else
            {
                $("#err_pan_no").text("");
            }
        } else
        {
            $("#err_pan_no").text("");
        }
    });



})
