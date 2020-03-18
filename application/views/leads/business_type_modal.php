<div id="business_type_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="business_type_label">Add Business Type</h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">
                                <div class="row">
                                    <form id="business_typeForm">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="business_type1">Business Type</label>
                                                <input type="hidden" name="business_id" id="business_id" value="">
                                                <input type="text" class="form-control" id="business_type1" name="business_type1" value="<?php echo set_value('business_type1'); ?>">
                                                <span class="validation-color" id="err_business_type1"><?php echo form_error('business_type1'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary pull-right" id="business_type_modal_submit" data-dismiss="modal">Add</button>
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
    if (typeof business_type_add === 'undefined' || business_type_add === null) {
        var business_type_add = "no";
    }
    $(document).ready(function () {
        $(".add_business_type").click(function (event) {
            $("#business_type_label").text('Add Business Type');
            $("#business_type_modal_submit").text('Add');
            $("#business_type1").val("");
            $("#err_business_type1").text("");
        });
        $('body').on('click', '.edit_business_type', function () {
            $("#business_type_label").text('Edit Business Type');
            $("#business_type_modal_submit").text('Update');
            $("#business_id").val($(this).data('id'));
            $("#business_type1").val($(this).data('name'));
            $("#err_business_type1").text("");
            business_type_add = 'edit';
        });
        $(".business_type_modal").click(function (event) {
            $("#business_type1").val("");
            $("#err_business_type1").text("");
        });
        $("#business_type_modal_submit").click(function (event)
        {
            var business_type = $('#business_type1').val();
            if (business_type == null || business_type == "")
            {
                $("#err_business_type1").text("Please Enter Business Type.");
                return false;
            } else
            {
                $("#err_business_type1").text("");
            }
            if (business_type_add == 'edit')
            {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "<?php echo base_url('leads/edit_business_type/') ?>",
                    data:
                            {
                                business_id: $('#business_id').val(),
                                business_type: business_type
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
                    url: "<?php echo base_url('leads/add_business_type/') ?>",
                    data:
                            {
                                business_type: business_type
                            },
                    success: function (result)
                    {
                        var data = result['data'];
                        if (business_type_add == 'no')
                        {
                            setTimeout(function () {
                                location.reload();
                            });
                        } else
                        {
                            $('#business_type').html('');
                            $('#business_type').append('<option value="">Select</option>');
                            for (i = 0; i < data.length; i++)
                            {
                                $('#business_type').append('<option value="' + data[i].lead_business_id + '">' + data[i].lead_business_type + '</option>');
                            }
                            $('#business_type').val(result['id']).attr("selected", "selected");
                            $('#business_type').change();
                        }
                    }
                });
            }
        });
    });
</script>
