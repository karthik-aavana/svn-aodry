var mapping = {};

if (typeof count_data === 'undefined' || count_data === null)
{
    var count_data = 0;
}
var table_index = count_data;
var branch_country_id = $('#branch_country_id').val();
var branch_state_id = $('#branch_state_id').val();
var flag2 = 0;
$(document).ready(function ()
{
    $("#purchase_submit").click(function (event) {

        if ($('#invoice_number').val() == "")
        {
            $('#err_invoice_number').text("Please Enter Invoice Number.");
            $('#invoice_number').focus();
            return !1
        } else
        {

            $('#err_invoice_number').text(" ");
            invoice_number_count();

        }
        if ($('#invoice_date').val() == "" || $('#invoice_date').attr('valid') == '0')
        {
            $('#err_date').text("Please Select the Date.");
            $('#invoice_date').focus();
            return !1
        }
        if ($('#supplier').val() == "")
        {
            $('#err_supplier').text("Please Select the Supplier.");
            $('#supplier').focus();
            return !1
        } else
        {
            $('#err_supplier').text("");
        }

        if ($('#purchase_invoice_number').val() == "")
        {
            $('#err_purchase_invoice_number').text("Please Select Purchase Invoice Number.");
            $('#purchase_invoice_number').focus();
            return !1
        } else
        {
            $('#err_purchase_invoice_number').text("");
        }

        if ($('#type_of_supply').val() == "")
        {
            $('#err_type_of_supply').text("Please Select the Type of Supply.");
            $('#err_type_of_supply').focus();
            return !1
        }
        if ($('#billing_country').val() == "")
        {
            $('#err_billing_country').text("Please Select the Billing Country.");
            $('#err_billing_country').focus();
            return !1
        }
        if ($('#billing_state').val() == "")
        {
            $('#err_billing_state').text("Please Select the Billing State.");
            $('#err_billing_state').focus();
            return !1
        }

        var grand_total = $('#total_grand_total').val();
        if (grand_total == "" || grand_total == null || grand_total == 0.00)
        {
            $("#err_product").text("Please select Items");
            $('#input_purchase_code').focus();
            return !1;
        } else
        {
            $("#err_product").text("")
        }

        var tablerowCount = $('#purchase_table_body tr').length;
        if (tablerowCount < 1)
        {
            $("#err_product").text("Please Select Items");
            $('#input_purchase_code').focus();
            return !1
        }
    });

    select_checkbox();
    //date change
    $('#invoice_date').on('changeDate', function ()
    {
        var selected = $(this).val();
        var module_id = $("#module_id").val();
        var date_old = $("#invoice_date_old").val();
        var privilege = $("#privilege").val();
        var old_res = date_old.split("-");
        var new_res = selected.split("-");
        if (old_res[1] != new_res[1])
        {
            $.ajax(
                    {
                        url: base_url + 'general/generate_date_reference',
                        type: 'POST',
                        data:
                                {
                                    date: selected,
                                    privilege: privilege,
                                    module_id: module_id
                                },
                        success: function (data)
                        {

                            var parsedJson = $.parseJSON(data);
                            var type = parsedJson.reference_no;
                            $('#invoice_number').val(type)
                        }
                    })
        } else
        {
            var old_reference = $('#invoice_number_old').val();
            $('#invoice_number').val(old_reference)
        }
    });

    $("#supplier").change(function (event)
    {
        var supplier = $('#supplier').val();
        var s_id = supplier.split('-');
        var supplier_id = s_id[0];
        if (supplier_id != "")
        {
            $.ajax(
                    {
                        url: base_url + 'purchase_return/get_purchase_invoice_number',
                        type: 'POST',
                        dataType: "json",
                        data:
                                {
                                    supplier_id: supplier_id
                                },
                        success: function (result)
                        {
                            // var parsedJson = $.parseJSON(result);
                            // var data=parsedJson.data;
                            $('#purchase_invoice_number').html('');
                            $('#purchase_invoice_number').append('<option value="">Select</option>');
                            for (var i = 0; i < result.length; i++)
                            {
                                $('#purchase_invoice_number').append('<option value="' + result[i].purchase_id + '">' + result[i].purchase_invoice_number + '</option>');
                            }
                        }
                    });
        }

    });

    $("#purchase_invoice_number").change(function (event)
    {
        $('#purchase_table_body').html('');

        var purchase_id = $('#purchase_invoice_number').val();

        if (purchase_id != "")
        {
            $.ajax(
                    {
                        url: base_url + 'purchase/get_purchase_item',
                        type: 'POST',
                        data:
                                {
                                    purchase_id: purchase_id
                                },
                        success: function (result)
                        {
                            var data = $.parseJSON(result);

                           // $('#currency_id').html('');
                           //var option = '<option value="' + data['currency'][0].currency_id + '">' + data['currency'][0].currency_name + '</option>';
                            //$('#currency_id').html(option);
                            var purchase_state_id = data.billing_state_id;

                            $("#billing_state").select2().val(purchase_state_id).trigger('change.select2');
                             if(purchase_state_id == 0){
                                $("#type_of_supply").html("");
                                $("#type_of_supply").append('<option value="import">Import</option>');
                                 
                             }else{
                                $("#type_of_supply").html("");
                                    $("#type_of_supply").append('<option value="regular">Regular</option>');
                             }
                            add_row(data);
                            call_css();
                            $('#table_data').val('');
                            select_checkbox();
                        }
                    });

        }
    });


    //billing country changes
    $("#billing_country").change(function (event)
    {
        var table_data = $('#table_data').val();
        var billing_country = $('#billing_country').val();
        if (branch_country_id != billing_country)
        {
            flag2 = 1;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="0">Other Country</option>');

            $('#type_of_supply').html('');
            $('#type_of_supply').append('<option value="import">Import</option>');
        } else
        {
            flag2 = 2;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="">Select</option>');
            for (var i = 0; i < branch_state_list.length; i++)
            {
                $('#billing_state').append('<option value="' + branch_state_list[i].state_id + '" utgst="' + branch_state_list[i].is_utgst + '">' + branch_state_list[i].state_name + '</option>');
            }
            $('#billing_state').append('<option value="0">Other Country</option>');
            $('#type_of_supply').html('');
            $('#type_of_supply').append('<option value="regular">Regular</option>');

        }
        $('#type_of_supply').change();
        // if (table_data != "")
        // {
        change_gst();
        // }
    });

    //billing state changes
    $("#billing_state").change(function (event)
    {
        var table_data = $('#table_data').val();
        var billing_state_id = $('#billing_state').val();

        // if (table_data != "")
        // {
        change_gst();
        // }
    });

    $("#type_of_supply").change(function (event)
    {
        if ($('#type_of_supply').val() == 'import')
        {
            $('input[name="gstPayable"][value="no"]').attr('checked', false);
            $('input[name="gstPayable"][value="yes"]').attr('checked', true);
        } else
        {
            $('input[name="gstPayable"][value="yes"]').attr('checked', false);
            $('input[name="gstPayable"][value="no"]').attr('checked', true);
        }
    });

    //billing state changes
    $("#supplier").change(function (event)
    {
        var table_data = $('#table_data').val();
        var supplier = $('#supplier').val();
        var supplier_split = supplier.split('-');
        var supplier_id = supplier_split[0];
        var supplier_country_id = supplier_split[1];
        var supplier_state_id = supplier_split[2];

        $("#billing_country").select2().val(supplier_country_id).trigger('change.select2');
        $("#billing_state").select2().val(supplier_state_id).trigger('change.select2');

        var billing_country_id = $('#billing_country').val();

        if (billing_country_id != branch_country_id)
        {
            $('#billing_country').change();
        } else
        {
            $('#billing_country').change();
            $("#billing_state").select2().val(supplier_state_id).trigger('change.select2');
            if (table_data != "")
            {
                change_gst();
            }
        }


    });

    // changes in rows
    $("#purchase_table_body").on("change blur", 'input[name^="item_description"],input[name^="item_price"],input[name^="item_quantity"],select[name^="item_igst"],input[name^="item_cgst"],input[name^="item_sgst"],input[name^="checkbox_valid"],select[name^="item_tax"],select[name^="item_tax_cess"]', function (event){
        console.log('ahi');
        calculateRow($(this).closest("tr"));
        calculateDiscountTax($(this).closest("tr"));
        $purchase_return_id = $('#purchase_return_id').val();
        if ($purchase_return_id > 0){
            calculateGrandTotal();
        } else{
            calculateGrandTotal_check();
        }
    });

    // Invoice number check
    $("#invoice_number").on("blur", function (event)
    {

        var invoice_number = $('#invoice_number').val();
        if (invoice_number == null || invoice_number == "") {
            $("#err_invoice_number").text("Please Enter Invoice Number.");
            return false;
        } else
        {
            $("#err_invoice_number").text("");
            invoice_number_count();
        }


    });

});


