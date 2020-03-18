<div id="add_service_uom_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add UOM</h4>
            </div>
            <div class="modal-body">
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
                                            <option value='both'>Both</option>
                                     </select>
                                        <span class="validation-color" id="err_uom_type"><?php echo form_error('uom_type'); ?></span>
                                    </div>
                                </div> 
                            </div>                          
                            <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="description">Description</span></label>
                                    <textarea class="form-control" id="description_uom" name="description_uom" maxlength="50" value="<?php echo set_value('description_uom'); ?>"></textarea>
                                    <span class="validation-color" id="err_description"><?php echo form_error('description_uom'); ?></span>
                                </div>
                        </div>
                </div>               
            </div>
            <div class="modal-footer">
                <button type="button" id="submit" class="btn btn-info" >Add</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
$(document).ready(function() {
        $("#submit").click(function(event) {
            var name_regex = /^[-a-zA-Z\s]+$/;
            var uom = $('#uom').val().trim();
            var uom_type = $('#uom_type_a').val().trim();
            var description = $('#description_uom').val().trim();
            if (uom == null || uom == "") {
                $("#err_uom").text("Please Enter UOM");
                return false;
            } else {
                $("#err_uom").text("");
            }
            if (uom_type == null || uom_type == "") {
                $("#err_uom_type").text("Please Select UOM Type");
                return false;
            } else {
                $("#err_uom_type").text("");
            }
            if(uom === description){
               $("#err_description").text("Both UOM and Description shouldn't Identical");
                return false; 
            } else {
                $("#err_description").text("");
            }
            if (!uom.match(name_regex)){
                $('#err_uom').text("Please Enter Valid UOM.");
                return !1
            } else {
                $("#err_uom").text("")
            }
            /*if (!description.match(name_regex) && description !=''){
                $('#err_description').text("Please Enter Valid Description.");
                return !1
            } else {
            $("#err_description").text("")
            }*/
            if(!(description == null || description == "")) {
                var i,temp="";
                var addr_trim=$.trim(description);
                // console.log(addr_trim.length);
                for(i=0;i<addr_trim.length;i++) {
                    if(addr_trim[i] == addr_trim[i+1]) {
                        temp +=addr_trim[i];
                        if(temp.length >= 4) {
                           $("#err_description").text("Please Enter Valid Address.");
                            return false; 
                        }
                    }  
                }  
            }
             $.ajax(
                {
                    url: base_url + 'uqc/add_uqc_modal',
                    dataType: 'JSON',
                    method: 'POST',
                    data:{'uom': uom, 'description': description,'uom_type': uom_type },
                    success: function (result) {
                        if(result == 'duplicate'){
                            $('#err_uom').text("UQC already exist");
                            return !1
                        }else if(result == 'duplicate_desc'){
                            $('#err_uom').text("This UQC is exist in Description");
                            return !1
                        } else{
                            $('#add_service_uom_modal').modal('hide');
                        }
                    }        
                });
        });
        
        $("#uom").on("blur keyup", function(event) {
            var name_regex = /^[-a-zA-Z\s]+$/;
            var uom = $('#uom').val();
            return !1;
            if (uom == null || uom == "") {
                $("#err_uom").text("Please Enter UOM");
                return false;
            } else {
                $("#err_uom").text("");
            }
        });
    /*
        $("#description").on("blur keyup", function(event) {
                var name_regex = /^[-a-zA-Z\s]+$/;
                var description = $('#description').val();
                if (description == null || description == "") {
                    $("#err_description").text("Please Enter Description");
                    return false;
                } else {
                    $("#err_description").text("");
                }
            });*/
    
    });
</script>
