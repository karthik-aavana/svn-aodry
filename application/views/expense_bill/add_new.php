<?php defined('BASEPATH') or exit('No direct script access allowed');
$this -> load -> view('layout/header');
?>
<div class="content-wrapper">
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo base_url('purchase'); ?>">Purchase</a></li>
            <li class="active">Add New Expense</li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add New Expense</h3>
                        <a class="btn btn-sm btn-default pull-right" href="<?php echo base_url('purchase'); ?>" title="Back">Back </a>
                    </div>
                    <div class="box-body">
                        <form role="form" id="form" method="post" action="<?php echo base_url('purchase/add_purchase'); ?>">
                            <div class="well">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date">Date<span class="validation-color">*</span></label>
                                            <input type="date" class="form-control" id="invoice_date" name="invoice_date" autocomplete="off">
                                            <span class="validation-color" id="err_date"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="invoice_number">Invoice No<span class="validation-color">*</span></label>
                                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" />
                                            <span class = "validation-color" id = "err_invoice_number"></span>
                                        </div>
                                    </div>
                                    <div class = "col-sm-3">
                                        <div class = "form-group">
                                            <label for = "supplier">Supplier <span class = "validation-color">*</span></label>
                                            <div class = "input-group date">
                                                <div class = "input-group-addon">
                                                    <a data-backdrop = "static" data-keyboard = "false" href = "#" data-toggle = "modal" data-target = "#supplier_modal" data-reference_type = "purchase" class = "open_supplier_modal pull-right">+</a>
                                                </div>
                                                <select class = "form-control select2" id = "supplier" name = "supplier">
                                                    <option value="">Select</option>
                                                    <option value="1">Supplier-1</option>
                                                    <option value="2">Supplier-2</option>
                                                </select>
                                            </div>
                                            <span class = "validation-color" id = "err_supplier"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        	<label>Nature of Supply<span class = "validation-color">*</span></label>
                                            <input type="text" class="form-control" id="nature_of_supply1" name="nature_of_supply1" value="Goods" readonly>
                                            <span class="validation-color" id="err_nature_supply"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="type_of_supply">Place of Supply <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="billing_state" name="billing_state">
                                                <option value="">Select</option>
                                                <?php
                                                foreach ($state as $key) {
                                                    ?>
                                                    <option value='<?php echo $key -> state_id; ?>' <?php
													if ($key -> state_id == $branch[0] -> branch_state_id) {
														echo "selected";
													}
                                                    ?>>
                                                                <?php echo $key -> state_name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="validation-color" id="err_billing_state"><?php echo form_error('billing_state'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="type_of_supply">Type of Supply <span class="validation-color">*</span></label>
                                            <select class="form-control select2" id="type_of_supply" name="type_of_supply" >
                                                <option value="regular" <?php echo "selected"; ?>>Regular</option>
                                                <option>Regular(Intra State)</option>
                                                <option>Regular(Interstate)</option>
                                                <option>Import</option>
                                            </select>
                                            <span class="validation-color" id="err_type_supply"></span>
                                        </div>
                                    </div>                                  
                                	<div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="gst_payable">GST Payable(Reverse Charge) <span class="validation-color">*</span></label>
                                            <br/>
                                            <label class="radio-inline">
                                                <input type="radio" name="gst_payable" value="yes" class="minimal" /> Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="gst_payable" value="no" class="minimal" checked="checked"/> No
                                            </label>
                                            <br/>
                                            <span class="validation-color" id="err_gst_payable"></span>
                                        </div>
                                    </div>                          
                                </div>			
							<div class="well">
								<div class="row">
									<div class="col-sm-12 mt-15"  id="add_inventory_item">
										<div class="input-group">
											<div class="input-group-addon">
												<a href="" data-toggle="modal" data-target="#item_modal" class="pull-left">+</a>
											</div>
											<input id="input_purchase_code" class="form-control" type="text" name="input_purchase_code" placeholder="Enter Product Name/Service Code" autocomplete="off">
										</div>
									</div>
									<div class="col-sm-12" id="item_note">
										<span class="validation-color">Please select the supplier to do purchase</span>
									</div>
								</div>
								<div class="row">
									<div class="box-header with-border">
										<h3 class="box-title">Inventory Items</h3>
									</div>
								</div>
							</div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <table class="table table-striped table-bordered table-condensed table-hover purchase_table table-responsive" id="purchase_data">
                                           <thead>
												<tr>
													<th><i class="fa fa-trash"></i></th>
													<th>Items</th>
													<th>Particulars</th>
													<th>Qty</th>
													<th>Price</th>
													<th>Discount <a href="" data-toggle="modal" data-target="#discount_modal"><strong>+</strong></a></th>
													<th>Taxable Value</th>
													<th>TDS/TCS (%)</th>
													<th>TAX (%) </th>
													<th>Total</th>
												</tr>
											</thead>
                                            <tbody id="purchase_table_body"></tbody>
                                        </table>
                                        <div class="row">
											<div class="box-header with-border others">
												<h3 class="box-title">Extra Charges Details</h3>
												<span class="pull-right-container"> <i class="glyphicon glyphicon-chevron-right pull-right" style="margin-top: 18px;    margin-right: 20px;"></i> </span>
											</div>
										</div>										
											<div class="well others-data mt-15">
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<label>Freight Charges(+)Tax</label>
															<div class="row">
																<div class="col-sm-6 pr-2">
																	<input type="text" step="0.01" class="form-control float_number" placeholder="Amount" name="freight_charge_amount" id="freight_charge_amount" value="">
																</div>
																<div class="col-sm-6 pl-2">
																	<select class="form-control select2" name="freight_charge_tax_select" id="freight_charge_tax_select">
																		<option value="">Select</option>
																		<option value="1-12.00">12.00 (%)</option>
																		<option value="2-14.00">14.00 (%)</option>
																		<option value="3-15.00">15.00 (%)</option>
																		<option value="4-16.00">16.00 (%)</option>
																		<option value="6-12.00">12.00 (%)</option>
																		<option value="8-5.00">5.00 (%)</option>
																	</select>
																	<span id="err_freight_charge_tax_amount" style="color: red;float: right;"></span>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label>Insurance Charges(+)Tax</label>
															<div class="row">
																<div class="col-sm-6 pr-2">
																	<input type="text" step="0.01" class="form-control float_number" placeholder="Amount" name="insurance_charge_amount" id="insurance_charge_amount" value="">
																</div>
																<div class="col-sm-6 pl-2">
																	<select class="form-control select2" name="insurance_charge_tax_select" id="insurance_charge_tax_select">
																		<option value="">Select</option>
																		<option value="1-12.00">12.00 (%)</option>
																		<option value="2-14.00">14.00 (%)</option>
																		<option value="3-15.00">15.00 (%)</option>
																		<option value="4-16.00">16.00 (%)</option>
																		<option value="6-12.00">12.00 (%)</option>
																		<option value="8-5.00">5.00 (%)</option>
																		<option value="44-18.00">18.00 (%)</option>
																		<option value="45-20.00">20.00 (%)</option>
																	</select>
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST Id" name="insurance_charge_tax_id" id="insurance_charge_tax_id" value="">
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST(in %)" name="insurance_charge_tax_percentage" id="insurance_charge_tax_percentage" value="">
																	<input type="hidden" class="form-control" name="insurance_charge_tax_amount" id="insurance_charge_tax_amount" value="">
																	<span id="err_insurance_charge_tax_amount" style="color: red;float: right;"></span>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label>Pack Forward Charges(+)Tax</label>
															<div class="row">
																<div class="col-sm-6 pr-2">
																	<input type="text" step="0.01" class="form-control float_number" placeholder="Amount" name="packing_charge_amount" id="packing_charge_amount" value="">
																</div>
																<div class="col-sm-6 pl-2">
																	<select class="form-control select2" name="packing_charge_tax_select" id="packing_charge_tax_select">
																		<option value="">Select</option>
																		<option value="1-12.00">12.00 (%)</option>
																		<option value="2-14.00">14.00 (%)</option>
																		<option value="3-15.00">15.00 (%)</option>
																		<option value="4-16.00">16.00 (%)</option>
																		<option value="6-12.00">12.00 (%)</option>
																		<option value="8-5.00">5.00 (%)</option>
																	</select>
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST Id" name="packing_charge_tax_id" id="packing_charge_tax_id" value="">
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST(in %)" name="packing_charge_tax_percentage" id="packing_charge_tax_percentage" value="">
																	<input type="hidden" class="form-control" name="packing_charge_tax_amount" id="packing_charge_tax_amount" value="">
																	<span id="err_packing_charge_tax_amount" style="color: red;float: right;"></span>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label>Incidental Charges(+)Tax</label>
															<div class="row">
																<div class="col-sm-6 pr-2">
																	<input type="text" step="0.01" class="form-control float_number" placeholder="Amount" name="incidental_charge_amount" id="incidental_charge_amount" value="">
																</div>
																<div class="col-sm-6 pl-2">
																	<select class="form-control select2" name="incidental_charge_tax_select" id="incidental_charge_tax_select">
																		<option value="">Select</option>
																		<option value="1-12.00">12.00 (%)</option>
																		<option value="2-14.00">14.00 (%)</option>
																		<option value="3-15.00">15.00 (%)</option>
																		<option value="4-16.00">16.00 (%)</option>
																	</select>
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST Id" name="incidental_charge_tax_id" id="incidental_charge_tax_id" value="">
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST(in %)" name="incidental_charge_tax_percentage" id="incidental_charge_tax_percentage" value="">
																	<input type="hidden" class="form-control" name="incidental_charge_tax_amount" id="incidental_charge_tax_amount" value="">
																	<span id="err_incidental_charge_tax_amount" style="color: red;float: right;"></span>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<label>Other(+)Tax</label>
															<div class="row">
																<div class="col-sm-6 pr-2">
																	<input type="text" step="0.01" class="form-control float_number" placeholder="Amount" name="inclusion_other_charge_amount" id="inclusion_other_charge_amount" value="">
																</div>
																<div class="col-sm-6 pl-2">
																	<select class="form-control select2" name="inclusion_other_charge_tax_select" id="inclusion_other_charge_tax_select">
																		<option value="">Select</option>
																		<option value="1-12.00">12.00 (%)</option>
																		<option value="2-14.00">14.00 (%)</option>
																		<option value="3-15.00">15.00 (%)</option>
																		<option value="4-16.00">16.00 (%)</option>
																		<option value="6-12.00">12.00 (%)</option>
																	</select>
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST Id" name="inclusion_other_charge_tax_id" id="inclusion_other_charge_tax_id" value="">
																	<input type="hidden" step="0.01" class="form-control float_number" placeholder="GST(in %)" name="inclusion_other_charge_tax_percentage" id="inclusion_other_charge_tax_percentage" value="">
																	<input type="hidden" class="form-control" name="inclusion_other_charge_tax_amount" id="inclusion_other_charge_tax_amount" value="">
																	<span id="err_inclusion_other_charge_tax_amount" style="color: red;float: right;"></span>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label>Other(-)Tax</label>
															<div class="row">
																<div class="col-sm-6 pr-2">
																	<input type="text" step="0.01" class="form-control float_number" placeholder="Amount" name="exclusion_other_charge_amount" id="exclusion_other_charge_amount" value="">
																</div>
																<div class="col-sm-6 pl-2">
																	<select class="form-control select2" name="exclusion_other_charge_tax_select" id="exclusion_other_charge_tax_select">
																		<option value="">Select</option>
																		<option value="1-12.00">12.00 (%)</option>
																		<option value="2-14.00">14.00 (%)</option>
																	</select>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>             
                                            <table id="table-total" class="table table-striped table-bordered table-condensed table-hover table-responsive">
                                            <tr>
                                                <td align="right" width="80%"><?php echo 'Subtotal'; ?> (+)</td>
                                                <td align='right'><span id="totalSubTotal">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><?php echo 'Discount'; ?> (-)</td>
                                                <td align='right'>
                                                    <span id="totalDiscountAmount">0.00</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right"><?php echo 'TDS Amount'; ?> (+-)</td>
                                                <td align='right'>
                                                    <span id="totalTdsAmount">0.00</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right"><?php echo 'TCS Amount'; ?> (+)</td>
                                                <td align='right'>
                                                    <span id="totalTcsAmount">0.00</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right"><?php echo 'Tax Amount'; ?> (+)</td>
                                                <td align='right'>
                                                    <span id="totalTaxAmount">0.00</span>
                                                </td>
                                            </tr>
                                            <!--
											<tr id="freight_charge_tr">
											<td align="right">Freight Charge (+)</td>
											<td align='right'>
											<span id="freight_charge">0.00</span>
											</td>
											</tr>
											<tr id="insurance_charge_tr">
											<td align="right">Insurance Charge (+)</td>
											<td align='right'>
											<span id="insurance_charge">0.00</span>
											</td>
											</tr>
											<tr id="packing_charge_tr">
											<td align="right">Packing & Forwarding Charge (+)</td>
											<td align='right'>
											<span id="packing_charge">0.00</span>
											</td>
											</tr>
											<tr id="incidental_charge_tr">
											<td align="right">Incidental Charge (+)</td>
											<td align='right'>
											<span id="incidental_charge">0.00</span>
											</td>
											</tr>
											<tr id="other_inclusive_charge_tr">
											<td align="right">Other Inclusive Charge (+)</td>
											<td align='right'>
											<span id="other_inclusive_charge">0.00</span>
											</td>
											</tr>
											<tr id="other_exclusive_charge_tr">
											<td align="right">Other Exclusive Charge (-)</td>
											<td align='right'>
											<span id="other_exclusive_charge">0.00</span>
											</td>
											</tr>  -->                                                                                          
                                            <tr>
                                                <td align="right"><?php echo 'Grand Total'; ?> (=)</td>
                                                <td align='right'><span id="totalGrandTotal">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td align="right">Round Off</td>
                                                <td align="right">
                                                        <label>
                                                            <input class="minimal" type="radio" name="round_off_key" value="yes">yes
                                                        </label>
                                                        <label>
                                                            <input class="minimal" type="radio" name="round_off_key" value="no" checked>No
                                                        </label>
                                                    </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" id="purchase_submit" name="submit" value="add" class="btn btn-info">Add</button>
                                        <button type="submit" id="purchase_pay_now" name="submit" value="pay_now" class="btn btn-success"> Pay Now </button>
                                        <span class="btn btn-default" id="purchase_cancel" onclick="cancel('purchase')">Cancel</span>
                                    </div>                                    
                                    <?php $this -> load -> view('sub_modules/notes_sub_module'); ?>                                    
                                </div>
                            </div>                           
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $this -> load -> view('layout/footer');
	$this -> load -> view('supplier/supplier_modal.php');
?>
<script src="<?php echo base_url('assets/js/purchase/'); ?>purchase.js"></script>
<script>
	$(document).ready(function() {
		$("#add_inventory_item").hide();
		$("#supplier").change(function() {
			var supplier = $(this).val();
			//console.log("Fdsfds" + supplier)
			if (supplier == "" || supplier == null) {
				$("#add_inventory_item").hide();
				$("#item_note").show();
			} else {
				$("#add_inventory_item").show();
				$("#item_note").hide();
			};
		});
		$('.transporter').click(function() {
			$('.transporter-data').slideToggle();
		}).trigger('click');

		$('.shipping').click(function() {
			$('.shipping-data').slideToggle();
		}).trigger('click');

		$('.others').click(function() {
			$('.others-data').slideToggle();
		}).trigger('click');
	})
</script>