<div id="uom_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="uomForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add UOM</h4>
                </div>
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
                                    <label for="uom">UOM (Unit of Measurement)<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="uom" name="uom" maxlength="4" value="<?php echo set_value('uom'); ?>">
                                    <span class="validation-color" id="err_uom"><?php echo form_error('uom'); ?></span>
                                </div>
                            </div>
                           
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="uom">UOM Type<span class="validation-color">*</span></label>
                                        <select class="form-control select2" id="uom_type_a" name="uom_type_a">
                                            <option value="">Select UOM Type</option>
                                            <option value='service'>Service</option>
                                            <option value='product'>Goods/Products</option>
                                            <option value='both'>Both</option>
                                        </select>
                                    <span class="validation-color" id="err_uom_type"><?php echo form_error('uom_type'); ?>
                                        
                                    </span>
                                </div>
                            </div> 
                            </div>                          
                            <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="description">Description</span></label>
                                    <input type="text" class="form-control" id="description_uom" name="description_uom" maxlength="50" value="<?php echo set_value('description_uom'); ?>">
                                    <span class="validation-color" id="err_description"><?php echo form_error('description_uom'); ?></span>
                                </div>
                        </div>
                </div>               
            </div>
            <div class="modal-footer">
                <button type="button" id="submit" class="btn btn-info" >Add</button>
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
    if(type == 'service'){
        $("#uom_type_a").html("");
        $("#uom_type_a").append('<option value="service" selected>Service</option>');
        $("#uom_type_a").append('<option value="both">Both</option>');
    }else{
        $("#uom_type_a").html("");
        $("#uom_type_a").append('<option value="product" selected>Goods/Products</option>');
        $("#uom_type_a").append('<option value="both">Both</option>');
    }
});
</script>
<script src="<?php echo base_url('assets/js/uom/') ?>uom.js"></script>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>

