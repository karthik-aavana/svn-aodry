<div id="edit_city" class="modal fade z_index_modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4>Edit City</h4>
			</div>
			<div class="modal-body">				 
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="hidden" name="city_id" id="city_id">
                            <label for="country_modal_edit">Country<span class="validation-color">*</span></label>
                              <select class="form-control country" id="country_modal_edit" name="country_modal_edit">
                                <option value="">Select Country</option>
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
                            <label for="state_modal_edit">Sate<span class="validation-color">*</span></label>
                           <select class="form-control state" id="state_modal_edit" name="state_modal_edit">
                                    <option value="">Select State</option>
                                </select>                            
                            <span class="validation-color" id="err_state_edit"><?php echo form_error('err_state_edit'); ?></span>
                        </div>
                    </div> 
                   </div>
                   <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group">
                            <label for="txt_city_edit">City<span class="validation-color">*</span></label>
                         <input type="text" class="form-control" id="txt_city_edit" name="txt_city_edit"/>
                            <span class="validation-color" id="err_city_edit"><?php echo form_error('err_city_edit'); ?></span>
                        </div>
                    </div>                   
                </div>  
			</div>			
			<div class="modal-footer">
                    <button type="submit" id="updateCity" class="btn btn-info">Update</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                </div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
	$(document).on("click", ".edit_city", function () {
        var id = $(this).data('id');
        $.ajax({url: base_url + 'location/get_city_modal/' + id,
                dataType: 'JSON',
                method: 'POST',
                success: function (result){
                   console.log(result);
                   $('#city_id').val(result[0].city_id);
                    $('#country_modal_edit').val(result[0].country_id).change();
                    $('#txt_city_edit').val(result[0].city_name);
                     $('#state_modal_edit').empty();
                   var country_id = result[0].country_id;
                   var state_id = result[0].state_id;
                   $.ajax({
                url: base_url + 'general/get_state/',
                method: "POST",
                data: { id : country_id},
                dataType: "JSON",
                success: function (data) {
                     var state = $('#state_modal_edit');
                     var opt = document.createElement('option');              
                    for (i = 0; i < data.length; i++) {
                        if(state_id == data[i].state_id){
                            $('#state_modal_edit').append('<option value="' + data[i].state_id + '" selected>' + data[i].state_name + '</option>');
                        }else{
                            $('#state_modal_edit').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                        }


                         
                    }

                }
            });
                }
            });
        
        
       // $('#state_modal_edit').val(state_id_modal).change();
    });

$('#country_modal_edit').change(function () {
            var id = $(this).val();
            $('#state_modal').empty();
            $.ajax({
                url: base_url + 'general/get_state/',
                method: "POST",
                data: { id : id},
                dataType: "JSON",
                success: function (data) {
                    console.log(data);
                     var state = $('#state_modal_edit');
                     var opt = document.createElement('option');
                        opt.text = 'Select State';
                        opt.value = '';
                        state.append(opt);                    
                    for (i = 0; i < data.length; i++) {
                         $('#state_modal_edit').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                    }
                }
            });
        });
    $("#updateCity").click(function() {       

            var country  = $('#country_modal_edit').val();
            var  state = $('#state_modal_edit').val();
            var city = $('#txt_city_edit').val();
            var city_id = $('#city_id').val();
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

            $.ajax({ url: base_url + 'location/update_city_modal',
                     dataType: 'JSON',
                     method: 'POST',
                     data: { 'country': country, 'state': state, 'city': city, 'id' : city_id },
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