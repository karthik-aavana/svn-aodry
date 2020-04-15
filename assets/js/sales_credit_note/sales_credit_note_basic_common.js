var mapping = {};

if (typeof count_data === 'undefined' || count_data === null) {
    var count_data = 0;
}
var table_index = count_data;
var branch_country_id = $('#branch_country_id').val();
var branch_state_id = $('#branch_state_id').val();
var flag2 = 0;
$(document).ready(function () {

    $(document).keyup(function (event) {

        if ((event.which == 13) && ($(event.target)[0] != $("textarea")[0])) {
            event.preventDefault();

            return false;
        }
    });

    $('#input_sales_credit_note_code').on('change focus', function () {
        $('#input_sales_credit_note_code').trigger('click');
    });

    $(document).on('change','[name=item_taxable_value],input[name=item_quantity]',function(){
        var val = $(this).closest('tr').find('[name=item_taxable_value]').val();
       
        var qnt = $(this).closest('tr').find('[name=item_quantity]').val();
        if(typeof qnt == 'undefined' || qnt == ''){
          qnt = 0;  
          $(this).closest('tr').find('[name=item_quantity]').val('0');
        } 
        var item_price = (val/qnt);
        $(this).closest('tr').find('[name=item_sub_total]').val(precise_amount(val));
        $(this).closest('tr').find('[name=item_price]').val(precise_amount(item_price)).trigger('change');
        
    })

    if ($('#customer').val() != "") {
        $('#shipping_address_div').show();
    } else {
        $('#shipping_address_div').hide();
    }

    $("#freight_charge_amount,#insurance_charge_amount,#packing_charge_amount,#incidental_charge_amount,#inclusion_other_charge_amount,#exclusion_other_charge_amount").on("blur change", function (event){
        setDefault($(this));
    });

    $('#sales_credit_note_table_body').on('change blur','input[name=item_quantity],input[name=item_taxable_value],input[name=item_grand_total]',function(){
        setDefault($(this));
    })

    $("#sales_invoice_number").change(function (event) {
        var sales_id = $('#sales_invoice_number').val();
        k = 'sales_'+sales_id;
        var taxes = sales_invoice_ary[k];
        var sales_date = taxes.sales_date;
        var sales_billing_state_id = taxes.sales_billing_state_id;
        var sales_billing_country_id = taxes.sales_billing_country_id;
        var nature_of_supply = taxes.sales_nature_of_supply;
        var type_of_supply = taxes.sales_type_of_supply;
        var currency_id = taxes.currency_id;
        var gstPayable = taxes.sales_gst_payable;
        var shipping_address_id_val = taxes.shipping_address_id;
        var billing_address_id = taxes.billing_address_id;
        var ship_to_customer = taxes.ship_to_customer_id;
        
        var freight_val = '';
        $('#billing_address_id').val(billing_address_id);
        
        $('#shipping_address').val(shipping_address_id_val);
        
        $('#ship_to').val(ship_to_customer).change();
        /*$('#invoice_date').val(sales_date);*///.attr('disabled',true)
        /*$('#invoice_date_old').val(sales_date);*/
        $('[name=billing_country]').val(sales_billing_country_id).change();
        if(sales_billing_state_id == 0){
            $('[name=billing_state]').val(0).change();
        }else{
            $('[name=billing_state]').val(sales_billing_state_id).change();
        }        
        $('[name=billing_state]').addClass('disable_input');

        $('[name=nature_of_supply]').val(nature_of_supply).trigger('change').addClass('disable_input');
        $('[name=type_of_supply]').val(type_of_supply).addClass('disable_input');
        $('[name=currency_id]').val(currency_id).addClass('disable_input');       
        $('[name=gst_payable][value='+gstPayable+']').prop('checked',true).trigger('change');
       
        if(parseFloat(taxes.freight_charge_amount) > 0)
            freight_val =  $('#freight_charge_tax_select option[id='+taxes.freight_charge_tax_id+']').val();
           /* freight_val = taxes.freight_charge_tax_id+'-'+precise_amount(taxes.freight_charge_tax_percentage);*/
            
        $('#freight_charge_tax_select').val(freight_val).trigger('change');
        $('[name=freight_charge_amount]').val(precise_amount(taxes.freight_charge_amount)).attr('limit',taxes.freight_charge_amount);
        var incide_val = '';
        if(parseFloat(taxes.incidental_charge_amount) > 0){
            /*incide_val =  taxes.incidental_charge_tax_id+'-'+precise_amount(taxes.incidental_charge_tax_percentage);*/
            incide_val =  $('#incidental_charge_tax_select option[id='+taxes.incidental_charge_tax_id+']').val();
        }
        $('#incidental_charge_tax_select').val(incide_val).trigger('change');
        $('[name=incidental_charge_amount]').val(precise_amount(taxes.incidental_charge_amount)).attr('limit',taxes.incidental_charge_amount);
        
        var inclusion_other_val = '';
        if(parseFloat(taxes.inclusion_other_charge_amount) > 0){
            //inclusion_other_val =  taxes.inclusion_other_charge_tax_id+'-'+precise_amount(taxes.inclusion_other_charge_tax_percentage);
            inclusion_other_val =  $('#inclusion_other_charge_tax_select option[id='+taxes.inclusion_other_charge_tax_id+']').val();
        }
        $('#inclusion_other_charge_tax_select').val(inclusion_other_val).trigger('change');
        $('[name=inclusion_other_charge_amount]').val(precise_amount(taxes.inclusion_other_charge_amount)).attr('limit',taxes.inclusion_other_charge_amount);
        var insurance_val = '';
        if( parseFloat(taxes.insurance_charge_amount) > 0){
            insurance_val = $('#insurance_charge_tax_select option[id='+taxes.insurance_charge_tax_id+']').val();
        }
        $('[name=insurance_charge_amount]').val(precise_amount(taxes.insurance_charge_amount)).attr('limit',taxes.insurance_charge_amount);
        $('#insurance_charge_tax_select').val(insurance_val).trigger('change');
        var exclusion_other_val = '';
        if(parseFloat(taxes.exclusion_other_charge_amount) > 0){
            /*exclusion_other_val =  taxes.exclusion_other_charge_tax_id+'-'+precise_amount(taxes.exclusion_other_charge_tax_percentage);*/
            exclusion_other_val =  $('#exclusion_other_charge_tax_select option[id='+taxes.exclusion_other_charge_tax_id+']').val();
        }
        $('#exclusion_other_charge_tax_select').val(exclusion_other_val).trigger('change');
        $('[name=exclusion_other_charge_amount]').val(precise_amount(taxes.exclusion_other_charge_amount)).attr('limit',taxes.exclusion_other_charge_amount);
        
        var packing_val = '';
        if(parseFloat(taxes.packing_charge_amount) > 0){
            /*packing_val =  taxes.packing_charge_tax_id+'-'+precise_amount(taxes.packing_charge_tax_percentage);*/
            packing_val =  $('#packing_charge_tax_select option[id='+taxes.packing_charge_tax_id+']').val();
        }
        $('#packing_charge_tax_select').val(packing_val).trigger('change');
        $('[name=packing_charge_amount]').val(precise_amount(taxes.packing_charge_amount)).attr('limit',taxes.packing_charge_amount);
        
        $.ajax({
                url: base_url + "general/get_shipping_address_place_edit",
                dataType: "JSON",
                method: "POST",
                data: {
                    party_id: $("#customer").val(),
                    party_type: "customer"
                },
                success: function (result) {
                    var data = result["shipping_address_data"];
                    /*$("#shipping_address").html("");
                    $("#shipping_address").append('<option value="">Select</option>');
                    for (i = 0; i < data.length; i++) {
                        var shipping_address = data[i].shipping_address;
                        var shipping_address_new = shipping_address.replace(
                            /\n\r|\\r|\\n|\r|\n|\r\n|\\n\\r|\\r\\n/g,
                            "<br/>"
                        );
                        
                        if(data[i].shipping_address_id == shipping_address_id_val){
                            $("#shipping_address").append(
                                '<option value="' +
                                data[i].shipping_address_id +
                                '" selected>' +
                                shipping_address_new +
                                "</option>"
                            );
                        }else{
                            $("#shipping_address").append(
                                '<option value="' +
                                data[i].shipping_address_id +
                                '">' +
                                shipping_address_new +
                                "</option>"
                            );  
                        }
                    }

                    $("#shipping_address").select2({
                        containerCssClass: "wrap"
                    });
                    
                    $("#shipping_address").change();*/
                }
            });
       
        if (sales_id != "") {
            $.ajax({
                url: base_url + 'sales_credit_note/get_sales_item',
                type: 'POST',
                data: { sales_id: sales_id},
                success: function (result) {

                    table_index = 0;
                    sales_credit_note_data = new Array();
                    $("#sales_credit_note_table_body").html('');
                    var data = $.parseJSON(result);

                    /*$('#currency_id').html('');
                    var option = '<option value="' + data['currency'][0].currency_id + '">' + data['currency'][0].currency_name + ' - '+ data['currency'][0].country_shortname + '</option>';
                    $('#currency_id).html(option);*/
                    $('#currency_id').val(data['currency'][0].currency_id).trigger('change');

                    add_row_loop(data);
                    call_css();
                    suggestion_box();
                    disable_select_box();
                    $('#insurance_charge_tax_select').trigger('change');
                    /*sales_credit_note_data = new Array();*/
                    var table_data = JSON.stringify(sales_credit_note_data);
                    $('#table_data').val(table_data);
                }
            });
        } else {
            table_index = 0;
            sales_credit_note_data = new Array();
            $("#sales_credit_note_table_body").html('');
            suggestion_box();
        }
    });

    function setDefault(ths){
        var limit = ths.attr('limit');
        if(limit != 'nochange'){
            if(limit != '' && typeof limit != 'undefined'){// && limit != '0'
                if(parseFloat(ths.val()) > parseFloat(limit)){
                    // ths.val(precise_amount(limit)).trigger('change');
                    ths.val(precise_amount(limit)).trigger('change');
                }
            }
            if(isNaN(parseFloat(ths.val()))){
                if(limit != '' && typeof limit != 'undefined' && limit != '0'){
                    /*ths.val(precise_amount(limit)).trigger('change');*/
                    ths.val(precise_amount(limit)).trigger('change');
                }else{
                    ths.val(0).trigger('change');
                    /*ths.val(1).trigger('change');*/
                } 
            }
        }
        if(ths.attr('name') == 'item_quantity'){
            ths.val(parseInt(ths.val()));
        }
    }

    function suggestion_box() {

        if ($('#sales_invoice_number').val() != "") {
            $('#input_sales_credit_note_code').prop('disabled', false);
            $('.search_sales_credit_note_code').show();
            $('#err_sales_credit_note_code').text('');
        } else {
            $('.search_sales_credit_note_code').hide();
            $('#input_sales_credit_note_code').prop('disabled', true);
            $('#err_sales_credit_note_code').text('Please select the customer to do sales credit note.');
            $('#err_product').text('');

            table_index = 0;
            sales_credit_note_data = new Array();
            $("#sales_credit_note_table_body").html('');
        }
        $('#myForm input').change();
    }

    var sales_invoice_ary = {}; 
    $('#customer').change(function () {
        if ($('#customer').val() != "") {
            $('#shipping_address_div').show();
            // $('#modal_party_name').val($('#customer').text());
            var party_id = $('#customer').val();
            $('#modal_party_id').val(party_id);
            $('#modal_party_type').val('customer');
            $.ajax({
                url: base_url + 'sales_credit_note/get_sales_invoice_number',
                type: 'POST',
                dataType: "json",
                data: {
                    customer_id: $('#customer').val()
                },
                success: function (data) {
                    sales_invoice_ary = {};
                    $.each(data,function(k,v){
                        sales_invoice_ary['sales_'+v.sales_id] = v;
                    })
                    
                    $('#sales_invoice_number').html('');
                    $('#sales_invoice_number').append('<option value="">Select</option>');
                    for (var i = 0; i < data.length; i++) {
                        $('#sales_invoice_number').append('<option value="' + data[i].sales_id + '">' + data[i].sales_invoice_number + '</option>');
                    }

                    $.ajax({
                        url: base_url + 'general/get_shipping_address',
                        dataType: 'JSON',
                        method: 'POST',
                        data: {
                            'party_id': $('#customer').val(),
                            'party_type': 'customer'
                        },
                        success: function (result) {
                            var data = result['shipping_address_data'];
                            /*$('#shipping_address').html('');
                            $('#shipping_address').append('<option value="">Select</option>');
                            for (i = 0; i < data.length; i++) {
                                var shipping_address = data[i].shipping_address;

                                var shipping_address_new = shipping_address.replace(/\n\r|\\r|\\n|\r|\n|\r\n|\\n\\r|\\r\\n/g, '<br/>')

                                $('#shipping_address').append('<option value="' + data[i].shipping_address_id + '">' + shipping_address_new + '</option>');

                            }

                            $("#shipping_address").select2({
                                containerCssClass: "wrap"
                            });
                            $("#shipping_address").change();*/
                            $('#ship_to').val("").change();
                            if(result.customer_detail){
                                var is_utgst = result.customer_detail.is_utgst;
                                $('#is_utgst').val(is_utgst);
                                var out_of_india = result.customer_detail.out_of_india;
                                var opt = '';
                                if(out_of_india){
                                    $('#billing_state').val('0').change();
                                }else{
                                    $('#billing_state').val(result.customer_detail.customer_state_id).change();
                                }
                               /* $('#billing_country').val(result.customer_detail.customer_country_id).change();*/
                                ChangeTypeOfSupply();
                            }
                        }
                    });

                    suggestion_box();
                }
            });


        } else {
            $('#shipping_address_div').hide();
            $('#modal_party_id').val('');
            $('#modal_party_type').val('');
            $('#shipping_address').html('<option value="">Select</option>');
        }
    });

    //date change
    $('#invoice_date').on('changeDate', function () {
        var selected = $(this).val();
        var module_id = $("#module_id").val();
        var date_old = $("#invoice_date_old").val();
        var privilege = $("#privilege").val();
        var section_area = $("#section_area").val();
        var old_res = date_old.toString().split("-");
        var new_res = selected.toString().split("-");
        if (old_res[1] != new_res[1]) {
            $.ajax({
                url: base_url + 'general/generate_date_reference',
                type: 'POST',
                data: {
                    date: selected,
                    privilege: privilege,
                    module_id: module_id,
                    section_area: section_area
                },
                success: function (data) {

                    var parsedJson = $.parseJSON(data);
                    var type = parsedJson.reference_no;
                    $('#invoice_number').val(type)
                }
            })
        } else {
            var old_reference = $('#invoice_number_old').val();
            $('#invoice_number').val(old_reference)
        }
    });
    //billing country changes
    /*$("#billing_country").change(function (event) {
        var table_data = $('#table_data').val();
        var billing_country = $('#billing_country').val();
        if (branch_country_id != billing_country) {
            flag2 = 1;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="0">Out of Country</option>');

            $('#type_of_supply').html('');
            $('#type_of_supply').append('<option value="export_with_payment">Export (With Tax Payment)</option>');
            $('#type_of_supply').append('<option value="export_without_payment" selected>Export (Without Tax Payment)</option>');
        } else {
            flag2 = 2;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="">Select</option>');
            for (var i = 0; i < branch_state_list.length; i++) {
                $('#billing_state').append('<option value="' + branch_state_list[i].state_id + '">' + branch_state_list[i].state_name + '</option>');
            }

            $('#type_of_supply').html('');
            $('#type_of_supply').append('<option value="regular">Regular</option>');

        }
        $('#type_of_supply').change();

    });*/

    function ChangeTypeOfSupply(){
        var table_data = $("#table_data").val();
        var state = $('#billing_state').val();
        if(state == '0'){
            flag2 = 1;
           
            $("#type_of_supply").html("");
            $("#type_of_supply").append(
                '<option value="export_with_payment">Export (With Tax Payment)</option>'
            );
            $("#type_of_supply").append(
                '<option value="export_without_payment" selected>Export (Without Tax Payment)</option>'
            );

            $('[name=gst_payable][value=no]').prop('checked',true);
        } else {
            flag2 = 2;
            
            $("#type_of_supply").html("");
            $("#type_of_supply").append('<option value="regular">Regular</option>');
        }
        $("#type_of_supply").change();
    }

    $("#billing_state").change(function (event) {
        if($(this).val() == '0'){
           $('#billing_country').val('0').change(); 
        }
        ChangeTypeOfSupply();
    });

    $(document).on('change','[name=round_off_value]',function(){
        CalculateRoundOff();
    })

    $(document).on('change','[name=type_of_supply]',function(){
        var type_of_supply = $(this).val();
        if(type_of_supply == 'export_without_payment'){
            $('.gst_payable_div').hide();
        }else if(type_of_supply == 'export_with_payment'){
            $('.gst_payable_div').hide();
        }else{
            $('.gst_payable_div').show();
        }
        reCalculateTable();
    });
    // get items to table starts
    $('#input_sales_credit_note_code').autoComplete({
        minChars: 1,
        cache: false,
        source: function (term, suggest) {
            term = term.toLowerCase();
            if (common_settings_inventory_advanced == "") {
                var inventory_advanced = "no";
            } else {
                var inventory_advanced = common_settings_inventory_advanced;
            }
            var item_access = $('#nature_of_supply').val();
            if (item_access == "") {
                item_access = "product";
            }

            var isnum = /^\d+$/.test(term);
            $.ajax({
                url: base_url + "sales_credit_note/get_sales_credit_note_suggestions/" + term + "/" + inventory_advanced + "/" + item_access,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    var suggestions = [];
                    for (var i = 0; i < data.length; ++i) {
                        suggestions.push(data[i].item_code + ' ' + data[i].item_name + ' ' + data[i].product_batch);
                        var item_code = data[i].item_code;
                        var code = item_code.toString().split(' ');
                        mapping[code[0]] = data[i].item_id + '-' + data[i].item_type + '-' + settings_discount_visible + '-' + settings_tax_type;
                    }
                    //&& isnum == true
                    if (i == 1 && term.length > 7 ) {

                        $.ajax({
                            url: base_url + 'sales_credit_note/get_table_items/' + mapping[data[0].item_code],
                            type: "GET",
                            dataType: "JSON",
                            success: function (data1) {
                                add_row(data1);
                                call_css();
                                $('#input_sales_credit_note_code').val('')
                            }
                        });
                        suggest('');
                    } else {
                        suggest(suggestions);
                    }
                }
            })
        },
        onSelect: function (event, ui) {
            var code = ui.toString().split(' ');
            $.ajax({
                url: base_url + 'sales_credit_note/get_table_items/' + mapping[code[0]],
                type: "GET",
                dataType: "JSON",
                success: function (data) {

                    add_row(data);
                    call_css();
                    $('#input_sales_credit_note_code').val('')
                }
            })
        }
    });
    // get items to table ends

    //delete rows 
    $("#sales_credit_note_table_body").on("click", "a.deleteRow", function (event) {
        var newRow = $(this).closest("tr");
        deleteRow(newRow);
        $(this).closest("tr").remove();
        $("#sales_credit_note_table_body").find('input[name^="item_tax_id"]').each(function () {
            var row = $(this).closest("tr");
            //if(type_of_supply == 'export_without_payment') row.find('[name=item_tax]').val('').change();
            generateJson(row);
        });
        calculateGrandTotal();
    });

    // changes in rows
    $("#sales_credit_note_table_body").on("change blur", 'input[name^="item_price"],input[name^="item_description"],select[name="item_discount"],select[name="item_tax"]', function (event) {
        var newRow = $(this).closest("tr");
        calculateTable(newRow);
    });

    $("#sales_credit_note_table_body").on("change",'select[name="item_tax_cess"]',function (event) {
            var newRow = $(this).closest("tr");
            calculateTable(newRow);
        }
    );

    $("#sales_credit_note_table_body").on("change",'select[name="item_tds_percentage"]',function (event) {
            /**/
            var newRow = $(this).closest("tr");
            var item_tds_id = newRow.find('select[name^="item_tds_percentage"] option:selected').attr('tds_id');
            var item_tds_type = newRow.find('select[name^="item_tds_percentage"] option:selected').attr('type');
          
            newRow.find('input[name=item_tds_id]').val(item_tds_id);
            newRow.find('input[name=item_tds_type]').val(item_tds_type);
            calculateTable(newRow);
        }
    );

    //reverse calculations
    $("#sales_credit_note_table_body").on("change", 'input[name="item_grand_total"]', function (event) {

        row = $(this).closest("tr");
        var item_grand_total = +parseFloat(row.find('input[name="item_grand_total"]').val());
        var item_discount_amount = 0;
        var item_tax_amount = 0;
        var item_tds_amount = 0;
        var item_discount_percentage = 0;
        var item_tax_percentage = 0;
        var item_tds_percentage = 0;
        if (settings_discount_visible != "no") {
            item_discount_amount = +parseFloat(row.find('input[name^="item_discount_amount"]').val());
            item_discount_percentage = +parseFloat(row.find('input[name^="item_discount_percentage"]').val());
        }
        if (settings_tax_type != "no_tax") {
            item_tax_amount = +parseFloat(row.find('input[name="item_tax_amount"]').val());
            item_tax_cess_amount = +parseFloat(
                    row.find('input[name="item_tax_cess_amount"]').val()
                );
            item_tax_cess_percentage = +parseFloat(
                    row.find('input[name^="item_tax_cess_percentage"]').val()
                );
            item_tax_percentage = +parseFloat(row.find('input[name="item_tax_percentage"]').val());
        }
        if (settings_tds_visible != "no") {
            item_tds_amount = +parseFloat(row.find('input[name="item_tds_amount"]').val());
            item_tds_percentage = +parseFloat(row.find('[name="item_tds_percentage"]').val());
            var item_tds_type = row.find('input[name^="item_tds_type"]').val();
            item_tds_type = item_tds_type.toLowerCase();
            item_tds_type = item_tds_type.toUpperCase();
            if (item_tds_type != "TCS") {
                item_tds_amount = 0;
                item_tds_percentage = 0;
            }
        }

        var item_quantity = +row.find('input[name="item_quantity"]').val();
        if (item_quantity == 0) {
            item_quantity = 1;
        }
        var item_taxable_value = precise_amount((+item_grand_total * 100) / (100 + (+item_tds_percentage + +item_tax_percentage + +item_tax_cess_percentage)));

        var item_sub_total = (+item_taxable_value * 100) / (100 - +item_discount_percentage);
        var item_price = item_sub_total / item_quantity;
        row.find('input[name="item_price"]').val(precise_amount(item_price));
        row.find('input[name="item_taxable_value"]').val(precise_amount(item_taxable_value));
        calculateTable(row);
    });

    $("input[type=radio][name=round_off_key].minimal").on(
        "ifChanged",
        function () {
            var grand_total = $("#without_reound_off_grand_total").val();
            if(isNaN(grand_total) || grand_total == '' || typeof grand_total == 'undefined' || grand_total == NaN)
                grand_total = $("#total_grand_total").val();
            if (grand_total != "" && grand_total != 0) {
                var radioValue = $(
                    "input[type=radio][name='round_off_key']:checked"
                ).val();

                if (radioValue == "yes") {
                    var next_grand_total = round_off_next(grand_total);
                    var prev_grand_total = round_off_prev(grand_total);
                    var options = "";
                    if (next_grand_total != prev_grand_total) {
                        options +=
                            '<option val="' +
                            prev_grand_total +
                            '">' +
                            prev_grand_total +
                            "</option>";
                        options +=
                            '<option val="' +
                            next_grand_total +
                            '">' +
                            next_grand_total +
                            "</option>";
                    } else {
                        options +=
                            '<option val="' +
                            prev_grand_total +
                            '">' +
                            prev_grand_total +
                            "</option>";
                    }
                    $("#round_off_value").html(options);

                    $("#round_off_select").removeAttr("style");
                    
                } else {
                    $("#round_off_value").html('');
                    $("#round_off_select").attr("style", "display: none");
                }
            }
            CalculateRoundOff();
        }
    );

});

