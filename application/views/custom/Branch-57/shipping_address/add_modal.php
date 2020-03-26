<div id="add_shipping" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Shipping Address</h4>
            </div>
            <form name="frm_shipping_add" id="frm_shipping_add" method="post" >
                <div class="modal-body">
                    <div id="loader_coco">
                        <h1 class="ml8">
                            <span class="letters-container">
                                <span class="letters letters-left">
                                    <img src="<?php echo base_url('assets/'); ?>images/loader-icon.png" width="40px">
                                </span>
                            </span>
                            <span class="circle circle-white"></span>
                            <span class="circle circle-dark"></span>
                            <span class="circle circle-container">
                                <span class="circle circle-dark-dashed"></span>
                            </span></h1>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_type">
                                    Company Type<span class="validation-color">*</span>
                                </label>
                                <select class="form-control" id="company_type" name="company_type">
                                    <option value="">Select Type</option>
                                    <option value="customer">Customer</option>
                                    <!-- <option value="supplier">Supplier</option> -->
                                </select>
                                <span class="validation-color" id="err_company_type"><?php echo form_error('company_type'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_name">
                                    Store Name<span class="validation-color">*</span>
                                </label>
                                <select class="form-control select2" id="company_name" name="company_name">
                                    <option value="">Select Company</option>   
                                </select>
                                <span class="validation-color" id="err_company_name"><?php echo form_error('company_name'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="txt_shipping_code">
                                    Shipping Code<span class="validation-color">*</span>
                                </label>
                                <input type="text"  class="form-control" id="txt_shipping_code" name="txt_shipping_code" maxlength="30">      
                                <span class="validation-color" id="err_shipping_code"><?php echo form_error('company_name'); ?></span>                          
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_person_name">
                                    Contact Person Name<!-- <span class="validation-color">*</span> -->
                                </label>
                                <input type="text"  class="form-control" id="contact_person_name" name="contact_person_name" maxlength="30">  
                                <span class="validation-color" id="err_contact_person_name"><?php echo form_error('contact_person_name'); ?></span>                              
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="party_address">Customer/Vendor Shipping Address<span class="validation-color">*</span> </label>
                                <input type="text" class="form-control" id="party_address" name="party_address" value="" maxlength="120">
                                <span class="validation-color" id="err_party_address"><?php echo form_error('party_address'); ?></span>
                            </div>  
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Country<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="cmb_country" name="cmb_country" style="width: 100%;">
                                    <option value="">Select Country</option>
                                    <?php
                                    foreach ($country as $row) {
                                        echo "<option value='" . $row->country_id . "'>" . $row->country_name . "</option>";
                                    }
                                    ?>
                                </select>
                                <span class="validation-color" id="err_country"><?php echo form_error('cmb_country'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">State<span class="validation-color">*</span></label>
                                <select class="form-control state select2" id="cmb_state" name="cmb_state" style="width: 100%;">
                                    <option value="">Select State</option>

                                </select>
                                <span class="validation-color" id="err_state"><?php echo form_error('cmb_state'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">City<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="cmb_city" name="cmb_city" style="width: 100%;">
                                    <option value="">Select City</option>

                                </select>
                                <span class="validation-color" id="err_city"><?php echo form_error('cmb_city'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pin_code">PIN Code</label>
                                <input type="text" class="form-control" id="pin_code" name="pin_code" value=""  maxlength="15">
                                <span class="validation-color" id="err_pin_code"><?php echo form_error('pin_code'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" value=""  maxlength="20">
                                <span class="validation-color" id="err_department"><?php echo form_error('department'); ?></span>
                            </div>  
                        </div>  
                    </div>
                    <div class="row">
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
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="gst_number">GST Number </label>
                                <input type="text" class="form-control" id="gst_number" name="gst_number" value=""  maxlength="15">
                                <span class="validation-color" id="err_gst_number"><?php echo form_error('gst_number'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="store_location">Store Location<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="store_location" name="store_location" value=""  maxlength="50">
                                <span class="validation-color" id="err_store_location"><?php echo form_error('store_location'); ?></span>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button type="button" id="shipping_submit" class="btn btn-info"> Add </button>
                    <button type="button" class="btn btn-info" class="close" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>               
    </div>
</div>
</div>
<script>
    $('#cmb_country').change(function () {
        var id = $(this).val();
        $('#cmb_state').empty();
        $('#cmb_city').empty();
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: {id: id},
            dataType: "JSON",
            success: function (data) {
                console.log(data);
                var state = $('#cmb_state');
                var opt = document.createElement('option');
                opt.text = 'Select State';
                opt.value = '';
                state.append(opt);
                var city = $('#cmb_city');
                var opt1 = document.createElement('option');
                opt1.text = 'Select City';
                opt1.value = '';
                city.append(opt1);
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
            url: base_url + 'general/get_city/' + id,
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
    $('#company_name').change(function () {
        var id = $(this).val();
        var party_type = $('#company_type').val();
        $('#txt_shipping_code').val('');
        $.ajax({
            url: base_url + 'shipping_address/get_party_shipping_code/',
            method: "POST",
            data: {party_id: id, party_type: party_type},
            dataType: "JSON",
            success: function (data) {
                console.log(data.shipping_code);
                $('#txt_shipping_code').val(data.shipping_code);
            }
        });
    });
    $(document).ready(function () {
        $("#gst_number").on("blur keyup", function (event) {
            var gstin = $('#gst_number').val();
            $("#gst_number").val(gstin.toUpperCase());
            var gstin_length = gstin.length;
            var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
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
        var company_type = $('#company_type').val();
        var company_name = $('#company_name').val();
        var txt_shipping_code = $('#txt_shipping_code').val();
        var contact_person = $('#contact_person_name').val();
        var party_address = $('#party_address').val();
        var department = $('#department').val();
        var txt_email = $('#txt_email').val();
        var contact_number = $('#contact_number').val();
        var country = $('#cmb_country').val();
        var state = $('#cmb_state').val();
        var city = $('#cmb_city').val();
        var pin_number= $('#pin_code').val();
        var gst_number= $('#gst_number').val();
        var store_location= $('#store_location').val();
        var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
        var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
        var alpa_regex = /^[a-zA-Z ]+$/;
        var num_regex = /^[0-9]+$/;
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
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

        if (txt_shipping_code == null || txt_shipping_code == "") {
            $("#err_shipping_code").text("Please add shipping code!");
            return false;
        } else {
            $("#err_shipping_code").text("");
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
        if (department != "" ) {
           if (!department.match(name_regex1)) {
                $('#err_department').text("Please Enter Valid Department");
                return false; 
            }  else {
            $("#err_department").text("");
        }
        } else {
            $("#err_department").text("");
        }
        if (contact_person != "" ) {
           if (!contact_person.match(alpa_regex)) {
                $('#err_contact_person_name').text("Please Enter Valid Contact Person Name");
                return false; 
            } else {
            $("#err_contact_person_name").text("");
        }  
        } else {
            $("#err_contact_person_name").text("");
        }
        if (contact_number != "" ) {
           if (!contact_number.match(num_regex)) {
                $('#err_contact_number').text("Please Enter Valid Contact Number");
                return false; 
            }  else {
            $("#err_contact_number").text("");
        }
        } else {
            $("#err_contact_number").text("");
        }
        if (gst_number != "" ) {
           if (!gst_number.match(gst_regex_format)) {
                $('#err_gst_number').text("Please Enter Valid GST Number");
                return false; 
            } else {
            $("#err_gst_number").text("");
        } 
        } else {
            $("#err_gst_number").text("");
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
        if (store_location == null || store_location == "") {
            $("#err_store_location").text("Please Enter Store Location.");
            return false;
        } else {
            $("#err_store_location").text("");
        }
        $.ajax({
            url: base_url + 'shipping_address/get_check_shipping_with_location',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'company_name' :company_name,
                'store_location': store_location
            },
            success: function (result) {
                var count = result[0].num;
                if (count > 0) {
                    $("#err_store_location").text("Combination of Company and Store Location is Already Exist");
                        return false;
                }else{
                    var form_data = $('#frm_shipping_add').serializeArray();
                    $.ajax({
                        url: base_url + 'shipping_address/add_shipping_address',
                        dataType: 'JSON',
                        method: 'POST',
                        data: form_data,
                        beforeSend: function(){
                                 // Show image container
                                $("#loader_coco").show();
                        },
                        success: function (result) {
                            setTimeout(function () {
                            location.reload();
                             $("#loader_coco").hide();

                            });                
                        }
                    });
                     anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
                }
            }
        });
    });
</script>