<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">    
    <div class="fixed-breadcrumb">
        <ol class="breadcrumb abs-ol">
            <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                    Dashboard</a>
            </li>
            <li><a href="<?php echo base_url('customer'); ?>">
                    Customer
                </a></li>
            <li class="active"><!-- Add customer -->
                Add Customer
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Add Customer
                        </h3>
                        <a href="javascript:void(0);" class="btn btn-sm btn-info pull-right add_button">Add Field</a>                        
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('customer/add_customer'); ?>"  encType="multipart/form-data">
                        <div class="box-body">                      
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="customer_code">
                                            Customer Code<span class="validation-color">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="reference_number" name="reference_number" style="display: none;" value="<?php echo $reference_number; ?>" readonly>
                                        <input type="text" class="form-control" id="customer_code" name="customer_code" value="<?php echo $invoice_number; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_customer_code"><?php echo form_error('customer_code'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 cust_type">
                                    <label for="customer_type">
                                        Customer Type<span class="validation-color">*</span>
                                    </label>
                                    <select class="form-control select2" id="customer_type" name="customer_type" style="width: 100%;">
                                        <option value="">Select Type</option> 
                                        <option value="company">Company</option>
                                        <option value="individual">Firm</option>
                                        <option value="private limited company">Private Limited Company</option>
                                        <option value="proprietorship">Proprietorship</option>
                                        <option value="partnership">Partnership</option>
                                        <option value="one person company">One Person Company</option>
                                        <option value="limited liability partnership">Limited Liability Partnership</option>
                                    </select>
                                    <span class="validation-color" id="err_customer_type"><?php echo form_error('customer_type'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="customer_name" id="cust_name" >
                                        Company/Firm Name<span class="validation-color">*</span>
                                    </label>
                                    <label hidden="hidden" for="customer_name" id="comp_name">
                                        Company Name<span class="validation-color">*</span>
                                    </label>
                                    <input type="hidden" name="ledger_id" id="customer_ledger_id" value="0">
                                    <input type="hidden" name="customer_id" id="customer_id" value="0">
                                    <input type="text"  class="form-control" id="customer_name" name="customer_name" maxlength="100">
                                    <input type="hidden"  class="form-control" id="customer_name_used" name="customer_name_used" maxlength="90">
                                    <span class="validation-color" id="err_customer_name"><?php echo form_error('customer_name'); ?></span>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="col-md-4">
                                    <div class="form-group">                                   
                                        <label for="pin_code">PIN Code</label>
                                    <input class="form-control" type="text" name="txt_pin_code" id="txt_pin_code"  maxlength="12" />
                                    <span class="validation-color" id="err_pin_code"><?php echo form_error('pin_number'); ?></span>
                                    </div>    
                                </div>                                
                                <!--<div class="col-md-4">
                                    <div class="form-group">                                   
                                        <label for="pan">PAN Number</label>
                                        <input type="text"  class="form-control" id="" name="" maxlength="">
                                    </div>    
                                </div>                                -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cmb_country">
                                            Country<span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="cmb_country" class="country" name="cmb_country" style="width: 100%;">
                                            <option value="">Select Country</option>
                                            <?php
                                            foreach ($country as $key) {
                                                ?>
                                                <option value='<?php echo $key->country_id ?>' <?=($key->country_id == 101 ? 'selected' : ''); ?> ><?php echo $key->country_name; ?> </option>
                                            <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_country"></span>
                                    </div>
                                </div>                               
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cmb_state">
                                            State <span class="validation-color">*</span>
                                        </label>
                                        <select class="form-control select2" id="cmb_state" class="state" name="cmb_state" style="width: 100%;">
                                            <?php
                                            foreach($state as $key){
                                                ?>

                                            <option value="<?php echo $key->state_id ?>" <?=($key->state_id == $branch[0]->branch_state_id ? 'selected' : ''); ?> ><?php echo $key->state_name; ?></option>
                                             <?php } ?>
                                        </select>
                                        <span class="validation-color" id="err_state"></span>
                                    </div>
                                </div>   
                                </div>
                                <div class="row">
                                <div class="form-group col-md-4">                                   
                                    <label for="cmb_city">
                                        City<span class="validation-color">*</span>
                                    </label>
                                    <select class="form-control select2" id="cmb_city" class="city" name="cmb_city" style="width: 100%;">
                                        <?php
                                        foreach($city as $key){ ?>
                                        <option value="<?php echo $key->city_id?>" <?=($key->city_id == $branch[0]->branch_city_id ? 'selected' : '');?>><?php echo $key->city_name?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="validation-color" id="err_city"></span>
                                </div>                                
                                <!--<div class="col-md-4">
                                    <div class="form-group">                                   
                                        <label for="zip_code">Zip Code</label>
                                        <input type="text"  class="form-control" id="" name="" maxlength="5">
                                    </div>    
                                </div>                                -->
                                <div class="form-group col-md-4">
                                    <label for="address">
                                        Address<span class="validation-color">*</span>
                                    </label>
                                    <textarea class="form-control" id="address" rows="2" name="address" maxlength="1000"></textarea>
                                    <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                </div> 
                                 <div class="form-group col-md-4">
                                    <label for="gst">GST Number</label>
                                        <input type="text"  class="form-control" id="gst_number" name="gst_number" maxlength="15">
                                        <span class="validation-color" id="err_gstin"><?php echo form_error('gstin'); ?></span>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="pan_number">PAN Number</label>
                                    <input class="form-control" type="text" name="txt_pan_number" id="txt_pan_number" maxlength="20" />
                                    <span class="validation-color" id="err_pan_number"><?php echo form_error('pan_number'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="contact_person">Contact Person Name</label>
                                    <input class="form-control" type="text" name="txt_contact_person" id="txt_contact_person" maxlength="50" />
                                    <span class="validation-color" id="err_contact_person"><?php echo form_error('contact_person'); ?></span>
                                </div>  
                                <div class="form-group col-md-4">
                                    <label for="contact_number">Contact Number</label>
                                    <input class="form-control" type="number" name="txt_contact_number" id="txt_contact_number" maxlength="16" />
                                    <span class="validation-color" id="err_contact_number"><?php echo form_error('contact_number'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="email_address">Email<span class="validation-color"></span></label>
                                    <input class="form-control" type="text" name="email_address" id="email_address" maxlength="50" />
                                    <span class="validation-color" id="err_email_address"><?php echo form_error('email_address'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="due_days">Due Days</label>
                                    <input class="form-control" type="number" name="due_days" id="due_days" min = '0' max = '365' />
                                    <span class="validation-color" id="err_due_days"><?php echo form_error('due_days'); ?></span>
                                </div>
                                 <div class="form-group col-md-4">
                                    <label for="tan_number">TAN Number</label>
                                    <input class="form-control" type="text" name="txt_tan_number" id="txt_tan_number" maxlength="20"  />
                                    <span class="validation-color" id="err_tan_number"><?php echo form_error('tan_number'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" value="">
                                <span class="validation-color" id="err_department"><?php echo form_error('department'); ?></span>
                                </div>  
                            </div>
                            </div>
                            <div class="row">
                                <div class="field_wrapper"></div>  
                            </div>
                            <div class="box-footer">
                                <button type="submit" id="customer_submit" class="btn btn-info">
                                    Add</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('customer')"><!-- Cancel -->
                                    Cancel</span>
                            </div>
                        </div>                      
                    </form>
                </div>   
            </div>
        </div>
    </section>
</div>
<?php $this->load->view('layout/footer'); ?>
<script type="text/javascript">
    var xhr;
    $(document).ready(function () {
        /*getCountryById();*/
        /*$('form input:not([type="submit"])').keydown(function(e) {
            if (e.keyCode == 13) {
            e.preventDefault();
            return false;
            }
        });*/
        var maxField = 10;
        //Input fields increment limitation
        var addButton = $('.add_button');
        //Add button selector
        var wrapper = $('.field_wrapper');
        //New input field html
        var x = 1;
        var count = 1;
        //Initial field counter is 1
        //Once add button is clicked
        $(addButton).click(function () {
            //Check maximum number of input fields
            var flag_label = 'N';
            $(".coustem_label").each(function () {
                var lbl = $(this).val();
                if (lbl == '') {
                    flag_label = 'Y';
                }
            });
            var flag_value = 'N';
            $(".coustem_field").each(function () {
                var val_cu = $(this).val();
                if (val_cu == '') {
                    flag_value = 'Y';
                }
            });
            if (flag_value == 'Y' || flag_label == 'Y') {
                alert_d.text ='Please Enter valid input for customer field and label';
                PNotify.error(alert_d);
                return false;
            } else {
                if (x < maxField) {
                    //Input field wrapper
                    var fieldHTML = '<div class="col-sm-4 form-group"><label><input type="text" id="custom_lablel' + count + '" name="custom_lablel[]" Placeholder="Textfield" class="disable_in form-control coustem_label" id="edit_label"></label><a href="javascript:void(0);" class="remove_button btn btn-info btn-xs pull-right" title="Remove"><i class="fa fa-minus"></i></a><input type="text" id="custom_field' + count + '" name="custom_field[]" value="" class="form-control coustem_field"/><span class="validation-color" id="err_txt_fld' + count + '"></span></div>';
                    x++;
                    //Increment field counter
                    count++;
                    $(wrapper).append(fieldHTML);
                    //Add field html
                }
            }
        });

        $('[name=customer_name]').on('blur',function(){
            $('[name=customer_name]').trigger('keyup');
        })

        $('[name=customer_name]').on('keyup',function(){
            
            var cust_name= $(this).val();
            if (xhr && xhr.readyState != 4) {
                xhr.abort();
            }
            $('[name=customer_name_used]').val('0');
            $('#err_customer_name').text('');
            xhr = $.ajax({
                url: '<?= base_url(); ?>customer/CustomerValidation',
                type: 'post',
                data: {cust_name:cust_name,id:0},
                dataType: 'json',
                success: function (json) {
                    if(json.rows > 0){
                        $('#err_customer_name').text('Name already used!');
                        $('[name=customer_name_used]').val('1');
                    }
                }, complete: function () {
                   
                }
            })
        })

        //Once remove button is clicked
        $(wrapper).on('click', '.remove_button', function (e) {
            e.preventDefault();
            $(this).parent('div').remove();
            //Remove field html
            x--;
            //Decrement field counter
        });

        $(wrapper).on('dblclick', '#edit_label', function () {
            //console.log("fdf");
            $(this).removeClass('disable_in');
        });

        $("#gst_number").on("blur keyup", function (event) {
            var gstin = $('#gst_number').val();
            $("#gst_number").val(gstin.toUpperCase());
            var gstin_length = gstin.length;
            var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
            if (gstin_length > 0)
            {
                if (gstin == null || gstin == "") {
                    $("#err_gstin").text("Please Enter GSTIN Number.");
                    return false;
                } else {
                    $("#err_gstin").text("");
                }
                if (!gstin.match(gst_regex_format)) {
                    $('#err_gstin').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_gstin").text("");
                }
                if (gstin_length < 15 || gstin_length > 15)
                {
                    $("#err_gstin").text("GSTIN Number should have length 15");
                    return false;
                } else {
                    $("#err_branch_gstin_number").text("");
                }
                if (!gstin.match(gst_regex_format)) {
                    $('#err_gstin').text("Please Enter Valid GSTIN Number.");
                    return false;
                } else {
                    $("#err_gstin").text("");
                }
            }
        });
    });

    $('#cmb_country').change(function () {
        getCountryById();
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

    function getCountryById(){
        var id = $('#cmb_country').val();
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
                console.log(opt1);
                for (i = 0; i < data.length; i++) {
                    $('#cmb_state').append('<option value="' + data[i].state_id + '">' + data[i].state_name + '</option>');
                }
            }
        });
    }
    // jQuery('#pincode').on('keyup',function(){
    //     });

    // function get_details(){
    //     var pincode = jQuery('#pincode').val();
    //     if(pincode == ''){
    //         jQuery('#country').val('');
    //         jQuery('#state').val('');
    //         jQuery('#city').val('');
    //     }else{
    //         jQuery.getJSON('https://api.postalpincode.in/pincode/'+ pincode, function(result){
    //             data = result[0];

    //             if(data.Status == 'Success'){
    //                 var v = data.PostOffice[0];
    //                 var district = data.PostOffice[0].District;
    //                 var state = data.PostOffice[0].State;
    //                 var Country = data.PostOffice[0].Country;
                
    //                 $('#city').val(district);
    //                 $('#state').val(state);
    //                 $('#country').val(Country);
    //                 // console.log( state,Country,district);
    //             }
    //             /*console.log(result,'result');*/
    //         });
    //         /*jQuery.ajax({
    //             url : 'https://api.postalpincode.in/pincode/'+ pincode,
    //             type : 'post' ,
    //             data : 'pincode ='+ pincode,
    //             success : function(data){
    //                 var getData = $.parseJSON(data);
    //                 // jQuery('#city').val('city');
    //                 // jQuery('#state').val('state');
    //                 console.log(getData);
    //             }
    //         });*/
    //     }
    // }
</script>
<script>
    var state_url = '<?php echo base_url('common/getState/') ?>';
    var city_url = '<?php echo base_url('common/getCity/') ?>';
    var state_code_url = '<?php echo base_url('common/getStateCode/') ?>';
    var customer_add_url = '<?php echo base_url('customer/add_customer_ajax') ?>';
    var ledger_check_url = '<?php echo base_url('common/get_check_ledger/') ?>';
    var customer_add = 'yes';
</script>
<script>
    $(document).ready(function () {
        $('.cust_type .select2').select2({
            minimumResultsForSearch: -1
        });
    });
    $("#customer_submit").click(function (event) {
        // event.preventDefault();
        var customer_code = $('#customer_code').val() ? $('#customer_code').val() : "";
        var customer_name = $('#customer_name').val() ? $('#customer_name').val() : "";
        var customer_type = $('#customer_type').val() ? $('#customer_type').val() : "";
        var city = $('#cmb_city').val() ? $('#cmb_city').val() : "";
        var state = $('#cmb_state').val() ? $('#cmb_state').val() : "";
        var country = $('#cmb_country').val() ? $('#cmb_country').val() : "";
        var address = $('#address').val() ? $('#address').val() : "";
        var contact_person = $('#txt_contact_person').val() ? $('#txt_contact_person').val() : "";
        var pin_number = $('#txt_pin_code').val() ? $('#txt_pin_code').val() : "";
        var pan_number = $('#txt_pan_number').val() ? $('#txt_pan_number').val() : "";
        var tan_number = $('#txt_tan_number').val() ? $('#txt_tan_number').val() : "";
        var gst_number = $('#gst_number').val() ? $('#gst_number').val() : "";
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
        var tan_regex = /^([A-Z]{4}[0-9]{5}[A-Z]{1})$/;
        var email = $('#email_address').val() ? $('#email_address').val() : "";
        var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var num_regex = /^[0-9]+$/;
        var alpa_regex = /^[a-zA-Z ]+$/;
        var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
        //var extra=$(document).find('input[name^=custom_field]');
        
        var flag_label = 'N';
        $(".coustem_label").each(function () {
            var lbl = $(this).val();
            if (lbl == '') {
                flag_label = 'Y';
            }
        });
        var flag_value = 'N';
        $(".coustem_field").each(function () {
            var val_cu = $(this).val();
            if (val_cu == '') {
                flag_value = 'Y';
            }
        });
        if (customer_type == null || customer_type == "") {
            $("#err_customer_type").text("Please Select Customer Type.");
            return false;
        } else {
            $("#err_customer_type").text("");
        }
        if (customer_name == null || customer_name == "") {
            $("#err_customer_name").text("Please Enter Company/Firm Name.");
            return false;
        } else {
            $("#err_customer_name").text("");
            if($('[name=customer_name_used]').val() > 0){
                $("#err_customer_name").text("Name already used!");
                return false;
            }
        }
        if (!customer_name.match(name_regex1)) {
            $('#err_customer_name').text("Please Enter Valid Customer Name ");
            return false;
        } else {
            $("#err_customer_name").text("");
        }
       /* if (email == null || email == "") {
            $("#err_email_address").text("Please Enter Email.");
            return false;
        } else {
            $("#err_email_address").text("");
        } */
        if (email != "") {
            if (!email.match(email_regex)) {
                $('#err_email_address').text("Please Enter Valid Email");
                return false;
            } else {
                $("#err_email_address").text("");
            }
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
        if (address == null || address == "") {
            $("#err_address").text("Please Enter Address.");
            return false;
        } else {
            $("#err_address").text("");
        }
        if (pin_number != "" ) {
           if (!pin_number.match(num_regex)) {
                $('#err_pin_code').text("Please Enter Valid Pin Number");
                return false; 
            } else {
            $("#err_pin_code").text("");
        } 
        } else {
            $("#err_pin_code").text("");
        }
        if (pan_number != "" ) {
           if (!pan_number.match(name_regex)) {
                $('#err_pan_number').text("Please Enter Valid Pan Number");
                return false; 
            }else {
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

        if (gst_number != "" ) {
           if (!gst_number.match(gst_regex_format)) {
                $('#err_gstin').text("Please Enter Valid GST Number");
                return false; 
            }  else {
            $("#err_gstin").text("");
        }
        } else {
            $("#err_gstin").text("");
        }
        if (contact_person != "" ) {
           if (!contact_person.match(alpa_regex)) {
                $('#err_contact_person').text("Please Enter Valid Contact Person");
                return false; 
            }  else {
            $("#err_contact_person").text("");
        }
        } else {
            $("#err_contact_person").text("");
        }

        /*if (!(address == null || address == "")) {
            var i, temp = "";
            var addr_trim = $.trim(address);
            // console.log(addr_trim.length);
            for (i = 0; i < addr_trim.length; i++) {
                if (addr_trim[i] == addr_trim[i + 1]) {
                    temp += addr_trim[i];
                    if (temp.length == 4) {
                        $("#err_address").text("Please Enter Valid Address.");
                        return false;
                    }
                }
            }
        }*/
        /*if ((!contact_person.match(name_regex)) && (contact_person != '')) {
            $('#err_contact_person').text("Please Enter Valid Person Name ");
            return false;
        } else {
            $("#err_contact_person").text("");
        }*/
    });
</script>

