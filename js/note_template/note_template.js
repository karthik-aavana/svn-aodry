$(document).ready(function ()
{
    $('#hash_tag').autoComplete({
        minChars: 1,
        cache: false,
        source: function (term, suggest) {
            $.ajax({
                url: base_url + "note_template/get_note_template_by_tag/",
                type: "POST",
                dataType: "json",
                data: {hash_tag: '#' + $("#hash_tag").val()},
                success: function (data) {
                    if (data == 'fail')
                    {
                        $("#err_hash_tag").text("Hash tag already exist.");
                    }
                }
            });
            str1 = term.substring(term.length - 1, term.length);
            if (str1 == '#')
            {
                $("#err_hash_tag").text("Invalid character #.");
                $("#msg_hash_tag").text('');
                $('#hash_tag').val(term.substring(0, term.length - 1));
            } else
            {
                $("#err_hash_tag").text("");
                $("#msg_hash_tag").text('#' + term);
            }
        }
    });
    
    $("#hash_tag").on("keyup", function (event) {
        var hash_tag = $('#hash_tag').val();
        if(hash_tag.match(' ')){           
            var newStr = hash_tag.slice(0, -1);         
            $('#hash_tag').val(newStr);
            alert_d.text ="Please Don't Enter Space in Hash tag";
            PNotify.error(alert_d); 
            /*alert("Please Don't Enter Space in Hash tag");*/
            return false;
        }
    });
    $("#template_submit").click(function (event) {
        var tag_regex = /^[#]+$/;
        var hash_tag = $('#hash_tag').val();
        var title = $('#title').val();
        var content = $('#content').val();
        if (hash_tag == null || hash_tag == "")
        {
            $("#err_hash_tag").text("Please Enter Hash tag");
            $('#msg_hash_tag').text("");
            return false;
        } else if ($('#err_hash_tag').text() == 'Please Enter Hash tag')
        {
            $('#err_hash_tag').text("");
        }
        if (title == null || title == "")
        {
            $("#err_title").text("Please Enter Title");
            return false;
        } else
        {
            $('#err_title').text("");
        }
        if (content == null || content == "")
        {
            $("#err_content").text("Please Enter Content");
            return false;
        } else
        {
            $("#err_content").text("");
        }
        if ($('#err_hash_tag').text() != '')
        {
            return false;
        }
    });
});
