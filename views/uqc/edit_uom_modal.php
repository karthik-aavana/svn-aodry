<!--Subcategory Modal -->
<div id="edit_uom_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit UOM</h4>
            </div>
            <div class="modal-body">
                <div id="loader_coco">
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
                            	<input type="hidden" name="uqc_id" id="uqc_id">
                                <div class="form-group">
                                    <label for="uom_edit">UOM (Unit of Measurement)<span class="validation-color">*</span></label>
                                    <input type="text" class="form-control" id="uom_edit" name="uom_edit" maxlength="4" value="<?php echo set_value('err_uom_edit'); ?>">
                                    <span class="validation-color" id="err_uom_edit"><?php echo form_error('uom_edit'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="uom">UOM Type<span class="validation-color">*</span></label>
                                <select class="form-control select2" id="uom_type_e" name="uom_type_e">
                                    <option value="">Select UOM Type</option>
                                    <option value='service'>Service</option>
                                    <option value='product'>Goods/Products</option>
                                    <option value='both'>Both</option>
                                </select>
                                    <span class="validation-color" id="err_uom_type_e"><?php echo form_error('uom_type_e'); ?></span>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="description_edit">Description</span></label>
                                    <textarea class="form-control" id="description_edit" name="description_edit" maxlength="50" value="<?php echo set_value('description_edit'); ?>"></textarea>
                                    <span class="validation-color" id="err_description_edit"><?php echo form_error('err_description_edit'); ?></span>
                                </div>
                            </div>
                     </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="update_submit" class="btn btn-info">Update</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
	$(document).on("click", ".edit_uom", function () {
        var id = $(this).data('id');
        $.ajax(
                {
                    url: base_url + 'uqc/get_uqc_modal/' + id,
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (result)
                    {
                        $('#uqc_id').val(result[0].id);
                        $('#uom_edit').val(result[0].uom);
                        $('#description_edit').val(result[0].description).change();
                        $('#uom_type_e').val(result[0].uom_type).change();
                    }
                });
    });
$(document).ready(function () {
        $("#update_submit").click(function (event) {
        var uom_edit = $('#uom_edit').val();        
        var description = $('#description_edit').val();
        var uom_type_edit = $('#uom_type_e').val();
        var uqc_id = $('#uqc_id').val();
        var name_regex = /^[-a-zA-Z\s]+$/;
	    if (uom_edit == null || uom_edit == "") {
	        $("#err_uom_edit").text("Please Enter UOM");
	        return false;
	    } else {
	        $("#err_uom_edit").text("");
	    }
        if (uom_type_edit == null || uom_type_edit == "") {
            $("#err_uom_type_e").text("Please Select UOM Type");
            return false;
        } else {
            $("#err_uom_type_e").text("");
        }
        if(uom_edit === description){
               $("#err_description_edit").text("Both UOM and Description shouldn't Identical");
                return false; 
        } else {
            $("#err_description_edit").text("");
        }
        if (!uom_edit.match(name_regex)){
            $('#err_uom_edit').text("Please Enter Valid UOM.");
            return !1
        } else {
            $("#err_uom_edit").text("")
        }
        if(!(description == null || description == "")) {
                var i,temp="";
                var addr_trim=$.trim(description);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_description_edit").text("Please Enter Valid Address.");
                            return false; 
                        }
                    }  
                }  
            }
       /* if (!description.match(name_regex) && description !=''){
            $('#err_description_edit').text("Please Enter Valid Description.");
            return !1
        } else {
            $("#err_description_edit").text("")
        }
*/
        $.ajax(
                {
                    url: base_url + 'uqc/update_uqc_modal',
                    dataType: 'JSON',
                    method: 'POST',
                    data:{'uom': uom_edit, 'description': description, 'id': uqc_id,'uom_type': uom_type_edit },
                    beforeSend: function(){
                     // Show image container
                    $("#loader_coco").show();
                    }, 
                    success: function (result) {
                        if(result.resl == 'duplicate'){
                            $("#loader_coco").hide();
                            $('#err_uom_edit').text("UOM already exist in "+result.type);
                            return !1
                        }else if(result.resl == 'duplicate_desc'){

                            $('#err_uom_edit').text("This UOM  is exist in Description.");
                            return !1
                        } else {
                            setTimeout(function () {// wait for 5 secs(2)
                                $(document).find('[name=check_item]').trigger('change');
                                //location.reload(); // then reload the page.(3)
                            },500);
                            if(result.flag){
                                $("#loader_coco").hide();
                                $('#edit_uom_modal').modal('hide');
                                dTable.destroy();
                                dTable = getAllUqc();
                                alert_d.text = result.msg;
                                PNotify.success(alert_d);
                            }else{
                                alert_d.text = result.msg;
                                PNotify.error(alert_d);
                                $('#edit_uom_modal').modal('hide');
                            }
                         }    
                    }
                });
    });
        
    });
</script>