//function to add new row to datatable
function add_row(data)
{
    // var flag = 0;
    for (var i = 0; i < data.items.length; i++){
        var purchase_item_id = data.items[i].purchase_item_id;
        var item_id = data.items[i].item_id;
        var item_type = data.items[i].item_type;
        var branch_country_id = data.branch_country_id;
        var branch_state_id = data.branch_state_id;
        var branch_id = data.branch_id;

        if (item_type == 'service')
        {
            var item_code = data.items[i].service_code;
            var item_name = data.items[i].service_name;
            var item_hsn_sac_code = data.items[i].service_hsn_sac_code;
        } else{
            var item_code = data.items[i].product_code;
            var item_name = data.items[i].product_name;
            var item_hsn_sac_code = data.items[i].product_hsn_sac_code;
        }
        var item_description = data.items[i].purchase_item_description;
        var item_quantity = data.items[i].purchase_item_quantity ;
        //- data.items[i].purchase_return_quantity
        var item_price = data.items[i].purchase_item_unit_price;
        var item_igst = data.items[i].purchase_item_igst_percentage;
        var item_cgst = data.items[i].purchase_item_cgst_percentage;
        var item_sgst = data.items[i].purchase_item_sgst_percentage;
        var item_tax_id = data.items[i].purchase_item_tax_id;
        var item_tax_cess_percentage = data.items[i].purchase_item_tax_cess_percentage;
        var item_tax_cess_amount = data.items[i].purchase_item_tax_cess_amount;
        var item_tax_cess_id =   data.items[i].purchase_item_tax_cess_id;

        var purchase_item_discount_amount = data.items[i].purchase_item_discount_amount;

        var billing_state_id = $('#billing_state').val();
        var billing_country_id = $('#billing_country').val();

        var item_discount = 0;

        var temp = {
            "item_id": item_id,
            "item_type": item_type,
            "item_igst": item_igst,
            "item_cgst": item_cgst,
            "item_sgst": item_sgst
        };
        item_gst[item_id + '-' + item_type] = temp;

        if (billing_state_id == branch_state_id){
            var item_igst = 0;
            var item_cgst = item_cgst;
            var item_sgst = item_sgst;

            var igst_required = "no";
        } else
        {
            var item_igst = item_igst;
            var item_cgst = 0;
            var item_sgst = 0;

            var igst_required = "yes";
        }
        if (billing_country_id != branch_country_id)
        {
            var item_igst = item_igst;
            var item_cgst = 0;
            var item_sgst = 0;
            var igst_required = "yes";
        }
        table_index = +table_index;
        var data_item = {
            "item_id": item_id,
            "item_type": item_type,
            "item_code": item_code,
            "item_name": item_name,
            "item_hsn_sac_code": item_hsn_sac_code,
            "item_price": item_price,
            "item_igst": item_igst,
            "item_cgst": item_cgst,
            "item_sgst": item_sgst,
            "item_key_value": table_index
        };

      /*   var select_discount = "";
       select_discount += '<div class="form-group">';
        select_discount += '<select class="form-control open_discount form-fixer select2" name="item_discount" style="width: 100%;" readonly>';
        var item_discount_value = 0;
        for (a = 0; a < data.discount.length; a++)
        {
            if (data.items[i].purchase_item_discount_id == data.discount[a].discount_id)
            {
                select_discount += '<option value="' + data.discount[a].discount_id + '-' + data.discount[a].discount_value + '">' + data.discount[a].discount_value + '%' + '</option>';
                item_discount_value = data.discount[a].discount_value;
            }
        }

        select_discount += '</select></div>'; */

        var select_tax = "";
        select_tax += '<div class="form-group" style="margin-bottom:0px;">';  
        select_tax +=
            '<select class="form-control open_tax form-fixer select2" name="item_tax" style="width: 100%;" disabled="disabled">';
        select_tax += '<option value="">Select</option>';

        for (a = 0; a < data.tax.length; a++) {
            if (item_tax_id == data.tax[a].tax_id) {
                var selected = "selected";
            } else {
                var selected = "";
            }
            if(data.tax[a].tax_name == 'GST'){
                select_tax +=
                    '<option value="' +
                    data.tax[a].tax_id +
                    "-" +
                    parseFloat(data.tax[a].tax_value) +
                    '" ' +
                    selected +
                    " >" +
                    parseFloat(data.tax[a].tax_value) +
                    "%</option>";
            }
        }
        select_tax += "</select></div>";

         var cess_select = "";
        
                cess_select +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
                cess_select +=
                '<select class="form-control open_tax form-fixer select2" name="item_tax_cess" style="width: 100%;"  disabled="disabled">';
                cess_select += '<option value="">Select</option>';
                for (a = 0; a < data.tax.length; a++) {
                if (item_tax_cess_id == data.tax[a].tax_id) {
                    var selected = "selected";
                } else {
                    var selected = "";
                }
                if(data.tax[a].tax_name == 'CESS'){
                    cess_select +=
                        '<option value="' +
                        data.tax[a].tax_id +
                        "-" +
                        parseFloat(data.tax[a].tax_value) +
                        '" ' +
                        selected +
                        " >" +
                        parseFloat(data.tax[a].tax_value) +
                        "%</option>";
                }
            }
        cess_select += "</select></div>";
        var item_discount_value = 0;

        var newRow = $("<tr id=" + table_index + ">");
        var cols = "";
        cols += "<td><input type='checkbox' name='checkbox_valid'><input type='hidden' name='checkbox_valid1' value='off'><input type='hidden' name='purchase_item_id' value='" + purchase_item_id + "'><input type='hidden' name='item_key_value' value=" + table_index + "><input type='hidden' name='item_id' value=" + item_id + "><input type='hidden' name='item_type' value=" + item_type + "><input type='hidden' name='item_code' value='" + item_code + "'></td>";
        if (item_type == 'product' || item_type == 'product_inventory')
            cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";
        else
            cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";
        cols += "<td>" + "<input type='text' class='form-control form-fixer' name='item_description' value='" + item_description + "' readOnly>" + "</td>";
        // cols += "<td>" + "<input type='number' step='0.01' class='form-control form-fixer text-center' value='"+item_quantity+"' data-rule='quantity' name='item_quantity' min='1' max='"+ item_quantity +"' readonly>" + "</td>";
        cols += "<td>" + "<input type='number' class='float_number form-control form-fixer text-center' value='" + item_quantity + "' data-rule='quantity' name='item_quantity' readOnly min= '0' >" + "</td>";
        cols += "<td>" + "<span id='price_span'>" + "<input type='text' class='float_number form-control form-fixer text-right' name='item_price' value='" + precise_amount(item_price) + "' readOnly>" + "</span>" + "<span id='item_sub_total_lbl_" + table_index + "' class='pull-right' style='color:red; display:none' >" + item_price + "</span>" + "<input type='hidden' class='form-control form-fixer text-right' style='' value='" + item_price + "' name='item_sub_total'>" + "</td>";
       // cols += "<td>" + "<input type='hidden' name='item_discount_value' value='" + item_discount_value + "'>" + "<input type='hidden' name='item_discount_amount' value='" + purchase_item_discount_amount + "'>" + select_discount + "</td>";
        cols += "<td align='right'><input type='hidden' name='item_taxable_value' ><span id='item_taxable_value_" + table_index + "'>" + item_price + "</span></td>";
//tax area

        cols += "<td style='display:none'>" + "<input type='hidden' name='item_tax_percentage'><input type='hidden' name='item_tax_amount'><input type='hidden' name='item_igst' class='float_number form-control form-fixer text-center' value='" + item_igst + "' readOnly >" + "<input type='hidden' name='item_igst_amount' class='form-control form-fixer'>" + "<span id='item_igst_amount_lbl_" + table_index + "' class='pull-right' style='color:red; display:none;'></span>" + "</td>";
        cols += "<td>"+
            "<input type='hidden' name='item_tax_id' value='"+item_tax_id+"'>" +
            "<input type='hidden' name='item_tax_percentage' value='0'>" +
            "<input type='hidden' name='item_tax_amount' value='0'>" +
            "<input type='hidden' name='item_igst' value='" + item_igst + "'>" +
            "<input type='hidden' name='item_sgst' value='" + item_sgst + "'>" +
            "<input type='hidden' name='item_cgst' value='" + item_cgst + "'>" +
            select_tax + 
            "<span id='item_tax_lbl_" + table_index + "' class='pull-right' style='color:red;'>0.00</span>" + "</td>";
           
        cols += "<td>"+
                    "<input type='hidden' name='item_tax_cess_id' value='"+item_tax_cess_id+"'>" +
                    "<input type='hidden' name='item_tax_cess_percentage' value='"+precise_amount(item_tax_cess_percentage)+"'>" +
                    "<input type='hidden' name='item_tax_cess_amount' value='"+precise_amount(item_tax_cess_amount)+"'>" +
                    cess_select +
                    "<span id='item_tax_cess_lbl_" + table_index + "' class='pull-right' style='color:red;'>"+item_tax_cess_amount+"</span>" +
                    "</td>";
            
       cols += "<td style='display:none'>" + "<input type='hidden' name='item_cgst' value='" + item_cgst + "' class='float_number form-control form-fixer text-center' readOnly >" + "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer'>" + "<span id='item_cgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red; display:none;'></span>" + "</td>";
       cols += "<td style='display:none'>" + "<input type='hidden' name='item_sgst' value='" + item_sgst + "' class='float_number form-control form-fixer text-center' readOnly >" + "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer'>" + "<span id='item_sgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red; display:none;'></span>" + "</td>"

        //tax area 

        cols += "<td><input type='text' class='float_number form-control form-fixer text-right' name='item_grand_total' readOnly></td>";
        cols += "</tr>";

        newRow.append(cols);
        $("#purchase_table_body").append(newRow);
        // var table_data = JSON.stringify(data_item);
        // $('#table_data').val(table_data);
        table_index++;


        calculateRow(newRow);
        calculateDiscountTax(newRow);
        calculateGrandTotal_check();
    }
}

