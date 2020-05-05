var reference_array = new Array();
var reference_array_list = new Array();
$(document).ready(function (){
    if ($('#payment_mode').val() == "cash" || $('#payment_mode').val() == "" || $('#payment_mode').val() == 'other payment mode'){
        $('#hide').hide();
    }
    var count = 0;
    $('.add_supplier_row_button').click(function () {

        var reference_html = $(this).closest('.main_row_supplier').find('#supplier').html();
        $(document).find('[name=supplier]').each(function () {
            if ($(this).val() != '') {
                $('#common_select_supplier').find('option[value=' + $(this).val() + ']').remove();
            }
        });
        var reference_html = $('#common_select_supplier').html();
        var row = '<div class="row row_count" row="' + row_count + '"><div class="col-sm-3"><div class="form-group"> <label for="supplier">supplier<span class="validation-color">*</span></label> <select class="form-control select2 supplier" id="supplier' + row_count + '" name="supplier">' + reference_html + ' </select><span class="validation-color" id="err_supplier"></span></div></div><div class="col-sm-3"> <div class="form-group"> <label for="supplier">Actual payment amount<span class="validation-color">*</span></label> <input type="text" class="form-control number_only" id="total_payment_amount' + row_count + '" name="total_payment_amount" value=""><span class="validation-color" id="err_total_payment_amount"></span></div></div><div class="col-sm-3"><div class="form-group" style="padding-top: 15px;"><div class="btn-group"><a type="buttion" href="javascript:void(0);" class="remove_row_button_supplier pull-right disabled"><i class="fa fa-minus"></i></a><a type="buttion" href="javascript:void(0);" class="invoice_edit pull-right disabled"><i class="fa fa-pencil"></i></a></div> </div></div></div>';
        /*$clone = '<div class="row row_count" row="'+row_count+'">'+$clone+'</div>';
   
        $('.add_row_button', $clone).text('');*/
        $("#add_row_field_supplier").append(row);
    });

    $(document).on("click", ".remove_row_button_supplier", function () {
        $(this).closest('.row').remove();
    });
  
    $(document).on('click','.toggle_plus',function(){
        var i = $(this).find('i');
        var dv = $(this).closest('.input-group');
        var lbl = $(this).attr('name');

        if(i.hasClass('fa-plus')){
            dv.find('input[type=hidden]').val('minus');
            i.removeClass('fa-plus').addClass('fa-minus');
            if(lbl == 'Loss')lbl = 'Gain';
            dv.parent().find('.toggle_lbl').text(lbl +'(-)');
        }else{
            dv.find('input[type=hidden]').val('plus');
            i.removeClass('fa-minus').addClass('fa-plus');
            dv.parent().find('.toggle_lbl').text(lbl +'(+)');
        }
        dv.find('input[type=text]').trigger('change');
    })
    //date change
    $('#voucher_date').on('changeDate', function (){
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
                data:{
                        date: selected,
                        privilege: privilege,
                        module_id: module_id
                    },
                success: function (data){
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
    var all_purchase_obj = {};
    if(typeof data_items != 'undefined'){
        if(data_items.length > 0){
            all_purchase_obj = $.parseJSON(data_items);
        }
    }

    $(document).on('change','[name=supplier]',function(){
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

    var XHpayment;
    $(document).on('click','.invoice_edit', function (event){
        var currency_id = $('#currency_id').val();
        var supplier_id = $(this).closest('.row_count').find('[name=supplier]').val();
        
        var reference_type = $('#reference_type').val();
        if (reference_type == "") var reference_type = 'purchase';
        
        var row_count = $('.row_count').length;
        var inv_row_count = $('.inv_row_count').length;

        if (row_count > 1) {
            $('#add_row_field').html('');
        }
        $('#payment_popup_view table#payment_data').find('tbody').hide();
        if (supplier_id) {
            if($('#payment_popup_view table#payment_data').find('tbody#payment_data_'+supplier_id).length > 0){
                $('#payment_popup_view table#payment_data').find('tbody#payment_data_'+supplier_id).show();
                $('#payment_popup_view').modal('show');
            }else{
                $('#payment_popup_view #loader').show();
                $('#payment_popup_view').modal('show');
                if(XHpayment && XHpayment.readyState != 4){
                    XHpayment.abort();
                }
                
                XHpayment = $.ajax({
                    url: base_url + 'payment_voucher/get_purchase_invoice_number',
                    type: 'POST',
                    dataType:'json',
                    data: {supplier_id: supplier_id, currency_id: currency_id, reference_type: reference_type},
                    success: function (result) {
                        $('#payment_popup_view #loader').hide();
                        /*var parsedJson = $.parseJSON(result);*/
                        var data = result.data;
                        $(data).each(function(k,v){
                            all_purchase_obj['purchase_'+v.purchase_id] = v;
                        })
                        $row = '';
                        if(result.opening_balance){
                            if(result.opening_balance > 0){
                                inv_row_count++;
                                $row += '<tr class="inv_row_count" row="'+inv_row_count+'"><td class="reference_number_div"><input type="hidden" class="form-control" id="last_payment_amount_'+inv_row_count+'" name="last_payment_amount" value="'+global_round_off(parseFloat(result.pending_amount))+'"><input type="hidden" name="opening_balance_id" value="'+result.opening_balance_id+'"><input type="checkbox" name="reference_number" id="opening_balance" class="payment_voucher_number" checked value="opening_balance"></td><td>Opening Balance</td><td>'+global_round_off(parseFloat(result.opening_balance))+'</td><td colspan="4"><input type="text" class="form-control number_only" id="payment_amount_'+inv_row_count+'" name="payment_amount" data-id="'+supplier_id+'" ledger_id="'+result.ledger_id+'" paid_amount="'+global_round_off(parseFloat(result.paid_amount))+'" blnc="'+global_round_off(parseFloat(result.opening_balance))+'" pending_amount="'+global_round_off(parseFloat(result.pending_amount))+'" value="'+ global_round_off(parseFloat(result.pending_amount)) + '" autocomplete="off"></td></tr>';
                                //<td><input type="text" value="'+global_round_off(parseFloat(result.pending_amount))+'" id="pending_amount_'+inv_row_count+'" disabled="disabled" name="pending_amount"></td>
                            }
                        }
                        
                        if(data.length > 0){
                            $(data).each(function (k, v) {
                                inv_row_count++;
                                var grand_total = (parseFloat(v.purchase_grand_total));
                                if(v.credit_note_amount)
                                var grand_total = (parseFloat(v.purchase_grand_total) + parseFloat(v.credit_note_amount) - parseFloat(v.debit_note_amount)).toFixed(2);
                                $row += '<tr class="inv_row_count" row="'+inv_row_count+'"><td class="reference_number_div"><input type="checkbox" name="reference_number" id="'+v.purchase_id+'"  class="payment_voucher_number" checked value="'+v.purchase_id+'"></td><td>'+v.purchase_invoice_number+'</td><td>'+ parseFloat(v.purchase_grand_total).toFixed(2) + '</td><td class="discount_div"><input type="text" class="form-control number_only" id="discount_'+inv_row_count+'" data-id="'+v.purchase_id+'" name="discount" value=""></td><td class="round_off_div"><div class="input-group"><input type="hidden" name="icon_round_off" value="plus"><span class="input-group-addon toggle_plus" id="Input_addon_roundoff" name="Round Off" style="width:20px;"><i id="roundoff_button" class="fa fa-plus"></i></span><input type="text" class="form-control number_only" name="round_off" id="round_off_'+inv_row_count+'" data-id="'+v.purchase_id+'" stlye="float: left;"></div><span class="validation-color" id="err_grand_total_'+inv_row_count+'"></span></td><td class="payment_amount_div"><input type="text" class="form-control number_only" id="payment_amount_'+inv_row_count+'" name="payment_amount" data-id="'+v.purchase_id+'" value=""><input type="hidden" class="form-control number_only" id="paid_amount_'+inv_row_count+'" class="form-control" name="paid_amount" value="" readonly=""><input type="hidden" class="form-control" id="remaining_amount_'+inv_row_count+'" name="remaining_amount" value="3000.00"></td><input type="hidden" class="form-control" id="reference_number_text_'+inv_row_count+'" name="reference_number_text" value="'+v.purchase_invoice_number+'"><input type="hidden" class="form-control" id="current_paid_amount_'+inv_row_count+'" name="current_paid_amount" value="'+parseFloat(v.purchase_paid_amount).toFixed(2)+'"><input type="hidden" class="form-control" id="current_invoice_amount_'+inv_row_count+'" name="current_invoice_amount" value="'+parseFloat(v.purchase_grand_total).toFixed(2)+'"><input type="hidden" class="form-control" id="old_payment_amount_'+inv_row_count+'" name="old_payment_amount" value="0"><input type="hidden" class="form-control" id="last_payment_amount_'+inv_row_count+'" name="last_payment_amount" value="0"><td><input type="text" value="'+(grand_total - parseFloat(v.purchase_paid_amount)).toFixed(2)+'" id="pending_amount_'+inv_row_count+'" disabled="disabled" name="pending_amount"><input type="hidden" class="float_number form-control" id="invoice_total_'+inv_row_count+'" name="invoice_total" value="3000.00"></td></tr>';
                                
                            })
                        }
                        if($row == ''){
                            inv_row_count++;
                            $row += '<tr class="inv_row_count" row="'+inv_row_count+'" style="text-align: center;"><td colspan="7">No data found!</td></tr>';
                        }
                        $row = "<tbody id='payment_data_"+supplier_id+"' cust_id='"+supplier_id+"'>"+$row+"</tbody>";
                        $('#payment_popup_view table#payment_data').append($row);
                        /*$('#payment_popup_view').modal('show');*/
                    },error:function(){
                        $('#payment_popup_view #loader').hide();
                    }
                });
            }
        }
    });
    
    $(document).on('click', '.payment_voucher_number', function () {
        var val =$(this).attr('id');
        if($(this).prop("checked") == true){
            $(this).parents('tr:first').find("[data-id='"+val+"']").prop('disabled',false);
        }else{
            $(this).parents('tr:first').find("[data-id='"+val+"']").prop('disabled',true);
        }
    });

    $(document).on('click','.auto_set',function(){
        var cust_id = $('#payment_popup_view').find('tbody:visible').attr('cust_id');
        var total_payment_amount = $(document).find('.row_count[cust_id='+cust_id+'] [name=total_payment_amount]').val();
        var remains = total_payment_amount;
       
        var row_cnt = 0;
        $('#payment_popup_view').find('tbody:visible tr.inv_row_count').each(function(){
            row_cnt++;
            $(this).find('[name=payment_amount]').val(0);
            purchase_id = $(this).find('[name=reference_number]').attr('id');
            if(purchase_id != 'excess_amount'){
                if(purchase_id == 'opening_balance'){
                    remaining_amount = $(this).find('[name=payment_amount]').attr('pending_amount');
                    
                }else{
                    data = all_purchase_obj['purchase_' + purchase_id];
                    GetRemianAmount = getRemainigAmount($(this), data);
                    remaining_amount = GetRemianAmount.remaining_amount;
                    over_all_amount = GetRemianAmount.over_all_amount;
                }

                if(remaining_amount > 0 && remains > 0){
                    assign = remains - getFloatNumber(remaining_amount);
                    if(assign <= 0){
                        $(this).find('[name=payment_amount]').val(global_round_off(remains));
                        remains = 0;
                    }else{
                        $(this).find('[name=payment_amount]').val(global_round_off(remaining_amount));
                        remains = remains - getFloatNumber(remaining_amount);
                    }
                    $(this).find('[name=payment_amount]').trigger('change');
                }
            }
        });
        var tbd = $('#payment_popup_view').find('tbody:visible');
        if(remains > 0){
            if(tbd.find('#excess_amount').length > 0){
                var el = tbd.find('#excess_amount');
                el.closest('tr').find('[name=payment_amount]').val(global_round_off(parseFloat(remains)));
            }else{
                row_cnt++;
                var row = '<tr class="inv_row_count" row="'+row_cnt+'"><td class="reference_number_div"><input type="checkbox" name="reference_number" id="excess_amount"  class="payment_voucher_number" checked value="excess_amount"></td><td colspan="2">Excess Amount</td><td colspan="4"><input type="text" class="form-control number_only" id="payment_amount_'+row_cnt+'" name="payment_amount" data-id="'+cust_id+'" value="'+ global_round_off(parseFloat(remains)) + '" autocomplete="off"></td></tr>';
                tbd.append(row);
            }
        }else{
            tbd.find('#excess_amount').remove();
        }
    })
    
    $(document).on('click','#save_invoice',function(){
        var final_json = new Array();
        var all_payment_total = 0;
        var cust_id = $('#payment_popup_view').find('tbody:visible').attr('cust_id');
        $('#payment_popup_view').find('tbody:visible tr.inv_row_count').each(function(){
          if($(this).find('[name=reference_number]').is(':checked') && $(this).find('[name=payment_amount]').val() != ''){
            var payment_total_paid = 0;
            payment_total_paid +=getFloatNumber($(this).find('[name=payment_amount]').val());
            all_payment_total +=getFloatNumber($(this).find('[name=payment_amount]').val());

            if($(this).find('[name=reference_number]').val() != 'excess_amount' && $(this).find('[name=reference_number]').val() != 'opening_balance'){
                payment_total_paid +=($(this).find('[name=discount]').val() != '') ? getFloatNumber($(this).find('[name=discount]').val()) : 0;
               
                if($(this).find('[name=icon_round_off]').val() == 'plus'){
                    //payment_total_paid -= ($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                    all_payment_total += ($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                }else{
                    payment_total_paid +=($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                   // all_payment_total +=($(this).find('[name=round_off]').val() != '') ? getFloatNumber($(this).find('[name=round_off]').val()) : 0;
                }
                if($(this).find('[name=pending_amount]').val() <= 0){
                    $('#excess_purchase_id').val($(this).find('[name=reference_number]').val());
                }
                var data_item = {
                    reference_number_text : $(this).find('[name=reference_number_text]').val(),
                    reference_number: $(this).find('[name=reference_number]').val(),
                    payment_amount: $(this).find('[name=payment_amount]').val(),
                    payment_total_paid : payment_total_paid,
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
                            excess_sales_id : $(this).find('[name=excess_sales_id]').val(),
                            opening_balance_id : $(this).find('[name=opening_balance_id]').val(),
                            opening_balance : $(this).find('[name=payment_amount]').attr('blnc'),
                            ledger_id : $(this).find('[name=payment_amount]').attr('ledger_id'),
                            paid_amount : $(this).find('[name=payment_amount]').attr('paid_amount'),
                            reference_number_text: $(this).find('[name=reference_number]').val(),
                            reference_number: $(this).find('[name=reference_number]').val(),
                            payment_amount: $(this).find('[name=payment_amount]').val(),
                            payment_total_paid:$(this).find('[name=payment_amount]').val(),
                        };
            }
            final_json.push(data_item);
          }
          console.log(final_json);
        });
        $(document).find('[name=supplier]').each(function(){
          
            if($(this).val() == cust_id){
                $(this).closest('.row_count').find('[name=total_payment_amount]').val(global_round_off(parseFloat(all_payment_total)));
            }
        });
        $('#payment_popup_view').modal('hide');
    })
    
    function getRemainigAmount(ths,data){
        var row_no = ths.attr('row');
        if(!row_no) row_no = 1;
        //var grand_total = (parseFloat(data.purchase_grand_total) + parseFloat(data.credit_note_amount) - parseFloat(data.debit_note_amount)).toFixed(2);
        var grand_total = parseFloat(data.purchase_grand_total).toFixed(2);
        var remaining_amount = (grand_total - parseFloat(data.purchase_paid_amount)).toFixed(2);
        var already_paid_amount = parseFloat(data.purchase_paid_amount).toFixed(2);
        
        var payment_amount = (ths.find('#payment_amount_'+row_no).val()) ? ths.find('#payment_amount_'+row_no).val() : 0;
        var gain_loss_amount = 0;//(ths.find('#gain_loss_amount_'+row_no).val()) ? ths.find('#gain_loss_amount_'+row_no).val() : 0;
        var loss_amount = 0
        //if(ths.find('[name=icon_gain_loss_amount]').val() != 'plus') loss_amount= gain_loss_amount;
        var discount = (ths.find('#discount_'+row_no).val()) ? ths.find('#discount_'+row_no).val() : 0;
        var other_charges = 0;//(ths.find('#other_charges_'+row_no).val()) ? ths.find('#other_charges_'+row_no).val() : 0;
        
        var round_off = (ths.find('#round_off_'+row_no).val()) ? ths.find('#round_off_'+row_no).val() : 0;
        round_off_minus = round_off_plus = 0;
        if(ths.find('[name=icon_round_off]').val() == 'plus'){
            round_off_plus =  round_off;
        }else{
            round_off_minus =  round_off;
        }
        var payment_amount_old = parseFloat(ths.find('#old_payment_amount_'+row_no).val());
        
        if(isNaN(payment_amount_old)) payment_amount_old = 0;
       /* var paid_amount = (parseFloat(current_paid_amount) - parseFloat(payment_amount_old)).toFixed(2);*/
        /* peinding amount formula peinding = recipt + discount + othr crgs + round off minus - round off plus */ 
        /*var pending_mount = +payment_amount + +loss_amount + +discount + +other_charges + +round_off_minus - +round_off_plus;*/ 
        var pending_mount = +payment_amount + +loss_amount + +discount + +other_charges + +round_off_minus; 
        remaining_amount = +grand_total - +already_paid_amount - pending_mount;
        
        over_all_amount = parseFloat(payment_amount);
        if(ths.find('[name^=icon_gain_loss_amount]').val() == 'plus'){
            over_all_amount += parseFloat(gain_loss_amount);
        }
        if(ths.find('[name^=icon_round_off]').val() == 'plus'){
            over_all_amount += parseFloat(round_off);
        }
        return {'remaining_amount':parseFloat(remaining_amount).toFixed(2),'over_all_amount':over_all_amount};
    }

    function calculateAmount(ths){
        var reference_type = $('#reference_type').val();
        if (reference_type == "") reference_type = 'purchase';
        purchase_id = ths.find('[name=reference_number]').val();
        data = all_purchase_obj['purchase_'+purchase_id];
        
        var total_payment_amount = $('#total_payment_amount').val();
        var all_payment_total = 0;
        var row_no = ths.attr('row');
        if(!row_no) row_no = 1;
        if (purchase_id != "" && purchase_id != "excess_amount" && typeof purchase_id != undefined && !isNaN(purchase_id)){
            var reference_number_text = ths.find('#reference_number_text_'+row_no).val();
            ths.find('.total_invoice_amount').text(parseFloat(data.purchase_grand_total).toFixed(2));
            var grand_total = (parseFloat(data.purchase_grand_total) + parseFloat(data.credit_note_amount) - parseFloat(data.debit_note_amount)).toFixed(2);
            var remaining_amount = (grand_total - parseFloat(data.purchase_paid_amount)).toFixed(2);
            var payment_amount = (ths.find('#payment_amount_'+row_no).val()) ? ths.find('#payment_amount_'+row_no).val() : 0;
           
            var current_paid_amount = parseFloat(data.purchase_paid_amount).toFixed(2);
            var current_invoice_amount = parseFloat(grand_total).toFixed(2);
            var payment_amount_old = parseFloat(ths.find('#old_payment_amount_'+row_no).val());
            var total_paid_amount = data.purchase_paid_amount;
            if(isNaN(payment_amount_old)) payment_amount_old = 0;
            var paid_amount = (parseFloat(current_paid_amount) - parseFloat(payment_amount_old)).toFixed(2);
            
            GetRemianAmount = getRemainigAmount(ths,data);
            remaining_amount = GetRemianAmount.remaining_amount;
            over_all_amount = GetRemianAmount.over_all_amount;
            /*total_paid_amount = (parseFloat(paid_amount) + parseFloat(over_all_amount)).toFixed(2);*/
            
            if(remaining_amount >= 0 ){
                /*ths.find('#paid_amount_'+row_no).val(parseFloat(total_paid_amount));*/
                ths.find('#paid_amount_'+row_no).val(global_round_off(parseFloat(data.purchase_paid_amount)));
                ths.find('#remaining_amount_'+row_no).val(global_round_off(parseFloat(remaining_amount)));
                ths.find('#pending_amount_'+row_no).val(global_round_off(parseFloat(remaining_amount)));
                ths.find('#invoice_total_'+row_no).val(global_round_off(parseFloat(grand_total)));
                ths.find('#last_payment_amount_'+row_no).val(payment_amount);
                ths.find('#current_invoice_amount_'+row_no).val(global_round_off(parseFloat(grand_total)));
                ths.find('#current_paid_amount_'+row_no).val(parseFloat(data.purchase_paid_amount));
                ths.find('#old_payment_amount_'+row_no).val(0);
            }
        }else if(purchase_id != "excess_amount" && purchase_id != "opening_balance"){
            ths.find('#payment_amount_'+row_no).val('');
            ths.find('#paid_amount_'+row_no).val('');
            ths.find('#remaining_amount_'+row_no).val('');
            ths.find('#pending_amount_'+row_no).val('');
            ths.find('#invoice_total_'+row_no).val('');
            ths.find('#current_invoice_amount_'+row_no).val('');
            ths.find('#current_paid_amount_'+row_no).val('');
            ths.find('#old_payment_amount_'+row_no).val('');
        }
        checkExcessAmount();
    }
    $(document).on('change','[name=payment_amount]',function(){
        ths = $(this).closest('tr');
        purchase_id = ths.find('[name=reference_number]').val();
        if (purchase_id != "" && purchase_id != "excess_amount" && typeof purchase_id != undefined && !isNaN(purchase_id) && purchase_id != 'opening_balance'){
            
            data = all_purchase_obj['purchase_'+purchase_id];
            var row_no = ths.attr('row');
            if(!row_no) row_no = 1;
            var GetRemianAmount = getRemainigAmount(ths,data);
            if(GetRemianAmount.remaining_amount < 0 ){
                alert_d.text ='Total Payment Amount should not exceed total invoice amount!';
                PNotify.error(alert_d); 
                /*alert("Total Received Amount should not exceed total invoice amount!");*/
                var last_payment_amount_1 = ths.find('#last_payment_amount_'+row_no).val();
                
                if(last_payment_amount_1 == '' || isNaN(last_payment_amount_1) || typeof last_payment_amount_1 == 'undefined') last_payment_amount_1 = 0;
                ths.find('#payment_amount_'+row_no).val(parseFloat(last_payment_amount_1));
                calculateAmount(ths);
                return false;
            }else{
                calculateAmount(ths);
            }
        }
        if(purchase_id == 'opening_balance'){
            var blnc = parseFloat($(this).attr('pending_amount'));
            var payment_amount = parseFloat($(this).val());
            /*var opening_balance = parseFloat($(this).attr('blnc')).toFixed(2);
            var pending_amount = parseFloat($(this).attr('pending_amount')).toFixed(2);
            var pending_amount = parseFloat(ths.find('[name=pending_amount]').val()).toFixed(2);
            var remain = +opening_balance - +pending_amount;
            var payment_amount = parseFloat($(this).val());
            console.log(+opening_balance,+pending_amount);*/
            if (payment_amount > blnc) {
                alert_d.text ='Total Received Amount should not exceed total opening balance!';
                PNotify.error(alert_d); 
                /*alert("Total Received Amount should not exceed total invoice amount!");*/
                var last_payment_amount_1 = ths.find('#last_payment_amount_' + row_no).val();
                if (last_payment_amount_1 == '' || isNaN(last_payment_amount_1) || typeof last_payment_amount_1 == 'undefined')
                    last_payment_amount_1 = 0;
                ths.find('#payment_amount_' + row_no).val(global_round_off(parseFloat(last_payment_amount_1)));
                
                calculateAmount(ths);
                return false;
            }/*else{
                pending_amount = pending_amount - payment_amount;
                ths.find('[name=pending_amount]').val(pending_amount)
            }*/
        }
    });
    $(document).on('change','[id^=round_off],[id^=discount]',function(){
        IsChangeValid($(this).attr('id'));
    });

    function checkExcessAmount(){
        var is_pending = 0;
        $('#payment_popup_view').find('tbody:visible [name=pending_amount]').each(function(){
            if($(this).val() <= 0){
                is_pending = 1;
            }
        })
    
        if(!is_pending){
            $('#payment_popup_view').find('tbody:visible [name=reference_number]').each(function(){
                if($(this).val() == 'excess_amount'){
                    $(this).closest('.reference_number_div').find('.remove_row_button').trigger('click');
                }
            })
        }
    }

    function IsChangeValid(id) {
        ths = $('#'+id).closest('.inv_row_count');
        var val = ths.find('#'+id).val();
        purchase_id = ths.find('[name^=reference_number]').val();
        /*if(val > parseFloat(ths.find('[name=payment_amount]').val())){
            alert('Amount should not exceed payment amount!');
            return false;
        }*/
        if (purchase_id != "" && typeof purchase_id != undefined && !isNaN(purchase_id)){
            data = all_purchase_obj['purchase_'+purchase_id];
            purchase_grand_total = parseFloat(data.purchase_grand_total);
            /*if( val > purchase_grand_total){
                alert_d.text ='Amount should not grater than total paid amount!';
                PNotify.error(alert_d); 
                ths.find('#'+id).val(0);
                calculateAmount(ths);
                return false;
            }*/
            var row_no = ths.attr('row');
            if(!row_no) row_no = 1;
            var GetRemianAmount = getRemainigAmount(ths,data);
            if(GetRemianAmount.remaining_amount < 0 ){
                alert_d.text ='Amount should not grater than total paid amount!';
                PNotify.error(alert_d); 
                /*alert('Amount should not grater than total paid amount!');*/
                ths.find('#'+id).val(0);
                calculateAmount(ths);
                return false;
            }else{
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
