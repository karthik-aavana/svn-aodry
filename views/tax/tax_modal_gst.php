<div id="tax_gst_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Add Tax</h4>
            </div>
            <form name="form" id="taxForm_gst" >
                <div class="modal-body">
                     <div id="loader">
                        <h1 class="ml8">
                            <span class="letters-container">
                                <span class="letters letters-left">
                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
             
                                </span>
                            </span>
                            <span class="circle circle-white"></span>
                            <span class="circle circle-dark"></span>
                            <span class="circle circle-container">
                                <span class="circle circle-dark-dashed"></span>
                            </span>
                        </h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="tax_name_gst">
                                    <!-- Tax Name --> Tax Type <span class="validation-color">*</span>
                                </label>
                                <select class="form-control" id="tax_name_gst" name="tax_name_gst" readonly>
                                        <option value="GST">GST</option>
                                    </select>  
                                <input type="hidden" name="tax_item_type_gst" id="tax_item_type_gst">
                                <input type="hidden" class="form-control" id="tax_id_gst" name="id" value="0" >
                                <span class="validation-color" id="err_tax_name_gst"><?php echo form_error('tax_name_gst'); ?></span>
                            </div>
                            <input type="hidden" value="" id="cmb_section_gst" name="cmb_section_gst">
                        </div>
                        <div class="col-sm-6" id="section" style="display: none">
                                <div class="form-group">
                                    <label for="tax_type">Section <span class="validation-color">*</span></label>
                                <select class="form-control select2" id="cmb_section_gst" name="cmb_section_gst">
                                    <option value="">Select Section</option>
                                    <?php foreach ($tax_section as  $value) {
                                        ?>
                                        <option value="<?php echo $value->section_id; ?>"><?php echo $value->section_name;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>                                 
                                    <span class="validation-color" id="err_tax_name_gst"><?php echo form_error('tax_name'); ?></span>
                                </div>
                            </div>
                        <div class="col-sm-6">
                                <div class="form-group sales">
                                    <label for="tax_value">Tax Value in % <span class="validation-color">*</span></label>
                                    <div class="input-group">
                                        <input type="number" min="0" max="100" maxlength="5"  onKeyUp="if(this.value>100){this.value='100';}else if(this.value<0){this.value='0';}" class="form-control text-right" id="tax_value_gst" name="tax_value_gst" value="<?php echo set_value('tax_value'); ?>" tabindex="2">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                    <span class="validation-color" id="err_tax_value_gst"><?php echo form_error('tax_value'); ?></span>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="description">
                                    <!-- Description --> Description
                                </label>
                                <textarea class="form-control" id="tax_description" name="tax_description"><?php echo set_value('description_gst'); ?></textarea>
                                <span class="validation-color" id="err_description_gst"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="add_tax_gst" type="submit" class="btn btn-info" data-dismiss="modal">Add</button>
                     <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
var tax_ajax = "yes";
</script>
<script type="text/javascript">
$(document).on("click", '.new_tax', function (e) {
var type = $(this).data('name');
$("#tax_item_type_gst").val(type);
});
</script>
<script src="<?php echo base_url('assets/js/tax/') ?>tax_gst.js"></script>