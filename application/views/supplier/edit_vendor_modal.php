<form id="frm_edit_vendor" name="frm_edit_vendor" type="post">

<div id="edit_vendor" class="modal fade" role="dialog">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal">

					&times;

				</button>

				<h4> Add Vendor </h4>

			</div>

			<div class="modal-body">

				<form id="vendorForm">

					<div class="row">

						<div class="form-group col-md-6">

							<label>Vendor Code</label>

							 <input type="hidden" name="ledger_id_edit" id="ledger_id_edit" >

                             <input type="hidden" name="supplier_id_edit" id="supplier_id_edit">

							<input type="text" class="form-control" id="supplier_code_edit" name="supplier_code_edit" readonly="readonly">

							<span class="validation-color" id="err_vendor_code_edit"></span>

						</div>

						<div class="form-group col-md-6">

							<label for="vendor_type"> Customer Type<span class="validation-color">*</span> </label>

							<select class="form-control select2" id="vendor_type_edit" name="vendor_type_edit">

								<option value="">Select Type</option>

								<option value="individual">Individual</option>

                                <option value="company">Company</option>
                                <option value="private limited company">Private Limited Company</option>
                                <option value="proprietorship">Proprietorship</option>
                                <option value="partnership">Partnership</option>
                                <option value="one person company">One Person Company</option>
                                <option value="limited liability partnership">Limited Liability Partnership</option>

							</select>

							<span class="validation-color" id="err_vendor_type_edit"><?php echo form_error('vendor_type'); ?></span>

						</div>

					</div>

					<div class="row">

						<div class="form-group col-md-6">

							<label for="vendor_name_edit"> Company/Individual Name<span class="validation-color">*</span> </label>

							<input type="text"  class="form-control" id="vendor_name_edit" name="vendor_name_edit" >

							 <span class="validation-color" id="err_vendor_name_edit"><?php echo form_error('vendor_name_edit'); ?></span>

						</div>

						<div class="form-group col-md-6">

							<label for="txt_contact_person_edit">Contact Person Name<span class="validation-color">*</span> </label>

							<input type="text" class="form-control" name="txt_contact_person_edit" id="txt_contact_person_edit"/>

							<span class="validation-color" id="err_customer_name_edit"><?php echo form_error('txt_contact_person_edit'); ?></span>

						</div>

					</div>

					<div class="row">

					<div class="form-group col-md-6">

						<label for="cmb_country_edit">Country<span class="validation-color">*</span></label>

						<select class="from-control select2" name="cmb_country_edit" id="cmb_country_edit">

							<option value="">Select Country</option>

							<?php 

							    foreach ($country as $row){

									echo "<option value='".$row->country_id."'>".$row->country_name."</option>";

							    }

							?>

						</select>

						<span class="validation-color" id="err_country_edit"><?php echo form_error('cmb_country_edit'); ?></span>

					</div>

					<div class="form-group col-md-6">

						<label for="cmb_state_edit">State<span class="validation-color">*</span> </label>

						<select class="from-control select2" name="cmb_state_edit" id="cmb_state_edit">

							<option value="">Select State</option>

						</select>

						<span class="validation-color" id="err_state_edit"><?php echo form_error('cmb_state_edit'); ?></span>

					</div>

					<div class="form-group col-md-6">

						<label for="cmb_city_edit">City<span class="validation-color">*</span></label>

						<select class="from-control select2"  name="cmb_city_edit" id="cmb_city_edit">

							<option value="">Select City</option>

						</select>

						<span class="validation-color" id="err_city_edit"><?php echo form_error('cmb_city_edit'); ?></span>

					</div>

					<div class="form-group col-md-6">

						<label for="address_edit">Address</label>

						<textarea name="address_edit" id="address_edit" rows="2" cols="40" class="form-control"></textarea>

						<span class="validation-color" id="err_address_edit"><?php echo form_error('address_edit'); ?></span>

					</div>

				</div>

				</form>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-primary" id="vendor_submit_edit"> Add </button>

				<button type="button" class="btn btn-default" data-dismiss="modal">

					Close

				</button>

			</div>

		</div>

	</div>

</div>

</form>

