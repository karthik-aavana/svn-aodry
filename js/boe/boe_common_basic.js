var mapping = {};
if (typeof count_data === "undefined" || count_data === null) {
    var count_data = 0;
}
var table_index = count_data;
var branch_country_id = $("#branch_country_id").val();
var branch_state_id = $("#branch_state_id").val();
var flag2 = 0;
$(document).ready(function () {
    $(document).keyup(function (event) {
        if (event.which == 13 && $(event.target)[0] != $("textarea")[0]) {
            event.preventDefault();
            return false;
        }
    });
    $("#input_purchase_code").change(function () {
        $("#input_purchase_code").trigger("click");
    });
    $("#supplier").change(function () {
        if ($(this).find("option:selected").text() != "Select Shipping Address") {
            $("#input_purchase_code").prop("disabled", false);
            $(".search_purchase_code").show();
            $("#err_purchase_code").text("");
        } else {
            $(".search_purchase_code").hide();
            $("#input_purchase_code").prop("disabled", true);
            $("#err_purchase_code").text("Please select the supplier to do purchase.");
            $("#err_product").text("");
        }
        $("#myForm input").change();
    });
    if ($("#supplier").val() != "") {
        $("#shipping_address_div").show();
    } else {
        $("#shipping_address_div").hide();
    }
    $("#supplier").change(function () {
        if ($("#supplier").val() != "") {
            $("#shipping_address_div").show();
            // $('#modal_party_name').val($('#supplier').text());
            var party_id = $("#supplier").val();
            $("#modal_party_id").val(party_id);
            $("#modal_party_type").val("supplier");
            $.ajax({
                url: base_url + "general/get_shipping_address",
                dataType: "JSON",
                method: "POST",
                data: {
                    party_id: $("#supplier").val(),
                    party_type: "supplier"
                },
                success: function (result) {
                    var data = result["shipping_address_data"];
                    $("#shipping_address").html("");
                    $("#shipping_address").append('<option value="">Select</option>');
                    for (i = 0; i < data.length; i++) {
                        var shipping_address = data[i].shipping_address;
                        var shipping_address_new = shipping_address.replace(
                            /\n\r|\\r|\\n|\r|\n|\r\n|\\n\\r|\\r\\n/g,
                            "<br/>"
                        );
                        $("#shipping_address").append(
                            '<option value="' +
                            data[i].shipping_address_id +
                            '">' +
                            shipping_address_new +
                            "</option>"
                        );
                    }
                    $("#shipping_address").select2({
                        containerCssClass: "wrap"
                    });
                    $("#shipping_address").change();
                    if(result.supplier_detail){
                        var is_utgst = result.supplier_detail.is_utgst;
                        $('#is_utgst').val(is_utgst);
                        var out_of_india = result.supplier_detail.out_of_india;
                        var opt = '';
                        if(out_of_india){
                            $('#billing_state').val('0').change();
                        }else{
                            $('#billing_state').val(result.supplier_detail.supplier_state_id).change();
                        }
                        $('#billing_country').val(result.supplier_detail.supplier_country_id).change();
                        ChangeTypeOfSupply();
                    }
                }
            });
        } else {
            $("#shipping_address_div").hide();
            $("#modal_party_id").val("");
            $("#modal_party_type").val("");
            $("#shipping_address").html('<option value="">Select Shipping Address</option>');
        }
    });
    function ChangeTypeOfSupply(){
        var table_data = $("#table_data").val();
        var state = $('#billing_state').val();
        if(state == '0'){
            flag2 = 1;
            /*$("#billing_state").html("");
            $("#billing_state").append('<option value="0">Out of Country</option>');*/
            $("#type_of_supply").html("");
            $("#type_of_supply").append(
                '<option value="export_with_payment">Export (With Tax Payment)</option>'
            );
            $("#type_of_supply").append(
                '<option value="export_without_payment" selected>Export (Without Tax Payment)</option>'
            );
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
    $("#billing_state").change(function (event) {
        ChangeTypeOfSupply();
    });

    var invoice_ary = [];
    $('#purchase_invoice').on('change',function(){
        var reference_ary = $(this).val();
        $.each(reference_ary,function(k,v){
            if(invoice_ary.indexOf(v) == -1){
                invoice_ary.push(v);
                $.ajax({
                    url: base_url + 'purchase_credit_note/get_purchase_item',
                    type: 'POST',
                    data: { purchase_id: v},
                    success: function (result) {
                        /*table_index = 0;*/
                        $('#table-total').show();
                        var data = $.parseJSON(result);
                        add_row_loop(data,v);
                        call_css();
                        $('#input_purchase_code').val('');
                        
                    }
                });
            }
        });

        if(reference_ary.length <= 0){
            $('#purchase_table_body').html('');
            invoice_ary = [];
        }else{
            removeExtra(invoice_ary,reference_ary);
        }
        
    })

    function removeExtra(invoice_ary,reference_ary){
        $.each(invoice_ary,function(k,v){
            if(reference_ary.indexOf(v) == -1){
                key = invoice_ary.indexOf(v);
                invoice_ary.splice(key, 1); 
                $('#purchase_table_body').find('tr[pid='+v+']').remove();
            }
        });
    }

    $("#invoice_date").on("changeDate", function () {
        var selected = $(this).val();
        var module_id = $("#module_id").val();
        var date_old = $("#invoice_date_old").val();
        var privilege = $("#privilege").val();
        var section_area = $("#section_area").val();
        var old_res = date_old.toString().split("-");
        var new_res = selected.toString().split("-");
        if (old_res[1] != new_res[1]) {
            $.ajax({
                url: base_url + "general/generate_date_reference",
                type: "POST",
                data: {
                    date: selected,
                    privilege: privilege,
                    module_id: module_id,
                    section_area: section_area
                },
                success: function (data) {
                    var parsedJson = $.parseJSON(data);
                    var type = parsedJson.reference_no;
                    $("#invoice_number").val(type);
                }
            });
        } else {
            var old_reference = $("#invoice_number_old").val();
            $("#invoice_number").val(old_reference);
        }
    });
    // get items to table starts
    $('#input_purchase_code').autoComplete({
        minChars: 1,
        cache: false,
        source: function (term, suggest) {
            term = term.toLowerCase();
            if (common_settings_inventory_advanced == "") {
                var inventory_advanced = "no";
            } else {
                var inventory_advanced = common_settings_inventory_advanced;
            }
            /*var item_access = $('#nature_of_supply').val();
            if (item_access == "") {
            }*/
            item_access = "product";
            var isnum = /^\d+$/.test(term);
            $.ajax({
                url: base_url + "purchase/get_purchase_suggestions/" + term + "/" + inventory_advanced + "/" + item_access,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    var suggestions = [];
                    for (var i = 0; i < data.length; ++i) {
                        suggestions.push(data[i].item_code + ' ' + data[i].item_name);
                        mapping[data[i].item_code] = data[i].item_id + '-' + data[i].item_type + '-' + settings_discount_visible + '-' + settings_tax_type;
                    }
                    if (i == 1 && term.length > 7 && isnum == true) {
                        $.ajax({
                            url: base_url + 'purchase/get_table_items/' + mapping[data[0].item_code],
                            type: "GET",
                            dataType: "JSON",
                            success: function (data1) {
                                $('#table-total').show();
                                add_row(data1);
                                call_css();
                                $('#input_purchase_code').val('')
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
                url: base_url + 'purchase/get_table_items/' + mapping[code[0]],
                type: "GET",
                dataType: "JSON",
                success: function (data) {
                    $('#table-total').show();
                    add_row(data);
                    call_css();
                    $('#input_purchase_code').val('')
                }
            })
        }
    });
    // get items to table ends
    //delete rows
    $("#purchase_table_body").on("click", "a.deleteRow", function (event) {
        var newRow = $(this).closest("tr");
        deleteRow(newRow);
        $(this).closest("tr").remove();
        reCalculateTable();
    });
    // changes in rows
    $("#purchase_table_body").on(
        "change blur",
        'input[name="item_price"],input[name="item_quantity"],input[name="bcd_tax"],input[name="item_tax"],input[name="item_tax_cess"],input[name="other_duties_tax"]',
        function (event) {
            if($(this).val() < 0) $(this).val(0);
            var newRow = $(this).closest("tr");
            calculateTable(newRow);
        }
    );
    /*$("#purchase_table_body").on(
        "change",
        'input[name="item_tds_percentage"]',
        function (event) {
            var newRow = $(this).closest("tr");
            var item_tds_id = newRow.find('input[name^="item_tds_percentage"] option:selected').attr('tds_id');
            var item_tds_type = newRow.find('input[name^="item_tds_percentage"] option:selected').attr('type');
          
            newRow.find('input[name=item_tds_id]').val(item_tds_id);
            newRow.find('input[name=item_tds_type]').val(item_tds_type);
            calculateTable(newRow);
        }
    );*/
    $("#purchase_table_body").on(
        "change",
        'input[name="item_tax_cess"]',
        function (event) {
            var newRow = $(this).closest("tr");
            calculateTable(newRow);
        }
    );
    //reverse calculations
    $("#purchase_table_body").on(
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
            /*if (settings_tds_visible != "no") {
                item_tds_amount = +parseFloat(
                    row.find('input[name^="item_tds_amount"]').val()
                );
                item_tds_percentage = +parseFloat(
                    row.find('[name="item_tds_percentage"]').val()
                );
                var item_tds_type = row.find('input[name^="item_tds_type"]').val();
                if (item_tds_type != "TCS" || item_tds_type != "tcs") {
                    item_tds_amount = 0;
                    item_tds_percentage = 0;
                }
            }*/
            var item_quantity = +row.find('input[name^="item_quantity"]').val();
            var item_taxable_value =
                (+item_grand_total * 100) /
                (100 + (+item_tax_percentage + +item_tax_cess_percentage));
            var item_sub_total =
                (+item_taxable_value * 100) / (100);
            var item_price = item_sub_total / item_quantity;
            row.find('input[name^="item_price"]').val(item_price);
            calculateTable(row);
        }
    );
    /*$("input[type=radio][name=round_off_key].minimal").on(
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
                    $("#round_off_select").attr("style", "display: none");
                }
            }
            CalculateRoundOff();
        }
    );
    $(document).on('change','[name=round_off_value]',function(){
        CalculateRoundOff();
    })*/
    $(document).on('change','[name=type_of_supply]',function(){
        var type_of_supply = $(this).val();
        if(type_of_supply != 'regular'){
            $('.gst_payable_div').hide();
        }else{
            $('.gst_payable_div').show();
        }
        reCalculateTable();
    }) 
});

function add_row_loop(data,p_id) {

    for (var i = 0; i < data.items.length; i++) {

        data.items[i].module_type = data.items[i].tds_module_type;

        var temp = [data.items[i]];
        temp.item_id = data.items[i].item_id;
        temp.purchase_id = p_id;
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

function reCalculateTable(){
    $("#purchase_table_body")
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
    var purchase_id = data.purchase_id;
    if (item_type == "service") {
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
        var item_code = '';
        if(data[0].product_code) item_code = data[0].product_code;
        var item_name = data[0].product_name;
        var item_hsn_sac_code = data[0].product_hsn_sac_code;
        var item_details = data[0].product_details;
        var item_price = precise_amount(data[0].product_price);
        var item_tax_id = data[0].product_tax_id;
        var item_tax_percentage = precise_amount(data[0].product_tax_value);
        var item_tds_id = data[0].product_tds_id;
        var item_tds_percentage = precise_amount(data[0].product_tds_value);
       /* var item_tds_type = data[0].module_type;*/
        var item_tds_type = 'TCS';
    }
    if (item_tds_id == "") {
        item_tds_id = 0;
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
    var tax_exist = 0;
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
        
        select_tax += "<input type='number' class='form-control' name='item_tax' value='0'></div>";
        var cess_select = "";
        if (input_type == "hidden") {
            cess_select +=
                '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            cess_select +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
        }
        cess_select += "<input type='number' class='form-control' name='item_tax_cess' value='0'></div>";
        /*cess_select +=
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
                    precise_amount(data.tax[a].tax_value) +
                    '" ' +
                    selected +
                    " >" +
                    precise_amount(data.tax[a].tax_value) +
                    "</option>";
            }
        }
        cess_select += "</select></div>";*/
    }
    var newRow = $("<tr id=" + table_index + " pid='"+purchase_id+"'>");
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
        cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";
    else cols += "<td>" + item_name + "<br>HSN/SAC:" + item_hsn_sac_code + "</td>";
    
    cols +=
        "<td style='text-align:center'><input type='text' class='form-control form-fixer text-center float_number' value='1' data-rule='quantity' name='item_quantity' readonly></td>";
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
    //tax area
    cols += "<td style='text-align:center'><input type='hidden' name='item_bcd_amount' value='0'>\n\
            <input type='number' class='form-control' name='bcd_tax' value='0'></div>\n\
            <span id='item_bcd_lbl_" +table_index +"' class='pull-right' style='color:red;'>0.00</span></td>";
    if (tax_exist == 1) {
        cols += "<input type='hidden' name='item_taxable_value' value='" +
                item_price +"' >";
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
            "<input type='hidden' name='item_tax_amount_igst' value='0'>" +
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

    cols += "<td style='text-align:center'><input type='hidden' name='item_other_duties_amount' value='0'>\n\
            <input type='number' class='form-control' name='other_duties_tax' value='0'></div>\n\
            <span id='item_other_duties_lbl_" +table_index +"' class='pull-right' style='color:red;'>0.00</span></td>";
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
    $("#purchase_table_body").prepend(newRow);
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
    var discount_data = row.find('input[name^="item_discount"]').val();
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
function calculateBcd(row){
    var table_index = +row.find('input[name^="item_key_value"]').val();
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    var item_taxable_value = item_sub_total;
    item_taxable_value = item_sub_total;
    row.find('input[name^="item_taxable_value"]').val(item_taxable_value);
    var bcd_tax = row.find('input[name="bcd_tax"]').val();
    if (bcd_tax == "" || isNaN(bcd_tax)) bcd_tax = 0;
    var item_bcd_amount = (item_taxable_value * bcd_tax) / 100;
    row.find('input[name="item_bcd_amount"]').val(item_bcd_amount);
    row.find("#item_bcd_lbl_" + table_index).text(item_bcd_amount);

    var other_duties_tax = row.find('input[name="other_duties_tax"]').val();
    if (other_duties_tax == "" || isNaN(other_duties_tax)) other_duties_tax = 0;
    var item_other_duties_amount = (item_taxable_value * other_duties_tax) / 100;
    row.find('input[name="item_other_duties_amount"]').val(item_other_duties_amount);
    row.find("#item_other_duties_lbl_" + table_index).text(item_other_duties_amount);
}
function calculateTax(row) {
    var item_sub_total = +parseFloat(
        row.find('input[name^="item_sub_total"]').val()
    );
    /*var item_discount_amount = +parseFloat(
        row.find('input[name^="item_discount_amount"]').val()
    );*/
    var item_taxable_value = item_sub_total;
    item_taxable_value = item_sub_total;
    row.find('input[name^="item_taxable_value"]').val(item_taxable_value);
    
    if (settings_tax_type != "no_tax") {
        var table_index = +row.find('input[name^="item_key_value"]').val();
        var tax_data = row.find('input[name="item_tax"]').val();
       
        if(tax_data != '' && typeof tax_data != 'undefined' ){
            /*var tax_split = tax_data.toString().split("-");
            var item_tax_id = +tax_split[0];*/
            /*var item_tax = +parseFloat(tax_split[1]);*/
            var item_tax = +parseFloat(tax_data);
            if (item_tax == "" || isNaN(item_tax)) {
                item_tax = 0;
            }
        }
        var type_of_supply = $("#type_of_supply").val();
        var cess_tax = row.find('input[name=item_tax_cess]').val();
        if(cess_tax != '' && typeof cess_tax != 'undefined' ){
            /*var tax_cess_split = cess_tax.toString().split("-");
            var item_tax_cess_id = +tax_cess_split[0];*/
            var item_tax_cess = +parseFloat(cess_tax);
            if (item_tax_cess == "" || isNaN(item_tax_cess)) {
                item_tax_cess = 0;
            }
        }
        /*if (type_of_supply == "export_without_payment") {
            item_tax_id = 0;
            item_tax = 0;
            item_tax_cess_id = item_tax_cess =0;
        }*/
        var item_tax_amount = (item_taxable_value * item_tax) / 100;
        var item_tax_cess_amount = (item_taxable_value * item_tax_cess) / 100;
        if(settings_tax_type == 'gst'){
            var txable_amount = parseFloat(item_tax_amount);
            purchase_igst_amount = txable_amount;
        }
    }
    item_tax_amount = item_tax_amount || 0;
    item_tax_cess_amount = item_tax_cess_amount || 0;
    purchase_igst_amount = purchase_igst_amount || 0;
    item_tax = item_tax || 0;
    item_tax_cess = item_tax_cess || 0;
    item_tax_amount = item_tax_amount || 0;
    row.find('input[name="item_tax_amount"]').val(item_tax_amount);
    row.find('input[name="item_tax_cess_amount"]').val(item_tax_cess_amount);
    row.find('input[name^="item_tax_amount_igst"]').val(purchase_igst_amount);
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
    var item_tds_id = +row.find('input[name="item_tds_id"]').val();
    var item_tds = +parseFloat(
        row.find('[name^="item_tds_percentage"]').val()
    );
    if (item_tds == "" || isNaN(item_tds)) {
        item_tds = 0;
    }
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
    var item_tds_amount = (item_taxable_value * item_tds) / 100;
    row.find('input[name^="item_tds_amount"]').val(item_tds_amount);
    //row.find('input[name^="item_tds_id"]').val(item_tds_id);
    //row.find('[name="item_tds_percentage"]').val(item_tds);
    row.find("#item_tds_lbl_" + table_index).text(item_tds_amount);
    if (settings_item_editable == "no") {
        row
            .find("#item_tds_percentage_hide_lbl_" + table_index)
            .text(item_tds + " %");
    }
}
function calculateRowTotal(row) {
    var table_index = +row.find('input[name^="item_key_value"]').val();
   
    var item_bcd_amount = +parseFloat(
        row.find('input[name="item_bcd_amount"]').val()
    );

    var item_other_duties_amount = +parseFloat(
        row.find('input[name="item_other_duties_amount"]').val()
    );

    var item_tax_amount = +parseFloat(
        row.find('input[name="item_tax_amount"]').val()
    );
    var item_tax_cess_amount = +parseFloat(
        row.find('input[name="item_tax_cess_amount"]').val()
    );
    if (typeof item_bcd_amount === "undefined" || isNaN(item_bcd_amount)) item_bcd_amount = 0;
    if (typeof item_other_duties_amount === "undefined" || isNaN(item_other_duties_amount)) item_other_duties_amount = 0;
    if (typeof item_tax_amount === "undefined" || isNaN(item_tax_amount)) 
        item_tax_amount = 0;
    if (typeof item_tax_cess_amount === "undefined" || isNaN(item_tax_cess_amount)) 
        item_tax_cess_amount = 0;
    
    var item_grand_total = item_tax_amount + item_tax_cess_amount + item_bcd_amount + item_other_duties_amount;
   
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
    var item_bcd_amount = +parseFloat(
        row.find('input[name^="item_bcd_amount"]').val()
    );

    var item_other_duties_amount = +parseFloat(
        row.find('input[name^="item_other_duties_amount"]').val()
    );

    var item_tax_amount = +parseFloat(
        row.find('input[name="item_tax_amount"]').val()
    );
    var item_tax_cess_amount = +parseFloat(
        row.find('input[name="item_tax_cess_amount"]').val()
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
    
    if (typeof item_taxable_value !== "undefined" && !isNaN(item_taxable_value)) {
        row
            .find('input[name^="item_taxable_value"]')
            .val(precise_amount(item_taxable_value));
        row
            .find("#item_taxable_value_lbl_" + table_index)
            .text(precise_amount(item_taxable_value));
    }
    if (typeof item_bcd_amount !== "undefined" && !isNaN(item_bcd_amount)) {
        row
            .find('input[name="item_bcd_amount"]')
            .val(precise_amount(item_bcd_amount));
        row
            .find("#item_bcd_lbl_" + table_index)
            .text(precise_amount(item_bcd_amount));
    }

    if (typeof item_other_duties_amount !== "undefined" && !isNaN(item_other_duties_amount)) {
        row
            .find('input[name="item_other_duties_amount"]')
            .val(precise_amount(item_other_duties_amount));
        row
            .find("#item_other_duties_lbl_" + table_index)
            .text(precise_amount(item_other_duties_amount));
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
   
    row.find('input[name^="item_price"]').val(precise_amount(item_price));
    row.find('input[name^="item_sub_total"]').val(precise_amount(item_sub_total));
    row.find("#item_sub_total_lbl_" + table_index).text(precise_amount(item_sub_total));
    row.find('input[name^="item_grand_total"]').val(precise_amount(item_grand_total));
    if (settings_item_editable == "no") {
        row.find("#item_price_hide_lbl_" + table_index).text(precise_amount(item_sub_total));
        row.find("#item_grand_total_hide_lbl_" + table_index).text(precise_amount(item_grand_total));
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
    var item_bcd_amount = +parseFloat(
        row.find('input[name^="item_bcd_amount"]').val()
    );

    var item_other_duties_amount = +parseFloat(
        row.find('input[name^="item_other_duties_amount"]').val()
    );

    var item_tax_amount = +parseFloat(
        row.find('input[name="item_tax_amount"]').val()
    );
    var item_tax_cess_amount = +parseFloat(
        row.find('input[name="item_tax_cess_amount"]').val()
    );
    var bcd_percentage = +row.find('input[name^="bcd_tax"]').val();
    var other_duties_percentage = +row.find('input[name^="other_duties_tax"]').val();
    
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
    
    var data_item = {
        item_key_value: +table_index,
        item_id: +item_id,
        item_type: item_type,
        item_code: item_code,
        item_quantity: +item_quantity,
        item_price: +item_price,
        item_description: item_description,
        item_sub_total: +item_sub_total,
        item_bcd_amount: +item_bcd_amount,
        bcd_percentage: +bcd_percentage,
        item_other_duties_amount: +item_other_duties_amount,
        other_duties_percentage: +other_duties_percentage,
        item_tax_amount: +item_tax_amount,
        item_tax_percentage: +item_tax_percentage,
        item_tax_cess_amount: +item_tax_cess_amount,
        item_tax_cess_percentage: +item_tax_cess_percentage,
        item_taxable_value: +item_taxable_value,
        item_grand_total: +item_grand_total
    };
    var flag = 0;
    var i_val = "";
    for (var i = 0; i < purchase_data.length; i++) {
        if (
            purchase_data[i] !== "undefined" &&
            purchase_data[i].item_key_value == table_index
        ) {
            flag = 1;
            i_val = i;
            break;
        }
    }
    if (flag == 1) {
        purchase_data[i_val] = data_item;
    } else {
        purchase_data.push(data_item);
    }
    var table_data = JSON.stringify(purchase_data);
    $("#table_data").val(table_data);
}
function calculateTable(row) {
    calculateRow(row);
    
    if (settings_tax_type != "no_tax") {
        calculateTax(row);
    }
    calculateBcd(row);
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
    var bcd = other_duties = 0;
    var tax = 0;
    var tax_cess = 0;
    var tax_igst = 0;
    var taxable = 0;
    var grand_total = 0;
    var bflag = 0;
    var tflag = 0;
    var otflag = 0;
    $("#purchase_table_body")
        .find('input[name^="item_sub_total"]')
        .each(function () {
            sub_total += +$(this).val();
        });
    
    $("#purchase_table_body")
        .find('input[name^="item_taxable_value"]')
        .each(function () {
            taxable += +$(this).val();
        });
    $("#purchase_table_body")
        .find('input[name="item_bcd_amount"]')
        .each(function () {
            bflag = 1;
            bcd += +$(this).val();
        });
    $("#purchase_table_body")
        .find('input[name="item_other_duties_amount"]')
        .each(function () {
            otflag = 1;
            other_duties += +$(this).val();
        });
    $("#purchase_table_body")
        .find('input[name="item_tax_amount"]')
        .each(function () {
            tflag = 1;
            tax += +$(this).val();
        });
    $("#purchase_table_body")
        .find('input[name="item_tax_cess_amount"]')
        .each(function () {
            tflag = 1;
            tax_cess += +$(this).val();
        });
   
    $("#purchase_table_body")
        .find('input[name="item_tax_amount_igst"]')
        .each(function () {
            tflag = 1;
            tax_igst += +$(this).val();
        });
    $("#purchase_table_body")
        .find('input[name^="item_grand_total"]')
        .each(function () {
            grand_total += +$(this).val();
        });
    if(tax > 0){
        if(tax_igst > 0){
            tax_igst = parseFloat(tax);
        }
    }
    console.log(grand_total);
    $('#without_reound_off_grand_total').val(grand_total);
    $("#total_sub_total").val(precise_amount(sub_total));
    $("#total_taxable_amount").val(precise_amount(taxable));
    $("#total_bcd_amount").val(precise_amount(bcd));
    $("#total_other_duties_amount").val(precise_amount(other_duties));
    $("#total_tax_amount").val(precise_amount(tax));
    $("#total_tax_cess_amount").val(precise_amount(tax_cess));
    $("#total_grand_total").val(precise_amount(grand_total));
    if(bcd > 0){
        $('.totalBCDAmount_tr').show();
        $("#totalBCD").text(precise_amount(bcd));
    }else{
        $('.totalBCDAmount_tr').hide();
    }
    
    if(other_duties > 0){
        $('.totalOtherDutiesAmount_tr').show();
        $("#totalOtherDutiesAmount").text(precise_amount(other_duties));
    }else{
        $('.totalOtherDutiesAmount_tr').hide();
    }

    if (settings_tax_type != "no_tax") {
        $("#totalTaxAmount").text(precise_amount(tax));
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
    $("#totalGrandTotal").text(precise_amount(grand_total));
    if (common_settings_round_off == "yes") {
        /*$("input[type=radio][name=round_off_key].minimal").trigger("ifChanged");*/
    }
    //CalculateRoundOff();
}
// Delete function
function deleteRow(row) {
    var table_index_key = +row.find('input[name^="item_key_value"]').val();
    var j = 0;
    var purchase_data_temp = new Array();
    for (var i = 0; i < purchase_data.length; i++) {
        if (purchase_data[i] !== "undefined" && purchase_data[i].item_key_value != table_index_key) {
            // purchase_data.splice(i, 1);
            var data_item = {
                item_key_value: +purchase_data[i].item_key_value,
                item_id: +purchase_data[i].item_id,
                item_type: purchase_data[i].item_type,
                item_code: purchase_data[i].item_code,
                item_quantity: +purchase_data[i].item_quantity,
                item_price: +purchase_data[i].item_price,
                item_description: purchase_data[i].item_description,
                item_sub_total: +purchase_data[i].item_sub_total,
                item_discount_amount: +purchase_data[i].item_discount_amount,
                item_discount_id: +purchase_data[i].item_discount_id,
                item_discount_percentage: +purchase_data[i].item_discount_percentage,
                item_tax_amount: +purchase_data[i].item_tax_amount,
                item_tax_id: +purchase_data[i].item_tax_id,
                item_tax_percentage: +purchase_data[i].item_tax_percentage,
                item_tax_cess_amount: +purchase_data[i].item_tax_cess_amount,
                item_tax_cess_id: +purchase_data[i].item_tax_cess_id,
                item_tax_cess_percentage: +purchase_data[i].item_tax_cess_percentage,
                item_tds_amount: +purchase_data[i].item_tds_amount,
                item_tds_id: +purchase_data[i].item_tds_id,
                item_tds_percentage: +purchase_data[i].item_tds_percentage,
                item_tds_type: +purchase_data[i].item_tds_type,
                item_taxable_value: +purchase_data[i].item_taxable_value,
                item_grand_total: +purchase_data[i].item_grand_total
            };
            purchase_data_temp.push(data_item);
        }
    }
    purchase_data = new Array();
    purchase_data.length = 0;
    purchase_data = purchase_data_temp;
    var table_data = JSON.stringify(purchase_data);
    $("#table_data").val(table_data);
}