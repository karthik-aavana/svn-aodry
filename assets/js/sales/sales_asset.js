

var mapping = {};

var index = count_data;



var counter = count_data;

var product_data = new Array();



$(document).ready(function () {







    $("#purchase_submit").click(function (event) {





        if ($('#customer').val() == "")

        {

            $('#err_customer').text("Please Select the customer.");

            $('#customer').focus();

            return false;

        }



        var radio_btn = $('input[name="myRadio"]:checked', '#myForm').val();

        if (radio_btn != "yes" && radio_btn != "no")

        {

            $('#err_myradio').text("Please Check Any Option.");



            return false;

        }





        if ($('#table_data1').val() == "")

        {



            if ($('#table_data2').val() == "")

            {
                alert_d.text ='Purchase Items are empty';
                PNotify.error(alert_d); 
                /*alert("Purchase Items are empty");*/

                return false;

            }



        }









        var grand_total = $('#grand_total').val();



        if (grand_total == "" || grand_total == null || grand_total == 0.00) {

            ;

            $("#err_product").text("Please Select product");

            $('#product').focus();

            return false;

        } else {

            $("#err_product").text("");

        }





    });







    $('#customer').change(function () {

        if ($(this).find('option:selected').text() != 'Select') {



            $('#input_product_code').prop('disabled', false);



            $('.search_product_code').show();

            $('#err_product').text('');



        } else {



            $('#err_product').text('Please select the customer to do sales.');

            $('.search_product_code').hide();

            $('#input_product_code').prop('disabled', true);

        }

        $('#myForm input').change();





    }).change();



    $("#customer").change(function (event) {

        var table_data1 = $('#table_data1').val();



        if (table_data1 != "") {



            $.ajax({

                url: base_url + 'sales_asset/change_gst',

                dataType: 'JSON',

                method: 'POST',

                data: {

                    'customer_id': $('#customer').val(),

                    'table_data': $('#table_data1').val()

                },

                success: function (result) {



                    if (result.table_data_new != 0)

                    {



                        var replace_data = result.cols;



                        $('#product_data tbody').html(replace_data);

                        $('#table_data1').val(result.table_data_new);



                        product_data = [];

                        product_data = JSON.parse(result.table_data_new);







                    }



                }

            });



        }

    });





    $('#input_product_code').autoComplete({

        minChars: 1,

        cache: false,

        source: function (term, suggest) {

            term = term.toLowerCase();

            //var warehouse_id = $('#warehouse').val();

            $.ajax({

                url: base_url + "product/getBarcodeproductAsset/" + term,

                type: "GET",

                dataType: "json",

                success: function (data) {

// console.log(data);

                    var suggestions = [];

                    for (var i = 0; i < data.length; ++i) {

                        suggestions.push(data[i].code + ' - ' + data[i].name + ' - ' + data[i].date);

                        mapping[data[i].code + ' - ' + data[i].name + ' - ' + data[i].date] = data[i].asset_id;

                    }

                    suggest(suggestions);



                }

            });

        },

        onSelect: function (event, ui) {

            var str = ui.split(' ');



            var customer_id = $('#customer').val();



//alert()

            $.ajax({

                url: base_url + "service/getAssetCode/" + mapping[ui] + '/' + customer_id,

                type: "GET",

                dataType: "JSON",

                success: function (data) {

                    // console.log(data);

                    add_row(data);

                    $('#input_product_code').val('');

                }

            });

        }

    });





    $("#date").blur(function (event) {

        var date = $('#date').val();

        if (date == null || date == "") {

            $("#err_date").text("Please Enter Date");

            $('#date').focus();

            return false;

        } else {

            $("#err_date").text("");

        }

        if (!date.match(date_regex)) {

            $('#err_code').text(" Please Enter Valid Date ");

            $('#date').focus();

            return false;

        } else {

            $("#err_code").text("");

        }

    });





    $("#customer").change(function (event) {

        var customer = $('#customer').val();

        if (customer == "") {

            $("#err_customer").text("Please Enter customer");

            $('#customer').focus();

            return false;

        } else {

            $("#err_customer").text("");

        }

    });







    $("table.product_table").on("click", "a.deleteRow", function (event) {

        deleteRow($(this).closest("tr"));

        $(this).closest("tr").remove();

        calculateGrandTotal();

    });



    $("table.product_table").on("click", "a.deleteRow1", function (event) {

        deleteRow1($(this).closest("tr"));

        $(this).closest("tr").remove();

        calculateGrandTotal();

    });





    $("table.product_table").on("change", 'input[name^="price"],input[name^="description"], #item_discount,input[name^="qty"],input[name^="igst"],input[name^="cgst"],input[name^="sgst"]', function (event) {

        calculateRow($(this).closest("tr"));

        calculateDiscountTax($(this).closest("tr"));

        calculateGrandTotal();

    });



    if (purchase_reload == "yes")

    {

        var table_count = $('table.product_table tr').length;



        for (var m = 1; m < table_count; m++)

        {

            $('input[name^="qty"]').change();



        }



    }





    $("table.product_table").on("change", '#item_discount', function (event) {

        var row = $(this).closest("tr");

        var discount = +row.find('#item_discount').val();



        if (discount != "") {

            $.ajax({

                url: base_url + 'common/getDiscountValue/' + discount,

                type: "GET",

                data: {

                    'ci_csrf_token': get_csrf_hash

                },

                datatype: JSON,

                success: function (value) {



                    data = JSON.parse(value);

                    row.find('#discount_value').val(data[0].discount_value);

                    calculateDiscountTax(row, data[0].discount_value);

                    calculateGrandTotal();

                }

            });

        } else {

            row.find('#discount_value').val('0');

            calculateDiscountTax(row, 0);

            calculateGrandTotal();

        }

    });



});



