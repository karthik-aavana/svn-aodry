<style type="text/css">
	.modal-lg {
		width: 90%; /* New width for large modal */
	}
</style>
<div id="supplier_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4><!-- <?php echo $this->lang->line('product_hsn_sac_lookup'); ?> -->
                    Add Supplier
                </h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">                               
                                    <form id="supplierForm">
                                        <div class="row">
                                            <input type="hidden" class="form-control" id="supplier_area" name="supplier_area" value="expense">
                                            <div class="form-group col-md-4">
                                                <label for="supplier_code">
                                                    Supplier Code
                                                </label>
                                                <input type="text" class="form-control" id="supplier_code" name="supplier_code" value="<?php echo set_value('supplier_code'); ?>">
                                                <span class="validation-color" id="err_supplier_code"><?php echo form_error('supplier_code'); ?></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="supplier_name">
                                                    Supplier Name<span class="validation-color">*</span>
                                                </label>
                                                <input type="hidden" name="ledger_id" id="supplier_ledger_id" value="0">

                                                <input type="text"  class="form-control" id="supplier_name" name="supplier_name">
                                                <span class="validation-color" id="err_supplier_name"><?php echo form_error('supplier_name'); ?></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="gstregtype">Gst Registration Type
                                                    <span class="validation-color">*</span>
                                                </label>
                                                <select class="form-control select2" id="gstregtype" name="gstregtype" style="width: 100%;">
                                                    <option value="">
                                                        Select
                                                    </option>
                                                    <option value="Registered">Registered</option>
                                                    <option value="Unregistered">Unregistered</option>
                                                </select>
                                                <span class="validation-color" id="err_gstin_type"><?php echo form_error('gstin_type'); ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4" id="gstn_selected">
                                                <label for="gstin">GSTIN<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" id="gstin" name="gstin" value="<?php echo set_value('gstin'); ?>">
                                                <span class="validation-color" id="err_gstin"><?php echo form_error('gstin'); ?></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="country">
                                                    <!-- Country -->
                                                    Country<span class="validation-color">*</span>
                                                </label>
                                                <select class="form-control select2" id="country" name="country" style="width: 100%;">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($country as $key)
                                                    {
                                                        ?>
                                                        <option value='<?php echo $key->country_id ?>' <?php
																																																								if($key->country_id==$branch[0]->branch_country_id) {
																																																								echo "selected";
																																																								}
                                                        ?>>
                                                                    <?php echo $key->country_name; ?>
                                                        </option>
                                                        <?php } ?>
                                                </select>
                                                <span class="validation-color" id="err_country"></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="state">
                                                    <!-- State -->
                                                    State <span class="validation-color">*</span>
                                                </label>
                                                <select class="form-control select2" id="state" name="state" style="width: 100%;">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($state as $key)
                                                    {
                                                        ?>
                                                        <option value='<?php echo $key->state_id ?>' <?php
																																																								if($key->state_id==$branch[0]->branch_state_id) {
																																																								echo "selected";
																																																								}
                                                        ?>>
                                                                    <?php echo $key->state_name; ?>
                                                        </option>
                                                        <?php } ?>
                                                </select>
                                                <span class="validation-color" id="err_state"></span>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="city">
                                                    <!-- City -->
                                                    City<span class="validation-color">*</span>
                                                </label>
                                                <select class="form-control select2" id="city" name="city" style="width: 100%;">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($city as $key)
                                                    {
                                                        ?>
                                                        <option value='<?php echo $key->city_id ?>' <?php
																																																								if($key->city_id==$branch[0]->branch_city_id) {
																																																								echo "selected";
																																																								}
                                                        ?>>
                                                                    <?php echo $key->city_name; ?>
                                                        </option>
                                                        <?php } ?>
                                                </select>
                                                <span class="validation-color" id="err_city"></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="address1">
                                                    Address<span class="validation-color">*</span>
                                                </label>
                                                <textarea class="form-control" id="address" rows="2" name="address"><?php echo set_value('address'); ?></textarea>
                                                <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="postal_code"><!-- Postal Code -->
                                                    Postal Code</label>
                                                <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo set_value('postal_code'); ?>">
                                                <span class="validation-color" id="err_postal_code"><?php echo form_error('postal_code'); ?></span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="email"><!-- email -->
                                                    Email
                                                </label>
                                                <input type="text" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>">
                                                <span class="validation-color" id="err_email"><?php echo form_error('email'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="mobile"><!-- Mobile -->
                                                    Mobile
                                                </label>

                                                <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo set_value('mobile'); ?>">
                                                <span class="validation-color" id="err_mobile"><?php echo form_error('mobile'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="pan_no">PAN No

                                                    <span class="validation-color"></span>
                                                </label>
                                                <input type="text" class="form-control" id="panno" name="panno" value="<?php echo set_value('panno'); ?>">
                                                <span class="validation-color" id="err_panno"><?php echo form_error('panno'); ?></span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="tanno">TAN<span class="validation-color"></span>
                                                </label>
                                                <input type="text" class="form-control" id="tanno" name="tanno" value="<?php echo set_value('tanno'); ?>">
                                                <span class="validation-color" id="err_tan"><?php echo form_error('tanno'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="address1">
                                                    Contact Person Name<span class="validation-color"></span>
                                                </label>
                                                <input type="text" class="form-control" id="contact_person_name" name="contact_person_name" value="<?php echo set_value('contact_person_name'); ?>">
                                                <span class="validation-color" id="err_contact_person_name"><?php echo form_error('contact_person_name'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="address1">
                                                    Contact Person Department
                                                </label>
                                                <input type="text" class="form-control" id="contact_person_department" name="contact_person_department" value="<?php echo set_value('contact_person_department'); ?>">
                                                <span class="validation-color" id="err_contact_person_department"><?php echo form_error('contact_person_department'); ?></span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="address1">
                                                    Contact Person Email
                                                </label>
                                                <input type="text" class="form-control" id="contact_person_email" name="contact_person_email" value="<?php echo set_value('contact_person_email'); ?>">
                                                <span class="validation-color" id="err_contact_person_email"><?php echo form_error('contact_person_email'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="address1">
                                                    Contact Person Mobile
                                                </label>
                                                <input type="text" class="form-control" id="contact_person_mobile" name="contact_person_mobile" value="<?php echo set_value('contact_person_mobile'); ?>">
                                                <span class="validation-color" id="err_contact_person_mobile"><?php echo form_error('contact_person_mobile'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4" style="display: none;">
                                                <label for="state_code">State Code</label>
                                                <input type="text" class="form-control" id="state_code" name="state_code" value="<?= $branch[0]->branch_state_code ?>" readonly>
                                                <span class="validation-color" id="err_state_code"></span>
                                            </div>
                                           <!--  <input type="hidden"  class="form-control" id="supplier_area" name="supplier_area" value="purchase" > -->
                                        </div>

                                       
                                            <div class="box-footer">
                                                <button type="submit" id="supplier_modal_submit" class="btn btn-info" class="close"  data-dismiss="modal">Add</button>
                                                <button type="button" class="btn btn-info" class="close"  data-dismiss="modal">Cancel</button>
                                            </div>
                                    </form>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                </div> <!-- /controls -->
            </div> <!-- /control-group -->
        </div>
    </div>
</div>
<script type="text/javascript">var base_url =     "<?php echo base_url(); ?>";</script>


<script>
	var supplier_ajax = "yes";

</script>

<script src="<?php echo base_url('assets/js/supplier/') ?>supplier.js"></script>



