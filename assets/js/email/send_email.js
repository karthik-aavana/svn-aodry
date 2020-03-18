$(document).ready(function () {
    $("#email_submit").click(function (event) {
        var email_template = $('#email_template').val();
        var from_email = $('#from_email').val();
        var to_email = $('#to_email').val();
        var subject = $('#subject').val();
        var message = $('#compose-textarea').val();
        if (email_template == null || email_template == "") {
            $("#err_email_template").text("Please Select Template.");
            $('#email_template').focus();
            return false;
        } else {
            $("#err_email_template").text("");
        }
        if (from_email == null || from_email == "") {
            $("#err_from_email").text("Please Select From Email-id.");
            $('#from_email').focus();
            return false;
        } else {
            $("#err_from_email").text("");
        }
        if (to_email == null || to_email == "") {
            $("#err_to_email").text("Please Enter To Email-id.");
            $('#to_email').focus();
            return false;
        } else {
            $("#err_to_email").text("");
        }
        if (subject == null || subject == "") {
            $("#err_subject").text("Please Enter Subject");
            $('#subject').focus();
            return false;
        } else {
            $("#err_subject").text("");
        }
        console.log(message);
        if (message == null || message == "") {
            $("#err_message").text("Please Enter Any Message.");
            $('#message').focus();
            return false;
        } else {
            $("#err_message").text("");
        }
    });
 $("#email_template").change(function (event) {
    var id = $("#email_template").val();
    templateAppend(id);
 });

});
