$(document).ready(function(){
	$(document).on('blur','[name=product_mrp]',function(){
        var gst_tax = parseFloat($('[name=gst_tax_product] option:selected').attr('per'));
        var mrp = $(this).val();
        
        if(mrp > 0 && (gst_tax == '' || typeof gst_tax == 'undefined' || gst_tax <= 0 || isNaN(gst_tax))){
        	
            if(mrp > 999){
                var gst_id = $('#gst_tax_product').find('option[per=18]').attr('value');
                $('#gst_tax_product').val(gst_id).trigger('change');
            }else{
                var gst_id = $('#gst_tax_product').find('option[per=5]').attr('value');
                $('#gst_tax_product').val(gst_id).trigger('change');
            }
        }
        calculateBasicPrice();
    })

    $(document).on('change','#markdown_discount,[name=margin_discount],[name=gst_tax_product]',function(){
        calculateBasicPrice();
    })
})

function calculateBasicPrice(){
    var product_mrp = $('[name=product_mrp]').val();
    if(product_mrp != '' && product_mrp > 0){
        var markdown_disc = parseFloat($('#markdown_discount option:selected').attr('dic'));
        var selling_price = product_mrp;
        if(markdown_disc != '' && typeof markdown_disc != 'undefined' && markdown_disc > 0){
            $('[name=product_discount_value]').val(markdown_disc);
            var markdwn_amnt = (product_mrp * markdown_disc) / 100;
            selling_price = product_mrp - markdwn_amnt;
        }else{
            $('[name=product_discount_value]').val(0);
        }

        var gst_tax = parseFloat($('[name=gst_tax_product] option:selected').attr('per'));
        var gst_amnt= 0;
        if(gst_tax != '' && typeof gst_tax != 'undefined' && gst_tax > 0){
            gst_amnt = (selling_price * gst_tax) / (100 + gst_tax);
        }
        
        var marginal_disc = parseFloat($('#margin_discount option:selected').attr('dic'));
        var marginal_amnt = 0;
        if(marginal_disc != '' && typeof marginal_disc != 'undefined' && marginal_disc > 0){
            $('[name=margin_discount_value]').val(marginal_disc);
            marginal_amnt = (selling_price * marginal_disc) / 100;
        }else{
            $('[name=margin_discount_value]').val(0);
        }
        
        var basic_price = selling_price - gst_amnt - marginal_amnt;
        $('[name=product_selling_price]').val(precise_amount(selling_price));
        $('[name=product_basic_price]').val(precise_amount(basic_price));
    }
}