<div id="source_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="source_label">Add Source</h4>
            </div>
            <div class="modal-body" style="padding-top: 0;">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">
                                <div class="row">
                                    <form id="sourceForm">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="source_name">Source Name</label>
                                                <input type="hidden" name="source_id" id="source_id" value="">
                                                <input type="text" class="form-control" id="source_name" name="source_name" value="<?php echo set_value('source_name'); ?>">
                                                <span class="validation-color" id="err_source_name"><?php echo form_error('source_name'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary pull-right" id="source_modal_submit" data-dismiss="modal">Add</button>
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
    if (typeof source_add === 'undefined' || source_add === null) {
        var source_add = "no";
    }

    $(document).ready(function () {
        $(".add_source").click(function (event) {
            $("#source_label").text('Add Source');
            $("#source_modal_submit").text('Add');

            $("#source_name").val("");
            $("#err_source_name").text("");
        });

        $('body').on('click', '.edit_source', function () {
            $("#source_label").text('Edit Source');
            $("#source_modal_submit").text('Update');

            $("#source_id").val($(this).data('id'));
            $("#source_name").val($(this).data('name'));
            $("#err_source_name").text("");
            source_add = 'edit';
        });

        $(".source_modal").click(function (event) {
            $("#source_name").val("");
            $("#err_source_name").text("");
        });

        $("#source_modal_submit").click(function (event)
        {
            var source_name = $('#source_name').val();
            if (source_name == null || source_name == "")
            {
                $("#err_source_name").text("Please Enter Source Name.");
                return false;
            } else
            {
                $("#err_source_name").text("");
            }

            if (source_add == 'edit')
            {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "<?php echo base_url('leads/edit_source/') ?>",
                    data:
                            {
                                source_id: $('#source_id').val(),
                                source_name: source_name
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
                    url: "<?php echo base_url('leads/add_source/') ?>",
                    data:
                            {
                                source_name: source_name
                            },
                    success: function (result)
                    {
                        var data = result['data'];
                        if (source_add == 'no')
                        {
                            setTimeout(function () {
                                location.reload();
                            });
                        } else
                        {
                            $('#source').html('');
                            $('#source').append('<option value="">Select</option>');
                            for (i = 0; i < data.length; i++)
                            {
                                $('#source').append('<option value="' + data[i].lead_source_id + '">' + data[i].lead_source_name + '</option>');
                            }
                            $('#source').val(result['id']).attr("selected", "selected");
                            $('#source').change();
                        }
                    }
                });
            }
        });
    });
</script>