//function to add new row to datatable
function add_row_loop(data) {

    for (var i = 0; i < data.items.length; i++) {

        data.items[i].module_type = data.items[i].tds_module_type;

        var temp = [data.items[i]];
        temp.item_id = data.items[i].item_id;
        temp.item_type = data.items[i].item_type;
        temp.branch_country_id = data.branch_country_id;
        temp.branch_state_id = data.branch_state_id;
        temp.branch_id = data.branch_id;
        temp.discount = data.discount;
        temp.currency = data.currency;
        temp.tax = data.tax;
        add_row(temp, 'loop');
    }

}

function CalculateRoundOff(){
    var grand_total = parseFloat($("#without_reound_off_grand_total").val());
    
    if(isNaN(grand_total) || typeof grand_total == 'undefined' || grand_total == NaN)
        grand_total = $("#total_grand_total").val();
    var round_off_value = round_off_plus = round_off_minus = 0;
    if (grand_total != "" && grand_total != 0) {
        var radioValue = $("input[type=radio][name='round_off_key']:checked").val();
        if (radioValue == "yes") {
            round_off_value = parseFloat($('#round_off_value').val());
            if(grand_total < round_off_value){
                round_off_plus = round_off_value - grand_total;
                grand_total = parseFloat(grand_total) + parseFloat(round_off_plus);
            }else{
                round_off_minus = grand_total - round_off_value; 
                grand_total = parseFloat(grand_total) - parseFloat(round_off_minus);
            }
        } 
    }
    
    $('#round_off_plus').val(round_off_plus);
    $('#round_off_minus').val(round_off_minus);

    if(round_off_minus > 0){
        $('.round_off_minus_tr').show();
        $('#round_off_minus_charge').text(precise_amount(round_off_minus));
    }else{
        $('.round_off_minus_tr').hide();
    }

    if(round_off_plus > 0){
        $('.round_off_plus_tr').show();
        $('#round_off_plus_charge').text(precise_amount(round_off_plus));
    }else{
        $('.round_off_plus_tr').hide();
    }

    $("#totalGrandTotal").text(precise_amount(grand_total));
}