//calculate row
function calculateRow(row)
{
    var key = +row.index();
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_price = +row.find('input[name^="item_price"]').val();
    var item_quantity = +row.find('input[name^="item_quantity"]').val();
    var item_id = +row.find('input[name^="item_id"]').val();
    var item_type = row.find('input[name^="item_type"]').val();
    var item_code = row.find('input[name^="item_code"]').val();
    var checkbox_value = row.find('input[name^="checkbox_valid1"]').val();
    var purchase_item_id = row.find('input[name^="purchase_item_id"]').val();

    var item_sub_total = (item_price * item_quantity).toFixed(2);

    row.find('input[name^="item_sub_total"]').val(item_sub_total);
    row.find('#item_sub_total_lbl_' + table_index).text(item_sub_total);
    var item_description = row.find('input[name^="item_description"]').val();

    if (purchase_data[key] == null)
    {
        var temp = {
            "item_id": item_id,
            "item_type": item_type,
            "item_code": item_code,
        };
        purchase_data[key] = temp
    }
    purchase_data[key].item_description = item_description;
    purchase_data[key].item_quantity = item_quantity;
    purchase_data[key].item_price = item_price;
    purchase_data[key].item_key_value = table_index;
    purchase_data[key].item_sub_total = item_sub_total;
    purchase_data[key].checkbox_value = checkbox_value;
    purchase_data[key].purchase_item_id = purchase_item_id;
    var table_data = JSON.stringify(purchase_data);
    $('#table_data').val(table_data);
}
// calculate row
//calculate Discount

