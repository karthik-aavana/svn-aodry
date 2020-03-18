

<div id="template_modal" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4><!-- <?php echo $this->lang->line('product_hsn_sac_lookup'); ?> -->

                    Add Template

                </h4>

            </div>

            <div class="modal-body">

                <div class="control-group">

                    <div class="controls">

                        <div class="tabbable">

                            <div class="box-body">

                                <div class="row">

                                    <form id="templateForm">

                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <label for="hash_tag">Hash Tag (#)<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" id="hash_tag" placeholder="#tag" name="hash_tag" value="<?php echo set_value('hash_tag'); ?>">

                                                <span class="validation-color" id="err_hash_tag"><?php echo form_error('title'); ?></span>

                                                <span style="color: green;" id="msg_hash_tag"><?php echo form_error('title'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="title">Title<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" placeholder="Title" id="title" name="title" value="<?php echo set_value('title'); ?>">

                                                <span class="validation-color" id="err_title"><?php echo form_error('title'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="content">Content<span style="color:red;">*</span></label>

                                                <textarea class="form-control" id="content" name="content"></textarea>

                                                <span class="validation-color" id="err_content"><?php echo form_error('content'); ?></span>

                                            </div>

                                        </div>



                                        <div class="col-sm-12">

                                            <button type="submit" id="template_modal_submit" class="btn btn-info pull-right" class="close" data-dismiss="modal"><!-- Add -->

                                                <?php echo $this->lang->line('add_user_btn'); ?>

                                            </button>

                                        </div>

                                    </form>

                                </div>

                                <!-- /.box-body -->

                            </div>



                        </div>

                    </div>

                </div> <!-- /controls -->

            </div> <!-- /control-group -->

        </div>

    </div>

</div>



<script>

    $(document).ready(function ()

    {

        $('#hash_tag').autoComplete({

            minChars: 1,

            cache: false,

            source: function (term, suggest) {

                $.ajax({

                    url: base_url + "template/get_template_by_tag/",

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



        $("#template_modal_submit").click(function (event) {

            // var name_regex = /^[a-zA-Z\s]+$/;

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



            $.ajax({

                url: base_url + "template/addTemplate/",

                type: "POST",

                dataType: "json",

                data: {hash_tag: $("#hash_tag").val(), title: $("#title").val(), content: $("#content").val()},

                success: function (data) {
                    alert_d.text ='Template Added Successfully.';
                    PNotify.success(alert_d); 
                    /*alert('Template Added Successfully.');*/

                }

            });

        });

    });

</script>

