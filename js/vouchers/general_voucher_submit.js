
$(document).ready(function (){

    $("#general_submit").click(function () {
        var voucher_date = $('#voucher_date').val() ? $('#voucher_date').val() : "";
        var trans_purpose = $('#trans_purpose').val() ? $('#trans_purpose').val() : "";
        var receipt_amount = $('#receipt_amount').val() ? $('#receipt_amount').val() : "";
        var payment_mode = $('#payment_mode').val() ? $('#payment_mode').val() : "";
        var input_type = $('#input_type').val() ? $('#input_type').val() : "";
        var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";

        var txt_cgst = $('#txt_cgst').val() ? $('#txt_cgst').val() : "";
        var txt_sgst = $('#txt_sgst').val() ? $('#txt_sgst').val() : ""; 
        var txt_igst = $('#txt_igst').val() ? $('#txt_igst').val() : "";
        var txt_utgst = $('#txt_utgst').val() ? $('#txt_utgst').val() : "";
        var txt_cess = $('#txt_cess').val() ? $('#txt_cess').val() : "";
        
        if (voucher_date == null || voucher_date == "") {
            $("#err_voucher_date").text("Please Select Voucher Date.");
            return false;
        } else {
            $("#err_voucher_date").text("");
        }

        if (trans_purpose == null || trans_purpose == "") {
            $("#err_trans_purpsose").text("Please Select Transaction Purpose.");
            return false;
        } else {
            $("#err_trans_purpsose").text("");
        }
        if(trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
               
            if (txt_cgst == '' && txt_sgst == "" && txt_igst == '' && txt_utgst == '' && txt_cess == '') {
                $("#err_txt_cgst").text("Please Enter Any One Tax Amount.");
                return false;
            } else {
                $("#err_txt_cgst").text("");
            }
        }else{
             if (receipt_amount == null || receipt_amount == "") {
                $("#err_receipt_amount").text("Please Enter Amount.");
                return false;
            } else {
                $("#err_receipt_amount").text("");
            }
        }
        if(input_type != 'interest fixed' && input_type != 'interest recurring' && input_type != 'interest other' && input_type != 'interest liability'){
            if (payment_mode == null || payment_mode == "") {
                $("#err_payment_mode").text("Please Select Mode of Payment.");
                return false;
            } else {
                $("#err_payment_mode").text("");
            }
        }
    });
});

