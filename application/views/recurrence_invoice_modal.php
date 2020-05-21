<div class="modal fade" id="recurrence_invoice" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Generate recurrence Invoice</h4>
            </div>
            <!-- <form role="form" id="form" method="post" action="<?php echo base_url('receipt/pay_now'); ?>"> -->
            <div class="modal-body col-sm-12">
                <div class="form-group">
                    <label for="recurrence_date">Select Date <span class="validation-color">*</span></label>
                    <input type="hidden" name="invoice_id" id="invoice_id" value="">
                    <input type="hidden" name="invoice_type" id="invoice_type" value="">
                    <select multiple="" class="form-control select2" id="recurrence_date" name="recurrence_date[]" style="width: 42%;">
                        <!-- <option value="">Select</option>
                        <?php
                        foreach ($modules as $module)
                        {
                            ?>
                                    <option value='<?php echo $module->module_id ?>' <?php
                            ?>>
                            <?php echo $module->module_name; ?>
                                </option>
                            <?php
                        }
                        ?> -->
                    </select>
                    <span class="validation-color" id="err_recurrence_date"><?php echo form_error('recurrence_date'); ?></span>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="added_month_consider" id="added_month_consider" checked="checked" value="1"/> I want to regenerate this invoice from current month.
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generate_invoice">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on("click", '.recurrence_invoice', function (e) {
        var invoice_id = $(this).data('id');
        var invoice_type = $(this).data('type');
        $("#invoice_id").val(invoice_id);
        $("#invoice_type").val(invoice_type);
        $('#err_date').text('');
        $.ajax(
                {
                    url: base_url + 'recurrence/get_recurrence_ajax',
                    dataType: 'JSON',
                    method: 'POST',
                    data:
                            {
                                'invoice_id': invoice_id,
                                'invoice_type': invoice_type
                            },
                    success: function (result)
                    {
                        var arr = [];
                        for (var i = 0; i < result.length; i++)
                        {
                            arr[i] = result[i].recurrence_date;
                        }
                        var option = '';
                        for (var i = 1; i <= 31; i++)
                        {
                            var day = '';
                            if (i < 10)
                            {
                                day = '0' + i;
                                if (arr.indexOf(day.toString()) < 0)
                                {
                                    option += '<option value="0' + i + '">0' + i + '</option>';
                                }
                            } else
                            {
                                day = i;
                                if (arr.indexOf(day.toString()) < 0)
                                {
                                    option += '<option value="' + i + '">' + i + '</option>';
                                }
                            }
                        }
                        $('#recurrence_date').html(option);
                    }
                });
    });
    $(document).on("click", '#generate_invoice', function (e) {
        var recurrence_date = $('#recurrence_date').val();
        if (recurrence_date == null || recurrence_date == "")
        {
            $('#err_recurrence_date').text('Select the Recurrence date.');
            return false;
        } else
        {
            $('#err_recurrence_date').text('');
        }

        $.ajax(
                {
                    url: base_url + 'recurrence/add_recurrence_invoice',
                    dataType: 'JSON',
                    method: 'POST',
                    data:
                            {
                                'recurrence_date': $('#recurrence_date').val(),
                                'added_month_consider': $('#added_month_consider').val(),
                                'invoice_id': $('#invoice_id').val(),
                                'invoice_type': $('#invoice_type').val()
                            },
                    success: function (result)
                    {
                        setTimeout(function () {// wait for 5 secs(2)
                            location.reload(); // then reload the page.(3)
                        });
                    }
                });
    });
</script>
