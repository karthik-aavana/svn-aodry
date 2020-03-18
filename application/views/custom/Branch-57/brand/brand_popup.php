<?php
$branch_id = $this->session->userdata('SESS_BRANCH_ID');
?>
<div id="brand_popup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Brand</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <label for="brand_name">Brand Name
                                <span class="validation-color">*</span>
                            </label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" value="<?php echo set_value('brand_name'); ?>">
                            <input type="hidden" name="brand_id" id="brand_id">
                            <input type="hidden" name="exist" value="0">
                            <span class="validation-color" id="err_brand_name"><?php echo form_error('brand_name'); ?></span>
                        </div>
                    </div>
                    <!-- <div class="col-sm-12">
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
                        <div class="form-group col-sm-4">
                            <label for="invoice_seperation">Invoice Separation<span class="validation-color">*</span>
                            </label>
                            <select class="form-control select2" id="invoice_seperation" name="invoice_seperation">
                                <option value="">Select</option>
                                <option value="-">-</option>
                                <option value="/">/</option>
                            </select>
                            <span class="validation-color" id="err_invoice_seperation"><?php echo form_error('invoice_seperation'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
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
                            <label for="brand_invoice_readonly">Invoice Readonly<span class="validation-color">*</span></label>
                            <select class="form-control select2" id="brand_invoice_readonly" name="brand_invoice_readonly">
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <span class="validation-color" id="err_brand_invoice_readonly"><?php echo form_error('brand_invoice_readonly'); ?></span>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="brand_submit" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div> 
    </div>
</div>
<script src="<?php echo base_url('assets/custom/branch-'.$branch_id.'/js/brand/'); ?>brand.js"></script>