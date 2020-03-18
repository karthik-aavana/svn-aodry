<div id="edit_state" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Edit State</h4>
			</div>	
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<input type="hidden" name="state_id" id="state_id">
								<label for="country_edit">Country<span class="validation-color">*</span></label>
								<select class="form-control country" id="country_edit" name="country_edit">
									<?php foreach ($country as $row){
    										echo "<option value='".$row->country_id."'>".$row->country_name."</option>";
											}
									?>
								</select>
								<span class="validation-color" id="err_country_edit"><?php echo form_error('err_country_edit'); ?></span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="state_edit">State<span class="validation-color">*</span></label>
								<input type="text" class="form-control" id="state_edit" name="state_edit" value=""/>
								<span class="validation-color" id="err_state_edit"><?php echo form_error('err_state_edit'); ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="updateState" class="btn btn-info">Update</button>
					<button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
				</div>
		</div>
	</div>
</div>


		
<script type="text/javascript">
    $(document).ready(function (){
    	
        $("#updateState").click(function() {       

            var state = $('#state_edit').val();
			var country = $('#country_edit').val();
			var state_id = $('#state_id').val();

            if (country == null || country == "") {
				$("#err_country_edit").text("Please Select Country ");
				return false;
			} else {
				$("#err_country_edit").text("");
			}

			if (state == null || state == "") {
				$("#err_state_edit").text("Please Enter State ");
				return false;
			} else {
				$("#err_state_edit").text("");
			}
             $.ajax({ url: base_url + 'location/update_state_modal',
                 dataType: 'JSON',
                 method: 'POST',
                data:{'state': state, 'country': country, 'id': state_id },
                success: function (result) {
                    if(result == 'duplicate'){
                        $('#err_state_edit').text("State already exist");
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


    $(document).on("click", ".edit_state", function () {
        var id = $(this).data('id');
        $.ajax({url: base_url + 'location/get_state_modal/' + id,
                dataType: 'JSON',
                method: 'POST',
                success: function (result){
                    $('#state_id').val(result[0].state_id);
                    $('#country_edit').val(result[0].country_id).change();
                    $('#state_edit').val(result[0].state_name);
                }
            });
    });

</script>