//function to add new row to datatable
function add_row(data, loop = "") {
    var flag = 0
    if (loop == "loop") {
        var quantity = 0;
        //var loop_edit = "no";
        var loop_edit = "yes";
    } else {
        var quantity = 1;
        var loop_edit = "yes";
    }

    var item_id = data.item_id;
    var item_type = data.item_type;
    var branch_country_id = data.branch_country_id;
    var branch_state_id = data.branch_state_id;
    var branch_id = data.branch_id;
    var sales_item = data[0];

    if (item_type == 'service') {
        var item_code = data[0].service_code;
        var item_name = data[0].service_name;
        var item_hsn_sac_code = data[0].service_hsn_sac_code;
        var item_details = data[0].service_details;
        var item_price = precise_amount(data[0].service_price);
        var item_tax_id = data[0].service_tax_id;
        var item_tax_percentage = precise_amount(data[0].service_tax_value);
        var item_tds_id = data[0].service_tds_id;
        var item_tds_percentage = precise_amount(data[0].service_tds_value);
       var item_tds_type = 'TDS';

    } else {
        var item_code = data[0].product_code;
        var item_name = data[0].product_name;
        var item_hsn_sac_code = data[0].product_hsn_sac_code;
        var item_details = data[0].product_details;
        var item_price = precise_amount(data[0].product_price);
        var item_tax_id = data[0].product_tax_id;
        var item_tax_percentage = precise_amount(data[0].product_tax_value);
        var item_tds_percentage = precise_amount(data[0].product_tds_value);
        var item_tds_type = 'TCS';
        var product_batch = data[0].product_batch;
    }
    
    var sales_item_discount_id = sales_item.sales_item_discount_id;
    var item_discount_percentage = sales_item.item_discount_percentage;
    var item_tds_id = sales_item.sales_item_tds_id;
    var item_tds_amount = sales_item.sales_item_tds_amount;
    var item_tds_percentage = sales_item.sales_item_tds_percentage;
    var item_price = sales_item.sales_item_unit_price;
    var item_description = sales_item.sales_item_description;
    var item_quantity = sales_item.sales_item_quantity;
    var item_tax_id = sales_item.sales_item_tax_id;
    var item_tax_percentage = sales_item.sales_item_tax_percentage;
    var item_tax_amount = sales_item.sales_item_tax_amount;
    var item_tax_cess_id = sales_item.sales_item_tax_cess_id;
    var item_tax_cess_percentage = sales_item.sales_item_tax_cess_percentage;
    var item_tax_cess_amount = sales_item.sales_item_tax_cess_amount;
    var grand_total = sales_item.sales_item_grand_total;

    if (item_tds_id == "") {
        item_tds_id = 0;
    }

    var billing_state_id = $('#billing_state').val();
    var billing_country_id = $('#billing_country').val();
    var type_of_supply = $('#type_of_supply').val();
    var item_discount = 0;

    table_index = +table_index;

    var input_type = "text";
    var quantity_type = "text";
    var grand_total_type = "text";
    var display_style = "";
    var color_style = "style='color:red;'";
    if (settings_item_editable == "no" || loop_edit == "no") {
        input_type = "hidden";
        display_style = "style='display:none;'";
        color_style = "style='color:red;display:none;'";
    }

    if (loop_edit == "no") {
        quantity_type = "hidden";
        grand_total_type = "text";
    }

    var discount_exist = 0;
    var tax_exist = 0;
    if (settings_discount_visible != "no") {
        discount_exist = 1;
        var select_discount = "";
        if (input_type == "hidden") {
            select_discount += '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            select_discount += '<div class="form-group" style="margin-bottom:0px !important;">';
        }

        select_discount += '<select class="form-control open_discount form-fixer select2" name="item_discount" style="width: 100%;">';
        select_discount += '<option value="">Select</option>';
        for (a = 0; a < data.discount.length; a++) {
            selected = '';
            if(sales_item_discount_id == data.discount[a].discount_id) selected= 'selected';
            if(parseFloat(data.discount[a].discount_value) >= parseFloat(item_discount_percentage)){
                select_discount += '<option value="' + data.discount[a].discount_id + '-' + precise_amount(data.discount[a].discount_value) + '" '+selected+' >' + parseFloat(data.discount[a].discount_value) + '%</option>'
            }
        }
        select_discount += '</select></div>';
    }
    if (settings_tds_visible == "yes") {
        var select_tds = '<input type="text" class="form-control open_tds_modal pointer" name="item_tds_percentage" value="'+parseFloat(item_tds_percentage)+'%" readonly>';

        var tds_body = '<table id="tds_table" index="'+ table_index +'" class="table table-bordered table-striped sac_table ">\
                    <thead>\
                    <th>TAX Name</th>\
                    <th>Action</th>\
                    </thead>\
                    <tbody>';
     
        for (a = 0; a < data.tax.length; a++) {
            
            if((data.tax[a].tax_name == 'TDS' || data.tax[a].tax_name == 'TCS')  && parseFloat(data.tax[a].tax_value) <= parseFloat(item_tds_percentage)){
                tax_name = data.tax[a].tax_name;
                if(item_tds_type.toLowerCase() == tax_name.toLowerCase()){
                    var selected = "";
                    if (item_tds_id == data.tax[a].tax_id) {
                       var selected = "selected";
                    }
                    tds_body += '<tr>\
                            <td>'+data.tax[a].tax_name+'(Sec '+data.tax[a].section_name+') @ '+parseFloat(data.tax[a].tax_value) +'%</td>\
                            <td><div class="radio">\
                                    <label><input type="radio" name="tds_tax" value="'+precise_amount(data.tax[a].tax_value) +
                       '"'+selected +" tds_id='"+data.tax[a].tax_id+"' typ='"+data.tax[a].tax_name+"'></label>\
                                </div></td></tr>";
                }
            }
        }
        tds_body += '</tbody></table>';
    }
    
    if (settings_tax_type != "no_tax") {
        tax_exist = 1;
        var gst_disable = ''; 
        if(type_of_supply == 'export_without_tax'){
            item_tax_id = '0';
            gst_disable = 'disabled';
        } 

        /*var select_tds = '';*/
        
        /*if (input_type == "hidden") {
            select_tds +=
                '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            select_tds +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
        }
        select_tds +=
            '<select class="form-control open_tax form-fixer select2" name="item_tds_percentage" style="width: 100%;" '+gst_disable+'>';
        select_tds += '<option value="">Select</option>';
        
        for (a = 0; a < data.tax.length; a++) {
            var selected = "";
            if (item_tds_id == data.tax[a].tax_id) {
                selected = "selected";
            }
            if((data.tax[a].tax_name == 'TDS' || data.tax[a].tax_name == 'TCS')  && parseFloat(data.tax[a].tax_value) <= parseFloat(item_tds_percentage)){
                tax_name = data.tax[a].tax_name;
                if(item_tds_type.toLowerCase() == tax_name.toLowerCase()){
                    select_tds +=
                        '<option value="'+
                        precise_amount(data.tax[a].tax_value) +
                        '" ' +
                        selected +
                        " tds_id='"+data.tax[a].tax_id+"' type='"+data.tax[a].tax_name+"'>" +
                        precise_amount(data.tax[a].tax_value) +
                        "%</option>";
                }
            }
        }
        select_tds += "</select></div>";*/
        
        var select_tax = "";
        if (input_type == "hidden") {
            select_tax += '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            select_tax += '<div class="form-group" style="margin-bottom:0px !important;">';
        }

        select_tax += '<select class="form-control open_tax form-fixer select2" name="item_tax" style="width: 100%;">';
        select_tax += '<option value="">Select</option>';
        for (a = 0; a < data.tax.length; a++) {
            if(data.tax[a].tax_name == 'GST' && parseFloat(data.tax[a].tax_value) <= parseFloat(item_tax_percentage)){
                var selected = "";
                if (item_tax_id == data.tax[a].tax_id) {
                    selected = "selected";
                }
                select_tax += '<option value="' + data.tax[a].tax_id + '-' + parseFloat(data.tax[a].tax_value) + '" ' + selected + ' >' + parseFloat(data.tax[a].tax_value) + '%</option>'
            }
        }
        select_tax += '</select></div>';
    }

    var cess_select = "";
    if (input_type == "hidden") {
        cess_select +=
            '<div class="form-group" style="margin-bottom:0px !important;display:none">';
    } else {
        cess_select +=
            '<div class="form-group" style="margin-bottom:0px !important;">';
    }
    
    cess_select +=
        '<select class="form-control open_tax form-fixer select2" name="item_tax_cess" style="width: 100%;" '+gst_disable+'>';
    cess_select += '<option value="">Select</option>';
    for (a = 0; a < data.tax.length; a++) {
        var selected = "";
        if (item_tax_cess_id == data.tax[a].tax_id) {
            selected = "selected";
        }
        if(data.tax[a].tax_name == 'CESS' && parseFloat(data.tax[a].tax_value) <= parseFloat(item_tax_cess_percentage)){
            cess_select +=
                '<option value="' +data.tax[a].tax_id +"-" + parseFloat(data.tax[a].tax_value) +'" ' +
                selected +" >" +parseFloat(data.tax[a].tax_value) + "%</option>";
        }
    }
    cess_select += "</select></div>";

    var newRow = $("<tr id=" + table_index + ">");
    var cols = "";
    cols += "<td><a class='deleteRow'><input type='hidden' name='loop_exist' value='" + loop + "' > <img src='" + base_url + "assets/images/bin_close.png' /> </a><input type='hidden' name='item_key_value'  value=" + table_index + "><input type='hidden' name='item_id' value=" + item_id + "><input type='hidden' name='item_type' value=" + item_type + "><input type='hidden' name='item_code' value='" + item_code + "'></td>";
    if (item_type == 'product' || item_type == 'product_inventory')
        cols += "<td>" + item_name + "<br>(P) (HSN/SAC:" + item_hsn_sac_code + ")<br>"+ product_batch +"</td>";
    else
        cols += "<td>" + item_name + "<br>(S) (HSN/SAC:" + item_hsn_sac_code + ")</td>";

    if (settings_description_visible == "yes") {
        cols += "<td>" + "<input type='text' class='form-control form-fixer' name='item_description' value='"+item_description+"'></td>";
    }

    if (quantity_type == "hidden") {
        cols += "<td style='text-align:center'><input type='" + quantity_type + "' class='form-control form-fixer text-center float_number' value='" + item_quantity + "' data-rule='quantity' name='item_quantity'><span id='item_quantity_hide_lbl_" + table_index + "'  >" + quantity + "</span></td>";

    } else {
        cols += "<td><input type='text' class='form-control form-fixer text-center float_number' value='" + item_quantity + "' limit='"+item_quantity+"' data-rule='quantity' name='item_quantity'></td>";

    }
    /*if (input_type == "hidden") {
        cols += "<td style='text-align:center'>" + "<span id='item_price_hide_lbl_" + table_index + "' class='text-center'>" + item_price + "</span>";
    } else {
        cols += "<td>"
    }*/

   /* cols += "<input type='" + input_type + "' class='form-control form-fixer text-right float_number' name='item_price' value='" + item_price + "' limit='"+item_price+"'>" + "<span id='item_sub_total_lbl_" + table_index + "' class='pull-right' " + display_style + "  >" + item_price + "</span>" + "<input type='hidden' class='form-control form-fixer text-right' style='' value='" + item_price + "' name='item_sub_total'>" + "</td>";
    if (discount_exist == 1) {
        if (input_type == "hidden") {
            cols += "<td style='text-align:center'>" + "<span id='item_discount_hide_lbl_" + table_index + "' class='text-center' ></span>";
        } else {
            cols += "<td>"
        }
        cols += "<input type='hidden' name='item_discount_id' value='0'>" + "<input type='hidden' name='item_discount_percentage' value='0'>" + "<input type='hidden' name='item_discount_amount' value='0'>" + select_discount + "<span id='item_discount_lbl_" + table_index + "' class='pull-right' " + color_style + " >0.00</span>" + "</td>";
    }*/

    //taxable area
    /*if (tax_exist == 1) {
        if (discount_exist == 1) {*/
            cols += "<td style='text-align:center'>\
            <input type='hidden' class='form-control form-fixer text-right' style='' value='" + item_price + "' name='item_sub_total'><input type='hidden' class='form-control form-fixer text-right float_number' name='item_price' value='" + item_price + "' limit='nochange'>";
            if (discount_exist == 1) {
                cols += "<input type='hidden' name='item_discount_id' value='0'>" + "<input type='hidden' name='item_discount_percentage' value='0'>" + "<input type='hidden' name='item_discount_amount' value='0'>";
            }
            item_taxable_value = precise_amount(item_price * item_quantity);
            cols += "<input type='text' class='form-control form-fixer text-center float_number' name='item_taxable_value' value='" + item_taxable_value + "' limit='" + item_taxable_value + "'></td>";
         /*}
    }*/

    if (settings_tds_visible == "yes") {
        var tds_input = input_type;
        // tds area
        if (tds_input == "hidden" || (item_tds_id == "" || item_tds_id == 0)) {
            tds_input = "hidden";
            cols +=
                "<td style='text-align:center'>";
        } else {
            cols += "<td>";
        }

        cols +=
            "<input type='hidden' name='item_tds_id' value='" +
            item_tds_id +
            "'>" +
            "<input type='hidden' name='item_tds_type' value='" +
            item_tds_type +
            "'>" +
            /*"<input type='" +
            tds_input +
            "' class='form-control form-fixer text-center float_number' name='item_tds_percentage' value='" +
            item_tds_percentage +
            "'>" +*/
            select_tds+"<div class='tds_modal_body' style='display:none;'>"+tds_body+"</div>"+
            "<input type='hidden' name='item_tds_amount' value='0'><span id='item_tds_lbl_" +
            table_index +
            "' class='pull-right' style='color:red;'>0.00</span>" +
            "</td>";
    }

    //tax area
    if (tax_exist == 1) {
        if (input_type == "hidden") {
            cols += "<td style='text-align:center'>" + "<span id='item_tax_percentage_hide_lbl_" + table_index + "' class='text-center' >0.00</span><br/>";
        } else {
            cols += "<td>"
        }
        cols += "<input type='hidden' name='item_tax_id' value='"+item_tax_id+"'>" + "<input type='hidden' name='item_tax_percentage' value='"+item_tax_percentage+"'>" 
        + "<input type='hidden' name='item_tax_amount' value='"+item_tax_amount+"'>" +
        "<input type='hidden' name='item_tax_amount_cgst' value='0'>" +
        "<input type='hidden' name='item_tax_amount_sgst' value='0'>" +
        "<input type='hidden' name='item_tax_amount_igst' value='0'>"
        + select_tax + "<span id='item_tax_lbl_" + table_index + "' class='pull-right' style='color:red;'>"+item_tax_amount+"</span>" + "</td>";
    }

    if (input_type == "hidden") {
        cols +=
            "<td style='text-align:center'>" +
            "<span id='item_tax_percentage_hide_lbl_" +
            table_index +
            "' class='text-center' >0.00</span><br/>";
    } else {
        cols += "<td>";
    }

    cols += "<input type='hidden' name='item_tax_cess_id' value='"+item_tax_cess_id+"'>" +
            "<input type='hidden' name='item_tax_cess_percentage' value='"+item_tax_cess_percentage+"'>" +
            "<input type='hidden' name='item_tax_cess_amount' value='"+item_tax_cess_amount+"'>" +
            cess_select +
            "<span id='item_tax_cess_lbl_" +
            table_index +
            "' class='pull-right' style='color:red;'>0.00</span>" +
            "</td>";

    //tax area 

    if (input_type == "hidden" && grand_total_type == "hidden") {
        cols += "<td style='text-align:center'>" + "<span id='item_grand_total_hide_lbl_" + table_index + "' class='text-center' >0.00</span>";
    } else {
        cols += "<td>"
    }
    cols += "<input type='" + grand_total_type + "' class='float_number form-control form-fixer text-right' name='item_grand_total' value='"+grand_total+"' limit='"+grand_total+"'></td>";
    cols += "</tr>";

    newRow.append(cols);
    $("#sales_credit_note_table_body").prepend(newRow);
    table_index++;
    calculateTable(newRow);
    $(".select2").select2();
}
//calculate row
function calculateRow(row) {
    // var key = +row.index();
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_price = +parseFloat(row.find('input[name="item_price"]').val());
    var item_quantity = +row.find('input[name="item_quantity"]').val();
    var loop = row.find('input[name^="loop_exist"]').val();
    var sub_total_editable = "yes";
    /*if (loop == "loop") {
        item_quantity = 1;
        sub_total_editable = "yes";
    }*/
    var item_sub_total = parseFloat(precise_amount(item_price) * item_quantity);
    row.find('input[name^="item_sub_total"]').val(precise_amount(item_sub_total));
    row.find('#item_sub_total_lbl_' + table_index).text(item_sub_total);
    
    if (settings_item_editable == "no" || sub_total_editable == "no") {
        row.find('#item_price_hide_lbl_' + table_index).text(item_sub_total);
    }

}
// calculate row
//calculate Discount

