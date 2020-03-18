$(document).ready(function ()
{
    var err_title = "Please Enter Title";
    var err_type = "Please Select Type";
    var err_usersSelect = "Please Select Users";
    var err_description = "Please Enter Description";
    $("#news_updates_submit").click(function (event)
    {
        var title = $('#title').val();
        var type = $('#type').val();
        var usersSelect = $('#usersSelect').val();
        var description = $('#description').val();

        if (title == null || title == "")
        {
            $("#err_title").text(err_title);
            return !1
        } else
        {
            $("#err_title").text("")
        }

        if (type == null || type == "")
        {
            $("#err_type").text(err_type);
            return !1
        } else
        {
            $("#err_type").text("")
        }

        if (usersSelect == null || usersSelect == "")
        {
            $("#err_usersSelect").text(err_usersSelect);
            return !1
        } else
        {
            $("#err_usersSelect").text("")
        }

        if (description == null || description == "")
        {
            $("#err_description").text(err_description);
            return !1
        } else
        {
            $("#err_description").text("")
        }
    });

    $("#title").on("blur", function (event)
    {
        var title = $('#title').val();
        if (title.length > 0)
        {
            if (title == null || title == "")
            {
                $("#err_title").text(err_title);
                return !1
            } else
            {
                $("#err_title").text("")
            }
            if (!title.match(general_regex))
            {
                $('#err_title').text("Please Enter valid Title");
                return !1
            } else
            {
                $("#err_title").text("")
            }
        }
    });



});
