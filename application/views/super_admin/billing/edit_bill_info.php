<div class="modal fade" id="edit_billing" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="se-pre-con" style="display: none;"></div>
			<div class="modal-header">
				<h5 class="modal-title">Edit Billing Information</h5>
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button> -->
			</div>
			<div class="modal-body">
				<form class="forms-sample">
					<div class="row">											
						<div class="col-sm-6">
							<div class="form-group">
								<label>Package<span class="validation-color">*</span></label>
								<select class="form-control custom_select2" name="package_name">
									<option value="">Select Package</option>
									<?php if(isset($payments)){
										foreach ($payments as $key => $value) {
											echo "<option value='{$value->Id}'>{$value->payment_method}</option>";
										}
									}?>								
								</select>								
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group mb-0">
								<label>Amount<span class="validation-color">*</span></label>
								<input type="text" class="form-control" name="amount" id="" maxlength="5">
							</div>
						</div>	
						<div class="col-sm-6">
							<div class="form-group">
								<label>Activation Date<span class="validation-color">*</span></label>
								<div class="input-group date datepicker">									
									<input type="text" class="form-control" name="activation_date">
									<span class="input-group-addon input-group-append"> <span class="mdi mdi-calendar input-group-text"></span> </span>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>End Date<span class="validation-color">*</span></label>
								<div class="input-group date datepicker">
									<input type="text" class="form-control" name="end_date">
									<span class="input-group-addon input-group-append"> <span class="mdi mdi-calendar input-group-text"></span> </span>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group mb-0">
								<label>Payment Status<span class="validation-color">*</span></label>
								<select class="form-control custom_select2" name="payment_status">
									<option value="">Select Payment</option>
									<option value="1">Paid</option>
									<option value="0">Unpaid</option>							
									<option value="2">Partially Paid</option>
									<option value="3">Failed</option>
								</select>								
							</div>
						</div>
						
					</div>					
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary tbl-btn update_bill_info">
					Update
				</button>
				<button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">
					Cancel
				</button>
				<button type="button" class="btn btn-primary tbl-btn" data-dismiss="modal">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(".datepicker").keypress(function(event) {event.preventDefault();});
	$(document).ready(function(){
	    $(document).on('click','.edit_bill',function(){
	      var id = $(this).attr('data-id');
	      var package_name = $(this).parent().find('[name=package_id]').val();
	      var activation_date = $(this).parent().find('[name=activation_date]').val();
	      var end_date = $(this).parent().find('[name=end_date]').val();
	      
	      var amount = $(this).parent().find('[name=amount]').val();
	      var firm_id = $(this).parent().find('[name=firm_id]').val();
	      var payment_status = $(this).parent().find('[name=payment_status]').val();
	      $('#edit_billing').find('[name=activation_date]').val(activation_date);
	      $('#edit_billing').find('[name=end_date]').val(end_date);
	      $('#edit_billing').find('[name=amount]').val(amount);
	      $('#edit_billing').find('[name=package_name]').val(package_name).prop('selected',true).change();
	      $('#edit_billing').find('[name=payment_status]').val(payment_status).prop('selected',true).change();
	      $('#edit_billing').find('.update_bill_info').attr('data-id',id);
	      $('#edit_billing').find('.update_bill_info').attr('firm_id',firm_id);
	      console.log(package_name);
	      $('#edit_billing').modal({
	        backdrop : 'static',
	        keyboard : false,
	        show : true
	      }); 
	    });

	    $(document).on('click','.update_bill_info',function(){
	      var id = $(this).data('id');
	      var firm_id = $('#edit_billing').find('[name=firm_id]').val();
	      var package_name = $('#edit_billing').find('[name=package_name]').val();
	      var activation_date = $('#edit_billing').find('[name=activation_date]').val();
	      var end_date = $('#edit_billing').find('[name=end_date]').val();
	      var eDate = new Date(end_date);
	      var sDate = new Date(activation_date);
	        
	      /*if(activation_date!= '' || sDate >= eDate) {
	          alert("Please ensure that the End Date is greater than the Start Date.");
	          return false;
	        }*/
	      var amount = $('#edit_billing').find('[name=amount]').val();
	      var payment_status = $('#edit_billing').find('[name=payment_status]').val();
	      
	      if(package_name == ''){
	        alert_d.text = "Please select package name!";
	        PNotify.error(alert_d);
	        return false;
	      }
	      if(amount == ''){
	        alert_d.text = "Please add amount!";
	        PNotify.error(alert_d);
	        return false;
	      }
	      if(payment_status == ''){
	        alert_d.text = "Please select payment status!";
	        PNotify.error(alert_d);
	        return false;
	      }
	      $('#edit_billing .se-pre-con').show();
	      $.ajax({
	        url:'<?=base_url();?>superadmin/Payment_methods/updateBillInfo',
	        type:'post',
	        dataType:'json',
	        data : {id:id,firm_id:firm_id,package:package_name,activation_date:activation_date,end_date:end_date,amount:amount,payment_status:payment_status},
	        success:function(j){
	          $('#edit_billing .se-pre-con').hide();
	          alert_d.text = j.msg;
	                      
	          if(j.flag){
	            PNotify.success(alert_d);
	            $('#edit_billing').modal('hide');
	            tbl.destroy();
	            tbl = GetBillingInfo();
	          }else{
	            PNotify.error(alert_d);
	          }
	        }
	      })
	    })
	  })
</script>