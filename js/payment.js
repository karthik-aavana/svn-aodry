$(document).ready(function ()

{

    var receipt_amount = parseFloat($('#receipt_amount').val());

    var grand_total = parseFloat($('#grand_total').val());

    var paid_amount = parseFloat($('#paid_amount').val());

    var return_value = parseFloat($('#purchase_return_value').val());



    if (return_value == null || return_value == "")

    {

        return_value = 0;

    }

    var remaining_amount = (grand_total - paid_amount - receipt_amount - return_value).toFixed(2);

    // alert(remaining_amount);



    $('#err_remaining_amount').text('Remaining Amount : ' + remaining_amount);

    $('#round_off').hide();



    $('#transaction_type').change(function () {

        var transaction_type = $('#transaction_type').val();



        var receipt_amount = parseFloat($('#receipt_amount').val());

        var grand_total = parseFloat($('#grand_total').val());

        var paid_amount = parseFloat($('#paid_amount').val());

        var remaining_amount = (grand_total - paid_amount - receipt_amount).toFixed(2);



        if (transaction_type == 'Regular' && remaining_amount != '0.00')

        {

            $('#round_off').show();

        } else

        {

            $('#round_off').hide();

        }

    });





    $('#round_amount').blur(function () {

        var round_amount = $('#round_amount').val();

        var gst_round_amount = $('#gst_round_amount').val();

        var total_amount = (round_amount * gst_round_amount) / 100;

        $('#total_amount').val(total_amount.toFixed(2));

        $('#err_total_amount').text('Total Amount : ' + (parseFloat(round_amount) + parseFloat(total_amount)).toFixed(2));

    });

    $('#gst_round_amount').blur(function () {

        var round_amount = $('#round_amount').val();

        var gst_round_amount = $('#gst_round_amount').val();

        var total_amount = (round_amount * gst_round_amount) / 100;

        $('#total_amount').val(total_amount.toFixed(2));

        $('#err_total_amount').text('Total Amount : ' + (parseFloat(round_amount) + parseFloat(total_amount)).toFixed(2));

    });





    $('#hide').hide();

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



    $("#payment_submit").click(function (event) {



        var date = $('#date').val().trim();

        var paying_by = $('#paying_by').val();

        var transaction_type = $('#transaction_type').val();





        var receipt_amount = parseFloat($('#receipt_amount').val().trim());



        var grand_total = parseFloat($('#grand_total').val());

        var paid_amount = parseFloat($('#paid_amount').val());

        var return_value = parseFloat($('#purchase_return_value').val());

        if (return_value == null || return_value == "")

        {

            return_value = 0;

        }

        var remaining_amount = (grand_total - paid_amount - receipt_amount - return_value).toFixed(2);



        if (date == null || date == "") {

            $("#err_date").text(date_empty);

            return false;

        } else {

            $("#err_date").text("");

        }



        if (!date.match(date_regex)) {

            $('#err_date').text(date_invalid);

            return false;

        } else {

            $("#err_date").text("");

        }



        if (receipt_amount == null || $('#receipt_amount').val().trim() == "" || receipt_amount == 0)

        {

            $("#err_receipt_amount").text("Please Enter Receipt Amount.");

            $("#err_remaining_amount").text("");

            return false;

        } else {



            // alert("000");

            $("#err_receipt_amount").text("");

            $('#err_remaining_amount').text('Remaining Amount : ' + remaining_amount);

        }



        //date validation complite.

        if (transaction_type == null || transaction_type == "") {

            $("#err_transaction_type").text(transaction_type_empty);

            return false;

        } else {

            $("#err_transaction_type").text("");

        }

        if (paying_by == null || paying_by == "") {

            $("#err_paying_by").text(paying_by_empty);

            return false;

        } else {

            $("#err_paying_by").text("");

        }

        if (paying_by == 'Cash' && amount > 20000) {



            $("#err_receipt_amount").text(error_payment);



            return false;

        }







        if (paying_by != "Cash")

        {

            var bank_name = $('#bank_name').val().trim();

            var cheque_no = $('#cheque_no').val().trim();

            var round_amount = $('#round_amount').val().trim();

            var gst_round_amount = $('#gst_round_amount').val().trim();



            if (bank_name.length > 2) {

                if (!bank_name.match(name_regex)) {

                    $('#err_bank_name').text(bank_name_invalid);

                    return false;

                } else {

                    $("#err_bank_name").text("");

                }

                if (bank_name.length < 3) {

                    $('#err_bank_name').text(bank_name_length);

                    return false;

                } else {

                    $("#err_bank_name").text("");

                }

            } else {

                $('#err_bank_name').text('');

            }





            if (cheque_no > 2) {

                if (!cheque_no.match(num_regex)) {

                    $('#err_cheque_no').text(cheque_no_invalid);

                    return false;

                } else {

                    $("#err_cheque_no").text("");

                }

            } else {

                $('#err_cheque_no').text('');

            }





            if ((round_amount == null || round_amount == "" || round_amount == 0) && $("#roundoff_amt").is(":visible"))

            {

                $("#err_round_amount").text("Please Enter Round off Amount");

                return false;

            } else {



                if (!round_amount.match(price_regex) && $("#roundoff_amt").is(":visible"))

                {

                    $('#err_round_amount').text("Please enter the valid round off amount");

                    return false;

                } else

                {

                    $("#err_round_amount").text("");

                }

            }



            if (gst_round_amount != "" && $("#roundoff_amt").is(":visible"))

            {

                if (!gst_round_amount.match(price_regex)) {

                    $('#err_gst_round_amount').text("Please enter the valid GST Amount(in %)");

                    return false;

                } else {

                    $("#err_gst_round_amount").text("");

                }

            }



            receipt_amount = parseFloat($('#receipt_amount').val());

            var grand_total = parseFloat($('#grand_total').val());

            var paid_amount = parseFloat($('#paid_amount').val());

            var return_value = parseFloat($('#purchase_return_value').val());

            var remaining_amount = (grand_total - paid_amount - receipt_amount - return_value).toFixed(2);



            var round_amount = $('#round_amount').val();

            var gst_round_amount = $('#gst_round_amount').val();

            var total_amount = (round_amount * gst_round_amount) / 100;

            if ($("#roundoff_amt").is(":visible"))

            {

                total_amount = parseFloat((parseFloat(round_amount) + parseFloat(total_amount)).toFixed(2));

                remaining_amount = (grand_total - paid_amount - receipt_amount - return_value).toFixed(2);

                $('#err_total_amount').text('Total Amount : ' + total_amount);



                if (total_amount > remaining_amount)

                {

                    $('#err_amount_exceeds').text('Round Off Total Amount should be less than or equal to remaining amount.');

                    return false;

                } else

                {

                    $('#err_amount_exceeds').text('');

                }

            } else

            {

                $('#round_amount').val('');

                $('#gst_round_amount').val('');

                $('#total_amount').val('');

            }

        }



        // var receipt_amount=parseFloat($('#receipt_amount').val());

        // if (!receipt_amount.match(price_regex)) 

        // {

        //     $('#err_receipt_amount').text("Enter a Valid Amount ");

        //      $('#err_remaining_amount').text('');

        //     return false;

        // } 

        // else 

        // {

        //     $("#err_receipt_amount").text("");

        //     $('#err_remaining_amount').text('Remaining Amount : '+remaining_amount);

        // }



    });

    $("#date").on("blur keyup", function (event) {

        var date = $('#date').val().trim();

        if (date == null || date == "") {

            $("#err_date").text(date_empty);

            return false;

        } else {

            $("#err_date").text("");

        }

        if (!date.match(date_regex)) {

            $('#err_date').text(date_invalid);

            return false;

        } else {

            $("#err_date").text("");

        }

    });

// alert('22252');

    $("#receipt_amount").on("blur keyup", function (event) {



        var receipt_amount = parseFloat($('#receipt_amount').val());

        var grand_total = parseFloat($('#grand_total').val());

        var paid_amount = parseFloat($('#paid_amount').val());

        var return_value = parseFloat($('#purchase_return_value').val());

        if (return_value == null || return_value == "")

        {

            return_value = 0;

        }

        var pay_amount = $('#receipt_amount').val();

        var pay_mode = $('#paying_by').val();



        var remaining_amount = (grand_total - paid_amount - receipt_amount - return_value).toFixed(2);



        if (pay_mode == 'Cash' && pay_amount > 20000) {



            $('#err_receipt_amount').text(error_payment);

            $('#err_remaining_amount').text('');



            return false;

        } else {



            $("#err_receipt_amount").text("");

            $('#err_remaining_amount').text('Remaining Amount : ' + remaining_amount);



        }



        if (!pay_amount.match(price_regex)) {

            $('#err_receipt_amount').text("Enter a Valid Amount ");

            $('#err_remaining_amount').text('');

            return false;

        } else {

            $("#err_receipt_amount").text("");

            $('#err_remaining_amount').text('Remaining Amount : ' + remaining_amount);

        }





        if (remaining_amount <= 0)

        {

            // alert(receipt_amount+'--'+remaining_amount);



            var diff = (grand_total - paid_amount - return_value).toFixed(2);

            $('#err_receipt_amount').text('Amount shouldnt be greater than ' + diff);

            $('#receipt_amount').val(diff);

            $('#err_remaining_amount').text('');

            remaining_amount = '0.00';

        } else

        {

            $('#err_receipt_amount').text('');

            $('#err_remaining_amount').text('Remaining Amount : ' + remaining_amount);

        }



        if (remaining_amount == '0.00')

        {

            $('#round_off').hide();

        } else

        {

            $('#round_off').show();

        }



    });



    $("#paying_by").on("change", function (event) {

        $('#hide').hide();

        var paying_by = $('#paying_by').val();



        if (paying_by == null || paying_by == "") {

            $("#err_paying_by").text(paying_by_empty);

            return false;

        } else {

            if (paying_by != "Cash") {

                $('#hide').show();

            }

            $("#err_paying_by").text("");

        }

    }).change();



    $("#transaction_type").on("change", function (event) {

        // $('#hide').hide();

        var transaction_type = $('#transaction_type').val();



        if (transaction_type == null || transaction_type == "") {

            $("#err_transaction_type").text(transaction_type_empty);

            return false;

        } else {



            $("#err_transaction_type").text("");

        }

    });

    $("#bank_name").on("blur keyup", function (event) {



        var bank_name = $('#bank_name').val().trim();

        if (bank_name.length > 2) {

            if (bank_name == null || bank_name == "") {

                $('#err_bank_name').text(bank_name_empty);

                return false;

            } else {

                $('#err_bank_name').text('');

            }

            if (!bank_name.match(name_regex)) {

                $('#err_bank_name').text(bank_name_invalid);

                return false;

            } else {

                $("#err_bank_name").text("");

            }

            if (bank_name.length < 3) {

                $('#err_bank_name').text(bank_name_length);

                return false;

            } else {

                $("#err_bank_name").text("");

            }

        }

    });



    $("#cheque_no").on("blur keyup", function (event) {

        var cheque_no = $('#cheque_no').val().trim();

        if (cheque_no > 2) {

            if (cheque_no == null || cheque_no == "") {

                $('#err_cheque_no').text(cheque_no_empty);

                return false;

            } else {

                $('#err_cheque_no').text('');

            }

            if (!cheque_no.match(num_regex)) {

                $('#err_cheque_no').text(cheque_no_invalid);

                return false;

            } else {

                $("#err_cheque_no").text("");

            }

        }

    });

    $('#roundoff_amt').hide();







    $('#myForm input').on('change', function () {



        var radio_btn = $('input[name="myRadio"]:checked').val();

        if (radio_btn == "yes") {



            $('#roundoff_amt').show();

        }

        if (radio_btn == "no") {



            $('#roundoff_amt').hide();

        }

    });



});





