<?php defined('BASEPATH') OR exit('No direct script access allowed');
$this -> load -> view('layout/header');
?>
<!-- Content Wrapper. Contains page content -->
<form role="form" id="form" method="post" action="<?php echo base_url('supplier/edit_supplier'); ?>">
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header"></section>
	<section class="content mt-50">
		<div class="row">
			<!-- right column -->
			<div class="col-md-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Vendor </h3>
						<a href="javascript:void(0);" class="btn btn-sm btn-info pull-right add_button">Add Field</a>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
							<div class="row">
								<div class="form-group col-md-4">
									<label>Vendor Code</label>
									 <input type="hidden" name="ledger_id_edit" id="ledger_id_edit" value="<?php echo $data[0]->ledger_id; ?>" >
		                             <input type="hidden" name="supplier_id_edit" id="supplier_id_edit" value="<?php echo $data[0]->supplier_id; ?>">
									<input type="text" class="form-control" id="supplier_code_edit" name="supplier_code_edit" value="<?php echo $data[0]->supplier_code; ?>" >
									<span class="validation-color" id="err_vendor_code_edit"></span>
								</div>
								<div class="form-group col-md-4">
									<label for="vendor_type"> Supplier Type<span class="validation-color">*</span> </label>
									<select class="form-control select2" id="vendor_type_edit" name="vendor_type_edit">
										<option value="">Select Type</option>
										<option value="individual" <?php
                                                    if ($data[0]->supplier_type == 'individual')
                                                    {
                                                        echo "selected";
                                                    }
                                                    ?>>Firm</option>
                                            <option value="company" <?php
                                            if ($data[0]->supplier_type == 'company')
                                            {
                                                echo "selected";
                                            }
                                                    ?>>Company</option>
                                            <option value="private limited company" <?php
                                            if ($data[0]->supplier_type == 'private limited company') {
                                                echo "selected";
                                            }
                                            ?>>Private Limited Company</option>
                                            <option value="proprietorship" <?php
                                            if ($data[0]->supplier_type == 'proprietorship') {
                                                echo "selected";
                                            }
                                            ?>>Proprietorship</option>
                                            <option value="partnership" <?php
                                            if ($data[0]->supplier_type == 'partnership') {
                                                echo "selected";
                                            }
                                            ?>>Partnership</option>
                                            <option value="one person company" <?php
                                            if ($data[0]->supplier_type == 'one person company') {
                                                echo "selected";
                                            }
                                            ?>>One Person Company</option>
                                            <option value="limited liability partnership" <?php
                                            if ($data[0]->supplier_type == 'limited liability partnership') {
                                                echo "selected";
                                            }
                                            ?>>Limited Liability Partnership</option>
									</select>
									<span class="validation-color" id="err_vendor_type_edit"><?php echo form_error('vendor_type'); ?></span>
								</div>
								<div class="form-group col-md-4">
									<label for="vendor_name_edit"> Company/Firm Name<span class="validation-color">*</span> </label>
                                    <input type="hidden"  class="form-control" id="vendor_name_used" name="vendor_name_used" maxlength="90" value="0">
									<input type="text"  class="form-control" id="vendor_name_edit" name="vendor_name_edit" value="<?php echo $data[0]->supplier_name; ?>">
									 <span class="validation-color" id="err_vendor_name_edit"><?php echo form_error('vendor_name_edit'); ?></span>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-4">
									<label for="gst">GST Number</label>
                                        <input type="text"  class="form-control" id="gst_number_edit" name="gst_number_edit" value="<?php echo strtoupper(strtolower($data[0]->supplier_gstin_number)); ?>" maxlength="15">
                                        <span class="validation-color" id="err_gst_number_edit"><?php echo form_error('gstin'); ?></span>
								</div>
								<div class="form-group col-md-4">
									<label for="txt_contact_person_edit">Contact Person Name</label>
									<input type="text" class="form-control" name="txt_contact_person_edit" id="txt_contact_person_edit" value="<?php echo $data[0]->supplier_contact_person; ?>" />
									<span class="validation-color" id="err_customer_name_edit"><?php echo form_error('txt_contact_person_edit'); ?></span>
								</div>
								<div class="form-group col-md-4">
									<label for="cmb_country_edit">Country<span class="validation-color">*</span></label>
									<select class="from-control select2" name="cmb_country_edit" id="cmb_country_edit">
										<option value="">Select Country</option>
                                        <?php
                                        foreach ($country as $key){
                                           ?>
                                                <option value='<?php echo $key->country_id ?>' <?php
                                            if ($key->country_id == $data[0]->supplier_country_id)
                                            {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $key->country_name; ?> </option>
    <?php
}
?>
									</select>
									<span class="validation-color" id="err_country_edit"><?php echo form_error('cmb_country_edit'); ?></span>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-md-4">
									<label for="cmb_state_edit">State<span class="validation-color">*</span> </label>
									<select class="from-control select2" name="cmb_state_edit" id="cmb_state_edit">
										<option value="">Select State</option>
										  <?php
                                            foreach ($state as $key)
                                            {
                                                ?>
                                                <option value='<?php echo $key->state_id ?>'  <?php
                                                if ($key->state_id == $data[0]->supplier_state_id)
                                                {
                                                    echo "selected";
                                                }
                                                ?> ><?php echo $key->state_name; ?>
                                                </option>
										    <?php
										}
										?>
									</select>
									<span class="validation-color" id="err_state_edit"><?php echo form_error('cmb_state_edit'); ?></span>
								</div>
								<div class="form-group col-md-4">
									<label for="cmb_city_edit">City<span class="validation-color">*</span></label>
								<select class="from-control select2"  name="cmb_city_edit" id="cmb_city_edit">
									<option value="">Select City</option>
									<?php
                                            foreach ($city as $key){
                                                ?>
                                                <option value='<?php echo $key->city_id ?>' <?php
                                                if ($key->city_id == $data[0]->supplier_city_id)
                                                {
                                                    echo "selected";
                                                }
                                                ?> > <?php echo $key->city_name; ?>
                                                </option>
                                                <?php
                                            }
                                                if ($data[0]->supplier_city_id == 0) {
                                                    echo "<option value='0' selected> Others </option>";
                                                }else{
                                                    echo "<option value='0'> Others </option>";
                                                }
                                            ?>
								</select>
								<span class="validation-color" id="err_city_edit"><?php echo form_error('cmb_city_edit'); ?></span>
								</div>
								<div class="form-group col-md-4">
									<label for="address_edit">Address<span class="validation-color">*</span></label>
									<textarea name="address_edit" id="address_edit" rows="2" cols="40" class="form-control"><?php echo $data[0]->supplier_address;?></textarea>
									<span class="validation-color" id="err_address_edit"><?php echo form_error('address_edit'); ?></span>
								</div>
							</div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="pin_code">PIN Code</label>
                                    <input class="form-control" type="text" name="txt_pin_code" id="txt_pin_code" maxlength="30"value="<?php echo $data[0]->supplier_postal_code; ?>"/> <span class="validation-color" id="err_pin_code"><?php echo form_error('supplier_postal_code'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="pan_number">PAN Number</label>
                                    <input class="form-control" type="text" name="txt_pan_number" id="txt_pan_number" maxlength="30" value="<?php echo $data[0]->supplier_pan_number; ?>"/> <span class="validation-color" id="err_pan_number"><?php echo form_error('supplier_pan_number'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="pan_number">TAN Number</label>
                                    <input class="form-control" type="text" name="txt_tan_number" id="txt_tan_number" maxlength="30" value="<?php echo $data[0]->supplier_tan_number; ?>"/> <span class="validation-color" id="err_tan_number"><?php echo form_error('supplier_tan_number'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                            <div class="form-group col-md-4">
                                    <label for="gstin">Contact Number</label>
                                    <input class="form-control" type="number" name="txt_contact_number" id="txt_contact_number" maxlength="30" value="<?php echo $data[0]->supplier_mobile; ?>"/> <span class="validation-color" id="err_contact_number"><?php echo form_error('contact_number'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="email">Email<span class="validation-color"></span></label>
                                    <input class="form-control" type="text" name="txt_email" id="txt_email" maxlength="50" value="<?php echo $data[0]->supplier_email; ?>"/> <span class="validation-color" id="err_email"><?php echo form_error('email'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="payment_days">Payment Days</label>
                                    <input class="form-control" type="number" name="payment_days" id="payment_days" min="0" max="365" value="<?php echo $data[0]->payment_days; ?>"/> <span class="validation-color" id="err_payment_days"><?php echo form_error('payment'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" value="<?php echo $data[0]->department;?>">
                                <span class="validation-color" id="err_department"><?php echo form_error('department'); ?></span>
                                </div>  
                            </div>
							<div class="row">
								<div class="field_wrapper"><?php $i = 1;
                                        foreach ($additional_info as $row){?>
                                        <div class="col-sm-4 form-group"><label><input type="text" id="custom_lablel<?php echo $i;?>" name="custom_lablel[]" Placeholder="Textfield" class="disable_in form-control custom_lablel" id="edit_label" value="<?php echo $row->column_name;?>"></label><a href="javascript:void(0);" class="remove_button btn btn-info btn-xs pull-right" title="Remove"><i class="fa fa-minus"></i></a><input type="text" id="custom_field<?php echo $i;?>" name="custom_field[]" value="<?php echo $row->value;?>" class="form-control custom_field"/></div>
                                        <?php 
                                        $i++;
                                        } ?></div>
							</div>
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="vendor_submit_edit" data-dismiss="modal">
							Update
						</button>
						<a class="btn btn-default" href="<?php echo base_url("supplier") ?>">Cancel</a>
					</div>
				</div>
	</section>
</div>
</form>
<?php $this -> load -> view('layout/footer'); ?>
<script type="text/javascript">
	$(document).ready(function() {
		var maxField = 10;
		//Input fields increment limitation
		var addButton = $('.add_button');
		//Add button selector
		var wrapper = $('.field_wrapper');
		
        //New input field html
        var x = <?php echo $additional_info_count + 1 ;?>;
        var count = <?php echo $additional_info_count + 1;?>;

        $('[name=vendor_name_edit]').on('blur',function(){
            $('[name=vendor_name_edit]').trigger('keyup');
        })
        var xhr;
        $('[name=vendor_name_edit]').on('keyup',function(){
            
            var cust_name= $(this).val();
            var customer_id = $('#customer_id').val();
            if (xhr && xhr.readyState != 4) {
                xhr.abort();
            }
            $('[name=vendor_name_used]').val('0');
            $('#err_vendor_name_edit').text('');
            xhr = $.ajax({
                url: '<?= base_url(); ?>customer/CustomerValidation',
                type: 'post',
                data: {cust_name:cust_name,id:customer_id},
                dataType: 'json',
                success: function (json) {
                    if(json.rows > 0){
                        $('#err_vendor_name_edit').text('Name already used!');
                        $('[name=vendor_name_used]').val('1');
                    }
                }, complete: function () {
                   
                }
            })
        })

		//Once add button is clicked
		$(addButton).click(function() {
			//Check maximum number of input fields
            var flag_label = 'N';
            $(".custom_lablel").each(function() {
                var lbl = $(this).val();
                if(lbl == ''){
                    flag_label = 'Y';
                }
            });            
            var flag_value = 'N';
            $(".custom_field").each(function() {
                var val_cu = $(this).val();
                if(val_cu == ''){
                    flag_value = 'Y';
                }
            });
            if(flag_value == 'Y' || flag_label == 'Y' ){
                alert_d.text ='Please Enter valid input for customer field and label!';
                PNotify.error(alert_d); 
                /*alert("Please Enter valid input for customer field and label");*/
                return false;
            }else{
    			if (x < maxField) {
    				 //Input field wrapper
                                var fieldHTML = '<div class="col-sm-4 form-group"><label><input type="text" id="custom_lablel'+count+'" name="custom_lablel[]" Placeholder="Textfield" class="disable_in form-control custom_lablel" id="edit_label"></label><a href="javascript:void(0);" class="remove_button btn btn-info btn-xs pull-right" title="Remove"><i class="fa fa-minus"></i></a><input type="text" id="custom_field'+count+'" name="custom_field[]" class="form-control custom_field"/></div>';
                                x++;
                                //Increment field counter
                                count++;
    				//Increment field counter
    				$(wrapper).append(fieldHTML);
    				//Add field html
    			}
            }
		});
		//Once remove button is clicked
		$(wrapper).on('click', '.remove_button', function(e) {
			e.preventDefault();
			$(this).parent('div').remove();
			//Remove field html
			x--;
			//Decrement field counter
		});
		$(wrapper).on('dblclick', '#edit_label', function() {
			console.log("fdf");
			$(this).removeClass('disable_in');
		});
	}); 
</script>
<script>	
    $("#gst_number_edit").on("blur keyup", function (event) {
        var gst_regex = /^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;
        var gstin = $('#gst_number_edit').val();
        $("#gst_number_edit").val(gstin.toUpperCase());
        var gstin_length = gstin.length;
        if (gstin_length > 0)
        {
            if (gstin == null || gstin == "") {
                $("#err_gst_number_edit").text("Please Enter GSTIN Number.");
                return false;
            } else {
                $("#err_gst_number_edit").text("");
            }
            if (!gstin.match(gst_regex)) {
                $('#err_gst_number_edit').text("Please Enter Valid GSTIN Number.");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            if (gstin_length < 15 || gstin_length > 15)
            {
                $("#err_gst_number_edit").text("GSTIN Number should have length 15");
                return false;
            } else {
                $("#err_gst_number_edit").text("");
            }
            var res = gstin.slice(2, 12);
            $('#txt_pan_number').val(res);
        }
    }); 
	 $('#cmb_country_edit').change(function () {
        var id = $(this).val();
        $('#cmb_state_edit').empty();
         $('#cmb_city_edit').empty();
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
                var city = $('#cmb_city_edit');
                var opt1 = document.createElement('option');
                    opt1.text = 'Select City';
                    opt1.value = ''; 
                city.append(opt1);                   
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
                var opt1 = document.createElement('option');
                    opt1.text = 'Others';
                    opt1.value = '0';
                    city.append(opt1);
            }
        });
    });
$("#vendor_submit_edit").click(function() {
            var supplier_module_id = $('#supplier_id_edit').val()?$('#supplier_id_edit').val():"";
            var supplier_code = $('#supplier_code_edit').val()?$('#supplier_code_edit').val():"";
            var supplier_name = $('#vendor_name_edit').val()?$('#vendor_name_edit').val():"";
            var supplier_type = $('#vendor_type_edit').val()?$('#vendor_type_edit').val():"";
            var city = $('#cmb_city_edit').val()?$('#cmb_city_edit').val():"";
            var state = $('#cmb_state_edit').val()?$('#cmb_state_edit').val():"";
            var country = $('#cmb_country_edit').val()?$('#cmb_country_edit').val():"";
            var address = $('#address_edit').val()?$('#address_edit').val():"";
            var contact_person = $('#txt_contact_person_edit').val()?$('#txt_contact_person_edit').val():"";
            var email = $('#txt_email').val() ? $('#txt_email').val() : "";
            var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var gst_number = $('#gst_number_edit').val() ? $('#gst_number_edit').val() : "";
            var pan_number = $('#txt_pan_number').val() ? $('#txt_pan_number').val() : "";
            var tan_number = $('#txt_tan_number').val() ? $('#txt_tan_number').val() : "";
            var pin_number = $('#txt_pin_code').val() ? $('#txt_pin_code').val() : "";
            var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
             var flag_label = 'N';
             var name_regex = /^[a-zA-Z0-9\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
             var tan_regex = /^([A-Z]{4}[0-9]{5}[A-Z]{1})$/;
             var alpa_regex = /^[a-zA-Z ]+$/;
             var num_regex = /^[0-9]+$/;
             var name_regex_alnu = /^[-a-zA-Z\s0-9 ]+$/;
            
            $(".custom_lablel").each(function() {
                var lbl = $(this).val();
                if(lbl == ''){
                    flag_label = 'Y';
                }
            });            
            var flag_value = 'N';
            $(".custom_field").each(function() {
                var val_cu = $(this).val();
                if(val_cu == ''){
                    flag_value = 'Y';
                }
            });          
            if (supplier_code == null || supplier_code == "") {
                $("#err_vendor_code_edit").text("Please Enter Supplier Code.");
                return false;
            } else {
                $("#err_vendor_code_edit").text("");
            }
            if (supplier_type == null || supplier_type == ""){
                $("#err_vendor_type_edit").text("Please Enter Supplier Type.");
                return false;
            } else {
                $("#err_vendor_type_edit").text("");
            }
            if (supplier_name == null || supplier_name == ""){
                $("#err_vendor_name_edit").text("Please Enter Company/Firm Name.");
                return false;
            } else {
                $("#err_vendor_name_edit").text("");
                if($('[name=vendor_name_used]').val() > 0){
                    $("#err_vendor_name_edit").text("Name already used!");
                    return false;
                }
            }
            if (!supplier_name.match(name_regex)) {
                $('#err_vendor_name_edit').text("Please Enter Valid Vendor Name ");
                return false;
            } else {
                $("#err_vendor_name_edit").text("");
            }
            if ( email != "") {
                if (!email.match(email_regex)) {
                    $('#err_email').text("Please Enter Valid Email");
                    return false;
                } else {
                $("#err_email").text("");
                }
            }
           /* if (email == null || email == "") {
                $("#err_email_address").text("Please Enter Email.");
                return false;
            } else {
                $("#err_email_address").text("");
            } */
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
           if (contact_person != "" ) {
           if (!contact_person.match(alpa_regex)) {
                $('#err_customer_name_edit').text("Please Enter Valid Contact Person Name");
                return false; 
            } else {
            $("#err_customer_name_edit").text("");
            }  
            } else {
                $("#err_customer_name_edit").text("");
            }
            if (gst_number != "" ) {
               if (!gst_number.match(gst_regex_format)) {
                    $('#err_gst_number_edit').text("Please Enter Valid GST Number");
                    return false; 
                } else {
                $("#err_gst_number_edit").text("");
            } 
            } else {
                $("#err_gst_number_edit").text("");
            }
            if (pan_number != "" ) {
               if (!pan_number.match(name_regex_alnu)) {
                    $('#err_pan_number').text("Please Enter Valid Pan Number");
                    return false; 
                } else {
                $("#err_pan_number").text("");
            } 
            } else {
                $("#err_pan_number").text("");
            }

            $("#err_tan_number").text("");
            if (tan_number != "" ) {
                if (!tan_number.match(tan_regex)) {
                    $('#err_tan_number').text("Please Enter Valid Tan Number");
                    return false; 
                }
            }
            
            if (pin_number != "" ) {
               if (!pin_number.match(num_regex)) {
                    $('#err_pin_code').text("Please Enter Valid Pin Number");
                    return false; 
                }  else {
                $("#err_pin_code").text("");
            }
            } else {
                $("#err_pin_code").text("");
            }
            if(flag_value == 'Y' || flag_label == 'Y' ){
                alert_d.text ='Please Enter valid input for customer field and label!';
                PNotify.error(alert_d); 
                /*alert("Please Enter valid input for customer field and label");*/
                return false;
            }
            $.ajax({
                url: base_url + 'supplier/get_check_supplier',
                dataType: 'JSON',
                method: 'POST',
                data: {
                    'supplier_id' :supplier_module_id,
                    'supplier_code' :supplier_code
            },
            success: function (result) {
                var count = result[0].num;
                if (count > 0) {
                    $("#err_vendor_code_edit").text("Vendor Code Already Exist.");
                        return false;
                }
                else{
                    $("#err_vendor_code_edit").val("");
                    $('#form').submit();
                }
            }
        });
        
    });
</script>