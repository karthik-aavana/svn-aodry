var mapping = {};

var index = count_data;

var counter = count_data;

var service_data = new Array();

$(document).on("click", ".open_service_modal", function ()

{

    $.ajax(

            {

                url: base_url + 'purchase/get_service_code',

                type: 'POST',

                success: function (data)

                {

                    var parsedJson = $.parseJSON(data);

                    var service_code = parsedJson.service_code;

                    $(".modal-body #code").val(service_code);

                    $(".modal-body #category_type").val('service');

                    $(".modal-body .type_div").hide()

                }

            })

});

$(document).ready(function ()

{

    $("#sales_submit").click(function (event)

    {

        if ($('#date').val() == "")

        {

            $('#err_date').text("Please Select the Date.");

            $('#customer').focus();

            return !1

        }

        if ($('#customer').val() == "")

        {

            $('#err_customer').text("Please Select the Customer.");

            $('#customer').focus();

            return !1

        }

        var radio_btn = $('input[name="myRadio"]:checked', '#myForm').val();

        if (radio_btn != "yes" && radio_btn != "no")

        {

            $('#err_myradio').text("Please Check Any Option.");

            return !1

        }

        var supply = $('#nature_of_supply').val();

        if (supply == "")

        {

            $("#err_nature_supply").text("Please Select Nature of Supply");

            return !1

        } else

        {

            $("#err_nature_supply").text("")

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

        if ($('#type_of_supply').val() == "")

        {

            $('#err_type_of_supply').text("Please Select the Type of Supply.");

            $('#err_type_of_supply').focus();

            return !1

        }

        if ($('#quotation').val() != "convert_quotation")

        {

            if ($('#table_data1').val() == "")

            {

                if ($('#table_data2').val() == "")

                {
                    alert_d.text ='Sales Items are empty';
                    PNotify.error(alert_d); 
                    /*alert("Sales Items are empty");*/

                    return !1

                }

            }

        }

        var grand_total = $('#grand_total').val();

        if (grand_total == "" || grand_total == null || grand_total == 0.00)

        {

            $("#err_service").text("Please Select service");

            $('#service').focus();

            return !1

        } else

        {

            $("#err_service").text("")

        }

        var tablerowCount = $('#service_table_body tr').length;

        if (tablerowCount < 1)

        {

            $("#err_service").text("Please Select service");

            $('#service').focus();

            return !1

        }

    });

    $('#customer').change(function ()

    {

        if ($(this).find('option:selected').text() != 'Select')

        {

            $('#input_service_code').prop('disabled', !1);

            $('.search_service_code').show();

            $('#err_service').text('')

        } else

        {

            $('#err_service').text('Please select the customer to do sale.');

            $('.search_service_code').hide();

            $('#input_service_code').prop('disabled', !0)

        }

        $('#myForm input').change()

    }).change();

    $("#billing_state").change(function (event)

    {

        var table_data1 = $('#table_data1').val();

        if (table_data1 != "")

        {

            $.ajax(

                    {

                        url: base_url + 'sales/change_gst',

                        dataType: 'JSON',

                        method: 'POST',

                        data:

                                {

                                    'billing_state_id': $('#billing_state').val(),

                                    'billing_country_id': $('#billing_country').val(),

                                    'section_area': $('#section_area').val(),

                                    'table_data': $('#table_data1').val()

                                },

                        success: function (result)

                        {

                            if (result.table_data_new != 0)

                            {

                                var replace_data = result.cols;

                                $('#service_data tbody').html(replace_data);

                                $('#table_data1').val(result.table_data_new);

                                service_data = [];

                                service_data = JSON.parse(result.table_data_new);

                                $("table.service_table").find('input[name^="qty"]').each(function ()

                                {

                                    //alert($(this).val());

                                    $("table.service_table").find('#qty').change();

                                });

                            }

                        }

                    })

        }

    });

    $('#billing_country').change(function ()

    {

        var id = $(this).val();

        $('#billing_state').html('<option value="">Select</option>');

        $.ajax(

                {

                    url: base_url + 'common/getState/' + id,

                    type: "GET",

                    dataType: "JSON",

                    success: function (data)

                    {

                        for (i = 0; i < data.length; i++)

                        {

                            $('#billing_state').append('<option value="' + data[i].id + '">' + data[i].name + '</option>')

                        }

                    }

                })

    });

    $("#customer").change(function (event)

    {

        var customer = $('#customer').val();

        if (customer != "")

        {

            $.ajax(

                    {

                        url: base_url + 'sales/get_customer_place',

                        dataType: 'JSON',

                        method: 'POST',

                        data:

                                {

                                    'customer_id': $('#customer').val(),

                                },

                        success: function (result)

                        {

                            $('#billing_country').val(result.data.country_id);

                            $('#billing_country').change();

                            var data = result.state;

                            $('#billing_state').html('');

                            $('#billing_state').append('<option value="">Select</option>');

                            for (i = 0; i < data.length; i++)

                            {

                                $('#billing_state').append('<option value="' + data[i].id + '">' + data[i].name + '</option>')

                            }

                            $('#billing_state').val(result.data.state_id);

                            $('#billing_state').change()

                        }

                    })

        }

    }).change();

    $('#input_service_code').autoComplete(

            {

                minChars: 1,

                cache: false,

                source: function (term, suggest)

                {

                    term = term.toLowerCase();

                    $.ajax(

                            {

                                url: base_url + "service/getBarcodeService/" + term,

                                type: "GET",

                                dataType: "json",

                                success: function (data)

                                {

                                    var suggestions = [];

                                    for (var i = 0; i < data.length; ++i)

                                    {

                                        suggestions.push(data[i].code + ' - ' + data[i].name);

                                        mapping[data[i].code] = data[i].service_id

                                    }

                                    suggest(suggestions)

                                }

                            })

                },

                onSelect: function (event, ui)

                {

                    var str = ui.split(' ');

                    var billing_state_id = $('#billing_state').val();

                    var billing_country_id = $('#billing_country').val();

                    $.ajax(

                            {

                                url: base_url + "service/getServiceUseCode/" + mapping[str[0]] + '/' + billing_state_id + '/' + billing_country_id,

                                type: "GET",

                                dataType: "JSON",

                                success: function (data)

                                {

                                    add_row(data);

                                    $('#input_service_code').val('')

                                }

                            })

                }

            });

    $("#date").blur(function (event)

    {

        var date = $('#date').val();

        if (date == null || date == "")

        {

            $("#err_date").text("Please Enter Date");

            $('#date').focus();

            return !1

        } else

        {

            $("#err_date").text("")

        }

        if (!date.match(date_regex))

        {

            $('#err_code').text(" Please Enter Valid Date ");

            $('#date').focus();

            return !1

        } else

        {

            $("#err_code").text("")

        }

    });

    $("#customer").change(function (event)

    {

        var customer = $('#customer').val();

        if (customer == "")

        {

            $("#err_customer").text("Please Enter Customer");

            $('#customer').focus();

            return !1

        } else

        {

            $("#err_customer").text("")

        }

    });

    $("#nature_of_supply").change(function (event)

    {

        var supply = $('#nature_of_supply').val();

        if (supply == "")

        {

            $("#err_nature_supply").text("Please Select Nature of Supply");

            return !1

        } else

        {

            $("#err_nature_supply").text("")

        }

    });

    $("#type_of_supply").change(function (event)

    {

        var typ_supply = $('#type_of_supply').val();

        if (typ_supply == "")

        {

            $("#err_type_supply").text("Please Select Type of Supply");

            return !1

        } else

        {

            $("#err_type_supply").text("")

        }

    });

    $("#service").blur(function (event)

    {

        var sname_regex = /^[a-zA-Z0-9]+$/;

        var service = $('#service').val();

        if (service == null || service == "")

        {

            $("#err_service").text("Please Enter Service Code/Name");

            $('#service').focus();

            return !1

        } else

        {

            $("#err_service").text("")

        }

        if (!date.match(sname_regex))

        {

            $('#err_service').text(" Please Enter Valid Service Code/Name ");

            $('#service').focus();

            return !1

        } else

        {

            $("#err_service").text("")

        }

    });

    $("table.service_table").on("click", "a.deleteRow", function (event)

    {

        deleteRow($(this).closest("tr"));

        $(this).closest("tr").remove();

        calculateGrandTotal()

    });

    $("table.service_table").on("click", "a.deleteRow1", function (event)

    {

        deleteRow1($(this).closest("tr"));

        $(this).closest("tr").remove();

        calculateGrandTotal()

    });

    $("table.service_table").on("change", 'input[name^="price"],input[name^="description"], #item_discount,input[name^="qty"],input[name^="igst"],input[name^="cgst"],input[name^="sgst"]', function (event)

    {

        calculateRow($(this).closest("tr"));

        calculateDiscountTax($(this).closest("tr"));

        calculateGrandTotal()

    });

    var table_count = $('table.service_table tr').length;



    for (var m = 1; m < table_count; m++)

    {

        $('input[name^="qty"]').change();

    }

    $("table.service_table").on("change", '#item_discount', function (event)

    {

        var row = $(this).closest("tr");

        var discount = +row.find('#item_discount').val();

        if (discount != "")

        {

            $.ajax(

                    {

                        url: base_url + 'common/getDiscountValue/' + discount,

                        type: "GET",

                        data:

                                {

                                    'ci_csrf_token': get_csrf_hash

                                },

                        datatype: JSON,

                        success: function (value)

                        {

                            data = JSON.parse(value);

                            row.find('#discount_value').val(data[0].discount_value);

                            calculateDiscountTax(row, data[0].discount_value);

                            calculateGrandTotal()

                        }

                    })

        } else

        {

            row.find('#discount_value').val('0');

            calculateDiscountTax(row, 0);

            calculateGrandTotal()

        }

    })

});



function add_row(data)

{

    var flag = 0;

    var id = data[0].service_id;

    var code = data[0].code;

    var name = data[0].name;

    var hsn_sac_code = data[0].hsn_sac_code;

    var description = data[0].details;

    var price = data[0].price;

    var tax_id = data[0].tax_id;

    var tax_value = data[0].tax_value;

    var igst = data.tax_data.igst;

    var cgst = data.tax_data.cgst;

    var sgst = data.tax_data.sgst;



    if (tax_value == null)

    {

        tax_value = 0

    }

    var service = {

        "service_id": id,

        "price": price,

        "id": +index

    };

    length = service_data.length - 1;

    var select_discount = "";

    select_discount += '<div class="form-group">';

    select_discount += '<select class="form-control open_discount form-fixer select2" id="item_discount" name="item_discount" style="width: 100%;">';

    select_discount += '<option value="">Select</option>';

    for (a = 0; a < data.discount.length; a++)

    {

        select_discount += '<option value="' + data.discount[a].discount_id + '">' + data.discount[a].discount_value + '%' + '</option>'

    }

    select_discount += '</select></div>';

    var color;

    var newRow = $("<tr>");

    var cols = "";

    cols += "<td><a class='deleteRow'> <img src='" + base_url + "assets/images/bin3.png' /> </a><input type='hidden' name='id'  value=" + index + "><input type='hidden' name='service_id' name='service_id' value=" + id + "></td>";

    cols += "<td>" + name + "<br>HSN/SAC:" + hsn_sac_code + "</td>";

    cols += "<td class=''>" + "<input type='text' class='form-control form-fixer' name='description" + counter + "' id='description" + counter + "'  >" + "</td>";

    cols += "<td class=''>" + "<input type='number' class='form-control form-fixer text-center' value='" + data[0].quantity + "' data-rule='quantity' name='qty" + counter + "' id='qty" + counter + "' min='1'>" + "</td>";

    cols += "<td>" + "<span id='price_span'>" + "<input type='number'step='0.01' min='1' class='form-control form-fixer text-right' name='price" + counter + "' id='price" + counter + "' value='" + price + "'>" + "</span>" + "<span id='sub_total' class='pull-right'></span>" + "<input type='hidden' class='form-control form-fixer text-right' style='' value='0.00' name='linetotal" + counter + "' id='linetotal" + counter + "'>" + "</td>";

    cols += '<td>' + '<input type="hidden" id="discount_value" name="discount_value">' + '<input type="hidden" id="hidden_discount" name="hidden_discount">' + select_discount + '</td>';

    cols += '<td align="right"><span id="taxable_value"></span></td>';

    if (igst != "" && igst != 0)

    {

        cols += '<td>' + '<input type="number" step="0.01" name="igst" id="igst" class="form-control form-fixer text-center" value="' + igst + '" max="100" min="0" >' + '<input type="hidden" name="igst_tax" value="' + igst + '" id="igst_tax" class="form-control form-fixer">' + '<span id="igst_tax_lbl" class="pull-right" style="color:red;"></span>' + '</td>';

        cols += '<td>' + '<input type="number" step="0.01" name="cgst" id="cgst" value="' + cgst + '" class="form-control form-fixer text-center" max="100" min="0" readonly>' + '<input type="hidden" name="cgst_tax" id="cgst_tax" value="' + cgst + '" class="form-control form-fixer">' + '<span id="cgst_tax_lbl" class="pull-right" style="color:red;"></span>' + '</td>';

        cols += '<td>' + '<input type="number" step="0.01" name="sgst" id="sgst" value="' + sgst + '" class="form-control form-fixer text-center" max="100" min="0" readonly>' + '<input type="hidden" name="sgst_tax" id="sgst_tax" value="' + sgst + '" class="form-control form-fixer">' + '<span id="sgst_tax_lbl" class="pull-right" style="color:red;"></span>' + '</td>'

    } else

    {

        cols += '<td>' + '<input type="number" step="0.01" name="igst" id="igst" class="form-control form-fixer text-center" value="' + igst + '" max="100" min="0" readonly>' + '<input type="hidden" name="igst_tax" value="' + igst + '" id="igst_tax" class="form-control form-fixer">' + '<span id="igst_tax_lbl" class="pull-right" style="color:red;"></span>' + '</td>';

        cols += '<td>' + '<input type="number" step="0.01" name="cgst" id="cgst" value="' + cgst + '" class="form-control form-fixer text-center" max="100" min="0" >' + '<input type="hidden" name="cgst_tax" id="cgst_tax" value="' + cgst + '" class="form-control form-fixer">' + '<span id="cgst_tax_lbl" class="pull-right" style="color:red;"></span>' + '</td>';

        cols += '<td>' + '<input type="number" step="0.01" name="sgst" id="sgst" value="' + sgst + '" class="form-control form-fixer text-center" max="100" min="0" >' + '<input type="hidden" name="sgst_tax" id="sgst_tax" value="' + sgst + '" class="form-control form-fixer">' + '<span id="sgst_tax_lbl" class="pull-right" style="color:red;"></span>' + '</td>'

    }

    cols += '<td><input type="text" class="form-control form-fixer text-right" id="service_total" name="service_total" readonly></td>';

    cols += "</tr>";

    counter++;

    newRow.append(cols);

    $("table.service_table").append(newRow);

    var table_data = JSON.stringify(service_data);

    $('#table_data').val(table_data);

    index++;

    calculateRow(newRow);

    calculateDiscountTax(newRow);

    calculateGrandTotal();

}



function deleteRow(row)

{

    var id = +row.find('input[name^="id"]').val();



    for (var i = 0; i < service_data.length; i++)

    {

        if (service_data[i] != null && service_data[i].id == id)

        {

            service_data.splice(i, 1)

        }

    }



    var table_data = JSON.stringify(service_data);

    $('#table_data1').val(table_data)

}



function deleteRow1(row)

{

    var id = +row.find('input[name^="id"]').val();

    service_data[id] = 'delete';

    var table_data = JSON.stringify(service_data);

    $('#table_data1').val(table_data)

}



function calculateDiscountTax(row, data = 0, data1 = 0)

{

    var discount;

    if (data == 0)

    {

        discount = +row.find('#discount_value').val()

    } else

    {

        discount = data

    }

    var sales_total = +row.find('input[name^="linetotal"]').val();

    var total_discount = sales_total * discount / 100;

    var taxable_value = sales_total - total_discount;

    row.find('#taxable_value').text(taxable_value.toFixed(2));

    var igst = +row.find('input[name^="igst"]').val();

    var cgst = +row.find('input[name^="cgst"]').val();

    var sgst = +row.find('input[name^="sgst"]').val();

    var igst_tax = taxable_value * igst / 100;

    var cgst_tax = taxable_value * cgst / 100;

    var sgst_tax = taxable_value * sgst / 100;

    var tax = igst_tax + cgst_tax + sgst_tax;

    var key_value = +row.find('input[name^="id"]').val();

    var sum_sub = taxable_value + tax;

    row.find('#service_total').val(sum_sub.toFixed(2));

    row.find('input[name^="igst_tax"]').val(igst_tax);

    row.find('#igst_tax_lbl').text(igst_tax.toFixed(2));

    row.find('input[name^="cgst_tax"]').val(cgst_tax);

    row.find('#cgst_tax_lbl').text(cgst_tax.toFixed(2));

    row.find('input[name^="sgst_tax"]').val(sgst_tax);

    row.find('#sgst_tax_lbl').text(sgst_tax.toFixed(2));

    row.find('#hidden_discount').val(total_discount);

    var key = +row.index();

    service_data[key].discount = total_discount;

    service_data[key].discount_value = +row.find('#discount_value').val();

    service_data[key].discount_id = +row.find('#item_discount').val();

    service_data[key].id = +key_value;

    service_data[key].igst = igst;

    service_data[key].igst_tax = igst_tax;

    service_data[key].cgst = cgst;

    service_data[key].cgst_tax = cgst_tax;

    service_data[key].sgst = sgst;

    service_data[key].sgst_tax = sgst_tax;

    var table_data = JSON.stringify(service_data);

    $('#table_data1').val(table_data)

}



function calculateRow(row)

{

    var key = +row.index();

    var price = +row.find('input[name^="price"]').val();

    var qty = +row.find('input[name^="qty"]').val();

    var service_id = +row.find('input[name^="service_id"]').val();

    var sales_item_id = +row.find('input[name^="sales_item_id"]').val();

    row.find('input[name^="linetotal"]').val((price * qty).toFixed(2));

    row.find('#sub_total').text((price * qty).toFixed(2));

    var description = row.find('input[name^="description"]').val();

    var key_value = +row.find('input[name^="id"]').val();

    if (service_data[key] == null)

    {

        var temp = {

            "service_id": service_id,

            "price": price,

            "quantity": qty,

            "id": +key_value,

            "total": (price * qty).toFixed(2)

        };

        service_data[key] = temp

    }

    service_data[key].description = description;

    service_data[key].quantity = qty;

    service_data[key].price = price;

    service_data[key].id = +key_value;

    service_data[key].total = (price * qty).toFixed(2);

    var table_data = JSON.stringify(service_data);

    $('#table_data1').val(table_data)

}



function calculateGrandTotal()

{

    var totalValue = 0;

    var totalDiscount = 0;

    var grandTax = 0;

    var grandTotal = 0;

    $("table.service_table").find('input[name^="linetotal"]').each(function ()

    {

        totalValue += +$(this).val()

    });

    $("table.service_table").find('input[name^="hidden_discount"]').each(function ()

    {

        totalDiscount += +$(this).val()

    });

    $("table.service_table").find('input[name^="igst_tax"]').each(function ()

    {

        grandTax += +$(this).val()

    });

    $("table.service_table").find('input[name^="cgst_tax"]').each(function ()

    {

        grandTax += +$(this).val()

    });

    $("table.service_table").find('input[name^="sgst_tax"]').each(function ()

    {

        grandTax += +$(this).val()

    });

    $("table.service_table").find('input[name^="service_total"]').each(function ()

    {

        grandTotal += +$(this).val()

    });

    $('#totalValue').text(totalValue.toFixed(2));

    $('#total_value').val(totalValue.toFixed(2));

    $('#totalDiscount').text(totalDiscount.toFixed(2));

    $('#total_discount').val(totalDiscount.toFixed(2));

    $('#totalTax').text(grandTax.toFixed(2));

    $('#total_tax').val(grandTax.toFixed(2));

    $('#grandTotal').text(grandTotal.toFixed() + ".00");

    $('#grand_total').val(grandTotal.toFixed() + ".00");

}

