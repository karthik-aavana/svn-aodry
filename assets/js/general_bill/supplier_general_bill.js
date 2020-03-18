if (typeof supplier_ajax === 'undefined' || supplier_ajax === null) {
    var supplier_ajax = "no";
}

$(document).ready(function () {

    var supplier_name_exist = 0;

    $("#supplier_modal_submit").click(function (event) {

        var supplier_name = $('#supplier_name').val();
        var address = $('#supplier_address').val();
        var city = $('#supplier_city').val();
        var state = $('#supplier_state').val();
        var country = $('#supplier_country').val();
        var mobile = $('#supplier_mobile').val();
        var email = $('#supplier_email').val();
        var gsttype = $('#supplier_gstregtype').val();
        var gstin = $('#supplier_gstin').val();
        var postal_code = $('#supplier_postal_code').val();
        var panno = $('#supplier_panno').val();
        var tanno = $('#supplier_tanno').val();
        var state_code = $('#supplier_state_code').val();
        var contact_person_name = $('#supplier_contact_person_name').val();
        var contact_person_department = $('#supplier_contact_person_department').val();
        var contact_person_email = $('#supplier_contact_person_email').val();
        var contact_person_mobile = $('#supplier_contact_person_mobile').val();

        if (supplier_name_exist == 1)
        {
            $("#err_supplier_name").text(supplier_name + " name already exists!");
            return false;
        }
        if (supplier_name == null || supplier_name == "") {
            $("#err_supplier_name").text("Please Enter supplier Name.");
            return false;
        } else {
            $("#err_supplier_name").text("");
        }
        if (!supplier_name.match(name_regex)) {
            $('#err_supplier_name').text("Please Enter Valid supplier Name ");
            return false;
        } else {
            $("#err_supplier_name").text("");
        }


        if ($("#supplier_gstregtype").val() == "")
        {
            ;
            $("#gstn_selected").hide();
            $("#err_supplier_gstid_type").text("");
        }
        $("#supplier_gstregtype").change(function () {
            var gstn_type = $(this).val();

            if (gstn_type == 'Unregistered') {
                $("#gstn_selected").hide();
            } else if (gstn_type == 'Registered') {
                $("#gstn_selected").show();
                $("#err_supplier_gstid_type").text("");
            } else {

                $("#gstn_selected").hide();
                $("#err_supplier_gstid_type").text("");
            }
        });

        call_css();

        if (gsttype == null || gsttype == "") {
            $("#err_gstin_type").text("Please Select Gst Registration Type.");
            return false;
        } else {
            $("#err_gstin_type").text("");
        }
        if (gsttype != "Unregistered") {
            if (gstin == null || gstin == "") {
                $("#err_supplier_gstin").text("Please Enter GSTIN Number");
                return false;
            } else {
                $("#err_supplier_gstin").text("");
            }
            if (gstin.length < 15 || gstin.length > 15) {
                $("#err_supplier_gstin").text("GSTIN Number should have length 15");
                return false;
            } else {
                $("#err_supplier_gstin").text("");
            }

            var res = gstin.slice(0, 2);
            var res1 = gstin.slice(13, 14);

            if (res != state_code || res1 != 'Z') {
                $("#err_supplier_gstin").text("Please enter valid GST number(Ex:29AAAAA9999AXZX).");
                return false;
            } else {
                $("#err_supplier_gstin").text("");
            }
            var res = gstin.slice(2, 12);
            $('#supplier_panno').val(res);
        }

        if (panno.length > 0)
        {
            $("#supplier_panno").val(panno.toUpperCase());

            if (!panno.match(alphnum_regex))
            {
                $('#err_supplier_panno').text("Please Enter Valid Pan Number");
                return false;
            } else
            {
                $("#err_supplier_panno").text("");
            }

            if (panno.length != 10)
            {
                $('#err_supplier_panno').text("Please Enter 10 Digit Pan Number");
                return false;

            } else
            {
                $("#err_supplier_panno").text("");
            }
        }
        if (tanno.length > 0)
        {
            $("#supplier_tanno").val(tanno.toUpperCase());

            if (!tanno.match(alphnum_regex))
            {
                $('#err_supplier_tan').text("Please Enter Valid Tan Number");
                return false;
            } else
            {
                $("#err_supplier_tan").text("");
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

        if (address == null || address == "") {
            $("#err_supplier_address").text("Please Enter Address");
            return false;
        } else {
            $("#err_supplier_address").text("");
        }
        if (!address.match(general_regex)) {
            $('#err_supplier_address').text("Please Enter Valid Address");
            return false;
        } else {
            $("#err_supplier_address").text("");
        }

        //  if (contact_person_name == null || contact_person_name == "") {
        //     $("#err_contact_person_name").text("Please Enter Contact Person Name.");
        //     return false;
        // } else {
        //     $("#err_contact_person_name").text("");
        // }
        if (contact_person_name.length > 1)
        {
            if (!contact_person_name.match(name_regex)) {
                $('#err_supplier_contact_person_name').text("Please Enter Valid Contact Person Name ");
                return false;
            } else {
                $("#err_supplier_contact_person_name").text("");
            }
        }

        if (country == null || country == "") {
            $("#err_supplier_country").text("Please Select Country ");
            return false;
        } else {
            $("#err_supplier_country").text("");
        }

        if (state == null || state == "") {
            $("#err_supplier_state").text("Please Select State ");
            return false;
        } else {
            $("#err_supplier_state").text("");
        }



        if (city == null || city == "") {
            $("#err_supplier_city").text("Please Select City ");
            return false;
        } else {
            $("#err_supplier_city").text("");
        }

        if (mobile.length > 0)
        {
            if (mobile == null || mobile == "") {
                $("#err_supplier_mobile").text("Please Enter Mobile.");
                return false;
            } else {
                $("#err_supplier_mobile").text("");
            }

            if (!mobile.match(mobile_regex)) {
                $('#err_supplier_mobile').text("Please Enter Valid Mobile ");
                return false;
            } else {
                $("#err_supplier_mobile").text("");
            }
        }

        if (postal_code.length > 0)
        {

            if (!postal_code.match(digit_regex))
            {
                $('#err_supplier_postal_code').text("Please Enter Valid Postal Code");
                return false;
            } else
            {
                $("#err_supplier_postal_code").text("");
            }

            if (postal_code.length != 6)
            {
                $('#err_supplier_postal_code').text("Please Enter 6 Digit Postal Code");
                return false;

            } else
            {
                $("#err_supplier_postal_code").text("");
            }


        }
        if (email.length > 0)
        {
            if (email == null || email == "") {
                $("#err_supplier_email").text("Please Enter Email.");
                return false;
            } else {
                $("#err_supplier_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_supplier_email').text("Please Enter Valid Email Address ");
                return false;
            } else {
                $("#err_supplier_email").text("");
            }
        }

        if (supplier_ajax == "yes")
        {
            var purpose_of_transaction = $('#purpose_of_transaction').val();
            var type_of_transaction = $('#type_of_transaction').val();

            $.ajax({
                url: base_url + 'supplier/add_supplier_ajax',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'supplier_name': $('#supplier_name').val(),
                    'supplier_code': $('#supplier_code').val(),
                    'gstregtype': $('#supplier_gstregtype').val(),
                    'supplier_area': $('#supplier_area').val(),
                    'state_id': $('#state_id').val(),
                    'email': $('#supplier_email').val(),
                    'gstin': $('#supplier_gstin').val(),
                    'postal_code': $('#supplier_postal_code').val(),
                    'state_code': $('#state_code').val(),
                    'panno': $('#supplier_panno').val(),
                    'tanno': $('#supplier_tanno').val(),
                    'address': $("#supplier_address").val(),
                    'country': $("#supplier_country").val(),
                    'state': $("#supplier_state").val(),
                    'city': $("#supplier_city").val(),
                    'mobile': $('#supplier_mobile').val(),
                    'contact_person_name': $('#supplier_contact_person_name').val(),
                    'contact_person_department': $('#supplier_contact_person_department').val(),
                    'contact_person_email': $('#supplier_contact_person_email').val(),
                    'contact_person_mobile': $('#supplier_contact_person_mobile').val()
                },
                success: function (result) {
                    var data = result['data'];

                    var ledgers_data = result['ledgers_data'];


                    if (type_of_transaction == "Advance Given" || type_of_transaction == "Receipt of Advance Given")
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





                    $("#supplierForm")[0].reset();

                }
            });

        }

    });



    // supplier Name check
    $("#supplier_name").on("blur", function (event) {
        var supplier_name = $('#supplier_name').val();
        if (supplier_name > 0)
        {
            var ledger_id = $('#supplier_ledger_id').val();
            if (supplier_name == null || supplier_name == "") {
                $("#err_supplier_name").text("Please Enter Supplier Name.");
                return false;
            } else {
                $("#err_supplier_name").text("");
            }
            if (!supplier_name.match(general_regex)) {
                $('#err_supplier_name').text("Please Enter Valid Supplier Name ");
                return false;
            } else {
                $("#err_supplier_name").text("");
            }

            $.ajax({
                url: base_url + 'ledger/get_check_ledger',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'ledger_name': supplier_name,
                    'ledger_id': ledger_id
                },
                success: function (result) {
                    if (result[0].num > 0)
                    {
                        $('#supplier_name').val("");
                        $("#err_supplier_name").text(supplier_name + " name already exists!");
                    } else
                    {
                        $("#err_supplier_name").text("");
                    }
                }
            });
        }
    });

    $("#supplier_name").on("blur", function (event) {
        var supplier_name = $('#supplier_name').val();
        var ledger_id = $('#supplier_ledger_id').val();

        $.ajax({
            url: base_url + 'ledger/get_check_ledger',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'ledger_name': supplier_name,
                'ledger_id': ledger_id
            },
            success: function (result) {
                if (result[0].num > 0)
                {
                    $("#err_supplier_name").text(supplier_name + " name already exists!");
                    supplier_name_exist = 1;
                } else
                {
                    $("#err_supplier_name").text("");
                    supplier_name_exist = 0;
                }
            }
        });
    });

    $("#supplier_contact_person_name").on("blur", function (event) {

        var contact_person_name = $('#supplier_contact_person_name').val();
        if (contact_person_name.length > 0)
        {
            // if (contact_person_name == null || contact_person_name == "") {
            //     $("#err_contact_person_name").text("Please Enter Contact Person Name.");
            //     return false;
            // } else {
            //     $("#err_contact_person_name").text("");
            // }
            if (!contact_person_name.match(general_regex)) {
                $('#err_supplier_contact_person_name').text("Please Enter Valid Contact Person Name ");
                return false;
            } else {
                $("#err_supplier_contact_person_name").text("");
            }
        }
    });



    //   $("#gstin").on("blur keyup", function (event) 
    //   {


    // if ($('#gstin').val() != "")
    //   {
    //        var gstin = $('#gstin').val();

    //       $("#gstin").val(gstin.toUpperCase());

    //           if (gstin == null || gstin == "") {
    //               $("#err_gstin").text("Please Enter GSTIN Number.");
    //               return false;
    //           } else {
    //               $("#err_gstin").text("");
    //           }
    //           if (!gstin.match(sname_regex)) {
    //               $('#err_gstin').text("Please Enter Valid GSTIN Number.");
    //               return false;
    //           } else {
    //               $("#err_gstin").text("");
    //           }
    //           if (gstin.length <15 || gstin.length>15) {

    //               $("#err_gstin").text("GSTIN Number should have length 15");
    //               return false;
    //           } else {
    //               $("#err_gstin").text("");
    //           }
    //       }
    //   });

    $("#supplier_gstin").on("blur keyup", function (event) {
        var gstin = $('#supplier_gstin').val();
        $("#supplier_gstin").val(gstin.toUpperCase());
        var gstin_length = gstin.length;
        if (gstin_length > 0)
        {
            if (gstin == null || gstin == "") {
                $("#err_supplier_gstin").text("Please Enter GSTIN Number.");
                return false;
            } else {
                $("#err_supplier_gstin").text("");
            }
            if (!gstin.match(sname_regex)) {
                $('#err_supplier_gstin').text("Please Enter Valid GSTIN Number.");
                return false;
            } else {
                $("#err_supplier_gstin").text("");
            }
            if (gstin_length < 15 || gstin_length > 15)
            {
                $("#err_supplier_gstin").text("GSTIN Number should have length 15");
                return false;
            } else {
                $("#err_supplier_gstin").text("");
            }

            var res = gstin.slice(2, 12);
            $('#supplier_panno').val(res);
        }
    });

    $("#supplier_panno").on("blur keyup", function (event)
    {
        if ($('#supplier_panno').val() != "")
        {
            var panno = $('#supplier_panno').val();

            $("#supplier_panno").val(panno.toUpperCase());

            if (!panno.match(pan_regex))
            {
                $('#err_supplier_panno').text("Please Enter Valid Pan Number");
                return false;
            } else
            {
                $("#err_supplier_panno").text("");
            }

            if (panno.length != 10)
            {
                $('#err_supplier_panno').text("Please Enter 10 Digit Pan Number");
                return false;

            } else
            {
                $("#err_supplier_panno").text("");
            }
        }
    });

    $("#supplier_tanno").on("blur keyup", function (event)
    {
        if ($('#supplier_tanno').val() != "")
        {
            var tanno = $('#supplier_tanno').val();

            $("#supplier_tanno").val(tanno.toUpperCase());

            if (!tanno.match(tan_regex))
            {
                $('#err_supplier_tan').text("Please Enter Valid Tan Number");
                return false;
            } else
            {
                $("#err_supplier_tan").text("");
            }

            if (tanno.length != 10)
            {
                $('#err_supplier_tan').text("Please Enter 10 Digit Tan Number");
                return false;

            } else
            {
                $("#err_supplier_tan").text("");
            }

        }
    });


    $("#supplier_address").on("blur", function (event) {
        var address = $('#supplier_address').val();
        if (address.length > 0)
        {
            if (address == null || address == "") {
                $("#err_supplier_address").text("Please Enter Address");
                return false;
            } else {
                $("#err_supplier_address").text("");
            }
            if (!address.match(general_regex)) {
                $('#err_supplier_address').text("Please Enter Valid Address");
                return false;
            } else {
                $("#err_supplier_address").text("");
            }
        }
    });


    $("#supplier_country").change(function (event) {
        var country = $('#supplier_country').val();
        if (country == null || country == "") {
            $("#err_supplier_country").text("Please Select Country");
            return false;
        } else {
            $("#err_supplier_country").text("");
        }
    });
    $("#supplier_gstregtype").change(function (event) {
        var gstregtype = $('#supplier_gstregtype').val();
        if (gstregtype == null || gstregtype == "") {
            $("#err_supplier_gstin_type").text("Please Select GST Registration");
            return false;
        } else {
            $("#err_supplier_gstin_type").text("");
        }
    });


    $("#supplier_state").change(function (event) {
        var state = $('#supplier_state').val();
        if (state == null || state == "") {
            $("#err_supplier_state").text("Please Select State ");
            return false;
        } else {
            $("#err_supplier_state").text("");
        }
    });

    $("#supplier_city").change(function (event) {
        var city = $('#supplier_city').val();
        if (city == null || city == "") {
            $("#err_supplier_city").text("Please Select City ");
            return false;
        } else {
            $("#err_supplier_city").text("");
        }

    });


    $("#supplier_postal_code").on("blur", function (event)
    {
        if ($('#supplier_postal_code').val() != "")
        {
            var postal_code = $('#supplier_postal_code').val();
            if (!postal_code.match(digit_regex))
            {
                $('#err_supplier_postal_code').text("Please Enter Valid Postal Code");
                return false;
            } else
            {
                $("#err_supplier_postal_code").text("");
            }

            if (postal_code.length != 6)
            {
                $('#err_supplier_postal_code').text("Please Enter 6 Digit Postal Code");
                return false;

            } else
            {
                $("#err_supplier_postal_code").text("");
            }
        }
    });

    $("#supplier_mobile").on("blur", function (event) {
        if ($('#supplier_mobile').val() != "")
        {
            var mobile = $('#supplier_mobile').val();
            $('#mobile').val(mobile);
            if (mobile == null || mobile == "") {
                $("#err_supplier_mobile").text("Please Enter Mobile.");
                return false;
            } else {
                $("#err_supplier_mobile").text("");
            }
            if (!mobile.match(mobile_regex)) {
                $('#err_supplier_mobile').text("Please Enter Valid Mobile ");
                return false;
            } else {
                $("#err_supplier_mobile").text("");
            }
        }
    });


    $("#supplier_email").on("blur", function (event) {
        if ($('#supplier_email').val() != "")
        {
            var email = $('#supplier_email').val();
            $('#supplier_email').val(email);
            if (email == null || email == "") {
                $("#err_supplier_email").text("Please Enter Email.");
                return false;
            } else {
                $("#err_email").text("");
            }
            if (!email.match(email_regex)) {
                $('#err_supplier_email').text("Please Enter Valid Email Address ");
                return false;
            } else {
                $("#err_supplier_email").text("");
            }
        }
    });

});
