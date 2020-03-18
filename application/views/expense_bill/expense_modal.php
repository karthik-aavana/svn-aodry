<div id="expense_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<form role="form" id="expenseCategory">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						&times;
					</button>
					<h4>Add Expense</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="group_title"><!-- Supplier Name --> Expense Title <span class="validation-color">*</span> </label>
								<input type="hidden" name="ledger_id" id="ledger_id" value="0">
								<input type="text" class="form-control" id="expense_name" name="expense_name" value="">
								<span class="validation-color" id="err_expense_name"></span>

							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="group_title"><!-- Supplier Name --> Expense TDS </label>

								<input type="text" class="form-control" id="expense_tds" name="expense_tds" value="">
								<span class="validation-color" id="err_expense_tds"></span>

							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="product_hsn_sac_code">Select Product Tax <span class="validation-color">*</span></label>
								<div class="input-group">
									<div class="input-group-addon">
										<a href="" data-toggle="modal" data-target="#tds_modal" class="pull-right"><span class="fa fa-eye"></span></a>
									</div>
									<input type="text" class="form-control" id="product_tds_code" name="product_tds_code" value="" tabindex="11" readonly>
									<input type="hidden" class="form-control" id="tds_id" name="tds_id" value="" tabindex="11">
								</div>
								<span class="validation-color" id="err_product_tds_code"></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="expense_submit" class="btn btn-info" class="close" data-dismiss="modal">
						Add
					</button>
					<button type="button" class="btn btn-info" class="close" data-dismiss="modal">
						Cancel
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>var expense_ajax = "yes";</script>
<script src="<?php echo base_url('assets/js/expense/') ?>expense.js"></script>

