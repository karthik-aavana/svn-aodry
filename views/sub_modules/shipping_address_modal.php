 <div id="shipping_address_modal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Shipping Address</h4>
                </div>
                <form name="frm_shipping_add" id="frm_shipping_add" method="post" >
                <div class="modal-body">
                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_type">
                                    Company Type<span class="validation-color">*</span>
                                </label>
                                <select class="form-control" id="company_type" name="company_type">
                                    <option value="">Select Type</option>
                                    <option value="customer">Customer</option>
                                    <option value="supplier">Supplier</option>
                                </select>
                                <span class="validation-color" id="err_company_type"><?php echo form_error('company_type'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="company_name">
                                    Company<span class="validation-color">*</span>
                                </label>
                                <select class="form-control select2" id="company_name" name="company_name">
                                    <option value="">Select Company</option>   
                                </select>
                                <span class="validation-color" id="err_company_name"><?php echo form_error('company_name'); ?></span>
                            </div>
                        </div>
                    </div> -->
                    <input type="hidden" name="company_type" value="customer">
                    <input type="hidden" name="company_name">
                    <input type="hidden" name="ajax_return" value="all">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="txt_shipping_code">
                                    Shipping Code
                                </label>
                                <input type="text"  class="form-control" id="txt_shipping_code" name="txt_shipping_code" maxlength="30" readonly>                                
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_person_name">
                                    Contact Person Name
                                </label>
                                <input type="text"  class="form-control" id="contact_person_name" name="contact_person_name" maxlength="30">  
                                 <span class="validation-color" id="err_contact_person_name"><?php echo form_error('contact_person_name'); ?></span>                              
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="party_address">Address<span class="validation-color">*</span> </label>
                                <input type="text" class="form-control" id="party_address" name="party_address" value="" maxlength="120">
                                <span class="validation-color" id="err_party_address"><?php echo form_error('party_address'); ?></span>
                            </div>  
                        </div>
                       <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Country<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="ship_cmb_country" name="ship_cmb_country" style="width: 100%;">
                                    <option value="">Select Country</option>
                                    <?php 
                                        foreach ($country as $row){
                                            echo "<option value='".$row->country_id."'>".$row->country_name."</option>";
                                        }
                                    ?>
                                </select>
                                <span class="validation-color" id="err_country"><?php echo form_error('ship_cmb_country'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">State<span class="validation-color">*</span></label>
                                <select class="form-control state select2" id="ship_cmb_state" name="ship_cmb_state" style="width: 100%;">
                                    <option value="">Select State</option>
                                   
                                </select>
                                <span class="validation-color" id="err_state"><?php echo form_error('ship_cmb_state'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">City<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="ship_cmb_city" name="ship_cmb_city" style="width: 100%;">
                                    <option value="">Select City</option>
                                   
                                </select>
                                <span class="validation-color" id="err_city"><?php echo form_error('ship_cmb_city'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" value=""  maxlength="20">
                                <span class="validation-color" id="err_department"><?php echo form_error('department'); ?></span>
                            </div>  
                        </div>                                    
                        <div class="col-sm-6">                                  
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="txt_email" name="txt_email" value=""  maxlength="40">
                                <span class="validation-color" id="err_txt_email"><?php echo form_error('email'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_number"> Contact Number </label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" value=""  maxlength="15">
                                <span class="validation-color" id="err_contact_number"><?php echo form_error('contact_number'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="gst_number">GST Number </label>
                                <input type="text" class="form-control" id="gst_number" name="gst_number" value=""  maxlength="15">
                                <span class="validation-color" id="err_gst_number"><?php echo form_error('gst_number'); ?></span>
                            </div>
                        </div>
                    </div>
                </div> 
            </form>
                <div class="modal-footer">
                    <button type="button" id="shipping_submit" class="btn btn-info shipping_address_submit"> Add </button>
                    <button type="button" class="btn btn-info" class="close" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>               
        </div>
    </div>
</div>
<script>
    $('#ship_cmb_country').change(function () {
        var id = $(this).val();
        $('#ship_cmb_state').empty();
        $('#ship_cmb_city').empty();
      
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: { id : id},
            dataType: "JSON",
            success: function (data) {
                console.log(data);
                 var state = $('#ship_cmb_state');
                 var opt = document.createElement('option');
                    opt.text = 'Select State';
                    opt.value = '';
                    state.append(opt);  
                 var city = $('#ship_cmb_city');
                 var opt1 = document.createElement('option');
                    opt1.text = 'Select City';
                    opt1.value = '';
                    city.append(opt1);                   
                for (i = 0; i < data.length; i++) {
                     $('#ship_cmb_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                }
            }
        });
    });
    $('#ship_cmb_state').change(function () {
        var id = $(this).val();
        $('#ship_cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_city/'+id,
            dataType: "JSON",
            success: function (data) {
                console.log(data);
                 var city = $('#ship_cmb_city');
                 var opt = document.createElement('option');
                    opt.text = 'Select City';
                    opt.value = '';
                    city.append(opt);
                                      
                for (i = 0; i < data.length; i++) {
                     $('#ship_cmb_city').append('<option value="' + data[i].city_id + '">' + data[i].city_name + '</option>');
                }
                 var opt1 = document.createElement('option');
                    opt1.text = 'Others';
                    opt1.value = '0';
                    city.append(opt1); 
            }
        });
    });
    $('#company_type').change(function () {
        var id = $(this).val();
        $('#txt_shipping_code').val('');
        $('#company_name').empty();
        $.ajax({
            url: base_url + 'shipping_address/get_party_name/',
            method: "POST",
            data: {party_type: id},
            dataType: "JSON",
            success: function (data) {
                $('#company_name').append(data);
            }
        });
    });

    $('.add_shipping').click(function () {
        var id = $('#customer').val();
        var party_type = $('#company_type').val();
        $('#txt_shipping_code').val('');
        $.ajax({
            url: base_url + 'shipping_address/get_party_shipping_code/',
            method: "POST",
            data: {party_id: id,party_type:'customer'},
            dataType: "JSON",
            success: function (data) {
                $('#txt_shipping_code').val(data.shipping_code);
                $('[name=company_name]').val(id);
                $('#shipping_address_modal').modal('show');
            }
        });
    });

    $(document).ready(function () {
        $("#gst_number").on("blur keyup", function (event) {
            var gstin = $('#gst_number').val();
            $("#gst_number").val(gstin.toUpperCase());
            var gstin_length = gstin.length;
            var gst_regex_format = "^([0][1-9]|[1-2][0-9]|[3][0-5])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
            if (gstin_length > 0)
            {
                if (gstin == null || gstin == "") {
                    $("#err_gst_number").text("Please Enter GSTIN Number.");
                    return false;
                } else {
                    $("#err_gst_number").text("");
                }
                if (!gstin.match(sname_regex)) {
                    $('#err_gst_number').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_gst_number").text("");
                }
                if (gstin_length < 15 || gstin_length > 15)
                {
                    $("#err_gst_number").text("GSTIN Number should have length 15");
                    return false;
                } else {
                    $("#err_gst_number").text("");
                }
                if (!gstin.match(gst_regex_format)) {
                    $('#err_gst_number').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_gst_number").text("");
                }
            }
        });
        
    });

    $("#shipping_submit").click(function (event) {
            var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var company_type = $('[name=company_type]').val();
            var company_name = $('[name=company_name]').val();
            var contact_person = $('#contact_person_name').val();
            var party_address = $('#party_address').val();
            var department = $('#department').val();
            var txt_email = $('#txt_email').val();
            var contact_number = $('#contact_number').val();
            var country = $('#ship_cmb_country').val();
            var state = $('#ship_cmb_state').val();
            var city = $('#ship_cmb_city').val();
            if (company_type == null || company_type == "") {
                $("#err_company_type").text("Please Select Company Type.");
                return false;
            } else {
                $("#err_company_type").text("");
            }
            if (company_name == null || company_name == "") {
                $("#err_company_name").text("Please Select Company Name.");
                return false;
            } else {
                $("#err_company_name").text("");
            }
            
            /*if (contact_person == null || contact_person == "") {
                $("#err_contact_person_name").text("Please Enter Contact Person Name.");
                return false;
            } else {
                $("#err_contact_person_name").text("");
            }*/
            if (party_address == null || party_address == "") {
                $("#err_party_address").text("Please Enter Address.");
                return false;
            } else {
                $("#err_party_address").text("");
            }
             if (country == null || country == "") {
                $("#err_country").text("Please Select Country.");
                return false;
            } else {
                $("#err_country").text("");
            }
            if (state == null || state == "") {
                $("#err_state").text("Please Select State.");
                return false;
            } else {
                $("#err_state").text("");
            }
            if (city == null || city == "") {
                $("#err_city").text("Please Select City.");
                return false;
            } else {
                $("#err_city").text("");
            }
            if (!txt_email.match(email_regex) && txt_email != '') {
                $('#err_txt_email').text('Please Enter valid Email Address');
                return false;
            } else {
                $("#err_txt_email").text("");
            }
            if (txt_email.length < 2 && txt_email != '') {
                $('#err_txt_email').text("Email length must be greater than 2");
                return false;
            } else {
                $("#err_txt_email").text("");
            }
            
          
            var form_data = $('#frm_shipping_add').serializeArray();
            
            $.ajax({
                url: base_url + 'shipping_address/add_shipping_address',
                dataType: 'JSON',
                method: 'POST',
                data: form_data,
                success: function (result) {
                    setTimeout(function () {
                        /*location.reload();*/
                    });
                }
            });
        });
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$("#shipping_address_submit").click(function(event) {
			var modal_shipping_address = $('#modal_shipping_address').val();
			var modal_party_id = $('#modal_party_id').val();
			var modal_party_type = $('#modal_party_type').val();
			var modal_shipping_gstin = $('#modal_shipping_gstin').val();
			var modal_shipping_address_empty = "Please Enter Shipping Address.";
			if (modal_shipping_address == null || modal_shipping_address == "") {
				$("#err_modal_shipping_address").text(modal_shipping_address_empty);
				return !1;
			} else {
				$("#err_modal_shipping_address").text("")
			}
			if (modal_shipping_gstin != "") {
				if (modal_shipping_gstin.length < 15 || modal_shipping_gstin.length > 15) {
					$("#err_modal_shipping_gstin").text("GSTIN Number should have length 15");
					return false;
				} else {
					$("#err_modal_shipping_gstin").text("");
				}
				// var res=modal_shipping_gstin.slice(0, 2);
				// var res1 = modal_shipping_gstin.slice(13, 14);
				// if (res!=state_code || res1!='Z')
				if (!modal_shipping_gstin.match(gst_regex)) {
					$("#err_modal_shipping_gstin").text("Please enter valid GST number(Ex:29AAAAA9999AXZX).");
					return false;
				} else {
					$("#err_modal_shipping_gstin").text("");
				}
			}
			$.ajax({
				url : base_url + 'general/add_shipping_address',
				dataType : 'JSON',
				method : 'POST',
				data : {
					'modal_shipping_address' : modal_shipping_address,
					'modal_party_id' : modal_party_id,
					'modal_party_type' : modal_party_type,
					'modal_shipping_gstin' : modal_shipping_gstin
				},
				success : function(result) {
					var data = result['shipping_address_data'];
					var shipping_address_id = result['shipping_address_id'];
					$('#shipping_address').html('');
					$('#shipping_address').append('<option value="">Select</option>');
					for ( i = 0; i < data.length; i++) {
						var shipping_address = data[i].shipping_address;
						var shipping_address_new = shipping_address.replace(/\n\r|\\r|\\n|\r|\n|\r\n|\\n\\r|\\r\\n/g, '<br/>')
						$('#shipping_address').append('<option value="' + data[i].shipping_address_id + '">' + shipping_address_new + '</option>');
					}
					$('#shipping_address').val(shipping_address_id).attr("selected", "selected");
					$("#shipping_address").select2({
						containerCssClass : "wrap"
					});
					$('#shipping_address').change();
					$("#modal_shipping_address").val('');
					$("#modal_shipping_gstin").val('');
				}
			});
		});
	}); 
</script>
