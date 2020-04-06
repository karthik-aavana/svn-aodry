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
                <li><a href="<?php echo base_url('brand'); ?>">Company</a></li>
                <li class="active">Add Company </li>
            </ol>
        </h5>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Add Company
                        </h3>
                    </div>
                    <form role="form" id="form" method="post" action="<?php echo base_url('brand/add_brand'); ?>">
                        <div class="box-body">
                            <div class="row"> 
                                <div class="col-md-12">
                                    <div class="form-group col-md-12">
                                        <label for="module_name">Company Name
                                            <span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="brand_name" name="brand_name" value="<?php echo set_value('brand_name'); ?>">
                                        <span class="validation-color" id="err_module_name"><?php echo form_error('module_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group col-sm-4">
                                        <label for="invoice_first_prefix">Invoice First Prefix<span class="validation-color">*</span></label>
                                        <input type="text" class="form-control" id="invoice_first_prefix" name="invoice_first_prefix" value="<?php echo set_value('invoice_first_prefix'); ?>">
                                        <span class="validation-color" id="err_invoice_first_prefix"><?php echo form_error('invoice_first_prefix'); ?></span>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="invoice_last_prefix">Invoice Last Prefix<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="invoice_last_prefix" name="invoice_last_prefix">
                                            <option value="">Select</option>
                                            <option value="number">Number</option>
                                            <option value="month_with_number">Month With Number</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_last_prefix"><?php echo form_error('invoice_last_prefix'); ?></span>
                                    </div>
                                    <!-- <div class="form-group col-sm-4">
                                        <label for="invoice_seperation">Invoice Separation<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="invoice_seperation" name="invoice_seperation">
                                            <option value="">Select</option>
                                            <option value="-">-</option>
                                            <option value="/">/</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_seperation"><?php echo form_error('invoice_seperation'); ?></span>
                                    </div> -->
                                    <div class="form-group col-sm-4">
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
                                <div class="col-sm-12">
                                    <div class="form-group col-sm-4">
                                        <label for="invoice_creation">Invoice Creation<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="invoice_creation" name="invoice_creation">
                                            <option value="">Select</option>
                                            <option value="automatic">Automatic</option>
                                            <option value="manual">Manual</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_creation"><?php echo form_error('invoice_creation'); ?></span>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="invoice_readonly">Invoice Readonly<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="invoice_readonly" name="invoice_readonly">
                                            <option value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <span class="validation-color" id="err_invoice_readonly"><?php echo form_error('invoice_readonly'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="box-footer">
                                        <button type="submit" id="brand_submit" class="btn btn-info">Add</button>
                                        <span class="btn btn-default" id="cancel" onclick="cancel('brand')">Cancel</span>
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
<script src="<?php echo base_url('assets/js/brand/') ?>brand.js"></script>