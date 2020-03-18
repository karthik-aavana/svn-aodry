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
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                        Dashboard</a>
                </li>
                <li><a href="<?php echo base_url('module_settings'); ?>">
                        Module Settings
                    </a></li>
                <li class="active">
                    Add Module Settings
                </li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Add Module Settings
                        </h3>
                    </div>

                    <form role="form" id="form" method="post" action="<?php echo base_url('module_settings/add_module_settings'); ?>">
                        <div class="box-body">


                            <div class="row"> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="module_name">Module Name
                                            <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="module_name" name="module_name">
                                            <option value="">Select</option>
                                            <?php
                                            foreach ($module_id as $key => $value) {
                                                echo "<option value='" . $value->module_id . "'>" . $value->module_name . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <span class="validation-color" id="err_module_name"><?php echo form_error('module_name'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_first_prefix">Invoice First Prefix<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="invoice_first_prefix" name="invoice_first_prefix" value="<?php echo set_value('invoice_first_prefix'); ?>">
                                        <span class="validation-color" id="err_invoice_first_prefix"><?php echo form_error('invoice_first_prefix'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_last_prefix">Invoice Last Prefix<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="invoice_last_prefix" name="invoice_last_prefix">
                                            <option value="">Select</option>
                                            <option value="number">Number</option>
                                            <option value="month_with_number">Month With Number</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_last_prefix"><?php echo form_error('invoice_last_prefix'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_seperation">Invoice Separation<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="invoice_seperation" name="invoice_seperation">
                                            <option value="">Select</option>
                                            <option value="-">-</option>
                                            <option value="/">/</option>
                                            <option value="no">No Separation</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_seperation"><?php echo form_error('invoice_seperation'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_type">Invoice Type<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="invoice_type" name="invoice_type">
                                            <option value="">Select</option>
                                            <option value="regular">Regular</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_type"><?php echo form_error('invoice_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="invoice_creation">Invoice Creation<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="invoice_creation" name="invoice_creation">
                                            <option value="">Select</option>
                                            <option value="automatic">Automatic</option>
                                            <option value="manual">Manual</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_creation"><?php echo form_error('invoice_creation'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_readonly">Invoice Readonly<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="invoice_readonly" name="invoice_readonly">
                                            <option value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_readonly"><?php echo form_error('invoice_readonly'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="item_access">Item Access<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="item_access" name="item_access">
                                            <option value="">Select</option>
                                            <option value="product">Goods</option>
                                            <option value="service">Service</option>
                                            <option value="both">Both</option>
                                        </select>
                                        <span class="validation-color" id="err_item_access"><?php echo form_error('item_access'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="note_split">Note Split<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="note_split" name="note_split">
                                            <option value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <span class="validation-color" id="err_note_split"><?php echo form_error('note_split'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="note_split">Display Tax</label><br>
                                        <label>
                                            <input type="checkbox" id="gst" name="gst" value="yes">
                                            GST </label>&nbsp;&nbsp;&nbsp; <label>
                                            <input type="checkbox" id="tcs" name="tcs" value="yes">
                                            TCS </label>&nbsp;&nbsp;&nbsp;  <label>
                                            <input type="checkbox" id="tds" name="tds" value="yes">
                                            TDS </label>
                                        <span class="validation-color" id="err_note_split"><?php echo form_error('note_split'); ?></span>
                                    </div>
                                    <div class="form-group" id="reference_first_prefix_div" style="display: none;">
                                        <label for="reference_first_prefix">Reference First Prefix<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="reference_first_prefix" name="reference_first_prefix" value="<?php echo set_value('reference_first_prefix'); ?>">
                                        <span class="validation-color" id="err_reference_first_prefix"><?php echo form_error('reference_first_prefix'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="module_settings_submit" class="btn btn-info">Add</button>
                                        <button class="btn btn-default" id="cancel" onclick="cancel('module_settings')">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$this->load->view('layout/footer');
?>
<script src="<?php echo base_url('assets/js/module_settings/') ?>module_settings.js"></script>
