<div id="edit_ledger_modal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>
                    Edit Ledger
                </h4>
            </div>
            <form id="categoryForm_e">
                <div class="modal-body">               
                    <div class="row">                                   
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Sub Group Title<span class="validation-color">*</span></label>
                                <select class="form-control select2" id="sub_group_title_e" name="sub_group_title_e">
                                </select>
                                <span class="validation-color" id="err_sub_group_title_e"></span>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="ledger_id" id="ledger_id">
                                <label for="">Ledger Title <span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="ledger_title_e" name="ledger_title_e">
                                <span class="validation-color" id="err_ledger_title_e"></span>
                            </div>
                            <div class="form-group">
                                <label for="">Opening Balance<span class="validation-color">*</span></label>
                                <input type="input" class="form-control" name="opening_balance_e" id="opening_balance_e">
                                <span class="validation-color" id="err_opening_balance_e"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">    
                    <button type="submit" id="ledger_edit_submit" class="btn btn-info">Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).on("click", ".edit_ledger", function () {
        var id = $(this).data('id');
        $.ajax(
                {
                    url: base_url + 'account_sub_group/get_all_account_sub_group',
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (data)
                    {


                        var option = "<option value=''>Select</option>";
                        for (var i = 0; i < data.length; i++)
                        {
                            option += "<option value='" + data[i].account_subgroup_id + "'>" + data[i].subgroup_title + "</option>";
                        }
                        $('#sub_group_title_e').html(option);
                        $.ajax(
                                {
                                    url: base_url + 'ledger/get_ledger_data/' + id,
                                    dataType: 'JSON',
                                    method: 'POST',
                                    success: function (result)
                                    {
                                        $('#ledger_id').val(result[0].ledger_id);
                                        $('#ledger_title_e').val(result[0].ledger_title);
                                        $('#opening_balance_e').val(result[0].opening_balance);
                                        $('#sub_group_title_e').val(result[0].account_subgroup_id).change();
                                    }
                                });

                    }
                });


    });
    $(document).ready(function ()
    {
        $("#ledger_edit_submit").click(function (event) {
            var ledger_id = $('#ledger_id').val();
            var ledger_title_e = $('#ledger_title_e').val();
            var sub_group_title_e = $('#sub_group_title_e').val();
            var opening_balance_e = $('#opening_balance_e').val();
            var ledger_name_empty = "Please Enter Ledger Name.";
            var ledger_name_invalid = "Please Enter Valid Ledger Name";

            if (ledger_title_e == null || ledger_title_e == "")
            {
                $("#err_ledger_title_e").text(ledger_name_empty);
                return !1
            } else
            {
                $("#err_ledger_title_e").text("")
            }
            if (!ledger_title_e.match(general_regex))
            {
                $('#err_ledger_title_e').text(ledger_name_invalid);
                return !1
            } else
            {
                $("#err_ledger_title_e").text("")
            }


            if (sub_group_title_e == null || sub_group_title_e == "")
            {
                $("#err_sub_group_title_e").text("Please Select Sub Group Title");
                return !1
            } else
            {
                $("#err_sub_group_title_e").text("")
            }

            $.ajax(
                    {
                        url: base_url + 'ledger/edit_ledger_modal',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'id': ledger_id,
                                    'ledger_title': ledger_title_e,
                                    'sub_group_title': sub_group_title_e,
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
        $("#ledger_title_e").on("blur", function (event)
        {
            var ledger_title_e = $('#ledger_title_e').val();
            if (ledger_title_e == "")
            {
                $('#err_ledger_title_e').text("Please Enter Ledger title");
                return !1
            } else
            {
                $('#err_ledger_title_e').text("");
            }
        });
        $("#sub_group_title_e").change(function ()
        {
            var sub_group_title_e = $('#sub_group_title_e').val();
            if (sub_group_title_e == "")
            {
                $('#err_sub_group_title_e').text("Please Select Group Title.");
                return !1
            } else
            {
                $('#err_sub_group_title_e').text("");
            }
        });
    });
</script>

