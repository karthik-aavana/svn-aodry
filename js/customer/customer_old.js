if (typeof customer_ajax === 'undefined' || customer_ajax === null) {

    var customer_ajax = "no";

}

if (typeof customer_add === 'undefined' || customer_add === null) {

    var customer_add = "no";

}

$(document).on("click", "#add_contact_person", function ()

{

    if ($('#add_contact_person').is(":checked"))

    {

        $('#contact_person_div').show();

    } else

    {

        $('#contact_person_div').hide();

    }

});

$("#customer_type").change(function (event)

{

    if (customer_add == 'yes')

    {

        if ($('#customer_type').val() == 'company')

        {

            $('#contact_person').show();

        } else

        {

            $('#contact_person').hide();

            $('#add_contact_person').prop('checked', false);

            $('#contact_person_div').hide();

        }

    }

});

$(document).on("click", ".open_customer_modal", function ()

{

    var reference_type = $(this).data('reference_type');

    $('.modal-body #reference_type').val(reference_type);



    var selected = 'current';

    var module_id = $("#customer_module_id").val();

    var privilege = $("#privilege").val();

    $.ajax({

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

                    var customer_code = parsedJson.reference_no;

                    $(".modal-body #customer_code").val(customer_code);



                    if (reference_type != 'leads')

                    {

                        var reference_number = parsedJson.reference_no;

                        $(".modal-body #reference_number").val(reference_number);

                    }



                    if (parsedJson.access_settings[0].invoice_readonly == "yes")

                    {

                        $('.modal-body #customer_code').attr('readonly', 'true');

                    }

                    $("#err_customer_code").text("");



                    $('.modal-body #customer_name').focus();



                }

            });

});