function add_row(data) {



    var flag = 0;



    var flag = 0;

    $("table.product_table").find('input[name^="asset_id"]').each(function () {

        if (data[0].asset_id == +$(this).val()) {

            flag = 1;

        }

    });



    if (flag == 0)

    {

        var id = data[0].product_id;

        var asset_id = data[0].asset_id;

        //alert(asset_id);

        var code = data[0].code;

        var name = data[0].name;

        var hsn_sac_code = data[0].hsn_sac_code;

        // var description = data[0].details;

        var price = data[0].price;

        var tax_id = data[0].tax_id;

        var tax_value = data[0].tax_value;



        var igst = data['tax_data'].igst;

        var cgst = data['tax_data'].cgst;

        var sgst = data['tax_data'].sgst;





        if (tax_value == null) {

            tax_value = 0;

        }

        var product = {"product_id": id,

            "asset_id": asset_id,

            "price": price

        };

        product_data[index] = product;



        length = product_data.length - 1;



        var select_discount = "";

        select_discount += '<div class="form-group">';

        select_discount += '<select class="form-control form-fixer select2" id="item_discount" name="item_discount" style="width: 100%;">';

        select_discount += '<option value="">Select</option>';

        for (a = 0; a < data['discount'].length; a++) {

            select_discount += '<option value="' + data['discount'][a].discount_id + '">' + data['discount'][a].discount_value + '%' + '</option>';

        }

        select_discount += '</select></div>';

        var color;

        // data[0].quantity > 10 ? color = "green" : color = "red";

        var newRow = $("<tr>");

        var cols = "";



        cols += "<td><a class='deleteRow'> <img src='" + base_url + "assets/images/bin3.png' /> </a><input type='hidden' name='id'  value=" + index + "><input type='hidden' name='product_id' name='product_id' value=" + id + "></td><input type='hidden' name='asset_id' name='asset_id' value=" + asset_id + "></td>";

        cols += "<td>" + name + "<br>HSN/SAC:" + hsn_sac_code + "</td>";

        cols += "<td class=''>"

                + "<input type='text' class='form-control form-fixer' name='description" + counter + "' id='description" + counter + "'  >"



                + "</td>";

        cols += "<td class=''>"

                + "<input type='number' class='form-control form-fixer text-center' value='1' data-rule='quantity' name='qty" + counter + "' id='qty" + counter + "' min='1'>"



                + "</td>";

        cols += "<td>"

                + "<span id='price_span'>"

                + "<input type='number'step='0.01' min='1' class='form-control form-fixer text-right' name='price" + counter + "' id='price" + counter + "' value='" + price + "'>"

                + "</span>"

                + "<span id='sub_total' class='pull-right'></span>"

                + "<input type='hidden' class='form-control form-fixer text-right' style='' value='0.00' name='linetotal" + counter + "' id='linetotal" + counter + "'>"

                + "</td>";

        cols += '<td style="display:none">'

                + '<input type="hidden" id="discount_value" name="discount_value">'

                + '<input type="hidden" id="hidden_discount" name="hidden_discount">'

                + select_discount

                + '</td>';

        cols += '<td align="right"><span id="taxable_value"></span></td>';





        if (igst != "" && igst != 0)

        {



            cols += '<td>'

                    + '<input type="number" step="0.01" name="igst" id="igst" class="form-control form-fixer text-center" value="' + igst + '" max="100" min="0" >'

                    + '<input type="hidden" name="igst_tax" value="' + igst + '" id="igst_tax" class="form-control form-fixer">'

                    + '<span id="igst_tax_lbl" class="pull-right" style="color:red;"></span>'

                    + '</td>';

            cols += '<td>'

                    + '<input type="number" step="0.01" name="cgst" id="cgst" value="' + cgst + '" class="form-control form-fixer text-center" max="100" min="0" readonly>'

                    + '<input type="hidden" name="cgst_tax" id="cgst_tax" value="' + cgst + '" class="form-control form-fixer">'

                    + '<span id="cgst_tax_lbl" class="pull-right" style="color:red;"></span>'

                    + '</td>';

            cols += '<td>'

                    + '<input type="number" step="0.01" name="sgst" id="sgst" value="' + sgst + '" class="form-control form-fixer text-center" max="100" min="0" readonly>'

                    + '<input type="hidden" name="sgst_tax" id="sgst_tax" value="' + sgst + '" class="form-control form-fixer">'

                    + '<span id="sgst_tax_lbl" class="pull-right" style="color:red;"></span>'

                    + '</td>';



        } else

        {



            cols += '<td>'

                    + '<input type="number" step="0.01" name="igst" id="igst" class="form-control form-fixer text-center" value="' + igst + '" max="100" min="0" readonly>'

                    + '<input type="hidden" name="igst_tax" value="' + igst + '" id="igst_tax" class="form-control form-fixer">'

                    + '<span id="igst_tax_lbl" class="pull-right" style="color:red;"></span>'

                    + '</td>';

            cols += '<td>'

                    + '<input type="number" step="0.01" name="cgst" id="cgst" value="' + cgst + '" class="form-control form-fixer text-center" max="100" min="0" >'

                    + '<input type="hidden" name="cgst_tax" id="cgst_tax" value="' + cgst + '" class="form-control form-fixer">'

                    + '<span id="cgst_tax_lbl" class="pull-right" style="color:red;"></span>'

                    + '</td>';

            cols += '<td>'

                    + '<input type="number" step="0.01" name="sgst" id="sgst" value="' + sgst + '" class="form-control form-fixer text-center" max="100" min="0" >'

                    + '<input type="hidden" name="sgst_tax" id="sgst_tax" value="' + sgst + '" class="form-control form-fixer">'

                    + '<span id="sgst_tax_lbl" class="pull-right" style="color:red;"></span>'

                    + '</td>';

        }



        cols += '<td><input type="text" class="form-control form-fixer text-right" id="product_total" name="product_total" readonly></td>';

        cols += "</tr>";



        counter++;





        newRow.append(cols);



        $("table.product_table").append(newRow);



        var table_data = JSON.stringify(product_data);

        $('#table_data').val(table_data);

        index++;







        calculateRow(newRow);





        calculateDiscountTax(newRow);



        calculateGrandTotal();





    } else {

        $('#err_product').text('Product Already Added').animate({opacity: '0.0'}, 2000).animate({opacity: '0.0'}, 1000).animate({opacity: '1.0'}, 2000);

    }







}



