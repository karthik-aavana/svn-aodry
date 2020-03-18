<div id="edit_country" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Edit Country</h4>
			</div>
				<div class="modal-body">
					<input type="hidden" name="country_id" id="country_id">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="country_edit">Country<span class="validation-color">*</span></label>
								<input type="text" class="form-control" name="country_edit" id="country_edit"/>
								<span class="validation-color" id="err_country_edit"><?php echo form_error('err_country_edit'); ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="updateCountry"  class="btn btn-info">Update</button>
					<button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
				</div>			
		</div>
	</div>
</div>

<script type="text/javascript">



$(document).ready(function () {
        $("#updateCountry").click(function (event) {
        var country_name = $('#country_edit').val();
        var country_id = $('#country_id').val();

			if (country_name == null || country_name == "") {
				$("#err_country_edit").text("Please Enter Country ");
				return false;
			} else {
				$("#err_country_edit").text("");
			}
			
        $.ajax({ url: base_url + 'location/update_country_modal',
                 dataType: 'JSON',
                 method: 'POST',
                data:{'country_name': country_name, 'id': country_id },
                success: function (result) {
                    if(result == 'duplicate'){
                        $('#err_country_edit').text("Country already exist");
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


	

	$(document).on("click", ".edit_country", function () {
        var id = $(this).data('id');
        $.ajax(
                {
                    url: base_url + 'location/get_country_modal/' + id,
                    dataType: 'JSON',
                    method: 'POST',
                    success: function (result)
                    {
                        $('#country_id').val(result[0].country_id);
                        $('#country_edit').val(result[0].country_name);
                    }
                });
    });
</script>
