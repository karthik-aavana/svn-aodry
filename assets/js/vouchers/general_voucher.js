
$(document).ready(function (){

    /*if ($('#payment_mode').val() != "bank"){
        $('#hide').hide();
    }*/

     $("#trans_purpose").on("change", function (event) {
        

    })
    $("#payment_mode").on("change", function (event) {
        var payment_mode = $('#payment_mode').val();
        if (payment_mode == null || payment_mode == "") {
            $('#hide').hide();
            $("#other_payment").hide();
        } else {
            if (payment_mode != "cash" && payment_mode != "" && payment_mode != "others" ) {
                //&& payment_mode == "bank"
                $('#hide').show();
                $("#other_payment").hide();
            } else
            {
                $('#hide').hide();
                $("#bank_name").val('');
                $("#cheque_number").val('');
                $("#cheque_date").val('');
            }
            if (payment_mode != "cash" && payment_mode != "" && payment_mode != "bank" && payment_mode == "other payment mode") {
                $("#other_payment").show();
                $('#hide').hide();
                $("#bank_name").val('');
                $("#cheque_number").val('');
                $("#cheque_date").val('');
            } else
            {
                $("#other_payment").hide();
                $("#payment_via").val('');
                $("#ref_number").val('');
            }
        }
    });


    $("#trans_purpose").on("change", function (event) {
        var trans_purpose = $(this).val();
            $.ajax({
            url: base_url + 'general_voucher/get_trans_purpose_det',
            type: 'POST',
            data:{trans_purpose: trans_purpose},
            success: function (data){
               //console.log(data);
                var parsedJson = $.parseJSON(data);
                var voucher_type = parsedJson.voucher_type;
                var input_type = parsedJson.input_type;
                var transaction_purpose = parsedJson.transaction_purpose;
               $("#lbl_amount").html('Amount<span class="validation-color">*</span>');
                $('#voucher_type').val(voucher_type);
                $('#input_type').val(input_type);
                $('#transaction_purpose').val(transaction_purpose);
                var trans_category = parsedJson.transaction_category;
                $('#transaction_cat').val(trans_category);
                if(trans_category == 'Advance Tax Refund by Govt' || trans_category == 'Deposit Amount Withdrawal from FD' || trans_category == 'Deposit Amount Withdrawal from RD'){
                    $("#div_interest").show();
                    $("#lbl_interest").text('Interest');
                    $("#div_others").hide();
                    $("#div_cess").hide();
                    $("#div_sgst").hide();
                    $("#div_utgst").hide();
                    $("#div_cgst").hide();
                    $("#div_igst").hide();
                    $("#div_receipt").show();
                    $("#div_tds").hide();
                }else if(trans_category == 'Cash deposited in bank'){
                    $("#div_interest").show();
                    $("#lbl_interest").text('Charges');
                    $("#div_others").hide();
                    $("#div_cess").hide();
                    $("#div_sgst").hide();
                    $("#div_utgst").hide();
                    $("#div_cgst").hide();
                    $("#div_igst").hide();
                    $("#div_receipt").show();
                    $("#div_tds").hide();
                }else if(trans_category == 'Cash withdrawal from bank'){
                    $("#div_interest").show();
                    $("#lbl_interest").text('Charges');
                    $("#div_others").hide();
                    $("#div_cess").hide();
                    $("#div_sgst").hide();
                    $("#div_utgst").hide();
                    $("#div_cgst").hide();
                    $("#div_igst").hide();
                    $("#div_receipt").show();
                    $("#div_tds").hide();
                } else if(trans_category == 'Advance repaid by vendor' || trans_category == 'Deposit Amount Withdrawal from RENT' || trans_category == 'Deposit Amount Withdrawal from ELECTRICITY' || trans_category == 'Deposit Amount Withdrawal from WATER' || trans_category == 'Deposit Amount Withdrawal from (other)'){
                    $("#div_interest").show();
                    $("#lbl_interest").text('Other Charges');
                    $("#div_others").hide();
                    $("#div_cess").hide();
                    $("#div_sgst").hide();
                    $("#div_utgst").hide();
                    $("#div_cgst").hide();
                    $("#div_igst").hide();
                    $("#div_receipt").show();
                    $("#div_tds").hide();
                }else if(trans_category == 'Purchase of Fixed Asset'){
                    $("#div_interest").show();
                    $("#lbl_interest").text('Other Charges');
                    $("#div_others").hide();
                    $("#div_cess").show();
                    $("#div_sgst").show();
                    $("#div_utgst").show();
                    $("#div_cgst").show();
                    $("#div_igst").show();
                    $("#div_receipt").show();
                    $("#div_tds").hide();
                    $("#lbl_amount").html('Purchase Price<span class="validation-color">*</span>');
                }else if(trans_category == 'Sales/Disposal of Fixed Asset'){
                    $("#div_receipt").show();
                    $("#lbl_interest").text('Gain');
                    $("#lbl_others").text('Loss');
                    $("#div_interest").show();
                    $("#div_others").show();                    
                    $("#div_cess").show();
                    $("#div_sgst").show();
                    $("#div_utgst").show();
                    $("#div_cgst").show();
                    $("#div_igst").show();
                    $("#div_tds").hide();
                    $("#lbl_amount").html('Taxable Value<span class="validation-color">*</span>');
                }else if(trans_category == 'GST Payable' || trans_category == 'Tax received from GST' ){
                    $("#div_interest").hide();
                    $("#div_receipt").hide();
                    $("#div_others").hide();
                    $("#div_cess").show();
                    $("#div_sgst").show();
                    $("#div_utgst").show();
                    $("#div_cgst").show();
                    $("#div_igst").show();
                    $("#div_tds").hide();
                }else if(trans_category == 'Tax paid to INCOME TAX' || trans_category == 'Tax received from Income Tax' ){
                    $("#div_interest").show();
                    $("#div_receipt").show();
                    $("#lbl_interest").text('Interest');
                    $("#div_others").show();
                    $("#div_cess").hide();
                    $("#div_sgst").hide();
                    $("#div_utgst").hide();
                    $("#div_cgst").hide();
                    $("#div_igst").hide();
                    $("#div_tds").hide();
                }else if(transaction_purpose == 'Investments' && voucher_type == 'RECEIPTS'){
                    $("#lbl_interest").text('Gain');
                    $("#lbl_others").text('Loss');
                    $("#div_interest").show();
                    $("#div_others").show();
                    $("#div_receipt").show();
                    $("#lbl_amount").html('Amount<span class="validation-color">*</span>');
                }else if(transaction_purpose == 'Loan Borrowed and repaid'  && voucher_type == 'PAYMENT'){
                    $("#lbl_interest").text('Interest');
                    $("#lbl_others").text('Others');
                    $("#div_interest").show();
                    $("#div_others").show();
                     $("#div_receipt").show();
                    $("#lbl_amount").html('Principal Amount<span class="validation-color">*</span>');
                   
                }else{
                    $("#div_interest").hide();
                    $("#div_others").hide();
                    $("#div_receipt").show();
                    $("#div_cess").hide();
                    $("#div_sgst").hide();
                    $("#div_utgst").hide();
                    $("#div_cgst").hide();
                    $("#div_igst").hide();
                    $("#div_tds").hide();
                    $("#lbl_amount").html('Amount<span class="validation-color">*</span>');
                }
                
                if(input_type == 'interest fixed' || input_type == 'interest recurring' || input_type == 'interest other' || input_type == 'interest liability'){
                    $("#trans_mode").hide();
                    $("#div_tds").show();
                    $('#hide').hide();
                }else{
                    $("#trans_mode").show();
                    $("#div_tds").hide();
                }


                if(trans_category == 'Equity shares issued to shareholder' || trans_category == 'Preference share issue to shareholders'){
                    $("#div_interest").show();
                    $("#lbl_interest").text('Security Premium');
                }



                if(input_type == 'partner' || input_type == 'shareholder'){
                    $("#div_partner").show();
                    $("#partner").text(input_type);
                    
                     $.ajax({
                        url: base_url + 'general_voucher/get_parners',
                        type: 'POST',
                        data:{type: input_type},
                        success: function (res){
                            var parsedres = $.parseJSON(res);
                            console.log(parsedres.length);
                            $('#cmb_partner').html('');
                            $('#cmb_partner').append('<option value="">Select</option>');
                        for (i = 0; i < parsedres.length; i++) {
                            $('#cmb_partner').append('<option value="' + parsedres[i].id + '">' + parsedres[i].sharholder_name + '</option>');
                        }
                        }
                    });
                }else{
                    $("#div_partner").hide();
                }
                 
                
               
                if( trans_category == 'Cash Receipt/Received from unknown' ||  trans_category == 'Cash Paid/Payment to unknown' ){
                    $('#payment_mode').html('');
                    $('#payment_mode').append('<option value="">Select</option>');
                    $('#payment_mode').append('<option value="cash">Cash</option>');
                }else if(trans_category == 'Cash withdrawal from bank' || trans_category == 'Cash deposited in bank' || trans_category == 'Bank Receipt/Received from unknown' || trans_category == 'Bank Paid/Payment to unknown'){
                    $('#payment_mode').html('');
                    console.log('hdie')
                    $.ajax({
                        url: base_url + 'general_voucher/getAllBank',
                        type: 'POST',
                        data:{type: input_type},
                        success: function (res){
                            var parsedres = $.parseJSON(res);
                            console.log(parsedres.length);
                                $('#payment_mode').append('<option value="">Select</option>');
                                
                                for (i = 0; i < parsedres.length; i++) {
                                    $('#payment_mode').append('<option value="' + parsedres[i].bank_account_id + '/' + parsedres[i].ledger_title +'">' + parsedres[i].ledger_title + '</option>');
                                }
                                
                        }
                    });
                }else if(trans_category != 'Cash deposited in bank' || trans_category != 'Bank Receipt/Received from unknown' || trans_category != 'Bank Paid/Payment to unknown' || trans_category != 'Cash withdrawal from bank' || trans_category != 'Cash Receipt/Received from unknown' ||  trans_category != 'Cash Paid/Payment to unknown'){
                    $('#payment_mode').html('');
                     $.ajax({
                        url: base_url + 'general_voucher/getAllBank',
                        type: 'POST',
                        data:{type: input_type},
                        success: function (res){
                            var parsedres = $.parseJSON(res);
                            console.log(parsedres.length);
                                $('#payment_mode').append('<option value="">Select</option>');
                                $('#payment_mode').append('<option value="cash">Cash</option>');
                                for (i = 0; i < parsedres.length; i++) {
                                    $('#payment_mode').append('<option value="' + parsedres[i].bank_account_id + '/' + parsedres[i].ledger_title +'">' + parsedres[i].ledger_title + '</option>');
                                }
                                
                        }
                    });
                    }
                }
        });
    });
});