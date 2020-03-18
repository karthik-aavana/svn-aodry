<div id="email_setup_modal" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4><!-- <?php echo $this->lang->line('product_hsn_sac_lookup'); ?> -->

                    Add Email Details

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

                                                <label for="email_protocol">Email Protocol<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" id="email_protocol" placeholder="Ex. (SMTP, POP, IMAP)" name="email_protocol" value="<?php echo set_value('email_protocol'); ?>">

                                                <span class="validation-color" id="err_email_protocol"><?php echo form_error('email_protocol'); ?></span>

                                                <!-- <span style="color: green;" id="msg_hash_tag"><?php echo form_error('title'); ?></span> -->

                                            </div>

                                            <div class="form-group">

                                                <label for="smtp_host">SMTP Host<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" placeholder="Ex. (smtp.gmail.com, smtp.googlemail.com)" id="smtp_host" name="smtp_host" value="<?php echo set_value('smtp_host'); ?>">

                                                <span class="validation-color" id="err_smtp_host"><?php echo form_error('smtp_host'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="smtp_port">SMTP Port<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" placeholder="Ex. (587, 465)" id="smtp_port" name="smtp_port" value="<?php echo set_value('smtp_port'); ?>">

                                                <span class="validation-color" id="err_smtp_port"><?php echo form_error('smtp_port'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="smtp_secure">SMTP Secure<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" placeholder="Ex. (tls, ssl)" id="smtp_secure" name="smtp_secure" value="<?php echo set_value('smtp_secure'); ?>">

                                                <span class="validation-color" id="err_smtp_secure"><?php echo form_error('smtp_secure'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="smtp_user_name">SMTP User Name<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" placeholder="SMTP User Name" id="smtp_user_name" name="smtp_user_name" value="<?php echo set_value('smtp_user_name'); ?>">

                                                <span class="validation-color" id="err_smtp_user_name"><?php echo form_error('smtp_user_name'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="smtp_password">SMTP Password<span style="color:red;">*</span></label>

                                                <input type="password" class="form-control" placeholder="SMTP Password" id="smtp_password" name="smtp_password" value="<?php echo set_value('smtp_password'); ?>">

                                                <span class="validation-color" id="err_smtp_password"><?php echo form_error('smtp_password'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="from_name">From Name<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" placeholder="From Name" id="from_name" name="from_name" value="<?php echo set_value('from_name'); ?>">

                                                <span class="validation-color" id="err_from_name"><?php echo form_error('from_name'); ?></span>

                                            </div>

                                            <div class="form-group">

                                                <label for="reply_mail">Reply Mail<span style="color:red;">*</span></label>

                                                <input type="text" class="form-control" placeholder="Reply Mail" id="reply_mail" name="reply_mail" value="<?php echo set_value('reply_mail'); ?>">

                                                <span class="validation-color" id="err_reply_mail"><?php echo form_error('reply_mail'); ?></span>

                                            </div>



                                        </div>



                                        <div class="col-sm-12">

                                            <button type="submit" id="email_modal_submit" class="btn btn-info pull-right" class="close" data-dismiss="modal"> Add 

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

        $("#email_modal_submit").click(function (event) {

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



            $.ajax({

                url: base_url + "email_setup/add_email_setup_modal/",

                type: "POST",

                dataType: "json",

                data: {email_protocol: $("#email_protocol").val(), smtp_host: $("#smtp_host").val(), smtp_port: $("#smtp_port").val(), smtp_secure: $("#smtp_secure").val(), smtp_user_name: $("#smtp_user_name").val(), smtp_password: $("#smtp_password").val(), from_name: $("#from_name").val(), reply_mail: $("#reply_mail").val()},

                success: function (data) {

                    $('#templateForm')[0].reset();

                    $('#from_email').html(data);

                }

            });

        });

    });

</script>

