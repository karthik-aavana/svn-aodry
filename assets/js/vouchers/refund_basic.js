var date_empty = "Please Enter Date.";
var date_invalid = "Please Enter Valid Date";
var paying_by_empty = "Please Select Payment Mode.";
var transaction_type_empty = "Please Enter Transaction Type.";
var bank_name_empty = "Please Enter Bank Name.";
var bank_name_invalid = "Please Enter Valid Bank Name";
var bank_name_length = "Please Enter Bank Name Minimun 3 Character";
var cheque_no_empty = "Please Enter Cheque No.";
var cheque_no_invalid = "Please Enter Valid Cheque No";
var error_payment = "More Than 20000 cant be paid through Cash";
$(document).ready(function ()
{
    $("#receipt_submit").click(function (event)
    {
        var receipt_value = $('#receipt_amount').val();
        var date = $('#voucher_date').val().trim();
        var paying_by = $('#payment_mode').val();
        var receipt_amount = $('#receipt_amount').val().trim();
        var grand_total = parseFloat($('#total_grand_total').val());
        if ($('#customer').val() == "")
        {
            $('#err_customer').text("Please Select the Customer.");
            $('#customer').focus();
            return false;
        } else
        {
            $('#err_customer').text("");
        }
        if ($('#reference_number').val() == "")
        {
            $('#err_reference_number').text("Please Select the Reference Number.");
            $('#reference_number').focus();
            return false;
        } else
        {
            $('#err_reference_number').text("");
        }
        if (isNaN(receipt_amount) || receipt_amount == "")
        {
            receipt_amount = 0;
        }
        if (date == null || date == "" || $('#voucher_date').attr('valid') == '0')
        {
            $("#err_date").text(date_empty);
            $('#voucher_date').focus();
            return false;
        } else
        {
            $("#err_date").text("");
        }
        if (!date.match(date_regex))
        {
            $('#err_date').text(date_invalid);
            return false;
        } else
        {
            $("#err_date").text("");
        }
        if ($('#customer').val() == "")
        {
            $('#err_customer').text("Please Select the Customer.");
            $('#customer').focus();
            return false;
        } else
        {
            $('#err_customer').text("");
        }
        if (receipt_amount == null || receipt_amount == "" || receipt_amount == 0)
        {
            $("#err_receipt_amount").text("Please Enter Receipt Amount.");
            return false;
        } else
        {
            $("#err_receipt_amount").text("");
        }
        if (paying_by == null || paying_by == "")
        {
            $("#err_payment_mode").text(paying_by_empty);
            return false;
        } else
        {
            $("#err_payment_mode").text("");
        }
        if (paying_by == 'Cash' && receipt_amount > 20000)
        {
            $("#err_receipt_amount").text(error_payment);
            return false;
        } else
        {
            $("#err_receipt_amount").text("");
        }
        if (paying_by != "Cash" && paying_by != "")
        {
            var bank_name = $('#bank_name').val();
            var cheque_no = $('#cheque_no').val();
            if (bank_name.length > 0)
            {
                if (!bank_name.match(name_regex))
                {
                    $('#err_bank_name').text(bank_name_invalid);
                    return false;
                } else
                {
                    $("#err_bank_name").text("");
                }
                if (bank_name.length < 3)
                {
                    $('#err_bank_name').text(bank_name_length);
                    return false;
                } else
                {
                    $("#err_bank_name").text("");
                }
            } else
            {
                $('#err_bank_name').text('');
            }
            if (cheque_no > 0)
            {
                if (!cheque_no.match(num_regex)) {
                    $('#err_cheque_no').text(cheque_no_invalid);
                    return false;
                } else
                {
                    $("#err_cheque_no").text("");
                }
            } else {
                $('#err_cheque_no').text('');
            }
        }
        if (grand_total == "" || grand_total == null || grand_total == 0.00 || isNaN(grand_total))
        {
            $("#err_product").text("Please Select Product");
            return false;
        } else
        {
            $("#err_product").text("");
        }
        if (grand_total != receipt_amount)
        {
            $('#err_amount_exceeds').text('Receipt Amount is not matching with item total.');
            return false;
        } else
        {
            $('#err_amount_exceeds').text('');
        }
    });
});
// Invoice number check
$("#voucher_number").on("blur", function (event)
{
    var voucher_number = $('#voucher_number').val();
    var section_area = $("#section_area").val();
    if (voucher_number == null || voucher_number == "") {
        $("#err_voucher_number").text("Please Enter Voucher Number.");
        return false;
    } else
    {
        $("#err_voucher_number").text("");
        if (section_area == "edit_refund_voucher")
        {
            var voucher_number_old = $("#voucher_number_old").val();
            if (voucher_number_old != voucher_number)
            {
                invoice_number_count();
            }
        } else
        {
            invoice_number_count();
        }
    }
});
function invoice_number_count()
{
    var voucher_number = $('#voucher_number').val();
    var module_id = $("#module_id").val();
    var privilege = $("#privilege").val();
    var section_area = $("#section_area").val();
    $.ajax({
        url: base_url + 'general/check_date_reference',
        dataType: 'JSON',
        method: 'POST',
        data: {
            'invoice_number': voucher_number,
            'privilege': privilege,
            'module_id': module_id
        },
        success: function (result) {
            if (result[0]['num'] > 0)
            {
                $('#voucher_number').val("");
                $("#err_voucher_number").text(voucher_number + " already exists!");
                return false;
            } else
            {
                $("#err_voucher_number").text("");
            }
        }
    });
}