function calculateDiscountTax(row, data = 0)
{
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var discount;
    /*if (data == 0)
    {
        discount = +row.find('input[name^="item_discount_value"]').val();
    } else
    {
        discount = data
    }
     var discount_data = row.find('select[name^="item_discount"]').val();
    if (discount_data == null)
    {
        discount_data = "-";
    }
    var discount_split = discount_data.split("-");

    var item_discount = discount_split[0];

    */
        discount = 0;
        var item_discount = 0;

        var item_sub_total = +row.find('input[name^="item_sub_total"]').val();
        // var item_discount_amount = item_sub_total * discount / 100;
        var item_discount_amount = 0;
        var item_taxable_value = item_sub_total - item_discount_amount;
        var tax_data = row.find('select[name="item_tax"]').val();
        var cess_tax = row.find('select[name=item_tax_cess]').val();
       console.log(tax_data);
        if(tax_data != '' && typeof tax_data != 'undefined' ){
            var tax_split = tax_data.toString().split("-");
            var item_tax_id = +tax_split[0];
            var item_tax = +parseFloat(tax_split[1]);
            if (item_tax == "" || isNaN(item_tax)) {
                item_tax = 0;
            }
        }else{
            var item_tax_id = +'';
            var item_tax = +0;
        }

        if(cess_tax != '' && typeof cess_tax != 'undefined' ){
            var tax_cess_split = cess_tax.toString().split("-");
            var item_tax_cess_id = +tax_cess_split[0];
            var item_tax_cess = +parseFloat(tax_cess_split[1]); 
    }

    if (item_tax_cess == "" || isNaN(item_tax_cess)) {
        var item_tax_cess = 0;
        var item_tax_cess_id = '';
    }

        var item_tax_cess_amount = item_taxable_value * item_tax_cess;
        var item_tax_cess_amount = item_tax_cess_amount / 100;

        var item_tax_amount = (item_taxable_value * item_tax) / 100;       
       console.log(tax_data);
        var cgst_amount_percentage = parseFloat(settings_tax_percentage);
        var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
        var purchase_igst_amount = 0; 
        var purchase_cgst_amount = 0;
        var purchase_sgst_amount = 0;
       // console.log(branch_country_id);
       // console.log(branch_state_id);
            var txable_amount = parseFloat(item_tax_amount);
            if(branch_country_id == $('#billing_country').val()){
                if(branch_state_id == $('#billing_state').val()){
                    purchase_igst_amount = 0;
                    purchase_cgst_amount = (txable_amount * cgst_amount_percentage)/100;
                    purchase_sgst_amount = (txable_amount * sgst_amount_percentage)/100;
                    var igst = 0;
                    var sgst = item_tax / 2;
                    var cgst = item_tax / 2;
                }else{
                    purchase_igst_amount = txable_amount;
                    purchase_cgst_amount = 0;
                    purchase_sgst_amount = 0;
                    var igst = item_tax;
                    var sgst = 0;
                    var cgst = 0;
                }
            }else{
                
                    purchase_igst_amount = txable_amount;
                    purchase_cgst_amount = 0;
                    purchase_sgst_amount = 0;
                     var igst = item_tax;
                    var sgst = 0;
                    var cgst = 0;
                   
            }
        
    row.find('input[name^="item_igst"]').val(igst.toFixed(2));
    row.find('input[name^="item_cgst"]').val(cgst.toFixed(2));
    row.find('input[name^="item_sgst"]').val(sgst.toFixed(2));
    row.find('input[name^="item_tax_id"]').val(item_tax_id);
    
    row.find('#item_taxable_value_' + table_index).text(item_taxable_value.toFixed(2));
    var item_igst = +row.find('input[name^="item_igst"]').val();
    var item_cgst = +row.find('input[name^="item_cgst"]').val();
    var item_sgst = +row.find('input[name^="item_sgst"]').val();
    var item_tax_percentage = item_igst + item_cgst + item_sgst;
    var item_igst_amount = item_taxable_value * item_igst / 100;
    var item_cgst_amount = item_taxable_value * item_cgst / 100;
    var item_sgst_amount = item_taxable_value * item_sgst / 100;
    var item_tax_amount = item_igst_amount + item_cgst_amount + item_sgst_amount;
    // alert(discount);
    // return false;
    var item_grand_total = (item_taxable_value + item_tax_amount + item_tax_cess_amount).toFixed(2);
    row.find('input[name^="item_grand_total"]').val(global_round_off(item_grand_total));
    row.find('input[name^="item_taxable_value"]').val(item_taxable_value.toFixed(2));
    row.find('input[name^="item_igst_amount"]').val(item_igst_amount.toFixed(2));
    row.find('#item_igst_amount_lbl_' + table_index).text(item_igst_amount.toFixed(2));
    row.find('input[name^="item_cgst_amount"]').val(item_cgst_amount.toFixed(2));
    row.find('#item_cgst_amount_lbl_' + table_index).text(item_cgst_amount.toFixed(2));
    row.find('input[name^="item_sgst_amount"]').val(item_sgst_amount.toFixed(2));
    row.find('#item_sgst_amount_lbl_' + table_index).text(item_sgst_amount.toFixed(2));
    row.find('input[name^="item_discount_amount"]').val(item_discount_amount.toFixed(2));
    row.find('input[name^="item_tax_percentage"]').val(item_tax_percentage.toFixed(2));
    row.find('input[name^="item_tax_amount"]').val(item_tax_amount.toFixed(2));
    row.find('#item_tax_lbl_' + table_index).text(item_tax_amount.toFixed(2));
    row.find('input[name^="item_tax_cess_amount"]').val(item_tax_cess_amount.toFixed(2));
    row.find('input[name^="item_tax_cess_percentage"]').val(item_tax_cess.toFixed(2));
    row.find('input[name^="item_tax_cess_id"]').val(item_tax_cess_id);
    row.find('#item_tax_cess_lbl_' + table_index).text(item_tax_cess_amount.toFixed(2));

    var key = +row.index();
    purchase_data[key].item_discount_amount = item_discount_amount.toFixed(2);
    purchase_data[key].item_discount_value = discount;
    purchase_data[key].item_discount = item_discount;
    purchase_data[key].item_key_value = +table_index;
    purchase_data[key].item_igst = item_igst;
    purchase_data[key].item_igst_amount = item_igst_amount.toFixed(2);
    purchase_data[key].item_cgst = item_cgst;
    purchase_data[key].item_cgst_amount = item_cgst_amount.toFixed(2);
    purchase_data[key].item_sgst = item_sgst;
    purchase_data[key].item_tax_id = item_tax_id;
    purchase_data[key].item_sgst_amount = item_sgst_amount.toFixed(2);
    purchase_data[key].item_tax_percentage = item_tax_percentage.toFixed(2);
    purchase_data[key].item_tax_amount = item_tax_amount.toFixed(2);
    purchase_data[key].item_taxable_value = item_taxable_value.toFixed(2);    
    purchase_data[key].item_tax_cess_amount = item_tax_cess_amount.toFixed(2);
    purchase_data[key].item_tax_cess_percentage = item_tax_cess.toFixed(2);
    purchase_data[key].item_tax_cess_id = item_tax_cess_id;
    purchase_data[key].item_grand_total = global_round_off(item_grand_total);
    var table_data = JSON.stringify(purchase_data);
    $('#table_data').val(table_data)
}

