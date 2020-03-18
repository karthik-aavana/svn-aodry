<!--Subgroup  Modal -->

<div id="add_ledger_modal" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Add Ledger</h4>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="">Account Sub Group<span class="validation-color">*</span></label>

                            <select name="account_sub_group_a" id="account_sub_group_a" class="select2 form-control" style="width: 100%;">

                            </select>

                            <span class="validation-color" id="err_account_sub_group_a"></span>

                        </div>

                        <div class="form-group">

                            <label for="">Ledger Title<span class="validation-color">*</span></label>

                            <input type="input" class="form-control" name="ledger_title_a" id="ledger_title_a">

                            <span class="validation-color" id="err_ledger_title_a"></span>

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

                <button class="btn btn-info btn-flat push-right" data-dismiss="modal">Cancel</button>

                <button id="ledger_add_submit" class="btn btn-info btn-flat push-right" data-dismiss="modal">Add</button>

            </div>

        </div>

    </div>

</div>

<script>

    $(document).on("click", ".add_ledger", function () {

        $.ajax(

                {

                    url: base_url + 'account_sub_group/get_all_account_sub_group',

                    dataType: 'JSON',

                    method: 'POST',

                    success: function (result)

                    {

                        var option = "<option value=''>Select</option>";

                        for (var i = 0; i < result.length; i++)

                        {

                            option += "<option value='" + result[i].account_subgroup_id + "'>" + result[i].subgroup_title + "</option>";

                        }

                        $('#account_sub_group_a').html(option);

                    }

                });

    });

    $(document).ready(function ()

    {

        $("#ledger_add_submit").click(function (event)

        {

            var ledger_name_empty = "Please Enter ledger.";

            var ledger_name_invalid = "Please Enter Valid Ledger Name";

            var account_sub_group_select = "Please Select Account sub group.";

            var ledger_title = $('#ledger_title_a').val().trim();

            var account_sub_group = $('#account_sub_group_a').val();

            var opening_balance = $('#opening_balance_a').val();

            if (account_sub_group == "" || account_sub_group == null)

            {

                $('#err_account_sub_group_a').text(account_sub_group_select);

                return !1

            } else

            {

                $('#err_account_sub_group_a').text("")

            }

            $('#ledger_title_a').val(ledger_title);

            if (ledger_title == null || ledger_title == "")

            {

                $("#err_ledger_title_a").text(ledger_name_empty);

                return !1

            } else

            {

                $("#err_ledger_title_a").text("")

            }

            if (!ledger_title.match(general_regex))

            {

                $('#err_ledger_title_a').text(ledger_name_invalid);

                return !1

            } else

            {

                $("#err_ledger_title_a").text("")

            }

            $.ajax(

                    {

                        url: base_url + 'ledger/add_ledger_modal',

                        dataType: 'JSON',

                        method: 'POST',

                        data:

                                {

                                    'ledger_title': ledger_title,

                                    'account_sub_group': account_sub_group,

                                    'opening_balance': opening_balance

                                },

                        success: function (result)

                        {

                            setTimeout(function () {// wait for 5 secs(2)

                                location.reload(); // then reload the page.(3)

                            });

                        }

                    });

        });

        $("#account_sub_group_a").change(function ()

        {

            var account_sub_group_a = $('#account_sub_group_a').val();

            if (account_sub_group_a == "")

            {

                $('#err_account_sub_group_a').text("Please Select Account Sub Group Name");

                return !1

            } else

            {

                $('#err_account_sub_group_a').text("");

            }

        });

        $("#ledger_title_a").on("blur", function (event)

        {

            var ledger_title_a = $('#ledger_title_a').val();

            if (ledger_title_a == "")

            {

                $('#err_ledger_title_a').text("Please Enter Ledger.");

                return !1

            } else

            {

                $('#err_ledger_title_a').text("");

            }

        });

    });

</script>

