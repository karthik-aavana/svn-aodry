<!--Subgroup  Modal -->
<div id="add_subgroup_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Subgroup</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="">Account Group<span class="validation-color">*</span></label>
                            <select name="account_group_a" id="account_group_a" class="select2 form-control" style="width: 100%;">
                            </select>
                            <span class="validation-color" id="err_account_group_a"></span>
                        </div>
                        <div class="form-group">
                            <label for="">Account Sub Group<span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="account_sub_group_a" id="account_sub_group_a">
                            <span class="validation-color" id="err_account_sub_group_a"></span>
                        </div>
                        <div class="form-group">
                            <label for="">Formula<span class="validation-color">*</span></label>
                            <select name="formula_a" id="formula_a" class="select2 form-control" style="width: 100%;">
                                <option value="ob-dr+cr">OB-Dr+Cr</option>
                                <option value="ob+dr-cr">OB+Dr-Cr</option>
                            </select>
                            <span class="validation-color" id="err_formula_a"></span>
                        </div>
                        <div class="form-group">
                            <label for="">Opening Balance<span class="validation-color">*</span></label>
                            <input type="input" class="form-control" name="opening_balance_a" id="opening_balance_a">
                            <span class="validation-color" id="err_opening_balance_a"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="subgroup_submit" class="btn btn-info btn-flat" data-dismiss="modal">Add</button>
                <button type="button" class="btn btn-info btn-flat" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on("click", ".add_subgroup", function () {
        var purpose_of_transaction = $('#purpose_of_transaction').val();
        var section = $('#section').val();

        var type_of_transaction = $('#type_of_transaction').val();
        if (purpose_of_transaction == "Advance")
        {
            purpose_of_transaction = "Duties & Taxes"; //while createing new Tax paid,Account group should select as duties taxes for igst,cgst
        }
        if (purpose_of_transaction == "Cash")
        {
            purpose_of_transaction = "Cash-in-Hand";
        }
        $.ajax(
                {
                    url: base_url + 'account_group/get_account_group',
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (result)
                    {


                        if (section == "direct")
                        {

                            var option = "<option value=''>Select</option>";
                            for (var i = 0; i < result.length; i++)
                            {
                                option += "<option value='" + result[i].account_group_id + "'>" + result[i].account_group_title + "</option>";
                            }

                        } else if (section == "general_bill")
                        {
                            for (var i = 0; i < result.length; i++)
                            {
                                if (result[i].account_group_title == purpose_of_transaction)
                                {
                                    option = "<option value='" + result[i].account_group_id + "'>" + result[i].account_group_title + "</option>";
                                }
                            }
                        }
                        $('#account_group_a').html(option);

                    }
                });
    });
    $("#subgroup_submit").click(function (e)
    {
        e.preventDefault();

        var subgroup_name_empty = "Please Enter Subgroup.";
        var subgroup_name_invalid = "Please Enter Valid Subgroup Name";
        var account_group_select = "Please Select Account group.";

        var subgroup_name = $('#account_sub_group_a').val().trim();
        var account_group = $('#account_group_a').val();
        var opening_balance = $('#opening_balance_a').val();
        var section = $('#section').val();
        var purpose_of_transaction = $('#purpose_of_transaction').val();
        var formula = $('#formula_a').val();
        if (account_group == "" || account_group == null)
        {
            $('#err_account_group_a').text(category_select);
            return !1
        } else
        {
            $('#err_account_group_a').text("")
        }
        $('#account_sub_group_a').val(subgroup_name);
        if (subgroup_name == null || subgroup_name == "")
        {
            $("#err_account_sub_group_a").text(subgroup_name_empty);
            return !1
        } else
        {
            $("#err_account_sub_group_a").text("")
        }
        if (!subgroup_name.match(general_regex))
        {
            $('#err_account_sub_group_a').text(subcategory_name_invalid);
            return !1
        } else
        {
            $("#err_account_sub_group_a").text("")
        }

        $.ajax(
                {
                    url: base_url + 'account_sub_group/add_subgroup_ajax',
                    dataType: 'JSON',
                    method: 'POST',
                    data:
                            {
                                'subgroup_name': subgroup_name,
                                'account_group': account_group,
                                'opening_balance': opening_balance,
                                'formula': formula,
                                'purpose_of_transaction': purpose_of_transaction
                            },
                    success: function (result)
                    {

                        if (section == "direct")
                        {
                            console.log("ffd");
                            setTimeout(function () {// wait for 5 secs(2)
                                location.reload(); // then reload the page.(3)
                            });
                        } else if (section == "general_bill")
                        {

                            var sub_group_data = result['sub_group_data'];

                            $('#account_sub_group').html('');
                            $('#account_sub_group').append('<option value="">Select</option>');
                            for (i = 0; i < sub_group_data.length; i++) {
                                $('#account_sub_group').append('<option value="' + sub_group_data[i].account_subgroup_id + '">' + sub_group_data[i].subgroup_title + '</option>');
                            }
                            $('#account_sub_group').val(result['id']).attr("selected", "selected");

                            $("#account_sub_group_a").val('');
                        }

                    }
                });
    });
    $("#account_group_a").change(function ()
    {
        var account_group_a = $('#account_group_a').val();
        if (account_group_a == "")
        {
            $('#err_account_group_a').text("Please Select Account Group Name");
            return !1
        } else
        {
            $('#err_account_group_a').text("");
        }
    });
    $("#formula_a").change(function ()
    {
        var formula_a = $('#formula_a').val();
        if (formula_a == "")
        {
            $('#err_formula_a').text("Please Select Formula");
            return !1
        } else
        {
            $('#err_formula_a').text("");
        }
    });
    $("#account_sub_group_a").on("blur", function (event)
    {
        var account_sub_group_a = $('#account_sub_group_a').val();


        if (account_sub_group_a == "")
        {
            $('#err_account_sub_group_a').text("Please Enter Subgroup.");
            return !1
        } else
        {
            $('#err_account_sub_group_a').text("");
        }

    });
</script>
<script type="text/javascript">
    $('#add_subgroup_modal').on('show.bs.modal', function (e) {
        $(this).css("overflow-y", "auto");
    });
// $("#subgroup_submit").click(function(e){
//   e.preventDefault();
//   alert()
// })

</script>
