<div id="add_city" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Add City</h4>
			</div>
			<div class="modal-body">				 
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                        <label for="">Country<span class="validation-color">*</span></label>
                         <select class="form-control country" id="country_modal" name="country_modal">
                            <option value="">Select Country</option>
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
                            <label for="state_modal">Sate<span class="validation-color">*</span></label>
                                <select class="form-control state" id="state_modal" name="state_modal">
                                    <option value="">Select State</option>
                                </select>                            
                            <span class="validation-color" id="err_state"></span>
                        </div>
                    </div> 
                   </div>
                   <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group">
                            <label for="txt_city">City<span class="validation-color">*</span></label>
                            <input type="text" class="form-control" id="txt_city" name="txt_city"/>
                            <span class="validation-color" id="err_city"><?php echo form_error('err_city'); ?></span>
                        </div>
                    </div>                   
                </div>  
			</div>			
			<div class="modal-footer">
                <button type="submit" id="submitCity" class="btn btn-info">Add</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
    $('#country_modal').change(function () {
            var id = $(this).val();
            $('#state_modal').empty();
            $.ajax({
                url: base_url + 'general/get_state/',
                method: "POST",
                data: { id : id},
                dataType: "JSON",
                success: function (data) {
                    console.log(data);
                     var state = $('#state_modal');
                     var opt = document.createElement('option');
                        opt.text = 'Select State';
                        opt.value = '';
                        state.append(opt);                    
                    for (i = 0; i < data.length; i++) {
                         $('#state_modal').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                    }
                }
            });
        });

        $("#submitCity").click(function() {       

            var state = $('#state_modal').val();
            var country = $('#country_modal').val();
            var city = $('#txt_city').val();
            if (country == null || country == "") {
                $("#err_country").text("Please Select Country ");
                return false;
            } else {
                $("#err_country").text("");
            }

            if (state == null || state == "") {
                $("#err_state").text("Please Select State ");
                return false;
            } else {
                $("#err_state").text("");
            }

            if (city == null || city == "") {
                $("#err_city").text("Please Enter City ");
                return false;
            } else {
                $("#err_city").text("");
            }

            $.ajax({ url: base_url + 'location/addCity_modal',
                     dataType: 'JSON',
                     method: 'POST',
                     data: { 'country': country, 'state': state, 'city': city },
                        success: function (result){
                            if(result == 'duplicate'){
                                $('#err_city').text("City already exist");
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