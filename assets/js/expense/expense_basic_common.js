var mapping = {};
var allExp = {};
var addedItems = {};

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
    $("#input_expense_code").change(function () {
        $("#input_expense_code").trigger("click");
    });
    $("#supplier").change(function () {
        if ($(this).find("option:selected").text() != "Select Shipping Address") {
            $("#input_expense_code").prop("disabled", false);
            $(".search_expense_code").show();
            $("#err_expense_code").text("");
        } else {
            $(".search_expense_code").hide();
            $("#input_expense_code").prop("disabled", true);
            $("#err_expense_code").text("Please select the supplier to do expense.");
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
        var party_id = $("#supplier").val();
        if (party_id != "" && party_id != 0) {
            // console.log(party_id);
            $('#expense_table_body').find('tr').each(function(){
                $(this).find('select[name="item_discount"]').attr('disabled' , false);
                $(this).find('select[name="item_tax"]').attr('disabled' , false);
                $(this).find('select[name="item_tax_cess"]').attr('disabled' , false);
                $(this).find('input[name="item_tds_percentage"]').attr('disabled' , false);
            })
            $("#shipping_address_div").show();
            // $('#modal_party_name').val($('#supplier').text());
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
            if(party_id == 0){
                $('#expense_table_body').find('tr').each(function(){
                    $(this).find('select[name="item_discount"]').attr('disabled' , true);
                    $(this).find('select[name="item_tax"]').attr('disabled' , true);
                    $(this).find('select[name="item_tax_cess"]').attr('disabled' , true);
                    $(this).find('input[name="item_tds_percentage"]').attr('disabled' , true);
                });
            }else{
                $("#shipping_address_div").hide();
                $("#modal_party_id").val("");
                $("#modal_party_type").val("");
                $("#shipping_address").html('<option value="">Select Shipping Address</option>');
            }
        }
    });
    function ChangeTypeOfSupply(){
        var table_data = $("#table_data").val();
        var state = $('#billing_state').val();
        if(state == '0'){
            flag2 = 1;
           
            $("#type_of_supply").html("");
            $("#type_of_supply").append(
                '<option value="import" selected>Import</option>'
            );
           
            $('[name=gst_payable][value=no]').prop('checked',true);
        } else {
            flag2 = 2;
            $("#type_of_supply").html("");
            if(branch_state_id == $('#billing_state').val()){
                $("#type_of_supply").append('<option value="intra_state">Regular(Intra State)</option>');
            }else{
                $("#type_of_supply").append('<option value="inter_state">Regular(Inter State)</option>');
            }
        }
        $("#type_of_supply").change();
    }
    $("#billing_state").change(function (event) {
        ChangeTypeOfSupply();
    });
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

    function isInRange(currentElement,term) {
        var n = currentElement.item_name.toLowerCase();
        
        if(n.includes(this)){
            return true;
        }else{
            return false;
        }
    }

    // get items to table starts
    $('#input_expense_code').autoComplete({
        minChars: 0,
        cache: false,
        source: function (term, suggest) {
            term = term.toLowerCase();
            if(term == '') term = '-';
            var isnum = /^\d+$/.test(term);
            if (common_settings_inventory_advanced == "") {
                var inventory_advanced = "no";
            } else {
                var inventory_advanced = common_settings_inventory_advanced;
            }
            var isnum = /^\d+$/.test(term);
            var suggestions = [];

            if(term.length < 0){

                var filterExp  =  Object.values(allExp).filter(isInRange,term);

            }else{

                var filterExp = allExp;
            }

            if(filterExp.length == 1){
                item_id  = filterExp.item_id;
                if(addedItems['pro_' +item_id]){
                    var row_index = addedItems['pro_' +item_id];
                    var table_row = $("#"+ row_index);
                    var quantity = table_row.find('input[name^="item_quantity"]').val();
                    quantity = ++quantity;
                    table_row.find('input[name=^"item_quantity"]').val(quantity);
                    new_item = false;
                    calculateTable(table_row);
                }else{
                    $('#table-total').show();
                    add_row(filterExp[0]);
                }

                $('#input_expense_code').val("");
            }else{
                $.each(filterExp, function(k,v){
                    suggestions.push(v.item_name);
                    var pro_name = v.item_name;
                    pro_name = pro_name.replace(/\s/g, "");
                    pro_name = pro_name.replace(/\//g, "");
                    pro_name = pro_name.replace(/-/g, "");

                        var item_code = pro_name;
                        var code = item_code.toString().split(' ');
                        mapping[code[0]] =
                            v.item_id +
                            "-" +
                            settings_discount_visible +
                            "-" +
                            settings_tax_type;
                });
            }
            suggest(suggestions);
        },
        onSelect: function (event, ui) {

            var code = ui;
            var pro_name = '';

            pro_name = code.replace(/\s/g, "");
            pro_name = pro_name.replace(/\//g, "");
            pro_name = pro_name.replace(/-/g, "");

            k = pro_name;
            var item = mapping[k].toString().split("-");
            var item_id = item[0];
            
            $('#table-total').show();
                add_row(allExp[item_id]);
                call_css();
            $('#input_expense_code').val("");
        }
    });    

            // $.ajax({
            //     url: base_url + "expense_bill/get_expense_suggestions/" + term ,
            //     type: "GET",
            //     dataType: "json",
            //     success: function (data) {
            //         var suggestions = [];
            //         for (var i = 0; i < data.length; ++i) {
            //             suggestions.push(data[i].item_name);
            //             var item_code = data[i].item_code;
            //             item_code = item_code.replace(" ", "_");
            //             var code = item_code.toString().split(' ');
            //             mapping[code[0]] = data[i].item_id + '-'+ settings_discount_visible + '-' + settings_tax_type;
            //         }
            //       // && isnum == true
            //         if (i == 1 && term.length > 7 ) {

            //             $.ajax({
            //                 url: base_url + 'expense_bill/get_table_items/' + mapping[data[0].item_code],
            //                 type: "GET",
            //                 dataType: "JSON",
            //                 success: function (data1) {
                                
            //                     $('#table-total').show();
            //                     add_row(data1);
            //                     call_css();
            //                     $('#input_expense_code').val('')
            //                 }
            //             });
            //             suggest('');
            //         } else {
            //             suggest(suggestions);
            //         }
            //     }
            // })
    //     },
    //     onSelect: function (event, ui) {
    //         ui = ui.replace(" ", "_");
    //         var code = ui.toString().split(' ');
    //       //  console.log(code,ui,3333333333);
    //         $.ajax({
    //             url: base_url + 'expense_bill/get_table_items/' + mapping[code[0]],
    //             type: "GET",
    //             dataType: "JSON",
    //             success: function (data) {

    //                 $('#table-total').show();
    //                 add_row(data);
    //                 call_css();
    //                 $('#input_expense_code').val('')
    //             }
    //         })
    //     }
    // });
    // get items to table ends
    //delete rows
    $("#expense_table_body").on("click", "a.deleteRow", function (event) {
        var newRow = $(this).closest("tr");
        deleteRow(newRow);
        $(this).closest("tr").remove();
        reCalculateTable();
    });
    // changes in rows
    $("#expense_table_body").on(
        "change blur",
        'input[name="item_price"],input[name="item_description"],input[name="item_quantity"],select[name="item_discount"],select[name="item_tax"]','select[name="item_tax_cess"]',
        function (event) {
            var newRow = $(this).closest("tr");
            calculateTable(newRow);
        }
    );
    $("#expense_table_body").on(
        "change",
        'select[name="item_tds_percentage"]',
        function (event) {
            /**/
            var newRow = $(this).closest("tr");
            var item_tds_id = newRow.find('select[name^="item_tds_percentage"] option:selected').attr('tds_id');
            var item_tds_type = newRow.find('select[name^="item_tds_percentage"] option:selected').attr('type');
          
            newRow.find('input[name=item_tds_id]').val(item_tds_id);
            newRow.find('input[name=item_tds_type]').val(item_tds_type);
            calculateTable(newRow);
        }
    );
    $("#expense_table_body").on(
        "change",
        'select[name="item_tax_cess"]',
        function (event) {
            var newRow = $(this).closest("tr");
            calculateTable(newRow);
        }
    );
    //reverse calculations
    $("#expense_table_body").on(
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
                    row.find('[name="item_tds_percentage"]').val()
                );
                var item_tds_type = row.find('input[name^="item_tds_type"]').val();
                if (item_tds_type != "TCS" || item_tds_type != "tcs") {
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
    })
    $(document).on('change','[name=type_of_supply]',function(){
        var type_of_supply = $(this).val();
        if(type_of_supply == 'import'){
            $('.gst_payable_div').hide();
        }else{
            $('.gst_payable_div').show();
        }
        reCalculateTable();
    }) 
});
function CalculateRoundOff(){
    var grand_total = parseFloat($("#without_reound_off_grand_total").val());
    
    if(isNaN(grand_total) || typeof grand_total == 'undefined' || grand_total == NaN)
        grand_total = $("#total_grand_total").val();
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
    $("#expense_table_body")
        .find('input[name^="item_tax_id"]')
        .each(function () {
            var row = $(this).closest("tr");
            //if(type_of_supply == 'export_without_payment') row.find('[name=item_tax]').val('').change();
            calculateTable(row);
        });
}