//calculate grand total
function calculateGrandTotal()
{
    var sub_total = 0;
    var discount = 0;
    var tax = 0;
    var igst = 0;
    var cgst = 0;
    var sgst = 0;
    var taxable = 0;
    var grand_total = 0;
    var tax_cess = 0;
    var total_tax_amount = 0;

    $("#purchase_table_body").find('input[name^="item_sub_total"]').each(function ()
    {
        sub_total += +$(this).val()
    });
    $("#purchase_table_body").find('input[name^="item_taxable_value"]').each(function ()
    {
        taxable += +$(this).val()
    });
    $("#purchase_table_body").find('input[name^="item_discount_amount"]').each(function ()
    {
        discount += +$(this).val()
    });
    $("#purchase_table_body").find('input[name^="item_tax_amount"]').each(function ()
    {
        tax += +$(this).val()
    });
    $("#purchase_table_body").find('input[name^="item_igst_amount"]').each(function ()
    {
        igst += +$(this).val()
    });

    $("#purchase_table_body").find('input[name^="item_cgst_amount"]').each(function ()
    {
        cgst += +$(this).val()
    });
    $("#purchase_table_body").find('input[name^="item_sgst_amount"]').each(function ()
    {
        sgst += +$(this).val()
    });

    $("#purchase_table_body").find('input[name^="item_grand_total"]').each(function ()
    {
        grand_total += +$(this).val()
    });

    var total_other_amount = +$('#total_other_amount').val();
    var final_grand_total = grand_total + total_other_amount;

    $("#purchase_table_body").find('input[name^="item_tax_cess_amount"]').each(function ()
    {
        tax_cess += +$(this).val()
    });
   // total_tax_amount = tax + tax_cess;

    $('#total_sub_total').val(sub_total.toFixed(2));
    $('#total_taxable_amount').val(taxable.toFixed(2));
    $('#total_igst_amount').val(igst.toFixed(2));
    $('#total_cgst_amount').val(cgst.toFixed(2));
    $('#total_sgst_amount').val(sgst.toFixed(2));
    $('#total_discount_amount').val(discount.toFixed(2));
   // $('#total_tax_amount').val(total_tax_amount.toFixed(2));
    $('#total_grand_total').val(round_off(final_grand_total));
     $("#total_tax_cess_amount").val(precise_amount(tax_cess));

    $('#totalSubTotal').text(sub_total.toFixed(2));
    $('#totalDiscountAmount').text(discount.toFixed(2));
    $('#totalTaxAmount').text(tax.toFixed(2));
    $('#totalGrandTotal').text(round_off(final_grand_total));
     $("#totalTaxCessAmount").text(precise_amount(tax_cess));
     $("#totalIGSTAmount").text(precise_amount(igst));
     $("#totalSGSTAmount").text(precise_amount(sgst));
     $("#totalCGSTAmount").text(precise_amount(sgst));

}

