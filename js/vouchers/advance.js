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
    $("#input_sales_code").keypress(function (event) {
        if (event.which == 13) {
            return false;
        }
    });
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
    $("#billing_country").change(function (event){
        var table_data = $('#table_data').val();
        var billing_country = $('#billing_country').val();
        if (branch_country_id != billing_country){
             $('.gst_payable_div').hide();
            flag2 = 1;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="0">Out of Country</option>');
            for (var i = 0; i < branch_state_list.length; i++)
            {
                $('#billing_state').append('<option value="' + branch_state_list[i].state_id + '" utgst="' + branch_state_list[i].is_utgst + '" >' + branch_state_list[i].state_name + '</option>');
            }
        } else {
             $('.gst_payable_div').show();
            flag2 = 2;
            $('#billing_state').html('');
            $('#billing_state').append('<option value="">Select</option>');
            for (var i = 0; i < branch_state_list.length; i++)
            {
                $('#billing_state').append('<option value="' + branch_state_list[i].state_id +  '" utgst="' + branch_state_list[i].is_utgst + '" > ' + branch_state_list[i].state_name +'</option>');
            }
            $('#billing_state').append('<option value="0">Out of Country</option>');
        }
       // if (table_data != "")
     //   {
            ChangeTypeOfSupply();
       // }
    });
    //billing state changes
    $("#billing_state").change(function (event){
        var table_data = $('#table_data').val();
        var billing_state_id = $('#billing_state').val();
        if(billing_state_id == 0){
            $('.gst_payable_div').hide();
        }else{
           $('.gst_payable_div').show(); 
        }
        ChangeTypeOfSupply();
        //if (table_data != "")
        //{
            ChangeTypeOfSupply();
        //}
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
              //  if (table_data != "")
                //{
                    ChangeTypeOfSupply();
               // }
            }
        }
    });
 function ChangeTypeOfSupply(){
        var table_data = $("#table_data").val();
        var state = $('#billing_state').val();
        var is_utgst = $("#billing_state option:selected").attr('utgst');
        $('#is_utgst').val(is_utgst);        
        if(state == '0'){
            flag2 = 1;
            /*$("#billing_state").html("");
            $("#billing_state").append('<option value="0">Out of Country</option>');*/
            $("#type_of_supply").html("");
            $("#type_of_supply").append(
                '<option value="export_with_payment" selected>Export (With Tax Payment)</option>'
            );
            $("#type_of_supply").append(
                '<option value="export_without_payment" >Export (Without Tax Payment)</option>'
            );
           //  $("input:radio[name=gst_payable][value ='no']").prop('checked', true);
            $("input:radio[name=gst_payable][value ='no']").prop('checked', true);
            $('[name=gst_payable]').val('no').trigger('change').attr('disabled',true);
        } else {
            flag2 = 2;
            /*$("#billing_state").html("");
            $("#billing_state").append('<option value="">Select</option>');
            for (var i = 0; i < branch_state_list.length; i++) {
                $("#billing_state").append(
                    '<option value="' +
                    branch_state_list[i].state_id +
                    '">' +
                    branch_state_list[i].state_name +
                    "</option>"
                );
            }*/
            $("#type_of_supply").html("");
            $("#type_of_supply").append('<option value="regular">Regular</option>');
            $('[name=gst_payable]').attr('disabled',false);
        }
        $("#type_of_supply").change();
        /*var state = $('#billing_state').val();
        if(state == '0'){
            opt = "<option value='export_without_tax'>Export without tax</option>\n\
                            <option value='export_with_tax'>Export with tax</option>";
            $('[name=gst_payable]').val('no').trigger('change').attr('disabled',true);
        }else{
            opt = "<option selected='selected' value='regular'>Regular</option>";
            $('[name=gst_payable]').attr('disabled',false);
        }
        $('#type_of_supply').html(opt);*/
    }
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
                                url: base_url + "sales/get_sales_suggestions/" + term+"/d"+"/both",
                                type: "GET",
                                dataType: "json",
                                success: function (data)
                                {
                                    var suggestions = [];
                                    for (var i = 0; i < data.length; ++i) {
                                        suggestions.push(data[i].item_code + ' ' + data[i].item_name + ' ' + data[i].product_batch);
                                        mapping[data[i].item_code] = data[i].item_id +
                            "-" +
                            data[i].item_type +
                            "-" +
                            settings_discount_visible +
                            "-" +
                            settings_tax_type;
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
                                    // suggest(suggestions);
                                }
                            })
                },
                onSelect: function (event, ui)
                {
                    var code = ui.toString().split(" ");
            $.ajax({
                url: base_url + "sales/get_table_items/" + mapping[code[0]],
                type: "GET",
                dataType: "JSON",
                success: function (data) {
                    add_row(data);
                    call_css();
                    $("#input_sales_code").val("");
                }
            });
                }
            });
    // get items to table ends
   
    //delete rows
    $("#sales_table_body").on("click", "a.deleteRow", function (event) {
        var newRow = $(this).closest("tr");
        deleteRow(newRow);
        $(this)
            .closest("tr")
            .remove();
        calculateGrandTotal();
    });
    // changes in rows
    $("#sales_table_body").on(
        "change blur",
        'input[name="item_price"],input[name="item_description"],input[name="item_quantity"],select[name="item_discount"],select[name="item_tax"]','select[name="item_tax_cess"]',
        function (event) {
            var newRow = $(this).closest("tr");
            console.log(1212121212);
            calculateTable(newRow);
        }
    );
    $("#sales_table_body").on(
        "change",
        'select[name="item_tds_percentage"]',
        function (event) {
            var newRow = $(this).closest("tr");
            var item_tds_id = newRow.find('select[name^="item_tds_percentage"] option:selected').attr('tds_id');
            var item_tds_type = newRow.find('select[name^="item_tds_percentage"] option:selected').attr('type');
          
            newRow.find('input[name=item_tds_id]').val(item_tds_id);
            newRow.find('input[name=item_tds_type]').val(item_tds_type);
            calculateTable(newRow);
        }
    );
    $("#sales_table_body").on(
        "change",
        'select[name="item_tax_cess"]',
        function (event) {
            /*var item_tds_id = $(this).attr('[name=tds_id]');
            var item_tds_type = $(this).attr('[name=type]');*/
            var newRow = $(this).closest("tr");
            console.log(77777777777);
            /*newRow.find('input[name=item_tds_id]').val(item_tds_id);
            newRow.find('input[name=item_tds_type]').val(item_tds_type);*/
            console.log(newRow);
            calculateTable(newRow);
        }
    );
    //reverse calculations
    $("#sales_table_body").on(
        "change",
        'input[name^="item_grand_total"]',
        function (event) {
            row = $(this).closest("tr");
            var item_grand_total = +parseFloat(
                row.find('input[name^="item_grand_total"]').val()
            );
            var item_discount_amount = 0;
            var item_tax_amount = 0;
            var item_tax_cess_amount = 0;
            var item_tds_amount = 0;
            var item_discount_percentage = 0;
            var item_tax_percentage = 0;
            var item_tax_cess_percentage = 0;
            var item_tds_percentage = 0;
            if (settings_discount_visible != "no") {
                item_discount_amount = +parseFloat(
                    row.find('input[name^="item_discount_amount"]').val()
                );
                item_discount_percentage = +parseFloat(
                    row.find('input[name^="item_discount_percentage"]').val()
                );
            }
            if (settings_tax_type != "no_tax") {
                item_tax_amount = +parseFloat(
                    row.find('input[name="item_tax_amount"]').val()
                );
                item_tax_cess_amount = +parseFloat(
                    row.find('input[name="item_tax_cess_amount"]').val()
                );
                item_tax_percentage = +parseFloat(
                    row.find('input[name^="item_tax_percentage"]').val()
                );
                item_tax_cess_percentage = +parseFloat(
                    row.find('input[name^="item_tax_cess_percentage"]').val()
                );
            }
            if (settings_tds_visible != "no") {
                item_tds_amount = +parseFloat(
                    row.find('input[name^="item_tds_amount"]').val()
                );
                item_tds_percentage = +parseFloat(
                    row.find('[name^="item_tds_percentage"]').val()
                );
                var item_tds_type = row.find('input[name^="item_tds_type"]').val();
                //   item_tds_type = item_tds_type.toLowerCase();
               // item_tds_type = item_tds_type.toUpperCase();
                if (item_tds_type != "TCS") {
                    item_tds_amount = 0;
                    item_tds_percentage = 0;
                }
            }
            var item_quantity = +row.find('input[name^="item_quantity"]').val();
            var item_taxable_value =
                (+item_grand_total * 100) /
                (100 + (+item_tds_percentage + +item_tax_percentage + +item_tax_cess_percentage));
            var item_sub_total =
                (+item_taxable_value * 100) / (100 - +item_discount_percentage);
            var item_price = item_sub_total / item_quantity;
            row.find('input[name^="item_price"]').val(item_price);
            calculateTable(row);
        }
    );
    $("input[type=radio][name=round_off_key].minimal").on(
        "ifChanged",
        function () {
            var grand_total = $("#without_reound_off_grand_total").val();
            
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
                    $("#round_off_select").attr("style", "display: none");
                }
            }
            CalculateRoundOff();
        }
    );
    $(document).on('change','[name=round_off_value]',function(){
        CalculateRoundOff();
    })
    $(document).on('change','[name=type_of_supply]',function(){
        var type_of_supply = $(this).val();
        reCalculateTable();
    }) 
});
function CalculateRoundOff(){
    var grand_total = parseFloat($("#without_reound_off_grand_total").val());
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
function reCalculateTable(){
    $("#sales_table_body")
        .find('input[name^="item_tax_id"]')
        .each(function () {
            var row = $(this).closest("tr");
            //if(type_of_supply == 'export_without_payment') row.find('[name=item_tax]').val('').change();
            calculateTable(row);
        });
}
//function to add new row to datatable
function add_row(data) {
    var flag = 0;
    var item_id = data.item_id;
    var item_type = data.item_type;
    var branch_country_id = data.branch_country_id;
    var branch_state_id = data.branch_state_id;
    var branch_id = data.branch_id;
    if (item_type == "service") {
        var item_code = data[0].service_code;
        var item_name = data[0].service_name;
        var item_hsn_sac_code = data[0].service_hsn_sac_code;
        var item_details = data[0].service_details;
        var item_price = precise_amount(data[0].service_price);
        var item_tax_id = data[0].service_tax_id;
        var item_tax_percentage = precise_amount(data[0].service_tax_value);
        var product_batch = data[0].product_batch;        
    } else {
        var item_code = data[0].product_code;
        var item_name = data[0].product_name;
        var item_hsn_sac_code = data[0].product_hsn_sac_code;
        var item_details = data[0].product_details;
        var item_price = precise_amount(data[0].product_price);
        var item_tax_id = data[0].product_tax_id;
        var item_tax_percentage = precise_amount(data[0].product_tax_value);
        var product_batch = data[0].product_batch;
    }
    
    var billing_state_id = $("#billing_state").val();
    var billing_country_id = $("#billing_country").val();
    var type_of_supply = $("#type_of_supply").val();
    var item_discount = 0;
    table_index = +table_index;
    var input_type = "text";
    var display_style = "";
    var color_style = "style='color:red;'";
    if (settings_item_editable == "no") {
        input_type = "hidden";
        display_style = "style='display:none;'";
        color_style = "style='color:red;display:none;'";
    }
    var discount_exist = 0;
    var tax_exist = 0;
    if (settings_discount_visible != "no") {
        discount_exist = 1;
        var select_discount = "";
        if (input_type == "hidden") {
            select_discount +=
                '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            select_discount +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
        }
        select_discount +=
            '<select class="form-control open_discount form-fixer select2" name="item_discount" style="width: 100%;">';
        select_discount += '<option value="">Select</option>';
        for (a = 0; a < data.discount.length; a++) {
            select_discount +=
                '<option value="' +
                data.discount[a].discount_id +
                "-" +
                parseFloat(data.discount[a].discount_value) +
                '" >' +
                parseFloat(data.discount[a].discount_value) +
                "%</option>";
        }
        select_discount += "</select></div>";
    }
    if (settings_tax_type != "no_tax") {
        tax_exist = 1;
        var select_tax = "";
        if (input_type == "hidden") {
            select_tax +=
                '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            select_tax +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
        }
        var gst_disable = ''; 
        if(type_of_supply == 'export_without_tax'){
            item_tax_id = '0';
            gst_disable = 'disabled';
        } 
        
        
        select_tax +=
            '<select class="form-control open_tax form-fixer select2" name="item_tax" style="width: 100%;" '+gst_disable+'>';
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
            if (item_tax_id == data.tax[a].tax_id) {
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
    }
    var newRow = $("<tr id=" + table_index + ">");
    var cols = "";
    cols +=
        "<td><a class='deleteRow'> <img src='" +
        base_url +
        "assets/images/bin_close.png' /> </a><input type='hidden' name='item_key_value'  value=" +
        table_index +
        "><input type='hidden' name='item_id' value=" +
        item_id +
        "><input type='hidden' name='item_type' value=" +
        item_type +
        "><input type='hidden' name='item_code' value='" +
        item_code +
        "'></td>";
    if (item_type == "product" || item_type == "product_inventory")
        cols += "<td>" + item_name + "<br>(P) HSN/SAC:" + item_hsn_sac_code + "<br>"+product_batch+"</td>";
    else cols += "<td>" + item_name + "<br>(S) HSN/SAC:" + item_hsn_sac_code + "</td>";
    if (settings_description_visible == "yes") {
        cols +=
            "<td>" +
            "<input type='text' class='form-control form-fixer' name='item_description' ></td>";
    }
    cols +=
        "<td style='text-align:center'><input type='text' class='form-control form-fixer text-center float_number' value='1' data-rule='quantity' name='item_quantity'></td>";
    if (input_type == "hidden") {
        cols +=
            "<td style='text-align:center'>" +
            "<span id='item_price_hide_lbl_" +
            table_index +
            "' class='text-center' >" +
            item_price +
            "</span>";
    } else {
        cols += "<td>";
    }
    cols +=
        "<input type='" +
        input_type +
        "' class='form-control form-fixer text-right float_number' name='item_price' value='" +
        item_price +
        "'>" +
        "<span id='item_sub_total_lbl_" +
        table_index +
        "' class='pull-right' " +
        display_style +
        "  >" +
        item_price +
        "</span>" +
        "<input type='hidden' class='form-control form-fixer text-right' style='' value='" +
        item_price +
        "' name='item_sub_total'>" +
        "</td>";
    if (discount_exist == 1) {
        if (input_type == "hidden") {
            cols +=
                "<td style='text-align:center'>" +
                "<span id='item_discount_hide_lbl_" +
                table_index +
                "' class='text-center' >0.00</span>";
        } else {
            cols += "<td>";
        }
        cols +=
            "<input type='hidden' name='item_discount_id' value='0'>" +
            "<input type='hidden' name='item_discount_percentage' value='0'>" +
            "<input type='hidden' name='item_discount_amount' value='0'>" +
            select_discount +
            "<span id='item_discount_lbl_" +
            table_index +
            "' class='pull-right' " +
            color_style +
            " >0.00</span>" +
            "</td>";
    }
    //taxable area
    if (tax_exist == 1) {
      //  if (discount_exist == 1) {
            cols +=
                "<td style='text-align:center'><input type='hidden' name='item_taxable_value' value='" +
                item_price +
                "' ><span id='item_taxable_value_lbl_" +
                table_index +
                "'>" +
                item_price +
                "</span></td>";
       // }
    }
   
    //tax area
    if (tax_exist == 1) {
        if (input_type == "hidden") {
            cols +=
                "<td style='text-align:center'>" +
                "<span id='item_tax_percentage_hide_lbl_" +
                table_index +
                "' class='text-center' >0.00</span><br/>";
        } else {
            cols += "<td>";
        }
        cols +=
            "<input type='hidden' name='item_tax_id' value='0'>" +
            "<input type='hidden' name='item_tax_percentage' value='0'>" +
            "<input type='hidden' name='item_tax_amount' value='0'>" +
            "<input type='hidden' name='item_tax_amount_cgst' value='0'>" +
            "<input type='hidden' name='item_tax_amount_sgst' value='0'>" +
            "<input type='hidden' name='item_tax_amount_igst' value='0'>" +
             "<input type='hidden' name='item_percentage_cgst' value='0'>" +
            "<input type='hidden' name='item_percentage_sgst' value='0'>" +
            "<input type='hidden' name='item_percentage_igst' value='0'>" +
            select_tax +
            "<span id='item_tax_lbl_" +
            table_index +
            "' class='pull-right' style='color:red;'>0.00</span>" +
            "</td>";
        if (input_type == "hidden") {
            cols +=
                "<td style='text-align:center'>" +
                "<span id='item_tax_percentage_hide_lbl_" +
                table_index +
                "' class='text-center' >0.00</span><br/>";
        } else {
            cols += "<td>";
        }
        cols +=
            "<input type='hidden' name='item_tax_cess_id' value='0'>" +
            "<input type='hidden' name='item_tax_cess_percentage' value='0'>" +
            "<input type='hidden' name='item_tax_cess_amount' value='0'>" +
            cess_select +
            "<span id='item_tax_cess_lbl_" +
            table_index +
            "' class='pull-right' style='color:red;'>0.00</span>" +
            "</td>";
    }
    //tax area
    if (input_type == "hidden") {
        cols +=
            "<td style='text-align:center'>" +
            "<span id='item_grand_total_hide_lbl_" +
            table_index +
            "' class='text-center' >0.00</span>";
    } else {
        cols += "<td>";
    }
    cols +=
        "<input type='" +
        input_type +
        "' class='float_number form-control form-fixer text-right' name='item_grand_total'></td>";
    cols += "</tr>";
    newRow.append(cols);
    $("#sales_table_body").prepend(newRow);
    table_index++;
    calculateTable(newRow);
    $("#type_of_supply").change();
    $(".select2").select2();
}
//calculate row
function calculateRow(row) {
    // var key = +row.index();
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_price = +parseFloat(row.find('input[name^="item_price"]').val());
    var item_quantity = +row.find('input[name^="item_quantity"]').val();
    var item_sub_total = parseFloat(item_price * item_quantity);
    row.find('input[name^="item_sub_total"]').val(item_sub_total);
    row.find("#item_sub_total_lbl_" + table_index).text(item_sub_total);
    if (settings_item_editable == "no") {
        row.find("#item_price_hide_lbl_" + table_index).text(item_sub_total);
    }
}
// calculate row
//calculate Discount
function calculateDiscount(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var discount_data = row.find('select[name^="item_discount"]').val();
    var discount_split = discount_data.toString().split("-");
    var item_discount_id = +discount_split[0];
    var item_discount = +parseFloat(discount_split[1]);
    if (item_discount == "" || isNaN(item_discount)) {
        item_discount = 0;
    }
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_discount_amount = (item_sub_total * item_discount) / 100;
    row.find('input[name^="item_discount_amount"]').val(item_discount_amount);
    row.find('input[name^="item_discount_id"]').val(item_discount_id);
    row.find('input[name^="item_discount_percentage"]').val(item_discount);
    row.find("#item_discount_lbl_" + table_index).text(item_discount_amount);
    if (settings_item_editable == "no") {
        row
            .find("#item_discount_hide_lbl_" + table_index)
            .text(item_discount_amount);
    }
}
function calculateTax(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var tax_data = row.find('select[name="item_tax"]').val();
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
    var type_of_supply = $("#type_of_supply").val();
    
    if (type_of_supply == "export_without_payment") {
        item_tax_id = 0;
        item_tax = 0;
        item_tax_cess_id = item_tax_cess =0;
        /*row.find('[name=item_tax]').val('').change();*/
    }
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_discount_amount = +parseFloat(
        row.find('input[name^="item_discount_amount"]').val()
    );
    var item_taxable_value = item_sub_total;
    if (typeof item_discount_amount != "undefined" && !isNaN(item_discount_amount)) {
        item_taxable_value = item_sub_total - item_discount_amount;
        row.find("#item_taxable_value_lbl_" + table_index).text(item_taxable_value);
        row.find('input[name^="item_taxable_value"]').val(item_taxable_value);
    }else{
        row.find("#item_taxable_value_lbl_" + table_index).text(item_taxable_value);
        row.find('input[name^="item_taxable_value"]').val(item_taxable_value); 
    }
    var item_tax_amount = item_taxable_value * item_tax;
     var item_tax_amount = item_tax_amount / 100;
   
    var item_tax_cess_amount = item_taxable_value * item_tax_cess;
    var item_tax_cess_amount = item_tax_cess_amount / 100;
     
    var other_tax_amount = $('#total_other_taxable_amount').val();
    if(other_tax_amount == '' || typeof other_tax_amount == 'undefined' )
        other_tax_amount = 0;
    if(settings_tax_type == 'gst'){
        var igst_X_percentage = 0;
        var cgst_percentage = 0;
        var sgst_percentage = 0;
        var cgst_amount_percentage = parseFloat(settings_tax_percentage);
        var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
        var sales_igst_amount = 0; 
        var sales_cgst_amount = 0;
        var sales_sgst_amount = 0;
        var txable_amount = (parseFloat(other_tax_amount) + parseFloat(item_tax_amount));
        //if(branch_country_id == $('#billing_country').val()){
        if($('#type_of_supply').val() == 'regular'){
            console.log(branch_state_id );
            if(branch_state_id == $('#billing_state').val()){
                sales_igst_amount = 0;
                sales_cgst_amount = (txable_amount * cgst_amount_percentage)/100;
                sales_sgst_amount = (txable_amount * sgst_amount_percentage)/100;
                var igst_X_percentage = 0;
                var cgst_percentage = item_tax * (cgst_amount_percentage / 100);
               var sgst_percentage =  item_tax * (sgst_amount_percentage / 100);          
            }else{
                sales_igst_amount = txable_amount;
                sales_cgst_amount = 0;
                sales_sgst_amount = 0;
                var igst_X_percentage = item_tax;
               var cgst_percentage = 0;
               var sgst_percentage = 0;   
              ////  item_tax_cess_amount = 0;
            }
        }else{
            /*if($('#type_of_supply').val() == 'export_with_payment'){
                sales_igst_amount = txable_amount;
                sales_cgst_amount = 0;
                sales_sgst_amount = 0;
                //item_tax_cess_amount= 0;
                var igst_X_percentage = 0;
                var cgst_percentage = 0;
                var sgst_percentage = 0;  
            }*/
            sales_igst_amount = txable_amount;
            sales_cgst_amount = 0;
            sales_sgst_amount = 0;
            var igst_X_percentage = item_tax;
            var cgst_percentage = 0;
            var sgst_percentage = 0; 
        }
    }
    
    item_tax_amount = item_tax_amount || 0;
    item_tax_cess_amount = item_tax_cess_amount || 0;
    sales_igst_amount = sales_igst_amount || 0;
    sales_sgst_amount = sales_sgst_amount || 0;
    sales_cgst_amount = sales_cgst_amount || 0;
    igst_X_percentage = igst_X_percentage || 0;
    cgst_percentage = cgst_percentage || 0;
    sgst_percentage = sgst_percentage || 0;
    item_tax = item_tax || 0;
    item_tax_cess = item_tax_cess || 0;
    item_tax_amount = item_tax_amount || 0;
    row.find('input[name="item_tax_amount"]').val(item_tax_amount);
    row.find('input[name="item_tax_cess_amount"]').val(item_tax_cess_amount);
    row.find('input[name^="item_tax_amount_igst"]').val(sales_igst_amount);
    row.find('input[name^="item_tax_amount_sgst"]').val(sales_sgst_amount);
    row.find('input[name^="item_percentage_igst"]').val(igst_X_percentage);
    row.find('input[name^="item_percentage_cgst"]').val(cgst_percentage);
    row.find('input[name^="item_percentage_sgst"]').val(sgst_percentage);    
    row.find('input[name^="item_tax_amount_cgst"]').val(sales_cgst_amount);
    row.find('input[name^="item_tax_id"]').val(item_tax_id);
    row.find('input[name^="item_tax_cess_id"]').val(item_tax_cess_id);
    row.find('input[name^="item_tax_percentage"]').val(item_tax);
    row.find('input[name^="item_tax_cess_percentage"]').val(item_tax_cess);
    row.find("#item_tax_lbl_" + table_index).text(item_tax_amount);
    row.find("#item_tax_cess_lbl_" + table_index).text(item_tax_cess_amount);
    if (settings_item_editable == "no") {
        row
            .find("#item_tax_percentage_hide_lbl_" + table_index)
            .text(item_tax + " %");
    }
}
function calculateTds(row) {
    var table_index = +row.find('input[name="item_key_value"]').val();
    
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_discount_amount = +parseFloat(
        row.find('input[name^="item_discount_amount"]').val()
    );
    var item_taxable_value = item_sub_total;
    if (
        typeof item_discount_amount != "undefined" &&
        !isNaN(item_discount_amount)
    ) {
        item_taxable_value = item_sub_total - item_discount_amount;
    }
    
}
function calculateRowTotal(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_discount_amount = +parseFloat(
        row.find('input[name^="item_discount_amount"]').val()
    );
    var item_tax_amount = +parseFloat(
        row.find('input[name="item_tax_amount"]').val()
    );
    var item_tax_cess_amount = +parseFloat(
        row.find('input[name="item_tax_cess_amount"]').val()
    );
    var item_taxable_value = +parseFloat(
        row.find('input[name^="item_taxable_value"]').val()
    );
   
    if (typeof item_tax_amount === "undefined" || isNaN(item_tax_amount)) {
        item_tax_amount = 0;
    }
    if (typeof item_tax_cess_amount === "undefined" || isNaN(item_tax_cess_amount)) {
        item_tax_amount = 0;
    }
    var item_grand_total = item_taxable_value + item_tax_amount + item_tax_cess_amount;
    row.find('input[name^="item_grand_total"]').val(item_grand_total);
    if (settings_item_editable == "no") {
        row.find("#item_grand_total_hide_lbl_" + table_index).text(item_grand_total);
    }
}
function preciseRowAmount(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_price = +parseFloat(row.find('input[name^="item_price"]').val());
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_discount_amount = +parseFloat(
        row.find('input[name^="item_discount_amount"]').val()
    );
    var item_tax_amount = +parseFloat(
        row.find('input[name="item_tax_amount"]').val()
    );
    var item_tax_cess_amount = +parseFloat(
        row.find('input[name="item_tax_cess_amount"]').val()
    );
    var item_tax_amount_cgst = +parseFloat(
        row.find('input[name^="item_tax_amount_cgst"]').val()
    );
    var item_tax_amount_sgst = +parseFloat(
        row.find('input[name^="item_tax_amount_sgst"]').val()
    );
    var item_tax_amount_igst = +parseFloat(
        row.find('input[name^="item_tax_amount_igst"]').val()
    );
    var item_taxable_value = +parseFloat(
        row.find('input[name^="item_taxable_value"]').val()
    );
    var item_grand_total = +parseFloat(
        row.find('input[name^="item_grand_total"]').val()
    );
    var item_tds_amount = +parseFloat(
        row.find('input[name^="item_tds_amount"]').val()
    );
    if (typeof item_taxable_value !== "undefined" && !isNaN(item_taxable_value)) {
        row
            .find('input[name^="item_taxable_value"]')
            .val(precise_amount(item_taxable_value));
        row
            .find("#item_taxable_value_lbl_" + table_index)
            .text(precise_amount(item_taxable_value));
    }
    if (typeof item_tax_amount !== "undefined" && !isNaN(item_tax_amount)) {
        row
            .find('input[name="item_tax_amount"]')
            .val(precise_amount(item_tax_amount));
        row
            .find("#item_tax_lbl_" + table_index)
            .text(precise_amount(item_tax_amount));
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
    
    if (
        typeof item_discount_amount !== "undefined" &&
        !isNaN(item_discount_amount)
    ) {
        row
            .find('input[name^="item_discount_amount"]')
            .val(precise_amount(item_discount_amount));
        row
            .find("#item_discount_lbl_" + table_index)
            .text(precise_amount(item_discount_amount));
        if (settings_item_editable == "no") {
            row
                .find("#item_discount_hide_lbl_" + table_index)
                .text(precise_amount(item_discount_amount));
        }
    }
    row.find('input[name^="item_price"]').val(precise_amount(item_price));
    row.find('input[name^="item_sub_total"]').val(precise_amount(item_sub_total));
    row
        .find("#item_sub_total_lbl_" + table_index)
        .text(precise_amount(item_sub_total));
    row
        .find('input[name^="item_grand_total"]')
        .val(precise_amount(item_grand_total));
    if (settings_item_editable == "no") {
        row
            .find("#item_price_hide_lbl_" + table_index)
            .text(precise_amount(item_sub_total));
        row
            .find("#item_grand_total_hide_lbl_" + table_index)
            .text(precise_amount(item_grand_total));
    }
}
function generateJson(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_price = +parseFloat(row.find('input[name^="item_price"]').val());
    var item_description = row.find('input[name^="item_description"]').val();
    var item_quantity = +row.find('input[name^="item_quantity"]').val();
    var item_id = +row.find('input[name^="item_id"]').val();
    var item_type = row.find('input[name^="item_type"]').val();
    var item_code = row.find('input[name^="item_code"]').val();
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_discount_amount = +parseFloat(
        row.find('input[name^="item_discount_amount"]').val()
    );
    var item_tax_amount = +parseFloat(
        row.find('input[name="item_tax_amount"]').val()
    );
    var item_tax_cess_amount = +parseFloat(
        row.find('input[name="item_tax_cess_amount"]').val()
    );
    var item_discount_id = +row.find('input[name^="item_discount_id"]').val();
    var item_tax_id = +row.find('input[name^="item_tax_id"]').val();
    var item_tax_cess_id = +row.find('input[name^="item_tax_cess_id"]').val();
    var item_tds_id = row.find('select[name=item_tds_percentage] option:selected').attr('tds_id');
    var item_tds_percentage = +parseFloat(
        row.find('[name^="item_tds_percentage"]').val()
    );
    
     var item_percentage_igst = row.find('input[name^="item_percentage_igst"]').val();
     var item_percentage_cgst =row.find('input[name^="item_percentage_cgst"]').val();
     var item_percentage_sgst =row.find('input[name^="item_percentage_sgst"]').val();  
    var item_tax_amount_cgst =row.find('input[name^="item_tax_amount_cgst"]').val(); 
    var item_tax_amount_sgst =row.find('input[name^="item_tax_amount_sgst"]').val(); 
    var item_tax_amount_igst =row.find('input[name^="item_tax_amount_igst"]').val(); 
    var item_tds_amount = +parseFloat(
        row.find('input[name^="item_tds_amount"]').val()
    );
    var item_tds_type = row.find('select[name=item_tds_percentage] option:selected').attr('type');
    var item_discount_percentage = +parseFloat(
        row.find('input[name^="item_discount_percentage"]').val()
    );
    var item_tax_percentage = +parseFloat(
        row.find('input[name^="item_tax_percentage"]').val()
    );
    var item_tax_cess_percentage = +parseFloat(
        row.find('input[name^="item_tax_cess_percentage"]').val()
    );
    var item_taxable_value = +parseFloat(
        row.find('input[name^="item_taxable_value"]').val()
    );
    var item_grand_total = +parseFloat(
        row.find('input[name^="item_grand_total"]').val()
    );
    if (typeof item_taxable_value === "undefined" || isNaN(item_taxable_value)) {
        item_taxable_value = item_sub_total;
    }
    if (typeof item_tds_amount === "undefined" || isNaN(item_tds_amount)) {
        item_tds_amount = 0;
        item_tds_id = 0;
        item_tds_percentage = 0;
    }
    if (typeof item_tax_amount === "undefined" || isNaN(item_tax_amount)) {
        item_tax_amount = 0;
        item_tax_id = 0;
        item_tax_percentage = 0;
    }
    if (typeof item_tax_cess_amount === "undefined" || isNaN(item_tax_cess_amount)) {
        item_tax_cess_amount = 0;
        item_tax_cess_id = 0;
        item_tax_cess_percentage = 0;
    }
    if (typeof item_discount_amount === "undefined" || isNaN(item_discount_amount)) {
        item_discount_amount = 0;
        item_discount_id = 0;
        item_discount_percentage = 0;
    }
    var data_item = {
        item_key_value: +table_index,
        item_id: +item_id,
        item_type: item_type,
        item_code: item_code,
        item_quantity: +item_quantity,
        item_price: +item_price,
        item_description: item_description,
        item_sub_total: +item_sub_total,
        item_discount_amount: +item_discount_amount,
        item_discount_id: +item_discount_id,
        item_discount_percentage: +item_discount_percentage,
        item_tax_amount: +item_tax_amount,
        item_tax_id: +item_tax_id,
        item_tax_percentage: +item_tax_percentage,
        item_tax_cess_amount: +item_tax_cess_amount,
        item_tax_cess_id: +item_tax_cess_id,
        item_tax_cess_percentage: +item_tax_cess_percentage,
        item_tds_amount: +item_tds_amount,
        item_tds_id: +item_tds_id,
        item_tds_percentage: +item_tds_percentage,
        item_tds_type: item_tds_type,
        item_taxable_value: +item_taxable_value,
        item_grand_total: +item_grand_total,
        item_percentage_igst: +item_percentage_igst,
        item_percentage_cgst: +item_percentage_cgst,
        item_percentage_sgst: +item_percentage_sgst,
        item_tax_amount_cgst: +item_tax_amount_cgst,
        item_tax_amount_sgst: +item_tax_amount_sgst,
        item_tax_amount_igst: +item_tax_amount_igst
    };
    var flag = 0;
    var i_val = "";
    for (var i = 0; i < sales_data.length; i++) {
        if (
            sales_data[i] !== "undefined" &&
            sales_data[i].item_key_value == table_index
        ) {
            flag = 1;
            i_val = i;
            break;
        }
    }
    if (flag == 1) {
        sales_data[i_val] = data_item;
    } else {
        sales_data.push(data_item);
    }
    var table_data = JSON.stringify(sales_data);
    $("#table_data").val(table_data);
}
function calculateTable(row) {
    calculateRow(row);
    if (settings_discount_visible != "no") {
        calculateDiscount(row);
    }
    if (settings_tax_type != "no_tax") {
        calculateTax(row);
    }
    if (settings_tds_visible != "no") {
        calculateTds(row);
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
    $("#sales_table_body")
        .find('input[name^="item_sub_total"]')
        .each(function () {
            sub_total += +$(this).val();
        });
    $("#sales_table_body")
        .find('input[name^="item_taxable_value"]')
        .each(function () {
            taxable += +$(this).val();
        });
    $("#sales_table_body")
        .find('input[name^="item_discount_amount"]')
        .each(function () {
            dflag = 1;
            discount += +$(this).val();
        });
    $("#sales_table_body")
        .find('input[name="item_tax_amount"]')
        .each(function () {
            tflag = 1;
            tax += +$(this).val();
        });
    $("#sales_table_body")
        .find('input[name="item_tax_cess_amount"]')
        .each(function () {
            tflag = 1;
            tax_cess += +$(this).val();
        });
    $("#sales_table_body")
        .find('input[name="item_tax_amount_cgst"]')
        .each(function () {           
            tflag = 1;
            tax_cgst += +$(this).val();
        });
    $("#sales_table_body")
        .find('input[name="item_tax_amount_sgst"]')
        .each(function () {            
            tflag = 1;
            tax_sgst += +$(this).val();
        });
    $("#sales_table_body")
        .find('input[name="item_tax_amount_igst"]')
        .each(function () {
            tflag = 1;
            tax_igst += +$(this).val();
        });
    
    $("#sales_table_body")
        .find('input[name^="item_grand_total"]')
        .each(function () {
            grand_total += +$(this).val();
        });
    var total_other_amount = +$("#total_other_amount").val();
    var final_grand_total = grand_total + total_other_amount;
    var tax_value = tax_cess + tax;
    $('#without_reound_off_grand_total').val(final_grand_total);
    $("#total_sub_total").val(precise_amount(sub_total));
    $("#total_taxable_amount").val(precise_amount(taxable));
    $("#total_discount_amount").val(precise_amount(discount));
    $("#total_tax_amount").val(precise_amount(tax_value));
    $("#total_tax_cess_amount").val(precise_amount(tax_cess));
    $("#total_igst_amount").val(precise_amount(tax_igst));
    $("#total_cgst_amount").val(precise_amount(tax_cgst));
    $("#total_sgst_amount").val(precise_amount(tax_sgst));
    $("#total_other_amount").val(precise_amount(total_other_amount));
    $("#total_grand_total").val(precise_amount(final_grand_total));
    $("#totalSubTotal").text(precise_amount(sub_total));
    if (settings_discount_visible == "yes" && discount > 0) {
        $('.totalDiscountAmount_tr').show();
        $("#totalDiscountAmount").text(precise_amount(discount));
    }else{
        $('.totalDiscountAmount_tr').hide();
    }
    if (settings_tax_type != "no_tax") {       
        $("#totalTaxAmount").text(precise_amount(tax));
        
        if(tax_sgst > 0){
            var lbl = 'SGST (+)';
            if($('#billing_state option:selected').attr('utgst') == '1'){                
                lbl = 'UTGST (+)';
            }            
            $('.totalSGSTAmount_tr').show();
            $('.totalSGSTAmount_tr').find('td:first').text(lbl);
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
    
    $("#totalGrandTotal").text(precise_amount(final_grand_total));
    if (common_settings_round_off == "yes") {
        $("input[type=radio][name=round_off_key].minimal").trigger("ifChanged");
    }
    //CalculateRoundOff();
}
// Delete function
function deleteRow(row) {
    var table_index_key = +row.find('input[name^="item_key_value"]').val();
    var j = 0;
    var sales_data_temp = new Array();
    for (var i = 0; i < sales_data.length; i++) {
        if (
            sales_data[i] !== "undefined" &&
            sales_data[i].item_key_value != table_index_key
        ) {
            // sales_data.splice(i, 1);
            var data_item = {
                item_key_value: +sales_data[i].item_key_value,
                item_id: +sales_data[i].item_id,
                item_type: sales_data[i].item_type,
                item_code: sales_data[i].item_code,
                item_quantity: +sales_data[i].item_quantity,
                item_price: +sales_data[i].item_price,
                item_description: sales_data[i].item_description,
                item_sub_total: +sales_data[i].item_sub_total,
                item_discount_amount: +sales_data[i].item_discount_amount,
                item_discount_id: +sales_data[i].item_discount_id,
                item_discount_percentage: +sales_data[i].item_discount_percentage,
                item_tax_amount: +sales_data[i].item_tax_amount,
                item_tax_id: +sales_data[i].item_tax_id,
                item_tax_percentage: +sales_data[i].item_tax_percentage,
                item_tds_amount: +sales_data[i].item_tds_amount,
                item_tds_id: +sales_data[i].item_tds_id,
                item_tds_percentage: +sales_data[i].item_tds_percentage,
                item_tds_type: +sales_data[i].item_tds_type,
                item_taxable_value: +sales_data[i].item_taxable_value,
                item_grand_total: +sales_data[i].item_grand_total,
                item_tax_cess_amount: +sales_data[i].item_tax_cess_amount,
                item_tax_cess_id: +sales_data[i].item_tax_cess_id,
                item_tax_cess_percentage: +sales_data[i].item_tax_cess_percentage,
                item_percentage_igst: +sales_data[i].item_percentage_igst,
                item_percentage_cgst: +sales_data[i].item_percentage_cgst,
                item_percentage_sgst: +sales_data[i].item_percentage_sgst,
                item_tax_amount_cgst: +sales_data[i].item_tax_amount_cgst,
                item_tax_amount_sgst: +sales_data[i].item_tax_amount_sgst,
                item_tax_amount_igst: +sales_data[i].item_tax_amount_igst
            };
            sales_data_temp.push(data_item);
        }
    }
    sales_data = new Array();
    sales_data.length = 0;
    sales_data = sales_data_temp;
    var table_data = JSON.stringify(sales_data);
    $("#table_data").val(table_data);
}
