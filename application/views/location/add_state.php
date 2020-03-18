<div id="add_state" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Add State</h4>
			</div>
			<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="country">Country<span class="validation-color">*</span></label>
								<select class="form-control country" id="country" name="country">
									<?php 
									    foreach ($country as $row){
    										echo "<option value='".$row->country_id."'>".$row->country_name."</option>";
									    }
									?>
								</select>
								<span class="validation-color" id="err_country"><?php echo form_error('err_country'); ?></span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="state">State<span class="validation-color">*</span></label>
								<input type="text" class="form-control" id="txt_state" name="txt_state"/>
								<span class="validation-color" id="err_state"><?php echo form_error('err_state'); ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="submitState" class="btn btn-info"> Add </button>
					<button type="button" class="btn btn-info" data-dismiss="modal"> Cancel	</button>
				</div>
		</div>
	</div>
</div>


		
<script type="text/javascript">
    $(document).ready(function (){
    	
        $("#submitState").click(function() {       

            var state = $('#txt_state').val();
			var country = $('#country').val();
            if (country == null || country == "") {
				$("#err_country").text("Please Select Country ");
				return false;
			} else {
				$("#err_country").text("");
			}

			if (state == null || state == "") {
				$("#err_state").text("Please Enter State ");
				return false;
			} else {
				$("#err_state").text("");
			}
            $.ajax({ url: base_url + 'location/addState_modal',
                     dataType: 'JSON',
                     method: 'POST',
                     data: { 'country': country, 'state': state },
                        success: function (result){
                        	if(result == 'duplicate'){
                        		$('#err_state').text("State already exist");
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
</script>