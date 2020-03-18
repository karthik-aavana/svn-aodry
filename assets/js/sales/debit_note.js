$(function () {

    $("#sales_submit").click(function (event)

    {

        if ($('#date').val() == "")

        {

            $('#err_date').text("Please Select the Date.");

            $('#customer').focus();

            return !1;

        }

        if ($('#customer').val() == "")

        {

            $('#err_customer').text("Please Select the Customer.");

            $('#customer').focus();

            return !1;

        }

        var radio_btn = $('input[name="myRadio"]:checked', '#myForm').val();

        if (radio_btn != "yes" && radio_btn != "no")

        {

            $('#err_myradio').text("Please Check Any Option.");

            return !1;

        }

        var supply = $('#nature_of_supply').val();

        if (supply == "")

        {

            $("#err_nature_supply").text("Please Select Nature of Supply");

            return !1;

        } else

        {

            $("#err_nature_supply").text("")

        }

        if ($('#billing_country').val() == "")

        {

            $('#err_billing_country').text("Please Select the Billing Country.");

            $('#err_billing_country').focus();

            return !1;

        }

        if ($('#billing_state').val() == "")

        {

            $('#err_billing_state').text("Please Select the Billing State.");

            $('#err_billing_state').focus();

            return !1;

        }

        if ($('#type_of_supply').val() == "")

        {

            $('#err_type_of_supply').text("Please Select the Type of Supply.");

            $('#err_type_of_supply').focus();

            return !1;

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

                    return !1;

                }

            }

        }

        var grand_total = $('#grand_total').val();

        var grandTotal = $('#grandTotal').text();

        // alert(grand_total);alert(grandTotal);



        if (grand_total == "0.00" || grandTotal == "0.00")

        {

            // $("#err_product").text("Please Select product");

            // $('#product').focus();

            // return !1;

            $("#err_grandTotal").text("Please Select Any Product");

            return !1;

        } else

        {

            $("#err_product").text("")

        }

        var tablerowCount = $('#product_table_body tr').length;

        if (tablerowCount < 1)

        {



            $("#err_product").text("Please Select Product");

            $('#product').focus();

            return !1;

        }

        // return !1;

    });



    $("table.product_table").on("change", 'input[name^="price"],input[name^="description"], #item_discount,input[name^="qty"],input[name^="igst"],input[name^="cgst"],input[name^="sgst"]', function (event)

    {

        calculateRow($(this).closest("tr"));

        calculateDiscountTax($(this).closest("tr"));

        calculateGrandTotal();

    });



    var table_count = $('table.product_table tr').length;

    for (var m = 1; m < table_count; m++)

    {

        $('input[name^="qty"]').change();



    }



    // $("table.product_table").on("change", '#item_discount', function (event) {

    //     var row = $(this).closest("tr");

    //     var discount = +row.find('#item_discount').val();



    //     if (discount != "") {

    //         $.ajax({

    //             url: base_url+'common/getDiscountValue/' + discount,

    //             type: "GET",

    //             data: {

    //                 'ci_csrf_token': get_csrf_hash

    //             },

    //             datatype: JSON,

    //             success: function (value) {



    //                 data = JSON.parse(value);

    //                 row.find('#discount_value').val(data[0].discount_value);

    //                 calculateRow(row);

    //                 calculateGrandTotal_check();

    //             }

    //         });

    //     } else {

    //         row.find('#discount_value').val('0');

    //         // calculateDiscountTax(row, 0);

    //         calculateGrandTotal_check();

    //     }

    // });



    $("table.product_table").on("change", 'input[name^="product_total"]', function (event)

    {



        row = $(this).closest("tr");

        var product_total = +row.find('input[name^="product_total"]').val();

        discount = +row.find('#discount_value').val();





        var quantity = +row.find('input[name^="qty"]').val();

        //console.log(product_total);

        var igst = +row.find('input[name^="igst"]').val();

        var cgst = +row.find('input[name^="cgst"]').val();

        var sgst = +row.find('input[name^="sgst"]').val();

        var tax = igst + cgst + sgst;



        var taxable_value = (+product_total * 100) / (100 + +tax);







        var sales_total = ((100 * +taxable_value) / (100 - +discount)).toFixed(2);



        var price = (+sales_total / +quantity).toFixed(2);

        var total_discount = sales_total * discount / 100;

        row.find('#taxable_value').text(taxable_value.toFixed(2));



        row.find('input[name^="price"]').val(price);

        row.find('#hidden_discount').val(total_discount);



        +row.find('input[name^="linetotal"]').val(sales_total);



        calculateRow($(this).closest("tr"));

        calculateDiscountTax($(this).closest("tr"));

        calculateGrandTotal();

        calculateGrandTotal_check();

        row.find('input[name^="product_total"]').attr("readonly", false);



    });



    $("table.product_table").on("change", 'input[name^="price"], input[name^="checkbox_valid"], input[name^="description"], input[name^="qty"],input[name^="igst"],input[name^="cgst"],input[name^="sgst"]', function (event) {

        var row = $(this).closest("tr");

        // calculateRow(row);

        calculateGrandTotal_check();

    });



    $("table.product_table").on("change", '#item_discount', function (event) {



        var row = $(this).closest("tr");

        var discount = +row.find('#item_discount').val();

        if (discount != "") {

            $.ajax({

                url: base_url + 'purchase/getDiscountValue/' + discount,

                type: "GET",

                data: {

                    get_csrf_token_name: get_csrf_hash

                },

                datatype: JSON,

                success: function (value) {

                    data = JSON.parse(value);

                    row.find('#discount_value').val(data[0].discount_value);

                    // calculateRow(row);

                    calculateDiscountTax(row, data[0].discount_value);

                    // calculateGrandTotal();

                    calculateGrandTotal_check();

                }

            });

        } else {

            row.find('#discount_value').val('0');

            calculateDiscountTax(row, 0);

            // calculateGrandTotal();

            calculateGrandTotal_check();

        }

    });



    $("table.product_table").on("change", 'input[name^="checkbox_valid"]', function (event) {

        if ($(this).prop('checked')) {

            var row = $(this).closest("tr");

            var checkbox_valid = row.find('input[name^="checkbox_valid"]').val();

            row.find('input[name^="checkbox_valid1"]').val('on');

            calculateRow(row);



            calculateDiscountTax($(this).closest("tr"));

            calculateGrandTotal();

            calculateGrandTotal_check();



        } else

        {  //alert($("#checkbox_valid").val('off'));



            var row = $(this).closest("tr");

            var checkbox_valid = row.find('input[name^="checkbox_valid"]').val();

            row.find('input[name^="checkbox_valid1"]').val('off');



            calculateRow(row);



            calculateDiscountTax($(this).closest("tr"));

            calculateGrandTotal();

            calculateGrandTotal_check();

        }

    });

    $("#product").blur(function (event) {

        var sname_regex = /^[a-zA-Z0-9]+$/;

        var product = $('#product').val();

        if (product == null || product == "") {

            $("#err_product").text("Please Enter Product Code/Name");

            $('#product').focus();

            return false;

        } else {

            $("#err_product").text("");

        }

        if (!date.match(sname_regex)) {

            $('#err_product').text(" Please Enter Valid Product Code/Name ");

            $('#product').focus();

            return false;

        } else {

            $("#err_product").text("");

        }

    });

});