function deleteRow(row) {

    var id = +row.find('input[name^="id"]').val();

    //var array_id = product_data[id].product_id;

    product_data.splice(id, 1);

    var table_data = JSON.stringify(product_data);

    $('#table_data1').val(table_data);

}



function deleteRow1(row) {

    var id = +row.find('input[name^="id"]').val();

    product_data[id] = 'delete';

    var table_data = JSON.stringify(product_data);

    $('#table_data1').val(table_data);

}

function calculateDiscountTax(row, data = 0, data1 = 0) {

    var discount;

    if (data == 0) {

        discount = +row.find('#discount_value').val();

    } else {

        discount = data;

    }



    var sales_total = +row.find('input[name^="linetotal"]').val();

    var total_discount = sales_total * discount / 100;

    var taxable_value = sales_total - total_discount;

    row.find('#taxable_value').text(taxable_value.toFixed(2));

    var igst = +row.find('input[name^="igst"]').val();

    // var tds = +row.find('input[name^="tds_value"]').val();



    var cgst = +row.find('input[name^="cgst"]').val();

    var sgst = +row.find('input[name^="sgst"]').val();

    // var tds_amt = taxable_value*tds/100;

    var igst_tax = taxable_value * igst / 100;

    var cgst_tax = taxable_value * cgst / 100;

    var sgst_tax = taxable_value * sgst / 100;

    var tax = igst_tax + cgst_tax + sgst_tax;

    // row.find('input[name^="tds_tax_amt"]').val(tds_amt.toFixed(2));

    // row.find('#tds_tax_lbl').text(tds_amt.toFixed(2));

    var sum_sub = taxable_value + tax;

    row.find('#product_total').val(sum_sub.toFixed(2));

    row.find('input[name^="igst_tax"]').val(igst_tax);

    row.find('#igst_tax_lbl').text(igst_tax.toFixed(2));

    row.find('input[name^="cgst_tax"]').val(cgst_tax);

    row.find('#cgst_tax_lbl').text(cgst_tax.toFixed(2));

    row.find('input[name^="sgst_tax"]').val(sgst_tax);

    row.find('#sgst_tax_lbl').text(sgst_tax.toFixed(2));





    row.find('#hidden_discount').val(total_discount);



    var key = +row.find('input[name^="id"]').val();

    product_data[key].discount = total_discount;

    product_data[key].discount_value = +row.find('#discount_value').val();

    product_data[key].discount_id = +row.find('#item_discount').val();

    // product_data[key].tds = tds;

    //  product_data[key].tds_amt = tds_amt;

    product_data[key].igst = igst;

    product_data[key].igst_tax = igst_tax;

    product_data[key].cgst = cgst;

    product_data[key].cgst_tax = cgst_tax;

    product_data[key].sgst = sgst;

    product_data[key].sgst_tax = sgst_tax;

    var table_data = JSON.stringify(product_data);

    $('#table_data1').val(table_data);

}



