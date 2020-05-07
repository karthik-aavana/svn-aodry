<!--Subcategory Modal -->
<div id="edit_user_group_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit User Group</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <input type="hidden" name="group_id" id="group_id">
                            <label for="edit_groups">Group Name<span class="validation-color">*</span></label>
                            <input type="text" class="form-control" id="edit_groups" name="edit_groups" maxlength="50" value="<?php echo set_value('err_groups_edit'); ?>">
                            <span class="validation-color" id="err_groups_edit"><?php echo form_error('edit_groups'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="description_edit">Description<span class="validation-color">*</span></label>
                            <textarea class="form-control" id="description_edit" name="description_edit" maxlength="200" value="<?php echo set_value('description_edit'); ?>"></textarea>
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
<script type="text/javascript">
    $(document).on("click", ".edit_groups", function () {
        var id = $(this).data('id');
        $.ajax(
                {
                    url: base_url + 'groups/edit/' + id,
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (result)
                    {                       
                        $('#group_id').val(result[0].id);
                        $('#edit_groups').val(result[0].name);
                        $('#description_edit').val(result[0].description);
                    }
                });
    });
    $(document).ready(function(){
        $("#update_submit").click(function(event) {
            var group_id = $('#group_id').val();
            var edit_groups = $('#edit_groups').val();
            var description_edit = $('#description_edit').val();
            var name_regex = /^[a-zA-Z0-9\[\]/@()#$%&\-.+,\d\-_\s]+$/; 
            if (edit_groups == null || edit_groups == "") {
                $("#err_groups_edit").text("Please Enter Group Name");
                return false;
            } else {
                $("#err_groups_edit").text("");
            }
            if (!edit_groups.match(name_regex)){
                $('#err_groups_edit').text("Please Enter Valid Gruop Name.");
                return !1
            } else {
                $("#err_groups_edit").text("")
            }
            if (description_edit == null || description_edit == "") {
                $("#err_description_edit").text("Please Enter Description");
                return false;
            } else {
                $("#err_description_edit").text("");
            }
            $.ajax(
                {
                    url: base_url + 'groups/update_user_group',
                    dataType: 'JSON',
                    method: 'POST',
                    data:{'group_id': group_id, 'group_name': edit_groups, 'description': description_edit},
                    success: function (result) {
                        if(result.resl == 'duplicate'){
                            $('#err_groups_edit').text('Group Name Already Exist');
                            return !1
                        }else{
                            setTimeout(function () {// wait for 5 secs(2)
                                $(document).find('[name=check_item]').trigger('change');
                                //location.reload(); // then reload the page.(3)
                            },500);
                            if(result.flag){
                                dTable.destroy();
                                dTable = getAllUserGroup();
                                alert_d.text = result.msg;
                                PNotify.success(alert_d);
                                $('#edit_user_group_modal').modal('hide');
                                $('#edit_groups').val('');
                                $('#description_edit').val('');
                            }else{
                                alert_d.text = result.msg;
                                PNotify.error(alert_d);
                                $('#edit_user_group_modal').modal('hide');
                            }
                        }
                    },
                    error:function(){
                        alert_d.text = 'Something Went Wrong!';
                        PNotify.error(alert_d);
                    }    
            });
        });
    });
</script>