getAllProducts();
function getAllProducts(){

    $.ajax({
        url : base_url + "expense_bill/get_all_items",
        type : "POST",
        dataType : "JSON",
        success : function(data){
            allExp = data;
        }
    });
}

//function to add new row to datatable
function add_row(data) {
    var flag = 0;
    var item_id = data.item_id;
    
    var branch_country_id = data.branch_country_id;
    var branch_state_id = data.branch_state_id;
    var branch_id = data.branch_id;
    var item_name = data.item_name;
    var item_hsn_sac_code = data.expense_hsn_code 
    var item_price = precise_amount(0);
    var item_tax_id = data.product_tax_id;
    var item_tax_percentage = precise_amount(data.product_tax_value);
    var item_tds_id = data.expense_tds_id;
    var item_tds_percentage = precise_amount(data.expense_tds_value);
    var item_tds_type = 'TDS';
    var supplier_id = $('#supplier').val();
    
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
        var is_disabled = '';
        if(supplier_id == 0){
            is_disabled = ' disabled="disabled" ';
        }
        select_discount +=
            '<select class="form-control open_discount form-fixer select2" ' + is_disabled + '  name="item_discount" style="width: 100%;">';
        select_discount += '<option value="">Select</option>';
        for (a = 0; a < discount_ary.length; a++) {
            select_discount +=
                '<option value="' +
                discount_ary[a].discount_id +
                "-" +
                precise_amount(discount_ary[a].discount_value) +
                '" >' +
                parseFloat(discount_ary[a].discount_value) +
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
        if(type_of_supply == 'import'){
            item_tax_id = '0';
            gst_disable = 'disabled';
        } 
        /*var select_tds = '';
        if (input_type == "hidden") {
            select_tds +=
                '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            select_tds +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
        }
        select_tds +=
            '<select class="form-control open_tax form-fixer select2" name="item_tds_percentage" style="width: 100%;">';
        select_tds += '<option value="">Select</option>';
        for (a = 0; a < tax_ary.length; a++) {
            if (item_tds_id == tax_ary[a].tax_id) {
                var selected = "selected";
            } else {
                var selected = "";
            }
            
            var t = tax_ary[a].tax_name;
            if(t.toLowerCase() == item_tds_type.toLowerCase()){
                select_tds +=
                    '<option value="'+
                    precise_amount(tax_ary[a].tax_value) +
                    '" ' +
                    selected +
                    " tds_id='"+tax_ary[a].tax_id+"' type='"+tax_ary[a].tax_name+"'>" +
                    precise_amount(tax_ary[a].tax_value) +
                    "</option>";
            }
        }
        select_tds += "</select></div>";*/
        select_tax +=
            '<select class="form-control open_tax form-fixer select2" ' + is_disabled +' name="item_tax" style="width: 100%;" '+gst_disable+'>';
        select_tax += '<option value="">Select</option>';
        for (a = 0; a < tax_ary.length; a++) {
            if (item_tax_id == tax_ary[a].tax_id) {
                var selected = "selected";
            } else {
                var selected = "";
            }
            if(tax_ary[a].tax_name == 'GST'){
                select_tax +=
                    '<option value="' +
                    tax_ary[a].tax_id +
                    "-" +
                    parseFloat(tax_ary[a].tax_value) +
                    '" ' +
                    selected +
                    " >" +
                    parseFloat(tax_ary[a].tax_value) +
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
            '<select class="form-control open_tax form-fixer select2" ' + is_disabled +' name="item_tax_cess" style="width: 100%;" '+gst_disable+'>';
        cess_select += '<option value="">Select</option>';
        for (a = 0; a < tax_ary.length; a++) {
            if (item_tax_id == tax_ary[a].tax_id) {
                var selected = "selected";
            } else {
                var selected = "";
            }
            if(tax_ary[a].tax_name == 'CESS'){
                cess_select +=
                    '<option value="' +
                    tax_ary[a].tax_id +
                    "-" +
                    parseFloat(tax_ary[a].tax_value) +
                    '" ' +
                    selected +
                    " >" +
                    parseFloat(tax_ary[a].tax_value) +
                    "%</option>";
            }
        }
        cess_select += "</select></div>";
    }
    if (settings_tds_visible == "yes") {
        var select_tds = '<input type="text" class="form-control open_tds_modal pointer" ' + is_disabled + ' name="item_tds_percentage" value="'+parseFloat(item_tds_percentage)+'%" readonly>';
        var tds_body = '<table id="tds_table" index="'+ table_index +'" class="table table-bordered table-striped sac_table ">\
                    <thead>\
                    <th>TAX Name</th>\
                    <th>Action</th>\
                    </thead>\
                    <tbody>';
        
        for (a = 0; a < tax_ary.length; a++) {
            if(tax_ary[a].tax_name == 'TDS' || tax_ary[a].tax_name == 'TCS'){
                tax_name = tax_ary[a].tax_name;
                if(item_tds_type.toLowerCase() == tax_name.toLowerCase()){
                    var selected = "";
                    if (item_tds_id == tax_ary[a].tax_id) {
                        selected = "selected";
                    }
                    tds_body += '<tr>\
                            <td>'+tax_ary[a].tax_name+'(Sec '+tax_ary[a].section_name+') @ '+parseFloat(tax_ary[a].tax_value) +'%</td>\
                            <td><div class="radio">\
                                    <label><input type="radio" name="tds_tax" value="'+parseFloat(tax_ary[a].tax_value) +
                       '"'+selected +" tds_id='"+tax_ary[a].tax_id+"' typ='"+tax_ary[a].tax_name+"'></label>\
                                </div></td></tr>";
                }
            }
        }
        tds_body += '</tbody></table>';
        /*var select_tds = '';
        if (input_type == "hidden") {
            select_tds +=
                '<div class="form-group" style="margin-bottom:0px !important;display:none">';
        } else {
            select_tds +=
                '<div class="form-group" style="margin-bottom:0px !important;">';
        }
        select_tds +=
            '<select class="form-control open_tax form-fixer select2" name="item_tds_percentage" style="width: 100%;">';
        select_tds += '<option value="">Select</option>';
        for (a = 0; a < tax_ary.length; a++) {
            if (item_tds_id == tax_ary[a].tax_id) {
                var selected = "selected";
            } else {
                var selected = "";
            }
           
            var t = tax_ary[a].tax_name;
            if(t.toLowerCase() == item_tds_type.toLowerCase()){
                select_tds +=
                    '<option value="'+
                    precise_amount(tax_ary[a].tax_value) +
                    '" ' +
                    selected +
                    " tds_id='"+tax_ary[a].tax_id+"' type='"+tax_ary[a].tax_name+"'>" +
                    precise_amount(tax_ary[a].tax_value) +
                    "</option>";
            }
        }
        select_tds += "</select></div>";*/
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
        "><input type='hidden' name='item_type' value='product'><input type='hidden' name='item_code' value=''></td>";
        cols += "<td>" + item_name;
        if(item_hsn_sac_code != ''){
            cols += "<br>(HSN: " + item_hsn_sac_code + ")";
        }
        cols += "</td>";
    
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
        if (discount_exist == 1) {
            cols +=
                "<td style='text-align:center'><input type='hidden' name='item_taxable_value' value='" +
                item_price +
                "' ><span id='item_taxable_value_lbl_" +
                table_index +
                "'>" +
                item_price +
                "</span></td>";
        }
    }
    if (settings_tds_visible == "yes") {
        var tds_input = input_type;
        // tds area
        if (tds_input == "hidden" || (item_tds_id == "" || item_tds_id == 0)) {
            tds_input = "hidden";
            cols +=
                "<td style='text-align:center'>";
        } else {
            cols += "<td>";
        }
        cols +=
            "<input type='hidden' name='item_tds_id' value='" +
            item_tds_id +
            "'>" +
            "<input type='hidden' name='item_tds_type' value='" +
            item_tds_type +
            "'>" +
            /*"<input type='" +
            tds_input +
            "' class='form-control form-fixer text-center float_number' name='item_tds_percentage' value='" +
            item_tds_percentage +
            "'>" +*/
            select_tds+"<div class='tds_modal_body' style='display:none;'>"+tds_body+"</div>"+
            "<input type='hidden' name='item_tds_amount' value='0'><span id='item_tds_lbl_" +
            table_index +
            "' class='pull-right' style='color:red;'>0.00</span>" +
            "</td>";
    }
    //tax area
    if (tax_exist == 1  && settings_gst_visible == "yes") {
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
    $("#expense_table_body").prepend(newRow);
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
    }
    if (settings_tax_type != "no_tax") {
        var table_index = +row.find('input[name^="item_key_value"]').val();
        var tax_data = row.find('select[name="item_tax"]').val();
       
        if(tax_data != '' && typeof tax_data != 'undefined' ){
            var tax_split = tax_data.toString().split("-");
            var item_tax_id = +tax_split[0];
            var item_tax = +parseFloat(tax_split[1]);
            if (item_tax == "" || isNaN(item_tax)) {
                item_tax = 0;
            }
        }
        var type_of_supply = $("#type_of_supply").val();
        var cess_tax = row.find('select[name=item_tax_cess]').val();
        if(cess_tax != '' && typeof cess_tax != 'undefined' ){
            var tax_cess_split = cess_tax.toString().split("-");
            var item_tax_cess_id = +tax_cess_split[0];
            var item_tax_cess = +parseFloat(tax_cess_split[1]);
            if (item_tax_cess == "" || isNaN(item_tax_cess)) {
                item_tax_cess = 0;
            }
        }
        if (type_of_supply == "import") {
            item_tax_id = 0;
            item_tax = 0;
            item_tax_cess_id = item_tax_cess =0;
            /*row.find('[name=item_tax]').val('').change();*/
        }
        var item_tax_amount = (item_taxable_value * item_tax) / 100;
        var item_tax_cess_amount = (item_taxable_value * item_tax_cess) / 100;
        if(settings_tax_type == 'gst'){
            var cgst_amount_percentage = parseFloat(settings_tax_percentage);
            var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
            var expense_igst_amount = 0; 
            var expense_cgst_amount = 0;
            var expense_sgst_amount = 0;
           /* var txable_amount = (parseFloat(other_tax_amount) + parseFloat(item_tax_amount));*/
            var txable_amount = parseFloat(item_tax_amount);
            if(branch_country_id == $('#billing_country').val()){
                if(branch_state_id == $('#billing_state').val()){
                    expense_igst_amount = 0;
                    expense_cgst_amount = (txable_amount * cgst_amount_percentage)/100;
                    expense_sgst_amount = (txable_amount * sgst_amount_percentage)/100;
                }else{
                    expense_igst_amount = txable_amount;
                    expense_cgst_amount = 0;
                    expense_sgst_amount = 0;
                    /*item_tax_cess_amount = 0;*/
                }
            }else{
                if($('#type_of_supply').val() == 'export_with_payment'){
                    expense_igst_amount = txable_amount;
                    expense_cgst_amount = 0;
                    expense_sgst_amount = 0;
                    /*item_tax_cess_amount= 0;*/
                }
            }
        }
    }
    
    
    /*var other_tax_amount = $('#total_other_taxable_amount').val();
    if(other_tax_amount == '' || typeof other_tax_amount == 'undefined' )
        other_tax_amount = 0;*/
    item_tax_amount = item_tax_amount || 0;
    item_tax_cess_amount = item_tax_cess_amount || 0;
    expense_igst_amount = expense_igst_amount || 0;
    expense_sgst_amount = expense_sgst_amount || 0;
    expense_cgst_amount = expense_cgst_amount || 0;
    item_tax = item_tax || 0;
    item_tax_cess = item_tax_cess || 0;
    item_tax_amount = item_tax_amount || 0;
    row.find('input[name="item_tax_amount"]').val(item_tax_amount);
    row.find('input[name="item_tax_cess_amount"]').val(item_tax_cess_amount);
    row.find('input[name^="item_tax_amount_igst"]').val(expense_igst_amount);
    row.find('input[name^="item_tax_amount_sgst"]').val(expense_sgst_amount);
    row.find('input[name^="item_tax_amount_cgst"]').val(expense_cgst_amount);
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
    var item_tds_amount = +parseFloat(
        row.find('input[name^="item_tds_amount"]').val()
    );
    var item_tds_type = row.find('input[name^="item_tds_type"]').val();
    if (typeof item_taxable_value === "undefined" || isNaN(item_taxable_value)) {
        item_taxable_value = item_sub_total;
    }
    if (typeof item_tds_amount === "undefined" || isNaN(item_tds_amount)) {
        item_tds_amount = 0;
    } else {
        if (item_tds_type == "tds" || item_tds_type == "TDS") {
            item_tds_amount = 0;
        }
    }
    if (typeof item_tax_amount === "undefined" || isNaN(item_tax_amount)) {
        item_tax_amount = 0;
    }
    if (typeof item_tax_cess_amount === "undefined" || isNaN(item_tax_cess_amount)) {
        item_tax_cess_amount = 0;
    }
    var item_grand_total = item_taxable_value + item_tax_amount + item_tds_amount + item_tax_cess_amount;
    
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
    if (typeof item_tds_amount !== "undefined" && !isNaN(item_tds_amount)) {
        row
            .find('input[name^="item_tds_amount"]')
            .val(precise_amount(item_tds_amount));
        row
            .find("#item_tds_lbl_" + table_index)
            .text(precise_amount(item_tds_amount));
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
    var item_tds_id = +row.find('input[name^="item_tds_id"]').val();
    var item_tds_percentage = +parseFloat(
        row.find('[name^="item_tds_percentage"]').val()
    );
    
    var item_tds_amount = +parseFloat(
        row.find('input[name^="item_tds_amount"]').val()
    );
    var item_tds_type = row.find('input[name^="item_tds_type"]').val();
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
        item_grand_total: +item_grand_total
    };
    var flag = 0;
    var i_val = "";
    for (var i = 0; i < expense_data.length; i++) {
        if (
            expense_data[i] !== "undefined" &&
            expense_data[i].item_key_value == table_index
        ) {
            flag = 1;
            i_val = i;
            break;
        }
    }
    if (flag == 1) {
        expense_data[i_val] = data_item;
    } else {
        expense_data.push(data_item);
    }
    var table_data = JSON.stringify(expense_data);
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
    $("#expense_table_body")
        .find('input[name^="item_sub_total"]')
        .each(function () {
            sub_total += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name^="item_taxable_value"]')
        .each(function () {
            taxable += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name^="item_discount_amount"]')
        .each(function () {
            dflag = 1;
            discount += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name="item_tax_amount"]')
        .each(function () {
            tflag = 1;
            tax += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name="item_tax_cess_amount"]')
        .each(function () {
            tflag = 1;
            tax_cess += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name="item_tax_amount_cgst"]')
        .each(function () {
            tflag = 1;
            tax_cgst += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name="item_tax_amount_sgst"]')
        .each(function () {
            tflag = 1;
            tax_sgst += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name="item_tax_amount_igst"]')
        .each(function () {
            tflag = 1;
            tax_igst += +$(this).val();
        });
    $("#expense_table_body")
        .find('input[name^="item_tds_amount"]')
        .each(function () {
            row = $(this).closest("tr");
            var tds_type = row.find('input[name^="item_tds_type"]').val();
            if (tds_type == "tcs" || tds_type == 'TCS') {
                tcs += +$(this).val();
                tcsflag = 1;
            } else {
                tds += +$(this).val();
                tdsflag = 1;
            }
        });
    $("#expense_table_body")
        .find('input[name^="item_grand_total"]')
        .each(function () {
            grand_total += +$(this).val();
        });
    var total_other_amount = +$("#total_other_amount").val();
    var other_tax_amount = $('#total_other_taxable_amount').val();
    if(other_tax_amount == '' || typeof other_tax_amount == 'undefined' )
        other_tax_amount = 0;
    if(tax > 0){
        if(tax_cgst > 0){
            var cgst_amount_percentage = parseFloat(settings_tax_percentage);
            var sgst_amount_percentage = 100 - parseFloat(cgst_amount_percentage);
            var txable_amount = (parseFloat(other_tax_amount) + parseFloat(tax));
            tax_cgst = (txable_amount * cgst_amount_percentage)/100;
            tax_sgst = (txable_amount * sgst_amount_percentage)/100;
        }
        if(tax_igst > 0){
            tax_igst = (parseFloat(other_tax_amount) + parseFloat(tax));
        }
    }
    
    var final_grand_total = grand_total + total_other_amount;
    if (!isNaN(final_grand_total) && final_grand_total.toString().indexOf('.') != -1){
        $('#round_off_option').show();
    }else{
        $('#round_off_option').hide();
    }
    $('#without_reound_off_grand_total').val(final_grand_total);
    $("#total_sub_total").val(precise_amount(sub_total));
    $("#total_taxable_amount").val(precise_amount(taxable));
    $("#total_discount_amount").val(precise_amount(discount));
    $("#total_tax_amount").val(precise_amount(tax));
    $("#total_tax_cess_amount").val(precise_amount(tax_cess));
    $("#total_tds_amount").val(precise_amount(tds));
    $("#total_tcs_amount").val(precise_amount(tcs));
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
            if($('#billing_state option:selected').attr('utgst') == '1')
                lbl = 'UTGST (+)';
            $('.totalSGSTAmount_tr').find('td:first').text(lbl);
            $('.totalSGSTAmount_tr').show();
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
    if (settings_tds_visible == "yes" && tds > 0) {
        $('.tds_amount_tr').show();
        $("#totalTdsAmount").text(precise_amount(tds));
    }else{
        $('.tds_amount_tr').hide();
    }
    if (settings_tds_visible == "yes" && tcs > 0) {
        $('.tcs_amount_tr').show();
        $("#totalTcsAmount").text(precise_amount(tcs));
    }else{
        $('.tcs_amount_tr').hide();
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
    var expense_data_temp = new Array();
    for (var i = 0; i < expense_data.length; i++) {
        if (expense_data[i] !== "undefined" && expense_data[i].item_key_value != table_index_key) {
            // expense_data.splice(i, 1);
            var data_item = {
                item_key_value: +expense_data[i].item_key_value,
                item_id: +expense_data[i].item_id,
                item_type: expense_data[i].item_type,
                item_code: expense_data[i].item_code,
                item_quantity: +expense_data[i].item_quantity,
                item_price: +expense_data[i].item_price,
                item_description: expense_data[i].item_description,
                item_sub_total: +expense_data[i].item_sub_total,
                item_discount_amount: +expense_data[i].item_discount_amount,
                item_discount_id: +expense_data[i].item_discount_id,
                item_discount_percentage: +expense_data[i].item_discount_percentage,
                item_tax_amount: +expense_data[i].item_tax_amount,
                item_tax_id: +expense_data[i].item_tax_id,
                item_tax_percentage: +expense_data[i].item_tax_percentage,
                item_tax_cess_amount: +expense_data[i].item_tax_cess_amount,
                item_tax_cess_id: +expense_data[i].item_tax_cess_id,
                item_tax_cess_percentage: +expense_data[i].item_tax_cess_percentage,
                item_tds_amount: +expense_data[i].item_tds_amount,
                item_tds_id: +expense_data[i].item_tds_id,
                item_tds_percentage: +expense_data[i].item_tds_percentage,
                item_tds_type: +expense_data[i].item_tds_type,
                item_taxable_value: +expense_data[i].item_taxable_value,
                item_grand_total: +expense_data[i].item_grand_total
            };
            expense_data_temp.push(data_item);
        }
    }
    expense_data = new Array();
    expense_data.length = 0;
    expense_data = expense_data_temp;
    var table_data = JSON.stringify(expense_data);
    $("#table_data").val(table_data);
}