function calculateRow(row) {

    var key = +row.find('input[name^="id"]').val();

    var price = +row.find('input[name^="price"]').val();

    var qty = +row.find('input[name^="qty"]').val();

    var product_id = +row.find('input[name^="product_id"]').val();

    var asset_id = +row.find('input[name^="asset_id"]').val();

    var purchase_item_id = +row.find('input[name^="purchase_item_id"]').val();

    row.find('input[name^="linetotal"]').val((price * qty).toFixed(2));

    row.find('#sub_total').text((price * qty).toFixed(2));

    var description = row.find('input[name^="description"]').val();





    if (product_data[key] == null) {

        var temp = {

            "product_id": product_id,

            "asset_id": asset_id,

            "price": price,

            "quantity": qty,

            "total": (price * qty).toFixed(2)

        };

        product_data[key] = temp;

    }

    product_data[key].description = description;

    product_data[key].quantity = qty;

    product_data[key].price = price;

    product_data[key].total = (price * qty).toFixed(2);

    var table_data = JSON.stringify(product_data);



    $('#table_data1').val(table_data);

}

function calculateGrandTotal() {

    var totalValue = 0;

    var totalDiscount = 0;

    var grandTax = 0;

    var grandTotal = 0;

    $("table.product_table").find('input[name^="linetotal"]').each(function () {

        totalValue += +$(this).val();

    });

    $("table.product_table").find('input[name^="hidden_discount"]').each(function () {

        totalDiscount += +$(this).val();

    });

    $("table.product_table").find('input[name^="igst_tax"]').each(function () {

        grandTax += +$(this).val();

    });

    $("table.product_table").find('input[name^="cgst_tax"]').each(function () {

        grandTax += +$(this).val();

    });

    $("table.product_table").find('input[name^="sgst_tax"]').each(function () {

        grandTax += +$(this).val();

    });

    $("table.product_table").find('input[name^="product_total"]').each(function () {

        grandTotal += +$(this).val();

    });



    $('#totalValue').text(totalValue.toFixed(2));

    $('#total_value').val(totalValue.toFixed(2));

    $('#totalDiscount').text(totalDiscount.toFixed(2));

    $('#total_discount').val(totalDiscount.toFixed(2));

    $('#totalTax').text(grandTax.toFixed(2));

    $('#total_tax').val(grandTax.toFixed(2));

    $('#grandTotal').text(grandTotal.toFixed(2));

    $('#grand_total').val(grandTotal.toFixed(2));



}







