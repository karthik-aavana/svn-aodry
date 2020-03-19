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
            <li><a href="<?php echo base_url('share_holder'); ?>">
                    People
                </a></li>
            <li class="active"><!-- Add customer -->
                Add People
            </li>
        </ol>
    </div>
    <section class="content mt-50">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Add People
                        </h3>                                            
                    </div>
                    <form name="form" id="form" method="post" action="<?php echo base_url('share_holder/add_shareholder'); ?>"  encType="multipart/form-data">
                        <div class="box-body">                      
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="partner_code">
                                            Code<span class="validation-color">*</span>
                                        </label>                                        
                                        <input type="text" class="form-control" id="partner_code" name="partner_code" value="<?php echo $invoice_number; ?>" <?php
                                        if ($access_settings[0]->invoice_readonly == 'yes') {
                                            echo "readonly";
                                        }
                                        ?>>
                                        <span class="validation-color" id="err_partner_code"><?php echo form_error('partner_code'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3 cust_type">
                                    <label for="partner_type"> Type <span class="validation-color">*</span>
                                    </label>
                                    <select class="form-control select2" id="partner_type" name="partner_type" style="width: 100%;">
                                        <option value="">Select Type</option>
                                        <option value="partner">Partner</option>
                                        <option value="shareholder">Shareholder</option>
                                        <option value="director">Director</option>
                                    </select>
                                    <span class="validation-color" id="err_partner_type"><?php echo form_error('partner_type'); ?></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="lbl_partner_name" id="lbl_partner_name" >Shareholder/Partner Name<span class="validation-color">*</span>
                                    </label>
                                    <input type="hidden" name="partner_id" id="partner_id" value="0">
                                    <input type="text"  class="form-control" id="partner_name" name="partner_name" maxlength="120">
                                    <input type="hidden"  class="form-control" id="partner_name_used" name="partner_name_used" maxlength="90">
                                    <span class="validation-color" id="err_partner_name"><?php echo form_error('lbl_partner_name'); ?></span>
                                </div>                             
                                <div class="form-group col-md-3">
                                    <label for="date_of_birth">Date of Birth<span class="validation-color">*</span></label>
                                    <div class="input-group date">
                                        <input class="form-control datepicker" type="text" name="txt_date_of_birth" id="txt_date_of_birth" maxlength="30" />
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                    <span class="validation-color" id="err_date_of_birth"><?php echo form_error('date_of_birth'); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="address">Address<span class="validation-color">*</span>
                                    </label>
                                    <textarea class="form-control" id="address" rows="2" name="address" maxlength="150"></textarea>
                                    <span class="validation-color" id="err_address"><?php echo form_error('address'); ?></span>
                                </div> 
                                <div class="form-group col-md-3">
                                    <label for="pan_number">PAN Number<span class="validation-color">*</span></label>
                                    <input class="form-control" type="text" name="txt_pan_number" id="txt_pan_number" maxlength="30" />
                                    <span class="validation-color" id="err_pan_number"><?php echo form_error('pan_number'); ?></span>
                                </div>                                
                                <div class="form-group col-md-3" id="div_partner_initial_capt_amt">
                                    <label for="capital_amount">Initial Capital Amount<span class="validation-color"></span></label>
                                    <input class="form-control text-right number" type="text" name="txt_capital_amount" id="txt_capital_amount" maxlength="15" />
                                    <span class="validation-color" id="err_capital_amount"><?php echo form_error('capital_amount'); ?></span>
                                </div> 
                                <div class="col-sm-3 eligible_claim_div" id="eligible_claim">
                                    <div class="form-group ">
                                        <label for="eligible_claim">Eligible to claim remuneration? <span class="validation-color">*</span></label>
                                        <br/>
                                        <label class="radio-inline">
                                            <input type="radio" name="eligible_claim" value="yes" class="minimal" /> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="eligible_claim" value="no" class="minimal" checked="checked"/> No
                                        </label>
                                        <br/>
                                        <span class="validation-color" id="err_eligible_claim"></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3" style="display: none;" id="div_share_nos">
                                    <label for="no_of_shares">No of shares<span class="validation-color"></span></label>
                                    <input class="form-control text-right number" type="text" name="txt_no_of_shares" id="txt_no_of_shares" maxlength="15" />
                                    <span class="validation-color" id="err_no_of_shares"><?php echo form_error('no_of_shares'); ?></span>
                                </div>
                                <div class="form-group col-md-3" style="display: none;" id="div_share_value">
                                    <label for="face_value">Face Value per share<span class="validation-color"></span></label>
                                    <input class="form-control text-right number" type="text" name="txt_face_value" id="txt_face_value" maxlength="15" />
                                    <span class="validation-color" id="err_face_value"><?php echo form_error('face_value'); ?></span>
                                </div>
                                <div class="form-group col-md-3" style="display: none;" id="div_dir_date_app">
                                    <label for="date_apptmnt">Date of Appointment<span class="validation-color">*</span></label>
                                    <div class="input-group date">
                                       <input class="form-control datepicker" type="text" name="txt_date_apptmnt" id="txt_date_apptmnt" maxlength="15" />
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>                                    
                                    <span class="validation-color" id="err_date_apptmnt"><?php echo form_error('date_apptmnt'); ?></span>
                                </div> 
                                <div class="form-group col-md-3" style="display: none;" id="div_dir_type">
                                    <label for="type_director">Type of Director<span class="validation-color">*</span></label>
                                    <select class="form-control select2" id="cmb_type_director" name="cmb_type_director" style="width: 100%;">
                                        <option value="">Select Type of Director</option> 
                                        <option value="managing director">Managing Director</option>
                                        <option value="additional director">Additional Director</option>
                                        <option value="director">Director</option>
                                    </select>
                                    <span class="validation-color" id="err_type_director"><?php echo form_error('type_director'); ?></span>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3" style="display: none;" id="remuneration_div">
                                    <label for="remuneration">Monthly remuneration<span class="validation-color">*</span></label>
                                    <input class="form-control text-right number" type="text" name="txt_remuneration" id="txt_remuneration" maxlength="15" />
                                    <span class="validation-color" id="err_txt_remuneration"><?php echo form_error('remuneration'); ?></span>
                                </div>
                                <div class="form-group col-md-3" id="div_partner_capt_roi">
                                    <label for="roi_capital_intrest">Rate of Interest on capital (%)<span class="validation-color"></span></label>
                                    <input class="form-control text-right number" type="text" name="txt_roi_capital_intrest" id="txt_roi_capital_intrest" maxlength="15" />
                                    <span class="validation-color" id="err_roi_capital_intrest"><?php echo form_error('roi_capital_intrest'); ?></span>
                                </div>
                                <div class="form-group col-md-3" id="div_partner_profit">
                                    <label for="share_profit">Percentage share of profit (%)<span class="validation-color"></span></label>
                                    <input class="form-control text-right number" type="text" name="txt_share_profit" id="txt_share_profit" maxlength="15" />
                                    <span class="validation-color" id="err_share_profit"><?php echo form_error('share_profit'); ?></span>
                                </div>                                
                                <div class="form-group col-md-3" style="display: none;" id="div_share_premium">
                                    <label for="security_premimum">Security Premium<span class="validation-color"></span></label>
                                    <input class="form-control text-right number" type="text" name="txt_security_premimum" id="txt_security_premimum" maxlength="15" />
                                    <span class="validation-color" id="err_security_premimum"><?php echo form_error('security_premimum'); ?></span>
                                </div>
                                <div class="form-group col-md-3" style="display: none;" id="div_share_paid_capital">
                                    <label for="amount_paid_capital">Amount of paid up capital<span class="validation-color"></span></label>
                                    <input class="form-control text-right number" type="text" name="txt_amount_paid_capital" id="txt_amount_paid_capital" maxlength="15" />
                                    <span class="validation-color" id="err_amount_paid_capital"><?php echo form_error('amount_paid_capital'); ?></span>
                                </div>
                                <div class="form-group col-md-3" style="display: none;" id="div_share_type">
                                    <label for="share_type">Share Type<span class="validation-color">*</span></label>
                                   <select class="form-control select2" id="cmb_share_type" name="cmb_share_type" style="width: 100%;">
                                        <option value="">Select Share Type</option> 
                                        <option value="equity">Equity</option>
                                        <option value="preference">Preference</option>
                                    </select>
                                    <span class="validation-color" id="err_share_type"><?php echo form_error('share_type'); ?></span>
                                </div>                     
                            </div>                 
                            <div class="box-footer">
                                <button type="submit" id="customer_submit" class="btn btn-info">Add</button>
                                <span class="btn btn-default" id="cancel" onclick="cancel('share_holder')">Cancel</span>
                            </div>
                        </div>                      
                    </form>
                </div>   
            </div>
        </div>
    </section>
</div>
<?php $this->load->view('layout/footer'); ?>

<script>
    $(document).ready(function () {
        $('.cust_type .select2').select2({
            minimumResultsForSearch: -1
        });


        $('[name=partner_name]').on('blur', function () {

            var part_name = $(this).val();

            $('[name=partner_name_used]').val('0');
            $('#err_partner_name').text('');
            if(part_name != ''){
            xhr = $.ajax({
                url: '<?= base_url(); ?>share_holder/PartnerValidation',
                type: 'post',
                data: {partner_name: part_name, id: 0},
                dataType: 'json',
                success: function (json) {
                    if (json.rows > 0) {
                        $('#err_partner_name').text('Name already used!');
                        $('[name=partner_name_used]').val('1');
                    }
                }, complete: function () {

                }
            })
            }
        });
    });
    $("#customer_submit").click(function (event) {
        var partner_code = $('#partner_code').val() ? $('#partner_code').val() : "";
        var partner_name = $('#partner_name').val() ? $('#partner_name').val() : "";
        var partner_type = $('#partner_type').val() ? $('#partner_type').val() : "";
        var date_of_birth = $('#txt_date_of_birth').val() ? $('#txt_date_of_birth').val() : "";
        var remuneration = $('#txt_remuneration').val() ? $('#txt_remuneration').val() : "";
        var roi_captial = $('#txt_roi_capital_intrest').val() ? $('#txt_roi_capital_intrest').val() : "";
        var share_profit = $('#txt_share_profit').val() ? $('#txt_share_profit').val() : "";
        var address = $('#address').val() ? $('#address').val() : "";
        var pan_number = $('#txt_pan_number').val() ? $('#txt_pan_number').val() : "";
        var name_regex = /^[-a-zA-Z\s0-9 ]+$/;
        var name_regex1 = /^[a-zA-Z\[\]/@()#$%&\-.+,\d\-_\s\']+$/;
        var eligible_claim = $("input[type=radio][name='eligible_claim']:checked").val();
        var date_apptmnt = $('#txt_date_apptmnt').val() ? $('#txt_date_apptmnt').val() : "";
        var type_director = $('#cmb_type_director').val() ? $('#cmb_type_director').val() : "";
        var share_type = $('#cmb_share_type').val() ? $('#cmb_share_type').val() : "";
        if (partner_type == null || partner_type == "") {
            $("#err_partner_type").text("Please Select People Type.");
            return false;
        } else {
            $("#err_partner_type").text("");
        }
        if (partner_name == null || partner_name == "") {
            $("#err_partner_name").text("Please Enter "+ partner_type +"Name.");
            return false;
        } else {
            $("#err_partner_name").text("");
            if ($('[name=partner_name_used]').val() > 0) {
                $("#err_partner_name").text("Name already used!");
                return false;
            }
        }
        if (!partner_name.match(name_regex1)) {
            $('#err_partner_name').text("Please Enter Valid "+ partner_type + "Name ");
            return false;
        } else {
            $("#err_customer_name").text("");
        }
          if (date_of_birth == null || date_of_birth == "") {
            $("#err_date_of_birth").text("Please Select Date of Birth.");
            return false;
        } else {
            $("#err_date_of_birth").text("");
        }
        if (address == null || address == "") {
            $("#err_address").text("Please Enter Address.");
            return false;
        } else {
            $("#err_address").text("");
        }
        if (pan_number == "" || pan_number == null) {
             $('#err_pan_number').text("Please Enter Pan Number");
                return false;
        }

        if (pan_number != "") {
            if (!pan_number.match(name_regex)) {
                $('#err_pan_number').text("Please Enter Valid Pan Number");
                return false;
            } else {
                $("#err_pan_number").text("");
            }
        } else {
            $("#err_pan_number").text("");
        }      

        if (eligible_claim == 'yes') {
            if (remuneration == null || remuneration == "") {
                $("#err_txt_remuneration").text("Please Enter Rate Of Interest On Capital .");
                return false;
            } else {
                $("#err_txt_remuneration").text("");
            }
        }
        if(partner_type == 'director'){
            if(date_apptmnt == null || date_apptmnt == ''){
                $("#err_date_apptmnt").text("Please Select Appointment Date.");
                return false;
            } else {
                $("#err_date_apptmnt").text("");
            }

            if(type_director == null || type_director == ''){
                $("#err_type_director").text("Please Select Type of Director.");
                return false;
            } else {
                $("#err_type_director").text("");
            }                
        }
        
        if(partner_type == 'shareholder'){
            if(share_type == null || share_type == ''){
                $("#err_share_type").text("Please Select Share Type.");
                return false;
            } else {
                $("#err_share_type").text("");
            }
        }
        

        /*  if (roi_captial == null || roi_captial == "") {
         $("#err_roi_capital_intrest").text("Please Enter Rate Of Interest On Capital .");
         return false;
         } else {
         $("#err_roi_capital_intrest").text("");
         }
         
         if (share_profit == null || share_profit == "") {
         $("#err_share_profit").text("Please Enter Percentage Share Of Profit.");
         return false;
         } else {
         $("#err_share_profit").text("");
         } */



    });

</script>

<script src="<?php echo base_url('assets/js/') ?>partner.js"></script>

