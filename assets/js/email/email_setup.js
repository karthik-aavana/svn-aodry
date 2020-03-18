$(document).ready(function ()
{
    $("#email_setup_submit").click(function (event) {
        // var name_regex = /^[a-zA-Z\s]+$/;
        var email_protocol = $('#email_protocol').val();
        var smtp_host = $('#smtp_host').val();
        var smtp_port = $('#smtp_port').val();
        var smtp_secure = $('#smtp_secure').val();
        var smtp_user_name = $('#smtp_user_name').val();
        var smtp_password = $('#smtp_password').val();
        var from_name = $('#from_name').val();
        var reply_mail = $('#reply_mail').val();

        if (email_protocol == null || email_protocol == "")
        {
            $("#err_email_protocol").text("Please Enter Email Protocol");
            return false;
        } else
        {
            $('#err_email_protocol').text("");
        }

        if (smtp_host == null || smtp_host == "")
        {
            $("#err_smtp_host").text("Please Enter SMTP Host");
            return false;
        } else
        {
            $('#err_smtp_host').text("");
        }

        if (smtp_port == null || smtp_port == "")
        {
            $("#err_smtp_port").text("Please Enter SMTP Port");
            return false;
        } else
        {
            $("#err_smtp_port").text("");
        }

        if (smtp_secure == null || smtp_secure == "")
        {
            $("#err_smtp_secure").text("Please Enter SMTP Secure");
            return false;
        } else
        {
            $('#err_smtp_secure').text("");
        }

        if (smtp_user_name == null || smtp_user_name == "")
        {
            $("#err_smtp_user_name").text("Please Enter SMTP User Name");
            return false;
        } else
        {
            $("#err_smtp_user_name").text("");
        }

        if (smtp_password == null || smtp_password == "")
        {
            $("#err_smtp_password").text("Please Enter SMTP Password");
            return false;
        } else
        {
            $('#err_smtp_password').text("");
        }

        if (from_name == null || from_name == "")
        {
            $("#err_from_name").text("Please Enter From Name");
            return false;
        } else
        {
            $("#err_from_name").text("");
        }

        if (reply_mail == null || reply_mail == "")
        {
            $("#err_reply_mail").text("Please Enter Reply Mail");
            return false;
        } else
        {
            $("#err_reply_mail").text("");
        }
    });
});
