<!--Subcategory Modal --><div id="add_currency" class="modal fade" role="dialog">    <div class="modal-dialog">        <div class="modal-content">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal">                    &times;                </button>                <h4 class="modal-title">Add Currency</h4>            </div>            <div class="modal-body">                <form id="form">                    <div class="row">                        <div class="col-sm-6">                            <div class="form-group">                                <label for="">Country<span class="validation-color">*</span></label>                                <select class="form-control country select2" id="cmb_country" name="cmb_country" style="width: 100%;">                                    <option value="">Select Country</option>                                    <?php                                    foreach ($country as $row) {                                        echo "<option value='" . $row->country_id . "'>" . $row->country_name . "</option>";                                    }                                    ?>                                </select>                                <span class="validation-color" id="err_country"><?php echo form_error('err_country'); ?></span>                            </div>                        </div>                        <div class="col-sm-6">                            <div class="form-group">                                <label for="currency">Currency<span class="validation-color">*</span></label>                                <input type="text" class="form-control" id="txt_currency" name="txt_currency" maxlength="40" >                                <span class="validation-color" id="err_currency"><?php echo form_error('err_country'); ?></span>                            </div>                        </div>                    </div>                    <div class="row">                        <div class="col-sm-6">                            <div class="form-group">                                <label for="iso">ISO<span class="validation-color">*</span></label>                                <input type="text" class="form-control" id="txt_iso" name="txt_iso" maxlength="3">                                <span class="validation-color" id="err_iso"><?php echo form_error('err_iso'); ?></span>                            </div>                        </div>                        <div class="col-sm-6">                            <div class="form-group">                                <label for="iso">Currency Symbol<span class="validation-color">*</span></label>                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" maxlength="50">                                <span class="validation-color" id="err_currency_symbol"><?php echo form_error('err_currency_symbol'); ?></span>                            </div>                        </div>                    </div>                </form>            </div>            <div class="modal-footer">                <button type="submit" id="submitCurrency" class="btn btn-info">Add</button>                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>            </div>        </div>    </div></div><script>    $(document).ready(function () {        $("#submitCurrency").click(function () {            var country = $('#cmb_country').val();            var currency = $('#txt_currency').val();            var iso = $('#txt_iso').val();            var symbol = $('#currency_symbol').val();            var name_regex = /^[-a-zA-Z\s]+$/;            // var sname_regex = /^[A-Za-z][A-Za-z0-9]+$/;            if (country == null || country == "") {                $("#err_country").text("Please Select Country ");                return false;            } else {                $("#err_country").text("");            }            if (currency == null || currency == "") {                $("#err_currency").text("Please Enter Currency ");                return false;            } else {                $("#err_currency").text("");            }            if (iso == null || iso == "") {                $(" #err_iso").text("Please Enter ISO ");                return false;            } else {                $("#err_iso").text("");            }            if (symbol == null || symbol == "") {                $(" #err_currency_symbol").text("Please Enter Currency Symbol");                return false;            } else {                $("#err_currency_symbol").text("");            }            if (!iso.match(name_regex)) {                $('#err_iso').text("Please Enter Valid ISO.");                return !1            } else {                $("#err_iso").text("")            }            if (!currency.match(name_regex)) {                $('#err_currency').text("Please Enter Valid Currency.");                return !1            } else {                $("#err_currency").text("")            }            $.ajax({                url: base_url + 'currency/addCurrency_modal',                dataType: 'JSON',                method: 'POST',                data: {'country': country, 'currency': currency, 'iso': iso, 'symbol':symbol },                success: function (result) {                    if (result == 'duplicate') {                        $('#err_country').text("Combination of Currency And ISO already exist");                        return !1                    } else {                        setTimeout(function () {                            location.reload();                        });                    }                }            });        });    });</script>