//calculate grand total of selected row
function calculateGrandTotal_check()
{
    var parent;
    var sub_total = 0;
    var discount = 0;
    var tax = 0;
    var igst = 0;
    var cgst = 0;
    var sgst = 0;
    var taxable = 0;
    var grand_total = 0;
    var tax_cess = 0;
    var total_tax_amount = 0;
    $("#purchase_table_body").find('input[name^="item_sub_total"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            sub_total += +$(this).val();
        }
    });
    $("#purchase_table_body").find('input[name^="item_taxable_value"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            taxable += +$(this).val();
        }
    });
    $("#purchase_table_body").find('input[name^="item_discount_amount"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            discount += +$(this).val();
        }
    });
    $("#purchase_table_body").find('input[name^="item_tax_amount"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            tax += +$(this).val();
        }
    });
    $("#purchase_table_body").find('input[name^="item_igst_amount"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            igst += +$(this).val();
        }
    });

    $("#purchase_table_body").find('input[name^="item_cgst_amount"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            cgst += +$(this).val();
        }
    });
    $("#purchase_table_body").find('input[name^="item_sgst_amount"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            sgst += +$(this).val();
        }
    });

    $("#purchase_table_body").find('input[name^="item_grand_total"]').each(function ()
    {
        parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            grand_total += +$(this).val();
        }
    });

    $("#purchase_table_body").find('input[name^="item_tax_cess_amount"]').each(function ()
    {
       parent = $(this).closest('tr');
        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {
            tax_cess += +$(this).val();
        }

    });

    total_tax_amount = tax_cess + igst + cgst + sgst;

    var total_other_amount = +$('#total_other_amount').val();
    var final_grand_total = grand_total + total_other_amount;

    $('#total_sub_total').val(sub_total.toFixed(2));
    $('#total_taxable_amount').val(taxable.toFixed(2));
    $('#total_igst_amount').val(igst.toFixed(2));
    $('#total_cgst_amount').val(cgst.toFixed(2));
    $('#total_sgst_amount').val(sgst.toFixed(2));
    $('#total_discount_amount').val(discount.toFixed(2));
    $('#total_tax_amount').val(total_tax_amount.toFixed(2));
    $('#total_grand_total').val(round_off(final_grand_total));

    $('#totalSubTotal').text(sub_total.toFixed(2));
    $('#totalDiscountAmount').text(discount.toFixed(2));
    $('#totalTaxAmount').text(total_tax_amount.toFixed(2));
    $("#total_tax_cess_amount").val(precise_amount(tax_cess));



    $('#totalTaxAmount').text(tax.toFixed(2));
     if(sgst > 0){
            var lbl = 'SGST (+)';
            if($('#billing_state option:selected').attr('utgst') == '1'){                
                lbl = 'UTGST (+)';
            }            
            $('.totalSGSTAmount_tr').show();
            $('.totalSGSTAmount_tr').find('td:first').text(lbl);
            $("#totalSGSTAmount").text(precise_amount(sgst));
        }else{
            $('.totalSGSTAmount_tr').hide();
        }
        if(cgst > 0){
            $("#totalCGSTAmount").text(precise_amount(cgst));
            $('.totalCGSTAmount_tr').show();
        }else{
            $('.totalCGSTAmount_tr').hide();
        }
       
        if(igst > 0){
            $('.totalIGSTAmount_tr').show();
            $("#totalIGSTAmount").text(precise_amount(igst));
        }else{
            $('.totalIGSTAmount_tr').hide();
        }

        if(tax_cess > 0){
            $('.totalCessAmount_tr').show();
            $("#totalTaxCessAmount").text(precise_amount(tax_cess));
        }else{
            $('.totalCessAmount_tr').hide();
        }
        
    $('#totalGrandTotal').text(round_off(final_grand_total));
}

