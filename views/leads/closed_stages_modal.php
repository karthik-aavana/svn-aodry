<div id="closed_stages_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="stages_label">Changes Closed Stage</h4>
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
                                                <label for="group_name2">Group</label>
                                                <select class="form-control select2" id="group_name2" name="group_name2" style="width: 100%;">
                                                </select>
                                                <span class="validation-color" id="err_group_name2"><?php echo form_error('group_name2'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="closed_stages_name">Stage<span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="closed_stages_name" name="closed_stages_name" style="width: 100%;">
                                                    <option value="">Select</option>
                                                </select>
                                                <span class="validation-color" id="err_stages"><?php echo form_error('stages'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary pull-right" id="closed_stages_modal_submit" data-dismiss="modal">Change</button>
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
        $(".closed_stages_modal").click(function (event) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: base_url + 'leads/get_group',
                success: function (data)
                {
                    $('#group_name2').html('<option value="">Select</option>');
                    var option = '';
                    for (i = 0; i < data.length; i++)
                    {
                        option += '<option value="' + data[i].lead_group_id + '">' + data[i].lead_group_name + '</option>';
                    }
                    $('#group_name2').append(option);
                }
            });
        });

        $("#group_name2").change(function (event)
        {
            var group = $('#group_name2').val();
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: base_url + 'leads/get_stages',
                data:
                        {
                            group: group
                        },
                success: function (data)
                {
                    $('#closed_stages_name').html('<option value="">Select</option>');
                    var option = '';
                    for (i = 0; i < data.length; i++)
                    {
                        option += '<option value="' + data[i].lead_stages_id + '">' + data[i].lead_stages_name + '</option>';
                    }
                    $('#closed_stages_name').append(option);
                }
            });
        });

        $("#closed_stages_modal_submit").click(function (event)
        {
            var closed_stages_name = $('#closed_stages_name').val();
            var group_name2 = $('#group_name2').val();
            if (closed_stages_name == null || closed_stages_name == "")
            {
                $("#err_closed_stages_name").text("Please Enter Stage Name.");
                return false;
            } else
            {
                $("#err_closed_stages_name").text("");
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('leads/closed_stages') ?>",
                data:
                        {
                            closed_stages_name: closed_stages_name,
                            group_name: group_name2
                        },
                success: function (result)
                {
                    setTimeout(function () {
                        location.reload();
                    });
                }
            });
        });
    });
</script>
