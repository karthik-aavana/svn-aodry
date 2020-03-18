<div id="edit_account_sub_group_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>
                    Edit Account Sub Group
                </h4>
            </div>
            <div class="modal-body" >
                <div class="control-group">
                    <div class="controls">
                        <div class="tabbable">
                            <div class="box-body">
                                <div class="row">
                                    <form id="categoryForm_e">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="hidden" name="account_sub_group_id" id="account_sub_group_id">
                                                <label for="">Sub Group Title <span class="validation-color">*</span></label>
                                                <input type="text" class="form-control" id="sub_group_title_e" name="sub_group_title_e">
                                                <span class="validation-color" id="err_sub_group_title_e"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Group Title<span class="validation-color">*</span></label>
                                                <select class="form-control select2" id="group_title_e" name="group_title_e" style="width: 100%;">
                                                </select>
                                                <span class="validation-color" id="err_group_title_e"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Formula<span class="validation-color">*</span></label>
                                                <select name="formula_e" id="formula_e" class="select2 form-control" style="width: 100%;">
                                                    <option value="ob-dr+cr">OB-Dr+Cr</option>
                                                    <option value="ob+dr-cr">OB+Dr-Cr</option>
                                                </select>
                                                <span class="validation-color" id="err_formula_e"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Opening Balance<span class="validation-color">*</span></label>
                                                <input type="input" class="form-control" name="opening_balance_e" id="opening_balance_e">
                                                <span class="validation-color" id="err_opening_balance_e"></span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="sub_group_edit_submit" class="btn btn-info btn-flat" class="close" data-dismiss="modal">Update</button>
                <button type="type" class="btn btn-info btn-flat" class="close" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on("click", ".edit_account_sub_group", function () {
        var id = $(this).data('id');
        $.ajax(
                {
                    url: base_url + 'account_group/get_account_group',
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (data)
                    {

                        var option = "<option value=''>Select</option>";
                        for (var i = 0; i < data.length; i++)
                        {
                            option += "<option value='" + data[i].account_group_id + "'>" + data[i].account_group_title + "</option>";
                        }
                        $('#group_title_e').html(option);
                        $.ajax(
                                {
                                    url: base_url + 'account_sub_group/get_sub_group_data/' + id,
                                    dataType: 'JSON',
                                    method: 'POST',
                                    success: function (result)
                                    {
                                        console.log(result[0].formula);
                                        $('#account_sub_group_id').val(result[0].account_subgroup_id);
                                        $('#sub_group_title_e').val(result[0].subgroup_title);
                                        $('#opening_balance_e').val(result[0].opening_balance);
                                        $('#group_title_e').val(result[0].account_group_id).change();
                                        $('#formula_e').val(result[0].formula).change();
                                    }
                                });

                    }
                });
    });
    $(document).ready(function ()
    {
        $("#sub_group_edit_submit").click(function (event) {
            var account_sub_group_id = $('#account_sub_group_id').val();
            var group_title_e = $('#group_title_e').val();
            var sub_group_title_e = $('#sub_group_title_e').val();
            var opening_balance_e = $('#opening_balance_e').val();
            var formula = $('#formula_e').val();
            var sub_group_name_empty = "Please Enter the Sub group Name.";
            var sub_group_name_invalid = "Please Enter Valid Sub group Name";

            if (sub_group_title_e == null || sub_group_title_e == "")
            {
                $("#err_sub_group_title_e").text(sub_group_name_empty);
                return !1
            } else
            {
                $("#err_sub_group_title_e").text("")
            }
            if (!sub_group_title_e.match(general_regex))
            {
                $('#err_sub_group_title_e').text(sub_group_name_invalid);
                return !1
            } else
            {
                $("#err_sub_group_title_e").text("")
            }
            if (group_title_e == null || group_title_e == "")
            {
                $("#err_group_title_e").text("Please Select Group Title");
                return !1
            } else
            {
                $("#err_group_title_e").text("")
            }
            $.ajax(
                    {
                        url: base_url + 'account_sub_group/edit_sub_group_modal',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'id': account_sub_group_id,
                                    'group_title': group_title_e,
                                    'sub_group_title': sub_group_title_e,
                                    'formula': formula,
                                    'opening_balance': opening_balance_e
                                },
                        success: function (result)
                        {
                            setTimeout(function () {// wait for 5 secs(2)
                                location.reload(); // then reload the page.(3)
                            });
                        }
                    });
        });
        $("#sub_group_title_e").on("blur", function (event)
        {
            var sub_group_title_e = $('#sub_group_title_e').val();
            if (sub_group_title_e == "")
            {
                $('#err_sub_group_title_e').text("Please Enter Sub group title");
                return !1
            } else
            {
                $('#err_sub_group_title_e').text("");
            }
        });
        $("#formula_e").change(function ()
        {
            var formula_e = $('#formula_e').val();
            if (formula_e == "")
            {
                $('#err_formula_e').text("Please Select Formula");
                return !1
            } else
            {
                $('#err_formula_e').text("");
            }
        });
        $("#group_title_e").change(function ()
        {
            var group_title_e = $('#group_title_e').val();


            if (group_title_e == "")
            {
                $('#err_group_title_e').text("Please Select Group Title.");
                return !1
            } else
            {
                $('#err_group_title_e').text("");
            }

        });
    });
</script>
