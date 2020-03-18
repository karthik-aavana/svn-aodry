<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li><a href="<?php echo base_url('sales'); ?>">Sales</a></li>
                <li class="active">Sales Email</li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <?php
            $sales_id = $this->encryption_url->encode($data[0]->sales_id);
            ?>
            <form role="form" id="form" method="post" action="<?php echo base_url('email_template/send_email/') . $sales_id; ?>" encType="multipart/form-data">
                <!-- right column -->
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Sales Email</h3>
                            <span class="btn btn-default pull-right" id="sale_cancel" onclick="cancel('sales')">Back</span>
                        </div>
                        <div class="box-body">
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-sm-12" style="margin-left: 15%;">
                                        <?php
                                        if (isset($data[0]->customer_name))
                                        {
                                            ?>
                                            <div class="col-sm-8">
                                                <div class="col-sm-2">
                                                    <h4>Customer :</h4>
                                                </div>
                                                <div class="col-sm-10" >
                                                    <h4 style="text-transform: capitalize;"><?= $data[0]->customer_name ?></h4>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <div class="col-sm-8">
                                            <div class="col-sm-2">
                                                <h4>Company :</h4>
                                            </div>
                                            <div class="col-sm-10" >
                                                <h4 style="text-transform: capitalize;"><?= $branch[0]->firm_name ?></h4>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <br><br>
                                            <label class="col-sm-3">Select Template<span class="validation-color">*</span></label>
                                            <div class="form-group col-sm-9">
                                                <input type="hidden" name="redirect" value="sales">
                                                <select class="form-control select2" id="email_template" name="email_template">
                                                    <option value="">Select</option>
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
                                        <div class="col-sm-8">
                                            <br>
                                            <label class="col-sm-3"><br>From<span class="validation-color">*</span></label>
                                            <div class="form-group col-sm-9">
                                                <a href="" data-toggle="modal" data-target="#email_setup_modal" class="pull-right">+ Add Email</a>
                                                <select class="form-control select2" id="from_email" name="from_email">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($email_setup as $value)
                                                    {
                                                        ?>
                                                        <option value="<?php echo $value->email_setup_id; ?>"><?php echo $value->smtp_username; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="validation-color" id="err_from_email"><?php echo form_error('from_email'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <br>
                                            <label class="col-sm-3">To<span class="validation-color">*</span></label>
                                            <div class="col-sm-9" >
                                                <input type="text" class="form-control" id="to_email" name="to_email" value="<?= $data[0]->customer_email ?>" data-role="tagsinput"><br>
                                                <span class="validation-color" id="err_to_email"><?php echo form_error('to_email'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <br>
                                            <label class="col-sm-3">CC</label>
                                            <div class="col-sm-9" >
                                                <input type="text" class="form-control" id="cc_email" name="cc_email" data-role="tagsinput"><br>
                                                <span class="validation-color" id="err_cc_email"><?php echo form_error('cc_email'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" style="margin-left: 15%;">
                                        <br/><br/>
                                        <div class="col-sm-8">
                                            <label class="col-sm-3">Subject<span class="validation-color">*</span></label>
                                            <div class="col-sm-9" >
                                                <input type="text" class="form-control" id="subject" name="subject">
                                                <span class="validation-color" id="err_subject"><?php echo form_error('subject'); ?></span>
                                            </div>
                                        </div>
                                        <br/><br/><br/>
                                        <div class="col-sm-8">
                                            <label class="col-sm-3">Message<span class="validation-color">*</span></label>
                                            <div class="col-sm-9" >
                                                <textarea class="form-control" id="compose-textarea" rows="12" name="message"></textarea>
                                                <span class="validation-color" id="err_message"><?php echo form_error('message'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <br/><br/>
                                            <label class="col-sm-3">Attachments<span class="validation-color">*</span></label>
                                            <div class="col-sm-9" >
                                                <input type="hidden" name="pdf_file_path" value="<?= $pdf_file_path ?>">
                                                <a href="<?php echo base_url($pdf_file_path); ?>" target="_blank"><?= $pdf_file_name ?></a>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <br/><br/>
                                            <label class="col-sm-3">Add More Attachments</label>
                                            <div class="col-sm-9" >
                                                <input type="file" class="form-control" id="attachments" name="attachments">
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <br/><br/>
                                            <label class="col-sm-3"></label>
                                            <div class="col-sm-9">
                                                <button type="submit" id="email_submit" name="sales_submit" class="btn btn-info pull-right">Send</button>
                                                <span class="btn btn-default pull-right" id="quotation_cancel" style="margin-right: 2%" onclick="cancel('sales')">Cancel</span>
                                            </div>
                                        </div>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
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
