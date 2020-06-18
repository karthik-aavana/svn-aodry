if (typeof expense_ajax === 'undefined' || expense_ajax === null)
{
    var expense_ajax = "no"
}
$(document).ready(function () {
    var expense_name_exist = 0;
    $("#input_expense_code").keypress(function (event) {
        if (event.which == 13) {
            return false;
        }
    });
    $("#expense_submit").click(function (event)
    {
        var expense_name = $('#expense_name').val();
        if (expense_name_exist == 1)
        {
            $("#err_expense_name").text(expense_name + " name already exists!");
            return !1;
        }
        if (expense_name == null || expense_name == "")
        {
            $("#err_expense_name").text("Please Enter Expense Name.");
            return !1;
        } else
        {
            $("#err_expense_name").text("");
        }
        if (!expense_name.match(general_regex))
        {
            $('#err_expense_name').text("Please Enter Valid Expense Name ");
            return !1;
        } else
        {
            $("#err_expense_name").text("");
        }
        if (expense_ajax == 'yes')
        {
            $.ajax({
                url: base_url + 'expense/add_expense_ajax',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'expense_name': $('#expense_name').val(),
                    'expense_tds': $('#expense_tds').val()
                },
                success: function (result) {
                    var data = result['data'];
                    reference_array_data = data;
                    var count_row = ($('#count_row').val()) - 1;
                    //   $('#expense_type_'+count_row).html('');
                    //   $('#expense_type_'+count_row).append('<option value="">Select Expense</option>');
                    // for (i = 0; i < data.length; i++) 
                    // {
                    //     $('#expense_type_'+count_row).append('<option value="' + data[i].expense_id + '">' + data[i].expense_title + '</option>');
                    //  }
                    $('.expense_type').append('<option value="' + result['id'] + '">' + result['value'] + '</option>');
                    // $('#expense_type_'+count_row).val(result['id']).attr("selected", "selected");
                    $('#expense_type_' + count_row).change();
                    $('#expenseCategory')[0].reset();
                }
            });
        }
    });
    $(document).on("click", '#expense_bill_submit', function (event) {
        var invoice_number = $('#invoice_number').val();
        if ($('#invoice_number').val() == "") {
            $('#err_invoice_number').text("Please Enter Invoice Number.");
            $('#invoice_number').focus();
            return !1
        } else {
            $('#err_invoice_number').text(" ");
        }
        if ($('#invoice_date').val() == "" || $('#invoice_date').attr('valid') == '0') {
            $('#err_date').text("Please Select the Date.");
            $('#invoice_date').focus();
            return !1
        }
        if ($('#supplier').val() == "") {
            $('#err_supplier').text("Please Select the supplier.");
            $('#supplier').focus();
            return !1
        }
        /*var supply = $('#nature_of_supply').val();
        if (supply == "" || supply == null) {
            $("#err_nature_supply").text("Please Select Nature of Supply");
            return !1
        } else {
            $("#err_nature_supply").text("")
        }*/
        if ($('#billing_country').val() == "") {
            $('#err_billing_country').text("Please Select the Billing Country.");
            $('#err_billing_country').focus();
            return !1
        }
    
        if ($('#billing_state').val() == "") {
            $('#err_billing_state').text("Please Select the Billing State.");
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
        var tablerowCount = $('#expense_table_body tr').length;
        if (tablerowCount < 1) {
            $("#err_product").text("Please Select Items");
            $('#input_purchase_code').focus();
            return !1
        }
        var grand_total = +$('#total_grand_total').val();
        if (grand_total == "" || grand_total == null || grand_total <= 0) {
            $("#err_product").text("Total Amount Should be equal or greater than Zero.");
            $('#input_purchase_code').focus();
            return !1
        } else {
            $("#err_product").text("")
        }
        var expense_file = $('#expense_file').val();
        if(expense_file)
        {
           var extension = expense_file.split('.').pop().toUpperCase();
           if (extension == "PNG" || extension == "JPG" || extension == "JPEG" || extension == "PDF") {
            $('#error_expence_file').text('');
           } else {
                $('#error_expence_file').text("Invalid file extension " + extension);
                return false;
           }
       }
    });
    $("#expense_name").on("blur", function (event)
    {
        var expense_name = $('#expense_name').val();
        var ledger_id = $('#ledger_id').val();
        if (expense_name.length > 2)
        {
            if (expense_name == null || expense_name == "")
            {
                $("#err_expense_name").text("Please Enter Expense Name.");
                return !1;
            } else
            {
                $("#err_expense_name").text("");
            }
            if (!expense_name.match(general_regex))
            {
                $('#err_expense_name').text("Please Enter Valid Expense Name ");
                return !1;
            } else
            {
                $("#err_expense_name").text("");
            }
        } else
        {
            $("#err_expense_name").text("");
        }
        $.ajax({
            url: base_url + 'ledger/get_check_ledger',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'ledger_name': expense_name,
                'ledger_id': ledger_id
            },
            success: function (result) {
                if (result[0].num > 0)
                {
                    $('#expense_name').val("");
                    $("#err_expense_name").text(expense_name + " name already exists!");
                } else
                {
                    $("#err_expense_name").text("");
                }
            }
        });
    });
   /* $("#expense_name").on("blur", function (event) {
        var expense_name = $('#expense_name').val();
        var ledger_id = $('#ledger_id').val();
        $.ajax({
            url: base_url + 'ledger/get_check_ledger',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'ledger_name': expense_name,
                'ledger_id': ledger_id
            },
            success: function (result) {
                if (result[0].num > 0)
                {
                    $("#err_expense_name").text(expense_name + " name already exists!");
                    expense_name_exist = 1;
                } else
                {
                    $("#err_expense_name").text("");
                    expense_name_exist = 0;
                }
            }
        });
    });*/
});
/*function invoice_number_count() {
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
}*/