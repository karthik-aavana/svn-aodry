
<div id="create_new_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="create_new">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="account_group">Account Group <span class="validation-color">*</span></label>
                                <select name="account_group" id="account_group" class="select2 form-control" style="width: 100%;">

                                </select>
                                <span class="validation-color" id="err_account_group"></span>
                            </div>

                            <div class="form-group">
                                <a href="" data-toggle="modal" data-target="#add_subgroup_modal" class="add_subgroup pull-right">+ Add</a>
                                <label for="account_sub_group">Account Sub Group<span class="validation-color">*</span></label>

                                <select name="account_sub_group" id="account_sub_group" class="select2 form-control" style="width: 100%;">

                                </select>
                                <input type="hidden" name="section" id="section" value="general_bill">
                                <span class="validation-color" id="err_account_sub_group"></span>
                            </div>
                            <div class="form-group">
                                <label for="title">Title <span class="validation-color">*</span></label>
                                <input type="hidden" name="ledger_id" id="title_ledger_id" value="0">
                                <input type="input" class="form-control" name="title" id="title">
                                <span class="validation-color" id="err_title"></span>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button id="add_submit" class="btn btn-info btn-flat center-block" data-dismiss="modal">Add</button>
            </div>
        </div>

    </div>
</div>

<?php
$this->load->view('account_sub_group/add_modal');
?>
<script>
    $(document).ready(function ()
    {
        $("#purpose_of_transaction").change(function (event)
        {

            var purpose_of_transaction = $('#purpose_of_transaction').val();
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
                            var option = "";

                            for (var i = 0; i < result.length; i++)
                            {


                                if (result[i].account_group_title == purpose_of_transaction)
                                {
                                    option += "<option value='" + result[i].account_group_id + "'>" + result[i].account_group_title + "</option>";
                                }
                            }
                            $('#account_group').html(option);

                            $.ajax(
                                    {

                                        url: base_url + 'account_sub_group/get_sub_group_data_ajax',
                                        dataType: 'JSON',
                                        method: 'POST',
                                        data: {
                                            "purpose": purpose_of_transaction

                                        },
                                        success: function (result)
                                        {
                                            var option = "<option value=''>Select</option>";
                                            for (var i = 0; i < result.length; i++)
                                            {
                                                option += "<option value='" + result[i].account_subgroup_id + "'>" + result[i].subgroup_title + "</option>";
                                            }
                                            $('#account_sub_group').html(option);
                                        }
                                    });
                        }
                    });

        });
    });
</script>

<script>

    $(document).ready(function ()
    {
        $("#add_submit").click(function (event)
        {
            var subcategory_name_empty = "Please Enter Subcategory.";
            var subcategory_name_invalid = "Please Enter Valid Subategory Name";
            var subcategory_name_length = "Please Enter Subcategory Name Minimun 3 Character";
            var category_select = "Please Select Category.";

            var account_group = $('#account_group').val();
            var account_sub_group = $('#account_sub_group').val();
            var title = $('#title').val();
            var purpose_of_transaction = $('#purpose_of_transaction').val();
            // if (category == "" || category == null)
            // {
            //     $('#err_category_a').text(category_select);
            //     return !1
            // }
            // else
            // {
            //     $('#err_category_a').text("")
            // }
            // $('#subcategory_name').val(subcategory_name);
            // if (subcategory_name == null || subcategory_name == "")
            // {
            //     $("#err_subcategory_name_a").text(subcategory_name_empty);
            //     return !1
            // }
            // else
            // {
            //     $("#err_subcategory_name_a").text("")
            // }
            // if (!subcategory_name.match(general_regex))
            // {
            //     $('#err_subcategory_name_a').text(subcategory_name_invalid);
            //     return !1
            // }
            // else
            // {
            //     $("#err_subcategory_name_a").text("")
            // }
            // if (subcategory_name.length < 3)
            // {
            //     $('#err_subcategory_name_a').text(subcategory_name_length);
            //     return !1
            // }
            // else
            // {
            //     $("#err_subcategory_name_a").text("")
            // }

            $.ajax(
                    {
                        url: base_url + 'ledger/add_ledger_general_bill',
                        dataType: 'JSON',
                        method: 'POST',
                        data:
                                {
                                    'account_group': account_group,
                                    'account_sub_group': account_sub_group,
                                    'ledger_title': title,
                                    'closing_balance': 0.00,
                                    'purpose_of_transaction': purpose_of_transaction
                                },
                        success: function (result)
                        {


                            var data = result['data'];
                            var ledger_data = result['ledger_data'];

                            $('#from_to').html('');
                            $('#from_to').append('<option value="">Select</option>');
                            for (i = 0; i < ledger_data.length; i++) {
                                $('#from_to').append('<option value="' + ledger_data[i].ledger_id + '">' + ledger_data[i].ledger_title + '</option>');
                            }
                            $('#from_to').val(result['id']).attr("selected", "selected");
                            $('#from_to').change();
                            $("#title").val('');

                        }

                    });


        });
        $("#title").on("blur", function (event)
        {

            var ledger_id = $('#title_ledger_id').val();
            var title = $('#title').val();

            $.ajax({
                url: base_url + 'ledger/get_check_ledger',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'ledger_name': title,
                    'ledger_id': ledger_id
                },
                success: function (result) {
                    console.log(result);
                    if (result[0].num > 0)
                    {
                        $('#title').val("");
                        $("#err_title").text(title + " name already exists!");
                    } else
                    {
                        $("#err_title").text("");
                    }
                }
            });
        });

        // $("#category_a").change(function ()
        //    {
        //       var category_a = $('#category_a').val();
        //      if(category_a=="")
        //      {
        //         $('#err_category_a').text("Please Select Category Name");
        //          return !1
        //      }
        //      else
        //      {
        //          $('#err_category_a').text("");
        //      }

        //    });
        // $("#subcategory_name_a").on("blur", function (event)
        // {
        //    var subcategory_name_a = $('#subcategory_name_a').val();


        //       if(subcategory_name_a=="")
        //      {
        //         $('#err_subcategory_name_a').text("Please Enter Subcategory.");
        //          return !1
        //      }
        //      else
        //      {
        //          $('#err_subcategory_name_a').text("");
        //      }


        // });


    });
</script>

<script type="text/javascript">
    $('#create_new_modal').on('show.bs.modal', function (e) {
        $(this).css("overflow-y", "auto");
    });
</script>