$(document).ready(function ()

{

    var customer_name_exist = 0;

    var customer_code_exist = 0;





        $("#customer_submit").click(function (event)

    {

        var customer_code = $('#customer_code').val()?$('#customer_code').val():"";

        var customer_name = $('#customer_name').val()?$('#customer_name').val():"";

        // var address = $('#address').val();

        var city = $('#city').val()?$('#city').val():"";

        var state = $('#state').val()?$('#state').val():"";

        var country = $('#country').val()?$('#country').val():"";

        var mobile = $('#mobile').val()?$('#mobile').val():"";

        var email = $('#email').val()?$('#email').val():"";

        var gsttype = $('#gstregtype').val()?$('#gstregtype').val():"";

        var gstin = $('#gstin').val()?$('#gstin').val():"";

        var postal_code = $('#postal_code').val()?$('#postal_code').val():"";

        var panno = $('#panno').val()?$('#panno').val():"";

        var tanno = $('#tanno').val()?$('#tanno').val():"";

        var state_code = $('#state_code').val()?$('#state_code').val():"";

        if ($('#add_contact_person').is(":checked"))

        {

            var add_contact_person = "yes";

        } else

        {

            var add_contact_person = "no";

        }



        if (customer_code == null || customer_code == "")

        {

            $("#err_customer_code").text("Please Enter Customer Code.");

            return false;

        } else

        {

            $("#err_customer_code").text("");

        }

        if (customer_code_exist == 1)

        {

            $("#err_customer_code").text(customer_code + " code already exists!");

            return false;

        }



        if (customer_name == null || customer_name == "")

        {

            $("#err_customer_name").text("Please Enter Customer Name.");

            return false;

        } else

        {

            $("#err_customer_name").text("");

        }

        if (customer_name_exist == 1)

        {

            $("#err_customer_name").text(customer_name + " name already exists!");

            return false;

        }



        if (!customer_name.match(name_regex))

        {

            $('#err_customer_name').text("Please Enter Valid Customer Name ");

            return false;

        } else

        {

            $("#err_customer_name").text("");

        }



        if (gsttype == null || gsttype == "")

        {

            $("#err_gstin_type").text("Please Select Gst Registration Type.");

            return false;

        } else

        {

            $("#err_gstin_type").text("");

        }

        if (gsttype != "Unregistered") {

            if (gstin == null || gstin == "")

            {

                $("#err_gstin").text("Please Enter GSTIN Number");

                return false;

            } else

            {

                $("#err_gstin").text("");

            }

            if (gstin.length < 15 || gstin.length > 15)

            {

                $("#err_gstin").text("GSTIN Number should have length 15");

                return false;

            } else

            {

                $("#err_gstin").text("");

            }



            var res = gstin.slice(0, 2);

            var res1 = gstin.slice(13, 14);



            if (res1 != 'Z')

            {

                $("#err_gstin").text("Please enter valid GST number(Ex:29AAAAA9999AXZX).");

                return false;

            } else

            {

                $("#err_gstin").text("");

            }

        }



        if (panno.length > 0)

        {

            $("#panno").val(panno.toUpperCase());



            if (!panno.match(pan_regex))

            {

                $('#err_panno').text("Please Enter Valid Pan Number");

                return false;

            } else

            {

                $("#err_panno").text("");

            }



            if (panno.length != 10)

            {

                $('#err_panno').text("Please Enter 10 Digit Pan Number");

                return false;



            } else

            {

                $("#err_panno").text("");

            }



        }

        if (tanno.length > 0)

        {

            $("#tanno").val(tanno.toUpperCase());



            if (!tanno.match(tan_regex))

            {

                $('#err_tan').text("Please Enter Valid Tan Number");

                return false;

            } else

            {

                $("#err_tan").text("");

            }



            if (tanno.length != 10)

            {

                $('#err_tan').text("Please Enter 10 Digit Tan Number");

                return false;



            } else

            {

                $("#err_tan").text("");

            }





        }



if (customer_ajax != "yes")

{

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



        if (mobile.length > 0)

        {

            if (mobile == null || mobile == "") {

                $("#err_mobile").text("Please Enter Mobile.");

                return false;

            } else {

                $("#err_mobile").text("");

            }



            if (!mobile.match(mobile_regex)) {

                $('#err_mobile').text("Please Enter Valid Mobile ");

                return false;

            } else {

                $("#err_mobile").text("");

            }

        }



        if (postal_code.length > 0)

        {



            if (!postal_code.match(digit_regex))

            {

                $('#err_postal_code').text("Please Enter Valid Postal Code");

                return false;

            } else

            {

                $("#err_postal_code").text("");

            }



            if (postal_code.length != 6){

                $('#err_postal_code').text("Please Enter 6 Digit Postal Code");
                return false;
            }else{
                $("#err_postal_code").text("");

            }

        }

        if (email.length > 0){
            if (!email.match(email_regex)) {
                $('#err_email').text("Please Enter Valid Email Address ");
                return false;
            } else {
                $("#err_email").text("");
            }

        }



        if (add_contact_person == 'yes')

        {

var contact_person_code = $('#contact_person_code').val()?$('#contact_person_code').val():"";

            var contact_person_name = $('#contact_person_name').val()?$('#contact_person_name').val():"";

            // var contact_person_address = $('#contact_person_address').val();

            var contact_person_city = $('#contact_person_city').val()?$('#contact_person_city').val():"";

            var contact_person_state = $('#contact_person_state').val()?$('#contact_person_state').val():"";

            var contact_person_country = $('#contact_person_country').val()?$('#contact_person_country').val():"";

            var contact_person_mobile = $('#contact_person_mobile').val()?$('#contact_person_mobile').val():"";

            var contact_person_telephone = $('#contact_person_telephone').val()?$('#contact_person_telephone').val():"";

            var contact_person_email = $('#contact_person_email').val()?$('#contact_person_email').val():"";

            var contact_person_postal_code = $('#contact_person_postal_code').val()?$('#contact_person_postal_code').val():"";



    if (customer_ajax != "yes")

    {

            if (contact_person_code == null || contact_person_code == "")

            {

                $("#err_contact_person_code").text("Please Enter Contact Person Code.");

                return false;

            } else

            {

                $("#err_contact_person_code").text("");

            }

        }

            if (contact_person_name == null || contact_person_name == "")

            {

                $("#err_contact_person_name").text("Please Enter Contact Person Name.");

                return false;

            } else

            {

                $("#err_contact_person_name").text("");

            }

     if (customer_ajax != "yes")

        {       

            if (contact_person_country == null || contact_person_country == "") {

                $("#err_contact_person_country").text("Please Select Country ");

                return false;

            } else {

                $("#err_contact_person_country").text("");

            }



            if (contact_person_state == null || contact_person_state == "") {

                $("#err_contact_person_state").text("Please Select State ");

                return false;

            } else {

                $("#err_contact_person_state").text("");

            }



            if (contact_person_city == null || contact_person_city == "") {

                $("#err_contact_person_city").text("Please Select City ");

                return false;

            } else {

                $("#err_contact_person_city").text("");

            }

        }



            if (contact_person_mobile.length > 0)

            {

                if (contact_person_mobile == null || contact_person_mobile == "") {

                    $("#err_contact_person_mobile").text("Please Enter Mobile.");

                    return false;

                } else {

                    $("#err_contact_person_mobile").text("");

                }



                if (!contact_person_mobile.match(mobile_regex)) {

                    $('#err_contact_person_mobile').text("Please Enter Valid Mobile ");

                    return false;

                } else {

                    $("#err_contact_person_mobile").text("");

                }

            }



            if (contact_person_telephone.length > 0)

            {

                if (contact_person_telephone == null || contact_person_telephone == "") {

                    $("#err_contact_person_telephone").text("Please Enter Telephone.");

                    return false;

                } else {

                    $("#err_contact_person_telephone").text("");

                }



                if (!contact_person_telephone.match(mobile_regex)) {

                    $('#err_contact_person_telephone').text("Please Enter Valid Telephone.");

                    return false;

                } else {

                    $("#err_contact_person_telephone").text("");

                }

            }



            if (contact_person_postal_code.length > 0)

            {

                if (!contact_person_postal_code.match(digit_regex))

                {

                    $('#err_contact_person_postal_code').text("Please Enter Valid Postal Code");

                    return false;

                } else

                {

                    $("#err_contact_person_postal_code").text("");

                }

                if (contact_person_postal_code.length != 6)

                {

                    $('#err_contact_person_postal_code').text("Please Enter 6 Digit Postal Code");

                    return false;



                } else

                {

                    $("#err_contact_person_postal_code").text("");

                }

            }

            if (contact_person_email.length > 0){

               

                if (!contact_person_email.match(email_regex)) {

                    $('#err_contact_person_email').text("Please Enter Valid Email Address ");

                    return false;

                } else {

                    $("#err_contact_person_email").text("");

                }

            }

        }



        if (customer_ajax == "yes")

        {

            var reference_number = $('#reference_number').val();

            var reference_type = $('#reference_type').val();

            var purpose_of_transaction = $('#purpose_of_transaction').val();

            var type_of_transaction = $('#type_of_transaction').val();

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

                    'email': $('#email').val(),

                    'gstin': $('#gstin').val(),

                    'postal_code': $('#postal_code').val(),

                    'state_code': $('#state_code').val(),

                    'panno': $('#panno').val(),

                    'tanno': $('#tanno').val(),

                    'address': $("#address").val(),

                    'country': $("#country").val(),

                    'state': $("#state").val(),

                    'city': $("#city").val(),

                    'mobile': $('#mobile').val(),

                    'telephone': $('#telephone').val(),

                    'website': $('#website').val(),

                    'add_contact_person': add_contact_person,

                    'contact_person_name': $('#contact_person_name').val(),

                    'contact_person_code': $('#contact_person_code').val(),

                    'contact_person_country': $("#contact_person_country").val(),

                    'contact_person_state': $("#contact_person_state").val(),

                    'contact_person_city': $("#contact_person_city").val(),

                    'contact_person_email': $('#contact_person_email').val(),

                    'contact_person_mobile': $('#contact_person_mobile').val()

                },

                success: function (result)

                {

                    var data = result['data'];

                    var ledgers_data = result['ledgers_data'];

                    $('#customer').html('');

                    $('#customer').append('<option value="">Select</option>');

                    for (i = 0; i < data.length; i++) {

                        $('#customer').append('<option value="' + data[i].customer_id  + '">' + data[i].customer_name + '</option>');

                    }

                    $('#customer').val(result['id']).attr("selected", "selected");

                    $('#customer').change();



                    $("#customerForm")[0].reset();





                    if (type_of_transaction == "Advance Taken" || type_of_transaction == "Payment of Advance Taken")

                    {





                        $('#from_to').html('');

                        $('#from_to').append('<option value="">Select</option>');

                        for (i = 0; i < ledgers_data.length; i++)

                        {



                            $('#from_to').append('<option value="' + ledgers_data[i].ledger_id + '">' + ledgers_data[i].ledger_title + '</option>');



                        }



                        $('#from_to').val(result['ledger_id']).attr("selected", "selected");

                        $('#from_to').change();

                    }

                }





            });

        }



    });



    $("#customer_code").on("blur", function (event) {



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

            success: function (result) {

                if (result[0].num > 0)

                {

                    $('#customer_code').val("");

                    $("#err_customer_code").text(customer_code + " code already exists!");

                    customer_code_exist = 1;

                } else

                {

                    $("#err_customer_code").text("");

                    customer_code_exist = 0;

                }

            }

        });

    });



    // Customer Name check

    $("#customer_name").on("blur", function (event) {



        var customer_name = $('#customer_name').val();

        var ledger_id = $('#customer_ledger_id').val();

        if (customer_name.length > 0)

        {

            if (customer_name == null || customer_name == "") {

                $("#err_customer_name").text("Please Enter Customer Name.");

                return false;

            } else {

                $("#err_customer_name").text("");

            }

            if (!customer_name.match(general_regex)) {

                $('#err_customer_name').text("Please Enter Valid Customer Name ");

                return false;

            } else {

                $("#err_customer_name").text("");

            }



            $.ajax({

                url: base_url + 'ledger/get_check_ledger',

                dataType: 'JSON',

                method: 'POST',

                data: {

                    'ledger_name': customer_name,

                    'ledger_id': ledger_id

                },

                success: function (result) {

                    if (result[0].num > 0)

                    {

                        $('#customer_name').val("");

                        $("#err_customer_name").text(customer_name + " name already exists!");

                    } else

                    {

                        $("#err_customer_name").text("");

                    }

                }

            });

        }

    });



    $("#contact_person_name").on("blur keyup", function (event) {

        var contact_person_name = $('#contact_person_name').val();

        if (contact_person_name > 0)

        {

            // if (contact_person_name == null || contact_person_name == "") {

            //     $("#err_contact_person_name").text("Please Enter Contact Person Name.");

            //     return false;

            // } else {

            //     $("#err_contact_person_name").text("");

            // }

            if (!contact_person_name.match(general_regex)) {

                $('#err_contact_person_name').text("Please Enter Valid Contact Person Name ");

                return false;

            } else {

                $("#err_contact_person_name").text("");

            }

        }

    });







    $("#gstin").on("blur keyup", function (event) {

        var gstin = $('#gstin').val();

        $("#gstin").val(gstin.toUpperCase());

        var gstin_length = gstin.length;

        if (gstin_length > 0)

        {

            if (gstin == null || gstin == "") {

                $("#err_gstin").text("Please Enter GSTIN Number.");

                return false;

            } else {

                $("#err_gstin").text("");

            }

            if (!gstin.match(sname_regex)) {

                $('#err_gstin').text("Please Enter Valid GSTIN Number.");

                return false;

            } else {

                $("#err_gstin").text("");

            }

            if (gstin_length < 15 || gstin_length > 15)

            {

                $("#err_gstin").text("GSTIN Number should have length 15");

                return false;

            } else {

                $("#err_gstin").text("");

            }



            var res = gstin.slice(2, 12);

            $('#panno').val(res);

        }

    });



    $("#panno").on("blur keyup", function (event)

    {

        if ($('#panno').val() != "")

        {

            var panno = $('#panno').val();



            $("#panno").val(panno.toUpperCase());



            if (!panno.match(pan_regex))

            {

                $('#err_panno').text("Please Enter Valid Pan Number");

                return false;

            } else

            {

                $("#err_panno").text("");

            }



            if (panno.length != 10)

            {

                $('#err_panno').text("Please Enter 10 Digit Pan Number");

                return false;



            } else

            {

                $("#err_panno").text("");

            }

        }

    });



    $("#tanno").on("blur keyup", function (event)

    {

        if ($('#tanno').val() != "")

        {

            var tanno = $('#tanno').val();



            $("#tanno").val(tanno.toUpperCase());



            if (!tanno.match(tan_regex))

            {

                $('#err_tan').text("Please Enter Valid Tan Number");

                return false;

            } else

            {

                $("#err_tan").text("");

            }



            if (tanno.length != 10)

            {

                $('#err_tan').text("Please Enter 10 Digit Tan Number");

                return false;



            } else

            {

                $("#err_tan").text("");

            }



        }

    });





    $("#address").on("blur keyup", function (event) {

        var address = $('#address').val();

        if (address.length > 0)

        {

            if (address == null || address == "") {

                $("#err_address").text("Please Enter Address");

                return false;

            } else {

                $("#err_address").text("");

            }

            if (!address.match(general_regex)) {

                $('#err_address').text("Please Enter Valid Address");

                return false;

            } else {

                $("#err_address").text("");

            }

        }



    });





    $("#country").change(function (event) {

        var country = $('#country').val();

        if (country == null || country == "") {

            $("#err_country").text("Please Select Country");

            return false;

        } else {

            $("#err_country").text("");

        }

    });







    $("#state").change(function (event) {

        var state = $('#state').val();

        if (state == null || state == "") {

            $("#err_state").text("Please Select State ");

            return false;

        } else {

            $("#err_state").text("");

        }

    });



    $("#city").change(function (event) {

        var city = $('#city').val();

        if (city == null || city == "") {

            $("#err_city").text("Please Select City ");

            return false;

        } else {

            $("#err_city").text("");

        }



    });





    $("#postal_code").on("blur keyup", function (event)

    {

        if ($('#postal_code').val() != "")

        {

            var postal_code = $('#postal_code').val();

            if (!postal_code.match(digit_regex))

            {

                $('#err_postal_code').text("Please Enter Valid Postal Code");

                return false;

            } else

            {

                $("#err_postal_code").text("");

            }



            if (postal_code.length != 6)

            {

                $('#err_postal_code').text("Please Enter 6 Digit Postal Code");

                return false;



            } else

            {

                $("#err_postal_code").text("");

            }

        }

    });



    $("#mobile").on("blur keyup", function (event) {

        if ($('#mobile').val() != "")

        {

            var mobile = $('#mobile').val();

            $('#mobile').val(mobile);

            if (mobile == null || mobile == "") {

                $("#err_mobile").text("Please Enter Mobile.");

                return false;

            } else {

                $("#err_mobile").text("");

            }

            if (!mobile.match(mobile_regex)) {

                $('#err_mobile').text("Please Enter Valid Mobile ");

                return false;

            } else {

                $("#err_mobile").text("");

            }

        }

    });





    $("#email").on("blur keyup", function (event) {

        if ($('#email').val() != "")

        {

            var email = $('#email').val();

            $('#email').val(email);

            if (email == null || email == "") {

                $("#err_email").text("Please Enter Email.");

                return false;

            } else {

                $("#err_email").text("");

            }

            if (!email.match(email_regex)) {

                $('#err_email').text("Please Enter Valid Email Address ");

                return false;

            } else {

                $("#err_email").text("");

            }

        }

    });



});

