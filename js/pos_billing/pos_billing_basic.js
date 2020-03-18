$(document).ready(function ()
{
    $("#pos_billing_submit").click(function (event)
    {

        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();
        var invoice_number_old = $("#invoice_number_old").val();

        if ($('#invoice_number').val() == "")
        {
            $('#err_invoice_number').text("Please Enter Invoice Number.");
            $('#invoice_number').focus();
            return !1
        } else
        {

            $('#err_invoice_number').text("");
            if (section_area == "edit_pos_billing")
            {
                if (invoice_number_old != invoice_number)
                {
                    invoice_number_count();
                }
            } else
            {
                invoice_number_count();
            }

        }
        if ($('#invoice_date').val() == "")
        {
            $('#err_date').text("Please Select the Date.");
            $('#invoice_date').focus();
            return !1
        }

        var grand_total = $('#total_grand_total').val();
        if (grand_total == "" || grand_total == null || grand_total == 0.00)
        {
            $("#err_product").text("Please Select Items");
            $('#input_sales_code').focus();
            return !1
        } else
        {
            $("#err_product").text("")
        }
        var tablerowCount = $('#sales_table_body tr').length;
        if (tablerowCount < 1)
        {
            $("#err_product").text("Please Select Items");
            $('#input_sales_code').focus();
            return !1
        }
    });

    $("#invoice_date").blur(function (event)
    {
        var date = $('#invoice_date').val();
        if (date == null || date == "")
        {
            $("#err_date").text("Please Enter Date");
            $('#date').focus();
            return !1
        } else
        {
            $("#err_date").text("")
        }
        if (!date.match(date_regex))
        {
            $('#err_code').text(" Please Enter Valid Date ");
            $('#date').focus();
            return !1
        } else
        {
            $("#err_date").text("")
        }
    });

    // Invoice number check
    $("#invoice_number").on("blur", function (event)
    {
        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();

        if (invoice_number == null || invoice_number == "") {
            $("#err_invoice_number").text("Please Enter Invoice Number.");
            return false;
        } else
        {
            $("#err_invoice_number").text("");
            if (section_area == "edit_sales" || section_area == "edit_sales" || section_area == "edit_delivery_challan" || section_area == "convert_quotation")
            {
                var invoice_number_old = $("#invoice_number_old").val();
                if (invoice_number_old != invoice_number)
                {
                    invoice_number_count();
                }
            } else
            {
                invoice_number_count();
            }
        }
    });

    $("#mobile").on("blur keyup", function (event)
    {
        if ($('#mobile').val() != "")
        {
            var mobile = $('#mobile').val();
            $('#mobile').val(mobile);
            if (mobile == null || mobile == "") {
                $("#err_mobile").text("Please Enter Mobile.");
                return false;
            } else {
                $("#err_mobile").text("");
            }
            if (!mobile.match(mobile_regex)) {
                $('#err_mobile').text("Please Enter Valid Mobile ");
                return false;
            } else {
                $("#err_mobile").text("");
            }
        }
    });


    $("#email").on("blur keyup", function (event)
    {
        if ($('#email').val() != "")
        {
            var email = $('#email').val();
            $('#email').val(email);
            if (email == null || email == "") {
                $("#err_email").text("Please Enter Email.");
                return false;
            } else {
                $("#err_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_email').text("Please Enter Valid Email Address ");
                return false;
            } else {
                $("#err_email").text("");
            }
        }
    });



});

function invoice_number_count()
{

    var invoice_number = $('#invoice_number').val();
    var module_id = $("#module_id").val();

    var privilege = $("#privilege").val();
    var section_area = $("#section_area").val();


    $.ajax({
        url: base_url + 'general/check_date_reference',
        dataType: 'JSON',
        method: 'POST',
        data: {
            'invoice_number': invoice_number,
            'privilege': privilege,
            'module_id': module_id

        },
        success: function (result) {

            if (result[0]['num'] > 0)
            {
                $('#invoice_number').val("");
                $("#err_invoice_number").text(invoice_number + " already exists!");
                return false;
            } else
            {
                $("#err_invoice_number").text("");
            }

        }
    });
}





