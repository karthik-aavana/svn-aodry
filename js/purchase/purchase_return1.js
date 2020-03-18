$(document).ready(function () {
    $("#submit").click(function (event)
    {
        if ($('#date').val() == "")
        {
            $('#err_date').text("Please Select the Date.");
            $('#date').focus();
            return !1;
        } else
        {
            $('#err_date').text("");
        }

        if ($('#supplier').val() == "")
        {
            $('#err_supplier').text("Please Select the Supplier.");
            $('#supplier').focus();
            return !1;
        } else
        {
            $('#err_supplier').text("");
        }

        if ($('#invoice').val() == "")
        {
            $('#err_invoice').text("Please Select the Purchase Invoice No.");
            $('#invoice').focus();
            return !1;
        } else
        {
            $('#err_invoice').text("");
        }

        if ($('#billing_country').val() == "")
        {
            $('#err_billing_country').text("Please Select the Billing Country.");
            $('#err_billing_country').focus();
            return !1;
        } else
        {
            $('#err_billing_country').text("");
        }

        if ($('#billing_state').val() == "")
        {
            $('#err_billing_state').text("Please Select the Billing State.");
            $('#err_billing_state').focus();
            return !1;
        } else
        {
            $('#err_billing_state').text("");
        }

        var grand_total = $('#grand_total').val();
        var grandTotal = $('#grandTotal').text();
        // alert(grand_total);alert(grandTotal);

        if (grand_total == "0.00" || grandTotal == "0.00")
        {
            $("#err_grandTotal").text("Please Select Any Product");
            return !1;
        } else
        {
            $("#err_product").text("")
        }
        // var tablerowCount = $('#product_table_body tr').length;
        // if (tablerowCount < 1)
        // {

        //    $("#err_product").text("Please Select Product");
        //    $('#product').focus();
        //    return !1;
        // }
        // return !1;
    });
    $("#billing_state").change(function (event) {
        var table_data1 = $('#table_data1').val();

        if (table_data1 != "") {

            $.ajax({
                url: base_url + 'purchase/change_gst_purchase_return',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'billing_state_id': $('#billing_state').val(),
                    'billing_country_id': $('#billing_country').val(),
                    'select_check_box': $('#select_check_box').val(),
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

                        $("table.product_table").find('input[name^="qty"]').each(function ()
                        {
                            //alert($(this).val());
                            $("table.product_table").find('input[name^="qty"]').change();
                        });

                    }

                    // $('#invoice').change();

                    $('input:checkbox').each(function () {
                        if ($(this).prop('checked'))
                        {
                            $(this).parent().nextAll().find('input#qty').prop('readOnly', false).css('background-color', '#FFFFFF');
                            $(this).parent().nextAll().find('input#description').prop('readOnly', false).css('background-color', '#FFFFFF');
                        } else
                        {
                            $(this).parent().nextAll().find('input#qty').prop('readOnly', true).css('background-color', '#E3E3E3');
                            $(this).parent().nextAll().find('input#description').prop('readOnly', true).css('background-color', '#E3E3E3');
                        }
                    });

                    $('input:checkbox').on('click', function () {
                        if ($(this).prop('checked'))
                        {
                            $(this).parent().nextAll().find('input#qty').prop('readOnly', false).css('background-color', '#FFFFFF');
                            $(this).parent().nextAll().find('input#description').prop('readOnly', false).css('background-color', '#FFFFFF');
                        } else
                        {
                            $(this).parent().nextAll().find('input#qty').prop('readOnly', true).css('background-color', '#E3E3E3');
                            $(this).parent().nextAll().find('input#description').prop('readOnly', true).css('background-color', '#E3E3E3');
                        }
                    });
                }
            });

        }
    });

    $('#billing_country').change(function () {

        var id = $(this).val();
        $('#billing_state').html('<option value="">Select</option>');
        $.ajax({
            url: base_url + 'common/getState/' + id,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                for (i = 0; i < data.length; i++) {
                    $('#billing_state').append('<option value="' + data[i].id + '">' + data[i].name + '</option>');
                }
            }
        });

    }).change();

    $(function () {
        $("#invoice_to").val("Purchase Return");

        $('#myForm input').on('change', function () {
            var radio_btn = $('input[name="myRadio"]:checked', '#myForm').val();
            if (radio_btn == "yes") {
                $("#invoice_from").val("Assets");
                //$('#asset-option').hide();
                // $("input[name='assets']:radio").removeAttr("checked");

            }
            if (radio_btn == "no") {
                var invoice_name = $("#supplier option:selected").text();
                $("#invoice_from").val(invoice_name);
            }

        }).change();
    });



    $('input:checkbox').each(function () {
        if ($(this).prop('checked')) {
            $(this).parent().nextAll().find('input#qty').prop('readOnly', false).css('background-color', '#FFFFFF');
            //  $(this).parent().nextAll().find('select').prop('disabled', false).css('background-color', '#FFFFFF');
        } else
        {
            $(this).parent().nextAll().find('input#qty').prop('readOnly', true).css('background-color', '#C0C0C0');
            //   $(this).parent().nextAll().find('select').prop('disabled', true).css('background-color', '#C0C0C0');
        }
    });
    $('input:checkbox').on('click', function () {
        if ($(this).prop('checked')) {
            $(this).parent().nextAll().find('input#qty').prop('readOnly', false).css('background-color', '#FFFFFF');
            // $(this).parent().nextAll().find('select').prop('disabled', false).css('background-color', '#FFFFFF');
        } else {
            $(this).parent().nextAll().find('input#qty').prop('readOnly', true).css('background-color', '#C0C0C0');
            // $(this).parent().nextAll().find('select').prop('disabled', true).css('background-color', '#C0C0C0');
        }
    });

    $("table.product_table").on("change", 'input[name^="price"],input[name^="checkbox_valid"], input[name^="qty"],input[name^="description"],input[name^="igst"],input[name^="cgst"],input[name^="sgst"]', function (event) {
        var row = $(this).closest("tr");
        // calculateRow(row);
        calculateRow(row);
        calculateDiscountTax(row);
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
                    calculateRow(row);
                    calculateGrandTotal_check();
                }
            });
        } else {
            row.find('#discount_value').val('0');

            calculateGrandTotal_check();
        }
    });

    $("table.product_table").on("change", 'input[name^="checkbox_valid"]', function (event) {
        if ($(this).prop('checked'))
        {
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



});

function calculateDiscountTax(row, data = 0, data1 = 0) {
    var discount;
    if (data == 0) {
        discount = +row.find('#discount_value').val();
    } else {
        discount = data;
    }

    var sales_total = +row.find('input[name^="linetotal"]').val();
    // alert(discount);
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
    var pi_quantity = +row.find('input[name^="pi_quantity"]').val();
    var product_id = +row.find('input[name^="product_id"]').val();
    var purchase_item_id = +row.find('input[name^="purchase_item_id"]').val();
    row.find('input[name^="linetotal"]').val((price * qty).toFixed(2));
    row.find('#sub_total').text((price * qty).toFixed(2));
    var description = row.find('input[name^="description"]').val();
    var checkbox_value = row.find('input[name^="checkbox_valid1"]').val();

    if (product_data[key] == null) {
        var temp = {
            "product_id": product_id,
            "price": price,
            "quantity": qty,
            "pi_quantity": pi_quantity,
            "total": (price * qty).toFixed(2)
        };
        product_data[key] = temp;
    }

    product_data[key].checkbox_valid1 = checkbox_value;

    product_data[key].description = description;
    product_data[key].quantity = qty;
    product_data[key].price = price;
    product_data[key].purchase_item_id = purchase_item_id;
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

    // var total_other_amount= +$('#total_other_amount').val();

    // var final_grand_total=grandTotal+total_other_amount;

    // alert(totalDiscount);
    // console.log(totalDiscount);

    $('#totalValue').text(totalValue.toFixed(2));
    $('#total_value').val(totalValue.toFixed(2));
    $('#totalDiscount').text(totalDiscount.toFixed(2));
    $('#total_discount').val(totalDiscount.toFixed(2));
    $('#totalTax').text(grandTax.toFixed(2));
    $('#total_tax').val(grandTax.toFixed(2));
    $('#grandTotal').text(grandTotal.toFixed() + ".00");
    $('#grand_total').val(grandTotal.toFixed() + ".00");

}

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
    $('#grandTotal').text(grandTotal.toFixed(2));
    $('#grand_total').val(grandTotal.toFixed(2));
}
