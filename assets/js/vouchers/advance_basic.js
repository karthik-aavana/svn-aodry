var date_empty = "Please Enter Date.";
var date_invalid = "Please Enter Valid Date";
var paying_by_empty = "Please Select Payment Mode.";
var transaction_type_empty = "Please Enter Transaction Type.";
var bank_name_empty = "Please Enter Bank Name.";
var bank_name_invalid = "Please Enter Valid Bank Name";
var bank_name_length = "Please Enter Bank Name Minimun 3 Character";
var cheque_no_empty = "Please Enter Cheque No.";
var cheque_no_invalid = "Please Enter Valid Cheque Number";
var error_payment = "More Than 20000 cant be paid through Cash";
var num_regex = /^[0-9]+$/;
//var num_regex1 = "^[a-zA-Z0-9/]*$";


$(document).ready(function ()
{
    $('#customer').change(function ()
    {
        if ($(this).find('option:selected').text() != 'Select')
        {
            $('#input_sales_code').prop('disabled', false);
            $('.search_sales_code').show();
            $('#err_sales_code').text('');
        } else
        {
            $('.search_sales_code').hide();
            $('#input_sales_code').prop('disabled', true);
            $('#err_sales_code').text('Please select the customer to add advance voucher.');
            $('#err_product').text('');
        }
        $('#myForm input').change();
    });

    $("#receipt_submit").click(function (event)
    {


        var receipt_value = $('#receipt_amount').val();
        var date = $('#voucher_date').val().trim();
        var paying_by = $('#payment_mode').val();
        var receipt_amount = parseFloat($('#receipt_amount').val().trim());
        var grand_total = parseFloat($('#total_grand_total').val());
        var voucher_number = $('#voucher_number').val();
        var section_area = $("#section_area").val();

        if ($('#voucher_number').val() == ""){
            $("#err_voucher_number").text("Please enter voucher number");
            return false;
        }else{
            $("#err_voucher_number").text(" ");
            if (section_area == "edit_advance_voucher"){
                var voucher_number_old = $("#voucher_number_old").val();
                if (voucher_number_old != voucher_number){
                    invoice_number_count();
                }
            }else{
                invoice_number_count();
            }
        }

        if ($('#customer').val() == ""){
            $('#err_customer').text("Please Select the Customer.");
            $('#customer').focus();
            return false;
        }else{
            $('#err_customer').text("");
        }

        if (isNaN(receipt_amount) || receipt_amount == ""){
            receipt_amount = 0;
        }

        if (date == null || date == ""){
            $("#err_date").text(date_empty);
            return false;
        }else{
            $("#err_date").text("");
        }

        if (!date.match(date_regex)){
            $('#err_date').text(date_invalid);
            return false;
        }else{
            $("#err_date").text("");
        }


        if ($('#customer').val() == ""){
            $('#err_customer').text("Please Select the Customer.");
            $('#customer').focus();
            return false;
        }else{
            $('#err_customer').text("");
        }

        if (receipt_amount == null || receipt_amount == "" || receipt_amount == 0){

            $("#err_receipt_amount").text("Please Enter Advance Amount.");
            return false;
        } else {
            $("#err_receipt_amount").text("");

        }


        if (paying_by == null || paying_by == ""){
            $("#err_payment_mode").text(paying_by_empty);
            return false;
        }else{
            $("#err_payment_mode").text("");
        }


        if (paying_by == 'Cash' && receipt_amount > 20000){

            $("#err_receipt_amount").text(error_payment);

            return false;
        }else{
            $("#err_receipt_amount").text("");
        }


        if (paying_by != "cash" && paying_by != "other payment mode" && paying_by != ""){
           // var bank_name = $('#bank_name').val();
            var cheque_no = $('#cheque_number').val();
        /*    if (bank_name.length > 0)
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
            }*/
            

            // if(cheque_no == ''){
            //     $('#err_cheque_number').text("Please Enter Cheque Number");
            //         return false;
            // }else {
            //     $('#err_cheque_number').text('');
            // }

            /*if (cheque_no != '')
            {
                if (!cheque_no.match(num_regex)) {
                    $('#err_cheque_number').text(cheque_no_invalid);
                    return false;
                } else {
                    $("#err_cheque_number").text("");
                }
            } else {
                $('#err_cheque_number').text('');
            }*/

        }


        if (grand_total == "" || grand_total == null || grand_total == 0.00 || isNaN(grand_total)){
            $("#err_receipt_amount").text("Please Enter Receipt Amount");
            return false;
        }else{
            $("#err_receipt_amount").text("");

        }
        

        if (grand_total != receipt_amount){
            $('#err_amount_exceeds').text('Advance Amount is not matching with item total.');
            return false;
        }else{
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
        if (section_area == "edit_advance_voucher")
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
