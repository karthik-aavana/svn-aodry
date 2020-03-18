var mapping = {};
var reference_array = new Array();
var reference_array_data = new Array();
var reference_array_list = new Array();
if (typeof count_data == 'undefined' || count_data == null)
{
    var count_data = 1;
}
var table_index = count_data;
$(document).ready(function () {
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
// if($('#payment_mode').val() !="bank")
// {
//   $('.hiden').hide();
// }
    /*$("#expense_bill_submit").click(function (event) {
        var date = $('#invoice_date').val().trim();
        // var payment_mode = $('#payment_mode').val();
        // var bank_name = $('#bank_name').val().trim();
        // var cheque_no = $('#cheque_no').val().trim();
        // var cheque_date = $('#cheque_date').val().trim();
        var reference_number = $('#reference_number').val();
        var supplier = $('#supplier').val();
        // var expense_type = $('#expense_type').val();
        // var grand_total = $('#grand_total').val();
        var grand_total = $('#total_grand_total').val();
        var section_area = $("#section_area").val();
        var invoice_number_old = $("#invoice_number_old").val();
        if ($('#invoice_number').val() == "")
        {
            $('#err_invoice_number').text("Please Enter Invoice Number.");
            $('#invoice_number').focus();
            return !1
        } else
        {
            $('#err_invoice_number').text(" ");
            if (section_area == "expense_bill_edit")
            {
                if (invoice_number_old != $('#invoice_number').val())
                {
                    invoice_number_count();
                }
            } else
            {
                invoice_number_count();
            }
        }
        if (date == null || date == "")
        {
            $("#err_invoice_date").text('Please Enter Date.');
            return false;
        } else {
            $("#err_invoice_date").text("");
        }
        if (!date.match(date_regex)) {
            $('#err_invoice_date').text('Please Enter Valid Date');
            return false;
        } else {
            $("#err_invoice_date").text("");
        }
        if (supplier == null || supplier == "") {
            $("#err_supplier").text('Please select the supplier.');
            return false;
        } else {
            $("#err_supplier").text("");
        }
        var limit = (table_index - 1);
        for (var i = 0; i <= limit; i++)
        {
            var expense_type_val = $('#expense_type_' + i).val();
            if (expense_type_val != undefined)
            {
                if (expense_type_val == "" || expense_type_val == null)
                {
                    $('#err_expense_type_' + i).text("Please Select the Expense Type.");
                    return false;
                } else
                {
                    $('#err_expense_type_' + i).text("");
                }
            }
        }
        if (grand_total == null || grand_total == "" || grand_total == 0) {
            $("#err_empty").text('Grand total is empty.');
            return false;
        } else {
            $("#err_empty").text("");
        }
    });*/
    $("#expense_bill_pay_now").click(function (event)
    {
        var date = $('#invoice_date').val().trim();
        var reference_number = $('#reference_number').val().trim();
        var supplier = $('#supplier').val().trim();
        // var expense_type_val = $('#expense_type').val().trim();
        var grand_total = $('#total_grand_total').val();
        if (date == null || date == "") {
            $("#err_invoice_date").text('Please Enter Date.');
            return false;
        } else {
            $("#err_invoice_date").text("");
        }
        if (!date.match(date_regex)) {
            $('#err_invoice_date').text('Please Enter Valid Date');
            return false;
        } else {
            $("#err_invoice_date").text("");
        }
        if (supplier == null || supplier == "") {
            $("#err_supplier").text('Please select the supplier.');
            return false;
        } else {
            $("#err_supplier").text("");
        }
        var limit = (table_index - 1);
        for (var i = 0; i <= limit; i++)
        {
            var expense_type_val = $('#expense_type_' + i).val();
            if (expense_type_val != undefined)
            {
                if (expense_type_val == "" || expense_type_val == null)
                {
                    $('#err_expense_type_' + i).text("Please Select the Expense Type.");
                    return false;
                } else
                {
                    $('#err_expense_type_' + i).text("");
                }
            }
        }
        if (grand_total == null || grand_total == "" || grand_total == 0) {
            $("#err_empty").text('Grand total is empty.');
            return false;
        } else {
            $("#err_empty").text("");
        }
    });
    //  $("#expense_table_body").on("change", $('.expense_type'), function (event)
    // {
    //  var expense_id=$('.expense_type').val();
    // });
    $("#expense_table_body").on("change keyup", 'select[name^="expense_type"],input[name^="amount"],input[name^="description"],input[name^="tds"],input[name^="igst"],input[name^="cgst"],input[name^="sgst"]', function (event) {
        calculateRow($(this).closest("tr"));
        calculateDiscountTax($(this).closest("tr"));
        calculateGrandTotal();
    });
    $("#expense_table_body").on("keyup", 'input[name^="igst"]', function (e)
    {
        var val = 0;
        $("#expense_table_body").find('input[name^="igst"]').each(function (e)
        {
            var row = $(this).closest("tr");
            val += +row.find('input[name^="igst"]').val();
            // var igstLength = row.find('input[name^="igst"]').val();
            //$(this).change();
        });
        if (val > 0)
        {
            $("#expense_table_body").find('input[name^="igst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                row.find('input[name^="cgst"]').prop('readonly', true);
                row.find('input[name^="cgst"]').css('background-color', '#DEDEDE');
                row.find('input[name^="sgst"]').prop('readonly', true);
                row.find('input[name^="sgst"]').css('background-color', '#DEDEDE');
            });
        } else
        {
            $("#expense_table_body").find('input[name^="igst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                row.find('input[name^="igst"]').prop('readonly', false);
                row.find('input[name^="igst"]').css('background-color', '#fff');
                row.find('input[name^="cgst"]').prop('readonly', false);
                row.find('input[name^="cgst"]').css('background-color', '#fff');
                row.find('input[name^="sgst"]').prop('readonly', false);
                row.find('input[name^="sgst"]').css('background-color', '#fff');
            });
        }
    });
    $("#expense_table_body").on("keyup", 'input[name^="cgst"]', function (e)
    {
        var val = 0;
        $("#expense_table_body").find('input[name^="cgst"]').each(function (e)
        {
            var row = $(this).closest("tr");
            val += +row.find('input[name^="cgst"]').val();
        });
        if (val > 0)
        {
            $("#expense_table_body").find('input[name^="cgst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                row.find('input[name^="igst"]').prop('readonly', true);
                row.find('input[name^="igst"]').css('background-color', '#DEDEDE');
            });
        } else
        {
            $("#expense_table_body").find('input[name^="cgst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                row.find('input[name^="igst"]').prop('readonly', false);
                row.find('input[name^="igst"]').css('background-color', '#fff');
                row.find('input[name^="cgst"]').prop('readonly', false);
                row.find('input[name^="cgst"]').css('background-color', '#fff');
                row.find('input[name^="sgst"]').prop('readonly', false);
                row.find('input[name^="sgst"]').css('background-color', '#fff');
            });
        }
    });
    $("#expense_table_body").on("keyup", 'input[name^="sgst"]', function (e)
    {
        var val = 0;
        $("#expense_table_body").find('input[name^="sgst"]').each(function (e)
        {
            var row = $(this).closest("tr");
            val += +row.find('input[name^="sgst"]').val();
        });
        if (val > 0)
        {
            $("#expense_table_body").find('input[name^="sgst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                row.find('input[name^="igst"]').prop('readonly', true);
                row.find('input[name^="igst"]').css('background-color', '#DEDEDE');
            });
        } else
        {
            $("#expense_table_body").find('input[name^="sgst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                row.find('input[name^="igst"]').prop('readonly', false);
                row.find('input[name^="igst"]').css('background-color', '#fff');
                row.find('input[name^="cgst"]').prop('readonly', false);
                row.find('input[name^="cgst"]').css('background-color', '#fff');
                row.find('input[name^="sgst"]').prop('readonly', false);
                row.find('input[name^="sgst"]').css('background-color', '#fff');
            });
        }
    });
    //split tax equally
    if (common_settings_tax_split == "yes")
    {
        $("#expense_table_body").on("change", 'input[name^="cgst"]', function (event)
        {
            var row = $(this).closest("tr");
            var cgst = +row.find('input[name^="cgst"]').val();
            var sgst = +row.find('input[name^="sgst"]').val();
            row.find('input[name^="sgst"]').val(cgst);
            if (cgst != sgst)
            {
                row.find('input[name^="sgst"]').change();
            }
            calculateGrandTotal();
        });
        $("#expense_table_body").on("change", 'input[name^="sgst"]', function (event)
        {
            var row = $(this).closest("tr");
            var sgst = +row.find('input[name^="sgst"]').val();
            var cgst = +row.find('input[name^="cgst"]').val();
            row.find('input[name^="cgst"]').val(sgst);
            if (cgst != sgst)
            {
                row.find('input[name^="cgst"]').change();
            }
            calculateGrandTotal();
        });
    }
    //reverse calculations
    $("#expense_table_body").on("change", 'input[name^="grand_total"]', function (event)
    {
        row = $(this).closest("tr");
        var item_grand_total = +row.find('input[name^="grand_total"]').val();
        var item_igst = +row.find('input[name^="igst"]').val();
        var item_cgst = +row.find('input[name^="cgst"]').val();
        var item_sgst = +row.find('input[name^="sgst"]').val();
        var tax = item_igst + item_cgst + item_sgst;
        var item_discount_amount = 0;
        var item_quantity = 1;
        var tds = +row.find('input[name^="tds"]').val();
        if (tds != "")
        {
            var tds = tds;
        } else
        {
            var tds = 0;
        }
        var sub_total = ((+item_grand_total * 100) / (100 + (+tax - +tds))).toFixed(2);
        //var tds_amount = (+sub_total * tds) / 100;
        row.find('input[name^="amount"]').val(sub_total);
        row.find('input[name^="tds"]').val(tds);
        calculateRow($(this).closest("tr"));
        calculateDiscountTax($(this).closest("tr"));
        calculateGrandTotal();
        if (common_settings_round_off == 'no')
        {
            row.find('input[name^="item_grand_total"]').val(item_grand_total.toFixed(2));
        }
    });
    // Invoice number check
    $("#invoice_number").on("blur", function (event)
    {
        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();
        if (invoice_number == null || invoice_number == "") {
            $("#err_invoice_number").text("Please Enter Invoice Number.");
            return false;
        } else
        {
            $("#err_invoice_number").text("");
            if (section_area == "expense_bill_edit")
            {
                var invoice_number_old = $("#invoice_number_old").val();
                if (invoice_number_old != invoice_number)
                {
                    invoice_number_count();
                }
            } else
            {
                invoice_number_count();
            }
        }
    });
    // $("#expense_type_0").on("change", function (event) 
    // { 
    //      var data_tds=getTDS($("#expense_type_0").val());
    //      console.log(data_tds);
    //      // row=$(this).closest("tr");
    //      //  row.find('input[name^="tds"]').val(data_tds);
    // });
    $("#expense_table_body").on("change keyup", 'select[name^="expense_type"]', function (event) {
        var row = $(this).closest("tr");
        var expense = +row.find('select[name^="expense_type"]').val();
        var result_val;
        $.ajax({
            url: base_url + 'expense_bill/get_tds',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'expense_id': expense
            },
            success: function (result) {
                var val = result[0].expense_tds;
                result_val = val;
                +row.find('input[name^="tds"]').val(result_val);
            }
        });
        calculateRow(row);
        calculateDiscountTax(row);
        calculateGrandTotal();
    });
    //dynamic entries
    var x = 2;
    reference_array_data = JSON.parse($('#expense_type_array').val()); //initlal text box count
    $('.add_row_button').click(function (e)
    {
        e.preventDefault();
        var jk = x - 1;
        $('#count_row').val(x);
        if ($('#expense_type_' + (+table_index - 1)).val() != "")
        {
            $('#err_expense_type_' + (table_index - 1)).text("");
            var get_val = $('#expense_type_' + jk).val();
            var get_text = $("#expense_type_" + jk + " option:selected").text();
            table_index = +table_index;
            // $('#expense_type_'+jk).html('');
            // $('#expense_type_'+jk).append('<option value="' + get_val + '">' + get_text + '</option>');
            var val1 = 0;
            var val2 = 0;
            var val3 = 0;
            $("#expense_table_body").find('input[name^="igst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                val1 += +row.find('input[name^="igst"]').val();
            });
            $("#expense_table_body").find('input[name^="cgst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                val2 += +row.find('input[name^="cgst"]').val();
            });
            $("#expense_table_body").find('input[name^="sgst"]').each(function (e)
            {
                var row = $(this).closest("tr");
                val3 += +row.find('input[name^="sgst"]').val();
            });
            var tr = '<tr id="' + table_index + '"><td><input type="hidden" name="item_key_value"  value="' + table_index + '"><select class="expense_type form-control select2"  name="expense_type" id="expense_type_' + table_index + '" ><option value="">Select Expense</option></select><span class="validation-color" id="err_expense_type_' + table_index + '"></span></td>' +
                    '<td><input class="form-control form-fixer" type="text" name="description" ></td>' +
                    '<td><input class="form-control form-fixer float_number" type="text" name="amount" ></td>' +
                    '<td><input class="form-control form-fixer float_number" type="text" name="tds" ><input type="hidden" name="item_tds_amount"><span id="tds_lbl_' + table_index + '" name="tds_lbl" class="pull-right" style="color:red;"></span></td>' +
                    '<td><input class="form-control form-fixer float_number" type="text" name="net_amount"  readonly></td>';
            if (val1 == 0 && val2 == 0 && val3 == 0)
            {
                tr += '<td><input class="form-control form-fixer float_number" type="text" name="igst"><input type="hidden" name="item_igst_amount" class="form-control form-fixer"><span id="igst_tax_lbl_' + table_index + '" name="igst_tax_lbl" class="pull-right" style="color:red;"></span> </td>' +
                        '<td><input class="form-control form-fixer float_number" type="text" name="cgst"><input type="hidden" name="item_cgst_amount" class="form-control form-fixer"><span id="cgst_tax_lbl_' + table_index + '" name="cgst_tax_lbl" class="pull-right" style="color:red;"></span></td> ' +
                        '<td><input class="form-control form-fixer float_number" type="text" name="sgst"><input type="hidden" name="item_sgst_amount" class="form-control form-fixer"><span id="sgst_tax_lbl_' + table_index + '" name="sgst_tax_lbl" class="pull-right" style="color:red;"></span><input type="hidden" name="item_tax_amount"></td>';
            } else if (val1 > 0)
            {
                tr += '<td><input class="form-control form-fixer float_number" type="text" name="igst" ><input type="hidden" name="item_igst_amount" class="form-control form-fixer"><span id="igst_tax_lbl_' + table_index + '" name="igst_tax_lbl" class="pull-right" style="color:red;"></span> </td>' +
                        '<td><input class="form-control form-fixer float_number" type="text" name="cgst" style="background-color:#DEDEDE" readonly><input type="hidden" name="item_cgst_amount" class="form-control form-fixer"><span id="cgst_tax_lbl_' + table_index + '" name="cgst_tax_lbl" class="pull-right" style="color:red;"></span></td> ' +
                        '<td><input class="form-control form-fixer float_number" type="text" name="sgst" style="background-color:#DEDEDE" readonly><input type="hidden" name="item_sgst_amount" class="form-control form-fixer"><span id="sgst_tax_lbl_' + table_index + '" name="sgst_tax_lbl" class="pull-right" style="color:red;"></span><input type="hidden" name="item_tax_amount"></td>';
            } else
            {
                tr += '<td><input class="form-control form-fixer float_number" type="text" name="igst" style="background-color:#DEDEDE" readonly><input type="hidden" name="item_igst_amount" class="form-control form-fixer"><span id="igst_tax_lbl_' + table_index + '" name="igst_tax_lbl" class="pull-right" style="color:red;"></span> </td>' +
                        '<td><input class="form-control form-fixer float_number" type="text" name="cgst"><input type="hidden" name="item_cgst_amount" class="form-control form-fixer"><span id="cgst_tax_lbl_' + table_index + '" name="cgst_tax_lbl" class="pull-right" style="color:red;"></span></td> ' +
                        '<td><input class="form-control form-fixer float_number" type="text" name="sgst"><input type="hidden" name="item_sgst_amount" class="form-control form-fixer"><span id="sgst_tax_lbl_' + table_index + '" name="sgst_tax_lbl" class="pull-right" style="color:red;"></span><input type="hidden" name="item_tax_amount"></td>';
            }
            tr += '<td><input class="form-control form-fixer float_number" type="text" name="grand_total"><a href="#" id="remove_' + x + '" class="remove_field pull-right">(-)Remove</a></td></tr>';
            $('#expense_table_body').append(tr); //add input box
            for (var i = 0; i < reference_array_data.length; i++)
            {
                var check = 1;
                if (check > 0)
                {
                    $('#expense_type_' + table_index).append('<option value="' + reference_array_data[i].expense_id + '">' + reference_array_data[i].expense_title + '</option>');
                }
            }
            $('.expense_type').select2();
            call_css();
            x++;
            table_index++;
            calculateGrandTotal();
        } else
        {
            for (var i = 0; i < table_index - 1; i++)
            {
                $('#err_expense_type_' + i).text("");
            }
            ;
            $('#err_expense_type_' + (table_index - 1)).text("Please Select Expense Type.");
        }
    });
});
$('#expense_table_body').on("click", ".remove_field", function (e) { //user click on remove text
    e.preventDefault();
    // var id = $(this).attr('id');
    // var split= id.split('_');
    // reference_array_list[split[1]-1]="";
    deleteRow($(this).closest("tr"));
    $(this).closest("tr").remove();
    calculateGrandTotal();
})
// Delete function
function deleteRow(row)
{
    var table_index = +row.find('input[name^="item_key_value"]').val();
    for (var i = 0; i < expense_data.length; i++)
    {
        if (expense_data[i] != null && expense_data[i].item_key_value == table_index)
        {
            expense_data.splice(i, 1)
        }
    }
    var table_data = JSON.stringify(expense_data);
    $('#table_data').val(table_data)
}
function calculateRow(row) {
    var amount = +row.find('input[name^="amount"]').val();
    row.find('input[name^="grand_total"]').val((amount).toFixed(2));
    var description = row.find('input[name^="description"]').val();
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var key = +row.index();
    var amount = +row.find('input[name^="amount"]').val();
    var expense_type_id = +row.find($('.expense_type')).val();
    var description = row.find('input[name^="description"]').val();
    if (expense_data[key] == null)
    {
        var temp = {
            "expense_type_id": expense_type_id,
            "amount": amount,
            "description": description,
            "item_key_value": table_index,
        };
        expense_data[key] = temp
    }
    expense_data[key].expense_type_id = expense_type_id;
    expense_data[key].description = description;
    expense_data[key].amount = amount;
    expense_data[key].item_key_value = table_index;
    var table_data = JSON.stringify(expense_data);
    // console.log(table_data);
    $('#table_data').val(table_data);
}
function calculateDiscountTax(row, data = 0, data1 = 0) {
    var grand_total = +row.find('input[name^="grand_total"]').val();
    var amount = +row.find('input[name^="amount"]').val();
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var igst = +row.find('input[name^="igst"]').val();
    var cgst = +row.find('input[name^="cgst"]').val();
    var sgst = +row.find('input[name^="sgst"]').val();
    var tds = +row.find('input[name^="tds"]').val();
    var net_amount = (+amount).toFixed(2);
    var tds_tax = (+amount * +tds / 100).toFixed(2);
    var igst_tax = (+net_amount * +igst / 100).toFixed(2);
    var cgst_tax = (+net_amount * +cgst / 100).toFixed(2);
    var sgst_tax = (+net_amount * +sgst / 100).toFixed(2);
    var tax_amount = (+igst_tax + +cgst_tax + +sgst_tax).toFixed(2);
    var tax_percentage = (igst + cgst + sgst).toFixed(2);
    var item_igst_amount = net_amount * igst / 100;
    var item_cgst_amount = net_amount * cgst / 100;
    var item_sgst_amount = net_amount * sgst / 100;
    var item_tds_amount = net_amount * tds / 100;
    var item_tax_amount = item_igst_amount + item_cgst_amount + item_sgst_amount;
    var sum_sub = +net_amount + +tax_amount - +tds_tax;
    row.find('input[name^="grand_total"]').val(global_round_off(+sum_sub));
    row.find('input[name^="net_amount"]').val(+net_amount);
    row.find('input[name^="total_tax_amount"]').val(tax_amount);
    row.find('input[name^="total_tax_percentage"]').val(tax_percentage);
    row.find('input[name^="igst_amount"]').val(igst_tax);
    row.find('input[name^="cgst_amount"]').val(cgst_tax);
    row.find('input[name^="sgst_amount"]').val(sgst_tax);
    row.find('input[name^="tds_amount"]').val(tds_tax);
    row.find('input[name^="item_tax_amount"]').val(item_tax_amount.toFixed(2));
    row.find('input[name^="item_tds_amount"]').val(item_tds_amount.toFixed(2));
    row.find('input[name^="item_igst_amount"]').val(item_igst_amount.toFixed(2));
    row.find('input[name^="item_cgst_amount"]').val(item_cgst_amount.toFixed(2));
    row.find('input[name^="item_sgst_amount"]').val(item_sgst_amount.toFixed(2));
    var row_limit = (table_index - 1);
    for (var i = 0; i <= table_index; i++)
    {
        // $('input[name^="igst_amount"]').val(igst_tax);
        row.find('#igst_tax_lbl_' + i).text(igst_tax);
        // $('input[name^="cgst_amount"]').val(cgst_tax);
        row.find('#cgst_tax_lbl_' + i).text(cgst_tax);
        // $('input[name^="sgst_amount"]').val(sgst_tax);
        row.find('#sgst_tax_lbl_' + i).text(sgst_tax);
        // $('input[name^="tds_amount"]').val(tds_tax);
        row.find('#tds_lbl_' + i).text(tds_tax);
    }
    $('#totalValue').text(+amount.toFixed(2));
    $('#totalTax').text(tax_amount);
    $('#totalTds').text(tds_tax);
    $('#grandTotal').text(global_round_off(+sum_sub));
    var key = +row.index();
    expense_data[key].item_key_value = +table_index;
    expense_data[key].tds = tds;
    expense_data[key].item_tds_amount = item_tds_amount.toFixed(2);
    expense_data[key].net_amount = net_amount;
    expense_data[key].igst = igst;
    expense_data[key].item_igst_amount = item_igst_amount.toFixed(2);
    expense_data[key].cgst = cgst;
    expense_data[key].item_cgst_amount = item_cgst_amount.toFixed(2);
    expense_data[key].sgst = sgst;
    expense_data[key].item_sgst_amount = item_sgst_amount.toFixed(2);
    expense_data[key].tax_percentage = tax_percentage;
    expense_data[key].item_tax_amount = item_tax_amount.toFixed(2);
    expense_data[key].sum_sub = global_round_off(sum_sub);
    var table_data = JSON.stringify(expense_data);
    $('#table_data').val(table_data)
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
//calculate grand total
function calculateGrandTotal()
{
    var amount = 0;
    var tax = 0;
    var igst = 0;
    var cgst = 0;
    var sgst = 0;
    var net_amount = 0;
    var tds = 0;
    var grand_total = 0;
    $("#expense_table_body").find('input[name^="amount"]').each(function ()
    {
        amount += +$(this).val()
    });
    $("#expense_table_body").find('input[name^="net_amount"]').each(function ()
    {
        net_amount += +$(this).val()
    });
    $("#expense_table_body").find('input[name^="item_tds_amount"]').each(function ()
    {
        tds += +$(this).val()
    });
    $("#expense_table_body").find('input[name^="item_tax_amount"]').each(function ()
    {
        tax += +$(this).val()
    });
    $("#expense_table_body").find('input[name^="item_igst_amount"]').each(function ()
    {
        igst += +$(this).val()
    });
    $("#expense_table_body").find('input[name^="item_cgst_amount"]').each(function ()
    {
        cgst += +$(this).val()
    });
    $("#expense_table_body").find('input[name^="item_sgst_amount"]').each(function ()
    {
        sgst += +$(this).val()
    });
    $("#expense_table_body").find('input[name^="grand_total"]').each(function ()
    {
        grand_total += +$(this).val()
    });
    // var total_other_amount= +$('#total_other_amount').val();
    var final_grand_total = grand_total;
    $('#total_amount').val(amount.toFixed(2));
    $('#total_igst_amount').val(igst.toFixed(2));
    $('#total_cgst_amount').val(cgst.toFixed(2));
    $('#total_sgst_amount').val(sgst.toFixed(2));
    $('#total_tax_amount').val(tax.toFixed(2));
    $('#total_tds_amount').val(tds.toFixed(2));
    $('#total_net_amount').val(net_amount.toFixed(2));
    $('#total_grand_total').val(round_off(final_grand_total));
    $('#totalSubTotal').text(amount.toFixed(2));
    $('#totaltdsAmount').text(tds.toFixed(2));
    $('#totalTaxAmount').text(tax.toFixed(2));
    $('#totalGrandTotal').text(round_off(final_grand_total));
}