function change_gst()
{
    if (flag2 == 1)
    {
        $("#purchase_table_body").find('input[name^="item_igst"]').each(function ()
        {
            var row = $(this).closest('tr');
            var igst_amount = +row.find('input[name^="item_tax_percentage"]').val();

            row.find('input[name^="item_igst"]').val(igst_amount);
            // row.find('input[name^="item_igst"]').attr("readonly", false); 
            row.find('input[name^="item_cgst"]').val(0);
            // row.find('input[name^="item_cgst"]').attr("readonly", true); 
            row.find('input[name^="item_sgst"]').val(0);
            // row.find('input[name^="item_sgst"]').attr("readonly", true);  
            $(this).change();
        });
    } else if (flag2 == 2)
    {
        $("#purchase_table_body").find('input[name^="item_igst"]').each(function ()
        {
            var row = $(this).closest('tr');
            row.find('input[name^="item_igst"]').val(0);
            row.find('input[name^="item_igst"]').attr("readOnly", true);
            // var item_id=+row.find('input[name^="item_id"]').val();
            // var item_type=row.find('input[name^="item_type"]').val();

            // var igst_amount = item_gst[item_id+'-'+item_type].item_igst;
            // var cgst_amount = item_gst[item_id+'-'+item_type].item_cgst;
            // var sgst_amount = item_gst[item_id+'-'+item_type].item_sgst;
            var item_tax_percentage = +row.find('input[name^="item_tax_percentage"]').val();

            var cgst = sgst = (item_tax_percentage / 2).toFixed(2);

            row.find('input[name^="item_cgst"]').val(cgst);
            // row.find('input[name^="item_cgst"]').attr("readonly", false); 
            row.find('input[name^="item_sgst"]').val(sgst);
            // row.find('input[name^="item_sgst"]').attr("readonly", false); 

            $(this).change();
        });

    } else
    {
        var billing_state_id = $('#billing_state').val();

        $("#purchase_table_body").find('input[name^="item_igst"]').each(function ()
        {
            var row = $(this).closest('tr');


            var item_id = +row.find('input[name^="item_id"]').val();
            var item_type = row.find('input[name^="item_type"]').val();
            var item_tax_percentage = +row.find('input[name^="item_tax_percentage"]').val();

            var cgst = sgst = (item_tax_percentage / 2).toFixed(2);
            var igst = item_tax_percentage;

            if (branch_state_id == billing_state_id)
            {
                row.find('input[name^="item_igst"]').val(0);
                // row.find('input[name^="item_igst"]').attr("readonly", true);
                row.find('input[name^="item_cgst"]').val(cgst);
                // row.find('input[name^="item_cgst"]').attr("readonly", false); 
                row.find('input[name^="item_sgst"]').val(sgst);
                // row.find('input[name^="item_sgst"]').attr("readonly", false); 
            } else
            {
                row.find('input[name^="item_igst"]').val(igst);
                // row.find('input[name^="item_igst"]').attr("readonly", false);
                row.find('input[name^="item_cgst"]').val(0);
                // row.find('input[name^="item_cgst"]').attr("readonly", true); 
                row.find('input[name^="item_sgst"]').val(0);
                // row.find('input[name^="item_sgst"]').attr("readonly", true); 
            }

            $(this).change();
        });
    }
    flag2 = 0;
}

