<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?php echo base_url('email_setup'); ?>">Email Setup</a></li>
                <li class="active">Add Email Setup</li>
            </ol>
        </h5>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- right column -->
            <?php
            if ($fail = $this->session->flashdata('fail'))
            {
                ?>
                <div class="col-sm-12">
                    <div class="alert alert-success">
                        <button class="close" data-dismiss="alert" type="button">×</button>
                        <?php echo $fail; ?>
                        <div class="alerts-con"></div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Email Setup</h3>
                        <a class="btn btn-default pull-right" id="cancel" onclick="cancel('email_setup')">Back</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form" method="post" action="<?php echo base_url('email_setup/add_email_setup'); ?>">
                                <div class="well">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email_protocol">Email Protocol<span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" id="email_protocol" placeholder="Ex. (SMTP, POP, IMAP)" name="email_protocol" value="<?php echo set_value('email_protocol'); ?>">
                                            <span class="validation-color" id="err_email_protocol"><?php echo form_error('email_protocol'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="smtp_host">SMTP Host<span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" placeholder="Ex. (smtp@gmail.com, smtp@googlemail.com)" id="smtp_host" name="smtp_host" value="<?php echo set_value('smtp_host'); ?>">
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
                                    </div>
                                    <div class="col-md-6">
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
                                        <div class="box-footer">
                                            <button type="submit" id="email_setup_submit" name="email_setup_submit" class="btn btn-info btn-flat pull-right">  Add  </button>
                                            <span class="btn btn-default btn-flat pull-right" id="cancel" style="margin-right: 2%" onclick="cancel('email_setup')">Cancel</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>
<script src="<?php echo base_url('assets/js/email/') ?>email_setup.js"></script>
