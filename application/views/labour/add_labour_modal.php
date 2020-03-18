<div id="add_labour_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>
                    Add Labour
                </h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">
                                <div class="row">
                                    <form id="categoryForm_a">
                                        <div class="col-md-12">
                                            <div class="form-group col-md-4">
                                                <label for="raw_material_code">Activity Name <span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" tabindex="1"  id="activity_name" name="activity_name" value="">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="raw_material_code">Classification <span class="validation-color">* <span  data-toggle="modal" data-target="#add_classification" style="float: right;color:blue">+ Add </span></span></label>
                                                <select class="form-control select2" id="classify" name="classify" style="width: 100%;" tabindex="6">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($labour_classification as $key => $value)
                                                    {

                                                        echo "<option value='" . $value->labour_classification_id . "'>" . $value->labour_classification_name . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="raw_material_code">No Of Labour <span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" tabindex="1"  id="no_of_labour" name="no_of_labour" value="">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">

                                            <div class="form-group col-md-4">
                                                <label for="raw_material_code">Cost per Hour <span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" tabindex="1"  id="cost_per_person" name="cost_per_person" value="">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="raw_material_code"> Type<span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="labour_type" name="labour_type" style="width: 100%;" tabindex="6">
                                                    <option value="hourly">Hourly</option>
                                                    <option value="daily_basis">Days Basis</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4" id="day_basis">
                                                <label for="raw_material_code"> No of Hours/days<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" tabindex="1"  id="no_of_hours" name="no_of_hours" value="">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="raw_material_code"> No of Days<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" tabindex="1"  id="no_of_days_per" name="no_of_days_per" value="">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="raw_material_code">Total Hours<span class="validation-color">*</span></label>
                                                <input type="text" class="form-control"  tabindex="1"  id="total_hours" name="total_hours" value="">
                                                <span class="validation-color" id="err_raw_material_code"><?php echo form_error('raw_material_code'); ?></span>
                                            </div>

                                        </div>

                                        <div class="col-sm-12">
                                            <button type="submit" id="add_submit" class="btn btn-info pull-right" class="close" data-dismiss="modal"> Add 
                                            </button>
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
    $(document).ready(function ()
    {
        if ($("#labour_type").val() == "hourly") {

            $("#day_basis").hide()
        }
        $("#labour_type").change(function ()
        {
            if ($("#labour_type").val() == "hourly")
            {

                $("#day_basis").hide()
            }
            if ($("#labour_type").val() == "daily_basis") {

                $("#day_basis").show()
            }
        });
        $("#add_submit").click(function (event) {
            var category_name_a = $('#category_name_a').val();
            var category_type_a = $('#category_type_a').val();
            var category_name_empty = "Please Enter the Category Name.";
            var category_name_invalid = "Please Enter Valid Category Name";
            var category_name_length = "Please Enter Category Name Minimun 3 Character";

            if (category_name_a == null || category_name_a == "")
            {
                $("#err_category_name_a").text(category_name_empty);
                return !1
            } else
            {
                $("#err_category_name_a").text("")
            }
            if (!category_name_a.match(general_regex))
            {
                $('#err_category_name_a').text(category_name_invalid);
                return !1
            } else
            {
                $("#err_category_name_a").text("")
            }
            if (category_name_a.length < 3)
            {
                $('#err_category_name_a').text(category_name_length);
                return !1
            } else
            {
                $("#err_category_name_a").text("")
            }

            if (category_type_a == null || category_type_a == "")
            {
                $("#err_category_type_a").text("Please Select Category Type");
                return !1
            } else
            {
                $("#err_category_type_a").text("")
            }

            $.ajax(
                    {
                        url: base_url + 'labour/add_category_modal',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'category_name': category_name_a,
                                    'category_type': category_type_a
                                },
                        success: function (result)
                        {
                            setTimeout(function () {
                                location.reload();
                            });
                        }
                    });
        });

        $("#category_name_a").on("blur", function (event)
        {
            var category_name_a = $('#category_name_a').val();
            if (category_name_a == "")
            {
                $('#err_category_name_a').text("Please Enter Category Name");
                return !1
            } else
            {
                $('#err_category_name_a').text("");
            }

        });
        $("#category_type_a").change(function ()
        {
            var category_type_a = $('#category_type_a').val();


            if (category_type_a == "")
            {
                $('#err_category_type_a').text("Please Select Category Type.");
                return !1
            } else
            {
                $('#err_category_type_a').text("");
            }


        });
    });
</script>

