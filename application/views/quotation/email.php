<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li><a href="<?php echo base_url('quotation'); ?>">Quotation</a></li>
                <li class="active">Quotation Email</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <?php
            $quotation_id = $this->encryption_url->encode($data[0]->quotation_id);
            ?>
            <form role="form" id="form" method="post" action="<?php echo base_url('email_template/send_email/') . $quotation_id; ?>" encType="multipart/form-data">
                <!-- right column -->
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Quotation Email</h3>
                            <a class="btn btn-default pull-right" id="sale_cancel" onclick="cancel('quotation')">Back</a>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="box-header">
                                    <?php
                                    if (isset($data[0]->customer_name))
                                    {
                                        ?><h4 class="box-title">Customer : <?= $data[0]->customer_name ?></h4>

                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="box-header">
                                    <h4 class="box-title" style="margin-left: 0px;">Company : <?= $branch[0]->firm_name ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <a href="" data-toggle="modal" data-target="#email_setup_modal" class="pull-right">+</a>
                                            </div>
                                            <select class="form-control select2" id="from_email" name="from_email">
                                                <option value="">From</option>
                                                <?php
                                                foreach ($email_setup as $value)
                                                {
                                                    ?>
                                                    <option value="<?php echo $value->email_setup_id; ?>"><?php echo $value->smtp_username; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span class="validation-color" id="err_from_email"><?php echo form_error('from_email'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="hidden" name="redirect" value="quotation">
                                        <select class="form-control select2" id="email_template" name="email_template">
                                            <option value="">Select Template</option>
                                            <?php
                                            foreach ($email_template as $value)
                                            {
                                                ?>
                                                <option value="<?php echo $value->email_template_id; ?>"><?php echo $value->email_template_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_email_template"><?php echo form_error('email_template'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group" >
                                        <input type="text" class="form-control" id="to_email" name="to_email" value="<?= $data[0]->customer_email ?>" data-role="tagsinput" placeholder="To:"><br>
                                        <span class="validation-color" id="err_to_email"><?php echo form_error('to_email'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group" >
                                        <input type="text" class="form-control" id="cc_email" name="cc_email" data-role="tagsinput" placeholder="CC:"><br>
                                        <span class="validation-color" id="err_cc_email"><?php echo form_error('cc_email'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" >
                                <input type="text" class="form-control" id="subject" name="subject">
                                <span class="validation-color" id="err_subject"><?php echo form_error('subject'); ?></span>
                            </div>
                            <div class="form-group">
                                <textarea id="compose-textarea" class="form-control" id="message" rows="12" name="message" style="height: 300px">
                                </textarea>
                                <span class="validation-color" id="err_message"><?php echo form_error('message'); ?></span>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="hidden" name="pdf_file_path" value="<?= $pdf_file_path ?>">
                                    <a href="<?php echo base_url($pdf_file_path); ?>" target="_blank"><?= $pdf_file_name ?></a>
                                    <p class="help-block">Attachment</p>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="btn btn-default btn-file">
                                            <i class="fa fa-paperclip"></i> More Attachment
                                            <input type="file" class="form-control" id="attachments" name="attachments">
                                        </div>
                                        <p class="help-block">Max. 32MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <button type="submit" id="email_submit" name="quotation_submit" class="btn btn-info" style="margin-right: 20px;">Send</button>
                                    <button type="button" class="btn btn-default pull-right" id="quotation_cancel" onclick="cancel('quotation')">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
$this->load->view('email_setup/email_setup_modal');
?>
<script src="<?php echo base_url('assets/js/email/') ?>send_email.js"></script>
