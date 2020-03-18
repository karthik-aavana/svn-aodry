<div id="split_statement" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Split Statement</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>How many split you want for <op id="t_type"></op> Amount : <op id="amount"></op></label>
                    <div class="form-group">
                        <select class="form-control" id="split_select" name="split_select">
                            <option value="">Select</option>
                            <?php
                            for ($i = 2; $i <= 10; $i++)
                            {
                                ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>

                        <input type="hidden" id="type" name="type" value="">
                        <input type="hidden" id="id" name="id" value="">
                        <input type="hidden" id="sid" name="sid" value="">

                    </div>
                </div>
                <form id="split_form" method="POST">
                    <div class="form-group">
                        <div id="split_text">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="split_submit" type="button" class="btn btn-primary" data-dismiss="modal">Split</button>
            </div>
        </div>

    </div>
</div>

<div id="merge_statement" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Merge Statement</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <!-- <label>Are you sure! You want to merge this statement again:</label> -->
                    <label>If you merge this statement then its categorized substatement also be merge as being uncategorized, if exists. </label>

                    <label>Do you want to continue ? </label>

                    <input type="hidden" id="sid" name="sid" value="">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button id="merge_submit" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<div id="add_comment" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Add Comment</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="comment">Comment/Narration</label>
                    <textarea type="text" class="form-control" id="comment" rows="4" name="comment"></textarea>
                </div>
                <input type="hidden" name="substatement_id" id="substatement_id" value="" />
                <input type="hidden" name="statement_id" id="statement_id" value="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button id="comment_submit" type="button" class="btn btn-primary" data-dismiss="modal">Add</button>
            </div>
        </div>

    </div>
</div>

<div id="remove_categorized" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Remove Categorization</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Are you sure! You want to uncategorized this statement ?</label>

                    <input type="hidden" id="sid" name="sid" value="">
                    <input type="hidden" id="id" name="id" value="">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button id="remove_submit" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<div id="move_to_suspense" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Move to Suspense</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <!-- <label>Are you sure! You want to merge this statement again:</label> -->
                    <label>This statement will move to Suspense Data from the Raw Data. </label>

                    <label>Do you want to continue ? </label>

                    <input type="hidden" id="sid" name="sid" value="">
                    <input type="hidden" id="id" name="id" value="">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button id="suspense_submit" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<input type="button" id="open_delete_invoice" name="open_delete_invoice" data-toggle="modal" data-target="#delete_invoice" hidden="true">

<div id="delete_invoice" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Remove Invoices</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <!-- <label>Are you sure! You want to merge this statement again:</label> -->
                    <label>There is some invoice which doesn't contain any voucher.</label>
                    <br>
                    <label>Do you want to remove these invoices ? </label>
                    <br>
                    <label id="invoice_no"></label>

                    <input type="hidden" id="invoice_id" name="invoice_id" value="">
                    <input type="hidden" id="voucher_type" name="voucher_type" value="">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete_invoice2_no" class="btn btn-default" data-dismiss="modal">No</button>
                <button id="delete_invoice_submit" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<input type="button" id="open_delete_invoice2" name="open_delete_invoice2" data-toggle="modal" data-target="#delete_invoice2" hidden="true">

<div id="delete_invoice2" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title" style="float: left;">Remove Invoice</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <!-- <label>Are you sure! You want to merge this statement again:</label> -->
                    <label id="invoice_no2"></label>
                    <br>
                    <label>Do you want to remove this invoice ? </label>
                    <br>

                    <input type="hidden" id="invoice_id2" name="invoice_id2" value="">
                    <input type="hidden" id="voucher_type2" name="voucher_type2" value="">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete_invoice_no" class="btn btn-default" data-dismiss="modal">No</button>
                <button id="delete_invoice2_submit" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(".split_statement").click(function () {
            // $('#statement_id').val($(this).data('id'));
            var type = $(this).data('name');
            document.getElementById("amount").innerHTML = $(this).data('amount');
            document.getElementById("type").value = type;
            document.getElementById("id").value = $(this).data('id');
            document.getElementById("sid").value = $(this).data('sid');
            if (type == 'debit')
            {
                document.getElementById("t_type").innerHTML = "Withdrawal";
            } else if (type == 'credit')
            {
                document.getElementById("t_type").innerHTML = "Deposit";
            }
        });
    });

    $(function () {
        $(".merge_statement").click(function () {
            document.getElementById("sid").value = $(this).data('sid');
        });
    });

    $(function () {
        $(".add_comment").click(function () {

            document.getElementById("statement_id").value = $(this).data('sid');
            document.getElementById("substatement_id").value = $(this).data('id');
            document.getElementById("comment").value = $(this).data('comment');
        });
    });

    $(function () {
        $(".remove_categorized").click(function () {
            document.getElementById("sid").value = $(this).data('sid');
            document.getElementById("id").value = $(this).data('id');
        });
    });

    $(function () {
        $(".suspense_data").click(function () {
            document.getElementById("sid").value = $(this).data('sid');
            document.getElementById("id").value = $(this).data('id');
        });
    });

    $('#split_select').on('change', function () {
        var val = $('#split_select').val();
        var text = '';
        if (val != undefined)
        {
            for (var i = 1; i <= val; i++)
            {
                text += '<div class="form-group"><div style="float:left;margin-right:10px;">Amount ' + i + ' </div><label class="form-label"> <input class="form-control" type="number" name="split' + i + '" id="split' + i + '" placeholder="Amount"></label> </div>';
            }
            document.getElementById('split_text').innerHTML = text;
        } else
        {
            document.getElementById('split_text').innerHTML = text;
        }
    });


    $(document).ready(function () {

        $('#split_submit').click(function (event) {

            var val = $('#split_select').val();
            var num = /^[0-9]+$/;
            var k = 0;
            var sum = 0;
            $('.error').remove();
            if (val != undefined)
            {
                for (var i = 1; i <= val; i++)
                {
                    if ($('#split' + i).val() == "" || $('#split' + i).val() == "0")
                    {
                        $('#split' + i).after('<span style="color:red;" class="error"> Enter the Amount</span>');
                        k = 1;
                    } else if (!num.test($('#split' + i).val())) {
                        $('#split' + i).after('<span style="color:red;" class="error"> Amount should be in digit</span>');
                        k = 1;
                    } else
                    {
                        sum = (sum + parseInt($('#split' + i).val()));
                    }
                }

                if (sum > parseInt($('#amount').text()))
                {
                    $('#split_submit').before('<span style="color:red;float:left;" class="error"> Sum of Amount ' + sum + ' is greater than total amount.</span>');
                    k = 1;
                } else if (sum < parseInt($('#amount').text()))
                {
                    $('#split_submit').before('<span style="color:red;float:left;" class="error"> Sum of Amount ' + sum + ' is less than total amount.</span>');
                    k = 1;
                }
            }
            if (k == 1)
            {
                return false;
            }

            $.ajax({

                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/split_statement/') ?>",
                data:
                        {
                            id: $("#id").val(),
                            sid: $("#sid").val(),
                            type: $("#type").val(),
                            form: $("#split_form").serializeArray()
                        },
                success: function (data) {
                    document.getElementById('tbody_rawdata').innerHTML = data;

                    $(function () {
                        $(".split_statement").click(function () {
                            // $('#statement_id').val($(this).data('id'));
                            var type = $(this).data('name');
                            document.getElementById("amount").innerHTML = $(this).data('amount');
                            document.getElementById("type").value = type;
                            document.getElementById("id").value = $(this).data('id');
                            document.getElementById("sid").value = $(this).data('sid');
                            if (type == 'debit')
                            {
                                document.getElementById("t_type").innerHTML = "Withdrawal";
                            } else if (type == 'credit')
                            {
                                document.getElementById("t_type").innerHTML = "Deposit";
                            }
                        });
                    });

                    setTimeout(function () {// wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                    });
                }
            });
        });
    });

    $(document).ready(function () {

        $('#merge_submit').click(function (event) {
            $('#open_delete_invoice').hide();
            $.ajax({

                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/merge_statement/') ?>",
                data: {sid: $("#sid").val()},
                success: function (data) {
                    document.getElementById('tbody_rawdata').innerHTML = data[0];

                    if (data[2] != '')
                    {
                        document.getElementById('invoice_no').innerHTML = data[1];
                        $('#voucher_type').val(data[2]);
                        $('#invoice_id').val(data[3]);
                        $('#open_delete_invoice').click();
                    } else
                    {
                        setTimeout(function () {// wait for 5 secs(2)
                            location.reload(); // then reload the page.(3)
                        });
                    }
                    $(function () {
                        $(".merge_statement").click(function () {
                            document.getElementById("sid").value = $(this).data('sid');
                        });
                    });
                }
            });
        });
    });

    $(document).ready(function () {

        $('#remove_submit').click(function (event) {

            $.ajax({

                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/remove_categorized/') ?>",
                data: {sid: $("#sid").val(), id: $("#id").val()},
                success: function (data) {
                    document.getElementById('tbody_rawdata').innerHTML = data[0];
                    document.getElementById('tbody_categorized').innerHTML = data[1];
                    document.getElementById('tbody_suspense').innerHTML = data[2];

                    if (data[3] != '')
                    {
                        document.getElementById('invoice_no2').innerHTML = "The Invoice : " + data[3] + " doesn't contain any voucher.";
                        var type_id = data[4].split('_');
                        $('#voucher_type2').val(type_id[0]);
                        $('#invoice_id2').val(type_id[1]);
                        $('#open_delete_invoice2').click();
                    } else
                    {
                        setTimeout(function () {// wait for 5 secs(2)
                            location.reload(); // then reload the page.(3)
                        });
                    }

                    $(function () {
                        $(".remove_categorized").click(function () {
                            document.getElementById("sid").value = $(this).data('sid');
                            document.getElementById("id").value = $(this).data('id');
                        });
                    });
                }
            });
        });
    });

    $(document).ready(function () {

        $('#suspense_submit').click(function (event) {

            $.ajax({

                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/suspense_statement/') ?>",
                data: {sid: $("#sid").val(), id: $("#id").val()},
                success: function (data) {
                    document.getElementById('tbody_suspense').innerHTML = data;

                    $(function () {
                        $(".suspense_data").click(function () {
                            document.getElementById("sid").value = $(this).data('sid');
                            document.getElementById("id").value = $(this).data('id');
                        });
                    });

                    setTimeout(function () {// wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                    });
                }
            });
        });
    });

    $(document).ready(function () {
        $('#comment_submit').click(function (event) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/add_comment/') ?>",
                data: {sid: $("#statement_id").val(), id: $("#substatement_id").val(), comment: $("#comment").val()},
                success: function (data) {
                    setTimeout(function () {// wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                    });
                }
            });
        });
    });

    $(function () {
        $("#delete_invoice_no").click(function () {
            setTimeout(function () {// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
            });
        });
    });

    $(function () {
        $("#delete_invoice2_no").click(function () {
            setTimeout(function () {// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
            });
        });
    });

    $(document).ready(function () {
        $('#delete_invoice_submit').click(function (event) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/remove_invoices/') ?>",
                data: {vtype: $("#voucher_type").val(), id: $("#invoice_id").val()},
                success: function (data) {
                    setTimeout(function () {// wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                    });
                }
            });
        });
    });

    $(document).ready(function () {
        $('#delete_invoice2_submit').click(function (event) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "<?php echo base_url('bank_statement/remove_invoice/') ?>",
                data: {vtype: $("#voucher_type2").val(), id: $("#invoice_id2").val()},
                success: function (data) {
                    setTimeout(function () {// wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                    });
                }
            });
        });
    });
</script>
