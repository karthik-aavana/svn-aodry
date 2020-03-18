<div id="edit_bank_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Bank Account</h4>
            </div>
            <!-- <form id="bankform"> -->
            <form role="form" id="editBankform">
                <div class="modal-body">
                    <div id="loader_coco">
                        <h1 class="ml8">
                            <span class="letters-container">
                                <span class="letters letters-left">
                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                </span>
                            </span>
                            <span class="circle circle-white"></span>
                            <span class="circle circle-dark"></span>
                            <span class="circle circle-container">
                                <span class="circle circle-dark-dashed"></span>
                            </span>
                        </h1>
                    </div>                
                    <div class="row">                    
                            <div class="col-md-6 form-group">
                            	<input type="hidden" id='bank_account_id' name="bank_account_id" value="">
                                <label for="bank_name1">Account Holder Name<span class="validation-color">*</span></label>
                                <input type="hidden" name="edit_selection_type" id="edit_selection_type" value="">
                                <input type="text" class="form-control" id="edit_account_holder" name="edit_account_holder" >
                                <span class="validation-color" id="err_edit_account_holder"><?php echo form_error('account_holder'); ?></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="bank_name1">Bank Name<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="edit_bank_name1" name="edit_bank_name1" >
                                <span class="validation-color" id="err_edit_bank_name1"><?php echo form_error('bank_name'); ?></span>
                            </div>
                        </div>

                          <div class="row">  
                            <div class="col-md-6 form-group">
                                <label for="short_name">Reference Name<span class="validation-color">*</span></label>
                                <input type="hidden" class="form-control" id="edit_reference_status" name="edit_reference_status" value="0">
                                <input type="text" class="form-control" id="edit_short_name" name="edit_short_name" placeholder="Reference name for Bank">
                                <span class="validation-color" id="err_edit_short_name"><?php echo form_error('short_name'); ?></span>
                            </div>
                            <div class=" col-md-6 form-group">
                                <label for="account_number">Account Number<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="edit_account_number" name="edit_account_number" value="<?php echo set_value('account_number'); ?>">
                                <span class="validation-color" id="err_edit_account_number"><?php echo form_error('account_number'); ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="type">Account Type<span class="validation-color">*</span></label>
                                <select class="form-control select2" id="edit_type" name="edit_type" style="width: 100%;">
                                    <option value="">Select</option>
                                    <option value="Savings Account">Savings Account</option>
                                    <option value="Current Account">Current Account</option>
                                    <option value="OD Account">OD Account</option>
                                    <option value="Term Loan Account">Term Loan Account</option>
                                </select>
                                <span class="validation-color" id="err_edit_type"><?php echo form_error('type'); ?></span>
                            </div>
                            <!-- <div class="form-group">
                                <label for="balance">Opening Balance<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="balance" name="balance" value="<?php echo set_value('balance'); ?>">
                                <span class="validation-color" id="err_balance"><?php echo form_error('balance'); ?></span>
                            </div> -->                           
                            <div class="col-md-6 form-group">
                                <label for="address">Bank Branch</label>
                                <input type="text" class="form-control" id="edit_branch_name" name="edit_branch_name" value="<?php echo set_value('branch_name'); ?>">
                                <span class="validation-color" id="err_edit_branch_name"><?php echo form_error('branch_name'); ?></span>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="address">Bank IFSC</label>
                                <input type="text" class="form-control" id="edit_ifsc_code" name="edit_ifsc_code" value="<?php echo set_value('ifsc_code'); ?>">
                                <span class="validation-color" id="err_edit_ifsc_code"><?php echo form_error('ifsc_code'); ?></span>
                            </div>
                            <div class="col-md-6 form-group hide">
                                <label for="default">Default Account</label>
                                <select class="form-control select2" id="edit_default" name="edit_default" style="width: 100%;">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <span class="validation-color" id="err_edit_default"><?php echo form_error('default'); ?></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="hidden" class="form-control" id="edit_hidden_id1" name="edit_hidden_id1" value="0">
                                <input type="hidden" class="form-control" id="ledger_id_old" name="ledger_id_old" value="">
                                <input type="hidden" class="form-control" id="hidden_id" name="hidden_id" value="">
                                <input type="hidden" class="form-control" id="ledger_id" name="ledger_id" value="">
                                <label for="account_number">Ledger Title<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="edit_ledger_title" name="edit_ledger_title" value="" readonly="">
                                <input type="hidden" class="form-control" id="ledger_title_old" name="ledger_title_old" value="" readonly="">
                            </div>
                        </div>
                             <div class="row">
                             <div class="col-md-12 form-group">
                                <label for="address">Bank Address</label>
                                <textarea type="text" class="form-control" id="edit_bank_address" name="edit_bank_address" value="<?php echo set_value('address'); ?>"></textarea>
                                <span class="validation-color" id="err_edit_address"><?php echo form_error('address'); ?></span>
                            </div>
                        </div>
                    </div>
                
                <div class="modal-footer">
                    <button type="button" id="update_bank_account" class="btn btn-info" class="close">Update</button>
                    <button class="btn btn-default" id="cancel_edit_bank_account"  data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/js/') ?>icon-loader.js"></script>
<script type="text/javascript">
	$(document).on("click", ".edit_bank", function() {
		var id = $(this).data('id');
		$.ajax({
			url : base_url + 'bank_account/get_bank_modal/' + id,
			dataType : 'JSON',
			method : 'POST',
			success : function(result) {
                /*console.log(result);*/
			    /*console.log("bank_account_id: " + result.bank_account_id);*/
				/*console.log(result.data[0].bank_account_id);*/
				$('#edit_account_holder').val(result.data[0].account_holder);
				$('#edit_bank_address').val(result.data[0].bank_address);
				$('#edit_bank_name1').val(result.data[0].bank_name);
				$('#edit_short_name').val(result.data[0].short_name);
				$('#edit_account_number').val(result.data[0].account_no);
				$('#bank_account_id').val(result.data[0].bank_account_id);
				$('#ledger_id_old').val(result.data[0].ledger_id);
				$('#ledger_id').val(result.data[0].ledger_id);
				$('#ledger_title_old').val(result.data[0].ledger_name);
				$('#edit_branch_name').val(result.data[0].branch_name);
				$('#edit_ifsc_code').val(result.data[0].ifsc_code);
				$('#edit_ledger_title').val(result.data[0].ledger_title);
				$('#edit_type').val(result.data[0].account_type).change();
			}
		});
	});
</script>