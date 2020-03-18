var mapping = {};



if (typeof count_data === 'undefined' || count_data === null)

{

    var count_data = 0;

}

var table_index = count_data;

// var branch_country_id = $('#branch_country_id').val();

// var branch_state_id = $('#branch_state_id').val();

var flag2 = 0;

$(document).ready(function ()

{

    $(window).keydown(function (event) {

        if (event.keyCode == 13) {

            event.preventDefault();

            return false;

        }

    });

    $('#input_sales_code').change(function () {

        $('#input_sales_code').trigger('click');

    });

    // $('#customer').change(function () 

    // {

    //     if ($(this).find('option:selected').text() != 'Select') 

    //     {

    //         $('#input_sales_code').prop('disabled', false);

    //         $('.search_sales_code').show();

    //         $('#err_sales_code').text('');

    //     } 

    //     else 

    //     {

    //         $('.search_sales_code').hide();

    //         $('#input_sales_code').prop('disabled', true);

    //         $('#err_sales_code').text('Please select the customer to do sales.');

    //         $('#err_product').text('');

    //     }

    //     $('#myForm input').change();

    // });



    //date change

    $('#invoice_date').on('changeDate', function ()

    {

        var selected = $(this).val();

        var module_id = $("#module_id").val();

        var date_old = $("#invoice_date_old").val();

        var privilege = $("#privilege").val();

        var section_area = $("#section_area").val();

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

                                    module_id: module_id,

                                    section_area: section_area

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



    // get items to table starts

    $('#input_sales_code').autoComplete(

            {

                minChars: 1,

                cache: false,

                source: function (term, suggest)

                {

                    term = term.toLowerCase();

                    var isnum = /^\d+$/.test(term);

                    // console.log(isnum);

                    $.ajax(

                            {

                                url: base_url + "sales/get_sales_suggestions/" + term,

                                type: "GET",

                                dataType: "json",

                                success: function (data)

                                {

                                    var suggestions = [];

                                    for (var i = 0; i < data.length; ++i)

                                    {

                                        suggestions.push(data[i].item_code + ' ' + data[i].item_name);

                                        mapping[data[i].item_code] = data[i].item_id + '-' + data[i].item_type;

                                    }

                                    // console.log(i);

                                    if (i == 1 && term.length > 7 && isnum == true)

                                    {

                                        // console.log(term.length);

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

                                    call_css();

                                    $('#input_sales_code').val('')

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

    $("#sales_table_body").on("change blur", 'input[name^="item_price"],input[name^="item_description"],input[name^="item_quantity"],input[name^="item_igst"],input[name^="item_cgst"],input[name^="item_sgst"]', function (event)

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

        var item_grand_total = +row.find('input[name^="item_grand_total"]').val();





        var item_igst = +row.find('input[name^="item_igst"]').val();



        var item_cgst = +row.find('input[name^="item_cgst"]').val();

        var item_sgst = +row.find('input[name^="item_sgst"]').val();

        var tax = item_igst + item_cgst + item_sgst;

        var item_discount_amount = +row.find('input[name^="item_discount_amount"]').val();

        var item_quantity = +row.find('input[name^="item_quantity"]').val();

        var item_discount = +row.find('input[name^="item_discount_value"]').val();

        var item_taxable_value = (+item_grand_total * 100) / (100 + +tax);



        var item_sub_total = (+item_taxable_value * 100) / (100 - +item_discount);



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





});



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



    var billing_state_id = 0;

    var billing_country_id = 0;

// var type_of_supply=$('#type_of_supply').val();



    var item_discount = 0;



    var temp = {

        "item_id": item_id,

        "item_type": item_type,

        "item_igst": item_igst,

        "item_cgst": item_cgst,

        "item_sgst": item_sgst



    };

    item_gst[item_id + '-' + item_type] = temp;



// if(billing_state_id==branch_state_id)

// {

//    var item_igst = 0;

//    var item_cgst = item_cgst;

//    var item_sgst = item_sgst;



//    var igst_required="no";

// }

// else

// {

//   var item_igst = item_igst;

//    var item_cgst = 0;

//    var item_sgst = 0;



//    var igst_required="yes";

// }

// if(billing_country_id!=branch_country_id)

// {

    // var item_igst = 0;

    // var item_cgst = 0;

    // var item_sgst = 0;

//    var igst_required="yes";

// }

    var item_igst1 = item_igst;

    item_igst = 0;



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

    select_discount += '<select class="form-control open_discount form-fixer select2" name="item_discount" style="width: 100%;">';

    select_discount += '<option value="">Select</option>';

    for (a = 0; a < data.discount.length; a++)

    {

        select_discount += '<option value="' + data.discount[a].discount_id + '-' + data.discount[a].discount_value + '">' + data.discount[a].discount_value + '%' + '</option>'

    }

    select_discount += '</select></div>';



    var newRow = $("<tr id=" + table_index + ">");

    var cols = "";

    cols += "<td><a class='deleteRow'> <img src='" + base_url + "assets/images/bin_close.png' /> </a><input type='hidden' name='item_key_value'  value=" + table_index + "><input type='hidden' name='item_id' value=" + item_id + "><input type='hidden' name='item_type' value=" + item_type + "><input type='hidden' name='item_code' value='" + item_code + "'></td>";

    if (item_type == 'product' || item_type == 'product_inventory')

        cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";

    else

        cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";



    cols += "<td style='display:none;'>" + "<input type='hidden' class='form-control form-fixer' name='item_description' >" + "</td>";

    cols += "<td>" + "<input type='text' class='form-control form-fixer text-center float_number' value='1' data-rule='quantity' name='item_quantity'>" + "</td>";

    cols += "<td>" + "<span id='price_span'>" + "<input type='text' class='form-control form-fixer text-right float_number' name='item_price' value='" + item_price + "'>" + "</span>" + "<span id='item_sub_total_lbl_" + table_index + "' class='pull-right' style='color:red;'>" + item_price + "</span>" + "<input type='hidden' class='form-control form-fixer text-right' style='' value='" + item_price + "' name='item_sub_total'>" + "</td>";

    cols += "<td>" + "<input type='hidden' name='item_discount_value' value='0'>" + "<input type='hidden' name='item_discount_amount' value='0'>" + select_discount + "</td>";

    cols += "<td align='right'><input type='hidden' name='item_taxable_value' value='" + item_price + "' ><span id='item_taxable_value_" + table_index + "'>" + item_price + "</span></td>";

//tax area

// if(igst_required=="no")

// {

    cols += "<td>" + "<input type='hidden' name='item_tax_percentage'><input type='hidden' name='item_tax_amount'><input type='hidden' name='item_igst' class='form-control form-fixer text-center float_number' value='" + item_igst + "' readonly ><input type='text' name='item_igst1' class='form-control form-fixer text-center float_number' value='" + item_igst1 + "' readonly >" + "<input type='hidden' name='item_igst_amount' class='form-control form-fixer'>" + "<span  style='display: none;' id='item_igst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";

    cols += "<td style='display:none;'>" + "<input type='hidden' name='item_cgst' value='" + item_cgst + "' class='form-control form-fixer text-center float_number' readonly>" + "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer'>" + "<span  style='display: none;' id='item_cgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>";

    cols += "<td style='display:none;'>" + "<input type='hidden' name='item_sgst' value='" + item_sgst + "' class='form-control form-fixer text-center float_number' readonly>" + "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer'>" + "<span  style='display: none;' id='item_sgst_amount_lbl_" + table_index + "' class='pull-right' style='color:red;'></span>" + "</td>"

// }

// else

// {

//     cols += "<td>" + "<input type='hidden' name='item_tax_percentage'><input type='hidden' name='item_tax_amount'><input type='text' name='item_igst' class='form-control form-fixer text-center float_number' value='" + item_igst + "'>" + "<input type='hidden' name='item_igst_amount' class='form-control form-fixer'>" + "<span id='item_igst_amount_lbl_"+table_index+"' class='pull-right' style='color:red;'></span>" + "</td>";

//     cols += "<td>" + "<input type='text' name='item_cgst' value='" + item_cgst + "' class='form-control form-fixer text-center float_number' readonly >" + "<input type='hidden' name='item_cgst_amount' class='form-control form-fixer'>" + "<span id='item_cgst_amount_lbl_"+table_index+"' class='pull-right' style='color:red;'></span>" + "</td>";

//     cols += "<td>" + "<input type='text' name='item_sgst' value='" + item_sgst + "' class='form-control form-fixer text-center float_number' readonly >" + "<input type='hidden' name='item_sgst_amount' class='form-control form-fixer'>" + "<span id='item_sgst_amount_lbl_"+table_index+"' class='pull-right' style='color:red;'></span>" + "</td>"

// }



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



    // $('#type_of_supply').change();

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

    row.find('#item_taxable_value_' + table_index).text(item_taxable_value.toFixed(2));

    var item_igst = +row.find('input[name^="item_igst"]').val();

    var item_cgst = +row.find('input[name^="item_cgst"]').val();

    var item_sgst = +row.find('input[name^="item_sgst"]').val();

    var item_tax_percentage = item_igst + item_cgst + item_sgst;

    var item_igst_amount = item_taxable_value * item_igst / 100;

    var item_cgst_amount = item_taxable_value * item_cgst / 100;

    var item_sgst_amount = item_taxable_value * item_sgst / 100;

    var item_tax_amount = item_igst_amount + item_cgst_amount + item_sgst_amount;



    var item_grand_total = (item_taxable_value + item_tax_amount).toFixed(2);

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

    sales_data[key].item_tax_amount = item_tax_amount.toFixed(2);

    sales_data[key].item_taxable_value = item_taxable_value.toFixed(2);

    sales_data[key].item_grand_total = global_round_off(item_grand_total);

    var table_data = JSON.stringify(sales_data);

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



    var total_other_amount = +$('#total_other_amount').val();

    var final_grand_total = grand_total + total_other_amount;



    $('#total_sub_total').val(sub_total.toFixed(2));

    $('#total_taxable_amount').val(taxable.toFixed(2));

    $('#total_igst_amount').val(igst.toFixed(2));

    $('#total_cgst_amount').val(cgst.toFixed(2));

    $('#total_sgst_amount').val(sgst.toFixed(2));

    $('#total_discount_amount').val(discount.toFixed(2));

    $('#total_tax_amount').val(tax.toFixed(2));

    $('#total_other_amount').val(global_round_off(total_other_amount));

    $('#total_grand_total').val(round_off(final_grand_total));



    $('#totalSubTotal').text(sub_total.toFixed(2));

    $('#totalDiscountAmount').text(discount.toFixed(2));

    $('#totalTaxAmount').text(tax.toFixed(2));

    $('#totalGrandTotal').text(round_off(final_grand_total));

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