function select_checkbox()
{
    $('input:checkbox').on('click', function () {
        if ($(this).prop('checked'))
        {
            $(this).parent().nextAll().find('input[name^="item_quantity"]').prop('readOnly', false).css('background-color', '#FFFFFF');
            $(this).parent().nextAll().find('input[name^="item_description"]').prop('readOnly', false).css('background-color', '#FFFFFF');
            $(this).parent().nextAll().find('select[name^="item_tax"]').attr('disabled', false).css('background', '#FFFFFF');
             $(this).parent().nextAll().find('select[name^="item_tax_cess"]').attr('disabled', false).css('background', '#FFFFFF');
            $(this).parent().nextAll().find('input[name^="item_price"]').prop('readOnly', false).css('background-color', '#FFFFFF');
            var row = $(this).closest("tr");
            var checkbox_valid = row.find('input[name^="checkbox_valid"]').val();
            row.find('input[name^="checkbox_valid1"]').val('on');
            calculateRow(row);

            calculateDiscountTax($(this).closest("tr"));
            // calculateGrandTotal();
            calculateGrandTotal_check();
        } else
        {
            $(this).parent().nextAll().find('input[name^="item_quantity"]').prop('readOnly', true).css('background-color', '#E3E3E3');
            $(this).parent().nextAll().find('input[name^="item_description"]').prop('readOnly', true).css('background-color', '#E3E3E3');
            $(this).parent().nextAll().find('select[name^="item_tax"]').attr('disabled', 'disabled').css('background', '#E3E3E3');
            $(this).parent().nextAll().find('select[name^="item_tax_cess"]').attr('disabled', 'disabled').css('background', '#E3E3E3');
            $(this).parent().nextAll().find('input[name^="item_price"]').prop('readOnly', true).css('background-color', '#E3E3E3');
            var row = $(this).closest("tr");
            var checkbox_valid = row.find('input[name^="checkbox_valid"]').val();
            row.find('input[name^="checkbox_valid1"]').val('off');

            calculateRow(row);

            calculateDiscountTax($(this).closest("tr"));
            // calculateGrandTotal();
            calculateGrandTotal_check();
        }
    });
}

function invoice_number_count()
{

    var invoice_number = $('#invoice_number').val();
    var module_id = $("#module_id").val();

    var privilege = $("#privilege").val();
    var section_area = $("#section_area").val();


    $.ajax({
        url: base_url + 'general/check_date_reference',
        dataType: 'JSON',
        method: 'POST',
        data: {
            'invoice_number': invoice_number,
            'privilege': privilege,
            'module_id': module_id

        },
        success: function (result) {


            if (result[0]['num'] > 0)
            {
                $('#invoice_number').val("");
                $("#err_invoice_number").text(invoice_number + " already exists!");
                return false;
            } else
            {
                $("#err_invoice_number").text("");
            }

        }
    });
}
