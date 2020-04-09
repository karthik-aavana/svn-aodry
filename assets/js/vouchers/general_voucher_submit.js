
$(document).ready(function (){
   
     $('#interest_amount').on('blur', function () {
        var transaction_purpose = $('#transaction_purpose').val();
        var voucher_type = $('#voucher_type').val();
        var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
        var interest = $('#interest_amount').val() ? $('#interest_amount').val() : 0;
        var loss = $('#others_amount').val() ? $('#others_amount').val() : 0;

        if((transaction_purpose == 'Investments' && voucher_type == 'RECEIPTS') || trans_category == 'Sales/Disposal of Fixed Asset'){
            if(parseInt(interest) > 0 && parseInt(loss) > 0){
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
        var interest = $('#interest_amount').val() ? $('#interest_amount').val() : 0;
        var loss = $('#others_amount').val() ? $('#others_amount').val() : 0;
        if((transaction_purpose == 'Investments' && voucher_type == 'RECEIPTS') || trans_category == 'Sales/Disposal of Fixed Asset'){
            if(parseInt(interest) > 0 && parseInt(loss) > 0){
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
            var igst = $('#txt_igst').val() ? $('#txt_igst').val() : 0;
            var cgst = $('#txt_cgst').val() ? $('#txt_cgst').val() : 0;
            var sgst = $('#txt_sgst').val() ? $('#txt_sgst').val() : 0;
            var utgst = $('#txt_utgst').val() ? $('#txt_utgst').val() : 0;
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){                
            console.log(igst);
            console.log(cgst);

            if(parseInt(igst) > 0 && parseInt(cgst) > 0 && parseInt(utgst) > 0 && parseInt(sgst) > 0){
                    $("#err_txt_igst").text("If you want enter IGST. Clear CGST, SGST & UTGST ");
                }else{
                    $("#err_txt_igst").text("");
                }

            }
            
        })

        $('#txt_cgst').on('blur', function () {
            var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
             var igst = $('#txt_igst').val() ? $('#txt_igst').val() : 0;
            var cgst = $('#txt_cgst').val() ? $('#txt_cgst').val() : 0;
            var sgst = $('#txt_sgst').val() ? $('#txt_sgst').val() : 0;
            var utgst = $('#txt_utgst').val() ? $('#txt_utgst').val() : 0;
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                
               if(parseInt(igst) > 0 && parseInt(cgst) > 0){
                    $("#err_txt_cgst").text("If you want to enter CGST. Clear IGST");
                }else{
                    $("#err_txt_cgst").text("");
                }

            }            
        })

        $('#txt_sgst').on('blur', function () {
           var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
             var igst = $('#txt_igst').val() ? $('#txt_igst').val() : 0;
            var cgst = $('#txt_cgst').val() ? $('#txt_cgst').val() : 0;
            var sgst = $('#txt_sgst').val() ? $('#txt_sgst').val() : 0;
            var utgst = $('#txt_utgst').val() ? $('#txt_utgst').val() : 0;
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset'  || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                
                if((parseInt(igst) > 0 && parseInt(sgst) > 0) && parseInt(utgst) > 0){
                    $("#err_txt_sgst").text("If you want to enter SGST. Clear IGST & UTGST ");
                }else{
                    $("#err_txt_sgst").text("");
                }

            }
            
        })

        $('#txt_utgst').on('blur', function () {
           var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
            var igst = $('#txt_igst').val() ? $('#txt_igst').val() : 0;
            var cgst = $('#txt_cgst').val() ? $('#txt_cgst').val() : 0;
            var sgst = $('#txt_sgst').val() ? $('#txt_sgst').val() : 0;
            var utgst = $('#txt_utgst').val() ? $('#txt_utgst').val() : 0;
            if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                if((parseInt(igst) > 0 || parseInt(sgst) > 0) && parseInt(utgst) > 0){
                    $("#err_txt_utgst").text("If you want to enter UTGST. Clear IGST & SGST ");
                }else{
                    $("#err_txt_utgst").text("");
                }

            }
            
        })

        

    $("#general_submit").click(function () {

        var voucher_date = $('#voucher_date').val() ? $('#voucher_date').val() : "";
        var trans_purpose = $('#trans_purpose').val() ? $('#trans_purpose').val() : "";
        var receipt_amount = $('#receipt_amount').val() ? $('#receipt_amount').val() : 0;
        var payment_mode = $('#payment_mode').val() ? $('#payment_mode').val() : "";
        var input_type = $('#input_type').val() ? $('#input_type').val() : "";
        var trans_category = $('#transaction_cat').val() ? $('#transaction_cat').val() : "";
        var voucher_type = $('#voucher_type').val();
        var txt_cgst = $('#txt_cgst').val() ? $('#txt_cgst').val() : 0;
        var txt_sgst = $('#txt_sgst').val() ? $('#txt_sgst').val() : 0; 
        var txt_igst = $('#txt_igst').val() ? $('#txt_igst').val() : 0;
        var txt_utgst = $('#txt_utgst').val() ? $('#txt_utgst').val() : 0;
        var txt_cess = $('#txt_cess').val() ? $('#txt_cess').val() : 0;
        var interest = $('#interest_amount').val()? $('#interest_amount').val() : 0;
        var loss = $('#others_amount').val()? $('#others_amount').val() : 0;
        var transaction_purpose = $('#transaction_purpose').val();
        txt_utgst = parseInt(txt_utgst);
        txt_cgst = parseInt(txt_cgst);
        txt_sgst = parseInt(txt_sgst);
        txt_igst = parseInt(txt_igst);
        txt_cess = parseInt(txt_cess);
        interest = parseInt(interest);
        loss = parseInt(loss);
        receipt_amount = parseInt(receipt_amount);
        console.log(receipt_amount);
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
            if(txt_utgst == 0 && txt_cgst == 0 && txt_sgst == 0 && txt_utgst == 0 && txt_igst == 0 && txt_cess == 0){
                $("#err_txt_cgst").text("Please Enter Any One Tax Amount.");
                return false;
            } else {
                $("#err_txt_cgst").text("");
            }
        }else{
             if (receipt_amount == null || receipt_amount == "" || receipt_amount == 0) {
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
            if(interest > 0 && loss > 0){
                $("#err_others_amount").text("Please Enter any one Profit or Loss.");
                return false;
            }else{
                $("#err_others_amount").text("");
            }            
        }else{
            $("#err_others_amount").text("");
        }
        
        if(trans_category == 'Capital withdrawn by partner' || trans_category == 'Capital Invested from Partner'){
            if($('#cmb_partner').val() == '' || $('#cmb_partner').val() == null){
                $("#err_partner").text("Please Select Partner.");
                return false;
            }else{
                $("#err_partner").text("");
            }
        }
        

        if(trans_category == 'Purchase of Fixed Asset' || trans_category == 'Sales/Disposal of Fixed Asset' || trans_category == 'GST Payable' || trans_category == 'Tax received from GST'){
                
                if(txt_utgst > 0 && txt_cgst > 0 && txt_sgst > 0){
                    $("#err_txt_utgst").text("Please Enter any one UTGST or SGST");
                    return false;
                }else if((txt_igst > 0 && txt_cgst > 0 && txt_utgst > 0) || (txt_igst > 0 && txt_cgst > 0 && txt_sgst > 0)){
                    $("#err_txt_sgst").text("Please Enter IGST / (CGST and SGST) ");
                    return false;
                }else if((txt_igst > 0 && txt_cgst > 0 ) || (txt_igst > 0 && txt_sgst > 0) || (txt_igst > 0 && txt_utgst > 0)){
                    $("#err_txt_igst").text("Please Enter IGST / (CGST and SGST) ");
                    return false;
                }else{
                    $("#err_txt_utgst").text("");
                }


                if(txt_utgst > 0 && txt_cgst > 0 ){
                    if(txt_utgst != txt_cgst){
                        $("#err_txt_utgst").text("Please Enter amount of UTGST & CGST are equal.");
                        return false;
                    }else{
                        $("#err_txt_utgst").text("");
                    }
                }else if((txt_utgst > 0 && txt_cgst == 0 && txt_sgst == 0) ||  (txt_utgst == 0 && txt_cgst > 0 && txt_sgst == 0) ||  (txt_sgst == 0 && txt_cgst > 0 && txt_utgst == 0 ) ||  (txt_sgst > 0 && txt_cgst == 0 && txt_utgst == 0 )){
                    $("#err_txt_cgst").text("Please Enter amount of (UTGST & CGST) /   (SGST & CGST) are equal.");
                        return false;
                }else{
                    $("#err_txt_cgst").text("");
                }

                if(txt_cgst > 0 && txt_sgst > 0){
                    if(txt_sgst != txt_cgst){
                        $("#err_txt_sgst").text("Please Enter amount of SGST & CGST are equal.");
                        return false;
                    }else{
                        $("#err_txt_sgst").text("");
                    }
                }

                

        }
    });
});

