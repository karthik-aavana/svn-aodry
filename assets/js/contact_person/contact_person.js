if (typeof customer_ajax === 'undefined' || customer_ajax === null) {
    var customer_ajax = "no";
}

$(document).ready(function ()
{
    $("#party_type").change(function (event)
    {
        var party_type = $('#party_type').val();
        $("#party_id").html('');
        if (party_type.length > 0)
        {
            $.ajax({
                url: base_url + 'contact_person/get_party_name',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'party_type': party_type
                },
                success: function (result)
                {
                    $("#party_id").html(result);
                }
            });
        } else
        {
            $("#party_id").html('<option value="">Select</option>');
        }
    });

    $("#contact_person_submit").click(function (event)
    {
        var contact_person_code = $('#contact_person_code').val();
        var contact_person_name = $('#contact_person_name').val();
        var party_type = $('#party_type').val();
        var party_id = $('#party_id').val();
        var address = $('#address').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var country = $('#country').val();
        var mobile = $('#mobile').val();
        var telephone = $('#telephone').val();
        var email = $('#email').val();
        var postal_code = $('#postal_code').val();
        var website = $('#website').val();
        var designation = $('#designation').val();
        var department = $('#department').val();
        var industry = $('#industry').val();

        if (contact_person_code == null || contact_person_code == "")
        {
            $("#err_contact_person_code").text("Please Enter Contact Person Code.");
            return false;
        } else
        {
            $("#err_contact_person_code").text("");
        }
        if (contact_person_name == null || contact_person_name == "")
        {
            $("#err_contact_person_name").text("Please Enter Contact Person Name.");
            return false;
        } else
        {
            $("#err_contact_person_name").text("");
        }

        if (party_type == null || party_type == "")
        {
            $("#err_party_type").text("Please Select Party Type.");
            return false;
        } else
        {
            $("#err_party_type").text("");
        }
        if (party_id == null || party_id == "")
        {
            $("#err_party_id").text("Please Select Party Name.");
            return false;
        } else
        {
            $("#err_party_id").text("");
        }

        // if (address == null || address == "") 
        // {
        //     $("#err_address").text("Please Enter Address");
        //     return false;
        // } else {
        //     $("#err_address").text("");
        // }
        // if (!address.match(general_regex)) 
        // {
        //     $('#err_address').text("Please Enter Valid Address");
        //     return false;
        // } 
        // else 
        // {
        //     $("#err_address").text("");
        // }

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


        if (telephone.length > 0)
        {
            if (telephone == null || telephone == "") {
                $("#err_telephone").text("Please Enter Telephone.");
                return false;
            } else {
                $("#err_telephone").text("");
            }

            if (!telephone.match(mobile_regex)) {
                $('#err_telephone').text("Please Enter Valid Telephone.");
                return false;
            } else {
                $("#err_telephone").text("");
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

            if (postal_code.length != 6)
            {
                $('#err_postal_code').text("Please Enter 6 Digit Postal Code");
                return false;

            } else
            {
                $("#err_postal_code").text("");
            }


        }
        if (email.length > 0)
        {
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

        // if (website == null || website == "") 
        // {
        //     $("#err_website").text("Please Enter Website.");
        //     return false;
        // }
        // else
        // {
        //     $("#err_website").text("");
        // }
        // if (industry == null || industry == "") 
        // {
        //     $("#err_industry").text("Please Enter Industry.");
        //     return false;
        // }
        // else
        // {
        //     $("#err_industry").text("");
        // }
    });


    $("#contact_person_name").on("blur", function (event) {
        var contact_person_name = $('#contact_person_name').val();
        if (contact_person_name > 0)
        {
            if (!contact_person_name.match(general_regex)) {
                $('#err_contact_person_name').text("Please Enter Valid Contact Person Name.");
                return false;
            } else {
                $("#err_contact_person_name").text("");
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

    $("#telephone").on("blur keyup", function (event) {
        if ($('#telephone').val() != "")
        {
            var telephone = $('#telephone').val();
            $('#telephone').val(telephone);
            if (telephone == null || telephone == "") {
                $("#err_telephone").text("Please Enter Telephone.");
                return false;
            } else {
                $("#err_telephone").text("");
            }
            if (!telephone.match(mobile_regex)) {
                $('#err_telephone').text("Please Enter Valid Telephone ");
                return false;
            } else {
                $("#err_telephone").text("");
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
