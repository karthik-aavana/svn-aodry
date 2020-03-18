<div id="subdepartment_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Subdepartment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="department_id_model">Department<span class="validation-color">*</span></label>
                            <input type="hidden" name="department_id_model" value="" id="department_id_model" class="form-control"/>
                            <input type="text" name="department_name" value="" id="department_name" class="form-control" readonly=""/>
                            <span class="validation-color" id="err_department_id_model"><?php echo form_error('department_id_model'); ?></span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="subdepartment_name_a">Subdepartment Name <span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="subdepartment_name_a" id="subdepartment_name_a" maxlength="50">
                            <span class="validation-color" id="err_subdepartment_name_a"><?php echo form_error('subdepartment_name_a'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="subdepartment_add_submit" class="btn btn-info" >Add</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var subdepartment_ajax = "yes";
    $(document).on("click", ".new_subdepartment", function (){
        var new_department = $('#cmb_department').val();
        if(new_department == ''){
            $('#subdepartment_modal #department_id_model').val('');
            $('#subdepartment_modal #department_name').val('');
        }else{
            $.ajax({ url: base_url + 'department/get_department_ajax',
                dataType: 'JSON',
                method: 'POST',
                data: {'new_department': new_department},
                success: function (result){
                var data = result['data']; 
                for (i = 0; i < data.length; i++){
                    if (result.id == data[i].department_id){
                        $('#subdepartment_modal #department_id_model').val(data[i].department_id);
                        $('#subdepartment_modal #department_name').val(data[i].department_name);
                    }
                }
                }
            });
        }
        
    });
</script>
<script src="<?php echo base_url('assets/js/') ?>subdepartment.js"></script>
