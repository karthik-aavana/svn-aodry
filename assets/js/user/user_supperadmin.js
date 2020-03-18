if (typeof user_ajax === 'undefined' || user_ajax === null) {
    var user_ajax = "no";
}
$(document).ready(function () {
    var email_exist = 0;
    $("#user_submit").click(function (event)
    {
        var firm_name = $('#firm_id').val();
        var branch_name = $('#branch_id').val();
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var company = $('#company').val();
        var email = $('#email').val();
        var mobile = $('#phone').val();
        var password = $('#password').val();
        var password_confirm = $('#password_confirm').val();   
        var mobile_regex = /^[1-9][0-9]{9,14}$/;  

        if (firm_name == null || firm_name == ""){
            $("#err_firm").text("Please Select Firm.");
            return false;
        }else{
            $("#err_firm").text("");
        } 

        if (branch_name == null || branch_name == ""){
            $("#err_branch").text("Please Select Branch.");
            return false;
        }else{
            $("#err_first_name").text("");
        }
        //First name
        if (first_name == null || first_name == "")
        {
            $("#err_first_name").text("Please Enter First Name.");
            return false;
        } else
        {
            $("#err_first_name").text("");
        }
        if (!first_name.match(name_regex))
        {
            $('#err_first_name').text("Please Enter Valid First Name ");
            return false;
        } else
        {
            $("#err_first_name").text("");
        }
        //Last Name
        if (last_name == null || last_name == "")
        {
            $("#err_last_name").text("Please Enter Last Name.");
            return false;
        } else
        {
            $("#err_last_name").text("");
        }
        if (!last_name.match(name_regex))
        {
            $('#err_last_name').text("Please Enter Valid Last Name ");
            return false;
        } else
        {
            $("#err_last_name").text("");
        }
        // if (company == null || company == "") 
        // {
        //     $("#err_company").text("Please Enter Company Name.");
        //     return false;
        // } else 
        // {
        //     $("#err_company").text("");
        // }
        if (email_exist == 1)
        {
            $("#err_email").text(email + " already exists!");
            return false;
        }
        if (email == null || email == "")
        {
            $("#err_email").text("Please Enter Email.");
            return false;
        } else
        {
            $("#err_email").text("");
        }
        if (!email.match(email_regex))
        {
            $('#err_email').text("Please Enter Valid Email Address ");
            return false;
        } else
        {
            $("#err_email").text("");
        }
        if (mobile == null || mobile == "") {
            $("#err_phone").text("Please Enter Mobile.");
            return false;
        } else {
            $("#err_phone").text("");
        } 
        if (!mobile.match(mobile_regex)) {
            $('#err_phone').text("Please Enter Valid Mobile ");
            return false;
        } else {
            $("#err_phone").text("");
        }
        if ($('#section_area').val() != "edit_user")
        {
            if (password == null || password == "") {
                $("#err_password").text("Please Enter Password.");
                return false;
            } else {
                $("#err_password").text("");
            }
            if (password_confirm == null || password_confirm == "") {
                $("#err_password_confirm").text("Please Enter Confirm Password.");
                return false;
            } else
            {
                $("#err_password_confirm").text("");
            }
        }
        if (password != password_confirm)
        {
            $('#err_password_confirm').text('Password Not matching');
            return false;
        }
    });

    $("#first_name").on("blur", function (event) {
        var first_name = $('#first_name').val();
        var mobile_regex = /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/; 
        if (first_name.length > 0)
        {
            if (first_name == null || first_name == "") {
                $("#err_first_name").text("Please Enter First Name.");
                return false;
            } else {
                $("#err_first_name").text("");
            }
            if (!first_name.match(general_regex)) {
                $('#err_first_name').text("Please Enter Valid First Name ");
                return false;
            } else {
                $("#err_first_name").text("");
            }
        }
    });
    $("#last_name").on("blur", function (event) {
        var last_name = $('#last_name').val();
        if (last_name.length > 0)
        {
            if (last_name == null || last_name == "") {
                $("#err_last_name").text("Please Enter Last  Name.");
                return false;
            } else {
                $("#err_last_name").text("");
            }
            if (!last_name.match(general_regex)) {
                $('#err_last_name').text("Please Enter Valid Last Name.");
                return false;
            } else {
                $("#err_last_name").text("");
            }
        }
    });
    $("#company").on("blur", function (event) {
        var company = $('#company').val();
        if (company == null || company == "") {
            $("#err_company").text("Please Enter Company Name.");
            return false;
        } else {
            $("#err_company").text("");
        }
    });
    
    $("#email").on("blur", function (event)
    {
        var email = $('#email').val();
        var id = $('#id').val();
        var branch_id = $('[name=branch_id]').val();        
        if (email.length > 0)
        {
            if (email == null || email == "")
            {
                $("#err_email").text("Please Enter Email.");
                return false;
            } else
            {
                $("#err_email").text("");
            }
            if (!email.match(email_regex) ) {
              $('#err_email').text("Please Enter Valid Email Address ");   
              return false;
            }
            else{
              $("#err_email").text("");
            }
            $.ajax({
                url: base_url + 'auth/get_check_email',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'email': email,
                    'id': id,
                   /* 'branch_id' : branch_id*/
                },
                success: function (result) {
                    if (result[0].num > 0)
                    {
                        $('#email').val("");
                        $("#err_email").text(email + " already exists!");
                        email_exist = 1;
                    } else
                    {
                        $("#err_email").text("");
                        email_exist = 0;
                    }
                }
            });
        }
    });
    $("#phone").on("blur", function (event)
    {
        var mobile = $('#phone').val();
        var mobile_regex = /[1-9][0-9]{9,14}/; 
        $('#mobile').val(mobile);
        if (mobile == null || mobile == "") {
            $("#err_phone").text("Please Enter Mobile.");
            return false;
        } else {
            $("#err_phone").text("");
        }
        if (!mobile.match(mobile_regex)) {
            $('#err_phone').text("Please Enter Valid Mobile ");
            return false;
        } else {
            $("#err_phone").text("");
        }
    });
    $("#email").on("blur", function (event) {
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
            if (!email.match(email_regex) ) {
              $('#err_email').text("Please Enter Valid Email Address ");   
              return false;
            }
            else{
              $("#err_email").text("");
            }
        }
    });
    if ($('#section_area').val() != "edit_user")
    {
        $("#password").on("blur", function (event) {
            var password = $('#password').val();
            if (password.length > 0)
            {
                if (password == null || password == "") {
                    $("#err_password").text("Please Enter Password.");
                    return false;
                } else {
                    $("#err_password").text("");
                }
            }
        });
        $("#password_confirm").on("blur", function (event) {
            var password_confirm = $('#password_confirm').val();
            if (password_confirm.length > 0)
            {
                if (password_confirm == null || password_confirm == "") {
                    $("#err_password_confirm").text("Please Enter Confirm Password.");
                    return false;
                } else {
                    $("#err_password_confirm").text("");
                }
            }
        });
    }
});
