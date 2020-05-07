var date_empty = "Please Enter Date.";
var date_invalid = "Please Enter Valid Date";
var paying_by_empty = "Please Select Payment Mode.";
var bank_name_empty = "Please Enter Bank Name.";
var bank_name_invalid = "Please Enter Valid Bank Name";
var bank_name_length = "Please Enter Bank Name Minimun 3 Character";
var cheque_no_empty = "Please Enter Cheque No.";
var cheque_no_invalid = "Please Enter Valid Cheque No";
var error_payment = "More Than 20000 cant be paid through Cash";

$(document).ready(function (){

    $("#payment_submit").click(function (event){
        
        var all_payment_total = 0;
        var final_json = new Array();
        var flag = true;
        $(document).find('.row_count').each(function(){
            var cust_id = $(this).find('[name=supplier]').val();
            var payment_id = $(this).find('[name=payment_id]').val();
            var payment_amount_old = $(this).find('[name=payment_amount_old]').val();
            var total_payment_amount = $(this).find('[name=total_payment_amount]').val();
            $(this).find("#err_supplier,#err_total_payment_amount").text('');
            if(!cust_id){
                $(this).find("#err_supplier").text('Select supplier!');
                
                flag = false;
                return false;
            }
            if(total_payment_amount == '' || isNaN(total_payment_amount)){
                $(this).find("#err_total_payment_amount").text('Actual payment amount required!');
                flag = false;
                return false;
            }
            var cust_inv_data = new Array();
            $('#payment_popup_view').find('tbody[cust_id='+cust_id+'] tr.inv_row_count').each(function(){
                if($(this).find('[name=reference_number]').is(':checked') && $(this).find('[name=payment_amount]').val() > 0 ){
                    var payment_total_paid = 0;
                    payment_total_paid +=parseFloat($(this).find('[name=payment_amount]').val());
                    all_payment_total +=parseFloat($(this).find('[name=payment_amount]').val());
                    
                    if($(this).find('[name=reference_number]').val() != 'excess_amount' && $(this).find('[name=reference_number]').val() != 'opening_balance'){
                        
                        payment_total_paid +=($(this).find('[name=discount]').val() != '') ? parseFloat($(this).find('[name=discount]').val()) : 0;
                        
                        if($(this).find('[name=icon_round_off]').val() == 'plus'){
                            //payment_total_paid -= ($(this).find('[name=round_off]').val() != '') ? parseFloat($(this).find('[name=round_off]').val()) : 0;
                            all_payment_total += ($(this).find('[name=round_off]').val() != '') ? parseFloat($(this).find('[name=round_off]').val()) : 0;
                        }else{
                            payment_total_paid +=($(this).find('[name=round_off]').val() != '') ? parseFloat($(this).find('[name=round_off]').val()) : 0;
                           // all_payment_total +=($(this).find('[name=round_off]').val() != '') ? parseFloat($(this).find('[name=round_off]').val()) : 0;
                        }
                        if($(this).find('[name=pending_amount]').val() <= 0){
                            $('#excess_purchase_id').val($(this).find('[name=reference_number]').val());
                        }
                        var data_item = {
                            reference_number_text : $(this).find('[name=reference_number_text]').val(),
                            reference_number: $(this).find('[name=reference_number]').val(),
                            payment_amount: $(this).find('[name=payment_amount]').val(),
                            payment_total_paid : payment_total_paid,
                            is_edit : $(this).find('[name=is_edit]').val(),
                            paid_amount: $(this).find('[name=paid_amount]').val(),
                            pending_amount: $(this).find('[name=pending_amount]').val(),
                            invoice_total: $(this).find('[name=invoice_total]').val(),
                            gain_loss_amount: 0,
                            gain_loss_amount_icon: 0,
                            discount: $(this).find('[name=discount]').val(),
                            other_charges: 0,
                            round_off: $(this).find('[name=round_off]').val(),
                            icon_round_off: $(this).find('[name=icon_round_off]').val(),
                        };
                    }else{
                        var data_item = {
                            excess_sales_id : $(this).find('[name=excess_sales_id]').val(),
                            opening_balance_id : $(this).find('[name=opening_balance_id]').val(),
                            opening_balance : $(this).find('[name=payment_amount]').attr('blnc'),
                            ledger_id : $(this).find('[name=payment_amount]').attr('ledger_id'),
                            paid_amount : parseFloat($(this).find('[name=payment_amount]').attr('paid_amount')),
                            reference_number_text: $(this).find('[name=reference_number]').val(),
                            reference_number: $(this).find('[name=reference_number]').val(),
                            payment_amount: $(this).find('[name=payment_amount]').val(),
                            payment_total_paid:$(this).find('[name=payment_amount]').val(),
                        };
                        
                    }
                    cust_inv_data.push(data_item);
                }
            });
            if(cust_inv_data.length <= 0){
                $(this).find("#err_total_payment_amount").text('Please select invoice number for this amount!');
                flag = false;
                return false;
            }
            final_json.push({supplier_id : cust_id,payment_id:payment_id,total_payment_amount:total_payment_amount,payment_amount_old:payment_amount_old,data_item:cust_inv_data});
        });
        if(flag){
            if(final_json.length <= 0){
                alert_d.text ='Please select supplier and select invoice number!';
                PNotify.error(alert_d); 
                return false;
            }
            $('#invoice_data').val(JSON.stringify(final_json));
            
            /*if(parseFloat(total_payment_amount) != parseFloat(all_payment_total)){
                alert_d.text ='Actual payment amount is not equals to Paid amount!';
                PNotify.error(alert_d); 
                return false;
            }*/

            var date = $('#voucher_date').val().trim();
            var paying_by = $('#payment_mode').val();
            var row_count = $('.row_count').length;
            var voucher_number = $('#voucher_number').val();
            var section_area = $("#section_area").val();
            if ($('#voucher_number').val() == "") {
                $("#err_voucher_number").text("Please enter voucher number");
                return false;
            } else {
                $("#err_voucher_number").text(" ");
                if (section_area == "edit_payment_voucher"){
                    var voucher_number_old = $("#voucher_number_old").val();
                    if (voucher_number_old != voucher_number) {
                        invoice_number_count();
                    }
                } else {
                    invoice_number_count();
                }
            }

            /*if ($('#supplier').val() == ""){
                $('#err_supplier').text("Please Select the supplier.");
                $('#supplier').focus();
                return false;
            } else {
                $('#err_supplier').text("");
            }*/
            
            if (paying_by == null || paying_by == ""){
                $("#err_payment_mode").text(paying_by_empty);
                return false;
            } else{
                $("#err_payment_mode").text("");
            }
           
            if (paying_by != "cash" && paying_by != "other payment mode" && paying_by != ""){
                var bank_name = $('#bank_name').val();
                var cheque_no = $('#cheque_number').val();
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

                /*$("#err_cheque_number").text("");
                if(cheque_no == ''){
                    $('#err_cheque_number').text(cheque_no_invalid);
                    return false;
                }

                if (cheque_no > 0)
                {
                    if (!cheque_no.match(num_regex)) {
                        $('#err_cheque_number').text(cheque_no_invalid);
                        return false;
                    }
                } */
            }
           
            if (date == null || date == ""){
                $("#err_voucher_date").text(date_empty);
                return false;
            } else{
                $("#err_voucher_date").text("");
            }
            if (!date.match(date_regex)){
                $('#err_voucher_date').text(date_invalid);
                return false;
            } else{
                $("#err_voucher_date").text("");
            }
            setTimeout(function(){ console.log(121); }, 0);
        }else{
            return false;
        }
    });
});

// Invoice number check
$("#voucher_number").on("blur", function (event){
    var voucher_number = $('#voucher_number').val();
    var section_area = $("#section_area").val();
    if (voucher_number == null || voucher_number == "") {
        $("#err_voucher_number").text("Please Enter Voucher Number.");
        return false;
    } else {
        $("#err_voucher_number").text("");
        if (section_area == "edit_payment_voucher"){
            var voucher_number_old = $("#voucher_number_old").val();
            if (voucher_number_old != voucher_number)
            {
                invoice_number_count();
            }
        } else {
            invoice_number_count();
        }
    }
});

function invoice_number_count(){
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
        },success: function (result) {
            if (result[0]['num'] > 0){
                $('#voucher_number').val("");
                $("#err_voucher_number").text(voucher_number + " already exists!");
                return false;
            } else{
                $("#err_voucher_number").text("");
            }
        }
    });
}
