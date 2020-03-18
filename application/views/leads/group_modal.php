<div id="group_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="group_label">Add Group</h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">
                                <div class="row">
                                    <form id="groupForm">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="group_name">Group Name</label>
                                                <input type="hidden" name="group_id" id="group_id" value="">
                                                <input type="text" class="form-control" id="group_name" name="group_name" value="<?php echo set_value('group_name'); ?>">
                                                <span class="validation-color" id="err_group_name"><?php echo form_error('group_name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary pull-right" id="group_modal_submit" data-dismiss="modal">Add</button>
                                                <button type="button" class="btn btn-default pull-right" data-dismiss="modal" style="margin-right: 2%;">Close</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.box-body -->
                            </div>

                        </div>
                    </div>
                </div> <!-- /controls -->
            </div> <!-- /control-group -->
        </div>
    </div>
</div>
<script type="text/javascript">
    if (typeof group_add === 'undefined' || group_add === null) {
        var group_add = "no";
    }
    $(document).ready(function () {
        $(".add_group").click(function (event) {
            $("#group_label").text('Add Group');
            $("#group_modal_submit").text('Add');
            $("#group_name").val("");
            $("#err_group_name").text("");
        });
        $('body').on('click', '.edit_group', function () {
            $("#group_label").text('Edit Group');
            $("#group_modal_submit").text('Update');
            $("#group_id").val($(this).data('id'));
            $("#group_name").val($(this).data('name'));
            $("#err_group_name").text("");
            group_add = 'edit';
        });
        $(".group_modal").click(function (event) {
            $("#group_name").val("");
            $("#err_group_name").text("");
        });
        $("#group_modal_submit").click(function (event)
        {
            var group_name = $('#group_name').val();
            if (group_name == null || group_name == "")
            {
                $("#err_group_name").text("Please Enter Group Name.");
                return false;
            } else
            {
                $("#err_group_name").text("");
            }
            if (group_add == 'edit')
            {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "<?php echo base_url('leads/edit_group/') ?>",
                    data:
                            {
                                group_id: $('#group_id').val(),
                                group_name: group_name
                            },
                    success: function (result)
                    {
                        setTimeout(function () {
                            location.reload();
                        });
                    }
                });
            } else
            {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "<?php echo base_url('leads/add_group/') ?>",
                    data:
                            {
                                group_name: group_name
                            },
                    success: function (result)
                    {
                        var data = result['data'];
                        if (group_add == 'no')
                        {
                            setTimeout(function () {
                                location.reload();
                            });
                        } else
                        {
                            $('#group').html('');
                            $('#group').append('<option value="">Select</option>');
                            for (i = 0; i < data.length; i++)
                            {
                                $('#group').append('<option value="' + data[i].lead_group_id + '">' + data[i].lead_group_name + '</option>');
                            }
                            $('#group').val(result['id']).attr("selected", "selected");
                            $('#group').change();
                        }
                    }
                });
            }
        });
    });
</script>
