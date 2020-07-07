$(document).ready(function () {
    
    //getLedgersList();

    getLedgersListAll($('.add_voucher').data('value'));
    $(document).on('change','[name=check_voucher]',function(){
        var voucher_id = $(this).val();
        var voucher_type =$(this).attr('vtype');
        if($(this).is(':checked')){
            $(document).find('[name=check_voucher]').prop('checked',false);

            $(this).prop('checked',true);
            $('.edit_voucher').attr('voucher_type',voucher_type).show();
            $('.edit_voucher').attr('voucher_id',voucher_id);
            $('.delete_voucher').attr('voucher_type',voucher_type).show();
            $('.delete_voucher').attr('voucher_id',voucher_id);
            $('.add_voucher').hide();
            $('.download_voucher').hide();
            $('.upload_voucher_popup').hide();
        }else{
            $(document).find('[name=check_voucher]').prop('checked',false);
            $('.edit_voucher').hide();
            $('.delete_voucher').hide();
            $('.add_voucher').show();
            $('.download_voucher').show();
            $('.upload_voucher_popup').show();
            setDefault();
        }
    })

    $('.upload_voucher_popup').click(function(){
        $('#upload_doc').modal({show:true, backdrop: 'static', keyboard: false});
    })

    $(document).on('click',".save_bulk",function(){
        var bulk_voucher = $('#upload_bulk_voucher [name=bulk_voucher]').val();
        if(bulk_voucher != ''){
           /*alert('Select file to upload!');*/
           $('#upload_doc').hide();
           alert_d.text = 'success file to upload!';
           PNotify.success(alert_d);
           return true;
       }else{
           alert_d.text = 'select file to upload!';
           PNotify.error(alert_d);
           return false;
       }
    })

    $(document).on('click','.delete_voucher',function(){
        if(confirm('Are you sure you want to delete?')){
            var voucher_id = $(this).attr('voucher_id');
            var voucher_type =$(this).attr('voucher_type');
            $(this).attr('disabled',true);
            $.ajax({
                url: base_url +'general_voucher/deleteVoucher',
                type:'post',
                dataType:'json',
                data:{voucher_id:voucher_id,voucher_type:voucher_type},

                success:function(j){
                    if(j.flag){
                        if(getVoucher) getVoucher.destroy();
                        getVoucher = GetAllVoucher();
                        $('.delete_voucher').attr('disabled',false);
                        alert_d.text = j.msg;
                        PNotify.success(alert_d);
                    }else{
                        if(getVoucher) getVoucher.destroy();
                        getVoucher = GetAllVoucher();
                        $('.delete_voucher').attr('disabled',false);
                        alert_d.text = j.msg;
                        PNotify.error(alert_d);
                    }
                },error:function(){
                    $('.delete_voucher').attr('disabled',false);
                }
            })
        }
     
    })
    $(document).on('click','.edit_voucher',function(){   
        setDefault();
        var voucher_id = $(this).attr('voucher_id');
        var voucher_type =$(this).attr('voucher_type');
        if(voucher_type == 'journal'){
            /*getLedgersList();*/
            $("#voucher_type_text_class").addClass("hidden");
            $("#voucher_type_class").removeClass("hidden");
            AppendOption();
        }
        if(voucher_type == 'cash'){
           $("#voucher_type").html("");
           $("#voucher_type").append('<option value="cash" selected>Cash</option>');
           $("#voucher_type_text").val('Cash');
           $("#voucher_type_text").attr('disabled','disabled');
           /*getLedgersListAll('cash');*/
           AppendOption();
           $("#invoice_ledger").attr('disabled','disabled');
        }
        if(voucher_type == 'bank'){
           $("#voucher_type").html("");
           $("#voucher_type").append('<option value="bank" selected>Bank</option>');
           $("#voucher_type_text").val('Bank');
           $("#voucher_type_text").attr('disabled','disabled');
           AppendOption();
           /*getLedgersListAll('bank');*/
        }
        if(voucher_type == 'contra'){
           $("#voucher_type").html("");
           $("#voucher_type").append('<option value="contra" selected>Contra</option>');
           $("#voucher_type_text").val('Contra');
           $("#voucher_type_text").attr('disabled','disabled');
           $("#add_row").hide();
           AppendOption();
           /*getLedgersListAll('contra');*/
           $("#invoice_ledger").attr('disabled','disabled');
        }
        $('#add_voucher #voucher_id').val(0);
        $(this).attr('disabled',true);
        $.ajax({
            url:base_url +'general_voucher/getVoucherDetail',
            type:'post',
            dataType:'json',
            data:{voucher_id:voucher_id,voucher_type:voucher_type},
            success:function(j){
                if(j.length > 0){
                    $('#add_voucher #voucher_type').val(voucher_type).change();
                    $('#add_voucher #voucher_id').val(voucher_id);
                    $('#add_voucher #invoice_date').val(j[0].voucher_date);
                    $('#add_voucher #invoice_number').val(j[0].reference_number);
                    $('#add_voucher #invoice_total').val(j[0].receipt_amount);
                    $('#add_voucher #invoice_narration').val(j[0].note1);
                    $("#add_voucher #voucher_type option").not('option:selected').attr('disabled',true);
                    var total_row = j.length;
                    if(total_row > 2 ){
                        var needToAdd = total_row - 2;
                        for (var k = 0; k < needToAdd; k++) {
                            $('#add_voucher #add_row').trigger('click');
                        }
                    }
                    setTimeout(function(){
                        for (var k = 0; k < j.length; k++) {                           
                            amount_type = 'DR';
                            if(j[k].cr_amount > 0) amount_type = 'CR';
                            $('#add_voucher #row_'+k).find('[name=amount_type]').val(amount_type).change();
                            $('#add_voucher #row_'+k).find('[name=voucher_amount]').val(j[k].voucher_amount);
                          
                            $('#add_voucher #row_'+k).find('[name=invoice_ledger]').val(j[k].ledger_id).change();
                        }
                        $('#add_voucher .modal-title').text("Edit Voucher");
                        $('#add_voucher #AddNewVoucher').text('Update');
                        $('#add_voucher').modal({show:true, backdrop: 'static', keyboard: false});
                        $('.edit_voucher').attr('disabled',false);
                    },500);
                }
            },error:function(){
                $('.edit_voucher').attr('disabled',false);
            }
        })
    })

    $(document).on('click','.add_voucher',function(){     
        var voucher_name = $(this).attr('data-value');
        setDefault();
        $('#add_voucher .modal-title').text("Add Voucher");
        $('#add_voucher #AddNewVoucher').text('Add');
        $('#add_voucher').modal({show: true, backdrop: 'static', keyboard: false});
        if(voucher_name == 'journal'){
            /*getLedgersList();*/
            $("#voucher_type_text_class").addClass("hidden");
            $("#voucher_type_class").removeClass("hidden");
            AppendOption();
        }
        if(voucher_name == 'cash'){
           $("#voucher_type").html("");
           $("#voucher_type").append('<option value="cash" selected>Cash</option>');
           $("#voucher_type_text").val('Cash');
           $("#voucher_type_text").attr('disabled','disabled');
           /*getLedgersListAll('cash');*/
           AppendOption();
           $("#invoice_ledger").attr('disabled','disabled');
        }
        if(voucher_name == 'bank'){
           $("#voucher_type").html("");
           $("#voucher_type").append('<option value="bank" selected>Bank</option>');
           $("#voucher_type_text").val('Bank');
           $("#voucher_type_text").attr('disabled','disabled');
           /*getLedgersListAll('bank');*/
           AppendOption();
        }
        if(voucher_name == 'contra'){
           $("#voucher_type").html("");
           $("#voucher_type").append('<option value="contra" selected>Contra</option>');
           $("#add_row").hide();
           $("#voucher_type_text").val('Contra');
           $("#voucher_type_text").attr('disabled','disabled');
           /*getLedgersListAll('contra');*/
           AppendOption();
           $("#invoice_ledger").attr('disabled','disabled');
        }
    })

    $(document).on('click','#AddNewVoucher',function(){
        var flag = true; //$('#add_voucher input[name=voucher_number]').val() == '' ||
        var invoice_number = $('#add_voucher input[name=invoice_number]').val();
        var invoice_narration = $('#add_voucher input[name=invoice_narration]').val();
        var invoice_date = $('#add_voucher input[name=invoice_date]').val();
        var invoice_total = $('#add_voucher input[name=invoice_total]').val();
        var voucher_type =$('#add_voucher select[name=voucher_type]').val();
        if( invoice_date == '' || voucher_type  == '' || $('#invoice_date').attr('valid') == '0'){// || invoice_narration == ''
            /*alert('fill required fields');*/
            $('#invoice_date').focus();
            alert_d.text = 'fill required fields';
            PNotify.error(alert_d);
            flag = false;
            return false;
        }
        var string_regex = /[a-zA-Z0-9]/;
        
        /*if (!invoice_narration.match(string_regex)) {
            alert('Add valid narration');
            alert_d.text = 'Add valid narration';
            PNotify.error(alert_d);
            
            flag = false;
            return false;
        }*/
        var ledgers = {};
        var i = 0;
        var cr_amnt = dr_amnt = 0;
        var ledger_ids_ary = new Array;
        $('#add_voucher').find('.ledger_row').each(function(){
            var ledger_id = $(this).find("[name=invoice_ledger]").val();
            var amount_type = $(this).find("[name=amount_type]").val();
            var voucher_amount = $(this).find("[name=voucher_amount]").val();
            if(flag == true && (ledger_id == '' || amount_type == '' || voucher_amount == '')){
                alert_d.text = 'Empty fields not allowed!';
                PNotify.error(alert_d);
                /*alert('Empty fields not allowed!');*/
                flag = false;
                return false;
            }
            
            if(flag == true && ledger_ids_ary.length > 0 && ledger_ids_ary.includes(ledger_id)){
                /*alert("Same ledgers not allowed!");*/
                alert_d.text = 'Same ledgers not allowed!';
                PNotify.error(alert_d);          
                flag = false;
                return false;
            }
            if(amount_type == 'CR') cr_amnt += parseFloat(voucher_amount);
            if(amount_type == 'DR') dr_amnt += parseFloat(voucher_amount);
            ledgers[i] = {'ledger_id' : ledger_id ,'amount_type' :amount_type ,'voucher_amount':voucher_amount};
            i++;
            ledger_ids_ary.push(ledger_id);
        });
        
        if(flag == true && (ledger_ids_ary.length <= 0 || cr_amnt != dr_amnt || cr_amnt == 0)){
            /*alert('Invalid ledger amount added');*/
            alert_d.text = 'Invalid ledger amount added';
            PNotify.error(alert_d);
            //alert("Invalid amount added!");
            flag = false;
            return false;
        }
        
        if(voucher_type == 'purchase' || voucher_type == 'sales'){
            /*if (!invoice_number.match(string_regex) || invoice_number == '') {
                alert('Add valid invoice number');
                alert_d.text = 'Add valid invoice number';
                PNotify.error(alert_d);
                
                flag = false;
                return false;
            }*/

            if(flag == true && invoice_total != cr_amnt){
                /*alert('Invalid voucher amount added!');*/
                alert_d.text = 'Invalid voucher amount added!';
                PNotify.error(alert_d);
                flag = false;
                return false;
            }
        }
        var formData = {/*
            'voucher_number' : $('#add_voucher input[name=voucher_number]').val(),*/
            /*'firm_id' : $('input[name=filter_admin_comp]').val(),
            'company_id' : $('select[name=filter_acc_name]').val(),*/
            'voucher_id' : $('#add_voucher #voucher_id').val(),
            'invoice_date': $('#add_voucher input[name=invoice_date]').val(),
            'invoice_number': $('#add_voucher input[name=invoice_number]').val(),
            'invoice_narration': $('#add_voucher input[name=invoice_narration]').val(),
            /*'invoice_total': $('#add_voucher input[name=invoice_total]').val(),*/
            'voucher_type': $('#add_voucher select[name=voucher_type]').val(),
            'ledgers' : ledgers
        };

        if(flag){
            $('#AddNewVoucher').attr('disabled',true);
            $.ajax({
                
                url: base_url +'general_voucher/AddNewVoucher',
                type:'post',
                dataType:'json',
                data:formData,
                  beforeSend: function(){
                   // Show image container
                   $("#loader").show();
                },
                success:function(j){
                    /*alert_d.text = j.msg;*/
                    alert_d.text =j.msg;
                    PNotify.success(alert_d); 
                     $("#loader").hide();  
                    /*alert(j.msg);*/
                    if(j.flag) {
                        /*PNotify.success(alert_d);   */
                        $('#add_voucher').modal('hide');
                        if(getVoucher) getVoucher.destroy();
                        getVoucher = GetAllVoucher();
                        /*SetDefaultClass();*/
                    }
                    $('#AddNewVoucher').attr('disabled',false);
                },error:function(){
                    $('#AddNewVoucher').attr('disabled',false);
                }
            })
            anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});

        }
        
    })

    setTimeout(function(){
        $('.alert').hide();
    },5000);
});