function calculateDiscount(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();

    var discount_data = row.find('select[name^="item_discount"]').val();
    if (discount_data == "" || discount_data == null || typeof discount_data == 'undefined') {
        discount_data = 0;

    }
  
    var discount_split = discount_data.toString().split("-");
    var item_discount_id = +discount_split[0];
    var item_discount = +parseFloat(discount_split[1]);

    if (item_discount == "" || isNaN(item_discount)) {
        item_discount = 0;
    }
    var item_sub_total = +parseFloat(row.find('input[name^="item_sub_total"]').val());
    var item_discount_amount = precise_amount(item_sub_total) * item_discount / 100;

    row.find('input[name^="item_discount_amount"]').val(precise_amount(item_discount_amount));

    row.find('input[name^="item_discount_id"]').val(item_discount_id);
    row.find('input[name^="item_discount_percentage"]').val(item_discount);
    row.find('#item_discount_lbl_' + table_index).text(item_discount_amount);

    var loop = row.find('input[name^="loop_exist"]').val();
    var discount_editable = "yes";
    if (loop == "loop") {
        discount_editable = "no";
    }
    if (settings_item_editable == "no" || discount_editable == "no") {
        row.find('#item_discount_hide_lbl_' + table_index).text(item_discount_amount);
    }

}
/*
function calculateTax(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var tax_data = row.find('select[name^="item_tax"]').val();
    var cess_tax = row.find('select[name=item_tax_cess]').val();
    if(tax_data != '' && typeof tax_data != 'undefined' ){
        var tax_split = tax_data.toString().split("-");
        var item_tax_id = +tax_split[0];
        var item_tax = +parseFloat(tax_split[1]);
        if (item_tax == "" || isNaN(item_tax)) {
            item_tax = 0;
        }
    }

    if(cess_tax != '' && typeof cess_tax != 'undefined' ){
        var tax_cess_split = cess_tax.toString().split("-");
        var item_tax_cess_id = +tax_cess_split[0];
        var item_tax_cess = +parseFloat(tax_cess_split[1]);
        if (item_tax_cess == "" || isNaN(item_tax_cess)) {
            item_tax_cess = 0;
        }
    }

    var type_of_supply = $('#type_of_supply').val();
    if (type_of_supply == "export_without_payment") {
        item_tax_id = 0;
        item_tax = 0;
    }
    var item_sub_total = +parseFloat(row.find('input[name^="item_sub_total"]').val());
    var item_discount_amount = +parseFloat(row.find('input[name^="item_discount_amount"]').val());
    var item_taxable_value = item_sub_total;
    if (typeof item_discount_amount != 'undefined' && !isNaN(item_discount_amount)) {
        item_taxable_value = item_sub_total - item_discount_amount;
        row.find('#item_taxable_value_lbl_' + table_index).text(item_taxable_value);
        row.find('input[name^="item_taxable_value"]').val(item_taxable_value);
    }

    var item_tax_amount = item_taxable_value * item_tax / 100;
    var item_tax_cess_amount = (item_taxable_value * item_tax_cess) / 100;
    var other_tax_amount = $('#total_other_taxable_amount').val();
    if(other_tax_amount == '' || typeof other_tax_amount == 'undefined' )
        other_tax_amount = 0;

    if(settings_tax_type == 'gst'){
        var cgst_amount_percentage = parseFloat(settings_tax_percentage);
        var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
        var sales_igst_amount = 0; 
        var sales_cgst_amount = 0;
        var sales_sgst_amount = 0;
        var txable_amount = (parseFloat(other_tax_amount) + parseFloat(item_tax_amount));
        if(branch_country_id == $('#billing_country').val()){
            if(branch_state_id == $('#billing_state').val()){
                sales_igst_amount = 0;
                sales_cgst_amount = (txable_amount * cgst_amount_percentage)/100;
                sales_sgst_amount = (txable_amount * sgst_amount_percentage)/100;
            }else{
                sales_igst_amount = txable_amount;
                sales_cgst_amount = 0;
                sales_sgst_amount = 0;
                item_tax_cess_amount = 0;
            }
        }else{
            if($('#type_of_supply').val() == 'export_with_payment'){
                sales_igst_amount = txable_amount;
                sales_cgst_amount = 0;
                sales_sgst_amount = 0;
                item_tax_cess_amount= 0;
            }
        }
    }
    item_tax_amount = item_tax_amount || 0;
    item_tax_cess_amount = item_tax_cess_amount || 0;
    sales_igst_amount = sales_igst_amount || 0;
    sales_sgst_amount = sales_sgst_amount || 0;
    sales_cgst_amount = sales_cgst_amount || 0;
    item_tax = item_tax || 0;
    item_tax_cess = item_tax_cess || 0;
    item_tax_amount = item_tax_amount || 0;
    row.find('input[name="item_tax_amount"]').val(item_tax_amount);
    row.find('input[name="item_tax_cess_amount"]').val(item_tax_cess_amount);
    row.find('input[name^="item_tax_amount_igst"]').val(sales_igst_amount);
    row.find('input[name^="item_tax_amount_sgst"]').val(sales_sgst_amount);
    row.find('input[name^="item_tax_amount_cgst"]').val(sales_cgst_amount);
    row.find('input[name^="item_tax_id"]').val(item_tax_id);
    row.find('input[name^="item_tax_cess_id"]').val(item_tax_cess_id);
    row.find('input[name^="item_tax_percentage"]').val(item_tax);
    row.find('input[name^="item_tax_cess_percentage"]').val(item_tax_cess);
    row.find("#item_tax_lbl_" + table_index).text(item_tax_amount);
    row.find("#item_tax_cess_lbl_" + table_index).text(item_tax_cess_amount);

    var loop = row.find('input[name^="loop_exist"]').val();
    var tax_editable = "yes";
    if (loop == "loop") {
        tax_editable = "no";
    }

    if (settings_item_editable == "no" || tax_editable == "no") {
        row.find('#item_tax_percentage_hide_lbl_' + table_index).text(item_tax + ' %');
    }
}*/

