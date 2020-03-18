<div id="convert_currency_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Currency Conversion</h4>
            </div>
            <form role="form" id="convert_currency_form" method="post" action="">
                <div class="modal-body" >
                    <div class="row">
                        <div class="col-sm-5 pr-0">
                            <div class="form-group">
                                <input type="hidden" name="convert_currency_id" id="convert_currency_id">
                                <div class="form-group">
                                    <label for="convert_grand_total">Grand Total (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_CODE') ?>) <span class="validation-color">*</span> </label>
                                    <input type="text" class="form-control" id="convert_grand_total" name="convert_grand_total" value="" readonly="true">
                                    <span class="validation-color" id="err_convert_grand_total"></span>
                                </div>
                            </div>
                        </div>                   
                        <div class="col-sm-2 pl-0 pr-0 mt-30 text-center"><i class="fa fa-exchange" aria-hidden="true"></i></i></div>                        
                        <div class="col-sm-5 pl-0">
                            <div class="form-group">
                                <label for="">(<b><span class="other_code"></span></b>)</label>
                                <input type="text" class="form-control float_number" id="currency_converted_amount" name="currency_converted_amount" value="" readonly="true">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5 pr-0">
                            <div class="form-group">
                                <label for="branch_rate">Conversion Rate (<?= $this->session->userdata('SESS_DEFAULT_CURRENCY_CODE') ?>) <span class="validation-color">*</span> </label>
                                <input type="text" class="form-control float_number" id="branch_rate" name="branch_rate" value="1.00" readonly="true">
                                <span class="validation-color" id="err_convertion_rate"></span>
                                <!-- <span id="converted_currency_symbol" hidden="true"><?= $this->session->userdata('SESS_DEFAULT_CURRENCY_SYMBOL') ?></span> <span id="convert_amount"></span> -->
                                
                            </div>
                        </div>
                        <div class="col-sm-2 pl-0 pr-0 mt-30 text-center"><i class="fa fa-exchange" aria-hidden="true"></i></i></div>
                        <div class="col-sm-5 pl-0">
                            <div class="form-group">
                                <label for="convertion_rate">(<b><span class="other_code"></span></b>)</label>
                                <input type="text" class="form-control float_number" id="convertion_rate" name="convertion_rate" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="branch_rate">Conversion Date<span class="validation-color">*</span> </label>
                                <div class="input-group date">
                                    <input type="text" class="form-control datepicker" id="conversion_date" name="conversion_date" value="">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                                <span class="validation-color" id="err_convertion_date"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <span class="pull-left">Conversion Date : <span id='conversion_date'></span></span> -->
                    <button type="submit" class="btn btn-info" id="convert_currency_button" name="convert_currency_button">Convert</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="false_delete_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Records</h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="category_name">Sorry! you can't delete this record because its associated with some other records.</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
<div id="false_edit_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Records</h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="category_name">Sorry! you can't edit this record because its is a default ledger.</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
<div id="default_data_delete_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Records</h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="category_name">Sorry! you can't delete this record because its is a default ledger.</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
<div id="delete_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Records</h4>
            </div>
            <form role="form" id="delete_form" method="post" action="">
                <div class="modal-body" >
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="hidden" name="delete_id" id="delete_id" >
                                <input type="hidden" name="delete_redirect" id="delete_redirect">
                                <label for="delete_message" id="delete_message"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" id="delete_button" name="delete_button">Yes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on("click", '.delete_button', function (e) {
        var id = $(this).data('id');
        var path = $(this).data('path');
        var redirect = $(this).data('return');
        var delete_message = $(this).data('delete_message');
        if (id != "" && path != "")
        {
            $("#delete_id").val(id);
            $("#delete_redirect").val(redirect);
            if (delete_message != "" && delete_message != null)
            {
                $("#delete_message").text(delete_message);
            } else
            {
                $("#delete_message").text("Are You Sure To Remove This Record ?");
            }
            $('#delete_form').attr('action', base_url + path);
        } else
        {
            location.reload();
        }
    });

    $(document).on("click", '.convert_currency', function (e) {
        var id = $(this).data('id');
        var convert_grand_total = $(this).data('grand_total');
        var rate = $(this).data('rate');
        var conversion_date = $(this).data('conversion_date');
        if(!rate) rate= 1;
        var currency_converted_amount = convert_grand_total * rate;
        var currency_code = $(this).data('currency_code');
        var path = $(this).data('path');
        if (id != "" && path != ""){
            $("#convert_currency_id").val(id);
            $("#convert_grand_total").val(convert_grand_total);
            $('#currency_converted_amount').val(currency_converted_amount.toFixed(2));
            $("#convertion_rate").val(rate);
            $('#conversion_date').val(conversion_date);
            $(".other_code").text(currency_code);
            $('#convert_currency_form').attr('action', base_url + path);
        } else{
            location.reload();
        }
    });

    $(document).ready(function (){
        $("#convert_currency_button").click(function (event)
        {
            if ($('#convert_grand_total').val() == "")
            {
                $('#err_convert_grand_total').text("Convertion Grand Total shouldn't be empty.");
                $('#convert_grand_total').focus();
                return !1
            } else
            {
                $('#err_convert_grand_total').text("");
            }
            if ($('#convertion_rate').val() > 0)
            {
                $('#err_convertion_rate').text("");
            } else
            {
                $('#err_convertion_rate').text("Please Enter Convertion Rate");
                $('#convert_amount').text("");
                $('#currency_converted_amount').val("0.00");
                $('#converted_currency_symbol').hide();
                $('#convertion_rate').focus();
                return !1
            }

            $('#err_convertion_date').text("");
            if($('#conversion_date').val() == ''){
                $('#err_convertion_date').text("Select conversion date");
                return !1
            }
        });

        $("#convertion_rate").on("change blur keyup", function (event)
        {
            var convert_grand_total = $('#convert_grand_total').val();
            var convertion_rate = $('#convertion_rate').val();
            if (convertion_rate > 0){
                var convert_amount = convert_grand_total * convertion_rate;
                $('#convert_amount').text(convert_amount.toFixed(2));
                $('#currency_converted_amount').val(convert_amount.toFixed(2));
                $('#err_convertion_rate').text("");
                $('#converted_currency_symbol').show();
            } else {
                $('#convert_amount').text("");
                $('#currency_converted_amount').val("0.00");
                $('#converted_currency_symbol').hide();
            }
        });
    });
</script>
