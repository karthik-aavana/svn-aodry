<form id="frm_add_vendor" name="frm_add_vendor" type="post">
<div id="add_vendor" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					&times;
				</button>
				<h4> Add Vendor</h4>
			</div>
			<div class="modal-body">
				<form id="vendorForm">
					<div class="row">
						<div class="form-group col-md-6">
							<label>Vendor Code</label>
							 <input type="hidden" name="ledger_id" id="ledger_id" value="0">
                              <input type="hidden" name="supplier_id" id="supplier_id" value="0">
							<input type="text" class="form-control" id="supplier_code" name="supplier_code" value="<?php echo $invoice_number; ?>" <?php if ($access_settings[0]->invoice_readonly == 'yes') { echo "readonly";}?>>
							<span class="validation-color" id="err_vendor_code"></span>
						</div>
						<div class="form-group col-md-6">
							<label for="vendor_type"> Customer Type<span class="validation-color">*</span> </label>
							<select class="form-control select2" id="vendor_type" name="vendor_type">
								<option value="">Select Type</option>
								<option value="individual">Individual</option>
                                <option value="company">Company</option>
                                <option value="private limited company">Private Limited Company</option>
                                <option value="proprietorship">Proprietorship</option>
                                <option value="partnership">Partnership</option>
                                <option value="one person company">One Person Company</option>
                                <option value="limited liability partnership">Limited Liability Partnership</option>
							</select>
							<span class="validation-color" id="err_vendor_type"><?php echo form_error('vendor_type'); ?></span>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-6">
							<label for="vendor_name"> Company/Individual Name<span class="validation-color">*</span> </label>
							<input type="text"  class="form-control" id="vendor_name" name="vendor_name" >
							 <span class="validation-color" id="err_vendor_name"><?php echo form_error('vendor_name'); ?></span>
						</div>
						<div class="form-group col-md-6">
							<label for="txt_contact_person">Contact Person Name<span class="validation-color">*</span> </label>
							<input type="text" class="form-control" name="txt_contact_person" id="txt_contact_person"/>
							<span class="validation-color" id="err_customer_name"><?php echo form_error('txt_contact_person'); ?></span>
						</div>
					</div>
					<div class="row">
					<div class="form-group col-md-6">
						<label for="cmb_country">Country<span class="validation-color">*</span></label>
						<select class="from-control select2" name="cmb_country" id="cmb_country">
							<option value="">Select Country</option>
							<?php 
							    foreach ($country as $row){
									echo "<option value='".$row->country_id."'>".$row->country_name."</option>";
							    }
							?>
						</select>
						<span class="validation-color" id="err_country"><?php echo form_error('cmb_country'); ?></span>
					</div>
					<div class="form-group col-md-6">
						<label for="cmb_state">State<span class="validation-color">*</span> </label>
						<select class="from-control select2" name="cmb_state" id="cmb_state">
							<option value="">Select State</option>
						</select>
						<span class="validation-color" id="err_state"><?php echo form_error('cmb_state'); ?></span>
					</div>
					<div class="form-group col-md-6">
						<label for="cmb_state">City<span class="validation-color">*</span></label>
						<select class="from-control select2"  name="cmb_city" id="cmb_city">
							<option value="">Select City</option>
						</select>
						<span class="validation-color" id="err_city"><?php echo form_error('cmb_state'); ?></span>
					</div>
					<div class="form-group col-md-6">
						<label for="address">Address</label>
						<textarea name="address" id="address" rows="2" cols="40" class="form-control"></textarea>
						<span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
					</div>
				</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="vendor_submit">
					Add
				</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
</form>
<script>
	
	 $('#cmb_country').change(function () {
        var id = $(this).val();
        $('#cmb_state').empty();
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: { id : id},
            dataType: "JSON",
            success: function (data) {
                console.log(data);
                 var state = $('#cmb_state');
                 var opt = document.createElement('option');
                    opt.text = 'Select State';
                    opt.value = '';
                    state.append(opt);                    
                for (i = 0; i < data.length; i++) {
                     $('#cmb_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                }
            }
        });
    });
    $('#cmb_state').change(function () {
        var id = $(this).val();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_city/'+id,
            dataType: "JSON",
            success: function (data) {
                console.log(data);
                 var city = $('#cmb_city');
                 var opt = document.createElement('option');
                    opt.text = 'Select City';
                    opt.value = '';
                    city.append(opt);                    
                for (i = 0; i < data.length; i++) {
                     $('#cmb_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
                }
            }
        });
    });
$("#vendor_submit").click(function() {
            var supplier_code = $('#supplier_code').val()?$('#supplier_code').val():"";
            var supplier_name = $('#vendor_name').val()?$('#vendor_name').val():"";
            var supplier_type = $('#vendor_type').val()?$('#vendor_type').val():"";
            var city = $('#cmb_city').val()?$('#cmb_city').val():"";
            var state = $('#cmb_state').val()?$('#cmb_state').val():"";
            var country = $('#cmb_country').val()?$('#cmb_country').val():"";
            var address = $('#address').val()?$('#address').val():"";
            var contact_person = $('#txt_contact_person').val()?$('#txt_contact_person').val():"";
            
            if (supplier_type == null || supplier_type == ""){
                $("#err_vendor_type").text("Please Enter Vendor Name.");
                return false;
            } else {
                $("#err_vendor_type").text("");
            }
            if (supplier_name == null || supplier_name == ""){
                $("#err_vendor_name").text("Please Enter Vendor Name.");
                return false;
            } else {
                $("#err_vendor_name").text("");
            }
            if (!supplier_name.match(name_regex)) {
                $('#err_vendor_name').text("Please Enter Valid Vendor Name ");
                return false;
            } else {
                $("#err_vendor_name").text("");
            }
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
                $("#err_city").text("Please Select City ");
                return false;
            } else {
                $("#err_city").text("");
            }
            if (address == null || address == ""){
                $("#err_address").text("Please Enter Address.");
                return false;
            } else {
               $("#err_address").text("");
            }
            if (contact_person == null || contact_person == ""){
                $("#err_customer_name").text("Please Enter Person Name.");
                return false;
            } else {
                $("#err_contact_person").text("");
            }
            if (!contact_person.match(name_regex)){
                $('#err_contact_person').text("Please Enter Valid Person Name ");
                return false;
            } else {
                $("#err_contact_person").text("");
            }
           var form_data = $('#frm_add_vendor').serializeArray();
           $.ajax({
                        url: base_url + 'supplier/add_supplier',
                        dataType: 'JSON',
                        method: 'POST',
                        data: form_data,
                        success: function (result){
                        	setTimeout(function () {
                                location.reload();
                            });
                        }
                           
                    });
        
    });
</script>
