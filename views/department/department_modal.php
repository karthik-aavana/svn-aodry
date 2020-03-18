
<div id="department_modal" class="modal fade" role="dialog" data-backdrop="static" >
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content" style="width: 75%">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4>Add Department </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="department_name_a">Department Code <span class="validation-color">*</span></label>
                            <input type="text" class="form-control" id="department_code_a" name="department_code_a" maxlength="50" value="<?php echo $invoice_number; ?>" <?php if ($access_settings[0]->invoice_readonly == 'yes') { echo "readonly"; }?>>
                            <span class="validation-color" id="err_department_code_a"></span>
                        </div>
                    </div> -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="department_name_a">Department Name <span class="validation-color">*</span></label>
                            <input type="text" class="form-control" id="department_name_a" name="department_name_a" maxlength="50" value="<?php echo set_value('department_name_a'); ?>">
                            <span class="validation-color" id="err_department_name_a"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="department_add_submit" class="btn btn-info">
                    Add
                </button>
                <button type="button" class="btn btn-info" class="close" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	var department_ajax = "yes";
</script>
<script src="<?php echo base_url('assets/js/') ?>department.js"></script>
