<div id="add_country" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Add Country</h4>
			</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="country">Country<span class="validation-color">*</span></label>
								<input type="text" class="form-control" name="country" id="country"/>
								<span class="validation-color" id="err_country"><?php echo form_error('err_country'); ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="submitCountry"  class="btn btn-info" >Add</button>
					<button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
				</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var country_name_exist = 0;
		$("#submitCountry").click(function(event) {

			var country = $('#country').val();

			if (country == null || country == "") {
				$("#err_country").text("Please Enter Country ");
				return false;
			} else {
				$("#err_country").text("");
			}

			if (country_name_exist > 0) {
				$('#err_country').text("The country name is already exist.");
				return false;
			} else {
				$("#err_country").text("");
			}

			$.ajax({
				url : base_url + 'location/addCountry_modal',
				dataType : 'JSON',
				method : 'POST',
				data : {
					'country' : country
				},
				success : function(result) {
					 if(result == 'duplicate'){
                            $('#err_country').text("Country already exist");
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

	/*$("#country").on("blur", function(event) {
		var countryId = $('#countryId').val();
		var country = $('#country').val();
		if (country.length > 1) {
			if (country == null || country == "") {
				$("#err_country").text("Please Enter Country.");
				return !1
			} else {
				$("#err_country").text("")
			}

		}

		$.ajax({
			url : base_url + 'location/getCountryName',
			type : "POST",
			dataType : "json",
			data : {
				country : country,
				countryId : countryId

			},
			success : function(data) {
				if (data[0].num_country_name > 0) {
					$('#err_country').text("The country name is already exist.");
					// $('#tax_value').val('');
					country_name_exist = 1;
				} else {
					$('#err_country').text("");
					country_name_exist = 0;
				}
			}
		});
	}); */
</script>