function calculateGrandTotal_check() {



    var totalValue = 0;

    var totalDiscount = 0;

    var grandTax = 0;

    var grandTotal = 0;

    $("table.product_table").find('input[name^="linetotal"]').each(function () {



        parent = $(this).closest('tr');

        // alert(parent.find('input[type="checkbox"]:checked').val();

        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {

            totalValue += +$(this).val();

        }

    });

    $("table.product_table").find('input[name^="hidden_discount"]').each(function () {

        parent = $(this).closest('tr');

        // alert(parent.find('input[type="checkbox"]:checked').val();

        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {

            totalDiscount += +$(this).val();

        }

    });

    $("table.product_table").find('input[name^="igst_tax"]').each(function () {

        parent = $(this).closest('tr');

        // alert(parent.find('input[type="checkbox"]:checked').val();

        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {

            grandTax += +$(this).val();

        }

    });

    $("table.product_table").find('input[name^="cgst_tax"]').each(function () {

        parent = $(this).closest('tr');

        // alert(parent.find('input[type="checkbox"]:checked').val();

        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {

            grandTax += +$(this).val();

        }

    });

    $("table.product_table").find('input[name^="sgst_tax"]').each(function () {

        parent = $(this).closest('tr');

        // alert(parent.find('input[type="checkbox"]:checked').val();

        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {

            grandTax += +$(this).val();

        }

    });

    $("table.product_table").find('input[name^="product_total"]').each(function () {

        parent = $(this).closest('tr');

        // alert(parent.find('input[type="checkbox"]:checked').val();

        if (parent.find('input[type="checkbox"]:checked').val() == 'on') {

            grandTotal += +$(this).val();

        }

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

    row.find('#product_total').val(sum_sub.toFixed(2));

    row.find('input[name^="igst_tax"]').val(igst_tax);

    row.find('#igst_tax_lbl').text(igst_tax.toFixed(2));

    row.find('input[name^="cgst_tax"]').val(cgst_tax);

    row.find('#cgst_tax_lbl').text(cgst_tax.toFixed(2));

    row.find('input[name^="sgst_tax"]').val(sgst_tax);

    row.find('#sgst_tax_lbl').text(sgst_tax.toFixed(2));

    row.find('#hidden_discount').val(total_discount);

    var key = +row.index();

    product_data[key].discount = total_discount;

    product_data[key].discount_value = +row.find('#discount_value').val();

    product_data[key].discount_id = +row.find('#item_discount').val();

    product_data[key].id = +key_value;

    product_data[key].igst = igst;

    product_data[key].igst_tax = igst_tax;

    product_data[key].cgst = cgst;

    product_data[key].cgst_tax = cgst_tax;

    product_data[key].sgst = sgst;

    product_data[key].sgst_tax = sgst_tax;

    var table_data = JSON.stringify(product_data);

    $('#table_data1').val(table_data);

}



function calculateRow(row)

{

    var key = +row.index();

    var price = +row.find('input[name^="price"]').val();

    var qty = +row.find('input[name^="qty"]').val();

    var di_quantity = +row.find('input[name^="di_quantity"]').val();

    var product_id = +row.find('input[name^="product_id"]').val();

    var code = row.find('input[name^="code"]').val();

    var sales_item_id = +row.find('input[name^="sales_item_id"]').val();

    var credit_note_item_id = +row.find('input[name^="credit_note_item_id"]').val();

    row.find('input[name^="linetotal"]').val((price * qty).toFixed(2));

    row.find('#sub_total').text((price * qty).toFixed(2));

    var description = row.find('input[name^="description"]').val();

    var key_value = +row.find('input[name^="id"]').val();



    var checkbox_value = row.find('input[name^="checkbox_valid1"]').val();



    if (product_data[key] == null)

    {

        var temp = {

            // "checkbox_valid1": checkbox_value,

            "code": code,

            "product_id": product_id,

            "price": price,

            "quantity": qty,

            "di_quantity": di_quantity,

            "id": +key_value,

            "total": (price * qty).toFixed(2)

        };

        product_data[key] = temp

    }



    product_data[key].checkbox_valid1 = checkbox_value;



    product_data[key].sales_item_id = sales_item_id;

    product_data[key].credit_note_item_id = credit_note_item_id;

    product_data[key].description = description;

    product_data[key].quantity = qty;

    product_data[key].price = price;

    product_data[key].id = +key_value;

    product_data[key].total = (price * qty).toFixed(2);

    var table_data = JSON.stringify(product_data);

    // alert(table_data);

    // console.log(table_data);

    $('#table_data1').val(table_data);

}



function calculateGrandTotal()

{

    var totalValue = 0;

    var totalDiscount = 0;

    var grandTax = 0;

    var grandTotal = 0;

    $("table.product_table").find('input[name^="linetotal"]').each(function ()

    {

        totalValue += +$(this).val()

    });

    $("table.product_table").find('input[name^="hidden_discount"]').each(function ()

    {

        totalDiscount += +$(this).val()

    });

    $("table.product_table").find('input[name^="igst_tax"]').each(function ()

    {

        grandTax += +$(this).val()

    });

    $("table.product_table").find('input[name^="cgst_tax"]').each(function ()

    {

        grandTax += +$(this).val()

    });

    $("table.product_table").find('input[name^="sgst_tax"]').each(function ()

    {

        grandTax += +$(this).val()

    });

    $("table.product_table").find('input[name^="product_total"]').each(function ()

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