<script>

	 $(document).on("click", ".edit_vendor", function () {

        var id = $(this).data('id');

        $.ajax({

                url: base_url + 'supplier/edit_modal/' + id,

                dataType: 'JSON',

                method: 'POST',

                success: function (result) {

                	 var data = result['data'];

                	 $('#cmb_country_edit').val(data[0].supplier_country_id).change();

                	var data_state = result['state'];

                	var data_city = result['city'];

                	var state_id = data[0].supplier_state_id;

                	var city_id = data[0].supplier_city_id;

                	for (i = 0; i < data_state.length; i++) {

                        if(state_id == data_state[i].state_id){

                            $('#cmb_state_edit').append('<option value="' + data_state[i].state_id + '" selected>' + data_state[i].state_name + '</option>');

                        }else{

                            $('#cmb_state_edit').append('<option value="' + data_state[i].state_id + '">' + data_state[i].state_name + '</option>');

                        }  

                    }



                    for (i = 0; i < data_city.length; i++) {

                        if(city_id == data_city[i].city_id){

                            $('#cmb_city_edit').append('<option value="' + data_city[i].city_id + '" selected>' + data_city[i].city_name + '</option>');

                        }else{

                            $('#cmb_city_edit').append('<option value="' + data_city[i].city_id + '">' + data_city[i].city_name + '</option>');

                        }  

                    }



               

                	$('#ledger_id_edit').val(data[0].ledger_id);

                	$('#supplier_id_edit').val(data[0].supplier_id);

                	$('#supplier_code_edit').val(data[0].supplier_code);                	

                	//$('#cmb_state_edit').val(data[0].supplier_state_id).change();

                	//$('#cmb_city_edit').val(data[0].supplier_city_id).change();

                	$('#vendor_type_edit').val(data[0].supplier_type).change();

                	$('#txt_contact_person_edit').val(data[0].supplier_contact_person);

                	$('#vendor_name_edit').val(data[0].supplier_name);

                	$('#address_edit').val(data[0].supplier_address);

                }

        });

    });



	 $('#cmb_country_edit').change(function () {

        var id = $(this).val();

        $('#cmb_state_edit').empty();

        $.ajax({

            url: base_url + 'general/get_state/',

            method: "POST",

            data: { id : id},

            dataType: "JSON",

            success: function (data) {

                console.log(data);

                 var state = $('#cmb_state_edit');

                 var opt = document.createElement('option');

                    opt.text = 'Select State';

                    opt.value = '';

                    state.append(opt);                    

                for (i = 0; i < data.length; i++) {

                     $('#cmb_state_edit').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');

                }

            }

        });

    });



    $('#cmb_state_edit').change(function () {

        var id = $(this).val();

        $('#cmb_city_edit').empty();

        $.ajax({

            url: base_url + 'general/get_city/'+id,

            dataType: "JSON",

            success: function (data) {

                console.log(data);

                 var city = $('#cmb_city_edit');

                 var opt = document.createElement('option');

                    opt.text = 'Select City';

                    opt.value = '';

                    city.append(opt);                    

                for (i = 0; i < data.length; i++) {

                     $('#cmb_city_edit').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');

                }

            }

        });

    });



$("#vendor_submit_edit").click(function() {

            var supplier_code = $('#supplier_code_edit').val()?$('#supplier_code_edit').val():"";

            var supplier_name = $('#vendor_name_edit').val()?$('#vendor_name_edit').val():"";

            var supplier_type = $('#vendor_type_edit').val()?$('#vendor_type_edit').val():"";

            var city = $('#cmb_city_edit').val()?$('#cmb_city_edit').val():"";

            var state = $('#cmb_state_edit').val()?$('#cmb_state_edit').val():"";

            var country = $('#cmb_country_edit').val()?$('#cmb_country_edit').val():"";

            var address = $('#address_edit').val()?$('#address_edit').val():"";

            var contact_person = $('#txt_contact_person_edit').val()?$('#txt_contact_person_edit').val():"";

            

            if (supplier_type == null || supplier_type == ""){

                $("#err_vendor_type_edit").text("Please Enter Vendor Name.");

                return false;

            } else {

                $("#err_vendor_type_edit").text("");

            }



            if (supplier_name == null || supplier_name == ""){

                $("#err_vendor_name_edit").text("Please Enter Vendor Name.");

                return false;

            } else {

                $("#err_vendor_name_edit").text("");

            }



            if (!supplier_name.match(name_regex)) {

                $('#err_vendor_name_edit').text("Please Enter Valid Vendor Name ");

                return false;

            } else {

                $("#err_vendor_name_edit").text("");

            }



            if (country == null || country == "") {

                $("#err_country_edit").text("Please Select Country ");

                return false;

            } else {

                $("#err_country_edit").text("");

            }



            if (state == null || state == "") {

                $("#err_state_edit").text("Please Select State ");

                return false;

            } else {

                $("#err_state_edit").text("");

            }





            if (city == null || city == "") {

                $("#err_city_edit").text("Please Select City ");

                return false;

            } else {

                $("#err_city_edit").text("");

            }



            if (address == null || address == ""){

                $("#err_address_edit").text("Please Enter Address.");

                return false;

            } else {

               $("#err_address_edit").text("");

            }



            if (contact_person == null || contact_person == ""){

                $("#err_customer_name_edit").text("Please Enter Person Name.");

                return false;

            } else {

                $("#err_customer_name_edit").text("");

            }



            if (!contact_person.match(name_regex)){

                $('#err_customer_name_edit').text("Please Enter Valid Person Name ");

                return false;

            } else {

                $("#err_customer_name_edit").text("");

            }



           var form_data = $('#frm_edit_vendor').serializeArray();

           $.ajax({

                        url: base_url + 'supplier/edit_supplier',

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