function calculateTax(row) {
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_discount_amount = +parseFloat(
        row.find('input[name^="item_discount_amount"]').val()
    );
    var item_taxable_value = precise_amount(item_sub_total);
    item_taxable_value = +parseFloat(row.find('input[name^="item_taxable_value"]').val());
    /*if (typeof item_discount_amount != "undefined" && !isNaN(item_discount_amount)) {
        item_taxable_value = item_sub_total - item_discount_amount;
        console.log(item_taxable_value,'item_taxable_value');
        row.find("#item_taxable_value_lbl_" + table_index).text(item_taxable_value);
        row.find('input[name^="item_taxable_value"]').val(item_taxable_value);
    }*/

    if (settings_tax_type != "no_tax") {
        var table_index = +row.find('input[name^="item_key_value"]').val();
        var tax_data = row.find('select[name="item_tax"]').val();
        if(tax_data != '' && typeof tax_data != 'undefined' ){
            var tax_split = tax_data.toString().split("-");
            var item_tax_id = +tax_split[0];
            var item_tax = +parseFloat(tax_split[1]);
            if (item_tax == "" || isNaN(item_tax)) {
                item_tax = 0;
            }
        }
        var cess_tax = row.find('select[name=item_tax_cess]').val();
            
        if(cess_tax != '' && typeof cess_tax != 'undefined' ){
            var tax_cess_split = cess_tax.toString().split("-");
            var item_tax_cess_id = +tax_cess_split[0];
            var item_tax_cess = +parseFloat(tax_cess_split[1]);
            if (item_tax_cess == "" || isNaN(item_tax_cess)) {
                item_tax_cess = 0;
            }
        }
        var type_of_supply = $("#type_of_supply").val();
        
        if (type_of_supply == "export_without_payment") {
            item_tax_id = 0;
            item_tax = 0;
            item_tax_cess_id = item_tax_cess =0;
            /*row.find('[name=item_tax]').val('').change();*/
        }
        var item_tax_amount = (item_taxable_value * item_tax) / 100;
        if(settings_tax_type == 'gst'){
            var cgst_amount_percentage = parseFloat(settings_tax_percentage);
            var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
            var sales_igst_amount = 0; 
            var sales_cgst_amount = 0;
            var sales_sgst_amount = 0;
            var item_tax_cess_amount = 0;
           /* var txable_amount = (parseFloat(other_tax_amount) + parseFloat(item_tax_amount));*/
            var txable_amount = parseFloat(precise_amount(item_tax_amount));

            if($('#type_of_supply').val() == 'regular'){
                if(branch_state_id == $('#billing_state').val()){
                    sales_igst_amount = 0;
                    sales_cgst_amount = (txable_amount * cgst_amount_percentage)/100;
                    sales_sgst_amount = (txable_amount * sgst_amount_percentage)/100;
                    item_tax_cess_amount = (item_taxable_value * item_tax_cess) / 100;
                }else{
                    sales_igst_amount = txable_amount;
                    sales_cgst_amount = 0;
                    sales_sgst_amount = 0;
                    item_tax_cess_amount = (item_taxable_value * item_tax_cess) / 100;
                }
            }else{
                if($('#type_of_supply').val() == 'export_with_payment'){
                    sales_igst_amount = txable_amount;
                    sales_cgst_amount = 0;
                    sales_sgst_amount = 0;
                    item_tax_cess_amount = (item_taxable_value * item_tax_cess) / 100;
                }
            }
        }
    }
    
    /*var other_tax_amount = $('#total_other_taxable_amount').val();
    if(other_tax_amount == '' || typeof other_tax_amount == 'undefined' )
        other_tax_amount = 0;*/
    item_tax_amount = item_tax_amount || 0;
    item_tax_cess_amount = item_tax_cess_amount || 0;
    sales_igst_amount = sales_igst_amount || 0;
    sales_sgst_amount = sales_sgst_amount || 0;
    sales_cgst_amount = sales_cgst_amount || 0;
    item_tax = item_tax || 0;
    item_tax_cess = item_tax_cess || 0;
    item_tax_amount = item_tax_amount || 0;
    row.find('input[name="item_tax_amount"]').val(precise_amount(item_tax_amount));
    row.find('input[name="item_tax_cess_amount"]').val(precise_amount(item_tax_cess_amount));
    row.find('input[name^="item_tax_amount_igst"]').val(precise_amount(sales_igst_amount));
    row.find('input[name^="item_tax_amount_sgst"]').val(precise_amount(sales_sgst_amount));
    row.find('input[name^="item_tax_amount_cgst"]').val(precise_amount(sales_cgst_amount));
    row.find('input[name^="item_tax_id"]').val(item_tax_id);
    row.find('input[name^="item_tax_cess_id"]').val(item_tax_cess_id);
    row.find('input[name^="item_tax_percentage"]').val(precise_amount(item_tax));
    row.find('input[name^="item_tax_cess_percentage"]').val(precise_amount(item_tax_cess));
    row.find("#item_tax_lbl_" + table_index).text(precise_amount(item_tax_amount));
    row.find("#item_tax_cess_lbl_" + table_index).text(precise_amount(item_tax_cess_amount));

    var loop = row.find('input[name^="loop_exist"]').val();
    var tax_editable = "yes";
    if (loop == "loop") {
        tax_editable = "no";
    }

    if (settings_item_editable == "no") {
        row
            .find("#item_tax_percentage_hide_lbl_" + table_index)
            .text(item_tax + " %");
    }
}
function calculateTds(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();

    var item_tds_id = +row.find('input[name^="item_tds_id"]').val();
    var item_tds = +parseFloat(row.find('[name^="item_tds_percentage"]').val());

    if (item_tds == "" || isNaN(item_tds)) {
        item_tds = 0;
    }

    var item_sub_total = +parseFloat(row.find('input[name^="item_sub_total"]').val());
    var item_discount_amount = +parseFloat(row.find('input[name^="item_discount_amount"]').val());
    var item_taxable_value = precise_amount(item_sub_total);
    if (typeof item_discount_amount != 'undefined' && !isNaN(item_discount_amount)) {
        item_taxable_value = precise_amount(item_sub_total) - precise_amount(item_discount_amount);
    }
   
    var item_tds_amount = item_taxable_value * item_tds / 100;

    row.find('input[name^="item_tds_amount"]').val(item_tds_amount);
    /*row.find('input[name^="item_tds_id"]').val(item_tds_id);
    row.find('input[name^="item_tds_percentage"]').val(item_tds);*/
    row.find('#item_tds_lbl_' + table_index).text(precise_amount(item_tds_amount));

    var loop = row.find('input[name^="loop_exist"]').val();
    var tds_editable = "yes";
    if (loop == "loop") {
        tds_editable = "no";
    }

    if (settings_item_editable == "no" || tds_editable == "no") {
        row.find('#item_tds_percentage_hide_lbl_' + table_index).text(item_tds + ' %');
    }
}

