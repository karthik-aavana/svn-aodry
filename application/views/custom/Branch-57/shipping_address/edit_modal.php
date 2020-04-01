<div id="edit_shipping" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Edit Shipping Address</h4>
            </div>

            <form name="frm_shipping_edit" id="frm_shipping_edit" method="post" >
                <div class="modal-body">
                    <div id="loader">
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
                                <label for="company_type_edit">
                                    Company Type<span class="validation-color">*</span>
                                </label>
                                <select class="form-control" id="company_type_edit" name="company_type_edit">
                                    <option value="">Select Type</option>
                                    <option value="customer">Customer</option>
                                    <!-- <option value="supplier">Supplier</option> -->
                                </select>
                                <span class="validation-color" id="err_company_type_edit"><?php echo form_error('company_type_edit'); ?></span>
                                <input type="hidden" name="shipping_address_id_edit" id="shipping_address_id_edit" value="">
                                <input type="hidden" name="primary_address_edit" id="primary_address_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_name_edit">
                                    Store Name<span class="validation-color">*</span>
                                </label>
                                <select class="form-control select2" id="company_name_edit" name="company_name_edit">                                 
                                </select>
                                <span class="validation-color" id="err_company_name_edit"><?php echo form_error('company_name_edit'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="txt_shipping_code_edit">
                                    Shipping Code<span class="validation-color">*</span>
                                </label>
                                <input type="text"  class="form-control" id="txt_shipping_code_edit" name="txt_shipping_code_edit" maxlength="30" >  <span class="validation-color" id="err_txt_shipping_code_edit"><?php echo form_error('company_name_edit'); ?></span>                             
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_person_name_edit">
                                    Contact Person Name<!-- <span class="validation-color">*</span> -->
                                </label>
                                <input type="text"  class="form-control" id="contact_person_name_edit" name="contact_person_name_edit"  maxlength="30">
                                <span class="validation-color" id="err_contact_person_name_edit"><?php echo form_error('contact_person_name_edit'); ?></span>
                            </div>
                        </div>                        
                        </div>
                    <div class="row">                        
                        <div class="col-sm-6">  
                            <div class="form-group">
                                <label for="party_address_edit">Customer/Vendor Shipping Address<span class="validation-color">*</span> </label>
                                <input type="text" class="form-control" id="party_address_edit" name="party_address_edit" value=""  maxlength="250">
                                <span class="validation-color" id="err_party_address_edit"><?php echo form_error('party_address_edit'); ?></span>
                            </div>  
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Country<span class="validation-color">*</span></label>
                                <select class="form-control" id="cmb_country_edit1" name="cmb_country_edit1" style="width: 100%;">
                                    <?php
                                    foreach ($country as $row) {
                                        echo "<option value='" . $row->country_id . "'>" . $row->country_name . "</option>";
                                    }
                                    ?>
                                </select>
                                <span class="validation-color" id="err_cmb_country_edit"></span>
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">State<span class="validation-color">*</span></label>
                                <select class="form-control state select2" id="cmb_state_edit" name="cmb_state_edit" style="width: 100%;">
                                    <option value="">Select State</option>
                                </select>
                                <span class="validation-color" id="err_state_edit"><?php echo form_error('cmb_state_edit'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">City<span class="validation-color">*</span></label>
                                <select class="form-control country select2" id="cmb_city_edit" name="cmb_city_edit" style="width: 100%;">                                   
                                </select>
                                <span class="validation-color" id="err_city_edit"><?php echo form_error('cmb_city_edit'); ?></span>
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pin_code">PIN Code</label>
                                <input type="text" class="form-control" id="edit_pin_code" name="edit_pin_code" value=""  maxlength="20">
                                <span class="validation-color" id="err_pin_error_code"><?php echo form_error('edit_pin_code'); ?></span>
                            </div>  
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="department_edit">Department</label>
                                <input type="text" class="form-control" id="department_edit" name="department_edit" value=""  maxlength="20">
                                <span class="validation-color" id="err_department_edit"><?php echo form_error('department_edit'); ?></span>
                            </div>  
                        </div>    
                        </div>
                    <div class="row">                                
                        <div class="col-sm-6">                                  
                            <div class="form-group">
                                <label for="email_edit">Email</label>
                                <input type="text" class="form-control" id="txt_email_edit" name="txt_email_edit" value=""  maxlength="40">
                                <span class="validation-color" id="err_txt_email_edit"><?php echo form_error('email_edit'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_number_edit"> Contact Number </label>
                                <input type="text" class="form-control" id="contact_number_edit" name="contact_number_edit" value=""  maxlength="15">
                                <span class="validation-color" id="err_contact_number_edit"><?php echo form_error('contact_number_edit'); ?></span>
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="gst_number_edit">GST Number </label>
                                <input type="text" class="form-control" id="gst_number_edit" name="gst_number_edit" value=""  maxlength="15">
                                <span class="validation-color" id="err_gst_number_edit"><?php echo form_error('gst_number_edit'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="edit_store_location">Store Location<span class="validation-color">*</span></label>
                                <input type="text" class="form-control" id="edit_store_location" name="edit_store_location" value=""  maxlength="15">
                                <span class="validation-color" id="err_edit_store_location"><?php echo form_error('edit_store_location'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="shipping_update" class="btn btn-info"> Update </button>
                    <button type="button" class="btn btn-info" class="close" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>                
        </div>
    </div>
</div>
<script>
    $('#cmb_country_edit1').change(function () {
      var id = $(this).val();
        $('#cmb_state_edit').empty();
        $('#cmb_city_edit').empty();
        $.ajax({
            url: base_url + 'general/get_state/',
            method: "POST",
            data: {id: id},
            dataType: "JSON",
            success: function (data) {
            
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
            url: base_url + 'general/get_city/' + id,
            dataType: "JSON",
            success: function (data) {
                
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
    $('#company_type_edit').change(function () {
        var id = $(this).val();
        $('#company_name_edit').empty();
        $('#txt_shipping_code_edit').val('');
        $.ajax({
            url: base_url + 'shipping_address/get_party_name/',
            method: "POST",
            data: {party_type: id},
            dataType: "JSON",
            success: function (data) {
                $('#company_name_edit').append(data);
            }
        });
    });
    $('#company_name_edit').change(function () {
        var id = $(this).val();
        var party_type = $('#company_type_edit').val();
        var shipping_address_id = $('#shipping_address_id_edit').val();
        $('#txt_shipping_code_edit').val('');
        $.ajax({
            url: base_url + 'shipping_address/get_party_shipping_code_edit/',
            method: "POST",
            data: {party_id: id, party_type: party_type, id: shipping_address_id},
            dataType: "JSON",
            success: function (data) {
                console.log(data.shipping_code);
                $('#txt_shipping_code_edit').val(data.shipping_code);
            }
        });
    });
    $(document).on("click", ".edit_shipping", function () {
        var id = $(this).data('id');
        $.ajax({
            url: base_url + 'shipping_address/edit_modal/' + id,
            dataType: 'JSON',
            method: 'POST',
            success: function (result) {
                console.log(result);
                var party_list = result['party_list'];
                var state_list = result['state_list'];
                var city_list = result['city_list'];
                $('#company_name_edit').append(party_list);
                $('#cmb_state_edit').append(state_list);
                $('#cmb_city_edit').append(city_list);
                var data = result['data'];
                // $("#cmb_country_edit").val(data[0].country_id).attr("selected","selected");
                $('#shipping_address_id_edit').val(data[0].shipping_address_id);
                $('#primary_address_edit').val(data[0].primary_address);
                $('#company_type_edit').val(data[0].shipping_party_type);
                $('#contact_person_name_edit').val(data[0].contact_person);
                $('#party_address_edit').val(data[0].shipping_address);
                $('#department_edit').val(data[0].department);
                $('#txt_email_edit').val(data[0].email);
                $('#contact_number_edit').val(data[0].contact_number);
                $('#company_name_edit').val(data[0].shipping_party_id);
                $('#txt_shipping_code_edit').val(data[0].shipping_code);
                $('#cmb_country_edit1').val(data[0].country_id);
                $('#cmb_state_edit').val(data[0].state_id);
                $('#cmb_city_edit').val(data[0].city_id);
                $('#gst_number_edit').val(data[0].shipping_gstin);
                $('#edit_pin_code').val(data[0].address_pin_code);
                $('#edit_store_location').val(data[0].store_location);
            }
        });
    });
    $(document).ready(function () {
        $("#gst_number_edit").on("blur keyup", function (event) {
            var gstin = $('#gst_number_edit').val();
            $("#gst_number_edit").val(gstin.toUpperCase());
            var gstin_length = gstin.length;
            var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
            if (gstin_length > 0)
            {
                if (gstin == null || gstin == "") {
                    $("#err_gst_number_edit").text("Please Enter GSTIN Number.");
                    return false;
                } else {
                    $("#err_gst_number_edit").text("");
                }
                if (!gstin.match(sname_regex)) {
                    $('#err_gst_number_edit').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_gst_number_edit").text("");
                }
                if (gstin_length < 15 || gstin_length > 15)
                {
                    $("#err_gst_number_edit").text("GSTIN Number should have length 15");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                if (!gstin.match(gst_regex_format)) {
                    $('#err_gst_number_edit').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_gst_number_edit").text("");
                }
            }
        });
        $("#shipping_update").click(function (event) {
            var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            var company_type = $('#company_type_edit').val();
            var company_name = $('#company_name_edit').val();
            var txt_shipping_code_edit = $('#txt_shipping_code_edit').val();
            var contact_person = $('#contact_person_name_edit').val();
            var party_address = $('#party_address_edit').val();
            var department = $('#department_edit').val();
            var txt_email = $('#txt_email_edit').val();
            var contact_number = $('#contact_number_edit').val();
            var gst_number = $('#gst_number_edit').val();
            var country = $('#cmb_country_edit1').val();
            var state = $('#cmb_state_edit').val();
            var city = $('#cmb_city_edit').val();
            var pin_number= $('#edit_pin_code').val();
            var store_location = $('#edit_store_location').val();   
            var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
            var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
            var alpa_regex = /^[a-zA-Z ]+$/;
            var num_regex = /^[0-9]+$/;
            var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
            if (company_type == null || company_type == "") {
                $("#err_company_type_edit").text("Please Enter Company Type.");
                return false;
            } else {
                $("#err_company_type_edit").text("");
            }
            if (company_name == null || company_name == "") {
                $("#err_company_name_edit").text("Please Enter Store Name.");
                return false;
            } else {
                $("#err_company_name_edit").text("");
            }

            if (txt_shipping_code_edit == null || txt_shipping_code_edit == "") {
                $("#err_txt_shipping_code_edit").text("Please add shipping code!");
                return false;
            } else {
                $("#err_txt_shipping_code_edit").text("");
            }
            // if (contact_person == null || contact_person == "") {
            //     $("#err_contact_person_name_edit").text("Please Enter Contact Person Name.");
            //     return false;
            // } else {
            //     $("#err_contact_person_name_edit").text("");
            // }
            if (party_address == null || party_address == "") {
                $("#err_party_address_edit").text("Please Enter Address.");
                return false;
            } else {
                $("#err_party_address_edit").text("");
            }
            if (country == null || country == "") {
                $("#err_country_edit").text("Please Select Country.");
                return false;
            } else {
                $("#err_country_edit").text("");
            }
            if (state == null || state == "") {
                $("#err_state_edit").text("Please Select State.");
                return false;
            } else {
                $("#err_state_edit").text("");
            }
            if (city == null || city == "") {
                $("#err_city_edit").text("Please Select City.");
                return false;
            } else {
                $("#err_city_edit").text("");
            }
            if (pin_number != "" ) {
            if (!pin_number.match(num_regex)) {
                $('#err_pin_error_code').text("Please Enter Valid Pin Number");
                return false; 
            }  else {
            $("#err_pin_error_code").text("");
            }
            } else {
            $("#err_pin_error_code").text("");
            }
        if (department != "" ) {
           if (!department.match(name_regex1)) {
                $('#err_department_edit').text("Please Enter Valid Department");
                return false; 
            }  else {
            $("#err_department_edit").text("");
        }
        } else {
            $("#err_department_edit").text("");
        }
        if (contact_person != "" ) {
           if (!contact_person.match(alpa_regex)) {
                $('#err_contact_person_name_edit').text("Please Enter Valid Contact Person Name");
                return false; 
            } else {
            $("#err_contact_person_name_edit").text("");
        }  
        } else {
            $("#err_contact_person_name_edit").text("");
        }
        if (contact_number != "" ) {
           if (!contact_number.match(num_regex)) {
                $('#err_contact_number_edit').text("Please Enter Valid Contact Number");
                return false; 
            }  else {
            $("#err_contact_number_edit").text("");
        }
        } else {
            $("#err_contact_number_edit").text("");
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
        if (!txt_email.match(email_regex) && txt_email != '') {
            $('#err_txt_email_edit').text('Please Enter valid Email Address');
            return false;
        } else {
            $("#err_txt_email_edit").text("");
        }
        if (txt_email.length < 2 && txt_email != '') {
            $('#err_txt_email_edit').text("Email length must be greater than 2");
            return false;
        } else {
            $("#err_txt_email_edit").text("");
        }
        if (store_location == null || store_location == "") {
            $("#err_edit_store_location").text("Please Enter Store Location.");
            return false;
        } else {
            $("#err_edit_store_location").text("");
        }
        var shipping_address_id = $('#shipping_address_id_edit').val();
        $.ajax({
            url: base_url + 'shipping_address/edit_get_check_shipping_with_location',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'shipping_address_id' : shipping_address_id,
                'company_name' :company_name,
                'store_location': store_location
            },
            success: function (result) {
                var count = result[0].num;
                if (count > 0) {
                    $("#err_edit_store_location").text("Combination of Company and Store Location is Already Exist");
                        return false;
                }else{
                    var form_data = $('#frm_shipping_edit').serializeArray();
                    $("#loader").show();
                    $.ajax({
                        url: base_url + 'shipping_address/edit_shipping_address',
                        dataType: 'JSON',
                        method: 'POST',
                        data: form_data,
                         beforeSend: function(){
                             // Show image container
                            $("#loader").show();
                        },
                        success: function (result) {
                            setTimeout(function () {
                                location.reload();
                                $("#loader").hide();
                            });
                        }
                    });
                    anime.timeline({loop:!0}).add({targets:".ml8 .circle-white",scale:[0,3],opacity:[1,0],easing:"easeInOutExpo",rotateZ:360,duration:8e3}),anime({targets:".ml8 .circle-dark-dashed",rotateZ:360,duration:8e3,easing:"linear",loop:!0});
                }
            }
        });
            // if (contact_number == null || contact_number == "") {
            //     $("#err_contact_number_edit").text("Please Enter Contact Number.");
            //     return false;
            // } else {
            //     $("#err_contact_number_edit").text("");
            // }
    });
});

</script>