function setDefault(){
    i = 1;
    $('#add_voucher').find('input').val('');
    $('#add_voucher').find('select').val('').prop('selected',true).trigger('change');
    $('#add_voucher #voucher_type').val($('input[name=voucher_type]').val()).prop('selected',true).trigger('change');
    $("#add_voucher #voucher_type option").attr('disabled',false);
    if ($("#voucher_type").length) {
        $("#voucher_type").select2();
    }
    $('#add_voucher').find('.remove_row').each(function(){
        $(this).closest('.ledger_row').remove();
    })
    $('#add_voucher #voucher_id').val(0);
}

function getLedgersList(){
    var firm_id = $('input[name=filter_admin_comp]').val();
    var acc_id = $('select[name=filter_acc_name]').val();
    $.ajax({
       url:base_url +'/general/getLedgersList',
        type:'post',
        dataType:'html',
        data:{},
        success:function(j){
            $(document).find('.filter [name=invoice_ledger]').html(j);
            $(document).find("#add_voucher [name=invoice_ledger]").each(function(){
                $(this).html(j);
            });
        }
     })
 }

function getLedgersListAll(type=''){
    /*var firm_id = $('input[name=filter_admin_comp]').val();
    var acc_id = $('select[name=filter_acc_name]').val();*/
    $.ajax({
        url:base_url +'/general/getLedgersListAll',
        type:'post',
        dataType:'json',
        data:{type : type},
        success:function(j){
            if(type == 'bank' || type == 'cash' || type == 'contra'){
                $(document).find("#add_voucher [name=invoice_ledger]:first").each(function(){
                    $(this).html(j.option);
                var val = $('[name=invoice_ledger]').find('option:selected').attr('closing_balance');
                $('[name=invoice_ledger]').parent('.form-group:first').find('.closing_span').text(val);

                });
                $(document).find("#add_voucher [name=invoice_ledger]:last").each(function(){
                    $(this).html(j.option1);
                });
                $(document).find('.voucher_option1').html(j.option);
                $(document).find('.voucher_option2').html(j.option1);
            }else{
                $(document).find("#add_voucher [name=invoice_ledger]").each(function(){
                    $(this).html(j.option);
                });
                $(document).find('.voucher_option1').html(j.option);
                $(document).find('.voucher_option2').html(j.option);
            }
            //console.log(type + 'gdfgfdgdf');
            /*$(document).find("#add_voucher [name=invoice_ledger]:first").each(function(){
                $(this).html(j);
                $('#invoice_ledger option:eq(1)').attr('selected', 'selected');
            });*/
        }
    })
}

function AppendOption(){
    var voucher_option1 = $(document).find('.voucher_option1').html();
    $(document).find("#add_voucher [name=invoice_ledger]:first").each(function(){
        $(this).html(voucher_option1);
    });

    var voucher_option2 = $(document).find('.voucher_option2').html();
    $(document).find("#add_voucher [name=invoice_ledger]:last").each(function(){
        $(this).html(voucher_option2);
    });
}