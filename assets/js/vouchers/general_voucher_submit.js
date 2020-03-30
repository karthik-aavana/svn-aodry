
$(document).ready(function (){
   
     $('#interest_amount').on('blur', function () {
        var transaction_purpose = $('#transaction_purpose').val();
        var voucher_type = $('#voucher_type').val();
        var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
        var interest = $('#interest_amount').val();
        var loss = $('#others_amount').val();

        if((transaction_purpose == 'Investments' && voucher_type == 'RECEIPTS') || trans_category == 'Sales/Disposal of Fixed Asset'){
            if(interest != '' && loss != ''){
                $("#err_interest_amount").text("Please Enter any one Profit or Loss.");
            }else{
                $("#err_interest_amount").text("");
            }            
        }else{
            $("#err_interest_amount").text("");
        }
    })

    $('#others_amount').on('blur', function () {
        var transaction_purpose = $('#transaction_purpose').val();
        var voucher_type = $('#voucher_type').val();
        var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
        var interest = $('#interest_amount').val();
        var loss = $('#others_amount').val();
        if((transaction_purpose == 'Investments' && voucher_type == 'RECEIPTS') || trans_category == 'Sales/Disposal of Fixed Asset'){
            if(interest != '' && loss != ''){
                $("#err_others_amount").text("Please Enter any one Profit or Loss.");
            }else{
                $("#err_others_amount").text("");
            }            
        }else{
            $("#err_others_amount").text("");
        }
        

        })

        $('#txt_igst').on('blur', function () {
            var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
            var igst = $('#txt_igst').val();
            var cgst = $('#txt_cgst').val();
            var sgst = $('#txt_sgst').val();
            var utgst = $('#txt_utgst').val();
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                if((cgst != '' || sgst != '' || utgst != '') && igst != ''){
                    $("#err_txt_igst").text("If you want enter IGST. Clear CGST, SGST & UTGST ");
                }else{
                    $("#err_txt_igst").text("");
                }

            }
            
        })

        $('#txt_cgst').on('blur', function () {
            var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
            var igst = $('#txt_igst').val();
            var cgst = $('#txt_cgst').val();
            var sgst = $('#txt_sgst').val();
            var utgst = $('#txt_utgst').val();
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                if(igst != '' && cgst != ''){
                    $("#err_txt_cgst").text("If you want to enter CGST. Clear IGST");
                }else{
                    $("#err_txt_cgst").text("");
                }

            }            
        })

        $('#txt_sgst').on('blur', function () {
           var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
            var igst = $('#txt_igst').val();
            var cgst = $('#txt_cgst').val();
            var sgst = $('#txt_sgst').val();
            var utgst = $('#txt_utgst').val();
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset'  || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                if((igst != '' || utgst != '') && sgst != '') {
                    $("#err_txt_sgst").text("If you want to enter SGST. Clear IGST & UTGST ");
                }else{
                    $("#err_txt_sgst").text("");
                }

            }
            
        })

        $('#txt_utgst').on('blur', function () {
           var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
            var igst = $('#txt_igst').val();
            var cgst = $('#txt_cgst').val();
            var sgst = $('#txt_sgst').val();
            var utgst = $('#txt_utgst').val();
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                if((igst != '' || sgst != '') && utgst != ''){
                    $("#err_txt_utgst").text("If you want to enter SGST. Clear IGST & STGST ");
                }else{
                    $("#err_txt_utgst").text("");
                }

            }
            
        })

        

    $("#general_submit").click(function () {
        var voucher_date = $('#voucher_date').val() ? $('#voucher_date').val() : "";
        var trans_purpose = $('#trans_purpose').val() ? $('#trans_purpose').val() : "";
        var receipt_amount = $('#receipt_amount').val() ? $('#receipt_amount').val() : "";
        var payment_mode = $('#payment_mode').val() ? $('#payment_mode').val() : "";
        var input_type = $('#input_type').val() ? $('#input_type').val() : "";
        var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
        var voucher_type = $('#voucher_type').val();
        var txt_cgst = $('#txt_cgst').val() ? $('#txt_cgst').val() : "";
        var txt_sgst = $('#txt_sgst').val() ? $('#txt_sgst').val() : ""; 
        var txt_igst = $('#txt_igst').val() ? $('#txt_igst').val() : "";
        var txt_utgst = $('#txt_utgst').val() ? $('#txt_utgst').val() : "";
        var txt_cess = $('#txt_cess').val() ? $('#txt_cess').val() : "";
        var interest = $('#interest_amount').val()? $('#interest_amount').val() : "";
        var loss = $('#others_amount').val()? $('#others_amount').val() : "";
        var transaction_purpose = $('#transaction_purpose').val();
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

        if((transaction_purpose == 'Investments' && voucher_type == 'RECEIPTS') || trans_category == 'Sales/Disposal of Fixed Asset'){
            if(interest != '' && loss != ''){
                $("#err_others_amount").text("Please Enter any one Profit or Loss.");
                return false;
            }else{
                $("#err_others_amount").text("");
            }            
        }else{
            $("#err_others_amount").text("");
        }

        if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                if(txt_cgst != '' && txt_sgst != "" && txt_utgst != ''){
                    $("#err_txt_utgst").text("Please Enter any one UTGST or SGST");
                    return false;
                }else if((txt_igst != '' && txt_cgst != "" && txt_utgst != '') || (txt_igst != '' && txt_cgst != "" && txt_sgst != '')){
                    $("#err_txt_utgst").text("Please Enter IGST / (CGST and SCGST) ");
                    return false;
                }else{
                    $("#err_txt_utgst").text("");
                }

        }
    });
});

