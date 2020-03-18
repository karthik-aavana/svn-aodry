<div id="add_over_head_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>
                    Add Over Head
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
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="raw_material_code">Name<span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" tabindex="1"  id="over_head_name" name="over_head_name" value="">
                                                    <span class="validation-color" id="err_over_head_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="raw_material_code">Unit<span class="validation-color">*</span></label>

                                                    <select class="form-control select2" id="over_head_unit" name="over_head_unit" style="width: 100%;" tabindex="4">
                                                        <option value="">Select</option>
                                                        <?php
                                                        foreach ($uqc as $value)
                                                        {
                                                            echo "<option value='$value->uom - $value->description'" . set_select('brand', $value->id) . ">$value->uom - $value->description</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <span class="validation-color" id="err_over_head_unit"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="raw_material_code">Cost per Unit <span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" tabindex="1"  id="cost_per_unit" name="cost_per_unit" value="">
                                                    <span class="validation-color" id="err_cost_per_unit"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="raw_material_code">Quantity <span class="validation-color">*</span></label>
                                                    <input type="text" class="form-control" tabindex="1"  id="over_head_quantity" name="over_head_quantity" value="">
                                                    <span class="validation-color" id="err_over_head_quantity"></span>
                                                </div>
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

