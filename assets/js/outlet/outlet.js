$(document).ready(function () {      
     $("#input_outlet_code").keypress(function (event) {
        if (event.which == 13) {
            return false;
        }
    });

    $(document).on("click", '#outlet_submit', function (event) {
        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();
        var invoice_number_old = $("#invoice_number_old").val();
        if ($('#invoice_number').val() == "") {
            $('#err_invoice_number').text("Please Enter Invoice Number.");
            $('#invoice_number').focus();
            return !1
        } else {
            $('#err_invoice_number').text(" ");
            if (section_area == "edit_outlet" ) {
                if (invoice_number_old != invoice_number) {
                    invoice_number_count();
                }
            } else {
                invoice_number_count();
            }
        }

        if ($('#invoice_date').val() == "" || $('#invoice_date').attr('valid') == '0') {
            $('#err_date').text("Please Select valid Date.");
            $('#invoice_date').focus();
            return !1
        }
        if ($('#to_branch_id').val() == "") {
            $('#err_to_branch_id').text("Please Select the branch.");
            $('#to_branch_id').focus();
            return !1
        }
        
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
        
        var tablerowCount = $('#outlet_table_body tr').length;
        if (tablerowCount < 1) {
            $("#err_product").text("Please Select Items");
            $('#input_outlet_code').focus();
            return !1
        }
        if($('#outlet_table_body').find('.deleteRow').length <= 0){
            $("#err_product").text("Please Select Items.");
            $('#input_outlet_code').focus();
            return !1
        }else {
            $("#err_product").text("")
        }
        var grand_total = +$('#total_grand_total').val();
        if (grand_total == "" || grand_total == null || grand_total <= 0) {
            $("#err_product").text("Total Amount Should be equal or greater than Zero.");
            $('#input_outlet_code').focus();
            return !1
        } else {
            $("#err_product").text("")
        }
    });
    $("#invoice_date").blur(function (event) {
        var date = $('#invoice_date').val();
        if (date == null || date == "") {
            $("#err_date").text("Please Enter Date");
            $('#invoice_date').focus();
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
    $("#to_branch_id").change(function (event) {
        var customer = $('#to_branch_id').val();
        if (customer == "") {
            $("#err_customer").text("Please Select Customer");
            $('#customer').focus();
            return !1
        } else {
            $("#err_customer").text("")
        }
    });
    $("#total_grand_total").change(function (event) {
        var total_grand_total = $('#total_grand_total').val();
        if (total_grand_total == "") {
            $("#err_product").text("Total Amount Should be equal or greater than Zero.");
            return !1
        } else {
            $("#err_product").text("")
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
            if (section_area == "edit_outlet" || section_area == "edit_outlet" || section_area == "edit_delivery_challan" || section_area == "convert_quotation") {
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
