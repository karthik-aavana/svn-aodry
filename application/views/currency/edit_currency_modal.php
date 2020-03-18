<!--Subcategory Modal -->
<div id="edit_currency" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4 class="modal-title">Edit Currency</h4>
			</div>
			<div class="modal-body">
                    <input type="hidden" name="currencyId" id="currencyId">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">Country<span class="validation-color">*</span></label>
								<select class="form-control country" id="cmb_country_edit" name="cmb_country_edit" style="width: 100%;">
									<?php 
                                        foreach ($country as $row){
                                            echo "<option value='".$row->country_id."'>".$row->country_name."</option>";
                                        }
                                    ?>
								</select>
								<span class="validation-color" id="err_country_edit"><?php echo form_error('err_country_edit'); ?></span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">Currency<span class="validation-color">*</span></label>
							    <input type="text" class="form-control" id="txt_currency_edit" name="txt_currency_edit" maxlength="40">
                                <span class="validation-color" id="err_currency_edit"><?php echo form_error('err_currency_edit'); ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="">ISO<span class="validation-color">*</span></label>
								<input type="text" class="form-control" id="txt_iso_edit" name="txt_iso_edit" maxlength="3">
                                <span class="validation-color" id="err_iso_edit"><?php echo form_error('err_iso_edit'); ?></span>
							</div>
						</div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="iso">Currency Symbol<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="currency_symbol_edit" name="currency_symbol_edit" maxlength="40">
                                <span class="validation-color" id="err_currency_symbol_edit"><?php echo form_error('err_currency_symbol_edit'); ?></span>
                            </div>
                        </div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" id="updateCurrency" class="btn btn-info">Update</button>
				<button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
    $("#updateCurrency").click(function() {
            var country = $('#cmb_country_edit').val();
            var currency = $('#txt_currency_edit').val();
            var iso = $('#txt_iso_edit').val();
            var symbol = $('#currency_symbol_edit').val();
            var currency_id = $('#currencyId').val();
            var name_regex = /^[-a-zA-Z\s]+$/;
            if (country == null || country == "") {
                $("#err_country_edit").text("Please Select Country ");
                return false;
            } else {
                $("#err_country_edit").text("");
            }
            if (currency == null || currency == "") {
                $("#err_currency_edit").text("Please Enter Currency ");
                return false;
            } else {
                $("#err_currency_edit").text("");
            }
            if (iso == null || iso == "") {
                $(" #err_iso_edit").text("Please Enter ISO ");
                return false;
            } else {
                $("#err_iso_edit").text("");
            }
            if (symbol == null || symbol == "") {
                $(" #err_currency_symbol_edit").text("Please Enter Currency Symbol");
                return false;
            } else {
                $("#err_currency_symbol_edit").text("");
            }
            if (!iso.match(name_regex)){
                $('#err_iso_edit').text("Please Enter Valid ISO.");
                return !1
            } else {
                $("#err_iso_edit").text("")
            }
            if (!currency.match(name_regex)){
                $('#err_currency_edit').text("Please Enter Valid Currency.");
                return !1
            } else {
                $("#err_currency_edit").text("")
            }
            $.ajax({
                    url: base_url + 'currency/updateCurrency_modal',
                    dataType: 'JSON',
                    method: 'POST',
                    data: { 'country': country, 'currency': currency, 'iso':iso, 'id' : currency_id, 'currency_symbol' : symbol },
                    success: function (result){
                        if(result == 'duplicate'){
                                $('#err_currency_edit').text("Currency already exist");
                                return !1
                            }else{
                                setTimeout(function () {
                                    location.reload();
                                });
                            }
                    }
            });
        }); 
    }); 
$(document).on("click", ".edit_currency", function () {
        var id = $(this).data('id');
        $.ajax({url: base_url + 'currency/get_currency_modal/' + id,
                dataType: 'JSON',
                method: 'POST',
                success: function (result){
                    $('#currencyId').val(result[0].currency_id);
                    $('#cmb_country_edit').val(result[0].country_id).change();
                    $('#txt_currency_edit').val(result[0].currency_name);
                    $('#txt_iso_edit').val(result[0].currency_code);
                    $('#currency_symbol_edit').val(result[0].currency_symbol);
                }
            });
    });
</script>