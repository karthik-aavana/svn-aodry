<!-- Category Modal -->
<div id="category_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Category</h4>
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
                            </span></h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="category_name">Category Name<span class="validation-color">*</span></label>
                                <input type="hidden" name="category_item_type" id="category_item_type">
                                <input type="input" class="form-control" name="category_name" id="category_name" maxlength="50">
                                <span class="validation-color" id="err_category_name"><?php echo form_error('category_name'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 type_div">
                            <div class="form-group">
                                <label for="category_name">Category Type <span class="validation-color">*</span></label>
                                <input type="text" class="form-control" name="category_type" value="service" id="category_type" readonly="">
                                
                                <!-- <select class="form-control" id="category_type" name="category_type">
                                	<option value='service'>Service</option>                                    
                                    <option value="">Select Category Type</option>
                                    <option value='service'>Service</option>
                                    <option value='product'>Goods</option>                                    
                                </select> -->
                                
                                <span class="validation-color" id="err_category_type"><?php echo form_error('category_name'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="add_category" class="btn btn-info" >Add</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
	var category_ajax = "yes";
</script>
<script type="text/javascript">
	$(document).on("click", '.new_category', function(e) {
		var type = $(this).data('name');
		$("#category_item_type").val(type);
        if(type == 'service'){
            $('#category_type').val('service');
        }else{
             $('#category_type').val('product');
        }
	}); 
</script>
<script src="<?php echo base_url('assets/js/category/') ?>category.js"></script>
