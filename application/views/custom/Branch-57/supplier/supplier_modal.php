<div id="supplier_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>
                    Add Supplier
                </h4>
            </div>
            <form id="supplierForm">
                <div class="modal-body">
                    <div id="loader_coco" style="display: none">
                        <h1 class="ml8"><span class="letters-container"> <span class="letters letters-left"><img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px"></span></span><span class="circle circle-white"></span><span class="circle circle-dark"></span><span class="circle circle-container"><span class="circle circle-dark-dashed"></span></span></h1>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="supplier_code">Supplier Code</label>
                            <input type="text" class="form-control" id="reference_number" style="display: none;" name="reference_number" value="<?php echo set_value('reference_number'); ?>" readonly>
                            <input type="text" class="form-control" id="reference_type" style="display: none;" name="reference_type" value="<?php echo set_value('reference_type'); ?>" readonly>
                            <input type="text" class="form-control" id="supplier_code" name="supplier_code" value="<?php echo set_value('supplier_code'); ?>"> <span class="validation-color" id="err_supplier_code"><?php echo form_error('supplier_code'); ?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="supplier_type">Supplier Type<span class="validation-color">*</span>
                            </label>
                            <select class="form-control select2" id="supplier_type" name="supplier_type">  
                                <option value="">Select Type</option>                             
                                <option value="individual">Firm</option>
                                <option value="company">Company</option>
                                <option value="private limited company">Private Limited Company</option>
                                <option value="proprietorship">Proprietorship</option>
                                <option value="partnership">Partnership</option>
                                <option value="one person company">One Person Company</option>
                                <option value="limited liability partnership">Limited Liability Partnership</option>
                            </select> <span class="validation-color" id="err_supplier_type"><?php echo form_error('supplier_type'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="supplier_name">Company/Firm Name<span class="validation-color">*</span></label>
                            <input type="hidden" name="ledger_id" id="supplier_ledger_id" value="0">
                            <input type="hidden" name="supplier_id" id="supplier_id" value="0">
                            <input type="hidden" name="contact_person_telephone" id="contact_person_telephone" value="">
                            <input type="hidden" name="contact_person_postal_code" id="contact_person_postal_code" value="">
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name" maxlength="100" >
                            <input type="hidden" name="vendor_name_used">
                            <span class="validation-color" id="err_supplier_name"><?php echo form_error('supplier_name'); ?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="gst">GST Number</label>
                            <input type="text" class="form-control" id="gst_number" name="gst_number" maxlength="15"> <span class="validation-color" id="err_gstin"><?php echo form_error('gstin'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="cmb_country">Country<span class="validation-color">*</span>
                            </label>
                            <select class="form-control select2" id="cmb_country" name="cmb_country">
                                <option value="">Select Country</option>
                                <?php foreach ($country as $key) { ?>
                                    <option value='<?php echo $key->country_id ?>'>
                                        <?php echo $key->country_name; ?></option>
                                <?php } ?>
                            </select> <span class="validation-color" id="err_country"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cmb_state">State <span class="validation-color">*</span>
                            </label>
                            <select class="form-control select2" id="cmb_state" name="cmb_state">
                                <option value="">Select State</option>
                            </select> <span class="validation-color" id="err_state"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="cmb_city">City<span class="validation-color">*</span>
                            </label>
                            <select class="form-control select2" id="cmb_city" name="cmb_city">
                                <option value="">Select City</option>
                            </select> <span class="validation-color" id="err_city"></span>
                        </div>
                        <div class="form-group col-md-6">
                                    <label for="pin_code">PIN Code</label>
                                    <input class="form-control" type="number" name="txt_pin_code" id="txt_pin_code" maxlength="12" />
                                    <span class="validation-color" id="err_pin_code"><?php echo form_error('pin_number'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                                <label for="pan_number">PAN Number</label>
                                <input class="form-control" type="text" name="txt_pan_number" id="txt_pan_number" maxlength="20" />
                                <span class="validation-color" id="err_pan_number"><?php echo form_error('tan_number'); ?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tan_number">TAN Number</label>
                            <input class="form-control" type="text" name="txt_tan_number" id="txt_tan_number" maxlength="20" />
                            <span class="validation-color" id="err_tan_number"><?php echo form_error('tan_number'); ?></span>
                        </div>
                    </div>
                    <div class= "row">
                        <div class="form-group col-md-6">
                            <label for="contact_person">Contact Person Name</label>
                            <input class="form-control" type="text" name="txt_contact_person" id="txt_contact_person" maxlength="50" /> <span class="validation-color" id="err_contact_person"><?php echo form_error('contact_person'); ?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="contact_number">Contact Number</label>
                            <input class="form-control" type="number" name="txt_contact_number" id="txt_contact_number" maxlength="16" />
                            <span class="validation-color" id="err_contact_number"><?php echo form_error('contact_number'); ?></span>
                        </div>
                    </div>
                    <div class = "row">
                        <div class="form-group col-md-6">
                            <label for="email_address">Email<span class="validation-color"></span></label>
                            <input class="form-control" type="text" name="email_address" id="email_address" maxlength="50" />
                            <span class="validation-color" id="err_email_address"><?php echo form_error('email_address'); ?></span>
                        </div>
                        <div class="form-group col-md-6">
                                    <label for="payment_days">Payment Days</label>
                                    <input class="form-control" type="number" name="payment_days" id="payment_days" min="0" max="365" />
                                    <span class="validation-color" id="err_payment_days"><?php echo form_error('payment_days'); ?></span>
                                </div>
                    </div>
                    <div class = "row">
                        <div class="form-group col-md-6">
                            <label for="department">Department</label>
                            <input type="text" class="form-control" id="department" name="department" >
                            <span class="validation-color" id="err_department"><?php echo form_error('department'); ?></span>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="address">Address<span class="validation-color">*</span>
                            </label>
                            <textarea class="form-control" id="address" rows="2" name="address" maxlength="1000"></textarea> <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="supplier_submit" data-dismiss="modal">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var supplier_ajax = "yes";
    var supplier_add = "yes";
</script>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script src="<?php echo base_url('assets/custom/branch-'.$this->session->userdata('SESS_BRANCH_ID').'/js/supplier/') ?>supplier.js"></script>