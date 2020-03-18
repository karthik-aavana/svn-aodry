<div id="tax_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">

    <div class="modal-dialog modal-md">

        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4>Add Tax123</h4>

            </div>

            <form name="form" id="taxForm" >

                <div class="modal-body">

                    <div class="row">

                        <div class="col-sm-12">

                            <div class="form-group">

                                <label for="tax_name">

                                    <!-- Tax Name --> Tax Type <span class="validation-color">*</span>

                                </label>

                                <input type="text" class="form-control" id="tax_name" name="tax_name" value="<?php echo set_value('tax_name'); ?>">

                                <input type="hidden" name="tax_item_type" id="tax_item_type">

                                <input type="hidden" class="form-control" id="tax_id" name="id" value="0" >

                                <span class="validation-color" id="err_tax_name"><?php echo form_error('tax_name'); ?></span>

                            </div>

                        </div>

                        <div class="col-sm-12">

                            <div class="form-group sales">

                                <label for="tax_value">Tax Rate <span class="validation-color">*</span></label>

                                <div class="input-group">

                                    <input type="text" class="form-control text-right" id="tax_value" name="tax_value" value="<?php echo set_value('tax_value'); ?>">

                                    <span class="input-group-addon">%</span>

                                </div>

                                <span class="validation-color" id="err_tax_value"><?php echo form_error('tax_value'); ?></span>

                            </div>

                        </div>

                        <div class="col-sm-12">

                            <div class="form-group">

                                <label for="description">

                                    <!-- Description --> Description

                                </label>

                                <textarea class="form-control" id="tax_description" name="description"><?php echo set_value('description'); ?></textarea>

                                <span class="validation-color" id="err_description"></span>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button id="add_tax" type="submit" class="btn btn-info" data-dismiss="modal">Add</button>

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

$("#tax_item_type").val(type);

});

</script>

<script src="<?php echo base_url('assets/js/tax/') ?>tax.js"></script>