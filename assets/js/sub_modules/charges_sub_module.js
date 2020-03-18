$(document).ready(function () {
    // var tot_other_amount=$('#total_other_amount').val();
    // if(tot_other_amount>0)
    // {
    // calculateOtherAmount();
    if ( parseFloat($('#total_freight_charge').val()) == parseFloat(0) || $('#total_freight_charge').val() == "")
    {
        $('#freight_charge_tr').hide();
    }
    if (parseFloat($('#total_insurance_charge').val()) == parseFloat(0) || $('#total_insurance_charge').val() == "")
    {
        $('#insurance_charge_tr').hide();
    }
    if (parseFloat($('#total_packing_charge').val()) == parseFloat(0) || $('#total_packing_charge').val() == "")
    {
        $('#packing_charge_tr').hide();
    }
    if (parseFloat($('#total_incidental_charge').val()) == parseFloat(0) || $('#total_incidental_charge').val() == "")
    {
        $('#incidental_charge_tr').hide();
    }
    if (parseFloat($('#total_other_inclusive_charge').val()) == parseFloat(0) || $('#total_other_inclusive_charge').val() == "")
    {
        $('#other_inclusive_charge_tr').hide();
    }
    if (parseFloat($('#total_other_exclusive_charge').val()) == parseFloat(0) || $('#total_other_exclusive_charge').val() == "")
    {
        $('#other_exclusive_charge_tr').hide();
    }
    
    $("#type_of_supply,#freight_charge_amount,#freight_charge_tax_select,#insurance_charge_amount,#insurance_charge_tax_select,#packing_charge_amount,#packing_charge_tax_select,#incidental_charge_amount,#incidental_charge_tax_select,#inclusion_other_charge_amount,#inclusion_other_charge_tax_select,#exclusion_other_charge_amount,#exclusion_other_charge_tax_select").on("blur change", function (event){
        calculateOtherAmount();
    });
});
function calculateOtherAmount()
{
    var freight_charge_amount = +parseFloat($('#freight_charge_amount').val());
    var freight_charge_tax_select = $('#freight_charge_tax_select').val();
    var insurance_charge_amount = +parseFloat($('#insurance_charge_amount').val());
    var insurance_charge_tax_select = $('#insurance_charge_tax_select').val();
    var packing_charge_amount = +parseFloat($('#packing_charge_amount').val());
    var packing_charge_tax_select = $('#packing_charge_tax_select').val();
    var incidental_charge_amount = +parseFloat($('#incidental_charge_amount').val());
    var incidental_charge_tax_select = $('#incidental_charge_tax_select').val();
    var inclusion_other_charge_amount = +parseFloat($('#inclusion_other_charge_amount').val());
    var inclusion_other_charge_tax_select = $('#inclusion_other_charge_tax_select').val();
    var exclusion_other_charge_amount = +parseFloat($('#exclusion_other_charge_amount').val());
    var exclusion_other_charge_tax_select = $('#exclusion_other_charge_tax_select').val();
    if(freight_charge_tax_select == '' || freight_charge_tax_select == null || typeof freight_charge_tax_select == undefined)
        freight_charge_tax_select = '';
    var freight_charge_tax_split = freight_charge_tax_select.toString().split("-");
    var freight_charge_tax_id = +freight_charge_tax_split[0];
    var freight_charge_tax_percentage = +parseFloat(freight_charge_tax_split[1]);
    if(insurance_charge_tax_select == '' || insurance_charge_tax_select == null || typeof insurance_charge_tax_select == undefined)
        insurance_charge_tax_select = '';
    var insurance_charge_tax_split = insurance_charge_tax_select.toString().split("-");
    var insurance_charge_tax_id = +insurance_charge_tax_split[0];
    var insurance_charge_tax_percentage = +parseFloat(insurance_charge_tax_split[1]);
    if(packing_charge_tax_select == '' || packing_charge_tax_select == null || typeof packing_charge_tax_select == undefined)
        packing_charge_tax_select = '';
    var packing_charge_tax_split = packing_charge_tax_select.toString().split("-");
    var packing_charge_tax_id = +packing_charge_tax_split[0];
    var packing_charge_tax_percentage = +parseFloat(packing_charge_tax_split[1]);
    if(incidental_charge_tax_select == '' || incidental_charge_tax_select == null || typeof incidental_charge_tax_select == undefined)
        incidental_charge_tax_select = '';
    var incidental_charge_tax_split = incidental_charge_tax_select.toString().split("-");
    var incidental_charge_tax_id = +incidental_charge_tax_split[0];
    var incidental_charge_tax_percentage = +parseFloat(incidental_charge_tax_split[1]);
    if(inclusion_other_charge_tax_select == '' || inclusion_other_charge_tax_select == null || typeof inclusion_other_charge_tax_select == undefined)
        inclusion_other_charge_tax_select = '';
    var inclusion_other_charge_tax_split = inclusion_other_charge_tax_select.toString().split("-");
    var inclusion_other_charge_tax_id = +inclusion_other_charge_tax_split[0];
    var inclusion_other_charge_tax_percentage = +parseFloat(inclusion_other_charge_tax_split[1]);
    if(exclusion_other_charge_tax_select == '' || exclusion_other_charge_tax_select == null || typeof exclusion_other_charge_tax_select == undefined)
        exclusion_other_charge_tax_select = '';
    var exclusion_other_charge_tax_split = exclusion_other_charge_tax_select.toString().split("-");
    var exclusion_other_charge_tax_id = +exclusion_other_charge_tax_split[0];
    var exclusion_other_charge_tax_percentage = +parseFloat(exclusion_other_charge_tax_split[1]);
    var type_of_supply = $('#type_of_supply').val();
    if(type_of_supply=="export_without_payment" || type_of_supply=="import"){
        freight_charge_tax_id = 0;
        freight_charge_tax_percentage = 0;
        insurance_charge_tax_id = 0;
        insurance_charge_tax_percentage = 0;
        packing_charge_tax_id = 0;
        packing_charge_tax_percentage = 0;
        incidental_charge_tax_id = 0;
        incidental_charge_tax_percentage = 0;
        inclusion_other_charge_tax_id = 0;
        inclusion_other_charge_tax_percentage = 0;
        exclusion_other_charge_tax_id = 0;
        exclusion_other_charge_tax_percentage = 0  ;
    }
    freight_charge_amount= (freight_charge_amount)?freight_charge_amount:0;
    insurance_charge_amount= (insurance_charge_amount)?insurance_charge_amount:0;
    packing_charge_amount= (packing_charge_amount)?packing_charge_amount:0;
    incidental_charge_amount= (incidental_charge_amount)?incidental_charge_amount:0;
    inclusion_other_charge_amount= (inclusion_other_charge_amount)?inclusion_other_charge_amount:0;
    exclusion_other_charge_amount= (exclusion_other_charge_amount)?exclusion_other_charge_amount:0;
    
    freight_charge_tax_percentage=(freight_charge_tax_percentage)?freight_charge_tax_percentage:0;
    insurance_charge_tax_percentage=(insurance_charge_tax_percentage)?insurance_charge_tax_percentage:0;
    packing_charge_tax_percentage=(packing_charge_tax_percentage)?packing_charge_tax_percentage:0;
    incidental_charge_tax_percentage=(incidental_charge_tax_percentage)?incidental_charge_tax_percentage:0;
    inclusion_other_charge_tax_percentage=(inclusion_other_charge_tax_percentage)?inclusion_other_charge_tax_percentage:0;
    exclusion_other_charge_tax_percentage=(exclusion_other_charge_tax_percentage)?exclusion_other_charge_tax_percentage:0;
    var freight_charge_tax_amount = (freight_charge_amount * freight_charge_tax_percentage / 100);
    var insurance_charge_tax_amount = (insurance_charge_amount * insurance_charge_tax_percentage / 100);
    var packing_charge_tax_amount = (packing_charge_amount * packing_charge_tax_percentage / 100);
    var incidental_charge_tax_amount = (incidental_charge_amount * incidental_charge_tax_percentage / 100);
    var inclusion_other_charge_tax_amount = (inclusion_other_charge_amount * inclusion_other_charge_tax_percentage / 100);
    var exclusion_other_charge_tax_amount = (exclusion_other_charge_amount * exclusion_other_charge_tax_percentage / 100);
    var total_freight_charge = (freight_charge_amount);
    //var total_freight_charge = freight_charge_amount + freight_charge_tax_amount;
    var total_insurance_charge = (insurance_charge_amount); // + insurance_charge_tax_amount
    var total_packing_charge = (packing_charge_amount); // + packing_charge_tax_amount
    var total_incidental_charge = (incidental_charge_amount); // + incidental_charge_tax_amount
    var total_inclusion_other_charge = (inclusion_other_charge_amount); // + inclusion_other_charge_tax_amount
    var total_exclusion_other_charge = (exclusion_other_charge_amount); // + exclusion_other_charge_tax_amount
    var total_other_amount = total_freight_charge + total_insurance_charge + total_packing_charge + total_incidental_charge + total_inclusion_other_charge - total_exclusion_other_charge;
    var total_other_taxable_amount = freight_charge_tax_amount + insurance_charge_tax_amount + packing_charge_tax_amount + incidental_charge_tax_amount + inclusion_other_charge_tax_amount - exclusion_other_charge_tax_amount;
    total_other_amount = total_other_amount + total_other_taxable_amount;
    console.log(total_other_taxable_amount, freight_charge_tax_amount);
    $('#total_other_amount').val(precise_amount(total_other_amount));
    $('#total_other_taxable_amount').val(precise_amount(total_other_taxable_amount));
    $('#freight_charge_tax_amount').val(precise_amount(freight_charge_tax_amount));
    $('#insurance_charge_tax_amount').val(precise_amount(insurance_charge_tax_amount));
    $('#packing_charge_tax_amount').val(precise_amount(packing_charge_tax_amount));
    $('#incidental_charge_tax_amount').val(precise_amount(incidental_charge_tax_amount));
    $('#inclusion_other_charge_tax_amount').val(precise_amount(inclusion_other_charge_tax_amount));
    $('#exclusion_other_charge_tax_amount').val(precise_amount(exclusion_other_charge_tax_amount));
    $('#freight_charge_tax_id').val(freight_charge_tax_id);
    $('#insurance_charge_tax_id').val(insurance_charge_tax_id);
    $('#packing_charge_tax_id').val(packing_charge_tax_id);
    $('#incidental_charge_tax_id').val(incidental_charge_tax_id);
    $('#inclusion_other_charge_tax_id').val(inclusion_other_charge_tax_id);
    $('#exclusion_other_charge_tax_id').val(exclusion_other_charge_tax_id);
    $('#freight_charge_tax_percentage').val(freight_charge_tax_percentage);
    $('#insurance_charge_tax_percentage').val(insurance_charge_tax_percentage);
    $('#packing_charge_tax_percentage').val(packing_charge_tax_percentage);
    $('#incidental_charge_tax_percentage').val(incidental_charge_tax_percentage);
    $('#inclusion_other_charge_tax_percentage').val(inclusion_other_charge_tax_percentage);
    $('#exclusion_other_charge_tax_percentage').val(exclusion_other_charge_tax_percentage);
    $('#err_freight_charge_tax_amount').html(precise_amount(freight_charge_tax_amount));
    $('#err_insurance_charge_tax_amount').html(precise_amount(insurance_charge_tax_amount));
    $('#err_packing_charge_tax_amount').html(precise_amount(packing_charge_tax_amount));
    $('#err_incidental_charge_tax_amount').html(precise_amount(incidental_charge_tax_amount));
    $('#err_inclusion_other_charge_tax_amount').html(precise_amount(inclusion_other_charge_tax_amount));
    $('#err_exclusion_other_charge_tax_amount').html(precise_amount(exclusion_other_charge_tax_amount));
    $('#freight_charge').html(precise_amount(total_freight_charge));
    $('#insurance_charge').html(precise_amount(total_insurance_charge));
    $('#packing_charge').html(precise_amount(total_packing_charge));
    $('#incidental_charge').html(precise_amount(total_incidental_charge));
    $('#other_inclusive_charge').html(precise_amount(total_inclusion_other_charge));
    $('#other_exclusive_charge').html(precise_amount(total_exclusion_other_charge));
    $('#total_freight_charge').val(precise_amount(total_freight_charge));
    if(total_freight_charge > 0){
        $('#freight_charge_tr').show();
    }else{
        $('#freight_charge_tr').hide();
    }
    $('#total_insurance_charge').val(precise_amount(total_insurance_charge));
    if(total_insurance_charge > 0){
        $('#insurance_charge_tr').show();
    }else{
        $('#insurance_charge_tr').hide();
    }
    $('#total_packing_charge').val(precise_amount(total_packing_charge));
    if(total_packing_charge > 0){
        $('#packing_charge_tr').show();
    }else{
        $('#packing_charge_tr').hide();
    }
    $('#total_incidental_charge').val(precise_amount(total_incidental_charge));
    if(total_incidental_charge > 0){
        $('#incidental_charge_tr').show();
    }else{
        $('#incidental_charge_tr').hide();
    }
    $('#total_other_inclusive_charge').val(precise_amount(total_inclusion_other_charge));
    if(total_inclusion_other_charge > 0){
        $('#other_inclusive_charge_tr').show();
    }else{
        $('#other_inclusive_charge_tr').hide();
    }
    $('#total_other_exclusive_charge').val(precise_amount(total_exclusion_other_charge));
    if(total_exclusion_other_charge > 0){
        $('#other_exclusive_charge_tr').show();
    }else{
        $('#other_exclusive_charge_tr').hide();
    }
    if (parseFloat($('#total_sub_total').val()) > 0 && $('#total_sub_total').val() != "")
    {
        if ($('#grand_total_check').val() == 1)
        {
            calculateGrandTotal_check();
        } else
        {
            //calculateGrandTotal();
            reCalculateTable();
        }
    }
}  
