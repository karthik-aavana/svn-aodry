var reference_array = new Array();
var reference_array_list = new Array();
$(document).ready(function () {
    if ($('#payment_mode').val() == "cash" || $('#payment_mode').val() == "" || $('#payment_mode').val() == 'other payment mode'){
        $('#hide').hide();
    }
    var count = 0;
    $('.add_customer_row_button').click(function () {

        var reference_html = $(this).closest('.main_row_customer').find('#customer').html();
        $(document).find('[name=customer]').each(function () {
            if ($(this).val() != '') {
                $('#common_select_customer').find('option[value=' + $(this).val() + ']').remove();
            }
        });
        var reference_html = $('#common_select_customer').html();
        var row = '<div class="row row_count" row="' + row_count + '"><div class="col-sm-3"><div class="form-group"> <label for="customer">Customer<span class="validation-color">*</span></label> <select class="form-control select2 customer" id="customer' + row_count + '" name="customer">' + reference_html + ' </select><span class="validation-color" id="err_customer"></span></div></div><div class="col-sm-3"> <div class="form-group"> <label for="customer">Actual receipt amount<span class="validation-color">*</span></label> <input type="text" class="form-control number_only" id="total_receipt_amount' + row_count + '" name="total_receipt_amount" value=""><span class="validation-color" id="err_total_receipt_amount"></span></div></div><div class="col-sm-3"><div class="form-group" style="padding-top: 15px;"><div class="btn-group"><a type="buttion" href="javascript:void(0);" class="remove_row_button_customer pull-right disabled"><i class="fa fa-minus"></i></a><a type="buttion" href="javascript:void(0);" class="invoice_edit pull-right disabled"><i class="fa fa-pencil"></i></a></div> </div></div></div>';
        /*$clone = '<div class="row row_count" row="'+row_count+'">'+$clone+'</div>';
   
        $('.add_row_button', $clone).text('');*/
        $("#add_row_field_customer").append(row);
    });

    $(document).on("click", ".remove_row_button_customer", function () {
        $(this).closest('.row').remove();
    });

    $(document).on('click', '.toggle_plus', function () {
        var i = $(this).find('i');
        var dv = $(this).closest('.input-group');
        var lbl = $(this).attr('name');
        if (i.hasClass('fa-plus')) {
            dv.find('input[type=hidden]').val('minus');
            i.removeClass('fa-plus').addClass('fa-minus');
            if (lbl == 'Gain')
               lbl = 'Loss';
            dv.parent().find('.toggle_lbl').text(lbl + '(-)');
        } else {
            dv.find('input[type=hidden]').val('plus');
            i.removeClass('fa-minus').addClass('fa-plus');
            dv.parent().find('.toggle_lbl').text(lbl + '(+)');
        }
        dv.find('input[type=text]').trigger('change');
    });

    //date change
    $('#voucher_date').on('changeDate', function () {
        var selected = $(this).val();
        var module_id = $("#module_id").val();
        var date_old = $("#voucher_date_old").val();
        var privilege = $("#privilege").val();
        var old_res = date_old.split("-");
        var new_res = selected.split("-");
        if (old_res[1] != new_res[1]) {
            $.ajax({
                url: base_url + 'general/generate_date_reference',
                type: 'POST',
                data: {
                   date: selected,
                    privilege: privilege,
                    module_id: module_id
                },
                success: function (data) {
                    var parsedJson = $.parseJSON(data);
                    var type = parsedJson.reference_no;
                    $('#voucher_number').val(type)
                }
            })
        } else {
            var old_reference = $('#voucher_number_old').val();
            $('#voucher_number').val(old_reference)
        }
    });

    var all_sales_obj = {};
    if (typeof data_items != 'undefined') {
        if (data_items.length > 0) {
            all_sales_obj = $.parseJSON(data_items);
            console.log(all_sales_obj);
        }
    }

    $(document).on('change','[name=customer]',function(){
        if($(this).val() != ''){
            $(this).closest('.row_count').attr('cust_id',$(this).val());
            $(this).closest('.row_count').find('input').removeAttr('disabled');
            $(this).closest('.row_count').find('a').removeClass('disabled');
        }else{
            $(this).closest('.row_count').attr('cust_id',0);
            $(this).closest('.row_count').find('input').attr('disabled','disabled');
            $(this).closest('.row_count').find('a').addClass('disabled');
        }
    })
    var XHReceipt;
    $(document).on('click','.invoice_edit', function (event){
        var currency_id = $('#currency_id').val();
        var customer_id = $(this).closest('.row_count').find('[name=customer]').val();
        
        var reference_type = $('#reference_type').val();
        if (reference_type == "") var reference_type = 'sales';
        
        var row_count = $('.row_count').length;
        var inv_row_count = $('.inv_row_count').length;

        if (row_count > 1) {
            $('#add_row_field').html('');
        }
        $('#receipt_popup_view table#receipt_data').find('tbody').hide();
        if (customer_id) {
            if($('#receipt_popup_view table#receipt_data').find('tbody#receipt_data_'+customer_id).length > 0){
                $('#receipt_popup_view table#receipt_data').find('tbody#receipt_data_'+customer_id).show();
                $('#receipt_popup_view').modal('show');
            }else{
                $('#receipt_popup_view #loader').show();
                $('#receipt_popup_view').modal('show');
                if(XHReceipt && XHReceipt.readyState != 4){
                    XHReceipt.abort();
                }
                
                XHReceipt = $.ajax({
                    url: base_url + 'receipt_voucher/get_sales_invoice_number',
                    type: 'POST',
                    dataType:'json',
                    data: {customer_id: customer_id, currency_id: currency_id, reference_type: reference_type},
                    success: function (result) {
                        $('#receipt_popup_view #loader').hide();
                        /*var parsedJson = $.parseJSON(result);*/
                        var data = result.data;
                        $(data).each(function (k, v) {
                            all_sales_obj['sales_' + v.sales_id] = v;
                        })
                        $row = '';
                        if(result.opening_balance){
                            if(result.opening_balance > 0){
                                inv_row_count++;
                                $row += '<tr class="inv_row_count" row="'+inv_row_count+'"><td class="reference_number_div"><input type="hidden" class="form-control" id="last_receipt_amount_'+inv_row_count+'" name="last_receipt_amount" value="'+global_round_off(parseFloat(result.pending_amount))+'"><input type="hidden" name="opening_balance_id" value="'+result.opening_balance_id+'"><input type="checkbox" name="reference_number" id="opening_balance" class="receipt_voucher_number" checked value="opening_balance"></td><td colspan="2">Opening Balance</td><td colspan="4"><input type="text" class="form-control number_only" id="receipt_amount_'+inv_row_count+'" name="receipt_amount" data-id="'+customer_id+'" ledger_id="'+result.ledger_id+'" paid_amount="'+global_round_off(parseFloat(result.paid_amount))+'" blnc="'+global_round_off(parseFloat(result.opening_balance))+'" pending_amount="'+global_round_off(parseFloat(result.pending_amount))+'" value="'+ global_round_off(parseFloat(result.pending_amount)) + '" autocomplete="off"></td></tr>';
                            }
                        }
                        if(data.length > 0){
                            $(data).each(function (k, v) {
                                inv_row_count++;
                                v.sales_invoice_number = v.sales_brand_invoice_number != '' ? v.sales_brand_invoice_number : v.sales_invoice_number; 
                                var grand_total = (parseFloat(v.sales_grand_total) + parseFloat(v.credit_note_amount) - parseFloat(v.debit_note_amount)).toFixed(2);
                                $row += '<tr class="inv_row_count" row="'+inv_row_count+'"><td class="reference_number_div"><input type="checkbox" name="reference_number" id="'+v.sales_id+'"  class="receipt_voucher_number" checked value="'+v.sales_id+'"></td><td>'+v.sales_invoice_number+'</td><td>'+ parseFloat(v.sales_grand_total).toFixed(2) + '</td><td class="discount_div"><input type="text" class="form-control number_only" id="discount_'+inv_row_count+'" data-id="'+v.sales_id+'" name="discount" value=""></td><td class="round_off_div"><div class="input-group"><input type="hidden" name="icon_round_off" value="plus"><span class="input-group-addon toggle_plus" id="Input_addon_roundoff" name="Round Off" style="width:20px;"><i id="roundoff_button" class="fa fa-plus"></i></span><input type="text" class="form-control number_only" name="round_off" id="round_off_'+inv_row_count+'" data-id="'+v.sales_id+'" stlye="float: left;"></div><span class="validation-color" id="err_grand_total_'+inv_row_count+'"></span></td><td class="receipt_amount_div"><input type="text" class="form-control number_only" id="receipt_amount_'+inv_row_count+'" name="receipt_amount" data-id="'+v.sales_id+'" value=""><input type="hidden" class="form-control number_only" id="paid_amount_'+inv_row_count+'" class="form-control" name="paid_amount" value="" readonly=""><input type="hidden" class="form-control" id="remaining_amount_'+inv_row_count+'" name="remaining_amount" value="3000.00"></td><input type="hidden" class="form-control" id="reference_number_text_'+inv_row_count+'" name="reference_number_text" value="'+v.sales_invoice_number+'"><input type="hidden" class="form-control" id="current_paid_amount_'+inv_row_count+'" name="current_paid_amount" value="'+parseFloat(v.sales_paid_amount).toFixed(2)+'"><input type="hidden" class="form-control" id="current_invoice_amount_'+inv_row_count+'" name="current_invoice_amount" value="'+parseFloat(v.sales_grand_total).toFixed(2)+'"><input type="hidden" class="form-control" id="old_receipt_amount_'+inv_row_count+'" name="old_receipt_amount" value="0"><input type="hidden" class="form-control" id="last_receipt_amount_'+inv_row_count+'" name="last_receipt_amount" value="0"><td><input type="text" value="'+(grand_total - parseFloat(v.sales_paid_amount)).toFixed(2)+'" id="pending_amount_'+inv_row_count+'" disabled="disabled" name="pending_amount"><input type="hidden" class="float_number form-control" id="invoice_total_'+inv_row_count+'" name="invoice_total" value="3000.00"></td></tr>';
                                
                            })
                        }
                        if($row == ''){
                            inv_row_count++;
                            $row += '<tr class="inv_row_count" row="'+inv_row_count+'" style="text-align: center;"><td colspan="7">No data found!</td></tr>';
                        }
                        $row = "<tbody id='receipt_data_"+customer_id+"' cust_id='"+customer_id+"'>"+$row+"</tbody>";
                        $('#receipt_popup_view table#receipt_data').append($row);
                        
                    },error:function(){
                        $('#receipt_popup_view #loader').hide();
                    }
                });
            }
        }
    });

    $(document).on('click', '.receipt_voucher_number', function () {
        var val =$(this).attr('id');
        if($(this).prop("checked") == true){
            $(this).parents('tr:first').find("[data-id='"+val+"']").prop('disabled',false);
        }else{
            $(this).parents('tr:first').find("[data-id='"+val+"']").prop('disabled',true);
        }
    });

    $(document).on('click','.auto_set',function(){
        var cust_id = $('#receipt_popup_view').find('tbody:visible').attr('cust_id');
        var total_receipt_amount = $(document).find('.row_count[cust_id='+cust_id+'] [name=total_receipt_amount]').val();
        var remains = total_receipt_amount;
        var row_cnt = 0;
        if(total_receipt_amount > 0){
            $('#receipt_popup_view').find('tbody:visible tr.inv_row_count').each(function(){
                $(this).find('[name=receipt_amount]').val(0);
                if($(this).find('[name=reference_number]').is(':checked')){
                    row_cnt++;
                    sales_id = $(this).find('[name=reference_number]').attr('id');
                    
                    if(sales_id != 'excess_amount'){
                        if(sales_id == 'opening_balance'){
                            remaining_amount = $(this).find('[name=receipt_amount]').attr('pending_amount');
                            
                        }else{
                            data = all_sales_obj['sales_' + sales_id];
                            GetRemianAmount = getRemainigAmount($(this), data);
                            remaining_amount = GetRemianAmount.remaining_amount;
                            over_all_amount = GetRemianAmount.over_all_amount;
                        }
                        
                        if(remaining_amount > 0 && remains > 0){

                            assign = remains - getFloatNumber(remaining_amount);
                            if(assign <= 0){
                                $(this).find('[name=receipt_amount]').val(global_round_off(remains));
                                remains = 0;
                            }else{
                                $(this).find('[name=receipt_amount]').val(global_round_off(remaining_amount));
                                remains = remains - getFloatNumber(remaining_amount);
                            }
                            
                        }
                        $(this).find('[name=receipt_amount]').trigger('change');
                    }
                }else{
                    $(this).find('[name=receipt_amount]').trigger('change');
                    var val =$(this).attr('id');
                    $(this).parents('tr:first').find("[data-id='"+val+"']").prop('disabled',true);
                }
            });
            var tbd = $('#receipt_popup_view').find('tbody:visible');
            var el = tbd.find('#excess_amount');
            if(remains > 0){
                if(el.length > 0){
                    el.closest('tr').find('[name=receipt_amount]').val(global_round_off(parseFloat(remains)));
                }else{
                    row_cnt++;
                    var row = '<tr class="inv_row_count" row="'+row_cnt+'"><td class="reference_number_div"><input type="hidden" name="excess_sales_id" value="0"><input type="checkbox" name="reference_number" id="excess_amount"  class="receipt_voucher_number" checked value="excess_amount"></td><td colspan="2">Excess Amount</td><td colspan="4"><input type="text" class="form-control number_only" id="receipt_amount_'+row_cnt+'" name="receipt_amount" data-id="'+cust_id+'" value="'+ global_round_off(parseFloat(remains)) + '" autocomplete="off"></td></tr>';
                    tbd.append(row);
                }
            }else{
                if(el.length > 0) el.closest('tr').remove();
            }
        }
    })

    $(document).on('click','#save_invoice',function(){
        var final_json = new Array();
        console.log(final_json.length);
        var all_receipt_total = 0;
        var cust_id = $('#receipt_popup_view').find('tbody:visible').attr('cust_id');
        $('#receipt_popup_view').find('tbody:visible tr.inv_row_count').each(function(){
          if($(this).find('[name=reference_number]').is(':checked') && $(this).find('[name=receipt_amount]').val() != ''){
            var receipt_total_paid = 0;
            receipt_total_paid +=getFloatNumber($(this).find('[name=receipt_amount]').val());
            all_receipt_total +=getFloatNumber($(this).find('[name=receipt_amount]').val());

            if($(this).find('[name=reference_number]').val() != 'excess_amount' && $(this).find('[name=reference_number]').val() != 'opening_balance'){
                receipt_total_paid +=($(this).find('[name=discount]').val() != '') ? getFloatNumber($(this).find('[name=discount]').val()) : 0;
               
                if($(this).find('[name=icon_round_off]').val() == 'plus'){
                    //receipt_total_paid -= ($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                    all_receipt_total += ($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                }else{
                    receipt_total_paid +=($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                   // all_receipt_total +=($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                }
                if($(this).find('[name=pending_amount]').val() <= 0){
                    $(this).find('#excess_sales_id').val($(this).find('[name=reference_number]').val());
                }
                var data_item = {
                    reference_number_text : $(this).find('[name=reference_number]').val(),
                    reference_number: $(this).find('[name=reference_number]').val(),
                    receipt_amount: $(this).find('[name=receipt_amount]').val(),
                    receipt_total_paid : receipt_total_paid,
                    paid_amount: $(this).find('[name=paid_amount]').val(),
                    pending_amount: $(this).find('[name=pending_amount]').val(),
                    is_edit : $(this).find('[name=is_edit]').val(),
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
                    reference_number: $(this).find('[name=reference_number]').val(),
                    receipt_amount: $(this).find('[name=receipt_amount]').val()
                };
            }
            final_json.push(data_item);
          }
        });
        $(document).find('[name=customer]').each(function(){
            if($(this).val() == cust_id){
                $(this).closest('.row_count').find('[name=total_receipt_amount]').val(global_round_off(parseFloat(all_receipt_total)));
            }
        });
        console.log(final_json.length,11111);
        $('#receipt_popup_view').modal('hide');
    })

    $('#currency_id').change(function (event)
    {
        $('#customer').change();
    });

    function getRemainigAmount(ths, data) {
        var row_no = ths.attr('row');
        if (!row_no) row_no = 1;
        //var grand_total = (parseFloat(data.sales_grand_total) + parseFloat(data.credit_note_amount) - parseFloat(data.debit_note_amount)).toFixed(2);
        var grand_total = parseFloat(data.sales_grand_total).toFixed(2);
        var remaining_amount = (grand_total - parseFloat(data.sales_paid_amount)).toFixed(2);
        var already_paid_amount = parseFloat(data.sales_paid_amount).toFixed(2);
        var receipt_amount = getFloatNumber((ths.find('#receipt_amount_' + row_no).val()) ? ths.find('#receipt_amount_' + row_no).val() : 0);
        
        var gain_loss_amount =0;// getFloatNumber((ths.find('#gain_loss_amount_' + row_no).val()) ? ths.find('#gain_loss_amount_' + row_no).val() : 0);
        var loss_amount = 0
        /*if (ths.find('[name=icon_gain_loss_amount]').val() != 'plus')
            loss_amount = gain_loss_amount;*/
        var discount = getFloatNumber((ths.find('#discount_' + row_no).val()) ? ths.find('#discount_' + row_no).val() : 0);
        var other_charges = 0;//getFloatNumber((ths.find('#other_charges_' + row_no).val()) ? ths.find('#other_charges_' + row_no).val() : 0);
        var round_off = getFloatNumber((ths.find('#round_off_' + row_no).val()) ? ths.find('#round_off_' + row_no).val() : 0);
        round_off_minus = round_off_plus = 0;
        if (ths.find('[name=icon_round_off]').val() == 'plus') {
            round_off_plus = round_off;
        } else {
            round_off_minus = round_off;
        }
        var receipt_amount_old = parseFloat(ths.find('#old_receipt_amount_' + row_no).val());
        if (isNaN(receipt_amount_old))
            receipt_amount_old = 0;

        /* var paid_amount = (parseFloat(current_paid_amount) - parseFloat(receipt_amount_old)).toFixed(2);*/
        /* peinding amount formula peinding = recipt + discount + othr crgs + round off minus - round off plus */
        /*var pending_mount = +receipt_amount + +loss_amount + +discount + +other_charges + +round_off_minus - +round_off_plus;*/
        var pending_mount = +receipt_amount + +loss_amount + +discount + +other_charges + +round_off_minus;
        remaining_amount = +grand_total - +already_paid_amount - pending_mount;
        over_all_amount = parseFloat(receipt_amount);
        
        /*if(receipt_amount_old > 0){
         remaining_amount = receipt_amount_old;
        }*/
        /*if (ths.find('[name^=icon_gain_loss_amount]').val() == 'plus') {
            over_all_amount += parseFloat(gain_loss_amount);
        }*/
        if (ths.find('[name^=icon_round_off]').val() == 'plus') {
            over_all_amount += parseFloat(round_off);
        }
        return {'remaining_amount': parseFloat(remaining_amount).toFixed(2), 'over_all_amount': over_all_amount};
    }

    function calculateAmount(ths) {
        var reference_type = $('#reference_type').val();
        if (reference_type == "")
            reference_type = 'sales';
        sales_id = ths.find('[name=reference_number]').attr('id');
        data = all_sales_obj['sales_' + sales_id];
        var total_receipt_amount = $('#total_receipt_amount').val();
        var all_receipt_total = 0;
        var row_no = ths.attr('row');
        if (!row_no)
            row_no = 1;

        if (sales_id != "" && typeof sales_id != undefined && !isNaN(sales_id)) {
            
            ths.find('.total_invoice_amount').text(parseFloat(data.sales_grand_total).toFixed(2));
            var grand_total = (parseFloat(data.sales_grand_total) + parseFloat(data.credit_note_amount) - parseFloat(data.debit_note_amount)).toFixed(2);
            var remaining_amount = (grand_total - parseFloat(data.sales_paid_amount)).toFixed(2);
            var receipt_amount = (ths.find('#receipt_amount_' + row_no).val()) ? ths.find('#receipt_amount_' + row_no).val() : 0;
            
            var current_paid_amount = parseFloat(data.sales_paid_amount).toFixed(2);
            var current_invoice_amount = parseFloat(grand_total).toFixed(2);
            var receipt_amount_old = parseFloat(ths.find('#old_receipt_amount_' + row_no).val());
            var total_paid_amount = data.sales_paid_amount;
            if (isNaN(receipt_amount_old))
                receipt_amount_old = 0;
            var paid_amount = (parseFloat(current_paid_amount) - parseFloat(receipt_amount_old)).toFixed(2);
            GetRemianAmount = getRemainigAmount(ths, data);
            remaining_amount = GetRemianAmount.remaining_amount;
            over_all_amount = GetRemianAmount.over_all_amount;
            /*total_paid_amount = (parseFloat(paid_amount) + parseFloat(over_all_amount)).toFixed(2);*/
            if (remaining_amount >= 0) {
                /*ths.find('#paid_amount_'+row_no).val(parseFloat(total_paid_amount));*/
                ths.find('#paid_amount_' + row_no).val(global_round_off(parseFloat(data.sales_paid_amount)));
                ths.find('#remaining_amount_' + row_no).val(global_round_off(parseFloat(remaining_amount)));
                ths.find('#pending_amount_' + row_no).val(global_round_off(parseFloat(remaining_amount)));
                ths.find('#invoice_total_' + row_no).val(global_round_off(parseFloat(grand_total)));
                ths.find('#last_receipt_amount_' + row_no).val(receipt_amount);
                ths.find('#current_invoice_amount_' + row_no).val(global_round_off(parseFloat(grand_total)));
                ths.find('#current_paid_amount_' + row_no).val(parseFloat(data.sales_paid_amount));
                ths.find('#old_receipt_amount_' + row_no).val(0);
            }
        } else {
            if(sales_id != "opening_balance"){
                ths.find('#receipt_amount_' + row_no).val('');
                ths.find('#paid_amount_' + row_no).val('');
                ths.find('#remaining_amount_' + row_no).val('');
                ths.find('#pending_amount_' + row_no).val('');
                ths.find('#invoice_total_' + row_no).val('');
                ths.find('#current_invoice_amount_' + row_no).val('');
                ths.find('#current_paid_amount_' + row_no).val('');
                ths.find('#old_receipt_amount_' + row_no).val('');
            }
        }
        checkExcessAmount();
    }

    $(document).on('change', '[name=receipt_amount]', function () {
        ths = $(this).closest('tr');
        sales_id = ths.find('[name=reference_number]').attr('id');
        var row_no = ths.attr('row');
        if (!row_no)
            row_no = 1;
        
        if (sales_id != "" && typeof sales_id != undefined && !isNaN(sales_id) && sales_id != 'opening_balance') {
            data = all_sales_obj['sales_' + sales_id];
            var GetRemianAmount = getRemainigAmount(ths, data);
           
            if (GetRemianAmount.remaining_amount < 0) {
                alert_d.text ='Total Received Amount should not exceed total invoice amount!';
                PNotify.error(alert_d); 
                /*alert("Total Received Amount should not exceed total invoice amount!");*/
                var last_receipt_amount_1 = ths.find('#last_receipt_amount_' + row_no).val();
                if (last_receipt_amount_1 == '' || isNaN(last_receipt_amount_1) || typeof last_receipt_amount_1 == 'undefined')
                    last_receipt_amount_1 = 0;
                ths.find('#receipt_amount_' + row_no).val(parseFloat(last_receipt_amount_1));
                calculateAmount(ths);
                return false;
            } else {
                calculateAmount(ths);
            }
        }
        if(sales_id == 'opening_balance'){
            var blnc = parseFloat($(this).attr('pending_amount'));
            var receipt_amount = parseFloat($(this).val());
            
            if (receipt_amount > blnc) {
                alert_d.text ='Total Received Amount should not exceed total opening balance!';
                PNotify.error(alert_d); 
                /*alert("Total Received Amount should not exceed total invoice amount!");*/
                var last_receipt_amount_1 = ths.find('#last_receipt_amount_' + row_no).val();
                if (last_receipt_amount_1 == '' || isNaN(last_receipt_amount_1) || typeof last_receipt_amount_1 == 'undefined')
                    last_receipt_amount_1 = 0;
                ths.find('#receipt_amount_' + row_no).val(global_round_off(parseFloat(last_receipt_amount_1)));
                
                calculateAmount(ths);
                return false;
            }
            console.log(blnc - receipt_amount);
        }
    });

    $(document).on('change', '[id^=round_off],[id^=discount]', function () {
        IsChangeValid($(this).attr('id'));
    });

    function checkExcessAmount() {
        var is_pending = 0;
        $(document).find('[name=pending_amount]').each(function () {
            if ($(this).val() <= 0) {
                is_pending = 1;
            }
        })
        if (!is_pending) {
            $(document).find('[name=reference_number]').each(function () {
                if ($(this).val() == 'excess_amount') {
                    $(this).closest('.reference_number_div').find('.remove_row_button').trigger('click');
                }
            })
        }
    }

    function IsChangeValid(id) {
        ths = $('#' + id).closest('.inv_row_count');
        var val = ths.find('#' + id).val();
        sales_id = ths.find('[name=reference_number]').attr('id');
        /*if(val > parseFloat(ths.find('[name=receipt_amount]').val())){
         
         alert('Amount should not exceed receipt amount!');
         
         return false;
         
         }*/
        if (sales_id != "" && typeof sales_id != undefined && !isNaN(sales_id)) {
            data = all_sales_obj['sales_' + sales_id];
            sales_grand_total = parseFloat(data.sales_grand_total);
            /*if( val > sales_grand_total){
             
             alert('Amount should not grater than total paid amount!');
             
             ths.find('#'+id).val(0);
             
             calculateAmount(ths);
             
             return false;
             
             }*/
            var row_no = ths.attr('row');
            if (!row_no)
                row_no = 1;
            var GetRemianAmount = getRemainigAmount(ths, data);
          
            if (GetRemianAmount.remaining_amount < 0) {
                alert_d.text ='Amount should not grater than total paid amount!';
                PNotify.error(alert_d); 
                /*alert('Amount should not grater than total paid amount!');*/
                ths.find('#' + id).val(0);
                calculateAmount(ths);
                return false;
            } else {
                calculateAmount(ths);
            }
        }
    }

    $("#payment_mode").on("change", function (event) {
        var payment_mode = $('#payment_mode').val();
        if (payment_mode == null || payment_mode == "") {
            $('#hide').hide();
            $("#other_payment").hide();
        } else {
            if ((payment_mode != "cash" && payment_mode != "" && payment_mode != "other payment mode") || payment_mode == "bank") {
                $('#hide').show();
                $("#other_payment").hide();
            } else
            {
                $('#hide').hide();
                $("#bank_name").val('');
                $("#cheque_number").val('');
                $("#cheque_date").val('');
            }
            if (payment_mode == "other payment mode") {
                $("#other_payment").show();
            } else
            {
                $("#other_payment").hide();
                $("#payment_via").val('');
                $("#reff_number").val('');
            }
        }
    });

    $('#add_row_field').on("click", ".remove_field", function (e) { //user click on remove text
        e.preventDefault();
        // parent().parent().remove()
        var id = $(this).attr('id');
        var split = id.split('_');
        reference_array_list[split[1] - 1] = "";
        $(this).parent('div').parent('div').parent('div').remove();
    });
});