function calculateRowTotal(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_sub_total = +parseFloat(row.find('input[name="item_sub_total"]').val());
    var item_discount_amount = +parseFloat(row.find('input[name="item_discount_amount"]').val());
    var item_tax_amount = +parseFloat(row.find('input[name="item_tax_amount"]').val());
    var item_taxable_value = +parseFloat(row.find('input[name="item_taxable_value"]').val());
    var item_tds_amount = +parseFloat(row.find('input[name="item_tds_amount"]').val());
    var item_tds_type = row.find('input[name="item_tds_type"]').val();
    var item_tax_cess_amount = +parseFloat(row.find('input[name="item_tax_cess_amount"]').val());

    if (typeof item_taxable_value === 'undefined' || isNaN(item_taxable_value)) {
        item_taxable_value = item_sub_total;

    }
    if (typeof item_tds_amount === 'undefined' || isNaN(item_tds_amount)) {
        item_tds_amount = 0;

    } else {
        if (item_tds_type == "tds" || item_tds_type == 'TDS') {
            item_tds_amount = 0;
        }
    }
    if (typeof item_tax_amount === 'undefined' || isNaN(item_tax_amount)) {
        item_tax_amount = 0;
    }
    if (typeof item_tax_cess_amount === "undefined" || isNaN(item_tax_cess_amount)) 
        item_tax_cess_amount = 0;
    var item_grand_total = (item_taxable_value + item_tax_amount + item_tds_amount + item_tax_cess_amount);
    row.find('input[name="item_grand_total"]').val(item_grand_total);
    if (settings_item_editable == "no") {
        row.find('#item_grand_total_hide_lbl_' + table_index).text(item_grand_total);
    }
}

