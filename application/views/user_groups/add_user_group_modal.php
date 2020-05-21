<div id="add_user_group_modal" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add User Group</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="groups">Group Name<span class="validation-color">*</span></label>
                            <input type="text" class="form-control" id="add_groups" name="add_groups" maxlength="50" value="<?php echo set_value('add_groups'); ?>">
                            <span class="validation-color" id="err_add_groups"><?php echo form_error('add_groups'); ?></span>
                        </div>
                    </div>
                </div>                          
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="description">Description<span class="validation-color">*</span></label>
                            <textarea class="form-control" id="description_groups" name="description_groups" maxlength="200" value="<?php echo set_value('description_groups'); ?>"></textarea>
                            <span class="validation-color" id="err_description"><?php echo form_error('description_groups'); ?></span>
                        </div>
                    </div>
                </div>               
            </div>
            <div class="modal-footer">
                <button type="submit" id="submit" class="btn btn-info" >Add</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#submit").click(function(event) {
            var group_name = $('#add_groups').val();
            var description_groups = $('#description_groups').val();
            var name_regex = /^[a-zA-Z0-9\[\]/@()#$%&\-.+,\d\-_\s]+$/; 
            if (group_name == null || group_name == "") {
                $("#err_add_groups").text("Please Enter Group Name");
                return false;
            } else {
                $("#err_add_groups").text("");
            }
            if (!group_name.match(name_regex)){
                $('#err_add_groups').text("Please Enter Valid Gruop Name.");
                return !1
            } else {
                $("#err_add_groups").text("")
            }
            if (description_groups == null || description_groups == "") {
                $("#err_description").text("Please Enter Description");
                return false;
            } else {
                $("#err_description").text("");
            }
            $.ajax(
                {
                    url: base_url + 'groups/add_user_group',
                    dataType: 'JSON',
                    method: 'POST',
                    data:{'group_name': group_name, 'description': description_groups},
                    success: function (result) {
                        console.log(result);
                        if(result.resl == 'duplicate'){
                            $('#err_add_groups').text('Group Name Already Exist');
                            return !1
                        }else{
                            if(result.flag){
                                dTable.destroy();
                                dTable = getAllUserGroup();
                                alert_d.text = result.msg;
                                PNotify.success(alert_d);
                                $('#add_user_group_modal').modal('hide');
                                $('#add_groups').val('');
                                $('#description_groups').val('');
                            }else{
                                alert_d.text = result.msg;
                                PNotify.error(alert_d);
                                $('#add_user_group_modal').modal('hide');
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

