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
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    if ($('#payment_mode').val() != "bank")
    {
        $('#hide').hide();
    }
    //date change
    $('#voucher_date').on('changeDate', function ()
    {
        var selected = $(this).val();
        var module_id = $("#module_id").val();
        var date_old = $("#voucher_date_old").val();
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
                            console.log(data);
                            var parsedJson = $.parseJSON(data);
                            var type = parsedJson.reference_no;
                            $('#voucher_number').val(type)
                        }
                    })
        } else
        {
            var old_reference = $('#voucher_number_old').val();
            $('#voucher_number').val(old_reference)
        }
    });
    //billing country changes
    $("#billing_country").change(function (event)
    {
        var table_data = $('#table_data').val();
        var billing_country = $('#billing_country').val();
        if (branch_country_id != billing_country) {
            flag2 = 1;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="0">Out of Country</option>');
              $('.gst_payable_div').hide();
        } else{
            flag2 = 2;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="">Select</option>');
            for (var i = 0; i < branch_state_list.length; i++)
            {
                $('#billing_state').append('<option value="' + branch_state_list[i].state_id + '" utgst="' + branch_state_list[i].is_utgst +'" >' + branch_state_list[i].state_name + '</option>');
            }
        }
        if (table_data != "")
        {
            change_gst();
        }
    });
    //billing state changes
    $("#billing_state").change(function (event)
    {
        var table_data = $('#table_data').val();
        var billing_state_id = $('#billing_state').val();
        if(billing_state_id == 0){
            $('.gst_payable_div').hide();
        }else{
           $('.gst_payable_div').show(); 
        }
        if (table_data != "")
        {
            change_gst();
        }
    });
    //billing state changes
    $("#customer").change(function (event)
    {
        var table_data = $('#table_data').val();
        var customer = $('#customer').val();
        var customer_split = customer.split('-');
        var customer_id = customer_split[0];
        var customer_country_id = customer_split[1];
        var customer_state_id = customer_split[2];
        $("#billing_country").select2().val(customer_country_id).trigger('change.select2');
        $("#billing_state").select2().val(customer_state_id).trigger('change.select2');
        var billing_country_id = $('#billing_country').val();
        if (billing_country_id != branch_country_id)
        {
            $('#billing_country').change();
        } else
        {
            if (flag2 == 1)
            {
                $('#billing_country').change();
                $("#billing_state").select2().val(customer_state_id).trigger('change.select2');
            } else
            {
                if (table_data != "")
                {
                    change_gst();
                }
            }
        }
        //get refund items
        $('#reference_number').html('<option value="">Select</option>');
        if (customer != "") {
            $.ajax({
                url: base_url + 'refund_voucher/get_advance_invoice',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'customer_id': customer_id,
                },
                success: function (data)
                {
                    for (i = 0; i < data.length; i++)
                    {
                        $('#reference_number').append('<option value="' + data[i].voucher_number + '">' + data[i].voucher_number + '</option>');
                    }
                }
            });
        }
    });
    $("#reference_number").change(function (event)
    {
        var advance_voucher_number = $('#reference_number').val();
        $("#sales_table_body").empty();
        if (advance_voucher_number != "")
        {
            $('#receipt_amount_div').show();
            $('#err_receipt_amount').text('');
            $('#err_receipt_no').text('');
            $.ajax({
                url: base_url + 'refund_voucher/get_advance_items',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'invoice': $('#reference_number').val(),
                },
                success: function (data) {
                    $("#receipt_amount").val(precise_amount(data['receipt_amount'][0].receipt_amount));
                    $("#advance_voucher_number").val(advance_voucher_number);
                    $("#advance_voucher_id").val(data['advance_id']);
                    /*$('#currency_id').html('');
                    var option = '<option value="' + data['currency'][0].currency_id + '">' + data['currency'][0].currency_name + '</option>';
                    $('#currency_id').html(option);*/
                    $('#currency_id').val(data['currency'][0].currency_id).trigger('change');
                    var branch_country_id = data['branch_country_id'];
                        var branch_state_id = data['branch_state_id'];
                        var branch_id = data['branch_id'];
                    for (i = 0; i < data['items'].length; i++)
                    {
                        var item_id = data['items'][i].item_id;
                        var item_type = data['items'][i].item_type;
                        
                        if (item_type == 'service'){
                            var item_code = data['items'][i].service_code;
                            var item_name = data['items'][i].service_name;
                            var item_hsn_sac_code = data['items'][i].service_hsn_sac_code;
                            var item_details = data['items'][i].service_details;  
                        }else if (item_type == 'advance'){
                            var item_code = data['items'][i].product_code;
                            var item_name = data['items'][i].product_name;
                            var item_hsn_sac_code = data['items'][i].product_hsn_sac_code;
                            var item_details = data['items'][i].product_details;
                        }else{
                            var item_code = data['items'][i].product_code;
                            var item_name = data['items'][i].product_name;
                            var item_hsn_sac_code = data['items'][i].product_hsn_sac_code;
                            var item_details = data['items'][i].product_details;
                        }
                        var item_price = data['items'][i].item_price;
                        var item_tax_id = data['items'][i].item_gst_id;
                        var item_cess_id = data['items'][i].item_cess_id;
                        var item_igst = data['items'][i].item_igst_percentage;
                        var item_cgst = data['items'][i].item_cgst_percentage;
                        var item_sgst = data['items'][i].item_sgst_percentage;
                        var item_tax_cess_percentage =  data['items'][i].item_cess_percentage;
                        var item_tax_cess_amount =  data['items'][i].item_cess_amount;
                        var voucher_item_description = data['items'][i].item_description;
                        var voucher_item_grand_total = data['items'][i].item_grand_total;
                        var voucher_item_sub_total = data['items'][i].item_sub_total;
                        var voucher_item_igst_amount = data['items'][i].item_igst_amount;
                        var voucher_item_igst_percentage = data['items'][i].item_igst_percentage;
                        var voucher_item_sgst_amount = data['items'][i].item_sgst_amount;
                        var voucher_item_sgst_percentage = data['items'][i].item_sgst_percentage;
                        var voucher_item_cgst_amount = data['items'][i].item_cgst_amount;
                        var voucher_item_cgst_percentage = data['items'][i].item_cgst_percentage;
                        var voucher_item_tax_amount = data['items'][i].item_tax_amount;
                        var voucher_item_tax_percentage = data['items'][i].item_tax_percentage;
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
                        if (billing_state_id == branch_state_id)
                        {
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
                            var item_igst = 0;
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
                            "item_details": item_details,
                            "item_price": item_price,
                            "item_tax_id": item_tax_id,
                            "item_igst": item_igst,
                            "item_cgst": item_cgst,
                            "item_sgst": item_sgst,
                            "item_key_value": table_index
                        };
                        var select_discount = "";
                        select_discount += '<div class="form-group">';
                        select_discount += '<select class="form-control open_discount form-fixer select2" name="item_discount">';
                        select_discount += '<option value="">Select</option>';
                        // for (a = 0; a < data.discount.length; a++)
                        // {
                        //    select_discount += '<option value="' + data.discount[a].discount_id+'-'+data.discount[a].discount_value + '">' + data.discount[a].discount_value + '%' + '</option>'
                        // }
                        // select_discount += '</select></div>';
                        var newRow = $("<tr id=" + table_index + ">");
                        var cols = "";
                        if (item_type == 'product' || item_type == 'product_inventory')
                            cols += "<td>" + item_name + "<br>(P) HSN/SAC:" + item_hsn_sac_code + "</td>";
                        else
                        cols += "<td>" + item_name + "<br>(S) HSN/SAC:" + item_hsn_sac_code + "</td>";
                        cols += "<td><input type='hidden' name='item_key_value'  value=" + table_index + "><input type='hidden' name='item_id' value=" + item_id + "><input type='hidden' name='item_type' value=" + item_type + "><input type='hidden' name='item_code' value='" + item_code + "'>" + "<input type='text' class='form-control form-fixer' name='item_description'value='" + voucher_item_description + "' >" + "</td>";
                        cols += "<td hidden='true'>" + "<input type='text' class='float_number form-control form-fixer text-center' value='1' data-rule='quantity' name='item_quantity' min='1'>" + "</td>";
                        cols += "<td>" + "<span id='price_span'>" + "<input type='text' class='float_number form-control form-fixer text-right' name='item_price' value='" + precise_amount(voucher_item_sub_total) + "'>" + "</span>" + "<span id='item_sub_total_lbl_" + table_index + "' class='pull-right' style='color:red;'>" + item_price + "</span>" + "<input type='hidden' class='form-control form-fixer text-right' style='' value='" + item_price + "' name='item_sub_total'>" + "</td>";
                        cols += "<td style='display:none;'>" + "<input type='hidden' name='item_discount_value' value='0'>" + "<input type='hidden' name='item_discount_amount' value='0'>" + select_discount + "</td>";
                        cols += "<td align='right' hidden='true'><input type='hidden' name='item_taxable_value' value='" + precise_amount(voucher_item_sub_total) + "' ><span id='item_taxable_value_" + table_index + "'>" + precise_amount(voucher_item_sub_total) + "</span></td>";
//tax area
                        
                         var select_tax = "";
                        select_tax += '<div class="form-group" style="margin-bottom:0px !important;">';  
                        select_tax +=
                            '<select class="form-control open_tax select2" name="item_tax">';
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
                         cols += "<td>";
                        var cess_select = "";
        
                cess_select +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
                cess_select +=
                '<select class="form-control open_tax form-fixer select2" name="item_tax_cess">';
                cess_select += '<option value="">Select</option>';
                for (a = 0; a < data.tax.length; a++) {
                if (item_cess_id == data.tax[a].tax_id) {
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
                     cols +=
                    "<input type='hidden' name='item_tax_id' value='"+item_tax_id+"'>" +
                    "<input type='hidden' name='item_tax_percentage' value='" + precise_amount(voucher_item_tax_percentage) + "'>" +
                    "<input type='hidden' name='item_tax_amount' value='"+ precise_amount(voucher_item_tax_amount) +"'>" +
                    "<input type='hidden' name='item_igst' value='" + precise_amount(voucher_item_igst_percentage) + "'>" +
                    "<input type='hidden' name='item_sgst' value='" + precise_amount(voucher_item_sgst_percentage) + "'>" +
                    "<input type='hidden' name='item_cgst' value='" + precise_amount(voucher_item_cgst_percentage) + "'>" +
                    "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer' value='" + precise_amount(voucher_item_cgst_amount) + "'>"+
                    "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer' value='" + precise_amount(voucher_item_sgst_amount) + "'>" +
                    "<input type='hidden' name='item_igst_amount' class='form-control form-fixer' value='" + precise_amount(voucher_item_igst_amount) + "'>" +
                    select_tax +
                    "<span id='item_tax_lbl_" +
                    table_index +
                    "' class='pull-right' style='color:red;'>"+precise_amount(voucher_item_tax_amount)+"</span>" +
                    "</td>";
                    cols += "<td>"+
                    "<input type='hidden' name='item_tax_cess_id' value='"+item_cess_id+"'>" +
                    "<input type='hidden' name='item_tax_cess_percentage' value='"+precise_amount(item_tax_cess_percentage)+"'>" +
                    "<input type='hidden' name='item_tax_cess_amount' value='"+precise_amount(item_tax_cess_amount)+"'>" +
                    cess_select +
                    "<span id='item_tax_cess_lbl_" +
                     table_index +
                    "' class='pull-right' style='color:red;'>"+item_tax_cess_amount+"</span>" +
                    "</td>";
                       /* if (igst_required == "no")
                        {
                            cols += "<td>" + "<input type='hidden' name='item_tax_percentage' value='" + voucher_item_tax_percentage + "'><input type='hidden' name='item_tax_amount' value='" + voucher_item_tax_amount + "'><input type='text' name='item_igst' class='float_number form-control form-fixer text-center' value='" + voucher_item_igst_percentage + "' readonly >" + "<input type='hidden' name='item_igst_amount' class='form-control form-fixer' value='" + voucher_item_igst_amount + "'>" + "<span id='item_igst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
                            cols += "<td>" + "<input type='text' name='item_cgst' value='" + voucher_item_cgst_percentage + "' class='float_number form-control form-fixer text-center' >" + "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer' value='" + voucher_item_cgst_amount + "'>" + "<span id='item_cgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
                            cols += "<td>" + "<input type='text' name='item_sgst' value='" + voucher_item_sgst_percentage + "' class='float_number form-control form-fixer text-center' >" + "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer' value='" + voucher_item_sgst_amount + "'>" + "<span id='item_sgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>"
                        } else
                        {
                            cols += "<td>" + "<input type='hidden' name='item_tax_percentage' value='" + voucher_item_tax_percentage + "'><input type='hidden' name='item_tax_amount' value='" + voucher_item_tax_amount + "'><input type='text' name='item_igst' class='float_number form-control form-fixer text-center' value='" + voucher_item_igst_percentage + "' >" + "<input type='hidden' name='item_igst_amount' class='form-control form-fixer' value='" + voucher_item_igst_amount + "'>" + "<span id='item_igst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
                            cols += "<td>" + "<input type='text' name='item_cgst' value='" + voucher_item_cgst_percentage + "' class='float_number form-control form-fixer text-center' readonly >" + "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer' value='" + voucher_item_cgst_amount + "'>" + "<span id='item_cgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
                            cols += "<td>" + "<input type='text' name='item_sgst' value='" + voucher_item_sgst_percentage + "' class='float_number form-control form-fixer text-center' readonly >" + "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer' value='" + voucher_item_sgst_amount + "'>" + "<span id='item_sgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>"
                        }*/
                        //tax area 
                        cols += "<td><input type='text' class='float_number form-control form-fixer text-right' name='item_grand_total' value='" + voucher_item_grand_total + "'></td>";
                        cols += "</tr>";
                        newRow.append(cols);
                        //$("table.product_table").append(cols);
                        $("table.sales_table").append(newRow);
                        var table_data = JSON.stringify(sales_data);
                        $('#table_data1').val(table_data);
                        calculateRow(newRow);
                        calculateDiscountTax(newRow);
                    }
                    calculateGrandTotal();
                    call_css();
                }
            });
        } else
        {
            $('#receipt_amount_div').hide();
            $('#receipt_amount').val("");
            $('#err_receipt_no').text('Select Advance Voucher No.');
        }
    });
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
    // get items to table starts
    $('#input_sales_code').autoComplete(
            {
                minChars: 1,
                cache: false,
                source: function (term, suggest)
                {
                    term = term.toLowerCase();
                    var isnum = /^\d+$/.test(term);
                    $.ajax(
                            {
                                url: base_url + "sales/get_sales_suggestions/" + term,
                                type: "GET",
                                dataType: "json",
                                success: function (data)
                                {
                                    var suggestions = [];
                                    for (var i = 0; i < data.length; ++i) {
                                        suggestions.push(data[i].item_code + ' ' + data[i].item_name + ' ' + data[i].product_batch);
                                        mapping[data[i].item_code] = data[i].item_id + '-' + data[i].item_type;
                                    }
                                    if (i == 1 && term.length > 7 && isnum == true)
                                    {
                                        $.ajax(
                                                {
                                                    url: base_url + 'sales/get_table_items/' + mapping[data[0].item_code],
                                                    type: "GET",
                                                    dataType: "JSON",
                                                    success: function (data1)
                                                    {
                                                        add_row(data1);
                                                        call_css();
                                                        $('#input_sales_code').val('')
                                                    }
                                                });
                                        suggest('');
                                    } else
                                    {
                                        suggest(suggestions);
                                    }
                                    suggest(suggestions);
                                }
                            })
                },
                onSelect: function (event, ui)
                {
                    var code = ui.split(' ');
                    $.ajax(
                            {
                                url: base_url + 'sales/get_table_items/' + mapping[code[0]],
                                type: "GET",
                                dataType: "JSON",
                                success: function (data)
                                {
                                    add_row(data);
                                    $('#input_sales_code').val('');
                                    call_css();
                                }
                            })
                }
            });
    // get items to table ends
    //delete rows 
    $("#sales_table_body").on("click", "a.deleteRow", function (event)
    {
        deleteRow($(this).closest("tr"));
        $(this).closest("tr").remove();
        calculateGrandTotal();
    });
    // changes in rows
    $("#sales_table_body").on("change blur", 'input[name^="item_price"],input[name^="item_description"],input[name^="item_quantity"],input[name^="item_igst"],input[name^="item_cgst"],input[name^="item_sgst"],select[name^="item_tax"],select[name^="item_tax_cess"]', function (event)
    {
        calculateRow($(this).closest("tr"));
        calculateDiscountTax($(this).closest("tr"));
        calculateGrandTotal();
    });
    //split tax equally
    if (common_settings_tax_split == "yes")
    {
        $("#sales_table_body").on("change", 'input[name^="item_cgst"]', function (event)
        {
            var row = $(this).closest("tr");
            var cgst = +row.find('input[name^="item_cgst"]').val();
            var sgst = +row.find('input[name^="item_sgst"]').val();
            row.find('input[name^="item_sgst"]').val(cgst);
            if (cgst != sgst)
            {
                row.find('input[name^="item_sgst"]').change();
            }
        });
        $("#sales_table_body").on("change", 'input[name^="item_sgst"]', function (event)
        {
            var row = $(this).closest("tr");
            var sgst = +row.find('input[name^="item_sgst"]').val();
            var cgst = +row.find('input[name^="item_cgst"]').val();
            row.find('input[name^="item_cgst"]').val(sgst);
            if (cgst != sgst)
            {
                row.find('input[name^="item_cgst"]').change();
            }
        });
    }
    //reverse calculations
    $("#sales_table_body").on("change", 'input[name^="item_grand_total"]', function (event)
    {
        row = $(this).closest("tr");
        var item_grand_total = + parseFloat(row.find('input[name^="item_grand_total"]').val());
        var item_igst = + parseFloat(row.find('input[name^="item_igst"]').val());
        var item_cgst = + parseFloat(row.find('input[name^="item_cgst"]').val());
        var item_sgst = + parseFloat(row.find('input[name^="item_sgst"]').val());
        var tax = item_igst + item_cgst + item_sgst;
        //var item_discount_amount = +row.find('input[name^="item_discount_amount"]').val();
        item_tax_cess_percentage = +parseFloat( row.find('input[name^="item_tax_cess_percentage"]').val());
        var item_discount_amount = 0;
        var item_quantity = +row.find('input[name^="item_quantity"]').val();
        var item_taxable_value = (+item_grand_total * 100) / (100 + (+tax + +item_tax_cess_percentage));
        var item_sub_total = item_taxable_value - item_discount_amount;
        var item_price = item_sub_total / item_quantity;
        row.find('input[name^="item_price"]').val(item_price.toFixed(2));
        calculateRow($(this).closest("tr"));
        calculateDiscountTax($(this).closest("tr"));
        calculateGrandTotal();
        if (common_settings_round_off == 'no')
        {
            row.find('input[name^="item_grand_total"]').val(item_grand_total.toFixed(2));
        }
    });
    $("#sales_table_body").on("change", 'select[name^="item_discount"]', function (event)
    {
        var row = $(this).closest("tr");
        var discount = row.find('select[name^="item_discount"]').val();
        if (discount != "")
        {
            var discount_data = discount.split("-");
            row.find('input[name^="item_discount_value"]').val(discount_data[1]);
            calculateDiscountTax(row, discount_data[1]);
            calculateGrandTotal();
        } else
        {
            row.find('input[name^="item_discount_value"]').val('0');
            calculateDiscountTax(row, 0);
            calculateGrandTotal();
        }
    });
});// main function
//function to add new row to datatable
function add_row(data)
{
    var flag = 0;
    var item_id = data.item_id;
    var item_type = data.item_type;
    var branch_country_id = data.branch_country_id;
    var branch_state_id = data.branch_state_id;
    var branch_id = data.branch_id;
    if (item_type == 'service')
    {
        var item_code = data[0].service_code;
        var item_name = data[0].service_name;
        var item_hsn_sac_code = data[0].service_hsn_sac_code;
        var item_details = data[0].service_details;
        var item_price = data[0].service_price;
        var item_tax_id = data[0].service_tax_id;
        var item_igst = data[0].service_igst;
        var item_cgst = data[0].service_cgst;
        var item_sgst = data[0].service_sgst;
    } else
    {
        var item_code = data[0].product_code;
        var item_name = data[0].product_name;
        var item_hsn_sac_code = data[0].product_hsn_sac_code;
        var item_details = data[0].product_details;
        var item_price = data[0].product_price;
        var item_tax_id = data[0].product_tax_id;
        var item_igst = data[0].product_igst;
        var item_cgst = data[0].product_cgst;
        var item_sgst = data[0].product_sgst;
    }
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
    if (billing_state_id == branch_state_id)
    {
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
        var item_igst = 0;
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
        "item_details": item_details,
        "item_price": item_price,
        "item_tax_id": item_tax_id,
        "item_igst": item_igst,
        "item_cgst": item_cgst,
        "item_sgst": item_sgst,
        "item_key_value": table_index
    };
    var select_discount = "";
    select_discount += '<div class="form-group">';
    select_discount += '<select class="form-control open_discount form-fixer select2" id="item_discount" name="item_discount">';
    select_discount += '<option value="">Select</option>';
    for (a = 0; a < data.discount.length; a++)
    {
        select_discount += '<option value="' + data.discount[a].discount_id + '-' + data.discount[a].discount_value + '">' + data.discount[a].discount_value + '%' + '</option>'
    }
    select_discount += '</select></div>';
    var newRow = $("<tr id=" + table_index + ">");
    var cols = "";
    cols += "<td><a class='deleteRow'> <img src='" + base_url + "assets/images/bin_close.png' /> </a><input type='hidden' name='item_key_value'  value=" + table_index + "><input type='hidden' name='item_id' value=" + item_id + "><input type='hidden' name='item_type' value=" + item_type + "><input type='hidden' name='item_code' value='" + item_code + "'></td>";
    if (item_type == 'product')
        cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";
    else
        cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";
    cols += "<td>" + "<input type='text' class='form-control form-fixer' name='item_description' >" + "</td>";
    cols += "<td hidden='true'>" + "<input type='text' class='float_number form-control form-fixer text-center' value='1' data-rule='quantity' name='item_quantity'>" + "</td>";
    cols += "<td>" + "<span id='price_span'>" + "<input type='text' class='float_number form-control form-fixer text-right' name='item_price' value='" + item_price + "'>" + "</span>" + "<span id='item_sub_total_lbl_" + table_index + "' class='pull-right' style='color:red;'>" + item_price + "</span>" + "<input type='hidden' class='form-control form-fixer text-right' style='' value='" + item_price + "' name='item_sub_total'>" + "</td>";
    cols += "<td style='display:none;'>" + "<input type='hidden' name='item_discount_value' value='0'>" + "<input type='hidden' name='item_discount_amount' value='0'>" + select_discount + "</td>";
    cols += "<td align='right' hidden='true'><input type='hidden' name='item_taxable_value' value='" + item_price + "' ><span id='item_taxable_value_" + table_index + "'>" + item_price + "</span></td>";
//tax area
    if (igst_required == "no")
    {
        cols += "<td>" + "<input type='hidden' name='item_tax_percentage'><input type='hidden' name='item_tax_amount'><input type='text' name='item_igst' class='float_number form-control form-fixer text-center' value='" + item_igst + "' readonly >" + "<input type='hidden' name='item_igst_amount' class='form-control form-fixer'>" + "<span id='item_igst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
        cols += "<td>" + "<input type='text' name='item_cgst' value='" + item_cgst + "' class='float_number form-control form-fixer text-center' >" + "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer'>" + "<span id='item_cgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
        cols += "<td>" + "<input type='text' name='item_sgst' value='" + item_sgst + "' class='float_number form-control form-fixer text-center' >" + "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer'>" + "<span id='item_sgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>"
    } else
    {
        cols += "<td>" + "<input type='hidden' name='item_tax_percentage'><input type='hidden' name='item_tax_amount'><input type='text' name='item_igst' class='float_number form-control form-fixer text-center' value='" + item_igst + "' >" + "<input type='hidden' name='item_igst_amount' class='form-control form-fixer'>" + "<span id='item_igst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
        cols += "<td>" + "<input type='text' name='item_cgst' value='" + item_cgst + "' class='float_number form-control form-fixer text-center' readonly >" + "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer'>" + "<span id='item_cgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";
        cols += "<td>" + "<input type='text' name='item_sgst' value='" + item_sgst + "' class='float_number form-control form-fixer text-center' readonly >" + "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer'>" + "<span id='item_sgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>"
    }
    //tax area 
    cols += "<td><input type='text' class='float_number form-control form-fixer text-right' name='item_grand_total'></td>";
    cols += "</tr>";
    newRow.append(cols);
    $("#sales_table_body").append(newRow);
    // var table_data = JSON.stringify(data_item);
    // $('#table_data').val(table_data);
    table_index++;
    calculateRow(newRow);
    calculateDiscountTax(newRow);
    calculateGrandTotal();
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
    var item_sub_total = (item_price * item_quantity).toFixed(2);
    row.find('input[name^="item_sub_total"]').val(item_sub_total);
    row.find('#item_sub_total_lbl_' + table_index).text(item_sub_total);
    var item_description = row.find('input[name^="item_description"]').val();
    if (sales_data[key] == null)
    {
        var temp = {
            "item_id": item_id,
            "item_type": item_type,
            "item_code": item_code,
            "item_price": item_price,
            "item_key_value": table_index,
            "item_sub_total": item_sub_total,
        };
        sales_data[key] = temp
    }
    sales_data[key].item_description = item_description;
    sales_data[key].item_quantity = item_quantity;
    sales_data[key].item_price = item_price;
    sales_data[key].item_key_value = table_index;
    sales_data[key].item_sub_total = item_sub_total;
    var table_data = JSON.stringify(sales_data);
    $('#table_data').val(table_data);
}
// calculate row
//calculate Discount
function calculateDiscountTax(row, data = 0)
{
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var discount;
    if (data == 0)
    {
        discount = +row.find('input[name^="item_discount_value"]').val();
    } else
    {
        discount = data
    }
    var discount_data = row.find('select[name^="item_discount"]').val();
    var discount_split = discount_data.split("-");
    var item_discount = discount_split[0];
    var item_sub_total = +row.find('input[name^="item_sub_total"]').val();
    var item_discount_amount = item_sub_total * discount / 100;
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
            if (item_tax == "" || isNaN(item_tax)) {
                item_tax = 0;
                var item_tax_id = '';
            }
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
        var cgst_amount_percentage = parseFloat(settings_tax_percentage);
        var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
        var item_igst_amount = 0; 
        var item_cgst_amount = 0;
        var item_sgst_amount = 0;
       // console.log(branch_country_id);
       // console.log(branch_state_id);
            var txable_amount = parseFloat(item_tax_amount);
            if(branch_country_id == $('#billing_country').val()){
                if(branch_state_id == $('#billing_state').val()){
                    item_igst_amount = 0;
                    item_cgst_amount = (txable_amount * cgst_amount_percentage)/100;
                    item_sgst_amount = (txable_amount * sgst_amount_percentage)/100;
                    var igst = 0;
                    var sgst = item_tax / 2;
                    var cgst = item_tax / 2;
                }else{
                    item_igst_amount = txable_amount;
                    item_cgst_amount = 0;
                    item_sgst_amount = 0;
                    var igst = item_tax;
                    var sgst = 0;
                    var cgst = 0;
                }
            }else{
                
                    item_igst_amount = txable_amount;
                    item_cgst_amount = 0;
                    item_sgst_amount = 0;
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
    row.find('input[name^="item_tax_cess_amount"]').val(item_tax_cess_amount.toFixed(2));
    row.find('input[name^="item_tax_cess_percentage"]').val(item_tax_cess.toFixed(2));
    row.find('input[name^="item_tax_cess_id"]').val(item_tax_cess_id);
    row.find('#item_tax_cess_lbl_' + table_index).text(item_tax_cess_amount.toFixed(2));
    
    
    row.find('#item_tax_lbl_' + table_index).text(item_tax_amount.toFixed(2));
    var key = +row.index();
    sales_data[key].item_discount_amount = item_discount_amount.toFixed(2);
    sales_data[key].item_discount_value = discount;
    sales_data[key].item_discount = item_discount;
    sales_data[key].item_key_value = +table_index;
    sales_data[key].item_igst = item_igst;
    sales_data[key].item_igst_amount = item_igst_amount.toFixed(2);
    sales_data[key].item_cgst = item_cgst;
    sales_data[key].item_cgst_amount = item_cgst_amount.toFixed(2);
    sales_data[key].item_sgst = item_sgst;
    sales_data[key].item_sgst_amount = item_sgst_amount.toFixed(2);
    sales_data[key].item_tax_percentage = item_tax_percentage.toFixed(2);
    sales_data[key].item_tax_id = item_tax_id;
    sales_data[key].item_tax_amount = item_tax_amount.toFixed(2);
    sales_data[key].item_taxable_value = item_taxable_value.toFixed(2);
    sales_data[key].item_tax_cess_amount = item_tax_cess_amount.toFixed(2);
    sales_data[key].item_tax_cess_percentage = item_tax_cess.toFixed(2);
    sales_data[key].item_tax_cess_id = item_tax_cess_id;
    sales_data[key].item_grand_total = global_round_off(item_grand_total);
    var table_data = JSON.stringify(sales_data);
    $('#table_data').val(table_data)
}
//calculate grand total
function calculateGrandTotal(){
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
    $("#sales_table_body").find('input[name^="item_sub_total"]').each(function ()
    {
        sub_total += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_taxable_value"]').each(function ()
    {
        taxable += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_discount_amount"]').each(function ()
    {
        discount += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_tax_amount"]').each(function ()
    {
        tax += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_igst_amount"]').each(function ()
    {
        igst += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_cgst_amount"]').each(function ()
    {
        cgst += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_sgst_amount"]').each(function ()
    {
        sgst += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_grand_total"]').each(function ()
    {
        grand_total += +$(this).val()
    });
    $("#sales_table_body").find('input[name^="item_tax_cess_amount"]').each(function ()
    {
        tax_cess += +$(this).val()
    });
    total_tax_amount = tax + tax_cess;
    $('#total_sub_total').val(sub_total.toFixed(2));
    $('#total_taxable_amount').val(taxable.toFixed(2));
    $('#total_igst_amount').val(igst.toFixed(2));
    $('#total_cgst_amount').val(cgst.toFixed(2));
    $('#total_sgst_amount').val(sgst.toFixed(2));
    $('#total_discount_amount').val(discount.toFixed(2));
    $('#total_tax_amount').val(total_tax_amount.toFixed(2));    
    $('#total_grand_total').val(round_off(grand_total));
    $("#total_tax_cess_amount").val(precise_amount(tax_cess));
    
    $('#totalSubTotal').text(sub_total.toFixed(2));
    $('#totalDiscountAmount').text(discount.toFixed(2));
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
    $('#totalGrandTotal').text(round_off(grand_total));
}
// Delete function
function deleteRow(row)
{
    var table_index = +row.find('input[name^="item_key_value"]').val();
    for (var i = 0; i < sales_data.length; i++)
    {
        if (sales_data[i] != null && sales_data[i].item_key_value == table_index)
        {
            sales_data.splice(i, 1)
        }
    }
    var table_data = JSON.stringify(sales_data);
    $('#table_data').val(table_data)
}
function change_gst()
{
    if (flag2 == 1)
    {
        $("#sales_table_body").find('input[name^="item_igst"]').each(function ()
        {
            var row = $(this).closest('tr');
            row.find('input[name^="item_igst"]').val(0);
            row.find('input[name^="item_igst"]').attr("readonly", false);
            row.find('input[name^="item_cgst"]').val(0);
            row.find('input[name^="item_cgst"]').attr("readonly", true);
            row.find('input[name^="item_sgst"]').val(0);
            row.find('input[name^="item_sgst"]').attr("readonly", true);
            $(this).change();
        });
    } else if (flag2 == 2)
    {
        $("#sales_table_body").find('input[name^="item_igst"]').each(function ()
        {
            var row = $(this).closest('tr');
            row.find('input[name^="item_igst"]').val(0);
            row.find('input[name^="item_igst"]').attr("readonly", true);
            var item_id = +row.find('input[name^="item_id"]').val();
            var item_type = row.find('input[name^="item_type"]').val();
            var igst_amount = item_gst[item_id + '-' + item_type].item_igst;
            var cgst_amount = item_gst[item_id + '-' + item_type].item_cgst;
            var sgst_amount = item_gst[item_id + '-' + item_type].item_sgst;
            row.find('input[name^="item_cgst"]').val(cgst_amount);
            row.find('input[name^="item_cgst"]').attr("readonly", false);
            row.find('input[name^="item_sgst"]').val(sgst_amount);
            row.find('input[name^="item_sgst"]').attr("readonly", false);
            $(this).change();
        });
    } else
    {
        var billing_state_id = $('#billing_state').val();
        $("#sales_table_body").find('input[name^="item_igst"]').each(function ()
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
                row.find('input[name^="item_igst"]').attr("readonly", true);
                row.find('input[name^="item_cgst"]').val(cgst);
                row.find('input[name^="item_cgst"]').attr("readonly", false);
                row.find('input[name^="item_sgst"]').val(sgst);
                row.find('input[name^="item_sgst"]').attr("readonly", false);
            } else
            {
                row.find('input[name^="item_igst"]').val(igst);
                row.find('input[name^="item_igst"]').attr("readonly", false);
                row.find('input[name^="item_cgst"]').val(0);
                row.find('input[name^="item_cgst"]').attr("readonly", true);
                row.find('input[name^="item_sgst"]').val(0);
                row.find('input[name^="item_sgst"]').attr("readonly", true);
            }
            $(this).change();
        });
    }
    flag2 = 0;
}
