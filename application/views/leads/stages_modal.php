<div id="stages_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="stages_label">Add Stage</h4>
            </div>
            <div class="modal-body" style="padding-top: 0;">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">
                                <div class="row">
                                    <form id="stagesForm">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="group_name1">Group Name</label>
                                                <input type="hidden" name="stages_id" id="stages_id" value="">
                                                <select class="form-control select2" id="group_name1" name="group_name1" style="width: 100%;">
                                                </select>
                                                <span class="validation-color" id="err_group_name1"><?php echo form_error('group_name1'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="stages_name">Stage Name</label>
                                                <input type="text" class="form-control" id="stages_name" name="stages_name" value="<?php echo set_value('stages_name'); ?>">
                                                <span class="validation-color" id="err_stages_name"><?php echo form_error('stages_name'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary pull-right" id="stages_modal_submit" data-dismiss="modal">Add</button>
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
    if (typeof stages_add === 'undefined' || stages_add === null) {
        var stages_add = "no";
    }

    $(document).ready(function () {
        $(".add_stages").click(function (event) {
            $("#stages_label").text('Add Stage');
            $("#stages_modal_submit").text('Add');

            $("#stages_name").val("");
            $("#err_stages_name").text("");
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: base_url + 'leads/get_group',
                success: function (data)
                {
                    $('#group_name1').html('<option value="">Select</option>');
                    var option = '';
                    for (i = 0; i < data.length; i++)
                    {
                        option += '<option value="' + data[i].lead_group_id + '">' + data[i].lead_group_name + '</option>';
                    }
                    $('#group_name1').append(option);
                }
            });
        });

        $('body').on('click', '.edit_stages', function () {
            $("#stages_label").text('Edit Stages');
            $("#stages_modal_submit").text('Update');

            $("#stages_id").val($(this).data('id'));
            $("#stages_name").val($(this).data('name'));
            $("#err_stages_name").text("");
            stages_add = 'edit';
            var group_id = $(this).data('group_id');
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: base_url + 'leads/get_group',
                success: function (data)
                {
                    $('#group_name1').html('<option value="">Select</option>');
                    var option = '';
                    for (i = 0; i < data.length; i++)
                    {
                        if (data[i].lead_group_id == group_id)
                        {
                            option += '<option value="' + data[i].lead_group_id + '" selected>' + data[i].lead_group_name + '</option>';
                        } else
                        {
                            option += '<option value="' + data[i].lead_group_id + '">' + data[i].lead_group_name + '</option>';
                        }
                    }
                    $('#group_name1').append(option);
                }
            });
        });

        $(".stages_modal").click(function (event) {
            $("#stages_name").val("");
            $("#err_stages_name").text("");
        });

        $("#stages_modal_submit").click(function (event)
        {
            var stages_name = $('#stages_name').val();
            var group_name1 = $('#group_name1').val();
            if (stages_name == null || stages_name == "")
            {
                $("#err_stages_name").text("Please Enter Stage Name.");
                return false;
            } else
            {
                $("#err_stages_name").text("");
            }

            if (stages_add == 'edit')
            {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "<?php echo base_url('leads/edit_stages/') ?>",
                    data:
                            {
                                stages_id: $('#stages_id').val(),
                                stages_name: stages_name
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
                    url: "<?php echo base_url('leads/add_stages/') ?>",
                    data:
                            {
                                stages_name: stages_name,
                                group_name1: group_name1
                            },
                    success: function (result)
                    {
                        var data = result['data'];
                        if (stages_add == 'no')
                        {
                            setTimeout(function () {
                                location.reload();
                            });
                        } else
                        {
                            $('#stages').html('');
                            $('#stages').append('<option value="">Select</option>');
                            for (i = 0; i < data.length; i++)
                            {
                                $('#stages').append('<option value="' + data[i].lead_stages_id + '">' + data[i].lead_stages_name + '</option>');
                            }
                            $('#stages').val(result['id']).attr("selected", "selected");
                            $('#stages').change();
                        }
                    }
                });
            }
        });
    });
</script>
