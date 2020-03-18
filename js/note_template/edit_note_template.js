$(document).ready(function ()
{
    $('#edit_hash_tag').autoComplete({
        minChars: 1,
        cache: false,
        source: function (term, suggest) {
            $.ajax({
                url: base_url + "note_template/get_note_template_by_tag/",
                type: "POST",
                dataType: "json",
                data: {edit_hash_tag: '#' + $("#edit_hash_tag").val()},
                success: function (data) {
                    if (data == 'fail')
                    {
                        $("#err_edit_hash_tag").text("Hash tag already exist.");
                    }
                }
            });
            str1 = term.substring(term.length - 1, term.length);
            if (str1 == '#')
            {
                $("#err_edit_hash_tag").text("Invalid character #.");
                $("#edit_msg_hash_tag").text('');
                $('#edit_hash_tag').val(term.substring(0, term.length - 1));
            } else
            {
                $("#err_edit_hash_tag").text("");
                $("#edit_msg_hash_tag").text('#' + term);
            }
        }
    });
    
    $("#edit_hash_tag").on("keyup", function (event) {
        var hash_tag = $('#edit_hash_tag').val();
        if(hash_tag.match(' ')){           
            var newStr = hash_tag.slice(0, -1);         
            $('#edit_hash_tag').val(newStr);
            alert_d.text ="Please Don't Enter Space in Hash tag";
            PNotify.error(alert_d); 
            /*alert("Please Don't Enter Space in Hash tag");*/
            return false;
        }
    });
    $("#template_submit").click(function (event) {
        var tag_regex = /^[#]+$/;
        var hash_tag = $('#edit_hash_tag').val();
        var title = $('#edit_title').val();
        var content = $('#edit_content').val();
        if (hash_tag == null || hash_tag == "")
        {
            $("#err_edit_hash_tag").text("Please Enter Hash tag");
            $('#edit_msg_hash_tag').text("");
            return false;
        } else if ($('#err_edit_hash_tag').text() == 'Please Enter Hash tag')
        {
            $('#err_edit_hash_tag').text("");
        }
        if (title == null || title == "")
        {
            $("#err_edit_title").text("Please Enter Title");
            return false;
        } else
        {
            $('#err_edit_title').text("");
        }
        if (content == null || content == "")
        {
            $("#err_edit_content").text("Please Enter Content");
            return false;
        } else
        {
            $("#err_edit_content").text("");
        }
        if ($('#err_edit_hash_tag').text() != '')
        {
            return false;
        }
        var data = $('#form_note').serialize();
        $('#loader_coco').show();
        $.ajax({
                url : base_url + 'note_template/update_note_template',
                dataType : 'JSON',
                method : 'POST',
                data : data,
                success : function(result) {
                    setTimeout(function () {// wait for 5 secs(2)
                            $(document).find('[name=check_item]').trigger('change');
                            //location.reload(); // then reload the page.(3)
                        },500);
                    if(result.flag){
                        $('#loader_coco').hide();
                        $('#edit_note_template_modal').modal('hide');
                        dTable.destroy();
                        dTable = getAllNoteTemplate();
                        alert_d.text = result.msg;
                        PNotify.success(alert_d);
                        $('#edit_note_template').prop('checked', false);
                    }else{
                        $('#edit_note_template_modal').modal('hide');
                        alert_d.text = result.msg;
                        PNotify.error(alert_d);
                        $('#edit_note_template').prop('checked', false);
                    }
                }
            });
    });
});