function preciseRowAmount(row) {
    var table_index = +row.find('input[name="item_key_value"]').val();
    var item_price = +parseFloat(row.find('input[name="item_price"]').val());
    var item_sub_total = +parseFloat(row.find('input[name="item_sub_total"]').val());
    var item_discount_amount = +parseFloat(row.find('input[name="item_discount_amount"]').val());
    var item_tax_amount = +parseFloat(row.find('input[name="item_tax_amount"]').val());
    var item_taxable_value = +parseFloat(row.find('input[name="item_taxable_value"]').val());
    var item_grand_total = +parseFloat(row.find('input[name="item_grand_total"]').val());
    var item_tds_amount = +parseFloat(row.find('input[name="item_tds_amount"]').val());
    var item_tax_cess_amount = +parseFloat(row.find('input[name="item_tax_cess_amount"]').val());
    var item_tax_amount_cgst = +parseFloat(
        row.find('input[name^="item_tax_amount_cgst"]').val()
    );
    var item_tax_amount_sgst = +parseFloat(
        row.find('input[name^="item_tax_amount_sgst"]').val()
    );
    var item_tax_amount_igst = +parseFloat(
        row.find('input[name^="item_tax_amount_igst"]').val()
    );

    var loop = row.find('input[name^="loop_exist"]').val();
    var sub_total_editable = "yes";
    var discount_editable = "yes";
    var tax_editable = "yes";
    var tds_editable = "yes";
    if (loop == "loop") {
        sub_total_editable = "no";
        discount_editable = "no";
        tax_editable = "no";
        tds_editable = "no";
    }

    if (typeof item_taxable_value !== 'undefined' && !isNaN(item_taxable_value)) {

        row.find('input[name^="item_taxable_value"]').val(precise_amount(item_taxable_value));
        row.find('#item_taxable_value_lbl_' + table_index).text(precise_amount(item_taxable_value));
    }
    if (typeof item_tax_amount !== 'undefined' && !isNaN(item_tax_amount)) {
        row.find('input[name^="item_tax_amount"]').val(precise_amount(item_tax_amount));
        row.find('#item_tax_lbl_' + table_index).text(precise_amount(item_tax_amount));
    }
    if (typeof item_tax_cess_amount !== "undefined" && !isNaN(item_tax_cess_amount)) {
        row
            .find('input[name="item_tax_cess_amount"]')
            .val(precise_amount(item_tax_cess_amount));
        row
            .find("#item_tax_cess_lbl_" + table_index)
            .text(precise_amount(item_tax_cess_amount));
    }
    if (typeof item_tax_amount_igst !== "undefined" && !isNaN(item_tax_amount_igst)) {
        row
            .find('input[name^="item_tax_amount_igst"]')
            .val(precise_amount(item_tax_amount_igst));
    }
    if (typeof item_tax_amount_sgst !== "undefined" && !isNaN(item_tax_amount_sgst)) {
        row
            .find('input[name^="item_tax_amount_sgst"]')
            .val(precise_amount(item_tax_amount_sgst));
    }
    if (typeof item_tax_amount_cgst !== "undefined" && !isNaN(item_tax_amount_cgst)) {
        row
            .find('input[name^="item_tax_amount_cgst"]')
            .val(precise_amount(item_tax_amount_cgst));
    }
    if (typeof item_tds_amount !== 'undefined' && !isNaN(item_tds_amount)) {
        row.find('input[name^="item_tds_amount"]').val(precise_amount(item_tds_amount));
        row.find('#item_tds_lbl_' + table_index).text(precise_amount(item_tds_amount));
    }

    if (typeof item_discount_amount !== 'undefined' && !isNaN(item_discount_amount)) {
        row.find('input[name^="item_discount_amount"]').val(precise_amount(item_discount_amount));
        row.find('#item_discount_lbl_' + table_index).text(precise_amount(item_discount_amount));

        if (settings_item_editable == "no" || discount_editable == "no") {
            row.find('#item_discount_hide_lbl_' + table_index).text(precise_amount(item_discount_amount));
        }
    }

    row.find('input[name^="item_price"]').val(precise_amount(item_price));
    row.find('input[name^="item_sub_total"]').val(precise_amount(item_sub_total));
    row.find('#item_sub_total_lbl_' + table_index).text(precise_amount(item_sub_total));
    row.find('input[name="item_grand_total"]').val(precise_amount(item_grand_total));

    if (settings_item_editable == "no" || sub_total_editable == "no") {
        row.find('#item_price_hide_lbl_' + table_index).text(precise_amount(item_sub_total));
        row.find('#item_grand_total_hide_lbl_' + table_index).text(precise_amount(item_grand_total));
    }
}

function generateJson(row) {
    var table_index = +row.find('input[name="item_key_value"]').val();
    var item_price = +parseFloat(row.find('input[name="item_price"]').val());
    var item_description = row.find('input[name="item_description"]').val();
    var item_quantity = +row.find('input[name="item_quantity"]').val();
    var item_id = +row.find('input[name="item_id"]').val();
    var item_type = row.find('input[name="item_type"]').val();
    var item_code = row.find('input[name="item_code"]').val();
    var item_sub_total = +parseFloat(row.find('input[name="item_sub_total"]').val());

    var item_discount_amount = +parseFloat(row.find('input[name="item_discount_amount"]').val());
    var item_tax_amount = +parseFloat(row.find('input[name="item_tax_amount"]').val());
    var item_tax_cess_amount = +parseFloat(
        row.find('input[name="item_tax_cess_amount"]').val()
    );
    var item_discount_id = +row.find('input[name="item_discount_id"]').val();
    var item_tax_id = +row.find('input[name="item_tax_id"]').val();
    var item_tax_cess_id = +row.find('input[name="item_tax_cess_id"]').val();

    var item_tds_id = +row.find('input[name="item_tds_id"]').val();
    var item_tds_percentage = +parseFloat(row.find('[name="item_tds_percentage"]').val());
    var item_tds_amount = +parseFloat(row.find('input[name="item_tds_amount"]').val());
    var item_tds_type = row.find('input[name="item_tds_type"]').val();

    var item_discount_percentage = +parseFloat(row.find('input[name="item_discount_percentage"]').val());
    var item_tax_percentage = +parseFloat(row.find('input[name="item_tax_percentage"]').val());
    var item_tax_cess_percentage = +parseFloat(
        row.find('input[name^="item_tax_cess_percentage"]').val()
    );
    var item_taxable_value = +parseFloat(row.find('input[name="item_taxable_value"]').val());
    var item_grand_total = +parseFloat(row.find('input[name="item_grand_total"]').val());

    if (typeof item_taxable_value === 'undefined' || isNaN(item_taxable_value)) {
        item_taxable_value = item_sub_total;

    }
    if (typeof item_tds_amount === 'undefined' || isNaN(item_tds_amount)) {
        item_tds_amount = 0;
        item_tds_id = 0;
        item_tds_percentage = 0;
    }
    if (typeof item_tax_amount === 'undefined' || isNaN(item_tax_amount)) {
        item_tax_amount = 0;
        item_tax_id = 0;
        item_tax_percentage = 0;
    }

    if (typeof item_tax_cess_amount === "undefined" || isNaN(item_tax_cess_amount)) {
        item_tax_cess_amount = 0;
        item_tax_cess_id = 0;
        item_tax_cess_percentage = 0;
    }

    if (typeof item_discount_amount === 'undefined' || isNaN(item_discount_amount)) {
        item_discount_amount = 0;
        item_discount_id = 0;
        item_discount_percentage = 0;
    }

    var data_item = {
        "item_key_value": +table_index,
        "item_id": +item_id,
        "item_type": item_type,
        "item_code": item_code,
        "item_quantity": +item_quantity,
        "item_price": +item_price,
        "item_description": item_description,
        "item_sub_total": +item_sub_total,
        "item_discount_amount": +item_discount_amount,
        "item_discount_id": +item_discount_id,
        "item_discount_percentage": +item_discount_percentage,
        "item_tax_amount": +item_tax_amount,
        "item_tax_id": +item_tax_id,
        "item_tax_percentage": +item_tax_percentage,
        "item_tax_cess_amount": +item_tax_cess_amount,
        "item_tax_cess_id": +item_tax_cess_id,
        "item_tax_cess_percentage": +item_tax_cess_percentage,
        "item_tds_amount": +item_tds_amount,
        "item_tds_id": +item_tds_id,
        "item_tds_percentage": +item_tds_percentage,
        "item_tds_type": item_tds_type,
        "item_taxable_value": +item_taxable_value,
        "item_grand_total": +item_grand_total

    };
    var flag = 0;
    var i_val = "";
    for (var i = 0; i < sales_credit_note_data.length; i++) {
        if (sales_credit_note_data[i] !== "undefined" && sales_credit_note_data[i].item_key_value == table_index) {
            flag = 1;
            i_val = i;
            break;
        }
    }
    if (flag == 1) {
        sales_credit_note_data[i_val] = data_item;
    } else {
        sales_credit_note_data.push(data_item);
    }

    var table_data = JSON.stringify(sales_credit_note_data);
    $('#table_data').val(table_data);
}

