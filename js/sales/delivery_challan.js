var sales_invoice_ary = {}; 
$('#customer').change(function () {
    if ($('#customer').val() != "") {
        $('#shipping_address_div').show();
        // $('#modal_party_name').val($('#customer').text());
        var party_id = $('#customer').val();
        var invoice_type= $('#customer option:selected').attr('type');
        $('#modal_party_id').val(party_id);
        $('#modal_party_type').val('customer');
        $.ajax({
            url: base_url + 'delivery_challan/get_sales_invoice_number',
            type: 'POST',
            dataType: "json",
            data: { customer_id: $('#customer').val(),invoice_type:invoice_type},
            success: function (data) {
                sales_invoice_ary = {};
                sales = data.sales_invoice;
                sales_credit = data.sales_credit_invoice;
                sales_debit = data.sales_debit_invoice;
                purchase_return = data.purchase_return;
                var opt = '';
                $('#delivery_reference').html('');
                opt += '<option value="">Select</option>';
                if(sales.length > 0){
                    $.each(sales,function(k,v){
                        sales_invoice_ary['sales_'+v.sales_id] = v;
                        opt += '<option value="' + v.sales_id + '" type="sales" en="'+v.en_id+'">' + v.sales_invoice_number + '</option>';
                    });
                }

                if(sales_credit.length > 0){
                    $.each(sales_credit,function(k,v){
                        sales_invoice_ary['sales_credit_note_'+v.sales_credit_note_id] = v;
                        opt += '<option value="' + v.sales_credit_note_id + '" type="sales_credit_note" en="'+v.en_id+'">' + v.sales_credit_note_invoice_number + '</option>';
                    });
                }

                if(sales_debit.length > 0){
                    $.each(sales_debit,function(k,v){
                        sales_invoice_ary['sales_debit_note_'+v.sales_debit_note_id] = v;
                        opt += '<option value="' + v.sales_debit_note_id + '" type="sales_debit_note" en="'+v.en_id+'">' + v.sales_debit_note_invoice_number + '</option>';
                    });
                }

                if(purchase_return.length > 0){
                    $.each(purchase_return,function(k,v){
                        sales_invoice_ary['purchase_return_'+v.purchase_return_id] = v;
                        opt += '<option value="' + v.purchase_return_id + '" type="purchase_return" en="'+v.en_id+'">' + v.purchase_return_invoice_number + '</option>';
                    });
                }

                $('#delivery_reference').html(opt);
            }
        });

    } else {
        $('#shipping_address_div').hide();
        $('#modal_party_id').val('');
        $('#modal_party_type').val('');
        $('#shipping_address').html('<option value="">Select</option>');
    }
});

$('#delivery_reference').on('change', function () {
    var id = $(this).val();
    var type = $(this).find('option:selected').attr('type');
    var en = $(this).find('option:selected').attr('en');
   
    if (id != "") {
        var invoice_date = $('#invoice_date').val();
        var invoice_number = $('#invoice_number').val();
        $('input[name=reference_type]').val(type);
        $('input[name=reference_id]').val(id);
        $.ajax({
            url: base_url + type +'/view/'+en,
            type: 'POST',
            data: { id: id,is_only_view:'1',invoice_date:invoice_date,invoice_number:invoice_number},
            success: function (result) {
                $('#view_html').html(result);
                $("#sales_credit_note_table_body").html('');
            }
        });
    }
})
$('#delivery_challan_submit').on('click',function(){
    var invoice_number = $('#invoice_number').val();
    if ($('#invoice_number').val() == "") {
        $('#err_invoice_number').text("Please Enter Invoice Number.");
        $('#invoice_number').focus();
        return !1
    } else {
        $('#err_invoice_number').text(" ");
    }

    if ($('#invoice_date').val() == "" || $('#invoice_date').attr('valid') == '0') {
        $('#err_date').text("Please Select valid Date.");
        $('#invoice_date').focus();
        return !1
    }
    if ($('#customer').val() == "") {
        $('#err_customer').text("Please Select the Customer.");
        $('#customer').focus();
        return !1
    }
    if ($('#delivery_reference').val() == "") {
        $('#err_delivery_reference').text("Please Select the reference number.");
        $('#delivery_reference').focus();
        return !1
    }

})