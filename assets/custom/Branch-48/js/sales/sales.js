$(document).ready(function () {      
     $("#input_sales_code").keypress(function (event) {
        if (event.which == 13) {
            return false;
        }
    });

    $(document).on("click", '#sales_submit,#sales_pay_now', function (event) {
        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();
        var invoice_number_old = $("#invoice_number_old").val();
        if ($('#invoice_number').val() == "") {
            $('#err_invoice_number').text("Please Enter Invoice Number.");
            $('#invoice_number').focus();
            return !1
        } else {
            $('#err_invoice_number').text(" ");
            if (section_area == "edit_sales" || section_area == "edit_sales" || section_area == "edit_delivery_challan" || section_area == "convert_quotation") {
                if (invoice_number_old != invoice_number) {
                    invoice_number_count();
                }
            } else {
                invoice_number_count();
            }
        }

        var brand_invoice_number = $('#brand_invoice_number');
        if(brand_invoice_number){
            if (brand_invoice_number.val() == "") {
                $('#err_brand_invoice_number').text("Please Enter Invoice Number.");
                brand_invoice_number.focus();
                return !1
            } else {
                $('#err_brand_invoice_number').text(" ");
            }
        }
        /*if($('input[name=apply_billing]:radio').prop('checked', false)) {
            console.log('error');
            $('#err_customer').text("Please Select One Billing Address.");
            $('#customer').focus();
            return !1
        }*/
        /*if ($('input[name=category_name]').val() == "") {
            $('#item_category').text("Please Select Category.");
            $('#category_name').focus();
            return !1
        }*/

        if ($('#invoice_date').val() == "" || $('#invoice_date').attr('valid') == '0') {
            $('#err_date').text("Please Select valid Date.");
            $('#invoice_date').focus();
            return !1
        }
        if ($('#customer').val() == "") {
            $('#err_customer').text("Please Select the Customer.");
            $('#customer').focus();
            return !1
        }
        var supply = $('#nature_of_supply').val();
        if (supply == "" || supply == null) {
            $("#err_nature_supply").text("Please Select Nature of Supply");
            return !1
        } else {
            $("#err_nature_supply").text("")
        }
        if ($('#billing_country').val() == "") {
            $('#err_billing_country').text("Please Select the Billing Country.");
            $('#err_billing_country').focus();
            return !1
        }
        if ($('#billing_state').val() == "") {
            $('#err_billing_state').text("Please Select the Place of Supply.");
            $('#err_billing_state').focus();
            return !1
        }
        if ($('#type_of_supply').val() == "") {
            $('#err_type_of_supply').text("Please Select the Type of Supply.");
            $('#err_type_of_supply').focus();
            return !1
        }
        if ($('#shipping_address').val() == "") {
            $('#err_shipping_address').text("Please Select the Ship-To.");
            $('#err_shipping_address').focus();
            return !1
        }else{
            $('#err_shipping_address').text("");
        }
        var tablerowCount = $('#sales_table_body tr').length;
        if (tablerowCount < 1) {
            $("#err_product").text("Please Select Items");
            $('#input_sales_code').focus();
            return !1
        }
        if($('#sales_table_body').find('.deleteRow').length <= 0){
            $("#err_product").text("Please Select Items.");
            $('#input_sales_code').focus();
            return !1
        }else {
            $("#err_product").text("")
        }
        
        if(!row_validation()){
            return false;
        }
        var grand_total = +$('#total_grand_total').val();
        if (grand_total == "" || grand_total == null || grand_total <= 0) {
            $("#err_product").text("Total Amount Should be equal or greater than Zero.");
            //$('#input_sales_code').focus();
            return !1
        } else {
            $("#err_product").text("")
        }
        
    });

    $("#invoice_date").blur(function (event) {
        var date = $('#invoice_date').val();
        if (date == null || date == "") {
            $("#err_date").text("Please Enter Date");
            $('#date').focus();
            return !1
        } else {
            $("#err_date").text("")
        }
        if (!date.match(date_regex)) {
            $('#err_code').text(" Please Enter Valid Date ");
            $('#date').focus();
            return !1
        } else {
            $("#err_date").text("")
        }
    });
    $("#customer").change(function (event) {
        var customer = $('#customer').val();
        if (customer == "") {
            $("#err_customer").text("Please Select Customer");
            $('#customer').focus();
            return !1
        } else {
            $("#err_customer").text("")
        }
    });
    $("#total_grand_total").change(function (event) {
        var total_grand_total = $('#total_grand_total').val();
        if (total_grand_total == "") {
            $("#err_product").text("Total Amount Should be equal or greater than Zero.");
            return !1
        } else {
            $("#err_product").text("")
        }
    });
    $("#shipping_address").change(function (event) {
        var shipping_address = $('#shipping_address').val();
        if (shipping_address == "") {
            $("#err_shipping_address").text("Please Select shipping address");
            $('#shipping_address').focus();
            return !1
        } else {
            $("#err_shipping_address").text("")
        }
    });
    $("#nature_of_supply").change(function (event) {
        var supply = $('#nature_of_supply').val();
        if (supply == "") {
            $("#err_nature_supply").text("Please Select Nature of Supply");
            return !1
        } else {
            $("#err_nature_supply").text("")
        }
    });
    $("#type_of_supply").change(function (event) {
        var typ_supply = $('#type_of_supply').val();
        if (typ_supply == "") {
            $("#err_type_supply").text("Please Select Type of Supply");
            return !1
        } else {
            $("#err_type_supply").text("")
        }
    });
    // Invoice number check
    $("#invoice_number").on("blur", function (event) {
        var invoice_number = $('#invoice_number').val();
        var section_area = $("#section_area").val();
        if (invoice_number == null || invoice_number == "") {
            $("#err_invoice_number").text("Please Enter Invoice Number.");
            return false;
        } else {
            $("#err_invoice_number").text("");
            if (section_area == "edit_sales" || section_area == "edit_sales" || section_area == "edit_delivery_challan" || section_area == "convert_quotation") {
                var invoice_number_old = $("#invoice_number_old").val();
                if (invoice_number_old != invoice_number) {
                    invoice_number_count();
                }
            } else {
                invoice_number_count();
            }
        }
    });
});