function calculateTable(row) {

    calculateRow(row);
    if (settings_discount_visible != "no") {
        calculateDiscount(row);
    }
    /*if (settings_tax_type != "no_tax") {*/
        calculateTax(row);
    /*}*/
    if (settings_tds_visible != "no") {
        calculateTds(row)
    }

    calculateRowTotal(row);
    preciseRowAmount(row);
    generateJson(row);
    calculateGrandTotal();

    var grand_total = +$('#total_grand_total').val();
    var error = $("#err_product").text();
    if (error != "" && grand_total > 0) {
        $("#err_product").text("");
    }
}
//calculate grand total
function calculateGrandTotal() {
    var sub_total = 0;
    var discount = 0;
    var tax = 0;
    var tax_cess = 0;
    var tax_cgst = 0;
    var tax_sgst = 0;
    var tax_igst = 0;
    var tds = 0;
    var tcs = 0;
    var taxable = 0;
    var grand_total = 0;

    var dflag = 0;
    var tflag = 0;
    var tdsflag = 0;
    var tcsflag = 0;
    $("#sales_credit_note_table_body").find('input[name="item_sub_total"]').each(function () {
        sub_total += +$(this).val()
    });
    $("#sales_credit_note_table_body").find('input[name="item_taxable_value"]').each(function () {
        taxable += +$(this).val()
    });
    $("#sales_credit_note_table_body").find('input[name="item_discount_amount"]').each(function () {
        dflag = 1;
        discount += +$(this).val()
    });
    $("#sales_credit_note_table_body").find('input[name="item_tax_amount"]').each(function () {
        tflag = 1;
        tax += +$(this).val()
    });

    $("#sales_credit_note_table_body")
        .find('input[name="item_tax_cess_amount"]')
        .each(function () {
            tflag = 1;
            tax_cess += +$(this).val();
        });

    $("#sales_credit_note_table_body")
        .find('input[name="item_tax_amount_cgst"]')
        .each(function () {
            tflag = 1;
            tax_cgst += +$(this).val();
        });

    $("#sales_credit_note_table_body")
        .find('input[name="item_tax_amount_sgst"]')
        .each(function () {
            tflag = 1;
            tax_sgst += +$(this).val();
        });

    $("#sales_credit_note_table_body")
        .find('input[name="item_tax_amount_igst"]')
        .each(function () {
            tflag = 1;
            tax_igst += +$(this).val();
        });

    $("#sales_credit_note_table_body").find('input[name^="item_tds_amount"]').each(function () {
        row = $(this).closest("tr");
        var tds_type = row.find('input[name^="item_tds_type"]').val();
        if (tds_type == "tcs" || tds_type == 'TCS') {
            tcs += +$(this).val();
            tcsflag = 1;
        } else {
            tds += +$(this).val();
            tdsflag = 1;
        }
    });

    $("#sales_credit_note_table_body").find('input[name="item_grand_total"]').each(function () {
        grand_total += +$(this).val()
    });

    var total_other_amount = +parseFloat($('#total_other_amount').val());
    if(isNaN(total_other_amount) || typeof total_other_amount == 'undefined') total_other_amount = 0;
    var final_grand_total = grand_total + total_other_amount;

    var other_tax_amount = $('#total_other_taxable_amount').val();
    if(other_tax_amount == '' || typeof other_tax_amount == 'undefined' || isNaN(other_tax_amount))
        other_tax_amount = 0;
    
    if(tax > 0){
        if(tax_cgst > 0){
            var cgst_amount_percentage = parseFloat(settings_tax_percentage);
            var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
            var txable_amount = (parseFloat(other_tax_amount) + parseFloat(tax));
            tax_cgst = (txable_amount * cgst_amount_percentage)/100;
            tax_sgst = (txable_amount * sgst_amount_percentage)/100;
        }
        if(tax_igst > 0){
            tax_igst = (parseFloat(other_tax_amount) + parseFloat(tax));
        }
    }

    if(tax <= 0 && other_tax_amount > 0){
        if($('#type_of_supply').val() != 'import'){
            var cgst_amount_percentage = parseFloat(settings_tax_percentage);
            var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
            if(branch_state_id == $('#billing_state').val()){
                tax_cgst = (other_tax_amount * cgst_amount_percentage)/100;
                tax_sgst = (other_tax_amount * sgst_amount_percentage)/100;
            }else{
                tax_igst = other_tax_amount;
            }
        }else{
            total_other_amount = parseFloat(total_other_amount) - +parseFloat(other_tax_amount);
        }
    }
    
    $('#total_sub_total').val(precise_amount(sub_total));
    $('#total_taxable_amount').val(precise_amount(taxable));
    $('#total_discount_amount').val(precise_amount(discount));
    $('#total_tax_amount').val(precise_amount(tax));
    $("#total_tax_cess_amount").val(precise_amount(tax_cess));
    $('#total_tds_amount').val(precise_amount(tds));
    $('#total_tcs_amount').val(precise_amount(tcs));
    $('#total_other_amount').val(precise_amount(total_other_amount));
    $('#total_grand_total').val(precise_amount(final_grand_total));
    if (!isNaN(final_grand_total) && final_grand_total.toString().indexOf('.') != -1){
        $('#round_off_option').show();
    }else{
        $('#round_off_option').hide();
    }
    $('#without_reound_off_grand_total').val(final_grand_total);
    $('#totalSubTotal').text(precise_amount(sub_total));

    if (settings_discount_visible == "yes" && discount > 0) {
        $('.totalDiscountAmount_tr').show();
        $("#totalDiscountAmount").text(precise_amount(discount));
    }else{
        $('.totalDiscountAmount_tr').hide();
    }

    if (settings_tds_visible == "yes" && tds > 0) {
        $('.tds_amount_tr').show();
        $('#totalTdsAmount').text(precise_amount(tds));
    }else{
        $('.tds_amount_tr').hide();
    }

    if (settings_tds_visible == "yes" && tcs > 0) {
        $('.tcs_amount_tr').show();
        $('#totalTcsAmount').text(precise_amount(tcs));
    }else{
        $('.tcs_amount_tr').hide();
    }

    $('#totalGrandTotal').text(precise_amount(final_grand_total));

    if (settings_tax_type != "no_tax") {
        $("#totalTaxAmount").text(precise_amount(tax));
        
        if(tax_sgst > 0){
            var lbl = 'SGST (+)';
            if($('#billing_state option:selected').attr('utgst') == '1')
                lbl = 'UTGST (+)';
            $('.totalSGSTAmount_tr').find('td:first').text(lbl);
            $('.totalSGSTAmount_tr').show();
            $("#totalSGSTAmount").text(precise_amount(tax_sgst));
        }else{
            $('.totalSGSTAmount_tr').hide();
        }
        if(tax_cgst > 0){
            $("#totalCGSTAmount").text(precise_amount(tax_cgst));
            $('.totalCGSTAmount_tr').show();
        }else{
            $('.totalCGSTAmount_tr').hide();
        }
       
        if(tax_igst > 0){
            $('.totalIGSTAmount_tr').show();
            $("#totalIGSTAmount").text(precise_amount(tax_igst));
        }else{
            $('.totalIGSTAmount_tr').hide();
        }

        if(tax_cess > 0){
            $('.totalCessAmount_tr').show();
            $("#totalTaxCessAmount").text(precise_amount(tax_cess));
        }else{
            $('.totalCessAmount_tr').hide();
        }
    }
    if (common_settings_round_off == "yes") {
        $("input[type=radio][name=round_off_key].minimal").trigger('ifChanged');
    }
}


function reCalculateTable(){
    $("#sales_credit_note_table_body")
        .find('input[name="item_tax_id"]')
        .each(function () {
            var row = $(this).closest("tr");
            //if(type_of_supply == 'export_without_payment') row.find('[name=item_tax]').val('').change();
            calculateTable(row);
        });
}

function disable_select_box(){
    $('.others-data').find('select').each(function(){
        var val = $(this).val();
        var input_val = $(this).closest('.form-group').find('input').val();
            
        if((val != '' && val != undefined )|| (input_val != '' && input_val != '0' && input_val != undefined)){
            val = val.split('-');
            val = val[1];
            $(this).attr('disabled',false);

            $(this).closest('.form-group').find('input').attr('disabled',false);
            var typ = $(this).attr('name');
            $(this).find('option').each(function(k,v){
                var option_frg = $(this).val();
                option_frg = option_frg.split('-');

                if(typ == 'exclusion_other_charge_tax_select'){
                    if(parseFloat(option_frg[1]) < parseFloat(val)){
                        $(this).attr('disabled',true);
                    }else{
                        $(this).attr('disabled',false);
                    }
                }else{
                    if(parseFloat(option_frg[1]) > parseFloat(val)){
                        $(this).attr('disabled',true);
                    }else{
                        $(this).attr('disabled',false);
                    }
                }
            })
        }else{

            $(this).attr('disabled',true);
            $(this).closest('.form-group').find('input').attr('disabled',true);
        }
    })
}

// Delete function
function deleteRow(row) {
    var table_index_key = +row.find('input[name="item_key_value"]').val();
    var j = 0;
    var sales_credit_note_data_temp = new Array();

    for (var i = 0; i < sales_credit_note_data.length; i++) {

        if (sales_credit_note_data[i] !== "undefined" && sales_credit_note_data[i].item_key_value != table_index_key) {
            // sales_credit_note_data.splice(i, 1);
            var data_item = {
                "item_key_value": +sales_credit_note_data[i].item_key_value,
                "item_id": +sales_credit_note_data[i].item_id,
                "item_type": sales_credit_note_data[i].item_type,
                "item_code": sales_credit_note_data[i].item_code,
                "item_quantity": +sales_credit_note_data[i].item_quantity,
                "item_price": +sales_credit_note_data[i].item_price,
                "item_description": sales_credit_note_data[i].item_description,
                "item_sub_total": +sales_credit_note_data[i].item_sub_total,
                "item_discount_amount": +sales_credit_note_data[i].item_discount_amount,
                "item_discount_id": +sales_credit_note_data[i].item_discount_id,
                "item_discount_percentage": +sales_credit_note_data[i].item_discount_percentage,
                "item_tax_amount": +sales_credit_note_data[i].item_tax_amount,
                "item_tax_id": +sales_credit_note_data[i].item_tax_id,
                "item_tax_percentage": +sales_credit_note_data[i].item_tax_percentage,
                "item_tds_amount": +sales_credit_note_data[i].item_tds_amount,
                "item_tds_id": +sales_credit_note_data[i].item_tds_id,
                "item_tds_percentage": +sales_credit_note_data[i].item_tds_percentage,
                "item_tds_type": +sales_credit_note_data[i].item_tds_type,
                "item_taxable_value": +sales_credit_note_data[i].item_taxable_value,
                "item_grand_total": +sales_credit_note_data[i].item_grand_total
            };
            sales_credit_note_data_temp.push(data_item);
        }
    }

    sales_credit_note_data = new Array();
    sales_credit_note_data.length = 0;
    sales_credit_note_data = sales_credit_note_data_temp;

    var table_data = JSON.stringify(sales_credit_note_data);
    $('#table_data').val(table_data);
}
