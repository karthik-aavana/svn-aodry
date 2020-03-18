$(document).ready(function () {
    $(document).on("click", '#sales_credit_note_submit,#sales_credit_note_pay_now', function (event) {
        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();
        var invoice_number_old = $("#invoice_number_old").val();

        if ($('#invoice_number').val() == "") {
            $('#err_invoice_number').text("Please Enter Invoice Number.");
            $('#invoice_number').focus();
            return !1
        } else {

            $('#err_invoice_number').text(" ");
            if (section_area == "edit_sales_credit_note" || section_area == "edit_sales_credit_note" || section_area == "edit_delivery_challan" || section_area == "convert_quotation") {
                if (invoice_number_old != invoice_number) {
                    invoice_number_count();
                }
            } else {
                invoice_number_count();
            }

        }
        if ($('#invoice_date').val() == "") {
            $('#err_date').text("Please Select the Date.");
            $('#invoice_date').focus();
            return !1
        }
        if ($('#customer').val() == "") {
            $('#err_customer').text("Please Select the customer.");
            $('#customer').focus();
            return !1
        }
        if ($('#sales_invoice_number').val() == "") {
            $('#err_sales_invoice_number').text("Please Select the Sales reference.");
            $('#sales_invoice_number').focus();
            return !1
        }

        var supply = $('#nature_of_supply').val();

        if (supply == "" || supply == null) {
            $("#err_nature_supply").text("Please Select Nature of Supply");
            return !1
        } else {
            $("#err_nature_supply").text("")
        }
        /*if ($('#billing_country').val() == "") {
            $('#err_billing_country').text("Please Select the Billing Country.");
            $('#err_billing_country').focus();
            return !1
        }*/
        if ($('#billing_state').val() == "") {
            $('#err_billing_state').text("Please Select the Place of Supply.");
            $('#err_billing_state').focus();
            return !1
        }
        if ($('#type_of_supply').val() == "") {
            $('#err_type_of_supply').text("Please Select the Type of Supply.");
            $('#err_type_of_supply').focus();
            return !1
        }
        /*if ($('#shipping_address').val() == "") {
            $('#err_shipping_address').text("Please Select the shipping address.");
            $('#err_shipping_address').focus();
            return !1
        }*/

        var tablerowCount = $('#sales_credit_note_table_body tr').length;
        if (tablerowCount < 1) {
            $("#err_product").text("Please Select Items");
            $('#input_sales_credit_note_code').focus();
            return !1
        }


        var grand_total = +$('#total_grand_total').val();

        if (grand_total == "" || grand_total == null || grand_total <= 0) {

            $("#err_product").text("Total Amount Should be equal or greater than Zero.");
            $('#input_sales_credit_note_code').focus();
            return !1
        } else {
            $("#err_product").text("")
        }


    });


    $("#invoice_date").blur(function (event) {
        var date = $('#invoice_date').val();
        if (date == null || date == "") {
            $("#err_date").text("Please Enter Date");
            $('#date').focus();
            return !1
        } else {
            $("#err_date").text("")
        }
        if (!date.match(date_regex)) {
            $('#err_code').text(" Please Enter Valid Date ");
            $('#date').focus();
            return !1
        } else {
            $("#err_date").text("")
        }
    });
    $("#customer").change(function (event) {
        var customer = $('#customer').val();
        if (customer == "") {
            $("#err_customer").text("Please Select customer");
            $('#customer').focus();
            return !1
        } else {
            $("#err_customer").text("")
        }
    });



    $("#shipping_address").change(function (event) {
        var shipping_address = $('#shipping_address').val();
        if (shipping_address == "") {
            $("#err_shipping_address").text("Please Select shipping address");
            $('#shipping_address').focus();
            return !1
        } else {
            $("#err_shipping_address").text("")
        }
    });

    $("#sales_invoice_number").change(function (event) {
        var sales_invoice_number = $('#sales_invoice_number').val();
        if (sales_invoice_number == "") {
            $("#err_sales_invoice_number").text("Please Select sales invoice number");
            $('#sales_invoice_number').focus();
            return !1
        } else {
            $("#err_sales_invoice_number").text("")
        }
    });
    $("#nature_of_supply").change(function (event) {
        var supply = $('#nature_of_supply').val();
        if (supply == "") {
            $("#err_nature_supply").text("Please Select Nature of Supply");
            return !1
        } else {
            $("#err_nature_supply").text("")
        }
    });
    $("#type_of_supply").change(function (event) {
        var typ_supply = $('#type_of_supply').val();
        if (typ_supply == "") {
            $("#err_type_supply").text("Please Select Type of Supply");
            return !1
        } else {
            $("#err_type_supply").text("")
        }
    });


    // Invoice number check
    $("#invoice_number").on("blur", function (event) {
        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();


        if (invoice_number == null || invoice_number == "") {
            $("#err_invoice_number").text("Please Enter Invoice Number.");
            return false;
        } else {
            $("#err_invoice_number").text("");
            if (section_area == "edit_sales_credit_note" || section_area == "edit_sales" || section_area == "edit_delivery_challan" || section_area == "convert_quotation") {
                var invoice_number_old = $("#invoice_number_old").val();
                if (invoice_number_old != invoice_number) {
                    invoice_number_count();
                }
            } else {
                invoice_number_count();
            }
        }


    });



});

function invoice_number_count() {
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
            if (result[0]['num'] > 0) {
                $('#invoice_number').val("");
                $("#err_invoice_number").text(invoice_number + " already exists!");
                return false;
            } else {
                $("#err_invoice_number").text("");
            }

        }
    });
}