function invoice_number_count() {
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
            if (result[0]['num'] > 0) {
                $('#invoice_number').val("");
                $("#err_invoice_number").text(invoice_number + " already exists!");
                return false;
            } else {
                $("#err_invoice_number").text("");
            }
        }
    });
}

function row_validation(){
$(document)
    .find('#sales_table_body tr')
    .each(function () {
        var rows = $(this);
        rows.find('[name=input_sales_code_err]').text("");
        if(rows.find('[name=input_sales_code]').length > 0){
            if(rows.find('[name=input_sales_code]').val() == ''){
                rows.find('[name=input_sales_code_err]').text("Please product name.");
                rows.find('[name="input_sales_code"]').focus();
                return !1
            }
        }


        var item_uom = rows.find('select[name="item_uom"]').val();
        if (item_uom == "" || item_uom == null) {
            
            rows.find('[name=item_uom_err]').text("Please Select Unit.");
            rows.find('select[name="item_uom"]').focus();
            return !1
        }else {
            rows.find('[name=item_uom_err]').text("")
        }

        if (rows.find('input[name=item_price]').val() == "" || rows.find('input[name=item_price]').val() <= 0) {    
            rows.find('[name=item_amount_err]').text("Please enter amount.");
            rows.find('input[name=item_price]').focus();
            $("#err_product").text("Please enter amount.")
            return !1
        }else {
            rows.find('[name=err_product_hsn_sac_code]').text("")
        }

        if (rows.find('input[name=product_hsn_sac_code]').val() == "") {
            
            rows.find('[name=err_product_hsn_sac_code]').text("Please Select HSN.");
            rows.find('input[name=product_hsn_sac_code]').focus();
            return !1
        }else {
            rows.find('[name=err_product_hsn_sac_code]').text("")
        }

        if (rows.find('select[name="item_category"]').val() == "") {
            
            rows.find('[name=category_name_err]').text("Please Select Category.");
            rows.find('select[name="item_category"]').focus();
            return !1
        }else {
            rows.find('[name=category_name_err]').text("")
        }
    });
    return true;
}

/*$("#sales_table_body")
    .change('input[name^="product_hsn_sac_code"]','select[name="item_category"]','select[name="item_uom"]')
    .each(function () {
        var rows = $(this).closest("tr");

        var item_uom = rows.find('select[name="item_uom"]').val();
        if (item_uom == "") {
            console.log('errorrrrrr')
            rows.find('[name=item_uom_err]').text("Please Select Unit.");
            return !1
        }else {
            rows.find('[name=item_uom_err]').text("")
        }
        
        var product_hsn_sac_code = rows.find('input[name=product_hsn_sac_code]').val();
        if (product_hsn_sac_code == "") {
            console.log('errorrrrrr')
            rows.find('[name=err_product_hsn_sac_code]').text("Please Select HSN.");
            rows.find('input[name=product_hsn_sac_code]').focus();
            return !1
        }else {
            rows.find('[name=err_product_hsn_sac_code]').text("")
        }

        var category_name = rows.find('select[name="item_category"]').val();
        if (category_name == "") {
            console.log('errorrrrrr')
            rows.find('[name=category_name_err]').text("Please Select Category.");
            rows.find('select[name="item_category"]').focus();
            return !1
        }else {
            rows.find('[name=category_name_err]').text("")
        }


});*/