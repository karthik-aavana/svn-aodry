if (typeof customer_ajax === 'undefined' || customer_ajax === null) {
    var customer_ajax = "no";
}
if (typeof customer_add === 'undefined' || customer_add === null) {
    var customer_add = "no";
}
$(document).on("click", ".open_customer_modal", function() {
    var reference_type = $(this).data('reference_type');
    $('.modal-body #reference_type').val(reference_type);
    var selected = 'current';
    var module_id = $("#customer_module_id").val();
    var privilege = $("#privilege").val();
    $.ajax({
        url: base_url + 'general/generate_date_reference',
        type: 'POST',
        data: {
            date: selected,
            privilege: privilege,
            module_id: module_id
        },
        success: function(data) {
            var parsedJson = $.parseJSON(data);
            var customer_code = parsedJson.reference_no;
            $(".modal-body #customer_code").val(customer_code);
            if (reference_type != 'leads') {
                var reference_number = parsedJson.reference_no;
                $(".modal-body #reference_number").val(reference_number);
            }
            if (parsedJson.access_settings[0].invoice_readonly == "yes") {
                $('.modal-body #customer_code').attr('readonly', 'true');
            }
            $("#err_customer_code").text("");
            $('.modal-body #customer_name').focus();
        }
    });
});
$(document).ready(function() {
    $('#cmb_country').change(function() {
        var id = $(this).val();
        $('#cmb_state').empty();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: {
                id: id
            },
            dataType: "JSON",
            success: function(data) {
                var state = $('#cmb_state');
                var opt = document.createElement('option');
                opt.text = 'Select State';
                opt.value = '';
                state.append(opt);
                var city = $('#cmb_city');
                var opt1 = document.createElement('option');
                opt1.text = 'Select City';
                opt1.value = '';
                city.append(opt1);
                for (i = 0; i < data.length; i++) {
                    $('#cmb_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                }
            }
        });
    });
    $('#cmb_state').change(function() {
        var id = $(this).val();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_city/' + id,
            dataType: "JSON",
            success: function(data) {

                var city = $('#cmb_city');
                var opt = document.createElement('option');
                opt.text = 'Select City';
                opt.value = '';
                city.append(opt);
                for (i = 0; i < data.length; i++) {
                    $('#cmb_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
                }
                var opt1 = document.createElement('option');
                opt1.text = 'Others';
                opt1.value = '0';
                city.append(opt1);
            }
        });
    });
    var customer_name_exist = 0;
    var customer_code_exist = 0;
    $("#customer_submit").click(function(event) {
        //var gst_regex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        var customer_code = $('#customer_code').val() ? $('#customer_code').val() : "";
        var customer_name = $('#customer_name').val() ? $('#customer_name').val() : "";
        var customer_type = $('#customer_type').val() ? $('#customer_type').val() : "";
        var gstin = $('#gst_number').val() ? $('#gst_number').val() : "";
        var address = $('#address').val() ? $('#address').val() : "";
        var city = $('#cmb_city').val() ? $('#cmb_city').val() : "";
        var state = $('#cmb_state').val() ? $('#cmb_state').val() : "";
        var country = $('#cmb_country').val() ? $('#cmb_country').val() : "";
        var pin_number = $('#txt_pin_code').val() ? $('#txt_pin_code').val() : "";
        var pan_number = $('#txt_pan_number').val() ? $('#txt_pan_number').val() : "";
        var email = $('#email_address').val() ? $('#email_address').val() : "";
        var contact_person = $('#txt_contact_person').val() ? $('#txt_contact_person').val() : "";
        var due_days = $('#due_days').val() ? $('#due_days').val() : "";
        var dl_no = $('#dl_no').val();
        var num_regex = /^[0-9]+$/;
        var alpa_regex = /^[a-zA-Z ]+$/;
        var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var gst_regex = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
       
        if (customer_type == null || customer_type == "") {
            $("#err_customer_type").text("Please Select Customer Type.");
            return false;
        } else {
            $("#err_customer_type").text("");
        }
        if (customer_code == null || customer_code == "") {
            $("#err_customer_code").text("Please Enter Customer Code.");
            return false;
        } else {
            $("#err_customer_code").text("");
        }
        if (customer_code_exist == 1) {
            $("#err_customer_code").text(customer_code + " code already exists!");
            return false;
        }
        if (customer_name == null || customer_name == "") {
            $("#err_customer_name").text("Please Enter Customer Name.");
            return false;
        } else {
            $("#err_customer_name").text("");
            if ($('[name=customer_name_used]').val() > 0) {
                $("#err_customer_name").text("Name already used!");
                return false;
            }
        }
        if (customer_name_exist == 1) {
            $("#err_customer_name").text(customer_name + " name already exists!");
            return false;
        }
        if (!customer_name.match(name_regex1)) {
            $('#err_customer_name').text("Please Enter Valid Customer Name ");
            return false;
        } else {
            $("#err_customer_name").text("");
        }
        if (country == null || country == "") {
            $("#err_country").text("Please Enter Country Name.");
            return false;
        } else {
            $("#err_country").text("");
        }
        if (state == null || state == "") {
            $("#err_state").text("Please Enter State Name.");
            return false;
        } else {
            $("#err_state").text("");
        }
        if (city == null || city == "") {
            $("#err_city").text("Please Enter City Name.");
            return false;
        } else {
            $("#err_city").text("");
        }
        if (gstin.length > 0) {
            if (!gstin.match(gst_regex)) {
                $('#err_gstin').text("Please Enter Valid GSTIN Number.");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            if ((gstin.length) < 15 || (gstin.length) > 15) {
                $("#err_gstin").text("GSTIN Number should have length 15");
                return false;
            } else {
                $("#err_gstin").text("");
            }
        } else {
            $("#err_gstin").text("");
        }
        
        if (email != "") {
            if (!email.match(email_regex)) {
                $('#err_email_address').text("Please Enter Valid Email");
                return false;
            } else {
                $("#err_email_address").text("");
            }
        }
        
        if (pan_number != "") {
            if (!pan_number.match(name_regex)) {
                $('#err_pan_number').text("Please Enter Valid Pan Number");
                return false;
            } else {
                $("#err_pan_number").text("");
            }
        } else {
            $("#err_pan_number").text("");
        }
        if (pin_number != "") {
            if (!pin_number.match(num_regex)) {
                $('#err_pin_code').text("Please Enter Valid Pin Number");
                return false;
            } else {
                $("#err_pin_code").text("");
            }
        } else {
            $("#err_pin_code").text("");
        }
        if (contact_person != "") {
            if (!contact_person.match(alpa_regex)) {
                $('#err_contact_person').text("Please Enter Valid Contact Person");
                return false;
            } else {
                $("#err_contact_person").text("");
            }
        } else {
            $("#err_contact_person").text("");
        }

        if (customer_name_exist == 1) {
            $("#err_customer_name").text(customer_name + " name already exists!");
            return false;
        }
        if (address == null || address == "") {
            $("#err_address").text("Please Enter Address.");
            return false;
        } else {
            $("#err_address").text("");
        }
        if (customer_ajax != "yes") {
            if (country == null || country == "") {
                $("#err_country").text("Please Select Country ");
                return false;
            } else {
                $("#err_country").text("");
            }
            if (state == null || state == "") {
                $("#err_state").text("Please Select State ");
                return false;
            } else {
                $("#err_state").text("");
            }
            if (city == null || city == "") {
                $("#err_city").text("Please Select City ");
                return false;
            } else {
                $("#err_city").text("");
            }
        }
        if (due_days != "") {
            if (due_days < 0 || due_days > 365) {
                $('#err_due_days').text("Value must be less than or equal to 365");
                return false;
            } else {
                $("#err_due_days").text("");
            }
        }
        if (customer_ajax == "yes") {
            $("#loader_coco").show();
            var reference_number = $('#reference_number').val();
            var reference_type = $('#reference_type').val();
            var purpose_of_transaction = $('#purpose_of_transaction').val();
            var type_of_transaction = $('#type_of_transaction').val();
            var customer_name = $('#customer_name').val();
            $("#loader_coco").show();

            $.ajax({
                url: base_url + 'customer/add_customer_ajax',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'customer_name': $('#customer_name').val(),
                    'customer_code': $('#customer_code').val(),
                    'reference_number': $('#reference_number').val(),
                    'reference_type': $('#reference_type').val(),
                    'customer_type': $('#customer_type').val(),
                    'gstregtype': $('#gstregtype').val(),
                    'state_id': $('#state_id').val(),
                    'email': $('#email_address').val(),
                    'gst_number': $('#gst_number').val(),
                    'postal_code': $('#txt_pin_code').val(),
                    'state_code': $('#state_code').val(),
                    'panno': $('#txt_pan_number').val(),
                    'tanno': $('#txt_tan_number').val(),
                    'due': $('#due_days').val(),
                    'department' : $('#department').val(),
                    'address': $("#address").val(),
                    'country': $("#cmb_country").val(),
                    'state': $("#cmb_state").val(),
                    'city': $("#cmb_city").val(),
                    'mobile': $('#txt_contact_number').val(),
                    'dl_no' : $('#dl_no').val(),
                    'food_ln' : $('#food_ln').val(),
                    //'telephone': $('#telephone').val(),
                    //'website': $('#website').val(),
                    //'add_contact_person': add_contact_person,
                    'contact_person_name': $('#txt_contact_person').val(),
                    /*'contact_person_code': $('#contact_person_code').val(),                     
                     'contact_person_country': $("#contact_person_country").val(),                     
                     'contact_person_state': $("#contact_person_state").val(),                     
                     'contact_person_city': $("#contact_person_city").val(),                     
                     'contact_person_email': $('#contact_person_email').val(),                     
                     'contact_person_mobile': $('#contact_person_mobile').val()*/
                },
                /*beforeSend: function(){
                     // Show image container
                    $("#loader_coco").show();
                },*/
                success: function(result) {
                    var data = result['data'];
                 
                    var ledgers_data = result['ledgers_data'];
                    if (reference_type != 'ship') {
                        $('#customer').html('');
                        $('#customer').append('<option value="">Select</option>');
                        for (i = 0; i < data.length; i++) {
                            if (data[i].customer_code != '') {
                                $('#customer').append('<option value="' + data[i].customer_id + '">'+ data[i].customer_code +' - '+ data[i].customer_name + '</option>');
                            } else {
                                $('#customer').append('<option value="' + data[i].customer_id + '">' + data[i].customer_name + '</option>');
                            }
                        }
                        $('#ship_to').append('<option value="' + result['id'] + '">' + customer_name.toUpperCase() + '</option>');
                        $('#customer').val(result['id']).attr("selected", "selected");
                        $('#customer').change();
                        //$('#ship_to').change();
                    }


                    /*if(reference_type == 'sales'){
                         $('#ship_to').append('<option value="' + result['id'] + '">' + customer_name.toUpperCase() + '</option>');
                    }*/

                    if (reference_type == 'ship') {
                        $('#ship_to').html('');
                        $('#ship_to').append('<option value="">Select</option>');
                        for (i = 0; i < data.length; i++) {
                            $('#ship_to').append('<option value="' + data[i].customer_id + '">' + data[i].customer_name + '</option>');
                        }
                        $('#ship_to').val(result['id']).attr("selected", "selected");
                        $('#ship_to').change();
                        $('#customer').append('<option value="' + result['id'] + '">' + customer_name.toUpperCase() + '</option>');
                    }

                    $("#customerForm")[0].reset();
                    if (type_of_transaction == "Advance Taken" || type_of_transaction == "Payment of Advance Taken") {
                        $('#from_to').html('');
                        $('#from_to').append('<option value="">Select</option>');
                        for (i = 0; i < ledgers_data.length; i++) {
                            $('#from_to').append('<option value="' + ledgers_data[i].ledger_id + '">' + ledgers_data[i].ledger_title + '</option>');
                        }
                        $('#from_to').val(result['ledger_id']).attr("selected", "selected");
                        $('#from_to').change();
                    }
                    $("#loader_coco").hide();
                    $('#customer_modal').modal('hide');
                },error:function(){
                    $('#customer_modal').modal('hide');
                }
            });
        }
    });
    $("#customer_code").on("blur", function(event) {
        var customer_code = $('#customer_code').val();
        var customer_id = $('#customer_id').val();
        if (customer_code == null || customer_code == "") {
            $("#err_customer_code").text("Please Enter Customer Code.");
            return false;
        } else {
            $("#err_customer_code").text("");
        }
        $.ajax({
            url: base_url + 'customer/get_check_customer',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'customer_code': customer_code,
                'customer_id': customer_id
            },
            success: function(result) {
                if (result[0].num > 0) {
                    $('#customer_code').val("");
                    $("#err_customer_code").text(customer_code + " code already exists!");
                    customer_code_exist = 1;
                } else {
                    $("#err_customer_code").text("");
                    customer_code_exist = 0;
                }
            }
        });
    });
    $('[name=customer_name]').on('blur', function() {
        $('[name=customer_name]').trigger('keyup');
    })
    $('[data-target="#customer_modal"]').click(function() {
        $('#customer_modal input').val('');
        $('#customer_modal span[id^=err_]').text('');
    })
    $('[name=customer_name]').on('keyup', function() {
        var cust_name = $(this).val();
        if (cust_name != '') {
            var customer_id = $('#customer_id').val();
            if (typeof xhr != 'undefined') {
                if (xhr.readyState != 4) xhr.abort();
            }
            $('[name=customer_name_used]').val('0');
            $('#err_customer_name').text('');
            xhr = $.ajax({
                url: base_url + 'customer/CustomerValidation',
                type: 'post',
                data: {
                    cust_name: cust_name,
                    id: customer_id
                },
                dataType: 'json',
                success: function(json) {
                    if (json.rows > 0) {
                        $('#err_customer_name').text('Name already used!');
                        $('[name=customer_name_used]').val('1');
                    }
                },
                complete: function() {

                }
            })
        }
    });

    $("#gstin").on("blur keyup", function(event) {
        var gst_regex = /^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;
        var gstin = $('#gstin').val();
        $("#gstin").val(gstin.toUpperCase());
        var gstin_length = gstin.length;
        if (gstin_length > 0) {
            if (!gstin.match(gst_regex)) {
                $('#err_gstin').text("Please Enter Valid GSTIN Number.");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            if (gstin_length < 15 || gstin_length > 15) {
                $("#err_gstin").text("GSTIN Number should have length 15");
                return false;
            } else {
                $("#err_gstin").text("");
            }
        }
    });
    $("#address").on("blur keyup", function(event) {
        var address = $('#address').val();
        if (address.length > 0) {
            if (address == null || address == "") {
                $("#err_address").text("Please Enter Address");
                return false;
            } else {
                $("#err_address").text("");
            }
        }
    });
    $("#country").change(function(event) {
        var country = $('#country').val();
        if (country == null || country == "") {
            $("#err_country").text("Please Select Country");
            return false;
        } else {
            $("#err_country").text("");
        }
    });
    $("#state").change(function(event) {
        var state = $('#state').val();
        if (state == null || state == "") {
            $("#err_state").text("Please Select State ");
            return false;
        } else {
            $("#err_state").text("");
        }
    });
    $("#city").change(function(event) {
        var city = $('#city').val();
        if (city == null || city == "") {
            $("#err_city").text("Please Select City ");
            return false;
        } else {
            $("#err_city").text("");
        }
    });
});