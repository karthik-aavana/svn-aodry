<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<form id="frm_add_vendor" name="frm_add_vendor" method="post" action="<?php echo base_url('supplier/add_supplier'); ?>"  encType="multipart/form-data">
    <div class="content-wrapper">
        <section class="content mt-50">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><!-- Add customer --> Add Vendor </h3>
                            <a href="javascript:void(0);" class="btn btn-sm btn-info pull-right add_button">Add Field</a>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Vendor Code</label>
                                    <input type="hidden" name="ledger_id" id="ledger_id" value="0">
                                    <input type="hidden" name="supplier_id" id="supplier_id" value="0">
                                    <input type="text" class="form-control" id="supplier_code" name="supplier_code" value="<?php echo $invoice_number; ?>" <?php
                                    if ($access_settings[0]->invoice_readonly == 'yes') {
                                        /*echo "readonly";*/
                                    }
                                    ?>>
                                    <span class="validation-color" id="err_vendor_code"></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="vendor_type"> Supplier Type<span class="validation-color">*</span> </label>
                                    <select class="form-control select2" id="vendor_type" name="vendor_type">
                                        <option value="">Select Type</option>
                                        <option value="individual">Firm</option>
                                        <option value="company">Company</option>
                                        <option value="private limited company">Private Limited Company</option>
                                        <option value="proprietorship">Proprietorship</option>
                                        <option value="partnership">Partnership</option>
                                        <option value="one person company">One Person Company</option>
                                        <option value="limited liability partnership">Limited Liability Partnership</option>
                                    </select>
                                    <span class="validation-color" id="err_vendor_type"><?php echo form_error('vendor_type'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="vendor_name"> Company/Firm Name<span class="validation-color">*</span> </label>
                                    <input type="hidden"  class="form-control" id="vendor_name_used" name="vendor_name_used" maxlength="5">
                                    <input type="text"  class="form-control" id="vendor_name" name="vendor_name" maxlength="100">
                                    <span class="validation-color" id="err_vendor_name"><?php echo form_error('vendor_name'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="gst">GST Number</label>
                                    <input type="text"  class="form-control" id="gst_number" name="gst_number" maxlength="15">
                                    <span class="validation-color" id="err_gstin"><?php echo form_error('gstin'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="txt_contact_person">Contact Person Name</label>
                                    <input type="text" class="form-control" name="txt_contact_person" id="txt_contact_person" maxlength="50"/>
                                    <span class="validation-color" id="err_customer_name"><?php echo form_error('txt_contact_person'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="cmb_country">Country<span class="validation-color">*</span></label>
                                    <select class="from-control select2" name="cmb_country" id="cmb_country">
                                        <option value="">Select Country</option>
                                        <?php
                                        foreach ($country as $row) {
                                            echo "<option value='" . $row->country_id . "' ".($row->country_id == 101 ? 'selected' : '').">" . $row->country_name . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <span class="validation-color" id="err_country"><?php echo form_error('cmb_country'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="cmb_state">State<span class="validation-color">*</span> </label>
                                    <select class="from-control select2" name="cmb_state" id="cmb_state">
                                        <option value="">Select State</option>
                                        <?php
                                        foreach($state as $key){ ?>

                                        <option value="<?php echo $key->state_id ?>" <?=($key->state_id == $branch[0]->branch_state_id ? 'selected' : ''); ?> ><?php echo $key->state_name; ?></option>
                                         <?php } ?>
                                    </select>
                                    <span class="validation-color" id="err_state"><?php echo form_error('cmb_state'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="cmb_state">City<span class="validation-color">*</span></label>
                                    <select class="from-control select2"  name="cmb_city" id="cmb_city">
                                        <option value="">Select City</option>
                                        <?php
                                        foreach($city as $key){ ?>
                                        <option value="<?php echo $key->city_id?>" <?=($key->city_id == $branch[0]->branch_city_id ? 'selected' : '');?>><?php echo $key->city_name?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="validation-color" id="err_city"><?php echo form_error('cmb_state'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="address">Address<span class="validation-color">*</span></label>
                                    <textarea name="address" id="address" rows="2" cols="40" class="form-control" maxlength="1000"></textarea>
                                    <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="pin_code">PIN Code</label>
                                    <input class="form-control" type="text" name="txt_pin_code" id="txt_pin_code" maxlength="12" />
                                    <span class="validation-color" id="err_pin_code"><?php echo form_error('pin_number'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="pan_number">PAN Number</label>
                                    <input class="form-control" type="text" name="txt_pan_number" id="txt_pan_number" maxlength="20" />
                                    <span class="validation-color" id="err_pan_number"><?php echo form_error('tan_number'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tan_number">TAN Number</label>
                                    <input class="form-control" type="text" name="txt_tan_number" id="txt_tan_number" maxlength="20" />
                                    <span class="validation-color" id="err_tan_number"><?php echo form_error('tan_number'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="contact_number">Contact Number</label>
                                    <input class="form-control" type="number" name="txt_contact_number" id="txt_contact_number" maxlength="16" />
                                    <span class="validation-color" id="err_contact_number"><?php echo form_error('contact_number'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="email_address">Email<span class="validation-color"></span></label>
                                    <input class="form-control" type="text" name="email_address" id="email_address" maxlength="50" />
                                    <span class="validation-color" id="err_email_address"><?php echo form_error('email_address'); ?></span>
                                </div>
                                 <div class="form-group col-md-4">
                                    <label for="payment_days">Payment Days</label>
                                    <input class="form-control" type="number" name="payment_days" id="payment_days" min="0" max="365" />
                                    <span class="validation-color" id="err_payment_days"><?php echo form_error('payment_days'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" value="" >
                                <span class="validation-color" id="err_department"><?php echo form_error('department'); ?></span>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="field_wrapper"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="vendor_submit" data-dismiss="modal">
                                Add
                            </button>
                            <a class="btn btn-default" href="<?php echo base_url("supplier") ?>">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>
<?php $this->load->view('layout/footer'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        var maxField = 10;
        //Input fields increment limitation
        var addButton = $('.add_button');
        //Add button selector
        var wrapper = $('.field_wrapper');
        //Input field wrapper
        var x = 1;
        var count = 1;
        ;
        //Initial field counter is 1
        //Once add button is clicked
        $(addButton).click(function () {
            //Check maximum number of input fields
            var flag_label = 'N';
            $(".custom_lablel").each(function () {
                var lbl = $(this).val();
                if (lbl == '') {
                    flag_label = 'Y';
                }
            });
            var flag_value = 'N';
            $(".custom_field").each(function () {
                var val_cu = $(this).val();
                if (val_cu == '') {
                    flag_value = 'Y';
                }
            });
            if (flag_value == 'Y' || flag_label == 'Y') {
                alert_d.text ='Please Enter valid input for customer field and label!';
                PNotify.error(alert_d); 
                /*alert("Please Enter valid input for customer field and label");*/
                return false;
            } else {
                if (x < maxField) {
                    var fieldHTML = '<div class="col-sm-4 form-group"><label><input type="text" id="custom_lablel' + count + '" name="custom_lablel[]" Placeholder="Textfield" class="disable_in form-control custom_lablel" id="edit_label"></label><a href="javascript:void(0);" class="remove_button btn btn-info btn-xs pull-right" title="Remove"><i class="fa fa-minus"></i></a><input type="text" id="custom_field' + count + '" name="custom_field[]" value="" class="form-control custom_field"/></div>';
                    x++;
                    //Increment field counter
                    count++;
                    //Increment field counter
                    $(wrapper).append(fieldHTML);
                    //Add field html
                }
            }
        });

        $('[name=vendor_name]').on('blur',function(){
            $('[name=vendor_name]').trigger('keyup');
        })
        var xhr;
        $('[name=vendor_name]').on('keyup',function(){
            
            var cust_name= $(this).val();
            if (xhr && xhr.readyState != 4) {
                xhr.abort();
            }
            $('[name=vendor_name_used]').val('0');
            $('#err_vendor_name').text('');
            xhr = $.ajax({
                url: '<?= base_url(); ?>supplier/SupplierValidation',
                type: 'post',
                data: {cust_name:cust_name,id:0},
                dataType: 'json',
                success: function (json) {
                    if(json.rows > 0){
                        $('#err_vendor_name').text('Name already used!');
                        $('[name=vendor_name_used]').val('1');
                    }
                }, complete: function () {
                   
                }
            })
        })

        $(wrapper).on('click', '.remove_button', function (e) {
            e.preventDefault();
            $(this).parent('div').remove();
            //Remove field html
            x--;
            //Decrement field counter
        });
        $(wrapper).on('dblclick', '#edit_label', function () {
            $(this).removeClass('disable_in');
        });
    });
</script>
<script>
    $("#gst_number").on("blur keyup", function (event) {
        var gst_regex = /^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;
        var gstin = $('#gst_number').val();
        $("#gst_number").val(gstin.toUpperCase());
        var gstin_length = gstin.length;
        if (gstin_length > 0)
        {
            if (gstin == null || gstin == "") {
                $("#err_gstin").text("Please Enter GSTIN Number.");
                return false;
            } else {
                $("#err_gstin").text("");
            }
            if (!gstin.match(gst_regex)) {
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
                $("#err_gstin").text("");
            }
            var res = gstin.slice(2, 12);
            $('#panno').val(res);
        }
    });
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
                var city = $('#cmb_city');
                var opt1 = document.createElement('option');
                opt1.text = 'Select City';
                opt1.value = '';
                city.append(opt1);
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
    $("#vendor_submit").click(function () {
        var supplier_code = $('#supplier_code').val() ? $('#supplier_code').val() : "";
        var supplier_name = $('#vendor_name').val() ? $('#vendor_name').val() : "";
        var supplier_type = $('#vendor_type').val() ? $('#vendor_type').val() : "";
        var city = $('#cmb_city').val() ? $('#cmb_city').val() : "";
        var state = $('#cmb_state').val() ? $('#cmb_state').val() : "";
        var country = $('#cmb_country').val() ? $('#cmb_country').val() : "";
        var address = $('#address').val() ? $('#address').val() : "";
        var contact_person = $('#txt_contact_person').val() ? $('#txt_contact_person').val() : "";
        var email = $('#email_address').val() ? $('#email_address').val() : "";
        var email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var tan_regex = /^([A-Z]{4}[0-9]{5}[A-Z]{1})$/;
        var gst_number = $('#gst_number').val() ? $('#gst_number').val() : "";
        var pan_number = $('#txt_pan_number').val() ? $('#txt_pan_number').val() : "";
        var tan_number = $('#txt_tan_number').val() ? $('#txt_tan_number').val() : "";
        var pin_number = $('#txt_pin_code').val() ? $('#txt_pin_code').val() : "";
        var gst_regex_format = "^([0][1-9]|[1-4][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$";
        var flag_label = 'N';
        $(".custom_lablel").each(function () {
            var lbl = $(this).val();
            if (lbl == '') {
                flag_label = 'Y';
            }
        });
        var flag_value = 'N';
        $(".custom_field").each(function () {
            var val_cu = $(this).val();
            if (val_cu == '') {
                flag_value = 'Y';
            }
        });
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
        var alpa_regex = /^[a-zA-Z ]+$/;
        var num_regex = /^[0-9]+$/;
        if (supplier_code == null || supplier_code == "") {
            $("#err_vendor_code").text("Please Enter Supplier Code.");
            return false;
        } else {
            $("#err_vendor_code").text("");
        }
        if (supplier_type == null || supplier_type == "") {
            $("#err_vendor_type").text("Please Select Supplier Type.");
            return false;
        } else {
            $("#err_vendor_type").text("");
        }
        if (supplier_name == null || supplier_name == "") {
            $("#err_vendor_name").text("Please Enter Company/Firm Name");
            return false;
        } else {
            $("#err_vendor_name").text("");
            if($('[name=vendor_name_used]').val() > 0){
                $("#err_vendor_name").text("Name already used!");
                return false;
            }
        }
        if (!supplier_name.match(name_regex1)) {
            $('#err_vendor_name').text("Please Enter Valid Vendor Name");
            return false;
        } else {
            $("#err_vendor_name").text("");
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
        if (flag_value == 'Y' || flag_label == 'Y') {
            alert_d.text ='Please Enter valid input for customer field and label!';
            PNotify.error(alert_d);
            /*alert("Please Enter valid input for customer field and label");*/
            return false;
        }
        if (contact_person != "" ) {
           if (!contact_person.match(alpa_regex)) {
                $('#err_customer_name').text("Please Enter Valid Contact Person Name");
                return false; 
            } else {
            $("#err_customer_name").text("");
        }  
        } else {
            $("#err_customer_name").text("");
        }
        if (gst_number != "" ) {
           if (!gst_number.match(gst_regex_format)) {
                $('#err_gstin').text("Please Enter Valid GST Number");
                return false; 
            } else {
            $("#err_gstin").text("");
        } 
        } else {
            $("#err_gstin").text("");
        }
        if (pan_number != "" ) {
           if (!pan_number.match(name_regex)) {
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
        $.ajax({
            url: base_url + 'supplier/get_check_supplier_code',
            dataType: 'JSON',
            method: 'POST',
            data: {
                'supplier_code' :supplier_code
            },
            success: function (result) {
                var count = result[0].num;
                if (count > 0) {
                    $("#err_vendor_code").text("Vendor Code Already Exist.");
                        return false;
                }
                else{
                    $("#err_vendor_code").val("");
                    $('#frm_add_vendor').submit();
                }
            }
        });
    });
</script>