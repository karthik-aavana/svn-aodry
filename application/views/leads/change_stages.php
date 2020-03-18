<div id="change_stages" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Change Stages</h4>
            </div>
            <div class="modal-body">
                <form role="form" method="post" accept-charset="utf-8" name="change_stages_form" id="change_stages_form">
                    <div class="form-group">
                        <label for="group1">Group<span class="validation-color">*</span></label>
                        <input type="hidden" name="lead_id" id="lead_id" value="">
                        <select class="form-control select2" id="group1" name="group1" style="width: 100%;">
                            <option value="">Select</option>
                            <!-- <option value="Subscriber">Subscriber</option>
                            <option value="Lead">Lead</option>
                            <option value="Opportunities">Opportunities</option>
                            <option value="Not Interested">Not Interested</option> -->
                        </select>
                        <span class="validation-color" id="err_group"><?php echo form_error('group'); ?></span>
                    </div>
                    <div class="form-group">
                        <label for="stages1">Stages<span class="validation-color">*</span></label>
                        <select class="form-control select2" id="stages1" name="stages1" style="width: 100%;">
                            <option value="">Select</option>
                        </select>
                        <span class="validation-color" id="err_stages"><?php echo form_error('stages'); ?></span>
                    </div>
                    <div class="form-group">
                        <label for="assign_to">Assign To<span class="validation-color">*</span>
                        </label>
                        <!-- <input type="text" class="form-control" id="assign_to" name="assign_to" value="<?php echo set_value('assign_to'); ?>"> -->
                        <select class="form-control select2" id="assign_to" name="assign_to" style="width: 100%;">
                            <option value="">Select</option>
                        </select>
                        <span class="validation-color" id="err_assign_to"><?php echo form_error('assign_to'); ?></span>
                    </div>
                    <div class="form-group">
                        <label for="next_action_date">
                            Next Action Date<span class="validation-color">*</span>
                        </label>
                        <input type="text" class="form-control datepicker" id="next_action_date" name="next_action_date" value="<?php echo date('Y-m-d'); ?>">
                        <span class="validation-color" id="err_next_action_date"><?php echo form_error('next_action_date'); ?></span>
                    </div>
                    <div class="form-group">
                        <label for="comments">
                            Comments<span class="validation-color">*</span>
                        </label>
                        <textarea class="form-control" id="comments" rows="2" name="comments"><?php echo set_value('comments'); ?></textarea>
                        <span class="validation-color" id="err_comments"><?php echo form_error('comments'); ?></span>
                    </div>
                    <div class="form-group">
                        <label for="history">History</label>
                        <textarea type="text" class="form-control" id="history" rows="8" name="history" readonly></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="change_stages_submit" type="button" class="btn btn-primary" data-dismiss="modal">Change</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('body').on('click', '.change_stages', function ()
        {
            $("#change_stages_form")[0].reset();
            $("#group1").select2().val('').trigger('change.select2');
            $("#stages1").select2().val('').trigger('change.select2');

            var id = $(this).data('id');
            $('#lead_id').val(id);
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('leads/get_leads_history/') ?>",
                data: {id: id},
                success: function (data)
                {
                    var history = data['history'];
                    document.getElementById('history').innerHTML = history;

                    $('#group1').html('');
                    var option = '<option value="">Select</option>';
                    for (i = 0; i < data['group_data'].length; i++)
                    {
                        // if(data['stages_data1'][0].closed_stages_status == 1)
                        // {
                        //     if(data['group_data'][i].lead_group_id == data['group'])
                        //     {
                        //         option += '<option value="' + data['group_data'][i].lead_group_id + '" selected>' + data['group_data'][i].lead_group_name + '</option>';
                        //     }
                        // }
                        // else
                        // {
                        if (data['group_data'][i].lead_group_id == data['group'])
                        {
                            option += '<option value="' + data['group_data'][i].lead_group_id + '" selected>' + data['group_data'][i].lead_group_name + '</option>';
                        } else
                        {
                            option += '<option value="' + data['group_data'][i].lead_group_id + '">' + data['group_data'][i].lead_group_name + '</option>';
                        }
                        // }
                    }
                    $('#group1').html(option);

                    $('#stages1').html('');
                    var option = '<option value="">Select</option>';
                    for (i = 0; i < data['stages_data'].length; i++)
                    {
                        // if(data['stages_data1'][0].closed_stages_status == 1)
                        // {
                        //     if(data['stages_data'][i].lead_stages_id == data['stages'])
                        //     {
                        //         option += '<option value="' + data['stages_data'][i].lead_stages_id + '" selected>' + data['stages_data'][i].lead_stages_name + '</option>';
                        //     }
                        // }
                        // else
                        // {
                        if (data['stages_data'][i].lead_stages_id == data['stages'])
                        {
                            option += '<option value="' + data['stages_data'][i].lead_stages_id + '" selected>' + data['stages_data'][i].lead_stages_name + '</option>';
                        } else
                        {
                            option += '<option value="' + data['stages_data'][i].lead_stages_id + '">' + data['stages_data'][i].lead_stages_name + '</option>';
                        }
                        // }
                    }
                    $('#stages1').html(option);

                    $('#assign_to').html('');
                    var option = '<option value="">Select</option>';
                    for (i = 0; i < data['users'].length; i++)
                    {
                        if (data['users'][i].id == data['assign_to'])
                        {
                            option += '<option value="' + data['users'][i].id + '" selected>' + data['users'][i].first_name + ' ' + data['users'][i].last_name + '</option>';
                        } else
                        {
                            option += '<option value="' + data['users'][i].id + '">' + data['users'][i].first_name + ' ' + data['users'][i].last_name + '</option>';
                        }
                    }
                    $('#assign_to').html(option);
                }
            });
        });

        $("#group1").change(function (event)
        {
            var group = $('#group1').val();
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
                    $('#stages1').html('<option value="">Select</option>');
                    var option = '';
                    for (i = 0; i < data.length; i++)
                    {
                        option += '<option value="' + data[i].lead_stages_id + '">' + data[i].lead_stages_name + '</option>';
                    }
                    $('#stages1').append(option);
                }
            });
        });

        $("#stages1").change(function (event)
        {
            var stages1 = $('#stages1').val();
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: base_url + 'leads/get_stage',
                data:
                    {
                        stages: stages1
                    },
                success: function (data)
                {
                    if (data[0].closed_stages_status == 1)
                    {
                        $("#err_stages").text("The lead will be closed and customer will be added in our customers list.");
                    } else
                    {
                        $("#err_stages").text("");
                    }
                }
            });
        });

        $("#change_stages_submit").click(function (event)
        {
            var lead_id = $('#lead_id').val();
            var group = $('#group1').val();
            var stages = $('#stages1').val();
            var next_action_date = $('#next_action_date').val();
            var comments = $('#comments').val();
            var assign_to = $('#assign_to').val();

            if (group == null || group == "")
            {
                $("#err_group").text("Please Select Group.");
                return false;
            } else
            {
                $("#err_group").text("");
            }
            if (stages == null || stages == "")
            {
                $("#err_stages").text("Please Select Stages.");
                return false;
            } else
            {
                $("#err_stages").text("");
            }

            if (next_action_date == null || next_action_date == "")
            {
                $("#err_next_action_date").text("Please Select Next Action Date.");
                return false;
            } else
            {
                $("#err_next_action_date").text("");
            }
            if (assign_to == null || assign_to == "")
            {
                $("#err_assign_to").text("Please Enter Assign To.");
                return false;
            } else
            {
                $("#err_assign_to").text("");
            }
            if (comments == null || comments == "")
            {
                $("#err_comments").text("Please Enter Comments.");
                return false;
            } else
            {
                $("#err_comments").text("");
            }

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('leads/change_stages/') ?>",
                data: {
                    lead_id: lead_id,
                    group: group,
                    stages: stages,
                    next_action_date: next_action_date,
                    comments: comments,
                    assign_to: assign_to
                },
                success: function (data)
                {
                    setTimeout(function () {
                        location.reload();
                    });
                }
            });
        });
    });

</script>
